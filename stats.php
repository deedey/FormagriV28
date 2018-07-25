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
$date_jour = date("Y-m-d");
$sh_date=explode ("-",$date_jour);
$annee_jour = $sh_date[0];
$letitre = (isset($_SERVER['HTTP_REFERER'])) ? $mess_stats : $mess_stats." de la plateforme \"" .
           mysql_result(mysql_query("select param_etat_lb from param_foad where param_typ_lb='adresse'"),0,"param_etat_lb")."\"";
entete_simple($mess_stats);
$nb_util = mysql_result(mysql_query("SELECT count(*) from utilisateur"),0);
$nb_app = mysql_result(mysql_query("SELECT count(*) from utilisateur where util_typutil_lb = 'APPRENANT'"),0);
$nb_tut = mysql_result(mysql_query("SELECT count(*) from utilisateur where util_typutil_lb = 'TUTEUR'"),0);
$nb_form = mysql_result(mysql_query("SELECT count(*) from utilisateur where util_typutil_lb = 'FORMATEUR_REFERENT'"),0);
$nb_resp = mysql_result(mysql_query("SELECT count(*) from utilisateur where util_typutil_lb = 'RESPONSABLE_FORMATION'"),0);
$nb_adm = mysql_result(mysql_query("SELECT count(*) from utilisateur where util_typutil_lb = 'ADMINISTRATEUR'"),0);
echo "<TR><TD bgColor='#FFFFFF'><TABLE bgColor='#FFFFFF' cellspacing='1'  cellpadding = '6' width='100%'>";
echo "<TR bgcolor='#FFFFFF' height='20'><TD rowspan='2' align='left' valign='top'><B>$mess_nbr_usr</B></TD>".
     "<TD align='left' valign='top'><B>$nb_util</B></TD></TR>";
echo "<TR bgcolor='#FFFFFF'><TD align='left' valign='top'><TABLE bgColor= '#2b677a' cellspacing='1' cellpadding = '0' width='300'>";
echo "<TR bgcolor='#FFFFFF'><TD><TABLE cellpadding='0' cellspacing='0' width='100%'><TR><TD bgcolor='#DEE3E7'>".
     "<TABLE cellspacing='1' cellpadding = '8' width='100%'>";
echo "<TR bgcolor='#2b677a' height='20'><TD align='left' valign='top'><FONT COLOR=white><B>$profil</B></FONT></TD>".
     "<TD align='left' valign='top'><FONT COLOR=white><B>$mess_nbre</B></FONT></TD></TR>";
echo "<TR bgcolor='#EFEFEF'><TD align='left' valign='top'><FONT COLOR=black><B>$mes_des_app</B></FONT></TD>".
     "<TD><B>$nb_app</B></TD></TR>";
echo "<TR bgcolor='#FFFFFF'><TD align='left' valign='top'><FONT COLOR=black><B>$mes_des_tut</B></FONT></TD>".
     "<TD><B>$nb_tut</B></TD></TR>";
echo "<TR bgcolor='#EFEFEF'><TD align='left' valign='top'><FONT COLOR=black><B>$mes_des_fr</B></FONT></TD>".
     "<TD><B>$nb_form</B></TD></TR>";
echo "<TR bgcolor = '#FFFFFF'><TD align='left' valign='top'><FONT COLOR=black><B>$mes_des_rf</B></FONT></TD>".
     "<TD><B>$nb_resp</B></TD></TR>";
echo "<TR bgcolor='#EFEFEF'><TD align='left' valign='top'><FONT COLOR=black><B>$mes_des_adm</B></FONT></TD>".
     "<TD><B>$nb_adm</B></TD></TR>";
