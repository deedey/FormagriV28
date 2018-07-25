<?php
session_start();
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
require "mindmapClass.php";
dbConnect();
$nom_user = $_SESSION["name_user"];
$prenom_user = $_SESSION["prename_user"];
$email_user = $_SESSION["email_user"];
include ("../style.inc.php");
if (!empty($_GET['numApp'])) $numApp = $_GET['numApp'];
if (!empty($_GET['id_seq'])) $id_seq = $_GET['id_seq'];
if (!empty($_GET['id_parc'])) $id_parc = $_GET['id_parc'];
if (!empty($_GET['numero_groupe'])) $numero_groupe = $_GET['numero_groupe'];
if (!empty($_GET['numeroApp'])) $numeroApp = $_GET['numeroApp'];
if (!empty($_GET['id_clan'])) $id_clan = $_GET['id_clan'];
//echo "<pre>";print_r($_SESSION);echo "</pre>";
if (isset($_GET['supp']) && $_GET['supp'] == 1)
{
  $IdSupp = GetDataField ($connect,"select mmapp_cdn from mindmapapp where mmapp_app_no='".$_GET['numeroApp']."' and
                           mmapp_seq_no='".$_GET['id_seq']."' and mmapp_parc_no='".$_GET['id_parc']."' and
                           mmapp_grp_no='".$_GET['numero_groupe']."' and mmapp_clan_nb='".$_GET['id_clan']."'","mmapp_cdn");
  $supprimer = mysql_query("delete from mindmapapp where mmapp_app_no='".$_GET['numeroApp']."' and
                           mmapp_seq_no='".$_GET['id_seq']."' and mmapp_parc_no='".$_GET['id_parc']."' and
                           mmapp_grp_no='".$_GET['numero_groupe']."' and mmapp_clan_nb='".$_GET['id_clan']."'");
  $supprimer = mysql_query("delete from mindmapnote where mmnote_app_no= '$IdSupp'");
  $mess_notif= "Vous venez de supprimer un travail en commun précédement affecté à ".NomUser($_GET['numeroApp']);
}
$reqMap=mysql_query("select * from mindmap where mindmap_seq_no = '$id_seq'");
$nbMap = mysql_num_rows($reqMap);
$content='';
if (!empty($mess_notif))
   echo notifier($mess_notif);
if  (isset($_GET['supp']) && $_GET['supp'] == 1 && isset($_GET['numeroApp']) && $_GET['numeroApp'] == $_GET['numApp'])
{
      echo "<script language=\"JavaScript\">";
          echo " setTimeout(function() {parent.location.reload();},4000)";
      echo "</script>";
}
if ($nbMap > 0)
{
  $nom_actuel = NomUser($_GET['numApp']);
  $reqMM=mysql_query("select * from mindmap,mindmapapp where mmapp_app_no='".$_GET['numApp']."' and
                      mmapp_seq_no='".$_GET['id_seq']."' and (mmapp_seq_no=mindmap_seq_no or mmapp_seq_no = '".$_GET['id_seq']."') and
                      mmapp_parc_no='".$_GET['id_parc']."' and mmapp_grp_no='".$_GET['numero_groupe'].
                      "' and mmapp_mindmap_no = mindmap_cdn");
  $nbMM = mysql_num_rows($reqMM);
  if($nbMM == 1)
     $content .= affectationMM(0);
  elseif($nbMM == 0)
  {
     $content .= affectationMM(1);
     //créer un nouveau groupe
     $reqMMExist=mysql_query("select * from mindmap where mindmap_seq_no='".$_GET['id_seq']."' order by mindmap_seq_no desc");
     $nbMMExt = mysql_num_rows($reqMMExist);
     $content .='</div>';
     $content.= DivCont();
     if ($nbMMExt > 0)
     {
         while ($oExist = mysql_fetch_object($reqMMExist))
         {
             $content.= newClan($_GET['numApp'],$nom_actuel,$oExist);
         }
     }
     $content .='</div>';
echo "<pre>";print_r($TabClan);echo "</pre>";
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
echo '<div id="mien" class="cms" style="margin-top:50px;" '.
     'title="Cliquer pour fermer cette alerte" onClick="javascript:$(this).empty();";></div>';
//fonctions

function ajtClan($numApp,$nom_actuel,$iClan,$oClan)
{
    GLOBAL $id_parc,$id_seq,$numero_groupe;
    $html = '<div id="ajt'.$iClan.'" style="clear:both;padding:4px;font-size:12px;font-family:arial,verdana;cursor:pointer;"
                                     onClick="javascript:$.ajax({type: \'GET\',
                                              url: \'mindmapLib.php\',
                                              data: \'numApp='.$numApp.'&ajtClan=1&id_mindmap='.$oClan->mmapp_mindmap_no.
                                                     '&id_seq='.$oClan->mmapp_seq_no.'&id_parc='.$id_parc.'&id_grp='.
                                                     $numero_groupe.'&id_clan='.$oClan->mmapp_clan_nb.'\',
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
                                                                     ' ce type de travail en groupe\');
                                                 }
                                                 else
                                                 {
                                                   $(\'#ajt'.$iClan.'\').empty();
                                                   $(\'#ajt'.$iClan.'\').html(\''.$nom_actuel.'\');
                                                   $(\'#ajt'.$iClan.'\').addClass(\'sequence\');
                                                   $(\'#mien\').empty();
                                                   $(\'#mien\').html(\' Vous avez affecté un travail de groupe sur une carte heuristique à '.$nom_actuel.'\');
                                                 }
                                                 $(\'#affiche\').empty();
                                                 setTimeout(function() {parent.location.reload();},2000);
                                              }
                                        });" '.
                                        'title="Ajoutez  <strong>'.$nom_actuel.'</strong> à ce groupe d\'apprenants">'.
                                        '<img src="images/tete.gif" border="0"> '.
                                        '<img src="images/icogod.gif" style="padding-bottom:4px;" border="0">'.
                                        '<img src="images/wikigrp.gif" border="0"></div>';
     $html .= '<div style="clear:both;text-decoration:underline;font-size:10px;">Membre(s) de ce groupe:'.$iClan.'</div>';
   return $html;
}
function newClan($numApp,$nom_actuel,$oExist)
{
    GLOBAL $id_parc,$id_seq,$numero_groupe;
    $html = '<div id="ajtNew" style="clear:both;float:left;padding:4px;background-color:#F1F5F5;margin-top:5px;'.
            'min-height:24px;font-size:12px;border:1px dotted #bbb;font-family:arial,verdana;cursor:pointer;width:98%;"
                                     onClick="javascript:$.ajax({type: \'GET\',
                                              url: \'mindmapLib.php\',
                                              data: \'numApp='.$numApp.'&ajtNewClan=1&id_mindmap='.$oExist->mindmap_cdn.
                                                     '&laSeq='.$id_seq.'&id_seq='.$oExist->mindmap_seq_no.
                                                     '&id_parc='.$id_parc.'&id_grp='.$numero_groupe.'\',
                                              beforeSend:function()
                                              {
                                                 $(\'#affiche\').addClass(\'Status\');
                                                 $(\'#affiche\').append(\'Opération en cours....\');
                                              },
                                              success: function(msg)
                                              {
                                                   $(\'#mien\').empty();
                                                   $(\'#mien\').html(\' Vous avez affecté un travail de groupe sur une carte heuristique à  '.$nom_actuel.'\');
                                                   $(\'#affiche\').empty();
                                                   setTimeout(function() {parent.location.reload();},2000);
                                              }
                                        });" '.
                                        'title ="Cliquez ici pour créer un nouveau groupe d\'apprenants avec \"'.$nom_actuel.'".
                                        "\" sur ce thème."><div style="clear:both;float:left;'.
                                        'background:url(../images/modules/wikigrp.gif) no-repeat top left;">'.
                                        '<div style="margin-left:35px;">'.clearTheme($oExist->mindmap_titre_lb).'</div></div></div>';
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
                     'Cliquez sur un des thèmes de carte heuristique à affecter à ce nouveau groupe d\'apprenants.</div>';
                     //'Les dates de début et de fin de prescription de cette séquence lui seront affectés par défaut et '.
                     //'vous aurez tout le loisir de les modifier plus tard..
   return $html;
}
function ThemeMM($oClan)
{
     GLOBAL $connect,$lg;
     $theme = GetDataField ($connect,"select mindmap_titre_lb from mindmap where mindmap_cdn='".$oClan->mmapp_mindmap_no."'","mindmap_titre_lb");
     $html = '<div style="clear:both;text-decoration:underline;font-size:10px;">';
     $html .= 'Thème traité par ce groupe:</div>'.
              '<div style="clear:both;border:1px dotted #000;font-size:11px;margin:4px;max-width:220px;max-height:60px;overflow-y:auto;">'.
              clearTheme($theme).'</div>';
   return $html;
}
function showFoto($numApp)
{
     GLOBAL $connect,$lg;
     $id_photo=GetDataField ($connect,"select util_photo_lb from utilisateur WHERE util_cdn = '$numApp'","util_photo_lb");
     if ($id_photo == '')
         $html = '';
     else
     {
         list($w_img, $h_img, $type_img, $attr_img) = getimagesize("../images/$id_photo");
         $html = " onMouseOver=\"overlib('', TEXTCOLOR, '#ffffff', TEXTSIZE, 2, WIDTH, $w_img, HEIGHT, ".
                 "$h_img, BACKGROUND, '../images/$id_photo', PADX, 60, 20, PADY, 20, 20,DELAY,500)\" onMouseOut=\"nd();\"";
     }
     return $html;
}
function suppApp($oClan)
{
   GLOBAL $connect,$lg,$numApp,$id_seq,$id_parc,$numero_groupe,$id_clan;
   $html = '<div style="float:left;margin:6px 4px 0 10px;cursor:pointer;width=20px;" '.
            'title="Enlever '.NomUser($oClan->mmapp_app_no).' de ce groupe. Pour l\'heure, '.
            'il n\'a encore aucune participation dans le document créé." '.
            'onClick=document.location.replace("mindAffect.php?supp=1&id_seq='.$id_seq.'&numApp='.$numApp.'&id_parc='.
            $id_parc.'&numero_groupe='.$numero_groupe.'&numeroApp='.$oClan->mmapp_app_no.'&id_clan='.$oClan->mmapp_clan_nb.'")>'.
            '<img src="../images/suppression1.gif" border="0"></div>';
   return $html;
}
function affectationMM($AjtMM)
{
     GLOBAL $connect,$lg,$numApp,$id_seq,$id_parc,$id_clan,$numero_groupe,$oClan,$nom_actuel;
     $MMMM = mysql_query("select * from mindmapapp where
                           mmapp_seq_no='$id_seq' and
                           mmapp_parc_no='$id_parc' and
                           mmapp_grp_no='$numero_groupe' order by mmapp_clan_nb asc");
     $nBrMM = mysql_num_rows($MMMM);
     $html = '';
     if ($nBrMM > 0)
     {
         $iClan = 0;
         $step = 0;
         $TabClan=array();
         $html .= '<div id="clan" style="clear:both;float:left;background-color: #eee;'.
                    'margin:-5px 2px 0 2px;padding-bottom:4px;max-height:450px;overflow-y:auto;">';
         $html .= '<div style="clear:both;border:1px solid #24677A;'.
                     'padding:2px 2px 0 2px;font-size:11px;font-family:arial,verdana;background-color: #D4E7ED;">'.
                     'Liste des apprenants auxquels a déjà été prescrite une carte heuristique pour cette séquence</div>';
         $html .= '<div id="clan'.$iClan.'" style="clear:both;float:left;border:1px dotted #bbb;'.
                     'background-color:#F1F5F5;margin:4px 10px 0 2px;font-family:arial,verdana,tahoma;">';
         while ($oClan = mysql_fetch_object($MMMM))
         {
             array_push($TabClan,$oClan);
             $nom_app = NomUser($oClan->mmapp_app_no);
             $nbEdit = mysql_num_rows(mysql_query("select * from mindmaphistory where mindhisto_auteur_no='".
                                                  $oClan->mmapp_app_no."' and mindhisto_map_no='".$oClan->mmapp_mindmap_no."'"));
             if ($iClan == 0)
             {
                 $html .= ThemeMM($oClan);
                 if ($AjtMM == 1)
                     $html .= ajtClan($numApp,$nom_actuel,$iClan,$oClan);
             }
             if (($iClan > 0  && $TabClan[$iClan]->mmapp_clan_nb != $TabClan[$iClan - 1]->mmapp_clan_nb))
             {
                 $step = 1;
                 if ($iClan == ($nBrMM-1) && $TabClan[$iClan]->mmapp_clan_nb != $TabClan[$iClan-1]->mmapp_clan_nb)
                 {
                    if (!empty($TabClan[$iClan-1]->mmapp_clan_nb))
                    {
                        $compt = $iClan-1;
                        $html .= '</div><div id="clan'.$iClan.'" style="float:left;border:1px dotted #bbb;'.
                                    'background-color:#F1F5F5;margin:4px 10px 0 2px;font-family:arial,verdana,tahoma;">';
                        $html .= ThemeMM($oClan);
                        if ($AjtMM == 1)
                            $html .= ajtClan($numApp,$nom_actuel,$iClan,$oClan);
                    }
                    $html .= '<div style="clear:both;float:left;"><div id="seqinv" style="clear:both;float:left;padding:2px 4px 0 4px;'.
                             'font-size:12px;font-family:arial,verdana,tahoma;cursor:default;" '.showFoto($oClan->mmapp_app_no).'>'.
                             $nom_app.'</div>';
                 }
             }
             if ($step == 1 && $iClan < ($nBrMM -1))
             {
                    $html .= '</div><div id="clan'.$iClan.'" style="float:left;border:1px dotted #bbb;'.
                                'background-color:#F1F5F5;margin:4px 10px 0 2px;font-family:arial,verdana,tahoma;">';
                    $html .= ThemeMM($oClan);
                    if ($AjtMM == 1)
                        $html .= ajtClan($numApp,$nom_actuel,$iClan,$oClan);
             }
             if (($iClan < ($nBrMM-1) && $step==1) || ($iClan < $nBrMM && $step == 0))
                 $html .= '<div style="clear:both;float:left;"><div id="seqinv" style="clear:both;float:left;padding:2px 4px 0 4px'.
                          ';cursor:default;font-size:12px;font-family:arial,verdana;" '.showFoto($oClan->mmapp_app_no).'>'.
                          $nom_app.'</div>';
             if ($nbEdit == 0 && $_SESSION['DroitsWiki'] == 1)
                    $html .= suppApp($oClan);
             $html .= '</div>';
             if ($iClan == ($nBrMM-1))
                 $html .= '</div>';
             $step = 0;
          $iClan++;
         }
     $html .='</div>';
     }
   if (!empty($html))
      return $html;
}
?>