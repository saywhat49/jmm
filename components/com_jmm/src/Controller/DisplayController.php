<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_jmm
 */

namespace Saywhat49\Component\Jmm\Administrator\Controller;

\defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\BaseController;

class DisplayController extends BaseController
{
    protected $default_view = 'lists';

    public function display($cachable = false, $urlparams = []): static
    {
        return parent::display($cachable, $urlparams);
    }
}
