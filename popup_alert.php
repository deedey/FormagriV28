<?php
if (!isset($_SESSION)) session_start();
include ("include/UrlParam2PhpVar.inc.php");
if (!isset($_SESSION['id_user']) || $_SESSION['id_user'] == "")
{
  exit();
}
require 'admin.inc.php';
require 'fonction.inc.php';
require 'fonction_html.inc.php';
require "lang$lg.inc.php";
dbConnect();
include 'style.inc.php';
//NbrPost=$nbr_posts&id_forum=$id_forum&utilisateur=$id_app&id_grp=$id_grp
entete_simple("$mess_alert");
$nom_grp = GetDataField ($connect,"select grp_nom_lb from groupe where grp_cdn=$id_grp","grp_nom_lb");
$req_msg = mysql_query("SELECT * from message where
                        ((msg_dhdeb_dt < '$dater' OR msg_dhdeb_dt = '$dater') AND
                        (msg_dhfin_dt = '$dater' OR msg_dhfin_dt > '$dater')) AND
                        ((msg_apprenant_no = $id_user AND msg_groupe_no = $id_grp) OR (msg_groupe_no = $id_grp and msg_apprenant_no = 0))");
$nb_msg = mysql_num_rows($req_msg);
echo "<tr><td><table cellspacing='1' cellpadding='3' width='100%'>";
echo "<tr><td colspan='4'class='sous_titre'><strong>$mess_gp_nom_grp</strong> : $nom_grp<br /><strong>$mess_nb_msg</strong> : $nb_msg</td></tr>";
echo "<tr bgcolor=\"#2B677A\">";
echo "<td style=\"color: #FFFFFF;width: 60%;font-weight: bold;\">$mess_mail_mess</td>";
echo "<td style=\"color: #FFFFFF;width: 15%;font-weight: bold;\">$mess_mail_dest</td>";
echo "<td style=\"color: #FFFFFF;width: 25%;font-weight: bold;\">$mrc_aut</td>";
if ($nb_msg > 0)
{
   while ($item = mysql_fetch_object($req_msg))
   {
         $i++;
          $id_msg = $item->msg_cdn;
          $contenu = html_entity_decode($item->msg_contenu_cmt,ENT_QUOTES,'iso-8859-1');
          $auteur = $item->msg_auteur_no;
          $app = $item->msg_apprenant_no;
          $affiche .= couleur_tr($i,'');
          $affiche .= "<td valign='top'>$contenu</td>";
          if ($app == 0)
             $affiche .= "<td valign='top'>$mess_fav_grp</td>";
          else
             $affiche .= "<td valign='top'>$mess_fav_pers</td>";
          $lien = "prescription.php?affiche_fiche_app=1&identite=1&id_util=$auteur&stopper=1";
          $lien = urlencode($lien);
          $affiche .= "<td nowrap valign='top'><div id='sequence'><A HREF=\"javascript:void(0);\" onclick=\"window.open('trace.php?link=$lien','','width=600,height=380,resizable=yes,status=no')\"";
          $nom_util=GetDataField ($connect,"select util_nom_lb from utilisateur WHERE util_cdn = '$auteur'","util_nom_lb");
          $prenom_util=GetDataField ($connect,"select util_prenom_lb from utilisateur WHERE util_cdn = '$auteur'","util_prenom_lb");
          $photo = GetDataField ($connect,"select util_photo_lb from utilisateur WHERE util_cdn = '$auteur'","util_photo_lb");
          $majuscule = $nom_util." ".$prenom_util;
          if ($photo != ""){
             list($w_img, $h_img, $type_img, $attr_img) = getimagesize("images/$photo");
             $affiche .= " onMouseOver=\"overlib('', TEXTCOLOR, '#ffffff', TEXTSIZE, 2, WIDTH, $w_img, HEIGHT, $h_img, BACKGROUND, 'images/$photo',ABOVE,LEFT, PADX, 60, 20, PADY, 20, 20)\" onMouseOut=\"nd()\"";
          }else
             $affiche .=  bulle($mess_suite_fp,"","CENTER","",120);
          $affiche .="<strong> $majuscule</strong></A></div></td>";
          $affiche .= "</tr>";
   }
   echo $affiche;
   echo fin_tableau('');
}
?>