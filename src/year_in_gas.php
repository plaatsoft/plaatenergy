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
 * @brief contain year in gas report
 */
 
/*
** ---------------------
** PAGES
** ---------------------
*/

function plaatenergy_year_in_gas_page() {

	// input
	global $pid;
	global $eid;

	global $date; 
	global $gas_forecast;

	$prev_date = plaatenergy_prev_year($date);
	$next_date = plaatenergy_next_year($date);
	
	list($year) = explode("-", $date);	

	$gas_price = plaatenergy_db_get_config_item('gas_price', GAS_METER_1);
	$gas_use_forecast = plaatenergy_db_get_config_item('gas_use_forecast');

	$total=0;
	$total_price=0;
	$count=0;
	$data="";
	
	for($m=1; $m<=12; $m++) {
		$value=0;
	
		$time=mktime(0, 0, 0, $m, 1, $year);          
		$timestamp1=date('Y-m-0 00:00:00', $time);
		$timestamp2=date('Y-m-t 23:59:59', $time);
	
		$sql  = 'select sum(gas_used) as gas_used FROM energy_summary where ';
		$sql .= 'date>="'.$timestamp1.'" and date<="'.$timestamp2.'"';
	
		$result = plaatenergy_db_query($sql);
		$row = plaatenergy_db_fetch_object($result);
		
		if ( isset($row->gas_used)) {
			$count++;
			$value = $row->gas_used;
		}
	
		if (strlen($data)>0) {
			$data.=',';
		}
		$price2 = $value * $gas_price;
		$data .= "['".date("m-Y", $time)."',";

		if ($eid==EVENT_M3) {
			$data .= round($value,2).','.round(($gas_forecast[$m]*$gas_use_forecast),2).']';
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
	
	$page = '<script type="text/javascript" src="https://www.google.com/jsapi"></script>

    <script type="text/javascript">
      google.load("visualization", "1", {packages:["bar"]});
      google.setOnLoadCallback(drawChart);
      function drawChart() {

       var options = {
          bars: "vertical",
          bar: {groupWidth: "90%"},
          legend: { position: "'.plaatenergy_db_get_config_item('chart_legend',LOOK_AND_FEEL).'", textStyle: {fontSize: 10} },
          vAxis: {format: "decimal"}, ';
			 
	if ($eid==EVENT_M3) {
		$page .= "colors: ['#0066cc', '#808080']";
	} else {
		$page .= "colors: ['#e0440e']";
	}

	$page .= ' };

        var data = google.visualization.arrayToDataTable('.$json.');

        var chart = new google.charts.Bar(document.getElementById("chart_div"));
        chart.draw(data, google.charts.Bar.convertOptions(options));

        google.visualization.events.addListener(chart, "select", selectHandler);

        function selectHandler(e)     {
           var date = data.getValue(chart.getSelection()[0].row, 0);
           var month = date.split("-");
          link("pid='.PAGE_MONTH_IN_GAS.'&eid='.$eid.'&date='.$year.'-"+month[0]+"-1");
        }
    }
    </script>';

	$page .= '<h1>'.t('TITLE_YEAR_IN_M3', $year).'</h1>';
	$page .= '<div id="chart_div" style="'.plaatenergy_db_get_config_item('chart_dimensions',LOOK_AND_FEEL).'"></div>';

	$page .= '<div class="remark">';	
	if ($count>0) {
		if ($eid==EVENT_M3) {
			$page .= t('AVERAGE_PER_MONTH_M3', round(($total/$count),2), round($total,2));
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

function plaatenergy_year_in_gas() {

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

		case PAGE_YEAR_IN_GAS:
			return plaatenergy_year_in_gas_page();
			break;
	}
}

/*
** ---------------------
** THE END
** ---------------------
*/

?>
