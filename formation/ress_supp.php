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
$id_ress = GetDataField($connect,"SELECT act_ress_no FROM activite WHERE act_cdn = $id_act","act_ress_no");
$url_ress = GetDataField($connect,"SELECT ress_url_lb FROM ressource_new WHERE ress_cdn = $id_ress","ress_url_lb");
$nb_ress = mysql_result(mysql_query("SELECT count(ress_cdn) FROM ressource_new WHERE ress_url_lb = \"$url_ress\""),0);
$supp_ress = mysql_query("UPDATE activite SET act_ress_no='0' WHERE act_cdn = $id_act");
if (strstr($url_ress,"forum/read.php") && $nb_ress == 1)
   $requete = mysql_query("DELETE FROM ressource_new WHERE ress_cdn = $id_ress");
if ($flag == 1)
{
  $req_grp = mysql_query ("select grp_cdn from groupe order by grp_cdn");
  if ($req_grp)
  {
      while ($item = mysql_fetch_object($req_grp))
      {
           $num_grp = $item->grp_cdn;
           if ($ress_norok == "NON")
              $changer_suivi = mysql_query ("update suivi1_$num_grp set suivi_etat_lb = 'PRESENTIEL' where suivi_act_no =$id_act AND suivi_etat_lb != 'TERRMINE'");
           elseif ($ress_norok == "OUI")
              $changer_suivi = mysql_query ("update suivi1_$num_grp set suivi_etat_lb = 'A FAIRE' where suivi_act_no =$id_act AND suivi_etat_lb != 'ATTENTE' AND suivi_etat_lb != 'TERMINE'");
      }
  }
}
$mess_notif = $mmsg_supact;
sleep(1);
echo  utf2Charset($mess_notif,"iso-8859-1");
?>
