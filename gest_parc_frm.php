<?php
if (!isset($_SESSION)) session_start();
include ("include/UrlParam2PhpVar.inc.php");
if (!isset($_SESSION['id_user']) || $_SESSION['id_user'] == "")
{
  exit();
}
require 'admin.inc.php';
require 'fonction.inc.php';
require "lang$lg.inc.php";
//include ("click_droit.txt");
dbConnect();
include ('include/varGlobals.inc.php');
//de quel type est l'utilisateur (apprenant, formateur, administrateur)
$nom_user=GetDataField ($connect,"select util_nom_lb from utilisateur where util_cdn = $id_user","util_nom_lb");
$prenom_user=GetDataField ($connect,"select util_prenom_lb from utilisateur where util_cdn = $id_user","util_prenom_lb");
if ($utilisateur)
 $num_app = $utilisateur;
else
 $num_app = $id_user;

echo "<html>";
if ($graph == 0)
  echo "<frameset cols='25%,75%' border='0' >";
else
  echo "<frameset cols='35%,65%' border='0' >";
echo "<frame src=\"gest_parc1.php?suit=$suit&saut=$saut&vn=$vn&hgrp=$hgrp&accord=$accord&utilisateur=$utilisateur&a_faire=1&id_seq=$id_seq&seq_ouverte=$seq_ouverte&parc_ouvert=$parc_ouvert&numero_groupe=$numero_groupe&graph=$graph&tout=$tout&formation=$formation\" name='idx' frameborder='0' scrolling='auto' />";
if ($provenance == "graphe")
   echo "<frame src=\"vide.php?titre=$mess_menu_mon_parc&contenu=$mess_suivi_vide&ret=sans\" name='principal' frameborder='0' scrolling='auto' />";
else
   echo "<frame src=\"details_parc.php?depasse=$depasse&prq=$prq&visible=$visible&proposable=$proposable&autorise=$autorise&saut=$saut&hgrp=$hgrp&ouvrir=$ouvrir&liste=$liste&utilisateur=$utilisateur&a_faire=$a_faire&seq=$seq_ouverte&id_parc=$parc_ouvert&seq_ouverte=$seq_ouverte&parc_ouvert=$parc_ouvert&numero_groupe=$numero_groupe&formation=$formation&entantque=$entantque\" name='principal' frameborder='0' scrolling='auto' />";
?>
</frameset>
<noframes>
<body bgcolor="silver">
<center>
<h2>Votre navigateur n'accepte pas les frames.</h2>
</center>
</body>
</noframes>
</body></head></html>