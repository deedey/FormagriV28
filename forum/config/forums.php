<?php
// DO NOT EDIT THIS FILE.  USE THE ADMIN
   require '../graphique/admin.inc.php';

// initialize database variables
  $dbName=$bdd;
  $dbUser=$log;
  $dbPass=$mdp;
  $dbServer=$adresse;

// create database classes
  if ( defined( "_DB_LAYER" ) && $dbName!=''){
    $DB = new db();
    $DB->open($dbName, $dbServer, $dbUser, $dbPass);
    $q = new query($DB); //dummy query for generic operations
  }

// master information
  $DefaultDisplay='1000';
  $PhorumMailCode='';
  $UseCookies='0';
  $SortForums='1';
   $ActiveForums = "4";

  $uploadDir= str_replace('//','/',$repertoire."/ressources/forums");
  $uploadUrl=$adresse_http."/ressources/forums";
  $ext='php';
  $forum_page='index';
  $list_page='list';
  $search_page='search';
  $read_page='read';
  $post_page='post';
  $violation_page='violation';
  $down_page='down';
  $default_lang='lang/french.php';
  $default_table_width='98%';
  $default_table_header_color='#000080';
  $default_table_header_font_color='#FFFFFF';
  $default_table_body_color_1='#FFFFFF';
  $default_table_body_font_color_1='#000000';
  $default_table_body_color_2='#FFFFEA';
  $default_table_body_font_color_2='#000000';
  $default_nav_color='#FFFFEA';
  $default_nav_font_color='#000000';

?>
