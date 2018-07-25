<?php
if (!isset($_SESSION)) session_start();
if (!isset($_SESSION['id_user']) | $_SESSION['id_user'] == "")
{
  exit();
}
include "../include/UrlParam2PhpVar.inc.php";
require "admin.inc.php";
require '../fonction.inc.php';
require '../fonction_html.inc.php';
require '../langues/graphique.inc.php';
require "../lang$lg.inc.php";
dbConnect();
include ('../style.inc.php');
$req = requete("*","groupe","grp_cdn = $numero_groupe");
if ($req == TRUE)
{
    while ($itemG = mysql_fetch_object($req))
    {
          $tut_grp = $itemG->grp_tuteur_no;
          $resp_grp = $itemG->grp_resp_no;
          $nom_grp = $itemG->grp_nom_lb;
          $comment_grp = $itemG->grp_commentaire_cmt;
    }
}
if ($typ_user == "APPRENANT")
{
  $titre_page = $msgrph_Mavanti." : ".$nom_grp;
  $num_app = $id_user;
  $lien = "../lancement.php?plan=plan_parc&planning=$planning";
}
else
{
  $titre_page = $msgrph_avanti." : ".$nom_grp;
  $num_app = $utilisateur;
  $lien = "../lancement.php?numero_groupe=$id_grp&id_grp=$id_grp&groupe=$groupe&hgrp=$hgrp&planning=mercure&entantqueobs=$entantqueobs&entantquetut=$entantquetut";
}
$affiche = "<CENTER><TABLE bgColor='#298CA0' cellspacing='2' cellpadding='0' width='98%'><TR><TD>";
$affiche .= "<TABLE bgColor='#FFFFFF' cellspacing='1' cellpadding='0' width='100%'>";
$affiche .= "<TR><TD background=\"../images/fond_titre_table.jpg\" colspan='2' height='36' align='center' valign='center'>".
            "<Font size='3' color='#FFFFFF'><B>$titre_page</B></FONT></TD></TR>";
$affiche .= "<TR><TD><TABLE bgColor='#FFFFFF' cellspacing='1' cellpadding='6' width='100%'>";
$affiche .= "<TR><TD colspan='2' valign='center'><div id='other' style=\"float:left;padding-right:8px;\">".
            "<A href=\"$lien\" target='main' class='bouton_new'>$mess_menu_planning_grp $nom_grp</A></div>".
            aide_div("planning_formateur",0,0,0,0)."</td>";
