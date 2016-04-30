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
	
	$low_delivered_value = plaatenergy_db_get_config_item('meter_reading_delivered_low', ENERGY_METER_1);
	$normal_delivered_value = plaatenergy_db_get_config_item('meter_reading_delivered_normal', ENERGY_METER_1);
	$solar_meter_vendor = plaatenergy_db_get_config_item('solar_meter_vendor', SOLAR_METER_1);	
	$solar_delivered_1 = plaatenergy_db_get_config_item('solar_initial_meter_reading', SOLAR_METER_1);
	$solar_delivered_2 = plaatenergy_db_get_config_item('solar_initial_meter_reading', SOLAR_METER_2);
	$solar_delivered_3 = plaatenergy_db_get_config_item('solar_initial_meter_reading', SOLAR_METER_3);

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
	
	if ( isset($row->etotal) ) {
		$solar_delivered_1 = $row->etotal;
	}
	
	$sql  = 'select max(etotal) as etotal from solar2 where ';
	$sql .= 'timestamp>="'.$prev_date.' 00:00:00" and timestamp<="'.$prev_date.' 23:59:59" ';
	$result = plaatenergy_db_query($sql);
	$row = plaatenergy_db_fetch_object($result);
	
	if ( isset($row->etotal) ) {
		$solar_delivered_2 = $row->etotal;
	}
	 
	$sql  = 'select max(etotal) as etotal from solar3 where ';
	$sql .= 'timestamp>="'.$prev_date.' 00:00:00" and timestamp<="'.$prev_date.' 23:59:59" ';
	$result = plaatenergy_db_query($sql);
	$row = plaatenergy_db_fetch_object($result);
	
	if ( isset($row->etotal) ) {
		$solar_delivered_3 = $row->etotal;
	}
	
	$delivered_low = 0;
	$delivered_normal = 0;
	$delivered_local = 0;

	$solar_diff_1 = 0;
	$solar_diff_2 = 0;
	$solar_diff_3 = 0;
		
	while ($i<96) {
	
	 	$timestamp1 = date("Y-m-d H:i:s", $current_date+(900*$i));
		$timestamp2 = date("Y-m-d H:i:s", $current_date+(900*($i+1)));

		$sql1  = 'select max(low_delivered) as low_delivered, max(normal_delivered) as normal_delivered from energy1 where ';
		$sql1 .= 'timestamp>="'.$timestamp1.'" and timestamp<"'.$timestamp2.'"';	
		$result1 = plaatenergy_db_query($sql1);
		$row1 = plaatenergy_db_fetch_object($result1);
			
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
		
		$sql3a  = 'select max(etotal) as etotal FROM solar1 where ';
		$sql3a .= 'timestamp>="'.$timestamp1.'" and timestamp<"'.$timestamp2.'"';	
		$result3a = plaatenergy_db_query($sql3a);
		$row3a = plaatenergy_db_fetch_object($result3a);

		if (isset($row3a->etotal)) {
			if ($row3a->etotal >= $solar_delivered_1 ) {
				$solar_diff_1 = $row3a->etotal - $solar_delivered_1;
			} else {			
				$solar_diff_1 = $row3a->etotal;			
			}
		}
					
		$sql3b  = 'select max(etotal) as etotal FROM solar2 where ';
		$sql3b .= 'timestamp>="'.$timestamp1.'" and timestamp<"'.$timestamp2.'"';	
		$result3b = plaatenergy_db_query($sql3b);
		$row3b = plaatenergy_db_fetch_object($result3b);	
		
		if (isset($row3b->etotal)) {
			if ($row3b->etotal >= $solar_delivered_2 ) {
				$solar_diff_2 = $row3b->etotal - $solar_delivered_2;
			} else {			
				$solar_diff_2 = $row3b->etotal;			
			}
		}

		$sql3c  = 'select max(etotal) as etotal FROM solar3 where ';
		$sql3c .= 'timestamp>="'.$timestamp1.'" and timestamp<"'.$timestamp2.'"';	
		$result3c = plaatenergy_db_query($sql3c);
		$row3c = plaatenergy_db_fetch_object($result3c);	
		
		if (isset($row3c->etotal)) {
			if ($row3c->etotal >= $solar_delivered_3 ) {
				$solar_diff_3 = $row3c->etotal - $solar_delivered_3;
			} else {			
				$solar_diff_3 = $row3c->etotal;			
			}
		}
				
		if (isset($row3a->etotal) || isset($row3b->etotal) || isset($row3c->etotal)) {
		
			$delivered_local = $solar_diff_1 + $solar_diff_2 + $solar_diff_3 - $delivered_low - $delivered_normal;
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
			$sql  = 'select timestamp, power as pac FROM energy1 ';
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
			}
			
		} else {	
		
			$sql  = 'select p1.timestamp, p1.pac as pac1, ifnull(p2.pac,0) as pac2, ifnull(p3.pac,0) as pac3 ';
			$sql .= 'from solar1 p1 left join solar2 p2 on p1.timestamp=p2.timestamp left join solar3 p3 on ';
			$sql .= 'p1.timestamp=p3.timestamp ';
			$sql .= 'where p1.timestamp>="'.$timestamp1.'" and p1.timestamp<="'.$timestamp2.'" order by p1.timestamp';
	
			$result = plaatenergy_db_query($sql);
		
			while ($row = plaatenergy_db_fetch_object($result)) {

				$value = 0;
				if ( isset($row->pac1)) {
					$value= $row->pac1 + $row->pac2 + $row->pac3;
				}
	
				if (strlen($data)>0) {
					$data.=',';
				}
				$data .= "['".substr($row->timestamp,11,5)."',";
				$data .= round($value,2).']';
			}
		}
		$json = "[".$data."]";
	} 
		
	if ($eid==EVENT_WATT) {
	
		$page = '
		<script type="text/javascript" src="https://www.google.com/jsapi"></script>
		<script type="text/javascript">
      google.load("visualization", "1", {packages:["line"]});
      google.setOnLoadCallback(drawChart);

      function drawChart() {

         var data = new google.visualization.DataTable();
         data.addColumn("string", "Time");
         data.addColumn("number", "Watt");
         data.addRows('.$json.');

         var options = {
          legend: { position: "'.plaatenergy_db_get_config_item('chart_legend',LOOK_AND_FEEL).'", textStyle: {fontSize: 10} },
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
          legend: { position: "'.plaatenergy_db_get_config_item('chart_legend',LOOK_AND_FEEL).'", textStyle: {fontSize: 10} },
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
	$page .= '<div id="chart_div" style="'.plaatenergy_db_get_config_item('chart_dimensions',LOOK_AND_FEEL).'"></div>';

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
