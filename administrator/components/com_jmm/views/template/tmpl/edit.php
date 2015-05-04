<?php
/**
 * @package		JMM
 * @link		http://adidac.github.com/jmm/index.html
 * @license		GNU/GPL
 * @copyright	Biswarup Adhikari
*/
defined('_JEXEC') or die('Restricted access');
if(isset($this->item->title)){
	$templateFolder='/components'.DS.'com_jmm'.DS.'templates'.DS.$this->item->title.DS.'images';
}else{
	$templateFolder='images';
}
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('formbehavior.chosen', 'select');
?>
<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		if (task == 'cannedquery.cancel' || document.formvalidator.isValid(document.id('template-form')))
		{
			Joomla.submitform(task, document.getElementById('template-form'));
		}
	}
</script>
<form action="index.php?option=com_jmm&amp;layout=edit&amp;id=<?php echo $this->item->id;?>" method="POST" name="adminForm" id="template-form" class="form-validate form-horizontal">
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
		<!--Media Manager Starts-->
				<div class="controls">
					<iframe class="span10" height="650"  scrolling="no" frameborder="no" src="<?php echo JURI::root();?>administrator/index.php?option=com_media&view=images&tmpl=component&asset=com_jmm&author=&fieldid=jform_images&folder=<?php echo $templateFolder;?>"></iframe>
				</div>
				<!--Media Manager Ends-->
	</fieldset>
	<input type="hidden" name="task" value="template.edit">
<?php echo JHtml::_('form.token');?>
</form>
</div>