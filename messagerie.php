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
//include ("click_droit.txt");
dbConnect();

//de quel type est l'utilisateur (apprenant, formateur, administrateur)
$nom_user=GetDataField ($connect,"select util_nom_lb from utilisateur where util_cdn = $id_user","util_nom_lb");
$prenom_user=GetDataField ($connect,"select util_prenom_lb from utilisateur where util_cdn = $id_user","util_prenom_lb");
$email=GetDataField ($connect,"select util_email_lb from utilisateur where util_cdn='$id_user'","util_email_lb");
$adr_webmail = GetDataField ($connect, "select util_urlmail_lb from utilisateur where util_cdn=$id_user","util_urlmail_lb");
$averti = 1;
$_SESSION['averti'] = $averti;
include ('style.inc.php');
  ?>
  <script type="text/javascript" src="OutilsJs/lib/ajax/ajax_cms.js"></script>
  <div id="affiche" class="Status"></div>
  <div id="mon_contenu" class="mon_contenu"
        onClick="javascript:$(document).ready(function() {if ($.browser.msie) {$('#mon_contenu').hide();}else{$('#mon_contenu').empty();}})"
        <?php echo "title=\"$mess_clkF\">";?>
  </div>
  <?php
/*
if ($complement == 1)
{
   echo "<TABLE background=\"images/menu/fond_logo_formagri.jpg\" border='0' cellspacing='0' cellpadding='0' width='100%'>";
   echo "<TR width='100%'><TD align='left' width='800'><IMG SRC=\"images/logo_formagri.jpg\" border='0'></TD>";
   $lien="delog.php";
   $lien = urlencode($lien);
   echo "<TD align='right' valign='bottom'><A href=\"trace.php?link=$lien\" title=\"$mess_dcnx\"".
        " onmouseover=\"img_dec.src='images/complement/boutdeconecb.gif';return true;\" ".
        "onmouseout=\"img_dec.src='images/complement/boutdeconec.gif'\">";
   echo "<IMG NAME=\"img_dec\" SRC=\"images/complement/boutdeconec.gif\" BORDER='0'".
        " onLoad=\"tempImg=new Image(0,0); tempImg.src='images/complement/boutdeconecb.gif'\"></A></TD></TR></TABLE>";
   echo "<TABLE background=\"images/ecran_annonce/bando.gif\" cellspacing='0' cellpadding='0' width='100%' border='0'>".
        "<TR width='100%'><TD align='left' width='100%' valign='top'><IMG SRC=\"images/complement/soustitre.gif\" border='0'>".
        "</TD></TR></TABLE>";
   echo "<TABLE cellspacing='0' cellpadding='0' width='100%' border='0'><TR width='100%'>".
        "<TD align='left' bgcolor='#002D44' height ='40'></TD></TR></TABLE>";
}
*/
if ($restaurer == 1)
   $requete = mysql_query("update messagerie set lu = 1,supprime = 0 where mess_cdn = $numero");
if ($dejalu == 1)
   $requete = mysql_query("update messagerie set lu = 0,supprime = 0 where mess_cdn = $numero");
if ($renonlu == 1)
   $requete = mysql_query("update messagerie set lu = 1,supprime = 0 where mess_cdn = $numero");

