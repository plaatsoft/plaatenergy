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

include "config.inc";
include "general.inc";
include "database.inc";

/*
** ---------------------
** PARAMETERS
** ---------------------
*/

year_parameters();

$gas_price = plaatenergy_db_get_config_item('gas_price');
$gas_use_forecast = plaatenergy_db_get_config_item('gas_use_forecast');

/*
** ---------------------
** PAGES
** ---------------------
*/

function plaatenergy_years_in_gas_euro_page() {

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
		
		if (strlen($data)>0) {
			$data.=',';
		}
		$price2 = $value * $gas_price;
		$data .= "['".date("Y", $time)."',"; 
	   $data .= round($price2,2).']';
		
		$total += $value;
		$total_price += $price2;
	}

	$json = "[['','".t('EURO')."'],".$data."]";

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
			 colors: ["#e0440e"]";
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

	echo '<h1>'.t('TITLE_YEARS_IN_M3', ($year-10), $year).'</h1>';
	echo '<div id="chart_div" style="width: '.$graph_width.'; height: '.$graph_height.';"></div>';
	
	if ($count>0) {
		text_banner(t('AVERAGE_PER_YEAR_EURO', round(($total_price/$count),2), round($total_price,2)));
	} else {
		text_banner('&nbsp;');
	}

	$page .= '<div class="nav">';
	$page .= plaatenergy_link('pid='.$pid.'&year='.$prev_year.'&eid='.EVENT_PREV,t('LINK_PREV_DAY');
	$page .= plaatenergy_link('pid='.PAGE_HOME, t('LINK_HOME'));
	$page .= plaatenergy_link('pid='.$pid.'&year='.$next_year.'&eid='.EVENT_NEXT,t('LINK_NEXT_DAY'));	
	$page .= '</div>';
   
	return $page;
}

function plaatenergy_years_in_gas_m3_page() {

	$total=0;
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
		
		if (strlen($data)>0) {
			$data.=',';
		}
		$price2 = $value * $gas_price;
		$data .= "['".date("Y", $time)."',";

		if ($type==1) {
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

	$json = "[['','".t('USED_M3')."','".t('FORECAST_M3')."'],".$data."]";

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
			 colors: ["#0066cc", "#808080"],
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

	echo '<h1>'.t('TITLE_YEARS_IN_M3', ($year-10), $year).'</h1>';
	echo '<div id="chart_div" style="width: '.$graph_width.'; height: '.$graph_height.';"></div>';
	
	if ($count>0) {
		text_banner(t('AVERAGE_PER_YEAR_M3', round(($total/$count),2), round($total,2)));
	} else {
		text_banner('&nbsp;');
	}

	$page .= '<div class="nav">';
	$page .= plaatenergy_link('pid='.$pid.'&year='.$prev_year.'&eid='.EVENT_PREV,t('LINK_PREV_YEAR');
	$page .= plaatenergy_link('pid='.PAGE_HOME, t('LINK_HOME'));
	$page .= plaatenergy_link('pid='.$pid.'&year='.$next_year.'&eid='.EVENT_NEXT,t('LINK_NEXT_YEAR'));	
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

		case PAGE_YEARS_IN_GAS_M3:
			echo plaatenergy_years_in_gas_m3_page();
			break;
		  
		case PAGE_YEARS_IN_GAS_EURO:
			echo plaatenergy_years_in_gas_euro_page();
			break;
  }
}

/*
** ---------------------
** THE END
** ---------------------
*/




?>
