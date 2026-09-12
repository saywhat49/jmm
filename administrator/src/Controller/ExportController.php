<?php
namespace Saywhat49\Component\Jmm\Administrator\Controller;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Session\Session;

class ExportController extends BaseController
{
    public function csv(): void
    {
        $app = Factory::getApplication();
        $input = $app->getInput();

        if (!Session::checkToken('request')) {
            throw new \Exception(Text::_('JINVALID_TOKEN'), 403);
        }

        $user = $app->getIdentity();
        if (!$user->authorise('core.manage', 'com_jmm')) {
            throw new \Exception(Text::_('JERROR_ALERTNOAUTHOR'), 403);
        }

        // getString applique le filtre STRING, qui supprime tout ce qui
        // ressemble a une balise : "WHERE a < 5 AND b > 2" y perdrait un
        // morceau. Une requete SQL se lit en brut.
        $query = (string) $input->get('query', '', 'raw');
        $rawFilename = $input->getString('filename', 'export');
        $filename = preg_replace('/[^A-Za-z0-9_-]/', '', $rawFilename) ?: 'export';
        $dbname = $input->getString('dbname', '');

        /** @var \Saywhat49\Component\Jmm\Administrator\Model\ExportModel $model */
        $model = $this->getModel('Export');
        $model->streamCsv($query, $filename, $dbname);
        $app->close();
    }
}