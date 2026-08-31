<?php
namespace Saywhat49\Component\Jmm\Administrator\Model;

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\AdminModel;
use Saywhat49\Component\Jmm\Administrator\Helper\JmmHelper;

class InsertModel extends AdminModel
{
    public function getForm($data = [], $loadData = true)
    {
        return false;
    }

    public function insertRow(string $tableName, array $data, ?string $dbname = null): array
    {
        $cleanTable = JmmHelper::cleanIdentifier($tableName);
        if ($cleanTable === '') {
            return ['status' => false, 'msg' => Text::_('COM_JMM_INVALID_TABLE_NAME')];
        }

        $db = JmmHelper::getDatabaseConnection($dbname);
        $validColumns = JmmHelper::getColumnsFromTable($cleanTable, $db);

        if (empty($validColumns)) {
            return ['status' => false, 'msg' => Text::_('COM_JMM_ERROR_NO_COLUMNS_FOUND')];
        }

        $insertCols = [];
        $insertVals = [];

        foreach ($data as $col => $val) {
            if (!in_array($col, $validColumns, true)) {
                continue;
            }

            $insertCols[] = $db->quoteName($col);
            $insertVals[] = $db->quote((string) $val);
        }

        if (empty($insertCols)) {
            return ['status' => false, 'msg' => Text::_('COM_JMM_ERROR_NO_DATA_TO_INSERT')];
        }

        $query = $db->getQuery(true)
            ->insert($db->quoteName($cleanTable))
            ->columns($insertCols)
            ->values(implode(',', $insertVals));

        try {
            $db->setQuery($query)->execute();
            return ['status' => true, 'msg' => Text::_('COM_JMM_RECORD_INSERTED_SUCCESS')];
        } catch (\Throwable $e) {
            return ['status' => false, 'msg' => $e->getMessage()];
        }
    }
}