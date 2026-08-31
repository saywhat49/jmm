<?php
namespace Saywhat49\Component\Jmm\Administrator\Model;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Model\ListModel;

class CannedqueriesModel extends ListModel
{
    public function __construct($config = [])
    {
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = ['id', 'title', 'dbname', 'query', 'datetime', 'published'];
        }
        parent::__construct($config);
    }

    protected function getListQuery()
    {
        $db = $this->getDbo();
        $query = $db->getQuery(true)
            ->select($db->quoteName(['id', 'title', 'dbname', 'query', 'datetime', 'published']))
            ->from($db->quoteName('#__jmm_canned_queries'));

        $published = $this->getState('filter.published');
        if (is_numeric($published)) {
            $query->where($db->quoteName('published') . ' = ' . (int) $published);
        } elseif ($published === '') {
            $query->where($db->quoteName('published') . ' IN (0, 1)');
        }

        $search = $this->getState('filter.search');
        if (!empty($search)) {
            $searchVal = '%' . $db->escape($search, true) . '%';
            $query->where(
                '(' . $db->quoteName('title') . ' LIKE ' . $db->quote($searchVal)
                . ' OR ' . $db->quoteName('dbname') . ' LIKE ' . $db->quote($searchVal)
                . ' OR ' . $db->quoteName('query') . ' LIKE ' . $db->quote($searchVal) . ')'
            );
        }

        $database = $this->getState('filter.database');
        if (!empty($database)) {
            $query->where($db->quoteName('dbname') . ' = ' . $db->quote($database));
        }

        $orderCol = $this->state->get('list.ordering', 'id');
        $orderDirn = strtoupper($this->state->get('list.direction', 'DESC')) === 'ASC' ? 'ASC' : 'DESC';

        if (in_array($orderCol, $this->filter_fields, true)) {
            $query->order($db->quoteName($orderCol) . ' ' . $orderDirn);
        } else {
            $query->order($db->quoteName('id') . ' DESC');
        }

        return $query;
    }

    protected function populateState($ordering = 'id', $direction = 'desc')
    {
        $search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
        $this->setState('filter.search', $search);

        $published = $this->getUserStateFromRequest($this->context . '.filter.published', 'filter_published', '');
        $this->setState('filter.published', $published);

        $database = $this->getUserStateFromRequest($this->context . '.filter.database', 'filter_database', '');
        $this->setState('filter.database', $database);

        parent::populateState($ordering, $direction);
    }
}