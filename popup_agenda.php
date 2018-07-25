<?php
if (!isset($_SESSION)) session_start();
include ("include/UrlParam2PhpVar.inc.php");
if (!isset($_SESSION['id_user']) || $_SESSION['id_user'] == "")
{
  exit();
}
require "lang$lg.inc.php";
require "style.inc.php";
echo "<title>$mess_ag_carnet_rdv</title></head>";
echo "<body bgcolor=\"#002D44\" marginwidth='0' marginheight='0' leftmargin='0' topmargin='0'>";
echo "<CENTER><TABLE bgColor='#298CA0' cellspacing='2'><TR><TD>";
echo "<TABLE bgColor='#FFFFFF' cellspacing='2'>";
echo "<TR><TD background=\"images/fond_titre_table.jpg\" colspan='1' height='40' align='center' valign='center'><Font size='3' color='#FFFFFF'><B>$mess_ag_carnet_rdv</B></FONT></TD></TR>";
echo "<TR><TD><P &nbsp;><B>$titre</B></TD></TR>";
echo "<TR><TD>$com<P &nbsp;></TD></TR>";
echo "</TABLE></TD></TR></TABLE></body></html>";

?>

</body>

</html>