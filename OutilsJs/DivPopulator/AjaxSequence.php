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

//$table="sequence";
//$fieldLabel="seq_titre_lb";
//$fieldId="seq_cdn";

//
//---------------------------------------------------------------------------/
//
require_once 'AjaxListPopulator.php';
//
$oParam = new stdClass();
$oParam->table =$table;
$oParam->fieldLabel = $fieldLabel;
$oParam->fieldId = $fieldId;
//
//
$oParam->strip_tags = true;
//$oParam->maxItems = 50;// la valeur par d&eacute;faut
$oParam->informations = array (
  'search_min_char' => '&raquo;&nbsp;Au moins <b>3</b> caract&egrave;res',
  'search_total_over_limit' => '&raquo;&nbsp;Il existe plus de # libellés, affiner votre recherche !',
  'total_found' => '&raquo;&nbsp;# &eacute;l&eacute;ment (s) trouv&eacute; (s)'
);
$oAjaxController = new AjaxListPopulator($oParam);
$oAjaxController->run();

?>
