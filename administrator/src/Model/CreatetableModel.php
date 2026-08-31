<?php
namespace Saywhat49\Component\Jmm\Administrator\Model;

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Saywhat49\Component\Jmm\Administrator\Helper\JmmHelper;

class CreatetableModel extends BaseDatabaseModel
{
    public function createTable(array $posts): array
    {
        $rawTableName = $posts['tbl_name'] ?? '';
        $tableName = JmmHelper::cleanIdentifier($rawTableName);
        $dbname = $posts['dbname'] ?? null;

        if ($tableName === '') {
            return ['status' => false, 'msg' => Text::_('COM_JMM_INVALID_TABLE_NAME')];
        }

        if (empty($posts['field_name']) || !is_array($posts['field_name'])) {
            return ['status' => false, 'msg' => Text::_('COM_JMM_AT_LEAST_ONE_FIELD')];
        }

        $allowedTypes = [
            'INT', 'VARCHAR', 'TEXT', 'DATE', 'TINYINT', 'SMALLINT', 'MEDIUMINT', 'BIGINT',
            'DECIMAL', 'FLOAT', 'DOUBLE', 'REAL', 'BIT', 'BOOLEAN', 'SERIAL', 'DATETIME',
            'TIMESTAMP', 'TIME', 'YEAR', 'CHAR', 'TINYTEXT', 'MEDIUMTEXT', 'LONGTEXT',
            'BINARY', 'VARBINARY', 'TINYBLOB', 'MEDIUMBLOB', 'BLOB', 'LONGBLOB'
        ];

        $allowedEngines = ['INNODB', 'MYISAM', 'MEMORY', 'CSV', 'ARCHIVE', 'BLACKHOLE'];

        $db = JmmHelper::getDatabaseConnection($dbname);
        $fieldDefinitions = [];
        $primaryKeys = [];
        $uniqueKeys = [];
        $indexKeys = [];

        foreach ($posts['field_name'] as $i => $rawName) {
            $fieldName = JmmHelper::cleanIdentifier($rawName);
            if ($fieldName === '') {
                continue;
            }

            $rawType = strtoupper((string) ($posts['field_type'][$i] ?? 'VARCHAR'));
            $fieldType = in_array($rawType, $allowedTypes, true) ? $rawType : 'VARCHAR';

            $rawLength = $posts['field_length'][$i] ?? '';
            $lengthSql = (is_numeric($rawLength) && (int) $rawLength > 0) ? '(' . (int) $rawLength . ')' : '';

            $nullSql = isset($posts['field_null'][$i]) ? ' NULL' : ' NOT NULL';
            $extraSql = (($posts['field_extra'][$i] ?? '') === 'AUTO_INCREMENT') ? ' AUTO_INCREMENT' : '';

            $rawComment = trim((string) ($posts['field_comments'][$i] ?? ''));
            $commentSql = $rawComment !== '' ? " COMMENT " . $db->quote($rawComment) : '';

            $fieldDefinitions[] = $db->quoteName($fieldName) . ' ' . $fieldType . $lengthSql . $nullSql . $extraSql . $commentSql;

            $keyType = $posts['field_key'][$i] ?? 'none';
            if ($keyType === 'primary') {
                $primaryKeys[] = $db->quoteName($fieldName);
            } elseif ($keyType === 'unique') {
                $uniqueKeys[] = $db->quoteName($fieldName);
            } elseif ($keyType === 'index') {
                $indexKeys[] = $db->quoteName($fieldName);
            }
        }

        if (empty($fieldDefinitions)) {
            return ['status' => false, 'msg' => Text::_('COM_JMM_AT_LEAST_ONE_FIELD')];
        }

        $query = 'CREATE TABLE IF NOT EXISTS ' . $db->quoteName($tableName) . " (\n";
        $query .= implode(",\n", $fieldDefinitions);

        if (!empty($primaryKeys)) {
            $query .= ",\n PRIMARY KEY (" . implode(',', $primaryKeys) . ")";
        }
        foreach ($uniqueKeys as $uCol) {
            $idxName = $db->quoteName('uk_' . substr(md5($uCol), 0, 8));
            $query .= ",\n UNIQUE KEY {$idxName} ({$uCol})";
        }
        foreach ($indexKeys as $iCol) {
            $idxName = $db->quoteName('idx_' . substr(md5($iCol), 0, 8));
            $query .= ",\n KEY {$idxName} ({$iCol})";
        }

        $rawEngine = strtoupper((string) ($posts['tbl_type'] ?? 'INNODB'));
        $engine = in_array($rawEngine, $allowedEngines, true) ? $rawEngine : 'INNODB';

        $rawTblComment = trim((string) ($posts['tbl_comments'] ?? ''));
        $tblCommentSql = $rawTblComment !== '' ? " COMMENT=" . $db->quote($rawTblComment) : '';

        $query .= "\n) ENGINE=" . $engine . " DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci" . $tblCommentSql;

        try {
            $db->setQuery($query)->execute();
            return [
                'status' => true,
                'msg'    => Text::sprintf('COM_JMM_TABLE_CREATED_SUCCESS_WITH_NAME', $tableName),
                'data'   => ['tableName' => $tableName],
            ];
        } catch (\Throwable $e) {
            return ['status' => false, 'msg' => $e->getMessage()];
        }
    }
}