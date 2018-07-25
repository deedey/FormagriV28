<?php
if (!isset($_SESSION)) session_start();
header("Content-type  : text/plain");
header("Cache-Control: no-cache, max-age=0");
require '../admin.inc.php';
require '../fonction.inc.php';
require "../lang$lg.inc.php";
require "../langues/adm.inc.php";
require '../fonction_html.inc.php';
dbConnect();
if (isset($AffectApp) && $AffectApp == 1 && isset($chxDt))
{
     if ($chxDt == '1')
         $updt_css = mysql_query ("UPDATE user_config SET ucfg_affgrp_on = '1' WHERE ucfg_user_no = '$id_user'");
     elseif($chxDt == '2')
         $updt_css = mysql_query ("UPDATE user_config SET ucfg_affgrp_on = '0' WHERE ucfg_user_no = '$id_user'");
     $mess_notif = "Votre configuration personnelle pour l'inscription d'un nouvel apprenant à une formation a changé...";
}
sleep(1);
echo utf2Charset(stripslashes($mess_notif),$charset);
?>