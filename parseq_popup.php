<?php
if (!isset($_SESSION)) session_start();
include ("include/UrlParam2PhpVar.inc.php");
if (!isset($_SESSION['id_user']) || $_SESSION['id_user'] == "")
{
  exit();
}
require 'admin.inc.php';
require 'fonction.inc.php';
require "lang$lg.inc.php";
dbConnect();
include 'style.inc.php';
   echo "<CENTER><TABLE bgColor='#298CA0' cellspacing='2' width='98%'><TR><TD width='100%'>";
   echo "<TABLE bgColor='#FFFFFF' cellspacing='1' width='100%'>";
   echo "<TR><TD background=\"images/fond_titre_table.jpg\" colspan='2' height='34' align='center' valign='center'><Font size='3' color='#FFFFFF'><B>$mpr_lisparc_form</B></FONT>";
   echo "</TD></TR><TR><TD><TABLE cellspacing='1' cellpadding='3' width='100%'>";
   $sql_id_ref = mysql_query ("select * from parcours where parcours_auteur_no = $id_util");
   $nbre_sql = mysql_num_rows($sql_id_ref);
   if ($nbre_sql != 0){
      echo "<TR height='30'><TD colspan=2 align='middle'><Font size='2'><B>$mess_menu_gest_seq_liste $mess_menu_gest_parc_liste_tts</B></font></TD></TR>";
      echo "<TR bgcolor='#2B677A' height='30'><TD width='40%'><FONT COLOR='#FFFFFF'><B>$mpr_nom_parc</B></FONT></TD>";
      echo "<TD><FONT COLOR='#FFFFFF'><B>$mess_desc</B></FONT></TD>";
      $nnn=0;
      $nom_referentiel = "";
      while ($nnn < $nbre_sql){
        $nne=$nnn+1;
        $id_nom = mysql_result($sql_id_ref,$nnn,"parcours_nom_lb");
        $id_desc = mysql_result($sql_id_ref,$nnn,"parcours_desc_cmt");
        if (($nnn/2) == ceil($nnn/2))
           echo "<TR height='30' bgcolor='#F4F4F4'>";
        else
           echo "<TR height='30' bgcolor='#FFFFFF'>";
        echo "<TD>$id_nom</TD>";
        echo "<TD>$id_desc</TD>";
        $nnn++;
      }
   }
   $sql_id_ref = mysql_query ("select * from sequence where seq_auteur_no = $id_util");
   $nbre_sql = mysql_num_rows($sql_id_ref);
   if ($nbre_sql != 0){
      echo "<TR><TD colspan=2 align='middle'>&nbsp;<P><Font size='2'><B>$mess_menu_gest_seq_liste $mess_menu_gest_seq_liste_tts</B></font></TD></TR>";
      echo "<TR bgcolor='#2B677A' height='30'><TD width='40%'><FONT COLOR='#FFFFFF'><B>$mpr_nom_seq</B></FONT></TD>";
      echo "<TD><FONT COLOR='#FFFFFF'><B>$mess_desc</B></FONT></TD>";
      $nnn=0;
      $nom_referentiel = "";
      while ($nnn < $nbre_sql){
        $nne=$nnn+1;
        $id_num = mysql_result($sql_id_ref,$nnn,"seq_cdn");
        $id_nom = mysql_result($sql_id_ref,$nnn,"seq_titre_lb");
        $id_desc = mysql_result($sql_id_ref,$nnn,"seq_desc_cmt");
        if (($nnn/2) == ceil($nnn/2))
           echo "<TR height='30' bgcolor='#F4F4F4'>";
        else
           echo "<TR height='30' bgcolor='#FFFFFF'>";
        echo "<TD>$id_nom</TD>";
        echo "<TD>$id_desc</TD>";
        $nnn++;
      }
   }
   echo "</TABLE></TD></TR></TABLE></TD></TR></TABLE>";
?>
</body></html>