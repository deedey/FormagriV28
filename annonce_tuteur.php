<?php
if (!isset($_SESSION)) session_start();
if (!isset($_SESSION['id_user']) || $_SESSION['id_user'] == "")
{
  exit();
}
require "admin.inc.php";
require 'fonction.inc.php';
include ("include/UrlParam2PhpVar.inc.php");
require 'fonction_html.inc.php';
//require 'include/fonction_annonce.php';
require 'accueil/fonction_accueil.inc.php';
require 'langues/accueil.inc.php';
require "lang$lg.inc.php";
//include ("click_droit.txt");
dbConnect();
unset($_SESSION['typ_user']);
if (isset($_SESSION['mode_user']) && $_SESSION['mode_user'] == 'tout')
   $typ_user = GetDataField ($connect,"select util_typutil_lb from utilisateur where util_cdn='$id_user'","util_typutil_lb");
else
   $typ_user = "TUTEUR";
$_SESSION['typ_user'] = $typ_user;
$user_type=GetDataField ($connect,"select util_typutil_lb from utilisateur where util_cdn='$id_user'","util_typutil_lb");
include ('include/entete_annonce_inc.php');
$Register = base64url_encode($_COOKIE["monID"].'_'.$_COOKIE["monLogin"].'|'.str_replace('http://','',str_replace('.educagri.fr','',$adresse_http)));
//------------------------------------------------------------------------------------------------------------------
// Détermine la provenance de la connection
$ip = getenv('REMOTE_ADDR');
//if (IsIPv6($ip) == TRUE)
   $serveur = gethostbyaddr($_SERVER['REMOTE_ADDR']);
