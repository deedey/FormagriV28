<?php
if (!isset($_SESSION)) session_start();
include('../../include/UrlParam2PhpVar.inc.php');
require ("required/config$lg.php");
?>
<html>
<head>
<title><?php echo htmlentities($review_title,ENT_QUOTES,'ISO-8859-1');?></title>
<body bgcolor="#FFFFFF" text="#000000" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<table border="0" width="288" cellspacing="3" cellpadding="3">
<tr><td width="288">
<font face="Verdana, Arial, Helvetica, sans-serif" size="1" color="#666666">
<?php

$chat_file_ok = "required/msg.txt";//message file

$users_file = "required/users.txt";//The file where you save users and passwords

$person = str_replace ("\n"," ", $person);
$person = str_replace ("<", " ", $person);
$person = str_replace (">", " ", $person);
$person = trim ($person);
$person = stripslashes ($person);

/*        check the user  */
/*        --------------- */
$text_string = join ('', file ($users_file));
$valid_user=  "false";
if (preg_match("/(.)*$person\n(.)*$password\n/", $text_string)){
$valid_user = "true";
}
if ($valid_user == "false"){
echo "<center>".htmlentities($kicked_user,ENT_QUOTES,'ISO-8859-1')."</center>";
}else{
/*        reads the last $chat_lenght lines of $chat_file_ok */
/*        -------------------------------------------------- */

$lines = file($chat_file_ok);
$a = count($lines);
$u = $a - $review_lenght;

/*        check the text order */
/*        -------------------- */

if ($review_text_order == "up"){
for($i = $a; $i >= $u ;$i--){
$is_private = 0;
$show_message = 1;
$line_value= strval($lines[$i]);
$string_to_add="";

/*        output to the chat private and general messages */
/*        ----------------------------------------------- */

if (preg_match("/$private_message_expression/", $line_value, $name) and preg_match("/\(de (.*) a (.*)$/", $line_value, $pass)){// is a private message
        $senderpassword=strval($pass[1]);
        $receiverpassword=strval($pass[2]);
        $receivername=strval($name[1]);
        $is_private = 1;
        $show_message = 0;
        }
        if ($is_private == 1){
        if (($receivername==$person) and preg_match("/(.)?$password(\n)?/", $receiverpassword)){//is the receiver
        $line_value = preg_replace( "/\(de (.*) a (.*)$/", "", $line_value);
        echo "<font face='Verdana, Arial, Helvetica, sans-serif' size='1' color='#990000'><b>".htmlentities($line_value,ENT_QUOTES,'ISO-8859-1')."</b></font><br>";
        } elseif(preg_match("/\(..:..:..\) $person :/", $line_value) and preg_match("/(.)?$password/", $senderpassword)){//is the sender
        if (preg_match("/(.)?eztezamarchaoynoezta(\n)?/", $receiverpassword)) {$string_to_add = $not_here_string;}
        $line_value = preg_replace( "/\(de (.*) a (.*)$/", "<font face='Verdana, Arial, Helvetica, sans-serif' size='1' color='#990000'>$string_to_add</font><br>", htmlentities($line_value,ENT_QUOTES,'ISO-8859-1'));
        echo "<font face='Verdana, Arial, Helvetica, sans-serif' size='1' color='#000000'><b>".$line_value."</b></font>";
        }else{
        $u--;
        $show_message = 0;
        }
        }
if (($show_message == 1) and ($line_value)){
{echo htmlentities($line_value,ENT_QUOTES,'ISO-8859-1')."<br>";}// is a general message
        }}
}
else{
for($i = $u; $i <= $a ;$i++){
$is_private = 0;
$show_message = 1;
$line_value= strval($lines[$i]);
$string_to_add="";

/*        output to the chat private and general messages */
/*        ----------------------------------------------- */

if (preg_match("/$private_message_expression/", $line_value, $name) and preg_match("/\(de (.*) a (.*)$/", $line_value, $pass)){// is a private message
        $senderpassword=strval($pass[1]);
        $receiverpassword=strval($pass[2]);
        $receivername=strval($name[1]);
        $is_private = 1;
        $show_message = 0;
        }
        if ($is_private == 1){
        if (($receivername==$person) and preg_match("/(.)?$password(\n)?/", $receiverpassword)){//is the receiver
        $line_value = preg_replace( "/\(de (.*) a (.*)$/", "", $line_value);
        echo "<font face='Verdana, Arial, Helvetica, sans-serif' size='1' color='#990000'><b>".htmlentities($line_value,ENT_QUOTES,'ISO-8859-1')."</b></font><br>";
        } elseif(preg_match("/\(..:..:..\) $person :/", $line_value) and preg_match("/(.)?$password/", $senderpassword)){//is the sender
        if (preg_match("/(.)?eztezamarchaoynoezta(\n)?/", $receiverpassword)) {$string_to_add = $not_here_string;}
        $line_value = preg_replace( "/\(de (.*) a (.*)$/", "<font face='Verdana, Arial, Helvetica, sans-serif' size='1' color='#990000'>$string_to_add</font><br>", htmlentities($line_value,ENT_QUOTES,'ISO-8859-1'));
        echo "<font face='Verdana, Arial, Helvetica, sans-serif' size='1' color='#000000'><b>".$line_value."</b></font>";
        }else{
        $u--;
        $show_message = 0;
        }
        }
if (($show_message == 1) and ($line_value)){
{echo htmlentities($line_value)."<br>";}// is a general message
        }}
}
}

?>
</font></td></tr>
</table>
</body>
</html>
