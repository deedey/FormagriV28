<html>
<head>
<SCRIPT language=javascript>
   setTimeout("Quit()",1500);
   function Quit() {
     self.opener=null;self.close();return false;
   }
   </SCRIPT>
</body>
<title>Redirection</title>
</head>
<?php
include ("include/UrlParam2PhpVar.inc.php");
echo "<body onload=\"window.open('chat/index.php?user=$user&pass=$pass','','scrollbars,resizable=yes,width=550,height=305')\">";
?>
</body>
</html>