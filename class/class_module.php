<?php
function add_seq_user($seq)
{
   GLOBAL $id_parc,$id_user,$lg,$connect;
   require("lang$lg.inc.php");
   require_once("fonction.inc.php");
//Insertion des sequences dans le parcours
// if ($insert_seq == 1) {
   $date_op = date("Y-m-d H:i:s" ,time());
   $heure_fiche = substr($date_op,11);
   $date_fiche = substr($date_op,0,10);
   $date_dujour = date ("Y-n-d");
   $seq_query = requete("*","sequence","seq_cdn = $seq");
   $data = mysql_fetch_object($seq_query);
   $titre = $data->seq_titre_lb;
   $descriptif = $data->seq_desc_cmt;
   $cles_seq = $data->seq_mots_clef;
   $duree = $data->seq_duree_nb;
   $droit_voir = $data->seq_publique_on;
   $aut_seq = $data->seq_auteur_no;
   $seq_type = $data->seq_type_on;
   $type_sequence = $data->seq_type_lb;
   $ordre_act = $data->seq_ordreact_on;
   if (strstr($type_sequence,"SCORM"))
      $scormOk = 1;
   else
      $scormOk = 0;
   // vue parc_seq
   $titre_parc = GetDataField ($connect,"SELECT parcours_nom_lb from parcours where parcours_cdn = '$id_parc'","parcours_nom_lb");
   $req_classe = requete("count(DISTINCT gp_grp_no)","groupe_parcours,groupe","gp_parc_no = '$id_parc' AND gp_grp_no = grp_cdn AND grp_classe_on = 0");
   $nbr_classe = mysql_result($req_classe,0);
   if ($nbr_classe > 0)
      $req_classe = mysql_query("SELECT DISTINCT gp_grp_no FROM groupe_parcours,groupe WHERE gp_parc_no = '$id_parc' AND gp_grp_no = grp_cdn AND grp_classe_on = 0");
   $exist_seq_parc = mysql_result (mysql_query ("SELECT count(*) FROM sequence_parcours WHERE seqparc_seq_no = $seq"),0);
   $list_seq_query = requete (" count(*)", "sequence_parcours","seqparc_parc_no = $id_parc and seqparc_seq_no = $seq");
   $nb_seq_list = mysql_result ($list_seq_query,0);
   if ($nb_seq_list > 0)
      $list_seq_query = requete("*","sequence_parcours","seqparc_parc_no = $id_parc and seqparc_seq_no = $seq");
   $id_seqparc = Donne_ID ($connect,"SELECT max(seqparc_cdn) FROM sequence_parcours");
   $id_ordre = Donne_ID ($connect,"SELECT max(seqparc_ordre_no) from sequence_parcours WHERE seqparc_parc_no=$id_parc");
   if ($nb_seq_list == 0)
   {
       if (isset($le_type) && $le_type == 1)
         $insc_type = 1;
       else
         $insc_type = 0;
       $nom_user = GetDataField ($connect,"SELECT util_nom_lb FROM utilisateur WHERE util_cdn = $id_user","util_nom_lb");
       $titre = trim($titre)." ($nom_user)";
       $descriptif = str_replace("\"","'",$descriptif);
       $cles_seq = str_replace("\"","'",$cles_seq);
       $combinaison = (($droit_voir == 0 && $seq_type == 1) || ($droit_voir == 1 && $seq_type == 1)) ? 0 : 1;
       if ($combinaison == 1)
       {
          $id_new_seq = $seq;
          $ins_seqparc = mysql_query ("INSERT INTO sequence_parcours VALUES ($id_seqparc,$seq,$id_parc,$id_ordre)");
          $upd_seqparc = mysql_query ("UPDATE scorm_module SET mod_parc_no = $id_parc WHERE mod_seq_no = $seq");
       }
       else
       {
          $id_new_seq = Donne_ID ($connect,"SELECT MAX(seq_cdn) FROM sequence");
          $insert_new_seq = mysql_query ("INSERT INTO sequence VALUES ".
                                         "($id_new_seq,\"$titre\",\"$descriptif\",\"$cles_seq\",\"$ordre_act\",$duree,$id_user,".
                                         "\"$date_dujour\",\"$date_dujour\",1,$insc_type,\"$type_sequence\")");
          $id_ref = GetDataField ($connect,"SELECT seqref_referentiel_no FROM sequence_referentiel WHERE seqref_seq_no = \"$seq\"","seqref_referentiel_no");
          $id_seqref = Donne_ID ($connect,"SELECT MAX(seqref_cdn) FROM sequence_referentiel");
          $autre_ref = mysql_query ("INSERT INTO sequence_referentiel VALUES ($id_seqref,$id_new_seq,$id_ref)");
          $ins_seqparc = mysql_query ("INSERT INTO sequence_parcours VALUES ($id_seqparc,$id_new_seq,$id_parc,$id_ordre)");
       }
       if (isset($nbr_classe) && $nbr_classe > 0)
       {
          $nc=0;
          while ($nc < $nbr_classe)
          {
              $groupe = mysql_result($req_classe,$nc,"gp_grp_no");
              $Ext = "_$groupe";
              $util_grp_query = requete("utilgr_utilisateur_no","utilisateur_groupe","utilgr_groupe_no = $groupe");
              $nb_util = mysql_num_rows ($util_grp_query);
              $j = 0;
              while ($j < $nb_util)
              {
                  $id_util = mysql_result ($util_grp_query,$j,"utilgr_utilisateur_no");
                  $id_new_suivi2 = Donne_ID ($connect,"SELECT MAX(suiv2_cdn) FROM suivi2$Ext");
                  $date_deb = GetDataField ($connect,"SELECT gp_db_dt FROM groupe_parcours WHERE
                                                      gp_parc_no = $id_parc AND
                                                      gp_grp_no = $groupe","gp_db_dt");
                  $date_fin = GetDataField ($connect,"SELECT gp_df_dt FROM groupe_parcours WHERE
                                                      gp_parc_no = $id_parc AND
                                                      gp_grp_no = $groupe","gp_df_dt");
                  $ordre_grp_parc = GetDataField ($connect,"SELECT gp_ordre_no FROM groupe_parcours WHERE
                                                            gp_parc_no = $id_parc AND
                                                            gp_grp_no = $groupe","gp_ordre_no");
                  $id_ordre_suivi2 = Donne_ID ($connect,"SELECT MAX(suiv2_ordre_no) FROM
                                                         suivi2$Ext,sequence_parcours WHERE
                                                         suiv2_seq_no = seqparc_seq_no AND
                                                         sequence_parcours.seqparc_parc_no = $id_parc");
                  $insert_new_suivi2 = mysql_query ("INSERT INTO suivi2$Ext VALUES
                                                     ('$id_new_suivi2','$id_util','$id_new_seq',\"A FAIRE\",'$duree','$id_ordre_suivi2','$groupe')");
                  $id_presc = Donne_ID ($connect,"SELECT max(presc_cdn) FROM prescription$Ext");
                  $ins_presc = mysql_query ("INSERT INTO prescription$Ext VALUES
                                             ($id_presc,$id_new_seq,$id_parc,$id_util,'$date_deb','$date_fin',$id_user,
                                             $id_user,$groupe,$ordre_grp_parc)");
                  $qualite = $msq_prescripteur;
                  $action_fiche = $mess_menu_presc;
                  $commentaire_fiche = $mess_presc_fiche." ".$titre."\n $msq_parc : $titre_parc" ;
                  $new_fiche = Donne_ID ($connect,"SELECT MAX(fiche_cdn) FROM fiche_suivi");
                  $req_fiche = mysql_query("INSERT INTO fiche_suivi
                                            (fiche_cdn,fiche_utilisateur_no,fiche_auteur_no,fiche_qualite_lb,fiche_date_dt,fiche_heure_dt,
                                            fiche_commentaire_cmt,fiche_grp_no,fiche_parc_no,fiche_seq_no,fiche_act_no,fiche_typaction_lb)
                                            VALUES($new_fiche,$id_util,$id_user,'$qualite','$date_fiche','$heure_fiche',
                                            \"$commentaire_fiche\",$groupe,$id_parc,$id_new_seq,0,\"$action_fiche\")");
                $j++;
              }
            $nc++;
          }
       }
       if (isset($scormOk) && $scormOk == 1)
       {
          $act_query = requete_order("*","scorm_module","mod_seq_no = $seq","mod_cdn");
          $Nb_act_seq = mysql_num_rows($act_query);
       }
       else
       {
          $act_query = requete_order("count(*)","activite","act_seq_no = $seq","act_ordre_nb");
          $Nb_act_seq = mysql_result ($act_query,0);
       }
       if ($Nb_act_seq == 0)
       {
          $message =  "$msq_noact<br><br>";
          return $message;
       }
       else
       {
          $i = 0;
          if (isset($scormOk) && $scormOk == 1)
          {
            while ($DataSco = mysql_fetch_object($act_query))
            {
               $id_sco = $DataSco->mod_cdn;
               $nommer = $DataSco->mod_titre_lb;
               $desc = str_replace("\"","'",$DataSco->mod_desc_cmt);
               $consigne = str_replace("\"","'",$DataSco->mod_consigne_cmt);
               $clef = str_replace("\"","'",$DataSco->mod_motclef_lb);
               if ($id_new_seq != $seq)
                  $nommer = trim($nommer)." ($nom_user)";
               if ($id_new_seq != $seq)
                 $id_new_mod = Donne_ID ($connect,"SELECT MAX(mod_cdn) FROM scorm_module");
               else
                 $id_new_mod = $id_sco;
               if ($id_new_seq != $seq)
               {
                   $insert_new_mod = mysql_query ("INSERT INTO scorm_module VALUES
                                                   ($id_new_mod,$id_parc,$id_new_seq,
                                                   \"$nommer\",\"$desc\",\"$consigne\",\"$clef\",
                                                   \"".$DataSco->mod_visible."\",\"".$DataSco->mod_duree_nb."\",".$DataSco->mod_niveau_no.",
                                                   \"".$DataSco->mod_launch_lb."\",\"".$DataSco->mod_numero_lb."\",\"".$DataSco->mod_ordre_no."\",
                                                   \"".$DataSco->mod_pere_lb."\",\"".$DataSco->mod_pere_no."\",\"".$DataSco->mod_content_type_lb."\",
                                                   \"".$DataSco->mod_prereq_lb."\",\"".$DataSco->mod_maxtimeallowed."\",\"".$DataSco->mod_timelimitaction."\",
                                                   \"".$DataSco->mod_datafromlms."\",\"".$DataSco->mod_masteryscore."\")");
               }
               if (isset($nbr_classe) && $nbr_classe > 0)
               {
                $nc=0;
                while ($nc < $nbr_classe)
                {
                    $groupe = mysql_result($req_classe,$nc,"gp_grp_no");
                    $ext = "_$groupe";
                    $util_grp_query = requete("utilgr_utilisateur_no","utilisateur_groupe","utilgr_groupe_no = $groupe");
                    $nb_util = mysql_num_rows($util_grp_query);
                    while ($data_ugr = mysql_fetch_object($util_grp_query)){
                       $id_suivi = Donne_ID ($connect,"SELECT max(user_module_cdn) FROM scorm_util_module$ext");
                       $ins_suivi = mysql_query ("INSERT INTO scorm_util_module$ext (user_module_cdn,user_module_no,mod_module_no,mod_grp_no) ".
                                                 "VALUES ($id_suivi,".$data_ugr->utilgr_utilisateur_no.",$id_new_mod,$groupe)");
                    }
                  $nc++;
                }
               }
             $i++;
            }
          }
          else
          {
            $act_query = requete_order("*","activite","act_seq_no = $seq","act_ordre_nb");
            while ($DataAct = mysql_fetch_object($act_query))
            {
               $id_act = $DataAct->act_cdn;
               $nommer =  $DataAct->act_nom_lb;
               if ($id_new_seq != $seq)
                  $nommer .= " ($nom_user)";
               if ($id_new_seq != $seq)
                  $id_new_act = Donne_ID ($connect,"SELECT MAX(act_cdn) FROM activite");
               else
                  $id_new_act = $id_act;
               if ($id_new_seq != $seq)
                  $insert_new_act = mysql_query ("INSERT INTO activite VALUES ($id_new_act,$id_new_seq,".$DataAct->act_ordre_nb.
                                                ",\"$nommer\",\"".$DataAct->act_consigne_cmt."\",\"".$DataAct->act_commentaire_cmt.
                                                "\",'".$DataAct->act_ress_on."',".$DataAct->act_ress_no.
                                                ",".$DataAct->act_duree_nb.",\"".$DataAct->act_passagemult_on.
                                                "\",\"".$DataAct->act_acquittement_lb."\",\"".$DataAct->act_notation_on.
                                                "\",\"".$DataAct->act_devoirarendre_on."\",'$id_user',\"$date_dujour\",".
                                                "\"$date_dujour\",'".$DataAct->act_publique_on."',".$DataAct->act_flag_on.")");

               if (isset($nbr_classe) && $nbr_classe > 0)
               {
                $nc=0;
                while ($nc < $nbr_classe)
                {
                    $groupe = mysql_result($req_classe,$nc,"gp_grp_no");
                    $Ext = "_$groupe";
                    $util_grp_query = requete("utilgr_utilisateur_no","utilisateur_groupe","utilgr_groupe_no = $groupe");
                    $nb_util = mysql_num_rows ($util_grp_query);
                    $j = 0;
                    while ($j < $nb_util)
                    {
                       $id_util = mysql_result ($util_grp_query,$j,"utilgr_utilisateur_no");
                       $id_suivi = Donne_ID ($connect,"SELECT max(suivi_cdn) from suivi1$Ext");
                       if ($DataAct->act_ress_no == 0 && $DataAct->act_ress_on == 'OUI')
                          $ins_suivi = mysql_query ("INSERT INTO suivi1$Ext(suivi_cdn,suivi_utilisateur_no,suivi_act_no,suivi_etat_lb,suivi_grp_no) ".
                                                    "VALUES ($id_suivi,$id_util,$id_new_act,'A FAIRE','$groupe')");
                       elseif ($DataAct->act_ress_no == 0 && $DataAct->act_ress_on == 'NON')
                          $ins_suivi = mysql_query ("INSERT INTO suivi1$Ext(suivi_cdn,suivi_utilisateur_no,suivi_act_no,suivi_etat_lb,suivi_grp_no) ".
                                                    "VALUES ($id_suivi,$id_util,$id_new_act,'PRESENTIEL','$groupe')");
                       else
                          $ins_suivi = mysql_query ("INSERT INTO suivi1$Ext (suivi_cdn,suivi_utilisateur_no,suivi_act_no,suivi_etat_lb,suivi_grp_no)".
                                                    "VALUES ($id_suivi,$id_util,$id_new_act,'A FAIRE','$groupe')");
                      $j++;
                    }//while ($j < $nb_util)
                  $nc++;
                }//while ($nc < $nbr_classe)
               }//if ($nbr_classe > 0)
             }//while ($DataAct = mysql_fetch_object($act_query))
          }//if ($scormOk == 1){if ($Nb_act_seq == 0)
       }//if ($Nb_act_seq == 0)
     }
     else
     {
       $message = $seq_parc_exist;
       return $message;
    }
}

// ajout de séquence on the fly pour group_classe
function add_seq_user_after($seq,$id_parc)
{
   GLOBAL $id_user,$lg,$connect;
   require("lang$lg.inc.php");
   require_once("fonction.inc.php");

//Insertion des sequences dans le parcours
// if ($insert_seq == 1) {
   $date_op = date("Y-m-d H:i:s" ,time());
   $heure_fiche = substr($date_op,11);
   $date_fiche = substr($date_op,0,10);
   $date_dujour = date ("Y-n-d");
   $seq_query = requete("*","sequence","seq_cdn = $seq");
   $data = mysql_fetch_object($seq_query);
   $titre = $data->seq_titre_lb;
   $descriptif = $data->seq_desc_cmt;
   $cles_seq = $data->seq_mots_clef;
   $duree = $data->seq_duree_nb;
   $droit_voir = $data->seq_publique_on;
   $aut_seq = $data->seq_auteur_no;
   $seq_type = $data->seq_type_on;
   $type_sequence = $data->seq_type_lb;
   $ordre_act = $data->seq_ordreact_on;
   $titre_parc = GetDataField ($connect,"SELECT parcours_nom_lb from parcours where parcours_cdn = '$id_parc'","parcours_nom_lb");
   $req_classe = requete("count(DISTINCT gp_grp_no)","groupe_parcours,groupe","gp_parc_no = '$id_parc' AND gp_grp_no = grp_cdn AND grp_classe_on = 0");
   $nbr_classe = mysql_result($req_classe,0);
   if ($nbr_classe > 0)
      $req_classe = mysql_query("SELECT DISTINCT gp_grp_no FROM groupe_parcours,groupe WHERE gp_parc_no = '$id_parc' AND gp_grp_no = grp_cdn AND grp_classe_on = 0");
   $exist_seq_parc = mysql_result (mysql_query ("SELECT count(*) FROM sequence_parcours WHERE seqparc_seq_no = $seq"),0);
   $list_seq_query = mysql_query("SELECT seqparc_cdn FROM sequence_parcours WHERE seqparc_parc_no = $id_parc and seqparc_seq_no = $seq");
   $nb_seq_list = mysql_num_rows($list_seq_query);
   if ($nb_seq_list > 0)
   {
       if ($nbr_classe > 0)
       {
          $nc=0;
        while ($nc < $nbr_classe)
          {
              $groupe = mysql_result($req_classe,$nc,"gp_grp_no");
              $Ext="_$groupe";
              $le_formateur = GetDataField ($connect,"SELECT presc_formateur_no from prescription$Ext where presc_parc_no = '$id_parc'","presc_formateur_no");
              $le_prescripteur = GetDataField ($connect,"SELECT presc_prescripteur_no from prescription$Ext where presc_parc_no = '$id_parc'","presc_prescripteur_no");
              $util_grp_query = requete("utilgr_utilisateur_no","utilisateur_groupe","utilgr_groupe_no = $groupe");
              $nb_util = mysql_num_rows ($util_grp_query);
              $j = 0;
              while ($j < $nb_util)
              {
                $id_util = mysql_result ($util_grp_query,$j,"utilgr_utilisateur_no");
                $deja_fait = mysql_num_rows(mysql_query("SELECT presc_cdn from prescription$Ext Where presc_utilisateur_no = '$id_util' AND presc_parc_no = '$id_parc' AND presc_seq_no = '$seq'"));

                if ($deja_fait == 0)
                {
                  $id_new_suivi2 = Donne_ID ($connect,"SELECT MAX(suiv2_cdn) FROM suivi2$Ext");
                  $date_deb = GetDataField ($connect,"SELECT gp_db_dt FROM groupe_parcours WHERE
                                                      gp_parc_no = $id_parc AND
                                                      gp_grp_no = $groupe","gp_db_dt");
                  $date_fin = GetDataField ($connect,"SELECT gp_df_dt FROM groupe_parcours WHERE
                                                      gp_parc_no = $id_parc AND
                                                      gp_grp_no = $groupe","gp_df_dt");
                  $ordre_grp_parc = GetDataField ($connect,"SELECT gp_ordre_no FROM groupe_parcours WHERE
                                                            gp_parc_no = $id_parc AND
                                                            gp_grp_no = $groupe","gp_ordre_no");
                  $id_ordre_suivi2 = Donne_ID ($connect,"SELECT MAX(suiv2_ordre_no) FROM
                                                         suivi2$Ext,sequence_parcours WHERE
                                                         suiv2_seq_no = seqparc_seq_no AND
                                                         seqparc_parc_no = $id_parc");
                  $insert_new_suivi2 = mysql_query ("INSERT INTO suivi2$Ext VALUES
                                                     ('$id_new_suivi2','$id_util','$seq',\"A FAIRE\",'$duree','$id_ordre_suivi2','$groupe')");
                  $id_presc = Donne_ID ($connect,"SELECT max(presc_cdn) FROM prescription$Ext");
                  $ins_presc = mysql_query ("INSERT INTO prescription$Ext VALUES
                                             ($id_presc,$seq,$id_parc,$id_util,'$date_deb','$date_fin',$le_prescripteur,
                                             $le_formateur,$groupe,$ordre_grp_parc)");
                  $qualite = $msq_prescripteur;
                  $action_fiche = $mess_menu_presc;
                  $commentaire_fiche = $mess_presc_fiche." ".$titre."\n $msq_parc : $titre_parc" ;
                  $new_fiche = Donne_ID ($connect,"SELECT MAX(fiche_cdn) FROM fiche_suivi");
                  $req_fiche = mysql_query("INSERT INTO fiche_suivi
                                            (fiche_cdn,fiche_utilisateur_no,fiche_auteur_no,fiche_qualite_lb,fiche_date_dt,fiche_heure_dt,
                                            fiche_commentaire_cmt,fiche_grp_no,fiche_parc_no,fiche_seq_no,fiche_act_no,fiche_typaction_lb)
                                            VALUES($new_fiche,$id_util,$le_prescripteur,'$qualite','$date_fiche','$heure_fiche',
                                            \"$commentaire_fiche\",$groupe,$id_parc,$seq,0,\"$action_fiche\")");
                }//fin deja_fait
                $j++;
              }
            $nc++;
          }
       }
       $act_query = mysql_query("SELECT * FROM activite WHERE act_seq_no = $seq ORDER BY act_ordre_nb");
       $Nb_act_seq = mysql_num_rows($act_query);
      if ($Nb_act_seq> 0)
       {
          while ($DataAct = mysql_fetch_object($act_query))
          {
            $id_act = $DataAct->act_cdn;
            if ($nbr_classe > 0)
            {
               $nc=0;
                 while ($nc < $nbr_classe)
                 {
                    $groupe = mysql_result($req_classe,$nc,"gp_grp_no");
                    $Ext= "_$groupe";
                    $util_grp_query = mysql_query("SELECT utilgr_utilisateur_no FROM utilisateur_groupe WHERE utilgr_groupe_no = $groupe");
                    $nb_util = mysql_num_rows ($util_grp_query);
                    $j = 0;
                    while ($j < $nb_util)
                    {
                     $id_util = mysql_result ($util_grp_query,$j,"utilgr_utilisateur_no");
                     $deja_fait = mysql_num_rows(mysql_query("SELECT suivi_cdn from suivi1$Ext,activite WHERE suivi_utilisateur_no = $id_util AND suivi_act_no = activite.act_cdn AND activite.act_seq_no = $seq AND suivi_grp_no = $groupe"));
                     if ($deja_fait == 0)
                     {
                       $id_suivi = Donne_ID ($connect,"SELECT max(suivi_cdn) from suivi1$Ext");
                       if ($DataAct->act_ress_no == 0 && $DataAct->act_ress_on == 'OUI')
                          $ins_suivi = mysql_query ("INSERT INTO suivi1$Ext(suivi_cdn,suivi_utilisateur_no,suivi_act_no,suivi_etat_lb,suivi_grp_no) ".
                                                    "VALUES ($id_suivi,$id_util,$id_act,'A FAIRE','$groupe')");
                       elseif ($DataAct->act_ress_no == 0 && $DataAct->act_ress_on == 'NON')
                          $ins_suivi = mysql_query ("INSERT INTO suivi1$Ext(suivi_cdn,suivi_utilisateur_no,suivi_act_no,suivi_etat_lb,suivi_grp_no) ".
                                                    "VALUES ($id_suivi,$id_util,$id_act,'PRESENTIEL','$groupe')");
                       else
                          $ins_suivi = mysql_query ("INSERT INTO suivi1$Ext (suivi_cdn,suivi_utilisateur_no,suivi_act_no,suivi_etat_lb,suivi_grp_no)".
                                                    "VALUES ($id_suivi,$id_util,$id_act,'A FAIRE','$groupe')");
                     }// fin dejà_fait_suivi1
                     $j++;
                    }//while ($j < $nb_util)
                  $nc++;
                 }//while ($nc < $nbr_classe)
            }//if ($nbr_classe > 0)
          }//while ($DataAct = mysql_fetch_object($act_query))
       }//if ($Nb_act_seq == 0)
     }
}

/******************************************************
Inséré après le 15/11/2006
----------------------------------------------
exclusif pour id_classe=0 (formation sans individualisation:
permet d'actualiser l'ordre des modules prescrits dans la table 'prescription' en corrélation avec la date de fin des module
est appelée depuis modif == 1 du fichier modif_gp.php lors d'une modification de date
******************************************************/
function ClassModuleActualiseOrdre($classe)
{
     $req_gp = mysql_query("select * from groupe_parcours where gp_grp_no=$classe order by gp_parc_no");
     while ($item = mysql_fetch_object($req_gp))
     {
        $req_presc = mysql_query("update prescription_$classe set presc_ordre_no = ".$item->gp_ordre_no." where presc_parc_no= ".$item->gp_parc_no." and ".$item->gp_grp_no." = $classe");
     }
}

function ClassSeqActualiseOrdre($parc,$util,$grp)
{
     $req_sp = mysql_query("select * from sequence_parcours where seqparc_parc_no=$parc order by seqparc_ordre_no");
     while ($item = mysql_fetch_object($req_sp))
     {
        $req_seqparc = mysql_query("update suivi2_$grp set suiv2_ordre_no = ".$item->seqparc_ordre_no." where suiv2_utilisateur_no= $util and suiv2_seq_no = ".$item->seqparc_seq_no);
     }
}

function ChoixActModSeq($id_seq)
{
    GLOBAL $connect,$star,$id_parc,$id_user,$lg,$rechercher,$medor,$requete_parc,$id_ref,$id_ref_seq,
           $id_ref_parc,$parcours,$liste,$keydesc,$keytitre,$keypub,$message,
           $id_parc,$parc,$vient_de_search,$proprio,$refer,$miens,$miens_parc,
           $requete_seq,$requete_act,$liste_act_seq,$choix,$suffix,$suffixer;
    require("lang$lg.inc.php");
    require("langues/module.inc.php");
    $ret='';
    //Sélection des activités
    if (!isset($_SESSION['requete_parc']))
          $_SESSION['requete_parc']=$requete_parc;
    if (!isset($_SESSION['requete_seq']))
          $_SESSION['requete_seq']=$requete_seq;
    $act_query = mysql_query ("select act_cdn,act_nom_lb from activite");
    $Nb_act = mysql_num_rows ($act_query);
    if ($Nb_act == 0)
    {
      $ret .="<TR height='30'><TD>$msq_noact<br></TD></TR>";
    }
    else
    {
       if ($medor == 1)
       {
        if ($keydesc != "" && $keytitre == "" && $keypub == "")
           $champ_rech = "(activite.act_consigne_cmt like \"%$keydesc%\" OR activite.act_consigne_cmt like \"$keydesc%\" OR activite.act_consigne_cmt like \"%$keydesc\")";
        elseif ($keydesc == "" && $keytitre != "" && $keypub == "")
           $champ_rech = "(activite.act_nom_lb like \"%$keytitre%\" OR activite.act_nom_lb like \"$keytitre%\" OR activite.act_nom_lb like \"%$keytitre\")";
        elseif ($keydesc == "" && $keytitre == "" && $keypub != "")
           $champ_rech = "(activite.act_publique_on = '$keypub')";
        elseif ($keydesc != "" && $keytitre != "" && $keypub == "")
           $champ_rech = "(activite.act_nom_lb like \"%$keytitre%\" OR activite.act_nom_lb like \"$keytitre%\" OR activite.act_nom_lb like \"%$keytitre\") AND (activite.act_consigne_cmt like \"%$keydesc%\" OR activite.act_consigne_cmt like \"$keydesc%\" OR activite.act_consigne_cmt like \"%$keydesc\")";
        elseif ($keydesc == "" && $keytitre != "" && $keypub != "")
           $champ_rech = "(activite.act_publique_on = '$keypub' AND (activite.act_nom_lb like \"%$keytitre%\" OR activite.act_nom_lb like \"$keytitre%\" OR activite.act_nom_lb like \"%$keytitre\"))";
        elseif ($keydesc != "" && $keytitre == "" && $keypub != "")
           $champ_rech = "(activite.act_publique_on = '$keypub' AND (activite.act_consigne_cmt like \"%$keydesc%\" OR activite.act_consigne_cmt like \"$keydesc%\" OR activite.act_consigne_cmt like \"%$keydesc\"))";
        elseif ($keydesc != "" && $keytitre != "" && $keypub != "")
           $champ_rech = "(activite.act_publique_on = '$keypub' AND (activite.act_nom_lb like \"%$keytitre%\" OR activite.act_nom_lb like \"$keytitre%\" OR activite.act_nom_lb like \"%$keytitre\") AND (activite.act_consigne_cmt like \"%$keydesc%\" OR activite.act_consigne_cmt like \"$keydesc%\" OR activite.act_consigne_cmt like \"%$keydesc\"))";
        elseif ($keydesc == "" && $keytitre == "" && $keypub == "")
           $champ_rech = "(activite.act_publique_on = '1' OR activite.act_publique_on = '0')";
        $requete_act ="select activite.act_cdn,activite.act_nom_lb,activite.act_consigne_cmt,act_publique_on,utilisateur.util_nom_lb,utilisateur.util_prenom_lb from activite,utilisateur where $champ_rech AND utilisateur.util_cdn = activite.act_auteur_no order by activite.act_nom_lb,utilisateur.util_nom_lb asc";
    // fin de configuration des champs à discriminer
        if ($star == 1)
        {
          $letitre = "Mes favoris";
          $requete_act = "SELECT * from activite,stars,utilisateur where stars.star_item_id=activite.act_cdn and utilisateur.util_cdn = act_auteur_no and stars.star_user_id= $id_user and stars.star_type_no=3 order by act_nom_lb asc";
        }

        $act_query = mysql_query ($requete_act);
        $nb_act = mysql_num_rows($act_query);
        if ($nb_act ==0)
          $ret .="<TR><TD colspan='2'><B>$mess_no_occur</B></TD></TR>";
        if ($star != 1)
        {
           $ret .="<TR height='30'><TD colspan='2'><B>$mess_filtapp </B>  $msq_titre ";
           if ($keytitre != "") $ret .="<font color='#D45211'><B>$keytitre</B></font> , ";else $ret .="<B>$mess_nofiltre</B>, ";
           $ret .="$msq_aff_cons ";
           if ($keydesc != "") $ret .="<font color='#D45211'><B>$keydesc</B></font> , ";else $ret .="<B>$mess_nofiltre</B>, ";
           $ret .="$mess_visdup : ";
           if ($keypub == 1) $ret .="<font color='#D45211'><B>$mess_oui</B></font> ";elseif($keypub == 0 && $keypub != "") $ret .="<font color='#D45211'><B>$mess_non</B></font> ";else $ret .="<B>$mess_nofiltre</B>";
           $ret .="</TD></TR>";
        }
       }
       $ret .="<TR height='30'><TD nowrap colspan='2'><B>$mrc_rech</B></TD><TR>";
       $ret .="<TR><TD colspan='2'>";
       $ret .="<FORM name='form2' ACTION=\"sequence$suffix.php?choix=$choix&choix_act=1&medor=1&action_seq=1&rechercher=$rechercher&id_seq=$id_seq&id_ref=$id_ref&id_ref_seq=$id_ref_seq&id_ref_parc=$id_ref_parc&parcours=$parcours&liste=$liste&id_parc=$id_parc&proprio=$proprio&refer=$refer&miens=$miens&miens_parc=$miens_parc&medor=1&vient_de_search=1&parc=$id_parc\" METHOD='POST' target='main'>";
       $ret .="<TABLE cellpadding='6' cellspacing='0' width='90%' border='0'>";
       $ret .="<TR bgcolor= '#F4F4F4'>";
       $ret .="<TD nowrap>$msq_titre</TD><TD nowrap><INPUT TYPE='text'  class='INPUT'   name='keytitre' size='20' align='middle'></TD>";
       $ret .="<TD nowrap>$msq_aff_cons</TD><TD nowrap><INPUT TYPE='text'  class='INPUT'   name='keydesc' size='20' align='middle'></TD>";
       $ret .="<TD nowrap>$mess_visdup</TD>";
       $ret .="<TD nowrap>";
       $ret .="<SELECT name='keypub'>";
       $ret .="<OPTION></OPTION>";
       $ret .="<OPTION value='1'>$mess_oui</OPTION>";
       $ret .="<OPTION value='0'>$mess_non</OPTION>";
       $ret .="</SELECT></TD>";
       $ret .="<TD align='center'><A HREF=\"javascript:document.form2.submit();\" onmouseover=\"img10.src='images/fiche_identite/boutvalidb$suffixer.gif';return true;\" onmouseout=\"img10.src='images/fiche_identite/boutvalid$suffixer.gif'\">".
              "<IMG NAME=\"img10\" SRC=\"images/fiche_identite/boutvalid$suffixer.gif\" BORDER='0' onLoad=\"tempImg=new Image(0,0); tempImg.src='images/fiche_identite/boutvalidb$suffixer.gif'\"></A>";
       $ret .="</TD></TR></TABLE></FORM></TD></TR>";
       if ($medor == 1 && $nb_act > 0)
       {
         $ret .= "<TR><TD colspan='2'><table cellpadding='6' cellspacing='2' width='100%' border='0'>";
         $ret .= "<TR bgcolor='#2b677a'>";
         $ret .= "<TD height='20' align='left'><FONT COLOR=white><b>$msq_activite ";
         if ($star == 1) $ret .= " $msgseq_mark";
         $ret .=  "</b></FONT></TD>";
         $ret .="<TD height='20' align='left'><FONT COLOR=white><b>$mrc_aut</b></FONT></TD>";
         $ret .="<TD height='20' align='left'><FONT COLOR=white><b>$msq_aff_cons</b></FONT></TD></TR>";

         $titre_seq = GetDataField ($connect,"select seq_titre_lb from sequence where seq_cdn = $id_seq","seq_titre_lb");
         $requete_grp = mysql_query ("select * from groupe order by grp_nom_lb");
         $nb_grp = mysql_num_rows($requete_grp);
         if ($nb_grp > 0)
         {
            $gp=0;
            $nb_ut=0;
            while ($gp < $nb_grp)
            {
                 $id_grp = mysql_result($requete_grp,$gp,"grp_cdn");
                 $nomb = mysql_num_rows(mysql_query ("select * from suivi2_$id_grp where suiv2_seq_no = $id_seq"));
                 $nb_ut += $nomb;
             $gp++;
            }
         }
         $i = 0;
         while ($i < $nb_act)
         {
           $id_act = mysql_result ($act_query,$i,"act_cdn");
           $titre = mysql_result ($act_query,$i,"act_nom_lb");
           $consigne = str_replace('amp;','',mysql_result ($act_query,$i,"act_consigne_cmt"));
           $nom_auteur = mysql_result ($act_query,$i,"util_nom_lb");
           $droit_voir_act = mysql_result ($act_query,$i,"act_publique_on");
           $prenom_auteur = mysql_result ($act_query,$i,"util_prenom_lb");
           if (($i/2) == (floor($i/2)))
              $ret .="<TR bgcolor= \"#F4F4F4\">";
           else
              $ret .="<TR bgcolor= '#FFFFFF'>";
           $ret .="<TD height='20' align='left'>";
           $lien ="sequence$suffix.php?activite=1&star=$star&parcours=$parcours&liste=$liste&id_act=$id_act&id_parc=$id_parc&proprio=$proprio&refer=$refer&ins_ch_act=1&id_seq=$id_seq&id_ref=$id_ref&id_ref_parc=$id_ref_parc&miens=$miens&miens_parc=$miens_parc&vient_de_search=$vient_de_search&parc=$id_parc&liste_act_seq=$liste_act_seq";
           $lien = urlencode($lien);
           if ($droit_voir_act == 0)
              $ret .="<DIV id='sequence'><IMG SRC=\"images/modules/tut_form/icocadenas.gif\" border='0'>";
           $ret .="&nbsp;<A HREF=\"trace.php?link=$lien\" ";
           //if ($nb_ut > 0)
              //$ret .="onclick=\"return(confv());\"";
           $ret .=" OnMouseOver = \"overlib('<TABLE><TR><TD width=5></TD><TD>$mess_ajt_act_seq : <B>".addslashes($titre_seq)."</B></TD></TR></TABLE>',ol_hpos,RIGHT,ABOVE,WIDTH,'200')\" onMouseOut=\"nd()\"";
           $ret .=">$titre</A></DIV></TD>";
           $ret .="<TD height='20' align='left'>$prenom_auteur $nom_auteur</TD>";
           $ret .="<TD height='20' align='left'>".html_entity_decode($consigne,ENT_QUOTES,'iso-8859-1')."</TD></TR>";
         $i++;
         }
         $ret .="</TABLE></TD></TR>";
       }
       if ($medor != 1 || ($medor == 1 && $nb_act == 0))
       {
         if ($message != "")
            $ret .="</TD></TR><TR height='40'><TD valign='center'><FONT size='2'><b>$message</B>";
       }
    }
      $ret .="</TD></TR></FORM></TABLE></TD></TR></TABLE>";
    return $ret;
}
function ActiviteDupli($act_a_dupli)
{
          GLOBAL $id_user,$date_dujour,$connect;
          $act_query = mysql_query("select * from activite where act_cdn=$act_a_dupli");
          $ia = 0;
          $id = mysql_result ($act_query,$ia,"act_cdn");
          $nom = mysql_result ($act_query,$ia,"act_nom_lb");
          $ordre = mysql_result ($act_query,$ia,"act_ordre_nb");
          $flag = mysql_result ($act_query,$ia,"act_flag_on");
          $id_ress = mysql_result ($act_query,$ia,"act_ress_no");
          $duree = mysql_result ($act_query,$ia,"act_duree_nb");
          $ress_norok = mysql_result ($act_query,$ia,"act_ress_on");
          $consigne = mysql_result ($act_query,$ia,"act_consigne_cmt");
          $commentaire = mysql_result ($act_query,$ia,"act_commentaire_cmt");
          $pass_mult = mysql_result ($act_query,$ia,"act_passagemult_on");
          $acquit = mysql_result ($act_query,$ia,"act_acquittement_lb");
          $devoir = mysql_result ($act_query,$ia,"act_devoirarendre_on");
          $notation = mysql_result ($act_query,$ia,"act_notation_on");
          $auteur = mysql_result ($act_query,$ia,"act_auteur_no");
          $droit_voir_act = mysql_result ($act_query,$ia,"act_publique_on");
          $nom_user = GetDataField ($connect,"select util_nom_lb from utilisateur where util_cdn = $id_user","util_nom_lb");
          $nom .= "($nom_user)";
          $dupli_act = 0;
          $num_act = Donne_ID ($connect,"select max(act_ordre_nb) from activite where act_seq_no = 0");
          $id_nouv = Donne_ID ($connect,"select max(act_cdn) from activite");
          $insert_act_exist = mysql_query ("INSERT INTO activite  values ($id_nouv,0,$num_act,\"$nom\",\"$consigne\",\"$commentaire\",'$ress_norok',$id_ress,\"$duree\",\"$pass_mult\",\"$acquit\",\"$notation\",\"$devoir\",'$id_user',\"$date_dujour\",\"$date_dujour\",'$droit_voir_act','$flag')");
}
?>