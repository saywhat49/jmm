<?php
defined('_JEXEC') or die ;
abstract class JMMHelper {

	public static function addSubmenu($viewName) {
		if(isset($_REQUEST['dbname'])){
			$dbname=JRequest::getVar('dbname');
			$urlString='&dbname='.$dbname;
		}else{
			$urlString='';
		}
		JSubMenuHelper::addEntry('Databases', 'index.php?option=com_jmm&amp;view=databases'.$urlString, $viewName == 'databases' OR $viewName == 'database');
		JSubMenuHelper::addEntry('Tables', 'index.php?option=com_jmm&amp;view=tables'.$urlString, $viewName == 'tables' OR $viewName == 'table');
		JSubMenuHelper::addEntry('Run SQL Query', 'index.php?option=com_jmm&amp;view=sql'.$urlString, $viewName == 'sql' OR $viewName == 'sql');
		JSubMenuHelper::addEntry('Canned Query', 'index.php?option=com_jmm&amp;view=cannedqueries'.$urlString, $viewName == 'cannedqueries' OR $viewName == 'cannedquery');            
	}

}
