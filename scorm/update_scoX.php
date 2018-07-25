<?php
session_start();
if ($lg == ""){
  include ('../deconnexion-fr.txt');
  exit();
}
require '../admin.inc';
require '../fonction.inc';
require "../lang$lg.inc";
//include ("click_droit.txt");
dbConnect();
$Ext = '_'.$numero_groupe;
$le_type = GetDataField ($connect,"
                               SELECT mod_content_type_lb
                               FROM scorm_module
                               WHERE`mod_cdn` ='$scormid'","mod_content_type_lb");
if (!isset($id_util) || $id_util == ''){
  $etat_actuel = GetDataField ($connect,"
                               SELECT lesson_status
                               FROM scorm_util_module$Ext
                               WHERE `user_module_no` = '".$id_user."'
                               AND `mod_module_no` ='$scormid'
                               AND `mod_grp_no` ='$numero_groupe'","lesson_status");
  if (($etat_actuel == "NOT ATTEMPTED" || $etat_actuel == "BROWSED") && strtoupper($le_type) == 'ASSET'){
     $sql = "UPDATE scorm_util_module$Ext SET
                `lesson_status` = 'COMPLETED',
                `entry` = 'RESUME',
                `raw` = '0',
                `scoreMin` = '0',
                `scoreMax` = '0'
          WHERE `user_module_no` = '".$id_user."'
                 AND `mod_module_no` ='$scormid'
                 AND `mod_grp_no` ='$numero_groupe'";
     $requete = mysql_query($sql);
  }
}
if ($id_util> 0){
// echo $_SERVER["QUERY_STRING"];
   // Le SCO a déjà été lancé une fois aussi faut'il le mettre à RESUME
   // gestion des interactions
   if (strstr($_SERVER["QUERY_STRING"],'cmi.interactions.')){
     $NbQ = $inter_nb;//echo $NbQ."-->";
     $ChParam =  utf8_decode(nl2br(str_replace("â&#128;&#153;","'",$chaine_inter)));
//$datasave = "<pre>\r\n";
     $tab = array();
     $subtab = array();
     $tab_final[] = array();
     $tab = explode('*',$ChParam);
     $nb_tab = count($tab);
     $kk = 0;
     $nb_basekk = array();$subsubtabkk = array();$subtabkk = array();
     if ($nb_tab > 0 && (!isset($inter_nb) || $NbQ == 0))
        $NbQ = 1;
     while ($kk < $nb_tab){
         $subtabkk = explode('=',$tab[$kk]);
         $subsubtabkk= explode('.',$subtabkk[0]);
         $nb_basekk[$kk] = $subsubtabkk[2];
         $ll = $kk-1;
         if ($nb_basekk[$kk] != $nb_basekk[$ll] && $kk > 1)
            $NbQ++;
         if ($kk == 0){
           $subsubtab= explode('.',$subtabkk[$kk]);
           $nb_base = $subsubtab[2];
         }
       $kk++;
     }
     for ($i = 0; $i< $nb_tab;$i++){
         $subtab = explode('=',$tab[$i]);
         $nb_subtab = count($subtab);
         for ($jj = $nb_base;$jj < ($NbQ+$nb_base);$jj++){
            if (strstr($subtab[0],"cmi.interactions.$jj.")){
               $comptage++;
               $tab_final['titre'][$comptage] = $subtab[0];
               $tab_final['valeur'][$comptage] = $subtab[1];
          //$datasave .="<BR>$jj et".$tab_final['titre'][$comptage]  . " et ".$tab_final['valeur'][$comptage]."  et ".$comptage;
            }
         }
     }
//     $datasave .= "</pre>\r\n";
//     $datasave .= "----------------------------------------------------------------------------------------------------------\r\n";

     $nombre = $nb_base;
     while ($nombre < $NbQ+$nb_base){ //$datasave .= "<BR>while ($nombre < $inter_nb+$nb_base){";
//$datasave .="<BR>$NbQ et nb_tab = $nb_tab<BR>".$chaine_inter;
         for ($z=1;$z < $comptage+1;$z++){ //$datasave .= "<BR>for ($z=1;$z < $comptage+1;$z++){";
           if ($tab_final['titre'][$z] =="cmi.interactions.$nombre.id"){
              $pid = $tab_final['valeur'][$z];
              $NbId++;
           }
           if ($tab_final['titre'][$z] =="cmi.interactions.$nombre.time")
              $ptime = $tab_final['valeur'][$z];
           if ($tab_final['titre'][$z] =="cmi.interactions.$nombre.type")
              $ptype = $tab_final['valeur'][$z];//echo "<BR>le ptype=".$ptype;echo " if (".$tab_final['titre'][$z]." ==cmi.interactions.$nombre.type)";
           if ($tab_final['titre'][$z] =="cmi.interactions.$nombre.correct_responses.0.pattern" || $tab_final['titre'][$z] =="cmi.interactions.$nombre.correct_response_text")
              $ppat = $tab_final['valeur'][$z];
           if ($tab_final['titre'][$z] =="cmi.interactions.$nombre.weighting")
              $ppoids = $tab_final['valeur'][$z];
           if ($tab_final['titre'][$z] =="cmi.interactions.$nombre.student_response" || $tab_final['titre'][$z] =="cmi.interactions.$nombre.student_response_text"){
              if ($tab_final['valeur'][$z] != '' && $tab_final['valeur'][$z] != 'undefined')
                  $pstud = $tab_final['valeur'][$z];
              else
                  $pstud = '';
           }
           if ($tab_final['titre'][$z] =="cmi.interactions.$nombre.result")
              $presult = $tab_final['valeur'][$z];
           if ($tab_final['titre'][$z] =="cmi.interactions.$nombre.latency")
              $plat = $tab_final['valeur'][$z];
           if ($tab_final['titre'][$z] =="cmi.interactions.$nombre.objectives.0.id")
              $obj = $tab_final['valeur'][$z];
           if ($z == $comptage){
              if (!isset($NbId) || $NbId == 0)
                  $pid = "NewId_".$nombre;
              $sql_verif_inter = "SELECT * FROM scorm_interact
                       WHERE `sci_mod_no` = '$scormid'
                       AND `sci_user_no` = '$id_user' AND  sci_num_lb = \"".$pid."\" AND sci_grp_no = '$numero_groupe'";
              $query = mysql_query($sql_verif_inter);
              $nbqcm = mysql_num_rows($query);
              if ($nbqcm == 1 && $pid != ''){
                  $id_sci = mysql_result($query,0,"sci_cdn");
                  $sql_act = "UPDATE scorm_interact set
                        sci_time_lb='$ptime', sci_type_lb='$ptype',
                        sci_pattern_cmt=\"$ppat\", sci_poids_nb='$ppoids',
                        sci_student_response_cmt=\"$pstud\",
                        sci_result_lb='$presult', sci_latency_lb='$plat', sci_objectives ='$obj'
                        WHERE sci_mod_no = '$scormid' AND
                        sci_user_no = '$id_user' AND
                        sci_num_lb = \"$pid\" AND
                        sci_grp_no = '$numero_groupe'";

                  $requete = mysql_query($sql_act);
              }elseif ($nbqcm != 1 && $pid != ''){
                  if ($plat == '') $latency = ""; else $lantency =$plat;
                  if ($ppoids == '') $poids = ""; else $poids = $ppoids;
                  if ($ptime == '') $duree = ""; else $duree = $ptime;
                  $id_inter = Donne_ID ($connect,"select max(sci_cdn) from scorm_interact");
                  $id_nombre = Donne_ID ($connect,"select max(sci_ordre_no) from scorm_interact where sci_mod_no = '$scormid' AND sci_user_no = '$id_user' AND sci_grp_no = '$numero_groupe'");
                  $sql_act = "INSERT INTO scorm_interact VALUES ('$id_inter',
                           \"$pid\",'$id_nombre','$id_user',
                           \"".$scormid."\",'$numero_groupe',
                           \"".$duree."\",
                           \"$ptype\",
                           \"$ppat\",
                           \"".$poids."\",
                           \"$pstud\",
                           \"$presult\",
                           \"$plat\",
                           \"$obj\")";
                           $requete = mysql_query($sql_act);
              }
           }//if ($z == $comptage)
//$datasave.= "<BR>".$sql_act;
         }// fin de for($z=1;$z < $comptage+1;$z++){
        $nombre++;
     }// fin de While
   }// fin de la gestion des interactions
//$fp = fopen("commande.txt", "a+");
//$fw = fwrite($fp, $datasave);
//fclose($fp);
   if ($objectives_nb != ''){
     $NbQ = $objectives_nb;
     $ChParam = $chaine_objectives;
//echo $ChParam;
     $tab = array();
     $subtab = array();
     $tab_final[] = array();
     $tab = explode('*',$ChParam);
     $nb_tab = count($tab);
     for ($i = 0; $i< $nb_tab;$i++){
         $subtab = explode('=',$tab[$i]);
         if ($i == 0){
           $subsubtab= explode('.',$subtab[$i]);
           $nb_base = $subsubtab[2];
         }
         $nb_subtab = count($subtab);
         for ($jj = $nb_base;$jj < ($NbQ+$nb_base);$jj++){
            if (strstr($subtab[0],"cmi.interactions.$jj.")){
               $comptage++;
               $tab_final['titre'][$comptage] = $subtab[0];
               $tab_final['valeur'][$comptage] = $subtab[1];
               $kk = $jj;
            }
         }
     }

     $nombre = $nb_base;
      $nb_tot = $objectives_nb;
     while ($nombre < $nb_tot+$nb_base){//echo "<BR>while ($nombre < $inter_nb+$nb_base){";
         for ($z=1;$z < $comptage+1;$z++){
           if ($tab_final['titre'][$z] =="cmi.objectives.$nombre.id")
              $pid = $tab_final['valeur'][$z];
           if ($tab_final['titre'][$z] =="cmi.objectives.$nombre.score.min")
              $pmin = $tab_final['valeur'][$z];
           if ($tab_final['titre'][$z] =="cmi.objectives.$nombre.score.max")
              $pmax = $tab_final['valeur'][$z];
           if ($tab_final['titre'][$z] =="cmi.objectives.$nombre.score.raw")
              $praw = $tab_final['valeur'][$z];
           if ($tab_final['titre'][$z] =="cmi.objectives.$nombre.score.scaled")
              $pscaled = $tab_final['valeur'][$z];
           if ($tab_final['titre'][$z] =="cmi.objectives.$nombre.completion_status")
              $pcompstat = $tab_final['valeur'][$z];
           if ($tab_final['titre'][$z] =="cmi.objectives.$nombre.success_status")
              $psucstat = $tab_final['valeur'][$z];
           if ($tab_final['titre'][$z] =="cmi.objectives.$nombre.status")
              $pstat = $tab_final['valeur'][$z];
           if ($z == $comptage){
              $sql_verif_obj = "SELECT * FROM scorm_objectives
                       WHERE `scob_mod_no` = '$scormid'
                       AND `scob_user_no` = '$id_user' AND  scob_num_lb = \"".$pid."\" AND scob_grp_no = '$numero_groupe'";
              $query = mysql_query($sql_verif_obj);
              $nbqcm = mysql_num_rows($query);
              if ($nbqcm == 1 && $pid != ''){
                 $id_scob = mysql_result($query,0,"scob_cdn");
                 $sql_act = "UPDATE scorm_objectives set
                        scob_min='$pmin', scob_max='$pmax',
                        scob_raw='$praw', scob_scaled='$pscaled',
                        scob_status='$pstat',
                        scob_success='$psucstat', scob_completion='$pcompstat'
                        WHERE scob_mod_no = '$scormid' AND
                        scob_user_no = '$id_user' AND
                        scob_num_lb = \"$pid\" AND
                        scob_grp_no = '$numero_groupe'";

                 $requete = mysql_query($sql_act);
              }elseif ($nbqcm != 1 && $pid != ''){
                 $id_objectives = Donne_ID ($connect,"select max(scob_cdn) from scorm_objectives");
                 $id_nombre = Donne_ID ($connect,"select max(scob_ordre_no) from scorm_objectives where
                                                  scob_mod_no = '$scormid' AND
                                                  scob_user_no = '$id_user' AND
                                                  scob_grp_no = '$numero_groupe'");
                 $sql_act = "INSERT INTO scorm_objectives VALUES ('$id_objectives',".
                           "\"$pid\",'$id_nombre','$id_user',".
                           "\"".$scormid."\",'$numero_groupe',".
                           "\"$pscaled\",\"$pmin\",\"$pmax\",".
                           "\"$praw\",\"$pstat\",\"$psucstat\",\"$pcompstat\")";
                           $requete = mysql_query($sql_act);
              }

           }//if ($z == $comptage)
         }// fin de for($z=1;$z < $comptage+1;$z++){
        $nombre++;
      }// fin de While
   }// fin de la gestion des objectifs
   // Passer certaines données en majuscule pour la sauvegarde
   $lesson_status_value = strtoupper($lesson_status);
   $lesson_mode_value = strtoupper($lesson_mode);
   $credit_value = strtoupper($credit);
   $exit_value = strtoupper($exit);
   // Pour indiquer que la prochaine visite ne ser a pas la première encore que lesson-status ai été mis à Browsed pour indiquer qu'un premier passage a déjà eu lieu
   $entry_value = "RESUME";
  // Mettre la valeur à COMPLETED au cas où le SCO ne s'en serait pas chargé et à partire de Not Attempted, l'etat bowsed indiquant que qu'il y a eu un premier passage
   $etat_actuel = GetDataField ($connect,"
                               SELECT lesson_status
                               FROM scorm_util_module$Ext
                               WHERE `user_module_no` = '".$id_user."'
                               AND `mod_module_no` ='$scormid'
                               AND `mod_grp_no` ='$numero_groupe'","lesson_status");
  if ( $etat_actuel == "NOT ATTEMPTED" && $lesson_status_value == "NOT ATTEMPTED")
      $lesson_status_value = "COMPLETED";
  // Si CREDIT n'a pas été affecté
  if ( $lesson_status_value == "COMPLETED" || $lesson_status_value == "PASSED")
        $credit_value = "CREDIT";
  if ( $lesson_status_value == "FAILED" || $lesson_status_value == "INCOMPLETE")
        $credit_value = "NO-CREDIT";
  if ( !isset($lesson_mode) || $lesson_mode_value == "")
        $lesson_mode_value = "NORMAL";

  if (isset($session_time) && $session_time != "0000:00:00.00"){
    $time1 = $total_time;
    $time2 = $session_time;
    $total_time_value = scorm_add_time($time1, $time2);
    $session_time_value = scorm_modifie_time($time2);
  }else{
    $total_time_value = $total_time;
    $session_time_value = "0000:00:00.00";
  }

  $sql = "UPDATE scorm_util_module$Ext SET
                `lesson_location` = '".$lesson_location."',
                `lesson_mode` = '".$lesson_mode_value."',
                `lesson_status` = '".$lesson_status_value."',
                `entry` = '".$entry_value."',
                `raw` = '".$raw."',
                `scoreMin` = '".$scoreMin."',
                `scoreMax` = '".$scoreMax."',
                `total_time` = '".$total_time_value."',
                `session_time` = '".$session_time_value."',
                `suspend_data` = '".$suspend_data."',
                `credit` = '".$credit_value."',
                `exit` = '".$exit_value."'
          WHERE `user_module_no` = '".$id_util."'
                 AND `mod_module_no` ='".$scormid."'
                 AND `mod_grp_no` ='$numero_groupe'";
  $requete = mysql_query($sql);
  $ordrepere = GetDataField ($connect,"
                               SELECT mod_pere_lb
                               FROM scorm_module
                               WHERE mod_cdn = ".$scormid,"mod_pere_lb");
  $req_seq = requete_order("mod_cdn","scorm_module"," mod_pere_lb = \"$ordrepere\"","mod_cdn ASC");
  if ($req_seq == TRUE){
     $scopere = GetDataField ($connect,"
                               SELECT mod_cdn
                               FROM scorm_module
                               WHERE mod_numero_lb = \"$ordrepere\" AND mod_content_type_lb='LABEL'","mod_cdn");

     $lesson_time = GetDataField ($connect,"select total_time from scorm_util_module$Ext where mod_module_no = '$scopere' AND user_module_no='$id_util' AND mod_grp_no = '$numero_groupe'","total_time");
     $time1 = $lesson_time;
     $time2 = $session_time;
     $lesson_time_value = scorm_add_time($time1, $time2);
     $session_time = scorm_modifie_time($time2);
     $sql1 = "UPDATE scorm_util_module$Ext SET
                `total_time` = '".$lesson_time_value."',
                `session_time` = '".$session_time."'
              WHERE `user_module_no` = '".$id_util."'
                 AND `mod_module_no`='$scopere'
                 AND `mod_grp_no` ='$numero_groupe'";
     $requete = mysql_query($sql1);
     $i=0;$j=0;
     while ($item = mysql_fetch_object($req_seq)) {
        $i++;
        $scoid = $item->mod_cdn;
        $scoOk = GetDataField ($connect,"
                               SELECT lesson_status
                               FROM scorm_util_module$Ext
                               WHERE mod_module_no = '$scoid'
                                    AND user_module_no = '$id_user'
                                    AND `mod_grp_no` ='$numero_groupe'","lesson_status");
        if ($scoOk == "COMPLETED" || $scoOk == "PASSED")
           $j++;
     }
     if ($j == $i){
        $sql2 = "UPDATE scorm_util_module$Ext SET
                `lesson_status` = 'COMPLETED',
                `entry` = 'RESUME',
                `credit` = 'CREDIT'
               WHERE `user_module_no` = '$id_user'
                 AND `mod_module_no` ='$scopere'
                 AND `mod_grp_no` ='$numero_groupe'";
        $requete = mysql_query($sql2);
     }
  }
}

?>
<html>
<head>
   <title>Modification des tables et réinitialisation</title>
   <script type="text/javascript" src="../fonction.js"></script>
<?php
if ($id_util > 0){
    $agent=getenv("HTTP_USER_AGENT");
    if (strstr($agent,"MSIE")){
       echo "<SCRIPT Language=\"Javascript\">";
       echo "window.parent.parent.opener.location.reload();";
       echo "</SCRIPT>";
    }else{
       echo "<SCRIPT Language=\"Javascript\">";
       echo "parent.parent.parent.top.opener.location.reload();";
       echo "</SCRIPT>";
    }
    ?>
    <script type="text/javascript">
    <!--//
      parent.frames['index_contenu'].location.href="<?php echo "index_contenuX.php?id_parc=$id_parc&id_seq=$id_seq&scormid=$scormid&cours=1&numero_groupe=$numero_groupe"; ?>";
    //-->
    </script>

    <?php
}
?>
<script language="Javascript">
<!--
function envoi_form() {
  document.cmiForm.submit(); // envoi du formulaire
// -->
}
</script>
</head>

<body>
   <form name="cmiForm" method="POST" action="<?php echo $_SERVER["PHP_SELF"] ?>">
        <input type="hidden" name="id_util" />
        <input type="hidden" name="id_seq" />
        <input type="hidden" name="scormid" />
        <input type="hidden" name="lesson_status" />
        <input type="hidden" name="lesson_mode" />
        <input type="hidden" name="lesson_location" />
        <input type="hidden" name="credit" />
        <input type="hidden" name="entry" />
        <input type="hidden" name="raw" />
        <input type="hidden" name="total_time" />
        <input type="hidden" name="session_time" />
        <input type="hidden" name="suspend_data" />
        <input type="hidden" name="scoreMin" />
        <input type="hidden" name="scoreMax" />
        <input type="hidden" name="inter_nb" />
        <input type="hidden" name="chaine_inter" />
   </form>
<?php
if ($id_util > 0 && ($uu == 0 || !isset($uu))){
   $uu++;
   ?>
   <script language="Javascript">
        envoi_form();
   </script>
   <?php
}
?>
</body>
</html>

