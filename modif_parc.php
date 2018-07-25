<?php
if (!isset($_SESSION)) session_start();
include ("include/UrlParam2PhpVar.inc.php");
if (!isset($_SESSION['id_user']) || $_SESSION['id_user'] == '')
{
  exit();
}
require "admin.inc.php";
require 'fonction.inc.php';
require 'fonction_html.inc.php';
require 'langues/prescription.inc.php';
require 'langues/formation.inc.php';
require 'class/class_formation.php';
require 'class/class_suivi.inc.php';
require "lang$lg.inc.php";
dbConnect();
//
if (isset($_GET['ReinitSco']) && $_GET['ReinitSco'] == 1)
{
  $appel = ReinitSco($_GET['scormid']);
  exit;
}
$date_op = date("Y-m-d H:i:s" ,time());
$heure_fiche = substr($date_op,11);
$date_fiche = substr($date_op,0,10);
$today = date("d/n/Y");
$Ext="_$numero_groupe";
$nom_user = $_SESSION['name_user'];
$prenom_user = $_SESSION['prename_user'];
$lien_retour = "http://".$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
$action_fiche = $gest_presc;

if (isset($supp_module) && $supp_module == 1)
{
       $cherche_seq_presc = mysql_query("SELECT presc_seq_no FROM prescription$Ext WHERE
                                         presc_utilisateur_no = '$utilisateur' AND
                                         presc_parc_no=$id_parc AND
                                         presc_grp_no=$numero_groupe");
       $nbr_seq=mysql_num_rows($cherche_seq_presc);
       if ($nbr_seq > 0)
       {
         $seqcount=0;
         while($seqcount < $nbr_seq)
         {
           $id_seq = mysql_result($cherche_seq_presc,$seqcount,"presc_seq_no");
           $type_seq = GetDataField ($connect,"select seq_type_lb from sequence where seq_cdn='$id_seq'","seq_type_lb");
           $scormOk = (strstr($type_seq,'SCORM')) ? 1 : 0;
           SupSeqModifParc($id_seq,$scormOk,$utilisateur,$numero_groupe,$id_parc);
           $seqcount++;
         }
       }
       $effacer_parc = mysql_query("DELETE from suivi3$Ext where
                                    suiv3_utilisateur_no = '$utilisateur' AND
                                    suiv3_parc_no = '$id_parc' AND
                                    suiv3_grp_no = '$numero_groupe'");
    $nom_parc = GetDataField ($connect,"select parcours_nom_lb from parcours where parcours_cdn = $id_parc","parcours_nom_lb");
    $mess_notif = "$mess_lemod : $nom_parc $parc_supp_ok";
}

if (isset($ajout_seq) && $ajout_seq == 1)
{
    $mess_notif = ajout_seq_suivi($id_seq);
}
if (isset($supp_seq) && $supp_seq == 1 && isset($suppression) && $suppression == 1)
{
   $mess_notif = SupSeqModifParc($id_seq,$scormOk,$utilisateur,$numero_groupe,$id_parc);
   $parc_query = mysql_query ("select presc_parc_no from prescription$Ext WHERE
                               presc_utilisateur_no=$utilisateur and
                               presc_grp_no = $numero_groupe
                               GROUP BY presc_parc_no
                               ORDER BY presc_ordre_no");
   $nbre_parc = mysql_num_rows ($parc_query);
   if ($nbre_parc == 0)
   {
     $lien = "gestion_affectation.php?affecte_groupe=1&grp_resp=$numero_groupe";
     $lien = urlencode($lien);
     echo "<script language=\"JavaScript\">";
     echo "document.location.replace(\"trace.php?link=$lien\")";
     echo "</script>";
   exit();
   }
}
// Réinitialisation d'une séquence Scorm
if (isset($reinit) && $reinit == 1)
{
   $mess_notif = Reinit($id_seq);
   if ($venue == 'details')
   {
      echo "<script language=\"JavaScript\">";
      echo "history.go(-1)";
      echo "</script>";
     exit();
   }
}
// Modification de l'ordre des parcours
if (isset($modif_ordre) && $modif_ordre == 1)
{
   $mess_notif = ModifOrdreParc($utilisateur);
   unset($parc);
}
// Modification de l'ordre des des sequences dans un parcours
if (isset($modif_ordre_seq) && $modif_ordre_seq == 1)
{
   $mess_notif = ModifOrdreSeqParc($utilisateur);
   unset($parc);
}

if (isset($_POST['modifier']) && $_POST['modifier'] == 1)
{
  $monRetour = ModifPrescUtil($_POST);
  //echo "<pre>";print_r($monRetour);echo "</pre>";
  if (isset($monRetour))
  {
   $mess_notif = $monRetour['message'];
   $utilisateur = $monRetour['utilisateur'];
   $numero_groupe = $monRetour['numero_groupe'];
   $hgrp = $monRetour['hgrp'];
   $a_faire = $monRetour['a_faire'];
  }
}
include ('style.inc.php');
?>
<script type="text/javascript" src="OutilsJs/lib/ajax/ajax_cms.js"></script>
<div id="affiche" class="Status"></div>
<div id="mon_contenu" class="cms"
        onClick="javascript:$(document).ready(function() {if ($.browser.msie) {$('#mon_contenu').hide();}else{$('#mon_contenu').hide('slow');}})"
        <?php echo "title=\"$mess_clkF\">";
echo "</div>";
if (isset($mess_notif) && $mess_notif != '')
{
   echo notifier($mess_notif);

}
if (isset($a_faire) && $a_faire == 1)
{
    $parc_query = mysql_query ("select presc_parc_no from prescription$Ext WHERE
                                prescription$Ext.presc_utilisateur_no=$utilisateur and
                                prescription$Ext.presc_grp_no= $numero_groupe
                                GROUP BY presc_parc_no
                                ORDER BY prescription$Ext.presc_ordre_no");
    $nb_parc = mysql_num_rows ($parc_query);
    $num_app = $utilisateur;
    $nom_user=GetDataField ($connect,"select util_nom_lb from utilisateur where util_cdn='$num_app'","util_nom_lb");
    $prenom_user=GetDataField ($connect,"select util_prenom_lb from utilisateur where util_cdn='$num_app'","util_prenom_lb");
    $inscripteur=GetDataField ($connect,"select util_auteur_no from utilisateur where util_cdn='$num_app'","util_auteur_no");
    if ($nb_parc == 0 && $typ_user == "APPRENANT")
    {
      entete_simple($mess_menu_mod_presc);
      echo "<TR><TD><FONT SIZE='2'>$mess_gp_tut_nopresc1</B></FONT>";
      echo fin_tableau("");
      exit;
    }
    $nom_grp =GetDataField ($connect,"select grp_nom_lb from groupe WHERE grp_cdn = $numero_groupe","grp_nom_lb");
    $crea_grp =GetDataField ($connect,"select grp_resp_no from groupe WHERE grp_cdn = $numero_groupe","grp_resp_no");
    $carac_grp = strlen($nom_grp);
    if ($carac_grp > 60)
       $nom_grp1 = substr($nom_grp,0,60)."..";
    else
       $nom_grp1 = $nom_grp;
    $titre = "$msg_indivi_tit $nom_grp1 $de ".ucfirst(strtolower($nom_user))." ".ucfirst(strtolower($prenom_user));
    entete_simple($titre);
    $titre = '';
    if ($mess_aff != "" && !$suppression)
       echo "<TR><TD colspan='2' align='center' bgcolor='#FFFFFF' width='100%'><TABLE border=0 cellspacing='0' cellpadding='3'>".
            "<TR><TD align='center'><FONT size='2'><B>$err_saisie</B></FONT><BR><FONT size='1'>* $err_saisie1</font></TD></TR>".
            "<TR><TD align=left><FONT size='1'>$mess_aff</FONT></TD></TR></TABLE>";
    elseif($mess_aff != "" && $suppression == 1)
       echo "<TR><TD colspan='2' align='center' bgcolor='#FFFFFF' width='100%'><TABLE border=0 cellspacing='0' cellpadding='3'>".
            "<TR><TD align=left><FONT size='1'>$mess_aff</FONT></TD></TR></TABLE></TD></TR>";
    echo "<TR><TD colspan='2' width='100%'><TABLE bgColor='#FFFFFF' cellspacing='0' cellpadding='0' border='0' width='100%'><TR><TD>";

    echo "<table cellpadding='3' cellspacing='0' border='0' width='100%'><tr height='30'><td colspan=2>";
    if ($typ_user != "FORMATEUR_REFERENT")
    {
        $lien_mod = "include/prescrire_grp.inc.php?ajout_mod_app=1&utilisateur=$utilisateur&numero_groupe=$numero_groupe";
        $lien_mod = urlencode($lien_mod);
        echo "<div id='format' style=\"float:left;padding-left:4px;\"><a href=\"javascript:void(0);\" class= 'bouton_new' ".
             "onclick=\"javascript:window.open('trace.php?link=$lien_mod', 'Espace', 'scrollbars=yes,height=400,width=500,resizable=yes');\">".
             "$mess_ajt_mod</a></div>";
    }
    if ($nb_parc > 0)
    {
       $lien = urlencode("gest_parc_frm1.php?saut=1&utilisateur=$utilisateur&a_faire=1&numero_groupe=$numero_groupe&tout=0&graph=1");
       echo "<div id='format' style=\"float:left;padding-left:8px;\"><a href=\"trace.php?link=$lien\" class= 'bouton_new'>$mesg_vaform</a></div>";
    }
    echo aide_div("individualisation",8,0,0,0);
    echo "</td></tr></table></td></tr>";

    $bgcolorB = '#9C72CD';
    $bgcolorA = '#FFEDD9';
    $inscripteur=GetDataField ($connect,"select util_auteur_no from utilisateur where util_cdn='$utilisateur'","util_auteur_no");
    echo "<TABLE width=100% cellspacing='1' cellpadding='2' border='0'>";
    echo "<FORM name = 'form' action=\"modif_parc.php\" method='post'>";
    echo "<INPUT TYPE='HIDDEN' NAME='utilisateur' VALUE='$utilisateur'>";
    echo "<INPUT TYPE='HIDDEN' NAME='numero_groupe' VALUE='$numero_groupe'>";
    echo "<INPUT TYPE='HIDDEN' NAME='hgrp' VALUE='$hgrp'>";
    echo "<INPUT TYPE='HIDDEN' NAME='modifier' VALUE='1'>";
       $p=0;$i_seq=0;$i_parc=0;
       $date_max = array();
       $num_parc = array();
       $ordre_p = array();
       while ($p < $nb_parc)
       {
              $scormOk = 0;
              $fin_parc=0;
              $id_parc = mysql_result ($parc_query,$p,"presc_parc_no");
              ?>
              <script type="text/javascript">
                $().ready(function() {

                   function findValueCallback(event, data, formatted) {
                          $("<li>").text( !data ? "No match!" : "Selected: " + formatted).appendTo("#result");
                   }

                   function formatItem(row) {
                          return row[0];
                   }

                   function formatResult(row) {

                          return row[0] + "          | id: " + row[1];
                   }
                   $("#appel_presc<?php echo $id_parc;?>").autocompleting("prescription/ajout_presc_app.php", {
                          delay: 150,
                          width: 150,
                          max: 15,
                          inputClass: "ac2_input",
                          resultsClass: "ac2_results",
                          loadingClass: "ac_loading",
                          formatItem: formatItem,
                          formatResult: formatResult,
                          minChars: 1,
                          selectFirst: false
                   });

                   $("#appel_form<?php echo $id_parc;?>").autocompleting("prescription/ajout_form_app.php", {
                          delay: 150,
                          width: 150,
                          max: 15,
                          inputClass: "ac2_input",
                          resultsClass: "ac2_results",
                          loadingClass: "ac_loading",
                          formatItem: formatItem,
                          formatResult: formatResult,
                          minChars: 1,
                          selectFirst: false
                   });
                });
              </script>
              <?php
              $num_parc[$p] = $id_parc;
              $type_parcours = getdatafield ($connect,"SELECT parcours_type_lb from parcours where parcours_cdn = $id_parc","parcours_type_lb");
              $icono = "";
              $etat_parc =  GetDataField ($connect,"select suiv3_etat_lb from suivi3$Ext where
                                                    suiv3_parc_no = $id_parc and
                                                    suiv3_utilisateur_no = $num_app and
                                                    suiv3_grp_no = $numero_groupe","suiv3_etat_lb");
              $ordre_parc = GetDataField ($connect,"select presc_ordre_no from prescription$Ext where
                                                    presc_utilisateur_no = $num_app and
                                                    presc_parc_no = $id_parc and
                                                    presc_grp_no = $numero_groupe
                                                    group by presc_parc_no","presc_ordre_no");
              $ordre_p[$p] = $ordre_parc;
              if ($p > 0)
              {
                 $parc_precedent = $num_parc[$p-1];
                 $ordre_precedent = $ordre_p[$p-1];
              }
              if ($id_parc > 0)
              {
                $createur = GetDataField ($connect,"select parcours_auteur_no from parcours where parcours_cdn = $id_parc","parcours_auteur_no");
                $nom = GetDataField ($connect,"select parcours_nom_lb from parcours where parcours_cdn = $id_parc","parcours_nom_lb");
                $description = GetDataField ($connect,"select parcours_desc_cmt from parcours where parcours_cdn = $id_parc","parcours_desc_cmt");
                $id_ref_parc = GetDataField ($connect,"select parcours_referentiel_no from parcours where parcours_cdn = $id_parc","parcours_referentiel_no");
                if ($id_ref_parc > 0)
                   $nom_referentiel = GetDataField ($connect,"select ref_desc_cmt from referentiel where ref_cdn = $id_ref_parc","ref_desc_cmt");
                else
                   $nom_referentiel = '';
                $desc = $nom_referentiel;
                $nom_createur = GetDataField ($connect,"select util_nom_lb from utilisateur where util_cdn = $createur","util_nom_lb");
                $prenom_createur = GetDataField ($connect,"select util_prenom_lb from utilisateur where util_cdn = $createur","util_prenom_lb");
              }
              else
              {
                $nom = GetDataField ($connect,"select parcours_nom_lb from parcours where parcours_cdn = $id_parc","parcours_nom_lb");
                $description = GetDataField ($connect,"select parcours_desc_cmt from parcours where parcours_cdn = $id_parc","parcours_desc_cmt");
                $desc = $mess_gp_nolien_ref;
                $nom_createur = $mess_gen_formagri;
                $prenom_createur = $mess_gen_gen_formagri;
                $nom="$nom";
                $nom_referentiel = $mess_gp_nolien_ref;
              }
              $nom_parc=$nom;
              $req_min= mysql_query("select presc_datedeb_dt from prescription$Ext where
                                     presc_utilisateur_no = $num_app and
                                     presc_parc_no = $id_parc and
                                     presc_grp_no = $numero_groupe
                                     order by presc_datedeb_dt asc");
              $date_min = mysql_result($req_min,0,"presc_datedeb_dt");
              $nb_jours_req_min = mysql_query ("select TO_DAYS('$date_min')");
              $req_max= mysql_query("select presc_datefin_dt from prescription$Ext where
                                     presc_utilisateur_no = $num_app and
                                     presc_parc_no = $id_parc and
                                     presc_grp_no = $numero_groupe
                                     order by presc_datefin_dt asc");
              $nbr_max = mysql_num_rows($req_max);
              $date_max[$p] = mysql_result($req_max,$nbr_max-1,"presc_datefin_dt");
              $nb_jours_req_max = mysql_query ("select TO_DAYS('$date_max[$p]')");

              $duree_act_query = mysql_result(mysql_query("SELECT sum(activite.act_duree_nb) from
                                             activite,suivi1$Ext,sequence,prescription$Ext where
                                             prescription$Ext.presc_parc_no = $id_parc and
                                             prescription$Ext.presc_utilisateur_no = $num_app and
                                             prescription$Ext.presc_grp_no = $numero_groupe and
                                             sequence.seq_cdn = prescription$Ext.presc_seq_no and
                                             activite.act_seq_no = sequence.seq_cdn and
                                             suivi1$Ext.suivi_act_no = activite.act_cdn and
                                             suivi1$Ext.suivi_utilisateur_no = $num_app and
                                             suivi1$Ext.suivi_grp_no=$numero_groupe"),0);
              $duree_mod_query = mysql_result(mysql_query ("SELECT sum(mod_duree_nb) from
                                             sequence,prescription$Ext,scorm_module,scorm_util_module$Ext where
                                             prescription$Ext.presc_parc_no = $id_parc and
                                             prescription$Ext.presc_utilisateur_no = $num_app and
                                             prescription$Ext.presc_grp_no = $numero_groupe and
                                             sequence.seq_cdn = prescription$Ext.presc_seq_no and
                                             scorm_module.mod_seq_no = prescription$Ext.presc_seq_no and
                                             scorm_module.mod_parc_no = prescription$Ext.presc_parc_no and
                                             scorm_util_module$Ext.mod_module_no = scorm_module.mod_cdn and
                                             scorm_util_module$Ext.user_module_no = $num_app and
                                             scorm_util_module$Ext.mod_grp_no = $numero_groupe"),0);
              $duree_parc = duree_calc($duree_mod_query + $duree_act_query);
              echo "<tr><td width='100%'><div id='par_mod' class='individ'>";
              echo "<table width='100%'><tr height='5'><td></td></tr><tr height='30'>";
              if (($typ_user == "ADMINISTRATEUR" || ($typ_user == "RESPONSABLE_FORMATION" && ($inscripteur == $id_user || $crea_grp == $id_user))) && $p > 0){
                 $lien = "modif_parc.php?a_faire=1&modif_ordre=1&parc=$id_parc&ordre_parc=$ordre_parc&ancien_ordre=$ordre_precedent&ancien_parc=$parc_precedent&utilisateur=$utilisateur&numero_groupe=$numero_groupe&hgrp=$hgrp&ajout_parc=0";
                 $lien = urlencode($lien);
                 $url="trace.php?link=$lien";
                 echo "<td valign='top' height='10' width='5%'><A HREF=\"$url\" target='main' title=\"$mess_decrement\">".
                      "<IMG SRC=\"images/modules/tut_form/flechehaut.gif\" ALT=\"$mess_decrement\" border='0'></a>".nbsp(4);
              }
              else
                 echo "<td valign='top' height='10' width='5%'>";
              $ce_titre = "$mess_gp_cree_par $prenom_createur $nom_createur.   ";
              if ($description != '')
                 $ce_titre .= "<br /><u>$mess_desc</u> : ".html_entity_decode($description,ENT_QUOTES,'ISO-8859-1');
              if ($nom_referentiel != '')
                 $ce_titre .= " <br /><u>$msq_act_ref</u> ".html_entity_decode($desc,ENT_QUOTES,'ISO-8859-1');
              echo "<b><font size='2'>$msq_parc</font></b></td>".
                   "<td align='left' valign='top' width='95%'>".
                   "<table cellpadding='0' cellspacing='0'><tr><td valign='middle'>".
                   "<a href=\"javascript:void(0);\" style=\"cursor:help;\" target='main' ".
              bulle(strip_tags($ce_titre),"","LEFT","ABOVE",250)." $icono <font size='2'>$nom</font></a></td>";
              if (($typ_user == 'ADMINISTRATEUR' || $inscripteur == $id_user || $crea_grp == $id_user) && $etat_parc == 'A FAIRE')
              {
                    $lien = "modif_parc.php?numero_groupe=$numero_groupe&supp_module=1&id_seq=$seq&id_parc=$id_parc&utilisateur=$utilisateur&a_faire=1";
                    $lien = urlencode($lien);
                    echo "  <td valign='top' style=\"padding-left: 3mm;\">$bouton_gauche<A HREF=\"trace.php?link=$lien\" ".
                         "onclick=\"return(conf());\" target='main'>$mess_ag_supp</A>$bouton_droite</td></tr></table></td>";
              }
              else
                   echo "<td></td></tr></table></td>";
              echo "</tr>";
              $ce_titre = '';
              $ch_date_deb = explode ("-",$date_min);
              $date_min = "$ch_date_deb[2]/$ch_date_deb[1]/$ch_date_deb[0]";
              $ch_date_fin = explode ("-",$date_max[$p]);
              $date_maxi = "$ch_date_fin[2]/$ch_date_fin[1]/$ch_date_fin[0]";
              if ($typ_user == "ADMINISTRATEUR")
              {
                    echo "<tr><td valign='top' width='10%'><b>$msq_prescripteur</td><td colspan='2'>";
                    echo '<div id="ajt_presc['.$id_parc.']"></div';
                    $param = '';
                    echo '<input type="hidden" name="presc_ajax['.$id_parc.']" value="1">'.
                         '<input type="text" name="presc_ref['.$id_parc.']" id="appel_presc'.$id_parc.'" value= "'.$mesg_modpresc.'"  '.
                         'onClick="javascript:var changer=getElementById(\'appel_presc'.$id_parc.'\');changer.value=\'\';"/>';
                    echo "</td></tr>";
              }
              elseif($typ_user == "RESPONSABLE_FORMATION" && $inscripteur == $id_user)
              {
                    echo "";
              }
              else
              {
                       echo "";
                    echo "<tr><td valign='top'><b>$mess_gp_duree</b></td><td valign='top' colspan='2'><B>$duree_parc</B></td></tr>";
                    echo "<tr><td valign='top'><b>$mess_gp_date_deb</b></td><td valign='top' colspan='2'><B>$date_min</B></td></tr>";
              }
              if ($typ_user == "ADMINISTRATEUR" || ($typ_user == "RESPONSABLE_FORMATION" && ($inscripteur == $id_user || $crea_grp == $id_user)))
              {
                    echo "<tr><td valign='top' width='10%'><b>$msq_formateur</b></td><td valign='top' colspan='2'>";
                    $param = '';
                    echo '<div id="ajt_form['.$id_parc.']"></div';
                    echo '<input type="hidden" name="form_ajax['.$id_parc.']" value="1">'.
                         '<input type="text" name="form_refer['.$id_parc.']" id="appel_form'.$id_parc.'" value= "'.$mesg_modform.'"  '.
                         'onClick="javascript:var changer=getElementById(\'appel_form'.$id_parc.'\');changer.value=\'\';"/>';
                    echo "</td></tr>";
                    echo "<tr><td valign='top' width='10%'><b>$mess_gp_duree</b></td><td valign='top' colspan='2'><B>$duree_parc</B></td></tr>";
                    echo "<tr><td valign='top' width='10%'><b>$mess_gp_date_deb</b></td><td valign='top' align='left' colspan='2'>
                          <INPUT TYPE= TEXT class='INPUT' NAME='date_min[$id_parc]' id='ddtp$id_parc' value= '$date_min' MAXLENGTH='10' ".
                          "size='10' title=\"$cal_click\" onfocus=\"this.select();lcs(this)\" ".
                          "onclick=\"event.cancelBubble=true;this.select();lcs(this)\" ".
                          "onChange=\"var ddep$id_parc=document.getElementById('ddtp$id_parc');".
                          "validateDate(ddep$id_parc,'$date_min','ddtp$id_parc');\"></td></tr>";
                    $i_parc++;
                    echo "<INPUT TYPE='HIDDEN' NAME='module[$i_parc]' VALUE='$id_parc'>";
                    echo "<tr><td valign='top' width='10%'><b>$mess_gp_date_fin</b></td>".
                         "<td valign='top' align='left' colspan='2'>".
                         "<INPUT TYPE= TEXT class='INPUT' NAME='date_max[$id_parc]' id='dfnp$id_parc' value= '$date_maxi' ".
                         "MAXLENGTH='10' size='10' title=\"$cal_click\" onfocus=\"this.select();lcs(this)\" ".
                         "onclick=\"event.cancelBubble=true;this.select();lcs(this)\" ".
                         "onChange=\"var dffp$id_parc=document.getElementById('dfnp$id_parc');".
                         "validateDate(dffp$id_parc,'$date_maxi','dfnp$id_parc');\"></td></tr>";
              }
              else
                    echo "<tr><td valign='top' width='10%'><b>$mess_gp_date_fin</b></td><td valign='top' colspan='2'><B>$date_maxi</B></td></tr>";
              $etat_parc = ucfirst(strtolower($etat_parc));
              if ($etat_parc == "Termine")
                 $etat_parc = $mess_lanc_termine;
              echo "<tr><td valign='top' width='10%'><b>$mess_gp_etat</b></td><td valign='top' align='left' nowrap colspan='2'><B>$etat_parc</B></td></tr>";
              echo "<TD colspan='3' width='100%'>";
    //Sequences a faire dans le parcours déroulé
    $ouvrir = 'parcours';
    $parc_ouvert = $id_parc;
    if ($utilisateur &&  $typ_user == "TUTEUR")
    {

         $seq_query = mysql_query ("select distinct suiv2_etat_lb,suiv2_seq_no FROM
                      suivi2$Ext,prescription$Ext WHERE
                      suivi2$Ext.suiv2_seq_no = prescription$Ext.presc_seq_no and
                      prescription$Ext.presc_utilisateur_no=$utilisateur and
                      prescription$Ext.presc_parc_no=$id_parc and
                      suiv2_utilisateur_no = $utilisateur and
                      suiv2_grp_no = $numero_groupe and
                      presc_grp_no = $numero_groupe
                      ORDER BY prescription$Ext.presc_datefin_dt,suivi2$Ext.suiv2_ordre_no asc");
      $num_app = $utilisateur;
    }
    elseif ($utilisateur && ($typ_user == "ADMINISTRATEUR" || $typ_user == "FORMATEUR_REFERENT" || $typ_user == "RESPONSABLE_FORMATION"))
    {
         $seq_query = mysql_query ("SELECT suiv2_etat_lb,suiv2_seq_no FROM
                      suivi2$Ext,prescription$Ext WHERE
                      suivi2$Ext.suiv2_seq_no = prescription$Ext.presc_seq_no and
                      prescription$Ext.presc_parc_no=$id_parc and
                      prescription$Ext.presc_utilisateur_no=$utilisateur and
                      suivi2$Ext.suiv2_utilisateur_no = $utilisateur and
                      suivi2$Ext.suiv2_grp_no = $numero_groupe and
                      prescription$Ext.presc_grp_no = $numero_groupe
                      ORDER BY prescription$Ext.presc_datefin_dt,suivi2$Ext.suiv2_ordre_no asc");
         $num_app = $utilisateur;
    }
    $nb_seq = mysql_num_rows ($seq_query);
    $inscripteur=GetDataField ($connect,"select util_auteur_no from utilisateur where util_cdn='$utilisateur'","util_auteur_no");
    $bgcolor2 = '#CD7DCB';
    $bgcolor1 = '#FFFEED';
       echo "<table width=100% cellspacing='1' cellpadding='3' border='0'>";
       echo "<tr bgcolor=\"#2B677A\">";
       if ($typ_user == "ADMINISTRATEUR" || (($inscripteur == $id_user && $hgrp != 1) || $crea_grp == $id_user) || $typ_user == "FORMATEUR_REFERENT" || $typ_user == "RESPONSABLE_FORMATION")
          echo "<TD width='28%' colspan='2' style=\"color: white;\"><b>$mess_gp_seq_parc</b></TD>";
       else
          echo"<TD height='10'>&nbsp;</TD>";
       echo "<TD height='15%' style=\"color: white;\"><b>$msq_prescripteur</b></TD>";
       if ($typ_user == "ADMINISTRATEUR" || ($inscripteur == $id_user && $hgrp != 1) || $typ_user == "FORMATEUR_REFERENT" || $typ_user == "RESPONSABLE_FORMATION")
           echo "<TD height='20' width='18%' style=\"color: white;\">";
       else
           echo "<TD height='20' width='20%' style=\"color: white;\">";
       echo "<b>$msq_formateur</b></TD>";
       echo "<TD height='20' width='5%' style=\"color: white;\"><b>$mess_gp_duree</b></TD>";
       echo "<TD height='20' width='7%' style=\"color: white;\"><b>$mess_gp_date_deb</b></TD>";
       echo "<TD height='20' width='9%' style=\"color: white;\"><b>$mess_gp_date_fin</b></TD>";
       echo "<TD height='20' width='9%' style=\"color: white;\"><b>$mess_gp_etat</b></TD>";
       echo "<TD height='20' width='7%' style=\"color: white;\"><b>$msq_prereq</b></TD>";
       echo "<TD height='20' colspan=2 style=\"color: white;\"><b>$mess_ag_supp</b></TD></TR>";
       if ($utilisateur)
          $num_app = $utilisateur;
       $compteur_fin_seq=0;
       $ich = 0;
       $date_deb = array();
       $date_maxi = array();
       $date_fin = array();
       $formateur = array();
       $prescripteur = array();
       $sequence = array();
       $ordre_seq='';
       while ($ich != $nb_seq)
       {
              $seq = mysql_result ($seq_query,$ich,"suiv2_seq_no");
              $type_sequence = GetDataField ($connect,"SELECT seq_type_lb from sequence where seq_cdn = '$seq'","seq_type_lb");
              $num_seq[$ich] = $seq;
              $ordre_seq = GetDataField ($connect,"select suiv2_ordre_no from suivi2$Ext where
                                                   suiv2_utilisateur_no = $num_app and
                                                   suiv2_seq_no = $seq and
                                                   suiv2_grp_no = $numero_groupe","suiv2_ordre_no");
              $ordre_s[$ich] = $ordre_seq;
              if ($ich > 0)
              {
                 $seq_precedent = $num_seq[$ich-1];
                 $ordre_precedent_seq = $ordre_s[$ich-1];
              }
              $icono = "";
              if (strstr($type_sequence,'SCORM'))
              {
                 $scormOk = 1;
                 $icono .= "<IMG SRC='images/gest_parc/scorm.gif' border='0' alt=\"$mess_seq_sco\">";
              }
              else
              {
                 $scormOk = 0;
                 $icono .= "";
              }
              $etat = mysql_result ($seq_query,$ich,"suiv2_etat_lb");
              $etat_sequence = $etat;
              $desc_seq = GetDataField ($connect,"select seq_desc_cmt from sequence where seq_cdn=$seq","seq_desc_cmt");
              $formateur[$seq] = GetDataField ($connect,"select presc_formateur_no from prescription$Ext where
                                                         presc_seq_no = $seq and presc_parc_no = $id_parc and
                                                         presc_utilisateur_no = $num_app and
                                                         presc_grp_no = $numero_groupe","presc_formateur_no");
              $nom_form=GetDataField ($connect,"select util_nom_lb from utilisateur where util_cdn='$formateur[$seq]'","util_nom_lb");
              $prenom_form=GetDataField ($connect,"select util_prenom_lb from utilisateur where util_cdn='$formateur[$seq]'","util_prenom_lb");
              $prescripteur[$seq] = GetDataField ($connect,"select presc_prescripteur_no from prescription$Ext where
                                                            presc_seq_no = $seq and presc_parc_no = $id_parc and
                                                            presc_utilisateur_no = $num_app and
                                                            presc_grp_no = $numero_groupe","presc_prescripteur_no");
              $nom_presc=GetDataField ($connect,"select util_nom_lb from utilisateur where util_cdn='$prescripteur[$seq]'","util_nom_lb");
              $prenom_presc=GetDataField ($connect,"select util_prenom_lb from utilisateur where util_cdn='$prescripteur[$seq]'","util_prenom_lb");
              if ($formateur[$seq] == $id_user || $prescripteur[$seq] == $id_user)
                 $avoir_droit++;
              $prerequis="";
              $prereq_query = mysql_query ("select * from prerequis where prereq_seq_no = $seq");
              $nb_prereq = mysql_num_rows ($prereq_query);
              if ($etat == "TERMINE")
                 $compteur_fin_seq++;
              if ($compteur_fin_seq == $nb_seq && $ich+1 == $nb_seq)
                 $fin_parc=1;
              if ($nb_prereq != 0)
              {
                $nb_proposable = 0;
                $prereq_not = 0;
                $jj = 0;
                $affiche_prereq = "<font size=2><B>$msq_prereq</B></font><BR>";// initialisation de l'affichage
                while ($jj < $nb_prereq)
                {
                  $type_condition = mysql_result ($prereq_query,$jj,"prereq_typcondition_lb");
                  if ($type_condition == 'SEQUENCE')
                  {
                      $condition = mysql_result ($prereq_query,$jj,"prereq_seqcondition_no");
                      $verif_seq_req = mysql_query ("select count(presc_seq_no) from prescription$Ext where
                                                     presc_seq_no = $condition and
                                                     presc_utilisateur_no = $num_app and presc_parc_no = $id_parc and
                                                     presc_grp_no = $numero_groupe");
                      $nb_verif_req = mysql_result($verif_seq_req,0);
                      if ($nb_verif_req > 0 )
                      {
                        $etat_seq_req = GetDataField ($connect,"select suiv2_etat_lb from suivi2$Ext where
                                                                suiv2_seq_no = $condition and
                                                                suiv2_utilisateur_no = $num_app and
                                                                suiv2_grp_no = $numero_groupe","suiv2_etat_lb");
                        $nom_seq_req = GetDataField ($connect,"select seq_titre_lb from sequence where seq_cdn = $condition","seq_titre_lb");
                        $affiche_prereq .= "<B>$msq_seq</B> : $nom_seq_req<BR>";
                        if ($etat_seq_req != 'TERMINE')
                        {
                          $motif = $mess_gp_seq_preq;
                          $proposable = 0;
                          $prerequis = "";
                        }
                        else
                          $nb_proposable++;
                      }
                      else
                          $prereq_not++;

                  }

                  if ($type_condition == 'ACTIVITE')
                  {
                      $condition = mysql_result ($prereq_query,$jj,"prereq_actcondition_no");
                      $num_seq_req = GetDataField ($connect,"select act_seq_no from activite where act_cdn = $condition","act_seq_no");
                      $verif_seq_req = mysql_query ("select count(presc_seq_no) from prescription$Ext where
                                                     presc_seq_no = $num_seq_req and
                                                     presc_utilisateur_no = $num_app and presc_parc_no = $id_parc and
                                                     presc_grp_no = $numero_groupe");
                      $nb_verif_req = mysql_result($verif_seq_req,0);
                      if ($nb_verif_req > 0 )
                      {
                        $etat_act_req = GetDataField ($connect,"select suivi_etat_lb from suivi1$Ext where
                                                                suivi_act_no = $condition and
                                                                suivi_utilisateur_no = $num_app and
                                                                suivi_grp_no = $numero_groupe","suivi_etat_lb");
                        if ($etat_act_req != 'TERMINE')
                        {
                           $nom_act_req = GetDataField ($connect,"select act_nom_lb from activite where act_cdn = $condition","act_nom_lb");
                           $nom_seq_req = GetDataField ($connect,"select seq_titre_lb from sequence,activite where act_cdn = $condition and act_seq_no= seq_cdn","seq_titre_lb");
                           $affiche_prereq .= "<B>$msq_activite</B> -->($msq_seq : $nom_seq_req) : <U>$nom_act_req</U><BR>";
                           $motif = $mess_gp_aut_act;
                           $proposable = 0;
                           $prerequis = $mess_act_prq." <B>".str_replace("'","|",$act_prereq)."</B> ".$mess_no_fin;
                        }
                        else
                          $nb_proposable++;
                      }
                      else
                          $prereq_not++;
                  }

                  if ($type_condition == 'NOTE')
                  {
                     $condition = mysql_result ($prereq_query,$jj,"prereq_actcondition_no");
                     $num_seq_req = GetDataField ($connect,"select act_seq_no from activite where act_cdn = $condition","act_seq_no");
                     $verif_seq_req = mysql_query ("select count(presc_seq_no) from prescription$Ext where
                                                    presc_seq_no = $num_seq_req and
                                                    presc_utilisateur_no = $num_app and presc_parc_no = $id_parc and
                                                    presc_grp_no = $numero_groupe");
                     $nb_verif_req = mysql_result($verif_seq_req,0);
                     if ($nb_verif_req > 0 )
                     {
                        $etat_act_req = GetDataField ($connect,"select suivi_etat_lb from suivi1$Ext where
                                                                suivi_act_no = $condition and
                                                                suivi_utilisateur_no = $num_app and
                                                                suivi_grp_no = $numero_groupe","suivi_etat_lb");
                        $nom_act_req = GetDataField ($connect,"select act_nom_lb from activite where act_cdn = $condition","act_nom_lb");
                        if ($etat_act_req != 'TERMINE')
                        {
                          $motif = $mess_gp_act_preq;
                          $proposable = 0;
                        }
                        else
                        {

                          $note_min = mysql_result ($prereq_query,$jj,"prereq_notemin_nb1");
                          $note_max = mysql_result ($prereq_query,$jj,"prereq_notemax_nb1");
                          $note_obtenue = GetDataField ($connect,"select suivi_note_nb1 from suivi1$Ext where
                                                                  suivi_act_no = $condition and
                                                                  suivi_utilisateur_no = $num_app and
                                                                suivi_grp_no = $numero_groupe" ,"suivi_note_nb1");
                          if (($note_obtenue < $note_min && is_integer($note_obtenue)) || $note_obtenue == $mess_suivi_noacq || $note_obtenue == "D" || $note_obtenue == "E")
                          {
                             $motif = $mess_gp_note;
                             $proposable = 0;
                             $prerequis = $mess_note_prq;
                             $affiche_prereq .= "<B>$msq_note</B> --> $msq_activite : $nom_act_req<BR>";
                          }
                          else
                             $nb_proposable++;
                        }
                     }else
                          $prereq_not++;
                  }
              $jj++;
              }
              if (($nb_proposable == $nb_prereq) || (($nb_proposable+$prereq_not) == $nb_prereq))
              {
                 $proposable=1;
                 $prerequis="OK";
              }
              else
                 $proposable = 0;
            }
            else 
                $proposable = 1;
              //***************************************************************************************************
              $act_query = mysql_query ("select * from activite where act_seq_no = $seq order by act_ordre_nb");
              $Nb_act_seq = mysql_num_rows ($act_query);
              $aq = 0;
              $encore = 0;
              $date_deb[$seq] = GetDataField ($connect,"select presc_datedeb_dt from prescription$Ext where
                                                        presc_seq_no = $seq and
                                                        presc_utilisateur_no = $utilisateur and
                                                        presc_grp_no = $numero_groupe","presc_datedeb_dt");
              $date_fin[$seq] = GetDataField ($connect,"select presc_datefin_dt from prescription$Ext where
                                                        presc_seq_no = $seq and presc_parc_no = $id_parc and
                                                        presc_utilisateur_no = $utilisateur and
                                                        presc_grp_no = $numero_groupe","presc_datefin_dt");
              $date_maxi[$ich] = $date_fin[$seq];
              $today = date("Y/n/d");
              $nb_date_fin_query = mysql_query ("select TO_DAYS('$date_fin[$seq]')");
              $nb_date_fin = mysql_result ($nb_date_fin_query,0);
              $nb_date_deb_query = mysql_query ("select TO_DAYS('$date_deb[$seq]')");
              $nb_date_deb = mysql_result ($nb_date_deb_query,0);
              $nb_today_query = mysql_query ("select TO_DAYS('$today')");
              $nb_today = mysql_result ($nb_today_query,0);
              for ($nn = 1;$nn < 10;$nn++)
              {
                if ($nb_date_fin == ($nb_today+$nn) && $etat != "TERMINE")
                {
                   $avertisseur = 1;
                }
              }
              $depasse=0;
              if ($nb_date_fin < $nb_today)
                 $depasse=1;
              if ($nb_date_fin < $nb_today && $etat == "TERMINE")
                 $autorise=1;
              if ($nb_date_deb <= $nb_today)
                  $visible = 1;
              else
                  $visible = 0;

              $ch_date_deb = explode ("-",$date_deb[$seq]);
              $date_deb[$seq] = "$ch_date_deb[2]/$ch_date_deb[1]/$ch_date_deb[0]";
              $nom = GetDataField ($connect,"select seq_titre_lb from sequence where seq_cdn=$seq","seq_titre_lb");
              $nom_seq = $nom;
              if ($scormOk == 1)
                 $duree_seq = GetDataField ($connect,"select seq_duree_nb from sequence where seq_cdn = '$seq'","seq_duree_nb");
              else{
                 $duree_sequence = mysql_query ("select sum(activite.act_duree_nb) from activite,suivi1$Ext where
                                                 activite.act_cdn = suivi1$Ext.suivi_act_no and
                                                 activite.act_seq_no=$seq and
                                                 suivi1$Ext.suivi_utilisateur_no =$utilisateur and
                                                 suivi1$Ext.suivi_grp_no = $numero_groupe");
                 $duree_seq = mysql_result($duree_sequence,0);
              }
              $ch_date_fin = explode ("-",$date_fin[$seq]);
              $date_fin[$seq] = "$ch_date_fin[2]/$ch_date_fin[1]/$ch_date_fin[0]";
              $ii=$ich-1;
              if (($ii/2) == ceil($ii/2))
                   echo "<TR height='30' bgcolor='#FFFFFF' id='maseq$seq'>";
              else
                   echo "<TR height='30' bgcolor='#F4F4F4' id='maseq$seq'>";
              if ($typ_user == "ADMINISTRATEUR" || (($inscripteur == $id_user && $hgrp != 1) || $crea_grp == $id_user) || $formateur[$seq] == $id_user)
              {
                 $lien = "modif_parc.php?a_faire=1&modif_ordre_seq=1&parc=$id_parc&ordre_seq=$ordre_seq&ordre_precedent=$ordre_precedent_seq&seq_actuelle=$seq&seq_precedent=$seq_precedent&utilisateur=$utilisateur&numero_groupe=$numero_groupe&hgrp=$hgrp";
                 $lien = urlencode($lien);
                 if (isset($date_maxi[$ich]) && isset($date_maxi[$ich-1]) && $date_maxi[$ich] == $date_maxi[$ich-1] && $ich > 0)
                    echo "<TD height='10' align='left'><A HREF=\"trace.php?link=$lien\" target='main' title=\"$mess_decrement\"><IMG SRC=\"images/modules/tut_form/flechehaut.gif\" ALT=\"$mess_decrement\" border='0'></TD>";
                 else
                    echo "<TD height='10'>&nbsp;</TD>";
              }
              else
                 echo "<TD height='10'>&nbsp;</TD>";
              if ($utilisateur && $typ_user == "ADMINISTRATEUR")
              {
                    if ($signal[$seq] == 1)
                      echo  "<TD><B>$icono $nom *</B></td>";
                    else
                      echo  "<TD><B>$icono $nom</B></td>";
                    echo "<TD>";
                    $param = $prescripteur[$seq];
                    Ascenseur_mult ("presc_rf[$seq]","select util_cdn,util_nom_lb,util_prenom_lb from utilisateur where (util_typutil_lb = 'RESPONSABLE_FORMATION' or util_typutil_lb = 'ADMINISTRATEUR') AND util_flag = 0 order by util_nom_lb ASC",$connect,$param);
              }
              else
              {
                    if ($signal[$seq] == 1)
                      echo  "<TD><B>$icono $nom *</B></td>";
                    else
                      echo  "<TD><B>$icono $nom</B></td>";
                    echo "<TD>$prenom_presc $nom_presc</td>";
              }
                  echo "<TD>";
                  $param = $formateur[$seq];
                  if ($utilisateur && ($typ_user == "ADMINISTRATEUR"  || ($typ_user == "RESPONSABLE_FORMATION" && ($id_user == $prescripteur[$seq] || $inscripteur == $id_user || $crea_grp == $id_user)) ))
                      Ascenseur_mult ("form_ref[$seq]","select util_cdn,util_nom_lb,util_prenom_lb from utilisateur ".
                                      "where (util_typutil_lb = 'FORMATEUR_REFERENT' or util_typutil_lb = 'RESPONSABLE_FORMATION' or ".
                                      "util_typutil_lb = 'ADMINISTRATEUR') AND util_flag = 0 order by util_nom_lb ASC",$connect,$param);
                  else
                      echo $prenom_form." ".$nom_form;
                  echo "</TD>";
                  $duree = duree_calc($duree_seq);
                  echo "<td align='left' nowrap>$duree</td><td width='7%'>";
                  if ($utilisateur && ($typ_user == "ADMINISTRATEUR"  || $id_user == $formateur[$seq] || $id_user == $prescripteur[$seq] || $crea_grp == $id_user))
                  {
                      echo "<INPUT TYPE= TEXT class='INPUT' NAME='date_debut[$seq]' id='ddt$seq' ".
                           "value='$date_deb[$seq]' MAXLENGTH='10' size='10' title=\"$cal_click\" ".
                           "onfocus=\"this.select();lcs(this)\" onclick=\"event.cancelBubble=true;this.select();lcs(this);\"  ".
                           "onChange=\"var ddeb$seq=document.getElementById('ddt$seq');".
                           "validateDate(ddeb$seq,'".$date_deb[$seq]."','ddt$seq');\"></td>";
                      $i_seq++;
                      echo "<INPUT TYPE='HIDDEN' NAME='sequence[$i_seq]' VALUE='$seq'>";
                  }
                  else
                      echo "$date_deb[$seq]</td>";
                  if ($utilisateur && ($typ_user == "ADMINISTRATEUR"  || $id_user == $formateur[$seq] || $id_user == $prescripteur[$seq] || $crea_grp == $id_user))
                     echo "<td><INPUT TYPE= TEXT class='INPUT' NAME='date_finale[$seq]' id='dfn$seq' value='$date_fin[$seq]' ".
                          "MAXLENGTH='10' size='10' title=\"$cal_click\" onfocus=\"this.select();lcs(this)\" ".
                          "onclick=\"event.cancelBubble=true;this.select();lcs(this);\" ".
                          "onChange=\"var dfin$seq=document.getElementById('dfn$seq').value;".
                          "validateDate(dfin$seq,'".$date_fin[$seq]."','dfn$seq');\"></td>";
                  else
                     echo "<td >$date_fin[$seq]</td>";
                  $etat = ucfirst(strtolower($etat));
                  if ($depasse == 1 && $etat != "Termine")
                  {
                      $etat = "<font color=red>$mess_gp_hsd</font>";
                  }
                  if ($etat == "Termine")
                     $etat = $mess_fait;
                  if ($etat != 'A faire' && $depasse != 1 && ($typ_user == 'ADMINISTRATEUR'  || $id_user == $formateur[$seq]))
                  {
                     $lien = "modif_parc.php?reinit=1&a_faire=1&hgrp=$hgrp&utilisateur=$utilisateur&scormOk=$scormOk&id_seq=$seq&id_parc=$id_parc&numero_groupe=$numero_groupe";
                     $lien = urlencode($lien);
                     $etat = "<DIV id='sequence'><A HREF=\"trace.php?link=$lien\" ".bullet($mess_reinit,"","LEFT","ABOVE",200).">$etat</A></DIV>";
                  }
                  if ($avertisseur == 1)
                  {
                    echo "<td align='left'>$etat</td>";
                    $avertisseur=0;
                  }
                  else
                     echo "<td align='left'>$etat</td>";
                  if ($nb_prereq == 0 || $prereq_not == $nb_prereq)
                    echo "<td align='middle'>$mess_gp_aucun</td>";
                  else
                  {
                    $nom_prereq = GetDataField ($connect,"select seq_titre_lb from sequence where seq_cdn=$condition","seq_titre_lb");
                    echo "<TD align='left'><A HREF=\"javascript:void(0);\" ".
                         bulle($affiche_prereq,"","LEFT","ABOVE",250)."$msq_prereq</A></TD>";
                  }
                  $prereq_query = mysql_query ("select prerequis.prereq_seqcondition_no from prerequis,suivi2$Ext where
                                                prerequis.prereq_seqcondition_no = $seq AND
                                                prerequis.prereq_seq_no = suivi2$Ext.suiv2_seq_no AND
                                                suivi2$Ext.suiv2_utilisateur_no = $utilisateur");
                  $nb_prereq = mysql_num_rows ($prereq_query);
                  $nbWiki = mysql_query("select * from wikiapp where wkapp_seq_no=$seq and
                                         wkapp_parc_no = $id_parc and wkapp_grp_no = $numero_groupe and
                                         wkapp_app_no= $utilisateur");
                  $NbWkApp = 0;
                  if (mysql_num_rows($nbWiki) > 0)
                  {
                     $item = mysql_fetch_object($nbWiki);
                     $leClan = $item->wkapp_clan_nb;
                     $NbWkApp = mysql_num_rows(mysql_query("select * from wikibodies,wikimeta where
                                            (wkbody_auteur_no = $utilisateur and wkbody_clan_no = $leClan) or
                                            wkmeta_auteur_no = $utilisateur and wkmeta_clan_no = $leClan"));
                  }

                  if ($NbWkApp == 0 && $etat_sequence == "A FAIRE" && ($typ_user == "ADMINISTRATEUR"  || $id_user == $formateur[$seq] || $id_user == $prescripteur[$seq] || $crea_grp == $id_user))
                  {
                     echo "<TD width='15' colspan='2' align='center'><div id='supp_seq$seq'>";
                    $lien = "modif_parc.php?numero_groupe=$numero_groupe&supp_seq=1&suppression=1&id_seq=$seq&id_parc=$id_parc&utilisateur=$utilisateur&hgrp=$hgrp&a_faire=1&scormOk=$scormOk";
                    $lien = urlencode($lien);
                    echo "<A HREF=\"trace.php?link=$lien\" onclick=\"return(conf());\" target='main'>
                         <IMG SRC=\"images/messagerie/icopoubelressour.gif\" height=\"20\" width=\"15\" BORDER=0 TITLE=\"$mess_ag_supp\"></A></div></TD>";

                  }
                  else
                    echo "<TD colspan=2>&nbsp;</TD>";
          echo "</TR>";
          $ich++;
          $pointeur = 0;
          $visible = 0;
       }
    if ($id_parc > 0 && ($typ_user == 'ADMINISTRATEUR' || ($typ_user == 'RESPONSABLE_FORMATION' && ($inscripteur == $id_user || $crea_grp == $id_user))))
    {
       $pp = $p+1;
       echo "<tr><td valign='top' colspan='8'><table><tbody><tr><td>";
       if ($typ_agent != 'msie')
           echo '<form action="">';
       echo "<input id='ac_me$id_parc' type='text' value='$msgajtseq' style='width: 515px;font-family:arial;font-size:10px;font-weight:normal;' onClick=\"javascript:var changer=getElementById('ac_me$id_parc');changer.value='';\"/>";
       echo nbsp(4)."<A HREF=\"javascript:void(0);\"  style='cursor:help;'".
           "onclick=\"return overlib('<TABLE border=0 cellspacing=0 cellpadding=0><TR><TD width=5></TD><TD>".addslashes($msg_ajtseq_nota)."</TD></TR></TABLE>'".
           ",STICKY,ol_hpos,LEFT,ABOVE,WIDTH,250,ol_offsetx,450,ol_offsety,350,CAPTION,'<TABLE width=100% border=0 cellspacing=0 cellpadding=0><TR width=100%>".
           "<TD align=left width=90% nowrap><B>$mess_nota_bene</B></TD></TR></TABLE>')\"".
           " onMouseOut=\"return nd();\"><IMG SRC='images/modules/tut_form/icoaide.gif' border='0'></A>";
       echo '</td></tr></tbody></table></td></tr>';
    }
  ?>
  <script type="text/javascript">
        function findValue(li) {
           if( li == null ) return alert("Aucune donnée");
           if (li.extra) {
               document.location.replace('<?php echo "modif_parc.php?a_faire=1&ajout_seq=1&numero_groupe=$numero_groupe&utilisateur=$utilisateur&id_parc="?>'+li.extra[1]+'&id_seq='+li.extra[0]);
           }
        }

        function selectItem(li)
        {
           findValue(li);
        }

        function lookupAjax()
        {
            var oSuggest = $("#ac_me<?php echo $id_parc;?>")[0].autocompleter;
            oSuggest.findValue();
            return false;
        }
        $(document).ready(function() {
           $("#ac_me<?php echo $id_parc;?>").autocomplete("prescription/ajout_seq_parc_app.php", {
           minChars:3, matchSubset:1, matchContains:1, cacheLength:1,
           onItemSelect:selectItem, selectOnly:1, id_parc:<?php echo $id_parc;?>});

        });
  </script>
  <?php
  echo"</TABLE></TD></TR></TABLE></TD></TR>";
  echo "</div>";
  if ($p < $nb_parc-1)
     echo "<TR height='20'><td colspan=10 valign='middle' align='center'></td></tr>";
  $p++;
  }
  if ($nb_parc > 0)
  {
    echo "<TR><TD colspan='10' align='center' valign='center'>";
    echo "<INPUT TYPE='HIDDEN' NAME='i_seq' VALUE='$i_seq'>";
    echo "<INPUT TYPE='HIDDEN' NAME='i_parc' VALUE='$i_parc'>";
    echo "<INPUT TYPE='HIDDEN' NAME='a_faire' VALUE='1'>";
    echo "<TABLE width='100%' border='0'><TR height='30'>";
    if ($typ_user =="ADMINISTRATEUR" || ($typ_user == "RESPONSABLE_FORMATION" && ($inscripteur == $id_user || $crea_grp == $id_user)))
    {
      $lien = "prescription.php?numero_groupe=$numero_groupe&prem=1&presc=appren&utilisateur=$utilisateur";
      $lien = urlencode($lien);
       echo "<TD colspan='9' align='right' valign='center'><A HREF=\"javascript:document.form.submit();\" onmouseover=\"img1.src='images/fiche_identite/boutvalidb.gif';return true;\" onmouseout=\"img1.src='images/fiche_identite/boutvalid.gif'\">".
        "<IMG NAME=\"img1\" SRC=\"images/fiche_identite/boutvalid.gif\" BORDER='0' onLoad=\"tempImg=new Image(0,0); tempImg.src='images/fiche_identite/boutvalidb.gif'\"></A></TD>";
    }
    elseif($typ_user =="ADMINISTRATEUR" || $avoir_droit > 0)
    {
       echo "<TD colspan='8' align='right' valign='center'><A HREF=\"javascript:document.form.submit();\" onmouseover=\"img1.src='images/fiche_identite/boutvalidb.gif';return true;\" onmouseout=\"img1.src='images/fiche_identite/boutvalid.gif'\">".
        "<IMG NAME=\"img1\" SRC=\"images/fiche_identite/boutvalid.gif\" BORDER='0' onLoad=\"tempImg=new Image(0,0); tempImg.src='images/fiche_identite/boutvalidb.gif'\"></A></TD>";
    }
    else
      echo "<TD>";
    echo "</TD></FORM></TR></TABLE>";
  }
  echo "</TD></TR></TABLE></TD></TR></TABLE>";
}
?>