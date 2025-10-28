<?php
defined('_JEXEC') or die;
use Joomla\CMS\MVC\View\HtmlView;

class JMMViewSiteTable extends HtmlView {
    
    protected $item;
    protected $form;
    
    function display($tpl=null) 
    {
        $this->item = $this->get('Item');
        $this->form = $this->get('Form');
        
        $wa = $this->document->getWebAssetManager();
        $wa->useScript('form.validate');
        $wa->useScript('keepalive');
        
        $this->addToolbar();
        parent::display($tpl);
    }
    
    public function addToolbar()
    {
        if($this->item->id){
            JToolBarHelper::title('Edit Site Table');
        }else{
            JToolBarHelper::title('Save Site Table');
        }
        
        JToolBarHelper::apply('sitetable.apply','JTOOLBAR_APPLY');
        JToolBarHelper::save('sitetable.save','JTOOLBAR_SAVE');
        JToolBarHelper::save2new('sitetable.save2new','JTOOLBAR_SAVE_AND_NEW');
        JToolBarHelper::cancel('sitetable.cancel');
    }
}
