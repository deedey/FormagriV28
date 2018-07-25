<form action="<?php echo $myname; ?>" method="POST">
<input type="Hidden" name="page" value="setup">
<input type="Hidden" name="action" value="global">
<table border="1" cellspacing="0" cellpadding="3">
<tr>
  <td colspan="2" align="center" valign="middle" bgcolor="#000080"><font face="Arial,Helvetica" color="#FFFFFF"><b>Global Settings</b></font></td>
</tr>
<tr>
  <td valign="middle" bgcolor="#FFFFFF"><font face="Arial,Helvetica">Default Messages Per Page:</font></td>
  <td valign="middle" bgcolor="#FFFFFF"><input type="Text" name="new_DefaultDisplay" value="<?php echo $DefaultDisplay; ?>" size="10" style="width: 200px;" class="TEXT"></td>
</tr>
<tr>
  <td valign="middle" bgcolor="#FFFFFF"><font face="Arial,Helvetica">Default Email:</font></td>
  <td valign="middle" bgcolor="#FFFFFF"><input type="Text" name="new_DefaultEmail" value="<?php echo $DefaultEmail; ?>" size="10" style="width: 200px;" class="TEXT"></td>
</tr>
<tr>
  <td valign="middle" bgcolor="#FFFFFF"><font face="Arial,Helvetica">PhorumMail
Code:</font></td>
  <td valign="middle" bgcolor="#FFFFFF"><input type="Text" name="new_PhorumMailCode" value="<?php echo $PhorumMailCode; ?>" size="10" style="width: 200px;" class="TEXT"></td>
</tr>
<tr>
  <td valign="middle" bgcolor="#FFFFFF"><font face="Arial,Helvetica">Cookies:</font></td>
  <td valign="middle" bgcolor="#FFFFFF"><select name="new_UseCookies" class=big>
<option value="0" <?php if($UseCookies==0) echo "selected"; ?>>Do Not Use Cookies</option>
<option value="1" <?php if($UseCookies==1) echo "selected"; ?>>Use Cookies</option>
</select></td>
</tr>
<tr>
  <td valign="middle" bgcolor="#FFFFFF"><font face="Arial,Helvetica">Sorting:</font></td>
  <td valign="middle" bgcolor="#FFFFFF"><select name="new_SortForums" class=big>
<option value="0" <?php if($SortForums==0) echo "selected"; ?>>Do Not Sort Forums</option>
<option value="1" <?php if($SortForums==1) echo "selected"; ?>>Sort Forums</option>
</select></td>
</tr>
<tr>
  <td valign="middle" bgcolor="#FFFFFF"><font face="Arial,Helvetica">Default Language:</font></td>
  <td valign="middle" bgcolor="#FFFFFF"><select name="new_default_lang">
<?php
$aryLangs = array();
$strLangDir = "lang/";
$dirCurrent = dir($strLangDir);
while($strFile=$dirCurrent->read()) {
  echo $strLangDir.$strFile;
  if (is_file($strLangDir.$strFile)) {
    $aryLangs[] = $strLangDir.$strFile;
  }
}
$dirCurrent->close();

if (count($aryLangs) > 1) { sort ($aryLangs); }

$file = current($aryLangs);
while ($file) {
  if($file!="$strLangDir"."blank.php"){
    $intStartLang = strpos($file, '/') + 1;
    $intLengthLang = strpos($file, '.') - $intStartLang;
    $text=ucwords(substr($file,$intStartLang,$intLengthLang));
    echo "<option value=\"$file\"";
    if($file==$default_lang) echo ' selected';
    echo ">$text</option>\n";
  }
  $file = next($aryLangs);
}
?></select></td>
</tr>
</table>
<br>
<center><input type="Submit" name="submit" value="Update" class="BUTTON"></center>
</form>
