<?php
namespace Saywhat49\Component\Jmm\Site\Model;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\Pagination\Pagination;
use Saywhat49\Component\Jmm\Administrator\Helper\JmmHelper;

class TableModel extends BaseDatabaseModel
{
    protected ?object $siteTableDetails = null;
    protected ?int $total = null;
    protected ?Pagination $pagination = null;

    public function getSiteTableDetails(?int $siteTableId = null): ?object
    {
        if ($this->siteTableDetails !== null && $siteTableId === null) {
            return $this->siteTableDetails;
        }

        $app = Factory::getApplication();
        if ($siteTableId === null) {
            $params = $app->getParams('com_jmm');
            $siteTableId = (int) $params->get('site_table_id', 0);
            if (!$siteTableId) {
                $siteTableId = $app->getInput()->getInt('site_table_id', 0);
            }
        }

        if ($siteTableId <= 0) {
            return null;
        }

        $db = Factory::getContainer()->get('DatabaseDriver');
        $query = $db->getQuery(true)
            ->select('*')
            ->from($db->quoteName('#__jmm_sitetables'))
            ->where($db->quoteName('id') . ' = ' . (int) $siteTableId)
            ->where($db->quoteName('published') . ' = 1');

        $db->setQuery($query);
        $this->siteTableDetails = $db->loadObject() ?: null;
        return $this->siteTableDetails;
    }

    public function getTotal(): int
    {
        if ($this->total !== null) {
            return $this->total;
        }

        $siteTable = $this->getSiteTableDetails();
        if (!$siteTable || empty($siteTable->query)) {
            $this->total = 0;
            return 0;
        }

        $rawQuery = rtrim(trim((string) $siteTable->query), " \t\n\r\0\x0B;");
        $db = JmmHelper::getDatabaseConnection($siteTable->dbname ?: null);

        try {
            $countQuery = 'SELECT COUNT(*) FROM (' . $rawQuery . ') AS jmm_cnt';
            $db->setQuery($countQuery);
            $this->total = (int) $db->loadResult();
        } catch (\Throwable $e) {
            $this->total = 0;
        }

        return $this->total;
    }

    public function getPagination(): ?Pagination
    {
        if ($this->pagination === null) {
            $app = Factory::getApplication();
            $params = $app->getParams('com_jmm');
            $limit = (int) $params->get('no_record_per_page', 20);
            if ($limit <= 0) {
                $limit = 20;
            }

            $limitstart = $app->getInput()->getInt('limitstart', 0);
            $this->pagination = new Pagination($this->getTotal(), $limitstart, $limit);
        }

        return $this->pagination;
    }

    public function getItems(): array
    {
        $siteTable = $this->getSiteTableDetails();
        if (!$siteTable || empty($siteTable->query)) {
            return [];
        }

        $rawQuery = rtrim(trim((string) $siteTable->query), " \t\n\r\0\x0B;");
        $db = JmmHelper::getDatabaseConnection($siteTable->dbname ?: null);
        $app = Factory::getApplication();
        $params = $app->getParams('com_jmm');

        $usePagination = (int) $params->get('table_pagination', 1) === 1;
        $query = $rawQuery;

        if ($usePagination) {
            $pagination = $this->getPagination();
            $limit = $pagination->limit;
            $limitstart = $pagination->limitstart;
            $query .= ' LIMIT ' . (int) $limitstart . ', ' . (int) $limit;
        }

        try {
            $db->setQuery($query);
            $rows = $db->loadAssocList();
            return is_array($rows) ? $rows : [];
        } catch (\Throwable $e) {
            return [];
        }
    }

    public function getColumns(): array
    {
        $rows = $this->getItems();
        if (!empty($rows)) {
            return array_keys($rows[0]);
        }
        return [];
    }
}