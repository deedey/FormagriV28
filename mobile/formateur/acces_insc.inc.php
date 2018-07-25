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
require ("../langfr.inc.php");
require ("../langues/accueil.inc.php");
dbconnect();
include ("../../include/varGlobals.inc.php");
$date_op = date("Y-m-d H:i:s" ,time());
$date_fiche = substr($date_op,0,10);
$der_connex = mysql_query("SELECT * from log WHERE login ='$login' AND date_fin != '0000-00-00' order by log_cdn desc");
$res = mysql_num_rows($der_connex);
if ($res > 0){
   $date_tab = mysql_result($der_connex,0,"date_fin");
}else
   $date_tab = $date_fiche;
$req = mysql_query("select * from utilisateur where util_date_dt != '0000-00-00 00:00:00' order by util_typutil_lb desc,util_date_dt,util_nom_lb");
$nb_req = mysql_num_rows($req);
$step2 = 0;
if ($nb_req > 0)
{
    while ($item = mysql_fetch_object($req))
    {
       $num_insc = $item->util_cdn;
       $nom_insc = $item->util_nom_lb;
       $prenom_insc = $item->util_prenom_lb;
       $date_insc = $item->util_date_dt;
       $auteur_insc = $item->util_auteur_no;
       $type_insc = strtolower($item->util_typutil_lb);
       $dtComp = substr($date_insc,0,10);

       if ($dtComp > $date_tab)
       {
          $step2++;
          $nom_inscripteur = GetDataField ($connect,"select util_nom_lb from utilisateur where util_cdn='$auteur_insc'","util_nom_lb");
          $prenom_inscripteur = GetDataField ($connect,"select util_prenom_lb from utilisateur where util_cdn='$auteur_insc'","util_prenom_lb");
          if ($step2 == 1)
               $affiche_insc .= '<center><table cellpadding="4" cellspacing="1" border=0 width="98%">'.
                                '<tr><td class="barre_titre">'.$mess_auth_util.'</td>'.
                                '<td class="barre_titre">'.$msgins_aut.'</td>'.
                                '<td class="barre_titre">'.$mess_admin_role.'</td>'.
                                '<td class="barre_titre">'.$mess_mess_date.'</td></tr>';
          $lien = "prescription.php?affiche_fiche_app=1&id_util=$num_insc&identite=1&stopper=1";
          $affiche_insc .= couleur_tr($step2,"")."<td><a href=\"javascript:void(0);\" ".
                           "onClick=\"window.open('$lien','','scrollbars=yes,resizable=yes,width=600,height=400');\">".
                            $nom_insc." ".$prenom_insc."</a></td>".
                           "<td>".$nom_inscripteur." ".$prenom_inscripteur."</td>".
                           "<td>".$type_insc."</td>".
                           "<td>".reverse_date($dtComp,"-","/")."</td></tr>";
       }
    }
    if (isset($step2) && $step2 > 0)
       $affiche_insc .= "</table></center>";
}
if (!isset($step2) || (isset($step2) && $step2 == 0))
   $affiche_insc = $msgins_no;
$step2 = 0;
echo utf2Charset($affiche_insc,$charset);
?>
