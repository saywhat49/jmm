<?php
/**
 * @package		JMM
 * @link		http://adidac.github.com/jmm/index.html
 * @license		GNU/GPL
 * @copyright	Biswarup Adhikari
*/
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
JLoader::register('JMMHelper', dirname(__FILE__) . DS . 'helpers' . DS . 'jmm.php');
JLoader::register('JMMCommon', dirname(__FILE__) . DS . 'models' . DS . 'jmmcommon.php');
$document=JFactory::getDocument();
//$document->addStyleSheet(JURI::root().'media'.DS.'com_jmm'.DS.'css'.DS.'bootstrap.min.css');
$document->addStyleSheet(JURI::root().'media'.DS.'com_jmm'.DS.'css'.DS.'jmm.css');
jimport('joomla.application.component.controller');
$controller = JController::getInstance('JMM');
$controller->execute(JRequest::getCmd('task'));
$controller->redirect();