echo "</TABLE></TD></TR></TABLE></TD></TR></TABLE></TD></TR>";
$dt_first = mysql_result(mysql_query("SELECT date_debut from log order by log_cdn"),0,"date_debut");
$ch_date = explode ("-",$dt_first);
$dt_first = "$ch_date[2]-$ch_date[1]-$ch_date[0]";
$nb_cnx = mysql_result(mysql_query("SELECT count(*) from log"),0);
echo "<TR bgcolor='#FFFFFF'><TD align='left' valign='top'><B>Ressources disque consommées</B></TD>".
     "<TD><B><a href=\"javascript:void(0);\" onclick=\"window.open('taille.php?lg=fr','','scrollbars=1,resizable=yes,width=350,height=120');\">$taille_serveur</a></B></TD></TR>";
echo "<TR bgcolor='#EFEFEF'><TD align='left' valign='top'><B>$mess_frst_cnx</B></TD>".
     "<TD><B>$dt_first</B></TD></TR>";
echo "<TR bgcolor='#FFFFFF'><TD align='left' valign='top'><B>$mess_nbr_cnx</B></TD>".
     "<TD><B>$nb_cnx</B></TD></TR>";
if ($nb_cnx > 0){
   if ($lg == "fr")
     setlocale(LC_TIME,'fr_FR');
   elseif($lg == "ru")
     setlocale(LC_TIME,'ru_RU');
   echo "<TR bgcolor='#EFEFEF'><TD align='left' valign='top'><B>$mess_der_cnx</B></TD>";
   echo "<TD align='left' valign='top'><TABLE bgcolor='#2B677A' cellpadding='1' cellspacing='0'><TR><TD><TABLE bgcolor='#DEE3E7' cellpadding='0' cellspacing='0'><TR><TD><TABLE cellpadding='8' cellspacing='1' width='300'>";
   echo "<TR bgcolor='#2b677a' height='20'><TD align='left' valign='top'><FONT COLOR=white><B>$mess_mess_date</B></FONT></TD>".
        "<TD align='left' valign='top'><FONT COLOR=white><B>$mess_nbre</B></FONT></TD></TR>";
  $i = 0;
  $nb_day = mysql_result(mysql_query("SELECT count(distinct date_debut)  from log"),0);
  if ($nb_day > 10)
    $commence = 9;
  else
    $commence = $nb_day-1;
  for ($a=$commence;$a > -1;$a--){
     $nb_day = mysql_result(mysql_query("SELECT distinct date_debut from log"),0);
     $dt_day = mysql_result(mysql_query("SELECT distinct date_debut from log order by date_debut desc"),$a,"date_debut");
     $ch_date = explode ("-",$dt_day);
     $dt_day1 = strftime("%A %e %B %Y", mktime(0, 0, 0,$ch_date[1],$ch_date[2],$ch_date[0]));
     $nb_cnx1 = mysql_result(mysql_query("SELECT count(*) from log where date_debut = '$dt_day'"),0);
     echo couleur_tr($a,'');
     $lien = "admin/connections.php?stats=1&jour=1&connection=1&dt=$dt_day&logue=$logue";
     $lien = urlencode($lien);
     echo "<td height='20' align='left'><A HREF=\"trace.php?link=$lien\"  target='main' ".
          bulle($mess_adm_date,"","RIGHT","ABOVE",200)."$dt_day1</A></td><TD><B>$nb_cnx1</B></TD></TR>";
  }
  echo "</TABLE></TD></TR></TABLE></TD></TR></TABLE></TD></TR>";
}
//Connexions
$liste=mysql_query("select * from log group by MONTH(date_debut),YEAR(date_debut) order by date_debut,heure_debut ASC");
$nbr = mysql_num_rows($liste);
$bgcolor2 = '#2B677A';
$bgcolor1 = '#EFEFEF';
if ($nbr == 0){
   echo "<TR bgcolor='#FFFFFF'><TD align='center' valign='top'><B>$mess_evol_cnx</B></TD>";
   echo "<TD><TABLE cellpadding=10><TR bgcolor='#2B677A'>";
   echo "<TD align='left'><FONT COLOR=white><b>$mess_mois</b></FONT></TD>";
   echo "<TD align='left'><FONT COLOR=white><b>$mess_admin_total</b></TD>";
   echo "</TR>";
   echo "<TD align='left'colspan='2'><FONT COLOR=white><b>$mess_mois</b></FONT></TD>";
   echo "</TD></TR></TABLE>";
}
else
{
  $i = 0;
  $ipCharts = gethostbyname("ef-dev2.educagri.fr");
  $ip = $_SERVER['REMOTE_ADDR'];
  if ($ipCharts == $ip)
  {
     echo "<TR bgcolor='#FFFFFF'><TD align='left' valign='top' colspan ='1'><B>$mess_evol_cnx</B><br/>";
     //formulaire
     echo "<form name=\"formulaire\" action=\"http://ef-dev2.educagri.fr/Administration/charts.php\" method=\"POST\" ".
              "target='_blank'title=\"Afficher l'évolution des connexions depuis l'ouverture de la plate-forme\">";
     echo "<input type='hidden' name='LaBdd' value='$bdd'>";
     echo "<input type='hidden' name='LaIp' value='$ip'>";
     echo "<input type='hidden' name='type' value='connex'>";
     echo "<input type='hidden' name='annee' value='$annee_jour'>";
     echo "<input type='hidden' name='Pdtps' value='mois'>";
     echo "<input type='image' name='submit' src='http://ef-dev2.educagri.fr/Administration/charts.jpg' border='0'>";
     echo "</form></TD>";
  }
  else
      echo "<TR bgcolor='#FFFFFF'><TD align='left' valign='top' colspan ='1'><B>$mess_evol_cnx</B></TD>";
  while ($i < $nbr)
  {
    $num = mysql_result($liste,$i,"log_cdn");
    $dtd = mysql_result($liste,$i,"date_debut");
    $dt_rech = $dtd;
      $ch_date = explode ("-",$dtd);
      $dtd = "$ch_date[2]-$ch_date[1]-$ch_date[0]";
      $le_mois = $ch_date[1];
      $annee = $ch_date[0];
      if ($i < $nbr-1)
      {
         $dtd_suivante = mysql_result($liste,$i+1,"date_debut");
         $dt_rech_suivante = $dtd_suivante;
         $ch_date_suivante = explode ("-",$dtd_suivante);
         $dtd_suivante = "$ch_date_suivante[2]-$ch_date_suivante[1]-$ch_date_suivante[0]";
         $le_mois_suivante = $ch_date_suivante[1];
         $annee_suivante = $ch_date_suivante[0];
      }
      else
         $annee_suivante = '';

      if ($annee_avant != $annee)
         $passage = 1;
      else
         $passage = 0;
      $annee_avant = $annee;
    $hrd = mysql_result($liste,$i,"heure_debut");
    $dtf = mysql_result($liste,$i,"date_fin");
      $ch_date = explode ("-",$dtf);
      $dtf = "$ch_date[2]-$ch_date[1]-$ch_date[0]";
    $hrf = mysql_result($liste,$i,"heure_fin");
    $total = mysql_result($liste,$i,"duree");
    $tot_gen = $tot_gen+$total;
    $machine = mysql_result($liste,$i,"serveur");
    $loguer = mysql_result($liste,$i,"login");
    $adrip = mysql_result($liste,$i,"ip");
    $nbr_by_mois = mysql_result(mysql_query("SELECT count(*) FROM log WHERE MONTH(date_debut) = '$le_mois' AND YEAR(date_debut) = '$annee'"),0);
    $lemois=intval($le_mois);
    $liste_nom=array();
    $liste_abc=array();
    $transite = 0;
    if ($i == 0)
    {
      echo "<TD bgcolor='#FFFFFF' valign='top'><TABLE cellspacing='0' cellpadding='0'>";
      echo "<TR><TD valign='top'><TABLE bgcolor='#FFFFFF' cellpadding='0' cellspacing='1'><TR><TD>".
           "<TABLE bgColor= '#2b677a' cellspacing='1' cellpadding = '0'><TR>";
    }
    if ($passage == 1)
    {
       $transite++;
       if ($transite == 1)
       {
           $liste_nom[$transite] = " \"\" , \"".le_mois($lemois)."\" , ";
           $liste_abc[$transite] = " \"$annee\" , $nbr_by_mois , ";
       }
       else
           $liste_abc[$transite] = " \"$annee\" , $nbr_by_mois , ";
       if ($i > 0)
       {
         echo "</TABLE></TD></TR></TABLE></TD>";
         echo "<TD bgcolor='#FFFFFF' valign='top'><TABLE cellspacing='0' cellpadding='0'>";
         echo "<TR><TD valign='top'><TABLE bgcolor='#FFFFFF' cellpadding='0' cellspacing='2'><TR><TD>".
              "<TABLE bgColor= '#2b677a' cellspacing='1' cellpadding = '0'><TR>";
       }
       echo "<TD valign='top' bgcolor='#DEE3E7'><TABLE cellspacing='1' cellpadding='8'><TR height='30'>".
            "<TD bgcolor='#2b677a' align='left'><FONT COLOR=white><b>$annee</b></FONT></TD>";
       echo "<TD bgcolor='#2b677a' align='left'><FONT COLOR=white><b>$mess_admin_total</b></TD>";
       echo "</TR>";
    }
    elseif ($passage == 0 && $transite == 1)
    {
       if ($annee_suivante != $annee || $i == $nbr-1)
       {
         $liste_nom[$transite] .= " \"".le_mois($lemois)."\" ";
         $liste_abc[$transite] .= " $nbr_by_mois ";
//         echo $liste_nom[$transite]."   ".$liste_abc[$transite];
       }else{
         $liste_nom[$transite] .= " \"".le_mois($lemois)."\" , ";
         $liste_abc[$transite] .= " $nbr_by_mois , ";
       }
    }
    elseif ($passage == 0 && $transite > 1)
    {
       if ($annee_suivante != $annee  || $i == $nbr-1)
       {
         $liste_abc[$transite] .= " $nbr_by_mois ";
       }else
         $liste_abc[$transite] .= " $nbr_by_mois , ";
    }

    echo couleur_tr($i,'');
    echo"<TD nowrap>".le_mois($lemois)."</TD>";
    $tit_mois = $mess_admin_journal_connect_deb." ".le_mois($lemois)." ".$annee;
    echo "<TD><a href=\"admin/connections.php?stats=1&annee=$annee&le_mois=$le_mois&connection=1&logue=\" ".
         bulle(NewHtmlentities($tit_mois),"","LEFT","ABOVE",150)."$nbr_by_mois</a></TD>";
    echo "</TR>";
    if ($annee_suivante != $annee)
    {
       $autre_table++;
       echo "</TABLE></TD></TR>";
    }
  $i++;
  }
  $hl = 0;
  if ($i == 1)
  {
      while ($hl < $autre_table+2)
      {
            echo "</TABLE></TD></TR>";
      $hl++;
      }
  
  }
  else
  {
      while ($hl < $autre_table+4)
      {
            echo "</TABLE></TD></TR>";
      $hl++;
      }
  }
}
// formations
$req_grp = mysql_query("SELECT grp_cdn,grp_nom_lb from groupe order by grp_nom_lb");
$nb_grp = mysql_num_rows($req_grp);
$add =  $nb_grp > 0 ?"rowspan='2'" : $add = "";
echo "<TR bgcolor='#EFEFEF'><TD $add valign='top'><B>$mess_gp_nom_grp(s)</B></TD>".
     "<TD valign='top'><B>$nb_grp&nbsp;&nbsp;".strtolower($mess_gp_nom_grp)."(s)</B></TD></TR>";
