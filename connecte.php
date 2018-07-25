<?php
if (!isset($_SESSION)) session_start();
include ("include/UrlParam2PhpVar.inc.php");
if (!isset($_SESSION['id_user']) || $_SESSION['id_user'] == "")
{
  exit();
}
require "admin.inc.php";
require 'fonction.inc.php';
require 'fonction_html.inc.php';
require "lang$lg.inc.php";
//include ("click_droit.txt");
dbConnect();
include 'style.inc.php';
//de quel type est l'utilisateur (apprenant, formateur, administrateur)
$nom_user=GetDataField ($connect,"select util_nom_lb from utilisateur where util_cdn = $id_user","util_nom_lb");
$prenom_user=GetDataField ($connect,"select util_prenom_lb from utilisateur where util_cdn = $id_user","util_prenom_lb");
$email=GetDataField ($connect,"select util_email_lb from utilisateur where util_cdn = $id_user","util_email_lb");
$person=$nom_user;
$password=$prenom_user;
$date_jour=date("Y/m/d");
$verif_connex = mysql_query("SELECT distinct login from log where login !='$login' AND date_debut = '$date_jour' AND date_fin ='0000-00-00'");
$result = mysql_num_rows($verif_connex);
if ($result != 0){
   $i = 0;$autre_connecte ="";
   while ($i < $result){
      $connecte = mysql_result($verif_connex,$i,"login");
      if ($i>0)
        $autre_connecte .=",";
      $autre_connecte .=$connecte;
   $i++;
   }
}
entete_simple($mess_con_ok);
echo "<TR><TD width=100%><TABLE width='100%' height='60' cellspacing='0' cellpadding='4'>";
$nbr_connectes = $result;
$connecte= explode (",",$autre_connecte);
$i = 0;
$la_liste=array();
$affiche = "";$affiche1 = "";
  while ($i < $nbr_connectes)
  {
         if ($typ_user == "ADMINISTRATEUR" || $typ_user == "RESPONSABLE_FORMATION" || $typ_user == "FORMATEUR_REFERENT")
           $liste=mysql_query("select util_cdn from utilisateur order by util_typutil_lb,util_nom_lb ASC");
         if ($typ_user == "APPRENANT")
         {
           $req_grp = mysql_query("select utilgr_groupe_no from utilisateur_groupe where
                                   utilgr_utilisateur_no = $id_user and
                                   utilgr_groupe_no=$numero_groupe");
           $nomb_grp = mysql_num_rows($req_grp);
           if ($nomb_grp != 0)
           {
             $num_grp = mysql_result($req_grp,0,"utilgr_groupe_no");
             $message="";
           }
           else
           {
             $message = "$mess_con_noaffect";
             echo "<TR><TD colspan='2'>$message";
             echo "</TD></TR></TABLE></TD></TR></TABLE></TD></TR></TABLE>";
           exit;
           }
           $log = GetDataField ($connect,"select util_cdn from utilisateur where util_login_lb='$connecte[$i]'","util_cdn");
           $compteur = 0;
           $req_grp = mysql_query("select util_cdn from utilisateur,utilisateur_groupe where
                                   utilisateur_groupe.utilgr_utilisateur_no = util_cdn AND
                                   utilisateur_groupe.utilgr_groupe_no = $numero_groupe");
           $app_grp = mysql_num_rows($req_grp);
           $req_presc = mysql_query("select distinct utilisateur.util_cdn from utilisateur,prescription_$numero_groupe where
                                      prescription_$numero_groupe.presc_grp_no = $numero_groupe AND
                                      (utilisateur.util_cdn = prescription_$numero_groupe.presc_prescripteur_no OR
                                      utilisateur.util_cdn = prescription_$numero_groupe.presc_formateur_no) AND
                                      prescription_$numero_groupe.presc_utilisateur_no = $id_user");
           $presc_form = mysql_num_rows($req_presc);
           $req_tut = mysql_query("select tut_tuteur_no from tuteur where tut_apprenant_no = $id_user");
           $tut_app = mysql_num_rows($req_tut);
           if ($app_grp > 0)
           {
              while ($item = mysql_fetch_object($req_grp))
              {
                 $id_util = $item->util_cdn;
                 if ($id_util == $log)
                 {
                   $droit++;
                   $verif_chatteur = mysql_query("SELECT * from p4_salle where user='$connecte[$i]'");
                   $result_chatteur = ($verif_chatteur == TRUE) ? mysql_num_rows($verif_chatteur) : 0;
                   if ($result_chatteur > 0)
                       $ajout = "<IMG SRC='images/chatteur.gif' border='0'>";
                   else
                       $ajout = "";
                   $login_connecte = $connecte[$i];
                   $typ_connecte=GetDataField ($connect,"select util_typutil_lb from utilisateur where util_login_lb='$connecte[$i]'","util_typutil_lb");
                   $nom_connecte=GetDataField ($connect,"select util_nom_lb from utilisateur where util_login_lb='$connecte[$i]'","util_nom_lb");
                   $prenom_connecte=GetDataField ($connect,"select util_prenom_lb from utilisateur where util_login_lb='$connecte[$i]'","util_prenom_lb");
                   $photo = GetDataField ($connect,"select util_photo_lb from utilisateur where util_login_lb = '$connecte[$i]'","util_photo_lb");
                   $email = GetDataField ($connect,"select util_email_lb from utilisateur where util_login_lb = '$connecte[$i]'","util_email_lb");
                   $lien="chat/index.php?user=$login&pass=$prenom_user&effet=1&parler=$login_connecte&appelant=$login";
                   $lien = urlencode($lien);
                   $affiche .= "<TR><TD valign='middle' align='left' bgcolor='#DEE3E7'><DIV id='sequence'><A href=\"trace.php?link=$lien\"";
                   if ($photo != "")
                   {
                     list($w_img, $h_img, $type_img, $attr_img) = getimagesize("images/$photo");
                     $affiche .= " onMouseOver=\"overlib('', TEXTCOLOR, '#ffffff', TEXTSIZE, 2, WIDTH, $w_img, HEIGHT, $h_img, BACKGROUND, 'images/$photo', PADX, 60, 20, PADY, 20, 20)\" onMouseOut=\"nd()\"";
                   }
                   $affiche .= ">$prenom_connecte $nom_connecte</A>$ajout</DIV></TD><TD align=left bgcolor='#EFEFEF'>$typ_connecte</TD>";
                 }
              }
           }
           if ($presc_form > 0)
           {
              while ($item = mysql_fetch_object($req_presc))
              {
                 $id_util = $item->util_cdn;
                 if ($id_util == $log)
                 {
                   $droit++;
                   $verif_chatteur = mysql_query("SELECT * from p4_salle where user='$connecte[$i]'");
                   $result_chatteur = mysql_num_rows($verif_chatteur);
                   if ($result_chatteur > 0)
                       $ajout = "<IMG SRC='images/chatteur.gif' border='0'>";
                   else
                       $ajout = "";
                   $login_connecte = $connecte[$i];
                   $typ_connecte=GetDataField ($connect,"select util_typutil_lb from utilisateur where util_login_lb='$connecte[$i]'","util_typutil_lb");
                   $nom_connecte=GetDataField ($connect,"select util_nom_lb from utilisateur where util_login_lb='$connecte[$i]'","util_nom_lb");
                   $prenom_connecte=GetDataField ($connect,"select util_prenom_lb from utilisateur where util_login_lb='$connecte[$i]'","util_prenom_lb");
                   $photo = GetDataField ($connect,"select util_photo_lb from utilisateur where util_login_lb = '$connecte[$i]'","util_photo_lb");
                   $email = GetDataField ($connect,"select util_email_lb from utilisateur where util_login_lb = '$connecte[$i]'","util_email_lb");
                   $lien="chat/index.php?user=$login&pass=$prenom_user&effet=1&parler=$login_connecte&appelant=$login";
                   $lien = urlencode($lien);
                   $affiche .= "<TR><TD valign='middle' align='left' bgcolor='#DEE3E7'><DIV id='sequence'><A href=\"trace.php?link=$lien\"";
                   if ($photo != ""){
                     list($w_img, $h_img, $type_img, $attr_img) = getimagesize("images/$photo");
                     $affiche .= " onMouseOver=\"overlib('', TEXTCOLOR, '#ffffff', TEXTSIZE, 2, WIDTH, $w_img, HEIGHT, $h_img, BACKGROUND, 'images/$photo', PADX, 60, 20, PADY, 20, 20)\" onMouseOut=\"nd()\"";
                   }
                   $affiche .= ">$prenom_connecte $nom_connecte</A>$ajout</DIV></TD><TD align=left bgcolor='#EFEFEF'>$typ_connecte</TD>";
                 }
              }
           }
           if ($tut_app > 0)
           {
              while ($item = mysql_fetch_object($req_tut))
              {
                 $id_util = $item->util_cdn;
                 if ($id_util == $log)
                 {
                   $droit++;
                   $verif_chatteur = mysql_query("SELECT * from p4_salle where user='$connecte[$i]'");
                   $result_chatteur = mysql_num_rows($verif_chatteur);
                   if ($result_chatteur > 0)
                       $ajout = "<IMG SRC='images/chatteur.gif' border='0'>";
                   else
                       $ajout = "";
                   $login_connecte = $connecte[$i];
                   $typ_connecte=GetDataField ($connect,"select util_typutil_lb from utilisateur where util_login_lb='$connecte[$i]'","util_typutil_lb");
                   $nom_connecte=GetDataField ($connect,"select util_nom_lb from utilisateur where util_login_lb='$connecte[$i]'","util_nom_lb");
                   $prenom_connecte=GetDataField ($connect,"select util_prenom_lb from utilisateur where util_login_lb='$connecte[$i]'","util_prenom_lb");
                   $photo = GetDataField ($connect,"select util_photo_lb from utilisateur where util_login_lb = '$connecte[$i]'","util_photo_lb");
                   $email = GetDataField ($connect,"select util_email_lb from utilisateur where util_login_lb = '$connecte[$i]'","util_email_lb");
                   $lien="chat/index.php?user=$login&pass=$prenom_user&effet=1&parler=$login_connecte&appelant=$login";
                   $lien = urlencode($lien);
                   $affiche .= "<TR><TD valign='middle' align='left' bgcolor='#DEE3E7'><DIV id='sequence'><A href=\"trace.php?link=$lien\"";
                   if ($photo != "")
                   {
                     list($w_img, $h_img, $type_img, $attr_img) = getimagesize("images/$photo");
                     $affiche .= " onMouseOver=\"overlib('', TEXTCOLOR, '#ffffff', TEXTSIZE, 2, WIDTH, $w_img, HEIGHT, $h_img, BACKGROUND, 'images/$photo', PADX, 60, 20, PADY, 20, 20)\" onMouseOut=\"nd()\"";
                   }
                   $affiche .= ">$prenom_connecte $nom_connecte</A>$ajout</DIV></TD><TD align=left bgcolor='#EFEFEF'>$typ_connecte</TD>";
                 }
              }
           }
           if (($tut_app > 0 || $presc_form > 0 || $app_grp > 0) && $i == $nbr_connectes-1)
           {
              if ($droit == 0)
              {
                 if ($nbr_connectes == 1)
                    $affiche1 .= "<TR><TD colspan='2' align='left' valign=top><BR>&nbsp;$nombre_connect1</TD>";
                 else
                    $affiche1 .= "<TR><TD colspan='2' align='left' valign=top><BR>&nbsp;$nbr_connectes $nombre_connect2</TD>";
                 $affiche1 .= " </TR>";
                 $affiche1 .= "<TR><TD colspan=2 align='left'>$mess_con_no_aut<BR>&nbsp;</TD></TR></TABLE></TD></TR>".
                      "<TR><TD colspan=2 align='left' width=100%><TABLE cellpadding='6' cellspacing=0 width=100%>";
              }
              elseif ($droit > 0)
              {
                 $lien="chat/index.php?user=$login&pass=$prenom_user";
                 $lien = urlencode($lien);
                 $affiche1 .= "<TR ><TD colspan=2 valign='center' align='left'>".
                              "<A href=\"trace.php?link=$lien\" class='bouton_new'>$mess_ag_go_chat</A></TD></TR>";
                 if ($nbr_connectes == 1)
                    $affiche1 .= " <TR><TD colspan='2' align='left' valign=top>$nombre_connect1</TD>";
                 else
                    $affiche1 .= " <TR><TD colspan='2' align='left' valign=top>$nbr_connectes $nombre_connect2</TD>";
                 $affiche1 .= " </TR>";
                 $affiche1 .= "<TR><TD colspan=2 align='left'>&nbsp;$mess_con_aut</TD></TR></TABLE></TD></TR>";
                 $affiche1 .= "<TR><TD colspan=2 align='left' width=100%><TABLE cellpadding='6' cellspacing=1 width=100%>";
              }
           }
         }
         else
         {
          /*if ($typ_user == "FORMATEUR_REFERENT")
            $liste=mysql_query("select util_cdn from utilisateur,prescription where
                                (utilisateur.util_cdn = prescription.presc_utilisateur_no and
                                prescription.presc_formateur_no = $id_user)  or
                                (utilisateur.util_typutil_lb != 'APPRENANT')
                                group by util_cdn
                                order by util_typutil_lb,util_nom_lb ASC");
                                */
          if ($typ_user == "TUTEUR")
            $liste=mysql_query("select util_cdn from utilisateur,tuteur where
                                (utilisateur.util_cdn = tuteur.tut_apprenant_no and
                                tuteur.tut_tuteur_no = $id_user)  or
                                (utilisateur.util_typutil_lb != 'APPRENANT')
                                group by utilisateur.util_cdn
                                order by util_typutil_lb,util_nom_lb ASC");
          $nbr = mysql_num_rows($liste);
          $k = 0;$droit = 0;
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
               echo "<TR><TD colspan='2' align='left' valign=top>$nombre_connect1</TD>";
             else
               echo "<TR><TD colspan='2' align='left' valign=top>$nbr_connectes $nombre_connect2</TD>";
             echo " </TR>";
             echo "<TR><TD colspan=2 align='left'>$mess_con_no_aut</TD></TR></TABLE></TD></TR>".
                  "<TR><TD colspan=2 align='left' width=100%><TABLE cellpadding='6' cellspacing=0 width=100%>";
          }
          elseif ($droit > 0)
          {
             if ($droit == 1 && $est_passe == 0 || !isset($est_passe))
             {
                $est_passe++;
                $lien="chat/index.php?user=$login&pass=$prenom_user";
                $lien = urlencode($lien);
                echo "<TR height='35'><TD colspan=2 valign='center' align='left'><A href=\"trace.php?link=$lien\" class='bouton_new'>$mess_ag_go_chat</A></TD></TR>";
             }
             if ($i == 0)
             {
                if ($nbr_connectes == 1)
                   echo " <TR><TD colspan='2' align='left' valign=top>$nombre_connect1</TD>";
             }
             else
                echo " <TR><TD colspan='2' align='left' valign=top>$nbr_connectes $nombre_connect2</TD>";
            echo " </TR>";
            echo "<TR><TD colspan=2 align='left'>&nbsp;$mess_con_aut<BR>&nbsp;</TD></TR></TABLE></TD></TR>".
                 "<TR><TD colspan=2 align='left' width=100%><TABLE cellpadding='6' cellspacing=1 width=100%>";
          }
          $k = 0;
          $j = 0;
          while ($k < $nbr)
          {
              if ($typ_user != "APPRENANT")
                 $num = mysql_result($liste,$k,"util_cdn");
              else
              $log=GetDataField ($connect,"select util_cdn from utilisateur where util_login_lb='$connecte[$i]'","util_cdn");
              if ($num == $log)
              {
                 $verif_chatteur = mysql_query("SELECT * from p4_salle where user='$connecte[$i]'");
                 $result_chatteur = mysql_num_rows($verif_chatteur);
                 if ($result_chatteur > 0)
                       $ajout = "<IMG SRC='images/chatteur.gif' border='0'>";
                 else
                       $ajout = "";
                 $lien_mess = "message_instant.php?num=$num";
                 $lien_mess= urlencode($lien_mess);
                 $ajout2 = "<A HREF=\"javascript:void(0);\" ".
                           " onClick=\"open('trace.php?link=$lien_mess','window','scrollbars=no,resizable=yes,width=500,height=130,left=300,top=300')\" "
                           .bulle("Envoyer un message instantané","","LEFT","ABOVE",180);

                 $ajout2 .= "<img src='images/ecran-annonce/icoMsgInst.gif' border=0></a>";
                 $login_connecte = $connecte[$i];
                 $typ_connecte=GetDataField ($connect,"select util_typutil_lb from utilisateur where util_login_lb='$connecte[$i]'","util_typutil_lb");
                 $nom_connecte=GetDataField ($connect,"select util_nom_lb from utilisateur where util_login_lb='$connecte[$i]'","util_nom_lb");
                 $prenom_connecte=GetDataField ($connect,"select util_prenom_lb from utilisateur where util_login_lb='$connecte[$i]'","util_prenom_lb");
                 $photo = GetDataField ($connect,"select util_photo_lb from utilisateur where util_login_lb = '$connecte[$i]'","util_photo_lb");
                 $email = GetDataField ($connect,"select util_email_lb from utilisateur where util_login_lb = '$connecte[$i]'","util_email_lb");
                 $lien="chat/index.php?user=$login&pass=$prenom_user&effet=1&parler=$login_connecte&appelant=$login";
                 $lien = urlencode($lien);
                 echo "<TR><TD valign='middle' align='left' bgcolor='#DEE3E7'><DIV id='sequence'><A href=\"trace.php?link=$lien\"";
                 if ($photo != "")
                 {
                   list($w_img, $h_img, $type_img, $attr_img) = getimagesize("images/$photo");
                   echo " onMouseOver=\"overlib('', TEXTCOLOR, '#ffffff', TEXTSIZE, 2, WIDTH, $w_img, HEIGHT, $h_img, BACKGROUND, 'images/$photo', PADX, 60, 20, PADY, 20, 20)\" onMouseOut=\"nd()\"";
                 }
                 echo ">$prenom_connecte $nom_connecte</A>$ajout&nbsp; &nbsp;&nbsp; $ajout2</DIV></TD>".
                       "<TD align=left bgcolor='#EFEFEF'>$typ_connecte</TD>";
             }
          $k++;
          }
         }
      }
   $i++;
  }
  if ($typ_user == "APPRENANT")
  {
    echo $affiche1;
    echo $affiche;
  }
  if (!$result)
    echo "<TR><TD  align='middle'><FONT SIZE='2'>&nbsp;<P><B>$mess_no_connect</B><P>&nbsp;</FONT></TD>";
  echo "</TR></TABLE>";
  echo "</TD></TR></TABLE></TD></TR></TABLE>";
  echo "</CENTER></BODY></HTML>";
?>
