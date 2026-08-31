<?php
namespace Saywhat49\Component\Jmm\Administrator\Model;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\ListModel;
use Saywhat49\Component\Jmm\Administrator\Helper\JmmHelper;

class TablesModel extends ListModel
{
    protected ?string $activeTable = null;
    protected ?string $activeDb = null;
    protected ?string $action = null;

    public function __construct($config = [])
    {
        $input = Factory::getApplication()->getInput();
        $this->activeDb = JmmHelper::cleanIdentifier($input->getString('dbname', ''));
        $this->activeTable = JmmHelper::cleanIdentifier($input->getString('tbl', ''));
        $this->action = $input->getCmd('action', '');

        if (empty($config['filter_fields']) && !empty($this->activeTable)) {
            $config['filter_fields'] = JmmHelper::getColumnsFromTable($this->activeTable);
        }

        parent::__construct($config);
    }

    public function getTables(): array
    {
        $db = JmmHelper::getDatabaseConnection($this->activeDb);
        return JmmHelper::getTablesFromDB($db);
    }

    public function getDatabases(): array
    {
        return JmmHelper::getDataBaseLists();
    }

    public function getTableStructure(): array
    {
        if (empty($this->activeTable)) {
            return [];
        }

        $db = JmmHelper::getDatabaseConnection($this->activeDb);
        try {
            $db->setQuery('DESCRIBE ' . $db->quoteName($this->activeTable));
            $rows = $db->loadAssocList();
            return is_array($rows) ? $rows : [];
        } catch (\Throwable $e) {
            return [];
        }
    }

    public function getItems()
    {
        if ($this->action === 'structure') {
            return $this->getTableStructure();
        }

        if ($this->action === 'browse' && !empty($this->activeTable)) {
            return parent::getItems();
        }

        $db = JmmHelper::getDatabaseConnection($this->activeDb);
        try {
            $db->setQuery('SHOW TABLE STATUS');
            $rows = $db->loadAssocList();
            return is_array($rows) ? $rows : [];
        } catch (\Throwable $e) {
            return [];
        }
    }

    protected function getListQuery()
    {
        $db = JmmHelper::getDatabaseConnection($this->activeDb);
        $query = $db->getQuery(true);

        if (empty($this->activeTable)) {
            $query->select('1');
            return $query;
        }

        $query->select('*')->from($db->quoteName($this->activeTable));

        $orderCol = $this->getState('list.ordering');
        $orderDirn = strtoupper($this->getState('list.direction', 'ASC')) === 'DESC' ? 'DESC' : 'ASC';

        if (!empty($orderCol)) {
            $allowedCols = JmmHelper::getColumnsFromTable($this->activeTable, $db);
            if (in_array($orderCol, $allowedCols, true)) {
                $query->order($db->quoteName($orderCol) . ' ' . $orderDirn);
            }
        }

        return $query;
    }

    public function getDbo()
    {
        return JmmHelper::getDatabaseConnection($this->activeDb);
    }
}