if ($lire == 1)
{
    if ($envoyes != 1)
       $requete = mysql_query("update messagerie set lu = 0 where mess_cdn = $numero");
    //if ($lu == 1)
       //recharge();
    $letitre = $mess_menu_mail;
    entete_simple($letitre);
    echo "<TR><TD width='100%'><TABLE cellspacing='0' cellpadding='4' width='100%' border='0'>";
    $requete = mysql_query ("SELECT * from messagerie where mess_cdn = $numero");
    $numero = mysql_result($requete,$i,"mess_cdn");
    if ($envoyes == 1)
       $envoyeur = mysql_result($requete,$i,"id_user");
    else
       $envoyeur = mysql_result($requete,$i,"envoyeur");
    $nom_envoyeur = GetDataField ($connect,"select util_nom_lb from utilisateur where util_cdn='$envoyeur'","util_nom_lb");
    $prenom_envoyeur = GetDataField ($connect,"select util_prenom_lb from utilisateur where util_cdn='$envoyeur'","util_prenom_lb");
    $origine = $prenom_envoyeur." ".$nom_envoyeur;
    $email = GetDataField ($connect, "select util_email_lb from utilisateur where util_cdn=$envoyeur","util_email_lb");
    $sujet = mysql_result($requete,$i,"origine");
    $date = mysql_result($requete,$i,"date");
    $message = mysql_result($requete,$i,"contenu");
    $fichier = mysql_result($requete,$i,"mess_fichier_lb");
    $lu = mysql_result($requete,$i,"lu");
    $supprime = mysql_result($requete,$i,"supprime");
    if ($fichier != "")
    {
      $son_nom = substr(strrchr ( $fichier, "/" ),1);
      echo "<TR bgcolor = '#FFFFFF'><TD align='left' valign='top'><b>$mess_piece_join</b></TD>";
      echo "<TD align='left' width='85%' valign='top'><A href=\"$fichier\" target='_blank' title=\"$mess_piece_join : $son_nom\"".
          " onmouseover=\"img_fjl.src='images/messagerie/icoGtrombonb.gif';return true;\"".
          " onmouseout=\"img_fjl.src='images/messagerie/icoGtrombon.gif'\">".
          "<IMG NAME=\"img_fjl\" SRC=\"images/messagerie/icoGtrombon.gif\" BORDER='0' ALT=\"$mess_piece_join : $son_nom\"".
          " onLoad=\"tempImg=new Image(0,0); tempImg.src='images/messagerie/icoGtrombonb.gif'\"></A></TD></TR>";
    }
    echo "<TR bgcolor = '#F4F4F4' height='30'><TD align='left' valign='top'><b>$mess_mess_date</b></TD>";
    echo "<TD align='left' width='85%' valign='top'>$date</TD></TR>";
    if ($envoyes != 1)
      echo "<TR bgcolor = '#FFFFFF' height='30'><TD align='left' valign='top'><b>$mess_mail_origine</b></TD>";
    else
      echo "<TR bgcolor = '#FFFFFF' height='30'><TD align=left' valign='top'><b>$mess_mail_dest</b></TD>";
    echo "<TD align='left' width='85%' valign='top'>$origine</TD></TR>";
    echo "<TR bgcolor = '#F4F4F4' height='30'><TD align='left' valign='top'><b>$mess_mail_sujet</b></TD>";
    echo "<TD align='left' width='85%' valign='top'>$sujet</TD></TR>";
    echo "<TR bgcolor = '#FFFFFF' height='30'><TD align='left' valign='top'><b>$mess_mail_mess</b></TD>";
    echo "<TD align='left' width='85%' valign='top'>$message</TD></TR>";
    $sujet = "Re : $sujet";
    $lien = "mail.php?contacter=1&num=$envoyeur&complement=$complement&sujet=$sujet";
    $lien = urlencode($lien);
    if ($envoyeur != $id_user && $envoyes != 1)
       echo "<TR bgcolor = '#FFFFFF' height='40'><td></td><TD valign='center'>$bouton_gauche".
            "<A HREF=\"trace.php?link=$lien\">$mess_repondre</A>$bouton_droite</TD></TR>";
    echo "</TABLE></TD></TR></TABLE></TD></TR></TABLE>";
    echo "</BODY></HTML>";
    exit();
}
//------------------------------------- Suppression de messages groupés ---------------------------------------------------
if ($supp_envoi == 1 && $nb_supp > 0)
{
 if ($envoyes == 1)
 {
   $ii=0;
   while ($ii < $nb_supp)
   {
     if ($ordre[$ii] == 'on')
     {
        $requete = mysql_query ("update messagerie set supp_envoi = 1 where envoyeur = $id_user and mess_cdn = $numer[$ii]");
        $mess_alerte = $mess_supp_mess;
     }
     $ii++;
   }
 }
 elseif ($poubelle == 1)
 {
   $ii=0;
   while ($ii < $nb_supp)
   {
     if ($ordre[$ii] == 'on')
     {
       $requete = mysql_query ("delete from messagerie where id_user = $id_user and mess_cdn = $numer[$ii]");
       $mess_alerte = $mess_vider_corbeille;
     }
     $ii++;
   }
 }
 elseif ($vider == 1)
 {
   $requete = mysql_query ("delete from messagerie where id_user = $id_user AND supprime = 1");
   $mess_alerte = $mess_vider_corbeille;
 }
 else
 {
   $ii=0;
   while ($ii < $nb_supp)
   {
     if ($ordre[$ii] == 'on')
     {
       $requete = mysql_query ("update messagerie set supprime = 1 where id_user=$id_user and mess_cdn = $numer[$ii]");
       $mess_alerte = $mess_pose_corbeille;
     }
     $ii++;
   }
 }
}
//-----------------------Suppression par message unique-----------------------------------------------------------------------
if ($supp_tous == 1)
{
   $requete = mysql_query ("update messagerie set supprime = 1 where id_user=$id_user and  supprime=0");
   $mess_alerte = $mess_pose_corbeille;
}
if ($supp_unique == 1 && $corbeille == 1 && $envoyes != 1)
{
   $requete = mysql_query ("delete from messagerie where mess_cdn = $numero");
   $mess_alerte = $mess_vider_corbeille;
}
elseif ($supp_unique == 1 && $corbeille != 1 && $envoyes != 1)
{
   $requete = mysql_query ("update messagerie set supprime = 1 where id_user=$id_user AND mess_cdn=$numero AND supprime=0");
   $mess_alerte = $mess_pose_corbeille;
}
elseif ($supp_unique == 1 && $corbeille != 1 && $envoyes == 1)
{
   $requete = mysql_query ("update messagerie set supp_envoi = 1 where envoyeur = $id_user and mess_cdn = $numero");
   $mess_alerte = $mess_supp_mess;
}
if ($corbeille == 1)
  $requete = mysql_query ("SELECT * from messagerie where id_user = $id_user AND supprime = 1 order by mess_cdn desc");
elseif ($envoyes == 1)
  $requete = mysql_query ("SELECT * from messagerie where envoyeur = $id_user AND supp_envoi = 0 order by mess_cdn desc");
else
  $requete = mysql_query ("SELECT * from messagerie where id_user = $id_user AND supprime = 0 order by mess_cdn desc");
$nbr_req = mysql_num_rows($requete);
    $letitre = $mess_menu_mail;
  if ($corbeille == 1)
    $soustitre = $mess_corbeille;
  elseif ($envoyes == 1)
   $soustitre = $mess_bt_envoi;
  else
    $soustitre = $mess_bt_recpt;
  if ($mess_alerte != "")
    $sous_soustitre = $mess_alerte;
  if ($sous_soustitre != "")
      echo notifier($sous_soustitre);
    entete_simple($letitre);
  echo "<TR><TD width='100%' bgcolor='#FFFFFF'><TABLE width='100%'  cellpadding='0' cellspacing='0' border='0'><TR><TD width='100%'>";
  echo "<TABLE cellpadding='0' cellspacing='0' width='50%' border='0'><TR>";
  $requete1 = mysql_query ("SELECT count(*) from messagerie where id_user = $id_user AND lu = 1 and supprime = 0");
  $nbr_bt = mysql_result($requete1,0);
  $requete1b = mysql_query ("SELECT count(*) from messagerie where id_user = $id_user AND supprime = 0");
  $nbr_bt1 = mysql_result($requete1b,0);
  $requete2 = mysql_query ("SELECT count(*) from messagerie where id_user = $id_user AND supprime = 1");
  $nbr_supp = mysql_result($requete2,0);
  $requete3 = mysql_query ("SELECT count(*) from messagerie where envoyeur = $id_user AND supp_envoi = 0");
  $nbr_be = mysql_result($requete3,0);
  $lien_annuaire="mail.php?prem=1&ret=$ret&complement=$complement&messagerie=1";
  $lien_mail_annuaire .= "<TD align=left valign='bottom' nowrap style=\"padding-right:8px;\"><A HREF=\"javascript:void(0);\"".
          " onclick=\"window.open('$lien_annuaire','','scrollbars=no,resizable=yes,width=680,height=520')\" ".
          "class='bouton_new'>$mess_ecr_mail\n</A></TD>";
