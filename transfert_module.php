<?php
if (!isset($_SESSION)) session_start();
include ("include/UrlParam2PhpVar.inc.php");
if (!isset($_SESSION['id_user']) || $_SESSION['id_user'] == "")
{
  exit();
}
require "admin.inc.php";
require 'fonction.inc.php';
Connecter($_GET['origine']);
$mareq = mysql_query("select * from export where export_link_lb = '".$_GET['export']."'");
if (mysql_num_rows($mareq) > 0)
{
   $content = mysql_result($mareq,0,'export_content_cmt');
   $newfile = str_replace(".exp",".php",$_GET['export']);
   $dir_file="import/".$newfile;
   if (!empty($content))
   {
      $fp = fopen($dir_file, "w+");
          $fw = fwrite($fp,urldecode($content));
      fclose($fp);
      chmod($dir_file,0664);
      echo urldecode($content);
   }
}
else
{
       echo "<div style='clear:both;float:left;padding:10px;margin-top:200px;margin-left:200px;
                     border=2px dotted red; background-color:#eee;color:blue;
                     font-size:16px;font-weight:bold;font-family:arial, verdana, tahoma;'>
              Vous avez sûrement importé ce module auparavant.. Vérifiez s'il vous plaît..<br />
              <span style='color:black;font-size:11px;font-weight:normal;'>
                    l'équipe de Cnerta/Eduter/AgoSupDijon.
              </span>
        </div>";
   exit;
}
$mareq = mysql_query("delete from export where export_link_lb = '".$_GET['export']."'");
echo "<script language=\"JavaScript\">";
    echo "document.location.replace(\"$dir_file\");";
echo "</script>";
?>
