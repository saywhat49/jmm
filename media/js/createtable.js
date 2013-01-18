var JQ = jQuery.noConflict();
JQ('document').ready(function(){
	/**
	 * Get FieldProperty Froms
	 */
	function getFieldPropertyInputBox(){
		var fieldProperty='<tr>';
			fieldProperty+='<td><input type="text" name="field_name[]" id="field_name[]" placeholder="Enter column Name"></td>';
			fieldProperty+='<td><select class="field_type[]" name="field_type[]" id="field_type[]"><option value="INT">INT</option><option value="VARCHAR">VARCHAR</option><option value="TEXT">TEXT</option><option value="DATE">DATE</option><optgroup label="NUMERIC"><option value="TINYINT">TINYINT</option><option value="SMALLINT">SMALLINT</option><option value="MEDIUMINT">MEDIUMINT</option><option value="INT">INT</option><option value="BIGINT">BIGINT</option><option value="-">-</option><option value="DECIMAL">DECIMAL</option><option value="FLOAT">FLOAT</option><option value="DOUBLE">DOUBLE</option><option value="REAL">REAL</option><option value="-">-</option><option value="BIT">BIT</option><option value="BOOLEAN">BOOLEAN</option><option value="SERIAL">SERIAL</option></optgroup><optgroup label="DATE and TIME"><option value="DATE">DATE</option><option value="DATETIME">DATETIME</option><option value="TIMESTAMP">TIMESTAMP</option><option value="TIME">TIME</option><option value="YEAR">YEAR</option></optgroup><optgroup label="STRING"><option value="CHAR">CHAR</option><option value="VARCHAR">VARCHAR</option><option value="-">-</option><option value="TINYTEXT">TINYTEXT</option><option value="TEXT">TEXT</option><option value="MEDIUMTEXT">MEDIUMTEXT</option><option value="LONGTEXT">LONGTEXT</option><option value="-">-</option><option value="BINARY">BINARY</option><option value="VARBINARY">VARBINARY</option><option value="-">-</option><option value="TINYBLOB">TINYBLOB</option><option value="MEDIUMBLOB">MEDIUMBLOB</option><option value="BLOB">BLOB</option><option value="LONGBLOB">LONGBLOB</option><option value="-">-</option><option value="ENUM">ENUM</option><option value="SET">SET</option></optgroup><optgroup label="SPATIAL"><option value="GEOMETRY">GEOMETRY</option><option value="POINT">POINT</option><option value="LINESTRING">LINESTRING</option><option value="POLYGON">POLYGON</option><option value="MULTIPOINT">MULTIPOINT</option><option value="MULTILINESTRING">MULTILINESTRING</option><option value="MULTIPOLYGON">MULTIPOLYGON</option><option value="GEOMETRYCOLLECTION">GEOMETRYCOLLECTION</option></optgroup>    </select></td>';
			fieldProperty+='<td><input type="text" name="field_length[]" id="field_length[]" placeholder="" style="width:45px;"></td>';
			fieldProperty+='<td><select name="field_default_type[]"><option value="NONE">None</option><option value="USER_DEFINED">As defined:</option><option value="NULL">NULL</option><option value="CURRENT_TIMESTAMP">CURRENT_TIMESTAMP</option></select></td>';
			fieldProperty+='<td><input name="field_null[]" id="field_null[]" type="checkbox" value="NULL"></td>';
			fieldProperty+='<td><select name="field_key[]" id="field_key[]"><option value="none">---</option><option value="primary" title="Primary">PRIMARY</option><option value="unique" title="Unique">UNIQUE</option><option value="index" title="Index">INDEX</option><option value="fulltext" title="Fulltext">FULLTEXT</option></select></td>';
			fieldProperty+='<td><input name="field_extra[]" id="field_extra[]" type="checkbox" value="AUTO_INCREMENT"></td>';
			fieldProperty+='<td><textarea name="field_comments[]" id="field_comments[]" placeholder="Enter Comments..."></textarea></td>';
			fieldProperty+='</tr>';	
		return fieldProperty;
	}
	/**
	 * On Click Add Another Fields
	 */
	JQ('#add_another_fields').live('click',(function(){		
		var filedProperty=getFieldPropertyInputBox();
		JQ('#createtable tr:last').after(filedProperty);
	}));

	/**
	 * On Click Create Table Structure
	 */
	
	JQ('#create_table_structure').live('click',(function(){
		var data=JQ('#adminForm').serialize();
		JQ.post('index.php?option=com_jmm&task=createTable.createTableStructure',data,function(res){
			var data=JSON.parse(res);
			JQ('#query-status').html(data.msg);
			if(data.status){
				JQ('#query-status').removeClass('fail-msg');
				JQ('#query-status').addClass('sucess-msg');
				JQ('#query-status').fadeIn(500);
			}else{
				JQ('#query-status').removeClass('sucess-msg');
				JQ('#query-status').addClass('fail-msg');
				JQ('#query-status').fadeIn(500);

			}
		});

	}));



});