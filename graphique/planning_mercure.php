<?php
if (!isset($_SESSION)) session_start();
require '../fonction.inc.php';
require "../lang$lg.inc.php";
dbConnect();
include ("jpgraph.php");
include ("jpgraph_gantt.php");
if ($plan == "plan_seq")
{
    if ($utilisateur > 0 && $typ_user == "FORMATEUR_REFERENT")
    {
      $seq_query = mysql_query ("select distinct suiv2_etat_lb,suiv2_seq_no from suivi2,prescription,suivi1,activite where activite.act_seq_no = suivi2.suiv2_seq_no and suivi2.suiv2_seq_no = prescription.presc_seq_no and prescription.presc_utilisateur_no=$utilisateur and suivi1.suivi_act_no = activite.act_cdn and suivi1.suivi_utilisateur_no = $utilisateur and suivi2.suiv2_utilisateur_no = $utilisateur and prescription.presc_utilisateur_no = $utilisateur and prescription.presc_formateur_no=$id_user order by prescription.presc_ordre_no,suivi2.suiv2_cdn asc");
      $num_app = $utilisateur;
    }
    elseif ($utilisateur > 0 &&  $typ_user == "TUTEUR")
    {
     $seq_query = mysql_query ("select distinct suiv2_etat_lb,suiv2_seq_no from suivi2,suivi1,activite,prescription where activite.act_seq_no = suivi2.suiv2_seq_no and suivi2.suiv2_seq_no = prescription.presc_seq_no and prescription.presc_utilisateur_no=$utilisateur and suivi1.suivi_act_no = activite.act_cdn and suivi1.suivi_utilisateur_no = $utilisateur and suiv2_utilisateur_no = $utilisateur order by prescription.presc_ordre_no,suivi2.suiv2_cdn asc");
      $num_app = $utilisateur;
    }
    elseif ($utilisateur > 0 && ($typ_user == "ADMINISTRATEUR" || $typ_user == "RESPONSABLE_FORMATION"))
    {
      $seq_query = mysql_query ("select distinct suiv2_etat_lb,suiv2_seq_no from suivi2,suivi1,activite,prescription where activite.act_seq_no = suivi2.suiv2_seq_no and suivi2.suiv2_seq_no = prescription.presc_seq_no and prescription.presc_utilisateur_no=$utilisateur and suivi1.suivi_act_no = activite.act_cdn and suivi1.suivi_utilisateur_no = $utilisateur and suiv2_utilisateur_no = $utilisateur order by prescription.presc_ordre_no,suivi2.suiv2_cdn asc");
      $num_app = $utilisateur;
    }
    else
    {
      $seq_query = mysql_query ("select distinct suiv2_etat_lb,suiv2_seq_no from suivi2,suivi1,activite,prescription where activite.act_seq_no = suivi2.suiv2_seq_no and suivi2.suiv2_seq_no = prescription.presc_seq_no and prescription.presc_utilisateur_no=$id_user and suivi1.suivi_act_no = activite.act_cdn and suivi1.suivi_utilisateur_no = $id_user and suiv2_utilisateur_no = $id_user order by prescription.presc_ordre_no,suivi2.suiv2_cdn asc");
     $num_app = $id_user;
    }
    $nb_seq = mysql_num_rows ($seq_query);
    if ($nb_seq == 0 && $typ_user == "APPRENANT")
    {
        echo "<center><FONT COLOR=blue><B><I>$mess_gp_tut_nopresc1</I></B></FONT>";
        exit;
    }
    elseif ($nb_seq == 0)
    {
        echo "<center><FONT COLOR=blue><B><I>$mess_gp_tut_nopresc</I></B></FONT>";
        exit;
    }
}
elseif ($plan == "plan_mixte")
{
    if ($utilisateur > 0 && $typ_user == "FORMATEUR_REFERENT")
    {
      $seq_query = mysql_query ("select distinct suiv2_etat_lb,suiv2_seq_no from suivi2,prescription,suivi1,activite where activite.act_seq_no = suivi2.suiv2_seq_no and suivi2.suiv2_seq_no = prescription.presc_seq_no and prescription.presc_parc_no = $parcours and prescription.presc_utilisateur_no=$utilisateur and suivi1.suivi_act_no = activite.act_cdn and suivi1.suivi_utilisateur_no = $utilisateur and suivi2.suiv2_utilisateur_no = $utilisateur and prescription.presc_utilisateur_no = $utilisateur and prescription.presc_formateur_no=$id_user order by prescription.presc_datefin_dt,prescription.presc_ordre_no,suivi2.suiv2_cdn asc");
      $num_app = $utilisateur;
    }
    elseif ($utilisateur > 0 &&  $typ_user == "TUTEUR")
    {
     $seq_query = mysql_query ("select distinct suiv2_etat_lb,suiv2_seq_no from suivi2,suivi1,activite,prescription where activite.act_seq_no = suivi2.suiv2_seq_no and suivi2.suiv2_seq_no = prescription.presc_seq_no and prescription.presc_parc_no = $parcours and prescription.presc_utilisateur_no=$utilisateur and suivi1.suivi_act_no = activite.act_cdn and suivi1.suivi_utilisateur_no = $utilisateur and suiv2_utilisateur_no = $utilisateur order by prescription.presc_datefin_dt,prescription.presc_ordre_no,suivi2.suiv2_cdn asc");
      $num_app = $utilisateur;
    }
    elseif ($utilisateur > 0 && ($typ_user == "ADMINISTRATEUR" || $typ_user == "RESPONSABLE_FORMATION"))
    {
      $seq_query = mysql_query ("select distinct suiv2_etat_lb,suiv2_seq_no from suivi2,suivi1,activite,prescription where activite.act_seq_no = suivi2.suiv2_seq_no and suivi2.suiv2_seq_no = prescription.presc_seq_no and prescription.presc_parc_no = $parcours and prescription.presc_utilisateur_no=$utilisateur and suivi1.suivi_act_no = activite.act_cdn and suivi1.suivi_utilisateur_no = $utilisateur and suiv2_utilisateur_no = $utilisateur order by prescription.presc_datefin_dt,prescription.presc_ordre_no,suivi2.suiv2_cdn asc");
      $num_app = $utilisateur;
    }
    else
    {
      $seq_query = mysql_query ("select distinct suiv2_etat_lb,suiv2_seq_no from suivi2,suivi1,activite,prescription where activite.act_seq_no = suivi2.suiv2_seq_no and suivi2.suiv2_seq_no = prescription.presc_seq_no and prescription.presc_parc_no = $parcours and prescription.presc_utilisateur_no=$id_user and suivi1.suivi_act_no = activite.act_cdn and suivi1.suivi_utilisateur_no = $id_user and suiv2_utilisateur_no = $id_user order by prescription.presc_datefin_dt,prescription.presc_ordre_no,suivi2.suiv2_cdn asc");
      $num_app = $id_user;
    }
    $nb_seq = mysql_num_rows ($seq_query);
    if ($nb_seq == 0 && $typ_user == "APPRENANT")
    {
        echo "<center><FONT COLOR=blue><B><I>$mess_gp_tut_nopresc1</I></B></FONT>";
        exit;
    }
    elseif ($nb_seq == 0)
    {
        echo "<center><FONT COLOR=blue><B><I>$mess_gp_tut_nopresc</I></B></FONT>";
        exit;
    }
}
elseif ($plan == "plan_parc")
{
    if ($utilisateur > 0 && $typ_user == "FORMATEUR_REFERENT")
    {
      $parc_query = mysql_query ("select suiv3_parc_no from suivi3,prescription where prescription.presc_parc_no = suiv3_parc_no and prescription.presc_utilisateur_no=$utilisateur AND prescription.presc_grp_no = $numero_groupe and prescription.presc_utilisateur_no = $utilisateur and prescription.presc_formateur_no=$id_user group by suiv3_parc_no order by prescription.presc_datefin_dt,prescription.presc_ordre_no,suivi3.suiv3_cdn asc");
      $num_app = $utilisateur;
    }
    elseif ($utilisateur > 0 &&  $typ_user == "TUTEUR")
    {
      $parc_query = mysql_query ("select suiv3_parc_no from suivi3,prescription where prescription.presc_parc_no = suiv3_parc_no and prescription.presc_utilisateur_no=$utilisateur AND prescription.presc_grp_no = $numero_groupe group by suiv3_parc_no order by prescription.presc_datefin_dt,prescription.presc_ordre_no,suivi3.suiv3_cdn asc");
      $num_app = $utilisateur;
    }
    elseif ($utilisateur > 0 && ($typ_user == "ADMINISTRATEUR" || $typ_user == "RESPONSABLE_FORMATION"))
    {
      $parc_query = mysql_query ("select suiv3_parc_no from suivi3,prescription where prescription.presc_parc_no = suiv3_parc_no and prescription.presc_utilisateur_no=$utilisateur AND prescription.presc_grp_no = $numero_groupe group by suiv3_parc_no order by prescription.presc_datefin_dt,prescription.presc_ordre_no,suivi3.suiv3_cdn asc");
      $num_app = $utilisateur;
    }
    else
    {
//      $parc_query = mysql_query ("select suiv3_parc_no from suivi3,prescription where prescription.presc_parc_no = suiv3_parc_no and prescription.presc_utilisateur_no = $id_user AND prescription.presc_grp_no = $numero_groupe group by suiv3_parc_no order by prescription.presc_datefin_dt,prescription.presc_ordre_no,suivi3.suiv3_cdn asc");
      $parc_query = mysql_query ("select suiv3_parc_no from suivi3,prescription where prescription.presc_parc_no = suiv3_parc_no and prescription.presc_utilisateur_no = $id_user AND prescription.presc_grp_no = $numero_groupe group by suiv3_parc_no order by prescription.presc_ordre_no,suivi3.suiv3_cdn asc");
      $num_app = $id_user;
    }
    $nb_parc = mysql_num_rows ($parc_query);
    if ($nb_parc == 0 && $typ_user == "APPRENANT")
    {
        echo "<center><FONT COLOR=blue><B><I>$mess_gp_tut_nopresc1</I></B></FONT>";
        include ("form_retour.txt");
        exit;
    }
    elseif ($nb_parc == 0 && $typ_user != "APPRENANT")
    {
        echo "<center><FONT COLOR=blue><B><I>$mess_gp_tut_nopresc</I></B></FONT>";
        include ("../form_retour.txt");
        exit;
    }
}
        $nom_user=GetDataField ($connect,"select util_nom_lb from utilisateur where util_cdn='$num_app'","util_nom_lb");
        $prenom_user=GetDataField ($connect,"select util_prenom_lb from utilisateur where util_cdn='$num_app'","util_prenom_lb");
        $group = mysql_query("select utilgr_groupe_no from utilisateur_groupe where utilgr_utilisateur_no = $num_app");
        $nbr_grp = mysql_num_rows($group);
        if ($nbr_grp > 0)
           $grp = mysql_result($group,0,"utilgr_groupe_no");
        if ($plan == "plan_seq" || $plan == "plan_parc")
           $req_min = mysql_query ("select presc_datedeb_dt from prescription where presc_utilisateur_no = $num_app  AND prescription.presc_grp_no = $numero_groupe order by presc_datedeb_dt asc");
        elseif ($plan == "plan_mixte")
           $req_min = mysql_query ("select presc_datedeb_dt from prescription where presc_parc_no = $parcours and presc_utilisateur_no = $num_app order by presc_datedeb_dt asc");
        $date_min = mysql_result($req_min,0,"presc_datedeb_dt");
        $nb_jours_req_min = mysql_query ("select TO_DAYS('$date_min')");
        $nb_jours_min = mysql_result ($nb_jours_req_min,0);
        if ($plan == "plan_seq" || $plan == "plan_parc")
           $req_max= mysql_query("select presc_datefin_dt from prescription where presc_utilisateur_no = $num_app AND prescription.presc_grp_no = $numero_groupe order by presc_datefin_dt asc");
        elseif ($plan == "plan_mixte")
           $req_max = mysql_query ("select presc_datefin_dt from prescription where presc_parc_no = $parcours and presc_utilisateur_no = $num_app order by presc_datefin_dt asc");
        $nbr_max = mysql_num_rows($req_max);
        $date_max = mysql_result($req_max,$nbr_max-1,"presc_datefin_dt");
        $nb_jours_req_max = mysql_query ("select TO_DAYS('$date_max')");
        $nb_jours_max = mysql_result ($nb_jours_req_max,0);
        $nbrj = $nb_jours_max-$nb_jours_min;
        $today = date("Y-m-d");
        $today1 = date("d-m-Y");
        $graph = new GanttGraph(800,-1,"auto",.2);
