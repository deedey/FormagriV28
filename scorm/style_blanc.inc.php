<?php
if (!isset($_SESSION['id_user']) || $_SESSION['id_user'] == "")
{
  exit();
}

//error_reporting (E_ALL);
if (isset($_SESSION['lg'])){
  if ($_SESSION['lg'] == "ru"){
    $code_langage = "ru";
    $charset = "Windows-1251";
    putenv("TZ=Europe/Moscow");
  }elseif ($_SESSION['lg'] == "fr"){
    $code_langage = "fr";
    $charset = "iso-8859-1";
    putenv("TZ=Europe/Paris");
  }elseif ($_SESSION['lg'] == "en"){
    $code_langage = "en";
    $charset = "iso-8859-1";
  }
}
$aSuperGlobal = array ('_GET','_POST','_SESSION');
foreach ($aSuperGlobal as $superGlobal)
{
       foreach ($GLOBALS[$superGlobal] as $key => $superGlobalVal)
       {
               $$key = $superGlobalVal;
       }
}
include ('../include/varGlobals.inc.php');

/*
echo "<pre>";
     print_r($_POST);print_r($_GET);
echo "</pre>";
*/
if (isset($lien_origine) && $lien_origine != '' && !strstr($_SERVER['REQUEST_URI'],'modif_rep_fic.php')){
   unset($_SESSION['lien_origine']);
   unset($_SESSION['affi_mouve']);
}
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
<HTML>
<HEAD>
<META HTTP-EQUIV="Content-Type" content="text/html; charset=<?php  echo $charset;?>">
<META HTTP-EQUIV="Content-Language" CONTENT="<?php  echo $code_langage ;?>">
<META HTTP-EQUIV="Expires" CONTENT="Fri, Jan 01 1900 00:00:00 GMT">
<META NAME="ROBOTS" CONTENT="No Follow">
<META HTTP-EQUIV="Cache-Control" CONTENT="no-cache, must-revalidate">
<META HTTP-EQUIV="Pragma" CONTENT="no-cache">
<TITLE>Formagri :: <?php echo str_replace('.educagri.fr','',str_replace('cfppa-','',str_replace('ef-','',$_SERVER['SERVER_NAME']))).
                         ' :: '.$RoleUser.' :: '.$currentUser;?></TITLE>
<link rel="shortcut icon" href="images/icone.ico" type="image/x-icon" />
<script type="text/javascript" src="../OutilsJs/lib/jquery.js"></script>
<script type="text/javascript" src="../OutilsJs/lib/interface.js"></script>
<?php
$ip = @gethostbyname('www.google.fr');
if(isset($ip) && strlen($ip) > 7)
   echo "\n<script type='text/javascript'>
       var _gaq=_gaq||[];var pluginUrl = '//www.google-analytics.com/plugins/ga/inpage_linkid.js';_gaq.push(['_require', 'inpage_linkid', pluginUrl]);_gaq.push(['_setAccount','UA-42020054-1']);_gaq.push(['_trackPageview']);_gaq.push(['_setCustomVar', 1, window.location.host, window.location.host, 1]);_gaq.push(['_setDomainName','none']);(function(){var ga=document.createElement('script');ga.type='text/javascript';ga.async=true;ga.src=('https:'==document.location.protocol?'https://ssl':'http://www')+'.google-analytics.com/ga.js';var s=document.getElementsByTagName('script')[0];s.parentNode.insertBefore(ga,s)})();
   </script>";
?>
<STYLE>


.myTree,
.myTree ul{ list-style: none; padding-left: 2px; margin-left : 2px;}
.expandImage{margin-right: 4px;}
.treeItem{list-style: none; margin-left : 8px;}

