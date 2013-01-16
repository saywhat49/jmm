<?php
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.modellist');
class JMMModelTables extends JModelList {

	public function __construct($config = array()) {
		if (empty($config['filter_fields'])) {
			if (isset($_REQUEST['tbl'])) {
				$tbl = JRequest::getVar('tbl');
				$config['filter_fields'] = JMMCommon::getCloumnsFromTable($tbl);
			}
		}
		parent::__construct($config);
	}

	function getItems() {
		$items = parent::getItems();
		foreach ($items as &$item) {
			$item = (array)$item;
		}
		return $items;
	}

	public function getDbo() {
		$db = JMMCommon::getDBInstance();
		return $db;
	}

	public function getListQuery() {
		$tbl = JRequest::getString('tbl', '#__users');
		$query = parent::getListQuery();
		$query -> select('*');
		$query -> from($tbl);
		$search = $this -> getState('filter.search');
		//$db = $this -> getDbo();
		$this->_db=JMMCommon::getDBInstance();
		/*
		 if (!empty($search)) {
		 $search = '%' . $db -> getEscaped($search, true) . '%';
		 $fileds = JMMCommon::getCloumnsFromTable($tbl);
		 $searchflString = implode(" LIKE '$search' OR ", $fileds);
		 $field_searches = "($searchflString)";
		 $query -> where($field_searches);
		 }
		 */

		$filter_order=JRequest::getString('filter_order',null);
		$filter_order_Dir=JRequest::getString('filter_order_Dir',null);

		if(!empty($filter_order) && !empty($filter_order_Dir)){
			$query -> order($filter_order . ' ' . $filter_order_Dir);
		}
		return $query;
	}

	function getTables() {
		$rows = JMMCommon::getTablesFromDB();
		$tables = array();
		for ($i = 0; $i < count($rows); $i++) {
			$tables[] = JHTML::_('select.option', $rows[$i], $rows[$i]);
		}
		return $tables;
	}
	function getDatabases() {
		$rows = JMMCommon::getDataBaseLists();
		$databases = array();
		for ($i = 0; $i < count($rows); $i++) {
			$databases[] = JHTML::_('select.option', $rows[$i], $rows[$i]);
		}
		return $databases;
	}

	protected function populateState($ordering = null, $direction = null) {
		$search = $this -> getUserStateFromRequest($this -> context . '.filter_search', 'filter_search');
		$this -> setState('filter.search', $search);
		$published = $this -> getUserStateFromRequest($this -> context . '.filter.published', 'filter_published');
		$this -> setState('filter.published', $published);
		parent::populateState($ordering, $direction);

	}

}
