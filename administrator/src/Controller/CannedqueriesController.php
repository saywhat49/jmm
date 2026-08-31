<?php
namespace Saywhat49\Component\Jmm\Administrator\Controller;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\AdminController;

class CannedqueriesController extends AdminController
{
    protected $text_prefix = 'COM_JMM_CANNED_QUERIES';

    public function getModel($name = 'Cannedquery', $prefix = 'Administrator', $config = ['ignore_request' => true])
    {
        return parent::getModel($name, $prefix, $config);
    }
}