<?php
if (!isset($_SESSION)) session_start();
include ("../include/UrlParam2PhpVar.inc.php");
require '../admin.inc.php';
require '../fonction.inc.php';
require "../lang$lg.inc.php";
require "../langues/adm.inc.php";
dbConnect();
header("Content-type  : text/plain");
header("Cache-Control: no-cache, max-age=0");
$date_op = date("Y-m-d H:i:s" ,time());
$heure_fiche = substr($date_op,11);
$date_fiche = substr($date_op,0,10);
$today = date("d/n/Y");
$ext = "_$numero_groupe";
   $titre_seq = GetDataField ($connect,"select seq_titre_lb from sequence where seq_cdn=$id_seq","seq_titre_lb");
   $nom_parc = GetDataField ($connect,"select parcours_nom_lb from parcours where parcours_cdn = $id_parc","parcours_nom_lb");
/*
   $effacer_fiche = mysql_query("DELETE FROM fiche_suivi where
                               fiche_utilisateur_no = '$utilisateur' AND
                               fiche_seq_no = $id_seq AND
                               fiche_grp_no = $numero_groupe");
*/
   if ($scormOk == 1)
   {
      $req = mysql_query("SELECT mod_module_no FROM
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
                                       fiche_seq_no = $id_seq");
        $i++;
      }
   }
   else
   {
      $req = mysql_query("SELECT suivi_act_no from
                          suivi1$ext,activite where
                          act_cdn = suivi_act_no AND
                          act_seq_no = $id_seq AND
                          suivi_utilisateur_no = $utilisateur");
      $nbr = mysql_num_rows($req);
      $i=0;
      while ($i< $nbr)
      {
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
                                       fiche_seq_no = '$id_seq'");
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
                             ($new_fiche,$utilisateur,$id_user,'$qualite','$date_fiche','$heure_fiche',\"$commentaire\",
                             $numero_groupe,$id_parc,$id_seq,0,\"$action_fiche\")");
   $mess_notif .= "$mess_laseq \"$titre_seq\" $mess_admin_sup_mess_fin";
   $effacer_psc = mysql_query("DELETE FROM prescription$ext where
                               presc_utilisateur_no = $utilisateur AND
                               presc_seq_no = $id_seq AND
                               presc_parc_no = $id_parc");
   $effacer_suivi2 = mysql_query("DELETE FROM suivi2$ext where
                               suiv2_utilisateur_no = '$utilisateur' AND
                               suiv2_seq_no = $id_seq");
   $reqWk = mysql_query("select * FROM wikiapp where
                               wkapp_app_no = '$utilisateur' AND
                               wkapp_seq_no = $id_seq AND
                               wkapp_parc_no = $id_parc AND
                               wkapp_grp_no = $ext");
    if (mysql_num_rows($reqWk) > 0)
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
          $effacer_wikiapp = mysql_query("DELETE from wikiapp where wkapp_cdn = "$itemWk->wkapp_cdn);
       }
    }
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

sleep(1);
echo utf2Charset($mess_notif,$charset);

?>
