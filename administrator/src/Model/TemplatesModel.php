<?php
namespace Saywhat49\Component\Jmm\Administrator\Model;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Model\ListModel;

class TemplatesModel extends ListModel
{
    public function __construct($config = [])
    {
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = ['id', 'title', 'datetime', 'published'];
        }
        parent::__construct($config);
    }

    protected function getListQuery()
    {
        $db = $this->getDbo();
        $query = $db->getQuery(true)
            ->select($db->quoteName(['id', 'title', 'datetime', 'published']))
            ->from($db->quoteName('#__jmm_templates'));

        $published = $this->getState('filter.published');
        if (is_numeric($published)) {
            $query->where($db->quoteName('published') . ' = ' . (int) $published);
        } elseif ($published === '') {
            $query->where($db->quoteName('published') . ' IN (0, 1)');
        }

        $search = $this->getState('filter.search');
        if (!empty($search)) {
            $searchVal = '%' . $db->escape($search, true) . '%';
            $query->where($db->quoteName('title') . ' LIKE ' . $db->quote($searchVal));
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

        parent::populateState($ordering, $direction);
    }
}