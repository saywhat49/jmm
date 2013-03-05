<?php
/**
 * @package		JMM
 * @link		http://adidac.github.com/jmm/index.html
 * @license		GNU/GPL
 * @copyright	Biswarup Adhikari
*/
defined('_JEXEC') or die('Restricted access');
$listOrder = $this -> escape($this -> state -> get('list.ordering'));
$orderDirn = $this -> escape($this -> state -> get('list.direction'));
?>
<form method="post" id="adminForm" name="adminForm">
	<fieldset id="filter-bar">
		<!--
		<div class="filter-search fltlft">
		<label for="filter_search">Search</label>
		<input type="text" name="filter_search" id="filter_search"
		value="<?php echo $this->escape($this->state->get('filter.search'));?>" title="Search" />
		<button type="submit" class="btn">Search</button>
		<button type="button" onclick="document.id('filter_search').value='';this.form.submit();">
		Clear
		</button>
		</div>
		-->
		<div class="filter-select fltrt">
			<label for="filter_chnagedatabase">Select DataBase</label>
			<select name="filter_chnagedatabase" id="filter_chnagedatabase" class="inputbox" onchange="document.getElementById('filter_browsetable').value='';document.getElementById('filter_tablestructure').value='';this.form.submit()">
				<option value="">Select Database</option>
				<?php
				$selecteddb = '';
				if (isset($_REQUEST['dbname'])) {
					$selecteddb = JRequest::getVar('dbname');
				} else {
					$selecteddb = JFactory::getApplication() -> getCfg('db');
				}

				echo JHtml::_('select.options', $this -> databases, 'value', 'text', JRequest::getVar('techbang_test', $selecteddb), true);

				if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'browse') {
					$browsetable = JRequest::getVar('tbl');
				}
				if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'structure') {
					$structuretable = JRequest::getVar('tbl');
				}
				?>
			</select>
			<label for="filter_browsetable">Browse Table Rows</label>
			<select name="filter_browsetable" id="filter_browsetable" class="inputbox" onchange="document.getElementById('filter_tablestructure').value='';this.form.submit()">
				<option value="">Select Table</option>
				<?php
				echo JHtml::_('select.options', $this -> tables, 'value', 'text',$browsetable, true);
				?>
			</select>

			<label for="filter_tablestructure">View Table Structure</label>
			<select name="filter_tablestructure" id="filter_tablestructure" class="inputbox" onchange="document.getElementById('filter_browsetable').value='';this.form.submit()">

				<option value="">Select Table</option>
				<?php echo JHtml::_('select.options', $this -> tables, 'value', 'text',$structuretable, true); ?>
			</select>
		</div>
	</fieldset>
	<?php
if(count($this->items)>0){
	?>
	<table class="adminlist">
		<thead>
			<tr>
				<?php
foreach($this->items[0] as $col=>$val){
				?>
				<th> <?php echo JHtml::_('grid.sort', $col, $col, $orderDirn, $listOrder); ?> </th>
				<?php
				}
				?>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($this->items as $i => $item):
			?>

			<tr class="row<?php echo $i % 2?>">

				<?php
$z=0;
foreach($this->items[$i] as $col=>$val){
				?>
				<?php
				if (count($this -> items[$i]) > 4 && $z != 0) {
					echo '<td align="center">';
				} else {
					echo '<td>';
				}
				?>

				<?php echo $val; ?>
				</td>
				</td>
				<?php
				$z++;
				}
				?>
			</tr>

			<?php endforeach ?>
		</tbody>
		<?php
if(isset($_REQUEST['action']) && $_REQUEST['action']=='browse'){
		?>
		<tfoot>
			<tr>
				<td colspan="<?php echo count($this->items[0]);?>"> <?php echo $this -> pagination -> getListFooter(); ?> </td>
			</tr>
		</tfoot>

		<?php
		}
		?>
	</table>
	<?php
	}
	?>
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
	<?php echo JHtml::_('form.token'); ?>
</form>