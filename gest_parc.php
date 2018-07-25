<?php
session_start();
include ("include/UrlParam2PhpVar.inc.php");
if (!isset($_SESSION['id_user']) || $_SESSION['id_user'] == "")
{
  exit();
}
require 'fonction.inc.php';
require 'fonction_html.inc.php';
require "lang$lg.inc.php";
require "graphique/admin.inc.php";
require 'langues/prescription.inc.php';
require 'langues/formation.inc.php';
require 'langues/ress.inc.php';
dbConnect();
$Ext="_$numero_groupe";
$nom_user = $_SESSION['name_user'];
$prenom_user = $_SESSION['prename_user'];
$email_user = $_SESSION['email_user'];
$lien_retour = "http://".$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
$date_op = date("Y-m-d H:i:s" ,time());
$heure_fiche = substr($date_op,11);
$date_fiche = substr($date_op,0,10);
$nom_grp = GetDataField ($connect,"select grp_nom_lb from groupe where grp_cdn  = $numero_groupe","grp_nom_lb");
$carac_grp = strlen($nom_grp);
if ($carac_grp > 20)
   $nom_grp1 = substr($nom_grp,0,20)."..";
else
   $nom_grp1 = $nom_grp;
//if ($typ_user == "APPRENANT")
//  include 'click_droit.txt';
include ('style.inc.php');
if (isset($saut) && $saut == 1)
{
  if (isset($utilisateur) && $utilisateur > 0 && $typ_user == "TUTEUR")
  {
    $req_grp =  mysql_query ("select utilgr_groupe_no from utilisateur_groupe,tuteur where
                              utilgr_utilisateur_no = $utilisateur and
                              tuteur.tut_apprenant_no = $utilisateur and
                              tuteur.tut_tuteur_no=$id_user AND
                              utilgr_groupe_no=$numero_groupe");
    $num_app = $utilisateur;
  }
  elseif (isset($utilisateur) && $utilisateur > 0  && ($typ_user == "ADMINISTRATEUR" || $typ_user == "FORMATEUR_REFERENT" || $typ_user == "RESPONSABLE_FORMATION"))
  {
    $req_grp =  mysql_query ("select utilgr_groupe_no from utilisateur_groupe where
                              utilgr_utilisateur_no = $utilisateur  AND
                              utilgr_groupe_no=$numero_groupe");
    $num_app = $utilisateur;
  }
  elseif($typ_user = "APPRENANT")
  {
     if ($numero_groupe > 0)
        $req_grp =  mysql_query ("select utilgr_groupe_no from utilisateur_groupe where
                                  utilgr_utilisateur_no = $id_user and
                                  utilgr_groupe_no=$numero_groupe");
     else
        $req_grp =  mysql_query ("select utilgr_groupe_no from utilisateur_groupe where utilgr_utilisateur_no = $id_user");
     $num_app = $id_user;
  }
  $nbr_id_grp = mysql_num_rows ($req_grp);
  if ($nbr_id_grp == 0 && $num_app != $id_user)
  {
    echo "<TABLE border='0' bgColor='#298CA0' cellspacing='2' width='680'><TR><TD>";
    echo "<TABLE border='0' bgColor='#FFFFFF' cellspacing='2' width='100%'><TR><TD>";
    echo "<CENTER>&nbsp;<P><FONT SIZE='2'><B>$mess_gp_tut_nogr</B></FONT><P>&nbsp;";
    echo "</TD></TR></TABLE></TD></TR></TABLE>";
    exit;
  }elseif ($nbr_id_grp == 0 && $num_app == $id_user)
  {
    echo "<TABLE border='0' bgColor='#298CA0' cellspacing='2' width='680'><TR><TD>";
    echo "<TABLE border='0' bgColor='#FFFFFF' cellspacing='2' width='100%'><TR><TD>";
    echo "<CENTER>&nbsp;<P><FONT SIZE='2'><B>$mess_gp_app_nogr</B></FONT><P>&nbsp;";
    echo "</TD></TR></TABLE></TD></TR></TABLE>";
    exit;
  }
  $id_grp = $numero_groupe;
  $nom_user=GetDataField ($connect,"select util_nom_lb from utilisateur where util_cdn='$num_app'","util_nom_lb");
  $prenom_user=GetDataField ($connect,"select util_prenom_lb from utilisateur where util_cdn='$num_app'","util_prenom_lb");
  $majuscule =$prenom_user." ".$nom_user;
  $grp_query = mysql_query ("select * from groupe where grp_cdn=$id_grp");
  $nom_grp = mysql_result ($grp_query,0,"grp_nom_lb");
  $obj_grp = html_entity_decode(mysql_result ($grp_query,0,"grp_formobject_lb"),ENT_QUOTES,'ISO-8859-1');
  $desc_grp = html_entity_decode(mysql_result ($grp_query,0,"grp_formdesc_cmt"),ENT_QUOTES,'ISO-8859-1');
  $comment_grp = html_entity_decode(mysql_result ($grp_query,0,"grp_commentaire_cmt"),ENT_QUOTES,'ISO-8859-1');
  $duree_query = mysql_query ("select sum(suiv2_duree_nb) from suivi2$Ext,prescription$Ext where
                               suiv2_utilisateur_no = prescription$Ext.presc_utilisateur_no AND
                               suiv2_utilisateur_no = $num_app AND
                               suiv2_seq_no = prescription$Ext.presc_seq_no AND
                               prescription$Ext.presc_grp_no = $numero_groupe AND
                               suiv2_grp_no = $numero_groupe");
  $duree_form = mysql_result($duree_query,0);
  if ($duree_form == 0)
  {
    echo "<TABLE border='0' bgColor='#298CA0' cellspacing='2' width='680'><TR><TD>";
    echo "<TABLE border='0' bgColor='#FFFFFF' cellspacing='2' width='100%'><TR><TD>";
    echo "<CENTER>&nbsp;<P><FONT SIZE='2'><B>$mess_gp_tut_nopresc</B></FONT><P>&nbsp;";
    echo "</TD></TR></TABLE></TD></TR></TABLE>";
    exit;
  }
  $duree = duree_calc($seq_form);
  $req_min= mysql_query("select presc_datedeb_dt from prescription$Ext where
                         presc_utilisateur_no = $num_app AND
                         prescription$Ext.presc_grp_no = $numero_groupe
                         order by presc_datedeb_dt asc");
  $date_min = mysql_result($req_min,0,"presc_datedeb_dt");
  $nb_jours_req_min = mysql_query ("select TO_DAYS('$date_min')");
  $nb_jours_min = mysql_result ($nb_jours_req_min,0);
  $req_max= mysql_query("select presc_datefin_dt from prescription$Ext where
                         presc_utilisateur_no = $num_app AND
                         prescription$Ext.presc_grp_no = $numero_groupe
                         order by presc_datefin_dt asc");
  $nbr_max = mysql_num_rows($req_max);
  $date_max = mysql_result($req_max,$nbr_max-1,"presc_datefin_dt");
  $nb_jours_req_max = mysql_query ("select TO_DAYS('$date_max')");
  $nb_jours_max = mysql_result ($nb_jours_req_max,0);
  $nbrj = $nb_jours_max-$nb_jours_min;
  $ch_date_deb = explode ("-",$date_min);
  $date_min = "$ch_date_deb[2]/$ch_date_deb[1]/$ch_date_deb[0]";
  $ch_date_fin = explode ("-",$date_max);
  $date_max = "$ch_date_fin[2]/$ch_date_fin[1]/$ch_date_fin[0]";
  $req_parcours = mysql_query("SELECT presc_parc_no from prescription$Ext where
                               presc_utilisateur_no = $num_app AND
                               prescription$Ext.presc_grp_no = $numero_groupe
                               group by presc_parc_no");
  $nomb_parc = mysql_num_rows($req_parcours);
  $bgcolorA = '#EEEF91';
  echo "<CENTER><TABLE border='0' bgColor='#298CA0' cellspacing='1' width='680' ><TR><TD>";
  echo "<TABLE border='0' bgColor='#FFFFFF' cellspacing='1' width='100%'><TR><TD>";
  if ($annuaire == 1)
    echo "<TABLE border='0' cellspacing='1' width='100%' cellpadding='3'>";
  else
    echo "<TABLE border='0' cellspacing='1' cellpadding='3'><TR><TD background=\"images/fond_titre_table.jpg\" colspan='2' height='34' align='center'>";
  if ($utilisateur)
    echo "<FONT COLOR='white' size=2><B>$mess_gp_form_suiv $majuscule</B></FONT><P>";
  elseif (!$utilisateur && $annuaire != 1)
    echo "<FONT size=3 COLOR='white'><B>$mess_menu_present</B></FONT>";
  elseif (!$utilisateur && $annuaire == 1)
    echo "<TR><TD background=\"images/fond_titre_table.jpg\" colspan='2' height='34' align='center' valign='center'><Font size='3' color='#FFFFFF'><B>$mess_ad_annu $mess_menu_gestion_grp $nom_grp</B></FONT><BR><FONT size='1' color='white'>$mess_admin_liste_app</FONT></TD></TR>";
  echo "</TD></TR>";
  if ($annuaire !=1)
    echo "<TR><TD align='left' nowrap bgcolor='#DEE3E7'><B>$mess_grp_form</B></TD><TD bgcolor='#F4F4F4' align='left'>$nom_grp</TD></TR>";
  $resp_grp = GetDataField ($connect,"select grp_resp_no from groupe where grp_cdn = $id_grp","grp_resp_no");
  $nom_resp=GetDataField ($connect,"select util_nom_lb from utilisateur where util_cdn='$resp_grp'","util_nom_lb");
  $photo=GetDataField ($connect,"select util_photo_lb from utilisateur where util_cdn='$resp_grp'","util_photo_lb");
  $prenom_resp=GetDataField ($connect,"select util_prenom_lb from utilisateur where util_cdn='$resp_grp'","util_prenom_lb");
  $majuscule =$prenom_resp." ".$nom_resp;
  $lien="prescription.php?numero_groupe=$numero_groupe&affiche_fiche_app=1&hgrp=$hgrp&id_util=$resp_grp&identite=1";
  $lien= urlencode($lien);
  echo "<TR><TD align='left' nowrap bgcolor='#DEE3E7' valign='top'><B>$mess_resp</B></TD>";
  echo "<TD bgcolor='#F4F4F4' align='left'><A href=\"trace.php?link=$lien\"";
  if ($photo != ""){
     list($w_img, $h_img, $type_img, $attr_img) = getimagesize("images/$photo");
     echo " onMouseOver=\"overlib('', TEXTCOLOR, '#ffffff', TEXTSIZE, 2, WIDTH, $w_img, HEIGHT, $h_img, BACKGROUND, 'images/$photo', PADX, 60, 20, PADY, 20, 20)\" onMouseOut=\"nd()\"";
  }else
     echo " title=\"$mess_suite_fp\"";
  echo ">$majuscule</A></TD></TR>";
  if ($annuaire !=1)
  {
    echo "<TR><TD align='left' nowrap bgcolor='#DEE3E7' valign='top' valign='top'><B>$mess_gp_obj_form</B></TD><TD bgcolor='#F4F4F4' align='left'>$obj_grp</TD></TR>";
    echo "<TR><TD align='left' nowrap bgcolor='#DEE3E7' valign='top'><B>$mess_gp_desc_form</B></TD><TD bgcolor='#F4F4F4' align='left'>$desc_grp</TD></TR>";
    echo "<TR><TD align='left' nowrap bgcolor='#DEE3E7' valign='top'><B>$mess_form_tot</B></TD><TD bgcolor='#F4F4F4' align='left'>$nomb_parc&nbsp;&nbsp;";
    echo strtolower($mess_gp_parc_appr);
    echo" </TD></TR>";
    echo "<TR>";
    echo "<TD align='left' nowrap bgcolor='#DEE3E7' valign='top'><B> $mess_gp_durre_tot_form</B></TD><TD bgcolor='#F4F4F4' align='left'>$duree_form $reparties_sur $nbrj $mess_jours</TD></TR>";
  }
  echo "<TR><TD align='left' nowrap bgcolor='#DEE3E7' valign='top'><B>$mess_intervenants </B></TD><TD bgcolor='#F4F4F4' align='left'>";
  $liste = mysql_query("select util_cdn,util_nom_lb from utilisateur,prescription$Ext where
                        utilisateur.util_cdn = prescription$Ext.presc_formateur_no and
                        prescription$Ext.presc_utilisateur_no = $num_app AND
                        prescription$Ext.presc_grp_no = $numero_groupe
                        group by utilisateur.util_nom_lb
                        order by utilisateur.util_nom_lb ASC");
  $nbr = mysql_num_rows($liste);
  $k = 0;
  while ($k < $nbr)
  {
    $num = mysql_result($liste,$k,"util_cdn");
    $nom_form=GetDataField ($connect,"select util_nom_lb from utilisateur where util_cdn='$num'","util_nom_lb");
    $prenom_form=GetDataField ($connect,"select util_prenom_lb from utilisateur where util_cdn='$num'","util_prenom_lb");
    $photo=GetDataField ($connect,"select util_photo_lb from utilisateur where util_cdn='$num'","util_photo_lb");
    $email = GetDataField ($connect,"select util_email_lb  from utilisateur where util_cdn = '$num'","util_email_lb");
    $nom_prof = $prenom_form." ".$nom_form;
    if ($k >0)
      echo "<BR>";
    $lien="prescription.php?numero_groupe=$numero_groupe&affiche_fiche_app=1&hgrp=$hgrp&id_util=$num&identite=1";
    $lien= urlencode($lien);
    echo "<A href=\"trace.php?link=$lien\"";
    if ($photo != "")
    {
       list($w_img, $h_img, $type_img, $attr_img) = getimagesize("images/$photo");
       echo " onMouseOver=\"overlib('', TEXTCOLOR, '#ffffff', TEXTSIZE, 2, WIDTH, $w_img, HEIGHT, $h_img, BACKGROUND, 'images/$photo', PADX, 60, 20, PADY, 20, 20)\" onMouseOut=\"nd()\"";
    }
    else
       echo " title=\"$mess_suite_fp\"";
    echo ">$nom_prof</A>";
    $k++;
  }
  echo "</TD></TR>";
  if (isset($utilisateur) && $utilisateur > 0)
    $req_tut = mysql_query("select tut_tuteur_no from tuteur where tut_apprenant_no = $utilisateur");
  else
  $req_tut = mysql_query("select tut_tuteur_no from tuteur where tut_apprenant_no = $id_user");
  $nomb_tut = mysql_num_rows($req_tut);
  if ($nomb_tut != 0)
  {
    $num_tut = mysql_result($req_tut,0,"tut_tuteur_no");
    $nom_tut=GetDataField ($connect,"select util_nom_lb from utilisateur where util_cdn='$num_tut'","util_nom_lb");
    $prenom_tut=GetDataField ($connect,"select util_prenom_lb from utilisateur where util_cdn='$num_tut'","util_prenom_lb");
    $photo=GetDataField ($connect,"select util_photo_lb from utilisateur where util_cdn='$num_tut'","util_photo_lb");
    $lien="prescription.php?numero_groupe=$numero_groupe&affiche_fiche_app=1&hgrp=$hgrp&id_util=$num_tut&identite=1";
    $lien= urlencode($lien);
    echo "<TR><TD bgcolor='#DEE3E7' valign='top'><B>".ucfirst(strtolower($mess_typ_tut))."</B></TD>";
    echo "<TD bgcolor='#F4F4F4'><A href=\"trace.php?link=$lien\"";
    if ($photo != "")
    {
       list($w_img, $h_img, $type_img, $attr_img) = getimagesize("images/$photo");
       echo " onMouseOver=\"overlib('', TEXTCOLOR, '#ffffff', TEXTSIZE, 2, WIDTH, $w_img, HEIGHT, $h_img, BACKGROUND, 'images/$photo', PADX, 60, 20, PADY, 20, 20)\" onMouseOut=\"nd()\"";
    }
    else
       echo " title=\"$mess_suite_fp\"";
    echo ">".$prenom_tut." ".$nom_tut."</A></TD></TR>";
  }

  echo "</TD></TR>";
  if ($annuaire != 1)
  {
    echo "<TR><TD align='left' nowrap bgcolor='#DEE3E7' valign='top'><B>$mess_gp_deb_form</B></TD><TD bgcolor='#F4F4F4' align='left'>$date_min</TD></TR>";
    echo "<TR><TD align='left' nowrap bgcolor='#DEE3E7' valign='top'><B>$mess_gp_fin_form</B></TD><TD bgcolor='#F4F4F4' align='left'>$date_max</TD></TR>";
  }
  elseif ($annuaire == 1 && $typ_user == "APPRENANT")
  {
    $liste = mysql_query("select util_cdn from utilisateur,utilisateur_groupe where utilisateur.util_cdn != $id_user AND utilisateur_groupe.utilgr_groupe_no = $id_grp AND utilisateur.util_cdn = utilisateur_groupe.utilgr_utilisateur_no order by utilisateur.util_nom_lb,utilisateur.util_prenom_lb ASC");
    $nomb_app = mysql_num_rows($liste);
    if ($nomb_app >0)
    {
      echo "<TR><TD width='40%' valign='top' bgcolor='#DEE3E7'><B>$mess_suite_ap_grp</B></TD><TD bgcolor='#F4F4F4' align='left'>";
      $photo=GetDataField ($connect,"select util_photo_lb from utilisateur where util_cdn='$id_user'","util_photo_lb");
      $lien="prescription.php?affiche_fiche_app=1&hgrp=$hgrp&id_util=$id_user&identite=1";
        $lien= urlencode($lien);
        echo "<A href=\"trace.php?link=$lien\"";
        if ($photo != "")
        {
           list($w_img, $h_img, $type_img, $attr_img) = getimagesize("images/$photo");
           echo " onMouseOver=\"overlib('', TEXTCOLOR, '#ffffff', TEXTSIZE, 2, WIDTH, $w_img, HEIGHT, $h_img, BACKGROUND, 'images/$photo', PADX, 60, 20, PADY, 20, 20)\" onMouseOut=\"nd()\"";
        }
        else
           echo " title=\"$mess_suite_fp\"";
        echo ">".$prenom_user." ".$nom_user."</A><BR>";
      $i=0;
      while ($i <$nomb_app)
      {
        $num_appre = mysql_result($liste,$i,"util_cdn");
        $nom_app=GetDataField ($connect,"select util_nom_lb from utilisateur where util_cdn='$num_appre'","util_nom_lb");
        $prenom_app=GetDataField ($connect,"select util_prenom_lb from utilisateur where util_cdn='$num_appre'","util_prenom_lb");
        $photo=GetDataField ($connect,"select util_photo_lb from utilisateur where util_cdn='$num_appre'","util_photo_lb");
        $lien="prescription.php?numero_groupe=$numero_groupe&affiche_fiche_app=1&hgrp=$hgrp&id_util=$num_appre&identite=1";
        $lien= urlencode($lien);
        echo "<A href=\"trace.php?link=$lien\"";
        if ($photo != "")
        {
           list($w_img, $h_img, $type_img, $attr_img) = getimagesize("images/$photo");
           echo " onMouseOver=\"overlib('', TEXTCOLOR, '#ffffff', TEXTSIZE, 2, WIDTH, $w_img, HEIGHT, $h_img, BACKGROUND, 'images/$photo', PADX, 60, 20, PADY, 20, 20)\" onMouseOut=\"nd()\"";
        }
        else
           echo " title=\"$mess_suite_fp\"";
        echo ">".$prenom_app." ".$nom_app."</A><BR>";
        $i++;
      }
      echo "</TD></TR>";
    }
  }
  echo "</TABLE>";
  echo "</TD></TR></TABLE></TD></TR></TABLE>";
exit;
}
//-----------------------------------------------------------------------------------------------------------------------------
if ($supp == 1 && !$suppression)
{
  echo "<CENTER><TABLE border='0' bgColor='#298CA0' cellspacing='2'><TR><TD>";
  echo "<TABLE border='0' bgColor='#FFFFFF' cellspacing='2'>";
  echo "<TR><TD background=\"images/fond_titre_table.jpg\" colspan='2' height='40' align='center' valign='center'><Font size='3' color='#FFFFFF'><B>$gest_presc : ".strtolower($msq_supp_titre)."</B></FONT></TD></TR>";
  echo "<TR><TD>&nbsp;<P><FONT SIZE='2'>$mess_gp_tut_nopresc</FONT><P>&nbsp;";
  if ($id_seq != 0)
  {
    $act_query = mysql_query ("select * from activite,suivi1$Ext where activite.act_seq_no = $id_seq and suivi1$Ext.suivi_act_no = activite.act_cdn and suivi1$Ext.suivi_utilisateur_no = $utilisateur");
    $Nb_act_seq = mysql_num_rows ($act_query);
    $seq_query = mysql_query ("select * from prescription$Ext where prescription$Ext.presc_parc_no = $id_parc AND prescription$Ext.presc_utilisateur_no = $utilisateur");
    $Nb_seq_parc = mysql_num_rows ($seq_query);
  }
  if ($Nb_act_seq == 1)
  {
     echo "<P><$mess_gp_der_act<br><br>";
     $supp_seq = 1;
     if ($Nb_seq_parc == 1)
     {
       echo "<P>$mess_gp_der_seq<br><br>";
       $supp_parc = 1;
     }
  }
  echo "$mess_gp_oui_sup_actseq<BR>";
  echo "<FORM NAME='form1' ACTION=\"gest_parc.php?formation=$formation&numero_groupe=$numero_groupe&hgrp=$hgrp&utilisateur=$utilisateur&supp_seq=$supp_seq&supp_parc=$supp_parc&id_parc=$id_parc&a_faire=1&id_seq=$id_seq&deroule[$i]=1&id_ref=$id_ref&seq_ouverte=$seq_ouverte&parc_ouvert=$parc_ouvert&vp=1\" METHOD='POST' target='main'>";
  echo "<INPUT TYPE='HIDDEN' NAME='supp' VALUE=1>";
  echo "<INPUT TYPE='HIDDEN' NAME='suppression' VALUE=1>";
  echo "<INPUT TYPE='HIDDEN' NAME='num' VALUE='$num'>";
  echo "<TD align='center'><A HREF=\"javascript:checkForm(document.form1);\" onclick =\"return(confv());\" onmouseover=\"img1.src='images/fiche_identite/boutvalidb.gif';return true;\" onmouseout=\"img1.src='images/fiche_identite/boutvalid.gif'\">".
       "<IMG NAME=\"img1\" SRC=\"images/fiche_identite/boutvalid.gif\" border='0' onLoad=\"tempImg=new Image(0,0); tempImg.src='images/fiche_identite/boutvalidb.gif'\"></A>";
  echo "</TD></TR></TABLE></TD></TR></TABLE>";
exit;
}
if ($supp == 1 && $suppression == 1)
{
  $supprimer = mysql_query("delete from suivi1$Ext where suivi_cdn = $num");
  if ($supp_seq == 1)
  {
     $supprimer = mysql_query("delete from suivi2$Ext where suiv2_seq_no = $id_seq and suiv2_utilisateur_no = $utilisateur");
     $supprimer = mysql_query("delete from prescription$Ext where presc_seq_no = $id_seq and presc_utilisateur_no = $utilisateur");
     if ($supp_parc == 1)
     {
       $supprimer = mysql_query("delete from suivi3$Ext where suiv3_parc_no = $id_parc and suiv3_utilisateur_no = $utilisateur");
       $supprimer = mysql_query("delete from prescription$Ext where presc_parc_no = $id_parc and presc_utilisateur_no = $utilisateur");
     }
  }
}
if (isset($change_date) && $change_date == 1)
{
    if ($iday && $imonth && $iyear)
      $new_date="$imonth/$iday/$iyear";
    if ($day && $month && $year)
      $new_date="$day/$month/$year";
    $ch_datenew = explode ("/",$new_date);
    $date = "$ch_datenew[2]-$ch_datenew[1]-$ch_datenew[0]";
    $date_inv = "$ch_datenew[0]-$ch_datenew[1]-$ch_datenew[2]";
    if ($debut == 1)
      $seq_change = mysql_query ("update prescription$Ext set
                                  presc_datedeb_dt = '$date' where
                                  presc_seq_no = $change_seq and
                                  presc_utilisateur_no = $utilisateur and
                                  presc_grp_no = $numero_groupe");
    else
      $seq_change = mysql_query ("update prescription$Ext set
                                  presc_datefin_dt = '$date' where
                                  presc_seq_no = $change_seq and
                                  presc_utilisateur_no = $utilisateur and
                                  presc_grp_no = $numero_groupe");
    $parc_ouvert = $parc;
    $seq_ouverte = $id_seq;
    $vp = 1;
    $titre_seq = GetDataField ($connect,"select seq_titre_lb from sequence where seq_cdn=$id_seq","seq_titre_lb");
    $le_parc = $parc;
   $prescripteur = GetDataField ($connect,"select presc_prescripteur_no from prescription$Ext where
                                           presc_seq_no = $id_seq and
                                           presc_utilisateur_no = $utilisateur and
                                           presc_grp_no = $numero_groupe","presc_prescripteur_no");
   $inscripteur = GetDataField ($connect,"select util_auteur_no from utilisateur where util_cdn='$utilisateur'","util_auteur_no");
   $formateur = GetDataField ($connect,"select presc_formateur_no from prescription$Ext where
                                        presc_seq_no = $id_seq and
                                        presc_utilisateur_no = $utilisateur and
                                        presc_grp_no = $numero_groupe","presc_formateur_no");
   if ($formateur == $id_user)
     $qualite = "Formateur";
   elseif ($prescripteur == $id_user)
     $qualite = "Prescripteur";
   elseif ($inscripteur == $id_user)
     $qualite = "Inscripteur";
   elseif ($prescripteur != $id_user && $inscripteur != $id_user && $typ_user == "ADMINISTRATEUR")
     $qualite = "Administrateur";
   else
     $qualite = "";
    $action_fiche = "Gestion des prescriptions";
    $commentaire = $mess_gp_chgdt_finpresc." ".$titre_seq." $date_fin $mess_rep_item $date_inv"."\n";
    $new_fiche = Donne_ID ($connect,"select max(fiche_cdn) from fiche_suivi");
    $req_fiche = mysql_query("INSERT INTO fiche_suivi
                              (fiche_cdn,fiche_utilisateur_no,fiche_auteur_no,fiche_qualite_lb,fiche_date_dt,fiche_heure_dt,
                              fiche_commentaire_cmt,fiche_grp_no,fiche_parc_no,fiche_seq_no,fiche_act_no,fiche_typaction_lb) VALUES
                              ($new_fiche,$utilisateur,$id_user,'$qualite','$date_fiche','$heure_fiche',\"$commentaire\",
                              $numero_groupe,$le_parc,$id_seq,0,\"$action_fiche\")");
}
if (isset($modif_etat) && $modif_etat == 1)
  $msg1 = "<CENTER><FONT color='white' size='2'>$mess_gp_etat_act_modif $prenom $nom</FONT></CENTER>";
if ($message == 1)
  $msg1 = "<CENTER><FONT color='white' size='2'>$mess_gp_seq_sup $prenom_user $nom_user</FONT></CENTER>";
  // Dï¿½but de la bloucle (parc/seq/activitï¿½s
if ($a_faire == 1)
{
    if ($id_ref>0)
    {
      $referentiel = GetDataField ($connect,"select ref_nomabrege_lb from referentiel where ref_cdn = $id_ref","ref_nomabrege_lb");
      $nom_referentiel = GetDataField ($connect,"select ref_desc_cmt from referentiel where ref_cdn = $id_ref","ref_desc_cmt");
      if ($nom_referentiel == "")
         $nom_referentiel = GetDataField ($connect,"select ref_nomabrege_lb from referentiel where ref_cdn = $id_ref","ref_nomabrege_lb");
    }
    $classe = GetDataField ($connect,"SELECT grp_classe_on from groupe where grp_cdn  = $numero_groupe","grp_classe_on");
    if ($utilisateur > 0 &&  $typ_user == "TUTEUR")
    {
       $parc_query = mysql_query ("select suiv3_parc_no from suivi3$Ext,prescription$Ext where
                                   prescription$Ext.presc_parc_no = suivi3$Ext.suiv3_parc_no and
                                   prescription$Ext.presc_utilisateur_no=$utilisateur AND
                                   prescription$Ext.presc_grp_no = $numero_groupe
                                   group by suivi3$Ext.suiv3_parc_no
                                   order by prescription$Ext.presc_ordre_no,prescription$Ext.presc_cdn");
       $num_app = $utilisateur;
    }
    elseif ($utilisateur > 0 && ($typ_user == "ADMINISTRATEUR" || $typ_user == "FORMATEUR_REFERENT" || $typ_user == "RESPONSABLE_FORMATION"))
    {
       $parc_query = mysql_query ("select all suiv3_parc_no from suivi3$Ext,prescription$Ext where
                                   prescription$Ext.presc_parc_no = suivi3$Ext.suiv3_parc_no and
                                   prescription$Ext.presc_utilisateur_no=$utilisateur AND
                                   prescription$Ext.presc_grp_no = $numero_groupe and
                                   suivi3$Ext.suiv3_grp_no = $numero_groupe
                                   group by suivi3$Ext.suiv3_parc_no
                                   order by prescription$Ext.presc_ordre_no,prescription$Ext.presc_cdn");
       $num_app = $utilisateur;
    }
    else
    {
       $parc_query = mysql_query ("select suiv3_parc_no from suivi3$Ext,prescription$Ext where
                                   prescription$Ext.presc_parc_no = suivi3$Ext.suiv3_parc_no and
                                   prescription$Ext.presc_utilisateur_no = $id_user AND
                                   prescription$Ext.presc_grp_no = $numero_groupe and
                                   suivi3$Ext.suiv3_grp_no = $numero_groupe
                                   group by suivi3$Ext.suiv3_parc_no
                                   order by prescription$Ext.presc_ordre_no,prescription$Ext.presc_cdn");
       $num_app = $id_user;
    }
    $nom_user=GetDataField ($connect,"select util_nom_lb from utilisateur where util_cdn='$num_app'","util_nom_lb");
    $prenom_user=GetDataField ($connect,"select util_prenom_lb from utilisateur where util_cdn='$num_app'","util_prenom_lb");
    $nb_parc = mysql_num_rows ($parc_query);
    if ($nb_parc == 0 && $typ_user == "APPRENANT")
    {
       echo "<CENTER><TABLE border='0' bgColor='#298CA0' cellspacing='2' width='98%'><TR><TD>";
       echo "<TABLE border='0' bgColor='#FFFFFF' cellspacing='2' cellpadding='0'>";
       echo "<TR><TD background=\"images/fond_titre_table.jpg\" colspan='2' height='40' align='center' valign='center'><Font size='3' color='#FFFFFF'><B>$mess_menu_suivi</B></FONT></TD></TR>";
       echo "<TR><TD><Font size='2'>$mess_gp_tut_nopresc1</TD></TR>";
       echo "<TR><TD align=left><A HREF=\"javascript:history.back();\" TITLE=\"$alter\" onmouseover=\"img_annonce.src='images/gest_parc/boutretour2b.gif';return true;\" onmouseout=\"img_annonce.src='images/gest_parc/boutretour2.gif'\">";
       echo "<IMG NAME=\"img_annonce\" SRC=\"images/gest_parc/boutretour2.gif\" border='0' onLoad=\"tempImg=new Image(0,0); tempImg.src='images/gest_parc/boutretour2b.gif'\"></A></TD>";
       echo "</TD></TR></TABLE></TD></TR></TABLE>";
       exit;
    }
    elseif ($nb_parc == 0 && $typ_user != "APPRENANT")
    {
       echo "<CENTER><TABLE border='0' bgColor='#298CA0' cellspacing='2' cellpadding='0' width='98%'><TR><TD>";
       echo "<TABLE border='0' bgColor='#FFFFFF' cellspacing='1' cellpadding='0'>";
       echo "<TR><TD background=\"images/fond_titre_table.jpg\" colspan='2' height='40' align='center' valign='center'><Font size='3' color='#FFFFFF'><B>$mess_menu_suivi</B></FONT></TD></TR>";
       echo "<TR><TD><Font size='2'>$mess_gp_tut_nopresc</TD></TR>";
       echo "<TR><TD align=left><A HREF=\"javascript:history.back();\" TITLE=\"$alter\" onmouseover=\"img_annonce.src='images/gest_parc/boutretour2b.gif';return true;\" onmouseout=\"img_annonce.src='images/gest_parc/boutretour2.gif'\">";
       echo "<IMG NAME=\"img_annonce\" SRC=\"images/gest_parc/boutretour2.gif\" border='0' onLoad=\"tempImg=new Image(0,0); tempImg.src='images/gest_parc/boutretour2b.gif'\"></A></TD>";
       echo "</TD></TR></TABLE></TD></TR></TABLE>";
       exit;
    }
    echo "<CENTER><TABLE border='0' bgColor='#298CA0' cellspacing='2' cellpadding='0' width='98%'><TR><TD width=100%>";
    echo "<TABLE border='0' bgColor='#FFFFFF' cellspacing='1' cellpadding='0' width=100%>";
    $laligne = "<tr><td><table width='100%' cellpadding='3' cellspacing='0' border='0'><tr height='40'>";
    $letitre = "<TABLE border='0' background=\"images/fond_titre_table.jpg\" cellspacing='2' cellpadding='2' width=100% height='36'><TR>";
    if (!empty($utilisateur) && $utilisateur> 0)
      $retour = "gest_parc_frm.php?numero_groupe=$numero_groupe&hgrp=$hgrp&depasse=$depasse&prq=$prq&visible=$visible&proposable=$proposable&autorise=$autorise&hgrp=$hgrp&switch=1&vn=1&saut=$saut&ouvrir=$ouvrir&accord=$accord&utilisateur=$utilisateur&a_faire=1&act_open=$act_open&id_seq=$id_seq&seq_ouverte=$id_seq&parc_ouvert=$parc&graph=1&tout=1&formation=0";
    else
    {
         $retour = "gest_frm_rappel1.php?le_groupe=$numero_groupe&graph=1&tout=1&menu=1&formation=1";
    }
    if ($tout == 1)
    {
       $sens_aff = $mess_replier;
       $lien="gest_parc.php?formation=$formation&numero_groupe=$numero_groupe&hgrp=$hgrp&utilisateur=$utilisateur&a_faire=1";
       $lien = urlencode($lien);
       $laligne .= "<TD style=\"height :25px;\"><div id=dep' style=\"float:left;padding-right:8px;\"><A href=\"trace.php?link=$lien\" class='bouton_new'>$sens_aff</A></div>";
    }
    else
    {
       $sens_aff = $mess_deplier;
       $lien="gest_parc.php?formation=$formation&numero_groupe=$numero_groupe&hgrp=$hgrp&tout=1&utilisateur=$utilisateur&a_faire=1";
       $lien = urlencode($lien);
       $laligne .= "<TD style=\"height :25px;\"><div id='rep' style=\"float:left;padding-right:8px;\"><A href=\"trace.php?link=$lien\" class='bouton_new'>$sens_aff</A></div>";
    }
    $affiche0 = "$mess_csv";
    $affiche1 = "$mess_genere_pdf <BR><U>$mess_pdf_nb</U>.<BR>$mess_genere_pdf1";
    $affiche2 = "$mess_genere_pdf <BR><U>$mess_pdf_color</U>.<BR>$mess_genere_pdf1";
    $titreur = ($utilisateur > 0) ?  "$mess_gp_form_presc_app ".ucfirst(strtolower($nom_user))." ".ucfirst(strtolower($prenom_user))." : $nom_grp1" : $mess_gp_ma_form;
    $letitre .= "<TD width='100%' nowrap align='center'><FONT SIZE=3 color='white'><B>$titreur</B></FONT></TD>";
    $laligne .= "<div id='print' style=\"float:left;padding-right:8px;\"><A href=\"#\" onClick=\"javascript:print();\" class='bouton_new' ".
                "onMouseOver=\"overlib('<TABLE><TR><TD width=5></TD><TD>".addslashes($mess_bas_print." ".$mess_bas_print1)."</TD></TR></TABLE>',".
                "ol_hpos,LEFT,BELOW,WIDTH,'220',DELAY,800,CAPTION,'')\"".
                " onMouseOut=\"nd()\">$mess_bas_print</A></div>";
    $laligne .= "<div id='excel' style=\"float:left;padding-right:8px;\"><A HREF=\"#\" class='bouton_new' onclick=".
                "\"javascript:window.open('genere_csv.php?".getenv("QUERY_STRING")."','','resizable=yes,scrollbars=yes,status=no')\"".
                " onMouseOver=\"overlib('<TABLE><TR><TD width=5></TD><TD>".addslashes($affiche0)."</TD></TR></TABLE>',".
                "ol_hpos,LEFT,BELOW,WIDTH,'220',DELAY,800,CAPTION,'')\"".
                " onMouseOut=\"nd()\">$mess_generXL</A></div>";
    $laligne .= "<div id='pdfnb' style=\"float:left;padding-right:8px;\"><A HREF=\"#\" class='bouton_new' onclick=".
                "\"javascript:window.open('genere_pdf_nb.php?".getenv("QUERY_STRING")."','','resizable=yes,scrollbars=yes,status=no')\"".
                " onMouseOver=\"overlib('<TABLE><TR><TD width=5></TD><TD>".addslashes($affiche1)."</TD></TR></TABLE>'".
                  ",ol_hpos,LEFT,BELOW,WIDTH,'220',DELAY,800,CAPTION,'')\"".
                  " onMouseOut=\"nd()\">$mess_generNB</A></div>";
    $laligne .= "<div id='pdfColor' style=\"float:left;padding-right:8px;\"><A HREF=\"#\" class='bouton_new' onclick=".
                   "\"javascript:window.open('genere_form_pdf.php?".getenv("QUERY_STRING")."','','resizable=yes,scrollbars=yes,status=no')\"".
                   " onMouseOver=\"overlib('<TABLE><TR><TD width=5></TD><TD>".addslashes($affiche2)."</TD></TR></TABLE>'".
                  ",ol_hpos,LEFT,BELOW,WIDTH,'220',DELAY,800,CAPTION,'')\"".
                  " onMouseOut=\"nd()\">Vue en Pdf Couleur</A></div>";
    $laligne .= "<div id='formation' style=\"float:left;padding-right:8px;\"><a href=\"$retour\" class= 'bouton_new'>$mesg_voirform</a></div>";
    $letitre .= "</TR></TABLE>";
    $laligne .= aide_div('impression_formation_apprenant',0,0,0,0);
    $laligne .= "</td></TR></TABLE></td></tr>";
    echo "<TR><TD background=\"images/fond_titre_table.jpg\" height='36' align='center' valign='center' width='100%'>$letitre</TD></TR>";
    echo $laligne;
       if ($msg1 != "")
         echo "<TR><TD> $msg1</TD></TR>";
//--------------------------Dï¿½but de la boucle parcours > sequences > activitï¿½s--------------------------------------------
//-------------------------------------------------------------------------------------------------------------------------

    $bgcolorB = '#2B677A';
    $bgcolorA = '#D5652E';
    echo "<TR><TD width = '100%'>";
    echo "<TABLE border='0' width=100% cellspacing='1' cellpadding='0'>";
    echo "<TR height='30' bgcolor='$bgcolorB'>";
    echo "<TD align='left' colspan='2' width='68%' nowrap><FONT COLOR=white><b>&nbsp;&nbsp;$mess_gp_parc_appr&nbsp;&nbsp;</b></FONT></TD>";
    echo "<TD align='left' width='8%' nowrap><b><FONT COLOR=white>&nbsp;&nbsp;$mess_gp_duree&nbsp;&nbsp;</FONT></b></TD>";
    echo "<TD align='left' width='8%' nowrap><b><FONT COLOR=white>&nbsp;&nbsp;$mess_gp_date_deb&nbsp;&nbsp;</FONT></b></TD>";
    echo "<TD align='left' width='8%' nowrap><b><FONT COLOR=white>&nbsp;&nbsp;$mess_gp_date_fin&nbsp;&nbsp;</FONT></b></TD>";
    echo "<TD align='left' nowrap width='8%' nowrap><b><FONT COLOR=white>&nbsp;&nbsp;$mess_gp_etat&nbsp;&nbsp;</FONT></b></TD>";
    echo "</TR>";
    $p=0;
    //$deroule_parc = array();
    //$deroule = array();
    while ($p < $nb_parc)
    {
          $compte++;
          $fin_parc=0;
          $id_parc = mysql_result ($parc_query,$p,"suiv3_parc_no");
          $etat_parc =  GetDataField ($connect,"select suiv3_etat_lb from suivi3$Ext where
                                                suiv3_parc_no = $id_parc and
                                                suiv3_parc_no = $id_parc and
                                                suiv3_utilisateur_no = $num_app and
                                                suiv3_grp_no = $numero_groupe","suiv3_etat_lb");
          $type_parcours =  GetDataField ($connect,"select parcours_type_lb from parcours where parcours_cdn = $id_parc","parcours_type_lb");
              if ($id_parc != 0)
              {
                 $createur = GetDataField ($connect,"select parcours_auteur_no from parcours where parcours_cdn = $id_parc","parcours_auteur_no");
                 $nom = GetDataField ($connect,"select parcours_nom_lb from parcours where parcours_cdn = $id_parc","parcours_nom_lb");
                 $description = GetDataField ($connect,"select parcours_desc_cmt from parcours where parcours_cdn = $id_parc","parcours_desc_cmt");
                 $id_ref_parc = GetDataField ($connect,"select parcours_referentiel_no from parcours where parcours_cdn = $id_parc","parcours_referentiel_no");
                 $nom_referentiel = GetDataField ($connect,"select ref_desc_cmt from referentiel where ref_cdn = $id_ref_parc","ref_desc_cmt");
                 $desc_ref = $nom_referentiel;
                 $nom_createur = GetDataField ($connect,"select util_nom_lb from utilisateur where util_cdn = $createur","util_nom_lb");
                 $prenom_createur = GetDataField ($connect,"select util_prenom_lb from utilisateur where util_cdn = $createur","util_prenom_lb");
              }
              else
              {
                 $nom = GetDataField ($connect,"select parcours_nom_lb from parcours where parcours_cdn = $id_parc","parcours_nom_lb");
                 $description = GetDataField ($connect,"select parcours_desc_cmt from parcours where parcours_cdn = $id_parc","parcours_desc_cmt");
                 $desc = $mess_gp_nolien_ref;
                 $id_ref_parc = GetDataField ($connect,"select parcours_referentiel_no from parcours where parcours_cdn = $id_parc","parcours_referentiel_no");
                 $nom_referentiel = GetDataField ($connect,"select ref_desc_cmt from referentiel where ref_cdn = $id_ref_parc","ref_desc_cmt");
                 $desc_ref = $nom_referentiel;
                 $nom_createur = $mess_gen_formagri;
                 $prenom_createur = "";//$mess_gen_gen_formagri;
                 $nom_referentiel = $mess_gp_nolien_ref;
              }
              $nom_parc=$nom;
              $lien = "gest_parc.php?formation=$formation&numero_groupe=$numero_groupe&hgrp=$hgrp&consult=1&parcours=$parcours&id_parc=$id_parc&id_ref_parc=$id_ref_parc";
              $lien = urlencode($lien);
              if ($deroule_parc[$p] == 1 && ($id_parc == $parc || $parc_ouvert == $id_parc) && (!isset($switch) || (isset($switch) && $switch != 1)))
                $deroulee_parc[$p] = 0;
              else
                $deroulee_parc[$p] = 1;
              if (($deroule_parc[$p] == 1 || $parc_ouvert == $id_parc) || $actif_parc == $id_parc || $tout == 1)
              {
                 echo "<TR height='30' bgcolor='#EFEFEF' width='100%'>";
                 if (!isset($deroule[$p]))
                     $deroule[$p] ='';
                 if (!isset($deroule[$i]))
                     $deroule[$i] ='';
                 $lien = "gest_parc.php?formation=$formation&numero_groupe=$numero_groupe&hgrp=$hgrp&accord=$accord&id_seq=$seq&parc=$id_parc&deroule_parc[$p]=0&deroule[$p]=$deroule[$p]&utilisateur=$utilisateur&a_faire=1";
                 $lien = urlencode($lien);
                 echo "<TD align='center' width='3%' valign='top'><A HREF=\"trace.php?link=$lien\" target='main' title=\"$mess_gp_masq_det_parc\">";
                 echo "<IMG SRC='images/gest_parc/flech02.gif' border='0' alt=\"$mess_gp_masq_det_parc\"></A></TD>";
                 $lien = "gest_parc.php?formation=$formation&numero_groupe=$numero_groupe&hgrp=$hgrp&accord=$accord&id_seq=$seq&parc=$id_parc&deroule_parc[$p]=$deroulee_parc[$p]&deroule[$i]=$deroule[$i]&utilisateur=$utilisateur&a_faire=1";
                 $lien = urlencode($lien);
                 echo "<TD align='left' valign='top'><DIV id='sequence'>&nbsp;&nbsp;<FONT COLOR='#000000'><A HREF=\"trace.php?link=$lien\" target='main' onMouseOver=\"overlib('";
                 if ($desc_ref != "")
                   echo "$msq_ref_ass : ".strip_tags(html_entity_decode($desc_ref,ENT_QUOTES,'ISO-8859-1')).".<BR>";
                 echo " $mess_gp_cree_par<B> $prenom_createur $nom_createur </B>.',ol_hpos,".
                      "RIGHT,WIDTH,'250')\" onMouseOut=\"nd()\">$nom</A>".
                      "&nbsp;:&nbsp;".html_entity_decode($description,ENT_QUOTES,'ISO-8859-1')."</DIV></TD>";
              }
              else
              {
                 echo "<TR height='30' bgcolor='#E1E0DF'>";
                 $lien = "gest_parc.php?formation=$formation&numero_groupe=$numero_groupe&hgrp=$hgrp&accord=$accord&deroule_parc[$p]=1&deroule[$i]=$deroule[$i]&a_faire=1&id_seq=$seq&parc=$id_parc&utilisateur=$utilisateur";
                 $lien = urlencode($lien);
                 echo "<TD align='center' width='3%' valign='top'><a href=\"trace.php?link=$lien\" target='main' title=\"$mess_gp_aff_det_parc\"".
                      " onmouseover=\"img_fl$compte.src='images/gest_parc/flech01b.gif';return true;\"".
                      " onmouseout=\"img_fl$compte.src='images/gest_parc/flech01.gif'\">".
                      "<IMG NAME=\"img_fl$compte\" SRC=\"images/gest_parc/flech01.gif\" border='0' valign='top' alt=\"$mess_gp_aff_det_parc\"".
                      " onLoad=\"tempImg=new Image(0,0); tempImg.src='images/gest_parc/flech01b.gif'\"></A></TD>";
                 $lien = "gest_parc.php?formation=$formation&numero_groupe=$numero_groupe&hgrp=$hgrp&accord=$accord&id_seq=$seq&parc=$id_parc&deroule_parc[$p]=$deroulee_parc[$p]&deroule[$i]=$deroule[$i]&utilisateur=$utilisateur&a_faire=1";
                 $lien = urlencode($lien);
                 echo "<TD align='left' valign='top'><DIV id='sequence'>&nbsp;&nbsp;<A HREF=\"trace.php?link=$lien\" target='main' onMouseOver=\"overlib('";
                 if ($desc_ref != "")
                   echo "$msq_ref_ass : ".strip_tags(html_entity_decode($desc_ref,ENT_QUOTES,'ISO-8859-1')).".<BR>";
                 echo " $mess_gp_cree_par<B> $prenom_createur $nom_createur </B>.',ol_hpos,".
                      "RIGHT,WIDTH,'250')\" onMouseOut=\"nd()\">$nom</A>".
                      "&nbsp;:&nbsp;".html_entity_decode($description,ENT_QUOTES,'ISO-8859-1')."</DIV></TD>";
              }
              $req_min= mysql_query("select presc_datedeb_dt from prescription$Ext where
                                     presc_utilisateur_no = $num_app and
                                     presc_parc_no = $id_parc and
                                     presc_grp_no = $numero_groupe
                                     order by presc_datedeb_dt asc");
              $date_min = mysql_result($req_min,0,"presc_datedeb_dt");
              $nb_jours_req_min = mysql_query ("select TO_DAYS('$date_min')");
              $req_max= mysql_query("select presc_datefin_dt from prescription$Ext where
                                     presc_utilisateur_no = $num_app and
                                     presc_parc_no = $id_parc and
                                     presc_grp_no = $numero_groupe
                                     order by presc_datefin_dt asc");
              $nbr_max = mysql_num_rows($req_max);
              $date_max = mysql_result($req_max,$nbr_max-1,"presc_datefin_dt");
              $nb_jours_req_max = mysql_query ("select TO_DAYS('$date_max')");
                 $seq_parc_query = mysql_query ("SELECT sum(sequence.seq_duree_nb) from sequence,sequence_parcours where sequence_parcours.seqparc_parc_no = $id_parc and  sequence.seq_cdn = sequence_parcours.seqparc_seq_no");
              $duree_parc = mysql_result($seq_parc_query,0);
              $duree_parc = duree_calc($duree_parc);
              if (($deroule_parc[$p] == 1 || $parc_ouvert == $id_parc) || $actif_parc == $id_parc || $tout == 1)
                 echo "<TD align='left' nowrap valign='top'><B>&nbsp;&nbsp;$duree_parc&nbsp;&nbsp;</B></TD>";
              else
                 echo "<TD align='left' valign='top' nowrap bgcolor='#EFEFEF'>&nbsp;&nbsp;$duree_parc&nbsp;&nbsp;</TD>";
              $ch_date_deb = explode ("-",$date_min);
              $date_min = "$ch_date_deb[2]/$ch_date_deb[1]/$ch_date_deb[0]";
              $ch_date_fin = explode ("-",$date_max);
              $date_max = "$ch_date_fin[2]/$ch_date_fin[1]/$ch_date_fin[0]";
              if ($deroule_parc[$p] == 1){
                 echo "<TD align='left' valign='top'><B>&nbsp;&nbsp;$date_min</B></TD>";
                 echo "<TD align='left' valign='top'><B>&nbsp;&nbsp;$date_max</B></TD>";
              }else{
                 echo "<TD align='left' valign='top'>&nbsp;&nbsp;$date_min&nbsp;&nbsp;</TD>";
                 echo "<TD align='left' valign='top'>&nbsp;&nbsp;$date_max&nbsp;&nbsp;</TD>";
              }
              if ($etat_parc == "TERMINE")
                 $parcsuiv = $mess_lanc_termine;
              elseif ($etat_parc == "A FAIRE")
                 $parcsuiv = $mess_lanc_afaire;
              elseif ($etat_parc == "EN COURS")
                 $parcsuiv = $mess_lanc_encours;
              elseif ($etat_parc == "ATTENTE")
                 $parcsuiv = $mess_lanc_attente;
              $parc_suivi = $parcsuiv;
              if ($deroule_parc[$p] == 1)
                 echo "<TD align='left' nowrap valign='top'><B>&nbsp;&nbsp;$parc_suivi&nbsp;&nbsp;</B></TD>";
              else
                 echo "<TD align='left' nowrap valign='top'>&nbsp;&nbsp;$parc_suivi&nbsp;&nbsp;</TD>";
              if ($utilisateur > 0){
                 $nb_sans_seq = 0;
                 $prescripteur = GetDataField ($connect,"select presc_prescripteur_no from prescription$Ext where
                                                         presc_parc_no = $id_parc and
                                                         presc_utilisateur_no = $num_app and
                                                         presc_grp_no = $numero_groupe","presc_prescripteur_no");
                 $formateur = GetDataField ($connect,"select presc_formateur_no from prescription$Ext where
                                                      presc_parc_no = $id_parc and
                                                      presc_utilisateur_no = $num_app and
                                                      presc_grp_no = $numero_groupe","presc_formateur_no");
                 $sans_seq = mysql_query ("select presc_seq_no from prescription$Ext where
                                           presc_parc_no = $id_parc and
                                           presc_utilisateur_no = $num_app and
                                           presc_grp_no = $numero_groupe");
                 $nb_sans_seq = mysql_num_rows ($sans_seq);
                 $signal=0;
              }
              echo "</TR>";
  //Sequences a faire dans le parcours dï¿½roulï¿½
  if ((($deroule_parc[$p] == 1 && $parc == $id_parc) || $tout == 1) || (isset($vp) && $vp == 1 && $id_parc == $parc_ouvert)){
   $ouvrir = 'parcours';
   $parc_ouvert = $id_parc;
       if ($utilisateur &&  $typ_user == "TUTEUR"){
          $seq_query = mysql_query ("SELECT DISTINCT suiv2_etat_lb,suiv2_seq_no FROM
                                     suivi2$Ext,prescription$Ext WHERE
                                     suivi2$Ext.suiv2_seq_no = prescription$Ext.presc_seq_no AND
                                     prescription$Ext.presc_utilisateur_no=$utilisateur AND
                                     prescription$Ext.presc_parc_no=$id_parc AND
                                     prescription$Ext.presc_grp_no = $numero_groupe AND
                                     suiv2_utilisateur_no = $utilisateur AND
                                     suiv2_grp_no = $numero_groupe
                                     ORDER BY prescription$Ext.presc_datefin_dt,prescription$Ext.presc_ordre_no,suivi2$Ext.suiv2_ordre_no ASC");
          $num_app = $utilisateur;
       }elseif ($utilisateur && ($typ_user == "ADMINISTRATEUR"  || $typ_user == "FORMATEUR_REFERENT" || $typ_user == "RESPONSABLE_FORMATION")){
          $seq_query = mysql_query ("SELECT DISTINCT suiv2_etat_lb,suiv2_seq_no FROM
                                     suivi2$Ext,prescription$Ext WHERE
                                     suivi2$Ext.suiv2_seq_no = prescription$Ext.presc_seq_no AND
                                     prescription$Ext.presc_utilisateur_no=$utilisateur AND
                                     prescription$Ext.presc_parc_no=$id_parc AND
                                     prescription$Ext.presc_grp_no = $numero_groupe AND
                                     suiv2_utilisateur_no = $utilisateur AND
                                     suiv2_grp_no = $numero_groupe
                                     ORDER BY prescription$Ext.presc_datefin_dt,prescription$Ext.presc_ordre_no,suivi2$Ext.suiv2_ordre_no ASC");
          $num_app = $utilisateur;
       }elseif(!$utilisateur){
          $seq_query = mysql_query ("SELECT DISTINCT suiv2_etat_lb,suiv2_seq_no FROM
                                     suivi2$Ext,prescription$Ext WHERE
                                     suivi2$Ext.suiv2_seq_no = prescription$Ext.presc_seq_no AND
                                     prescription$Ext.presc_parc_no=$id_parc AND
                                     prescription$Ext.presc_grp_no = $numero_groupe AND
                                     suiv2_utilisateur_no = $id_user AND
                                     suiv2_grp_no = $numero_groupe
                                     ORDER BY prescription$Ext.presc_datefin_dt,prescription$Ext.presc_ordre_no,suivi2$Ext.suiv2_ordre_no ASC");
          $num_app = $id_user;
       }
    $nb_seq = mysql_num_rows ($seq_query);
    $bgcolor2 = '#2B677A';
    $bgcolor1 = '#ECD6C0';
       echo "<TR><TD bgcolor='#EFEFEF' valign='top' align='right'>&nbsp;</TD>";
       echo "<TD colspan='5' bgcolor='#EFEFEF'>";
       echo "<TABLE border='0' bgcolor='#EFEFEF' width='95%' cellspacing='1' cellpadding='3'>";
        echo "<TR height='30'>";
          echo "<TD align='left' colspan='2' bgcolor=$bgcolor2><FONT COLOR=white><b>&nbsp;&nbsp;$mess_gp_seq_parc&nbsp;&nbsp;</b></FONT></TD>";
          echo "<TD align='left' bgcolor=$bgcolor2><b><FONT COLOR=white>&nbsp;&nbsp;$msq_prereq&nbsp;&nbsp;</FONT></b></TD>";
          echo "<TD align='left' bgcolor=$bgcolor2><b><FONT COLOR=white>&nbsp;&nbsp;$mess_gp_duree&nbsp;&nbsp;</FONT></b></TD>";
          echo "<TD align='left' bgcolor=$bgcolor2><b><FONT COLOR=white>&nbsp;&nbsp;$mess_gp_date_deb&nbsp;&nbsp;</FONT></b></TD>";
          echo "<TD align='left' bgcolor=$bgcolor2><b><FONT COLOR=white>&nbsp;&nbsp;$mess_gp_date_fin&nbsp;&nbsp;</FONT></b></TD>";
          echo "<TD align='left' bgcolor=$bgcolor2><b><FONT COLOR=white>&nbsp;&nbsp;$mess_gp_etat&nbsp;&nbsp;</FONT></b></TD>";
          echo "</TR>";
       if ($utilisateur)
          $num_app = $utilisateur;
       else
          $num_app = $id_user;
       $compteur_fin_seq=0;
       $i = 0;
       while ($i != $nb_seq) {
              $compte++;
              $seq = mysql_result ($seq_query,$i,"suiv2_seq_no");
              $type_sequence = GetDataField ($connect,"SELECT seq_type_lb from sequence where seq_cdn = '$seq'","seq_type_lb");
              if (strstr($type_sequence,"SCORM")){
                 $scormOk = 1;
                 $icono = "<IMG SRC='images/gest_parc/scorm.gif' border='0' alt=\"$mess_seq_sco\">";
              }else{
                 $scormOk = 0;
                 $icono = "";
              }
              $etat = mysql_result ($seq_query,$i,"suiv2_etat_lb");
              $desc_seq = GetDataField ($connect,"select seq_desc_cmt from sequence where seq_cdn=$seq","seq_desc_cmt");
              $formateur = GetDataField ($connect,"select presc_formateur_no from prescription$Ext where
                                                   presc_seq_no = $seq and
                                                   presc_utilisateur_no = $num_app and
                                                   presc_grp_no = $numero_groupe","presc_formateur_no");
              $prescripteur = GetDataField ($connect,"select presc_prescripteur_no from prescription$Ext where
                                                      presc_seq_no = $seq and
                                                      presc_utilisateur_no = $num_app and
                                                      presc_grp_no = $numero_groupe","presc_prescripteur_no");
              $nom_form=GetDataField ($connect,"select util_nom_lb from utilisateur where util_cdn='$formateur'","util_nom_lb");
              $prenom_form=GetDataField ($connect,"select util_prenom_lb from utilisateur where util_cdn='$formateur'","util_prenom_lb");
              //$proposable est un flag qui sert a savoir si la seq est proposable ou non
              // => 0 : NON     => 1 : OUI
              //on regarde prerequis => sequence proposable ou non
              $prerequis="";
              $prereq_query = mysql_query ("select * from prerequis where prereq_seq_no = $seq");
              $nb_prereq = mysql_num_rows ($prereq_query);
              if ($etat == "TERMINE")
                 $compteur_fin_seq++;
              if ($compteur_fin_seq == $nb_seq && $i+1 == $nb_seq)
                 $fin_parc=1;
              if ($nb_prereq != 0) {
                $nb_proposable = 0;
                $prereq_not = 0;
                $jj = 0;
                $affiche_prereq = "<font size=2><B>$msq_prereq</B></font><BR>";// initialisation de l'affichage
                while ($jj < $nb_prereq){
                  //on raisonne selon le type de condition
                  $type_condition = mysql_result ($prereq_query,$jj,"prereq_typcondition_lb");
                  if ($type_condition == 'SEQUENCE') {
                      $condition = mysql_result ($prereq_query,$jj,"prereq_seqcondition_no");
                      $verif_seq_req = mysql_query ("select count(presc_seq_no) from prescription$Ext where
                                                     presc_seq_no = $condition and
                                                     presc_utilisateur_no = $num_app and
                                                     presc_grp_no = $numero_groupe");
                      $nb_verif_req = mysql_result($verif_seq_req,0);
                      if ($nb_verif_req > 0 ){
                        $etat_seq_req = GetDataField ($connect,"select suiv2_etat_lb from suivi2$Ext where
                                                                suiv2_seq_no = $condition and
                                                                suiv2_utilisateur_no = $num_app and
                                                                suiv2_grp_no = $numero_groupe","suiv2_etat_lb");
                        $nom_seq_req = GetDataField ($connect,"select seq_titre_lb from sequence where seq_cdn = $condition","seq_titre_lb");
                        $affiche_prereq .= "<B>$msq_seq</B> : $nom_seq_req<BR>";
                        if ($etat_seq_req != 'TERMINE')  {
                          $motif = $mess_gp_seq_preq;
                          $proposable = 0;
                          $prerequis = "";
                        }else
                          $nb_proposable++;
                      }else
                          $prereq_not++;

                  }

                  //on a besoin du numero de l'activite pour recuperer les notes
                  if ($type_condition == 'ACTIVITE') {
                      $condition = mysql_result ($prereq_query,$jj,"prereq_actcondition_no");
                      $num_seq_req = GetDataField ($connect,"select act_seq_no from activite where act_cdn = $condition","act_seq_no");
                      $verif_seq_req = mysql_query ("select count(presc_seq_no) from prescription$Ext where
                                                     presc_seq_no = $num_seq_req and
                                                     presc_utilisateur_no = $num_app and
                                                     presc_grp_no = $numero_groupe");
                      $nb_verif_req = mysql_result($verif_seq_req,0);
                      if ($nb_verif_req > 0 ){
                        $etat_act_req = GetDataField ($connect,"select suivi_etat_lb from suivi1$Ext where
                                                                suivi_act_no = $condition and
                                                                suivi_utilisateur_no = $num_app and
                                                                suivi_grp_no = $numero_groupe","suivi_etat_lb");
                        if ($etat_act_req != 'TERMINE') {
                           $nom_act_req = GetDataField ($connect,"select act_nom_lb from activite where act_cdn = $condition","act_nom_lb");
                           $nom_seq_req = GetDataField ($connect,"select seq_titre_lb from sequence,activite where act_cdn = $condition and act_seq_no= seq_cdn","seq_titre_lb");
                           $affiche_prereq .= "<B>$msq_activite</B> -->($msq_seq : $nom_seq_req) : <U>$nom_act_req</U><BR>";
                           $motif = $mess_gp_aut_act;
                           $proposable = 0;
                           $prerequis = $mess_act_prq." <B>".str_replace("'","|",$act_prereq)."</B> ".$mess_no_fin;
                        }else
                          $nb_proposable++;
                      }else
                          $prereq_not++;
                  }

                  if ($type_condition == 'NOTE') {
                     $condition = mysql_result ($prereq_query,$jj,"prereq_actcondition_no");
                     $num_seq_req = GetDataField ($connect,"select act_seq_no from activite where act_cdn = $condition","act_seq_no");
                     $verif_seq_req = mysql_query ("select count(presc_seq_no) from prescription$Ext where
                                                    presc_seq_no = $num_seq_req and
                                                    presc_utilisateur_no = $num_app and
                                                    presc_grp_no = $numero_groupe");
                     $nb_verif_req = mysql_result($verif_seq_req,0);
                     if ($nb_verif_req > 0 ){
                        $etat_act_req = GetDataField ($connect,"select suivi_etat_lb from suivi1$Ext where
                                                                suivi_act_no = $condition and
                                                                suivi_utilisateur_no = $num_app and
                                                                suivi_grp_no = $numero_groupe","suivi_etat_lb");
                        $nom_act_req = GetDataField ($connect,"select act_nom_lb from activite where act_cdn = $condition","act_nom_lb");
                        if ($etat_act_req != 'TERMINE') {
                          $motif = $mess_gp_act_preq;
                          $proposable = 0;
                        }else{
                          $note_min = mysql_result ($prereq_query,$jj,"prereq_notemin_nb1");
                          $note_max = mysql_result ($prereq_query,$jj,"prereq_notemax_nb1");
                          $note_obtenue = GetDataField ($connect,"select suivi_note_nb1 from suivi1$Ext where
                                                                  suivi_act_no = $condition and
                                                                  suivi_utilisateur_no = $num_app and
                                                                  suivi_grp_no = $numero_groupe"  ,"suivi_note_nb1");
                          if (($note_obtenue < $note_min && is_integer($note_obtenue)) || $note_obtenue == $mess_suivi_noacq || $note_obtenue == "D" || $note_obtenue == "E")  {
                             $motif = $mess_gp_note;
                             $proposable = 0;
                             $prerequis = $mess_note_prq;
                             $affiche_prereq .= "<B>$msq_note</B> --> $msq_activite : $nom_act_req<BR>";
                          }else
                             $nb_proposable++;
                        }
                     }else
                          $prereq_not++;//finif ($nb_verif_req > 0 )
                  }
              $jj++;
              }
              if (($nb_proposable == $nb_prereq) || (($nb_proposable+$prereq_not) == $nb_prereq)){
                 $proposable=1;
                 $prerequis="OK";
              }else
                 $proposable = 0;
            }else //fin if ($nb_prereq !=0)
                $proposable = 1;
               $act_query = mysql_query ("select * from activite where act_seq_no = $seq order by act_ordre_nb");
               $Nb_act_seq = mysql_num_rows ($act_query);
               $aq = 0;
               $encore = 0;
           if ($utilisateur > 0){
              $date_deb = GetDataField ($connect,"select presc_datedeb_dt from prescription$Ext where
                                                  presc_seq_no = $seq and
                                                  presc_utilisateur_no = $utilisateur and
                                                  presc_grp_no = $numero_groupe","presc_datedeb_dt");
              $date_fin = GetDataField ($connect,"select presc_datefin_dt from prescription$Ext where
                                                  presc_seq_no = $seq and
                                                  presc_utilisateur_no = $utilisateur and
                                                  presc_grp_no = $numero_groupe","presc_datefin_dt");
            }else{
              $date_deb = GetDataField ($connect,"select presc_datedeb_dt from prescription$Ext where
                                                  presc_seq_no = $seq and
                                                  presc_utilisateur_no = $id_user and
                                                  presc_grp_no = $numero_groupe","presc_datedeb_dt");
              $date_fin = GetDataField ($connect,"select presc_datefin_dt from prescription$Ext where
                                                  presc_seq_no = $seq and
                                                  presc_utilisateur_no = $id_user and
                                                  presc_grp_no = $numero_groupe","presc_datefin_dt");
            }
              $today = date("Y/n/d");
              //On compare la date de deb a la date d'aujourd'hui pour savoir s'il peut encore faire la sequence
              //Pour simplifier le test, on selctionne le nombre de jours passes depuis an 0 pour $today et $date_deb
              $nb_date_fin_query = mysql_query ("select TO_DAYS('$date_fin')");
              $nb_date_fin = mysql_result ($nb_date_fin_query,0);
              $nb_date_deb_query = mysql_query ("select TO_DAYS('$date_deb')");
              $nb_date_deb = mysql_result ($nb_date_deb_query,0);
              $nb_today_query = mysql_query ("select TO_DAYS('$today')");
              $nb_today = mysql_result ($nb_today_query,0);
              for ($nn = 1;$nn < 10;$nn++){
                if ($nb_date_fin == ($nb_today+$nn) && $etat != "TERMINE") {
                   $avertisseur = 1;
                }
              }
              $depasse=0;

              $ch_date_deb = explode ("-",$date_deb);
              $date_deb = "$ch_date_deb[2]/$ch_date_deb[1]/$ch_date_deb[0]";
              $nom = GetDataField ($connect,"select seq_titre_lb from sequence where seq_cdn=$seq","seq_titre_lb");
              $nom_seq = $nom;
              if ($scormOk == 1)
                 $img_sco = "<IMG SRC='images/gest_parc/scorm.gif' border='0' alt=\"$mess_seq_sco\">";
              else
                 $img_sco = "<IMG SRC='images/spacer.gif' border='0'>";
              if ($utilisateur > 0){
                  $date_fin = GetDataField ($connect,"select presc_datefin_dt from prescription$Ext where
                                                      presc_seq_no = $seq and
                                                      presc_utilisateur_no = $utilisateur and
                                                      presc_grp_no = $numero_groupe","presc_datefin_dt");
                  if ($scormOk == 1)
                    $duree_seq = GetDataField ($connect,"SELECT seq_duree_nb from sequence where seq_cdn = '$seq'","seq_duree_nb");
                  else{
                    $duree_sequence = mysql_query ("select sum(activite.act_duree_nb) from activite,suivi1$Ext where
                                                    activite.act_cdn = suivi1$Ext.suivi_act_no and
                                                    activite.act_seq_no=$seq and
                                                    suivi1$Ext.suivi_utilisateur_no =$utilisateur and
                                                    suivi1$Ext.suivi_grp_no = $numero_groupe");
                    $duree_seq = mysql_result($duree_sequence,0);
                  }
              }else{
                  $date_fin = GetDataField ($connect,"select presc_datefin_dt from prescription$Ext where
                                                      presc_seq_no = $seq and
                                                      presc_utilisateur_no = $id_user and
                                                      presc_grp_no = $numero_groupe","presc_datefin_dt");
                  if ($scormOk == 1)
                    $duree_seq = GetDataField ($connect,"SELECT seq_duree_nb from sequence where seq_cdn = '$seq'","seq_duree_nb");
                  else{
                    $duree_sequence = mysql_query ("select sum(activite.act_duree_nb) from activite,suivi1$Ext where
                                                    activite.act_cdn = suivi1$Ext.suivi_act_no and
                                                    activite.act_seq_no=$seq and
                                                    suivi1$Ext.suivi_utilisateur_no =$id_user and
                                                    suivi1$Ext.suivi_grp_no = $numero_groupe");
                    $duree_seq = mysql_result($duree_sequence,0);
                  }
              }
              $ch_date_fin = explode ("-",$date_fin);
              $date_fin = "$ch_date_fin[2]/$ch_date_fin[1]/$ch_date_fin[0]";
              if ($nb_date_fin < $nb_today && $etat != "TERMINE"){
                 $depasse=1;
                 $motif = "$mess_acces_depasse : $date_fin";
              }
              if ($nb_date_fin < $nb_today && $etat == "TERMINE")
                 $autorise=1;
              if ($nb_date_deb <= $nb_today)
                  $visible = 1;
              else
                  $visible = 0;

                  //Besoin pour ouvrir les activites a partir de la sequence
               $id_ref = GetDataField ($connect,"select seqref_referentiel_no from sequence_referentiel where seqref_seq_no = $seq","seqref_referentiel_no");
               if ($id_ref > 0){
                   $nom_referentiel = GetDataField ($connect,"select ref_desc_cmt from referentiel where ref_cdn = $id_ref","ref_desc_cmt");
                   if ($nom_referentiel == "")
                      $nom_referentiel = GetDataField ($connect,"select ref_nomabrege_lb from referentiel where ref_cdn = $id_ref","ref_nomabrege_lb");
               }
                  $ii=$i-1;
                  if ($nom_user == "Test")
                     $proposable = 1;
                  if (($autorise == 1 || $depasse != 1) && (($nb_prereq > 0 && $proposable == 1) || $nb_prereq == 0) && ((($marqueur == 1 && $marqueur[$ii] != 1) || $encore ==0) || ($utilisateur))){
                      $accord = 1;
                      if ((isset($deroule[$i]) && $deroule[$i] == 1 && $id_seq == $seq) || ($seq == $seq_ouverte && $seq_ouverte > 0) && $switch != 1)
                         $deroulee[$i] = 0;
                      else
                         $deroulee[$i] = 1;
                      if (isset($deroule[$i]) && $deroule[$i] == 1 || ($seq == $seq_ouverte && $seq_ouverte>0) || $tout == 1)
                      {
                        echo "<TR bgcolor='#E1E0DF'>";
                        $lien = "gest_parc.php?formation=$formation&numero_groupe=$numero_groupe&depasse=$depasse&visible=$visible&proposable=$proposable&hgrp=$hgrp&accord=$accord&id_seq=$seq&parc=$id_parc&deroule[$i]=0&deroule_parc[$p]=1&utilisateur=$utilisateur&a_faire=1";
                        $lien = urlencode($lien);
                        echo "<TD bgcolor='#EFEFEF' align='center' valign='top'><A HREF=\"trace.php?link=$lien\" target='main' title=\"$mess_gp_masq_det_seq\"><IMG SRC='images/gest_parc/flech02.gif' border='0' alt=\"$mess_gp_masq_det_seq\"></A></TD><TD align='left'><DIV id='sequence'>";
                      }
                      else
                      {
                        echo "<TR bgcolor= '#EFEFEF'>";
                        $lien = "gest_parc.php?formation=$formation&numero_groupe=$numero_groupe&depasse=$depasse&proposable=$proposable&visible=$visible&hgrp=$hgrp&accord=$accord&id_seq=$seq&parc=$id_parc&deroule[$i]=1&deroule_parc[$p]=1&utilisateur=$utilisateur&a_faire=1";
                        $lien = urlencode($lien);
                        echo "<TD align='center' valign='top'><a href=\"trace.php?link=$lien\" target='main' title=\"$mess_gp_aff_det_seq\"".
                           " onmouseover=\"img_fl$compte.src='images/gest_parc/flech01b.gif';return true;\"".
                           " onmouseout=\"img_fl$compte.src='images/gest_parc/flech01.gif'\">".
                           "<IMG NAME=\"img_fl$compte\" SRC=\"images/gest_parc/flech01.gif\" border='0' valign='top' alt=\"$mess_gp_aff_det_seq\"".
                           " onLoad=\"tempImg=new Image(0,0); tempImg.src='images/gest_parc/flech01b.gif'\"></A></TD><TD align='left'><DIV id='sequence'>";
                      }
                      $lien="gest_parc.php?formation=$formation&numero_groupe=$numero_groupe&depasse=$depasse&visible=$visible&proposable=$proposable&hgrp=$hgrp&accord=$accord&id_seq=$seq&parc=$id_parc&deroule[$i]=$deroulee[$i]&deroule_parc[$p]=1&utilisateur=$utilisateur&a_faire=1";
                      $lien = urlencode($lien);
                      if (isset($deroule[$i]) && $deroule[$i] == 1 || ($seq == $seq_ouverte && $seq_ouverte>0) || $tout == 1)
                         echo  "<FONT COLOR='#000000'><A HREF=\"trace.php?link=$lien\" target='main' TITLE=\"$mess_gp_masq_det_seq\">$img_sco $nom</A>&nbsp;&nbsp;&nbsp;";
                      else
                         echo  "<A HREF=\"trace.php?link=$lien\" target='main' onMouseOver=\"overlib('$mess_gp_der_act_seq <B>".
                               "<Font color=#686840>$prenom_form  $nom_form</font></B>',ol_hpos,RIGHT,ABOVE,WIDTH,'200',CAPTION,'<center>".
                               "$mess_gp_seq_afaire</center>')\" onMouseOut=\"nd()\" TITLE=\"$mess_gp_masq_det_seq\">$icono $nom</A>".
                               "&nbsp;&nbsp;&nbsp;";
                      $lien = "favoris.php?numero_groupe=$numero_groupe&hgrp=$hgrp&ajouter=1&seq=$seq";
                      $lien = urlencode($lien);

                      if ($visible == 0){
                        $motif = "&nbsp;$msq_mess_deb_apres";
                        echo "$motif";
                      }
                      if ($seq > 0)
                      {
                         $id_ref_seq = GetDataField ($connect,"select seqref_referentiel_no from sequence_referentiel where seqref_seq_no = '$seq'","seqref_referentiel_no");
                         if ($id_ref_seq > 0)
                         {
                            $desc_ref = GetDataField ($connect,"select ref_desc_cmt from referentiel where ref_cdn = '$id_ref_seq'","ref_desc_cmt");
                            if ($desc_ref == "")
                               $desc_ref = GetDataField ($connect,"select ref_nomabrege_lb from referentiel where ref_cdn = $id_ref_seq","ref_nomabrege_lb");
                        }
                        else
                            $desc_ref = "";

                      }
                      echo "<BR>".html_entity_decode($desc_seq,ENT_QUOTES,'ISO-8859-1')."</FONT>";
                      if ($desc_ref != "")
                         echo "<BR><U>$msq_act_ref</U> ".html_entity_decode($desc_ref,ENT_QUOTES,'ISO-8859-1');
                      echo "</DIV></TD>";

                 }
                 else
                 {
                      $accord = 0;
                      if (isset($deroule[$i]) && $deroule[$i] == 1 || ($seq == $seq_ouverte && $seq_ouverte>0) || $tout == 1)
                      {
                         $lien = "gest_parc.php?formation=$formation&numero_groupe=$numero_groupe&depasse=$depasse&visible=$visible&hgrp=$hgrp&accord=$accord&utilisateur=$utilisateur&a_faire=1&id_seq=$seq&parc=$id_parc&deroule[$i]=0&deroule_parc[$p]=1";
                         $lien = urlencode($lien);
                         echo "<TR bgcolor='#E1E0DF'><TD align='center' valign='top'><A HREF=\"trace.php?link=$lien\" target='main' TITLE=\"$mess_gp_masq_det_seq\"><IMG SRC='images/gest_parc/flech02.gif' border='0' alt=\"$mess_gp_masq_det_seq\"></A></TD>";
                      }
                      else
                      {
                         $lien = "gest_parc.php?formation=$formation&numero_groupe=$numero_groupe&depasse=$depasse&visible=$visible&hgrp=$hgrp&accord=$accord&utilisateur=$utilisateur&a_faire=1&id_seq=$seq&parc=$id_parc&deroule[$i]=1&deroule_parc[$p]=1";
                         $lien = urlencode($lien);
                         echo "<TR bgcolor='#EFEFEF'><TD align='center' valign='top'><A HREF=\"trace.php?link=$lien\" target='main' TITLE=\"$mess_gp_aff_det_seq\"".
                              " onmouseover=\"img_fl$compte.src='images/gest_parc/flech01b.gif';return true;\"".
                              " onmouseout=\"img_fl$compte.src='images/gest_parc/flech01.gif'\">".
                              "<IMG NAME=\"img_fl$compte\" SRC=\"images/gest_parc/flech01.gif\" border='0' valign='top' alt=\"$mess_gp_aff_det_seq\"".
                              " onLoad=\"tempImg=new Image(0,0); tempImg.src='images/gest_parc/flech01b.gif'\"></A></TD>";
                      }
                      if ($seq > 0)
                      {
                         $id_ref_seq = GetDataField ($connect,"select seqref_referentiel_no from sequence_referentiel where seqref_seq_no = '$seq'","seqref_referentiel_no");
                         if ($id_ref_seq > 0){
                           $desc_ref = GetDataField ($connect,"select ref_desc_cmt from referentiel where ref_cdn = '$id_ref_seq'","ref_desc_cmt");
                           if ($desc_ref == "")
                              $desc_ref = GetDataField ($connect,"select ref_nomabrege_lb from referentiel where ref_cdn = $id_ref_seq","ref_nomabrege_lb");
                         }else
                            $desc_ref = "";
                      }
                      if (isset($deroule[$i]) && $deroule[$i] == 1 || ($seq == $seq_ouverte && $seq_ouverte>0) || $tout == 1)
                      {
                        $lien = "gest_parc.php?formation=$formation&numero_groupe=$numero_groupe&depasse=$depasse&visible=$visible&hgrp=$hgrp&accord=$accord&utilisateur=$utilisateur&a_faire=1&id_seq=$seq&parc=$id_parc&deroule[$i]=0&deroule_parc[$p]=1";
                        $lien = urlencode($lien);
                        echo "<TD align='left' valign='top'><DIV id='sequence'><A HREF=\"trace.php?link=$lien\" target='main' TITLE=\"$mess_gp_masq_det_seq\">$nom</a>";
                      }
                      else
                      {
                        $lien = "gest_parc.php?formation=$formation&numero_groupe=$numero_groupe&depasse=$depasse&visible=$visible&hgrp=$hgrp&accord=$accord&utilisateur=$utilisateur&a_faire=1&id_seq=$seq&parc=$id_parc&deroule[$i]=1&deroule_parc[$p]=1";
                        $lien = urlencode($lien);
                        echo "<TD align='left' valign='top'><DIV id='sequence'><A HREF=\"trace.php?link=$lien\" target='main' onMouseOver=\"overlib('$mess_gp_der_act_seq <B><Font color=#686840>$prenom_form  $nom_form</font></B>',ol_hpos,RIGHT,ABOVE,WIDTH,'200')\" onMouseOut=\"nd()\" TITLE=\"$mess_gp_aff_det_seq\">$nom</A>";
                      }
                      echo " &nbsp;&nbsp;$motif<BR>".html_entity_decode($desc_seq,ENT_QUOTES,'ISO-8859-1');
                      if ($desc_ref != "")
                         echo "<BR><U>$msq_act_ref</U>  ".html_entity_decode($desc_ref,ENT_QUOTES,'ISO-8859-1');
                      echo "</DIV></TD>";
                  }
                  if ($nb_prereq == 0 || $prereq_not == $nb_prereq)
                    echo "<TD align='left' nowrap valign='top'>&nbsp;&nbsp;$mess_gp_aucun&nbsp;&nbsp;</TD>";
                  else
                  {
                    $nom_prereq = GetDataField ($connect,"select seq_titre_lb from sequence where seq_cdn=$condition","seq_titre_lb");
                    $lien="sequence.php?numero_groupe=$numero_groupe&venir=g_p&hgrp=$hgrp&utilisateur=$utilisateur&seq_ouverte=$seq&parc_ouvert=$id_parc&aff_prereq=1&parcours=$parcours&id_parc=$id_parc&id_seq=$seq&id_ref=$id_ref&titre=$nom&id_apprenant=$id_user";
                    $lien = urlencode($lien);
                    if (isset($deroule[$i]) && $deroule[$i] == 1 || ($seq == $seq_ouverte && $seq_ouverte>0) || $tout == 1)
                       echo "<TD align='left' nowrap valign='top'>$msq_prereq</TD>";
                    else
                       echo "<TD align='left' nowrap valign='top'><strong>$msq_prereq</strong></TD>";
                  }
                  $duree = duree_calc($duree_seq);
                  if (isset($deroule[$i]) && $deroule[$i] == 1 && $id_seq == $seq)
                     echo "<TD align='left' nowrap valign='top'>&nbsp;&nbsp;$duree&nbsp;&nbsp;</TD>";
                  else
                     echo "<TD align='left' nowrap valign='top'>&nbsp;&nbsp;$duree&nbsp;&nbsp;</TD>";
                  $date_debut = $date_deb;
                  if (isset($deroule[$i]) && $deroule[$i] == 1 && $id_seq == $seq)
                     echo "<TD align='left' nowrap valign='top'>&nbsp;&nbsp;$date_debut&nbsp;&nbsp;</TD>";
                  else
                     echo "<TD align='left' nowrap valign='top'>&nbsp;&nbsp;$date_debut&nbsp;&nbsp;</TD>";
                  $date_finale = $date_fin;
                  if (isset($deroule[$i]) && $deroule[$i] == 1 && $id_seq == $seq)
                     echo "<TD align='left' nowrap valign='top'>&nbsp;&nbsp;$date_finale&nbsp;&nbsp;</TD>";
                  else
                     echo "<TD align='left' nowrap valign='top'>&nbsp;&nbsp;$date_finale&nbsp;&nbsp;</TD>";
                   if ($etat == "TERMINE")
                     $actsuiv = $mess_lanc_termine;
                   elseif ($etat == "A FAIRE")
                     $actsuiv = $mess_lanc_afaire;
                   elseif ($etat == "EN COURS")
                     $actsuiv = $mess_lanc_encours;
                   elseif ($etat == "ATTENTE")
                     $actsuiv = $mess_lanc_attente;
                   $seq_suivi = $actsuiv;
                  if ($depasse == 1 && $etat != "TERMINE")
                     $etat = "&nbsp;&nbsp;<font color=red>$mess_gp_hsd&nbsp;&nbsp;</font>";
                  if ($avertisseur == 1 && isset($deroule[$i]) && $deroule[$i] == 1 && $id_seq == $seq)
                  {
                    echo "<TD align='left' nowrap valign='top'><B>&nbsp;&nbsp;$seq_suivi&nbsp;&nbsp;</B></TD>";
                    $avertisseur=0;
                  }
                  elseif ($avertisseur == 1 && isset($deroule[$i]) && $deroule[$i] == 0) {
                    echo "<TD align='left' nowrap valign='top'>&nbsp;&nbsp;$seq_suivi&nbsp;&nbsp;</TD>";
                    $avertisseur=0;
                  }
                  elseif (isset($deroule[$i]) && $deroule[$i] == 1 && $id_seq == $seq)
                     echo "<TD align='left' nowrap valign='top'><B>&nbsp;&nbsp;$seq_suivi&nbsp;&nbsp;</B></TD>";
                  else
                     echo "<TD align='left' nowrap valign='top'>&nbsp;&nbsp;$seq_suivi&nbsp;&nbsp;</TD>";
              if ($typ_user == 'APPRENANT' && ((isset($deroule[$i]) && $deroule[$i] == 1) || $id_seq == $seq))
              {
                 if (!isset($deroule[$p]))
                     $deroule[$p] ='';
                 if (!isset($deroule[$i]))
                     $deroule[$i] ='';
                $der_details = "details_parc.php?numero_groupe=$numero_groupe&depasse=$depasse&hgrp=$hgrp&prq=$prerequis&accord=$accord&depasse=$depasse&visible=$visible&id_parc=$id_parc&seq=$seq&ouvrir=sequence&seq_ouverte=$seq&parc_ouvert=$id_parc";
                $der_gest = "gest_parc1.php?numero_groupe=$numero_groupe&depasse=$depasse&visible=$visible&hgrp=$hgrp&prq=$prerequis&a_faire=1&seq_ouverte=$seq&parc_ouvert=$id_parc&actif_seq=$seq_actif&actif_parc=$parc_actif&id_seq=$seq&parc=$id_parc&deroule[$i]=$deroulee[$i]&deroule_parc[$p]=$deroule_parc[$p]";
                $req = mysql_query("select traceur_cdn from traceur where traceur_util_no = $id_user AND traceur_grp_no = $numero_groupe");
                $nbr_trac = mysql_num_rows($req);
                $date_cour = date ("Y/n/d");
                if ($nbr_trac == 1)
                {
                   $requete = mysql_query("update traceur set traceur_der_details = \"$der_details\",traceur_der_gest1 = \"$der_gest\",traceur_date_dt = \"$date_cour\" where traceur_util_no = $id_user AND traceur_grp_no = $numero_groupe");
                }
                else
                {
                   $id_traceur = Donne_ID ($connect,"select max(traceur_cdn) from traceur");
                   $requete = mysql_query("insert into traceur (traceur_cdn,traceur_util_no,traceur_der_details,traceur_der_gest1,traceur_date_dt,traceur_grp_no) values($id_traceur,$id_user,\"$der_details\",\"$der_gest\",\"$date_cour\",$numero_groupe)");
                }
              }
              if ($utilisateur > 0)
              {
                 $prescripteur = GetDataField ($connect,"select presc_prescripteur_no from prescription$Ext where
                                                         presc_seq_no = $seq and
                                                         presc_utilisateur_no = $utilisateur and
                                                         presc_grp_no = $numero_groupe","presc_prescripteur_no");
                 $inscripteur = GetDataField ($connect,"select util_auteur_no from utilisateur where util_cdn = $utilisateur","util_auteur_no");
                 $formateur = GetDataField ($connect,"select presc_formateur_no from prescription$Ext where
                                                      presc_seq_no = $seq and
                                                      presc_utilisateur_no = $utilisateur and
                                                      presc_grp_no = $numero_groupe","presc_formateur_no");
                 $sans_prereq = mysql_query ("select prereq_seq_no from prerequis where prereq_seqcondition_no = $seq");
                 $nb_sans_prereq = mysql_num_rows ($sans_prereq);
                 $signal=0;
                 if ($nb_sans_prereq > 0)
                 {
                    $sequence = mysql_result($sans_prereq,0,"prereq_seq_no");
                    $seq_cible = mysql_query ("select suiv2_seq_no from suivi2$Ext where
                                               suiv2_utilisateur_no = $utilisateur and
                                               suiv2_grp_no = $numero_groupe");
                    $nb_seq_cible = mysql_num_rows($seq_cible);
                    $nsc=0;
                    while ($nsc < $nb_seq_cible){
                      $sequence_cible = mysql_result($seq_cible,$nsc,"suiv2_seq_no");
                      if ($sequence == $sequence_cible){
                        $signal++;
                      }
                    $nsc++;
                    }
                 }
              }//else
       echo "</TR>";
  if (((isset($deroule[$i]) && $deroule[$i] == 1 && $id_seq == $seq) || $tout == 1) || (isset($vp) && $vp == 1 && $seq == $seq_ouverte))
  {
    $ouvrir = '';
    $seq_ouverte = $seq;
    if ($typ_user == 'APPRENANT')
    {
      if ($scormOk == 1)
         $act_query = mysql_query ("select * from scorm_module,scorm_util_module$Ext WHERE
                                    scorm_module.mod_seq_no = $seq AND
                                    scorm_util_module$Ext.mod_module_no = scorm_module.mod_cdn AND
                                    scorm_util_module$Ext.user_module_no = $id_user AND
                                    scorm_util_module$Ext.mod_grp_no = $numero_groupe
                                    order by scorm_module.mod_cdn");
      else
         $act_query = mysql_query ("select * from activite,suivi1$Ext WHERE
                                    (activite.act_seq_no = $seq OR suivi_seqajout_no = $seq) AND
                                    suivi1$Ext.suivi_act_no = activite.act_cdn AND
                                    suivi1$Ext.suivi_utilisateur_no = $id_user and
                                    suivi1$Ext.suivi_grp_no = $numero_groupe
                                    order by suivi1$Ext.suivi_seqajout_no,activite.act_ordre_nb");
    }
    else
    {
      if ($scormOk == 1)
         $act_query = mysql_query ("select * from scorm_module,scorm_util_module$Ext WHERE
                                    scorm_module.mod_seq_no = $seq AND
                                    scorm_util_module$Ext.mod_module_no = scorm_module.mod_cdn AND
                                    scorm_util_module$Ext.user_module_no = $utilisateur AND
                                    scorm_util_module$Ext.mod_grp_no = $numero_groupe
                                    order by scorm_module.mod_cdn");
      else
         $act_query = mysql_query ("select * from activite,suivi1$Ext WHERE
                                    (activite.act_seq_no = $seq OR suivi_seqajout_no = $seq) AND
                                    suivi1$Ext.suivi_act_no = activite.act_cdn AND
                                    suivi1$Ext.suivi_utilisateur_no = $utilisateur and
                                    suivi1$Ext.suivi_grp_no = $numero_groupe
                                    order by suivi1$Ext.suivi_seqajout_no,activite.act_ordre_nb");
    }
    $Nb_act_seq = mysql_num_rows ($act_query);
    if ($Nb_act_seq == 0)
        echo "<tr><td></td><td colspan='7' width='100%'><FONT COLOR=RED><B>$msq_noact.</B></FONT></td></tr>";
    else
    {
         if ($utilisateur > 0)
         {
           $id_nom = GetDataField ($connect,"select util_nom_lb from utilisateur where util_cdn = $utilisateur","util_nom_lb");
           $id_prenom = GetDataField ($connect,"select util_prenom_lb from utilisateur where util_cdn = $utilisateur","util_prenom_lb");
           $id_app = $utilisateur;
         }
         else
           $id_app = $id_user;
         $suivi_etat_seq = GetDataField ($connect,"select suiv2_etat_lb from suivi2$Ext where
                                                   suiv2_seq_no = '$seq' and
                                                   suiv2_utilisateur_no = $id_app and
                                                   suiv2_grp_no = $numero_groupe","suiv2_etat_lb");
         $titre_sequence = GetDataField ($connect,"select seq_titre_lb from sequence where seq_cdn = '$seq'","seq_titre_lb");
         $nom_sequence = GetDataField ($connect,"select seq_desc_cmt from sequence where seq_cdn = '$seq'","seq_desc_cmt");
         if ($nom_sequence == '')
           $nom_sequence = $msq_no_decrite;
          $bgcolorC = "bgcolor='#2B677A'";
           if ($utilisateur > 0)
             echo "<TR><TD colspan='8' align='left' width='100%'>";
           else
             echo "<TR><TD colspan='7' align='left' width='100%'>";
           echo "<TABLE border='0' width='100%' cellspacing='1' cellpadding='3'>";
           echo "<TR height='30'>";
           echo "<TD align='left' width='2%' bgcolor='#EFEFEF'>&nbsp;</TD>";
       if ($scormOk == 0)
       {
           echo "<TD align='left' width='20%' $bgcolorC><FONT COLOR=white><b>$msq_acts </b></FONT></TD>";
           if ($utilisateur > 0)
               echo "<TD align='left' width='2%' $bgcolorC><FONT COLOR=white><b>$msq_aff_ordre </b></FONT></TD>";
           echo "<TD align='left' width='40%' $bgcolorC><FONT COLOR=white><b>$msq_aff_cons </b></FONT></TD>";
           echo "<TD align='left' width='5%' $bgcolorC><FONT COLOR=white><b>$msq_duree_seq </b></FONT></TD>";
           echo "<TD align='left' width='20%' $bgcolorC><FONT COLOR=white><b>$msq_ress_assoc </b></FONT></TD>";
           echo "<TD align='left' width='5%' $bgcolorC><FONT COLOR=white><b>$msq_etat </b></FONT></TD>";
           echo "<TD align='left' width='4%' $bgcolorC><FONT COLOR=white><b>$msq_tit_label </b></FONT></TD>";
           echo "<TD align='left' width='5%' $bgcolorC><FONT COLOR=white><b>Nombre d'accès </b></FONT></TD>";
           echo "<TD align='left' width='4%' $bgcolorC><FONT COLOR=white><b>Temps consacré </b></FONT></TD>";
           echo "<TD align='left' width='4%' $bgcolorC><FONT COLOR=white><b>Dates d'accès </b></FONT></TD>";
           echo "</TR>";
          $ii = 0;
          while ($ii != $Nb_act_seq)
          {
               $id_suivi = mysql_result ($act_query,$ii,"suivi_cdn");
               $id = mysql_result ($act_query,$ii,"act_cdn");
               $nom = mysql_result ($act_query,$ii,"act_nom_lb");
               $ordre[$ii] = mysql_result ($act_query,$ii,"act_ordre_nb");
               $suivi_seqno = mysql_result ($act_query,$ii,"suivi_seqajout_no");
               $consigne = html_entity_decode(mysql_result ($act_query,$ii,"act_consigne_cmt"),ENT_QUOTES,'ISO-8859-1');
               $pass_mult = mysql_result ($act_query,$ii,"act_passagemult_on");
               $acquit = mysql_result ($act_query,$ii,"act_acquittement_lb");
               $devoir = mysql_result ($act_query,$ii,"act_devoirarendre_on");
               $notation = mysql_result ($act_query,$ii,"act_notation_on");
               $flag = mysql_result ($act_query,$ii,"act_flag_on");
               //dey Dfoad
               $req_typdev = mysql_result(mysql_query("select count(*) from activite_devoir where actdev_act_no = $id"),0);
               $dev_act = "";
               if ($req_typdev > 0)
               {
                 $dev_act = GetDataField ($connect,"select actdev_dev_lb from activite_devoir where
                                            actdev_act_no = $id ","actdev_dev_lb");
               }
               if (!$consigne)
                   $consigne = $msq_aucune;
               //Sï¿½lï¿½ction ressource
               $id_ress = mysql_result ($act_query,$ii,"act_ress_no");
               if ($id_ress > 0 && $utilisateur == "")
               {
                  $lien_ressource = GetDataField ($connect,"select ress_url_lb from ressource_new where ress_cdn = $id_ress","ress_url_lb");
                 if (strstr($lien_ressource,"forum/read.php?f=")){
                     $new_lien = str_replace("$adresse_http/forum/read.php?","",$lien_ressource);
                     list($uno,$duo,$trio) = explode("&",$new_lien);
                     list($titre_forum,$id_forum) = explode("=",$uno);
                     $comment_forum = GetDataField ($connect,"select name from forums where id='$id_forum'","name");
                     $id_grp = GetDataField ($connect,"select grp_cdn from groupe where grp_nom_lb =\"$comment_forum\"","grp_cdn");
                     if ($id_grp != $numero_groupe){
                       $requete = mysql_query("delete from suivi1$Ext where
                                               suivi_cdn = $id_suivi AND
                                               suivi_utilisateur_no = $id_user and
                                               suivi_grp_no = $numero_groupe");
                       if ($Nb_act_seq == 1)
                       {
                         $le_parc = GetDataField ($connect,"select presc_parc_no from prescription$Ext where
                                                            presc_seq_no = $seq AND
                                                            presc_utilisateur_no=$id_user AND
                                                            presc_grp_no=$numero_groupe","presc_parc_no");
                         $requete = mysql_query("delete from prescription$Ext where
                                                 presc_seq_no = $seq AND
                                                 presc_utilisateur_no=$id_user AND
                                                 presc_grp_no=$numero_groupe");
                         $requete = mysql_query("delete from suivi2$Ext where
                                                 suiv2_seq_no = $seq AND
                                                 suiv2_utilisateur_no=$id_user and
                                                 suiv2_grp_no = $numero_groupe");
                         $requete = mysql_query("SELECT count(presc_cdn) from prescription$Ext where
                                                 presc_parc_no = $le_parc AND
                                                 presc_utilisateur_no=$id_user and
                                                 presc_grp_no = $numero_groupe");
                         $nb_fois_parc = mysql_result($requete,0);
                         if ($nb_fois_parc == 0)
                           $requete = mysql_query("delete from suivi3$Ext where
                                                   suiv3_parc_no = $le_parc AND
                                                   suiv3_utilisateur_no = $id_user AND
                                                   suiv3_grp_no = $numero_groupe");
                         echo "<TR><TD valign='top'><IMG SRC='images/gest_parc/icoflashb.gif' border='0'></TD><TD colspan='4'><B>$mess_actforumno_ok</B></TD></TR>";
                       }
                        $ii++;
                       continue;
                     }
                  }
               }
               if ($id_ress > 0){
                  $lien_ressource = GetDataField ($connect,"select ress_url_lb from ressource_new where ress_cdn = $id_ress","ress_url_lb");
                  if (strstr($lien_ressource,"http://www.editions.educagri.fr/educagriNet"))
                    $lien_ressource.="&url=$url_ress&auth_cdn=$auth_cdn";
                  $lien_ress = str_replace("&","%",$lien_ressource);
                  $lr = 0;
                  if (strstr($lien_ress,"qcm.php"))
                     $lr = 1;
                  $non_affic_ress_lien = 0;
               }
               if ($id_ress == 0){
                   $non_affic_ress_lien = 1;
                   $ressource = $msq_aucune;
               }else {
                  $ressource = GetDataField ($connect,"select ress_titre from ressource_new where ress_cdn = $id_ress","ress_titre");
               //Dans le cas ou la ressource a ete supprimee
                   if ($ressource == ''){
                       $ressource = "<B>$msq_ress_sup</B>";
                       $non_affic_ress_lien = 1;
                   }else {
                       $typ_ress = GetDataField ($connect,"select ress_support from ressource_new where ress_cdn = $id_ress","ress_support");
                       $typ_ress = strtoupper ($typ_ress);  }
                } //fin else

               //Pour meme raison que typ_user, on selectionne auteur
               $id_auteur = GetDataField ($connect,"select seq_auteur_no from sequence where seq_cdn = $seq","seq_auteur_no");
               if ($utilisateur > 0)
                 $id_formateur = GetDataField ($connect,"select presc_formateur_no from prescription$Ext where
                                                         presc_seq_no = $seq and
                                                         presc_utilisateur_no=$utilisateur and
                                                         presc_grp_no = $numero_groupe","presc_formateur_no");

               //Si c'est l'apprenant qui consulte les seq et activites a faire
               //On doit savoir s'il doit effectuer activites ds l'ordre ou non pour activer ou non le lien
               if ($typ_user == 'APPRENANT') {
                   $id_apprenant = $id_user;
                   $ordre_act = GetDataField ($connect,"select seq_ordreact_on from sequence where seq_cdn = $seq","seq_ordreact_on");
                   //On connait l'ordre de l'activite courante  ($ordre)
                   if ($ordre[$ii] > 1 && $ii > 0 && $suivi_seqno == 0) {
                       //On recupere l'etat l'activite precedente
                       $ordre_prec = $ordre[$ii-1];
                       $act_prec =   GetDataField ($connect,"select act_cdn from activite where act_ordre_nb = $ordre_prec and act_seq_no = $seq","act_cdn");
                       $etat_act = GetDataField ($connect,"select suivi_etat_lb from suivi1$Ext where
                                                           suivi_act_no = $act_prec and
                                                           suivi_utilisateur_no = $id_apprenant and
                                                           suivi_grp_no = $numero_groupe","suivi_etat_lb");
                   }
               } //fin if ($apprenant == 1)
               $duree = mysql_result ($act_query,$ii,"act_duree_nb");
               //On passe certaines infos par URL, cela evite de refaire une requete
               //on affiche le lien pour un apprenant que si activite precedente terminee
               // GESTION DE L'ORDRE ET DE L'ETAT DES ACTIVITES POUR UN APPRENANT OU SON FORMATEUR
               echo "<TR height='30'><TD bgcolor='#EFEFEF' align='center' valign='top'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</TD>";
               $xapi = ($dev_act == 'xApi TinCan') ? '<IMG SRC="images/gest_parc/xApi.gif" border="0">&nbsp;' : '';
                  
               if ($etat_act == 'TERMINE' || $ordre[$ii] != 0 || $typ_user != "APPRENANT" || $ordre_act == "NON"){
                  echo "<TD bgcolor= '#DEE3E7' align='left' valign='top'>";
                   $lien= $lien_retour;
                   $lien = urlencode($lien);
                   $nom_act= str_replace("'","\'",$nom);
                   if ($pass_mult == "OUI")
                     $aff_pass_mult = $mess_oui;
                   elseif($pass_mult == "NON")
                     $aff_pass_mult = $mess_non;
                   if ($acquit == "FORMATEUR_REFERENT")
                     $aff_acquit = strtoupper($msq_formateur);
                   elseif ($acquit == "APPRENANT")
                     $aff_acquit = $mess_typ_app;
                   elseif ($acquit == "RESSOURCE")
                     $aff_acquit = strtoupper($msq_ress);
                   elseif ($acquit == "AUTO")
                     $aff_acquit = strtoupper($msq_auto);
                   if ($devoir == "OUI")
                     $aff_devoir = $mess_oui;
                   elseif($devoir == "NON")
                     $aff_devoir = $mess_non;
                   if ($notation == "OUI")
                     $aff_notation = $mess_oui;
                   elseif($notation == "NON")
                     $aff_notation = $mess_non;
                   echo "<DIV id='sequence'><a href=\"javascript:void(0);\" target='main' style=\"cursor: help;\"";
                   echo" onMouseOver=\"overlib('$msq_aff_pass_mult<B>$aff_pass_mult</B><BR>$msq_aff_acquit<B>$aff_acquit</B><BR>$msq_aff_dev_rend <B>$aff_devoir</B><BR>$msq_act_evalue <B>$aff_notation</B>',ol_hpos,RIGHT,WIDTH,'250')\" onMouseOut=\"nd()\">$xapi $nom</a></DIV></TD>";
               }else{
                  echo "<TD bgcolor= '#DEE3E7' align='left' valign='top'>$xapi $nom</TD>";
               }
               if ($utilisateur > 0){
                 if ($suivi_seqno == 0) $aff_ordre = $ordre[$ii]; else $aff_ordre = "<IMG SRC=\"images/plus_lav.gif\" height=\"12\" width=\"12\">";
                 echo "<TD bgcolor= '#DEE3E7' align='left' valign='top'>$aff_ordre</TD>";
               }
               echo "<TD bgcolor= '#DEE3E7' align='left' valign='top'>".html_entity_decode($consigne,ENT_QUOTES,'ISO-8859-1')."</TD>";
               $duree = duree_calc($duree);
               echo "<TD bgcolor= '#DEE3E7' align='left' valign='top' nowrap>$duree</TD>";
               if ($id_ress != 0) {
                         if ($utilisateur > 0)
                           $actform = GetDataField ($connect,"select presc_formateur_no from prescription$Ext where
                                                              presc_seq_no = $seq and
                                                              presc_utilisateur_no = $utilisateur and
                                                              presc_grp_no = $numero_groupe","presc_formateur_no");
                         else
                           $actform = GetDataField ($connect,"select presc_formateur_no from prescription$Ext where
                                                              presc_seq_no = $seq and
                                                              presc_utilisateur_no = $id_user and
                                                              presc_grp_no = $numero_groupe","presc_formateur_no");
                        $ordre_act = GetDataField ($connect,"select seq_ordreact_on from sequence where seq_cdn = $seq","seq_ordreact_on");
                         if ($utilisateur > 0){
                           $etat_act = GetDataField ($connect,"select suivi_etat_lb from suivi1$Ext where
                                                               suivi_act_no = $id and
                                                               suivi_utilisateur_no = $utilisateur and
                                                               suivi_grp_no = $numero_groupe","suivi_etat_lb");
                           $nom_user=GetDataField ($connect,"select util_nom_lb from utilisateur where util_cdn='$utilisateur'","util_nom_lb");
                           $prenom_user=GetDataField ($connect,"select util_prenom_lb from utilisateur where util_cdn='$utilisateur'","util_prenom_lb");
                         }else {
                           $etat_act = GetDataField ($connect,"select suivi_etat_lb from suivi1$Ext where
                                                               suivi_act_no = $id and
                                                               suivi_utilisateur_no = $id_user and
                                                               suivi_grp_no = $numero_groupe","suivi_etat_lb");
                           $email_user = GetDataField ($connect, "select util_email_lb from utilisateur where util_cdn=$id_user","util_email_lb");
                           $nom_user=GetDataField ($connect,"select util_nom_lb from utilisateur where util_cdn='$id_user'","util_nom_lb");
                           $prenom_user=GetDataField ($connect,"select util_prenom_lb from utilisateur where util_cdn='$id_user'","util_prenom_lb");
                         }
                         $lien_retour = "http://".$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
                         if ($etat_act == "TERMINE")
                           $fin_ordre_non++;
                         if ((($etat_act == "TERMINE" && $notation == "NON") || $etat_act == "A FAIRE" || $etat_act == "EN COURS") && $ordre[$ii] >0  && ($typ_user == "APPRENANT")) {
                          $marqueur[$ii] = 1;
                          $iii = $ii-1;
                          $lien_ressource = GetDataField ($connect,"select ress_url_lb from ressource_new where ress_cdn = $id_ress","ress_url_lb");
                          if ($lien_ressource == "")
                              $lien_ressource = "ress_virtuel.php?numero_groupe=$numero_groupe&id_ress=$id_ress&id_act=$id";
                          $lien_ress = str_replace("&","|",$lien_ressource);
                          if ((strstr($lien_ress,"ParWeb")) || (strstr($lien_ress,"parweb")) || (strstr($lien_ress,"Legweb")) || (strstr($lien_ress,"legweb")) || (strstr($lien_ress,"Tatweb"))  || (strstr($lien_ress,"tatweb")) || (strstr($lien_ress,"Qcmweb")) || (strstr($lien_ress,"qcmweb")) || (strstr($lien_ress,"Elaweb")) || (strstr($lien_ress,"elaweb")))
                             $lien_ress .= "%nom=$nom_user%prenom=$prenom_user%email=$email_user";
                          $lr = 0;
                          if (strstr($lien_ress,"qcm.php")){
                             $lien_ress .= "%id_act=$id";
                             $lr = 1;
                          }
                          if (strstr($lien_ress,"http://www.editions.educagri.fr/educagriNet"))
                             $lien_ress .="|url=$url_ress|auth_cdn=$auth_cdn";
                           if ($etat_act != "TERMINE" && $ordre_act == "OUI")
                              $pointeur++;
                           if ($ii == 0 && !$utilisateur)
                             $pointer = 1;
                           if ($Nb_act_seq == $ii+1 && $ordre_act == "OUI")
                             $fin_seq=1;
                           if ($Nb_act_seq == $ii+1 && $ordre_act == "NON" && $fin_ordre_non == $Nb_act_seq)
                             $fin_seq = 1;
                           if ($visible == 1 && (($depasse !=1 && $accord == 1) || (($depasse == 1 || $accord == 1) && $pass_mult == "OUI" && $etat_act == "TERMINE")) && (($etat_act == "TERMINE" && $notation == "NON" && $acquit != "AUTO") || ($pointeur == 1 && $ordre_act == "OUI" && ($etat_act == "A FAIRE" || ($etat_act == "EN COURS" && $pass_mult == "OUI"))) || ($login == "Test") || ($ordre_act == "NON" && ($etat_act == "A FAIRE" || ($etat_act == "EN COURS"  && $pass_mult == "OUI"))) || ($marqueur[$ii] != 1 && $etat_act == "A FAIRE"))){
                             $lien ="suivi.php?numero_groupe=$numero_groupe&hgrp=$hgrp&verif_lien_ress=1&fin_parc=$fin_parc&pointer=$pointer&id_seq=$seq&id_parc=$id_parc&fin_seq=$fin_seq&id_act=$id&lien_ress=$lien_ress";
                             if (strstr(strtolower($lien),".doc") || strstr(strtolower($lien),".xls") || strstr(strtolower($lien),".xlt") || strstr(strtolower($lien),".ppt") || strstr(strtolower($lien),".pps") || strstr(strtolower($lien),".txt") || strstr(strtolower($lien),".rtf") || strstr(strtolower($lien),".pdf"))
                               echo "<TD bgcolor= '#DEE3E7' align='left' valign='top'>$ressource</TD>";
                             else{
                               $lien = urlencode($lien);
                               echo "<TD bgcolor= '#DEE3E7' align='left' valign='top'>$ressource</TD>";
                             }
                           }else
                             echo "<TD bgcolor= '#DEE3E7' align='left' valign='top'>$ressource</TD>";//fin if (($pointeur == 1 && $ordre_act == "OUI") ||($ordre_act == "NON"))
                         }else{
                           echo "<TD bgcolor= '#DEE3E7' align='left' valign='top'>$ressource</TD>";
                         }//fin if (($etat_act == "A FAIRE" && $ordre >0 && ($typ_user == "NULL" || $id_user == $actform)))
                         $ress_type = GetDataField ($connect,"select ress_type from ressource_new where ress_cdn = $id_ress","ress_type");
                 }
                 elseif($id_ress == 0)
                 {
                      $ressource = ($flag == 1) ? $mmsg_noResLgn : $mess_gp_noress_ass;
                      echo "<TD bgcolor= '#DEE3E7' align='left' valign='top'><b>$ressource</b></TD>";
                 }
                 //fin if ($id_ress != 0)
                 if ($utilisateur > 0 || $typ_user =="APPRENANT"){
                   if ($utilisateur > 0){
                      $actform = GetDataField ($connect,"select presc_formateur_no from prescription$Ext where
                                                         presc_seq_no = $seq and
                                                         presc_utilisateur_no = $utilisateur and
                                                         presc_grp_no = $numero_groupe","presc_formateur_no");
                      $prescripteur = GetDataField ($connect,"select presc_prescripteur_no from prescription$Ext where
                                                              presc_seq_no = $seq and
                                                              presc_utilisateur_no = $utilisateur and
                                                              presc_grp_no = $numero_groupe","presc_prescripteur_no");
                      $actsuivi = GetDataField ($connect,"select suivi_etat_lb from suivi1$Ext where
                                                          suivi_act_no = $id and
                                                          suivi_utilisateur_no = $utilisateur and
                                                          suivi_grp_no = $numero_groupe","suivi_etat_lb");
                      $suivi_fichier = GetDataField ($connect,"select suivi_fichier_lb from suivi1$Ext where
                                                               suivi_act_no = $id and
                                                               suivi_utilisateur_no = $utilisateur and
                                                               suivi_grp_no = $numero_groupe","suivi_fichier_lb");
                   }else{
                      $actform = GetDataField ($connect,"select presc_formateur_no from prescription$Ext where
                                                         presc_seq_no = $seq and
                                                         presc_utilisateur_no = $id_user and
                                                         presc_grp_no = $numero_groupe","presc_formateur_no");
                      $prescripteur = GetDataField ($connect,"select presc_prescripteur_no from prescription$Ext where
                                                              presc_seq_no = $seq and
                                                              presc_utilisateur_no = $id_user and
                                                              presc_grp_no = $numero_groupe","presc_prescripteur_no");
                      $actsuivi = GetDataField ($connect,"select suivi_etat_lb from suivi1$Ext where
                                                          suivi_act_no = $id and
                                                          suivi_utilisateur_no = $id_user and
                                                          suivi_grp_no = $numero_groupe","suivi_etat_lb");
                      $suivi_fichier = GetDataField ($connect,"select suivi_fichier_lb from suivi1$Ext where
                                                               suivi_act_no = $id and
                                                               suivi_utilisateur_no = $id_user and
                                                               suivi_grp_no = $numero_groupe","suivi_fichier_lb");
                   }
                   if ($suivi_fichier != "" && strstr($suivi_fichier,"qf_"))
                     $tip_fic = substr($suivi_fichier, 0, 3);
                   if ($actsuivi == "TERMINE")
                     $actsuiv = $mess_fait;
                   elseif ($actsuivi == "A FAIRE" && $flag == 1)
                     $actsuiv = $mess_lanc_afaire;
                   elseif ($actsuivi == "EN COURS")
                     $actsuiv = $mess_lanc_encours;
                   elseif ($actsuivi == "ATTENTE")
                   {
                     $actsuiv = $mess_lanc_attente;//formateur sur etat et apprenant sur acq/eval
                     $actsuivalid = $mess_fait;//apprenant
                   }
                   elseif ($actsuivi == "PRESENTIEL")
                     $actsuiv = $mess_lanc_afaire;//attente
                   elseif ($actsuivi == "A FAIRE" && $flag == 0)
                     $actsuiv = $mess_trvx;
                   $etat_activite = $actsuiv;
                   if ($actsuivi == 'TERMINE' && $utilisateur > 0 && ($id_user == $actform || $id_user == $prescripteur || $id_user == $inscripteur)){
                     $nom_user=GetDataField ($connect,"select util_nom_lb from utilisateur where util_cdn='$utilisateur'","util_nom_lb");
                     $prenom_user=GetDataField ($connect,"select util_prenom_lb from utilisateur where util_cdn='$utilisateur'","util_prenom_lb");
                     $lien = "suivi.php?numero_groupe=$numero_groupe&hgrp=$hgrp&nom=$nom_user&prenom=$prenom_user&fin_parc=$fin_parc&utilisateur=$utilisateur&id_parc=$id_parc&id_act=$id&parc=$id_parc&id_seq=$seq&vp=1&suivi_etat_seq=$suivi_etat_seq&modif_etat=1&visible=$visible&accord=$accord&prq=$prerequis&depasse=$depasse&proposable=$proposable";
                     $lien = urlencode($lien);
                     echo "<TD bgcolor= '#DEE3E7' align='left' valign='top' nowrap>$etat_activite</TD>";
                   }else
                     echo "<TD bgcolor= '#DEE3E7' align='left' valign='top' nowrap>$etat_activite</TD>";
                   if ($utilisateur > 0){
                     $nom_app = GetDataField ($connect,"select util_nom_lb  from utilisateur where util_cdn = $utilisateur","util_nom_lb");
                     $prenom_app = GetDataField ($connect,"select util_prenom_lb  from utilisateur where util_cdn = $utilisateur","util_prenom_lb");
                   }
                 }//fin if ($origine == "liste")
                 if ($ii == 0 && $utilisateur == "")
                    $pointer = 1;
                 if ($Nb_act_seq == $ii+1 && $ordre_act == "OUI")
                    $fin_seq=1;
                 if ($Nb_act_seq == $ii+1 && $ordre_act == "NON" && $fin_ordre_non == $Nb_act_seq)
                    $fin_seq = 1;
                 $actvalid = GetDataField ($connect,"select act_acquittement_lb from activite where act_seq_no = $seq and act_cdn = $id","act_acquittement_lb");
                 if ($utilisateur > 0)
                    $actnote = GetDataField ($connect,"select suivi_note_nb1 from suivi1$Ext where
                                                       suivi_act_no = $id and
                                                       suivi_utilisateur_no= $utilisateur and
                                                       suivi_grp_no = $numero_groupe","suivi_note_nb1");
                 $ress_type = GetDataField ($connect,"select ress_type from ressource_new where ress_cdn = $id_ress","ress_type");
                 if (($id_user == $actform  && $actsuivi == 'PRESENTIEL' &&
                     ($actvalid == 'FORMATEUR_REFERENT' || $actvalid == 'APPRENANT')) ||
                     ($id_user == $actform  && ($actsuivi == 'ATTENTE' || ($actsuivi == 'EN COURS' &&
                     (($actvalid == 'APPRENANT'|| $actvalid == 'FORMATEUR_REFERENT') && $notation == 'NON') ||
                     ($actvalid == 'FORMATEUR_REFERENT' && $notation == 'OUI' && $actsuivi != 'TERMINE' &&
                     $actsuivi != 'A FAIRE' && $devoir == 'NON')))) ||
                     (($actvalid == 'FORMATEUR_REFERENT' || $actvalid == 'APPRENANT') &&
                     $typ_user == 'APPRENANT' && $actsuivi == 'EN COURS') ||
                     ($pass_mult == 'OUI' && $actsuivi == 'EN COURS' && $pointeur == 1))
                 {
                    $lien="suivi.php?numero_groupe=$numero_groupe&hgrp=$hgrp&prov=vp&actform=$actform&utilisateur=$utilisateur&id_parc=$id_parc&id_act=$id&ress_type=$ress_type&id_seq=$seq&pointer=$pointer&validation=1&fin_seq=$fin_seq&sequ=$i&parc=$id_parc&visible=$visible&accord=$accord&prq=$prerequis&depasse=$depasse&proposable=$proposable";
                    if ($utilisateur > 0 &&  $actnote == "" && $notation == "OUI")
                       $lien .="&noter=1";
                    elseif (($utilisateur > 0 &&  $actnote != "" && $notation == "OUI" && $devoir == "OUI") || (!$utilisateur && $actnote == "" && $notation == "OUI") || ($actsuivi == "PRESENTIEL" && $notation == "NON"))
                       $lien .="&envoyer=1";
                    $lien = urlencode($lien);
                    echo "<TD bgcolor= '#DEE3E7' align='left' valign='top'></TD> ";
                 }
                 else
                 {
                    if ($actsuivi == "TERMINE" && $utilisateur == "")
                    {
                       $note = GetDataField ($connect,"select suivi_note_nb1 from suivi1$Ext where
                                                       suivi_act_no = $id and
                                                       suivi_utilisateur_no = $id_user and
                                                       suivi_grp_no = $numero_groupe","suivi_note_nb1");
                       if (is_numeric($note) && $note > -1 && $note < 21)
                         $note .="/20";
                       if ($note == "Acquis")
                         $note = $mess_suivi_acq;
                       elseif ($note == "Non acquis")
                         $note = $mess_suivi_noacq;
                       $no_suivi = GetDataField ($connect,"select suivi_cdn from suivi1$Ext where
                                                           suivi_act_no = $id and
                                                           suivi_utilisateur_no = $id_user and
                                                           suivi_grp_no = $numero_groupe","suivi_cdn");
                       if ($note !="" && (($notation == "OUI" && $devoir == "OUI" && $actvalid == "FORMATEUR_REFERENT") || $tip_fic != "")){
                         $lien = "bilan.php?numero_groupe=$numero_groupe&hgrp=$hgrp&bilan_act=1&id_suivi=$no_suivi&utilisateur=$utilisateur&code_act=oof&nom_seq=$nom_seq&nom_parc=$nom_parc&nom_act=$nom";
                         $lien = urlencode($lien);
                         echo "<TD bgcolor= '#DEE3E7' align='left' valign='top' nowrap>";
                         echo "<DIV id='sequence'><A HREF = \"#\"  title=\"$mess_gp_bilan\" onclick=\"open('trace.php?link=$lien','window','scrollbars,resizable=yes,width=700,height=300')\">$note</A></DIV></TD>";
                       }elseif ($note != "" && ($notation == "OUI" || ($notation == "NON" && $suivi_fichier != '')) && $devoir == "NON")
                         echo "<TD bgcolor= '#DEE3E7' align='left' valign='top' nowrap>$note</TD>";
                       else
                         echo "<TD bgcolor= '#DEE3E7' align='left' valign='top'>$msq_valide</TD>";
                    }elseif (($id_user == $actform || $id_user == $prescripteur || $id_user == $inscripteur || $typ_user == "ADMINISTRATEUR")  && $actsuivi == "TERMINE" && $utilisateur){
                       $note = GetDataField ($connect,"select suivi_note_nb1 from suivi1$Ext where
                                                       suivi_act_no = $id and
                                                       suivi_utilisateur_no = $utilisateur and
                                                       suivi_grp_no = $numero_groupe","suivi_note_nb1");
                       $no_suivi = GetDataField ($connect,"select suivi_cdn from suivi1$Ext where
                                                           suivi_act_no = $id and
                                                           suivi_utilisateur_no = $utilisateur and
                                                           suivi_grp_no = $numero_groupe","suivi_cdn");
                       if (is_numeric($note) && $note > -1 && $note < 21)
                         $note .="/20";
                       if ($note == "Acquis")
                         $note = $mess_suivi_acq;
                       elseif ($note == "Non acquis")
                         $note = $mess_suivi_noacq;
                       if ($note != "" && (($notation == "OUI" && $devoir == "OUI" && $actvalid == "FORMATEUR_REFERENT") || $tip_fic != "")){
                         $lien = "bilan.php?numero_groupe=$numero_groupe&hgrp=$hgrp&bilan_act=1&id_suivi=$no_suivi&utilisateur=$utilisateur&code_act=oof&nom_seq=$nom_seq&nom_parc=$nom_parc&nom_act=$nom";
                         $lien = urlencode($lien);
                         echo "<TD bgcolor= '#DEE3E7' align='left' valign='top' nowrap><DIV id='sequence'><A HREF = \"#\" onclick=\"open('trace.php?link=$lien','window','scrollbars,resizable=yes,width=700,height=300')\"";
                         echo" onMouseOver=\"overlib('$mess_gp_bilan',ol_hpos,LEFT,CAPTION,'<center>$mess_gp_tit_suiv_ped</center>')\" onMouseOut=\"nd()\"\">$note</A></DIV></TD>";
                       }elseif ($note != "" && ($notation == "OUI" || ($notation == "NON" && $suivi_fichier != '')) && $devoir == "NON")
                         echo "<TD bgcolor= '#DEE3E7' align='left' valign='top' nowrap>$note</TD>";
                       else
                         echo "<TD bgcolor= '#DEE3E7' align='left' valign='top'>$msq_valide</TD>";
                    }elseif ($actvalid == 'APPRENANT' && $typ_user == 'APPRENANT' && $actsuivi == 'TERMINE' && $utilisateur == '')
                       echo "<TD bgcolor= '#DEE3E7' align='left' valign='top'>$msq_valide</TD>";
                    else
                       echo "<TD bgcolor= '#DEE3E7'>&nbsp;</TD>";
                 }
                 if ($utilisateur > 0)
                    $act_suivi = mysql_query ("select * from suivi1$Ext where
                                               suivi_act_no = $id and
                                               suivi_utilisateur_no=$utilisateur and
                                               suivi_grp_no = $numero_groupe");
                 else
                    $act_suivi = mysql_query ("select * from suivi1$Ext where
                                               suivi_act_no = $id and
                                               suivi_utilisateur_no=$id_user and
                                               suivi_grp_no = $numero_groupe");
                 //echo "</TR>";

                 $deroule[$i]=0;
                 $traquer = AffiDureeAct($id,$scormOk,$id_app,$numero_groupe);
                 $tabTrq = explode('|',$traquer);
                 $nbTrq = $tabTrq[0];
                 $dureeActivite = $tabTrq[1];
                 $dureeTotale += $dureeActivite;
                 echo "<TD bgcolor= '#DEE3E7' valign='top'>".$nbTrq."</TD>".
                      "<TD bgcolor= '#DEE3E7' valign='top'>".AfficheDureeAN($dureeActivite,$nbTrq)."</TD>".
                      "<TD bgcolor= '#DEE3E7' valign='top' nowrap>".$tabTrq[2]."</TD></TR>";

               $ii++;
               } // while ($ii != $Nb_act_seq) {
          }
          elseif ($scormOk == 1)
          {
              echo "<TD class='barre_titre'>$msq_activite</TD>";
              echo "<TD class='barre_titre'>$msq_aff_ordre</TD>";
              echo "<TD class='barre_titre'>$msq_desc_seq</TD>";
              echo "<TD class='barre_titre'>$mess_motclef</TD>";
              echo "<TD class='barre_titre'>$msq_duree_seq</TD>";
              echo "<TD class='barre_titre'>$mess_dur_max</TD>";
              echo "<TD class='barre_titre'>$msq_si_prereq</TD>";
              echo "<TD class='barre_titre'>$mess_sco_min</TD>";
              echo "<TD class='barre_titre'>$msq_ress_assoc</TD>";
              echo "<TD class='barre_titre'>$msq_etat</TD>";
              echo "<TD class='barre_titre'>Nombre d'accès</TD>";
              echo "<TD class='barre_titre'>Temps consacré</TD>";
              echo "<TD class='barre_titre'>Dates d'accès</TD>";
              echo "</TR>";
              $ii = 0;
              $code = array();
              $id_act_seq = array();
              while ($ii != $Nb_act_seq) {
                  $affiche_scorm ='';
                  $id = mysql_result ($act_query,$ii,"mod_cdn");
                  $code[$id]=$ii;
                  $nom = mysql_result ($act_query,$ii,"mod_titre_lb");
                  $description = html_entity_decode(mysql_result ($act_query,$ii,"mod_desc_cmt"),ENT_QUOTES,'ISO-8859-1');
                  $motclef =html_entity_decode( mysql_result ($act_query,$ii,"mod_motclef_lb"),ENT_QUOTES,'ISO-8859-1');
                  $numero = mysql_result ($act_query,$ii,"mod_numero_lb");
                  $launch = mysql_result ($act_query,$ii,"mod_launch_lb");
                  $duree = mysql_result ($act_query,$ii,"mod_duree_nb");
                  $prerequis = mysql_result ($act_query,$ii,"mod_prereq_lb");
                  $score_max = mysql_result ($act_query,$ii,"mod_masteryscore");
                  $maxtime = mysql_result ($act_query,$ii,"mod_maxtimeallowed");
                  $status = mysql_result ($act_query,$ii,"lesson_status");
                  if ($status == "COMPLETED" || $status == "PASSED")
                     $status = $mess_fait;
                  elseif ($status == "NOT ATTEMPTED")
                     $status = $mess_lanc_afaire;
                  elseif ($status == "INCOMPLETE")
                     $status = $mess_lanc_encours;
                  elseif ($status == "FAILED")
                     $status = $mess_echec;//formateur sur etat et apprenant sur acq/eval
                   elseif ($status == "BROWSED" || $status == "UNKNOWN")
                     $status = $mess_vu;
                 if ($maxtime != '' && strlen($maxtime) > 9){
                    if (strstr($maxtime,'t') || strstr($maxtime,'T'))
                       $maxtime = substr($maxtime,13);
                    $liste_duree = explode(":",$maxtime);
                    $maxtime = ($liste_duree[0]*60)+$liste_duree[1];
                 }
                 if ($prerequis != ""){
                    $prereq1 = GetDataField ($connect,"select mod_cdn from scorm_module where mod_numero_lb = \"$prerequis\"","mod_cdn");
                    $prereq = $code[$prereq1]+1;
                 }else
                    $prereq = '';
                 $affiche_scorm .= "<TR>";
                 $l = $ii+1;
                 if ($launch == '')
                 {
                    $affiche_scorm = "<TD valign='top'>&nbsp;</TD>".
                                     "<TD bgcolor= '#cccccc' valign='top' colspan='13' align='left'><B>$nom</B></TD>";
                 }
                 else
                 {
                     $affiche_scorm .= "<TD valign='top'>&nbsp;</TD>";
                     $affiche_scorm .= "<TD bgcolor= '#DEE3E7' valign='top'>$nom</TD>";
                     $affiche_scorm .= "<TD bgcolor= '#DEE3E7' valign='top'>$l</TD>";
                     $affiche_scorm .= "<TD bgcolor= '#DEE3E7' valign='top'>$description</TD>";
                     $affiche_scorm .= "<TD bgcolor= '#DEE3E7' valign='top'>$motclef</TD>";
                     if ($duree != 0)
                        $affiche_scorm .= "<TD bgcolor= '#DEE3E7' valign='top'>$duree'</TD>";
                     else
                        $affiche_scorm .= "<TD bgcolor= '#DEE3E7' valign='top'>&nbsp;</TD>";
                     if ($maxtime != '')
                        $affiche_scorm .= "<TD bgcolor= '#DEE3E7' valign='top'>$maxtime'</TD>";
                     else
                        $affiche_scorm .= "<TD bgcolor= '#DEE3E7' valign='top'>&nbsp;</TD>";
                     $affiche_scorm .= "<TD bgcolor= '#DEE3E7' valign='top'>$prereq</TD>";
                     $affiche_scorm .= "<TD bgcolor= '#DEE3E7' valign='top'>$score_max</TD>";
                     if (!strstr($launch,"http://"))
                        $launch = "../".$launch;
                    $affiche_scorm .= "<TD bgcolor= '#DEE3E7' valign='top'><IMG SRC=\"images/ecran-annonce/icoGgo.gif\" border='0'></TD>";
                    $affiche_scorm .= "<TD bgcolor= '#DEE3E7' valign='top'>$status</TD>";
                    $traquer = AffiDureeAct($id,$scormOk,$id_app,$numero_groupe);
                    $tabTrq = explode('|',$traquer);
                    $nbTrq = $tabTrq[0];
                    $dureeActivite = $tabTrq[1];
                    $dureeTotale += $dureeActivite;
                    $affiche_scorm .=  "<TD bgcolor= '#DEE3E7' valign='top'>".$nbTrq."</TD>";
                    $affiche_scorm .=  "<TD bgcolor= '#DEE3E7' valign='top'>".AfficheDureeAN($dureeActivite,$nbTrq)."</TD>";
                    $affiche_scorm .=  "<TD bgcolor= '#DEE3E7' valign='top' nowrap>".$tabTrq[2]."</TD>";
                 }
                 echo "</TR>$affiche_scorm";
            $ii++;
           }
          }
               $reqWk=mysql_query("select * from wikiapp,wiki where wkapp_app_no='$id_app' and
                                   wkapp_seq_no='$seq' and wkapp_parc_no='$id_parc' and
                                   wkapp_grp_no='$numero_groupe' and wkapp_wiki_no = wiki_cdn");
               $nbWk = mysql_num_rows($reqWk);
               if ($nbWk == 1)
               {
                  $oWk = mysql_fetch_object($reqWk);
                  $reqClan = mysql_query("select * from wikiapp where wkapp_clan_nb='".$oWk->wkapp_clan_nb."' and
                                        wkapp_seq_no='$seq' and wkapp_parc_no='$id_parc' and
                                        wkapp_grp_no='$numero_groupe'");
                  $NbBodyWk = mysql_num_rows(mysql_query("select wkbody_cdn from wikibodies where wkbody_auteur_no ='".
                                                         $id_app."' and wkbody_clan_no ='".$oWk->wkapp_clan_nb."'"));
                  $NbMetaWk = mysql_num_rows(mysql_query("select wkmeta_cdn from wikimeta where wkmeta_auteur_no ='".
                                                        $id_app."' and wkmeta_clan_no ='".$oWk->wkapp_clan_nb."'"));
                  $NoteWk = GetDataField ($connect,"select wknote_note_lb from wikinote where wknote_app_no ='".
                                          $oWk->wkapp_cdn."'","wknote_note_lb");
                  if ($NoteWk != 'NULL')
                     $_SESSION['NoteWk'] = $NoteWk;
                  else
                     $_SESSION['NoteWk'] = '';
                  $nbClan = mysql_num_rows($reqClan);
                  if ($nbClan > 1)
                  {
                     $listeClan = 'Travail en commun avec ';
                     while ($oClan = mysql_fetch_object($reqClan))
                     {
                         if ($oClan->wkapp_app_no != $id_app)
                         {

                               $nomApp = GetDataNP ($connect,"select util_nom_lb,util_prenom_lb from utilisateur where util_cdn = '".
                                                   $oClan->wkapp_app_no."'","util_nom_lb,util_prenom_lb");
                               $listeClan .= ' <strong>'.$nomApp.'</strong>,';
                         }

                     }
                  }
                  else
                     $listeClan .= 'Travail en commun : seul pour l\'instant';
                  $cols = ($scormOk == 1)? 6:2 ;
                  echo '<tr style="background-color:#D4E7ED;"><td bgcolor="#efefef"></td><td valign="top"><div class="sequence" title="'.
                        $listeClan.'" style="cursor:default;">Travail en commun</div></td>';
                  if ($utilisateur > 0)
                  {
                      echo '<td valign="top">';
                      $lien= "wiki/wiki.php?id_seq=$seq&id_parc=$id_parc&numero_groupe=$numero_groupe&numApp=$num_app&keepThis=true&TB_iframe=true&height=425&width=650";
                      echo "<a href=\"$lien\" class='thickbox' name='Composantes du groupe'>Groupe</a></td>";
                  }
                  else
                      echo '<td valign="top">&nbsp;</td>';
                  echo '<td colspan="'.$cols.'" valign="top">'.$oWk->wiki_consigne_cmt.'</td>';
                  echo "<TD align='left' valign='top' >".
                           "<IMG SRC=\"images/ecran-annonce/icoGgoinactif.gif\" border='0' ALT= \"$mess_inactif\"></TD>";
                  if (!empty($_SESSION['NoteWk']))
                     echo "<td valign='top'>Fait</td>";
                  elseif ($NbBodyWk > 0 || $NbMetaWk > 0)
                     echo "<td valign='top' nowrap>En cours</td>";
                  elseif ($NbBodyWk == 0 && $NbMetaWk == 0)
                     echo "<td valign='top' nowrap>A faire</td>";
                  if (!empty($_SESSION['NoteWk']))
                  {
                     echo "<td valign='top' align='center'><strong>".$_SESSION['NoteWk']."</strong></td>";
                  }
                  else
                      echo '<td valign="top" align="center">&nbsp;</td>';
                  if ($utilisateur > 0)
                     unset($_SESSION['NoteWk']);
                  echo '<td valign="top" colspan="2" align="center">&nbsp;</td>';
                  echo '</tr>';
               }
               //fin wiki
               //mindmap
               $reqMM=mysql_query("select * from mindmapapp,mindmap where mmapp_app_no='$id_app' and
                                   mmapp_seq_no='$seq' and mmapp_parc_no='$id_parc' and
                                   mmapp_grp_no='$numero_groupe' and mmapp_mindmap_no = mindmap_cdn");
               $nbMM = mysql_num_rows($reqMM);
               if ($nbMM == 1)
               {
                  $oMM = mysql_fetch_object($reqMM);
                  $reqClanMM = mysql_query("select * from mindmapapp where mmapp_clan_nb='".$oMM->mmapp_clan_nb."' and
                                        mmapp_seq_no='$seq' and mmapp_parc_no='$id_parc' and
                                        mmapp_grp_no='$numero_groupe'");
                  $NoteMM = GetDataField ($connect,"select mmnote_note_lb from mindmapnote where mmnote_app_no ='".
                                          $oMM->mmapp_cdn."'","mmnote_note_lb");
                  if ($NoteMM != 'NULL')
                     $_SESSION['NoteMM'] = $NoteMM;
                  else
                     $_SESSION['NoteMM'] = '';
                  $nbClanMM = mysql_num_rows($reqClanMM);
                  if ($nbClanMM > 1)
                  {
                     $listeClanMM = 'Carte heuristique avec ';
                     while ($oClanMM = mysql_fetch_object($reqClanMM))
                     {
                         if ($oClanMM->mmapp_app_no != $id_app)
                         {

                               $nomApp = GetDataNP ($connect,"select util_nom_lb,util_prenom_lb from utilisateur where util_cdn = '".
                                                   $oClanMM->mmapp_app_no."'","util_nom_lb,util_prenom_lb");
                               $listeClanMM .= ' <strong>'.$nomApp.'</strong>,';
                         }

                     }
                  }
                  else
                     $listeClanMM .= 'Carte heuristique : pour l\'instant seul';

                  $cols = ($scormOk == 1) ? 6 : 3 ;
                  echo '<tr style="background-color:#D4E7ED;"><td bgcolor="#efefef"></td><td valign="top"><div title="'.
                        $listeClanMM.'" style="clear:both;float:left;cursor:default;font-weight:bold;text-align:left;">'.
                        'MindMap</div></td>';
                  if ($utilisateur > 0)
                  {
                      echo '<td valign="top">';
                      echo "Groupe MindMap";
                      $MMaffecte = 1;
                      echo '</td>';
                  }
                  else
                      echo '<td valign="top">&nbsp;</td>';
                  echo '<td colspan="'.$cols.'"><div style="text-align:left;">';
                  echo NewHtmlEntityDecode($oMM->mindmap_intro_cmt,ENT_QUOTES).'</div></td>';
                  $nbMM = mysql_num_rows(mysql_query("select * from mindmaphistory where
                                        mindhisto_auteur_no = $id_app and mindmap_clan_no = ".$oMM->mmapp_clan_nb));
                  echo "<TD align='left' valign='top' >".
                           "<IMG SRC=\"images/ecran-annonce/icoGgoinactif.gif\" border='0' ALT= \"$mess_inactif\"></TD>";
                  if (!empty($_SESSION['NoteMM']))
                     echo "<td valign='top'>Fait</td>";
                  elseif ($NbMM == 0)
                     echo "<td valign='top' nowrap>A faire</td>";
                  if (!empty($_SESSION['NoteMM']) && empty($utilisateur))
                  {
                     echo "<td valign='top' align='center'><strong>".$_SESSION['NoteMM']."</strong></td>";
                  }
                  elseif (empty($_SESSION['NoteMM']) && $id_app > 0  && $nbMM == 0)
                  {
                      echo '<td valign="top" align="center">&nbsp;</td>';
                  }
                  if ($utilisateur > 0 && $id_formateur == $id_user)
                      echo '<td valign="top" align="center">&nbsp;</td>';
                  if ($utilisateur > 0)
                     unset($_SESSION['NoteMM']);
                  echo '<td valign="top" colspan="3" align="center">&nbsp;</td>';
                  echo '</tr>';
               }
               //fin mindmap

               echo "<TR height='10'><TD colspan='7'>&nbsp;</TD></TR>";
               echo "</TABLE></TD></TR>";

            } //fin else ($Nb__seq ...)
         } // fin if ((($deroule[$i] == 1 && $id_seq == $seq) || $tout == 1) || ($vp == 1 && $seq == $seq_ouverte)){
         $i++;
         $pointeur = 0;
         $visible = 0;
       }//fin while ($i != $nb_seq)
//       echo"<TR height='10'><TD colspan='5'>&nbsp;</TD></TR>";
       echo "</TABLE></TD></TR>";
     }// fin if deroule[$i]_parc
     $p++;
  }// fin while ($p < $nb_parc)
 if ($tout == 1)
    echo  "<TR><TD width='10%' valign='top'>&nbsp;</TD><td><center><TABLE cellspacing=4 cellpadding=5>".
                     "<TR><TD nowrap colspan=2 align=center style='margin:5px;padding:10px;'>".
                     "<span style='margin:5px;padding:10px;font-weight:bold;font-size:20px;border:2px solid red;'>".
                     "Durée totale calculée ou estimée en l'absence de tracking : ".AfficheDureeAN($dureeTotale,0).
                     "</span></td></TR></TABLE></td></TR></TABLE></TD><TD width='10%' valign='top'>&nbsp;</TD></TR>".
                     "</TABLE></TD></TR></TABLE>";
  if ($utilisateur == ""){
    echo "</TABLE></TD></TR></TABLE></TD></TR></TABLE></body></html>";
    exit;
  }

  echo "</TABLE></TD></TR><TR><TD width='100%' align='left'>";
  echo "</TD></TR></TABLE></TD></TR></TABLE></body></html>";
}
?>
