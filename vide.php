<?php
if (!isset($_SESSION)) session_start();
include ("include/UrlParam2PhpVar.inc.php");
if (!isset($_SESSION['id_user']) || $_SESSION['id_user'] == "")
{
  exit();
}
if ($titre == '')
   echo "<HTML><BODY></BODY></HTML>";
else{
  if (!isset($lg) || $lg == ""){
    include ('deconnexion-fr.txt');
    exit();
  }
  require 'fonction.inc.php';
  require "lang$lg.inc.php";
  //include ("click_droit.txt");
  dbConnect();
  if (isset($qcm) && $qcm == 1){
     echo stripslashes($content);
     exit;
  }
  include 'style.inc.php';
  $titre = str_replace("|","'",$titre);
  echo "<CENTER><TABLE bgColor='#298CA0' cellspacing='2' cellpadding='0' width='300'><TR><TD>";
  echo "<TABLE bgColor='#FFFFFF' cellspacing='1' cellpadding='0' width='100%'>";
  echo "<TR><TD background=\"images/fond_titre_table.jpg\" colspan='2' height='36' align='center' valign='center'><Font size='3' color='#FFFFFF'><B>$titre</B></FONT></TD></TR>";
  echo "<TR><TD colspan='2'><TABLE bgColor='#CEE6EC' cellspacing='0' cellpadding='6' width='100%'>";
  echo "<TR><TD>&nbsp;<BR><FONT SIZE='2'>".stripslashes($contenu)."<BR>&nbsp;</TD></TR>";
  echo "<TR><TD align='left'><A HREF=\"javascript:history.back();\" onmouseover=\"img_annonce.src='images/fiche_identite/boutretourb.gif';return true;\" onmouseout=\"img_annonce.src='images/fiche_identite/boutretour.gif'\">";
  echo "<IMG NAME=\"img_annonce\" SRC=\"images/fiche_identite/boutretour.gif\" BORDER='0' onLoad=\"tempImg=new Image(0,0); tempImg.src='images/fiche_identite/boutretourb.gif'\"></A>";
  echo "</TD></TR></TABLE></TD></TR></TABLE></TD></TR></TABLE>";
//***********************************************************************************************
echo "</BODY></HTML>";
}
?>