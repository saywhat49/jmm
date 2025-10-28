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
use Joomla\CMS\Router\Route;

// Charger les assets JavaScript pour Joomla 5
$wa = Factory::getApplication()->getDocument()->getWebAssetManager();
$wa->useScript('form.validate');
$wa->useScript('keepalive');

// Initialisation des objets nécessaires
$app = Factory::getApplication();
$input = $app->input;
?>
<div id="query-status" style="display:none;">
</div>

<form action="<?php echo Route::_('index.php?option=com_jmm&view=createTable'); ?>" method="post" id="adminForm" name="adminForm">
	<table>
			<tr>
				<td>
					<b><?php echo Text::_('Table Name'); ?></b> 
					<input type="text" name="tbl_name" id="tbl_name" class="form-control d-inline-block" style="width: 200px;" placeholder="<?php echo Text::_('Enter Table Name'); ?>" required>
					<input type="button" class="btn btn-primary ms-2" id="add_another_fields" value="<?php echo Text::_('Add Another Fields'); ?>">
				</td>
			</tr>			
	</table>
	
	<table class="table table-striped" id="createtable">
		<thead>
			<tr>
				<th><b><?php echo Text::_('Column'); ?></b></th>
				<th><b><?php echo Text::_('Type'); ?></b></th>
				<th><b><?php echo Text::_('Length'); ?></b></th>
				<th><b><?php echo Text::_('Default'); ?></b></th>
				<th><b><?php echo Text::_('Null'); ?></b></th>
				<th><b><?php echo Text::_('Index'); ?></b></th>
				<th><b><?php echo Text::_('AUTO_INCREMENT'); ?></b></th>
				<th><b><?php echo Text::_('Comments'); ?></b></th>
				<th><b><?php echo Text::_('Action'); ?></b></th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>
					<input type="text" name="field_name[]" class="form-control" placeholder="<?php echo Text::_('Enter column Name'); ?>" required>
				</td>
				<td>
					<select class="form-select" name="field_type[]">
						<option value="INT">INT</option>
						<option value="VARCHAR">VARCHAR</option>
						<option value="TEXT">TEXT</option>
						<option value="DATE">DATE</option>
						<optgroup label="NUMERIC">
							<option value="TINYINT">TINYINT</option>
							<option value="SMALLINT">SMALLINT</option>
							<option value="MEDIUMINT">MEDIUMINT</option>
							<option value="INT">INT</option>
							<option value="BIGINT">BIGINT</option>
							<option value="-">-</option>
							<option value="DECIMAL">DECIMAL</option>
							<option value="FLOAT">FLOAT</option>
							<option value="DOUBLE">DOUBLE</option>
							<option value="REAL">REAL</option>
							<option value="-">-</option>
							<option value="BIT">BIT</option>
							<option value="BOOLEAN">BOOLEAN</option>
							<option value="SERIAL">SERIAL</option>
						</optgroup>
						<optgroup label="DATE and TIME">
							<option value="DATE">DATE</option>
							<option value="DATETIME">DATETIME</option>
							<option value="TIMESTAMP">TIMESTAMP</option>
							<option value="TIME">TIME</option>
							<option value="YEAR">YEAR</option>
						</optgroup>
						<optgroup label="STRING">
							<option value="CHAR">CHAR</option>
							<option value="VARCHAR">VARCHAR</option>
							<option value="-">-</option>
							<option value="TINYTEXT">TINYTEXT</option>
							<option value="TEXT">TEXT</option>
							<option value="MEDIUMTEXT">MEDIUMTEXT</option>
							<option value="LONGTEXT">LONGTEXT</option>
							<option value="-">-</option>
							<option value="BINARY">BINARY</option>
							<option value="VARBINARY">VARBINARY</option>
							<option value="-">-</option>
							<option value="TINYBLOB">TINYBLOB</option>
							<option value="MEDIUMBLOB">MEDIUMBLOB</option>
							<option value="BLOB">BLOB</option>
							<option value="LONGBLOB">LONGBLOB</option>
							<option value="-">-</option>
							<option value="ENUM">ENUM</option>
							<option value="SET">SET</option>
						</optgroup>
						<optgroup label="SPATIAL">
							<option value="GEOMETRY">GEOMETRY</option>
							<option value="POINT">POINT</option>
							<option value="LINESTRING">LINESTRING</option>
							<option value="POLYGON">POLYGON</option>
							<option value="MULTIPOINT">MULTIPOINT</option>
							<option value="MULTILINESTRING">MULTILINESTRING</option>
							<option value="MULTIPOLYGON">MULTIPOLYGON</option>
							<option value="GEOMETRYCOLLECTION">GEOMETRYCOLLECTION</option>
						</optgroup>
					</select>
				</td>
				<td>
					<input type="text" name="field_length[]" class="form-control" placeholder="" style="width:80px;">
				</td>
				<td>
					<select name="field_default_type[]" class="form-select">
						<option value="NONE"><?php echo Text::_('None'); ?></option>
						<option value="USER_DEFINED"><?php echo Text::_('As defined:'); ?></option>
						<option value="NULL">NULL</option>
						<option value="CURRENT_TIMESTAMP">CURRENT_TIMESTAMP</option>
					</select>
				</td>
				<td>
					<input name="field_null[]" type="checkbox" value="NULL" class="form-check-input">
				</td>
				<td>
					<select name="field_key[]" class="form-select">
						<option value="none">---</option>
						<option value="primary" title="Primary">PRIMARY</option>
						<option value="unique" title="Unique">UNIQUE</option>
						<option value="index" title="Index">INDEX</option>
						<option value="fulltext" title="Fulltext">FULLTEXT</option>
					</select>
				</td>
				<td>
					<input name="field_extra[]" type="checkbox" value="AUTO_INCREMENT" class="form-check-input">
				</td>
				<td>
					<textarea name="field_comments[]" class="form-control" placeholder="<?php echo Text::_('Enter Comments...'); ?>" rows="2"></textarea>
				</td>
				<td>
					<span class="text-muted">-</span>
				</td>
			</tr>
		</tbody>			
	</table>
	
	<div class="row mt-4">
		<div class="col-md-4">
			<div class="form-group mb-3">
				<label for="tbl_comments"><b><?php echo Text::_('Table comments:'); ?></b></label>
				<textarea name="tbl_comments" id="tbl_comments" class="form-control" placeholder="<?php echo Text::_('Enter table Comments'); ?>" rows="3"></textarea>
			</div>
		</div>
		<div class="col-md-4">
			<div class="form-group mb-3">
				<label for="tbl_type"><b><?php echo Text::_('Storage Engine:'); ?></b></label>
				<select name="tbl_type" id="tbl_type" class="form-select">
					<option value="MEMORY" title="<?php echo Text::_('Hash based, stored in memory, useful for temporary tables'); ?>">
						MEMORY
					</option>
					<option value="CSV" title="<?php echo Text::_('CSV storage engine'); ?>">
						CSV
					</option>
					<option value="MRG_MYISAM" title="<?php echo Text::_('Collection of identical MyISAM tables'); ?>">
						MRG_MYISAM
					</option>
					<option value="BLACKHOLE" title="<?php echo Text::_('/dev/null storage engine (anything you write to it disappears)'); ?>">
						BLACKHOLE
					</option>
					<option value="MyISAM" title="<?php echo Text::_('MyISAM storage engine'); ?>">
						MyISAM
					</option>
					<option value="InnoDB" title="<?php echo Text::_('Supports transactions, row-level locking, and foreign keys'); ?>" selected="selected">
						InnoDB
					</option>
					<option value="ARCHIVE" title="<?php echo Text::_('Archive storage engine'); ?>">
						ARCHIVE
					</option>
					<option value="PERFORMANCE_SCHEMA" title="<?php echo Text::_('Performance Schema'); ?>">
						PERFORMANCE_SCHEMA
					</option>
				</select>
			</div>
		</div>
		<div class="col-md-4">
			<div class="form-group mb-3">
				<label>&nbsp;</label><br>
				<button type="button" class="btn btn-success" id="create_table_structure">
					<?php echo Text::_('Create Table'); ?>
				</button>
			</div>
		</div>
	</div>
	
	<!-- Champs cachés pour le formulaire -->
	<input type="hidden" name="option" value="com_jmm" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="format" value="json" />
	<?php echo HTMLHelper::_('form.token'); ?>
</form>
