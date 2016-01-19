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

function plaatenergy_day_in_gas_page() {

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
	
	$gas_price = plaatenergy_db_get_config_item('gas_price');
	$gas_use_forecast = plaatenergy_db_get_config_item('gas_use_forecast');
	
	$i = 0;
	$data = "";
	$page = "";
	$value = 0;
	$total = 0;
	
	// Get last energy measurement 
	$sql  = 'select gas from energy where ';
	$sql .= 'timestamp>="'.$prev_date.' 00:00:00" and timestamp<="'.$prev_date.' 23:59:59" order by timestamp desc limit 0,1';	
	$result = plaatenergy_db_query($sql);
	$row = plaatenergy_db_fetch_object($result);

	$gas_prev=0;
	if ( isset($row->gas) ) {
		$gas_prev = $row->gas;
	}      
	
	while ($i<96) {

		$timestamp = date("Y-m-d H:i:s", $current_date+(900*$i));
		$sql = 'select gas FROM energy where timestamp="'.$timestamp.'"';
		$result = plaatenergy_db_query($sql);
		$row = plaatenergy_db_fetch_object($result);
	
		if ($timestamp>date("Y-m-d H:i:s")) {
			$value=0;
		} else {
			$total = round($value,2);  
		}

		if ( isset($row->gas)) {
			$value= $row->gas - $gas_prev;
		}
  
		if (strlen($data)>0) {
			$data.=',';
		}
		$data .= "['".date("H:i", $current_date+(900*$i))."',";
		$data .= round($value,2).']';
		
		$i++;
	}		

	$json = "[['','".t('USED_M3')."'],".$data."]";

	$page = '
    <script type="text/javascript" src="https://www.google.com/jsapi"></script>
    <script type="text/javascript">
      google.load("visualization", "1", {packages:["bar"]});
      google.setOnLoadCallback(drawChart);
      function drawChart() {

			var options = {
				bar: {groupWidth: "90%"},
				legend: { position: "none" },
				isStacked: true,
				vAxis: {format: "decimal"},
			};

			var data = google.visualization.arrayToDataTable('.$json.');
			var chart = new google.charts.Bar(document.getElementById("chart_div"));
			chart.draw(data, google.charts.Bar.convertOptions(options));
      }
		</script>';
    
	$page .= '<h1>'.t('TITLE_DAY_IN_GAS', $day, $month, $year).'</h1>';
	$page .= '<div id="chart_div" style="width: '.$graph_width.'; height: '.$graph_height.';"></div>';

	$page .= '<div class="remark">';
	$page .= t('TOTAL_PER_DAY_M3', $total);
	$page .= '</div>';
	
	$page .= '<div class="nav">';
	
	// If zero or one measurements are found. Measurement can be manully adapted.	
	$timestamp1 = date("Y-m-d 00:00:00", $current_date);
	$timestamp2 = date("Y-m-d 23:59:59", $current_date);
	$sql = 'select * FROM energy where timestamp>="'.$timestamp1.'" and timestamp<="'.$timestamp2.'"';
	$result = plaatenergy_db_query($sql);
	$records = plaatenergy_db_num_rows($result);
		
	if ($records<=1) {
		$page .= plaatenergy_link('pid='.PAGE_DAY_IN_GAS_EDIT.'&date='.$date, t('LINK_EDIT'));			
	}
	
	$page .= plaatenergy_link('pid='.$pid.'&date='.$prev_date.'&eid='.$eid, t('LINK_PREV_DAY'));
	$page .= plaatenergy_link('pid='.PAGE_HOME, t('LINK_HOME'));
	$page .= plaatenergy_link('pid='.$pid.'&date='.$next_date.'&eid='.$eid, t('LINK_NEXT_DAY'));	
	
	$page .= '</div>';
	return $page;
}

/*
** ---------------------
** HANDLER
** ---------------------
*/

function plaatenergy_day_in_gas() {

  /* input */
  global $pid;
  global $eid;
  
   /* Event handler */
  switch ($eid) {
     
		case EVENT_SAVE:
				plaatenergy_day_in_gas_edit_save_event();
				break;

		case EVENT_M3:
				break;
				
		case EVENT_EURO:
				break;
	}
	
	/* Page handler */
	switch ($pid) {

		case PAGE_DAY_IN_GAS:
			return plaatenergy_day_in_gas_page();
			break;
	}
}

/*
** ---------------------
** THE END
** ---------------------
*/

?>
