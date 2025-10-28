<?php
/**
 * @package		JMM
 * @link		http://adidac.github.com/jmm/index.html
 * @license		GNU/GPL
 * @copyright	Biswarup Adhikari
*/
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\MVC\Model\ListModel;
use Joomla\CMS\HTML\HTMLHelper;

/**
 * SiteTables Model for JMM component
 */
class JMMModelSiteTables extends ListModel {

	/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 */
	public function __construct($config = array()) {
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array('id', 'title', 'dbname', 'query', 'datetime', 'published');
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
		// Vous pouvez ajouter ici un traitement supplémentaire si nécessaire
		return $items;
	}

	/**
	 * Method to build an SQL query to load the list data.
	 *
	 * @return  \Joomla\Database\DatabaseQuery  The query object.
	 */
	public function getListQuery() {
		$query = parent::getListQuery();
		$query->select('*');
		$query->from('#__jmm_sitetables');
		
		$published = $this->getState('filter.published');
		
		if ($published == '') {
			$query->where('published IN (1, 0)');
		} else if ($published != '*') {
			$published = (int)$published;
			$query->where("published = " . $published);
		}
		
		$search = $this->getState('filter.search');
		$database = $this->getState('filter.database');
		
		if ($database != '') {
			$query->where("dbname = " . $this->getDbo()->quote($database));
		}
		
		$db = $this->getDbo();

		if (!empty($search)) {
			// Utilisation de escape() au lieu de getEscaped()
			$search = '%' . $db->escape($search, true) . '%';
			$field_searches = "(title LIKE " . $db->quote($search) . 
							 " OR dbname LIKE " . $db->quote($search) . 
							 " OR query LIKE " . $db->quote($search) . ")";
			$query->where($field_searches);
		}
		
		$orderCol = $this->getState('list.ordering');
		$orderDirn = $this->getState('list.direction');
		
		if (!empty($orderCol)) {
			// Utilisation de quote() pour les noms de colonnes et les directions
			$query->order($db->escape($orderCol) . ' ' . $db->escape($orderDirn));
		} else { 
			$query->order('id DESC');
		}
		
		return $query;
	}
	
	/**
	 * Method to get available databases.
	 *
	 * @return  array  The databases options.
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
		
		$database = $this->getUserStateFromRequest($this->context . '.filter.database', 'filter_database');
		$this->setState('filter.database', $database);
		
		parent::populateState($ordering, $direction);
	}
}
