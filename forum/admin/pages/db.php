<form action="<?php echo $myname; ?>" method="POST">
<input type="Hidden" name="page" value="setup">
<input type="Hidden" name="action" value="db">
<table border="1" cellspacing="0" cellpadding="3">
<tr>
  <td colspan="2" align="center" valign="middle" bgcolor="#000080"><font face="Arial,Helvetica" color="#FFFFFF"><b>Database Settings</b></font></td>
</tr>
<tr>
  <td valign="middle" bgcolor="#FFFFFF"><font face="Arial,Helvetica">Server Name:</font></td>
  <td valign="middle" bgcolor="#FFFFFF"><input type="Text" name="new_dbServer" value="<?php echo $dbServer; ?>" size="10" style="width: 300px;" class="TEXT"></td>
</tr>
<tr>
  <td valign="middle" bgcolor="#FFFFFF"><font face="Arial,Helvetica">Database Name:</font></td>
  <td valign="middle" bgcolor="#FFFFFF"><input type="Text" name="new_dbName" value="<?php echo $dbName; ?>" size="10" style="width: 300px;" class="TEXT"></td>
</tr>
<tr>
  <td valign="middle" bgcolor="#FFFFFF"><font face="Arial,Helvetica">User Name:</font></td>
  <td valign="middle" bgcolor="#FFFFFF"><input type="Text" name="new_dbUser" value="<?php echo $dbUser; ?>" size="10" style="width: 300px;" class="TEXT"></td>
</tr>
<tr>
  <td valign="middle" bgcolor="#FFFFFF"><font face="Arial,Helvetica">Password:</font></td>
  <td valign="middle" bgcolor="#FFFFFF"><input type="Text" name="new_dbPass" value="<?php echo $dbPass; ?>" size="10" style="width: 300px;" class="TEXT"></td>
</tr>
</table>
<br>
<center><input type="Submit" name="submit" value="Update" class="BUTTON"></center><p>
<b>NOTE:  If SQL Safe Mode is in use on your server, leave the username and password emtpy.</b>
</form>