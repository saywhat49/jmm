<?php
namespace Saywhat49\Component\Jmm\Site\Model;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\Pagination\Pagination;
use Saywhat49\Component\Jmm\Administrator\Helper\JmmHelper;
use Saywhat49\Component\Jmm\Administrator\Helper\QueryHelper;

class TableModel extends BaseDatabaseModel
{
    protected ?object $siteTableDetails = null;
    protected ?int $total = null;
    protected ?Pagination $pagination = null;

    /** Lignes de la page courante, chargees une seule fois par requete HTTP. */
    protected ?array $items = null;

    /** Dernier message d'erreur de chargement ('' si tout va bien). */
    protected string $queryError = '';

    public function getSiteTableDetails(?int $siteTableId = null): ?object
    {
        if ($this->siteTableDetails !== null && $siteTableId === null) {
            return $this->siteTableDetails;
        }

        $app = Factory::getApplication();
        if ($siteTableId === null) {
            $params = $app->getParams('com_jmm');
            $siteTableId = (int) $params->get('site_table_id', 0);
            if (!$siteTableId) {
                $siteTableId = $app->getInput()->getInt('site_table_id', 0);
            }
        }

        if ($siteTableId <= 0) {
            return null;
        }

        $db = Factory::getContainer()->get('DatabaseDriver');
        $query = $db->getQuery(true)
            ->select('*')
            ->from($db->quoteName('#__jmm_sitetables'))
            ->where($db->quoteName('id') . ' = ' . (int) $siteTableId)
            ->where($db->quoteName('published') . ' = 1');

        $db->setQuery($query);
        $this->siteTableDetails = $db->loadObject() ?: null;
        return $this->siteTableDetails;
    }

    /**
     * Charge une page de resultats pour une table de site.
     *
     * Une table de site est publiee sur le site public : elle ne lit jamais
     * que des donnees. Toute requete qui ecrirait est refusee ici, que la
     * page vienne de l'affichage ou de l'appel AJAX.
     *
     * @param  int|null $limit  Null pour tout charger, sans pagination.
     * @return array{total:int,rows:array<int,array<string,mixed>>,error:string}
     */
    public function fetchPage(object $siteTable, int $offset = 0, ?int $limit = null, string $sorting = ''): array
    {
        $sql    = (string) ($siteTable->query ?? '');
        $result = ['total' => 0, 'rows' => [], 'error' => ''];

        if (!QueryHelper::isReadOnly($sql)) {
            $result['error'] = Text::_('COM_JMM_INVALID_QUERY_TYPE');

            return $result;
        }

        try {
            $db = JmmHelper::getDatabaseConnection($siteTable->dbname ?: null);
        } catch (\RuntimeException $e) {
            $result['error'] = $e->getMessage();

            return $result;
        }

        try {
            // Le cas general : la requete est enveloppee dans une sous-requete
            // (comptage, tri et page cote MySQL).
            if (QueryHelper::isDerivable($sql)) {
                try {
                    return $this->fetchDerived($db, $sql, $offset, $limit, $sorting);
                } catch (\Throwable $e) {
                    // Sous-requete impossible, par exemple deux colonnes de
                    // meme nom issues d'un JOIN : MySQL refuse "Duplicate
                    // column name" dans une table derivee. On bascule sur la
                    // lecture complete, plus lente mais qui accepte la requete.
                }
            }

            return $this->fetchAll($db, $sql, $offset, $limit, $sorting);
        } catch (\Throwable $e) {
            $result['error'] = Text::_('COM_JMM_ERROR_LOADING_DATA') . ' [' . $e->getMessage() . ']';
        }

        return $result;
    }

    /**
     * Comptage, tri et page par MySQL, en enveloppant la requete.
     *
     * @throws \Throwable
     */
    private function fetchDerived($db, string $sql, int $offset, ?int $limit, string $sorting): array
    {
        $orderBy = $this->buildOrderBy($db, $sorting);

        // Sans pagination ni tri, la requete s'execute telle quelle : le
        // nombre de lignes est celui du resultat, sans requete de comptage.
        if ($limit === null && $orderBy === '') {
            $db->setQuery(QueryHelper::prepare($sql));
            $rows = $db->loadAssocList();
            $rows = is_array($rows) ? $rows : [];

            return ['total' => count($rows), 'rows' => $rows, 'error' => ''];
        }

        $db->setQuery(QueryHelper::countQuery($sql));
        $total = (int) $db->loadResult();
        $size  = $limit ?? max(1, $total);

        // Un tri sur une colonne absente du resultat fait echouer la requete :
        // on retente sans tri plutot que d'afficher une erreur.
        foreach (array_unique([$orderBy, '']) as $order) {
            try {
                $db->setQuery(QueryHelper::pageQuery($sql, $offset, $size, $order));
                $rows = $db->loadAssocList();

                return ['total' => $total, 'rows' => is_array($rows) ? $rows : [], 'error' => ''];
            } catch (\Throwable $e) {
                if ($order === '') {
                    throw $e;
                }
            }
        }

        return ['total' => $total, 'rows' => [], 'error' => ''];
    }

