<?php
if (!isset($_SESSION)) session_start();
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
<HTML>
<HEAD>
<META HTTP-EQUIV="Content-Type" content="text/html; charset=<?php echo $charset;?>">
<META HTTP-EQUIV="Content-Language" CONTENT="<?php echo $code_langage ;?>">
<META HTTP-EQUIV="Expires" CONTENT="Fri, Jan 01 1900 00:00:00 GMT">
<META NAME="ROBOTS" CONTENT="No Follow">
<META HTTP-EQUIV="Cache-Control" CONTENT="no-cache, must-revalidate">
<META HTTP-EQUIV="Pragma" CONTENT="no-cache">
<link rel="shortcut icon" href="../images/logcnerta.ico" type="image/x-icon" />
<?php
$ip = @gethostbyname('www.google.fr');
if(isset($ip) && strlen($ip) > 7)
   echo "\n<script type='text/javascript'>
       var _gaq=_gaq||[];var pluginUrl = '//www.google-analytics.com/plugins/ga/inpage_linkid.js';_gaq.push(['_require', 'inpage_linkid', pluginUrl]);_gaq.push(['_setAccount','UA-42020054-1']);_gaq.push(['_trackPageview']);_gaq.push(['_setCustomVar', 1, window.location.host, window.location.host, 1]);_gaq.push(['_setDomainName','none']);(function(){var ga=document.createElement('script');ga.type='text/javascript';ga.async=true;ga.src=('https:'==document.location.protocol?'https://ssl':'http://www')+'.google-analytics.com/ga.js';var s=document.getElementsByTagName('script')[0];s.parentNode.insertBefore(ga,s)})();
   </script>";
?>
<TITLE>Formagri :: <?php echo str_replace('.educagri.fr','',str_replace('cfppa-','',str_replace('ef-','',$_SERVER['SERVER_NAME']))).
                         ' :: '.$RoleUser.' :: '.$currentUser;?></TITLE>
<STYLE>
#menu a:hover {
background-repeat:no-repeat;
background-position:1% 50%;
color:#D45211;
}

