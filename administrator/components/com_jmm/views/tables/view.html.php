<?php
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.view');
class JMMViewTables extends JView {

	protected $items;
	protected $state;
	protected $pagination;
	protected $tables;
	function display($tmpl = null) {
		$action = JRequest::getString('action', '');
		$tbl = JRequest::getString('tbl');
		switch ($action) {
			case 'structure' :
				$this -> items = JMMCommon::showTableStructure($tbl);
				break;
			case 'browse' :
				//$this -> items = JMMCommon::showDataFromTable($tbl);
				$this -> items =  $this -> get('Items');
				break;

			default :
				$this -> items = JMMCommon::showTableLists();
				break;
		}
		$this->tables=$this->get('Tables');
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
