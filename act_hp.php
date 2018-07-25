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
/*
echo "<pre>";
print_r($_SESSION);
echo "</pre>";
exit;
*/
if ($typ_user == "APPRENANT")
{
  $Ext='_'.$numero_groupe;
  $date_op = date("Y-m-d H:i:s");
  $date = date("Y/m/d H:i:s" ,time());
  list($dtj,$hdeb) = explode(" ",$date);
  $suivi_etat= GetDataField ($connect,"select suivi_etat_lb from suivi1$Ext where suivi_act_no= $id_act and suivi_utilisateur_no=$id_user and suivi_grp_no=$numero_groupe","suivi_etat_lb");
  $suivi_comment= GetDataField ($connect,"select suivi_commentaire_cmt from suivi1$Ext where suivi_act_no= $id_act and suivi_utilisateur_no=$id_user and suivi_grp_no=$numero_groupe","suivi_commentaire_cmt");
  $suivi_fichier= GetDataField ($connect,"select suivi_fichier_lb from suivi1$Ext where suivi_act_no= $id_act and suivi_utilisateur_no=$id_user and suivi_grp_no=$numero_groupe","suivi_fichier_lb");
  $suivi_note= GetDataField ($connect,"select suivi_note_nb1 from suivi1$Ext where suivi_act_no= $id_act and suivi_utilisateur_no=$id_user and suivi_grp_no=$numero_groupe","suivi_note_nb1");
  $note .="%";
  $change_traq = mysql_query("update suivi1$Ext set suivi_fichier_lb='$tip' where suivi_act_no=$id_act and suivi_utilisateur_no=$id_user and suivi_grp_no=$numero_groupe");
  if ($suivi_comment != "Activité repassée" && $suivi_etat == "A FAIRE")
     $change_traq = mysql_query("update suivi1$Ext set suivi_date_debut_dt='$date_op' where suivi_act_no=$id_act and suivi_utilisateur_no=$id_user and suivi_grp_no=$numero_groupe");
  elseif ($suivi_etat == "A FAIRE" && $suivi_comment == "Activité repassée")
     $change_traq = mysql_query("update suivi1$Ext set suivi_date_fin_dt='$date_op' where suivi_act_no=$id_act and suivi_utilisateur_no=$id_user and suivi_grp_no=$numero_groupe");
  $change_note = mysql_query("update suivi1$Ext set suivi_note_nb1='$note' where suivi_act_no=$id_act and suivi_utilisateur_no=$id_user and suivi_grp_no=$numero_groupe");
}
?>
<SCRIPT language=javascript>
  setTimeout("Quit()",1500);
  function Quit() {
   self.opener=null;self.close();return false;
  }
</SCRIPT>
