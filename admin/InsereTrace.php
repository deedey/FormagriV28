<?php
if (!isset($_SESSION)) session_start();
if (!isset($_SESSION['id_user']) || $_SESSION['id_user'] == "")
{
  exit();
}
header("Content-type  : text/plain");
header("Cache-Control: no-cache, max-age=0");
include ('../include/UrlParam2PhpVar.inc.php');
require '../admin.inc.php';
require '../fonction.inc.php';
require '../fonction_html.inc.php';
dbConnect();
$date_op = date("Y-m-d H:i:s" ,time());
$heure_fiche = substr($date_op,11);
$date_fiche = substr($date_op,0,10);
$id_max = Donne_ID ($connect,"select max(trace_cdn) from trace");
$sql = mysql_query("insert into trace values(\"$id_max\",\"".$_SESSION['login']."\",\"".urldecode($lelien)."\",\"$date_fiche\",\"$heure_fiche\")");
?>