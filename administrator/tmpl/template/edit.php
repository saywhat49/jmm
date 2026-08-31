<?php
defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

$wa = $this->document->getWebAssetManager();
$wa->useScript('keepalive');
$wa->useScript('form.validate');
?>
<form action="<?php echo Route::_('index.php?option=com_jmm&layout=edit&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="item-form" class="form-validate">
    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-3">
                <div class="card-header bg-light fw-bold"><?php echo Text::_('COM_JMM_TEMPLATE_DETAILS'); ?></div>
                <div class="card-body">
                    <?php echo $this->form->renderField('title'); ?>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card mb-3">
                <div class="card-header bg-light fw-bold"><?php echo Text::_('COM_JMM_PUBLISHING_OPTIONS'); ?></div>
                <div class="card-body">
                    <?php echo $this->form->renderField('published'); ?>
                    <?php echo $this->form->renderField('datetime'); ?>
                    <?php if (!empty($this->item->id)): ?>
                        <?php echo $this->form->renderField('id'); ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <input type="hidden" name="task" value="">
    <?php echo HTMLHelper::_('form.token'); ?>
</form>