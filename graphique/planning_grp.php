<?php
if (!isset($_SESSION)) session_start();
include "../include/UrlParam2PhpVar.inc.php";
require 'admin.inc.php';
require '../fonction.inc.php';
require '../fonction_html.inc.php';
require "../lang$lg.inc.php";
require ("jpgraph.php");
require ("jpgraph_gantt.php");
dbConnect();
require '../include/varGlobals.inc.php';
if ($lg == "fr")
   setlocale(LC_ALL,'fr_FR');
elseif($lg == "ru")
   setlocale(LC_ALL,'ru_RU');
elseif($lg == "en")
   setlocale(LC_ALL,'en_US');
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
$req_min = mysql_query ("select presc_datedeb_dt from
                         prescription_$id_grp,utilisateur_groupe where
                         presc_utilisateur_no = utilisateur_groupe.utilgr_utilisateur_no and
                         utilisateur_groupe.utilgr_groupe_no=$id_grp
                         order by presc_datedeb_dt asc");
$date_min = mysql_result($req_min,0,"presc_datedeb_dt");
$nb_jours_req_min = mysql_query ("select TO_DAYS('$date_min')");
$nb_jours_min = mysql_result ($nb_jours_req_min,0);
$req_max = mysql_query ("select presc_datefin_dt from
                         prescription_$id_grp,utilisateur_groupe where
                         presc_utilisateur_no = utilisateur_groupe.utilgr_utilisateur_no and
                         utilisateur_groupe.utilgr_groupe_no=$id_grp
                         order by presc_datefin_dt asc");
$nbr_max = mysql_num_rows($req_max);
$date_max = mysql_result($req_max,$nbr_max-1,"presc_datefin_dt");
$nb_jours_req_max = mysql_query ("select TO_DAYS('$date_max')");
$nb_jours_max = mysql_result ($nb_jours_req_max,0);
$nbrj = $nb_jours_max-$nb_jours_min;
$today = date("Y-m-d");
$today1 = date("d-m-Y");
if ($nbrj < 365)
   $frame_gw = 800;
else
   $frame_gw = 300+$nbrj;
$graph = new GanttGraph($frame_gw,-1,"auto",.2);
$graph->SetBox();
if ($typ_user != "APPRENANT"){
  $graph->scale->tableTitle->SetFont(FF_ARIAL,FS_BOLD);
  $graph->scale->SetTableTitleBackground("#FFFFFF");
}
$graph->SetMarginColor("#FFFFFF");
if ($nbrj <92){
   $graph->ShowHeaders(GANTT_HYEAR | GANTT_HMONTH | GANTT_HDAY | GANTT_HWEEK);//
   $graph->scale->month->SetStyle(MONTHSTYLE_LONGNAME);
}elseif($nbrj >91 && $nbrj <120){
   $graph->ShowHeaders(GANTT_HYEAR | GANTT_HMONTH  | GANTT_HWEEK);//| GANTT_HDAY
   $graph->scale->month->SetStyle(MONTHSTYLE_LONGNAME);
}else{
   $graph->ShowHeaders(GANTT_HYEAR | GANTT_HMONTH );//| GANTT_HDAY | GANTT_HWEEK
   $graph->scale->month->SetStyle(MONTHSTYLE_SHORTNAME);
}
$graph->scale->year->Setbackgroundcolor("#2b677A");
$graph->scale->year->SetFontColor("white");
$graph->scale->month->Setbackgroundcolor("#2b677A");
$graph->scale->month->SetfontColor("white");
$graph->SetMarginColor("#CEE6EC");
$liste_apprenant = mysql_query("SELECT * from utilisateur_groupe,prescription_$id_grp,utilisateur where
                                utilgr_groupe_no = '$id_grp' AND
                                presc_utilisateur_no = utilgr_utilisateur_no AND
                                utilgr_utilisateur_no = util_cdn
                                group by utilgr_utilisateur_no
                                order by util_nom_lb,util_prenom_lb ASC");
$nbre_apprenant = mysql_num_rows($liste_apprenant);
$app=0;
  while ($app < $nbre_apprenant){
      $ll= $app+1;
      $num_app = mysql_result($liste_apprenant,$app,"utilgr_utilisateur_no");
      $id_nom = mysql_result($liste_apprenant,$app,"util_nom_lb");
      $id_prenom = mysql_result($liste_apprenant,$app,"util_prenom_lb");
      $id_photo = mysql_result($liste_apprenant,$app,"util_photo_lb");
      $lien_photo="prescription.php?affiche_fiche_app=1&hgrp=$hgrp&id_util=$num_app&identite=1&stopper=1";
      if ($id_photo == '')
         $le_lien = "javascript:void(0);\" onclick=\"window.open('../$lien_photo','','scrollbars=yes,resizable=yes,width=800,height=300');";
      else
      {
         $le_lien= "javascript:void(0);\" onclick=\"window.open('../$lien_photo','','scrollbars=yes,resizable=yes,width=800,height=300')\" ".
                    photo_img($id_photo,"","");
      }
      $j = $app+1;
      $titre = $j."- ".$id_nom." ".$id_prenom;
      $nb_parc_req = mysql_query("select distinct presc_parc_no from prescription_$id_grp where
                                  presc_utilisateur_no = $num_app");
      $nb_parc = mysql_num_rows($nb_parc_req);
      $req_min = mysql_query ("select presc_datedeb_dt from prescription_$id_grp where
                               presc_utilisateur_no = $num_app order by presc_datedeb_dt asc");
      $date_deb = mysql_result($req_min,0,"presc_datedeb_dt");
      $date_deb_time = $date_deb." 00:00:01";
      $req_max = mysql_query ("select presc_datefin_dt from prescription_$id_grp where
                               presc_utilisateur_no = $num_app order by presc_datefin_dt asc");
      $nbr_max = mysql_num_rows($req_max);
      $date_fin = mysql_result($req_max,$nbr_max-1,"presc_datefin_dt");
      $date_fin_time = $date_fin." 23:59:59";
      $seq_parc_query = mysql_query ("select sum(act_duree_nb) from
                                     suivi1_$id_grp,activite,prescription_$id_grp where
                                     act_cdn = suivi_act_no AND
                                     act_seq_no = presc_seq_no AND
                                     suivi_utilisateur_no=$num_app");
      $seq_mod_query = mysql_query ("select sum(mod_duree_nb) from
                                     scorm_module,scorm_util_module_$id_grp,prescription_$id_grp where
                                     mod_cdn = mod_module_no AND
                                     mod_seq_no = presc_seq_no AND
                                     user_module_no=$num_app");
      $duree_parc = mysql_result($seq_parc_query,0);
      $duree_mod = mysql_result($seq_mod_query,0);
      $duree_act_termine = mysql_query ("select sum(act_duree_nb) from
                                         suivi1_$id_grp,activite,prescription_$id_grp where
                                         act_cdn = suivi_act_no AND
                                         act_seq_no = presc_seq_no AND
                                         suivi_utilisateur_no=$num_app and
                                         suivi_etat_lb='TERMINE'");
      $duree_mod_termine = mysql_query ("select sum(mod_duree_nb) from
                                         scorm_module,scorm_util_module_$id_grp,prescription_$id_grp where
                                         mod_cdn = mod_module_no AND
                                         mod_seq_no = presc_seq_no AND
                                         user_module_no=$num_app and
                                         (lesson_status='COMPLETED' OR
                                         lesson_status='FAILED' OR
                                         lesson_status='BROWSED' OR
                                         lesson_status='PASSED')");
      $duree_act_ter = mysql_result($duree_act_termine,0);
      $duree_mod_ter = mysql_result($duree_mod_termine,0);
      $dureeTot = $duree_act_ter + $duree_mod_ter;
      if (($duree_parc + $duree_mod) > 0)
         $result =ceil($dureeTot * 100 / ($duree_parc + $duree_mod));
      else
         $result = 0;
//      $pourcent= round($dureeTot/($duree_parc + $duree_mod),2);
      $nb_traque = 0;
      $traque_req = mysql_query ("select fiche_date_dt from fiche_suivi where fiche_utilisateur_no = $num_app and
                                 fiche_auteur_no = $num_app and fiche_grp_no=$id_grp order by fiche_date_dt desc");
      $nb_traque = mysql_num_rows($traque_req);
      if ($nb_traque > 0)
      {
         $date_traque = mysql_result($traque_req,0,"fiche_date_dt");
         $nb_jours_req = mysql_query ("select TO_DAYS('$date_traque')");
         $nb_traq = mysql_result ($nb_jours_req,0);
         $nb_jours_req = mysql_query ("select TO_DAYS('$date_deb')");
         $nb_deb = mysql_result ($nb_jours_req,0);
         $nb_jours_req = mysql_query ("select TO_DAYS('$date_fin')");
         $nb_fin = mysql_result ($nb_jours_req,0);
         if ($nb_fin > $nb_deb && $nb_traq > $nb_deb && $result > 0)
            $pourcent = round(($nb_traq - $nb_deb)/($nb_fin-$nb_deb),2);
         elseif ($result == 0)
            $pourcent = 0;
         else
            $pourcent = .02;
        //$ch_date_trq = explode("-",$date_traque);
        //$date_trq = date('Y-m-d',mktime(0,0,0,$ch_date_trq[2],intval(($ch_date_trq[1])),intval($ch_date_trq[0])));
        //$date_trq_deb = date('Y-m-d',mktime(0,0,0,$ch_date_trq[2],intval(($ch_date_trq[1]-8)),intval($ch_date_trq[0])));
      }
      else
         $pourcent = 0;
      if ($typ_user !='APPRENANT')
      {
        $lien ="bilan.php?bilan=1&utilisateur=$num_app&numero_groupe=$numero_groupe";
        $lien = urlencode($lien);
        $alt="$mess_menu_bilan $de  $id_nom $id_prenom";
        $sequence = "javascript:void(0);\" onClick=\"javascript:window.open('../trace.php?link=$lien','','scrollbars=yes,resizable=yes,width=400,height=500')\" ";
        $alt1= $mess_suite_fp;
        $bar = new GanttBar($ll,$titre,$date_deb,$date_fin,"",.5);
        //$bar->title->SetFont(FF_ARIAL,FS_NORMAL);
        $bar->title->Setcolor("#333333");
        $bar->SetShadow(true,"darkgray");
        $bar->SetPattern(BAND_RDIAG,"#7FBFCD");
        $bar->SetFillColor("#7FBFCD");
        $bar->progress->Set($pourcent);

        $ch_date_fin = explode("-",$date_fin);

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
        $bar->title->SetCSIMTarget($le_lien);
        if ($id_photo == '')
           $bar->title->SetCSIMAlt($alt1);
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
            ($typ_user == 'RESPONSABLE_FORMATION' && ($resp_grp == $id_user || $insc_app == $id_user || $presc_app > 0))) && $entantquetut != 1 && $hgrp !=1))
        {
           $bar->SetCSIMTarget($sequence);
           $bar->SetCSIMAlt($alt);
           $bar->title->SetCSIMTarget($le_lien);
           $alt1="$mess_suite_fp";
           if ($id_photo == '')
             $bar->title->SetCSIMAlt($alt1);
          $passage++;
        }
      }
      else
      {
        $bar = new GanttBar($ll,$titre,$date_deb,$date_fin,"",.5);
        //$bar->title->SetFont(FF_ARIAL,FS_NORMAL);
        $bar->title->Setcolor("#333333");
        $bar->SetShadow(true,"darkgray");
        $bar->SetPattern(BAND_RDIAG,"#7FBFCD");
        $bar->SetFillColor("#7FBFCD");
        $alt1="$mess_suite_fp";
        $bar->title->SetCSIMTarget($le_lien);
        if ($id_photo == '')
             $bar->title->SetCSIMAlt($alt1);
      }
      $graph->Add($bar);
//      $graph->Add($bar1);
    $app++;
  }
  if ($date_max > $today)
  {
    $vl = new GanttVLine($today,"$today1","#333333",2,"solid");
    $vl->SetDayOffset(0.5);
    $graph->Add($vl);
  }
$rdv_query = mysql_query ("select * from rendez_vous where
                           (rdv_creneau_nb=0 or rdv_titre_lb='Présentiel') and
                           rdv_grp_no=$id_grp and
                           rdv_tuteur_no=0 and rdv_apprenant_no=0");
$Nb_rdv = mysql_num_rows ($rdv_query);
if ($Nb_rdv !=0)
{
   $rv=0;
   while ($rv < $Nb_rdv)
   {
     $comment = mysql_result ($rdv_query,$rv,"rdv_commentaire_cmt");
     $date_event = mysql_result ($rdv_query,$rv,"rdv_date_dt");
     if ($date_event > $date_min)
     {
       $vl = new GanttVLine($date_event,"","darkred",1,'dotted');
       $vl->SetDayOffset(0.5);
       $graph->Add($vl);
     }
   $rv++;
   }
}

$graph->StrokeCSIM(basename(__FILE__));
?>