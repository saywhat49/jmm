<?php
namespace Saywhat49\Component\Jmm\Administrator\Model;

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Saywhat49\Component\Jmm\Administrator\Helper\JmmHelper;

class CreatetableModel extends BaseDatabaseModel
{
    /**
     * Construit la portion de longueur d'une definition de colonne.
     *
     * Renvoie la chaine a concatener ('' si aucune longueur), ou leve une
     * exception avec un message lisible. On valide ici plutot que de laisser
     * MySQL repondre : "You have an error in your SQL syntax near '(3)'"
     * n'aide personne a comprendre que DATE n'accepte pas de longueur.
     *
     * @throws \RuntimeException
     */
    private function buildLength(string $type, string $spec, string $raw, string $column): string
    {
        $raw = trim($raw);

        if ($spec === 'none') {
            if ($raw !== '') {
                throw new \RuntimeException(
                    Text::sprintf('COM_JMM_ERROR_TYPE_TAKES_NO_LENGTH', $column, $type)
                );
            }

            return '';
        }

        if ($raw === '') {
            if ($spec === 'int_req') {
                throw new \RuntimeException(
                    Text::sprintf('COM_JMM_ERROR_LENGTH_REQUIRED', $column, $type)
                );
            }

            return '';
        }

        // Forme (M,D) : DECIMAL, NUMERIC, FLOAT, DOUBLE, REAL.
        if ($spec === 'decimal') {
            if (!preg_match('/^(\d{1,2})(?:\s*,\s*(\d{1,2}))?$/', $raw, $m)) {
                throw new \RuntimeException(
                    Text::sprintf('COM_JMM_ERROR_DECIMAL_FORMAT', $column)
                );
            }

            $precision = (int) $m[1];
            $scale     = isset($m[2]) ? (int) $m[2] : null;

            if ($precision < 1 || $precision > 65) {
                throw new \RuntimeException(
                    Text::sprintf('COM_JMM_ERROR_DECIMAL_PRECISION', $column)
                );
            }

            if ($scale === null) {
                return '(' . $precision . ')';
            }

            if ($scale > 30 || $scale > $precision) {
                throw new \RuntimeException(
                    Text::sprintf('COM_JMM_ERROR_DECIMAL_SCALE', $column)
                );
            }

            return '(' . $precision . ',' . $scale . ')';
        }

        if (!preg_match('/^\d{1,5}$/', $raw)) {
            throw new \RuntimeException(
                Text::sprintf('COM_JMM_ERROR_LENGTH_NOT_NUMERIC', $column)
            );
        }

        $value = (int) $raw;

        // Precision des fractions de seconde : DATETIME(0) a DATETIME(6).
        if ($spec === 'fsp') {
            if ($value > 6) {
                throw new \RuntimeException(
                    Text::sprintf('COM_JMM_ERROR_FSP_RANGE', $column)
                );
            }

            return '(' . $value . ')';
        }

        if ($spec === 'bit') {
            if ($value < 1 || $value > 64) {
                throw new \RuntimeException(
                    Text::sprintf('COM_JMM_ERROR_BIT_RANGE', $column)
                );
            }

            return '(' . $value . ')';
        }

        // CHAR va jusqu'a 255, VARCHAR jusqu'a 65535 octets (limite de ligne).
        $max = ($type === 'CHAR' || $type === 'BINARY') ? 255 : 65535;

        if ($value < 1 || $value > $max) {
            throw new \RuntimeException(
                Text::sprintf('COM_JMM_ERROR_LENGTH_RANGE', $column, $max)
            );
        }

        return '(' . $value . ')';
    }

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

        $types        = JmmHelper::getColumnTypes();
        $autoIncTypes = JmmHelper::getAutoIncrementTypes();
        $allowedEngines = ['INNODB', 'MYISAM', 'MEMORY', 'CSV', 'ARCHIVE', 'BLACKHOLE'];

        $db = JmmHelper::getDatabaseConnection($dbname);

        $fieldDefinitions = [];
        $primaryKeys = [];
        $uniqueKeys = [];
        $indexKeys = [];
        $autoIncrementCount = 0;
        $seenNames = [];

