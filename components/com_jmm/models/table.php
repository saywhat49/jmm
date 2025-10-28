<?php
/**
 * @package   JMM
 * @link    http://adidac.github.com/jmm/index.html
 * @license   GNU/GPL
 * @copyright Biswarup Adhikari
*/
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\Factory;
use Joomla\CMS\Pagination\Pagination;

/**
 * Table Model for JMM component
 */
class JMMModelTable extends BaseDatabaseModel
{
	/**
	 * Total number of items
	 *
	 * @var    integer
	 */
	var $_total = null;
	
	/**
	 * Pagination object
	 *
	 * @var    object
	 */
	var $_pagination = null;
	
	/**
	 * Constructor
	 */
	function __construct() {
		parent::__construct();
		
		$app = Factory::getApplication();
		$input = $app->input;
		
		$limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->get('list_limit'), 'int');
		$limitstart = $input->getInt('limitstart', 0);
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);
		
		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
	}
	
	/**
	 * Get site table details
	 *
	 * @param   integer  $siteTableId  The site table ID
	 *
	 * @return  object|null  The table details or null if not found
	 */
	public function getSiteTableDetails($siteTableId = null) {
		$app = Factory::getApplication();
		$input = $app->input;
		
		if (isset($siteTableId)) {
			$siteTableId = intval($siteTableId);
		} else {
			$params = $app->getParams();
			$siteTableId = (int) $params->get('site_table_id');
			
			if (!$siteTableId) {
				$siteTableId = $input->getInt('site_table_id');
			}
		}
		
		$db = Factory::getDbo();
		$query = $db->getQuery(true)
			->select('*')
			->from($db->quoteName('#__jmm_sitetables'))
			->where($db->quoteName('id') . ' = ' . $db->quote($siteTableId))
			->where($db->quoteName('published') . ' = 1');
		
		$db->setQuery($query);
		$row = $db->loadObject();
		
		return $row;
	}

	/**
	 * Get total number of items
	 *
	 * @return  integer  Total number of items
	 */
	function getTotal() {
		if (empty($this->_total)) {
			$app = Factory::getApplication();
			$input = $app->input;
			
			$params = $app->getParams();
			$siteTableId = (int) $params->get('site_table_id');
			
			if (!$siteTableId) {
				$siteTableId = $input->getInt('site_table_id');
			}
			
			$siteTableDetails = $this->getSiteTableDetails($siteTableId);
			$dbname = $siteTableDetails->dbname;
			$query = $siteTableDetails->query;
			
			$db = JMMCommon::getDBInstance(null, null, null, null, $dbname, null);
			$db->setQuery($query);
			
			$items = $db->loadObjectList();
			$this->_total = count($items);
		}
		
		return $this->_total;
	}

	/**
	 * Get pagination object
	 *
	 * @return  Pagination  The pagination object
	 */
	function getPagination() {
		// Load the content if it doesn't already exist
		if (empty($this->_pagination)) {
			$this->_pagination = new Pagination(
				$this->getTotal(),
				$this->getState('limitstart'),
				$this->getState('limit')
			);
		}
		
		return $this->_pagination;
	}
	
	/**
	 * Get table items
	 *
	 * @return  array  The table items
	 */
	function getItems() {
		$app = Factory::getApplication();
		$input = $app->input;
		
		$params = $app->getParams();
		$siteTableId = (int) $params->get('site_table_id');
		
		if (!$siteTableId) {
			$siteTableId = $input->getInt('site_table_id');
		}
		
		$table_pagination = (int) $params->get('table_pagination');
		
		if ($table_pagination === 0) {
			$table_pagination = $input->getInt('table_pagination', 0);
		}
		
		$no_record_per_page = (int) $params->get('no_record_per_page');
		
		if (!$no_record_per_page) {
			$no_record_per_page = $input->getInt('no_record_per_page', 10);
		}
		
		$this->setState('limit', $no_record_per_page);
		
		$siteTableDetails = $this->getSiteTableDetails($siteTableId);
		$dbname = $siteTableDetails->dbname;
		$query = $siteTableDetails->query;
		
		$db = JMMCommon::getDBInstance(null, null, null, null, $dbname, null);

		// Add pagination if enabled
		if ($table_pagination) {
			$query .= ' LIMIT ' . $this->getState('limitstart') . ',' . $this->getState('limit');
		}

		$db->setQuery($query);
		$items = $db->loadAssocList();
		
		return $items;
	}

	/**
	 * Get table columns
	 *
	 * @return  array  The table columns
	 */
	function getColumns() {
		$rows = $this->getItems();
		
		if (!empty($rows)) {
			$cols = $rows[0];
			return array_keys($cols);
		}
		
		return array();
	}
}
