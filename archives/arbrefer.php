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
<META HTTP-EQUIV="Pragma" CONTENT="no-cache">';

function getItems()
{
  GLOBAL $connect,$num;
  $req = "select * from referentiel where ref_parent_no='$num' GROUP BY ref_dom_lb,ref_nomabrege_lb order by ref_nomabrege_lb asc";
  $req_fil = mysql_query($req);
  if ($req_fil == TRUE)
  {
     $titre = GetDataField($connect,"select ref_nom_lb from referentiel WHERE ref_cdn=$num","ref_nom_lb");
     echo '<H4 style="font-family: arial, tahoma,  serif;color: #003333;">'.$titre.'</H4>';
     echo '<div id="bodi" class="bodi" onclick="javascript:var monDiv=document.getElementById(\'bodi\');'.
          'monDiv.innerHTML=\'\';monDiv.className=\'bodi\';" title="Cliquez pour fermer"></div>';
     $aItems=array();
     $cpt = 0;
     while ($item = mysql_fetch_object($req_fil))
     {
         $cpt++;
         $top = 40;//*$cpt;content.style.top='".$top."px';
         $obj = new stdClass();
         $obj->id = $item->ref_cdn;
         $obj->ref_denom_lb = $item->ref_denom_lb;
         $obj->auteur = $item->ref_auteur_lb;
         $obj->categorie = $item->ref_nomabrege_lb;
         $obj->description = $item->ref_desc_cmt;
         $obj->niveau = $item->ref_niv_no;
         $obj->subject = $item->ref_nom_lb;
         $obj->domaine = $item->ref_dom_lb;
         if ($obj->auteur != 'DGER' && $obj->auteur != 'dey')
         {
              $majuscule = GetDataField ($connect,"select util_nom_lb from utilisateur where util_login_lb = ".$obj->auteur,"util_nom_lb")." ";
              $majuscule .= GetDataField ($connect,"select util_prenom_lb from utilisateur where util_login_lb = ".$obj->auteur,"util_prenom_lb");
         }else
              $majuscule = 'Formagri';
         $affiche = "<strong>Auteur : ".$majuscule."<br />";
         $ma_liste = array("\t", "\n", "\r");
         $obj->body = trim(str_replace($ma_liste,"",nl2br(html_entity_decode($obj->description,ENT_QUOTES,'ISO-8859-1'))));
         $affiche .= $obj->body;
         $obj->subject = "<a href=\"#\" onMouseOver=\"javascript:var content= document.getElementById('bodi');\n".
                         "content.className='bodi_over';content.innerHTML='". htmlspecialchars(addslashes($affiche),ENT_QUOTES,'ISO-8859-1')."';\"\n".
                         " onClick=\"javascript:var content= document.getElementById('bodi');\n".
                         "content.className='bodi';content.innerHTML='';content.className='bodi_over';".
                         "content.innerHTML='". htmlspecialchars(addslashes($affiche),ENT_QUOTES,'ISO-8859-1')."';\">\n".
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
$entete .= "<H4 style=\"font-family: arial, tahoma,  serif;color: #003333;\">".$titre."</H4>\n";
$entete .='<div id="bodi" class="bodi" onclick="javascript:var monDiv=document.getElementById(\'bodi\');'.
          'monDiv.innerHTML=\'\';monDiv.className=\'bodi\';" title="Cliquez pour fermer"></div>';

require_once 'TreeForum.php';
$oTreeForum = new TreeForum(getItems());
$entete .= $oTreeForum->getResolvedTree();
$entete .= "<script type=\"text/javascript\">\$(document).ready(function(){tree = \$('#myTree');\$('li', tree.get(0)).each(function(){subbranch = \$('ul', this);if (subbranch.size() > 0) {if (subbranch.eq(0).css('display') == 'none') {\$(this).prepend('<img src=\"plus.gif\"  class=\"expandImage\" />');} else {\$(this).prepend('<img src=\"moins.gif\" class=\"expandImage\" />');}} else {\$(this).prepend('<img src=\"spacer.gif\" class=\"expandImage\" />');}});\$('img.expandImage', tree.get(0)).click(function(){if (this.src.indexOf('spacer') == -1) {subbranch = \$('ul', this.parentNode).eq(0);if (subbranch.css('display') == 'none') {subbranch.show();this.src = 'moins.gif';} else {subbranch.hide();this.src = 'plus.gif'; }}});});</script></body></html>";
if (!isset($downloader) || isset($downloader) && $downloader != 1){
  $oTreeForum->display($oTreeForum->getResolvedTree());
  $html = "ref"."_".str_replace(" ","_",$titre).".html";
  $fp = fopen($html, "w+");
      $fw = fwrite($fp, $entete);
  fclose($fp);
  chmod($html,0775);
  $sortie = "ref"."_".str_replace(" ","_",$titre).".zip";
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