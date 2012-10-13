<?php
error_reporting(E_ALL);
ini_set('display_errors',1);
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.controller');
class JMMController extends JController
{

	function display($cachable = false) 
	{
		
		parent::display($cachable);
		$viewName=JRequest::getCmd('view');
		JMMHelper::addSubmenu($viewName);		
	}

	
}
