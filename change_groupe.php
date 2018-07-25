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
//include ("click_droit.txt");
dbConnect();
$req_grp = mysql_query("select utilgr_groupe_no from utilisateur_groupe WHERE utilgr_utilisateur_no = $id_user");
$nomb_grp = mysql_num_rows($req_grp);
if ($nomb_grp > 0)
{
      echo "<TABLE>";
      $i=0;
      while ($i < $nomb_grp){
         $id_grp = mysql_result($req_grp,$i,"utilgr_groupe_no");
         $nom_grp =GetDataField ($connect,"select grp_nom_lb from groupe WHERE grp_cdn = $id_grp","grp_nom_lb");
         $req_presc_grp = mysql_query("SELECT COUNT(*) from prescription_$id_grp WHERE presc_utilisateur_no = $id_user");
         $nb_presc_grp = mysql_result($req_presc_grp,0);
         if ($nb_presc_grp > 0)
         {
           $lien = $mon_url."?numero_groupe=$id_grp";
           $lien=urlencode($lien);
           echo  "<TR><TD><A HREF=\"trace.php?link=$lien\" target='_parent'>$nom_grp</A></TD></TR>";
         }else
           echo  "<TR><TD>$nom_grp</TD></TR>";
        $i++;
      }
      echo "</table>";
}else
      echo  "<CENTER><FONT SIZE=2>$mess_gp_app_nogr</FONT></CENTER>";
?>
