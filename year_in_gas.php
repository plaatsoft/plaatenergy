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

year_parameters();

plaatenergy_db_connect($dbhost, $dbuser, $dbpass, $dbname);

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

   if ($type==1) {
      $data .= round($value,2).','.round(($gas_forecast[$m]*$gas_use_forecast),2).']';
   } else { 
      $data .= round($price2,2).']';
   }
   $total += $value;
   $total_price += $price2;
}

if ($type==1) {
   $json = "[['','".t('USED_M3')."','".t('FORECAST_M3')."'],".$data."]";
} else { 
   $json = "[['','".t('EURO')."'],".$data."]";
}

general_header();

?>

   <script type="text/javascript" src="https://www.google.com/jsapi"></script>
    <script type="text/javascript">
      google.load("visualization", "1", {packages:["bar"]});
      google.setOnLoadCallback(drawChart);
      function drawChart() {

       var options = {
          bars: 'vertical',
          bar: {groupWidth: "90%"},
          legend: { position: 'none' },
          vAxis: {format: 'decimal'},
          <?php
          if ($type==1) {
             echo "colors: ['#0066cc', '#808080']";
          } else {
             echo "colors: ['#e0440e']";
          }
          ?>

        };

        var data = google.visualization.arrayToDataTable(<?php echo $json?>);
        var chart = new google.charts.Bar(document.getElementById('chart_div'));
        chart.draw(data, google.charts.Bar.convertOptions(options));

        google.visualization.events.addListener(chart, 'select', selectHandler);

        function selectHandler(e)     {
           var date = data.getValue(chart.getSelection()[0].row, 0);
           var month = date.split("-");
           window.location="month_in_gas.php?month="+month[0]+"&year=<?php echo $year;?>"
        }
     }

    </script>

<?php

echo '<h1>'.t('TITLE_YEAR_IN_M3', $year).'</h1>';
echo '<div id="chart_div" style="width: '.$graph_width.'; height: '.$graph_height.';"></div>';

if ($count>0) {
  if ($type==1) {
     text_banner(t('AVERAGE_PER_MONTH_M3', round(($total/$count),2), round($total,2)));
  } else {
     text_banner(t('AVERAGE_PER_MONTH_EURO', round(($total_price/$count),2), round($total_price,2)));
  }
} else {
   text_banner('&nbsp;');
}

year_navigation(t('LINK_M3'));

general_footer();

?>
