<?php
namespace Saywhat49\Component\Jmm\Administrator\Model;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Model\ListModel;
use Saywhat49\Component\Jmm\Administrator\Helper\JmmHelper;

class DatabasesModel extends ListModel
{
    public function __construct($config = [])
    {
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = ['name'];
        }
        parent::__construct($config);
    }

    public function getItems()
    {
        $databases = JmmHelper::getDataBaseLists();
        $search = $this->getState('filter.search');

        if (!empty($search)) {
            $search = strtolower($search);
            $databases = array_filter($databases, function ($db) use ($search) {
                return str_contains(strtolower($db), $search);
            });
        }

        $items = [];
        foreach ($databases as $dbName) {
            $items[] = (object) ['name' => $dbName];
        }

        return $items;
    }

    public function getTotal()
    {
        return count($this->getItems());
    }

    protected function populateState($ordering = 'name', $direction = 'asc')
    {
        $search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
        $this->setState('filter.search', $search);
        parent::populateState($ordering, $direction);
    }
}