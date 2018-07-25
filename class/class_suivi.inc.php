<?php
function ReinitSco($scormid)
{
  GLOBAL $connect,$utilisateur,$numero_groupe;
  $Ext = "_$numero_groupe";
  $sql = mysql_query("UPDATE scorm_util_module$Ext SET
                                 `lesson_location` = '',
                                 `lesson_mode` = 'NORMAL',
                                 `lesson_status` = 'NOT ATTEMPTED',
                                 `entry` = 'RESUME',
                                 `session_time` = '0000:00:00.00',
                                 `raw` = '-1',
                                 `suspend_data` = '',
                                 `credit` = 'NO-CREDIT'  WHERE
                                 `user_module_no` = '$utilisateur' AND
                                 `mod_module_no` ='$scormid'");
  $sql_verif_inter = mysql_query("DELETE FROM scorm_interact WHERE
                                      `sci_mod_no` = '$scormid' AND
                                      `sci_user_no` = '$utilisateur' AND
                                      `sci_grp_no` ='$numero_groupe'");

  $sql_verif_objectives = mysql_query("DELETE FROM scorm_objectives WHERE
                                           `scob_mod_no` = '$scormid' AND
                                           `scob_user_no` = '$utilisateur' AND
                                           `scob_grp_no` ='$numero_groupe'");
  return "Cette activité scorm a été réinitialisée";
}

function Reinit($id_seq)
{
  GLOBAL $connect,$scormOk,$utilisateur,$numero_groupe,$id_user,$date_fiche,$heure_fiche,$id_parc,$lg;
  $Ext = "_$numero_groupe";
  require ("lang$lg.inc.php");
  $type_sequence = getdatafield ($connect,"SELECT seq_type_lb from sequence where seq_cdn = $id_seq","seq_type_lb");
  $scormOk = (strstr($type_sequence,"SCORM")) ? 1 : 0;
  if ($scormOk == 1)
      $act_query = mysql_query("SELECT mod_cdn from scorm_module WHERE mod_seq_no = '$id_seq' order by mod_cdn");
  else
      $act_query = mysql_query("SELECT act_cdn from activite WHERE act_seq_no = '$id_seq' order by act_cdn");
  $Nb_act_seq = mysql_num_rows($act_query);
  $i = 0;
  while ($i < $Nb_act_seq)
  {
     if ($scormOk == 1)
     {
        $scormid = mysql_result($act_query,$i,"mod_cdn");
        $sql = mysql_query("UPDATE scorm_util_module$Ext SET
                                 `lesson_location` = '',
                                 `lesson_mode` = 'NORMAL',
                                 `lesson_status` = 'NOT ATTEMPTED',
                                 `entry` = 'RESUME',
                                 `session_time` = '0000:00:00.00',
                                 `raw` = '-1',
                                 `suspend_data` = '',
                                 `credit` = 'NO-CREDIT'  WHERE
                                 `user_module_no` = '$utilisateur' AND
                                 `mod_module_no` ='$scormid'");
        $sql_verif_inter = mysql_query("DELETE FROM scorm_interact WHERE
                                      `sci_mod_no` = '$scormid' AND
                                      `sci_user_no` = '$utilisateur' AND
                                      `sci_grp_no` ='$numero_groupe'");

        $sql_verif_objectives = mysql_query("DELETE FROM scorm_objectives WHERE
                                           `scob_mod_no` = '$scormid' AND
                                           `scob_user_no` = '$utilisateur' AND
                                           `scob_grp_no` ='$numero_groupe'");

        $modif_seq = mysql_query("UPDATE suivi2$Ext SET suiv2_etat_lb = 'A FAIRE' WHERE
                                suiv2_seq_no = $id_seq AND
                                suiv2_utilisateur_no = $utilisateur");
        $modif_parc = mysql_query("UPDATE suivi3$Ext SET suiv3_etat_lb = 'A FAIRE' WHERE
                                 suiv3_parc_no = $id_parc AND
                                 suiv3_utilisateur_no = $utilisateur");
     }
     else
     {
       $id_act = mysql_result($act_query,$i,"act_cdn");
       $id_ress = GetDataField ($connect,"select act_ress_no from activite where act_cdn = $id_act","act_ress_no");
       if ($id_ress == 0)
          $suivi_etat= mysql_query ("update suivi1$Ext set suivi_etat_lb = 'PRESENTIEL',
                                 suivi_fichier_lb= '',suivi_note_nb1 = '',
                                 suivi_commentaire_cmt='Activité repassée' where
                                 suivi_act_no= $id_act and
                                 suivi_utilisateur_no=$utilisateur");
       else
          $suivi_etat= mysql_query ("update suivi1$Ext set suivi_etat_lb = 'A FAIRE',
                                 suivi_fichier_lb= '',suivi_note_nb1 = '',
                                 suivi_commentaire_cmt='Activité repassée' where
                                 suivi_act_no= $id_act and
                                 suivi_utilisateur_no=$utilisateur");
       $suivi_etat_sequence = GetDataField ($connect,"select suiv2_etat_lb from suivi2$Ext where
                                                  suiv2_seq_no = $id_seq and
                                                  suiv2_utilisateur_no=$utilisateur","suiv2_etat_lb");
       if ($suivi_etat_sequence == "TERMINE" && $Nb_act_seq > 1)
           $change_suivi_etat= mysql_query ("update suivi2$Ext set suiv2_etat_lb = 'EN COURS' where
                                       suiv2_seq_no= $id_seq and
                                       suiv2_utilisateur_no=$utilisateur");
       elseif ($suivi_etat_sequence == "TERMINE" && $Nb_act_seq == 1)
           $change_suivi_etat= mysql_query ("update suivi2$Ext set suiv2_etat_lb = 'A FAIRE' where
                                       suiv2_seq_no= $id_seq and
                                       suiv2_utilisateur_no=$utilisateur");
           $suivi_etat_parcours = GetDataField ($connect,"select suiv3_etat_lb from suivi3$Ext where
                                                  suiv3_parc_no = $id_parc and
                                                  suiv3_utilisateur_no=$utilisateur","suiv3_etat_lb");
       if ($suivi_etat_parcours == "TERMINE")
           $change_suivi_etat= mysql_query ("update suivi3$Ext set suiv3_etat_lb = 'EN COURS' where
                                       suiv3_parc_no= $id_parc and
                                       suiv3_utilisateur_no=$utilisateur");
     }
     $i++;
   }
   $titre_seq = GetDataField ($connect,"select seq_titre_lb from sequence where seq_cdn=$id_seq","seq_titre_lb");
   $prescripteur = GetDataField ($connect,"select presc_prescripteur_no from prescription$Ext where
                                           presc_seq_no = $id_seq and
                                           presc_utilisateur_no = $utilisateur","presc_prescripteur_no");
   $inscripteur = GetDataField ($connect,"select util_auteur_no from utilisateur where util_cdn='$utilisateur'","util_auteur_no");
   $formateur = GetDataField ($connect,"select presc_formateur_no from prescription$Ext where
                                        presc_seq_no = $id_seq and
                                        presc_utilisateur_no = $utilisateur","presc_formateur_no");
   if ($formateur == $id_user)
     $qualite = "Formateur";
   elseif ($prescripteur == $id_user)
     $qualite = "Prescripteur";
   elseif ($inscripteur == $id_user)
     $qualite = "Inscripteur";
   elseif ($prescripteur != $id_user && $inscripteur != $id_user)
     $qualite = "Administrateur";
   $action_fiche = "Gestion des prescriptions";
   $commentaire = $mess_reinit."<br /> ".$titre_seq ;
   $mess_notif = "$titre_seq : $mess_reinit" ;
   $new_fiche = Donne_ID ($connect,"select max(fiche_cdn) from fiche_suivi");
   $req_fiche = mysql_query("INSERT INTO fiche_suivi
                             (fiche_cdn,fiche_utilisateur_no,fiche_auteur_no,fiche_qualite_lb,fiche_date_dt,fiche_heure_dt,
                             fiche_commentaire_cmt,fiche_grp_no,fiche_parc_no,fiche_seq_no,fiche_act_no,fiche_typaction_lb) VALUES
                             ($new_fiche,$utilisateur,$id_user,'$qualite','$date_fiche','$heure_fiche',\"$commentaire\",
                             $numero_groupe,$id_parc,$id_seq,0,\"$action_fiche\")");

   return $mess_notif;
}
?>
