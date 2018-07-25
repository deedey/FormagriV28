<?php

if (!isset($_SESSION)) session_start();
include ("../include/UrlParam2PhpVar.inc.php");
require '../admin.inc.php';
require '../fonction.inc.php';
require "../lang$lg.inc.php";
require "../langues/adm.inc.php";
dbConnect();

//
define('DEF_HOST', 'localhost');
define('DEF_USER', $log);
define('DEF_PASSWORD', $mdp);
define('DEF_DATABASE', $bdd);
//
require_once '../class/AjaxListPopulatorController.php';

$oParam=new stdClass();
$oParam->table="sequence";
$oParam->fieldLabel="seq_titre_lb";
$oParam->fieldId="seq_cdn";

$oAjaxController = new AjaxListPopulatorController($oParam);
$oAjaxController->run();
//
?>