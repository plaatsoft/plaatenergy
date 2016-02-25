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
 * @brief contain month gas report
 */
 
/*
** ---------------------
** PAGES
** ---------------------
*/

function plaatenergy_month_in_gas_page() {

	// input
	global $pid;
	global $eid;

	global $date; 
	global $in_forecast;
		
	$prev_date = plaatenergy_prev_month($date);
	$next_date = plaatenergy_next_month($date);
	
	list($year, $month) = explode("-", $date);	
        $month = ltrim($month ,'0');
	
	$gas_price = plaatenergy_db_get_config_item('gas_price');
	
	$total=0;
	$total_price=0;
	$count=0;
	$data="";
	
	if ( (date('m')==$month) && (date('Y')==$year)) { 
		$count = date('d');
	} else {
		$count = cal_days_in_month(CAL_GREGORIAN, $month, $year); 
	}
	
	for($d=1; $d<=31; $d++)
	{
		$time=mktime(12, 0, 0, $month, $d, $year);          
	
		if (date('m', $time)==$month) {
			$timestamp1=date('Y-m-d 00:00:00', $time);
			$timestamp2=date('Y-m-d 23:59:59', $time);
	
			$sql = 'select sum(gas) as gas FROM energy_day where date>="'.$timestamp1.'" and date<="'.$timestamp2.'"';

			$result = plaatenergy_db_query($sql);
			$row = plaatenergy_db_fetch_object($result);

			$gas_value=0;

			if ( isset($row->gas)) {	
				if ($row->gas>0) {
					$gas_value=$row->gas;
				}
			}

			if (strlen($data)>0) {
				$data.=',';
			}
			$data .= "['".date("d-m", $time)."',";
			if ($eid==EVENT_M3) {
				$data .= round($gas_value,2).']';
			} else { 
				$data .= round($gas_value*$gas_price,2).']';
			}
			$total += $gas_value;
			$total_price += $gas_value*$gas_price;
		}
	}

	if ($eid==EVENT_M3) {
		$json = "[['','".t('USED_M3')."'],".$data."]";
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
          legend: { position: "'.plaatenergy_db_get_config_item('chart_legend').'", textStyle: {fontSize: 10} },
          vAxis: {format: "decimal"},';
   
	if ($eid==EVENT_EURO) {
            $page .= "colors: ['#e0440e']";
         }
       
	$page .= '};

        var data = google.visualization.arrayToDataTable('.$json.');
        var chart = new google.charts.Bar(document.getElementById("chart_div"));
        chart.draw(data, google.charts.Bar.convertOptions(options));

        google.visualization.events.addListener(chart, "select", selectHandler);

        function selectHandler(e)     {
           var date = data.getValue(chart.getSelection()[0].row, 0);
           var day = date.split("-");
           link("pid='.PAGE_DAY_IN_GAS.'&eid='.$eid.'&date='.$year.'-'.$month.'-"+day[0]);
        }
     }

   </script>';
	
	$page .= '<h1>'.t('TITLE_MONTH_IN_GAS', $month, $year).'</h1>';
        $page .= '<div id="chart_div" style="'.plaatenergy_db_get_config_item('chart_dimensions').'"></div>';

	$page .= '<div class="remark">';
	if ($count>0) {
		if ($eid==EVENT_M3) {
			$page .= t('AVERAGE_PER_DAY_M3', round(($total/$count),2), round($total,2));
		} else {
			$page .= t('AVERAGE_PER_DAY_EURO', round(($total_price/$count),2), round($total_price,2));
		}
	} else {
		$page .= '&nbsp';
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

function plaatenergy_month_in_gas() {

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

		case PAGE_MONTH_IN_GAS:
			return plaatenergy_month_in_gas_page();
			break;
	}
}

/*
** ---------------------
** THE END
** ---------------------
*/

?>



