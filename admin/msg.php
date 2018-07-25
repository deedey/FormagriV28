<?php
/*
 * Created on 15 janv. 2009 by Dey Bendifallah
 * Cnerta/Eduter/Enesad/
 */
if (!isset($_SESSION)) session_start();
if (!isset($_SESSION['id_user']) || $_SESSION['id_user'] == "")
{
  exit();
}
header("Content-type  : text/plain");
header("Cache-Control: no-cache, max-age=0");
include ("../include/UrlParam2PhpVar.inc.php");
require '../fonction.inc.php';
require '../admin.inc.php';
require "../lang$lg.inc.php";
require "../fonction_html.inc.php";
require "../langues/messagerie.inc.php";
dbConnect();
if (isset($_GET['restaurer']) && $_GET['restaurer'] == 1)
{
   $requete = mysql_query("update messagerie set supprime = 0 where mess_cdn = $numero");
   echo  utf2Charset("ok",$charset);
   exit;
}
if (isset($_GET['dejalu']) && $_GET['dejalu'] == 1)
{
  if (!isset($_GET['recuperation']))
  {
   $requete = mysql_query("update messagerie set lu = 0,supprime = 0 where mess_cdn = $numero");
   if (!isset($_GET['notif']))
      echo  utf2Charset($msg_mess_lu,$charset);
   else
      echo "";
   exit;
   
  }
  elseif(isset($_GET['recuperation']) && $_GET['recuperation'] == 1)
  {
   $lien = "admin/msg.php?numero=$numero&dl=dejalu$i&renonlu=1&ret=$ret&complement=$complement&i=$i";
   $envoi = "<A HREF=\"javascript:appelle_ajax('$lien'); ".
          "var mon_content=document.getElementById('mien');mon_content.style.visibility='hidden'; ".
          "var mon_div=document.getElementById('dejalu$i');mon_div.innerHTML=''; ".
          "addContent_msg('$lien');\" ".
          "onMouseOver=\"overlib('<TABLE border=0 cellspacing=2 cellpadding=6><TR>".
          "<TD align=left><font size=2>$mess_mail_renonlu</font></TD></TR></TABLE>'".
          ",ol_hpos,RIGHT,ABOVE,WIDTH,'180')\" ".
          "onMouseOut=\"nd();\"><IMG SRC='images/messagerie/icoGcheckb.gif' border='0'></A>";
  }
  echo utf2Charset($envoi,$charset);
  exit();
}
if (isset($_GET['renonlu']) && $_GET['renonlu'] == 1)
{
  if (!isset($_GET['recuperation']))
  {
   $requete = mysql_query("update messagerie set lu = 1,supprime = 0 where mess_cdn = $numero");
   echo  utf2Charset($msg_mess_nonlu,$charset);
   exit;
   
  }
  elseif(isset($_GET['recuperation']) && $_GET['recuperation'] == 1)
  {
   $lien = "admin/msg.php?numero=$numero&dl=dejalu$i&dejalu=1&ret=$ret&complement=$complement&i=$i";
   $envoi = "<A HREF=\"javascript:appelle_ajax('$lien'); ".
          "var mon_content=document.getElementById('mien');mon_content.style.visibility='hidden'; ".
          "var mon_div=document.getElementById('dejalu$i');mon_div.innerHTML=''; ".
          "addContent_msg('$lien');\" ".
          "onMouseOver=\"overlib('<TABLE border=0 cellspacing=2 cellpadding=6><TR>".
          "<TD align=left><font size=2>$mess_mail_djalu</font></TD></TR></TABLE>'".
          ",ol_hpos,RIGHT,ABOVE,WIDTH,'180')\" ".
          "onMouseOut=\"nd();\"><IMG SRC='images/messagerie/icoGcheck.gif' border='0'></A>";
  }
  echo utf2Charset($envoi,$charset);
  exit();
}
?>
