<?php
if (!isset($_SESSION)) session_start();
include ("../include/UrlParam2PhpVar.inc.php");
require '../admin.inc.php';
require '../fonction.inc.php';
require '../fonction_html.inc.php';
require "../lang$lg.inc.php";
require "../langues/adm.inc.php";
dbConnect();
header("Content-type  : text/plain");
header("Cache-Control: no-cache, max-age=0");
$q = strtolower($_GET["q"]);
if (!$q) return;

      if ($typ_user == "RESPONSABLE_FORMATION" || $typ_user == "ADMINISTRATEUR")
         $list_parc = mysql_query("select parcours_cdn,parcours_nom_lb from parcours where parcours_type_on='0' and parcours_cdn > 0 and parcours_nom_lb like \"%$q%\" order by parcours_nom_lb");
      if ($list_parc == TRUE)
      {
          $c = 0;
          while ($item = mysql_fetch_object($list_parc))
          {
                $c++;
                $num_parc = $item->parcours_cdn;
                $titre_parc = strip_tags($item->parcours_nom_lb);
                $mess_notif = "$titre_parc|$num_parc\n";
                echo utf2Charset($mess_notif,$charset);
          }
      }
sleep(1);
?>