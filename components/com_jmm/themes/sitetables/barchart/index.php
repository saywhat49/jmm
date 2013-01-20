<?php
function filterChartFilter($val){
	if(is_numeric($val)){
		return $val;
	}else{
		return strlen($val);
	}
}
$document->addScript('https://www.google.com/jsapi');
$data=array();
$data[]=$cols;
foreach($rows as $row){
	$tmp=array();
	foreach($row as $key=>$value){
		$tmp[]=(int)$value;
	}
	$data[]=$tmp;
}
$JSONDATA=json_encode($data);
?>
<script type="text/javascript">
      google.load("visualization", "1", {packages:["corechart"]});
      google.setOnLoadCallback(drawChart);
      function drawChart() {
      	//var d=<?php echo $JSONDATA;?>;
      	var data = google.visualization.arrayToDataTable([
          ['Year', 'Sales', 'Expenses'],
          ['2004',  1000,      400],
          ['2005',  1170,      460],
          ['2006',  660,       1120],
          ['2007',  1030,      540]
        ]);
        //var data = google.visualization.arrayToDataTable(d);

        var options = {
          title: 'Company Performance',
          vAxis: {title: 'Year',  titleTextStyle: {color: 'red'}}
        };

        var chart = new google.visualization.BarChart(document.getElementById('chart_div'));
        chart.draw(data, options);
      }
    </script>

    <div id="chart_div" style="width: 900px; height: 500px;"></div>