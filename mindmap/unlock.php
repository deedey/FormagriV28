<?php
session_start();
if (!isset($_SESSION['id_user']) || $_SESSION['id_user'] == "")
{
  exit();
}
include ("../include/UrlParam2PhpVar.inc.php");
require "../admin.inc.php";
require '../fonction.inc.php';
require '../fonction_html.inc.php';
dbConnect();
GLOBAL $connect;
if (isset($_POST['Provenance']))
   $Provenance = $_POST['Provenance'];
elseif(isset($_GET['Provenance']))
   $Provenance = $_GET['Provenance'];
if (isset($Provenance) && $Provenance == 'GRP')
 $GrP = $id_grp;
elseif (isset($Provenance) && $Provenance == 'SEQ')
 $ID = $id;
if (isset($GrP))
    $ReqUpdateXml = mysql_query('update mindmap set mindmap_locked_on = "0" where mindmap_grp_no = '.$GrP.' and mindmap_idlock_no = '.$_SESSION['id_user']);
elseif (isset($GrP))
{
    $ReqMind = mysql_query("select * from mindmap where mindmap_cdn = ".$ID);
    $XmlMap = mysql_fetch_object($ReqMind);

    if ($_SESSION['typ_user'] != 'APPRENANT' && $XmlMap->mindmap_locked_on == 1 &&
        isset($_POST['id']) && $XmlMap->mindmap_idlock_no != $_SESSION['id_user'])
    {
        $IDcauser = Donne_ID ($connect,"SELECT max(causer_cdn) from causer");
        $message = "Oups! ".NomUser($_SESSION['id_user'])." vient de bloquer cette carte.";
        $req = mysql_query("insert into causer values($IDcauser,".$_SESSION['id_user'].",".$_POST['blocID'].",\" ".$message."\")");
        $ReqUpdateXml = mysql_query('update mindmap set mindmap_locked_on = 0,mindmap_locked_on = 0,mindmap_idlock_no = '.
                            $_SESSION['id_user'].' where mindmap_cdn = '.$ID);
    }
    elseif ($XmlMap->mindmap_locked_on == 1 && isset($_GET['id']) && $XmlMap->mindmap_idlock_no == $_SESSION['id_user'])
       $ReqUpdateXml = mysql_query('update mindmap set mindmap_locked_on = "0",mindmap_locked_on = "0" where mindmap_cdn = '.$ID);
}
if (isset($_POST['id']))
{
   echo "<script language='JavaScript'>";
     echo "document.location.replace(\"index.php?id=".$ID."&id_clan=".$_SESSION['idClan']."\");";
   echo "</script>";
}
else
{
  unset($_SESSION['idClan']);
}
?>