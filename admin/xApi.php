<?php
if (!isset($_SESSION)) session_start();
header("Content-type  : text/plain");
header("Cache-Control: no-cache, max-age=0");
include ("../include/UrlParam2PhpVar.inc.php");
require '../admin.inc.php';
require '../fonction.inc.php';
require "../lang$lg.inc.php";
require '../fonction_html.inc.php';
dbConnect();
$date_op = date("Y-m-d H:i:s");
$date_messagerie = date("Y/m/d H:i:s");
$heure_fiche = substr($date_op,11);
$date_fiche = substr($date_op,0,10);
$url = parse_url($_SERVER['REQUEST_URI']);
$resultUrl=array();
parse_str($url['query'],$resultUrl);
if (isset($resultUrl['course']))
{
   $oAuth = base64url_decode($resultUrl['course']);
   $Register = base64url_decode($resultUrl['registration']);
   $TabCours = explode('|',$oAuth);
   $NbrElm = count($TabCours);
   if($NbrElm == 5 && $_SESSION['typ_user'] == 'APPRENANT')
   {
       $Ext = $TabCours[4];
       $commentaire = $mess_lanc_act;
       $qualité = '';
       $new_fiche = Donne_ID ($connect,"select max(fiche_cdn) from fiche_suivi");
       $req_fiche = mysql_query("INSERT INTO fiche_suivi (fiche_cdn,fiche_utilisateur_no,fiche_auteur_no,fiche_qualite_lb,fiche_date_dt,".
                               "fiche_heure_dt,fiche_commentaire_cmt,fiche_grp_no,fiche_parc_no,fiche_seq_no,fiche_act_no,".
                               "fiche_typaction_lb) VALUES($new_fiche,".$_SESSION['id_user'].",".$_SESSION['id_user'].",'$qualite','$date_fiche',".
                               "'$heure_fiche',\"$commentaire\",'".$Ext."','".$TabCours[1]."','".$TabCours[2].
                               "','".$TabCours[3]."','suivi pedagogique')");
       $Letat = (isset($NumVerb) && ($NumVerb == 'terminated' || $NumVerb == 'passed' || $NumVerb == 'responded' || $NumVerb == 'scored' || $NumVerb == 'completed')) ? 'TERMINE' : 'EN COURS';
       $change_etat = mysql_query("update suivi1_$Ext set suivi_etat_lb='".$Letat."' where
                                   suivi_act_no='".$TabCours[3]."' and
                                   suivi_utilisateur_no = '".$_SESSION['id_user']."'");
       $numtrq = Donne_ID ($connect,"select max(traq_cdn) from traque");
       $insere_traq= mysql_query("INSERT INTO traque (traq_cdn,traq_util_no,traq_act_no,traq_grp_no,traq_date_dt,traq_hd_dt)".
                                 " values($numtrq,".$_SESSION['id_user'].",".$TabCours[3].",$Ext,'$date_fiche','$heure_fiche')");
       $NumVerb = 'En attente';
       echo $NumVerb;
   }
}
?>