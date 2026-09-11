<?php
namespace Saywhat49\Component\Jmm\Site\View\Table;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Uri\Uri;

class HtmlView extends BaseHtmlView
{
    protected array $items = [];
    protected array $columns = [];
    protected ?object $siteTable = null;
    protected $pagination;
    protected $params;

    /** Enregistrement #__jmm_templates selectionne dans l'element de menu. */
    protected ?object $jmmTemplate = null;

    /** Chemin absolu du dossier du modele utilisateur. */
    protected string $jmmTemplatePath = '';

    /** URL de base du dossier du modele utilisateur. */
    protected string $jmmTemplateUrl = '';

    /** Etat du chargement du modele, pour le bloc de diagnostic. */
    protected array $jmmDiagnostic = [];

    // --- Proprietes de compatibilite avec les modeles JMM 5.0.x ---

    /** Alias historique de $items. */
    public $rows = [];

    /** Alias historique de $siteTable. */
    public $siteTableDetails = null;

    /** Un modele peut faire $this->defaultPagination = false; */
    public $defaultPagination = true;

    /** Nom du modele en cours. */
    public $theme = '';

    /** URL du dossier du modele (trois noms historiques). */
    public $curThemeURL = '';
    public $themeBaseURL = '';
    public $templateBaseURL = '';

    public function display($tpl = null): void
    {
        /** @var \Saywhat49\Component\Jmm\Site\Model\TableModel $model */
        $model            = $this->getModel();
        $this->siteTable  = $model->getSiteTableDetails();
        $this->items      = $model->getItems();
        $this->columns    = $model->getColumns();
        $this->pagination = $model->getPagination();
        $this->params     = Factory::getApplication()->getParams('com_jmm');

        $templateId = (int) $this->params->get('jmm_template_id', 0);
        $this->jmmTemplate = $this->loadTemplateRecord($templateId);

        $base = JPATH_SITE . '/components/com_jmm/templates';

        if ($this->jmmTemplate && !empty($this->jmmTemplate->title)) {
            $this->jmmTemplatePath = $base . '/' . $this->jmmTemplate->title;
            $this->jmmTemplateUrl  = Uri::root(true) . '/components/com_jmm/templates/' . $this->jmmTemplate->title;
        }

        $this->jmmDiagnostic = [
            'template_id'   => $templateId,
            'record_found'  => $this->jmmTemplate !== null,
            'title'         => $this->jmmTemplate->title ?? '',
            'layout_type'   => $this->jmmTemplate->layout_type ?? '',
            'path'          => $this->jmmTemplatePath,
            'file_exists'   => $this->jmmTemplatePath !== '' && is_file($this->jmmTemplatePath . '/index.php'),
            'base_exists'   => is_dir($base),
            'css_url'       => '',
            'js_url'        => '',
        ];

        $document = Factory::getApplication()->getDocument();
        $wa       = $document->getWebAssetManager();
        $wa->useStyle('com_jmm.site');

        // NOTE : ne pas passer par le WebAssetManager ici. Il resout les chemins
        // relatifs via HTMLHelper, qui ne cherche que dans /media et /templates ;
        // un fichier de /components/com_jmm/templates est alors silencieusement
        // ignore. addStyleSheet/addScript ecrivent l'URL telle quelle.
        if ($this->jmmTemplatePath !== '') {
            $cssFile = $this->jmmTemplatePath . '/css/default.css';
            $jsFile  = $this->jmmTemplatePath . '/js/custom.js';

            if (is_file($cssFile)) {
                $this->jmmDiagnostic['css_url'] = $this->jmmTemplateUrl . '/css/default.css?v=' . filemtime($cssFile);
                $document->addStyleSheet($this->jmmDiagnostic['css_url']);
            }

            if (is_file($jsFile)) {
                $this->jmmDiagnostic['js_url'] = $this->jmmTemplateUrl . '/js/custom.js?v=' . filemtime($jsFile);
                $document->addScript($this->jmmDiagnostic['js_url'], [], ['defer' => true]);
            }
        }

        // Compatibilite avec les modeles ecrits pour JMM 5.0.x.
        $this->rows             = $this->items;
        $this->siteTableDetails = $this->siteTable;
        $this->theme            = $this->jmmTemplate->title ?? '';
        $this->curThemeURL      = $this->jmmTemplateUrl;
        $this->themeBaseURL     = $this->jmmTemplateUrl;
        $this->templateBaseURL  = $this->jmmTemplateUrl;

        if ($this->jmmTemplate && !empty($this->jmmTemplate->custom_css)) {
            $document->addStyleDeclaration((string) $this->jmmTemplate->custom_css);
        }

        parent::display($tpl);
    }

    /**
     * Le bloc de diagnostic n'est visible que par un utilisateur ayant acces
     * a l'administration du composant.
     */
    protected function canSeeDiagnostic(): bool
    {
        return Factory::getApplication()->getIdentity()->authorise('core.manage', 'com_jmm');
    }

    private function loadTemplateRecord(int $id): ?object
    {
        if ($id <= 0) {
            return null;
        }

        $db    = Factory::getContainer()->get('DatabaseDriver');
        $query = $db->getQuery(true)
            ->select('*')
            ->from($db->quoteName('#__jmm_templates'))
            ->where($db->quoteName('id') . ' = ' . $id)
            ->where($db->quoteName('published') . ' = 1');

        try {
            $db->setQuery($query);
            return $db->loadObject() ?: null;
        } catch (\Throwable $e) {
            return null;
        }
    }
}
