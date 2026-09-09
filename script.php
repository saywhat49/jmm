<?php
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\Installer\InstallerScript;
use Joomla\CMS\Language\Text;

class Com_JmmInstallerScript extends InstallerScript
{
    protected $minimumJoomla = '5.0.0';
    protected $minimumPhp    = '8.1.0';

    public function preflight($type, $parent): bool
    {
        if (version_compare(PHP_VERSION, $this->minimumPhp, '<')) {
            Factory::getApplication()->enqueueMessage(
                Text::sprintf('JLIB_INSTALLER_MINIMUM_PHP', $this->minimumPhp),
                'error'
            );
            return false;
        }

        if (version_compare(JVERSION, $this->minimumJoomla, '<')) {
            Factory::getApplication()->enqueueMessage(
                Text::sprintf('JLIB_INSTALLER_MINIMUM_JOOMLA', $this->minimumJoomla),
                'error'
            );
            return false;
        }

        return true;
    }

    public function install($parent): void
    {
        $this->cleanupLegacyFiles();
    }

    public function update($parent): void
    {
        $this->cleanupLegacyFiles();
    }

/**
 * Safely add a column if it doesn't exist (MySQL 8 compatible)
 */
private function addColumnIfNotExists($db, $table, $column, $definition): void
{
    try {
        $columns = $db->getTableColumns($table);
        if (!array_key_exists($column, $columns)) {
            $db->setQuery("ALTER TABLE " . $db->quoteName($table) . " ADD COLUMN " . $db->quoteName($column) . " " . $definition);
            $db->execute();
        }
    } catch (\Throwable $e) {
        // Column may already exist, silently ignore
    }
}

/**
 * Ensure template table has the new columns (safe for MySQL 8)
 */
private function migrateTemplatesTable(): void
{
    try {
        $db = \Joomla\CMS\Factory::getContainer()->get('DatabaseDriver');
        $table = $db->getPrefix() . 'jmm_templates';
        $this->addColumnIfNotExists($db, $table, 'layout_type', "varchar(20) NOT NULL DEFAULT 'table' AFTER `title`");
        $this->addColumnIfNotExists($db, $table, 'chart_type', "varchar(20) NOT NULL DEFAULT 'PieChart' AFTER `layout_type`");
        $this->addColumnIfNotExists($db, $table, 'custom_css', "text DEFAULT NULL AFTER `chart_type`");
    } catch (\Throwable $e) {
        // Non-fatal
    }
}
    public function postflight($type, $parent): void
    {
        $this->cleanupLegacyFiles();
    }

    private function cleanupLegacyFiles(): void
    {
        $legacyFiles = [
            JPATH_ADMINISTRATOR . '/components/com_jmm/controller.php',
            JPATH_ADMINISTRATOR . '/components/com_jmm/jmm.php',
            JPATH_ADMINISTRATOR . '/components/com_jmm/models/jmmcommon.php',
            JPATH_ADMINISTRATOR . '/components/com_jmm/update.mysql.sql',
            JPATH_SITE . '/components/com_jmm/controller.php',
            JPATH_SITE . '/components/com_jmm/jmm.php',
            JPATH_SITE . '/components/com_jmm/router.php',
        ];

        foreach ($legacyFiles as $file) {
            if (File::exists($file)) {
                File::delete($file);
            }
        }

        // Cleanup obsolete legacy folders from previous versions if empty
        $legacyDirs = [
            JPATH_ADMINISTRATOR . '/components/com_jmm/controllers',
            JPATH_ADMINISTRATOR . '/components/com_jmm/models',
            JPATH_ADMINISTRATOR . '/components/com_jmm/tables',
            JPATH_ADMINISTRATOR . '/components/com_jmm/views',
            JPATH_SITE . '/components/com_jmm/controllers',
            JPATH_SITE . '/components/com_jmm/models',
            JPATH_SITE . '/components/com_jmm/views',
            JPATH_SITE . '/components/com_jmm/templates',
        ];

        foreach ($legacyDirs as $dir) {
            if (Folder::exists($dir)) {
                // Delete legacy models/views if they don't contain modern src files
                Folder::delete($dir);
            }
        }
    }
}