if ($nb_grp > 0)
{
   echo "<TR bgcolor='#EFEFEF'><TD>";
   echo "<TABLE bgColor= '#2b677a' cellspacing='1' cellpadding = '0' width='300'><TR bgcolor='#EFEFEF'><TD>";
   echo "<TABLE cellpadding='0' cellspacing='0' width='300'><TR><TD>".
        "<TABLE bgColor='#EFEFEF' cellspacing='1' cellpadding = '8' width='300'>";
   echo "<TR bgcolor='#2b677a' height='30'><TD align='left' valign='center'><FONT COLOR=white><B>$msq_aff_ordre</B></FONT></TD>".
        "<TD align='left' valign='center'><FONT COLOR=white><B>$mess_gp_nom_grp</B></FONT></TD>".
        "<TD align='center'><FONT COLOR=white><B>$mess_nbre</B></FONT></TD></TR>";
    $i = 0;
    while ($i < $nb_grp){
      $l = $i+1;
      $id_grp = mysql_result($req_grp,$i,"grp_cdn");
      $nom_grp = mysql_result($req_grp,$i,"grp_nom_lb");
      $nb_app_grp = mysql_result(mysql_query ("select count(utilgr_utilisateur_no) from utilisateur_groupe WHERE utilgr_groupe_no=$id_grp"),0);
      echo couleur_tr($l,'')."<TD valign='CENTER'><FONT COLOR=black><B>$l</B></FONT></TD>".
           "<TD valign='CENTER'><FONT COLOR=black><B>$nom_grp</B></FONT></TD>".
           "<TD><B>$nb_app_grp</B></TD></TR>";
    $i++;
    }
  echo "</TABLE></TD></TR></TABLE></TD></TR></TABLE></TD></TR>";
}
$req = mysql_query ("select distinct utilgr_utilisateur_no from utilisateur_groupe");
$nb_app_grp = mysql_num_rows($req);
if ($nb_app_grp > 0)
{
  $id_app = array();
  $nb_grp = array();
  $grp = array();
  $i = 0;
  $passe = 0;
  while ($i < $nb_app_grp)
  {
    $id_app[$i] = mysql_result($req,$i,"utilgr_utilisateur_no");
    $util = $id_app[$i];
    $nb_grp[$i] = mysql_num_rows(mysql_query ("select utilgr_groupe_no from utilisateur_groupe WHERE utilgr_utilisateur_no = $util"));
       $transit++;
       if ($transit == 1)
       {
          echo "<TR bgcolor='#FFFFFF'><TD rowspan='2' valign='top'><B>$mess_nbr_app $et $mess_nbr_frm</B></TD><TD>";
          echo "<TABLE bgColor= '#2b677a' cellspacing='1' cellpadding = '0'><TR bgcolor='#EFEFEF'><TD>";
          echo "<TABLE bgcolor='#2B677A' cellpadding='0' cellspacing='0'><TR bgcolor='#FFFFFF'><TD>";
          echo "<TABLE bgColor='#FFFFFF' cellspacing='1'  cellpadding = '8'>";
          echo "<TR bgcolor='#2b677a' height='30'><TD valign='Center'><FONT COLOR=white><B>$mess_nbr_app</B></FONT></TD>";
          echo "<TD valign='center'><FONT COLOR=white><B>$mess_nbr_frm</B></FONT></TD></TR>";
       }
       for ($compteur=10;$compteur > 0;$compteur--)
       {
           if ($nb_grp[$i] == $compteur && isset($grp[$compteur]))
           {
              $grp[$compteur]++;
           }
           elseif ($nb_grp[$i] == $compteur && !isset($grp[$compteur]))
              $grp[$compteur] = 1;
       }
    $i++;
  }
  for ($compteur=1;$compteur < 11;$compteur++)
  {
      if (isset($grp[$compteur]) && $grp[$compteur] > 0)
      {
         $passe++;
         echo couleur_tr($passe,'')."<TD align='center' valign='top'><B>".$grp[$compteur]."</B>";
         echo "</TD><TD align='center' valign='top'><B>$compteur</B> ";
         echo "</TD></TR>";
      }
  }
  echo "</TABLE></TD></TR></TABLE></TD></TR></TABLE></TD></TR>";
}
echo "</TABLE></TD></TR></TABLE></TD></TR></TABLE>";

?>