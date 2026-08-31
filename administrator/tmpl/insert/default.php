<?php
defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

?>
<form action="<?php echo Route::_('index.php?option=com_jmm&view=insert'); ?>" method="post" name="adminForm" id="adminForm">
    <div class="row">
        <div class="col-md-12">
            <div id="j-main-container" class="j-main-container">
                <div class="card mb-3 shadow-sm">
                    <div class="card-body">
                        <div class="row g-3 align-items-center">
                            <div class="col-md-4">
                                <label for="insert_dbname" class="form-label fw-bold"><?php echo Text::_('COM_JMM_SELECT_DATABASE'); ?>:</label>
                                <select name="dbname" id="insert_dbname" class="form-select" onchange="this.form.submit();">
                                    <option value=""><?php echo Text::_('COM_JMM_DEFAULT_DATABASE'); ?></option>
                                    <?php foreach ($this->databases as $dbName): ?>
                                        <option value="<?php echo $this->escape($dbName); ?>" <?php echo $this->selectedDb === $dbName ? 'selected' : ''; ?>>
                                            <?php echo $this->escape($dbName); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="insert_tbl" class="form-label fw-bold"><?php echo Text::_('COM_JMM_SELECT_TABLE'); ?>:</label>
                                <select name="tbl" id="insert_tbl" class="form-select" onchange="this.form.submit();">
                                    <option value=""><?php echo Text::_('COM_JMM_SELECT_TABLE'); ?></option>
                                    <?php foreach ($this->tables as $tableName): ?>
                                        <option value="<?php echo $this->escape($tableName); ?>" <?php echo $this->selectedTable === $tableName ? 'selected' : ''; ?>>
                                            <?php echo $this->escape($tableName); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <?php if (!empty($this->selectedTable)): ?>
                    <div class="card shadow-sm mb-3">
                        <div class="card-header bg-light fw-bold">
                            <?php echo Text::sprintf('COM_JMM_INSERT_ROW_INTO_TABLE', $this->escape($this->selectedTable)); ?>
                        </div>
                        <div class="card-body">
                            <?php
                            $field = new \Joomla\Component\Jmm\Administrator\Field\TablefieldsField();
                            echo $field->renderField();
                            ?>
                            <div class="mt-3">
                                <button type="submit" class="btn btn-success" onclick="document.adminForm.task.value='insert.save';">
                                    <span class="icon-check" aria-hidden="true"></span> <?php echo Text::_('COM_JMM_SAVE_ROW'); ?>
                                </button>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">
                        <?php echo Text::_('COM_JMM_SELECT_TABLE_BEFORE_INSERT'); ?>
                    </div>
                <?php endif; ?>

                <input type="hidden" name="task" value="">
                <?php echo HTMLHelper::_('form.token'); ?>
            </div>
        </div>
    </div>
</form>