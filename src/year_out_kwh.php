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
 * @brief contain year out energy report
 */
 
/*
** ---------------------
** PAGES
** ---------------------
*/

function plaatenergy_year_out_energy_page() {

	// input
	global $pid;
	global $eid;

	global $date; 
	global $out_forecast;
	
	$prev_date = plaatenergy_prev_year($date);
	$next_date = plaatenergy_next_year($date);
	
	list($year) = explode("-", $date);	
	
	$energy_price = plaatenergy_db_get_config_item('energy_price');
	$energy_delivery_forecast = plaatenergy_db_get_config_item('energy_delivery_forecast');

	
	$total=0;
	$total_price=0;
	$count=0;
	$data="";

	for($m=1; $m<=12; $m++) {

		$time=mktime(0, 0, 0, $m, 1, $year);
		$timestamp1=date('Y-m-0 00:00:00', $time);
		$timestamp2=date('Y-m-t 23:59:59', $time);
	
		$sql  = 'select sum(solar) as solar FROM energy_day ';
		$sql .= 'where date>="'.$timestamp1.'" and date<="'.$timestamp2.'"';

		$result = plaatenergy_db_query($sql);
		$row = plaatenergy_db_fetch_object($result);
	
		$value=0;
		if ( isset($row->solar)) {
			$count++;
			$value=$row->solar;
		}
	
		if (strlen($data)>0) {
			$data.=',';
		}
		$price2 = $value * $energy_price;
		$data .= "['".date("m-Y", $time)."',";
		if ($eid==EVENT_KWH) {
			$data .= round($value,2).','.round(($out_forecast[$m]*$energy_delivery_forecast),1).']';
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
          legend: { position: "'.plaatenergy_db_get_config_item('chart_legend').'", textStyle: {fontSize: 10} },
          vAxis: {format: "decimal" },
			 ';
			 
	if ($eid==EVENT_KWH) {
		$page .= 'colors: ["#0066cc", "#808080"]';
	} else {
		$page .= 'colors: ["#e0440e"]';
	}  
       
	$page .= ' };

        var data = google.visualization.arrayToDataTable('.$json.');
        var chart = new google.charts.Bar(document.getElementById("chart_div"));
        chart.draw(data, google.charts.Bar.convertOptions(options));

        google.visualization.events.addListener(chart, "select", selectHandler);

        function selectHandler(e)     {
           var date = data.getValue(chart.getSelection()[0].row, 0);
           var month = date.split("-");
			  link("pid='.PAGE_MONTH_OUT_ENERGY.'&eid='.$eid.'&date='.$year.'-"+month[0]+"-1");
        }
      }
    </script>';

	$page .= '<h1>'.t('TITLE_YEAR_OUT_KWH', $year).'</h1>';
        $page .= '<div id="chart_div" style="'.plaatenergy_db_get_config_item('chart_style').'"></div>';

	$page .= '<div class="remark">';
	if ($count>0) {
		if ($eid==EVENT_KWH) {
			$page .= t('AVERAGE_PER_MONTH_KWH', round(($total/$count),2), round($total,2) );
		} else {
			$page .= t('AVERAGE_PER_MONTH_EURO', round(($total_price/$count),2), round($total_price,2) );
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

function plaatenergy_year_out_energy() {

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

		case PAGE_YEAR_OUT_ENERGY:
			return plaatenergy_year_out_energy_page();
			break;
	}
}

/*
** ---------------------
** THE END
** ---------------------
*/

?>


