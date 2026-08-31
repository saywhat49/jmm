<?php
namespace Saywhat49\Component\Jmm\Administrator\View\Createtable;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Saywhat49\Component\Jmm\Administrator\Helper\JmmHelper;

class HtmlView extends BaseHtmlView
{
    protected array $databases = [];
    protected string $currentDb = '';

    public function display($tpl = null): void
    {
        $input = Factory::getApplication()->getInput();
        $this->currentDb = JmmHelper::cleanIdentifier($input->getString('dbname', ''));
        $this->databases = JmmHelper::getDataBaseLists();

        JmmHelper::addSubmenu('createtable');
        $this->addToolbar();

        $wa = Factory::getApplication()->getDocument()->getWebAssetManager();
        $wa->useScript('com_jmm.createtable');

        parent::display($tpl);
    }

    protected function addToolbar(): void
    {
        ToolbarHelper::title(Text::_('COM_JMM_CREATE_TABLE'), 'plus');
        if (Factory::getApplication()->getIdentity()->authorise('core.admin', 'com_jmm')) {
            ToolbarHelper::preferences('com_jmm');
        }
    }
}