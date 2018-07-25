<?php
include ("include/UrlParam2PhpVar.inc.php");
$nbr = strlen($text);
while ($i < $nbr){
   $lettre = substr($text, $i);
   if ($i == 0){
     $im = imagecreatefromgif("images/complement/milieu.gif");
     $orange = imagecolorallocate($im,  0,0,0);
     imagestring($im, 3, 4, 9, $lettre, $orange);
   }else{
     $img = imagecreatefromgif("images/complement/milieu.gif");
     $orange = imagecolorallocate($img, 0,0,0);
     imagestring($img, 3, 4, 9, $lettre, $orange);
   }
   if ($i != 0)
     imagecopymerge($im, $img, 0, 0, $i+4, 0, 256, 256, 60);
   $i++;
}
header('Content-type: image/gif');
imagegif($im);
imagedestroy($im);
imagedestroy($img);
?>