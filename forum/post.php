<?php
if (!isset($_SESSION)) session_start();
//echo "<pre>";print_r($_POST);echo "</pre>";
  //echo "<script type='text/javascript' src='".$_SESSION['monURI']."/forum/SCORMDriver/auto-scripts/AutoBookmark.js'></script>";
  require "common.php";
  require "$include_path/post.php";
  $thread=$t;
  $action=$a;
  $id=$i;
  $parent=$p;

  if($num==0 || $ForumName==''){
    Header("Location: $forum_url?$GetVars");
    exit;
  }

  $ip = getenv('REMOTE_HOST');
  if(!$ip){
    $ip = getenv('REMOTE_ADDR');
  }
  if(!$ip){
    $ip = $REMOTE_ADDR;
  }
  if(!$ip){
    $ip = $REMOTE_HOST;
  }

  $host = @GetHostByAddr($ip);

  $IsError = @check_data($host, $author, $subject, $body, $email);

  if ($ForumAllowUploads == 'Y' && isset($_FILES['attachment']['name']) && trim($_FILES['attachment']['name']) != '' && trim($uploadDir != ''))
  {
    if (!preg_match("/^[-A-Za-z0-9_.]+$/", trim($_FILES['attachment']['name'])))
    {
      $IsError=$lInvalidFile;
    } elseif (file_exists($uploadDir.'/'.$ForumTableName.'/'.$_FILES['attachment']['name']))
    {
      $IsError=$lFileExists;
    }
  }
  else{
    $attachment="";
    $_FILES['attachment']['name']="";
  }

  if($IsError || !$action){
    if(file_exists("$include_path/header_$ForumConfigSuffix.php")){
      include "$include_path/header_$ForumConfigSuffix.php";
    }
    else{
      include "$include_path/header.php";
    }

  echo '<table width="'.$ForumTableWidth.'" border="0" cellspacing="0" cellpadding="3">'.
       '<tr><td bgcolor='.$ForumNavColor.' valign="TOP" nowrap><font color="$ForumNavFontColor">';
  echo "<div id='accueil' style=\"float:left;margin-left:2px;margin-right:10px;\">".
         "<a href=\"$list_page.$ext?f=$num\" class= 'bouton_new'>$mess_acc</a>".
         "</div>";
  if ($f == 3 && $typ_user == 'APPRENANT')
     echo aide_div("forum_libre_apprenant",0,0,0,0);
  elseif ($f == 3 && $typ_user != 'APPRENANT')
     echo aide_div("forum_libre_formateur",0,0,0,0);
  elseif ($f > 5 && $typ_user == 'APPRENANT')
     echo aide_div("forum_apprenant",0,0,0,0);
  elseif ($f > 5 && $typ_user != 'APPRENANT')
     echo aide_div("forum_formateur",0,0,0,0);
  echo "</font></td></tr></table>";
    include "$include_path/form.php";
    if(file_exists("$include_path/footer_$ForumConfigSuffix.php")){
      include "$include_path/footer_$ForumConfigSuffix.php";
    }
    else{
      include "$include_path/footer.php";
    }
    exit();
  }

  $author=trim($author);
  $subject=trim($subject);
  $email=trim($email);
  $body=chop($body);

  if($UseCookies){
    $name_cookie="phorum_name";
    $email_cookie="phorum_email";

    if((!IsSet($$name_cookie)) || ($$name_cookie != $author)) {
      SetCookie($name_cookie,stripslashes($author),time()+ 31536000);
    }
    if((!IsSet($$email_cookie)) || ($$email_cookie != $email)) {
      SetCookie($email_cookie,stripslashes($email),time()+ 31536000);
    }
  }

  list($author, $subject, $email, $body) = censor($author, $subject, $email, $body);

  if(!get_magic_quotes_gpc()){
    $author = addslashes($author);
    $email = addslashes($email);
    $subject = addslashes($subject);
    $body = addslashes($body);
  }

  $datestamp = date("Y-m-d H:i:s");

  $plain_author=stripslashes($author);
  $plain_subject=stripslashes(preg_replace("/<[^>]+>/", "", $subject));
  $plain_body=stripslashes(preg_replace("/<[^>]+>/", "", $body));

  $author = htmlspecialchars($author,ENT_QUOTES,'ISO-8859-1');
  $email = htmlspecialchars($email);
  $subject = htmlspecialchars($subject,ENT_QUOTES,'ISO-8859-1');

  // Attachment handling:
  if ($ForumAllowUploads == 'Y' && trim($_FILES['attachment']['name']) != '' && trim($uploadDir != '')) {
    $org_attachment = $_FILES['attachment']['name'];
    $new_name = $uploadDir.'/'.$ForumTableName.'/'.$org_attachment;
    $LienTincan = $uploadUrl.'/'.$ForumTableName.'/'.$org_attachment;
    if (!file_exists($new_name))
    {
      move_uploaded_file($_FILES['attachment']['tmp_name'], $new_name);
    } else {
      print $lFileExists;
      exit();
    }
  } else {
    $org_attachment = '';
  }

  if(($email==$ForumModPass && $ForumModPass!="") || ($email==$Password && $Password!="")){
    $ForumModeration='';
    $email=$ForumModEmail;
    $author = "<b>$author</b>";
    $subject = "<b>$subject</b>";
    $body="<HTML>$body</HTML>";
    $host="<b>$ForumStaffHost</b>";
  }
  else{
    $body=preg_replace("/<\/HTML>/i", "", $body);
    if($ForumAllowHTML=="Y"){
      $body="<HTML>$body</HTML>";
    }
  }

  if (!check_dup()) {
    // generate a message id for the email if needed.
    $msgid="<".md5(uniqid(rand())).".$ForumModEmail>";
    // This will add the message to the database, and email the
    // moderator if required.
    $error = post_to_database();
    if (!empty($error)) {
      echo $error;
      exit();
    }

    // This will send email to the mailing list, if applicable,
    // and send email replies to earlier posters, if necessary.
    // Note that when posting to a mailing list, active moderation
    // does not apply.
    post_to_email();
  }
  if (isset($new_name) && strstr($new_name,$uploadDir))
     $Poster = urlencode("$subject|||".$body."|||$LienTincan");
  else
     $Poster = urlencode("$subject|||".$body."|||--");
Header ("Location: $list_page.$ext?f=$num&t=$t$GetVars&collapse=$collapse&retour=$retour&Poster=$Poster");
?>
