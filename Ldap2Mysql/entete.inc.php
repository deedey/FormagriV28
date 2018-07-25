<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title></title>
<meta http-equiv="content-type" content="charset=iso-8859-1" />
<link rel="stylesheet" type="text/css" href="css/ldap2Mysql.css" />
</head>
<?php
$bkg = GetDataField($connect,"select param_etat_lb from param_foad where param_typ_lb = 'bkg'","param_etat_lb");
$image = GetDataField($connect,"select param_etat_lb from param_foad where param_cdn=1","param_etat_lb");
$i_body= "<BODY bgcolor=\"$bkg\" marginwidth='0' marginheight='0' leftmargin='0'";
echo $i_body.">";
echo "&nbsp;<P><CENTER><TABLE bgColor='#298CA0' cellspacing='2' cellpadding='0' width='90%'><TR><TD>";
echo "<TABLE bgColor='#FFFFFF' cellspacing='1' cellpadding='0' width='100%'>";
echo "<TR><TD background=\"../images/fond_titre_table.jpg\" colspan='3' height='40' align='center' valign='center'>".
     "<span style=\"Font-size:17px; font-family:arial; font-weight:bold; color:#FFFFFF\">$mess_auth_titre</SPAN></TD></TR>".
     "</TR><TR><TD><TABLE bgColor='#FFFFFF' cellspacing='1' cellpadding='8' width='100%'><TR><TD>";
?>