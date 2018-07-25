<?php
if (!isset($_SESSION)) session_start();
if (!isset($_SESSION['id_user']) || $_SESSION['id_user'] == "")
{
  exit();
}
require '../admin.inc.php';
require '../fonction.inc.php';
include ('../include/UrlParam2PhpVar.inc.php');
require "../lang$lg.inc.php";
require_once 'Ldap2Mysql.php';
dbConnect();
include ('entete.inc.php');
$oServerManager = new Ldap2Mysql();
$oServerManager->run();

////////////////////////////////////////////////////////////////////////////
$aContent[] = '</TD></TR></TABLE></TD></TR></TABLE></TD></TR></TABLE>';
$aContent[] = '<script type="text/javascript" src="js/ldap2Mysql.js"></script>';
$aContent[] = '</body>';
$aContent[] = '</html>';
echo implode("\n", $aContent);
?>
