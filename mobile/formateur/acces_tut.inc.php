<?php
if (!isset($_SESSION)) session_start();
if (!isset($_SESSION['IDUSER']) || $_SESSION['IDUSER'] == "")
{
  exit();
}
include ('../include/UrlParam2PhpVar.inc.php');
require ("../admin.inc.php");
require ("../fonction.inc.php");
require ("../fonction_html.inc.php");
require ("../langues/accueil.inc.php");
require ("../langfr.inc.php");
dbconnect();
include ("../../include/varGlobals.inc.php");
$dt_jour = date("Y-m-d");
$req_rdv = mysql_query("SELECT * from rendez_vous where
                       rdv_tuteur_no = $id_user and
                       rdv_date_dt = '$dt_jour'
                       ORDER BY rdv_apprenant_no,rdv_creneau_nb ASC");
$nomb_rdv = mysql_num_rows($req_rdv);
$date =date("d/m/Y H:i:s",time());
list($dt,$heure) =  explode (" ",$date);
list($hh,$mm,$ss) = explode(':',$heure);
if ($nomb_rdv > 0)
{
   require ('../agenda.inc.php');
   $rdv = 0;
   while ($rdv < $nomb_rdv){
           $creneau = mysql_result($req_rdv,$rdv,"rdv_creneau_nb");
           $cr = Horaire($creneau);
           list($valeur,$reste,$rest) = explode("h",$cr);
           if ($hh <= $valeur)
             $nb_final++;
          $rdv++;
   }
}
if ($nb_final > 0)
{
   $affiche_tut = "<center><table cellpadding='4' cellspacing='1' border=0 width='98%'><tr><td align='left' valign='bottom'>".
                  "<A HREF=\"../trace.php?link=".urlencode("agenda.php?tut=1")."\" class='sequence'".
                  bulle($mess_clk_rdv,"","CENTER","ABOVE",150).
                  $vous_avez." ".$nb_final." ".strtolower($mess_ag_rdv_tut).
                  "</a></td></tr></table></center>";
}
else
   $affiche_tut = $msgtutno;
echo utf2Charset($affiche_tut,$charset);
?>
