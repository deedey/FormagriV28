<?php
if (!isset($_SESSION)) session_start();
require '../fonction.inc.php';
require '../fonction_html.inc.php';
require '../admin.inc.php';
require "../lang$lg.inc.php";
dbConnect();
if($lg == ""){
  include ('../deconnexion-fr.txt');
  exit();
}
if (isset($downloader) && $downloader == 1){
  ForceFileDownload($entete,'html');
  unlink($entete);
  exit;
}
include ('../style.inc.php');
$entete = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML>
<HEAD>
<META HTTP-EQUIV="Content-Type" content="text/html; charset='.$charset.'">
<META HTTP-EQUIV="Content-Language" CONTENT="'.$code_langage.'">
<META HTTP-EQUIV="Expires" CONTENT="Fri, Jan 01 1900 00:00:00 GMT">
<META NAME="ROBOTS" CONTENT="No Follow">
<META HTTP-EQUIV="Cache-Control" CONTENT="no-cache, must-revalidate">
<META HTTP-EQUIV="Pragma" CONTENT="no-cache">
';
echo "<div id='acceuil' style=\"float:left;margin-left:2px;margin-right:10px;\">".
     "<a href=\"../forum/list.php?f=$f\" class= 'bouton_new'>$mess_acc</a></div><br />";
$tit_forum = GetDataField ($connect,"select name from forums WHERE id = $f","name");
$nom_forum = GetDataField ($connect,"select table_name from forums where id = $f","table_name");
if (isset($t) && $t > 0)
{
   $titre_fil = GetDataField ($connect,"select subject from $nom_forum WHERE parent = 0 and thread=$t","subject");
}
else
{
   if ($f > 0 && $f < 6)
       $titre_forum = $nom_forum;
   else
       $titre_forum = $tit_forum;
}
$titre = ($t > 0) ?  "Contenu du sujet de discussion [ $titre_fil ] du forum :$tit_forum " : "Forum de la formation ".$tit_forum;
function getItems()
{
  GLOBAL $connect,$f,$t,$titre;
  $nom_forum = GetDataField ($connect,"select table_name from forums where id = $f","table_name");
  $monT = ($t > 0) ?  "thread = $t" : "thread > 0";
  $req_fil = requete_order("*",$nom_forum,$monT,"id");
  if ($req_fil == TRUE)
  {
     echo '<H4 style="font-family: arial, tahoma,  serif;color: #003333;margin-left:40px;padding-top:15px;">'.$titre.'</H4>';
     echo "<div id='bodi' class='bodi' onclick=\"javascript:\$(bodi).empty();\$(bodi).hide();\" title='$mess_clkF'></div>";
     $aItems=array();
     $cpt = 0;
     while ($item = mysql_fetch_object($req_fil))
     {
         $cpt++;
         $top = 40;//*$cpt;content.style.top='".$top."px';
         $obj = new stdClass();
         $obj->id = $item->id;
         $obj->parent = $item->parent;
         $obj->thread = $item->thread;
         $obj->author = $item->author;
         $date_post = $item->datestamp;
         $dater = substr($date_post,0,10);
         $ch_date = explode ("-",$dater);
         $obj->date = "$ch_date[2]/$ch_date[1]/$ch_date[0]";
         $obj->heure = substr($date_post,11);
         $obj->approved = $item ->approved;
         $affiche = "<strong>Posté par : ".$obj->author."<br />";
         $affiche .= "<strong> le".$obj->date." à ".$obj->heure."<br />";
         $ma_liste = array("\t", "\n", "\r");
         $obj->body = trim(str_replace($ma_liste,"",nl2br(html_entity_decode(GetDataField ($connect,"select body from $nom_forum"."_bodies WHERE id = ".$item->id,"body"),ENT_QUOTES,'ISO-8859-1'))));
         //echo$obj->body."<br>";
         $affiche = "<strong>Posté par : ".$obj->author."</strong><br />";
         $affiche .= "<strong>".$obj->date."  ".$obj->heure."</strong><br /><br />";
         if (strstr(strtolower($obj->body),"onclick") || strstr(strtolower($obj->body),".php"))
            $affiche .= "<strong><font color=red>Ce message contient un appel javascript ou à une application.<br />".
                        "Il ne peut donc être affiché</font><strong>";
         else
            $affiche .= $obj->body;

         $obj->subject = "<a href=\"#\" onMouseOver=\"javascript:\$(bodi).show();\$(bodi).addClass('bodi_over');".
                         "\$(bodi).empty();\$(bodi).append('". htmlspecialchars(addslashes($affiche),ENT_QUOTES,'ISO-8859-1')."');\"\n".
                         " onClick=\"javascript:\$(bodi).show();\$(bodi).addClass('bodi_over');".
                         "\$(bodi).empty();\$(bodi).append('". htmlspecialchars(addslashes($affiche),ENT_QUOTES,'ISO-8859-1')."');\">\n".
                         $item->subject."</A>\n";

         $aItems[]=$obj;
         $item=null;
     }
 }
 return $aItems;
}
$entete .= "<TITLE>$titre</TITLE>\n";
$entete .= "<script type=\"text/javascript\" src=\"jquery-113.js\"></script>\n";
$entete .= "<style>\n";
$entete .= "A {font-family:arial;font-size:11px;color:#24677A;text-decoration:none}\n".
           "A:link    {font-family:arial;font-size:11px;color:#24677A;font-weight:bold}\n".
           "A:visited {font-family:arial;font-size:11px;color:#24677A;font-weight:bold}\n".
           "A:hover   {font-family:arial;font-size:11px;color:#D45211;font-weight:bold}\n";
