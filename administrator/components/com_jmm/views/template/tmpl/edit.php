<?php
/**
 * @package		JMM
 * @link		http://adidac.github.com/jmm/index.html
 * @license		GNU/GPL
 * @copyright	Biswarup Adhikari
*/
defined('_JEXEC') or die('Restricted access');
?>
<form action="index.php?option=com_jmm&amp;layout=edit&amp;id=<?php echo $this->item->id;?>" method="POST" name="adminForm" class="form-validate">
<div class="width-60 fltlft">
	<fieldset class="adminform">
		<ul class="adminformList">
			<li><?php echo $this->form->getLabel('id'); ?>
				<?php echo $this->form->getInput('id'); ?></li>
				<li><?php echo $this->form->getLabel('title'); ?>
				<?php echo $this->form->getInput('title'); ?><br><br>	</li>
				<li><?php echo $this->form->getLabel('php'); ?>
				<?php echo $this->form->getInput('php'); ?></li>
				<li><?php echo $this->form->getLabel('css'); ?>
				<?php echo $this->form->getInput('css'); ?></li>
				<li><?php echo $this->form->getLabel('js'); ?>
				<?php echo $this->form->getInput('js'); ?></li>
		</ul>
	</fieldset>
</div>
<input type="hidden" name="task" value="template.edit">
<?php echo JHtml::_('form.token');?>
</form>
<div class="width-40 fltrt">
	<fieldset class="adminform">
	<iframe width="100%" height="500" frameborder="0" scrolling="no" src="index.php?option=com_media&amp;view=images&amp;tmpl=component&amp;asset=com_jmm&amp;author=&amp;fieldid=jform_images&amp;folder="></iframe>
</fieldset>
</div>
