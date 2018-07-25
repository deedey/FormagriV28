<?php
if (!isset($_SESSION)) session_start();
require 'admin.inc.php';
require 'fonction.inc.php';
require "fonction_html.inc.php";
dbConnect();
$sql = mysql_query("SELECT img_blb,typ_img,image FROM qcm_donnees WHERE qcm_data_cdn=$code");
$res_sql = mysql_num_rows($sql);
//$blb = mysql_result($sql,0,'img_blb');
$typ = mysql_result($sql,0,'typ_img');
//if (strlen($blb) < 225)
//{
   $img = mysql_result($sql,0,'image');
   echo "<img src=\"ressources/$img\" border=0>";
   exit;
//}
//else
  // header("Content-Type:$typ");
//print $blb;
?>
