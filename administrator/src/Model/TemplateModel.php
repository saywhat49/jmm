<?php
namespace Saywhat49\Component\Jmm\Administrator\Model;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\Filter\OutputFilter;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\AdminModel;

class TemplateModel extends AdminModel
{
    /**
     * Racine des modèles utilisateur (hors manifeste : préservée par les mises à jour).
     */
    public static function getTemplatesBasePath(): string
    {
        return JPATH_SITE . '/components/com_jmm/templates';
    }

    public static function getTemplatePath(string $title): string
    {
        return self::getTemplatesBasePath() . '/' . $title;
    }

    /**
     * Seuls les administrateurs du composant peuvent écrire du PHP.
     */
    protected function canEditPhp(): bool
    {
        return Factory::getApplication()->getIdentity()->authorise('core.admin', 'com_jmm');
    }

    public function getTable($type = 'Template', $prefix = 'Administrator', $config = [])
    {
        return parent::getTable($type, $prefix, $config);
    }

    public function getForm($data = [], $loadData = true)
    {
        $form = $this->loadForm('com_jmm.template', 'template', ['control' => 'jform', 'load_data' => $loadData]);

        if ($form && !$this->canEditPhp()) {
            $form->removeField('php');
            $form->removeField('js');
        }

        return $form;
    }

    protected function loadFormData()
    {
        $data = Factory::getApplication()->getUserState('com_jmm.edit.template.data', []);
        if (empty($data)) {
            $data = $this->getItem();
        }
        return $data;
    }

    /**
     * Charge l'enregistrement + le contenu des fichiers du dossier associé.
     */
    public function getItem($pk = null)
    {
        $item = parent::getItem($pk);

        if ($item && !empty($item->title)) {
            $folder = self::getTemplatePath($item->title);

            $item->php = $this->readFile($folder . '/index.php');
            $item->css_file = $this->readFile($folder . '/css/default.css');
            $item->js  = $this->readFile($folder . '/js/custom.js');
            $item->template_path = $folder;

            // Premier passage : si le dossier n'existe pas encore, on amorce le PHP.
            if ($item->php === '' && !is_dir($folder)) {
                $item->php = $this->getStarterPhp();
            }
        }

        return $item;
    }

    /**
     * Enregistre l'enregistrement ET écrit les fichiers du dossier du modèle.
     */
    public function save($data)
    {
        $table   = $this->getTable();
        $pk      = (!empty($data['id'])) ? (int) $data['id'] : (int) $this->getState($this->getName() . '.id');
        $isNew   = true;
        $oldTitle = '';

        if ($pk > 0 && $table->load($pk)) {
            $isNew    = false;
            $oldTitle = (string) $table->title;
        }

        $title = OutputFilter::stringURLSafe(trim((string) ($data['title'] ?? '')));

        if ($title === '') {
            $this->setError(Text::_('COM_JMM_ERROR_TEMPLATE_TITLE_REQUIRED'));
            return false;
        }

        $data['title'] = $title;
        $folder = self::getTemplatePath($title);

        // Renommage du dossier si le titre a changé.
        if (!$isNew && $oldTitle !== '' && $oldTitle !== $title) {
            $oldFolder = self::getTemplatePath($oldTitle);

            if (is_dir($oldFolder) && !is_dir($folder)) {
                try {
                    Folder::move($oldFolder, $folder);
                } catch (\Throwable $e) {
                    $this->setError(Text::sprintf('COM_JMM_ERROR_TEMPLATE_FOLDER', $e->getMessage()));
                    return false;
                }
            }
        }

        // Création de l'arborescence.
        foreach ([$folder, $folder . '/css', $folder . '/js', $folder . '/images'] as $dir) {
            if (!is_dir($dir)) {
                try {
                    Folder::create($dir);
                } catch (\Throwable $e) {
                    $this->setError(Text::sprintf('COM_JMM_ERROR_TEMPLATE_FOLDER', $e->getMessage()));
                    return false;
                }
            }
        }

        // Écriture des fichiers.
        try {
            if ($this->canEditPhp() && array_key_exists('php', $data)) {
                $php = (string) $data['php'];

                if ($php === '') {
                    $php = $this->getStarterPhp();
                }

                File::write($folder . '/index.php', $php);
            } elseif (!is_file($folder . '/index.php')) {
                File::write($folder . '/index.php', $this->getStarterPhp());
            }

            if (array_key_exists('css_file', $data)) {
                File::write($folder . '/css/default.css', (string) $data['css_file']);
            } elseif (!is_file($folder . '/css/default.css')) {
                File::write($folder . '/css/default.css', "/* CSS du modèle " . $title . " */\n");
            }

            if ($this->canEditPhp() && array_key_exists('js', $data)) {
                File::write($folder . '/js/custom.js', (string) $data['js']);
            } elseif (!is_file($folder . '/js/custom.js')) {
                File::write($folder . '/js/custom.js', "// JS du modèle " . $title . "\n");
            }
        } catch (\Throwable $e) {
            $this->setError(Text::sprintf('COM_JMM_ERROR_TEMPLATE_WRITE', $e->getMessage()));
            return false;
        }

        // Ces champs sont stockés sur disque, pas en base.
        unset($data['php'], $data['css_file'], $data['js'], $data['template_path']);

        return parent::save($data);
    }

    /**
     * Supprime aussi le dossier associé.
     */
    public function delete(&$pks)
    {
        $pks     = (array) $pks;
        $folders = [];

        foreach ($pks as $pk) {
            $table = $this->getTable();

            if ($table->load((int) $pk) && !empty($table->title)) {
                $folders[] = self::getTemplatePath($table->title);
            }
        }

        if (!parent::delete($pks)) {
            return false;
        }

        foreach ($folders as $folder) {
            if (is_dir($folder) && basename($folder) !== 'default') {
                try {
                    Folder::delete($folder);
                } catch (\Throwable $e) {
                    // Non bloquant : l'enregistrement est déjà supprimé.
                }
            }
        }

        return true;
    }

    private function readFile(string $path): string
    {
        return is_file($path) ? (string) file_get_contents($path) : '';
    }

    private function getStarterPhp(): string
    {
        return <<<'PHPTPL'
<?php
/**
 * Modèle JMM personnalisé.
 *
 * Variables disponibles :
 *   $rows   tableau de lignes associatives (résultat de la requête)
 *   $cols   noms des colonnes
 *   $params paramètres de l'élément de menu
 *   $this   la vue frontend (pagination, escape, ...)
 */
defined('_JEXEC') or die('Restricted access');
?>
<?php foreach ($rows as $row) : ?>
    <div class="jmm-row">
        <?php foreach ($cols as $col) : ?>
            <span class="jmm-<?php echo $col; ?>"><?php echo $row[$col]; ?></span>
        <?php endforeach; ?>
    </div>
<?php endforeach; ?>
PHPTPL;
    }
}
