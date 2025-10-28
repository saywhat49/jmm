<?php
defined('_JEXEC') or die('Restricted access');
?>
<form action="index.php" method="POST" name="adminForm" id="adminForm" class="form-validate">
<div class="width-60 fltlft">
    <fieldset class="adminform">
        <ul class="adminformList">
        <?php foreach($this->form->getFieldset() as $field):?>
            <li>
                <?php echo $field->label;?>
                <?php echo $field->input;?>
            </li>
        <?php endforeach ?>
        </ul>
    </fieldset>
</div>

<input type="hidden" name="option" value="com_jmm" />
<input type="hidden" name="layout" value="edit" />
<input type="hidden" name="id" value="<?php echo $this->item->id; ?>" />
<input type="hidden" name="task" value="" />
<?php echo JHtml::_('form.token'); ?>
</form>
