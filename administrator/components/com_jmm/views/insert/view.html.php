<?php
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.view');
class JMMViewInsert extends JView {
	
	protected $item;
	protected $form;
	protected $databases;
	protected $Tables;
	
	function display($tpl=null) 
	{
		$dbname=JRequest::getVar('dbname',null);
		if(!isset($dbname)){
			$defaultdbname=JFactory::getApplication() -> getCfg('db');
			$redirectUrl='index.php?option=com_jmm&view=insert';
			$redirectUrl.='&dbname='.$defaultdbname;
			$message.='Default Joomla Database  '.$defaultdbname.' Selected';
			JFactory::getApplication()->redirect($redirectUrl,$message);
		}
		
		$this->item=$this->get('Item');
		$this->form=$this->get('Form');

		$this->addToolbar();
		$document=JFactory::getDocument();
		$document->addStyleSheet('http://code.jquery.com/ui/1.10.0/themes/base/jquery-ui.css');
		$document->addStyleSheet(JURI::root().'media'.DS.'com_jmm'.DS.'css'.DS.'jquery-ui-timepicker-addon.css');
		$document->addStyleSheet(JURI::root().'media'.DS.'com_jmm'.DS.'css'.DS.'createtable.css');
		$document->addScript(JURI::root().'media'.DS.'com_jmm'.DS.'js'.DS.'jquery-1.7.2.min.js');
		$document->addScript('http://code.jquery.com/ui/1.10.0/jquery-ui.js');
		$document->addScript(JURI::root().'media'.DS.'com_jmm'.DS.'js'.DS.'jquery-ui-timepicker-addon.js');
		$document->addScript(JURI::root().'media'.DS.'com_jmm'.DS.'js'.DS.'insert.js');

			

		$filter_chnagetable=JRequest::getVar('filter_chnagetable');
		$filter_chnagedatabase=JRequest::getVar('filter_chnagedatabase','');
		$redirectUrl='index.php?option=com_jmm&view=insert';
		$message='';
		if($filter_chnagetable!=''){	
			$redirectUrl.='&tbl='.$filter_chnagetable;
			$message.='Table Switched to  '.$filter_chnagetable;		
		}

		$filter_chnagedatabase=JRequest::getVar('filter_chnagedatabase','');
		if($filter_chnagedatabase!=''){
			$redirectUrl.='&dbname='.$filter_chnagedatabase;
			$message.='Database Switched to  '.$filter_chnagedatabase;
		}
		if($filter_chnagetable!='' || $filter_chnagedatabase!=''){
			JFactory::getApplication()->redirect($redirectUrl,$message);
		}

		$this->databases=$this->get('Databases');
		$this->Tables=$this->get('Tables');	

		parent::display($tpl);
	}
	
	public function addToolbar()
	{
		if($this->item->id){
			JToolBarHelper::title('Insert Into Table', 'insert.png');
		}else{
			JToolBarHelper::title('Save to Table', 'insert.png');
		}
		
		
		JToolBarHelper::apply('insert.apply','JToolBar_APPLY');
		JToolBarHelper::save('insert.save','JToolBar_SAVE');
		JToolBarHelper::save2new('insert.save2new','JToolBar_SAVE_AND_NEW');
		JToolBarHelper::cancel('insert.cancel');
	}

}
