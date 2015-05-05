<?php
/**
 * @package		JMM
 * @link		http://adidac.github.com/jmm/index.html
 * @license		GNU/GPL
 * @copyright	Biswarup Adhikari
*/
defined('_JEXEC') or die('Restricted access');
class JMMModelSiteTables extends JModelList {

	public function __construct($config = array()) {
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array('id', 'title', 'dbname', 'query', 'datetime', 'published');
		}
		parent::__construct($config);
	}

	function getItems() {
		$items = parent::getItems();
		foreach ($items as &$item) {
			$editURL= JRoute::_('index.php?option=com_jmm&view=sitetable&task=sitetable.edit&id='.(int) $item->id); 
			$item->title='<a href="'.$editURL.'">'.$item->title.'</a>';
		}
		return $items;
	}

	public function getListQuery() {
		$query = parent::getListQuery();
		$query -> select('st.*');
		$query -> from('#__jmm_sitetables as st');
		$published = $this -> getState('filter.published');
		if ($published == '') {
			$query -> where('st.published IN(1,0)');
		} else if ($published != '*') {
			$published = (int)$published;
			$query -> where("st.published='$published'");
		}		
		$search = $this -> getState('filter.search');
		$database = $this -> getState('filter.database');
		if ($database!='') {
			$query -> where("st.dbname='$database'");
		}
		$db = $this -> getDbo();

		if (!empty($search)) {
			$search = '%' . $db -> getEscaped($search, true) . '%';
			$field_searches = "(st.title LIKE '{$search}' OR st.dbname LIKE '{$search}' OR st.query LIKE '{$search}')";
			$query -> where($field_searches);
		}
		$query->select('vl.title as access_level');
		$query->join('LEFT','#__viewlevels as vl ON vl.id=st.access_level');
		$orderCol = $this -> getState('list.ordering');
		$orderDirn = $this -> getState('list.direction');
		if (isset($orderCol)) {
			$query -> order($orderCol . ' ' . $orderDirn);
		} else { 
			$query -> order('st.id desc');
		}		
		return $query;
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
		$database = $this -> getUserStateFromRequest($this -> context . '.filter.database', 'filter_database');
		$this -> setState('filter.database', $database);
		parent::populateState($ordering, $direction);

	}

}
