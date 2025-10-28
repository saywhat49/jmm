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
class JMMViewInsert extends HtmlView {
	
	protected $item;
	protected $form;
	protected $databases;
	protected $Tables;
	
	function display($tpl=null) 
	{
		$dbname=JRequest::getVar('dbname',null);
		if(!isset($dbname)){
			$defaultdbname=Joomla\CMS\Factory::getApplication() -> getCfg('db');
			$redirectUrl='index.php?option=com_jmm&view=insert';
			$redirectUrl.='&dbname='.$defaultdbname;
			$message.='Default Joomla Database  '.$defaultdbname.' Selected';
			Joomla\CMS\Factory::getApplication()->redirect($redirectUrl,$message);
		}
		
		$this->item=$this->get('Item');
		$this->form=$this->get('Form');

		$this->addToolbar();
		$document=Joomla\CMS\Factory::getDocument();
		$document->addStyleSheet('http://code.jquery.com/ui/1.10.0/themes/base/jquery-ui.css');
		$document->addStyleSheet(JURI::root().'media/com_jmm/css/jquery-ui-timepicker-addon.css');
		$document->addStyleSheet(JURI::root().'media/com_jmm/css/createtable.css');
		$document->addScript(JURI::root().'media/com_jmm/js/jquery-1.7.2.min.js');
		$document->addScript('http://code.jquery.com/ui/1.10.0/jquery-ui.js');
		$document->addScript(JURI::root().'media/com_jmm/js/jquery-ui-timepicker-addon.js');
		$document->addScript(JURI::root().'media/com_jmm/js/insert.js');

			

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
			Joomla\CMS\Factory::getApplication()->redirect($redirectUrl,$message);
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
