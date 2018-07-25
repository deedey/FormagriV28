<?php
session_start();
if (!isset($_SESSION['id_user']) || $_SESSION['id_user'] == "")
{
  exit();
}
require "../admin.inc.php";
require '../fonction.inc.php';
dbConnect();
if (!isset($_GET['numero']))
{
   $ReqMind = mysql_query("select mindhisto_xmldata_cmt from mindmaphistory where mindhisto_map_no = ".$_GET['id'].
                          " order by mindhisto_cdn desc");
   $XmlMap = mysql_fetch_object($ReqMind);
   echo html_entity_decode($XmlMap->mindhisto_xmldata_cmt,ENT_QUOTES,'ISO-8859-1');
}
else
{
   $ReqMind = mysql_query("select * from mindmaphistory where mindhisto_cdn = ".$_GET['numero']);
   $XmlMap = mysql_fetch_object($ReqMind);
   echo html_entity_decode($XmlMap->mindhisto_xmldata_cmt,ENT_QUOTES,'ISO-8859-1');
}
?>
