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
 * Templates Model for JMM component
 */
class JMMModelTemplates extends ListModel {

	/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 */
	public function __construct($config = array()) {
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array('id', 'title', 'datetime', 'published');
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
		// Des traitements supplémentaires peuvent être ajoutés ici si nécessaire
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
		$query->from('#__jmm_templates');
		
		$published = $this->getState('filter.published');
		if ($published == '') {
			$query->where('published IN (1, 0)');
		} else if ($published != '*') {
			$published = (int)$published;
			$query->where('published = ' . $published);
		}		
		
		$search = $this->getState('filter.search');
		$database = $this->getState('filter.database');
		
		if ($database != '') {
			$query->where('dbname = ' . $this->getDbo()->quote($database));
		}
		
		$db = $this->getDbo();
		
		if (!empty($search)) {
			$search = '%' . $db->escape($search, true) . '%';
			$field_searches = '(title LIKE ' . $db->quote($search) . ')';
			$query->where($field_searches);
		}
		
		$orderCol = $this->getState('list.ordering');
		$orderDirn = $this->getState('list.direction');
		
		if (!empty($orderCol)) {
			$query->order($db->escape($orderCol) . ' ' . $db->escape($orderDirn));
		} else { 
			$query->order('id DESC');
		}
		
		return $query;
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
