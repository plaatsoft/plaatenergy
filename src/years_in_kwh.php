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
 * @brief contain years in energy report
 */
 
/*
** ---------------------
** PAGES
** ---------------------
*/

function plaatenergy_years_in_energy_page() {

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
	$max_forecast=0;
	
	for($y=$year-10; $y<=$year; $y++) {

		$time=mktime(0, 0, 0, 1, 1, $y);
		$timestamp1=date('Y-1-1', $time);
		$timestamp2=date('Y-12-t', $time);
	
		$sql1  = 'select sum(dal) as dal, sum(piek) as piek, sum(dalterug) as dalterug, ';
		$sql1 .= 'sum(piekterug) as piekterug, sum(solar) as solar ';
		$sql1 .= 'from energy_day where date>="'.$timestamp1.'" and date<="'.$timestamp2.'"';
	
		$result1 = plaatenergy_db_query($sql1);
		$row1 = plaatenergy_db_fetch_object($result1);
	 
		$sql2 =  'select month(date) as month from energy_day ';
		$sql2 .= 'where date>="'.$timestamp1.'" and date<="'.$timestamp2.'" ';
		$sql2 .= 'group by month ';
	
		$result2 = plaatenergy_db_query($sql2);

		$forecast_total=0;
		while ($row2 = plaatenergy_db_fetch_object($result2)) {
			if (isset($row2->month)) {
				$forecast_total += $in_forecast[$row2->month];
			}
		}
	
		if (($forecast_total*$energy_use_forecast)>$max_forecast) {
			$max_forecast=$forecast_total*$energy_use_forecast;
		}
	
		$dal_value=0;
		$piek_value=0;
		$dalterug_value=0;
		$piekterug_value=0;
		$solar_value=0;
		$verbruikt=0;
	
		if (isset($row1->dal)) {
			$dal_value= $row1->dal;
			$piek_value= $row1->piek;
			$dalterug_value= $row1->dalterug;
			$piekterug_value= $row1->piekterug;
			$solar= $row1->solar;
	
			$verbruikt = $solar - $dalterug_value - $piekterug_value;
			if ($verbruikt < 0) {
				$verbruikt = 0;
			}
			$count++;
		}

		if (strlen($data)>0) {
			$data.=',';
		}
		$data .= "['".date("Y", $time)."',";
		$price2 = ($dal_value + $piek_value + $verbruikt)*$energy_price;
		if ($eid==EVENT_KWH) {
			$data .= round($dal_value,2).','.round($piek_value,2).','.round($verbruikt,2).','.round(($forecast_total*$energy_use_forecast),2).']';
		} else { 
			$data .= round($price2,2).']';
		}
		$total += $dal_value + $piek_value + $verbruikt;
		$total_price += $price2;
	}

	if ($eid==EVENT_KWH) {
		$json = "[['','".t('USED_LOW_KWH')."','".t('USED_HIGH_KWH')."','".t('USED_LOCAL_KWH')."','".t('FORECAST_KWH')."'],".$data."]";
	} else { 
		$json = "[['','".t('EURO')."'],".$data."]";
	}

	general_header();

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
		$page .= '
                         colors: ["#0066cc", "#808080"],
		         vAxis: {   
                                   format:"decimal", 
                                   viewWindow: { min: 0, max: "'.round($max_forecast+50).'" },
                                 },';
	} else {
		$page .= 'colors: ["#e0440e"],';
	}
		
	$page .= 'series: {
            0: { targetAxisIndex: 0 },
            1: { targetAxisIndex: 0 },
            2: { targetAxisIndex: 0 },
            3: { targetAxisIndex: 1 },
          },
        };

        var data = google.visualization.arrayToDataTable('.$json.');
        var chart = new google.charts.Bar(document.getElementById("chart_div"));
        chart.draw(data, google.charts.Bar.convertOptions(options));

        google.visualization.events.addListener(chart, "select", selectHandler);

        function selectHandler(e)     {
           var year = data.getValue(chart.getSelection()[0].row, 0);
			  link("pid='.PAGE_YEAR_IN_ENERGY.'&eid='.$eid.'&date="+year+"-1-1");
        }
      }
    </script>';
  
	$page .= '<h1>'.t('TITLE_YEARS_IN_KWH',($year-10),$year).'</h1>';
        $page .= '<div id="chart_div" style="'.plaatenergy_db_get_config_item('chart_dimensions').'"></div>';

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

function plaatenergy_years_in_energy() {

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

		case PAGE_YEARS_IN_ENERGY:
			return plaatenergy_years_in_energy_page();
			break;
	}
}

/*
** ---------------------
** THE END
** ---------------------
*/

?>
