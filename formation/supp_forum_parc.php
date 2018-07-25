<?php
if (!isset($_SESSION)) session_start();
header("Content-type  : text/plain");
header("Cache-Control: no-cache, max-age=0");
require "../admin.inc.php";
require "../fonction.inc.php";
include "../include/UrlParam2PhpVar.inc.php";
require "../lang$lg.inc.php";
require "../langues/module.inc.php";
require "../fonction_html.inc.php";
dbConnect();
$requete = mysql_query("delete from forums_modules where fm_module_no = $id_parc");
$requete = mysql_query("delete from parcours_forums where parcforum_parc_no = $id_parc");
$nom_module = GetDataField($connect,"SELECT parcours_nom_lb FROM parcours WHERE parcours_cdn = $id_parc","parcours_nom_lb");
$mess_notif = $msg_supforparc.$nom_module;
sleep(1);
echo  utf2Charset($mess_notif,$charset);

?>
