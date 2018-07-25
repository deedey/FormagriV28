<?php
if (!isset($_SESSION)) session_start();
if (!isset($_SESSION['id_user']) || $_SESSION['id_user'] == "")
{
  exit();
}
require "admin.inc.php";
require 'fonction.inc.php';
include ("include/UrlParam2PhpVar.inc.php");
require "lang$lg.inc.php";
require "fonction_html.inc.php";
require "langues/formation.inc.php";
//include ("click_droit.txt");
dbConnect();
if ($lg == "ru")
{
  $code_langage = "ru";
  $charset = "Windows-1251";
}
elseif ($lg == "fr")
{
  $code_langage = "fr";
  $charset = "iso-8859-1";
}
elseif ($lg == "en")
{
  $code_langage = "en";
  $charset = "iso-8859-1";
}
$Ext= "_$numero_groupe";
$nom_user= $_SESSION['name_user'];
$prenom_user = $_SESSION['prename_user'];
$majuscule = $prenom_user." ".$nom_user;
if ($utilisateur > 0)
  $id_app = $utilisateur;
else
  $id_app = $id_user;
//$majuscule = ucwords(strtolower($majuscule));
include ('style.inc.php');
$date_op = date("Y-m-d H:i:s" ,time());
$heure_fiche = substr($date_op,11);
$date_fiche = substr($date_op,0,10);
if (isset($change_date) && $change_date == 1)
{
    if (isset($iday) && $iday && isset($imonth) && $imonth && isset($iyear) && $iyear)
      $new_date="$imonth/$iday/$iyear";
    if (isset($day) && $day && isset($month) && $month && isset($year) && $year)
      $new_date="$day/$month/$year";
    $ch_datenew = explode ("/",$new_date);
    $date = "$ch_datenew[2]-$ch_datenew[1]-$ch_datenew[0]";
    $date_inv = "$ch_datenew[0]-$ch_datenew[1]-$ch_datenew[2]";
    $seq_change = mysql_query ("update prescription$Ext set
                                presc_datefin_dt = '$date' where
                                presc_seq_no = $id_seq and
                                presc_utilisateur_no = $utilisateur and
                                presc_grp_no = $numero_groupe");
    $titre_seq = GetDataField ($connect,"select seq_titre_lb from sequence where seq_cdn=$id_seq","seq_titre_lb");
    $le_parc = GetDataField ($connect,"select presc_parc_no from prescription$Ext where
                                       presc_seq_no = $id_seq and
                                       presc_utilisateur_no = $utilisateur and
                                       presc_grp_no = $numero_groupe","presc_parc_no");
    $action_fiche = "Gestion des prescriptions";
    $qualite = "Prescripteur";
    $commentaire = $mess_gp_chgdt_finpresc." ".$titre_seq." $date_fin $mess_rep_item $date_inv"."\n";
    $new_fiche = Donne_ID ($connect,"select max(fiche_cdn) from fiche_suivi");
    $req_fiche = mysql_query("INSERT INTO fiche_suivi
                              (fiche_cdn,fiche_utilisateur_no,fiche_auteur_no,fiche_qualite_lb,fiche_date_dt,fiche_heure_dt,
                              fiche_commentaire_cmt,fiche_grp_no,fiche_parc_no,fiche_seq_no,fiche_act_no,fiche_typaction_lb) VALUES
                              ($new_fiche,$utilisateur,$id_user,'$qualite','$date_fiche','$heure_fiche',\"$commentaire\",
                              $numero_groupe,$le_parc,$id_seq,0,\"$action_fiche\")");
}
$id_nom = GetDataField ($connect,"select util_nom_lb from utilisateur where util_cdn = $id_app","util_nom_lb");
$id_prenom = GetDataField ($connect,"select util_prenom_lb from utilisateur where util_cdn = $id_app","util_prenom_lb");
$id_login=GetDataField ($connect,"select util_login_lb from utilisateur where util_cdn='$id_app'","util_login_lb");
if (isset($utilisateur) && $utilisateur > 0)
   $Register = base64url_encode($utilisateur.'_'.$id_login.'|'.str_replace('http://','',str_replace('.educagri.fr','',$adresse_http)));
else
   $Register = base64url_encode($_COOKIE["monID"].'_'.$_COOKIE["monLogin"].'|'.str_replace('http://','',str_replace('.educagri.fr','',$adresse_http)));
$RegisterTwit = $Register;
  $date_du_jour=date("d/m/Y");
  $datearray=getdate();
  $jour_sem=$datearray['weekday'];
  if ($jour_sem == "Monday")
   $jour = $jour_lundi;
  if ($jour_sem == "Tuesday")
   $jour = $jour_mardi;
  if ($jour_sem == "Wednesday")
  $jour = $jour_mercredi;
 if ($jour_sem == "Thursday")
  $jour = $jour_jeudi;
 if ($jour_sem == "Friday")
  $jour = $jour_vendredi;
 if ($jour_sem == "Saturday")
  $jour = $jour_samedi;
 if ($jour_sem == "Sunday")
  $jour = $jour_dimanche;
 $aff_date = $mess_menu_lib.strtolower($jour)." $date_du_jour";
  if ($vient_de_menu == 'menu')
    $_SESSION['numero_groupe'] = $numero_groupe;
  $averti = 1;
  $_SESSION['averti'] = $averti;
if ($vient_de_menu == 'menu'){
   unset($_SESSION['numero_groupe']);
   $numero_groupe=$le_groupe;
   $_SESSION['numero_groupe'] = $numero_groupe;
}
$grp_req = mysql_query("SELECT utilgr_groupe_no from utilisateur_groupe where utilgr_utilisateur_no = '$id_app'");
$nbr = mysql_num_rows($grp_req);
//if ($nbr > 1 &&  $via_menu != 1)
  $lacouleurdefond="#FFFFFF";
//else
//  $lacouleurdefond="##002D44";
$image = GetDataField($connect,"select param_etat_lb from param_foad where param_cdn=1","param_etat_lb");
echo "<BODY bgcolor = '$lacouleurdefond' marginwidth='0' leftmargin='0'";
if ($vient_de_menu == 'menu')
   echo " topmargin='5'>";
else
   echo " topmargin='5'>";
echo "<div id=\"overDiv\" style=\"position:absolute; visibility:hiden;z-index:1000;\"></div>";
echo "<SCRIPT LANGUAGE=\"JavaScript\" SRC=\"overlib.js\"><!-- overLIB (c) Erik Bosrup --></SCRIPT>";
//------------------------------------------------------------------------------------------------------------------
// Détermine la provenance de la connection
$ip = $_SERVER['REMOTE_ADDR'];
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
  $verif_connex = mysql_query("SELECT * from log where
                               login ='$login' AND
                               date_fin ='0000-00-00'");
  $result = mysql_num_rows($verif_connex);
      $i=0;
      while ($i  < $result){
        $dates= mysql_result($verif_connex,$i,"date_fin");
        $heure= mysql_result($verif_connex,$i,"heure_fin");
        $date_d= mysql_result($verif_connex,$i,"date_debut");
        $heure_d= mysql_result($verif_connex,$i,"heure_debut");
        $adr_ip= mysql_result($verif_connex,$i,"ip");
        $ch_date = explode ("-",$dates);
        $date = "$ch_date[2]/$ch_date[1]/$ch_date[0]";
        $ch_date_hier = explode ("/",$date_debut);
        $date_hier = "$ch_date_hier[2]/$ch_date_hier[1]/$ch_date_hier[0]";
        $nbj_date = mysql_query ("select TO_DAYS('$date_d')");
        $nbj_date_lue = mysql_result ($nbj_date,0);
        $diff_dates = $nbj_date_comp - $nbj_date_lue;
        if ($depart == 1 && $diff_dates <= 1)
        {
          $req_trace = mysql_query("SELECT MAX(trace_cdn) from trace where
                                    trace_login_lb = '$login' AND
                                    trace_lien_lb != \"annonce_app.php?depart=1\" AND
                                    trace_date_dt = '$date_d'");
          $nb_trc = mysql_num_rows($req_trace);
          if ($nb_trc > 0)
          {
           $id_trc = mysql_result($req_trace,0);
           $date_trc = GetDataField ($connect,"select trace_date_dt from trace where trace_cdn  = $id_trc","trace_date_dt");
           $time_trc = GetDataField ($connect,"select trace_heure_dt from trace where trace_cdn  = $id_trc","trace_heure_dt");
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
             if (($minutes_fin > $minutes_deb) || ($minutes_fin == $minutes_deb))
             {
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
                                   date_fin ='$date_trc',heure_fin ='$time_trc',duree=$minutes_total where
                                   login='$login' AND
                                   date_debut='$date_d' AND
                                   heure_debut ='$heure_d' AND
                                   date_fin ='0000-00-00'");// '
           }
           else
             $inserer=mysql_query("UPDATE log SET
                                   date_fin ='$date_trc',heure_fin ='$time_trc',duree=0 where
                                   login='$login' AND
                                   date_debut='$date_d' AND
                                   heure_debut ='$heure_d' AND
                                   date_fin ='0000-00-00'");// '
          }
        }
        if ($depart == 1 && $diff_dates > 1)
           $inserer=mysql_query("UPDATE log SET
                                 date_fin ='$date_d',heure_fin ='$heure_d',duree=0 where
                                 login='$login' AND
                                 date_debut='$date_d' AND
                                 heure_debut ='$heure_d' AND
                                 date_fin ='0000-00-00'");// '
      $i++;
      }
      $id_max = Donne_ID ($connect,"select max(log_cdn) from log");
      if ($depart == 1)
         $inserer=mysql_query ("INSERT INTO log
                                (log_cdn,date_debut,heure_debut,login,serveur,ip) VALUES
                                ($id_max,'$date_debut','$heure_debut','$login','$ChampServeur','$ip')");
         echo"<center><TABLE width='98%'  border='0'><TR><TD width='100%'><TABLE cellpadding='2' cellspacing='0' border='0' width='100%'>";
//      if ($typ_user != "APPRENANT")
//      {
            echo "<TR><TD width='100%' colspan=3>";
/*
            $nom_grpe =  GetDataField ($connect,"select grp_nom_lb from groupe where grp_cdn = $numero_groupe","grp_nom_lb");
            $id_forum = GetDataField ($connect,"select id from forums where name ='$nom_grpe'","id");
            $comment_forum = GetDataField ($connect,"select name from forums where id='$id_forum'","name");
            $lien_forum="forum/list.php?f=$id_forum&collapse=1";
            $lien_forum = urlencode($lien_forum);
            echo "<div id='forum' style=\"float:left;padding-right:10px;\"><A HREF=\"trace.php?link=$lien_forum\" class='bouton_new'>$mess_menu_forum $mess_menu_gestion_grp</A></div>";
*/
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
         if ($_SESSION['onLine'] == 1)
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
                                $('#tincan_F_$id_forum').css('display','block');
                             }
                         }
                 });
         </script></div>";
         echo "<DIV id='tincan_F_$id_forum' style='float:left;display:none;'><A HREF = \"$lien\" ".
         "name='Forum ".$nom_grpe." : historique' class='thickbox'>".
         "<div id='xApi_F_$id_forum' style='float:left;background-image:".
         "url(\"images/ecran-annonce/ongl01.gif\");margin-left:2px;'></div></A></div></div>";
            $lien_rp = "modif_rep_fic.php?id_grp=$numero_groupe&formateurs=&nom_grp=$nom_grpe&communes_groupe=1&rallonge=ressources/groupes/$numero_groupe&sousdos=ressources/groupes/$numero_groupe&parent=ressources/groupes&dossier=ressources/groupes/$numero_groupe&fichier=$numero_groupe&direct=dossier";
            $lien_rp = urlencode($lien_rp);
            echo "<div id='dossiers' style=\"float:left;padding-right:10px;\"><A HREF=\"trace.php?link=$lien_rp\" class='bouton_new'>$mess_menu_casier_groupe $mess_menu_gestion_grp</A></div>";
            $fichier_aide = ($typ_user == "APPRENANT") ? "formation_apprenant" : "formation_formateur";
            echo aide_div("$fichier_aide",0,0,0,0);
            echo "</td></tr>";
//      }
      $grp_req = mysql_query("SELECT utilgr_groupe_no from utilisateur_groupe where utilgr_utilisateur_no = '$id_app'");
      $nbr = mysql_num_rows($grp_req);
      if ($nbr > 0)
      {
        $nom_grp = GetDataField ($connect,"select grp_nom_lb from groupe WHERE grp_cdn = $numero_groupe","grp_nom_lb");
        echo "<TR width='100%'><TD align='left' nowrap colspan='3' height='40' valign='middle'><font color='#333333' size=3><B>$mess_grp_form : $nom_grp</B></font></TD></TR>";
      }
// insertion de messages urgents
     if  ($numero_groupe > 0)
     {
        $req_presc = mysql_query("SELECT COUNT(*) from prescription$Ext where
                                presc_utilisateur_no = $id_app and
                                presc_grp_no=$numero_groupe");
        $nb_presc = mysql_result($req_presc,0);
      if ($nb_presc > 0)
      {
//        $lien = "gest_frm_rappel1.php?utilisateur=$utilisateur&a_faire=1&graph=1&tout=1";
//        $lien = "gest_frm_rappel.php?utilisateur=$utilisateur&a_faire=1&graph=0&tout=0";
        $lien="gest_parc_frm1.php?hgrp=$hgrp&saut=1&utilisateur=$utilisateur&a_faire=1&numero_groupe=$numero_groupe&tout=1&graph=1";
        $lien = urlencode($lien);
        echo "<TR height='40'><TD align='left' valign='top'><A href=\"trace.php?link=$lien\" target='main' ".
             "onmouseover=\"img_form.src='images/ecran-annonce/icogob.gif';return true;\" onmouseout=\"img_form.src='images/ecran-annonce/icogo.gif'\">";
        echo "<IMG NAME=\"img_form\" SRC=\"images/ecran-annonce/icogo.gif\" BORDER='0' onLoad=\"tempImg=new Image(0,0); tempImg.src='images/ecran-annonce/icogob.gif'\"></TD></A>";
        echo "<TD align='left' valign='top' width='95%'><DIV class='sequence'>".
             "<A href=\"trace.php?link=$lien\" target='main' ".bulle($mess_rep_graph,"","LEFT","ABOVE",200).
             "<FONT SIZE='2'>$mess_rep_graph</FONT></A></DIV></TD></TR>";
      }else
        echo  "<TR><TD align='middle' valign='top' colspan='2'><FONT SIZE=2>$mess_gp_app_nogr</FONT></TD></TR>";
     }else
        echo  "<TR><TD align='middle' valign='top' colspan='2'><FONT SIZE=2>$mess_gp_app_nogr</FONT></TD></TR>";
//     echo  "<TR><TD valign='bottom' colspan='3' height='40'>- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - </TD></TR>";
     if ($numero_groupe > 0)
     {
       $req_msg = mysql_query("SELECT * from message where
                              ((msg_dhdeb_dt < '$madate_compare' OR msg_dhdeb_dt = '$madate_compare') AND
                              (msg_dhfin_dt = '$madate_compare' OR msg_dhfin_dt > '$madate_compare')) AND
                              msg_apprenant_no = $id_app AND msg_groupe_no = $numero_groupe");
       $nb_msg = mysql_num_rows($req_msg);
       $g = 0;
       if ($nb_msg > 0)
       {
         $passage = 1;
         echo "<TR><TD align='left' valign='top' style=\"background-color:#edebec;padding:0px;\">".
              "<IMG SRC=\"images/ecran-annonce/gus01.gif\" border = '0' title ='$mess_avertis'></TD>";
         echo "<TD align='left' valign=top style=\"padding:0px;background-color:#edebec;width:auto;\"><B>$mess_msg_indiv :</B>";
         while ($g < $nb_msg){
          $id_msg = mysql_result($req_msg,$g,"msg_cdn");
          $contenus = mysql_result($req_msg,$g,"msg_contenu_cmt");
          $contenu = html_entity_decode($contenus,ENT_QUOTES,'ISO-8859-1');
          $auteur = mysql_result($req_msg,$g,"msg_auteur_no");
          echo "<LI>";
          $nom_form = GetDataField ($connect,"select util_nom_lb from utilisateur where util_cdn='$auteur'","util_nom_lb");
          $prenom_form = GetDataField ($connect,"select util_prenom_lb from utilisateur where util_cdn='$auteur'","util_prenom_lb");
          $type_form = GetDataField ($connect,"select util_typutil_lb from utilisateur where util_cdn='$auteur'","util_typutil_lb");
          if ($type_form != "ADMINISTRATEUR")
          {
            $email_form = GetDataField ($connect,"select util_email_lb from utilisateur where util_cdn='$auteur'","util_email_lb");
            $sujet = "$mess_sujet_msg";
            $lien = "mail.php?contacter=1&a_qui=$email_form&num=$auteur&sujet=$sujet";
            $lien = urlencode($lien);
            if ($id_app == $id_user)
               echo "<A HREF=\"#\" onclick=\"window.open('trace.php?link=$lien','','width=680,height=390,resizable=yes,status=no')\" title=\"$msq_formateur : $prenom_form $nom_form ->$mess_ecrire\"><font size='1' >$prenom_form $nom_form</FONT></A> : ";
            else
               echo "$prenom_form $nom_form  ";
          }else
            echo ucfirst(strtolower($mess_typ_adm))." : ";
          echo "$contenu</LI>";
          $g++;
         }
       }
       $req_msg = mysql_query("SELECT * from message where
                               ((msg_dhdeb_dt < '$madate_compare' OR msg_dhdeb_dt = '$madate_compare') AND
                               (msg_dhfin_dt = '$madate_compare' OR msg_dhfin_dt > '$madate_compare')) AND
                               msg_groupe_no = $numero_groupe AND msg_apprenant_no = 0");
       $nb_msg = mysql_num_rows($req_msg);
       if ($nb_msg == 0)
          echo "</TD></TR>";
       $g = 0;
       if ($nb_msg > 0)
       {
         if ($passage == 0)
         {
            echo "<TR><TD align='left' valign='top' style=\"padding:0px;background-color:#edebec;\">".
                 "<IMG SRC=\"images/ecran-annonce/gus01.gif\" border = '0' title ='$mess_avertis'></td>".
                 "<td valign=top style=\"padding:0px;background-color:#edebec; width:auto;\">";
         }
         echo "<br /><B>$mess_msg_grp $nom_grp :</B>";
         while ($g < $nb_msg)
         {
          $id_msg = mysql_result($req_msg,$g,"msg_cdn");
          $contenus = mysql_result($req_msg,$g,"msg_contenu_cmt");
          $contenu = html_entity_decode($contenus,ENT_QUOTES,'ISO-8859-1');
          $auteur = mysql_result($req_msg,$g,"msg_auteur_no");
          echo "<LI>";
          $nom_form = GetDataField ($connect,"select util_nom_lb from utilisateur where util_cdn='$auteur'","util_nom_lb");
          $prenom_form = GetDataField ($connect,"select util_prenom_lb from utilisateur where util_cdn='$auteur'","util_prenom_lb");
          $type_form = GetDataField ($connect,"select util_typutil_lb from utilisateur where util_cdn='$auteur'","util_typutil_lb");
          if ($type_form != "ADMINISTRATEUR"){
            $email_form = GetDataField ($connect,"select util_email_lb from utilisateur where util_cdn='$auteur'","util_email_lb");
            $sujet = "$mess_sujet_msg";
            $lien = "mail.php?contacter=1&a_qui=$email_form&num=$auteur&sujet=$sujet";
            $lien = urlencode($lien);
            if ($id_app == $id_user)
               echo "<A HREF=\"#\" onclick=\"window.open('trace.php?link=$lien','','width=680,height=390,resizable=yes,status=no')\" title=\"$msq_formateur : $prenom_form $nom_form ->$mess_ecrire\"><font size='1' >$prenom_form $nom_form</FONT></A> : ";
            else
            echo "$prenom_form $nom_form  ";
          }
          else
            echo ucfirst(strtolower($mess_typ_adm))." : ";
          echo "$contenu</LI>";
          $g++;
        }
        echo"</TD></TR>";
      }
      //if ($nb_msg > 0)
      //   echo  "<TR><TD valign='bottom' colspan='3' height='20'>- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - </TD></TR>";
    }
//    echo "<TR><TD width='2%'>&nbsp;</TD>";
     if ($typ_user == "APPRENANT" || $typ_user == "TUTEUR")
        $req = mysql_query("select presc_seq_no,presc_datefin_dt,presc_parc_no from prescription$Ext where
                            presc_utilisateur_no = $id_app and
                            presc_grp_no=$numero_groupe
                            order by presc_datefin_dt desc");
     elseif($typ_user == "FORMATEUR_REFERENT")
        $req = mysql_query("select presc_seq_no,presc_datefin_dt,presc_parc_no from prescription$Ext where
                            presc_utilisateur_no = $id_app and
                            presc_formateur_no = $id_user and
                            presc_grp_no=$numero_groupe
                            order by presc_datefin_dt desc");
     elseif($typ_user == "RESPONSABLE_FORMATION")
        $req = mysql_query("select presc_seq_no,presc_datefin_dt,presc_parc_no from prescription$Ext where
                            presc_utilisateur_no = $id_app and
                            presc_prescripteur_no = $id_user and
                            presc_grp_no=$numero_groupe
                            order by presc_datefin_dt desc");
     elseif($typ_user == "ADMINISTRATEUR")
        $req = mysql_query("select presc_seq_no,presc_datefin_dt,presc_parc_no from prescription$Ext where
                            presc_utilisateur_no = $id_app and
                            presc_grp_no=$numero_groupe
                            order by presc_datefin_dt desc");
     $nb_req = mysql_num_rows($req);
     $jj = 0;
     if ($nb_req > 0)
     {
       $today = date("Y-n-d");
       $ii = 0;$kk = 0;
       while ($ii < $nb_req)
       {
         $id_seq = mysql_result($req,$ii,"presc_seq_no");
         $titre_sequence = GetDataField ($connect,"select seq_titre_lb from sequence where seq_cdn = '$id_seq'","seq_titre_lb");
         $id_parc = mysql_result($req,$ii,"presc_parc_no");
         $date_fin = mysql_result($req,$ii,"presc_datefin_dt");
         $nb_date_fin = nb_jours($date_fin);
         $nb_today = nb_jours($today);
              $prerequis="";
              $prereq_query = mysql_query ("select * from prerequis where prereq_seq_no = $id_seq");
              $nb_prereq = mysql_num_rows ($prereq_query);
              $etat_seq = GetDataField ($connect,"select suiv2_etat_lb from suivi2$Ext where
                                                  suiv2_seq_no =$id_seq AND
                                                  suiv2_utilisateur_no = $id_app AND
                                                  suiv2_grp_no = $numero_groupe","suiv2_etat_lb");
              if ($nb_prereq != 0)
              {
                $nb_proposable = 0;
                $jj = 0;
                while ($jj < $nb_prereq)
                {
                  //on raisonne selon le type de condition
                  $type_condition = mysql_result ($prereq_query,$jj,"prereq_typcondition_lb");
                  if ($type_condition == 'SEQUENCE') {
                      $condition = mysql_result ($prereq_query,$jj,"prereq_seqcondition_no");
                      $etat_seq_req = GetDataField ($connect,"select suiv2_etat_lb from suivi2$Ext where
                                                              suiv2_seq_no = $condition and
                                                              suiv2_utilisateur_no = $id_app AND
                                                              suiv2_grp_no = $numero_groupe","suiv2_etat_lb");
                      if ($etat_seq_req != 'TERMINE')  {
                          $motif = $mess_gp_seq_preq;
                          $proposable = 0;
                          $prerequis = $mess_seq_prq." <B>".str_replace("'","|",$seq_prereq)."</B> ".$mess_no_fin;
                      }else {
                        $nb_proposable++;
                      }
                  }

                  //on a besoin du numero de l'activite pour recuperer les notes
                  if ($type_condition == 'ACTIVITE') {
                      $condition = mysql_result ($prereq_query,$jj,"prereq_actcondition_no");
                      $etat_act_req = GetDataField ($connect,"select suivi_etat_lb from suivi1$Ext where
                                                              suivi_act_no = $condition and
                                                              suivi_utilisateur_no = $id_app AND
                                                              suivi_grp_no = $numero_groupe","suivi_etat_lb");
                      if ($etat_act_req != 'TERMINE') {
                          $motif = $mess_gp_aut_act;
                          $proposable = 0;
                          $prerequis = $mess_act_prq." <B>".str_replace("'","|",$act_prereq)."</B> ".$mess_no_fin;
                      }else {
                        $nb_proposable++;
                      }
                  }

                  if ($type_condition == 'NOTE') {
                     $condition = mysql_result ($prereq_query,$jj,"prereq_actcondition_no");
                      $etat_act_req = GetDataField ($connect,"select suivi_etat_lb from suivi1$Ext where
                                                              suivi_act_no = $condition and
                                                              suivi_utilisateur_no = $id_app AND
                                                              suivi_grp_no = $numero_groupe","suivi_etat_lb");
                     if ($etat_act_req != 'TERMINE') {
                          $motif = $mess_gp_act_preq;
                          $proposable = 0;
                     }else{
                      $note_min = mysql_result ($prereq_query,$jj,"prereq_notemin_nb1");
                      $note_max = mysql_result ($prereq_query,$jj,"prereq_notemax_nb1");
                      $note_obtenue = GetDataField ($connect,"select suivi_note_nb1  from suivi1$Ext where
                                                              suivi_act_no = $condition and
                                                              suivi_utilisateur_no = $id_app AND
                                                              suivi_grp_no = $numero_groupe"  ,"suivi_note_nb1");
                      if (($note_obtenue < $note_min || $note_obtenue > $note_max) || $note_obtenue == "acquis")  {
                        $motif = $mess_gp_note;
                        $proposable = 0;
                        $prerequis = $mess_note_prq;
                      }else{
                        $nb_proposable++;
                      }
                    }
                  }
              $jj++;
              }
              if ($nb_proposable == $nb_prereq)
              {
                 $proposable=1;
                 $prerequis="OK";
              }
              else
              {
                 $proposable = 0;
              }
            } //fin if ($nb_prereq !=0)
            else
              $proposable = 1;
            if ($nb_date_deb <= $nb_today)
               $visible = 1;
            else
               $visible = 0;
            if ($nb_date_fin >= ($nb_today))
               $depasse=0;
            if ($nb_date_fin < ($nb_today))
            {
               $depasse=1;
               $diff = $nb_today - $nb_date_fin;
               if ($etat_seq == "A FAIRE" || $etat_seq == "EN COURS")
               {
                  $kk++;
                  $actform = GetDataField ($connect,"select presc_formateur_no from prescription$Ext where
                                                     presc_seq_no = $id_seq and
                                                     presc_utilisateur_no = $id_app and
                                                     presc_grp_no = $numero_groupe","presc_formateur_no");
                  $prescform = GetDataField ($connect,"select presc_prescripteur_no from prescription$Ext where
                                                     presc_seq_no = $id_seq and
                                                     presc_utilisateur_no = $id_app and
                                                     presc_grp_no = $numero_groupe","presc_prescripteur_no");
                  $email_form = GetDataField ($connect, "select util_email_lb from utilisateur where util_cdn=$actform","util_email_lb");
                  $nom_form = GetDataField ($connect,"select util_nom_lb from utilisateur where util_cdn='$actform'","util_nom_lb");
                  $prenom_form = GetDataField ($connect,"select util_prenom_lb from utilisateur where util_cdn='$actform'","util_prenom_lb");
                  $sujet = "$mess_sujet $titre_sequence";
                  $ch_date_fin = explode ("-",$date_fin);
                  if ($kk == 1)
                  {
                     $mess_depasse = "<TR><TD height='20' colspan='2'>&nbsp;</TD></TR>".
                                     "<TR><TD align=left valign=top><IMG SRC='images/gest_parc/icoflashb.gif' border='0'></TD>".
                                     "<TD align=left valign='top' width='92%'><font size='2' color='#D45211'><B>$mess_acces_depasse</B></FONT>";
                     if ($typ_user == "APPRENANT")
                        $mess_depasse = "<TR><TD align=left valign='center' colspan='2'><font size='2' color='#333333'><B>$mess_seqno_dispo ; $mess_seqno_redef</B></TD></TR>";
                     elseif ($typ_user != "APPRENANT" && $typ_user == "TUTEUR")
                        $mess_depasse = "<TR><TD align=left valign='center' colspan='2'><font size='2' color='#333333'><B>$mess_seqno_dispo</B></TD></TR>";
                     elseif ($typ_user != "APPRENANT" && ($typ_user == "FORMATEUR_REFERENT" || $typ_user == "RESPONSABLE_FORMATION" || $typ_user == "ADMINISTRATEUR"))
                        $mess_depasse = "<TR><TD align=left valign='center' colspan='2'><font size='2' color='#333333'><B>$mess_seqno_dispo ; $mess_seqno_new_date</B></TD></TR>";
                  }
                  if ($typ_user == "APPRENANT")
                  {
                     $icone = " title=\"$msq_formateur : $nom_form $prenom_form ->$mess_ecrire\" ".
                              "onmouseover=\"img_lettre$ii.src='images/ecran-annonce/icolettrebw.gif';return true;\" onmouseout=\"img_lettre$ii.src='images/ecran-annonce/icolettrew.gif'\">".
                              "<IMG NAME=\"img_lettre$ii\" SRC=\"images/ecran-annonce/icolettrew.gif\" BORDER='0' onLoad=\"tempImg=new Image(0,0); tempImg.src='images/ecran-annonce/icolettrebw.gif'\">";
                     $lien = "mail.php?contacter=1&a_qui=$email_form&num=$actform&sujet=$sujet&sequence=1";
                     $lien = urlencode($lien);
                     $mess_depasse .= "<TR><TD align='left' valign='top' colspan='2'><A HREF=\"#\" ";
                     $mess_depasse .= "onclick=\"window.open('trace.php?link=$lien','','width=680,height=390,resizable=yes,status=no')\" $icone";
                     $mess_depasse .= "&nbsp;&nbsp;<font size='2'>".ucwords($mess_dep_form)." $diff $mess_jours : $titre_sequence</FONT></A></TD></TR>";
                  }
                  elseif ($typ_user != "APPRENANT" && (($actform == $id_user && $typ_user == "FORMATEUR_REFERENT") || ($prescform == $id_user && $typ_user == "RESPONSABLE_FORMATION") || $typ_user == "ADMINISTRATEUR"))
                  {
                     $icone = "><IMG SRC=\"images/repertoire/icoptirenom.gif\" BORDER='0'>";
                     $lien = "prescription.php?vient_de_app=1&numero_groupe=$numero_groupe&hgrp=$hgrp&change_date=1&day=$ch_date_fin[2]&month=$ch_date_fin[1]&year=$ch_date_fin[0]&id_seq=$id_seq&utilisateur=$utilisateur";  //&entantque=$entantque
                     $lien = urlencode($lien);
                     $mess_depasse .= "<TR><TD align='left' valign='top' colspan='2'><A HREF=\"trace.php?link=$lien\" $icone";
                     $mess_depasse .= "&nbsp;&nbsp;<font size='2'>".ucwords($mess_dep_form)." $diff $mess_jours : $titre_sequence</FONT></A></TD></TR>";
                  }
                  elseif ($typ_user != "APPRENANT" && $entantque == "tut")
                     $mess_depasse .= "<TR><TD align='left' valign='top' colspan='2'>&nbsp;&nbsp;<font size='2'>".ucwords($mess_dep_form)." $diff $mess_jours : $titre_sequence</FONT></TD></TR>";
               }
           }
           $jj=0;
           for ($nn = 1;$nn < 10;$nn++)
           {
             if ($nb_date_fin == ($nb_today+$nn))
             {
               if ($etat_seq == "A FAIRE" || $etat_seq == "EN COURS")
               {
                  $jj++;
                  if ($jj == 1)
                      $mess_suivi .= "<TR><TD colspan='3' align='left'>";
                  if ($id_app == $id_user)
                     $mess_suivi .= "<A HREF=\"gest_frm_rappel1.php?prq=$prerequis&depasse=$depasse&nb_prereq=$nb_prereq&visible=$visible&proposable=$proposable&accord=$proposable&page=1&vn=1&saut=0&a_faire=1&id_seq=$id_seq&seq_ouverte=$id_seq&parc_ouvert=$id_parc&graph=1&tout=1\" target='main'";
                  elseif ($utilisateur > 0 && ($entantque== 'form' || $entantque== 'presc'))
                  {
                     $lien="gest_parc1.php?formation=0&graph=1&numero_groupe=$numero_groupe&depasse=$depasse&proposable=$proposable&visible=$visible&hgrp=$hgrp&utilisateur=$utilisateur&a_faire=1&seq=$id_seq&actif_seq=$id_seq&actif_parc=$id_parc&parc=$id_parc&tout=1&voir_parc=$id_parc";
                     $lien=urlencode($lien);
                     $lien1 ="details_parc.php?numero_groupe=$numero_groupe&hgrp=$hgrp&prq=$prerequis&accord=$accord&depasse=$depasse&visible=$visible&id_parc=$id_parc&seq=$id_seq&ouvrir=sequence&utilisateur=$utilisateur";
                     $lien1 = urlencode($lien1);
                     $mess_suivi .= "<TR><TD align='left' valign='top' colspan='2'><A HREF=\"trace.php?link=$lien\" target='idx' onClick='parent.principal.location=\"trace.php?link=$lien1\"' "
                                    .bullet($mess_reinit,"","LEFT","ABOVE",200);
                  }
                  else
                     $mess_suivi .= "<A HREF=\"#\" ";
                  $mess_suivi .= " onmouseover=\"img_danger$nn.src='images/ecran-annonce/icoBdangerb.gif';return true;\" onmouseout=\"img_danger$nn.src='images/ecran-annonce/icoBdanger.gif'\">".
                                 "<IMG NAME=\"img_danger$nn\" SRC=\"images/ecran-annonce/icoBdanger.gif\" BORDER='0' onLoad=\"tempImg=new Image(0,0); tempImg.src='images/ecran-annonce/icoBdangerb.gif'\">".
                                 "&nbsp;&nbsp;<font size='2'><span style='cursor:help;'>$mess_mess_att1 $nn $mess_mess_att2 $titre_sequence</span></FONT></A></TD></TR>";
               }
             }
           }
           if ($utilisateur > 0 && ($typ_user == "FORMATEUR_REFERENT" || $typ_user == "RESPONSABLE_FORMATION" || $typ_user == "ADMINISTRATEUR"))
           {
               if ($typ_user == "FORMATEUR_REFERENT")
                  $act_ficreq = mysql_query ("select count(suivi1$Ext.suivi_act_no) from
                                              suivi1$Ext,activite,prescription$Ext where
                                              suivi1$Ext.suivi_act_no=activite.act_cdn AND
                                              activite.act_seq_no=$id_seq AND
                                              activite.act_devoirarendre_on='OUI' AND
                                              activite.act_notation_on='OUI' AND
                                              prescription$Ext.presc_seq_no=$id_seq AND
                                              prescription$Ext.presc_formateur_no=$id_user AND
                                              prescription$Ext.presc_utilisateur_no=$utilisateur AND
                                              suivi1$Ext.suivi_utilisateur_no=$utilisateur AND
                                              suivi1$Ext.suivi_grp_no=$numero_groupe AND
                                              prescription$Ext.presc_grp_no=$numero_groupe AND
                                              suivi1$Ext.suivi_etat_lb = 'ATTENTE' AND
                                              suivi1$Ext.suivi_fichier_lb != ''");
               elseif ($typ_user == "RESPONSABLE_FORMATION")
                  $act_ficreq = mysql_query ("select count(suivi1$Ext.suivi_act_no) from
                                              suivi1$Ext,activite,prescription$Ext where
                                              suivi1$Ext.suivi_act_no=activite.act_cdn AND
                                              activite.act_seq_no=$id_seq AND
                                              activite.act_devoirarendre_on='OUI' AND
                                              activite.act_notation_on='OUI' AND
                                              prescription$Ext.presc_seq_no=$id_seq AND
                                              prescription$Ext.presc_prescripteur_no=$id_user AND
                                              prescription$Ext.presc_utilisateur_no=$utilisateur AND
                                              suivi1$Ext.suivi_utilisateur_no=$utilisateur AND
                                              suivi1$Ext.suivi_grp_no=$numero_groupe AND
                                              prescription$Ext.presc_grp_no=$numero_groupe AND
                                              suivi1$Ext.suivi_etat_lb = 'ATTENTE' AND
                                              suivi1$Ext.suivi_fichier_lb != ''");
               elseif ($typ_user == "ADMINISTRATEUR")
                  $act_ficreq = mysql_query ("select count(suivi1$Ext.suivi_act_no) from
                                              suivi1$Ext,activite,prescription$Ext where
                                              suivi1$Ext.suivi_act_no=activite.act_cdn AND
                                              activite.act_seq_no=$id_seq AND
                                              activite.act_devoirarendre_on='OUI' AND
                                              activite.act_notation_on='OUI' AND
                                              prescription$Ext.presc_seq_no=$id_seq AND
                                              prescription$Ext.presc_utilisateur_no=$utilisateur AND
                                              suivi1$Ext.suivi_utilisateur_no=$utilisateur AND
                                              suivi1$Ext.suivi_grp_no=$numero_groupe AND
                                              prescription$Ext.presc_grp_no=$numero_groupe AND
                                              suivi1$Ext.suivi_etat_lb = 'ATTENTE' AND
                                              suivi1$Ext.suivi_fichier_lb != ''");
               $nb_file = mysql_result($act_ficreq,0);
               $id_parc = GetDataField ($connect,"SELECT presc_parc_no FROM prescription$Ext WHERE
                                                  presc_seq_no = $id_seq AND
                                                  presc_utilisateur_no = $utilisateur AND
                                                  presc_grp_no=$numero_groupe","presc_parc_no");
               $actif_parc = $id_parc;
               $actif_seq = $id_seq;
               $icone = "><IMG SRC=\"images/repertoire/icoptirenom.gif\" BORDER='0'>";
               if ($nb_file > 0)
               {
                 if ($xfile == 0)
                    $mess_fichier .= "<TR><TD align='left' valign='top' colspan='2'><B>$mess_gp_seq_parc $mess_seq_nb_fic</B></TD></TR>";
                 $lien="gest_parc1.php?formation=0&graph=1&numero_groupe=$numero_groupe&depasse=$depasse&proposable=$proposable&visible=$visible&hgrp=$hgrp&utilisateur=$utilisateur&a_faire=1&seq=$id_seq&actif_seq=$id_seq&actif_parc=$id_parc&parc=$id_parc&tout=1&voir_parc=$id_parc";
                 $lien=urlencode($lien);
                 $lien1 ="details_parc.php?numero_groupe=$numero_groupe&hgrp=$hgrp&prq=$prerequis&accord=$accord&depasse=$depasse&visible=$visible&id_parc=$id_parc&seq=$id_seq&ouvrir=sequence&utilisateur=$utilisateur";
                 $lien1 = urlencode($lien1);
                 $mess_fichier .= "<TR><TD align='left' valign='top' colspan='2'><A HREF=\"trace.php?link=$lien\" target='idx' onClick='parent.principal.location=\"trace.php?link=$lien1\"'; $icone";
                 $mess_fichier .= "&nbsp;&nbsp;<font size='2'> $titre_sequence";
                 if ($nb_file > 1)
                    $mess_fichier .= " ($nb_file ".strtolower($mess_trx_rendus).")";
                 $mess_fichier .= "</FONT></A></TD></TR>";
                 $xfile++;
               }
           }
        $ii++;
        }
   }

if ($mess_suivi != "" || $mess_depasse != "" || $mess_fichier != "")
   echo "</CENTER><TABLE border='0' width=100%>$mess_suivi $mess_depasse $mess_fichier</TABLE>";

if ($typ_user != 'APPRENANT')
     echo boutret(1,1);
echo "</TD></TR></TABLE></TD></TR></TABLE>";
?>