// lien vers boite de reception
   $lien= "messagerie.php?ret=$ret&corbeille=0&complement=$complement";
   $lien=urlencode($lien);
   if ($corbeille != 1 && $envoyes != 1)
      $lien_reception = "\n<TD nowrap align='center'><TABLE cellspacing='0' cellpadding='0'><TR><TD>".
                        "<IMG SRC='images/messagerie/onglGvert.gif' border='0'></TD>".
                        "<TD bgcolor='#2B677A' nowrap>&nbsp;&nbsp;&nbsp;<Font size='2' color='#FFFFFF'><B>$mess_bt_recpt</B></FONT>".
                        "&nbsp;&nbsp;&nbsp;&nbsp;</TD><TD><IMG SRC='images/messagerie/onglDvert.gif' border='0'></TD></TR></TABLE></TD>";
   else
     $lien_reception = "\n<TD nowrap align='center' valign='bottom'>".
                       "<TABLE cellspacing='0' cellpadding='0'><TR height='26'><TD valign='bottom'>".
                       "<IMG SRC='images/messagerie/onglGblanc.gif' border='0'></TD>".
                       "<TD valign='center' nowrap background='images/messagerie/onglCblanc.gif'>".
                       "<DIV id='sequence'><A href=\"trace.php?link=$lien\"><Font size='2'><B>$mess_bt_recpt</B></FONT></A>".
                       "</DIV></TD><TD><IMG SRC='images/messagerie/onglDblanc.gif' border='0'></TD></TR></TABLE></TD>";
// lien vers boite d'envoi
   $lien="messagerie.php?ret=$ret&envoyes=1&complement=$complement";
   $lien=urlencode($lien);
   if ($envoyes == 1)
      $lien_envoi = "\n<TD nowrap align='center'><TABLE cellspacing='0' cellpadding='0'><TR><TD>".
                    "<IMG SRC='images/messagerie/onglGvert.gif' border='0'></TD>".
                    "<TD bgcolor='#2B677A' nowrap>&nbsp;&nbsp;&nbsp;<Font size='2' color='#FFFFFF'><B>$mess_bt_envoi</B></FONT>".
                    "&nbsp;&nbsp;&nbsp;</TD><TD><IMG SRC='images/messagerie/onglDvert.gif' border='0'></TD></TR></TABLE></TD>";
   else
      $lien_envoi = "\n<TD align='center' valign='bottom'>".
                    "<TABLE cellspacing='0' cellpadding='0'><TR height='26'><TD valign='bottom'>".
                    "<IMG SRC='images/messagerie/onglGblanc.gif' border='0'></TD>".
                    "<TD valign='center' nowrap background='images/messagerie/onglCblanc.gif'>".
                    "<DIV id='sequence'><A href=\"trace.php?link=$lien\"><Font size='2'><B>$mess_bt_envoi</B></FONT></A>".
                    "</DIV></TD><TD><IMG SRC='images/messagerie/onglDblanc.gif' border='0'></TD></TR></TABLE></TD>";
