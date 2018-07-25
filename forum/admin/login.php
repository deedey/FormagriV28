<?php
if (!isset($_SESSION)) session_start();

  // login.php
  if(!empty($HTTP_COOKIE_VARS["phorum_logged_in"])){

    $phorum_logged_in=$HTTP_COOKIE_VARS["phorum_logged_in"];

  }
  $sSQL="Select password from ".$pho_main." where id=$num";
  $q->query($DB, $sSQL);
  $row=$q->getrow();
  $phorum_password = $row["password"];

  $phorum_logged_in=$num;
  if(!isset($phorum_logged_in)) $phorum_logged_in=0;

  if(!isset($phorum_password)) $phorum_password='';

  if(!isset($login)) $login=0;

  function show_login($status){

    GLOBAL $myname, $admindir, $phorum_password, $phorum_logged_in, $loginnum;

    GLOBAL $phorumver, $HTTP_USER_AGENT, $DB, $fullaccess, $q, $use_security;

    GLOBAL $forum_url, $QUERY_STRING, $page, $num, $logout, $forum_page, $ext;

    GLOBAL $pho_main, $admin_url;

    $title='Phorum Admin Login';

    $login=1;

    include "$admindir/header.php";

?>

<table border="0" cellspacing="0" cellpadding="3">

<tr>

<td align="LEFT" valign="MIDDLE">

<?php

  $URL=$myname;echo $URL;

  if(!empty($QUERY_STRING) && !isset($logout)) $URL.="?$QUERY_STRING";

?>

<form action="<?php echo $URL; ?>" method="POST">

<input type="hidden" name="login" value="1">

<?php if($status==1) { ?>

<b>Vous avez inséré un mauvais mot de passe.</b><p>

<?php } ?>

<?php if(empty($num)) { ?>

<font size='2' face="Arial,Helvetica"><b>Type d'accès/Forum:</b></font>

<select name="num">

          <option value="0">Global</option>

<?php

  if(isset($q)){

    $sSQL="Select id, name from ".$pho_main." order by name";

    $q->query($DB, $sSQL);

    $row=$q->getrow();

    while($row){

      echo "<option value=\"$row[id]\">$row[name]</option>\n";

      $row=$q->getrow();

    }

  }

?>

</select><p>

<?php } ?>

<font size='2' face="Arial,Helvetica"><b>Mot de passe:</b></font>

<input type="password" name="phorum_password" value="<?php echo $phorum_password; ?>" size="20" maxlength="20"><input type="submit" value="Valider">

</form>

</td></tr></table>

<?php

    include "$admindir/footer.php";

    exit();

  }



  function log_in_user($user, $num=0){

    global $phorum_logged_in,$admindir;

    $possible="ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz123456789012345678901234567890";

    srand(hexdec(uniqid(''))%getrandmax());

    $str="";

    while(strlen($str)<30) {

      $str.=substr($possible,(rand()%(strlen($possible))),1);

    }

    $cookieval="$num.$str";

    setcookie("phorum_logged_in", $cookieval);

    $phorum_logged_in=$cookieval;

    $fp=fopen("$admindir/forums/session$num.php", "w");

    fputs($fp, "<?php \$phorum_session=\"$phorum_logged_in\"; ?>");

    fclose($fp);

    $user_id=$user;

  }



  function check_login(){

    GLOBAL $logout, $login, $loginnum, $phorum_logged_in, $phorum_password, $Password, $ForumName, $ForumModPass, $page, $num, $modpages, $action, $admindir, $fullaccess, $forum_id;



    $mynum = $num;

    if(isset($forum_id) && ($forum_id != 0) && ($num == 0)) {

      $mynum = $forum_id;

    }



    if(isset($phorum_logged_in)){

      @list($loginnum, $junk)=@explode(".", $phorum_logged_in);

    }

    if(isset($logout)){

      $phorum_logged_in=0;

      setcookie("phorum_logged_in", "0");

      show_login(0);

    }

    elseif(isset($phorum_logged_in)){

      if($fullaccess==false && $loginnum!=0){

        unset($phorum_logged_in);

      }

      else{

        if(empty($loginnum)) $loginnum="0";

        if(file_exists("$admindir/forums/session$loginnum.php")){

          $phorum_session="";

          include "$admindir/forums/session$loginnum.php";

          if($phorum_logged_in!=$phorum_session){

            unset($phorum_logged_in);

          }

          elseif($loginnum!=$mynum && $loginnum!=0){

            unset($phorum_logged_in);

          }

        }

        else{

          unset($phorum_logged_in);

        }

      }

    }

    if(empty($phorum_logged_in)){

      if($phorum_password==$Password){

        log_in_user($login);

      }

      elseif(empty($ForumModPass)){

        show_login($login);

      }

      elseif($phorum_password==$ForumModPass){

        log_in_user($login, $num);

        $loginnum=$num;

        $page="managemenu";

      }

      else{

        show_login($login);

      }

    }

  }





?>