<?php

if (!isset($_SESSION)) session_start();
include ("../include/UrlParam2PhpVar.inc.php");
require '../admin.inc.php';
require '../fonction.inc.php';
require "../lang$lg.inc.php";
require '../fonction_html.inc.php';
dbConnect();

$ContentHead = '';
$ContentHead .= '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
                <html>
                <HEAD>
                <!Quirks mode -->
                <META HTTP-EQUIV="X-UA-Compatible" content="IE=8" />
                <META HTTP-EQUIV="Content-Type" content="text/html; charset='.$charset.'">
                <META HTTP-EQUIV="Content-Language" CONTENT="'.$code_langage.'">
                <META HTTP-EQUIV="Expires" CONTENT="Fri, Jan 01 1900 00:00:00 GMT">
                <META NAME="ROBOTS" CONTENT="No Follow">
                <META HTTP-EQUIV="Cache-Control" CONTENT="no-cache, must-revalidate">
                <META HTTP-EQUIV="Pragma" CONTENT="no-cache">';

$ContentHead .= '<TITLE>Formagri :: '.str_replace('.educagri.fr','',str_replace('cfppa-','',str_replace('ef-','',$_SERVER['SERVER_NAME']))).
                  ' :: '.$RoleUser.' :: '.$currentUser.'</TITLE>';
$ContentHead .= '<link rel="shortcut icon" href="/images/icone.ico" type="image/x-icon" />
                <link rel="stylesheet" type="text/css" href="../general.css" />
                <link rel="stylesheet" type="text/css" href="../admin/style_admin.css" />
                <link rel="stylesheet" type="text/css" href="../OutilsJs/style_jquery.css" />
                <link rel="stylesheet" type="text/css" href="../OutilsJs/lib/simplePagination.css"/>
                <script type="text/javascript" src="../OutilsJs/jquery-182-min.js"></script>
                <script type="text/javascript" src="../OutilsJs/lib/jquery.simplePagination.js"></script>';
$ContentHead .= '<div id="overDiv" style="position:absolute; visibility:hidden;z-index:1000;"></div>
                <SCRIPT type="text/javascript" SRC="'.$adresse_http.'/overlib.js"><!-- overLIB (c) Erik Bosrup --></SCRIPT>';
echo $ContentHead;
$url = parse_url($_SERVER['REQUEST_URI']);
$resultUrl=array();
parse_str($url['query'],$resultUrl);
$num_app = (isset($utilisateur) && $utilisateur > 0) ? $utilisateur : $_SESSION['id_user'] ;
$typo = ( $_SESSION['typ_user'] == 'APPRENANT') ? 0 : 1 ;
//echo "<pre>";print_r($_SESSION);echo "</pre>";
if ($_SESSION['onLine'] == 1)
{
echo "<div>
<script language='javascript'>
     $.ajax({
                type: 'GET',
                 url: 'http://lms.annulab.com/TinCanApi/statements.php',
                 data: 'utilisateur=$num_app&typo=$typo&course=".$resultUrl['course']."&registration=".$resultUrl['registration']."',
                 success: function(msg)
                 {
                   \$('#xApi').html(msg);
                     //alert(msg);
                 }
      });
</script>
</div>";
}
echo "<div id='xApi'></div>";
?>