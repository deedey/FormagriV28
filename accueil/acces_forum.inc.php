<?php
if (!isset($_SESSION)) session_start();
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
if (!isset($_SESSION['id_user']) || $_SESSION['id_user'] == "")
{
  exit();
}

require ("../admin.inc.php");
require ("../fonction.inc.php");
include ('../include/UrlParam2PhpVar.inc.php');
require ("../fonction_html.inc.php");
require ("../lang$lg.inc.php");
require ("../langues/accueil.inc.php");
require ("fonction_accueil.inc.php");
dbconnect();
$affiche_forum = '';
$affiche_footer = '';
$affiche_entete='';
$step1 = 0;
$der_connex = mysql_query("SELECT date_fin,heure_fin from log WHERE login ='$login' AND date_fin !='0000-00-00' order by log_cdn desc");
$result = mysql_num_rows($der_connex);
if ($result > 0)
   $madate_compare = mysql_result($der_connex,0,"date_fin")." ".mysql_result($der_connex,0,"heure_fin");
else
   $madate_compare = "2007-01-01 00:00:00";

$nom_user=GetDataField ($connect,"select util_nom_lb from utilisateur WHERE util_cdn = '$id_user'","util_nom_lb");
$prenom_user=GetDataField ($connect,"select util_prenom_lb from utilisateur WHERE util_cdn = '$id_user'","util_prenom_lb");
$mon_nom = $prenom_user." ".$nom_user;
  if ($typ_user == "TUTEUR")
  {
     $req_grp = mysql_query ("select groupe.grp_cdn,groupe.grp_nom_lb from tuteur,utilisateur_groupe,groupe where
                                   groupe.grp_tuteur_no = '$id_user' OR
                                   (tuteur.tut_tuteur_no = '$id_user' and
                                   tuteur.tut_apprenant_no=utilisateur_groupe.utilgr_utilisateur_no and
                                   utilisateur_groupe.utilgr_groupe_no = groupe.grp_cdn) group by groupe.grp_cdn
                                   order by groupe.grp_nom_lb");
  }
  else
  {
     $req_grp = mysql_query ("select * from groupe order by grp_nom_lb");
  }
  if ($req_grp)
  {
      $affiche_forum .= "";
      while ($item = mysql_fetch_object($req_grp))
      {
           $num_grp = $item->grp_cdn;
           $nom_grp = $item->grp_nom_lb;
           $resp_grp = $item->grp_resp_no;
           $tut_grp = $item->grp_tuteur_no;
           if ($typ_user == "RESPONSABLE_FORMATION")
           {
              $ifPresc = mysql_num_rows(mysql_query("select presc_cdn from prescription_$id_grp where presc_formateur_no = '$id_user' or presc_prescripteur_no = '$id_user'"));
              $ifTut = mysql_num_rows(mysql_query("select tut_tuteur_no from tuteur,utilisateur_groupe where
                                                    (tut_tuteur_no = '$id_user' and
                                                    tut_apprenant_no = utilgr_utilisateur_no and
                                                    utilgr_groupe_no = $id_grp)"));
           }
           elseif ($typ_user == "FORMATEUR_REFERENT")
           {
              $ifPresc = mysql_num_rows(mysql_query("select presc_cdn from prescription_$id_grp where presc_formateur_no = '$id_user'"));
              $ifTut = mysql_num_rows(mysql_query("select tut_tuteur_no from tuteur,utilisateur_groupe where
                                                    (tut_tuteur_no = '$id_user' and
                                                    tut_apprenant_no = utilgr_utilisateur_no and
                                                    utilgr_groupe_no = $id_grp)"));
           }
           if (isset($ifPresc) && $ifPresc == 0 && isset($ifTut) && $ifTut == 0 && $resp_grp != $id_user && $tut_grp != $id_user && $typ_user != "ADMINISTRATEUR" && $typ_user != "TUTEUR")
              continue;
           $nbr_mess_forum = cherche_tout_forum($num_grp,$madate_compare,$mon_nom);
           if ($nbr_mess_forum > 0)
           {
                 $step1++;
                 $id_forum = GetDataField ($connect,"select id from forums where name ='$nom_grp'","id");
                 if ($step1 == 1)
                 $affiche_forum .= couleur_tr($step1,"")."<td><a href=\"forum/list.php?f=$id_forum&collapse=1\">".
                                   $nom_grp."</a></td>".
                                   "<td>$nbr_mess_forum ".strtolower($mess_mail_mess)."(s)</td></tr>";
          }

      }
  }
  if (!isset($step1) || (isset($step1) && $step1 == 0))
  {
     $step1++;
     $affiche_forum .= couleur_tr($step1,'')."<td colspan='3'>".$msgfrm_app."</td></tr>";
     $step1++;
  }
  $nbr_mess_formateurs = cherche_forum_simple(1,$madate_compare,$mon_nom);
  $nbr_mess_libre = cherche_forum_simple(3,$madate_compare,$mon_nom);
  if ($nbr_mess_formateurs > 0)
  {
     $affiche_forum .= couleur_tr($step1,"")."<td><a href=\"forum/list.php?f=1&collapse=1\">".
                       $mess_menu_forum." ".$mess_cadre_pedago."</a></td>".
                       "<td>$nbr_mess_formateurs ".strtolower($mess_mail_mess)."(s)</td></tr>";
     $step1++;
  }
  else
  {
     $affiche_forum .= couleur_tr($step1,"")."<td colspan='3'>".$msgfrm_form."</td></tr>";
     $step1++;
  }
  $etat_flib = GetDataField($connect,"select param_etat_lb from param_foad where param_typ_lb='forum_libre'","param_etat_lb");
  if ($etat_flib == 'OUI')
  {
     if ($nbr_mess_libre)
     {
        $affiche_forum .= couleur_tr($step1,"")."<td><a href=\"forum/list.php?f=1&collapse=1\">".
        $mess_menu_forum." ".$mess_menu_forum_lib."</a></td>".
        "<td>$nbr_mess_libre ".strtolower($mess_mail_mess)."(s)</td></tr>";
     $step1++;
     }
     else
     {
        $affiche_forum .= couleur_tr($step1,"")."<td colspan='3'>".$msgfrm_lib."</td></tr>";
     $step1++;
     }
  }
  else
     $affiche_forum .= couleur_tr($step1,"")."<td colspan='3'>".$msgfrm_nolib."</td></tr>";
  $premier_titre = $mess_menu_forum.' '.$mess_menu_gestion_grp;
  $affiche_entete .= '<center><table cellpadding="8" cellspacing="1" border=0 width="98%">'.
                     '<tr><td class="barre_titre">'.$premier_titre.'</td>'.
                     '<td  class="barre_titre">'.$mess_nb_msg.'</td></tr>';

  if (isset($step1) && $step1 > 0)
     $affiche_footer.= "</table></center>";
  $step1 = 0;
  $affiche_content = $affiche_entete.$affiche_forum.$affiche_footer;
echo utf2Charset($affiche_content,$charset);
?>