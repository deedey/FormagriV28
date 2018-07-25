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
$result[]=array('NbrAnnonce'=>$nb_msg);
if ($nb_msg > 0){
   while ($row = mysql_fetch_object($req_msg))
  {
      $result[]=$row;
  }
}
$Response['Messages'] = $result;
// Récupération des sequence hors délai et urgentes
$nb_seq_ret = cherche_seq($_SESSION['IDUSER'],$_GET['id_grp']);
$seq = explode("|",$nb_seq_ret);
$Response['Sequences'] = array("seqDepassee"=>$seq[0],"seqUrgente"=>$seq[1]);
// Récupération du nombre de postts dans les forums
$majuscule = $_SESSION['prenom']." ".$_SESSION['nom'];
$log_der = mysql_query("SELECT * from log WHERE login='".$_SESSION['login']."' order by log_cdn desc limit 1");
$date_der = mysql_result($log_der,0,'date_debut');
$heure_der = mysql_result($log_der,0,'heure_debut');
$dateComp =  $date_der." ".$heure_der;
$NbPostsForum = cherche_forum($_GET['id_grp'],$dateComp,$majuscule);
$nbPosts = ($NbPostsForum > 0) ? $NbPostsForum: 0;
$Response['Forum'] = array("Forum"=>$nbPosts);
echo json_encode($Response);
?>
