<?php
/**
 * Teste TableModel (site) de bout en bout, sans Joomla : les classes du CMS
 * sont remplacees par de minuscules doublures, et la base par SQLite.
 *
 * SQLite n'est pas MySQL. Ce test verifie la logique du modele (refus des
 * ecritures, pagination, tri, replis) et la validite des sous-requetes
 * produites ; il ne remplace pas un essai sur un vrai site Joomla.
 *
 * Charge par tests/run.php.
 */

namespace Joomla\CMS {
    class Factory
    {
        public static $app;

        public static function getApplication()
        {
            return self::$app;
        }
    }
}

namespace Joomla\CMS\Language {
    class Text
    {
        public static function _($key)
        {
            return $key;
        }

        public static function sprintf($key, ...$args)
        {
            return $key;
        }
    }
}

namespace Joomla\CMS\MVC\Model {
    class BaseDatabaseModel
    {
    }
}

namespace Joomla\CMS\Pagination {
    class Pagination
    {
        public $total;
        public $limitstart;
        public $limit;

        public function __construct($total, $limitstart, $limit)
        {
            $this->total      = $total;
            $this->limitstart = $limitstart;
            $this->limit      = $limit;
        }
    }
}

namespace Saywhat49\Component\Jmm\Administrator\Helper {
    /** Doublure : renvoie la base SQLite du test au lieu d'ouvrir une connexion. */
    class JmmHelper
    {
        public static $driver;

        public static function getDatabaseConnection(?string $targetDb = null)
        {
            return self::$driver;
        }
    }
}

namespace JmmTest {
    /** Pilote minimal, a la forme de Joomla\Database\DatabaseDriver. */
    class SqliteDriver
    {
        public $pdo;
        public $queries = [];
        public $failDerived = false;
        private $sql = '';

        public function __construct()
        {
            $this->pdo = new \PDO('sqlite::memory:');
            $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            $this->pdo->exec('CREATE TABLE t (id INTEGER PRIMARY KEY, n INTEGER, label TEXT)');

            for ($i = 1; $i <= 25; $i++) {
                $this->pdo->exec("INSERT INTO t (n, label) VALUES ($i, 'ligne $i')");
            }
        }

        public function setQuery($sql)
        {
            $this->sql = (string) $sql;
            $this->queries[] = $this->sql;

            // Simule MySQL refusant une table derivee (colonnes homonymes).
            if ($this->failDerived && (str_contains($this->sql, 'AS jmm_page') || str_contains($this->sql, 'AS jmm_cnt'))) {
                throw new \RuntimeException('Duplicate column name');
            }

            return $this;
        }

        public function loadAssocList()
        {
            return $this->pdo->query($this->sql)->fetchAll(\PDO::FETCH_ASSOC);
        }

        public function loadResult()
        {
            return $this->pdo->query($this->sql)->fetchColumn();
        }

        public function quoteName($name)
        {
            return '`' . str_replace('`', '', $name) . '`';
        }
    }

    class FakeParams
    {
        public $values = [];

        public function get($key, $default = null)
        {
            return $this->values[$key] ?? $default;
        }
    }

    class FakeInput
    {
        public $values = [];

        public function getInt($key, $default = 0)
        {
            return (int) ($this->values[$key] ?? $default);
        }
    }

    class FakeApp
    {
        public $params;
        public $input;

        public function __construct()
        {
            $this->params = new FakeParams();
            $this->input  = new FakeInput();
        }

        public function getParams($component = null)
        {
            return $this->params;
        }

        public function getInput()
        {
            return $this->input;
        }
    }
}

namespace {
    require_once __DIR__ . '/../site/src/Model/TableModel.php';

    /** Modele de test : injecte la table de site au lieu de la lire en base Joomla. */
    class JmmTestTableModel extends \Saywhat49\Component\Jmm\Site\Model\TableModel
    {
        public function useSiteTable(object $siteTable): void
        {
            $this->siteTableDetails = $siteTable;
        }
    }

    $driver = new \JmmTest\SqliteDriver();
    \Saywhat49\Component\Jmm\Administrator\Helper\JmmHelper::$driver = $driver;

    $app = new \JmmTest\FakeApp();
    \Joomla\CMS\Factory::$app = $app;

    $model = new JmmTestTableModel();
    $site  = static fn (string $query): object => (object) ['id' => 1, 'query' => $query, 'dbname' => ''];

