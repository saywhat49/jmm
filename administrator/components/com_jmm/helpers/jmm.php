<?php
defined('_JEXEC') or die ;
abstract class JMMHelper {

	public static function addSubmenu($viewName) {
		JSubMenuHelper::addEntry('Databases', 'index.php?option=com_jmm&amp;view=databases', $viewName == 'databases' OR $viewName == 'database');
		JSubMenuHelper::addEntry('Tables', 'index.php?option=com_jmm&amp;view=tables', $viewName == 'tables' OR $viewName == 'table');
		JSubMenuHelper::addEntry('Run SQL Query', 'index.php?option=com_jmm&amp;view=sql', $viewName == 'sql' OR $viewName == 'sql');
		JSubMenuHelper::addEntry('Canned Query', 'index.php?option=com_jmm&amp;view=cannedqueries', $viewName == 'cannedqueries' OR $viewName == 'cannedquery');            
	}

}
