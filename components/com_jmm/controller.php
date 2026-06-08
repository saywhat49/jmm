<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_jmm
 *
 * Corrected site controller.
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Response\JsonResponse;
use Joomla\CMS\Session\Session;

class JMMController extends BaseController
{
    /**
     * JTable ajax endpoint for published site tables.
     *
     * Security fixes:
     * - CSRF token check for POST/AJAX calls.
     * - jtSorting is validated against returned column names.
     * - LIMIT values are bounded.
     * - SQL errors are not exposed verbatim to visitors.
     */
    public function getTableData()
    {
        $app   = Factory::getApplication();
        $input = $app->input;

        if (!Session::checkToken('request')) {
            echo new JsonResponse([
                'Result' => 'ERROR',
                'Message' => Text::_('JINVALID_TOKEN'),
            ]);
            $app->close();
        }

        $response = [
            'Result' => 'FALSE',
            'TotalRecordCount' => 0,
            'Records' => [],
        ];

        $siteTableId  = $input->getInt('site_table_id', 0);
        $jtStartIndex = max(0, $input->getInt('jtStartIndex', 0));
        $jtPageSize   = min(500, max(1, $input->getInt('jtPageSize', 25)));
        $jtSorting    = trim((string) $input->getString('jtSorting', ''));

        try {
            $model            = $this->getModel('Table');
            $siteTableDetails = $model->getSiteTableDetails($siteTableId);

            if (!$siteTableDetails || empty($siteTableDetails->query)) {
                echo new JsonResponse($response);
                $app->close();
            }

            $dbname = $siteTableDetails->dbname ?? null;
            $db     = JMMCommon::getDBInstance(null, null, null, null, $dbname, null);
            $query  = rtrim((string) $siteTableDetails->query, " \t\n\r\0\x0B;");

            if (!preg_match('/^\s*SELECT\s/i', $query)) {
                throw new RuntimeException('Only SELECT queries are allowed on the site endpoint.');
            }

            $countQuery = 'SELECT COUNT(*) FROM (' . $query . ') AS jmm_count_table';
            $db->setQuery($countQuery);
            $total = (int) $db->loadResult();

            $orderBy = $this->buildSafeOrderBy($db, $query, $jtSorting);
            if ($orderBy !== '') {
                $query .= ' ORDER BY ' . $orderBy;
            }

            $query .= ' LIMIT ' . (int) $jtStartIndex . ', ' . (int) $jtPageSize;
            $db->setQuery($query);

            $response['Result'] = 'OK';
            $response['TotalRecordCount'] = $total;
            $response['Records'] = $db->loadAssocList();
        } catch (Throwable $e) {
            $response['Result'] = 'ERROR';
            $response['Message'] = 'Erreur lors du chargement des données.';
            Factory::getApplication()->getLogger()->error($e->getMessage(), ['category' => 'com_jmm']);
        }

        echo new JsonResponse($response);
        $app->close();
    }

    private function buildSafeOrderBy($db, string $query, string $jtSorting): string
    {
        if ($jtSorting === '') {
            return '';
        }

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

            if (!in_array($column, $allowedColumns, true)) {
                continue;
            }

            $safe[] = $db->quoteName($column) . ' ' . ($dir === 'DESC' ? 'DESC' : 'ASC');
        }

        return implode(', ', $safe);
    }
}