    // --- Pagination ---------------------------------------------------------
    $r = $model->fetchPage($site('SELECT * FROM t'), 0, 10);
    check('site : total', 25, $r['total']);
    check('site : 10 lignes en premiere page', 10, count($r['rows']));
    check('site : pas d erreur', '', $r['error']);

    $r = $model->fetchPage($site('SELECT * FROM t;'), 20, 10);
    check('site : derniere page partielle', 5, count($r['rows']));
    check('site : derniere page, point-virgule final accepte', 21, (int) $r['rows'][0]['n']);

    // --- Requetes qui posaient probleme avant l'enveloppe ------------------------
    $r = $model->fetchPage($site('SELECT * FROM t LIMIT 12'), 0, 5);
    check('site : requete avec LIMIT deja present, total', 12, $r['total']);
    check('site : requete avec LIMIT deja present, page', 5, count($r['rows']));
    check('site : requete avec LIMIT deja present, sans erreur', '', $r['error']);

    $r = $model->fetchPage($site("SELECT * FROM t ORDER BY n DESC -- les plus recents"), 0, 3);
    check('site : commentaire de fin, sans erreur', '', $r['error']);
    check('site : ORDER BY deja present conserve', 25, (int) $r['rows'][0]['n']);

    // --- Tri --------------------------------------------------------------------
    $r = $model->fetchPage($site('SELECT * FROM t'), 0, 3, 'n DESC');
    check('site : tri descendant', [25, 24, 23], array_map('intval', array_column($r['rows'], 'n')));

    $r = $model->fetchPage($site('SELECT * FROM t'), 0, 3, 'nope DESC');
    check('site : tri sur colonne inconnue, sans erreur', '', $r['error']);
    check('site : tri sur colonne inconnue, lignes rendues', 3, count($r['rows']));

    $r = $model->fetchPage($site('SELECT * FROM t'), 0, 3, 'n; DROP TABLE t');
    check('site : tri injecte ignore', '', $r['error']);
    check('site : la table existe toujours', 25, (int) $driver->pdo->query('SELECT COUNT(*) FROM t')->fetchColumn());

    // --- Sans pagination ----------------------------------------------------------
    $r = $model->fetchPage($site('SELECT * FROM t'), 0, null);
    check('site : sans pagination, tout est lu', 25, count($r['rows']));

    // --- Repli quand la sous-requete est refusee ----------------------------------
    $driver->failDerived = true;
    $r = $model->fetchPage($site('SELECT * FROM t'), 5, 10, 'n DESC');
    check('site : repli, sans erreur', '', $r['error']);
    check('site : repli, total', 25, $r['total']);
    check('site : repli, page triee en PHP', [20, 19, 18], array_map('intval', array_slice(array_column($r['rows'], 'n'), 0, 3)));
    check('site : repli, taille de page', 10, count($r['rows']));
    $driver->failDerived = false;

    // --- Ecritures refusees, rien n'est execute ---------------------------------
    $before = count($driver->queries);

    foreach (['DELETE FROM t', 'UPDATE t SET n = 0', 'DROP TABLE t', 'SELECT 1; DELETE FROM t', "SELECT * FROM t INTO OUTFILE '/tmp/x'"] as $bad) {
        $r = $model->fetchPage($site($bad), 0, 10);
        check("site : refuse '$bad'", true, $r['error'] !== '' && $r['rows'] === []);
    }

    check('site : aucune requete refusee n est parvenue a la base', $before, count($driver->queries));
    check('site : les donnees sont intactes', 25, (int) $driver->pdo->query('SELECT COUNT(*) FROM t')->fetchColumn());

    // --- Affichage : une seule execution par page, page hors limites ----------------
    $app->params->values = ['no_record_per_page' => 10, 'table_pagination' => 1];
    $app->input->values  = ['limitstart' => 500];

    $view = new JmmTestTableModel();
    $view->useSiteTable($site('SELECT * FROM t'));

    $driver->queries = [];
    $items   = $view->getItems();
    $columns = $view->getColumns();
    $total   = $view->getTotal();

    check('page : limitstart hors limites ramene a la derniere page', 5, count($items));
    check('page : total', 25, $total);
    check('page : colonnes', ['id', 'n', 'label'], $columns);

    $queriesAfterFirstLoad = count($driver->queries);
    $view->getItems();
    $view->getColumns();
    check('page : getItems/getColumns ne relancent pas la requete', $queriesAfterFirstLoad, count($driver->queries));
}
