<?php
if (!isset($_SESSION)) session_start();
if (!isset($_SESSION['id_user']) || $_SESSION['id_user'] == "")
{
  exit();
}
header("Content-type  : text/plain");
header("Cache-Control: no-cache, max-age=0");
require '../admin.inc.php';
require '../fonction.inc.php';
include ('../include/UrlParam2PhpVar.inc.php');
require "../lang$lg.inc.php";
require '../langues/notif.inc.php';
require '../langues/adm.inc.php';
require '../fonction_html.inc.php';
require '../class/class_admin.php';
dbConnect();
if ($type == "APPRENANT")
   $annu_nom = strtolower($mes_des_app);
elseif($type == "TUTEUR")
   $annu_nom = strtolower($mes_des_tut);
elseif($type == "FORMATEUR_REFERENT")
   $annu_nom = strtolower($mes_des_rf);
elseif($type == "RESPONSABLE_FORMATION")
   $annu_nom = strtolower($mes_des_fr);
elseif($type == "ADMINISTRATEUR")
   $annu_nom = strtolower($mes_des_adm);

if ($typ_ecran == "n")
{
    $remplace = mysql_query("update parametre set param_ecran='NORMAL' where param_user='$type'");
    $mess_notif = $msgadm_scNrm." ".$mess_pr_les." ".$annu_nom;
}
elseif ($typ_ecran == "m")
{
    $remplace = mysql_query("update parametre set param_ecran='MEDIAN' where param_user='$type'");
    $mess_notif = $msgadm_scMed." ".$mess_pr_les." ".$annu_nom;
}
elseif ($typ_ecran == "p")
{
    $remplace = mysql_query("update parametre set param_ecran='PLEIN' where param_user='$type'");
}
sleep(1);
echo  utf2Charset($mess_notif,$charset);
?>