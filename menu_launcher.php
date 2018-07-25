<?php
if (!isset($_SESSION)) session_start();
include ("include/UrlParam2PhpVar.inc.php");
if (!isset($_SESSION['id_user']) || $_SESSION['id_user'] == "")
{
  exit();
}
require "admin.inc.php";
require 'fonction.inc.php';
require 'fonction_html.inc.php';
require "lang$lg.inc.php";
include ('include/varGlobals.inc.php');
//include ("click_droit.txt");
dbConnect();
if ($lg == "ru")
{
  $code_langage = "ru";
  $charset = "Windows-1251";
}
elseif ($lg == "fr")
{
  $code_langage = "fr";
  $charset = "iso-8859-1";
}
elseif ($lg == "en")
{
  $code_langage = "en";
  $charset = "iso-8859-1";
}
//de quel type est l'utilisateur (apprenant, formateur, administrateur)
$agent= $_SERVER['HTTP_USER_AGENT'];
$nom_user = $_SESSION['name_user'];
$prenom_user = $_SESSION['prename_user'];
$email=$_SESSION['email_user'];
$person = $nom_user;
if ($vient_de_menu == 'menu'){
   unset($_SESSION['numero_groupe']);
   $numero_groupe=$le_groupe;
   $_SESSION['numero_groupe'] = $numero_groupe;
}
 unset($_SESSION['acces']);
 unset($_SESSION['complement']);
 unset($_SESSION['agenda']);
$currentUser = nomUser($_SESSION['id_user']);
switch ($_SESSION['typ_user'])
{
   case 'ADMINISTRATEUR' : $RoleUser = "Adm";break;
   case 'APPRENANT' : $RoleUser = "App";break;
   case 'RESPONSABLE_FORMATION' : $RoleUser = "RF";break;
   case 'FORMATEUR_REFERENT' : $RoleUser = "FR";break;
   case 'TUTEUR' : $RoleUser = "Tut";break;
}
?>
<html>
<head>
<META HTTP-EQUIV="Content-Type" content="text/html; charset=<?php echo $charset;?>">
<META HTTP-EQUIV="Content-Language" CONTENT="<?php echo $code_langage ;?>">
<META HTTP-EQUIV="Expires" CONTENT="Fri, Jan 01 1900 00:00:00 GMT">
<META NAME="ROBOTS" CONTENT="No Follow">
<META HTTP-EQUIV="Cache-Control" CONTENT="no-cache, must-revalidate">
<META HTTP-EQUIV="Pragma" CONTENT="no-cache">
<link rel="stylesheet" type="text/css" href="<?php echo $monURI;?>/general.css" />
<link rel="stylesheet" type="text/css" href="<?php echo $monURI;?>/admin/style_admin.css" />
<script type="text/javascript" src="<?php echo $monURI;?>/OutilsJs/jquery-113.js"></script>
<TITLE>Formagri :: <?php echo str_replace('.educagri.fr','',str_replace('cfppa-','',str_replace('ef-','',$_SERVER['SERVER_NAME']))).
                         ' :: '.$RoleUser.' :: '.$currentUser;?></TITLE>
