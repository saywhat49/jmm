<?php
/**
 * @package		JMM
 * @link		http://adidac.github.com/jmm/index.html
 * @license		GNU/GPL
 * @copyright	Biswarup Adhikari
*/
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.modellist');
class JMMModelTemplates extends JModelList {

	public function __construct($config = array()) {
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array('id', 'title','datetime', 'published');
		}
		parent::__construct($config);
	}

	function getItems() {
		$items = parent::getItems();
		foreach ($items as &$item) {

		}
		return $items;
	}

	public function getListQuery() {
		$query = parent::getListQuery();
		$query -> select('*');
		$query -> from('#__jmm_templates');
		$published = $this -> getState('filter.published');
		if ($published == '') {
			$query -> where('published IN(1,0)');
		} else if ($published != '*') {
			$published = (int)$published;
			$query -> where("published='$published'");
		}		
		$search = $this -> getState('filter.search');
		$database = $this -> getState('filter.database');
		if ($database!='') {
			$query -> where("dbname='$database'");
		}
		$db = $this -> getDbo();
		if (!empty($search)) {
			$search = '%' . $db -> getEscaped($search, true) . '%';
			$field_searches = "(title LIKE '{$search}'";
			$query -> where($field_searches);
		}
		$orderCol = $this -> getState('list.ordering');
		$orderDirn = $this -> getState('list.direction');
		if (isset($orderCol)) {
			$query -> order($db -> getEscaped($orderCol . ' ' . $orderDirn));
		} else { 
			$query -> order('id desc');
		}
		return $query;
	}

	protected function populateState($ordering = null, $direction = null) {
		$search = $this -> getUserStateFromRequest($this -> context . '.filter_search', 'filter_search');
		$this -> setState('filter.search', $search);
		$published = $this -> getUserStateFromRequest($this -> context . '.filter.published', 'filter_published');
		$this -> setState('filter.published', $published);
		parent::populateState($ordering, $direction);

	}

}
