<?php
function filterChartFilter($val){
	if(is_numeric($val)){
		return $val;
	}else{
		return strlen($val);
	}
}
$document->addScript('https://www.google.com/jsapi');

function drawPieChart($params,$row,$cols,$chartId='1'){
$document =JFactory::getDocument();
$chart_div='chart_div_'.$chartId;
$Callback='drawChart_'.$chartId;
$js='google.load("visualization", "1", {packages:["corechart"]});';
$js.='google.setOnLoadCallback('.$Callback.');';
$js.='function '.$Callback.'() {';
$js.='var data = google.visualization.arrayToDataTable([';
$js.=" ['".$params->get('page_title')."', '".$params->get('page_title')."'],";
$dataArray='';
foreach($cols as $col){
		$dataArray.="['".$col."', ".filterChartFilter($row[$col])."],";
}
$js.=rtrim($dataArray,',');
$js.=']);';
$js.='var options = {';
$js.="title: '".$params->get('page_title')."'";
$js.=' };';
$js.='var chart = new google.visualization.PieChart(document.getElementById("'.$chart_div.'"));';
$js.='chart.draw(data, options);';
$js.="}";
$document->addScriptDeclaration($js);
return '<div id="'.$chart_div.'" style="width: 900px; height: 500px;"></div>';
}
$i=1;
foreach($rows as $row){
	echo drawPieChart($params,$rows[0],$cols,$i);
	$i++;
}
?>

