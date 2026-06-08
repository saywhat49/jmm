<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_jmm
 *
 * Corrected front entry point for Joomla 4/5 legacy MVC mode.
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Log\Log;

// Register classes used by the legacy component.
JLoader::register('JMMCommon', JPATH_ADMINISTRATOR . '/components/com_jmm/models/jmmcommon.php');
JLoader::register('JMM', JPATH_COMPONENT . '/helpers/jmm.php');

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
