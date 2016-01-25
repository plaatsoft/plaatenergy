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
 * @brief contain years out energy report
 */
 
/*
** ---------------------
** PAGES
** ---------------------
*/

function plaatenergy_years_out_energy_page() {

	// input
	global $pid;
	global $eid;

	global $date; 
	global $out_forecast;
	global $graph_width;
	global $graph_height;
	
	$prev_date = plaatenergy_prev_year($date);
	$next_date = plaatenergy_next_year($date);
	
	list($year) = explode("-", $date);	
	
	$energy_price = plaatenergy_db_get_config_item('energy_price');
	$energy_use_forecast = plaatenergy_db_get_config_item('energy_use_forecast');
	
	
	$energy_price = plaatenergy_db_get_config_item('energy_price');
	$energy_delivery_forecast = plaatenergy_db_get_config_item('energy_delivery_forecast');

	$total=0;
	$total_price=0;
	$count=0;
	$data="";
	
	for($y=($year-10); $y<=$year; $y++) {
	
		$time=mktime(0, 0, 0, 1, 1, $y);
		$timestamp1=date('Y-1-1', $time);
		$timestamp2=date('Y-12-t', $time);
	
		$sql1  = 'select sum(solar) as solar from energy_day ';
		$sql1 .= 'where date>="'.$timestamp1.'" and date<="'.$timestamp2.'"';
	
		$result1 = plaatenergy_db_query($sql1);
		$row1 = plaatenergy_db_fetch_object($result1);

		$value=0;
		if ( isset($row1->solar)) {
			$count++;
			$value=$row1->solar;
		}
	
		$sql2  = 'select month(date) as month from energy_day ';
		$sql2 .= 'where date>="'.$timestamp1.'" and date<="'.$timestamp2.'" ';
		$sql2 .= 'group by month ';
		$result2 = plaatenergy_db_query($sql2);
	
		$forecast_total=0;
		while ($row2 = plaatenergy_db_fetch_object($result2)) {
			if (isset($row2->month)) {
				$forecast_total += $out_forecast[$row2->month];
			}
		}
	
		if (strlen($data)>0) {
			$data.=',';
		}
		$price2 = $value * $energy_price;
		$data .= "['".date("Y", $time)."',";
		if ($eid==EVENT_KWH) {
	
			if ($value>0) {
				$data .= round($value,2).','.round(($forecast_total*$energy_delivery_forecast),2).']';
			} else {
				$data .= '0,0]';
			}
		} else { 
			$data .= round($price2,2).']';
		}
		$total += $value;
		$total_price += $price2;
	}

	if ($eid==EVENT_KWH) {
		$json = "[['','".t('DELIVERED_KWH')."', '".t('FORECAST_KWH')."'],".$data."]";
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
          vAxis: {format: "decimal"},';
			 
	if ($eid==EVENT_KWH) {
		$page .= "colors: ['#0066cc', '#808080']";
	} else {
		$page .= "colors: ['#e0440e']";
   }
   
	$page .= '};

      var data = google.visualization.arrayToDataTable('.$json.');
      var chart = new google.charts.Bar(document.getElementById("chart_div"));
      chart.draw(data, google.charts.Bar.convertOptions(options));

      google.visualization.events.addListener(chart, "select", selectHandler);

      function selectHandler(e)     {
           var year = data.getValue(chart.getSelection()[0].row, 0);
			  link("pid='.PAGE_YEAR_OUT_ENERGY.'&eid='.$eid.'&date="+year+"-1-1");
        }
      }
    </script>';
    
	$page .= '<h1>'.t('TITLE_YEARS_OUT_KWH', ($year-10), $year).'</h1>';
	$page .= '<div id="chart_div" style="width: '.$graph_width.'; height: '.$graph_height.';"></div>';

	$page .= '<div class="remark">';
	if ($count>0) {
			if ($eid==EVENT_KWH) {
			$page .= t('AVERAGE_PER_YEAR_KWH', round(($total/$count),2), round($total,2));
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

function plaatenergy_years_out_energy() {

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

		case PAGE_YEARS_OUT_ENERGY:
			return plaatenergy_years_out_energy_page();
			break;
	}
}

/*
** ---------------------
** THE END
** ---------------------
*/
