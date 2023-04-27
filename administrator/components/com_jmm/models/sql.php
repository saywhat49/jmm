<?php
/**
 * @package		JMM
 * @link		http://adidac.github.com/jmm/index.html
 * @license		GNU/GPL
 * @copyright	Biswarup Adhikari
*/
defined('_JEXEC') or die('Restricted access');
class JMMModelSQL extends JModelItem {

	function getItems() {
		$db = JMMCommon->getDBInstance();
		$query = JRequest::getVar('query', '');
		if (isset($query) && $query!='') {
			$db -> setQuery($query);
			if (!$db -> query()) {
				JFactory::getApplication() -> enqueueMessage($db -> getErrorMsg(), 'error');
				return false;
			} else {
				$rows = $db -> loadAssocList();
				$total = count($rows);
				JFactory::getApplication() -> enqueueMessage('SQL Statement "' . $query . '" Executed Sucessfully,' . $total . ' rows found');
				return $rows;
			}
		} else {
			return false;
		}
	}

	function saveCannedQuery($data) {
		$response = array();
		$row = JTable::getInstance('CannedQuery', 'JMMTable');
		if (!$row -> bind($data)) {
			$response['status'] = false;
			$response['msg'] = $this -> _db -> getErrorMsg();
			return $response;
		}
		if (!$row -> check()) {
			$response['status'] = false;
			$response['msg'] = $this -> _db -> getErrorMsg();
			return $response;
		}
		if (!$row -> store()) {
			$response['status'] = false;
			$response['msg'] = $this -> _db -> getErrorMsg();
			return $response;
		}
		$response['status'] = true;
		$response['msg'] = 'Canned Query Added Sucessfully';
		$response['row']=array('title'=>JRequest::getVar('title'),'query'=>JRequest::getVar('query'));

		return $response;
	}

	function saveSiteTable($data) {
		$response = array();
		$row = JTable::getInstance('SiteTable', 'JMMTable');
		if (!$row -> bind($data)) {
			$response['status'] = false;
			$response['msg'] = $this -> _db -> getErrorMsg();
			return $response;
		}
		if (!$row -> check()) {
			$response['status'] = false;
			$response['msg'] = $this -> _db -> getErrorMsg();
			return $response;
		}
		if (!$row -> store()) {
			$response['status'] = false;
			$response['msg'] = $this -> _db -> getErrorMsg();
			return $response;
		}
		$response['status'] = true;
		$response['msg'] = 'Site Table Added Sucessfully';  
		$response['row']=array('title'=>JRequest::getVar('title'),'query'=>JRequest::getVar('query'));
		return $response;
	}

	function getDatabases() {
		$rows = JMMCommon::getDataBaseLists();
		$databases = array();
		for ($i = 0; $i < count($rows); $i++) {
			$databases[] = JHTML::_('select.option', $rows[$i], $rows[$i]);
		}
		return $databases;
	}
	
	function getCannedQueries(){
		$rows = JMMCommon::listCannedQueries();
		$queries = array();
		for ($i = 0; $i < count($rows); $i++) {
			$queries[] = JHTML::_('select.option', $rows[$i]->query, $rows[$i]->title);
		}
		return $queries;
		
	}
	function getSiteTables(){
		$rows = JMMCommon::listSiteTables();
		$queries = array();
		for ($i = 0; $i < count($rows); $i++) {
			$queries[] = JHTML::_('select.option', $rows[$i]->query, $rows[$i]->title);
		}
		return $queries;
		
	}
 


}
