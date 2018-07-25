<?php

if (!isset($_SESSION)) session_start();
include ("../include/UrlParam2PhpVar.inc.php");
require '../admin.inc.php';
require '../fonction.inc.php';
require '../fonction_html.inc.php';
require "../lang$lg.inc.php";
require "../langues/adm.inc.php";
require "../langues/prescription.inc.php";
dbConnect();
header("Content-type  : text/plain");
header("Cache-Control: no-cache, max-age=0");
$q = strtolower($_GET["q"]);
if (!$q) return;
      if ($typ_user == "RESPONSABLE_FORMATION" || $typ_user == "ADMINISTRATEUR")
         $list_presc = mysql_query("SELECT util_cdn,util_nom_lb,util_prenom_lb from utilisateur where (util_typutil_lb = 'RESPONSABLE_FORMATION' or util_typutil_lb = 'ADMINISTRATEUR') AND util_flag = 0 AND (util_nom_lb like \"%$q%\" OR util_prenom_lb like \"%$q%\") order by util_nom_lb ASC");
      if ($list_presc == TRUE)
      {
          while ($item = mysql_fetch_object($list_presc))
          {
                $c++;
                $presc_ref = $item->util_cdn;
                $lenom = strip_tags($item->util_nom_lb)." ".strip_tags($item->util_prenom_lb);
                $mess_notif = "$lenom|$presc_ref|presc\n";
                echo utf2Charset($mess_notif,$charset);
          }
      }
      else
                echo utf2Charset($mesg_modpresc."| ",$charset);
//sleep(1);
?>