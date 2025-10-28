<?php

/**

 * @package		JMM

 * @link		http://adidac.github.com/jmm/index.html

 * @license		GNU/GPL

 * @copyright	Biswarup Adhikari

*/

defined('_JEXEC') or die("Access Not Allowed") ;

class JMMHelper 

{

	public static function addSubmenu($viewName) {

		if(isset($_REQUEST['dbname'])){

			$dbname=JRequest::getVar('dbname');

			$urlString='&dbname='.$dbname;

		}else{

			$urlString='';

		}

		JHtmlSidebar::addEntry('Databases', 'index.php?option=com_jmm&amp;view=databases'.$urlString, $viewName == 'databases' OR $viewName == 'database');

		JHtmlSidebar::addEntry('Tables', 'index.php?option=com_jmm&amp;view=tables'.$urlString, $viewName == 'tables' OR $viewName == 'table');

		JHtmlSidebar::addEntry('Run SQL Query', 'index.php?option=com_jmm&amp;view=sql'.$urlString, $viewName == 'sql' OR $viewName == 'sql');

		JHtmlSidebar::addEntry('Canned Query', 'index.php?option=com_jmm&amp;view=cannedqueries'.$urlString, $viewName == 'cannedqueries' OR $viewName == 'cannedquery'); 

		JHtmlSidebar::addEntry('Site Tables', 'index.php?option=com_jmm&amp;view=sitetables'.$urlString, $viewName == 'sitetables' OR $viewName == 'sitetable');   

		JHtmlSidebar::addEntry('Create Table', 'index.php?option=com_jmm&amp;view=createtable'.$urlString, $viewName == 'createtables' OR $viewName == 'createtable');       

		JHtmlSidebar::addEntry('Insert Data', 'index.php?option=com_jmm&amp;view=insert'.$urlString, $viewName == 'insert' OR $viewName == 'insert');                                   

		JHtmlSidebar::addEntry('Templates', 'index.php?option=com_jmm&amp;view=templates'.$urlString, $viewName == 'templates' OR $viewName == 'template');                                   

	}



}


