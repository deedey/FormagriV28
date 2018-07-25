<?php
if (!isset($_SESSION)) session_start();
include ("include/UrlParam2PhpVar.inc.php");
if (!isset($_SESSION['id_user']) || $_SESSION['id_user'] == "")
{
  exit();
}
require "admin.inc.php";
require 'fonction.inc.php';
$fileImport = file_get_contents(urldecode($_GET['file']));
$tab = explode('/',urldecode($_GET['file']));
$nbr = count($tab);
$newfile = str_replace(".exp",".php",$tab[$nbr-1]);
$dir_file="import/".$newfile;
if (!empty($fileImport))
{
  $fp = fopen($dir_file, "w+");
     $fw = fwrite($fp,$fileImport);
  fclose($fp);
  chmod($dir_file,0664);
}
else
{ 
   echo "<div style='clear:both;float:left;padding:10px;margin-top:200px;margin-left:200px;
                     border=2px dotted red; background-color:#eee;color:blue;
                     font-size:16px;font-weight:bold;font-family:arial, verdana, tahoma;'>
              Le fichier n'a pu être importé en raison de restrictions posées par votre hébergeur. Contactez-le.. <br />
              <span style='color:black;font-size:11px;font-weight:normal;'>
                    l'équipe de Cnerta/Eduter/AgoSupDijon.
              </span>
        </div>";
   exit;
}
echo "<script language=\"JavaScript\">";
    echo "document.location.replace(\"$dir_file\");";
echo "</script>";
?>