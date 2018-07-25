<?php
session_start();
header("Access-Control-Allow-Origin: * ");
if (!isset($_SESSION['id_user']) || $_SESSION['id_user'] == "")
{
  exit();
}
include ("../include/UrlParam2PhpVar.inc.php");
require '../admin.inc';
require '../fonction.inc';
require '../graphique/admin.inc';
require "../lang$lg.inc";
//include ("../click_droit.txt");
dbConnect();
$Ext = '_'.$numero_groupe;
echo "<html>";
echo "<head>";
echo "<SCRIPT  LANGUAGE=\"JavaScript1.2\" SRC=\"../fonction.js\"></SCRIPT>";
if ($dou != 'sequence'){
  $sco = array();
  if ($typ_user == "APPRENANT"){
        $sql = "SELECT * FROM scorm_util_module$Ext as UM,scorm_module as SM,utilisateur as UT
                 WHERE UM.user_module_no = '$id_user' AND UT.util_cdn='$id_user' AND SM.mod_cdn = '$scormid'
                 AND UM.`mod_module_no` = '$scormid' AND SM.`mod_seq_no` = '$id_seq' AND UM.mod_grp_no = '$numero_groupe'";
        $query = mysql_query($sql);
        $donnees_apprenant = mysql_fetch_array($query);
        $sco['student_id'] = $donnees_apprenant['util_cdn'];
        $sco['student_name'] = $donnees_apprenant['util_nom_lb']." ".$donnees_apprenant['util_prenom_lb'];
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

  }else{ // autre
        $sql = "SELECT util_nom_lb,util_prenom_lb FROM utilisateur WHERE util_cdn='$id_user'";
        $query = mysql_query($sql);
        $donnees = mysql_fetch_array($query);
        $sco['student_id'] = $donnees['util_cdn'];
        $sco['student_name'] = $donnees['util_nom_lb']." ".$donnees['util_prenom_lb'];
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
  }
  //common vars
  $sco['objectives_children'] = "id,score.raw,score.min,score.max,score.scaled,status,success_status,completion_status";
  $sco['objectives_count'] = 0;
  $sco['interactions_children'] = "id,time,type,correct_responses.0.pattern,weighting,student_response,result,latency,correct_response_text,student_response_text";
  $sco['interactions._count'] = 0;
  $sco['_children'] = "student_id,student_name,lesson_location,credit,lesson_status,lesson_mode,entry,score,total_time,exit,session_time";
  $sco['exit'] = "";
  $sco['exit2'] = "";
  $sco['score_scaled'] = $sco['raw'];
  $sco['score_children'] = "raw,min,max";
  $sco['session_time'] = "0000:00:00.00";
  $sco['launch_data'] = stripslashes($sco['mod_launch_lb']);
  $chaine = "&sco_student_id=".$sco['student_id']."&sco_student_name=".$sco['student_name'].
          "&sco_lesson_mode=".$sco['lesson_mode']."&sco_lesson_location=".$sco['lesson_location'].
          "&sco_credit=".$sco['credit']."&sco_exit=".$sco['exit']."&sco_lesson_status=".$sco['lesson_status'].
          "&sco_exit2=".$sco['exit2']."&sco_completion_status=".$sco['lesson_status'].
          "&sco_success_status=".$sco['lesson_status']."&sco_score_scaled=".$sco['score_scaled'].
          "&sco_entry=".$sco['entry']."&sco_raw=".$sco['raw']."&sco_scoreMin=".$sco['scoreMin'].
          "&sco_scoreMax=".$sco['scoreMax']."&sco_score_children=".$sco['score_children'].
          "&sco_total_time=".$sco['total_time']."&sco_session_time=".$sco['session_time'].
          "&sco_comments=".$sco['comments']."&sco_comments_from_lms=".$sco['comments_from_lms'].
          "&sco_suspend_data=".$sco['suspend_data']."&sco_launch_data=".$sco['mod_launch_lb'].
          "&sco_interactions_children=".$sco['interactions_children'].
          "&sco_objectives_children=".$sco['objectives_children']."&sco_children=".$sco['_children'];

  $sco_student_id = $sco['student_id'];
  $sco_student_name = $sco['student_name'];
  $sco_lesson_location = $sco['lesson_location'];
  $sco_lesson_mode = $sco['lesson_mode'];
  $sco_lesson_status = $sco['lesson_status'];
  $sco_completion_status = $sco['lesson_status'];
  $sco_success_status = $sco['lesson_status'];
  $sco_raw = $sco['raw'];
  $sco_score_scaled = $sco['score_scaled'];
  $sco_score_children = $sco['score_children'];
  $sco_interactions_children = $sco['interactions_children'];
  $sco_objectives_children = $sco['objectives_children'];
  $sco_scoreMin = $sco['scoreMin'];
  $sco_scoreMax = $sco['scoreMax'];
  $sco_total_time = $sco['total_time'];
  $sco_session_time = $sco['session_time'];
  $sco_credit = $sco['credit'];
  $sco_exit = $sco['exit'];
  $sco_exit2 = $sco['exit2'];
  $sco_entry = $sco['entry'];
  $sco_suspend_data = $sco['suspend_data'];
  $sco_comments = $donnees_apprenant['comments'];
  $sco_comments_from_lms = $donnees_apprenant['comments_from_lms'];
  $sco_mod_launch_lb = $sco['mod_launch_lb'];

}
if(!strstr($_SERVER['HTTP_REFERER'],$adresse_http))
{
   echo "<script language=\"JavaScript\">";
   echo "document.location.replace(\"$adresse_http/index.php\")";
   echo "</script>";
   exit();
}

