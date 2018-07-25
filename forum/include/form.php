<?php
if (!isset($_SESSION)) session_start();
  $p_author = str_replace("\"","&quot;",$p_author);
  $p_subject = str_replace("\"","&quot;",$p_subject);
  $name_cookie="phorum_name";
if (!$nolog){
  $requete = mysql_query("select * from utilisateur where util_cdn=$id_user");
  $nom_user = mysql_result($requete,0,"util_nom_lb");
  $prenom_user = mysql_result($requete,0,"util_prenom_lb");
  $email = mysql_result($requete,0,"util_email_lb");
}
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

  if($IsError && $action)
  {
//    echo "<p><b>$IsError</b>";
  }
  if ($ForumAllowUploads == 'Y' && !empty($uploadDir))
  {
    $enctype = "multipart/form-data";
  } else {
    $enctype = "application/x-www-form-urlencoded";
  }
?>
<SCRIPT language=JavaScript>
function checkForm(frm) {
  var ErrMsg = "<?php echo $mess_info_no;?>\n";
  var lenInit = ErrMsg.length;
  if (isEmpty(frm.subject)==true)
    ErrMsg += ' - <?php echo $mess_mail_sujet;?>\n';
  if (isEmpty(frm.author)==true)
    ErrMsg += ' - <?php echo $mess_admin_nom." & ".$mess_admin_prenom;?>\n';
  if (isEmpty(frm.email)==true)
    ErrMsg += ' - <?php echo $mess_admin_email;?>\n';
  if (isEmpty(frm.body)==true)
        ErrMsg += 'Contenu\n';
  if (ErrMsg.length > lenInit)
    alert(ErrMsg);
  else
    frm.submit();
}
function isEmpty(elm) {
  var elmstr = elm.value + "";
  if (elmstr.length == 0)
    return true;
  return false;
}
</SCRIPT>
<form name="form1" action="<?php echo "$post_page.$ext"; ?>" method="post" enctype="<?php echo $enctype ?>">
<input type="Hidden" name="t" value="<?php  echo $thread; ?>">
<input type="Hidden" name="a" value="post">
<input type="Hidden" name="retour" value="1">
<input type="Hidden" name="f" value="<?php echo $num; ?>">
<input type="Hidden" name="p" value="<?php echo $parent; ?>">
<input type='hidden' name="email" value="<?php echo $p_email; ?>">
<input type='hidden'  name="author" value="<?php echo $p_author; ?>">
<?php echo $PostVars; ?>
<center><table cellspacing="0" cellpadding="2" border="0">
<tr>
    <td colspan="2" >
      <table cellspacing="0" cellpadding="1" border="0">
        <tr>
          <td><DIV id='sequence'><?php echo $nav; ?></DIV></td>
        </tr>
      </table>
    </td>
</tr>
<tr>
    <td height="21" colspan="2" <?php echo bgcolor($ForumTableHeaderColor); ?>>
    <FONT color="<?php echo $ForumTableHeaderFontColor; ?>">&nbsp;<B><?php echo $caption; ?></B</font></td>
</tr>
<?php
/*
<tr>
    <td colspan=2 nowrap><font color="<?php echo $ForumTableBodyFontColor1; ?>">&nbsp;<?php echo $p_author;?></font></td>
</tr>
*/
?>
<tr>
    <td <?php echo bgcolor($ForumTableBodyColor1); ?> nowrap style="text-align:left;font-family:arial;font-size:12px;">&nbsp;<?php echo $lsjttit;?></td>
    <td <?php echo bgcolor($ForumTableBodyColor1); ?> style="text-align:left;"><input type="Text" class='INPUT' name="subject" size="50" maxlength="255" value="<?php echo $p_subject; ?>"></td>
</tr>
<?php
        if ($ForumAllowUploads == 'Y' AND $uploadDir != '')
        {
                print "<tr>\n";
                print '    <td ' . bgcolor($ForumTableBodyColor1) . ' nowrap style="text-align:left;font-family:arial;font-size:12px;"><span  title="Seuls les caractères alphanumériques sont autorisés"><font color="' . $TableBodyFontColor1 . '">&nbsp;' . $lFormAttachment . '</font></span></td>';
                print '    <td ' . bgcolor($ForumTableBodyColor1) . ' style="text-align:left;font-family:arial;font-size:12px;"><input type="File" class="INPUT" name="attachment" size="30" maxlength="90" title="Seuls les caractères alphanumériques sont autorisés"></td>';
                print "</tr>\n";
        }
?>
<tr>
    <td <?php echo bgcolor($ForumTableBodyColor1); ?> colspan=2 width="100%" nowrap align="left">
    <table cellpadding="0" cellspacing="0" border="0">
    <tr><td valign="TOP">
    <textarea name="body" cols="70" rows="10"><?php echo $p_body; ?></textarea></td>
    </tr></table>
    </td>

</tr>
<?php if($ForumModeration!="a")
{ ?>
<?php } ?>
<tr>
    <td width="100%" colspan="2" align="left" <?php echo bgcolor($ForumTableBodyColor1); ?>>&nbsp;
    <input type="hidden" name="post" value=" <?php echo $lFormPost;?> ">
<?php
   echo "<A HREF=\"javascript:checkForm(document.form1);\" onClick=\"TinyMCE.prototype.triggerSave();\" onmouseover=\"img1.src='../images/fiche_identite/boutvalidb.gif';return true;\"".
        " onmouseout=\"img1.src='../images/fiche_identite/boutvalid.gif'\">".
        "<IMG NAME=\"img1\" SRC=\"../images/fiche_identite/boutvalid.gif\" BORDER='0' onLoad=\"tempImg=new Image(0,0);".
        " tempImg.src='../images/fiche_identite/boutvalidb.gif'\"></A>";
?>
    </td>
</tr>
</form>
</table>

