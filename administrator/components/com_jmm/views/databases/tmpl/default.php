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
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('dropdown.init');
JHtml::_('formbehavior.chosen', 'select');
?>
<form action="index.php?option=com_jmm&amp;view=databases" method="post" id="adminForm" name="adminForm">
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
		<div class="btn-wrapper input-append">
			<input type="text" name="filter_search" id="filter_search" 
			value="<?php echo $this->escape($this->state->get('filter.search'));?>" title="Search" />
			<input type="submit" class="btn" value="Search">
			<input type="button" class="btn search_clear_btn" onclick="document.getElementById('filter_search').value='';this.form.submit();" value="Clear">
		</div>
	</div>
</div>
<div class="clearfix"> </div>
<table class="table table-bordered table-hover">
		<thead>
			<tr>
				<?php
				foreach($this->items[0] as $col=>$val){
				?>
				<th>
					<?php echo JHtml::_('grid.sort',$col,$col,$orderDirn,$listOrder);?>
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
					foreach($this->items[$i] as $col=>$val){
					?>
					<td>
						<?php echo $val;?>
					</td>
					</td>
					<?php
					}
					?>
					
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
</div>
</form>