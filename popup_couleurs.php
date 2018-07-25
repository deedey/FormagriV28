<?php
if (!isset($_SESSION)) session_start();
include ("include/UrlParam2PhpVar.inc.php");
if (!isset($_SESSION['id_user']) || $_SESSION['id_user'] == "")
{
  exit();
}
require 'fonction.inc.php';
require "lang$lg.inc.php";
require "admin.inc.php";
//include ("click_droit.txt");
dbConnect();
$nav="opener.document.";
include ('style.inc.php');
?>
<center>
<table border="0" cellspacing="0" cellpadding="0" bgcolor='#CEE6EC'>
  <tr>
    <td>
    <table cellspacing=1 cellpadding=0 border="1">
        <tr>
          <td width="20" height="20" bgcolor="#000000"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='000000';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#000033"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='000033';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#000066"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='000066';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#000099"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='000099';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#0000CC"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='0000CC';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#0000FF"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='0000FF';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
        </tr>
        <tr>
          <td width="20" height="20" bgcolor="#003300"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='003300';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#003333"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='003333';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#003366"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='003366';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#003399"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='003399';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#0033CC"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='0033CC';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#0033FF"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='0033FF';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
        </tr>
        <tr>
          <td width="20" height="20" bgcolor="#006600"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='006600';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#006633"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='006633';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#006666"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='006666';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#006699"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='006699';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#0066CC"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='0066CC';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#0066FF"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='0066FF';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
        </tr>
        <tr>
          <td width="20" height="20" bgcolor="#009900"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='009900';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#009933"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='009933';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#009966"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='009966';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#009999"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='009999';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#0099CC"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='0099CC';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#0099FF"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='0099FF';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
        </tr>
        <tr>
          <td width="20" height="20" bgcolor="#00CC00"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='00CC00';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#00CC33"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='00CC33';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#00CC66"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='00CC66';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#00CC99"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='00CC99';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#00CCCC"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='00CCCC';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#00CCFF"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='00CCFF';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
        </tr>
        <tr>
          <td width="20" height="20" bgcolor="#00FF00"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='00FF00';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#00FF33"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='00FF33';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#00FF66"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='00FF66';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#00FF99"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='00FF99';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#00FFCC"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='00FFCC';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#00FFFF"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='00FFFF';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
        </tr>
      </table>
        </td>
    <td><table cellspacing=1 cellpadding=0  border="1">
        <tr>
          <td width="20" height="20" bgcolor="#330000"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='330000';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#330033"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='330033';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#330066"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='330066';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#330099"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='330099';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#3300CC"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='3300CC';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#3300FF"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='3300FF';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
        </tr>
        <tr>
          <td width="20" height="20" bgcolor="#333300"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='333300';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#333333"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='333333';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#333366"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='333366';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#333399"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='333399';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#3333CC"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='3333CC';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#3333FF"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='3333FF';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
        </tr>
        <tr>
          <td width="20" height="20" bgcolor="#336600"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='336600';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#336633"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='336633';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#336666"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='336666';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#336699"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='336699';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#3366CC"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='3366CC';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#3366FF"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='3366FF';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
        </tr>
        <tr>
          <td width="20" height="20" bgcolor="#339900"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='339900';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#339933"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='339933';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#339966"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='339966';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#339999"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='339999';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#3399CC"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='3399CC';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#3399FF"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='3399FF';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
        </tr>
        <tr>
          <td width="20" height="20" bgcolor="#33CC00"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='33CC00';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#33CC33"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='33CC33';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#33CC66"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='33CC66';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#33CC99"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='33CC99';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#33CCCC"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='33CCCC';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#33CCFF"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='33CCFF';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
        </tr>
        <tr>
          <td width="20" height="20" bgcolor="#33FF00"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='33FF00';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#33FF33"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='33FF33';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#33FF66"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='33FF66';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#33FF99"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='33FF99';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#33FFCC"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='33FFCC';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#33FFFF"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='33FFFF';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
        </tr>
      </table>
        </td>
    <td><table cellspacing=1 cellpadding=0  border="1">
        <tr>
          <td width="20" height="20" bgcolor="#660000"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='660000';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#660033"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='660033';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#660066"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='660066';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#660099"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='660099';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#6600CC"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='6600CC';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#6600FF"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='6600FF';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
        </tr>
        <tr>
          <td width="20" height="20" bgcolor="#663300"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='663300';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#663333"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='663333';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#663366"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='663366';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#663399"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='663399';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#6633CC"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='6633CC';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#6633FF"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='6633FF';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
        </tr>
        <tr>
          <td width="20" height="20" bgcolor="#666600"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='666600';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#666633"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='666633';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#666666"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='666666';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#666699"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='666699';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#6666CC"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='6666CC';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#6666FF"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='6666FF';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
        </tr>
        <tr>
          <td width="20" height="20" bgcolor="#669900"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='669900';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#669933"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='669933';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#669966"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='669966';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#669999"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='669999';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#6699CC"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='6699CC';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#6699FF"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='6699FF';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
        </tr>
        <tr>
          <td width="20" height="20" bgcolor="#66CC00"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='66CC00';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#66CC33"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='66CC33';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#66CC66"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='66CC66';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#66CC99"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='66CC99';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#66CCCC"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='66CCCC';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#66CCFF"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='66CCFF';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
        </tr>
        <tr>
          <td width="20" height="20" bgcolor="#66FF00"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='66FF00';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#66FF33"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='66FF33';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#66FF66"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='66FF66';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#66FF99"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='66FF99';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#66FFCC"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='66FFCC';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#66FFFF"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='66FFFF';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
        </tr>
      </table>
        </td>
  </tr>
  <tr>
    <td><table cellspacing=1 cellpadding=0 border="1">
        <tr>
          <td width="20" height="20" bgcolor="#990000"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='990000';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#990033"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='990033';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#990066"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='990066';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#990099"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='990099';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#9900CC"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='9900CC';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#9900FF"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='9900FF';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
        </tr>
        <tr>
          <td width="20" height="20" bgcolor="#993300"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='993300';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#993333"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='993333';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#993366"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='993366';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#993399"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='993399';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#9933CC"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='9933CC';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#9933FF"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='9933FF';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
        </tr>
        <tr>
          <td width="20" height="20" bgcolor="#996600"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='996600';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#996633"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='996633';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#996666"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='996666';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#996699"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='996699';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#9966CC"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='9966CC';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#9966FF"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='9966FF';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
        </tr>
        <tr>
          <td width="20" height="20" bgcolor="#999900"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='999900';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#999933"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='999933';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#999966"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='999966';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#999999"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='999999';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#9999CC"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='9999CC';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#9999FF"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='9999FF';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
        </tr>
        <tr>
          <td width="20" height="20" bgcolor="#99CC00"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='99CC00';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#99CC33"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='99CC33';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#99CC66"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='99CC66';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#99CC99"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='99CC99';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#99CCCC"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='99CCCC';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#99CCFF"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='99CCFF';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
        </tr>
        <tr>
          <td width="20" height="20" bgcolor="#99FF00"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='99FF00';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#99FF33"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='99FF33';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#99FF66"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='99FF66';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#99FF99"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='99FF99';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#99FFCC"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='99FFCC';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#99FFFF"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='99FFFF';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
        </tr>
      </table>
        </td>
    <td><table cellspacing=1 cellpadding=0  border="1">
        <tr>
          <td width="20" height="20" bgcolor="#CC0000"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='CC0000';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#CC0033"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='CC0033';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#CC0066"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='CC0066';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#CC0099"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='CC0099';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#CC00CC"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='CC00CC';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#CC00FF"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='CC00FF';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
        </tr>
        <tr>
          <td width="20" height="20" bgcolor="#CC3300"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='CC3300';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#CC3333"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='CC3333';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#CC3366"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='CC3366';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#CC3399"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='CC3399';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#CC33CC"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='CC33CC';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#CC33FF"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='CC33FF';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
        </tr>
        <tr>
          <td width="20" height="20" bgcolor="#CC6600"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='CC6600';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#CC6633"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='CC6633';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#CC6666"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='CC6666';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#CC6699"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='CC6699';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#CC66CC"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='CC66CC';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#CC66FF"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='CC66FF';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
        </tr>
        <tr>
          <td width="20" height="20" bgcolor="#CC9900"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='CC9900';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#CC9933"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='CC9933';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#CC9966"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='CC9966';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#CC9999"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='CC9999';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#CC99CC"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='CC99CC';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#CC99FF"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='CC99FF';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
        </tr>
        <tr>
          <td width="20" height="20" bgcolor="#CCCC00"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='CCCC00';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#CCCC33"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='CCCC33';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#CCCC66"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='CCCC66';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#CCCC99"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='CCCC99';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#CCCCCC"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='CCCCCC';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#CCCCFF"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='CCCCFF';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
        </tr>
        <tr>
          <td width="20" height="20" bgcolor="#CCFF00"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='CCFF00';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#CCFF33"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='CCFF33';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#CCFF66"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='CCFF66';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#CCFF99"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='CCFF99';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#CCFFCC"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='CCFFCC';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#CCFFFF"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='CCFFFF';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
        </tr>
      </table>
        </td>
    <td><table cellspacing=1 cellpadding=0  border="1">
        <tr>
          <td width="20" height="20" bgcolor="#FF0000"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='FF0000';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#FF0033"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='FF0033';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#FF0066"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='FF0066';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#FF0099"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='FF0099';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#FF00CC"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='FF00CC';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#FF00FF"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='FF00FF';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
        </tr>
        <tr>
          <td width="20" height="20" bgcolor="#FF3300"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='FF3300';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#FF3333"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='FF3333';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#FF3366"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='FF3366';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#FF3399"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='FF3399';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#FF33CC"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='FF33CC';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#FF33FF"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='FF33FF';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
        </tr>
        <tr>
          <td width="20" height="20" bgcolor="#FF6600"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='FF6600';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#FF6633"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='FF6633';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#FF6666"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='FF6666';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#FF6699"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='FF6699';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#FF66CC"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='FF66CC';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#FF66FF"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='FF66FF';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
        </tr>
        <tr>
          <td width="20" height="20" bgcolor="#FF9900"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='FF9900';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#FF9933"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='FF9933';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#FF9966"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='FF9966';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#FF9999"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='FF9999';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#FF99CC"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='FF99CC';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#FF99FF"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='FF99FF';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
        </tr>
        <tr>
          <td width="20" height="20" bgcolor="#FFCC00"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='FFCC00';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#FFCC33"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='FFCC33';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#FFCC66"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='FFCC66';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#FFCC99"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='FFCC99';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#FFCCCC"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='FFCCCC';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#FFCCFF"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='FFCCFF';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
        </tr>
        <tr>
          <td width="20" height="20" bgcolor="#FFFF00"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='FFFF00';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#FFFF33"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='FFFF33';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#FFFF66"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='FFFF66';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#FFFF99"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='FFFF99';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#FFFFCC"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='FFFFCC';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
          <td width="20" height="20" bgcolor="#FFFFFF"><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='FFFFFF';return false"><img src="images/dummy.gif" alt="<?php echo $mess_colo_chx;?>" width="20" height="20" border="0"></a></td>
        </tr>
      </table>
                </td>
