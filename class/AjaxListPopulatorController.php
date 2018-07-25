<?php
if (!isset($_SESSION)) session_start();
//@temp
if ((int) phpversion() < 5)
{
//  require_once "<?php echo $monURI;?>/Referentiel/addons/addons.php";
  require_once "<?php echo $monURI;?>/OutilsJs/DivPopulator/addons/addons.php";
}
//fin temp
//------------------------------------------------------------------------------//
/**
* Class  AjaxController
*
* @author Nordine Zetoutou  - <nordine.zetoutou@gmail.fr>
* @package
* @description
* @dependance
* @license
* @version 1.0
* @date 21 févr. 08 16:49:28
* @copyright
* @todo
*/

//
class AjaxListPopulatorController
{
  var $maxItems = 50;
  var $dbName = DEF_DATABASE;
  var $warnings = array (
    'search_min_char' => '&raquo;&nbsp;Au moins 3 caract&egrave;res',
    'search_total_over_limit' => '&raquo;&nbsp;Il existe plus de # libellés, affiner votre recherche !'
  );
  //
  /**
    * @description description
    * @param type  $variable name
    * @return type  $variable name
    */
  function AjaxListPopulatorController($oParam)
  {
        $this->table = $oParam->table;
        $this->fieldLabel = $oParam->fieldLabel;
        $this->fieldId = $oParam->fieldId;
        //
        if( isset($oParam->maxItems))
        {
            $this->maxItems=$oParam->maxItems;
        }
        if(isset($oParam->warnings))
        {
            $this->warnings=$oParam->warnings;
        }
  }
  //fin du constructeur
  function run()
  {
    echo json_encode($this->display());
  }
  function clean($str)
  {
    $str = preg_replace("`[^[:alnum:]]+`", ' ', $str);
    return $str;
  }
  //
  function display()
  {
    $result = array ();
    $toJson['items'] = array ();
    //$_GET["search"]="zet";
    if (!empty ($_GET["search"]))
    {
      $words = trim($_GET["search"]); //
      $words = (urldecode($words));//Utils :: stripAccent
    }
    else
    {
      return $toJson;
    }
    //
    if (strlen($words) < 3)
    {
      $toJson['resultInformation'] = $this->warnings['search_min_char'];
      return $toJson;
    }
    //
    $aWord = explode(" ", $words);
    //
    //
    //$_GET["list"] = 'competence_module';
//    switch ($_GET["list"])
//    {
//      case 'competence_module' :
//        $this->table = 'parcours';
//        $this->fieldLabel = 'parcours_nom_lb';
//        $this->fieldLabel = 'parcours_cdn';
//        break;
//    } //fin switch
    //
    foreach ($aWord as $key => $word)
    {
      $aLike[] = $this->fieldLabel . " LIKE '%" . addslashes($word) . "%'";
      //$aFormatedWord[] = '(' . $word . ')';
    }
    //
    $strLike = implode(' AND ', $aLike);
    //$query = "select distinct " . $this->fieldLabel . " from " . $this->table . " where " . $like . ""; // limit 0,100
    $query = "select * from " . $this->table . " where " . $strLike . ""; // limit 0,100
    //echo $query;
    $aRow = $this->getLibelleList($query);
    //
    if (count($aRow) > $this->maxItems)
    {
      $maxItems = '<span style="font-weight:bold">' . $this->maxItems . '</span>';
      $toJson['resultInformation'] = str_replace('#', $maxItems, $this->warnings['search_total_over_limit']);
    }
    else
    {
      foreach ($aRow as $row)
      {
        $row['jLabel'] = htmlentities($row[$this->fieldLabel],ENT_QUOTES,,'ISO-8859-1');
        $row['jId'] = $row[$this->fieldId];
        $toJson['items'][] = $row;
      }
    }
    //
    return $toJson;
  }
  //
  //
  function getLibelleList($query)
  {
    mysql_connect(DEF_HOST, DEF_USER, DEF_PASSWORD);
    mysql_select_db(DEF_DATABASE);
    $res = mysql_query($query);
    $arr = array ();
    while ($row = mysql_fetch_assoc($res))
    {
      $arr[] = $row;
    }
    return $arr;
  }
} //fin classe AjaxController
//
?>