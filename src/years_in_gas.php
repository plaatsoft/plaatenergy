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
 * @brief contain years in gas report
 */
 
/*
** ---------------------
** PAGES
** ---------------------
*/

function plaatenergy_years_in_gas_page() {

	// input		 
	global $pid;  
	global $eid;  
	
	global $date; 	
	global $gas_forecast;
	
	global $graph_width;
	global $graph_height;
	
	$prev_date = plaatenergy_prev_year($date);
	$next_date = plaatenergy_next_year($date);
	
	list($year) = explode("-", $date);	
	
	$gas_price = plaatenergy_db_get_config_item('gas_price');
	$gas_use_forecast = plaatenergy_db_get_config_item('gas_use_forecast');

	$total=0;
	$total_price=0;
	$price=0;
	$count=0;
	$data="";
	
	for($y=($year-10); $y<=$year; $y++) {
		$value=0;

		$time=mktime(0, 0, 0, 1, 1, $y);          
		$timestamp1=date('Y-1-1 00:00:00', $time);
		$timestamp2=date('Y-12-t 23:59:59', $time);

		$sql1  = 'select sum(gas) as gas FROM energy_day ';
		$sql1 .= 'where date>="'.$timestamp1.'" and date<="'.$timestamp2.'"';
		
		$result1 = plaatenergy_db_query($sql1);
		$row1 = plaatenergy_db_fetch_object($result1);
	
		if ( isset($row1->gas)) {
			$count++;
			$value=$row1->gas;
		}

		$sql2  = 'select month(date) as month from energy_day ';
		$sql2 .= 'where date>="'.$timestamp1.'" and date<="'.$timestamp2.'" ';
		$sql2 .= "group by month ";
		
		$result2 = plaatenergy_db_query($sql2);

		$forecast_total=0;
		while ($row2 = plaatenergy_db_fetch_object($result2)) {
			if (isset($row2->month)) { 
				$forecast_total += $gas_forecast[$row2->month];
	                        $price = $gas_price * $row1->gas;
			}
		} 

                if (strlen($data)>0) {
                        $data.=',';
                }
                $data .= "['".date("Y", $time)."',";

		
		if ($eid==EVENT_M3) {
			if ($value>0) { 
				$data .= round($value,2).','.round(($forecast_total*$gas_use_forecast),2).']';
			} else { 
				$data .= '0,0]';
			}
		} else { 
			$data .= round($price,2).']';
		}
		
		$total += $value;
		$total_price += $price;
	}

	if ($eid==EVENT_M3) {
		$json = "[['','".t('USED_M3')."','".t('FORECAST_M3')."'],".$data."]";		
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
          legend: { position: "none" },
          vAxis: {format: "decimal"},
			 ';
				if ($eid==EVENT_EURO) {			 
					$page .= 'colors: ["#e0440e"],';
				} else {
					$page .= 'colors: ["#0066cc", "#808080"],';
				}
			$page .= '
        };
        var data = google.visualization.arrayToDataTable('.$json.');
        var chart = new google.charts.Bar(document.getElementById("chart_div"));
        chart.draw(data, google.charts.Bar.convertOptions(options));

        google.visualization.events.addListener(chart, "select", selectHandler);

        function selectHandler(e)     {
           var year = data.getValue(chart.getSelection()[0].row, 0);
			  link("pid='.PAGE_YEAR_IN_GAS.'&eid='.$eid.'&date="+year+"-1-1");
        }
     }
    </script>';

	if ($eid==EVENT_M3) {			
		$page .= '<h1>'.t('TITLE_YEARS_IN_GAS', ($year-10), $year).'</h1>';	
	} else {
		$page .= '<h1>'.t('TITLE_YEARS_IN_GAS', ($year-10), $year).'</h1>';
	}
	
	$page .= '<div id="chart_div" style="width: '.$graph_width.'; height: '.$graph_height.';"></div>';	
	$page .= '<div class="remark">';
	
	if ($count>0) {			 
		if ($eid==EVENT_M3) {		
			$page .= t('AVERAGE_PER_YEAR_M3', round(($total/$count),2), round($total,2));
		} else {
			$page .= t('AVERAGE_PER_YEAR_EURO', round(($total_price/$count),2), round($total_price,2));
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

function plaatenergy_years_in_gas() {

  /* input */
  global $pid;
  global $eid;
  
   /* Event handler */
  switch ($eid) {
  
  		case EVENT_M3:
				break;
				
		case EVENT_EURO:
				break;
	}
	
	/* Page handler */
	switch ($pid) {

		case PAGE_YEARS_IN_GAS:
			return plaatenergy_years_in_gas_page();
			break;
	}
}

/*
** ---------------------
** THE END
** ---------------------
*/

?>
