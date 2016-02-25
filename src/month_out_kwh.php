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
 * @brief contain month energy out report
 */
 
/*
** ---------------------
** PAGES
** ---------------------
*/

function plaatenergy_month_out_energy_page() {

	// input
	global $pid;
	global $eid;

	global $date;
	global $in_forecast;

	$prev_date = plaatenergy_prev_month($date);
	$next_date = plaatenergy_next_month($date);
	
	list($year, $month) = explode("-", $date);	
        $month = ltrim($month ,'0');
	
	$energy_price = plaatenergy_db_get_config_item('energy_price');
	$energy_use_forecast = plaatenergy_db_get_config_item('energy_use_forecast');
	
	$data="";
	$value = 0;
	$total = 0;
	$count = 0;
	$max = 0;
	
	if ($eid==EVENT_MAX) {
	
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

		$json = "[['','".t('WATT')."'],".$data."]";	

	} else {
	
		for($d=1; $d<=31; $d++) {
		
			$time=mktime(12, 0, 0, $month, $d, $year);          
			if (date('m', $time)==$month) {
				$timestamp1=date('Y-m-d 00:00:00', $time);
				$timestamp2=date('Y-m-d 23:59:59', $time);
		
				$sql = 'select solar FROM energy_day where date>="'.$timestamp1.'" and date<="'.$timestamp2.'"';
		
				$result = plaatenergy_db_query($sql);
				$row = plaatenergy_db_fetch_object($result);
	
				if (isset($row->solar)) {
					$value = $row->solar;
				
					$total += $value;
					$count++;
				} else {
					$value=0;
				}
		
				if (strlen($data)>0) {
					$data.=',';
				}
			
				if ($eid==EVENT_KWH) {
					$data .= "['".date("d-m", $time)."',".round($value,2)."]";
				} else { 
					$data .= "['".date("d-m", $time)."',".round($value*$energy_price,2)."]";
				}
			}
		}
		
		if ($eid==EVENT_KWH) {
			$json = "[['','".t('DELIVERED_KWH')."'],".$data."]";
	
		} else {
			$total= $total * $energy_price;
			$json = "[['','".t('EURO')."'],".$data."]";
		}
	}
	
	$page = '
	<script type="text/javascript" src="https://www.google.com/jsapi"></script>
	<script type="text/javascript">
      google.load("visualization", "1", {packages:["bar"]});
      google.setOnLoadCallback(drawChart);
      function drawChart() {

       var options = {
          bars: "vertical",
          bar: {groupWidth: "90%"},
          legend: { position: "'.plaatenergy_db_get_config_item('chart_legend').'", textStyle: {fontSize: 10} },
          vAxis: {format: "decimal" },
			 isStacked: false,';
			 
	if ($eid==EVENT_EURO) {
		$page .= "colors: ['#e0440e']";
	}
	
	$page .= '
        };

        var data = google.visualization.arrayToDataTable('.$json.');
        var chart = new google.charts.Bar(document.getElementById("chart_div"));
        chart.draw(data, google.charts.Bar.convertOptions(options));

        google.visualization.events.addListener(chart, "select", selectHandler);

        function selectHandler(e)     {
           var date = data.getValue(chart.getSelection()[0].row, 0);
           var day = date.split("-");
			  link("pid='.PAGE_DAY_OUT_ENERGY.'&eid='.$eid.'&date='.$year.'-'.$month.'-"+day[0]);
        }
      }
    </script>';
	
	$page .= '<h1>'.t('TITLE_MONTH_OUT_KWH', $month, $year).'</h1>';
        $page .= '<div id="chart_div" style="'.plaatenergy_db_get_config_item('chart_dimensions').'"></div>';

	$page .= '<div class="remark">';

	$value = 0;	
	if ($count>0) {
		$value = $total/$count;
	}
	
	switch ($eid) {
		
		case EVENT_KWH:
			$page .= t('AVERAGE_PER_DAY_KWH', round(($value),2), round($total,2));
			break;
			
		case EVENT_MAX:
			$page .= t('MAX_PEAK_ENERGY', $max);
			break;
			
		case EVENT_EURO:
			$page .= t('AVERAGE_PER_DAY_EURO', round(($value),2), round($total,2));
			break;
				
		default:
			$page .= '&nbsp;';
			break;
	}
	$page .= '</div>';

	$page .= plaatenergy_navigation_month();
	
	return $page;
}

/*
** ---------------------
** HANDLER
** ---------------------
*/

function plaatenergy_month_out_energy() {

  /* input */
  global $pid;
  global $eid;
  
   /* Event handler */
  switch ($eid) {
  
		case EVENT_MAX:
				break;
				
		case EVENT_KWH:
				break;
				
		case EVENT_EURO:
				break;
	}
	
	/* Page handler */
	switch ($pid) {

		case PAGE_MONTH_OUT_ENERGY:
			return plaatenergy_month_out_energy_page();
			break;
	}
}

/*
** ---------------------
** THE END
** ---------------------
*/

?>
