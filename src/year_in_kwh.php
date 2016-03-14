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

/**atenergy_db_get_config_item('energy_price');

 * @file
 * @brief contain year in energy report
 */
 
/*
** ---------------------
** PAGES
** ---------------------
*/

function plaatenergy_year_in_energy_page() {

	// input
	global $pid;
	global $eid;

	global $date; 
	global $in_forecast;

	$prev_date = plaatenergy_prev_year($date);
	$next_date = plaatenergy_next_year($date);
	
	list($year) = explode("-", $date);	

	$energy_price = plaatenergy_db_get_config_item('energy_price');
	$energy_use_forecast = plaatenergy_db_get_config_item('energy_use_forecast');
	
	$total=0;
	$total_price=0;
	$count=0;
	$data="";

		if ($eid==EVENT_SCATTER) {

			$sql  = 'select date, low_used, normal_used, solar_delivered, low_delivered, normal_delivered ';
			$sql .= 'from energy_summary where date>="'.$year.'-1-1" and date<="'.$year.'-12-31"';
	
			$result = plaatenergy_db_query($sql);
			while ($row = plaatenergy_db_fetch_object($result)) {
				if (strlen($data)>0) {
					$data .= ',';
				}
				$data .= '["'.$row->date.'",'.round(($row->low_used+$row->normal_used+$row->solar_delivered-$row->low_delivered-$row->normal_delivered),2).','.$row->solar_delivered.']';
			} 
			$json = "[".$data."]";

		} else {
	
			for($m=1; $m<=12; $m++) {

				$time=mktime(0, 0, 0, $m, 1, $year);
				$timestamp1=date('Y-m-0 00:00:00', $time);
				$timestamp2=date('Y-m-t 23:59:59', $time);
		
				$sql  = 'select sum(low_used) as low_used, sum(normal_used) as normal_used, sum(low_delivered) as ow_delivered, ';
				$sql .= 'sum(normal_delivered) as normal_delivered, sum(solar_delivered) as solar_delivered ';
				$sql .= 'FROM energy_summary where date>="'.$timestamp1.'" and date<="'.$timestamp2.'"';

				$result = plaatenergy_db_query($sql);
				$row = plaatenergy_db_fetch_object($result);
	
				$low_used_value=0;
				$normal_used_value=0;
				$low_delivered_value=0;
				$normal_delivered_value=0;
				$solar_delivered_value=0;
				$verbruikt=0;
	
				if (isset($row->dal)) {
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
				$data .= "['".date("m-Y", $time)."',";
				$price2 = ($low_used_value + $normal_used_value + $verbruikt)*$energy_price;
				
				if ($eid==EVENT_KWH) {
					$data .= round($low_used_value,2).','.round($normal_used_value,2).','.round($verbruikt,2).','.round(($in_forecast[$m]*$energy_use_forecast),2).']';
				} else { 
					$data .= round($price2,2).']';
				}
				$total += $low_used_value + $normal_used_value + $verbruikt;
				$total_price += $price2;
			}
	
			if ($eid==EVENT_KWH) {
				$json = "[['','".t('USED_LOW_KWH')."','".t('USED_HIGH_KWH')."','".t('USED_LOCAL_KWH')."','".t('FORECAST_KWH')."'],".$data."]";
			} else { 
				$json = "[['','".t('EURO')."'],".$data."]";
			}		
		}

		if ($eid==EVENT_SCATTER) {

 $page = '
                   <script type="text/javascript" src="https://www.google.com/jsapi"></script>
                        <script type="text/javascript">
                        google.load("visualization", "1.1", {packages:["line"]});
                        google.setOnLoadCallback(drawChart);

                        function drawChart() {

                                var data = new google.visualization.DataTable();
                                data.addColumn("string","'.t('DATE').'");
                                data.addColumn("number","'.t('USED_KWH').'");
                                data.addColumn("number","'.t('DELIVERED_KWH').'");
                                data.addRows('.$json.');

                                var options = {
                                        legend: { position: "'.plaatenergy_db_get_config_item('chart_legend').'", textStyle: {fontSize: 10} },
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
				vAxis: {format: "decimal"},
				isStacked:true,';
				
	  if ($eid==EVENT_KWH) {
		$page .= "colors: ['#0066cc', '#808080'],";
		$page .= "vAxis: { format: 'decimal',  viewWindow: { min: 0, max: 300 } }, ";
	  } else {
		$page .= "colors: ['#e0440e'],";
	  }
	
  	  $page .= 'series: {
					3: {
						targetAxisIndex: 1
					}
				}
			};
	
			var data = google.visualization.arrayToDataTable('.$json.');
			var chart = new google.charts.Bar(document.getElementById("chart_div"));
			chart.draw(data, google.charts.Bar.convertOptions(options));
	
			google.visualization.events.addListener(chart, "select", selectHandler);
	
			function selectHandler(e)     {
				var date = data.getValue(chart.getSelection()[0].row, 0);
				var month = date.split("-");
				link("pid='.PAGE_MONTH_IN_ENERGY.'&eid='.$eid.'&date='.$year.'-"+month[0]+"-1");
			}
		}
		</script>';
        }
	
	$page .= '<h1>'.t('TITLE_YEAR_IN_KWH', $year).'</h1>';
	$page .= '<div id="chart_div" style="'.plaatenergy_db_get_config_item('chart_dimensions').'"></div>';
	
	$page .= '<div class="remark">';	
	if ($count>0) {
		if ($eid==EVENT_KWH) {
			$page .= t('AVERAGE_PER_MONTH_KWH', round(($total/$count),2), round($total,2));
		} else {
			$page .= t('AVERAGE_PER_MONTH_EURO', round(($total_price/$count),2), round($total_price,2));
		}
	} else {
		$page .= '&nbsp;';
	}
	$page .= '</div>';
	
	$page .= plaatenergy_navigation_year();
	
	return $page;
}

/*
** ---------------------
** HANDLER
** ---------------------
*/

function plaatenergy_year_in_energy() {

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

		case PAGE_YEAR_IN_ENERGY:
			return plaatenergy_year_in_energy_page();
			break;
	}
}

/*
** ---------------------
** THE END
** ---------------------
*/

?>

