<?php
require '../../admin.inc.php';
echo '<HTML><HEAD></HEAD><BODY><strong>Abonnez-vous à ce service d\'actualités...</strong>
  <br />.. via un de ces portails d\'aggrégation - Choisissez parmi cette liste le vôtre si vous y êtes déjà inscrit(e)-
<TABLE cellpadding=2 cellspacing = 6><TR><TD>
<A HREF = "http://www.netvibes.com/subscribe.php?url='.$adresse_http.'/utilitaires/Rss/flux.php" target="_blank">
<IMG SRC="images/addNetvibes.gif" border=0></A></TD>

<TD><A HREF = "http://add.my.yahoo.com/rss?url='.$adresse_http.'/utilitaires/Rss/flux.php" target="_blank">
<IMG SRC="images/addYahoo.gif" border=0></A></TD>

<TD><A HREF = "http://fusion.google.com/add?feedurl='.$adresse_http.'/utilitaires/Rss/flux.php" target="_blank">
<IMG SRC="images/addGoogle.gif" border=0></A></TD>

<TD><A HREF = "http://my.msn.com/addtomymsn.armx?id=rss&ut='.$adresse_http.'/utilitaires/Rss/flux.php" target="_blank">
<IMG SRC="images/addMSN.gif" border=0></A></TD>

<TD><A HREF = "http://www.newsgator.com/ngs/subscriber/subext.aspx?url='.$adresse_http.'/utilitaires/Rss/flux.php" target="_blank">
<IMG SRC="images/addGator.gif" border=0></A></TD>

<TD><A HREF = "http://www.bloglines.com/sub/'.$adresse_http.'/utilitaires/Rss/flux.php" target="_blank">
<IMG SRC="images/addBloglines.gif" border=0></A></TD>

</TR></TABLE>
... ou en copiant l\'adresse suivante directement dans votre application de lecture de flux RSS<br />
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font color="#336699"><strong>'.$adresse_http.'/utilitaires/Rss/flux.php?p=6</strong></font><font size=-1> [mettez derrière "p=" à la place de "6" le nombre d\'items à afficher]</font><br />
... Si vous n\'en avez pas, Cliquez sur le lien suivant: <A href="'.$adresse_http.'/utilitaires/Rss/acco_lire_flux.php?p=10&url='.$adresse_http.'/utilitaires/Rss/flux.php" target="_blank"><font color="#24677A"><strong>Lire ce fil RSS</strong></FONT></A>
  et ajoutez-le à vos favoris<br />
</BODY></HTML>';

?>