<?php
ini_set('default_charset','ISO-8859-1');
ini_set('display_errors','on');
ini_set('error_reporting', E_ALL);
require ('../langfr.inc.php');
?>
<html>
<head>
<META http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<META http-equiv="content-language" content="fr">
<META HTTP-EQUIV="Expires" CONTENT="Fri, Jan 01 1900 00:00:00 GMT">
<META HTTP-EQUIV="Pragma" CONTENT="no-cache">
<META HTTP-EQUIV="Cache-Control" CONTENT="no-cache">
<link rel="stylesheet" type="text/css" href="style_install.css" />
<SCRIPT LANGUAGE="JavaScript" SRC="ajax.js"></SCRIPT>
<title>Installation de Formagri </title>
  <script type="text/javascript">
      var envoi_base=0;
  </script>
</HEAD>
<BODY>
<?php
$engage = 0;
if (empty($_POST['envoi']) && $engage != 1 && empty($_POST['licence']))
{
   ?>
   <center><div id='alerte' class="le_formulaire" style="cursor:default;">
     Si vous êtes d'accord avec ce contrat de licence, déroulez le texte avec l'ascenseur et cochez la case pour signifier votre accord.
   </div>
   <h1>CONTRAT de LICENCE</h1><p></p><p></p><table width="90%"><tr><td align="center">
   <div  class="le_formulaire" style="font-weight:normal;text-align:left; width:70%; height:500px; overflow:auto;cursor:default;">
   <?php include ("licence.txt");?>
   <form id="licence" name="licence" action="install.php" method="post">
   <input type="checkbox" name="licence" value="1" title= "Cochez pour donner votre accord"
   onclick="javascript:document.licence.submit()"> &nbsp;<strong>J'accepte les conditions énumérées par cette licence..</strong>
   </form></p><p>&nbsp;</p>
   </div></td></tr><tr><td align="center">
      <div style="width:70%; font-weight:normal; font-size:9px;font-family:arial,tahoma,verdana; text-align:left; cursor:default;">
         Formagri-2.7 2013 © Cnerta/Eduter/AgroSupDijon
      </div>
   </td></tr></table></center>
   <?php
  exit();
}
if (isset($message) && $message == 1)
   echo "<CENTER><div class='warning'>Vous n'avez pas encore configuré Formagri.<BR><BR>".
              "Vous avez été redirigé vers cette page pour configurer Formagri et installer la Base de données</div><BR></CENTER>";
echo "<CENTER><TABLE bgColor='#FFFFFF' cellspacing='0' cellpadding='0' border=0><TR><TD align=left>";
echo "<TABLE border='0' width='95%' cellspacing='0' cellpadding='2'>";
echo "<TR height='39'><TD background=\"../images/fond_titre_table.jpg\" width='100%' valign='center' align='center' colspan='2' style = \"font-family:'arial'\">".
         "<FONT COLOR= 'white' SIZE='3'><B>Installation de Formagri</B></FONT></TD></TR>";
?>
<TR><TD>
  <div id="id" class="le_formulaire"
     onclick="javascript:var id=document.getElementById('monform').className ='hidden';document.monform.mon_hote.focus();"
     onDBLclick ="javascript:var id=document.getElementById('monform').className ='visible';">
     <acronym title="Click pour ouvrir et Double-click pour cacher le formulaire">
          Etape 1 ==>  Ouvrir ou refermer le formulaire de configuration de Formagri
     <acronym />
  </div>
<?php
if (!empty($_POST['envoi']))
{
  echo " </TD></TR><TR><TD>".
       "<div class=\"montrer\" id=\"message\">Veuillez patienter...</div>".
       "<div class=\"contenu\" id=\"contenu\"><IMG SRC=\"../images/progress_bar.gif\" border=\"0\"></div>";
  $params = '';
  foreach($_POST as $index => $valeur)
  {
    if (isset($index) && $index != "envoi" && $index != "submit")
       $params .= $index."=".$valeur."&";
  }
//  echo $params;
//echo "<pre>";print_r($_POST);echo "</pre>";
  ?>
  <script type="text/javascript">
      sendData('', 'admin_save.php?<?php echo $params;?>', 'POST');
  </script>
<?php
}
if (!empty($_POST['envoi']))
{
  $_POST['repertoire'] = str_replace('\\','\\\\',$_POST['repertoire']);
  ?>
   <TR><TD>
     <div id="id"  class="le_formulaire"
        onclick="javascript:var envoi_base=1;sendData('','formagri.php?basedd=<?php echo $_POST['nom_base'];?>&url=<?php echo $_POST['mon_hote'];?>','POST');">
        Etape 2 ==>  Créer la base de données<?php $_POST['nom_base'];?> et ses tables
     </div>
   </TD></TR>
<?php
}
$engage = 1;
$mon_localhost = "http://".$_SERVER['HTTP_HOST'].substr($_SERVER['REQUEST_URI'],0,-20);
$mon_repertoire = substr($_SERVER['SCRIPT_FILENAME'],0,-20);

?>
</TD></TR>
<TR><TD align=center>
<div  class="formid" id="formid">
  <?php include ('formulaire1.php'); ?>
</div>
</TD></TR>
</TABLE></TD></TR></TABLE></CENTER>
</BODY>
</HTML>
