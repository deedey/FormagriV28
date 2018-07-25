<?php
if (!isset($_SESSION)) session_start();
include ('include/UrlParam2PhpVar.inc.php');
include "include/varGlobals.inc.php";

//ini_set('error_reporting',E_ALL);
if (isset($_SESSION['lg']))
{
  if ($_SESSION['lg'] == "ru")
  {
    $code_langage = "ru";
    $charset = "Windows-1251";
    putenv("TZ=Europe/Moscow");
  }
  elseif ($_SESSION['lg'] == "fr")
  {
    $code_langage = "fr";
    $charset = "iso-8859-1";
    putenv("TZ=Europe/Paris");
  }
  elseif ($_SESSION['lg'] == "en")
  {
    $code_langage = "en";
    $charset = "iso-8859-1";
  }
}
$agent = $_SERVER['HTTP_USER_AGENT'];
if (strstr($agent,"Mac") || strstr($agent,"Konqueror"))
    $mac=1;
if (strstr($agent,"MSIE") || strstr($agent,"Opera"))
    $revient=1;
if (isset($full) && $full == 1 && isset($revient) && $revient == 1 && (!isset($complement) || $complement != 1))
{
   echo '<SCRIPT Language="Javascript" type="text/javascript">
            window.parent.opener.location.reload("index.php?fermeture=1");
        </SCRIPT>';
}
elseif(isset($full) && $full == 1 && $mac != 1 && $revient != 1)
{
      echo '<SCRIPT Language="Javascript" type="text/javascript">
              parent.parent.opener.close();
           </SCRIPT>';
}
$bkg = GetDataField($connect,"select param_etat_lb from param_foad where param_typ_lb = 'bkg'","param_etat_lb");
$currentUser = nomUser($_SESSION['id_user']);
switch ($_SESSION['typ_user'])
{
   case 'ADMINISTRATEUR' : $RoleUser = "Adm";break;
   case 'APPRENANT' : $RoleUser = "App";break;
   case 'RESPONSABLE_FORMATION' : $RoleUser = "RF";break;
   case 'FORMATEUR_REFERENT' : $RoleUser = "FR";break;
   case 'TUTEUR' : $RoleUser = "Tut";break;
}
$nom_user = $_SESSION['name_user'];
$prenom_user= $_SESSION['prename_user'];
$majuscule = $prenom_user." ".$nom_user;
$lien_retour = "http://".$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
//$majuscule = ucwords(strtolower($majuscule));
  $date_du_jour=date("d/m/Y");
  $datearray=getdate();
  $jour_sem=$datearray['weekday'];
  if ($jour_sem == "Monday")
   $jour = $jour_lundi;
  if ($jour_sem == "Tuesday")
   $jour = $jour_mardi;
  if ($jour_sem == "Wednesday")
   $jour = $jour_mercredi;
  if ($jour_sem == "Thursday")
   $jour = $jour_jeudi;
  if ($jour_sem == "Friday")
   $jour = $jour_vendredi;
  if ($jour_sem == "Saturday")
   $jour = $jour_samedi;
  if ($jour_sem == "Sunday")
   $jour = $jour_dimanche;
  $aff_date = $mess_menu_lib." $date_du_jour";
  $averti = 1;
  $_SESSION['averti'] = $averti;
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML>
<HEAD>
<META HTTP-EQUIV="Content-Type" content="text/html; charset=<?php  echo $charset;?>">
<META HTTP-EQUIV="Content-Language" CONTENT="<?php  echo $code_langage ;?>">
<META HTTP-EQUIV="Expires" CONTENT="Fri, Jan 01 1900 00:00:00 GMT">
<META NAME="ROBOTS" CONTENT="No Follow">
<META HTTP-EQUIV="Cache-Control" CONTENT="no-cache, must-revalidate">
<META HTTP-EQUIV="Pragma" CONTENT="no-cache">
<link rel="stylesheet" type="text/css" href="<?php echo $monURI;?>/admin/style_admin.css" />
<link rel="stylesheet" type="text/css" href="<?php echo $monURI;?>/OutilsJs/style_jquery.css" />
<link rel="stylesheet" type="text/css" href="<?php echo $monURI;?>/general.css" />
<link rel="stylesheet" type="text/css" href="<?php echo $monURI;?>/lib/css/box.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo $monURI;?>/OutilsJs/lib/jquery.tabs.css" media="print, projection, screen">
<!-- Additional IE/Win specific style sheet (Conditional Comments) -->
 <!--[if lte IE 7]>
        <link rel="stylesheet" type="text/css" href="<?php echo $monURI;?>/OutilsJs/lib/jquery.tabs-ie.css" media="print, projection, screen">
 <![endif]-->
<?php 
if (strstr($_SERVER['REQUEST_URI'],'annonce_formateur.php'))
    echo '<script type="text/javascript" src="'.$monURI.'/OutilsJs/jquery-113.js?t='.time().'"></script>';
else
    echo '<script type="text/javascript" src="'.$monURI.'/OutilsJs/jquery-144.js?t='.time().'"></script>';
