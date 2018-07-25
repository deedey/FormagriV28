<?php
if (!isset($lg) || $lg == ""){
  include ("$adresse_http/deconnexion-fr.txt");
  exit();
}
if (isset($lg)){
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
}
if (!isset($arrive) || $arrive == ""){
   unset($_SESSION['chaine_act']);
   unset($_SESSION['forum_act']);
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
$bkg = GetDataField($connect,"select param_etat_lb from param_foad where param_typ_lb = 'bkg'","param_etat_lb");
?>
<HTML>
<HEAD>
<META HTTP-EQUIV="Content-Type" content="text/html; charset=<?php  echo $charset;?>">
<META HTTP-EQUIV="Content-Language" CONTENT="<?php  echo $code_langage ;?>">
<META HTTP-EQUIV="Expires" CONTENT="Fri, Jan 01 1900 00:00:00 GMT">
<META NAME="ROBOTS" CONTENT="No Follow">
<META HTTP-EQUIV="Cache-Control" CONTENT="no-cache, must-revalidate">
<META HTTP-EQUIV="Pragma" CONTENT="no-cache">
<?php
echo '<TITLE>Formagri :: '.str_replace('.educagri.fr','',str_replace('cfppa-','',str_replace('ef-','',$_SERVER['SERVER_NAME']))).
                         ' :: '.$RoleUser.' :: '.$currentUser.'</TITLE>';
?>
<link rel="stylesheet" type="text/css" href="style_admin.css" />
<link rel="shortcut icon" href="../images/icone.ico" type="image/x-icon" />
<SCRIPT LANGUAGE="JavaScript" SRC="../OutilsJs/lib/ajax/ajax_cms.js"></SCRIPT>
<?php
if ($sock = @fsockopen('www.google.fr', 80, $number01, $error, 5))
   echo "\n<script type='text/javascript'>
                    var _gaq=_gaq||[];var pluginUrl = '//www.google-analytics.com/plugins/ga/inpage_linkid.js';_gaq.push(['_require', 'inpage_linkid', pluginUrl]);_gaq.push(['_setAccount','UA-42020054-1']);_gaq.push(['_trackPageview']);_gaq.push(['_setCustomVar', 1, window.location.host, window.location.host, 1]);_gaq.push(['_setDomainName','none']);(function(){var ga=document.createElement('script');ga.type='text/javascript';ga.async=true;ga.src=('https:'==document.location.protocol?'https://ssl':'http://www')+'.google-analytics.com/ga.js';var s=document.getElementsByTagName('script')[0];s.parentNode.insertBefore(ga,s)})();
   </script>";
?>
<STYLE>

.INPUT {
     color:#333333;
     background-color:#FFFFFF;
     font-size:12px;
     font-family:arial;
     border:1px solid #002D44;
}
.SELECT {
     color:#333333;
     background-color:#FFFFFF;
     font-size:12px;
     font-family:arial;
     border:1px solid #002D44;
}
.TEXTAREA {
         background-color: #FFFFFF ;
         font-family: Arial ;
         font-size: 12px;
}

/*BODY { font-family: arial; font-size: 12px; color: #333333 }*/
TD   { font-family: arial; font-size: 12px; color: #333333 }
TH   { font-family: arial; font-size: 12px; color: #333333 }
A         {font-family:arial;font-size:12px;color:#24677A;text-decoration:none}
A:link    {font-family:arial;font-size:12px;color:#24677A;font-weight:bold}
A:visited {font-family:arial;font-size:12px;color:#24677A;font-weight:bold}
A:hover   {font-family:arial;font-size:12px;color:#D45211;font-weight:bold}

#titre A:link{background-repeat:no-repeat;background-position:1% 50%;color:#3BACC4;font-family:arial;font-size:11px;}
#titre A:visited{background-repeat:no-repeat;background-position:1% 50%;color:#3BACC4;font-family:arial;font-size:11px;}
#titre A:hover {background-repeat:no-repeat;background-position:1% 50%;color:#D45211;font-family:arial;font-size:11px;}

#menu A:link{background-repeat:no-repeat;background-position:1% 50%;color:#002D44;}
#menu A:visited{background-repeat:no-repeat;background-position:1% 50%;color:#002D44;}
#menu A:hover {background-repeat:no-repeat;background-position:1% 50%;color:#D45211;}

#mp A:link{background-repeat:no-repeat;background-position:1% 50%;color:#FFFFFF;}
#mp A:visited{background-repeat:no-repeat;background-position:1% 50%;color:#FFFFFF;}
#mp A:hover {background-repeat:no-repeat;background-position:1% 50%;color:#FFFFFF;}

#mpwo A:link{background-repeat:no-repeat;background-position:1% 50%;color:#FFFFFF;}
#mpwo A:visited{background-repeat:no-repeat;background-position:1% 50%;color:#FFFFFF;}
#mpwo A:hover {background-repeat:no-repeat;background-position:1% 50%;color:#D45211;}

#sequence A:link{background-repeat:no-repeat;background-position:1% 50%;color:#24677A;}
#sequence A:visited{background-repeat:no-repeat;background-position:1% 50%;color:#24677A;}
#sequence A:hover {background-repeat:no-repeat;background-position:1% 50%;color:#D45211;}

#seqinv A:link{background-repeat:no-repeat;background-position:1% 50%;color:#D45211;}
#seqinv A:visited{background-repeat:no-repeat;background-position:1% 50%;color:#D45211;}
#seqinv A:hover {background-repeat:no-repeat;background-position:1% 50%;color:#648992;}

#seqgris A:link{background-repeat:no-repeat;background-position:1% 50%;color:#648992;}
#seqgris A:visited{background-repeat:no-repeat;background-position:1% 50%;color:#648992;}
#seqgris A:hover {background-repeat:no-repeat;background-position:1% 50%;color:#D45211;}

#parcours A:link{background-repeat:no-repeat;background-position:1% 50%;color:#002D44;}
#parcours A:visited{background-repeat:no-repeat;background-position:1% 50%;color:#002D44;}
#parcours A:hover {background-repeat:no-repeat;background-position:1% 50%;color:#FFFFFF;}

#parcseqtype A:link{background-repeat:no-repeat;background-position:1% 50%;color:red;}
#parcseqtype A:visited{background-repeat:no-repeat;background-position:1% 50%;color:red;}
#parcseqtype A:hover {background-repeat:no-repeat;background-position:1% 50%;color:#D45211;}

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
.bouton1{text-align:center;padding:2px;}

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

a.bouton3:link{height:26px;text-decoration:none;color:#2B677A;text-align:center;font-weight:bold;
     background-image:url(images/messagerie/onglCblanc.gif);padding:5px;}
a.bouton3:visited{height:26px;text-decoration:none;color:#2B677A;text-align:center;font-weight:bold;
     background-image:url(images/messagerie/onglCblanc.gif);padding:5px;}
a.bouton3:hover{height:26px;text-decoration:none;color:#D45211;text-align:center;font-weight:bold;
     background-image:url(images/messagerie/onglCblanc.gif);padding:5px;}
.bouton3{text-align:center;padding:4px;}

.clq {LEFT: 0px; VISIBILITY: hidden; POSITION: absolute; TOP: 0px}
.mar { font-family: arial;font-size:9px;;color:#800000 }
.small {font-family:arial;color:navy;font-size:11px;}
.admin {font-family:arial;color:#9999FF;font-size:13px}
.texte {font-family:arial;color:navy;font-size:13px}
.Softricks_Calendar {
        position: absolute;
        visibility: visible;
        top: 200;
        left: 10;
        height: 250;
        width: 260;
}
<?php if ($typ_user == 'APPRENANT' && $nombre_groupes > 1){?>
<!--
#slidemenubar, #slidemenubar2{
position:absolute;
border:1.5px solid black;
line-height:20px;
}
-->
<?php }?>
</STYLE>
<SCRIPT  LANGUAGE="JavaScript1.2" SRC="../calendrier_<?php echo $lg;?>.js"></SCRIPT>
<SCRIPT LANGUAGE="JavaScript">
function popupload(cible,nom,lg,ht) {
//Javascript:popupload('telecharger.pgi?cmd=frame','telecharger','470','280');
  var win = window.open(cible, nom, 'width='+lg+',height='+ht+',resizable=yes,scrollbars=yes,status=yes,menubar=no,toolbar=no,location=no,directories=no,closed=no,opener=no');
}
function appel_w(sel_val) {

  var fset=sel_val.substring(0,2);
  var f2=sel_val;
  var url1 = ""+f2+"";
  if ( fset == "tr" )
   parent.main.location=url1
}
function appel_simple(ele) {
   appelle_ajax(ele);
}
function appelle_ajax(ele) {
   //alert ('element = '+ele);
   sendData('',ele,'post');
}
function appel_wpop(sel_val) {
  var fset=sel_val.substring(0,2);
  var f2=sel_val;
  var url1 = ""+f2+"";
  if ( fset == "tr" )
   window.open('','','width=680,height=425,resizable=yes,status=no').location=url1
}
</script>
<script language="javascript" type="text/javascript" src="../tinymce/jscripts/tiny_mce/tiny_mce.js"></script>
<script language="javascript" type="text/javascript">
tinyMCE.init({
        mode : "textareas",
        entity_encoding : "raw",
        force_br_newlines : true,
        theme_advanced_toolbar_align : "left",
        theme_advanced_toolbar_location : "top",
        theme_advanced_buttons1 : "bold,italic,underline,separator,"
        + "justifyleft,justifycenter,justifyright,justifyfull,formatselect,"
        + "bullist,numlist",
        theme_advanced_buttons2 : "",
        theme_advanced_buttons3 : "",
        language : "fr"
});
</script>
<script language="javascript">
function TryCallFunction() {
        var sd = document.MForm.mydate1.value.split("\/");
        document.MForm.iday.value = sd[1];
        document.MForm.imonth.value = sd[0];
        document.MForm.iyear.value = sd[2];
}
function TryCallFunction1() {
        var sd = document.MForm.ma_date.value.split("\/");
        document.MForm.iday1.value = sd[1];
        document.MForm.imonth1.value = sd[0];
        document.MForm.iyear1.value = sd[2];
}

function Today() {
        var dd = new Date();
        return((dd.getMonth()+1) + "/" + dd.getDate() + "/" + dd.getFullYear());
}
function popup(f,nom, w, h) {
   window.open(f, nom, 'resizable,screenX=0,screenY=0,scrollbars=yes,menubar=yes,width=' + w + ',height=' + h);
}

function tinypopup(f,nom, w, h) {
   window.open(f, nom, 'resizable,screenX=0,screenY=0,scrollbars=no,menubar=no,width=' + w + ',height=' + h);
}
msgconfm="<?php  echo $mess_admin_valid_modif;?>"
function confm() {
        if ( confirm(msgconfm) )
                return(true);
        return(false);
}
msgconf1="<?php  echo $mess_admin_valid_supp;?>"
msgconf2="<?php  echo $mess_op_irrev;?>"
msgconf = msgconf1+'\n'+msgconf2;
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
msgconfdupli="<?php  echo $mess_conf_dupli;?>"
function confd() {
        if ( confirm(msgconfdupli) )
                return(true);
        return(false);
}
msgconfseq="<?php  echo $mess_seq_presc;?>"
function confseq() {
        if ( confirm(msgconfseq) )
                return(true);
        return(false);
}
function makevisible(cur,which){
   if(document.getElementById){
        if (which==0){
           if(document.all)
              cur.filters.alpha.opacity=100;
           else
              cur.style.setProperty("-moz-opacity", 1, "");
        }else{
           if(document.all)
              cur.filters.alpha.opacity=1;
           else
              cur.style.setProperty("-moz-opacity", .01, "");
        }
   }
}
//--></SCRIPT>
</HEAD>
<?php

$nom_user = GetDataField ($connect,"select util_nom_lb from utilisateur where util_cdn='$id_user'","util_nom_lb");
$prenom_user = GetDataField ($connect,"select util_prenom_lb from utilisateur where util_cdn='$id_user'","util_prenom_lb");
$image = GetDataField($connect,"select param_etat_lb from param_foad where param_cdn=1","param_etat_lb"); //
if (strstr(getenv("HTTP_USER_AGENT"),"MSIE"))
  $typ_agent = 'msie';
$i_body= "<BODY bgcolor=\"$bkg\" marginwidth='0' marginheight='0' leftmargin='0' topmargin='0'";
echo $i_body.">";
echo "<a name = 'sommet'></a>";
if (!isset($complement) || $complement != '1')
   echo "&nbsp;<P>";

$venue = getenv("SCRIPT_NAME")."?".getenv("QUERY_STRING");
if (strstr($venue,'agenda.php?tuteur='))
   $venue=1;
else
   $venue=0;
$bouton_gauche = "<TABLE cellpadding='0' cellspacing='0' border=0><TR><TD><IMG SRC=\"$adresse_http/images/complement/cg.gif\" border='0'></TD><TD background='$adresse_http/images/complement/milieu.gif' nowrap align='center'><DIV id='sequence'>&nbsp;";
$bouton_droite = "&nbsp;</DIV></TD><TD><IMG SRC=\"../images/complement/cd.gif\" border='0'></TD><TR></TABLE>";
?>
<div id="overDiv" style="position:absolute; visibility:hidden;z-index:1000;"></div>
<SCRIPT LANGUAGE="JavaScript" SRC="../overlib.js"><!-- overLIB (c) Erik Bosrup --></SCRIPT>