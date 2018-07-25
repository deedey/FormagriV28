<?php
error_reporting(E_ALL);
//----------------------------addon-on de remplacement --------------------------------------------------//
//
//array_combine

function array_combine($array1, $array2)
{
	//
	$count = count($array1);
	for ($index = 0; $index < $count; $index++)
	{
		$arr[$array1[$index]] = $array2[$index];
	}
	return $arr;
	//
}
//json
function json_encode($value)
{
	require_once "json.php";
	$oServices_JSON = new Services_JSON();
	return $oServices_JSON->encode($value);
}
//
function json_decode($value)
{
    require_once 'json.php';
    $oServices_JSON = new Services_JSON();
    return $oServices_JSON->encode($value);
}
//


?>
