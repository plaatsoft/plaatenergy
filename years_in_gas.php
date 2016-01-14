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
** PARAMETERS
** ---------------------
*/

$date = plaatenergy_post("date", date('Y'));

$gas_price = plaatenergy_db_get_config_item('gas_price');
$gas_use_forecast = plaatenergy_db_get_config_item('gas_use_forecast');

/*
** ---------------------
** PAGES
** ---------------------
*/

function plaatenergy_years_in_gas_euro_page() {

	// input	
	global $date;  
	global $pid;  
	global $eid;  
	
	global $gas_price;
	global $gas_use_forecast;
	
	$prev_date = plaatenergy_prev_year($date);
	$next_date = plaatenergy_next_year($date);
	
	list($year) = explode("-", $date);	
	
	$total=0;
	$total_price=0;
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
			}
		} 
		
		if ($eid==EVENT_M3) {
				if ($value>0) { 
				$data .= round($value,2).','.round(($forecast_total*$gas_use_forecast),2).']';
			} else { 
				$data .= '0,0]';
			}
		} else { 
			$data .= round($price2,2).']';
		}
		
		$total += $value;
		$total_price += $price2;
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
					$page .= 'colors: ["#0066cc", "#808080"],';
				} else {
					$page .= 'colors: ["#e0440e"],';
			$page .= '}
				
        };
        var data = google.visualization.arrayToDataTable('.$json.');
        var chart = new google.charts.Bar(document.getElementById("chart_div"));
        chart.draw(data, google.charts.Bar.convertOptions(options));

        google.visualization.events.addListener(chart, "select", selectHandler);

        function selectHandler(e)     {
           var year = data.getValue(chart.getSelection()[0].row, 0);
           window.location="year_in_gas.php?year="+year;
        }
     }
    </script>';

	if ($eid==EVENT_M3) {			
		echo '<h1>'.t('TITLE_YEARS_IN_M3', ($year-10), $year).'</h1>';	
	} else {
		echo '<h1>'.t('TITLE_YEARS_IN_EURO', ($year-10), $year).'</h1>';
	}
	
	echo '<div id="chart_div" style="width: '.$graph_width.'; height: '.$graph_height.';"></div>';
	
	echo '<div class="remark">';
	
	if ($count>0) {			 
		if ($eid==EVENT_M3) {		
			$page .= t('AVERAGE_PER_YEAR_M3', round(($total/$count),2), round($total,2));
		} else {
			$page .= t('AVERAGE_PER_YEAR_EURO', round(($total_price/$count),2), round($total_price,2));
		}
			
	} else {	
		$page .= '&nbsp;';
	}
	echo '</div>';	
	
	$page .= '<div class="nav">';
	$page .= plaatenergy_link('pid='.$pid.'&date='.$prev_date.'&eid='.EVENT_PREV,t('LINK_PREV_YEAR'));
	$page .= plaatenergy_link('pid='.PAGE_HOME, t('LINK_HOME'));
	$page .= plaatenergy_link('pid='.$pid.'&date='.$next_date.'&eid='.EVENT_NEXT,t('LINK_NEXT_YEAR'));	
	if ($eid==EVENT_M3) {		
		$page .= plaatenergy_link('pid='.$pid.'&date='.$next_date.'&eid='.EVENT_EURO,t('LINK_EURO'));	
	} else {
		$page .= plaatenergy_link('pid='.$pid.'&date='.$next_date.'&eid='.EVENT_M3,t('LINK_M3'));		
	}
	$page .= '</div>';
	   
	return $page;
}

/*
** ---------------------
** HANDLER
** ---------------------
*/

function plaatenergy_years_in_m3() {

  /* input */
  global $pid;
  
   /* Event handler */
  switch ($eid) {
  
		case EVENT_PREV:
				break;
				
		case EVENT_NEXT:
				break;
	}
	
	/* Page handler */
	switch ($pid) {

		case PAGE_YEARS_IN_GAS:
			echo plaatenergy_years_in_gas_page();
			break;
	}
}

/*
** ---------------------
** THE END
** ---------------------
*/

?>
