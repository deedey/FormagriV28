<?php
if (!isset($_SESSION)) session_start();
include ("include/UrlParam2PhpVar.inc.php");
if (!isset($_SESSION['id_user']) || $_SESSION['id_user'] == "")
{
  exit();
}
require "admin.inc.php";
require 'fonction.inc.php';
require "lang$lg.inc.php";
require "fonction_html.inc.php";
require "class/class_formation.php";
require "langues/formation.inc.php";
dbConnect();
include ("include/varGlobals.inc.php");
$date_op = date("Y-m-d H:i:s" ,time());
$heure_fiche = substr($date_op,11);
$date_fiche = substr($date_op,0,10);
// Suppression des prescriptions d'un apprenant
if (isset($supprimer_app) && $supprimer_app == 1)
{
//dey_presc
    $ext = "_$id_grp";
    $nb_fois = SupprimePrescriptionApprenant($num,$id_grp,$ext);
    if ($nb_fois > 0)
      $lien = "gestion_affectation.php?affecte_groupe=1&grp_resp=$id_grp&le_groupe=$id_grp&ret_supp=1&num_app=$num";
    else
      $lien = "gestion_groupe.php?cms=1&ordre_affiche=nom_groupe&le_groupe=$id_grp&ret_supp=1&num_app=$num";
    $lien = urlencode($lien);
    echo "<script language=\"JavaScript\">";
      echo "document.location.replace(\"trace.php?link=$lien\")";
    echo "</script>";
  exit();
}
if (isset($supp_tous) && $supp_tous == 1)
{
    $crea_grp = GetDataField ($connect,"SELECT grp_resp_no from groupe where grp_cdn  = $id_grp","grp_resp_no");
    if ($typ_user == "ADMINISTRATEUR")
              $req_util = requete("utilisateur.util_cdn","utilisateur,utilisateur_groupe","utilisateur.util_typutil_lb = 'APPRENANT' and utilisateur.util_cdn = utilisateur_groupe.utilgr_utilisateur_no and utilisateur_groupe.utilgr_groupe_no = $id_grp");
    elseif ($typ_user == "RESPONSABLE_FORMATION")
    {
          if ($crea_grp == $id_user)
             $req_util = requete("utilisateur.util_cdn","utilisateur,utilisateur_groupe","utilisateur.util_typutil_lb = 'APPRENANT' and utilisateur.util_cdn = utilisateur_groupe.utilgr_utilisateur_no and utilisateur_groupe.utilgr_groupe_no = $id_grp");
          else
             $req_util = requete("utilisateur.util_cdn","utilisateur,utilisateur_groupe","utilisateur.util_typutil_lb = 'APPRENANT' AND utilisateur.util_cdn = utilisateur_groupe.utilgr_utilisateur_no AND utilisateur_groupe.utilgr_groupe_no = $id_grp AND utilisateur.util_auteur_no = $id_user");
    }
    while ($data_util = mysql_fetch_object($req_util))
    {
          $id_util = $data_util->util_cdn;
          //dey_presc
          $ext = "_$id_grp";
          SupprimePrescriptionApprenant($id_util,$id_grp,$ext);
    }
    $lien = "gestion_groupe.php?cms=1&ordre_affiche=nom_groupe&le_groupe=$id_grp&ret_supp_tous=1";
    $lien = urlencode($lien);
    echo "<script language=\"JavaScript\">";
    echo "document.location.replace(\"trace.php?link=$lien\")";
    echo "</script>";
  exit();
}
if (isset($change_date) && $change_date == 1)
{
   if (isset($vient_de_gest) && $vient_de_gest == 1)
      include ("style.inc.php");
   elseif (isset($vient_de_app) && $vient_de_app == "1")
      include ("style.inc.php");
   if (isset($debut) && $debut == 1){
      $seq_change = GetDataField ($connect,"SELECT presc_datedeb_dt from prescription_$numero_groupe where
                                  presc_seq_no = $id_seq and
                                  presc_utilisateur_no=$utilisateur and presc_grp_no=$numero_groupe","presc_datedeb_dt");
      $mot = "début";
   }
   else
   {
      $mot = "fin";
      $seq_change = GetDataField ($connect,"SELECT presc_datefin_dt from prescription_$numero_groupe where
                                  presc_seq_no = $id_seq and
                                  presc_utilisateur_no=$utilisateur and presc_grp_no = $numero_groupe","presc_datefin_dt");
   }
   $ch_datenew = explode ("-",$seq_change);
   $date_fin = "$ch_datenew[2]-$ch_datenew[1]-$ch_datenew[0]";
   echo "<CENTER><TABLE bgColor='#298CA0' cellspacing='2' cellpadding='0'><TR><TD>";
   echo "<TABLE bgColor='#FFFFFF' cellspacing='1' cellpadding='0' width='100%'>";
   echo "<TR><TD background=\"images/fond_titre_table.jpg\" colspan='2' height='40' align='center' valign='center'>";
   echo "<Font size='3' color='#FFFFFF'><B>$gest_presc : $modif_date</B></FONT></TD></TR>";
   $titre_seq = GetDataField ($connect,"select seq_titre_lb from sequence where seq_cdn=$id_seq","seq_titre_lb");
   echo "<TR><TD align=center' colspan='2' height='40'>$titre_seq</TD></TR>";
   echo "<TR><TD>";
   if (isset($vient_de_gest) && $vient_de_gest == 1)
     echo "<center><FORM name=MForm action='gest_parc.php?numero_groupe=$numero_groupe&change_date=1&hgrp=$hgrp&debut=$debut&a_faire=1&date_fin=$date_fin&change_seq=$id_seq&utilisateur=$utilisateur&parc=$parc&id_seq=$id_seq' method='POST' target='main'>";
   elseif (isset($vient_de_app) && $vient_de_app == 1)
     echo "<center><FORM name=MForm action=\"annonce_app.php?numero_groupe=$numero_groupe&change_date=1&hgrp=$hgrp&id_seq=$id_seq&date_fin=$date_fin&utilisateur=$utilisateur&entantque=$entantque\" method='POST' target='principal'>";
?>   <TABLE>
     <TR>
      <TD>
       <small><font color=marroon><?php  echo "$mpr_new_dt  $mot $mpr_deseq" ;?></B></FONT></small>
      </TD>
       <input type="hidden" name=txt_custom value="AppendOrReplace=Replace;AppendChar=';';CloseOnSelect=Yes;ReturnData=Date;Title=Calendrier;InlineX=240;InlineY=140;CurrentDate=Today;SelectAfter=Today-30;SelectBefore=Today+30;AllowWeekends=No;Resizable=Yes;CallFunction=TryCallFunction;PopupX=300;PopupY=300;Nav=Yes;SmartNav=Yes;Fix=No;WeekStart=1;Weekends=06">
      <TD><input type="text" name="imonth" value="<?php  echo $day;?>" MAXLENGTH="2" size="2"></TD>
      <TD><input type="text" name="iday" value="<?php  echo $month;?>" MAXLENGTH="2" size="2"></TD>
      <TD><input type="text" name="iyear" value="<?php  echo $year;?>" MAXLENGTH="4" size="4"></TD>
      <TD><input type="hidden" value="" name="mydate1">
      <a href="javascript:show_calendar('MForm.mydate1','09', '2002', 'DD/MM/YYYY', 'INLINE', MForm.txt_custom.value);"
               onmouseover="img_cal1.src='images/agenda/icocalendb.gif';return true;"
               onmouseout="img_cal1.src='images/agenda/icocalend.gif'">
               <IMG NAME="img_cal1" SRC="images/agenda/icocalend.gif" BORDER='0' valign='top' alt="<?php  echo $cal_click  ;?>"
               onLoad="tempImg=new Image(0,0); tempImg.src='images/agenda/icocalendb.gif'"></A>
     </TR>
   </TABLE>
   <?php
   echo "</TD></TR>";
   echo boutret(1,0);
   echo "</TD><TD align='center'><A HREF=\"javascript:document.MForm.submit();\" onmouseover=\"img1.src='images/fiche_identite/boutvalidb.gif';return true;\" onmouseout=\"img1.src='images/fiche_identite/boutvalid.gif'\">".
        "<IMG NAME=\"img1\" SRC=\"images/fiche_identite/boutvalid.gif\" BORDER='0' onLoad=\"tempImg=new Image(0,0); tempImg.src='images/fiche_identite/boutvalidb.gif'\"></A>";
   echo "</FORM> ";
   echo "</TD></TR></TABLE></TD></TR></TABLE>";
   ?>
    <DIV ID=Main>
      <!-- A Separate Layer for the Calendar -->
      <!-- Make sure to use the name Calendar for this layer -->
      <SCRIPT Language="Javascript" TYPE="text/javascript">
         Calendar.CreateCalendarLayer(10, 275, "");
      </SCRIPT>
    </DIV>
<?php
exit();
}
$today = date("d/n/Y");
if (isset($mail_sous_grp) && $mail_sous_grp == 1)
{
   $send_to = "";
   $list_envoi = explode(",",$liste_envoi);
   $nb_envoi = count($list_envoi);
   $i=0;
   while ($i < $nb_envoi){
      $envoyer = explode("|",$list_envoi[$i]);
      $adresse = $envoyer[0];
      $num = $envoyer[1];
      if ($envoi[$i] == 'on' && $i < $nb_envoi-1)
         $send_to .= $adresse.",";
      elseif ($envoi[$i] == 'on' && $i == $nb_envoi-1)
         $send_to .= $adresse;
    $i++;
 }
 print("<SCRIPT language=javascript>");
 print("window.open('mail.php?send_to=$send_to&sous_grp=1&message_mail=$vers','','width=680,height=520,resizable=yes,status=no')");
 print("</SCRIPT>");
}
if (isset($affiche_fiche_app) && $affiche_fiche_app == 1)
{
  include ("style.inc.php");
  if (isset($id_util) && $id_util > 0)
  {
    $req = mysql_query("select * from utilisateur where util_cdn=$id_util");
    while ($item = mysql_fetch_object($req))
    {
        $id_nom = $item->util_nom_lb;
        $id_prenom = $item->util_prenom_lb;
        $id_photo = $item->util_photo_lb;
        $id_titre = $item->util_typutil_lb;
        $id_email = $item->util_email_lb;
        $id_webmail = $item->util_urlmail_lb;
        $id_tel = $item->util_tel_lb;
        $id_com = $item->util_commentaire_cmt;
        $inscripteur = $item->util_auteur_no;
    }
    echo "<CENTER><TABLE bgColor='#298CA0' cellspacing='2' cellpadding='0' width='98%'><TR><TD>";
    echo "<TABLE bgColor='#FFFFFF' cellspacing='1' cellpadding='0' width='100%'>";
    echo "<TR><TD background=\"images/fond_titre_table.jpg\" colspan='2' height='34' align='center' valign='center'><Font size='3' color='#FFFFFF'><B>$mess_suite_fp</B></FONT></TD></TR>";
    echo "<TR><TD colspan='2'><TABLE cellspacing='2' cellpadding='2' width='100%' bgColor='#FFFFFF' border=0>";
    echo "<TR bgColor='#EFEFEF'><TD valign='top' height='30' width='20%'>";
    if (isset($identite) && $identite == 1)
    {
       if ($id_photo != "")
         echo "<IMG SRC=\"images/$id_photo\" border=0>";
       else
         echo "<IMG SRC=\"images/ecran_profil/ombre.jpg\" border='0'>";
    }
    echo"</TD><TD valign='top'><TABLE cellspacing='2' cellpadding='2' width='100%' bgColor='#FFFFFF' border='0'>";
    echo "<TR bgColor='#F4F4F4'><TD valign='top' height='30'><B>$id_nom $id_prenom</B></TD></TR>";
    echo "<TR bgColor='#FFFFFF'><TD>";
    $lien= "mail.php?contacter=1&a_qui=$id_email&num=$id_util";
    $lien = urlencode($lien);
    echo "<A HREF=\"#\" onclick=\"window.open('trace.php?link=$lien','','width=680,height=520,resizable=yes,status=no')\">$id_email</A></TD></TR>";
    echo "<TR bgColor='#F4F4F4'><TD>$id_titre</TD></TR>";
    echo "</TABLE></TD></TR>";
    if ($id_com != '')
       echo "<TR bgColor='#FFFFFF'><TD height='30'><B>$mess_admin_comment</B></TD><TD>".NewHtmlEntityDecode($id_com)."</TD></TR>";
    echo "<TR bgColor='#F4F4F4'><TD height='30'><B>$mess_admin_tel</B></TD><TD>$id_tel</TD></TR>";
    if ($id_titre == "APPRENANT")
    {
       $group = mysql_query("SELECT grp_cdn from groupe,utilisateur_groupe WHERE utilisateur_groupe.utilgr_utilisateur_no = $id_util AND utilisateur_groupe.utilgr_groupe_no = groupe.grp_cdn ORDER BY grp_nom_lb ASC");
       $nbr_grp = mysql_num_rows($group);
       if ($nbr_grp != 0)
       {
         echo "<TR bgColor='#FFFFFF'><TD valign='top'nowrap align='left'><B>$mess_grp_form_suiv </B></TD><TD align='left'>";
         $g = 0;
         if ($nbr_grp > 1)
            echo "<UL>";
         while ($g < $nbr_grp)
         {
            $id_grp = mysql_result($group,$g,"grp_cdn");
            $nom_grp = GetDataField ($connect,"SELECT grp_nom_lb from groupe where grp_cdn  = $id_grp","grp_nom_lb");
            if ($nbr_grp > 1)
               echo "<LI>$nom_grp</LI>";
            else
               echo "$nom_grp";
          $g++;
         }
         if ($nbr_grp > 1)
            echo "</UL>";
         echo "</TD></TR>";
       }
       if ($inscripteur > 0)
       {
          $nom_inscripteur=GetDataField ($connect,"select util_nom_lb from utilisateur where util_cdn='$inscripteur'","util_nom_lb");
          $prenom_inscripteur=GetDataField ($connect,"select util_prenom_lb from utilisateur where util_cdn='$inscripteur'","util_prenom_lb");
          $nom_resp =$prenom_inscripteur." ".$nom_inscripteur;
          $photo_inscripteur = GetDataField ($connect,"SELECT util_photo_lb from utilisateur where util_cdn = $inscripteur","util_photo_lb");
          echo "<TR bgColor='#F4F4F4'><TD valign='top' height='30' nowrap><B>$mess_inscripteur</B></TD>";
          $lien="prescription.php?affiche_fiche_app=1&hgrp=$hgrp&id_util=$inscripteur&identite=1";
          $lien= urlencode($lien);
          echo "<TD><DIV id='sequence'><A href=\"trace.php?link=$lien\"";
          if ($photo_inscripteur != "")
          {
            list($w_img, $h_img, $type_img, $attr_img) = getimagesize("images/$photo_inscripteur");
            echo " onMouseOver=\"overlib('', TEXTCOLOR, '#ffffff', TEXTSIZE, 2, WIDTH, $w_img, HEIGHT, $h_img, BACKGROUND, 'images/$photo_inscripteur', PADX, 60, 20, PADY, 20, 20)\" onMouseOut=\"nd()\"";
          }
          else
            echo " title=\"$mess_suite_fp\"";
          echo ">$nom_resp</A></DIV></TD></TR>";
       }
       $req_tut = mysql_query("SELECT tut_tuteur_no from tuteur where tut_apprenant_no = $id_util");
       $nomb_tut = mysql_num_rows($req_tut);
       if ($nomb_tut > 0)
       {
         echo "<TR bgColor='#FFFFFF'><TD valign='top' height='30' nowrap><B>".ucfirst(strtolower($mess_typ_tut))."</B></TD>";
         echo "<TD><DIV id='sequence'>";
         $i_t = 0;
         while ($i_t < $nomb_tut)
         {
           if ($i_t > 0)
             echo "<BR>";
           $num_tut = mysql_result($req_tut,$i_t,"tut_tuteur_no");
           $nom_tut=GetDataField ($connect,"SELECT util_nom_lb from utilisateur where util_cdn='$num_tut'","util_nom_lb");
           $prenom_tut=GetDataField ($connect,"SELECT util_prenom_lb from utilisateur where util_cdn='$num_tut'","util_prenom_lb");
           $photo_tut = GetDataField ($connect,"SELECT util_photo_lb from utilisateur where util_cdn = $num_tut","util_photo_lb");
           $lien="prescription.php?affiche_fiche_app=1&hgrp=$hgrp&id_util=$num_tut&identite=1";
           $lien= urlencode($lien);
           echo "<A href=\"trace.php?link=$lien\"";
           if ($photo_tut != "")
           {
             list($w_img, $h_img, $type_img, $attr_img) = getimagesize("images/$photo_tut");
             echo " onMouseOver=\"overlib('', TEXTCOLOR, '#ffffff', TEXTSIZE, 2, WIDTH, $w_img, HEIGHT, $h_img, BACKGROUND, 'images/$photo_tut', PADX, 60, 20, PADY, 20, 20)\" onMouseOut=\"nd()\"";
           }
           else
             echo " title=\"$mess_suite_fp\"";
           echo ">$prenom_tut $nom_tut</A>";
          $i_t++;
         }
         echo "</DIV></TD></TR>";
       }
    }
    if ($typ_user != "APPRENANT")
    {
      if ($id_titre == "APPRENANT")
      {
         $sql_id_ref = mysql_query ("SELECT insc_referentiel_no from inscription where insc_apprenant_no = $id_util AND insc_referentiel_no > 0 group by insc_referentiel_no");
         $nbre_sql = mysql_num_rows ($sql_id_ref);
         if ($nbre_sql != 0)
         {
            $nnn=0;$nom_referentiel = "";
            while ($nnn < $nbre_sql)
            {
              $nne=$nnn+1;
              $id_referentiel = mysql_result($sql_id_ref,$nnn,"insc_referentiel_no");
              if ($id_referentiel)
              {
                 $nom_ref= GetDataField ($connect,"SELECT ref_desc_cmt  from referentiel where ref_cdn = $id_referentiel","ref_desc_cmt");
                 if ($nom_ref == "")
                    $nom_ref= GetDataField ($connect,"SELECT ref_nomabrege_lb from referentiel where ref_cdn = $id_referentiel","ref_nomabrege_lb");
                 $nom_referentiel .= str_replace("'"," ",$nom_ref).".<BR><BR>";
              }
            $nnn++;
            }
            echo "<TR bgColor='#FFFFFF'><TD valign='top'><B>$mess_gp_obj_form</B></TD><TD>$nom_referentiel</TD><TD>&nbsp;</TD></TR>";
         }
      }
       if ($id_titre != "APPRENANT" && $id_titre != "TUTEUR" && $typ_user != "APPRENANT")
       {
              $sql_id_ref = mysql_query ("SELECT * from parcours where parcours_auteur_no = $id_util");
              $nbre_sql_parc = mysql_num_rows ($sql_id_ref);
              $sql_id_ref = mysql_query ("SELECT * from sequence where seq_auteur_no = $id_util");
              $nbre_sql_seq = mysql_num_rows ($sql_id_ref);
              if ($nbre_sql_parc > 0 || $nbre_sql_seq > 0)
              {
                $lien = "parseq_popup.php?id_util=$id_util";
                echo "<TR bgColor='#F4F4F4'><TD colspan='2'>&nbsp;<BR><A HREF=\"#\" onclick=\"javascript:window.open('$lien','','left=0, top=0, width=640,height=550,resizable=yes,scrollbars=yes, menubar=0,location=0, toolbar=0')\">$mpr_lisparc_form</A><BR></TD></TR>";
              }

      }
    }
  }
  if (isset($identite) && $identite == 1)
  {
    echo "";
    if (!isset($stopper) || (isset($stopper) && $stopper != 1))
       echo boutret(1,1);
    echo "</TABLE></TD></TR></TABLE></TD></TR></TABLE>";
  exit;
  }
  if (isset($id_group) && $id_group > 0)
  {
    $numero_groupe = $id_group;
    $nom_grp = GetDataField ($connect,"SELECT grp_nom_lb from groupe where grp_cdn  = $id_group","grp_nom_lb");
    $comment_grp = GetDataField ($connect,"SELECT grp_commentaire_cmt from groupe where grp_cdn  = $id_group","grp_commentaire_cmt");
    if ($typ_user == "ADMINISTRATEUR"  || ($typ_user == "RESPONSABLE_FORMATION" && $hgrp != 1))
      $liste_apprenant = mysql_query("SELECT * from utilisateur_groupe,utilisateur where utilgr_groupe_no = '$id_group' AND utilgr_utilisateur_no = util_cdn ORDER BY util_nom_lb,util_prenom_lb ASC");
    elseif ($typ_user == "FORMATEUR_REFERENT" || ($typ_user == "RESPONSABLE_FORMATION" && $hgrp == 1))
      $liste_apprenant = mysql_query("SELECT * from utilisateur,utilisateur_groupe,prescription_$id_group,tuteur where utilgr_groupe_no = '$id_group' AND presc_grp_no = $id_group and utilgr_utilisateur_no = presc_utilisateur_no and (presc_formateur_no=$id_user OR (tut_apprenant_no = utilgr_utilisateur_no AND tut_tuteur_no = $id_user)) AND utilgr_utilisateur_no = util_cdn group by utilgr_utilisateur_no ORDER BY util_nom_lb,util_prenom_lb ASC");
    elseif ($typ_user == "TUTEUR")
      $liste_apprenant = mysql_query("SELECT * from utilisateur_groupe,tuteur,utilisateur where utilgr_groupe_no = '$id_group' and utilgr_utilisateur_no = tut_apprenant_no and tut_tuteur_no =$id_user AND utilgr_utilisateur_no = util_cdn group by utilgr_utilisateur_no  ORDER BY util_nom_lb,util_prenom_lb ASC");
    $nbre_apprenant = mysql_num_rows($liste_apprenant);

    echo "<CENTER><TABLE bgColor='#298CA0' cellspacing='2' cellpadding='0' width='98%'><TR><TD>";
    echo "<TABLE bgColor='#FFFFFF' cellspacing='1' cellpadding='0' width='100%'>";
    echo "<TR><TD background=\"images/fond_titre_table.jpg\" colspan='2' height='34' align='center' valign='center'><Font size='3' color='#FFFFFF'><B>$mess_suite_ap_grp $nom_grp </B></FONT></TD></TR>";
    echo "<FORM name='form1' action = \"prescription.php?mail_sous_grp=1&affiche_fiche_app=1&hgrp=1&nom_grp=$nom_grp&id_group=$id_group\" METHOD='POST'>";
    echo "<TR><TD><TABLE cellpadding='3' cellspacing='3'><TR bgcolor='#2B677A'>";
    echo "<TD align=left><FONT COLOR=white><B>$msq_apprenant</B></FONT></TD>";
//    echo "<TD align=center><FONT COLOR=WHITE><B>$mess_admin_prenom</B></FONT></TD>";
    echo "<TD align=center><FONT COLOR=WHITE><B>".ucfirst(strtolower($mpr_suivi))."</B></FONT></TD>";
    if ($typ_user != "TUTEUR")
    {
       echo "<TD align=left><FONT COLOR=white><B>".ucfirst(strtolower($mess_typ_tut))."</B></FONT></TD>";
       echo "<TD align=center><IMG SRC=\"images/modifier-bleu.gif\" width = '15' height = '15' border=0></TD>";
       echo "<TD align=center><IMG SRC='images/ecran-annonce/icolettrew.gif' border='0'></TD>";
    }
    else
       echo "<TD align=center><IMG SRC=\"images/copier.gif\" alt=\"$mess_menu_bilan\" width='15' height='15' border=0 ></TD>";
    echo "</TR>";
    $i=0;
    while ($itemApp == mysql_fetch_object($liste_apprenant))
    {
      $id_app = $itemApp->utilgr_utilisateur_no;
      $id_nom = $itemApp->util_nom_lb;
      $id_type = $itemApp->util_typutil_lb;
      $id_prenom = $itemApp->util_prenom_lb;
      $id_email = $itemApp->util_email_lb;
      $id_photo = $itemApp->util_photo_lb;
      $id_webmail = $itemApp->util_urlmail_lb;
      $id_tel = $itemApp->util_tel_lb;
      $id_com = $itemApp->util_commentaire_cmt;
      if ($i == ($nbre_apprenant-1))
      {
         $envoi_a .= "$id_email|$i";
         $send_to .=$id_email;
      }
      else
      {
         $envoi_a .= "$id_email|$i,";
         $send_to .= "$id_email,";
      }
      $id_util=$id_app;
      echo couleur_tr($i);
      $lien="prescription.php?affiche_fiche_app=1&hgrp=$hgrp&id_util=$id_util";
      $lien = urlencode($lien);
      if ($id_photo == '')
      {
         echo"<TD valign='top'><DIV id='sequence'><A HREF=\"trace.php?link=$lien \" title=\"$mess_suite_fp\" target='main'><B>$id_nom $id_prenom</B></A></DIV></TD>";
      }
      else
      {
        list($w_img, $h_img, $type_img, $attr_img) = getimagesize("images/$id_photo");
        echo "<TD valign='top'><DIV id='sequence'><A HREF=\"trace.php?link=$lien\" onMouseOver=\"overlib('', TEXTCOLOR, '#ffffff', TEXTSIZE, 2, WIDTH, $w_img, HEIGHT, $h_img, BACKGROUND, 'images/$id_photo', PADX, 60, 20, PADY, 20, 20)\" onMouseOut=\"nd()\"><B>$id_nom $id_prenom</B></FONT></A></DIV></TD>";
      }
//      $lien = "gest_parc.php?a_faire=1&hgrp=$hgrp&tout=1&utilisateur=$id_app&numero_groupe=$id_group";
      $lien = "gest_frm_rappel1.php?utilisateur=$id_util&a_faire=1&le_groupe=$id_group&id_grp=$id_group&numero_groupe=$id_group&graph=1&tout=1&vient_de_grp=1";
      $lien = urlencode($lien);
      echo"<TD align='middle' height='20' valign='top'><A href=\"trace.php?link=$lien\" target='main' title=\"$mess_lanc_mess1 $id_prenom $id_nom, $mess_lanc_mess2\"><img SRC=\"images/deroule.gif\" border=0 alt=\"$mess_lanc_mess1 $id_prenom $id_nom, $mess_lanc_mess2\"></A></TD>";
      if ($typ_user != "TUTEUR")
      {
         $lien = "prescription.php?id_util=$id_tut&identite=1&affiche_fiche_app=1";
         $lien = urlencode($lien);
         $req_tut = mysql_query ("SELECT tuteur.tut_tuteur_no,utilisateur.util_nom_lb,utilisateur.util_prenom_lb,utilisateur.util_photo_lb from tuteur,utilisateur where tuteur.tut_apprenant_no = $id_app and tuteur.tut_tuteur_no= utilisateur.util_cdn order by utilisateur.util_nom_lb");
         $nb_tut = mysql_num_rows($req_tut);
         if ($nb_tut > 0)
         {
            $i_tut=0;
            echo "<TD valign='top'><DIV id='sequence'>";
            while ($i_tut < $nb_tut)
            {
               $id_tut = mysql_result($req_tut,$i_tut,"tut_tuteur_no");
               $nom_tut = mysql_result($req_tut,$i_tut,"util_nom_lb");
               $prenom_tut =mysql_result($req_tut,$i_tut,"util_prenom_lb");
               $photo_tut =mysql_result($req_tut,$i_tut,"util_photo_lb");
               $lien="prescription.php?affiche_fiche_app=1&hgrp=$hgrp&id_util=$id_tut";
               $lien = urlencode($lien);
               if ($photo_tut == '')
               {
                 echo"<A HREF=\"trace.php?link=$lien \" title=\"$mess_suite_fp\" target='main'><B>$nom_tut $prenom_tut</B></A>";
               }
               else
               {
                 list($w_img, $h_img, $type_img, $attr_img) = getimagesize("images/$photo_tut");
                 echo "<A HREF=\"trace.php?link=$lien\" onMouseOver=\"overlib('', TEXTCOLOR, '#ffffff', TEXTSIZE, 2, WIDTH, $w_img, HEIGHT, $h_img, BACKGROUND, 'images/$photo_tut', PADX, 60, 20, PADY, 20, 20)\" onMouseOut=\"nd()\"><B>$nom_tut $prenom_tut</B></FONT></A>";
               }
               if ($i_tut < $nb_tut-1)
                 echo "<BR>";
             $i_tut++;
            }
            echo "</DIV></TD>";
         }
         else
            echo "<TD>&nbsp;</TD>";
         $lien = "modif_parc.php?a_faire=1&utilisateur=$id_util&hgrp=$hgrp&numero_groupe=$id_group";
         $lien = urlencode($lien);
         echo "<TD align='center' valign='top'><A href=\"trace.php?link=$lien\" target='main' title=\"$mess_menu_mod_presc $de $id_nom $id_prenom\">
         <IMG SRC=\"images/modifier-blanc.gif\" width = '15' height = '15' border=0 alt=\"$mess_menu_mod_presc $de $id_nom $id_prenom\"></A></TD>";
         echo "<TD valign='top' align='center'><INPUT TYPE='checkbox' name='envoi[$i]'></TD></TR>";
      }
      else
      {
         $req_traq = mysql_query("select traq_act_no from traque,activite,prescription_$id_group where traque.traq_util_no = $id_util AND traque.traq_act_no = activite.act_cdn AND presc_seq_no = activite.act_seq_no AND presc_utilisateur_no = traque.traq_util_no group by traque.traq_act_no ORDER BY activite.act_nom_lb");
         $nbr_trq = mysql_num_rows($req_traq);
         if ($nbr_trq > 0)
         {
            $lien = "bilan.php?bilan=1&utilisateur=$id_util&numero_groupe=$id_group";
            $lien = urlencode($lien);
            echo "<TD align='middle'><A HREF = \"trace.php?link=$lien\" target='main' title=\"$mess_menu_bilan $de $id_prenom $id_nom\"><IMG SRC=\"images/copier-blanc.gif\" border=0  alt=\"$mess_menu_bilan $de  $id_prenom $id_nom\" width='15' height='15'></A></TD>";
         }
         else
            echo "<TD>&nbsp;</TD>";
      }
    $i++;
    }
    if ($typ_user != "TUTEUR")
    {
       // Mail à certains apprenants du groupe
       $vers = "$mess_mail_cert_app $mess_menu_gestion_grp $nom_grp";
       echo "<INPUT type='HIDDEN' name='liste_envoi' value=\"$envoi_a\">";
       echo "<INPUT type='HIDDEN' name='vers' value=\"$vers\">";
       echo "<TR><TD colspan='5' valign='top' align='right'><A HREF=\"javascript:document.form1.submit();\"".
            " title=\"$mess_mail_avert $mess_mail_cert_app $mess_menu_gestion_grp $nom_grp\"".
            " onmouseover=\"img_lettre2.src='images/ecran-annonce/icolettrebw.gif';return true;\"".
            " onmouseout=\"img_lettre2.src='images/ecran-annonce/icolettrew.gif'\">".
            "<IMG NAME=\"img_lettre2\" SRC=\"images/ecran-annonce/icolettrew.gif\" BORDER='0'".
            " onLoad=\"tempImg=new Image(0,0); tempImg.src='images/ecran-annonce/icolettrebw.gif'\"></A></TD></TR></FORM>";
    }
    echo"<TR><TD colspan=4>";
    if ($typ_user == "ADMINISTRATEUR" || ($typ_user == "RESPONSABLE_FORMATION" && $hgrp != 1))
    {
       echo"</TD></TR></TABLE></CENTER>";
    }
    else
    {
       // accés graphique et planning du groupe
       $lien = "lancement.php?numero_groupe=$numero_groupe&id_grp=$id_group&groupe=1&hgrp=$hgrp";
       $lien = urlencode($lien);
       echo "<A HREF=\"trace.php?link=$lien\" target='main' title=\"$mpr_plan_grp\">$mpr_consult_grp $nom_grp</A><BR><BR>";
       // Repartoire du groupe
       $nom_grpe =  GetDataField ($connect,"select grp_nom_lb from groupe where grp_cdn = $id_group","grp_nom_lb");
       $lien_rp = "modif_rep_fic.php?id_grp=$id_group&formateurs=&nom_grp=$nom_grpe&communes_groupe=1&rallonge=ressources/groupes/$id_group&sousdos=ressources/groupes/$id_group&parent=ressources/groupes&dossier=ressources/groupes/$id_group&fichier=$id_group&direct=dossier";
       $lien_rp = urlencode($lien_rp);
       echo "<A HREF=\"trace.php?link=$lien_rp\" title =\"$mess_menu_casier_groupe\">$mess_menu_casier_groupe</A><BR><BR>";
       // accès forum du groupe
       $nom_grpe =  GetDataField ($connect,"select grp_nom_lb from groupe where grp_cdn = $id_group","grp_nom_lb");
       $id_forum = GetDataField ($connect,"select id from forums where name ='$nom_grpe'","id");
       $comment_forum = GetDataField ($connect,"select name from forums where id='$id_forum'","name");
       $lien_forum="forum/list.php?f=$id_forum&collapse=0";
       $lien_forum = urlencode($lien_forum);
       echo "<A HREF=\"trace.php?link=$lien_forum\" title =\"$mess_menu_forum $comment_forum\">$mess_menu_forum</A><BR>";
       // Mail à tout le groupe
        $vers = "$mess_mail_app $mess_menu_gestion_grp $nom_grp";
        $message_mail ="<CENTER><Font size=2>$mess_mail_avert $vers </FONT></CENTER>";
        $lien = "mail.php?groupee=1&grp=$id_group&message_mail=$message_mail";
        $lien = urlencode($lien);
        echo "<CENTER><A HREF=\"#\" onclick=\"window.open('trace.php?link=$lien','','width=680,height=520,resizable=yes,status=no')\" title=\"$mess_mail_avert $vers\"".
             " onmouseover=\"img_lettre1.src='images/ecran-annonce/icolettrebw.gif';return true;\"".
             " onmouseout=\"img_lettre1.src='images/ecran-annonce/icolettrew.gif'\">".
             "<IMG NAME=\"img_lettre1\" SRC=\"images/ecran-annonce/icolettrew.gif\" BORDER='0' ALT=\"$mess_mail_avert $vers\"".
             " onLoad=\"tempImg=new Image(0,0); tempImg.src='images/ecran-annonce/icolettrebw.gif'\"></A>";
    }
  }
  echo"</TD></TR></TABLE></TD></TR></TABLE></TD></TR></TABLE></CENTER>";
 exit;
}
if (isset($prem) && $prem == 1 && (!isset($prescription) || (isset($prescription) && $prescription != 1)))
{
  $titre_aff = ($presc == "groupe") ? $mpr_presc_form : $mess_menu_presc;
  include ("style.inc.php");
  echo "<CENTER><TABLE bgColor='#298CA0' cellspacing='2' cellpadding='0' width='420'><TR><TD>";
  echo "<TABLE bgColor='#FFFFFF' cellspacing='1' cellpadding='0' width='100%'>";
  echo "<TR><TD background=\"images/fond_titre_table.jpg\" height='40' align='center' valign='center'><Font size='3' color='#FFFFFF'><B>$titre_aff</B></Font></TD></TR>";
  if (isset($message) && $message !='')
     echo "<TR><TD width='100%'><FONT SIZE='2'><B>$message</B></FONT></TD></TR>";
  echo "<TR><TD width='100%' ><TABLE cellspacing='2' cellpadding='4' border='0' width='100%'>";
  $req = mysql_query("SELECT COUNT(*)  from utilisateur where util_typutil_lb = 'APPRENANT'");
  $nombre_app = mysql_result($req,0);
  $req = mysql_query("SELECT COUNT(*)  from groupe");
  $nombre_grp = mysql_result($req,0);
  if ($nombre_grp > 0)
  {
    if (isset($presc) && $presc != "groupe")
    {
      $lien = "prescription.php?prem=0&prescription=1&presc=$presc&sq=1&rf=1&numero_groupe=$numero_groupe&utilisateur=$utilisateur";
      $lien = urlencode($lien);
      echo "<TR height='45'><TD width='100%' valign='center' bgColor='#F4F4F4'>$bouton_gauche<A href=\"trace.php?link=$lien\" target='main'>$mess_sq_ref</A>$bouton_droite</TD></TR>";
      $lien = "prescription.php?prem=0&prescription=1&presc=$presc&sq=1&rf=0&numero_groupe=$numero_groupe&utilisateur=$utilisateur";
      $lien = urlencode($lien);
      echo "<TR height='45'><TD width='100%' valign='center' bgColor='#DFE3E7'>$bouton_gauche<A href=\"trace.php?link=$lien\" target='main'>$mess_sq_srf</A>$bouton_droite</TD></TR>";
    }
    $lien = "prescription.php?prem=0&prescription=1&presc=$presc&pc=1&rf=1&numero_groupe=$numero_groupe&utilisateur=$utilisateur&grp_actif=$groupe";
    $lien = urlencode($lien);
    echo "<TR height='45'><TD width='100%' valign='center' bgColor='#F4F4F4'>$bouton_gauche<A href=\"trace.php?link=$lien\" target='main'>$mess_prc_ref</A>$bouton_droite</TD></TR>";
    $lien = "prescription.php?prem=0&prescription=1&presc=$presc&pc=1&rf=0&numero_groupe=$numero_groupe&utilisateur=$utilisateur&grp_actif=$groupe";
    $lien = urlencode($lien);
    echo "<TR height='45'><TD width='100%' valign='center' bgColor='#DFE3E7'>$bouton_gauche<A href=\"trace.php?link=$lien\" target='main'>$mess_prc_srf</A>$bouton_droite</TD></TR>";
  }
  else
     echo "<TR><TD width='100%'><FONT SIZE='2'><B>$mpr_nogrp</B></FONT></TD></TR>";
   echo "<TR height='60'><TD align=left><A HREF=\"javascript:history.go(-1);\" onmouseover=\"img_annonce.src='images/fiche_identite/boutretourb.gif';return true;\" onmouseout=\"img_annonce.src='images/fiche_identite/boutretour.gif'\">";
   echo "<IMG NAME=\"img_annonce\" SRC=\"images/fiche_identite/boutretour.gif\" BORDER='0' onLoad=\"tempImg=new Image(0,0); tempImg.src='images/fiche_identite/boutretourb.gif'\"></A></TD>";
  echo "</TR></TABLE></TD></TR></TABLE></TD></TR></TABLE>";
  exit;
}
if (isset($insert_presc) && $insert_presc == 1)
{
  include ("style.inc.php");
  //on doit inverser les champs des dates pour qu'ils soient au format de base mysql
/*  if ($comeback == "liste_gp"){
    $date_fin= date("d/n/Y");
    $date_deb = date("d/n/Y");
    $form_ref = $id_user;
  }else{
*/
  if (isset($_POST['appel_parc']) && $_POST['appel_parc'] != '')
  {
      $tabParc=explode(' id: ',$_POST['appel_parc']);
      $parc = $tabParc[1];
      $tabForm=explode(' id: ',$_POST['appel_form']);
      $form_ref = $tabForm[1];
  }

  if (isset($imonth) && $imonth != 0)
  {
      $date_fin="$imonth/$iday/$iyear";
      $date_deb = "$imonth1/$iday1/$iyear1";
  }
  $ch_datedeb = explode ("/",$date_deb);
  $ch_datefin = explode ("/",$date_fin);
  $date_deb = "$ch_datedeb[2]/$ch_datedeb[1]/$ch_datedeb[0]";
  $date_fin = "$ch_datefin[2]/$ch_datefin[1]/$ch_datefin[0]";
  $nb_date_fin_query = mysql_query ("SELECT TO_DAYS('$date_fin')");
  $nb_dat_fin = mysql_result ($nb_date_fin_query,0);
  $nb_date_deb_query = mysql_query ("SELECT TO_DAYS('$date_deb')");
  $nb_dat_deb = mysql_result ($nb_date_deb_query,0);
  // Gestion des erreurs de saisie ou des champs vides
  if (isset($parc) && $parc > -1)
     $nom_parcours = getdatafield ($connect,"SELECT parcours_nom_lb from parcours where parcours_cdn = $parc","parcours_nom_lb");
  if ($nb_dat_deb > $nb_dat_fin)
    $mess_notif .= $mpr_avert1."<BR>";
  if ($date_deb =="" || $date_fin =="")
    $mess_notif .= $mpr_avert2."<BR>";
  if (($groupe == -1 && !$util) || (!$groupe && $util == -1))
    $mess_notif .= $mpr_avert3."<BR>";
  if (isset($form_ref) && $form_ref == -1)
    $mess_notif .= $mpr_avert6." : $msq_parc => $nom_parcours<BR>";
  if ((isset($parc) && $parc == -1 && !isset($seq)) || (isset($seq) && $seq == -1 && !isset($parc)))
    $mess_notif .= $mpr_avert4."<BR>";
  if (isset($mess_notif) && $mess_notif != "")
      $message_erreur = 1;
//    echo " if ($form_ref != -1 && ($parc > -1 || $seq > -1) && ($groupe != -1 || $util != -1) && ($date_deb !='' && $date_fin !='') && ($nb_dat_deb <= $nb_dat_fin) && (($groupe > -1 && ($parc > -1 || $seq > -1)) || ($util > -1 && ($parc > -1 || $seq > -1)) && $form_ref > -1)){";exit;
  if ($form_ref != -1 &&
     ($parc > -1 || $seq > -1) &&
     ($groupe != -1 || $util != -1) &&
     ($date_deb !='' && $date_fin !='') &&
     ($nb_dat_deb <= $nb_dat_fin) &&
     (($groupe > -1 && ($parc > -1 || $seq > -1)) || ($util > -1 && ($parc > -1 || $seq > -1)) && $form_ref > -1))
  {
     $id_prescripteur=$id_user;
     if (isset($groupe) &&  $groupe != -1 && isset($parc) && $parc != -1)
     {

        $id_classe = getdatafield ($connect,"SELECT grp_classe_on from groupe where grp_cdn = $groupe","grp_classe_on");
        $type_parcours = getdatafield ($connect,"SELECT parcours_type_lb from parcours where parcours_cdn = $parc","parcours_type_lb");
        $seq_parc_query = mysql_query ("SELECT seqparc_seq_no from sequence_parcours where seqparc_parc_no = $parc order by seqparc_cdn" );
        $nb_seq = mysql_num_rows ($seq_parc_query);
        if ($nb_seq == 0)
        {
          $mess_notif .= "<B>$nom_parcours</B> : $mpr_avert9";
          echo "<CENTER><TABLE bgColor='#298CA0' cellspacing='2'><TR><TD>";
          echo "<TABLE bgColor='#FFFFFF' cellspacing='2' width='100%'>";
          echo "<TR><TD background=\"images/fond_titre_table.jpg\" colspan='2' height='34' align='center' valign='center'>";
          echo "<Font size='3' color='#FFFFFF'><B>$mpr_presc_form</B></FONT></TD></TR>";
          echo "<TR><TD align='middle'>";
          echo"<CENTER><FONT SIZE='2'>$mess_notif</FONT></CENTER><P>&nbsp;";
          echo "</TD></TR>";
          echo "<TR><TD align=left><A HREF=\"javascript:history.back();\"  onmouseover=\"img_annonce.src='images/fiche_identite/boutretourb.gif';return true;\" onmouseout=\"img_annonce.src='images/fiche_identite/boutretour.gif'\">";
          echo "<IMG NAME=\"img_annonce\" SRC=\"images/fiche_identite/boutretour.gif\" BORDER='0' onLoad=\"tempImg=new Image(0,0); tempImg.src='images/fiche_identite/boutretourb.gif'\"></A>";
          echo "</TD></TR></TABLE></TD></TR></TABLE>";
          exit;
        }
        $i = 0;
        while ($i < $nb_seq)
        {
          $id_seq = mysql_result ($seq_parc_query,$i,"seqparc_seq_no");
          $type_sequence = getdatafield ($connect,"SELECT seq_type_lb from sequence where seq_cdn = $id_seq","seq_type_lb");
          if ($type_sequence == 'NORMAL')
              $act_query = mysql_query ("SELECT act_cdn from activite where act_seq_no = $id_seq");
          elseif (strstr($type_sequence,'SCORM'))
              $act_query = mysql_query ("SELECT mod_cdn from scorm_module where mod_seq_no = $id_seq");
          $nb_act = mysql_num_rows ($act_query);
          if ($nb_act == 0)
          {
             echo "<CENTER><TABLE bgColor='#298CA0' cellspacing='2' cellpadding='0'><TR><TD>";
             echo "<TABLE bgColor='#FFFFFF' cellspacing='1' cellpadding='0' width='100%'>";
             echo "<TR><TD background=\"images/fond_titre_table.jpg\" colspan='2' height='34' align='center' valign='center'>";
             echo "<Font size='3' color='#FFFFFF'><B>$mpr_presc_form</B></FONT></TD></TR>";
             echo "<TR><TD align='middle'>";
             echo"<CENTER><FONT SIZE='2'>$msq_noact_seq_parc</FONT></CENTER><P>&nbsp;</TD>";
             echo "<TD align=left><A HREF=\"javascript:history.back();\"  onmouseover=\"img_annonce.src='images/fiche_identite/boutretourb.gif';return true;\" onmouseout=\"img_annonce.src='images/fiche_identite/boutretour.gif'\">";
             echo "<IMG NAME=\"img_annonce\" SRC=\"images/fiche_identite/boutretour.gif\" BORDER='0' onLoad=\"tempImg=new Image(0,0); tempImg.src='images/fiche_identite/boutretourb.gif'\"></A></TD>";
             echo "</TR></TABLE></TD></TR></TABLE>";
            exit;
          }
          $i++;
        }
        $nom_grp =GetDataField ($connect,"SELECT grp_nom_lb from groupe where grp_cdn = $groupe","grp_nom_lb");
        $cherche_grp_parc = mysql_query ("SELECT * from groupe_parcours where gp_grp_no = $groupe AND gp_parc_no = $parc");
        $nb_fois = mysql_num_rows ($cherche_grp_parc);
        if ($nb_fois > 0)
        {
          echo "<CENTER><TABLE bgColor='#298CA0' cellspacing='2' cellpadding='0'><TR><TD>";
          echo "<TABLE bgColor='#FFFFFF' cellspacing='1' cellpadding='0' width='100%'>";
          echo "<TR><TD background=\"images/fond_titre_table.jpg\" colspan='2' height='34' align='center' valign='center'>";
          echo "<Font size='3' color='#FFFFFF'><B>$mpr_presc_form</B></FONT></TD></TR>";
          echo "<TR><TD align='middle'>";
          echo "<CENTER><TABLE><TR><TD><font size=2>$nom_parcours : $parc_aff_grp $nom_grp</B></FONT><BR>";
          echo "<font size=2>$list_parc_grp $nom_grp</FONT><BR><UL>";
          $grp_parc = mysql_query ("SELECT gp_parc_no from groupe_parcours where gp_grp_no =$groupe order by gp_ordre_no asc");
          $nb_f = mysql_num_rows ($grp_parc);
          $nnn = 0;
          while ($nnn < $nb_f)
          {
             $num_parc = mysql_result($grp_parc,$nnn,"gp_parc_no");
             $nom_parcours = getdatafield ($connect,"SELECT parcours_nom_lb from parcours where parcours_cdn = $num_parc","parcours_nom_lb");
             echo "<LI><font size=2>$nom_parcours<BR></LI>";
             $nnn++;
          }
          echo "</UL></TD></TR></TABLE></TD></TR>";
          echo "<TR><TD align=left><A HREF=\"javascript:history.back();\"  onmouseover=\"img_annonce.src='images/fiche_identite/boutretourb.gif';return true;\" onmouseout=\"img_annonce.src='images/fiche_identite/boutretour.gif'\">";
          echo "<IMG NAME=\"img_annonce\" SRC=\"images/fiche_identite/boutretour.gif\" BORDER='0' onLoad=\"tempImg=new Image(0,0); tempImg.src='images/fiche_identite/boutretourb.gif'\"></A>";
          echo "</TD></TR></TABLE></TD></TR></TABLE>";
        exit();
       }
       $id_grp_parc = Donne_ID ($connect,"SELECT max(gp_cdn) from groupe_parcours");
       $ordre_grp_parc = Donne_ID ($connect,"SELECT max(gp_ordre_no) from groupe_parcours where gp_grp_no = $groupe");
       $insere_grp_parc = mysql_query ("INSERT INTO groupe_parcours values('$id_grp_parc','$groupe','$parc','$form_ref','$date_deb','$date_fin','$ordre_grp_parc')");
      if ($id_classe == 0)
      {
        $referentiel = GetDataField ($connect,"SELECT parcours_referentiel_no from parcours where parcours_cdn = $parc","parcours_referentiel_no");
        $titre_parc = GetDataField ($connect,"select parcours_nom_lb from parcours where parcours_cdn=$parc","parcours_nom_lb");
        if (!$kaler)
           $id_ref = $referentiel;
        $util_grp_query = mysql_query ("SELECT utilgr_utilisateur_no from utilisateur_groupe where utilgr_groupe_no = $groupe");
        $nb_util = mysql_num_rows ($util_grp_query);
        $j = 0;
        while ($j < $nb_util)
        {
           $id_util = mysql_result ($util_grp_query,$j,"utilgr_utilisateur_no");
          //insertion dans la table inscription
           $seq_parc_query = mysql_query ("SELECT sum(sequence.seq_duree_nb) from sequence,sequence_parcours where sequence_parcours.seqparc_parc_no = $parc and  sequence.seq_cdn = sequence_parcours.seqparc_seq_no");
           $duree_parc = mysql_result($seq_parc_query,0);
           $cherche_util = mysql_query ("SELECT suiv3_parc_no from suivi3_$groupe where suiv3_utilisateur_no = $id_util AND suiv3_parc_no=$parc AND suiv3_grp_no = $groupe");
           $nb_fois = mysql_num_rows ($cherche_util);
           if ($nb_fois == 0)
           {
             $id_suivi3 = Donne_ID ($connect,"SELECT max(suiv3_cdn) from suivi3_$groupe");
             $ins_suivi3 = mysql_query ("INSERT into suivi3_$groupe values ($id_suivi3,$id_util,$parc,'A FAIRE',$duree_parc,$groupe)");
           }
          $j++;
        }  //fin while ($j != $nb_util)
        $titre_parc = getdatafield ($connect,"SELECT parcours_nom_lb from parcours where parcours_cdn = $parc","parcours_nom_lb");
        $seq_parc_query = mysql_query ("SELECT seqparc_seq_no,seqparc_ordre_no from sequence_parcours,sequence where
                                        sequence_parcours.seqparc_parc_no = $parc and
                                        sequence_parcours.seqparc_seq_no = sequence.seq_cdn and
                                        sequence.seq_type_on = '0'
                                        order by sequence_parcours.seqparc_cdn");
        $nb_seq = mysql_num_rows ($seq_parc_query);
        $seq_parc_compare = mysql_query ("SELECT seqparc_seq_no,seqparc_ordre_no from sequence_parcours where
                                        sequence_parcours.seqparc_parc_no = $parc");
        $nb_seq_compare = mysql_num_rows ($seq_parc_compare);
        if ($nb_seq < $nb_seq_compare)
           $mess_notif .= "$nb_seq/$nb_seq_compare $msgrp_seq_presc<br />";
               $i = 0;
               $ajout_parc_util = array();
               while ($i != $nb_seq)
               {
                      $j = 0;
                      $id_seq = mysql_result ($seq_parc_query,$i,"seqparc_seq_no");
                      $type_seq = GetDataField ($connect,"SELECT seq_type_on from sequence where seq_cdn = '$id_seq'","seq_type_on");
                      $type_sequence = GetDataField ($connect,"SELECT seq_type_lb from sequence where seq_cdn = '$id_seq'","seq_type_lb");
                      $ordre_seq = mysql_result ($seq_parc_query,$i,"seqparc_ordre_no");
                      $nom_seq = getdatafield ($connect,"SELECT seq_titre_lb from sequence where seq_cdn = $id_seq","seq_titre_lb");
                      while ($j != $nb_util)
                      {
                         $id_util = mysql_result ($util_grp_query,$j,"utilgr_utilisateur_no");
                         // chercher le responsable de formation qui a inscrit cet apprenant pour en faire son prescripteur
                         //$id_prescripteur = GetDataField ($connect,"SELECT util_auteur_no  from utilisateur where util_cdn = $id_util","util_auteur_no");
                         // ou inscrire comme prescripteur le responsable du groupe
                         $nom_app = GetDataField ($connect,"SELECT util_nom_lb  from utilisateur where util_cdn = $id_util","util_nom_lb");
                         $prenom_app = GetDataField ($connect,"SELECT util_prenom_lb from utilisateur where util_cdn = $id_util","util_prenom_lb");
                         $cherche_util = mysql_query ("SELECT presc_utilisateur_no from prescription_$groupe where presc_seq_no = $id_seq AND presc_parc_no = $parc AND presc_utilisateur_no = $id_util AND presc_grp_no = '$groupe'");
                         $nb_fois = mysql_num_rows ($cherche_util);
                         if ($nb_fois == 0 && $type_seq == 0)
                         {
                           $ajout_parc_util[$util]++;
                           $id_presc = Donne_ID ($connect,"SELECT max(presc_cdn) from prescription_$groupe");
                           $ins_presc = mysql_query ("insert into prescription_$groupe values ($id_presc,$id_seq,$parc,$id_util,'$date_deb','$date_fin',$id_prescripteur,$form_ref,$groupe,$ordre_grp_parc)");
                           $inscripteur = GetDataField ($connect,"select util_auteur_no from utilisateur where util_cdn='$id_util'","util_auteur_no");
                           if ($id_prescripteur == $inscripteur)
                             $qualite = $msq_prescripteur;
                           elseif ($inscripteur == $id_user)
                             $qualite = $mess_inscripteur;
                           elseif ($id_prescripteur != $inscripteur && $inscripteur != $id_user)
                             $qualite = $mess_typ_adm;
                           $action_fiche = $mess_menu_presc;
                           $commentaire = $mess_presc_fiche." ".$nom_seq."\n $msq_parc : $titre_parc" ;
                           $new_fiche = Donne_ID ($connect,"select max(fiche_cdn) from fiche_suivi");
                           $req_fiche = mysql_query("INSERT INTO fiche_suivi (fiche_cdn,fiche_utilisateur_no,fiche_auteur_no,fiche_qualite_lb,fiche_date_dt,fiche_heure_dt,fiche_commentaire_cmt,fiche_grp_no,fiche_parc_no,fiche_seq_no,fiche_act_no,fiche_typaction_lb) VALUES($new_fiche,$id_util,$id_user,'$qualite','$date_fiche','$heure_fiche',\"$commentaire\",$groupe,$parc,$id_seq,0,\"$action_fiche\")");

                           //Lors de prescription, activite et seq sont à faire
                           $duree_sequence = GetDataField ($connect,"SELECT seq_duree_nb from sequence where seq_cdn = '$id_seq'","seq_duree_nb");
                           $id_suivi2 = Donne_ID ($connect,"SELECT max(suiv2_cdn) from suivi2_$groupe");
                           $ins_suivi2 = mysql_query ("insert into suivi2_$groupe values ($id_suivi2,$id_util,$id_seq,'A FAIRE',$duree_sequence,$ordre_seq,$groupe)");
                           if (!strstr($type_sequence,"SCORM"))
                           {
                              //Selection des activites de la sequence
                              $act_query = mysql_query ("SELECT * from activite where act_seq_no = $id_seq");
                              $nb_act = mysql_num_rows ($act_query);
                              $k = 0;
                              while ($k != $nb_act)
                              {
                                $act = mysql_result ($act_query,$k,"act_cdn");
                                $ress = mysql_result ($act_query,$k,"act_ress_no");
                                $ress_on = mysql_result ($act_query,$k,"act_ress_on");
                                $id_suivi = Donne_ID ($connect,"SELECT max(suivi_cdn) from suivi1_$groupe");
                                if ($ress == 0 && $ress_on == 'OUI')
                                  $ins_suivi = mysql_query ("INSERT INTO suivi1_$groupe(suivi_cdn,suivi_utilisateur_no,suivi_act_no,suivi_grp_no,suivi_etat_lb) values ($id_suivi,$id_util,$act,$groupe,'A FAIRE')");
                                elseif ($ress == 0 && $ress_on == 'NON')
                                  $ins_suivi = mysql_query ("INSERT INTO suivi1_$groupe(suivi_cdn,suivi_utilisateur_no,suivi_act_no,suivi_grp_no,suivi_etat_lb) values ($id_suivi,$id_util,$act,$groupe,'PRESENTIEL')");
                                else
                                  $ins_suivi = mysql_query ("INSERT INTO suivi1_$groupe(suivi_cdn,suivi_utilisateur_no,suivi_act_no,suivi_grp_no,suivi_etat_lb) values ($id_suivi,$id_util,$act,$groupe,'A FAIRE')");
                              $k++;
                              } //fin while ($k != $nb_act)
                           }
                           elseif (strstr($type_sequence,"SCORM"))
                           {
                              $mod_query = mysql_query ("SELECT * from scorm_module where mod_seq_no = $id_seq order by mod_cdn");
                              $nb_mod = mysql_num_rows ($mod_query);
                              $k = 0;
                              while ($k != $nb_mod)
                              {
                                $id_mod= mysql_result ($mod_query,$k,"mod_cdn");
                                $id_suivi = Donne_ID ($connect,"SELECT max(user_module_cdn) from scorm_util_module_$groupe");
                                $ins_suivi = mysql_query ("INSERT INTO scorm_util_module_$groupe (user_module_cdn,user_module_no,mod_module_no,mod_grp_no) values ($id_suivi,$id_util,$id_mod,$groupe)");
                               $k++;
                              } //fin while ($k != $nb_act)
                           }
                         }
                         if (($i+1) == $nb_seq)
                         {
                           if ($ajout_parc_util[$util] < $nb_seq)
                             $mess_notif .= "$seq_parc_aff_app $prenom_app $nom_app $seq_parc_aff_app1<BR>";
                           elseif ($ajout_parc_util[$util] == 0)
                           {
                             $del_suivi3 = mysql_query ("DELETE from suivi3_$groupe where suiv3_parc_no = $parc AND suiv3_utilisateur_no = $id_util AND suiv3_grp_no =  $numero_groupe");
                           }
                         }
                      $j++;
                      } //fin while ($j != $nb_util)
                      if ($type_seq == 1)
                      {
                            $commentaire = $mess_nopresc_fiche." <U>".$nom_seq."</U>--> $msq_parc : $titre_parc" ;
                            $mess_notif .= addslashes($commentaire)."<BR>";
                      }
               $i++;
               }  //fin while ($i != $nb_seq)
              $mess_notif .= "Le module choisi $seq_aff_grp $nom_grp<BR>";
           }//if ($id_classe == 0)
        }  //fin if ($grp == 'on' && $parcours == 'on')

    if ($util > -1 && $parc > -1)
    {
       $nom_app = GetDataField ($connect,"SELECT util_nom_lb  from utilisateur where util_cdn = $util","util_nom_lb");
       $prenom_app = GetDataField ($connect,"SELECT util_prenom_lb  from utilisateur where util_cdn = $util","util_prenom_lb");
       $nom_parcours = getdatafield ($connect,"SELECT parcours_nom_lb from parcours where parcours_cdn = $parc","parcours_nom_lb");
       $type_parcours = getdatafield ($connect,"SELECT parcours_type_lb from parcours where parcours_cdn = $parc","parcours_type_lb");
       $cherche_util = mysql_query ("SELECT count(*) from prescription_$numero_groupe where presc_parc_no = $parc AND presc_utilisateur_no = $util AND presc_grp_no = $numero_groupe");
       $nb_fois = mysql_result ($cherche_util,0);
       if ($nb_fois > 0)
       {
          echo "<CENTER><TABLE bgColor='#298CA0' cellspacing='2' cellpadding='0'><TR><TD>";
          echo "<TABLE bgColor='#FFFFFF' cellspacing='1' cellpadding='0' width='100%'>";
          echo "<TR><TD background=\"images/fond_titre_table.jpg\" colspan='2' height='34' align='center' valign='center'>";
          echo "<Font size='3' color='#FFFFFF'><B>$mpr_presc_form</B></FONT></TD></TR>";
          echo "<TR><TD>&nbsp;<P><FONT SIZE='2'>$mpr_app_insc</FONT></CENTER><P>";
          echo "</TD></TR></TABLE></TD></TR></TABLE>";
        exit;
       }
       // il faut introduire ici la valeur du groupe en $numero_groupe
       $cherche_grp = mysql_query ("SELECT count(*) from utilisateur_groupe where utilgr_utilisateur_no = $util");
       $nb_fois = mysql_result ($cherche_grp,0);
       if ($nb_fois == 0)
       {
          echo "<CENTER><TABLE bgColor='#298CA0' cellspacing='2' cellpadding='0'><TR><TD>";
          echo "<TABLE bgColor='#FFFFFF' cellspacing='1' cellpadding='0' width='100%'>";
          echo "<TR><TD background=\"images/fond_titre_table.jpg\" colspan='2' height='34' align='center' valign='center'>";
          echo "<Font size='3' color='#FFFFFF'><B>$mpr_presc_form</B></FONT></TD></TR>";
          echo "<TR><TD>&nbsp;<P><FONT SIZE='2'>$mpr_app_insc_grp</FONT></CENTER><P>";
          echo "</TD></TR></TABLE></TD></TR></TABLE>";
          exit;
       }
        $seq_parc_query = mysql_query ("SELECT seqparc_seq_no from sequence_parcours where seqparc_parc_no = $parc order by seqparc_cdn" );
        $nb_seq = mysql_num_rows ($seq_parc_query);
        if ($nb_seq == 0)
        {
          $mess_notif .= "<B>$nom_parcours</B> : $mpr_avert9";
          echo "<CENTER><TABLE bgColor='#298CA0' cellspacing='2' cellpadding='0'><TR><TD>";
          echo "<TABLE bgColor='#FFFFFF' cellspacing='1' cellpadding='0' width='100%'>";
          echo "<TR><TD background=\"images/fond_titre_table.jpg\" colspan='2' height='34' align='center' valign='center'>";
          echo "<Font size='3' color='#FFFFFF'><B>$mpr_presc_form</B></FONT></TD></TR>";
          echo "<TR><TD>&nbsp;<P><FONT SIZE='2'>$mess_notif</FONT></CENTER><P>";
          echo "</TD></TR></TABLE></TD></TR></TABLE>";
        }
        $i = 0;
        while ($i != $nb_seq)
        {
          $id_seq = mysql_result ($seq_parc_query,$i,"seqparc_seq_no");
          $type_sequence = GetDataField ($connect,"SELECT seq_type_lb from sequence where seq_cdn = '$id_seq'","seq_type_lb");
          if (strstr($type_sequence,"SCORM"))
             $scormOk = 1;
          else
             $scormOk = 0;
          if ($scormOk == 1)
             $nb_act = mysql_num_rows(mysql_query ("SELECT * from scorm_module where mod_seq_no = $id_seq order by mod_cdn"));
           else
             $nb_act = mysql_num_rows(mysql_query("SELECT * from activite where act_seq_no = $id_seq"));
          if ($nb_act == 0)
          {
                echo "<CENTER><TABLE bgColor='#298CA0' cellspacing='2' cellpadding='0'><TR><TD>";
            echo "<TABLE bgColor='#FFFFFF' cellspacing='1' cellpadding='0' width='100%'>";
            echo "<TR><TD background=\"images/fond_titre_table.jpg\" colspan='2' height='34' align='center' valign='center'>";
            echo "<Font size='3' color='#FFFFFF'><B>$mpr_presc_form</B></FONT></TD></TR>";
            echo "<TR><TD>&nbsp;<P><FONT SIZE='2'>$msq_noact_seq_parc</FONT></CENTER><P>";
            echo "</TD></TR></TABLE></TD></TR></TABLE>";
          }
          $i++;
        }
        $num_parc_der = Donne_ID($connect,"SELECT max(presc_ordre_no) from prescription_$numero_groupe WHERE presc_utilisateur_no = $util");
        $seq_parc_query = mysql_query ("SELECT sum(sequence.seq_duree_nb) from sequence,sequence_parcours where sequence_parcours.seqparc_parc_no = $parc and  sequence.seq_cdn = sequence_parcours.seqparc_seq_no");
        $duree_parc = mysql_result($seq_parc_query,0);
        $cherche_util = mysql_query ("SELECT suiv3_parc_no from suivi3_$numero_groupe where suiv3_utilisateur_no = $util and  suiv3_parc_no = $parc and suiv3_grp_no = $numero_groupe");
        $nb_fois = mysql_num_rows ($cherche_util);
        if ($nb_fois == 0)
        {
          $id_suivi3 = Donne_ID ($connect,"SELECT max(suiv3_cdn) from suivi3_$numero_groupe");
          $ins_suivi3 = mysql_query ("INSERT INTO suivi3_$numero_groupe values ($id_suivi3,$util,$parc,'A FAIRE',$duree_parc,$numero_groupe)");
        }
        $seq_parc_query = mysql_query ("SELECT seqparc_seq_no,seqparc_ordre_no from sequence_parcours where seqparc_parc_no = $parc order by seqparc_cdn" );
        $nb_seq = mysql_num_rows ($seq_parc_query);

        $i = 0;
        while ($i != $nb_seq)
        {
          $id_seq = mysql_result ($seq_parc_query,$i,"seqparc_seq_no");
          $nom_seq = getdatafield ($connect,"SELECT seq_titre_lb from sequence where seq_cdn = $id_seq","seq_titre_lb");
          $ordre_seq = mysql_result ($seq_parc_query,$i,"seqparc_ordre_no");
          $type_sequence = GetDataField ($connect,"SELECT seq_type_lb from sequence where seq_cdn = '$id_seq'","seq_type_lb");
          if (strstr($type_sequence,"SCORM"))
             $scormOk = 1;
          else
             $scormOk = 0;
          $type_seq = GetDataField ($connect,"SELECT seq_type_on from sequence where seq_cdn = '$id_seq'","seq_type_on");
          $referentiel = GetDataField ($connect,"SELECT seqref_referentiel_no from sequence_referentiel where seqref_seq_no = $id_seq","seqref_referentiel_no");
          if (!$kaler)
             $id_ref = $referentiel;
          $cherche_util = mysql_query ("SELECT presc_utilisateur_no from prescription_$numero_groupe where presc_seq_no = $id_seq AND presc_utilisateur_no = $util AND presc_grp_no = $numero_groupe");
          $nb_fois = mysql_num_rows ($cherche_util);
          if ($nb_fois == 0 && $type_seq == 0)
          {
               $compte_seq_ajout++;
               $duree_sequence = GetDataField ($connect,"SELECT seq_duree_nb from sequence where seq_cdn = '$id_seq'","seq_duree_nb");
               $id_presc = Donne_ID ($connect,"SELECT max(presc_cdn) from prescription_$numero_groupe");
               $ins_presc = mysql_query ("INSERT INTO prescription_$numero_groupe values ($id_presc,$id_seq,$parc,$util,'$date_deb','$date_fin',$id_user,$form_ref,$numero_groupe,$num_parc_der)");
               //informations de suivi
               $id_suivi2 = Donne_ID ($connect,"SELECT max(suiv2_cdn) from suivi2_$numero_groupe");
               $ins_suivi2 = mysql_query ("insert into suivi2_$numero_groupe values ($id_suivi2,$util,$id_seq,'A FAIRE',$duree_sequence,$ordre_seq,$numero_groupe)");
               $inscripteur = GetDataField ($connect,"select util_auteur_no from utilisateur where util_cdn='$util'","util_auteur_no");
               if ($id_prescripteur == $inscripteur)
                  $qualite = $msq_prescripteur;
               elseif ($inscripteur == $id_user)
                  $qualite = $mess_inscripteur;
               elseif ($id_prescripteur != $inscripteur && $inscripteur != $id_user)
                  $qualite = $mess_typ_adm;
               $action_fiche = $mess_menu_presc;
               $commentaire = $mess_presc_fiche." ".$nom_seq."\n $msq_parc : $nom_parcours" ;
               $new_fiche = Donne_ID ($connect,"select max(fiche_cdn) from fiche_suivi");
               $req_fiche = mysql_query("INSERT INTO fiche_suivi (fiche_cdn,fiche_utilisateur_no,fiche_auteur_no,fiche_qualite_lb,fiche_date_dt,fiche_heure_dt,fiche_commentaire_cmt,fiche_grp_no,fiche_parc_no,fiche_seq_no,fiche_act_no,fiche_typaction_lb) VALUES($new_fiche,$util,$id_user,'$qualite','$date_fiche','$heure_fiche',\"$commentaire\",$numero_groupe,$parc,$id_seq,0,\"$action_fiche\")");
               //Selection des activites de la sequence
               if ($scormOk == 1)
               {
                  $mod_query = mysql_query ("SELECT * from scorm_module where mod_seq_no = $id_seq order by mod_cdn");
                  $nb_mod = mysql_num_rows ($mod_query);
                  $k = 0;
                  while ($k != $nb_mod)  {
                      $id_mod= mysql_result ($mod_query,$k,"mod_cdn");
                      $id_suivi = Donne_ID ($connect,"SELECT max(user_module_cdn) from scorm_util_module_$numero_groupe");
                      $ins_suivi = mysql_query ("INSERT into scorm_util_module_$numero_groupe (user_module_cdn,user_module_no,mod_module_no,mod_grp_no) values ($id_suivi,$util,$id_mod,$numero_groupe)");
                    $k++;
                  } //fin while ($k != $nb_act)
               }else{
                  $act_query = mysql_query ("SELECT * from activite where act_seq_no = $id_seq");
                  $nb_act = mysql_num_rows ($act_query);
                  $k = 0;
                  while ($k != $nb_act)  {
                      $act = mysql_result ($act_query,$k,"act_cdn");
                      $ress = mysql_result ($act_query,$k,"act_ress_no");
                      $ress_on = mysql_result ($act_query,$k,"act_ress_on");
                      $id_suivi = Donne_ID ($connect,"SELECT max(suivi_cdn) from suivi1_$numero_groupe");
                      if ($ress == 0 && $ress_on == 'OUI')
                         $ins_suivi = mysql_query ("insert into suivi1_$numero_groupe(suivi_cdn,suivi_utilisateur_no,suivi_act_no,suivi_grp_no,suivi_etat_lb) values ($id_suivi,$util,$act,$numero_groupe,'A FAIRE')");
                      elseif ($ress == 0 && $ress_on == 'NON')
                         $ins_suivi = mysql_query ("insert into suivi1_$numero_groupe(suivi_cdn,suivi_utilisateur_no,suivi_act_no,suivi_grp_no,suivi_etat_lb) values ($id_suivi,$util,$act,$numero_groupe,'PRESENTIEL')");
                      else
                         $ins_suivi = mysql_query ("insert into suivi1_$numero_groupe(suivi_cdn,suivi_utilisateur_no,suivi_act_no,suivi_grp_no,suivi_etat_lb) values ($id_suivi,$util,$act,$numero_groupe,'A FAIRE')");
                   $k++;
                  } //fin while ($k != $nb_act)
               }
           }
        $i++;
        }
               //insertion dans la table inscription
        $ref_parc = GetDataField ($connect,"SELECT parcours_referentiel_no from parcours where parcours_cdn = $parc","parcours_referentiel_no");
        if ($compte_seq_ajout == 0)
        {
           $mess_notif .= $tt_seq_dej_presc;
           $eff_parc_insc = mysql_query("DELETE from suivi3_$numero_groupe where suiv3_parc_no = $parc AND suiv3_utilisateur_no = $util and suiv3_grp_no=$numero_groupe");
        }elseif ($compte_seq_ajout < $nb_seq && $nb_seq > 1)
           $mess_notif .= "$une_seq_dej_presc<BR>$nom_parcours : $seq_aff_app $prenom_app $nom_app";
        elseif ($compte_seq_ajout < $nb_seq && $nb_seq == 1)
           $mess_notif .= "$une_seq_dej_presc<BR>$nom_parcours : $parc_mono_seq";
        else
           $mess_notif .= "$nom_parcours : $seq_aff_app $prenom_app $nom_app";
    }    //fin if ($user == 'on' && $parcours == 'on')

    if ($util > -1 && $seq > -1)
    {
       $type_sequence = GetDataField ($connect,"SELECT seq_type_lb from sequence where seq_cdn = '$seq'","seq_type_lb");
       if (strstr($type_sequence,"SCORM"))
          $scormOk = 1;
       else
          $scormOk = 0;
       if ($scormOk == 1)
          $act_query = mysql_query ("SELECT * from scorm_module where mod_seq_no = $seq order by mod_cdn");
       else
          $act_query = mysql_query ("SELECT * from activite where act_seq_no = $seq");
       $nb_act = mysql_num_rows ($act_query);
       if ($nb_act == 0)
       {
          echo "<CENTER><TABLE bgColor='#298CA0' cellspacing='2' cellpadding='0'><TR><TD>";
          echo "<TABLE bgColor='#FFFFFF' cellspacing='1' cellpadding='0' width='100%'>";
          echo "<TR><TD background=\"images/fond_titre_table.jpg\" colspan='2' height='34' align='center' valign='center'>";
          echo "<Font size='3' color='#FFFFFF'><B>$mpr_presc_form</B></FONT></TD></TR>";
          echo "<TR><TD>&nbsp;<P><FONT SIZE='2'>$msq_noact_seq</FONT></CENTER><P>";
          echo "</TD></TR></TABLE></TD></TR></TABLE>";
       exit;
       }
       $nom_app = GetDataField ($connect,"SELECT util_nom_lb  from utilisateur where util_cdn = $util","util_nom_lb");
       $prenom_app = GetDataField ($connect,"SELECT util_prenom_lb  from utilisateur where util_cdn = $util","util_prenom_lb");
       $nom_sequence = getdatafield ($connect,"SELECT seq_titre_lb from sequence where seq_cdn = $seq","seq_titre_lb");
       $parc=0;
       $cherche_util = mysql_query ("SELECT presc_utilisateur_no from prescription_$numero_groupe where presc_seq_no = $seq AND presc_utilisateur_no = $util");
       $nb_fois = mysql_num_rows ($cherche_util);
       if ($nb_fois > 0)
       {
          echo "<CENTER><TABLE bgColor='#298CA0' cellspacing='2' cellpadding='0'><TR><TD>";
          echo "<TABLE bgColor='#FFFFFF' cellspacing='1' cellpadding='0' width='100%'>";
          echo "<TR><TD background=\"images/fond_titre_table.jpg\" colspan='2' height='34' align='center' valign='center'>";
          echo "<Font size='3' color='#FFFFFF'><B>$mpr_presc_form</B></FONT></TD></TR>";
          echo "<TR><TD>&nbsp;<P><FONT SIZE='2'>$mpr_app_insc</FONT></CENTER><P>";
          echo "</TD></TR></TABLE></TD></TR></TABLE>";
       exit;
       }
       $cherche_grp = mysql_query ("SELECT count(*) from utilisateur_groupe where utilgr_utilisateur_no = $util");
       $nb_fois = mysql_result ($cherche_grp,0);
       if ($nb_fois == 0){
          echo "<CENTER><TABLE bgColor='#298CA0' cellspacing='2' cellpadding='0'><TR><TD>";
          echo "<TABLE bgColor='#FFFFFF' cellspacing='1' cellpadding='0' width='100%'>";
          echo "<TR><TD background=\"images/fond_titre_table.jpg\" colspan='2' height='34' align='center' valign='center'>";
          echo "<Font size='3' color='#FFFFFF'><B>$mpr_presc_form</B></FONT></TD></TR>";
          echo "<TR><TD>&nbsp;<P><FONT SIZE='2'>$mpr_app_insc_grp</FONT></CENTER><P>";
          echo "</TD></TR></TABLE></TD></TR></TABLE>";
       exit;
       }
        $duree_sequence = GetDataField ($connect,"SELECT seq_duree_nb from sequence where seq_cdn = '$seq'","seq_duree_nb");
        $seq_parc_query = mysql_query ("SELECT sum(sequence.seq_duree_nb) from sequence,prescription_$numero_groupe where presc_parc_no = $parc and seq_cdn = presc_seq_no");
        $duree_parc_exist = mysql_result($seq_parc_query,0);
        $duree_parc = $duree_parc_exist + $duree_sequence;
        // traitement new_presc
        $parc_zero = mysql_query("select count(*) from prescription_$numero_groupe where presc_parc_no = 0 AND presc_grp_no = $numero_groupe and presc_utilisateur_no = $util");
        $nb_parc_zero = mysql_result($parc_zero,0);
        if ($nb_parc_zero == 0)
          $ordre_seq = Donne_ID ($connect,"select max(presc_ordre_no) from prescription_$numero_groupe where presc_utilisateur_no=$util AND presc_parc_no = 0");
        else
          $ordre_seq = GetDataField ($connect,"SELECT presc_ordre_no from prescription_$numero_groupe where presc_utilisateur_no=$util AND presc_grp_no = $numero_groupe AND presc_parc_no = 0","presc_ordre_no");
        $id_suivi3 = Donne_ID ($connect,"SELECT max(suiv3_cdn) from suivi3_$numero_groupe");
        $referentiel = GetDataField ($connect,"SELECT seqref_referentiel_no from sequence_referentiel where seqref_seq_no = $seq","seqref_referentiel_no");
        if (!$kaler)
           $id_ref = $referentiel;
        $id_presc = Donne_ID ($connect,"SELECT max(presc_cdn) from prescription_$numero_groupe");
        $ins_presc = mysql_query ("insert into prescription_$numero_groupe values ($id_presc,$seq,$parc,$util,'$date_deb','$date_fin',$id_user,$form_ref,$numero_groupe,'$ordre_seq')");
        // fin
        $cherche_parc = mysql_query ("SELECT suiv3_parc_no from suivi3_$numero_groupe where suiv3_parc_no = 0 and suiv3_utilisateur_no = $util");
        $nb = mysql_num_rows ($cherche_parc);
           if ($nb == 0)
           {
              $id_suivi3 = Donne_ID ($connect,"SELECT max(suiv3_cdn) from suivi3_$numero_groupe");
              $ins_suivi3 = mysql_query ("insert into suivi3_$numero_groupe values ($id_suivi3,$util,$parc,'A FAIRE',$duree_parc,$numero_groupe)");
           }
           else
           {
              $ins_suivi3 = mysql_query ("update suivi3_$numero_groupe set suiv3_duree_nb = $duree_parc where suiv3_utilisateur_no=$util and suiv3_parc_no=0 AND suiv3_grp_no = $numero_groupe");
              $parc = 0;
           }
           $id_suivi2 = Donne_ID ($connect,"SELECT max(suiv2_cdn) from suivi2_$numero_groupe");
           $ins_suivi2 = mysql_query ("insert into suivi2_$numero_groupe values ($id_suivi2,$util,$seq,'A FAIRE',$duree_sequence,$ordre_seq,$numero_groupe)");
           $inscripteur = GetDataField ($connect,"select util_auteur_no from utilisateur where util_cdn='$util'","util_auteur_no");
           if ($id_prescripteur == $inscripteur)
              $qualite = $msq_prescripteur;
           elseif ($inscripteur == $id_user)
              $qualite = $mess_inscripteur;
           elseif ($id_prescripteur != $inscripteur && $inscripteur != $id_user)
              $qualite = $mess_typ_adm;
           $action_fiche = $mess_aff_forma;
           $commentaire = $mess_presc_fiche." : ".$nom_sequence."\n $msq_parc : $mess_gp_seq_hors_parc" ;
           $new_fiche = Donne_ID ($connect,"select max(fiche_cdn) from fiche_suivi");
           $req_fiche = mysql_query("INSERT INTO fiche_suivi (fiche_cdn,fiche_utilisateur_no,fiche_auteur_no,fiche_qualite_lb,fiche_date_dt,fiche_heure_dt,fiche_commentaire_cmt,fiche_grp_no,fiche_parc_no,fiche_seq_no,fiche_act_no,fiche_typaction_lb) VALUES($new_fiche,$util,$id_user,'$qualite','$date_fiche','$heure_fiche',\"$commentaire\",$numero_groupe,0,$seq,0,\"$action_fiche\")");
           //Selection des activites de la sequence
           if ($scormOk == 1)
           {
                  $mod_query = mysql_query ("SELECT * from scorm_module where mod_seq_no = $seq order by mod_cdn");
                  $nb_mod = mysql_num_rows ($mod_query);
                  $k = 0;
                  while ($k != $nb_mod)  {
                      $id_mod= mysql_result ($mod_query,$k,"mod_cdn");
                      $id_suivi = Donne_ID ($connect,"SELECT max(user_module_cdn) from scorm_util_module_$numero_groupe");
                      $ins_suivi = mysql_query ("insert into scorm_util_module_$numero_groupe (user_module_cdn,user_module_no,mod_module_no,mod_grp_no) values ($id_suivi,$util,$id_mod,$numero_groupe)");
                    $k++;
                  } //fin while ($k != $nb_act)
           }
           else
           {
                  $act_query = mysql_query ("SELECT * from activite where act_seq_no = $seq");
                  $nb_act = mysql_num_rows ($act_query);
                  $k = 0;
                  while ($k != $nb_act)
                  {
                      $act = mysql_result ($act_query,$k,"act_cdn");
                      $ress = mysql_result ($act_query,$k,"act_ress_no");
                      $ress_on = mysql_result ($act_query,$k,"act_ress_on");
                      $id_suivi = Donne_ID ($connect,"SELECT max(suivi_cdn) from suivi1_$numero_groupe");
                      if ($ress == 0 && $ress_on == 'OUI')
                         $ins_suivi = mysql_query ("insert into suivi1_$numero_groupe (suivi_cdn,suivi_utilisateur_no,suivi_act_no,suivi_grp_no,suivi_etat_lb) values ($id_suivi,$util,$act,$numero_groupe,'A FAIRE')");
                      elseif ($ress == 0 && $ress_on == 'NON')
                         $ins_suivi = mysql_query ("insert into suivi1_$numero_groupe (suivi_cdn,suivi_utilisateur_no,suivi_act_no,suivi_grp_no,suivi_etat_lb) values ($id_suivi,$util,$act,$numero_groupe,'PRESENTIEL')");
                      else
                         $ins_suivi = mysql_query ("insert into suivi1_$numero_groupe (suivi_cdn,suivi_utilisateur_no,suivi_act_no,suivi_grp_no,suivi_etat_lb) values ($id_suivi,$util,$act,$numero_groupe,'A FAIRE')");
                   $k++;
                  } //fin while ($k != $nb_act)
           }
           $mess_notif .= "$nom_sequence : $seq_aff_app $prenom_app $nom_app ";
        } //fin if ($user == 'on' ...)
  }
  if ($comeback == "liste_gp")
  {
     $lien = "modif_gp.php?id_grp=$groupe&mess_notif=$mess_notif";
     $lien = urlencode($lien);
     echo "<script language=\"JavaScript\">";
     echo "document.location.replace(\"trace.php?link=$lien\")";
     echo "</script>";
     exit();
  }
  else
  {
     $lien = "modif_parc.php?a_faire=1&numero_groupe=$numero_groupe&utilisateur=$utilisateur&mess_notif=$mess_notif";
     $lien = urlencode($lien);
     echo "<script language=\"JavaScript\">";
     echo "document.location.replace(\"trace.php?link=$lien\")";
     echo "</script>";
     exit();
  }

}
//fin if ($insert_presc == 1)
if ($prescription == 1)
{
 include ("style.inc.php");
 if ($typ_user == "RESPONSABLE_FORMATION")
 {
   $req_grp = mysql_query("SELECT grp_cdn from groupe where grp_resp_no = $id_user");
   $ngr = mysql_num_rows($req_grp);
   if ($ngr == 0)
   {
      echo "<CENTER><TABLE bgColor='#298CA0' cellspacing='2' cellpadding='0'><TR><TD>";
      echo "<TABLE bgColor='#FFFFFF' cellspacing='1' cellpadding='0' width='100%'>";
      echo "<TR><TD background=\"images/fond_titre_table.jpg\" colspan='2' height='34' align='center' valign='center'>";
      echo "<Font size='3' color='#FFFFFF'><B>$mpr_presc_form</B></FONT></TD></TR>";
      echo "<TR><TD>&nbsp;<P><FONT SIZE='2'>$mpr_no_resp</FONT></CENTER><P>";
      echo "</TD></TR></TABLE></TD></TR></TABLE>";
   exit;
   }
 }
if (isset($mess_notif) && $mess_notif != '')
{
 /*
    ?>
     <script type="text/javascript">
        $(function(){
           $('div.cms').corner("round 10px");
        });
     </script>
   <?php
 */
   echo "<div id=\"mien\" class=\"cms\" style=\"padding:2px;\" onClick=\"javascript:\$(document).ready(function() {".
        "if (\$.browser.msie) {\$('div.cms').hide();}else{\$('div.cms').hide('slow');}})\" ".
        "title=\"$mess_clkF\">".stripslashes($mess_notif);
   echo "</div>";
}
 echo "<CENTER><TABLE bgColor='#298CA0' cellspacing='2' cellpadding='0' width='800'><TR><TD>";
 echo "<TABLE bgColor='#FFFFFF' cellspacing='1' cellpadding='0' width='100%'>";
 echo "<TR><TD background=\"images/fond_titre_table.jpg\" colspan='2' height='40' align='center' valign='center'>";
 echo "<Font size='3' color='#FFFFFF'><B>$mpr_titre</B></FONT></TD></TR>";
 if ($insert_presc == 1 && $kaler == 1)
 {
    $nom_referentiel = GetDataField ($connect,"SELECT ref_desc_cmt  from referentiel where ref_cdn = $id_ref","ref_desc_cmt");
    $niv_ref = GetDataField ($connect,"SELECT ref_denom_lb  from referentiel where ref_cdn = '$id_ref'","ref_denom_lb");
    $nom_abr = GetDataField ($connect,"SELECT ref_nomabrege_lb from referentiel where ref_cdn = '$id_ref'","ref_nomabrege_lb");
    if ($nom_referentiel == ""){
      $nom_referentiel = GetDataField ($connect,"SELECT ref_nomabrege_lb from referentiel where ref_cdn = $id_ref","ref_nomabrege_lb");
    }
    echo"<TR><TD nowrap><center><SMALL><B><FONT COLOR=white>$nom_abr--><FONT COLOR=marroon>$msq_ref_ass<I>&nbsp;:&nbsp;$nom_referentiel</FONT><FONT COLOR=red>--> $mess_niveau $niv_ref<I></B></FONT></SMALL><P></TD></TR>";
 }
 if ($comeback == "liste_gp")
    echo "<form name=\"MForm\" action=\"prescription.php?sq=$sq&pc=$pc&rf=$rf&comeback=$comeback&kaler=$kaler&presc=$presc&insert_presc=1&id_ref=$id_ref\" method='post' target='main'>";
 else
    echo "<form name=\"MForm\" action=\"prescription.php?prem=1&sq=$sq&pc=$pc&rf=$rf&prescription=1&kaler=$kaler&presc=$presc&insert_presc=1&id_ref=$id_ref\" method='post' target='main'>";
 echo "<TR><TD nowrap><TABLE cellspacing='3' align='MIDDLE' valign='top' bgColor='#CEE6EC' border='0' width='100%'><TR><TD><TABLE border='0' width='100%'>";
 if ($presc != "groupe")
 {
       echo "<INPUT TYPE='HIDDEN' NAME='numero_groupe' VALUE='$numero_groupe'>";
       echo "<TR height='40'><TD nowrap><B> $msq_apprenant * </B></TD>";
       if ($prem == 1 && $insert_presc != 1)
       {
           echo "<td rowspan=3 colspan='2'>";
           $lien = "referenciel.php?prescription=1&kaler=$kaler&presc=$presc&numero_groupe=$numero_groupe&utilisateur=$utilisateur";
           $lien = urlencode($lien);
           echo "<A href=\"trace.php?link=$lien\" target='main'><B>$msq_elem_ref</B></A></td>";
       }
       else
       {
         echo "<TD nowrap colspan='2'>";
           $nom_util=GetDataField ($connect,"select util_nom_lb from utilisateur where util_cdn = $utilisateur","util_nom_lb");
           $prenom_util=GetDataField ($connect,"select util_prenom_lb from utilisateur where util_cdn = $utilisateur","util_prenom_lb");
           echo "$nom_util $prenom_util";
           echo "<INPUT TYPE='HIDDEN' NAME='util' VALUE='$utilisateur'>";
           echo "<INPUT TYPE='HIDDEN' NAME='utilisateur' VALUE='$utilisateur'>";
         echo "</TD>";
       }
       echo "</TR>";
 }// fin if presc=appren
 if ($presc != "appren")
 {
      echo "<TR height='40'>";
       echo "<TD nowrap>";
          echo "<B> $mpr_grpmin * </B>";
       echo "</TD>";
    if (!$grp_actif)
    {
       if ($prem == 1 && $insert_presc != 1)
       {
           echo "<td rowspan=3 colspan='2'>";
           $lien = "referenciel.php?prescription=1&kaler=$kaler&presc=$presc";
           $lien = urlencode($lien);
           echo "<A href=\"trace.php?link=$lien\" target='main'><B>$msq_elem_ref</B></A></td>";
       }
       elseif ($prem != 1 || $insert_presc == 1)
       {
             echo "<TD nowrap colspan='2'>";
              if ($typ_user == "ADMINISTRATEUR")
              {
                 $param = $groupe;
                 if ($sq != 1)
                   Ascenseur ("groupe","SELECT distinct grp_cdn,grp_nom_lb from groupe where (grp_publique_on=1 || (grp_publique_on=0 AND grp_resp_no = $id_user)) AND grp_flag_on=1 ORDER BY groupe.grp_nom_lb",$connect,$param);
                 else
                   Ascenseur ("groupe","SELECT distinct grp_cdn,groupe.grp_nom_lb from groupe,utilisateur_groupe where utilisateur_groupe.utilgr_groupe_no = groupe.grp_cdn AND (grp_publique_on=1 || (grp_publique_on=0 AND grp_resp_no = $id_user)) ORDER BY groupe.grp_nom_lb",$connect,$param);
              }
              elseif ($typ_user == "RESPONSABLE_FORMATION")
              {
                 $param = $groupe;
                 if ($sq != 1)
                   Ascenseur ("groupe","SELECT distinct grp_cdn,grp_nom_lb from groupe where grp_resp_no = $id_user ORDER BY groupe.grp_nom_lb",$connect,$param);
                 else
                   Ascenseur ("groupe","SELECT distinct groupe.grp_cdn,groupe.grp_nom_lb from groupe,utilisateur_groupe where groupe.grp_resp_no = $id_user AND utilisateur_groupe.utilgr_groupe_no = grp_cdn AND (grp_publique_on=1 || (grp_publique_on=0 AND grp_resp_no = $id_user)) ORDER BY groupe.grp_nom_lb",$connect,$param);
              }
         echo "</TD>";
       }
    }
    else
    {
      $nom_grp = GetDataField ($connect,"select grp_nom_lb from groupe where grp_cdn = $grp_actif","grp_nom_lb");
      echo "<TD nowrap><INPUT TYPE='HIDDEN' NAME='groupe' VALUE='$grp_actif'>$nom_grp</TD>";
    }
   echo "</TR>";
   }// fin if presc=groupe
   if ($sq == 1 || $kaler)
   {
     echo "<TR height='40'>";
      echo "<TD nowrap>";
          echo "<B>  $msq_seq * </B>";
      echo "</TD>";
      echo "<TD nowrap colspan='2'>";
      if ($prem != 1 || $insert_presc == 1)
      {
            if (!$kaler)
              $seq_query = mysql_query ("SELECT seq_cdn,seq_titre_lb from sequence where sequence.seq_type_on = 0" );
            else
              $seq_query = mysql_query ("SELECT seq_cdn,seq_titre_lb from sequence,sequence_referentiel,referentiel where sequence.seq_cdn = sequence_referentiel.seqref_seq_no AND sequence_referentiel.seqref_referentiel_no = '$id_ref' AND ref_cdn = '$id_ref' AND sequence.seq_type_on = 0" );
              $nb_seq = mysql_num_rows ($seq_query);
            if ($nb_seq == 0)
               echo " $msq_noseq_ref " ;
            else {
                $param = $seq;
                if (!$kaler && $rf == 1)
                  Ascenseur_mult_tot ("seq","SELECT distinct seq_cdn,util_nom_lb,seq_titre_lb from sequence,utilisateur,sequence_referentiel where (sequence_referentiel.seqref_referentiel_no > 0 AND sequence.seq_cdn = sequence_referentiel.seqref_seq_no) and sequence.seq_type_on = 0 and sequence.seq_publique_on = 1 AND utilisateur.util_cdn = sequence.seq_auteur_no GROUP BY util_nom_lb,seq_titre_lb",$connect,$param);
                elseif (!$kaler && $rf == 0)
                  Ascenseur_mult_tot ("seq","SELECT distinct seq_cdn,util_nom_lb,seq_titre_lb from sequence,utilisateur,sequence_referentiel where (sequence_referentiel.seqref_referentiel_no = 0 AND sequence.seq_cdn = sequence_referentiel.seqref_seq_no) and sequence.seq_type_on = 0 and sequence.seq_publique_on = 1 AND utilisateur.util_cdn = sequence.seq_auteur_no GROUP BY util_nom_lb,seq_titre_lb",$connect,$param);
                elseif ($kaler == 1)
                  Ascenseur_mult_tot ("seq","SELECT distinct seq_cdn,ref_desc_cmt,seq_titre_lb from sequence,sequence_referentiel,referentiel where sequence.seq_duree_nb != 0 and sequence.seq_type_on = 0 and sequence.seq_publique_on = 1 AND sequence.seq_cdn = sequence_referentiel.seqref_seq_no AND sequence_referentiel.seqref_referentiel_no = '$id_ref' AND ref_cdn = '$id_ref' GROUP BY ref_desc_cmt,seq_titre_lb",$connect,$param);
           }
      }
     echo "</TD></TR>";
   }// fin if ($sq == 1 || $kaler){
   if ($comeback != "liste_gp")
   {
     if ($pc == 1 || $kaler)
     {
      echo "<TR height='40'>";
      echo "<TD nowrap>";
          echo "<B> $msq_parc * </B>";
      echo "</TD>";
      if ($prem != 1 || $insert_presc == 1)
      {
        echo "<TD nowrap colspan='2'>";
            if (!$kaler)
               $parc_query = mysql_query ("SELECT parcours_cdn,parcours_nom_lb from parcours where parcours.parcours_type_on = 0");
            else
               $parc_query = mysql_query ("SELECT parcours_cdn,parcours_nom_lb from referentiel,parcours where referentiel.ref_cdn = parcours.parcours_referentiel_no AND referentiel.ref_cdn = '$id_ref' AND parcours.parcours_type_on = 0");
            $nb_parc = mysql_num_rows ($parc_query);
            if ($nb_parc == 0)
                 echo " $mpa_noparc_ref ";
            else
            {
                $param = $parc;
                if (!$kaler && $rf == 1)
                  Ascenseur_mult_tot ("parc","SELECT distinct parcours_cdn,util_nom_lb,parcours_nom_lb from utilisateur,parcours,sequence_parcours where parcours.parcours_referentiel_no > 0 AND parcours.parcours_type_on = 0 AND utilisateur.util_cdn = parcours.parcours_auteur_no AND parcours.parcours_cdn = sequence_parcours.seqparc_parc_no GROUP BY util_nom_lb,parcours_nom_lb",$connect,$param);
                elseif (!$kaler && $rf == 0)
                  Ascenseur_mult_tot ("parc","SELECT distinct parcours_cdn,util_nom_lb,parcours_nom_lb from utilisateur,parcours,sequence_parcours where parcours.parcours_referentiel_no = 0 AND parcours.parcours_type_on = 0 AND utilisateur.util_cdn = parcours.parcours_auteur_no AND parcours.parcours_cdn = sequence_parcours.seqparc_parc_no GROUP BY util_nom_lb,parcours_nom_lb",$connect,$param);
                else
                  Ascenseur_mult_tot ("parc","SELECT distinct parcours_cdn,ref_desc_cmt,parcours_nom_lb from referentiel,parcours,sequence_parcours where referentiel.ref_cdn = parcours.parcours_referentiel_no AND parcours.parcours_type_on = 0 AND referentiel.ref_cdn = '$id_ref' AND parcours.parcours_cdn = sequence_parcours.seqparc_parc_no GROUP BY ref_desc_cmt,parcours_nom_lb",$connect,$param);
                echo "</TD>";
            }
        }
     echo "</TR>";
     }
   }
   else
   { // finif ($comeback != "liste_gp"){// fin if ($pc == 1 || $kaler){
      $nom_parc = GetDataField ($connect,"select parcours_nom_lb from parcours where parcours_cdn = $parc","parcours_nom_lb");
      echo "<INPUT type='hidden' name='parc' value='$parc'>";
      echo "<TR height='40'><TD nowrap><B> $msq_parc * : </B></TD><TD nowrap>$nom_parc</TD></TR>";
   }

 $ch_date_fin = explode ("/",$today);
 $day = $ch_date_fin[0];
 if (strlen($day) == 1)
   $day = "0$day";
 $month = $ch_date_fin[1];
 if (strlen($month) == 1)
   $month = "0$month";
 $year = $ch_date_fin[2];
 ?>
 <TR>
      <TD nowrap>
          <B> <?php echo "$mess_gp_date_deb *";?> </B>
      </TD>
          <TD nowrap colspan='2'><TABLE><TR>
      <input type="hidden" name=txt_custom value="AppendOrReplace=Replace;AppendChar=';';CloseOnSelect=Yes;ReturnData=Date;Title=Calendrier;InlineX=660;InlineY=160;CurrentDate=Today;SelectAfter=Today-30;SelectBefore=Today+30;AllowWeekends=No;Resizable=Yes;CallFunction=TryCallFunction1;PopupX=300;PopupY=300;Nav=Yes;SmartNav=Yes;Fix=No;WeekStart=1;Weekends=06">
      <TD><input type="text" class='INPUT' name="imonth1" value="<?php echo $day;?>" size="1" MAXLENGTH="2"></TD>
      <TD><input type="text" class='INPUT' name="iday1" value="<?php echo $month;?>" size="1" MAXLENGTH="2"></TD>
      <TD><input type="text" class='INPUT' name="iyear1" value="<?php echo $year;?>" size="2" MAXLENGTH="4"></TD>
      <input type="hidden" value="" name="ma_date" size=10>
      <TD><a href="javascript:show_calendar('MForm.ma_date','09', '2002', 'DD/MM/YYYY', 'INLINE', MForm.txt_custom.value);"
               onmouseover="img_cal1.src='images/agenda/icocalendb.gif';return true;"
               onmouseout="img_cal1.src='images/agenda/icocalend.gif'">
               <IMG NAME="img_cal1" SRC="images/agenda/icocalend.gif" BORDER='0' valign='top' alt="<?php  echo $cal_click  ;?>"
               onLoad="tempImg=new Image(0,0); tempImg.src='images/agenda/icocalendb.gif'"></A></TD>
     </TR></TABLE></TD>
   </TR>
   <TR><br>
      <TD nowrap>
          <B> <?php echo "$mess_gp_date_fin *";?>  </B>
      </TD>
      <TD nowrap  colspan='2'><TABLE><TR>
      <input type="hidden" name=txt_custom1 value="AppendOrReplace=Replace;AppendChar=';';CloseOnSelect=Yes;ReturnData=Date;Title=<Calendrier;InlineX=660;InlineY=160;CurrentDate=Today;SelectAfter=Today-30;SelectBefore=Today+30;AllowWeekends=No;Resizable=Yes;CallFunction=TryCallFunction;PopupX=300;PopupY=300;Nav=Yes;SmartNav=Yes;Fix=No;WeekStart=1;Weekends=06">
      <TD><input type="text" class='INPUT' name="imonth" value="<?php echo $imonth;?>" size="1" MAXLENGTH="2"></TD>
      <TD><input type="text" class='INPUT' name="iday" value="<?php echo $iday;?>" size="1" MAXLENGTH="2"></TD>
      <TD><input type="text" class='INPUT' name="iyear" value="<?php echo $iyear;?>" size="2" MAXLENGTH="4"></TD>
      <input type="hidden" value="" name="mydate1">
      <TD><a href="javascript:show_calendar('MForm.mydate1','09', '2002', 'DD/MM/YYYY', 'INLINE', MForm.txt_custom1.value);"
               onmouseover="img_cal2.src='images/agenda/icocalendb.gif';return true;"
               onmouseout="img_cal2.src='images/agenda/icocalend.gif'">
               <IMG NAME="img_cal2" SRC="images/agenda/icocalend.gif" BORDER='0' valign='top' alt="<?php  echo $cal_click  ;?>"
               onLoad="tempImg=new Image(0,0); tempImg.src='images/agenda/icocalendb.gif'"></A></TD>
     </TR></TABLE></TD>
   <TR>
      <TD nowrap>
          <B> <?php echo $mess_formrefdisc." *";?>   </B>
      </TD>
      <TD nowrap colspan='2'>
         <?php  $form_query = mysql_query ("SELECT util_cdn from utilisateur where (util_typutil_lb = 'FORMATEUR_REFERENT' or util_typutil_lb = 'RESPONSABLE_FORMATION' or util_typutil_lb = 'ADMINISTRATEUR') AND util_flag = 0");
            $nb_form = mysql_num_rows ($form_query);
            $param = $form_ref;
            if ($nb_form == 0)
               echo $mess_noform_ref;
            else
               Ascenseur_mult_tot ("form_ref","SELECT util_cdn,util_nom_lb,util_prenom_lb from utilisateur where (util_typutil_lb = 'FORMATEUR_REFERENT' or util_typutil_lb = 'RESPONSABLE_FORMATION' or util_typutil_lb = 'ADMINISTRATEUR') AND util_flag = 0 order by util_nom_lb ASC",$connect,$param);?>
      <BR><BR></TD>
   </TR>
   <?php
   echo "<TR><TD></TD><TD align='left'><FONT size=1>$mess_insc_symb</FONT></TD></TR>";
   echo "<TR height='40'><TD></TD><TD align='left'><A HREF=\"javascript:document.MForm.submit();\" onmouseover=\"img1.src='images/fiche_identite/boutvalidb.gif';return true;\" onmouseout=\"img1.src='images/fiche_identite/boutvalid.gif'\">".
        "<IMG NAME=\"img1\" SRC=\"images/fiche_identite/boutvalid.gif\" BORDER='0' onLoad=\"tempImg=new Image(0,0); tempImg.src='images/fiche_identite/boutvalidb.gif'\"></A></TD></TR>";
   if ($grp_actif > 0 )
   {
     echo "<TR height='30'><TD align=left><A HREF=\"javascript:history.go(-1);\" onmouseover=\"img_annonce.src='images/fiche_identite/boutretourb.gif';return true;\" onmouseout=\"img_annonce.src='images/fiche_identite/boutretour.gif'\">";
     echo "<IMG NAME=\"img_annonce\" SRC=\"images/fiche_identite/boutretour.gif\" BORDER='0' onLoad=\"tempImg=new Image(0,0); tempImg.src='images/fiche_identite/boutretourb.gif'\"></A></TD></TR>";
   }

   if ($groupe && !$message_erreur)
   {
     $lien = "modif_gp.php?id_grp=$groupe&revenir=prescription";
     $lien = urlencode($lien);
     echo "<TD align='center'><A href=\"trace.php?link=$lien\" target='main'>";
     echo "<IMG SRC=\"images/modules/tut_form/icomodiparc1.gif\" BORDER='0' ALT=\"$modif_parc_grp\" ></A></TD>";
   }
   if ($util && !$message_erreur)
   {
      $nom_util = GetDataField ($connect,"SELECT util_nom_lb from utilisateur where util_cdn='$util'","util_nom_lb");
      $prenom_util = GetDataField ($connect,"SELECT util_prenom_lb from utilisateur where util_cdn='$util'","util_prenom_lb");
      $lien = "modif_parc.php?a_faire=1&utilisateur=$util&revenir=prescription&numero_groupe=$numero_groupe";
      $lien = urlencode($lien);
      echo "<TD align='center'><A href=\"trace.php?link=$lien\" target='main' title=\"$mess_menu_mod_presc $de $nom_util $prenom_util\">
      <IMG SRC=\"images/modules/tut_form/icomodiparc1.gif\" border='0' alt=\"$mess_menu_mod_presc $de $nom_util $prenom_util\"></A></TD>";
   }
   if (!$groupe && !$util)
     echo "<TD align='center'>&nbsp;</TD>";
   echo "</TR></TABLE></TD></form></TR></TABLE></TD></TR></TABLE>";
   echo"</TD></TR></TABLE></center>";
?>
               <DIV ID=Main>
               <!-- A Separate Layer for the Calendar -->
               <!-- Make sure to use the name Calendar for this layer -->
               <SCRIPT Language="Javascript" TYPE="text/javascript">
               Calendar.CreateCalendarLayer(10, 275, "");
               </SCRIPT>
               </DIV>
<?php
}// fin if presc == 1
//include 'click_doit.txt';
?>
</body>
</html>
