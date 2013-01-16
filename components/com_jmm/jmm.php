<?php
error_reporting(E_ALL);
ini_set('display_errors',1);
defined('_JEXEC') or die('Restricted access');
JLoader::register('JMMCommon',JPATH_ADMINISTRATOR .DS.'components'. DS .'com_jmm'.DS. 'models' . DS . 'jmmcommon.php');
jimport('joomla.application.component.controller');
$controller = JController::getInstance('JMM');
$controller->execute(JRequest::getCmd('task'));
$controller->redirect();