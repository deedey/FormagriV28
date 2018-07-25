<?php
if (!isset($_SESSION)) session_start();
if (!isset($_SESSION['id_user']) || $_SESSION['id_user'] == "")
{
  exit();
}
header("Content-type  : text/plain");
header("Cache-Control: no-cache, max-age=0");
require "../fonction.inc.php";
require "../admin.inc.php";
include "../include/UrlParam2PhpVar.inc.php";
require "../lang$lg.inc.php";
require "../fonction_html.inc.php";
require "../class/class_module.php";
dbConnect();
$date_op = date("Y-m-d H:i:s" ,time());
$heure_fiche = substr($date_op,11);
$date_fiche = substr($date_op,0,10);
//recupération du lien et du contenu via ajax
if (isset($recuperation) && $recuperation == 1)
{
   $envoi = "<table cellspacing='0' cellpadding='2' border='0' width=100%><tbody><tr><td width='90%'>";
   $numero_tut = mysql_result(mysql_query("select count(*) from tuteur where tut_tuteur_no='$id_tut'and tut_apprenant_no = $id_util"),0);
   $nom_tut=GetDataField ($connect,"SELECT util_nom_lb from utilisateur where util_cdn='$id_tut'","util_nom_lb");
   $prenom_tut=GetDataField ($connect,"SELECT util_prenom_lb from utilisateur where util_cdn='$id_tut'","util_prenom_lb");
   $photo_tut = GetDataField ($connect,"SELECT util_photo_lb from utilisateur where util_cdn = $id_tut","util_photo_lb");
   $lien="../prescription.php?affiche_fiche_app=1&identite=1&id_util=$id_tut&stopper=1";
   $lien= urlencode($lien);
   $envoi .= "<DIV id='sequence'><A HREF=\"javascript:void(0);\" onclick=\"window.open('../trace.php?link=$lien','','width=600,height=340,resizable=yes,status=no');\"";
   if ($photo_tut != ""){
      list($w_img, $h_img, $type_img, $attr_img) = getimagesize("../images/$photo_tut");
      $envoi .= " onMouseOver=\"overlib('',CENTER,ABOVE,TEXTCOLOR, '#ffffff', TEXTSIZE, 2, WIDTH,$w_img, HEIGHT, $h_img, BACKGROUND, 'images/$photo_tut', PADX, 60, 20, PADY, 20, 20);\" onMouseOut=\"nd();\">";
   }else{
      $titrer="$mpr_consult_fic $prenom_tut $nom_tut";
      $envoi .= bulle($titrer,"","LEFT","",160);
      $titrer = "";
   }
   $envoi .= "<span style='font-size:11px;font-weight:bold;'> $prenom_tut $nom_tut</span></A></DIV></td>";
   $inscripteur = GetDataField ($connect,"SELECT util_auteur_no from utilisateur where util_cdn = $id_util","util_auteur_no");
   $crea_grp = GetDataField ($connect,"SELECT grp_resp_no from groupe where grp_cdn  = $grp_resp","grp_resp_no");
   if ($inscripteur == $id_user || $crea_grp == $id_user || $typ_user == "ADMINISTRATEUR")
   {
      $lien = "gestion_affectation.php?affecte_groupe=1&grp_resp=$grp_resp&supp_tut=1&num_app=$id_util&num_tut=$id_tut";
      $lien=urlencode($lien);
      $envoi .= "<td width='10%'><A href=\"$monURI/trace.php?link=$lien\" onclick=\"javascript:return(conf());\" target='main'";
      $envoi .= bulle($mess_ag_supp,"","LEFT","",100).
           "<IMG SRC=\"$monURI/images/messagerie/icoGpoubel.gif\" width=\"10\" height=\"15\"  BORDER=0></A></td>";
   }
   else
      $envoi .= "<td>&nbsp;</td>";
   $envoi .= "</tr></tbody></table>";
   $commentaire ="$mess_supn_tut $prenom_tut $nom_tut\n";
   if ($commentaire != '' && $numero_tut == 1)
   {
          $inscripteur = GetDataField ($connect,"select util_auteur_no from utilisateur where util_cdn='$id_util'","util_auteur_no");
          $prescripteur = GetDataField ($connect,"select grp_resp_no from groupe where grp_cdn='$grp_resp'","grp_resp_no");
          if ($prescripteur == $inscripteur)
             $qualite = $msq_prescripteur;
          elseif ($inscripteur == $id_user)
             $qualite = $mess_inscripteur;
          elseif ($prescripteur != $inscripteur && $inscripteur != $id_user)
             $qualite = $mess_typ_adm;
          $action_fiche = $mess_tutorat;
          $new_fiche = Donne_ID ($connect,"select max(fiche_cdn) from fiche_suivi");
          $req_fiche = mysql_query("INSERT INTO fiche_suivi (fiche_cdn,fiche_utilisateur_no,fiche_auteur_no,fiche_qualite_lb,fiche_date_dt,fiche_heure_dt,fiche_commentaire_cmt,fiche_grp_no,fiche_parc_no,fiche_seq_no,fiche_act_no,fiche_typaction_lb) VALUES($new_fiche,$id_util,$id_user,'$qualite','$date_fiche','$heure_fiche',\"$commentaire\",0,0,0,0,\"$action_fiche\")");
   }
   if ($numero_tut == 1)
      echo  utf2Charset($envoi,$charset);
   else
      echo "";
  exit;

}

