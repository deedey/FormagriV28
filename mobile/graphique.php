<?php
session_start();
if (!isset($_SESSION['IDUSER']) || $_SESSION['IDUSER'] == "")
{
  exit();
}
error_reporting(E_ALL);
$aSuperGlobal = array (
       '_GET',
       '_FILES',
       '_COOKIE',
       '_POST',
       '_COOKIE',
       '_SESSION');
foreach ($aSuperGlobal as $superGlobal)
{
       foreach ($GLOBALS[$superGlobal] as $key => $superGlobalVal)
       {
               $$key = $superGlobalVal;

       }
}
require ("../admin.inc.php");
require ("../fonction.inc.php");
require ("../fonction_html.inc.php");
require ("../langfr.inc.php");
dbConnect();
$duree_tot = array();
$duree_act_tot = array();
$nb_tot = array();
$nb_act_tot = array();
$duree_tot = array();
$duree_act_tot = array();
$affiche='';
$compteur=0;
$req_grp = mysql_query("select grp_cdn, grp_nom_lb from utilisateur_groupe,groupe WHERE
                        utilgr_utilisateur_no = ".$_SESSION['IDUSER']." AND
                        utilgr_groupe_no = grp_cdn ORDER by grp_nom_lb");
$nomb_grp = mysql_num_rows($req_grp);
///calcul du nom d'activités
if ($nomb_grp > 0)
{
  $affiche .= "<div style='margin:5px auto;padding:4px;font-size:14px;font-weight:bold;".
              "border:1px solid #aaa;background-color: #fefefe;max-width:96%;width:auto;'>".
              "<span style='padding-left:15px;'>Etat actuel des formations suivies</span>";
  while ($itemGrp = mysql_fetch_object($req_grp))
  {
    $compteur++;
    $id_grp = $itemGrp->grp_cdn;
    $num_app = $_SESSION['IDUSER'];
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
             if ($sco->mod_maxtimeallowed != '')
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
             if ($sco->mod_maxtimeallowed != '')
               $duree_mod_ter += agrege_time($sco->mod_maxtimeallowed);
         }
      }
      $duree_act_ter = mysql_result($duree_act_termine,0);

      $duree_tot[$compteur] = $duree_act + $duree_mod;
      $duree_act_tot[$compteur] = $duree_act_ter + $duree_mod_ter;
      if ( $compteur == 1)
         $la_reference = $duree_tot[$compteur];
      if ($duree_tot[$compteur] > $la_reference && $compteur > 1)
         $la_reference = $duree_tot[$compteur];
         //
      $nb_parc_req = mysql_query("select distinct presc_parc_no from prescription_$id_grp where
                                  presc_utilisateur_no = $num_app");
      $nb_parc = mysql_num_rows($nb_parc_req);
      $req_min = mysql_query ("select presc_datedeb_dt from prescription_$id_grp where
                               presc_utilisateur_no = $num_app order by presc_datedeb_dt asc");
      if ($req_min > 0)
      {
          if ($nb_tot[$compteur] > 0)
          {
              $date_deb = mysql_result($req_min,0,"presc_datedeb_dt");
              $nb_jours_deb = mysql_query ("select TO_DAYS('$date_deb')");
              $nb_deb = mysql_result ($nb_jours_deb,0);
              /// Calcul pour le nombre
              $pourcentNb = ceil($nb_act_tot[$compteur]*100/$nb_tot[$compteur]);
              $largeurNb = ceil($nb_tot[$compteur] * 220/$la_ref_nb);

          }
          //// Calcul pour la duree
          if ($duree_tot[$compteur] > 0)
          {
              $pourcent= ceil($duree_act_tot[$compteur]*100/$duree_tot[$compteur]);
              $largeur = ceil($duree_tot[$compteur] * 220/$la_reference);
          }
          $fond = ($compteur/2 == intval($compteur/2)) ? '#eee' : '#eed';
          $affiche .="<div style='margin:2px 0 0 10px;border:1px solid #bbb;background-color: $fond ;max-width:90%;'>".
                     "<span style='padding-left:5px;font-size:12px;font-weight:bold;'> Formation : \"".
                     $itemGrp->grp_nom_lb."\"</span>";
          if ((isset($nb_tot[$compteur]) && $nb_tot[$compteur] == 0) || !isset($nb_tot[$compteur]))
             $affiche .= "<div id='graphe' style='font-weight: bold;'>$msgrph_ActNo</div>";
          else
             $affiche .= "<div id='graphe' style='font-weight: bold;border: 0px solid #24677A; width: auto;'>".
                         "<div id='nb' style='font-weight: bold;'>".
                         "<img src='image_create.php?graphe=1&pourcent=$pourcentNb&largeur=$largeurNb&obj=duree' border='0'>".
                         "<span style='font-size:11px;font-weight:normal;'>".
                         "&nbsp;".$nb_act_tot[$compteur]."  /  ".$nb_tot[$compteur]." activités</span></div>";
          if ($duree_tot[$compteur] > 0)
             $affiche .= "<div id='dur' style='font-weight: bold;'>".
                         "<img src='image_create.php?graphe=1&pourcent=$pourcent&largeur=$largeur&obj=nb' border='0'>".
                         "<span style='font-size:11px;font-weight:normal;'>".
                         "&nbsp;".duree_calc($duree_act_tot[$compteur])."  /  ".duree_calc($duree_tot[$compteur])."</span></div></div>";
          else
             $affiche .= "<div id='dur' style='font-weight: bold;'>$msgrph_dureeNo</div></div>";
          $affiche .="</div>";
      }
   }
   $affiche .="</div>";
}
else
   $affiche.= "Vous n'êtes inscrit(e) à aucune formation à ce jour";

echo utf2Charset($affiche,'iso-8859-1');
?>
