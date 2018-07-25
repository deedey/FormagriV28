<?php
if (!isset($_SESSION)) session_start();
include ("include/UrlParam2PhpVar.inc.php");
if (!isset($_SESSION['id_user']) || $_SESSION['id_user'] == "")
{
  exit();
}
header("Content-type  : text/plain");
header("Cache-Control: no-cache, max-age=0");
require "admin.inc.php";
require 'fonction.inc.php';
require "lang$lg.inc.php";
require 'fonction_html.inc.php';
dbConnect();
$verif2 =  mysql_query("SELECT * from causer where causer_dest_no=".$_SESSION['id_user']);
$result2 = mysql_num_rows($verif2);
$verif = mysql_query("SELECT * from chatter where login='$login'");
$result = mysql_num_rows($verif);
if ($result > 0 || $result2 > 0)
{

 if ($result > 0)
 {
   $id_chat = mysql_result($verif,0,"id");
   $appele = mysql_result($verif,0,"login");
   $appelant = mysql_result($verif,0,"appelant");
   if ($appele == $login && $login != "")
   {
      $prenom_user=GetDataField ($connect,"select util_prenom_lb from utilisateur where util_cdn = $id_user","util_prenom_lb");
      $type=GetDataField ($connect,"select util_typutil_lb from utilisateur where util_login_lb = '$appelant'","util_typutil_lb");
      $nom=GetDataField ($connect,"select util_nom_lb from utilisateur where util_login_lb = '$appelant'","util_nom_lb");
      $prenom=GetDataField ($connect,"select util_prenom_lb from utilisateur where util_login_lb = '$appelant'","util_prenom_lb");
      $query=mysql_query("DELETE FROM chatter where login ='$login'");
      $verif_salle = mysql_query("SELECT * from p4_salle where user='$login'");
      $result_salle = mysql_num_rows($verif_salle);
      if ($result_salle > 0)
      {
         $message = "$nom $prenom :  $type $mess_trace_dial";
         $retour = $message;
      }
      else
      {
         if (!isset($ok)) $ok = '';
         $message = "<CENTER><SMALL><B>$nom $prenom $type </B>$mess_trace_dial</SMALL></CENTER>";
         $retour = "chat/index.php?ok=$ok&message=".urlencode($message)."&aller=1&user=$login&pass=$prenom_user";
      }
      echo utf2Charset($retour,$charset);
      exit;
   }
 }
 if($result2 > 0)
 {
   $id_message = mysql_result($verif2,0,"causer_cdn");
   $appelant = mysql_result($verif2,0,"causer_origin_no");
   $content_msg = mysql_result($verif2,0,"causer_mess_cmt");
   $nom=GetDataField ($connect,"select util_nom_lb from utilisateur where util_cdn = '$appelant'","util_nom_lb");
   $prenom=GetDataField ($connect,"select util_prenom_lb from utilisateur where util_cdn = '$appelant'","util_prenom_lb");
   $type=GetDataField ($connect,"select util_typutil_lb from utilisateur where util_cdn = '$appelant'","util_typutil_lb");
   $query=mysql_query("DELETE FROM causer where causer_dest_no ='".$_SESSION['id_user']."'");
   if (strlen($content_msg) > 60)
      $content_msg = substr($content_msg,0,60);
   $retour = "<div class='EnteteMsg'>Message de $nom $prenom : ".strtolower($type)." </div>".
             "<div id='cttMsg' class='cttMsg' style='background-color:#ffffff'>$content_msg</div>";
   $lien_mess = "message_instant.php?num=$appelant";
   $ajout2 = "<div id='boutAct' style='float:top;margin-top:10px;'>".
             "<div id='bout_msg' class='bout_msg' style='float:left;'".
             " onClick=\"javascript:window.open('$monURI/$lien_mess','','scrollbars=no,resizable=yes,width=550,height=120,left=300,top=300');".
             " parent.frames['logo'].document.getElementById('msgInst').innerHTML='';".
             " parent.frames['logo'].document.getElementById('msgInst').className='close';\" > ".
             " Repondre</div>";
   $ajout2 .= "<div id='bout_msg' class='bout_msg' style='float:right;' onClick=\"javascript:".
             " parent.frames['logo'].document.getElementById('msgInst').innerHTML='';".
             " parent.frames['logo'].document.getElementById('msgInst').className='close';\" >".
             "Fermer</div></div>";
   $retour .= $ajout2;
   echo utf2Charset($retour,$charset);
 }
}
else
   echo '';
?>