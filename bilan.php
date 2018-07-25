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
require 'langues/formation.inc.php';
require "lang$lg.inc.php";
//include ("click_droit.txt");
dbConnect();
include ('style.inc.php');
//de quel type est l'utilisateur (apprenant, formateur, administrateur)
$lien_retour = "http://".$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
$id= (isset($id) ) ? $id: '';
$lien = "bilan.php?bilan_trq=1&utilisateur=$utilisateur&id_act=$id";
$date_jour=date("Y/m/d");
$id_app = ($utilisateur > 0) ? $utilisateur : $id_user;
$nom_app=GetDataField ($connect,"select util_nom_lb from utilisateur where util_cdn='$id_app'","util_nom_lb");
$prenom_app=GetDataField ($connect,"select util_prenom_lb from utilisateur where util_cdn='$id_app'","util_prenom_lb");
if (isset($bilan_trq) && $bilan_trq == 1)
{
    $large_col = $details == 1 ? 4 : 3;
    if (!isset($details) || (isset($details) &&  $details != 1))
       $titre = "$mess_bilan_tit2 $de ".ucfirst(strtolower($prenom_app))." ".ucwords(strtolower($nom_app));
    else
    {
       if ($scorm == 1)
          $nom_act=GetDataField ($connect,"select mod_titre_lb from scorm_module where mod_cdn='$act'","mod_titre_lb");
       else
          $nom_act=GetDataField ($connect,"select act_nom_lb from activite where act_cdn='$act'","act_nom_lb");
       $titre = $mess_bilan_tit2."&nbsp;&nbsp;&nbsp;&nbsp;<font size=2 color=white>".ucfirst(strtolower($msq_activite))." : ".stripslashes($nom_act)."</FONT>";
    }
    echo "<CENTER><TABLE bgColor='#298CA0' cellspacing='0' cellpadding = '1' width='96%'><TR><TD>";
    echo "<TABLE bgColor='#FFFFFF' cellspacing='0' cellpadding = '0' width='100%'><TR><TD><TABLE cellspacing='1' width='100%'>";
    echo "<TR><TD background=\"images/fond_titre_table.jpg\" colspan='$large_col' height='34' align='center' valign='center'><Font size='3' color='#FFFFFF'><B>$titre</B></FONT></TD></TR>";
    echo "<tr bgcolor='#2B677A'>";
    if (!isset($details) || (isset($details) &&  $details != 1))
    {
         echo "<td height='20'><FONT COLOR=white size='2'><b>$msq_activite</b></FONT></td>";
       if ($scorm == 1)
         echo "<td height='20'><FONT COLOR=white size='2'><b>$msq_etat</b></FONT></td>";
       else
         echo "<td height='20'><FONT COLOR=white size='2'><b>$msq_note</b></FONT></td>";
         echo "<td height='20'><FONT COLOR=white size='2'><b>$msq_nbr_pass</b></FONT></td>";
    }
    else
    {
         echo "<td height='20'><FONT COLOR=white size='2'><b>".ucfirst(strtolower($mess_mess_date))." ".$mess_passage."</b></FONT></td>";
         echo "<td height='20'><FONT COLOR=white size='2'><b>$mess_gp_deb_form</b></FONT></td>";
         echo "<td height='20'><FONT COLOR=white size='2'><b>$mess_gp_fin_form</b></FONT></td>";
         echo "<td height='20'><FONT COLOR=white size='2'><b>".ucfirst(strtolower($msq_duree_seq))."</b></FONT></td>";
    }
   echo "</tr>";
   if (!isset($details) || (isset($details) && $details != 1))
   {
     for ($kj = 0;$kj <2;$kj++)
     {
       if (isset($scorm) && $scorm == 1)
          $req_traq = mysql_query("select traq_mod_no from traque,scorm_module,prescription_$numero_groupe where
                                 traque.traq_util_no = $id_app AND
                                 traque.traq_mod_no = scorm_module.mod_cdn AND
                                 (traque.traq_grp_no = $numero_groupe OR traque.traq_grp_no = -1) AND
                                 presc_seq_no = scorm_module.mod_seq_no AND
                                 presc_utilisateur_no = $id_app
                                 group by traque.traq_mod_no
                                 ORDER BY scorm_module.mod_titre_lb");
       else
          $req_traq = mysql_query("select traq_act_no from traque,activite,prescription_$numero_groupe where
                                 traque.traq_util_no = $id_app AND
                                 (traque.traq_grp_no = $numero_groupe OR traque.traq_grp_no = -1) AND
                                 traque.traq_act_no = activite.act_cdn AND
                                 presc_seq_no = activite.act_seq_no AND
                                 presc_utilisateur_no = $id_app
                                 group by traque.traq_act_no
                                 ORDER BY activite.act_nom_lb");
     $nbr_trq = mysql_num_rows($req_traq);
     $i=0;
     while ($i < $nbr_trq)
     {
       if (isset($scorm) && $scorm == 1)
       {
          $act = mysql_result($req_traq,$i,"traq_mod_no");
          $req_act = mysql_query("select count(*) from traque where
                                  traq_mod_no = $act AND
                                  (traque.traq_grp_no = $numero_groupe OR traque.traq_grp_no = -1) and
                                  traq_util_no = $id_app");
          $req_dat_traq= mysql_query("select max(traq_date_dt) from traque where
                                      traq_mod_no = $act AND
                                      (traque.traq_grp_no = $numero_groupe OR traque.traq_grp_no = -1) and
                                      traq_util_no = $id_app");
          $note_traq=GetDataField ($connect,"select lesson_status from scorm_util_module_$numero_groupe where
                                             mod_module_no =$act AND
                                             user_module_no = $id_app AND
                                             mod_grp_no = $numero_groupe","lesson_status");
       }
       else
       {
          $act = mysql_result($req_traq,$i,"traq_act_no");
          $req_act = mysql_query("select count(*) from traque where
                                  traq_act_no = $act AND
                                  (traque.traq_grp_no = $numero_groupe OR traque.traq_grp_no = -1) and
                                  traq_util_no = $id_app");
          $req_dat_traq= mysql_query("select max(traq_date_dt) from traque where
                                      traq_act_no = $act and
                                      traq_util_no = $id_app");
          $note_traq=GetDataField ($connect,"select suivi_note_nb1 from suivi1_$numero_groupe where
                                             suivi_act_no =$act AND
                                             suivi_utilisateur_no = $id_app","suivi_note_nb1");
       }
          if ($note_traq == "COMPLETED" || $note_traq == "PASSED")
             $note_traq = $mess_fait;
          elseif ($note_traq == "NOT ATTEMPTED")
             $note_traq = "";//$mess_lanc_afaire;
          elseif ($note_traq == "INCOMPLETE")
             $note_traq = $mess_lanc_encours;
          elseif ($note_traq == "FAILED")
             $note_traq = $mess_echec;//formateur sur etat et apprenant sur acq/eval
          elseif ($note_traq == "BROWSED" || $note_traq == "UNKNOWN")
             $note_traq = $mess_vu;
       if (isset($scorm) && $scorm == 1)
          $nom_parc=GetDataField ($connect,"SELECT parcours.parcours_nom_lb FROM
                                         parcours,prescription_$numero_groupe,scorm_module WHERE
                                         presc_seq_no=scorm_module.mod_seq_no AND
                                         presc_parc_no=parcours.parcours_cdn AND
                                         presc_utilisateur_no=$id_app AND
                                         scorm_module.mod_cdn='$act'","parcours.parcours_nom_lb");
       else
          $nom_parc=GetDataField ($connect,"SELECT parcours.parcours_nom_lb FROM
                                         parcours,prescription_$numero_groupe,activite WHERE
                                         presc_seq_no=activite.act_seq_no AND
                                         presc_parc_no=parcours.parcours_cdn AND
                                         presc_utilisateur_no=$id_app AND
                                         activite.act_cdn='$act'","parcours.parcours_nom_lb");

       $nbr_pass = mysql_result($req_act,0);
       $dat_traq = mysql_result($req_dat_traq,0);
       $ch_date = explode("-",$dat_traq);
       $date_trq = "$ch_date[2]-$ch_date[1]-$ch_date[0]";
       echo couleur_tr($i+1,'');
       if (isset($scorm) && $scorm == 1)
       {
          $nom_act=GetDataField ($connect,"select mod_titre_lb from scorm_module where mod_cdn='$act'","mod_titre_lb");
          $nom_seq=GetDataField ($connect,"SELECT sequence.seq_titre_lb FROM
                                           sequence,scorm_module WHERE
                                           sequence.seq_cdn = scorm_module.mod_seq_no AND
                                           scorm_module.mod_cdn='$act'","sequence.seq_titre_lb");
       }
       else
       {
          $nom_act=GetDataField ($connect,"select act_nom_lb from activite where act_cdn='$act'","act_nom_lb");
          $nom_seq=GetDataField ($connect,"SELECT sequence.seq_titre_lb FROM
                                           sequence,activite WHERE
                                           sequence.seq_cdn=activite.act_seq_no AND
                                           activite.act_cdn='$act'","sequence.seq_titre_lb");
       }
       echo "<TD align='left'><A HREF=\"#\" onMouseOver=\"overlib('<B>$msq_parc</B> : ".addslashes($nom_parc)."<BR><B>$msq_seq</B> : ".addslashes($nom_seq)."',ol_hpos,RIGHT,WIDTH,'250',CAPTION,'<center>$msq_fic_act</center>')\" onMouseOut=\"nd()\">$nom_act</A></TD>";
       echo "<TD align='left'>$note_traq</TD>";
//       echo "<TD align='middle'>$date_trq</TD>";
       $lien = "bilan.php?bilan_trq=1&utilisateur=$utilisateur&details=1&act=$act&scorm=$scorm&numero_groupe=$numero_groupe";
       $lien = urlencode($lien);
       echo "<TD align='left'><A HREF=\"trace.php?link=$lien\" title='$mess_clic_det_act'>$nbr_pass ".strtolower($mess_consult)."(s)</A></TD>";
     $i++;
     }
     $scorm = 1;
    }
  }
  elseif (isset($details) && $details == 1)
  {
     $item = ($scorm == 1) ? "traq_mod_no" : "traq_act_no";
     $req_traq = mysql_query("select * from traque where
                              traq_util_no = $id_app AND
                              (traque.traq_grp_no = $numero_groupe OR traque.traq_grp_no = -1) AND
                              $item = $act
                              ORDER BY traq_date_dt,traq_hd_dt asc");
     $nbr_trq = mysql_num_rows($req_traq);
     $i=0;
     while ($i < $nbr_trq)
     {
       $num_traq = mysql_result($req_traq,$i,"traq_cdn");
       $date_trq = mysql_result($req_traq,$i,"traq_date_dt");
       $hd = mysql_result($req_traq,$i,"traq_hd_dt");
       $hf = mysql_result($req_traq,$i,"traq_hf_dt");
       $h_fin = $hf;
       if ($hf == "00:00:00")
       {
         $ch_heure_deb = explode (":",$hd);
         $heure_deb = $ch_heure_deb[0];
         $minutes_deb = $ch_heure_deb[1];
         $sec_deb = $ch_heure_deb[2];
         $duree_act = GetDataField ($connect,"SELECT act_duree_nb FROM activite where act_cdn = $act","act_duree_nb");
         $mfin = $heure_deb*60 + $duree_act;
         $hfin = floor($mfin/60);
         $mreste = $mfin%60;
         if ($mreste == 0)
            $hfin = "$hfin:$minutes_deb:$sec_deb";
         else{
            $min_fin = $minutes_deb+$mreste;
            if ($min_fin > 60){
              $hfin = $hfin+1;
              $min_fin = $min_fin - 60;
              if ($hfin > 24)
                $hfin = $fin - 24;
            }
            $hfin = "$hfin:$min_fin:$sec_deb";
         }
        $req = mysql_query ("UPDATE traque SET traq_hf_dt = \"$hfin\" where traq_cdn = $num_traq");
       }
       $ch_date = explode("-",$date_trq);
       $date_trq = "$ch_date[2]-$ch_date[1]-$ch_date[0]";
       $hf = GetDataField ($connect,"SELECT traq_hf_dt FROM traque where traq_cdn = $num_traq","traq_hf_dt");
       $ch_heure_fin = explode (":",$hf);
       $hf = $ch_heure_fin[0];
       $minutes_fin = $ch_heure_fin[1];
       $ch_heure_deb = explode (":",$hd);
       $heure_deb = $ch_heure_deb[0];
       if ($hf < $heure_deb && $h_fin != "00:00:00")
       {
         $hf +=24;
       }
       $minutes_deb = $ch_heure_deb[1];
       $dif_heures=$hf-$heure_deb;
       if ($dif_heures == 0) {
           $minutes_plus = 0;
       }else{
           $minutes_plus = $dif_heures*60;
       }
       if (($minutes_fin > $minutes_deb) || ($minutes_fin == $minutes_deb))
       {
          $minutes = $minutes_fin-$minutes_deb;
          $minutes_rest = $minutes;
       }else
       {
          $dif_heures--;
          $minutes_plus=60-$minutes_deb+$minutes_fin;
          $minutes = $dif_heures*60;
          $minutes_rest = $minutes_plus;
       }
       $minutes_total = $minutes+$minutes_plus;
       if ($hfn == 1)
        $minutes_total = 1;
       $hfn = 0;
       $heure = floor($minutes_total/60);
       if ($heure > 0)
           $reste = $minutes_total%60;
       else
         $reste = $minutes_total;
       if ($reste == 0 && $heure > 0)
         $minutes_total = $heure."$h";
       elseif ($reste == 0 && $heure == 0)
         $minutes_total = "< 1$mn";
       else
         $minutes_total = $heure.$h.$reste.$mn;
       echo "<TR>";
       echo "<TD>$date_trq</TD><TD>$hd</TD><TD>$h_fin</TD>";
       echo "<TD>$minutes_total</TD></TR>";
     $i++;
     }
   }
   echo "<TR><TD align=left colspan='$large_col'><A HREF=\"javascript:history.back();\" onmouseover=\"img_annonce.src='images/fiche_identite/boutretourb.gif';return true;\" onmouseout=\"img_annonce.src='images/fiche_identite/boutretour.gif'\">";
   echo "<IMG NAME=\"img_annonce\" SRC=\"images/fiche_identite/boutretour.gif\" BORDER='0' onLoad=\"tempImg=new Image(0,0); tempImg.src='images/fiche_identite/boutretourb.gif'\"></A></TD></TR>";
   echo"</TABLE></TD></TR></TABLE></TD></TR></TABLE>";
  exit();
}
if (isset($bilan) && $bilan == 1)
{
    if (!isset($utilisateur) ||(isset($utilisateur) && $utilisateur == ''))
       $titre = $mess_menu_bilan;
    else
       $titre = "$mess_menu_bilan $de ".ucfirst(strtolower($prenom_app))." ".ucfirst(strtolower($nom_app));
    entete_simple($titre);
//    echo "<tr><td>".aide_div('bilan',1,0,2,2)."</td></tr>";
    echo "<tr><td colspan='2' style=\"padding-top:4px;height:8px;\"></td></tr>";
    echo "<tr><td colspan='2' class='sous_titre'>$msg_bilglb</td></tr>";
    echo "<tr><td style=\"height:3px;\"></td></tr>";
      echo "<TR bgcolor='#EFEFEF' height='30'><td><B>$mess_duree_connect </B></TD>";
    if (isset($utilisateur) && $utilisateur > 0)
      $sql = mysql_query("SELECT SUM(duree) FROM log,utilisateur WHERE
                                  utilisateur.util_cdn = $utilisateur AND
                                  utilisateur.util_login_lb = log.login");
    else
       $sql= mysql_query("SELECT SUM(duree) from log where login='$login'");
    $total = mysql_result($sql,0);
    $heur_tot=duree_calc($total);
    echo "<TD><B>$heur_tot</B></TD></TR>";
    if (isset($utilisateur) && $utilisateur > 0)
       $req = mysql_query("select date_debut from log,utilisateur where
                           log.login = utilisateur.util_login_lb AND
                           utilisateur.util_cdn = $utilisateur
                           order by log.date_debut asc");
    else
       $req = mysql_query("select date_debut from log where login = '$login' order by date_debut asc");
    echo "<tr bgcolor='#FFFFFF' height='30'><td><B>$mess_dt_prem_connex </B></TD>";
    if (mysql_num_rows($req) > 0)
    {
       $prem_dt = mysql_result($req,0,"date_debut");
       $ch_date = explode ("-",$prem_dt);
       $prem_dt = "$ch_date[2]/$ch_date[1]/$ch_date[0]";
       echo "<TD><B>$prem_dt</B></TD>";
    }
    echo "</TR>";
    if (isset($utilisateur) && $utilisateur > 0)
       $req = mysql_query("select date_debut from log,utilisateur where
                           log.login = utilisateur.util_login_lb AND
                           utilisateur.util_cdn = $utilisateur
                           order by log.date_debut desc");
    else
       $req = mysql_query("select date_debut from log where login = '$login' order by date_debut desc");
    echo "<tr bgcolor='#EFEFEF' height='30'><td><B>$mess_dt_der_connex </B></TD>";
    if (mysql_num_rows($req) > 0)
    {
       $der_dt = mysql_result($req,0,"date_debut");
       $ch_date = explode ("-",$der_dt);
       $der_dt = "$ch_date[2]/$ch_date[1]/$ch_date[0]";
       echo "<TD><B>$der_dt</B></TD>";
    }
    echo "</TR><tr><td colspan='2' style=\"padding-top:4px;height:8px;\"></td></tr>";
    echo "<tr><td style=\"height:3px;\"></td></tr>";
    echo "<tr><td colspan='2' class='sous_titre'>$msg_bilfrm</td></tr>";
    echo "<tr><td style=\"height:3px;\"></td></tr>";
    if (isset($utilisateur) && $utilisateur > 0)
    {
       $req_traq = mysql_query("select distinct traq_act_no from
                                traque,activite,prescription_$numero_groupe where
                                traque.traq_util_no = $utilisateur AND
                                (traque.traq_grp_no = $numero_groupe OR traque.traq_grp_no = -1) AND
                                traque.traq_act_no = activite.act_cdn AND
                                presc_seq_no = activite.act_seq_no AND
                                presc_utilisateur_no = $utilisateur");
       $nbr_trq = mysql_num_rows($req_traq);
       $req_traq_sco = mysql_query("select distinct traq_mod_no from
                                    traque,scorm_module,prescription_$numero_groupe where
                                    traque.traq_util_no = $utilisateur AND
                                    traque.traq_mod_no = scorm_module.mod_cdn AND
                                    (traque.traq_grp_no = $numero_groupe OR traque.traq_grp_no = -1) AND
                                    presc_seq_no = scorm_module.mod_seq_no AND
                                    presc_utilisateur_no = $utilisateur");
       $nbr_trq_sco = mysql_num_rows($req_traq_sco);
       $nbr_tot = $nbr_trq_sco + $nbr_trq;
       if ($nbr_trq > 0 || $nbr_trq_sco > 0)
       {
          echo "<TR height='30' bgcolor='#FFFFFF'><TD><B>".ucfirst($mess_bilan_tit2)."</B></TD>";
          $lien = "bilan.php?bilan_trq=1&utilisateur=$utilisateur&id=$id&numero_groupe=$numero_groupe";
          $lien = urlencode($lien);
          echo "<TD><A HREF = \"trace.php?link=$lien\" ".bulle("$mess_trq_hist $nbr_tot","","LEFT","ABOVE",220).
               $mess_menu_consult_favori."</A></TD></TR>";
       }
    }
    echo "<TR bgcolor='#EFEFEF'><TD height='20' align='left'>";
    $lien = "bilan.php?regroupement=1&utilisateur=$utilisateur&numero_groupe=$numero_groupe";
    $lien = urlencode($lien);
    if ($typ_user == "APPRENANT" || (isset($utilisateur) && $utilisateur > 0))
    {
       if (isset($utilisateur) && $utilisateur > 0)
         $group = mysql_query("select utilgr_groupe_no from utilisateur_groupe where
                               utilgr_utilisateur_no = $utilisateur and
                               utilgr_groupe_no=$numero_groupe");
       elseif((!isset($utilisateur) || (isset($utilisateur) && $utilisateur =''))  && $numero_groupe > 0)
         $group = mysql_query("select utilgr_groupe_no from utilisateur_groupe where
                               utilgr_utilisateur_no = $id_user AND
                               utilgr_groupe_no=$numero_groupe");
       elseif((!isset($utilisateur) || (isset($utilisateur) && $utilisateur =''))  &&
              (!isset($numero_groupe) || (isset($numero_groupe) &&  $numero_groupe= '')))
         $group = mysql_query("select utilgr_groupe_no from utilisateur_groupe where utilgr_utilisateur_no = $id_user");
       $nbr_grp = mysql_num_rows($group);
       if ($nbr_grp > 0){
          if ($utilisateur)
            echo  "<strong>$mess_bilan_rgt</strong>";
          else
            echo "<strong>$msq_tit_regr_app</strong>";
          echo "</TD>";
          echo "<TD align='left'><DIV class='sequence'><A HREF=\"#\" onclick=\"open('trace.php?link=$lien','window','scrollbars,resizable=yes,width=550,height=500')\">";
          echo "$mess_menu_consult_favori</A></DIV></TD></TR>";
       }
       $titre = $mess_bilan_tut_tut;
       $lien = "bilan.php?agenda=1&obj=tut&utilisateur=$utilisateur";
       $lien = urlencode($lien);
       if (isset($utilisateur) && $utilisateur > 0)
          $rdv_query = mysql_query ("select * from rendez_vous where
                                     rdv_util_no=$utilisateur or
                                     rdv_apprenant_no=$utilisateur");
       else
          $rdv_query = mysql_query ("select * from rendez_vous where
                                     rdv_util_no=$id_user or
                                     rdv_apprenant_no=$id_user");
       $Nb_rdv = mysql_num_rows ($rdv_query);
       if ($Nb_rdv > 0)
       {
         echo"<TR bgcolor='#FFFFFF'><TD height='20' align='left'><strong>";
         if ($utilisateur)
           echo ucfirst(strtolower(substr($mess_bilan_rdv,4)))."</strong>";
         else
           echo "$mess_ag_rdv_tut</strong>";
         echo "</TD>";
         echo "<TD align='left'><A HREF=\"#\" onclick=\"open('trace.php?link=$lien','window','scrollbars,resizable=yes,width=550,height=500')\">$mess_menu_consult_favori</A></TD></TR>";
       }
    }
    elseif ($typ_user != "APPRENANT" && (!isset($utilisateur) || (isset($utilisateur) && $utilisateur = '')))
    {
       echo"<DIV id='sequence'><A HREF=\"#\" onclick=\"open('trace.php?link=$lien','window','scrollbars,resizable=yes,width=550,height=600')\">$mess_bilan_rgt</A></DIV></TD></TR>";
       $lien = "bilan.php?agenda=1&obj=tut";
       $lien = urlencode($lien);
       echo"<TR><TD><DIV id='sequence'><A HREF=\"#\" onclick=\"open('trace.php?link=$lien','window','scrollbars,resizable=yes,width=550,height=600')\">";
       echo "$mess_bilan_rdv</A></DIV></TD></TR>";
    }
    echo "</TD></TR></TABLE></TD></TR></TABLE>";
    echo"</CENTER>";
exit;
}
if (isset($regroupement) && $regroupement == 1)
{
  require 'agenda.inc.php';
  if (isset($utilisateur) && $utilisateur > 0)
    $req = mysql_query("select utilgr_groupe_no from utilisateur_groupe where
                        utilgr_utilisateur_no = $utilisateur AND
                        utilgr_groupe_no=$numero_groupe");
  if ($typ_user == "APPRENANT")
    $req = mysql_query("select utilgr_groupe_no from utilisateur_groupe where
                        utilgr_utilisateur_no = $id_user AND
                        utilgr_groupe_no=$numero_groupe");
  elseif ($typ_user == "TUTEUR" && (!isset($utilisateur) || (isset($utilisateur) && $utilisateur = '')))
    $req = mysql_query("select distinct utilisateur_groupe.utilgr_groupe_no from
                        utilisateur_groupe,tuteur where
                        tuteur.tut_tuteur_no= $id_user AND
                        tuteur.tut_apprenant_no = utilisateur_groupe.utilgr_utilisateur_no");
  elseif (($typ_user == "RESPONSABLE_FORMATION" || $typ_user == "FORMATEUR_REFERENT") &&
        (!isset($utilisateur) || (isset($utilisateur) && $utilisateur = '')))
  {
    $req = mysql_query("SELECT utilisateur_groupe.utilgr_groupe_no from
                        prescription_$numero_groupe,utilisateur_groupe,groupe where
                        groupe.grp_resp_no = $id_user OR
                        ((presc_prescripteur_no = $id_user OR
                        presc_formateur_no= $id_user) AND
                        presc_utilisateur_no = utilisateur_groupe.utilgr_utilisateur_no)
                        GROUP BY utilisateur_groupe.utilgr_groupe_no");
  }
  elseif ($typ_user == "ADMINISTRATEUR" &&  (!isset($utilisateur) || (isset($utilisateur) && $utilisateur = '')))
    $req = mysql_query("select distinct utilgr_groupe_no from utilisateur_groupe");
  $nomb_grp = mysql_num_rows($req);
  if ($nomb_grp != 0){
    echo "<CENTER><TABLE bgColor='#298CA0' cellspacing='2' width='100%'><TR><TD>";
    echo "<TABLE bgColor='#FFFFFF' cellspacing='1' cellpadding='4' width='100%'>";
    if ($typ_user == "APPRENANT" || (isset($utilisateur) && $utilisateur > 0))
    {
      $id_grp = $numero_groupe;
      $nom_grp = GetDataField ($connect,"select grp_nom_lb from groupe where grp_cdn  = $id_grp","grp_nom_lb");
      $largeur=3;
      if (!$utilisateur)
        $titre = "$mess_menu_bilan : ".strtolower($msq_tit_regr_app);
      else
        $titre = "$mess_menu_bilan : ".strtolower($msq_tit_regr_app)." $de ".ucfirst(strtolower($prenom_app))." ".ucfirst(strtolower($nom_app));
    }
    else
    {
      $largeur=4;
      $titre = "$mess_menu_bilan : ".strtolower($mess_bilan_rgt);
    }
    echo "<TR><TD background=\"images/fond_titre_table.jpg\" colspan='4' height='34' align='center' valign='center'><Font size='3' color='#FFFFFF'><B>$titre</B></FONT></TD></TR>";
    echo "<TR>";
    if ($typ_user != "APPRENANT" && !$utilisateur)
       echo "<TD class='barre_titre'>$mpr_grpmin</TD>";
    echo "<TD class='barre_titre'>$mess_mess_date</TD>";
    echo "<TD class='barre_titre'>".str_replace(":","",$mess_ag_cren)." </TD>";
    echo "<TD class='barre_titre'>$msq_mod_regr</TD>";
    echo "</TR>";
    $i = 0;
    while ($i < $nomb_grp)
    {
      $id_grp = mysql_result($req,$i,"utilgr_groupe_no");
      $req_grp = mysql_query("SELECT rdv_commentaire_cmt,rdv_creneau_nb,rdv_date_dt FROM rendez_vous WHERE
                              rdv_grp_no = $id_grp AND
                              (rdv_creneau_nb = 0 OR rdv_titre_lb = 'Présentiel')
                              GROUP BY rdv_commentaire_cmt,rdv_date_dt
                              ORDER BY rdv_date_dt");
      $nomb_rdv = mysql_num_rows($req_grp);
      if ($nomb_rdv > 0)
      {
         $nom_grp = GetDataField ($connect,"select grp_nom_lb from groupe where grp_cdn  = $id_grp","grp_nom_lb");
         $j = 0;
         echo couleur_tr($i+1,'25');
         while ($j < $nomb_rdv)
         {
           $comment = mysql_result($req_grp,$j,"rdv_commentaire_cmt");
           $cren = mysql_result($req_grp,$j,"rdv_creneau_nb");
           $date = mysql_result($req_grp,$j,"rdv_date_dt");
           if ($cren > 0)
           {
             $cren_max = GetDataField ($connect,"select rdv_creneau_nb FROM rendez_vous WHERE
                                                 rdv_grp_no = '$id_grp' AND
                                                 rdv_date_dt = '$date' AND
                                                 rdv_commentaire_cmt = \"$comment\"
                                                 ORDER BY rdv_creneau_nb","rdv_creneau_nb");
             $cren_min = GetDataField ($connect,"select rdv_creneau_nb FROM rendez_vous WHERE
                                                 rdv_grp_no = '$id_grp' AND
                                                 rdv_date_dt = '$date' AND
                                                 rdv_commentaire_cmt = \"$comment\"
                                                 ORDER BY rdv_creneau_nb desc","rdv_creneau_nb");
             $duree_rg = $cren_max-$cren_min+1;
             $deb_rg = Horaire($cren_min);
             $heures = explode("-",$deb_rg);
             $h_deb = $heures[0];
             $fin_rg = Horaire($cren_max);
             $heures = explode("-",$fin_rg);
             $h_fin = $heures[1];
           }
           $ch_date = explode("-",$date);
           $nom_mois = array ("01"=>$mois1,
                             "02"=>$mois2,
                             "03"=>$mois3,
                             "04"=>$mois4,
                             "05"=>$mois5,
                             "06"=>$mois6,
                             "07"=>$mois7,
                             "08"=>$mois8,
                             "09"=>$mois9,
                             "10"=>$mois10,
                             "11"=>$mois11,
                             "12"=>$mois12);
           $mois = $ch_date[1];
           $date = "$ch_date[2] &nbsp;".strtolower($nom_mois[$mois])." &nbsp;$ch_date[0]";
           echo "<TD bgcolor = '#DEE3E7' nowrap valign='top'><FONT size='1'>$date</font></TD><TD bgcolor = '#EFEFEF' nowrap valign='top'><FONT size='1'>$h_deb-$h_fin </font></TD><TD bgcolor = '#DEE3E7' valign='top'><FONT size='1'>$comment</font></TD></TR>";
         $j++;
         }
         if ( $i+1 < $nomb_grp)
           echo "</TR><TR><TD height='3' colspan=$largeur align='center'><small><HR SIZE=3></TD></TR>";
      }
    $i++;
    }
   echo "</TABLE></TD></TR></TABLE>";
  }
  else
  {
     entete_simple($mess_bilan_tit." : ".strtolower($mess_bilan_rgt));
     echo "<P>&nbsp;<FONT size='2'>$mess_bilan_no_reg</FONT><P>&nbsp;";
     echo "<A HREF=\"javascript:window.close();\"><IMG SRC=\"images/ok-lav.gif\" border='0'></A><P>";
     echo "</TD></TR></TABLE></TD></TR></TABLE>";
   exit();
  }
  exit();
}
if (isset($agenda) && $agenda == 1)
{
  if (isset($utilisateur) && $utilisateur > 0)
    $num_app = $utilisateur;
  else
    $num_app = $id_user;
  if ($typ_user == "APPRENANT" || $utilisateur)
  {
    if (isset($obj) && $obj == "tut")
      $req=mysql_query("SELECT utilisateur.util_nom_lb,rendez_vous.rdv_date_dt,rendez_vous.rdv_tuteur_no,rendez_vous.rdv_modecontact_lb FROM
                        rendez_vous,utilisateur where
                        rendez_vous.rdv_apprenant_no = $num_app and
                        utilisateur.util_cdn=rendez_vous.rdv_tuteur_no AND
                        rendez_vous.rdv_tuteur_no > 0
                        ORDER BY utilisateur.util_nom_lb,rendez_vous.rdv_date_dt,rendez_vous.rdv_modecontact_lb ASC");
    elseif (isset($obj) && $obj == "date")
      $req=mysql_query("SELECT rdv_date_dt,rdv_tuteur_no,rdv_modecontact_lb FROM  rendez_vous where
                        rdv_apprenant_no = $num_app AND
                        rdv_tuteur_no > 0
                        ORDER BY rdv_date_dt,rdv_tuteur_no,rdv_modecontact_lb ASC");
    elseif (isset($obj) && $obj == "mode")
      $req=mysql_query("SELECT rdv_date_dt,rdv_tuteur_no,rdv_modecontact_lb FROM rendez_vous where
                        rdv_apprenant_no = $num_app AND
                        rdv_tuteur_no > 0
                        ORDER BY rdv_modecontact_lb,rdv_tuteur_no,rdv_date_dt ASC");
  }
  else
  {
    if (isset($obj) && $obj == "tut")
      $req=mysql_query("SELECT utilisateur.util_nom_lb,rendez_vous.rdv_date_dt,rendez_vous.rdv_apprenant_no,rendez_vous.rdv_modecontact_lb FROM
                        rendez_vous,utilisateur where
                        rendez_vous.rdv_tuteur_no = $id_user and
                        utilisateur.util_cdn=rendez_vous.rdv_apprenant_no AND
                        rdv_apprenant_no > 0
                        ORDER BY utilisateur.util_nom_lb,rendez_vous.rdv_date_dt,rendez_vous.rdv_modecontact_lb ASC");
    elseif (isset($obj) && $obj == "date")
      $req=mysql_query("SELECT rdv_date_dt,rdv_apprenant_no,rdv_modecontact_lb FROM rendez_vous where
                        rdv_tuteur_no = $id_user AND
                        rdv_apprenant_no > 0
                        ORDER BY rdv_date_dt,rdv_apprenant_no,rdv_modecontact_lb ASC");
    elseif (isset($obj) && $obj == "mode")
      $req=mysql_query("SELECT rdv_date_dt,rdv_apprenant_no,rdv_modecontact_lb  FROM rendez_vous where
                        rdv_tuteur_no = $id_user AND
                        rdv_apprenant_no > 0
                        ORDER BY rdv_modecontact_lb,rdv_apprenant_no,rdv_date_dt ASC");
  }
  $nbr = mysql_num_rows($req);
  if ($nbr == 0 && ($typ_user == 'APPRENANT' || (isset($utilisateur) && $utilisateur > 0)))
  {
    $le_titre = "$mess_menu_bilan : ".strtolower(substr($mess_ag_rdv_tut,4))." $de ".ucfirst(strtolower($prenom_app))." ".ucfirst(strtolower($nom_app));
    entete_simple($le_titre);
    echo "<center><Font color='white' size='3'><B>$mess_ag_rdv_tut</B></font></TD></TR>";
    echo "<FONT size='2'>$mess_app_bil_ag</FONT><P>&nbsp;";
    echo "<A HREF=\"javascript:window.close();\"><IMG SRC=\"images/ok-lav.gif\" border='0'></A></P>";
   echo "</TD></TR></TABLE></TD></TR></TABLE>";
   exit;
  }
  elseif($nbr == 0 && $typ_user != 'APPRENANT')
  {
    entete_simple($mess_ag_rdv_tut);
    echo "<TR><TD><FONT size='2'>$mess_tut_bil_ag</FONT><P>&nbsp;";
    echo "<A HREF=\"javascript:window.close();\"><IMG SRC=\"images/ok-lav.gif\" border='0'></A></P>";
   echo "</TD></TR></TABLE></TD></TR></TABLE>";
   exit;
  }
    $total = $nbr.$h;
/*  if ($typ_user != "APPRENANT" && ((isset($utilisateur) && $utilisateur > 0) || !isset($utilisateur)))
     $titre = $msq_apprenant;
  else
*/
    $titre = $mpr_tut;
  $complement = "<img src='images/fleche0.gif' border=0>";
  $complement1 = "<img src='images/fleche1.gif' border=0>";
  if (isset($obj) && $obj == "tut")
  {
    $message_titre =$complement;
    $message_date = $complement1;
    $message_mode = $complement1;
  }
  elseif (isset($obj) && $obj == "date")
  {
    $message_date = $complement;
    $message_titre= $complement1;
    $message_mode = $complement1;
  }
  elseif (isset($obj) && $obj == "mode")
  {
    $message_mode=$complement;
    $message_titre = $complement1;
    $message_date = $complement1;
  }
   if (isset($utilisateur) && $utilisateur > 0)
      $texte = "$mess_menu_bilan : ".strtolower($mess_ag_rdv_tut)." $de ".ucfirst(strtolower($prenom_app))." ".ucfirst(strtolower($nom_app));
   else
      $texte = "$mess_menu_bilan : ".strtolower($mess_ag_rdv_tut);
   entete_simple($texte);
   echo "<TR><TD colspan=2 width='100%'><table cellpadding='3' cellspacing='1' border='0' width='100%'>";
   echo "<tr>";
   echo "<td class='barre_titre'>".$titre;
   if ((isset($obj) && $obj != "tut") || !isset($obj))
      echo nbsp(4)."<a href=\"bilan.php?agenda=1&obj=tut&utilisateur=$utilisateur\">$message_titre</a></td>" ;
   echo "<td class='barre_titre'>".$mess_mess_date;
   if ((isset($obj) && $obj != "date") || !isset($obj))
      echo nbsp(4)."<a href=\"bilan.php?agenda=1&obj=date&utilisateur=$utilisateur\">$message_date</a></td>" ;
   echo "<td class='barre_titre'>".$mess_ag_mode_cont;
   if ((isset($obj) && $obj != "mode") || !isset($obj))
      echo nbsp(4)."<a href=\"bilan.php?agenda=1&obj=mode&utilisateur=$utilisateur\">$message_mode</a></td>" ;
   echo "</tr>";
   $i=0;
   $j=1;
   while ($i < $nbr){
    echo couleur_tr($i+1,'25');
    if ($typ_user == "APPRENANT" || (isset($utilisateur) && $utilisateur > 0))
      $tuteur = mysql_result($req,$i,"rdv_tuteur_no");
    else
      $tuteur = mysql_result($req,$i,"rdv_apprenant_no");
    if (!isset($tuteur1) || (isset($tuteur1) && $tuteur != $tuteur1))
    {
      $nom  = GetdataField ($connect,"select util_nom_lb from utilisateur where util_cdn='$tuteur'","util_nom_lb");
      $prenom  = GetdataField ($connect,"select util_prenom_lb from utilisateur where util_cdn='$tuteur'","util_prenom_lb");
      if ($i > 0 && isset($obj) && $obj =="tut"){
           $jtotal = $i.$h;
        if ($typ_user != "APPRENANT" && ((isset($utilisateur) && $utilisateur == '') || !isset($utilisateur)))
        {
          echo "<TR><TD>$mess_bilan_tot_ptut</TD>";
          echo "<TD colspan='2' align='middle'>$jtotal1$h</TD></TR>";
          echo "<TR>";
        }
        $j=1;
        $jtotal1=0;
      }
      $message = "<TD>".ucfirst($prenom)."&nbsp;".ucfirst($nom)."</TD>";
      echo $message;
    }
    else
      echo "<TD align='left'>".nbsp(9)."</TD>";
    $jtotal1++;
    $tuteur1 = $tuteur;
    $date = mysql_result($req,$i,"rdv_date_dt");
    if ($date != $date1)
    {
      $ch_date = explode("-",$date);
      $dates = "$ch_date[2]-$ch_date[1]-$ch_date[0]";
      echo "<TD><font size='1'>$dates</font></TD>";
    }
    else
      echo "<TD align='left'>".nbsp(9)."</TD>";
    $date1 = $date;
    $mode = mysql_result($req,$i,"rdv_modecontact_lb");
    if ($mode != $mode1)
      echo "<TD>".ucfirst(strtolower($mode))."</TD></TR>";
    else
      echo "<TD align='left'>".nbsp(9)."</TD>";
    $mode1 = $mode;
  $j++;
  $i++;
  }
  if ($i == $nbr && $obj =="tut"){
     $jtotal = $i.$h;
     if ($typ_user != "APPRENANT" && ((isset($utilisateur) && $utilisateur == '') || !isset($utilisateur)))
     {
       echo "<TR bgcolor='#e1eaff'><TD>$mess_bilan_tot_ptut</TD>";
       echo "<TD colspan='2' align='middle'>$jtotal1$h</TD></TR>";
     }
  }

  echo "</TR>";
  echo "<TR height='35'><TD colspan='3'><B>$mess_bilan_tot_tut = $total</B></TD></TR>";
  echo "</TABLE>";
  echo "</TD></TR></TABLE></TD></TR></TABLE></TD></TR></TABLE>";
  exit();
}
if ($bilan_act == 1)
{
  if ((isset($utilisateur) && $utilisateur == '') || !isset($utilisateur))
    $app = $id_user;
  else
    $app = $utilisateur;
  $login_user=GetDataField ($connect,"select util_login_lb from utilisateur where util_cdn='$app'","util_login_lb");
  $req_act = requete('*','activite',' act_cdn='.$id_act);
  $item_act = mysql_fetch_object($req_act);
  $valid = $item_act->act_acquittement_lb;
  $nom_act = $item_act->act_nom_lb;
  $id_seq = $item_act->act_seq_no;
  $nom_seq = GetDataField ($connect,"select seq_titre_lb from sequence where seq_cdn='$id_seq'","seq_titre_lb");
  $nom_parc = GetDataField ($connect,"select parcours_nom_lb from parcours where parcours_cdn='$id_parc'","parcours_nom_lb");
  $req_suivi = requete("*","suivi1_$numero_groupe"," suivi_cdn = $id_suivi");
  while ($row = mysql_fetch_assoc($req_suivi))
  {
     $suivi_etat = $row["suivi_etat_lb"];
     $suivi_comment = $row["suivi_commentaire_cmt"];
     $suivi_fichier = $row["suivi_fichier_lb"];
     $suivi_note = $row["suivi_note_nb1"];
     $suivi_dtd = $row["suivi_date_debut_dt"];
     $suivi_dtf = $row["suivi_date_fin_dt"];
  }
  $date_dtd =  substr($suivi_dtd,0,10);
  $heure_debut = substr($suivi_dtd,11);
  $ch_date = explode ("-",$date_dtd);
  $date_debut = "$ch_date[2]-$ch_date[1]-$ch_date[0]";
  $date_dtf =  substr($suivi_dtf,0,10);
  $heure_fin =" à ".substr($suivi_dtf,11);
  $ch_date = explode ("-",$date_dtf);
  $date_fin = "$ch_date[2]-$ch_date[1]-$ch_date[0]";
  if ($suivi_dtf == '0000-00-00 00:00:00')
  {
     $date_fin = $mess_act_n_repas;
     $heure_fin = '';
  }
  if ($suivi_fichier != "" && (strstr($suivi_fichier,"/") || strstr($suivi_fichier,"\\")))
  {
     $affichier = substr(strrchr($suivi_fichier,"/"),1);
     $pere = dirname($suivi_fichier);
  }
  elseif ($suivi_fichier != "" && (!strstr($suivi_fichier,"/") || !strstr($suivi_fichier,"\\")))
  {
     $affichier = $suivi_fichier;
     $pere ="";
  }
  entete_simple("$mess_bil_tit : $nom_act");
  echo "<TR><TD colspan='2'><TABLE width='100%' bgcolor ='#FFFFFF'>";
  echo "<TR height='5'><TD></TD></TR>";
  echo "<TR height='25'><TD>$msq_parc&nbsp;&nbsp; : <B>".stripslashes($nom_parc)."</B></TD></TR>";
  echo "<TR height='25'><TD>$msq_seq :  <B>".stripslashes($nom_seq)."</B></TD></TR>";
  echo "<TR height='10'><TD></TD></TR></TABLE></TD></TR>";
  echo "<TR height='25'><TD align ='left' bgcolor='#DEE3E7'>$msq_not_act_form</TD>  <TD align ='left' bgcolor='#EFEFEF'><B>$suivi_note</B></TD></TR>";
  echo "<TR height='25'><TD align ='left' bgcolor='#DEE3E7'>$mess_1_acc_ress</TD><TD align ='left' bgcolor='#EFEFEF'><B>$date_debut</B></TD></TR>";
  echo "<TR height='25'><TD align ='left' bgcolor='#DEE3E7'>$mess_act_repas </TD><TD align ='left' bgcolor='#EFEFEF'><B>$date_fin $heure_fin</B></TD></TR>";
  echo "<TR height='25'><TD align ='left' bgcolor='#DEE3E7'>$mess_cons_dev</TD>";
  if (strstr($suivi_fichier,"/") || strstr($suivi_fichier,"\\"))
  {
    $lien = "$monURI/".$suivi_fichier;
    $ajout = "";
  }
  else
  {
    $lien = "ressources/".$login_user."_".$app."/devoirs/".$suivi_fichier;
    $ajout = "ressources/".$login_user."_".$app."/devoirs/";
  }
  $lien = urlencode($lien);
  if (strstr($suivi_fichier,'htm') || strstr($suivi_fichier,"qf_"))
  {
    echo "<TD align ='left' bgcolor='#EFEFEF'>";
    echo "<A HREF=\"#\" onclick= \"javascript:window.open('trace.php?link=$lien','','left=0, top=0, width=500,height=700,resizable=yes,scrollbars=yes, menubar=no,location=no, toolbar=no')\">$affichier</A>";
    echo "</TD></TR>";
  }
  elseif (!strstr($suivi_fichier,'htm') && !strstr($suivi_fichier,"qf_"))
    echo "<TD align ='left' bgcolor='#EFEFEF'><A HREF= \"trace.php?link=$lien\" target ='_blank'>$affichier</A></TD></TR>";
  if (!strstr($suivi_fichier,"qf_"))
    echo "<TR height='25'><TD align ='left' bgcolor='#DEE3E7'>$mess_cons_dev_cor</font></TD>";
  if ($code_act == "oof")
     $lien = $ajout.$pere."/"."corrige_".$affichier;
  elseif ($code_act == 'ona_qcm')
     $lien = $ajout.$suivi_fichier;
  $lien = urlencode($lien);
  if (strstr($suivi_fichier,'htm') && !strstr($suivi_fichier,"qf_") && !strstr($suivi_fichier,"_formagri_"))
  {
    echo "<TD align ='left' bgcolor='#EFEFEF'>";
    echo "<A HREF=\"#\" onclick= \"javascript:window.open('trace.php?link=$lien','','left=0, top=0, width=500,height=550,resizable=yes,scrollbars=yes, menubar=0,location=0, toolbar=0')\">corrige_$affichier</A>";
    echo "</TD></TR>";
  }
  elseif (!strstr($suivi_fichier,'htm') && !strstr($suivi_fichier,"qf_") && !strstr($suivi_fichier,"_formagri_"))
    echo "<TD align ='left' bgcolor='#EFEFEF'><A HREF= \"trace.php?link=$lien\" target ='_blank'>corrige_$affichier</A></TD></TR>";
   else
    echo "<TD align ='left' bgcolor='#EFEFEF'>&nbsp;</TD></TR>";
  echo "</TABLE></TD></TR></TABLE>";
  exit();
}
?>