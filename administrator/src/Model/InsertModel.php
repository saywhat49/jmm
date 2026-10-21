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
        $tableName = trim($tableName);

        if (!JmmHelper::isValidIdentifier($tableName)) {
            return ['status' => false, 'msg' => Text::_('COM_JMM_INVALID_TABLE_NAME')];
        }

        try {
            $db = JmmHelper::getDatabaseConnection($dbname);
        } catch (\RuntimeException $e) {
            return ['status' => false, 'msg' => $e->getMessage()];
        }

        // Le nom de table n'est utilise qu'apres avoir ete retrouve dans la
        // base : un nom "nettoye" en silence pouvait viser une autre table.
        $table = JmmHelper::resolveTable($tableName, $db);

        if ($table === null) {
            return ['status' => false, 'msg' => Text::_('COM_JMM_INVALID_TABLE_NAME')];
        }

        $columns = JmmHelper::getColumnsInfo($table, $db);

        if (empty($columns)) {
            return ['status' => false, 'msg' => Text::_('COM_JMM_ERROR_NO_COLUMNS_FOUND')];
        }

        $insertCols = [];
        $insertVals = [];

        foreach ($data as $col => $val) {
            $col = (string) $col;

            if (!isset($columns[$col]) || (!is_scalar($val) && $val !== null)) {
                continue;
            }

            $info = $columns[$col];
            $val  = (string) $val;

            if ($val === '') {
                if ($this->isStringType((string) ($info['Type'] ?? ''))) {
                    // Une colonne de texte laissee vide recoit une chaine vide.
                    $sqlValue = $db->quote('');
                } elseif (($info['Null'] ?? '') === 'YES') {
                    // Nombre, date... : une valeur vide n'a pas de sens, NULL oui.
                    $sqlValue = 'NULL';
                } else {
                    // Colonne obligatoire : on laisse jouer la valeur par defaut
                    // (ou l'AUTO_INCREMENT) au lieu d'envoyer '' que MySQL
                    // refuse en mode strict.
                    continue;
                }
            } else {
                $sqlValue = $db->quote($val);
            }

            $insertCols[] = $db->quoteName($col);
            $insertVals[] = $sqlValue;
        }

        if (empty($insertCols)) {
            return ['status' => false, 'msg' => Text::_('COM_JMM_ERROR_NO_DATA_TO_INSERT')];
        }

        $query = $db->getQuery(true)
            ->insert($db->quoteName($table))
            ->columns($insertCols)
            ->values(implode(',', $insertVals));

        try {
            $db->setQuery($query)->execute();

            return ['status' => true, 'msg' => Text::_('COM_JMM_RECORD_INSERTED_SUCCESS')];
        } catch (\Throwable $e) {
            return ['status' => false, 'msg' => $e->getMessage()];
        }
    }

    /** Colonnes qui acceptent une chaine vide : CHAR, VARCHAR, TEXT, BINARY, BLOB. */
    private function isStringType(string $type): bool
    {
        return (bool) preg_match('/^(?:var)?(?:char|binary)\b|^(?:tiny|medium|long)?(?:text|blob)\b/i', $type);
    }
}
