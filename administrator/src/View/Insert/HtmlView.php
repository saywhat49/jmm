<?php
namespace Saywhat49\Component\Jmm\Administrator\View\Insert;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Saywhat49\Component\Jmm\Administrator\Helper\JmmHelper;

class HtmlView extends BaseHtmlView
{
    protected array $databases = [];
    protected array $tables = [];
    protected string $selectedDb = '';
    protected string $selectedTable = '';

    public function display($tpl = null): void
    {
        $input = Factory::getApplication()->getInput();
        $this->selectedDb    = JmmHelper::requestIdentifier('dbname', $dbValid);
        $this->selectedTable = JmmHelper::requestIdentifier('tbl');

        $this->databases = JmmHelper::getDataBaseLists();

        if (!$dbValid) {
            JmmHelper::reportError(Text::_('COM_JMM_ERROR_INVALID_DATABASE_NAME'));
            $this->selectedTable = '';
        } else {
            $db = JmmHelper::tryConnection($this->selectedDb);
            $this->tables = $db !== null ? JmmHelper::getTablesFromDB($db) : [];
        }

        // Une table absente de la base n'est pas selectionnable.
        if ($this->selectedTable !== '' && !in_array($this->selectedTable, $this->tables, true)) {
            $this->selectedTable = '';
        }

        $wa = $this->getDocument()->getWebAssetManager();

        $this->getDocument()->getWebAssetManager()->useStyle('com_jmm.admin');

        JmmHelper::addSubmenu('insert');
        $this->addToolbar();

        parent::display($tpl);
    }

    protected function addToolbar(): void
    {
        ToolbarHelper::title(Text::_('COM_JMM_INSERT_DATA'), 'pencil');
        if (!empty($this->selectedTable)) {
            ToolbarHelper::save('insert.save', 'COM_JMM_INSERT_ROW');
        }
        if (Factory::getApplication()->getIdentity()->authorise('core.admin', 'com_jmm')) {
            ToolbarHelper::preferences('com_jmm');
        }
    }
}