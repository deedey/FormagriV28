<?php
if (!isset($_SESSION)) session_start();
include ("include/UrlParam2PhpVar.inc.php");
if (!isset($_SESSION['id_user']) || $_SESSION['id_user'] == "")
{
  exit();
}
require 'fonction.inc.php';
require "lang$lg.inc.php";
require "admin.inc.php";
dbConnect();
include 'style.inc.php';
if (isset($_GET['qui']))
{
  if ($qui == "dossier")
     $result= DirSize($ou."/",$recursive=TRUE);
  elseif ($qui == "autre")
     $result= DirSize("ressources/".$logue."_".$num."/ressources/",$recursive=TRUE);
  elseif ($qui == "perso")
     $result= DirSize("$director/",$recursive=TRUE);
  elseif ($qui == "echange_groupe")
     $result= DirSize("$director/",$recursive=TRUE);
  elseif ($qui == "echange_formateur")
     $result= DirSize("$director/",$recursive=TRUE);
}
else
  $result = DirSize("ressources/",$recursive=TRUE);
if ($result >= 1000000)
  $taille = ($result/1000000)." Mega Octets";
elseif ($result < 1000000 && $result >= 1000)
  $taille = ($result/1000)." Kilo Octets";
elseif ($result < 1000)
  $taille = ($result)." octets";
echo "<CENTER><TABLE bgColor='#298CA0' cellspacing='2' width='100%'><TR><TD>";
echo "<TABLE bgColor='#FFFFFF' cellspacing='1' width='100%'>";
echo "<TR><TD background=\"images/fond_titre_table.jpg\" colspan='2' height='34' align='center' valign='center'>";
echo "<Font size='3' color='#FFFFFF'><B>$taille_serveur</B></FONT></TD></TR>";
echo "<TR><TD>&nbsp;<BR><Font size='2'>$taille_serveur : <B>$taille</B></FONT>";
if ($result > 100000000)
  echo "<BR><Font color='red' size='2'><B>$depasse_serveur</B></FONT>";
echo "<BR>&nbsp;</TD></TR></TABLE></TD></TR></TABLE>";
?>