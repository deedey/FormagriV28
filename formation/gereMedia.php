<?php
/*
 * Created on 25 fév. 2010 by Dey Bendifallah
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
require "../lang$lg.inc.php";
require "../fonction_html.inc.php";
dbConnect();
if ($_GET['suppMedia'] == 1 && isset($_GET['id_act']) && $_GET['id_act'] > 0)
{
  $req_sql = mysql_query("delete from activite_media where actmedia_act_no = ".$_GET['id_act']);
  exit();
}
if ($_GET['affMedia'] == 1 && isset($_GET['actit']) && $_GET['actit'] > 0)
{
if (empty($largeur) && empty($hauteur))
{
  $largeur = "300";
  $hauteur = "220";
}
elseif (strstr(strtolower($media_act),".mp3"))
{
  $largeur = "180";
  $hauteur = "80";
}
$envoi='<div id="player'.$actit.'" style="clear:both;"></div>
<script type="text/javascript">
	var s'.$actit.' = new SWFObject("ressources/flvplayer.swf","single","'.$largeur.'","'.$hauteur.'","7");
	s<?php echo $actit;?>.addParam("allowscriptaccess","always");
	s<?php echo $actit;?>.addParam("allowfullscreen","true");
	s<?php echo $actit;?>.addParam("wmode","transparent");
	s<?php echo $actit;?>.addVariable("file","<?php echo "$media_act";?>");
	s<?php echo $actit;?>.addVariable("image","images/menu/logformb.gif");
	s<?php echo $actit;?>.addVariable("backcolor","0xFFFFFF");
	s<?php echo $actit;?>.addVariable("frontcolor","0x000000");
	s<?php echo $actit;?>.addVariable("lightcolor","0xFF0000");
	s<?php echo $actit;?>.addVariable("screencolor","0x000000");
	s'.$actit.'.write("player'.$actit.'");
</script>';
echo $envoi;
exit();
}
?>