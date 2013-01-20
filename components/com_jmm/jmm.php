<?php
defined('_JEXEC') or die('Restricted access');
set_error_handler("JMMErrorHandler");
function JMMErrorHandler(){
	/**
	 * Do not Display Any Errors Warning
	 */
}
function printobj($obj){
	echo '<pre>';
	print_r($obj);
	echo '</pre>';
}
function dd($obj){
	echo '<pre>';
	print_r($obj);
	echo '</pre>';
}
defined('_JEXEC') or die('Restricted access');
JLoader::register('JMMCommon',JPATH_ADMINISTRATOR .DS.'components'. DS .'com_jmm'.DS. 'models' . DS . 'jmmcommon.php');
jimport('joomla.application.component.controller');
$controller = JController::getInstance('JMM');
$controller->execute(JRequest::getCmd('task'));
$controller->redirect();
