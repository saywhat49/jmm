<?php
defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Saywhat49\Component\Jmm\Administrator\Helper\JmmHelper;

$columnTypes = JmmHelper::getColumnTypes();

// Le script a besoin de savoir quelle forme de longueur chaque type accepte.
$this->getDocument()->addScriptOptions('com_jmm.columnTypes', $columnTypes);
$this->getDocument()->addScriptOptions('com_jmm.createtable', [
    'lengthNone'    => Text::_('COM_JMM_LENGTH_NOT_APPLICABLE'),
    'lengthDecimal' => Text::_('COM_JMM_LENGTH_DECIMAL_HINT'),
    'lengthFsp'     => Text::_('COM_JMM_LENGTH_FSP_HINT'),
    'lengthInt'     => Text::_('COM_JMM_LENGTH_HINT'),
]);

/** Options du selecteur de type, partagees par les deux lignes de depart. */
$typeOptions = static function (string $selected) use ($columnTypes): string {
    $html = '';
    foreach (array_keys($columnTypes) as $type) {
        $html .= '<option value="' . $type . '"' . ($type === $selected ? ' selected' : '') . '>' . $type . '</option>';
    }
    return $html;
};
?>
<form action="<?php echo Route::_('index.php?option=com_jmm&task=createtable.createTableStructure'); ?>" method="post" name="adminForm" id="adminForm">
    <div class="row">
        <div class="col-md-12">
            <div id="j-main-container" class="j-main-container">
                <div class="card mb-3 shadow-sm">
                    <div class="card-header bg-light fw-bold"><?php echo Text::_('COM_JMM_TABLE_PROPERTIES'); ?></div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="createtable_dbname" class="form-label fw-bold"><?php echo Text::_('COM_JMM_SELECT_DATABASE'); ?>:</label>
                                <select name="dbname" id="createtable_dbname" class="form-select">
                                    <option value=""><?php echo Text::_('COM_JMM_DEFAULT_DATABASE'); ?></option>
                                    <?php foreach ($this->databases as $dbName): ?>
                                        <option value="<?php echo $this->escape($dbName); ?>" <?php echo $this->currentDb === $dbName ? 'selected' : ''; ?>>
                                            <?php echo $this->escape($dbName); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="tbl_name" class="form-label fw-bold"><?php echo Text::_('COM_JMM_TABLE_NAME'); ?> <span class="text-danger">*</span>:</label>
                                <input type="text" name="tbl_name" id="tbl_name" class="form-control" placeholder="my_new_table" required pattern="[A-Za-z0-9_]+">
                            </div>
                            <div class="col-md-4">
                                <label for="tbl_type" class="form-label fw-bold"><?php echo Text::_('COM_JMM_STORAGE_ENGINE'); ?>:</label>
                                <select name="tbl_type" id="tbl_type" class="form-select">
                                    <option value="InnoDB" selected>InnoDB</option>
                                    <option value="MyISAM">MyISAM</option>
                                    <option value="MEMORY">MEMORY</option>
                                    <option value="CSV">CSV</option>
                                    <option value="ARCHIVE">ARCHIVE</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label for="tbl_comments" class="form-label"><?php echo Text::_('COM_JMM_TABLE_COMMENTS'); ?>:</label>
                                <input type="text" name="tbl_comments" id="tbl_comments" class="form-control" placeholder="<?php echo Text::_('COM_JMM_OPTIONAL_COMMENT'); ?>">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-3 shadow-sm">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                        <span class="fw-bold"><?php echo Text::_('COM_JMM_COLUMNS_DEFINITION'); ?></span>
                        <button type="button" class="btn btn-sm btn-primary" id="btn_add_column">
                            <span class="icon-plus" aria-hidden="true"></span> <?php echo Text::_('COM_JMM_ADD_COLUMN'); ?>
                        </button>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped align-middle mb-0" id="columns_table">
                                <thead class="table-light">
                                    <tr>
                                        <th style="min-width: 160px;"><?php echo Text::_('COM_JMM_COLUMN_NAME'); ?></th>
                                        <th style="min-width: 140px;"><?php echo Text::_('COM_JMM_TYPE'); ?></th>
                                        <th style="width: 100px;"><?php echo Text::_('COM_JMM_LENGTH'); ?></th>
                                        <th style="width: 90px;" class="text-center"><?php echo Text::_('COM_JMM_NULLABLE'); ?></th>
                                        <th style="width: 140px;"><?php echo Text::_('COM_JMM_KEY'); ?></th>
                                        <th style="width: 120px;" class="text-center"><?php echo Text::_('COM_JMM_AUTO_INC'); ?></th>
                                        <th><?php echo Text::_('COM_JMM_COMMENTS'); ?></th>
                                        <th style="width: 60px;" class="text-center"><?php echo Text::_('COM_JMM_ACTION'); ?></th>
                                    </tr>
                                </thead>
                                <tbody id="columns_tbody">
                                    <tr class="jmm-col-row">
                                        <td><input type="text" name="field_name[0]" class="form-control" value="id" required></td>
                                        <td>
                                            <select name="field_type[0]" class="form-select jmm-type-select"><?php echo $typeOptions('INT'); ?></select>
                                        </td>
                                        <td><input type="text" name="field_length[0]" class="form-control jmm-length-input" value="11"></td>
                                        <td class="text-center"><input type="checkbox" name="field_null[0]" class="form-check-input" value="1"></td>
                                        <td>
                                            <select name="field_key[0]" class="form-select">
                                                <option value="primary" selected>PRIMARY</option>
                                                <option value="unique">UNIQUE</option>
                                                <option value="index">INDEX</option>
                                                <option value="none">---</option>
                                            </select>
                                        </td>
                                        <td class="text-center"><input type="checkbox" name="field_extra[0]" class="form-check-input" value="AUTO_INCREMENT" checked></td>
                                        <td><input type="text" name="field_comments[0]" class="form-control"></td>
                                        <td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger btn-remove-row" disabled><span class="icon-trash" aria-hidden="true"></span></button></td>
                                    </tr>
                                    <tr class="jmm-col-row">
                                        <td><input type="text" name="field_name[1]" class="form-control" value="title" required></td>
                                        <td>
                                            <select name="field_type[1]" class="form-select jmm-type-select"><?php echo $typeOptions('VARCHAR'); ?></select>
                                        </td>
                                        <td><input type="text" name="field_length[1]" class="form-control jmm-length-input" value="255"></td>
                                        <td class="text-center"><input type="checkbox" name="field_null[1]" class="form-check-input" value="1"></td>
                                        <td>
                                            <select name="field_key[1]" class="form-select">
                                                <option value="none" selected>---</option>
                                                <option value="primary">PRIMARY</option>
                                                <option value="unique">UNIQUE</option>
                                                <option value="index">INDEX</option>
                                            </select>
                                        </td>
                                        <td class="text-center"><input type="checkbox" name="field_extra[1]" class="form-check-input" value="AUTO_INCREMENT"></td>
                                        <td><input type="text" name="field_comments[1]" class="form-control"></td>
                                        <td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger btn-remove-row"><span class="icon-trash" aria-hidden="true"></span></button></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-success btn-lg">
                        <span class="icon-check" aria-hidden="true"></span> <?php echo Text::_('COM_JMM_CREATE_TABLE_SUBMIT'); ?>
                    </button>
                </div>

                <input type="hidden" name="task" value="createtable.createTableStructure">
                <?php echo HTMLHelper::_('form.token'); ?>
            </div>
        </div>
    </div>
</form>