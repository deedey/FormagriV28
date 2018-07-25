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
include ("style.inc.php");
$requete = requete("*","groupe","groupe.grp_cdn = $n");
while ($item =  mysql_fetch_object($requete)){
       $nom_grp = $item->grp_nom_lb;
       $comment_grp = $item->grp_commentaire_cmt;
       $id_com = str_replace("'","\'",$comment_grp);
       $obj_grp = $item->grp_formobject_lb;
       $desc_grp = $item->grp_formdesc_cmt;
       $crea_grp = $item->grp_resp_no;
       $tut_grp = $item->grp_tuteur_no;
       $date_creat_grp = $item->grp_datecreation_dt;
       $date_modif_grp = $item->grp_datemodif_dt;
}
    $duree_query = requete ("sum(sequence.seq_duree_nb)","sequence,sequence_parcours,groupe_parcours",
                           "sequence.seq_cdn = sequence_parcours.seqparc_seq_no AND
                           groupe_parcours.gp_parc_no = sequence_parcours.seqparc_parc_no AND
                           groupe_parcours.gp_grp_no = $n");
    if ($duree_query)
       $duree_form = mysql_result($duree_query,0);
    if ($duree_form > 0)
       $duree_form = duree_calc($duree_form);
    else
       $duree_form = $mess_NR;
    $titre = $caract_grp.' : '.$nom_grp;
    entete_simple($titre);
    echo "<TR><TD width='100%' valign='top'><TABLE width='100%' cellpadding='2'>";
    $resp_grp_nom = GetDataField ($connect,"select util_nom_lb from utilisateur where util_cdn='$crea_grp'","util_nom_lb");
    $resp_grp_prenom = GetDataField ($connect,"select util_prenom_lb from utilisateur where util_cdn='$crea_grp'","util_prenom_lb");
    if ($tut_grp > 0){
       $obs_grp_nom = GetDataField ($connect,"select util_nom_lb from utilisateur where util_cdn='$tut_grp'","util_nom_lb");
       $obs_grp_prenom = GetDataField ($connect,"select util_prenom_lb from utilisateur where util_cdn='$tut_grp'","util_prenom_lb");
       $obs = $obs_grp_nom." ".$obs_grp_prenom;
    }
    $majuscule = $resp_grp_nom." ".$resp_grp_prenom;
    $majuscule = ucwords(strtolower($majuscule));
    $grp_parc = requete("gp_parc_no","groupe_parcours","gp_grp_no =$n");
    $liste= '';
    if ($grp_parc)
    {
          $nb_f = mysql_num_rows ($grp_parc);
          $liste ="<TABLE cellpadding='4' width='100%'><TR><TD valign='top' width='50%'><FONT COLOR=marroon><B>$mess_gp_parc_appr</B></FONT><BR>";
          $nnn = 0;
          while ($nnn < $nb_f)
          {
                $cc = $nnn+1;
                $num_parc = mysql_result($grp_parc,$nnn,"gp_parc_no");
                $nom_parcours = getdatafield ($connect,"select parcours_nom_lb from parcours where parcours_cdn = $num_parc","parcours_nom_lb");
                $nom_parc = str_replace("'","\'",$nom_parcours);
                $liste .="$cc - $nom_parc<BR>";
             $nnn++;
          }
         $liste .= "</TD>";
    }
    $requete = requete("utilgr_utilisateur_no","utilisateur_groupe","utilgr_groupe_no = $n");
    if ($requete && $grp_parc)
       $liste .=  "<TD valign='top' width='50%'><FONT COLOR=marroon><B>$mess_liste_app</B></FONT><BR>";
    elseif($requete && !$grp_parc)
       $liste .=  "<TABLE cellpadding='4' width='100%'><TR><TD valign='top' width='50%'>".
                  "<FONT COLOR=marroon><B>$mess_liste_app</B></FONT><BR>";
    if ($requete)
    {
       $nb_app = mysql_num_rows ($requete);
       while ($item =  mysql_fetch_object($requete))
       {
             $id_app = $item->utilgr_utilisateur_no;
             $nom_app = GetDataField ($connect,"select util_nom_lb from utilisateur where util_cdn='$id_app'","util_nom_lb");
             $prenom_app = GetDataField ($connect,"select util_prenom_lb from utilisateur where util_cdn='$id_app'","util_prenom_lb");
             $liste .= " - ".$nom_app." ".$prenom_app."<BR>";
       }
    }
    if (isset($liste) && $liste != '')
       $liste .= "</TD></TR></TABLE>";
    echo "<TR><TD valign='top' nowrap='nowrap'><B>$mess_gp_obj_form</B></TD><TD valign='top'>".
         html_entity_decode($obj_grp,ENT_QUOTES,'iso-8859-1')."</TD></TR>";
    echo "<TR><TD valign='top' nowrap='nowrap'><B>$mess_gp_desc_form</B></TD><TD valign='top'>".
         html_entity_decode($desc_grp,ENT_QUOTES,'iso-8859-1')."</TD></TR>";
    if ($comment_grp !='')
       echo "<TR><TD valign='top' nowrap='nowrap'><B>$mess_admin_comment</B></TD>".
            "<TD valign='top'>".html_entity_decode($comment_grp,ENT_QUOTES,'iso-8859-1')."</FONT></TD></TR>";
    echo "<TR><TD valign='top' nowrap='nowrap'><B>$mess_classe</B></TD><TD valign='top'>";
    if ($classe == 1)
       echo "$mess_non";
    else
       echo "$mess_oui";
    echo "</TD></TR>";
    echo "<TR><TD valign='top' nowrap='nowrap'><B>$msq_duree_form</B></TD><TD valign='top'>$duree_form</TD></TR>";
    echo "<TR><TD valign='top'><B>$mess_resp</B></TD><TD valign='top'>$majuscule</TD></TR>";
    echo "<TR><TD valign='top'><B>$mess_superviseur</B></TD><TD valign='top'>$obs</TD></TR>";
    $heure_creat = substr($date_creat_grp,11);
    $date_creat = reverse_date(substr($date_creat_grp,0,10),"-","-");
    $heure_modif = substr($date_modif_grp,11);
    $date_modif = reverse_date(substr($date_modif_grp,0,10),"-","-");
    echo "<TR><TD valign='top'><B>$mess_menu_creat_qcm</B></TD><TD valign='top'>$date_creat</TD></TR>";
    echo "<TR><TD valign='top'><B>$mess_modif_dt</B></TD><TD valign='top'>$date_modif</TD></TR>";
    if ($liste != '')
    {
       echo "<TR width='30'><TD valign='top' colspan='2'>&nbsp;</TD></TR>";
       echo "<TR><TD valign='top' colspan='2'>$liste</TD></TR>";
    }
    echo "</TABLE></TD></TR></TABLE></TD></TR></TABLE>";
?>