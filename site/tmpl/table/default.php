<?php
defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;

$layout = $this->params->get('display_layout', 'table');
?>
<div class="com-jmm-container py-3">
    <?php if (!empty($this->siteTable)): ?>
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
            <h1 class="h2 mb-0"><?php echo $this->escape($this->siteTable->title); ?></h1>
            <?php if (!empty($this->items)): ?>
                <span class="badge bg-primary fs-6"><?php echo count($this->items); ?> <?php echo Text::_('COM_JMM_RECORDS'); ?></span>
            <?php endif; ?>
        </div>

        <?php if (!empty($this->items)): ?>
            <?php if ($layout === 'cards'): ?>
                <?php echo $this->loadTemplate('cards'); ?>
            <?php elseif ($layout === 'chart'): ?>
                <?php echo $this->loadTemplate('chart'); ?>
            <?php else: ?>
                <?php echo $this->loadTemplate('table'); ?>
            <?php endif; ?>

            <?php if ($layout !== 'chart' && $this->pagination && $this->pagination->pagesTotal > 1): ?>
                <div class="d-flex justify-content-center mt-4">
                    <?php echo $this->pagination->getPagesLinks(); ?>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="alert alert-info shadow-sm">
                <span class="icon-info-circle me-2" aria-hidden="true"></span>
                <?php echo Text::_('COM_JMM_NO_RECORDS_FOUND'); ?>
            </div>
        <?php endif; ?>
    <?php else: ?>
        <div class="alert alert-warning shadow-sm">
            <span class="icon-exclamation-triangle me-2" aria-hidden="true"></span>
            <?php echo Text::_('COM_JMM_SITE_TABLE_NOT_SPECIFIED'); ?>
        </div>
    <?php endif; ?>
</div>