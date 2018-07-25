<?php
/****************************************** ***************
*                                                         *
* Copyright  formagri/cnerta/eduter/enesad                *
* Dey Bendifallah                                         *
* Ce script fait partie intégrante du LMS Formagri.       *
* Il peut être modifié ou utilisé à d'autres fins.        *
* Il est libre et sous licence GPL                        *
* Les auteurs n'apportent aucune garantie                 *
*                                                         *
**********************************************************/
if (!isset($_SESSION))
    session_start();
if (!isset($_SESSION['IDUSER']) || $_SESSION['IDUSER'] == "")
{
  exit();
}
include ('../../include/UrlParam2PhpVar.inc.php');
require ("../../admin.inc.php");
require ("../../fonction.inc.php");
require ("../../fonction_html.inc.php");
require ("../../langfr.inc.php");
require ("../../langues/accueil.inc.php");
dbConnect();
?>
<style>
.all{
     font-size:12px;
}
.barre_titre{
     background-color: #2b677a;
     color: #FFF;
     font-weight: bold;
     font-family: arial;
     font-size: 13px;
     padding-left: 3px;
     padding-top: 3px;
     padding-bottom: 3px;

}
.sous_titre {
     text-align:left;
     font-family:arial;
     background-color: #D4E7ED; /*#F6E7D4;*/
     padding:4px;
     font-size:13px;
     color:#333333;
     border:1px solid #24677A;
}
</style>
<?php
$date_op = date("Y-m-d H:i:s" ,time());
$date_fiche = substr($date_op,0,10);
$der_connex = mysql_query("SELECT * from log WHERE login ='$login' AND date_fin != '0000-00-00' order by log_cdn desc");
$res = mysql_num_rows($der_connex);
if ($res > 0)
{
   $date_tab = mysql_result($der_connex,0,"date_fin");
}
else
   $date_tab = $date_fiche;
if ($objet == "formation")
{
   $req_formation = mysql_query("select * from groupe where grp_datecreation_dt > '$date_tab' order by grp_datecreation_dt,grp_resp_no,grp_nom_lb asc");
   $nb_req = mysql_num_rows($req_formation);
   if ($nb_req > 0)
   {
       while ($item = mysql_fetch_object($req_formation))
       {
          $num_grp = $item->grp_cdn;
          $nom_grp = $item->grp_nom_lb;
          $date_grp = $item->grp_datecreation_dt;
          $auteur_grp = $item->grp_resp_no;
          $nom_auteur = GetDataField ($connect,"select util_nom_lb from utilisateur WHERE util_cdn = '$auteur_grp'","util_nom_lb");
          $prenom_auteur=GetDataField ($connect,"select util_prenom_lb from utilisateur WHERE util_cdn = '$auteur_grp'","util_prenom_lb");

         if ($date_grp > $date_tab)
         {
          $step2++;
          if ($step2 == 1)
          {
              $affiche_grp .= '<center><table cellpadding="4" cellspacing="1" border=0 width="98%">'.
                                '<tr><td colspan="3" class="sous_titre" style=\"font-weight: bold;\">'.$nb_req.' '.$mess_gp_nom_grp.'(s)</td></tr>'.
                                '<tr><td class="barre_titre">'.$msq_titre.'</td>'.
                                '<td class="barre_titre">'.$mrc_aut.'</td>'.
                                '<td class="barre_titre">'.$mess_mess_date.'</td></tr>';
          }
          $affiche_grp .= couleur_tr($step2,"")."<td class='all'>$nom_grp</td>".
                           "<td class='all'>".$nom_auteur." ".$prenom_auteur." </td>".
                           "<td class='all'>".reverse_date(substr($date_grp,0,10),"-","/")."</td></tr>";
         }

       }
       if (isset($step2) && $step2 > 0)
          $affiche_grp .= "</table></center>";
     }
     if  (!isset($step2) || (isset($step2) && $step2 == 0))
         $affiche_grp = $msgCnt_formno;
     $step2 = 0;
     echo utf2Charset($affiche_grp,'iso-8859-1');
   exit;
}