    /**
     * Lecture complete puis tri et pagination en PHP. Sert aux requetes qui
     * ne s'enveloppent pas (SHOW, DESCRIBE, EXPLAIN) et de repli aux autres.
     *
     * @throws \Throwable
     */
    private function fetchAll($db, string $sql, int $offset, ?int $limit, string $sorting): array
    {
        $db->setQuery(QueryHelper::prepare($sql));
        $all = $db->loadAssocList();
        $all = is_array($all) ? $all : [];

        $order = array_values(array_filter(
            QueryHelper::parseSorting($sorting),
            static fn (array $o): bool => $all !== [] && array_key_exists($o[0], $all[0])
        ));

        if ($order !== []) {
            usort($all, static function (array $a, array $b) use ($order): int {
                foreach ($order as [$column, $direction]) {
                    $x   = $a[$column] ?? null;
                    $y   = $b[$column] ?? null;
                    $cmp = (is_numeric($x) && is_numeric($y)) ? $x <=> $y : strnatcasecmp((string) $x, (string) $y);

                    if ($cmp !== 0) {
                        return $direction === 'DESC' ? -$cmp : $cmp;
                    }
                }

                return 0;
            });
        }

        return [
            'total' => count($all),
            'rows'  => $limit === null ? $all : array_slice($all, max(0, $offset), $limit),
            'error' => '',
        ];
    }

    /**
     * Clause ORDER BY a partir du tri demande par le tableau AJAX. Chaque
     * colonne est validee ("lettres, chiffres, _") puis quotee : aucun
     * fragment de la saisie n'atteint la requete tel quel.
     */
    private function buildOrderBy($db, string $sorting): string
    {
        $parts = [];

        foreach (QueryHelper::parseSorting($sorting) as [$column, $direction]) {
            $parts[] = $db->quoteName($column) . ' ' . $direction;
        }

        return implode(', ', $parts);
    }

    /** Charge la page courante, la pagination et le total, une seule fois. */
    private function load(): void
    {
        if ($this->items !== null) {
            return;
        }

        $app    = Factory::getApplication();
        $params = $app->getParams('com_jmm');

        $limit = (int) $params->get('no_record_per_page', 20);
        if ($limit <= 0) {
            $limit = 20;
        }

        $usePagination = (int) $params->get('table_pagination', 1) === 1;
        $limitstart    = max(0, $app->getInput()->getInt('limitstart', 0));

        $this->items = [];
        $this->total = 0;

        $siteTable = $this->getSiteTableDetails();

        if ($siteTable && !empty($siteTable->query)) {
            $page = $this->fetchPage($siteTable, $usePagination ? $limitstart : 0, $usePagination ? $limit : null);

            // Page demandee au-dela de la fin : on revient a la derniere.
            if ($usePagination && $page['error'] === '' && $page['rows'] === [] && $page['total'] > 0 && $limitstart >= $page['total']) {
                $limitstart = (int) (floor(($page['total'] - 1) / $limit) * $limit);
                $page       = $this->fetchPage($siteTable, $limitstart, $limit);
            }

            $this->items      = $page['rows'];
            $this->total      = $page['total'];
            $this->queryError = $page['error'];
        }

        $this->pagination = new Pagination($this->total, $limitstart, $limit);
    }

    public function getQueryError(): string
    {
        $this->load();

        return $this->queryError;
    }

    public function getTotal(): int
    {
        $this->load();

        return (int) $this->total;
    }

    public function getPagination(): ?Pagination
    {
        $this->load();

        return $this->pagination;
    }

    public function getItems(): array
    {
        $this->load();

        return $this->items ?? [];
    }

    public function getColumns(): array
    {
        $rows = $this->getItems();

        return $rows !== [] ? array_keys($rows[0]) : [];
    }
}
