<?php
if (!isset($_SESSION)) session_start();
if (!isset($_SESSION['id_user']) || $_SESSION['id_user'] == "")
{
  exit();
}
header("Content-type  : text/plain");
header("Cache-Control: no-cache, max-age=0");
require '../../admin.inc.php';
require '../../fonction.inc.php';
require "../../fonction_html.inc.php";
dbConnect();
if (isset($_POST['value']) && $_POST['value'] > 0)
{
      $id_wk = Donne_ID ($connect,"select max(wkstar_cdn) from wikistar");
      $req = mysql_query("insert into wikistar values($id_wk,".$_SESSION['id_user'].",".$_POST['IdBody'].",".$_POST['value'].",'".time()."')");
}
sleep(1);
?>
