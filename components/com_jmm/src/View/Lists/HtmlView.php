<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_jmm
 */

namespace Saywhat49\Component\Jmm\Administrator\View\Lists;

\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;

class HtmlView extends BaseHtmlView
{
    public string $title = 'Joomla MySQL Manager';

    public function display($tpl = null): void
    {
        $this->addToolbar();

        parent::display($tpl);
    }

    protected function addToolbar(): void
    {
        Factory::getApplication()->getInput()->set('hidemainmenu', false);

        ToolbarHelper::title('Joomla MySQL Manager', 'database');
    }
}
