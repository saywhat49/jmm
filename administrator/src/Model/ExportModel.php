<?php
namespace Saywhat49\Component\Jmm\Administrator\Model;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
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
            echo Text::sprintf('COM_JMM_EXPORT_QUERY_ERROR', $e->getMessage());
            return;
        }

        if (empty($rows)) {
            echo Text::_('COM_JMM_EXPORT_NO_RECORDS');
            return;
        }

        $safeFilename = preg_replace('/[^A-Za-z0-9_-]/', '', $filename) ?: 'export';
        $safeFilename .= '-' . date('Ymd-His') . '.csv';

        $app = Factory::getApplication();
        $app->setHeader('Content-Type', 'text/csv; charset=UTF-8', true);
        $app->setHeader('Content-Disposition', 'attachment; filename="' . $safeFilename . '"', true);
        $app->setHeader('Pragma', 'no-cache', true);
        $app->setHeader('Expires', '0', true);

        // Sans sendHeaders(), les en-tetes restent dans l'objet reponse et
        // $app->close() sort du script sans jamais les emettre : le
        // navigateur affiche le CSV comme du texte au lieu de le telecharger.
        // On vide aussi les tampons de sortie de Joomla pour que le flux ne
        // soit pas precede du debut de la page d'administration.
        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        $app->sendHeaders();

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