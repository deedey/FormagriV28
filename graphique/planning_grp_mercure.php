<?php
if (!isset($_SESSION)) session_start();
require '../fonction.inc.php';
require "../lang$lg.inc.php";
dbConnect();
include ("jpgraph.php");
include ("jpgraph_gantt.php");
include '../include/varGlobals.inc.php';
if ($lg == "fr")
   setlocale(LC_ALL,'fr_FR');
elseif($lg == "ru")
   setlocale(LC_ALL,'ru_RU');
elseif($lg == "en")
   setlocale(LC_ALL,'en_US');
$tut_grp = GetDataField ($connect,"select grp_tuteur_no from groupe where grp_cdn = $numero_groupe","grp_tuteur_no");
$resp_grp = GetDataField ($connect,"select grp_resp_no from groupe where grp_cdn = $numero_groupe","grp_resp_no");
$nom_grp = GetDataField ($connect,"select grp_nom_lb from groupe where grp_cdn  = $id_grp","grp_nom_lb");
$comment_grp = GetDataField ($connect,"select grp_commentaire_cmt from groupe where grp_cdn  = $id_grp","grp_commentaire_cmt");

$req_min = mysql_query ("select prescription.presc_datedeb_dt from prescription,utilisateur_groupe where prescription.presc_utilisateur_no = utilisateur_groupe.utilgr_utilisateur_no and utilisateur_groupe.utilgr_groupe_no=$id_grp and prescription.presc_grp_no=$id_grp order by prescription.presc_datedeb_dt asc");
$date_min = mysql_result($req_min,0,"presc_datedeb_dt");
$nb_jours_req_min = mysql_query ("select TO_DAYS('$date_min')");
$nb_jours_min = mysql_result ($nb_jours_req_min,0);
$req_max = mysql_query ("select prescription.presc_datefin_dt from prescription,utilisateur_groupe where prescription.presc_utilisateur_no = utilisateur_groupe.utilgr_utilisateur_no and utilisateur_groupe.utilgr_groupe_no=$id_grp and prescription.presc_grp_no=$id_grp order by prescription.presc_datefin_dt asc");
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
//$graph = new GanttGraph(800,-1,"auto",.2);
//$graph->SetShadow();
$graph->SetBox();
  $graph->title->Set("$mess_pg_tit3 $nom_grp");
  $graph->title->SetFont(FF_ARIAL,FS_BOLD,10);
  $graph->subtitle->SetFont(FF_ARIAL,FS_NORMAL,10);
  $graph->subtitle->SetColor("darkred");
  $graph->title->SetColor("darkred");
  $graph->scale->tableTitle->Set("$mess_formation $nom_grp");
  $graph->scale->tableTitle->SetFont(FF_FONT1,FS_BOLD);
  $graph->scale->SetTableTitleBackground("#FFFFD4");
  $graph->SetMarginColor("#CEE6EC");
