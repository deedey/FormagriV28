<?php
if (!isset($_SESSION)) session_start();
if (!isset($_SESSION['id_user']) || $_SESSION['id_user'] == "")
{
  exit();
}
include ("../include/UrlParam2PhpVar.inc.php");
require "../admin.inc.php";
require "../fonction.inc.php";
require "../lang$lg.inc.php";
include ('../include/varGlobals.inc.php');
//include ("click_droit.txt");
dbConnect();
$Ext = '_'.$numero_groupe;
$le_type = GetDataField ($connect,"SELECT mod_content_type_lb
                               FROM scorm_module
                               WHERE`mod_cdn` ='$scormid'","mod_content_type_lb");

if (!isset($_POST['id_util']) && empty($aicc_sid))
{
  $etat_actuel = GetDataField ($connect,"
                               SELECT lesson_status
                               FROM scorm_util_module$Ext
                               WHERE `user_module_no` = '".$id_user."' AND
                               `mod_module_no` ='$scormid' AND
                               `mod_grp_no` = '$numero_groupe'","lesson_status");
  if (($etat_actuel == "NOT ATTEMPTED" || $etat_actuel == "BROWSED") && strtoupper($le_type) == 'ASSET')
  {
     $sql = "UPDATE scorm_util_module$Ext SET
                `lesson_status` = 'COMPLETED',
                `entry` = 'RESUME',
                `raw` = '0',
                `scoreMin` = '0',
                `scoreMax` = '0'
          WHERE `user_module_no` = '".$id_user."' AND
                `mod_module_no` ='$scormid' AND
                `mod_grp_no` = '$numero_groupe'";
     $requete = mysql_query($sql);
  }
}
/*
echo "<pre>";
     print_r($_POST);print_r($_GET);
echo "</pre>";
*/

if (isset($_POST['id_util']) && $_POST['id_util'] > 0)
{
   // gestion des interactions
   if (strstr($_POST['chaine_inter'],'cmi.interactions.'))
   {
     $NbQ = $_POST['inter_nb'];
     $nb_revu = $NbQ;
     $ChParam = utf8_decode(nl2br(str_replace("â&#128;&#153;","'",$_POST["chaine_inter"])));
//     $datasave = "<pre>\r\n";
//     $datasave .= $ChParam."\r\n";
     $tab = array();
     $subtab = array();
     $tab_final[] = array();
     $tab = explode('&',$ChParam);
     $nb_tab = count($tab);
     $kk = 0;
     $comptage = 0;
     $nb_basekk = array();$subsubtabkk = array();$subtabkk = array();
     if ($nb_tab > 0 && (!isset($inter_nb) || $NbQ == 0))
        $NbQ = 1;
     while ($kk < $nb_tab)
     {
         $subtabkk = explode('=',$tab[$kk]);
         $subsubtabkk= explode('.',$subtabkk[0]);
         $nb_basekk[$kk] = $subsubtabkk[2];
         $ll = $kk-1;
         if (isset($nb_basekk[$kk]) && isset($nb_basekk[$ll]) && $nb_basekk[$kk] != $nb_basekk[$ll] && $kk > 1)
            $NbQ++;
         if ($kk == 0)
         {
           $subsubtab= explode('.',$subtabkk[$kk]);
           $nb_base = $subsubtab[2];
         }
       $kk++;
     }
     for ($i = 0; $i< $nb_tab;$i++)
     {
         $subtab = explode('=',$tab[$i]);
         $nb_subtab = count($subtab);
         for ($jj = $nb_base;$jj < ($NbQ+$nb_base);$jj++)
         {//echo "<BR>$jj = $nb_base;$jj < ($NbQ+$nb_base);$jj++";
            if (strstr($subtab[0],"cmi.interactions.$jj."))
            {
               $comptage++;
               $tab_final["titre"][$comptage] = $subtab[0];
               $tab_final["valeur"][$comptage] = $subtab[1];
         //echo "<BR>$jj et".$tab_final['titre'][$comptage]  . " et ".$tab_final['valeur'][$comptage]."  et ".$comptage;
            }
         }
     }
//       echo "<BR><Binter_nb = ".$_POST['inter_nb'] ."<BR>chaine_inter = ".$_POST['chaine_inter']."<BR>";

     $nombre = $nb_base;
     $NbId = 0;
     $nb_tot = $_POST['inter_nb'];
     while ($nombre < $nb_tot+$nb_base)
     {//echo "for ($z=1;$z < $comptage+1;$z++){";
         $ptime = ''; $pstud = '';
         for ($z=1;$z < $comptage+1;$z++)
         {
           if ($tab_final['titre'][$z] =="cmi.interactions.$nombre.id")
           {
              $pid = $tab_final['valeur'][$z];
              $NbId++;
           }
           if ($tab_final['titre'][$z] =="cmi.interactions.$nombre.time")
              $ptime = $tab_final['valeur'][$z];
           if ($tab_final['titre'][$z] =="cmi.interactions.$nombre.type")
              $ptype = $tab_final['valeur'][$z];
           if ($tab_final['titre'][$z] =="cmi.interactions.$nombre.correct_responses.0.pattern" || $tab_final['titre'][$z] =="cmi.interactions.$nombre.correct_response_text")
              $ppat =  $tab_final["valeur"][$z] ;
           if ($tab_final['titre'][$z] =="cmi.interactions.$nombre.weighting")
              $ppoids = $tab_final['valeur'][$z];
           else
              $ppoids = 1;
           if ($tab_final['titre'][$z] =="cmi.interactions.$nombre.student_response" || $tab_final['titre'][$z] =="cmi.interactions.$nombre.student_response_text")
           {
              if ($tab_final["valeur"][$z] != '' && $tab_final['valeur'][$z] != 'undefined')
                  $pstud =  $tab_final["valeur"][$z];
           }
           if ($tab_final['titre'][$z] == "cmi.interactions.$nombre.result")
              $presult = $tab_final['valeur'][$z];
           if ($tab_final['titre'][$z] =="cmi.interactions.$nombre.latency")
              $plat = $tab_final['valeur'][$z];
           else
              $plat = '';
           if ($tab_final['titre'][$z] =="cmi.interactions.$nombre.objectives.0.id")
              $obj = $tab_final['valeur'][$z];
           else
              $obj = '';
//           echo "<BR>$pid , $ppat,  $pstud,  $presult,  $ppoids, $ptype , $ptime";
           if ($z == $comptage)
           {
              if (!isset($NbId) || $NbId == 0)
                  $pid = "NewId_".$nombre;
              $sql_verif_inter = "SELECT * FROM scorm_interact WHERE
                      `sci_mod_no` = '$scormid'AND
                      `sci_user_no` = '$id_user' AND
                      `sci_num_lb` = \"".$pid."\" AND
                      `sci_grp_no` = '$numero_groupe'";
              $query = mysql_query($sql_verif_inter);
              $nbqcm = mysql_num_rows($query);
              if ($nbqcm == 1 && $pid != '')
              {
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
//                 echo "<BR>la =" .$sql_act."<BR>";
              }
              elseif ($nbqcm != 1 && $pid != '')
              {
                 if ($plat == '') $latency = ""; else $lantency =$plat;
                 if ($ppoids == '') $poids = ""; else $poids = $ppoids;
                 if ($ptime == '') $duree = ""; else $duree = $ptime;
                 $id_inter = Donne_ID ($connect,"select max(sci_cdn) from scorm_interact");
                 $id_nombre = Donne_ID ($connect,"select max(sci_ordre_no) from scorm_interact where
                                                  sci_mod_no = '$scormid' AND
                                                  sci_user_no = '$id_user' AND
                                                  sci_grp_no = '$numero_groupe'");
                 $sql_act = "INSERT INTO scorm_interact VALUES ('$id_inter',
                           \"$pid\",'$id_nombre','$id_user',
                           \"".$_POST['scormid']."\",'$numero_groupe',
                           \"".$duree."\",
                           \"$ptype\",
                           \"$ppat\",
                           \"".$poids."\",
                           \"$pstud\",
                           \"$presult\",
                           \"$plat\",
                           \"$obj\")";
                           $requete = mysql_query($sql_act);
//                           echo "<BR>ici =" .$sql_act;
              }
           }//if ($z == $comptage)
         }// fin de for($z=1;$z < $comptage+1;$z++){
        $nombre++;
//     $datasave .= "</pre>\r\n";
//     $datasave .= "----------------------------------------------------------------------------------------------------------\r\n";
//     $fp = fopen("commande.txt", "a+");
//     $fw = fwrite($fp, $datasave);
//     fclose($fp);
     }// fin de While
   }// fin de la gestion des interactions
   if ($_POST['objectives_nb'] != 0)
   {
     $NbQ = $_POST['objectives_nb'];//echo $NbQ;
     $ChParam = $_POST['chaine_objectives'];
//echo $ChParam;
     $tab = array();
     $subtab = array();
     $tab_final[] = array();
     $tab = explode('&',$ChParam);
     $nb_tab = count($tab);
     for ($i = 0; $i< $nb_tab;$i++)
     {
         $subtab = explode('=',$tab[$i]);
         $nb_subtab = count($subtab);
         for ($jj = 0;$jj < 50;$jj++)
         {
            if (strstr($subtab[0],"cmi.objectives.$jj."))
            {
               $comptage++;
               $tab_final['titre'][$comptage] = $subtab[0];
               $tab_final['valeur'][$comptage] = $subtab[1];
               $kk = $jj;
//echo "<BR>".$tab_final['titre'][$comptage]  . " et comptage= $comptage  et jj = $jj et kk = $kk ".$tab_final['valeur'][$comptage];
            }
         }
      }
      $nombre = 0;
      $nb_tot = $NbQ;
//echo "<BR>nb_tot = $nb_tot et while ($nombre < $kk){<BR>";
      while ($nombre < $kk+1)
      {
         for ($z=1;$z < $comptage+1;$z++)
         {
//echo "<BR>cmi.objectives.$nombre.id<BR>";
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
           if ($z == $comptage)
           {
              $sql_verif_obj = "SELECT * FROM scorm_objectives
                       WHERE `scob_mod_no` = '$scormid' AND
                       `scob_user_no` = '$id_user' AND
                       `scob_num_lb` = \"".$pid."\" AND
                       `scob_grp_no` = '$numero_groupe'";
//echo "<BR>$sql_verif_obj<BR>";
              $query = mysql_query($sql_verif_obj);
              $nbqcm = mysql_num_rows($query);
//echo "<BR>if ($nbqcm == 1 && $pid != '')<BR>";
              if ($nbqcm == 1 && $pid != '')
              {
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
//echo "<BR>la =" .$sql_act."<BR>";
              }
              elseif ($nbqcm != 1 && $pid != '')
              {
                 $id_objectives = Donne_ID ($connect,"select max(scob_cdn) from scorm_objectives");
                 $id_nombre = Donne_ID ($connect,"select max(scob_ordre_no) from scorm_objectives where
                                                  scob_mod_no = '$scormid' AND
                                                  scob_user_no = '$id_user' AND
                                                  `scob_grp_no` = '$numero_groupe'");
                 $sql_act = "INSERT INTO scorm_objectives VALUES ('$id_objectives',".
                           "\"$pid\",'$id_nombre','$id_user',".
                           "\"".$_POST['scormid']."\",'$numero_groupe',".
                           "\"$pscaled\",\"$pmin\",\"$pmax\",".
                           "\"$praw\",\"$pstat\",\"$psucstat\",\"$pcompstat\")";
                           $requete = mysql_query($sql_act);
              }

           }//if ($z == $comptage)
         }// fin de for($z=1;$z < $comptage+1;$z++){
        $nombre++;
      }// fin de While
   }// fin de la gestion des objectifs

   // On intègre les valeurs en les passant en majuscule au cas où elles n'aurait pas été changée
   if (isset($_POST['lesson_status']))
      $lesson_status_value = strtoupper($_POST['lesson_status']);
   if (isset($_POST['lesson_mode']))
      $lesson_mode_value = strtoupper($_POST['lesson_mode']);
   if (isset($_POST['credit']))
      $credit_value = strtoupper($_POST['credit']);
   if (isset($_POST['exit']))
      $exit_value = strtoupper($_POST['exit']);

   // Pour indiquer que la prochaine visite ne ser a pas la première encore que lesson-status ai été mis à Browsed pour indiquer qu'un premier passage a déjà eu lieu
   $entry_value = "RESUME";
  // Mettre la valeur à COMPLETED au cas où le SCO ne s'en serait pas chargé et à partire de Not Attempted, l'etat bowsed indiquant que qu'il y a eu un premier passage
  $etat_actuel = GetDataField ($connect,"SELECT lesson_status
                               FROM scorm_util_module$Ext
                               WHERE `user_module_no` = '".$id_user."'
                               AND `mod_module_no` ='$scormid' AND
                               mod_grp_no = '$numero_groupe'","lesson_status");
  if ($etat_actuel == "NOT ATTEMPTED"  && $lesson_status_value == "NOT ATTEMPTED")
      $lesson_status_value = "COMPLETED";

  // set credit if needed
  if ( $lesson_status_value == "COMPLETED" || $lesson_status_value == "PASSED" || $lesson_status_value == "BROWSED")
        $credit_value = "CREDIT";
  elseif ( $lesson_status_value == "FAILED")
        $credit_value = "NO-CREDIT";
  if ( !isset($lesson_mode) || $lesson_mode_value == "")
        $lesson_mode_value = "NORMAL";

  if (isset($_POST['session_time']) && $_POST['session_time'] != "0000:00:00.00" && !empty($_POST['session_time']))
  {
    $time1 = $_POST['total_time'];
    $time2 = $_POST['session_time'];
    if (!empty($time1) && !empty($time2))
        $total_time_value = scorm_add_time($time1, $time2);
    $session_time_value = scorm_modifie_time($time2);
  }
  else
  {
    $total_time_value = $_POST['total_time'];
    $session_time_value = "0000:00:00.00";
  }
  $suspend_data = GetDataField ($connect,"SELECT suspend_data
                               FROM scorm_util_module$Ext
                               WHERE `user_module_no` = '".$id_user."'
                               AND `mod_module_no` ='$scormid' AND
                               mod_grp_no = '$numero_groupe'","suspend_data");
  $SuspenData = (strstr($_POST['suspend_data'],'assmnt	:{	#	:{') && $_POST['raw'] > 0) ? $suspend_data : $_POST['suspend_data'];
  $sql = "UPDATE scorm_util_module$Ext SET
                `lesson_location` = '".$_POST['lesson_location']."',
                `lesson_mode` = '".$lesson_mode_value."',
                `lesson_status` = '".$lesson_status_value."',
                `entry` = '".$entry_value."',
                `raw` = '".$_POST['raw']."',
                `scoreMin` = '".$_POST['scoreMin']."',
                `scoreMax` = '".$_POST['scoreMax']."',
                `total_time` = '".$total_time_value."',
                `session_time` = '".$session_time_value."',
                `suspend_data` = '".$SuspenData."',
                `comments` = '".$_POST['comments']."',
                `comments_from_lms` = '".$_POST['comments_from_lms']."',
                `credit` = '".$credit_value."'
          WHERE `user_module_no` = '".$id_user."'
                 AND `mod_module_no` ='".$_POST['scormid']."' AND
                 `mod_grp_no` = '$numero_groupe'";

  $requete = mysql_query($sql);
  if (strstr($_POST['suspend_data'],'assmnt	:{	#	:{') && (isset($_POST['raw']) && $_POST['raw'] == '' || !isset($_POST['raw'])))
  {
     $tab = explode(',	s	:',$_POST['suspend_data']);
     $NbrS = count($tab);
     for ($i=1;$i < $NbrS+1; $i++)
     {
         $retab = explode (',	a	:',$tab[$i]);
         $note += $retab[0];
     }
     $lanote = ceil(($note*100)/($NbrS-1));
     //echo $lanote;
     //echo "<br>et affiche $note";
     //exit;
     $lescore = mysql_query("UPDATE scorm_util_module$Ext SET `raw` = '".$lanote."'
                 WHERE `user_module_no` = '".$id_user."'
                 AND `mod_module_no` ='".$_POST['scormid']."' AND
                 `mod_grp_no` = '$numero_groupe'");
  }
//  echo "<BR>$sql";
     $date = date("Y-m-d H:i:s" ,time());
     list($dtj,$hfin) = explode(" ",$date);
     $requete=mysql_query("select traq_cdn from traque where traq_mod_no='".$_POST['scormid']."' and
                     traq_util_no = '$id_user' AND traq_grp_no='$numero_groupe' and
                     traq_hf_dt = '00:00:00' order by traq_cdn desc");
     $nbr = mysql_num_rows($requete);
     if ($nbr > 0)
     {
        $num_act = mysql_result($requete,0,"traq_cdn");
        $req = mysql_query ("UPDATE traque set traq_hf_dt = \"$hfin\" where traq_cdn = $num_act AND
                            traq_grp_no='$numero_groupe' and traq_util_no = '$id_user'");
     }
  $ordrepere = GetDataField ($connect,"SELECT mod_pere_lb
                               FROM scorm_module
                               WHERE mod_cdn = ".$_POST['scormid'],"mod_pere_lb");
  $req_seq = requete_order("mod_cdn","scorm_module"," mod_pere_lb = \"$ordrepere\"","mod_cdn ASC");
  if ($req_seq == TRUE)
  {
     $scopere = GetDataField ($connect,"SELECT mod_cdn
                               FROM scorm_module
                               WHERE mod_numero_lb = \"$ordrepere\" AND mod_content_type_lb = 'LABEL'","mod_cdn");
     if ($scopere > 0)
        $lesson_time = GetDataField ($connect,"select total_time from scorm_util_module$Ext where
                                            mod_module_no = '$scopere' AND
                                            user_module_no='$id_user' AND
                                            mod_grp_no = '$numero_groupe'","total_time");
     else
        $lesson_time = GetDataField ($connect,"select total_time from scorm_util_module$Ext where
                                            mod_module_no = '".$_POST['scormid']."' AND
                                            user_module_no='$id_user' AND
                                            mod_grp_no = '$numero_groupe'","total_time");
     $req_seq = requete("mod_cdn","scorm_module"," mod_pere_lb = \"$ordrepere\"");
     $time1 = $lesson_time;
     $time2 = $_POST['session_time'];
     if (isset($time2) && isset($time1))
        $lesson_time_value = scorm_add_time($time1, $time2);
     else
        $lesson_time_value = "0000:00:00.00";
     $session_time = scorm_modifie_time($time2);
     $sql1 = "UPDATE scorm_util_module$Ext SET
                `total_time` = '".$lesson_time_value."',
                `session_time` = '".$session_time."' WHERE
                `user_module_no` = '".$id_user."'
                 AND `mod_module_no`='$scopere' AND
                 `mod_grp_no` = '$numero_groupe'";
     $requete = mysql_query($sql1);
     $i=0;$j=0;
     while ($item = mysql_fetch_object($req_seq))
     {
        $i++;
        $scoid = $item->mod_cdn;
        $scoOk = GetDataField ($connect,"SELECT lesson_status
                               FROM scorm_util_module$Ext
                               WHERE mod_module_no = '$scoid' AND
                                    user_module_no = '$id_user' AND
                                    mod_grp_no = '$numero_groupe'","lesson_status");
        if ($scoOk == "COMPLETED" || $scoOk == "PASSED" || $scoOk == "BROWSED")
           $j++;
     }
     if ($j == $i)
     {
        $sql2 = "UPDATE scorm_util_module$Ext SET
                `lesson_status` = 'COMPLETED',
                `entry` = 'RESUME',
                `credit` = 'CREDIT'
               WHERE `user_module_no` = '$id_user'
                 AND `mod_module_no` ='$scopere'
                 AND mod_grp_no = '$numero_groupe'";
        $requete = mysql_query($sql2);
     }
  }
}
if (isset($_POST['id_util']) && $_POST['id_util'] > 0)
{
    echo "<SCRIPT Language=\"Javascript\">";
          echo "parent.opener.location.reload();";
    echo "</SCRIPT>";
    echo "<script type='text/javascript'>".
             "parent.frames['index_contenu'].location.href=\"index_contenu.php?id_parc=$id_parc&id_seq=$id_seq&scormid=$scormid&cours=1&numero_groupe=$numero_groupe&aicc_sid=$aicc_sid\"".
         " </script>";
    unset($_POST);
}
?>
<HTML>
<HEAD>
<META HTTP-EQUIV="Content-Type" content="text/html; charset=iso-8859-1">
<META HTTP-EQUIV="Content-Language" CONTENT="FR-fr">
<META HTTP-EQUIV="Expires" CONTENT="Fri, Jan 01 1900 00:00:00 GMT">
<META NAME="ROBOTS" CONTENT="No Follow">
<META HTTP-EQUIV="Cache-Control" CONTENT="no-cache, must-revalidate">
<META HTTP-EQUIV="Pragma" CONTENT="no-cache">
<TITLE>Formagri</TITLE>
</HEAD>
<BODY>
   <form name="cmiForm" method="POST" action="<?php echo $_SERVER["PHP_SELF"] ?>">
        <input type="hidden" name="id_util" />
        <input type="hidden" name="id_seq" />
        <input type="hidden" name="scormid" />
        <input type="hidden" name="lesson_status" />
        <input type="hidden" name="numero_groupe" />
        <input type="hidden" name="lesson_mode" />
        <input type="hidden" name="lesson_location" />
        <input type="hidden" name="credit" />
        <input type="hidden" name="entry" />
        <input type="hidden" name="raw" />
        <input type="hidden" name="total_time" />
        <input type="hidden" name="session_time" />
        <input type="hidden" name="suspend_data" />
        <input type="hidden" name="comments" />
        <input type="hidden" name="comments_from_lms" />
        <input type="hidden" name="scoreMin" />
        <input type="hidden" name="scoreMax" />
        <input type="hidden" name="chaine_inter" />
        <input type="hidden" name="inter_nb" />
        <input type="hidden" name="objectives_nb" />
        <input type="hidden" name="chaine_objectives" />
   </form>
</BODY></HTML>