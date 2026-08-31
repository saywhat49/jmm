<?php
namespace Saywhat49\Component\Jmm\Administrator\View\Tables;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Saywhat49\Component\Jmm\Administrator\Helper\JmmHelper;

class HtmlView extends BaseHtmlView
{
    protected $items;
    protected $pagination;
    protected $state;
    protected array $tables = [];
    protected array $databases = [];
    protected string $activeTable = '';
    protected string $activeDb = '';
    protected string $action = '';

    public function display($tpl = null): void
    {
        $input = Factory::getApplication()->getInput();
        $this->items       = $this->get('Items');
        $this->pagination  = $this->get('Pagination');
        $this->state       = $this->get('State');
        $this->tables      = $this->get('Tables');
        $this->databases   = $this->get('Databases');
        $this->activeTable = JmmHelper::cleanIdentifier($input->getString('tbl', ''));
        $this->activeDb    = JmmHelper::cleanIdentifier($input->getString('dbname', ''));
        $this->action      = $input->getCmd('action', '');

        JmmHelper::addSubmenu('tables');
        $this->addToolbar();

        $wa = Factory::getApplication()->getDocument()->getWebAssetManager();
        $wa->useScript('com_jmm.export');

        parent::display($tpl);
    }

    protected function addToolbar(): void
    {
        ToolbarHelper::title(Text::_('COM_JMM_TABLES'), 'table');
        if (Factory::getApplication()->getIdentity()->authorise('core.admin', 'com_jmm')) {
            ToolbarHelper::preferences('com_jmm');
        }
    }
}