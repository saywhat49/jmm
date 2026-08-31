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
        $this->selectedDb    = JmmHelper::cleanIdentifier($input->getString('dbname', ''));
        $this->selectedTable = JmmHelper::cleanIdentifier($input->getString('tbl', ''));

        $this->databases = JmmHelper::getDataBaseLists();
        $db = JmmHelper::getDatabaseConnection($this->selectedDb);
        $this->tables = JmmHelper::getTablesFromDB($db);

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