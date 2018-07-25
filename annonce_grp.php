<?php
if (!isset($_SESSION)) session_start();
if (!isset($_SESSION['id_user']) || $_SESSION['id_user'] == "")
{
  exit();
}
require "admin.inc.php";
require 'fonction.inc.php';
include "include/UrlParam2PhpVar.inc.php";
require 'fonction_html.inc.php';
require 'accueil/fonction_accueil.inc.php';
require "lang$lg.inc.php";
//include ("click_droit.txt");
dbConnect();
if (isset($lg))
{
  if ($lg == "ru")
  {
    $code_langage = "ru";
    $charset = "Windows-1251";
    putenv("TZ=Europe/Moscow");
  }
  elseif ($lg == "fr")
  {
    $code_langage = "fr";
    $charset = "iso-8859-1";
    putenv("TZ=Europe/Paris");
  }
  elseif ($lg == "en")
  {
    $code_langage = "en";
    $charset = "iso-8859-1";
  }
}
  $agent = $_SERVER['HTTP_USER_AGENT'];
  if (strstr($agent,"Mac") || strstr($agent,"Konqueror"))
    $mac=1;
  if (strstr($agent,"MSIE") || strstr($agent,"Opera"))
    $revient=1;
  if (isset($full) && $full == 1 && isset($revient) && $revient == 1 && (!isset($complement) || $complement != 1)){
   ?>
   <SCRIPT Language="Javascript">
     window.parent.opener.location.reload('index.php?fermeture=1');
   </SCRIPT>
   <?php
  }elseif(isset($full) && $full == 1 && $mac != 1 && $revient != 1){
   ?>
   <SCRIPT Language="Javascript">
      parent.parent.opener.close();
   </SCRIPT>
   <?php
  }
//de quel type est l'utilisateur (apprenant, formateur, administrateur)
$agent=getenv("HTTP_USER_AGENT");
$ip = getenv('REMOTE_ADDR');
if (IsIPv6($ip) == TRUE)
   $serveur = gethostbyaddr($_SERVER['REMOTE_ADDR']);
else
   $serveur = $_SERVER['REMOTE_ADDR'];
$ChampServeur = $serveur."-".$_COOKIE['maVille']."-".$_COOKIE['maRegion']."-".$_COOKIE['monPays'];
$nom_user = $_SESSION['name_user'];
$prenom_user = $_SESSION['prename_user'];
$majuscule = $prenom_user." ".$nom_user;
$bkg = GetDataField($connect,"select param_etat_lb from param_foad where param_typ_lb = 'bkg'","param_etat_lb");
//$majuscule = ucwords(strtolower($majuscule));
include('style.inc.php');

$url = parse_url($_SERVER['REQUEST_URI']);
$resultUrl=array();
parse_str($url['query'],$resultUrl);
if (isset($resultUrl['endpoint']))
{

echo "<script type='text/javascript' src='lib/TinCanGeneric/scripts/TinCanJS/build/tincan-min.js'></script>";

echo '<script type="text/javascript">
  FormagriExample = {};
  var getTitre = "'.$RoleUser.' : '.$ChampServeur.'";
  FormagriExample.CourseActivity = {
    id: "http://formagri.com/Suivi",
    definition: {
        type: "http://adlnet.gov/expapi/activities/course",
        name: {
            "fr-FR": "formagri.com/suivi - Tin Can Course"
        },
        description: {
            "fr-FR": getTitre
        }
    }
  };

  FormagriExample.getContext = function(parentActivityId) {
    var ctx = {
        contextActivities: {
            grouping: {
                id: FormagriExample.CourseActivity.id
            }
        }
    };
    if (parentActivityId !== undefined && parentActivityId !== null) {
        ctx.contextActivities.parent = {
            id: parentActivityId
        };
    }
    return ctx;
  };
  var tincan = new TinCan (
  {
    url: window.location.href,
    activity: {
       id: "Suivi : " + getTitre,
       definition: {
          name: {
             "fr-FR": "Suivi : " + getTitre
          },
          description: {
             "fr-FR":  "'.$_COOKIE['monPrenom'].' '.$_COOKIE['monNom'].' s\'est connecté."
          }
       }
    }
  }
  );

  tincan.sendStatement(
            {
                verb: "logged_in",
                context: FormagriExample.getContext(
                    FormagriExample.CourseActivity.id
                )
            },
            function () {}
  );
</script>
';
}
  $date_du_jour=date("d/m/Y");
  $date_jour=date("Y-m-d");
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
  $aff_date = $mess_menu_lib." $date_du_jour";
  $averti = 1;
  $_SESSION['averti'] = $averti;

