<?php
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.jmodel');
class JMMModelSQL extends JModel {

	function getItems() {
		$db = JMMCommon::getDBInstance();
		$query = JRequest::getVar('query', null);
		if (isset($query)) {
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
		$response=array();
		$row = JTable::getInstance('CannedQuery', 'JMMTable');
		if (!$row -> bind($data)) {
			$response['status']=false;
			$response['msg']=$this -> _db -> getErrorMsg();
			return $response;
		}
		if (!$row -> check()) {
			$response['status']=false;
			$response['msg']=$this -> _db -> getErrorMsg();
			return $response;
		}
		if (!$row -> store()) {
			$response['status']=false;
			$response['msg']=$this -> _db -> getErrorMsg();
			return $response;
		}
		$response['status']=true;
		$response['msg']='Canned Query Added Sucessfully';
		
		return $response;
	}
	
	function saveSiteTable($data){
		$response=array();
		$row = JTable::getInstance('SiteTable', 'JMMTable');
		if (!$row -> bind($data)) {
			$response['status']=false;
			$response['msg']=$this -> _db -> getErrorMsg();
			return $response;
		}
		if (!$row -> check()) {
			$response['status']=false;
			$response['msg']=$this -> _db -> getErrorMsg();
			return $response;
		}
		if (!$row -> store()) {
			$response['status']=false;
			$response['msg']=$this -> _db -> getErrorMsg();
			return $response;
		}
		$response['status']=true;
		$response['msg']='Site Table Added Sucessfully';
		
		return $response;
	}

}
