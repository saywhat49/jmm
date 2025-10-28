<?php
/**
 * @package     Joomla.Component
 * @subpackage  com_jmm
 */

defined('_JEXEC') or die;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Language\Text;

require_once JPATH_COMPONENT . '/helpers/jmm.php';

class JmmController extends BaseController
{
    /**
     * Post data storage pour createTable
     * 
     * @var    array
     */
    private $posts = array();

    public function display($cachable = false, $urlparams = [])
    {
        parent::display($cachable, $urlparams);

        $input = Factory::getApplication()->input;
        $viewName = $input->getCmd('view', '');
        JMMHelper::addSubmenu($viewName);
    }

    public function saveCannedQuery()
    {
        $app = Factory::getApplication();
        $input = $app->input;

        try {
            $model = $this->getModel('SQL');
            $response = $model->saveCannedQuery($input->post->getArray());
            
            // AMÉLIORATION : En-têtes JSON appropriés
            $app->setHeader('Content-Type', 'application/json; charset=utf-8');
            echo json_encode($response);
        } catch (Exception $e) {
            // AMÉLIORATION : Gestion d'erreur robuste
            $app->setHeader('Content-Type', 'application/json; charset=utf-8');
            echo json_encode(array(
                'status' => false,
                'msg' => 'Error: ' . $e->getMessage()
            ));
        }
        
        $app->close();
    }

    public function saveSiteTable()
    {
        $app = Factory::getApplication();
        $input = $app->input;

        try {
            $model = $this->getModel('SQL');
            $response = $model->saveSiteTable($input->post->getArray());
            
            // AMÉLIORATION : En-têtes JSON appropriés
            $app->setHeader('Content-Type', 'application/json; charset=utf-8');
            echo json_encode($response);
        } catch (Exception $e) {
            // AMÉLIORATION : Gestion d'erreur robuste
            $app->setHeader('Content-Type', 'application/json; charset=utf-8');
            echo json_encode(array(
                'status' => false,
                'msg' => 'Error: ' . $e->getMessage()
            ));
        }
        
        $app->close();
    }

    /**
     * Method to create a table structure
     *
     * @return  void
     */
    public function createTableStructure()
    {
        $result = array();
        $app = Factory::getApplication();
        $input = $app->input;
        
        try {
            // Vérifier le token CSRF
            if (!Factory::getSession()->checkToken()) {
                $result['status'] = false;
                $result['msg'] = Text::_('JINVALID_TOKEN');
                $this->sendResponse($result);
                return;
            }
            
            // Get form data
            $this->posts = $input->post->getArray();
            
            // Validation des données
            if (empty($this->posts['tbl_name'])) {
                $result['status'] = false;
                $result['msg'] = Text::_('COM_JMM_FIELD_REQUIRED');
                $this->sendResponse($result);
                return;
            }
            
            if (empty($this->posts['field_name']) || !is_array($this->posts['field_name'])) {
                $result['status'] = false;
                $result['msg'] = Text::_('COM_JMM_AT_LEAST_ONE_FIELD');
                $this->sendResponse($result);
                return;
            }
            
            // Nettoyer le nom de table
            $tableName = preg_replace('/[^a-zA-Z0-9_]/', '', $this->posts['tbl_name']);
            if (empty($tableName)) {
                $result['status'] = false;
                $result['msg'] = Text::_('COM_JMM_INVALID_TABLE_NAME');
                $this->sendResponse($result);
                return;
            }
            
            $this->posts['tbl_name'] = $tableName;
            
            $totalfields = count($this->posts['field_name']);
            $query = 'CREATE TABLE IF NOT EXISTS `' . $this->getTableName() . '` (';
            $query .= "\n";
            
            $validFields = 0;
            for ($i = 0; $i < $totalfields; $i++) {
                $fieldName = trim($this->posts['field_name'][$i]);
                if (empty($fieldName)) {
                    continue; // Ignorer les champs vides
                }
                
                if ($validFields > 0) {
                    $query .= ',';
                }
                $validFields++;
                $query .= '`' . $this->getFieldName($i) . '`' . $this->getFieldType($i) . $this->getFieldLength($i) . $this->getNull($i) . $this->getAutoIncrements($i) . $this->getFieldComments($i);
            }
            
            if ($validFields === 0) {
                $result['status'] = false;
                $result['msg'] = Text::_('COM_JMM_AT_LEAST_ONE_FIELD');
                $this->sendResponse($result);
                return;
            }
            
            $query .= $this->getTableKeys();
            $query .= ')' . ' ENGINE=' . $this->getTableType() . ' DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci' . $this->getTableComment() . $this->getAutoIncrementCounter();
            
            // Utiliser Factory::getDbo()
            $db = Factory::getDbo();
            $db->setQuery($query);
            
            $db->execute();
            $result['status'] = true;
            $result['msg'] = Text::sprintf('COM_JMM_TABLE_CREATED_SUCCESS_WITH_NAME', $this->getTableName());
            
        } catch (Exception $e) {
            // Log l'erreur pour le débogage
            Factory::getApplication()->enqueueMessage(Text::_('COM_JMM_TABLE_CREATION_ERROR') . ': ' . $e->getMessage(), 'error');
            
            $result['status'] = false;
            $result['msg'] = Text::_('COM_JMM_TABLE_CREATION_ERROR') . ': ' . $e->getMessage();
        }
        
        $this->sendResponse($result);
    }
    
