<?php
defined('_JEXEC') or die('Restricted access');
/**
 * @package		JMM
 * @link		http://adidac.github.com/jmm/index.html
 * @license		GNU/GPL
 * @copyright	Biswarup Adhikari
*/
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('formbehavior.chosen', 'select');
?>
<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		if (task == 'sitetable.cancel' || document.formvalidator.isValid(document.id('site-table-form')))
		{
			Joomla.submitform(task, document.getElementById('site-table-form'));
		}
	}
</script>
<form action="<?php echo JRoute::_('index.php?option=com_jmm&view=sitetable&layout=edit&id='.(int) $this->item->id); ?>" method="POST" name="adminForm" id="site-table-form" class="form-validate form-horizontal">
<div class="span10 form-horizontal">
	<fieldset>
		<?php foreach($this->form->getFieldset() as $field):?>
			<div class="control-group">
				<div class="control-label">
					<?php echo $field->label;?>
				</div>
				<div class="controls">
					<?php echo $field->input;?>
					
				</div>
			</div>			
		<?php endforeach ?>
	</fieldset>
	<input type="hidden" name="task" value="sitetable.edit">
<?php echo JHtml::_('form.token');?>
</form>
</div>