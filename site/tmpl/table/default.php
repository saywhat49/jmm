<?php
defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

?>
<div class="com-jmm-table py-3">
    <?php if (!empty($this->siteTable)): ?>
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="h2 mb-0"><?php echo $this->escape($this->siteTable->title); ?></h1>
        </div>

        <?php if (!empty($this->items)): ?>
            <div class="table-responsive shadow-sm rounded mb-3">
                <table class="table table-striped table-hover table-bordered align-middle mb-0">
                    <thead class="table-primary">
                        <tr>
                            <?php foreach ($this->columns as $col): ?>
                                <th scope="col"><?php echo $this->escape(ucwords(str_replace('_', ' ', $col))); ?></th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($this->items as $row): ?>
                            <tr>
                                <?php foreach ($row as $val): ?>
                                    <td><?php echo htmlspecialchars((string) $val, ENT_QUOTES, 'UTF-8'); ?></td>
                                <?php endforeach; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <?php if ($this->pagination && $this->pagination->pagesTotal > 1): ?>
                <div class="d-flex justify-content-center mt-3">
                    <?php echo $this->pagination->getPagesLinks(); ?>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="alert alert-info">
                <?php echo Text::_('COM_JMM_NO_RECORDS_FOUND'); ?>
            </div>
        <?php endif; ?>
    <?php else: ?>
        <div class="alert alert-warning">
            <?php echo Text::_('COM_JMM_SITE_TABLE_NOT_SPECIFIED'); ?>
        </div>
    <?php endif; ?>
</div>