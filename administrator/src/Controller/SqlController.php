<?php
namespace Saywhat49\Component\Jmm\Administrator\Controller;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Response\JsonResponse;
use Joomla\CMS\Session\Session;

class SqlController extends BaseController
{
    public function display($cachable = false, $urlparams = [])
    {
        $user = Factory::getApplication()->getIdentity();
        if (!$user->authorise('core.admin', 'com_jmm') && !$user->authorise('core.manage', 'com_jmm')) {
            throw new \Exception(Text::_('JERROR_ALERTNOAUTHOR'), 403);
        }

        return parent::display($cachable, $urlparams);
    }

    public function saveCannedQuery(): void
    {
        $app = Factory::getApplication();
        if (!Session::checkToken('request')) {
            echo new JsonResponse(null, Text::_('JINVALID_TOKEN'), true);
            $app->close();
        }

        $user = $app->getIdentity();
        if (!$user->authorise('core.create', 'com_jmm')) {
            echo new JsonResponse(null, Text::_('JERROR_ALERTNOAUTHOR'), true);
            $app->close();
        }

        $input = $app->getInput();
        $title = $input->getString('title', '');
        $query = $input->getString('query', '', 'raw');
        $dbname = $input->getString('dbname', '');

        /** @var \Saywhat49\Component\Jmm\Administrator\Model\SqlModel $model */
        $model = $this->getModel('Sql');
        $result = $model->saveCannedQuery([
            'title' => $title,
            'query' => $query,
            'dbname' => $dbname,
        ]);

        echo new JsonResponse($result['data'] ?? null, $result['msg'] ?? '', !$result['status']);
        $app->close();
    }

    public function saveSiteTable(): void
    {
        $app = Factory::getApplication();
        if (!Session::checkToken('request')) {
            echo new JsonResponse(null, Text::_('JINVALID_TOKEN'), true);
            $app->close();
        }

        $user = $app->getIdentity();
        if (!$user->authorise('core.create', 'com_jmm')) {
            echo new JsonResponse(null, Text::_('JERROR_ALERTNOAUTHOR'), true);
            $app->close();
        }

        $input = $app->getInput();
        $title = $input->getString('title', '');
        $query = $input->getString('query', '', 'raw');
        $dbname = $input->getString('dbname', '');

        /** @var \Saywhat49\Component\Jmm\Administrator\Model\SqlModel $model */
        $model = $this->getModel('Sql');
        $result = $model->saveSiteTable([
            'title' => $title,
            'query' => $query,
            'dbname' => $dbname,
        ]);

        echo new JsonResponse($result['data'] ?? null, $result['msg'] ?? '', !$result['status']);
        $app->close();
    }
}