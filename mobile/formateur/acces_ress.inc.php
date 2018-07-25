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
include ("../../include/varGlobals.inc.php");
?>
<style>
.all{
     font-size:12px;
}
.barre_titre{
     background-color: #2b677a;
     color: #FFF;
     font-family: arial;
     font-size: 12px;
     padding-left: 3px;
     padding-top: 3px;
     padding-bottom: 3px;

}
.sous_titre {
     text-align:left;
     font-family:arial;
     background-color: #D4E7ED; /*#F6E7D4;*/
     padding:4px;
     font-size:12px;
     color:#333333;
     border:1px solid #24677A;
}
</style>
<?php
$id_user=$_SESSION['IDUSER'];
$charset='iso-8859-1';
$date_op = date("Y-m-d H:i:s" ,time());
$date_fiche = substr($date_op,0,10);
$der_connex = mysql_query("SELECT * from log WHERE login ='$login' AND date_fin != '0000-00-00' order by log_cdn desc");
$res = mysql_num_rows($der_connex);
if ($res > 0){
   $date_tab = mysql_result($der_connex,0,"date_fin");
}else
   $date_tab = '2007-01-01';
$req_ress = mysql_query("select * from ressource_new where ress_create_dt > '2007-01-01' and ress_create_dt > \"$date_tab\" order by ress_create_dt,ress_ajout,ress_titre");
$nb_req = mysql_num_rows($req_ress);
if ($nb_req > 0)
{
    while ($item = mysql_fetch_object($req_ress))
    {
       $num_ress = $item->ress_cdn;
       $titre_ress = $item->ress_titre;
       $url_ress = $item->ress_url_lb;
       $pere_ress = $item->ress_typress_no;
       $cat_ress = $item->ress_cat_lb;
       $date_ress = $item->ress_create_dt;
       $auteur_ress = $item->ress_ajout;
       if ($date_ress > $date_tab)
       {
          $step2++;
          if (strtolower($auteur_ress) != 'foad' && $auteur_ress != '')
          {
              $nom_auteur = GetDataField ($connect,"select util_nom_lb from utilisateur where util_login_lb='$auteur_ress'","util_nom_lb");
              $prenom_auteur = GetDataField ($connect,"select util_prenom_lb from utilisateur where util_login_lb='$auteur_ress'","util_prenom_lb");
              $majuscule = $nom_auteur." ".$prenom_auteur;
          }
          else
          {
              $majuscule = $mess_gen_gen_formagri." ".$mess_gen_formagri;
          }
          if ($step2 == 1)
               $affiche_ress .= '<table cellpadding="4" cellspacing="1" border=0 width="96%" align=left>'.
                                '<tr><td colspan="4" class="sous_titre">'.$nb_req.' '.$msgnew_ins.'</td></tr>'.
                                '<tr style="font-weight:normal !important;"><td class="barre_titre">'.$msq_titre.'</td>'.
                                '<td class="barre_titre">'.$msgress_aut.'</td>'.
                                '<td class="barre_titre">'.$mrc_cat.'</td>'.
                                '<td class="barre_titre">'.$mess_mess_date.'</td></tr>';
          if ($titre_ress == "" && $url_ress == "")
          {
             $affiche_ress .= couleur_tr($step2,"")."<td class='all'>$msgress_cat</td>";
             $affiche_ress .= "<td class='all'>$majuscule</td>".
                              "<td class='all'>$cat_ress</td>".
                              "<td class='all'>".reverse_date($date_ress,"-","/")."</td></tr>";
          }
          else
          {
             if ($titre_ress != "" && $url_ress != "" && strstr($url_ress,'http') && !strstr($url_ress,'forum/read'))
             {
                $lien = $url_ress;
                $trgt = '_blank';
             }
             elseif ($titre_ress != "" && $url_ress != "" && strstr($url_ress,'ressources/') && !strstr($url_ress,'http') && !strstr($url_ress,'forum/read'))
             {
                $lien = $adresse_http.'/'.$url_ress;
                $trgt = '_blank';
             }
             elseif ($titre_ress != "" && $url_ress != "" && !strstr($url_ress,'ressources/') && strstr($url_ress,'forum/read'))
             {
                $lien = $url_ress;
                $trgt = 'main';
             }
             $affiche_ress .= couleur_tr($step2,"")."<td class='all'>$titre_ress</td>";
             $affiche_ress .= "<td class='all'>$majuscule</td>".
                              "<td class='all'>$cat_ress</td>".
                              "<td class='all'>".reverse_date($date_ress,"-","/")."</td></tr>";
          }
       }
    }
    if (isset($step2) && $step2 > 0)
       $affiche_ress .= "</table></center>";
}
if (!isset($step2) || (isset($step2) && $step2 == 0))
    $affiche_ress = "<div class='all'>$msgress_no</div>";
$step2 = 0;
echo utf2Charset($affiche_ress,$charset);
?>
