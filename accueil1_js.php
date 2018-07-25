<?
if (!isset($_SESSION)) session_start();
if (!isset($_SESSION['id_user']) || $_SESSION['id_user'] == "")
{
  exit();
}
include ("include/UrlParam2PhpVar.inc.php");
require "admin.inc.php";
require 'fonction.inc.php';
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
<HTML>
<HEAD>
<META HTTP-EQUIV="Content-Type" content="text/html; charset=<? echo $charset;?>">
<META HTTP-EQUIV="Content-Language" CONTENT="<? echo $code_langage ;?>">
<META HTTP-EQUIV="Expires" CONTENT="Fri, Jan 01 1900 00:00:00 GMT">
<META NAME="ROBOTS" CONTENT="No Follow">
<META HTTP-EQUIV="Cache-Control" CONTENT="no-cache, must-revalidate">
<META HTTP-EQUIV="Pragma" CONTENT="no-cache">
<TITLE>Formagri ******</TITLE>
<link rel="shortcut icon" type="image/x-icon" href="images/logcnerta.ico" />
</HEAD>
<?
//de quel type est l'utilisateur (apprenant, formateur, administrateur)
if ($typ_user =='APPRENANT' && !isset($numero_groupe) && !isset($continue) && !isset($vient_de_grp)){
   $req_grp = mysql_query("select utilgr_groupe_no from utilisateur_groupe where utilgr_utilisateur_no = $id_user");
   $nombre_groupes = mysql_num_rows($req_grp);
   if ($monURI != "")
   {
      $mon_url = str_replace("$monURI","",getenv('SCRIPT_NAME'));
      $mon_url = str_replace ("/","",$mon_url);
   }else
      $mon_url = substr(getenv(SCRIPT_NAME),1);
   $_SESSION['nombre_groupes'] = $nombre_groupes;
   $_SESSION['mon_url'] = $mon_url;
   $lien="annonce_grp.php?depart=1&full=$full";
   $lien=urlencode($lien);
   echo "<script language='JavaScript'>";
     echo "document.location.replace(\"trace.php?link=$lien\");";
   echo "</script>";
   exit;
}elseif ($typ_user =='APPRENANT' && $numero_groupe > 0 && !isset($continue) && !isset($va_forum) && !isset($vient_de_grp)){
  $_SESSION['numero_groupe'] = $numero_groupe;
  $lien="annonce_app.php";
}elseif ($typ_user =='APPRENANT' && $numero_groupe > 0 && !isset($continue) && !isset($va_forum) && !isset($vient_de_grp)){
  $_SESSION['numero_groupe'] = $numero_groupe;
  $lien="annonce_app.php";
}elseif ($typ_user =='APPRENANT' && $numero_groupe > 0 && !isset($continue) && !isset($vient_de_grp) && $va_forum == 1){
  $_SESSION['numero_groupe'] = $numero_groupe;
  $lien="forum/list.php?f=$f&collapse=0";
}elseif ($typ_user =='TUTEUR' && !isset($continue) && !isset($va_forum) && !isset($vient_de_grp))
  $lien="annonce_tuteur.php?depart=1&activee=1";
elseif ($typ_user =='FORMATEUR_REFERENT' && !isset($continue) && !isset($va_forum) && !isset($vient_de_grp))
  $lien="annonce_formateur.php?depart=1";
elseif (($typ_user =='RESPONSABLE_FORMATION'  || $typ_user =='ADMINISTRATEUR') && !isset($continue) && !isset($va_forum) && !isset($vient_de_grp))
  $lien="annonce_formateur.php?depart=1";
elseif ($typ_user =='TUTEUR' && !isset($continue) && !isset($va_forum) && !isset($vient_de_grp))
  $lien="annonce_form.php?depart=1";
elseif ($continue == 1 && !isset($vient_de_grp)){
  $_SESSION['numero_groupe'] = $numero_groupe;
  $lien = "gest_frm_rappel1.php?a_faire=1&continue=1&le_groupe=$numero_groupe&graph=1&tout=1";
}elseif ($vient_de_grp == 1){
  $_SESSION['numero_groupe'] = $numero_groupe;
  $lien = "gest_frm_rappel1.php?a_faire=1&le_groupe=$numero_groupe&graph=1&tout=1&vient_de_grp=$vient_de_grp";
}
$lien = urlencode($lien);
if ($typ_user == "APPRENANT")
{
   echo "<frameset rows=\"0,0,128,*,35\" border='0'>";
   echo "<frame src=\"deconnection.php\" name='quitter' frameborder='0' noresize scrolling='no' />";
   echo "<frame src=\"blanche.php\" name='chatter' frameborder='0' noresize scrolling='no' />";
   echo "<frame src=\"menu_js.php?full=$full\" name='logo' frameborder='0' noresize scrolling=no />";
}
else
{
   echo "<frameset rows=\"0,0,128,*,35\" border='0'>";
   echo "<frame src=\"deconnection.php?full=$full\" name='quitter' frameborder='0' noresize scrolling='no' />";
   echo "<frame src=\"blanche.php?full=$full\" name='chatter' frameborder='0' noresize scrolling='no' />";
   if ($typ_user =='TUTEUR')
      echo "<frame src=\"menu_tuteur.php?full=$full&affiche_toutapp=0&activee=1\" name='logo' frameborder='0' noresize scrolling=no />";
   else
      echo "<frame src=\"menu_formateur.php?full=$full\" name='logo' frameborder='0' noresize scrolling=no />";
}
echo "<frame src=\"trace.php?link=$lien\" name='main' frameborder='0' noresize scrolling='auto' />";
echo "<frame src=\"bas.php?logue=1\" name=\"ressource\" frameborder='0' noresize scrolling='auto' />";
echo "</frameset>";
?>
<noframes>
<body bgcolor="silver">
<center>
<h2>Votre navigateur n'accepte pas les frames.</h2>
</center>
</body>
</noframes>
</html>
