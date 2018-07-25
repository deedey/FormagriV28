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
include 'style.inc.php';
if ($utilisateur > 0)
   $id_app = $utilisateur;
else
   $id_app = $id_user;
$Ext="_$numero_groupe";
$titre = str_replace("|","'",$titre);
$affiche_sco .= entete_simple($mess_detsco);
$affiche_sco .="<TR><TD width='100%' colspan='2' ><TABLE cellspacing='1' cellpadding='5' width='100%' bgColor='#FFFFFF'>";
$act_query = mysql_query ("select * from scorm_module where mod_cdn = $scormid");
$nb_act_seq = mysql_num_rows ($act_query);
$id = mysql_result ($act_query,$i,"mod_cdn");
$nom = mysql_result ($act_query,0,"mod_titre_lb");
$description = mysql_result ($act_query,0,"mod_desc_cmt");
$motclef = mysql_result ($act_query,0,"mod_motclef_lb");
$numero = mysql_result ($act_query,0,"mod_numero_lb");
$duree = mysql_result ($act_query,0,"mod_duree_nb");
$prerequis = mysql_result ($act_query,0,"mod_prereq_lb");
$score_max = mysql_result ($act_query,0,"mod_masteryscore");
$maxtime = mysql_result ($act_query,0,"mod_maxtimeallowed");
if ($prerequis != ""){
   $prereq1 = GetDataField ($connect,"select mod_cdn from scorm_module where mod_numero_lb = \"$prerequis\"","mod_cdn");
   $titre_prereq = GetDataField ($connect,"select mod_titre_lb from scorm_module where mod_cdn = '$prereq1'","mod_titre_lb");
}
if ($maxtime != '' && strlen($maxtime) > 9){
   if (strstr($maxtime,'t') || strstr($maxtime,'T'))
      $maxtime = substr($maxtime,13);
   $liste_duree = explode(":",$maxtime);
   $maxtime = ($liste_duree[0]*60)+$liste_duree[1];
}
$affiche_sco .="<TR><TD align='left' valign='top'><b>$msq_activite</b></TD><TD align='left' valign='top'>$nom</TD></TR>";
$affiche_sco .="<TR><TD align='left' valign='top'><b>$msq_desc_seq</b></TD><TD align='left' valign='top'>".
               html_entity_decode($description,ENT_QUOTES,'iso-8859-1')."</TD></TR>";
if ($motclef != "")
   $affiche_sco .="<TR><TD align='left' valign='top'><b>$mess_motclef</b></TD><TD align='left' valign='top'>".
               html_entity_decode($motclef,ENT_QUOTES,'iso-8859-1')."</TD></TR>";
if ($duree != 0)
   $affiche_sco .="<TR><TD align='left' valign='top'><b>$msq_duree_seq</b></TD><TD align='left' valign='top'>$duree$mn</TD></TR>";
if ($maxtime != "")
   $affiche_sco .="<TR><TD align='left' valign='top'><b>$mess_dur_max</b></TD><TD align='left' valign='top'>$maxtime$mn</TD></TR>";
if ($prerequis != "")
   $affiche_sco .="<TR><TD align='left' valign='top'><b>$msq_si_prereq</b></TD><TD align='left' valign='top'>$titre_prereq</TD></TR>";
if (intval($score_max) > 0)
   $affiche_sco .="<TR><TD align='left' valign='top'><b>$mess_sco_min</b></TD><TD align='left' valign='top'>$score_max%</TD></TR>";
$affiche_sco .="</TABLE></TD></TR></TABLE></TD></TR></TABLE>";
//**********************  Suivi
if ($lg == "fr")
   setlocale(LC_TIME,'fr_FR');
elseif($lg == "ru")
   setlocale(LC_TIME,'ru_RU');
