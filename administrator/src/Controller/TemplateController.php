<?php
namespace Saywhat49\Component\Jmm\Administrator\Controller;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Controller\FormController;

class TemplateController extends FormController
{
    protected $view_list = 'templates';

    protected function allowAdd($data = []): bool
    {
        return Factory::getApplication()->getIdentity()->authorise('core.create', 'com_jmm');
    }

    protected function allowEdit($data = [], $key = 'id'): bool
    {
        return Factory::getApplication()->getIdentity()->authorise('core.edit', 'com_jmm');
    }

    protected function allowSave($data = [], $key = 'id'): bool
    {
        $user = Factory::getApplication()->getIdentity();
        if (!empty($data[$key])) {
            return $user->authorise('core.edit', 'com_jmm');
        }
        return $user->authorise('core.create', 'com_jmm');
    }
}