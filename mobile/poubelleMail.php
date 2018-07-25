<?php
session_start();
if (!isset($_SESSION['IDUSER']) || $_SESSION['IDUSER'] == "")
{
  exit();
}
require ("../admin.inc.php");
require ("../fonction.inc.php");
require ("../fonction_html.inc.php");
dbConnect();
if (isset($_GET['idMail']))
{
    $PoubelleMail = mysql_query("update messagerie set supprime = 1 where mess_cdn = ".$_GET['idMail']);
   echo utf2Charset("Ce mail a été mis à la poubelle en attendant sa suppression définitive",'iso-8859-1');
}
?>
