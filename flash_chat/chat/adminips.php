<?php
if (!isset($_SESSION)) session_start();
require "admin.inc.php";
require 'fonction.inc.php';
require ("required/config$lg.php");
?>

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta http-equiv="no-cache">
<META HTTP-EQUIV="Pragma" CONTENT="no-cache">
<meta http-equiv="expires" content="Wed, 26 Feb 1995 08:21:57 GMT">
        <titleChat Administration</title>
</head>
<body>

<?php
$banned_file = "required/banned_ip.txt";

if (($name==$admin_name) and ($passord==$admin_password)){


$lines = file($banned_file);
$a = count($lines);

if ($a==0){
echo "<table width=\"400\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">";
echo "<tr><td>";
echo "<center>";
echo "<font face='Verdana, Arial, Helvetica, sans-serif' size='1' color='#000000'>";
echo htmlentities($no_ips,ENT_QUOTES,'ISO-8859-1');
echo "</font>";
echo "</center>";
echo "</td></tr></table>";
}
else{
$presence=0;
for($i = $a; $i >= 0 ;$i--){
$each_ip = strval($lines[$i]);//each ip in the file
$each_ip = str_replace ("\n","", $each_ip);
$each_ip = trim ($each_ip);
if ($each_ip!=""){
$presence=1;
echo "<table width=\"400\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">";
echo "<tr>";
echo "<td width=\"100\">";
echo "<font face='Verdana, Arial, Helvetica, sans-serif' size='1' color='#000000'>";
echo $each_ip."&nbsp;";
echo "</font></td><td width=\"100\">";
echo "<font face='Verdana, Arial, Helvetica, sans-serif' size='1' color='#000000'>";
echo "&nbsp;";
echo "</font></td><td width=\"200\">";
echo "<form name=\"$each_user\" method=\"post\" action=\"pardon_ip.php\">";
echo "<font face='Verdana, Arial, Helvetica, sans-serif' size='1' color='#000000'>";
echo "<input type=\"hidden\" name=\"name\" value=\"$name\">";
echo "<input type=\"hidden\" name=\"passord\" value=\"$passord\">";
echo "<input type=\"hidden\" name=\"ip\" value=\"$each_ip\">";
echo "<input type=\"submit\" name=\"Submit\" value=\"".htmlentities($text_for_pardon_button,ENT_QUOTES,'ISO-8859-1')."\">";
echo "</font></form></td></tr></table><hr>"."\n";
}
 }
 if ($presence==0){
echo "<table width=\"400\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">";
echo "<tr><td>";
echo "<center>";
echo "<font face='Verdana, Arial, Helvetica, sans-serif' size='1' color='#000000'>";
echo htmlentities($no_ips,ENT_QUOTES,'ISO-8859-1');
echo "</font>";
echo "</center>";
echo "</td></tr></table>";
}
}
echo "<hr>";
echo "<table width=\"400\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">";
echo "<tr>";
echo "<td width=\"400\">";
echo "<center>";
echo "<font face='Verdana, Arial, Helvetica, sans-serif' size='1' color='#000000'>";
echo "<a href=\"adminusers.php?name=$name&passord=$passord\">".htmlentities($users_link,ENT_QUOTES,'ISO-8859-1')."</a>";
echo "</font>";
echo "</center>";
echo "</td></tr></table>";
}
else{
echo "<script>";
echo "location.replace('admin.php?name=$name&passord=$passord')";
echo "</script>";
}

?>
</body>
</html>
