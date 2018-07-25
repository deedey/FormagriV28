<?php
  if (!isset($_SESSION)) session_start();
if (!isset($_SESSION['id_user']) || $_SESSION['id_user'] == "")
{
  exit();
}
  require 'fonction.inc.php';
  require "lang$lg.inc.php";
  dbConnect();
//de quel type est l'utilisateur (apprenant, formateur, administrateur)
$nom_user = GetDataField ($connect,"select util_nom_lb from utilisateur where util_cdn = $id_user","util_nom_lb");
$prenom_user = GetDataField ($connect,"select util_prenom_lb from utilisateur where util_cdn = $id_user","util_prenom_lb");
?>
<html>
<head>
        <title><?php  echo $mess_bas_titre;?></title>
</head>
<body background="images/fond_titre_table.jpg" marginwidth="0" marginheight="0" leftmargin="0" topmargin="0">
<CENTER><TABLE cellpadding='1' cellspacing='0'><TR height="33">
<TD valign="top"><IMG src="images/precedent.gif" ALT="<?php  echo $mess_bas_avant ;?>" onClick='parent.main.location="javascript:history.back()"'></TD>
<TD valign="top">&nbsp;&nbsp;&nbsp;&nbsp;<IMG src="images/actua-nb.gif" ALT="<?php  echo $mess_bas_reload ;?>" onClick='parent.location="javascript:main.location.reload()"'></TD>
<TD valign="top">&nbsp;&nbsp;&nbsp;&nbsp;<IMG src="images/suivant.gif"  ALT="<?php  echo $mess_bas_suiv ;?>" onClick='parent.main.location="javascript:history.forward()"'></TD>
<?php
echo "<TD valign='top'>&nbsp;&nbsp;&nbsp;&nbsp;<A href=\"#\" title=\"$mess_bas_print\"  onClick=\"javascript:parent.frames[1].focus();parent.frames[1].print()\"\"".
          " onmouseover=\"img_imp.src='images/gest_parc/icovimprimb.jpg';return true;\"".
          " onmouseout=\"img_imp.src='images/gest_parc/icovimprim.jpg'\">".
          "<IMG NAME=\"img_imp\" SRC=\"images/gest_parc/icovimprim.jpg\" border='0' valign='top' alt=\"$mess_bas_print\"".
          " onLoad=\"tempImg=new Image(0,0); tempImg.src='images/gest_parc/icovimprimb.jpg'\"></A></TD>";
?>
</BODY></HTML>
