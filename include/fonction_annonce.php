<?php
function send_grp_mail($liste_envoi,$affiche_groupe){
   global $connect,$lg;
   require "../lang$lg.inc.php";
   $send_to = "";
   $list_envoi = explode(",",$liste_envoi);
   $nb_envoi = count($list_envoi);
   $i=0;
   while ($i < $nb_envoi){
      $envoyer = explode("|",$list_envoi[$i]);
      $adresse = $envoyer[0];
      $num = $envoyer[1];
      if ($envoi[$num] == 'on' && $i < $nb_envoi-1)
         $send_to .= $num.",";
      elseif ($envoi[$num] == 'on' && $i == $nb_envoi-1)
         $send_to .= $num;
    $i++;
   }
   if ($affiche_groupe != 3)
     $vers = "$mess_mail_avert $mess_mail_cert_app $mess_menu_gestion_grp $nom_grp";
   else
     $vers = "$mess_mail_avert $mess_mail_cert_app";
   if ($send_to != ""){
     $retfunc = "send_to=$send_to&sous_grp=1&message_mail=$vers";
     return $retfunc;
   }
}
function cherche_forum($id_grp,$date_compare,$majuscule)
{
   global $connect,$lg;
   require "../lang$lg.inc.php";
   $nom_grpe =  GetDataField ($connect,"select grp_nom_lb from groupe where grp_cdn = $id_grp","grp_nom_lb");
   $id_forum = GetDataField ($connect,"select id from forums where name ='$nom_grpe'","id");
   $comment_forum = GetDataField ($connect,"select name from forums where id='$id_forum'","name");
   $nom_grp = "groupe".$id_grp;
   $req_mess_for = mysql_query ("select count(*) from $nom_grp where
                                 datestamp > '$date_compare' and
                                 author = '$majuscule' and
                                 approved='Y'");
   $nbr_mess_for = mysql_result($req_mess_for,0);
   if ($nbr_mess_for > 0)
     return $nbr_mess_for;
}
function cherche_seq($id_app,$id_grp)
{
  global $id_user,$typ_user,$connect,$lg,$mess_depasse,$mess_suivi;
   require "./lang$lg.inc.php";
  $tut_grp = GetDataField ($connect,"select grp_tuteur_no from groupe where grp_cdn=$id_grp","grp_tuteur_no");
  if ($typ_user == 'RESPONSABLE_FORMATION')
     $resp_grp = GetDataField ($connect,"select grp_resp_no from groupe where grp_cdn=$id_grp","grp_resp_no");
  if ($typ_user == 'RESPONSABLE_FORMATION' && $resp_grp == $id_user && $id_user != $tut_grp)
     $req = requete("presc_seq_no,presc_datefin_dt","prescription_$id_grp","presc_utilisateur_no = '$id_app'");
  elseif ($typ_user == 'RESPONSABLE_FORMATION' && $tut_grp != $id_user && $resp_grp != $id_user )
     $req = requete("presc_seq_no,presc_datefin_dt","prescription_$id_grp","presc_utilisateur_no = '$id_app' AND presc_prescripteur_no = $id_user");
  elseif ($typ_user == 'FORMATEUR_REFERENT' && $id_user != $tut_grp)
     $req = requete("presc_seq_no,presc_datefin_dt","prescription_$id_grp","presc_utilisateur_no = '$id_app' AND presc_formateur_no = $id_user");
  elseif ($typ_user == 'ADMINISTRATEUR' && $id_user != $tut_grp)
     $req = requete("presc_seq_no,presc_datefin_dt","prescription_$id_grp","presc_utilisateur_no = '$id_app'");
  elseif (($typ_user == 'TUTEUR' || $typ_user == 'ADMINISTRATEUR' || $typ_user == 'FORMATEUR_REFERENT' || $typ_user == 'RESPONSABLE_FORMATION') && $id_user == $tut_grp)
     $req = requete("presc_seq_no,presc_datefin_dt","prescription_$id_grp","presc_utilisateur_no = '$id_app'");
  if ($req == FALSE)
     return $retour;
  $nb_req = mysql_num_rows($req);
  $jj = 0;
  $kk = 0;
  if ($nb_req > 0)
  {
    $today = date("Y-n-d");
    $ii = 0;
    while ($data = mysql_fetch_object($req))
    {
      $id_seq = $data->presc_seq_no;
      $date_fin = $data->presc_datefin_dt;
      $nb_date_fin = nb_jours($date_fin);
      $nb_today = nb_jours($today);
      $etat_seq = GetDataField ($connect,"select suiv2_etat_lb from suivi2_$id_grp where
                                          suiv2_seq_no =$id_seq AND
                                          suiv2_utilisateur_no = $id_app","suiv2_etat_lb");
      if ($nb_date_fin < ($nb_today))
      {
         if ($etat_seq == "A FAIRE" || $etat_seq == "EN COURS")
         {
            $kk++;
            $diff = $nb_today - $nb_date_fin;
            $mess_depasse .="<font size='1' >&nbsp;\$mess_dep_form $diff \$mess_jours : </font>\$titre_sequence";
         }
      }
      for ($nn = 1;$nn < 10;$nn++)
      {
          if ($nb_date_fin == ($nb_today+$nn))
          {
             if ($etat_seq == "A FAIRE" || $etat_seq == "EN COURS")
             {
                $jj++;
                $mess_suivi .="<font size='2'>&nbsp;&nbsp;&nbsp;$nn \$mess_mess_att2&nbsp;</font>";
             }
          }
      }
      $ii++;
    }
    $retour = "$kk|$jj*$mess_depasse*$mess_suivi";
   }
   return $retour;
}
?>
