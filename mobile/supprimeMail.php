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
    $SuppMail = mysql_query("delete from messagerie where mess_cdn = ".$_GET['idMail']);
    echo utf2Charset("Le mail en question a été supprimé définitivement de votre messagerie",'iso-8859-1');
}
?>
