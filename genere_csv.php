<?php
if (!isset($_SESSION)) session_start();
include ("include/UrlParam2PhpVar.inc.php");
if (!isset($_SESSION['id_user']) || $_SESSION['id_user'] == "")
{
  exit();
}
require "graphique/admin.inc.php";
require 'fonction.inc.php';
require 'fonction_html.inc.php';
require "lang$lg.inc.php";
dbConnect();
if (strstr(getenv("HTTP_USER_AGENT"),"MSIE")){
   $agent = "ie";$c1=",";$c2=";";
}else{
   $agent = "mz";$c1=",";$c2=";";
}
include ('include/varGlobals.inc.php');
$Ext = '_'.$numero_groupe;
$nom_user = $_SESSION['name_user'];
$prenom_user = $_SESSION['prename_user'];
$email_user = $_SESSION['email_user'];
$lien_retour = "http://".$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
$nom_grp =GetDataField ($connect,"select grp_nom_lb from groupe WHERE grp_cdn = $numero_groupe","grp_nom_lb");
$carac_grp = strlen($nom_grp);
if ($carac_grp > 40)
   $nom_grp2 = substr($nom_grp,0,40)."...";
else
   $nom_grp2 = $nom_grp;
if ($carac_grp > 10){
   $nom_grp1 = substr($nom_grp,0,8)."...";
   $nom_grp3 = substr($nom_grp,0,10);
}else
    $nom_grp1 = $nom_grp;
  if ($a_faire == 1)  {
    if ($id_ref>0){
      $referentiel = GetDataField ($connect,"select ref_nomabrege_lb from referentiel where ref_cdn = $id_ref","ref_nomabrege_lb");
      $nom_referentiel = GetDataField ($connect,"select ref_desc_cmt  from referentiel where ref_cdn = $id_ref","ref_desc_cmt");
      if ($nom_referentiel == "")
         $nom_referentiel = GetDataField ($connect,"select ref_nomabrege_lb from referentiel where ref_cdn = $id_ref","ref_nomabrege_lb");
    }
    if ($utilisateur > 0 && ($typ_user == "FORMATEUR_REFERENT" || ($typ_user == "RESPONSABLE_FORMATION" && $hgrp == 1))){
       $parc_query = mysql_query ("select suiv3_parc_no from suivi3$Ext,prescription$Ext,tuteur where
                                   prescription$Ext.presc_parc_no = suivi3$Ext.suiv3_parc_no and
                                   prescription$Ext.presc_utilisateur_no=$utilisateur and
                                   prescription$Ext.presc_utilisateur_no = $utilisateur and
                                   (prescription$Ext.presc_formateur_no=$id_user OR
                                   (tuteur.tut_apprenant_no = prescription$Ext.presc_utilisateur_no AND
                                   tuteur.tut_tuteur_no = $id_user)) AND
                                   prescription$Ext.presc_grp_no = $numero_groupe AND
                                   suivi3$Ext.suiv3_grp_no = $numero_groupe
                                   group by suivi3$Ext.suiv3_parc_no
                                   order by prescription$Ext.presc_ordre_no,prescription$Ext.presc_cdn");
       $num_app = $utilisateur;
    }elseif ($utilisateur > 0 &&  $typ_user == "TUTEUR"){
       $parc_query = mysql_query ("select suiv3_parc_no from suivi3$Ext,prescription$Ext where
                                   prescription$Ext.presc_parc_no = suivi3$Ext.suiv3_parc_no and
                                   prescription$Ext.presc_utilisateur_no=$utilisateur AND
                                   prescription$Ext.presc_grp_no = $numero_groupe AND
                                   suivi3$Ext.suiv3_grp_no = $numero_groupe
                                   group by suivi3$Ext.suiv3_parc_no
                                   order by prescription$Ext.presc_ordre_no,prescription$Ext.presc_cdn");
       $num_app = $utilisateur;
    }elseif ($utilisateur > 0 && ($typ_user == "ADMINISTRATEUR" || ($typ_user == "RESPONSABLE_FORMATION" && $hgrp != 1))){
       $parc_query = mysql_query ("select suiv3_parc_no from suivi3$Ext,prescription$Ext where
                                   prescription$Ext.presc_parc_no = suivi3$Ext.suiv3_parc_no and
                                   prescription$Ext.presc_utilisateur_no=$utilisateur AND
                                   prescription$Ext.presc_grp_no = $numero_groupe AND
                                   suivi3$Ext.suiv3_grp_no = $numero_groupe
                                   group by suivi3$Ext.suiv3_parc_no
                                   order by prescription$Ext.presc_ordre_no,prescription$Ext.presc_cdn");
       $num_app = $utilisateur;
    }elseif ($typ_user == "APPRENANT"){
       $parc_query = mysql_query ("select suiv3_parc_no from suivi3$Ext,prescription$Ext where
                                   prescription$Ext.presc_parc_no = suivi3$Ext.suiv3_parc_no and
                                   prescription$Ext.presc_utilisateur_no = $id_user AND
                                   prescription$Ext.presc_grp_no = $numero_groupe AND
                                   suivi3$Ext.suiv3_grp_no = $numero_groupe
                                   group by suivi3$Ext.suiv3_parc_no
                                   order by prescription$Ext.presc_ordre_no,prescription$Ext.presc_cdn");
       $num_app = $id_user;
    }
    $nom_app = GetDataField ($connect,"select util_nom_lb from utilisateur where util_cdn='$num_app'","util_nom_lb");
    $prenom_app = GetDataField ($connect,"select util_prenom_lb from utilisateur where util_cdn='$num_app'","util_prenom_lb");
    $date_heure = date("d-m-Y -- H\h i' s''" ,time());
    $fichier_csv = ucfirst(strtolower($nom_app))." ".ucfirst(strtolower($prenom_app))." - $mess_grp_form : $nom_grp  [ $mess_mess_date : $date_heure ]\n";
    $inscripteur = GetDataField ($connect,"select util_auteur_no from utilisateur where util_cdn='$num_app'","util_auteur_no");
    $nb_parc = mysql_num_rows ($parc_query);

//-------------------------- Début de la boucle parcours > sequences > activités --------------------------------------------
    $p=0;
    while ($p < $nb_parc )
    {
          $compte++;
          $fin_parc=0;
          $id_parc = mysql_result ($parc_query,$p,"suiv3_parc_no");
          $etat_parc =  GetDataField ($connect,"select suiv3_etat_lb from suivi3$Ext where
                                                suiv3_parc_no = $id_parc and
                                                suiv3_utilisateur_no = $num_app AND
                                                suiv3_grp_no = $numero_groupe","suiv3_etat_lb");
          $createur = GetDataField ($connect,"select parcours_auteur_no from parcours where parcours_cdn = $id_parc","parcours_auteur_no");
          $nom = GetDataField ($connect,"select parcours_nom_lb from parcours where parcours_cdn = $id_parc","parcours_nom_lb");
          $type_parcours =  GetDataField ($connect,"select parcours_type_lb from parcours where parcours_cdn = $id_parc","parcours_type_lb");
          if (strstr($type_parcours,"SCORM"))
              $scormOk = 1;
           else
              $scormOk = 0;
          $description = GetDataField ($connect,"select parcours_desc_cmt from parcours where parcours_cdn = $id_parc","parcours_desc_cmt");
          $description = strip_tags(NewHtmlEntityDecode($description,ENT_QUOTES));
              if ($id_parc != 0)
              {
                 $id_ref_parc = GetDataField ($connect,"select parcours_referentiel_no from parcours where parcours_cdn = $id_parc","parcours_referentiel_no");
                 $nom_referentiel = GetDataField ($connect,"select ref_desc_cmt from referentiel where ref_cdn = $id_ref_parc","ref_desc_cmt");
                 $desc = str_replace ("'","\'",$nom_referentiel);
                 $nom_createur = GetDataField ($connect,"select util_nom_lb from utilisateur where util_cdn = $createur","util_nom_lb");
                 $prenom_createur = GetDataField ($connect,"select util_prenom_lb from utilisateur where util_cdn = $createur","util_prenom_lb");
              }
              else
              {
                 $desc = $mess_gp_nolien_ref;
                 $nom_createur = $mess_gen_formagri;
                 $prenom_createur = "";
                 $nom_referentiel = str_replace("$c2","$c1",$mess_gp_nolien_ref);
              }
              $nom = strip_tags($nom);
              $nom_parc=$nom;
              $req_min= mysql_query("select presc_datedeb_dt from prescription$Ext where
                                     presc_utilisateur_no = $num_app and
                                     presc_parc_no = $id_parc AND
                                     presc_grp_no = $numero_groupe
                                     order by presc_datedeb_dt asc");
              $date_min = mysql_result($req_min,0,"presc_datedeb_dt");
              $nb_jours_req_min = mysql_query ("select TO_DAYS('$date_min')");
              $req_max= mysql_query("select presc_datefin_dt from prescription$Ext where
                                     presc_utilisateur_no = $num_app and
                                     presc_parc_no = $id_parc AND
                                     presc_grp_no = $numero_groupe
                                     order by presc_datefin_dt asc");
              $nbr_max = mysql_num_rows($req_max);
              $date_max = mysql_result($req_max,$nbr_max-1,"presc_datefin_dt");
              $nb_jours_req_max = mysql_query ("select TO_DAYS('$date_max')");
              if ($scormOk == 1)
                 $seq_parc_query = mysql_query ("SELECT sum(sequence.seq_duree_nb) from sequence,sequence_parcours where sequence_parcours.seqparc_parc_no = $id_parc and  sequence.seq_cdn = sequence_parcours.seqparc_seq_no");
              else
                 $seq_parc_query = mysql_query ("select sum(activite.act_duree_nb) from
                                                 suivi2$Ext,prescription$Ext,suivi1$Ext,activite where
                                                 activite.act_seq_no = suivi2$Ext.suiv2_seq_no and
                                                 suivi2$Ext.suiv2_seq_no = prescription$Ext.presc_seq_no and
                                                 prescription$Ext.presc_utilisateur_no=$num_app and
                                                 suivi1$Ext.suivi_act_no = activite.act_cdn and
                                                 prescription$Ext.presc_parc_no=$id_parc and
                                                 prescription$Ext.presc_grp_no=$numero_groupe and
                                                 suivi1$Ext.suivi_grp_no = $numero_groupe and
                                                 suivi2$Ext.suiv2_grp_no = $numero_groupe and
                                                 suivi1$Ext.suivi_utilisateur_no = $num_app and
                                                 suivi2$Ext.suiv2_utilisateur_no = $num_app and
                                                 prescription$Ext.presc_utilisateur_no = $num_app");
              $duree_parc = mysql_result($seq_parc_query,0);
              $duree_parc = duree_calc($duree_parc);
              $ch_date_deb = explode ("-",$date_min);
              $date_min = "$ch_date_deb[2]/$ch_date_deb[1]/$ch_date_deb[0]";
              $ch_date_fin = explode ("-",$date_max);
              $date_max = "$ch_date_fin[2]/$ch_date_fin[1]/$ch_date_fin[0]";
              if ($etat_parc == "TERMINE")
                 $parcsuiv = $mess_lanc_termine;
              elseif ($etat_parc == "A FAIRE")
                 $parcsuiv = $mess_lanc_afaire;
              elseif ($etat_parc == "EN COURS")
                 $parcsuiv = $mess_lanc_encours;
              elseif ($etat_parc == "ATTENTE")
                 $parcsuiv = $mess_lanc_attente;
              $parc_suivi = $parcsuiv;
  //Sequences a faire dans le parcours déroulé
   $ouvrir = 'parcours';
   $parc_ouvert = $id_parc;
       if ($utilisateur &&  $typ_user == "TUTEUR")
       {
          $seq_query = mysql_query ("SELECT DISTINCT suiv2_etat_lb,suiv2_seq_no FROM
                                     suivi2$Ext,prescription$Ext WHERE
                                     suivi2$Ext.suiv2_seq_no = prescription$Ext.presc_seq_no AND
                                     prescription$Ext.presc_utilisateur_no=$utilisateur AND
                                     prescription$Ext.presc_parc_no=$id_parc AND
                                     prescription$Ext.presc_grp_no = $numero_groupe AND
                                     suivi2$Ext.suiv2_grp_no = $numero_groupe and
                                     suiv2_utilisateur_no = $utilisateur
                                     ORDER BY prescription$Ext.presc_datefin_dt,prescription$Ext.presc_ordre_no,suivi2$Ext.suiv2_ordre_no ASC");
          $num_app = $utilisateur;
       }elseif ($utilisateur && ($typ_user == "ADMINISTRATEUR"  || $typ_user == "FORMATEUR_REFERENT" || $typ_user == "RESPONSABLE_FORMATION")){
          $seq_query = mysql_query ("SELECT DISTINCT suiv2_etat_lb,suiv2_seq_no FROM
                                     suivi2$Ext,prescription$Ext WHERE
                                     suivi2$Ext.suiv2_seq_no = prescription$Ext.presc_seq_no AND
                                     prescription$Ext.presc_utilisateur_no=$utilisateur AND
                                     prescription$Ext.presc_parc_no=$id_parc AND
                                     prescription$Ext.presc_grp_no = $numero_groupe AND
                                     suivi2$Ext.suiv2_grp_no = $numero_groupe and
                                     suiv2_utilisateur_no = $utilisateur
                                     ORDER BY prescription$Ext.presc_datefin_dt,prescription$Ext.presc_ordre_no,suivi2$Ext.suiv2_ordre_no ASC");
          $num_app = $utilisateur;
       }
       elseif(!$utilisateur)
       {
          $seq_query = mysql_query ("SELECT DISTINCT suiv2_etat_lb,suiv2_seq_no FROM
                                     suivi2$Ext,prescription$Ext WHERE
                                     suivi2$Ext.suiv2_seq_no = prescription$Ext.presc_seq_no AND
                                     prescription$Ext.presc_parc_no=$id_parc AND
                                     prescription$Ext.presc_grp_no = $numero_groupe AND
                                     suivi2$Ext.suiv2_grp_no = $numero_groupe and
                                     suiv2_utilisateur_no = $id_user
                                     ORDER BY prescription$Ext.presc_datefin_dt,prescription$Ext.presc_ordre_no,suivi2$Ext.suiv2_ordre_no ASC");
          $num_app = $id_user;
       }
       $nb_seq = mysql_num_rows ($seq_query);
       if ($utilisateur)
          $num_app = $utilisateur;
       else
          $num_app = $id_user;
       $compteur_fin_seq=0;
       $i = 0;
       while ($i != $nb_seq)
       {
              $compte++;
              $seq = mysql_result ($seq_query,$i,"suiv2_seq_no");
              $etat = mysql_result ($seq_query,$i,"suiv2_etat_lb");
              $type_sequence = GetDataField ($connect,"SELECT seq_type_lb from sequence where seq_cdn = '$seq'","seq_type_lb");
              if (strstr($type_sequence,"SCORM"))
                 $scormOk = 1;
              else
                 $scormOk = 0;
              $desc_seq = GetDataField ($connect,"select seq_desc_cmt from sequence where seq_cdn=$seq","seq_desc_cmt");
              $desc_seq = strip_tags(NewHtmlEntityDecode($desc_seq,ENT_QUOTES));
              $formateur = GetDataField ($connect,"select presc_formateur_no from prescription$Ext where
                                                   presc_seq_no = $seq and
                                                   presc_utilisateur_no = $num_app AND
                                                   presc_grp_no = $numero_groupe","presc_formateur_no");
              $prescripteur = GetDataField ($connect,"select presc_prescripteur_no from prescription$Ext where
                                                      presc_seq_no = $seq and
                                                      presc_utilisateur_no = $num_app AND
                                                      presc_grp_no = $numero_groupe","presc_prescripteur_no");
              $nom_form=GetDataField ($connect,"select util_nom_lb from utilisateur where util_cdn='$formateur'","util_nom_lb");
              $prenom_form=GetDataField ($connect,"select util_prenom_lb from utilisateur where util_cdn='$formateur'","util_prenom_lb");
              $prerequis="";
              $prereq_query = mysql_query ("select * from prerequis where prereq_seq_no = $seq");
              $nb_prereq = mysql_num_rows ($prereq_query);
              if ($etat == "TERMINE")
                 $compteur_fin_seq++;
              if ($compteur_fin_seq == $nb_seq && $i+1 == $nb_seq)
                 $fin_parc=1;
              if ($nb_prereq != 0) {
                $nb_proposable = 0;
                $jj = 0;
                while ($jj < $nb_prereq)
                {
                  //on raisonne selon le type de condition
                  $type_condition = mysql_result ($prereq_query,$jj,"prereq_typcondition_lb");
                  if ($type_condition == 'SEQUENCE') {
                      $condition = mysql_result ($prereq_query,$jj,"prereq_seqcondition_no");
                      $etat_seq_req = GetDataField ($connect,"select suiv2_etat_lb from suivi2$Ext where
                                                              suiv2_seq_no = $condition and
                                                              suiv2_utilisateur_no = $num_app and
                                                              suiv2_grp_no = $numero_groupe","suiv2_etat_lb");
                      if ($etat_seq_req != 'TERMINE')  {
                          $motif = $mess_gp_seq_preq;
                          $proposable = 0;
                          $prerequis = "";
                      }else {
                        $nb_proposable++;
                      }
                  }

                  //on a besoin du numero de l'activite pour recuperer les notes
                  if ($type_condition == 'ACTIVITE') {
                      $condition = mysql_result ($prereq_query,$jj,"prereq_actcondition_no");
                      $etat_act_req = GetDataField ($connect,"select suivi_etat_lb from suivi1$Ext where
                                                              suivi_act_no = $condition and
                                                              suivi_utilisateur_no = $num_app and
                                                              suivi_grp_no = $numero_groupe","suivi_etat_lb");
                      if ($etat_act_req != 'TERMINE') {
                          $motif = $mess_gp_aut_act;
                          $proposable = 0;
                          $prerequis = "";//$mess_act_prq." <B>".str_replace("'","|",$act_prereq)."</B> ".$mess_no_fin;
                      }else {
                        $nb_proposable++;
                      }
                  }

                  if ($type_condition == 'NOTE') {
                     $condition = mysql_result ($prereq_query,$jj,"prereq_actcondition_no");
                      $etat_act_req = GetDataField ($connect,"select suivi_etat_lb from suivi1$Ext where
                                                              suivi_act_no = $condition and
                                                              suivi_utilisateur_no = $num_app and
                                                              suivi_grp_no = $numero_groupe","suivi_etat_lb");
                     if ($etat_act_req != 'TERMINE') {
                          $motif = $mess_gp_act_preq;
                          $proposable = 0;
                     }else{
                      $note_min = mysql_result ($prereq_query,$jj,"prereq_notemin_nb1");
                      $note_max = mysql_result ($prereq_query,$jj,"prereq_notemax_nb1");
                      $note_obtenue = GetDataField ($connect,"select suivi_note_nb1  from suivi1$Ext where
                                                              suivi_act_no = $condition and
                                                              suivi_utilisateur_no = $num_app and
                                                              suivi_grp_no = $numero_groupe"  ,"suivi_note_nb1");
                      if (($note_obtenue < $note_min || $note_obtenue > $note_max) || $note_obtenue == "acquis")  {
                        $motif = $mess_gp_note;
                        $proposable = 0;
                        $prerequis = "";//$mess_note_prq;
                      }else{
                        $nb_proposable++;
                      }
                    }
                  }
              $jj++;
              }
              if ($nb_proposable == $nb_prereq){
                 $proposable=1;
                 $prerequis="OK";
              }else
                 $proposable = 0;
            } //fin if ($nb_prereq !=0)
               $act_query = mysql_query ("select * from activite where act_seq_no = $seq order by act_ordre_nb");
               $Nb_act_seq = mysql_num_rows ($act_query);
               $aq = 0;
               $encore = 0;
            if ($utilisateur > 0){
              $date_deb = GetDataField ($connect,"select presc_datedeb_dt from prescription$Ext where
                                                  presc_seq_no = $seq and
                                                  presc_utilisateur_no = $utilisateur and
                                                  presc_grp_no = $numero_groupe","presc_datedeb_dt");
              $date_fin = GetDataField ($connect,"select presc_datefin_dt from prescription$Ext where
                                                  presc_seq_no = $seq and
                                                  presc_utilisateur_no = $utilisateur and
                                                  presc_grp_no = $numero_groupe","presc_datefin_dt");
            }else{
              $date_deb = GetDataField ($connect,"select presc_datedeb_dt from prescription$Ext where
                                                  presc_seq_no = $seq and
                                                  presc_utilisateur_no = $id_user and
                                                  presc_grp_no = $numero_groupe","presc_datedeb_dt");
              $date_fin = GetDataField ($connect,"select presc_datefin_dt from prescription$Ext where
                                                  presc_seq_no = $seq and
                                                  presc_utilisateur_no = $id_user and
                                                  presc_grp_no = $numero_groupe","presc_datefin_dt");
            }
              $today = date("Y/n/d");
              //On compare la date de deb a la date d'aujourd'hui pour savoir s'il peut encore faire la sequence
             //Pour simplifier le test, on selctionne le nombre de jours passes depuis an 0 pour $today et $date_deb
              $nb_date_fin_query = mysql_query ("select TO_DAYS('$date_fin')");
              $nb_date_fin = mysql_result ($nb_date_fin_query,0);
              $nb_date_deb_query = mysql_query ("select TO_DAYS('$date_deb')");
              $nb_date_deb = mysql_result ($nb_date_deb_query,0);
              $nb_today_query = mysql_query ("select TO_DAYS('$today')");
              $nb_today = mysql_result ($nb_today_query,0);
              for ($nn = 1;$nn < 10;$nn++){
                if ($nb_date_fin == ($nb_today+$nn) && $etat != "TERMINE") {
                   $avertisseur = 1;
                }
              }
              $depasse=0;

              $ch_date_deb = explode ("-",$date_deb);
              $date_deb = "$ch_date_deb[2]/$ch_date_deb[1]/$ch_date_deb[0]";
              $nom = GetDataField ($connect,"select seq_titre_lb from sequence where seq_cdn=$seq","seq_titre_lb");
              $nom = strip_tags($nom);
              $nom_seq = $nom;
              if ($utilisateur > 0){
                  $date_fin = GetDataField ($connect,"select presc_datefin_dt from prescription$Ext where
                                                      presc_seq_no = $seq and
                                                      presc_utilisateur_no = $utilisateur and
                                                      presc_grp_no = $numero_groupe","presc_datefin_dt");
                  if ($scormOk == 1)
                    $duree_seq = GetDataField ($connect,"SELECT seq_duree_nb from sequence where seq_cdn = '$seq'","seq_duree_nb");
                  else{
                    $duree_sequence = mysql_query ("select sum(activite.act_duree_nb) from activite,suivi1$Ext where
                                                    activite.act_cdn = suivi1$Ext.suivi_act_no and
                                                    activite.act_seq_no = $seq and
                                                    suivi1$Ext.suivi_grp_no =$numero_groupe and
                                                    suivi1$Ext.suivi_utilisateur_no =$utilisateur");
                    $duree_seq = mysql_result($duree_sequence,0);
                  }
              }else{
                  $date_fin = GetDataField ($connect,"select presc_datefin_dt from prescription$Ext where
                                                      presc_seq_no = $seq and
                                                      presc_utilisateur_no = $id_user and
                                                      presc_grp_no = $numero_groupe","presc_datefin_dt");
                  if ($scormOk == 1)
                    $duree_seq = GetDataField ($connect,"SELECT seq_duree_nb from sequence where seq_cdn = '$seq'","seq_duree_nb");
                  else{
                    $duree_sequence = mysql_query ("select sum(activite.act_duree_nb) from activite,suivi1$Ext where
                                                    activite.act_cdn = suivi1$Ext.suivi_act_no and
                                                    activite.act_seq_no=$seq and
                                                    suivi1$Ext.suivi_grp_no =$numero_groupe and
                                                    suivi1$Ext.suivi_utilisateur_no =$id_user");
                    $duree_seq = mysql_result($duree_sequence,0);
                  }
              }
              $ch_date_fin = explode ("-",$date_fin);
              $date_fin = "$ch_date_fin[2]/$ch_date_fin[1]/$ch_date_fin[0]";
              if ($nb_date_fin < $nb_today && $etat != "TERMINE"){
                 $depasse=1;
                 $motif = str_replace("$c2","$c1","$mess_acces_depasse : $date_fin");
              }
              if ($nb_date_fin < $nb_today && $etat == "TERMINE")
                 $autorise=1;
              if ($nb_date_deb <= $nb_today)
                  $visible = 1;
              else
                  $visible = 0;
                  //Besoin pour ouvrir les activites a partir de la sequence
            $id_ref = GetDataField ($connect,"select seqref_referentiel_no from sequence_referentiel where seqref_seq_no = $seq","seqref_referentiel_no");
            if ($id_ref > 0){
                   $nom_referentiel = GetDataField ($connect,"select ref_desc_cmt from referentiel where ref_cdn = $id_ref","ref_desc_cmt");
                   if ($nom_referentiel == "")
                      $nom_referentiel = GetDataField ($connect,"select ref_nomabrege_lb from referentiel where ref_cdn = $id_ref","ref_nomabrege_lb");
            }
            $ii=$i-1;
            if ($nom_user == "Test")
               $proposable = 1;
            if (($autorise == 1 || $depasse != 1) && (($nb_prereq > 0 && $proposable == 1) || $nb_prereq == 0) && ((($marqueur == 1 && $marqueur[$ii] != 1) || $encore ==0) || ($utilisateur))){
               $accord = 1;
               if ((isset($deroule[$i]) && $deroule[$i] == 1 && $id_seq == $seq) || ($seq == $seq_ouverte && $seq_ouverte > 0) && $switch != 1)
                  $deroulee[$i] = 0;
               else
                  $deroulee[$i] = 1;
               if ($visible == 0){
                  $fichier_csv .=  str_replace("$c2","$c1","$motif");
               }
               if ($id_ref_seq > 0){
                  $desc_ref = GetDataField ($connect,"select ref_desc_cmt  from referentiel where ref_cdn = '$id_ref_seq'","ref_desc_cmt");
                  if ($desc_ref == "")
                     $desc_ref = GetDataField ($connect,"select ref_nomabrege_lb from referentiel where ref_cdn = $id_ref_seq","ref_nomabrege_lb");
               }
            }else{
               $accord = 0;
               $id_ref_seq = GetDataField ($connect,"select seqref_referentiel_no from sequence_referentiel where seqref_seq_no = '$seq'","seqref_referentiel_no");
               if ($id_ref_seq > 0){
                  $desc_ref = GetDataField ($connect,"select ref_desc_cmt from referentiel where ref_cdn = '$id_ref_seq'","ref_desc_cmt");
                  if ($desc_ref == "")
                     $desc_ref = GetDataField ($connect,"select ref_nomabrege_lb from referentiel where ref_cdn = $id_ref_seq","ref_nomabrege_lb");
               }
            }
            if ($utilisateur > 0){
               $prescripteur = GetDataField ($connect,"select presc_prescripteur_no from prescription$Ext where
                                                       presc_seq_no = $seq and
                                                       presc_utilisateur_no = $utilisateur and
                                                       presc_grp_no = $numero_groupe","presc_prescripteur_no");
               $formateur = GetDataField ($connect,"select presc_formateur_no from prescription$Ext where
                                                    presc_seq_no = $seq and
                                                    presc_utilisateur_no = $utilisateur and
                                                    presc_grp_no = $numero_groupe","presc_formateur_no");
               $sans_prereq = mysql_query ("select prereq_seq_no from prerequis where prereq_seqcondition_no = $seq");
               $nb_sans_prereq = mysql_num_rows ($sans_prereq);
               $signal=0;
               if ($nb_sans_prereq > 0){
                  $sequence = mysql_result($sans_prereq,0,"prereq_seq_no");
                  $seq_cible = mysql_query ("select suiv2_seq_no from suivi2$Ext where
                                             suiv2_utilisateur_no = $utilisateur and
                                             suiv2_grp_no = $numero_groupe");
                  $nb_seq_cible = mysql_num_rows($seq_cible);
                  $nsc=0;
                  while ($nsc < $nb_seq_cible){
                      $sequence_cible = mysql_result($seq_cible,$nsc,"suiv2_seq_no");
                      if ($sequence == $sequence_cible){
                        $signal++;
                      }
                    $nsc++;
                  }
               }
            }

    $ouvrir = '';
    $seq_ouverte = $seq;
    if ($typ_user == 'APPRENANT'){
      if ($scormOk == 1)
         $act_query = mysql_query ("select * from scorm_module,scorm_util_module$Ext WHERE
                                    scorm_module.mod_seq_no = $seq AND
                                    scorm_util_module$Ext.mod_module_no = scorm_module.mod_cdn AND
                                    scorm_util_module$Ext.user_module_no = $id_user AND
                                    scorm_util_module$Ext.mod_grp_no = $numero_groupe
                                    order by scorm_module.mod_cdn");
      else
         $act_query = mysql_query ("select * from activite,suivi1$Ext WHERE
                                    (activite.act_seq_no = $seq OR suivi_seqajout_no = $seq) AND
                                    suivi1$Ext.suivi_act_no = activite.act_cdn AND
                                    suivi1$Ext.suivi_utilisateur_no = $id_user AND
                                    suivi1$Ext.suivi_grp_no = $numero_groupe
                                    order by suivi1$Ext.suivi_seqajout_no,activite.act_ordre_nb");
    }else{
      if ($scormOk == 1)
         $act_query = mysql_query ("select * from scorm_module,scorm_util_module$Ext WHERE
                                    scorm_module.mod_seq_no = $seq AND
                                    scorm_util_module$Ext.mod_module_no = scorm_module.mod_cdn AND
                                    scorm_util_module$Ext.user_module_no = $utilisateur AND
                                    scorm_util_module$Ext.mod_grp_no = $numero_groupe
                                    order by scorm_module.mod_cdn");
      else
         $act_query = mysql_query ("select * from activite,suivi1$Ext WHERE
                                    (activite.act_seq_no = $seq OR suivi_seqajout_no = $seq) AND
                                    suivi1$Ext.suivi_act_no = activite.act_cdn AND
                                    suivi1$Ext.suivi_utilisateur_no = $utilisateur AND
                                    suivi1$Ext.suivi_grp_no = $numero_groupe
                                    order by suivi1$Ext.suivi_seqajout_no,activite.act_ordre_nb");
    }
    $Nb_act_seq = mysql_num_rows ($act_query);
    if ($Nb_act_seq == 0)
        $fichier_csv .=  $msq_noact;
    else {
       if ($utilisateur > 0){
         $id_nom = GetDataField ($connect,"select util_nom_lb  from utilisateur where util_cdn = $utilisateur","util_nom_lb");
         $id_prenom = GetDataField ($connect,"select util_prenom_lb  from utilisateur where util_cdn = $utilisateur","util_prenom_lb");
         $id_app = $utilisateur;
       }else{
         $id_app = $id_user;}
         $suivi_etat_seq = GetDataField ($connect,"select suiv2_etat_lb from suivi2$Ext where
                                                   suiv2_seq_no = '$seq' and
                                                   suiv2_utilisateur_no = $id_app and
                                                   suiv2_grp_no = $numero_groupe","suiv2_etat_lb");
         $titre_sequence = GetDataField ($connect,"select seq_titre_lb from sequence where seq_cdn = '$seq'","seq_titre_lb");
         $titre_sequence = strip_tags($titre_sequence);
         $nom_sequence = GetDataField ($connect,"select seq_desc_cmt from sequence where seq_cdn = '$seq'","seq_desc_cmt");
         $nom_sequence = NewHtmlEntityDecode($nom_sequence,ENT_QUOTES);
         if ($nom_sequence == '')
           $nom_sequence = $msq_no_decrite;
           $fichier_csv .= "\n$msq_parc : ".str_replace("$c2","$c1",$nom_parc)."$c2$c2$c2$c2$c2$c2\n$msq_seq : ".str_replace("$c2","$c1",$nom_seq)."$c2$c2$c2$c2$c2$c2\n";
           $fichier_csv .=  "$msq_acts$c2$msq_aff_cons$c2$msq_duree_seq$c2$msq_ress_assoc$c2$msq_etat$c2$msq_tit_label$c2"."Nombre d'accès".$c2."Temps consacré".$c2."Dates d'accès"."$c2$mess_signat\n";
             //On selectionne le type d'utilisateur (uniquement si ce n'est pas un apprenant ki vient consulter ses activites)car seul l'administrateur et l'auteur de l'activite ont le droit de modifier ou supprimer celle-ci
          if ($apprenant == 1)
              $typ_user = 'NULL';
        if ($scormOk == 0){
          $ii = 0;
          while ($ii != $Nb_act_seq) {
               $id_suivi = mysql_result ($act_query,$ii,"suivi_cdn");
               $id = mysql_result ($act_query,$ii,"act_cdn");
               $nom = mysql_result ($act_query,$ii,"act_nom_lb");
               $ordre[$ii] = mysql_result ($act_query,$ii,"act_ordre_nb");
               $consigne = str_replace(',',' ',clean_text(strip_tags(html_entity_decode(html_entity_decode(DelAmp(mysql_result ($act_query,$ii,"act_consigne_cmt")),ENT_QUOTES,'UTF-8'),ENT_QUOTES,'UTF-8'))));
               $pass_mult = mysql_result ($act_query,$ii,"act_passagemult_on");
               $acquit = mysql_result ($act_query,$ii,"act_acquittement_lb");
               $devoir = mysql_result ($act_query,$ii,"act_devoirarendre_on");
               $notation = mysql_result ($act_query,$ii,"act_notation_on");
               $flag = mysql_result ($act_query,$ii,"act_flag_on");
               if (empty($consigne))
                 $consigne = $msq_aucune;
               //Séléction ressource
               $id_ress = mysql_result ($act_query,$ii,"act_ress_no");
               if ($id_ress > 0){
                  $lien_ressource = GetDataField ($connect,"select ress_url_lb from ressource_new where ress_cdn = $id_ress","ress_url_lb");
                  if (strstr($lien_ressource,"http://www.editions.educagri.fr/educagriNet"))
                    $lien_ressource.="&url=$url_ress&auth_cdn=$auth_cdn";
                  $lien_ress = str_replace("&","%",$lien_ressource);
                  $lr = 0;
                  if (strstr($lien_ress,"qcm.php"))
                     $lr = 1;
                  $non_affic_ress_lien = 0;
               }
               if ($id_ress == 0){
                   $non_affic_ress_lien = 1;
                   $ressource = $msq_aucune;
               }else {
                  $ressource = GetDataField ($connect,"select ress_titre from ressource_new where ress_cdn = $id_ress","ress_titre");
                //Dans le cas ou la ressource a ete supprimee
                   if ($ressource == ''){
                       $ressource = "$msq_ress_sup";
                       $non_affic_ress_lien = 1;
                   }else {
                       $typ_ress = GetDataField ($connect,"select ress_support from ressource_new where ress_cdn = $id_ress","ress_support");
                       $typ_ress = strtoupper ($typ_ress);  }
                } //fin else

               //Pour meme raison que typ_user, on selectionne auteur
               $id_auteur = GetDataField ($connect,"select seq_auteur_no from sequence where seq_cdn = $seq","seq_auteur_no");
               if ($utilisateur > 0)
                 $id_formateur = GetDataField ($connect,"select presc_formateur_no from prescription$Ext where
                                                         presc_seq_no = $seq and
                                                         presc_utilisateur_no=$utilisateur and
                                                         presc_grp_no = $numero_groupe","presc_formateur_no");

               //Si c'est l'apprenant qui consulte les seq et activites a faire
               //On doit savoir s'il doit effectuer activites ds l'ordre ou non pour activer ou non le lien
               if ($apprenant == 1) {
                   $id_apprenant = $id_user;
                   $ordre_act = GetDataField ($connect,"select seq_ordreact_on from sequence where seq_cdn = $seq","seq_ordreact_on");
                  //On connait l'ordre de l'activite courante  ($ordre)
                   if ($ordre[$ii] > 1 && $ii > 0) {
                       //On recupere l'etat l'activite precedente
                       $ordre_prec = $ordre[$ii-1];
                       $act_prec =   GetDataField ($connect,"select act_cdn from activite where act_ordre_nb = $ordre_prec and act_seq_no = $seq","act_cdn");
                       $etat_act = GetDataField ($connect,"select suivi_etat_lb from suivi1$Ext where
                                                           suivi_act_no = $act_prec and
                                                           suivi_utilisateur_no = $id_apprenant and
                                                           suivi_grp_no = $numero_groupe","suivi_etat_lb");
                    }
               } //fin if ($apprenant == 1)
               $duree = mysql_result ($act_query,$ii,"act_duree_nb");
               //On passe certaines infos par URL, cela evite de refaire une requete
               //on affiche le lien pour un apprenant que si activite precedente terminee
               // GESTION DE L'ORDRE ET DE L'ETAT DES ACTIVITES POUR UN APPRENANT OU SON FORMATEUR
               if ($etat_act == 'TERMINE' || $ordre[$ii] != 0 || $typ_user != "NULL" || $ordre_act == "NON"){
                   $lien= $lien_retour;
                   $lien = urlencode($lien);
                   $nom_act= str_replace("'","\'",$nom);
                   if ($pass_mult == "OUI")
                     $aff_pass_mult = $mess_oui;
                   elseif($pass_mult == "NON")
                     $aff_pass_mult = $mess_non;
                   if ($acquit == "FORMATEUR_REFERENT")
                     $aff_acquit = strtoupper($msq_formateur);
                   elseif ($acquit == "APPRENANT")
                     $aff_acquit = $mess_typ_app;
                   elseif ($acquit == "RESSOURCE")
                     $aff_acquit = strtoupper($msq_ress);
                   elseif ($acquit == "AUTO")
                     $aff_acquit = strtoupper($msq_auto);
                   if ($devoir == "OUI")
                     $aff_devoir = $mess_oui;
                   elseif($devoir == "NON")
                     $aff_devoir = $mess_non;
                   if ($notation == "OUI")
                     $aff_notation = $mess_oui;
                   elseif($notation == "NON")
                     $aff_notation = $mess_non;
                     $fichier_csv .=  str_replace("$c2","$c1","$nom")."$c2";
               }else
                   $fichier_csv .=  str_replace("$c2","$c1","$nom")."$c2";
               $fichier_csv .=  str_replace("$c2","$c1","$consigne")."$c2";
               $duree = duree_calc($duree);
               $fichier_csv .=  "$duree$c2";
               if ($id_ress != 0)
               {
                         if ($utilisateur > 0)
                           $actform = GetDataField ($connect,"select presc_formateur_no from prescription$Ext where
                                                              presc_seq_no = $seq and
                                                              presc_utilisateur_no = $utilisateur and
                                                              presc_grp_no = $numero_groupe","presc_formateur_no");
                         else
                           $actform = GetDataField ($connect,"select presc_formateur_no from prescription$Ext where
                                                              presc_seq_no = $seq and
                                                              presc_utilisateur_no = $id_user and
                                                              presc_grp_no = $numero_groupe","presc_formateur_no");
                        $ordre_act = GetDataField ($connect,"select seq_ordreact_on from sequence where seq_cdn = $seq","seq_ordreact_on");
                         if ($utilisateur > 0)
                           $etat_act = GetDataField ($connect,"select suivi_etat_lb from suivi1$Ext where
                                                               suivi_act_no = $id and
                                                               suivi_utilisateur_no = $utilisateur and
                                                               suivi_grp_no = $numero_groupe","suivi_etat_lb");
                         else {
                           $etat_act = GetDataField ($connect,"select suivi_etat_lb from suivi1$Ext where
                                                               suivi_act_no = $id and
                                                               suivi_utilisateur_no = $id_user and
                                                               suivi_grp_no = $numero_groupe","suivi_etat_lb");
                           $email_user = GetDataField ($connect, "select util_email_lb from utilisateur where util_cdn=$id_user","util_email_lb");
                           $nom_user=GetDataField ($connect,"select util_nom_lb from utilisateur where util_cdn='$id_user'","util_nom_lb");
                           $prenom_user=GetDataField ($connect,"select util_prenom_lb from utilisateur where util_cdn='$id_user'","util_prenom_lb");
                         }
                         $lien_retour = "http://".$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
                         if ($etat_act == "TERMINE")
                           $fin_ordre_non++;
                         if ((($etat_act == "TERMINE" && $notation == "NON") || $etat_act == "A FAIRE" || $etat_act == "EN COURS") && $ordre[$ii] >0  && ($typ_user == "NULL")) {
                          $marqueur[$ii] = 1;
                          $iii = $ii-1;
                          $lien_ressource = GetDataField ($connect,"select ress_url_lb from ressource_new where ress_cdn = $id_ress","ress_url_lb");
                          if ($lien_ressource == "")
                              $lien_ressource = "ress_virtuel.php?numero_groupe=$numero_groupe&id_ress=$id_ress&id_act=$id";
                          $lien_ress = str_replace("&","|",$lien_ressource);
                          if ((strstr($lien_ress,"ParWeb")) || (strstr($lien_ress,"parweb")) || (strstr($lien_ress,"Legweb")) || (strstr($lien_ress,"legweb")) || (strstr($lien_ress,"Tatweb"))  || (strstr($lien_ress,"tatweb")) || (strstr($lien_ress,"Qcmweb")) || (strstr($lien_ress,"qcmweb")) || (strstr($lien_ress,"Elaweb")) || (strstr($lien_ress,"elaweb")))
                             $lien_ress .= "%nom=$nom_user%prenom=$prenom_user%email=$email_user";
                          $lr = 0;
                          if (strstr($lien_ress,"qcm.php")){
                             $lien_ress .= "%id_act=$id";
                             $lr = 1;
                          }
                          if (strstr($lien_ress,"http://www.editions.educagri.fr/educagriNet"))
                             $lien_ress .="|url=$url_ress|auth_cdn=$auth_cdn";
                           if ($etat_act != "TERMINE" && $ordre_act == "OUI")
                              $pointeur++;
                           if ($ii == 0 && !$utilisateur)
                             $pointer = 1;
                           if ($Nb_act_seq == $ii+1 && $ordre_act == "OUI")
                             $fin_seq=1;
                           if ($Nb_act_seq == $ii+1 && $ordre_act == "NON" && $fin_ordre_non == $Nb_act_seq)
                             $fin_seq = 1;
                           if ($visible == 1 && (($depasse !=1 && $accord == 1) || (($depasse == 1 || $accord == 1) && $pass_mult == "OUI" && $etat_act == "TERMINE")) && (($etat_act == "TERMINE" && $notation == "NON" && $acquit != "AUTO") || ($pointeur == 1 && $ordre_act == "OUI" && ($etat_act == "A FAIRE" || ($etat_act == "EN COURS" && $pass_mult == "OUI"))) || ($login == "Test") || ($ordre_act == "NON" && ($etat_act == "A FAIRE" || ($etat_act == "EN COURS"  && $pass_mult == "OUI"))) || ($marqueur[$ii] != 1 && $etat_act == "A FAIRE"))){
                             $lien ="suivi.php?numero_groupe=$numero_groupe&hgrp=$hgrp&verif_lien_ress=1&fin_parc=$fin_parc&pointer=$pointer&id_seq=$seq&id_parc=$id_parc&fin_seq=$fin_seq&id_act=$id&lien_ress=$lien_ress";
                             if (strstr(strtolower($lien),".doc") || strstr(strtolower($lien),".xls") || strstr(strtolower($lien),".xlt") || strstr(strtolower($lien),".ppt") || strstr(strtolower($lien),".pps"))
                                $fichier_csv .= str_replace("$c2","$c1","$ressource")."$c2";
                             else{
                                $fichier_csv .= str_replace("$c2","$c1","$ressource")."$c2";
                             }
                           }else
                             $fichier_csv .= str_replace("$c2","$c1","$ressource")."$c2";
                         }else
                           $fichier_csv .= str_replace("$c2","$c1","$ressource")."$c2";
                         //fin if (($etat_act == "A FAIRE" && $ordre >0 && ($typ_user == "NULL" || $id_user == $actform)))
                         $ress_type = GetDataField ($connect,"select ress_type from ressource_new where ress_cdn = $id_ress","ress_type");
                 }else{
                      $ressource="$mess_gp_noress_ass";
                      $fichier_csv .= str_replace("$c2","$c1","$ressource")."$c2";

                 } //fin if ($id_ress != 0)
                 if ($utilisateur > 0 || $typ_user =="NULL"){
                   if ($utilisateur > 0){
                      $actform = GetDataField ($connect,"select presc_formateur_no from prescription$Ext where
                                                         presc_seq_no = $seq and
                                                         presc_utilisateur_no = $utilisateur and
                                                         presc_grp_no = $numero_groupe","presc_formateur_no");
                      $prescripteur = GetDataField ($connect,"select presc_prescripteur_no from prescription$Ext where
                                                              presc_seq_no = $seq and
                                                              presc_utilisateur_no = $utilisateur and
                                                              presc_grp_no = $numero_groupe","presc_prescripteur_no");
                      $actsuivi = GetDataField ($connect,"select suivi_etat_lb from suivi1$Ext where
                                                          suivi_act_no = $id and
                                                          suivi_utilisateur_no = $utilisateur and
                                                          suivi_grp_no = $numero_groupe","suivi_etat_lb");
                      $suivi_fichier = GetDataField ($connect,"select suivi_fichier_lb from suivi1$Ext where
                                                               suivi_act_no = $id and
                                                               suivi_utilisateur_no = $utilisateur and
                                                               suivi_grp_no = $numero_groupe","suivi_fichier_lb");
                   }else{
                      $actform = GetDataField ($connect,"select presc_formateur_no from prescription$Ext where
                                                         presc_seq_no = $seq and
                                                         presc_utilisateur_no = $id_user and
                                                         presc_grp_no = $numero_groupe","presc_formateur_no");
                      $prescripteur = GetDataField ($connect,"select presc_prescripteur_no from prescription$Ext where
                                                              presc_seq_no = $seq and
                                                              presc_utilisateur_no = $id_user and
                                                              presc_grp_no = $numero_groupe","presc_prescripteur_no");
                      $actsuivi = GetDataField ($connect,"select suivi_etat_lb from suivi1$Ext where
                                                          suivi_act_no = $id and
                                                          suivi_utilisateur_no = $id_user and
                                                          suivi_grp_no = $numero_groupe","suivi_etat_lb");
                      $suivi_fichier = GetDataField ($connect,"select suivi_fichier_lb from suivi1$Ext where
                                                               suivi_act_no = $id and
                                                               suivi_utilisateur_no = $id_user and
                                                               suivi_grp_no = $numero_groupe","suivi_fichier_lb");
                   }
                   if ($suivi_fichier != "" && strstr($suivi_fichier,"qf_"))
                     $tip_fic = substr($suivi_fichier, 0, 3);
                   if ($actsuivi == "TERMINE")
                     $actsuiv = $mess_fait;
                   elseif ($actsuivi == "A FAIRE" && $flag == 1)
                     $actsuiv = $mess_lanc_afaire;
                   elseif ($actsuivi == "EN COURS")
                     $actsuiv = $mess_lanc_encours;
                   elseif ($actsuivi == "ATTENTE"){
                     $actsuiv = $mess_lanc_attente;//formateur sur etat et apprenant sur acq/eval
                     $actsuivalid = $mess_fait;//apprenant
                   }elseif ($actsuivi == "PRESENTIEL")
                     $actsuiv = $mess_lanc_attente;
                   elseif ($actsuivi == "A FAIRE" && $flag == 0)
                     $actsuiv = $mess_trvx;
                   $etat_activite = $actsuiv;
                   if ($actsuivi == 'TERMINE' && $utilisateur > 0 && ($id_user == $actform || $id_user == $prescripteur || $id_user == $inscripteur)){
                     $nom_user=GetDataField ($connect,"select util_nom_lb from utilisateur where util_cdn='$utilisateur'","util_nom_lb");
                     $prenom_user=GetDataField ($connect,"select util_prenom_lb from utilisateur where util_cdn='$utilisateur'","util_prenom_lb");
                     $lien = "suivi.php?numero_groupe=$numero_groupe&hgrp=$hgrp&nom=$nom_user&prenom=$prenom_user&fin_parc=$fin_parc&utilisateur=$utilisateur&id_parc=$id_parc&id_act=$id&parc=$id_parc&id_seq=$seq&vp=1&suivi_etat_seq=$suivi_etat_seq&modif_etat=1&visible=$visible&accord=$accord&prq=$prerequis&depasse=$depasse&proposable=$proposable";
                     $lien = urlencode($lien);
                     $fichier_csv .=  "$etat_activite$c2";
                   }else
                     $fichier_csv .=  "$etat_activite$c2";
                   if ($utilisateur > 0)
                     $nom_app = GetDataField ($connect,"select util_nom_lb  from utilisateur where util_cdn = $utilisateur","util_nom_lb");
                   if ($utilisateur > 0)
                     $prenom_app = GetDataField ($connect,"select util_prenom_lb  from utilisateur where util_cdn = $utilisateur","util_prenom_lb");
                 }//fin if ($origine == "liste")
                 if ($ii == 0 && $utilisateur == "")
                    $pointer = 1;
                 if ($Nb_act_seq == $ii+1 && $ordre_act == "OUI")
                    $fin_seq=1;
                 if ($Nb_act_seq == $ii+1 && $ordre_act == "NON" && $fin_ordre_non == $Nb_act_seq)
                   $fin_seq = 1;
                 $actvalid = GetDataField ($connect,"select act_acquittement_lb from activite where act_seq_no = $seq and act_cdn = $id","act_acquittement_lb");
                 if ($utilisateur > 0)
                    $actnote = GetDataField ($connect,"select suivi_note_nb1 from suivi1$Ext where
                                                       suivi_act_no = $id and
                                                       suivi_utilisateur_no= $utilisateur and
                                                       suivi_grp_no = $numero_groupe","suivi_note_nb1");
                 $ress_type = GetDataField ($connect,"select ress_type from ressource_new where ress_cdn = $id_ress","ress_type");
                 if (($id_user == $actform  && $actsuivi == "PRESENTIEL" && ($actvalid == "FORMATEUR_REFERENT" ||
                      $actvalid == "APPRENANT")) || (($id_user == $actform)  && ($actsuivi == "ATTENTE" ||
                      ($actsuivi == "EN COURS" && $actvalid == "APPRENANT"))) ||
                      (($actvalid == "FORMATEUR_REFERENT" || $actvalid == "APPRENANT") && $apprenant == 1 && $actsuivi == "EN COURS") ||
                      ($pass_mult == "OUI" && ($actsuivi == "EN COURS") && $pointeur == 1))
                 {
                       $fichier_csv .=  " $c2";
                 }
                 else
                 {
                    if ($actsuivi == "TERMINE" && $utilisateur == "")
                    {
                       $note = GetDataField ($connect,"select suivi_note_nb1 from suivi1$Ext where
                                                       suivi_act_no = $id and
                                                       suivi_utilisateur_no = $id_user and
                                                       suivi_grp_no = $numero_groupe","suivi_note_nb1");
                       $note = str_replace("/",". sur ",$note);
                       if ($note == "Acquis")
                         $note = $mess_suivi_acq;
                       elseif ($note == "Non acquis")
                         $note = $mess_suivi_noacq;
                       $no_suivi = GetDataField ($connect,"select suivi_cdn from suivi1$Ext where
                                                           suivi_act_no = $id and
                                                           suivi_utilisateur_no = $id_user and
                                                           suivi_grp_no = $numero_groupe","suivi_cdn");
                       if ($note !="" && (($notation == "OUI" && $devoir == "OUI" && $actvalid == "FORMATEUR_REFERENT") || $tip_fic != "")){
                         $fichier_csv .=  "$note$c2";
                       }elseif ($note != "" && ($notation == "OUI" || ($notation == "NON" && $suivi_fichier != '')) && $devoir == "NON")
                         $fichier_csv .=  "$note$c2";
                       else
                         $fichier_csv .=  "$msq_valide$c2";
                    }
                    elseif (($id_user == $actform || $id_user == $prescripteur || $id_user == $inscripteur || $typ_user == "ADMINISTRATEUR")  && $actsuivi == "TERMINE" && $utilisateur)
                    {
                       $note = GetDataField ($connect,"select suivi_note_nb1 from suivi1$Ext where
                                                       suivi_act_no = $id and
                                                       suivi_utilisateur_no = $utilisateur and
                                                       suivi_grp_no = $numero_groupe","suivi_note_nb1");
                       $note = str_replace("/"," sur ",$note);
                       if ($note == "Acquis")
                         $note = $mess_suivi_acq;
                       elseif ($note == "Non acquis")
                         $note = $mess_suivi_noacq;
                       $no_suivi = GetDataField ($connect,"select suivi_cdn from suivi1$Ext where
                                                           suivi_act_no = $id and
                                                           suivi_utilisateur_no = $utilisateur and
                                                           suivi_grp_no = $numero_groupe","suivi_cdn");
                       if ($note != "" && (($notation == "OUI" && $devoir == "OUI" && $actvalid == "FORMATEUR_REFERENT") || $tip_fic != ""))
                         $fichier_csv .= "$note$c2";
                       elseif ($note != "" && ($notation == "OUI" || ($notation == "NON" && $suivi_fichier != '')) && $devoir == "NON")
                         $fichier_csv .=  "$note$c2";
                       else
                         $fichier_csv .=  "$msq_valide$c2";
                    }
                    elseif ($actvalid == "APPRENANT" && $apprenant == 1 && $actsuivi == "TERMINE" && $utilisateur == "")
                       $fichier_csv .=  "$msq_valide$c2";
                    else
                       $fichier_csv .=  " $c2";
                 }
                 $deroule[$i]=0;
                 $traquer = AffiDureeAct($id,$scormOk,$id_app,$numero_groupe);
                 $tabTrq = explode('|',$traquer);
                 $nbTrq = $tabTrq[0];
                 $dureeActivite = $tabTrq[1];
                 $dureeTotale += $dureeActivite;
                 $fichier_csv .=  $nbTrq.$c2.AfficheDureeAN($dureeActivite,$nbTrq).$c2.
                                  '"'.str_replace('<br />',chr(13).chr(10),$tabTrq[2]).'"'."\n";
               $ii++;
               }// while ($ii != $Nb_act_seq) {
          }
          elseif ($scormOk == 1)
          {
              $ii = 0;
              $code = array();
              $id_act_seq = array();
              while ($ii != $Nb_act_seq)
              {
                  $affiche_scorm ='';
                  $id = mysql_result ($act_query,$ii,"mod_cdn");
                  $code[$id]=$ii;
                  $nom = mysql_result ($act_query,$ii,"mod_titre_lb");
                  $description = strip_tags(html_entity_decode(mysql_result ($act_query,$ii,"mod_desc_cmt"),ENT_QUOTES,'ISO-8859-1'));
                  $motclef = strip_tags(html_entity_decode(mysql_result ($act_query,$ii,"mod_motclef_lb"),ENT_QUOTES,'ISO-8859-1'));
                  $numero = mysql_result ($act_query,$ii,"mod_numero_lb");
                  $launch = mysql_result ($act_query,$ii,"mod_launch_lb");
                  $duree = mysql_result ($act_query,$ii,"mod_duree_nb");
                  $prerequis = mysql_result ($act_query,$ii,"mod_prereq_lb");
                  $score_max = mysql_result ($act_query,$ii,"mod_masteryscore");
                  $maxtime = mysql_result ($act_query,$ii,"mod_maxtimeallowed");
                  $status = mysql_result ($act_query,$ii,"lesson_status");
                  if ($status == "COMPLETED" || $status == "PASSED")
                     $status = $mess_fait;
                  elseif ($status == "NOT ATTEMPTED")
                     $status = $mess_lanc_afaire;
                  elseif ($status == "INCOMPLETE")
                     $status = $mess_lanc_encours;
                  elseif ($status == "FAILED")
                     $status = $mess_echec;//formateur sur etat et apprenant sur acq/eval
                   elseif ($status == "BROWSED" || $status == "UNKNOWN")
                     $status = $mess_vu;
                 if ($maxtime != '' && strlen($maxtime) > 9){
                    if (strstr($maxtime,'t') || strstr($maxtime,'T'))
                       $maxtime = substr($maxtime,13);
                    $liste_duree = explode(":",$maxtime);
                    $maxtime = ($liste_duree[0]*60)+$liste_duree[1];
                 }
                 if ($prerequis != ""){
                    $prereq1 = GetDataField ($connect,"select mod_cdn from scorm_module where mod_numero_lb = \"$prerequis\"","mod_cdn");
                    If (isset($code[$prereq1]))
                       $prereq = $code[$prereq1]+1;
                 }else
                    $prereq = '';
                 $l = $ii+1;
                 if ($launch == '')
                 {
                    $affiche_scorm = str_replace("$c2","$c1",trim($nom))."$c2$c2$c2$c2$c2$c2$c2";
                 }
                 else
                 {
                     $affiche_scorm .= str_replace("$c2","$c1",trim($nom))."$c2";
                     $affiche_scorm .= str_replace("$c2","$c1",trim($description));
                     if ($motclef != "")
                         $affiche_scorm .=" ===> Mots-clef : ". str_replace("$c2","-",trim($motclef))."$c2";
                     else
                         $affiche_scorm .= "$c2";
                     if ($duree != 0)
                         $affiche_scorm .= $duree."'$c2";
                     else
                         $affiche_scorm .= "non indiquée$c2";

                     $affiche_scorm .= $mess_seq_sco."$c2";


                     $affiche_scorm .= $status.$c2;
                     $traquer = AffiDureeAct($id,$scormOk,$id_app,$numero_groupe);
                     $tabTrq = explode('|',$traquer);
                     $nbTrq = $tabTrq[0];
                     $dureeActivite = $tabTrq[1];
                     $dureeTotale += $dureeActivite;
                     $affiche_scorm .= $c2.$nbTrq.$c2.AfficheDureeAN($dureeActivite,$nbTrq).$c2.
                                       '"'.str_replace('<br />',chr(13).chr(10),$tabTrq[2]).'"';
                 }
                 $fichier_csv .= str_replace('&eacute,','é',str_replace("&#039,","'",$affiche_scorm));
                 $fichier_csv .=  "\n";
            $ii++;
           }
         }// fin elseif scormOk == 1
            } //fin else ($Nb__seq ...)
         $i++;
         $pointeur = 0;
         $visible = 0;
       }//fin while ($i != $nb_seq)
     $p++;
  }// fin while ($p < $nb_parc)
 if ($tout == 1)
  $fichier_csv .= "\n\n Durée totale calculée ou estimée en l'absence de tracking : $c2".AfficheDureeAN($dureeTotale,0);
  $fichier_csv = str_replace('&eacute,','é',str_replace("&#039,","'",$fichier_csv));
  //echo $fichier_csv; exit;
    $nom_user = modif_nom($nom_user);
    $nom_grp3 = modif_nom($nom_grp3);
    $sortie_csv = "formation_$nom_grp3"."_$nom_app.csv";
    $dir_app_csv = "ressources/".$login."_".$id_user."/ressources/".$sortie_csv;
    $fp = fopen($dir_app_csv, "w+");
    $fw = fwrite($fp, $fichier_csv);
    fclose($fp);
    chmod($dir_app_csv,0775);
    $lien = "$dir_app_csv";
    ForceFileDownload($lien,'ascii');
    exit;
}
?>