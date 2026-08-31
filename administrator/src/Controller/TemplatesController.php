<?php
namespace Saywhat49\Component\Jmm\Administrator\Controller;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\AdminController;

class TemplatesController extends AdminController
{
    protected $text_prefix = 'COM_JMM_TEMPLATES';

    public function getModel($name = 'Template', $prefix = 'Administrator', $config = ['ignore_request' => true])
    {
        return parent::getModel($name, $prefix, $config);
    }
}