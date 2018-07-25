<?php
  if (!isset($_SESSION)) session_start();
 require ("../admin.inc.php");
    $sqlhost=$adresse;
    $sqllogin=$log;
    $sqlpasswd=$mdp;
    $sqlDB=$bdd;

  function Connectix()
  {
    global $sqlDB,$sqllink,$sqlhost,$sqllogin,$sqlpasswd;
    $sqllink = mysql_pconnect($sqlhost,$sqllogin,$sqlpasswd);
    @mysql_select_db("$sqlDB",$sqllink) or die("CONNEXION à MYSQL IMPOSSIBLE.<BR>Vérifiez les paramètres de connexion dans mysql.php.<BR>Lisez le fichier <A href='install.txt' target='_blank'>install.txt</A>");
  }
?>