<?php
/**
   * Class  View
   *
   * @author Nordine Zetoutou  - <nordine.zetoutou@gmail.fr>
   * @package View
   * @description
   * @example
   * @dependance
   * @license
   * @version 1.0
   * @date 8 janv. 08 14:58:18
   * @copyright
   * @todo
   */
class View
{
	var $delimiter = "\n";
	//
	function View()
	{
	}
	//

	function buildAttribs($attribs)
	{
		if(count($attribs))
        {
		foreach ((array) $attribs as $key => $val)
		{
			$xhtml[] = trim($key) . '="' . trim($val) . '"';
		}
        return ' '.implode(' ', $xhtml);
        }
        return '';

	}
	//

	//
	function debug($msg)
	{
		$style = "background-color:#000;padding:8px;margin:4px;font-size:11px;color:red;";
		$style .= "font-family:georgia;border:2px solid red;";
		echo '<pre style="' . $style . '">';
		print_r($msg);
		echo '</pre>';
	}
	//
}
?>