//        $graph->SetShadow();
        $graph->SetBox();
        if ($typ_user != "APPRENANT")
        {
          if ($plan == "plan_mixte")
          {
            if ($parcours == 0)
             $nom = $mess_gp_seq_hors_parc;
            else
             $nom = GetDataField ($connect,"select parcours_nom_lb from parcours where parcours_cdn = $parcours","parcours_nom_lb");
          $graph->title->Set("$mess_gp_parc_appr $nom $mess_pf_de $prenom_user  $nom_user");
          //$graph->subtitle->Set("($mess_pf_sp) --> $mess_pf_vert");
          }
          elseif ($plan == "plan_seq")
          {
            $graph->title->Set("$mess_pf_tsf $prenom_user  $nom_user");
          //$graph->subtitle->Set("($mess_pf_pf) --> $mess_pf_vert");
          }
          else
          {
            $graph->title->Set("$mess_pf_pef $prenom_user  $nom_user");
          //$graph->subtitle->Set("($mess_pf_spa) --> $mess_pf_vert");
          }
          $graph->title->SetFont(FF_ARIAL,FS_BOLD,12);
          $graph->subtitle->SetFont(FF_ARIAL,FS_NORMAL,10);
          $graph->subtitle->SetColor("darkred");
          $graph->title->SetColor("darkred");
        }
        $graph->SetMarginColor("#CEE6EC");
