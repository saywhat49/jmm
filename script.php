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

    public function postflight($type, $parent): void
    {
        $this->cleanupLegacyFiles();
        $this->deployTemplates($parent);
        $this->flagCustomTemplates();
    }

    /**
     * Copie les modeles livres dans le paquet vers
     * /components/com_jmm/templates/<nom>/.
     *
     * Un dossier deja present sur le serveur n'est JAMAIS ecrase : les
     * modifications faites depuis l'administration sont conservees. Pour
     * forcer le remplacement d'un modele, supprimez son dossier avant
     * d'installer.
     */
    private function deployTemplates($parent): void
    {
        try {
            $source = $parent->getParent()->getPath('source') . '/templates';
        } catch (\Throwable $e) {
            return;
        }

        if (!Folder::exists($source)) {
            return;
        }

        $target = JPATH_SITE . '/components/com_jmm/templates';

        if (!Folder::exists($target)) {
            Folder::create($target);
        }

        $copied = [];
        $kept   = [];

        foreach (Folder::folders($source) as $name) {
            if (Folder::exists($target . '/' . $name)) {
                $kept[] = $name;
                continue;
            }

            try {
                Folder::copy($source . '/' . $name, $target . '/' . $name, '', true);
                $copied[] = $name;
            } catch (\Throwable $e) {
                // Non bloquant : on continue avec les autres modeles.
            }
        }

        foreach (Folder::files($source) as $file) {
            if (!File::exists($target . '/' . $file)) {
                File::copy($source . '/' . $file, $target . '/' . $file);
            }
        }

        $this->registerTemplates($copied);

        $app = Factory::getApplication();

        // Les chaines vivent dans com_jmm.sys.ini ; le repli anglais couvre le
        // cas ou le fichier de langue n'est pas encore charge pendant l'installation.
        $app->getLanguage()->load('com_jmm.sys', JPATH_ADMINISTRATOR, null, true);

        if ($copied) {
            $app->enqueueMessage(
                $this->translate('COM_JMM_INSTALL_TEMPLATES_INSTALLED', 'JMM: %d template(s) installed: %s', count($copied), implode(', ', $copied)),
                'message'
            );
        }

        if ($kept) {
            $app->enqueueMessage(
                $this->translate('COM_JMM_INSTALL_TEMPLATES_KEPT', 'JMM: %d template(s) already present, kept unchanged: %s', count($kept), implode(', ', $kept)),
                'info'
            );
        }
    }

    /** Texte traduit, ou $fallback si la cle n'est pas disponible. */
    private function translate(string $key, string $fallback, ...$args): string
    {
        $text = Text::_($key);

        return vsprintf($text === $key ? $fallback : $text, $args);
    }

    /**
     * Cree l'enregistrement #__jmm_templates manquant pour les modeles copies.
     */
    private function registerTemplates(array $names): void
    {
        if (!$names) {
            return;
        }

        try {
            $db = Factory::getContainer()->get('DatabaseDriver');

            foreach ($names as $name) {
                $db->setQuery(
                    'SELECT COUNT(*) FROM ' . $db->quoteName('#__jmm_templates')
                    . ' WHERE ' . $db->quoteName('title') . ' = ' . $db->quote($name)
                );

                if ((int) $db->loadResult() > 0) {
                    continue;
                }

                $db->setQuery(
                    'INSERT INTO ' . $db->quoteName('#__jmm_templates')
                    . ' (' . $db->quoteName('title') . ', ' . $db->quoteName('layout_type')
                    . ', ' . $db->quoteName('published') . ', ' . $db->quoteName('datetime') . ')'
                    . ' VALUES (' . $db->quote($name) . ', ' . $db->quote('custom') . ', 1, NOW())'
                );
                $db->execute();
            }
        } catch (\Throwable $e) {
            // Non bloquant.
        }
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
        ];

        // NOTE : /components/com_jmm/templates ne DOIT PAS être supprimé.
        // Ce dossier contient les modèles PHP créés par l'utilisateur.

        foreach ($legacyDirs as $dir) {
            if (Folder::exists($dir)) {
                // Delete legacy models/views if they don't contain modern src files
                Folder::delete($dir);
            }
        }

        $this->ensureTemplatesFolder();
    }

    /**
     * Passe en layout_type = 'custom' tout modèle dont le dossier contient
     * déjà un index.php (modèles restaurés depuis une version 5.0.x).
     */
    private function flagCustomTemplates(): void
    {
        try {
            $db    = Factory::getContainer()->get('DatabaseDriver');
            $base  = JPATH_SITE . '/components/com_jmm/templates';

            $db->setQuery('SELECT id, title, layout_type FROM ' . $db->quoteName('#__jmm_templates'));
            $rows = $db->loadObjectList();

            foreach ((array) $rows as $row) {
                if ($row->layout_type === 'custom' || empty($row->title)) {
                    continue;
                }

                if (!File::exists($base . '/' . $row->title . '/index.php')) {
                    continue;
                }

                $db->setQuery(
                    'UPDATE ' . $db->quoteName('#__jmm_templates')
                    . ' SET ' . $db->quoteName('layout_type') . ' = ' . $db->quote('custom')
                    . ' WHERE ' . $db->quoteName('id') . ' = ' . (int) $row->id
                );
                $db->execute();
            }
        } catch (\Throwable $e) {
            // Non bloquant.
        }
    }

    /**
     * Cree la racine des modeles utilisateur si elle est absente.
     * Le modele "default" est fourni par le paquet, pas genere ici.
     */
    private function ensureTemplatesFolder(): void
    {
        $base = JPATH_SITE . '/components/com_jmm/templates';

        if (!Folder::exists($base)) {
            Folder::create($base);
        }

        if (!File::exists($base . '/index.html')) {
            File::write($base . '/index.html', '<!DOCTYPE html><title></title>');
        }
    }

}