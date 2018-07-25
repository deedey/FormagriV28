<?php
/*
 * Created on 15 janv. 2009 by Dey Bendifallah
 * Cnerta/Eduter/Enesad/
 */
if (!isset($_SESSION)) session_start();
if (!isset($_SESSION['id_user']) || $_SESSION['id_user'] == "")
{
  exit();
}
header("Content-type  : text/plain");
header("Cache-Control: no-cache, max-age=0");
require '../fonction.inc.php';
require '../admin.inc.php';
include ("../include/UrlParam2PhpVar.inc.php");
require "../lang$lg.inc.php";
require "../fonction_html.inc.php";
require ('../langues/module.inc.php');
dbConnect();
if (isset($_GET['remplir']) && $_GET['remplir'] == 1)
{
  if (!isset($_GET['recuperation']))
  {
   $id_star = Donne_ID ($connect,"SELECT max(star_cdn) from stars");
   $requete = mysql_query("insert into stars values($id_star,".$_SESSION['id_user'].",".$_GET['numero'].",".$_GET['type'].")");
   if (!isset($_GET['notif']))
      echo  utf2Charset("A ete marque comme favori",$charset);
   else
      echo "";
   exit;

  }
  elseif(isset($_GET['recuperation']) && $_GET['recuperation'] == 1)
  {
   $lien = "formation/star.php?numero=$numero&dl=star$i&ajt=starfull&vider=1&i=$i&type=$type";
   $envoi = "<div id='lance$i' title=\"$msg_fav_ot\" onclick=\"javascript:appelle_ajax('$lien'); ".
            "\$('#mien').hide();\$('#star$i').html('');addContent_star('$lien');\"> ".
            "<img src='images/starfull.gif'></div>";
   $nb_star = mysql_num_rows(mysql_query("select * from stars where star_user_id='$id_user' and star_type_no=$type"));

  }
  echo utf2Charset($envoi,$charset);
  exit();
}
if (isset($_GET['vider']) && $_GET['vider'] == 1)
{
  if (!isset($_GET['recuperation']))
  {
   $requete = mysql_query("delete from stars where star_user_id=".$_SESSION['id_user']." and ".
                          "star_item_id=".$_GET['numero']." and star_type_no=".$_GET['type']);
   echo  utf2Charset("A ete enleve de mes favoris",$charset);
   exit;

  }
  elseif(isset($_GET['recuperation']) && $_GET['recuperation'] == 1)
  {
   $lien = "formation/star.php?numero=$numero&dl=star$i&ajt=starfull&remplir=1&i=$i&type=$type";
   $envoi = "<div id='lance$i' title=\"$mess_menu_ajout_favori\" onclick=\"javascript:appelle_ajax('$lien'); ".
            "\$('#mien').hide();\$('#star$i').html(''); addContent_star('$lien');\">".
            "<img src='images/starempty.gif' border=0></div> ";
   $nb_star = mysql_num_rows(mysql_query("select * from stars where star_user_id='$id_user' and star_type_no=$type"));
  }
  echo utf2Charset($envoi,$charset);
  exit();
}
?>
