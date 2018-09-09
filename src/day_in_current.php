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
**  All copyrights reserved (c) 2008-2018 PlaatSoft
*/

/**
 * @file
 * @brief contain day voltage report
 */
 
/*
** ---------------------
** PAGES
** ---------------------
*/

function plaatenergy_day_in_current_page() {

	// input
	global $pid;
	global $eid;

	global $date; 
	
	$prev_date = plaatenergy_prev_day($date);
	$next_date = plaatenergy_next_day($date);
			
	list($year, $month, $day) = explode("-", $date);	
	$day = ltrim($day ,'0');
	$month = ltrim($month ,'0');
		
	$data = "";
	$page = "";
	
	$sql  = 'select timestamp,  current_f1, current_f2, current_f3 from energy1_details where ';
	$sql .= 'timestamp>="'.$date.' 00:00:00" and timestamp<="'.$date.' 23:59:59" order by timestamp asc';	
		
	$result = plaatenergy_db_query($sql);  
	
	while ($row = plaatenergy_db_fetch_object($result)) {
	  
		if (strlen($data)>0) {
			$data.=',';
		}
		$data .= "['".date("H:i", strtotime($row->timestamp))."',";
		$data .= round($row->current_f1,2);
		$data .= ',';
		$data .= round($row->current_f2,2);
		$data .= ',';
		$data .= round($row->current_f3,2);
		$data .= ']';
	}		

	$json = "[".$data."]";
	
	$page .= '
		   <script type="text/javascript" src="https://www.google.com/jsapi"></script>
			<script type="text/javascript">
			google.load("visualization", "1", {packages:["line"]});
			google.setOnLoadCallback(drawChart);

			function drawChart() {

				var data = new google.visualization.DataTable();
				data.addColumn("string", "Time");
				data.addColumn("number",  "'.t('PHASE_1').'");
				data.addColumn("number",  "'.t('PHASE_2').'");
				data.addColumn("number",  "'.t('PHASE_3').'");
				data.addRows('.$json.');

				var options = {
					legend: { position: "'.plaatenergy_db_get_config_item('chart_legend',LOOK_AND_FEEL).'", textStyle: {fontSize: 10} },
					vAxis: {format: "decimal", title: ""},
					hAxis: {title: ""},
					backgroundColor: "transparent",
					chartArea: {
						backgroundColor: "transparent"
					}
				};

				var chart = new google.charts.Line(document.getElementById("chart_div"));
				chart.draw(data, google.charts.Line.convertOptions(options));
		}
		</script>';
    
	$page .= '<h1>'.t('TITLE_CURRENT', plaatenergy_dayofweek($date), $day, $month, $year).'</h1>';
    $page .= '<div id="chart_div" style="'.plaatenergy_db_get_config_item('chart_dimensions',LOOK_AND_FEEL).'"></div>';
		
	$page .= plaatenergy_navigation_day();
	
	return $page;
}

/*
** ---------------------
** HANDLER
** ---------------------
*/

function plaatenergy_in_current() {

	/* input */
	global $pid;
	global $eid;
  	
	/* Page handler */
	switch ($pid) {

		case PAGE_DAY_IN_CURRENT:
			return plaatenergy_day_in_current_page();
			break;
	}
}

/*
** ---------------------
** THE END
** ---------------------
*/

?>
