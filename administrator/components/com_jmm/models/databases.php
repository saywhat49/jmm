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

/**
 * Databases Model for JMM component
 */
class JMMModelDatabases extends ListModel {

	/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 */
	public function __construct($config = array()) {
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array('pp.id', 'pp.points', 'pp.issuer', 'pp.recipients', 'pp.datetime', 'pp.published', 'ui.username', 'ur.username');
		}
		parent::__construct($config);
	}

	/**
	 * Method to get an array of data items.
	 *
	 * @return  array  An array of data items.
	 */
	function getItems() {
		$db = Factory::getDbo();
		$db->setQuery($this->getListQuery());
		$items = $db->loadAssocList();
		
		foreach($items as &$item) {
			$item['link'] = '<a href="index.php?option=com_jmm&tbl=">';
		}
		
		return $items;
	}

	/**
	 * Method to build an SQL query to load the list data.
	 *
	 * @return  string  The query string
	 */
	public function getListQuery() {
		$query = "SHOW DATABASES";
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
