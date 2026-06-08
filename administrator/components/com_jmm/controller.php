<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_jmm
 *
 * Corrected administrator controller.
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Log\Log;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;

class JMMController extends BaseController
{
    private array $posts = [];

    public function display($cachable = false, $urlparams = [])
    {
        $input = Factory::getApplication()->input;
        $viewName = $input->getCmd('view', '');

        if (class_exists('JMMHelper')) {
            JMMHelper::addSubmenu($viewName);
        }

        return parent::display($cachable, $urlparams);
    }

    public function saveCannedQuery()
    {
        if (!Session::checkToken()) {
            $this->sendJson(['status' => false, 'msg' => Text::_('JINVALID_TOKEN')]);
        }

        try {
            $model = $this->getModel('SQL');
            $response = $model->saveCannedQuery(Factory::getApplication()->input->post->getArray());
            $this->sendJson($response);
        } catch (Throwable $e) {
            Log::add($e->getMessage(), Log::ERROR, 'com_jmm');
            $this->sendJson(['status' => false, 'msg' => 'Erreur lors de l’enregistrement de la requête.']);
        }
    }

    public function saveSiteTable()
    {
        if (!Session::checkToken()) {
            $this->sendJson(['status' => false, 'msg' => Text::_('JINVALID_TOKEN')]);
        }

        try {
            $model = $this->getModel('SQL');
            $response = $model->saveSiteTable(Factory::getApplication()->input->post->getArray());
            $this->sendJson($response);
        } catch (Throwable $e) {
            Log::add($e->getMessage(), Log::ERROR, 'com_jmm');
            $this->sendJson(['status' => false, 'msg' => 'Erreur lors de l’enregistrement de la table site.']);
        }
    }

    public function createTableStructure()
    {
        if (!Session::checkToken()) {
            $this->sendResponse(['status' => false, 'msg' => Text::_('JINVALID_TOKEN')]);
            return;
        }

        $this->posts = Factory::getApplication()->input->post->getArray();

        $tableName = $this->cleanIdentifier($this->posts['tbl_name'] ?? '');
        if ($tableName === '') {
            $this->sendResponse(['status' => false, 'msg' => Text::_('COM_JMM_INVALID_TABLE_NAME')]);
            return;
        }
        $this->posts['tbl_name'] = $tableName;

        if (empty($this->posts['field_name']) || !is_array($this->posts['field_name'])) {
            $this->sendResponse(['status' => false, 'msg' => Text::_('COM_JMM_AT_LEAST_ONE_FIELD')]);
            return;
        }

        try {
            $db = Factory::getDbo();
            $query = 'CREATE TABLE IF NOT EXISTS ' . $db->quoteName($this->getTableName()) . " (\n";

            $fields = [];
            foreach ($this->posts['field_name'] as $i => $rawFieldName) {
                $fieldName = $this->cleanIdentifier($rawFieldName);
                if ($fieldName === '') {
                    continue;
                }

                $fields[] = $db->quoteName($fieldName)
                    . $this->getFieldType($i)
                    . $this->getFieldLength($i)
                    . $this->getNull($i)
                    . $this->getAutoIncrements($i)
                    . $this->getFieldComments($i);
            }

            if (empty($fields)) {
                $this->sendResponse(['status' => false, 'msg' => Text::_('COM_JMM_AT_LEAST_ONE_FIELD')]);
                return;
            }

            $query .= implode(",\n", $fields);
            $query .= $this->getTableKeys($db);
            $query .= "\n) ENGINE=" . $this->getTableType()
                . ' DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci'
                . $this->getTableComment()
                . $this->getAutoIncrementCounter();

            $db->setQuery($query)->execute();

            $this->sendResponse([
                'status' => true,
                'msg' => Text::sprintf('COM_JMM_TABLE_CREATED_SUCCESS_WITH_NAME', $this->getTableName()),
            ]);
        } catch (Throwable $e) {
            Log::add($e->getMessage(), Log::ERROR, 'com_jmm');
            $this->sendResponse(['status' => false, 'msg' => Text::_('COM_JMM_TABLE_CREATION_ERROR')]);
        }
    }

    private function sendJson(array $result): void
    {
        $app = Factory::getApplication();
        $app->setHeader('Content-Type', 'application/json; charset=utf-8', true);
        echo json_encode($result);
        $app->close();
    }

    private function sendResponse(array $result): void
    {
        $app = Factory::getApplication();
        $input = $app->input;

        if ($input->getCmd('format') === 'json' || $input->server->getString('HTTP_X_REQUESTED_WITH') === 'XMLHttpRequest') {
            $this->sendJson([
                'success' => (bool) $result['status'],
                'message' => $result['msg'] ?? null,
                'data' => $result,
            ]);
        }

        $this->setRedirect(
            Route::_('index.php?option=com_jmm&view=createTable', false),
            $result['msg'] ?? '',
            !empty($result['status']) ? 'message' : 'error'
        );
    }

