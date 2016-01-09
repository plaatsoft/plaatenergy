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

day_parameters();

plaatenergy_db_connect($dbhost, $dbuser, $dbpass, $dbname);

$energy_meter_reading_low = plaatenergy_db_get_config_item('energy_meter_reading_low');
$energy_meter_reading_normal = plaatenergy_db_get_config_item('energy_meter_reading_normal');
$gas_meter_reading = plaatenergy_db_get_config_item('gas_meter_reading');

$dal = $energy_meter_reading_low;
$piek = $energy_meter_reading_normal;
$gas = $gas_meter_reading;
$dalterug = 0;
$piekterug = 0;
$etotal = 0;
$round = 3;

if ($type==2) {

  $sql = 'truncate table energy_day';
  $result = $conn->query($sql);

} else {

  $timestamp1 = $prev_year.'-'.$prev_month.'-'.$prev_day.' 00:00:00';
  $timestamp2 = $prev_year.'-'.$prev_month.'-'.$prev_day.' 23:59:59';

  $sql2  = 'select dal, piek, dalterug, piekterug, gas from energy ';
  $sql2 .= 'where timestamp>="'.$timestamp1.'" and timestamp<="'.$timestamp2.'" ';
  $sql2 .= 'order by timestamp desc limit 0,1';

  $result2 = plaatenergy_db_query($sql2);
  $data2 = plaatenergy_db_fetch_object($result2);

  $dal = $data2->dal;
  $piek = $data2->piek;
  $dalterug = $data2->dalterug;
  $piekterug = $data2->piekterug;
  $gas = $data2->gas;

  $sql3  = 'select etotal from solar ';
  $sql3 .= 'where timestamp>="'.$timestamp1.'" and timestamp<="'.$timestamp2.'" ';
  $sql3 .= 'order by timestamp desc limit 0,1';

  $result3 = plaatenergy_db_query($sql3);
  $data3 = plaatenergy_db_fetch_object($result3);

  $etotal = $data3->etotal;
}

if ($type==2) {
   $sql1 = 'select cast(timestamp as date) as date from energy group by date';
} else {
   $timestamp1 = $year.'-'.$month.'-'.$day.' 00:00:00';
   $timestamp2 = $year.'-'.$month.'-'.$day.' 23:59:59';

   $sql1  = 'select cast(timestamp as date) as date from energy ';
   $sql1 .= 'where timestamp>"'.$timestamp1.'" and timestamp<"'.$timestamp2.'" limit 0,1';
}
$result1 = plaatenergy_db_query($sql1);
while ($data1 = plaatenergy_db_fetch_object($result1)) {

  $timestamp1 = $data1->date.' 00:00:00';
  $timestamp2 = $data1->date.' 23:59:59'; 

  $sql2  = 'select dal, piek, dalterug, piekterug, gas from energy ';
  $sql2 .= 'where timestamp>="'.$timestamp1.'" and timestamp<="'.$timestamp2.'" ';
  $sql2 .= 'order by timestamp desc limit 0,1';

  $result2 = plaatenergy_db_query($sql2);
  $data2 = plaatenergy_db_fetch_object($result2);

  $sql3  = 'select etotal from solar ';
  $sql3 .= 'where timestamp>="'.$timestamp1.'" and timestamp<="'.$timestamp2.'" ';
  $sql3 .= 'order by timestamp desc limit 0,1';

  $result3 = plaatenergy_db_query($sql3);
  $data3 = plaatenergy_db_fetch_object($result3);

  if ($dal>$data2->dal) {
    $dal_diff = round($data2->dal,$round);
  } else {
    $dal_diff = round($data2->dal-$dal,$round);
  }

  if ($piek>$data2->piek) {
    $piek_diff = round($data2->piek,$round);
  } else {
    $piek_diff = round($data2->piek-$piek,$round);
  }

  if ($dalterug>$data2->dalterug) {
    $dalterug_diff = round($data2->dalterug,$round);
  } else {
    $dalterug_diff = round($data2->dalterug-$dalterug,$round);
  }

  if ($piekterug>$data2->piekterug) {
    $piekterug_diff = round($data2->piekterug,$round);
  } else {
    $piekterug_diff = round($data2->piekterug-$piekterug,$round);
  }

  if ($gas>$data2->gas) {
    $gas_diff = round($data2->gas,$round);
  } else {
    $gas_diff = round($data2->gas-$gas,$round);  
  }

  if ($etotal>$data3->etotal) {
    $solar_diff = round($data3->etotal,$round);
  } else {
    $solar_diff = round($data3->etotal-$etotal,$round);  
  }

  if ($type!=2) {
 
     $sql4 = 'select id from energy_day where date="'.$data1->date.'"'; 

     $result4 = plaatenergy_db_query($sql4);
     $data4 = plaatenergy_db_fetch_object($result4);

     if ( isset($data4->id) ) {

         $sql3  = 'update energy_day set dal='.$dal_diff.', piek='.$piek_diff.', dalterug='.$dalterug_diff.', piekterug='.$piekterug_diff.',';
         $sql3 .= 'solar='.$solar_diff.', gas='.$gas_diff.' where id='.$data4->id; 

     } else {

         $sql3  = 'INSERT INTO energy_day (`id`, `date`, `dal`, `piek`, `dalterug`, `piekterug`, `solar`, `gas`) ';
         $sql3 .= 'VALUES (NULL, "'.$data1->date.'", "'.$dal_diff.'", "'.$piek_diff.'", "'.$dalterug_diff.'", "';
         $sql3 .= $piekterug_diff.'", "'.$solar_diff.'","'.$gas_diff.'")';
     }

  } else {
       
    $sql3  = 'INSERT INTO energy_day (`id`, `date`, `dal`, `piek`, `dalterug`, `piekterug`, `solar`, `gas`) ';
    $sql3 .= 'VALUES (NULL, "'.$data1->date.'", "'.$dal_diff.'", "'.$piek_diff.'", "'.$dalterug_diff.'", "';
    $sql3 .= $piekterug_diff.'", "'.$solar_diff.'","'.$gas_diff.'")';
  }

  plaatenergy_db_query($sql3);

  if ($data2->dal>0) {
     $dal = $data2->dal;
  }
  
  if ($data2->piek>0) {
     $piek = $data2->piek;
  }
  
  if ($data2->dalterug>0) {
     $dalterug = $data2->dalterug;
  }

  if ($data2->piekterug>0) {
     $piekterug = $data2->piekterug;
  }

  if ($data2->gas>0) {
     $gas = $data2->gas;
  }

  if ($data3->etotal>0) {
     $etotal = $data3->etotal;
  }
}