if ($nbrj <100){
   $graph->ShowHeaders(GANTT_HYEAR | GANTT_HMONTH | GANTT_HDAY | GANTT_HWEEK);//
   $graph->scale->month->SetStyle(MONTHSTYLE_LONGNAME);
}else{
   $graph->ShowHeaders(GANTT_HYEAR | GANTT_HMONTH );//| GANTT_HDAY | GANTT_HWEEK
   $graph->scale->month->SetStyle(MONTHSTYLE_SHORTNAME);
}
$graph->scale->divider->SetWeight(1);
$graph->scale->divider->SetColor("white");
$graph->scale->dividerh->SetWeight(1);
$graph->scale->dividerh->SetColor("white");
$graph->scale->year->grid->SetColor("white");
$graph->scale->month->grid->SetColor("white");
$graph->scale->week->grid->SetColor("white");
$graph->scale->day->grid->SetColor("white");
$graph->scale->year->Setbackgroundcolor("white");
$graph->scale->year->SetfontColor("white");
$graph->scale->month->Setbackgroundcolor("white");
$graph->scale->month->SetfontColor("white");
$graph->scale->week->Setbackgroundcolor("white");
$graph->scale->week->SetfontColor("white");
$graph->scale->day->Setbackgroundcolor("white");
$graph->scale->day->SetfontColor("white");
$graph->scale->day->SetWeekendColor("white");
$graph->scale->day->SetSundayFontColor("white");
$liste_apprenant = mysql_query("SELECT * from utilisateur_groupe,prescription,utilisateur where utilisateur_groupe.utilgr_groupe_no = '$id_grp' and prescription.presc_utilisateur_no = utilisateur_groupe.utilgr_utilisateur_no AND utilisateur_groupe.utilgr_utilisateur_no = utilisateur.util_cdn group by utilisateur_groupe.utilgr_utilisateur_no order by utilisateur.util_nom_lb,utilisateur.util_prenom_lb ASC");
$nbre_apprenant = mysql_num_rows($liste_apprenant);
$app=0;
  while ($app < $nbre_apprenant){
      $ll= $app+1;
      $num_app = mysql_result($liste_apprenant,$app,"utilgr_utilisateur_no");
      $id_nom = GetDataField ($connect,"select util_nom_lb  from utilisateur where util_cdn = $num_app","util_nom_lb");
      $id_prenom = GetDataField ($connect,"select util_prenom_lb  from utilisateur where util_cdn = $num_app","util_prenom_lb");
      $id_photo = GetDataField ($connect,"SELECT util_photo_lb from utilisateur where util_cdn = $num_app","util_photo_lb");
      $lien_photo="prescription.php?affiche_fiche_app=1&hgrp=$hgrp&id_util=$num_app&identite=1&stopper=1";
      if ($id_photo == '')
         $le_lien = "javascript:window.open('../$lien_photo','','scrollbars=yes,resizable=yes,width=800,height=300')";
      else{
         list($w_img, $h_img, $type_img, $attr_img) = getimagesize("../images/$id_photo");
         $le_lien= "javascript:window.open('../$lien_photo','','scrollbars=yes,resizable=yes,width=800,height=300')\" ".
              "onMouseOver=\"overlib('', ABOVE, TEXTCOLOR, '#ffffff', TEXTSIZE, 2, WIDTH, $w_img, HEIGHT, $h_img, BACKGROUND,".
              " '../images/$id_photo', PADX, 60, 20, PADY, 20, 20)\" onMouseOut=\"nd()";
      }
      $j = $app+1;
      $titre = $j."- ".$id_nom." ".$id_prenom;
      $nb_parc_req = mysql_query("select distinct presc_parc_no from prescription where
                                  presc_utilisateur_no = $num_app and presc_grp_no = $id_grp");
      $nb_parc = mysql_num_rows($nb_parc_req);
      $req_min = mysql_query ("select presc_datedeb_dt from prescription where presc_utilisateur_no = $num_app and presc_grp_no = $id_grp order by presc_datedeb_dt asc");
      $date_deb = mysql_result($req_min,0,"presc_datedeb_dt");
      $nb_jours_deb = mysql_query ("select TO_DAYS('$date_deb')");
      $nb_deb = mysql_result ($nb_jours_deb,0);
      $ch_date_min = split("-",$date_min);
      $act_nb = mysql_result(mysql_query ("select count(suivi_cdn) from suivi1 where suivi_grp_no=$id_grp and suivi_utilisateur_no=$num_app"),0);
      $mod_nb = mysql_result(mysql_query ("select count(mod_module_no) from scorm_util_module where mod_grp_no=$id_grp and user_module_no=$num_app"),0);
      $nbactif = $act_nb + $mod_nb;
      $date_fin = date('Y-m-d',mktime(0,0,0,$ch_date_min[1],($ch_date_min[2]+$nbactif),$ch_date_min[0]));
      $date_deb = $date_min;
      $seq_parc_query = mysql_query ("select sum(activite.act_duree_nb) from
                                     suivi1,activite,prescription where
                                     activite.act_cdn = suivi1.suivi_act_no AND
                                     activite.act_seq_no = prescription.presc_seq_no AND
                                     prescription.presc_grp_no=$id_grp and
                                     suivi1.suivi_utilisateur_no=$num_app");
      $seq_mod_query = mysql_query ("select sum(scorm_module.mod_duree_nb) from
                                     scorm_module,scorm_util_module,prescription where
                                     scorm_module.mod_cdn = scorm_util_module.mod_module_no AND
                                     scorm_module.mod_seq_no = prescription.presc_seq_no AND
                                     prescription.presc_grp_no=$id_grp and
                                     scorm_util_module.user_module_no=$num_app");
      $duree_parc = mysql_result($seq_parc_query,0);
      $duree_mod = mysql_result($seq_mod_query,0);
      $duree_act_termine = mysql_query ("select sum(activite.act_duree_nb) from
                                         suivi1,activite,prescription where
                                         activite.act_cdn = suivi1.suivi_act_no AND
                                         activite.act_seq_no = prescription.presc_seq_no AND
                                         prescription.presc_grp_no=$id_grp AND
                                         suivi1.suivi_utilisateur_no=$num_app and
                                         suivi1.suivi_etat_lb='TERMINE'");
      $duree_mod_termine = mysql_query ("select sum(scorm_module.mod_duree_nb) from
                                         scorm_module,scorm_util_module,prescription where
                                         scorm_module.mod_cdn = scorm_util_module.mod_module_no AND
                                         scorm_module.mod_seq_no = prescription.presc_seq_no AND
                                         prescription.presc_grp_no=$id_grp AND
                                         scorm_util_module.user_module_no=$num_app and
                                         (scorm_util_module.lesson_status='COMPLETED' OR
                                         scorm_util_module.lesson_status='FAILED' OR
                                         scorm_util_module.lesson_status='BROWSED' OR
                                         scorm_util_module.lesson_status='PASSED')");
      $duree_act_ter = mysql_result($duree_act_termine,0);
      $duree_mod_ter = mysql_result($duree_mod_termine,0);
      $result =ceil(($duree_act_ter + $duree_mod_ter) * 100 / ($duree_parc + $duree_mod));
      $pourcent= round(($duree_act_ter + $duree_mod_ter)/($duree_parc + $duree_mod),2);
      if ($typ_user !='APPRENANT'){
        $lien ="lancement.php?numero_groupe=$numero_groupe&hgrp=$hgrp&utilisateur=$num_app&plan=plan_parc";
        $lien = urlencode($lien);
        $alt="$nb_parc ".addslashes($mess_ca_der2)." ".addslashes($id_prenom)." ".addslashes($id_nom);
        $sequence ="../trace.php?link=$lien\" onMouseOver=\"overlib('$alt',ol_hpos,CENTER,ABOVE,WIDTH,'240',DELAY,'200',CAPTION, '')\"  onMouseOut=\"nd();\" target=\"main";
        $alt1="$mess_suite_fp";
      }
      $bar = new GanttBar($app,$titre,$date_deb,$date_fin,"$result%",.5);
      $bar->title->SetFont(FF_ARIAL,FS_NORMAL);
      $bar->title->Setcolor("#333333");
      $bar->SetShadow(true,"darkgray");
      $bar->SetPattern(BAND_RDIAG,"#7FBFCD");
      $bar->SetFillColor("#7FBFCD");
      $bar->progress->Set($pourcent);
      if ($typ_user !='APPRENANT'){
        $insc_app = GetDataField ($connect,"select util_auteur_no from utilisateur where util_cdn = $num_app","util_auteur_no");
        $presc_req_app = mysql_query ("select count(presc_cdn) from
                                       prescription where presc_utilisateur_no = $num_app AND
                                       presc_prescripteur_no =$id_user AND
                                       presc_grp_no=$id_grp");
        $presc_app = mysql_result($presc_req_app,0);
        $tut_appreq = mysql_query ("select count(tut_cdn) from tuteur where
                                    tut_tuteur_no = $id_user AND tut_apprenant_no = $num_app");
        $tut_app = mysql_result($tut_appreq,0);
        $form_appreq = mysql_query ("select count(presc_cdn) from prescription where
                                     presc_formateur_no = $id_user AND
                                     presc_utilisateur_no = $num_app AND
                                     presc_grp_no = $id_grp");
        $form_app = mysql_result($form_appreq,0);
        $bar->title->SetCSIMTarget($le_lien);
        if ($id_photo == '')
           $bar->title->SetCSIMAlt($alt1);
        if ((($typ_user == "TUTEUR"  && $tut_app > 0) || (($typ_user == "FORMATEUR_REFERENT" || $typ_user == "RESPONSABLE_FORMATION" || $typ_user == "ADMINISTRATEUR") && $entantquetut == 1 && $tut_app > 0) || ($entantquetut != 1 && $tut_grp == $id_user && $hgrp != 1 && $entantquepresc !=1) || ($entantquetut != 1 && $entantquepresc == 1 && $presc_app > 0 && $hgrp != 1)) || ((($typ_user == "ADMINISTRATEUR" || $typ_user == "RESPONSABLE_FORMATION") && $entantquetut != 1 && (($resp_grp == $id_user && $hgrp != 1 && $entantquepresc != 1) || ($form_app > 0 && $hgrp == 1))) || ($typ_user == "FORMATEUR_REFERENT" && $form_app > 0) || ($tut_grp == $id_user && $entantquetut !=1 && $hgrp != 1 && $entantquepresc != 1) || ($entantquetut !=1 && $hgrp != 1 && $entantquepresc == 1 && $presc_app > 0)) || (($typ_user == "ADMINISTRATEUR" || ($typ_user == "RESPONSABLE_FORMATION" && $resp_grp == $id_user)) && $entantquetut != 1 && $hgrp !=1)){
           $bar->SetCSIMTarget($sequence);
           $bar->SetCSIMAlt($alt);
           $bar->title->SetCSIMTarget($le_lien);
           if ($id_photo == '')
             $bar->title->SetCSIMAlt($alt1);
          $passage++;
        }
      }else{
        $alt1="$mess_suite_fp";
        $bar->title->SetCSIMTarget($le_lien);
        if ($id_photo == '')
           $bar->title->SetCSIMAlt($alt1);
      }
      $bar->SetHeight(12);
     $graph->Add($bar);
  $app++;
  }
$graph->Stroke();
//$graph->StrokeCSIM(basename(__FILE__));
?>
