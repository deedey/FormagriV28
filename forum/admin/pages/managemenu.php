<?php /* Forum/Folder Menu */?>
<script language="JavaScript" type="text/javascript">
  function dropforum(url, folder){
    if(folder){
      ans=window.confirm("You are about to drop this folder.  All sub folders and sub forums of this folder will be dropped also.  Do you want to continue?");
    }
    else{
      ans=window.confirm("You are about to drop this forum.  Do you want to continue?");
    }
    if(ans){
      window.location.replace(url);
    }
  }
</script>
<table cellspacing="0" cellpadding="3" border="1">
<tr>
    <td align="center" bgcolor="#5b8bab"><font size='3' face="Arial,Helvetica" color="#FFFFFF"><B><?php echo $ForumName; ?></B></font></td>
</tr>
<?php
  if($ForumFolder==1){
    $uword="Folder";
    $lword="folder";
  }
  else{
    $uword="Forum";
    $lword="forum";
  }
?>
<tr>
<td bgcolor="#FFFFFF">
<font face="Arial,Helvetica">
<?php if($ForumFolder!="1"){ ?>
<a href="<?php echo $myname; ?>?page=easyadmin&num=<?php echo $num."\">".$easyadmin; ?></a><br>
<a href="<?php echo $myname; ?>?page=recentadmin&num=<?php echo $num."\">".$recentadmin; ?></a><br>
<a href="<?php echo $myname; ?>?page=quickedit&num=<?php echo $num."\">".$quickedit; ?></a><br>
<a href="<?php echo $myname; ?>?page=quickdel&num=<?php echo $num."\">".$quickdelete; ?></a><br>
<a href="<?php echo $myname; ?>?page=quickapp&num=<?php echo $num."\">".$quickapprove; ?></a><br>
<a href="<?php echo $myname; ?>?page=datedel&num=<?php echo $num."\">".$deletebydate; ?></a><br>
<?php } ?>
<?php if($ForumActive){ ?>
<a href="<?php echo $myname; ?>?action=deactivate&page=managemenu&num=<?php echo $num."\">".$hide; ?></a><br>
<?php }else{ ?>
<a href="<?php echo $myname; ?>?action=activate&page=managemenu&num=<?php echo $num."\">".$makevisible; ?></a><br>
<?php } ?>
</font>
</td>
</tr>
</table>