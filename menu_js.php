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
if ($lg == "ru"){
  $code_langage = "ru";
  $charset = "Windows-1251";
}elseif ($lg == "fr"){
  $code_langage = "fr";
  $charset = "iso-8859-1";
}elseif ($lg == "en"){
  $code_langage = "en";
  $charset = "iso-8859-1";
}
//de quel type est l'utilisateur (apprenant, formateur, administrateur)
$agent= $_SERVER['HTTP_USER_AGENT'];
$nom_user = $_SESSION['name_user'];
$prenom_user = $_SESSION['prename_user'];
$email=$_SESSION['email_user'];
$person = $nom_user;
if ($vient_de_menu == 'menu')
{
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
/*
if ($affiche_non == 1){
  echo "<TABLE cellspacing='0' cellpadding='0' width='100%'>".
       "<TR width='100%' background=\"images/menu/fond_logo_formagri.jpg\" border='0'><TD align=left width='800' background=\"images/menu/fond_logo_formagri.jpg\"><IMG SRC=\"images/logo_formagri.jpg\" border='0'></TD>";
  $lien="delog.php";
  $lien = urlencode($lien);
  echo "<TD align='right' valign='bottom' background=\"images/menu/fond_logo_formagri.jpg\"><A href=\"trace.php?link=$lien\" target='main' onclick ='parent.logo.location=\"rien.html\"' title=\"$mess_dcnx\"".
       " onmouseover=\"img_dec.src='images/complement/boutdeconecb.gif';return true;\" onmouseout=\"img_dec.src='images/complement/boutdeconec.gif'\">";
  echo "<IMG NAME=\"img_dec\" SRC=\"images/complement/boutdeconec.gif\" BORDER='0'".
       " onLoad=\"tempImg=new Image(0,0); tempImg.src='images/complement/boutdeconecb.gif'\"></A></TD></TR>";
  echo "<TR width='100%' height='40'><TD align='left' colspan='2'  background=\"images/ecran-annonce/sous_titre_plusmenu.jpg\" valign='top'>".
       "<IMG SRC=\"images/complement/soustitre.gif\" border='0'></TD></TR></TABLE>";
  exit;
}
*/
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
$lien_accueil = "annonce_grp.php?vient_de_menu=menu&menu_prov=1&agenda=0";
$lien_accueil = urlencode($lien_accueil);
$lien_casier = "modif_rep_fic.php";
$lien_casier = urlencode($lien_casier);
$lien_agenda = "agenda.php?apprenant=1&agenda=1";
$lien_agenda = urlencode($lien_agenda);
?>
<TABLE border="0" cellspacing="0" cellpadding="0" width='100%'><TR><TD background="images/menu/fond_menu.jpg" height='20' valign="center">
<A HREF="<?php echo "trace.php?link=$lien_accueil";?>" onclick="javascript:parent.logo.location.replace('menu_launcher.php');" target='main' onMouseover="showit(-1)" class="bouton1">&nbsp;&nbsp;<?php echo $mess_acc ;?>&nbsp;&nbsp;</A>
&nbsp; <A HREF="javascript:void(0);" onMouseover="showit(0)" class="bouton1">  &nbsp;&nbsp;  <?php echo $mess_form_cours ;?>&nbsp;&nbsp; </A>
&nbsp; <A HREF="javascript:void(0);" onMouseover="showit(2)" class="bouton1">  &nbsp;&nbsp;<?php echo $mess_menu_com ;?>&nbsp;&nbsp;  </A>
&nbsp; <A HREF="javascript:void(0);" onMouseover="showit(4)" class="bouton1">  &nbsp;&nbsp;<?php echo $mess_casier_rep_source ;?>&nbsp;&nbsp;  </A>
&nbsp; <A HREF="<?php echo "trace.php?link=$lien_agenda";?>" target="main" onMouseover="showit(-1)" class="bouton1">  &nbsp;&nbsp;<?php echo $mess_menu_agenda ;?>&nbsp;&nbsp;  </A>
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
$lien_formation = "gest_frm_rappel1.php?le_groupe=$numero_groupe&graph=1&tout=1&menu=1&formation=1";
$lien_formation = urlencode($lien_formation);
$lien_planning = "lancement.php?plan=plan_parc&planning=normal";
$lien_planning = urlencode($lien_planning);
$lien_annuaire = "details_parc.php?saut=1&annuaire=1";
$lien_annuaire = urlencode($lien_annuaire);
$lien_bilan = "bilan.php?bilan=1";
$lien_bilan = urlencode($lien_bilan);
?>
submenu[0]='<a href="<?php echo "trace.php?link=$lien_formation";?>" target="main" class="bouton2">&nbsp;&nbsp;<?php echo $mess_menu_present ;?>&nbsp;&nbsp;</a>&nbsp; &nbsp;&nbsp;<a href="<?php echo "trace.php?link=$lien_planning";?>" target=main class="bouton2">&nbsp;&nbsp;<?php echo $mess_lanc_plan_form ;?>&nbsp;&nbsp;</a>&nbsp; &nbsp;&nbsp;<a href="<?php echo "trace.php?link=$lien_annuaire";?>" target="main" class="bouton2">&nbsp;&nbsp;<?php echo $mess_menu_admin_app ;?>&nbsp;&nbsp;</a>&nbsp; &nbsp;&nbsp;<a href="<?php echo "trace.php?link=$lien_bilan";?>" target=main class="bouton2">&nbsp;&nbsp;<?php echo addslashes($mess_menu_bilan) ;?>&nbsp;&nbsp;</a></B></TD></TR></TABLE>'
submenu[1]='<a href="favoris.php?consulter=1&objet=toutes&toutes=1" target="main" class="bouton2">&nbsp;&nbsp;<?php echo $mess_menu_prescrites ;?>&nbsp;&nbsp;</a> <?php $cdi = GetDataField($connect,"select param_etat_lb from param_foad where param_typ_lb='cdi'","param_etat_lb");if ($cdi != ""){?><a href="<?php echo $cdi;?>" target="_blank" class="bouton2">&nbsp;&nbsp;<?php echo $mess_menu_bas_doc;?>&nbsp;&nbsp;</a><?php }?></TD></TR></TABLE>'
<?php
$lien_mail = "messagerie.php?vient_de_menu=menu";
$lien_mail = urlencode($lien_mail);
$lien_rp = "modif_rep_fic.php?communes_groupe=1";
$lien_rp = urlencode($lien_rp);
$grp_req = mysql_query("SELECT utilgr_groupe_no from utilisateur_groupe where utilgr_utilisateur_no = $id_user");
$nbr = mysql_num_rows($grp_req);
if ($nbr > 0){
  $id_grp = $numero_groupe;
  $nom_grp = GetDataField ($connect,"select grp_nom_lb from groupe where grp_cdn  = $id_grp","grp_nom_lb");
  $id_forum = GetDataField ($connect,"select id from forums where name=\"$nom_grp\"","id");
  $carac_grp = strlen($nom_grp);
  if ($carac_grp > 20)
   $nom_grp1 = substr($nom_grp,0,20)."..";
  else
   $nom_grp1 = $nom_grp;
  $course = base64url_encode('apprenant|0|0|0|'.$id_grp);
  $lien_forum = 'forum/list.php?f='.$id_forum.'&collapse=1'.TinCanTeach ('apprenant|0|0|0|'.$id_grp,'forum/list.php?f='.$id_forum.'&collapse=1',$adresse_http.'/Forums - ');
//   $lien_forum="forum/list.php?f=$id_forum&collapse=0";
//   $lien_forum = urlencode($lien_forum);
}
$etat_chat = GetDataField($connect,"select param_etat_lb from param_foad where param_typ_lb='chat'","param_etat_lb");
$etat_flib = GetDataField($connect,"select param_etat_lb from param_foad where param_typ_lb='forum_libre'","param_etat_lb");
$lien_for_lib = "forum/list.php?f=3&collapse=0";
$lien_for_lib = urlencode($lien_for_lib);
$lien_chat = "connecte1.php";
$lien_chat = urlencode($lien_chat);
?>
submenu[2]='<a href="<?php echo "trace.php?link=$lien_mail";?>" target="main" class="bouton2">'+
           '&nbsp;&nbsp;<?php echo $mess_menu_mail ;?>&nbsp;&nbsp;</a>&nbsp;&nbsp; &nbsp;<?php if ($lien_forum != ""){?>'+
           '<A href="<?php echo $lien_forum;?>" target="_blank" class="bouton2">'+
           '&nbsp;&nbsp;<?php echo "$mess_menu_forum $nom_grp1";?>&nbsp;&nbsp;</A><?php }?>&nbsp; &nbsp;&nbsp;'+
           '<?php  if ($etat_flib == "OUI"){?><A href="trace.php?link=<?php echo $lien_for_lib;?>" target="main" class="bouton2">'+
           ' &nbsp;&nbsp;<?php echo "$mess_menu_forum $mess_menu_forum_lib";?>&nbsp;&nbsp;</a>'+
           '&nbsp; &nbsp;&nbsp;<?php } if ($etat_chat == "OUI"){?><a href="javascript:void(0);" class="bouton2" '+
           'onclick="window.open(\'trace.php?link=<?php echo $lien_chat;?>\',\'\',\'scrollbars=1,resizable=yes,width=400,height=305\')">'+
           '&nbsp;&nbsp;<?php echo $mess_menu_chat ;?>&nbsp;&nbsp;</a><?php }?></TD></TR></TABLE>'
<?php
$lien_outils = "moteurs.php?plugins=1";
$lien_outils = urlencode($lien_outils);
$lien_faq = "faq.php";
$lien_faq = urlencode($lien_faq);
?>
submenu[3]='<a href="<?php echo "trace.php?link=$lien_outils";?>" class="bouton2" target="main">&nbsp;&nbsp;<?php echo addslashes($mess_menu_plug) ;?>&nbsp;&nbsp;</a>&nbsp; &nbsp;&nbsp;<a href="<?php echo "trace.php?link=$lien_faq";?>" class="bouton2" target="main">&nbsp;&nbsp;<?php echo $mess_menu_forum_formagri ;?>&nbsp;&nbsp;</a></TD></TR></TABLE>'
submenu[4]='<A HREF="<?php echo "trace.php?link=$lien_casier";?>" class="bouton2" target="main">  &nbsp;&nbsp;<?php echo $mess_casier_rep_source ;?>&nbsp;&nbsp;  </A>&nbsp; &nbsp;&nbsp;<a href="<?php echo "trace.php?link=$lien_rp";?>" class="bouton2" target="main">&nbsp;&nbsp;<?php echo addslashes($mess_menu_casier_groupe);?>&nbsp;&nbsp;</a></TD></TR></TABLE>'
<?php
include ('include/footer_menu.inc.php');
?>

</TD></TR></TABLE>
</body>
</html>