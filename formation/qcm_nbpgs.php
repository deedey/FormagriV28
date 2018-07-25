<?php
if (!isset($_SESSION)) session_start();
header("Content-type  : text/plain");
header("Cache-Control: no-cache, max-age=0");
require '../admin.inc.php';
require '../fonction.inc.php';
include "../include/UrlParam2PhpVar.inc.php";
require "../lang$lg.inc.php";
require "../langues/ress.inc.php";
require "../fonction_html.inc.php";
dbConnect();
unset($_SESSION['qcm_pages']);
$qcm_pages = $pages_qcm;
$_SESSION['qcm_pages'] = $qcm_pages;
$mess_notif = $mmsg_qcmPgs." ".$pages_qcm;
sleep(1);
echo  utf2Charset($mess_notif,$charset);
?>