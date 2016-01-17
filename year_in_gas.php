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
** PAGES
** ---------------------
*/

function plaatenergy_year_in_gas_page() {

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
	$count=0;
	$data="";
	
	for($m=1; $m<=12; $m++) {
		$value=0;
	
		$time=mktime(0, 0, 0, $m, 1, $year);          
		$timestamp1=date('Y-m-0 00:00:00', $time);
		$timestamp2=date('Y-m-t 23:59:59', $time);
	
		$sql = 'select sum(gas) as gas FROM energy_day where date>="'.$timestamp1.'" and date<="'.$timestamp2.'"';
	
		$result = plaatenergy_db_query($sql);
		$row = plaatenergy_db_fetch_object($result);
		
		if ( isset($row->gas)) {
			$count++;
			$value=$row->gas;
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
          legend: { position: "none" },
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
           window.location="month_in_gas.php?month="+month[0]+"&year='.$year.'"
        }
    }
    </script>';

	$page .= '<h1>'.t('TITLE_YEAR_IN_M3', $year).'</h1>';
	$page .= '<div id="chart_div" style="width: '.$graph_width.'; height: '.$graph_height.';"></div>';

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
	
	$page .= '<div class="nav">';
	$page .= plaatenergy_link('pid='.$pid.'&date='.$prev_date.'&eid='.$eid,t('LINK_PREV_YEAR'));
	$page .= plaatenergy_link('pid='.PAGE_HOME, t('LINK_HOME'));
	$page .= plaatenergy_link('pid='.$pid.'&date='.$next_date.'&eid='.$eid,t('LINK_NEXT_YEAR'));	
	if ($eid==EVENT_KWH) {		
		$page .= plaatenergy_link('pid='.$pid.'&date='.$date.'&eid='.EVENT_EURO,t('LINK_EURO'));	
	} else {
		$page .= plaatenergy_link('pid='.$pid.'&date='.$date.'&eid='.EVENT_M3,t('LINK_KWH'));		
	}
	$page .= '</div>';
	
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
			echo plaatenergy_year_in_gas_page();
			break;
	}
}

/*
** ---------------------
** THE END
** ---------------------
*/

?>
