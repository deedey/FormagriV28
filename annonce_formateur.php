<?php
if (!isset($_SESSION)) session_start();
if (!isset($_SESSION['id_user']) || $_SESSION['id_user'] == "")
{
  exit();
}
require "admin.inc.php";
require "fonction.inc.php";
include "include/UrlParam2PhpVar.inc.php";
require "fonction_html.inc.php";
require "accueil/fonction_accueil.inc.php";
require "langues/accueil.inc.php";
require "lang$lg.inc.php";
//include ("click_droit.txt");
dbConnect();
include ('include/entete_annonce_inc.php');

$ip = $_SERVER['REMOTE_ADDR'];
if (IsIPv6($ip) == TRUE)
$serveur = gethostbyaddr($_SERVER['REMOTE_ADDR']);
else
   $serveur = $_SERVER['REMOTE_ADDR'];
$ChampServeur = $serveur."-".$_COOKIE['maVille']."-".$_COOKIE['maRegion']."-".$_COOKIE['monPays'];

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
/*
echo "<pre>";
     print_r($_SESSION);
echo "</pre>";
*/
if (isset($_SESSION['typ_user']))
   unset($_SESSION['typ_user']);
if (isset($_SESSION['requete_parc']))
   unset($_SESSION['requete_parc']);
if (isset($_SESSION['requete_seq']))
   unset($_SESSION['requete_seq']);
if (isset($_SESSION['requete_act']))
   unset($_SESSION['requete_act']);
$typ_user = GetDataField ($connect,"select util_typutil_lb from utilisateur where util_cdn='$id_user'","util_typutil_lb");
$_SESSION['typ_user']=$typ_user;
if (!isset($_GET['mode_user']) && $typ_user == 'ADMINISTRATEUR')
{
   $mode_user = 'tout';
   $_SESSION['mode_user']=$mode_user;
}
if (isset($_GET['lemode_user']))
{
   $mode_user = $_GET['lemode_user'];
   $_SESSION['mode_user']=$mode_user;
}

