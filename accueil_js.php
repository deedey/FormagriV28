<?php
if (!isset($_SESSION)) session_start();
if (!isset($_SESSION['id_user']) || $_SESSION['id_user'] == "")
{
 exit();
}
require "admin.inc.php";
require 'fonction.inc.php';
require 'fonction_html.inc.php';
include ("include/UrlParam2PhpVar.inc.php");
ini_set('error_reporting',1);
ini_set('display_errors',1);
//include ("click_droit.txt");
dbConnect();
if ($lg == "ru"){
  $code_langage = "ru";
  $charset = "Windows-1251";
  putenv("TZ=Europe/Moscow");
}elseif ($lg == "fr"){
  $code_langage = "fr";
  $charset = "iso-8859-1";
  putenv("TZ=Europe/Paris");
}elseif ($lg == "en"){
  $code_langage = "en";
  $charset = "iso-8859-1"; 
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xml:lang="<?php  echo $code_langage ;?>" lang="<?php  echo $code_langage ;?>" xmlns="http://www.w3.org/1999/xhtml">
<HEAD>
<META HTTP-EQUIV="Content-Type" content="text/html; charset=<?php  echo $charset;?>">
<META HTTP-EQUIV="Content-Language" CONTENT="<?php  echo $code_langage ;?>">
<META HTTP-EQUIV="Expires" CONTENT="Fri, Jan 01 1900 00:00:00 GMT">
<META NAME="ROBOTS" CONTENT="No Follow">
<META HTTP-EQUIV="Cache-Control" CONTENT="no-cache, must-revalidate">
<META HTTP-EQUIV="Pragma" CONTENT="no-cache">
<TITLE>Formagri ******</TITLE>
<link rel="shortcut icon" type="image/x-icon" href="images/logcnerta.ico" />
<script type="text/javascript" src="OutilsJs/jquery-144.js"></script>
</HEAD>

<?php

//de quel type est l'utilisateur (apprenant, formateur, administrateur)
GLOBAL $full,$vient_de_grp,$continue,$va_forum;
if ($typ_user =='APPRENANT' && !isset($numero_groupe) && !isset($continue) && !isset($vient_de_grp))
{
   $req_grp = mysql_query("select utilgr_groupe_no from utilisateur_groupe where utilgr_utilisateur_no = $id_user");
   $nombre_groupes = mysql_num_rows($req_grp);
   if ($monURI != "")
   {
      $mon_url = $monURI."/".$_SERVER['SCRIPT_NAME'];
      //$mon_url = str_replace ("/","",$mon_url);
   }
   else
      $mon_url = substr($_SERVER['SCRIPT_NAME'],1);
   $_SESSION['nombre_groupes'] = $nombre_groupes;
   $_SESSION['mon_url'] = $mon_url;
   $lien = 'annonce_grp.php?depart=1&full=$full'.TinCanTeach ('apprenant|0|0|0|0','annonce_grp.php?depart=1&full=$full',$adresse_http.'/Suivi');
   $complete = "launcher";
}

elseif ($typ_user =='APPRENANT' && $numero_groupe > 0 && !isset($continue) && !isset($va_forum) && !isset($vient_de_grp))
{
   $_SESSION['numero_groupe'] = $numero_groupe;
   $lien = 'trace.php?link='.urlencode('annonce_app.php');
   $complete = "js";
}
elseif ($typ_user =='APPRENANT' && $numero_groupe > 0 && !isset($continue) && !isset($va_forum) && !isset($vient_de_grp))
{
   $_SESSION['numero_groupe'] = $numero_groupe;
   $lien = 'trace.php?link='.urlencode('annonce_app.php');
   $complete = "js";
}
elseif ($typ_user =='APPRENANT' && $numero_groupe > 0 && !isset($continue) && !isset($vient_de_grp) && $va_forum == 1)
{
   $_SESSION['numero_groupe'] = $numero_groupe;
   $lien = 'trace.php?link='.urlencode('forum/list.php?f=$f&collapse=0');
   $complete = "js";
}
elseif ($typ_user =='TUTEUR' && !isset($continue) && !isset($va_forum) && !isset($vient_de_grp))
   $lien='annonce_tuteur.php?depart=1&affiche_toutapp=0&activee=1'.TinCanTeach ('formateur|0|0|0|0','annonce_tuteur.php?depart=1&affiche_toutapp=0&activee=1',$adresse_http.'/Suivi');
elseif ($typ_user =='FORMATEUR_REFERENT' && !isset($continue) && !isset($va_forum) && !isset($vient_de_grp))
   $lien='annonce_formateur.php?depart=1'.TinCanTeach ('formateur|0|0|0|0','annonce_formateur.php?depart=1',$adresse_http.'/Suivi');
elseif (($typ_user =='RESPONSABLE_FORMATION'  || $typ_user =='ADMINISTRATEUR') && !isset($continue) && !isset($va_forum) && !isset($vient_de_grp))
   $lien='annonce_formateur.php?depart=1'.TinCanTeach ('formateur|0|0|0|0','annonce_formateur.php?depart=1',$adresse_http.'/Suivi');
elseif (isset($numero_groupe) && $numero_groupe > 0)
{
   $complete = "js";
   $_SESSION['numero_groupe'] = $numero_groupe;
   $lien = "gest_frm_rappel1.php?a_faire=1&le_groupe=$numero_groupe&graph=1&tout=1&vient_de_grp=$vient_de_grp";
}
//$lien = urlencode($lien);
if ($typ_user == "APPRENANT")
{
   echo "<frameset rows=\"0,0,128,*\" border='0'>";
   echo "<frame src=\"deconnection.php\" name='quitter' frameborder='0' noresize scrolling='no' />";
   echo "<frame src=\"blanche.php\" name='chatter' frameborder='0' noresize scrolling='no' />";
   echo "<frame src=\"menu_$complete.php?full=$full\" name='logo' frameborder='0' noresize scrolling=no />";
}
else
{
   echo "<frameset rows=\"0,0,128,*\" border='0'>";
   echo "<frame src=\"deconnection.php\" name='quitter' frameborder='0' noresize scrolling='no' />";
   echo "<frame src=\"blanche.php\" name='chatter' frameborder='0' noresize scrolling='no' />";
   if ($typ_user =='TUTEUR')
      echo "<frame src=\"menu_tuteur.php?full=$full&affiche_toutapp=0&activee=1\" name='logo' frameborder='0' noresize scrolling=no />";
   else
      echo "<frame src=\"menu_formateur.php?full=$full\" name='logo' frameborder='0' noresize scrolling=no />";
}
echo "<frame src=\"$lien\" name='main' frameborder='0' noresize scrolling='auto' />";
echo "</frameset>";



?>
<noframes>
<body bgcolor="silver">
<center>
<h2>Votre navigateur n'accepte pas les frames.</h2>
</center>
</body>
</noframes>
</body>
</html>