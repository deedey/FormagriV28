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
$nom_util=GetDataField ($connect,"select util_nom_lb from utilisateur WHERE util_cdn = '$utilisateur'","util_nom_lb");
$prenom_util=GetDataField ($connect,"select util_prenom_lb from utilisateur WHERE util_cdn = '$utilisateur'","util_prenom_lb");
$email_util=GetDataField ($connect,"select util_email_lb from utilisateur WHERE util_cdn = '$utilisateur'","util_email_lb");
$majuscule = $prenom_util." ".$nom_util;
entete_simple("$mess_ajt_frm");
$nom_grp = GetDataField ($connect,"select grp_nom_lb from groupe where grp_cdn=$id_grp","grp_nom_lb");
echo "<tr><td><table cellspacing='1' cellpadding='3' width='100%'>";
echo "<tr><td colspan='4'class='sous_titre'><strong>$mess_typ_app</strong> : $nom_util $prenom_util<br /><strong>$mess_gp_nom_grp</strong> : $nom_grp<br /><strong>$mess_nb_msg</strong> : $NbrPost</td></tr>";
echo "<tr bgcolor=\"#2B677A\">";
echo "<td style=\"color: #FFFFFF;width: 15%;font-weight: bold;\">$mess_mess_date et $heur</td>";
echo "<td style=\"color: #FFFFFF;width: 25%;font-weight: bold;\">$mess_mail_sujet</td>";
echo "<td style=\"color: #FFFFFF;width: 45%;font-weight: bold;\">$mess_mail_mess</td>";
$nom_forum = GetDataField ($connect,"select table_name from forums where name='$nom_grp'","table_name");
$req_forum = mysql_query("SELECT * from $nom_forum where author = \"$majuscule\" AND email = \"$email_util\" AND approved='Y' order by datestamp desc");
if ($req_forum == TRUE){
   while ($item = mysql_fetch_object($req_forum)) {
         $i++;
         $id_post = $item->id;
         $date_post = $item->datestamp;
         $sujetPost = $item->subject;
         $bodPost = GetDataField ($connect,"select body from $nom_forum"."_bodies WHERE id = '$id_post'","body");
         $bodyPost = (strlen($bodPost > 60)) ? substr($bodPost,0,60) : $bodPost;
         $datPost = substr($date_post,0,10);
         $ch_date = explode ("-",$datPost);
         $datPost = "$ch_date[2]/$ch_date[1]/$ch_date[0]";
         $heurPost = substr($date_post,11);
         $affiche .= couleur_tr($i,'');
         $affiche .= "<td valign='top'>$datPost";
         $affiche .= "<br>$heurPost</td>";
         $affiche .= "<td valign='top'>$sujetPost</td>";
         $affiche .= "<td valign='top'>$bodyPost</td>";
         $affiche .= "</tr>";
   }
   echo $affiche;
   echo fin_tableau('');
}
exit();
?>