if ($objet == "module")
{
   $req_module = mysql_query("select * from parcours where parcours_create_dt > '$date_tab' and parcours_type_on = '0' order by parcours_create_dt,parcours_auteur_no,parcours_nom_lb asc");
   $nb_req = mysql_num_rows($req_module);
   if ($nb_req > 0)
   {
       while ($item = mysql_fetch_object($req_module))
       {
          $num_parc = $item->parcours_cdn;
          $nom_parc = $item->parcours_nom_lb;
          $date_parc = $item->parcours_create_dt;
          $ref_parc = $item->parcours_referentiel_no;
          $auteur_parc = $item->parcours_auteur_no;
          $nom_auteur = GetDataField ($connect,"select util_nom_lb from utilisateur WHERE util_cdn = '$auteur_parc'","util_nom_lb");
          $prenom_auteur=GetDataField ($connect,"select util_prenom_lb from utilisateur WHERE util_cdn = '$auteur_parc'","util_prenom_lb");

         if ($date_parc > $date_tab)
         {
          $step2++;
          if ($step2 == 1)
          {
              $affiche_parc .= '<center><table cellpadding="4" cellspacing="1" border=0 width="98%">'.
                                '<tr><td colspan="3" class="sous_titre" style=\"font-weight: bold;\">'.$nb_req.' '.$msgnew_mod.'</td></tr>'.
                                '<tr><td class="barre_titre">'.$msq_titre.'</td>'.
                                '<td class="barre_titre">'.$mrc_aut.'</td>'.
                                '<td class="barre_titre">'.$mess_mess_date.'</td></tr>';
          }
          $affiche_parc .= couleur_tr($step2,"")."<td class='all'>$nom_parc</td>".
                           "<td class='all'>".$nom_auteur." ".$prenom_auteur." </td>".
                           "<td class='all'>".reverse_date($date_parc,"-","/")."</td></tr>";
         }

       }
       if (isset($step2) && $step2 > 0)
          $affiche_parc .= "</table></center>";
     }
     if  (!isset($step2) || (isset($step2) && $step2 == 0))
         $affiche_parc = $msgCnt_modno;
     $step2 = 0;
     echo utf2Charset($affiche_parc,'iso-8859-1');
   exit;
}
if ($objet == "sequence")
{
   $req_seq = mysql_query("select * from sequence where seq_create_dt > '$date_tab' and seq_type_on = '0' order by seq_create_dt,seq_auteur_no,seq_titre_lb asc");
   $nb_req = mysql_num_rows($req_seq);
   if ($nb_req > 0)
   {
       while ($item = mysql_fetch_object($req_seq))
       {
          $num_seq = $item->seq_cdn;
          $nom_seq = $item->seq_titre_lb;
          $date_seq = $item->seq_create_dt;
          $auteur_seq = $item->seq_auteur_no;
          $ref_seq = GetDataField ($connect,"select seqref_referentiel_no from sequence_referentiel where seqref_seq_no = $num_seq","seqref_referentiel_no");
          $nom_auteur = GetDataField ($connect,"select util_nom_lb from utilisateur WHERE util_cdn = '$auteur_seq'","util_nom_lb");
          $prenom_auteur=GetDataField ($connect,"select util_prenom_lb from utilisateur WHERE util_cdn = '$auteur_seq'","util_prenom_lb");
          if ($date_seq > $date_tab)
          {
              $step2++;
              if ($step2 == 1)
              {
                  $affiche_seq .= '<center><table cellpadding="4" cellspacing="1" border=0 width="98%">'.
                                  '<tr><td colspan="3" class="sous_titre" style=\"font-weight: bold;\">'.$nb_req.' '.$msgnew_seq.'</td></tr>'.
                                  '<tr><td class="barre_titre">'.$msq_titre.'</td>'.
                                  '<td class="barre_titre">'.$mrc_aut.'</td>'.
                                  '<td class="barre_titre">'.$mess_mess_date.'</td></tr>';
              }
              $affiche_seq .= couleur_tr($step2,"")."<td class='all'>$nom_seq</td>".
                              "<td class='all'>".$nom_auteur." ".$prenom_auteur." </td>".
                              "<td class='all'>".reverse_date($date_seq,"-","/")."</td></tr>";
          }
       }
       if  (isset($step2) && $step2 > 0)
           $affiche_seq .= "</table></center>";
   }
   if  (!isset($step2) || (isset($step2) && $step2 == 0))
       $affiche_seq = $msgCnt_seqno;
   $step2 = 0;
     echo utf2Charset($affiche_seq,'iso-8859-1');
   exit;
}
if ($objet == "activite")
{
   $req_act = mysql_query("select * from activite where act_create_dt > '$date_tab' and act_publique_on = '1' order by act_create_dt,act_auteur_no,act_nom_lb asc");
   $nb_req = mysql_num_rows($req_act);
   if ($nb_req > 0)
   {
       while ($item = mysql_fetch_object($req_act))
       {
          $num_act = $item->act_cdn;
          $nom_act = $item->act_nom_lb;
          $date_act = $item->act_create_dt;
          $auteur_act = $item->act_auteur_no;
          $nom_auteur = GetDataField ($connect,"select util_nom_lb from utilisateur WHERE util_cdn = '$auteur_act'","util_nom_lb");
          $prenom_auteur=GetDataField ($connect,"select util_prenom_lb from utilisateur WHERE util_cdn = '$auteur_act'","util_prenom_lb");
          if ($date_act > $date_tab)
          {
              $step2++;
              if ($step2 == 1)
              {
                  $affiche_act .= '<center><table cellpadding="4" cellspacing="1" border=0 width="98%">'.
                                  '<tr><td colspan="3" class="sous_titre">'.$nb_req.' '.$msgnew_act.'</td></tr>'.
                                  '<tr><td class="barre_titre">'.$msq_titre.'</td>'.
                                  '<td class="barre_titre">'.$mrc_aut.'</td>'.
                                  '<td class="barre_titre">'.$mess_mess_date.'</td></tr>';
              }
              $affiche_act .= couleur_tr($step2,"")."<td class='all'>$nom_act</td>".
                              "<td class='all'>".$nom_auteur." ".$prenom_auteur." </td>".
                              "<td class='all'>".reverse_date($date_act,"-","/")."</td></tr>";
          }
       }
       if  (isset($step2) && $step2 > 0)
           $affiche_act .= "</table></center>";
   }
   if  (!isset($step2) || (isset($step2) && $step2 == 0))
       $affiche_act = $msgCnt_actno;
   $step2 = 0;
   echo utf2Charset($affiche_act,'iso-8859-1');
   exit;
}

?>
