//Set delay before submenu disappears after mouse moves out of it (in milliseconds)
var delay_hide=500

/////No need to edit beyond here

var menuobj=document.getElementById? document.getElementById("describe") : document.all? document.all.describe : document.layers? document.dep1.document.dep2 : ""
function showit(which){
 clear_delayhide()
 thecontent=(which==-1)? "&nbsp;" : submenu[which]
 if (document.getElementById||document.all)
   menuobj.innerHTML=thecontent
 else if (document.layers){
   menuobj.document.write(thecontent)
   menuobj.document.close()
 }
}

function resetit(e){
 if (document.all&&!menuobj.contains(e.toElement))
   delayhide=setTimeout("showit(-1)",delay_hide)
 else if (document.getElementById&&e.currentTarget!= e.relatedTarget&& !contains_ns6(e.currentTarget, e.relatedTarget))
   delayhide=setTimeout("showit(-1)",delay_hide)
}

function clear_delayhide(){
  if (window.delayhide)
  clearTimeout(delayhide)
}

function contains_ns6(a, b) {
 while (b.parentNode)
  if ((b = b.parentNode) == a)
   return true;
  return false;
}
</script>
<script type="text/javascript">
    var _gaq=_gaq||[];var pluginUrl = '//www.google-analytics.com/plugins/ga/inpage_linkid.js';_gaq.push(['_require', 'inpage_linkid', pluginUrl]);_gaq.push(['_setAccount','UA-42020054-1']);_gaq.push(['_trackPageview']);_gaq.push(['_setDomainName','none']);(function(){var ga=document.createElement('script');ga.type='text/javascript';ga.async=true;ga.src=('https:'==document.location.protocol?'https://ssl':'http://www')+'.google-analytics.com/ga.js';var s=document.getElementsByTagName('script')[0];s.parentNode.insertBefore(ga,s)})();
</script>
<?php
  $agent=getenv("HTTP_USER_AGENT");
  if (strstr($agent,"Mac") || strstr($agent,"Konqueror"))
    $mac=1;
  if (strstr($agent,"MSIE") || strstr($agent,"Opera"))
    $revient=1;
  if (isset($full) && $full == 1 && isset($revient) && $revient == 1 && (!isset($complement) || $complement != 1)){
   ?>
   <SCRIPT Language="Javascript" type="text/javascript">
     window.parent.opener.location.reload('<?php echo $monURI;?>/index.php?fermeture=1');
   </SCRIPT>
   <?php
  }elseif(isset($full) && $full == 1 && $mac != 1 && $revient != 1){
   ?>
   <SCRIPT Language="Javascript" type="text/javascript">
      parent.parent.opener.close();
   </SCRIPT>
   <?php
  }
?>