//------------------------------------------------------------------------------------------------------------------
// Détermine la provenance de la connection
  $affiche_titre .= "<TABLE cellspacing='0' width='775' border='0'>"; //|| $depart == 1
  $lien =  urlencode("connecte1.php");
  $affiche_titre .= "<TR height='65'><TD align='left' valign='center' colspan='2'>".
                    "<div id='gus'><img src='images/ecran-annonce/gus02.gif' style=\"float:left;padding-right:8px; border:0;\"></div>".
                    "<div id='whois' style=\"float:left;padding-top:15px; border:0;\">".
                    "<A HREF=\"#\" class='bouton_new' onclick=\"window.open('trace.php?link=$lien','','scrollbars=1,resizable=yes,width=400,height=305')\">".
                    "$mess_whoisH</A></div>".aide_div("accueil_apprenant",8,0,15,0);
  $affiche_titre .= "<div style='padding-top:15px;'>
                    <a href='appliMobile.php?&keepThis=true&TB_iframe=true&height=400&width=550' class='thickbox'
                    title='Application mobile de Formagri' name='Application mobile de Formagri'>
                    <div class='bouton_vert' onMouseOver=\"\$(this).removeClass();\$(this).addClass('bouton_hover');\"
                    onMouseOut=\"\$(this).removeClass();\$(this).addClass('bouton_vert');\">Formagri mobile</div></a></div>";
  $affiche_titre .= "</td></tr>";
  $req_mess = mysql_query ("SELECT * from messagerie where id_user = $id_user AND lu = '1' AND supprime = '0'");
  $nbr_mess= mysql_num_rows($req_mess);
  if ($nbr_mess > 0){
     $lien="messagerie.php?vient_de_menu=$vient_de_menu&complement=$complement&ret=1";
     $lien=urlencode($lien);
     $affiche_titre .= "<TR height='30'><TD align='left' valign='center' colspan=2 width='753'>".
                       "<div id='lettre'><A href=\"trace.php?link=$lien\"><IMG NAME=\"img_lettre1\" SRC=\"images/ecran-annonce/icolettre.gif\" ".
                       "style=\"float:left;margin-left:10px; border:0;\"></A></div>".
                       "<div id='mails' style=\"float:left;margin-left:8px;\"><A href=\"trace.php?link=$lien\">".
                       "$vous_avez $nbr_mess ".strtolower($mess_mailu_no)."</A></div></TD></TR>";
  }//echo "<FONT SIZE='3'>$mess_msge</FONT>";
    $dt_jour = date("Y-m-d");
    $req_rdv = mysql_query("SELECT * from rendez_vous where
                            rdv_apprenant_no = $id_user and
                            rdv_date_dt = '$dt_jour'
                            ORDER BY rdv_tuteur_no,rdv_creneau_nb ASC");
    $nomb_rdv = mysql_num_rows($req_rdv);
    $date =date("d/m/Y H:i:s",time());
    list($dt,$heure) =  explode (" ",$date);
    list($hh,$mm,$ss) = explode(':',$heure);
    if ($nomb_rdv > 0){
       require 'agenda.inc.php';
       $rdv =0;
       while ($rdv < $nomb_rdv){
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
        $agenda=0;
        unset($_SESSION['acces']);
        $_SESSION['menu_prov'] = $menu_prov;
        $_SESSION['agenda'] = $agenda;
        $affiche_titre .= "<TR height='30'><TD align='left' valign='left' width='753'><div id=tut style=\"float:left;margin-left:4px; border:0;\">".
                         "<A HREF=\"agenda.php?apprenant=1&acces=annonce_grp\">".
                          "<IMG  SRC=\"images/ecran-annonce/icotutoW.gif\" BORDER='0'></A></div><div id='rdv' style=\"float:left;margin-left:8px;\">";
        $affiche_titre .= "<A HREF=\"agenda.php?apprenant=1&acces=annonce_grp\">$vous_avez $nb_final ".strtolower($mess_ag_rdv_tut)."</A></div></TD></TR>";
    }
//    $verif_connex = mysql_num_rows(mysql_query("SELECT distinct login from log where login !='$login' AND date_debut = '$date_jour' AND date_fin ='0000-00-00'"));
    $affiche_titre .= "</table><TABLE bgColor='#298CA0' cellspacing='0' cellpadding='2' width='775' ><TR><TD>".
                 "<TABLE bgColor='#FFFFFF' cellspacing='0' cellpadding='0' width='100%'><TR><TD>".
                 "<TABLE border='0' width='100%' cellspacing='1' cellpadding='3'><TR height='30'>".
                 "<TD background=\"images/fond_titre_table.jpg\" valign='top'><FONT COLOR= 'white' nowrap><B>$mess_grp_form_suiv</B></FONT></TD>".
                 "<TD background=\"images/fond_titre_table.jpg\" valign='top'><FONT COLOR= 'white'><B>$mess_acc</B></FONT></TD>".
                 "<TD background=\"images/fond_titre_table.jpg\" valign='top'><FONT COLOR= 'white'><B>$mess_alert</B></FONT></TD>".
                 "<TD background=\"images/fond_titre_table.jpg\" valign='top'><FONT COLOR= 'white'><B>$mess_gp_seq_parc ".strtolower($mess_gp_hsd)."</B></FONT></TD>".
                 "<TD background=\"images/fond_titre_table.jpg\" valign='top'><FONT COLOR= 'white'><B>$mess_seq_urgent</B></FONT></TD>".
                 "<TD background=\"images/fond_titre_table.jpg\" valign='top'><FONT COLOR= 'white'><B>$mess_grp_forum</B></FONT></TD>".
                 "<TD background=\"images/fond_titre_table.jpg\" valign='top'><FONT COLOR= 'white'><B>$mess_demarrer</B></FONT></TD>".
                 "</TR>";
$agent=getenv("HTTP_USER_AGENT");
$ip = getenv('REMOTE_ADDR');
//if (IsIPv6($ip) == TRUE)
   $serveur = gethostbyaddr($_SERVER['REMOTE_ADDR']);
//else
//   $serveur = $_SERVER['REMOTE_ADDR'];
$ChampServeur = $serveur."-".$_COOKIE['maVille']."-".$_COOKIE['maRegion']."-".$_COOKIE['monPays'];
$etat_GraphTout = GetDataField($connect,"select param_etat_lb from param_foad where param_typ_lb='GraphTout'","param_etat_lb");
$GraphTout = ($etat_GraphTout == 'OUI') ? 'graph=1&tout=1': 'graph=0&tout=0';
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
      echo "<CENTER><TABLE width='800' border='0'><TR>";
      $lien = "admin/modifiche.php?modifier=1&num=$id_user&mafiche=1";
       $lien = urlencode($lien);
      echo "<TD align='center' colspan='2' width='100%'>".
           "<TABLE cellspacing='15' border='0' width='100%'>".
           "<TR><TD><A href=\"trace.php?link=$lien\" title=\"$mess_menu_profil\" ".
           "onmouseover=\"img_fiche.src='images/fiche2b.gif';return true;\" ".
           "onmouseout=\"img_fiche.src='images/fiche2.gif'\">";
      echo "<IMG NAME=\"img_fiche\" SRC=\"images/fiche2.gif\" BORDER='0' ".
           "onLoad=\"tempImg=new Image(0,0); tempImg.src='images/fiche2b.gif'\"></A></TD>".
           "<TD valign='bottom' align='left'>";
      echo "<Font size='5'><B>$mess_bonjour, $majuscule</B></FONT><P></TD></TR></TABLE></TD></TR>";
      if ($depart == 1)
        echo "<TR><TD><CENTER><TABLE width='100%' cellpadding='20' cellspacing='20'>".
             "<TR><TD align='left' nowrap><font size='2' color='#999999'><B>$aff_date, $message</B></font></TD></TR></TABLE></TD></TR>";
      $req_msg = mysql_query("SELECT * from message where
                              ((msg_dhdeb_dt < '$madate_compare' OR msg_dhdeb_dt = '$madate_compare') AND
                              (msg_dhfin_dt = '$madate_compare' OR msg_dhfin_dt > '$madate_compare')) AND
                              msg_tous_on=1");
      $nb_msg = mysql_num_rows($req_msg);
      $g = 0;
      if ($nb_msg > 0)
      {
        echo "<TR><TD align='right' valign='top'>".
             "<IMG SRC=\"images/ecran-annonce/gus01.gif\" border = '0' title ='$mess_avertis'></TD>";
        echo "<TD valign='middle'>";
        while ($g < $nb_msg)
        {
          $id_msg = mysql_result($req_msg,$g,"msg_cdn");
          $contenus = mysql_result($req_msg,$g,"msg_contenu_cmt");
          $contenu = html_entity_decode($contenus,ENT_QUOTES,'ISO-8859-1');
          $auteur = mysql_result($req_msg,$g,"msg_auteur_no");
          echo "<div style='margin:2px;padding:2px;'><div style='padding:2px;border:1px solid #bbb;width:auto;'>$contenu</div></div>";
          $g++;
        }
        echo"</TD></TR>";
      }
      echo '</table>'.$affiche_titre;
    }
    else
    {
      $i=$res-1;
      $dates= mysql_result($der_connex,$i,"date_fin");
      $heure= mysql_result($der_connex,$i,"heure_fin");
      $ch_date = explode ("-",$dates);
      $date = "$ch_date[2]/$ch_date[1]/$ch_date[0]";
      $message="$mess_verif_der $date ($heure)";
      echo "<CENTER><TABLE border='0' width='775'><TR>";
      $lien = "admin/modifiche.php?modifier=1&num=$id_user&mafiche=1";
      $lien = urlencode($lien);
      echo "<TD align='center' colspan='2' width='100%'><TABLE cellspacing='15' border='0' width='60%'>";
      echo "<TR><TD><A href=\"trace.php?link=$lien\" title=\"$mess_menu_profil\" ".
           "onmouseover=\"img_fiche.src='images/fiche2b.gif';return true;\" ".
           "onmouseout=\"img_fiche.src='images/fiche2.gif'\">";
      echo "<IMG NAME=\"img_fiche\" SRC=\"images/fiche2.gif\" BORDER='0' ".
           "onLoad=\"tempImg=new Image(0,0); tempImg.src='images/fiche2b.gif'\"></A></TD>";
      echo "<TD valign='center'><font size=5><B>$mess_bonjour, $majuscule</B></font><P></TD></TR></TABLE></TD></TR>";
      if ($depart == 1)
        echo "<TR><TD align='left' colspan='2'><font size='2' Color='#999999'><B>$aff_date, $message</B></font><BR>&nbsp;</TD></TR>";
      $req_msg = mysql_query("SELECT * from message where
                              ((msg_dhdeb_dt < '$madate_compare' OR msg_dhdeb_dt = '$madate_compare') AND
                              (msg_dhfin_dt = '$madate_compare' OR msg_dhfin_dt > '$madate_compare')) AND
                              msg_tous_on=1");
      $nb_msg = mysql_num_rows($req_msg);
      $g = 0;
      if ($nb_msg > 0)
      {
          echo "<TR><td width='100%' style=\"background-color:#edebec;padding:2px;border:2px solid #ccc;\">".
               "<table cellspacing='0' cellpadding='3'><tr><td align='left' valign='top' width='4%'>".
               "<IMG SRC=\"images/ecran-annonce/gus01.gif\" border = '0' title ='$mess_avertis'></TD>".
               "<TD align='left' valign='middle'>";
        while ($g < $nb_msg)
        {
          $id_msg = mysql_result($req_msg,$g,"msg_cdn");
          $contenus = mysql_result($req_msg,$g,"msg_contenu_cmt");
          $contenu = html_entity_decode($contenus,ENT_QUOTES,'ISO-8859-1');
          $auteur = mysql_result($req_msg,$g,"msg_auteur_no");
          echo "<div style='margin:2px;padding:2px;'><div style='padding:2px;border:1px solid #bbb;width:auto;'>$contenu</div></div>";
          $g++;
        }
        echo"</TD></TR></TABLE></TD></TR>";
      }
      echo '</table>'.$affiche_titre;
    }//fin elseif ($res == 0){
    if ($vient_de_menu != 'menu')
       unset($_SESSION['numero_groupe']);
      $req_grp = mysql_query("select utilisateur_groupe.utilgr_groupe_no from
                              utilisateur_groupe,groupe WHERE
                              utilisateur_groupe.utilgr_utilisateur_no = $id_user AND
                              utilisateur_groupe.utilgr_groupe_no = groupe.grp_cdn
                              ORDER by groupe.grp_nom_lb");
      $nomb_grp = mysql_num_rows($req_grp);
      $i=0;
    if ($nomb_grp > 0)
    {
      while ($i < $nomb_grp)
      {
         $id_grp = mysql_result($req_grp,$i,"utilgr_groupe_no");
         $nom_grp =GetDataField ($connect,"select grp_nom_lb from groupe WHERE grp_cdn = $id_grp","grp_nom_lb");
         $desc_grp =GetDataField ($connect,"select grp_formobject_lb from groupe WHERE grp_cdn = $id_grp","grp_formobject_lb");
         $id_classe = GetDataField ($connect,"SELECT grp_classe_on from groupe where grp_cdn  = $id_grp","grp_classe_on");
         if ($id_classe == 0)
            $desc_grp .= "<BR><B>$mess_indiv_alrt</B>";
         $desc_grp =str_replace(chr(146),chr(39),$desc_grp);
         $req_presc_grp = mysql_query("SELECT COUNT(*) from prescription_$id_grp WHERE
                                       presc_utilisateur_no = $id_user AND
                                       presc_grp_no = $id_grp");
         $nb_presc_grp = mysql_result($req_presc_grp,0);
         echo couleur_tr($i+1,30);
         echo "<TD><strong>$nom_grp</strong></TD>";
         if ($nb_presc_grp > 0)
         {
             $lien = "menu_js.php?le_groupe=$id_grp&vient_de_menu=$vient_de_menu";
             $lien=urlencode($lien);
             echo  "<TD align='center'><A HREF=\"gest_frm_rappel1.php?a_faire=1&le_groupe=$id_grp&graph=1&tout=0&vient_de_grp=1\" target='main' ".
                   "onclick=\"javascript:parent.logo.location.replace('trace.php?link=$lien')\">".
                  "<img src=\"images/ecran-annonce/icohomeG.gif\" border='0'></A></TD>";
         }
         else
            echo  "<TD align='center'><img src=\"images/ecran-annonce/icohomeG.gif\" border='0'></TD>";
         $log_der = Donne_ID($connect,"SELECT max(log_cdn) from log WHERE
                                       login='$login' AND
                                       date_fin !='0000-00-00'");
         if ($log_der != 1)
         {
             $log = $log_der-1;
             $date_der = GetDataField ($connect,"select date_fin from log WHERE log_cdn = $log","date_fin");
             $heure_der = GetDataField ($connect,"select heure_fin from log WHERE log_cdn = $log","heure_fin");
             $date_compare =  $date_der." ".$heure_der;
         }
         $nb_msg = mysql_result(mysql_query("SELECT count(*) from message where
                 ((msg_dhdeb_dt < '$madate_compare' OR msg_dhdeb_dt = '$madate_compare') AND
                 (msg_dhfin_dt = '$madate_compare' OR msg_dhfin_dt > '$madate_compare')) AND
                 ((msg_apprenant_no = $id_user AND msg_groupe_no = $id_grp) OR (msg_apprenant_no = 0 AND msg_groupe_no = $id_grp))"),0);
         $haut_win = 200 + $nb_msg * 40;
         $lien = urlencode("popup_alert.php?id_grp=$id_grp&dater=$madate_compare");
         if ($nb_msg > 0)
             echo  "<TD align='center'>".
                   "<A HREF=\"trace.php?link=$lien&keepThis=true&TB_iframe=true&height=$haut_win&width=600\" class='thickbox''>$nb_msg</A></TD>";
         else
             echo  "<TD>&nbsp;</TD>";
         $nb_seq_ret = cherche_seq($id_user,$id_grp);
         $ma_liste = explode("|",$nb_seq_ret);
         $seq_depasse = $ma_liste[0];
         if ($seq_depasse > 0)
         {
            $lien = urlencode("popup_seq_out.php?id_grp=$id_grp");
            echo  "<TD align='center'><a href=\"trace.php?link=$lien&keepThis=true&TB_iframe=true&height=400&width=500\" class='thickbox'>$seq_depasse</A></TD>";
         }
         else
            echo  "<TD>&nbsp;</TD>";
         $seq_urgent = $ma_liste[1];
         if ($seq_urgent > 0)
         {
            $lien = urlencode("popup_seq_urgent.php?id_grp=$id_grp");
            echo  "<TD align='center'><a href=\"trace.php?link=$lien&keepThis=true&TB_iframe=true&height=400&width=500\" class='thickbox'>$seq_urgent</A></TD>";
         }
         else
            echo  "<TD>&nbsp;</TD>";
         $nbr_mess_forum = cherche_forum($id_grp,$date_compare,$majuscule);
         $reqGrp = mysql_query("select * from groupe where grp_cdn='$id_grp'");
         $itemGrp = mysql_fetch_object($reqGrp);
         $nom_grp = $itemGrp->grp_nom_lb;
         $reqForum = mysql_query("select * from forums where name =\"$nom_grp\"");
         $itemForum = mysql_fetch_object($reqForum);
         $id_forum = $itemForum->id;
         $comment_forum = $itemForum->name;
         $leCours =($utilisateur > 0) ? '|0|0|-|'.$id_grp : '|0|0|0|'.$id_grp;
         $course =  base64url_encode('apprenant'.$leCours);
         $lien_forum = 'forum/list.php?f='.$id_forum.'&collapse=1'.TinCanTeach ('apprenant'.$leCours,'forum/list.php?f='.$id_forum.'&collapse=1',$adresse_http.'/Forums - ');
/*
         $nom_grp = GetDataField ($connect,"select grp_nom_lb from groupe where grp_cdn  = $id_grp","grp_nom_lb");
         $id_forum = GetDataField ($connect,"select id from forums where name='$nom_grp'","id");
*/         if ($nbr_mess_forum > 0 && $nb_presc_grp > 0)
         {
/*
               $lien = "menu_js.php?le_groupe=$id_grp&vient_de_menu=$vient_de_menu&va_forum=1";
               $lien=urlencode($lien);
               echo  "<TD align='center'><A HREF=\"trace.php?link=$lien\" target='logo' ".
                     "onclick=\"javascript:parent.main.location.replace('forum/list.php?f=$id_forum&collapse=0');\">";
               echo "$nbr_mess_forum</A></TD>";
*/
               echo  "<TD align='center'><A HREF=\"$lien_forum\" target='_blank' >$nbr_mess_forum</A></TD>";
         }
         else
            echo  "<TD>&nbsp;</TD>";

         if ($nb_presc_grp > 0)
         {
             $lien = "menu_js.php?le_groupe=$id_grp&vient_de_menu=$vient_de_menu";
             $lien=urlencode($lien);
             echo "<TD align='center'><A HREF=\"gest_frm_rappel.php?le_groupe=$id_grp&continue=1&a_faire=1&".$GraphTout."\" target='main' ".
                  "onclick=\"javascript:parent.logo.location.replace('trace.php?link=$lien')\" TITLE=\"$mess_continuer\"".
                   " onmouseover=\"img$i.src='images/ecran-annonce/icoGgob.gif';return true;\" ".
                   "onmouseout=\"img$i.src='images/ecran-annonce/icoGgo.gif'\">".
                   "<IMG NAME=\"img$i\" SRC=\"images/ecran-annonce/icoGgo.gif\" BORDER='0' ".
                   "onLoad=\"tempImg=new Image(0,0); tempImg.src='images/ecran-annonce/icoGgob.gif'\"></A></TD>";
         }
         else
            echo "<TD align='center'><IMG SRC=\"images/ecran-annonce/icoGgoinactif.gif\" border='0'></TD>";
      $i++;
      }
      echo "</TR>";
    }
    else
    {
      echo $affiche_titre;
      echo  "<TR height=50><TD colspan='6' valign='center' align='center'><FONT SIZE=2><B>$mess_gp_app_nogr</B></FONT></TD></TR>";
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
        $ch_date_hier = explode ("-",$date_debut);
        if (count($ch_date_hier) == 3)
           $date_hier = "$ch_date_hier[2]/$ch_date_hier[1]/$ch_date_hier[0]";
        else
           $date_hier = $date_debut;
        $nbj_date = mysql_query ("select TO_DAYS('$date_d')");
        $nbj_date_lue = mysql_result ($nbj_date,0);
        $diff_dates = $nbj_date_comp - $nbj_date_lue;
        if ($depart == 1 && $diff_dates <= 1)
        {
          $req_trace = mysql_query("SELECT MAX(trace_cdn) from trace WHERE
                                    trace_login_lb = '$login' AND
                                    trace_lien_lb != \"annonce_grp.php?depart=\" AND
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
             if (($minutes_fin > $minutes_deb) || ($minutes_fin == $minutes_deb))
             {
               $minutes = $minutes_fin-$minutes_deb;
               $minutes_rest = $minutes;
             }
             else
             {
               $dif_heures--;
               $minutes_plus = 60-$minutes_deb+$minutes_fin;
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
           }else
             $inserer=mysql_query("UPDATE log SET
                                   date_fin ='$date_trc',heure_fin ='$time_trc',duree=1 WHERE
                                   login='$login' AND
                                   date_debut='$date_d' AND
                                   heure_debut ='$heure_d' AND
                                   date_fin ='0000-00-00'");// '
          }
        }
        if ($depart == 1 && $diff_dates > 1)
           $inserer=mysql_query("UPDATE log SET
                                 date_fin ='$date_d',heure_fin ='$heure_d',duree=1 WHERE
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
     }
         //      include 'form_deb_sans.txt';
      echo "<CENTER><TABLE cellspacing='5' border='0' width='775'>";
      $lien = "admin/modifiche.php?modifier=1&num=$id_user&mafiche=1&vient_de_menu=$vient_de_menu";
      $lien = urlencode($lien);
      echo "<TR><TD align='center' colspan='2'><TABLE cellspacing='15' border='0' width='60%'><TR>".
           "<TD align='right'><A href=\"trace.php?link=$lien\" title=\"$mess_menu_profil\" ".
           "onmouseover=\"img_fiche.src='images/fiche2b.gif';return true;\" ".
           "onmouseout=\"img_fiche.src='images/fiche2.gif'\">";
      echo "<IMG NAME=\"img_fiche\" SRC=\"images/fiche2.gif\" BORDER='0' ".
           "onLoad=\"tempImg=new Image(0,0); tempImg.src='images/fiche2b.gif'\"></A></TD><TD valign='center'>";
      if ($depart == 1)
        echo "<font size=5><B>$mess_bonjour, $majuscule</B></font><P></TD></TR></TABLE></TD></TR>";
      else
        echo "<font size=5><B>$majuscule</B></font><P></TD></TR></TABLE></TD></TR>";
      $msg_req = "SELECT * from message where
                 ((msg_dhdeb_dt < '$madate_compare' OR msg_dhdeb_dt = '$madate_compare') AND
                 (msg_dhfin_dt = '$madate_compare' OR msg_dhfin_dt > '$madate_compare')) AND
                 msg_tous_on=1";
      $req_msg = mysql_query($msg_req);
      $nb_msg = mysql_num_rows($req_msg);
      $g = 0;
      if ($nb_msg > 0)
      {
          echo "<TR><td style=\"background-color:#edebec;padding:2px;border:2px solid #ccc;width:auto;\">
                <table cellspacing='0' cellpadding='3'><tr><td align='left' valign='top' width='4%'>".
               "<IMG SRC=\"images/ecran-annonce/gus01.gif\" border = '0' title ='$mess_avertis'></TD><td valign='middle'>";
        while ($g < $nb_msg){
          $id_msg = mysql_result($req_msg,$g,"msg_cdn");
          $contenus = mysql_result($req_msg,$g,"msg_contenu_cmt");
          $contenu = html_entity_decode($contenus,ENT_QUOTES,'ISO-8859-1');
          $auteur = mysql_result($req_msg,$g,"msg_auteur_no");
          echo "<div style='margin:2px;padding:2px;'><div style='padding:2px;border:1px solid #bbb;width:auto;'>$contenu</div></div>";
          $g++;
        }
        echo"</td></tr></table></td></tr>";
      }
    if ($vient_de_menu != 'menu')
      unset($_SESSION['numero_groupe']);
      $req_grp = mysql_query("select utilisateur_groupe.utilgr_groupe_no from
                              utilisateur_groupe,groupe WHERE
                              utilisateur_groupe.utilgr_utilisateur_no = $id_user AND
                              utilisateur_groupe.utilgr_groupe_no = groupe.grp_cdn
                              ORDER by groupe.grp_nom_lb");
      $nomb_grp = mysql_num_rows($req_grp);
    if ($nomb_grp > 0)
    {
       echo '</table>'.$affiche_titre;
      $i=0;
      while ($i < $nomb_grp)
      {
         $id_grp = mysql_result($req_grp,$i,"utilgr_groupe_no");
         $nom_grp =GetDataField ($connect,"select grp_nom_lb from groupe WHERE grp_cdn = $id_grp","grp_nom_lb");
         $desc_grp =GetDataField ($connect,"select grp_formobject_lb from groupe WHERE grp_cdn = $id_grp","grp_formobject_lb");
         $id_classe = GetDataField ($connect,"SELECT grp_classe_on from groupe where grp_cdn  = $id_grp","grp_classe_on");
         if ($id_classe == 0)
            $desc_grp .= "<BR><B>$mess_indiv_alrt<B>";
         $desc_grp =str_replace(chr(146),chr(39),$desc_grp);
//         $desc1_grp =addcslashes($desc_grp,"\0..\47!@\176..\255");
         $req_presc_grp = mysql_query("SELECT COUNT(*) from prescription_$id_grp WHERE
                                       presc_utilisateur_no = $id_user AND
                                       presc_grp_no = $id_grp");
         $nb_presc_grp = mysql_result($req_presc_grp,0);
         echo couleur_tr($i+1,30);
         echo "<TD style=\"font-weight: bold; font-size=12px;\">$nom_grp</TD>";
         if ($nb_presc_grp > 0)
         {
             $lien = "menu_js.php?le_groupe=$id_grp&vient_de_menu=$vient_de_menu";
             $lien=urlencode($lien);
             echo "<TD align='center'><A HREF=\"gest_frm_rappel1.php?a_faire=1&le_groupe=$id_grp&graph=1&tout=0&vient_de_grp=1\" target='main' ".
                  "onclick=\"javascript:parent.logo.location.replace('trace.php?link=$lien')\">";
             echo
                  "<img src=\"images/ecran-annonce/icohomeG.gif\" border='0'></A></TD>";
         }
         else
           echo  "<TD align='center'><img src=\"images/ecran-annonce/icohomeG.gif\" border='0'></TD>";
         $log_der = Donne_ID($connect,"SELECT max(log_cdn) from log WHERE
                                       login='$login' AND
                                       date_fin !='0000-00-00'");
         if ($log_der != 1)
         {
            $log = $log_der-1;
            $date_der = GetDataField ($connect,"select date_fin from log WHERE log_cdn = $log","date_fin");
            $heure_der = GetDataField ($connect,"select heure_fin from log WHERE log_cdn = $log","heure_fin");
            $date_compare =  $date_der." ".$heure_der;
         }
         $nb_msg = mysql_result(mysql_query("SELECT count(*) from message where
                 ((msg_dhdeb_dt < '$madate_compare' OR msg_dhdeb_dt = '$madate_compare') AND
                 (msg_dhfin_dt = '$madate_compare' OR msg_dhfin_dt > '$madate_compare')) AND
                 ((msg_apprenant_no = $id_user AND msg_groupe_no = $id_grp) OR (msg_groupe_no = $id_grp and msg_apprenant_no = 0))"),0);
         $haut_win = 200 + $nb_msg * 40;
         $lien = urlencode("popup_alert.php?id_grp=$id_grp&dater=$madate_compare");
         if ($nb_msg > 0)
             echo "<TD align='center'><A HREF=\"trace.php?link=$lien&keepThis=true&TB_iframe=true&height=400&width=500\" class='thickbox'>$nb_msg</A></TD>";
         else
             echo  "<TD>&nbsp;</TD>";
         $nb_seq_ret = cherche_seq($id_user,$id_grp);
         $ma_liste = explode("|",$nb_seq_ret);
         $seq_depasse = $ma_liste[0];
         if ($seq_depasse > 0)
         {
            $lien = urlencode("popup_seq_out.php?id_grp=$id_grp");
            echo  "<TD align='center'><a href=\"trace.php?link=$lien&keepThis=true&TB_iframe=true&height=400&width=500\" class='thickbox'>$seq_depasse</A></TD>";
         }
         else
            echo  "<TD>&nbsp;</TD>";
         $seq_urgent = $ma_liste[1];
         if ($seq_urgent > 0)
         {
            $lien = urlencode("popup_seq_urgent.php?id_grp=$id_grp");
            echo  "<TD align='center'><a href=\"trace.php?link=$lien&keepThis=true&TB_iframe=true&height=400&width=500\" class='thickbox'>$seq_urgent</A></TD>";
         }
         else
            echo  "<TD>&nbsp;</TD>";
         $nbr_mess_forum = cherche_forum($id_grp,$date_compare,$majuscule);
         $reqGrp = mysql_query("select * from groupe where grp_cdn='$id_grp'");
         $itemGrp = mysql_fetch_object($reqGrp);
         $nom_grp = $itemGrp->grp_nom_lb;
         $reqForum = mysql_query("select * from forums where name =\"$nom_grp\"");
         $itemForum = mysql_fetch_object($reqForum);
         $id_forum = $itemForum->id;
         $comment_forum = $itemForum->name;
         $leCours =($utilisateur > 0) ? '|0|0|-|'.$id_grp : '|0|0|0|'.$id_grp;
         $course =  base64url_encode('apprenant'.$leCours);
         $lien_forum = 'forum/list.php?f='.$id_forum.'&collapse=1'.TinCanTeach ('apprenant'.$leCours,'forum/list.php?f='.$id_forum.'&collapse=1',$adresse_http.'/Forums - ');
         if ($nbr_mess_forum > 0 && $nb_presc_grp > 0)
               echo  "<TD align='center'><A HREF=\"$lien_forum\" target='_blank' >$nbr_mess_forum</A></TD>";
         else
            echo  "<TD>&nbsp;</TD>";
/*
         $nbr_mess_forum = cherche_forum($id_grp,$date_compare,$majuscule);
         $nom_grp = GetDataField ($connect,"select grp_nom_lb from groupe where grp_cdn  = $id_grp","grp_nom_lb");
         $id_forum = GetDataField ($connect,"select id from forums where name='$nom_grp'","id");
         if ($nbr_mess_forum > 0 && $nb_presc_grp > 0)
         {
               $lien = "menu_js.php?le_groupe=$id_grp&vient_de_menu=$vient_de_menu";
               $lien=urlencode($lien);
               echo  "<TD align='left'><A HREF=\"trace.php?link=$lien\" target='logo' ".
                     "onclick=\"javascript:parent.main.location.replace('forum/list.php?f=$id_forum&collapse=0')\";>";
               echo "$nbr_mess_forum</A></TD>";
         }
         else
            echo  "<TD>&nbsp;</TD>";
*/
         if ($nb_presc_grp > 0)
         {
             $lien = "menu_js.php?le_groupe=$id_grp&vient_de_menu=$vient_de_menu";
             $lien=urlencode($lien);
             echo "<TD align='center'><A HREF=\"gest_frm_rappel.php?le_groupe=$id_grp&continue=1&a_faire=1&".$GraphTout."\" target='main' ".
                  "onclick=\"javascript:parent.logo.location.replace('trace.php?link=$lien')\" TITLE=\"$mess_continuer\"".
                   " onmouseover=\"img$i.src='images/ecran-annonce/icoGgob.gif';return true;\" ".
                   "onmouseout=\"img$i.src='images/ecran-annonce/icoGgo.gif'\">".
                   "<IMG NAME=\"img$i\" SRC=\"images/ecran-annonce/icoGgo.gif\" BORDER='0' ".
                   "onLoad=\"tempImg=new Image(0,0); tempImg.src='images/ecran-annonce/icoGgob.gif'\"></A></TD>";
           }
           else
              echo "<TD align='center'><IMG SRC=\"images/ecran-annonce/icoGgoinactif.gif\" border='0'></TD>";
        $i++;
      }
      echo "</TR>";
    }
    else
    {
      echo $affiche_titre;
      echo  "<TR height=50><TD colspan='6' valign='center' align='center'><FONT SIZE=2><B>$mess_gp_app_nogr</B></FONT></TD></TR>";
    }
  }// fin du else premier
  echo "</TABLE></TD></TR></TABLE></TD></TR>";
  echo  "</TABLE>";
// Fin d'affichage

exit();
?>
