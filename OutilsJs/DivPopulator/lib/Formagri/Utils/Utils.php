<?php
error_reporting(E_ALL );
/**
   * Class  Utils_Debug
   *
   * @author Nordine Zetoutou  - <nordine.zetoutou@gmail.fr>
   * @package
   * @description
   * @example
   * @dependance
   * @license
   * @version 1.0
   * @date 22 janv. 08 15:06:24
   * @copyright
   * @todo
   */
// $oUtils=new  Utils();;
// Utils_Debug::debug();
class Utils
{
	function debug($strOrObject = 'hello', $debug = true)
	{
		if ($debug == true)
		{
			$sql_command_to_replace = array (
				'SELECT',
				'DISTINCT',
				'COLUMNS',
				'FROM',
				'LIMIT',
				'LIKE',
				'WHERE',
				'GROUP',
				'HAVING',
				'ORDER BY',
				'ASC',
				'DESC',
				'INNER JOIN',
				'LEFT JOIN',
				'RIGHT JOIN',
				'FULL JOIN',
				'CROSS JOIN',
				'NATURAL JOIN'
			);
			//
			foreach ($sql_command_to_replace as $value)
			{
				$sql_command_to_replaceBy[] = '<span style="color:green;">' . $value . '</span>';
			}
			//
			//settype($strOrObject,'array');
			//
			if (is_array($strOrObject))
			{
				$strOrObject = str_replace($sql_command_to_replace, $sql_command_to_replaceBy, $strOrObject);
			}
			$style = "text-align:left;background-color:#000;padding:8px;margin:4px;font-size:11px;color:red;";
			$style .= "font-family:georgia;border:2px solid red;";
			echo '<pre style="' . $style . '">';
			print_r($strOrObject);
			echo '</pre>';
		}
	}
    //
    function stripAccent($str)
{
    $aPattern['a'] = array (
        "/à/",
        "/á/",
        "/â/",
        "/ã/",
        "/ä/",
        "/å/"
    );
    $aPattern['c'] = array (
        "/ç/"
    );
    $aPattern['e'] = array (
        "/è/",
        "/é/",
        "/ê/",
        "/ë/"
    );
    $aPattern['i'] = array (
        "/ì/",
        "/í/",
        "/í/",
        "/î/",
        "/ï/"
    );
    $aPattern['o'] = array (
        "/ð/",
        "/ò/",
        "/ó/",
        "/ô/",
        "/õ/",
        "/ö/"
    );
    $aPattern['u'] = array (
        "/ù/",
        "/ú/",
        "/û/",
        "/ü/"
    );
    $aPattern['y'] = array (
        "/ý/",
        "/ÿ/"
    );
    $newStr = $str;
    foreach ($aPattern as $key => $pattern)
    {
        $newStr = preg_replace($pattern, array_fill(0, count($pattern), $key), $newStr);
    }
    return $newStr;
}
} //fin classe Utils
?>
