<?php
if (!isset($_SESSION)) session_start();
if (!isset($_SESSION['id_user']) || $_SESSION['id_user'] == "")
{
  exit();
}
include ("include/UrlParam2PhpVar.inc.php");
require "admin.inc.php";
require 'fonction.inc.php';
require "lang$lg.inc.php";
require "fonction_html.inc.php";
dbConnect();
if (!empty($_GET['id_act']))
    $media_act = GetDataField ($connect,"select ress_url_lb from ressource_new,activite where
                 ress_cdn = act_ress_no and act_cdn = '".$_GET['id_act']."'","ress_url_lb");
elseif (!empty($_GET['id_ress']))
    $media_act = GetDataField ($connect,"select ress_url_lb from ressource_new where
                 ress_cdn = '".$_GET['id_ress']."'","ress_url_lb");
elseif (!empty($_GET['id_rep']))
    $media_act = urldecode($_GET['id_rep']);

if (!strstr($media_act,'http://'))
    $media_act = $adresse_http.'/'.$media_act;
if($media_act != "")
{
   if (strstr(strtolower($media_act),'.flv') ||
       strstr(strtolower($media_act),'.mp3') ||
       strstr(strtolower($media_act),'.swf'))
   {
          include ("style.inc.php");
          $actit = $_GET['id_act'];
          $largeur = "400";
          $hauteur = "240";
          include ("media.php");
   }
   elseif(strstr(strtolower($media_act),'.mp4') ||
       strstr(strtolower($media_act),'.webm') ||
       strstr(strtolower($media_act),'.ogv'))
   {
          $actit = $_GET['id_act'];
          if (empty($_GET['largeur']))
          {
             $largeur = "400";
             $hauteur = "240";
             $css = "playful";
             $autoplay = ' autoplay ';
          }
          else
          {
             $css = "minimalist";
             $autoplay = ' poster = "images/entreedef1.jpg" ';
          }
      list($extension, $nom) = getextension($media_act);
      $ajoutLink= (strstr($media_act,'http://')) ? "" : "$monURI/";
      $content =  '
      <!doctype html>
      <head>
       <!-- player skin -->
       <link rel="stylesheet" type="text/css" href="OutilsJs/lib/'.$css.'.css" />
       <!-- site specific styling -->
       <style>
          body { font: 12px "Myriad Pro", "Lucida Grande", sans-serif; text-align: center; top:0;left:0;}
          .flowplayer { width: '.$largeur.'px; }
       </style>
       <!-- flowplayer depends on jQuery 1.4+ (for now) -->
       <script src="OutilsJs/jquery-182-min.js"></script>
          <!-- include flowplayer -->
       <script src="OutilsJs/flowplayer.js"></script>
     </head>
     <body>
       <!-- the player -->
       <div class="flowplayer" data-engine="flash" style="width: '.$largeur.'; height: '.$hauteur.'px" '.
       ' data-swf="ressources/flowplayer.swf" data-ratio="0.417">
         <video '.$autoplay.' >
           <source type="video/'.$extension.'" src="'.$ajoutLink.$media_act.'"/>
           <!-- <source type="video/webm" src="http://stream.flowplayer.org/bauhaus/624x260.webm"/> -->
           <!-- <source type="video/mp4" src="http://stream.flowplayer.org/bauhaus/624x260.mp4"/> -->
           <!-- <source type="video/ogv" src="http://stream.flowplayer.org/bauhaus/624x260.ogv"/> -->
         </video>
      </div>
     </body>';
     echo $content;
   }
}


function getextension($fichier)
{
  $bouts = explode(".", $fichier);
  return array(array_pop($bouts), implode(".", $bouts));
}

?>
