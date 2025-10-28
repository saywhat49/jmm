<?php
/**
 * @package		JMM
 * @link		http://adidac.github.com/jmm/index.html
 * @license		GNU/GPL
 * @copyright	Biswarup Adhikari
*/
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

// Charger les scripts nécessaires pour Joomla 5
$wa = Factory::getApplication()->getDocument()->getWebAssetManager();
$wa->useScript('table.columns');
$wa->useScript('multiselect');

// Initialisation des objets nécessaires
$app = Factory::getApplication();
$input = $app->input;

// Récupération des paramètres de tri
$listOrder = $this->escape($this->state->get('list.ordering'));
$orderDirn = $this->escape($this->state->get('list.direction'));

// Récupération de la base de données sélectionnée et mise à jour de l'état
$dbname = $input->getString('dbname', '');
$this->state->set('filter.database', $dbname);
?>
<form action="index.php?option=com_jmm&amp;view=cannedqueries" method="post" id="adminForm" name="adminForm">
	<fieldset id="filter-bar">
		<div class="filter-search fltlft">
			<label for="filter_search"><?php echo Text::_('Search'); ?></label>
			<input type="text" name="filter_search" id="filter_search" 
			value="<?php echo $this->escape($this->state->get('filter.search')); ?>" title="<?php echo Text::_('Search'); ?>" />
			<button type="submit" class="btn"><?php echo Text::_('Search'); ?></button>
			<button type="button" onclick="document.getElementById('filter_search').value='';this.form.submit();">
			<?php echo Text::_('Clear'); ?>
			</button>
		</div>
		
		<div class="filter-select fltrt">
			<!--Filter Transactions By Recipients Starts-->
			<select name="filter_database" class="inputbox" onchange="this.form.submit()">
				<option value=""><?php echo Text::_('Select DataBase'); ?></option>
				<?php echo HTMLHelper::_('select.options', $this->databases, 'value', 'text', $this->state->get('filter.database'), true); ?>
			</select>
		</div>
	</fieldset>
	<table class="adminlist">
		<thead>
			<tr>
				<th>
					<?php echo HTMLHelper::_('grid.checkall'); ?>
				</th>
				<th>
					<?php echo HTMLHelper::_('grid.sort', 'ID', 'ur.username', $orderDirn, $listOrder); ?>
				</th>
    			<th>
					<?php echo HTMLHelper::_('grid.sort', 'Title', 'pp.points', $orderDirn, $listOrder); ?>
				</th>
				<th>
					<?php echo HTMLHelper::_('grid.sort', 'DataBase', 'ui.username', $orderDirn, $listOrder); ?>
				</th>
    			<th>
					<?php echo HTMLHelper::_('grid.sort', 'Query', 'pp.notes', $orderDirn, $listOrder); ?>
				</th>
				<th>
					<?php echo HTMLHelper::_('grid.sort', 'Date Time', 'pp.datetime', $orderDirn, $listOrder); ?>
				</th>	
				<th>
					<?php echo HTMLHelper::_('grid.sort', 'Status', 'published', $orderDirn, $listOrder); ?>
				</th>				
				<th>
					<?php echo Text::_('Export'); ?>
				</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($this->items as $i => $item): ?>	
				<tr class="row<?php echo $i % 2?>" id="id<?php echo $i;?>">					
					<td class="center">
						<?php echo HTMLHelper::_('grid.id', $i, $item->id); ?>
					</td>
					<td class="center">
						<?php echo $this->escape($item->id); ?>
					</td>
    				<td>
						<?php echo $this->escape($item->title); ?>
					</td>
					<td>
						<?php echo $this->escape($item->dbname); ?>
					</td>
    				<td>
						<?php echo $this->escape($item->query); ?>
					</td>
					<td class="center">
						<?php echo $this->escape($item->datetime); ?>
					</td>
					<td class="center">
						<?php 
						echo HTMLHelper::_('jgrid.published', $item->published, $i, 'cannedqueries.', true, 'cb');
						?>
					</td>
					<td class="center">
						<input type="button" class="btn_runquery large" id="export_as_csv" value="Export as CSV">
					</td>
				</tr>
			<?php endforeach ?>
		</tbody>
		<tfoot>
			<tr>
				<td colspan="8">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
	</table>
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $orderDirn; ?>" />
	<?php echo HTMLHelper::_('form.token'); ?>
</form>
