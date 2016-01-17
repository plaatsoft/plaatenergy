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

function plaatenergy_month_out_energy_page() {

	// input
	global $pid;
	global $eid;

	global $date;
	global $in_forecast;
	global $graph_width;
	global $graph_height;

	$prev_date = plaatenergy_prev_month($date);
	$next_date = plaatenergy_next_month($date);
	
	list($year, $month) = explode("-", $date);	
	
	$energy_price = plaatenergy_db_get_config_item('energy_price');
	$energy_use_forecast = plaatenergy_db_get_config_item('energy_use_forecast');
	
	$data="";
	$value = 0;
	$total = 0;
	$total_price = 0;
	$count = 0;
	
	for($d=1; $d<=31; $d++)
	{
		$time=mktime(12, 0, 0, $month, $d, $year);          
		if (date('m', $time)==$month) {
			$timestamp1=date('Y-m-d 00:00:00', $time);
			$timestamp2=date('Y-m-d 23:59:59', $time);
	
			$sql = 'select solar FROM energy_day where date>="'.$timestamp1.'" and date<="'.$timestamp2.'"';
	
			$result = plaatenergy_db_query($sql);
			$row = plaatenergy_db_fetch_object($result);

			if (isset($row->solar)) {
				$value = $row->solar;
			
				$total += $value;
				$count++;
			} else {
				$value=0;
			}
	
			if (strlen($data)>0) {
				$data.=',';
			}
		
			if ($eid==EVENT_KWH) {
				$data .= "['".date("d-m", $time)."',".round($value,2)."]";
			} else { 
				$data .= "['".date("d-m", $time)."',".round($value*$energy_price,2)."]";
			}
		}
	}
	$total_price=$total*$energy_price;

	if ($eid==EVENT_KWH) {
		$json = "[['','".t('DELIVERED_KWH')."'],".$data."]";
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
          vAxis: {format: "decimal" },';
			 
	if ($eid==EVENT_EURO) {
		$page .= "colors: ['#e0440e']";
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
           window.location="day_out_kwh.php?day="+day[0]+"&month='.$month.'&year='.$year.'"
        }
      }
    </script>';
	
	$page .= '<h1>'.t('TITLE_MONTH_OUT_KWH', $month, $year).'</h1>';
	$page .= '<div id="chart_div" style="width: '.$graph_width.'; height: '.$graph_height.';"></div>';

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

	$page .= '<div class="nav">';
	$page .= plaatenergy_link('pid='.$pid.'&date='.$prev_date.'&eid='.$eid,t('LINK_PREV_YEAR'));
	$page .= plaatenergy_link('pid='.PAGE_HOME, t('LINK_HOME'));
	$page .= plaatenergy_link('pid='.$pid.'&date='.$next_date.'&eid='.$eid,t('LINK_NEXT_YEAR'));	
	if ($eid==EVENT_KWH) {		
		$page .= plaatenergy_link('pid='.$pid.'&date='.$date.'&eid='.EVENT_EURO,t('LINK_EURO'));	
	} else {
		$page .= plaatenergy_link('pid='.$pid.'&date='.$date.'&eid='.EVENT_KWH,t('LINK_KWH'));		
	}
	$page .= '</div>';
	
	return $page;
}

/*
** ---------------------
** HANDLER
** ---------------------
*/

function plaatenergy_month_out_energy() {

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

		case PAGE_MONTH_OUT_ENERGY:
			echo plaatenergy_month_out_energy_page();
			break;
	}
}

/*
** ---------------------
** THE END
** ---------------------
*/

?>
