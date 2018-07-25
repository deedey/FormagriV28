<?php
function req_gene($requete)
{
     GLOBAL $_SESSION;
     $req = mysql_query ($requete);
     $nb_items = mysql_num_rows($req);
     if ($nb_items > 0)
        return $req;
     else
        return FALSE;

}
function cherche_forum($id_grp,$date_compare,$majuscule)
{
   GLOBAL $connect,$lg;
   $nom_grpe =  GetDataField ($connect,"select grp_nom_lb from groupe where grp_cdn = $id_grp","grp_nom_lb");
   $id_forum = GetDataField ($connect,"select id from forums where name ='$nom_grpe'","id");
   $comment_forum = GetDataField ($connect,"select name from forums where id='$id_forum'","name");
   $nom_grp = "groupe".$id_grp;
   $req_mess_for = mysql_query ("select count(*) from $nom_grp where
                                 datestamp > '$date_compare' and
                                 author != '$majuscule' and
                                 approved='Y'");
   $nbr_mess_for = mysql_result($req_mess_for,0);
   if ($nbr_mess_for > 0)
     return $nbr_mess_for;
}
function cherche_forum_simple($f,$date_compare,$majuscule)
{
   GLOBAL $connect,$lg;
   $comment_forum = GetDataField ($connect,"select table_name from forums where id='$f'","table_name");
   $req_mess_for = mysql_query ("select count(*) from $comment_forum where
                                 datestamp > '$date_compare' and
                                 author != '$majuscule' and
                                 approved='Y'");
   $nbr_mess_for = mysql_result($req_mess_for,0);
   if ($nbr_mess_for > 0)
     return $nbr_mess_for;
}
function cherche_seq($id_util,$id_grp)
{
  GLOBAL $connect,$lg;
  $req = mysql_query("select presc_seq_no,presc_datefin_dt from prescription_$id_grp where
                      presc_utilisateur_no = '$id_util'");
  $nb_req = mysql_num_rows($req);
  $jj = 0;
  $kk = 0;
  if ($nb_req > 0)
  {
    $today = date("Y-n-d");
    $ii = 0;
    while ($ii < $nb_req)
    {
      $id_seq = mysql_result($req,$ii,"presc_seq_no");
      $date_fin = mysql_result($req,$ii,"presc_datefin_dt");
      $nb_date_fin_query = mysql_query ("select TO_DAYS('$date_fin')");
      $nb_date_fin = mysql_result ($nb_date_fin_query,0);
      $nb_today_query = mysql_query ("select TO_DAYS('$today')");
      $nb_today = mysql_result ($nb_today_query,0);
      $etat_seq = GetDataField ($connect,"select suiv2_etat_lb from suivi2_$id_grp where
                                          suiv2_seq_no =$id_seq AND
                                          suiv2_utilisateur_no = $id_util","suiv2_etat_lb");
      if ($nb_date_fin < ($nb_today)) {
         if ($etat_seq == "A FAIRE" || $etat_seq == "EN COURS")
         {
            $kk++;
            $mess_depasse .="<font size='1' >&nbsp;$mess_dep_form $diff $mess_jours : </font>$titre_sequence";
         }
      }
      for ($nn = 1;$nn < 10;$nn++)
      {
          if ($nb_date_fin == ($nb_today+$nn)) {
             if ($etat_seq == "A FAIRE" || $etat_seq == "EN COURS")
             {
                $jj++;
                $mess_suivi .="<font size='2'>&nbsp;&nbsp;&nbsp;$nn $mess_mess_att2&nbsp;</font>";
             }
          }
      }
      $ii++;
    }
    $retour = "$kk|$jj";
   }
   return $retour;
}

function cherche_tout_forum($id_grp,$date_compare,$majuscule)
{
   GLOBAL $connect,$lg;
   $nom_grpe =  GetDataField ($connect,"select grp_nom_lb from groupe where grp_cdn = $id_grp","grp_nom_lb");
   $id_forum = GetDataField ($connect,"select id from forums where name ='$nom_grpe'","id");
   $comment_forum = GetDataField ($connect,"select name from forums where id='$id_forum'","name");
   $nom_grp = "groupe".$id_grp;
   $req_mess_for = mysql_query ("select count(*) from $nom_grp where
                                 datestamp > '$date_compare' and
                                 author != '$majuscule' and
                                 approved='Y'");
   $nbr_mess_for = mysql_result($req_mess_for,0);
   if ($nbr_mess_for > 0)
      return $nbr_mess_for;
}

?>