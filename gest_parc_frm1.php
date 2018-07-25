<?php
if (!isset($_SESSION)) session_start();
include ("include/UrlParam2PhpVar.inc.php");
if (!isset($_SESSION['id_user']) || $_SESSION['id_user'] == "")
{
  exit();
}
require ("admin.inc.php");
require ("fonction.inc.php");
dbConnect();
include ('include/varGlobals.inc.php');
//de quel type est l'utilisateur (apprenant, formateur, administrateur)
$nom_user=GetDataField ($connect,"select util_nom_lb from utilisateur where util_cdn = $id_user","util_nom_lb");
$prenom_user=GetDataField ($connect,"select util_prenom_lb from utilisateur where util_cdn = $id_user","util_prenom_lb");
if (isset($utilisateur) && $utilisateur > 0)
 $num_app = $utilisateur;
else
 $num_app = $id_user;
$req = mysql_query("select * from traceur where traceur_util_no = $num_app AND traceur_grp_no = $numero_groupe");
$nbr_trac = mysql_num_rows($req);
$suite = $graph == 1 ? 1:"";
echo "<html><body>";
if (!isset($afaire)) $afaire = '';
if ($nbr_trac == 1 && $vn !=1)
   $lien="gest_frm_rappel$suite.php?saut=$saut&hgrp=$hgrp&utilisateur=$utilisateur&afaire=$afaire&numero_groupe=$numero_groupe&graph=$graph&tout=$tout";
else
   $lien = "gest_parc_frm.php?switch=1&hgrp=$hgrp&saut=$saut&ouvrir=$ouvrir&vn=$vn&accord=$accord&utilisateur=$utilisateur&a_faire=1&seq=$seq_ouverte&seq_ouverte=$seq_ouverte&parc_ouvert=$parc_ouvert&numero_groupe=$numero_groupe&graph=$graph&tout=$tout";
$lien = urlencode($lien);
echo "<script language=\"JavaScript\">";
   echo "document.location.replace(\"trace.php?link=$lien\")";
echo "</script>";
echo "</body></html>";