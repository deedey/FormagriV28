<?php
if (!isset($_SESSION)) session_start();
if (!isset($_SESSION['id_user']) || $_SESSION['id_user'] == "")
{
  exit();
}
include ("../include/UrlParam2PhpVar.inc.php");
require ("../admin.inc.php");
require ("../fonction.inc.php");
dbConnect();
if (isset($numero_groupe))
   $Ext = '_'.$numero_groupe;
if (isset($_POST['command']) && isset($_POST['session_id'])) {
   $commande = strtolower($_POST['command']);
   $aiccdata = $_POST['aicc_data'];
   $donnees_session = explode("|",$_POST['session_id']);
   $scormid = $donnees_session[1];
   $id_seq = $donnees_session[2];
   $id_parc = $donnees_session[3];
   $v_insert = "UPDATE scorm_util_module$Ext ";
   $w_nsert = " WHERE mod_module_no = '$scormid' AND user_module_no = '$id_user' AND mod_grp_no = '$numero_groupe'";
   if (isset($commande) && strtoupper($commande) == strtoupper("getparam"))
   {
      $sco = array();
      if ($typ_user == "APPRENANT"){
        $sql = "SELECT * FROM scorm_util_module$Ext, scorm_module ,utilisateur,prescription$Ext WHERE ".
                 "user_module_no = '$id_user' AND  presc_parc_no = '$id_parc' AND ".
                 "util_cdn='$id_user' AND presc_utilisateur_no ='$id_user' AND presc_seq_no = '$id_seq' AND ".
                 " mod_cdn = '$scormid' AND `mod_module_no` = '$scormid' AND `mod_seq_no` = '$id_seq'";
/*
$datasave = "<pre>\r\n";
$datasave .= $sql."\r\n";
$datasave .= "</pre>\r\n\r\n";
$fp = fopen("commande.txt", "a+");
$fw = fwrite($fp, $datasave);
fclose($fp);
*/
        $query = mysql_query($sql);
        $sco = mysql_fetch_array($query);
        $nom_app = $sco['util_nom_lb'].", ".$sco['util_prenom_lb'];
        $raw = ($sco['raw'] == -1) ? "" : "".$sco['raw'];
        $scoreMin = ($sco['scoreMin'] == -1) ? "" : "".$sco['scoreMin'];
        $scoreMax = ($sco['scoreMax'] == -1) ? "" : "".$sco['scoreMax'];
        $data = "error = 0\nerror_text = Successful\nversion = ".$_POST['version']."\naicc_data=\n";
        $data .= "[Core]\n";
        $data .= 'Student_ID = '.$sco['util_cdn']."\n";
        $data .= 'Student_Name = '.$nom_app."\n";
        if (isset($sco['lesson_location']))
           $data .= 'Lesson_Location = '.strtolower($sco['lesson_location'])."\n";
        else
           $data .= 'Lesson_Location = '."\n";
        $data .= 'Credit = '.strtolower($sco['credit'])."\n";
        if ($sco['lesson_status'] == '')
           $sco['entry'] = ', ab-initio';
        else
        {
           if (isset($sco['exit']) && strtolower($sco['exit']) == 'suspend')
                 $sco['entry']  = ', resume';
           else
                 $sco['entry'] = '';
        }
        if (isset($sco['lesson_status']))
           $data .= 'Lesson_Status = '.strtolower($sco['lesson_status']).strtolower($sco['entry'])."\n";
        else
           $data .= 'Lesson_Status = not attempted'.strtolower($sco['entry'])."\n";
        if (isset($sco['raw']))
        {
           $max = '';
           $min = '';
           if (isset($sco['scoreMax']) && !empty($sco['scoreMax']))
           {
              $max = ', '.$sco['scoreMax'];
              if (isset($sco['scoreMin']) && !empty($sco['scoreMin']))
                 $min = ', '.$sco['scoreMin'];
           }
           $data .= 'Score = '.$sco['raw'].$max.$min."\n";
        }
        else
           $data .= 'Score = '."\n";
        if (trim($sco['total_time']) != "0000:00:00.0")
           $data .= 'Time = '.substr(substr(trim($sco['total_time']),2),0,8)."\n";
        else
           $data .= 'Time = '.'00:00:00'."\n";
        $data .= 'Lesson_Mode = '.strtolower($sco['lesson_mode'])."\n";
        if (isset($sco['suspend_data']))
           $data .= "[Core_Lesson]\n".$sco['suspend_data']."\n";
        else
           $data .= "[Core_Lesson]\n"."\n";
        $data .= "[Core_Vendor]\n". $sco['mod_datafromlms']."\n";
        $data .= "[Evaluation]\nCourse_ID = ".$scormid."\n";
        $data .= "[Student_Data]\n";
        $data .= 'Mastery_Score = '.$sco['mod_masteryscore']."\n";
        if ($sco['mod_maxtimeallowed'] != '' && strlen($sco['mod_maxtimeallowed']) == 13)
            $maxtimeallowed = substr(substr(trim($sco['mod_maxtimeallowed']),2),0,8);
        else
            $maxtimeallowed = "";
        $data .= 'Max_Time_Allowed = '.$maxtimeallowed."\n";
        $data .= 'Time_Limit_Action = '.$sco['mod_timelimitaction']."\n";
        if ($sco['comments'] != '')
           $data .= "[Comments]\n".$sco['comments']."\n";
        else
           $data .= "[Comments]\n";
        header("Content-type: text/plain");
        header("Content-Disposition: inline; filename=data_aicc.txt");
        echo $data;
     }
   }
   if (isset($commande) && strtoupper($commande) == strtoupper("putparam"))
   {
      if (!empty($aiccdata))
      {
//$datasave = "<pre>\r\n";
//$datasave .= $aiccdata."\r\n";
//$datasave .= "</pre>\r\n\r\n";
//$fp = fopen("commande.txt", "a+");
//$fw = fwrite($fp, $datasave);
//fclose($fp);
         $le_mode = GetDataField ($connect,"SELECT lesson_mode from scorm_util_module$Ext WHERE mod_module_no = $scormid AND user_module_no = $id_user","lesson_mode");
         $le_statut = GetDataField ($connect,"SELECT lesson_status from scorm_util_module$Ext WHERE mod_module_no = $scormid AND user_module_no = $id_user","lesson_status");
         $masteryscore = GetDataField ($connect,"SELECT mod_masteryscore from scorm_module WHERE mod_cdn = $scormid","mod_masteryscore");
         $score = '';
         $ligne = explode("\n",$aiccdata);
         $nb_rows = count($ligne);
         for ($i=0;$i<$nb_rows;$i++)
         {
             if (strstr(strtolower($ligne[$i]),"lesson_status"))
             {
                $element = explode("=",trim($ligne[$i]));
                $double = 0;
                if (strstr($element[1],","))
                {
                   $subelement = explode(",",$element[1]);
                   $element1 = $subelement[0];
                   $double = 1;
                }
                else
                   $element1 = $element[1];
                if (strtolower($element1) == "p" || strtoupper($element1) == "PASSED") $insert_element = "PASSED";
                if (strtolower($element1) == "c" || strtoupper($element1) == "COMPLETED") $insert_element ="COMPLETED";
                if (strtolower($element1) == "f" || strtoupper($element1) == "FAILED") $insert_element = "FAILED";
                if (strtolower($element1) == "i" || strtoupper($element1) == "INCOMPLETE") $insert_element = "INCOMPLETE";
                if (strtolower($element1) == "b" || strtoupper($element1) == "BROWSED") $insert_element = "BROWSED";
                if (strtolower($element1) == "n" || strtoupper($element1) == "NOT ATTEMPTED") $insert_element = "NOT ATTEMPTED";
                $sql = "$v_insert "."SET lesson_status = \"".$insert_element."\" $w_nsert";
                $insere = mysql_query($sql);
                if ($insert_element != '' && $insert_element != "NOT ATTEMPTED")
                {
                   $entry = "RESUME";
                   $insere = mysql_query("$v_insert "."SET entry = '$entry' $w_nsert");
                   $sco_min = GetDataField ($connect,"SELECT scoreMin from scorm_util_module$Ext WHERE mod_module_no = $scormid AND user_module_no = $id_user","scoreMin");
                   if ($sco_min == -1)
                      $insere = mysql_query("$v_insert "."SET scoreMin = '0' $w_nsert");
                   $sco_max = GetDataField ($connect,"SELECT scoreMax from scorm_util_module$Ext WHERE mod_module_no = $scormid AND user_module_no = $id_user","scoreMax");
                   if ($sco_max == -1)
                      $insere = mysql_query("$v_insert "."SET scoreMax = '0' $w_nsert");
                }
                if (isset($double) && $double == 1)
                {
                   if (strtolower($subelement[1]) == "l" || strtoupper($element1) == "LOGOUT") $insert_element = "LOGOUT";
                   if (strtolower($subelement[1]) == "t" || strtoupper($element1) == "TIME_OUT") $insert_element = "TIME_OUT";
                   if (strtolower($subelement[1]) == "s" || strtoupper($element1) == "SUSPEND") $insert_element = "SUSPEND";
                   $sql = "$v_insert "."SET exit = \"".$insert_element."\" $w_nsert";
                $insere = mysql_query($sql);
                }
             }
             if (strstr(strtolower($ligne[$i]),"lesson_location"))
             {
                $element = explode("=",trim($ligne[$i]));
                $insert_element = $element[1];
                $sql = "$v_insert "."SET lesson_location = \"$insert_element\" $w_nsert";
                $insere = mysql_query($sql);
             }
              if (strstr(strtolower($ligne[$i]),"time"))
              {
                $element = explode("=",trim($ligne[$i]));
                if (strlen($element[1]) == 8)
                   $time2 = scorm_modifie_time($element[1]);
                else
                   $time2 = $element[1];
                $time1 = GetDataField ($connect,"SELECT total_time from scorm_util_module$Ext WHERE mod_module_no = '$scormid' AND user_module_no = '$id_user'","total_time");
                $total_time = scorm_add_time($time1, $time2);
                $sql = "$v_insert "."SET total_time = \"$total_time\" $w_nsert";
                $insere = mysql_query($sql);
                $sql = "$v_insert "."SET session_time = \"$time2\" $w_nsert";
                $insere = mysql_query($sql);
             }
             if (strstr(strtolower($ligne[$i]),"[core_lesson]"))
             {
                if (strstr(strtolower($ligne[$i+1]),"-"))
                {
                   $element = explode("-",trim($ligne[$i+1]));
                   $insert_element = $element[1];
                }
                else
                   $insert_element = trim($ligne[$i+1]);
                $sql = "$v_insert "."SET suspend_data = \"$insert_element\" $w_nsert";
                $insere = mysql_query($sql);
             }
             if (strstr(strtolower($ligne[$i]),"score"))
             {
                $element = explode("=",trim($ligne[$i]));
                if (strstr(strtolower($ligne[$i]),","))
                {
                   $subelement = explode(",",trim($element[1]));
                   $insert_element = $subelement[0];
                   $sql = "$v_insert "."SET scoreMin = \"".$subelement[1]."\" $w_nsert";
                   $sql = "$v_insert "."SET scoreMax = \"".$subelement[2]."\" $w_nsert";
                }
                else
                   $insert_element = $element[1];
                $sql = "$v_insert "."SET raw = \"$insert_element\" $w_nsert";
                $insere = mysql_query($sql);
             }
         }
         //fin for
         if (strtolower($le_mode) == 'browse' && strtolower($le_status) == 'not attempted')
            $lessonstatus = 'BROWSED';
         if (strtolower($le_mode) == 'normal')
         {
            if ($lessonstatus == 'COMPLETED')
            {
               if (!empty($masteryscore) && !empty($score) && $score >= $masteryscore)
                  $lessonstatus = 'PASSED';
               else
                  $lessonstatus = 'FAILED';
               $entry = "RESUME";
               $insere = mysql_query("$v_insert "."SET lesson_status = '$lessonstatus' $w_nsert");
               $insere = mysql_query("$v_insert "."SET entry = '$entry' $w_nsert");
               $sco_min = GetDataField ($connect,"SELECT scoreMin from scorm_util_module$Ext WHERE mod_module_no = $scormid AND user_module_no = $id_user","scoreMin");
               if ($sco_min == -1)
                  $insere = mysql_query("$v_insert "."SET scoreMin = '0' $w_nsert");
               $sco_max = GetDataField ($connect,"SELECT scoreMax from scorm_util_module$Ext WHERE mod_module_no = $scormid AND user_module_no = $id_user","scoreMax");
               if ($sco_max == -1)
                      $insere = mysql_query("$v_insert "."SET scoreMax = '0' $w_nsert");
            }
         }
      }// fin if empty
   }//fin $commande
/*
   if (strtoupper($commande) == strtoupper("putinteractions")){
         $aicc_data = trim(urldecode($aiccdata));

         $ligne = explode("\n",$aicc_data);
         $nb_rows = count($ligne);
         for ($i=0;$i<$nb_rows;$i++){
                $entete .= $ligne[$i];
         }
         $datasave = "<pre>\r\n";
         $datasave .= $entete."\r\n";
         $datasave .= "</pre>\r\n\r\n";
         $fp = fopen("commande.txt", "a+");
         $fw = fwrite($fp, $datasave);
         fclose($fp);
   }
*/

   if (strtoupper($commande) == strtoupper("exitau"))
   {
        $agent=$_SERVER["HTTP_USER_AGENT"];
        if (strstr($agent,"MSIE"))
        {
           echo "<SCRIPT Language=\"Javascript\">";
               echo "parent.opener.DoTheRefresh();";
           echo "</SCRIPT>";
        }
        else
        {
           echo "<SCRIPT Language=\"Javascript\">";
               echo "parent.opener.DoTheRefresh();";
           echo "</SCRIPT>";
        }
        ?><script type="text/javascript">
          <!--//
             parent.index_contenu.location.href="<?php echo "index_contenu.php?id_seq=$id_seq&id_parc=$id_parc&scormid=$scormid&cours=1&aicc_sid=$aicc_sid"; ?>";
          //-->
          </script>

         <SCRIPT LANGUAGE="JavaScript">
             setTimeout("Quit()",500);
             function Quit()
             {
                top.close();
             }
           </SCRIPT>
         <?php
   }
}
?>