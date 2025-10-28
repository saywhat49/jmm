<?php
/**
 * @package		JMM
 * @link		http://adidac.github.com/jmm/index.html
 * @license		GNU/GPL
 * @copyright	Biswarup Adhikari
*/
defined('_JEXEC') or die;
use Joomla\CMS\MVC\View\HtmlView;
('Restricted access');
class JMMViewSiteTable extends HtmlView {
	
	protected $item;
	protected $form;
	
	function display($tpl=null) 
	{
		$this->item=$this->get('Item');
		$this->form=$this->get('Form');
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
		
		
		JToolBarHelper::apply('sitetable.apply','JToolBar_APPLY');
		JToolBarHelper::save('sitetable.save','JToolBar_SAVE');
		JToolBarHelper::save2new('sitetable.save2new','JToolBar_SAVE_AND_NEW');
		JToolBarHelper::cancel('sitetable.cancel');
	}

}