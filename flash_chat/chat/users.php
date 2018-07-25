<?php
if (!isset($_SESSION)) session_start();
header("Expires: ".gmdate("D, d M Y H:i:s")."GMT");
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache");
error_reporting(7);
include('../../include/UrlParam2PhpVar.inc.php');
require "../../admin.inc.php";
require "../../fonction.inc.php";
require ("required/config$lg.php");
$chat_file_ok = "required/msg.txt";//message file

$users_file = "required/users.txt";//The file where you save users and passwords.


/*        check the user  */
/*        --------------- */
$text_string = join ('', file ($users_file));
$valid_user =  "valid";
if (preg_match("/(.)*$person\n(.)*kicked\n/", $text_string)){
    $valid_user = "kicked";
}
if (preg_match("/(.)*$person\n(.)*banned\n/", $text_string)){
    $valid_user = "banned";
}

if ($bye!="bye"){
    $person = trim ($person);
    echo "action=";
    echo $action;
    echo "&password=";
    echo $password;
    echo "&person=";

    if ($action =="delete"){

        //delete user and password
        //------------------------
        unset($_SESSION['text_entree']);
        $text_string = join ('', file ($users_file));
        $new_list= preg_replace ("/$person(0|[1-9][0-9]*)*\n $password\n/", "", $text_string);
        $fp = fopen($users_file, "w");
        $fw = fwrite($fp, $new_list);
        fclose($fp);

        if((strval(trim($new_list))=="") and ($delete_empty_room=="yes")){//if not users delete messages
            $fu = fopen($chat_file_ok, "w");
            $blanktext="";
            $fwu = fwrite($fu, $blanktext);
            fclose($fu);
        }
    }

    if ($action =="add"){
        $number_to_add = 1;
        $number_to_rest = 1;
        $save_person_temp = $person;

        //kick inactive users and delete kicked and banned users
        //------------------------------------------------------

        $actual_hour= date ("H");
        settype($actual_hour,"integer");
        $actual_minute= date ("i");
        settype($actual_minute,"integer");
        $actual_timing= (3600*$actual_hour)+(60*$actual_minute)+$correct_time;
        settype($actual_timing,"integer");

        $lines = file($users_file);
        $a = count($lines);

        for($i = $a; $i >= 0 ;$i=$i-2){
            $each_user = strval($lines[$i]);//each connected user
            $each_user = str_replace ("\n","", $each_user);
            $each_password = strval($lines[$i+1]);
            $each_password = str_replace ("\n","", $each_password);
            $each_password = trim($each_password);
            if (($each_password=="kicked") or($each_password=="banned")){
                 $text_string = join ('', file ($users_file));
                 $new_list= preg_replace ("/(.)*$each_user\n(.)*$each_password\n/", "", $text_string);//delete kicked users
                 $fp = fopen($users_file, "w");
                 $fw = fwrite($fp, $new_list);
                 fclose($fp);
                 if((strval(trim($new_list))=="") and ($delete_empty_room=="yes")){//if not users delete messages
                     $fu = fopen($chat_file_ok, "w");
                     $blanktext="";
                     $fwu = fwrite($fu, $blanktext);
                     fclose($fu);
                 }
            }

            $message_lines = file($chat_file_ok);
            $message_count = count($message_lines);

        }

        //correct existing name adding a number
        //-------------------------------------

        $text_string = join ('', file ($users_file));
        while (preg_match ("/$person(\n)?/", $text_string)){//repeat while name is taken
               $last_character = substr($person, -$number_to_rest);//look for the last characters in the name
               $test_last_character = $last_character;
               settype($test_last_character,"integer");

               if (strval($test_last_character) == $last_character) {// last character is a number;
                   $save_person_temp = substr($person, 0, strlen($person)-$number_to_rest);
               }

               $person = $save_person_temp.$number_to_add;// replace last character;
               $number_to_add++;
               $number_to_rest = strlen($number_to_add);
        }

        //write entering message
        //----------------------

        $text_to_write = date ("(H:i:s)",time()+$correct_time)." ".$person." : ".$enter_string."\n";//compound single message
        $fp = fopen($chat_file_ok, "a");
        $fw = fwrite($fp, "$text_to_write");// message is appended to the msg.txt file
        fclose($fp);

        //add user and password
        //---------------------
        $fp = fopen($users_file, "a");
        $fw = fwrite($fp, "$person\n $password\n");
        fclose($fp);
    }
    echo $person;

    echo "&usuarios=";

    $lines = file($users_file);
    $a = count($lines);

    /*        invert user list        */
    /*        -------------------        */
    for($i = $a; $i >= 0 ;$i=$i-2){
        $estate_to_see=trim(strval($lines[$i+1]));
        if(($estate_to_see!="kicked") and ($estate_to_see!="banned")){
            echo $lines[$i];
        }
    }

}else{

      //write exit user message
      //-----------------------

      $text_to_write = date ("(H:i:s)",time()+$correct_time)." ".$person." : ".$bye_user."\n";//compound single message
      $fp = fopen($chat_file_ok, "a");
      $fw = fwrite($fp, "$text_to_write");// message is appended to the msg.txt file
      fclose($fp);

      //delete user and password
      //------------------------

      $text_string = join ('', file ($users_file));
      $new_list= preg_replace ("/$person(0|[1-9][0-9]*)*\n $password\n/", "", $text_string);
      $fp = fopen($users_file, "w");
      $fw = fwrite($fp, $new_list);
      fclose($fp);

      if((strval(trim($new_list))=="") and ($delete_empty_room=="yes")){//if not users delete messages
          $fu = fopen($chat_file_ok, "w");
          $blanktext="";
          $fwu = fwrite($fu, $blanktext);
          fclose($fu);
      }

      // window for dimissed user
      //-------------------------
      if($valid_user == "kicked"){
         $bye_string=$bye_kicked_user;
      }

      if($valid_user == "banned"){
         $bye_string=$bye_banned_user;
      }
      echo "<html><head><title>".htmlentities($bye_string,ENT_QUOTES,'ISO-8859-1')." ".$person;
      unset($_SESSION['detail']);
      unset($_SESSION['text_entree']);
      ?></title>
       <SCRIPT language=javascript>
        function Quit() {
                top.close();
        }
        </SCRIPT>
        </head>
        <body  bgcolor="#FFFFFF" onload="javascript:setTimeout('Quit()',1500)";>
      <?php
    echo "<CENTER><TABLE bgColor='#298CA0' cellspacing='2'><TR><TD>";
    echo "<TABLE bgColor='#FFFFFF' cellspacing='1'  cellpadding='0' width='100%'>";
    echo "<TR><TD background=\"$adresse_http/images/fond_titre_table.jpg\" colspan='2' height='34' align='center' valign='center'><Font face=\"Arial\" size='3' color='#FFFFFF'><B>$intro_admin_title</B></FONT></TD></TR>";
    echo "<TR height = '50'><TD align ='center' valign ='center'>";
    echo "<font face=\"Verdana, Arial, Helvetica, sans-serif\" color= marroon size=\"3\"><B>".htmlentities($bye_string,ENT_QUOTES,'ISO-8859-1')." ".$password." ".$person;
    echo "</B></TD></TR></TABLE></TD></TR></TABLE>";
    echo "</body></html>";
}
?>