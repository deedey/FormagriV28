<?php
if (!isset($_SESSION)) session_start();
include ("include/UrlParam2PhpVar.inc.php");
if (!isset($_SESSION['id_user']) || $_SESSION['id_user'] == "")
{
  exit();
}
require "admin.inc.php";
require 'fonction.inc.php';
require "lang$lg.inc.php";
require "fonction_html.inc.php";
dbConnect();
$nom_user = $_SESSION["name_user"];
$prenom_user = $_SESSION["prename_user"];
$email_user = $_SESSION["email_user"];
$handle=opendir('MindMapping');
while ($fiche = readdir($handle))
{
   if (strtolower(strstr($fiche,'.zip')))
       unlink ('MindMapping/'.$fiche);
}
closedir($handle);
$content = '';
$content1 ='';
$content .= '<map version="0.9.0">'."\n";
$content .= '  <node BACKGROUND_COLOR="#d9f26b" COLOR="#274D77" CREATED="" ID="Ressource_01" MODIFIED="" STYLE="bubble" '.
            'TEXT="  Organigramme des ressources  ">'."\n";
$content .= '    <richcontent TYPE="NOTE">'."\n".
            '       <html><head/><body>'."\n".
            '         <p align="left">Plate-forme : '.$bdd."\n";
$content .= '         </p>'."\n".
            '       </body></html>'."\n".
            '    </richcontent>'."\n".
            '    <edge COLOR="#00cc66"  STYLE="bezier" WIDTH="2"/>'."\n".
            '    <font BOLD="true" NAME="SansSerif" SIZE="14"/>'."\n";
$ReqChilds = mysql_query("select * from ressource_new where ress_typress_no = '0' and ".
                         "ress_url_lb='' group by ress_cat_lb order by ress_cat_lb");
$nbr = mysql_num_rows($ReqChilds);
$i=0;
if ($nbr > 0)
{
    while ($ofils=mysql_fetch_object($ReqChilds))
    {
       $numer = $ofils->ress_cdn;
       $content .= '  <node BACKGROUND_COLOR="#FFC0C0" COLOR="#274D77" CREATED="" ID="ress_'.$ofils->ress_cdn.
                       '" MODIFIED="" STYLE="bubble" TEXT="      '.
                       NewHtmlentities(strip_tags( modif_az_qw(wordwrap($ofils->ress_cat_lb,30,"\n"))),ENT_QUOTES).'      ">'."\n";
       if ($ofils->ress_desc_cmt != '')
       {
          $content .= '    <richcontent TYPE="NOTE">'."\n".
                       '       <html><head/><body>'."\n".
                       '         <p align="left">'.
                       NewHtmlentities(strip_tags(NewHtmlEntityDecode( modif_az_qw($ofils->ress_desc_cmt),ENT_QUOTES)),ENT_QUOTES)."\n";
          $content .= '         </p>'."\n".
                       '       </body></html>'."\n".
                       '    </richcontent>'."\n";
       }
       $content .= '    <edge COLOR="#00cc66"  STYLE="bezier" WIDTH="2"/>'."\n".
                   '    <font BOLD="true" NAME="SansSerif" SIZE="12"/>'."\n";
       $content .= reverse($ofils->ress_cdn).'</node>'."\n";
      $i++;
    }
    $content .= '</node>'."\n".'</map>';
}
$fp = fopen('MindMapping/ressources.mm', "w+");
   $fw = fwrite($fp, $content);
fclose($fp);
chmod ("MindMapping/ressources.mm",0775);
$dateZip = time('Y-m-d hh:mm:ss');
if (isset($_GET['zip']) && $_GET['zip']==1)
{
    include_once("class/archive.inc.php");
    $dossier = "MindMapping";
    $zipper = new zip_file("ress_$dateZip.zip");
    $zipper->set_options(array('basedir'=>$dossier));
    $handle=opendir($dossier);
    while ($fiche = readdir($handle))
    {
           if ($fiche != '.' && $fiche != '..')
               $zipper->add_files($fiche);
    }
    closedir($handle);
    $zipper->create_archive();
    $linker ="MindMapping/ress_$dateZip.zip";
    ForceFileDownload($linker,'zip');
}
$lien="MindMapping/mindress.html";
echo "<script language=\"JavaScript\">";
    echo "window.open(\"$lien\")";
echo "</script>";
echo "<script language=\"JavaScript\">";
    echo "document.location.replace('".$_SERVER['HTTP_REFERER']."')";
