<?php
if (!isset($_SESSION)) session_start();
include ("include/UrlParam2PhpVar.inc.php");
if (!isset($_SESSION['id_user']) || $_SESSION['id_user'] == "")
{
  exit();
}
require "admin.inc.php";
require 'fonction.inc.php';
//include ("click_droit.txt");
dbConnect();
include ('include/varGlobals.inc.php');
//de quel type est l'utilisateur (apprenant, formateur, administrateur)
if ($continue == 1)
{
   unset($_SESSION['numero_groupe']);
   $numero_groupe=$le_groupe;
   $_SESSION['numero_groupe'] = $numero_groupe;
}
if (isset($utilisateur) && $utilisateur > 0)
 $num_app = $utilisateur;
else
 $num_app = $id_user;
if (!isset($afaire)) $afaire = '';
$req = mysql_query("select * from traceur where traceur_util_no = $num_app AND traceur_grp_no=$numero_groupe");
$nbr_trac = mysql_num_rows($req);
if ($nbr_trac == 1)
{
   $lien_gest = mysql_result($req,0,"traceur_der_gest1");
   $lien_details = mysql_result($req,0,"traceur_der_details");
   $date_tracee = mysql_result($req,0,"traceur_date_dt");
}
else
{
  $lien = "gest_parc_frm.php?suit=$suit&saut=1&hgrp=$hgrp&utilisateur=$utilisateur&a_faire=$afaire&numero_groupe=$numero_groupe&graph=$graph&tout=$tout&formation=0";
  $lien=urlencode($lien);
  echo "<script language=\"JavaScript\">";
  echo "document.location.replace(\"trace.php?link=$lien\");";
  echo "</script>";
  exit();
}
if ($nbr_trac == 1 && $vn !=1)
{
?>
<html>
<frameset cols="258,766" border='0'>
          <frame src="<?php  echo "$lien_gest&hgrp=$hgrp&utilisateur=$utilisateur&numero_groupe=$numero_groupe&graph=$graph&tout=$tout&formation=$formation";?>" name="idx" frameborder="0" noresize scrolling="auto" />
          <frame src="<?php  echo "$lien_details&date_rappel=$date_tracee&utilisateur=$utilisateur&numero_groupe=$numero_groupe&formation=$formation";?>" name="principal" frameborder="0" noresize scrolling="auto" />
</frameset>
<?php
}
else
{
?>
<html>
<frameset cols="258,766" border='0' >
          <frame src="gest_parc1.php?<?php  echo "tout=$tout&id_seq=$id_seq&page=$page&hgrp=$hgrp&vn=$vn&accord=$accord&utilisateur=$utilisateur&a_faire=1&seq_ouverte=$seq_ouverte&parc_ouvert=$parc_ouvert&numero_groupe=$numero_groupe&graph=$graph&tout=$tout&formation=$formation";?>" name="idx" frameborder="0" noresize scrolling="auto" />
          <frame src="details_parc.php?<?php  echo "depasse=$depasse&proposable=$proposable&prq=$prq&nb_prereq=$nb_prereq&visible=$visible&accord=$accord&saut=$saut&ouvrir=$ouvrir&liste=$liste&utilisateur=$utilisateur&a_faire=$a_faire&seq=$seq_ouverte&id_parc=$parc_ouvert&numero_groupe=$numero_groupe&formation=$formation";?>" name="principal" frameborder="0" noresize scrolling="auto" />
</frameset>
<?php
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