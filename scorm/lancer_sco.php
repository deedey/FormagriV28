<?php
if (!isset($_SESSION)) session_start();
if (!isset($_SESSION['id_user']) || $_SESSION['id_user'] == "")
{
  exit();
}
include ("../include/UrlParam2PhpVar.inc.php");
require '../admin.inc.php';
require '../fonction.inc.php';
require "../lang$lg.inc.php";
if (isset($numero_groupe))
   $Ext = '_'.$numero_groupe;
//include ("../click_droit.txt");
dbConnect();
include ("../include/varGlobals.inc.php");
echo "<HTML>";
echo "<HEAD>";
echo '<SCRIPT language="JavaScript" type="text/javascript" SRC="../fonction.js"></SCRIPT>';
//if ($aicc_sid != '')
//   echo "<SCRIPT language=\"JavaScript\" type=\"text/javascript\" SRC=\"aicc.php\"></SCRIPT>\n";

if (!isset($dou) || (isset($dou) && $dou != 'sequence'))
{
  $sco = array();
  if ($typ_user == "APPRENANT")
  {
        $sql = "SELECT * FROM scorm_util_module$Ext,scorm_module,utilisateur
                 WHERE user_module_no = '$id_user' AND util_cdn='$id_user' AND mod_cdn = '$scormid'
                 AND `mod_module_no` = '$scormid' AND `mod_grp_no` = '$numero_groupe' AND `mod_seq_no` = '$id_seq'";
        $query = mysql_query($sql);
        $donnees_apprenant = mysql_fetch_array($query);
        $sco['student_id'] = $donnees_apprenant['util_cdn'];
        $sco['student_name'] = $donnees_apprenant['util_nom_lb'].", ".$donnees_apprenant['util_prenom_lb'];
        $sco['lesson_location'] = $donnees_apprenant['lesson_location'];
        $sco['lesson_mode'] = strtolower($donnees_apprenant['lesson_mode']);
        $sco['credit'] = strtolower($donnees_apprenant['credit']);
        $sco['lesson_status'] = strtolower($donnees_apprenant['lesson_status']);
        $sco['completion_status'] = strtolower($donnees_apprenant['lesson_status']);
        $sco['success_status'] = strtolower($donnees_apprenant['lesson_status']);
        $sco['entry'] = strtolower($donnees_apprenant['entry']);
        $sco['raw'] = ($donnees_apprenant['raw'] == -1) ? "" : "".$donnees_apprenant['raw'];
        $sco['scoreMin'] = ($donnees_apprenant['scoreMin'] == -1) ? "" : "".$donnees_apprenant['scoreMin'];
        $sco['scoreMax'] = ($donnees_apprenant['scoreMax'] == -1) ? "" : "".$donnees_apprenant['scoreMax'];
        $sco['total_time'] = $donnees_apprenant['total_time'];
        $sco['suspend_data'] = $donnees_apprenant['suspend_data'];
        $sco['comments'] = $donnees_apprenant['comments'];
        $sco['comments_from_lms'] = $donnees_apprenant['comments_from_lms'];
        $sco['mod_launch_lb'] = $donnees_apprenant['mod_launch_lb'];
  }
  else
  { 
        $sql = "SELECT * FROM utilisateur,scorm_module WHERE util_cdn='$id_user' and mod_cdn = '$scormid'";
        $query = mysql_query($sql);
        $donnees = mysql_fetch_array($query);
        $sco['student_id'] = $donnees['util_cdn'];
        $sco['student_name'] = $donnees['util_nom_lb'].", ".$donnees['util_prenom_lb'];
        $sco['lesson_location'] = "";
        $sco['lesson_mode'] = "normal";
        $sco['credit'] ="no-credit";
        $sco['lesson_status'] = "not attempted";
        $sco['completion_status'] = "not attempted";
        $sco['success_status'] = "not attempted";
        $sco['entry'] = "ab-initio";
        $sco['raw'] = "";
        $sco['scoreMin'] = "";
        $sco['scoreMax'] = "";
        $sco['total_time'] = "0000:00:00.00";
        $sco['suspend_data'] = "";
        $sco['comments'] = "";
        $sco['comments_from_lms'] = "";
        $sco['mod_launch_lb'] = $donnees['mod_launch_lb'];
        $VO = "";
  }

  //variables de base
  $sco['interactions_children'] = "id, type, correct_responses.0.pattern, weighting, student_response, result, latency, time,correct_response_text,student_response_text";
  $sco['objectives_children'] = "id, score.raw, score.min, score.max, score.scaled, completion_status, success_status, status";
  $sco['interactions._count'] = '';
  $sco['objectives_count'] = '';
  $sco['_children'] = "student_id,student_name,lesson_location,credit,lesson_status,lesson_mode,entry,score,total_time,exit,session_time";
  $sco['score_children'] = "raw,min,max";
  $sco['exit'] = "";
  $sco['exit2'] = "";
  $sco['score_scaled'] = $sco['raw'];
  $sco['session_time'] = "0000:00:00.00";
  $sco['launch_data'] = stripslashes($sco['mod_launch_lb']);
}
include ("ApiScorm.inc_obj.php");
echo "<title>$mess_parc_sco</title>";
$cours = (isset($cours)) ? $cours : '';
if (isset($dou) && $dou == 'sequence')
{
  $lien = mysql_result(requete_order("*","scorm_module"," mod_seq_no = '$id_seq' AND mod_launch_lb != ''","mod_cdn ASC"),0,"mod_launch_lb");
  if (!strstr($lien,"http://"))
     $lien = "../".$lien;
  $lien_index = "index_contenu.php?id_parc=$id_parc&id_seq=$id_seq&cours=$cours&numero_groupe=$numero_groupe";
}
if (isset($aicc_sid) && $aicc_sid != '' && isset($cours) && $cours == 1)
{
  $lien = urldecode($lien);
  $lien .= "?aicc_sid=".urlencode($aicc_sid)."&aicc_url=".urlencode($aicc_url)."&vendorparam=".urlencode($vendor_param);
//  if ($vendor_param != '')
//     $lien .= ;
}
if (!isset($aicc_sid)) $aicc_sid = '';
$lien_index = urldecode($lien_index);
   echo "<frameset cols='0%,28%,72%'>";
     if (isset($cours) && $cours == 1)
     {
       echo "<frameset rows='0,0'>";
          echo "<frame src=\"update_sco.php?scormid=$scormid&numero_groupe=$numero_groupe&aicc_sid=$aicc_sid\" name='idsFrame' frameborder='0' resize scrolling=no />";
          echo "<frame src=\"blank_sco.php?scormid=$scormid\" name='ferme_sco' frameborder='0' noresize scrolling=no />";
       echo "</frameset>";
     }
     else
       echo "<frame src='../vide.php' name='nulle' frameborder='0' scrolling=no />";
     echo "<frame src=\"$lien_index&passe=1&aicc_sid=$aicc_sid\" name='index_contenu' frameborder='0' scrolling='auto' />";
     echo "<frame src=\"$lien\" name='contenu' frameborder='0' scrolling='auto' />";

   echo "</frameset>";
?>

<noframes>
<body bgcolor="silver">
<center>
<h2>Votre navigateur n'accepte pas les frames.</h2>
</center>
</body></html>
