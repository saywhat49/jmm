<?php
/**
 * @package     Joomla.Component
 * @subpackage  com_jmm
 */

defined('_JEXEC') or die;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Controller\BaseController;


require_once JPATH_COMPONENT . '/helpers/jmm.php';

class JmmController extends BaseController
{
    public function display($cachable = false, $urlparams = [])
    {
        parent::display($cachable, $urlparams);

        $input = Joomla\CMS\Factory::getApplication()->input;
        $viewName = $input->getCmd('view', '');
        JMMHelper::addSubmenu($viewName);
    }

    public function saveCannedQuery()
    {
        $app = Joomla\CMS\Factory::getApplication();
        $input = $app->input;

        $model = $this->getModel('SQL');
        $response = $model->saveCannedQuery($input->post->getArray());

        echo json_encode($response);
        $app->close();
    }

    public function saveSiteTable()
    {
        $app = Joomla\CMS\Factory::getApplication();
        $input = $app->input;

        $model = $this->getModel('SQL');
        $response = $model->saveSiteTable($input->post->getArray());

        echo json_encode($response);
        $app->close();
    }
}