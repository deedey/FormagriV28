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
dbConnect();
include 'style.inc.php';
//NbrPost=$nbr_posts&id_forum=$id_forum&utilisateur=$id_app&id_grp=$id_grp
entete_simple($mess_seq_urgent);
$nom_grp = GetDataField ($connect,"select grp_nom_lb from groupe where grp_cdn=$id_grp","grp_nom_lb");
echo "<tr><td><table cellspacing='1' cellpadding='3' width='100%'>";
echo "<tr><td colspan='3'class='sous_titre'><strong>$mess_gp_nom_grp</strong> : $nom_grp<br />$mess_seq_urgent</td></tr>";
echo "<tr>";
echo "<td class='barre_titre'>$msq_seq</td>";
echo "<td class='barre_titre'>$mess_jours</td>";
echo "<td class='barre_titre'>$mess_formateur</td></tr>";
if ($typ_user == 'APPRENANT')
   $id_app = $id_user;
else
   $id_app = $utilisateur;
$req = mysql_query("select * from prescription_$id_grp where
                   presc_utilisateur_no = '$id_app' and
                   presc_grp_no='$id_grp'");
$nb_req = mysql_num_rows($req);
if ($nb_req > 0)
{
    $today = date("Y-n-d");
    $ii = 0;
    while ($ii < $nb_req)
    {
      $id_seq = mysql_result($req,$ii,"presc_seq_no");
      $id_form = mysql_result($req,$ii,"presc_formateur_no");
      $date_fin = mysql_result($req,$ii,"presc_datefin_dt");
      $nb_date_fin_query = mysql_query ("select TO_DAYS('$date_fin')");
      $nb_date_fin = mysql_result ($nb_date_fin_query,0);
      $nb_today_query = mysql_query ("select TO_DAYS('$today')");
      $nb_today = mysql_result ($nb_today_query,0);
      $etat_seq = GetDataField ($connect,"select suiv2_etat_lb from suivi2_$id_grp where
                                          suiv2_seq_no = $id_seq AND
                                          suiv2_utilisateur_no = $id_app AND
                                          suiv2_grp_no = $id_grp","suiv2_etat_lb");
      $nom_form = GetDataField ($connect,"select util_nom_lb from utilisateur where util_cdn='$id_form'","util_nom_lb");
      $prenom_form = GetDataField ($connect,"select util_prenom_lb from utilisateur where util_cdn='$id_form'","util_prenom_lb");
      $titre_sequence = GetDataField ($connect,"select seq_titre_lb from sequence where seq_cdn = '$id_seq'","seq_titre_lb");
      $sujet = "$titre_sequence";
      $formateur = $nom_form.$prenom_form;
      for ($nn = 1;$nn < 10;$nn++)
      {
          if ($nb_date_fin == ($nb_today+$nn) && ($etat_seq == 'A FAIRE' || $etat_seq == 'EN COURS'))
          {
                echo couleur_tr($ii,'');
                echo "<td>$sujet</td>";
                echo "<td>$nn $mess_jours</td>";
                echo "<td>$formateur</td></tr>";
          }
      }
    $ii++;
    }
}
echo fin_tableau('');
?>