echo "</script>";
exit;
function reverse($num)
{
      GLOBAL $ajout;
      $content1 = '';
      $ReqEnfants=mysql_query("select * from ressource_new where ress_typress_no=".$num.
                              " and ress_url_lb='' group by ress_cat_lb order by ress_cat_lb");
      if (mysql_num_rows($ReqEnfants) > 0)
      {
         $ajout++;
         while ($oEnfants=mysql_fetch_object($ReqEnfants))
         {
            if ($ajout == 1) $bgcolor = '#C0FFFF';
            elseif($ajout == 2) $bgcolor = '#B8E6E5';
            elseif($ajout == 3) $bgcolor = '#C0FFC0';
            elseif($ajout == 4) $bgcolor = '#FFFFC0';
            elseif($ajout == 5) $bgcolor = '#C0C0FF';
            elseif($ajout == 6) $bgcolor = '#FFC0C0';
            elseif($ajout == 7) $bgcolor = '#FFC0FF';
            if ($ajout == 1) $corde = '#C00000';
            elseif($ajout == 2) $corde = '#C0C000';
            elseif($ajout == 3) $corde = '#00C000';
            elseif($ajout == 4) $corde = '#00C0C0';
            elseif($ajout == 5) $corde = '#0000C0';
            elseif($ajout == 6) $corde = '#C000C0';
            elseif($ajout == 7) $corde = '#510A52';
            $ReqPtis=mysql_query("select * from ressource_new where ress_typress_no=".$oEnfants->ress_cdn.
                                 " and ress_url_lb='' group by ress_cat_lb order by ress_cat_lb");
            $nbrC= mysql_num_rows($ReqPtis);
            $content1 .= '  <node BACKGROUND_COLOR="'.$bgcolor.'" COLOR="#274D77" CREATED="" ID="RessId_'.$oEnfants->ress_cdn.
                           '" MODIFIED="" STYLE="bubble" TEXT=" '.
                           strip_tags( modif_az_qw(NewHtmlEntityDecode(wordwrap($oEnfants->ress_cat_lb,30,"\n"),ENT_QUOTES))).'">'."\n";
            if ($oEnfants->ress_desc_cmt != '')
            {
               $content1 .= '    <richcontent TYPE="NOTE">'."\n".
                           '       <html><head/><body>'."\n".
                           '         <p align="left">'.
                           strip_tags( modif_az_qw(NewHtmlEntityDecode($oEnfants->ress_desc_cmt,ENT_QUOTES)))."\n";
               $content1 .= '         </p>'."\n".
                           '       </body></html>'."\n".
                           '    </richcontent>'."\n";
            }
            $content1 .= '    <edge COLOR="'.$corde.'" STYLE="bezier" WIDTH="2"/>'."\n".
                         '    <font BOLD="true" NAME="SansSerif" SIZE="12"/>'."\n";
            if ($nbrC > 0)
            {
                  $ajout++;
                  while ($optis=mysql_fetch_object($ReqPtis))
                  {
                        if ($ajout == 1) $bgcolor = '#C0FFFF';
                        elseif($ajout == 2) $bgcolor = '#B8E6E5';
                        elseif($ajout == 3) $bgcolor = '#C0FFC0';
                        elseif($ajout == 4) $bgcolor = '#FFFFC0';
                        elseif($ajout == 5) $bgcolor = '#C0C0FF';
                        elseif($ajout == 6) $bgcolor = '#FFC0C0';
                        elseif($ajout == 7) $bgcolor = '#FFC0FF';
                        if ($ajout == 1) $corde = '#C00000';
                        elseif($ajout == 2) $corde = '#C0C000';
                        elseif($ajout == 3) $corde = '#00C000';
                        elseif($ajout == 4) $corde = '#00C0C0';
                        elseif($ajout == 5) $corde = '#0000C0';
                        elseif($ajout == 6) $corde = '#C000C0';
                        elseif($ajout == 7) $corde = '#510A52';
                        $content1 .= '  <node BACKGROUND_COLOR="'.$bgcolor.'" COLOR="#274D77" CREATED="" ID="RessId_'.$optis->ress_cdn.
                                     '" MODIFIED="" STYLE="bubble" TEXT=" '.
                                     strip_tags( modif_az_qw(NewHtmlEntityDecode(wordwrap($optis->ress_cat_lb,30,"\n"),ENT_QUOTES))).'">'."\n";
                        if ($optis->ress_desc_cmt != '')
                        {
                            $content1 .= '    <richcontent TYPE="NOTE">'."\n".
                                     '       <html><head/><body>'."\n".
                                     '         <p align="left">'.
                                     strip_tags(modif_az_qw(NewHtmlEntityDecode($optis->ress_desc_cmt,ENT_QUOTES)))."\n";
                            $content1 .= '         </p>'."\n".
                                     '       </body></html>'."\n".
                                     '    </richcontent>'."\n";
                        }
                        $content1 .= '    <edge COLOR="'.$corde.'" STYLE="bezier" WIDTH="2"/>'."\n".
                                     '    <font BOLD="true" NAME="SansSerif" SIZE="12"/>'."\n";
                        $content1 .= reverse($optis->ress_cdn).'</node>'."\n";
                  }
                  $ajout--;
                  $content1 .= '</node>'."\n";
            }
            else
            $content1 .='</node>'."\n";
         }
         $ajout--;
      }
      if (!empty($content1))
          return $content1;
}
?>
