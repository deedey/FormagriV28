<form action="<?php echo $myname; ?>" method="POST">
<input type="Hidden" name="page" value="setup">
<input type="Hidden" name="action" value="files">
<table border="1" cellspacing="0" cellpadding="3">
<tr>
  <td colspan="2" align="center" valign="middle" bgcolor="#000080"><font face="Arial,Helvetica" color="#FFFFFF"><b>File/Path Settings</b></font></td>
</tr>
<tr>
  <td valign="middle" bgcolor="#FFFFFF"><font face="Arial,Helvetica">Forum URL:</font></td>
  <td valign="middle" bgcolor="#FFFFFF"><input type="Text" name="new_forum_url" value="<?php echo $forum_url; ?>" size="10" style="width: 300px;" class="TEXT"></td>
</tr>
<tr>
  <td valign="middle" bgcolor="#FFFFFF"><font face="Arial,Helvetica">Admin URL:</font></td>
  <td valign="middle" bgcolor="#FFFFFF"><input type="Text" name="new_admin_url" value="<?php echo $admin_url; ?>" size="10" style="width: 300px;" class="TEXT"></td>
</tr>
<tr>
  <td valign="middle" bgcolor="#FFFFFF"><font face="Arial,Helvetica">File Extension:</font></td>
  <td valign="middle" bgcolor="#FFFFFF"><input type="Text" name="new_ext" value="<?php echo $ext; ?>" size="10" style="width: 200px;" class="TEXT"></td>
</tr>
<tr>
  <td valign="middle" bgcolor="#FFFFFF"><font face="Arial,Helvetica">Attachment Directory (full path, leave off trailing /):<BR><SMALL>leave empty if you don't allow attachments in any forum</SMALL></font></td>
  <td valign="middle" bgcolor="#FFFFFF"><input type="Text" name="new_uploadDir" value="<?php echo $uploadDir; ?>" size="10" style="width: 200px;" class="TEXT"><input type="hidden" name="new_uploadUrl" value="<?php echo $uploadUrl; ?>"></td>
</tr>
<tr>
  <td valign="middle" bgcolor="#FFFFFF"><font face="Arial,Helvetica">Forum List Page Name:</font></td>
  <td valign="middle" bgcolor="#FFFFFF"><input type="Text" name="new_forum_page" value="<?php echo $forum_page; ?>" size="10" style="width: 200px;" class="TEXT"></td>
</tr>
<tr>
  <td valign="middle" bgcolor="#FFFFFF"><font face="Arial,Helvetica">Message List Page Name:</font></td>
  <td valign="middle" bgcolor="#FFFFFF"><input type="Text" name="new_list_page" value="<?php echo $list_page; ?>" size="10" style="width: 200px;" class="TEXT"></td>
</tr>
<tr>
  <td valign="middle" bgcolor="#FFFFFF"><font face="Arial,Helvetica">Search Page Name:</font></td>
  <td valign="middle" bgcolor="#FFFFFF"><input type="Text" name="new_search_page" value="<?php echo $search_page; ?>" size="10" style="width: 200px;" class="TEXT"></td>
</tr>
<tr>
  <td valign="middle" bgcolor="#FFFFFF"><font face="Arial,Helvetica">Read Page Name:</font></td>
  <td valign="middle" bgcolor="#FFFFFF"><input type="Text" name="new_read_page" value="<?php echo $read_page; ?>" size="10" style="width: 200px;" class="TEXT"></td>
</tr>
<tr>
  <td valign="middle" bgcolor="#FFFFFF"><font face="Arial,Helvetica">Post Page Name:</font></td>
  <td valign="middle" bgcolor="#FFFFFF"><input type="Text" name="new_post_page" value="<?php echo $post_page; ?>" size="10" style="width: 200px;" class="TEXT"></td>
</tr>
<tr>
  <td valign="middle" bgcolor="#FFFFFF"><font face="Arial,Helvetica">Violation Page Name:</font></td>
  <td valign="middle" bgcolor="#FFFFFF"><input type="Text" name="new_violation_page" value="<?php echo $violation_page; ?>" size="10" style="width: 200px;" class="TEXT"></td>
</tr>
<tr>
  <td valign="middle" bgcolor="#FFFFFF"><font face="Arial,Helvetica">Down Page Name:</font></td>
  <td valign="middle" bgcolor="#FFFFFF"><input type="Text" name="new_down_page" value="<?php echo $down_page; ?>" size="10" style="width: 200px;" class="TEXT"></td>
</tr>
</table>
<br>
<center><input type="Submit" name="submit" value="Update" class="BUTTON"></center>
</form>
