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
** PAGES
** ---------------------
*/

function plaatenergy_day_in_energy_page() {

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
	
	$energy_price = plaatenergy_db_get_config_item('energy_price');
	$energy_use_forecast = plaatenergy_db_get_config_item('energy_use_forecast');
	
	$dal_prev = plaatenergy_db_get_config_item('energy_meter_reading_low');
	$piek_prev = plaatenergy_db_get_config_item('energy_meter_reading_normal');
	$dalterug_prev = 0;
	$piekterug_prev = 0;
	$solar_prev=0;
	
	$dal_value=0;
	$piek_value=0;
	$solar_value=0;
	$dalterug_value=0;
	$piekterug_value=0;

	$i=0;
	$data = "";
	$page = "";
	$total = 0;

	// Get last energy measurement 
	$sql  = 'select dal, piek, dalterug, piekterug from energy where ';
	$sql .= 'timestamp>="'.$prev_date.' 00:00:00" and timestamp<="'.$prev_date.' 23:59:59" order by timestamp desc limit 0,1';	
	$result = plaatenergy_db_query($sql);
	$row = plaatenergy_db_fetch_object($result);

	if ( isset($row->dal) ) {
		$dal_prev = $row->dal;
		$piek_prev = $row->piek;
		$dalterug_prev = $row->dalterug;
		$piekterug_prev = $row->piekterug;
	}      

	// Get last energy measurement 
	$sql  = 'select etotal from solar where ';
	$sql .= 'timestamp>="'.$prev_date.' 00:00:00" and timestamp<="'.$prev_date.' 23:59:59" order by timestamp desc limit 0,1';
	$result = plaatenergy_db_query($sql);
	$row = plaatenergy_db_fetch_object($result);
	
	if ( isset($row->etotal) ) {
		$solar_prev = $row->etotal;
	}
		
	while ($i<96) {

		$timestamp1 = date("Y-m-d H:i:s", $current_date+(900*$i));
		$timestamp2 = date("Y-m-d H:i:s", $current_date+(900*($i+1)));
		$sql1  = 'select max(dal) as dal, max(piek) as piek, max(dalterug) as dalterug, max(piekterug) as piekterug from energy where ';
		$sql1 .= 'timestamp>="'.$timestamp1.'" and timestamp<"'.$timestamp2.'"';	
		$result1 = plaatenergy_db_query($sql1);
		$row1 = plaatenergy_db_fetch_object($result1);
	
		$sql2  = 'select max(etotal) as etotal from solar where ';
		$sql2 .= 'timestamp>="'.$timestamp1.'" and timestamp<"'.$timestamp2.'"';	
		$result2 = plaatenergy_db_query($sql2);
		$row2 = plaatenergy_db_fetch_object($result2);
	
		if ( isset($row1->dal)) {
	
			if ($row1->dal>=$dal_prev) {
				$dal_value = $row1->dal - $dal_prev;
			} else {
				$dal_value = $row1->dal;
			}
	
			if ($row1->piek>=$piek_prev) {
				$piek_value = $row1->piek - $piek_prev;
			} else { 
				$piek_value = $row1->piek;
			}
	
			if ($row1->dalterug>=$dalterug_prev) {
				$dalterug_value = $row1->dalterug - $dalterug_prev;
			} else {
				$dalterug_value = $row1->dalterug;
			}
	
			if ($row1->piekterug>=$piekterug_prev) {
				$piekterug_value = $row1->piekterug - $piekterug_prev;
			} else {
				$piekterug_value = $row1->piekterug;
        }
		}
	
		if ( isset($row2->etotal)) {
			$solar_value = $row2->etotal - $solar_prev - $dalterug_value - $piekterug_value;
			if ($solar_value<0) 
			{
				$solar_value=0;
			}
		}

		// Data in the future is always 0!	
		if ($timestamp1>date("Y-m-d H:i:s")) {
	
			$dal_value = 0;
			$piek_value = 0;
			$dalterug_value = 0;
			$piekterug_value = 0;
			$solar_value = 0;

		} else { 
			$total = round(($dal_value+$piek_value+$solar_value),2);
		}
	
		if (strlen($data)>0) {
			$data.=',';
		}
		$data .= "['".date("H:i", $current_date+(900*$i))."',";
		$data .= round($dal_value,2).','.round($piek_value,2).','.round($solar_value,2).']';
	
		$i++;
	}
	
	$json = "[['','".t('USED_LOW_KWH')."','".t('USED_HIGH_KWH')."','".t('USED_SOLAR_KWH')."'],".$data."]";

	if ($eid==EVENT_WATT) {
	
		$timestamp1 = date("Y-m-d 00:00:00", $current_date);
		$timestamp2 = date("Y-m-d 23:59:59", $current_date);

		$data="";
	
		$sql  = 'select timestamp, vermogen FROM energy where ';
		$sql .= 'timestamp>="'.$timestamp1.'" and timestamp<="'.$timestamp2.'" order by timestamp';
	
		$result = plaatenergy_db_query($sql);
		while ( $row = plaatenergy_db_fetch_object($result)) {
	
		$value=0;
		if ( isset($row->vermogen)) {
			$value= $row->vermogen;
		}
  
		if (strlen($data)>0) {
			$data.=',';
		}
		$data .= "['".substr($row->timestamp,11,5)."',";
      $data .= round($value,2).']';
	}
	$json = "[".$data."]";
	}
	
	if ($eid==EVENT_WATT) {
	
	$page .= '
		   <script type="text/javascript" src="https://www.google.com/jsapi"></script>
			<script type="text/javascript">
			google.load("visualization", "1.1", {packages:["line"]});
			google.setOnLoadCallback(drawChart);

			function drawChart() {

				var data = new google.visualization.DataTable();
				data.addColumn("string", "Time");
				data.addColumn("number",  "Watt");
				data.addRows('.$json.');

				var options = {
					legend: { position: "none" },
					pointSize: 2,
					pointShape: "circle",
					vAxis: {format: "decimal", title: ""},
					hAxis: {title: ""},
				};

				var chart = new google.charts.Line(document.getElementById("chart_div"));
				chart.draw(data, google.charts.Line.convertOptions(options));
		}
		</script>';
				
	} else { 
	
		$page .= '
		   <script type="text/javascript" src="https://www.google.com/jsapi"></script>
			<script type="text/javascript">
			google.load("visualization", "1", {packages:["bar"]});
			google.setOnLoadCallback(drawChart);
			function drawChart() {

			var options = {
				bars: "vertical",
				bar: {groupWidth: "90%"},
				legend: { position: "none" },
				vAxis: {format: "decimal" },
				isStacked:true
			};

			var data = google.visualization.arrayToDataTable('.$json.');
			var chart = new google.charts.Bar(document.getElementById("chart_div"));
			chart.draw(data, google.charts.Bar.convertOptions(options));
		}
		</script>';
			
	}

	$page .= '<h1>'.t('TITLE_DAY_IN_KWH', $day, $month, $year).'</h1>';
	$page .= '<div id="chart_div" style="width: '.$graph_width.'; height: '.$graph_height.';"></div>';

	$page .= '<div class="remark">';
	$page .= t('TOTAL_PER_DAY_KWH', $total);
	$page .= '</div>';


	$page .= '<div class="nav">';
	// If zero or one measurements are found. Measurement can be manully adapted.	
	$timestamp1 = date("Y-m-d 00:00:00", $current_date);
	$timestamp2 = date("Y-m-d 23:59:59", $current_date);
	$sql = 'select * FROM energy where timestamp>="'.$timestamp1.'" and timestamp<="'.$timestamp2.'"';
	$result = plaatenergy_db_query($sql);
	$records = plaatenergy_db_num_rows($result);
		
	if ($records<=1) {
		$page .= plaatenergy_link('pid='.PAGE_DAY_IN_KWH_EDIT.'&date='.$date, t('LINK_EDIT'));			
	}
	
	$page .= plaatenergy_link('pid='.$pid.'&date='.$prev_date.'&eid='.$eid, t('LINK_PREV_DAY'));
	$page .= plaatenergy_link('pid='.PAGE_HOME, t('LINK_HOME'));
	$page .= plaatenergy_link('pid='.$pid.'&date='.$next_date.'&eid='.$eid, t('LINK_NEXT_DAY'));	
	if ($eid==EVENT_KWH) {		
		$page .= plaatenergy_link('pid='.$pid.'&date='.$date.'&eid='.EVENT_WATT,t('LINK_WATT'));	
	} else {
		$page .= plaatenergy_link('pid='.$pid.'&date='.$date.'&eid='.EVENT_KWH,t('LINK_KWH'));		
	}
	$page .= '</div>';
		
	return $page;
}

/*
** ---------------------
** HANDLER
** ---------------------
*/

function plaatenergy_day_in_energy() {

  /* input */
  global $pid;
  global $eid;
  
   /* Event handler */
  switch ($eid) {
  
      case EVENT_SAVE:
				plaatenergy_day_in_edit_save_event();
				break;
				
		case EVENT_KWH:
				break;
				
		case EVENT_EURO:
				break;
	}
	
	/* Page handler */
	switch ($pid) {

		case PAGE_DAY_IN_ENERGY:
			return plaatenergy_day_in_energy_page();
			break;
	}
}

/*
** ---------------------
** THE END
** ---------------------
*/

?>
