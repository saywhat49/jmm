<?php
namespace Saywhat49\Component\Jmm\Administrator\Helper;

defined('_JEXEC') or die;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\Helpers\Sidebar;
use Joomla\CMS\Language\Text;
use Joomla\Database\DatabaseDriver;
use Joomla\Database\DatabaseFactory;

class JmmHelper
{
    /**
     * Types de colonnes proposes par le concepteur de tables, avec la forme
     * de longueur que chacun accepte :
     *
     *   none      aucune longueur (DATE, TEXT, BLOB, JSON...)
     *   int       un entier facultatif (largeur d'affichage des entiers)
     *   int_req   un entier obligatoire (VARCHAR, VARBINARY)
     *   decimal   (M) ou (M,D)  -- DECIMAL, FLOAT, DOUBLE
     *   fsp       precision des fractions de seconde, 0 a 6
     *   bit       un entier de 1 a 64
     *
     * @return array<string,string>
     */
    public static function getColumnTypes(): array
    {
        return [
            'INT'        => 'int',
            'TINYINT'    => 'int',
            'SMALLINT'   => 'int',
            'MEDIUMINT'  => 'int',
            'BIGINT'     => 'int',
            'BOOLEAN'    => 'none',
            'SERIAL'     => 'none',
            'DECIMAL'    => 'decimal',
            'NUMERIC'    => 'decimal',
            'FLOAT'      => 'decimal',
            'DOUBLE'     => 'decimal',
            'REAL'       => 'decimal',
            'BIT'        => 'bit',
            'CHAR'       => 'int',
            'VARCHAR'    => 'int_req',
            'TINYTEXT'   => 'none',
            'TEXT'       => 'none',
            'MEDIUMTEXT' => 'none',
            'LONGTEXT'   => 'none',
            'JSON'       => 'none',
            'BINARY'     => 'int',
            'VARBINARY'  => 'int_req',
            'TINYBLOB'   => 'none',
            'BLOB'       => 'none',
            'MEDIUMBLOB' => 'none',
            'LONGBLOB'   => 'none',
            'DATE'       => 'none',
            'YEAR'       => 'none',
            'DATETIME'   => 'fsp',
            'TIMESTAMP'  => 'fsp',
            'TIME'       => 'fsp',
        ];
    }

    /** Types acceptant AUTO_INCREMENT. */
    public static function getAutoIncrementTypes(): array
    {
        return ['INT', 'TINYINT', 'SMALLINT', 'MEDIUMINT', 'BIGINT', 'SERIAL'];
    }

    public static function addSubmenu(string $vName = 'databases'): void
    {
        $input = Factory::getApplication()->getInput();
        $dbname = $input->getString('dbname', '');
        $urlSuffix = $dbname !== '' ? '&dbname=' . urlencode($dbname) : '';

        Sidebar::addEntry(
            Text::_('COM_JMM_DASHBOARD'),
            'index.php?option=com_jmm&view=dashboard' . $urlSuffix,
            $vName === 'dashboard'
        );

        Sidebar::addEntry(
            Text::_('COM_JMM_DATABASES'),
            'index.php?option=com_jmm&view=databases' . $urlSuffix,
            $vName === 'databases'
        );

        Sidebar::addEntry(
            Text::_('COM_JMM_TABLES'),
            'index.php?option=com_jmm&view=tables' . $urlSuffix,
            $vName === 'tables'
        );

        Sidebar::addEntry(
            Text::_('COM_JMM_SQL_QUERY'),
            'index.php?option=com_jmm&view=sql' . $urlSuffix,
            $vName === 'sql'
        );

        Sidebar::addEntry(
            Text::_('COM_JMM_CANNED_QUERY'),
            'index.php?option=com_jmm&view=cannedqueries' . $urlSuffix,
            $vName === 'cannedqueries' || $vName === 'cannedquery'
        );

        Sidebar::addEntry(
            Text::_('COM_JMM_SITE_TABLES'),
            'index.php?option=com_jmm&view=sitetables' . $urlSuffix,
            $vName === 'sitetables' || $vName === 'sitetable'
        );

        Sidebar::addEntry(
            Text::_('COM_JMM_CREATE_TABLE'),
            'index.php?option=com_jmm&view=createtable' . $urlSuffix,
            $vName === 'createtable'
        );

        Sidebar::addEntry(
            Text::_('COM_JMM_INSERT_DATA'),
            'index.php?option=com_jmm&view=insert' . $urlSuffix,
            $vName === 'insert'
        );

        Sidebar::addEntry(
            Text::_('COM_JMM_TEMPLATES'),
            'index.php?option=com_jmm&view=templates' . $urlSuffix,
            $vName === 'templates' || $vName === 'template'
        );
    }

    public static function cleanIdentifier(string $identifier): string
    {
        return preg_replace('/[^A-Za-z0-9_$-]/', '', trim($identifier));
    }

    /** Connexions dediees deja ouvertes, indexees par nom de base. */
    private static array $connections = [];

