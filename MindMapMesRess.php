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
   if (strtolower(strstr($fiche,'.mm')))
       unlink ('MindMapping/'.$fiche);
   if (strtolower(strstr($fiche,'.csv')))
       unlink ('MindMapping/'.$fiche);
   if (strtolower(strstr($fiche,'mesressources.html')))
       unlink ('MindMapping/'.$fiche);
}
closedir($handle);
$listeCat = array();
$content = '';$contentFile='';$contentFile1='';
$NbrRessTotale=0;$NbrRessMM=0;$catMM=0;
$content1 ='';$listeCatCsv='';
$ordre = (isset($_GET['ordre'])) ? ' desc' : '';
$JSon = (isset($_GET['json'])) ? $_GET['json'] : '';
$c1=',';  $c2='\n'; $c3='\n';
if (isset($_GET['json']))
   $ReqChilds = mysql_query("select * from ressource_new where ress_titre != '' group by ress_cat_lb order by ress_cat_lb");
else
   $ReqChilds = mysql_query("select * from ressource_new where (ress_url_lb like \"%ressources/%\" OR ress_url_lb like \"%qcm.php%\")".
                            " and ress_ajout= \"".$_SESSION['login']."\" group by ress_cat_lb order by ress_cat_lb".$ordre);
$nbr = mysql_num_rows($ReqChilds);
$i=0;
if ($nbr > 0)
{   $ComptCat = 0;
    $contentFile = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
                   <html>
                   <head>
                   <meta http-equiv="content-type" content="text/html; charset=ISO-8859-1">
                   <meta name="generator" content="'.$prenom_user.' '.$nom_user.' : '.$adresse_http.'">
                   <link rel="shortcut icon" type="image/x-icon" href="../favicon.ico" />
                   <link rel="stylesheet" type="text/css" href="mesressources.css" />
                   <title>Mes ressources</title>
                   </head>
                   <body>';
    $contentFile .= '<center><h3> Mes ressources sur '.$_SERVER['SERVER_NAME'].'</h3></center>';
    $contentFile .='<ul>';

    $content .= '<map version="0.9.0">'."\n";
    $content .= '  <node BACKGROUND_COLOR="#d9f26b" COLOR="#274D77" CREATED="" ID="Ressource_01" MODIFIED="" STYLE="bubble" '.
                'TEXT="  Mes ressources sur '.$_SERVER['SERVER_NAME'].'">'."\n";
    $content .= '    <richcontent TYPE="NOTE">'."\n".
                '       <html><head></head><body>'."\n".
                '         <p align="left"> Ressources contenues dans '.$nbr.' catégories (La carte ne peut afficher plus de 200 ressources)';
    $content .= '         </p>'."\n".
                '       </body></html>'."\n".
                '    </richcontent>'."\n".
                '    <edge COLOR="#00cc66"  STYLE="bezier" WIDTH="2"/>'."\n".
                '    <font BOLD="true" NAME="SansSerif" SIZE="14"/>'."\n";
    while ($ofils=mysql_fetch_object($ReqChilds))
    {
       $numer = $ofils->ress_cdn;
       $ComptCat++;
       if ($NbrRessTotale < 200)
       {
           $content .= '  <node BACKGROUND_COLOR="#FFC0C0" COLOR="#274D77" CREATED="" fold="" ID="ress_'.$ofils->ress_cdn.
                       '" MODIFIED="" STYLE="bubble" TEXT="      '.
                       str_replace('"','-',strip_tags( modif_az_qw(NewHtmlEntityDecode(str_replace('amp;','',$ofils->ress_cat_lb),ENT_QUOTES)))).'      ">'."\n";
           $content .= '    <edge COLOR="#00cc66"  STYLE="bezier" WIDTH="2"/>'."\n".
                   '    <font BOLD="true" NAME="SansSerif" SIZE="12"/>'."\n";
       }
       $content .= reverse($ofils->ress_cat_lb);
       if ($NbrRessTotale < 200)
          $content .= '</node>'."\n";
       $contentFile .='<li class="categorie">'.clean_text('<span class="cat">Catégorie </span> <span class="catxt">'.
                      str_replace('"','-',strip_tags( modif_az_qw(NewHtmlEntityDecode(str_replace('amp;','',$ofils->ress_cat_lb),ENT_QUOTES))))).'</span>';
       $contentFile .= clean_text($contentFile1);
      $i++;
    }
    $contentFile .= '</ul>';
    $content .= '</node>'."\n".'</map>';
    $folds = ($NbrRessTotale > 50 || (isset($_GET['zip']) && $_GET['zip'] == 1)) ? ' FOLDED="true" ' : ' FOLDED="false" ';
    $content = str_replace(' fold="" ', $folds ,$content);
    $content = str_replace("  Mes ressources sur ","  Mes $NbrRessMM / $NbrRessTotale ressources sur ",$content);
    $content = str_replace("Ressources contenues dans","Ressources contenues dans $catMM /",$content);
    $contentFile = str_replace("Mes ressources sur ","Mes $NbrRessMM / $NbrRessTotale ressources sur ",$contentFile);
    if (!empty($_GET['json']) && $JSon == 1)
    {
        $ressources = clean_text($listeCatCsv);
        $dateCsv = date('d-m-Y');
        $file ='MindMapping/ressources_'.$dateCsv.'.csv';
        //echo $listeCatCsv;
        //echo "<pre>";print_r($listeCat);echo "</pre>";exit;
        $fp = fopen($file, "w+");
           $fw = fwrite($fp,$ressources);
        fclose($fp);
        chmod ($file,0775);
        ForceFileDownload($file,'ascci');
        exit;
    }
}

