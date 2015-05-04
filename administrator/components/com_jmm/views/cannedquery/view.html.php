<?php
/**
 * @package		JMM
 * @link		http://adidac.github.com/jmm/index.html
 * @license		GNU/GPL
 * @copyright	Biswarup Adhikari
*/
defined('_JEXEC') or die('Restricted access');
class JMMViewCannedQuery extends JViewLegacy {
	
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
			JToolBarHelper::title('Edit Canned Query');
		}else{
			JToolBarHelper::title('Save Canned Query');
		}
		
		
		JToolBarHelper::apply('cannedquery.apply','JToolBar_APPLY');
		JToolBarHelper::save('cannedquery.save','JToolBar_SAVE');
		JToolBarHelper::save2new('cannedquery.save2new','JToolBar_SAVE_AND_NEW');
		JToolBarHelper::cancel('cannedquery.cancel');
	}

}
