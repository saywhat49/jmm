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

// Initialisation des objets nécessaires
$app = Factory::getApplication();
$input = $app->input;

// Récupération de la base de données sélectionnée
if($input->exists('dbname')) {
	$dbname = $input->getString('dbname');
} else {
	$dbname = $app->get('db');
}

// Récupérer d'autres valeurs d'entrée
$query = $input->getString('query', '');
$filter_cannedqueries = $input->getString('filter_cannedqueries', '');
$selecteddb = $input->getString('dbname', $app->get('db'));
$filter_chnagedatabase = $input->getString('filter_chnagedatabase', $selecteddb, 'get');
?>
<form method="post" id="adminForm" name="adminForm">
<fieldset id="filter-bar">
	<div class="filter-search fltlft">
		<label for="filter_cannedqueries">Run Canned Queries OR Site Tables Queries</label>
			<select name="filter_cannedqueries" id="filter_cannedqueries" class="inputbox" onchange="document.getElementById('filter_chnagedatabase').value='';document.getElementById('query').value=document.getElementById('filter_cannedqueries').value;this.form.submit()">
				<option value="">Select Query</option>
				 <optgroup label="-- Canned Queries -- " id="opt-canned">
				<?php
				echo HTMLHelper::_('select.options', $this->cannedQueries, 'value', 'text', $filter_cannedqueries, true);
				?>
				</optgroup>
				
				 <optgroup label="-- Site Tables Queries -- " id="opt-stbl">
				<?php
				echo HTMLHelper::_('select.options', $this->siteTables, 'value', 'text', $filter_cannedqueries, true);
				?>
				</optgroup>
			</select>	
	</div>
	<div class="filter-select fltrt">
		<label for="filter_chnagedatabase">Select DataBase</label>
		<select name="filter_chnagedatabase" id="filter_chnagedatabase" class="inputbox" onchange="document.getElementById('query').value='';this.form.submit()">
			<option value="">Select Database</option>
			<?php
			echo HTMLHelper::_('select.options', $this->databases, 'value', 'text', $filter_chnagedatabase, true);
			?>
		</select>			
	</div>
</fieldset>
<textarea placeholder="Enter your SQL Quiries......" rows="10" cols="150" id="query" name="query"><?php echo $query; ?></textarea><br>
<input type="submit" class="btn_runquery large" value="Run Query">
<input type="hidden" id="currentdb" name="currentdb" value="<?php echo $dbname; ?>">
<?php
if($input->getMethod() === 'POST' && $input->exists('query')) {
?>
<input type="button" class="btn_runquery large" id="save_as_canned_query" value="Save as Canned Query">
<input type="button" class="btn_runquery large" id="save_as_site_table" value="Save as Site Table">
<input type="button" class="btn_runquery large" id="export_as_csv" value="Export as CSV">
<?php
}
?>
<div id="loading-icon" class="loading-icon"></div>
	<?php
	if($this->items && count($this->items) > 0) {
		?>
		<hr>
	<table class="adminlist">
		<thead>
			<tr>
				<?php
				foreach($this->items[0] as $col => $val) {
				?>
				<th>
					<?php echo $col; ?>
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
					$z = 0;
					foreach($this->items[$i] as $col => $val) {
					?>
					<?php
					if(count($this->items[$i]) > 4 && $z != 0) {
						echo '<td align="center">';
					} else {						
						echo '<td>';
					}
					?>
					
						<?php echo $val; ?>
					</td>
					<?php
					$z++;
					}
					?>
					
				</tr>
				
			<?php endforeach ?>
		</tbody>
	</table>
	<?php
	}
	?>
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<?php echo HTMLHelper::_('form.token'); ?>
</form>