        try {
            foreach ($posts['field_name'] as $i => $rawName) {
                $fieldName = JmmHelper::cleanIdentifier((string) $rawName);

                if ($fieldName === '') {
                    continue;
                }

                $lower = strtolower($fieldName);

                if (isset($seenNames[$lower])) {
                    throw new \RuntimeException(
                        Text::sprintf('COM_JMM_ERROR_DUPLICATE_COLUMN', $fieldName)
                    );
                }

                $seenNames[$lower] = true;

                $rawType   = strtoupper(trim((string) ($posts['field_type'][$i] ?? 'VARCHAR')));
                $fieldType = isset($types[$rawType]) ? $rawType : 'VARCHAR';

                $lengthSql = $this->buildLength(
                    $fieldType,
                    $types[$fieldType],
                    (string) ($posts['field_length'][$i] ?? ''),
                    $fieldName
                );

                $keyType  = $posts['field_key'][$i] ?? 'none';
                $isAuto   = (($posts['field_extra'][$i] ?? '') === 'AUTO_INCREMENT');

                if ($isAuto) {
                    if (!in_array($fieldType, $autoIncTypes, true)) {
                        throw new \RuntimeException(
                            Text::sprintf('COM_JMM_ERROR_AUTOINC_TYPE', $fieldName, $fieldType)
                        );
                    }

                    // MySQL exige que la colonne AUTO_INCREMENT porte un index.
                    if (!in_array($keyType, ['primary', 'unique', 'index'], true)) {
                        throw new \RuntimeException(
                            Text::sprintf('COM_JMM_ERROR_AUTOINC_KEY', $fieldName)
                        );
                    }

                    $autoIncrementCount++;

                    if ($autoIncrementCount > 1) {
                        throw new \RuntimeException(Text::_('COM_JMM_ERROR_AUTOINC_UNIQUE'));
                    }
                }

                // Une colonne de cle primaire ne peut pas etre NULL.
                $wantsNull = isset($posts['field_null'][$i]) && $keyType !== 'primary' && !$isAuto;
                $nullSql   = $wantsNull ? ' NULL' : ' NOT NULL';
                $extraSql  = $isAuto ? ' AUTO_INCREMENT' : '';

                $rawComment = trim((string) ($posts['field_comments'][$i] ?? ''));
                $commentSql = $rawComment !== '' ? ' COMMENT ' . $db->quote($rawComment) : '';

                $fieldDefinitions[] = $db->quoteName($fieldName) . ' ' . $fieldType
                    . $lengthSql . $nullSql . $extraSql . $commentSql;

                if ($keyType === 'primary') {
                    $primaryKeys[] = $fieldName;
                } elseif ($keyType === 'unique') {
                    $uniqueKeys[] = $fieldName;
                } elseif ($keyType === 'index') {
                    $indexKeys[] = $fieldName;
                }
            }
        } catch (\RuntimeException $e) {
            return ['status' => false, 'msg' => $e->getMessage()];
        }

        if (empty($fieldDefinitions)) {
            return ['status' => false, 'msg' => Text::_('COM_JMM_AT_LEAST_ONE_FIELD')];
        }

        $query = 'CREATE TABLE IF NOT EXISTS ' . $db->quoteName($tableName) . " (\n";
        $query .= implode(",\n", $fieldDefinitions);

        if (!empty($primaryKeys)) {
            $quoted = array_map([$db, 'quoteName'], $primaryKeys);
            $query .= ",\n PRIMARY KEY (" . implode(',', $quoted) . ')';
        }

        // Les noms d'index derivent du nom de colonne, tronque puis suffixe
        // d'une empreinte courte : lisible dans SHOW INDEX, et sans collision
        // entre deux colonnes dont les 20 premiers caracteres coincident.
        foreach ($uniqueKeys as $col) {
            $query .= ",\n UNIQUE KEY " . $db->quoteName($this->indexName('uk', $col))
                . ' (' . $db->quoteName($col) . ')';
        }

        foreach ($indexKeys as $col) {
            $query .= ",\n KEY " . $db->quoteName($this->indexName('idx', $col))
                . ' (' . $db->quoteName($col) . ')';
        }

        $rawEngine = strtoupper((string) ($posts['tbl_type'] ?? 'INNODB'));
        $engine = in_array($rawEngine, $allowedEngines, true) ? $rawEngine : 'INNODB';

        $rawTblComment = trim((string) ($posts['tbl_comments'] ?? ''));
        $tblCommentSql = $rawTblComment !== '' ? ' COMMENT=' . $db->quote($rawTblComment) : '';

        $query .= "\n) ENGINE=" . $engine . ' DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci' . $tblCommentSql;

        try {
            $db->setQuery($query)->execute();

            return [
                'status' => true,
                'msg'    => Text::sprintf('COM_JMM_TABLE_CREATED_SUCCESS_WITH_NAME', $tableName),
                'data'   => ['tableName' => $tableName, 'sql' => $query],
            ];
        } catch (\Throwable $e) {
            return ['status' => false, 'msg' => $e->getMessage()];
        }
    }

    private function indexName(string $prefix, string $column): string
    {
        return $prefix . '_' . substr($column, 0, 20) . '_' . substr(md5($column), 0, 6);
    }
}
