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
** PARAMETERS
** ---------------------
*/

$date = plaatenergy_post("date", date('Y-m'));

/*
** ---------------------
** PAGE
** ---------------------
*/

function plaatenergy_month_out_energy_max_page() {

   // input
	
	global $pid;
	global $eid;
	
	global $graph_width;
	global $graph_height;
	
	global $date;  
	
	$prev_date = plaatenergy_prev_month($date);
	$next_date = plaatenergy_next_month($date);
	
	list($year, $month) = explode("-", $date);	
	
	$data="";
	$value = 0;
	$max = 0;
	$type = 0;

	for($d=1; $d<=31; $d++) {
		
		$time=mktime(12, 0, 0, $month, $d, $year);  
        
		if (date('m', $time)==$month) {
			$timestamp1=date('Y-m-d 00:00:00', $time);
			$timestamp2=date('Y-m-d 23:59:59', $time);

			$sql = 'select max(pac) as pac FROM solar where timestamp>="'.$timestamp1.'" and timestamp<="'.$timestamp2.'"';

			$result = plaatenergy_db_query($sql);
			$row = plaatenergy_db_fetch_object($result);
	
			if (isset($row->pac)) {
				$value = $row->pac;
			} else {
				$value=0;
			}

			if($value>$max) {
				$max=$value;
			}
	
			if (strlen($data)>0) {
				$data.=',';
			}

			$data .= "['".date("d-m", $time)."',";
			$data .= $value."]";
		}
	}

	$json = "[['','".t('LINK_WATT')."'],".$data."]";

	$page = '<script type="text/javascript" src="https://www.google.com/jsapi"></script>
    <script type="text/javascript">
      google.load("visualization", "1", {packages:["bar"]});
      google.setOnLoadCallback(drawChart);
      function drawChart() {

       var options = {
          bars: "vertical",
          bar: {groupWidth: "90%"},
          legend: { position: "none" },
          vAxis: {format: "decimal" },
          isStacked:false
        };

        var data = google.visualization.arrayToDataTable('.$json.');
        var chart = new google.charts.Bar(document.getElementById("chart_div));
        chart.draw(data, google.charts.Bar.convertOptions(options));
      }
    </script>';

	$page .= '<h1>'.t('TITLE_MONTH_PEAK_OUT_KWH', $month, $year).'</h1>';
	$page .= '<div id="chart_div" style="width: '.$graph_width.'; height: '.$graph_height.';"></div>';

	text_banner( t('MAX_PEAK_ENERGY', $max));
	
	$page .= '<div class="nav">';
	$page .= plaatenergy_link('pid='.$pid.'&date='.$prev_date.'&eid='.EVENT_PREV,t('LINK_PREV_MONTH'));
	$page .= plaatenergy_link('pid='.PAGE_HOME, t('LINK_HOME'));
	$page .= plaatenergy_link('pid='.$pid.'&date='.$next_date.'&eid='.EVENT_NEXT,t('LINK_NEXT_MONTH'));	
	$page .= '</div>';
	
	return $page;
}

/*
** ---------------------
** HANDLER
** ---------------------
*/

function plaatenergy_month_out_energy_max() {

  /* input */
  global $eid;
  global $pid;
  
  /* Event handler */
  switch ($eid) {
  
		case EVENT_PREV:
				break;
				
		case EVENT_NEXT:
				break;
	}

	/* Page handler */
	switch ($pid) {

		case PAGE_MONTH_OUT_ENERGY_MAX:
			echo plaatenergy_month_out_energy_max_page();
			break;
	}
}

/*
** ---------------------
** THE END
** ---------------------
*/

?>