//        if ($nbrj <70 ){
//          $graph->ShowHeaders(GANTT_HYEAR | GANTT_HMONTH | GANTT_HDAY | GANTT_HWEEK);//
//          $graph->scale->month->SetStyle(MONTHSTYLE_LONGNAME);
        if ($nbrj <100)
        {
          $graph->ShowHeaders(GANTT_HYEAR | GANTT_HMONTH  | GANTT_HWEEK);//| GANTT_HDAY
//          $graph->scale->month->SetStyle(MONTHSTYLE_LONGNAME);
        }
        else
        {
          $graph->ShowHeaders(GANTT_HYEAR | GANTT_HMONTH );//| GANTT_HDAY | GANTT_HWEEK
//          $graph->scale->month->SetStyle(MONTHSTYLE_SHORTNAME);
        }
        $graph->scale->divider->SetWeight(1);
        $graph->scale->divider->SetColor("white");
        $graph->scale->dividerh->SetWeight(1);
        $graph->scale->dividerh->SetColor("white");
        $graph->scale->year->grid->SetColor("white");
        $graph->scale->year->Setbackgroundcolor("white");
        $graph->scale->year->SetfontColor("white");
        $graph->scale->month->grid->SetColor("white");
        $graph->scale->month->Setbackgroundcolor("white");
        $graph->scale->month->SetfontColor("white");
        $graph->scale->week->grid->SetColor("white");
        $graph->scale->week->Setbackgroundcolor("white");
        $graph->scale->week->SetfontColor("white");
        if ($plan == "plan_seq" || $plan == "plan_mixte")
        {
          for($i=0; $i<$nb_seq; ++$i)
          {
              $seq = mysql_result ($seq_query,$i,"suiv2_seq_no");
              $etat = mysql_result ($seq_query,$i,"suiv2_etat_lb");
              $nom = GetDataField ($connect,"select seq_titre_lb from sequence where SEQ_CDN=$seq","seq_titre_lb");
              $j = $i+1;
              $titre = $nom;
              $req_min = mysql_query ("select presc_datedeb_dt from prescription where presc_seq_no = $seq and presc_utilisateur_no = $num_app order by presc_datedeb_dt asc");
              $date_deb = mysql_result($req_min,0,"presc_datedeb_dt");
              $req_max = mysql_query ("select presc_datefin_dt from prescription where presc_seq_no = $seq and presc_utilisateur_no = $num_app order by presc_datefin_dt asc");
              $nbr_max = mysql_num_rows($req_max);
              $date_fin = mysql_result($req_max,$nbr_max-1,"presc_datefin_dt");
              $duree_sequence = mysql_query ("select sum(activite.act_duree_nb) from activite,suivi1 where activite.act_cdn = suivi1.suivi_act_no and activite.act_seq_no=$seq and suivi1.suivi_utilisateur_no =$num_app");
              $duree_seq = mysql_result($duree_sequence,0);
              $duree_act_termine = mysql_query ("select sum(activite.act_duree_nb) from activite,suivi1 where activite.act_cdn = suivi1.suivi_act_no and activite.act_seq_no=$seq and suivi1.suivi_utilisateur_no =$num_app && suivi1.suivi_etat_lb='TERMINE'");
              $duree_act_ter = mysql_result($duree_act_termine,0);
              $result =ceil($duree_act_ter * 100 / $duree_seq);
              $pourcent= round($duree_act_ter/$duree_seq,2);
              $bar = new GanttBar($i,$titre,$date_deb,$date_fin,"$result%",.5);
              $bar->title->SetFont(FF_ARIAL,FS_NORMAL);
              $bar->title->Setcolor("#333333");
              $bar->SetShadow(true,"darkgray");
              $bar->SetPattern(BAND_RDIAG,"#7FBFCD");
              $bar->SetFillColor("#7FBFCD");
              $bar->progress->Set($pourcent);
/*              $bar->rightMark->SetType(MARK_FILLEDCIRCLE);
             $bar->rightMark->SetFillColor("orange");
              $bar->rightMark->SetColor("chocolate3");
              $bar->rightMark->SetWidth(8);
              $bar->rightMark->title->Set("".$i+1);
              $bar->rightMark->title->SetColor("blue");
              $bar->rightMark->title->SetFont(FF_ARIAL,FS_BOLD,10);
              $bar->rightMark->Show();
*/
              $bar->SetHeight(12);
              $graph->Add($bar);
           }
        }
        elseif ($plan == "plan_parc")
        {
            for($i=0; $i<$nb_parc; ++$i)
            {
              $parc = mysql_result ($parc_query,$i,"suiv3_parc_no");
              if ($parc == 0)
                $nom = "$mess_gp_seq_hors_parc";
              else
                $nom = GetDataField ($connect,"select parcours_nom_lb from parcours where parcours_cdn=$parc","parcours_nom_lb");
              $j = $i+1;
              $titre = $j."- ".$nom;
              $req_min = mysql_query ("select presc_datedeb_dt from prescription where presc_utilisateur_no = $num_app  and presc_parc_no = $parc order by presc_datedeb_dt asc");
              $date_deb = mysql_result($req_min,0,"presc_datedeb_dt");
              $req_max = mysql_query ("select presc_datefin_dt from prescription where presc_utilisateur_no = $num_app  and presc_parc_no = $parc order by presc_datefin_dt asc");
              $nbr_max = mysql_num_rows($req_max);
              $date_fin = mysql_result($req_max,$nbr_max-1,"presc_datefin_dt");
              $seq_parc_query = mysql_query ("select sum(activite.act_duree_nb) from suivi2,prescription,suivi1,activite where activite.act_seq_no = suivi2.suiv2_seq_no and suivi2.suiv2_seq_no = prescription.presc_seq_no and prescription.presc_utilisateur_no=$num_app and suivi1.suivi_act_no = activite.act_cdn and prescription.presc_parc_no=$parc and suivi1.suivi_utilisateur_no = $num_app and suivi2.suiv2_utilisateur_no = $num_app and prescription.presc_utilisateur_no = $num_app");
              $duree_parc = mysql_result($seq_parc_query,0);
              $duree_act_termine = mysql_query ("select sum(activite.act_duree_nb) from suivi2,prescription,suivi1,activite where activite.act_seq_no = suivi2.suiv2_seq_no and suivi2.suiv2_seq_no = prescription.presc_seq_no and prescription.presc_utilisateur_no=$num_app and suivi1.suivi_act_no = activite.act_cdn and prescription.presc_parc_no=$parc and suivi1.suivi_utilisateur_no = $num_app and suivi2.suiv2_utilisateur_no = $num_app and prescription.presc_utilisateur_no = $num_app and suivi1.suivi_etat_lb='TERMINE'");
              $duree_act_ter = mysql_result($duree_act_termine,0);
              $result =ceil($duree_act_ter * 100 / $duree_parc);
              $pourcent= round($duree_act_ter/$duree_parc,2);
              $bar = new GanttBar($i,$titre,$date_deb,$date_fin,"$result%",.5);
              $bar->title->SetFont(FF_ARIAL,FS_NORMAL);
              $bar->title->Setcolor("#333333");
              $bar->SetShadow(true,"darkgray");
              $bar->SetPattern(BAND_RDIAG,"#7FBFCD");
              $bar->SetFillColor("#7FBFCD");
              $bar->progress->Set($pourcent);
/*
              $bar->rightMark->SetType(MARK_FILLEDCIRCLE);
              $bar->rightMark->SetFillColor("orange");
              $bar->rightMark->SetColor("chocolate3");
              $bar->rightMark->SetWidth(8);
              $bar->rightMark->title->Set("".$i+1);
              $bar->rightMark->title->SetColor("blue");
              $bar->rightMark->title->SetFont(FF_ARIAL,FS_BOLD,10);
              $bar->rightMark->Show();
*/
              $bar->SetHeight(12);
              $graph->Add($bar);
            }
        }
/*
         if ($date_max > $today){
            $vl = new GanttVLine($today,"$today1","#408080",2,"solid");
             $vl->SetDayOffset(0.5);
             $graph->Add($vl);
         }
        if ($grp){
         $rdv_query = mysql_query ("select * from rendez_vous where (rdv_creneau_nb=0 or rdv_titre_lb='Présentiel') and rdv_grp_no=$grp and rdv_tuteur_no=0 and rdv_apprenant_no=0");
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
        }
*/
       $graph->Stroke();
?>
