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
include ('../include/UrlParam2PhpVar.inc.php');

require ("../admin.inc.php");
require ("../fonction.inc.php");
require ("../fonction_html.inc.php");
require ("../lang$lg.inc.php");
include ("../include/varGlobals.inc.php");
dbconnect();
$etat_chat = GetDataField($connect,"select param_etat_lb from param_foad where param_typ_lb='chat'","param_etat_lb");
$nom_user= $_SESSION['name_user'];
$prenom_user= $_SESSION['prename_user'];
$email= $_SESSION['email_user'];
$person=$nom_user;
$password=$prenom_user;
$date_jour=date("Y/m/d");
$verif_connex = mysql_query("SELECT distinct login from log where login !='$login' AND date_debut = '$date_jour' AND date_fin ='0000-00-00'");
$result = mysql_num_rows($verif_connex);
if ($result != 0)
{
   $i = 0;$autre_connecte ="";
   while ($i < $result)
   {
      $connecte = mysql_result($verif_connex,$i,"login");
      if ($i>0)
        $autre_connecte .=",";
      $autre_connecte .= $connecte;
   $i++;
   }
}
$le_titre = ($etat_chat == 'OUI') ? $mess_con_ok : $mess_whoisH ;
$nbr_connectes = $result;
$connecte= explode (",",$autre_connecte);
$i = 0;
$la_liste=array();
$affiche_cnx = "";$affiche1 = "";$droit = 0;
$affiche_cnx .= "<TABLE bgcolor='#FFFFFF' height='30' cellspacing='0' cellpadding='4' border=0>";
$est_passe = 0;
while ($i < $nbr_connectes)
{
          $liste=mysql_query("select util_cdn from utilisateur order by util_typutil_lb,util_nom_lb ASC");
          $nbr = mysql_num_rows($liste);
          $k = 0;
          while ($k < $nbr)
          {
             $num = mysql_result($liste,$k,"util_cdn");
             $log=GetDataField ($connect,"select util_cdn from utilisateur where util_login_lb='$connecte[$i]'","util_cdn");
             if ($num == $log)
                $droit++;
            $k++;
          }
          if ($droit == 0 && $i == 0)
          {
             if ($nbr_connectes == 1)
               $affiche_cnx .= "<TR><TD colspan='2' align='left' valign=top>".$nombre_connect1."</TD>";
             else
               $affiche_cnx .= "<TR><TD colspan='2' align='left' valign=top>".$nbr_connectes." ".$nombre_connect2."</TD></TR>";
             $affiche_cnx .= "<TR><TD colspan=2 align='left'>".$mess_con_no_aut."</TD></TR>";

          }
          elseif ($droit > 0)
          {
             if ($droit == 1 && $est_passe == 0 && $etat_chat == 'OUI')
             {
                $est_passe++;
                $lien="chat/index.php?user=$login&pass=$prenom_user";
                $lien = urlencode($lien);
                $affiche_cnx .= "<TR height='35'><TD colspan=2 valign='center' align='left'>".
                                "<A href=\"javascript:void(0);\" class='bouton_new' ".
                                "onClick=\"window.open('trace.php?link=$lien','','scrollbars=1,resizable=yes,width=400,height=305');\">".
                                $mess_ag_go_chat."</A></TD></TR>";
             }
             if ($i == 0)
             {
                if ($nbr_connectes == 1)
                   $affiche_cnx .= " <TR><TD colspan='2' align='left' valign=top>".$nombre_connect1."</TD>";
                else
                   $affiche_cnx .= " <TR><TD colspan='2' align='left' valign=top>".$nbr_connectes." ".$nombre_connect2."</TD>";
                $affiche_cnx .= " </TR>";
             }
          }
          $k = 0;
          $j = 0;
          while ($k < $nbr)
          {
              $num = mysql_result($liste,$k,"util_cdn");
              $log = GetDataField ($connect,"select util_cdn from utilisateur where util_login_lb='$connecte[$i]'","util_cdn");

              if ($num == $log)
              {
                 $compteur++;
                 couleur_tr($compteur,'');
                 $verif_chatteur = mysql_query("SELECT * from p4_salle where user='$connecte[$i]'");
                 if ($verif_chatteur == TRUE)
                    $result_chatteur = mysql_num_rows($verif_chatteur);
                 if (isset($result_chatteur) && $result_chatteur > 0)
                       $ajout = "<IMG SRC='images/chatteur.gif' border='0'>";
                 else
                       $ajout = "";
                 $login_connecte = $connecte[$i];
                 $ajt ="";
                 $reqConnect = mysql_query("select * from utilisateur where util_login_lb='".$connecte[$i]."'");
                 $itemConnect = mysql_fetch_object($reqConnect);
                 $ConnectID = $itemConnect->util_cdn;
                 $typ_connecte = $itemConnect->util_typutil_lb;
                 $nom_connecte= $itemConnect->util_nom_lb;
                 $prenom_connecte= $itemConnect->util_prenom_lb;
                 $photo =  $itemConnect->util_photo_lb;
                 $email =  $itemConnect->util_email_lb;
                 $SuiteBlog = '';
                 $suiteAnnuBlog = '';
                 if ($typ_connecte == 'APPRENANT')
                 {
                    $num_appre = $ConnectID;
                    $req_blog = mysql_query("SELECT * FROM blogbodies,blogshare where bgbody_auteur_no='$num_appre' and bgshr_auteur_no ='$num_appre'");
                    $nb_blog = mysql_num_rows($req_blog);
                    if ($nb_blog > 0)
                    {
                       if (mysql_result($req_blog,0,'bgshr_grp_no') == $numero_groupe)
                          $suiteAnnuBlog = "blog/blogOpen.php?numApp=$num_appre&id_clan=$num_appre";
                       elseif (mysql_result($req_blog,0,'bgshr_apps_on') == 1)
                              $suiteAnnuBlog = "blog/blogOpen.php?numApp=$num_appre&id_clan=$num_appre";
                       elseif (mysql_result($req_blog,0,'bgshr_all_on') == 1)
                              $suiteAnnuBlog = "blog/blogOpen.php?numApp=$num_appre&id_clan=$num_appre";
                    }
                    if ($suiteAnnuBlog != '' )
                    {
                       $lien_blog = $suiteAnnuBlog;
                       $lien_blog = urlencode($lien_blog);
                       $SuiteBlog = "<span style=\"padding:4px;margin-left:4px;border:1px solid maroon;background:#eee;\">".
                            "<A HREF=\"trace.php?link=$lien_blog\" target='_blank'>Son Blog</A></span>";
                    }
                    else
                       $SuiteBlog = nbsp(21);

                 }
                 else
                    $SuiteBlog = nbsp(15);
                 if ($photo != "")
                 {
                    list($w_img, $h_img, $type_img, $attr_img) = getimagesize("../images/$photo");
                    $ajt = " onMouseOver=\"overlib('', TEXTCOLOR, '#ffffff', TEXTSIZE, 2, ABOVE, WIDTH, $w_img, HEIGHT, $h_img, BACKGROUND, 'images/$photo', PADX, 60, 20, PADY, 20, 20)\" onMouseOut=\"nd()\"";
                 }
                 $lien_mess = "message_instant.php?num=$num";
                 $lien_mess= urlencode($lien_mess);
                 $ajout2 = "<A HREF=\"javascript:void(0);\" ".
                           " onClick=\"open('trace.php?link=$lien_mess','window','scrollbars=no,resizable=yes,width=500,height=130,left=300,top=300')\" "
                           .bulle("Envoyer un message instantané","","LEFT","ABOVE",180);

                 $ajout2 .= "<img src='images/ecran-annonce/icoMsgInst.gif' border=0></a>";
                 $lien="chat/index.php?user=$login&pass=$prenom_user&effet=1&parler=$login_connecte&appelant=$login";
                 $lien = urlencode($lien);
                 if ($etat_chat == 'OUI')
                 {
                    $affiche_cnx .= couleur_tr($compteur,"");
                    $affiche_cnx .= "<td valign='middle' align='left'>".
                                    "<div id='chat$compteur' class='sequence' style=\"cursor: pointer;padding-right: 25px;\" ".
                                    "onClick=\"window.open('trace.php?link=$lien','','scrollbars=1,resizable=yes,width=400,height=305');\" ";
                    $affiche_cnx .= "$ajt>".$prenom_connecte." ". $nom_connecte."$ajout</div></td>";
                 }
                 else
                 {
                    $affiche_cnx .= couleur_tr($compteur,"");
                    $affiche_cnx .= "<td valign='middle' align='left'>".
                                    "<div id='conx$compteur' class='sequence' style=\"cursor: help;\" ";
                    $affiche_cnx .= "$ajt>".$prenom_connecte." ".$nom_connecte."</div></td>";
                 }
                 $affiche_cnx .= "<td align=left valign='middle'>".$ajout2."&nbsp; $SuiteBlog &nbsp;&nbsp;".$typ_connecte."</td></tr>";
            }
          $k++;
          }
   $i++;
}
if (!$result)
   $affiche_cnx .= "<TR><TD  align='middle'><P>".($mess_no_connect)."<P></TD>";
$affiche_cnx .= "</TR></TABLE>";
   echo utf2Charset($affiche_cnx,$charset);
?>
