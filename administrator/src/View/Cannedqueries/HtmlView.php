<?php
namespace Saywhat49\Component\Jmm\Administrator\View\Cannedqueries;

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
    public $filterForm;
    public $activeFilters;

    public function display($tpl = null): void
    {
        $this->items         = $this->get('Items');
        $this->pagination    = $this->get('Pagination');
        $this->state         = $this->get('State');
        $this->filterForm    = $this->get('FilterForm');
        $this->activeFilters = $this->get('ActiveFilters');

        JmmHelper::addSubmenu('cannedqueries');
        $this->addToolbar();

        $wa = Factory::getApplication()->getDocument()->getWebAssetManager();
        $wa->useScript('com_jmm.export');

        parent::display($tpl);
    }

    protected function addToolbar(): void
    {
        $canDo = Factory::getApplication()->getIdentity();
        ToolbarHelper::title(Text::_('COM_JMM_CANNED_QUERY'), 'bookmark');

        if ($canDo->authorise('core.create', 'com_jmm')) {
            ToolbarHelper::addNew('cannedquery.add');
        }
        if ($canDo->authorise('core.edit', 'com_jmm')) {
            ToolbarHelper::editList('cannedquery.edit');
        }
        if ($canDo->authorise('core.edit.state', 'com_jmm')) {
            ToolbarHelper::publish('cannedqueries.publish', 'JTOOLBAR_PUBLISH', true);
            ToolbarHelper::unpublish('cannedqueries.unpublish', 'JTOOLBAR_UNPUBLISH', true);
        }
        if ($canDo->authorise('core.delete', 'com_jmm')) {
            ToolbarHelper::deleteList(Text::_('COM_JMM_CONFIRM_DELETE'), 'cannedqueries.delete');
        }
        if ($canDo->authorise('core.admin', 'com_jmm')) {
            ToolbarHelper::preferences('com_jmm');
        }
    }
}