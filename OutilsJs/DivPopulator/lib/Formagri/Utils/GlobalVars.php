<?php

/**
   * Class  GlobalVars
   *
   * @author Nordine Zetoutou  - <nordine.zetoutou@gmail.fr>
   * @package
   * @description
   * @example
   * @dependance
   * @license
   * @version 1.0
   * @date 1 févr. 08 15:31:57
   * @copyright
   */
@ if (!isset($_SESSION)) session_start();
class GlobalVars
{
	function GlobalVars()
	{
	}
	function jEncode($obj)
	{
		return json_encode($obj);
	}
	function jDecode($obj)
	{
		return json_decode($obj);
	}
	function isCached($key)
	{
		if (isset ($_SESSION[DEF_MODULE_NAME][$key]))
		{
			return true;
		}
	}
	function getFromCache($key)
	{
		return @$_SESSION[DEF_MODULE_NAME][$key];
	}
	function setInCache($key, $value)
	{
		//mydebug('je vais  dans le cache');
		$_SESSION[DEF_MODULE_NAME][$key] = $value;
	}
} //fin classe GlobalVars
?>
