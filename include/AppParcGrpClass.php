<?php

$isError = "";
$compteSeq = 0;
$compte = 0;
$duree_totale = array();
if (!empty ($_POST))
{
?>
<script language="JavaScript">
   $(document).ready(function(){
     $('#patience').css('display','block');
   });
</script>
<?php
     //echo "<pre>";print_r($_POST);echo"</pre>";exit;
        for ($i = 0; $i < $_POST['compteApp'] + 1; $i++)
        {
                if (isset($_POST['envoi'][$i]) && $_POST['envoi'][$i]== 'on')
                        $compte++;
        }
        $titre_parc=array();
        if ($compte == 0)
                $isError .= " &nbsp;- Vous n'avez choisi aucun apprenant avant de valider. <br>";
        for ($m = 0; $m < $_POST['p']; $m++)
        {
                $duree_totale[$m] = 0;
                $titre_parc[$m] = GetDataField($connect, "select parcours_nom_lb from parcours where parcours_cdn = ".$_POST['module'][$m], "parcours_nom_lb");
                if ($_POST['form_ref'][$m] == -1)
                {
                        $isError .= " &nbsp;- Formateur non renseigné pour les séquences du Module ".$titre_parc[$m]." <br>";
                }
                if ($_POST['date_debut'][$m] == '' || !preg_match('#^[0-9]{2}\/[0-9]{2}\/[0-9]{4}$#', $_POST['date_debut'][$m]))
                {
                        $isError .= ($_POST['date_debut'][$m] == '') ? " &nbsp;- Date de début non renseignée les séquences du Module ".$titre_parc[$m]." <br>" : "&nbsp;- Vous avez saisi un format de date non autorisé pour le Module/séquences ".$titre_parc.". Utilisez le calendrier pour choisir la date!! ";
                }
                if ($_POST['date_finale'][$m] == '' || !preg_match('#^[0-9]{2}\/[0-9]{2}\/[0-9]{4}$#', $_POST['date_finale'][$m]))
                {
                        $isError .= ($_POST['date_finale'][$m] == '') ? "&nbsp; - Date de fin non renseignée les séquences du Module ".$titre_parc[$m]." <br>" : "&nbsp;- Vous avez saisi un format de date non autorisé pour le Module/séquences ".$titre_parc.". Utilisez le calendrier pour choisir la date!! ";
                }
                for ($s = 0; $s < $_POST['i'][$m] + 1; $s++)
                {
                        if (isset($_POST['sequence'][$m][$s]) && $_POST['sequence'][$m][$s] == 'on')
                        {
                                $compteSeq++;
                                if (isset($_POST['duree_seq'][$m][$s]))
                                   $duree_totale[$m] += $_POST['duree_seq'][$m][$s];
                        }
                }
        }
        if ($compteSeq == 0)
                        $isError .= "&nbsp;- Vous n'avez choisi aucune séquences avant de valider. <br>";
}
if (empty ($isError))
{
        $mess_notif = "";
        $comptage = 0;
        $prescripteur = $id_user;
        for ($uu = 0; $uu < $_POST['compteApp'] + 1; $uu++)
        {
                if (isset($_POST['envoi'][$uu]) && $_POST['envoi'][$uu] == 'on')
                {
                        $id_app = $_POST['util'][$uu];
                        for ($mm = 0; $mm < $_POST['p']; $mm++)
                        {
                                $num_parc = $_POST['module'][$mm];
                                $num_form = $_POST['form_ref'][$mm];
                                $presc_grp_exist = mysql_result(mysql_query("select count(*) from prescription_$id_grp where presc_utilisateur_no=$id_app"),0);
                                if ($presc_grp_exist > 0)
                                   $num_ordre = Donne_ID($connect, "SELECT max(presc_ordre_no) from prescription_$id_grp where  presc_utilisateur_no=$id_app");
                                else
                                   $num_ordre = '1';
                                $duree_parc = $duree_totale[$mm];
                                $date_deb = preg_replace('#^([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{2,4})$#i', '$3-$2-$1',$_POST['date_debut'][$mm]);
                                $date_fin = preg_replace('#^([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{2,4})$#i', '$3-$2-$1',$_POST['date_finale'][$mm]);
                                $nb_date_fin_query = mysql_query("SELECT TO_DAYS('$date_fin')");
                                $nb_dat_fin = mysql_result($nb_date_fin_query, 0);
                                $nb_date_deb_query = mysql_query("SELECT TO_DAYS('$date_deb')");
                                $nb_dat_deb = mysql_result($nb_date_deb_query, 0);
                                if ($nb_dat_deb > $nb_dat_fin && $_POST['action'] == 'ajouter')
                                {
                                        $nb_dat_fin = $nb_dat_deb +20;
                                        $date_fin = mysql_result(mysql_query("SELECT FROM_DAYS('$nb_dat_fin')"), 0);
                                        if (empty ($mess_notif) || strstr($mess_notif, "$titre_parc"))
                                                $mess_notif .= "&nbsp; - La date de fin pour le module \"".$titre_parc[$mm]."\" était antérieure à la date de début. ".
                                                                "Formagri a fait une première correction <br /> Veuillez contrôler SVP <br/>";
                                }
                                for ($ss = 0; $ss < $_POST['i'][$mm] + 1; $ss++)
                                {
                                        $modif = 0;
                                        if (isset($_POST['sequence'][$mm][$ss]) && $_POST['sequence'][$mm][$ss] == 'on')
                                        {
                                                $seq = $_POST['num_seq'][$mm][$ss];
                                                $scormOk = 0;
                                                $msgAction='';
                                                $parc_exist = mysql_result(mysql_query("select count(*) from suivi3_$id_grp where suiv3_utilisateur_no=$id_app and suiv3_parc_no=$num_parc"),0);
                                                $seq_exist = mysql_result(mysql_query("select count(*) from suivi2_$id_grp where suiv2_utilisateur_no=$id_app and suiv2_seq_no=$seq"),0);
                                                $presc_seq_exist = mysql_result(mysql_query("select count(*) from prescription_$id_grp where presc_utilisateur_no=$id_app and presc_seq_no=$seq and presc_parc_no=$num_parc"),0);
                                                $id_suivi3 = Donne_ID($connect, "SELECT max(suiv3_cdn) from suivi3_$id_grp");
                                                if ($parc_exist == 0 && $_POST['action'] == 'ajouter')
                                                        $ins_suivi3 = mysql_query("insert into suivi3_$id_grp values ($id_suivi3,$id_app,$num_parc,'A FAIRE',$duree_parc,'$id_grp')");
                                                if ($seq_exist > 0 && $_POST['action'] == 'ajouter' && isset($_POST['repare']) && $_POST['repare'] == 'on')
                                                       $modif = 1;
                                                if ($modif == 0)
                                                {
                                                  $ordre_seq = Donne_ID($connect, "SELECT max(suiv2_ordre_no) from suivi2_$id_grp,prescription_$id_grp where
                                                                                   suiv2_utilisateur_no=$id_app and presc_utilisateur_no=suiv2_utilisateur_no
                                                                                    and presc_parc_no=$num_parc and suiv2_seq_no=presc_seq_no");
                                                  $duree_sequence = $_POST['duree_seq'][$mm][$ss];
                                                  $titre_seq = GetDataField($connect, "select seq_titre_lb from sequence where seq_cdn=$seq", "seq_titre_lb");
                                                  $id_suivi2 = Donne_ID($connect, "SELECT max(suiv2_cdn) from suivi2_$id_grp");
                                                  $id_presc = Donne_ID($connect, "SELECT max(presc_cdn) from prescription_$id_grp");
                                                }
                                                $formateur = $num_form;
                                                if ($_POST['action'] == 'ajouter' && $modif == 0 && $seq_exist == 0 && $presc_seq_exist == 0)
                                                {
                                                        $msgAction = $mess_presc_fiche." ".$titre_seq."\n $msq_parc : ".$titre_parc[$mm];
                                                        $ins_presc = mysql_query("insert into prescription_$id_grp values ($id_presc,$seq,$num_parc,$id_app,'$date_deb','$date_fin',$prescripteur,$formateur,'$id_grp',$num_ordre)");
                                                        $ins_suivi2 = mysql_query("insert into suivi2_$id_grp values ($id_suivi2,$id_app,$seq,'A FAIRE',$duree_sequence,$ordre_seq,'$id_grp')");
                                                }
                                                elseif ($_POST['action'] == 'ajouter' && $modif == 1)
                                                {
                                                        $modif_sql = mysql_query("update prescription_$id_grp set presc_datedeb_dt=\"$date_deb\",presc_datefin_dt=\"$date_fin\",presc_formateur_no='$formateur' ".
                                                                            "where presc_utilisateur_no=$id_app and presc_seq_no=$seq and presc_grp_no=$id_grp and presc_parc_no=$num_parc");
                                                }
                                                elseif ($_POST['action'] == 'supprimer')
                                                {
                                                        $del_suivi2 = mysql_query("delete from suivi2_$id_grp where suiv2_utilisateur_no='$id_app' and suiv2_seq_no='$seq'");
                                                        $del_presc = mysql_query("delete from prescription_$id_grp where presc_seq_no='$seq' and presc_parc_no='$num_parc' and presc_utilisateur_no='$id_app'");
                                                        $seq_parc_exist = mysql_result(mysql_query("select count(*) from prescription_$id_grp where presc_utilisateur_no=$id_app and presc_parc_no=$num_parc"),0);
                                                        if ($seq_parc_exist == 0)
                                                                  $del_suivi3 = mysql_query("delete from suivi3_$id_grp where suiv3_utilisateur_no='$id_app' and suiv3_parc_no='$num_parc'");
                                                        $msgAction = "Suppression de la séquence : ".$titre_seq."\n $msq_parc : ".$titre_parc[$mm];
                                                }
                                                if (!empty($msgAction))
                                                {
                                                    $qualite = $msq_prescripteur;
                                                    $action_fiche = $mess_aff_forma;
                                                    $commentaire = $msgAction;
                                                    $new_fiche = Donne_ID($connect, "select max(fiche_cdn) from fiche_suivi");
                                                    $req_fiche = mysql_query("INSERT INTO fiche_suivi (fiche_cdn,fiche_utilisateur_no,fiche_auteur_no,fiche_qualite_lb,fiche_date_dt,fiche_heure_dt,fiche_commentaire_cmt,fiche_grp_no,fiche_parc_no,fiche_seq_no,fiche_act_no,fiche_typaction_lb) VALUES($new_fiche,$id_app,$id_user,'$qualite','$date_fiche','$heure_fiche',\"$commentaire\",$id_grp,$num_parc,$seq,0,\"$action_fiche\")");
                                                }
                                                $type_sequence = GetDataField($connect, "SELECT seq_type_lb from sequence where seq_cdn = '$seq'", "seq_type_lb");
                                                $scormOk = (strstr($type_sequence, "SCORM")) ? 1 : 0;
                                                if ($scormOk == 1)
                                                {
                                                        $mod_query = mysql_query("SELECT * from scorm_module where mod_seq_no = $seq order by mod_cdn");
                                                        $nb_mod = mysql_num_rows($mod_query);
                                                        $k = 0;
                                                        while ($k != $nb_mod)
                                                        {
                                                                $id_mod = mysql_result($mod_query, $k, "mod_cdn");
                                                                $id_suivi = Donne_ID($connect, "SELECT max(user_module_cdn) from scorm_util_module_$id_grp");
                                                                if ($_POST['action'] == 'ajouter')
                                                                {
                                                                    $mod_exist = mysql_result(mysql_query("select count(*) from scorm_util_module_$id_grp,scorm_module where user_module_no='$id_app' and mod_module_no='$id_mod' and mod_module_no=mod_cdn and mod_seq_no='$seq' and mod_parc_no='$num_parc'"),0);
                                                                    if ($mod_exist == 0 && (($presc_seq_exist > 0 && $_POST['repareAct'] == 'on') || $presc_seq_exist == 0))
                                                                             $ins_suivi = mysql_query("insert into scorm_util_module_$id_grp (user_module_cdn,user_module_no,mod_module_no,mod_grp_no) values ('$id_suivi','$id_app','$id_mod','$id_grp')");
                                                                }
                                                                elseif ($_POST['action'] == 'supprimer')
                                                                        $del_suivi = mysql_query("delete from scorm_util_module_$id_grp where user_module_no='$id_app' and mod_module_no='$id_mod'");
                                                                $k++;
                                                                $comptage++;
                                                        }
                                                }
                                                else
                                                {
                                                        //Selection des activites de la sequence
                                                        $act_query = mysql_query("SELECT * from activite where act_seq_no = '$seq' order by act_ordre_nb");
                                                        $nb_act = mysql_num_rows($act_query);
                                                        $k = 0;
                                                        while ($k != $nb_act)
                                                        {
                                                                $act = mysql_result($act_query, $k, "act_cdn");
                                                                $act_flag = mysql_result($act_query, $k, "act_flag_on");
                                                                $ress_on = mysql_result($act_query, $k, "act_ress_on");
                                                                $ress = mysql_result($act_query, $k, "act_ress_no");
                                                                $id_suivi = Donne_ID($connect, "SELECT max(suivi_cdn) from suivi1_$id_grp");
                                                                $act_exist = mysql_query("select * from suivi1_$id_grp,activite where suivi_utilisateur_no='$id_app' and suivi_act_no='$act' and act_cdn =suivi_act_no and act_seq_no = '$seq'");
                                                                $nbActExist = mysql_num_rows($act_exist);
                                                                if ($_POST['action'] == 'ajouter' && $nbActExist == 0 &&
                                                                   (($presc_seq_exist > 0 && $_POST['repareAct'] == 'on') || $presc_seq_exist == 0))
                                                                {
                                                                        if ($ress == 0 && $ress_on == 'OUI')
                                                                                $ins_suivi = mysql_query("insert into suivi1_$id_grp (suivi_cdn,suivi_utilisateur_no,suivi_act_no,suivi_grp_no,suivi_etat_lb) values ($id_suivi,'$id_app','$act','$id_grp','A FAIRE')");
                                                                        elseif ($ress == 0 && $ress_on == 'NON')
                                                                                $ins_suivi = mysql_query("insert into suivi1_$id_grp (suivi_cdn,suivi_utilisateur_no,suivi_act_no,suivi_grp_no,suivi_etat_lb) values ($id_suivi,'$id_app','$act','$id_grp','PRESENTIEL')");
                                                                        else
                                                                                $ins_suivi = mysql_query("insert into suivi1_$id_grp (suivi_cdn,suivi_utilisateur_no,suivi_act_no,suivi_grp_no,suivi_etat_lb) values ($id_suivi,'$id_app','$act','$id_grp','A FAIRE')");
                                                                }
                                                                if ($presc_seq_exist > 0 && isset($_POST['repareAct']) && $_POST['repareAct'] == 'on')
                                                                    $modif_SV2 = mysql_query("update suivi2_$id_grp set suiv2_etat_lb='EN COURS' where suiv2_utilisateur_no='$id_app' and suiv2_seq_no='$seq' and suiv2_etat_lb='TERMINE'");
                                                                elseif ($_POST['action'] == 'supprimer')
                                                                {
                                                                        if ($nbActExist > 0)
                                                                        {
                                                                            while ($item = mysql_fetch_object($act_exist))
                                                                            {
                                                                                   $del_suivi = mysql_query("delete from suivi1_$id_grp where suivi_utilisateur_no='$id_app' and suivi_act_no='".$item->act_cdn."'");
                                                                            }
                                                                        }
                                                                }
                                                                $k++;
                                                                $comptage++;
                                                        }
                                                }
                                        }
                                }
                        }
                }
        }
        if ($comptage > 0)
        {
                if (!empty ($mess_notif))
                        $mess_notif .= "&nbsp; En dehors de l'erreur liée aux dates<br /> ";
                $mess_notif .= "&nbsp; L'opération \" ".$_POST['action']."\" s'est bien déroulée ";
        }
} else
        $mess_notif = $isError."<br />&nbsp;&nbsp;<u>Veuillez renseigner tous les champs nécessaires</u><br />&nbsp; ";
// fin $ajout_app
?>