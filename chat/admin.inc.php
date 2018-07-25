<?php
$sys_exp = $_SERVER['PATH'];
if (strstr($sys_exp,"/"))
    $s_exp = "lx";
else 
    $s_exp = "Ms";
$signe ="/";
 $base_root = $_SERVER['DOCUMENT_ROOT'];
$rep_ttf = $base_root.$signe."graphique".$signe."ttf".$signe;
$host = "http://localhost/formagri";
$monURI = "/".str_replace('http://localhost/','',"http://localhost/formagri");
 $repertoire= ($monURI != '') ? $base_root.$monURI : $base_root;
$rep_graph =$repertoire.$signe."graphique".$signe;
$nom_url = "http://localhost/formagri";
$url_ress = $nom_url;
$adresse_http = $nom_url;
$adresse = "localhost";
$bdd = "formagri";
$log = "root";
$mdp ="";
$forum_url = $adresse_http.'/forum';
$admin_url = $adresse_http.'/forum/admin';
$DefaultEmail = 'moi@chez_untel.fr';
$Password = 'safia';
$ForumModEmail = 'moi@chez_untel.fr';
$ForumModPass = 'safia';
ini_set('error_reporting', 0);
 ini_set('date.timezone','Europe/Paris');
 ini_set('default_charset','iso-8859-1');
if (!isset($_SESSION['IsOff']))
      $_SESSION['IsOff'] = 1;

$ChainePost = '';
foreach ($_POST as $key => $value)
{
 $ChainePost .= (!is_array($value)) ? ' '.$value : ' ';
}
if (isset($_POST) && (strstr(strtolower($ChainePost),'&lt;/script') || strstr(strtolower($ChainePost),'&#139;/script') ||
    strstr(strtolower($ChainePost),'&lt;script') || strstr(strtolower($ChainePost),'&#139;script') ||
    strstr(strtolower($ChainePost),'<script') || strstr(strtolower($ChainePost),'</script') ||
    strstr(strtolower($ChainePost),'< script') || strstr(strtolower($ChainePost),'</ script')))
{
   echo "<script language='JavaScript'>";
     echo "alert('Attention !! Certaines balises incluses dans le formulaire sont interdites car dangereuses.');
          history.go(-2);";
   echo "</script>";
   exit;
}
if (strstr(strtolower(urlencode(json_encode($_GET))),'&lt;/script') || strstr(strtolower(urlencode(json_encode($_GET))),'&#139;/script') ||
    strstr(strtolower(urlencode(json_encode($_GET))),'&lt;script') || strstr(strtolower(urlencode(json_encode($_GET))),'&#139;script') ||
    strstr(strtolower(urlencode(json_encode($_GET))),'%3cscript') || strstr(strtolower(urlencode(json_encode($_GET))),'%2fscript') ||
    strstr(strtolower(json_encode($_GET)),'insert ') || strstr(strtolower(json_encode($_GET)),'select '))
{
   echo "<script language='JavaScript'>";
     echo "alert('Attention !! Certaines balises incluses dans l\'URL sont interdites car dangereuses.');
          history.go(-1);";
   echo "</script>";
   exit;
}


 ?>