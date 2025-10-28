<?php
/**
 * @package     Joomla.Component
 * @subpackage  com_jmm
 *
 * @copyright   Copyright (C) Biswarup Adhikari
 * @license     GNU General Public License version 2 or later
 */

defined('_JEXEC') or die;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Controller\BaseController;



JLoader::register('JMMHelper', dirname(__FILE__) . '/helpers/jmm.php');
JLoader::register('JMMCommon', dirname(__FILE__) . '/models/jmmcommon.php');

// Get an instance of the controller prefixed by Jmm
$controller = \Joomla\CMS\MVC\Controller\BaseController::getInstance('Jmm');

// Perform the Request task
$input = \Joomla\CMS\Factory::getApplication()->input;
$controller->execute($input->getCmd('task'));

// Redirect if set by the controller
$controller->redirect();