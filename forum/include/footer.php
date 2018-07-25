</TD></TR>
<?php
    $arriere = (isset($retour) && $retour == 2) ? 2 : 1;
    echo "<TR height = '40' ><TD align=left style=\"padding-left:10px;\">$bouton_gauche<A HREF=\"javascript:history.go(-$arriere);\">Retour</A>$bouton_droite</TD></TR>";
?></TABLE></TD></TR></TABLE>
</body>
</html>
