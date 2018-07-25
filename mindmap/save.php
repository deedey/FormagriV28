<?php
session_start();
if (!isset($_SESSION['id_user']) || $_SESSION['id_user'] == "")
{
  exit();
}
require "../admin.inc.php";
require '../fonction.inc.php';
require '../fonction_html.inc.php';
dbConnect();
GLOBAL $connect;
$date = date("Y-m-d H:i:s");
$ReqMind = mysql_query("select * from mindmap where mindmap_cdn = ".$_GET['id']);
$mindmap = mysql_fetch_object($ReqMind);
if ($mindmap->mindmap_locking_on > 0 && $mindmap->mindmap_locked_on > 0 && $mindmap->mindmap_idlock_no != $_SESSION['id_user'])
    $retour = "Votre sauvegarde ne peut être effectuée<br /> car ".
               NomUser($mindmap->mindmap_idlock_no)." a repris la main sur cette carte";
elseif ($mindmap->mindmap_locking_on > 0 && $mindmap->mindmap_locked_on > 0 && $mindmap->mindmap_idlock_no == $_SESSION['id_user'])
{
    $ReqUpdateXml = mysql_query('update mindmap set mindmap_xmldata_cmt = "'.htmlentities($_POST['mindmap'],ENT_QUOTES,'ISO-8859-1').
                                '" where mindmap_cdn = '.$_GET['id']);
    $id_max = Donne_ID ($connect,"SELECT max(mindhisto_cdn) from mindmaphistory");
    $ReqUpdateXml = mysql_query('INSERT INTO `mindmaphistory` VALUES('.$id_max.','.$_GET['id'].','.$_SESSION['id_user'].
                                ','.$_SESSION['idClan'].' ,"'.htmlentities($_POST['mindmap'],ENT_QUOTES,'ISO-8859-1').'", "'.$date.'")');
    $retour = 'Votre sauvegarde a été effectuée';
}else
$retour = "Aucune opération";
echo clean_text($retour);
?>