<table border="1" cellspacing="0" cellpadding="3">
<tr>
  <td colspan="2" align="center" valign="middle" bgcolor="#000080"><font face="Arial,Helvetica" color="#FFFFFF"><b>Latest Phorum Version</b></font></td>
</tr>
<tr>
  <td valign="middle" bgcolor="#FFFFFF"><font face="Arial,Helvetica">Latest Version:</font></td>
  <td valign="middle" bgcolor="#FFFFFF"><?php echo $ver_arr[0]; ?></td>
</tr>
<tr>
  <td valign="middle" bgcolor="#FFFFFF"><font face="Arial,Helvetica">Release Date:</font></td>
  <td valign="middle" bgcolor="#FFFFFF"><?php echo $ver_arr[1]; ?></td>
</tr>
<tr>
  <td valign="middle" bgcolor="#FFFFFF"><font face="Arial,Helvetica">Download Locations:</font></td>
  <td valign="middle" bgcolor="#FFFFFF"><?php
$cnt=count($ver_arr);
for($x=2;$x<$cnt;$x++){
  $url=$ver_arr[$x];
  echo "<a href=\"$url\">$url</a>\n<br>";
}
?></td>
</tr>
</table>

