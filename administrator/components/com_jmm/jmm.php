<?php
error_reporting(E_ALL);
ini_set('display_errors',1);
defined('_JEXEC') or die('Restricted access');
function printobj($obj){
	echo '<pre>';
	print_r($obj);
	echo '</pre>';
}
JLoader::register('JMMHelper', dirname(__FILE__) . DS . 'helpers' . DS . 'jmm.php');
JLoader::register('JMMCommon', dirname(__FILE__) . DS . 'models' . DS . 'jmmcommon.php');
$document=&JFactory::getDocument();
//$document->addStyleSheet(JURI::root().'media'.DS.'com_jmm'.DS.'css'.DS.'bootstrap.min.css');
$document->addStyleSheet(JURI::root().'media'.DS.'com_jmm'.DS.'css'.DS.'jmm.css');
jimport('joomla.application.component.controller');
$controller = JController::getInstance('JMM');
$controller->execute(JRequest::getCmd('task'));
$controller->redirect();