if (isset($_GET['vueHtml']) && $_GET['vueHtml']==1)
{
  $fp = fopen('MindMapping/mesressources.html', "w+");
     $fw = fwrite($fp, $contentFile);
  fclose($fp);
  chmod ("MindMapping/mesressources.html",0775);
  $lien="MindMapping/mesressources.html";
  echo "<script language=\"JavaScript\">";
    echo "document.location.replace(\"$lien\")";
  echo "</script>";
  exit;
}

$fp = fopen('MindMapping/mesressources.mm', "w+");
   $fw = fwrite($fp, $content);
fclose($fp);
chmod ("MindMapping/mesressources.mm",0775);

$dateZip = date('d-m-Y');
if (isset($_GET['zip']) && $_GET['zip']==1)
{
    include_once("class/archive.inc.php");
    $dossier = "MindMapping";
    $zipper = new zip_file("mesress_$dateZip.zip");
    $zipper->set_options(array('basedir'=>$dossier));
    $handle=opendir($dossier);
    while ($fiche = readdir($handle))
    {
           if ($fiche != '.' && $fiche != '..')
               $zipper->add_files($fiche);
    }
    closedir($handle);
    $zipper->create_archive();
    $linker ="MindMapping/mesress_$dateZip.zip";
    ForceFileDownload($linker,'zip');
}
$lien="MindMapping/mindmesress.html";
echo "<script language=\"JavaScript\">";
    echo "window.open(\"$lien\")";
echo "</script>";
echo "<script language=\"JavaScript\">";
    echo "document.location.replace('".$_SERVER['HTTP_REFERER']."')";
