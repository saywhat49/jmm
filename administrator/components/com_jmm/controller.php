<?php
/**
 * @package		JMM
 * @link		http://adidac.github.com/jmm/index.html
 * @license		GNU/GPL
 * @copyright	Biswarup Adhikari
*/
error_reporting(E_ALL);
ini_set('display_errors',1);
defined('_JEXEC') or die('Restricted access');
class JMMController extends JControllerLegacy
{

	function display($cachable = false, $urlparams = false) 
	{
		
		parent::display($cachable);
		$viewName=JRequest::getCmd('view');
		JMMHelper::addSubmenu($viewName);		
	}

	function saveCannedQuery(){
		$mainframe=JFactory::getApplication();
		$title=JRequest::getString('title');
		$dbname=JRequest::getVar('dbname');
		$query=JRequest::getVar('query');
		$model=&$this->getModel('SQL');
		$response=$model->saveCannedQuery(JRequest::get( 'post' ));
		echo json_encode($response);
		$mainframe->close();
	}
	function saveSiteTable(){		
		$mainframe=JFactory::getApplication();
		$title=JRequest::getString('title');
		$dbname=JRequest::getVar('dbname');
		$query=JRequest::getVar('query');
		$model=&$this->getModel('SQL');
		$response=$model->saveSiteTable(JRequest::get( 'post' ));
		echo json_encode($response);
		$mainframe->close();
	}
}
