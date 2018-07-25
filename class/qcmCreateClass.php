<?php
if (!isset($_SESSION)) session_start();
if (!isset($_SESSION['id_user']) || $_SESSION['id_user'] == "")
{
  exit();
}
header("Content-type  : text/plain");
header("Cache-Control: no-cache, max-age=0");
require '../fonction.inc.php';
require '../admin.inc.php';
require "../lang$lg.inc.php";
require "../fonction_html.inc.php";
dbConnect();
$retour = '#'.strval($Nj+1).'_prop';
$_SESSION['NombreProp']++;
echo $retour;
?>