// lien vers corbeille
   $lien= "messagerie.php?ret=$ret&corbeille=1&complement=$complement";
   $lien=urlencode($lien);
   if ($corbeille == 1)
      $lien_corbeille = "\n<TD nowrap align='center'><TABLE cellspacing='0' cellpadding='0'><TR><TD>".
                        "<IMG SRC='images/messagerie/onglGvert.gif' border='0'></TD>".
                        "<TD bgcolor='#2B677A' nowrap>&nbsp;&nbsp;&nbsp;<Font size='2' color='#FFFFFF'><B>$mess_corbeille</B></FONT>".
                        "&nbsp;&nbsp;&nbsp;</TD><TD><IMG SRC='images/messagerie/onglDvert.gif' border='0'></TD></TR></TABLE></TD>";
   else
      $lien_corbeille = "\n<TD nowrap align='center' valign='bottom'><TABLE cellspacing='0' cellpadding='0'>".
                        "<TR height='26'><TD valign='bottom'><IMG SRC='images/messagerie/onglGblanc.gif' border='0'></TD>".
                        "<TD valign='center' nowrap background='images/messagerie/onglCblanc.gif'>".
                        "<DIV id='sequence'><A href=\"trace.php?link=$lien\" class='bouton3'><Font size='2'><B>$mess_corbeille</B></FONT></A>".
                        "</DIV></TD><TD><IMG SRC='images/messagerie/onglDblanc.gif' border='0'></TD></TR></TABLE></TD>";
   echo "<TD nowrap valign='bottom' align='left' width='300px' style=\"padding-left:2px;padding-top:3px;padding-bottom:3px;\">".
        "<TABLE cellspacing='4' cellpadding='0' border='0'><TR>";
   if ($corbeille == 1)
   {
     if ($complement != 1 && $ret !=1)
     {
        echo $lien_mail_annuaire;
        echo $lien_mail;
     }
   }
   elseif($corbeille != 1 && $envoyes != 1)
   {
     if ($complement != 1 && $ret !=1)
     {
        echo $lien_mail_annuaire;
        echo $lien_mail;
     }
   }
   elseif ($envoyes == 1)
   {
     if ($complement != 1 && $ret !=1)
     {
        echo $lien_mail_annuaire;
        echo $lien_mail;
     }
   }
   if ($adr_webmail != "")
   {
      echo "<TD nowrap valign='bottom' align='left' style=\"padding-right:8px;\">";
      echo "<A HREF=\"$adr_webmail\" class='bouton_new' target='_blank'".
           bulle($mess_mail_web,"","RIGHT","ABOVE",180).
           "$mess_admin_webmail</A></TD>";
   }
   if ($typ_user == 'APPRENANT')
       echo aide("messagerie_apprenant",'');
   else
       echo aide("messagerie",'');
   echo "</TR></TABLE></TD>";
  echo "</TR></TABLE></TD></TR><TR><TD valign='bottom'>".
       "<TABLE cellpadding='0' cellspacing='0' border='0'><TR height='25' bordercolor='#2B677A'>";
  echo $lien_reception;
  echo $lien_envoi;
  echo $lien_corbeille;
  echo "</TR></TABLE></TD>";
  if ($typ_user != "APPRENANT")
  {
    echo "<form name=\"form3\"><TD valign='bottom' align='left'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
    echo "<SELECT name=\"select\" onChange=javascript:appel_wpop(form3.select.options[selectedIndex].value)>";
    echo "<OPTION>$mess_mail_ecrire_a.......</OPTION>";
    if ($typ_user == "ADMINISTRATEUR")
    {
      $lien="mail.php?groupee=1&pourqui=utilisateurs";
      $lien = urlencode($lien);
      echo "<OPTION value='trace.php?link=$lien'>$mess_menu_mail_tous</OPTION>";
      $lien="mail.php?groupee=1&pourqui=resp_form";
      $lien = urlencode($lien);
      echo"<OPTION value='trace.php?link=$lien'>$mess_menu_mail_resp_form</OPTION>";
    }
    if (($typ_user == "RESPONSABLE_FORMATION")  || ($typ_user == "ADMINISTRATEUR"))
    {
      $lien="mail.php?groupee=1&pourqui=formateurs";
      $lien = urlencode($lien);
      echo"<OPTION value='trace.php?link=$lien'>$mess_menu_mail_form</OPTION>";
    }
    if (($typ_user == "RESPONSABLE_FORMATION")  || ($typ_user == "ADMINISTRATEUR")  || ($typ_user == "FORMATEUR_REFERENT"))
    {
      $lien="mail.php?groupee=1&pourqui=tuteurs";
      $lien = urlencode($lien);
      echo"<OPTION value='trace.php?link=$lien'>$mess_menu_mail_tut</OPTION>";
    }
    $lien="mail.php?groupee=1&pourqui=apprenants";
    $lien = urlencode($lien);
    echo"<OPTION value='trace.php?link=$lien'>$mess_menu_mail_app</OPTION>";
    echo "</SELECT></TD></FORM>";
  }
  if ($typ_user == "RESPONSABLE_FORMATION" || $typ_user == "ADMINISTRATEUR")
  {
       $grp_req = mysql_query("SELECT * from groupe");
       $nbr = mysql_num_rows($grp_req);
       if ($nbr > 0)
       {
          echo "<form name=\"form2\"><TD valign='bottom' align='left'>";
          echo "<SELECT name=\"select\" onChange=javascript:appel_wpop(form2.select.options[selectedIndex].value)>";
          echo "<OPTION>$mess_mail_ecrire_aux</OPTION>";
          $i = 0;
          while ($i < $nbr)
          {
            $id_grp = mysql_result($grp_req,$i,"grp_cdn");
            $nom_grp = mysql_result($grp_req,$i,"grp_nom_lb");
            $resp_grp = mysql_result($grp_req,$i,"grp_resp_no");
            $carac_grp = strlen($nom_grp);
            if ($carac_grp > 45)
                $nom_grp1 = substr($nom_grp,0,45)."..";
            else
                $nom_grp1 = $nom_grp;
            if ($id_user == $resp_grp || $typ_user == "ADMINISTRATEUR")
            {
               $lien="mail.php?groupee=1&grp=$id_grp";
               $lien = urlencode($lien);
               echo "<OPTION value='trace.php?link=$lien'\">$nom_grp1</OPTION>";
            }
          $i++;
          }
          echo "</SELECT></TD></FORM>";
       }
  }
  echo "</TR></TABLE>";
  echo "<TABLE bgcolor='#FFFFFF' cellspacing='1' cellpadding='4' border='0' width='100%'>";
  echo "<TR>";
  echo "<td width='5%' class='barre_titre'>$mess_mail_lu</td>";
  echo "<td width='2%' class='barre_titre' nowrap>$mess_piece_join</td>";
  echo "<td width='10%' class='barre_titre'>$mess_mess_date</td>";
  if ($envoyes != 1)
    echo "<td width='10%' class='barre_titre'>$mess_mail_origine</td>";
  else
    echo "<td width='10%' class='barre_titre'>$mess_mail_dest</td>";
  echo "<td width='20%' class='barre_titre'>$mess_mail_sujet</td>";
  echo "<td width='50%' class='barre_titre'>$mess_mail_mess</td>";
  if ($envoyes != 1 && $corbeille != 1)
  {
        ?>
        <script language="JavaScript" type="text/javascript">        <!--
        function CheckAll() {
          for (var j = 0; j < document.form1.elements.length; j++) {
            if(document.form1.elements[j].type == 'checkbox'){
               document.form1.elements[j].checked = !(document.form1.elements[j].checked);
            }
          }
        }
        //--></script>
        <?php
     echo "<td class='barre_titre' nowrap>$mess_env_corb</td>";
     echo "<FORM NAME='form1' method=\"POST\" action=\"messagerie.php?ret=$ret&supp_envoi=1&nb_supp=$nbr_req&complement=$complement\">";
  }
  elseif($corbeille == 1)
  {
      if ($nbr_req > 1)
        echo "<FORM NAME='form1' method=\"POST\" action=\"messagerie.php?ret=$ret&supp_envoi=1&nb_supp=$nbr_req&complement=$complement&poubelle=1&corbeille=$corbeille&nb_supp=$nbr_req\">";
      else
        echo "<FORM NAME='form1' method=\"POST\" action=\"messagerie.php?ret=$ret&supp_envoi=1&nb_supp=$nbr_req&complement=$complement&poubelle=1&nb_supp=$nbr_req\">";
        ?>
        <script language="JavaScript" type="text/javascript">        <!--
        function CheckAll() {
          for (var j = 0; j < document.form1.elements.length; j++) {
            if(document.form1.elements[j].type == 'checkbox'){
               document.form1.elements[j].checked = !(document.form1.elements[j].checked);
            }
          }
        }
        //--></script>
        <?php
     echo "<td width='5%' class='barre_titre'>$mess_restaurer</td>";
     echo "<td class='barre_titre' nowrap>$mess_ag_supp</td>";
  }
  elseif($envoyes == 1)
     echo "<td class='barre_titre'>$mess_ag_supp</td>";
  echo "</TR>";
  if (($envoyes == 1 || $corbeille == 1 || ($envoyes != 1 && $corbeille != 1)) && $nbr_req > 0 && $typ_user != "APPRENANT")
  {
    if ($corbeille == 1)
      $largeur=8;
    else
      $largeur=7;
    echo "<TR><TD colspan='$largeur' align='right' bgcolor='#FFFFFF'><A href=\"javascript:void(0);\" Title=\"$mess_inv_sel\" ".
         "onClick=\"CheckAll();\"".
         " onmouseover=\"img_inv.src='images/messagerie/boutinversb.gif';return true;\"".
         " onmouseout=\"img_inv.src='images/messagerie/boutinvers.gif'\">".
         "<IMG NAME=\"img_inv\" SRC=\"images/messagerie/boutinvers.gif\" BORDER='0' ALT=\"$mess_inv_sel\"".
         " onLoad=\"tempImg=new Image(0,0); tempImg.src='images/messagerie/boutinversb.gif'\"></A>";
    echo "&nbsp;<A HREF=\"javascript:void(0);\" onclick=\"javascript:return(confm('javascript:document.form1.submit()'));\" Title=\"$mess_ag_supp\"".
         " onmouseover=\"img_supr.src='images/messagerie/boutsupprimb.gif';return true;\"".
         " onmouseout=\"img_supr.src='images/messagerie/boutsupprim.gif'\">".
         "<IMG NAME=\"img_supr\" SRC=\"images/messagerie/boutsupprim.gif\" BORDER='0' ALT=\"$mess_ag_supp\"".
         " onLoad=\"tempImg=new Image(0,0); tempImg.src='images/messagerie/boutsupprimb.gif'\"></A></TD></TR>";
  }
