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
    public static function addSubmenu(string $vName = 'databases'): void
    {
        $input = Factory::getApplication()->getInput();
        $dbname = $input->getString('dbname', '');
        $urlSuffix = $dbname !== '' ? '&dbname=' . urlencode($dbname) : '';

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

    public static function getDatabaseConnection(?string $targetDb = null): DatabaseDriver
    {
        $app = Factory::getApplication();
        $params = ComponentHelper::getParams('com_jmm');
        $useCustom = (int) $params->get('dbsettings', 0) === 1;

        if ($useCustom) {
            $options = [
                'driver'   => 'mysql',
                'host'     => (string) $params->get('dbhost', 'localhost'),
                'user'     => (string) $params->get('dbusername', ''),
                'password' => (string) $params->get('dbpass', ''),
                'database' => $targetDb ? self::cleanIdentifier($targetDb) : (string) $params->get('dbname', ''),
                'prefix'   => (string) $params->get('dbprefix', ''),
            ];

            try {
                $factory = new DatabaseFactory();
                return $factory->getDriver('mysql', $options);
            } catch (\Throwable $e) {
                $app->enqueueMessage(Text::sprintf('COM_JMM_CUSTOM_DB_CONNECT_ERROR', $e->getMessage()), 'warning');
            }
        }

        $db = Factory::getContainer()->get('DatabaseDriver');

        if (!empty($targetDb)) {
            $safeDb = self::cleanIdentifier($targetDb);
            if ($safeDb !== '') {
                $currentDb = method_exists($db, 'getDatabase') ? (string) $db->getDatabase() : '';
                // Only attempt switch if target is different from the currently active database
                if ($currentDb === '' || strcasecmp($safeDb, $currentDb) !== 0) {
                    try {
                        if (method_exists($db, 'select')) {
                            $db->select($safeDb);
                        } else {
                            $db->setQuery('USE ' . $db->quoteName($safeDb))->execute();
                        }
                    } catch (\Throwable $e) {
                        // Silent fail if same connection or log warning only if debug enabled
                    }
                }
            }
        }

        return $db;
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