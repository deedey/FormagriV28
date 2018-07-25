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
if (!isset($_SESSION)) session_start();
if (!isset($_SESSION['id_user']) || $_SESSION['id_user'] == "")
{
  exit();
}
require ("../admin.inc.php");
require ("../fonction.inc.php");
include ('../include/UrlParam2PhpVar.inc.php');
require ("../fonction_html.inc.php");
require ("../langues/accueil.inc.php");
require ("fonction_accueil.inc.php");
require ("../lang$lg.inc.php");
dbconnect();
$affiche_rep = "";
$step1 = 0;
$step2 = 0;
$der_connex = mysql_query("SELECT date_fin,heure_fin from log WHERE login ='$login' AND date_fin !='0000-00-00' order by log_cdn desc");
$result = mysql_num_rows($der_connex);
if ($result > 0)
   $madate_compare = mysql_result($der_connex,0,"date_fin");
else
   $madate_compare = "2007-01-01";

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
  elseif ($typ_user != "TUTEUR")
  {
     $req_grp = mysql_query ("select * from groupe order by groupe.grp_nom_lb");
  }
  if ($req_grp)
  {
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
           $req_rep = mysql_query("select * from echange_grp where ech_grp_no = $num_grp order by ech_grp_no,ech_date_dt");
           $nbr_mess_rep = mysql_num_rows($req_rep);
           if ($nbr_mess_rep > 0)
           {
                while ($fic = mysql_fetch_object($req_rep))
                {
                     $aut_fic = $fic->ech_auteur_no;
                     $date_fic = $fic->ech_date_dt;
                     $dtComp = substr($date_fic,0,10);
                     $path_fic = $fic->ech_path_lb;
                     $parent=dirname($path_fic);
                     $der_fic = substr(strrchr($path_fic,"/"),1);
                     if ($dtComp > $madate_compare)
                     {
                         $nom_auteur = GetDataField ($connect,"select util_nom_lb from utilisateur where util_cdn='$aut_fic'","util_nom_lb");
                         $prenom_auteur = GetDataField ($connect,"select util_prenom_lb from utilisateur where util_cdn='$aut_fic'","util_prenom_lb");
                         $step1++;
                         if ($step1 == 1)
                             $affiche_rep .= '<center><table cellpadding="4" cellspacing="1" border=0 width="98%">'.
                                             '<tr><td class="barre_titre" nowrap>'.$msgRep_rpf.'</td>'.
                                             '<td class="barre_titre">'.$mess_type.'</td>'.
                                             '<td class="barre_titre">'.$mess_admin_nom.'</td>'.
                                             '<td class="barre_titre">'.$mrc_aut.'</td>'.
                                             '<td class="barre_titre">'.$mess_mess_date.'</td></tr>';
                         if (!strstr($path_fic,'.'))
                         {
                             $lien = "<a href=\"modif_rep_fic.php?communes_groupe=1&rallonge=$path_fic&id_grp=$num_grp&sousdos=$path_fic&parent=$parent&dossier=$path_fic&direct=dossier&fichier=$der_fic\" ".
                                     bulle($msgRep_clkRep,"","CENTER","ABOVE",140).$der_fic."</a>";
                             $letype = $msgRep_dos;
                         }
                         else
                         {
                             $lien = "<a href=\"$path_fic\" target='_blank'".
                                     bulle($msgRep_clkFic,"","CENTER","ABOVE",140).$der_fic."</a>";
                             $letype = $msgRep_fic;
                         }
                         $affiche_rep .= couleur_tr($step1,"")."<td style=\"font-weight:bold;\">".$nom_grp."</td>".
                                         "<td>$letype</td>".
                                         "<td>$lien</td>".
                                         "<td>".$nom_auteur." ".$prenom_auteur." </td>".
                                         "<td>".reverse_date($dtComp,"-","/")."</td></tr>";
                     }
                }
           }
      }
  }
  if (!isset($step1) || $step1 == 0)
  {
     $affiche_rep .= "<div style=\"background-color: #F4F4F4; height:25px; padding-top: 4px;padding-left:4px;\">".$msgRep_Grpno."</div>";
  }
  $req_rep = mysql_query("select * from echange_grp where ech_grp_no = 0 order by ech_date_dt");
  $nbr_mess_rep = mysql_num_rows($req_rep);
  if ($nbr_mess_rep > 0)
  {
     while ($fic = mysql_fetch_object($req_rep))
     {
          $aut_fic = $fic->ech_auteur_no;
          $date_fic = $fic->ech_date_dt;
          $dtComp = substr($date_fic,0,10);
          $path_fic = $fic->ech_path_lb;
          $parent=dirname($path_fic);
          $der_fic = substr(strrchr($path_fic,"/"),1);
          if ($dtComp > $madate_compare)
          {
              $nom_auteur = GetDataField ($connect,"select util_nom_lb from utilisateur where util_cdn='$aut_fic'","util_nom_lb");
              $prenom_auteur = GetDataField ($connect,"select util_prenom_lb from utilisateur where util_cdn='$aut_fic'","util_prenom_lb");
              $step2++;
              if (!strstr($path_fic,'.'))
              {
                  $lien = "<a href=\"modif_rep_fic.php?formateurs=1&rallonge=$path_fic&sousdos=$path_fic&parent=$parent&dossier=$path_fic&direct=dossier&fichier=$der_fic\" ".
                          bulle($msgRep_clkRep,"","CENTER","ABOVE",140).$der_fic."</a>";
                  $letype = $msgRep_dos;
              }
              else
              {
                  $lien = "<a href=\"$path_fic\" target='_blank' ".
                          bulle($msgRep_clkFic,"","CENTER","ABOVE",140).$der_fic."</a>";
                  $letype = $msgRep_fic;
              }
              $affiche_rep .= couleur_tr($step1,"")."<td style=\"font-weight:bold;\">".
                               strtolower($msq_formateur)."s</td>".
                              "<td>$letype</td>".
                              "<td>$lien</td>".
                              "<td>".$nom_auteur." ".$prenom_auteur." </td>".
                              "<td>".reverse_date($dtComp,"-","/")."</td></tr>";
          }
     }
  }
  if ((isset($step1) && (!isset($step2) || $step2 == 0)))
     $affiche_rep .= "<tr><td colspan =5>";
  if (!isset($step2) || $step2 == 0)
      $affiche_rep .= "<div style=\"background-color: #FFFFFF; height:25px;font-size:11px;padding-top: 4px;padding-left:4px;\">".
      $msgRep_Frmno."</div></td></tr>";

  $step1 = 0;
  $step2 = 0;
echo utf2Charset($affiche_rep,$charset);
?>