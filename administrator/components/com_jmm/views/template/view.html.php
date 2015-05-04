<?php
/**
 * @package		JMM
 * @link		http://adidac.github.com/jmm/index.html
 * @license		GNU/GPL
 * @copyright	Biswarup Adhikari
*/
defined('_JEXEC') or die('Restricted access');
class JMMViewTemplate extends JViewLegacy {
	
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
			JToolBarHelper::title('Edit Template', 'template.png');
		}else{
			JToolBarHelper::title('Save Template', 'template.png');
		}
		
		
		JToolBarHelper::apply('template.apply','JToolBar_APPLY');
		JToolBarHelper::save('template.save','JToolBar_SAVE');
		JToolBarHelper::save2new('template.save2new','JToolBar_SAVE_AND_NEW');
		JToolBarHelper::cancel('template.cancel');
	}

}
