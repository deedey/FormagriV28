<?php
if (!isset($_SESSION)) session_start();
if (!isset($_SESSION['id_user']) || $_SESSION['id_user'] == "")
{
  exit();
}
include ("include/UrlParam2PhpVar.inc.php");
require 'fonction.inc.php';
require "admin.inc.php";
require "lang$lg.inc.php";
//include ("click_droit.txt");
dbConnect();
if(!strstr($_SERVER['HTTP_REFERER'],$adresse_http))
{
   echo "<script language=\"JavaScript\">";
   echo "document.location.replace(\"$adresse_http/index.php\")";
   echo "</script>";
   exit();
}
echo "<html>";
echo "<head>";
echo "<title>$msq_ress</title>";
echo "</head>";
echo "<frameset rows='0,*'>";
          echo "<frame src='' name='null' frameborder='0' scrolling=no />";
          echo "<frame src=\"$lien\" name='principe' frameborder='0' scrolling='auto' />";
?>
</frameset>
<noframes>
<body bgcolor="silver">
<center>
<h2>Votre navigateur n'accepte pas les frames.</h2>
</center>
</body>
</noframes>
</body></html>