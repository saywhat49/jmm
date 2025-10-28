<?php
/**
 * @package		JMM
 * @link		http://adidac.github.com/jmm/index.html
 * @license		GNU/GPL
 * @copyright	Biswarup Adhikari
*/
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\Factory;
use Joomla\CMS\Table\Table;
use Joomla\CMS\HTML\HTMLHelper;

/**
 * SQL Model for JMM component
 */
class JMMModelSQL extends BaseDatabaseModel {

    /**
     * Method to get query results
     *
     * @return  array|boolean  Results or false if no query
     */
	function getItems() {
		$db = JMMCommon::getDBInstance();
		$app = Factory::getApplication();
		$input = $app->input;
		
		$query = $input->getString('query', '');
		
		if (!empty($query)) {
			$db->setQuery($query);
			
			if (!$db->execute()) {
				$app->enqueueMessage($db->getErrorMsg(), 'error');
				return false;
			} else {
				$rows = $db->loadAssocList();
				$total = count($rows);
				$app->enqueueMessage('SQL Statement "' . $query . '" Executed Sucessfully, ' . $total . ' rows found');
				return $rows;
			}
		} else {
			return false;
		}
	}

    /**
     * Save a canned query
     *
     * @param   array  $data  The data to save
     *
     * @return  array  Response with status and message
     */
	function saveCannedQuery($data) {
		$app = Factory::getApplication();
		$input = $app->input;
		$response = array();
		
		$row = Table::getInstance('CannedQuery', 'JMMTable');
		
		if (!$row->bind($data)) {
			$response['status'] = false;
			$response['msg'] = $this->_db->getErrorMsg();
			return $response;
		}
		
		if (!$row->check()) {
			$response['status'] = false;
			$response['msg'] = $this->_db->getErrorMsg();
			return $response;
		}
		
		if (!$row->store()) {
			$response['status'] = false;
			$response['msg'] = $this->_db->getErrorMsg();
			return $response;
		}
		
		$response['status'] = true;
		$response['msg'] = 'Canned Query Added Sucessfully';
		$response['row'] = array(
			'title' => $input->getString('title'),
			'query' => $input->getString('query')
		);

		return $response;
	}

    /**
     * Save a site table
     *
     * @param   array  $data  The data to save
     *
     * @return  array  Response with status and message
     */
	function saveSiteTable($data) {
		$app = Factory::getApplication();
		$input = $app->input;
		$response = array();
		
		$row = Table::getInstance('SiteTable', 'JMMTable');
		
		if (!$row->bind($data)) {
			$response['status'] = false;
			$response['msg'] = $this->_db->getErrorMsg();
			return $response;
		}
		
		if (!$row->check()) {
			$response['status'] = false;
			$response['msg'] = $this->_db->getErrorMsg();
			return $response;
		}
		
		if (!$row->store()) {
			$response['status'] = false;
			$response['msg'] = $this->_db->getErrorMsg();
			return $response;
		}
		
		$response['status'] = true;
		$response['msg'] = 'Site Table Added Sucessfully';  
		$response['row'] = array(
			'title' => $input->getString('title'),
			'query' => $input->getString('query')
		);
		
		return $response;
	}

    /**
     * Get the databases
     *
     * @return  array  List of databases
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
     * Get canned queries
     *
     * @return  array  List of canned queries
     */
	function getCannedQueries() {
		$rows = JMMCommon::listCannedQueries();
		$queries = array();
		
		for ($i = 0; $i < count($rows); $i++) {
			$queries[] = HTMLHelper::_('select.option', $rows[$i]->query, $rows[$i]->title);
		}
		
		return $queries;
	}
	
    /**
     * Get site tables
     *
     * @return  array  List of site tables
     */
	function getSiteTables() {
		$rows = JMMCommon::listSiteTables();
		$queries = array();
		
		for ($i = 0; $i < count($rows); $i++) {
			$queries[] = HTMLHelper::_('select.option', $rows[$i]->query, $rows[$i]->title);
		}
		
		return $queries;
	}
}
