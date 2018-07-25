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
require 'mindmapClass.php';
dbConnect();
?>
<html>
<HEAD>
<!Quirks mode -->
     <META HTTP-EQUIV="X-UA-Compatible" content="IE=8" />
<META HTTP-EQUIV="Content-Type" content="text/html; charset=iso-8859-1">
<META HTTP-EQUIV="Content-Language" CONTENT="fr-FR">
<META HTTP-EQUIV="Expires" CONTENT="Fri, Jan 01 1900 00:00:00 GMT">
<META NAME="ROBOTS" CONTENT="No Follow">
<META HTTP-EQUIV="Cache-Control" CONTENT="no-cache, must-revalidate">
<META HTTP-EQUIV="Pragma" CONTENT="no-cache">
<link rel="shortcut icon" href="<?php echo $_SESSION['monURI'];?>/favicon.ico" type="image/x-icon" />
<link rel="stylesheet" type="text/css" href="<?php echo $_SESSION['monURI'];?>/mindmap/css/general.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo $_SESSION['monURI'];?>/mindmap/css/box.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo $_SESSION['monURI'];?>/mindmap/css/menu_vert.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo $_SESSION['monURI'];?>/mindmap/css/menu_compare.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo $_SESSION['monURI'];?>/mindmap/css/mindmap.css"/>
<script type="text/javascript" src="javascript/jquery.min.js"></script>
<script type="text/javascript" src="javascript/box.js"></script>
<TITLE>Carte heuristique</TITLE>
</HEAD>
<?php
$leBody = '';
if (!isset($_GET['numero']))
{
   $leBody = '<body onload="javascript:$.post(\'lock.php\',{id : \''.$_GET['id'].'\',Provenance : \''.$_GET['Provenance'].'\'});" '.
             ' onunload="javascript:parent.opener.DoTheRefresh();">';
}
else
   $leBody .='<body>';
echo $leBody;
if (!isset($_SESSION['id_clan']) && isset($_GET["id_clan"]) && $_GET["id_clan"] > 0)
   $_SESSION['idClan'] = $_GET["id_clan"];
else
{
   $_SESSION['idClan'] = 0;
   $_GET["id_clan"]=0;
}
$ReqMindH = mysql_query("select * from mindmap,mindmaphistory where mindmap_cdn = ".$_GET['id'].
                          " and mindhisto_map_no = mindmap_cdn order by mindhisto_create_dt desc");
if ($_SESSION['typ_user'] != 'APPRENANT' && mysql_num_rows($ReqMindH) > 0)
{
    echo '<div class="mindmap_hint">';
      $leNumero = (!empty($_GET['numero'])) ? $_GET['numero'] : mysql_result($ReqMindH,0,'mindhisto_cdn');
      $content = '';
      $content .= '<div id="menuComp" class="historyWk" style="margin-left:10px;">';
      $content .= str_replace('&lt;','<',str_replace('&gt;','>',compareWk($_GET['id'], $leNumero)));
      $content .= '</div>';
      $content .= '<div id="HWk" class="historyWk1">';
      $content .= str_replace('&lt;','<',str_replace('&gt;','>',HwWk($_GET['id'], $leNumero )));
      $content .= '</div>';
      if (isset($_GET['numero']))
      {
         $content .= '<div id="Ret" class="historyWk1" style="margin-top:8px;">';
         $content .= '<a href="index.php?id='.$_GET['id'].'&Provenance='.$_GET['Provenance'].'&id_clan='.
                     $_SESSION['idClan'].'"><span class="bouton1">Revenir en mode Création</span></div>';
         $content .= '</div>';
      }
    echo clean_text($content).'</div>';
}
if (!isset($_GET['numero']))
{
     //include ("../style.inc");
     
     $ReqMind = mysql_query("select * from mindmap where mindmap_cdn = ".$_GET['id']);
     $mindmap = mysql_fetch_object($ReqMind);
     if ($mindmap->mindmap_locking_on > 0 && $mindmap->mindmap_locked_on > 0 && $mindmap->mindmap_idlock_no != $_SESSION['id_user']) 
     {
         //Override lock for teachers
         echo '<div id="delocker" class="mindmap_locked"><span style="float:left;padding-top:5px;">
              Attention!!! La modification n\'est pas autorisée car <b>'.NomUser($mindmap->mindmap_idlock_no).
              '</b> l\'utilise en ce moment.</span>';
         if ($_SESSION['typ_user'] != 'APPRENANT')
         {
              echo '<div style="float:left;padding-left:10px;margin-top:5px;" '.
                         'onClick= "javascript:$.ajax({
                                   type: \'POST\',
                                   url: \'unlock.php\',
                                   data: {\'id\':'.$mindmap->mindmap_cdn.',\'blocID\' :'.$mindmap->mindmap_idlock_no.',\'Provenance\' : \''.$_GET['Provenance'].'\'},
                                   success: function()
                                   {
                                       document.location.replace(\'index.php?id='.$_GET['id'].'&Provenance='.$_GET['Provenance'].'&id_clan='.$_SESSION['idClan'].'\');
                                   }
                         });"><span class="bouton1">Débloquer la modification</span></div>';
          }
          echo '</div>';
      }
      if ($mindmap->mindmap_locking_on == 0 || ($mindmap->mindmap_locking_on > 0 &&
         (($mindmap->mindmap_locked_on == 1 && $mindmap->mindmap_idlock_no == $_SESSION['id_user'] ) || ($mindmap->mindmap_locked_on == 0))))
         echo '<div class="mindmap_locked"><span style="float:left;padding-top:5px;">Cliquez sur la touche "INSERT" pour ajouter un item!</span></div>';
      ?>
      <div id="flashcontent"></div>
      <script type="text/javascript" src="javascript/swfobject.js"></script>
      <script type="text/javascript">
        var so = new SWFObject('viewer.swf?uVal=<?php echo rand(0,100); ?>', 'viewer', '100%', '600', '9', '#FFFFFF');
            so.addVariable('load_url', 'xml.php?id=<?php echo $_GET["id"]; ?>');
            so.addVariable('save_url', 'save.php?id=<?php echo $_GET["id"];?>');
            <?php if ($mindmap->mindmap_locking_on == 0) { ?>
                    so.addVariable('editable', 'true');
            <?php } else { ?>
                <?php if ($mindmap->mindmap_locking_on > 0 && (($mindmap->mindmap_locked_on == 1 && $mindmap->mindmap_idlock_no == $_SESSION['id_user'] ) || ($mindmap->mindmap_locked_on == 0))) { ?>
                    so.addVariable('editable', 'true');
                <?php } ?>
            <?php } ?>
            so.addVariable('lang', 'en');
            so.addVariable('wmode', 'direct');
            so.write('flashcontent');
      </script>
<?php
}
else
{
?>
   <div id="flashcontent"></div>
   <script type="text/javascript" src="javascript/swfobject.js"></script>
   <script type="text/javascript">
    var so = new SWFObject('viewer.swf?uVal=<?php echo rand(0,100); ?>', 'viewer', '100%', '600', '9', '#FFFFFF');
        so.addVariable('load_url', 'xml.php?numero=<?php echo $_GET["numero"]; ?>');
        so.addVariable('lang', 'en');
        so.addVariable('wmode', 'direct');
        so.write('flashcontent');
   </script>
<?php
}
?>
</body>
</html>
