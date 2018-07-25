<?php
if (!isset($_SESSION)) session_start();
include ("../include/UrlParam2PhpVar.inc.php");
if (!isset($_SESSION['id_user']) || $_SESSION['id_user'] == "")
{
  exit();
}
require "../admin.inc.php";
require '../fonction.inc.php';
require "../lang$lg.inc.php";
require "../fonction_html.inc.php";
require "../langues/formation.inc.php";
require "../langues/module.inc.php";
require "blogClass.php";
dbConnect();
$nom_user = $_SESSION["name_user"];
$prenom_user = $_SESSION["prename_user"];
$email_user = $_SESSION["email_user"];
include ("../style.inc.php");
if (!empty($_GET['numApp'])) $numApp = $_GET['numApp'];
if (!empty($_GET['numero_groupe'])) $numero_groupe = $_GET['numero_groupe'];
if (!empty($_GET['numeroApp'])) $numeroApp = $_GET['numeroApp'];
if ($_GET['supp'] == 1)
{
  $IdSupp = GetDataField ($connect,"select bgapp_cdn from blogapp where bgapp_app_no='".$_GET['numeroApp']."'","bgapp_cdn");
  $supprimer = mysql_query("delete from blogapp where bgapp_app_no='".$_GET['numeroApp']."'");
  $mess_notif= "Vous venez de supprimer un travail en commun précédement affecté à ".NomUser($_GET['numeroApp']);
}
$reqblog=mysql_query("select * from blog where blog_auteur_no = ".$_SESSION['id_user']);
$nbblog = mysql_num_rows($reqblog);
$content='';
if (!empty($mess_notif))
   echo notifier($mess_notif);
if  ($_GET['supp'] == 1 && $_GET['numeroApp'] == $_GET['numApp'])
{
      echo "<script language=\"JavaScript\">";
          echo " setTimeout(function() {parent.location.reload();},4000)";
      echo "</script>";
}
if ($nbblog > 0)
{
  $nom_actuel = NomUser($_GET['numApp']);
  $reqBg=mysql_query("select * from blog,blogapp where bgapp_app_no='".$_GET['numApp']."' and bgapp_blog_no = blog_cdn");
  $nbBg = mysql_num_rows($reqBg);
  if($nbBg == 0)
  {
     //créer un nouveau groupe
     $content.= newClan($_GET['numApp'],$nom_actuel,$oExist);
     $content .='</div>';
//echo "<pre>";print_r($TabClan);echo "</pre>";
  }
}
else
{
    $content .= "rien pour l'instant";
}
echo $content;
echo '<div id="affiche"></div>';
echo '<div id="mien" class="cms" style="margin-top:50px;" '.
     'title="Cliquer pour fermer cette alerte" onClick="javascript:$(this).empty();";></div>';
//fonctions

