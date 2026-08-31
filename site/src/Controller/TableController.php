<?php
namespace Saywhat49\Component\Jmm\Site\Controller;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Response\JsonResponse;
use Joomla\CMS\Session\Session;
use Saywhat49\Component\Jmm\Administrator\Helper\JmmHelper;

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

        $rawQuery = rtrim(trim((string) $siteTable->query), " \t\n\r\0\x0B;");
        if (!preg_match('/^\s*(SELECT|SHOW|DESCRIBE|DESC)\s/i', $rawQuery)) {
            echo new JsonResponse(['Result' => 'ERROR', 'Message' => Text::_('COM_JMM_INVALID_QUERY_TYPE')], null, true);
            $app->close();
        }

        $db = JmmHelper::getDatabaseConnection($siteTable->dbname ?: null);

        try {
            $countQuery = 'SELECT COUNT(*) FROM (' . $rawQuery . ') AS jmm_cnt';
            $db->setQuery($countQuery);
            $total = (int) $db->loadResult();

            $orderBy = $this->buildSafeOrderBy($db, $rawQuery, $jtSorting);
            $pagedQuery = $rawQuery;
            if ($orderBy !== '') {
                $pagedQuery .= ' ORDER BY ' . $orderBy;
            }
            $pagedQuery .= ' LIMIT ' . (int) $jtStartIndex . ', ' . (int) $jtPageSize;

            $db->setQuery($pagedQuery);
            $records = $db->loadAssocList();
            $records = is_array($records) ? $records : [];

            echo new JsonResponse([
                'Result'           => 'OK',
                'TotalRecordCount' => $total,
                'Records'          => $records,
            ]);
        } catch (\Throwable $e) {
            echo new JsonResponse(['Result' => 'ERROR', 'Message' => Text::_('COM_JMM_ERROR_LOADING_DATA')], null, true);
        }

        $app->close();
    }

    private function buildSafeOrderBy($db, string $query, string $jtSorting): string
    {
        if ($jtSorting === '') {
            return '';
        }

        try {
            $db->setQuery($query . ' LIMIT 1');
            $sample = $db->loadAssocList();
            if (empty($sample)) {
                return '';
            }

            $allowedColumns = array_keys($sample[0]);
            $parts = array_map('trim', explode(',', $jtSorting));
            $safe = [];

            foreach ($parts as $part) {
                if (!preg_match('/^([A-Za-z0-9_]+)(?:\s+(ASC|DESC))?$/i', $part, $matches)) {
                    continue;
                }

                $column = $matches[1];
                $dir    = strtoupper($matches[2] ?? 'ASC');

                if (in_array($column, $allowedColumns, true)) {
                    $safe[] = $db->quoteName($column) . ' ' . ($dir === 'DESC' ? 'DESC' : 'ASC');
                }
            }

            return implode(', ', $safe);
        } catch (\Throwable $e) {
            return '';
        }
    }
}