</table>
        <table cellspacing=1 cellpadding=0 border=1>
                <tr>
                        <td bgcolor="#FFFFFF" width=10 height=10 ><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='FFFFFF';return false"><img src="/images/dummy.gif" width="20" height="20" border="0"></a></td>
                        <td bgcolor="#DDDDDD" width=10 height=10 ><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='DDDDDD';return false"><img src="/images/dummy.gif" width="20" height="20" border="0"></a></td>
                        <td bgcolor="#C0C0C0" width=10 height=10 ><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='C0C0C0';return false"><img src="/images/dummy.gif" width="20" height="20" border="0"></a></td>
                        <td bgcolor="#969696" width=10 height=10 ><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='969696';return false"><img src="/images/dummy.gif" width="20" height="20" border="0"></a></td>
                        <td bgcolor="#808080" width=10 height=10 ><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='808080';return false"><img src="/images/dummy.gif" width="20" height="20" border="0"></a></td>
                        <td bgcolor="#646464" width=10 height=10 ><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='646464';return false"><img src="/images/dummy.gif" width="20" height="20" border="0"></a></td>
                        <td bgcolor="#4B4B4B" width=10 height=10 ><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='4B4B4B';return false"><img src="/images/dummy.gif" width="20" height="20" border="0"></a></td>
                        <td bgcolor="#242424" width=10 height=10 ><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='242424';return false"><img src="/images/dummy.gif" width="20" height="20" border="0"></a></td>
                        <td bgcolor="#000000" width=10 height=10 ><a href="#" title="<?php echo $mess_colo_chx;?>" onClick="<?php  echo "$nav$origine";?>.value='000000';return false"><img src="/images/dummy.gif" width="20" height="20" border="0"></a></td>
                </tr>
        </table>
<?php
echo "<P>$bouton_gauche<a href=\"javascript:self.close()\">Fermer</a>$bouton_droite</center></body></html>";