?>
<script type="text/javascript" src="<?php echo $monURI;?>/OutilsJs/lib/ajax/ajax_cms.js"></script>
<script type="text/javascript" src="<?php echo $monURI;?>/OutilsJs/box.js"></script>
<script type="text/javascript" src="<?php echo $monURI;?>/OutilsJs/lib/interface.js"></script>
<script type="text/javascript" src="<?php echo $monURI;?>/general.js"></script>
<script type="text/javascript" src="<?php echo $monURI;?>/fonction.js"></script>
<script type="text/javascript" src="<?php echo $monURI;?>/OutilsJs/lib/jquery.tabs.pack.js?t=<?php echo time();?>"></script>
<script type="text/javascript" src="<?php echo $monURI;?>/OutilsJs/lib/jquery.history_remote.pack.js"></script>
<?php 
if (!strstr($_SERVER['REQUEST_URI'],'annonce_formateur.php'))
{
?>
<script type="text/javascript" src="<?php echo $monURI;?>/OutilsJs/jquery.tooltip.pack.js"></script>
<?php
}
?>
<STYLE type="text/css">
.individ {
     color:#333333;
     background-color:#FFFFFF;
     font-size:11px;
     font-family:arial;
     border:1px solid  #24677A;
}

TD   { font-family: arial; font-size: 11px; color: #333333 }
TH   { font-family: arial; font-size: 11px; color: #333333 }
A         {font-family:arial;font-size:11px;color:#24677A;text-decoration:none}
A:link    {font-family:arial;font-size:11px;color:#24677A;font-weight:bold}
A:visited {font-family:arial;font-size:11px;color:#24677A;font-weight:bold}
A:hover   {font-family:arial;font-size:11px;color:#D45211;font-weight:bold}
A.off     {font-family:arial;font-size:11px;color:#24677A;font-weight:bold}
fieldset {
        border: 1px solid #B5AEA4;
        margin: 1em;
}
#contenu {height: 60px; border: 1px solid #999;overflow: auto;}
#contenu li {list-style-type: disc;}

#titre A:link{background-repeat:no-repeat;background-position:1% 50%;color:#3BACC4;}
#titre A:visited{background-repeat:no-repeat;background-position:1% 50%;color:#3BACC4;}
#titre A:hover {background-repeat:no-repeat;background-position:1% 50%;color:#D45211;}

#menu A:link{background-repeat:no-repeat;background-position:1% 50%;color:#002D44;}
#menu A:visited{background-repeat:no-repeat;background-position:1% 50%;color:#002D44;}
#menu A:hover {background-repeat:no-repeat;background-position:1% 50%;color:#D45211;}

#sequence A:link{background-repeat:no-repeat;background-position:1% 50%;color:#24677A;}
#sequence A:visited{background-repeat:no-repeat;background-position:1% 50%;color:#24677A;}
#sequence A:hover {background-repeat:no-repeat;background-position:1% 50%;color:#D45211;}

#seqgris A:link{background-repeat:no-repeat;background-position:1% 50%;color:#648992;}
#seqgris A:visited{background-repeat:no-repeat;background-position:1% 50%;color:#648992;}
#seqgris A:hover {background-repeat:no-repeat;background-position:1% 50%;color:#D45211;}

</STYLE>
<?php 
if (!strstr($_SERVER['REQUEST_URI'],'annonce_formateur.php'))
{
   echo '<script type="text/javascript">
    $(document).ready(function()
    {
      $("a").tooltip({showURL: false});
      $("div").tooltip({showURL: false});
      $("span").tooltip({showURL: false});
      $("li").tooltip({showURL: false});
      $("input").tooltip({showURL: false});
      setTimeout(function() {$("#mien").empty();},7000);
    });
    </script>';
}
else
{
   echo '<script type="text/javascript">
        $(function() {
           $("#container-1").tabs({ remote: true , fxShow: { height: "show", opacity: "show" }, fxSpeed: "normal"});
        });
    </script>';
}
echo '<TITLE>Formagri :: '.str_replace('.educagri.fr','',str_replace('cfppa-','',str_replace('ef-','',$_SERVER['SERVER_NAME']))).
                         ' :: '.$RoleUser.' :: '.$currentUser.' </TITLE></HEAD>';
      
echo "<script type='text/javascript'>
         var _gaq=_gaq||[];var pluginUrl = '//www.google-analytics.com/plugins/ga/inpage_linkid.js';_gaq.push(['_require', 'inpage_linkid', pluginUrl]);_gaq.push(['_setAccount','UA-42020054-1']);_gaq.push(['_trackPageview']);_gaq.push(['_setDomainName','none']);(function(){var ga=document.createElement('script');ga.type='text/javascript';ga.async=true;ga.src=('https:'==document.location.protocol?'https://ssl':'http://www')+'.google-analytics.com/ga.js';var s=document.getElementsByTagName('script')[0];s.parentNode.insertBefore(ga,s)})();
     </script>";
$image = GetDataField($connect,"select param_etat_lb from param_foad where param_cdn=1","param_etat_lb");
if (strstr($_SERVER['HTTP_USER_AGENT'],"MSIE"))
  $typ_agent ='msie';
echo "<BODY bgcolor=\"$bkg\" style=\"margin-top:12px;\">";
echo "<div id=\"overDiv\" style=\"position:absolute; visibility:hidden;z-index:1000;\"></div>";
echo "<SCRIPT LANGUAGE=\"JavaScript\" type=\"text/javascript\" SRC=\"overlib.js\"><!-- overLIB (c) Erik Bosrup --></SCRIPT>";
$bouton_gauche = "<table cellpadding='0' cellspacing='0' border=0><tbody>".
                 "<tr><td><img src=\"$adresse_http/images/complement/cg.gif\" border='0'></td>".
                 "<td background='$adresse_http/images/complement/milieu.gif' nowrap align='center'><div id='sequence'>";
$bouton_droite = "</div></td><td><img src=\"$adresse_http/images/complement/cd.gif\" border='0'></td></tr></tbody></table>";

?>