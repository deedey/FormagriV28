<?php
//error_reporting(E_ALL);
//echo $_SERVER['QUERY_STRING'];
if (isset($_GET['obj']) && $_GET['obj'] == 'nb')
{
      $hauteur = 14;
      $texte = $_GET['pourcent']."%";
      $largeur = ($_GET['largeur'] < 30) ? ($_GET['largeur']+ceil(500/$_GET['largeur'])) :$_GET['largeur'];
      $longueurPlein =ceil($_GET['pourcent']*$largeur)/100;
      $img = imagecreate($largeur,$hauteur);
      $couleurFond = imagecolorallocate($img,0,45,68);
      $couleurTexte = imagecolorallocate($img,255,255,255);
      $couleurPourtour = imagecolorallocate($img,232,126,12);
      $couleurOmbre = imagecolorallocate($img,120,116,116);
      $couleurPlein = imagecolorallocate($img,232,126,12);
}
elseif (isset($_GET['obj']) && $_GET['obj'] == 'duree')
{
      $hauteur = 14;
      $texte = $_GET['pourcent']."%";
      $largeur = ($_GET['largeur'] < 30) ? ($_GET['largeur']+ceil(500/$_GET['largeur'])) : $_GET['largeur'];
      $longueurPlein =ceil($_GET['pourcent']*$largeur)/100;
      $img = imagecreate($largeur,$hauteur);
      $couleurFond = imagecolorallocate($img,0,128,128);
      $couleurTexte = imagecolorallocate($img,255,255,255);
      $couleurPourtour = imagecolorallocate($img,232,126,12);
      $couleurOmbre = imagecolorallocate($img,120,116,116);
      $couleurPlein = imagecolorallocate($img,200,202,14);
}
imagefilledrectangle($img,0,0,$largeur,$hauteur,$couleurFond);
imagefilledrectangle($img,0,0,$longueurPlein,$hauteur,$couleurPlein);
imagestring($img,3,$largeur/2,1,$texte,$couleurTexte);
header("Content-Type: image/png");
imagepng($img);
?>