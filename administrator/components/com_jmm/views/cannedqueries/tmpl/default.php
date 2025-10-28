<?php
/**
 * @package		JMM
 * @link		http://adidac.github.com/jmm/index.html
 * @license		GNU/GPL
 * @copyright	Biswarup Adhikari
*/
defined('_JEXEC') or die('Restricted access');
$listOrder=$this->escape($this->state->get('list.ordering'));
$orderDirn=$this->escape($this->state->get('list.direction'));
$this -> state -> set('filter.database',JRequest::getVar('dbname'));
?>
<form action="index.php?option=com_jmm&amp;view=cannedqueries" method="post" id="adminForm" name="adminForm">
	<fieldset id="filter-bar">
		<div class="filter-search fltlft">
			<label for="filter_search">Search</label>
			<input type="text" name="filter_search" id="filter_search" 
			value="<?php echo $this->escape($this->state->get('filter.search'));?>" title="Search" />
			<button type="submit" class="btn">Search</button>
			<button type="button" onclick="document.id('filter_search').value='';this.form.submit();">
			Clear
			</button>
		</div>
		
			
		<div class="filter-select fltrt">
			<!--Filter Transactions By Recipients Starts-->
			<select name="filter_database" class="inputbox" onchange="this.form.submit()">

				<option value="">Select DataBase</option>
				<?php echo JHtml::_('select.options', $this -> databases, 'value', 'text', $this -> state -> get('filter.database'), true); ?>
			</select>
		</div>
	</fieldset>
	<table class="adminlist">
		<thead>
			<tr>
				
				<th>
					<input type="checkbox" name="checkall-toggle" value="" onclick="checkAll(this)"/>
				</th>
				<th>
					<?php echo JHtml::_('grid.sort','ID','ur.username',$orderDirn,$listOrder);?>
				</th>
    			<th>
					<?php echo JHtml::_('grid.sort','Title','pp.points',$orderDirn,$listOrder);?>
				</th>
				<th>
					<?php echo JHtml::_('grid.sort','DataBase','ui.username',$orderDirn,$listOrder);?>
				</th>
    			<th>
					<?php echo JHtml::_('grid.sort','Query','pp.notes',$orderDirn,$listOrder);?>
				</th>
				<th>
					<?php echo JHtml::_('grid.sort','Date Time','pp.datetime',$orderDirn,$listOrder);?>
				</th>	
				<th>
					<?php echo JHtml::_('grid.sort','Status','published',$orderDirn,$listOrder);?>
				</th>				
				<th>
					Export
				</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($this->items as $i => $item): ?>	
				<tr class="row<?php echo $i % 2?>" id="id<?php echo $i;?>">					
					<td class="center">
						<?php echo JHtml::_('grid.id',$i,$item->id);?>
					</td>
					<td class="center">
						<?php echo $this->escape($item->id);?>
					</td>
    				<td>
						<?php echo $this->escape($item->title);?>
					</td>
					<td>
						<?php echo $this->escape($item->dbname);?>
					</td>
    				<td>
						<?php echo $this->escape($item->query);?>
					</td>
					<td class="center">
						<?php echo $this->escape($item->datetime);?>
					</td>
					<td class="center">
						<?php 
						echo JHtml::_('jgrid.published',$item->published,'sitetable',true,'cb');
						?>
					</td>
					<td class="center">
							<input type="button" class="btn_runquery large" id="export_as_csv" value="Export as CSV">
					</td>
				</tr>
				
			<?php endforeach?>
		</tbody>
			<tfoot>
			<tr>
				<td colspan="4">
					<?php echo $this->pagination->getListFooter();?>
				</td>
			</tr>
		</tfoot>
	</table>
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
	<?php echo JHtml::_('form.token');?>
</form>