<?php
/*
Modification après le 15/11/06
Appel à la fonction requete_order et autres formulations pour la récupération des données des différentes tables.
*/

$extension = str_replace("\"","'",$nom_grp);
$titre = trim($nom_parcours);
$titre = str_replace("\"","'",$titre);
$description = str_replace("\"","'",$description);
$cles = str_replace("\"","'",$cles);
$seq_query = requete_order ("*","sequence as SQ,sequence_parcours as SP","SP.seqparc_parc_no = $id_parc AND ".
                               "SP.seqparc_seq_no = SQ.seq_cdn AND ".
                               "(SQ.seq_publique_on=1 OR (SQ.seq_publique_on=0 AND SQ.seq_auteur_no=$id_user)) AND ".
                               "SQ.seq_type_lb='NORMAL'","SP.seqparc_ordre_no");
$nb_seq = mysql_num_rows ($seq_query);
if ($nb_seq == 0)
   echo $msq_noseq_parc."<br>";
else {
  $id_new_parc = Donne_ID ($connect,"SELECT max(parcours_cdn) from parcours");
  $insert_new_parc = mysql_query ("INSERT INTO parcours values ($id_new_parc,\"$titre\",\"$description\",\"$cles\",\"$id_referentiel_parc\",$id_user,\"$date_dujour\",\"$date_dujour\",$droit_voir,$type_parc,'$mode_parc')");
  while ($data_seq =  mysql_fetch_object($seq_query)) {
     $id_seq = $data_seq->seq_cdn;
     $act_query = requete_order("*","activite","act_seq_no = $id_seq AND ".
                               "(act_publique_on = 1 OR (act_publique_on=0 AND act_auteur_no=$id_user))"
                               ,"act_ordre_nb");
     $Nb_act_seq = mysql_num_rows ($act_query);
     $nom_seq =  $data_seq->seq_titre_lb;
     $desc_seq =  $data_seq->seq_desc_cmt;
     $cles_seq =  $data_seq->seq_mots_clef;
     $ordre_act = $data_seq->seq_ordreact_on;
     $duree_seq =  $data_seq->seq_duree_nb;
     $mode_seq =  $data_seq->seq_type_lb;
     $seq_type =  $data_seq->seq_type_on;
     $droit_seq =  $data_seq->seq_publique_on;
     $nom_seq = str_replace("\"","'",$nom_seq);
     $desc_seq = str_replace("\"","'",$desc_seq);
     $cles_seq = str_replace("\"","'",$cles_seq);
     if ($extension != "")
       $nom_seq .=" ".$extension;
     else
       $nom_seq .=" (".$nom_user.")";
     if ($Nb_act_seq == 0)
       echo $msq_noact."<br><br>";
     else {
        if ($droit_seq == 0 && $seq_type == 0){
          $id_new_seqparc = Donne_ID ($connect,"SELECT max(seqparc_cdn) from sequence_parcours");
          $id_ordre = Donne_ID ($connect,"SELECT seqparc_ordre_no from sequence_parcours WHERE seqparc_seq_no=$id_seq and seqparc_parc_no=$id_parc");
          $insert_new_seqparc = mysql_query ("INSERT INTO sequence_parcours values ($id_new_seqparc,\"$id_seq\",\"$id_new_parc\",$id_ordre)");
        }else{
          $id_new_seq = Donne_ID ($connect,"SELECT max(seq_cdn) from sequence");
          $insert_new_seq = mysql_query ("INSERT INTO sequence values ($id_new_seq,\"$nom_seq\",\"$desc_seq\",\"$cles_seq\",\"$ordre_act\",\"$duree_seq\",'$id_user',\"$date_dujour\",\"$date_dujour\",1,0,'$mode_seq')");
          $id_new_seqparc = Donne_ID ($connect,"SELECT max(seqparc_cdn) from sequence_parcours");
          $id_ordre = Donne_ID ($connect,"SELECT seqparc_ordre_no from sequence_parcours WHERE seqparc_seq_no=$id_seq and seqparc_parc_no=$id_parc");
          $insert_new_seqparc = mysql_query ("INSERT INTO sequence_parcours values ($id_new_seqparc,\"$id_new_seq\",\"$id_new_parc\",$id_ordre)");
          $id_ref_seq = GetDataField ($connect,"SELECT seqref_referentiel_no from sequence_referentiel WHERE seqref_seq_no = $id_seq","seqref_referentiel_no");
          $id_seqref = Donne_ID ($connect,"SELECT max(seqref_cdn) from sequence_referentiel");
          $id_ref_seq = GetDataField ($connect,"SELECT seqref_referentiel_no from sequence_referentiel WHERE seqref_seq_no = '$id_seq'","seqref_referentiel_no");
          $insert_seqref = mysql_query ("INSERT INTO sequence_referentiel values ($id_seqref,$id_new_seq,$id_ref_seq)");
          $i = 0;
          while ($data_act = mysql_fetch_object($act_query)) {
               $nommer = $data_act->act_nom_lb;
               if ($extension != "")
                 $nommer .=" $extension";
               else
                 $nommer .=" ($nom_user)";
               $id_new_act = Donne_ID ($connect,"SELECT max(act_cdn) FROM activite");
               $insert_new_act = mysql_query ("INSERT INTO activite VALUES ($id_new_act,$id_new_seq,".$DataAct->act_ordre_nb.
                                                ",\"$nommer\",\"".$DataAct->act_consigne_cmt."\",\"".$DataAct->act_commentaire_cmt.
                                                "\",'".$DataAct->act_ress_on."',".$DataAct->act_ress_no.
                                                ",".$DataAct->act_duree_nb.",\"".$DataAct->act_passagemult_on.
                                                "\",\"".$DataAct->act_acquittement_lb."\",\"".$DataAct->act_notation_on.
                                                "\",\"".$DataAct->act_devoirarendre_on."\",'$id_user',\"$date_dujour\",".
                                                "\"$date_dujour\",'".$DataAct->act_publique_on."',".$DataAct->act_flag_on.")");
          }
        }//fin else de if ($droit_seq == 0 && $seq_type == 0){
     }//fin de if ($Nb_act_seq == 0) {
  }// fin de while ($data_seq =  mysql_fetch_object($seq_query))
}// fin de if ($Nb_seq == 0) {

?>