BODY { font-family: arial; font-size: 12px; color: #333333 }
TD   { font-family: arial; font-size: 12px; color: #333333 }
TH   { font-family: arial; font-size: 12px; color: #333333 }
A         {font-family:arial;font-size:12px;color:#24677A;text-decoration:none}
A:link    {font-family:arial;font-size:12px;color:#24677A;font-weight:bold}
A:visited {font-family:arial;font-size:12px;color:#24677A;font-weight:bold}
A:hover   {font-family:arial;font-size:12px;color:#D45211;font-weight:bold}
A.off     {font-family:arial;font-size:12px;color:#24677A;font-weight:bold}

#titre A:link{background-repeat:no-repeat;background-position:1% 50%;color:#3BACC4;}
#titre A:visited{background-repeat:no-repeat;background-position:1% 50%;color:#3BACC4;}
#titre A:hover {background-repeat:no-repeat;background-position:1% 50%;color:#D45211;}

#menu A:link{background-repeat:no-repeat;background-position:1% 50%;color:#002D44;}
#menu A:visited{background-repeat:no-repeat;background-position:1% 50%;color:#002D44;}
#menu A:hover {background-repeat:no-repeat;background-position:1% 50%;color:#D45211;}

#sequence A:link{background-repeat:no-repeat;background-position:1% 50%;color:#24677A;}
#sequence A:visited{background-repeat:no-repeat;background-position:1% 50%;color:#24677A;}
#sequence A:hover {background-repeat:no-repeat;background-position:1% 50%;color:#D45211;}

.clq {LEFT: 0px; VISIBILITY: hidden; POSITION: absolute; TOP: 0px}
.mar { font-family: arial;font-size:9px;;color:#800000;}
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
<?php if ($typ_user == 'APPRENANT' && $nombre_groupes > 1){
?>
<!--
#slidemenubar, #slidemenubar2{
position:absolute;
border:1.5px solid black;
line-height:20px;
}
-->
<?php
}?>
</STYLE>

<SCRIPT  LANGUAGE="JavaScript1.2" SRC="../calendrier_<?php echo $lg;?>.js"></SCRIPT>
<script language="javascript" type="text/javascript" src="<?php echo $adresse_http;?>/tinymce/jscripts/tiny_mce/tiny_mce.js"></script>
<script language="javascript" type="text/javascript">
tinyMCE.init({
        mode : "textareas",
        force_br_newlines : true,
        theme_advanced_toolbar_align : "left",
        theme_advanced_toolbar_location : "top",
        theme_advanced_buttons1 : "bold,italic,underline,strikethrough,separator,"
        + "justifyleft,justifycenter,justifyright,justifyfull,formatselect,"
        + "bullist,numlist,outdent,indent",
        theme_advanced_buttons2 : "link,unlink,anchor,image,separator,"
        +"undo,redo,cleanup,code,separator,sub,sup,charmap",
        theme_advanced_buttons3 : "",
        language : "fr"
});
</script>

<SCRIPT LANGUAGE="JavaScript">
function appel_w(sel_val) {
  var fset=sel_val.substring(0,2);
  var f2=sel_val;
  var url1 = ""+f2+"";
  if ( fset == "tr" )
   parent.main.location=url1
}
function appel_wpop(sel_val) {
  var fset=sel_val.substring(0,2);
  var f2=sel_val;
  var url1 = ""+f2+"";
  if ( fset == "tr" )
   window.open('','','width=680,height=380,resizable=yes,status=no').location=url1
}
</script>
<script language="javascript" src="../fonction.js"></script>
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
</SCRIPT>
<SCRIPT LANGUAGE="JavaScript">
function popup(f,nom, w, h) {
   window.open(f, nom, 'resizable,screenX=0,screenY=0,scrollbars=yes,menubar=yes,width=' + w + ',height=' + h);
}

function tinypopup(f,nom, w, h) {
   window.open(f, nom, 'resizable,screenX=0,screenY=0,scrollbars=no,menubar=no,width=' + w + ',height=' + h);
}
</script>
<SCRIPT LANGUAGE="JavaScript" TYPE="text/javascript"><!--
msgconfm="<?php echo $mess_admin_valid_modif;?>"
function confm() {
        if ( confirm(msgconfm) )
                return(true);
        return(false);
}
//--></SCRIPT>
<SCRIPT LANGUAGE="JavaScript" TYPE="text/javascript"><!--
msgconf="<?php echo $mess_admin_valid_supp;?>"
function conf() {
        if ( confirm(msgconf) )
                return(true);
        return(false);
}
//--></SCRIPT>
<SCRIPT LANGUAGE="JavaScript" TYPE="text/javascript"><!--
msgconfv="<?php echo $mess_gen_val_sais;?>"
function confv() {
        if ( confirm(msgconfv) )
                return(true);
        return(false);
}
//--></SCRIPT>
<TITLE>Formagri</TITLE>
</HEAD>
<?php
$nom_user = GetDataField ($connect,"select util_nom_lb from utilisateur where util_cdn='$id_user'","util_nom_lb");
$prenom_user = GetDataField ($connect,"select util_prenom_lb from utilisateur where util_cdn='$id_user'","util_prenom_lb");
$image = GetDataField($connect,"select param_etat_lb from param_foad where param_cdn=1","param_etat_lb");
$bkg = GetDataField($connect,"select param_etat_lb from param_foad where param_typ_lb = 'bkg'","param_etat_lb");
if (strstr(getenv("HTTP_USER_AGENT"),"MSIE"))
  $typ_agent ='msie';
$i_body= "<BODY bgcolor=\"$bkg\" marginwidth='0' marginheight='0' leftmargin='0'";
if ($complement != '1' && $typ_agent == 'msie')
   $i_body .= " topmargin='20'";
else
   $i_body .= " topmargin='0'";
if ($typ_user == 'APPRENANT' && $nombre_groupes >1 and $nerienfaire == 1){
  $i_body .= "onMouseover=\"if ((document.all||document.getElementById)&&window.parent.pull) window.parent.pull()\" onMouseout=\"if ((document.all||document.getElementById)&&window.parent.pull) window.parent.draw()\"";
}
echo $i_body.">";
$bouton_gauche = "<TABLE cellpadding='0' cellspacing='0' border=0><TR><TD><IMG SRC='images/cg.gif' border='0'></TD><TD background='images/milieu.gif' nowrap align='center'><DIV id='sequence'>&nbsp;";
$bouton_droite = "&nbsp;</DIV></TD><TD><IMG SRC='images/cd.gif' border='0'></TD><TR></TABLE>";
if ($complement != '1' && $typ_agent != 'msie')
   echo "&nbsp;<P>";
?>
<?php if ($typ_user == 'APPRENANT' && $nombre_groupes > 1 and $nerienfaire == 1){?>
<script language="JavaScript1.2">

/***********************************************
* Fold-out external menu- © Dynamic Drive (www.dynamicdrive.com)
* This notice must stay intact for use
* Visit http://www.dynamicdrive.com/ for full source code
***********************************************/

var slidemenu_height=315 //specify height of menu
var slidemenu_width=160 //specify width of menu (in pixels)
var slidemenu_reveal=12 //specify amount that menu should protrude initially
var slidemenu_top=170   //specify vertical offset of menu on page
var slidemenu_url="change_groupe.php" //specify path to menu file

var ns4=document.layers?1:0
var ie4=document.all&&navigator.userAgent.indexOf("Opera")==-1
var ns6=document.getElementById&&!document.all?1:0

if (ie4||ns6)
document.write('<iframe id="slidemenubar2" style="left:'+((slidemenu_width-slidemenu_reveal)*-1)+'; top:'+slidemenu_top+'; width:'+slidemenu_width+'; height:'+slidemenu_height+'" src="'+slidemenu_url+'"></iframe>')
else if (ns4){
document.write('<style>\n#slidemenubar{\nwidth:'+slidemenu_width+';}\n<\/style>\n')
document.write('<layer id="slidemenubar" left=0 top='+slidemenu_top+' width='+slidemenu_width+' height='+slidemenu_height+' onMouseover="pull()" onMouseout="draw()" src="'+slidemenu_url+'" visibility=hide></layer>')
}
function regenerate(){
window.location.reload()
}
function regenerate2(){
if (ns4){
document.slidemenubar.left=((slidemenu_width-slidemenu_reveal)*-1)
document.slidemenubar.visibility="show"
setTimeout("window.onresize=regenerate",400)
}
}

window.onload=regenerate2

rightboundary=0
leftboundary=(slidemenu_width-slidemenu_reveal)*-1

if (ie4||ns6){
document.write('</div>')
themenu=(ns6)? document.getElementById("slidemenubar2").style : document.all.slidemenubar2.style
}
else if (ns4){
document.write('</layer>')
themenu=document.layers.slidemenubar
}

function pull(){
if (window.drawit)
clearInterval(drawit)
pullit=setInterval("pullengine()",10)
}
function draw(){
clearInterval(pullit)
drawit=setInterval("drawengine()",10)
}
function pullengine(){
if ((ie4||ns6)&&parseInt(themenu.left)<rightboundary)
themenu.left=parseInt(themenu.left)+10
else if(ns4&&themenu.left<rightboundary)
themenu.left+=10
else if (window.pullit){
themenu.left=0
clearInterval(pullit)
}
}

function drawengine(){
if ((ie4||ns6)&&parseInt(themenu.left)>leftboundary)
themenu.left=parseInt(themenu.left)-10
else if(ns4&&themenu.left>leftboundary)
themenu.left-=10
else if (window.drawit){
themenu.left=leftboundary
clearInterval(drawit)
}
}

</script>
<?php
}
$venue=getenv("SCRIPT_NAME")."?".getenv("QUERY_STRING");

if (strstr($venue,'agenda.php?tuteur='))

   $venue=1;

else

   $venue=0;

?>

  <div id="overDiv" style="position:absolute; visibility:hidden;z-index:1000;"></div>

  <SCRIPT LANGUAGE="JavaScript" SRC="../overlib.js"><!-- overLIB (c) Erik Bosrup --></SCRIPT>


