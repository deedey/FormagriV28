<?php
session_start();
include ("include/UrlParam2PhpVar.inc.php");
if (!isset($_SESSION['id_user']) || $_SESSION['id_user'] == "")
{
  exit();
}
require "graphique/admin.inc.php";
require 'fonction.inc.php';
require_once('ApiTweet/lib/config.php');
require "lang$lg.inc.php";
require "fonction_html.inc.php";
require "langues/formation.inc.php";
require "langues/module.inc.php";
require "class/rsa.class.php";
dbConnect();
include ("style.inc.php");
echo "<SCRIPT Language=\"Javascript\">";
          echo "function DoTheRefresh(){location.reload();}";
echo "</SCRIPT>";
?>
<script type="text/javascript" src="OutilsJs/lib/ajax/ajax_cms.js"></script>
<div id="affiche" class="Status"></div>
<div id="mon_contenu" class="cms"
        onClick="javascript:$(document).ready(function() {if ($.browser.msie) {$('#mon_contenu').hide();}else{$('#mon_contenu').hide('slow');}})"
        <?php echo "title=\"$mess_clkF\">";?>
</div>
<?php
$Ext="_$numero_groupe";
$date_jour=date("Y/m/d");
if (isset($_SESSION['lien_details_parc']))
    unset($_SESSION['lien_details_parc']);
if ($formation == 1)
  $saut = 1;
