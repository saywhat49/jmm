<?php
defined('_JEXEC') or die('Restricted access');
$rows=$this->rows;
$cols=$this->columns;
$params=$this->params;
$document =JFactory::getDocument();
$themeFile=JPATH_COMPONENT.DS.'themes'.DS.'sitetables'.DS.$this->theme.DS.'index.php';
if(file_exists($themeFile)){
require_once($themeFile);
}else{
$themeFile=JPATH_COMPONENT.DS.'themes'.DS.'sitetables'.DS.'default'.DS.'index.php';	
require_once($themeFile);
}

?>
<form action="index.php?option=com_jmm&amp;view=table" method="post" id="adminForm" name="adminForm">
<div class="jmm-pagination">
	<?php echo $this->pagination->getPagesLinks(); ?>
</div>
<input type="hidden" name="option" value="com_jmm" />
<input type="hidden" name="view" value="table" />
<?php echo JHtml::_('form.token');?>
</form>