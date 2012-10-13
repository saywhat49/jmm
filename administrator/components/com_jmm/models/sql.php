<?php
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.jmodel');
class JMMModelSQL extends JModel {

	function getItems() {
		$db=JFactory::getDBO();
		$query=JRequest::getVar('query',null);
		if(isset($query)){
			$db->setQuery($query);
			$rows=$db->loadAssocList();
			return $rows;
		}else{
			return false;
		}
	}

}
