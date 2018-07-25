<?php
session_start();
if (!isset($_SESSION['id_user']) || $_SESSION['id_user'] == "")
{
  exit();
}
require "../admin.inc.php";
require '../fonction.inc.php';
dbConnect();
GLOBAL $connect;
$ID = (isset($_POST['id'])) ? $_POST['id'] : $_GET['id'];
$ReqMind = mysql_query("select * from mindmap where mindmap_cdn = ".$ID);
$XmlMap = mysql_fetch_object($ReqMind);
if ($XmlMap->mindmap_locked_on == 0)
   $ReqUpdateXml = mysql_query("update mindmap set mindmap_locking_on = 1,mindmap_locked_on = 1,mindmap_idlock_no = ".
                                $_SESSION['id_user']." where mindmap_cdn = ".$ID);
?>