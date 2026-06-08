<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_jmm
 */

\defined('_JEXEC') or die;

use Joomla\CMS\Factory;

$app = Factory::getApplication();

$component = $app->bootComponent('com_jmm');
$component->getDispatcher($app)->dispatch();
