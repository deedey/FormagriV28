<?php
/**
* Class  Controller
*
* @author Nordine Zetoutou  - <nordine.zetoutou@gmail.fr>
* @package
* @description
* @dependance
* @license
* @version 1.0
* @date 11 mars 08 15:50:40
* @copyright
* @todo
*/
class Controller
{
  var $params = array ();
  var $action = null;
  var $controller = null;
  function Controller()
  {
    if (!empty ($_GET))
    {
      //
      foreach ($_GET as $key => $value)
      {
        $this->params[trim($key)] = trim($value);
        //
        $array_keyParam = array_keys($this->params);
        $this->controller = @ $array_keyParam[0];
        $this->action = @ $array_keyParam[1];
        //
      }
      //
    }
  }
} //fin classe Controller
?>
