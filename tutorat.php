<?php
if (!isset($_SESSION)) session_start();
include ("include/UrlParam2PhpVar.inc.php");
if (!isset($_SESSION['id_user']) || $_SESSION['id_user'] == "")
{
  exit();
}
require 'admin.inc.php';
require 'agenda.inc.php';
require 'fonction.inc.php';
require "lang$lg.inc.php";
//include "click_droit.txt";
dbConnect();
$nom_user=GetDataField ($connect,"select util_nom_lb from utilisateur where util_login_lb='$login'","util_nom_lb");
$prenom_user=GetDataField ($connect,"select util_prenom_lb from utilisateur where util_login_lb='$login'","util_prenom_lb");
$tuteur_query = mysql_query ("select distinct utilisateur.util_cdn,utilisateur.util_nom_lb,utilisateur.util_prenom_lb from
                            utilisateur,tuteur,prescription_$numero_groupe where
                            ((utilisateur.util_typutil_lb !='apprenant' and
                            tuteur.tut_apprenant_no = $id_user and
                            tuteur.tut_tuteur_no=utilisateur.util_cdn) OR
                            (presc_utilisateur_no = $id_user and
                            presc_formateur_no=utilisateur.util_cdn))");
$Nb_Tut = mysql_num_rows ($tuteur_query);
$date_cour = date ("Y-n-d");
$ch_date= explode ("-",$date_cour);
//On echange les champs car l'annee est conservee ds $ch_date[2] ds tout le script
include ('style.inc.php');
echo "<CENTER><TABLE bgColor='#298CA0' cellspacing='2' cellpadding='0' width='98%'><TR><TD>";
echo "<TABLE bgColor='#FFFFFF' cellspacing='1' cellpadding='0' width='100%'>";
echo "<TR><TD background=\"images/fond_titre_table.jpg\" colspan='3' height='34' align='center' valign='center'>".
     "<Font size='3' color='#FFFFFF'><B>$mess_prise_rdv</B></font></TD></TR>";
echo "<TR><TD colspan='3'><TABLE cellspacing='1' cellpadding='4' width='100%'>";
echo "<TR bgColor='#2B677A' height='30'>";
echo "<TD><Font color='white' size=2><B>$msq_formateur</B></FONT></TD>";
echo "<TD><Font color='white' size=2 nowrap><B>$mess_rdv_prop_form</B></FONT></TD>";
echo "<TD><Font color='white' size=2 nowrap><B>$mess_rdv_pris_form</B></FONT></TD></TR>";
$ch_date[2]=$ch_date[0];
if ($lg == "fr")
   setlocale(LC_TIME,'fr_FR');
elseif($lg == "ru")
   setlocale(LC_TIME,'ru_RU');
$i = 0;
while ($i != $Nb_Tut){
     $nomb_pris=0;
     $nomb_lib=0;
     $id = mysql_result ($tuteur_query,$i,"util_cdn");
     $nom = mysql_result ($tuteur_query,$i,"util_nom_lb");
     $prenom = mysql_result ($tuteur_query,$i,"util_prenom_lb");
     $lien="agenda.php?tuteur=$id&Prem=0&rech=0&ch_date[2]=$ch_date[2]";
     $lien = urlencode($lien);
     $req_lib = mysql_query("SELECT rdv_cdn,rdv_date_dt,rdv_creneau_nb from
                            rendez_vous where rdv_tuteur_no = $id and rdv_util_no=0 and rdv_apprenant_no=0 and
                            rdv_grp_no=0 and rdv_date_dt > '$date_cour' order by rdv_date_dt,rdv_creneau_nb asc");
     $nomb_lib = mysql_num_rows($req_lib);
     if ($nomb_lib > 0){
        $id_photo=GetDataField ($connect,"select util_photo_lb from utilisateur WHERE util_cdn = '$id'","util_photo_lb");
        if ($id_photo != ''){
           list($w_img, $h_img, $type_img, $attr_img) = getimagesize("images/$id_photo");
           echo "<TR height='30'><TD bgcolor='#DEE3E7' valign='top'><DIV id='sequence'>".
                "<a href=\"javascript:'top.close();\" target='_blank'".
                " onMouseOver=\"overlib('', TEXTCOLOR, '#ffffff', TEXTSIZE, 2, WIDTH, $w_img, HEIGHT, $h_img, BACKGROUND,".
                " 'images/$id_photo', PADX, 60, 20, PADY, 20, 20,ol_hpos,RIGHT,ABOVE,DELAY,500)\" onMouseOut=\"nd()\"".
                " onclick=\"javascript:window.open('trace.php?link=$lien','','resizable=yes,status=no');\">$prenom $nom</a></DIV></TD>".
                "<TD bgcolor = '#EFEFEF' valign='top'>";
        }else
           echo "<TR height='30'><TD bgcolor='#DEE3E7' valign='top'><DIV id='sequence'><a href=\"javascript:top.close();\" ".
                "onclick=\"javascript:window.open('trace.php?link=$lien','','resizable=yes,status=no');\">".
                "$prenom $nom</a></DIV></TD><TD bgcolor = '#EFEFEF' valign='top'>";
        for ($nb=0; $nb<$nomb_lib; ++$nb){
            $id_rv = mysql_result ($req_lib,$nb,"rdv_cdn");
            $typ_rdv = GetDataField ($connect, "select rdv_modecontact_lb from rendez_vous where rdv_cdn=$id_rv","rdv_modecontact_lb");
            $dt = mysql_result ($req_lib,$nb,"rdv_date_dt");
            $cr = mysql_result ($req_lib,$nb,"rdv_creneau_nb");
            $ch_dt = explode("-",$dt);
            $dt = "$ch_dt[2]-$ch_dt[1]-$ch_dt[0]";
            $dte = strftime("%A %e %B %Y", mktime(0, 0, 0, $ch_dt[1],$ch_dt[2],$ch_dt[0]));
            $crn = affiche_creneau($cr,$lg);
            echo "<DIV id='sequence'><A href=\"javascript:top.close();\" ".
                 "onclick=\"javascript:window.open('agenda.php?tuteur=$id&day=$ch_dt[2]&month=$ch_dt[1]&year=$ch_dt[0]&entree_date=1&Prem=1&rech=1&direct=1&tut=$tut&apprenant=$apprenant','','resizable=yes,status=no');\" title= \"$mess_clic_rdv\">";
            echo "Le ".ucfirst($dt)." $crn $par $typ_rdv</A>";
            if (($nb+1) < $nomb_lib)
              echo "<BR>";
            else
              echo "</DIV></TD>";
        }// fin for($nb=0; $nb<$nomb_lib; ++$nb){
     }
       // fin if($nomb_lib > 0){

     $req_pris = mysql_query("SELECT rdv_cdn,rdv_date_dt,rdv_creneau_nb from rendez_vous where
                             rdv_tuteur_no = $id and rdv_apprenant_no = $id_user and
                             rdv_date_dt > '$date_cour' order by rdv_date_dt,rdv_creneau_nb asc");
     $nomb_pris = mysql_num_rows($req_pris);
     if ($nomb_pris > 0){
        if ($nomb_lib == 0){
           echo "<TR height='30'>";
           echo "<TD bgcolor='#DEE3E7' valign='top'><DIV id='sequence'><A HREF=\"javascript:top.close();\" ".
                "onclick=\"javascript:window.open('trace.php?link=$lien','','resizable=yes,status=no');\">$prenom $nom</A></DIV></TD>";
           echo "<TD bgcolor='#EFEFEF'>&nbsp;</TD>";
        }
        echo "<TD bgcolor = '#DEE3E7' valign='top'>";
        for ($nb=0; $nb<$nomb_pris; ++$nb){
            $id_rv = mysql_result ($req_pris,$nb,"rdv_cdn");
            $typ_rdv = GetDataField ($connect, "select rdv_modecontact_lb from rendez_vous where rdv_cdn=$id_rv","rdv_modecontact_lb");
            $dt = mysql_result ($req_pris,$nb,"rdv_date_dt");
            $cr = mysql_result ($req_pris,$nb,"rdv_creneau_nb");
            $ch_dt = explode("-",$dt);
            $dt = "$ch_dt[2]-$ch_dt[1]-$ch_dt[0]";
            $dte = strftime("%A %e %B %Y", mktime(0, 0, 0, $ch_dt[1],$ch_dt[2],$ch_dt[0]));
            $crn = affiche_creneau($cr,$lg);
//            echo "<DIV id='sequence'><A href=\"agenda.php?tuteur=$id&day=$ch_dt[2]&month=$ch_dt[1]&year=$ch_dt[0]&entree_date=1&num_sem=$num_sem&Prem=0&rech=1&direct=1&num_sem_cour=$num_sem_cour&tut=$tut&apprenant=$apprenant&tuteur=$id\" target='_blank' onclick=\"javascript:setTimeout('top.close()',500);\"; title= \"$mess_ag_votre_rdv $dt $par $typ_rdv $crn\">";
            echo "<Font color = '#000033'><B>";
            echo "Le ".ucfirst($dt)." $crn $par $typ_rdv</FONT></B>";
            if (($nb+1) < $nomb_pris)
              echo "<BR>";
            else
              echo "</DIV></TD></TR>";
        }
     }elseif ($nomb_pris == 0 && $nomb_lib > 0)
        echo "<TD bgcolor='#DEE3E7'>&nbsp;</TD></TR>";
  $i++;
  }
echo "</TABLE></TD></TR></TABLE></TD></TR></TABLE>";

//echo "</TABLE>";
//echo "<CENTER><input type=image src=\"images/fermer.gif\" onclick=\"javascript:window.close();\"></CENTER>";
?>
