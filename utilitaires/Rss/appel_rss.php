<?php
if (!isset($_SESSION)) session_start();
require '../../fonction.inc.php';
require "../../langfr.inc.php";
require '../../admin.inc.php';
echo "<frameset rows='142px,*'>";
         echo "<frame src='agrege.php' name='nothing' frameborder='0' scrolling='no' />";
         echo "<frame src=\"$adresse_http/utilitaires/Rss/flux.php\" name='rss' frameborder='0' scrolling='auto' />";
echo "</frameset>";


?>
