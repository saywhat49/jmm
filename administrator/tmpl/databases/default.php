<?php
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

$listOrder = $this->escape($this->state->get('list.ordering', 'name'));
$listDirn  = $this->escape($this->state->get('list.direction', 'ASC'));

$sprite = Uri::root(true) . '/media/com_jmm/images/jmm-icons.svg';
// La vue n'expose pas de base active : on conserve celle de l'URL si
// elle est presente, pour ne pas perdre le contexte en naviguant.
$currentDb = Factory::getApplication()->getInput()->getString('dbname', '');
$dbSuffix  = $currentDb !== '' ? '&dbname=' . urlencode($currentDb) : '';

// Acces rapide aux sections du composant.
$tiles = [
    ['tables',        'jmm-table',     'COM_JMM_TABLES',       'COM_JMM_TILE_TABLES_HINT'],
    ['sql',           'jmm-sql',       'COM_JMM_SQL_QUERY',    'COM_JMM_TILE_SQL_HINT'],
    ['cannedqueries', 'jmm-bookmark',  'COM_JMM_CANNED_QUERY', 'COM_JMM_TILE_CANNED_HINT'],
    ['sitetables',    'jmm-globe',     'COM_JMM_SITE_TABLES',  'COM_JMM_TILE_SITETABLES_HINT'],
    ['createtable',   'jmm-newtable',  'COM_JMM_CREATE_TABLE', 'COM_JMM_TILE_CREATE_HINT'],
    ['insert',        'jmm-insert',    'COM_JMM_INSERT_DATA',  'COM_JMM_TILE_INSERT_HINT'],
    ['templates',     'jmm-template',  'COM_JMM_TEMPLATES',    'COM_JMM_TILE_TEMPLATES_HINT'],
];
?>
<div class="jmm-tiles">
    <?php foreach ($tiles as [$view, $icon, $label, $hint]): ?>
        <a class="jmm-tile" href="<?php echo Route::_('index.php?option=com_jmm&view=' . $view . $dbSuffix); ?>">
            <svg class="jmm-icon jmm-icon-lg" aria-hidden="true" focusable="false">
                <use href="<?php echo $sprite; ?>#<?php echo $icon; ?>"></use>
            </svg>
            <span class="jmm-tile-label"><?php echo Text::_($label); ?></span>
            <span class="jmm-tile-hint"><?php echo Text::_($hint); ?></span>
        </a>
    <?php endforeach; ?>
</div>
<?php ?>
<form action="<?php echo Route::_('index.php?option=com_jmm&view=databases'); ?>" method="post" name="adminForm" id="adminForm">
    <div class="row">
        <div class="col-md-12">
            <div id="j-main-container" class="j-main-container">
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="row g-2 align-items-center">
                            <div class="col-auto">
                                <div class="input-group">
                                    <input type="text" name="filter_search" id="filter_search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" class="form-control" placeholder="<?php echo Text::_('JSEARCH_FILTER'); ?>">
                                    <button type="submit" class="btn btn-primary"><span class="icon-search" aria-hidden="true"></span> <?php echo Text::_('JSEARCH_FILTER_SUBMIT'); ?></button>
                                    <button type="button" class="btn btn-secondary" onclick="document.getElementById('filter_search').value='';this.form.submit();"><span class="icon-times" aria-hidden="true"></span> <?php echo Text::_('JSEARCH_FILTER_CLEAR'); ?></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle">
                        <thead>
                            <tr>
                                <th scope="col" style="width: 50px;">#</th>
                                <th scope="col"><?php echo Text::_('COM_JMM_DATABASE_NAME'); ?></th>
                                <th scope="col" class="text-end" style="width: 200px;"><?php echo Text::_('COM_JMM_ACTIONS'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($this->items)): ?>
                                <?php foreach ($this->items as $i => $item): ?>
                                    <tr>
                                        <td><?php echo $i + 1; ?></td>
                                        <td>
                                            <a href="<?php echo Route::_('index.php?option=com_jmm&view=tables&dbname=' . urlencode($item->name)); ?>" class="fw-bold text-decoration-none">
                                                <span class="icon-database me-1" aria-hidden="true"></span>
                                                <?php echo $this->escape($item->name); ?>
                                            </a>
                                        </td>
                                        <td class="text-end">
                                            <a href="<?php echo Route::_('index.php?option=com_jmm&view=tables&dbname=' . urlencode($item->name)); ?>" class="btn btn-sm btn-outline-primary">
                                                <span class="icon-table" aria-hidden="true"></span> <?php echo Text::_('COM_JMM_VIEW_TABLES'); ?>
                                            </a>
                                            <a href="<?php echo Route::_('index.php?option=com_jmm&view=sql&dbname=' . urlencode($item->name)); ?>" class="btn btn-sm btn-outline-secondary">
                                                <span class="icon-code" aria-hidden="true"></span> <?php echo Text::_('COM_JMM_SQL'); ?>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-4">
                                        <?php echo Text::_('COM_JMM_NO_DATABASES_FOUND'); ?>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <input type="hidden" name="task" value="">
                <input type="hidden" name="boxchecked" value="0">
                <?php echo HTMLHelper::_('form.token'); ?>
            </div>
        </div>
    </div>
</form>