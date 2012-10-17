<?php
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.view');
class JMMViewTables extends JView {

	protected $items;
	protected $state;
	protected $pagination;
	protected $tables;
	protected $databases;
	function display($tmpl = null) {
		$action = JRequest::getString('action', '');
		$tbl = JRequest::getString('tbl');
		$filter_browsetable=JRequest::getVar('filter_browsetable','');
		if(isset($_REQUEST['dbname'])){
			$dbname=JRequest::getVar('dbname');
			$urlString='&dbname='.$dbname;
		}else{
			$urlString='';
		}
		if($filter_browsetable!=''){
			JFactory::getApplication()->redirect('index.php?option=com_jmm&view=tables&action=browse&tbl='.$filter_browsetable.$urlString,'Table Data of '.$filter_browsetable);
		}
		$filter_tablestructure=JRequest::getVar('filter_tablestructure');
		if($filter_tablestructure!=''){			
			JFactory::getApplication()->redirect('index.php?option=com_jmm&view=tables&action=structure&tbl='.$filter_tablestructure.$urlString,'Table Structure of '.$filter_tablestructure);
		}
		$filter_chnagedatabase=JRequest::getVar('filter_chnagedatabase','');
		if($filter_chnagedatabase!=''){
			JFactory::getApplication()->redirect('index.php?option=com_jmm&view=tables&dbname='.$filter_chnagedatabase,'Database Switched to  '.$filter_chnagedatabase);
		}
		
		
		switch ($action) {
			case 'structure' :
				$this -> items = JMMCommon::showTableStructure($tbl);
				break;
			case 'browse' :
				$this -> items =  $this -> get('Items');
				break;

			default :
				$this -> items = JMMCommon::showTableLists();
				break;
		}
		$this->tables=$this->get('Tables');
		$this->databases=$this->get('Databases');
		$this -> pagination = $this -> get('Pagination');
		$this -> state = $this -> get('State');
		$this -> addToolbar();
		parent::display($tmpl);
	}

	public function addToolbar() {
		JToolBarHelper::title('Tables', 'tables.png');
		JToolBarHelper::preferences('com_jmm');
	}

}
