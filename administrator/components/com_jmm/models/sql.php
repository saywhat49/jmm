<?php
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.jmodel');
class JMMModelSQL extends JModel {

	function getItems() {
		$db=JMMCommon::getDBInstance();
		$query = JRequest::getVar('query', null);
		if (isset($query)) {
			$db -> setQuery($query);
			if (!$db -> query()) {
				JFactory::getApplication() ->enqueueMessage($db -> getErrorMsg(), 'error');
				return false;
			} else {
				$rows = $db -> loadAssocList();
				$total=count($rows);
				JFactory::getApplication() ->enqueueMessage('SQL Statement "'.$query.'" Executed Sucessfully,'.$total.' rows found');
				return $rows;
			}
		} else {
			return false;
		}
	}

}