if ($nbr_req > 0 && $typ_user != "APPRENANT")
{
        echo "<FORM NAME='form1' method='POST' action=\"messagerie.php?ret=$ret&complement=$complement&supp_envoi=1&envoyes=1&nb_supp=$nbr_req\">";
        ?>
        <script language="JavaScript" type="text/javascript">        <!--
        function CheckAll() {
          for (var j = 0; j < document.form1.elements.length; j++) {
            if(document.form1.elements[j].type == 'checkbox'){
               document.form1.elements[j].checked = !(document.form1.elements[j].checked);
            }
          }
        }
        //--></script>
        <?php
}
if ($nbr_req > 0)
{
  $i = 0;
  $ordre = array();
  $numer = array();
  while ($i < $nbr_req)
  {
    $numero = mysql_result($requete,$i,"mess_cdn");
    $numer[$i] = $numero;
    if ($envoyes == 1)
       $envoyeur = mysql_result($requete,$i,"id_user");
    else
       $envoyeur = mysql_result($requete,$i,"envoyeur");
    $nom_envoyeur = GetDataField ($connect,"select util_nom_lb from utilisateur where util_cdn='$envoyeur'","util_nom_lb");
    $prenom_envoyeur=GetDataField ($connect,"select util_prenom_lb from utilisateur where util_cdn='$envoyeur'","util_prenom_lb");
    $origine = $prenom_envoyeur." ".$nom_envoyeur;
    $email = GetDataField ($connect, "select util_email_lb from utilisateur where util_cdn=$envoyeur","util_email_lb");
    $sujet = mysql_result($requete,$i,"origine");
    $date = mysql_result($requete,$i,"date");
    $message = mysql_result($requete,$i,"contenu");
    $fichier = mysql_result($requete,$i,"mess_fichier_lb");
    $lu = mysql_result($requete,$i,"lu");
    $supprime = mysql_result($requete,$i,"supprime");
    if ($lu == 1 && $envoyes == 0)
    {
      $encars = "<B>";
      $encarn = "</B>";
    }
    else
    {
      $encars = "";
      $encarn = "";
    }
    echo couleur_tr($i+1,'');
    if ($lu == 0)
    {
       if ($envoyes != 1)
       {
          echo "<TD align='left' valign='top'>";
          $lien1 = "admin/msg.php?numero=$numero&dl=dejalu$i&renonlu=1&ret=$ret&complement=$complement&i=$i";
          echo "<div id='dejalu$i'><A HREF=\"javascript:appelle_ajax('$lien1'); ".
          "var mon_content=document.getElementById('mien');mon_content.style.visibility='hidden'; ".
          "var mon_div=document.getElementById('dejalu$i');mon_div.innerHTML=''; ".
          "addContent_msg('$lien1');\" ".
          "onMouseOver=\"overlib('<TABLE border=0 cellspacing=2 cellpadding=6><TR>".
          "<TD align=left><font size=2>$mess_mail_renonlu</font></TD></TR></TABLE>'".
          ",ol_hpos,RIGHT,ABOVE,WIDTH,'180')\" ".
          "onMouseOut=\"nd();\"><IMG SRC='images/messagerie/icoGcheckb.gif' border='0'></A></div>";
       }
       else
          echo "<TD align='center' valign='top'><IMG SRC='images/messagerie/icoGcheckb.gif' border='0' ".
               bullet($mess_mail_lu,"","RIGHT","ABOVE",20)."></TD>";
    }
    else
    {
       if ($envoyes != 1)
       {
          echo "<TD align='left' valign='top'>";
          //$lien1 = "messagerie.php?ret=$ret&complement=$complement&numero=$numero&dejalu=1";
          $lien1 = "admin/msg.php?numero=$numero&dl=dejalu$i&dejalu=1&ret=$ret&complement=$complement&i=$i";
          //$lien1 = urlencode($lien1);
          echo "<div id='dejalu$i'><A HREF=\"javascript:appelle_ajax('$lien1'); ".
          "var mon_content=document.getElementById('mien');mon_content.style.visibility='hidden'; ".
          "var mon_div=document.getElementById('dejalu$i');mon_div.innerHTML=''; ".
          "addContent_msg('$lien1');\" ".
          "onMouseOver=\"overlib('<TABLE border=0 cellspacing=2 cellpadding=6><TR>".
          "<TD align=left><font size=2>$mess_mail_djalu</font></TD></TR></TABLE>'".
          ",ol_hpos,RIGHT,ABOVE,WIDTH,'180')\" ".
          "onMouseOut=\"nd();\"><IMG SRC='images/messagerie/icoGcheck.gif' border='0'></A></div>";
       }
       else
          echo "<TD align='center' valign='top'></TD>";
    }
    if ($fichier != "")
    {
      echo "<TD align='left' valign='top'>";
      $son_nom = substr(strrchr ( $fichier, "/" ),1);
      echo "<A href=\"$fichier\" target='_blank' title=\"$mess_piece_join : $son_nom\" ".
          " onmouseover=\"img_fj$i.src='images/messagerie/icoGtrombonb.gif';return true;\"".
          " onmouseout=\"img_fj$i.src='images/messagerie/icoGtrombon.gif'\">".
          "<IMG NAME=\"img_fj$i\" SRC=\"images/messagerie/icoGtrombon.gif\" BORDER='0' ALT=\"$mess_piece_join : $son_nom\"".
          " onLoad=\"tempImg=new Image(0,0); tempImg.src='images/messagerie/icoGtrombonb.gif'\">";
      echo "</A></TD>";
    }
    else
      echo "<TD></TD>";
    echo "<TD align='left' valign='top'>$encars$date$encarn</TD>";
    $lien = "mail.php?contacter=1&num=$envoyeur&complement=$complement";
    $lien = urlencode($lien);
    if ($envoyeur != $id_user)
      echo "<TD align='left' nowrap valign='top'><DIV id='sequence'><A HREF=\"javascript:void(0);\" ".bullet($mess_repondre,"","RIGHT","ABOVE",80).
           "onclick=\"open('trace.php?link=$lien','window','scrollbars,resizable=yes,width=680,height=520')\">$origine</A></DIV></TD>";
    else
      echo "<TD align='left' nowrap valign='top'>$encars$origine$encarn</TD>";
    $nbr_carac_sujet = strlen($sujet);
    if ($nbr_carac_sujet > 24)
        $sujet = substr($sujet,0,24)."...";
    $lien = "messagerie.php?ret=$ret&complement=$complement&lire=1&numero=$numero&complement=0&envoyes=$envoyes&corbeille=$corbeille&lu=$lu";
    $lien=urlencode($lien);
    $lien1 = "admin/msg.php?numero=$numero&dl=dejalu$i&dejalu=1&ret=$ret&complement=$complement&i=$i&notif=0";
    echo "<TD align='left' valign='top'><A HREF=\"javascript:appelle_ajax('$lien1'); ".
          "var mon_content=document.getElementById('mien');mon_content.style.visibility='hidden'; ".
          "var mon_div=document.getElementById('dejalu$i');mon_div.innerHTML=''; ".
          "addContent_msg('$lien1');\" ".
          bullet($mess_mail_lire,"","RIGHT","ABOVE",160).
         "onclick=\"open('trace.php?link=$lien','window','scrollbars,resizable=yes,width=680,height=400')\">".
         "$encars$sujet$encarn</A></TD>";
    $nbr_carac_message = strlen(strip_tags($message));
    if ($nbr_carac_message > 60)
      $message = substr(strip_tags($message),0,60)."...";
    $lien = "messagerie.php?ret=$ret&complement=$complement&lire=1&numero=$numero&complement=0&envoyes=$envoyes&corbeille=$corbeille&lu=$lu";
    $lien=urlencode($lien);
    $lien1 = "admin/msg.php?numero=$numero&dl=dejalu$i&dejalu=1&ret=$ret&complement=$complement&i=$i&notif=0";
    echo "<TD align='left' valign='top'><A HREF=\"javascript:appelle_ajax('$lien1'); ".
          "var mon_content=document.getElementById('mien');mon_content.style.visibility='hidden'; ".
          "var mon_div=document.getElementById('dejalu$i');mon_div.innerHTML=''; ".
          "addContent_msg('$lien1');\" ".
          bullet($mess_mail_lire,"","RIGHT","ABOVE",160).
         "onclick=\"open('trace.php?link=$lien','window','scrollbars,resizable=yes,width=680,height=400')\">".
         "$encars$message$encarn</A></DIV></TD>";
    if ($corbeille == 1)
    {
       $lien1 = "messagerie.php?ret=$ret&complement=$complement&numero=$numero&restaurer=1&corbeille=$corbeille";
       $lien1 = urlencode($lien1);
          echo "<TD align='center' valign='top'><A HREF=\"trace.php?link=$lien1\" ".
          "onMouseOver=\"overlib('<TABLE border=0 cellspacing=2 cellpadding=6>".
          "<TR><TD align=left><font size=2>$mess_mail_restaurer</font></TD></TR></TABLE>'".
          ",ol_hpos,LEFT,ABOVE,WIDTH,'180');\" ".
          "onMouseOut=\"nd();return true;\">".
          "<IMG SRC='images/messagerie/icoGcheck.gif' border='0'></a></TD>";
    }
    if ($envoyes != 1 && $corbeille != 1)
    {
      echo "<TD align='center' valign='center'>";
      if ($typ_user != "APPRENANT")
        echo "<TABLE width='60%'><TR><TD align='left' valign='center'>".
             "<INPUT TYPE='checkbox' name='ordre[$i]'".bulle($mess_select,"","RIGHT","ABOVE",80)."</TD>".
             "<TD align='center' valign='center'>";
      $lien1 = "messagerie.php?ret=$ret&complement=$complement&numero=$numero&supp_unique=1";
      $lien1 = urlencode($lien1);
      echo "<A href=\"javascript:void(0);\" onclick=\"javascript:return(confm('trace.php?link=$lien1'));\" title=\"$mess_env_corb\" ".
          "onmouseover=\"img_p$i.src='images/messagerie/icopticorbeilb.gif';return true;\"".
          " onmouseout=\"img_p$i.src='images/messagerie/icopticorbeil.gif'\">".
          "<IMG NAME=\"img_p$i\" SRC=\"images/messagerie/icopticorbeil.gif\" BORDER='0' ALT=\"$mess_env_corb\"".
          " onLoad=\"tempImg=new Image(0,0); tempImg.src='images/messagerie/icopticorbeilb.gif'\"></A>";
      echo " </TD></TR>";
      if ($typ_user != "APPRENANT")
        echo "</TABLE></TD></TR>";
      echo "<INPUT TYPE='HIDDEN' name='numer[$i]' value='$numer[$i]'>";
    }
    elseif ($corbeille == 1)
    {
      echo "<TD align='left' valign='top'>";
      if ($typ_user != "APPRENANT")
        echo "<INPUT TYPE='checkbox' name='ordre[$i]' ".bulle($mess_select,"","LEFT","ABOVE",60);
      $lien1 = "messagerie.php?ret=$ret&complement=$complement&numero=$numero&supp_unique=1&corbeille=$corbeille";
      $lien1 = urlencode($lien1);
      echo nbsp(5)."<A href=\"javascript:void(0);\" onclick=\"javascript:return(confm('trace.php?link=$lien1'));\" title=\"$mess_supp_def\" ".
          "onmouseover=\"img_p$i.src='images/messagerie/icoGpoubelb.gif';return true;\"".
          " onmouseout=\"img_p$i.src='images/messagerie/icoGpoubel.gif'\">".
          "<IMG NAME=\"img_p$i\" SRC=\"images/messagerie/icoGpoubel.gif\" BORDER='0' width='13' height='18' alt=\"$mess_supp_def\"".
          " onLoad=\"tempImg=new Image(0,0); tempImg.src='images/messagerie/icoGpoubelb.gif'\"></A>";
      echo "</TD></TR>".
           "<INPUT TYPE='HIDDEN' name='numer[$i]' value='$numer[$i]'>";
    }
    elseif ($envoyes == 1)
    {
      echo "<TD align='left' valign='top'>";
      if ($typ_user != "APPRENANT")
        echo "<INPUT TYPE='checkbox' name='ordre[$i]' ".bulle($mess_select,"","LEFT","ABOVE",60);
      $lien1 = "messagerie.php?ret=$ret&complement=$complement&numero=$numero&supp_unique=1&envoyes=$envoyes";
      $lien1 = urlencode($lien1);
      echo  nbsp(5)."<A href=\"javascript:void(0);\" onclick=\"javascript:return(confm('trace.php?link=$lien1'));\"".
          "onmouseover=\"img_p$i.src='images/messagerie/icoGpoubelb.gif';return true;\"".
          " onmouseout=\"img_p$i.src='images/messagerie/icoGpoubel.gif'\">".
          "<IMG NAME=\"img_p$i\" SRC=\"images/messagerie/icoGpoubel.gif\" BORDER='0' width='13' height='18'".
          " onLoad=\"tempImg=new Image(0,0); tempImg.src='images/messagerie/icoGpoubelb.gif'\"></A>";
      echo "</TD></TR>".
           "<INPUT TYPE='HIDDEN' name='numer[$i]' value='$numer[$i]'>";
    }
    else
      echo "</TR>";
    $i++;
  }
  if ($typ_user == "APPRENANT")
  {
    if ($complement == 1)
      $lien = "annonce_grp.php?full=$full&complement=$complement";
    else
      $lien = "annonce_grp.php?vient_de_menu=menu";
  }
  else
    $lien = "annonce_form.php";
  $lien = urlencode($lien);
  if ($complement == 1 || $ret == 1)
  {
    echo "</TABLE></TD></TR><TR><TD width='100%' colspan='7'>".
         "<TABLE width='100%' bgcolor='#FFFFFF'><TR><TD align=left><A HREF=\"trace.php?link=$lien\" ".
         "onmouseover=\"img_annonce.src='images/fiche_identite/boutretourb.gif';return true;\" ".
         "onmouseout=\"img_annonce.src='images/fiche_identite/boutretour.gif'\">".
         "<IMG NAME=\"img_annonce\" SRC=\"images/fiche_identite/boutretour.gif\" BORDER='0' ".
         "onLoad=\"tempImg=new Image(0,0); tempImg.src='images/fiche_identite/boutretourb.gif'\"></A></TD>";
    if ($corbeille == 1 && $nbr_req > 0)
    {
     $lien= "messagerie.php?ret=$ret&supp_envoi=1&corbeille=1&complement=$complement&vider=1&nb_supp=$nbr_req";
     $lien=urlencode($lien);
     echo "<TD align='right'>".
          "<A href=\"javascript:void(0);\" onclick=\"javascript:return(confm('trace.php?link=$lien'));\" title=\"$mess_vider_corb\" ".
          " onmouseover=\"img_inv1.src='images/messagerie/boutsuptoutb.gif';return true;\"".
          " onmouseout=\"img_inv1.src='images/messagerie/boutsuptout.gif'\">".
          "<IMG NAME=\"img_inv1\" SRC=\"images/messagerie/boutsuptout.gif\" BORDER='0' ALT=\"$mess_vider_corb\"".
          " onLoad=\"tempImg=new Image(0,0); tempImg.src='images/messagerie/boutsuptoutb.gif'\"></A>".nbsp(4)."</TD>";
    }
    echo "</TR>";
  }
  else
    echo "</TABLE></TD></TR>";
  if (($envoyes == 1 || $corbeille == 1 || ($envoyes != 1 && $corbeille != 1)) && $nbr_req > 0 && $typ_user != "APPRENANT")
  {
     echo "<TR bgcolor='#FFFFFF' height='50'><TD width='100%' align='right' valign='center'>".
          "<A href=\"javascript:void(0);\" onClick=\"CheckAll();\" Title=\"$mess_inv_sel\"".
          " onmouseover=\"img_inv1.src='images/messagerie/boutinversb.gif';return true;\"".
          " onmouseout=\"img_inv1.src='images/messagerie/boutinvers.gif'\">".
          "<IMG NAME=\"img_inv1\" SRC=\"images/messagerie/boutinvers.gif\" BORDER='0' ALT=\"$mess_inv_sel\"".
          " onLoad=\"tempImg=new Image(0,0); tempImg.src='images/messagerie/boutinversb.gif'\"></A>";
     echo "&nbsp;<A href=\"javascript:void(0);\" onclick=\"javascript:return(confm('javascript:document.form1.submit()'));\" Title=\"$mess_ag_supp\"".
          " onmouseover=\"img_supr1.src='images/messagerie/boutsupprimb.gif';return true;\"".
          " onmouseout=\"img_supr1.src='images/messagerie/boutsupprim.gif'\">".
          "<IMG NAME=\"img_supr1\" SRC=\"images/messagerie/boutsupprim.gif\" BORDER='0' ALT=\"$mess_ag_supp\"".
          " onLoad=\"tempImg=new Image(0,0); tempImg.src='images/messagerie/boutsupprimb.gif'\"></A>&nbsp;";
     echo "</TD></FORM>";
  }
  elseif ($corbeille == 1 && $typ_user == "APPRENANT" && $nbr_req > 0 && $complement != 1 && $ret != 1)
  {
     $lien= "messagerie.php?ret=$ret&supp_envoi=1&corbeille=1&complement=$complement&vider=1&nb_supp=$nbr_req";
     $lien=urlencode($lien);
     echo "<TR bgcolor='#FFFFFF' height='50'><TD width='100%' align='right' valign='center'>".
          "<A href=\"javascript:void(0);\" onclick=\"javascript:return(confm('javascript:document.form1.submit()'));\" title=\"$mess_vider_corb\"".
          " onmouseover=\"img_inv1.src='images/messagerie/boutsuptoutb.gif';return true;\"".
          " onmouseout=\"img_inv1.src='images/messagerie/boutsuptout.gif'\">".
          "<IMG NAME=\"img_inv1\" SRC=\"images/messagerie/boutsuptout.gif\" BORDER='0' ALT=\"$mess_vider_corb\"".
          " onLoad=\"tempImg=new Image(0,0); tempImg.src='images/messagerie/boutsuptoutb.gif'\"></A>&nbsp;&nbsp;&nbsp;&nbsp;</TD>";
  }
  if ($typ_user == "TUTEUR" || $typ_user == "FORMATEUR_REFERENT")
  {
     if ($vient_de_menu != 'menu')
     {
        if ($typ_user == "TUTEUR")
           $lien = "annonce_tuteur.php?affiche_toutapp=1&activee=1";
        else
           $lien = "annonce_formateur.php";
        $lien = urlencode($lien);
        echo "</TR><TR><TD width='100%' colspan='8'><TABLE width='100%' bgcolor='#FFFFFF'><TR>".
             "<TD align=left><A HREF=\"trace.php?link=$lien\" ".
             "onmouseover=\"img_annonce.src='images/fiche_identite/boutretourb.gif';return true;\" ".
             "onmouseout=\"img_annonce.src='images/fiche_identite/boutretour.gif'\">".
             "<IMG NAME=\"img_annonce\" SRC=\"images/fiche_identite/boutretour.gif\" BORDER='0' ".
             "onLoad=\"tempImg=new Image(0,0); tempImg.src='images/fiche_identite/boutretourb.gif'\"></A></TD>";
        echo "</TR></TABLE></TD></TR></TABLE></TD></TR></TABLE></TD></TR></TABLE>";
     }
  }
  else
     echo "</TR></TABLE></TD></TR></TABLE></TD></TR></TABLE></TD></TR></TABLE>";
}
else
{
  if ($typ_user == "APPRENANT")
  {
    if ($complement == 1)
      $lien = "annonce_grp.php?full=$full&complement=$complement";
    else
      $lien = "annonce_grp.php?vient_de_menu=menu";
  }
  else
    $lien = "annonce_form.php";
  $lien = urlencode($lien);
  if ($complement == 1 || $ret == 1)
  {
    echo "</TABLE></TD></TR><TR><TD width='100%' colspan='8'>".
         "<TABLE width='100%' bgcolor='#FFFFFF'><TR><TD align=left><A HREF=\"trace.php?link=$lien\" ".
         "onmouseover=\"img_annonce.src='images/fiche_identite/boutretourb.gif';return true;\" ".
         "onmouseout=\"img_annonce.src='images/fiche_identite/boutretour.gif'\">".
         "<IMG NAME=\"img_annonce\" SRC=\"images/fiche_identite/boutretour.gif\" BORDER='0' ".
         "onLoad=\"tempImg=new Image(0,0); tempImg.src='images/fiche_identite/boutretourb.gif'\"></A></TD>";
    echo "</TR></TABLE></TD></TR></TABLE></TD></TR></TABLE>";
  }
}
echo '<div id="mien" class="cms"></div>';
echo "</body></html>";
?>