$entete .= ".bodi{width: 350px;height:auto;margin-right: 20px;top: 40px;left: 400px;position:absolute;background-color: #F6E7D4;}\n".
           ".bodi_over{color: #003333;background-color: #F6E7D4;cursor: pointer;border:1px solid #24677A;font-family: arial, tahoma,  serif;font-size: 12px;margin-right : 20px;padding: 6px;width: 350px;height:auto;top : 40px;left : 600px; position:absolute;}\n";
$entete .= ".myTree,\n.myTree ul{ list-style: none; margin-left : 20px;}\n.expandImage{margin-left: 30px;margin-right: 10px;}\n.treeItem{list-style: none; margin-left : 5px;}\n";
$entete .= "</style>";
$entete .= "</head><body>";
$entete .= "<H4 style=\"font-family: arial, tahoma,  serif;color: #003333;margin-left:40px;padding-top:15px;\">".$titre."</H4>\n";
$entete .='<div id="bodi" class="bodi" onclick="javascript:var monDiv=document.getElementById(\'bodi\');'.
          'monDiv.innerHTML=\'\';monDiv.className=\'bodi\';" title="Cliquez pour fermer"></div>';

require_once 'TreeForum.php';
$oTreeForum = new TreeForum(getItems());
$entete .= $oTreeForum->getResolvedTree();
$entete .= "<script type=\"text/javascript\">\$(document).ready(function(){tree = \$('#myTree');\$('li', tree.get(0)).each(function(){subbranch = \$('ul', this);if (subbranch.size() > 0) {if (subbranch.eq(0).css('display') == 'none') {\$(this).prepend('<img src=\"plus.gif\"  class=\"expandImage\" />');} else {\$(this).prepend('<img src=\"moins.gif\" class=\"expandImage\" />');}} else {\$(this).prepend('<img src=\"spacer.gif\" class=\"expandImage\" />');}});\$('img.expandImage', tree.get(0)).click(function(){if (this.src.indexOf('spacer') == -1) {subbranch = \$('ul', this.parentNode).eq(0);if (subbranch.css('display') == 'none') {subbranch.show();this.src = 'moins.gif';} else {subbranch.hide();this.src = 'plus.gif'; }}});});</script></body></html>";
$oTreeForum->display($oTreeForum->getResolvedTree());
if (!isset($downloader) || isset($downloader) && $downloader != 1){
  $date_en_cours = date("Y-n-d");
  $ch_date = explode("-",$date_en_cours);
  $date_save = "$ch_date[2]_$ch_date[1]_$ch_date[0]";
  $html = "forum"."_".str_replace(" ","_",$tit_forum)."_".$date_save.".html";
  $fp = fopen($html, "w+");
      $fw = fwrite($fp, $entete);
  fclose($fp);
  chmod($html,0775);
  $sortie = "forum"."_".str_replace(" ","_",$tit_forum)."_".$date_save.".zip";
  $dir_save = "../ressources/".$login."_".$id_user."/ressources/".$sortie;
  include_once("../class/archive.inc.php");
  if ($s_exp == "lx"){
     $mon_zip = new zip_file($dir_save);
  }else{
     $mon_zip = new zip_file($dir_save);
  }
  $mon_zip->add_files($html);
  $mon_zip->add_files("spacer.gif");
  $mon_zip->add_files("plus.gif");
  $mon_zip->add_files("moins.gif");
  $mon_zip->add_files("jquery-113.js");
  $mon_zip->create_archive();
  chmod($dir_save,0775);
  unlink($html);
  echo "<table><tr><form name='form1' action='fil_forum.php' method='POST' target='main'>\n";
  echo "<td nowrap><input type=hidden name='entete' value=\"$dir_save\"></td>\n";
  echo "<td nowrap><input type=hidden name='downloader' value='1'></td>\n";
  echo "<td nowrap style=\"padding-left:60px;\"><a href=\"javascript:document.form1.submit();\" class='bouton_new'>Faites une sauvegarde sur votre ordinateur</a>\n";
  echo "</td></form></tr></table>\n";
}

?>
<script type="text/javascript">
$(document).ready(
      function()
        {
                tree = $('#myTree');
                $('li', tree.get(0)).each(
                        function()
                        {
                                subbranch = $('ul', this);
                                if (subbranch.size() > 0) {
                                        if (subbranch.eq(0).css('display') == 'none') {
                                                $(this).prepend('<img src="../images/plus.gif"  class="expandImage" />');
                                        } else {
                                                $(this).prepend('<img src="../images/moins.gif" class="expandImage" />');
                                        }
                                } else {
                                        $(this).prepend('<img src="../scorm/spacer.gif" class="expandImage" />');
                                }
                        }
                );
                $('img.expandImage', tree.get(0)).click(
                        function()
                        {
                                if (this.src.indexOf('spacer') == -1) {
                                        subbranch = $('ul', this.parentNode).eq(0);
                                        if (subbranch.css('display') == 'none') {
                                                subbranch.show('slow');
                                                this.src = '../images/moins.gif';
                                        } else {
                                                subbranch.hide('slow');
                                                this.src = '../images/plus.gif';
                                        }
                                }
                        }
                );
        }
);
</script>
</body>
</html>