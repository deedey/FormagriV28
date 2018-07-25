<?php
if (!isset($_SESSION)) session_start();
require "../../admin.inc.php";
require '../../fonction.inc.php';
require ("required/config$lg.php");
dbConnect();
$banned_file = "required/banned_ip.txt";

if (($name==$admin_name) and ($passord==$admin_password)){

$lines = file($banned_file);
$a = count($lines);

//delete de banned ip
//-------------------

$text_string = join ('', file ($banned_file));
$new_list= str_replace ("$ip", "", $text_string);
$fp = fopen($banned_file, "w");
$fw = fwrite($fp, $new_list);
fclose($fp);

echo "<script>";
echo "location.replace('adminips.php?name=$name&passord=$passord')";
echo "</script>";

}

?>
