<?php
if (!isset($_SESSION)) session_start();
include ("../include/UrlParam2PhpVar.inc.php");
if ($lg == "ru"){
  $code_langage = "ru";
  $charset = "Windows-1251";
  putenv("TZ=Europe/Moscow");
}elseif ($lg == "fr"){
  $code_langage = "fr";
  $charset = "iso-8859-1";
  putenv("TZ=Europe/Paris");
}elseif ($lg == "en"){
  $code_langage = "en";
  $charset = "iso-8859-1";
}

?>
<HTML>
<HEAD>
<META HTTP-EQUIV="Content-Type" content="text/html; charset=<?php echo $charset;?>">
<META HTTP-EQUIV="Content-Language" CONTENT="<?php echo $code_langage ;?>">
<META HTTP-EQUIV="Expires" CONTENT="Fri, Jan 01 1900 00:00:00 GMT">
<META NAME="ROBOTS" CONTENT="No Follow">
<META HTTP-EQUIV="Cache-Control" CONTENT="no-cache, must-revalidate">
<META HTTP-EQUIV="Pragma" CONTENT="no-cache">
<link REL='StyleSheet' TYPE='text/css' HREF='chat.css'>
<TITLE><?php print($param["title"]); ?></TITLE>
</HEAD>
<BODY background="../images/fondtitre.jpg" onLoad='top.load_coeur=1'>
<?php
  include "mysql.php";
  include "f_chat.php";
  Connectix();
  GLOBAL $nom_user,$prenom_user;
        $Z="";
        // PAGE PRINCIPALE DU COEUR DE GESTION
        if (!isset($first)) {
          if (isset($msg)) { // Ajout d'un message dans la liste des messages
            if (P4_Add_Msg($msg,$color,$user,"")==0) { // Post par un user deconnecté
              exit;
            }
          }
          if (isset($dt_last_liste)) { // Gestion de la liste des users
            $util= $nom_user." ".$prenom_user;
            $Z.=P4_Coeur_Liste($dt_last_liste,$user);
          }
          $Z.=P4_Coeur_Msg($dt_last_chat);
        }
        print("<SCRIPT language=javascript>".$Z."\ntop.CoeurLoad()</SCRIPT>");

?>
</BODY></HTML>
