<?php
/**
 * @package		JMM
 * @link		http://adidac.github.com/jmm/index.html
 * @license		GNU/GPL
 * @copyright	Biswarup Adhikari
*/
defined('_JEXEC') or die('Restricted access');
if(isset($_GET['dbname'])){
	$dbname=JRequest::getVar('dbname');
}else{
	$dbname = JFactory::getApplication() -> getCfg('db');
}
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('dropdown.init');
JHtml::_('formbehavior.chosen', 'select');
?>
<form method="post" id="adminForm" name="adminForm">
<?php if (!empty( $this->sidebar)) : ?>
	<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" class="span10">
<?php else : ?>
	<div id="j-main-container">
<?php endif;?>
<div id="filter-bar" class="btn-toolbar">
	<div class="btn-group pull-left">
				<label for="filter_cannedqueries">Run Canned Queries OR Site Tables Queries</label>
			<select name="filter_cannedqueries" id="filter_cannedqueries" class="inputbox" onchange="document.getElementById('filter_chnagedatabase').value='';document.getElementById('query').value=document.getElementById('filter_cannedqueries').value;this.form.submit()">
				<option value="">Select Query</option>
				 <optgroup label="-- Canned Queries -- " id="opt-canned">
				<?php
				echo JHtml::_('select.options', $this -> cannedQueries, 'value', 'text', JRequest::getVar('filter_cannedqueries',''), true);
				?>
				</optgroup>
				
				 <optgroup label="-- Site Tables Queries -- " id="opt-stbl">
				<?php
				echo JHtml::_('select.options', $this->siteTables, 'value', 'text', JRequest::getVar('filter_cannedqueries',''), true);
				?>
				</optgroup>				
			</select>	
	</div>
	<div class="btn-group pull-right">
		<label for="filter_chnagedatabase">Select DataBase</label>
			<select name="filter_chnagedatabase" id="filter_chnagedatabase" class="inputbox" onchange="document.getElementById('query').value='';this.form.submit()">
				<option value="">Select Database</option>
				<?php
				$selecteddb=JRequest::getVar('dbname',JFactory::getApplication() -> getCfg('db'));
				echo JHtml::_('select.options', $this -> databases, 'value', 'text', JRequest::getVar('filter_chnagedatabase', $selecteddb,'get'), true);
				?>
			</select>
	</div>
</div>
<div class="clearfix"> </div>
<div>
	<textarea placeholder="Enter your SQL Quiries......" rows="10" cols="150" id="query" name="query" class="span12"><?php echo JRequest::getVar('query','');?></textarea><br>
	<input type="submit" class="btn_runquery large" value="Run Query">
	<input type="hidden" id="currentdb" name="currentdb" value="<?php echo $dbname;?>">
<?php
if(isset($_POST['query'])){
?>
<input type="button" class="btn_runquery large" id="save_as_canned_query" value="Save as Canned Query">
<input type="button" class="btn_runquery large" id="save_as_site_table" value="Save as Site Table">
<input type="button" class="btn_runquery large" id="export_as_csv" value="Export as CSV">
<?php
}
?>
</div>
<div class="clearfix"> </div>
<div id="loading-icon" class="loading-icon"></div>
<?php
	if($this->items && count($this->items)>0){
		?>
		<hr>
	<table class="table table-bordered table-hover">
		<thead>
			<tr>
				<?php
				foreach($this->items[0] as $col=>$val){
				?>
				<th>
					<?php echo $col;?>
				</th>
				<?php
				}
				?>
							
			</tr>
		</thead>
		<tbody>
			<?php foreach ($this->items as $i => $item): ?>	
				
				<tr class="row<?php echo $i % 2?>">
					
					<?php
					$z=0;
					foreach($this->items[$i] as $col=>$val){
					?>
					<?php
					if(count($this->items[$i])>4 && $z!=0){
						echo '<td align="center">';
					}else{						
						echo '<td>';
					}
					?>
					
						<?php echo $val;?>
					</td>
					</td>
					<?php
					$z++;
					}
					?>
					
				</tr>
				
			<?php endforeach?>
		</tbody>
	</table>
	<?php
	}
	?>
<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<?php echo JHtml::_('form.token');?>
</div>
</form>