if (strstr($lien,"http://")){
   $liste_adr = explode("/",$lien);
   $adressage = "http://".$liste_adr[2];//echo "$lien<BR>$adressage";
}else{
   $liste_adr = explode("/",$lien);
   $adressage = $adresse_http;

}
echo "<title>$mess_parc_sco</title>";
if ($dou == 'sequence'){
  $lien = mysql_result(requete_order("*","scorm_module"," mod_seq_no = '$id_seq' AND mod_launch_lb != ''","mod_cdn ASC"),0,"mod_launch_lb");
  if (!strstr($lien,"http://"))
     $lien = "../".$lien;
  $lien_index = urlencode("index_contenuX.php?id_parc=$id_parc&id_seq=$id_seq&cours=$cours");
}
$lien_index = urldecode($lien_index);
     echo "<frameset cols='0%,28%,72%'>";
          if ($cours == 1){
            echo "<frameset rows='0,0'>";
             echo "<frame src=\"update_scoX.php?scormid=$scormid&numero_groupe=$numero_groupe\" name='idsFrame' frameborder='0' noresize scrolling=no />";
             echo "<frame src=\"blank_sco.php?scormid=$scormid&id_util=$id_util\" name='ferme_sco' frameborder='0' noresize scrolling=no />";
            echo "</frameset>";
          }else
            echo "<frame src='' name='nulle' frameborder='0' scrolling=no />";
          echo "<frame src=\"$lien_index&passe=1\" name='index_contenu' frameborder='0' scrolling='auto' />";
             $ext = strstr($lien,"educagrinet") ? "cfm" : "php";
             $lien = urlencode($lien);
             echo "<frame src=\"$adressage/scorm/lanceur.$ext?id_user=$id_user&numero_groupe=$numero_groupe&lien=$lien&id_parc=$id_parc&id_seq=$id_seq&cours=$cours&scormid=$scormid&domaine=$adresse_http".$chaine."\" name='lanceur' frameborder='0' scrolling='auto' />";
echo "</frameset>";
?>
<noframes>
<body bgcolor="silver">
<center>
<h2>Votre navigateur n'accepte pas les frames.</h2>
</center>
</body>
</noframes>
</body></html>