$act_query = mysql_query ("select * from scorm_util_module$Ext where
                            mod_module_no = $scormid AND
                            user_module_no = $id_app AND
                            mod_grp_no = $numero_groupe");
$nb_act_seq = mysql_num_rows ($act_query);
$date_last = mysql_result ($act_query,0,"last_acces");
$comments = mysql_result ($act_query,0,"comments");
$raw = mysql_result ($act_query,0,"raw");
$max = mysql_result ($act_query,0,"scoreMax");
$heure = substr($date_last,11);
$date = substr($date_last,0,10);
$ch_date = explode("-",$date);
$date_trq = "$ch_date[2]-$ch_date[1]-$ch_date[0]";
$dte = strftime("%A %e %B %Y", mktime(0, 0, 0, $ch_date[1],$ch_date[2],$ch_date[0]));
$duree_tot = mysql_result ($act_query,0,"total_time");
$liste_dur = explode(":",$duree_tot);
if ($liste_dur[0] == "0000" && $liste_dur[1] == "00"){
   $liste1 = explode(".",$liste_dur[2]);
   $duree_totale = intval($liste1[0])." sec";
}else{
   $duree_totale = ($liste_dur[0]*60)+$liste_dur[1];
   $duree_totale .= " $mn";
}
$duree_sess = mysql_result ($act_query,0,"session_time");
$liste_dur = explode(":",$duree_sess);
if ($liste_dur[0] == "0000" && $liste_dur[1] == "00"){
   $liste1 = explode(".",$liste_dur[2]);
   $duree_session = intval($liste1[0])." sec";
}else{
   $duree_session = ($liste_dur[0]*60)+$liste_dur[1];
   $duree_session .= " $mn";
}
$etat = mysql_result ($act_query,0,"lesson_status");
if ($etat == "COMPLETED" || $etat == "PASSED")
   $etats = $msq_valide;
elseif ($etat == "NOT ATTEMPTED" || $etat == "BROWSED")
   $etats = $mess_lanc_afaire;
elseif ($etat == "FAILED")
   $etats = $mess_echec;
elseif ($etat == "INCOMPLETE")
   $etats = $mess_no_fin;
$sql_verif_inter = "SELECT * FROM scorm_interact
                   WHERE `sci_mod_no` = '$scormid'
                   AND `sci_user_no` = '$id_app' AND sci_grp_no='$numero_groupe' ORDER BY sci_ordre_no";
$sql_verif_obj = "SELECT * FROM scorm_objectives
                   WHERE `scob_mod_no` = '$scormid'
                   AND `scob_user_no` = '$id_app' AND scob_grp_no='$numero_groupe' ORDER BY scob_ordre_no";
$query = mysql_query($sql_verif_inter);
$queryObj = mysql_query($sql_verif_obj);
$nbqcm = mysql_num_rows($query);
$nbObj = mysql_num_rows($queryObj);
if ($nbObj > 0)
{
   $afficheObj = "<P><CENTER><TABLE bgColor='#298CA0' cellspacing='2' cellpadding='0' width='98%'><TR><TD>";
   $afficheObj .= "<TABLE bgColor='#FFFFFF' cellspacing='1' cellpadding='0' width='100%'>";
   $afficheObj .= "<TR><TD background=\"images/fond_titre_table.jpg\" colspan='2' height='36' align='center'".
                  " valign='center'><Font size='3' color='#FFFFFF'><B>Atteinte des objectifs</B></FONT></TD></TR>";
   $afficheObj .= "<TR><TD width='100%' colspan='2' ><TABLE cellspacing='1' cellpadding='5' width='100%' bgColor='#FFFFFF'>";
   $afficheObj .= "<TR bgcolor='#2b677a'  height='30'>";
   $afficheObj .="<TD align='left' valign='top'><FONT COLOR=white><b>Objectif</b></FONT></TD>";
   $afficheObj .="<TD align='left' valign='top'><FONT COLOR=white><b>Taux atteint</b></FONT></TD>";
   $afficheObj .="<TD align='left' valign='top'><FONT COLOR=white><b>Bilan</b></FONT></TD>";
   $afficheObj .="<TD align='left' valign='top'><FONT COLOR=white><b>Points exigés</b></FONT></TD>";
   $afficheObj .="<TD align='left' valign='top'><FONT COLOR=white><b>Points atteints</b></FONT></TD></tr>";

   while ($itemObj = mysql_fetch_object($queryObj))
   {
      if ($itemObj->scob_max == 0)
          continue;
      if ($itemObj->scob_status != '')
          $statut = $itemObj->scob_status;
      elseif ($itemObj->scob_success != '')
          $statut = $itemObj->scob_success;
      if (strtolower($statut) == 'failed') $statut = 'Echec';
      if (strtolower($statut) == 'passed') $statut = 'Réussi';
      if (strtolower($statut) == 'completed') $statut = 'Terminé';
      if (strtolower($statut) == 'browsed') $statut = 'Visité';
      if (strtolower($statut) == 'incomplete') $statut = 'Non terminé';
      if (strtolower($statut) == 'not attempted') $statut = 'Non commencé';
      $afficheObj .= "<TR><TD align='left' valign='top'><Font size='2'>".$itemObj->scob_num_lb."</td>".
                      "<td align='left' valign='top'><IMG SRC=\"image_create.php?detail=1&text=1&nb_act=".
                                          $itemObj->scob_max."&nb_acterm=".$itemObj->scob_raw."\"> </td>".
                      "<td align='left' valign='top'>".$statut."</TD>".
                      "<td align='left' valign='top'>".$itemObj->scob_max."</TD>".
                      "<td align='left' valign='top'>".$itemObj->scob_raw."</TD></TR>";

   }
   $afficheObj .="</TABLE></TD></TR></TABLE></TD></TR></TABLE>";
}
else
   $afficheObj ='';
if ($nbqcm > 0){
  $ijk = 0;
  while ($ijk < $nbqcm){
     $le_type = mysql_result($query,$ijk,"sci_type_lb");
     if (strtolower($le_type) == "performance")
         $performance++;
     if ($le_type == "")
         $typevide++;
     $ijk ++;
  }
}

if ($nb_act_seq > 0 )
{
   //$affiche_sco .= entete_simple($mpr_suivi);
   $affiche_sco .="<CENTER><TABLE bgColor='#298CA0' cellspacing='2' cellpadding='0' width='98%'><TR><TD>";
   $affiche_sco .="<TABLE bgColor='#FFFFFF' cellspacing='1' cellpadding='0' width='100%'>";
   $affiche_sco .="<TR><TD background=\"images/fond_titre_table.jpg\" colspan='2' height='36' align='center' valign='center'><Font size='3' color='#FFFFFF'><B>$mpr_suivi</B></FONT></TD></TR>";
   $affiche_sco .="<TR><TD width='100%' colspan='2' ><TABLE cellspacing='1' cellpadding='5' width='100%' bgColor='#FFFFFF'>";
   if ($etat != "NOT ATTEMPTED"){
       $affiche_sco .="<TR><TD align='left' valign='top'><b>$mess_last_acces</b></TD><TD align='left' valign='top'>$dte à $heure</TD></TR>";
       if ($duree_sess != '0000:00:00.00')
          $affiche_sco .="<TR><TD align='left' valign='top'><b>$mess_dur_ses</b></TD><TD align='left' valign='top'>$duree_session</TD></TR>";
       if ($duree_tot != '0000:00:00.00')
          $affiche_sco .="<TR><TD align='left' valign='top'><b>$mess_dur_tot</b></TD><TD align='left' valign='top'>$duree_totale</TD></TR>";
   }

   if ($raw > 0 )
   {
       if ($max > 0)
           $score = $raw ." / ". $max;
       elseif ($max == 0 && intval($score_max) == 0 && $nbqcm == 0){
           $max = 100;
           $score = $raw ." / ". $max;
       }elseif ($max == 0 && intval($score_max) > 0 && intval($score_max) < 20 && $nbqcm > 0){
           $max = $nbqcm;
           $score = $raw ." / ". $max;
       }elseif ($max == 0 && intval($score_max) > 20 && $nbqcm > 0 && $raw < ($nbqcm +1)){
           $max = $nbqcm;
           $score = $raw ." / ". $max;
       }elseif ($max == 0 && (intval($score_max) == 0 || $score_max == "") && $nbqcm > 0){
           if ($raw > $nbqcm)
              $max = 100;
           else
              $max = $nbqcm;
           $score = $raw ." / ". $max;
       }
       elseif ($max == 0 && intval($score_max) > 0 && ($nbqcm == 0 || ($nbqcm > 0 && intval($score_max) > 20)))
       {
           $max = 100;
           $score = $raw ." / ". $max;
       }
       $affiche_sco .= "<TR><TD align='left' valign='top'><b>$mess_result</b></TD>".
                       "<TD align='left' valign='top'><IMG SRC=\"image_create.php?detail=1&nb_act=$max&nb_acterm=$raw\">";
       if (empty($performance) && empty($typevide))
          $affiche_sco .="&nbsp;&nbsp;$score";
       $affiche_sco .="</TD></TR>";
   }
   if ($comments != '')
       $affiche_sco .="<TR><TD align='left' valign='top'><b>$mess_comment_sco</b></TD><TD align='left' valign='top'>$comments</TD></TR>";
   $affiche_sco .="<TR><TD align='left' valign='top'><b>$msq_etat</b></TD><TD align='left' valign='top'>$etats</TD></TR>";
   $lien ="bilan.php?bilan_trq=1&utilisateur=$utilisateur&details=1&act=$scormid&scorm=1&numero_groupe=$numero_groupe";
   $lien = urlencode($lien);
   $affiche_sco .="<TD align='left' valign='top'>$bouton_gauche<A HREF=\"trace.php?link=$lien\">$mess_det_cons</A>$bouton_droite</TD></TR>";
   $affiche_sco .="</TABLE></TD></TR></TABLE></TD></TR></TABLE>";
}
//********************** Objectifs
 $affiche_sco .= $afficheObj;
//********************** Interactions
if ($nbqcm > 0){
   $ii = 0;$cr=0;$cd=0;$ch=0;
   while ($ii < $nbqcm)
   {
         $resultat = mysql_result($query,$ii,"sci_result_lb");
         $duree = mysql_result($query,$ii,"sci_latency_lb");
         $heure = mysql_result($query,$ii,"sci_time_lb");
         $poids = mysql_result($query,$ii,"sci_poids_nb");
         $nom_sci = mysql_result($query,$ii,"sci_num_lb");
         if ($resultat == 'correct')
            $cr++;
         if ($duree != '')
            $cd++;
         if ($heure != '')
            $ch++;
        $ii++;
   }
   $acquis = ceil($cr * 100/$nbqcm);
   $titre = $results."   ";
   $affiche_sco .= "<P><CENTER><TABLE bgColor='#298CA0' cellspacing='2' cellpadding='0' width='98%'><TR><TD>";
   $affiche_sco .= "<TABLE bgColor='#FFFFFF' cellspacing='1' cellpadding='0' width='100%'>";
   if (empty($performance) && empty($typevide))
   {
      $affiche_sco .= "<TR><TD background=\"images/fond_titre_table.jpg\" colspan='2' height='36' align='center' valign='center'>".
                      "<Font size='3' color='#FFFFFF'><B>$titre&nbsp;&nbsp;&nbsp;&nbsp;";
      if ($raw > 0 && $score_max < 20)
          $affiche_sco .= "<IMG SRC='image_create.php?detail=1&nb_act=$max&nb_acterm=$raw' />".
                          "&nbsp;&nbsp;$raw%</B></FONT></TD></TR>";
      else
          $affiche_sco .= "<IMG SRC='image_create.php?detail=1&nb_act=$nbqcm&nb_acterm=$cr' />".
                          "&nbsp;&nbsp;$acquis%</B></FONT></TD></TR>";
   }
   $affiche_sco .= "<TR><TD width='100%' colspan='2' ><TABLE cellspacing='1' cellpadding='5' width='100%' bgColor='#FFFFFF'>";
   $affiche_sco .= "<TR bgcolor='#2b677a'  height='30'>";
   if (strtolower($le_type) == 'performance'){
      $affiche_sco .="<TD align='left' valign='top'><FONT COLOR=white><b>$mess_cqcm_ins_q</b></FONT></TD>";
      $affiche_sco .="<TD align='left' valign='top'><FONT COLOR=white><b>$mess_result</b></FONT></TD>";
   }else{
      $aff_tit = (is_numeric($nom_sci)) ? $mess_qcm_quest_n : $mess_question;
      $affiche_sco .="<TD align='left' valign='top'><FONT COLOR=white><b>$aff_tit</b></FONT></TD>";
      $affiche_sco .="<TD align='left' valign='top'><FONT COLOR=white><b>$mess_repdon</b></FONT></TD>";
      $affiche_sco .="<TD align='left' valign='top'><FONT COLOR=white><b>$mess_repatt</b></FONT></TD>";
      $affiche_sco .="<TD align='left' valign='top'><FONT COLOR=white><b>$mess_result</b></FONT></TD>";
   }
   if ($duree !='')
      $affiche_sco .="<TD align='left' valign='top'><FONT COLOR=white><b>$msq_duree_seq</b></FONT></TD>";
   if ($heure !='')
      $affiche_sco .="<TD align='left' valign='top'><FONT COLOR=white><b>$heur</b></FONT></TD>";
   $i = 0;
   while ($i < $nbqcm){
       $l=$i+1;
       $id_sci = mysql_result($query,$i,"sci_cdn");
       $nom_sci = mysql_result($query,$i,"sci_num_lb");
       $reponse_donnee = mysql_result($query,$i,"sci_student_response_cmt");
       $reponse_attendue = mysql_result($query,$i,"sci_pattern_cmt");
       $leresultat = mysql_result($query,$i,"sci_result_lb");
       $heure = mysql_result($query,$i,"sci_time_lb");
       $duree = mysql_result($query,$i,"sci_latency_lb");
       if (isset($duree) && $duree != '')
       {
          if (strstr($duree,'PT'))
             $duree_cor = PTHMS($duree);//.'  '.$duree;
          else
          {
             $duree_ch = explode(':',$duree);
             $val_h = ($duree_ch[0] != '00') ? intval($duree_ch[0]).$h : '';
             $val_m = ($duree_ch[1] != '00') ? intval($duree_ch[1])."'" : '';
             $val_s = ($duree_ch[2] != '00') ? intval($duree_ch[2])."''" : '';
             $duree_cor = $val_h.$val_m.$val_s;
          }
       }
       else
          $duree_cor = "";
       $affiche_sco .="<TR><TD align='left' valign='top'>$nom_sci</TD>";
       if (strtolower($le_type) != 'performance'){
          $affiche_sco .="<TD align='left' valign='top'>$reponse_donnee</TD>";
          $affiche_sco .="<TD align='left' valign='top'>$reponse_attendue</TD>";
          $result = (strtolower($leresultat) == 'wrong' || strtolower($leresultat) == 'incorrect' ||($reponse_donnee != $reponse_attendue && $reponse_attendue != '') || ($leresultat != '' && is_int($leresultat) && $leresultat== 0)) ? 'Incorrect' : $result = (strtolower($leresultat) == 'correct' || ($reponse_donnee == $reponse_attendue && $reponse_attendue != '')) ? $qf_cr : "";
          $affiche_sco .="<TD align='left' valign='top'>$result</TD>";
       }else
          $affiche_sco .="<TD align='left' valign='top'>$leresultat</TD>";
       if ($cd > 0)
          $affiche_sco .="<TD align='left' valign='top'>$duree_cor</TD>";
       if ($ch > 0)
          $affiche_sco .="<TD align='left' valign='top'>$heure</TD>";
     $i++;
   }
   $affiche_sco .="</TR></TABLE></TD></TR></TABLE></TD></TR></TABLE>";
}
  if (strstr($SuspenData,'assmnt	:{	#	:{'))
  {
     $affiche_sco .= "<P><CENTER><TABLE bgColor='#298CA0' cellspacing='2' cellpadding='0' width='98%'><TR><TD>";
     $affiche_sco .= "<TABLE bgColor='#FFFFFF' cellspacing='1' cellpadding='0' width='100%'>";
     $affiche_sco .= "<TR><TD width='100%' colspan='2' ><TABLE cellspacing='1' cellpadding='5' width='100%' bgColor='#FFFFFF'>";
     $affiche_sco .= "<TR bgcolor='#2b677a'  height='30'>";
     $affiche_sco .="<TD align='left' valign='top'><FONT COLOR=white><b>Numéro de la question</b></FONT></TD>";
     $affiche_sco .="<TD align='left' valign='top'><FONT COLOR=white><b>$mess_result</b></FONT></TD>";
     $tab = explode(',	s	:',$SuspenData);
     $NbrS = count($tab);
     for ($i=1;$i < $NbrS; $i++)
     {
         $retab = explode (',	a	:',$tab[$i]);
         $affiche_sco .= "<TR height='30'>";
         $affiche_sco .="<TD align='left' valign='top'>$i</TD>";
         $affiche_sco .="<TD align='left' valign='top'>".ceil($retab[0]*100)."%</TD></TR>";
     }
     $affiche_sco .="</TABLE></TD></TR></TABLE></TD></TR></TABLE>";
  }
echo $affiche_sco;

?>
