<?php
if (!isset($_SESSION)) session_start();
include ("include/UrlParam2PhpVar.inc.php");
if (!isset($_SESSION['id_user']) || $_SESSION['id_user'] == "")
{
  exit();
}
require "admin.inc.php";
require 'fonction.inc.php';
require "lang$lg.inc.php";
dbConnect();
//include ("include/varGlobals.inc.php");
if (isset($mail_sous_grp) && $mail_sous_grp == 1)
{
   if (isset($envoi) && count($envoi) == 1)
   {
            $lien = "mail.php?contacter=1&num=".key($envoi);
            echo "<script language=\"JavaScript\">";
            echo "document.location.replace(\"trace.php?link=".urlencode($lien)."\")";
            echo "</script>";
   }
   else
   {
      $send_to = "";
      $list_envoi = explode(",",$liste_envoi);
      $nb_envoi = count($list_envoi);
      $i=0;$passage = 0;
      while ($i < $nb_envoi)
      {
         $envoyer = explode("|",$list_envoi[$i]);
         $adresse = $envoyer[0];
         $num = $envoyer[1];
         if (isset($envoi[$num]) && $envoi[$num] == 'on' && $i < $nb_envoi-1)
         {
           $passage++;
           $send_to .= $num.",";
         }
         elseif ($envoi[$num] == 'on' && $i == $nb_envoi-1)
             $send_to .= $num;
       $i++;
      }
      if (isset($an) && $an == 1)
         $vers = $mess_envoi_mail_annu;
      else
         $vers = "$mess_mail_avert $mess_mail_cert_app";
      if ($send_to != "" && strstr($send_to,','))
        $lien = "mail.php?send_to=$send_to&sous_grp=1&message_mail=$vers";
      elseif ($passage == 1)
        $lien = "mail.php?contacter=1&num=$send_to";
      if (strstr($send_to,',') || $passage == 1)
      {

         echo "<script language=\"JavaScript\">";
            echo "document.location.replace(\"trace.php?link=".urlencode($lien)."\")";
         echo "</script>";
      }
   }
}
if  (isset($origine) && $origine == "annuaire")
{
  echo "<script language=\"JavaScript\">";
    echo "setTimeout(\"Quit()\",1500);
        function Quit() {
          self.opener=null;self.close();return false;
        }
    </SCRIPT>";
}
else
{
     print("<SCRIPT language=javascript>");
        print("document.location.replace(\"javascript:history.back();\")");
     print("</SCRIPT>");
}
?>
