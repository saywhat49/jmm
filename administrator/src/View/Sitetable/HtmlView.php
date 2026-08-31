<?php
namespace Saywhat49\Component\Jmm\Administrator\View\Sitetable;

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;

class HtmlView extends BaseHtmlView
{
    protected $form;
    protected $item;
    protected $state;

    public function display($tpl = null): void
    {
        $this->form  = $this->get('Form');
        $this->item  = $this->get('Item');
        $this->state = $this->get('State');

        $this->addToolbar();
        parent::display($tpl);
    }

    protected function addToolbar(): void
    {
        $isNew = empty($this->item->id);
        ToolbarHelper::title($isNew ? Text::_('COM_JMM_NEW_SITE_TABLE') : Text::_('COM_JMM_EDIT_SITE_TABLE'), 'globe');
        ToolbarHelper::apply('sitetable.apply');
        ToolbarHelper::save('sitetable.save');
        ToolbarHelper::save2new('sitetable.save2new');
        ToolbarHelper::cancel('sitetable.cancel', $isNew ? 'JTOOLBAR_CANCEL' : 'JTOOLBAR_CLOSE');
    }
}