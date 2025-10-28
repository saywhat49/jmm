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

// Récupération des paramètres
$selecteddb = $input->getString('dbname', $app->get('db'));
$selectedtable = $input->getString('tbl', '');
$filter_chnagedatabase = $input->getString('filter_chnagedatabase', $selecteddb, 'get');
$filter_chnagetable = $input->getString('filter_chnagetable', $selectedtable, 'get');
?>
<form action="index.php?option=com_jmm&amp;view=insert&amp;layout=edit&amp;id=<?php echo $this->item->id; ?>" method="POST" name="adminForm" class="form-validate">
<fieldset id="filter-bar">
		<div class="filter-select fltrt">
			<label for="filter_chnagedatabase"><?php echo Text::_('Select DataBase'); ?></label>
			<select name="filter_chnagedatabase" id="filter_chnagedatabase" class="inputbox" onchange="this.form.submit()">
				<option value=""><?php echo Text::_('Select Database'); ?></option>
				<?php
				echo HTMLHelper::_('select.options', $this->databases, 'value', 'text', $filter_chnagedatabase, true);
				?>
			</select>			
		</div>
		<div class="filter-select fltrt">
			<label for="filter_chnagetable"><?php echo Text::_('Select Table'); ?></label>
			<select name="filter_chnagetable" id="filter_chnagetable" class="inputbox" onchange="this.form.submit()">
				<option value=""><?php echo Text::_('Select Table'); ?></option>
				<?php
				echo HTMLHelper::_('select.options', $this->Tables, 'value', 'text', $filter_chnagetable, true);
				?>
			</select>			
		</div>
</fieldset>
<div class="width-60 fltlft">
	<fieldset class="adminform">
		<?php 
		if (!empty($selectedtable)) {
		?>
		<p><?php echo Text::_('Insert Into Table'); ?> <b><?php echo $selectedtable; ?></b> <?php echo Text::_('On DataBase'); ?>  <b><?php echo $selecteddb; ?></b> </p>
		<ul class="adminformList">
		<?php foreach($this->form->getFieldset() as $field): ?>
				<?php echo $field->input; ?>
			<?php endforeach ?>
		</ul>
		<?php 
		} else {
			echo '<h3>' . Text::_('Please Select Table Before Insert Data') . '</h3>';
		}
		?>
	</fieldset>
</div>
<input type="hidden" name="view" value="insert">
<input type="hidden" name="task" value="">
<input type="hidden" name="tbl" value="<?php echo $selectedtable; ?>">
<input type="hidden" name="dbname" value="<?php echo $selecteddb; ?>">
<?php echo HTMLHelper::_('form.token'); ?>
</form>
