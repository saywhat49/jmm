<?php
namespace Saywhat49\Component\Jmm\Administrator\Model;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\Database\DatabaseDriver;
use Saywhat49\Component\Jmm\Administrator\Helper\JmmHelper;

class TablesModel extends ListModel
{
    protected ?string $activeTable = null;
    protected ?string $activeDb = null;
    protected ?string $action = null;

    /**
     * Vrai si la base ou la table demandee dans l'URL n'est pas un identifiant
     * valide. Le modele ne renvoie alors rien, au lieu de se rabattre sur la
     * base ou la table par defaut.
     */
    protected bool $invalidInput = false;

    private ?DatabaseDriver $connection = null;
    private bool $connectionTried = false;
    private ?string $resolvedTable = null;
    private bool $tableResolved = false;

    public function __construct($config = [])
    {
        $dbValid  = true;
        $tblValid = true;

        $this->activeDb    = JmmHelper::requestIdentifier('dbname', $dbValid);
        $this->activeTable = JmmHelper::requestIdentifier('tbl', $tblValid);
        $this->action      = Factory::getApplication()->getInput()->getCmd('action', '');

        if (!$dbValid || !$tblValid) {
            $this->invalidInput = true;
            JmmHelper::reportError(Text::_($dbValid ? 'COM_JMM_INVALID_TABLE_NAME' : 'COM_JMM_ERROR_INVALID_DATABASE_NAME'));
        }

        if (!$this->invalidInput && empty($config['filter_fields']) && !empty($this->activeTable)) {
            $db = $this->connection();

            // Sans connexion, pas de colonnes : surtout pas de repli sur la base par defaut.
            if ($db !== null) {
                $config['filter_fields'] = JmmHelper::getColumnsFromTable($this->activeTable, $db);
            }
        }

        parent::__construct($config);
    }

    /** Connexion vers la base demandee, ouverte une seule fois. Null si elle echoue. */
    private function connection(): ?DatabaseDriver
    {
        if (!$this->connectionTried) {
            $this->connectionTried = true;
            $this->connection      = $this->invalidInput ? null : JmmHelper::tryConnection($this->activeDb);
        }

        return $this->connection;
    }

    /** Nom reel de la table demandee, confronte a SHOW TABLES. Null si elle n'existe pas. */
    private function resolvedTable(): ?string
    {
        if (!$this->tableResolved) {
            $this->tableResolved = true;
            $db                  = $this->connection();

            if ($db !== null && !empty($this->activeTable)) {
                $this->resolvedTable = JmmHelper::resolveTable($this->activeTable, $db);

                if ($this->resolvedTable === null) {
                    JmmHelper::reportError(Text::_('COM_JMM_INVALID_TABLE_NAME'));
                }
            }
        }

        return $this->resolvedTable;
    }

    public function getTables(): array
    {
        $db = $this->connection();

        return $db === null ? [] : JmmHelper::getTablesFromDB($db);
    }

    public function getDatabases(): array
    {
        return JmmHelper::getDataBaseLists();
    }

    public function getTableStructure(): array
    {
        $table = $this->resolvedTable();
        $db    = $this->connection();

        if ($table === null || $db === null) {
            return [];
        }

        try {
            $db->setQuery('DESCRIBE ' . $db->quoteName($table));
            $rows = $db->loadAssocList();

            return is_array($rows) ? $rows : [];
        } catch (\Throwable $e) {
            return [];
        }
    }

    public function getItems()
    {
        if ($this->invalidInput) {
            return [];
        }

        if ($this->action === 'structure') {
            return $this->getTableStructure();
        }

        if ($this->action === 'browse' && $this->resolvedTable() !== null) {
            return parent::getItems();
        }

        $db = $this->connection();

        if ($db === null) {
            return [];
        }

        try {
            $db->setQuery('SHOW TABLE STATUS');
            $rows = $db->loadAssocList();

            return is_array($rows) ? $rows : [];
        } catch (\Throwable $e) {
            return [];
        }
    }

    public function getTotal()
    {
        if ($this->invalidInput || $this->connection() === null) {
            return 0;
        }

        return parent::getTotal();
    }

    protected function getListQuery()
    {
        $db    = $this->connection();
        $table = $this->resolvedTable();

        if ($db === null || $table === null) {
            return Factory::getContainer()->get('DatabaseDriver')->getQuery(true)->select('1');
        }

        $query = $db->getQuery(true)->select('*')->from($db->quoteName($table));

        $orderCol  = $this->getState('list.ordering');
        $orderDirn = strtoupper((string) $this->getState('list.direction', 'ASC')) === 'DESC' ? 'DESC' : 'ASC';

        if (!empty($orderCol)) {
            $allowedCols = JmmHelper::getColumnsFromTable($table, $db);

            if (in_array($orderCol, $allowedCols, true)) {
                $query->order($db->quoteName($orderCol) . ' ' . $orderDirn);
            }
        }

        return $query;
    }

    /**
     * ListModel::_getList renvoie par defaut des objets stdClass. Les vues
     * de com_jmm attendent des tableaux associatifs : elles appellent
     * array_keys() sur la premiere ligne pour construire l'en-tete et
     * accedent aux colonnes par $row['Name']. On force donc loadAssocList.
     */
    protected function _getList($query, $limitstart = 0, $limit = 0)
    {
        $db = $this->getDbo();
        $db->setQuery($query, $limitstart, $limit);

        $rows = $db->loadAssocList();

        return is_array($rows) ? $rows : [];
    }

    public function getDbo()
    {
        $db = $this->connection();

        if ($db === null) {
            throw new \RuntimeException(Text::_('COM_JMM_ERROR_CONNECTION_FAILED'));
        }

        return $db;
    }
}
