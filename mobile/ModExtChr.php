<?php
session_start();
require ("../admin.inc.php");
require ("../fonction.inc.php");
require ("../fonction_html.inc.php");
dbConnect();
$requete = mysql_query("SET character_set_results = 'utf8'");
$requete_mod = mysql_query("SELECT * from utilisateur where
               util_login_lb = '".$_POST['user']."' and util_motpasse_lb = '".$_POST['pass']."'");
if (mysql_num_rows($requete_mod) == 0)
   $content = "NULL";
else
{
  $_SESSION['IDUSER'] = mysql_result($requete_mod,0,'util_cdn');
  $_SESSION['login'] = mysql_result($requete_mod,0,'util_login_lb');
  $_SESSION['nom'] = mysql_result($requete_mod,0,'util_nom_lb');
  $_SESSION['prenom'] = mysql_result($requete_mod,0,'util_prenom_lb');
  $_SESSION['typUtil'] = mysql_result($requete_mod,0,'util_typutil_lb');
  $_SESSION['LMS'] = $adresse_http;
  $_SESSION['lg'] = 'fr';
  setcookie("typUtil", $_SESSION['typUtil'], time() + 600);
  $content = $_SESSION['prenom']." ".$_SESSION['nom'];
  $date = date("d/m/Y H:i:s" ,time());
  $serveur = gethostbyaddr($_SERVER['REMOTE_ADDR']);
  $agent=getenv("HTTP_USER_AGENT");
  $ip = getenv('REMOTE_ADDR');
  file_put_contents('mobilog.log',$_SESSION['IDUSER']." ,    CONNEXION,   $date ,   $serveur ,   $ip ,   ".$_SESSION['LMS'].
                    " ,   ".$_SESSION['nom']." ".$_SESSION['prenom'].",".
                    " \nAgent : $agent \n",FILE_APPEND);
}
echo utf2Charset($content,'iso-8859-1');
?>
