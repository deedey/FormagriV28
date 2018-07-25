<?php
if (!isset($_SESSION)) session_start();
include ("include/UrlParam2PhpVar.inc.php");
if (!isset($_SESSION['id_user']) || $_SESSION['id_user'] == "")
{
  exit();
}
require "admin.inc.php";
require 'fonction.inc.php';
require "lang$lg.inc.php";
//include ("click_droit.txt");
dbConnect();
include 'style.inc.php';
$titre_activite = GetDataField ($connect,"select act_nom_lb from activite where act_cdn = $id_act","act_nom_lb");
echo "<CENTER><TABLE bgColor='#298CA0' cellspacing='2' width='98%'><TR><TD>";
echo "<TABLE bgColor='#FFFFFF' cellspacing='1' width='100%'>";
echo "<TR><TD background=\"images/fond_titre_table.jpg\" colspan='1' height='38' align='center' valign='center'>".
     "<Font size='4' color='#FFFFFF'><B>$msq_activite : $titre_activite</B></FONT></TD></TR>";
echo "<TABLE bgColor='#FFFFFF' cellpadding='5' align='MIDDLE' valign='MIDDLE' width='100%'><TR><TD>";
$fouiner = mysql_query("SELECT * FROM ressource_new where ress_cdn = $id_ress");
$nombre = mysql_num_rows($fouiner);
if ($nombre == 0){
  echo "&nbsp;<BR><FONT size='2'>$mess_gp_noress_ass</FONT><BR>&nbsp;";
 exit();
}
$titre = mysql_result($fouiner,0,"ress_titre");
$descrip = mysql_result($fouiner,0,"ress_desc_cmt");
$aut = mysql_result($fouiner,0,"ress_auteurs_cmt");
$object = mysql_result($fouiner,0,"ress_type");
$sup = mysql_result($fouiner,0,"ress_support");
echo "&nbsp;<BR><FONT size='2'><B>$mrc_tit_ress</B> : <FONT size='2'>$titre</FONT><BR>&nbsp;";
echo "&nbsp;<BR><FONT size='2'><B>$mrc_auteur</B> : <FONT size='2'>$aut</FONT><BR>&nbsp;";
echo "&nbsp;<BR><FONT size='2'><B>$mess_desc/$mrc_mod_emp</B> : <FONT size='2'>$descrip</FONT><BR>&nbsp;";
echo "&nbsp;<BR><FONT size='2'><B>$mrc_opr</B> : <FONT size='2'>$object</FONT><BR>&nbsp;";
echo "&nbsp;<BR><FONT size='2'><B>$mrc_sup_ress</B> : <FONT size='2'>$sup</FONT><BR>&nbsp;";
echo "</TD></TR></TABLE>";
echo "</TD></TR></TABLE></TD></TR></TABLE>";
echo "</BODY></HTML>";
?>