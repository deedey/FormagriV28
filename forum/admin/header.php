<?php
if (!isset($_SESSION)) session_start();
require ("../admin.inc.php");
require "../fonction.inc.php";
require "../fonction_html.inc.php";
dbconnect();
include "../style.inc.php";
if ($arrive == ""){
   unset($_SESSION['chaine_act']);
   unset($_SESSION['forum_act']);
}
?>
<table width="100%" cellspacing="0" cellpadding="0" border="0">
<tr>
    <td width="50%"><font face="Arial,Helvetica">
<?php
/*  if(!@empty($phorum_logged_in)){
    echo " | <a href=\"$myname?logout=1\">logout</a><!-- pli: $phorum_logged_in -->";
  }
*/?></td>
    <td width="50%" align="right"><font face="Arial,Helvetica"><?php
if(@isset($DB->connect_id)){
  if($DB->connect_id){
    echo " ";
  }
  else{
    echo "<b>La connection a échoué</b>";
  }
}
else{
  echo "<b>Pas de connection valide</b>";
}
?></font></td>
</tr>
</table>
<center>
<?php
/*
<font size=+2 face="Impact,Arial,Helvetica" >Gestion des forums  /  </font><font size=+2 face="Impact,Arial,Helvetica" color="#800000">administration</font><p>
<P ALIGN=CENTER><A HREF="<?php echo $myname; ?><?php if($loginnum!=0) echo "?num=$loginnum&page=managemenu";?>"><font face="Arial,Helvetica"><b>principal</b></font></a> |
 <P ALIGN=CENTER><A HREF="<?php echo "$forum_url/$forum_page.$ext"; ?>"><font size = '3' face="Arial,Helvetica"><b>Consulter tous les forums</b></font></a></p>
*/
?>
