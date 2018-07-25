<?php
if (!isset($_SESSION)) session_start();
if (!isset($_SESSION['id_user']) || $_SESSION['id_user'] == "")
{
  exit();
}
include ("../include/UrlParam2PhpVar.inc.php");
require "../admin.inc.php";
require "../fonction.inc.php";
require "../lang$lg.inc.php";
//include ("click_droit.txt");
dbConnect();
include '../style.inc.php';
   ?>
   <SCRIPT language=javascript>
        function Quit() {
           top.close();
        }
   </SCRIPT>
   <?php
$titre = str_replace("|","'",$titre);
echo "<CENTER><TABLE bgColor='#298CA0' cellspacing='2' cellpadding='0' width='300'><TR><TD>";
echo "<TABLE bgColor='#FFFFFF' cellspacing='1' cellpadding='0' width='100%'>";
echo "<TR><TD background=\"../images/fond_titre_table.jpg\" colspan='2' height='36' align='center' valign='center'><Font size='3' color='#FFFFFF'><B>$titre</B></FONT></TD></TR>";
echo "<TR><TD colspan='2'><TABLE bgColor='#CEE6EC' cellspacing='0' cellpadding='6' width='100%'>";
echo "<TR><TD>&nbsp;<BR><FONT SIZE='2'><B>".stripslashes($contenu)."</B><BR>&nbsp;</TD></TR>";
echo "<TR><TD align='left'>$bouton_gauche<A HREF=\"javascript:void(0);\" ".
     "onClick =\"javascript:Quit();\">$mess_menu_quit</A>$bouton_droite</TD>";
echo "</TD></TR></TABLE></TD></TR></TABLE></TD></TR></TABLE>";
//***********************************************************************************************
echo "</BODY></HTML>";
?>