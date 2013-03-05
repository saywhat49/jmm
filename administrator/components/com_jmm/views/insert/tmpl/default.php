<?php
/**
 * @package		JMM
 * @link		http://adidac.github.com/jmm/index.html
 * @license		GNU/GPL
 * @copyright	Biswarup Adhikari
*/
defined('_JEXEC') or die('Restricted access');
?>
<form action="index.php?option=com_jmm&amp;view=insert&amp;layout=edit&amp;id=<?php echo $this->item->id;?>" method="POST" name="adminForm" class="form-validate">
<fieldset id="filter-bar">
		<div class="filter-select fltrt">
			<label for="filter_chnagedatabase">Select DataBase</label>
			<select name="filter_chnagedatabase" id="filter_chnagedatabase" class="inputbox" onchange="this.form.submit()">
				<option value="">Select Database</option>
				<?php
				$selecteddb=JRequest::getVar('dbname',JFactory::getApplication() -> getCfg('db'));
				echo JHtml::_('select.options', $this -> databases, 'value', 'text', JRequest::getVar('filter_chnagedatabase', $selecteddb,'get'), true);
				?>
			</select>			
		</div>
		<div class="filter-select fltrt">
			<label for="filter_chnagetable">Select Table</label>
			<select name="filter_chnagetable" id="filter_chnagetable" class="inputbox" onchange="this.form.submit()">
				<option value="">Select Table</option>
				<?php
				$selectedtable=JRequest::getVar('tbl');
				echo JHtml::_('select.options', $this -> Tables, 'value', 'text', JRequest::getVar('filter_chnagetable', $selectedtable,'get'), true);
				?>
			</select>			
		</div>
</fieldset>
<div class="width-60 fltlft">
	<fieldset class="adminform">

		<?php 
		$tbl=JRequest::getVar('tbl',null);
		if(isset($tbl))
		{
		?>
		<p>Insert Into Table <b><?php echo $selectedtable;?></b> On DataBase  <b><?php echo $selecteddb;?></b> </p>
		<ul class="adminformList">
		<?php foreach($this->form->getFieldset() as $field):?>
				<?php echo $field->input;?>
			<?php endforeach ?>
		</ul>
		<?php 
		}else{
			echo '<h3>Please Select Table Before Insert Data</h3>';
		}
		?>

	</fieldset>
	
</div>
<input type="hidden" name="view" value="insert">
<input type="hidden" name="task" value="">
<input type="hidden" name="tbl" value="<?php echo JRequest::getVar('tbl');?>">
<input type="hidden" name="dbname" value="<?php echo JRequest::getVar('dbname');?>">
<?php echo JHtml::_('form.token');?>
</form>