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
	
	$price = plaatenergy_db_get_config_item('energy_price', ENERGY_METER_1);
	$energy_use_forecast = plaatenergy_db_get_config_item('energy_use_forecast');
	$solar_meter_vendor = plaatenergy_db_get_config_item('solar_meter_vendor', SOLAR_METER_1);
	
	$data = "";
	$value = 0;
	$total_sum = 0;
	$count = 0;
	$max = 0;
	
	if ($eid==EVENT_MAX) {
	
		for($d=1; $d<=31; $d++) {
		
			$time=mktime(12, 0, 0, $month, $d, $year);  
        
			if (date('m', $time)==$month) {
				$timestamp1=date('Y-m-d 00:00:00', $time);
				$timestamp2=date('Y-m-d 23:59:59', $time);
				
				if ($solar_meter_vendor=='unknown') {
					$sql = 'select max(power) as power FROM energy1 where ';
					$sql.= 'timestamp>="'.$timestamp1.'" and timestamp<="'.$timestamp2.'" and power<0';
					$result = plaatenergy_db_query($sql);
					$row = plaatenergy_db_fetch_object($result);
		
					if (isset($row->pac)) {
						$value = ($row->power * -1);
					} else {
						$value = 0;
					}
		
				} else {
				
					$sql1 = 'select max(pac) as pac FROM solar1 where timestamp>="'.$timestamp1.'" and timestamp<="'.$timestamp2.'"';					
					$result1 = plaatenergy_db_query($sql1);
					$row1 = plaatenergy_db_fetch_object($result1);
		
					$value1 = 0;
					if (isset($row1->pac)) {
						$value1 = $row1->pac;
					} 
					
					$sql2 = 'select max(pac) as pac FROM solar2 where timestamp>="'.$timestamp1.'" and timestamp<="'.$timestamp2.'"';					
					$result2 = plaatenergy_db_query($sql2);
					$row2 = plaatenergy_db_fetch_object($result2);
		
					$value2 = 0;
					if (isset($row2->pac)) {
						$value2 = $row2->pac;
					} 
					
					$sql3 = 'select max(pac) as pac FROM solar3 where timestamp>="'.$timestamp1.'" and timestamp<="'.$timestamp2.'"';					
					$result3 = plaatenergy_db_query($sql3);
					$row3 = plaatenergy_db_fetch_object($result3);
		
					$value3 = 0;
					if (isset($row3->pac)) {
						$value3 = $row3->pac;
					} 
					
					$value = $value1 + $value2 + $value3;
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
		
				$sql1  = 'select sum(low_delivered) as low_delivered, sum(normal_delivered) as normal_delivered, ';
				$sql1 .= 'sum(solar_delivered) as solar_delivered from energy_summary ';
				$sql1 .= 'where date>="'.$timestamp1.'" and date<="'.$timestamp2.'"';

				$result1 = plaatenergy_db_query($sql1);
				$row1 = plaatenergy_db_fetch_object($result1);
	
				$delivered_low=0;
				$delivered_normal=0;
				$delivered_local=0;
				$total = 0;
	
				if ( isset($row1->solar_delivered)) {
					$count++;
					
					$delivered_low = $row1->low_delivered;
					$delivered_normal = $row1->normal_delivered;
					$tmp = $row1->solar_delivered - $delivered_low -$delivered_normal;
					if ($tmp >0 ) {
						$delivered_local=$tmp;
					}
					$total = $delivered_low + $delivered_normal + $delivered_local;
				}
				$total_sum += $total;
						
				if (strlen($data)>0) {
					$data.=',';
				}
			
				$price2 = $total * $price;
				$data .= "['".date("d-m", $time)."',";
				
				if ($eid==EVENT_KWH) {	
					$data .= round($delivered_low,2).',';
					$data .= round($delivered_normal,2).',';
					$data .= round($delivered_local,2).']';
				} else { 
					$data .= round($price2,2).']';
				}
			}
		}
		
		if ($eid==EVENT_KWH) {
			$json = "[['','".t('DELIVERED_LOW_KWH')."','".t('DELIVERED_NORMAL_KWH')."','".t('DELIVERED_LOCAL_KWH')."'],".$data."]";
	
		} else {
			$total= $total * $price;
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
          legend: { position: "'.plaatenergy_db_get_config_item('chart_legend',LOOK_AND_FEEL).'", textStyle: {fontSize: 10} },
          vAxis: {format: "decimal" },
			 isStacked: true,
			 backgroundColor: "transparent",
			 chartArea: {
            backgroundColor: "transparent"
          },';
			 
	if ($eid==EVENT_KWH) {
		$page .= 'colors: ["#0066cc", "#808080"],';
	} else if ($eid==EVENT_MAX) {
		$page .= 'colors: ["#0066cc", "#808080"],';
	} else {
		$page .= "colors: ['#e0440e'],";
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
        $page .= '<div id="chart_div" style="'.plaatenergy_db_get_config_item('chart_dimensions',LOOK_AND_FEEL).'"></div>';

	$page .= '<div class="remark">';

	$value = 0;	
	if ($count>0) {
		$value = $total_sum/$count;
	}
	
	switch ($eid) {
		
		case EVENT_KWH:
			$page .= t('AVERAGE_PER_DAY_KWH', round(($value),2), round($total_sum,2));
			break;
			
		case EVENT_MAX:
			$page .= t('MAX_PEAK_ENERGY', plaatenergy_format_watt($max));
			break;
			
		case EVENT_EURO:
			$page .= t('AVERAGE_PER_DAY_EURO', round(($value*$price),2), round($total_sum*$price,2));
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