    /**
     * Send JSON response
     *
     * @param   array  $result  The result array
     *
     * @return  void
     */
    private function sendResponse($result)
    {
        $app = Factory::getApplication();
        $input = $app->input;
        
        // Si la requête demande du JSON ou si c'est une requête AJAX
        if ($input->get('format') === 'json' || $input->server->get('HTTP_X_REQUESTED_WITH') === 'XMLHttpRequest') {
            // Format Joomla 5 standard pour les réponses JSON
            $response = array(
                'success' => $result['status'],
                'message' => null,
                'messages' => null,
                'data' => $result
            );
            
            // Définir les en-têtes appropriés
            $app->setHeader('Content-Type', 'application/json; charset=utf-8');
            echo json_encode($response);
            $app->close();
        } else {
            // Redirection normale pour les requêtes non-AJAX
            $this->setRedirect(
                Route::_('index.php?option=com_jmm&view=createTable', false),
                $result['msg'],
                $result['status'] ? 'message' : 'error'
            );
        }
    }

    // Méthodes helper pour createTable (même code que précédemment)
    private function getNull($fieldIndex)
    {
        if (isset($this->posts['field_null'][$fieldIndex])) {
            return ' NULL';
        } else {
            return ' NOT NULL';
        }
    }

    private function getAutoIncrements($fieldIndex)
    {
        if (isset($this->posts['field_extra'][$fieldIndex]) && $this->posts['field_extra'][$fieldIndex] === 'AUTO_INCREMENT') {
            return ' AUTO_INCREMENT';
        }
        return '';
    }
    
    private function getAutoIncrementCounter()
    {
        if (isset($this->posts['field_extra']) && is_array($this->posts['field_extra'])) {
            foreach ($this->posts['field_extra'] as $extra) {
                if ($extra === 'AUTO_INCREMENT') {
                    return ' AUTO_INCREMENT=1';
                }
            }
        }
        return '';
    }
    
    private function getFieldName($fieldIndex)
    {
        $fieldName = trim($this->posts['field_name'][$fieldIndex]);
        return preg_replace('/[^a-zA-Z0-9_]/', '', $fieldName);
    }
    
    private function getFieldLength($fieldIndex)
    {
        if (isset($this->posts['field_length'][$fieldIndex]) && 
            !empty($this->posts['field_length'][$fieldIndex]) && 
            is_numeric($this->posts['field_length'][$fieldIndex]) && 
            $this->posts['field_length'][$fieldIndex] > 0) {
            return '(' . intval($this->posts['field_length'][$fieldIndex]) . ')';
        }
        return '';
    }
    
