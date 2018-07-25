<?php
if (!isset($_SESSION)) session_start();
  $p_author = str_replace("\"","&quot;",$p_author);
  $p_subject = str_replace("\"","&quot;",$p_subject);

if (!$nolog){
  $requete = mysql_query("select * from utilisateur where util_cdn=$id_user");
  $nom_user = mysql_result($requete,0,"util_nom_lb");
  $prenom_user = mysql_result($requete,0,"util_prenom_lb");
  $email = mysql_result($requete,0,"util_email_lb");
}
  $name_cookie="phorum_name";
  if(isset($$name_cookie) && empty($author)){
    $author=$prenom_user." ".$nom_user;
  }
  elseif(!isset($author)){

    $author=$prenom_user." ".$nom_user;
  }

  $email_cookie="phorum_email";
  if(isset($$email_cookie) && empty($email)){
    $email=$email_user;
  }
  elseif(!isset($email)){
    $email=$email_user;
  }

  if(get_cfg_var("magic_quotes_gpc")){
    $email=stripslashes($email);
    $author=stripslashes($author);
    if(!empty($subject)) $subject=stripslashes($subject);
    $body=stripslashes($body);
  }

  if($read!=false){
    $caption = $lReplyMessage;
    if(!strstr( $qsubject,"re:")){
      $p_subject="Re: ".$qsubject;
    }
    else{
      $p_subject= $qsubject;
    }

    $parent=$id;
    if(!$$phflat){
      $quote = undo_htmlspecialchars($qauthor) . " $lWrote:\n";
      $quote .= textwrap("\n$qbody", 63, "\n", "> ") . "\n";
      $quote = htmlspecialchars($quote,ENT_QUOTES,'ISO-8859-1');
      $quote_button="&nbsp;&nbsp;<input type=\"Hidden\" name=\"hide\" value=\"".$quote."\">".
                    "<input tabindex=\"100\" type=\"Button\" name=\"quote\" value=\"$lQuote\" ".
                    "onClick=\"this.form.body.value=this.form.body.value + this.form.hide.value; this.form.hide.value='';\">";
    }
  }
  else{
    $caption = $lStartTopic;
    $p_subject=$subject;
    $p_body=$body;
  }
  $p_author=$author;
  $p_email=$email;

  if($IsError && $action){
    echo "<p><b>$IsError</b>";
  }
?>
<?php
  if ($ForumAllowUploads == 'Y' && !empty($uploadDir)) {
    $enctype = "multipart/form-data";
  } else {
    $enctype = "application/x-www-form-urlencoded";
  }
?>
<form action="<?php echo "$post_page.$ext"; ?>" method="post" enctype="<?php echo $enctype ?>">
<input type="Hidden" name="t" value="<?php  echo $thread; ?>">
<input type="Hidden" name="a" value="post">
<input type="Hidden" name="retour" value="2">
<input type="Hidden" name="f" value="<?php echo $num; ?>">
<input type="Hidden" name="collapse" value="0">
<input type="Hidden" name="p" value="<?php echo $parent; ?>">
<?php echo $PostVars; ?>
<table cellspacing="0" cellpadding="2" border="0">
<tr>
    <td colspan="2" <?php echo bgcolor($ForumNavColor); ?>>
      <table cellspacing="0" cellpadding="1" border="0">
        <tr>
          <td><div id='sequence'><?php echo $nav; ?></div></td>
        </tr>
      </table>
    </td>
</tr>
<tr>
    <td height="21" colspan="2" <?php echo bgcolor($ForumTableHeaderColor); ?>><FONT color="<?php echo $ForumTableHeaderFontColor; ?>">&nbsp;<?php echo $caption; ?></font></td>
</tr>
<tr>
    <td <?php echo bgcolor($ForumTableBodyColor1); ?> nowrap><font color="<?php echo $ForumTableBodyFontColor1; ?>">&nbsp;<?php echo $lFormName;?>:</font></td>
    <td <?php echo bgcolor($ForumTableBodyColor1); ?>><input type="Text" class='INPUT' name="author" size="30" maxlength="30" value="<?php echo $p_author; ?>"></td>
</tr>
<tr>
    <td <?php echo bgcolor($ForumTableBodyColor1); ?> nowrap><font color="<?php echo $ForumTableBodyFontColor1; ?>">&nbsp;<?php echo $lFormEmail;?>:</font></td>
    <td <?php echo bgcolor($ForumTableBodyColor1); ?>><input type="Text" class='INPUT' name="email" size="30" maxlength="200" value="<?php echo $p_email; ?>"></td>
</tr>
<tr>
    <td <?php echo bgcolor($ForumTableBodyColor1); ?> nowrap><font color="<?php echo $ForumTableBodyFontColor1; ?>">&nbsp;<?php echo $lFormSubject;?>:</font></td>
    <td <?php echo bgcolor($ForumTableBodyColor1); ?>><input type="Text" class='INPUT' name="subject" size="30" maxlength="255" value="<?php echo $p_subject; ?>"></td>
</tr>
<?php
        if ($ForumAllowUploads == 'Y' AND $uploadDir != '') {
                print "<tr>\n";
                print '    <td ' . bgcolor($ForumTableBodyColor1) . ' nowrap style="font-family:arial;font-size:12px;"><font color="' . $TableBodyFontColor1 . '">&nbsp;' . $lFormAttachment . '</font></td>';
                print '    <td ' . bgcolor($ForumTableBodyColor1) . ' style="font-family:arial;font-size:12px;" title="Seuls les caractères alphanumériques sont autorisés"><input type="File" class="INPUT" name="attachment" size="30" maxlength="90"></td>';
                print "</tr>\n";
        }
?>
<tr>
    <td <?php echo bgcolor($ForumTableBodyColor1); ?> colspan=2 width="100%" nowrap align="left"><table cellpadding="5" cellspacing="0" border="0"><tr><td align="CENTER" valign="TOP"><font face="courier"><textarea name="body" cols="65" rows="8" wrap="VIRTUAL"><?php echo $p_body; ?></textarea></font></td></tr></table></td>
</tr>
<?php if($ForumModeration!="a"){ ?>
<?php } ?>
<tr>
    <td width="100%" colspan="2" align="RIGHT" <?php echo bgcolor($ForumTableBodyColor1); ?>><?php echo $quote_button; ?>&nbsp;<input type="Submit" name="post" value=" <?php echo $lFormPost;?> ">&nbsp;<br><img src="images/trans.gif" width=3 height=3 border=0></td>
</tr>
</table>
</form>
