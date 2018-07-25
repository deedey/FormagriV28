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
    $PoubelleMail = mysql_query("update messagerie set lu = 1 where mess_cdn = ".$_GET['idMail']);
   echo utf2Charset("Ce mail bien que déjà lu est considéré comme non lu",'iso-8859-1');
}
?>
