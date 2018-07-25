<?php
if (!isset($_SESSION)) session_start();
header("Content-type  : text/plain");
header("Cache-Control: no-cache, max-age=0");
require "../admin.inc.php";
require "../fonction.inc.php";
include "../include/UrlParam2PhpVar.inc.php";
require "../lang$lg.inc.php";
require "../langues/module.inc.php";
require "../fonction_html.inc.php";
dbConnect();
if (!empty($_GET['id_parc']) && !empty($_GET['numero']))
{
  $module = $_GET['id_parc'];
  $monnum = $_GET['numero'];
  if (!isset($_GET['recuperation']))
  {
    if ($_GET['objet'] == 'visible')
    {
      $requete = mysql_query("update forums_modules set fm_visible_on = 1 where fm_cdn = ".$_GET['numero']);
      $mess_notif = $msg_SjtVis;
    }
    elseif ($_GET['objet'] == 'invisible')
    {
      $requete = mysql_query("update forums_modules set fm_visible_on = 0 where fm_cdn = ".$_GET['numero']);
      $mess_notif = $msg_SjtNoVis;
    }
    sleep(1);
    echo  utf2Charset($mess_notif,$charset);
  }
  elseif (isset($_GET['recuperation']) && $_GET['recuperation'] == 1)
  {
    if ($_GET['objet'] == 'visible')
    {
       $lien = "formation/forum_mod_modif.php?numero=$monnum&dl=oeil$monnum&objet=invisible&id_parc=$module";
       $envoi = "<A href=\"javascript:void(0);\" ".
                "onClick=\"javascript:appelle_ajax('$lien');".
                "\$('#oeil$monnum').empty();addContent_forum('$lien');\" ". 
                bulle($hide,"","LEFT","ABOVE",100).
                "<IMG SRC='images/modules/visible.gif' BORDER='0'></a>";
    }
    elseif ($_GET['objet'] == 'invisible')
    {
       $lien = "formation/forum_mod_modif.php?numero=$monnum&dl=oeil$monnum&objet=visible&id_parc=$module";
       $envoi = "<A href=\"javascript:void(0);\" ".
                "onClick=\"javascript:appelle_ajax('$lien');".
                "\$('#oeil$monnum').empty();addContent_forum('$lien');\" ". 
                bulle($makevisible,"","LEFT","ABOVE",100).
                "<IMG SRC='images/modules/invisible.gif' BORDER='0'></a>";
    }
    echo utf2Charset($envoi,$charset);
    exit();
  }
}

?>
