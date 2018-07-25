<?php
session_start();
if (!isset($_SESSION['IDUSER']) || $_SESSION['IDUSER'] == "")
{
  exit();
}
require ("../admin.inc.php");
require ("../fonction.inc.php");
require ("../fonction_html.inc.php");
dbConnect();
$requete = mysql_query("SET character_set_results = 'utf8'");
$req_grp = mysql_query("select * from utilisateur_groupe,groupe WHERE
                        utilgr_utilisateur_no = ".$_SESSION['IDUSER']." AND
                        utilgr_groupe_no = grp_cdn ORDER by grp_nom_lb");
$nomb_grp = mysql_num_rows($req_grp);
$Response=new stdClass();
$items=array();
while ($row = mysql_fetch_object($req_grp))
{
      $items[]=$row;
}
$Response->items=$items;
echo json_encode($Response);
?>
