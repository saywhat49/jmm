<?php
namespace Saywhat49\Component\Jmm\Administrator\Field;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Form\FormField;
use Saywhat49\Component\Jmm\Administrator\Helper\JmmHelper;

class TablefieldsField extends FormField
{
    protected $type = 'Tablefields';

    protected function getInput(): string
    {
        $input = Factory::getApplication()->getInput();
        $table = JmmHelper::cleanIdentifier($input->getString('tbl', ''));
        $dbname = $input->getString('dbname', '');

        if ($table === '') {
            return '<div class="alert alert-info">' . Factory::getApplication()->getLanguage()->_('COM_JMM_SELECT_TABLE_FIRST') . '</div>';
        }

        $db = JmmHelper::getDatabaseConnection($dbname);
        try {
            $db->setQuery('DESCRIBE ' . $db->quoteName($table));
            $columns = $db->loadAssocList();
        } catch (\Throwable $e) {
            return '<div class="alert alert-danger">' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') . '</div>';
        }

        if (empty($columns)) {
            return '<div class="alert alert-warning">' . Factory::getApplication()->getLanguage()->_('COM_JMM_NO_COLUMNS_FOUND') . '</div>';
        }

        $html = '<div class="table-responsive"><table class="table table-bordered table-striped align-middle">';
        $html .= '<thead><tr><th>' . Factory::getApplication()->getLanguage()->_('COM_JMM_FIELD') . '</th><th>' . Factory::getApplication()->getLanguage()->_('COM_JMM_TYPE') . '</th><th>' . Factory::getApplication()->getLanguage()->_('COM_JMM_VALUE') . '</th></tr></thead><tbody>';

        foreach ($columns as $col) {
            $fieldName = $col['Field'];
            $fieldType = strtolower($col['Type']);
            $isExtraAuto = str_contains(strtolower($col['Extra'] ?? ''), 'auto_increment');
            $safeName = htmlspecialchars($fieldName, ENT_QUOTES, 'UTF-8');
            $safeType = htmlspecialchars($col['Type'], ENT_QUOTES, 'UTF-8');

            $html .= '<tr>';
            $html .= '<td><strong>' . $safeName . '</strong>' . ($isExtraAuto ? ' <span class="badge bg-secondary">AUTO</span>' : '') . '</td>';
            $html .= '<td><span class="badge bg-light text-dark">' . $safeType . '</span></td>';
            $html .= '<td>';

            if ($isExtraAuto) {
                $html .= '<input type="text" class="form-control" placeholder="(Auto Increment)" disabled>';
            } elseif (str_contains($fieldType, 'text') || str_contains($fieldType, 'blob') || str_contains($fieldType, 'json')) {
                $html .= '<textarea name="jform[' . $safeName . ']" class="form-control" rows="3"></textarea>';
            } elseif (str_contains($fieldType, 'date') || str_contains($fieldType, 'time')) {
                $html .= '<input type="text" name="jform[' . $safeName . ']" class="form-control font-monospace" placeholder="YYYY-MM-DD HH:MM:SS">';
            } else {
                $html .= '<input type="text" name="jform[' . $safeName . ']" class="form-control">';
            }

            $html .= '</td></tr>';
        }

        $html .= '</tbody></table></div>';
        return $html;
    }
}