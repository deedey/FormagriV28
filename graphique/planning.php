<?php
if (!isset($_SESSION)) session_start();
include ("../include/UrlParam2PhpVar.inc.php");
require 'admin.inc.php';
require '../fonction.inc.php';
require '../fonction_html.inc.php';
require "../lang$lg.inc.php";
include ("jpgraph.php");
include ("jpgraph_gantt.php");
dbConnect();
$Ext="_".$numero_groupe;
    if ($utilisateur > 0 && ($typ_user == "FORMATEUR_REFERENT" || ($typ_user == "RESPONSABLE_FORMATION" && $hgrp == 1))){
      $parc_query = mysql_query ("select suiv3_parc_no from suivi3$Ext,prescription$Ext,tuteur,groupe where
                                  presc_parc_no = suiv3_parc_no AND
                                  presc_grp_no = $numero_groupe AND
                                  suiv3_grp_no = $numero_groupe and
                                  presc_utilisateur_no = $utilisateur and
                                  (presc_formateur_no=$id_user OR
                                  (tuteur.tut_apprenant_no = presc_utilisateur_no AND tuteur.tut_tuteur_no = $id_user) OR
                                  (groupe.grp_tuteur_no = $id_user AND groupe.grp_cdn=$numero_groupe))
                                  group by suiv3_parc_no order by presc_ordre_no,suiv3_cdn asc");
      $num_app = $utilisateur;
    }elseif ($utilisateur > 0 && $typ_user == "TUTEUR"){
      $parc_query = mysql_query ("select suiv3_parc_no from suivi3$Ext,prescription$Ext where
                                  presc_parc_no = suiv3_parc_no and
                                  presc_utilisateur_no=$utilisateur AND
                                  presc_grp_no = $numero_groupe AND
                                  suiv3_grp_no = $numero_groupe
                                  group by suiv3_parc_no
                                  order by presc_ordre_no,suiv3_cdn asc");
      $num_app = $utilisateur;
    }elseif ($utilisateur > 0 && ($typ_user == "ADMINISTRATEUR" || ($typ_user == "RESPONSABLE_FORMATION" && $hgrp != 1))){
      $parc_query = mysql_query ("select suiv3_parc_no from suivi3$Ext,prescription$Ext where
                                 presc_parc_no = suiv3_parc_no and
                                 presc_utilisateur_no=$utilisateur AND
                                 presc_grp_no = $numero_groupe AND
                                 suiv3_grp_no = $numero_groupe
                                 group by suiv3_parc_no
                                 order by presc_ordre_no,suiv3_cdn asc");
      $num_app = $utilisateur;
    }else{
      $parc_query = mysql_query ("select suiv3_parc_no from suivi3$Ext,prescription$Ext where
                                 presc_parc_no = suiv3_parc_no and
                                 presc_utilisateur_no = $id_user AND
                                 presc_grp_no = $numero_groupe AND
                                 suiv3_grp_no = $numero_groupe
                                 group by suiv3_parc_no
                                 order by presc_ordre_no,suiv3_cdn asc");
      $num_app = $id_user;
    }
    $nb_parc = mysql_num_rows ($parc_query);
    if ($nb_parc == 0 && $typ_user == "APPRENANT"){
        echo "<center><FONT COLOR=blue><B><I>$mess_gp_tut_nopresc1</I></B></FONT>";
        echo "</TD></TR></TABLE></TD></TR></TABLE>";
        exit;
    }elseif ($nb_parc == 0 && $typ_user != "APPRENANT"){
        echo "<center><FONT COLOR=blue><B><I>$mess_gp_tut_nopresc</I></B></FONT>";
        echo "</TD></TR></TABLE></TD></TR></TABLE>";
        exit;
    }
$nom_user=GetDataField ($connect,"select util_nom_lb from utilisateur where util_cdn='$num_app'","util_nom_lb");
$prenom_user=GetDataField ($connect,"select util_prenom_lb from utilisateur where util_cdn='$num_app'","util_prenom_lb");
       $group = mysql_query("select utilgr_groupe_no from utilisateur_groupe where utilgr_utilisateur_no = $num_app");
       $nbr_grp = mysql_num_rows($group);
       if ($nbr_grp > 0)
        $grp = mysql_result($group,0,"utilgr_groupe_no");
        if ($plan == "plan_seq" || $plan == "plan_parc")
           $req_min = mysql_query ("select presc_datedeb_dt from prescription$Ext where
                                   presc_utilisateur_no = $num_app AND
                                   presc_grp_no=$numero_groupe
                                   order by presc_datedeb_dt asc");
        elseif ($plan == "plan_mixte")
           $req_min = mysql_query ("select presc_datedeb_dt from prescription$Ext where
                                    presc_parc_no = $parcours and
                                    presc_utilisateur_no = $num_app
                                    order by presc_datedeb_dt asc");
        $date_min = mysql_result($req_min,0,"presc_datedeb_dt");
        $nb_jours_req_min = mysql_query ("select TO_DAYS('$date_min')");
        $nb_jours_min = mysql_result ($nb_jours_req_min,0);
        if ($plan == "plan_seq" || $plan == "plan_parc")
           $req_max= mysql_query("select presc_datefin_dt from prescription$Ext where
                                  presc_utilisateur_no = $num_app AND
                                  presc_grp_no=$numero_groupe
                                  order by presc_datefin_dt asc");
        elseif ($plan == "plan_mixte")
           $req_max = mysql_query ("select presc_datefin_dt from prescription$Ext where
                                    presc_parc_no = $parcours and
                                    presc_utilisateur_no = $num_app
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
//        $graph = new GanttGraph(800,-1,"auto",.2);
//        $graph->SetShadow();
        $graph->SetBox();
        if ($typ_user != 'APPRENANT'){
          if ($plan == "plan_mixte"){
            $nom = GetDataField ($connect,"select parcours_nom_lb from parcours where parcours_cdn = $parcours","parcours_nom_lb");
            $graph->title->Set("$mess_gp_parc_appr $nom $mess_pf_de $prenom_user  $nom_user");
            $graph->subtitle->Set("($mess_pf_sp) --> $mess_pf_vert");
          }elseif ($plan == "plan_seq"){
            $graph->title->Set("$mess_pf_tsf $prenom_user  $nom_user");
            $graph->subtitle->Set("($mess_pf_pf) --> $mess_pf_vert");
          }else{
            $graph->title->Set("$mess_pf_pef $prenom_user  $nom_user");
            $graph->subtitle->Set("($mess_pf_spa) --> $mess_pf_vert");
          }
          $graph->title->SetFont(FF_ARIAL,FS_BOLD,10);
          $graph->subtitle->SetFont(FF_ARIAL,FS_NORMAL,8);
          $graph->subtitle->SetColor("darkred");
          $graph->title->SetColor("darkred");
        }
        $graph->SetMarginColor("#CEE6EC");
        if ($nbrj < 21){
           $graph->ShowHeaders(GANTT_HYEAR | GANTT_HMONTH  |GANTT_HDAY | GANTT_HWEEK);//| GANTT_HDAY
           $graph->scale->day->SetStyle(DAYSTYLE_SHORT);
           $graph->scale->month->SetStyle(MONTHSTYLE_LONGNAME);
        }elseif ($nbrj >20 && $nbrj <92){
           $graph->ShowHeaders(GANTT_HYEAR | GANTT_HMONTH | GANTT_HDAY | GANTT_HWEEK);//
           $graph->scale->month->SetStyle(MONTHSTYLE_LONGNAME);
        }elseif($nbrj >91 && $nbrj <120){
           $graph->ShowHeaders(GANTT_HYEAR | GANTT_HMONTH  | GANTT_HWEEK);//| GANTT_HDAY
           $graph->scale->month->SetStyle(MONTHSTYLE_LONGNAME);
        }elseif($nbrj > 119 && $nbrj <300){
           $graph->ShowHeaders(GANTT_HYEAR | GANTT_HMONTH  | GANTT_HWEEK);//| GANTT_HDAY
           $graph->scale->month->SetStyle(MONTHSTYLE_SHORTNAME);
        }else{
           $graph->ShowHeaders(GANTT_HYEAR | GANTT_HMONTH );//| GANTT_HDAY | GANTT_HWEEK
           $graph->scale->month->SetStyle(MONTHSTYLE_FIRSTLETTER);
        }
        $graph->scale->year->SetFont(FF_ARIAL,FS_BOLD,10);
        $graph->scale->year->Setfontcolor("#333333");
        $graph->scale->month->Setbackgroundcolor("#2b677A");
        $graph->scale->month->SetfontColor("white");
        $graph->scale->day->SetFont(FF_ARIAL,FS_NORMAL,7);
            for($i=0; $i<$nb_parc; ++$i) {
              $parc = mysql_result ($parc_query,$i,"suiv3_parc_no");
              $type_parcours = getdatafield ($connect,"SELECT parcours_type_lb from parcours where parcours_cdn = $parc","parcours_type_lb");
              if (strstr($type_parcours,"SCORM"))
                 $scormOk = 1;
              else
                 $scormOk = 0;
              $nom = GetDataField ($connect,"select parcours_nom_lb from parcours where parcours_cdn=$parc","parcours_nom_lb");
              $taille_nom = strlen($nom);
              $link="";
              $nom_affiche = "";
              $req_min = mysql_query ("select presc_datedeb_dt from prescription$Ext where presc_utilisateur_no = $num_app and
                                       presc_parc_no = $parc AND presc_grp_no=$numero_groupe
                                       order by presc_datedeb_dt asc");
              $date_deb = mysql_result($req_min,0,"presc_datedeb_dt");
              $req_max = mysql_query ("select presc_datefin_dt from prescription$Ext where presc_utilisateur_no = $num_app and
                                       presc_parc_no = $parc AND presc_grp_no=$numero_groupe
                                       order by presc_datefin_dt asc");
              $nbr_max = mysql_num_rows($req_max);
              $date_fin = mysql_result($req_max,$nbr_max-1,"presc_datefin_dt");
              if ($scormOk == 0 || !isset($scormOk)){
                 $seq_parc_query = mysql_query ("select count(act_cdn) from suivi2$Ext,prescription$Ext,suivi1$Ext,activite where
                                                 act_seq_no = suiv2_seq_no and
                                                 suiv2_seq_no = presc_seq_no and
                                                 presc_utilisateur_no=$num_app and
                                                 suivi_act_no = act_cdn AND
                                                 act_seq_no = presc_seq_no AND
                                                 presc_grp_no=$numero_groupe AND
                                                 suivi_grp_no = $numero_groupe and
                                                 presc_parc_no=$parc and suivi_utilisateur_no = $num_app and
                                                 suiv2_utilisateur_no = $num_app");
                 $duree_act_termine = mysql_query ("select count(act_cdn) from suivi2$Ext,prescription$Ext,suivi1$Ext,activite where
                                                    act_seq_no = suiv2_seq_no and
                                                    suiv2_seq_no = presc_seq_no and
                                                    presc_utilisateur_no=$num_app and
                                                    suivi_act_no = act_cdn AND
                                                    act_seq_no = presc_seq_no AND
                                                    presc_grp_no=$numero_groupe and
                                                    presc_parc_no=$parc and
                                                    suivi_utilisateur_no = $num_app AND
                                                    suivi_grp_no = $numero_groupe and
                                                    suiv2_utilisateur_no = $num_app AND
                                                    suiv2_grp_no = $numero_groupe AND
                                                    suivi_etat_lb !='A FAIRE' AND
                                                    suivi_etat_lb !='PRESENTIEL'");
                 $traque = mysql_query ("select count(suivi_date_debut_dt) from suivi1$Ext,activite,prescription$Ext where
                                         suivi_date_debut_dt != \"0000-00-00 00:00:00\" and
                                         suivi_utilisateur_no = $num_app and
                                         suivi_act_no = act_cdn AND
                                         suivi_grp_no = $numero_groupe and
                                         act_seq_no = presc_seq_no and
                                         presc_parc_no = $parc and
                                         presc_utilisateur_no = $num_app");
                 $seq_parc_query1 = mysql_query ("select count(scorm_module.mod_cdn) from
                                                  suivi2$Ext,prescription$Ext,scorm_util_module$Ext,scorm_module where
                                                  scorm_module.mod_seq_no = suiv2_seq_no and
                                                  suiv2_seq_no = presc_seq_no and
                                                  presc_utilisateur_no=$num_app and
                                                  mod_module_no = scorm_module.mod_cdn AND
                                                  scorm_module.mod_seq_no = presc_seq_no AND
                                                  presc_grp_no=$numero_groupe and
                                                  presc_parc_no=$parc and
                                                  user_module_no = $num_app AND
                                                  suiv2_grp_no = $numero_groupe and
                                                  suiv2_utilisateur_no = $num_app");
                 $duree_act_termine1 = mysql_query ("select count(scorm_module.mod_cdn) from
                                                     suivi2$Ext,prescription$Ext,scorm_util_module$Ext,scorm_module where
                                                     scorm_module.mod_seq_no = suiv2_seq_no and
                                                     suiv2_seq_no = presc_seq_no and
                                                     presc_utilisateur_no=$num_app and
                                                     mod_module_no = scorm_module.mod_cdn AND
                                                     scorm_module.mod_seq_no = presc_seq_no AND
                                                     presc_grp_no=$numero_groupe and
                                                     presc_parc_no=$parc and
                                                     user_module_no = $num_app AND
                                                     suiv2_grp_no = $numero_groupe and
                                                     suiv2_utilisateur_no = $num_app AND
                                                     lesson_status !='NOT ATTEMPTED'");
                 $traque1 = mysql_query ("select count(last_acces) from scorm_util_module$Ext,scorm_module,prescription$Ext where
                                          last_acces != \"0000-00-00 00:00:00\" and
                                          user_module_no = $num_app AND
                                          mod_grp_no = $numero_groupe and
                                          mod_module_no = scorm_module.mod_cdn and
                                          scorm_module.mod_seq_no = presc_seq_no and
                                          presc_parc_no = $parc and
                                          presc_utilisateur_no = $num_app");
                 $duree_parc = mysql_result($seq_parc_query,0);
                 $duree_parc += mysql_result($seq_parc_query1,0);
                 $duree_act_ter = mysql_result($duree_act_termine,0);
                 $duree_act_ter += mysql_result($duree_act_termine1,0);
                 $result = ceil($duree_act_ter * 100 / $duree_parc);
              }elseif ($scormOk == 1){
                 $seq_parc_query = mysql_query ("select count(scorm_module.mod_cdn) from
                                                 suivi2$Ext,prescription$Ext,scorm_util_module$Ext,scorm_module where
                                                 scorm_module.mod_seq_no = suiv2_seq_no and
                                                 suiv2_seq_no = presc_seq_no and
                                                 presc_utilisateur_no=$num_app and
                                                 mod_module_no = scorm_module.mod_cdn AND
                                                 scorm_module.mod_seq_no = presc_seq_no AND
                                                 mod_grp_no = $numero_groupe and
                                                 presc_grp_no=$numero_groupe and
                                                 presc_parc_no=$parc and
                                                 user_module_no = $num_app and
                                                 suiv2_utilisateur_no = $num_app");
                 $duree_act_termine = mysql_query ("select count(scorm_module.mod_cdn) from
                                                    suivi2$Ext,prescription$Ext,scorm_util_module$Ext,scorm_module where
                                                    scorm_module.mod_seq_no = suiv2_seq_no and
                                                    suiv2_seq_no = presc_seq_no and
                                                    presc_utilisateur_no=$num_app and
                                                    mod_module_no = scorm_module.mod_cdn AND
                                                    scorm_module.mod_seq_no = presc_seq_no AND
                                                    mod_grp_no = $numero_groupe and
                                                    presc_grp_no=$numero_groupe and
                                                    presc_parc_no=$parc and
                                                    user_module_no = $num_app and
                                                    suiv2_utilisateur_no = $num_app AND
                                                    lesson_status !='NOT ATTEMPTED'");
                 $traque = mysql_query ("select count(last_acces) from
                                         scorm_util_module$Ext,scorm_module,prescription$Ext where
                                         last_acces != \"0000-00-00 00:00:00\" and
                                         user_module_no = $num_app and
                                         mod_module_no = scorm_module.mod_cdn and
                                         mod_grp_no = $numero_groupe and
                                         scorm_module.mod_seq_no = presc_seq_no and
                                         presc_parc_no = $parc and
                                         presc_utilisateur_no = $num_app");
                 $duree_parc = mysql_result($seq_parc_query,0);
                 $duree_act_ter = mysql_result($duree_act_termine,0);
                 $result = ceil($duree_act_ter * 100 / $duree_parc);
              }
//              $duree_parc = mysql_result($seq_parc_query,0);
              $seq_parc_cpt = mysql_query ("select count(presc_cdn) from prescription$Ext where
                                            presc_parc_no = $parc and presc_utilisateur_no=$num_app AND
                                            presc_grp_no=$numero_groupe");
              $nb_seq_parc = mysql_result($seq_parc_cpt,0);
              $nb_traque = mysql_result($traque,0);
              if ($nb_traque > 0){
                 if ($scormOk == 0 || !isset($scormOk)){
                    $date_maximum0 = mysql_query ("select suivi_date_debut_dt from suivi1$Ext,activite,prescription$Ext where
                                                   suivi_date_debut_dt != \"0000-00-00 00:00:00\" and
                                                   suivi_utilisateur_no = $num_app and
                                                   suivi_grp_no = $numero_groupe and
                                                   suivi_act_no=act_cdn and
                                                   act_seq_no=presc_seq_no and
                                                   presc_parc_no=$parc and
                                                   presc_utilisateur_no = $num_app
                                                   order by suivi_date_debut_dt desc");
                    if (mysql_num_rows($date_maximum0) > 0)
                       $date_traque0 = mysql_result($date_maximum0,0,"suivi_date_debut_dt");
                    $date_maximum1 = mysql_query ("select last_acces from
                                                   scorm_util_module$Ext,scorm_module,prescription$Ext where
                                                   last_acces != \"0000-00-00 00:00:00\" and
                                                   user_module_no = $num_app and
                                                   mod_grp_no = $numero_groupe and
                                                   mod_module_no=scorm_module.mod_cdn and
                                                   scorm_module.mod_seq_no=presc_seq_no and
                                                   presc_parc_no=$parc and
                                                   presc_utilisateur_no = $num_app
                                                   order by last_acces desc");
                    if (mysql_num_rows($date_maximum1) > 0)
                       $date_traque1 = mysql_result($date_maximum1,0,"last_acces");
                    $date_maximum = ($date_maximum1 < $date_maximum0) ? $date_maximum0 : $date_maximum1;
                    $date_traque = ($date_traque1 < $date_traque0) ? $date_traque0 : $date_traque1;
                 }elseif ($scormOk == 1){
                    $date_maximum = mysql_query ("select last_acces from
                                                  scorm_util_module$Ext,scorm_module,prescription$Ext where
                                                  last_acces != \"0000-00-00 00:00:00\" and
                                                  user_module_no = $num_app and
                                                   mod_grp_no = $numero_groupe and
                                                  mod_module_no=scorm_module.mod_cdn and
                                                  scorm_module.mod_seq_no=presc_seq_no and
                                                  presc_parc_no=$parc and
                                                  presc_utilisateur_no = $num_app
                                                  order by last_acces desc");
                    $date_traque = mysql_result($date_maximum,0,"last_acces");
                 }
                 $date_traque = substr($date_traque,0,10);
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
              }else
                 $pourcent = 0;

              $le_lien ="../lancement.php?numero_groupe=$numero_groupe&hgrp=$hgrp&utilisateur=$utilisateur&plan=plan_mixte&parcours=$parc";
              $alt ="$nb_seq_parc $mess_ac_der3 ".addslashes($nom);
              if ($taille_nom > 40){
                $nom1 = substr($nom,0,41)."..";
                $nom_affiche = addslashes($nom);
                $lien = urlencode($le_lien);
                $link ="../trace.php?link=$lien\" onMouseOver=\"overlib('$nom_affiche',ol_hpos,CENTER,ABOVE,WIDTH,'240',DELAY,'200',CAPTION, '')\"  onMouseOut=\"nd();";
                $link1 ="../trace.php?link=$lien\" onMouseOver=\"overlib('$alt',ol_hpos,CENTER,ABOVE,WIDTH,'240',DELAY,'200',CAPTION, '')\"  onMouseOut=\"nd();\" target=\"main";
              }else{
                $nom1 = $nom;
                $lien = urlencode($le_lien);
                $link ="../trace.php?link=$lien";
                $link1 ="../trace.php?link=$lien\" onMouseOver=\"overlib('$alt',ol_hpos,CENTER,ABOVE,WIDTH,'240',DELAY,'200',CAPTION, '')\"  onMouseOut=\"nd();\" target=\"main";
              }
              $j = $i+1;
              $titre = $j."- ".$nom1;
              if ($typ_user == 'APPRENANT')
              {
                $bar = new GanttBar($i,$titre,$date_deb,$date_fin,"",.5);
                $bar->title->SetFont(FF_ARIAL,FS_NORMAL);
                $bar->title->Setcolor("#333333");
                $bar->SetShadow(true,"darkgray");
                $bar->SetPattern(BAND_RDIAG,"#7FBFCD");
                $bar->SetFillColor("#7FBFCD");
              }else{
                $bar = new GanttBar($i,$titre,$date_deb,$date_fin,"$result%",.5);
                $bar->title->SetFont(FF_ARIAL,FS_NORMAL);
                $bar->title->Setcolor("#333333");
                $bar->title->SetCSIMTarget($link);
                $bar->SetShadow(true,"darkgray");
                $bar->SetPattern(BAND_RDIAG,"#7FBFCD");
                $bar->SetFillColor("#7FBFCD");
                $bar->progress->Set($pourcent);
                $bar->rightMark->SetColor("chocolate3");
                $bar->rightMark->SetWidth(1);
                $bar->rightMark->title->SetFont(FF_ARIAL,FS_BOLD,10);
                $bar->rightMark->Show();
                $bar->SetCSIMTarget($link1);
               // $bar->SetCSIMAlt($alt);
              }
              $graph->Add($bar);
            }
        if ($date_max > $today){
           $vl = new GanttVLine($today,"$today1","#333333",2,"solid");
           $vl->SetDayOffset(0.5);
           $graph->Add($vl);
        }
         $rdv_query = mysql_query ("select * from rendez_vous where (rdv_creneau_nb=0 or rdv_titre_lb='Présentiel') and
                                    rdv_grp_no=$numero_groupe and rdv_tuteur_no=0 and rdv_apprenant_no=0");
         $Nb_rdv = mysql_num_rows ($rdv_query);
         if ($Nb_rdv !=0){
           $j=0;
             while ($j < $Nb_rdv){
                $comment = mysql_result ($rdv_query,$j,"rdv_commentaire_cmt");
                $date_event = mysql_result ($rdv_query,$j,"rdv_date_dt");
                if ($date_event > $date_min){
                  $vl = new GanttVLine($date_event,"","darkred",1);
                  $vl->SetDayOffset(0.5);
                  $graph->Add($vl);
                }
             $j++;
             }
          }
if ($typ_user == "APPRENANT")
   $graph->Stroke();
else
   $graph->StrokeCSIM(basename(__FILE__));
?>
