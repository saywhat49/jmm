<?php
/**
 * Add Your Custom PHP Code
 * Your Have access to this following variables
 * For Columns $col (ARRAY)
 * For Data Rows $rows (ARRAY)
 * If You want to hide the pagination then $this->defaultPagination=false;
 * Template Base URL $this->templateBaseURL  
 * Access Parameters $this->params;
 * Access Pagination Object $this->pagination
 * 
 */
defined('_JEXEC') or die('Restricted access');

/**
 * Load google charts
 */
$document->addScript('https://www.google.com/jsapi');

function total($params,$rows,$cols) {
$total=0;
	foreach ($rows as $i => $row) {
		$p=0;
		foreach ($row as $col => $val) {
			if ($p%2) {
				$total=$total+intval($val);
			}
		$p++;
		}
	}
	return $total;
}

echo "<h3>Répartition des ".total($params,$rows,$cols)." sociétés connues</h3>\n";

function filterChartFilter($val){
	if(is_numeric($val)){
		return $val;
	}else{
		return strlen($val);
	}
}


function drawPieChart($params,$rows,$cols) {
	$document =JFactory::getDocument();
	$chart_div='chart_div';

	$js='google.load("visualization", "1", {packages:["corechart"]});';
	$js.='google.setOnLoadCallback(drawChart);';

	$js.='function drawChart() {';
	$js.='var data = google.visualization.arrayToDataTable([';

	$js.='['; 
	foreach($cols as $col) {
		$js.='"'.$col.'",';
	}
	$js=rtrim($js,',');
    $js.='],';

/*
	$js.='["Département","Nombre"],';
*/


    $dataArray='';

	foreach ($rows as $i => $row) {
		$dataArray.=' [';
		$p=0;
		foreach ($row as $col => $val) {
			if ($p%2) {
				$dataArray.=$val.',';
			}else{
				$dataArray.='"'.$val.'",';
			}
		$p++;
		}
		$dataArray=rtrim($dataArray,',');
		$dataArray.="],";
	}
	$dataArray=rtrim($dataArray,',');
/*

	$dataArray.='["37",38],';
	$dataArray.='["41",1],';
	$dataArray.='["44",3],';
	$dataArray.='["49",308],';
	$dataArray.='["53",1],';
	$dataArray.='["72",35],';
*/
	$js.=$dataArray;
	$js.="]); ";

	$js.='var options = {';
	$js.='title: "'.$params->get('page_title').'",';
	$js.='is3D: true';
	$js.=' };';
	
	$js.='var chart = new google.visualization.PieChart(document.getElementById("'.$chart_div.'"));';
	$js.='chart.draw(data, options);';
	$js.="}";
/*echo $js.'<br>';*/
	$document->addScriptDeclaration($js);
	return '<div id="'.$chart_div.'" style="width: 900px; height: 500px;"></div>';
	}

	echo drawPieChart($params,$rows,$cols);

?>
<table class="bordered">
	<thead>
		<tr>
			<?php
			foreach($cols as $col){
				echo '<th>'.$col.'</th>';
			}
			?>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($rows as $i => $row): ?>

		<tr class="row<?php echo $i % 2?>">

			<?php
			foreach ($row as $col => $val) {
				echo '<td>' . $val . '</td>';
			}
			?>
		</tr>

		<?php endforeach ?>
	</tbody>
</table>

