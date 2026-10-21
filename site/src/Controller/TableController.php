<?php
namespace Saywhat49\Component\Jmm\Site\Controller;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Response\JsonResponse;
use Joomla\CMS\Session\Session;

class TableController extends BaseController
{
    public function getTableData(): void
    {
        $app   = Factory::getApplication();
        $input = $app->getInput();

        if (!Session::checkToken('request')) {
            echo new JsonResponse(['Result' => 'ERROR', 'Message' => Text::_('JINVALID_TOKEN')], null, true);
            $app->close();
        }

        $siteTableId  = $input->getInt('site_table_id', 0);
        $jtStartIndex = max(0, $input->getInt('jtStartIndex', 0));
        $jtPageSize   = min(500, max(1, $input->getInt('jtPageSize', 25)));
        $jtSorting    = trim((string) $input->getString('jtSorting', ''));

        /** @var \Saywhat49\Component\Jmm\Site\Model\TableModel $model */
        $model = $this->getModel('Table');
        $siteTable = $model->getSiteTableDetails($siteTableId);

        if (!$siteTable || empty($siteTable->query)) {
            echo new JsonResponse(['Result' => 'ERROR', 'Message' => Text::_('COM_JMM_TABLE_NOT_FOUND')], null, true);
            $app->close();
        }

        // La lecture, le comptage, le tri et le refus des requetes qui
        // ecrivent vivent dans le modele, partages avec l'affichage.
        $page = $model->fetchPage($siteTable, $jtStartIndex, $jtPageSize, $jtSorting);

        if ($page['error'] !== '') {
            // Message generique : le detail peut citer l'hote ou le compte MySQL.
            echo new JsonResponse(['Result' => 'ERROR', 'Message' => Text::_('COM_JMM_ERROR_LOADING_DATA')], null, true);
            $app->close();
        }

        echo new JsonResponse([
            'Result'           => 'OK',
            'TotalRecordCount' => $page['total'],
            'Records'          => $page['rows'],
        ]);

        $app->close();
    }
}
