<?php
defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

$wa = $this->document->getWebAssetManager();
$wa->useScript('keepalive');
$wa->useScript('form.validate');
$wa->useScript('showon');

$templatePath = $this->item->template_path ?? '';
$hasPhpField  = $this->form->getField('php') !== false;
?>
<form action="<?php echo Route::_('index.php?option=com_jmm&layout=edit&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="item-form" class="form-validate">
    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-3">
                <div class="card-header bg-light fw-bold"><?php echo Text::_('COM_JMM_TEMPLATE_DETAILS'); ?></div>
                <div class="card-body">
                    <?php echo $this->form->renderField('title'); ?>
                    <?php echo $this->form->renderField('layout_type'); ?>
                    <?php echo $this->form->renderField('chart_type'); ?>
                    <?php if ($templatePath) : ?>
                        <div class="form-text text-muted small">
                            <span class="icon-folder me-1" aria-hidden="true"></span>
                            <code><?php echo $this->escape(str_replace(JPATH_SITE, '', $templatePath)); ?></code>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <?php if ($hasPhpField) : ?>
                <div class="card mb-3">
                    <div class="card-header bg-light fw-bold">
                        <span class="icon-code me-1" aria-hidden="true"></span>
                        <?php echo Text::_('COM_JMM_TEMPLATE_PHP'); ?>
                        <span class="text-muted fw-normal small ms-2">index.php</span>
                    </div>
                    <div class="card-body">
                        <?php echo $this->form->renderField('php'); ?>
                        <div class="form-text text-muted small mt-2">
                            <?php echo Text::_('COM_JMM_TEMPLATE_PHP_ACTIVATE'); ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <div class="card mb-3">
                <div class="card-header bg-light fw-bold">
                    <span class="icon-paint-brush me-1" aria-hidden="true"></span>
                    <?php echo Text::_('COM_JMM_TEMPLATE_CSS_FILE'); ?>
                    <span class="text-muted fw-normal small ms-2">css/default.css</span>
                </div>
                <div class="card-body">
                    <?php echo $this->form->renderField('css_file'); ?>
                </div>
            </div>

            <?php if ($this->form->getField('js') !== false) : ?>
                <div class="card mb-3">
                    <div class="card-header bg-light fw-bold">
                        <span class="icon-cogs me-1" aria-hidden="true"></span>
                        <?php echo Text::_('COM_JMM_TEMPLATE_JS'); ?>
                        <span class="text-muted fw-normal small ms-2">js/custom.js</span>
                    </div>
                    <div class="card-body">
                        <?php echo $this->form->renderField('js'); ?>
                    </div>
                </div>
            <?php endif; ?>

            <div class="card mb-3">
                <div class="card-header bg-light fw-bold">
                    <?php echo Text::_('COM_JMM_TEMPLATE_CUSTOM_CSS'); ?>
                </div>
                <div class="card-body">
                    <?php echo $this->form->renderField('custom_css'); ?>
                    <div class="form-text text-muted small mt-2">
                        <?php echo Text::_('COM_JMM_TEMPLATE_CSS_HELP'); ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card mb-3">
                <div class="card-header bg-light fw-bold"><?php echo Text::_('COM_JMM_PUBLISHING_OPTIONS'); ?></div>
                <div class="card-body">
                    <?php echo $this->form->renderField('published'); ?>
                    <?php echo $this->form->renderField('datetime'); ?>
                    <?php if (!empty($this->item->id)) : ?>
                        <?php echo $this->form->renderField('id'); ?>
                    <?php endif; ?>
                </div>
            </div>
            <div class="card mb-3 border-info">
                <div class="card-header bg-info text-white fw-bold">
                    <span class="icon-info-circle me-1" aria-hidden="true"></span>
                    <?php echo Text::_('COM_JMM_TEMPLATE_USAGE'); ?>
                </div>
                <div class="card-body small">
                    <p><?php echo Text::_('COM_JMM_TEMPLATE_USAGE_DESC'); ?></p>
                    <ul class="mb-2">
                        <li><strong>table</strong> — <?php echo Text::_('COM_JMM_LAYOUT_TABLE'); ?></li>
                        <li><strong>cards</strong> — <?php echo Text::_('COM_JMM_LAYOUT_CARDS'); ?></li>
                        <li><strong>chart</strong> — <?php echo Text::_('COM_JMM_LAYOUT_CHART'); ?></li>
                        <li><strong>custom</strong> — <?php echo Text::_('COM_JMM_LAYOUT_CUSTOM'); ?></li>
                    </ul>
                    <p class="mb-0"><?php echo Text::_('COM_JMM_TEMPLATE_VARS_HELP'); ?></p>
                </div>
            </div>
        </div>
    </div>

    <input type="hidden" name="task" value="">
    <?php echo HTMLHelper::_('form.token'); ?>
</form>
