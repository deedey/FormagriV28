<?php
if (!isset($_SESSION)) session_start();
include ("include/UrlParam2PhpVar.inc.php");
if (!isset($_SESSION['id_user']) || $_SESSION['id_user'] == "")
{
  exit();
}
require 'admin.inc.php';
require 'fonction.inc.php';
require "lang$lg.inc.php";
require 'fonction_html.inc.php';
require 'langues/module.inc.php';
dbConnect();
//echo "<pre>";print_r($_POST);echo "</pre>";
$nom_user=GetDataField ($connect,"select util_nom_lb from utilisateur where util_cdn = $id_user","util_nom_lb");
$prenom_user=GetDataField ($connect,"select util_prenom_lb from utilisateur where util_cdn = $id_user","util_prenom_lb");
function jour_mois($mois,$an){
  return date("t",mktime(0,0,0,$mois,1,$an));
}
$Ext='_'.$id_grp;
$id_classe = GetDataField ($connect,"select grp_classe_on from groupe where grp_cdn = $id_grp","grp_classe_on");
$date_op = date("Y-m-d H:i:s" ,time());
$heure_fiche = substr($date_op,11);
$date_fiche = substr($date_op,0,10);
$today = date("d/n/Y");
if (isset($supprimer) && $supprimer == 1)
{
   $titre_parc = GetDataField ($connect,"select parcours_nom_lb from parcours where parcours_cdn = $id_parc","parcours_nom_lb");
   if (!isset($id_classe) || (isset($id_classe) && $id_classe == 0))
   {
      $util_grp_query = mysql_query ("SELECT utilgr_utilisateur_no FROM utilisateur_groupe WHERE utilgr_groupe_no = $id_grp");
      $nb_util = mysql_num_rows ($util_grp_query);
      $j = 0;
      while ($j < $nb_util)
      {
             $id_util = mysql_result ($util_grp_query,$j,"utilgr_utilisateur_no");
             $req = mysql_query("SELECT scorm_util_module$Ext.mod_module_no FROM
                                 scorm_util_module$Ext,scorm_module where
                                 scorm_module.mod_cdn = scorm_util_module$Ext.mod_module_no AND
                                 scorm_module.mod_parc_no = $id_parc AND
                                 scorm_util_module$Ext.user_module_no = $id_util AND
                                 scorm_util_module$Ext.mod_grp_no = $id_grp");
             $nbr = mysql_num_rows($req);
             $i=0;
             while ($i< $nbr)
             {
                 $act_suivi = mysql_result($req,$i,"mod_module_no");
                 $effacer_ins = mysql_query("DELETE FROM scorm_util_module$Ext WHERE
                                             user_module_no = '$id_util' AND
                                             mod_module_no = $act_suivi AND
                                             mod_grp_no = $id_grp");
                 $del_interact = mysql_query ("DELETE FROM scorm_interact WHERE
                                               sci_user_no = '$id_util' AND
                                               sci_mod_no = '$act_suivi' AND
                                               sci_grp_no = '$id_grp'");
                 $del_objectives = mysql_query ("DELETE FROM scorm_objectives WHERE
                                               scob_user_no = '$id_util' AND
                                               scob_mod_no = '$act_suivi' AND
                                               scob_grp_no = '$id_grp'");


                 $supp_traque = mysql_query ("DELETE FROM traque WHERE
                                              traq_util_no = '$id_util' AND
                                              traq_mod_no = '$act_suivi'");
               $i++;
             }
             $req = mysql_query("SELECT suivi1_$id_grp.suivi_act_no FROM suivi1_$id_grp,activite,prescription_$id_grp WHERE
                                 activite.act_cdn = suivi1_$id_grp.suivi_act_no AND
                                 activite.act_seq_no = prescription_$id_grp.presc_seq_no AND
                                 prescription_$id_grp.presc_parc_no = $id_parc AND
                                 prescription_$id_grp.presc_grp_no = $id_grp AND
                                 suivi1_$id_grp.suivi_grp_no = $id_grp AND
                                 prescription_$id_grp.presc_utilisateur_no = $id_util AND
                                 suivi1_$id_grp.suivi_utilisateur_no = $id_util");
             $nbr = mysql_num_rows($req);
             $i=0;
             while ($i< $nbr)
             {
                 $act_suivi = mysql_result($req,$i,"suivi_act_no");
                 $effacer_ins = mysql_query("DELETE FROM suivi1_$id_grp WHERE
                                             suivi_utilisateur_no = '$id_util' AND
                                             suivi_act_no = '$act_suivi' AND
                                             suivi_grp_no = '$id_grp'");

                 $supp_traque = mysql_query ("DELETE FROM traque WHERE
                                              traq_util_no = '$id_util' AND
                                              traq_act_no = '$act_suivi'");
               $i++;
             }
             $supp_fiche = mysql_query ("DELETE FROM fiche_suivi WHERE
                                        fiche_utilisateur_no = '$id_util' AND
                                        fiche_grp_no = '$id_grp' AND
                                        fiche_parc_no = '$id_parc'");

         $action_fiche = "Gestion des prescriptions";
         $commentaire = $mess_sup_seq_app." ".$titre_seq ;
         $new_fiche = Donne_ID ($connect,"select max(fiche_cdn) from fiche_suivi");
         $req_fiche = mysql_query("INSERT INTO fiche_suivi
                                   (fiche_cdn,fiche_utilisateur_no,fiche_auteur_no,fiche_qualite_lb,fiche_date_dt,fiche_heure_dt,
                                   fiche_commentaire_cmt,fiche_grp_no,fiche_parc_no,fiche_seq_no,fiche_act_no,fiche_typaction_lb) VALUES
                                   ($new_fiche,$id_util,$id_user,\"Prescripteur\",'$date_fiche','$heure_fiche',\"$commentaire\",$id_grp,
                                   $id_parc,$id_seq,0,\"$action_fiche\")");

         $mess_aff .= "<BR><B>$msq_seq : ".$titre_seq."</B> ".$mess_admin_sup_mess_fin;
         $seqList = requete("presc_seq_no","prescription_$id_grp","presc_parc_no = '$id_parc' AND presc_utilisateur_no = '$id_util' AND presc_grp_no = '$id_grp'");
         $resultat = mysql_num_rows($seqList);
         if ($resultat > 0)
         {
            while ($dsseq = mysql_fetch_object($seqList))
            {
                $seqsup = $dsseq->presc_seq_no;
                $effacer_prc = mysql_query("DELETE FROM suivi2_$id_grp WHERE
                                            suiv2_seq_no = '$seqsup' AND
                                            suiv2_utilisateur_no = '$id_util' AND
                                            suiv2_grp_no = '$id_grp'");
            }
            $effacer_prc = mysql_query("DELETE FROM suivi3_$id_grp WHERE
                                        suiv3_parc_no = '$id_parc' AND
                                        suiv3_utilisateur_no = '$id_util' AND
                                        suiv3_grp_no = '$id_grp'");
            $effacer_prc = mysql_query("DELETE FROM prescription_$id_grp WHERE
                                        presc_parc_no = '$id_parc' AND
                                        presc_utilisateur_no = '$id_util' AND
                                        presc_grp_no = '$id_grp'");
         }
       $j++;
      }
      $nb_gp_parc = mysql_result(mysql_query("SELECT count(*) FROM prescription_$id_grp WHERE
                                              presc_grp_no='$id_grp' AND
                                              presc_parc_no = $id_parc"),0);
      if ($nb_gp_parc == 0)
      {
         $effacer_parc = mysql_query("DELETE FROM groupe_parcours WHERE
                                       gp_grp_no='$id_grp' AND
                                       gp_parc_no = '$id_parc'");
      }
      $mess_aff_supp .= $titre_parc." ".$mess_admin_sup_mess_fin;
   }
   elseif ($id_classe == 1)
   {
      $titre_parc = GetDataField ($connect,"SELECT parcours_nom_lb FROM parcours WHERE
                                            parcours_cdn = $id_parc","parcours_nom_lb");
      $effacer_parc = mysql_query("DELETE FROM groupe_parcours WHERE
                                   gp_grp_no='$id_grp' AND
                                   gp_parc_no = $id_parc");
      $mess_aff_supp .= $titre_parc." ".$mess_admin_sup_mess_fin;
   }

   $parc_gp_query = mysql_query ("select gp_parc_no from groupe_parcours where gp_grp_no = $id_grp");
   $nb_gp = mysql_num_rows ($parc_gp_query);
   if ($nb_gp == 0)
   {
     $lien = "gestion_groupe.php?cms=1&ordre_affiche=nom_groupe&mess_notif=$mess_aff_supp";
     $lien = urlencode($lien);
     echo "<script language=\"JavaScript\">";
     echo "document.location.replace(\"trace.php?link=$lien\")";
     echo "</script>";
     exit();
   }
   $mess_notif = $mess_aff_supp;
}

// Modification de l'ordre des parcours
if (isset($modif_ordre) && $modif_ordre == 1)
{
   $req_gp = GetDataField ($connect,"select gp_cdn from groupe_parcours where
                                     gp_ordre_no = $ancien_ordre and
                                     gp_grp_no = $id_grp","gp_cdn");
   $req_updt = mysql_query("update groupe_parcours set
                            gp_ordre_no = $ancien_ordre where
                            gp_ordre_no = $ordre_parc and
                            gp_grp_no=$id_grp");
   $req_updt = mysql_query("update groupe_parcours set
                            gp_ordre_no = $ordre_parc where
                            gp_cdn = $req_gp");
   if (!isset($id_classe) || (isset($id_classe) && $id_classe == 0))
   {
     $req_gp = mysql_query("select * from groupe_parcours where gp_grp_no=$id_grp order by gp_parc_no");
     while ($item = mysql_fetch_object($req_gp))
     {
        $req_presc = mysql_query("update prescription_$id_grp set presc_ordre_no = ".$item->gp_ordre_no." where presc_parc_no= ".$item->gp_parc_no." and presc_grp_no = $id_grp and ".$item->gp_grp_no." = $id_grp");
     }
   }
   $modif_ordre = 0;
}
if (isset($modif_ordre_seq) && $modif_ordre_seq == 1)
{
   $titre_seq = GetDataField ($connect,"select seq_titre_lb from sequence where seq_cdn = $seq_actuelle","seq_titre_lb");
   if ($id_classe == 0)
   {
      $prescripteur = GetDataField ($connect,"select presc_prescripteur_no from prescription_$id_grp where
                                           presc_seq_no = $seq_actuelle and
                                           presc_grp_no=$id_grp","presc_prescripteur_no");
      $formateur = GetDataField ($connect,"select presc_formateur_no from prescription_$id_grp where
                                        presc_seq_no = $seq_actuelle and
                                        presc_grp_no=$id_grp","presc_formateur_no");
      $le_parc = GetDataField ($connect,"select presc_parc_no from prescription_$id_grp where
                                      presc_seq_no = $seq_actuelle and
                                      presc_grp_no=$id_grp","presc_parc_no");
      if ($formateur == $id_user)
         $qualite = "Formateur";
      elseif ($prescripteur == $id_user)
         $qualite = "Prescripteur";
      elseif ($prescripteur != $id_user && $inscripteur != $id_user && $typ_user == "ADMINISTRATEUR")
         $qualite = "Administrateur";
      else
         $qualite = "";
      $action_fiche = $gest_presc;
      $commentaire = $mess_change_ordseq.$titre_seq." $ordre_seq $mess_rep_item $ordre_precedent";
      $util_grp_query = mysql_query ("SELECT utilgr_utilisateur_no FROM utilisateur_groupe WHERE utilgr_groupe_no = $id_grp");
      $nb_util = mysql_num_rows ($util_grp_query);
      $j = 0;
      while ($j < $nb_util)
      {
         $id_util = mysql_result ($util_grp_query,$j,"utilgr_utilisateur_no");
         if (isset($_GET['seq_precedent']))
         {
            $modif_ordre = mysql_query("UPDATE suivi2_$id_grp SET
                               suiv2_ordre_no = $ordre_precedent where
                               suiv2_seq_no = $seq_actuelle and
                               suiv2_utilisateur_no = $id_util and
                               suiv2_grp_no=$id_grp");
            $modif_ordre = mysql_query("UPDATE suivi2_$id_grp SET
                               suiv2_ordre_no = $ordre_seq where
                               suiv2_seq_no = $seq_precedent and
                               suiv2_utilisateur_no = $id_util and
                               suiv2_grp_no=$id_grp");
         }
         elseif (isset($_GET['seq_suivant']))
         {
            $modif_ordre = mysql_query("UPDATE suivi2_$id_grp SET
                               suiv2_ordre_no = $ordre_suivant where
                               suiv2_seq_no = $seq_actuelle and
                               suiv2_utilisateur_no = $id_util and
                               suiv2_grp_no=$id_grp");
            $modif_ordre = mysql_query("UPDATE suivi2_$id_grp SET
                               suiv2_ordre_no = $ordre_seq where
                               suiv2_seq_no = $seq_suivant and
                               suiv2_utilisateur_no = $id_util and
                               suiv2_grp_no=$id_grp");
         }
         $new_fiche = Donne_ID ($connect,"select max(fiche_cdn) from fiche_suivi");
         $req_fiche = mysql_query("INSERT INTO fiche_suivi
                             (fiche_cdn,fiche_utilisateur_no,fiche_auteur_no,fiche_qualite_lb,fiche_date_dt,fiche_heure_dt,
                             fiche_commentaire_cmt,fiche_grp_no,fiche_parc_no,fiche_seq_no,fiche_act_no,fiche_typaction_lb) VALUES
                             ($new_fiche,$id_util,$id_user,'$qualite','$date_fiche','$heure_fiche',\"$commentaire\",
                             $id_grp,$id_parc,$seq_actuelle,0,\"$action_fiche\")");
        $j++;
      }
      $modif_ordre = mysql_query("UPDATE sequence_parcours SET
                                  seqparc_ordre_no = $ordre_precedent where
                                  seqparc_seq_no = $seq_actuelle and
                                  seqparc_parc_no = $id_parc");
      $modif_ordre = mysql_query("UPDATE sequence_parcours SET
                                  seqparc_ordre_no = $ordre_seq where
                                  seqparc_seq_no = $seq_precedent and
                                  seqparc_parc_no = $id_parc");
      $mess_notif = $commentaire;

   }
   $modif_ordre_seq = 0;
}
//echo "<pre>";print_r($_POST);echo "</pre>";
if (isset($modifier) && $modifier == 1)
{
 require_once ('class/class_module.php');
 $signal = array();
 $nn = 0;
 $mess_aff = '';
 while ($nn < $i_parc)
 {
   $passage_deb = 0;
   $passage_fin = 0;
   $id_parc = $parcours[$nn];
   $titre_parc = GetDataField ($connect,"select parcours_nom_lb from parcours where parcours_cdn=$id_parc","parcours_nom_lb");
   if ($form_ref[$id_parc] == -1)
   {
      $mess_aff .= "<LI><B>".$titre_parc."</B> : ".$mpr_avert8."</LI>";
      $signal[$id_parc] = 1;
   }
   if (isset($presc_rf[$id_parc]) && $presc_rf[$id_parc] == -1)
   {
      $mess_aff .= "<LI><B>".$titre_parc."</B> : ".$mpr_avert7."</LI>";
      $signal[$id_parc] = 1;
   }
   $dt_db = GetDataField ($connect,"select gp_db_dt from groupe_parcours where gp_parc_no = $id_parc","gp_db_dt");
   $date_debut[$id_parc] = str_replace ("-","/",$date_debut[$id_parc]);
   $ch_date_deb = explode("/",$date_debut[$id_parc]);
   $date_deb = "$ch_date_deb[2]-$ch_date_deb[1]-$ch_date_deb[0]";
   $jour=intval($ch_date_deb[0]);
   $mois=intval($ch_date_deb[1]);
   $an=$ch_date_deb[2];
   $retour=@jour_mois($mois,$an);
   if($jour > $retour && $mois < 13)
   {
      $mess_aff .= "<LI><B>".$titre_parc."</B> : ".ucfirst($mess_dt_deb_seq)."<B> ..".$date_debut[$id_parc]."</B> : $il_nya_que <B>$retour</B> $mess_jours $en <B>".le_mois($mois)." </B></LI>";
      $passage_deb++;
      $signal[$id_parc] = 1;
   }
   $dt_fn = GetDataField ($connect,"select gp_df_dt from groupe_parcours where gp_parc_no = $id_parc","gp_df_dt");
   $date_finale[$id_parc] = str_replace ("-","/",$date_finale[$id_parc]);
   $ch_date_fin = explode("/",$date_finale[$id_parc]);
   $date_fin = "$ch_date_fin[2]-$ch_date_fin[1]-$ch_date_fin[0]";
   $jour=intval($ch_date_fin[0]);
   $mois=intval($ch_date_fin[1]);
   $an=$ch_date_fin[2];
   $retour=@jour_mois($mois,$an);
   if($jour > $retour && $mois < 13){
      $mess_aff .= "<LI><B>".$titre_parc."</B> : ".ucfirst($mess_dt_fin_seq)."<B>..".$date_finale[$id_parc]."</B> : $il_nya_que <B>$retour</B> $mess_jours $en <B>".le_mois($mois)." </B></LI>";
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
     $mess_aff .= "<LI>$mess_gp_deb_form : ".$date_debut[$id_parc]." $et $mess_gp_fin_form : ".$date_finale[$id_parc]." $de <B>".$titre_parc."</B> : ".$mpr_avert1."</LI>";
      $signal[$id_parc] = 1;
   }
   if ((substr($date_debut[$id_parc],2,1) != "/") || (substr($date_debut[$id_parc],5,1) != "/") || (strlen($date_debut[$id_parc]) != 10) || ($ch_date_deb[2] < 2003) || ($ch_date_deb[1] > 12) || ($ch_date_deb[0] > 31) || ($ch_date_deb[0] < 1) || ($ch_date_deb[1] < 1))
   {
     $mess_aff .= "<LI>".$date_debut[$id_parc]." : ".ucfirst($mess_dt_deb_seq)." <B>".$titre_parc."</B> ".$no_valid."</LI>";
     $passage_deb++;
      $signal[$id_parc] = 1;
   }
   if ((substr($date_finale[$id_parc],2,1) != "/") || (substr($date_finale[$id_parc],5,1) != "/") || (strlen($date_finale[$id_parc]) != 10) || ($ch_date_fin[2] < 2003) || ($ch_date_fin[1] > 12) || ($ch_date_fin[0] > 31) || ($ch_date_fin[0] < 1) || ($ch_date_fin[1] < 1))
   {
     $mess_aff .= "<LI>".$date_finale[$id_parc]." : ".ucfirst($mess_dt_fin_seq)." <B>".$titre_parc. "</B> ".$no_valid."</LI>";
     $passage_fin++;
      $signal[$id_parc] = 1;
   }
   if ($date_deb != $dt_db && $passage_deb == 0)
   {
      $requete = mysql_query("UPDATE groupe_parcours set gp_db_dt = '$date_deb' where
                              gp_parc_no = $id_parc AND
                              gp_grp_no = $id_grp");
      if ($id_classe == 0)
         $requete = mysql_query("UPDATE prescription_$id_grp set
                                 presc_datedeb_dt = '$date_deb' where
                                 presc_parc_no = $id_parc AND
                                 presc_grp_no = $id_grp");
   }
   if ($date_fin != $dt_fn && $passage_fin == 0)
   {
      $requete = mysql_query("UPDATE groupe_parcours set
                              gp_df_dt = '$date_fin' where
                              gp_parc_no = $id_parc AND
                              gp_grp_no = $id_grp");
      if ($id_classe == 0)
         $requete = mysql_query("UPDATE prescription_$id_grp set
                                 presc_datefin_dt = '$date_fin' where
                                 presc_parc_no = $id_parc AND
                                 presc_grp_no = $id_grp");
   }
   $formateur = GetDataField ($connect,"select gp_formateur_no from groupe_parcours where
                                        gp_parc_no = $id_parc AND
                                        gp_grp_no = $id_grp","gp_formateur_no");
   if ($formateur != $form_ref[$id_parc] && $form_ref[$id_parc] != -1)
   {
      $requete = mysql_query("UPDATE groupe_parcours set gp_formateur_no = $form_ref[$id_parc] where
                              gp_parc_no = $id_parc AND
                              gp_grp_no = $id_grp");
      if ($id_classe == 0)
         $requete = mysql_query("UPDATE prescription_$id_grp set
                                 presc_formateur_no = $form_ref[$id_parc] where
                                 presc_parc_no = $id_parc AND
                                 presc_grp_no = $id_grp");
   }
 $nn++;
 }
 $i_parc = 0;
 $nn = 0;
if ($id_classe == 1)
    ClassModuleActualiseOrdre($id_grp);
 if ($mess_aff != '')
 {
     $mess_notif = "$err_saisie *$err_saisie1 $mess_aff";
 }
}
include 'style.inc.php';
if (isset($mess_notif) && $mess_notif != '')
{
        echo notifier($mess_notif);
}
// Principal
   $nom_grp = GetDataField ($connect,"select grp_nom_lb from groupe where grp_cdn  = $id_grp","grp_nom_lb");
   $resp_grp = GetDataField ($connect,"select grp_resp_no from groupe where grp_cdn  = $id_grp","grp_resp_no");
   $parc_query = mysql_query ("select * from parcours,groupe_parcours where
                               groupe_parcours.gp_grp_no = $id_grp AND
                               parcours.parcours_cdn = groupe_parcours.gp_parc_no
                               order by groupe_parcours.gp_ordre_no asc");
   $nb_parc = mysql_num_rows ($parc_query);
   $num_pg = array();
   $i_parc = $nb_parc;
   echo "<CENTER><TABLE bgColor='#298CA0' cellspacing='2' cellpadding='0' width='98%'><TR>";
   include ("liste_gp_parc.inc.php");
   echo "<TD valign='top'>";
   echo "<CENTER><TABLE bgColor='#FFFFFF' cellspacing='0' border=0 cellpadding='0' width='100%' height='100%'><TR><TD valign='top' width='100%' height='100%'>";
   echo "<CENTER><TABLE cellspacing='1' border=0 cellpadding='0' width='100%'  height='100%'>";
   $titre = $modif_parc_grp." $nom_grp";
   echo "<TR><TD background=\"images/fond_titre_table.jpg\" colspan='2' height='40' align='center' valign='center'><B><Font size='3' color='#FFFFFF'>$titre</FONT></b></TD></TR>";
          ?>
              <SCRIPT language="JavaScript">
                  function checkForm1(frm) {
                        var ErrMsg = "<?php echo $mess_info_no;?>\n\n";
                        var lenInit = ErrMsg.length;
                        if (isEmpty(frm.search)==true)
                            ErrMsg += ' - <?php echo $msq_parc;?>\n';
                        if (isEmpty(frm.search)==false && frm.parc == undefined)
                            ErrMsg += ' - <?php echo "Le module doit être choisi parmi la liste des modules existants.";?>\n';
                        if (ErrMsg.length > lenInit)
                            alert(ErrMsg);
                        else
                        {
                            frm.submit();
                        }
                  }
                  function isEmpty(elm) {
                        var elmstr = elm.value + "";
                        if (elmstr.length == 0)
                            return true;
                        return false;
                  }
              </SCRIPT>
           <?php
           echo "<TR><TD style='padding-left: 120px;min-height:100px;height:100px;' valign='top'>".
                "<b>&nbsp;Faites une recherche de module par mot-clef en saisissant 3 caractères au moins</b><br />";
           echo "<form  name='ma_formule' action=\"modif_gp.php\" method='post'>";
          $table="parcours";
          $fieldLabel="parcours_nom_lb";
          $fieldId="parcours_cdn";
          $fieldCond=" AND parcours_cdn != 0 AND parcours_cdn NOT IN(select gp_parc_no from groupe_parcours where gp_grp_no = $id_grp)";// AND seq_auteur_no = $id_user
          $HideLabel="parc";
          include ("OutilsJs/DivPopulator/DivPopulator.php");
          echo "<INPUT TYPE='HIDDEN' NAME='prescrire_grp' VALUE='1' />";
          echo "<INPUT TYPE='HIDDEN' NAME='id_grp' VALUE='$id_grp' />";
          echo "<INPUT TYPE='HIDDEN' NAME='miens_parc' VALUE='$miens_parc' />";
          echo "<INPUT TYPE='HIDDEN' NAME='refer' VALUE='$refer' />";
          echo "<INPUT TYPE='HIDDEN' NAME='comeback' VALUE='liste_gp' />";
          echo "</TD>";
          echo "<TD align='left' valign='top' style='padding-top:25px;'>".
               "<A HREF=\"javascript:checkForm1(document.ma_formule);\" ".
               "onmouseover=\"img1.src='images/fiche_identite/boutvalidb.gif';return true;\" ".
               "onmouseout=\"img1.src='images/fiche_identite/boutvalid.gif'\">".
               "<IMG NAME=\"img1\" SRC=\"images/fiche_identite/boutvalid.gif\" BORDER='0' ".
               "onLoad=\"tempImg=new Image(0,0); tempImg.src='images/fiche_identite/boutvalidb.gif'\"></A>".
               "</TD></form>";
          echo "</TR>";
   if (!isset($prescrire_grp) && $prescrire_grp == '' && $nb_parc == 0)
   {
      echo "<TR height='100%'><TD valign='top' align='center'><H4>$mess_noparc_grp</H4></TD></TR>";
      echo "</TABLE></TD></TR></TABLE></TD></TR></TABLE>";
     exit;
   }
//********************************
    $bgcolorB = '#2B677A';
    $bgcolorA = '#FFEDD9';
    echo "<TR><TD valign='top' colspan=2 style=\"height:100%;\"><TABLE width=100% bgColor='#FFFFFF' cellspacing='0' cellpadding='0' border='0'>";
    if (!isset($prescrire_grp) && $prescrire_grp == '')
    {
       echo "<FORM NAME='formule' action=\"modif_gp.php\" method='post' target='main'>";
       echo "<INPUT TYPE='HIDDEN' NAME='modifier' VALUE='1'>";
    }
        echo "<TR height='25'>";
        if ($typ_user == "ADMINISTRATEUR" || $resp_grp == $id_user)
             echo "<TD class='barre_titre' width='20'>&nbsp;</TD>";
        echo "<TD class='barre_titre'>$mess_gp_parc_appr</TD>";
        echo "<TD class='barre_titre' style=\"text-align :left;\">$mess_gp_duree</TD>";
        echo "<TD class='barre_titre'>$msq_formateur</TD>";
        echo "<TD class='barre_titre'>$mess_gp_date_deb</TD>";
        echo "<TD class='barre_titre'>$mess_gp_date_fin</TD>";
        if (!isset($prescrire_grp) && $prescrire_grp == '')
           echo "<TD class='barre_titre'>$mess_ag_supp</TD>";
        else
           echo "<TD class='barre_titre'>$mess_gen_valider</TD>";
        echo "</TR>";
        if ($lg == "fr")
           setlocale(LC_TIME,'fr_FR');
       elseif($lg == "ru")
           setlocale(LC_TIME,'ru_RU');
       if (isset($prescrire_grp) && $prescrire_grp == 1)
            include ('include/prescrire_grp.inc.php');
       $p=0;
       $debut_parc = array();
       $date_f = array();
       $fin_parc = array();
       $formateur_parc = array();
       $num_parc = array();
       $ordre_p = array();
       while ($p < $nb_parc)
       {
              $id_parc = mysql_result ($parc_query,$p,"gp_parc_no");
              $debut_parc[$id_parc] = mysql_result ($parc_query,$p,"gp_db_dt");
              $fin_parc[$id_parc] = mysql_result ($parc_query,$p,"gp_df_dt");
              $date_f[$p] = $fin_parc[$id_parc];
              $num_parc[$p] = $id_parc;
              $ordre_parc = mysql_result ($parc_query,$p,"gp_ordre_no");
              $ordre_p[$p] = $ordre_parc;
              if ($p > 0)
              {
                 $parc_precedent = $num_parc[$p-1];
                 $ordre_precedent = $ordre_p[$p-1];
              }
              $date_creat = mysql_result ($parc_query,$p,"parcours_create_dt");
              $ch_dtc = explode("-",$date_creat);
              $dtc = strftime("%A %e %B %Y", mktime(0, 0, 0, $ch_dtc[1],$ch_dtc[2],$ch_dtc[0]));
              $date_modif = mysql_result ($parc_query,$p,"parcours_modif_dt");
              $ch_dtm = explode("-",$date_modif);
              $dtm = strftime("%A %e %B %Y", mktime(0, 0, 0, $ch_dtm[1],$ch_dtm[2],$ch_dtm[0]));
              $formateur_parc[$id_parc] = mysql_result ($parc_query,$p,"gp_formateur_no");
              $createur = mysql_result ($parc_query,$p,"parcours_auteur_no");
              $nom = mysql_result ($parc_query,$p,"parcours_nom_lb");
              $description = mysql_result ($parc_query,$p,"parcours_desc_cmt");
              $description = str_replace("\r\n","<BR>",trim($description));
              $id_ref_parc = mysql_result ($parc_query,$p,"parcours_referentiel_no");
              if ($id_ref_parc > 0){
                 $nom_referentiel = GetDataField ($connect,"select ref_desc_cmt from referentiel where ref_cdn = $id_ref_parc","ref_desc_cmt");
                 $desc = $nom_referentiel;
              }
              $type_parcours = getdatafield ($connect,"SELECT parcours_type_lb from parcours where parcours_cdn = $id_parc","parcours_type_lb");
              $nom_createur = GetDataField ($connect,"select util_nom_lb from utilisateur where util_cdn = $createur","util_nom_lb");
              $prenom_createur = GetDataField ($connect,"select util_prenom_lb from utilisateur where util_cdn = $createur","util_prenom_lb");
              $nom_parc=$nom;
              echo "<TR bgcolor=#e9e9e9>";
              if ($typ_user == "ADMINISTRATEUR" || $resp_grp == $id_user)
              {
                 $lien = "modif_gp.php?a_faire=1&modif_ordre=1&parc=$id_parc&ordre_parc=$ordre_parc&ancien_ordre=$ordre_precedent&ancien_parc=$parc_precedent&id_grp=$id_grp&hgrp=$hgrp";
                 $lien = urlencode($lien);
                 //if ($date_f[$p] == $date_f[$p-1] && $p > 0 && !isset($prescrire_grp) && $prescrire_grp == '')
                 if ($p > 0 && !isset($prescrire_grp) && $prescrire_grp == '')
                      echo "<TD height='20' width='20' valign='center' align='center'><A HREF=\"trace.php?link=$lien\" target='main' title=\"$mess_decrement\"><IMG SRC=\"images/modules/tut_form/flechehaut.gif\" ALT=\"$mess_decrement\" border='0'></TD>";
                 else
                      echo "<TD height='20' width='20'>&nbsp;</TD>";
              }
              if ($desc == "")
                 $mesg_nom = "<B>$mess_desc : </B>$description<BR><B>$mrc_aut : </B>$prenom_createur $nom_createur <BR><B>$mess_menu_gest_seq_ref :</B> $dtc<BR><B>$mess_modif_dt :</B> $dtm";
              else
                 $mesg_nom = "<B>$msq_act_ref</B> $desc <BR><B>$mess_desc : </B>$description<BR><B>$mrc_aut : </B>$prenom_createur $nom_createur  <BR><B>$mess_menu_gest_seq_ref :</B> $dtc<BR><B>$mess_modif_dt :</B> $dtm";
              echo "<TD align='left'><a href=\"javascript:void(0);\" target='main' ".bulle($mmsg_mod_ssref,"","LEFT","ABOVE",320)."&nbsp;";
              if (isset($signal[$id_parc]) && $signal[$id_parc] == 1)
                 echo  "$nom</A> *";
              else
                echo  "$nom</A>";
              echo "</TD>";
              $seq_parc_query = mysql_query ("select sum(sequence.seq_duree_nb) from sequence,sequence_parcours
                                              where sequence.seq_cdn = sequence_parcours.seqparc_seq_no AND
                                              sequence_parcours.seqparc_parc_no = $id_parc");
              $duree_parc = mysql_result($seq_parc_query,0);
              $duree_parc = duree_calc($duree_parc);
              echo "<TD nowrap style=\"background-color:#e9e9e9; font-size:12px;font-family: arial;font-weight:bold; text-align: left;\"><B>$duree_parc</B></TD><TD>";
              $form_mod_nom = GetDataField ($connect,"SELECT util_nom_lb from utilisateur where util_cdn='$formateur_parc[$id_parc]'","util_nom_lb");
              $form_mod_prenom = GetDataField ($connect,"SELECT util_prenom_lb from utilisateur where util_cdn='$formateur_parc[$id_parc]'","util_prenom_lb");
              $param = $formateur_parc[$id_parc];
              if (!isset($prescrire_grp) && $prescrire_grp == '')
                 Ascenseur_mult ("form_ref[$id_parc]","select util_cdn,util_nom_lb,util_prenom_lb from utilisateur where (util_typutil_lb = 'FORMATEUR_REFERENT' or util_typutil_lb = 'RESPONSABLE_FORMATION' or util_typutil_lb = 'ADMINISTRATEUR') AND util_flag = 0 order by util_nom_lb ASC",$connect,$param);
              else
                echo $form_mod_nom.' '.$form_mod_prenom;
              $ch_date_deb = explode ("-",$debut_parc[$id_parc]);
              $date_min = "$ch_date_deb[2]/$ch_date_deb[1]/$ch_date_deb[0]";
              $ch_date_fin = explode ("-",$fin_parc[$id_parc]);
              $date_max = "$ch_date_fin[2]/$ch_date_fin[1]/$ch_date_fin[0]";
              if (!isset($prescrire_grp) && $prescrire_grp == '')
                 echo "</TD><TD><INPUT TYPE=TEXT class='INPUT' NAME=date_debut[$id_parc] value= '$date_min' MAXLENGTH='10' size='10' onfocus=\"this.select();lcs(this)\" onclick=\"event.cancelBubble=true;this.select();lcs(this)\"></TD>";
              else
                 echo "</TD><TD align='left'>$date_min</TD>";
              echo "<INPUT TYPE='HIDDEN' NAME='parcours[$p]' VALUE='$id_parc'>";
              if (!isset($prescrire_grp) && $prescrire_grp == '')
                 echo "<TD align='right'><INPUT TYPE= TEXT class='INPUT' NAME=date_finale[$id_parc] value= '$date_max' MAXLENGTH='10' size='10' onfocus=\"this.select();lcs(this)\" onclick=\"event.cancelBubble=true;this.select();lcs(this)\"></TD>";
              else
                 echo "<TD align='left'>$date_max</TD>";
                 $lien = "modif_gp.php?supprimer=1&id_parc=$id_parc&id_grp=$id_grp";
                 $lien = urlencode($lien);
              if (!isset($prescrire_grp) && $prescrire_grp == ''){
                 echo "<TD align='middle'><A href=\"javascript:void(0);\" onclick=\"javascript:return(confm('trace.php?link=$lien'));\" target='main'>";
                 echo "<IMG SRC=\"images/messagerie/icopoubelressour.gif\" BORDER=0 ".
                      "ALT=\"$mess_ag_supp\">&nbsp;&nbsp;</A></TD>";
              }else
               echo "<TD>&nbsp;</TD>";
              echo "</TR><TR>";
              $cols = ($id_classe == 1) ? 2 : 2;
              if ($typ_user == "ADMINISTRATEUR" || $resp_grp == $id_user)
              {
                echo "<TD></TD>";
                echo "<TD colspan='$cols'>";
              }
              else
                echo "<TD colspan='$cols'>";
//Sequences a faire dans le parcours déroulé
  if (!isset($prescrire_grp) && $prescrire_grp == '')
  {
    $ouvrir = 'parcours';
    $parc_ouvert = $id_parc;
    $grp_parc = mysql_result(mysql_query("select count(*) from utilisateur_groupe where utilgr_groupe_no=$id_grp"),0);
    $champ_encours =  ($id_classe == 1 || $grp_parc == 0) ? "seqparc_seq_no" : "presc_seq_no";
    if ($id_classe == 1 || $grp_parc == 0)
        $seq_query = mysql_query ("SELECT seqparc_seq_no FROM sequence_parcours WHERE ".
                                  "seqparc_parc_no = $id_parc ORDER BY seqparc_ordre_no asc");
    elseif ($id_classe == 0 && $grp_parc > 0)
        $seq_query = mysql_query ("SELECT DISTINCT prescription_$id_grp.presc_seq_no,sequence_parcours.seqparc_ordre_no FROM
                                   sequence_parcours, prescription_$id_grp WHERE
                                   sequence_parcours.seqparc_parc_no = '$id_parc' AND
                                   prescription_$id_grp.presc_seq_no = sequence_parcours.seqparc_seq_no AND
                                   prescription_$id_grp.presc_parc_no = sequence_parcours.seqparc_parc_no
                                   ORDER BY seqparc_ordre_no ASC");
    $nb_seq = mysql_num_rows ($seq_query);
       echo "<TABLE width='100%'>";
       echo "<TR bgcolor=#F4F4F4>";
       echo "<TD align='left'><b>$mess_gp_seq_parc</b></TD>";
       echo "<TD style=\"background-color:#EFEFEF; height:20px; width:70px;font-size: 12px;font-family: arial;font-weight:bold; text-align: center;\">$mess_gp_duree</TD>";
       echo "</TR>";
       $i = 0;
       $num_seq = array();
       $ordre_s = array();
       while ($i != $nb_seq) {
              $duree = 0;
              $seq = mysql_result ($seq_query,$i,$champ_encours);
              $comb = GetDataField ($connect,"select seq_type_on from sequence where
                                                      seq_cdn = $seq","seq_type_on");
              if ($comb == 1){
                 $i++;
                 continue;
              }
              $num_seq[$i] = $seq;
              if ($id_classe == 0){
                 $ordre_seq = GetDataField ($connect,"select seqparc_ordre_no from sequence_parcours where
                                                      seqparc_seq_no = $seq and
                                                      seqparc_parc_no = $id_parc","seqparc_ordre_no");
                 $ordre_s[$i] = $ordre_seq;
                 if ($i > 0){
                    $seq_precedent = $num_seq[$i-1];
                    $ordre_precedent = $ordre_s[$i-1];
                 }
              }
              $desc_seq = GetDataField ($connect,"select seq_desc_cmt from sequence where seq_cdn=$seq","seq_desc_cmt");
              $duree_seq = GetDataField ($connect,"select seq_duree_nb from sequence where seq_cdn=$seq","seq_duree_nb");
              $nom_seq = GetDataField ($connect,"select seq_titre_lb from sequence where seq_cdn=$seq","seq_titre_lb");
              $type_seq = GetDataField ($connect,"select seq_type_lb from sequence where seq_cdn=$seq","seq_type_lb");
             //***************************************************************************************************
                  if (isset($signal[$seq]) && $signal[$seq] == 1)
                     $nom_seq .=" *";
                  if (strstr($type_seq,"SCORM"))
                     $logo_sco = "<IMG SRC='images/gest_parc/scorm.gif' border='0' alt=\"$mess_seq_sco\">";
                  else
                     $logo_sco = "- ";
                  echo  "<TD>$logo_sco <B><FONT size='1'>$nom_seq</FONT></B></TD>";
                  if ($duree_seq > 0)
                     $duree_seq = duree_calc($duree_seq);
                  $laduree = ($duree_seq == 0) ? "" : $duree_seq;
                  echo "<TD align='center' style=\"background-color:#EFEFEF; width:70px;font-size: 12px;font-family: arial; text-align: center;\">$laduree</td>";
          echo "</TR>";
          $i++;
          $pointeur = 0;
          $visible = 0;
       }//  fin while ($i != $nb_seq)
       echo"</TABLE></TD></TR>";
     }
    $p++;
    }// fin while ($p < $nb_parc)
  if (!isset($prescrire_grp) && $prescrire_grp == '' && $nb_parc > 0)
  {
    echo "<INPUT TYPE='HIDDEN' NAME='id_grp' VALUE='$id_grp'>";
    echo "<INPUT TYPE='HIDDEN' NAME='i_parc' VALUE='$i_parc'>";
    echo "<INPUT TYPE='HIDDEN' NAME='a_faire' VALUE='1'>";
    $lien = "prescription.php?prem=1&presc=groupe&groupe=$id_grp";
    $lien = urlencode($lien);
    if ($typ_user == "ADMINISTRATEUR" || $resp_grp == $id_user)
      echo "<TR bgcolor='#FFFFFF' height='40'><TD colspan='7' width='100%' align='right' style=\"padding-right:10px;\">";
    else
      echo "<TR bgcolor='#FFFFFF' height='40'><TD colspan='6' width='100%' align='right' style=\"padding-right:10px;\">";
    echo "<A href=\"javascript:document.formule.submit();\" TITLE=\"$mess_gen_valider\" ".
         "onmouseover=\"img2.src='images/fiche_identite/boutvalidb.gif';return true;\" ".
         "onmouseout=\"img2.src='images/fiche_identite/boutvalid.gif'\">";
    echo "<IMG NAME=\"img2\" SRC=\"images/fiche_identite/boutvalid.gif\" BORDER='0' ".
         "onLoad=\"tempImg=new Image(0,0); tempImg.src='images/fiche_identite/boutvalidb.gif'\"></A>";
 }
    echo "</TD></TR></TABLE>";
    echo "</TD></TR></TABLE></TD></TR></TABLE>";
?>
<DIV ID=Main>
     <!-- A Separate Layer for the Calendar -->
     <!-- Make sure to use the name Calendar for this layer -->
     <SCRIPT Language="Javascript" TYPE="text/javascript">
             Calendar.CreateCalendarLayer(10, 275, "");
     </SCRIPT>
</DIV>
