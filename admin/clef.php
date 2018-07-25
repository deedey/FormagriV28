<?php
if (!isset($_SESSION)) session_start();
if (!isset($_SESSION['id_user']) || $_SESSION['id_user'] == "")
{
  exit();
}
header("Content-type  : text/plain");
header("Cache-Control: no-cache, max-age=0");
include ('../include/UrlParam2PhpVar.inc.php');
require '../admin.inc.php';
require '../fonction.inc.php';
require "../lang$lg.inc.php";
require '../fonction_html.inc.php';
require '../class/class_admin.php';
dbConnect();
$etat_messInsc = GetDataField($connect,"select param_etat_lb from param_foad where param_typ_lb='mess_inscription'","param_etat_lb");
if ($etat_messInsc == 'OUI')
{
       $reqMess = mysql_query("SELECT * from message_inscription");
       if (mysql_num_rows($reqMess) > 0)
       {
          $mess_insc_mess1 = mysql_result($reqMess,0,'mi_text_cmt');
          $mess_insc_mess2 = mysql_result($reqMess,1,'mi_text_cmt');
          $mess_insc_mess3 = mysql_result($reqMess,2,'mi_text_cmt');
          $mess_insc_mess6 = mysql_result($reqMess,3,'mi_text_cmt');
          $mess_insc_mess4 = mysql_result($reqMess,4,'mi_text_cmt');
       }

}
$req_clef = requete("*","utilisateur","util_cdn='$num'");
while ($item = mysql_fetch_object($req_clef)) {
        $nom_cible = $item-> util_nom_lb;
        $prenom_cible = $item->util_prenom_lb;
        $email_cible = $item->util_email_lb;
        $type_cible = $item->util_typutil_lb;
        $logue = $item->util_login_lb;
        $logue_cas = $item->util_logincas_lb;
        $passe = $item->util_motpasse_lb;
}
   $email_user = GetdataField ($connect,"select util_email_lb from utilisateur where util_cdn='$id_user'","util_email_lb");
   $message= " $prenom_cible $nom_cible $mess_insc_mess1<br />$mess_insc_mess2 $logue<br />$mess_insc_mess3 $passe<br />$mess_login_cas : $logue_cas<br>$mess_insc_mess6 : $adresse_http<br>".ucfirst($vous_etes)." ".ucfirst(strtolower($type_cible))."<br />$mess_ag_cordial<br />";
   $from = $email_user;
   $reply = $email_user;
   $adr_mail = $from;
   $sendto = $email_cible;
   $subject = StripSlashes($mess_auth_serv_forma);
   $msg = StripSlashes($message);
   $origine = $nom_user."  ".$typ_user;
   $nom = "";
   $userfile = "none";
   $envoi = mail_attachement($sendto , $subject , html_entity_decode($msg,ENT_QUOTES,'ISO-8859-1') , $userfile , $reply, $nom, $from);
   $afficher = $mess_insc_mess_aff." ".$nom_cible." ".$prenom_cible ;
   sleep(1);
   echo  utf2Charset($afficher,$charset);
?>
