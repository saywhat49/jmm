<?php
/**
 * @package		JMM
 * @link		http://adidac.github.com/jmm/index.html
 * @license		GNU/GPL
 * @copyright	Biswarup Adhikari
*/
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;

/**
 * Filter chart value
 * 
 * @param   mixed  $val  Value to filter
 * 
 * @return  mixed  Filtered value
 */
function filterChartFilter($val) {
	if (is_numeric($val)) {
		return $val;
	} else {
		return strlen($val);
	}
}

$document = Factory::getDocument();
$document->addScript('https://www.google.com/jsapi');

/**
 * Draw a pie chart
 * 
 * @param   object   $params   Component parameters
 * @param   array    $row      Data row
 * @param   array    $cols     Columns
 * @param   integer  $chartId  Chart ID
 * 
 * @return  string   HTML for the chart
 */
function drawPieChart($params, $row, $cols, $chartId = '1') {
	$document = Factory::getDocument();
	$chart_div = 'chart_div_' . $chartId;
	$Callback = 'drawChart_' . $chartId;
	
	$js = 'google.load("visualization", "1", {packages:["corechart"]});';
	$js .= 'google.setOnLoadCallback(' . $Callback . ');';
	$js .= 'function ' . $Callback . '() {';
	$js .= 'var data = google.visualization.arrayToDataTable([';
	$js .= " ['" . $params->get('page_title') . "', '" . $params->get('page_title') . "'],";
	
	$dataArray = '';
	foreach ($cols as $col) {
		$dataArray .= "['" . $col . "', " . filterChartFilter($row[$col]) . "],";
	}
	
	$js .= rtrim($dataArray, ',');
	$js .= ']);';
	$js .= 'var options = {';
	$js .= "title: '" . $params->get('page_title') . "'";
	$js .= ' };';
	$js .= 'var chart = new google.visualization.PieChart(document.getElementById("' . $chart_div . '"));';
	$js .= 'chart.draw(data, options);';
	$js .= "}";
	
	$document->addScriptDeclaration($js);
	
	return '<div id="' . $chart_div . '" style="width: 900px; height: 500px;"></div>';
}

// Draw charts for each row
$i = 1;
foreach ($rows as $row) {
	echo drawPieChart($params, $rows[0], $cols, $i);
	$i++;
}
?>
