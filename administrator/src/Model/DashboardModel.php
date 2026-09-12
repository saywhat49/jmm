<?php
namespace Saywhat49\Component\Jmm\Administrator\Model;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\Version;
use Saywhat49\Component\Jmm\Administrator\Helper\JmmHelper;

/**
 * Donnees du tableau de bord.
 *
 * Toutes les lectures sont enveloppees : une information manquante doit
 * degrader l'affichage d'une carte, jamais casser la page. Sur un
 * hebergement mutualise, information_schema est souvent partiellement
 * restreint, et SHOW DATABASES peut ne rien renvoyer.
 */
class DashboardModel extends BaseDatabaseModel
{
    private function joomlaDb()
    {
        return Factory::getContainer()->get('DatabaseDriver');
    }

    /** Base de donnees en cours de consultation. */
    public function getCurrentDb(): string
    {
        $requested = (string) Factory::getApplication()->getInput()->getString('dbname', '');

        if ($requested !== '') {
            return JmmHelper::cleanIdentifier($requested);
        }

        return (string) Factory::getApplication()->getConfig()->get('db', '');
    }

    /** Version du serveur, moteur, encodage. */
    public function getServerInfo(): array
    {
        $info = [
            'server'    => '',
            'collation' => '',
            'joomla'    => (new Version())->getShortVersion(),
            'php'       => PHP_VERSION,
        ];

        try {
            $db = $this->joomlaDb();
            $info['server'] = (string) $db->getVersion();

            $db->setQuery('SELECT @@collation_server');
            $info['collation'] = (string) $db->loadResult();
        } catch (\Throwable $e) {
            // Carte partiellement remplie : acceptable.
        }

        return $info;
    }

    /** Nombre de bases accessibles par l'utilisateur MySQL. */
    public function getDatabaseCount(): int
    {
        try {
            return count(JmmHelper::getDataBaseLists());
        } catch (\Throwable $e) {
            return 0;
        }
    }

    /**
     * Nombre de tables, de lignes et volume occupe par la base courante.
     * Les valeurs d'information_schema sont des estimations pour InnoDB :
     * la vue le signale plutot que de les presenter comme exactes.
     */
    public function getDbStats(string $dbname): array
    {
        $stats = ['tables' => 0, 'rows' => 0, 'data' => 0, 'index' => 0];

        if ($dbname === '') {
            return $stats;
        }

        try {
            $db = $this->joomlaDb();
            $db->setQuery(
                'SELECT COUNT(*) AS tables_count,'
                . ' COALESCE(SUM(TABLE_ROWS), 0) AS rows_count,'
                . ' COALESCE(SUM(DATA_LENGTH), 0) AS data_size,'
                . ' COALESCE(SUM(INDEX_LENGTH), 0) AS index_size'
                . ' FROM information_schema.TABLES'
                . ' WHERE TABLE_SCHEMA = ' . $db->quote($dbname)
                . " AND TABLE_TYPE = 'BASE TABLE'"
            );

            $row = $db->loadAssoc();

            if ($row) {
                $stats = [
                    'tables' => (int) $row['tables_count'],
                    'rows'   => (int) $row['rows_count'],
                    'data'   => (int) $row['data_size'],
                    'index'  => (int) $row['index_size'],
                ];
            }
        } catch (\Throwable $e) {
            // information_schema restreint : on renvoie des zeros.
        }

        return $stats;
    }

    /** Les tables les plus volumineuses de la base courante. */
    public function getLargestTables(string $dbname, int $limit = 5): array
    {
        if ($dbname === '') {
            return [];
        }

        try {
            $db = $this->joomlaDb();
            $db->setQuery(
                'SELECT TABLE_NAME AS name, ENGINE AS engine,'
                . ' COALESCE(TABLE_ROWS, 0) AS rows_count,'
                . ' COALESCE(DATA_LENGTH, 0) + COALESCE(INDEX_LENGTH, 0) AS total_size'
                . ' FROM information_schema.TABLES'
                . ' WHERE TABLE_SCHEMA = ' . $db->quote($dbname)
                . " AND TABLE_TYPE = 'BASE TABLE'"
                . ' ORDER BY total_size DESC',
                0,
                $limit
            );

            $rows = $db->loadAssocList();

            return is_array($rows) ? $rows : [];
        } catch (\Throwable $e) {
            return [];
        }
    }

    /** Compteurs des objets propres au composant. */
    public function getComponentCounts(): array
    {
        $counts = [
            'cannedqueries' => 0,
            'sitetables'    => 0,
            'templates'     => 0,
        ];

        $tables = [
            'cannedqueries' => '#__jmm_canned_queries',
            'sitetables'    => '#__jmm_sitetables',
            'templates'     => '#__jmm_templates',
        ];

        $db = $this->joomlaDb();

        foreach ($tables as $key => $table) {
            try {
                $db->setQuery('SELECT COUNT(*) FROM ' . $db->quoteName($table) . ' WHERE published = 1');
                $counts[$key] = (int) $db->loadResult();
            } catch (\Throwable $e) {
                $counts[$key] = 0;
            }
        }

        return $counts;
    }

    /**
     * Coherence entre les modeles enregistres en base et les dossiers
     * presents sur le disque. Un ecart se traduit par un affichage vide
     * cote site, sans message : autant le signaler ici.
     */
    public function getTemplateHealth(): array
    {
        $base = JPATH_SITE . '/components/com_jmm/templates';

        $health = [
            'writable'       => is_dir($base) && is_writable($base),
            'path'           => $base,
            'missingFolders' => [],
            'orphanFolders'  => [],
        ];

        $titles = [];

        try {
            $db = $this->joomlaDb();
            $db->setQuery('SELECT title FROM ' . $db->quoteName('#__jmm_templates'));
            $titles = array_filter((array) $db->loadColumn());
        } catch (\Throwable $e) {
            return $health;
        }

        foreach ($titles as $title) {
            if (!is_file($base . '/' . $title . '/index.php')) {
                $health['missingFolders'][] = $title;
            }
        }

        if (is_dir($base)) {
            foreach ((array) scandir($base) as $entry) {
                if ($entry === '.' || $entry === '..' || !is_dir($base . '/' . $entry)) {
                    continue;
                }

                if (!in_array($entry, $titles, true)) {
                    $health['orphanFolders'][] = $entry;
                }
            }
        }

        return $health;
    }

    /** Dernieres requetes enregistrees, pour un acces direct. */
    public function getRecentQueries(int $limit = 5): array
    {
        try {
            $db = $this->joomlaDb();
            $db->setQuery(
                'SELECT id, title, dbname FROM ' . $db->quoteName('#__jmm_canned_queries')
                . ' WHERE published = 1 ORDER BY id DESC',
                0,
                $limit
            );

            $rows = $db->loadAssocList();

            return is_array($rows) ? $rows : [];
        } catch (\Throwable $e) {
            return [];
        }
    }
}
