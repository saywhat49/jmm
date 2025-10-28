<?php
/**
 * @package		JMM
 * @link		http://adidac.github.com/jmm/index.html
 * @license		GNU/GPL
 * @copyright	Biswarup Adhikari
*/
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Factory;

// Décommenter ces lignes uniquement pour le débogage
/*
error_reporting(E_ALL);
ini_set('display_errors', 1);
*/

/**
 * Function to handle errors
 *
 * @param   integer  $errno    Error number
 * @param   string   $errstr   Error message
 * @param   string   $errfile  File where error occurred
 * @param   integer  $errline  Line number where error occurred
 *
 * @return  void
 */
function JMMErrorHandler($errno, $errstr, $errfile, $errline) {
	/**
	 * Do not Display Any Errors Warning
	 */
	echo $errstr.' On File '.$errfile.' On Line Number'.$errline.' <hr>';
}

/**
 * Debug function to print object with formatting
 *
 * @param   mixed  $obj  Object to print
 *
 * @return  void
 */
function printobj($obj) {
	echo '<pre>';
	print_r($obj);
	echo '</pre>';
}

/**
 * Debug function to print object with formatting (alias)
 *
 * @param   mixed  $obj  Object to print
 *
 * @return  void
 */

// Register component files
$app = Factory::getApplication();
$input = $app->input;

// Register class files
JLoader::register('JMMCommon', JPATH_ADMINISTRATOR . '/components/com_jmm/models/jmmcommon.php');
JLoader::register('JMM', JPATH_COMPONENT . '/helpers/jmm.php');

// Get the controller
$controller = BaseController::getInstance('JMM');
$controller->execute($input->getCmd('task'));
$controller->redirect();
