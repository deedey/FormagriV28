<?php
if (!isset($_SESSION)) session_start();
require "../../admin.inc.php";
require '../../fonction.inc.php';
require "../../fonction_html.inc.php";
dbconnect();
header("Content-type: image/png");
$reqImg = requete("bgimg_content_blb","blogmg","bgimg_cdn= ".$_GET['numImg']);
$image = mysql_result($reqImg,0,"bgimg_content_blb");
print $image;
?>