<style>
BODY { font-family: arial; font-size: 11px; color: #333333 }
TD   { font-family: arial; font-size: 11px; color: #333333 }
TH   { font-family: arial; font-size: 11px; color: #333333 }

#titre A:link{background-repeat:no-repeat;background-position:1% 50%;color:#3BACC4;}
#titre A:visited{background-repeat:no-repeat;background-position:1% 50%;color:#3BACC4;}
#titre A:hover {background-repeat:no-repeat;background-position:1% 50%;color:#D45211;}

#menu A:link{background-repeat:no-repeat;background-position:1% 50%;color:#002D44;}
#menu A:visited{background-repeat:no-repeat;background-position:1% 50%;color:#002D44;}
#menu A:hover {background-repeat:no-repeat;background-position:1% 50%;color:#D45211;}

#sequence A:link{text-align:center;background-repeat:no-repeat;background-position:1% 50%;color:#24677A;}
#sequence A:visited{text-align:center;background-repeat:no-repeat;background-position:1% 50%;color:#24677A;}
#sequence A:hover {text-align:center;background-repeat:no-repeat;background-position:1% 50%;background-color:#FFFFFF;color:#D45211;}
#sequence A:active {text-align:center;background-repeat:no-repeat;background-position:1% 50%;background-color:#FFFFFF;color:#D45211;}
#sequence A:focus {text-align:center;background-repeat:no-repeat;background-position:1% 50%;background-color:#FFFFFF;color:#D45211;}

a.bouton1:link{height:20px;text-decoration:none;color:#002D44;text-align:center;font-weight:bold;
     background-image:url(images/ecran-annonce/ongl02.gif);padding:2px;border-width:0px;}
a.bouton1:visited{height:20px;text-decoration:none;color:#002D44;text-align:center;font-weight:bold;
     background-image:url(images/ecran-annonce/ongl02.gif);padding:2px;border-width:0px;}
a.bouton1:hover{height:20px;text-decoration:none;color:white;text-align:center;font-weight:bold;
     background-image:url(images/ecran-annonce/ongl01.gif);padding:2px;border-width:0px;}
a.bouton1:active{height:20px;text-decoration:none;color:white;text-align:center;font-weight:bold;
     background-image:url(images/ecran-annonce/ongl01.gif);padding:2px;border-width:0px;}
a.bouton1:focus{height:20px;text-decoration:none;color:white;text-align:center;font-weight:bold;
     background-image:url(images/ecran-annonce/ongl01.gif);padding:2px;border-width:0px;}

a.bouton2:link{height:20px;text-decoration:none;color:white;text-align:center;font-weight:bold;
     background-image:url(images/ecran-annonce/ongl01.gif);padding:1px;}
a.bouton2:visited{height:20px;text-decoration:none;color:white;text-align:center;font-weight:bold;
     background-image:url(images/ecran-annonce/ongl01.gif);padding:1px;}
a.bouton2:hover{height:20px;text-decoration:none;color:#002D44;text-align:center;font-weight:bold;
     background-image:url(images/ecran-annonce/ongl02.gif);padding:1px;}
a.bouton2:active{height:20px;text-decoration:none;color:#002D44;text-align:center;font-weight:bold;
     background-image:url(images/ecran-annonce/ongl02.gif);padding:1px;}
a.bouton2:focus{height:20px;text-decoration:none;color:#002D44;text-align:center;font-weight:bold;
     background-image:url(images/ecran-annonce/ongl02.gif);padding:1px;}
.bouton2{text-align:center;padding:4px;}



</style>
<SCRIPT LANGUAGE="JavaScript">
function open_w(arg){
 open(arg,'window','scrollbars,resizable=yes,width=360,height=550')
}
</script>
<TITLE> Barre de navigation</TITLE>
</head>
<body style="margin:0;">
<div id='msgInst' ></div>
<?php
if ($affiche_non == 1){
  echo "<TABLE cellspacing='0' cellpadding='0' width='100%'>".
       "<TR width='100%' background=\"images/menu/fond_logo_formagri.jpg\" border='0'><TD align=left width='800' background=\"images/menu/fond_logo_formagri.jpg\"><IMG SRC=\"images/logo_formagri.jpg\" border='0'></TD>";
  $lien="delog.php".TinCanTeach ('apprenant|0|0|0|0','delog.php',$adresse_http.'/Suivi');
  //$lien = urlencode($lien);
  echo "<TD align='right' valign='bottom' background=\"images/menu/fond_logo_formagri.jpg\"><A href=\"$lien\" target='main' onclick ='parent.logo.location=\"rien.html\"' title=\"$mess_dcnx\"".
       " onmouseover=\"img_dec.src='images/complement/boutdeconecb.gif';return true;\" onmouseout=\"img_dec.src='images/complement/boutdeconec.gif'\">";
  echo "<IMG NAME=\"img_dec\" SRC=\"images/complement/boutdeconec.gif\" BORDER='0'".
       " onLoad=\"tempImg=new Image(0,0); tempImg.src='images/complement/boutdeconecb.gif'\"></A></TD></TR>";
  echo "<TR width='100%' height='40'><TD align='left' colspan='2'  background=\"images/ecran-annonce/sous_titre_plusmenu.jpg\" valign='top'>".
       "<IMG SRC=\"images/complement/soustitre.gif\" border='0'></TD></TR></TABLE>";
  exit;
}
echo "<TABLE background=\"images/menu/fond_logo_formagri.jpg\" border='0' cellspacing='0' cellpadding='0' width='100%'>".
     "<TR width='100%'><TD align=left width='800'><IMG SRC=\"images/logo_formagri.jpg\" border='0'></TD>";
$lien="delog.php".TinCanTeach ('apprenant|0|0|0|0','delog.php',$adresse_http.'/Suivi');
//$lien = urlencode($lien);
echo "<TD align='right' valign='bottom'><A href=\"$lien\" target='main' onclick ='parent.logo.location=\"rien.html\"' title=\"$mess_dcnx\"".
     " onmouseover=\"img_dec.src='images/complement/boutdeconecb.gif';return true;\" onmouseout=\"img_dec.src='images/complement/boutdeconec.gif'\">";
echo "<IMG NAME=\"img_dec\" SRC=\"images/complement/boutdeconec.gif\" BORDER='0'".
     " onLoad=\"tempImg=new Image(0,0); tempImg.src='images/complement/boutdeconecb.gif'\"></A></TD></TR></TABLE>";
$imgG="<IMG SRC=\"images/ecran-annonce/ongl02.gif\" height=20 border=0>";
$imgD="<IMG SRC=\"images/ecran-annonce/ongl03.gif\" height=20 border=0>";
?>
<TABLE height=40 border="0" cellspacing="0" cellpadding="0" width=100%><TR><TD align="left" valign="center">
<!--Links used to initiate the sub menus. Pass in the desired submenu index numbers (ie: 0, 1) -->
<?php
$lien_accueil = "annonce_grp.php";
$lien_accueil = urlencode($lien_accueil);
$lien_casier = "modif_rep_fic.php";
$lien_casier = urlencode($lien_casier);
$lien_agenda = "agenda.php?apprenant=1&agenda=1";
$lien_agenda = urlencode($lien_agenda);
$lien_mail = "messagerie.php?vient_de_menu=&ret=1";
$lien_mail = urlencode($lien_mail);
?>
<TABLE border="0" cellspacing="0" cellpadding="0" width='100%'><TR><TD background="images/menu/fond_menu.jpg" height='20' valign="center">
<A HREF="<?php echo "trace.php?link=$lien_accueil";?>" onclick="javascript:parent.logo.location.replace('menu_launcher.php');" target='main' onMouseover="showit(-1)" class="bouton1">&nbsp;&nbsp;<?php echo $mess_acc ;?>&nbsp;&nbsp;</A>
&nbsp; <A HREF="<?php echo "trace.php?link=$lien_mail";?>" class="bouton1" target="main" onMouseover="showit(-1)">  &nbsp;&nbsp;<?php echo $mess_menu_mail ;?>&nbsp;&nbsp;  </A>
&nbsp; <A HREF="<?php echo "trace.php?link=$lien_casier";?>" class="bouton1" target="main" onMouseover="showit(-1)">  &nbsp;&nbsp;<?php echo $mess_casier_rep_source ;?>&nbsp;&nbsp;  </A>
&nbsp; <A HREF="javascript:void(0);" onMouseover="showit(3)" class="bouton1">&nbsp;&nbsp;<?php echo $mess_menu_aide ;?>&nbsp;&nbsp;</A>
</TD></TR><TR><TD background="images/ecran-annonce/ongl01.gif" height='20' valign="center">

<!-- Edit the dimensions of the below, plus background color-->
<ilayer name="dep1" width=800 height=20>
 <layer name="dep2" width=800 height=20>
 </layer>
</ilayer>
<div id="describe" width:800px;height:20px" onMouseover="clear_delayhide()" ></div>
<script language="JavaScript1.2">
/*
Tabs Menu (mouseover)- By Dynamic Drive
For full source code and more DHTML scripts, visit http://www.dynamicdrive.com
This credit MUST stay intact for use
*/
var submenu=new Array()
//Set submenu contents. Expand as needed. For each content, make sure everything exists on ONE LINE. Otherwise, there will be JS errors.
<?php
$lien_outils = "moteurs.php?plugins=1";
$lien_outils = urlencode($lien_outils);
$lien_faq = "faq.php";
$lien_faq = urlencode($lien_faq);
?>
submenu[3]='<a href="<?php echo "trace.php?link=$lien_outils";?>" class="bouton2" target="main">&nbsp;&nbsp;<?php echo addslashes($mess_menu_plug) ;?>&nbsp;&nbsp;</a>&nbsp; &nbsp;&nbsp;<a href="<?php echo "trace.php?link=$lien_faq";?>" class="bouton2" target="main">&nbsp;&nbsp;<?php echo $mess_menu_forum_formagri ;?>&nbsp;&nbsp;</a></TD></TR></TABLE>'
<?php
include ('include/footer_menu.inc.php');
?>

</TD></TR></TABLE>
</body>
</html>