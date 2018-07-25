<?php
if (!isset($_SESSION)) session_start();
include ('include/UrlParam2PhpVar.inc.php');
require 'fonction_html.inc.php';

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
error_reporting (E_ALL);
if (ini_get('auto_prepend_file') == '')
    ini_set('auto_prepend_file',$_SERVER['DOCUMENT_ROOT'].$_SESSION['monURI'].'/prepend.php');
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
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<META HTTP-EQUIV="Content-Type" content="text/html; charset=<?php  echo $charset;?>">
<META HTTP-EQUIV="Content-Language" CONTENT="<?php  echo $code_langage ;?>">
<META HTTP-EQUIV="Expires" CONTENT="Fri, Jan 01 1900 00:00:00 GMT">
<META NAME="ROBOTS" CONTENT="No Follow">
<META HTTP-EQUIV="Cache-Control" CONTENT="no-cache, must-revalidate">
<META HTTP-EQUIV="Pragma" CONTENT="no-cache">
<link rel="stylesheet" type="text/css" href="<?php echo $monURI;?>/general.css" />
<link rel="stylesheet" type="text/css" href="<?php echo $monURI;?>/admin/style_admin.css" />
<link rel="shortcut icon" href="images/icone.ico" type="image/x-icon" />
<script type="text/javascript" src="<?php echo $monURI;?>/OutilsJs/jquery-113.js"></script>
<script type="text/javascript">
   var _gaq=_gaq||[];var pluginUrl = '//www.google-analytics.com/plugins/ga/inpage_linkid.js';_gaq.push(['_require', 'inpage_linkid', pluginUrl]);_gaq.push(['_setAccount','UA-42020054-1']);_gaq.push(['_trackPageview']);_gaq.push(['_setCustomVar', 1, window.location.host, window.location.host, 1]);_gaq.push(['_setDomainName','none']);(function(){var ga=document.createElement('script');ga.type='text/javascript';ga.async=true;ga.src=('https:'==document.location.protocol?'https://ssl':'http://www')+'.google-analytics.com/ga.js';var s=document.getElementsByTagName('script')[0];s.parentNode.insertBefore(ga,s)})();
</script>
<TITLE>Formagri :: <?php echo str_replace('.educagri.fr','',str_replace('cfppa-','',str_replace('ef-','',$_SERVER['SERVER_NAME']))).
                         ' :: '.$RoleUser.' :: '.$currentUser;?></TITLE>
<style type="text/css">
BODY {
      font-family: arial;
       font-size: 12px;
       color: #333333
       }
TD   {
      font-family: arial;
      font-size: 12px;
      color: #333333 }
TH   {
      font-family: arial;
      font-size: 12px;
      color: #333333 }

#titre A:link{
              background-repeat:no-repeat;
              background-position:1% 50%;
              color:#3BACC4;
              }
#titre A:visited{
                 background-repeat:no-repeat;
                 background-position:1% 50%;
                 color:#3BACC4;
                 }
#titre A:hover {
                background-repeat:no-repeat;
                background-position:1% 50%;
                color:#D45211;
                }

#menu A:link{
             background-repeat:no-repeat;
             background-position:1% 50%;
             color:#002D44;
             }
#menu A:visited{
                background-repeat:no-repeat;
                background-position:1% 50%;
                color:#002D44;
                }
#menu A:hover {
               background-repeat:no-repeat;
               background-position:1% 50%;
               color:#D45211;
               }

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
.mon_bouton1_actif{
     height:20px;text-decoration:none;color:white;text-align:center;font-weight:bold;
     background-image:url(images/ecran-annonce/ongl01.gif);padding:2px;border-width:0px;
}

a.bouton2:link{height:20px;text-decoration:none;color:white;text-align:center;font-weight:bold;
     background-image:url(<?php  echo $bandeau;?>);padding:1px;}
a.bouton2:visited{height:20px;text-decoration:none;color:white;text-align:center;font-weight:bold;
     background-image:url(<?php  echo $bandeau;?>);padding:1px;}
a.bouton2:hover{height:20px;text-decoration:none;color:#002D44;text-align:center;font-weight:bold;
     background-image:url(images/ecran-annonce/ongl02.gif);padding:1px;}
a.bouton2:active{height:20px;text-decoration:none;color:#002D44;text-align:center;font-weight:bold;
     background-image:url(images/ecran-annonce/ongl02.gif);padding:1px;}
a.bouton2:focus{height:20px;text-decoration:none;color:#002D44;text-align:center;font-weight:bold;
     background-image:url(images/ecran-annonce/ongl02.gif);padding:1px;}
.bouton2{text-align:center;padding:4px;}
.mon_bouton2_actif {
     height:20px;text-decoration:none;color:#002D44;text-align:center;font-weight:bold;
     background-image:url(images/ecran-annonce/ongl02.gif);padding:2px;
}

</style>
<SCRIPT LANGUAGE="JavaScript" type="text/javascript">
function open_w(arg){
 open(arg,'window','scrollbars,resizable=yes,width=360,height=550')
}
</script>
</head>
<body marginwidth='0' marginheight='0' leftmargin='0' topmargin='0'>
<div id="overDiv" style="position:absolute; visibility:hidden;z-index:1000;"></div>
<SCRIPT LANGUAGE="JavaScript" type="text/javascript" SRC="overlib.js"><!-- overLIB (c) Erik Bosrup --></SCRIPT>
<div id='msgInst' ></div>
<?php
include ("varGlobals.inc.php");
echo "<TABLE background=\"$bando\" border='0' cellspacing='0' cellpadding='0' width='100%'>".
     "<TR width='100%'><TD align=left>$le_logo</TD>";
echo "<TD valign='middle' align='right'><TABLE background=\"$bando\" cellspacing='0' cellpadding='0'><TR><TD>$son_profil</TD>";
echo "$pour_ajout</TR></TABLE></TD>";
if ($suffixer == '_ru')
    $decon = "images/modules/tut_form/boutdeconec";
$lien="delog.php?bouton=1".TinCanTeach ('formateur|0|0|0|0','delog.php?bouton=1',$adresse_http.'/Suivi');
//$lien = urlencode($lien);
echo "<TD align='right' valign='bottom'><a href=\"$lien\" target='main' onclick ='parent.logo.location=\"rien.html\"' title=\"$mess_dcnx\"".
     " onmouseover=\"img_dec.src='".$decon."b$suffixer.jpg';return true;\" onmouseout=\"img_dec.src='".$decon."$suffixer.jpg'\">";
echo "<IMG NAME=\"img_dec\" SRC=\"".$decon."$suffixer.jpg\" BORDER='0'".
     " onLoad=\"tempImg=new Image(0,0); tempImg.src='".$decon."b$suffixer.jpg'\"></A></TD></TR></TABLE>";
$imgG="<IMG SRC=\"images/ecran-annonce/ongl02.gif\" height=20 border=0>";
$imgD="<IMG SRC=\"images/ecran-annonce/ongl03.gif\" height=20 border=0>";
?>
