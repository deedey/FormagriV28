<?php  // Edit Message

  $sSQL="Select author, email, subject, attachment, body from $ForumTableName as t, $ForumTableName"."_bodies as b where t.id=b.id and t.id=$id";

  $q->query($DB, $sSQL);

  $mtext = $q->getrow();

  if (isset($srcpage)) {

    $page = $srcpage;

  } else {

    $page = "managemenu";

  }

?>

<form action="<?php echo $myname; ?>" method="POST">

<input type="Hidden" name="action" value="edit">

<input type="Hidden" name="num" value="<?php echo $num; ?>">

<input type="Hidden" name="id" value="<?php echo $id; ?>">

<input type="Hidden" name="page" value="<?php echo $page; ?>">

<?php

if (isset($mythread)) { ?>

<input type="Hidden" name="mythread" value="<?php echo $mythread; ?>">

<?php

}
echo "<CENTER><TABLE bgColor='#298CA0' cellspacing='2'><TR><TD>";
echo "<TABLE bgColor='#FFFFFF' cellspacing='2' width='100%'>";
echo "<TR><TD background=\"$adresse_http/images/fond_titre_table.jpg\" colspan='2' height='34' align='center' valign='center'><Font size='3' color='#FFFFFF'><B>$mess_gest_forum_form : $ForumName</B></FONT></TD></TR>";
echo "<TR><TD>";
?>
<table cellspacing="0" cellpadding="3" border="0">
<tr>

    <td><font face="Arial,Helvetica"><?php echo $mrc_aut;?> :</font></td>

    <td><font face="Arial,Helvetica"><input type="Text" name="author" value="<?php echo $mtext["author"]; ?>" size="10" style="width: 300px;" class="TEXT"></font></td>

</tr>

<tr>

    <td><font face="Arial,Helvetica"><?php echo $mess_admin_email;?> :</font></td>

    <td><font face="Arial,Helvetica"><input type="Text" name="email" value="<?php echo $mtext["email"]; ?>" size="10" style="width: 300px;" class="TEXT"></font></td>

</tr>

<tr>

    <td><font face="Arial,Helvetica"><?php echo $mess_mail_sujet;?> :</font></td>

    <td><font face="Arial,Helvetica"><input type="Text" name="subject" value="<?php echo $mtext["subject"]; ?>" size="10" style="width: 300px;" class="TEXT"></font></td>

</tr>

<?php

if (!empty($attachment)) {

?>

<INPUT TYPE="hidden" NAME="attachment" VALUE="<?php echo $mtext["attachment"]; ?>">

<tr>

        <td bgcolor="#FFFFFF"><font face="Arial,Helvetica">Attachment:</font></td>

        <td bgcolor="#FFFFFF"><font face="Arial,Helvetica"><?php echo $mtext["attachment"]; ?>

        &nbsp; &nbsp;

        <INPUT TYPE="checkbox" NAME="del_attachment" VALUE="true"> delete attachment

        </font></td>

</tr>

<?php

}

?>

<tr>

    <td colspan=2><textarea name="body" cols="60" rows="20" wrap="VIRTUAL"><?php echo $mtext["body"]; ?></textarea></td>

</tr>

</td>

</tr>

</table>
<center><input type="Submit" name="submit" value="<?php echo $mess_gen_valider;?>" class="BUTTON"></center>

</form>
<?php
echo "</TD></TR></TABLE></TD></TR></TABLE>";
?>