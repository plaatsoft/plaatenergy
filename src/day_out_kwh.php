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
	global $out_forecast;

	$energy_delivery_forecast = plaatenergy_db_get_config_item('energy_delivery_forecast');
	$low_delivered_value = plaatenergy_db_get_config_item('meter_reading_delivered_low');
	$normal_delivered_value = plaatenergy_db_get_config_item('meter_reading_delivered_normal');
	$solar_meter_vendor = plaatenergy_db_get_config_item('solar_meter_vendor');

	$prev_date = plaatenergy_prev_day($date);
	$next_date = plaatenergy_next_day($date);
		
	list($year, $month, $day) = explode("-", $date);	
	$day = ltrim($day ,'0');
	$month = ltrim($month ,'0');
	$current_date = mktime(0, 0, 0, $month, $day, $year);  
	
	$i=0;
	$data = "";
	$page = "";
	$value = 0;	
	$total = 0;
	
	// Get last energy measurement 
	$sql  = 'select max(low_delivered) as low_delivered, max(normal_delivered) as normal_delivered from energy1 where ';
	$sql .= 'timestamp>="'.$prev_date.' 00:00:00" and timestamp<="'.$prev_date.' 23:59:59"';	
	$result = plaatenergy_db_query($sql);
	$row = plaatenergy_db_fetch_object($result);

	if ( isset($row->low_delivered) ) {
		$low_delivered_value = $row->low_delivered;
		$normal_delivered_value = $row->normal_delivered;
	}     
	
	// Get last solar measurement
	$sql  = 'select max(etotal) as etotal from solar1 where ';
	$sql .= 'timestamp>="'.$prev_date.' 00:00:00" and timestamp<="'.$prev_date.' 23:59:59" ';
	$result = plaatenergy_db_query($sql);
	$row = plaatenergy_db_fetch_object($result);
	
	$etotal_prev=0;
	if ( isset($row->etotal) ) {
		$etotal_prev = $row->etotal;
	}
	 
	
	$delivered_low = 0;
	$delivered_normal = 0;
	$delivered_local = 0;

	while ($i<96) {
	 	$timestamp1 = date("Y-m-d H:i:s", $current_date+(900*$i));
		$timestamp2 = date("Y-m-d H:i:s", $current_date+(900*($i+1)));

		$sql1  = 'select max(low_delivered) as low_delivered, max(normal_delivered) as normal_delivered from energy1 where ';
		$sql1 .= 'timestamp>="'.$timestamp1.'" and timestamp<"'.$timestamp2.'"';	
		$result1 = plaatenergy_db_query($sql1);
		$row1 = plaatenergy_db_fetch_object($result1);
			
		$sql2  = 'select max(etotal) as etotal FROM solar1 where ';
		$sql2 .= 'timestamp>="'.$timestamp1.'" and timestamp<"'.$timestamp2.'"';	
		$result2 = plaatenergy_db_query($sql2);
		$row2 = plaatenergy_db_fetch_object($result2);			
			
		if ( isset($row1->low_delivered)) {
				
			if ($row1->low_delivered>=$low_delivered_value) {
				$delivered_low = $row1->low_delivered - $low_delivered_value;
			} else {
				$delivered_low = $row1->low_delivered;
			}
			if ($row1->normal_delivered>=$normal_delivered_value) {
				$delivered_normal = $row1->normal_delivered - $normal_delivered_value;
			} else {
				$delivered_normal = $row1->normal_delivered;
			}
		}
		
		if ( isset($row2->etotal)) {
			$delivered_local = $row2->etotal - $etotal_prev - $delivered_low - $delivered_normal;
			if ($delivered_local<0) {
				$delivered_local = 0;
			}
		}
		
		// Data in the future is always 0!	
		if ($timestamp1>date("Y-m-d H:i:s")) {
			$delivered_low = 0;
			$delivered_normal = 0;
			$delivered_local = 0;
			
		} else {
		
			$total = $delivered_low + $delivered_normal + $delivered_local;
		}
		
		if (strlen($data)>0) {
			$data.=',';
		}
		
		$data .= "['".date("H:i", $current_date+(900*($i+1)))."',";
		$data .= round($delivered_low,2).',';
		$data .= round($delivered_normal,2).',';
		$data .= round($delivered_local,2).']';
		$i++;
	}		
	$json = "[['','".t('DELIVERED_LOW_KWH')."','".t('DELIVERED_NORMAL_KWH')."','".t('DELIVERED_LOCAL_KWH')."'],".$data."]";


	if ($eid==EVENT_WATT) {

		$data="";
		
		$timestamp1 = date("Y-m-d 00:00:00", $current_date);
		$timestamp2 = date("Y-m-d 23:59:59", $current_date);
	
		if ($solar_meter_vendor=='unknown') {		
			$sql  = 'select timestamp, vermogenterug as pac FROM energy ';
		} else {	
			$sql  = 'select timestamp, pac from solar1 ';
		}
		$sql .= 'where timestamp>="'.$timestamp1.'" and timestamp<="'.$timestamp2.'" order by timestamp';
		
		$result = plaatenergy_db_query($sql);

		while ($row = plaatenergy_db_fetch_object($result)) {

			$value = 0;
			if ( isset($row->pac)) {
				$value= $row->pac;
			}

			if (strlen($data)>0) {
				$data.=',';
			}
			$data .= "['".substr($row->timestamp,11,5)."',";
			$data .= round($value,2).']';
			$i++;
		}
		$json = "[".$data."]";
		
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
          legend: { position: "'.plaatenergy_db_get_config_item('chart_legend').'", textStyle: {fontSize: 10} },
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
          legend: { position: "'.plaatenergy_db_get_config_item('chart_legend').'", textStyle: {fontSize: 10} },
          vAxis: {format: "decimal" },
          isStacked: true,
          colors: ["#0066cc", "#808080"],

        };

        var data = google.visualization.arrayToDataTable('.$json.');
        var chart = new google.charts.Bar(document.getElementById("chart_div"));
        chart.draw(data, google.charts.Bar.convertOptions(options));
      }
		</script>';
	}
		
	$forecast = ($out_forecast[$month] * $energy_delivery_forecast) / cal_days_in_month (CAL_GREGORIAN, $month, $year);
 
	$page .= '<h1>'.t('TITLE_DAY_OUT_KWH', plaatenergy_dayofweek($date),$day, $month, $year).'</h1>';
	$page .= '<div id="chart_div" style="'.plaatenergy_db_get_config_item('chart_dimensions').'"></div>';

	$page .= '<div class="remark">';	
	$page .= t('TOTAL_PER_DAY_KWH', round($total,2), round($forecast,2));
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
	}
	
	/* Page handler */
	switch ($pid) {

		case PAGE_DAY_OUT_ENERGY:
			return plaatenergy_day_out_energy_page();
			break;
	}
}

?>