// affecter tuteur en insertion
if (isset($ajout_tut) && $ajout_tut == 1)
{
   $nom_app = GetDataField ($connect,"select util_nom_lb from utilisateur where util_cdn = $id_util","util_nom_lb");
   $prenom_app = GetDataField ($connect,"select util_prenom_lb from utilisateur where util_cdn = $id_util","util_prenom_lb");
   $nom_grp = GetDataField ($connect,"SELECT grp_nom_lb from groupe where grp_cdn  = $grp_resp","grp_nom_lb");
   if (isset($id_tut) && $id_tut > 0)
   {
      $nom_tut = GetDataField ($connect,"select util_nom_lb from utilisateur where util_cdn = $id_tut","util_nom_lb");
      $prenom_tut = GetDataField ($connect,"select util_prenom_lb from utilisateur where util_cdn = $id_tut","util_prenom_lb");
      $req_verif_tut = mysql_query("SELECT count(tut_tuteur_no) from tuteur where tut_apprenant_no = $id_util and tut_tuteur_no = $id_tut");
      $verif_tut = mysql_result($req_verif_tut,0);
   }
   else
      $verif_tut = false;
   if ($verif_tut == 0)
   {
      $commentaire = "$mess_ajt_tut $prenom_tut $nom_tut" ;
      $inserer = mysql_query("INSERT INTO tuteur (tut_cdn,tut_apprenant_no,tut_tuteur_no) VALUES ('','$id_util','$id_tut')");
      if ($commentaire != '')
      {
          $inscripteur = GetDataField ($connect,"select util_auteur_no from utilisateur where util_cdn='$id_util'","util_auteur_no");
          $prescripteur = GetDataField ($connect,"select grp_resp_no from groupe where grp_cdn='$grp_resp'","grp_resp_no");
          if ($prescripteur == $inscripteur)
             $qualite = $msq_prescripteur;
          elseif ($inscripteur == $id_user)
             $qualite = $mess_inscripteur;
          elseif ($prescripteur != $inscripteur && $inscripteur != $id_user)
             $qualite = $mess_typ_adm;
          $action_fiche = $mess_tutorat;
          $new_fiche = Donne_ID ($connect,"select max(fiche_cdn) from fiche_suivi");
          $req_fiche = mysql_query("INSERT INTO fiche_suivi (fiche_cdn,fiche_utilisateur_no,fiche_auteur_no,fiche_qualite_lb,fiche_date_dt,fiche_heure_dt,fiche_commentaire_cmt,fiche_grp_no,fiche_parc_no,fiche_seq_no,fiche_act_no,fiche_typaction_lb) VALUES($new_fiche,$id_util,$id_user,'$qualite','$date_fiche','$heure_fiche',\"$commentaire\",0,0,0,0,\"$action_fiche\")");
      }
      $mess_notif = "$mess_ajt_tut  $prenom_tut $nom_tut  $a  $prenom_app $nom_app";// <br /> $mess_reload
   }
   else
      $mess_notif = "Attention ! $prenom_app $nom_app $mess_tutdja $prenom_tut $nom_tut";
   echo  utf2Charset($mess_notif,$charset);
   exit();
}
?>