echo "</script>";
exit;
function reverse($cat)
{
      GLOBAL $connect,$ajout,$NbrRessTotale,$NbrRessMM,$catMM,$contentFile1,$c1,$c2,$c3,
             $contentFile,$JSon,$adresse_http,$ComptCat,$listeCatCsv;
      if ($NbrRessTotale < 200)
         $catMM++;
      $content1 = '';
      $ComptRess=0;
      $contentFileSQM = '';
      if (!empty($JSon ))
         $ReqEnfants=mysql_query("select * from ressource_new where ress_cat_lb=\"".$cat."\" and ress_titre != ''");
      else
         $ReqEnfants=mysql_query("select * from ressource_new where ress_cat_lb=\"".$cat."\" AND
                              (ress_url_lb like \"%ressources/%\" OR ress_url_lb like \"%qcm.php%\") AND
                              ress_ajout = \"".$_SESSION['login']."\"");
      if (mysql_num_rows($ReqEnfants) > 0)
      {
         $ajout++;
         $ComptRess=0;
         $contentFile1 ='<ul>';
         if (empty($listeCatCsv))
            $listeCatCsv = "CATEGORIE,RESSOURCE,AUTEUR,URL,ACTIVITE,SEQUENCE,MODULE,FORMATION"."\n\n";
         while ($oEnfants=mysql_fetch_object($ReqEnfants))
         {
            $ComptRess++;
            $categorie = str_replace('"','-',str_replace(',',' ',strip_tags(NewHtmlEntityDecode(str_replace('amp;','',$cat),ENT_QUOTES))));
            $monCat = '"'.$categorie.'"'.$c1;
            $userRess = GetDataField ($connect,"select util_cdn from utilisateur where util_login_lb = '".$oEnfants->ress_ajout."'","util_cdn");
            $auteurRess = NomUser($userRess);
            $ressource = str_replace('"','-',str_replace(',',' ',strip_tags(NewHtmlEntityDecode(str_replace('amp;','',$oEnfants->ress_titre),ENT_QUOTES))));
            $monRess = '"'.$ressource.'"'.$c1.'"'.$auteurRess.'"'.$c1.'"'.$oEnfants->ress_url_lb.'"'.$c1;
            $NbrRessTotale++;
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
            $ReqActs = mysql_query("select * from activite,sequence where act_ress_no = ".
                                   $oEnfants->ress_cdn." and act_seq_no = seq_cdn order by act_nom_lb");
            $nbrActs = mysql_num_rows($ReqActs);
            if ($nbrActs > 0)
            {
               $ContentAct='';
               $ComptAct=0;
               $ComptSeq=0;
               $contentFileAct ='<ul>';
               if ($NbrRessTotale < 200)
                  $icone ='<icon BUILTIN="full-'.$nbrActs.'"/>';
               while ($oActs=mysql_fetch_object($ReqActs))
               {
                     $activite = str_replace('"','-',str_replace(',',' ',strip_tags( modif_az_qw(NewHtmlEntityDecode(str_replace('amp;','',$oActs->act_nom_lb),ENT_QUOTES)))));
                     $monAct = '"'.$activite.'"'.$c1;
                     if ($oActs->act_seq_no > 0 && $oActs->seq_titre_lb != '')
                     {
                         $sequence = str_replace('"','-',str_replace(',',' ',strip_tags(NewHtmlEntityDecode(str_replace('amp;','',$oActs->seq_titre_lb),ENT_QUOTES))));
                         $monSeq =  '"'.$sequence.'"'.$c1;
                     }else{
                         $monSeq =  '\n';
                        continue;
                     }
                     $ComptAct++;
                     $ComptSeq++;
                     $ReqSeqMod = mysql_query("select * from sequence_parcours,parcours where seqparc_seq_no='".
                                               $oActs->act_seq_no."' and seqparc_parc_no = parcours_cdn order by parcours_nom_lb");
                     $NbrSeqParcMod = mysql_num_rows($ReqSeqMod);
                     $contentFileSQM .= '<li class="sequence"><span class="seq"> Séquence </span>  '.str_replace('"','-',strip_tags(NewHtmlEntityDecode(str_replace('amp;','',$oActs->seq_titre_lb),ENT_QUOTES))).'</li>';
                     $contentFileSQM .= '<ul>';
                     if ($NbrSeqParcMod > 0)
                     {
                        if ($NbrRessTotale < 200)
                           $ContentSQM = '';
                        $j=0;
                        $ComptM=0;
                        $contentFileSQM = '<ul>';
                        if ($NbrRessTotale < 200)
                            $iconeSQM ='<icon BUILTIN="full-'.$NbrSeqParcMod.'"/>';
                        while ($oMods=mysql_fetch_object($ReqSeqMod))
                        {
                              $j++;
                              $ComptM++;
                              $module = str_replace('"','-',str_replace(',',' ',strip_tags(NewHtmlEntityDecode(str_replace('amp;','',$oMods->parcours_nom_lb),ENT_QUOTES))));
                              $monMod = '"'.$module.'"'.$c1;
                              $ReqModGrp = mysql_query("select * from groupe_parcours,groupe where gp_parc_no='".
                                                       $oMods->parcours_cdn."'and gp_grp_no = grp_cdn order by grp_nom_lb");
                              $NbModGrp = mysql_num_rows($ReqModGrp);
                              $iconeParc = ($NbModGrp > 0) ? '<icon BUILTIN="full-'.$NbModGrp.'"/>'."\n" : '';
                              $Prescrit = ($NbModGrp > 0) ? '<span class="prescription"> Prescrit</span>' : '<span class="prescription"> Non prescrit</span>';
                              if ($NbrRessTotale < 200)
                              {
                                 $ContentSQM .= '  <node BACKGROUND_COLOR="#FFFFC0" COLOR="#274D77" CREATED="" ID="ModId_'.$j.$oMods->parcours_cdn.
                                             '" MODIFIED="" STYLE="bubble" TEXT=" '.
                                             str_replace('"','-',strip_tags( modif_az_qw(NewHtmlEntityDecode(str_replace('amp;','',$oMods->parcours_nom_lb),ENT_QUOTES)))).'">'."\n";
                                 $ContentSQM .= '    <richcontent TYPE="NOTE">'."\n".
                                             '       <html><head></head><body>'."\n".
                                             '         <p align="left">Module créé par '.NomUser($oMods->parcours_auteur_no).' le '.reverse_date($oMods->parcours_create_dt,'-','-');
                                 $ContentSQM .= '         </p>'."\n".
                                             '       </body></html>'."\n".
                                             '    </richcontent>'."\n\n".$iconeParc;
                                 $ContentSQM .= '    <edge COLOR="'.$corde.'" STYLE="bezier" WIDTH="2"/>'."\n".
                                             '    <font BOLD="true" NAME="SansSerif" SIZE="12"/>'."\n";
                              }
                              if ($oActs->seq_cdn > 0)
                                 $contentFileSQM .= '<li class="sequence"><span class="seq"> Séquence </span>  '.str_replace('"','-',strip_tags(NewHtmlEntityDecode(str_replace('amp;','',$oActs->seq_titre_lb),ENT_QUOTES))).'</li>'.
                                                    '<li class="module"><span class="mod"> Module</span> '.str_replace('"','-',strip_tags(NewHtmlEntityDecode(str_replace('amp;','',$oMods->parcours_nom_lb),ENT_QUOTES))).
                                                    $Prescrit.'</li>';
                              if ($NbModGrp > 0)
                              {
                                 $k = 0;
                                 $ComptG=0;
                                 $contentFileSQM .= '<ul>';
                                 $iconeParc ='<icon BUILTIN="full-'.$NbModGrp.'"/>';
                                 while ($oGrp = mysql_fetch_object($ReqModGrp))
                                 {
                                     $ComptG++;
                                     $formation = str_replace('"','-',str_replace(',',' ',strip_tags(NewHtmlEntityDecode(str_replace('amp;','',$oGrp->grp_nom_lb),ENT_QUOTES))));
                                     $monGrp = '"'.$formation.'"'."\n";
                                     $listeCatCsv .= $monCat.$monRess.$monAct.$monSeq.$monMod.$monGrp;
                                     $k++;
                                     if ($NbrRessTotale < 200)
                                     {
                                        $ContentSQM .= '  <node BACKGROUND_COLOR="#FFd2C0" COLOR="#274D77" CREATED="" ID="ModId_'.$k.$oGrp->grp_cdn.
                                                    '" MODIFIED="" STYLE="bubble" TEXT=" '.
                                                    str_replace('"','-',strip_tags( modif_az_qw(NewHtmlEntityDecode(str_replace('amp;','',$oGrp->grp_nom_lb),ENT_QUOTES)))).'">'."\n";
                                        $ContentSQM .= '    <richcontent TYPE="NOTE">'."\n".
                                                    '       <html><head></head><body>'."\n".
                                                    '         <p align="left">Formation créée par '.NomUser($oGrp->grp_resp_no).
                                                    '  le '.reverse_date(substr($oGrp->grp_datecreation_dt,0,10),'-','-');
                                        $ContentSQM .= '         </p>'."\n".
                                                    '       </body></html>'."\n".
                                                    '    </richcontent>'."\n\n";
                                        $ContentSQM .= '    <edge COLOR="'.$corde.'" STYLE="bezier" WIDTH="2"/>'."\n".
                                                    '    <font BOLD="true" NAME="SansSerif" SIZE="12"/>'."\n";
                                        $ContentSQM .= '</node>'."\n";
                                     }
                                     $contentFileSQM .= '<li class="formation"><span class="form"> Formation</span> '.
                                                        str_replace('"','-',strip_tags(NewHtmlEntityDecode(str_replace('amp;','',$oGrp->grp_nom_lb),ENT_QUOTES))).
                                                        '</li>';
                                 }
                                 $contentFileSQM .= '</ul>';
                              }
                              else
                                 $listeCatCsv .= $monCat.$monRess.$monAct.$monSeq.$monMod."\n";

                              if ($NbrRessTotale < 200)
                                 $ContentSQM .= '</node>'."\n";
                              $contentFileSQM .= '</li>';
                        }
                        $contentFileSQM .='</ul>';
                     }
                     else
                     {
                         if ($NbrRessTotale < 200)
                            $ContentSQM = '';
                         $contentFileSQM = '';
                         $listeCatCsv .= $monCat.$monRess.$monAct.$monSeq."\n";
                         if ($NbrRessTotale < 200)
                            $iconeSQM ='<icon BUILTIN="full-'.$NbrSeqParcMod.'"/>';
                     }

                     $contentFileAct.='<li class="activite" title="Activité créée par '.NomUser($oActs->act_auteur_no).' le '.
                                       reverse_date($oActs->act_create_dt,'-','-'). ' dans la séquence : '.
                                       str_replace('"','-',strip_tags(NewHtmlEntityDecode($oActs->seq_titre_lb,ENT_QUOTES))).
                                       ' créée par  '.NomUser($oActs->seq_auteur_no).' le '.
                                       reverse_date($oActs->seq_create_dt,'-','-').'"><span class="act"> Activité</span> '.
                                       str_replace('"','-',strip_tags(NewHtmlEntityDecode(str_replace('amp;','',$oActs->act_nom_lb),ENT_QUOTES)));
                     $contentFileAct.= ($ContentSQM == '') ? '<span class="prescription"> Non intégrée</span>' : '<span class="prescription"> Intégrée</span>';
                     $contentFileAct.= ($ContentSQM == '') ? '<li class="sequence"><span class="seq" style="margin-left:40px;"> Séquence </span>  '.
                                                             str_replace('"','-',strip_tags(NewHtmlEntityDecode(str_replace('amp;','',$oActs->seq_titre_lb),ENT_QUOTES))).
                                                             '</li>' : '';
                     if ($NbrRessTotale < 200)
                     {
                         $ContentAct .= '  <node BACKGROUND_COLOR="#F4f4f4" COLOR="#274D77" CREATED="" ID="ActId_'.$oActs->act_cdn.
                                    '" MODIFIED="" STYLE="bubble" TEXT=" '.
                                    str_replace('"','-',strip_tags( modif_az_qw(NewHtmlEntityDecode(str_replace('amp;','',$oActs->act_nom_lb),ENT_QUOTES)))).'">'."\n";
                            $ContentAct .= '    <richcontent TYPE="NOTE">'."\n".
                                           '       <html><head></head><body>'."\n".
                                           '         <p align="left">Activité créée par '.NomUser($oActs->act_auteur_no).' le '.
                                           reverse_date($oActs->act_create_dt,'-','-');
                         if ($oActs->seq_cdn > 0)
                            $ContentAct .= ' dans la séquence : '.
                                       str_replace('"','-',strip_tags( modif_az_qw(NewHtmlEntityDecode($oActs->seq_titre_lb,ENT_QUOTES)))).
                                       ' créée par  '.NomUser($oActs->seq_auteur_no).' le '.
                                       reverse_date($oActs->seq_create_dt,'-','-'). "<br />";
                         $ContentAct .= '         </p>'."\n".
                                       '       </body></html>'."\n".
                                       '    </richcontent>'."\n  ".$iconeSQM."\n";
                         $ContentAct .= '    <edge COLOR="'.$corde.'" STYLE="bezier" WIDTH="2"/>'."\n".
                                    '    <font BOLD="true" NAME="SansSerif" SIZE="12"/>'."\n";
                         $ContentAct .= $ContentSQM.'</node>'."\n";
                     }
                     $contentFileAct .= $contentFileSQM;
               }
               $contentFileAct .='</ul>';
            }
            else
            {
               $ContentAct='';
               $contentFileAct='';
               $listeCatCsv .= $monCat.$monRess."\n";
               if ($NbrRessTotale < 200)
                  $icone ='<icon BUILTIN="full-'.$nbrActs.'"/>';
            }
            $fold = ($ContentAct != '' && $NbrRessTotale > 50) ? ' FOLDED="true" ' : ' FOLDED="false" ';
            $urlAct = (!strstr($oEnfants->ress_url_lb,'http')) ? $adresse_http."/".$oEnfants->ress_url_lb : $oEnfants->ress_url_lb;
            if ($NbrRessTotale < 200)
            {
               $NbrRessMM++;
               $content1 .= '  <node BACKGROUND_COLOR="'.$bgcolor.'" COLOR="#274D77" LINK="'.$urlAct.
                         '" CREATED="" '.$fold.' ID="RessId_'.$oEnfants->ress_cdn.
                         '" MODIFIED="" STYLE="bubble" TEXT=" '.
                         str_replace('"','-',strip_tags( modif_az_qw(NewHtmlEntityDecode(str_replace('amp;','',$oEnfants->ress_titre),ENT_QUOTES)))).'">'."\n";
               $content1 .= '    <richcontent TYPE="NOTE">'."\n".
                           '       <html><head></head><body>'."\n".
                           '         <p align="left">Ressource ajoutée par '.$auteurRess.' le '.
                                     reverse_date($oEnfants->ress_create_dt,'-','-').'<br />';
               $content1 .= '         </p>'."\n".
                           '       </body></html>'."\n".
                           '    </richcontent>'."\n   ".$icone."\n";
               $content1 .= '    <edge COLOR="'.$corde.'" STYLE="bezier" WIDTH="2"/>'."\n".
                         '    <font BOLD="true" NAME="SansSerif" SIZE="12"/>'."\n";
               $content1 .= $ContentAct.'</node>'."\n";
            }
            $contentFile1 .= '<li class="ressource"><span class="ress"> Ressource</span> '.
                             '<a href="'.$urlAct.'" target="_blank" title="Ressource ajoutée par '.$auteurRess.' le '.
                             reverse_date($oEnfants->ress_create_dt,'-','-').'. Cliquez pour ouvrir dans une autre fenêtre.">'.
                             str_replace('"','-',strip_tags(NewHtmlEntityDecode(str_replace('amp;','',$oEnfants->ress_titre),ENT_QUOTES))).
                             '</a>';
            $contentFile1 .= $contentFileAct.'</li>';
         }
         //$listeCatCsv .= $monCat.$monRess."\n";
         $contentFile1 .='</ul></li>';
         $ajout--;
      }
      if (!empty($content1))
          return $content1;
}
?>