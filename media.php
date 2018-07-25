<div id="player<?php echo $actit;?>" style="clear:both;"></div>
<?php
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
$ajoutLink= (strstr($media_act,'http://')) ? "" : "$monURI/";
?>

<script type="text/javascript">
	var s<?php echo $actit;?> = new SWFObject("ressources/flvplayer.swf","single","<?php echo $largeur;?>","<?php echo $hauteur;?>","7");
	s<?php echo $actit;?>.addParam('allowscriptaccess','always');
	s<?php echo $actit;?>.addParam("allowfullscreen","true");
	s<?php echo $actit;?>.addParam("wmode","transparent");
	s<?php echo $actit;?>.addVariable("file","<?php echo "$ajoutLink"."$media_act";?>");
	s<?php echo $actit;?>.addVariable("image","images/menu/logformb.gif");
	s<?php echo $actit;?>.addVariable("backcolor","0xFFFFFF");
	s<?php echo $actit;?>.addVariable("frontcolor","0x000000");
	s<?php echo $actit;?>.addVariable('lightcolor','0xFF0000');
	s<?php echo $actit;?>.addVariable('screencolor','0x000000');
	s<?php echo $actit;?>.write("player<?php echo $actit;?>");
</script>
