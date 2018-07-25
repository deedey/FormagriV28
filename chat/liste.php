<?php
  if (!isset($_SESSION)) session_start();
  include "param.php";
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
include "param.php";
?>
<HTML>
<HEAD>
<META HTTP-EQUIV="Content-Type" content="text/html; charset=<?php echo $charset;?>">
<META HTTP-EQUIV="Content-Language" CONTENT="<?php echo $code_langage ;?>">
<META HTTP-EQUIV="Expires" CONTENT="Fri, Jan 01 1900 00:00:00 GMT">
<META NAME="ROBOTS" CONTENT="No Follow">
<META HTTP-EQUIV="Cache-Control" CONTENT="no-cache, must-revalidate">
<META HTTP-EQUIV="Pragma" CONTENT="no-cache">
<TITLE><?php print($param["title"]); ?></TITLE>
<link REL='StyleSheet' TYPE='text/css' HREF='chat.css'>
</HEAD>
<BODY background="../images/fondtitre.jpg" onLoad='top.load_liste=1'>
<DIV id="layerliste">        <?php print($param["text_connecting"]); ?></DIV>
</BODY></HTML>
