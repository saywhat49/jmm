<?php
defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

$sprite   = Uri::root(true) . '/media/com_jmm/images/jmm-icons.svg';
$dbSuffix = $this->currentDb !== '' ? '&dbname=' . urlencode($this->currentDb) : '';

$icon = static function (string $name, string $extra = '') use ($sprite): string {
    return '<svg class="jmm-icon ' . $extra . '" aria-hidden="true" focusable="false">'
        . '<use href="' . $sprite . '#' . $name . '"></use></svg>';
};

$tiles = [
    ['databases',     'jmm-database',  'COM_JMM_DATABASES',    'COM_JMM_TILE_DATABASES_HINT'],
    ['tables',        'jmm-table',     'COM_JMM_TABLES',       'COM_JMM_TILE_TABLES_HINT'],
    ['sql',           'jmm-sql',       'COM_JMM_SQL_QUERY',    'COM_JMM_TILE_SQL_HINT'],
    ['cannedqueries', 'jmm-bookmark',  'COM_JMM_CANNED_QUERY', 'COM_JMM_TILE_CANNED_HINT'],
    ['sitetables',    'jmm-globe',     'COM_JMM_SITE_TABLES',  'COM_JMM_TILE_SITETABLES_HINT'],
    ['createtable',   'jmm-newtable',  'COM_JMM_CREATE_TABLE', 'COM_JMM_TILE_CREATE_HINT'],
    ['insert',        'jmm-insert',    'COM_JMM_INSERT_DATA',  'COM_JMM_TILE_INSERT_HINT'],
    ['templates',     'jmm-template',  'COM_JMM_TEMPLATES',    'COM_JMM_TILE_TEMPLATES_HINT'],
];

