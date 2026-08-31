<?php
namespace Saywhat49\Component\Jmm\Administrator\Model;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Saywhat49\Component\Jmm\Administrator\Helper\JmmHelper;

class ExportModel extends BaseDatabaseModel
{
    public function streamCsv(string $sqlQuery, string $filename = 'export', ?string $dbname = null): void
    {
        $sqlQuery = trim($sqlQuery);
        if ($sqlQuery === '') {
            return;
        }

        $db = JmmHelper::getDatabaseConnection($dbname);
        try {
            $db->setQuery($sqlQuery);
            $rows = $db->loadAssocList();
        } catch (\Throwable $e) {
            echo "Error executing query: " . $e->getMessage();
            return;
        }

        if (empty($rows)) {
            echo "No records to export.";
            return;
        }

        $safeFilename = preg_replace('/[^A-Za-z0-9_-]/', '', $filename) ?: 'export';
        $safeFilename .= '-' . date('Ymd-His') . '.csv';

        $app = Factory::getApplication();
        $app->setHeader('Content-Type', 'text/csv; charset=UTF-8', true);
        $app->setHeader('Content-Disposition', 'attachment; filename="' . $safeFilename . '"', true);
        $app->setHeader('Pragma', 'no-cache', true);
        $app->setHeader('Expires', '0', true);

        echo "\xEF\xBB\xBF";

        $output = fopen('php://output', 'w');
        $headers = array_keys($rows[0]);
        fputcsv($output, $headers);

        foreach ($rows as $row) {
            $cleanRow = [];
            foreach ($row as $val) {
                $strVal = (string) $val;
                if ($strVal !== '' && in_array($strVal[0], ['=', '+', '-', '@', "\t", "\r"], true)) {
                    $strVal = "'" . $strVal;
                }
                $cleanRow[] = $strVal;
            }
            fputcsv($output, $cleanRow);
        }

        fclose($output);
    }
}