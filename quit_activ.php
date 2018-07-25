<?php
if (!isset($_SESSION)) session_start();
include ("include/UrlParam2PhpVar.inc.php");
if (!isset($_SESSION['id_user']) || $_SESSION['id_user'] == "")
{
  exit();
}
require "admin.inc.php";
require 'fonction.inc.php';
dbconnect();
$date = date("Y-m-d H:i:s" ,time());
list($dtj,$hfin) = explode(" ",$date);
$requete=mysql_query("select traq_cdn from traque where traq_act_no='$id_act' AND traq_grp_no='$numero_groupe' and traq_util_no = '$id_user' and traq_hf_dt = '00:00:00' order by traq_cdn desc");
$nbr = mysql_num_rows($requete);
if ($nbr > 0)
{
   $num_act = mysql_result($requete,0,"traq_cdn");
   $req = mysql_query ("UPDATE traque set traq_hf_dt = '$hfin' where traq_cdn = $num_act");
   $req = mysql_query ("DELETE FROM traque WHERE traq_hf_dt = '00:00:00' AND traq_act_no > 0 AND traq_util_no = '$id_user'");
}
unset($_SESSION['id_act']);
exit;
?>