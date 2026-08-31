<?php
namespace Saywhat49\Component\Jmm\Administrator\Controller;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\AdminController;

class SitetablesController extends AdminController
{
    protected $text_prefix = 'COM_JMM_SITETABLES';

    public function getModel($name = 'Sitetable', $prefix = 'Administrator', $config = ['ignore_request' => true])
    {
        return parent::getModel($name, $prefix, $config);
    }
}