<?php
/**
 * @package		JMM
 * @link		http://adidac.github.com/jmm/index.html
 * @license		GNU/GPL
 * @copyright	Biswarup Adhikari
*/
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\MVC\Model\ListModel;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;

/**
 * Tables Model for JMM component
 */
class JMMModelTables extends ListModel {

	/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 */
	public function __construct($config = array()) {
		$app = Factory::getApplication();
		$input = $app->input;
		
		if (empty($config['filter_fields'])) {
			if ($input->exists('tbl')) {
				$tbl = $input->getString('tbl');
				$config['filter_fields'] = JMMCommon::getCloumnsFromTable($tbl);
			}
		}
		parent::__construct($config);
	}

	/**
	 * Method to get an array of data items.
	 *
	 * @return  array  An array of data items.
	 */
	function getItems() {
		$items = parent::getItems();
		foreach ($items as &$item) {
			$item = (array)$item;
		}
		return $items;
	}

	/**
	 * Method to get a database object.
	 *
	 * @return  \Joomla\Database\DatabaseDriver  The database driver.
	 */
	public function getDbo() {
		$db = JMMCommon::getDBInstance();
		return $db;
	}

	/**
	 * Method to build an SQL query to load the list data.
	 *
	 * @return  \Joomla\Database\DatabaseQuery  The query object.
	 */
	public function getListQuery() {
		$app = Factory::getApplication();
		$input = $app->input;
		
		$tbl = $input->getString('tbl', '#__users');
		$query = parent::getListQuery();
		$query->select('*');
		$query->from($tbl);
		
		$search = $this->getState('filter.search');
		$this->_db = JMMCommon::getDBInstance();
		
		/*
		 if (!empty($search)) {
		 $search = '%' . $db->escape($search, true) . '%';
		 $fileds = JMMCommon::getCloumnsFromTable($tbl);
		 $searchflString = implode(" LIKE '$search' OR ", $fileds);
		 $field_searches = "($searchflString)";
		 $query->where($field_searches);
		 }
		 */

		$filter_order = $input->getString('filter_order', null);
		$filter_order_Dir = $input->getString('filter_order_Dir', null);

		if (!empty($filter_order) && !empty($filter_order_Dir)) {
			$query->order($filter_order . ' ' . $filter_order_Dir);
		}
		
		return $query;
	}

	/**
	 * Method to get available tables.
	 *
	 * @return  array  The tables.
	 */
	function getTables() {
		$rows = JMMCommon::getTablesFromDB();
		$tables = array();
		
		for ($i = 0; $i < count($rows); $i++) {
			$tables[] = HTMLHelper::_('select.option', $rows[$i], $rows[$i]);
		}
		
		return $tables;
	}
	
	/**
	 * Method to get available databases.
	 *
	 * @return  array  The databases.
	 */
	function getDatabases() {
		$rows = JMMCommon::getDataBaseLists();
		$databases = array();
		
		for ($i = 0; $i < count($rows); $i++) {
			$databases[] = HTMLHelper::_('select.option', $rows[$i], $rows[$i]);
		}
		
		return $databases;
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * @param   string  $ordering   An optional ordering field.
	 * @param   string  $direction  An optional direction (asc|desc).
	 *
	 * @return  void
	 */
	protected function populateState($ordering = null, $direction = null) {
		$search = $this->getUserStateFromRequest($this->context . '.filter_search', 'filter_search');
		$this->setState('filter.search', $search);
		
		$published = $this->getUserStateFromRequest($this->context . '.filter.published', 'filter_published');
		$this->setState('filter.published', $published);
		
		parent::populateState($ordering, $direction);
	}
}
