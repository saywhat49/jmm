<?php
namespace Saywhat49\Component\Jmm\Administrator\View\Templates;

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

        JmmHelper::addSubmenu('templates');
        $this->addToolbar();

        parent::display($tpl);
    }

    protected function addToolbar(): void
    {
        $canDo = Factory::getApplication()->getIdentity();
        ToolbarHelper::title(Text::_('COM_JMM_TEMPLATES'), 'color-palette');

        if ($canDo->authorise('core.admin', 'com_jmm')) {
            ToolbarHelper::addNew('template.add');
            ToolbarHelper::editList('template.edit');
            ToolbarHelper::publish('templates.publish', 'JTOOLBAR_PUBLISH', true);
            ToolbarHelper::unpublish('templates.unpublish', 'JTOOLBAR_UNPUBLISH', true);
            ToolbarHelper::deleteList(Text::_('COM_JMM_CONFIRM_DELETE'), 'templates.delete');
            ToolbarHelper::preferences('com_jmm');
        }
    }
}