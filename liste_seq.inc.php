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
GLOBAL $connect,$star,$proprio,$suite_affichage;
include ('include/head_list_seq.php');
    if ($requete_seq == "" && !isset($_SESSION['aut_ajx']))
       $requete_seq = "select * from sequence where seq_auteur_no = $id_user order by seq_titre_lb";
    elseif($requete_seq == "" && isset($_SESSION['aut_ajx']))
       $requete_seq = "select * from sequence where seq_cdn = ".$id_seq." order by seq_titre_lb";
    elseif (isset($id_seq) && $id_seq > 0)
       $requete_seq = "select * from sequence where seq_cdn = $id_seq";
    unset($_SESSION['aut_ajx']);
    $lien_scorm = "parseur.php?miens=$miens&refer=$refer&prov=seq";
    $lien = urlencode($lien_scorm);
    echo "<TR><TD align='left' colspan=2><DIV id='seqinv'><IMG SRC='images/gest_parc/scorm.gif' border='0'>&nbsp;";
    echo "&nbsp;<A HREF=\"trace.php?link=$lien\">$mess_imp_sco</A></DIV></TD></TR>";
    $_SESSION['requete_seq'] = $requete_seq;
    $req_seq = mysql_query($requete_seq);;
    $nb_req_seq = mysql_num_rows($req_seq);
    $s_i = 0;
             while ($s_i < $nb_req_seq){
                 $sequence = mysql_result ($req_seq,$s_i,"seq_cdn");
                 $seq_auteur = mysql_result ($req_seq,$s_i,"seq_auteur_no");
                 $nom_createur = GetDataField ($connect,"select util_nom_lb from utilisateur where util_cdn = $seq_auteur","util_nom_lb");
                 $prenom_createur = GetDataField ($connect,"select util_prenom_lb from utilisateur where util_cdn = $seq_auteur","util_prenom_lb");
                 $majuscule = "$prenom_createur $nom_createur";
                 $date_creat = mysql_result ($req_seq,$s_i,"seq_create_dt");
                 $ch_dtc = explode("-",$date_creat);
                 $dtc = date("d-m-Y", mktime(0, 0, 0, $ch_dtc[1],$ch_dtc[2],$ch_dtc[0]));
                 $dtc = strftime("%A %e %B %Y", mktime(0, 0, 0, $ch_dtc[1],$ch_dtc[2],$ch_dtc[0]));
                 $date_modif = mysql_result ($req_seq,$s_i,"seq_modif_dt");
                 $ch_dtm = explode("-",$date_modif);
                 $dtm = strftime("%A %e %B %Y", mktime(0, 0, 0, $ch_dtm[1],$ch_dtm[2],$ch_dtm[0]));
                 $seq_titre = mysql_result ($req_seq,$s_i,"seq_titre_lb");
                 $seq_type = mysql_result ($req_seq,$s_i,"seq_type_on");
                 $seq_duree = mysql_result ($req_seq,$s_i,"seq_duree_nb");
                 $mod_seq = mysql_result ($req_seq,$s_i,"seq_type_lb");
                 $droit_voir = mysql_result ($req_seq,$s_i,"seq_publique_on");
                 $seq_ref_query = mysql_query ("select seqref_referentiel_no from sequence_referentiel where seqref_seq_no = $sequence");
                 $nb_seq_ref = mysql_num_rows ($seq_ref_query);
                 if ($nb_seq_ref == 1)
                    $id_ref = GetDataField ($connect,"select seqref_referentiel_no from sequence_referentiel where seqref_seq_no = $sequence","seqref_referentiel_no");
                 else
                    $id_ref = 0;
                 if ($id_ref == 0)
                    $nom_referentiel = $msq_sl;
                 else
                    $nom_referentiel = GetDataField ($connect,"select ref_nomabrege_lb from referentiel where ref_cdn = $id_ref","ref_nomabrege_lb");
                 if ($seq_type == 1 && $droit_voir == 1){
                    $div="DIV id='sequence'";
                    $icono = "<IMG SRC='images/modules/icotype.gif' border='0' ".
                     bulle("$mess_visdup<br />$mess_notpresc","","LEFT","ABOVE",120);
                 }else{
                    if ($id_seq == $sequence)
                       $div="DIV id='seqinv'";
                    else
                       $div="DIV id='sequence'";
                    $icono = "";
                 }
                 if (strstr($mod_seq,"SCORM"))
                    $icono .= "<IMG SRC='images/gest_parc/scorm.gif' border='0' ".
                           bulle($mess_seq_sco,"","LEFT","ABOVE",100);
                 if ($seq_auteur == $id_user || $typ_user == "ADMINISTRATEUR"){
                    echo "<TR><TD width='15%' align='right' valign='top' nowrap>";
                    if ($droit_voir == 0)
                       echo "&nbsp;<IMG SRC=\"images/modules/tut_form/icocadenas.gif\" border='0' ".
                       bulle($mess_notdupli,"","LEFT","ABOVE",100);
                    elseif ($icono == "")
                       echo "&nbsp;&nbsp;";
                    elseif ($icono != "")
                       echo $icono;
                    if ($seq_auteur == $id_user)
                       echo "&nbsp;<IMG SRC=\"images/gest_parc/icofeuilb.gif\" border='0'>";
                    else
                       echo "&nbsp;";
                 }else{
                    echo "<TR><TD width='15%' valign='top' align='right' nowrap>";
                    if ($droit_voir == 0)
                       echo "&nbsp;<IMG SRC=\"images/modules/tut_form/icocadenas.gif\" border='0' ".
                       bulle($mess_notdupli,"","LEFT","ABOVE",100);
                    elseif ($icono == "")
                       echo "&nbsp;&nbsp;";
                    elseif ($icono != "")
                       echo $icono;
                    echo "&nbsp;&nbsp;-";
                 }
                 echo "</TD>";
                 $affichage = "&nbsp;<B>$msq_seq</B><BR>&nbsp;&nbsp;- $mrc_aut : <B>$majuscule</B><BR>&nbsp;&nbsp;- $mess_menu_gest_seq_ref : &nbsp;&nbsp; <B>$dtc</B><BR>&nbsp;&nbsp;- $mess_modif_dt :&nbsp;&nbsp; <B>$dtm</B>";
                 if (!strstr($mod_seq,"SCORM"))
                    $nb_act_sequence_query = mysql_query ("select * from activite where act_seq_no = $sequence order by act_ordre_nb");
                 else
                    $nb_act_sequence_query = mysql_query ("select * from scorm_module where mod_seq_no = $sequence order by mod_cdn");
                 $nb_act_sequence = mysql_num_rows ($nb_act_sequence_query);
                 $combi = ($droit_voir == 0 && $seq_type == 1) ? 0 : 1;
                 if ($sequence == $id_seq)
                    $seq_titre = "<span style='font-size:13px;border:1px solid #D45211; padding:1px;'>$seq_titre</span>";
                 if ($combi == 1 || $seq_auteur == $id_user || $typ_user == "ADMINISTRATEUR")
                 {
                   $lien="sequence_entree.php?toutes_seq=0&liste=1&id_seq=$sequence&consult=1&parcours=0&proprio=$proprio&refer=$refer&id_ref_seq=$id_ref&miens=$miens&miens=$miens&refer=$refer&liste_act_seq=1";
                   $lien = urlencode($lien);
                   echo "<TD valign='top' style='text-align:left;'><$div><A HREF=\"trace.php?link=$lien\" target='main' title=\"$msq_seq_det\" ".
                   bulle($affichage,"","RIGHT","ABOVE",280)."$seq_titre</A></DIV>";
                 }
                 else
                   echo "<TD valign='top' style='text-align:left;'><$div><A HREF=\"javascript:void(0);\" target='main' ".
                   bulle($affichage,"","RIGHT","ABOVE",280)."$seq_titre</A></DIV>";
                 if ($sequence == $id_seq)
                 {
                    echo "<TABLE width=100% cellpadding=0 cellspacing=2 border='0'>";
                    if ($nb_act_sequence > 0)
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
                         $dtm_act = $dtm;
                         $dtc_act = $dtc;
                         $act_seq_titre = mysql_result ($nb_act_sequence_query,$act_i,"mod_titre_lb");
                         $act_seq_dv = $droit_voir;
                       }else{
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
                         $act_seq_ress = mysql_result ($nb_act_sequence_query,$act_i,"act_ress_no");
                         $droit_voir_act = mysql_result ($nb_act_sequence_query,$act_i,"act_publique_on");
                         if (isset($id_ress) &&  $id_ress == 0)
                            $ressource = $msq_aucune;
                         else
                            $ressource = GetDataField ($connect,"select ress_titre from ressource_new where ress_cdn = $act_seq_ress","ress_titre");
                       }
                       if ($act_auteur == $id_user || $typ_user == "ADMINISTRATEUR")
                       {
                            echo "<TR><TD width='8%' align='right' valign='top' nowrap>";
                            if (isset($droit_voir_act) && $droit_voir_act == 0)
                               echo "<IMG SRC=\"images/modules/tut_form/icocadenas.gif\" border='0' ".
                                    bulle($mess_notdupli,"","LEFT","ABOVE",100);
                            if ($act_auteur == $id_user)
                               echo "&nbsp;<IMG SRC=\"images/gest_parc/icofeuilb.gif\" border='0'>";
                            else
                               echo "&nbsp;";
                       }else{
                            echo "<TR><TD width='8%' valign='top' align='right' nowrap>-";
                            if (isset($droit_voir_act) && $droit_voir_act == 0)
                               echo "&nbsp;&nbsp;<IMG SRC=\"images/modules/tut_form/icocadenas.gif\" border='0' ".
                                    bulle($mess_notdupli,"","LEFT","ABOVE",100);
                       }
                       echo xApiShow($act_seq_num);
                       echo "</TD>";
                         $affichage_act = "&nbsp<B>$msq_activite</B><BR>&nbsp;&nbsp;- $mrc_aut : <B>$majuscule_act</B><BR>&nbsp;&nbsp;- $mess_menu_gest_seq_ref : <BR>&nbsp;&nbsp;&nbsp;&nbsp; <B>$dtc_act</B><BR>&nbsp;&nbsp;- $mess_modif_dt :<BR>&nbsp;&nbsp;&nbsp;&nbsp; <B>$dtm_act</B>";
                         $aff_act_aut =  bulle($affichage_act,"","RIGHT","ABOVE",280);
                         if ($id_act != $act_seq_num){
                           if (($act_auteur == $id_user || $typ_user == "ADMINISTRATEUR") && !strstr($mod_seq,"SCORM")){
                              $lien="sequence_entree.php?liste=$liste&id_seq=$sequence&action_act=1&modif_act=1&id_act=$act_seq_num&id_ress=$act_seq_ress&liste=$liste&proprio=$proprio&refer=$refer&id_ref_seq=$id_ref&miens=$miens&refer=$refer&liste_act_seq=1";
                              $lien = urlencode($lien);
                              echo "<TD valign='top'><DIV id='sequence'><A HREF=\"trace.php?link=$lien\" target='main' title=\"$msq_modifier\" $aff_act_aut $act_seq_titre</A></DIV></TD></TR>";
                           }elseif(($act_auteur == $id_user || $typ_user == "ADMINISTRATEUR") && strstr($mod_seq,"SCORM")){
                              $lien="modif_sco.php?liste=$liste&id_seq=$sequence&modif_act=1&id_act=$act_seq_num&liste=$liste&proprio=$proprio&refer=$refer&id_ref=$id_ref&miens=$miens&refer=$refer&liste_act_seq=1&prov=seq";
                              $lien = urlencode($lien);
                              echo "<TD valign='top'><DIV id='sequence'><A HREF=\"trace.php?link=$lien\" target='main'>$act_seq_titre</A></DIV></TD></TR>";
                           }elseif($act_auteur != $id_user && $typ_user != "ADMINISTRATEUR" && !strstr($mod_seq,"SCORM"))
                              echo "<TD valign='top'><DIV id='sequence'><A HREF=\"javascript:void(0);\" target='main' $aff_act_aut $act_seq_titre</A></DIV></TD></TR>";
                           elseif($act_auteur != $id_user && $typ_user != "ADMINISTRATEUR" && strstr($mod_seq,"SCORM"))
                              echo "<TD valign='top'><DIV id='sequence'><A HREF=\"javascript:void(0);\" target='main' $aff_act_aut $act_seq_titre</A></DIV></TD></TR>";
                         }else
                           echo "<TD valign='top'><DIV id='seqinv'><A HREF=\"javascript:void(0);\"target='main' $suite_affichage >$act_seq_titre</A></DIV></TD></TR>";
                        $act_i++;
                      }
                    }else
                      echo "</TD></TR>";
                      if (($seq_auteur == $id_user || $typ_user == "ADMINISTRATEUR"))
                      {
                         if (!strstr($mod_seq,"SCORM"))
                         {
                              $lien="sequence_entree.php?liste=$liste&id_seq=$sequence&action_seq=1&proprio=$proprio&refer=$refer&choix=1&id_ref_seq=$id_ref&miens=$miens";
                              $lien = urlencode($lien);
                              echo "<TR><TD valign='top' align=right><IMG SRC='images/modules/tut_form/icoactivite.gif' border='0'></TD>";
                              echo "<TD valign='top'><DIV id='sequence'><A HREF=\"trace.php?link=$lien\" target='main'>$mess_ag_choix</A></DIV></TD></TR>";
                              $lienTinCan = "parseurTinCan.php?liste=$liste&id_seq=$sequence&proprio=$proprio&refer=$refer&id_ref_seq=$id_ref&miens=$miens&prov=seq";
                              $lien = urlencode($lienTinCan);
                              echo "<TR><TD align='left' colspan=2><DIV id='seqinv'>&nbsp;&nbsp;<IMG SRC='images/gest_parc/xApi.gif' border='0'>&nbsp;";
                              echo "&nbsp;<A HREF=\"trace.php?link=$lien\">Importer une activité TinCan (xApi)</A></DIV></TD></TR>";
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
                         if ($TweetOk == 1)
                         {
                           $nbrTwit = mysql_num_rows(mysql_query("select * from seq_twitter where seqtwit_seq_no='$sequence'"));
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
                          }//finif ($TweetOk == 1)
                     }
                      else
                      {
                         $nbrWk = mysql_num_rows(mysql_query("select * from wiki where wiki_seq_no='$sequence'"));
                         if ($nbrWk > 0)
                         {
                             $lien= "wiki/wikiAjout.php?id_seq=$sequence&keepThis=true&TB_iframe=true&height=550&width=700";
                             echo "<TR><TD valign='top' align='right'><IMG SRC='wiki/images/wikidoc.gif' border='0'></TD><TD>";
                             echo "<DIV id='sequence'>";
                             echo "<a href=\"$lien\" class='thickbox' title='Voir les travaux en commun' ";
                             echo "name='Voir les travaux en commun' ".
                                   bulle("Consulter les thèmes des travaux en commun de cette séquence" ,"","RIGHT","ABOVE",270).
                                   "Consulter les WikiDocs</A></DIV></TD></TR>";
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
                              "Consulter MindMaps</A></DIV></TD></TR>";
                           }
                         $nbrTwit = mysql_num_rows(mysql_query("select * from seq_twitter where seqtwit_seq_no='$sequence'"));
                         if ($nbrTwit > 0 && $TweetOk == 1)
                         {
                            echo "<TR><TD valign='top' align='right'><IMG SRC='ApiTweet/assets/tweeter.gif' border='0'></TD><TD>";
                             echo "<DIV  id='seqtwt' style='font-weight:bold;' title='Echange sur Twitter activé pour cette séquence' ".
                                  bulle("Cette séquence a été activée afin de lui permettre de lancer une activité ".
                                  "d'échanges sur Twitter via Formagri ou tout autre support de type Web, Smartphone ou encore Tablette" ,"","RIGHT","ABOVE",270).
                                  "Echange twitter activé</DIV></TD></TR>";
                         }
                      }
                      echo "</TABLE></TD>";
                 }
                 echo "</TR>";
               $s_i++;
             }
             unset($droit_voir_act);
             echo "</TABLE></TD></TR></TABLE></TD>";
?>