<?php
  if (!isset($_SESSION)) session_start();
  require 'param.php';
  require '../admin.inc.php';
  require '../fonction.inc.php';
  dbconnect();
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
<BODY>
<?php
  $query="SELECT * FROM ".$param["table_msg"]." WHERE user != '' ORDER By dt ASC";
  $result=mysql_query($query);
  while ($row=mysql_fetch_object($result)) {
    if (!strstr($row->text,$param["join_us"]))
      print($row->text."<BR>");
  }
?>
</BODY></HTML>