function ajtClan($numApp,$nom_actuel,$iClan,$oClan)
{
    GLOBAL $id_parc,$id_seq,$numero_groupe;
    $html = '<div id="ajt'.$iClan.'" style="clear:both;padding:4px;font-size:12px;font-family:arial,verdana;cursor:pointer;"
                                     onClick="javascript:$.ajax({type: \'GET\',
                                              url: \'bloglib.php\',
                                              data: \'numApp='.$numApp.'&ajtClan=1&id_blog='.$oClan->bgapp_blog_no.
                                                     'id_grp='.$numero_groupe.'&id_clan='.$oClan->bgapp_app_no.'\',
                                              beforeSend:function()
                                              {
                                                 $(\'#affiche\').addClass(\'Status\');
                                                 $(\'#affiche\').append(\'Opération en cours....\');
                                              },
                                              success: function(msg)
                                              {
                                                 if (msg == \'no\')
                                                 {
                                                   $(\'#mien\').empty();
                                                   $(\'#mien\').html(\'Attention!! <br />Vous avez déjà affecté à '.$nom_actuel.
                                                                     ' un travail en groupe\');
                                                 }
                                                 else
                                                 {
                                                   $(\'#ajt'.$iClan.'\').empty();
                                                   $(\'#ajt'.$iClan.'\').html(\''.$nom_actuel.'\');
                                                   $(\'#ajt'.$iClan.'\').addClass(\'sequence\');
                                                   $(\'#mien\').empty();
                                                   $(\'#mien\').html(\' Vous avez affecté un travail de groupe à '.$nom_actuel.'\');
                                                 }
                                                 $(\'#affiche\').empty();
                                                 setTimeout(function() {$(\'#mien\').empty();},5000);
                                                 setTimeout(function() {parent.location.reload();},4000);
                                              }
                                        });" '.
                                        'title="Ajoutez  <strong>'.$nom_actuel.'</strong> à ce groupe d\'apprenants">'.
                                        '<img src="images/tete.gif" border="0"> '.
                                        '<img src="images/icogod.gif" style="padding-bottom:4px;" border="0">'.
                                        '<img src="images/bloggrp.gif" border="0"></div>';
     $html .= '<div style="clear:both;text-decoration:underline;font-size:10px;">Membre(s) de ce groupe:'.$iClan.'</div>';
   return $html;
}
function newClan($numApp)
{
    GLOBAL $numero_groupe;
    $html = '<div id="ajtNew" style="clear:both;float:left;padding:4px;background-color:#F1F5F5;margin-top:5px;'.
            'min-height:24px;font-size:12px;border:1px dotted #bbb;font-family:arial,verdana;cursor:pointer;width:98%;"
                                     onClick="javascript:$.ajax({type: \'GET\',
                                              url: \'bloglib.php\',
                                              data: \'numApp='.$numApp.'&ajtNewClan=1&id_grp='.$numero_groupe.'\',
                                              beforeSend:function()
                                              {
                                                 $(\'#affiche\').addClass(\'Status\');
                                                 $(\'#affiche\').append(\'Opération en cours....\');
                                              },
                                              success: function(msg)
                                              {
                                                   $(\'#mien\').empty();
                                                   $(\'#mien\').html(\' Vous venez de creer le thème de votre Blog'\');
                                                   $(\'#affiche\').empty();
                                                   //setTimeout(function() {document.location.reload();},2000);
                                                   setTimeout(function() {parent.location.reload();},4000);
                                              }
                                        });" '.
                                        'title ="Cliquez ici pour créer le thème de votre Blog"'.
                                        '"><div style="clear:both;float:left;'.
                                        'background:url(../images/modules/bloggrp.gif) no-repeat top left;">'.
                                        '<div style="margin-left:35px;">'.clearTheme($oExist->blog_consigne_cmt).'</div></div></div>';
     return $html;
}
function clearTheme($txt)
{
   return str_replace('||','<br',strip_tags(str_replace('<br','||',$txt)));
}
function DivCont()
{
         $html = '<div id="clan" style="clear:both;float:left;background-color: #eee;'.
                    'margin:6px 2px 4px 2px;padding-bottom:4px;max-height:230px;overflow-y:auto;width:100%;">';
         $html .= '<div style="clear:both;border:1px solid #24677A;'.
                     'padding:4px;font-size:11px;font-family:arial,verdana;background-color: #D4E7ED;">'.
                     'Cliquez sur un des thèmes de travail à rendre à affecter à ce nouveau groupe d\'apprenants.</div>';
                     //'Les dates de début et de fin de prescription de cette séquence lui seront affectés par défaut et '.
                     //'vous aurez tout le loisir de les modifier plus tard..
   return $html;
}
function ThemeBg($oClan)
{
     $ordre = GetDataField ($connect,"select blog_ordre_no from blog where blog_cdn='".$oClan->bgapp_blog_no."'","blog_ordre_no");
     $theme = GetDataField ($connect,"select blog_consigne_cmt from blog where blog_cdn='".$oClan->bgapp_blog_no."'","blog_consigne_cmt");
     $html = '<div style="clear:both;text-decoration:underline;font-size:10px;">';
     if ($ordre > 0)
         $html .= '<span style="font-size:11px;font-weight:bold;">N° '.$ordre.' - </span> ';
     $html .= 'Thème traité par ce groupe:</div>'.
              '<div style="clear:both;border:1px dotted #000;font-size:11px;margin:4px;max-width:220px;max-height:60px;overflow-y:auto;">'.
              clearTheme($theme).'</div>';
   return $html;
}
function suppApp($oClan)
{
   GLOBAL $connect,$lg,$numApp,$id_seq,$id_parc,$numero_groupe,$id_clan;
   $html .= '<div style="float:left;margin:6px 4px 0 10px;cursor:pointer;width=20px;" '.
            'title="Enlever '.NomUser($oClan->bgapp_app_no).' de ce groupe. Pour l\'heure, '.
            'il n\'a encore aucune participation dans le document créé." '.
            'onClick=document.location.replace("blog.php?supp=1&id_seq='.$id_seq.'&numApp='.$numApp.'&id_parc='.
            $id_parc.'&numero_groupe='.$numero_groupe.'&numeroApp='.$oClan->bgapp_app_no.'&id_clan='.$oClan->bgapp_clan_nb.'")>'.
            '<img src="../images/suppression1.gif" border="0"></div>';
   return $html;
}
?>