    private function cleanIdentifier($value): string
    {
        return preg_replace('/[^A-Za-z0-9_]/', '', trim((string) $value));
    }

    private function getNull(int $fieldIndex): string
    {
        return isset($this->posts['field_null'][$fieldIndex]) ? ' NULL' : ' NOT NULL';
    }

    private function getAutoIncrements(int $fieldIndex): string
    {
        return (($this->posts['field_extra'][$fieldIndex] ?? '') === 'AUTO_INCREMENT') ? ' AUTO_INCREMENT' : '';
    }

    private function getAutoIncrementCounter(): string
    {
        foreach (($this->posts['field_extra'] ?? []) as $extra) {
            if ($extra === 'AUTO_INCREMENT') {
                return ' AUTO_INCREMENT=1';
            }
        }
        return '';
    }

    private function getFieldLength(int $fieldIndex): string
    {
        $length = $this->posts['field_length'][$fieldIndex] ?? '';
        return (is_numeric($length) && (int) $length > 0) ? '(' . (int) $length . ')' : '';
    }

    private function getFieldType(int $fieldIndex): string
    {
        $allowedTypes = [
            'TINYINT','SMALLINT','MEDIUMINT','INT','BIGINT','DECIMAL','FLOAT','DOUBLE','REAL','BIT','BOOLEAN','SERIAL',
            'DATE','DATETIME','TIMESTAMP','TIME','YEAR','CHAR','VARCHAR','TINYTEXT','TEXT','MEDIUMTEXT','LONGTEXT',
            'BINARY','VARBINARY','TINYBLOB','MEDIUMBLOB','BLOB','LONGBLOB','GEOMETRY','POINT','LINESTRING','POLYGON',
            'MULTIPOINT','MULTILINESTRING','MULTIPOLYGON','GEOMETRYCOLLECTION'
        ];

        $type = strtoupper((string) ($this->posts['field_type'][$fieldIndex] ?? 'VARCHAR'));
        return ' ' . (in_array($type, $allowedTypes, true) ? $type : 'VARCHAR');
    }

    private function getFieldComments(int $fieldIndex): string
    {
        $comment = trim((string) ($this->posts['field_comments'][$fieldIndex] ?? ''));
        return $comment !== '' ? " COMMENT '" . str_replace("'", "''", $comment) . "'" : '';
    }

    private function getTableName(): string
    {
        return $this->posts['tbl_name'];
    }

    private function getTableType(): string
    {
        $allowedEngines = ['MEMORY', 'CSV', 'MRG_MYISAM', 'BLACKHOLE', 'MYISAM', 'INNODB', 'ARCHIVE'];
        $engine = strtoupper((string) ($this->posts['tbl_type'] ?? 'INNODB'));
        return in_array($engine, $allowedEngines, true) ? $engine : 'INNODB';
    }

    private function getTableComment(): string
    {
        $comment = trim((string) ($this->posts['tbl_comments'] ?? ''));
        return $comment !== '' ? " COMMENT '" . str_replace("'", "''", $comment) . "'" : '';
    }

    private function getTableKeys($db): string
    {
        if (empty($this->posts['field_key']) || !is_array($this->posts['field_key'])) {
            return '';
        }

        $groups = ['primary' => [], 'unique' => [], 'index' => [], 'fulltext' => []];

        foreach ($this->posts['field_key'] as $index => $value) {
            $fieldName = $this->cleanIdentifier($this->posts['field_name'][$index] ?? '');
            if ($fieldName !== '' && isset($groups[$value])) {
                $groups[$value][] = $db->quoteName($fieldName);
            }
        }

        $keys = '';
        if ($groups['primary']) {
            $keys .= ', PRIMARY KEY (' . implode(',', $groups['primary']) . ')';
        }
        if ($groups['unique']) {
            $keys .= ', UNIQUE KEY ' . $db->quoteName('uk_' . substr(md5(implode(',', $groups['unique'])), 0, 8)) . ' (' . implode(',', $groups['unique']) . ')';
        }
        if ($groups['index']) {
            $keys .= ', KEY ' . $db->quoteName('idx_' . substr(md5(implode(',', $groups['index'])), 0, 8)) . ' (' . implode(',', $groups['index']) . ')';
        }
        if ($groups['fulltext']) {
            $keys .= ', FULLTEXT KEY ' . $db->quoteName('ft_' . substr(md5(implode(',', $groups['fulltext'])), 0, 8)) . ' (' . implode(',', $groups['fulltext']) . ')';
        }

        return $keys;
    }
}
