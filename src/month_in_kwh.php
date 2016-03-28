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
 * @brief contain month energy in report
 */
 
/*
** ---------------------
** PAGES
** ---------------------
*/

function plaatenergy_month_in_energy_page() {

	// input
	global $pid;
	global $eid;

	global $date; 
	global $in_forecast;
		
	$prev_date = plaatenergy_prev_month($date);
	$next_date = plaatenergy_next_month($date);
	
	list($year, $month) = explode("-", $date);	
	$month = ltrim($month ,'0');
	
	$energy_price = plaatenergy_db_get_config_item('energy_price', ENERGY_METER_1);
	$energy_use_forecast = plaatenergy_db_get_config_item('energy_use_forecast');
	
	$total = 0;
	$total_price = 0;
	$count = 0;
	$data = "";
	
	for($d=1; $d<=31; $d++)
	{
		$time=mktime(12, 0, 0, $month, $d, $year);          

		// Check you real days
		if (date('m', $time)==$month) {
			$timestamp1=date('Y-m-d 00:00:00', $time);
			$timestamp2=date('Y-m-d 23:59:59', $time);
	
			$low_used_value = 0;
			$normal_used_value = 0;
			$solar_delivered_value_value = 0;
			$low_delivered_value = 0;
			$normal_delivered_value = 0;
			$verbruikt = 0;
	
			$sql  = 'select sum(low_used) as low_used, sum(normal_used) as normal_used, ';
			$sql .= 'sum(low_delivered) as low_delivered, sum(normal_delivered) as normal_delivered, ';
			$sql .= 'sum(solar_delivered) as solar_delivered ';
			$sql .= 'from energy_summary where date>="'.$timestamp1.'" and date<="'.$timestamp2.'"';
	
			$result = plaatenergy_db_query($sql);
			$row = plaatenergy_db_fetch_object($result);
	
			if (isset($row->low_used)) {
				$low_used_value = $row->low_used;
				$normal_used_value = $row->normal_used;
				$low_delivered_value = $row->low_delivered;
				$normal_delivered_value = $row->normal_delivered;
				$solar_delivered_value = $row->solar_delivered;
	
				$verbruikt = $solar_delivered_value - $low_delivered_value - $normal_delivered_value;
				if ($verbruikt<0) {
					$verbruikt=0;
				} 
				$count++;
			}
	
			if (strlen($data)>0) {
				$data.=',';
			}
			$data .= "['".date("d-m", $time)."',";
			
			if ($eid==EVENT_KWH) {
				$data .= round($low_used_value,2).','.round($normal_used_value,2).','.round($verbruikt,2).']';
			} else {
				$data .= round(($low_used_value+$normal_used_value+$solar_delivered_value_value)*$energy_price,2).']';
			}
			$total += $low_used_value + $normal_used_value + $verbruikt;
			$total_price += ($low_used_value + $normal_used_value + $verbruikt)*$energy_price;
		}
	}

	if ($eid==EVENT_KWH) {
		$json = "[['','".t('USED_LOW_KWH')."','".t('USED_HIGH_KWH')."','".t('USED_LOCAL_KWH')."'],".$data."]";
	} else { 
		$json = "[['','".t('EURO')."'],".$data."]";
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
          vAxis: {format: "decimal"},
          isStacked:true,';
         
	if ($eid==EVENT_KWH) {
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
           link("pid='.PAGE_DAY_IN_ENERGY.'&eid='.$eid.'&date='.$year.'-'.$month.'-"+day[0]);
        }

      }
    </script>';

	$page .= '<h1>'.t('TITLE_MONTH_IN_KWH', $month, $year).'</h1>';
        $page .= '<div id="chart_div" style="'.plaatenergy_db_get_config_item('chart_dimensions',LOOK_AND_FEEL).'"></div>';

	$page .= '<div class="remark">';
	if ($count>0) {
		if ($eid==EVENT_KWH) {
			$page .= t('AVERAGE_PER_DAY_KWH', round(($total/$count),2), round($total,2));
		} else {
			$page .= t('AVERAGE_PER_DAY_EURO', round(($total_price/$count),2), round($total_price,2));
		}
	} else {
		$page .= '&nbsp;';
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

function plaatenergy_month_in_energy() {

  /* input */
  global $pid;
  global $eid;
  
   /* Event handler */
  switch ($eid) {
  
		case EVENT_KWH:
				break;
				
		case EVENT_EURO:
				break;
	}
	
	/* Page handler */
	switch ($pid) {

		case PAGE_MONTH_IN_ENERGY:
			return plaatenergy_month_in_energy_page();
			break;
	}
}

/*
** ---------------------
** THE END
** ---------------------
*/

?>