//else
//   $serveur = $_SERVER['REMOTE_ADDR'];
$ChampServeur = $serveur."-".$_COOKIE['maVille']."-".$_COOKIE['maRegion']."-".$_COOKIE['monPays'];
// fin de provenance du connecté
//insère la date, l'heure et autres données dans un fichier log

  $date_debut = date("Y/m/d");
  $madate_compare = date("Y-m-d");
  $nbj_date_compare = mysql_query ("select TO_DAYS('$madate_compare')");
  $nbj_date_comp = mysql_result ($nbj_date_compare,0);
  $heure_debut = substr($aujourdhui,11);
  $id_max = Donne_ID ($connect,"select max(log_cdn) from log");
  $verif_connex = mysql_query("SELECT * from log WHERE login ='$login' AND date_fin ='0000-00-00'");
  $result = mysql_num_rows($verif_connex);
  if ($result == 0)
  {
     if ($typ_user == "TUTEUR")
     {
      if ($depart == 1)
       $inserer=mysql_query ("INSERT INTO log
                              (log_cdn,date_debut,heure_debut,login,serveur,ip,log_agent) VALUES
                              ($id_max,'$date_debut','$heure_debut','$login','$ChampServeur','$ip',\"$agent\")");
    $der_connex = mysql_query("SELECT date_fin,heure_fin from log WHERE
                               login ='$login' AND
                               date_fin !='0000-00-00'
                               order by log_cdn asc");
    $res = mysql_num_rows($der_connex);
    if ($res == 0)
    {
      $message="<BR>$mess_verif_1fois<BR>";
      echo "<CENTER><TABLE  width='800' border='0'><TR>";
      if ($vient_de_menu == "menu")
         $lien = "admin/modifiche.php?modifier=1&num=$id_user&mafiche=1";
      else
         $lien = "admin/modifiche.php?modifier=1&num=$id_user&mafiche=1&complement=1&full=$full";
      $lien = urlencode($lien);
      echo "<TD align='right' colspan='2' width='100%'><TABLE cellspacing='15' border='0' width='100%'><TR><TD align='right'>".
           "<A href=\"trace.php?link=$lien\" title=\"$mess_menu_profil\" ".
           "onmouseover=\"img_fiche.src='images/fiche2b.gif';return true;\" ".
           "onmouseout=\"img_fiche.src='images/fiche2.gif'\">";
      echo "<IMG NAME=\"img_fiche\" SRC=\"images/fiche2.gif\" BORDER='0' ".
           "onLoad=\"tempImg=new Image(0,0); tempImg.src='images/fiche2b.gif'\"></A></TD>".
           "<TD valign='middle' align='left'>";
      echo "<Font size='5'><B>$mess_bonjour, $majuscule</B></FONT><br /></TD></TR></TABLE></TD></TR>";
      if ($depart == 1)
      {
        echo "<TR><TD><CENTER><TABLE width='100%' cellpadding='20' cellspacing='20'>";
        echo "<TR><TD align='left' colspan='2'><font size='3' Color='#999999'><B>$mess_insc_type ".strtolower($mess_typ_tut).
             "</B></FONT><BR><font size='2' Color='#999999'><B>$aff_date, $message</B></font></TD></TR></TABLE></TD></TR>";
      }
   $req_msg = mysql_query("SELECT * from message where
                              ((msg_dhdeb_dt < '$madate_compare' OR msg_dhdeb_dt = '$madate_compare') AND
                              (msg_dhfin_dt = '$madate_compare' OR msg_dhfin_dt > '$madate_compare')) AND
                              msg_tous_on=1");
   $nb_msg = mysql_num_rows($req_msg);
   $g = 0;
   if ($nb_msg > 0)
   {
        echo "<tr><td align='left' style=\"background-color:#edebec;\"><table border=0><tr><td valign='left'>".
             "<IMG SRC=\"images/ecran-annonce/gus01.gif\" border='0' title ='$mess_avertis'></TD>".
             "<TD valign='middle'>";
        while ($g < $nb_msg){
          $id_msg = mysql_result($req_msg,$g,"msg_cdn");
          $contenus = mysql_result($req_msg,$g,"msg_contenu_cmt");
          $contenu = html_entity_decode($contenus,ENT_QUOTES,'ISO-8859-1');
          $auteur = mysql_result($req_msg,$g,"msg_auteur_no");
          echo "$contenu<br />";
          $g++;
        }
        echo"</td></tr></table></td></tr>";
   }
      $mon_nom = $majuscule;
      include('accueil/acces_alrt.inc.php');
      /************************************** Inclusion de l'appel à la fenêtre "Quoi de neuf "***********************/
      echo "<tr height='30'>";
      echo "<td>";
      $mon_fichier = "events.php";
      $le_titwin = $msg_WNS;
      $id = 2;
      include("accueil/appel_js.php");
      echo aide_div('suivi_pedagogique_tut',10,0,0,0);
      echo "<div><a href='appliMobile.php?&keepThis=true&TB_iframe=true&height=400&width=550' class='thickbox'
           title='Application mobile de Formagri' name='Application mobile de Formagri' >
           <div class='bouton_vert' onMouseOver=\"\$(this).removeClass();\$(this).addClass('bouton_hover');\"
           onMouseOut=\"\$(this).removeClass();\$(this).addClass('bouton_vert');\">Formagri mobile</div></a></div>";
      echo "</td></tr>";
       /***************************************** fin de l'inclusion *************************************************/
      if ($nomb_rdv > 0 || $nbr_mess > 0)
         echo "<TR height='4'><TD></TD></TR></TABLE>";
      else
         echo "<TR height='4'><TD></TD></TR></TABLE>";
      echo "<TABLE bgColor='#298CA0' cellspacing='2' cellpadding='0' width='860' ><TR><TD width='100%'>";
      echo "<TABLE bgColor='#FFFFFF' cellspacing='1' cellpadding='0' width='100%'>";
    }
    else
    {
      $i=$res-1;
      $dates= mysql_result($der_connex,$i,"date_fin");
      $heure= mysql_result($der_connex,$i,"heure_fin");
      $ch_date = explode ("-",$dates);
      $date = "$ch_date[2]/$ch_date[1]/$ch_date[0]";
      $message="$mess_verif_der $date ($heure)";
      echo "<CENTER><TABLE border='0' width='860'><TR>";
      $lien = "admin/modifiche.php?modifier=1&num=$id_user&mafiche=1";
      $lien = urlencode($lien);
      echo "<TD align='center' colspan='2' width='100%'><TABLE cellspacing='15' border='0' width='60%'>";
      echo "<TR><TD><A href=\"trace.php?link=$lien\" title=\"$mess_menu_profil\" ".
           "onmouseover=\"img_fiche.src='images/fiche2b.gif';return true;\" ".
           "onmouseout=\"img_fiche.src='images/fiche2.gif'\">";
      echo "<IMG NAME=\"img_fiche\" SRC=\"images/fiche2.gif\" BORDER='0' ".
           "onLoad=\"tempImg=new Image(0,0); tempImg.src='images/fiche2b.gif'\"></A></TD>";
      echo "<TD valign='center'><font size=5 Color='#003366'><B>".
           "$mess_bonjour, $majuscule</B></font><P></TD></TR></TABLE></TD></TR>";
      if ($depart == 1)
      {
        echo "<TR><TD align='left' colspan='2'><font size='3' Color='#003366'><B>$mess_insc_type ".strtolower($mess_typ_tut).
             "</B></FONT><BR><font size='2' Color='#999999'><B>$aff_date, $message</B></font></TD></TR>";
      }
   $req_msg = mysql_query("SELECT * from message where
                              ((msg_dhdeb_dt < '$madate_compare' OR msg_dhdeb_dt = '$madate_compare') AND
                              (msg_dhfin_dt = '$madate_compare' OR msg_dhfin_dt > '$madate_compare')) AND
                              msg_tous_on=1");
   $nb_msg = mysql_num_rows($req_msg);
   $g = 0;
   if ($nb_msg > 0)
   {
        echo "<tr><td align='left' style=\"background-color:#edebec;\"><table border=0><tr><td valign='left'>".
             "<IMG SRC=\"images/ecran-annonce/gus01.gif\" border='0' title ='$mess_avertis'></TD>".
             "<TD valign='middle'>";
        while ($g < $nb_msg){
          $id_msg = mysql_result($req_msg,$g,"msg_cdn");
          $contenus = mysql_result($req_msg,$g,"msg_contenu_cmt");
          $contenu = html_entity_decode($contenus,ENT_QUOTES,'ISO-8859-1');
          $auteur = mysql_result($req_msg,$g,"msg_auteur_no");
          echo "$contenu<br />";
          $g++;
        }
        echo"</td></tr></table></td></tr>";
   }
      echo"</TABLE>";
      echo "<TABLE cellspacing='0'width='860' border='0'>";
      $req_mess = mysql_query ("SELECT * from messagerie where
                                id_user = $id_user AND
                                lu = '1' AND
                                supprime = '0'");
      $nbr_mess= mysql_num_rows($req_mess);
      if ($nbr_mess > 0)
      {
         $lien="messagerie.php?vient_de_menu=$vient_de_menu";
         $lien=urlencode($lien);
         echo "<TR><TD align='left' valign='center'>".
              "<span style=\"float:left;padding-right:8px;\"><A href=\"trace.php?link=$lien\" ".
              "onmouseover=\"img_lettre1.src='images/ecran-annonce/icolettre.gif';return true;\"".
              " onmouseout=\"img_lettre1.src='images/ecran-annonce/icolettre.gif'\">".
              "<IMG NAME=\"img_lettre1\" SRC=\"images/ecran-annonce/icolettre.gif\" BORDER='0'".
              " onLoad=\"tempImg=new Image(0,0); tempImg.src='images/ecran-annonce/icolettre.gif'\"></A></span>".
              "<span style=\"float:left;\"><A href=\"trace.php?link=$lien\">$vous_avez $nbr_mess ".
              strtolower($mess_mailu_no)."</A></span></TD></TR>";
      }
      $dt_jour = date("Y-m-d");
      $req_rdv = mysql_query("SELECT * from rendez_vous where
                              rdv_tuteur_no = $id_user and
                              rdv_date_dt = '$dt_jour'
                              ORDER BY rdv_apprenant_no,rdv_creneau_nb ASC");
      $nomb_rdv = mysql_num_rows($req_rdv);
      $date =date("d/m/Y H:i:s",time());
      list($dt,$heure) =  explode (" ",$date);
      list($hh,$mm,$ss) = explode(':',$heure);
      if ($nomb_rdv > 0)
      {
         require 'agenda.inc.php';
         $rdv =0;
         while ($rdv < $nomb_rdv)
         {
           $creneau = mysql_result($req_rdv,$rdv,"rdv_creneau_nb");
           $cr = Horaire($creneau);
           list($valeur,$reste,$rest) = explode("h",$cr);
           if ($hh <= $valeur)
             $nb_final++;
          $rdv++;
         }
      }
      if ($nb_final > 0)
      {
        echo "<TR><TD align='left' valign='center' width='22'><A HREF=\"agenda.php?tut=1\"".
             " onmouseover=\"img$i.src='images/ecran-annonce/icotutob.gif';return true;\" ".
             "onmouseout=\"img$i.src='images/ecran-annonce/icotuto.gif'\">".
             "<IMG NAME=\"img$i\" SRC=\"images/ecran-annonce/icotuto.gif\" BORDER='0' ".
             "onLoad=\"tempImg=new Image(0,0); tempImg.src='images/ecran-annonce/icotutob.gif'\"></A></TD>";
        echo "<TD align='left' valign='bottom'><DIV id='titre'>&nbsp;&nbsp;".
             "<A HREF=\"agenda.php?tut=1\">$vous_avez $nb_final ".strtolower($mess_ag_rdv_tut)."</A></DIV></TD></TR>";
      }
      if ($nomb_rdv > 0 || $nbr_mess > 0)
         echo "<TR height='4'><TD></TD></TR>";
      else
         echo "<TR height='4'><TD></TD></TR>";
      }
      
   //include('accueil/acces_alrt.inc.php');
   /************************************** Inclusion de l'appel à la fenêtre "Quoi de neuf "**********************
   echo "<tr height='30'>";
   echo "<td>";
   $mon_fichier = "events.php";
   $le_titwin = $msg_WNS;
   $id = 2;
   include("accueil/appel_js.php");
   echo aide_div('accueil',10,0,0,0);
   echo "<div><a href='appliMobile.php?&keepThis=true&TB_iframe=true&height=400&width=550' class='thickbox'
           title='Application mobile de Formagri' name='Application mobile de Formagri' >
           <div class='bouton_vert' onMouseOver=\"\$(this).removeClass();\$(this).addClass('bouton_hover');\"
           onMouseOut=\"\$(this).removeClass();\$(this).addClass('bouton_vert');\">Formagri mobile</div></a></div></td></tr>";*/
   echo "</TABLE>";
  /************************************** Inclusion de l'appel à la fenêtre "Quoi de neuf "***********************/
     }
   }
   else
   {
     if ($complement != 1)
     {
       $i=0;
       while ($i  < $result)
       {
        $dates= mysql_result($verif_connex,$i,"date_fin");
        $heure= mysql_result($verif_connex,$i,"heure_fin");
        $date_d= mysql_result($verif_connex,$i,"date_debut");
        $heure_d= mysql_result($verif_connex,$i,"heure_debut");
        $adr_ip= mysql_result($verif_connex,$i,"ip");
        $ch_date = explode ("-",$dates);
        $date = "$ch_date[2]/$ch_date[1]/$ch_date[0]";
        if (isset($date_debut)){
          $ch_date_hier = explode ("/",$date_debut);
          $date_hier = "$ch_date_hier[2]/$ch_date_hier[1]/$ch_date_hier[0]";
        }
        $nbj_date = mysql_query ("select TO_DAYS('$date_d')");
        $nbj_date_lue = mysql_result ($nbj_date,0);
        $diff_dates = $nbj_date_comp - $nbj_date_lue;
        if ($depart == 1 && $diff_dates <= 1){
          $req_trace = mysql_query("SELECT MAX(trace_cdn) from trace WHERE
                                    trace_login_lb = \"$login\" AND
                                    trace_lien_lb != \"annonce_tuteur.php?depart=1\" AND
                                    trace_date_dt = '$date_d'");
          $nb_trc = mysql_num_rows($req_trace);
          if ($nb_trc > 0)
          {
           $id_trc = mysql_result($req_trace,0);
           $date_trc = GetDataField ($connect,"select trace_date_dt from trace WHERE trace_cdn  = $id_trc","trace_date_dt");
           $time_trc = GetDataField ($connect,"select trace_heure_dt from trace WHERE trace_cdn  = $id_trc","trace_heure_dt");
           if ($time_trc > $heure_d)
           {
           // routine calcul minutes
             $ch_heure_fin = explode (":",$time_trc);
             $hour_fin = $ch_heure_fin[0];
             $minutes_fin = $ch_heure_fin[1];
             $ch_heure_deb = explode (":",$heure_d);
             $heure_deb = $ch_heure_deb[0];
             $minutes_deb = $ch_heure_deb[1];
             $dif_heures = $hour_fin-$heure_deb;
             if ($dif_heures == 0)
               $minutes_plus = 0;
             else
               $minutes_plus = $dif_heures*60;
             if (($minutes_fin > $minutes_deb) || ($minutes_fin == $minutes_deb)){
               $minutes = $minutes_fin-$minutes_deb;
               $minutes_rest = $minutes;
             }
             else
             {
               $dif_heures--;
               $minutes_plus=60-$minutes_deb+$minutes_fin;
               $minutes = $dif_heures*60;
               $minutes_rest = $minutes_plus;
             }
             $minutes_total = $minutes+$minutes_plus;
             $nb_deb = mysql_query ("select TO_DAYS('$date_d')");
             $nb_j_deb = mysql_result ($nb_deb,0);
             $nb_fin = mysql_query ("select TO_DAYS('$date_trc')");
             $nb_j_fin = mysql_result ($nb_fin,0);
             if ($nb_j_deb < $nb_j_fin)
               $minutes_total = 1440 + $minutes_total;
             if ($minutes_total == 0)
               $minutes_total = 1;
              //fin calcul minutes
             $inserer=mysql_query("UPDATE log SET
                                   date_fin ='$date_trc',heure_fin ='$time_trc',duree=$minutes_total WHERE
                                   login='$login' AND
                                   date_debut='$date_d' AND
                                   heure_debut ='$heure_d' AND
                                   date_fin ='0000-00-00'");// '
           }
           else
             $inserer=mysql_query("UPDATE log SET
                                   date_fin ='$date_trc',heure_fin ='$time_trc',duree=0 WHERE
                                   login='$login' AND
                                   date_debut='$date_d' AND
                                   heure_debut ='$heure_d' AND
                                   date_fin ='0000-00-00'");// '
          }
        }
        if ($depart == 1 && $diff_dates > 1)
           $inserer=mysql_query("UPDATE log SET
                                 date_fin ='$date_d',heure_fin ='$heure_d',duree=0 WHERE
                                 login='$login' AND
                                 date_debut='$date_d' AND
                                 heure_debut ='$heure_d' AND
                                 date_fin ='0000-00-00'");// '
      $i++;
      }
      $id_max = Donne_ID ($connect,"select max(log_cdn) from log");
      if ($depart == 1)
         $inserer=mysql_query ("INSERT INTO log
                                (log_cdn,date_debut,heure_debut,login,serveur,ip,log_agent) VALUES
                                ($id_max,'$date_debut','$heure_debut','$login','$ChampServeur','$ip',\"$agent\")");
         //      include 'form_deb_sans.txt';
   if ($user_type == "TUTEUR")
   {
      echo "<CENTER><TABLE cellspacing='5' border='0' width='860'>";
      $lien = "admin/modifiche.php?modifier=1&num=$id_user&mafiche=1&vient_de_menu=$vient_de_menu";
      $lien = urlencode($lien);
      echo "<TR><TD align='center' colspan='2'><TABLE cellspacing='15' border='0' width='60%'>".
           "<TR><TD align='right'><A href=\"trace.php?link=$lien\" title=\"$mess_menu_profil\" ".
           "onmouseover=\"img_fiche.src='images/fiche2b.gif';return true;\" ".
           "onmouseout=\"img_fiche.src='images/fiche2.gif'\">";
      echo "<IMG NAME=\"img_fiche\" SRC=\"images/fiche2.gif\" BORDER='0' ".
           "onLoad=\"tempImg=new Image(0,0); tempImg.src='images/fiche2b.gif'\"></A></TD><TD valign='center'>";
      if ($depart == 1)
        echo "<font size=5 ><B>$mess_bonjour, $majuscule</B></font><P></TD></TR></TABLE></TD></TR>";
      else
        echo "<font size=5><B>$majuscule</B></font><P></TD></TR></TABLE></TD></TR>";
   $req_msg = mysql_query("SELECT * from message where
                              ((msg_dhdeb_dt < '$madate_compare' OR msg_dhdeb_dt = '$madate_compare') AND
                              (msg_dhfin_dt = '$madate_compare' OR msg_dhfin_dt > '$madate_compare')) AND
                              msg_tous_on=1");
   $nb_msg = mysql_num_rows($req_msg);
   $g = 0;
   if ($nb_msg > 0)
   {
        echo "<tr><td align='left' style=\"background-color:#edebec;\"><table border=0><tr><td valign='left'>".
             "<IMG SRC=\"images/ecran-annonce/gus01.gif\" border='0' title ='$mess_avertis'></TD>".
             "<TD valign='middle'>";
        while ($g < $nb_msg){
          $id_msg = mysql_result($req_msg,$g,"msg_cdn");
          $contenus = mysql_result($req_msg,$g,"msg_contenu_cmt");
          $contenu = html_entity_decode($contenus,ENT_QUOTES,'ISO-8859-1');
          $auteur = mysql_result($req_msg,$g,"msg_auteur_no");
          echo "$contenu<br />";
          $g++;
        }
        echo"</td></tr></table></td></tr>";
   }
      echo"</TABLE>";
      echo "<TABLE cellspacing='0' width='860' border='0'>";
      $req_mess = mysql_query ("SELECT * from messagerie where
                                id_user = $id_user AND
                                lu = '1' AND
                                supprime = '0'");
      $nbr_mess= mysql_num_rows($req_mess);
      if ($nbr_mess > 0)
      {
         $lien="messagerie.php?vient_de_menu=$vient_de_menu";
         $lien=urlencode($lien);
         echo "<TR><TD valign='center'>".
              "<span style=\"float:left;padding-right:8px;\"><A href=\"trace.php?link=$lien\" ".
              "onmouseover=\"img_lettre1.src='images/ecran-annonce/icolettreb.gif';return true;\"".
              " onmouseout=\"img_lettre1.src='images/ecran-annonce/icolettre.gif'\">".
              "<IMG NAME=\"img_lettre1\" SRC=\"images/ecran-annonce/icolettre.gif\" BORDER='0'".
              " onLoad=\"tempImg=new Image(0,0); tempImg.src='images/ecran-annonce/icolettreb.gif'\"></A></span>".
              "<span style=\"float:left;\"><A href=\"trace.php?link=$lien\">$vous_avez $nbr_mess ".
              strtolower($mess_mailu_no)."</A></span></TD></TR>";
      }
      $dt_jour = date("Y-m-d");
      $req_rdv = mysql_query("SELECT * from rendez_vous where
                              rdv_tuteur_no = $id_user and
                              rdv_date_dt = '$dt_jour'
                              ORDER BY rdv_apprenant_no,rdv_creneau_nb ASC");
      $nomb_rdv = mysql_num_rows($req_rdv);
      $date =date("d/m/Y H:i:s",time());
      list($dt,$heure) =  explode (" ",$date);
      list($hh,$mm,$ss) = explode(':',$heure);
      if ($nomb_rdv > 0)
      {
         require 'agenda.inc.php';
         $rdv =0;
         while ($rdv < $nomb_rdv)
         {
           $creneau = mysql_result($req_rdv,$rdv,"rdv_creneau_nb");
           $cr = Horaire($creneau);
           list($valeur,$reste,$rest) = explode("h",$cr);
           if ($hh <= $valeur)
             $nb_final++;
          $rdv++;
         }
      }
      if ($nb_final > 0)
      {
        echo "<TR><TD align='left' valign='center' width='22'><A HREF=\"agenda.php?tut=1\"".
             " onmouseover=\"img$i.src='images/ecran-annonce/icotutob.gif';return true;\" ".
             "onmouseout=\"img$i.src='images/ecran-annonce/icotuto.gif'\">".
             "<IMG NAME=\"img$i\" SRC=\"images/ecran-annonce/icotuto.gif\" BORDER='0' ".
             "onLoad=\"tempImg=new Image(0,0); tempImg.src='images/ecran-annonce/icotutob.gif'\"></A></TD>";
        echo "<TD align='left' valign='bottom'><DIV id='titre'>&nbsp;&nbsp;".
             "<A HREF=\"agenda.php?tut=1\">$vous_avez $nb_final ".strtolower($mess_ag_rdv_tut)."</A></DIV></TD></TR>";
      }
   $mon_nom = $majuscule;
   include('accueil/acces_alrt.inc.php');
   /************************************** Inclusion de l'appel à la fenêtre "Quoi de neuf "***********************/
   echo "<TR height='30'>";
   echo "<td>";
   $mon_fichier = "events.php";
   $le_titwin = $msg_WNS;
   $id = 2;
   include("accueil/appel_js.php");
   echo aide_div('accueil',10,0,0,0);
   echo "<div><a href='appliMobile.php?&keepThis=true&TB_iframe=true&height=400&width=550' class='thickbox'
           title='Application mobile de Formagri' name='Application mobile de Formagri' >
           <div class='bouton_vert' onMouseOver=\"\$(this).removeClass();\$(this).addClass('bouton_hover');\"
           onMouseOut=\"\$(this).removeClass();\$(this).addClass('bouton_vert');\">Formagri mobile</div></a></div>";
   echo "</td></tr>";
   /***************************************** fin de l'inclusion *************************************************/
      if ($nomb_rdv > 0 || $nbr_mess > 0)
         echo "<TR height='4'><TD></TD></TR></TABLE>";
      else
         echo "<TR height='4'><TD></TD></TR></TABLE>";
    }// fin if typ_user == tuteur
   }// fin if complement
}//else premier
//gestion du tutorat de groupe
  $log_der = Donne_ID($connect,"SELECT max(log_cdn) from log WHERE login='$login' AND date_fin !='0000-00-00'");
  if ($log_der != 1)
  {
     $log = $log_der-1;
     $date_der = GetDataField ($connect,"select date_fin from log WHERE log_cdn = $log","date_fin");
     $heure_der = GetDataField ($connect,"select heure_fin from log WHERE log_cdn = $log","heure_fin");
     $date_compare =  $date_der." ".$heure_der;
  }
  $req_grp_tut = mysql_query ("select groupe.grp_cdn from groupe,utilisateur_groupe WHERE
                               groupe.grp_tuteur_no = $id_user AND
                               utilisateur_groupe.utilgr_groupe_no = groupe.grp_cdn
                               order by grp_nom_lb");
  $nomb_grp_tut = mysql_num_rows($req_grp_tut);
  $reqtut = mysql_query ("select * from tuteur,utilisateur where
                          tuteur.tut_tuteur_no = $id_user AND
                          tuteur.tut_apprenant_no = utilisateur.util_cdn
                          ORDER BY utilisateur.util_nom_lb");
  $nomb_app = mysql_num_rows($reqtut);
  if ($nomb_grp_tut > 0 && $nomb_app > 0 && $activee == 1)
  {
      echo "<TABLE bgColor='#298CA0' cellspacing='2' cellpadding='0' width='98%'>";
      echo "<TR><TD><TABLE bgColor='#FFFFFF' cellspacing='0' cellpadding='0' width='100%'>";
      echo "<TR><TD><TABLE border='0' width='100%' cellspacing='1' cellpadding='6'>";
      echo "<TR height='39'><TD background=\"images/fond_titre_table.jpg\" width='85%' valign='center'>".
           "<FONT COLOR= 'white' SIZE='2'><B>$mess_desire</B></FONT></TD>";
      echo "<TD background=\"images/fond_titre_table.jpg\" width='15%' valign='center' align='center'>".
           "<FONT COLOR= 'white' SIZE='2'><B>$mess_demarrer</B></FONT></TD>";
      echo "</TR>";
      echo aide_simple("tuteur");
      $lien = "annonce_tuteur.php?supervision=1&affiche_groupe=3";
      $lien = urlencode($lien);
      echo "<TR bgcolor='#F4F4F4' height='40'><TD><A HREF=\"trace.php?link=$lien\" target='main'> $mess_super_tutor</A></TD>";
      echo "<TD align='center'><A HREF=\"trace.php?link=$lien\" target='main'".
           " onmouseover=\"img1.src='images/ecran-annonce/icoGgob.gif';return true;\" ".
           "onmouseout=\"img1.src='images/ecran-annonce/icoGgo.gif'\">".
           "<IMG NAME=\"img1\" SRC=\"images/ecran-annonce/icoGgo.gif\" BORDER='0' ".
           "onLoad=\"tempImg=new Image(0,0); tempImg.src='images/ecran-annonce/icoGgob.gif'\"></A></TD></TR>";
      $lien = "annonce_tuteur.php?tutorat_app=1&affiche_toutapp=1";
      $lien = urlencode($lien);
      echo "<TR bgcolor='#FFFFFF' height='40'><TD><A HREF=\"trace.php?link=$lien\" target='main'> $mess_app_tutor</A></TD>";
      echo "<TD align='center'><A HREF=\"trace.php?link=$lien\" target='main'".
           " onmouseover=\"img1.src='images/ecran-annonce/icoGgob.gif';return true;\" ".
           "onmouseout=\"img1.src='images/ecran-annonce/icoGgo.gif'\">".
           "<IMG NAME=\"img1\" SRC=\"images/ecran-annonce/icoGgo.gif\" BORDER='0' ".
           "onLoad=\"tempImg=new Image(0,0); tempImg.src='images/ecran-annonce/icoGgob.gif'\"></A></TD></TR>";
      echo "</TABLE></TD></TR></TABLE></TD></TR></TABLE>";
      exit();
  }
  elseif ($nomb_grp_tut == 0 && $nomb_app > 0)
    $tutorat_app = 1;
  elseif ($nomb_grp_tut > 0 && $nomb_app == 0)
    $supervision = 1;
  elseif ($nomb_grp_tut == 0 && $nomb_app == 0)
  {
      $req_grpe_tut = mysql_query ("select groupe.grp_cdn from groupe WHERE groupe.grp_tuteur_no = $id_user");
      $nomb_grpe_tut = mysql_num_rows($req_grpe_tut);
      if ($nomb_grpe_tut > 0)
         echo "Il semble que vous soyez tuteur de $nomb_grpe_tut formation(s) ne comportant aucun apprenant<br /> ".
              "Vous n'y avez donc aucune raison d'y accéder.";
      exit;
  }

  if ($supervision == 1)
  {
      $req_grp_tut = mysql_query ("select * from groupe WHERE
                                   grp_tuteur_no = $id_user
                                   order by grp_nom_lb");
      $nomb_grp_tut = mysql_num_rows($req_grp_tut);
      if ($nomb_grp_tut > 0)
      {
         $i = 0;
         $passage = 0;
         $deja_passe = 0;
         while ($itemGrp = mysql_fetch_object($req_grp_tut))
         {
             $id_grp = $itemGrp->grp_cdn;
             $id_classe =$itemGrp->grp_classe_on;
             $nom_grp =$itemGrp->grp_nom_lb;
             $resp_grp = $itemGrp->grp_resp_no;
             $carac_grp = strlen($nom_grp);
             if ($carac_grp > 33)
                $nom_grp1 = substr($nom_grp,0,31)."..";
             else
                $nom_grp1 = $nom_grp;
             $desc_grp = $itemGrp->grp_formobject_lb;
             $desc_grp =str_replace(chr(146),chr(39),$desc_grp);
             $desc1_grp =addcslashes($desc_grp,"\0..\47!@\176..\255");
             $req_grp_app = mysql_query ("select count(*) from utilisateur_groupe WHERE utilgr_groupe_no = $id_grp");
             $nomb_grp_app = mysql_result($req_grp_app,0);
             if ($nomb_grp_app == 0)
             {
               $i++;
               continue;
               $passage++;
             }
             $flec1 = "<img src='images/gest_parc/flech02.gif' border=0>";
             $flec0 = "<img src='images/gest_parc/flechv01.gif' border=0>";
             if ($affiche_grp[$id_grp] == 0)
             {
                  $fleche = $flec0;
                  $alte = $mess_open;
                  $lechoix = 1;
             }
             else
             {
                  $fleche = $flec1;
                  $alte = $mess_close;
                  $lechoix = 0;
             }
             if ($affiche_groupe == 3)
             {
                $chx = 0;
                $mess_aff = $mess_replier;
             }
             else
             {
                $chx = 3;
                $mess_aff = $mess_deplier;
             }
             if ($passage == 0 && $deja_passe == 0)
             {
               $deja_passe++;
               echo "<CENTER><TABLE bgColor='#298CA0' cellspacing='2' cellpadding='0' width='98%' border=0><TR><TD>";
               echo "<TABLE bgColor='#FFFFFF' cellspacing='1' cellpadding='0' width='100%' border=0>";
               echo "<TR><TD background=\"images/fond_titre_table.jpg\" height='40' align='center' colspan='6' valign='center'>".
                    "<Font size='3' color='#FFFFFF'><B>$mess_suivi_grp_tut</B></FONT></TD></TR>";
               if ($nomb_grp_tut > 0)
               {
                   $lien = "annonce_tuteur.php?supervision=1&affiche_groupe=$chx";
                   $lien = urlencode($lien);

                   echo "<tr><td colspan='2'><table border='0'><tr><td align='left' ".
                        "valign='center' style=\"padding: 6px;\" width='70%'>";
                   echo "<A href=\"trace.php?link=$lien\" class='bouton_new'>$mess_aff</A>".
                   aide_div("tuteur_formation",8,0,0,0)."</td>";
                   echo "</td></tr></table></td></tr>";
               }
             }
             if ($affiche_grp[$id_grp] == 1 || $affiche_groupe == 3)
                echo "<TR><TD colspan='6' style=\"padding-left: 3px;padding-right: 3px;\">".
                      "<div id='tut_separe' class='individ'><table border='0' width='100%'>";
             else
                echo "<TR><TD colspan='6'><table border='0' width='100%'>";
             $lien = "annonce_tuteur.php?supervision=1&affiche_groupe=0&affiche_grp[$id_grp]=$lechoix";
             $lien = urlencode($lien);
             echo couleur_tr($i+1,30);
             echo "<TD valign='center' align='center' width='2%' nowrap><A HREF=\"trace.php?link=$lien\" ".
                   bulle($alte,"","RIGHT","",50)."$fleche &nbsp;</A></TD>";
             echo "<TD width='25%'>";
             $grp_parc = mysql_query ("SELECT gp_parc_no from groupe_parcours where gp_grp_no =$id_grp ORDER BY gp_ordre_no");
             $nb_f = mysql_num_rows ($grp_parc);
             if ($nb_f >0 )
             {
                $liste_parc ="<FONT COLOR=marroon><B><U>$mess_list_parc_grp</U></B></FONT><BR>";
                $nnn = 0;
                while ($nnn < $nb_f)
                {
                  $cc = $nnn+1;
                  $num_parc = mysql_result($grp_parc,$nnn,"gp_parc_no");
                  $nom_parcours = getdatafield ($connect,"SELECT parcours_nom_lb from parcours where parcours_cdn = $num_parc","parcours_nom_lb");
                  $nom_parc = str_replace("'","\'",$nom_parcours);
                  $liste_parc .="$cc - $nom_parc<BR>";
                $nnn++;
                }
                echo "<A href=\"groupe.php?n=$id_grp&keepThis=true&TB_iframe=true&height=325&width=600\" ".
                     "class='thickbox' onMouseOver=\"overlib('$liste_parc',ol_hpos,ABOVE,WIDTH,'220',DELAY,'800',CAPTION,".
                     " '<center>$mpr_presc</center>')\" onMouseOut=\"nd()\">$nom_grp</A></TD>";
             }
             else
                echo "<A href=\"groupe.php?n=$id_grp&keepThis=true&TB_iframe=true&height=340&width=650\" ".
                     "class='thickbox'>$nom_grp</A></FONT></TD>";
             $requete = "SELECT utilisateur.util_cdn,utilisateur.util_email_lb from
                         utilisateur,utilisateur_groupe where
                         utilisateur.util_typutil_lb = 'APPRENANT' and
                         utilisateur.util_cdn = utilisateur_groupe.utilgr_utilisateur_no and
                         utilisateur_groupe.utilgr_groupe_no = $id_grp";
             $user_query = mysql_query ("$requete");
             $nb_user = mysql_num_rows ($user_query);
             if ($nb_user == 0)
               echo "<TD width='18%'>&nbsp;</TD>";
             else
             {
               $j = 0;
               while ($j < $nb_user)
               {
                  $id_app = mysql_result($user_query,$j,"util_cdn");
                  $id_email = mysql_result($user_query,$j,"util_email_lb");
                  if ($j == ($nb_user-1))
                     $send_to .=$id_app;
                  else
                     $send_to .= "$id_app,";
                  $j++;
               }
               $vers = "$mess_mail_app $mess_menu_gestion_grp $nom_grp";
               $message_mail ="<CENTER><Font size=2>$mess_mail_avert $vers </FONT></CENTER>";
                 $lien= "annuaire.php?vientde=annonce&id_grp=$id_grp&superviseur=1";//&entantque=tut
                 $lien = urlencode($lien);
                 echo "<TD  width='18%' valign='center' align='left'><A HREF=\"javascript:void(0);\"".
                       " onclick=\"open('trace.php?link=$lien','window','scrollbars=no,resizable=yes,width=680,height=520')\"".
                       " title=\"$mess_mail_avert $mess_mail_cert_app\" ".
                       " onmouseover=\"img_lettre3$id_grp.src='images/ecran-annonce/icolettrebw.gif';return true;\"".
                       " onmouseout=\"img_lettre3$id_grp.src='images/ecran-annonce/icolettrew.gif'\">".
                       "<IMG NAME=\"img_lettre3$id_grp\" SRC=\"images/ecran-annonce/icolettrew.gif\" ".
                       "BORDER='0' ALT=\"$mess_mail_avert $vers\"".
                       " onLoad=\"tempImg=new Image(0,0); tempImg.src='images/ecran-annonce/icolettrebw.gif'\"></A></TD>";
             }
             $grp_parc = mysql_query("SELECT gp_parc_no from groupe_parcours where gp_grp_no =$id_grp ORDER BY gp_ordre_no");
             $nb_f = mysql_num_rows($grp_parc);
             $numWkG = 10000+$id_grp;
             $nbWkGrp = mysql_num_rows(mysql_query("select * from wikiapp where wkapp_seq_no=$numWkG"));
             if ($nb_f > 0 || $nbWkGrp > 0)
             {
                           echo "<td nowrap width=20%>";
                           if ($nb_f > 0)
                           {
                               $lien = "MindMapper.php?id_grp=$id_grp";
                               echo "<div style='clear:both;float:left;'>$bouton_gauche<a href=\"$lien\" ".
                                    "title='Voir le schéma de cette formation'>";
                               echo "MindMap</A>$bouton_droite</div>";
                           }
                           // Debut Wiki
                           if ($nbWkGrp > 0)
                           {
                                $titrer = "Wiki(s)";
                                $lien = "wiki/wikiGrp.php?id_seq=$numWkG&id_parc=10000&id_grp=$id_grp&keepThis=true&TB_iframe=true&height=260&width=550";
                                echo "<div style='float:right;margin-left:12px;'>$bouton_gauche<A HREF=\"$lien\"  class='thickbox'".
                                     " name='Wiki(s) de la formation ' title='Documents en ligne communs à cette formation'>".
                                     "$titrer</A>$bouton_droite</div>";
                                $titrer ='';
                           }
                           $wk = 1;
                           echo "</td>";
             }
             else
             {
                           echo "<td nowrap width=20%>&nbsp;</td>";
                           $wk = 0;
             }
             $req_presc_grp = mysql_query("SELECT count(presc_cdn) from prescription_$id_grp");
             $nomb_presc_grp = mysql_result($req_presc_grp,0);
             if ($nomb_presc_grp > 0)
             {
                $lien = "lancement.php?numero_groupe=$id_grp&id_grp=$id_grp&groupe=1&hgrp=$hgrp&entantqueobs=1&entantquetut=";
                $lien = urlencode($lien);
                $titrer =$mpr_consult_grp." ".$nom_grp;
                echo "<TD align='center' valign='center' width='15%'>$bouton_gauche<A HREF=\"trace.php?link=$lien\"".
                      bulle($titrer,"","CENTER","ABOVE",250)."$mess_aventi</A>$bouton_droite</TD>";
                $titrer='';
             }
             else
                echo "<TD align='center' valign='center' width='15%'>&nbsp;</TD>";
             $lien_rp = "modif_rep_fic.php?id_grp=$id_grp&formateurs=&nom_grp=$nom_grp&communes_groupe=1&rallonge=ressources/groupes/$id_grp&sousdos=ressources/groupes/$id_grp&parent=ressources/groupes&dossier=ressources/groupes/$id_grp&fichier=$id_grp&direct=dossier";
             $lien_rp = urlencode($lien_rp);
             echo "<TD align='center' valign='center' width='20%' nowrap>";
             echo "$bouton_gauche<A HREF=\"trace.php?link=$lien_rp\">$mess_menu_casier_groupe $mess_menu_gestion_grp</A>$bouton_droite</TD>";
             // accès forum du groupe
             $id_forum = GetDataField($connect, "select id from forums where name ='$nom_grp'", "id");
             $comment_forum = GetDataField($connect, "select name from forums where id='$id_forum'", "name");
             $course = base64url_encode('formateur|0|0|-|'.$id_grp);
             $lien_forum = 'forum/list.php?f='.$id_forum.'&collapse=1'.TinCanTeach ('formateur|0|0|-|'.$id_grp,'forum/list.php?f='.$id_forum.'&collapse=1',$adresse_http.'/Forums - ');
             if ($_SESSION['onLine'] == 1)
             {
                           $leCours =($utilisateur > 0) ? '|0|0|-|'.$id_grp : '|0|0|0|'.$id_grp;
                           $course =  base64url_encode('formateur'.$leCours);
                           $lien_forum = 'forum/list.php?f='.$id_forum.'&collapse=1'.TinCanTeach ('apprenant'.$leCours,'forum/list.php?f='.$id_forum.'&collapse=1',$adresse_http.'/Forums - ');
                           echo '<TD align="center" valign="middle" width="30%"><table cellpadding=0 cellspacing=0><tr><td>'.$bouton_gauche.'<div id="forum" style="float:left;padding-right:4px;">'.
                                '<A HREF=\''.$lien_forum.'\'><div id="forum_'.$id_forum.'" style="float:left;">'.$mess_menu_forum.' '.
                                $mess_menu_gestion_grp.'</div></A></div>'.$bouton_droite.'</td><td>';
             }
             else
             {
                           $lien = "forum/list.php?f=$id_forum&collapse=0";
                           $lien=urlencode($lien);
                           echo  "<TD align='left'><A HREF='trace.php?link=$lien'>$mess_menu_forum $mess_menu_gestion_grp</A></TD>";
             }
             if ($_SESSION['onLine'] == 1)
             {
                     $lien = "admin/statements.php?course=$course&utilisateur=$utilisateur&registration=$Register&Activity=forum&collapse=1&keepThis=true&TB_iframe=true&height=450&width=750";
                      echo "<div id='statement_F_$id_forum' style='float:left;display:none;'>
                             <script language='javascript'>
                             $.ajax({
                                type: 'GET',
                                url: 'http://lms.annulab.com/TinCanApi/statements.php',
                                data: 'verb=1&utilisateur=$utilisateur&course=$course&registration=$Register',
                                success: function(msg)
                                {
                                    if (msg == 'En attente')
                                        $('#tincan_F_$id_forum').css('display','none');
                                    else
                                    {
                                        $('#xApi_F_$id_forum').html('<img src=\"images/icones/ampoule18.gif\" ".
                                        " border=0 title=\"Consultations du forum\" style=\"float:left;padding-bottom:3px;\">');
                                        $('#tincan_F_$id_forum').css('display','block');
                                    }
                                }
                            });
                           </script></div>";
                        echo "<div id='tincan_F_$id_forum' style='float:left;display:none;'><A HREF = \"$lien\" ".
                          "name='Forum ".$nom_grpe." : historique' class='thickbox'>".
                          "<div id='xApi_F_$id_forum' style='float:left;background-image:".
                          "url(\"images/ecran-annonce/ongl01.gif\");margin-left:2px;'></div></A></div></td></tr></table></TD>";
             }
             echo "</TR>";
             if ($affiche_grp[$id_grp] == 1 || $nomb_grp_tut == 1 || $affiche_groupe == 3)
             {
               $req_app = mysql_query("SELECT distinct utilisateur_groupe.utilgr_utilisateur_no from
                                       utilisateur_groupe,utilisateur where
                                       utilisateur_groupe.utilgr_groupe_no=$id_grp AND
                                       utilisateur_groupe.utilgr_utilisateur_no = utilisateur.util_cdn
                                       order by utilisateur.util_nom_lb");
               $nomb_app = mysql_num_rows($req_app);
               echo "<TR>";
               $wdtTD = ($wk == 1) ? 7 : 6;
               echo "<TD colspan='$wdtTD' width='100%'><TABLE cellspacing=1 cellpadding=3 border='0' width='100%'>";
               echo "<TR bgcolor=\"#2B677A\"><TD width='30' bgcolor=\"#FFFFFF\">&nbsp;</TD>";
               echo "<TD width='180' valign='top'><FONT COLOR= 'white' SIZE='1'><B>$msq_apprenant</B></FONT></TD>";
               echo "<TD width='50' valign='top' align='center'><FONT COLOR= 'white' SIZE='1'><B>$mpr_suivi</B></FONT></TD>";
               echo "<TD width='95' valign=top><FONT COLOR= 'white' SIZE='1'><B>$mess_gp_seq_parc ".
                    strtolower($mess_gp_hsd)."</B></FONT></TD>";
               echo "<TD width='95' valign=top><FONT COLOR= 'white' SIZE='1'><B>$mess_seq_urgent</B></FONT></TD>";
//               echo "<TD width='50' valign='top' align='center'><FONT COLOR= 'white' SIZE='1'><B>$mess_tutorat</B></FONT></TD>";
               echo "<TD width='50' valign='top'><FONT COLOR= 'white' SIZE='1'><B>$mess_presc_fs</B></FONT></TD>";
               echo "<TD width='50' valign='top'><FONT COLOR= 'white' SIZE='1'><B>$msq_acts</B></FONT></TD>";
               echo "<TD width='50' valign='top'><FONT COLOR= 'white' SIZE='1'><B>$mess_obs_fs</B></FONT></TD>";
               echo "<TD width='50' valign=top><FONT COLOR= 'white' SIZE='1'><B>$mess_ajt_frm</B></FONT></TD>";
               echo "<TD width='50' valign='top'><FONT COLOR= 'white' SIZE='1'><B>$mess_bilan</B></FONT></TD>";
               echo "</TR>";
               if ($nomb_app > 0)
               {
                  $n_app = 0;
                  while ($n_app < $nomb_app)
                  {
                       $id_app = mysql_result($req_app,$n_app,"utilgr_utilisateur_no");
                       $req_identite = mysql_query("SELECT * from utilisateur where util_cdn = $id_app");
                       $nb_identite = mysql_num_rows($req_identite);
                       $id_login = mysql_result($req_identite,0,"util_login_lb");
                       $id_nom = mysql_result($req_identite,0,"util_nom_lb");
                       $id_prenom = mysql_result($req_identite,0,"util_prenom_lb");
                       $id_type = mysql_result($req_identite,0,"util_typutil_lb");
                       $id_email = mysql_result($req_identite,0,"util_email_lb");
                       $id_photo = mysql_result($req_identite,0,"util_photo_lb");
                       $majuscule1 = $id_prenom." ".$id_nom;
                       $aa = $n_app+200*$i;
                       $mm = $aa+50000;
                       echo couleur_tr($n_app+1,30);
                       echo "<TD width='30' bgcolor='#FFFFFF'>&nbsp;</TD><TD valign='top' width='171' nowrap>";
                       if ($_SESSION['onLine'] == 1)
                       {
                         $lien = "admin/ChartsStatements.php?groupe=$id_grp&who=".$id_app."_".$id_login."|".str_replace('http://','',str_replace('.educagri.fr','',$nom_url));
                         $traqXapi = '';
                         $traqXapi = "<script language='javascript'>
                            $.ajax({
                                    type: 'GET',
                                    url: 'http://lms.annulab.com/TinCanApi/statementsCharts.php',
                                    data: 'comptage=1&groupe=$id_grp&who=".$id_app."_".$id_login."|".str_replace('http://','',str_replace('.educagri.fr','',$nom_url))."',
                                    success: function(msg)
                                    {
                                       if (msg == 'Rien')
                                           $('#BilanAcces_$id_app').css('display','none');
                                       else
                                       {
                                           $('#BilanAcces_$id_app').html('<img src=\"images/icones/ampoule18.gif\" ".
                                           " border=0 title=\"Bilan sur le LRS\" style=\"padding-bottom:3px;\">');
                                           $('#LrsAcces_$id_app').css('display','block');
                                      }
                                    //alert(msg);
                                    }
                            });
                            </script>";
                         $traqXapi .= "<div id='LrsAcces_$id_app' style='display:none;float:left;'><A HREF = \"$lien\" title='Historique des accès' target='blank'>".
                                    "<div id='BilanAcces_$id_app' style='clear:both:float:left;width:20px;'></div></A></div>";
                       }
                       $lien="prescription.php?affiche_fiche_app=1&hgrp=$hgrp&id_util=$id_app&identite=1&stopper=1";
                       $lien = urlencode($lien);
                       $nbr_trac = mysql_result(mysql_query("select count(*) from traceur where
                                                             traceur_util_no = $id_app AND
                                                             traceur_grp_no=$id_grp"),0);
                       $nbr_log = mysql_result(mysql_query("select count(*) from log where login = '$id_login'"),0);
                       if ($nbr_trac == 0)
                          $encore = "<IMG SRC='images/ecran-annonce/icoabsent.gif' width='5' height='10' border='0' ".
                                    "onMouseOver=\"overlib('$mess_no_actif',ol_hpos,CENTER,ABOVE,WIDTH,'100',DELAY,'100',CAPTION, '')\" ".
                                    "onMouseOut=\"nd()\">";
                       else
                          $encore = "";
                       if ($nbr_log == 0)
                       {
                          $le_div = "<DIV id='seqgris' style='float:left;'>";
                          $nolog = ". << $no_connect >>";
                       }
                       else
                       {
                          $le_div = "<DIV id='sequence' style='float:left;'>";
                          $nolog = "";
                       }
                       if ($id_photo == '')
                       {
                          echo "$le_div<A HREF=\"trace.php?link=$lien&keepThis=true&TB_iframe=true&height=355&width=650\" class='thickbox' ".
                               "title=\"$mess_suite_fp$nolog\"><B>$id_nom $id_prenom</B></A>  $encore $traqXapi .</DIV>";
                       }
                       else
                       {
                          list($w_img, $h_img, $type_img, $attr_img) = getimagesize("images/$id_photo");
                          echo "$le_div<A HREF=\"trace.php?link=$lien&keepThis=true&TB_iframe=true&height=355&width=650\" class='thickbox' ".
                               "onMouseOver=\"overlib('', TEXTCOLOR, '#ffffff', TEXTSIZE, 2, WIDTH, $w_img, HEIGHT, $h_img, BACKGROUND,".
                               " 'images/$id_photo', PADX, 60, 20, PADY, 20, 20)\" onMouseOut=\"nd()\" title = \"$nolog\">".
                               "<B>$id_nom $id_prenom</B></A>  $encore $traqXapi</DIV>";
                       }
                       echo msgInst($id_app,"apprenant car il est connecté");
                      echo "</TD>";
                      $nb_fiche_presc = 0;
                      $nb_fiche_act = 0;
                      $nb_fiche_apprec = 0;
                      $nb_fiche_obs = 0;
                      $req_presc_grp = mysql_query("SELECT COUNT(*) from prescription_$id_grp WHERE presc_utilisateur_no = $id_app");
                      $nb_presc_grp = mysql_result($req_presc_grp,0);
                      if ($nb_presc_grp > 0)
                      {
                         if ($user_type == 'TUTEUR')
                            $lien1 = "menu_tuteur.php";
                         else
                            $lien1 = "menu_formateur.php?action=tutorer";
                         $lien1 = urlencode($lien1);
                         $lien = "gest_frm_rappel1.php?utilisateur=$id_app&a_faire=1&le_groupe=$id_grp&id_grp=$id_grp&numero_groupe=$id_grp&graph=1&tout=1&vient_de_grp=1";
                         $lien=urlencode($lien);//entantque=tut&
                         echo "<TD align='center' valign='top' width='50'><A HREF=\"trace.php?link=$lien\" target='main'".
                              " onclick=\"javascript:parent.logo.location.replace('trace.php?link=$lien1')\"".
                              " onmouseover=\"img$aa.src='images/ecran-annonce/icoGgob.gif';return true;\" ".
                              "onmouseout=\"img$aa.src='images/ecran-annonce/icoGgo.gif'\">".
                              "<IMG NAME=\"img$aa\" SRC=\"images/ecran-annonce/icoGgo.gif\" BORDER='0' ".
                              "onLoad=\"tempImg=new Image(0,0); tempImg.src='images/ecran-annonce/icoGgob.gif'\"></A></TD>";
                      }
                      else
                         echo  "<TD width='50'></TD>";
                      $nb_seq_ret = cherche_seq($id_app,$id_grp);
                      $ret_seq = explode("*",$nb_seq_ret);
                      $ma_liste = explode("|",$ret_seq[0]);
                      $seq_depasse = $ma_liste[0];
                      if ($seq_depasse > 0)
                         echo  "<TD align='center' valign='top' width='100'><B>$seq_depasse</B></TD>";
                      else
                         echo  "<TD width='100' valign='top'>&nbsp;</TD>";
                      $seq_urgent = $ma_liste[1];
                      if ($seq_urgent > 0)
                         echo  "<TD align='center' valign='top' width='100'><B>$seq_urgent</B></TD>";
                      else
                         echo  "<TD width='100'>&nbsp;</TD>";
                      $req_fiche_presc = mysql_query("SELECT count(*) FROM fiche_suivi WHERE
                                                      fiche_utilisateur_no = $id_app AND
                                                      fiche_grp_no = $id_grp AND fiche_auteur_no != $id_user AND
                                                      (fiche_typaction_lb = 'Gestion des prescriptions' OR
                                                      fiche_typaction_lb = 'Prescription') AND
                                                      fiche_date_dt >= '$date_der'");
                      $nb_fiche_presc = mysql_result($req_fiche_presc,0);
                      if ($nb_fiche_presc > 0){
                          $lien="fiche_suivi.php?utilisateur=$id_app&consulter=1&objet=prescription&event=1&ordre=asc&tuteur_vue=1&date_der=$date_der&id_grp=$id_grp";
                          $lien=urlencode($lien);
                          echo "<TD align='center' valign='top' width='30'><DIV id='sequence'>".
                               "<A HREF=\"trace.php?link=$lien&keepThis=true&TB_iframe=true&height=455&width=700\"".
                               " class='thickbox'>$nb_fiche_presc</A></DIV></TD>";
                      }else
                          echo "<TD align='center' valign='top' width='30'>&nbsp;</TD>";
                      $req_fiche_act = mysql_query("SELECT count(*) FROM fiche_suivi WHERE
                                                    fiche_utilisateur_no = $id_app AND
                                                    fiche_auteur_no != $id_user AND
                                                    fiche_grp_no = $id_grp AND
                                                    fiche_typaction_lb = 'suivi pedagogique' AND
                                                    fiche_date_dt >= '$date_der'");
                      $nb_fiche_act = mysql_result($req_fiche_act,0);
                      if ($nb_fiche_act > 0)
                      {
                         $lien="fiche_suivi.php?utilisateur=$id_app&consulter=1&objet=suivi&event=1&ordre=asc&tuteur_vue=1&date_der=$date_der&id_grp=$id_grp&id_grp=$id_grp";
                         $lien=urlencode($lien);
                         echo "<TD align='center' valign='top' width='30'><DIV id='sequence'>".
                              "<A HREF=\"trace.php?link=$lien&keepThis=true&TB_iframe=true&height=455&width=700\"".
                              " class='thickbox'>$nb_fiche_act</A></DIV></TD>";
                      }
                      else
                         echo "<TD align='center' valign='top' width='30'>&nbsp;</TD>";

                      $req_fiche_obs = mysql_query("SELECT count(*) FROM fiche_suivi WHERE
                                                    fiche_utilisateur_no = $id_app AND
                                                    fiche_auteur_no != $id_user AND
                                                    fiche_grp_no = $id_grp AND
                                                    (fiche_autraction_lb = 'observation' OR fiche_autraction_lb = 'appreciation') AND
                                                    fiche_date_dt >= '$date_der'");
                      $nb_fiche_obs = mysql_result($req_fiche_obs,0);
                      if ($nb_fiche_obs > 0){
                         $lien="fiche_suivi.php?utilisateur=$id_app&consulter=1&objet=observation&event=2&ordre=asc&tuteur_vue=1&date_der=$date_der&id_grp=$id_grp";
                         $lien=urlencode($lien);
                         echo "<TD align='center' valign='top' width='30'><DIV id='sequence'>".
                              "<A HREF=\"trace.php?link=$lien&keepThis=true&TB_iframe=true&height=555&width=850\"".
                              " class='thickbox'>$nb_fiche_obs</A></DIV></TD>";
                      }
                      else
                         echo "<TD align='center' valign='top' width='30'>&nbsp;</TD>";
                      $nom_grp = GetDataField ($connect,"select grp_nom_lb from groupe where grp_cdn  = $id_grp","grp_nom_lb");
                      $id_forum = GetDataField ($connect,"select id from forums where name='$nom_grp'","id");
                      $nom_forum = GetDataField ($connect,"select table_name from forums where name='$nom_grp'","table_name");
                      $req_forum =mysql_query("SELECT * from $nom_forum where author = \"$majuscule1\" AND ".
                                              "email = \"$id_email\" and approved='Y'");
                      $nbr_posts = mysql_num_rows($req_forum);
                      //$nbr_mess_forum = cherche_forum($id_grp,$date_compare,$majuscule1);
                      if ($nbr_posts > 0)
                      {
                          $lien = "popup_forums.php?NbrPost=$nbr_posts&id_forum=$id_forum&utilisateur=$id_app&id_grp=$id_grp";
                          $lien=urlencode($lien);
                          echo  "<TD align='center' valign='top' width='50'>".
                                "<A HREF=\"trace.php?link=$lien&keepThis=true&TB_iframe=true&height=355&width=650\" ".
                                "class='thickbox'>$nbr_posts</A></TD>";
                      }
                      else
                          echo  "<TD width='70'>&nbsp;</TD>";
                      $req_traq = mysql_query("select traq_cdn from traque where traq_util_no=$id_app AND traq_grp_no= $id_grp");
                      $nbr_trq = mysql_num_rows($req_traq);
                      if ($nbr_trq > 0)
                      {
                         $lien_bilan = "bilan.php?bilan=1&utilisateur=$id_app&numero_groupe=$id_grp";
                         //$lien_bilan = urlencode($lien_bilan);
                         echo "<TD align='center' valign='top'><DIV id='sequence'>".
                              "<A HREF=\"$lien_bilan&keepThis=true&TB_iframe=true&height=355&width=650\" class='thickbox'".
                              " onmouseover=\"img_regroup$n_app.src='images/bilan/icogroupb.gif';return true;\"".
                              " onmouseout=\"img_regroup$n_app.src='images/bilan/icogroup.gif'\">".
                              "<IMG NAME=\"img_regroup$n_app\" SRC=\"images/bilan/icogroup.gif\" BORDER='0' width='28' height='25'".
                              " onLoad=\"tempImg=new Image(0,0); tempImg.src='images/bilan/icogroupb.gif'\"></A></DIV></TD>";
                      }
                      else
                         echo "<TD align='center' valign='top' width='30'>&nbsp;</TD>";
                      echo "</TR>";
                     $n_app++;
                  }
                  echo "</TABLE></TD></TR>";
                  }
             }
             if ($affiche_grp[$id_grp] == 1 || $affiche_groupe == 3)
             {
                echo "<tr height='6'><td></td></tr></table></div></td></tr>";
                echo "<TR><TD colspan='6' width='100%' height='20'></TD></TR>";
             }
             else
                echo "</table></td></tr>";
          $i++;
         }
         echo "</TABLE></TD></TR></TABLE>";
      }
   exit();// fin de gestion du tutorat de groupe (fin supervision == 1)
  }
  elseif($tutorat_app == 1)
  {
      $reqtut = mysql_query ("select * from tuteur,utilisateur where
                              tuteur.tut_tuteur_no = $id_user AND
                              tuteur.tut_apprenant_no = utilisateur.util_cdn
                              ORDER BY utilisateur.util_nom_lb");
      $nomb_app = mysql_num_rows($reqtut);
      if ($nomb_app > 0)
      {
      echo "<CENTER><TABLE bgColor='#298CA0' cellspacing='1' cellpadding='1' width='98%' border=0><TR><TD width='100%'>";
      echo "<TABLE bgColor='#FFFFFF' cellspacing='1' cellpadding='0' width='100%' border=0>";
      echo "<TR><TD background=\"images/fond_titre_table.jpg\" height='37' align='center' colspan='5' valign='center'>".
           "<Font size='3' color='#FFFFFF'><B>$mess_app_tutorat</B></FONT></TD></TR>";
      $bgrd = "bgcolor='#FFFFFF'";
       if ($affiche_toutapp == 1)
       {
          $chx = 0;
          $alte = $mess_close_list;
          $sens_aff = $mess_replier;
       }
       else
       {
          $chx = 1;
          $alte = $list_app_grp;
          $sens_aff = $mess_deplier;
       }
       $lien = "annonce_tuteur.php?tutorat_app=1&affiche_toutapp=$chx";
       $lien = urlencode($lien);
       if ($nomb_app > 0)
       {
         echo "<tr><td colspan='2'><table width='100%'><tr><td align='left' valign='center' style=\"padding: 6px;\" width='10%'>";
         echo "<A href=\"trace.php?link=$lien\" class='bouton_new'>$sens_aff</A></td>".
         aide("suivi_pedagogique_tut",5);
         $lien= "annuaire.php?vientde=annonce";//&entantque=tut
         $lien = urlencode($lien);
         echo "<TD width='80%' valign='center' align='center'>".
              "<A HREF=\"javascript:void(0);\" class='bouton_new'".
              " onclick=\"open('trace.php?link=$lien','window','scrollbars=no,resizable=yes,width=680,height=520')\">".
              "$mess_mail_avert $mess_mail_cert_app</A>";
         echo "</TD></tr></table></td></tr>";
       }
       echo "<TR>";
       echo "<TD><TABLE border='0' width='100%' cellspacing='1'>";
       $n_app = 0;
       while($n_app < $nomb_app)
       {
         $nb_fiche_tut = 0;
         $id_app = mysql_result($reqtut,$n_app,"tut_apprenant_no");
         $id_login = mysql_result($reqtut,$n_app,"util_login_lb");
         $id_nom = mysql_result($reqtut,$n_app,"util_nom_lb");
         $id_prenom = mysql_result($reqtut,$n_app,"util_prenom_lb");
         $id_type = mysql_result($reqtut,$n_app,"util_typutil_lb");
         $id_email = mysql_result($reqtut,$n_app,"util_email_lb");
         $id_photo = mysql_result($reqtut,$n_app,"util_photo_lb");
         $majuscule1 = $id_prenom." ".$id_nom;
         $flec1 = "<img src='images/gest_parc/flech02.gif' border=0>";
         $flec0 = "<img src='images/gest_parc/flechv01.gif' border=0>";
         if ($affiche_app[$id_app] == 1 || $affiche_toutapp == 1)
         {
           $fleche = $flec1;
           $alte = $mess_close;
           $lien = "annonce_tuteur.php?tutorat_app=1&affiche_toutapp=0&affiche_app[$id_app]=0";
         }
         else
         {
           $fleche = $flec0;
           $alte = $mess_open;
           $lien = "annonce_tuteur.php?tutorat_app=1&affiche_toutapp=0&affiche_app[$id_app]=1";
         }
         $lien = urlencode($lien);
         echo couleur_tr($n_app+1,30);
         echo "<TD width=100%>";
         if ($affiche_app[$id_app] == 1 || $affiche_toutapp == 1 || $nomb_app == 1)
             echo "<div id='tut_separe' class='individ'>";
         echo "<TABLE cellspacing='2' cellspadding='0' width=100%>".
              "<TR><TD valign='top' align='center' width='15' nowrap>".
              "<A HREF=\"trace.php?link=$lien\" title=\"$alte\">$fleche</A></TD>";
         echo "<TD valign='top' width='165' nowrap><TABLE cellspacing='2' cellspadding='0' width=100%><TR>";
         $nbr_log = mysql_result(mysql_query("select count(*) from log where login = '$id_login'"),0);
         if ($nbr_log == 0)
         {
            $le_div = "<DIV id='seqgris' style='float:left;'>";
            $nolog = ". << $no_connect >>";
         }
         else
         {
            $le_div = "<DIV id='sequence' style='float:left;'>";
            $nolog = "";
         }
         echo "<TD valign=top width='80%'>$le_div<A HREF=\"trace.php?link=$lien\" ".
              "title=\"$alte$nolog\"><B>$id_nom $id_prenom</B></A></div>";
         echo msgInst($id_app,"apprenant car il est connecté");
         echo "</TD><TD valign=top width='20%'>";
         $lien="prescription.php?affiche_fiche_app=1&hgrp=$hgrp&id_util=$id_app&identite=1&stopper=1";
         $lien = urlencode($lien);
         if ($id_photo == '')
         {
            echo "<DIV id='sequence'><A HREF=\"trace.php?link=$lien&keepThis=true&TB_iframe=true&height=555&width=850\" class='thickbox' ".
                 "title=\"$mess_suite_fp\"><IMG SRC='images/repertoire/icoptisilhouet.gif' border=0></A></DIV>";
         }
         else
         {
            list($w_img, $h_img, $type_img, $attr_img) = getimagesize("images/$id_photo");
            echo "<DIV id='sequence'><A HREF=\"trace.php?link=$lien&keepThis=true&TB_iframe=true&height=555&width=850\" class='thickbox' ".
                 "onMouseOver=\"overlib('', TEXTCOLOR, '#ffffff', TEXTSIZE, 2, WIDTH, $w_img, HEIGHT, $h_img, BACKGROUND,".
                 " 'images/$id_photo', PADX, 60, 20, PADY, 20, 20)\" onMouseOut=\"nd()\">".
                 "<IMG SRC='images/repertoire/icoptisilhouet.gif' border=0></A></DIV>";
         }
         echo "</TD></TR></TABLE></TD>";
         $lien= "mail.php?contacter=1&a_qui=$id_email&num=$id_app";
         $lien = urlencode($lien);
         echo "<TD valign='top'><A HREF=\"javascript:void(0);\" ".
              "onclick=\"window.open('trace.php?link=$lien','','width=680,height=520,resizable=yes,status=no')\"".
              " onmouseover=\"img_lettre$i.src='images/ecran-annonce/icolettrebw.gif';return true;\"".
              " onmouseout=\"img_lettre$i.src='images/ecran-annonce/icolettrew.gif'\">".
              "<IMG NAME=\"img_lettre$i\" SRC=\"images/ecran-annonce/icolettrew.gif\"".
              " BORDER='0' onLoad=\"tempImg=new Image(0,0); tempImg.src='images/ecran-annonce/icolettrebw.gif'\"></A></TD>";
         echo "<TD valign=top colspan='9' width='80%'>";
         if ($affiche_app[$id_app] == 1 || $affiche_toutapp == 1 || $nomb_app == 1)
         {
            echo "<TABLE cellspacing='1' width='100%' border='0'>";
            $requete_grp = mysql_query ("select * from groupe_parcours,groupe WHERE
                             gp_grp_no = grp_cdn group by grp_cdn
                             order by grp_nom_lb");
            $nomb_grp = mysql_num_rows($requete_grp);
            if ($nomb_grp > 0)
            {
              $num_grp = 0;$nb_presc=0;
              while($num_grp < $nomb_grp)
              {
                   $idgrp = mysql_result($requete_grp,$num_grp,"grp_cdn");
                   $nbr_presc = mysql_num_rows(mysql_query("select presc_cdn from prescription_$idgrp where presc_utilisateur_no = $id_app"));
                   $nb_presc++;
                $num_grp++;
              }
            }
            if (isset($nb_presc) && $nb_presc > 0)
            {
                echo "<TR bgcolor=\"#2B677A\">";
                echo "<TD width='250' valign='top'><FONT COLOR= 'white' SIZE='1'><B>$mess_grp_form_suiv</B></FONT></TD>";
                echo "<TD  width='35' valign='top'><FONT COLOR= 'white' SIZE='1'><B>$mess_graphe</B></FONT></TD>";
                echo "<TD width='80' valign=top><FONT COLOR= 'white' SIZE='1'><B>$mess_gp_seq_parc ".
                      strtolower($mess_gp_hsd)."</B></FONT></TD>";
                echo "<TD width='80' valign=top><FONT COLOR= 'white' SIZE='1'><B>$mess_seq_urgent</B></FONT></TD>";
                echo "<TD width='50' valign='top'><FONT COLOR= 'white' SIZE='1'><B>$mess_presc_fs</B></FONT></TD>";
                echo "<TD width='50' valign='top'><FONT COLOR= 'white' SIZE='1'><B>$msq_acts</B></FONT></TD>";
                echo "<TD width='50' valign='top'><FONT COLOR= 'white' SIZE='1'><B>$mess_obs_fs</B></FONT></TD>";
                echo "<TD width='50' valign=top><FONT COLOR= 'white' SIZE='1'><B>$mess_ajt_frm</B></FONT></TD>";
                echo "<TD width='35' valign='top'><FONT COLOR= 'white' SIZE='1'><B>$mess_bilan</B></FONT></TD>";
                echo "<TR>";
            }
            if ($nb_presc > 0)
            {
               $req_grp = mysql_query("select utilisateur_groupe.utilgr_groupe_no from
                                       utilisateur_groupe,groupe WHERE
                                       utilisateur_groupe.utilgr_utilisateur_no = $id_app AND
                                       utilisateur_groupe.utilgr_groupe_no = groupe.grp_cdn
                                       ORDER by groupe.grp_nom_lb");
               $nomb_grp = mysql_num_rows($req_grp);
               $i=0;
               while ($i < $nomb_grp)
               {
                  $nb_fiche_presc = 0;
                  $nb_fiche_act = 0;
                  $nb_fiche_apprec = 0;
                  $nb_fiche_obs = 0;
                  echo couleur_tr($i+1,30);
                  $id_grp = mysql_result($req_grp,$i,"utilgr_groupe_no");
                  $nom_grp =GetDataField ($connect,"select grp_nom_lb from groupe WHERE grp_cdn = $id_grp","grp_nom_lb");
                  $id_classe = GetDataField ($connect,"SELECT grp_classe_on from groupe where grp_cdn  = $id_grp","grp_classe_on");
                  if ($id_classe == 0)
                     $aff_pop = $mess_gp_der_form."<BR>".addslashes($mess_indiv_alrt).".";
                  else
                     $aff_pop = $mess_gp_der_form;
                  $carac_grp = strlen($nom_grp);
                  if ($carac_grp > 33)
                     $nom_grp1 = substr($nom_grp,0,31)."..";
                  else
                     $nom_grp1 = $nom_grp;
                  $nbr_trac = mysql_result(mysql_query("select count(*) from traceur where
                                                        traceur_util_no = $id_app AND
                                                        traceur_grp_no=$id_grp"),0);
                  if ($nbr_trac == 0)
                     $encore = "<IMG SRC='images/ecran-annonce/icoabsent.gif' width='5' height='10' border='0' ".
                               "onMouseOver=\"overlib('$mess_no_actif',ol_hpos,CENTER,ABOVE,WIDTH,'100',DELAY,'100',CAPTION, '')\" ".
                               "onMouseOut=\"nd()\">";
                  else
                     $encore = "";
                  $desc_grp =GetDataField ($connect,"select grp_formobject_lb from groupe WHERE grp_cdn = $id_grp","grp_formobject_lb");
                  $desc_grp =str_replace(chr(146),chr(39),$desc_grp);
                  $desc1_grp =addcslashes($desc_grp,"\0..\47!@\176..\255");
                  $req_presc_grp = mysql_query("SELECT COUNT(*) from prescription_$id_grp WHERE presc_utilisateur_no = $id_app");
                  $nb_presc_grp = mysql_result($req_presc_grp,0);
                  $traqXapi = '';
                  if ($nb_presc_grp > 0)
                  {
                       if ($_SESSION['onLine'] == 1)
                       {
                          $lienStatements = "admin/ChartsStatements.php?groupe=$id_grp&who=".$id_app."_".$id_login."|".str_replace('http://','',str_replace('.educagri.fr','',$nom_url));
                          if ($_SESSION['onLine'] == 1)
                          $traqXapi = "<script language='javascript'>
                            $.ajax({
                                    type: 'GET',
                                    url: 'http://lms.annulab.com/TinCanApi/statementsCharts.php',
                                    data: 'comptage=1&groupe=$id_grp&who=".$id_app."_".$id_login."|".str_replace('http://','',str_replace('.educagri.fr','',$nom_url))."',
                                    success: function(msg)
                                    {
                                       if (msg == 'Rien')
                                           $('#BilanAcces_$id_app').css('display','none');
                                       else
                                       {
                                           $('#BilanAcces_$id_app').html('<img src=\"images/icones/ampoule18.gif\" ".
                                           " border=0 title=\"Bilan sur le LRS\" style=\"padding-bottom:3px;\">');
                                           $('#LrsAcces_$id_app').css('display','block');
                                      }
                                    //alert(msg);
                                    }
                            });
                            </script>";
                          $traqXapi .= "<div id='LrsAcces_$id_app' style='display:none;float:left;'><A HREF = \"$lienStatements\" title='Historique des accès' target='blank'>".
                                       "<div id='BilanAcces_$id_app' style='clear:both:float:left;width:20px;'></div></A></div>";
                     }
                     $lien = "gest_frm_rappel1.php?utilisateur=$id_app&a_faire=1&le_groupe=$id_grp&id_grp=$id_grp&numero_groupe=$id_grp&graph=1&tout=1&vient_de_grp=1";
                     $lien=urlencode($lien);//entantque=tut&
                     echo  "<TD width='165'><A HREF=\"trace.php?link=$lien\"";
                     echo " onMouseOver=\"overlib('$aff_pop',ol_hpos,RIGHT,ABOVE,WIDTH,'150',DELAY,'800',CAPTION, '<B>".
                          addslashes($nom_grp)."</B>')\" onMouseOut=\"nd()\">$nom_grp1</A>  $encore $traqXapi</TD>";
               }
               else
                     echo  "<TD width='165'><acronyme id='$nom_grp'>$nom_grp1 $encore</acronyme></TD>";
//                  if ($typ_user != "TUTEUR")
//                     $entantquetut = 1;
                  if ($nb_presc_grp > 0)
                  {
                     //$lien = "lancement.php?numero_groupe=$id_grp&hgrp=$hgrp&utilisateur=$id_app&plan=plan_parc";
                     $lien = "lancement.php?numero_groupe=$id_grp&id_grp=$id_grp&groupe=1&hgrp=$hgrp&entantquetut=1&entantqueobs=";
                     $lien = urlencode($lien);
                     echo "<TD align='center' valign='center' width='25'><A HREF=\"trace.php?link=$lien\"".
                          " onMouseOver=\"overlib('".addslashes($mess_pf_pef)." ". addslashes($majuscule1)." : ".
                           addslashes($nom_grp)."',ol_hpos,RIGHT,ABOVE,WIDTH,'150',DELAY,'800',CAPTION, '')\"".
                          " onMouseOut=\"nd()\"><IMG SRC='images/modules/tut_form/icograph1.gif' width='25' border='0'></A></TD>";
                  }
                  else
                     echo "<TD align='center' valign='top' width='25'>&nbsp;</TD>";
                  $nb_seq_ret = cherche_seq($id_app,$id_grp);
                  $ret_seq = explode("*",$nb_seq_ret);
                  $ma_liste = explode("|",$ret_seq[0]);
                  $seq_depasse = $ma_liste[0];
                  if ($seq_depasse > 0)
                  {
                     $lien = "gest_frm_rappel1.php?utilisateur=$id_app&a_faire=1&le_groupe=$id_grp&id_grp=$id_grp&numero_groupe=$id_grp&graph=1&tout=1&vient_de_grp=1";
                     $lien=urlencode($lien);//entantque=tut&
                     echo  "<TD align='center' width='60'><A HREF=\"trace.php?link=$lien\" target='main'><B>$seq_depasse</B></TD>";
                  }
                  else
                     echo  "<TD width='60'>&nbsp;</TD>";
                  $seq_urgent = $ma_liste[1];
                  if ($seq_urgent > 0)
                  {
                     $lien = "gest_frm_rappel1.php?utilisateur=$id_app&a_faire=1&le_groupe=$id_grp&id_grp=$id_grp&numero_groupe=$id_grp&graph=1&tout=1&vient_de_grp=1";
                     $lien=urlencode($lien);//entantque=tut&
                     echo  "<TD align='center' width='55'><A HREF=\"trace.php?link=$lien\" target='main'><B>$seq_urgent</B></TD>";
                  }
                  else
                     echo  "<TD width='55'>&nbsp;</TD>";
                  $req_fiche_presc = mysql_query("SELECT count(*) FROM fiche_suivi WHERE
                                                  fiche_utilisateur_no = $id_app AND
                                                  fiche_grp_no = $id_grp AND
                                                  fiche_auteur_no != $id_user AND
                                                  (fiche_typaction_lb = 'Gestion des prescriptions' OR
                                                  fiche_typaction_lb = 'Prescription') AND
                                                  fiche_date_dt >= '$date_der'");
                  $nb_fiche_presc = mysql_result($req_fiche_presc,0);
                  if ($nb_fiche_presc > 0)
                  {
                     $lien="fiche_suivi.php?utilisateur=$id_app&consulter=1&objet=prescription&event=1&ordre=asc&tuteur_vue=1&date_der=$date_der&id_grp=$id_grp&vientde=annonce";
                     $lien=urlencode($lien);
                     echo "<TD align='center' width='50'><DIV id='sequence'>".
                          "<A HREF=\"trace.php?link=$lien&keepThis=true&TB_iframe=true&height=455&width=750\"".
                          " class='thickbox'>$nb_fiche_presc</A></DIV></TD>";
                  }
                  else
                     echo "<TD align='center' width='50'>&nbsp;</TD>";
                  $req_fiche_act = mysql_query("SELECT count(*) FROM fiche_suivi WHERE
                                                fiche_utilisateur_no = $id_app AND
                                                fiche_auteur_no != $id_user AND
                                                fiche_grp_no = $id_grp AND
                                                fiche_typaction_lb = 'suivi pedagogique' AND
                                                fiche_date_dt >= '$date_der'");
                  $nb_fiche_act = mysql_result($req_fiche_act,0);
                  if ($nb_fiche_act > 0)
                  {
                     $lien="fiche_suivi.php?utilisateur=$id_app&consulter=1&objet=suivi&event=1&ordre=asc&tuteur_vue=1&date_der=$date_der&id_grp=$id_grp&id_grp=$id_grp&vientde=annonce";
                     $lien=urlencode($lien);
                     echo "<TD align='center' width='40'><DIV id='sequence'>".
                          "<A HREF=\"trace.php?link=$lien&keepThis=true&TB_iframe=true&height=455&width=750\"".
                          " class='thickbox'>$nb_fiche_act</A></DIV></TD>";
                  }
                  else
                     echo "<TD align='center' width='40'>&nbsp;</TD>";

                  $req_fiche_obs = mysql_query("SELECT count(*) FROM fiche_suivi WHERE
                                                fiche_utilisateur_no = $id_app AND
                                                fiche_auteur_no != $id_user AND
                                                fiche_grp_no = $id_grp AND
                                                (fiche_autraction_lb = 'observation' OR
                                                fiche_autraction_lb = 'appreciation')
                                                AND fiche_date_dt >= '$date_der'");
                  $nb_fiche_obs = mysql_result($req_fiche_obs,0);
                  if ($nb_fiche_obs > 0)
                  {
                     $lien="fiche_suivi.php?utilisateur=$id_app&consulter=1&objet=observation&event=2&ordre=asc&tuteur_vue=1&date_der=$date_der&id_grp=$id_grp&vientde=annonce";
                     $lien=urlencode($lien);
                     echo "<TD align='center' width='50'><DIV id='sequence'>".
                          "<A HREF=\"trace.php?link=$lien&keepThis=true&TB_iframe=true&height=355&width=650\"".
                          " class='thickbox'>$nb_fiche_obs</A></DIV></TD>";
                  }
                  else
                     echo "<TD align='center' width='50'>&nbsp;</TD>";

                  $nom_grp = GetDataField ($connect,"select grp_nom_lb from groupe where grp_cdn  = $id_grp","grp_nom_lb");
                  $id_forum = GetDataField ($connect,"select id from forums where name='$nom_grp'","id");
                  $nom_forum = GetDataField ($connect,"select table_name from forums where name='$nom_grp'","table_name");
                  $req_forum =mysql_query("SELECT * from $nom_forum where author = \"$majuscule1\" AND ".
                                          "email = \"$id_email\" and approved='Y'");
                  $nbr_posts = mysql_num_rows($req_forum);
                      //$nbr_mess_forum = cherche_forum($id_grp,$date_compare,$majuscule1);
                  if ($nbr_posts > 0)
                  {
                     $lien = "popup_forums.php?NbrPost=$nbr_posts&id_forum=$id_forum&utilisateur=$id_app&id_grp=$id_grp";
                     $lien=urlencode($lien);
                     echo  "<TD align='center' valign='top' width='50'>".
                           "<A HREF=\"trace.php?link=$lien&keepThis=true&TB_iframe=true&height=355&width=650\"".
                           " class='thickbox'>$nbr_posts</A></TD>";
                  }
                  else
                     echo  "<TD width='40'>&nbsp;</TD>";

                  $req_traq = mysql_query("select traq_cdn from traque where traq_util_no=$id_app AND traq_grp_no= $id_grp");
                  $nbr_trq = mysql_num_rows($req_traq);
                  if ($nbr_trq > 0)
                  {
                    $lien_bilan = "bilan.php?bilan=1&utilisateur=$id_app&numero_groupe=$id_grp";
                    //$lien_bilan = urlencode($lien_bilan);
                    echo "<TD align='center' valign='top'><DIV id='sequence'>".
                         "<A HREF=\"$lien_bilan&keepThis=true&TB_iframe=true&height=355&width=650\" class='thickbox'".
                         " onmouseover=\"img_regroup$id_grp"."_$id_app.src='images/bilan/icogroupb.gif';return true;\"".
                         " onmouseout=\"img_regroup$id_grp"."_$id_app.src='images/bilan/icogroup.gif'\">".
                         "<IMG NAME=\"img_regroup$id_grp"."_$id_app\" SRC=\"images/bilan/icogroup.gif\" ".
                         "BORDER='0' width='28' height='25'".
                         " onLoad=\"tempImg=new Image(0,0); tempImg.src='images/bilan/icogroupb.gif'\"></A></DIV></TD>";
                  }
                  else
                    echo "<TD align='center' valign='top' width='30'>&nbsp;</TD>";
               $i++;
             }
             echo "</TR>";
           }
           else
            echo  "<TR height='30'><TD colspan='9' valign='top' align='center' width='100%'>".
                  "<FONT SIZE=2><B>$mess_gp_tut_nopresc</B></FONT></TD></TR>";
           echo "</TABLE></TD></TR></TABLE></TD></TR>";
           if ($affiche_app[$id_app] == 1 || $affiche_toutapp == 1 || $nomb_app == 1)
              echo "<tr height='20'><td></td></tr>";
         }
         else
         {
            echo "</TD></TR></TABLE></TD></TR>";// if (affiche [$id_app].....
            if ($affiche_app[$id_app] == 1 || $affiche_toutapp == 1 || $nomb_app == 1)
               echo "<tr height='20'><td></td></tr>";
         }
         $n_app++;
      }
    }
  //  }// fin du else premier
  echo "</TABLE></TD></TR></TABLE>";
 // Fin d'affichage
}// fin $tutorat_app == 1
?>