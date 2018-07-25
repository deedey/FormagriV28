<?php
if (!isset($_SESSION)) session_start();
$NbT = 60;
if (isset($_SESSION['TinySeqTwit']))
{
   $Seq = $_SESSION['TinySeqTwit'];
   $Qui = $_SESSION['name_user'].' '.$_SESSION['prename_user'];
}
else
{
   $Seq = 'MaSeq';
   $Qui = 'SuperFormateur';
}
?>
