<?php
if (!isset($_SESSION)) session_start();
include ("include/UrlParam2PhpVar.inc.php");
if (!isset($_SESSION['id_user']) || $_SESSION['id_user'] == "")
{
  exit();
}
require "lang$lg.inc.php";
include ('style.inc.php');
echo "<CENTER><TABLE bgColor='#298CA0' cellspacing='2'><TR><TD>";
echo "<TABLE bgColor='#FFFFFF' cellspacing='2'>";
echo "<TR><TD background=\"images/fond_titre_table.jpg\" colspan='1' height='40' align='center' valign='center'><Font size='3' color='#FFFFFF'><B>$msq_acts</B></FONT></TD></TR>";
$nom=stripslashes($nom);
echo "<TR><TD><B>$nom </B></TD></TR>";
echo "<TR><TD><HR SIZE='3' width='200' align=center></TD></TR>";
echo "<TR><TD><Small>$mess1<B> $mess2</B></small></TD></TR>";
echo "<TR><TD><Small>$mess3<B> $mess4</B></small></TD></TR>";
echo "<TR><TD><Small>$mess5<B> $mess6</B></small></TD></TR>";
echo "<TR><TD><Small>$mess7<B> $mess8</B></small></TD></TR>";
echo "</TABLE></TD></TR></TABLE></body></html>";
?>