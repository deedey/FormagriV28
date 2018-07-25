<?php
session_start();
if (!isset($_SESSION['IDUSER']) || $_SESSION['IDUSER'] == "")
{
  exit();
}
require ("../admin.inc.php");
require ("../fonction.inc.php");
require ("../fonction_html.inc.php");
require ("../accueil/fonction_accueil.inc.php");
dbConnect();
include ("../include/varGlobals.inc.php");
$Response=array();
$result=array();
$resultM=array();
$aujourdhui = date("d/m/Y H:i:s" ,time());
$madate_compare = date("Y-m-d");
$nbj_date_compare = mysql_query ("select TO_DAYS('$madate_compare')");
$nbj_date_comp = mysql_result ($nbj_date_compare,0);
$heure_debut = substr($aujourdhui,11);
$req_msg = mysql_query("SELECT * from message where
                      ((msg_dhdeb_dt < '$madate_compare' OR msg_dhdeb_dt = '$madate_compare') AND
                      (msg_dhfin_dt = '$madate_compare' OR msg_dhfin_dt > '$madate_compare')) AND
                      ((msg_apprenant_no = ".$_SESSION['IDUSER']." AND msg_groupe_no = ".$_GET['id_grp'].") OR
                      (msg_apprenant_no = 0 AND msg_groupe_no = ".$_GET['id_grp']."))");
$nb_msg = mysql_num_rows($req_msg);

if ($nb_msg > 0)
{
       while ($row = mysql_fetch_object($req_msg))
       {
            $result[] = $row;
       }
       for ($i=0;$i<$nb_msg;$i++)
       {
           $couleur = (($i/2) > floor($i/2)) ? 'background-color:#fff' : 'background-color:#eee';
           $nom_auteur=GetDataField ($connect,"select util_nom_lb from utilisateur WHERE util_cdn = '".$result[$i]->msg_auteur_no."'","util_nom_lb");
           $prenom_auteur=GetDataField ($connect,"select util_prenom_lb from utilisateur WHERE util_cdn = '".$result[$i]->msg_auteur_no."'","util_prenom_lb");
           $majuscule = $prenom_auteur." ".$nom_auteur;
           $type = ($result[$i]->msg_apprenant_no > 0) ? 'Personnelle' : 'A l\'attention de tous les apprenants de la formation';
           $content .= "<div id='msg".$i."'style='clear:both;float:left;max-width:610px;".$couleur.
                  ";font-size:12px;width:95%;padding:4px;margin-top:6px;color:#000; border:2px solid #999;'>".
                  "<div style='margin:4px;padding:2px;'><span style='font-weight:bold;'>Date de fin : </span>".reverse_date($result[$i]->msg_dhfin_dt,'-','-')."</div>".
                  "<div style='margin:4px;padding:2px;'><span style='font-weight:bold;'>Auteur : </span>".$majuscule."</div>".
                  "<div style='margin:4px;padding:2px;'><span style='font-weight:bold;'>Destination : </span>".$type."</div>".
                  "<div style='margin:4px;padding:2px;background-color:#ddd; border:1px solid #bbb;max-width:600px;'>".
                  "<span style='font-weight:bold;'>Message : </span>".html_entity_decode($result[$i]->msg_contenu_cmt,ENT_QUOTES,'ISO-8859-1')."</div>".
                  "</div>";
      }
      echo utf2Charset(stripslashes($content),"iso-8859-1");
}
?>
