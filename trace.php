<?php
if (!isset($_SESSION)) session_start();
if (!isset($_SESSION['id_user']) || $_SESSION['id_user'] == "") {?>

    <HTML>
      <HEAD>
      <STYLE>
        BODY { font-family: arial; font-size: 12px; color: #FFFFFF }
      </STYLE>
       <TITLE>Déconnexion</TITLE>
      <SCRIPT language=javascript>
         setTimeout("Quit()",1500);
         function Quit() {
          <?php
          if (!strstr($_SERVER['HTTP_USER_AGENT'],"MSIE"))
             echo "top.parent.opener=null;top.parent.close();return false;";
          else
             echo "top.opener=null;top.close();";
          ?>
          }
        </SCRIPT>
      </HEAD>
      <BODY bgcolor="#CEE6EC"><CENTER>&nbsp;<P><FONT Color='#333333' SIZE='3'><B>
            Vous vous êtes déconnecté de la plate-forme</B></FONT></CENTER>
      </BODY>
    </HTML>
   <?php
exit;
}
include ("include/UrlParam2PhpVar.inc.php");
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
require 'fonction.inc.php';
require 'admin.inc.php';
require "lang$lg.inc.php";
dbconnect();
$agent=$_SERVER["REQUEST_URI"];
if (strstr($agent,"details_parc.php") || strstr($agent,"gest_parc1.php") ||
    strstr($agent,"suivi.php") || strstr($agent,"gest_frm_") || strstr($agent,"fiche_suivi.php"))
{
  $couleur="#002D44";
  $bcolor = "#FFFFFF";
}
else
{
  $couleur="#FFFFFF";
  $bcolor = "#002D44";
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
<STYLE>
  BODY { font-family: arial; font-size: 12px; color: #003333; }
</STYLE>
</HEAD>
<?php
echo "<BODY  bgcolor=\"#FFFFFF\" marginwidth='0' marginheight='0' leftmargin='0'>";
if (strstr($link,"agenda2"))
  $renvoi = "agenda.php?tut=1";
elseif (strstr($link,"agenda1"))
  $renvoi = "agenda.php?apprenant=1";
else
  $renvoi = "annonce_form.php";
$nom_user=GetDataField ($connect,"select util_nom_lb from utilisateur where util_cdn = $id_user","util_nom_lb");
$prenom_user=GetDataField ($connect,"select util_prenom_lb from utilisateur where util_cdn = $id_user","util_prenom_lb");
$person = $nom_user;
if (!strstr($link,"gest_parc1.php"))
  echo "<CENTER><font size=2><B>$mess_trace_patient...</B></font></CENTER>";
$date=date("Y/m/d H:i:s" ,time());
$date_jour=date("Y/m/d");
$heure_jour= substr($date,11);
if (isset($lien))
   $lien = str_replace("|","&",$lien);
$verif_connex = mysql_query("SELECT login from log where login !='$login' AND date_debut = '$date_jour' AND date_fin ='0000-00-00'");
$result = mysql_num_rows($verif_connex);
if ($result != 0){
    $i = 0;$autre_connecte ="";
    while ($i < $result){
      $connecte = mysql_result($verif_connex,$i,"login");
      if ($i>0)
        $autre_connecte .=",";
      $autre_connecte .=$connecte;
    $i++;
    }
}
$lien = urldecode($link);

$max_numero = Donne_ID ($connect,"select max(trace_cdn) from trace");
$inserer_log = mysql_query ("insert into trace (trace_cdn,trace_login_lb,trace_lien_lb,trace_date_dt,trace_heure_dt) ".
                            "values ($max_numero,'$login',\"$link\",'$date_jour','$heure_jour')");
$lien = str_replace(" ","%20",$lien);
$link = addslashes($lien);
if (!strstr($lien,"parler=&") && strstr($lien,"parler=")){
  $partie= explode ("&",$lien);
  foreach ($partie as $item){
    if (strstr($item,"parler"))
       $correspondant=substr($item,7);
  }
  $id_chatter = Donne_ID ($connect,"select max(id) from chatter");
  $requete = mysql_query ("insert into chatter (id,parler,login,appelant) values ($id_chatter,1,'$correspondant','$login')");
}
$verif = mysql_query("SELECT * from chatter");
$result = mysql_num_rows($verif);
if ($result == 0){
   if (!strstr($link,"chat.php") || !strstr($link,"flash_chat")){
      echo "<script language=\"JavaScript\">";
      if (isset($activ) && $activ == 1)
      {
         $lien = urlencode($link);
         echo "document.location.replace(\"blanc.php?lien=$lien\")";
      }
      else
         echo "document.location.replace('$link')";
      echo "</script>";
   }else{
      print("<SCRIPT language=javascript>");
         print("window.open('$link','','width=550,height=400,resizable=yes,status=no')");
      print("</SCRIPT>");
      echo "<script language=\"JavaScript\">";
         echo "window.close();";
      echo "</script>";
   }
  exit();
}else{
  $nbr_appel = 0;
  while ($nbr_appel < $result){
    $id_chat = mysql_result($verif,$nbr_appel,"id");
    $parler = mysql_result($verif,$nbr_appel,"login");
    $appelant = mysql_result($verif,$nbr_appel,"appelant");
    if ($parler == $login && $login != "") {
      $type=GetDataField ($connect,"select util_typutil_lb from utilisateur where util_login_lb='$appelant'","util_typutil_lb");
      $nom=GetDataField ($connect,"select util_nom_lb from utilisateur where util_login_lb='$appelant'","util_nom_lb");
      $prenom=GetDataField ($connect,"select util_prenom_lb from utilisateur where util_login_lb='$appelant'","util_prenom_lb");
      $message = "<CENTER><SMALL><font%20color=blue><B>$nom%20$prenom%20<U>$type</U></B></font>%20<font%20color=green>".
                 "$mess_trace_dial</font></SMALL></CENTER>";
      $effacer = mysql_query("delete from chatter where id = $id_chat");
      $aller = urldecode($link);
//      $aller = str_replace("&","|",$aller);
//      $aller = str_replace(" ","%20",$aller);
      $lien="chat/index.php?message=$message&aller=1&user=$login&pass=$prenom_user";
      print("<SCRIPT language=javascript>");
         print("window.open('$lien','','width=550,height=400,resizable=yes,status=no')");
      print("</SCRIPT>");
      echo "<script language=\"JavaScript\">";
         echo "self.location.replace(\"$aller\")";
      echo "</script>";
      exit;
    }
    $nbr_appel++;
  }
}
if (!strstr($link,"chat.php") || !strstr($link,"flash_chat")){
    echo "<script language=\"JavaScript\">";
    if (isset($activ) && $activ == 1)
    {
      $lien = urlencode($link);
      echo "document.location.replace(\"blanc.php?lien=$lien\");";
    }
    else
      echo "document.location.replace(\"$link\");";
    echo "</script>";
}
else
{
    print("<SCRIPT language=javascript>");
    print("window.open('$link','','width=550,height=400,resizable=yes,status=no')");
    print("</SCRIPT>");
    echo "<script language=\"JavaScript\">";
       echo "window.close();";
    echo "</script>";
}
exit();
?>