    /**
     * Retourne une connexion pointant reellement sur $targetDb.
     *
     * On n'emet PAS "USE <base>" sur le DatabaseDriver partage de Joomla :
     * la bascule n'est pas reversible pour le reste de la requete (session,
     * menus, journaux passeraient sur la mauvaise base) et elle echoue en
     * silence quand l'utilisateur MySQL n'a pas de droits sur la base cible.
     * On ouvre a la place une connexion distincte, mise en cache.
     */
    public static function getDatabaseConnection(?string $targetDb = null): DatabaseDriver
    {
        $app    = Factory::getApplication();
        $config = $app->getConfig();
        $params = ComponentHelper::getParams('com_jmm');
        $safeDb = $targetDb ? self::cleanIdentifier($targetDb) : '';

        $useCustom = (int) $params->get('dbsettings', 0) === 1;

        if ($useCustom) {
            $options = [
                'driver'   => 'mysqli',
                'host'     => (string) $params->get('dbhost', 'localhost'),
                'user'     => (string) $params->get('dbusername', ''),
                'password' => (string) $params->get('dbpass', ''),
                'database' => $safeDb !== '' ? $safeDb : (string) $params->get('dbname', ''),
                'prefix'   => (string) $params->get('dbprefix', ''),
            ];

            $driver = self::openConnection($options);

            if ($driver !== null) {
                return $driver;
            }
        }

        $shared = Factory::getContainer()->get('DatabaseDriver');

        // Pas de base demandee, ou base identique a celle de Joomla.
        if ($safeDb === '' || strcasecmp($safeDb, (string) $config->get('db', '')) === 0) {
            return $shared;
        }

        $options = [
            'driver'   => (string) $config->get('dbtype', 'mysqli'),
            'host'     => (string) $config->get('host', 'localhost'),
            'user'     => (string) $config->get('user', ''),
            'password' => (string) $config->get('password', ''),
            'database' => $safeDb,
            'prefix'   => (string) $config->get('dbprefix', ''),
        ];

        $driver = self::openConnection($options);

        if ($driver !== null) {
            return $driver;
        }

        // Echec explicite : l'utilisateur doit savoir pourquoi il voit
        // les tables de la base de Joomla et non celles qu'il a demandees.
        // Reserve aux gestionnaires : un visiteur du site n'a pas a lire
        // un diagnostic sur la configuration des bases de donnees.
        if ($app->getIdentity()->authorise('core.manage', 'com_jmm')) {
            $app->enqueueMessage(
                Text::sprintf('COM_JMM_DB_SWITCH_FAILED', $safeDb),
                'warning'
            );
        }

        return $shared;
    }

    /**
     * Ouvre (et met en cache) une connexion, ou null en cas d'echec.
     */
    private static function openConnection(array $options): ?DatabaseDriver
    {
        $key = ($options['driver'] ?? '') . '|' . ($options['host'] ?? '')
            . '|' . ($options['user'] ?? '') . '|' . ($options['database'] ?? '');

        if (isset(self::$connections[$key])) {
            return self::$connections[$key];
        }

        if (($options['database'] ?? '') === '') {
            return null;
        }

        $driverName = $options['driver'] ?: 'mysqli';

        if ($driverName === 'mysql') {
            $driverName = 'mysqli';
        }

        $options['driver'] = $driverName;

        try {
            $factory = new DatabaseFactory();
            $driver  = $factory->getDriver($driverName, $options);
            $driver->connect();
        } catch (\Throwable $e) {
            return null;
        }

        self::$connections[$key] = $driver;

        return $driver;
    }

    public static function getDataBaseLists(?DatabaseDriver $db = null): array
    {
        $db = $db ?? self::getDatabaseConnection();
        try {
            $db->setQuery('SHOW DATABASES');
            $rows = $db->loadColumn();
            return is_array($rows) ? $rows : [];
        } catch (\Throwable $e) {
            return [];
        }
    }

    public static function getTablesFromDB(?DatabaseDriver $db = null): array
    {
        $db = $db ?? self::getDatabaseConnection();
        try {
            $db->setQuery('SHOW TABLES');
            $rows = $db->loadColumn();
            return is_array($rows) ? $rows : [];
        } catch (\Throwable $e) {
            return [];
        }
    }

    public static function getColumnsFromTable(string $table, ?DatabaseDriver $db = null): array
    {
        $cleanTable = self::cleanIdentifier($table);
        if ($cleanTable === '') {
            return [];
        }

        $db = $db ?? self::getDatabaseConnection();
        try {
            $db->setQuery('SHOW COLUMNS FROM ' . $db->quoteName($cleanTable));
            $rows = $db->loadAssocList();
            $cols = [];
            if (is_array($rows)) {
                foreach ($rows as $row) {
                    if (isset($row['Field'])) {
                        $cols[] = $row['Field'];
                    }
                }
            }
            return $cols;
        } catch (\Throwable $e) {
            return [];
        }
    }
}