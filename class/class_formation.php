<?php
function SupprimePrescriptionApprenant($num,$id_grp,$ext)
{
   GLOBAL $connect,$id_user,$lg,$typ_user,$login,$s_exp,$repertoire;
   //echo ">>>>>>>>>>$id_user,$lg,$typ_user,$num,$id_grp";
   require("lang$lg.inc.php");
   $utilisateur = $num;
   $numero_groupe = $id_grp;
   $cherche_parc_presc = mysql_query("SELECT distinct presc_parc_no FROM prescription$ext WHERE presc_utilisateur_no = '$num'");
   $nbr_parc=mysql_num_rows($cherche_parc_presc);
   if ($nbr_parc > 0)
   {
     $parc=0;
     while ($parc < $nbr_parc)
     {
       $id_parc = mysql_result($cherche_parc_presc,$parc,"presc_parc_no");
       $cherche_seq_presc = mysql_query("SELECT presc_seq_no FROM prescription$ext WHERE
                                         presc_utilisateur_no = $num AND
                                         presc_parc_no=$id_parc");
       $nbr_seq=mysql_num_rows($cherche_seq_presc);
       if ($nbr_seq > 0)
       {
         $seq=0;
         while($seq < $nbr_seq)
         {
           $id_seq = mysql_result($cherche_seq_presc,$seq,"presc_seq_no");
           $type_seq = GetDataField ($connect,"select seq_type_lb from sequence where seq_cdn='$id_seq'","seq_type_lb");
           $scormOk = (strstr($type_seq,'SCORM')) ? 1 : 0;
           SupSeqModifParc($id_seq,$scormOk,$utilisateur,$numero_groupe,$id_parc,$ext);
           $seq++;
         }
       }// if $nbr_seq > 0
       $effacer_parc = mysql_query("DELETE from suivi3$ext where
                                    suiv3_utilisateur_no = '$utilisateur' AND
                                    suiv3_parc_no = '$id_parc'");
       $effacer_trac = mysql_query("DELETE from traceur where traceur_util_no = '$utilisateur' AND traceur_grp_no = '$id_grp'");
       $parc++;
     }
   }// if $nbr_parc > 0
   $log_rep = mysql_query("SELECT util_login_lb from utilisateur where util_cdn = '$num'");
   $log_app = mysql_result($log_rep,0,'util_login_lb');
   $dir_app = $repertoire."/ressources/".$log_app."_".$num."/devoirs/$id_grp";
   $dir_tut = $repertoire."/ressources/".$login."_".$id_user."/devoirs/".$log_app."--".$num."/$id_grp";
   if (file_exists($dir_app))
       viredir($dir_app,$s_exp);
   if (file_exists($dir_tut))
       viredir($dir_tut,$s_exp);

    $effacer_trak = mysql_query("DELETE from traque where traq_util_no = '$num' AND traq_grp_no = $id_grp");
    $effacer_psc = mysql_query("DELETE from prescription$ext where presc_utilisateur_no = '$num'");
    $cherche_grp_parc = mysql_query ("SELECT utilgr_utilisateur_no from utilisateur_groupe where utilgr_groupe_no = $id_grp");
    $nb_fois = mysql_num_rows ($cherche_grp_parc);
    $effacer_grp = mysql_query("DELETE from utilisateur_groupe where utilgr_utilisateur_no = '$num' AND utilgr_groupe_no = $id_grp");
    $effacer_fiche = mysql_query("DELETE from fiche_suivi where fiche_utilisateur_no = '$num' AND fiche_grp_no = $id_grp");
    $SuppWkGrp = supWikiGrp($id_grp,$num);
    return $nb_fois;
}
function SupSeqModifParc($id_seq,$scormOk,$utilisateur,$numero_groupe,$id_parc)
{
   GLOBAL $connect,$lg,$id_user,$typ_user,$date_fiche,$heure_fiche,$login;
   require("lang$lg.inc.php");
   $ext="_$numero_groupe";
   $suppWiki = supWikiSeq($id_seq);
   $mess_notif = "";
   $titre_seq = GetDataField ($connect,"select seq_titre_lb from sequence where seq_cdn=$id_seq","seq_titre_lb");
   $nom_parc = GetDataField ($connect,"select parcours_nom_lb from parcours where parcours_cdn = $id_parc","parcours_nom_lb");
   if ($scormOk == 1)
   {
      $req = mysql_query("SELECT scorm_util_module$ext.mod_module_no FROM
                          scorm_util_module$ext,scorm_module where
                          scorm_module.mod_cdn = mod_module_no AND
                          scorm_module.mod_seq_no = $id_seq AND
                          user_module_no = $utilisateur");
      $nbr = mysql_num_rows($req);
      $i=0;
      while ($i< $nbr)
      {
          $act_suivi = mysql_result($req,$i,"mod_module_no");
          $effacer_ins = mysql_query("DELETE FROM scorm_util_module$ext WHERE
                                     user_module_no = $utilisateur AND
                                     mod_module_no = $act_suivi");
          $supp_interact = mysql_query ("DELETE FROM scorm_interact WHERE
                                         sci_user_no = $utilisateur AND
                                         sci_mod_no = $act_suivi AND
                                         sci_grp_no = $numero_groupe");
          $supp_objectives = mysql_query ("DELETE FROM scorm_objectives WHERE
                                           scob_user_no = $utilisateur AND
                                           scob_mod_no = $act_suivi AND
                                           scob_grp_no = $numero_groupe");
          $supp_fiche = mysql_query ("DELETE FROM fiche_suivi WHERE
                                      fiche_utilisateur_no = $utilisateur AND
                                      fiche_act_no = $act_suivi AND
                                      fiche_grp_no = $numero_groupe AND
                                      fiche_seq_no = $id_seq");
          $supp_traque = mysql_query ("DELETE FROM traque WHERE
                                       traq_util_no = $utilisateur AND
                                       traq_mod_no = $act_suivi AND
                                       traq_grp_no = '$numero_groupe'");
        $i++;
      }
   }
   else
   {
      $req = mysql_query("SELECT suivi1$ext.suivi_act_no from
                          suivi1$ext,activite where
                          activite.act_cdn = suivi_act_no AND
                          activite.act_seq_no = $id_seq AND
                          suivi_utilisateur_no = $utilisateur");
      $nbr = mysql_num_rows($req);
      $i=0;
      while ($i< $nbr){
         $act_suivi = mysql_result($req,$i,"suivi_act_no");
         $effacer_ins = mysql_query("DELETE FROM suivi1$ext WHERE
                                     suivi_utilisateur_no = $utilisateur AND
                                     suivi_act_no = $act_suivi");
          $supp_fiche = mysql_query ("DELETE FROM fiche_suivi WHERE
                                      fiche_utilisateur_no = $utilisateur AND
                                      fiche_act_no = $act_suivi AND
                                      fiche_grp_no = $numero_groupe AND
                                      fiche_seq_no = $id_seq");
          $supp_traque = mysql_query ("DELETE FROM traque WHERE
                                       traq_util_no = '$utilisateur' AND
                                       traq_act_no = '$act_suivi' AND
                                       traq_grp_no = '$numero_groupe'");
        $i++;
      }
   }
   $prescripteur = GetDataField ($connect,"select presc_prescripteur_no from prescription$ext where
                                           presc_seq_no = $id_seq AND
                                           presc_utilisateur_no = $utilisateur","presc_prescripteur_no");
   $inscripteur = GetDataField ($connect,"select util_auteur_no from utilisateur where util_cdn='$utilisateur'","util_auteur_no");
   $formateur = GetDataField ($connect,"select presc_formateur_no from prescription$ext where
                                        presc_seq_no = $id_seq AND
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
   $commentaire = $mess_sup_seq_app." ".$titre_seq ;
   $new_fiche = Donne_ID ($connect,"select max(fiche_cdn) from fiche_suivi");
   $req_fiche = mysql_query("INSERT INTO fiche_suivi
                             (fiche_cdn,fiche_utilisateur_no,fiche_auteur_no,fiche_qualite_lb,fiche_date_dt,
                             fiche_heure_dt,fiche_commentaire_cmt,fiche_grp_no,fiche_parc_no,
                             fiche_seq_no,fiche_act_no,fiche_typaction_lb) VALUES
                             ($new_fiche,$utilisateur,$id_user,\"$qualite\",\"$date_fiche\",\"$heure_fiche\",\"$commentaire\",
                             $numero_groupe,$id_parc,$id_seq,0,\"$action_fiche\")");
   $mess_notif .= "$mess_laseq \"$titre_seq\" $mess_admin_sup_mess_fin";
   $effacer_psc = mysql_query("DELETE FROM prescription$ext where
                               presc_utilisateur_no = $utilisateur AND
                               presc_seq_no = $id_seq AND
                               presc_parc_no = $id_parc");
   $effacer_suivi2 = mysql_query("DELETE FROM suivi2$ext where
                               suiv2_utilisateur_no = '$utilisateur' AND
                               suiv2_seq_no = $id_seq");
   $req = mysql_query("SELECT count(*) from prescription$ext where
                       presc_parc_no = $id_parc AND
                       presc_utilisateur_no = $utilisateur");
   $resultat = mysql_result($req,0);
   if ($resultat == 0)
   {
      $mess_notif .= "<BR>$mess_lemod \"$nom_parc\" $mess_admin_sup_mess_fin";
      $effacer_prc = mysql_query("DELETE FROM suivi3$ext where
                                  suiv3_parc_no = $id_parc AND
                                  suiv3_utilisateur_no = '$utilisateur'");
   }
   return $mess_notif;
}

function supWikiGrp($id_grp,$num)
{
    GLOBAL $connect;
    $reqWk = mysql_query("select * from wikiapp where wkapp_grp_no='$id_grp' and wkapp_app_no='$num'");
   if (mysql_num_rows($reqWk) > 0)
    {
       while ($itemWk = mysql_fetch_object($reqWk))
       {
          $lesBodies = mysql_query("select * from wikibodies where wkbody_clan_no='".$itemWk->wkapp_clan_nb."'");
          if (mysql_num_rows($lesBodies) > 0)
          {
             while ($itemBg = mysql_fetch_object($lesBodies))
             {
                   $SuppStar = mysql_query("DELETE from starating where wkstar_body_no = '".$itemBg->wkbody_cdn."' and starate_auteur_no=".$num);
                   $SuppCmt = mysql_query("DELETE from commentaires where comwk_body_no = '".$itemBg->wkbody_cdn."' and com_auteur_no=".$num);
             }
          }
          $SuppBody = mysql_query("DELETE from wikibodies where wkbody_auteur_no = '$num' AND wkbody_clan_no = ".$itemWk->wkapp_clan_nb);
          $nbMeta = mysql_result(mysql_query("select count(*) from wikimeta where wkmeta_auteur_no != '$num' AND wkmeta_clan_no = ".$itemWk->wkapp_clan_nb),0);
          $nbOwnMeta = mysql_result(mysql_query("select count(*) from wikimeta where wkmeta_auteur_no = '$num' AND wkmeta_clan_no = ".$itemWk->wkapp_clan_nb),0);
          if ($nbOwnMeta > 0 && $nbMeta == 0)
              $remplacer_wikimeta = mysql_query("update wikimeta set wkmeta_auteur_no = '$id_user' where wkmeta_auteur_no = '$num' AND wkmeta_clan_no = ".$itemWk->wkapp_clan_nb);
          if ($nbOwnMeta > 0 && $nbMeta > 0)
              $effacer_wikimeta = mysql_query("DELETE from wikimeta where wkmeta_auteur_no = '$num' AND wkmeta_clan_no = ".$itemWk->wkapp_clan_nb);
          $effacer_wikinote = mysql_query("DELETE from wikinote where wknote_app_no = ".$itemWk->wkapp_cdn);
       }
       $effacer_wikiapp = mysql_query("DELETE from wikiapp where wkapp_app_no = '$num' AND wkapp_grp_no = $id_grp");
    }
}


function supWikiSeq($id_seq)
{
   GLOBAL $connect,$id_parc,$numero_groupe,$lg,$utilisateur;
   $reqWk = mysql_query("select * from wikiapp where wkapp_seq_no='$id_seq' and
                        wkapp_parc_no = $id_parc and wkapp_grp_no = '$numero_groupe' and wkapp_app_no='$utilisateur'");
    if ($reqWk == TRUE && mysql_num_rows($reqWk) > 0)
    {
       while ($itemWk = mysql_fetch_object($reqWk))
       {
          $lesBodies = mysql_query("select * from wikibodies where wkbody_clan_no='".$itemWk->wkapp_clan_nb."'");
          if (mysql_num_rows($lesBodies) > 0)
          {
             while ($itemBg = mysql_fetch_object($lesBodies))
             {
                   $SuppStar = mysql_query("DELETE from starating where wkstar_body_no = '".$itemBg->wkbody_cdn."' and starate_auteur_no=".$utilisateur);
                   $SuppCmt = mysql_query("DELETE from commentaires where comwk_body_no = '".$itemBg->wkbody_cdn."' and com_auteur_no=".$utilisateur);
             }
          }
          $effacer_wikibody = mysql_query("DELETE from wikibodies where wkbody_auteur_no = '$utilisateur' AND wkbody_clan_no = ".$itemWk->wkapp_clan_nb);
          $nbMeta = mysql_result(mysql_query("select count(*) from wikimeta where wkmeta_auteur_no != '$utilisateur' AND wkmeta_clan_no = ".$itemWk->wkapp_clan_nb),0);
          $nbOwnMeta = mysql_result(mysql_query("select count(*) from wikimeta where wkmeta_auteur_no = '$utilisateur' AND wkmeta_clan_no = ".$itemWk->wkapp_clan_nb),0);
          if ($nbOwnMeta > 0 && $nbMeta == 0)
              $remplacer_wikimeta = mysql_query("update wikimeta set wkmeta_auteur_no = '$id_user' where  wkmeta_auteur_no = '$utilisateur' AND wkmeta_clan_no = ".$itemWk->wkapp_clan_nb);
          if ($nbOwnMeta > 0 && $nbMeta > 0)
              $effacer_wikimeta = mysql_query("DELETE from wikimeta where wkmeta_auteur_no = '$utilisateur' AND wkmeta_clan_no = ".$itemWk->wkapp_clan_nb);
          $effacer_wikinote = mysql_query("DELETE from wikinote where wknote_app_no = '".$itemWk->wkapp_cdn."'");
       }
       $effaceWiki = mysql_query("delete from wikiapp where wkapp_seq_no=$id_seq and
                              wkapp_parc_no = $id_parc and wkapp_grp_no = $numero_groupe and
                              wkapp_app_no= $utilisateur");
    }
}

function ajout_seq_suivi($id_seq)
{
       GLOBAL $connect,$id_util,$id_parc,$lg,$utilisateur,$numero_groupe,$id_user,$typ_user,$date_fiche,$heure_fiche;
       require("lang$lg.inc.php");
       $ext="_$numero_groupe";
       $type_sequence = GetDataField ($connect,"SELECT seq_type_lb from sequence where seq_cdn = '$id_seq'","seq_type_lb");
       if (strstr($type_sequence,"SCORM"))
          $scormOk = 1;
       else
          $scormOk = 0;
       if ($scormOk == 1)
          $act_query = mysql_query ("SELECT * from scorm_module where mod_seq_no = $id_seq order by mod_cdn");
       else
          $act_query = mysql_query ("SELECT * from activite where act_seq_no = $id_seq");
       $nb_act = mysql_num_rows ($act_query);
       if ($nb_act == 0){
          $mess_notif .= $msq_noact_seq;
          return $mess_notif;
         exit;
       }
       $nom_app = GetDataField ($connect,"SELECT util_nom_lb  from utilisateur where util_cdn = $utilisateur","util_nom_lb");
       $prenom_app = GetDataField ($connect,"SELECT util_prenom_lb  from utilisateur where util_cdn = $utilisateur","util_prenom_lb");
       $nom_sequence = getdatafield ($connect,"SELECT seq_titre_lb from sequence where seq_cdn = $id_seq","seq_titre_lb");
       $nom_parc = GetDataField ($connect,"select parcours_nom_lb from parcours where parcours_cdn = $id_parc","parcours_nom_lb");
       $cherche_util = mysql_query ("SELECT presc_utilisateur_no from prescription$ext where presc_seq_no = $id_seq AND presc_utilisateur_no = $utilisateur");
       $nb_fois = mysql_num_rows ($cherche_util);
       if ($nb_fois > 0){
          $mess_notif .= "$mess_laseq : $nom_sequence $mpr_app_insc";
          return $mess_notif;
         exit;
       }
        $duree_sequence = GetDataField ($connect,"SELECT seq_duree_nb from sequence where seq_cdn = '$id_seq'","seq_duree_nb");
        $id_seq_parc_query = mysql_query ("SELECT sum(sequence.seq_duree_nb) from sequence,prescription$ext where prescription$ext.presc_parc_no = $id_parc AND presc_grp_no = $numero_groupe AND presc_utilisateur_no = $utilisateur and sequence.seq_cdn = prescription$ext.presc_seq_no");
        $duree_parc_exist = mysql_result($id_seq_parc_query,0);
        $duree_parc = $duree_parc_exist + $duree_sequence;
        $id_parc_zero = mysql_query("select count(*) from prescription$ext where presc_parc_no = $id_parc and presc_utilisateur_no = $utilisateur");
        $nb_parc_zero = mysql_result($id_parc_zero,0);
        $ordre_seq = Donne_ID($connect,"SELECT max(suiv2_ordre_no) from suivi2$ext,prescription$ext where presc_utilisateur_no=$utilisateur AND presc_parc_no = $id_parc AND suiv2_utilisateur_no=$utilisateur AND suiv2_seq_no=presc_seq_no");
        $form_ref =GetDataField ($connect,"SELECT presc_formateur_no from prescription$ext where presc_utilisateur_no = $utilisateur AND presc_parc_no = $id_parc","presc_formateur_no");
        $req_min = mysql_query ("select prescription$ext.presc_datedeb_dt from
                         prescription$ext where
                         presc_utilisateur_no = $utilisateur and
                         presc_parc_no = $id_parc
                         order by presc_datedeb_dt asc");
        $date_deb = mysql_result($req_min,0,"presc_datedeb_dt");
        $req_max = mysql_query ("select prescription$ext.presc_datefin_dt from
                         prescription$ext where
                         presc_utilisateur_no = $utilisateur and
                         presc_parc_no = $id_parc
                         order by presc_datefin_dt asc");
        $nbr_max = mysql_num_rows($req_max);
        $date_fin = mysql_result($req_max,$nbr_max-1,"presc_datefin_dt");
        $id_suivi3 = Donne_ID ($connect,"SELECT max(suiv3_cdn) from suivi3$ext");
        $referentiel = GetDataField ($connect,"SELECT seqref_referentiel_no from sequence_referentiel where seqref_seq_no = $id_seq","seqref_referentiel_no");
        $id_presc = Donne_ID ($connect,"SELECT max(presc_cdn) from prescription$ext");
        $ordre_parc = GetDataField($connect,"SELECT presc_ordre_no from prescription$ext where presc_utilisateur_no=$utilisateur AND presc_parc_no = $id_parc","presc_ordre_no");
        $ins_presc = mysql_query ("insert into prescription$ext values ($id_presc,$id_seq,$id_parc,$utilisateur,'$date_deb','$date_fin',$id_user,$form_ref,$numero_groupe,'$ordre_parc')");
        $cherche_parc = mysql_query ("SELECT suiv3_parc_no from suivi3$ext where suiv3_parc_no = 0 and suiv3_utilisateur_no = $utilisateur");
        $nb = mysql_num_rows ($cherche_parc);
           if ($nb == 0)
           {
              $id_suivi3 = Donne_ID ($connect,"SELECT max(suiv3_cdn) from suivi3$ext");
              $ins_suivi3 = mysql_query ("insert into suivi3$ext values ($id_suivi3,$utilisateur,$id_parc,'A FAIRE',$duree_parc,$numero_groupe)");
           }else
              $ins_suivi3 = mysql_query ("update suivi3$ext set suiv3_duree_nb = $duree_parc where suiv3_utilisateur_no=$utilisateur and suiv3_parc_no=$id_parc");
           $id_suivi2 = Donne_ID ($connect,"SELECT max(suiv2_cdn) from suivi2$ext");
           $ins_suivi2 = mysql_query ("insert into suivi2$ext values ($id_suivi2,$utilisateur,$id_seq,'A FAIRE',$duree_sequence,$ordre_seq,$numero_groupe)");
           $prescripteur = GetDataField ($connect,"select presc_prescripteur_no from prescription$ext where
                                           presc_seq_no = $id_seq AND
                                           presc_utilisateur_no = $utilisateur","presc_prescripteur_no");
           $inscripteur = GetDataField ($connect,"select util_auteur_no from utilisateur where util_cdn='$utilisateur'","util_auteur_no");
           $formateur = GetDataField ($connect,"select presc_formateur_no from prescription$ext where
                                        presc_seq_no = $id_seq AND
                                        presc_utilisateur_no = $utilisateur","presc_formateur_no");
           if ($prescripteur == $id_user)
              $qualite = "Prescripteur";
           elseif ($inscripteur == $id_user)
              $qualite = "Inscripteur";
           elseif ($formateur == $id_user)
              $qualite = "Formateur";
           elseif ($prescripteur != $id_user && $inscripteur != $id_user && $formateur != $id_user)
              $qualite = "Administrateur";
           $action_fiche = "Gestion des prescriptions";
           $commentaire = $mess_presc_fiche." : ".$nom_sequence."\n $msq_parc : $nom_parc" ;
           $new_fiche = Donne_ID ($connect,"select max(fiche_cdn) from fiche_suivi");
           $insere = "INSERT INTO fiche_suivi (fiche_cdn,fiche_utilisateur_no,fiche_auteur_no,fiche_qualite_lb,".
                     "fiche_date_dt,fiche_heure_dt,fiche_commentaire_cmt,fiche_grp_no,fiche_parc_no,fiche_seq_no,".
                     "fiche_act_no,fiche_typaction_lb) VALUES".
                     "($new_fiche,$utilisateur,$id_user,\"$qualite\",\"$date_fiche\",\"$heure_fiche\",".
                     "\"$commentaire\",$numero_groupe,$id_parc,$id_seq,0,\"$action_fiche\")";
           $req_fiche = mysql_query($insere);
           //Selection des activites de la sequence
           if ($scormOk == 1)
           {      $mod_query = mysql_query ("SELECT mod_cdn from scorm_module where mod_seq_no = $id_seq order by mod_cdn");
                  $nb_mod = mysql_num_rows ($mod_query);
                  $k = 0;
                  while ($k < $nb_mod)
                  {
                      $id_suivi = Donne_ID ($connect,"SELECT max(user_module_cdn) from scorm_util_module$ext");
                      $id_mod = mysql_result ($mod_query,$k,"mod_cdn");
                      $ins_suivi = mysql_query ("insert into scorm_util_module$ext (user_module_cdn,user_module_no,mod_module_no,mod_grp_no) values ('$id_suivi','$utilisateur','$id_mod','$numero_groupe')");
                  $k++;
                  }
                  //fin while ($k != $nb_act)
           }
           else
           {
                  $act_query = mysql_query ("SELECT * from activite where act_seq_no = $id_seq");
                  $nb_act = mysql_num_rows ($act_query);
                  $k = 0;
                  while ($k < $nb_act)
                  {
                      $id_suivi = Donne_ID ($connect,"SELECT max(suivi_cdn) from suivi1$ext");
                      $act = mysql_result ($act_query,$k,"act_cdn");
                      $ress = mysql_result ($act_query,$k,"act_ress_no");
                      $ress_on = mysql_result ($act_query,$k,"act_ress_on");
                      if ($ress == 0 && $ress_on == 'OUI')
                         $ins_suivi = mysql_query ("insert into suivi1$ext (suivi_cdn,suivi_utilisateur_no,suivi_act_no,suivi_grp_no,suivi_etat_lb) values ($id_suivi,$utilisateur,$act,$numero_groupe,'A FAIRE')");
                      elseif ($ress == 0 && $ress_on == 'NON')
                         $ins_suivi = mysql_query ("insert into suivi1$ext (suivi_cdn,suivi_utilisateur_no,suivi_act_no,suivi_grp_no,suivi_etat_lb) values ($id_suivi,$utilisateur,$act,$numero_groupe,'PRESENTIEL')");
                      else
                         $ins_suivi = mysql_query ("insert into suivi1$ext (suivi_cdn,suivi_utilisateur_no,suivi_act_no,suivi_grp_no,suivi_etat_lb) values ($id_suivi,$utilisateur,$act,$numero_groupe,'A FAIRE')");
                   $k++;
                  }
                  //fin while ($k != $nb_act)
           }
           $mess_notif = "$nom_sequence : $seq_aff_app $prenom_app $nom_app ";
           return $mess_notif;
}
function ModifOrdreParc($utilisateur)
{
   GLOBAL $connect,$numero_groupe,$utilisateur,$lg,$id_user,$typ_user,$date_fiche,$heure_fiche,$ancien_ordre,$ordre_parc,$parc,$ancien_parc;
   require("lang$lg.inc.php");
   require("langues/formation.inc.php");
   $ext="_$numero_groupe";
   $requete = mysql_query("select presc_cdn from prescription$ext where
                           presc_utilisateur_no = $utilisateur and
                           presc_ordre_no = $ancien_ordre and
                           presc_parc_no = $ancien_parc");
   $nb_seq_p = mysql_num_rows($requete);
   // pour insertion dans la fiche
   $titre_parc = GetDataField ($connect,"select parcours_nom_lb from parcours where parcours_cdn = $parc","parcours_nom_lb");
   $titre_parc_precedent = GetDataField ($connect,"select parcours_nom_lb from parcours where parcours_cdn = $ancien_parc","parcours_nom_lb");
   $prescripteur = GetDataField ($connect,"select presc_prescripteur_no from prescription$ext where
                                           presc_parc_no = $parc and
                                           presc_utilisateur_no = $utilisateur","presc_prescripteur_no");
   $inscripteur = GetDataField ($connect,"select util_auteur_no from utilisateur where util_cdn='$utilisateur'","util_auteur_no");
   if ($inscripteur == $id_user)
     $qualite = "Inscripteur";
   elseif ($prescripteur == $id_user)
     $qualite = "Prescripteur";
   elseif ($prescripteur != $id_user && $inscripteur != $id_user && $typ_user == "ADMINISTRATEUR")
     $qualite = "Administrateur";
   else
     $qualite = "";
   $action_fiche = $gest_presc;
   $commentaire = $mess_change_ordparc. $titre_parc." $msgrp_permut $titre_parc_precedent";
   $new_fiche = Donne_ID ($connect,"select max(fiche_cdn) from fiche_suivi");
   $req_fiche = mysql_query("INSERT INTO fiche_suivi
                             (fiche_cdn,fiche_utilisateur_no,fiche_auteur_no,fiche_qualite_lb,fiche_date_dt,fiche_heure_dt,
                             fiche_commentaire_cmt,fiche_grp_no,fiche_parc_no,fiche_seq_no,fiche_act_no,fiche_typaction_lb) VALUES
                             ($new_fiche,$utilisateur,$id_user,'$qualite','$date_fiche','$heure_fiche',\"$commentaire\",
                             $numero_groupe,$parc,0,0,\"$action_fiche\")");
   //fin d'insertion dans la fiche
   $req_updt = mysql_query("update prescription$ext set
                            presc_ordre_no = $ancien_ordre where
                            presc_utilisateur_no = $utilisateur and
                            presc_ordre_no = $ordre_parc and
                            presc_parc_no = $parc");

   $i = 0;
   while ($i < $nb_seq_p){
     $num_presc = mysql_result($requete,$i,"presc_cdn");
     $req_updt = mysql_query("update prescription$ext set presc_ordre_no = $ordre_parc where presc_cdn = $num_presc");
   $i++;
   }
  $mess_notif = $commentaire;
  return $mess_notif;
}
function ModifOrdreSeqParc($utilisateur)
{
   GLOBAL $connect,$numero_groupe,$lg,$id_user,$typ_user,$date_fiche,$heure_fiche,$seq_actuelle,$seq_precedent,$ordre_seq,$ordre_precedent;
   require("lang$lg.inc.php");
   require 'langues/formation.inc.php';
   $ext="_$numero_groupe";
   $titre_seq = GetDataField ($connect,"select seq_titre_lb from sequence where seq_cdn=$seq_actuelle","seq_titre_lb");
   $titre_precedent = GetDataField ($connect,"select seq_titre_lb from sequence where seq_cdn=$seq_precedent","seq_titre_lb");
   $prescripteur = GetDataField ($connect,"select presc_prescripteur_no from prescription$ext where
                                           presc_seq_no = $seq_actuelle and
                                           presc_utilisateur_no = $utilisateur","presc_prescripteur_no");
   $inscripteur = GetDataField ($connect,"select util_auteur_no from utilisateur where util_cdn='$utilisateur'","util_auteur_no");
   $formateur = GetDataField ($connect,"select presc_formateur_no from prescription$ext where
                                        presc_seq_no = $seq_actuelle and
                                        presc_utilisateur_no = $utilisateur","presc_formateur_no");
   $le_parc = GetDataField ($connect,"select presc_parc_no from prescription$ext where
                                      presc_seq_no = $seq_actuelle and
                                      presc_utilisateur_no = $utilisateur","presc_parc_no");
   if ($formateur == $id_user)
     $qualite = "Formateur";
   elseif ($prescripteur == $id_user)
     $qualite = "Prescripteur";
   elseif ($inscripteur == $id_user)
     $qualite = "Inscripteur";
   elseif ($prescripteur != $id_user && $inscripteur != $id_user && $typ_user == "ADMINISTRATEUR")
     $qualite = "Administrateur";
   else
     $qualite = "";
   $action_fiche = $gest_presc;
   $commentaire = $mess_change_ordseq. $titre_seq." $msgrp_permut $titre_precedent";
   $new_fiche = Donne_ID ($connect,"select max(fiche_cdn) from fiche_suivi");
   $req_fiche = mysql_query("INSERT INTO fiche_suivi
                             (fiche_cdn,fiche_utilisateur_no,fiche_auteur_no,fiche_qualite_lb,fiche_date_dt,fiche_heure_dt,
                             fiche_commentaire_cmt,fiche_grp_no,fiche_parc_no,fiche_seq_no,fiche_act_no,fiche_typaction_lb) VALUES
                             ($new_fiche,$utilisateur,$id_user,'$qualite','$date_fiche','$heure_fiche',\"$commentaire\",
                             $numero_groupe,$le_parc,$seq_actuelle,0,\"$action_fiche\")");
   $modif_ordre = mysql_query("UPDATE suivi2$ext SET
                               suiv2_ordre_no = $ordre_precedent where
                               suiv2_seq_no = $seq_actuelle and
                               suiv2_utilisateur_no = $utilisateur");
   $modif_ordre = mysql_query("UPDATE suivi2$ext SET
                               suiv2_ordre_no = $ordre_seq where
                               suiv2_seq_no = $seq_precedent and
                               suiv2_utilisateur_no = $utilisateur");
   $mess_notif = $commentaire;

   return $mess_notif;
}
function jour_mois($mois,$an)
{
  return date("t",mktime(0,0,0,$mois,1,$an));
}
function ModifPrescUtil($tabul)
{
 GLOBAL $connect,$lg,$id_user,$typ_user,$date_fiche,$heure_fiche,$_POST,$connect,$form_ref,$presc_rf,$hgrp;
        $utilisateur = $_POST['utilisateur'];
        $numero_groupe = $_POST['numero_groupe'];
        $i_seq = $_POST['i_seq'];
        $i_parc = $_POST['i_parc'];
        $a_faire = $_POST['a_faire'];
 require("lang$lg.inc.php");
 require("langues/module.inc.php");
 require("langues/formation.inc.php");
 require("langues/prescription.inc.php");
 $ext="_$numero_groupe";
 $signal = array();
 $action = 0;
 $action_parc = array();
 $action_seq = array();
 $nn = 0;$compteur=0;$comptmod=0;$mod=0;
/*
 echo "<pre>";
 print_r($_POST);
 echo "</pre>";
*/
 while ($mod < $i_parc)
 {
   $comptmod = $mod+1;
   $id_parc = $_POST['module'][$comptmod];
   if ($lg == "fr")
     $mois_list = array("", "Janvier", "Février", "Mars", "Avril", "Mai", "Juin", "Juillet", "Août", "Septembre", "Octobre", "Novembre", "Décembre");
   elseif ($lg == "ru")
     $mois_list = array("","ßíâàðü","Ôåâðàëü","Ìàðò","Àïðåëü","Ìàé","Èþíü","Èþëü","Àâãóñò","Ñåíòÿáðü","Îêòÿáðü","Íîÿáðü","Äåêàáðü");
   elseif ($lg == "en")
     $mois_list = array("","January","February","March","April","May","June","Julliet","August","September","October","November","December");
   $passage_deb = 0;
   $passage_fin = 0;
/*
   $presc_ref[$id_parc] = $_POST['presc_ref'][$id_parc];
   $form_refer[$id_parc] = $_POST['form_refer'][$id_parc];
*/
   if ( $_POST['presc_ref'][$id_parc] != $mesg_modpresc)
   {
         $tabPresc=explode(' id: ',$_POST['presc_ref'][$id_parc]);
         $presc_ref[$id_parc] = $tabPresc[1];
         //$presc_ref[$id_parc] = $_POST['id_presc'][$id_parc];
   }
   //else
//         $presc_ref[$id_parc] = $_POST['presc_ref'][$id_parc];
   if ( $_POST['form_refer'][$id_parc] != $mesg_modform)
   {
         $tabForm=explode(' id: ',$_POST['form_refer'][$id_parc]);
         $form_refer[$id_parc] = $tabForm[1];
   }
   //else
      //$form_refer[$id_parc] = $_POST['form_refer'][$id_parc];
   $date_min[$id_parc] = $_POST['date_min'][$id_parc];
   $date_max[$id_parc] = $_POST['date_max'][$id_parc];
   $titre_parc = GetDataField ($connect,"select parcours_nom_lb from parcours where parcours_cdn=$id_parc","parcours_nom_lb");

   if ($form_ref[$id_parc] == -1)
   {
      if ($mess_notif != '')$mess_notif .= "<br />";
      $mess_notif .= "<B>".$titre_parc."</B> : ".$mpr_avert8;
      $signal[$id_parc] = 1;
   }
   if ($presc_rf[$id_parc] == -1)
   {
      if ($mess_notif != '')$mess_notif .= "<br />";
      $mess_notif .= "<B>".$titre_parc."</B> : ".$mpr_avert7;
      $signal[$id_parc] = 1;
   }
   $dt_db_query = mysql_query ("select presc_datedeb_dt from prescription$ext where
                                    presc_parc_no = $id_parc and
                                    presc_utilisateur_no = $utilisateur order by presc_datedeb_dt asc");
   $dt_db = mysql_result($dt_db_query,0,"presc_datedeb_dt");
   $ch_dt_db = explode("-",$dt_db);
   $dt_dbt = "$ch_dt_db[2]-$ch_dt_db[1]-$ch_dt_db[0]";
   $date_min[$id_parc] = str_replace ("-","/",$date_min[$id_parc]);
   $ch_date_deb = explode("/",$date_min[$id_parc]);
   $date_deb = "$ch_date_deb[2]-$ch_date_deb[1]-$ch_date_deb[0]";
   $jour=intval($ch_date_deb[0]);
   $mois=intval($ch_date_deb[1]);
   $an=$ch_date_deb[2];
   $retour=@jour_mois($mois,$an);
   if($jour > $retour && $mois < 13)
   {
      if ($mess_notif != '')$mess_notif .= "<br />";
      $mess_notif .= "<B>".$titre_parc."</B> : ".ucfirst($mess_dt_deb_seq)."<B> ..".$date_min[$id_parc]."</B> : $il_nya_que <B>$retour</B> $mess_jours $en <B>".$mois_list[$mois]." $an</B>";
      $passage_deb++;
      $signal[$id_parc] = 1;
   }
   $dt_fn_query = mysql_query ("select presc_datefin_dt from prescription$ext where
                                    presc_parc_no = $id_parc and
                                    presc_utilisateur_no = $utilisateur order by presc_datefin_dt desc");
   $dt_fn = mysql_result($dt_fn_query,0,"presc_datefin_dt");
   $ch_dt_fn = explode("-",$dt_fn);
   $dt_fin = "$ch_dt_fn[2]-$ch_dt_fn[1]-$ch_dt_fn[0]";
   $date_max[$id_parc] = str_replace ("-","/",$date_max[$id_parc]);
   $ch_date_fin = explode("/",$date_max[$id_parc]);
   $date_fin = "$ch_date_fin[2]-$ch_date_fin[1]-$ch_date_fin[0]";
   $jour=intval($ch_date_fin[0]);
   $mois=intval($ch_date_fin[1]);
   $an=$ch_date_fin[2];
   $retour=@jour_mois($mois,$an);
   if($jour > $retour && $mois < 13)
   {
      if ($mess_notif != '')$mess_notif .= "<br />";
      $mess_notif .= "<B>".$titre_parc."</B> : ".ucfirst($mess_dt_fin_seq)."<B>..".$date_max[$id_parc]."</B> : $il_nya_que <B>$retour</B> $mess_jours $en <B>".$mois_list[$mois]." $an</B>";
      $passage_fin++;
      $signal[$id_parc] = 1;
   }
   $nb_date_deb_query = mysql_query ("select TO_DAYS('$date_deb')");
   $nb_date_deb = mysql_result ($nb_date_deb_query,0);
   $nb_date_fin_query = mysql_query ("select TO_DAYS('$date_fin')");
   $nb_date_fin = mysql_result ($nb_date_fin_query,0);
   if ($nb_date_deb > $nb_date_fin && $mois < 13)
   {
     $passage_deb++;
     $passage_fin++;
      if ($mess_notif != '')$mess_notif .= "<br />";
      $mess_notif .= "$mess_gp_deb_form : ".$date_min[$id_parc]." $et $mess_gp_fin_form : ".$date_max[$id_parc]." $de <B>".$titre_parc."</B> : ".$mpr_avert1;
      $signal[$id_parc] = 1;
   }
   if ((substr($date_min[$id_parc],2,1) != "/") || (substr($date_min[$id_parc],5,1) != "/") || (strlen($date_min[$id_parc]) != 10) || ($ch_date_deb[2] < 2003) || ($ch_date_deb[1] > 12) || ($ch_date_deb[0] > 31) || ($ch_date_deb[0] < 1) || ($ch_date_deb[1] < 1))
   {
      if ($mess_notif != '')$mess_notif .= "<br />";
      $mess_notif .= $date_min[$id_parc]." : ".ucfirst($mess_dt_deb_seq)." <B>".$titre_parc."</B> ".$no_valid;
      $passage_deb++;
      $signal[$id_parc] = 1;
   }
   if ((substr($date_max[$id_parc],2,1) != "/") || (substr($date_max[$id_parc],5,1) != "/") || (strlen($date_max[$id_parc]) != 10) || ($ch_date_fin[2] < 2003) || ($ch_date_fin[1] > 12) || ($ch_date_fin[0] > 31) || ($ch_date_fin[0] < 1) || ($ch_date_fin[1] < 1))
   {
      if ($mess_notif != '')$mess_notif .= "<br />";
      $mess_notif .= $date_max[$id_parc]." : ".ucfirst($mess_dt_fin_seq)." <B>".$titre_parc. "</B> ".$no_valid;
      $passage_fin++;
      $signal[$id_parc] = 1;
   }
   $prescripteur = GetDataField ($connect,"select presc_prescripteur_no from
                                           prescription$ext where presc_parc_no = $id_parc and
                                           presc_utilisateur_no = $utilisateur","presc_prescripteur_no");
   $inscripteur = GetDataField ($connect,"select util_auteur_no from utilisateur where util_cdn='$utilisateur'","util_auteur_no");
   $formateur = GetDataField ($connect,"select presc_formateur_no from prescription$ext where
                                        presc_parc_no = $id_parc and
                                        presc_utilisateur_no = $utilisateur","presc_formateur_no");
   if ($prescripteur != $id_user && $inscripteur != $id_user && $typ_user == "ADMINISTRATEUR")
     $qualite = "Administrateur";
   elseif ($prescripteur == $id_user)
     $qualite = "Prescripteur";
   elseif ($inscripteur == $id_user)
     $qualite = "Inscripteur";
   elseif ($formateur == $id_user)
     $qualite = "Formateur";
   else
     $qualite = "";
     $action_fiche = "Gestion des prescriptions";
   if ($date_deb != $dt_db && $passage_deb == 0)
   {
      $commentaire = $mess_gp_chgdt_debpresc." ".$titre_parc." $dt_dbt $mess_rep_item ".str_replace("/","-",$date_min[$id_parc])."\n";
      $new_fiche = Donne_ID ($connect,"select max(fiche_cdn) from fiche_suivi");
      $req_fiche = mysql_query("INSERT INTO fiche_suivi (fiche_cdn,fiche_utilisateur_no,fiche_auteur_no,fiche_qualite_lb,fiche_date_dt,fiche_heure_dt,fiche_commentaire_cmt,fiche_grp_no,fiche_parc_no,fiche_seq_no,fiche_act_no,fiche_typaction_lb) VALUES($new_fiche,$utilisateur,$id_user,'$qualite','$date_fiche','$heure_fiche',\"$commentaire\",$numero_groupe,$id_parc,0,0,\"$action_fiche\")");
      $requete = mysql_query("UPDATE prescription$ext set
                              presc_datedeb_dt = '$date_deb' where
                              presc_parc_no = $id_parc and
                              presc_utilisateur_no = $utilisateur");
   }
   if ($date_fin != $dt_fn && $passage_fin == 0)
   {
      $commentaire = $mess_gp_chgdt_finpresc." ".$titre_parc." $dt_fin $mess_rep_item ".str_replace("/","-",$date_max[$id_parc])."\n";
      $new_fiche = Donne_ID ($connect,"select max(fiche_cdn) from fiche_suivi");
      $req_fiche = mysql_query("INSERT INTO fiche_suivi (fiche_cdn,fiche_utilisateur_no,fiche_auteur_no,fiche_qualite_lb,fiche_date_dt,fiche_heure_dt,fiche_commentaire_cmt,fiche_grp_no,fiche_parc_no,fiche_seq_no,fiche_act_no,fiche_typaction_lb) VALUES($new_fiche,$utilisateur,$id_user,'$qualite','$date_fiche','$heure_fiche',\"$commentaire\",$numero_groupe,$id_parc,0,0,\"$action_fiche\")");
      $requete = mysql_query("UPDATE prescription$ext set
                              presc_datefin_dt = '$date_fin' where
                              presc_parc_no = $id_parc and
                              presc_utilisateur_no = $utilisateur");
   }
   if (isset($form_refer[$id_parc]) && ($prescripteur == $id_user || $typ_user == 'ADMINISTRATEUR') && $form_refer[$id_parc] != -1)
   {
      $nom_ancien_form = GetDataField ($connect,"select util_nom_lb from utilisateur where util_cdn = $formateur","util_nom_lb");
      $prenom_ancien_form = GetDataField ($connect,"select util_prenom_lb from utilisateur where util_cdn = $formateur","util_prenom_lb");
      $nom_form = GetDataField ($connect,"select util_nom_lb from utilisateur where util_cdn = $form_refer[$id_parc]","util_nom_lb");
      $prenom_form = GetDataField ($connect,"select util_prenom_lb from utilisateur where util_cdn = $form_refer[$id_parc]","util_prenom_lb");
      $commentaire = $mess_remplace_form.$titre_parc."  --> $prenom_ancien_form $nom_ancien_form $mess_rep_item $prenom_form $nom_form\n";
      $new_fiche = Donne_ID ($connect,"select max(fiche_cdn) from fiche_suivi");
      $req_fiche = mysql_query("INSERT INTO fiche_suivi (fiche_cdn,fiche_utilisateur_no,fiche_auteur_no,fiche_qualite_lb,fiche_date_dt,fiche_heure_dt,fiche_commentaire_cmt,fiche_grp_no,fiche_parc_no,fiche_seq_no,fiche_act_no,fiche_typaction_lb) VALUES($new_fiche,$utilisateur,$id_user,'$qualite','$date_fiche','$heure_fiche',\"$commentaire\",$numero_groupe,,$id_parc,0,0,\"$action_fiche\")");
      $requete = mysql_query("UPDATE prescription$ext set
                              presc_formateur_no = ".$form_refer[$id_parc]." where
                              presc_parc_no = $id_parc and
                              presc_utilisateur_no = $utilisateur");
   }
   if (isset($presc_ref[$id_parc])  && ($prescripteur == $id_user || $typ_user == 'ADMINISTRATEUR') && $presc_ref[$id_parc] != -1)
   {
      $nom_ancien_form = GetDataField ($connect,"select util_nom_lb from utilisateur where util_cdn = $prescripteur","util_nom_lb");
      $prenom_ancien_form = GetDataField ($connect,"select util_prenom_lb from utilisateur where util_cdn = $prescripteur","util_prenom_lb");
      $nom_form = GetDataField ($connect,"select util_nom_lb from utilisateur where util_cdn = $presc_ref[$id_parc]","util_nom_lb");
      $prenom_form = GetDataField ($connect,"select util_prenom_lb from utilisateur where util_cdn = $presc_ref[$id_parc]","util_prenom_lb");
      $commentaire = $mess_remplace_presc.$titre_parc."  --> $prenom_ancien_form $nom_ancien_form $mess_rep_item $prenom_form $nom_form\n";
      $new_fiche = Donne_ID ($connect,"select max(fiche_cdn) from fiche_suivi");
      $req_fiche = mysql_query("INSERT INTO fiche_suivi (fiche_cdn,fiche_utilisateur_no,fiche_auteur_no,fiche_qualite_lb,fiche_date_dt,fiche_heure_dt,fiche_commentaire_cmt,fiche_grp_no,fiche_parc_no,fiche_seq_no,fiche_act_no,fiche_typaction_lb) VALUES($new_fiche,$utilisateur,$id_user,'$qualite','$date_fiche','$heure_fiche',\"$commentaire\",$numero_groupe,$id_parc,0,0,\"$action_fiche\")");
      $requete = mysql_query("UPDATE prescription$ext set
                              presc_prescripteur_no = '".$presc_ref[$id_parc]."' where
                              presc_parc_no = '$id_parc' and
                              presc_utilisateur_no = '$utilisateur'");
   }
   if ((isset($presc_ref[$id_parc]) && $presc_ref[$id_parc] != $mesg_modpresc) || (isset($form_refer[$id_parc]) && $form_refer[$id_parc]!= $mesg_modform) || $date_deb != $dt_db || $date_fin != $dt_fn)
   {
       if (!isset($action))
           $action = 0;
       $action++;
       if (!isset($action_parc[$id_parc]))
       {
          $action_parc=array();
          $action_parc[$id_parc] = 0;
       }
       $action_parc[$id_parc]++;
   }
   $mod++;
 }
 $i_parc = 0;
 $mod = 0;
 while ($nn < $i_seq)
 {
   $compteur = $nn+1;
   $commentaire = "";
   if ($lg == "fr")
     $mois_list = array("", "Janvier", "Février", "Mars", "Avril", "Mai", "Juin", "Juillet", "Août", "Septembre", "Octobre", "Novembre", "Décembre");
   elseif ($lg == "ru")
     $mois_list = array("","ßíâàðü","Ôåâðàëü","Ìàðò","Àïðåëü","Ìàé","Èþíü","Èþëü","Àâãóñò","Ñåíòÿáðü","Îêòÿáðü","Íîÿáðü","Äåêàáðü");
   elseif ($lg == "en")
     $mois_list = array("","January","February","March","April","May","June","Julliet","August","September","October","November","December");
   $passage_deb = 0;
   $passage_fin = 0;
   $id_seq = $_POST['sequence'][$compteur];
   $le_parc = GetDataField ($connect,"select presc_parc_no from prescription$ext where
                                      presc_seq_no = $id_seq and
                                      presc_utilisateur_no = $utilisateur","presc_parc_no");
   if (isset($action_parc[$le_parc]) && $action_parc[$le_parc] > 0)
   {
      $nn++;
      continue;
   }

   $presc_rf[$id_seq] = $_POST['presc_rf'][$id_seq];
   $form_ref[$id_seq] = $_POST['form_ref'][$id_seq];
   $date_debut[$id_seq] = $_POST['date_debut'][$id_seq];
   $date_finale[$id_seq] = $_POST['date_finale'][$id_seq];
   $titre_seq = GetDataField ($connect,"select seq_titre_lb from sequence where seq_cdn=$id_seq","seq_titre_lb");
   if ($form_ref[$id_seq] == -1)
   {
      if ($mess_notif != '')$mess_notif .= "<br />";
      $mess_notif .= "<B>".$titre_seq."</B> : ".$mpr_avert8;
      $signal[$id_seq] = 1;
   }
   if ($presc_rf[$id_seq] == -1)
   {
      if ($mess_notif != '')$mess_notif .= "<br />";
      $mess_notif .= "<B>".$titre_seq."</B> : ".$mpr_avert7;
      $signal[$id_seq] = 1;
   }
   $dt_db = GetDataField ($connect,"select presc_datedeb_dt from prescription$ext where
                                    presc_seq_no = $id_seq and
                                    presc_utilisateur_no = $utilisateur","presc_datedeb_dt");
   $ch_dt_db = explode("-",$dt_db);
   $dt_dbt = "$ch_dt_db[2]-$ch_dt_db[1]-$ch_dt_db[0]";
   $date_debut[$id_seq] = str_replace ("-","/",$date_debut[$id_seq]);
   $ch_date_deb = explode("/",$date_debut[$id_seq]);
   $date_deb = "$ch_date_deb[2]-$ch_date_deb[1]-$ch_date_deb[0]";
   $jour=intval($ch_date_deb[0]);
   $mois=intval($ch_date_deb[1]);
   $an=$ch_date_deb[2];
   $retour=@jour_mois($mois,$an);
   if($jour > $retour && $mois < 13)
   {
      if ($mess_notif != '')$mess_notif .= "<br />";
      $mess_notif .= "<B>".$titre_seq."</B> : ".ucfirst($mess_dt_deb_seq)."<B> ..".$date_debut[$id_seq]."</B> : $il_nya_que <B>$retour</B> $mess_jours $en <B>".$mois_list[$mois]." $an</B>";
      $passage_deb++;
      $signal[$id_seq] = 1;
   }
   $dt_fn = GetDataField ($connect,"select presc_datefin_dt from prescription$ext where
                                    presc_seq_no = $id_seq and
                                    presc_utilisateur_no = $utilisateur","presc_datefin_dt");
   $ch_dt_fn = explode("-",$dt_fn);
   $dt_fin = "$ch_dt_fn[2]-$ch_dt_fn[1]-$ch_dt_fn[0]";
   $date_finale[$id_seq] = str_replace ("-","/",$date_finale[$id_seq]);
   $ch_date_fin = explode("/",$date_finale[$id_seq]);
   $date_fin = "$ch_date_fin[2]-$ch_date_fin[1]-$ch_date_fin[0]";
   $jour=intval($ch_date_fin[0]);
   $mois=intval($ch_date_fin[1]);
   $an=$ch_date_fin[2];
   $retour=@jour_mois($mois,$an);
   if($jour > $retour && $mois < 13)
   {
      if ($mess_notif != '')$mess_notif .= "<br />";
      $mess_notif .= "<B>".$titre_seq."</B> : ".ucfirst($mess_dt_fin_seq)."<B>..".$date_finale[$id_seq]."</B> : $il_nya_que <B>$retour</B> $mess_jours $en <B>".$mois_list[$mois]." $an</B>";
      $passage_fin++;
      $signal[$id_seq] = 1;
   }
   $nb_date_deb_query = mysql_query ("select TO_DAYS('$date_deb')");
   $nb_date_deb = mysql_result ($nb_date_deb_query,0);
   $nb_date_fin_query = mysql_query ("select TO_DAYS('$date_fin')");
   $nb_date_fin = mysql_result ($nb_date_fin_query,0);
   if ($nb_date_deb > $nb_date_fin && $mois < 13)
   {
     $passage_deb++;
     $passage_fin++;
      if ($mess_notif != '')$mess_notif .= "<br />";
      $mess_notif .= "$mess_gp_deb_form : ".$date_debut[$id_seq]." $et $mess_gp_fin_form : ".$date_finale[$id_seq]." $de <B>".$titre_seq."</B> : ".$mpr_avert1;
      $signal[$id_seq] = 1;
   }
   if ((substr($date_debut[$id_seq],2,1) != "/") || (substr($date_debut[$id_seq],5,1) != "/") || (strlen($date_debut[$id_seq]) != 10) || ($ch_date_deb[2] < 2003) || ($ch_date_deb[1] > 12) || ($ch_date_deb[0] > 31) || ($ch_date_deb[0] < 1) || ($ch_date_deb[1] < 1))
   {
      if ($mess_notif != '')$mess_notif .= "<br />";
      $mess_notif .= $date_debut[$id_seq]." : ".ucfirst($mess_dt_deb_seq)." <B>".$titre_seq."</B> ".$no_valid;
      $passage_deb++;
      $signal[$id_seq] = 1;
   }
   if ((substr($date_finale[$id_seq],2,1) != "/") || (substr($date_finale[$id_seq],5,1) != "/") || (strlen($date_finale[$id_seq]) != 10) || ($ch_date_fin[2] < 2003) || ($ch_date_fin[1] > 12) || ($ch_date_fin[0] > 31) || ($ch_date_fin[0] < 1) || ($ch_date_fin[1] < 1))
   {
      if ($mess_notif != '')$mess_notif .= "<br />";
      $mess_notif .= $date_finale[$id_seq]." : ".ucfirst($mess_dt_fin_seq)." <B>".$titre_seq. "</B> ".$no_valid;
      $passage_fin++;
      $signal[$id_seq] = 1;
   }
   $titre_seq = GetDataField ($connect,"select seq_titre_lb from sequence where seq_cdn=$id_seq","seq_titre_lb");
   $prescripteur = GetDataField ($connect,"select presc_prescripteur_no from
                                           prescription$ext where presc_seq_no = $id_seq and
                                           presc_utilisateur_no = $utilisateur","presc_prescripteur_no");
   $inscripteur = GetDataField ($connect,"select util_auteur_no from utilisateur where util_cdn='$utilisateur'","util_auteur_no");
   $formateur = GetDataField ($connect,"select presc_formateur_no from prescription$ext where
                                        presc_seq_no = $id_seq and
                                        presc_utilisateur_no = $utilisateur","presc_formateur_no");
   if ($prescripteur != $id_user && $inscripteur != $id_user && $typ_user == "ADMINISTRATEUR")
     $qualite = "Administrateur";
   elseif ($prescripteur == $id_user)
     $qualite = "Prescripteur";
   elseif ($inscripteur == $id_user)
     $qualite = "Inscripteur";
   elseif ($formateur == $id_user)
     $qualite = "Formateur";
   else
     $qualite = "";
     $action_fiche = "Gestion des prescriptions";
   if ($date_deb != $dt_db && $passage_deb == 0)
   {
      $commentaire = $mess_gp_chgdt_debpresc." ".$titre_seq." $dt_dbt $mess_rep_item ".str_replace("/","-",$date_debut[$id_seq])."\n";
      $new_fiche = Donne_ID ($connect,"select max(fiche_cdn) from fiche_suivi");
      $req_fiche = mysql_query("INSERT INTO fiche_suivi (fiche_cdn,fiche_utilisateur_no,fiche_auteur_no,fiche_qualite_lb,fiche_date_dt,fiche_heure_dt,fiche_commentaire_cmt,fiche_grp_no,fiche_parc_no,fiche_seq_no,fiche_act_no,fiche_typaction_lb) VALUES($new_fiche,$utilisateur,$id_user,'$qualite','$date_fiche','$heure_fiche',\"$commentaire\",$numero_groupe,$le_parc,$id_seq,0,\"$action_fiche\")");
      $requete = mysql_query("UPDATE prescription$ext set
                              presc_datedeb_dt = '".$date_deb."' where
                              presc_seq_no = $id_seq and
                              presc_utilisateur_no = $utilisateur");
   }
   if ($date_fin != $dt_fn && $passage_fin == 0)
   {
      $commentaire = $mess_gp_chgdt_finpresc." ".$titre_seq." $dt_fin $mess_rep_item ".str_replace("/","-",$date_finale[$id_seq])."\n";
      $new_fiche = Donne_ID ($connect,"select max(fiche_cdn) from fiche_suivi");
      $req_fiche = mysql_query("INSERT INTO fiche_suivi (fiche_cdn,fiche_utilisateur_no,fiche_auteur_no,fiche_qualite_lb,fiche_date_dt,fiche_heure_dt,fiche_commentaire_cmt,fiche_grp_no,fiche_parc_no,fiche_seq_no,fiche_act_no,fiche_typaction_lb) VALUES($new_fiche,$utilisateur,$id_user,'$qualite','$date_fiche','$heure_fiche',\"$commentaire\",$numero_groupe,$le_parc,$id_seq,0,\"$action_fiche\")");
      $requete = mysql_query("UPDATE prescription$ext set
                              presc_datefin_dt = '".$date_fin."' where
                              presc_seq_no = $id_seq and
                              presc_utilisateur_no = $utilisateur");
   }

   if ($formateur != $form_ref[$id_seq] && ($prescripteur == $id_user || $typ_user == 'ADMINISTRATEUR') && $form_ref[$id_seq] != -1 && isset($form_ref[$id_seq]))
   {
      $nom_ancien_form = GetDataField ($connect,"select util_nom_lb from utilisateur where util_cdn = $formateur","util_nom_lb");
      $prenom_ancien_form = GetDataField ($connect,"select util_prenom_lb from utilisateur where util_cdn = $formateur","util_prenom_lb");
      $nom_form = GetDataField ($connect,"select util_nom_lb from utilisateur where util_cdn = $form_ref[$id_seq]","util_nom_lb");
      $prenom_form = GetDataField ($connect,"select util_prenom_lb from utilisateur where util_cdn = $form_ref[$id_seq]","util_prenom_lb");
      $commentaire = $mess_remplace_form.$titre_seq."  --> $prenom_ancien_form $nom_ancien_form $mess_rep_item $prenom_form $nom_form\n";
      $new_fiche = Donne_ID ($connect,"select max(fiche_cdn) from fiche_suivi");
      $req_fiche = mysql_query("INSERT INTO fiche_suivi (fiche_cdn,fiche_utilisateur_no,fiche_auteur_no,fiche_qualite_lb,fiche_date_dt,fiche_heure_dt,fiche_commentaire_cmt,fiche_grp_no,fiche_parc_no,fiche_seq_no,fiche_act_no,fiche_typaction_lb) VALUES($new_fiche,$utilisateur,$id_user,'$qualite','$date_fiche','$heure_fiche',\"$commentaire\",$numero_groupe,$le_parc,$id_seq,0,\"$action_fiche\")");
      $requete = mysql_query("UPDATE prescription$ext set
                              presc_formateur_no = ".$form_ref[$id_seq]." where
                              presc_seq_no = $id_seq and
                              presc_utilisateur_no = $utilisateur");
   }
   if ($prescripteur != $presc_rf[$id_seq] && ($prescripteur == $id_user || $typ_user == "ADMINISTRATEUR") && $presc_rf[$id_seq] != -1 && isset($presc_rf[$id_seq]))
   {
      $nom_ancien_form = GetDataField ($connect,"select util_nom_lb from utilisateur where util_cdn = $prescripteur","util_nom_lb");
      $prenom_ancien_form = GetDataField ($connect,"select util_prenom_lb from utilisateur where util_cdn = $prescripteur","util_prenom_lb");
      $nom_form = GetDataField ($connect,"select util_nom_lb from utilisateur where util_cdn = $presc_rf[$id_seq]","util_nom_lb");
      $prenom_form = GetDataField ($connect,"select util_prenom_lb from utilisateur where util_cdn = $presc_rf[$id_seq]","util_prenom_lb");
      $commentaire = $mess_remplace_presc.$titre_seq."  --> $prenom_ancien_form $nom_ancien_form $mess_rep_item $prenom_form $nom_form\n";
      $new_fiche = Donne_ID ($connect,"select max(fiche_cdn) from fiche_suivi");
      $req_fiche = mysql_query("INSERT INTO fiche_suivi (fiche_cdn,fiche_utilisateur_no,fiche_auteur_no,fiche_qualite_lb,fiche_date_dt,fiche_heure_dt,fiche_commentaire_cmt,fiche_grp_no,fiche_parc_no,fiche_seq_no,fiche_act_no,fiche_typaction_lb) VALUES($new_fiche,$utilisateur,$id_user,'$qualite','$date_fiche','$heure_fiche',\"$commentaire\",$numero_groupe,$le_parc,$id_seq,0,\"$action_fiche\")");
      $requete = mysql_query("UPDATE prescription$ext set
                              presc_prescripteur_no = ".$presc_rf[$id_seq]." where
                              presc_seq_no = $id_seq and
                              presc_utilisateur_no = $utilisateur");
   }
   if (($prescripteur != $presc_rf[$id_seq] && $presc_rf[$id_seq] != -1) || ($formateur != $form_ref[$id_seq] && $form_ref[$id_seq] != -1) || $date_deb != $dt_db || $date_fin != $dt_fn)
   {
       if (!isset($action))
           $action = 0;
       $action++;
       if (!isset($action_seq[$id_seq]))
       {
          $action_seq=array();
          $action_seq[$id_seq] = 0;
       }
       $action_seq[$id_seq]++;
   }
  $nn++;
 }
 if ($action == 0)
    $message = $msgrp_nomodif_presc;
 else
    $message = $mmsg_gene;
 $i_seq = 0;
 $nn = 0;
 $monRetour = array('message' => $message,
                    'utilisateur' => $utilisateur,
                    'a_faire' => $a_faire,
                    'hgrp' => $hgrp,
                    'numero_groupe' => $numero_groupe);
 return $monRetour;
}
?>