if ($saut == 1)
{
  if ($utilisateur > 0 && ($typ_user == "TUTEUR" && $entantque != 'tut'))
  {
    $req_grp =  mysql_query ("select utilgr_groupe_no from utilisateur_groupe,tuteur,groupe where
                              utilgr_utilisateur_no = $utilisateur and
                              ((tuteur.tut_apprenant_no = $utilisateur and
                              tuteur.tut_tuteur_no=$id_user) OR
                              (groupe.grp_tuteur_no = $id_user)) AND
                              utilgr_groupe_no=$numero_groupe");
    $num_app = $utilisateur;
  }
  elseif ($utilisateur > 0 && ($typ_user == "TUTEUR" && $entantque == 'tut'))
  {
    $req_grp =  mysql_query ("select utilgr_groupe_no from utilisateur_groupe where
                              utilgr_utilisateur_no = $utilisateur AND
                              utilgr_groupe_no=$numero_groupe");
    $num_app = $utilisateur;
  }
  elseif ($utilisateur > 0 && ($typ_user == "ADMINISTRATEUR" || $typ_user == "RESPONSABLE_FORMATION" || $typ_user == "FORMATEUR_REFERENT"))
  {
    $req_grp =  mysql_query ("select utilgr_groupe_no from utilisateur_groupe where
                              utilgr_utilisateur_no = $utilisateur  AND
                              utilgr_groupe_no=$numero_groupe");
    $num_app = $utilisateur;
  }
  elseif ($typ_user == "APPRENANT")
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
    echo "<TABLE bgColor='#298CA0' cellspacing='2' width='680'><TR><TD>";
    echo "<TABLE bgColor='#FFFFFF' cellspacing='1' width='100%'><TR><TD>";
    echo "<CENTER>&nbsp;<P><FONT SIZE='2'><B>$mess_gp_tut_nogr</B></FONT><P>&nbsp;";
    echo "</TD></TR></TABLE></TD></TR></TABLE>";
    exit;
  }
  elseif ($nbr_id_grp == 0 && $num_app == $id_user)
  {
    echo "<TABLE bgColor='#298CA0' cellspacing='2' width='680'><TR><TD>";
    echo "<TABLE bgColor='#FFFFFF' cellspacing='1' width='100%'><TR><TD>";
    echo "<CENTER>&nbsp;<P><FONT SIZE='2'><B>$mess_gp_app_nogr</B></FONT><P>&nbsp;";
    echo "</TD></TR></TABLE></TD></TR></TABLE>";
    exit;
  }
  $id_grp = $numero_groupe;
  $nom_user=GetDataField ($connect,"select util_nom_lb from utilisateur where util_cdn='$num_app'","util_nom_lb");
  $prenom_user=GetDataField ($connect,"select util_prenom_lb from utilisateur where util_cdn='$num_app'","util_prenom_lb");
  $login_user=GetDataField ($connect,"select util_login_lb from utilisateur where util_cdn='$num_app'","util_login_lb");
  $majuscule =$prenom_user." ".$nom_user;
  $grp_query = mysql_query ("select * from groupe where grp_cdn=$id_grp");
  $nom_grp = mysql_result ($grp_query,0,"grp_nom_lb");
  $obj_grp = mysql_result ($grp_query,0,"grp_formobject_lb");
  $desc_grp = mysql_result ($grp_query,0,"grp_formdesc_cmt");
  $comment_grp = mysql_result ($grp_query,0,"grp_commentaire_cmt");
  $duree_query = mysql_query ("select sum(suiv2_duree_nb) from suivi2$Ext,prescription$Ext where
                               suiv2_utilisateur_no = prescription$Ext.presc_utilisateur_no AND
                               suiv2_utilisateur_no = $num_app AND
                               suiv2_seq_no = prescription$Ext.presc_seq_no AND
                               prescription$Ext.presc_grp_no = $numero_groupe and
                               suiv2_grp_no = $numero_groupe");
  $duree_form = mysql_result($duree_query,0);
  $duree_form = duree_calc($duree_form);
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
  echo "<CENTER><TABLE bgColor='#298CA0' cellspacing='2' cellpadding='0' width='98%'><TR><TD width='100%'>";
  echo "<TABLE bgColor='#FFFFFF' cellspacing='0' cellpadding='0' width='100%'><TR><TD>";
  if ($annuaire == 1)
    echo "<TABLE border='0' cellspacing='1' cellpadding='6' width='100%'>";
  else
    echo "<TABLE border='0' cellspacing='1' cellpadding='6' width='100%'><TR>".
         "<TD background=\"images/fond_titre_table.jpg\" colspan='2' height='34' align='center'>";
  if ($utilisateur > 0)
    echo "<FONT COLOR='white' size=3><B>$mess_gp_form_suiv $majuscule</B></FONT>";
  elseif ($utilisateur == "" && $annuaire != 1)
    echo "<FONT size=3 COLOR='white'><B>$mess_menu_present</B></FONT>";
  elseif ($utilisateur == "" && $annuaire == 1)
    echo "<TR><TD background=\"images/fond_titre_table.jpg\" colspan='3' height='34' align='center' valign='center'>".
         "<Font size='3' color='#FFFFFF'><B>$mess_ad_annu $mess_menu_gestion_grp $nom_grp</B></FONT>";
  echo "</TD></TR>";

  if ($annuaire !=1)
    echo "<TR bgcolor='#F4F4F4'><TD align='left' height='30' nowrap valign='top'><B>$mess_grp_form</B></TD>".
         "<TD align='left'>$nom_grp</TD></TR>";
  $resp_grp = GetDataField ($connect,"select grp_resp_no from groupe where grp_cdn = $id_grp","grp_resp_no");
  $nom_resp=GetDataField ($connect,"select util_nom_lb from utilisateur where util_cdn='$resp_grp'","util_nom_lb");
  $email = GetDataField ($connect,"select util_email_lb  from utilisateur where util_cdn = '$resp_grp'","util_email_lb");
  $photo=GetDataField ($connect,"select util_photo_lb from utilisateur where util_cdn='$resp_grp'","util_photo_lb");
  $prenom_resp=GetDataField ($connect,"select util_prenom_lb from utilisateur where util_cdn='$resp_grp'","util_prenom_lb");
  $majuscule =$prenom_resp." ".$nom_resp;
  $lien="prescription.php?numero_groupe=$numero_groupe&affiche_fiche_app=1&hgrp=$hgrp&id_util=$resp_grp&identite=1";
  $lien= urlencode($lien);
  echo "<TR bgcolor='#FFFFFF'><TD align='left' nowrap valign='top'><B>$mess_resp</B></TD>";
  echo "<TD align='left' width=50%><table width=100%><tr><TD width=100%><A href=\"trace.php?link=$lien\"";
  if ($photo != "")
  {
     list($w_img, $h_img, $type_img, $attr_img) = getimagesize("images/$photo");
     echo " onMouseOver=\"overlib('', TEXTCOLOR, '#ffffff', TEXTSIZE, 2, WIDTH, $w_img, HEIGHT, $h_img, BACKGROUND,".
          " 'images/$photo', PADX, 60, 20, PADY, 20, 20)\" onMouseOut=\"nd()\"";
  }
  else
     echo " title=\"$mess_suite_fp\"";
  echo ">$majuscule</A></TD>";
  echo "</table></td></TR>";
  if ($annuaire !=1)
  {
    echo "<TR bgcolor='#F4F4F4'><TD align='left' nowrap valign='top' height='30'><B>".
         "$mess_gp_obj_form</B></TD><TD align='left'>".html_entity_decode($obj_grp,ENT_QUOTES,'iso-8859-1')."</TD></TR>";
    echo "<TR bgcolor='#FFFFFF'><TD align='left' nowrap valign='top' height='30'><B>".
         "$mess_gp_desc_form</B></TD><TD align='left'>".html_entity_decode($desc_grp,ENT_QUOTES,'iso-8859-1')."</TD></TR>";
    echo "<TR bgcolor='#F4F4F4'><TD align='left' nowrap valign='top' height='30'><B>".
         "$mess_form_tot</B></TD><TD align='left'>$nomb_parc&nbsp;&nbsp;";
    echo strtolower($mess_gp_parc_appr);
    echo "</TD></TR>";
    echo "<TR bgcolor='#FFFFFF'><TD align='left' nowrap valign='top' height='30'><B> ".
         "$mess_gp_durre_tot_form</B></TD><TD align='left'>$duree_form $reparties_sur $nbrj $mess_jours</TD></TR>";
  }
  $liste = mysql_query("select util_cdn,util_nom_lb from utilisateur,prescription$Ext where
                       utilisateur.util_cdn = prescription$Ext.presc_formateur_no and
                       prescription$Ext.presc_utilisateur_no = $num_app AND
                       prescription$Ext.presc_grp_no = $numero_groupe group by utilisateur.util_nom_lb
                        order by utilisateur.util_nom_lb ASC");
  $nbr = mysql_num_rows($liste);
  echo "<TR bgcolor='#F4F4F4'><TD align='left' nowrap valign='top' height='30' rawspan='$nbr'><B>
       $mess_intervenants </B></TD><TD align='left' width=50%><table width=100%>";
  $k = 0;
  while ($k < $nbr)
  {
    $num = mysql_result($liste,$k,"util_cdn");
    $nom_form=GetDataField ($connect,"select util_nom_lb from utilisateur where util_cdn='$num'","util_nom_lb");
    $prenom_form=GetDataField ($connect,"select util_prenom_lb from utilisateur where util_cdn='$num'","util_prenom_lb");
    $photo=GetDataField ($connect,"select util_photo_lb from utilisateur where util_cdn='$num'","util_photo_lb");
    $email = GetDataField ($connect,"select util_email_lb  from utilisateur where util_cdn = '$num'","util_email_lb");
    $nom_prof = $prenom_form." ".$nom_form;
    $lien="prescription.php?numero_groupe=$numero_groupe&affiche_fiche_app=1&hgrp=$hgrp&id_util=$num&identite=1";
    $lien= urlencode($lien);
    echo "<tr><TD width=100%><A href=\"trace.php?link=$lien\"";
    if ($photo != "")
    {
       list($w_img, $h_img, $type_img, $attr_img) = getimagesize("images/$photo");
       echo " onMouseOver=\"overlib('', TEXTCOLOR, '#ffffff', TEXTSIZE, 2, WIDTH, $w_img, HEIGHT, $h_img, ".
            "BACKGROUND, 'images/$photo', PADX, 60, 20, PADY, 20, 20)\" onMouseOut=\"nd()\"";
    }
    else
       echo " title=\"$mess_suite_fp\"";
    echo ">$nom_prof</A></td>";
  $k++;
  }
  echo "</table></TD></TR>";
  // recherche du tuteur de groupe
  if ($utilisateur > 0)
    $req_tutgrp = mysql_query("select grp_tuteur_no from groupe where  grp_cdn = $numero_groupe");
  else
    $req_tutgrp = mysql_query("SELECT grp_tuteur_no from groupe where grp_cdn = $numero_groupe");
    $nomb_tutgrp = mysql_num_rows($req_tutgrp);
    if ($nomb_tutgrp > 0)
    {
       echo "<TR bgcolor='#FFFFFF'><TD valign='top' height='30' nowrap align=left><B>$mess_tut_forma</B></TD>";
       echo "<TD align='left' width=50%><table width=100%>";
       $num_tutgrp = mysql_result($req_tutgrp,0,"grp_tuteur_no");
       $nom_tutgrp=GetDataField ($connect,"SELECT util_nom_lb from utilisateur where util_cdn='$num_tutgrp'","util_nom_lb");
       $prenom_tutgrp=GetDataField ($connect,"SELECT util_prenom_lb from utilisateur where util_cdn='$num_tutgrp'","util_prenom_lb");
       $photo_tutgrp = GetDataField ($connect,"SELECT util_photo_lb from utilisateur where util_cdn = $num_tutgrp","util_photo_lb");
       $lien="prescription.php?affiche_fiche_app=1&hgrp=$hgrp&id_util=$num_tutgrp&identite=1";
       $lien= urlencode($lien);
       echo "<tr><td width=100% align=left><A href=\"trace.php?link=$lien\"";
       if ($photo_tutgrp != "")
       {
          list($w_img, $h_img, $type_img, $attr_img) = getimagesize("images/$photo_tutgrp");
          echo " onMouseOver=\"overlib('', TEXTCOLOR, '#ffffff', TEXTSIZE, 2, WIDTH, $w_img, HEIGHT, $h_img, BACKGROUND, 'images/$photo_tutgrp', PADX, 60, 20, PADY, 20, 20)\" onMouseOut=\"nd()\"";
       }
       else
          echo " title=\"$mess_suite_fp\"";
       echo ">$prenom_tutgrp $nom_tutgrp</A>";
       echo "</TD>";
       if ($annuaire == 1)
       {
          echo "<TD align='left'>";
          $email_tutgrp=GetDataField ($connect,"SELECT util_email_lb from utilisateur where util_cdn='$num_tutgrp'","util_email_lb");
          echo "</td></tr>";
       }
       echo "</table></td></tr>";
    }
  // recherche des tuteurs perso
  if ($utilisateur > 0)
    $req_tut = mysql_query("select tut_tuteur_no from tuteur where tut_apprenant_no = $utilisateur");
  else
    $req_tut = mysql_query("SELECT tut_tuteur_no from tuteur where tut_apprenant_no = $id_user");
  $nomb_tut = mysql_num_rows($req_tut);
  if ($nomb_tut > 0)
  {
     echo "<TR bgcolor='#F4F4F4'><TD valign='top' height='30' nowrap align=left><B>".ucfirst(strtolower($mess_typ_tut))."</B></TD>";
     echo "<TD align='left' width=50%><table  border=0>";
     $i_t = 0;
     while ($i_t < $nomb_tut)
     {
       $num_tut = mysql_result($req_tut,$i_t,"tut_tuteur_no");
       $nom_tut=GetDataField ($connect,"SELECT util_nom_lb from utilisateur where util_cdn='$num_tut'","util_nom_lb");
       $prenom_tut=GetDataField ($connect,"SELECT util_prenom_lb from utilisateur where util_cdn='$num_tut'","util_prenom_lb");
       $photo_tut = GetDataField ($connect,"SELECT util_photo_lb from utilisateur where util_cdn = $num_tut","util_photo_lb");
       $lien="prescription.php?affiche_fiche_app=1&hgrp=$hgrp&id_util=$num_tut&identite=1";
       $lien= urlencode($lien);
       echo "<tr><td width=50%><A href=\"trace.php?link=$lien\"";
       if ($photo_tut != "")
       {
          list($w_img, $h_img, $type_img, $attr_img) = getimagesize("images/$photo_tut");
          echo " onMouseOver=\"overlib('', TEXTCOLOR, '#ffffff', TEXTSIZE, 2, WIDTH, $w_img, HEIGHT, $h_img, BACKGROUND, 'images/$photo_tut', PADX, 60, 20, PADY, 20, 20)\" onMouseOut=\"nd()\"";
       }
       else
          echo " title=\"$mess_suite_fp\"";
       echo ">$prenom_tut $nom_tut</A></td>";
      $num_tut = mysql_result($req_tut,$i_t,"tut_tuteur_no");
      $email_tut=GetDataField ($connect,"SELECT util_email_lb from utilisateur where util_cdn='$num_tut'","util_email_lb");
       $i_t++;
     }
      echo "</td></tr></table></td></tr>";
  }
  if ($annuaire !=1)
  {
    echo "<tr bgcolor='#FFFFFF'><TD align='left' valign='top' height='30'><B>$mess_gp_deb_form</B></TD><TD align='left'>$date_min</TD></TR>";
    echo "<tr bgcolor='#F4F4F4'><TD align='left' nowrap valign='top' height='30'><B>$mess_gp_fin_form</B></TD><TD align='left'>$date_max</TD></TR>";
  }
  elseif ($annuaire == 1 && $typ_user == "APPRENANT")
  {
    $liste = mysql_query("select * from utilisateur,utilisateur_groupe where
                          utilisateur.util_cdn != $id_user AND
                          utilisateur_groupe.utilgr_groupe_no = $id_grp AND
                          utilisateur.util_cdn = utilisateur_groupe.utilgr_utilisateur_no
                          order by utilisateur.util_nom_lb,utilisateur.util_prenom_lb ASC");
    $nomb_app = mysql_num_rows($liste);
    if ($nomb_app > 0)
    {
      echo "<TR bgcolor='#FFFFFF'><TD width='40%' valign='top' nowrap rawspan='$nomb_app'><B>$mess_suite_ap_grp</B></TD><TD align='left' width=50%><table border=0>";
      $photo=GetDataField ($connect,"select util_photo_lb from utilisateur where util_cdn='$id_user'","util_photo_lb");
      $lien="prescription.php?affiche_fiche_app=1&hgrp=$hgrp&id_util=$id_user&identite=1";
        $lien= urlencode($lien);
        echo "<TR><TD align='left' nowrap><A href=\"trace.php?link=$lien\"";
        if ($photo != "")
        {
           list($w_img, $h_img, $type_img, $attr_img) = getimagesize("images/$photo");
           echo " onMouseOver=\"overlib('', TEXTCOLOR, '#ffffff', TEXTSIZE, 2, WIDTH, $w_img, HEIGHT, $h_img, BACKGROUND, 'images/$photo', PADX, 60, 20, PADY, 20, 20)\" onMouseOut=\"nd()\"";
        }
        else
           echo " title=\"$mess_suite_fp\"";
        echo ">".$prenom_user." ".$nom_user."</A></TD>";
      $i=0;
      while ($itemAnnu=mysql_fetch_object($liste))
      {
        $num_appre = $itemAnnu->util_cdn;
        $nom_app= $itemAnnu->util_nom_lb;
        $prenom_app= $itemAnnu->util_prenom_lb;
        $email =  $itemAnnu->util_email_lb;
        $photo= $itemAnnu->util_photo_lb;
        $lien="prescription.php?numero_groupe=$numero_groupe&affiche_fiche_app=1&hgrp=$hgrp&id_util=$num_appre&identite=1";
        $lien= urlencode($lien);
        echo "<TR height='25'><TD align='left' nowrap><A href=\"trace.php?link=$lien\"";
        if ($photo != "")
        {
           list($w_img, $h_img, $type_img, $attr_img) = getimagesize("images/$photo");
           echo " onMouseOver=\"overlib('', TEXTCOLOR, '#ffffff', TEXTSIZE, 2, WIDTH, $w_img, HEIGHT, $h_img, BACKGROUND, 'images/$photo', PADX, 60, 20, PADY, 20, 20)\" onMouseOut=\"nd()\"";
        }
        else
           echo " title=\"$mess_suite_fp\"";
        $suiteAnnuBlog = '';
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
        echo ">".$prenom_app." ".$nom_app."</A>";
        if ($suiteAnnuBlog != '' )
        {
           $lien_blog = $suiteAnnuBlog;
           $lien_blog = urlencode($lien_blog);
           echo "<span style=\"padding:2px;margin-left:8px;border:1px solid maroon;background:#eee;\">".
                "<A HREF=\"trace.php?link=$lien_blog\" target='_blank'>  Consulter son Blog</A></span>";
        }
        echo "</TD>";
      $i++;
      }
      echo "</table></td></tr>";
    }
  }
  echo "</TABLE>";
  echo "</TD></TR></TABLE></TD></TR></TABLE></TD></TR></TABLE>";
exit;
}
if ($utilisateur > 0)
{
 $num_app = $utilisateur;
}
else
{
 $num_app = $id_user;
}
if (isset($supp) && $supp == 1 && !isset($suppression))
{
  entete_simple($gest_presc." : ".strtolower($msq_supp_titre));
  if ($id_seq != 0)
  {
    $act_query = mysql_query ("select * from activite,suivi1$Ext where
                               activite.act_seq_no = $id_seq and
                               suivi1$Ext.suivi_act_no = activite.act_cdn and
                               suivi1$Ext.suivi_utilisateur_no = $utilisateur and
                              suivi1$Ext.suivi_grp_no = $numero_groupe");
    $Nb_act_seq = mysql_num_rows ($act_query);
    $seq_query = mysql_query ("select * from prescription$Ext where
                               prescription$Ext.presc_parc_no = $id_parc AND
                               prescription$Ext.presc_utilisateur_no = $utilisateur and
                               prescription$Ext.presc_grp_no = $numero_groupe");
    $Nb_seq_parc = mysql_num_rows ($seq_query);
  }
  if ($Nb_act_seq == 1)
  {
     echo "<TR><TD colspan='2' align='center' width='100%'><TABLE bgColor='#FFFFFF' cellspacing='2' width='100%'><TR><TD>";
     echo "$mess_gp_der_act<P></TD></TR>";
     $supp_seq = 1;
     if ($Nb_seq_parc == 1){
       echo "<TR><TD>$mess_gp_der_seq<P></TD></TR>";
       $supp_parc = 1;
     }
     echo "</TABLE></TD></TR>";
  }
  echo "<TR><TD colspan='2'><P>$mess_gp_oui_sup_actseq<P></TD></TR>";
  echo "<FORM NAME='form1' ACTION=\"details_parc.php?hgrp=$hgrp&ouvrir=$ouvrir&numero_groupe=$numero_groupe&utilisateur=$utilisateur&supp_seq=$supp_seq&supp_parc=$supp_parc&id_act=$id&id_parc=$id_parc&a_faire=1&id_seq=$id_seq&seq=$id_seq&deroule[$i]=1&id_ref=$id_ref&visible=$visible&&depasse=$depasse\" METHOD='POST' target='principal'>";
  echo "<INPUT TYPE='HIDDEN' NAME='supp' VALUE=1>";
  echo "<INPUT TYPE='HIDDEN' NAME='suppression' VALUE=1>";
  echo "<INPUT TYPE='HIDDEN' NAME='num' VALUE='$num'>";
  echo "<TR><TD align=left><A HREF=\"javascript:history.back();\" onmouseover=\"img_annonce.src='images/fiche_identite/boutretourb.gif';return true;\" onmouseout=\"img_annonce.src='images/fiche_identite/boutretour.gif'\">";
  echo "<IMG NAME=\"img_annonce\" SRC=\"images/fiche_identite/boutretour.gif\" BORDER='0' onLoad=\"tempImg=new Image(0,0); tempImg.src='images/fiche_identite/boutretourb.gif'\"></A></TD>";
  echo "<TD align='center'><A HREF=\"javascript:void(0);\" onclick=\"javascript:return(confm('javascript:document.form1.submit()'));\" onmouseover=\"img1.src='images/fiche_identite/boutvalidb.gif';return true;\" onmouseout=\"img1.src='images/fiche_identite/boutvalid.gif'\">".
       "<IMG NAME=\"img1\" SRC=\"images/fiche_identite/boutvalid.gif\" BORDER='0' title=\"$mess_admin_valid_supp\" onLoad=\"tempImg=new Image(0,0); tempImg.src='images/fiche_identite/boutvalidb.gif'\"></A>";
  echo "</TD></TR></TABLE></TD></TR></TABLE>";
exit;
}
if (isset($supp) && $supp == 1 && isset($suppression) && $suppression == 1)
{
   $date_op = date("Y-m-d H:i:s");
   $heure_fiche = substr($date_op,11);
   $date_fiche = substr($date_op,0,10);
   $formateur = GetDataField ($connect,"select presc_formateur_no from prescription$Ext where
                                        presc_seq_no = $id_seq and
                                        presc_utilisateur_no = $utilisateur and
                                        presc_grp_no = $numero_groupe","presc_formateur_no");
   $prescripteur = GetDataField ($connect,"select presc_prescripteur_no from prescription$Ext where
                                           presc_seq_no = $id_seq and
                                           presc_utilisateur_no = $utilisateur and
                                           presc_grp_no = $numero_groupe","presc_prescripteur_no");
   $inscripteur = GetDataField ($connect,"select util_auteur_no from utilisateur where util_cdn='$utilisateur'","util_auteur_no");
   if ($formateur == $id_user)
     $qualite = "Formateur";
   elseif ($prescripteur == $id_user)
     $qualite = "Prescripteur";
   elseif ($inscripteur == $id_user)
     $qualite = "Inscripteur";
   elseif ($prescripteur != $id_user && $inscripteur != $id_user && $typ_user == "ADMINISTRATEUR")
     $qualite = "Administrateur";
   else
     $qualite = "Inconnu";
   $nom_act = GetDataField ($connect,"select act_nom_lb from activite where act_cdn=$id_act","act_nom_lb");
   $action_fiche = "Gestion des prescriptions";
   $commentaire = $mess_supact." : ".$nom_act;
   $new_fiche = Donne_ID ($connect,"select max(fiche_cdn) from fiche_suivi");
   $req_fiche = mysql_query("INSERT INTO fiche_suivi
                             (fiche_cdn,fiche_utilisateur_no,fiche_auteur_no,fiche_qualite_lb,fiche_date_dt,
                             fiche_heure_dt,fiche_commentaire_cmt,fiche_grp_no,fiche_parc_no,fiche_seq_no,fiche_act_no,fiche_typaction_lb)
                             VALUES($new_fiche,$utilisateur,$id_user,'$qualite','$date_fiche','$heure_fiche',
                             \"$commentaire\",$numero_groupe,$id_parc,$id_seq,$id_act,\"$action_fiche\")");
   $supprimer = mysql_query("delete from suivi1$Ext where suivi_cdn = $num");
   if (isset($supp_seq) && $supp_seq == 1)
   {
     $titre_seq = GetDataField ($connect,"select seq_titre_lb from sequence where seq_cdn=$id_seq","seq_titre_lb");
     $commentaire = $mess_sup_seq_app." : ".$titre_seq;
     $new_fiche = Donne_ID ($connect,"select max(fiche_cdn) from fiche_suivi");
     $req_fiche = mysql_query("INSERT INTO fiche_suivi (fiche_cdn,fiche_utilisateur_no,fiche_auteur_no,fiche_qualite_lb,
                               fiche_date_dt,fiche_heure_dt,fiche_commentaire_cmt,fiche_grp_no,fiche_parc_no,fiche_seq_no,fiche_act_no,
                               fiche_typaction_lb) VALUES
                               ($new_fiche,$utilisateur,$id_user,'$qualite','$date_fiche','$heure_fiche',\"$commentaire\",
                               $numero_groupe,$id_parc,$id_seq,0,\"$action_fiche\")");
     $supprimer = mysql_query("delete from suivi2$Ext where suiv2_seq_no = $id_seq and suiv2_utilisateur_no = $utilisateur and suiv2_grp_no = $numero_groupe");
     $supprimer = mysql_query("delete from prescription$Ext where presc_seq_no = $id_seq and presc_utilisateur_no = $utilisateur and suiv2_grp_no = $numero_groupe");
     $seq = 0;
     $ouvrir = 1;
     if (isset($supp_parc) && $supp_parc == 1)
     {
       $nom_parc = GetDataField ($connect,"select parcours_nom_lb from parcours where parcours_cdn = $id_parc","parcours_nom_lb");
       $commentaire = $mess_supparc." : ".$nom_parc;
       $new_fiche = Donne_ID ($connect,"select max(fiche_cdn) from fiche_suivi");
       $req_fiche = mysql_query("INSERT INTO fiche_suivi (fiche_cdn,fiche_utilisateur_no,fiche_auteur_no,fiche_qualite_lb,
                                 fiche_date_dt,fiche_heure_dt,fiche_commentaire_cmt,fiche_grp_no,fiche_parc_no,fiche_seq_no,
                                 fiche_act_no,fiche_typaction_lb) VALUES
                                 ($new_fiche,$utilisateur,$id_user,'$qualite','$date_fiche','$heure_fiche',
                                 \"$commentaire\",$numero_groupe,$id_parc,0,0,\"$action_fiche\")");
       $supprimer = mysql_query("delete from suivi3$Ext where suiv3_parc_no = $id_parc and suiv3_utilisateur_no = $utilisateur and presc_grp_no =$numero_groupe");
       $supprimer = mysql_query("delete from prescription$Ext where presc_parc_no = $id_parc and presc_utilisateur_no = $utilisateur and presc_grp_no =$numero_groupe");
       $lien = "details_parc.php?saut=1&hgrp=$hgrp&a_faire=1&utilisateur=$utilisateur&numero_groupe=$numero_groupe";
       $lien = urlencode($lien);
       echo "<script language=\"JavaScript\">";
       echo "document.location.replace(\"trace.php?link=$lien\")";
       echo "</script>";
       exit();
     }
   }
}
if ($ouvrir == 'parcours')
{
              if ($id_parc != 0)
              {
                unset($scormOk);
                $createur = GetDataField ($connect,"select parcours_auteur_no from parcours where parcours_cdn = $id_parc","parcours_auteur_no");
                $nom_parc = GetDataField ($connect,"select parcours_nom_lb from parcours where parcours_cdn = $id_parc","parcours_nom_lb");
                $type_parcours = getdatafield ($connect,"SELECT parcours_type_lb from parcours where parcours_cdn = $id_parc","parcours_type_lb");
                $etat_parc = GetDataField ($connect,"select suiv3_etat_lb from suivi3$Ext where suiv3_parc_no = $id_parc and suiv3_utilisateur_no=$num_app","suiv3_etat_lb");
                if ($etat_parc == "TERMINE")
                  $etat_parc = $mess_suivi_acq;
                elseif ($etat_parc == "A FAIRE")
                  $etat_parc = $mess_lanc_afaire;
                elseif ($etat_parc == "EN COURS")
                  $etat_parc = $mess_lanc_encours;
                elseif ($etat_parc == "ATTENTE")
                  $etat_parc = $mess_lanc_attente;
                $description = GetDataField ($connect,"select parcours_desc_cmt from parcours where parcours_cdn = $id_parc","parcours_desc_cmt");
                $id_ref_parc = GetDataField ($connect,"select parcours_referentiel_no from parcours where parcours_cdn = $id_parc","parcours_referentiel_no");
                $nom_referentiel = GetDataField ($connect,"select ref_desc_cmt from referentiel where ref_cdn = $id_ref_parc","ref_desc_cmt");
                $desc = str_replace ("'","\'",$nom_referentiel);
                $nom_createur = GetDataField ($connect,"select util_nom_lb from utilisateur where util_cdn = $createur","util_nom_lb");
                $prenom_createur = GetDataField ($connect,"select util_prenom_lb from utilisateur where util_cdn = $createur","util_prenom_lb");
              }
              else
              {
                $desc = $mess_gp_nolien_ref;
                $description = $mess_gp_parc_0_desc;
                $nom_createur = $mess_gen_formagri;
                $prenom_createur = $mess_gen_gen_formagri;
                if ($lg == 'fr' || $lg == 'en' )
                  $nom_parc = strtolower($mess_gp_seq_hors_parc);
                else
                  $nom_parc = $mess_gp_seq_hors_parc;
                $nom_referentiel = $mess_gp_nolien_ref;
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
                 $seq_parc_query = mysql_query ("SELECT sum(sequence.seq_duree_nb) from sequence,sequence_parcours where
                                                 sequence_parcours.seqparc_parc_no = $id_parc and
                                                 sequence.seq_cdn = sequence_parcours.seqparc_seq_no");
              $duree_parc = mysql_result($seq_parc_query,0);
              $duree_parc = duree_calc($duree_parc);
              $ch_date_deb = explode ("-",$date_min);
              $date_min = "$ch_date_deb[2]/$ch_date_deb[1]/$ch_date_deb[0]";
              $ch_date_fin = explode ("-",$date_max);
              $date_max = "$ch_date_fin[2]/$ch_date_fin[1]/$ch_date_fin[0]";
              $bgcolorB = '#5b8bab';
              $bgcolorE = '#F2EBDC';
              $req_parcours = mysql_query("SELECT count(*) from prescription$Ext where
                                           presc_utilisateur_no = $num_app and
                                           presc_parc_no = $id_parc AND
                                           presc_grp_no = $numero_groupe");
              $nomb_parc = mysql_result($req_parcours,0);
              echo "<CENTER><TABLE><TR><TD>";
              echo "<TABLE bgColor='#298CA0' cellspacing='2' cellpadding='0' width='600' border='0'>";
              echo "<TR><TD><TABLE bgcolor='#FFFFFF' cellspacing='1' cellpadding='3' width='600'>";
              echo "<TR><TD background=\"images/fond_titre_table.jpg\" colspan='2' height='36' align='center' valign='center'>".
                   "<Font size='3' color='#FFFFFF'><B>$mpa_voir_parc ";
              if (isset($scormOk) && $scormOk == 1)
                 echo " SCORM";
              echo " : $nom_parc</B></FONT></TD></TR>";
              echo "<TR bgcolor='#F4F4F4'><TD nowrap valign='top' width='25%'><B>$mess_desc</B></TD><TD>  ".
                   html_entity_decode($description,ENT_QUOTES,'iso-8859-1')."</TD></TR>";
              if (isset($nom_referentiel) && $nom_referentiel != '')
                  echo "<TR><TD nowrap valign='top' width='25%'><B>$msq_ref</B></TD><TD>$nom_referentiel</TD></TR>";
              if ($id_parc != 0)
                 echo "<TR bgcolor='#F4F4F4'><TD nowrap><B>$msq_etat</B></TD><TD>$etat_parc</TD></TR>";
              echo "<TR bgcolor='#FFFFFF'><TD nowrap><B>$msq_duree_seq</B></TD><TD>  $duree_parc</TD></TR>";
              echo "<TR bgcolor='#F4F4F4'><TD nowrap><B>$mess_gp_date_deb</B></TD><TD>  $date_min</TD></TR>";
              echo "<TR bgcolor='#FFFFFF''><TD nowrap><B>$mess_gp_date_fin</B></TD><TD>  $date_max</TD></TR>";
              $SQL = "select * from forums_modules where fm_module_no = $id_parc";
              $nbf = mysql_num_rows(mysql_query($SQL));
              if ( $nbf > 0)
              {
                   echo "<TR bgcolor='#F4F4F4'><TD>&nbsp;<B>$msg_formod</B></TD><TD nowrap valign='top'>".
                        "<A href=forum_module.php?id_parc=$id_parc>$nbf $mess_mess_mess2</a></TD></TR>";
              }
         echo "</TABLE></CENTER></TD></TR></TABLE>";
         echo "<TABLE height='35' width='520'><TR><TD align=left><Font size='2'><B>$mess_parc_seq  $nomb_parc&nbsp;$mess_plur_seq :</B></FONT></TD></TR></TABLE>";
       $seq_query = mysql_query ("select presc_seq_no from prescription$Ext,suivi2$Ext where presc_parc_no = $id_parc and presc_grp_no = $numero_groupe and presc_utilisateur_no = $num_app and suiv2_utilisateur_no = $num_app and suiv2_seq_no= presc_seq_no AND suiv2_grp_no = $numero_groupe order by presc_datefin_dt,suiv2_ordre_no");
       $nb_seq = mysql_num_rows($seq_query);
       $ii=0;
       while ($ii < $nb_seq)
       {
         $iil=$ii + 1;
         $seq = mysql_result ($seq_query,$ii,"presc_seq_no");
         $nom_seq = GetDataField ($connect,"select seq_titre_lb from sequence where seq_cdn = $seq","seq_titre_lb");
         $mode_seq = GetDataField ($connect,"select seq_type_lb from sequence where seq_cdn = $seq","seq_type_lb");
         $description = GetDataField ($connect,"select seq_desc_cmt from sequence where seq_cdn = $seq","seq_desc_cmt");
         $etat = GetDataField ($connect,"select suiv2_etat_lb from suivi2$Ext where
                                         suiv2_seq_no = $seq and
                                         suiv2_utilisateur_no=$num_app and
                                         suiv2_grp_no = $numero_groupe","suiv2_etat_lb");
         $scormOk = strstr($mode_seq,"SCORM") ? 1 : 0;
         if ($scormOk == 1)
            $duree_seq = GetDataField ($connect,"SELECT seq_duree_nb from sequence where seq_cdn = '$seq'","seq_duree_nb");
         else
         {
            $duree_sequence = mysql_query ("select sum(activite.act_duree_nb) from activite,suivi1$Ext where
                                            activite.act_cdn = suivi1$Ext.suivi_act_no and
                                            activite.act_seq_no=$seq and
                                            suivi1$Ext.suivi_utilisateur_no =$num_app and
                                            suivi1$Ext.suivi_grp_no = $numero_groupe");
            $duree_seq = mysql_result($duree_sequence,0);
         }
         $duree_seq = duree_calc($duree_seq);
         if ($etat == "TERMINE")
           $etat_seq = $mess_suivi_acq;
         elseif ($etat == "A FAIRE")
           $etat_seq = $mess_lanc_afaire;
         elseif ($etat == "EN COURS")
           $etat_seq = $mess_lanc_encours;
         elseif ($etat == "ATTENTE")
           $etat_seq = $mess_lanc_attente;
         $createur = GetDataField ($connect,"select seq_auteur_no from sequence where seq_cdn = $seq","seq_auteur_no");
         $nom_createur = GetDataField ($connect,"select util_nom_lb from utilisateur where util_cdn = $createur","util_nom_lb");
         $prenom_createur = GetDataField ($connect,"select util_prenom_lb from utilisateur where util_cdn = $createur","util_prenom_lb");
         $prescripteur = GetDataField ($connect,"select presc_prescripteur_no from prescription$Ext where
                                                 presc_seq_no = $seq and
                                                 presc_utilisateur_no = $num_app and
                                                 presc_grp_no = $numero_groupe","presc_prescripteur_no");
         $nom_prescripteur = GetDataField ($connect,"select util_nom_lb from utilisateur where util_cdn = $prescripteur","util_nom_lb");
         $prenom_prescripteur = GetDataField ($connect,"select util_prenom_lb from utilisateur where util_cdn = $prescripteur","util_prenom_lb");
         $formateur = GetDataField ($connect,"select presc_formateur_no from prescription$Ext where
                                              presc_seq_no = $seq and
                                              presc_utilisateur_no = $num_app and
                                              presc_grp_no = $numero_groupe","presc_formateur_no");
         $nom_formateur = GetDataField ($connect,"select util_nom_lb from utilisateur where util_cdn = $formateur","util_nom_lb");
         $prenom_formateur = GetDataField ($connect,"select util_prenom_lb from utilisateur where util_cdn = $formateur","util_prenom_lb");
         $date_deb = GetDataField ($connect,"select presc_datedeb_dt from prescription$Ext where
                                             presc_seq_no = $seq and
                                             presc_utilisateur_no = $num_app and
                                             presc_grp_no = $numero_groupe","presc_datedeb_dt");
         $ch_date_deb = explode ("-",$date_deb);
         $date_debut = "$ch_date_deb[2]/$ch_date_deb[1]/$ch_date_deb[0]";
         $date_fin = GetDataField ($connect,"select presc_datefin_dt from prescription$Ext where presc_seq_no = $seq and presc_utilisateur_no = $num_app","presc_datefin_dt");
         $ch_date_fin = explode ("-",$date_fin);
         $date_fin = "$ch_date_fin[2]/$ch_date_fin[1]/$ch_date_fin[0]";
         if ($scormOk == 1)
             $req_seq = mysql_query("SELECT scorm_util_module$Ext.user_module_cdn from scorm_util_module$Ext,scorm_module where
                                     scorm_util_module$Ext.user_module_no = $num_app AND
                                     scorm_util_module$Ext.mod_module_no = scorm_module.mod_cdn AND
                                     scorm_module.mod_seq_no = $seq AND
                                     scorm_util_module$Ext.mod_grp_no = $numero_groupe and
                                     scorm_module.mod_content_type_lb != 'LABEL'");
         else
             $req_seq = mysql_query("SELECT suivi1$Ext.suivi_cdn from suivi1$Ext,activite where
                                     suivi1$Ext.suivi_utilisateur_no = $num_app and
                                     suivi1$Ext.suivi_act_no = activite.act_cdn and
                                     activite.act_seq_no = $seq and
                                     suivi1$Ext.suivi_grp_no = $numero_groupe");
         $nomb_activ = mysql_num_rows($req_seq);
         $seq_ref_query = mysql_query ("select seqref_referentiel_no from sequence_referentiel where seqref_seq_no = $seq");
         $nb_seq_ref = mysql_num_rows ($seq_ref_query);
         if ($nb_seq_ref == 1)
         {
           $id_ref = GetDataField ($connect,"select seqref_referentiel_no from sequence_referentiel where seqref_seq_no = $seq","seqref_referentiel_no");
           if ($id_ref == 0)
             $nom_referentiel = $msq_sl;
           else
             $nom_referentiel = GetDataField ($connect,"select ref_nomabrege_lb from referentiel where ref_cdn = $id_ref","ref_nomabrege_lb");
         }
         else
             $nom_referentiel ='';
       if ($utilisateur > 0)
       {
         $id_app = $utilisateur;
       }
       else
       {
         $id_app = $id_user;
       }
       $id_nom = GetDataField ($connect,"select util_nom_lb from utilisateur where util_cdn = $id_app","util_nom_lb");
       $id_prenom = GetDataField ($connect,"select util_prenom_lb  from utilisateur where util_cdn = $id_app","util_prenom_lb");
       $suivi_etat_seq = GetDataField ($connect,"select suiv2_etat_lb from suivi2$Ext where
                                                 suiv2_seq_no = '$seq' and
                                                 suiv2_utilisateur_no = $id_app and
                                                 suiv2_grp_no = $numero_groupe","suiv2_etat_lb");
       $titre_sequence = GetDataField ($connect,"select seq_titre_lb from sequence where seq_cdn = '$seq'","seq_titre_lb");
       $nom_sequence = GetDataField ($connect,"select seq_desc_cmt from sequence where seq_cdn = '$seq'","seq_desc_cmt");
       if ($nom_sequence == '')
          $nom_sequence = $msq_no_decrite;
       $bgcolor2 = '#006699';
       $bgcolor1 = '#F8F2E4';
       echo "<TABLE bgColor='#298CA0' cellspacing='2' cellpadding='0' width='520' border='0'>";
       echo "<TR><TD align='center' bgcolor='#FFFFFF'><TABLE cellspacing='1' cellpadding='0' width='520'>";
       echo "<TR><TD background=\"images/fond_titre_table.jpg\" colspan='2' height='30' align='left' valign='center'><Font size='2' color='#FFFFFF'><B>&nbsp;";
      if ($nb_seq > 1)
        echo "$msq_seq $iil : ";
      echo "$nom_seq</B></FONT></TD></TR><TR><TD colspan='2' width='100%'><TABLE cellspacing='1' cellpadding='2' width='100%'>";
      echo "<TR bgcolor='#FFFFFF'><TD nowrap width='40%' valign='top'><B>$mess_desc</B></TD><TD>".
                   html_entity_decode($description,ENT_QUOTES,'iso-8859-1')."</TD></TR>";
      if (isset($nom_referentiel) && ($nom_referentiel != '' && $nom_referentiel != $msq_sl))
         echo "<TR bgcolor='#F4F4F4'><TD nowrap valign='top' width='25%'><B>$msq_ref</B></TD><TD>$nom_referentiel</TD></TR>";
      if ($utilisateur > 0)
      {
         echo "<TR bgcolor='#F4F4F4'><TD nowrap width='40%'><B>$mess_cree_format</B></TD><TD>$prenom_createur $nom_createur</TD></TR>";
         echo "<TR bgcolor='#FFFFFF'><TD nowrap width='40%'><B>$mess_suiv_format</B></TD><TD>$prenom_formateur $nom_formateur</TD></TR>";
      }
      else
         echo "<TR bgcolor='#F4F4F4'><TD nowrap width='40%'><B>$mess_suiv_format</B></TD><TD>$prenom_formateur $nom_formateur</TD></TR>";
      if ($utilisateur > 0)
        echo "<TR bgcolor='#F4F4F4'><TD nowrap width='40%'><B>$mess_presc_resp_form   </B></TD><TD>$prenom_prescripteur  $nom_prescripteur</TD></TR>";
      echo "<TR bgcolor='#FFFFFF'><TD nowrap width='40%'><B>$mess_parc_seq</B></TD><TD>$nomb_activ ".strtolower($msq_acts)."</TD></TR>";
      echo "<TR bgcolor='#F4F4F4'><TD nowrap width='40%'><B>$msq_etat</B></TD><TD>$etat_seq</TD></TR>";
      echo "<TR bgcolor='#FFFFFF'><TD nowrap width='40%'><B>$msq_duree_seq</B></TD><TD>  $duree_seq</TD></TR>";
      echo "<TR bgcolor='#F4F4F4'><TD nowrap width='40%'><B>$mess_admin_date_debut</B></TD><TD>$date_debut</TD></TR>";
      echo "<TR bgcolor='#FFFFFF'><TD nowrap width='40%'><B>$mess_admin_date_fin</B></TD><TD>$date_fin</TD></TR>";
      $sql = mysql_query ("SELECT * from favoris where fav_seq_no = '$seq'");
      $nbr_fav = mysql_num_rows($sql);
      if ($nbr_fav > 0)
      {
         $lien = "favoris.php?consulter=1&seq=$seq&venu=seq";
         $lien = urlencode($lien);
         echo  "<TR><TD nowrap colspan=2><a href=\"javascript:void(0);\" title=\"$msq_voir_fav\"  ".
               "onclick=\"javascipt:window.open('trace.php?link=$lien','','width=750,height=300,resizable=yes,status=no')\">$msq_voir_fav </a>($nbr_fav)</TD></TR>";
      }
      echo "</TABLE></TD></TR></TABLE></TD></TR></TABLE></TD></TR><TR height='15'><TD>&nbsp;</TD></TR><TR height='25'><TD>";
   $ii++;
   }
   echo "</TD></TR></TABLE></TD></TR></TABLE>";
   exit();
}

if ((isset($seq) && $seq == 0) || !isset($seq))
{
   $lien = "details_parc.php?saut=1&hgrp=$hgrp&a_faire=1&utilisateur=$utilisateur&numero_groupe=$numero_groupe";
   $lien = urlencode($lien);
   echo "<script language=\"JavaScript\">";
   echo "document.location.replace(\"trace.php?link=$lien\")";
   echo "</script>";
   exit();
}
if ($typ_user == 'APPRENANT')
       $id_app = $id_user;
else
       $id_app = $utilisateur;
$id_nom = GetDataField ($connect,"select util_nom_lb from utilisateur where util_cdn = $id_app","util_nom_lb");
$id_prenom = GetDataField ($connect,"select util_prenom_lb from utilisateur where util_cdn = $id_app","util_prenom_lb");
$login_user=GetDataField ($connect,"select util_login_lb from utilisateur where util_cdn='$id_app'","util_login_lb");
if (isset($utilisateur) && $utilisateur > 0)
   $Register = base64url_encode($utilisateur.'_'.$login_user.'|'.str_replace('http://','',str_replace('.educagri.fr','',$adresse_http)));
else
   $Register = base64url_encode($_COOKIE["monID"].'_'.$_COOKIE["monLogin"].'|'.str_replace('http://','',str_replace('.educagri.fr','',$adresse_http)));
$RegisterTwit = $Register;
$id_classe = GetDataField ($connect,"SELECT grp_classe_on from groupe where grp_cdn  = $numero_groupe","grp_classe_on");
$type_sequence = getdatafield ($connect,"SELECT seq_type_lb from sequence where seq_cdn = $seq","seq_type_lb");
$scormOk = (strstr($type_sequence,"SCORM")) ? 1 : 0;
if ($scormOk == 1)
       $act_query = mysql_query("SELECT * from scorm_util_module$Ext,scorm_module where
                                 scorm_util_module$Ext.user_module_no = $id_app AND
                                 scorm_util_module$Ext.mod_module_no = scorm_module.mod_cdn AND
                                 scorm_module.mod_seq_no = $seq AND
                                 scorm_util_module$Ext.mod_grp_no = $numero_groupe AND
                                 scorm_module.mod_content_type_lb != 'LABEL'
                                 order by scorm_module.mod_cdn");
else
       $act_query = mysql_query ("select * from activite,suivi1$Ext where
                                  (activite.act_seq_no = $seq OR suivi_seqajout_no = $seq) and
                                  suivi1$Ext.suivi_act_no = activite.act_cdn and
                                  suivi1$Ext.suivi_utilisateur_no = $id_app and
                                  suivi1$Ext.suivi_grp_no = $numero_groupe
                                  order by suivi1$Ext.suivi_seqajout_no,activite.act_ordre_nb");
$Nb_act_seq = mysql_num_rows ($act_query);

if ($Nb_act_seq == 0)
{
      $lien = "details_parc.php?saut=1&hgrp=$hgrp&a_faire=1&utilisateur=$utilisateur&numero_groupe=$numero_groupe";
      $lien = urlencode($lien);
      echo "<script language=\"JavaScript\">";
      echo "document.location.replace(\"trace.php?link=$lien\")";
      echo "</script>";
      exit();
}
else
{
       if (isset($utilisateur) && $utilisateur > 0)
       {
         $id_email = GetDataField ($connect,"select util_email_lb from utilisateur where util_cdn = '$id_app'","util_email_lb");
         $tut_query = mysql_query ("SELECT count(tut_cdn) from tuteur where tut_apprenant_no=$utilisateur AND tut_tuteur_no=$id_user");
         $nb_tut = mysql_result ($tut_query,0);
         $superviseur = GetDataField ($connect,"select grp_tuteur_no from groupe where grp_cdn = $numero_groupe","grp_tuteur_no");
         $resp_grp = GetDataField ($connect,"select grp_resp_no from groupe where grp_cdn = $numero_groupe","grp_resp_no");
         $inscripteur = GetDataField ($connect,"select util_auteur_no from utilisateur where util_cdn = $utilisateur","util_auteur_no");
         $reqtut = mysql_query ("select distinct utilisateur.util_cdn from
                                 tuteur,utilisateur,groupe where
                                 (tuteur.tut_tuteur_no = $id_user AND
                                 utilisateur.util_cdn = $id_user AND
                                 tuteur.tut_apprenant_no = $utilisateur) OR (groupe.grp_tuteur_no = $id_user AND
                                 utilisateur.util_cdn=$id_user AND
                                 groupe.grp_cdn = $numero_groupe)");
         $nomb_app = mysql_num_rows($reqtut);
         if ($nomb_app > 0)
           $tut = mysql_result($reqtut,0,"utilisateur.util_cdn");
       }
       $prescQuery = mysql_query("select * from prescription$Ext where
                                            presc_seq_no = $seq and presc_parc_no = $id_parc and
                                            presc_utilisateur_no = $id_app and
                                            presc_grp_no = $numero_groupe");
       $prescItem = mysql_fetch_object($prescQuery);
       $actform = $prescItem->presc_formateur_no;
       $act_form = $prescItem->presc_formateur_no;
       $prescripteur = $prescItem->presc_prescripteur_no;
       $id_email = GetDataField ($connect,"select util_email_lb from utilisateur where util_cdn = '$actform'","util_email_lb");
       if($actform == $id_user || $typ_user =='ADMINISTRATEUR')
          $_SESSION["DroitsWiki"] = 1;
       if ($id_parc == 0)
            $nom_parc = $mess_gp_seq_hors_parc;
       else
            $nom_parc = GetDataField ($connect,"select parcours_nom_lb from parcours where parcours_cdn = $id_parc","parcours_nom_lb");
       $suiviSeqQuery = mysql_query("select * from suivi2$Ext,sequence where
                                                   suiv2_seq_no = '$seq' and seq_cdn = '$seq' and
                                                   suiv2_utilisateur_no = $id_app and
                                                   suiv2_grp_no = $numero_groupe");
         $suiviSeqItem = mysql_fetch_object($suiviSeqQuery);
         $suivi_etat_seq = $suiviSeqItem->suiv2_etat_lb;
         $titre_sequence =  $suiviSeqItem->seq_titre_lb;
         $auteur_sequence =  $suiviSeqItem->seq_auteur_no;
         $nom_sequence =  $suiviSeqItem->seq_desc_cmt;
         $carac_desc = strlen(strip_tags(html_entity_decode($nom_sequence,ENT_QUOTES,'iso-8859-1')));
         $desc_seq = "<span font-size:11px;\">$nom_sequence</span>";
         $ordre_sequence = GetDataField ($connect,"select seq_ordreact_on from sequence where seq_cdn = '$seq'","seq_ordreact_on");
         $ordre_act = $ordre_sequence;
         if ($nom_sequence == '')
            $nom_sequence = $msq_mess_no_titre;
         $ii=0;
         $increment = 0;$increment1 = 0;
          while ($ii != $Nb_act_seq)
          {
            if ($scormOk == 1)
            {
               $etat_activ = mysql_result ($act_query,$ii,"lesson_status");
               $credit_activ = mysql_result ($act_query,$ii,"credit");
            }
            else
               $etat_activ = mysql_result ($act_query,$ii,"suivi_etat_lb");
            if ($etat_activ == 'TERMINE' || $credit_activ == 'CREDIT' || $etat_activ == 'COMPLETED')
              $increment++;
            if (($etat_activ == 'INCOMPLETE' || $etat_activ == 'NOT ATTEMPTED') && $scormOk == 1 )
              $increment1++;
            $ii++;
          }
          if ($scormOk == 1 && $increment1 > 0)
          {
             $fini_seq = mysql_query("update suivi2$Ext set suiv2_etat_lb='EN COURS' where
                                      suiv2_seq_no = '$seq' and
                                      suiv2_utilisateur_no = $id_app and
                                      suiv2_grp_no = $numero_groupe");
             $fini_parc = mysql_query("update suivi3$Ext set suiv3_etat_lb='EN COURS' where
                                       suiv3_parc_no = '$id_parc' and
                                       suiv3_utilisateur_no = $id_app and
                                       suiv3_grp_no = $numero_groupe");
          }
          if ($increment == $Nb_act_seq){
             $fini_seq = mysql_query("update suivi2$Ext set suiv2_etat_lb='TERMINE' where
                                      suiv2_seq_no = '$seq' and
                                      suiv2_utilisateur_no = $id_app and
                                      suiv2_grp_no = $numero_groupe");
             $fini_parc = mysql_query("update suivi3$Ext set suiv3_etat_lb='TERMINE' where
                                       suiv3_parc_no = '$id_parc' and
                                       suiv3_utilisateur_no = $id_app and
                                       suiv3_grp_no = $numero_groupe");
          }
         $bgcolor1 = '#F4F4F4';
         $bgcolor2 = '#FFFFFF';
         echo "<center><TABLE border='0' width='98%'><TR><TD>";
         echo "<TABLE width='100%'><TR><TD>";
         $req_blog = mysql_query("SELECT * FROM blog where blog_auteur_no = $num_app");
         $nb_blog = mysql_num_rows($req_blog);
         if ($nb_blog > 0)
         {
            if ($num_app == $_SESSION['id_user'])
            {
                $GrpBlg = $_SESSION['numero_groupe'];
                $nomGrp = GetDataField ($connect,"select grp_nom_lb from groupe WHERE grp_cdn = $GrpBlg","grp_nom_lb");
                $lienP1 = 'blog/bloglib.php?partage=1&grp=1&groupe='.$GrpBlg;
                $lienP2 = 'blog/bloglib.php?partage=1&app=1';
                $lienP3 = 'blog/bloglib.php?partage=1&all=1';
                $partage = '<div id="menu_vert" style="float:left;">'.
                       '<span onClick="javascript:$(\'#ul25\').toggle();" title="Partager mon blog. <br />Cliquez sur la flèche pour ouvrir/refermer" alt=""> '.
                       '<img src="blog/images/flechebas.gif" border="0" style="margin:5px 2px 0 4px;cursor:pointer;"></span>'.
                       '<ul id="ul25" style="display:none;width:270px;font-weight:bold;height:100px; border:0;"> '.
                       '<span style="font-weight:bold;">Partager mon blog avec : </span>';
                $nbGrp = mysql_num_rows(mysql_query("select * from utilisateur_groupe where utilgr_utilisateur_no ='$num_app'"));
                $partage .= "<li>";
                $affPartage = ($nbGrp > 1) ? 'La formation "'.$nomGrp.'"' : "Les apprenants de ma formation";
                $partage .= '<span style="float:left;margin-left:0px;padding:0 8px 0 0;">'.
                        '<a href="javascript:void(0);" onClick="javascript:appelle_ajax(\''.$lienP1.'\');$(\'#ul25\').css(\'display\',\'none\');" '.
                        '>- '.$affPartage.'</a></span>';
                $partage .= '</li>';
                $nbGrp = mysql_num_rows(mysql_query("select * from utilisateur_groupe where utilgr_utilisateur_no ='$num_app'"));
                if ($nbGrp > 1)
                {
                    $partage .= "<li>";
                    $partage .= '<span style="float:left;margin-left:0px;padding:0 8px 0 0;">'.
                        '<a href="javascript:void(0);" onClick="javascript:appelle_ajax(\''.$lienP2.'\');$(\'#ul25\').css(\'display\',\'none\');" '.
                        '>- Les apprenants de toutes mes formations</a></span>';
                    $partage .= '</li>';
                }
         /*       $partage .= "<li>";
                $partage .= '<span style="float:left;margin-left:0px;padding:0 8px 0 0;">'.
                        '<a href="javascript:void(0);" onClick="javascript:appelle_ajax(\''.$lienP3.'\');$(\'#ul25\').css(\'display\',\'none\');" '.
                        '>- tous</a></span>';
                $partage .= '</li>';*/
                $reqShareImg = GetDataField ($connect,"select bgshr_img_on FROM blogshare where bgshr_auteur_no = $num_app","bgshr_img_on");
                $suiteShareImg = ($reqShareImg == 0) ? 'Partager aussi mes images ou photos' : 'Ne plus partager mes images ou photos';
                $lienP4 = ($reqShareImg == 0) ? 'blog/bloglib.php?partage=1&img=1': 'blog/bloglib.php?partage=1&img=0';
                $partage .= "<li>";
                $partage .= '<span style="float:left;margin-left:0px;padding:0 8px 0 0;">'.
                        '<a href="javascript:void(0);" onClick="javascript:appelle_ajax(\''.$lienP4.'\');$(\'#ul25\').css(\'display\',\'none\');" '.
                        '>- '.$suiteShareImg.'</a></span>';
                $partage .= '</li>';
                $partage .= '</ul></div>';
            }
            $titreBlog = ($num_app == $_SESSION['id_user'])? 'Mon Blog' : 'Le Blog';
            if (strstr(mysql_result($req_blog,0,'blog_consigne_cmt'),'---------') && $num_app == $_SESSION['id_user'])
            {
               $lien= "blog/blogAjout.php?ajt=1&numApp=$num_app&keepThis=true&TB_iframe=true&height=260&width=570";
               echo "<div id='Blog' style=\"float:left;padding:0 8px 0 8px;\"><a href='".$lien."' class='thickbox'".
                    " name='Finaliser la création de mon blog ' title='Mon Blog sur Formagri'>
                    <div class='bouton_vert' onMouseOver=\"\$(this).removeClass();\$(this).addClass('bouton_hover');\" ".
                    "onMouseOut=\"\$(this).removeClass();\$(this).addClass('bouton_vert');\">".
                    "$titreBlog</div></a>$partage</div>";
            }
            elseif(!strstr(mysql_result($req_blog,0,'blog_consigne_cmt'),'---------'))
            {
               $suiteBlog = ($num_app == $_SESSION['id_user']) ? $partage:'' ;
               $lien_blog = "blog/blogOpen.php?numApp=$num_app&id_clan=$num_app";
               $lien_blog = urlencode($lien_blog);
               echo "<div id='Blog' style=\"float:left;padding:0 8px 0 8px;\">".
                    "<A HREF=\"trace.php?link=$lien_blog\" target='_blank' class='bouton_new'>$titreBlog".nbsp(4)."</A> $suiteBlog</div>";
            }
         }
         elseif($nb_blog == 0 && $num_app == $_SESSION['id_user'])
         {
            $lien= "blog/blogAjout.php?ajt=1&numApp=$num_app&keepThis=true&TB_iframe=true&height=260&width=570";
            echo "<div id='Blog' style=\"float:left;padding:0 4px 0 8px;\"><a href='".$lien."' class='thickbox'".
                 " name='Créer Mon blog ' title='Mon Blog sur Formagri'>
                 <div class='bouton_vert' onMouseOver=\"\$(this).removeClass();\$(this).addClass('bouton_hover');\" ".
                 "onMouseOut=\"\$(this).removeClass();\$(this).addClass('bouton_vert');\">".
                 "Créer mon Blog</div></a></div>";
         }
         $reqGrp = mysql_query("select * from groupe where grp_cdn='$numero_groupe'");
         $itemGrp = mysql_fetch_object($reqGrp);
         $nom_grpe = $itemGrp->grp_nom_lb;
         $reqForum = mysql_query("select * from forums where name =\"$nom_grpe\"");
         $itemForum = mysql_fetch_object($reqForum);
         $id_forum = $itemForum->id;
         $comment_forum = $itemForum->name;
         $leType = ($utilisateur > 0) ? 'formateur' : 'apprenant';
         $leCours =($utilisateur > 0) ? '|0|0|-|'.$numero_groupe : '|0|0|0|'.$numero_groupe;
         $course =  base64url_encode($leType.$leCours);
         $lien_forum = 'forum/list.php?f='.$id_forum.'&collapse=1'.TinCanTeach ($leType.$leCours,'forum/list.php?f='.$id_forum.'&collapse=1',$adresse_http.'/Forums - ');
         echo '<div id="forum" style="float:left;padding-right:4px;"><div style="float:left;">'.
              '<A HREF=\''.$lien_forum.'\' class="bouton_new" target="_blank">'.$mess_menu_forum.' '.
              $mess_menu_gestion_grp.'</A></div>';
         $trackCourse = base64url_encode('apprenant|0|0|0|'.$numero_groupe);
         $lien = "admin/statements.php?course=$trackCourse&utilisateur=$utilisateur&registration=$Register&Activity=forum&collapse=1&keepThis=true&TB_iframe=true&height=450&width=750";
         echo "<div id='statement_F_$id_forum' style='float:left;display:none;'>
         <script language='javascript'>
                 $.ajax({
                         type: 'GET',
                         url: 'http://lms.annulab.com/TinCanApi/statements.php',
                         data: 'verb=1&utilisateur=$id_app&course=$trackCourse&registration=$Register',
                         success: function(msg)
                         {
                          //alert(msg);
                             if (msg == 'En attente')
                                $('#tincan_F_$id_forum').css('display','none');
                             else
                             {
                                $('#xApi_F_$id_forum').html('<img src=\"images/icones/ampoule18.gif\" border=0 title=\"Consultations du forum\">');
                                $('#titreFiche').html('Suivi');
                             }
                         }
                 });
         </script></div>";
         echo "<DIV id='tincan_F_$id_forum' style='float:left;'><A HREF = \"$lien\" ".
         "name='Forum ".$nom_grpe." : historique' class='thickbox'>".
         "<div id='xApi_F_$id_forum' style='float:left;background-image:".
         "url(\"images/ecran-annonce/ongl01.gif\");margin-left:2px;'></div></A></div></div>";
/*
         $lien_forum="forum/list.php?f=$id_forum&collapse=1";
         $lien_forum = urlencode($lien_forum);
         echo "<div id='forum' style=\"float:left;padding-right:4px;\"><A HREF=\"trace.php?link=$lien_forum\" class='bouton_new'>$mess_menu_forum $mess_menu_gestion_grp</A></div>";
*/
         $SQL = "select * from parcours_forums where parcforum_parc_no = $id_parc";
         $nbf = mysql_num_rows(mysql_query($SQL));
         if ( $nbf > 0)
         {
             $lien_fm="forum_module.php?id_parc=$id_parc";
             $lien_fm = urlencode($lien_fm);
             echo "<div id='forum' style=\"float:left;padding-right:4px;\"><A HREF=\"trace.php?link=$lien_fm\" class='bouton_new'>$msg_formod</A></div>";
         }
         $lien_rp = "modif_rep_fic.php?id_grp=$numero_groupe&formateurs=&nom_grp=$nom_grpe&communes_groupe=1&rallonge=ressources/groupes/$numero_groupe&sousdos=ressources/groupes/$numero_groupe&parent=ressources/groupes&dossier=ressources/groupes/$numero_groupe&fichier=$numero_groupe&direct=dossier";
         $lien_rp = urlencode($lien_rp);
         echo "<div id='dossiers' style=\"float:left;padding-right:4px;\"><A HREF=\"trace.php?link=$lien_rp\" class='bouton_new'>".trim($mess_menu_casier_groupe)."</A></div>";
         if ($suivi_etat_seq != 'A FAIRE' && $utilisateur > 0 && ($typ_user == "ADMINISTRATEUR" || $act_form == $id_user))
         {
               $lien = "modif_parc.php?reinit=1&venue=details&a_faire=1&hgrp=$hgrp&utilisateur=$utilisateur&scormOk=$scormOk&id_seq=$seq&id_parc=$id_parc&numero_groupe=$numero_groupe";
               $lien = urlencode($lien);
               echo "<div id='forum' style=\"float:left;\"><A HREF=\"trace.php?link=$lien\" class='bouton_new' ".bulle($mess_reinit,"","LEFT","ABOVE",150)."$msg_seq_rinit</A></div>";
         }
         $numWkG = 10000+$numero_groupe;
         $nbWkGrp = mysql_num_rows(mysql_query("select * from wikiapp where wkapp_app_no=$num_app and wkapp_seq_no=$numWkG"));
         if ($nbWkGrp > 0)
         {
            $lien= "wiki/wikiGrp.php?numApp=$num_app&id_seq=$numWkG&id_parc=10000&id_grp=$numero_groupe&keepThis=true&TB_iframe=true&height=260&width=570";
            echo "<div id='wikiGroupe'><a href='".$lien."' class='thickbox'".
                 " name='Wiki(s) de la formation ' title='Documents en ligne communs à cette formation'>
                 <div class='bouton_vert' onMouseOver=\"\$(this).removeClass();\$(this).addClass('bouton_hover');\" ".
                 "onMouseOut=\"\$(this).removeClass();\$(this).addClass('bouton_vert');\">".
                 "Wiki(s)</div></a></div>";
         }
         $ReqMind = mysql_query("select * from mindmap where mindmap_grp_no = '".$numero_groupe."'");
         if(mysql_num_rows($ReqMind) > 0)
         {
            $lien= "mindmap/mindAjout.php?id_grp=$numero_groupe&Provenance=GRP&numero_groupe=$numero_groupe&keepThis=true&TB_iframe=true&height=350&width=550";
             $contentMM  = "<div id='wikiGroupe'>";
             $contentMM .= "<a href=\"$lien\" class='thickbox' name='Cartes heuristiques liées à cette formation'".
                           "title='Cartes heuristiques liées à cette  formation' >";
             $contentMM .= "<div class='bouton_vert' onMouseOver=\"\$(this).removeClass();\$(this).addClass('bouton_hover');\" ".
                           "onMouseOut=\"\$(this).removeClass();\$(this).addClass('bouton_vert');\">".
                           "MindMap(s)</div></a></div>";
             $contentMM .='<SCRIPT language="javascript">
                               $.ajax ({type: "GET",url:"mindmap/unlock.php",data:"id_grp='.$numero_groupe.'&Provenance=GRP"});
                          </script>';
             echo $contentMM;
         }
         if ($TweetOk == 1)
         {
           $nbrTwit = mysql_num_rows(mysql_query("select * from seq_twitter where seqtwit_seq_no='$seq'"));
           $ReqTwitActif = mysql_query("select * from seq_twit_form where seqformtwit_seq_no='$seq' and
                                      seqformtwit_parc_no='$id_parc' and seqformtwit_grp_no='$numero_groupe'");
           $nbrTwitActif = mysql_num_rows($ReqTwitActif);
           if ($nbrTwit > 0 && ($typ_user == "ADMINISTRATEUR" || $act_form == $id_user || $resp_grp == $id_user))
           {
             if ($nbrTwitActif == 0)
             {

               $lien= "ApiTweet/LibSeq/SeqTwit.php?id_seq=$seq&id_parc=$id_parc&id_grp=$numero_groupe&formateur=$act_form&activite=1";
               echo "<div id='Tweet' style=\"float:left;padding-right:4px;\">".
                 "<a href=\"javascript:void(0);\" onClick=\"javascript:jQ_AjaxMsg('$lien');".
                 "setTimeout(function(){document.location.reload()},750);\" ".
                 "title='Créer une activité pour échanger sur Twitter à propos ".
                 "de cette séquence dans le contexte de cette formation de ce module et de cette formation'>".
                 "<div class='bouton_vert' onMouseOver=\"\$(this).removeClass();\$(this).addClass('bouton_hover');\" ".
                 "onMouseOut=\"\$(this).removeClass();\$(this).addClass('bouton_vert');\">".
                 "Twitter</div></a></div>";
             }
             elseif($nbrTwitActif > 0)
             {
               $itemTwit = mysql_fetch_object($ReqTwitActif);
               $itemFlag = $itemTwit->seqformtwit_flag_on;
               $operation = ($itemFlag == 1)? 'Désactiver': 'Activer';
               if ($itemFlag == 1)
                  $lien= "ApiTweet/LibSeq/SeqTwit.php?id_seq=$seq&id_twit=".$itemTwit->seqformtwit_cdn."&activite=0";
               else
                  $lien= "ApiTweet/LibSeq/SeqTwit.php?id_seq=$seq&id_parc=$id_parc&id_grp=$numero_groupe&id_twit=".$itemTwit->seqformtwit_cdn."&activite=1";
               echo "<div id='Tweet' style=\"float:left;padding-right:4px;\">".
                 "<a href=\"javascript:void(0);\" onClick=\"javascript:jQ_AjaxMsg('$lien');".
                 "setTimeout(function(){document.location.reload()},750);\" ".
                 "title='$operation le lien vers Twitter de cette séquence pour ce module et cette formation'>".
                 "<div class='bouton_vert' onMouseOver=\"\$(this).removeClass();\$(this).addClass('bouton_hover');\" ".
                 "onMouseOut=\"\$(this).removeClass();\$(this).addClass('bouton_vert');\">".
                 "Twitter</div></a></div>";
              }
            }
          }
         $req_fiche = mysql_query("SELECT count(fiche_seq_no) FROM fiche_suivi where
                                   fiche_utilisateur_no = $num_app AND
                                   fiche_seq_no = $seq and
                                   fiche_grp_no = $numero_groupe");
         $nb_fiche = mysql_result($req_fiche,0);
         $lien_details_parc = "http://".getenv("SERVER_NAME").getenv("REQUEST_URI");
         $lien_details_parc= str_replace("&","|",$lien_details_parc);
         $lien_fiche = "fiche_suivi.php?utilisateur=$utilisateur&id_seq=$seq&id_parc=$id_parc&consultation=1&numero_groupe=$numero_groupe&lien_details_parc=$lien_details_parc&keepThis=true&TB_iframe=true&height=350&width=650";
         $lien_fiche = urlencode($lien_fiche);
         echo "<div id='fiche_suivi' style=\"float:left;padding-right:4px;\">".
              "<a HREF=\"trace.php?link=$lien_fiche\" class='thickbox'>".
              "<div class='bouton_vert' onMouseOver=\"\$(this).removeClass();\$(this).addClass('bouton_hover');\" ".
                           "onMouseOut=\"\$(this).removeClass();\$(this).addClass('bouton_vert');\">".
                           "<div id='titreFiche'>Fiche de suivi</div></div></a></div>";
         $fichier_aide = ($typ_user == "APPRENANT") ? "formation_apprenant" : "formation_formateur";
         echo aide_div("$fichier_aide",0,0,0,0);
         $emailId = ($utilisateur > 0) ? $id_app : $actform;
         $aQui = ($utilisateur > 0) ? 'apprenant' : 'formateur';
         if ($id_email != '')
         {
            $lien = "mail.php?contacter=1&a_qui=$id_email&num=$emailId&formation=1";
            $lien = urlencode($lien);
            echo "<div id='fiche' style=\"float:left;padding-left:4px;margin-top:2px;\">".
                "<A HREF=\"javascript:void(0);\" title='Envoyer un mail à votre $aQui' ".
                 " onclick=\"window.open('trace.php?link=$lien','','width=680,height=520,resizable=yes,status=no')\">".
                 "<IMG SRC=\"images/ecran-annonce/icolettrew.gif\" BORDER='0'></A></div>";
         }
         echo msgInst($emailId,$aQui);
         echo "</TD></TR></TABLE>";
         echo "<TABLE border='0' width=100%><TR><TD align=left valign='center'><TABLE cellpadding='1' cellspacing='0' border='0' width=100%>";
         echo "<TR><TD valign='center' class='sous_titre' width=100%><span style=\"font-size:13px;font-weight: bold; width:100%;\">$msq_seq : ";
         echo "$titre_sequence</span><br/>".html_entity_decode($desc_seq,ENT_QUOTES,'iso-8859-1');
         echo "</TD></TR></TABLE></TD></TR><TR><TD>";
         $prerequis = "";
         $proposable = 1;
         $prereq_query = mysql_query ("SELECT * from prerequis WHERE prereq_seq_no = $seq");
         $nb_prereq = mysql_num_rows ($prereq_query);
         $jj = 0;
         $motif = '';
         $affiche_prereq = "<font size=2><B>$msq_prereq</B></font><BR>";// initialisation de l'affichage
         while ($jj < $nb_prereq)
         {
                  //on raisonne selon le type de condition
                  $type_condition = mysql_result ($prereq_query,$jj,"prereq_typcondition_lb");
                  if ($type_condition == 'SEQUENCE')
                  {
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
                        $nom_seq_req = GetDataField ($connect,"select seq_titre_lb from sequence where
                                                               seq_cdn = $condition","seq_titre_lb");
                        $affiche_prereq .= "<B>$msq_seq</B> : $nom_seq_req<BR>";
                        if ($etat_seq_req != 'TERMINE')
                        {
                          $motif = $mess_gp_seq_preq;
                          $proposable = 0;
                          $prerequis .= "<BR>- $msq_seq : $nom_seq_req";
                        }
                      }
                  }

                  //on a besoin du numero de l'activite pour recuperer les notes
                  if ($type_condition == 'ACTIVITE')
                  {
                      $condition = mysql_result ($prereq_query,$jj,"prereq_actcondition_no");
                      $num_seq_req = GetDataField ($connect,"select act_seq_no from activite where act_cdn = $condition","act_seq_no");
                      $verif_seq_req = mysql_query ("select count(presc_seq_no) from prescription$Ext where
                                                     presc_seq_no = $num_seq_req and
                                                     presc_utilisateur_no = $num_app and
                                                     presc_grp_no = $numero_groupe");
                      $nb_verif_req = mysql_result($verif_seq_req,0);
                      if ($nb_verif_req > 0 )
                      {
                        $nom_act_req = GetDataField ($connect,"select act_nom_lb from activite where act_cdn = $condition","act_nom_lb");
                        $nom_seq_req = GetDataField ($connect,"select seq_titre_lb from sequence where seq_cdn = $num_seq_req","seq_titre_lb");
                        $etat_act_req = GetDataField ($connect,"select suivi_etat_lb from suivi1$Ext where
                                                                suivi_act_no = $condition and
                                                                suivi_utilisateur_no = $num_app and
                                                                suivi_grp_no = $numero_groupe","suivi_etat_lb");
                        if ($etat_act_req != 'TERMINE')
                        {
                          $motif = $mess_gp_aut_act;
                          $proposable = 0;
                          $prerequis .="<BR>- $msq_activite : \"$nom_act_req\" de la ".strtolower($msq_seq)." \"$nom_seq_req\"";
                        }
                      }
                  }
                  if ($type_condition == 'NOTE')
                  {
                     $condition = mysql_result ($prereq_query,$jj,"prereq_actcondition_no");
                     $num_seq_req = GetDataField ($connect,"select act_seq_no from activite where act_cdn = $condition","act_seq_no");
                     $nom_seq_req = GetDataField ($connect,"select seq_titre_lb from sequence where seq_cdn = $num_seq_req","seq_titre_lb");
                     $verif_seq_req = mysql_query ("select presc_seq_no from prescription$Ext where
                                                  presc_seq_no = $num_seq_req and
                                                  presc_utilisateur_no = $num_app and
                                                  presc_grp_no = $numero_groupe");
                     $nb_verif_req = mysql_num_rows($verif_seq_req);
                     if ($nb_verif_req > 0 )
                     {
                        $etat_act_req = GetDataField ($connect,"select suivi_etat_lb from suivi1$Ext where
                                                                suivi_act_no = $condition and
                                                                suivi_utilisateur_no = $num_app and
                                                                suivi_grp_no = $numero_groupe","suivi_etat_lb");
                        $nom_act_req = GetDataField ($connect,"select act_nom_lb from activite where act_cdn = $condition","act_nom_lb");
                        if ($etat_act_req != 'TERMINE')
                        {
                          $motif = $mess_gp_act_preq." - $msq_activite : \"$nom_act_req\" de la ".strtolower($msq_seq)." \"$nom_seq_req\"";
                          $proposable = 0;
                        }
                        else
                        {
                          $note_min = mysql_result ($prereq_query,$jj,"prereq_notemin_nb1");
                          $note_max = mysql_result ($prereq_query,$jj,"prereq_notemax_nb1");
                          $note_obtenue = GetDataField ($connect,"select suivi_note_nb1 from suivi1$Ext where
                                                                  suivi_act_no = $condition and
                                                                  suivi_utilisateur_no = $num_app and
                                                                  suivi_grp_no = $numero_groupe"  ,"suivi_note_nb1");
                          if (($note_obtenue < $note_min && is_integer($note_obtenue)) || $note_obtenue == $mess_suivi_noacq || $note_obtenue == "D" || $note_obtenue == "E")
                          {
                            $motif = $mess_gp_aut_act;
                            $proposable = 0;
                            $prerequis .="<BR>- $msq_activite : \"$nom_act_req\" de la ".strtolower($msq_seq)." \"$nom_seq_req\"";
                          }
                        }
                     }
                  }
              $jj++;
         }
         $prq = str_replace("|","'",$prq);
         $prq = stripslashes($prq);
         $alerter = "";
         $date_deb = $prescItem->presc_datedeb_dt;
         $ch_date_deb = explode("-",$date_deb);
         $date_debut = "$ch_date_deb[2]/$ch_date_deb[1]/$ch_date_deb[0]";
         $date_fin = $prescItem->presc_datefin_dt;
         $today = date("Y-m-d");
         //On compare la date de deb a la date d'aujourd'hui pour savoir s'il peut encore faire la sequence
         //Pour simplifier le test, on SELECTionne le nombre de jours passes depuis an 0 pour $today et $date_deb
         $nb_date_fin_query = mysql_query ("SELECT TO_DAYS('$date_fin')");
         $nb_date_fin = mysql_result ($nb_date_fin_query,0);
         $nb_date_deb_query = mysql_query ("SELECT TO_DAYS('$date_deb')");
         $nb_date_deb = mysql_result ($nb_date_deb_query,0);
         $nb_today_query = mysql_query ("SELECT TO_DAYS('$today')");
         $nb_today = mysql_result ($nb_today_query,0);
         for ($nn = 1;$nn < 10;$nn++)
         {
             if ($nb_date_fin == ($nb_today+$nn) && $suivi_etat_seq != "TERMINE")
                $avertisseur = 1;
         }
         $depasse=0;
         if ($nb_date_fin < $nb_today)
            $depasse=1;
         if ($nb_date_fin < $nb_today && $suivi_etat_seq == "TERMINE")
            $autorise=1;
         if ($nb_date_deb <= $nb_today)
            $visible = 1;
         else
            $visible = 0;
         echo "<TABLE bgcolor='white' border='0' cellspacing='0' cellpadding='0' width='100%'><TR>";
         if ($prerequis != "")
           $alerter = "<BR><B>".$mess_prq_nterm."</B>";
         if ($proposable == 0)
            $alerter .= " $prerequis";
         if ($visible == 0)
            $alerter .= "&nbsp;: $msq_mess_deb_apres $date_debut</font>";
         $dt_fn = $prescItem->presc_datefin_dt;
         $ch_date_fin = explode("-",$dt_fn);
         $date_fin = "$ch_date_fin[2]/$ch_date_fin[1]/$ch_date_fin[0]";
         if ($depasse == 1 && $suivi_etat_seq != "TERMINE")
              $alerter .= "<BR><B>$mess_acces_depasse : $date_fin</B>";
         if ($alerter != "")
         {
           if(!empty($motif))
              $motif = '<br /><span style="font-size:11px;color:red;">'.$motif.'</span>';
           echo "<TD width='4%' align='left' valign='top'><font size = '2'><IMG SRC='images/gest_parc/icoflashb.gif' border='0'></TD>".
                "<TD width='60%' align='left' valign='top'><TABLE border='0' cellspacing='0' cellpadding='0' width='100%'><TR>";
           echo "<TD width='6'>&nbsp;</TD><TD valign='top'><B><font size='2' color='#B7B7B7'>$mess_seq_nodispo $motif</FONT></B>";
           echo "<font size='2' color='#333333'>$alerter</FONT></TD></TR></TABLE>";
         }
         // Chercher les ressources bibliographiques préconisées par le formateur
         $sql = mysql_query ("SELECT * from favoris where fav_seq_no = '$seq'");
         $nbr_fav = mysql_num_rows($sql);
         if ($nbr_fav > 0){
            $lien = "favoris.php?consulter=1&seq=$seq&venu=seq";
            $lien = urlencode($lien);
            echo "</TD><TD width='2%' align='left' valign='top'><A href=\"javascript:void(0);\" title=\"$msq_voir_fav\" ".
                 "onclick=\"javascipt:window.open('trace.php?link=$lien','','width=640,height=300,resizable=yes,status=no')\" ".
                 "onmouseover=\"img_conseil.src='images/gest_parc/icoconseilb.gif';return true;\" ".
                 "onmouseout=\"img_conseil.src='images/gest_parc/icoconseil.gif'\">";
            echo "<IMG NAME=\"img_conseil\" SRC=\"images/gest_parc/icoconseil.gif\" BORDER='0' ".
                 "onLoad=\"tempImg=new Image(0,0); tempImg.src='images/gest_parc/icoconseilb.gif'\"></A></TD>";
            echo "<TD width='33%' align='left' valign='top'><TABLE border='0' cellspacing='0' cellpadding='0' width='100%'><TR>";
            echo "<TD width='6'>&nbsp;&nbsp;</TD><TD valign='top'><DIV id='sequence'><A href=\"javascript:void(0);\" title=\"$msq_voir_fav\" ".
                 "onclick=\"javascipt:window.open('trace.php?link=$lien','','width=640,height=300,resizable=yes,status=no')\">$mess_menu_prescrites</A></DIV></TD></TR></TABLE>";
         }
         if ($ordre_sequence == "OUI" && $scormOk != 1)
           echo "</TD></TR></TABLE><BR><TABLE><TR><TD colspan='4'><B>$mess_seq_ordre</B></TD></TR></TABLE>";
         elseif ($ordre_sequence == "OUI" && $scormOk == 1)
          echo "</TD></TR></TABLE><BR><TABLE><TR><TD colspan='4'><B>$mess_act_prereq</B></TD></TR></TABLE>";
         else
           echo "</TD></TR></TABLE><BR>";
         if ($scormOk == 1)
         {
              $cours =  (!$utilisateur) ? 1 : '';
              $req_seq = requete_order("*","scorm_module"," mod_seq_no = '$seq' AND mod_visible = 'TRUE'","mod_cdn ASC");
              if ($req_seq == false)
              {
                 echo "Il n'y a aucun résultat pour cette requete";
                exit;
              }
              echo "<CENTER><TABLE bgColor='#298CA0' cellspacing='2' cellpadding='0' width=100%><TR><TD>";
              echo "<TABLE bgColor='#FFFFFF' cellspacing='0' cellpadding='0' width='100%'><TR><TD><TABLE bgColor='#FFFFFF' cellspacing='1' cellpadding='4' width=100%>";
              echo "<TR height='34'>";
              echo "<TD align='left' background=\"images/fond_titre_table.jpg\"><FONT COLOR=white size='2'><b>$msq_activite : ".strtolower($mess_fav_tit)."</b></FONT></TD>";
              echo "<TD  align='left' background=\"images/fond_titre_table.jpg\"><FONT COLOR=white size='2'><b>$msq_consigne_act_form</b></FONT></TD>";
              echo "<TD align='center' background=\"images/fond_titre_table.jpg\"><FONT COLOR=white size='2'><b>$mess_demarrer</b></FONT></TD>";
              echo "<TD align='left' background=\"images/fond_titre_table.jpg\"><FONT COLOR=white size='2'><b>&nbsp;&nbsp;$msq_etat&nbsp;&nbsp;</b></FONT></TD>";
              echo "<TD align='left' background=\"images/fond_titre_table.jpg\"><FONT COLOR=white size='2'><b>$msq_tit_label</b></FONT></TD>";
              if  ($typ_user == "ADMINISTRATEUR" || $act_form == $id_user)
                  echo "<TD align='left' background=\"images/fond_titre_table.jpg\"><FONT COLOR=white size='2'><b>Réinit</b></FONT></TD></TR>";
              else
                  echo "</tr>";
              $sco[]=array();
              while ($item = mysql_fetch_object($req_seq))
              {
                 $launch='';
                 $i++;
                 $sco['id_mod'][$i] = $item->mod_cdn;
                 $sco['titre_mod'][$i] = $item->mod_titre_lb;
                 $sco['consigne_mod'][$i] = DelAmp($item->mod_consigne_cmt);
                 $sco['desc_mod'][$i] = DelAmp($item->mod_desc_cmt);
                 $sco['visible'][$i] = $item->mod_visible;
                 $sco['type_mod'][$i] = $item->mod_content_type_lb;
                 $sco['url_mod'][$i] = $item->mod_launch_lb;
                 $sco['ordre_mod'][$i] = $item->mod_numero_lb;
                 $sco['parent_mod'][$i] = $item-> mod_pere_lb;
                 $sco['niveau_mod'][$i] = $item->mod_niveau_no;
                 $sco['prereq_mod'][$i] = $item->mod_prereq_lb;
                 $sco['datafromlms'][$i] = $item->mod_datafromlms;

                 unset($etat_prereq); unset($code_prereq);unset($titre_prereq);unset($noLink);
                 if ($sco['prereq_mod'][$i] != "")
                 {
                   $code_prereq = GetDataField ($connect,"select mod_cdn from scorm_module where mod_numero_lb = \"".$sco['prereq_mod'][$i]."\" AND mod_seq_no ='$seq'","mod_cdn");
                   if ($code_prereq > 0)
                   {
                    $etat_prereq = GetDataField ($connect,"select lesson_status from scorm_util_module$Ext where
                                                           mod_module_no = '$code_prereq' AND
                                                           user_module_no='$id_app' and
                                                           mod_grp_no = $numero_groupe","lesson_status");
                    $titre_prereq = GetDataField ($connect,"select mod_titre_lb from scorm_module where mod_cdn = '$code_prereq'","mod_titre_lb");
                    if ($etat_prereq == "NOT ATTEMPTED" || $etat_prereq == "INCOMPLETE" || $etat_prereq == "BROWSED")
                       $noLink = 1;
                   }
                 }
                 $etat = GetDataField ($connect,"select lesson_status from scorm_util_module$Ext where
                                                 mod_module_no = '".$sco['id_mod'][$i]."' AND
                                                 user_module_no='$id_app' and
                                                 mod_grp_no = $numero_groupe","lesson_status");
                 $credite = GetDataField ($connect,"select credit from scorm_util_module$Ext where
                                                    mod_module_no = '".$sco['id_mod'][$i]."' AND
                                                    user_module_no='$id_app' and
                                                    mod_grp_no = $numero_groupe","credit");
                 if ($etat == "COMPLETED" || $etat == "PASSED") $etat_img = $mess_fait;
                 if ($etat == "NOT ATTEMPTED") $etat_img = $mess_lanc_afaire;
                 if ($etat == "FAILED") $etat_img = $mess_echec;
                 if ($etat == "INCOMPLETE") $etat_img = $mess_nt;
                 if ($etat == "BROWSED") $etat_img = $mess_vu;
                 $Gparent='';$GpereBis ='';
                 if (isset($id_dvlp))
                 {
                    $Gparent  = GetDataField ($connect,"select mod_pere_lb from scorm_module where mod_numero_lb = \"$ordre\" AND mod_seq_no ='$seq'","mod_pere_lb");
                    if ($sco['niveau_mod'][$i] > 2)
                       $GpereBis = GetDataField ($connect,"select mod_pere_lb from scorm_module where mod_numero_lb=\" AND mod_seq_no ='$seq'".$sco['parent_mod'][$i]."\"","mod_pere_lb");
                 }
                 if (strstr($type_sequence,"SCORM") && !strstr($type_sequence,"AICC"))
                 {
                    if (!strstr($sco['url_mod'][$i],"http://") && $sco['url_mod'][$i] != '')
                    {
                       $launch = "$host/".$sco['url_mod'][$i];
                       $supplement = "";
                    }elseif (strstr($sco['url_mod'][$i],"http://"))
                    {
                       $launch = $sco['url_mod'][$i];
                       $supplement = "X";
                    }
                    if ($sco['datafromlms'][$i] != '')
                       $launch .="?".trim($sco['datafromlms'][$i]);
                    if (strstr($launch,$adresse_http) == TRUE)
                       $scorm_externe = 1;
                    else
                       $scorm_externe = 0;
                    $launcher = $launch;
                 }
                 elseif (strstr($type_sequence,"SCORM") && strstr($type_sequence,"AICC"))
                 {
                    if (!strstr($sco['url_mod'][$i],"http://") && $sco['url_mod'][$i] != '')
                       $launch = "$host/".$sco['url_mod'][$i];
                    elseif (strstr($sco['url_mod'][$i],"http://"))
                       $launch = $sco['url_mod'][$i];
                    $session_lms = md5($login)."|".$sco['id_mod'][$i]."|$seq|$id_parc|$id_user|$numero_groupe";
                    $suitable = "aicc_sid=$session_lms&aicc_url=".urlencode("$adresse_http/scorm/aicc.php")."&vendor_param=".trim($sco['datafromlms'][$i]);
                    $launcher = $launch."?aicc_sid=".$session_lms."&aicc_url=$suitable&vendor_param=".trim($sco['datafromlms'][$i]);
                    $launch = urlencode($launch);
                 }
                 $sco['desc_mod'][$i] = str_replace("(","-",$sco['desc_mod'][$i]);
                 $sco['desc_mod'][$i] = str_replace(")","-",$sco['desc_mod'][$i]);
                 $colonnes=($launch == "") ? 4 : 1;
                 $affiche_index .= couleur_tr($i,"")."<TD align='left' valign='top' colspan='$colonnes'>";
                 if (!isset($noLink) && $launch != "" && $proposable == 1)
                    $affiche_index .= "<Div id='sequence'><A HREF=\"javascript: void(0);\">".$sco['titre_mod'][$i]."</A></DIV></TD>";
                 elseif ($launch != "" && $noLink != 1 && !$utilisateur && $proposable != 1 && $visible !=1)
                    $affiche_index .= "<Div id='sequence'><A HREF=\"javascript: void(0);\" ".bulle("$mess_seq_nodispo $alerter","","CENTER","ABOVE",250).$sco['titre_mod'][$i]."</A></DIV></TD>";
                 elseif ($launch != "" && $noLink != 1 && $utilisateur > 0)
                    $affiche_index .= "<Div id='sequence'><A HREF=\"javascript: void(0);\">".$sco['titre_mod'][$i]."</A></DIV></TD>";
                 elseif ($launch != "" && $noLink == 1)
                    $affiche_index .= "<B><acronym title=\"$msq_act_prereq : $titre_prereq\">".$sco['titre_mod'][$i]."</acronym></B></TD>";
                 elseif ($launch == "")
                    $affiche_index .= "<B><acronym title=\"".$sco['titre_mod'][$i]." $mess_labnolk\">".$sco['titre_mod'][$i]."</B><FONT SIZE=1><I>($mess_label)</I></FONT></acronym></TD>";
                 $affiche_index .= "<TD align='left' valign='top'><Div id='sequence'><A HREF=\"javascript: void(0);\" ".bulle(NewHtmlentities($sco["desc_mod"][$i]),$mess_admin_comment,"RIGHT","ABOVE",270).
                                    " &nbsp;".html_entity_decode($sco['consigne_mod'][$i],ENT_QUOTES,'iso-8859-1')."</A></DIV></TD>";
                 if ($launch != "")
                    $affiche_index .= "<TD align='center' valign='top' >";
                 $desc1 = '';
                 if ($launch != "" && !isset($noLink)  && ((($proposable == 1 || $visible == 1) && $utilisateur > 0) || ($proposable == 1 && $visible == 1 && !$utilisateur)))
                 {
                    $lien_index = urlencode("index_contenu$supplement.php?id_parc=$id_parc&id_seq=$seq&cours=$cours&ecran_new=normal&actuelle=".$sco['id_mod'][$i]."&scormid=".$sco['id_mod'][$i]);
                    $affiche_index .= "<Div id='sequence'><A HREF=\"javascript:void(0);\"".
                        " onClick=\"window.open('scorm/lancer_sco$supplement.php?id_parc=$id_parc&id_seq=$seq&cours=$cours&lien=$launch&lien_index=$lien_index&actuelle=".$sco['id_mod'][$i]."&scormid=".$sco['id_mod'][$i]."&$suitable',".
                        "'null','status=yes, directories=no,copyhistory=0, titlebar=no, toolbar=no, location=no, menubar=no, scrollbars=auto, resizable=yes');\"".
                        "onmouseover=\"img$i.src='images/ecran-annonce/icoGgob.gif';return true;\" onmouseout=\"img$i.src='images/ecran-annonce/icoGgo.gif'\">".
                        "<IMG NAME=\"img$i\" SRC=\"images/ecran-annonce/icoGgo.gif\" BORDER='0' ".
                        "onLoad=\"tempImg=new Image(0,0); tempImg.src='images/ecran-annonce/icoGgob.gif'\"></A></DIV>";
                 }
                 elseif (isset($noLink) && $noLink == 1)
                    $affiche_index .= "<IMG SRC='$adresse_http/images/ecran-annonce/icoGgoinactif.gif' ALT=\"$msq_act_prereq : $titre_prereq\">";
                 elseif ($launch != "" && ($visible == 0 || $proposable == 0) && !$utilisateur)
                    $affiche_index .= "<IMG SRC='$adresse_http/images/ecran-annonce/icoGgoinactif.gif'>";
                 if ($launch != ""){
                        $affiche_index .= "</TD><TD align='left' valign='top' >";
                        $affiche_index .= "$etat_img</TD>";
                        $affiche_index .= "<TD align='left' valign='top' >";
                        $lien = "details_sco.php?utilisateur=$utilisateur&numero_groupe=$numero_groupe&scormid=".$sco['id_mod'][$i]."&keepThis=true&TB_iframe=true&height=400&width=400";
                        $lien = urlencode($lien);
                        $affiche_index .= "<Div id='sequence'><A HREF=\"trace.php?link=$lien\" class='thickbox'>".
                                          "<FONT SIZE='1'>$mess_detsco</FONT></A></DIV>";
                        $affiche_index .= "</TD>";
                 }
                 if ($etat != "NOT ATTEMPTED" && ($typ_user == "ADMINISTRATEUR" || $act_form == $id_user) &&  $sco['type_mod'][$i] != 'LABEL')
                 {
                        $affiche_index .= "<TD valign='top'>";
                       $affiche_index .= '<div id="ReinitSco">'.
                                   '<a href="javascript:void(0);" '.
                                   'title="Réinitialiser cette activité." '.
                                   'onClick="javascript:$(document).ready(function(){
                                        $.ajax({type: \'GET\',
                                              url: \'modif_parc.php\',
                                              data: \'ReinitSco=1&scormid='.$sco['id_mod'][$i].'&utilisateur='.
                                                    $utilisateur.'&numero_groupe='.$numero_groupe.'\',
                                              beforeSend:function()
                                              {
                                                 $(\'#affiche\').addClass(\'Status\');
                                                 $(\'#affiche\').append(\'Opération en cours....\');
                                              },
                                              success: function(msg){
                                                   $(\'#affiche\').empty();
                                                   $(\'#mien\').html(\'Vous venez de réinitialiser ce SCO\');
                                                   //$(\'#mien\').text(msg.value);
                                                   setTimeout(function() {$(\'#mien\').empty();document.location.reload();},2000);
                                              }
                                        });
                                    });" >Réinit</a></div></div>';

                        $affiche_index .= "</TD>";

                 }
                 else
                     $affiche_index .= "<TD>&nbsp;</TD>";

                 $affiche_index .= "</TR>";
             }
             echo $affiche_index;
             //echo "</TABLE></TD></TR>";//</TABLE></TD></TR></TABLE>
             //exit();
         }
         else
         {
         /// Scorm modifié au -dela non
         echo "<TABLE width='100%' bgColor='#298CA0' cellspacing='2' cellpadding='0'><TR><TD>";
         echo "<TABLE width='100%'  bgcolor='#FFFFFF' cellspacing='1' cellpadding='3'>";
         echo "<TR height='34'>";
         echo "<TD align='left' background=\"images/fond_titre_table.jpg\"><FONT COLOR=white size='2'><b>$msq_activite : ".strtolower($mess_fav_tit)."</b></FONT></TD>";
         echo "<TD align='left' background=\"images/fond_titre_table.jpg\"><IMG SRC=\"images/i-lav.gif\" border='0' width='15' height='15'></TD>";
         echo "<TD align='left' background=\"images/fond_titre_table.jpg\"><FONT COLOR=white size='2'><b>$msq_aff_ordre</b></FONT></TD>";
         echo "<TD  align='left' background=\"images/fond_titre_table.jpg\"><FONT COLOR=white size='2'><b>$msq_consigne_act_form</b></FONT></TD>";
         if ($utilisateur > 0)
            echo "<TD align='left' background=\"images/fond_titre_table.jpg\"><FONT COLOR=white size='2'><b>$msq_duree_seq</b></FONT></TD>";
         if (!$utilisateur || (isset($utilisateur) && $utilisateur == ''))
            echo "<TD align='left' background=\"images/fond_titre_table.jpg\"><FONT COLOR=white size='2'><b>$mess_demarrer</b></FONT></TD>";
         else
            echo "<TD align='left' background=\"images/fond_titre_table.jpg\"><FONT COLOR=white size='2'><b>$mess_menu_consult_favori</b></FONT></TD>";
         echo "<TD align='left' background=\"images/fond_titre_table.jpg\"><FONT COLOR=white size='2'><b>$msq_etat</b></FONT></TD>";
         echo "<TD align='left' background=\"images/fond_titre_table.jpg\"><FONT COLOR=white size='2'><b>$msq_tit_label</b></FONT></TD>";
         if ($actform == $id_user || $prescripteur == $id_user)
           echo "<TD align='left' background=\"images/fond_titre_table.jpg\"><IMG SRC='images/modules/tut_form/trombpoub.gif' border='0'></TD>";
         //On selectionne le type d'utilisateur (uniquement si ce n'est pas un apprenant ki vient consulter ses activites)car seul l'administrateur et l'auteur de l'activite ont le droit de modifier ou supprimer celle-ci
         $bgcolorC = '#F2EBDC';
         $ii = 0;
         while ($ii != $Nb_act_seq)
         {
               //session_unregister('id_act');
               $id_suivi = mysql_result ($act_query,$ii,"suivi_cdn");
               $suivi_fichier = mysql_result ($act_query,$ii,"suivi_fichier_lb");
               $suivi_seqno = mysql_result ($act_query,$ii,"suivi_seqajout_no");
               $id = mysql_result ($act_query,$ii,"act_cdn");
               $nom = mysql_result ($act_query,$ii,"act_nom_lb");
               $ressnorok = mysql_result ($act_query,$ii,"act_ress_on");
               $ordre[$ii] = mysql_result ($act_query,$ii,"act_ordre_nb");
               $consigne = DelAmp(mysql_result ($act_query,$ii,"act_consigne_cmt"));
               $commentaire = DelAmp(mysql_result ($act_query,$ii,"act_commentaire_cmt"));
               $pass_mult = mysql_result ($act_query,$ii,"act_passagemult_on");
               $acquit = mysql_result ($act_query,$ii,"act_acquittement_lb");
               $devoir = mysql_result ($act_query,$ii,"act_devoirarendre_on");
               $notation = mysql_result ($act_query,$ii,"act_notation_on");
               $flag = mysql_result ($act_query,$ii,"act_flag_on");
           //dey Dfoad
              $req_media = mysql_result(mysql_query("select count(*) from activite_media where actmedia_act_no = $id"),0);
              $media_act = "";
              if ($req_media > 0)
              {
                 $media_act = GetDataField ($connect,"select ress_url_lb from ressource_new,activite_media where
                                            ress_cdn = actmedia_ress_no and actmedia_act_no =$id ","ress_url_lb");
              }
               if ($commentaire != "")
                  $commentaire1 = $commentaire;//addcslashes($commentaire,"\0..\47!@\176..\255");
               else
                 $commentaire1 = $mess_no_comment;
               if (!$consigne)
                 $consigne = $msq_aucune;
               //Séléction ressource
               $id_ress = mysql_result ($act_query,$ii,"act_ress_no");
               if ($id_ress > 0)
               {
                  $reqRess = mysql_query ("select * from ressource_new where ress_cdn = $id_ress");
                  $itemRess = mysql_fetch_object($reqRess);
                  $lien_ressource = $itemRess->ress_url_lb;
                  if (strstr($lien_ressource,".flv") ||
                     strstr($lien_ressource,".swf") ||
                     strstr($lien_ressource,".mp3"))
                       $lien_ressource = "lanceMedia.php?id_act=$id";
                  $lien_ress = str_replace("&","%",$lien_ressource);

                  $lr = 0;
                  if (strstr($lien_ress,"qcm.php"))
                    $lr = 1;
                  $non_affic_ress_lien = 0;
               }
               if ($id_ress == 0)
               {
                   $non_affic_ress_lien = 1;
                   $ressource = $msq_aucune;
               }
               else
               {
                  $ressource =  $itemRess->ress_titre;
                //Dans le cas ou la ressource a ete supprimee
                   if ($ressource == '')
                   {
                       $ressource = "<B>$msq_ress_sup</B>";
                       $non_affic_ress_lien = 1;
                   }
                   else
                       $typ_ress = strtoupper ($itemRess->ress_support);
                } //fin else

               //Pour meme raison que typ_user, on selectionne auteur
               $id_auteur = $suiviSeqItem->seq_auteur_no;
               if ($utilisateur > 0)
                 $actform = $prescItem->presc_formateur_no;

               //Si c'est l'apprenant qui consulte les seq et activites a faire
               //On doit savoir s'il doit effectuer activites ds l'ordre ou non pour activer ou non le lien
               if (!$utilisateur)
               {
                  //On connait l'ordre de l'activite courante  ($ordre)
                   if ($ordre[$ii] > 1 && $ii > 0 && $suivi_seqno == 0)
                   {
                      //On recupere l'etat l'activite precedente
                       $ordre_prec = $ordre[$ii-1];
                       $act_prec =   GetDataField ($connect,"select act_cdn from activite where act_ordre_nb = $ordre_prec and act_seq_no = $seq","act_cdn");
                       $etat_act =GetDataField ($connect,"select suivi_etat_lb from suivi1$Ext where
                                                           suivi_act_no = $act_prec and
                                                           suivi_utilisateur_no = $id_user and
                                                           suivi_grp_no = $numero_groupe","suivi_etat_lb");
                    }
               } //fin if ($apprenant == 1)
               $duree = mysql_result ($act_query,$ii,"act_duree_nb");
               $duree = duree_calc($duree);
               echo couleur_tr($ii+1,'');
               //On passe certaines infos par URL, cela evite de refaire une requete
               //on affiche le lien pour un apprenant que si activite precedente terminee
               // GESTION DE L'ORDRE ET DE L'ETAT DES ACTIVITES POUR UN APPRENANT OU SON FORMATEUR
               if ($utilisateur > 0)
               {
                     $etat_act = mysql_result ($act_query,$ii,"suivi_etat_lb");
                     $suivi_comment = mysql_result ($act_query,$ii,"suivi_commentaire_cmt");
               }
               if (!$utilisateur)
               {
                     $actform = $prescItem->presc_formateur_no;
                     $suivi_comment= mysql_result ($act_query,$ii,"suivi_commentaire_cmt");
                     $etat_act = mysql_result ($act_query,$ii,"suivi_etat_lb");
               }
               if ($id_ress != 0)
               {
                   if ($etat_act == "TERMINE")
                      $fin_ordre_non++;
                   $lien_ressource =  $itemRess->ress_url_lb;
                   if  (strstr($lien_ressource,".flv") ||
                        strstr($lien_ressource,".swf") ||
                        strstr($lien_ressource,".mp3"))
                          $lien_ressource = "lanceMedia.php?id_act=$id";
                   $lien_ress_tut ="";
                   if ($lien_ressource == "")
                      $lien_ressource = "ress_virtuel.php?id_ress=$id_ress&id_act=$id";
                   $lien_ress_tut = $lien_ressource;
                   if ((($etat_act == "TERMINE" &&
                        $notation == "NON") ||
                        $etat_act == "A FAIRE" ||
                        $etat_act == "EN COURS") &&
                        $ordre[$ii] >0  &&
                        ($typ_user == "APPRENANT"))
                   {
                      $lien_retour = "http://".$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
                      $lien_ret=substr($lien_retour,1);
                      if (!$utilisateur)
                      {
                        $req = mysql_query("select * from traceur where traceur_util_no = $id_user AND traceur_grp_no = $numero_groupe");
                        $nbr_trac = mysql_num_rows($req);
                        if ($nbr_trac == 1)
                        {
                           $lien_details = mysql_result($req,0,"traceur_der_details");
                           $lien_det = $lien_details."&essai=essai";
                           $lien_rt=urlencode($lien_det);
                        }
                      }
                      $marqueur[$ii] = 1;
                      $iii = $ii-1;
                      $lien_ress = str_replace("&","|",$lien_ressource);
                      $lr = 0;
                      if (strstr($lien_ress,"qcm.php"))
                      {
                         $lien_ress .= "|id_act=$id";
                         $lr = 1;
                      }
                      if ((strstr($lien_ress,"ParWeb")) ||
                         (strstr($lien_ress,"parweb")) ||
                         (strstr($lien_ress,"Legweb")) ||
                         (strstr($lien_ress,"legweb")) ||
                         (strstr($lien_ress,"Tatweb"))  ||
                         (strstr($lien_ress,"tatweb")) ||
                         (strstr($lien_ress,"Qcmweb")) ||
                         (strstr($lien_ress,"qcmweb")) ||
                         (strstr($lien_ress,"Elaweb")) ||
                         (strstr($lien_ress,"elaweb")))
                      {
                         $lien_ress .= "%nom=$nom_user%prenom=$prenom_user%email=$email_user";
                      }
                      if (EduNet($lien_ress) == TRUE)
                            $lien_ress = str_replace("acces.html","direct.html",$lien_ress)."|url=$url_ress|auth_cdn=$auth_cdn";
                      if ($etat_act != "TERMINE" && $ordre_act == "OUI")
                            $pointeur++;
                      if ($ii == 0 && !$utilisateur)
                            $pointer = 1;
                      if ($Nb_act_seq == $ii+1 && $ordre_act == "OUI")
                            $fin_seq=1;
                      if ($Nb_act_seq == $ii+1 && $ordre_act == "NON" && $fin_ordre_non == $Nb_act_seq-1)
                            $fin_seq = 1;
                    }
                    //fin if (($etat_act == "A FAIRE" && $ordre >0 && ($typ_user == "NULL" || $id_user == $actform)))
                    $ress_type = $itemRess->ress_type;
                   }
                   else
                      $ressource="<Small>$msq_aucune</SMALL>";
                   if ($commentaire != "")
                      $envoi_comment = "<A href=\"javascript:void(0);\" ".bulle(htmlentities($commentaire1,ENT_QUOTES,'ISO-8859-1'),$mess_admin_comment,"RIGHT","ABOVE",270)."$nom</A>";
                   else
                      $envoi_comment = "<b>$nom</b>";
                     //fin if ($id_ress != 0)
                   $le_titre = "<TABLE>".
                        "<TR><TD width=5></TD><TD>$msq_aff_pass_mult <B>$pass_mult</B></TD></TR>".
                        "<TR><TD width=5></TD><TD>$msq_aff_acquit <B>$acquit</B></TD></TR>".
                        "<TR><TD width=5></TD><TD>$msq_aff_dev_rend <B>$devoir</B></TD></TR>".
                        "<TR><TD width=5></TD><TD>$msq_act_evalue <B>$notation</B></TD></TR>".
                        "<TR><TD width=5></TD><TD>$mess_gp_durre_tot_form : <B>$duree</B></TD></TR></TABLE>";
                   echo "<TD  align='left' valign='top'>$envoi_comment</TD>";
                   echo "<TD align='left' valign='top' >";
                   echo "<A HREF=\"javascript:void(0)\" ".bulle($le_titre,"","RIGHT","ABOVE",300).
                        "<IMG SRC=\"images/i-lav.gif\" border='0' width='15' height='15' style='cursor:help;'></a></TD>";
                   if ($suivi_seqno > 0)
                        echo "<TD align='left' valign='top'><IMG SRC=\"images/plus_lav.gif\" height=\"12\" width=\"12\" BORDER=0 " .
                        bulle($msgActPlus,"","RIGHT","ABOVE",170)."</TD>";
                   else
                        echo "<TD align='left' valign='top' >".$ordre[$ii]."</TD>";
                   //dey Dfoad
                   $req_typdev = mysql_num_rows(mysql_query("select * from activite_devoir where actdev_act_no = $id"));
                   $dev_act = "";
                   if ($req_typdev > 0)
                       $dev_act = GetDataField ($connect,"select actdev_dev_lb from activite_devoir where
                                            actdev_act_no = $id ","actdev_dev_lb");
                   else
                       $dev_act = "Pas de devoir";
                   $class_act =  GetDataField ($connect,"select actdevico_style_lb from actdev_icone where
                                            actdevico_type_lb = \"$dev_act\" ","actdevico_style_lb");
                   echo "<TD align='left' valign='top' ><div $class_act> ".html_entity_decode(html_entity_decode($consigne,ENT_QUOTES,'ISO-8859-1'),ENT_QUOTES,'ISO-8859-1')."</div>";
                   if($media_act != "")
                   {
                       $actit = $id;
                       $largeur = "220";
                       $hauteur = "140";
                       echo "<br />&nbsp;<div id='insertMedia' style='text-align:center;'>";
                            include ("media.php");
                       echo "</div>";
                  }
                  echo "</td>";
                  if (isset($utilisateur) && $utilisateur > 0)
                  {
                      echo "<TD align='left' valign='top' nowrap >$duree</TD>";
                      $actsuivi = mysql_result ($act_query,$ii,"suivi_etat_lb");
                      $suivi_fichier = mysql_result ($act_query,$ii,"suivi_fichier_lb");
                      if ($dev_act == 'xApi TinCan')
                      {
                         $lien_ress_tut =  $lien_ress.TinCanTeach ('formateur|'.$id_parc.'|'.$seq.'|'.$id.'|'.$numero_groupe,$lien_ress,$commentaire);
                      }
                      elseif (EduNet($lien_ress) == TRUE)
                         $lien_ress_tut =  str_replace("acces.html","direct.html",$lien_ress)."&url=$url_ress&auth_cdn=$auth_cdn";
                      else
                      {
                        $lien_ress_tut = urlencode(serveur_externe($lien_ress_tut));
                      }
                      if ($id_ress > 0)
                      {
                          $alerte_forum = 0;
                          if (strstr($lien_ress,"forum/read.php?f="))
                          {
                              $new_lien = str_replace("$adresse_http/forum/read.php?","",$lien_ress);
                              list($uno,$duo,$trio) = explode("&",$new_lien);
                              list($titre_forum,$id_forum) = explode("=",$uno);
                              $comment_forum = $itemForum->name;
                              $id_grp = GetDataField ($connect,"select grp_cdn from groupe where grp_nom_lb =\"$comment_forum\"","grp_cdn");
                          }
                          if (isset($id_grp) && $id_grp != $numero_groupe && strstr($lien_ress,"forum/read.php?f="))
                          {
                             $alerte_forum = 1;
                             echo "<TD align='center' valign='top' ><IMG SRC=\"images/ecran-annonce/icoGgoinactif.gif\" border='0' ALT= \"$mess_inactif\"></TD>";//fin if (($pointeur == 1 && $ordre_act == "OUI") ||($ordre_act == "NON"))
                          }
                          else
                          {
                              echo "<TD align='center' valign='top' ><A HREF=\"javascript:void(0)\" title=\"$mess_go_act\" ".
                                   "onClick=\"window.open('".urldecode($lien_ress_tut)."',null,'status=no, directories=no,copyhistory=0,".
                                   "titlebar=no, toolbar=yes, location=no, menubar=yes, scrollbars=yes, resizable=yes');\"".
                                   "onmouseover=\"img$ii.src='images/ecran-annonce/icoGgob.gif';return true;\" onmouseout=\"img$ii.src='images/ecran-annonce/icoGgo.gif'\">";
                              echo "<IMG NAME=\"img$ii\" SRC=\"images/ecran-annonce/icoGgo.gif\" BORDER='0' onLoad=\"tempImg=new Image(0,0); tempImg.src='images/ecran-annonce/icoGgob.gif'\"></A></TD>";
                          }
                      }
                      else
                      {
                         echo "<TD align='center' valign='top'><IMG SRC=\"images/ecran-annonce/icoGgoinactif.gif\" border='0' ALT= \"$mess_inactif\"></TD>";
                         }
                   }
                   else
                   {
                     if ($id_ress != 0 && ($prq == 'OK' || !$prq || $nb_verif_req == 0) && $visible == 1 && (($depasse !=1 && $accord == 1) ||
                        (($depasse == 1 || $accord == 1) && (($pass_mult == 'OUI' && $etat_act == 'TERMINE') ||
                        ($pass_mult == 'NON' && $acquit == 'FORMATEUR_REFERENT' &&  $etat_act != 'A FAIRE')))) &&
                        (($etat_act == 'TERMINE' && ($notation == 'NON' ||
                        ($notation == 'OUI' && $pass_mult == 'OUI')) && $acquit != 'AUTO') ||
                        ($acquit == 'FORMATEUR_REFERENT' &&  $etat_act != 'A FAIRE') ||
                        ($pointeur == 1 && $ordre_act == 'OUI' && ($etat_act == 'A FAIRE' ||
                        ($etat_act == 'EN COURS' && $pass_mult == 'OUI'))) || ($login == 'Test') ||
                        ($ordre_act == 'NON' && ($etat_act == 'A FAIRE' ||
                        ($etat_act == 'EN COURS'  && $pass_mult == 'OUI'))) ||
                        ($marqueur[$ii] != 1 && $etat_act == 'A FAIRE') || $dev_act == 'xApi TinCan'))
                     {
                          $lien="suivi.php?verif_lien_ress=1&fin_parc=$fin_parc&pointer=$pointer&id_seq=$seq&id_parc=$id_parc&fin_seq=$fin_seq&id_act=$id&numero_groupe=$numero_groupe&lien_ress=$lien_ress";
                          $alerte_forum = 0;
                          if (strstr($lien_ress,"forum/read.php?f="))
                          {
                              $new_lien = str_replace("$adresse_http/forum/read.php?","",$lien_ress);
                              list($uno,$duo,$trio) = explode("&",$new_lien);
                              list($titre_forum,$id_forum) = explode("=",$uno);
                              $comment_forum = $itemForum->name;
                              $id_grp = GetDataField ($connect,"select grp_cdn from groupe where grp_nom_lb =\"$comment_forum\"","grp_cdn");
                          }
                          if (isset($id_grp) && $id_grp != $numero_groupe && strstr($lien_ress,"forum/read.php?f="))
                          {
                             $alerte_forum = 1;
                             echo "<TD align='center' valign='top' ><IMG SRC=\"images/ecran-annonce/icoGgoinactif.gif\" border='0' ALT= \"$mess_inactif\"></TD>";//fin if (($pointeur == 1 && $ordre_act == "OUI") ||($ordre_act == "NON"))
                          }
                          elseif ($dev_act == 'xApi TinCan')
                          {
                                 $course = base64url_encode('apprenant|'.$id_parc.'|'.$seq.'|'.$id.'|'.$numero_groupe);
                                 $Register = base64url_encode($_COOKIE["monID"].'_'.$_COOKIE["monLogin"].'|'.str_replace('http://','',str_replace('.educagri.fr','',$adresse_http)));
                                 $lien = $lien_ress.TinCanTeach ('apprenant|'.$id_parc.'|'.$seq.'|'.$id.'|'.$numero_groupe,$lien_ress,$commentaire);
                                 echo "<TD align='center' valign='top' ><A HREF=\"javascript:void(0)\" title=\"$mess_demarrer\" ".
                                      "onClick=\"javascript:$.ajax({
                                                type: 'GET',
                                                url: 'admin/xApi.php',
                                                data: 'course=$course&registration=$Register',
                                                success: function(msg)
                                                {
                                                    \$('#xApi_$id').text(msg);
                                                    setTimeout(function() {document.location.reload();},5000);
                                                }
                                      });
                                      window.open('$lien',null,'status=no, directories=no,copyhistory=0,".
                                      "titlebar=no, toolbar=yes, location=no, menubar=yes, scrollbars=yes, resizable=yes');\">";
                                 echo "<IMG SRC=\"images/ecran-annonce/icoGgo.gif\" BORDER='0'\"></A></TD>";
                          }
                          elseif (EduNet($lien_ress) == TRUE ||
                                 strstr(strtolower($lien_ress),"ressources.eduter-cnpr.fr") ||
                                 strstr(strtolower($lien_ress),".doc") ||
                                 strstr(strtolower($lien_ress),".xls") ||
                                 strstr(strtolower($lien_ress),".xlt") ||
                                 strstr(strtolower($lien_ress),".ppt") ||
                                 strstr(strtolower($lien_ress),".pps") ||
                                 strstr(strtolower($lien_ress),".pdf") ||
                                 strstr(strtolower($lien),".txt") ||
                                 strstr(strtolower($lien),".rtf") ||
                                 strstr(strtolower($lien_ress),".gouv.fr") ||
                                 strstr(strtolower($lien),"google.com") ||
                                 strstr(strtolower($lien),"youtu"))
                          {
                            echo "<TD align='center' valign='top' ><A HREF=\"javascript:void(0)\" title=\"$mess_demarrer\" ".
                                 "onClick=\"window.open('$lien',null,'status=no, directories=no,copyhistory=0,".
                                 "titlebar=no, toolbar=yes, location=no, menubar=yes, scrollbars=yes, resizable=yes');\"".
                                 "onmouseover=\"img$ii.src='images/ecran-annonce/icoGgob.gif';return true;\" onmouseout=\"img$ii.src='images/ecran-annonce/icoGgo.gif'\">";
                            echo "<IMG NAME=\"img$ii\" SRC=\"images/ecran-annonce/icoGgo.gif\" BORDER='0' onLoad=\"tempImg=new Image(0,0); tempImg.src='images/ecran-annonce/icoGgob.gif'\"></A></TD>";
                          }
                          else
                          {
                            $lien = urlencode($lien);
                            echo "<TD align='center' valign='top' ><A href=\"javascript:void(0)\" title=\"$mess_demarrer\" ".
                            "onClick=\"window.open('trace.php?link=$lien&activ=1','','resizable=yes,status=no');\" ".
                            "onmouseover=\"img$ii.src='images/ecran-annonce/icoGgob.gif';return true;\" onmouseout=\"img$ii.src='images/ecran-annonce/icoGgo.gif'\">";
                            echo "<IMG NAME=\"img$ii\" SRC=\"images/ecran-annonce/icoGgo.gif\" BORDER='0' onLoad=\"tempImg=new Image(0,0); tempImg.src='images/ecran-annonce/icoGgob.gif'\"></A></TD>";
                          }
                       }
                       else
                          echo "<TD align='center' valign='top' ><IMG SRC=\"images/ecran-annonce/icoGgoinactif.gif\" border='0' ALT= \"$mess_inactif\"></TD>";//fin if (($pointeur == 1 && $ordre_act == "OUI") ||($ordre_act == "NON"))
                       $actform = $prescItem->presc_formateur_no;
                       $prescripteur = $prescItem->presc_prescripteur_no;
                       $actsuivi = mysql_result ($act_query,$ii,"suivi_etat_lb");
                       $suivi_fichier = mysql_result ($act_query,$ii,"suivi_fichier_lb");
                   }
                   if ($suivi_fichier != "" && strstr($suivi_fichier,"qf_"))
                       $tip_fic = substr($suivi_fichier, 0, 3);
                   $majuscule = NomUser($actform);
                   if ($actsuivi == "TERMINE")
                     $actsuiv = $mess_fait;
                   elseif ($actsuivi == "A FAIRE" && $flag == 1 && $alerte_forum == 0 && $id_ress > 0)
                     $actsuiv = $mess_lanc_afaire;
                   elseif ($actsuivi == "EN COURS")
                     $actsuiv = $mess_lanc_encours;
                   elseif ($actsuivi == "ATTENTE")
                   {
                     $actsuiv = $mess_lanc_attente;//formateur sur etat et apprenant sur acq/eval
                     $actsuivalid = $mess_fait;//apprenant
                   }
                   elseif ($actsuivi == "PRESENTIEL")
                     $actsuiv = $mess_lanc_afaire;//elseif ($actsuivi == "A FAIRE" && $id_ress == 0)  $actsuiv = $mess_lanc_attente;
                   elseif ($actsuivi == "A FAIRE" && ($flag == 0 || $alerte_forum == 1) && $id_ress == 0)
                     $actsuiv = $mess_trvx;
                   if ($actsuivi == 'TERMINE' && $utilisateur > 0 && ($id_user == $actform || $id_user == $prescripteur || $id_user == $inscripteur))
                   {
                     $lien = "suivi.php?prov=vm&numero_groupe=$numero_groupe&nom=$id_nom&prenom=$id_prenom&fin_parc=$fin_parc&utilisateur=$utilisateur&id_parc=$id_parc&id_act=$id&id_seq=$seq&suivi_etat_seq=$suivi_etat_seq&modif_etat=1&sequ=$i&p=$p&deroule[$i]=$deroule[$i]&deroule_parc[$p]=$deroule_parc[$p]&parc=$id_parc&visible=$visible&accord=$accord&prq=$prq&depasse=$depasse&proposable=$proposable";
                     $lien = urlencode($lien);
                     echo "<td align='left' valign='top' ><DIV id='sequence'><A href=\"trace.php?link=$lien\" ".bulle($mess_gp_label_refaire,$mess_gp_pup_act_titre,"LEFT","ABOVE",300)."$actsuiv</A></DIV></td>";
                   }
                   elseif ($actsuivi == 'ATTENTE' && !$utilisateur)
                     echo "<td align='left' valign='top'  nowrap>$actsuivalid</td>";
                   elseif ($actsuivi == "A FAIRE" && $suivi_comment == "Activité repassée")
                     echo "<td align='left' valign='top'  nowrap>$mess_refaire</td>";
                   else
                     echo "<td align='left' valign='top'  nowrap><div id='etatAct_$id'>$actsuiv</div></td>";
                 if ($ii == 0 && !$utilisateur)
                    $pointer = 1;
                 if (($Nb_act_seq == $ii+1 && $ordre_act == "OUI") || ($Nb_act_seq == $ii+1 && $ordre_act == "NON" && $fin_ordre_non == $Nb_act_seq-1))
                    $fin_seq = 1;
                 $actvalid = mysql_result ($act_query,$ii,"act_acquittement_lb");
                 if ($utilisateur > 0)
                    $actnote = mysql_result ($act_query,$ii,"suivi_note_nb1");
                 $ress_type = $itemRess->ress_type;

                 if (($id_user == $actform &&
                        (($actsuivi == 'PRESENTIEL' && ($actvalid == 'FORMATEUR_REFERENT' || $actvalid == 'APPRENANT')) ||
                        (($actsuivi == 'A FAIRE' || $suivi_comment == 'Activité repassée') && $actsuivi != 'TERMINE' && $id_ress == 0))) ||
                    ($actsuivi == 'PRESENTIEL' && $actvalid == 'APPRENANT' && $devoir == 'NON' && $notation == 'NON' && $typ_user =='APPRENANT') ||
                    ($id_user == $actform  &&
                        ($actsuivi == 'ATTENTE' ||
                        ($actsuivi == 'EN COURS' && (($actvalid == 'APPRENANT'|| $actvalid == 'FORMATEUR_REFERENT') && $notation == 'NON') ||
                                                     ($actvalid == 'FORMATEUR_REFERENT' && $notation == 'OUI' && $actsuivi != 'TERMINE' && $actsuivi != 'A FAIRE' && $devoir == 'NON')))) ||
                    ($typ_user == 'APPRENANT' && $dev_act != 'xApi TinCan' && ($actsuivi == 'EN COURS' || ($actsuivi == 'PRESENTIEL' &&  $devoir == 'OUI')))  ||
                    ($utilisateur > 0 && $actsuivi == 'EN COURS' && $devoir =='NON' && $notation == 'NON' && $actform == $id_user) ||
                    ($pass_mult == 'OUI' && $actsuivi == 'EN COURS' && $dev_act != 'xApi TinCan' && $pointeur == 1))
                    {
                     $lien="suivi.php?prov=vm&numero_groupe=$numero_groupe&actform=$actform&utilisateur=$utilisateur&id_parc=$id_parc&id_act=$id&ress_type=$ress_type&id_seq=$seq&pointer=$pointer&validation=1&fin_seq=$fin_seq&sequ=$i&p=$p&deroule[$i]=$deroule[$i]&deroule_parc[$p]=$deroule_parc[$p]&parc=$id_parc&visible=$visible&accord=$accord&prq=$prq&depasse=$depasse&proposable=$proposable";
                     if ($utilisateur > 0 && $actnote == "" && (($suivi_fichier != '' && $notation == "OUI") || ($suivi_fichier == '' && $notation == "OUI" && $devoir == "NON" && $actvalid != 'RESSOURCE')))
                        $lien .="&noter=1";
                     elseif (($utilisateur > 0 &&  $actnote != "" && $notation == "OUI" && $devoir == "OUI") ||
                            (!$utilisateur && $actnote == "" && $devoir == "OUI") ||
                            ($actsuivi == "PRESENTIEL" && $notation == "NON" && $devoir == "OUI"))
                        $lien .="&envoyer=1";
                     $lien = urlencode($lien);
                     if($actsuivi == 'EN COURS' && $actvalid == 'FORMATEUR_REFERENT' && $notation == "NON" && $devoir == "NON" && !$utilisateur)
                        echo "<td  align='left' valign='top'>&nbsp;</TD>";
                     else
                     {
                        if (($devoir == "OUI" && $utilisateur > 0 && $suivi_fichier == '') || ($id_ress == 0 && $actsuivi == 'A FAIRE'))
                            echo "<td  align='left' valign='top'>";
                        else
                            echo "<td  align='left' valign='top'><input type=checkbox onMouseOver=\"overlib('<TABLE cellspacing=2 cellpadding=3><TR><TD>$msq_cliq";
                        if (!$utilisateur)
                        {
                            if ($devoir == "OUI")
                               echo " ".strtolower($msq_envoi_dev);
                            else
                               echo "$msq_clore_act1  <B>$majuscule</B> $msq_clore_act2";
                            $test_fin = $ii+1;
                            if ( $ordre_act == "OUI" && $test_fin < $Nb_act_seq)
                               echo $mess_lib_act;
                        }
                        elseif ($notation == "OUI" && $devoir == "OUI" && $actnote != "" && $suivi_fichier != '')
                               echo" $msq_renvoi_dev <FONT COLOR=marroon> $id_prenom $id_nom</FONT><BR><B>$trvx_avert</B>";
                        elseif ($notation == "OUI" && $devoir == "OUI" && ($actnote == 0 || $actnote == '') && $suivi_fichier != '')
                               echo " $msq_don_note";
                        elseif ($notation == "OUI" && $devoir == "NON" && $actnote == '' && $actvalid != 'RESSOURCE')
                               echo " $msq_don_note1";
                        elseif ($notation == "NON" && $devoir == "OUI" && $utilisateur > 0 && $suivi_fichier != '')
                               echo "";
                        elseif ($notation == "OUI" && $devoir == "OUI" && $utilisateur > 0 && $suivi_fichier != '')
                               echo "";
                        elseif (($devoir == "OUI" && $utilisateur > 0 && $suivi_fichier == '') || ($id_ress == 0 && $actsuivi == 'A FAIRE'))
                               echo "";
                        else
                            echo " $msq_clore_act_app <B>$id_prenom $id_nom</B>";
                        if (($devoir == "OUI" && $utilisateur > 0 && $suivi_fichier == '') || ($id_ress == 0 && $actsuivi == 'A FAIRE'))
                           echo "</TD>";
                        else
                            echo "</TD></TR></TABLE>',ol_hpos,LEFT,ABOVE,WIDTH,'300',DELAY,800,CAPTION, '".
                                 "<TABLE cellspacing=2 cellpadding=3><TR><TD><FONT SIZE=2 COLOR=\'#333333\'><B>$msq_tit_label</B></FONT></TD></TR></TABLE>')\" onMouseOut=\"nd()\"".
                                 " onclick=\"javascript:window.location='trace.php?link=$lien';return true;\"></center></TD>";
                    }
                 }
                 else
                 {
                    if (!$utilisateur && $dev_act != 'xApi TinCan' && ($actsuivi == "TERMINE" ||
                        (strstr($lien_ress,'qcm.php?code=') && $suivi_comment == "Activité repassée")))
                    {
                       $note = mysql_result ($act_query,$ii,"suivi_note_nb1");
                       if (is_numeric($note) && $note > -1 && $note < 21)
                         $note .="/20";
                       if (strtolower($note) == "acquis")
                         $note = $mess_suivi_acq;
                       elseif (strtolower($note) == "non acquis")
                         $note = $mess_suivi_noacq;
                       $no_suivi = mysql_result ($act_query,$ii,"suivi_cdn");
                       if ($note != '' && (($notation == 'OUI' && $devoir == 'OUI' && $actvalid == 'FORMATEUR_REFERENT') || $tip_fic != '' || ($notation == 'OUI' && $actvalid == 'RESSOURCE' && $suivi_fichier != '')))
                       {
                         $lien = "bilan.php?bilan_act=1&numero_groupe=$numero_groupe&id_suivi=$no_suivi&id_act=$id&utilisateur=$utilisateur&code_act=oof&id_parc=$id_parc&keepThis=true&TB_iframe=true&height=250&width=400";
                         $lien = urlencode($lien);
                         echo "<TD align='left' valign='top'  nowrap>";
                         echo "<DIV id='sequence'><A HREF = \"trace.php?link=$lien\" title=\"$mess_gp_bilan\" class='thickbox'>$note</A></DIV></TD>";
                       }
                       elseif ($note != "" && $devoir == "NON" && (($notation == "OUI" && $suivi_fichier == '') || ($notation == "NON" && $suivi_fichier != '')))
                         echo "<TD align='left' valign='top'  nowrap>$note</TD>";
                       elseif ($note == "" && $suivi_fichier != '' && $devoir == "OUI")
                       {
                         $lien = $suivi_fichier;
                         $lien = urlencode($lien);
                         echo "<TD align='left' valign='top'  nowrap><DIV id='sequence'><A HREF = \"javascript:void(0);\" onclick=\"open('trace.php?link=$lien','window','scrollbars,resizable=yes,width=700,height=300')\"".
                               bulle($mess_lire_fic,"","CENTER","ABOVE",150)."$mess_lanc_termine</A></DIV></TD>";
                       }
                       else
                         echo "<TD align='left' valign='top' nowrap >$mess_lanc_termine</TD>";
                    }
                    elseif ($dev_act == 'xApi TinCan')
                    {
                         $NumVerb = 'En attente';
                         $LeType = ($_SESSION['typ_user'] == 'APPRENANT') ? 'apprenant' : 'formateur';
                         $course = base64url_encode($LeType.'|'.$id_parc.'|'.$seq.'|'.$id.'|'.$numero_groupe);
                         $lien = "admin/statements.php?course=$course&utilisateur=$utilisateur&registration=$Register&Activity=".urlencode(html_entity_decode($commentaire,ENT_QUOTES,'iso-8859-1'))."&keepThis=true&TB_iframe=true&height=450&width=750";
                         echo "<TD align='left' valign='top' nowrap><div id='statement_$id'>
                               <script language='javascript'>
                                $.ajax({
                                      type: 'GET',
                                      url: 'http://lms.annulab.com/TinCanApi/statements.php',
                                      data: 'verb=1&utilisateur=$num_app&course=$course&registration=$Register&Activity=".urlencode(html_entity_decode($commentaire,ENT_QUOTES,'iso-8859-1'))."',
                                      success: function(msg)
                                      {
                                          if (msg == 'En attente')
                                            $('#tincan_$id').text(msg);
                                          else
                                            $('#xApi_$id').text(msg);
                                          if(msg == 'Finalisé' || msg == 'Acquis' || msg == 'Terminé' || msg == 'Fait' || msg == 'Terminé' || msg == 'Répondu')
                                            $('#etatAct_$id').text(msg);
                                      }
                                });
                               </script></div>";
                          echo "<DIV id='tincan_$id'><A HREF = \"$lien\" ".
                               "title=\"$mess_gp_bilan\" name='".$nom." : historique' class='thickbox'>".
                               "<div id='xApi_$id'>$NumVerb</div></A></DIV></TD>";
                    }
                    elseif (($id_user == $actform || $id_user == $prescripteur || $id_user == $tut || $id_user == $superviseur ||
                              $id_user == $inscripteur || $typ_user == 'ADMINISTRATEUR' || $typ_user == 'TUTEUR')  &&
                              (($actsuivi == 'TERMINE' && $utilisateur > 0) || ($actsuivi == 'A FAIRE' && $suivi_comment == "Activité repassée")))
                    {
                       $note = mysql_result ($act_query,$ii,"suivi_note_nb1");
                       $no_suivi = mysql_result ($act_query,$ii,"suivi_cdn");
                       if (is_numeric($note) && $note > -1 && $note < 21)
                         $note .="/20";
                       if (strtolower($note) == "acquis")
                         $note = $mess_suivi_acq;
                       elseif (strtolower($note) == "non acquis")
                         $note = $mess_suivi_noacq;
                       if ($note != "" &&
                          (($notation == "OUI" && $devoir == "OUI" && $actvalid == "FORMATEUR_REFERENT") || "$tip_fic" != "" ||
                          ($notation == "OUI" && $actvalid == "RESSOURCE" && $suivi_fichier != '') ||
                          (strstr($lien_ress,'qcm.php?code=') && $suivi_comment == "Activité repassée")))
                       {
                         $lien = "bilan.php?bilan_act=1&numero_groupe=$numero_groupe&id_suivi=$no_suivi&id_act=$id&utilisateur=$utilisateur&code_act=oof&id_parc=$id_parc&keepThis=true&TB_iframe=true&height=250&width=400";
                         $lien = urlencode($lien);
                         echo "<TD align='left' valign='top' nowrap><DIV id='sequence'>".
                              "<A HREF = \"trace.php?link=$lien\" title=\"$mess_gp_bilan\" class='thickbox'>$note</A></DIV></TD>";
                       }
                       elseif ($note != "" && $devoir == "NON" && (($notation == "OUI" && $suivi_fichier == '') ||
                               ($notation == "NON" && $suivi_fichier != '')))
                         echo "<TD align='left' valign='top'  nowrap>$note</TD>";
                       elseif ($note == "" && $suivi_fichier != '' && $devoir == "OUI")
                       {
                         $lien = $suivi_fichier;
                         $lien = urlencode($lien);
                         echo "<TD align='left' valign='top'  nowrap><DIV id='sequence'>".
                              "<A HREF = \"javascript:void(0);\" ".
                              "onclick=\"open('trace.php?link=$lien','window','scrollbars,resizable=yes,width=700,height=300')\"".
                              bulle($mess_lire_fic,"",",CENTER","ABOVE",150)."$mess_lanc_termine</A></DIV></TD>";
                       }
                       elseif ($suivi_comment == "Activité repassée" && $actsuivi == 'A FAIRE')
                         echo "<TD align='left' valign='top'  nowrap></TD>";
                       else
                         echo "<TD align='left' valign='top'  nowrap>$mess_lanc_termine</TD>";
                    }
                    elseif ($actvalid == "APPRENANT" && $suivi_fichier != "hp" && ($apprenant == 1 || $utilisateur > 0) && $actsuivi == "TERMINE")
                    {
                       echo "<TD align='middle' nowrap  valign='top'>$msq_valide</TD>";
                    }
                    elseif ($actsuivi == "ATTENTE" && !$utilisateur)
                    {
                       echo "<TD nowrap  align='left' valign='top'>".
                            "<A HREF = \"javascript:void(0);\" ".bulle($mess_mess_attente,"",",LEFT","ABOVE",200)."$actsuiv</A></TD>";
                    }
                    else
                       echo "<TD  align='left' valign='top' ></TD>";
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
                 $Nb_act_suivi = mysql_num_rows ($act_suivi);
                 if ($Nb_act_suivi > 0)
                 {// || $prescripteur == $id_user
                    if (($actform == $id_user) && ($actsuivi == "PRESENTIEL" || ($actsuivi == 'A FAIRE' && $suivi_comment != "Activité repassée")) && $id_classe == 1)
                    {
                      if ($Nb_act_seq > 1)
                         $lien = "details_parc.php?visible=$visible&depasse=$depasse&hgrp=$hgrp&numero_groupe=$numero_groupe&supp=1&utilisateur=$utilisateur&a_faire=1&id=$id&id_seq=$seq&id_parc=$id_parc&deroule[$i]=1&num=$id_suivi&id_ref=$id_ref&seq_ouverte=$seq&parc_ouvert=$id_parc";
                      else
                         $lien = "details_parc.php?visible=$visible&depasse=$depasse&hgrp=$hgrp&numero_groupe=$numero_groupe&supp=1&utilisateur=$utilisateur&a_faire=1&id=$id&id_seq=$seq&id_parc=$id_parc&deroule[$i]=1&num=$id_suivi&id_ref=$id_ref&ouvrir=parcours";
                      $lien = urlencode($lien);
                      echo "<td width='2%' align='middle' valign='top' ><A href=\"javascript:void(0);\" onclick=\"javascript:confm('trace.php?link=$lien');\" title=\"$msq_sup_act\" target='principal'".
                          bulle($msq_sup_act,"","RIGHT","ABOVE",140)."<IMG SRC=\"images/messagerie/icoGpoubel.gif\" height=\"15\" width=\"12\" ALT=\"$msq_sup_act\" BORDER=0></A></td>";
                    }
                    elseif($actform == $id_user && $actsuivi == "ATTENTE" && $devoir == "OUI")
                    {
                       if (strstr($suivi_fichier,"/") || strstr($suivi_fichier,"\\"))
                          $lien = "$monURI/".$suivi_fichier;
                       else
                          $lien = "ressources/".$login_user."_".$utilisateur."/devoirs/".$suivi_fichier;
                       if (strstr(strtolower($lien),".doc") || strstr(strtolower($lien),".xls") || strstr(strtolower($lien),".xlt") || strstr(strtolower($lien),".ppt") || strstr(strtolower($lien),".pps") || strstr(strtolower($lien),".txt") || strstr(strtolower($lien),".gouv.fr") || strstr(strtolower($lien),".rtf"))
                          echo "<TD width='2%' align='middle' valign='top' ><A HREF=\"$lien\" target ='_blank'".
                          bulle($mess_vtr,"","RIGHT","ABOVE",125)."<IMG SRC=\"images/messagerie/icoGtrombon.gif\" width='8' height='22' border=0></A></TD>";
                       elseif (!strstr($suivi_fichier,"qf_"))
                          echo "<TD width='2%' align='middle' valign='top' ><A HREF= \"javascript:void(0);\" ".
                               "onclick= \"javascript:window.open('trace.php?link=$lien','','left=0, top=0,resizable=yes,scrollbars=yes,menubar=yes');\" ".
                               "title=\"$mess_vtr\"><IMG SRC=\"images/messagerie/icoGtrombon.gif\" width='8' height='22' border=0></A></TD>";
                    }
                    elseif (($actform == $id_user || $prescripteur == $id_user) && (($actsuivi != "A FAIRE" && $actsuivi != "PRESENTIEL") || ($actsuivi == 'A FAIRE' && $suivi_comment == "Activité repassée")))
                      echo "<TD width='2%' align='middle' valign='top' >&nbsp;</TD>";
                 }// fin if ($Nb_act_suivi == 0)
                 echo "</TR>";
                $ii++;
               }
               }//fin while ($i != $nb_seq)
               //wiki
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
                  $NoteWk = GetDataField ($connect,"select wknote_note_lb from wikinote where wknote_app_no ='".$oWk->wkapp_cdn."'","wknote_note_lb");
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
                     $listeClan .= 'Travail en commun : pour l\'instant seul';

                  $cols = ($scormOk == 1) ? 1 : 3 ;
                  echo '<tr style="background-color:#D4E7ED;"><td valign="top"><div title="'.
                        $listeClan.'" style="clear:both;float:left;cursor:help;font-weight:bold;">Travail en commun</div>';
                  if ($utilisateur > 0)
                  {
                      if ($scormOk != 1)
                         echo '</td><td valign="top">';
                      else
                         echo '<div  style="float:left;margin-left:20px;">';
                      $lien= "wiki/wiki.php?id_seq=$seq&id_parc=$id_parc&numero_groupe=$numero_groupe&numApp=$num_app&keepThis=true&TB_iframe=true&height=425&width=650";
                      echo "<a href=\"$lien\" class='thickbox' name='Composantes du groupe' ".
                           "title='Groupe de travail (binôme, trinôme ou plus) : ".
                           "liste des autres apprenants ayant un travail en commun sur cette séquence'>".
                           "Groupe</a>";
                      if ($scormOk == 1)
                         echo '</div>';
                  }
                  echo '</td><td colspan="'.$cols.'" align="left">';
                  echo $oWk->wiki_consigne_cmt.'</td>';
                  if ((!$utilisateur && $visible == 1 && $proposable==1 && $autorise == 0 && $depasse == 0) || $utilisateur > 0)
                  {
                     $lien = "wiki/wikiOpen.php?numApp=$id_app&id_seq=$seq&id_parc=$id_parc&id_grp=$numero_groupe&id_clan=".
                             $oWk->wkapp_clan_nb."&id_wk=".$oWk->wkapp_cdn;
                     echo "<TD align='center' valign='top' ><A HREF=\"javascript:void(0)\" title=\"$mess_demarrer\" ".
                          "onClick=\"window.open('$lien',null,'status=no, directories=no,copyhistory=0,".
                          "titlebar=no, toolbar=yes, location=no, menubar=yes, scrollbars=yes, resizable=yes');\"".
                          "onmouseover=\"img$ii.src='images/ecran-annonce/icoGgob.gif';return true;\" ".
                          "onmouseout=\"img$ii.src='images/ecran-annonce/icoGgo.gif'\">";
                     echo "<IMG NAME=\"img$ii\" SRC=\"images/ecran-annonce/icoGgo.gif\" BORDER='0' ".
                          "onLoad=\"tempImg=new Image(0,0); tempImg.src='images/ecran-annonce/icoGgob.gif'\"></A></TD>";
                  }
                  else
                  {
                      echo "<TD align='center' valign='top' >".
                           "<IMG SRC=\"images/ecran-annonce/icoGgoinactif.gif\" border='0' ALT= \"$mess_inactif\"></TD>";
                  }
                  if (!empty($_SESSION['NoteWk']))
                     echo "<td valign='top'>Fait</td>";
                  elseif ($NbBodyWk > 0 || $NbMetaWk > 0)
                     echo "<td valign='top' nowrap>En cours</td>";
                  elseif ($NbBodyWk == 0 && $NbMetaWk == 0)
                     echo "<td valign='top' nowrap>A faire</td>";
                  if (!empty($_SESSION['NoteWk']) && empty($utilisateur))
                  {
                     echo "<td valign='top' align='center'><strong>".$_SESSION['NoteWk']."</strong></td>";
                  }
                  elseif ($actform == $id_user && empty($_SESSION['NoteWk']) && $utilisateur > 0 && ($NbBodyWk > 0 || $NbMetaWk > 0))
                  {
                      $lien= "wiki/wikiNote.php?numApp=$num_app&IdWk=".$oWk->wkapp_cdn.
                             "&keepThis=true&TB_iframe=true&height=260&width=450";
                      echo '<td valign="top" align="left">'.
                           '<a href="'.$lien.'" class="thickbox" name="Noter la participation à ce travail commun" '.
                           'title="Noter la participation à ce travail commun">Noter</a></td>';
                  }
                  elseif (empty($_SESSION['NoteWk']) && $utilisateur > 0 && $NbBodyWk == 0 && $NbMetaWk == 0)
                  {
                      echo '<td valign="top" align="center">&nbsp;</td>';
                  }
                  elseif ($actform == $id_user && !empty($_SESSION['NoteWk']) && $utilisateur > 0 && ($NbBodyWk > 0 || $NbMetaWk > 0))
                  {
                      $lien= "wiki/wikiNote.php?numApp=$num_app&IdWk=".$oWk->wkapp_cdn.
                             "&keepThis=true&TB_iframe=true&height=260&width=450";
                      echo '<td valign="top" align="center">'.
                           '<a href="'.$lien.'" class="thickbox" name="Modifier cette note de participation" '.
                           'title="Modifier cette note de participation au travail commun">'.$_SESSION['NoteWk'].'</a></td>';
                  }
                  else
                      echo '<td valign="top" align="center">&nbsp;</td>';
                  if ($utilisateur > 0 && $actform == $id_user)
                      echo '<td valign="top" align="center">&nbsp;</td>';
                  //if  (($typ_user == "ADMINISTRATEUR" || $actform == $id_user) && $scormOk == 1)
                      //echo '<td valign="top" align="center">&nbsp;</td>';
                  if ($utilisateur > 0)
                     unset($_SESSION['NoteWk']);
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
                  $NoteMM = GetDataField ($connect,"select mmnote_note_lb from mindmapnote where mmnote_app_no ='".$oMM->mmapp_cdn."'","mmnote_note_lb");
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

                  $cols = ($scormOk == 1) ? 1 : 3 ;
                  echo '<tr style="background-color:#D4E7ED;"><td valign="top"><div title="'.
                        $listeClanMM.'" style="clear:both;float:left;cursor:default;font-weight:bold;text-align:left;">'.
                        'Travail sur Carte heuristique</div>';
                  if ($utilisateur > 0)
                  {
                      if ($scormOk != 1)
                         echo '</td><td valign="top">';
                      else
                         echo '<div  style="float:left;margin-left:20px;">';
                      $lien= "mindmap/mindAffect.php?id_seq=$seq&id_parc=$id_parc&numero_groupe=$numero_groupe&numApp=$num_app&keepThis=true&TB_iframe=true&height=425&width=650";
                      echo "<a href=\"$lien\" class='thickbox' name='Composantes du groupe' ".
                           "title='Groupe de travail sur carte heuristique(binôme, trinôme ou plus) : ".
                           "liste des autres apprenants ayant un travail en commun sur cette séquence'>".
                           "Groupe MindMap</a>";
                      $MMaffecte = 1;
                      if ($scormOk == 1)
                         echo '</div>';
                  }
                  echo '</td><td colspan="'.$cols.'"><div style="text-align:left;">';
                  echo addslashes(NewHtmlEntityDecode($oMM->mindmap_intro_cmt,ENT_QUOTES)).'</div></td>';
                  if ((!$utilisateur && $visible == 1 && $proposable==1 && $autorise == 0 && $depasse == 0) || $utilisateur > 0)
                  {
                     $lien = "mindmap/index.php?numApp=$id_app&Provenance=SEQ&id_seq=$seq&id_parc=$id_parc&id_grp=$numero_groupe&id_clan=".
                             $oMM->mmapp_clan_nb."&id=".$oMM->mmapp_mindmap_no;
                     echo "<TD align='center' valign='top' ><A HREF=\"javascript:void(0);\" title=\"$mess_demarrer\" ".
                          "onClick=\"window.open('$lien',null,'status=no, directories=no,copyhistory=0,".
                          "titlebar=no,toolbar=yes,location=no,menubar=yes,scrollbars=yes,resizable=yes,".
                          "top=150,left=0,width=1024,height=700');\"".
                          "onmouseover=\"img$ii.src='images/ecran-annonce/icoGgob.gif';return true;\" ".
                          "onmouseout=\"img$ii.src='images/ecran-annonce/icoGgo.gif'\">";
                     echo "<IMG NAME=\"img$ii\" SRC=\"images/ecran-annonce/icoGgo.gif\" BORDER='0' ".
                          "onLoad=\"tempImg=new Image(0,0); tempImg.src='images/ecran-annonce/icoGgob.gif'\"></A></TD>";
                     echo '<SCRIPT language="javascript">
                               $.ajax ({type: "GET",url:"mindmap/unlock.php",data:"id='.$oMM->mmapp_mindmap_no.'&Provenance=SEQ"});
                          </script>';
                  }
                  else
                  {
                      echo "<TD align='center' valign='top' >".
                           "<IMG SRC=\"images/ecran-annonce/icoGgoinactif.gif\" border='0' ALT= \"$mess_inactif\"></TD>";
                  }
                  $nbMM = mysql_num_rows(mysql_query("select * from mindmaphistory where
                                        mindhisto_auteur_no = $id_app and mindmap_clan_no = ".$oMM->mmapp_clan_nb));
                  if (!empty($_SESSION['NoteMM']))
                     echo "<td valign='top'>Fait</td>";
                  elseif ($nbMM == 0)
                     echo "<td valign='top' nowrap>A faire</td>";
                  elseif ($nbMM > 0 && $_SESSION['NoteMM'] == '')
                     echo "<td valign='top' nowrap>En cours</td>";
                  if (!empty($_SESSION['NoteMM']) && empty($utilisateur))
                  {
                     echo "<td valign='top' align='center'><strong>".$_SESSION['NoteMM']."</strong></td>";
                  }
                  elseif ($actform == $id_user && empty($_SESSION['NoteMM']) && $utilisateur > 0 && $nbMM > 0)
                  {
                      $lien= "mindmap/mindmapNote.php?numApp=$num_app&IdMM=".$oMM->mmapp_cdn.
                             "&keepThis=true&TB_iframe=true&height=260&width=450";
                      echo '<td valign="top" align="left">'.
                           '<a href="'.$lien.'" class="thickbox" name="Noter la participation à ce travail en commun" '.
                           'title="Noter la participation à ce travail commun sur une carte heuristique">Noter</a></td>';
                  }
                  elseif (empty($_SESSION['NoteMM']) && $id_app > 0  && $nbMM == 0)
                  {
                      echo '<td valign="top" align="center">&nbsp;</td>';
                  }
                  elseif ($actform == $id_user && !empty($_SESSION['NoteMM']) && $utilisateur > 0 && $nbMM > 0)
                  {
                      $lien= "mindmap/mindmapNote.php?numApp=$num_app&IdMM=".$oMM->mmapp_cdn.
                             "&keepThis=true&TB_iframe=true&height=260&width=450";
                      echo '<td valign="top" align="center">'.
                           '<a href="'.$lien.'" class="thickbox" name="Modifier cette note de participation" '.
                           'title="Modifier cette note de participation au travail commun">'.$_SESSION['NoteMM'].'</a></td>';
                  }
                  else
                      echo '<td valign="top" align="center">&nbsp;</td>';
                  if ($utilisateur > 0 && $actform == $id_user)
                      echo '<td valign="top" align="center">&nbsp;</td>';
                 // if  (($typ_user == "ADMINISTRATEUR" || $actform == $id_user) && $scormOk == 1)
                     // echo '<td valign="top" align="center">&nbsp;</td>';
                  if ($utilisateur > 0)
                     unset($_SESSION['NoteMM']);
                  echo '</tr>';
               }
               //fin mindmap
                 // echo "<pre>";print_r($itemTwit);echo "</pre>";
               if ($TweetOk == 1)
               {
                 $ReqTwitActif = mysql_query("select * from seq_twit_form where seqformtwit_seq_no='$seq' and
                                      seqformtwit_parc_no='$id_parc' and seqformtwit_grp_no='$numero_groupe'");
                 $nbrTwitActif = mysql_num_rows($ReqTwitActif);
                 if ($nbrTwitActif > 0)
                 {
                   $itemTwit = mysql_fetch_object($ReqTwitActif);
                   if ($itemTwit->seqformtwit_flag_on == 1)
                   {
                   $req = mysql_query("delete from datalrs_user, datalrs_activite where datalrs_user_lb='apprenant|1|40|T|1' and datalrs_cdn=datalrs_act_statmt_no");
                       $_SESSION['TinySeqTwit'] = $itemTwit->seqformtwit_code_lb;
                       unset($_COOKIE['course']);
                       unset($_COOKIE['registration']);
                       echo '<tr style="background-color:#D4E7ED;"><td valign="top"><div>';
                       $course = base64url_encode('apprenant|'.$id_parc.'|'.$seq.'|T|'.$numero_groupe);
                       $lien = "admin/statements.php?course=$course&utilisateur=$utilisateur&registration=$Register&Activity=ApiTweet&keepThis=true&TB_iframe=true&height=450&width=750";
                       echo "<div id='statement_Twitter' style='float:left;display:none;'>
                            <script language='javascript'>
                            $.ajax({
                                    type: 'GET',
                                    url: 'http://lms.annulab.com/TinCanApi/statements.php',
                                    data: 'verb=1&utilisateur=$id_app&course=$course&registration=$Register',
                                    success: function(msg)
                                    {
                                       if (msg == 'En attente')
                                          $('#tincan_Tweet').css('display','none');
                                       else
                                          $('#xApi_Tweet').html('<img src=\"images/icones/ampoule18.gif\" border=0 title=\"Activité sur le réseau social de la séquence\">');
                                    }
                            });
                            </script></div>";
                       echo "<DIV id='tincan_Tweet' style='float:left;'><A HREF = \"$lien\" ".
                            "name='Activité sur Twitter' class='thickbox'>".
                            "<div id='xApi_Tweet' style='float:left;margin-right:8px;'></div></A></div>";
                       echo '<div title="Discussion sur Twitter concernant cette séquence" '.
                            'style="float:left;cursor:help;font-weight:bold;">Twitter</div></div> </td>';
                       $cols = ($scormOk == 1) ? 3 : 5 ;
                       echo '<td colspan="'.$cols.'">';
                       echo "Cette activité vous permettra de suivre tout ce que vos collègues ou formateurs ont inséré".
                            " dans le fil de discussion sur Twitter. Nous vous conseillons de vous concerter pour échanger ".
                            "en direct vos impressions. Si vous n'êtes pas connectés à la plate-forme, vous pourrez suivre ".
                            "cette discussion et insérer vos tweets depuis votre Smartphone, votre Tablette ou simplement ".
                            "le Web via l'application twitter.com. Pour cela faites une recherche en mettant le HashTag ".
                            "<a href='https://twitter.com/search/realtime?q=%23".$_SESSION['TinySeqTwit']."&src=typd' target='_blank' ".
                            "title='Lancer une recherche pour tester'><span style='color:blue;font-weight:bold;font-size:14px;'>#".$_SESSION['TinySeqTwit'].
                            "</span></a> et n'oubliez par de le saisir pour répondre aux tweets. ".
                            "La validation des tweets par Twitter exige un temps de latence de 5 à 10 secondes. ".
                            "Alors soyez patients en attendant de voir s'afficher votre tweet.</td>";
                       $cols1 = ($scormOk == 1) ? 2 : 3 ;
                       $LeType = ($_SESSION['typ_user'] == 'APPRENANT') ? 'apprenant' : 'formateur';
                       $course = base64url_encode($LeType.'|'.$id_parc.'|'.$seq.'|T|'.$numero_groupe);
                       $RegisterTwit = base64url_encode($_COOKIE["monID"].'_'.$_COOKIE["monLogin"].'|'.str_replace('http://','',str_replace('.educagri.fr','',$adresse_http)));
                       echo '<script type="text/javascript">
                                   document.cookie="registration"+"="+escape("'.$RegisterTwit.'");
                                   document.cookie="course"+"="+escape("'.$course.'");
                                </script>'."\n";
                       $link = '&course='.$course.'&registration='.$RegisterTwit;
                       echo '<td colspan="'.$cols1.'" align="center"><div id="sequenceTweet"><a href="javascript:void(0);" '.
                            'onClick="javascript:jQ_AjaxTweet(\'ApiTweet/LibSeq/SeqTwit.php?lancerTwit=1'.$link.'\');" '.
                            'title="Cliquez pour ouvrir la fenêtre de dialogue Twitter">'.
                            '<img src="ApiTweet/assets/TwitLogo.gif" border="0"></a></div></td></tr>';
                       if  (($typ_user == "ADMINISTRATEUR" || $actform == $id_user) && $scormOk == 1)
                           $cols2 = ($scormOk == 1) ? 6 : 8 ;
                       else
                           $cols2 = ($scormOk == 1) ? 5 : 8 ;
                       echo '<tr><td colspan="'.$cols2.'"><div id="TweetSeq" style="text-align:center;"></div></td></tr>';
                   }
               }
               else
                  unset ($_SESSION['TinySeqTwit']);
           }
           $pointeur = 0;
           $visible = 0;
           $suivi_etat_seq = $suiviSeqItem->suiv2_etat_lb;
           $req_act_lib = mysql_query("SELECT count(act_cdn) from activite where act_seq_no = 0");
           $nb_act_lib = mysql_result($req_act_lib,0);
           if ($actform == $id_user && $suivi_etat_seq != "TERMINE" && $nb_act_lib > 0 && $id_classe == 1)
           {
              $chaine = getenv("QUERY_STRING");
              $params = str_replace("&","|",$chaine);
              $lien= "activite_free.php?id_seq=$seq&id_parc=$id_parc&numero_groupe=$numero_groupe&utilisateur=$utilisateur&sequence=$seq&params=$params&ajouter=1";
              $lien = urlencode($lien);
              echo "</TD></TR><TR><td width='2%' align='right' valign='top' bgcolor='#FFFFFF' colspan='9'>$bouton_gauche<a href=\"trace.php?link=$lien\"".
                   bulle($mess_ajtact_plus,"","RIGHT","ABOVE",170)."$mess_ag_choix</A>$bouton_droite</TD></TR>";
           }
           // wiki
           $reqSeqWk=mysql_query("select * from wiki where wiki_seq_no='$seq'");
           $nbSeqWk = mysql_num_rows($reqSeqWk);
           if($actform == $id_user && $suivi_etat_seq != "TERMINE" && $nbWk == 0 && $nbSeqWk > 0)
           {
              $lien= "wiki/wiki.php?id_seq=$seq&id_parc=$id_parc&numero_groupe=$numero_groupe&numApp=$num_app&keepThis=true&TB_iframe=true&height=425&width=650";
              echo "<TR><td width='2%' align='right' valign='top' bgcolor='#FFFFFF' colspan='9'>$bouton_gauche".
                   "<a href=\"$lien\" class='thickbox' title='Ajouter un travail en commun' name='Ajouter un travail en commun' ".
                   bulle("Son travail en groupe réduit : s'il n'en a pas, l'intégrer dans un groupe existant travaillant déjà sur un thème ".
                   "ou créer un nouveau groupe, binôme ou trinôme en choisissant un thème.." ,"","RIGHT","ABOVE",270).
                   "Travail en commun -WikiDoc-</A>$bouton_droite </TD></TR>";
           }
           // wiki
           $ReqMind = mysql_query("select * from mindmap where mindmap_seq_no = ".$seq."");
           if($actform == $id_user && $suivi_etat_seq != "TERMINE" && mysql_num_rows($ReqMind) > 0 && empty($MMaffecte))
           {
               $lien= "mindmap/mindAffect.php?id_seq=$seq&id_grp=$numero_groupe&numero_groupe=$numero_groupe&id_parc=$id_parc&numApp=$num_app&keepThis=true&TB_iframe=true&height=350&width=550";
               $contentMM  = "<TR><td width='2%' align='right' valign='top' bgcolor='#FFFFFF' colspan='9'>$bouton_gauche";
               $contentMM .= "<a href=\"$lien\" class='thickbox' name='Cartes heuristiques liées à cette formation' ".
                             bulle("Contruction d'une carte heurique en groupe réduit : ".
                             "s'il n'en a pas, l'intégrer dans un groupe existant ".
                             "travaillant déjà sur une carte ou créer un nouveau groupe, binôme ou trinôme".
                             " en choisissant une autre carte heuristique si elle existe.." ,"","RIGHT","ABOVE",270).
                             "Travail en commun sur une carte heuristique</A>$bouton_droite </TD></TR>";
               echo $contentMM;
           }
           echo "</table>";
           echo "</TD></TR></TABLE>";

}
if (isset($_SESSION['id_act']))
{
   echo '<script language="javascript">
         function loadData() {
              simplejQ_Ajax("quit_activ.php?id_act='.$_SESSION['id_act'].'");
         }
         </script>';
}
?>
<div id="mien" class="cms"></div>
</body></html>