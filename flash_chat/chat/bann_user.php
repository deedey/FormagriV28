<?php
if (!isset($_SESSION)) session_start();
require "../../admin.inc.php";
require '../../fonction.inc.php';
require ("required/config$lg.php");
$users_file = "required/users.txt";
$banned_file = "required/banned_ip.txt";
dbConnect();

if (($name==$admin_name) and ($passord==$admin_password)){

$lines = file($users_file);
$a = count($lines);

//inhabilitation for ip
//---------------------

$text_string = join ('', file ($users_file));
$new_list= str_replace ("$user_password", " banned", $text_string);
$fp = fopen($users_file, "w");
$fw = fwrite($fp, $new_list);
fclose($fp);

//add ip to banned_ip.txt
//-----------------------

$fp = fopen($banned_file, "a");
$user_password= trim ($user_password);
$fw = fwrite($fp, "$user_password\n");
fclose($fp);



echo "<script>";
echo "location.replace('adminuser.php?name=$name&passord=$passord')";
echo "</script>";

}

?>
