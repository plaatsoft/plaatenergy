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

/**
 * @file
 * @brief contain day energy out report
 */
 
/*
** ---------------------
** PAGES
** ---------------------
*/

function plaatenergy_day_out_energy_page() {

	// input
	global $pid;
	global $eid;

	global $date; 
	global $in_forecast;
	global $graph_width;
	global $graph_height;

        $prev_date = plaatenergy_prev_day($date);
        $next_date = plaatenergy_next_day($date);
		
	list($year, $month, $day) = explode("-", $date);	
	$current_date=mktime(0, 0, 0, $month, $day, $year);  
	
	$i=0;
	$data = "";
	$page = "";
	$value = 0;	
	$total = 0;
	
	// Get last solar measurement
	$sql  = 'select etotal from solar where ';
	$sql .= 'timestamp>="'.$prev_date.' 00:00:00" and timestamp<="'.$prev_date.' 23:59:59" order by timestamp desc limit 0,1';
	$result = plaatenergy_db_query($sql);
	$row = plaatenergy_db_fetch_object($result);
	
	$solar_prev=0;
	if ( isset($row->etotal) ) {
		$solar_prev = $row->etotal;
	}
	 
	if ($eid==EVENT_WATT) {

		$timestamp1 = date("Y-m-d 00:00:00", $current_date);
		$timestamp2 = date("Y-m-d 23:59:59", $current_date);
	
		$sql  = 'select timestamp, etoday, pac FROM solar where timestamp>="'.$timestamp1.'" ';
		$sql .= 'and timestamp<="'.$timestamp2.'" order by timestamp';
		$result = plaatenergy_db_query($sql);

		while ($row = plaatenergy_db_fetch_object($result)) {

			$value=0;
			if ( isset($row->pac)) {
				$value= $row->pac;
				$total = $row->etoday;
			}

			if (strlen($data)>0) {
				$data.=',';
			}
			$data .= "['".substr($row->timestamp,11,5)."',";
			$data .= round($value,2).']';
			$i++;
		}
		$json = "[".$data."]";
		
	} else {
		
		$current_date=mktime(0, 0, 0, $month, $day, $year);
		while ($i<96) {
	
			$timestamp = date("Y-m-d H:i:s", $current_date+(900*$i));
			$sql = 'select etotal FROM solar where timestamp="'.$timestamp.'"';		
			$result = plaatenergy_db_query($sql);
			$row = plaatenergy_db_fetch_object($result);
  
			if ($timestamp>date("Y-m-d H:i:s")) {
				$value=0;
			} else {
				$total = round($value,2);  
			}

			if ( isset($row->etotal)) {
				$value= $row->etotal-$solar_prev;
			}
  
			if (strlen($data)>0) {
				$data.=',';
			}
			$data .= "['".date("H:i", $current_date+(900*$i))."',";
			$data .= round($value,2).']';
		
			$i++;
		}		
		$json = "[['','Levering'],".$data."]";
	}

	
	
	if ($eid==EVENT_WATT) {
	
		$page = '<script type="text/javascript" src="https://www.google.com/jsapi"></script>
		<script type="text/javascript">
      google.load("visualization", "1", {packages:["line"]});
      google.setOnLoadCallback(drawChart);

      function drawChart() {

         var data = new google.visualization.DataTable();
         data.addColumn("string", "Time");
         data.addColumn("number", "Watt");

         data.addRows('.$json.');

         var options = {
           legend: { position: "none" },
           vAxis: {format: "decimal", title:""},
           hAxis: {title:""},
         };

         var chart = new google.charts.Line(document.getElementById("chart_div"));
         chart.draw(data, google.charts.Line.convertOptions(options));
		}
		</script>';
		
	} else {

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
			 isStacked: false
        };

        var data = google.visualization.arrayToDataTable('.$json.');
        var chart = new google.charts.Bar(document.getElementById("chart_div"));
        chart.draw(data, google.charts.Bar.convertOptions(options));
      }
		</script>';
	}
    
	$page .= '<h1>'.t('TITLE_DAY_OUT_KWH', plaatenergy_dayofweek(),$day, $month, $year).'</h1>';
	$page .= '<div id="chart_div" style="width: '.$graph_width.'; height: '.$graph_height.';"></div>';

	$page .= '<div class="remark">';	
	$page .= t('TOTAL_PER_DAY_KWH', $total);
	$page .= '</div>';

	$page .= plaatenergy_navigation_day();

	return $page;
}

/*
** ---------------------
** HANDLER
** ---------------------
*/

function plaatenergy_day_out_energy() {

  /* input */
  global $pid;
  global $eid;
  
   /* Event handler */
  switch ($eid) {
  
     case EVENT_SAVE:
				plaatenergy_day_out_edit_save_event();
				break;

		case EVENT_KWH:
				break;
				
		case EVENT_EURO:
				break;
	}
	
	/* Page handler */
	switch ($pid) {

		case PAGE_DAY_OUT_ENERGY:
			return plaatenergy_day_out_energy_page();
			break;
	}
}

?>