<?php
if (!isset($_SESSION)) session_start();
include ("include/UrlParam2PhpVar.inc.php");
if (!isset($_SESSION['id_user']) || $_SESSION['id_user'] == "")
{
  exit();
}
require "lang$lg.inc.php";
include ("click_droit.txt");
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML><HEAD><TITLE>Microsoft VM</TITLE>
<META http-equiv=Content-Type content=text/html;CHARSET=iso-8859-1>
<META content="MSHTML 6.00.2800.1400" name=GENERATOR></HEAD>
<BODY>
<H1 align=center><FONT face="Arial, Helvetica" color=#0033cc size=5>Téléchargement de Microsoft VM</FONT></H1>
<P><FONT face="Arial, Helvetica" color=red size=4>Vous êtes sur la page de téléchargement de Microsoft Virtual Machine
</FONT></P>
de vous retrouver sur cette page indique que <B>JAVA n'est pas installé sur la machine</B> que vous utilisez et qu'il est nécessaire pour une utilisation optimale de cette plateforme de formation.
<BR>Le téléchargement va se lancer automatiquement dans quelques secondes. Si vous préférez télécharger plutôt Java de Sun microsystems cliquez sur "Java de Sun" sinon laissez le téléchargement se poursuivre ou encore fermez la fenêtre en cliquant sur l'icône de sortie.<P><BR></P>
<P><CENTER><FORM><input type="BUTTON" name="SUBMIT" value="Java de Sun Microsystems" onclick="javascript:document.location.replace('http://www.java.com/fr/');">&nbsp;&nbsp;&nbsp;&nbsp;<input type=image src="images/fermer.gif" onclick="javascript:window.close();"></FORM></CENTER>
<P><FONT face="Arial, Helvetica" color=red size=4>Vous autorisez l'installation de Microsoft Virtual Machine</FONT></P>
<UL>
  <LI><B>Les fichiers d'installation vont être chargés</B>.
  <LI>Tant que vous aurez le sablier, le téléchargement n'est pas terminé
  <LI><B>Rappel:</B> Ce téléchargement peut exiger jusqu'à 30 minutes avec modem de 28.8 bits/s.
  <LI>Quand le téléchargement sera terminé, une invite de sécurité vous demandera si vous désirez installer Microsoft VM.
  <LI>Cliquez 'Oui' dans cette fenêtre d'alerte
  <LI>Quand l'installation est terminée, éteignez puis réallumer votre PC pour la prise en compte de Microsoft VM
  <LI>Vous pouvez toutefois continuer à utiliser le menu javascript actuel jusqu'à votre prochaine connection. </LI></UL>
<P>
<OBJECT codeBase=http://ef-dev.educagri.fr/java/JavaVM3186.exe#Version=5,0,3186 height=0 width=0
  classid=clsid:08B0E5C0-4FCB-11CF-AAA5-00401C608500>
</OBJECT></P>
</BODY>
</HTML>