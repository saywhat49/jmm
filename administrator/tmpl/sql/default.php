<?php
defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

?>
<form action="<?php echo Route::_('index.php?option=com_jmm&view=sql'); ?>" method="post" name="adminForm" id="adminForm">
    <div class="row">
        <div class="col-md-12">
            <div id="j-main-container" class="j-main-container">
                <div class="card mb-3 shadow-sm">
                    <div class="card-body">
                        <div class="row g-3 mb-3">
                            <div class="col-md-4">
                                <label for="sql_dbname" class="form-label fw-bold"><?php echo Text::_('COM_JMM_SELECT_DATABASE'); ?>:</label>
                                <select name="dbname" id="sql_dbname" class="form-select" onchange="this.form.submit();">
                                    <option value=""><?php echo Text::_('COM_JMM_DEFAULT_DATABASE'); ?></option>
                                    <?php foreach ($this->databases as $dbName): ?>
                                        <option value="<?php echo $this->escape($dbName); ?>" <?php echo $this->currentDb === $dbName ? 'selected' : ''; ?>>
                                            <?php echo $this->escape($dbName); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="sql_canned_queries" class="form-label fw-bold"><?php echo Text::_('COM_JMM_CANNED_QUERIES'); ?>:</label>
                                <select id="sql_canned_queries" class="form-select" onchange="if(this.value){document.getElementById('sql_query_editor').value=this.value;}">
                                    <option value=""><?php echo Text::_('COM_JMM_SELECT_SAVED_QUERY'); ?></option>
                                    <?php foreach ($this->cannedQueries as $can): ?>
                                        <option value="<?php echo $this->escape($can->query); ?>">
                                            <?php echo $this->escape($can->title); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="sql_site_tables" class="form-label fw-bold"><?php echo Text::_('COM_JMM_SITE_TABLES'); ?>:</label>
                                <select id="sql_site_tables" class="form-select" onchange="if(this.value){document.getElementById('sql_query_editor').value=this.value;}">
                                    <option value=""><?php echo Text::_('COM_JMM_SELECT_SITE_TABLE_QUERY'); ?></option>
                                    <?php foreach ($this->siteTables as $stbl): ?>
                                        <option value="<?php echo $this->escape($stbl->query); ?>">
                                            <?php echo $this->escape($stbl->title); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="sql_query_editor" class="form-label fw-bold"><?php echo Text::_('COM_JMM_SQL_STATEMENT'); ?>:</label>
                            <textarea name="query" id="sql_query_editor" class="form-control font-monospace" rows="6" placeholder="SELECT * FROM `#__users` WHERE `block` = 0;"><?php echo htmlspecialchars($this->query, ENT_QUOTES, 'UTF-8'); ?></textarea>
                        </div>

                        <div class="d-flex gap-2 flex-wrap">
                            <button type="submit" class="btn btn-primary">
                                <span class="icon-play" aria-hidden="true"></span> <?php echo Text::_('COM_JMM_RUN_QUERY'); ?>
                            </button>
                            <button type="button" class="btn btn-success" id="btn_save_canned">
                                <span class="icon-bookmark" aria-hidden="true"></span> <?php echo Text::_('COM_JMM_SAVE_AS_CANNED'); ?>
                            </button>
                            <button type="button" class="btn btn-info text-white" id="btn_save_sitetable">
                                <span class="icon-globe" aria-hidden="true"></span> <?php echo Text::_('COM_JMM_SAVE_AS_SITE_TABLE'); ?>
                            </button>
                            <?php if (!empty($this->query)): ?>
                                <button type="button" class="btn btn-outline-secondary jmm-export-btn" data-query="<?php echo $this->escape($this->query); ?>" data-filename="sql_export" data-dbname="<?php echo $this->escape($this->currentDb); ?>">
                                    <span class="icon-download" aria-hidden="true"></span> <?php echo Text::_('COM_JMM_EXPORT_CSV'); ?>
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <?php if (!empty($this->queryResult['rows'])): ?>
                    <div class="card shadow-sm">
                        <div class="card-header bg-light d-flex justify-content-between align-items-center">
                            <span class="fw-bold"><?php echo Text::_('COM_JMM_QUERY_RESULTS'); ?> (<?php echo count($this->queryResult['rows']); ?> <?php echo Text::_('COM_JMM_ROWS'); ?>)</span>
                            <span class="badge bg-secondary"><?php echo $this->queryResult['time']; ?>s</span>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive" style="max-height: 600px; overflow-y: auto;">
                                <table class="table table-striped table-hover table-bordered mb-0 align-middle">
                                    <thead class="table-light sticky-top">
                                        <tr>
                                            <?php foreach (array_keys($this->queryResult['rows'][0]) as $header): ?>
                                                <th scope="col"><?php echo $this->escape($header); ?></th>
                                            <?php endforeach; ?>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($this->queryResult['rows'] as $row): ?>
                                            <tr>
                                                <?php foreach ($row as $cell): ?>
                                                    <td><?php echo htmlspecialchars((string) $cell, ENT_QUOTES, 'UTF-8'); ?></td>
                                                <?php endforeach; ?>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <input type="hidden" name="task" value="">
                <?php echo HTMLHelper::_('form.token'); ?>
            </div>
        </div>
    </div>
</form>