//------------------------------------------------------------------------------------------------------------------
// Détermine la provenance de la connection IpV6 ou IpV4
// fin de provenance du connecté
//insère la date, l'heure et autres données dans un fichier log
  $date_jour=date("Y-m-d");
  $date_debut = date("Y/m/d");
  $madate_compare = date("Y-m-d");
  $nbj_date_comp =  nb_jours($madate_compare);
  $heure_debut = substr($aujourdhui,11);
  $id_max = Donne_ID ($connect,"select max(log_cdn) from log");
  $verif_connex = mysql_query("SELECT * from log WHERE login ='$login' AND date_fin ='0000-00-00'");
  $result = mysql_num_rows($verif_connex);
  if ($result == 0)
  {
    if ($depart == 1)
       $inserer=mysql_query ("INSERT INTO log (log_cdn,date_debut,heure_debut,login,serveur,ip,log_agent) VALUES
                            ($id_max,'$date_debut','$heure_debut','$login','$ChampServeur','$ip',\"$agent\")");
    $der_connex = mysql_query("SELECT date_fin,heure_fin from log WHERE login ='$login' AND
                             date_fin !='0000-00-00' order by log_cdn asc");
    $res = mysql_num_rows($der_connex);
    if ($res == 0)
    {
      $message="<BR>$mess_verif_1fois<BR>";
      $lien = "admin/modifiche.php?modifier=1&num=$id_user&mafiche=1";
      $lien = urlencode($lien);
      echo "<CENTER><TABLE width='600' border='0'><TR>";
      echo "<TD align='center' colspan='2' width='100%'><TABLE cellspacing='15' border='0' width='60%'>";
      echo "<TR><TD><A href=\"trace.php?link=$lien\" title=\"$mess_menu_profil\" ".
           "onmouseover=\"img_fiche.src='images/fiche2b.gif';return true;\" onmouseout=\"img_fiche.src='images/fiche2.gif'\">";
      echo "<IMG NAME=\"img_fiche\" SRC=\"images/fiche2.gif\" BORDER='0' onLoad=\"tempImg=new Image(0,0);
           tempImg.src='images/fiche2b.gif'\"></A></TD><TD valign='center' align='left'>";
      echo "</TD><TD><Font size='5'><B>$mess_bonjour, $majuscule</B></FONT><P>&nbsp;</TD></TR></TABLE></TD></TR>";
      if ($depart == 1)
        echo "<TR><TD><TABLE width='100%' cellpadding='20' cellspacing='20' border='0'>
             <TR><TD align='left' nowrap><font size='2' color='#999999'><B>
             $aff_date, $message</B></font></TD></TR></TABLE></TD></TR>";
    }
    else
    {
      $i=$res-1;
      $dates= mysql_result($der_connex,$i,"date_fin");
      $heure= mysql_result($der_connex,$i,"heure_fin");
      $ch_date = explode ("-",$dates);
      $date = "$ch_date[2]/$ch_date[1]/$ch_date[0]";
      $message="$mess_verif_der $date ($heure)";
      $lien = "admin/modifiche.php?modifier=1&num=$id_user&mafiche=1";
      $lien = urlencode($lien);
      echo "<CENTER><TABLE border='0' width='600'><TR>";
      echo "<TD align='center' colspan='2' width='100%'><TABLE cellspacing='0' cellpadding='4'border='0' width='100%'>";
      echo "<TR><TD align='right'><A href=\"trace.php?link=$lien\" title=\"$mess_menu_profil\" ".
           "onmouseover=\"img_fiche.src='images/fiche2b.gif';return true;\" onmouseout=\"img_fiche.src='images/fiche2.gif'\">";
      echo "<IMG NAME=\"img_fiche\" SRC=\"images/fiche2.gif\" BORDER='0' onLoad=\"tempImg=new Image(0,0); ".
            "tempImg.src='images/fiche2b.gif'\"></A></TD><TD valign='bottom' align='left'>";
      echo "<Font size='5'><B>$mess_bonjour, $majuscule</B></FONT><P></td></tr></table></td></tr>";
      if ($typ_user == 'APPRENANT')
         $typ_userb = $mess_typ_app;
      if ($typ_user == 'TUTEUR')
         $typ_userb = $mess_typ_tut;
      if ($typ_user == 'FORMATEUR_REFERENT')
         $typ_userb = $mess_typ_fr;
      if ($typ_user == 'RESPONSABLE_FORMATION')
         $typ_userb = $mess_typ_rf;
      if ($typ_user == 'ADMINISTRATEUR')
         $typ_userb = $mess_typ_adm;
      if ($depart == 1)
        echo "<TR><TD align='left' colspan='2'><font size='3'><B>$mess_insc_type ".strtolower($typ_userb).
             "</B></FONT><BR><font size='2' Color='#999999'><B>$aff_date, $message</B></font></TD></TR>";
    }
   }
   else
   {
     if ((isset($complement) &&  $complement!= 1) || !isset($complement))
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
        $date = $ch_date[2]."/".$ch_date[1]."/".$ch_date[0];
        $ch_date_hier = explode ("/",$date_debut);
        $date_hier = $ch_date_hier[2]."/".$ch_date_hier[1]."/".$ch_date_hier[0];
        $nbj_date_lue = nb_jours($date_d);
        $diff_dates = $nbj_date_comp - $nbj_date_lue;
        if (isset($depart) && $depart== 1 && $diff_dates <= 1)
        {
          if ($typ_user == "TUTEUR")
             $req_trace = mysql_query("SELECT MAX(trace_cdn) from trace WHERE
                                      trace_login_lb = '$login' AND
                                      trace_lien_lb != \"annonce_tuteur.php?depart=1&activee=1\" AND
                                      trace_date_dt = '$date_d'");
          else
             $req_trace = mysql_query("SELECT MAX(trace_cdn) from trace WHERE
                                     trace_login_lb = '$login' AND
                                     trace_lien_lb != \"annonce_formateur.php?depart=1\" AND
                                     trace_date_dt = '$date_d'");
          if ($req_trace == TRUE) $nb_trc = mysql_num_rows($req_trace);
          if (isset($nb_trc) && $nb_trc > 0)
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
             $nb_j_deb = nb_jours($date_d);
             $nb_j_fin = nb_jours($date_trc);
             if ($nb_j_deb < $nb_j_fin)
               $minutes_total = 1440 + $minutes_total;
             if ($minutes_total == 0)
               $minutes_total = 1;
              //fin calcul minutes
             $inserer=mysql_query("UPDATE log SET date_fin ='$date_trc',heure_fin ='$time_trc',duree=$minutes_total WHERE
                            login='$login' AND date_debut='$date_d' AND heure_debut ='$heure_d' AND date_fin ='0000-00-00'");
           }
           else
             $inserer=mysql_query("UPDATE log SET date_fin ='$date_trc',heure_fin ='$time_trc',duree=1 WHERE
                            login='$login' AND date_debut='$date_d' AND heure_debut ='$heure_d' AND date_fin ='0000-00-00'");
          }
        }
        if (isset($depart) &&$depart == 1 && $diff_dates > 1)
           $inserer=mysql_query("UPDATE log SET date_fin ='$date_d',heure_fin ='$heure_d',duree=1 WHERE
                                login='$login' AND date_debut='$date_d' AND heure_debut ='$heure_d' AND date_fin ='0000-00-00'");
      $i++;
      }
      $id_max = Donne_ID ($connect,"select max(log_cdn) from log");
      if (isset($depart) && $depart == 1)
         $inserer=mysql_query ("INSERT INTO log (log_cdn,date_debut,heure_debut,login,serveur,ip,log_agent) VALUES
                              ($id_max,'$date_debut','$heure_debut','$login','$ChampServeur','$ip',\"$agent\")");
     }
      echo "<CENTER><TABLE cellspacing='5' border='0' width='600'>";
      if (!isset($vient_de_menu)) $vient_de_menu='';
      $lien = "admin/modifiche.php?modifier=1&num=$id_user&mafiche=1&vient_de_menu=$vient_de_menu";
      $lien = urlencode($lien);
      echo "<TR><TD align='center' colspan='2'><TABLE cellspacing='0' cellpadding='4' border='0' width='100%'>".
           "<TR><TD align='right'><A href=\"trace.php?link=$lien\" ".
           "onmouseover=\"img_fiche.src='images/fiche2b.gif';overlib('".addslashes($mess_menu_profil).
           "',ol_hpos,RIGHT,BELOW,DELAY,'800',WIDTH,'150');return true;\"".
           " onmouseout=\"img_fiche.src='images/fiche2.gif';nd();\">";
      echo "<IMG NAME=\"img_fiche\" SRC=\"images/fiche2.gif\" BORDER='0' onLoad=\"tempImg=new Image(0,0); ".
           "tempImg.src='images/fiche2b.gif'\"></A></TD>";
      if (isset($depart) && $depart == 1)
        echo "<TD valign='center'><font size=5><B>$mess_bonjour, $majuscule</B></font><P></TD></TR></TABLE></TD></TR>";
      else
        echo "<TD valign='center' align='left'><font size=5><B>$majuscule</B></font><P></TD></TR></TABLE></TD></TR>";
      echo"<TR><TD align='center'>";
      echo "<TABLE cellspacing='0' width='100%' border='0'>";
   }
   $req_msg = mysql_query("SELECT * from message where
                          ((msg_dhdeb_dt < '$madate_compare' OR msg_dhdeb_dt = '$madate_compare') AND
                          (msg_dhfin_dt = '$madate_compare' OR msg_dhfin_dt > '$madate_compare')) AND
                          msg_tous_on = 1");
   $nb_msg = mysql_num_rows($req_msg);
   $g = 0;
   if ($nb_msg > 0)
   {
        echo "<tr><td align='left'  style=\"background-color:#edebec;padding:2px;border:2px solid #ccc;\">".
             "<table border=0><tr><td valign='left'>".
             "<IMG SRC=\"images/ecran-annonce/gus01.gif\" border='0' title ='$mess_avertis'></TD>".
             "<TD valign='middle'>";
        while ($g < $nb_msg)
        {
          $id_msg = mysql_result($req_msg,$g,"msg_cdn");
          $contenus = mysql_result($req_msg,$g,"msg_contenu_cmt");
          $contenu = html_entity_decode($contenus,ENT_QUOTES,'ISO-8859-1');
          $auteur = mysql_result($req_msg,$g,"msg_auteur_no");
          echo "<div style='margin:2px;padding:2px;'><div style='padding:2px;border:1px solid #bbb;width:auto;'>$contenu</div></div>";
          $g++;
        }
        echo"</td></tr></table></td></tr>";
   }
    $dt_jour = date("Y-m-d");
    $req_rdv = mysql_query("SELECT * from rendez_vous where
                            rdv_tuteur_no = $id_user and
                            rdv_date_dt = '$dt_jour'
                            ORDER BY rdv_tuteur_no,rdv_creneau_nb ASC");
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
        $agenda=0;
        $affiche_titre .= "<TR height='30'><TD align='left' valign='left' width='753'>".
                          "<div id=tut style=\"float:left;margin-left:4px; border:0;\">".
                         "<A HREF=\"trace.php?link=".urlencode("agenda.php?tut=1")."\">".
                          "<IMG  SRC=\"images/ecran-annonce/icotutoW.gif\" BORDER='0'></A></div>".
                          "<div id='rdv' style=\"float:left;margin-left:8px;\">";
        $affiche_titre .= "<A HREF=\"trace.php?link=".urlencode("agenda.php?tut=1")."\">".
                          "$vous_avez $nb_final ".strtolower($mess_ag_rdv_tut)."</A></div>".
                          "</TD></TR>";
        echo $affiche_titre;
    }
   $mon_nom = $majuscule;
   //include('accueil/acces_alrt.inc.php');
   /************************************** Inclusion de l'appel à la fenêtre "Quoi de neuf "***********************/
   echo "<TR height='30'>  ";
   echo "<td>";
   $mon_fichier = "accueil/events.php";
   $le_titwin = $msg_WNS;
   $id = 2;
   include("accueil/appel_js.php");
   echo aide_div('accueil',10,0,0,0);
   echo "<div><a href='appliMobile.php?&keepThis=true&TB_iframe=true&height=400&width=550' class='thickbox'
        title='Application mobile de Formagri' name='Application mobile de Formagri' >
        <div class='bouton_vert' onMouseOver=\"\$(this).removeClass();\$(this).addClass('bouton_hover');\"
        onMouseOut=\"\$(this).removeClass();\$(this).addClass('bouton_vert');\">";
   echo "Formagri mobile</div></a></div>";
   echo "</td></tr>";
   /***************************************** fin de l'inclusion *************************************************/
   echo "</TABLE></TD></TR>";
   echo"<TR><TD align='center'>";
   echo "<TABLE bgcolor='#FFFFFF' cellspacing='0' cellpadding='0' width='600' border=0><TR><TD align='center'>";
   echo "<TABLE bgColor='#298CA0' cellspacing='0' cellpadding='2' width='100%' border=0><TR><TD>";
   echo "<TABLE bgColor='#FFFFFF' cellspacing='1' cellpadding='0' width='100%' border=0><TR><TD align=left>";
   echo "<TABLE border='0' width='100%' cellspacing='0' cellpadding='4'><TR height='39'>";
   echo "<TD background=\"images/fond_titre_table.jpg\" width='85%' valign='middle' align='center' colspan='3'>".
        "<FONT COLOR= 'white' SIZE='3'><B>$mess_acc</B></FONT></TD>";
   echo "</TR>";
   if ($typ_user != "FORMATEUR_REFERENT")
   {
       if ($typ_user == "RESPONSABLE_FORMATION")
          $req_grp = mysql_query("SELECT count(*) FROM groupe where grp_resp_no = $id_user");
       else
          $req_grp = mysql_query("SELECT grp_cdn FROM groupe");
       if ($typ_user == 'ADMINISTRATEUR')
       {
         $lienA = "menu_formateur.php?lemode_user=rien&mode_user=rien";
         $lienB = "annonce_formateur.php?lemode_user=rien&mode_user=rien";
         $lienA1 = "menu_formateur.php?lemode_user=tout&mode_user=tout";
         $lienB1 = "annonce_formateur.php?lemode_user=tout&mode_user=tout";
         $affiche_bouton1 =  $mess_cumrol;
         $affiche_bouton2 =  $mess_disrol;
         $maclass1 = ($mode_user == 'tout') ? "boutacc" : "bouton_new";
         $maclass2 = ($mode_user == 'rien') ? "boutacc" : "bouton_new";
         echo "<TR bgcolor='#FFFFFF' height='40'><TD><IMG SRC='images/modules/tut_form/pticoproadmin.gif' border='0'>".
              "<TD><TABLE><TR><TD><A HREF=\"trace.php?link=$lienB1\" target='main'".
              " onclick=\"javascript:parent.logo.location.replace('trace.php?link=$lienA1');\" ".
              "class= \"$maclass1\"".bulle($mess_cumrol_txt,"","CENTER","ABOVE",250)."$affiche_bouton1</A></TD>".
              "<TD><A HREF=\"trace.php?link=$lienB\" target='main'".
              " onclick=\"javascript:parent.logo.location.replace('trace.php?link=$lienA')\" class= \"$maclass2\"".
              bulle($mess_disrol_txt,"","CENTER","ABOVE",250)."$affiche_bouton2</A>";
         echo "</td></TR></TABLE></TD>";
       }
       $lien = "menu_prescripteur.php?action=prescrire";
       $lien = urlencode($lien);
       $lien1 = "gestion_groupe.php?creation_groupe=1";
       $lien1 = urlencode($lien1);
       $lien1b = "gestion_groupe.php?cms=1&ordre_affiche=nom_groupe&vient_de_accueil=1";
       $lien1b = urlencode($lien1b);

      echo "<TR bgcolor='#F4F4F4' height='40'><TD width='30'>";
      echo "<IMG SRC='images/modules/tut_form/pticoprorespon.gif' border=0 width='22' height='22'></TD>";
      echo "<TD>";
      echo "<DIV id='sequence'><A HREF=\"trace.php?link=$lien1\" target='main'".
           " onclick=\"javascript:parent.logo.location.replace('trace.php?link=$lien')\"> $mess_creer </A>$mess_ou";
      echo "<A HREF=\"trace.php?link=$lien1b\" target='main'".
           " onclick=\"javascript:parent.logo.location.replace('trace.php?link=$lien')\"> ".strtolower($mess_gerer)." </A>".
           strtolower($mess_mes_forms);
      echo "</DIV></TD>";
      echo "<TD><A HREF=\"trace.php?link=$lien1b\" target='main'".
         " onclick=\"javascript:parent.logo.location.replace('trace.php?link=$lien')\" TITLE=\"\"".
         " onmouseover=\"img1.src='images/ecran-annonce/icoGgob.gif';return true;\" ".
         "onmouseout=\"img1.src='images/ecran-annonce/icoGgo.gif'\">".
         "<IMG NAME=\"img1\" SRC=\"images/ecran-annonce/icoGgo.gif\" BORDER='0' ".
         "onLoad=\"tempImg=new Image(0,0); tempImg.src='images/ecran-annonce/icoGgob.gif'\"></A></TD></TR>";
    }
    $lien = "menu_formateur.php?action=concevoir";
    $lien = urlencode($lien);
    $requete1_parc = "select * from parcours where parcours_cdn != 0 order by parcours_type_lb,parcours_nom_lb";
    $parc_query = mysql_query ("$requete1_parc");
    $les_modules = mysql_num_rows ($parc_query);
    $requete2_parc = "select * from parcours where parcours_cdn != 0 AND parcours_auteur_no = $id_user
                         order by parcours_type_lb,parcours_nom_lb";
    $parc_query = mysql_query ("$requete2_parc");
    $mes_modules = mysql_num_rows ($parc_query);
    if ($mes_modules > 0)
    {
       $lien_params = "prem=0&liste=0&miens_parc=1&id_ref=0&choix_ref=1&ordre_affiche=lenom";
       $lien_paramsb = "prem=1&liste=1&miens_parc=1&id_ref=0&ordre_affiche=lenom";
    }
    elseif ($mes_modules == 0 && $les_modules > 0){
       $lien_params = "prem=0&liste=1&miens_parc=0&id_ref=0&choix_ref=1&refer=2&ordre_affiche=lenom";
       $lien_paramsb = "prem=1&liste=1&miens_parc=0&id_ref=0&ordre_affiche=lenom&refer=2";
    }
    elseif ($les_modules == 0)
    {
       $lien_paramsb = "prem=1&liste=1&miens_parc=1&ordre_affiche=lenom";
       $lien_params = "prem=0&liste=0&miens_parc=1&id_ref=0&choix_ref=1&ordre_affiche=lenom";
    }
    $lien1 ="parcours.php?$lien_params";
    $lien1 = urlencode($lien1);
    $lien_envoi = str_replace ("&","|",$lien_params);
    $lien = "menu_formateur.php?action=concevoir&lien_params=$lien_envoi";
    $lien1b ="parcours.php?$lien_paramsb";
    $lien1b = urlencode($lien1b);
    echo "<TR bgcolor='#FFFFFF' height='40'><TD width='30'>".
         "<IMG SRC='images/modules/tut_form/icosequen1.gif' border='0'></TD><TD>";
    echo "<DIV id='sequence'><A HREF=\"trace.php?link=$lien1\" target='main'".
         " onclick=\"javascript:parent.logo.location.replace('trace.php?link=$lien')\"> $mess_creer </A>$mess_ou";
    echo "<A HREF=\"trace.php?link=$lien1b\" target='main'".
         " onclick=\"javascript:parent.logo.location.replace('trace.php?link=$lien')\"> ".
         strtolower($mess_gerer)." </A>".strtolower($mess_menu_mon_parc);
    echo "</DIV></TD>";
    echo "<TD><A HREF=\"trace.php?link=$lien1b\" target='main'".
         " onclick=\"javascript:parent.logo.location.replace('trace.php?link=$lien')\" TITLE=\"\"".
         " onmouseover=\"img1.src='images/ecran-annonce/icoGgob.gif';return true;\" ".
         "onmouseout=\"img1.src='images/ecran-annonce/icoGgo.gif'\">".
         "<IMG NAME=\"img1\" SRC=\"images/ecran-annonce/icoGgo.gif\" BORDER='0' ".
         "onLoad=\"tempImg=new Image(0,0); tempImg.src='images/ecran-annonce/icoGgob.gif'\"></A></TD></TR>";
    $req_miens_seq ="select * from sequence where seq_auteur_no = $id_user order by seq_titre_lb";
    $req_miens = mysql_query("$req_miens_seq");
    $nb_mes_seq = mysql_num_rows($req_miens);
    $req_seq_tout = "select * from sequence where seq_publique_on=1 OR 
                     (seq_publique_on = 0 AND seq_auteur_no = $id_user) order by seq_titre_lb";
    $lien_param_toutseq="prem=1&liste=1&refer=2&ordre_affiche=lenom";
    $req_tout = mysql_query("$req_seq_tout");
    $nb_tout =  mysql_num_rows($req_tout);
    if ($nb_mes_seq > 0)
    {
       $lien_paramseq="prem=0&liste=0&miens=1&id_ref_seq=0&choix_ref=1&ordre_affiche=lenom";
       $lien_paramseqb="prem=1&liste=1&miens=1&ordre_affiche=lenom";
    }
    elseif($nb_mes_seq == 0 && $nb_tout > 0)
    {
       $lien_paramseq="prem=0&liste=1&miens=0&id_ref_seq=0&choix_ref=1&refer=2&ordre_affiche=lenom";
       $lien_paramseqb="prem=1&liste=1&miens=0&ordre_affiche=lenom&refer=2";
    }
    elseif( $nb_tout == 0)
    {
       $lien_paramseq="prem=0&liste=0&miens=0&id_ref_seq=0&choix_ref=1&ordre_affiche=lenom";
       $lien_paramseqb="prem=1&liste=1&miens=1&ordre_affiche=lenom";
    }
    $lien_envoi_seq = str_replace ("&","|",$lien_paramseq);
    $lien = "menu_formateur.php?action=concevoir&lien_paramseq=$lien_envoi_seq";
    $lien = urlencode($lien);
    $lien1 ="sequence_entree.php?$lien_paramseq";
    $lien1b ="sequence_entree.php?$lien_paramseqb";
    $lien1b = urlencode($lien1b);
    $lien1 = urlencode($lien1);
    echo "<TR bgcolor='#F4F4F4' height='40'><TD width='30'>".
         "<IMG SRC='images/modules/tut_form/icosequen1.gif' border='0'></TD><TD>";
    echo "<DIV id='sequence'><A HREF=\"trace.php?link=$lien1\" target='main'".
         " onclick=\"javascript:parent.logo.location.replace('trace.php?link=$lien')\"> $mess_creer </A>$mess_ou";
    echo "<A HREF=\"trace.php?link=$lien1b\" target='main'".
         " onclick=\"javascript:parent.logo.location.replace('trace.php?link=$lien')\"> ".
         strtolower($mess_gerer)." </A>".strtolower($mess_menu_mes_seq);
    echo "</DIV></TD>";
    echo "<TD><A HREF=\"trace.php?link=$lien1b\" target='main'".
         " onclick=\"javascript:parent.logo.location.replace('trace.php?link=$lien')\"".
         " onmouseover=\"img1.src='images/ecran-annonce/icoGgob.gif';return true;\" ".
         "onmouseout=\"img1.src='images/ecran-annonce/icoGgo.gif'\">".
         "<IMG NAME=\"img1\" SRC=\"images/ecran-annonce/icoGgo.gif\" BORDER='0'".
         " onLoad=\"tempImg=new Image(0,0); tempImg.src='images/ecran-annonce/icoGgob.gif'\"></A></TD>";
    echo "</TR>";
    $requete_act1 ="select * from activite where act_auteur_no = $id_user and act_seq_no=0 order by act_nom_lb";
    $act_question_miens_lib = mysql_query ($requete_act1);
    $requete_act2 = "select * from activite where act_auteur_no = $id_user and act_seq_no>0 order by act_nom_lb";
    $act_question_miens = mysql_query ("$requete_act2");
    $requete_act3 = "select * from activite where act_seq_no=0 and (act_publique_on=1 or
                    (act_publique_on=0 and act_auteur_no=$id_user)) order by act_nom_lb";
    $act_question_tts_lib = mysql_query ("$requete_act3");
    $requete_act4 = "select * from activite where act_seq_no>0  and (act_publique_on=1 or
                     (act_publique_on=0 and act_auteur_no=$id_user)) order by act_nom_lb";
    $act_question_tts = mysql_query ("$requete_act4");
    $nb_act1 = mysql_num_rows ($act_question_miens_lib);
    $nb_act2 = mysql_num_rows ($act_question_miens);
    $nb_act3 = mysql_num_rows ($act_question_tts_lib);
    $nb_act4 = mysql_num_rows ($act_question_tts);
    if ($nb_act1 > 0)
    {
      $lien_act="activite_free.php?creer=1&lesseq=0&miens=1&ordre_affiche=lenom&titre_act=$mess_liste_vos_act";
      $lien_actb="activite_free.php?lesseq=0&medor=1&miens=1&ordre_affiche=lenom&titre_act=$mess_liste_vos_act";
    }
    elseif ($nb_act1 == 0 && $nb_act2 > 0)
    {
      $lien_act="activite_free.php?creer=1&miens=1&lesseq=1&ordre_affiche=lenom&titre_act=$mess_liste_vos_act_seq";
      $lien_actb="activite_free.php?&miens=1&medor=1&lesseq=1&ordre_affiche=lenom&titre_act=$mess_liste_vos_act_seq";
    }
    elseif ($nb_act1 == 0 && $nb_act2 == 0 && $nb_act3 > 0)
    {
      $lien_act="activite_free.php?creer=1&lesseq=0&ordre_affiche=lenom&titre_act=$mess_liste_act_autres";
      $lien_actb="activite_free.php?lesseq=0&medor=1&medor=1&ordre_affiche=lenom&titre_act=$mess_liste_act_autres";
    }
    elseif ($nb_act1 == 0 && $nb_act2 == 0 && $nb_act3 == 0 &&  $nb_act4 > 0)
    {
      $lien_act="activite_free.php?creer=1&miens=1&lesseq=1&ordre_affiche=lenom&titre_act=$mess_liste_act_seq_autres";
      $lien_actb="activite_free.php?miens=1&medor=1&lesseq=1&ordre_affiche=lenom&titre_act=$mess_liste_act_seq_autres";
    }
    elseif ($nb_act1 == 0 && $nb_act2 == 0 && $nb_act3 == 0 &&  $nb_act4 == 0)
    {
      $lien_act="activite_free.php?titre_act=$mess_liste_act_seq_autres";
      $lien_actb="activite_free.php?titre_act=$mess_liste_act_seq_autres";
    }
    $lien_act = urlencode($lien_act);
    $lien_actb = urlencode($lien_actb);
    echo "<TR bgcolor='#FFFFFF' height='40'><TD width='30'>".
         "<IMG SRC='images/modules/tut_form/icosequen1.gif' border='0'></TD>";
    echo "<TD><DIV id='sequence'><A HREF=\"trace.php?link=$lien_act\" target='main'".
         " onclick=\"javascript:parent.logo.location.replace('trace.php?link=$lien')\"> $mess_creer </A>$mess_ou";
    echo "<A HREF=\"trace.php?link=$lien_actb\" target='main'".
         " onclick=\"javascript:parent.logo.location.replace('trace.php?link=$lien')\"> ".
         strtolower($mess_gerer)." </A>".strtolower($mess_mes_act);
    echo "</DIV></TD>";
    echo "<TD><A HREF=\"trace.php?link=$lien_actb\" target='main'".
         " onclick=\"javascript:parent.logo.location.replace('trace.php?link=$lien')\"".
         " onmouseover=\"img1.src='images/ecran-annonce/icoGgob.gif';return true;\"".
         " onmouseout=\"img1.src='images/ecran-annonce/icoGgo.gif'\">".
         "<IMG NAME=\"img1\" SRC=\"images/ecran-annonce/icoGgo.gif\" BORDER='0'".
         " onLoad=\"tempImg=new Image(0,0); tempImg.src='images/ecran-annonce/icoGgob.gif'\"></A></TD>";
    echo "</TR>";
//---------------- fin concevoir et créer
    $reqtut = mysql_query ("select count(utilisateur.util_cdn) from tuteur,utilisateur,groupe where
                          (tuteur.tut_tuteur_no = $id_user AND tuteur.tut_apprenant_no = utilisateur.util_cdn) OR
                          (groupe.grp_tuteur_no = $id_user AND utilisateur.util_cdn=$id_user) ORDER BY utilisateur.util_nom_lb");
    $nomb_app = mysql_result($reqtut,0);
    if ($nomb_app > 0)
    {
       $lien = "menu_formateur.php?action=tutorer";
       $lien = urlencode($lien);
       $lien1 = "annonce_tuteur.php?affiche_toutapp=0&activee=1";
       $lien1 = urlencode($lien1);
       $link_tut = "<A HREF=\"trace.php?link=$lien1\" target='main'".
                   " onclick=\"javascript:parent.logo.location.replace('trace.php?link=$lien')\"";
       echo "<TR bgcolor='#F4F4F4' height='40'><TD><IMG SRC='images/modules/tut_form/pticoprotut.gif' border='0'></TD>";
       echo "<TD><TABLE><TR><TD valign='center'>".
            "<DIV id='sequence'>".$link_tut.">".$mess_suiv_app_tut."</A></DIV></TD></TR></TABLE></TD>";
       echo "<TD>".$link_tut.
         " onmouseover=\"img1.src='images/ecran-annonce/icoGgob.gif';return true;\" ".
         "onmouseout=\"img1.src='images/ecran-annonce/icoGgo.gif'\">".
         "<IMG NAME=\"img1\" SRC=\"images/ecran-annonce/icoGgo.gif\" BORDER='0' ".
         "onLoad=\"tempImg=new Image(0,0); tempImg.src='images/ecran-annonce/icoGgob.gif'\"></A></TD>";
       echo "</TR>";
    }
    else
    {
       echo "<TR bgcolor='#F4F4F4' height='40'><TD width='30'>".
            "<IMG SRC='images/modules/tut_form/pticoprotut.gif' border='0'></TD>";
       echo "<TD><DIV id='seqgris'><A HREF=\"javascript:void(0);\"".
            "onMouseOver=\"overlib('<TABLE border=0 cellspacing=2 cellpadding=6>".
            "<TR><TD>".addslashes($mess_notutor)."</TD></TR></TABLE>',".
            "ol_hpos,CENTER,'160',ABOVE,DELAY,800)\" onMouseOut=\"nd()\">$mess_suiv_app_tut</A></DIV></TD>";
       echo "<TD><IMG SRC='images/ecran-annonce/icoGgoinactif.gif' border=0'></TD>";
       echo "</TR>";
    }
    $requete_grp = mysql_query ("select * from groupe order by grp_cdn");
    $nb_grp = mysql_num_rows($requete_grp);
    if ($nb_grp > 0)
    {
        $gp=0;$nomb_app=0;
        while ($gp < $nb_grp)
        {
              $id_grp = mysql_result($requete_grp,$gp,"grp_cdn");
              $nb_presc = mysql_num_rows(mysql_query("select * from prescription_$id_grp where presc_formateur_no = $id_user"));
              $nomb_app += $nb_presc;
              $gp++;
        }
    }
    if ($nomb_app > 0)
    {
       $lien = "menu_formateur.php?action=suivre";
       $lien = urlencode($lien);
       $lien1 = "annonce_suivi.php?affiche_groupe=3";
       $lien1 = urlencode($lien1);
       $link_form = "<A HREF=\"trace.php?link=$lien1\" target='main'".
                    " onclick=\"javascript:parent.logo.location.replace('trace.php?link=$lien')\"";
       echo "<TR bgcolor='#FFFFFF' height='40'><TD width='30'>".
            "<IMG SRC='images/modules/tut_form/pticoproform.gif' border='0'></TD>";
       echo "<TD><TABLE><TR><TD valign='center'><DIV id='sequence'>".$link_form.">".
             $mess_suiv_app_form."</A></DIV></TD></TR></TABLE></TD>";
       echo "<TD>".$link_form.
            " onmouseover=\"img1.src='images/ecran-annonce/icoGgob.gif';return true;\"".
            " onmouseout=\"img1.src='images/ecran-annonce/icoGgo.gif'\">".
            "<IMG NAME=\"img1\" SRC=\"images/ecran-annonce/icoGgo.gif\" BORDER='0'".
            " onLoad=\"tempImg=new Image(0,0); tempImg.src='images/ecran-annonce/icoGgob.gif'\"></A></TD>";
       echo "</TR>";
    }
    else
    {
       echo "<TR bgcolor='#FFFFFF' height='40'>";
       echo "<TD width='30'><IMG SRC='images/modules/tut_form/pticoproform.gif' border='0'></TD>".
            "<TD><DIV id='seqgris'><A HREF=\"javascript:void(0);\"".
            "onMouseOver=\"overlib('<TABLE border=0 cellspacing=2 cellpadding=6><TR><TD>".
            addslashes($mess_noformator)."</TD></TR></TABLE>',".
            "ol_hpos,CENTER,'160',ABOVE,DELAY,800)\" onMouseOut=\"nd()\">$mess_suiv_app_form</A></DIV></TD>";
       echo "<TD><IMG SRC='images/ecran-annonce/icoGgoinactif.gif' border=0'></TD>";
       echo "</TR>";
    }
    
    if ($typ_user != "FORMATEUR_REFERENT")
    {
      if ($nb_grp > 0)
      {
          $gp=0;$nomb_presc1=0;$nomb_app=0;
          while ($gp < $nb_grp)
          {
               $id_grp = mysql_result($requete_grp,$gp,"grp_cdn");
               $nb_presc = mysql_num_rows(mysql_query("select * from prescription_$id_grp where presc_prescripteur_no = $id_user"));
               $nomb_app += $nb_presc;
               $nomb_presc1++;
               $gp++;
          }
      }
      if ($nomb_app > 0)
      {
         $lien = "menu_prescripteur.php?action=prescrire";
         $requete_grp1 = mysql_query ("select grp_cdn from groupe WHERE grp_resp_no = '$id_user' OR grp_tuteur_no = '$id_user'");
         $nomb_grp1 = mysql_num_rows($requete_grp1);
         $lien1 = "annonce_presc.php";
         $nb_grp_u = mysql_num_rows(mysql_query ("select grp_cdn from groupe"));
         if ((($nomb_grp1 < 4 || $nomb_presc1 < 4) && $typ_user != "ADMINISTRATEUR") || ($typ_user == "ADMINISTRATEUR" && $nb_grp_u < 4))
            $lien1 .= "?affiche_groupe=3";
         $lien = urlencode($lien);
         $lien1 = urlencode($lien1);
         $link_presc = "<A HREF=\"trace.php?link=$lien1\" target='main'".
                    " onclick=\"javascript:parent.logo.location.replace('trace.php?link=$lien')\"";
         echo "<TR bgcolor='#F4F4F4' height='40'><TD width='30'>".
              "<IMG SRC='images/modules/tut_form/pticoprorespon.gif' border='0'></TD>".
              "<TD><TABLE><TR><TD valign='center'>".
              "<DIV id='sequence'>".$link_presc.">".$mess_suiv_app_presc ."</A></DIV></TD></TR></TABLE></TD>";
         echo "<TD>".$link_presc.
           " onmouseover=\"img1.src='images/ecran-annonce/icoGgob.gif';return true;\"".
           " onmouseout=\"img1.src='images/ecran-annonce/icoGgo.gif'\">".
           "<IMG NAME=\"img1\" SRC=\"images/ecran-annonce/icoGgo.gif\" BORDER='0'".
           " onLoad=\"tempImg=new Image(0,0); tempImg.src='images/ecran-annonce/icoGgob.gif'\"></A></TD>";
         echo "</TR>";
      }
    }

    if ($typ_user == "ADMINISTRATEUR")
    {
         $lien = "menu_admin.php";
         $lien = urlencode($lien);
         $lien1 = "admin.php?annu=ADMINISTRATEUR";
         $lien1 = urlencode($lien1);
         $link_adm = "<A HREF=\"trace.php?link=$lien1\" target='main'".
                     " onclick=\"javascript:parent.logo.location.replace('trace.php?link=$lien')\"";
         echo "<TR bgcolor='#FFFFFF' height='40'><TD><IMG SRC='images/modules/tut_form/pticoproadmin.gif' border='0'>".
              "<TD><TABLE><TR><TD valign='center'>".
              "<DIV id='sequence'>".$link_adm.">".$mess_admin_retour ."</A></DIV></TD></TR></TABLE>";
         echo "<TD>".$link_adm.
              " onmouseover=\"img1.src='images/ecran-annonce/icoGgob.gif';return true;\"".
              " onmouseout=\"img1.src='images/ecran-annonce/icoGgo.gif'\">".
              "<IMG NAME=\"img1\" SRC=\"images/ecran-annonce/icoGgo.gif\" BORDER='0'".
              " onLoad=\"tempImg=new Image(0,0); tempImg.src='images/ecran-annonce/icoGgob.gif'\"></A></TD>";
         echo "</TR>";
    }
  echo "</TABLE></TD></TR></TABLE></TD></TR></TABLE></TD></TR></TABLE>";
?>