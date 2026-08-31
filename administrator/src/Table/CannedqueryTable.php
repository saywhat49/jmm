<?php
namespace Saywhat49\Component\Jmm\Administrator\Table;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Table\Table;
use Joomla\Database\DatabaseDriver;

class CannedqueryTable extends Table
{
    public function __construct(DatabaseDriver $db)
    {
        parent::__construct('#__jmm_canned_queries', 'id', $db);
    }

    public function check(): bool
    {
        $this->title = trim((string) $this->title);
        if ($this->title === '') {
            $this->setError(Text::_('COM_JMM_ERROR_TITLE_REQUIRED'));
            return false;
        }

        $this->query = trim((string) $this->query);
        if ($this->query === '') {
            $this->setError(Text::_('COM_JMM_ERROR_QUERY_REQUIRED'));
            return false;
        }

        if (empty($this->datetime)) {
            $this->datetime = Factory::getDate()->toSql();
        }

        return true;
    }
}