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

// Initialisation des objets nécessaires
$app = Factory::getApplication();
$input = $app->input;

// Récupération des paramètres de tri
$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn = $this->escape($this->state->get('list.direction')); // Correction du nom de variable
?>
<form action="index.php?option=com_jmm&amp;view=databases" method="post" id="adminForm" name="adminForm">
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
		</div>
	</fieldset>
	<table class="adminlist">
		<thead>
			<tr>
				<?php
				if (isset($this->items[0]) && is_array($this->items[0])) {
					foreach($this->items[0] as $col => $val) {
				?>
				<th>
					<?php echo HTMLHelper::_('grid.sort', $col, $col, $listDirn, $listOrder); ?>
				</th>
				<?php
					}
				}
				?>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($this->items as $i => $item): ?>	
				<tr class="row<?php echo $i % 2?>">
					<?php
					foreach($this->items[$i] as $col => $val) {
					?>
					<td>
						<?php echo $val; ?>
					</td>
					<?php
					}
					?>
				</tr>
			<?php endforeach ?>
		</tbody>
		<tfoot>
			<tr>
				<td colspan="<?php echo (isset($this->items[0]) && is_array($this->items[0])) ? count($this->items[0]) : 4; ?>">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
	</table>
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
	<?php echo HTMLHelper::_('form.token'); ?>
</form>
