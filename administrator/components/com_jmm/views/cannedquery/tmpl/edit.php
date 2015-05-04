<?php
/**
 * @package		JMM
 * @link		http://adidac.github.com/jmm/index.html
 * @license		GNU/GPL
 * @copyright	Biswarup Adhikari
*/
defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('formbehavior.chosen', 'select');
?>
<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		if (task == 'cannedquery.cancel' || document.formvalidator.isValid(document.id('canned-query-form')))
		{
			Joomla.submitform(task, document.getElementById('canned-query-form'));
		}
	}
</script>
<form action="index.php?option=com_jmm&amp;layout=edit&amp;id=<?php echo $this->item->id;?>" method="POST" name="adminForm" id="canned-query-form" class="form-validate form-horizontal">
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
	<input type="hidden" name="task" value="cannedquery.edit">
<?php echo JHtml::_('form.token');?>
</form>
</div>