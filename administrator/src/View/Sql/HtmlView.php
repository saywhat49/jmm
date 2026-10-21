<?php
namespace Saywhat49\Component\Jmm\Administrator\View\Sql;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Saywhat49\Component\Jmm\Administrator\Helper\JmmHelper;

class HtmlView extends BaseHtmlView
{
    protected array $databases = [];
    protected array $cannedQueries = [];
    protected array $siteTables = [];
    protected array $queryResult = [];
    protected string $currentDb = '';
    protected string $query = '';

    public function display($tpl = null): void
    {
        $app = Factory::getApplication();
        $input = $app->getInput();

        $this->currentDb     = JmmHelper::requestIdentifier('dbname', $dbValid);

        if (!$dbValid) {
            JmmHelper::reportError(Text::_('COM_JMM_ERROR_INVALID_DATABASE_NAME'));
        }

        $this->query         = $input->getString('query', '', 'raw');
        $this->databases     = JmmHelper::getDataBaseLists();

        /** @var \Saywhat49\Component\Jmm\Administrator\Model\SqlModel $model */
        $model = $this->getModel();
        $this->cannedQueries = $model->getCannedQueries($this->currentDb);
        $this->siteTables    = $model->getSiteTables($this->currentDb);

        if ($input->getMethod() === 'POST' && $this->query !== '') {
            // Une requete ne s'execute que depuis le formulaire de cette page.
            // Sans jeton, un site tiers pouvait en soumettre une au nom de
            // l'administrateur connecte.
            if (!Session::checkToken('post')) {
                $app->enqueueMessage(Text::_('JINVALID_TOKEN'), 'error');
            } elseif ($dbValid) {
                $this->queryResult = $model->executeQuery($this->query, $this->currentDb);
                if (!empty($this->queryResult['msg'])) {
                    $app->enqueueMessage($this->queryResult['msg'], $this->queryResult['status'] ? 'message' : 'error');
                }
            }
        }

        JmmHelper::addSubmenu('sql');
        $this->addToolbar();

        $wa = $app->getDocument()->getWebAssetManager();
        $wa->useScript('com_jmm.sql');
        // La vue affiche un bouton .jmm-export-btn : son gestionnaire vit ici.
        $wa->useScript('com_jmm.export');
        $this->getDocument()->getWebAssetManager()->useStyle('com_jmm.admin');

        parent::display($tpl);
    }

    protected function addToolbar(): void
    {
        ToolbarHelper::title(Text::_('COM_JMM_SQL_QUERY'), 'code');
        if (Factory::getApplication()->getIdentity()->authorise('core.admin', 'com_jmm')) {
            ToolbarHelper::preferences('com_jmm');
        }
    }
}