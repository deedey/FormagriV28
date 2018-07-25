<?php
if (!isset($_SESSION)) session_start();
include('../../include/UrlParam2PhpVar.inc.php');
require "../../admin.inc.php";
require '../../fonction.inc.php';
require ("required/config$lg.php");
dbConnect();
if ($typ_user == 'ADMINISTRATEUR'){
echo "<script language='JavaScript'>
      document.location.replace('adminusers.php?passord=safia&name=dey');
      </script>";
exit();
}
?>
<html>
<head>
<title><?php echo htmlentities($intro_admin_title,ENT_QUOTES,'ISO-8859-1')?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body bgcolor="#FFFFFF" text="#000000">
<form name="form1" method="post" action="adminusers.php">
  <font face="Verdana, Arial, Helvetica, sans-serif" size="1"> </font>
  <table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr>
      <td bgcolor="#999999" align="right"><font face="Verdana, Arial, Helvetica, sans-serif" size="1" color="#FFFFFF"><b><font color="#666666"><?php echo htmlentities($intro_admin_name,ENT_QUOTES,'ISO-8859-1')?></font>&nbsp;</b></font></td>
      <td bgcolor="#CCCCCC"><font face="Verdana, Arial, Helvetica, sans-serif" size="1">
        <input type="text" name="name" size="10" maxlength="12" value="<?php echo $name?>">
        </font></td>
      <td bgcolor="#9C9C9C" align="right"><font face="Verdana, Arial, Helvetica, sans-serif" size="1">
        <font color="#FFFFFF"><b>&nbsp;<font color="#666666"><?php echo htmlentities($intro_admin_password,ENT_QUOTES,'ISO-8859-1')?></font>&nbsp;</b></font></font></td>
      <td bgcolor="#CCCCCC"><font face="Verdana, Arial, Helvetica, sans-serif" size="1">
        <input type="password" name="passord" size="10" maxlength="12" value="<?php echo $passord?>">
        </font></td>
      <td><font face="Verdana, Arial, Helvetica, sans-serif" size="1">
        <input type="submit" name="Submit" value="<?php echo htmlentities($intro_admin_button,ENT_QUOTES,'ISO-8859-1')?>">
        </font></td>
    </tr>
  </table>
  <font face="Verdana, Arial, Helvetica, sans-serif" size="1"> </font>
</form>

</body>
</html>
