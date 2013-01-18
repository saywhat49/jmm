<?php
defined('_JEXEC') or die('Restricted access');
?>
<form action="index.php" method="post" id="adminForm" name="adminForm">
	<table>
			<tr>
				<td>
					<b>Table Name</b> <input type="text" name="tbl_name" id="tbl_name" placeholder="Enter Table Name">
					<input type="button" class="add_points_btn" id="add_another_fields" value="Add Another Fields">
				</td>
			</tr>			
	</table>
	<table class="bordered" id="createtable">
		<thead>
			<tr>
				<td><b>Column</b></td>
				<td><b>Type</b></td>
				<td><b>Length</b></td>
				<td><b>Default</b></td>
				<td><b>Null</b></td>
				<td><b>Index</b></td>
				<td><b>AUTO_INCREMENT</b></td>
				<td><b>Comments</b></td>
			</tr>
		</thead>
		<tbody>
			
			<tr>
				<td><input type="text" name="field_name[]" id="field_name[]" placeholder="Enter column Name"></td>
				<td><select class="field_type[]" name="field_type[]" id="field_type[]"><option value="INT">INT</option><option value="VARCHAR">VARCHAR</option><option value="TEXT">TEXT</option><option value="DATE">DATE</option><optgroup label="NUMERIC"><option value="TINYINT">TINYINT</option><option value="SMALLINT">SMALLINT</option><option value="MEDIUMINT">MEDIUMINT</option><option value="INT">INT</option><option value="BIGINT">BIGINT</option><option value="-">-</option><option value="DECIMAL">DECIMAL</option><option value="FLOAT">FLOAT</option><option value="DOUBLE">DOUBLE</option><option value="REAL">REAL</option><option value="-">-</option><option value="BIT">BIT</option><option value="BOOLEAN">BOOLEAN</option><option value="SERIAL">SERIAL</option></optgroup><optgroup label="DATE and TIME"><option value="DATE">DATE</option><option value="DATETIME">DATETIME</option><option value="TIMESTAMP">TIMESTAMP</option><option value="TIME">TIME</option><option value="YEAR">YEAR</option></optgroup><optgroup label="STRING"><option value="CHAR">CHAR</option><option value="VARCHAR">VARCHAR</option><option value="-">-</option><option value="TINYTEXT">TINYTEXT</option><option value="TEXT">TEXT</option><option value="MEDIUMTEXT">MEDIUMTEXT</option><option value="LONGTEXT">LONGTEXT</option><option value="-">-</option><option value="BINARY">BINARY</option><option value="VARBINARY">VARBINARY</option><option value="-">-</option><option value="TINYBLOB">TINYBLOB</option><option value="MEDIUMBLOB">MEDIUMBLOB</option><option value="BLOB">BLOB</option><option value="LONGBLOB">LONGBLOB</option><option value="-">-</option><option value="ENUM">ENUM</option><option value="SET">SET</option></optgroup><optgroup label="SPATIAL"><option value="GEOMETRY">GEOMETRY</option><option value="POINT">POINT</option><option value="LINESTRING">LINESTRING</option><option value="POLYGON">POLYGON</option><option value="MULTIPOINT">MULTIPOINT</option><option value="MULTILINESTRING">MULTILINESTRING</option><option value="MULTIPOLYGON">MULTIPOLYGON</option><option value="GEOMETRYCOLLECTION">GEOMETRYCOLLECTION</option></optgroup>    </select></td>
				<td><input type="text" name="field_length[]" id="field_length[]" placeholder="" style="width:45px;"></td>
				<td><select name="field_default_type[]"><option value="NONE">None</option><option value="USER_DEFINED">As defined:</option><option value="NULL">NULL</option><option value="CURRENT_TIMESTAMP">CURRENT_TIMESTAMP</option></select></td>
				<td><input name="field_null[]" id="field_null[]" type="checkbox" value="NULL"></td>
				<td><select name="field_key[]" id="field_key[]"><option value="none">---</option><option value="primary" title="Primary">PRIMARY</option><option value="unique" title="Unique">UNIQUE</option><option value="index" title="Index">INDEX</option><option value="fulltext" title="Fulltext">FULLTEXT</option></select></td>
				<td><input name="field_extra[]" id="field_extra[]" type="checkbox" value="AUTO_INCREMENT"></td>
				<td><textarea name="field_comments[]" id="field_comments[]" placeholder="Enter Comments..."></textarea></td>
			</tr>
		</tbody>			
	</table>
	
	<table>
			<tr>
				<td valign="top">
					<b>Table comments: </b>
					<textarea name="tbl_comments" id="tbl_comments" placeholder="Enter table Comments"></textarea>
				</td>
				<td>
					<b>Storage Engine:</b>
					<select name="tbl_type">
					    <option value="MEMORY" title="Hash based, stored in memory, useful for temporary tables">
					        MEMORY
					    </option>
					    <option value="CSV" title="CSV storage engine">
					        CSV
					    </option>
					    <option value="MRG_MYISAM" title="Collection of identical MyISAM tables">
					        MRG_MYISAM
					    </option>
					    <option value="BLACKHOLE" title="/dev/null storage engine (anything you write to it disappears)">
					        BLACKHOLE
					    </option>
					    <option value="MyISAM" title="MyISAM storage engine">
					        MyISAM
					    </option>
					    <option value="InnoDB" title="Supports transactions, row-level locking, and foreign keys" selected="selected">
					        InnoDB
					    </option>
					    <option value="ARCHIVE" title="Archive storage engine">
					        ARCHIVE
					    </option>
					    <option value="PERFORMANCE_SCHEMA" title="Performance Schema">
					        PERFORMANCE_SCHEMA
					    </option>
					</select>
				</td>
				<td>
						<input type="button" class="add_points_btn" id="create_table_structure" value="Create Table">
				</td>
			</tr>
	</table>
	<input type="hidden" name="option" value="com_jmm" />
	<input type="hidden" name="task" value="createTable.createTableStructure" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
	<?php echo JHtml::_('form.token'); ?>
</form>