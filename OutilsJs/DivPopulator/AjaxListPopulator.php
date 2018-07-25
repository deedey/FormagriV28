<?php
if (!isset($_SESSION)) session_start();
error_reporting(E_ALL | E_STRICT);
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
* @date 20 mars. 2008
* @copyright
* @todo
*/
usleep(200000);//1/5 seconde
//
class AjaxListPopulator
{
  var $debug = false;
  var $maxItems = 50;
  var $dbName = DEF_DATABASE;
  var $searchCharMin = 3;
  var $informations = array ();
  //
  function AjaxListPopulator($oParam)
  {
    //
    //
    if (isset ($oParam->informations))
    {
      $this->informations = $oParam->informations;
    }
    //
    if (isset ($oParam->searchCharMin))
    {
      $this->searchCharMin = $oParam->searchCharMin;
    }
    //
    if (isset ($oParam->stripTags))
    {
      $this->stripTags = $oParam->stripTags;
    }
  }
  //
  function run()
  {
     //if ((int) phpversion() < 5)
        //require_once "addons/addons.php";
     echo json_encode($this->display());
  }
  function clean($str)
  {
    $str = preg_replace("/[^[:alnum:]]+/", ' ', $str);
    return $str;
  }
  //
  function display()
  {
    $result = array ();
    $toJson['items'] = array ();
    if (!empty ($_POST["search"]))
    {
      $words = urldecode($_POST["search"]);
    }
    else
    {
      return $toJson;
    }
    //
    if (strlen($words) < $this->searchCharMin)
    {
      $toJson['resultInformation'] = $this->informations['search_min_char'];
      return $toJson;
    }
    //
    $this->table = $_POST['dbTable'];
    $this->fieldLabel = $_POST["dbTableFieldLabel"];
    $this->fieldId = $_POST["dbTableFieldId"];
    $this->fieldCond = $_POST["dbTableFieldCond"];
    //
    if (!empty ($_POST['maxItems']))
    {
      $this->maxItems = $_POST["maxItems"];
    }
    //
    $aWord = explode(" ", $words);
    //
    foreach ($aWord as $key => $word)
    {
      $aLike[] = $this->fieldLabel . " LIKE '%" . addslashes(utf8_decode(html_entity_decode($word,ENT_QUOTES,'ISO-8859-1'))) . "%'";
    }
    //
    $strLike = implode(' AND ', $aLike);
    $query = "select * from " . $this->table . " where " . $strLike . " ";
    if ($this->fieldCond != '')
       $query = "select * from " . $this->table . " where " . $strLike .$this->fieldCond;
    $aRow = $this->getLibelleList($query);
    //
    if (count($aRow) > $this->maxItems)
    {
      $maxItems = '<b>' . $this->maxItems . '</b>';
      $toJson['resultInformation'] = str_replace('#', $maxItems, $this->informations['search_total_over_limit']);
    }
    else
    {
      foreach ($aRow as $row)
      {
        $row['jLabel'] = trim(mb_convert_encoding(html_entity_decode($row[$this->fieldLabel],ENT_QUOTES,'ISO-8859-1'), 'UTF-8', 'ISO-8859-15'));
        //
        if ($this->stripTags)
        {
          $row['jLabel'] = strip_tags($row['jLabel']);
        }
        //
        $row['jId'] = $row[$this->fieldId];
        $toJson['items'][] = $row;
      }
      //
      $total_found = count($aRow);
      $total_found = '<span style="font-weight:bold">' . $total_found . '</span>';
      $toJson['msg_total_found'] = str_replace('#', $total_found, $this->informations['total_found']);
    }
    //
    return $toJson;
  }
  //
  function getLibelleList($query)
  {
    $aError = array ();
    $arr = array ();
    //
    if (!@ mysql_connect(DEF_HOST, DEF_USER, DEF_PASSWORD))
    {
      $aError[] = mysql_error();
    }
    //
    if (!@ mysql_select_db(DEF_DATABASE))
    {
      $aError[] = mysql_error();
    }
    if (!@ mysql_query($query))
    {
      $aError[] = mysql_error();
    }
    else
    {
      $res = mysql_query($query);
      while ($row = mysql_fetch_assoc($res))
      {
        $arr[] = $row;
      }
    }
    if ($this->debug == true)
    {
      $style = "background-color:#fff;padding:8px;margin:4px;font-size:12px;";
      $style .= "font-family:'Courier New';border:2px solid red;color:red;width:600px;";
      echo '<pre><div  style="' . $style . '">';
      print_r($aError);
      echo '</div></pre>';
    }
    return $arr;
  }
} //fin classe AjaxController
//
?>