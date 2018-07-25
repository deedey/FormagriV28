<?php
if (!isset($_SESSION)) session_start();
if (!isset($_SESSION['id_user']) || $_SESSION['id_user'] == "")
{
  exit();
}
include ("../include/UrlParam2PhpVar.inc.php");
require '../admin.inc.php';
require '../fonction.inc.php';
require "../lang$lg.inc.php";
dbconnect();
$agent=$_SERVER["HTTP_USER_AGENT"];
$date_acces = date("Y-m-d H:i:s" ,time());
$requete = mysql_query("UPDATE scorm_util_module_$numero_groupe SET last_acces = \"$date_acces\" WHERE
                        mod_module_no='$scormid' AND  user_module_no='$id_user'");
list($dtj,$hdeb) = explode(" ",$date_acces);
$num = Donne_ID ($connect,"select max(traq_cdn) from traque");
$insere_traq= mysql_query("INSERT INTO traque (traq_cdn,traq_util_no,traq_mod_no,traq_grp_no,traq_date_dt,traq_hd_dt) values
                          ($num,$id_user,$scormid,$numero_groupe,'$dtj','$hdeb')");
?>
<HTML>
<HEAD>
<TITLE><?php echo $msq_activite;?></TITLE>
<script type="text/javascript" src="../fonction.js"></script>
<script language="javascript">
   function loadData() {

        sendData('','validation.php?scormid=<?php echo $scormid?>','POST');
   }
</script>
</HEAD>
<body marginwidth="0" marginheight="0" leftmargin="0" topmargin="0" onunload="loadData();">
</body>
</html>
