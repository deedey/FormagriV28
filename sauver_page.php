<?php
if (!isset($_SESSION)) session_start();
include ("include/UrlParam2PhpVar.inc.php");
if (!isset($_SESSION['id_user']) || $_SESSION['id_user'] == "")
{
  exit();
}
require "admin.inc.php";
require 'fonction.inc.php';
require 'fonction_html.inc.php';
require "lang$lg.inc.php";
include("include/varGlobals.inc.php") ;
// "w" -> Ouvre en écriture seule. Si le fichier n'existe pas, on tente de le créer.
$nom_file = modif_nom($nom_file);
if ($objet == 'new')
  $nom_fichier = $rallonge."/".$nom_file.".html";
else
  $nom_fichier = $nom_file;
echo '<pre>';print_r($_POST);echo '</pre>';
$NewFile = fopen($nom_fichier, "w");
// Ecrit les données essentielles dans cette page et place le contenu de la saisie de l'utilisateur
$DebutHTML="<html>\n<head>\n<title>$titre</title>\n</head>\n\n<body>\n\n";
fwrite($NewFile, $DebutHTML);
$postArray = &$_POST ;
foreach ( $postArray as $sForm => $value )
{
  $postedValue = stripslashes( $value ) ;
  if ($value != $rallonge && $value != $nom_file && $value != $titre && $value != $objet && $value != $direct && $value != $parent && $value != $id_grp && $value != $communes_groupe && $value != $formateurs && $value != $dos)
  {
     $postedValue = str_replace("<title></title>","",$postedValue);
     fwrite($NewFile, $postedValue);
  }
}
$FinHTML="\n\n</body></html>";
fwrite($NewFile, $FinHTML);
// Ferme le fichier créé
fclose($NewFile);
$message = $mess_casier_fichier." ".$mess_casier_sauve;
$rep_insert = $rallonge;
$lien_retour = "modif_rep_fic.php?id_grp=$id_grp&formateurs=$formateurs&communes_groupe=$communes_groupe&rallonge=$rep_insert&parent=$parent&dos=$dos&dossier=$rep_insert&direct=1&objet=vient_edit&message=$message";
$agent=getenv("HTTP_USER_AGENT");
if (strstr($agent,"MSIE")){
  echo "<SCRIPT Language=\"Javascript\">";
  echo "window.parent.opener.location.reload('$lien_retour');";
  echo "</SCRIPT>";
}else{
  echo "<SCRIPT Language=\"Javascript\">";
       echo "parent.parent.top.opener.location.reload('$lien_retour');";
  echo "</SCRIPT>";
}
unset($_SESSION['rallongement']);
echo "<SCRIPT Language=\"Javascript\">";
  echo "self.close();";
echo "</SCRIPT>";

?>