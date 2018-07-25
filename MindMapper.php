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
$contents = '';
/*
if (!empty($_SESSION['MindFormApp']) && $_SESSION['MindFormApp'] > 0)
{
    $id_grp = $_SESSION['numero_groupe'];
    $Ext = '_'.$id_grp;
    $reqForm = mysql_query("select * from prescription$Ext where presc_utilisateur_no=$utilisateur order by presc_ordre_no");
}
else
*/
$reqForm = mysql_query("select * from groupe,groupe_parcours,parcours where grp_cdn = $id_grp and ".
                       "gp_grp_no= $id_grp and gp_parc_no = parcours_cdn order by gp_ordre_no" );
$nbParc = mysql_num_rows($reqForm);
$i = 0;
$j = 0;
while ($itemGp = mysql_fetch_object($reqForm))
{
   if ($i == 0)
   {
       $contents .= '<map version="0.9.0">'."\n".
                    '  <node BACKGROUND_COLOR="#d9f26b" COLOR="#274D77" CREATED="'.time($itemGp->grp_datecreation_dt).'"'.
                    ' ID="Formation_'.$itemGp->grp_cdn.'" MODIFIED="'.time($itemGp->grp_datemodif_dt).'"'.
                    ' TEXT="'.NewHtmlentities(strip_tags(modif_az_qw(wordwrap($itemGp->grp_nom_lb,35,"\n"))),ENT_QUOTES).'">'."\n";
       $contents .= '   <richcontent TYPE="NOTE">'."\n".
                    '     <html><head/><body>'."\n".
                    '        <p align="left">Auteur : '.modif_nom(NomUser($itemGp->grp_resp_no)).'</p>'."\n".
                    '     </body></html>'."\n".
                    '   </richcontent>'."\n".
                    '   <edge STYLE="bezier" WIDTH="2"/>'."\n".
                    '   <font BOLD="true" NAME="SansSerif" SIZE="12"/>'."\n";
       $WkGrp = 10000 + $id_grp;
       $ReqWk = mysql_query("select * from wiki,wikiapp where wiki_seq_no = '$WkGrp' and wkapp_seq_no = '$WkGrp' group by wiki_cdn");
       $NbWk = mysql_num_rows($ReqWk);
       if ($NbWk > 0)
       {
           $contents .= '    <node COLOR="#006600" CREATED="'.time($itemGp->grp_datecreation_dt).'" FOLDED="true" HGAP="57" '.
                        ' ID="UtilGrp_'.$id_grp.'" MODIFIED="'.time($itemGp->grp_datemodif_dt).'" POSITION="right"'.
                        ' TEXT="Wikis : '.$NbWk.'" VSHIFT="-2">'."\n";
           $contents .= '     <edge COLOR="#00cc66" STYLE="bezier" WIDTH="2"/>'."\n".
                        '     <cloud COLOR="#fbee98"/>'."\n".
                        '     <font BOLD="true" NAME="SansSerif" SIZE="12"/>'."\n";
           while ($itemWk = mysql_fetch_object($ReqWk))
           {

               $auteurWk = modif_az_qw(NomUser($itemWk->wiki_auteur_no));
               $AutWk =(is_string($auteurWk)) ? $auteurWk: 'Auteur inconnu';
               $contents .= '     <node COLOR="#006600" CREATED="'.time($itemWk->wiki_create_dt).'" ID="Wiki_'.$itemWk->wiki_cdn.
                            '" MODIFIED="'.time($itemWk->wiki_create_dt).'" TEXT="'. NewHtmlentities(modif_az_qw(wordwrap(trim($itemWk->wiki_consigne_cmt),35,"\n")),ENT_QUOTES).'">'."\n".
                            '       <richcontent TYPE="NOTE">'."\n".
                            '          <html><head/><body>'."\n".
                            '              <p align="left">Auteur : '.$AutWk.'</p>'."\n".
                            '          </body></html>'."\n".
                            '       </richcontent>'."\n".
                            '       <edge COLOR="#00cc66" STYLE="bezier" WIDTH="2"/>'."\n".
                            '       <font BOLD="true" NAME="SansSerif" SIZE="10"/>'."\n".
                            '     </node>'."\n";
           }
           $contents .= '   </node>'."\n";
       }
   }
   $reqSP = mysql_query("select * from sequence_parcours,sequence where seqparc_parc_no = ".
                        $itemGp->gp_parc_no." and seqparc_seq_no = seq_cdn order by seqparc_ordre_no");
   $nbSP = mysql_num_rows($reqSP);
   while ($itemSP = mysql_fetch_object($reqSP))
   {
      if ($j == 0)
      {
          $contents .= '  <node BACKGROUND_COLOR="#BAB4B4" COLOR="#274D77" CREATED="'.str_replace('-','',$itemGp->parcours_create_dt).
                       '" ID="Module_'.$itemGp->parcours_cdn.'" MODIFIED="'.str_replace('-','',$itemGp->parcours_modif_dt).
                       '" STYLE="bubble" TEXT=" '.strip_tags( modif_az_qw(wordwrap(NewHtmlEntityDecode($itemGp->parcours_nom_lb,ENT_QUOTES),35,"\n"))).'">'."\n";
          $contents .= '    <richcontent TYPE="NOTE">'."\n".
                       '       <html><head/><body>'."\n".
                       '         <p align="left">Auteur : '.modif_az_qw(NomUser($itemGp->parcours_auteur_no))."\n\n".
                       'Description : '.strip_tags( modif_az_qw(NewHtmlEntityDecode($itemGp->parcours_desc_cmt,ENT_QUOTES)))."\n";
          $contents .= '         </p>'."\n".
                       '       </body></html>'."\n".
                       '    </richcontent>'."\n".
                       '    <edge STYLE="bezier" WIDTH="2"/>'."\n".
                       '    <font BOLD="true" NAME="SansSerif" SIZE="12"/>'."\n";

      }
      if (strstr($itemSP->seq_type_lb, 'NORMAL'))
      {
          $reqSeq = mysql_query("select * from activite where act_seq_no = ".$itemSP->seq_cdn." order by act_ordre_nb");
          $fold = (mysql_num_rows($reqSeq) > 3) ? ' FOLDED="true" ' : ' FOLDED="false" ';
      }
      elseif (strstr($itemSP->seq_type_lb, 'SCORM'))
      {
          $reqSeq = mysql_query("select * from scorm_module where mod_seq_no = ".$itemSP->seq_cdn." order by mod_ordre_no");
          $fold = (mysql_num_rows($reqSeq) > 3) ? 'FOLDED="true"' : 'FOLDED="false"';
      }
      $contents .= '     <node BACKGROUND_COLOR="#A8CAF0" COLOR="#274D77" '.$fold.' CREATED="'.str_replace('-','',$itemSP->seq_create_dt).
                   '" ID="Sequence_'.$itemSP->seq_cdn.'" MODIFIED="'.str_replace('-','',$itemSP->seq_modif_dt).
                   '" STYLE="bubble" TEXT=" '.strip_tags( modif_az_qw(wordwrap(NewHtmlEntityDecode($itemSP->seq_titre_lb,35,"\n"))),ENT_QUOTES).'">'."\n";
      $contents .= '        <richcontent TYPE="NOTE">'."\n".
                   '            <html><head/><body>'."\n".
                   '                <p align="left">Sequence :  Auteur : '.modif_az_qw(NomUser($itemSP->seq_auteur_no))."\n\n".
                   'Description : '.NewHtmlentities(strip_tags(NewHtmlEntityDecode(modif_az_qw($itemSP->seq_desc_cmt),ENT_QUOTES)),ENT_QUOTES)."\n";
      $contents .= '                </p>'."\n".
                   '             </body></html>'."\n".
                   '         </richcontent>'."\n".
                   '         <edge STYLE="bezier" WIDTH="2"/>'."\n".
                   '         <font BOLD="true" NAME="SansSerif" SIZE="12"/>'."\n";
      $ReqWk = mysql_query("select * from wiki,wikiapp where wiki_seq_no = '".$itemSP->seq_cdn." '
                             and wkapp_parc_no = '".$itemGp->gp_parc_no."' group by wiki_cdn");
      $NbWk = mysql_num_rows($ReqWk);
      if ($NbWk > 0)
      {
           $contents .= '         <node COLOR="#006600" CREATED="'.time($itemGp->grp_datecreation_dt).'" FOLDED="true" HGAP="57" '.
                        ' ID="UtilGrp_'.$id_grp.'" MODIFIED="'.time($itemGp->grp_datemodif_dt).'" POSITION="right"'.
                        ' TEXT="Wikis : '.$NbWk.'" VSHIFT="-2">'."\n";
           $contents .= '              <edge COLOR="#00cc66" STYLE="bezier" WIDTH="2"/>'."\n".
                        '              <cloud COLOR="#CDE9E9"/>'."\n".
                        '              <font BOLD="true" NAME="SansSerif" SIZE="12"/>'."\n";
           while ($itemWk = mysql_fetch_object($ReqWk))
           {

               $auteurWk = modif_az_qw(NomUser($itemWk->wiki_auteur_no));
               $AutWk =(is_string($auteurWk)) ? $auteurWk: 'Auteur inconnu';
               $contents .= '              <node COLOR="#006600" CREATED="'.time($itemWk->wiki_create_dt).'" ID="Wiki_'.$itemWk->wiki_cdn.
                            '" MODIFIED="'.time($itemWk->wiki_create_dt).'" TEXT="'. NewHtmlentities(modif_az_qw(wordwrap(trim($itemWk->wiki_consigne_cmt),35,"\n")),ENT_QUOTES).'">'."\n".
                            '                <richcontent TYPE="NOTE">'."\n".
                            '                   <html><head/><body>'."\n".
                            '                       <p align="left">Auteur : '.$AutWk.'</p>'."\n".
                            '                   </body></html>'."\n".
                            '                </richcontent>'."\n".
                            '                <edge COLOR="#00cc66" STYLE="bezier" WIDTH="2"/>'."\n".
                            '                <font BOLD="true" NAME="SansSerif" SIZE="10"/>'."\n".
                            '              </node>'."\n";
           }
           $contents .= '         </node>'."\n";
      }
      if (strstr($itemSP->seq_type_lb, 'NORMAL'))
      {
          while ($itemSeq = mysql_fetch_object($reqSeq))
          {
             $contents .= '       <node BACKGROUND_COLOR="#D5DDD5" COLOR="#274D77" CREATED="'.str_replace('-','',$itemSeq->act_create_dt).
                          '" ID="Activite_'.$itemSeq->act_cdn.'" MODIFIED="'.str_replace('-','',$itemSeq->act_modif_dt).
                          '" STYLE="bubble" TEXT=" '.NewHtmlentities(strip_tags(modif_az_qw(wordwrap(trim($itemSeq->act_nom_lb),35,"\n"))),ENT_QUOTES).'">'."\n";
             $contents .= '          <richcontent TYPE="NOTE">'."\n".
                          '              <html><head/><body>'."\n".
                          '                  <p align="left">Activite:  Auteur : '.modif_az_qw(NomUser($itemSeq->act_auteur_no))."\n\n".
                          'Consigne : '.NewHtmlentities(strip_tags(NewHtmlEntityDecode( modif_az_qw($itemSeq->act_consigne_cmt),ENT_QUOTES)),ENT_QUOTES)."\n";
             $contents .= '                  </p>'."\n".
                          '              </body></html>'."\n".
                          '          </richcontent>'."\n".
                          '          <edge STYLE="bezier" WIDTH="2"/>'."\n".
                          '          <font BOLD="true" NAME="SansSerif" SIZE="9"/>'."\n".
                          '       </node>'."\n";
          }
      }
      elseif (strstr($itemSP->seq_type_lb, 'SCORM'))
      {
          while ($itemSeq = mysql_fetch_object($reqSeq))
          {
             $contents .= '       <node BACKGROUND_COLOR="#D5DDD5" COLOR="#274D77" CREATED="" ID="Scorm_'.$itemSeq->mod_cdn.'" MODIFIED="" '.
                          'STYLE="bubble" TEXT="'. NewHtmlentities(strip_tags(modif_az_qw(wordwrap(trim($itemSeq->mod_titre_lb),35,"\n"))),ENT_QUOTES).'">'."\n";
             $contents .= '          <richcontent TYPE="NOTE">'."\n".
                          '              <html><head/><body>'."\n".
                          '                  <p align="left">Scorm:  Auteur : '.NomUser($itemSP->seq_auteur_no)."\n\n".
                          'Description : '.NewHtmlentities(strip_tags(NewHtmlEntityDecode(modif_az_qw(trim($itemSeq->mod_desc_cmt)),ENT_QUOTES)),ENT_QUOTES)."\n";
             $contents .= '                  </p>'."\n".
                          '              </body></html>'."\n".
                          '          </richcontent>'."\n".
                          '          <edge STYLE="bezier" WIDTH="2"/>'."\n".
                          '          <font BOLD="true" NAME="SansSerif" SIZE="9"/>'."\n".
                          '       </node>'."\n";
          }
      }
      $contents .= '     </node>'."\n";
      $j++;
   }
   $i++;
   $j = 0;
   $contents .= '   </node>'."\n";
   if ($i == ($nbParc))
       $contents .= '  </node>'."\n";
}
$contents .= '</map>';
//$contents = str_replace('’','&#039;',$contents);
$fp = fopen('MindMapping/formation.mm', "w+");
   $fw = fwrite($fp, $contents);
fclose($fp);
chmod ("MindMapping/formation.mm",0777);
$dateZip = time('Y-m-d hh:mm:ss');
if (isset($_GET['zip']) && $_GET['zip']==1)
{
    include_once("class/archive.inc.php");
    $dossier = "MindMapping";
    $zipper = new zip_file("SchemaFormation_$dateZip.zip");
    $zipper->set_options(array('basedir'=>$dossier));
    $handle=opendir($dossier);
    while ($fiche = readdir($handle))
    {
           if ($fiche != '.' && $fiche != '..')
               $zipper->add_files($fiche);
    }
    closedir($handle);
    $zipper->create_archive();
    $linker ="MindMapping/SchemaFormation_$dateZip.zip";
    ForceFileDownload($linker,'zip');
}
$lien="MindMapping/mindmaps.html";
echo "<script language=\"JavaScript\">";
echo "window.open(\"$lien\")";
echo "</script>";
$lien="gestion_groupe.php?cms=1&ordre_affiche=nom_groupe";
echo "<script language=\"JavaScript\">";
echo "document.location.replace('".$_SERVER['HTTP_REFERER']."')";
echo "</script>";
exit;
?>
