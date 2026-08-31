<?php
defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

$listOrder = $this->escape($this->state->get('list.ordering', 'id'));
$listDirn  = $this->escape($this->state->get('list.direction', 'DESC'));
?>
<form action="<?php echo Route::_('index.php?option=com_jmm&view=sitetables'); ?>" method="post" name="adminForm" id="adminForm">
    <div class="row">
        <div class="col-md-12">
            <div id="j-main-container" class="j-main-container">
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="row g-2 align-items-center">
                            <div class="col-auto">
                                <div class="input-group">
                                    <input type="text" name="filter_search" id="filter_search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" class="form-control" placeholder="<?php echo Text::_('JSEARCH_FILTER'); ?>">
                                    <button type="submit" class="btn btn-primary"><span class="icon-search" aria-hidden="true"></span></button>
                                    <button type="button" class="btn btn-secondary" onclick="document.getElementById('filter_search').value='';this.form.submit();"><span class="icon-times" aria-hidden="true"></span></button>
                                </div>
                            </div>
                            <div class="col-auto">
                                <select name="filter_published" class="form-select" onchange="this.form.submit();">
                                    <option value=""><?php echo Text::_('JOPTION_SELECT_PUBLISHED'); ?></option>
                                    <?php echo HTMLHelper::_('select.options', HTMLHelper::_('jgrid.publishedOptions'), 'value', 'text', $this->state->get('filter.published'), true); ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th scope="col" style="width: 30px;">
                                    <?php echo HTMLHelper::_('grid.checkall'); ?>
                                </th>
                                <th scope="col" style="width: 50px;">
                                    <?php echo HTMLHelper::_('grid.sort', 'COM_JMM_ID', 'id', $listDirn, $listOrder); ?>
                                </th>
                                <th scope="col">
                                    <?php echo HTMLHelper::_('grid.sort', 'COM_JMM_TITLE', 'title', $listDirn, $listOrder); ?>
                                </th>
                                <th scope="col" style="width: 150px;">
                                    <?php echo HTMLHelper::_('grid.sort', 'COM_JMM_DATABASE', 'dbname', $listDirn, $listOrder); ?>
                                </th>
                                <th scope="col">
                                    <?php echo Text::_('COM_JMM_QUERY'); ?>
                                </th>
                                <th scope="col" style="width: 160px;">
                                    <?php echo HTMLHelper::_('grid.sort', 'COM_JMM_DATETIME', 'datetime', $listDirn, $listOrder); ?>
                                </th>
                                <th scope="col" class="text-center" style="width: 80px;">
                                    <?php echo HTMLHelper::_('grid.sort', 'JSTATUS', 'published', $listDirn, $listOrder); ?>
                                </th>
                                <th scope="col" class="text-end" style="width: 120px;">
                                    <?php echo Text::_('COM_JMM_ACTIONS'); ?>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($this->items)): ?>
                                <?php foreach ($this->items as $i => $item): ?>
                                    <tr>
                                        <td>
                                            <?php echo HTMLHelper::_('grid.id', $i, $item->id); ?>
                                        </td>
                                        <td><?php echo (int) $item->id; ?></td>
                                        <td>
                                            <a href="<?php echo Route::_('index.php?option=com_jmm&task=sitetable.edit&id=' . (int) $item->id); ?>" class="fw-bold text-decoration-none">
                                                <?php echo $this->escape($item->title); ?>
                                            </a>
                                        </td>
                                        <td><?php echo $this->escape($item->dbname ?: Text::_('COM_JMM_DEFAULT')); ?></td>
                                        <td>
                                            <code class="text-break"><?php echo htmlspecialchars(mb_strimwidth($item->query, 0, 120, '...'), ENT_QUOTES, 'UTF-8'); ?></code>
                                        </td>
                                        <td><?php echo $this->escape($item->datetime); ?></td>
                                        <td class="text-center">
                                            <?php echo HTMLHelper::_('jgrid.published', $item->published, $i, 'sitetables.', true, 'cb'); ?>
                                        </td>
                                        <td class="text-end">
                                            <a href="<?php echo Route::_('index.php?option=com_jmm&view=sql&query=' . urlencode($item->query) . '&dbname=' . urlencode($item->dbname)); ?>" class="btn btn-sm btn-outline-primary" title="<?php echo Text::_('COM_JMM_RUN_IN_EDITOR'); ?>">
                                                <span class="icon-play" aria-hidden="true"></span>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-secondary jmm-export-btn" data-query="<?php echo $this->escape($item->query); ?>" data-filename="<?php echo $this->escape($item->title); ?>" data-dbname="<?php echo $this->escape($item->dbname); ?>" title="<?php echo Text::_('COM_JMM_EXPORT_CSV'); ?>">
                                                <span class="icon-download" aria-hidden="true"></span>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-4">
                                        <?php echo Text::_('COM_JMM_NO_SITE_TABLES_FOUND'); ?>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                        <?php if ($this->pagination): ?>
                            <tfoot>
                                <tr>
                                    <td colspan="8">
                                        <?php echo $this->pagination->getListFooter(); ?>
                                    </td>
                                </tr>
                            </tfoot>
                        <?php endif; ?>
                    </table>
                </div>

                <input type="hidden" name="task" value="">
                <input type="hidden" name="boxchecked" value="0">
                <input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>">
                <input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>">
                <?php echo HTMLHelper::_('form.token'); ?>
            </div>
        </div>
    </div>
</form>