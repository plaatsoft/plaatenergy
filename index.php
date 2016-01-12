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
include "constants.inc";
include "database.inc";

/*
** ---------------------------------------------------------------- 
** POST
** ---------------------------------------------------------------- 
*/

$mid = PAGE_HOME;

$token = plaatenergy_post("token", "");

if (strlen($token)>0) {
	
  /* Decode token */
  $token = gzinflate(base64_decode($token));	
  $tokens = @preg_split("/&/", $token);
	
  foreach ($tokens as $item) {
     $items = preg_split ("/=/", $item);				
     $$items[0] = $items[1];	
  }
}

/*
** ---------------------------------------------------------------- 
** Database
** ---------------------------------------------------------------- 
*/

plaatenergy_db_connect($dbhost, $dbuser, $dbpass, $dbname);
plaatenergy_db_check_version($version);

/*
** ---------------------------------------------------------------- 
** OUTPUT
** ---------------------------------------------------------------- 
*/

general_header();

switch ($mid) {

  case PAGE_HOME: 
    include "home.inc";
    plaatenergy_home();
    break;

  case PAGE_ABOUT: 
    include "about.inc";
    plaatenergy_about();
    break;

}

general_footer();

?>