    private function getFieldType($fieldIndex)
    {
        $allowedTypes = array(
            'TINYINT', 'SMALLINT', 'MEDIUMINT', 'INT', 'BIGINT',
            'DECIMAL', 'FLOAT', 'DOUBLE', 'REAL', 'BIT', 'BOOLEAN', 'SERIAL',
            'DATE', 'DATETIME', 'TIMESTAMP', 'TIME', 'YEAR',
            'CHAR', 'VARCHAR', 'TINYTEXT', 'TEXT', 'MEDIUMTEXT', 'LONGTEXT',
            'BINARY', 'VARBINARY', 'TINYBLOB', 'MEDIUMBLOB', 'BLOB', 'LONGBLOB',
            'ENUM', 'SET',
            'GEOMETRY', 'POINT', 'LINESTRING', 'POLYGON', 'MULTIPOINT',
            'MULTILINESTRING', 'MULTIPOLYGON', 'GEOMETRYCOLLECTION'
        );
        
        $fieldType = strtoupper($this->posts['field_type'][$fieldIndex]);
        
        if (in_array($fieldType, $allowedTypes)) {
            return ' ' . $fieldType;
        }
        return ' VARCHAR';
    }
    
    private function getFieldComments($fieldIndex)
    {
        if (isset($this->posts['field_comments'][$fieldIndex]) && 
            !empty(trim($this->posts['field_comments'][$fieldIndex]))) {
            $comment = $this->posts['field_comments'][$fieldIndex];
            $comment = str_replace("'", "''", $comment);
            return ' COMMENT \'' . $comment . '\'';
        }
        return '';
    }
    
    private function getTableName()
    {
        return $this->posts['tbl_name'];
    }
    
    private function getTableType()
    {
        $allowedEngines = array(
            'MEMORY', 'CSV', 'MRG_MYISAM', 'BLACKHOLE', 'MyISAM', 
            'InnoDB', 'ARCHIVE', 'PERFORMANCE_SCHEMA'
        );
        
        if (!isset($this->posts['tbl_type'])) {
            return 'InnoDB';
        }
        
        $engine = strtoupper($this->posts['tbl_type']);
        
        if (in_array($engine, $allowedEngines)) {
            return $engine;
        }
        return 'InnoDB';
    }
    
    private function getTableComment()
    {
        if (isset($this->posts['tbl_comments']) && !empty(trim($this->posts['tbl_comments']))) {
            $comment = trim($this->posts['tbl_comments']);
            $comment = str_replace("'", "''", $comment);
            return ' COMMENT \'' . $comment . '\'';
        }
        return '';
    }
    
    private function getTableKeys()
    {
        if (!isset($this->posts['field_key']) || !is_array($this->posts['field_key'])) {
            return '';
        }
        
        $keys = '';
        $primaryFields = array();
        $uniqueFields = array();
        $indexFields = array();
        $fulltextFields = array();
        
        foreach ($this->posts['field_key'] as $key => $value) {
            if (empty(trim($this->posts['field_name'][$key]))) {
                continue;
            }
            
            switch ($value) {
                case 'primary':
                    $primaryFields[] = $key;
                    break;
                case 'unique':
                    $uniqueFields[] = $key;
                    break;
                case 'index':
                    $indexFields[] = $key;
                    break;
                case 'fulltext':
                    $fulltextFields[] = $key;
                    break;
            }
        }
        
        if (!empty($primaryFields)) {
            $keys .= ', PRIMARY KEY (' . $this->getFields($primaryFields) . ')';
        }
        
        if (!empty($uniqueFields)) {
            $fieldNames = $this->getFields($uniqueFields);
            $keys .= ', UNIQUE KEY uk_' . substr(md5($fieldNames), 0, 8) . ' (' . $fieldNames . ')';
        }
        
        if (!empty($indexFields)) {
            $fieldNames = $this->getFields($indexFields);
            $keys .= ', KEY idx_' . substr(md5($fieldNames), 0, 8) . ' (' . $fieldNames . ')';
        }
        
        if (!empty($fulltextFields)) {
            $fieldNames = $this->getFields($fulltextFields);
            $keys .= ', FULLTEXT KEY ft_' . substr(md5($fieldNames), 0, 8) . ' (' . $fieldNames . ')';
        }

        return $keys;
    }

    private function getFields($arr)
    {
        $fields = '';
        foreach ($arr as $index) {
            $fieldName = $this->getFieldName($index);
            if (!empty($fieldName)) {
                $fields .= '`' . $fieldName . '`,';
            }
        }
        return rtrim($fields, ',');
    }
}
