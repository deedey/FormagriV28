<?php
if (!isset($_SESSION)) session_start();
include ("include/UrlParam2PhpVar.inc.php");
if (!isset($_SESSION['id_user']) || $_SESSION['id_user'] == "")
{
  exit();
}
require 'fonction.inc.php';
require "admin.inc.php";
require "lang$lg.inc.php";
dbconnect();
list($un,$de,$tr,$qu,$cq,$sx,$serie,$suite) = explode("&",$lien,8);
list($act,$id_act) = explode("=",$serie);
$_SESSION['id_act'] = $id_act;
?>
<HTML>
<HEAD>
<script type="text/javascript" src="OutilsJs/jquery-113.js"></script>
<script type="text/javascript" src="OutilsJs/lib/ajax/ajax_cms.js"></script>
</script>
<TITLE><?php echo $msq_activite;?></TITLE>
<script language="javascript">
   function loadData() {
        simplejQ_Ajax("quit_activ.php?id_act=<?php echo $id_act?>");
   }
</script>
</HEAD>
<body background="white" marginwidth="0" marginheight="0" leftmargin="0" topmargin="0"onunload="loadData();">
<div id="affiche" class="Status"></div>
<?php
    $agent=getenv("HTTP_USER_AGENT");
    if (strstr($agent,"MSIE"))
    {
       echo "<SCRIPT Language=\"Javascript\">";
          echo "opener.DoTheRefresh();";
       echo "</SCRIPT>";
    }
    else
    {
       echo "<SCRIPT Language=\"Javascript\">";
          echo "opener.DoTheRefresh();";
       echo "</SCRIPT>";
    }
?>
</body>
</html>