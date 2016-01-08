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

month_parameters();

plaatenergy_db_connect($dbhost, $dbuser, $dbpass, $dbname);

$energy_price = plaatenergy_db_get_config_item('energy_price');

$dal_first=0;
$piek_first=0;
$dalterug_first=0;
$piekterug_first=0;
$solar_first=0;

$total=0;
$total_price=0;
$count=0;
$data="";

for($d=1; $d<=31; $d++)
{
    $time=mktime(12, 0, 0, $month, $d, $year);          

    // Check you real days
    if (date('m', $time)==$month) {
        $timestamp1=date('Y-m-d 00:00:00', $time);
        $timestamp2=date('Y-m-d 23:59:59', $time);

        $dal_value=0;
        $piek_value=0;
        $solar_value=0;
        $dalterug_value=0;
        $piekterug_value=0;
        $verbruikt=0;

        $sql = 'select sum(dal) as dal, sum(piek) as piek, sum(dalterug) as dalterug, sum(piekterug) as piekterug, sum(solar) as solar FROM energy_day where date>="'.$timestamp1.'" and date<="'.$timestamp2.'"';

        $result = plaatenergy_db_query($sql);
        $row = plaatenergy_db_fetch_object($result);
	
        if (isset($row->dal)) {
          $dal_value= $row->dal;
          $piek_value= $row->piek;
          $dalterug_value= $row->dalterug;
          $piekterug_value= $row->piekterug;
          $solar= $row->solar;

          $verbruikt = $solar-$dalterug_value-$piekterug_value;
          if ($verbruikt<0) {
             $verbruikt=0;
          } 
          $count++;
       }

       if (strlen($data)>0) {
          $data.=',';
       }
       $data .= "['".date("d-m", $time)."',";
       if ($type==1) {
          $data .= round($dal_value,2).','.round($piek_value,2).','.round($verbruikt,2).']';
       } else {
          $data .= round(($dal_value+$piek_value+$solar_value)*$energy_price,2).']';
       }
       $total += $dal_value + $piek_value + $verbruikt;
       $total_price += ($dal_value + $piek_value + $verbruikt)*$energy_price;
    }
}

if ($type==1) {
   $json = "[['','".t('USED_LOW_KWH')."','".t('USED_HIGH_KWH')."','".t('USED_SOLAR_KWH')."'],".$data."]";
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
          stacked:1,
          <?php
          if ($type==2) {
             echo "colors: ['#e0440e'],";
          }
          ?>
        };

        var data = google.visualization.arrayToDataTable(<?php echo $json?>);
        var chart = new google.charts.Bar(document.getElementById('chart_div'));
        chart.draw(data, google.charts.Bar.convertOptions(options));

        google.visualization.events.addListener(chart, 'select', selectHandler);

        function selectHandler(e)     {
           var date = data.getValue(chart.getSelection()[0].row, 0);
           var day = date.split("-");
           window.location="day_in_kwh.php?day="+day[0]+"&month=<?php echo $month.'&year='.$year;?>"
        }

      }
    </script>

<?php

echo '<h1>'.t('TITLE_MONTH_IN_KWH', $month, $year).'</h1>';
echo '<div id="chart_div" style="width: '.$graph_width.'; height: '.$graph_height.';"></div>';

if ($count>0) {
   if ($type==1) {
       text_banner(t('AVERAGE_PER_DAY_KWH', round(($total/$count),2), round($total,2)));
   } else {
       text_banner(t('AVERAGE_PER_DAY_EURO', round(($total_price/$count),2), round($total_price,2)));
   }
} else {
  text_banner('&nbsp;');
}

month_navigation( t('LINK_KWH') );

general_footer();

?>



