<?php
//error_reporting(E_ALL | E_STRICT);
//

require('../../admin.inc.php');
define('DEF_HOST', $adresse);
define('DEF_PORT', 3306);
define('DEF_USER', $log);
define('DEF_PASSWORD', $mdp);
define('DEF_DATABASE', $bdd);
define('DEF_NO_CONNEXION', '&raquo;&nbsp; Le site est actuellement indisponible, merci de votre compr&eacute;hension !');
//
//

//---------------------------------------------------------------------------/
//
require_once 'AjaxListPopulator.php';
//
$oParam = new stdClass();
//
//
$oParam->stripTags = true;
$oParam->informations = array (
  'search_min_char' => '&raquo;&nbsp;Au moins <b>3</b> caract&egrave;res',
  'search_total_over_limit' => '&raquo;&nbsp;Il existe plus de # libell&eacute;s, affiner votre recherche !',
  'total_found' => '&raquo;&nbsp;# &eacute;l&eacute;ment (s) trouv&eacute; (s)'
);
$oAjaxListPopulator = new AjaxListPopulator($oParam);
$oAjaxListPopulator->run();

?>