<?php
namespace Saywhat49\Component\Jmm\Administrator\Controller;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;

class InsertController extends FormController
{
    protected $view_list = 'insert';

    public function save($key = null, $urlVar = null)
    {
        $app = Factory::getApplication();
        if (!Session::checkToken('request')) {
            $this->setRedirect(Route::_('index.php?option=com_jmm&view=insert', false), Text::_('JINVALID_TOKEN'), 'error');
            return false;
        }

        $user = $app->getIdentity();
        if (!$user->authorise('core.create', 'com_jmm')) {
            $this->setRedirect(Route::_('index.php?option=com_jmm&view=insert', false), Text::_('JERROR_ALERTNOAUTHOR'), 'error');
            return false;
        }

        $input = $app->getInput();
        $tbl = $input->getString('tbl', '');
        $dbname = $input->getString('dbname', '');
        $jformData = $input->post->get('jform', [], 'array');

        /** @var \Saywhat49\Component\Jmm\Administrator\Model\InsertModel $model */
        $model = $this->getModel('Insert');
        $result = $model->insertRow($tbl, $jformData, $dbname);

        $redirectUrl = 'index.php?option=com_jmm&view=insert&tbl=' . urlencode($tbl) . '&dbname=' . urlencode($dbname);

        $this->setRedirect(
            Route::_($redirectUrl, false),
            $result['msg'],
            $result['status'] ? 'message' : 'error'
        );

        return $result['status'];
    }
}