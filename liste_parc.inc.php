<?php
if (!isset($_SESSION['id_user']) || $_SESSION['id_user'] == "")
{
  exit();
}
require_once('ApiTweet/lib/config.php');

if ($lg == "fr")
   setlocale(LC_TIME,'fr_FR');
elseif($lg == "ru")
   setlocale(LC_TIME,'ru_RU');
GLOBAL $connect,$star,$proprio,$parcours,$medor,$id_ref,$aff,$liste_act_seq,
       $ordre_affiche,$id_seq,$miens,$rechercher,$id_ress,$act_seq_ress,$ressource;
    include ('include/head_list_parc.php');
    if ($requete_parc == "" && !isset($_SESSION['aut_ajx']))
        $requete_parc = "select * from parcours where parcours_cdn != 0 AND parcours_auteur_no = $id_user order by parcours_type_lb,parcours_nom_lb";
    elseif ($requete_parc == "" && isset($_SESSION['aut_ajx']))
        $requete_parc = "select * from parcours where parcours_cdn > 0 AND parcours_auteur_no = ".$_SESSION['aut_ajx']." order by parcours_type_lb,parcours_nom_lb";
    $_SESSION['requete_parc'] = $requete_parc;
    unset($_SESSION['aut_ajx']);
    $req_parc = mysql_query($requete_parc);
    $nb_req_parc = mysql_num_rows ($req_parc);
    $p_i=0;
    while ($p_i < $nb_req_parc)
    {
          $parc_droit = mysql_result ($req_parc,$p_i,"parcours_cdn");
          $nom_parcours = mysql_result ($req_parc,$p_i,"parcours_nom_lb");
          $parc_type = mysql_result ($req_parc,$p_i,"parcours_type_on");
          $mod_parc = mysql_result ($req_parc,$p_i,"parcours_type_lb");
          $createur = mysql_result ($req_parc,$p_i,"parcours_auteur_no");
          $nom_createur = GetDataField ($connect,"select util_nom_lb from utilisateur where util_cdn = $createur","util_nom_lb");
          $prenom_createur = GetDataField ($connect,"select util_prenom_lb from utilisateur where util_cdn = $createur","util_prenom_lb");
          $majuscule = "$prenom_createur $nom_createur";
          $date_creat = mysql_result ($req_parc,$p_i,"parcours_create_dt");
          $ch_dtc = explode("-",$date_creat);
          $dtc = strftime("%A %e %B %Y", mktime(0, 0, 0, $ch_dtc[1],$ch_dtc[2],$ch_dtc[0]));
          $date_modif = mysql_result ($req_parc,$p_i,"parcours_modif_dt");
          $ch_dtm = explode("-",$date_modif);
          $dtm = strftime("%A %e %B %Y", mktime(0, 0, 0, $ch_dtm[1],$ch_dtm[2],$ch_dtm[0]));
          $droit_voir = mysql_result ($req_parc,$p_i,"parcours_publique_on");
          $droit_presc = mysql_result ($req_parc,$p_i,"parcours_type_on");
          $id_ref_parc = mysql_result ($req_parc,$p_i,"parcours_referentiel_no");
          $lien = "parcours.php?liste=$liste&consult=1&parcours=$parcours&proprio=$proprio&medor=$medor&parc=$parc_droit&id_parc=$parc_droit&id_ref_parc=$id_ref_parc&id_ref=$id_ref&miens_parc=$miens_parc&refer=$refer&liste_seq=1";
          $lien = urlencode($lien);
          if (!empty($aff))
             unset($aff);
          $icono = "";
          if ($parc_type == 1 && $droit_voir == 1)
          {
            $div="DIV id='sequence'";
            $icono = "<IMG SRC='images/modules/icotype.gif' border='0'>";
          }
          else
          {
            if ($parc == $parc_droit)
               $div="DIV id='seqinv' style='text-align:left;'";
            else
               $div="DIV id='sequence' style='text-align:left;'";
            $icono = "";
          }
          $affSeq = (isset($id_parc) && ($parc == $parc_droit || $parc_droit == $id_parc)) ? 1 : 0;
          $SQL = "select * from parcours_forums where parcforum_parc_no = $parc_droit";
          $Nbfrm = mysql_num_rows(mysql_query($SQL));
          $SQL = "select * from forums_modules where fm_module_no = $parc_droit";
          $NbPosts = mysql_num_rows(mysql_query($SQL));
          if ($createur == $id_user)
          {
            $aff = "<TR><TD width='5%' nowrap style='text-align:left;'>";
            if ($droit_voir == 0)
               $icono .= "<IMG SRC=\"images/modules/tut_form/icocadenas.gif\" border='0'>&nbsp;";
            elseif ($parc_type == 1 && $droit_voir == 1)
               $icono .= "";
            elseif ($affSeq == 0)
               $icono .= "&nbsp;&nbsp;&nbsp;&nbsp;";
            elseif ($affSeq == 1)
               $icono .= "";
            $icono .= "&nbsp;<IMG SRC=\"images/gest_parc/icofeuil.gif\" border='0'>";
          }
          else
          {
            $aff = "<TR><TD width='5%' nowrap style='text-align:left;'>";
            if ($droit_voir == 0)
               $icono .= "<IMG SRC=\"images/modules/tut_form/icocadenas.gif\" border='0'>&nbsp;&nbsp;&nbsp;&nbsp;";
            elseif ($parc_type == 1 && $droit_voir == 1)
               $icono .= "";
            elseif ($affSeq == 0)
               $icono .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
            elseif ($affSeq == 1)
               $icono .= "";
          }
          if ($Nbfrm > 0 )
             $icono .= "<A href=\"forum_module.php?id_parc=$parc_droit\" ".
                        bulle($msg_IsModFrm .": Cliquez pour ".strtolower($mess_menu_consult_favori).
                        " les $NbPosts $mess_mess_mess2","","RIGHT","ABOVE",170).
                        "&nbsp;<IMG SRC=\"images/modules/frmod.gif\" border='0'></a>";
          elseif ($affSeq == 0)
               $icono .= "&nbsp;&nbsp;&nbsp;";
          elseif ($affSeq == 1)
               $icono .= "";
          if (!isset($id_parc) || (isset($id_parc) && ($parc == $parc_droit || $parc_droit == $id_parc)))
          {
             if ($icono == "")
                 $aff .= "-";
             $aff .= "$icono</TD>";
             if ($affSeq == 1)
                $nom_parcours = "<span style='font-size:14px;border:1px solid #D45211; padding:2px;'>$nom_parcours</span>";

             $affichage = "&nbsp;<B>$msq_parc</B><BR>&nbsp;&nbsp;- $mrc_aut : <B>$majuscule</B><BR>&nbsp;&nbsp;- $mess_menu_gest_seq_ref : &nbsp;&nbsp; <B>$dtc</B><BR>&nbsp;&nbsp;- $mess_modif_dt :&nbsp;&nbsp; <B>$dtm</B>";
             echo $aff."<TD width='95%' style='text-align:left;'><$div><A href=\"trace.php?link=$lien\" ".bulle($affichage,"","RIGHT","ABOVE",280)."$nom_parcours</DIV></TD></TR>";
          }
          
          $nb_seq_parc_req = mysql_query ("SELECT seqparc_cdn from sequence_parcours where seqparc_parc_no = $parc_droit");
          $nb_seq_liste = mysql_num_rows($nb_seq_parc_req);
          if ($nb_seq_liste > 0 && ($parc == $parc_droit || $parc_droit == $id_parc))
          {
             echo "<TR><TD width='100%' valign='top' colspan='2' style='text-align:left;'>".
                  "<TABLE border='0' cellpadding='2' cellspacing='2' width='100%'>";
             $id_seq_query = mysql_query ("SELECT seqparc_seq_no from sequence_parcours where seqparc_parc_no = $parc_droit order by seqparc_ordre_no");
             $nb_seq2 = mysql_num_rows ($id_seq_query);
             $parc_duree = 0;
             $s_i = 0;
             while ($s_i < $nb_seq2)
             {
                 $sequence = mysql_result ($id_seq_query,$s_i,"seqparc_seq_no");
                 $seq_auteur = GetDataField ($connect,"select seq_auteur_no from sequence where seq_cdn = $sequence","seq_auteur_no");
                 $nom_createur_seq = GetDataField ($connect,"select util_nom_lb from utilisateur where util_cdn = $seq_auteur","util_nom_lb");
                 $prenom_createur_seq = GetDataField ($connect,"select util_prenom_lb from utilisateur where util_cdn = $seq_auteur","util_prenom_lb");
                 $majuscule_seq = "$prenom_createur_seq $nom_createur_seq";
                 $date_creat_seq = GetDataField ($connect,"select seq_create_dt from sequence where seq_cdn = $sequence","seq_create_dt");
                 $ch_dtc_seq = explode("-",$date_creat_seq);
                 $dtc_seq = strftime("%A %e %B %Y", mktime(0, 0, 0, $ch_dtc_seq[1],$ch_dtc_seq[2],$ch_dtc_seq[0]));
                 $date_modif_seq = GetDataField ($connect,"select seq_modif_dt from sequence where seq_cdn = $sequence","seq_modif_dt");
                 $ch_dtm_seq = explode("-",$date_modif_seq);
                 $dtm_seq = strftime("%A %e %B %Y", mktime(0, 0, 0, $ch_dtm_seq[1],$ch_dtm_seq[2],$ch_dtm_seq[0]));
                 $seq_titre = GetDataField ($connect,"select seq_titre_lb from sequence where seq_cdn = $sequence","seq_titre_lb");
                 $droit_voir_seq = GetDataField ($connect,"select seq_publique_on from sequence where seq_cdn = $sequence","seq_publique_on");
                 $seq_type = GetDataField ($connect,"select seq_type_on from sequence where seq_cdn = $sequence","seq_type_on");
                 $mod_seq = GetDataField ($connect,"select seq_type_lb from sequence where seq_cdn = $sequence","seq_type_lb");
                 $seq_duree = GetDataField ($connect,"select seq_duree_nb from sequence where seq_cdn = $sequence","seq_duree_nb");
                 $seq_ref_query = mysql_query ("select seqref_referentiel_no from sequence_referentiel where seqref_seq_no = $sequence");
                 $nb_seq_ref = mysql_num_rows ($seq_ref_query);
                 $icono = "";
                 if ($nb_seq_ref == 1)
                    $id_ref = GetDataField ($connect,"select seqref_referentiel_no from sequence_referentiel where seqref_seq_no = $sequence","seqref_referentiel_no");
                 if ($id_ref == 0)
                    $nom_referentiel = $msq_sl;
                 else
                    $nom_referentiel = GetDataField ($connect,"select ref_nomabrege_lb from referentiel where ref_cdn = $id_ref","ref_nomabrege_lb");
          
                 $parc_duree .= $seq_duree;
                 if ($seq_type == 1 && $droit_voir_seq == 1)
                 {
                    $div="DIV id='sequence' style='text-align:left;'";
                    $icono = "<IMG SRC='images/modules/icotype.gif' border='0' ".
                     bulle("$mess_visdup<br />$mess_notpresc","","RIGHT","ABOVE",120);
                 }
                 else
                 {
                    if ($id_seq == $sequence && $rechercher != 1 && $medor !=1)
                       $div="DIV id='seqinv' style='text-align:left;'";
                    else
                       $div="DIV id='sequence' style='text-align:left;'";
                    $icono = "";
                 }
                 if (strstr($mod_seq,"SCORM"))
                    $icono .= "<IMG SRC='images/gest_parc/scorm.gif' border='0' ".
                     bulle($mess_seq_sco,"","RIGHT","ABOVE",120);
                 if ($seq_auteur == $id_user || $typ_user == 'ADMINISTRATEUR')
                 {
                    echo "<TR><TD width='15%' align='right' valign='top' nowrap>";
                    if ($droit_voir_seq == 0)
                       $icono .= "<IMG SRC=\"images/modules/tut_form/icocadenas.gif\" border='0' ".
                       bulle($mess_notdupli,"","RIGHT","ABOVE",100);
                    elseif ($icono == "")
                       $icono .= "&nbsp;&nbsp;";
                    if ($seq_auteur == $id_user)
                       $icono .= "&nbsp;<IMG SRC=\"images/gest_parc/icofeuil.gif\" border='0'>";
                    else
                       $icono .= "&nbsp;";
                 }
                 else
                 {
                    echo "<TR><TD width='15%' valign='top' align='right' nowrap>";
                    if ($droit_voir_seq == 0)
                      $icono .= "<IMG SRC=\"images/modules/tut_form/icocadenas.gif\" border='0'".
                       bulle($mess_notdupli,"","RIGHT","ABOVE",100)."&nbsp;";
                 }
                 if ($icono == "")
                    $icono = "-";
                 $affichage_seq = "&nbsp;<B>$msq_seq</B><BR>&nbsp;&nbsp;- $mrc_aut : <B>".
                                  addslashes($majuscule_seq)."</B><BR>&nbsp;&nbsp;- $mess_menu_gest_seq_ref : ".
                                  "&nbsp;&nbsp; <B>$dtc_seq</B><BR>&nbsp;&nbsp;- $mess_modif_dt :".
                                  "&nbsp;&nbsp; <B>$dtm_seq</B>";
                 if (!strstr($mod_seq,"SCORM"))
                    $nb_act_sequence_query = mysql_query ("select * from activite where act_seq_no = $sequence order by act_ordre_nb");
                 else
                    $nb_act_sequence_query = mysql_query ("select * from scorm_module where mod_seq_no = $sequence order by mod_cdn");
                 $nb_act_sequence = mysql_num_rows ($nb_act_sequence_query);
                 $affAct = ($sequence == $id_seq && $rechercher != 1 && $medor !=1 && $nb_act_sequence > 0) ? 1 : 0;
                 if ($affAct == 1)
                     $seq_titre = "<span style='font-size:13px;border:1px solid #D45211; padding:1px;'>$seq_titre</span>";
                 $combi = ($droit_voir_seq == 0 && $seq_type == 1) ? 0 : 1;
                 if ($combi == 1 || $seq_auteur == $id_user || $typ_user == 'ADMINISTRATEUR')
                 {
                    if (!isset($liste_act_seq))
                    {
                       $lien="sequence.php?toutes_seq=0&liste=1&id_seq=$sequence&consult=1&parcours=1&id_parc=$id_parc&proprio=$proprio&refer=$refer&id_ref=$id_ref&id_ref_parc=$id_ref_parc&miens=$miens&miens_parc=$miens_parc&refer=$refer&parc=$id_parc&gauche=1&liste_act_seq=1";
                       $lien = urlencode($lien);
                       echo "$icono</TD><TD valign='top'><$div><A HREF=\"trace.php?link=$lien\" target='main' title=\"$msq_seq_det\" ".
                            bulle($affichage_seq,"","RIGHT","ABOVE",280)."$seq_titre</A></DIV>";
                    }
                    else
                    {
                       $lien="sequence.php?toutes_seq=0&liste=1&id_seq=$sequence&consult_act=1&parcours=1&id_parc=$id_parc&proprio=$proprio&refer=$refer&id_ref=$id_ref&id_ref_parc=$id_ref_parc&miens=$miens&miens_parc=$miens_parc&refer=$refer&parc=$id_parc&gauche=1";
                       $lien = urlencode($lien);
                       echo "$icono</TD><TD valign='top'><$div><A HREF=\"trace.php?link=$lien\" target='main' title=\"$msq_list_act\"".
                            bulle($affichage_seq,"","RIGHT","ABOVE",220)."$seq_titre</A></DIV>";
                    }
                 }
                 else
                    echo "$icono</TD><TD valign='top'><$div><A HREF=\"javascript:void(0);\" target='main'".
                         bulle($affichage_seq,"","RIGHT","ABOVE",220)."$seq_titre</A></DIV>";
                 if ($nb_act_sequence > 0)
                 {
                    require_once("class/class_module.php");
                    $reactive = add_seq_user_after($sequence,$parc_droit);
                    if ($sequence == $id_seq && $rechercher != 1 && $medor !=1)
                    {
                       echo "<TABLE width=100% cellpadding=0 cellspacing=2 border='0'>";
                       $act_i = 0;
                       while ($act_i < $nb_act_sequence)
                       {
                       if (strstr($mod_seq,"SCORM"))
                       {
                         $act_seq_num = mysql_result ($nb_act_sequence_query,$act_i,"mod_cdn");
                         $act_auteur =  $seq_auteur;
                         $nom_createur_act = GetDataField ($connect,"select util_nom_lb from utilisateur where util_cdn = $act_auteur","util_nom_lb");
                         $prenom_createur_act = GetDataField ($connect,"select util_prenom_lb from utilisateur where util_cdn = $act_auteur","util_prenom_lb");
                         $majuscule_act = "$prenom_createur_act $nom_createur_act";
                         $dtm_act = $dtm_seq;
                         $dtc_act = $dtc_seq;
                         $act_seq_titre = mysql_result ($nb_act_sequence_query,$act_i,"mod_titre_lb");
                         $act_seq_dv = $droit_voir_seq;
                       }
                       else
                       {
                         $act_seq_num = mysql_result ($nb_act_sequence_query,$act_i,"act_cdn");
                         $act_auteur =  mysql_result ($nb_act_sequence_query,$act_i,"act_auteur_no");
                         $nom_createur_act = GetDataField ($connect,"select util_nom_lb from utilisateur where util_cdn = $act_auteur","util_nom_lb");
                         $prenom_createur_act = GetDataField ($connect,"select util_prenom_lb from utilisateur where util_cdn = $act_auteur","util_prenom_lb");
                         $majuscule_act = "$prenom_createur_act $nom_createur_act";
                         $date_creat_act = mysql_result ($nb_act_sequence_query,$act_i,"act_create_dt");
                         $ch_dtc_act = explode("-",$date_creat_act);
                         $dtc_act = strftime("%A %e %B %Y", mktime(0, 0, 0, $ch_dtc_act[1],$ch_dtc_act[2],$ch_dtc_act[0]));
                         $date_modif_act = mysql_result ($nb_act_sequence_query,$act_i,"act_modif_dt");
                         $ch_dtm_act = explode("-",$date_modif_act);
                         $dtm_act = strftime("%A %e %B %Y", mktime(0, 0, 0, $ch_dtm_act[1],$ch_dtm_act[2],$ch_dtm_act[0]));
                         $act_seq_titre = mysql_result ($nb_act_sequence_query,$act_i,"act_nom_lb");
                         $act_seq_dv = mysql_result ($nb_act_sequence_query,$act_i,"act_publique_on");
                         $act_seq_ress = mysql_result ($nb_act_sequence_query,$act_i,"act_ress_no");
                         if ($id_ress == 0)
                            $ressource = $msq_aucune;
                         else
                            $ressource = GetDataField ($connect,"select ress_titre from ressource_new where ress_cdn = $act_seq_ress","ress_titre");
                       }
                       if ($act_auteur == $id_user)
                       {
                            echo "<TR><TD width='8%' align='right' valign='top' nowrap>";
                            if ($act_seq_dv == 0)
                               echo "&nbsp;<IMG SRC=\"images/modules/tut_form/icocadenas.gif\" border='0' ".
                               bulle($mess_notdupli,"","RIGHT","ABOVE",100);
                            if ($act_auteur == $id_user)
                               echo "&nbsp;<IMG SRC=\"images/gest_parc/icofeuilb.gif\" border='0'>";
                            else
                               echo "&nbsp;";
                       }
                       else
                       {
                            echo "<TR><TD width='8%' valign='top' align='right' nowrap>";
                            echo "-";
                            if ($act_seq_dv == 0)
                               echo "<IMG SRC=\"images/modules/tut_form/icocadenas.gif\" border='0' ".
                               bulle($mess_notdupli,"","RIGHT","ABOVE",100)."&nbsp;";
                       }
                       echo xApiShow($act_seq_num);
                       echo "</TD>";
                         $affichage_act = "&nbsp<B>$msq_activite</B><BR>&nbsp;&nbsp;- $mrc_aut : <B>$majuscule_act</B><BR>&nbsp;&nbsp;- $mess_menu_gest_seq_ref : &nbsp;&nbsp; <B>$dtc_act</B><BR>&nbsp;&nbsp;- $mess_modif_dt :&nbsp;&nbsp; <B>$dtm_act</B>";
                         $aff_act_aut = bulle($affichage_act,"","RIGHT","ABOVE",280);
                         $lien = "sequence.php?liste=$liste&id_seq=$sequence&action_act=1&modif_act=1&id_act=$act_seq_num&id_ress=$act_seq_ress&ressource=$ressource&parcours=$parcours&liste=$liste&id_parc=$id_parc&proprio=$proprio&refer=$refer&id_ref=$id_ref&miens=$miens&miens_parc=$miens_parc&refer=$refer&parc=$id_parc&id_ref_parc=$id_ref_parc&gauche=1&liste_act_seq=1";
                         $lien = urlencode($lien);
                         if (!isset($id_act) || (isset($id_act) && $id_act != $act_seq_num))
                         {
                           if (($act_auteur == $id_user || $typ_user == 'ADMINISTRATEUR') && !strstr($mod_seq,"SCORM"))
                              echo "<TD valign='top'><DIV id='sequence'><A HREF=\"trace.php?link=$lien\" target='main' $aff_act_aut $act_seq_titre</A></DIV></TD></TR>";
                           elseif(($act_auteur == $id_user || $typ_user == 'ADMINISTRATEUR') && strstr($mod_seq,"SCORM"))
                           {
                              $lien="modif_sco.php?liste=$liste&id_seq=$sequence&modif_act=1&id_act=$act_seq_num&parcours=$parcours&liste=$liste&id_parc=$id_parc&proprio=$proprio&refer=$refer&id_ref=$id_ref&miens=$miens&miens_parc=$miens_parc&refer=$refer&parc=$id_parc&id_ref_parc=$id_ref_parc&gauche=1&liste_act_seq=1";
                              $lien = urlencode($lien);
                              echo "<TD valign='top'><DIV id='sequence'><A HREF=\"trace.php?link=$lien\" target='main'>$act_seq_titre</A></DIV></TD></TR>";
                           }
                           elseif($act_auteur != $id_user && !strstr($mod_seq,"SCORM"))
                              echo "<TD valign='top'><DIV id='sequence'><A HREF=\"javascript:void(0);\" target='main' $aff_act_aut $act_seq_titre</A></DIV></TD></TR>";
                           elseif($act_auteur != $id_user && strstr($mod_seq,"SCORM"))
                              echo "<TD valign='top'><DIV id='sequence'><A HREF=\"javascript:void(0);\" target='main'>$act_seq_titre</A></DIV></TD></TR>";
                         }
                         else
                           echo "<TD valign='top'><DIV id='seqinv'><A HREF=\"javascript:void(0);\" target='main' $aff_act_aut $act_seq_titre</A></DIV></TD></TR>";
                        $act_i++;
                        }

                       if ($seq_auteur == $id_user || $typ_user == 'ADMINISTRATEUR')
                       {
                           if (!strstr($mod_seq,"SCORM"))
                           {
                                $lienTinCan = "parseurTinCan.php?liste=$liste&id_seq=$sequence&parcours=$parcours&id_parc=$id_parc&proprio=$proprio&refer=$refer&id_ref=$id_ref&miens=$miens&miens_parc=$miens_parc&parc=$id_parc&id_ref_parc=$id_ref_parc&liste_act_seq=1&prov=parc";
                                $lien = urlencode($lienTinCan);
                                echo "<TR><TD align='right'><IMG SRC='images/gest_parc/xApi.gif' border='0'>&nbsp;</TD>";
                                echo "<TD valign='top'><DIV id='sequence'><A HREF=\"trace.php?link=$lien\">Importer une activité TinCan (xApi)</A></DIV></TD></TR>";
                                $lien="sequence.php?liste=$liste&choix=1&id_seq=$sequence&parcours=$parcours&id_parc=$id_parc&proprio=$proprio&refer=$refer&action_seq=1&id_ref=$id_ref&miens=$miens&miens_parc=$miens_parc&parc=$id_parc&id_ref_parc=$id_ref_parc";
                                $lien = urlencode($lien);
                                echo "<TR><TD valign='top' align='right'><IMG SRC='images/modules/tut_form/icoactivite.gif' border='0'></font></TD>";
                                echo "<TD valign='top'><DIV id='sequence'><A HREF=\"trace.php?link=$lien\" target='main'>$mess_ag_choix</A></DIV></TD></TR>";
                           }
                           $lien= "wiki/wikiAjout.php?id_seq=$sequence&keepThis=true&TB_iframe=true&height=400&width=700";
                           echo "<TR><TD valign='top' align='right'><IMG SRC='wiki/images/wikidoc.gif' border='0'></TD><TD>";
                           echo "<DIV id='sequence'><a href=\"$lien\" class='thickbox' title='Ajouter ou modifier un travail en commun' ";
                           echo "name='Ajouter ou modifier un travail en commun' ".
                           bulle("Ajouter ou modifier un travail à faire en commun(WikiDoc): Donnez juste les consignes. ".
                               "Les affectations en binôme, trinôme ou groupe se font sur la page de suivi de l'apprenant" ,"","RIGHT","ABOVE",270).
                           "Gérer des WikiDocs</A></DIV></TD></TR>";
                           /* MindMap*/
                           $lien= "mindmap/mindAjout.php?id_seq=$sequence&keepThis=true&TB_iframe=true&height=350&width=550";
                           echo "<TR><TD valign='top' align='right'><IMG SRC='mindmap/pix/icon.png' border='0'></TD><TD>";
                           echo "<DIV id='sequence'><a href=\"$lien\" class='thickbox' title='Ajouter ou modifier une instance de carte heuristique' ";
                           echo "name='Ajouter ou supprimer une carte heuristique' ".
                           bulle("Ajouter ou supprimer une carte heuristique(MindMap): Donnez juste son titre. ".
                               "Les affectations en binôme, trinôme ou groupe se font sur la page de suivi de l'apprenant" ,"","RIGHT","ABOVE",270).
                           "Gérer des MindMaps</A></DIV></TD></TR>";
                         $nbrTwit = mysql_num_rows(mysql_query("select * from seq_twitter where seqtwit_seq_no='$sequence'"));
                         if ($TweetOk == 1)
                         {
                           if ($nbrTwit == 0)
                           {

                            $lien= "ApiTweet/LibSeq/SeqTwit.php?id_seq=$sequence&activer=1";
                            echo "<TR><TD valign='top' align='right'><IMG SRC='ApiTweet/assets/tweeter.gif' border='0'></TD><TD>";
                            echo "<DIV id='seqtwt'><a href=\"javascript:void(0);\" onClick=\"javascript:jQ_AjaxMsg('$lien');\" ".
                                 "title='Activer un échange sur Twitter pour cette séquence' ".
                            bulle("Cliquer sur ce lien permettra après validation dans une formation de pouvoir lancer une activité ".
                                  "d'échanges sur Twitter via Formagri ou tout autre support de type Web, Smartphone ou encore Tablette" ,"","RIGHT","ABOVE",270).
                           "Créer un échange twitter</A></DIV></TD></TR>";
                          }
                          else
                          {
                            echo "<TR><TD valign='top' align='right'><IMG SRC='ApiTweet/assets/tweeter.gif' border='0'></TD><TD>";
                             echo "<div id='seqtwt'><span style='font-weight:bold;' title='Echange sur Twitter activé pour cette séquence' ".
                             bulle("Twitter via Formagri a été activé pour cette séquence afin de lui permettre de lancer une activité ".
                                  " d'échanges sur Twitter via Formagri ou tout autre support de type Web, Smartphone ou encore Tablette" ,"","RIGHT","ABOVE",270).
                                  "Echange twitter activé</span></div></TD></TR>";
                          }
                         }//fin if ($TweetOk == 1)
                       }
                       else
                       {
                           $nbrWk = mysql_num_rows(mysql_query("select * from wiki where wiki_seq_no='$sequence'"));
                           if ($nbrWk > 0)
                           {
                             $lien= "wiki/wikiAjout.php?id_seq=$sequence&keepThis=true&TB_iframe=true&height=400&width=700";
                             echo "<TR><TD valign='top' align='right'><IMG SRC='wiki/images/wikidoc.gif' border='0'></TD><TD>";
                             echo "<DIV id='sequence'>";
                             echo "<a href=\"$lien\" class='thickbox' title='Voir les travaux en commun' ";
                             echo "name='Voir les travaux en commun' ".
                                   bulle("Consulter les thèmes des travaux en commun de cette séquence" ,"","RIGHT","ABOVE",270).
                                   "Consulter les WikiDocs</A></DIV>";
                           }
                           /* MindMap*/
                           $ReqMind = mysql_num_rows(mysql_query("select * from mindmap where mindmap_seq_no = ".$sequence));
                           if($ReqMind > 0)
                           {
                              $lien= "mindmap/mindAjout.php?id_seq=$sequence&keepThis=true&TB_iframe=true&height=350&width=550";
                              echo "</TD></TR><TR><TD valign='top' align='right'><IMG SRC='mindmap/pix/icon.png' border='0'></TD><TD>";
                              echo "<DIV id='sequence'><a href=\"$lien\" class='thickbox' title='Consulter les cartes heuristiques' ";
                              echo "name='Voir les cartes heuristiques liées à cette séquence' ".
                              bulle("Consulter les cartes heuristiques(MindMap)" ,"","RIGHT","ABOVE",270).
                              "Consulter MindMaps</A></DIV>";
                           }

                           $nbrTwit = mysql_num_rows(mysql_query("select * from seq_twitter where seqtwit_seq_no='$sequence'"));
                           if ($nbrTwit > 0 && $TweetOk == 1)
                           {
                              echo "</TD></TR><TR><TD valign='top' align='right'><IMG SRC='ApiTweet/assets/tweeter.gif' border='0'></TD><TD>";
                              echo "<div id='seqtwt'><span style='font-weight:bold;' title='Echange sur Twitter activé pour cette séquence' ".
                              bulle("Twitter via Formagri a été activé pour cette séquence afin de lui permettre de lancer une activité ".
                                    " d'échanges sur Twitter via Formagri ou tout autre support de type Web, Smartphone ou encore Tablette" ,"","RIGHT","ABOVE",270).
                                    "Echange twitter activé</span></div>";
                           }
                       }
                       echo "</TD></TR></TABLE></TD>";
                    }
                    else
                       echo "</TD>";
                   }
                   else
                   {
                       if ($seq_auteur == $id_user || $typ_user == 'ADMINISTRATEUR')
                       {
                           if (!strstr($mod_seq,"SCORM"))
                           {
                                $lien="sequence.php?liste=$liste&id_seq=$sequence&parcours=$parcours&id_parc=$id_parc&proprio=$proprio&refer=$refer&action_seq=1&choix=1&id_ref=$id_ref&miens=$miens&miens_parc=$miens_parc&parc=$id_parc&id_ref_parc=$id_ref_parc&liste_act_seq=1";
                                $lien = urlencode($lien);
                                echo "<TR><TD>&nbsp;</TD><TD><TABLE width=100% cellpadding=0 cellspacing=2 border='0'>";
                                echo "<TR><TD valign='top' align='right'><IMG SRC='images/modules/tut_form/icoactivite.gif' border='0'></TD>";
                                echo "<TD valign='top'><DIV id='sequence'><A HREF=\"trace.php?link=$lien\" target='main'>$mess_ag_choix</A></DIV>";
                                echo "</TD></TR>";
                                $lienTinCan = "parseurTinCan.php?liste=$liste&id_seq=$sequence&parcours=$parcours&id_parc=$id_parc&proprio=$proprio&refer=$refer&id_ref=$id_ref&miens=$miens&miens_parc=$miens_parc&parc=$id_parc&id_ref_parc=$id_ref_parc&liste_act_seq=1&prov=parc";
                                $lien = urlencode($lienTinCan);
                                echo "<TR><TD align='left' colspan=2><DIV id='seqinv'>&nbsp;&nbsp;<IMG SRC='images/gest_parc/xApi.gif' border='0'>&nbsp;";
                                echo "&nbsp;<A HREF=\"trace.php?link=$lien\">Importer une activité TinCan (xApi)</A></DIV></TD></TR>";
                           }
                           else
                           {
                                echo "<TR><TD>&nbsp;</TD><TD><TABLE width=100% cellpadding=0 cellspacing=2 border='0'>";
                           }
                           $lien= "wiki/wikiAjout.php?id_seq=$sequence&keepThis=true&TB_iframe=true&height=550&width=700";
                           echo "<TR><TD valign='top' align='right'><IMG SRC='wiki/images/wikidoc.gif' border='0'></TD>";
                           echo "<TD valign='top'><DIV id='sequence'>";
                           echo "<a href=\"$lien\" class='thickbox' title='Ajouter ou modifier un travail en commun' ";
                           echo "name='Ajouter ou modifier un travail en commun' ".
                           bulle("Ajouter ou modifier un travail à faire en commun (WikiDoc): Donnez juste les consignes. ".
                               "Les affectations en binôme, trinôme ou groupe se font sur la page de suivi de l'apprenant" ,"","RIGHT","ABOVE",270).
                           "Gérer des WikiDocs</A></DIV></TD></TR>";
                           /* MindMap*/
                           $ReqMind = mysql_num_rows(mysql_query("select * from mindmap where mindmap_seq_no = ".$sequence));
                           if($ReqMind > 0)
                           {
                               $lien= "mindmap/mindAjout.php?id_seq=$sequence&keepThis=true&TB_iframe=true&height=350&width=550";
                               echo "<TR><TD valign='top' align='right'><IMG SRC='mindmap/pix/icon.png' border='0'></TD><TD>";
                               echo "<DIV id='sequence'><a href=\"$lien\" class='thickbox' title='Consulter les cartes heuristiques' ";
                               echo "name='Voir les cartes heuristiques liées à cette séquence' ".
                               bulle("Consulter les cartes heuristiques(MindMap)" ,"","RIGHT","ABOVE",270).
                               "Consulter MindMaps</A></DIV></TD></TR>";
                           }
                           $nbrTwit = mysql_num_rows(mysql_query("select * from seq_twitter where seqtwit_seq_no='$sequence'"));
                           if ($TweetOk == 1)
                           {
                              if ($nbrTwit == 0)
                              {
                                  $lien= "ApiTweet/LibSeq/SeqTwit.php?id_seq=$sequence&activer=1";
                                  echo "<TR><TD valign='top' align='right'><IMG SRC='ApiTweet/assets/tweeter.gif' border='0'></TD><TD>";
                                  echo "<DIV id='seqtwt'><a href=\"javascript:void(0);\" onClick=\"javascript:jQ_AjaxMsg('$lien');\" ".
                                       "title='Activer un échange sur Twitter pour cette séquence' ".
                                  bulle("Cliquer sur ce lien permettra après validation dans une formation de pouvoir lancer une activité ".
                                        "d'échanges sur Twitter via Formagri ou tout autre support de type Web, Smartphone ou encore Tablette" ,"","RIGHT","ABOVE",270).
                                        "Créer un échange twitter</A></DIV></TD></TR>";
                              }
                              else
                              {
                                 echo "<TR><TD valign='top' align='right'><IMG SRC='ApiTweet/assets/tweeter.gif' border='0'></TD><TD>";
                                 echo "<div style='font-weight:bold;' title='Echange sur Twitter activé pour cette séquence' ".
                                  bulle("Twitter via Formagri a été activé pour cette séquence afin de lui permettre de lancer une activité ".
                                  " d'échanges sur Twitter via Formagri ou tout autre support de type Web, Smartphone ou encore Tablette" ,"","RIGHT","ABOVE",270).
                                "Echange twitter activé</div></TD></TR>";
                              }
                           }//fin if ($TweetOk == 1)
                           echo "</TABLE></TD>";
                      }
                      else
                      {
                           echo "<TR><TD>&nbsp;</TD><TD><TABLE width=100% cellpadding=0 cellspacing=2 border='0'>";
                           $nbrWk = mysql_num_rows(mysql_query("select * from wiki where wiki_seq_no='$sequence'"));
                           if ($nbrWk > 0)
                           {
                               $lien= "wiki/wikiAjout.php?id_seq=$sequence&keepThis=true&TB_iframe=true&height=550&width=700";
                               echo "<TR><TD>&nbsp;</TD><TD><TABLE width=100% cellpadding=0 cellspacing=2 border='0'>";
                               echo "<TR><TD valign='top' align='right'><IMG SRC='wiki/images/wikidoc.gif' border='0'></TD>";
                               echo "<td valign='top'><DIV id='sequence'>";
                               echo "<a href=\"$lien\" class='thickbox' title='Voir les travaux en commun' ";
                               echo "name='Voir les travaux en commun' ".
                                    bulle("Consulter les thèmes des travaux en commun de cette séquence" ,"","RIGHT","ABOVE",270).
                                    "Consulter les WikiDocs</A></DIV></TD></TR>";
                           }
                           /* MindMap*/
                           $ReqMind = mysql_num_rows(mysql_query("select * from mindmap where mindmap_seq_no = ".$sequence));
                           if($ReqMind > 0)
                           {
                               echo "<TR><TD valign='top' align='right'><IMG SRC='mindmap/pix/icon.png' border='0'></TD><TD>";
                               echo "<DIV id='sequence'><a href=\"$lien\" class='thickbox' title='Consulter les cartes heuristiques' ";
                               echo "name='Voir les cartes heuristiques liées à cette séquence' ".
                               bulle("Consulter les cartes heuristiques(MindMap)" ,"","RIGHT","ABOVE",270).
                               "Consulter MindMaps</A></DIV></TD></TR>";
                           }
                         $nbrTwit = mysql_num_rows(mysql_query("select * from seq_twitter where seqtwit_seq_no='$sequence'"));
                         if ($nbrTwit > 0 && $TweetOk == 1)
                         {
                             echo "<TR><TD>&nbsp;</TD><TD><TABLE width=100% cellpadding=0 cellspacing=2 border='0'>";
                             echo "<TR><TD valign='top' align='right'><IMG SRC='ApiTweet/assets/tweeter.gif' border='0'></TD><TD>";
                             echo "<div id='seqtwt'><span style='font-weight:bold;' title='Echange sur Twitter activé pour cette séquence' ".
                                  bulle("Twitter via Formagri a été activé pour cette séquence afin de lui permettre de lancer une activité ".
                                  " d'échanges sur Twitter via Formagri ou tout autre support de type Web, Smartphone ou encore Tablette" ,"","RIGHT","ABOVE",270).
                                  "Echange twitter activé</span></div></TD></TR>";
                          }
                         echo "</TD>";
                      }
                   }
                   echo "</TR>";
                 $s_i++;
             }
             if ($parc == $parc_droit && ($createur == $id_user || $typ_user == 'ADMINISTRATEUR') && !strstr($mod_parc,"SCORM")){
               $lien= "parcours.php?liste=$liste&action_parc=1&parcours=$parcours&id_ref=$id_ref_parc&droit_voir=$droit_voir&nb_seq=$nb_seq2&duree_parc=$parc_duree&id_ref=$id_ref&miens_parc=$miens_parc&refer=$refer&parc=$id_parc&id_ref_parc=$id_ref_parc&id_parc=$id_parc&proprio=$proprio&refer=$refer&gauche=1";
               $lien = urlencode($lien);
               echo "<TR><TD width='10%' align='right'><IMG SRC='images/modules/tut_form/icosequence.gif' border='0'></TD>";
               echo "<TD><DIV id='sequence'><A href=\"trace.php?link=$lien\">$mpa_ajt_seq</A></DIV></TD></TR>";
               $lien_scorm = "parseur.php?id_parc=$id_parc&miens=$miens&refer=$refer";
               $lien = urlencode($lien_scorm);
               echo "<TR><TD width='10%' align='right'><IMG SRC='images/gest_parc/scorm.gif' border='0'></TD>";
               echo "<TD><DIV id='sequence'><A HREF=\"trace.php?link=$lien\">$mess_imp_sco</A></DIV></TD></TR>";
             }
            echo "</TABLE></TD></TR>";
        }
        elseif ($nb_seq_liste == 0 && ($parc == $parc_droit || $parc_droit == $id_parc))
        {
          if (!strstr($mod_parc,"SCORM"))
          {
           $nb_seq2 = (isset($nb_seq2))?$nb_seq2:'';
           $parc_duree = (isset($parc_duree))?$parc_duree:'';
           $lien= "parcours.php?liste=$liste&action_parc=1&parcours=$parcours&id_ref=$id_ref_parc&droit_voir=$droit_voir&nb_seq=$nb_seq2&duree_parc=$parc_duree&id_ref=$id_ref&miens_parc=$miens_parc&refer=$refer&parc=$id_parc&id_ref_parc=$id_ref_parc&id_parc=$id_parc&proprio=$proprio&refer=$refer&gauche=1";
           $lien = urlencode($lien);
           echo "<TR><TD width='10%'>&nbsp;</TD><TD width='100%'><TABLE width=100% cellpadding=0 cellspacing=2>";
           echo "<TR><TD width='10%' align='right'><IMG SRC='images/modules/tut_form/icosequence.gif' border='0'></TD>";
           echo "<TD><DIV id='sequence'><A href=\"trace.php?link=$lien\">$msq_creer_seq_parc</DIV></TD></TR>";
           $lien_scorm = "parseur.php?id_parc=$id_parc&miens=$miens&refer=$refer";
           $lien = urlencode($lien_scorm);
           echo "<TR><TD width='10%' align='right'><IMG SRC='images/gest_parc/scorm.gif' border='0'></TD>";
           echo "<TD><DIV id='sequence'><A HREF=\"trace.php?link=$lien\">$mess_imp_sco</A></DIV></TD></TR></TABLE></TD></TR>";
          }
          else
           echo "</TABLE></TD></TR>";
        }
        $p_i++;
    }
  echo "</TABLE></TD></TR></TABLE></td>";
?>
