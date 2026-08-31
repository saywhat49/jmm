<?php
namespace Saywhat49\Component\Jmm\Administrator\Model;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Saywhat49\Component\Jmm\Administrator\Helper\JmmHelper;

class SqlModel extends BaseDatabaseModel
{
    public function executeQuery(string $query, ?string $dbname = null): array
    {
        $query = trim($query);
        if ($query === '') {
            return ['status' => false, 'msg' => Text::_('COM_JMM_NO_QUERY_PROVIDED'), 'rows' => [], 'time' => 0, 'total' => 0];
        }

        $db = JmmHelper::getDatabaseConnection($dbname);
        $startTime = microtime(true);

        try {
            $db->setQuery($query);
            
            if (preg_match('/^\s*(SELECT|SHOW|DESCRIBE|DESC|EXPLAIN)\s/i', $query)) {
                $rows = $db->loadAssocList();
                $rows = is_array($rows) ? $rows : [];
                $elapsed = round(microtime(true) - $startTime, 4);
                $total = count($rows);

                return [
                    'status' => true,
                    'msg'    => Text::sprintf('COM_JMM_QUERY_EXECUTED_SUCCESS', $total, $elapsed),
                    'rows'   => $rows,
                    'time'   => $elapsed,
                    'total'  => $total,
                ];
            }

            $db->execute();
            $elapsed = round(microtime(true) - $startTime, 4);
            $affected = $db->getAffectedRows();

            return [
                'status' => true,
                'msg'    => Text::sprintf('COM_JMM_STATEMENT_EXECUTED_AFFECTED', $affected, $elapsed),
                'rows'   => [],
                'time'   => $elapsed,
                'total'  => $affected,
            ];
        } catch (\Throwable $e) {
            $elapsed = round(microtime(true) - $startTime, 4);
            return [
                'status' => false,
                'msg'    => $e->getMessage(),
                'rows'   => [],
                'time'   => $elapsed,
                'total'  => 0,
            ];
        }
    }

    public function saveCannedQuery(array $data): array
    {
        $table = $this->getTable('Cannedquery', 'Administrator');
        if (!$table->bind($data)) {
            return ['status' => false, 'msg' => $table->getError()];
        }
        if (!$table->check()) {
            return ['status' => false, 'msg' => $table->getError()];
        }
        if (!$table->store()) {
            return ['status' => false, 'msg' => $table->getError()];
        }

        return [
            'status' => true,
            'msg'    => Text::_('COM_JMM_CANNED_QUERY_SAVED_SUCCESSFULLY'),
            'data'   => [
                'id'    => $table->id,
                'title' => $table->title,
                'query' => $table->query,
            ],
        ];
    }

    public function saveSiteTable(array $data): array
    {
        $table = $this->getTable('Sitetable', 'Administrator');
        if (!$table->bind($data)) {
            return ['status' => false, 'msg' => $table->getError()];
        }
        if (!$table->check()) {
            return ['status' => false, 'msg' => $table->getError()];
        }
        if (!$table->store()) {
            return ['status' => false, 'msg' => $table->getError()];
        }

        return [
            'status' => true,
            'msg'    => Text::_('COM_JMM_SITE_TABLE_SAVED_SUCCESSFULLY'),
            'data'   => [
                'id'    => $table->id,
                'title' => $table->title,
                'query' => $table->query,
            ],
        ];
    }

    public function getCannedQueries(?string $dbname = null): array
    {
        $db = Factory::getContainer()->get('DatabaseDriver');
        $query = $db->getQuery(true)
            ->select($db->quoteName(['id', 'title', 'query', 'dbname']))
            ->from($db->quoteName('#__jmm_canned_queries'))
            ->where($db->quoteName('published') . ' = 1')
            ->order($db->quoteName('id') . ' DESC');

        if (!empty($dbname)) {
            $query->where($db->quoteName('dbname') . ' = ' . $db->quote($dbname));
        }

        $db->setQuery($query);
        $rows = $db->loadObjectList();
        return is_array($rows) ? $rows : [];
    }

    public function getSiteTables(?string $dbname = null): array
    {
        $db = Factory::getContainer()->get('DatabaseDriver');
        $query = $db->getQuery(true)
            ->select($db->quoteName(['id', 'title', 'query', 'dbname']))
            ->from($db->quoteName('#__jmm_sitetables'))
            ->where($db->quoteName('published') . ' = 1')
            ->order($db->quoteName('id') . ' DESC');

        if (!empty($dbname)) {
            $query->where($db->quoteName('dbname') . ' = ' . $db->quote($dbname));
        }

        $db->setQuery($query);
        $rows = $db->loadObjectList();
        return is_array($rows) ? $rows : [];
    }
}