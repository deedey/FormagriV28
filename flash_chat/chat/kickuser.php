<?php
if (!isset($_SESSION)) session_start();
require "../../admin.inc.php";
require '../../fonction.inc.php';
require ("required/config$lg.php");
dbConnect();
$users_file = "required/users.txt";

if (($name==$admin_name) and ($passord==$admin_password)){

$lines = file($users_file);
$a = count($lines);

//kick the user
//-------------

$text_string = join ('', file ($users_file));
$new_list= str_replace ("$user_password", " kicked", $text_string);
$fp = fopen($users_file, "w");
$fw = fwrite($fp, $new_list);
fclose($fp);


echo "<script>";
echo "location.replace('adminusers.php?name=$name&passord=$passord')";
echo "</script>";

}

?>
