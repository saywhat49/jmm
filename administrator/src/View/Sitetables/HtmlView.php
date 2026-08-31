<?php
namespace Saywhat49\Component\Jmm\Administrator\View\Sitetables;

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

    public function display($tpl = null): void
    {
        $this->items      = $this->get('Items');
        $this->pagination = $this->get('Pagination');
        $this->state      = $this->get('State');

        JmmHelper::addSubmenu('sitetables');
        $this->addToolbar();

        $wa = Factory::getApplication()->getDocument()->getWebAssetManager();
        $wa->useScript('com_jmm.export');

        parent::display($tpl);
    }

    protected function addToolbar(): void
    {
        $canDo = Factory::getApplication()->getIdentity();
        ToolbarHelper::title(Text::_('COM_JMM_SITE_TABLES'), 'globe');

        if ($canDo->authorise('core.create', 'com_jmm')) {
            ToolbarHelper::addNew('sitetable.add');
        }
        if ($canDo->authorise('core.edit', 'com_jmm')) {
            ToolbarHelper::editList('sitetable.edit');
        }
        if ($canDo->authorise('core.edit.state', 'com_jmm')) {
            ToolbarHelper::publish('sitetables.publish', 'JTOOLBAR_PUBLISH', true);
            ToolbarHelper::unpublish('sitetables.unpublish', 'JTOOLBAR_UNPUBLISH', true);
        }
        if ($canDo->authorise('core.delete', 'com_jmm')) {
            ToolbarHelper::deleteList(Text::_('COM_JMM_CONFIRM_DELETE'), 'sitetables.delete');
        }
        if ($canDo->authorise('core.admin', 'com_jmm')) {
            ToolbarHelper::preferences('com_jmm');
        }
    }
}