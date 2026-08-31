<?php
namespace Saywhat49\Component\Jmm\Administrator\Controller;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Controller\FormController;

class TemplateController extends FormController
{
    protected $view_list = 'templates';

    protected function allowSave($data = [], $key = 'id'): bool
    {
        return Factory::getApplication()->getIdentity()->authorise('core.admin', 'com_jmm');
    }
}