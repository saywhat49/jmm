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
	$templateFolder='/images';
}
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
				<?php echo $this->form->getInput('php'); ?><br><br></li>
				<li><?php echo $this->form->getLabel('css'); ?>
				<?php echo $this->form->getInput('css'); ?><br><br></li>
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
	<iframe width="100%" height="700" frameborder="0" scrolling="no" src="index.php?option=com_media&amp;view=images&amp;tmpl=component&amp;folder=<?php echo $templateFolder;?>"></iframe>
</fieldset>
</div>
