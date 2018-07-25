<?php
if (!isset($_SESSION)) session_start();
if (!isset($_SESSION['id_user']) || $_SESSION['id_user'] == "")
{
  exit();
}
include ("include/UrlParam2PhpVar.inc.php");
include ("include/varGlobals.inc.php");
require "admin.inc.php";
require 'fonction.inc.php';
require "lang$lg.inc.php";
dbConnect();
$Ext="_".$numero_groupe;
if ($_SESSION['typ_user'] == "APPRENANT")
  $num_app = $_SESSION['id_user'];
elseif(isset($utilisateur))
  $num_app = $utilisateur;

if ((!isset($detail) || (isset($detail) && $detail != '1')) && !isset($graphe))
{
   if ($scormOk == 1)
      $act_termine = mysql_query ("select mod_cdn from scorm_module,scorm_util_module$Ext where
                                   scorm_module.mod_cdn = scorm_util_module$Ext.mod_module_no and
                                   scorm_module.mod_seq_no=$seq and
                                   scorm_util_module$Ext.user_module_no=$num_app and
                                   scorm_util_module$Ext.mod_grp_no=$numero_groupe AND
                                   scorm_module.mod_content_type_lb != 'LABEL' and
                                   (scorm_util_module$Ext.lesson_status='COMPLETED' OR
                                   scorm_util_module$Ext.lesson_status='PASSED' OR
                                   scorm_util_module$Ext.lesson_status='FAILED' OR
                                   scorm_util_module$Ext.lesson_status='BROWSED')");
   else
      $act_termine = mysql_query ("select act_cdn from activite,suivi1$Ext where
                                   activite.act_cdn = suivi1$Ext.suivi_act_no and
                                   activite.act_seq_no=$seq and
                                   suivi1$Ext.suivi_utilisateur_no =$num_app AND
                                   suivi1$Ext.suivi_grp_no =$numero_groupe AND
                                   suivi1$Ext.suivi_etat_lb='TERMINE'");
   $nb_acterm = mysql_num_rows($act_termine);
   $largeur = 44;
   $hauteur = 7;
}
elseif(isset($detail) && $detail == '1' && !isset($graphe))
{
   $largeur = 120;
   $hauteur = 12;
}
elseif (isset($graphe) && $graphe == 1)
{
   if (isset($obj) && $obj == 'nb')
   {
      $hauteur = 14;
      $texte = $pourcent."%";
      $longueurPlein = ceil($pourcent*$largeur)/100;
      $img = ImageCreate($largeur,$hauteur);
      $couleurFond = ImageColorAllocate($img,0,45,68);
      $couleurTexte = ImageColorAllocate($img,255,255,255);
      $couleurPourtour = ImageColorAllocate($img,232,126,12);
      $couleurOmbre = ImageColorAllocate($img,120,116,116);
      $couleurPlein = ImageColorAllocate($img,232,126,12);
   }
   elseif (isset($obj) && $obj == 'duree')
   {
      $hauteur = 14;
      $texte = $pourcent."%";
      $longueurPlein =ceil($pourcent*$largeur)/100;
      $img = ImageCreate($largeur,$hauteur);
      $couleurFond = ImageColorAllocate($img,0,128,128);
      $couleurTexte = ImageColorAllocate($img,255,255,255);
      $couleurPourtour = ImageColorAllocate($img,232,126,12);
      $couleurOmbre = ImageColorAllocate($img,120,116,116);
      $couleurPlein = ImageColorAllocate($img,200,202,14);
   }
   ImageFilledRectangle($img,0,0,$largeur,$hauteur,$couleurFond);
   ImageFilledRectangle($img,0,0,$longueurPlein,$hauteur,$couleurPlein);
   ImageString($img,3,$largeur/2,1,$texte,$couleurTexte);
   header("Content-Type: image/png");
   ImagePng($img);
   exit;
}

$pourcent= round($nb_acterm/$nb_act,2)*100;
$longueurPlein =ceil($pourcent*$largeur)/100;
$texte = $pourcent."%";
// Création de l'image
$img = ImageCreate($largeur,$hauteur);
// Allocation des couleurs
$couleurFond = ImageColorAllocate($img,0,45,68);
$couleurTexte = ImageColorAllocate($img,255,255,255);
$couleurPourtour = ImageColorAllocate($img,128,128,128);
$couleurPlein = ImageColorAllocate($img,232,126,12);
// On dessine deux rectangles légèrement décalés pour donner une petite impression de relief
//ImageFilledRectangle($img,0,0,$largeur,$hauteur,$couleurPourtour);
ImageFilledRectangle($img,0,0,$largeur,$hauteur,$couleurFond);
//ImageFilledRectangle($img,2,2,$longueurPlein,$hauteur-2,$couleurPourtour );
ImageFilledRectangle($img,0,0,$longueurPlein,$hauteur,$couleurPlein);
// On insère le texte
    //ImageString($img,3,40,1,$texte,$couleurTexte);
// Enfin, on envoie l'image au navigateur
header("Content-Type: image/png"); // format PNG
imagepng($img);
?>