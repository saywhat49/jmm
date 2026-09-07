<?php
namespace Saywhat49\Component\Jmm\Administrator\Table;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Filter\OutputFilter;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Table\Table;
use Joomla\Database\DatabaseDriver;

class TemplateTable extends Table
{
    public function __construct(DatabaseDriver $db)
    {
        parent::__construct('#__jmm_templates', 'id', $db);
    }

    public function check(): bool
    {
        $this->title = trim((string) $this->title);
        if ($this->title === '') {
            $this->setError(Text::_('COM_JMM_ERROR_TEMPLATE_TITLE_REQUIRED'));
            return false;
        }

        $this->title = OutputFilter::stringURLSafe($this->title);
        if ($this->title === '') {
            $this->setError(Text::_('COM_JMM_ERROR_INVALID_TEMPLATE_TITLE'));
            return false;
        }

        // Sanitize layout_type whitelist
        $allowedLayouts = ['table', 'cards', 'chart'];
        if (empty($this->layout_type) || !in_array($this->layout_type, $allowedLayouts, true)) {
            $this->layout_type = 'table';
        }

        // Sanitize chart_type whitelist
        $allowedCharts = ['PieChart', 'ColumnChart', 'BarChart'];
        if (empty($this->chart_type) || !in_array($this->chart_type, $allowedCharts, true)) {
            $this->chart_type = 'PieChart';
        }

        // Sanitize CSS: strip PHP tags and script tags
        if (!empty($this->custom_css)) {
            $this->custom_css = preg_replace('/<\?(?:php)?|<\/?script[^>]*>/i', '', (string) $this->custom_css);
        }

        if (empty($this->datetime)) {
            $this->datetime = Factory::getDate()->toSql();
        }

        return true;
    }
}