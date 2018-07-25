<?php
if (!isset($_SESSION)) session_start();
include ("include/UrlParam2PhpVar.inc.php");
if (!isset($_SESSION['id_user']) || $_SESSION['id_user'] == "")
{
  exit();
}
require "admin.inc.php";
require 'fonction.inc.php';
require "lang$lg.inc.php";
include("fckeditor/fckeditor.php") ;
include("include/varGlobals.inc.php") ;
dbConnect();
?>

<html>
<head>
<title>Formagri : Editeur HTML</title>

<style>
<!--
body, td, input, select, textarea{font-size: 12px;font-family: Arial, Verdana, Sans-Serif;}
h1{font-weight: bold;font-size: 180%;margin-bottom: 10px;}
form{margin: 0px 0px 0px 0px; padding: 0px 0px 0px 0px;}
A         {font-family:arial;font-size:12px;color:#24677A;text-decoration:none}
A:link    {font-family:arial;font-size:12px;color:#24677A;font-weight:bold}
A:visited {font-family:arial;font-size:12px;color:#24677A;font-weight:bold}
A:hover   {font-family:arial;font-size:12px;color=#D45211;font-weight:bold}
A.off     {font-family:arial;font-size:12px;color:#24677A;font-weight:bold}
.zone_saisie {
border: 2px inset; font-family: Times New Roman; background-color: #FFFFFF;
padding: 2px; font-size: 14px; width: 700px; height: 450px; word-wrap: break-word; overflow: auto;
}
-->
</style>
</head>
<body marginwidth='0' marginheight='0' leftmargin='0' topmargin='0'>
<?php
unset($_SESSION['rallongement']);
echo "<CENTER><TABLE bgColor='#298CA0' cellspacing='2' width='100%' height='100%'><TR><TD valign='top'>";
echo "<TABLE bgColor='#FFFFFF' cellspacing='2' width='100%' height='100%'>";
if ($objet == 'new')
  $msg1 = $mess_modcreat_html;
else
  $msg1 = $mess_modedit_html;
echo "<TR><TD background=\"images/fond_titre_table.jpg\" colspan='2' height='34' align='center' valign='top'><Font size='3' color='#FFFFFF'><B>$msg1</B></FONT></TD></TR>";
//if (!isset($objet) || (isset($objet) && $objet != 'new'))
// echo "<TR><TD colspan='2'>$msg1</TD></TR>";
// debut du formulaire
 echo "<TR><TD colspan='2' valign='top'>";
echo "<FORM name='form1' action=\"sauver_page.php\" method=\"POST\">";
if (strstr($adresse_http,"localhost") || strstr($adresse_http,"127.0.0"))
   $rallongement = dirname($_SERVER["PHP_SELF"])."/".$rallonge;
else
   $rallongement = $_SESSION['monURI']."/".$rallonge;
$_SESSION['rallongement'] = $rallongement;
$sBasePath = "fckeditor/" ;
$oFCKeditor = new FCKeditor('FCKeditor1') ;
$oFCKeditor->BasePath = $sBasePath ;
if (isset($fichier) && $objet != 'new')
{
  echo "<font size='2'><CENTER>$mess_suivi_nom_fic  : <b>$fichier</b><BR>&nbsp;";
  $fichier=$rallonge."/".$fichier;
  echo "<INPUT TYPE='HIDDEN' name='nom_file' value=\"$fichier\">";
  $html = implode('', file($fichier));
  $oFCKeditor->Value = $html ;
  $oFCKeditor->Create() ;
}
else
{
  echo "&nbsp;<BR><CENTER><font size='2'><b>$mess_nomfic_html  ";
  echo "<INPUT TYPE='TEXT' name='nom_file'>";
  echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$msq_titre ".
       " <INPUT TYPE='TEXT' name='titre' value=\"$titre\"></b></font><P>";
  echo "<INPUT TYPE='HIDDEN' name='rallonge' value=\"$rallonge\">";
  echo "<INPUT TYPE='HIDDEN' name='objet' value=\"$objet\"></CENTER>";
  $oFCKeditor->Value = $html ;
  $oFCKeditor->Create() ;
}
echo "<INPUT TYPE=\"HIDDEN\" NAME=\"direct\" VALUE=\"1\">";
if (isset($parent))
   echo "<INPUT TYPE=\"HIDDEN\" NAME=\"parent\" VALUE=\"$parent\">";
echo "<INPUT TYPE=\"HIDDEN\" NAME=\"id_grp\" VALUE=\"$id_grp\">";
echo "<INPUT TYPE=\"HIDDEN\" NAME=\"communes_groupe\" VALUE=\"$communes_groupe\">";
echo "<INPUT TYPE=\"HIDDEN\" NAME=\"formateurs\" VALUE=\"$formateurs\">";
echo "<INPUT TYPE=\"HIDDEN\" NAME=\"dos\" VALUE=\"$dos\">";
echo "</TD></TR><TR><TD align='center' colspan='2'><A HREF=\"javascript:document.form1.submit();\" onmouseover=\"img1.src='images/fiche_identite/boutvalidb.gif';return true;\" onmouseout=\"img1.src='images/fiche_identite/boutvalid.gif'\">".
       "<IMG NAME=\"img1\" SRC=\"images/fiche_identite/boutvalid.gif\" BORDER='0' onLoad=\"tempImg=new Image(0,0); tempImg.src='images/fiche_identite/boutvalidb.gif'\"></A>";
echo "</TD></TR></FORM></TABLE></TD></TR></TABLE>";
echo "</body></html>";
?>