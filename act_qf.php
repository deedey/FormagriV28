<?php
if (!isset($_SESSION)) session_start();
include ("include/UrlParam2PhpVar.inc.php");
if (!isset($_SESSION['id_user']) || $_SESSION['id_user'] == "")
{
  exit();
}
require ("admin.inc.php");
require ("fonction.inc.php");
require ("lang$lg.inc.php");
dbconnect();
echo "<HTML>";
echo "<BODY marginwidth='0' marginheight='0' leftmargin='0' topmargin='0'>";
$date_op = date("Y-m-d H:i:s");
$date = date("Y/m/d H:i:s" ,time());
list($dtj,$hdeb) = explode(" ",$date);
$aza = str_replace("undefined","",$aza);
if ($lg == "fr")
{
  $aza = str_replace("√©","È",$aza);
  $aza = str_replace("√®","Ë",$aza);
  $aza = str_replace("√ß","Á",$aza);
  $aza = str_replace("√¢","‚",$aza);
  $aza = str_replace("√π","˘",$aza);
  $aza = str_replace("√Æ","Ó",$aza);
  $aza = str_replace("√¥","Ù",$aza);
  $aza = str_replace("√","‡",$aza);
}
if ($lg == "ru")
{
  $aza = str_replace("–π","È",$aza);
  $aza = str_replace("—Ü","ˆ",$aza);
  $aza = str_replace("—É","Û",$aza);
  $aza = str_replace("–∫","Í",$aza);
  $aza = str_replace("–µ","Â",$aza);
  $aza = str_replace("–Ω","Ì",$aza);
  $aza = str_replace("–≥","„",$aza);
  $aza = str_replace("—à","¯",$aza);
  $aza = str_replace("—â","˘",$aza);
  $aza = str_replace("–∑","Á",$aza);
  $aza = str_replace("—Ö","ı",$aza);
  $aza = str_replace("—ä","˙",$aza);
  $aza = str_replace("—Ñ","Ù",$aza);
  $aza = str_replace("—ã","˚",$aza);
  $aza = str_replace("–≤","‚",$aza);
  $aza = str_replace("–∞","‡",$aza);
  $aza = str_replace("–ø","Ô",$aza);
  $aza = str_replace("—Ä","",$aza);
  $aza = str_replace("–æ","Ó",$aza);
  $aza = str_replace("–ª","Î",$aza);
  $aza = str_replace("–¥","‰",$aza);
  $aza = str_replace("–∂","Ê",$aza);
  $aza = str_replace("—ç","˝",$aza);
  $aza = str_replace("—è","ˇ",$aza);
  $aza = str_replace("—á","˜",$aza);
  $aza = str_replace("—Å","Ò",$aza);
  $aza = str_replace("–º","Ï",$aza);
  $aza = str_replace("–∏","Ë",$aza);
  $aza = str_replace("—Ç","Ú",$aza);
  $aza = str_replace("—å","¸",$aza);
  $aza = str_replace("–±","·",$aza);
  $aza = str_replace("—é","˛",$aza);
  $aza = str_replace("–ô","…",$aza);
  $aza = str_replace("–¶","÷",$aza);
  $aza = str_replace("–£","”",$aza);
  $aza = str_replace("–ë"," ",$aza);
  $aza = str_replace("–ï","≈",$aza);
  $aza = str_replace("–ù","Õ",$aza);
  $aza = str_replace("–ì","√",$aza);
  $aza = str_replace("–®","ÿ",$aza);
  $aza = str_replace("–©","Ÿ",$aza);
  $aza = str_replace("–ó","«",$aza);
  $aza = str_replace("–•","’",$aza);
  $aza = str_replace("–™","⁄",$aza);
  $aza = str_replace("–§","‘",$aza);
  $aza = str_replace("–´","€",$aza);
  $aza = str_replace("–í","¬",$aza);
  $aza = str_replace("–ê","¿",$aza);
  $aza = str_replace("–ü","œ",$aza);
  $aza = str_replace("– ","–",$aza);
  $aza = str_replace("–û","Œ",$aza);
  $aza = str_replace("–õ","À",$aza);
  $aza = str_replace("–î","ƒ",$aza);
  $aza = str_replace("–ñ","∆",$aza);
  $aza = str_replace("–≠","›",$aza);
  $aza = str_replace("–Ø","ﬂ",$aza);
  $aza = str_replace("–ß","◊",$aza);
  $aza = str_replace("–°","—",$aza);
  $aza = str_replace("–ú","Ã",$aza);
  $aza = str_replace("–ò","»",$aza);
  $aza = str_replace("–¢","“",$aza);
  $aza = str_replace("–¨","‹",$aza);
  $aza = str_replace("–ë","¡",$aza);
  $aza = str_replace("–Æ","ﬁ",$aza);
}
$aza = str_replace("Number of questions",$qf_nq,$aza);
$aza = str_replace("Right Answers",$qf_ra,$aza);
$aza = str_replace("Wrong Answers",$qf_was,$aza);
$aza = str_replace("Wrong answer",$qf_wa,$aza);
$aza = str_replace("Unmarked questions",$qf_uqs,$aza);
$aza = str_replace("Question not valuated",$qf_uq,$aza);
$aza = str_replace("Time elapsed",$qf_tps,$aza);
$aza = str_replace("Date",$qf_dat,$aza);
$aza = str_replace("MARK",$qf_not,$aza);
$aza = str_replace("Correct answer",$qf_cr,$aza);
$aza = str_replace("Right sentences",$qf_rs,$aza);
$aza = str_replace("Guess words",$qf_gw,$aza);
$aza = str_replace(" on ",$qf_on,$aza);
$aza = str_replace("Matching",$qf_mt,$aza);
$complement= "<CENTER><TABLE bgColor='#298CA0' cellspacing='2'><TR><TD>\n".
             "<TABLE bgColor='#FFFFFF' cellspacing='2' width='100%'>\n".
             "<TR><TD background=\"/images/fond_titre_table.jpg\" colspan='2' height='34' align='center' valign='center'>\n<Font size='3' color='#FFFFFF'><B>$results</B></FONT>\n".
             "</TD></TR><TR><TD>";
