<?php
defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

$listOrder = $this->escape($this->state->get('list.ordering', ''));
$orderDirn = $this->escape($this->state->get('list.direction', 'ASC'));
?>
<form action="<?php echo Route::_('index.php?option=com_jmm&view=tables'); ?>" method="post" name="adminForm" id="adminForm">
    <div class="row">
        <div class="col-md-12">
            <div id="j-main-container" class="j-main-container">
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="row g-3 align-items-center">
                            <div class="col-md-4">
                                <label for="filter_dbname" class="form-label fw-bold"><?php echo Text::_('COM_JMM_SELECT_DATABASE'); ?>:</label>
                                <select name="dbname" id="filter_dbname" class="form-select" onchange="this.form.submit();">
                                    <option value=""><?php echo Text::_('COM_JMM_DEFAULT_DATABASE'); ?></option>
                                    <?php foreach ($this->databases as $dbName): ?>
                                        <option value="<?php echo $this->escape($dbName); ?>" <?php echo $this->activeDb === $dbName ? 'selected' : ''; ?>>
                                            <?php echo $this->escape($dbName); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="filter_tbl" class="form-label fw-bold"><?php echo Text::_('COM_JMM_SELECT_TABLE'); ?>:</label>
                                <select name="tbl" id="filter_tbl" class="form-select" onchange="this.form.submit();">
                                    <option value=""><?php echo Text::_('COM_JMM_SELECT_TABLE'); ?></option>
                                    <?php foreach ($this->tables as $tableName): ?>
                                        <option value="<?php echo $this->escape($tableName); ?>" <?php echo $this->activeTable === $tableName ? 'selected' : ''; ?>>
                                            <?php echo $this->escape($tableName); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-4 text-end align-self-end">
                                <?php if (!empty($this->activeTable)): ?>
                                    <a href="<?php echo Route::_('index.php?option=com_jmm&view=tables&action=browse&tbl=' . urlencode($this->activeTable) . '&dbname=' . urlencode($this->activeDb)); ?>" class="btn <?php echo $this->action === 'browse' ? 'btn-primary' : 'btn-outline-primary'; ?>">
                                        <span class="icon-eye" aria-hidden="true"></span> <?php echo Text::_('COM_JMM_BROWSE'); ?>
                                    </a>
                                    <a href="<?php echo Route::_('index.php?option=com_jmm&view=tables&action=structure&tbl=' . urlencode($this->activeTable) . '&dbname=' . urlencode($this->activeDb)); ?>" class="btn <?php echo $this->action === 'structure' ? 'btn-info text-white' : 'btn-outline-info'; ?>">
                                        <span class="icon-list-view" aria-hidden="true"></span> <?php echo Text::_('COM_JMM_STRUCTURE'); ?>
                                    </a>
                                    <a href="<?php echo Route::_('index.php?option=com_jmm&view=insert&tbl=' . urlencode($this->activeTable) . '&dbname=' . urlencode($this->activeDb)); ?>" class="btn btn-outline-success">
                                        <span class="icon-plus" aria-hidden="true"></span> <?php echo Text::_('COM_JMM_INSERT'); ?>
                                    </a>
                                    <button type="button" class="btn btn-outline-secondary jmm-export-btn" data-query="SELECT * FROM <?php echo $this->escape($this->activeTable); ?>" data-filename="<?php echo $this->escape($this->activeTable); ?>" data-dbname="<?php echo $this->escape($this->activeDb); ?>">
                                        <span class="icon-download" aria-hidden="true"></span> <?php echo Text::_('COM_JMM_EXPORT_CSV'); ?>
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <?php if (!empty($this->items) && is_array($this->items)): ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover table-bordered align-middle">
                            <thead class="table-light">
                                <tr>
                                    <?php foreach (array_keys($this->items[0]) as $colHeader): ?>
                                        <th scope="col">
                                            <?php if ($this->action === 'browse'): ?>
                                                <?php echo HTMLHelper::_('grid.sort', $this->escape($colHeader), $colHeader, $orderDirn, $listOrder); ?>
                                            <?php else: ?>
                                                <?php echo $this->escape($colHeader); ?>
                                            <?php endif; ?>
                                        </th>
                                    <?php endforeach; ?>
                                    <?php if ($this->action !== 'browse' && $this->action !== 'structure'): ?>
                                        <th scope="col" class="text-end" style="width: 280px;"><?php echo Text::_('COM_JMM_ACTIONS'); ?></th>
                                    <?php endif; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($this->items as $i => $row): ?>
                                    <tr>
                                        <?php foreach ($row as $colName => $colVal): ?>
                                            <td><?php echo htmlspecialchars((string) $colVal, ENT_QUOTES, 'UTF-8'); ?></td>
                                        <?php endforeach; ?>
                                        <?php if ($this->action !== 'browse' && $this->action !== 'structure'): ?>
                                            <?php $tName = $row['Name'] ?? ''; ?>
                                            <td class="text-end">
                                                <a href="<?php echo Route::_('index.php?option=com_jmm&view=tables&action=browse&tbl=' . urlencode($tName) . '&dbname=' . urlencode($this->activeDb)); ?>" class="btn btn-sm btn-outline-primary">
                                                    <span class="icon-eye" aria-hidden="true"></span> <?php echo Text::_('COM_JMM_BROWSE'); ?>
                                                </a>
                                                <a href="<?php echo Route::_('index.php?option=com_jmm&view=tables&action=structure&tbl=' . urlencode($tName) . '&dbname=' . urlencode($this->activeDb)); ?>" class="btn btn-sm btn-outline-info">
                                                    <span class="icon-list-view" aria-hidden="true"></span> <?php echo Text::_('COM_JMM_STRUCTURE'); ?>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-outline-secondary jmm-export-btn" data-query="SELECT * FROM <?php echo $this->escape($tName); ?>" data-filename="<?php echo $this->escape($tName); ?>" data-dbname="<?php echo $this->escape($this->activeDb); ?>">
                                                    <span class="icon-download" aria-hidden="true"></span>
                                                </button>
                                            </td>
                                        <?php endif; ?>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <?php if ($this->action === 'browse' && $this->pagination): ?>
                                <tfoot>
                                    <tr>
                                        <td colspan="<?php echo count(array_keys($this->items[0])); ?>">
                                            <?php echo $this->pagination->getListFooter(); ?>
                                        </td>
                                    </tr>
                                </tfoot>
                            <?php endif; ?>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">
                        <?php echo Text::_('COM_JMM_NO_TABLE_DATA_TO_DISPLAY'); ?>
                    </div>
                <?php endif; ?>

                <input type="hidden" name="task" value="">
                <input type="hidden" name="action" value="<?php echo $this->escape($this->action); ?>">
                <input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>">
                <input type="hidden" name="filter_order_Dir" value="<?php echo $orderDirn; ?>">
                <?php echo HTMLHelper::_('form.token'); ?>
            </div>
        </div>
    </div>
</form>