$totalSize = (int) ($this->dbStats['data'] ?? 0) + (int) ($this->dbStats['index'] ?? 0);
$health    = $this->templateHealth;
$hasIssue  = !empty($health['missingFolders']) || !empty($health['orphanFolders']) || empty($health['writable']);
?>
<div class="com-jmm com-jmm-dashboard">
    <div id="j-main-container" class="j-main-container">

        <div class="jmm-tiles">
            <?php foreach ($tiles as [$view, $iconName, $label, $hint]): ?>
                <a class="jmm-tile" href="<?php echo Route::_('index.php?option=com_jmm&view=' . $view . $dbSuffix); ?>">
                    <?php echo $icon($iconName, 'jmm-icon-lg'); ?>
                    <span class="jmm-tile-label"><?php echo Text::_($label); ?></span>
                    <span class="jmm-tile-hint"><?php echo Text::_($hint); ?></span>
                </a>
            <?php endforeach; ?>
        </div>

        <div class="jmm-stats">
            <div class="jmm-stat">
                <span class="jmm-stat-value"><?php echo $this->formatNumber($this->databaseCount); ?></span>
                <span class="jmm-stat-label"><?php echo Text::_('COM_JMM_STAT_DATABASES'); ?></span>
            </div>
            <div class="jmm-stat">
                <span class="jmm-stat-value"><?php echo $this->formatNumber((int) ($this->dbStats['tables'] ?? 0)); ?></span>
                <span class="jmm-stat-label"><?php echo Text::_('COM_JMM_STAT_TABLES'); ?></span>
            </div>
            <div class="jmm-stat">
                <span class="jmm-stat-value"><?php echo $this->escape($this->formatBytes($totalSize)); ?></span>
                <span class="jmm-stat-label"><?php echo Text::_('COM_JMM_STAT_SIZE'); ?></span>
            </div>
            <div class="jmm-stat">
                <span class="jmm-stat-value"><?php echo $this->formatNumber((int) ($this->counts['sitetables'] ?? 0)); ?></span>
                <span class="jmm-stat-label"><?php echo Text::_('COM_JMM_STAT_SITETABLES'); ?></span>
            </div>
            <div class="jmm-stat">
                <span class="jmm-stat-value"><?php echo $this->formatNumber((int) ($this->counts['cannedqueries'] ?? 0)); ?></span>
                <span class="jmm-stat-label"><?php echo Text::_('COM_JMM_STAT_CANNED'); ?></span>
            </div>
            <div class="jmm-stat">
                <span class="jmm-stat-value"><?php echo $this->formatNumber((int) ($this->counts['templates'] ?? 0)); ?></span>
                <span class="jmm-stat-label"><?php echo Text::_('COM_JMM_STAT_TEMPLATES'); ?></span>
            </div>
        </div>

        <div class="row g-3">

            <div class="col-lg-7">
                <div class="card h-100">
                    <div class="card-header bg-light fw-bold">
                        <?php echo $icon('jmm-table'); ?>
                        <?php echo Text::sprintf('COM_JMM_LARGEST_TABLES_IN', $this->escape($this->currentDb)); ?>
                    </div>
                    <?php if (!empty($this->largestTables)): ?>
                        <table class="table table-striped align-middle mb-0">
                            <thead>
                                <tr>
                                    <th scope="col"><?php echo Text::_('COM_JMM_TABLE_NAME'); ?></th>
                                    <th scope="col" style="width:110px;"><?php echo Text::_('COM_JMM_STORAGE_ENGINE'); ?></th>
                                    <th scope="col" class="text-end" style="width:110px;"><?php echo Text::_('COM_JMM_RECORDS'); ?></th>
                                    <th scope="col" class="text-end" style="width:110px;"><?php echo Text::_('COM_JMM_STAT_SIZE'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($this->largestTables as $t): ?>
                                    <tr>
                                        <td>
                                            <a href="<?php echo Route::_('index.php?option=com_jmm&view=tables&tbl=' . urlencode($t['name']) . $dbSuffix); ?>">
                                                <?php echo $this->escape($t['name']); ?>
                                            </a>
                                        </td>
                                        <td><span class="badge bg-secondary jmm-badge-engine"><?php echo $this->escape((string) $t['engine']); ?></span></td>
                                        <td class="text-end"><?php echo $this->formatNumber((int) $t['rows_count']); ?></td>
                                        <td class="text-end"><?php echo $this->escape($this->formatBytes((int) $t['total_size'])); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <div class="card-footer small text-muted">
                            <?php echo Text::_('COM_JMM_INNODB_ESTIMATE_NOTE'); ?>
                        </div>
                    <?php else: ?>
                        <div class="card-body jmm-empty">
                            <?php echo $icon('jmm-table', 'jmm-icon-lg'); ?>
                            <p class="mb-0"><?php echo Text::_('COM_JMM_NO_SCHEMA_ACCESS'); ?></p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="card mb-3">
                    <div class="card-header bg-light fw-bold">
                        <?php echo $icon('jmm-structure'); ?>
                        <?php echo Text::_('COM_JMM_ENVIRONMENT'); ?>
                    </div>
                    <ul class="list-group list-group-flush small">
                        <li class="list-group-item d-flex justify-content-between">
                            <span><?php echo Text::_('COM_JMM_ENV_DATABASE'); ?></span>
                            <span class="jmm-font-mono"><?php echo $this->escape($this->currentDb); ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span><?php echo Text::_('COM_JMM_ENV_SERVER'); ?></span>
                            <span class="jmm-font-mono"><?php echo $this->escape((string) ($this->serverInfo['server'] ?? '?')); ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span><?php echo Text::_('COM_JMM_ENV_COLLATION'); ?></span>
                            <span class="jmm-font-mono"><?php echo $this->escape((string) ($this->serverInfo['collation'] ?? '?')); ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Joomla</span>
                            <span class="jmm-font-mono"><?php echo $this->escape((string) ($this->serverInfo['joomla'] ?? '?')); ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>PHP</span>
                            <span class="jmm-font-mono"><?php echo $this->escape((string) ($this->serverInfo['php'] ?? '?')); ?></span>
                        </li>
                    </ul>
                </div>

                <div class="card mb-3 <?php echo $hasIssue ? 'border-warning' : ''; ?>">
                    <div class="card-header bg-light fw-bold">
                        <?php echo $icon('jmm-template'); ?>
                        <?php echo Text::_('COM_JMM_TEMPLATE_HEALTH'); ?>
                    </div>
                    <div class="card-body small">
                        <?php if (!$hasIssue): ?>
                            <p class="mb-0 text-success">
                                <span class="icon-check me-1" aria-hidden="true"></span>
                                <?php echo Text::_('COM_JMM_TEMPLATE_HEALTH_OK'); ?>
                            </p>
                        <?php else: ?>
                            <?php if (empty($health['writable'])): ?>
                                <p class="mb-2 text-danger"><?php echo Text::sprintf('COM_JMM_TEMPLATE_FOLDER_NOT_WRITABLE', $this->escape(str_replace(JPATH_SITE, '', (string) $health['path']))); ?></p>
                            <?php endif; ?>
                            <?php if (!empty($health['missingFolders'])): ?>
                                <p class="mb-2"><?php echo Text::sprintf('COM_JMM_TEMPLATE_MISSING_FOLDERS', $this->escape(implode(', ', $health['missingFolders']))); ?></p>
                            <?php endif; ?>
                            <?php if (!empty($health['orphanFolders'])): ?>
                                <p class="mb-0"><?php echo Text::sprintf('COM_JMM_TEMPLATE_ORPHAN_FOLDERS', $this->escape(implode(', ', $health['orphanFolders']))); ?></p>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header bg-light fw-bold">
                        <?php echo $icon('jmm-bookmark'); ?>
                        <?php echo Text::_('COM_JMM_RECENT_QUERIES'); ?>
                    </div>
                    <?php if (!empty($this->recentQueries)): ?>
                        <ul class="list-group list-group-flush small">
                            <?php foreach ($this->recentQueries as $q): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <a href="<?php echo Route::_('index.php?option=com_jmm&view=sql&canned_id=' . (int) $q['id'] . ($q['dbname'] ? '&dbname=' . urlencode($q['dbname']) : '')); ?>">
                                        <?php echo $this->escape($q['title']); ?>
                                    </a>
                                    <?php if (!empty($q['dbname'])): ?>
                                        <span class="badge bg-light text-dark jmm-font-mono"><?php echo $this->escape($q['dbname']); ?></span>
                                    <?php endif; ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <div class="card-body jmm-empty py-4">
                            <p class="mb-0"><?php echo Text::_('COM_JMM_NO_CANNED_QUERIES'); ?></p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </div>
</div>
