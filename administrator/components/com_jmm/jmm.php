<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_jmm
 *
 * Corrected administrator entry point for Joomla 4/5 legacy MVC mode.
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Log\Log;

JLoader::register('JMMHelper', JPATH_COMPONENT_ADMINISTRATOR . '/helpers/jmm.php');
JLoader::register('JMMCommon', JPATH_COMPONENT_ADMINISTRATOR . '/models/jmmcommon.php');

try {
    $app        = Factory::getApplication();
    $input      = $app->input;
    $controller = BaseController::getInstance('JMM');

    $controller->execute($input->getCmd('task'));
    $controller->redirect();
} catch (Throwable $e) {
    Log::add($e->getMessage(), Log::ERROR, 'com_jmm');
    Factory::getApplication()->enqueueMessage('Une erreur est survenue dans le composant JMM.', 'error');
}
