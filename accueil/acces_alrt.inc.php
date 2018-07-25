<?php
$req_msg = mysql_query("SELECT * from message where ((msg_dhdeb_dt < '$madate_compare' OR msg_dhdeb_dt = '$madate_compare') AND (msg_dhfin_dt = '$madate_compare' OR msg_dhfin_dt > '$madate_compare')) AND msg_tous_on=1");
$nb_msg = mysql_num_rows($req_msg);
$g = 0;
if ($nb_msg > 0){
   echo "<TR><TD align='right' valign='top'><IMG SRC=\"images/ecran-annonce/icoNalert.gif\" border = '0' title ='$mess_avertis'></TD><TD>";
   while ($g < $nb_msg){
          $id_msg = mysql_result($req_msg,$g,"msg_cdn");
          $contenus = mysql_result($req_msg,$g,"msg_contenu_cmt");
          $contenu = html_entity_decode($contenus,ENT_QUOTES,'ISO-8859-1');
          $auteur = mysql_result($req_msg,$g,"msg_auteur_no");
          if ($g > 0)
             echo "<br />";
          echo "<font size='2' color='#D45211'>$contenu</font>";
          $g++;
   }
   echo "</td></tr>";
}
?>
