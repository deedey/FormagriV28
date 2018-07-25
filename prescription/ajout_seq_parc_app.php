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
$tab=explode('*',$q);
$id_parc = $tab[1];
$search = $tab[0];

if ($typ_user == "RESPONSABLE_FORMATION" || $typ_user == "ADMINISTRATEUR")
   $list_seq = mysql_query("select seq_cdn,seq_titre_lb from sequence where seq_type_on='0' and seq_titre_lb like \"%$search%\" order by seq_titre_lb");
if ($list_seq == TRUE)
{
    while ($item = mysql_fetch_object($list_seq))
    {
          $num_seq = $item->seq_cdn;
          $titre_seq = strip_tags($item->seq_titre_lb);
          $mess_notif .= "$titre_seq|$num_seq|$id_parc\n";
    }
    sleep(1);
    echo utf2Charset($mess_notif,$charset);
}
?>
