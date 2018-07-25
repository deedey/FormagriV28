<?php
if (!isset($_SESSION)) session_start();
include ("include/UrlParam2PhpVar.inc.php");
require "admin.inc.php";
require 'fonction.inc.php';
require "lang$lg.inc.php";
//include ("click_droit.txt");
dbConnect();
$agent=getenv("HTTP_USER_AGENT");
echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">';
echo "<html><head><link rel='shortcut icon' href='images/icone.ico' type='image/x-icon' /></head>";
echo "<body bgcolor='white'";
if (strstr($agent,"MSIE"))
   $relance = 0;
else
   $relance = 1;
if ($id_user)
{
  $lien= "delog.php?relance=$relance";
  $lien = urlencode($lien);
  echo " onunload=\"javascript:window.open('trace.php?link=$lien','','scrollbars,resizable=yes,width=450,height=100')\">";
}
else
  echo ">";
?>
</body>
</HTML>