<?php
namespace Saywhat49\Component\Jmm\Administrator\Controller;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Response\JsonResponse;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;

class CreatetableController extends BaseController
{
    public function createTableStructure(): void
    {
        $app = Factory::getApplication();
        if (!Session::checkToken('request')) {
            if ($app->getInput()->getCmd('format') === 'json') {
                echo new JsonResponse(null, Text::_('JINVALID_TOKEN'), true);
                $app->close();
            }
            $this->setRedirect(Route::_('index.php?option=com_jmm&view=createtable', false), Text::_('JINVALID_TOKEN'), 'error');
            return;
        }

        $user = $app->getIdentity();
        if (!$user->authorise('core.create', 'com_jmm') && !$user->authorise('core.admin', 'com_jmm')) {
            if ($app->getInput()->getCmd('format') === 'json') {
                echo new JsonResponse(null, Text::_('JERROR_ALERTNOAUTHOR'), true);
                $app->close();
            }
            $this->setRedirect(Route::_('index.php?option=com_jmm&view=createtable', false), Text::_('JERROR_ALERTNOAUTHOR'), 'error');
            return;
        }

        /** @var \Saywhat49\Component\Jmm\Administrator\Model\CreatetableModel $model */
        $model = $this->getModel('Createtable');
        $postData = $app->getInput()->post->getArray();
        $result = $model->createTable($postData);

        if ($app->getInput()->getCmd('format') === 'json') {
            echo new JsonResponse($result['data'] ?? null, $result['msg'] ?? '', !$result['status']);
            $app->close();
        }

        $this->setRedirect(
            Route::_('index.php?option=com_jmm&view=createtable', false),
            $result['msg'],
            $result['status'] ? 'message' : 'error'
        );
    }
}