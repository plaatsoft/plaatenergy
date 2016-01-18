<?php

/* 
**  ===========
**  PlaatEnergy
**  ===========
**
**  Created by wplaat
**
**  For more information visit the following website.
**  Website : www.plaatsoft.nl 
**
**  Or send an email to the following address.
**  Email   : info@plaatsoft.nl
**
**  All copyrights reserved (c) 2008-2016 PlaatSoft
*/
 
/*
** ---------------------
** PAGE
** ---------------------
*/

function plaatenergy_day_humidity_page() {

   // input
	global $pid;

	global $graph_width;
	global $graph_height;
	
	global $date;  
	
	$prev_date = plaatenergy_prev_day($date);
	$next_date = plaatenergy_next_day($date);
	
	list($year, $month, $day) = explode("-", $date);	
	$current_date = mktime(0, 0, 0, $month, $day, $year);
	
	$i=0;
	$data="";
	$value=0;
	
	while ($i<97) {

		$timestamp = date("Y-m-d H:i:s", $current_date+(900*$i));
		$sql = 'select humidity from weather where timestamp="'.$timestamp.'"';
	
		$result = plaatenergy_db_query($sql);
		$row = plaatenergy_db_fetch_object($result);
		
		if ($timestamp>date("Y-m-d H:i:s")) {
			$value=0;
		} else if ( isset($row->humidity)) {
			$value= $row->humidity;
		}

		if (strlen($data)>0) {
			$data.=',';
		}
		$data .= "['".date("H:i", $current_date+(900*$i))."',";
		$data .= round($value,1).']';

		$i++;
	}

	$json = "[['','".t('HUMIDITY')."'],".$data."]";

	$page = '
    <script type="text/javascript" src="https://www.google.com/jsapi"></script>
    <script type="text/javascript">
      google.load("visualization", "1", {packages:["bar"]});
      google.setOnLoadCallback(drawChart);
      function drawChart() {

        var options = {
          bars: "vertical",
          bar: {groupWidth: "90%"},
          legend: { position: "none" },
          vAxis: {format: "decimal", baseline:0},
        };

        var data = google.visualization.arrayToDataTable('.$json.');
        var chart = new google.charts.Bar(document.getElementById("chart_div"));
        chart.draw(data, google.charts.Bar.convertOptions(options));
      }
    </script>';
	 
	$page .= '<h1>'.t('TITLE_DAY_HUMIDITY', $day, $month, $year).'</h1>';
	$page .= '<div id="chart_div" style="width: '.$graph_width.'; height: '.$graph_height.';"></div>';
		
	$page .= '<div class="nav">';
	$page .= plaatenergy_link('pid='.$pid.'&date='.$prev_date ,t('LINK_PREV_DAY'));
	$page .= plaatenergy_link('pid='.PAGE_HOME, t('LINK_HOME'));
	$page .= plaatenergy_link('pid='.$pid.'&date='.$next_date ,t('LINK_NEXT_DAY'));	
	$page .= '</div>';
	
	return $page;
}

/*
** ---------------------
** HANDLER
** ---------------------
*/

function plaatenergy_day_humidity() {

  /* input */
  global $pid;

  /* Page handler */
  switch ($pid) {

     case PAGE_DAY_HUMIDITY:
        echo plaatenergy_day_humidity_page();
        break;
  }
}

/*
** ---------------------
** THE END
** ---------------------
*/

?>
