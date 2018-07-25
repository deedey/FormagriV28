<?php
if (!isset($_SESSION)) session_start();
header("Expires: ".gmdate("D, d M Y H:i:s")."GMT");
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache");
include('../../include/UrlParam2PhpVar.inc.php');
require "admin.inc.php";
require "fonction.inc.php";
require ("required/config$lg.php");

$chat_file_ok = "required/msg.txt";//message file

$users_file = "required/users.txt";//The file where you save users and passwords

if($show_without_time == "no"){//show time?
$substart=0;
}else{
$substart=19;
}
/*        check banning   */
/*        --------------- */

$banned_file = "required/banned_ip.txt";//the file with banned ips
$text_string = join ('', file ($banned_file));
if (preg_match("/(.)*$password\n/",$text_string)){//this user was banned
$text_string = join ('', file ($users_file));
$new_list= str_replace ("$password", " banned", $text_string);
$fp = fopen($users_file, "w");
$fw = fwrite($fp, $new_list);
fclose($fp);
}

$person = str_replace ("\n"," ", $person);
$person = str_replace ("<", " ", $person);
$person = str_replace (">", " ", $person);
$person = trim ($person);
$person = stripslashes ($person);
?>
&output=
<?php
/*        check the user  */
/*        --------------- */
$text_string = join ('', file ($users_file));
$valid_user=  "valid";
if ((preg_match("/(.)*$person\n(.)* kicked\n/", $text_string)) or (preg_match("/(.)*$person\n(.)* banned\n/", $text_string))){
$valid_user = "kicked";
}
if ($valid_user == "kicked"){
if (preg_match("/(.)*$person\n(.)* kicked\n/", $text_string)){echo urlencode($kicked_user)."\n";}
if (preg_match("/(.)*$person\n(.)* banned\n/", $text_string)){echo urlencode($banned_user)."\n";}
}else{

/*        limit file size of $chat_file_ok */
/*        -------------------------------- */

$lines = file($chat_file_ok);
$a = count($lines);
if ($a <= $total_lenght){
$u = $a - $review_lenght;
$msg_old="";
for($i =$u ; $i <= $a ;$i++){
                $msg_old .=  $lines[$i];
        }
$fp = fopen($chat_file_ok, "w");
$fw = fwrite($fp, $msg_old);
fclose($fp);
}

/*        write message to $chat_file_ok */
/*        ------------------------------ */

$msg = str_replace ("\n"," ", $message);
$msg = stripslashes ($msg);

/*        filter bad words */
/*        ----------------- */

$number_of_bad_words = count($words_to_filter);
for($i = 0; $i <= $number_of_bad_words ;$i++){
if (strval($words_to_filter[$i])!=""){
$msg = preg_replace("/".strval($words_to_filter[$i]."/i"),$replace_by,$msg);
}
}

if (preg_match ("/$private_message_expression/", $msg, $regs) and preg_match ("/\(de $password a/", $msg)){  // is a private message
  $receiver = strval($regs[1])."\n";
  $lines = file($users_file);
  $a = count($lines);
  $follow = 1;
  for($i = 0; $i <= $a ;$i++){
    if (strval($lines[$i]) == $receiver){
       $add = rtrim(strval($lines[$i+1]));//read the password of receiver
       $follow = 0;
    }else{
      if ($follow==1){
        $add = " eztezamarchaoynoezta";// receiver is not in the room
      }
    }
  }
  $msg .= $add;
}
if ($msg != ""){
$detail++;
$_SESSION['detail'] = $detail;
if ($detail == 1){
  $date_entree = date ("d M-");
  $text_entree =$date_entree.date ("(H:i:s)",time()+$correct_time)." ".$person." : ".$msg;
  $_SESSION['text_entree'] = $text_entree;
}else
  $date_entree = "";
$text_to_write =$date_entree.date ("(H:i:s)",time()+$correct_time)." ".$person." : ".$msg."\n";//compound single message
$fp = fopen($chat_file_ok, "a");
$fw = fwrite($fp, "$text_to_write");// message is appended to the msg.txt file
fclose($fp);
}

/*        reads the last $chat_lenght lines of $chat_file_ok */
/*        -------------------------------------------------- */

$lines = file($chat_file_ok);
$fl = fopen($chat_file_ok,"r");
while (!feof($fl)) {
      $ligne = fgets($fl, 4096);
      if (strstr($ligne, $text_entree)){
       $compter=1;
      }
      if ($compter == 1)
       $chat_longueur++;
}
fclose ($fl);
$a = count($lines);
$u = $a - $chat_longueur;

/*        check the text order */
/*        -------------------- */
if ($text_order == "up"){
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
        $line_value = preg_replace( "/\(de (.*) a (.*)$/", "\n", $line_value);
        echo substr(urlencode($line_value),$substart);//show message
        } elseif(preg_match("/\(..:..:..\) $person :/", $line_value) and preg_match("/(.)?$password/", $senderpassword)){//is the sender
        if (preg_match("/(.)?eztezamarchaoynoezta(\n)?/", $receiverpassword)) {$string_to_add = $not_here_string;}
        $line_value = preg_replace( "/\(de (.*) a (.*)$/", "$string_to_add\n", $line_value);
        echo substr(urlencode($line_value),$substart);//show message
        }else{
        $u--;
        $show_message = 0;
        }
        }
if ($show_message == 1) {// is a general message
if ((preg_match("/$bye_user/",$line_value)) or (preg_match("/$enter_string/",$line_value))){
echo urlencode ($line_value);//show time only if user enters or exits
}
else{
echo substr(urlencode($line_value),$substart);//show message
}
}
}
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
        $line_value = preg_replace( "/\(de (.*) a (.*)$/", "\n", $line_value);
        echo substr(urlencode($line_value),$substart);//show message
        } elseif(preg_match("/\(..:..:..\) $person :/", $line_value) and preg_match("/(.)?$password/", $senderpassword)){//is the sender
        if (preg_match("/(.)?eztezamarchaoynoezta(\n)?/", $receiverpassword)) {$string_to_add = $not_here_string;}
        $line_value = preg_replace( "/\(de (.*) a (.*)$/", "$string_to_add\n", $line_value);
        echo substr(urlencode($line_value),$substart);//show message
        }else{
        $u--;
        $show_message = 0;
        }
        }
if ($show_message == 1) {// is a general message
if ((preg_match("/$bye_user/",$line_value)) or (preg_match("/$enter_string/",$line_value))){
echo urlencode ($line_value);//show time only if user enters or exits
}
else{
echo substr(urlencode($line_value),$substart);//show message
}
}
}
}
}
echo "&order=";
echo $text_order;
?>
