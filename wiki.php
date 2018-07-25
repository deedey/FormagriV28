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
dbConnect();
$nom_user = $_SESSION["name_user"];
$prenom_user = $_SESSION["prename_user"];
$email_user = $_SESSION["email_user"];
include ("../style.inc.php");
$reqwiki=mysql_query("select * from wiki where wiki_seq_no = '$id_seq' or wiki_seq_no = '0'");
$nbWiki = mysql_num_rows($reqwiki);
$content='';
if ($nbWiki > 0)
{
  $reqWk=mysql_query("select * from wiki,wikiapp where wkapp_app_no='$numApp' and
                      wkapp_seq_no='$id_seq' and (wkapp_seq_no=wiki_seq_no or wkapp_seq_no = '$id_seq') and
                      wkapp_parc_no='$id_parc' and wkapp_grp_no='$numero_groupe' and wkapp_wiki_no = wiki_cdn");
  $nbWk = mysql_num_rows($reqWk);
  if ($nbWk == 1)
  {
     $oWiki = mysql_fetch_object($reqWk);
     $WkClan = mysql_query("select * from wikiapp where
                           wkapp_seq_no='$id_seq' and wkapp_parc_no='$id_parc' and
                           wkapp_grp_no='$numero_groupe' and wkapp_clan_nb='".$oWiki->wkapp_clan_nb."'");
     $nBClan = mysql_num_rows($WkClan);
     $iClan = 0;
     $TabClan=array();
     $content .= '<div id="clan'.$iClan.'" style="float:left;border:1px dotted #bbb;background-color:#eee;font-family:arial,verdana, tahoma;">';
     $nom_app = GetDataNP ($connect,"select util_nom_lb,util_prenom_lb from utilisateur where util_cdn = '".
                               $numApp."'","util_nom_lb,util_prenom_lb");
     $content .= '<div style="font-size:12px;padding:4px 4px 10px 4px;">'.
                 'Liste des apprenants du groupe de <span style="font-weight:bold;">'.$nom_app.'</span></div>';
     while ($oClan = mysql_fetch_object($WkClan))
     {
        array_push($TabClan,$oClan);
        $nom_app = GetDataNP ($connect,"select util_nom_lb,util_prenom_lb from utilisateur where util_cdn = '".
                               $oClan->wkapp_app_no."'","util_nom_lb,util_prenom_lb");
        if ($TabClan[$iClan]->wkapp_app_no != $numApp)
           $content .= '<div class="sequence" style="clear:both;padding:4px 4px 0 4px;font-size:12px;">'.
                        $nom_app.'</div>';
       $iClan++;
     }
     $theme = GetDataField ($connect,"select wiki_consigne_cmt from wiki where wiki_cdn='".$TabClan[$iClan-1]->wkapp_wiki_no."'","wiki_consigne_cmt");
     $content .= '<div style="clear:both;font-weight:bold;font-size:11px;padding:4px 0 0 4px;">Thème traité :</div>'.
              '<div style="clear:both;border:1px dotted #000;font-size:11px;margin:4px;max-width:250px;max-height:60px;overflow-y:auto;">'.
              clearTheme($theme).'</div></div>';
  }
  elseif($nbWk == 0)
  {
     //$content .= '<form id="21"><textarea name="ici"></textarea></form>';
     $nom_actuel = GetDataNP ($connect,"select util_nom_lb,util_prenom_lb from utilisateur where util_cdn = '".
                              $numApp."'","util_nom_lb,util_prenom_lb");
     $WkWk = mysql_query("select * from wikiapp where
                           wkapp_seq_no='$id_seq' and
                           wkapp_parc_no='$id_parc' and
                           wkapp_grp_no='$numero_groupe' order by wkapp_clan_nb asc");
     $nBrWk = mysql_num_rows($WkWk);
     if ($nBrWk > 0)
     {
         $iClan = 0;
         $step = 0;
         $TabClan=array();
         $content.= '<div id="clan" style="clear:both;float:left;background-color: #eee;'.
                    'margin:-5px 2px 0 2px;padding-bottom:4px;max-height:270px;overflow-y:auto;">';
         $content .= '<div style="clear:both;height:20px;border:1px solid #24677A;'.
                     'padding:2px 2px 0 2px;font-size:11px;font-family:arial,verdana;background-color: #D4E7ED;">'.
                     'Liste des apprenants auxquels a déjà été affecté un travail commun à faire pour cette séquence..</div>';
         $content .= '<div id="clan'.$iClan.'" style="clear:both;float:left;border:1px dotted #bbb;'.
                     'background-color:#F1F5F5;margin:4px 10px 0 2px;font-family:arial,verdana,tahoma;">';
         while ($oClan = mysql_fetch_object($WkWk))
         {
             array_push($TabClan,$oClan);
             $nom_app = GetDataNP ($connect,"select util_nom_lb,util_prenom_lb from utilisateur where util_cdn = '".
                               $oClan->wkapp_app_no."'","util_nom_lb,util_prenom_lb");
             if ($iClan == 0)
             {
                 $content .= ThemeWk($oClan);
                 $content .= ajtClan($numApp,$nom_actuel,$oClan);
             }
             if (($iClan > 0  && $TabClan[$iClan]->wkapp_clan_nb != $TabClan[$iClan - 1]->wkapp_clan_nb) || $iClan == ($nBrWk-1))
             {
                 $step = 1;
                 if ($iClan == ($nBrWk-1) && $TabClan[$iClan]->wkapp_clan_nb != $TabClan[$iClan-1]->wkapp_clan_nb)
                 {
                    if (!empty($TabClan[$iClan-1]->wkapp_clan_nb))
                    {
                        $compt = $iClan-1;
                        $content .= '</div><div id="clan'.$iClan.'" style="float:left;border:1px dotted #bbb;'.
                                    'background-color:#F1F5F5;margin:4px 10px 0 2px;font-family:arial,verdana,tahoma;">';
                        $content .= ThemeWk($oClan);
                        $content .= ajtClan($numApp,$nom_actuel,$oClan);
                    }
                    $content .= '<div id="seqinv" style="clear:both;float:left;padding:2px 4px 0 4px;font-size:12px;'.
                                'font-family:arial,verdana,tahoma;cursor:default;" '.showFoto($oClan->wkapp_app_no).'>'.
                                $nom_app.'</div>';
                    /*
                    $content .= '<div style="float:left;margin:6px 4px 0 10px;cursor:pointer;width=20px;" '.
                                'title="Enlever '.$nom_app.' de ce groupe" '.
                                'onClick=document.location.replace("wiki.php?supp=1&id_seq='.$id_seq.'&id_parc='.
                                $id_parc.'&numero_groupe='.$numero_groupe.'&numApp='.$numApp.'")>'.
                                '<img src="../images/suppression1.gif" border="0"></div>';
                    */

                 }
             }
             if ($step == 1 && $iClan < ($nBrWk -1))
             {
                    $content .= '</div><div id="clan'.$iClan.'" style="float:left;border:1px dotted #bbb;'.
                                'background-color:#F1F5F5;margin:4px 10px 0 2px;font-family:arial,verdana,tahoma;">';
                    $content .= ThemeWk($oClan);
                    $content .= ajtClan($numApp,$nom_actuel,$oClan);
                    $step = 0;
             }
             if ($iClan < ($nBrWk-1))
                 $content .= '<div id="seqinv" style="clear:both;float:left;padding:2px 4px 0 4px;cursor:default;'.
                             'font-size:12px;font-family:arial,verdana;" '.showFoto($oClan->wkapp_app_no).'>'.
                             $nom_app.'</div>';
                  /*
                    $content .= '<div style="float:left;margin:6px 4px 0 10px;cursor:pointer;width=20px;" '.
                                'title="Enlever '.$nom_app.' de ce groupe" '.
                                'onClick=document.location.replace("wiki.php?supp=1&id_seq='.$id_seq.'&id_parc='.
                                $id_parc.'&numero_groupe='.$numero_groupe.'&numApp='.$numApp.'")>'.
                                '<img src="../images/suppression1.gif" border="0"></div>';
                  */
             if ($iClan == ($nBrWk-1))
                 $content .= '</div>';
          $iClan++;
         }
     }
//echo "<pre>";print_r($TabClan);echo "</pre>";
     //créer un nouveau groupe
     $reqWkExist=mysql_query("select * from wiki where wiki_seq_no='$id_seq' or wiki_seq_no = '0' order by wiki_seq_no desc");
     $nbWkExt = mysql_num_rows($reqWkExist);
     $content .='</div>';
     $content.= DivCont();
     if ($nbWkExt > 0)
     {
         while ($oExist = mysql_fetch_object($reqWkExist))
         {
              if (!strstr($oExist->wiki_consigne_cmt,'-------'))
                  $content.= newClan($numApp,$nom_actuel,$oExist);
         }
     }
     $content .='</div>';
  }
  else
  {
      $content .=  "pas d'affectation";

  }
}
else
{
    $content .= "rien pour l'instant";
}
echo $content;
echo '<div id="affiche"></div>';
echo '<div id="mien" class="cms" title="Cliquer pour fermer cette alerte" onClick="javascript:$(this).empty();";></div>';
//fonctions

function ajtClan($numApp,$nom_actuel,$oClan)
{
    GLOBAL $id_parc,$id_seq,$numero_groupe,$iClan;
    $html = '<div id="ajt'.$iClan.'" style="clear:both;padding:4px;font-size:12px;font-family:arial,verdana;cursor:pointer;"
                                     onClick="javascript:$.ajax({type: \'GET\',
                                              url: \'wikilib.php\',
                                              data: \'numApp='.$numApp.'&ajtClan=1&id_wiki='.$oClan->wkapp_wiki_no.
                                                     '&id_seq='.$oClan->wkapp_seq_no.'&id_parc='.$id_parc.'&id_grp='.
                                                     $numero_groupe.'&id_clan='.$oClan->wkapp_clan_nb.'\',
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
                                              }
                                        });
                                        setTimeout(function() {$(\'#mien\').empty();},5000);" '.
                                        'title="Ajoutez  <strong>'.$nom_actuel.'</strong> à ce groupe d\'apprenants">'.
                                        '<img src="../images/modules/tete.gif" border="0"> '.
                                        '<img src="../images/ecran-annonce/icogod.gif" style="padding-bottom:4px;" border="0">'.
                                        '<img src="../images/modules/wikigrp.gif" border="0"></div>';
     $html .= '<div style="clear:both;text-decoration:underline;font-size:10px;">Membre(s) de ce groupe:</div>';
   return $html;
}
function newClan($numApp,$nom_actuel,$oExist)
{
    GLOBAL $id_parc,$id_seq,$numero_groupe;
    $html = '<div id="ajtNew" style="clear:both;float:left;padding:4px;background-color:#F1F5F5;margin-top:5px;'.
            'min-height:24px;font-size:12px;border:1px dotted #bbb;font-family:arial,verdana;cursor:pointer;width:98%;"
                                     onClick="javascript:$.ajax({type: \'GET\',
                                              url: \'wikilib.php\',
                                              data: \'numApp='.$numApp.'&ajtNewClan=1&id_wiki='.$oExist->wiki_cdn.
                                                     '&laSeq='.$id_seq.'&id_seq='.$oExist->wiki_seq_no.
                                                     '&id_parc='.$id_parc.'&id_grp='.$numero_groupe.'\',
                                              beforeSend:function()
                                              {
                                                 $(\'#affiche\').addClass(\'Status\');
                                                 $(\'#affiche\').append(\'Opération en cours....\');
                                              },
                                              success: function(msg)
                                              {
                                                   $(\'#mien\').empty();
                                                   $(\'#mien\').html(\' Vous avez affecté un travail de groupe à  '.$nom_actuel.'\');
                                                   $(\'#affiche\').empty();
                                              }
                                        });
                                        setTimeout(function() {document.location.reload();},2000);" '.
                                        'title ="Cliquez ici pour créer un nouveau groupe d\'apprenants avec <strong>'.$nom_actuel.'</strong>".
                                        " sur ce thème."><div style="clear:both;float:left;'.
                                        'background:url(../images/modules/wikigrp.gif) no-repeat top left;">'.
                                        '<div style="margin-left:35px;">'.clearTheme($oExist->wiki_consigne_cmt).'</div></div></div>';
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
                     'Cliquez sur un des thèmes de travail à rendre à affecter à ce nouveau groupe d\'apprenants. '.
                     'Les dates de début et de fin de prescription de cette séquence lui seront affectés par défaut et '.
                     'vous aurez tout le loisir de les modifier plus tard..</div>';
   return $html;
}
function ThemeWk($oClan)
{
     $theme = GetDataField ($connect,"select wiki_consigne_cmt from wiki where wiki_cdn='".$oClan->wkapp_wiki_no."'","wiki_consigne_cmt");
     $html .= '<div style="clear:both;text-decoration:underline;font-size:10px;">Thème traité par ce groupe:</div>'.
              '<div style="clear:both;border:1px dotted #000;font-size:11px;margin:4px;max-width:220px;max-height:60px;overflow-y:auto;">'.
              clearTheme($theme).'</div>';
   return $html;
}
function showFoto($numApp)
{
     $id_photo=GetDataField ($connect,"select util_photo_lb from utilisateur WHERE util_cdn = '$numApp'","util_photo_lb");
     if ($id_photo == '')
         $html = '';
     else
     {
         list($w_img, $h_img, $type_img, $attr_img) = getimagesize("../images/$id_photo");
         $html = " onMouseOver=\"overlib('', TEXTCOLOR, '#ffffff', TEXTSIZE, 2, WIDTH, $w_img, HEIGHT, $h_img, BACKGROUND, '../images/$id_photo', PADX, 60, 20, PADY, 20, 20,DELAY,500)\" onMouseOut=\"nd();\"";
     }
     return $html;
}
?>