$affiche .= "</tr>";
$liste_apprenant = mysql_query("SELECT * from utilisateur_groupe,prescription_$id_grp,utilisateur where
                                utilgr_groupe_no = '$id_grp' and
                                presc_utilisateur_no = utilgr_utilisateur_no AND
                                utilgr_utilisateur_no = utilisateur.util_cdn
                                group by utilgr_utilisateur_no
                                order by util_nom_lb,util_prenom_lb ASC");
$nbre_apprenant = mysql_num_rows($liste_apprenant);
$duree_tot = array();
$duree_act_tot = array();
while ($item = mysql_fetch_object($liste_apprenant))
{
      $num_app = $item->utilgr_utilisateur_no;
      $compteur++;
      ///calcul du nom d'activités
      $nb_act_query = mysql_query ("select act_cdn from
                                     suivi1_$id_grp,activite,prescription_$id_grp where
                                     act_cdn = suivi_act_no AND
                                     act_seq_no = presc_seq_no and
                                     presc_utilisateur_no=$num_app and
                                     suivi_utilisateur_no=$num_app");
      $nb_mod_query = mysql_query ("select mod_cdn from
                                     scorm_module,scorm_util_module_$id_grp,prescription_$id_grp where
                                     mod_cdn = mod_module_no AND
                                     mod_content_type_lb != 'LABEL' AND
                                     mod_seq_no = presc_seq_no and
                                     presc_utilisateur_no=$num_app and
                                     user_module_no=$num_app");
      $nb_act = mysql_num_rows($nb_act_query);
      $nb_mod = mysql_num_rows($nb_mod_query);

      $nb_act_termine = mysql_query ("select act_cdn from
                                         suivi1_$id_grp,activite,prescription_$id_grp where
                                         act_cdn = suivi_act_no and
                                         act_seq_no = presc_seq_no and
                                         presc_utilisateur_no=$num_app and
                                         suivi_utilisateur_no=$num_app and
                                         suivi_etat_lb='TERMINE'");
      $nb_mod_termine = mysql_query ("select mod_cdn from
                                         scorm_module,scorm_util_module_$id_grp,prescription_$id_grp where
                                         mod_cdn = mod_module_no AND
                                         mod_seq_no = presc_seq_no AND
                                         mod_content_type_lb != 'LABEL' AND
                                         presc_utilisateur_no=$num_app and
                                         user_module_no=$num_app and
                                         (lesson_status='COMPLETED' OR
                                         lesson_status='FAILED' OR
                                         lesson_status='BROWSED' OR
                                         lesson_status='PASSED')");
      $nb_act_ter = mysql_num_rows($nb_act_termine);
      $nb_mod_ter = mysql_num_rows($nb_mod_termine);

      $nb_tot[$compteur] = $nb_act + $nb_mod;
      $nb_act_tot[$compteur] = $nb_act_ter + $nb_mod_ter;
      if ( $compteur == 1)
         $la_ref_nb = $nb_tot[$compteur];
      if ($nb_tot[$compteur] > $la_ref_nb && $compteur > 1)
         $la_ref_nb = $nb_tot[$compteur];
      /// calcul des durees totales
      $duree_act_query = mysql_query ("select sum(act_duree_nb) from
                                     suivi1_$id_grp,activite,prescription_$id_grp where
                                     act_cdn = suivi_act_no AND
                                     act_seq_no = presc_seq_no and
                                     presc_utilisateur_no=$num_app and
                                     suivi_utilisateur_no=$num_app");
      $duree_mod_query = mysql_query ("select mod_maxtimeallowed from
                                     scorm_module,scorm_util_module_$id_grp,prescription_$id_grp where
                                     mod_cdn = mod_module_no AND
                                     mod_content_type_lb != 'LABEL' AND
                                     mod_seq_no = presc_seq_no and
                                     presc_utilisateur_no=$num_app and
                                     user_module_no=$num_app");
      $duree_act = mysql_result($duree_act_query,0);
      if ($duree_mod_query == TRUE)
      {
         $duree_mod = 0;
         while ($sco = mysql_fetch_object($duree_mod_query))
         {
             $duree_mod += agrege_time($sco->mod_maxtimeallowed);
         }
      }
      /// calcul des durees terminees
      $duree_act_termine = mysql_query ("select sum(act_duree_nb) from
                                         suivi1_$id_grp,activite,prescription_$id_grp where
                                         act_cdn = suivi_act_no and
                                         act_seq_no = presc_seq_no AND
                                         presc_utilisateur_no=$num_app and
                                         suivi_utilisateur_no=$num_app and
                                         suivi_etat_lb='TERMINE'");
      $duree_mod_termine = mysql_query ("select mod_maxtimeallowed from
                                         scorm_module,scorm_util_module_$id_grp,prescription_$id_grp where
                                         mod_cdn = mod_module_no AND
                                         mod_seq_no = presc_seq_no AND
                                         mod_content_type_lb != 'LABEL' AND
                                         presc_utilisateur_no=$num_app and
                                         user_module_no=$num_app and
                                         (lesson_status='COMPLETED' OR
                                         lesson_status='FAILED' OR
                                         lesson_status='BROWSED' OR
                                         lesson_status='PASSED')");
      if ($duree_mod_termine == TRUE)
      {
         $duree_mod_ter = 0;
         while ($sco = mysql_fetch_object($duree_mod_termine))
         {
             $duree_mod_ter += agrege_time($sco->mod_maxtimeallowed);
         }
      }
      $duree_act_ter = mysql_result($duree_act_termine,0);
      $duree_mod = (isset($duree_mod)) ? $duree_mod : 0;
      $duree_mod_ter = (isset($duree_mod_ter)) ? $duree_mod_ter : 0;
      $duree_tot[$compteur] = $duree_act + $duree_mod;
      $duree_act_tot[$compteur] = $duree_act_ter + $duree_mod_ter;
      if ( $compteur == 1)
         $la_reference = $duree_tot[$compteur];
      if ($duree_tot[$compteur] > $la_reference && $compteur > 1)
         $la_reference = $duree_tot[$compteur];
}

$app=0;
while ($app < $nbre_apprenant)
{
      $ll = $app+1;
      $affiche .= "\n".couleur_tr($ll,40);
      $num_app = mysql_result($liste_apprenant,$app,"utilgr_utilisateur_no");
      $id_nom = GetDataField ($connect,"select util_nom_lb  from utilisateur where util_cdn = $num_app","util_nom_lb");
      $id_prenom = GetDataField ($connect,"select util_prenom_lb  from utilisateur where util_cdn = $num_app","util_prenom_lb");
      $id_photo = GetDataField ($connect,"SELECT util_photo_lb from utilisateur where util_cdn = $num_app","util_photo_lb");
      $lien_photo="prescription.php?affiche_fiche_app=1&hgrp=$hgrp&id_util=$num_app&identite=1&stopper=1";
      if ($id_photo == '')
         $le_lien = "javascript:window.open('../$lien_photo','','scrollbars=yes,resizable=yes,width=800,height=300')";
      else
      {
         $le_lien= "javascript:window.open('../$lien_photo','','scrollbars=yes,resizable=yes,width=800,height=300')\" ".
                    photo_img("$id_photo","","");
      }
      $j = $app+1;
      $titre = $j."- ".$id_nom." ".$id_prenom;
      $nb_parc_req = mysql_query("select distinct presc_parc_no from prescription_$id_grp where
                                  presc_utilisateur_no = $num_app");
      $nb_parc = mysql_num_rows($nb_parc_req);
      $req_min = mysql_query ("select presc_datedeb_dt from prescription_$id_grp where
                               presc_utilisateur_no = $num_app order by presc_datedeb_dt asc");
      if ($req_min > 0)
      {
          if ($nb_tot[$ll] > 0)
          {
              $date_deb = mysql_result($req_min,0,"presc_datedeb_dt");
              $nb_jours_deb = mysql_query ("select TO_DAYS('$date_deb')");
              $nb_deb = mysql_result ($nb_jours_deb,0);
              $ch_date_min = explode("-",$date_min);
              /// Calcul pour le nombre
              $pourcentNb = ceil($nb_act_tot[$ll]*100/$nb_tot[$ll]);
              $largeurNb = ceil($nb_tot[$ll] * 600/$la_ref_nb);
          }
          //// Calcul pour la duree
          if ($duree_tot[$ll] > 0)
          {
              $pourcent= ceil($duree_act_tot[$ll]*100/$duree_tot[$ll]);
              $largeur = ceil($duree_tot[$ll] * 600/$la_reference);
          }
          if ($typ_user !='APPRENANT')
          {
             $lien = "gest_frm_rappel1.php?utilisateur=$num_app&a_faire=1&le_groupe=$id_grp&id_grp=$id_grp&numero_groupe=$id_grp&graph=1&tout=1&vient_de_grp=1";
             $lien = urlencode($lien);
             $alt = "$mess_lanc_mess1 $id_nom $id_prenom<br />$mess_auth_click $mess_lanc_mess2";
             $sequence ="../trace.php?link=$lien\" ".bullet($alt,'','CENTER','ABOVE','240')." target=\"main";
             $alt1="$mess_suite_fp";
             $insc_app = GetDataField ($connect,"select util_auteur_no from utilisateur where util_cdn = $num_app","util_auteur_no");
             $presc_req_app = mysql_query ("select count(presc_cdn) from
                                       prescription_$id_grp where presc_utilisateur_no = $num_app AND
                                       presc_prescripteur_no =$id_user");
             $presc_app = mysql_result($presc_req_app,0);
             $tut_appreq = mysql_query ("select count(tut_cdn) from tuteur where
                                    tut_tuteur_no = $id_user AND tut_apprenant_no = $num_app");
             $tut_app = mysql_result($tut_appreq,0);
             $form_appreq = mysql_query ("select count(presc_cdn) from prescription_$id_grp where
                                     presc_formateur_no = $id_user AND
                                     presc_utilisateur_no = $num_app");
             $form_app = mysql_result($form_appreq,0);
             if ($id_photo == '')
                 $affiche .= "\n"."<td class='sequence' valign='top' nowrap><div style='float:left;'>".
                             "<a href=\"javascript:void(0);\" style=\"cursor:help;\" title=\"$alt1\" ".
                             "onclick=\"$le_lien\"> $titre </a></div>";
             else
                 $affiche .= "\n"."<td class='sequence' valign='top' nowrap><div style='float:left;'>".
                             "<a href=\"$le_lien\" style=\"cursor:help;\"> $titre </a></div>";
             $affiche .= msgInst($num_app,"apprenant car il est connecté")."</td>";
             if ((($typ_user == 'TUTEUR'  && $tut_app > 0) ||
                (($typ_user == 'FORMATEUR_REFERENT' || $typ_user == 'RESPONSABLE_FORMATION' || $typ_user == 'ADMINISTRATEUR') &&
                $entantquetut == 1 && $tut_app > 0) || ($entantqueobs == 1 && $tut_grp == $id_user && $hgrp != 1 && $entantquepresc !=1) ||
                ($entantquetut != 1 && $entantquepresc == 1 && $presc_app > 0 && $hgrp != 1)) ||
                ((($typ_user == 'ADMINISTRATEUR' || $typ_user == 'RESPONSABLE_FORMATION') &&
                $entantquetut != 1 && (($resp_grp == $id_user && $hgrp != 1 && $entantquepresc != 1) ||
                ($form_app > 0 && $hgrp == 1))) || ($typ_user == 'FORMATEUR_REFERENT' && $form_app > 0) ||
                ($tut_grp == $id_user && $entantqueobs == 1 && $hgrp != 1 && $entantquepresc != 1) ||
                ($entantquetut !=1 && $hgrp != 1 && $entantquepresc == 1 && $presc_app > 0)) ||
                (($typ_user == 'ADMINISTRATEUR' ||
                ($typ_user == 'RESPONSABLE_FORMATION' && ($resp_grp == $id_user ||
                    $insc_app == $id_user || $presc_app > 0))) && $entantquetut != 1 && $hgrp !=1))
             {
                if ((isset($nb_tot[$ll]) && $nb_tot[$ll] == 0) || !isset($nb_tot[$ll]))
                     $affiche .= "<td valign='middle'><div id='graphe' style=\"font-weight: bold;\">$msgrph_ActNo</div>";
                else
                     $affiche .= "\n"."<td valign='middle'>".
                                 "<div id='graphe' style=\"font-weight: bold;border: 0px solid #24677A; width: auto;\">".
                                 "<div id='nb' style=\"font-weight: bold;\"><a href=\"$sequence\">".
                                 "<img src=\"../image_create.php?graphe=1&pourcent=$pourcentNb&largeur=$largeurNb&obj=nb\" border='0'>".
                                 "</a>&nbsp;".$nb_act_tot[$ll]."  /  ".$nb_tot[$ll]." activités</div>";
                if ($duree_tot[$ll] > 0)
                     $affiche .= "\n"."<div id='dur' style=\"font-weight: bold;\"><a href=\"$sequence\">
                                 <img src=\"../image_create.php?graphe=1&pourcent=$pourcent&largeur=$largeur&obj=duree\" border='0'>
                                 </a>&nbsp;".duree_calc($duree_act_tot[$ll])."  /  ".duree_calc($duree_tot[$ll])."</div></td></tr>";
                else
                     $affiche .= "<div id='dur' style=\"font-weight: bold;\">$msgrph_dureeNo</div></div></td></tr>";
                 $passage++;
             }
             else
             {
                if ((isset($nb_tot[$ll]) && $nb_tot[$ll] == 0) || !isset($nb_tot[$ll]))
                     $affiche .= "<td valign='middle'><div id='graphe' style=\"font-weight: bold;\">$msgrph_ActNo</div>";
                else
                     $affiche .= "\n"."<td valign='middle'>".
                                 "<div id='graphe' style=\"font-weight: bold;border: 0px solid #24677A; width: auto;\">".
                                 "<div id='nb' style=\"font-weight: bold;\">".
                                 "<img src=\"../image_create.php?graphe=1&pourcent=$pourcentNb&largeur=$largeurNb&obj=nb\" border='0'>".
                                 "&nbsp;".$nb_act_tot[$ll]."  /  ".$nb_tot[$ll]." activités</div>";
                 if ($duree_tot[$ll] > 0)
                     $affiche .= "\n"."<div id='dur' style=\"font-weight: bold;\">".
                                 "<img src=\"../image_create.php?graphe=1&pourcent=$pourcent&largeur=$largeur&obj=duree\" border='0'>".
                                 "&nbsp;".duree_calc($duree_act_tot[$ll])."  /  ".duree_calc($duree_tot[$ll]).
                                 "</div></td></tr>";
                 else
                     $affiche .= "<div id='dur' style=\"font-weight: bold;\">$msgrph_dureeNo</div></div></td></tr>";
             }
          }
          else
              $alt1="$mess_suite_fp";
      }
  $app++;
}
$affiche .= boutret_sd(1,'')."<td>".
            "<div id='comment' style=\"font-weight: normal;border: 1px solid #24677A; padding:4px;margin-left:100px; ".
            "text-align : left; width: 280px;\">$msgrph_av_cmt</div></td></tr>";
$affiche .= "</table>".fin_tableau('');
echo $affiche;
?>