.INPUT { background-color: #FFFFFF ; font-family: Arial;font-size: 12px; color: #333333 ; }
.SELECT { background-color: #FFFFFF ; font-family: Arial ;font-size: 12px; color: #333333 ; }
.TEXTAREA { background-color: #FFFFFF ; font-family: Arial ;font-size: 12px; color: #333333 ; }

/*BODY { font-family: arial; font-size: 12px; color: #333333 }*/
TD   { font-family: arial; font-size: 12px; color: #333333 }
TH   { font-family: arial; font-size: 12px; color: #333333 }
A         {font-family:arial;font-size:11px;color:#24677A;text-decoration:none}
A:link    {font-family:arial;font-size:11px;color:#24677A;font-weight:bold}
A:visited {font-family:arial;font-size:11px;color:#24677A;font-weight:bold}
A:hover   {font-family:arial;font-size:11px;color:#D45211;font-weight:bold}
A.off     {font-family:arial;font-size:11px;color:#24677A;font-weight:bold}

#titre A:link{background-repeat:no-repeat;background-position:1% 50%;color:#3BACC4;}
#titre A:visited{background-repeat:no-repeat;background-position:1% 50%;color:#3BACC4;}
#titre A:hover {background-repeat:no-repeat;background-position:1% 50%;color:#D45211;}

#menu A:link{background-repeat:no-repeat;background-position:1% 50%;font-family:arial;font-size:10px;color:#002D44;}
#menu A:visited{background-repeat:no-repeat;background-position:1% 50%;font-family:arial;font-size:10px;color:#002D44;}
#menu A:hover {background-repeat:no-repeat;background-position:1% 50%;font-family:arial;font-size:10px;color:#D45211;}

#sequence A:link{background-repeat:no-repeat;background-position:1% 50%;font-family:arial;font-size:10px;color:#24677A;}
#sequence A:visited{background-repeat:no-repeat;background-position:1% 50%;font-family:arial;font-size:10px;color:#24677A;}
#sequence A:hover {background-repeat:no-repeat;background-position:1% 50%;font-family:arial;font-size:10px;color:#D45211;}

.seqsco A:link{background-repeat:no-repeat;background-position:1% 50%;font-family:arial;font-size:12px;color:#24677A;}
.seqsco A:visited{background-repeat:no-repeat;background-position:1% 50%;font-family:arial;font-size:12px;color:#24677A;}
.seqsco A:hover {background-repeat:no-repeat;background-position:1% 50%;font-family:arial;font-size:12px;color:#D45211;}

.scoseq A:link{background-repeat:no-repeat;background-position:1% 50%;font-family:arial;font-size:12px;color:#D45211;}
.scoseq A:visited{background-repeat:no-repeat;background-position:1% 50%;font-family:arial;font-size:12px;color:#D45211;}
.scoseq A:hover{background-repeat:no-repeat;background-position:1% 50%;font-family:arial;font-size:12px;color:#24677A;}

.heada {font-family: arial; font-size: 12px; color: #333333}
.headaCnt {font-family: arial; font-size: 12px; color: #333333}
.parent {font-family:arial; font-size: 12px; color: #333333; text-decoration: none;font-weight:bold}
.child {font-family:arial; font-size: 12px; color: #333333; text-decoration: none}
.parent:hover {font-family:arial; font-size: 12px; color: #D45211; text-decoration: none;font-weight:bold}
.item, .item:active, .item:hover, .tblItem, .tblItem:active {
  font-family:arial; font-size: 12px;color: #333333; text-decoration: none;font-weight:bold
}
.tblItem:hover {font-family:arial; color: #D45211; font-size: 12px; text-decoration: none}
</STYLE>
<SCRIPT  LANGUAGE="JavaScript1.2" SRC="<?php echo $adresse_http."/calendrier_$lg";?>.js"></SCRIPT>
<script language="javascript" src="<?php echo $adresse_http;?>/fonction.js"></script>
<SCRIPT Language="Javascript">
function fsub() {
        document.MForm.submit();
}
</script>
<SCRIPT Language="Javascript">

function TryCallFunction() {
        var sd = document.MForm.mydate1.value.split("\/");
        document.MForm.iday.value = sd[1];
        document.MForm.imonth.value = sd[0];
        document.MForm.iyear.value = sd[2];
}

function Today() {
        var dd = new Date();
        return((dd.getMonth()+1) + "/" + dd.getDate() + "/" + dd.getFullYear());
}
msgconfm="<?php  echo $mess_admin_valid_modif;?>"
function confm() {
        if ( confirm(msgconfm) )
                return(true);
        return(false);
}
msgconf="<?php  echo $mess_admin_valid_supp;?>"
function conf() {
        if ( confirm(msgconf) )
                return(true);
        return(false);
}
msgconfv="<?php  echo $mess_gen_val_sais;?>"
function confv() {
        if ( confirm(msgconfv) )
                return(true);
        return(false);
}
msgconfseq="<?php  echo $mess_seq_presc;?>"
function confseq() {
        if ( confirm(msgconfseq) )
                return(true);
        return(false);
}

</SCRIPT>
<SCRIPT LANGUAGE="JavaScript">
function popup(f,nom, w, h) {
   window.open(f, nom, 'resizable,screenX=0,screenY=0,scrollbars=yes,menubar=yes,width=' + w + ',height=' + h);
}

function tinypopup(f,nom, w, h) {
   window.open(f, nom, 'resizable,screenX=0,screenY=0,scrollbars=no,menubar=no,width=' + w + ',height=' + h);
}
</script>
<SCRIPT LANGUAGE="JavaScript">
function appel_w(sel_val) {
var fset=sel_val.substring(0,2);
var f2=sel_val;
var url1 = ""+f2+"";
if ( fset == "tr" ) parent.main.location=url1
}
</script>
</HEAD>
<?php

if (strstr($_SERVER["HTTP_USER_AGENT"],"MSIE"))
  $i_body ="<BODY marginwidth='0' marginheight='0' leftmargin='0'";
else
  $i_body ="<BODY marginwidth='0' marginheight='0' leftmargin='12'";
if ($typ_user== "APPRENANT")
   $i_body .= "topmargin='40'";
else
   $i_body .= "topmargin='20'";
if (isset($annuaire) && $annuaire == 1)
  $i_body .= " bgcolor = \"#002D44\"";
$i_body .= ">";
echo $i_body;
if (!strstr($_SERVER["HTTP_USER_AGENT"],"MSIE"))
  echo "&nbsp;<P>";
$bouton_gauche = "<TABLE cellpadding='0' cellspacing='0' border=0><TR><TD>".
                 "<IMG SRC='$adresse_http/images/complement/cg.gif' border='0'></TD>".
                 "<TD background='$adresse_http/images/complement/milieu.gif' nowrap align='center'><DIV id='sequence'>&nbsp;";
$bouton_droite = "&nbsp;</DIV></TD><TD><IMG SRC='$adresse_http/images/complement/cd.gif' border='0'></TD><TR></TABLE>";
echo '<div id="overDiv" style="position:absolute; visibility:hidden; z-index:1000;"></div>
      <SCRIPT LANGUAGE="JavaScript" SRC="'.$adresse_http.'/overlib.js"><!-- overLIB (c) Erik Bosrup --></SCRIPT>';