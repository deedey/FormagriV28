<?php
if (!isset($_SESSION)) session_start();
include ("include/UrlParam2PhpVar.inc.php");
if (!isset($_SESSION['id_user']) || $_SESSION['id_user'] == "")
{
  exit();
}
//include ("click_droit.txt");
require "admin.inc.php";
require 'fonction.inc.php';
dbConnect();
include ('include/varGlobals.inc.php');
//de quel type est l'utilisateur (apprenant, formateur, administrateur)
include('include/UrlParam2PhpVar.inc.php');

if (($continue == 1 || (isset($vient_de_grp) && $vient_de_grp == 1)) && $typ_user == "APPRENANT")
{
   unset($_SESSION['numero_groupe']);
   $numero_groupe= $le_groupe;
   $_SESSION['numero_groupe'] = $numero_groupe;
}
if (isset($utilisateur) && $utilisateur > 0)
 $num_app = $utilisateur;
else
 $num_app = $id_user;
if (isset($numero_groupe))
{

  $req = mysql_query("select * from traceur where traceur_util_no = $num_app AND traceur_grp_no=$numero_groupe");
  $nbr_trac = mysql_num_rows($req);
}
if ($nbr_trac == 1)
{
   $lien_gest = mysql_result($req,0,"traceur_der_gest1");
   $lien_details = mysql_result($req,0,"traceur_der_details");
   $date_tracee = mysql_result($req,0,"traceur_date_dt");
}
else
{
  $lien = "gest_parc_frm.php?suit=$suit&entantque=$entantque&saut=1&hgrp=$hgrp&utilisateur=$utilisateur&vn=$vn&a_faire=$a_faire&numero_groupe=$numero_groupe&graph=$graph&tout=$tout&formation=0";
  $lien=urlencode($lien);
  echo "<script language=\"JavaScript\">";
  echo "document.location.replace(\"trace.php?link=$lien\");";
  echo "</script>";
  exit();
}
if ($nbr_trac == 1 && $vn !=1 && !isset($vient_de_grp))
{
  echo "<html>";
  echo "<frameset cols=\"324,700\" border='0'>";
  if (isset($menu) && $menu == 1)
  {
    echo "<frame src=\"gest_parc1.php?id_seq=$id_seq&page=$page&hgrp=$hgrp&vn=$vn&accord=$accord&utilisateur=$utilisateur&a_faire=1&seq_ouverte=$seq_ouverte&parc_ouvert=$parc_ouvert&numero_groupe=$numero_groupe&graph=$graph&tout=$tout&formation=$formation\" name=\"idx\" frameborder=\"0\" noresize scrolling=\"auto\" />";
    echo "<frame src=\"details_parc.php?saut=1&a_faire=1\" name=\"principal\" frameborder=\"0\" noresize scrolling=\"auto\" />";
  }
  else
  {
    echo "<frame src=\"$lien_gest&hgrp=$hgrp&utilisateur=$utilisateur&numero_groupe=$numero_groupe&graph=$graph&tout=$tout&formation=$formation\" name=\"idx\" frameborder=\"0\" noresize scrolling=\"auto\" />";
    echo "<frame src=\"$lien_details&date_rappel=$date_tracee&utilisateur=$utilisateur&numero_groupe=$numero_groupe&formation=$formation\" name=\"principal\" frameborder=\"0\" noresize scrolling=\"auto\" />";
  }
  echo "</frameset>";
}
elseif ($nbr_trac == 1 && $vn !=1 && $vient_de_grp == 1)
{
  echo "<html>";
  echo "<frameset cols=\"324,700\" border='0'>";
  echo "<frame src=\"gest_parc1.php?id_seq=$id_seq&page=$page&hgrp=$hgrp&vn=$vn&accord=$accord&utilisateur=$utilisateur&a_faire=1&seq_ouverte=$seq_ouverte&parc_ouvert=$parc_ouvert&numero_groupe=$numero_groupe&graph=$graph&tout=$tout&formation=$formation\" name=\"idx\" frameborder=\"0\" noresize scrolling=\"auto\" />";
  echo "<frame src=\"annonce_app.php?entantque=$entantque&le_groupe=$numero_groupe&utilisateur=$utilisateur&numero_groupe=$numero_groupe&vient_de_menu=$vient_de_menu&depart=$depart\" name=\"principal\" frameborder=\"0\" noresize scrolling=\"auto\" />";
  echo "</frameset>";
}
else
{
  echo "<html>";
  echo "<frameset cols=\"324,700\" border='0'>";
  echo "<frame src=\"gest_parc1.php?tout=$tout&id_seq=$id_seq&page=$page&hgrp=$hgrp&vn=$vn&accord=$accord&utilisateur=$utilisateur&a_faire=1&seq_ouverte=$seq_ouverte&parc_ouvert=$parc_ouvert&numero_groupe=$numero_groupe&graph=$graph&tout=$tout&formation=$formation\" name=\"idx\" frameborder=\"0\" noresize scrolling=\"auto\" />";
  echo "<frame src=\"details_parc.php?depasse=$depasse&proposable=$proposable&prq=$prq&nb_prereq=$nb_prereq&visible=$visible&accord=$accord&saut=$saut&ouvrir=$ouvrir&liste=$liste&utilisateur=$utilisateur&a_faire=$a_faire&seq=$seq_ouverte&id_parc=$parc_ouvert&numero_groupe=$numero_groupe&formation=$formation\" name=\"principal\" frameborder=\"0\" noresize scrolling=\"auto\" />";
  echo "</frameset>";
}

?>
<noframes>
<body bgcolor="silver">
<center>
<h2>Votre navigateur n'accepte pas les frames.</h2>
</center>
</body>
</noframes>
</body></head></html>