$entete = "<HTML>\n<HEAD>\n<TITLE>$msq_activite</TITLE>\n".
          "<STYLE>\n".
          "BODY {font-family:verdana;font-size:11px;color: #000066}\n".
          "TD {font-family:verdana;font-size:12px;color: #000066}\n".
          "</STYLE>\n".
          "</HEAD><BODY bgcolor=\"#002D44\">";
$one = "";
$two = "";
$fin_page = "</BODY></HTML>";
$complement1 = "</TD></TR></TABLE></TD></TR></TABLE>";
$aza=$entete.$complement.$one.$two.stripslashes($aza).$complement1.$fin_page;
/*
echo "<pre>";
     print_r($_POST);print_r($_GET);
echo "</pre>";
*/
if ($typ_user == "APPRENANT")
{
  $Ext='_'.$_SESSION['numero_groupe'];
  $suivi_etat= GetDataField ($connect,"select suivi_etat_lb from suivi1$Ext where suivi_act_no= $id_act and suivi_utilisateur_no=$id_user and suivi_grp_no=$numero_groupe","suivi_etat_lb");
  $suivi_comment= GetDataField ($connect,"select suivi_commentaire_cmt from suivi1$Ext where suivi_act_no= $id_act and suivi_utilisateur_no=$id_user and suivi_grp_no=$numero_groupe","suivi_commentaire_cmt");
  $suivi_fichier= GetDataField ($connect,"select suivi_fichier_lb from suivi1$Ext where suivi_act_no= $id_act and suivi_utilisateur_no=$id_user and suivi_grp_no=$numero_groupe","suivi_fichier_lb");
  $suivi_note= GetDataField ($connect,"select suivi_note_nb1 from suivi1$Ext where suivi_act_no= $id_act and suivi_utilisateur_no=$id_user and suivi_grp_no=$numero_groupe","suivi_note_nb1");
  $id_ress = GetDataField ($connect,"select act_ress_no from activite where act_cdn = $id_act","act_ress_no");
  $act_valid = GetDataField ($connect,"select act_acquittement_lb from activite where act_cdn = $id_act","act_acquittement_lb");
  $ress = GetDataField ($connect,"select ress_url_lb from ressource_new where ress_cdn = $id_ress","ress_url_lb");
  $fichier="qf_".$id_ress."_";
  $fichier .= substr(strrchr($ress, "/"), 1);
  $dir_app="ressources/".$login."_".$id_user."/devoirs/".$fichier;
  $fp = fopen($dir_app, "w");
  $fw = fwrite($fp, $aza);
  fclose($fp);
  $change_traq = mysql_query("update suivi1$Ext set suivi_fichier_lb='$fichier',suivi_note_nb1 = '$note' where suivi_act_no=$id_act and suivi_utilisateur_no=$id_user and suivi_grp_no=$numero_groupe");
  if ($suivi_comment != "ActivitÈ repassÈe" && $suivi_etat == "A FAIRE")
    $change_traq = mysql_query("update suivi1$Ext set suivi_date_debut_dt='$date_op' where suivi_act_no=$id_act and suivi_utilisateur_no=$id_user and suivi_grp_no=$numero_groupe");
  elseif (($suivi_etat == "A FAIRE" && $suivi_comment == "ActivitÈ repassÈe") || ($suivi_etat != "A FAIRE" && strstr($fichier,"qf_") && $act_valid == "APPRENANT"))
    $change_traq = mysql_query("update suivi1$Ext set suivi_date_fin_dt='$date_op',suivi_commentaire_cmt='ActivitÈ repassÈe' where suivi_act_no=$id_act and suivi_utilisateur_no=$id_user and suivi_grp_no=$numero_groupe");
}
?>
<SCRIPT language=javascript>
    setTimeout("Quit()",1500);
    function Quit() {
      self.opener=null;self.close();return false;
    }
</SCRIPT>
</BODY></HTML>