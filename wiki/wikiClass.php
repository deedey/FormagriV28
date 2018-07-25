<?php
function compareWk($iBody,$oBody)
{
  GLOBAL $connect,$lg,$numApp,$id_seq,$id_parc,$id_grp,$id_wk,$id_clan;
  $AllBody = mysql_query("select * from wikibodies where wkbody_clan_no=".$oBody->wkbody_clan_no."
                           and (wkbody_show_on=1 or(wkbody_show_on=0 and wkbody_auteur_no=".$_SESSION['id_user']."))
                           order by wkbody_order_no,wkbody_date_dt desc");
  $cBody = 0;$TabcBody = array();
  $html = '';
  while ($aBody = mysql_fetch_object($AllBody))
  {
    array_push($TabcBody,$aBody);
    $cBody++;
  }
  $reqBefore = mysql_query("select * from wikibodies where wkbody_clan_no=".$oBody->wkbody_clan_no." and
                            (wkbody_show_on=1 or(wkbody_show_on=0 and wkbody_auteur_no=".$_SESSION['id_user'].")) and
                            wkbody_order_no=".$oBody->wkbody_order_no." and
                            wkbody_date_dt < ".$oBody->wkbody_date_dt." order by wkbody_date_dt desc");
  $reqAfter = mysql_query("select * from wikibodies where wkbody_clan_no=".$oBody->wkbody_clan_no." and
                            wkbody_order_no=".$oBody->wkbody_order_no." and
                            wkbody_date_dt > ".$oBody->wkbody_date_dt." order by wkbody_date_dt asc");
  if (mysql_num_rows($reqBefore) > 0 || mysql_num_rows($reqAfter) > 0)
      $html = '<span title="Cliquez sur les flèches pour ouvrir ou refermer les historiques" '.
              'style="cursor:help;padding-left:12px;">Comparer<br /></span>';

  if (mysql_num_rows($reqBefore) > 0)
  {
     //if ($oBefore = mysql_fetch_object($reqBefore) > 0)
        $html .= CompGauche(CompareWkMoins($oBody),300,$oBody->wkbody_order_no);
  }
  if (mysql_num_rows($reqAfter) > 0)
  {
     //if ($oAfter = mysql_fetch_object($reqAfter) > 0)
        $html .= CompDroite(CompareWkPlus($oBody),300,$oBody->wkbody_order_no);
  }
  if ($html != '')
     return $html;
}
function CompareWkMoins($oBody)
{
   GLOBAL $connect,$lg;
  $OrdreMoinsBody = mysql_query("select * from wikibodies where wkbody_clan_no=".$oBody->wkbody_clan_no." and
                               (wkbody_show_on=1 or(wkbody_show_on=0 and wkbody_auteur_no=".$_SESSION['id_user'].")) and
                               wkbody_order_no=".$oBody->wkbody_order_no." and
                               wkbody_date_dt < ".$oBody->wkbody_date_dt." order by wkbody_date_dt desc");
  $NbLstMoins = mysql_num_rows($OrdreMoinsBody);
  $listMoins = '';
  $NbMns=0;
  if ($NbLstMoins > 0)
  {
     $listMoins .='<ul id="ulCompG'.$oBody->wkbody_order_no.'" class="ulComp" style="display:none;">';
     $listMoins .='<li style="background:url(images/titreTable.jpg);color:#fff;height:25px;font-size:12px;'.
                  'padding:8px 0 4px 8px;">Comparer avec les instances précédentes</li>';

     while ($oMoins = mysql_fetch_object($OrdreMoinsBody))
     {
            $listMoins .= '<li style="padding-left:4px;"><a href="wikiDiffText.php?IdCdn='.$oMoins->wkbody_cdn.'&actuId='.$oBody->wkbody_cdn.
                         '&IdDt='.$oMoins->wkbody_date_dt.'&actuDt='.$oBody->wkbody_date_dt.
                         '&IdUser='.$oMoins->wkbody_auteur_no.'&actuUser='.$oBody->wkbody_auteur_no.
                         '&keepThis=true&TB_iframe=true&height=425&width=650" '.
                         'class="thickbox" title="Comparer le contenu affiché avec cette instance du paragraphe" '.
                         'name="Comparaison des deux contenus.">'.
                         date('d-m-Y à H\h i\'',$oMoins->wkbody_date_dt).' par '.NomUser($oMoins->wkbody_auteur_no).'</a></li>';
          $NbMns++;
      }
  }
  $listMoins .='</ul>';
  return $listMoins;
}
function CompareWkPlus($oBody)
{
  GLOBAL $connect,$lg;
  $OrdrePlusBody = mysql_query("select * from wikibodies where wkbody_clan_no=".$oBody->wkbody_clan_no." and
                               (wkbody_show_on=1 or(wkbody_show_on=0 and wkbody_auteur_no=".$_SESSION['id_user'].")) and
                               wkbody_order_no=".$oBody->wkbody_order_no." and
                               wkbody_date_dt > ".$oBody->wkbody_date_dt." order by wkbody_date_dt asc");
  $NbLstPlus = mysql_num_rows($OrdrePlusBody);
  $listPlus = '';
  $NbPls=0;
  if ($NbLstPlus > 0)
  {
     $listPlus .='<ul id="ulCompD'.$oBody->wkbody_order_no.'" class="ulComp" style="display:none;">';
     $listPlus .='<li style="background:url(images/titreTable.jpg);color:#fff;height:25px;font-size:12px;'.
                 'padding:8px 0 4px 8px;">Comparer avec les instances suivantes</li>';

      while ($oPlus = mysql_fetch_object($OrdrePlusBody))
      {
            $listPlus .= '<li style="padding-left:2px;"><a href="wikiDiffText.php?IdCdn='.$oPlus->wkbody_cdn.'&actuId='.$oBody->wkbody_cdn.
                         '&IdDt='.$oPlus->wkbody_date_dt.'&actuDt='.$oBody->wkbody_date_dt.
                         '&IdUser='.$oPlus->wkbody_auteur_no.'&actuUser='.$oBody->wkbody_auteur_no.
                         '&keepThis=true&TB_iframe=true&height=425&width=650" '.
                         'class="thickbox" title="Comparer le contenu affiché avec cette instance du paragraphe" '.
                         'name="Comparaison des deux contenus.">'.
                         date('d-m-Y à H\h i\'',$oPlus->wkbody_date_dt).' par '.NomUser($oPlus->wkbody_auteur_no).'</a></li>';
          $NbPls++;
      }
  }
  $listPlus .='</ul>';
  return $listPlus;
}
function HwWk($iBody,$oBody)
{
  GLOBAL $connect,$lg,$numApp,$id_seq,$id_parc,$id_grp,$id_wk,$id_clan;
  $AllBody = mysql_query("select * from wikibodies where wkbody_clan_no=".$oBody->wkbody_clan_no."
                           and (wkbody_show_on=1 or(wkbody_show_on=0 and wkbody_auteur_no=".$_SESSION['id_user']."))
                           order by wkbody_order_no,wkbody_date_dt desc");
  $cBody = 0;$TabcBody = array();
  $html = '';
  while ($aBody = mysql_fetch_object($AllBody))
  {
    array_push($TabcBody,$aBody);
    $cBody++;
  }
  $reqBefore = mysql_query("select * from wikibodies where wkbody_clan_no=".$oBody->wkbody_clan_no." and
                            (wkbody_show_on=1 or(wkbody_show_on=0 and wkbody_auteur_no=".$_SESSION['id_user'].")) and
                            wkbody_order_no=".$oBody->wkbody_order_no." and
                            wkbody_date_dt < ".$oBody->wkbody_date_dt." order by wkbody_date_dt desc");
  $reqAfter = mysql_query("select * from wikibodies where wkbody_clan_no=".$oBody->wkbody_clan_no." and
                            wkbody_order_no=".$oBody->wkbody_order_no." and
                            wkbody_date_dt > ".$oBody->wkbody_date_dt." order by wkbody_date_dt asc");
  if (mysql_num_rows($reqBefore) > 0 || mysql_num_rows($reqAfter) > 0)
      $html = '<span title="Cliquez sur les flèches pour ouvrir ou refermer les historiques" '.
              'style="cursor:help;padding-left:12px;">Naviguez dans l\'historique de ce paragraphe<br /></span>';

  if (mysql_num_rows($reqBefore) > 0)
  {
     //if ($oBefore = mysql_fetch_object($reqBefore) > 0)
        $html .= HwGauche(HwWkMoins($oBody),300,$oBody->wkbody_order_no);
  }
  $html .= '<a href="javascript:void(0);" title="Le paragraphe courant a été validé le <br />'.
           date('d-m-Y à H\h i\'',$oBody->wkbody_date_dt).' par '.NomUser($oBody->wkbody_auteur_no).'">'.
           '<img src="images/centre.gif" border="0" style="margin:0 0 1px 0;"></a>';
  if (mysql_num_rows($reqAfter) > 0)
  {
     //if ($oAfter = mysql_fetch_object($reqAfter) > 0)
        $html .= HwDroite(HwWkPlus($oBody),300,$oBody->wkbody_order_no);
  }
  if ($html != '')
     return $html;
}
function HwWkMoins($oBody)
{
  GLOBAL $connect,$lg,$numApp,$id_seq,$id_parc,$id_grp,$id_wk,$id_clan;
  $OrdreMoinsBody = mysql_query("select * from wikibodies where wkbody_clan_no=".$oBody->wkbody_clan_no." and
                               (wkbody_show_on=1 or(wkbody_show_on=0 and wkbody_auteur_no=".$_SESSION['id_user'].")) and
                               wkbody_order_no=".$oBody->wkbody_order_no." and
                               wkbody_date_dt < ".$oBody->wkbody_date_dt." order by wkbody_date_dt desc");
  $NbLstMoins = mysql_num_rows($OrdreMoinsBody);
  $listMoins = '';
  $NbMns=0;
  if ($NbLstMoins > 0)
  {
     $listMoins .='<ul id="ulHwG'.$oBody->wkbody_order_no.'" class="ulComp" style="display:none;">';
     $listMoins .='<li style="background:url(images/titreTable.jpg);color:#fff;height:25px;font-size:12px;'.
                  'padding:8px 0 4px 8px;">Naviguer dans les instances précédentes</li>';

     while ($oMoins = mysql_fetch_object($OrdreMoinsBody))
     {
            $listMoins .= '<li style="padding-left:4px;"><a href="wikiOpen.php?IdOrdre='.
              $oMoins->wkbody_order_no.'&IdCdn='.$oMoins->wkbody_cdn.'&numApp='.$numApp.
              '&id_seq='.$id_seq.'&id_parc='.$id_parc.'&id_grp='.$id_grp.'&id_clan='.$id_clan.'&id_wk='.$id_wk.'">'.
              date('d-m-Y à H\h i\'',$oMoins->wkbody_date_dt).' par '.NomUser($oMoins->wkbody_auteur_no).'</a></li>';
          $NbMns++;
      }
  }
  $listMoins .='</ul>';
  return $listMoins;
}
function HwWkPlus($oBody)
{
  GLOBAL $connect,$lg,$numApp,$id_seq,$id_parc,$id_grp,$id_wk,$id_clan;
  $OrdrePlusBody = mysql_query("select * from wikibodies where wkbody_clan_no=".$oBody->wkbody_clan_no." and
                               (wkbody_show_on=1 or(wkbody_show_on=0 and wkbody_auteur_no=".$_SESSION['id_user'].")) and
                               wkbody_order_no=".$oBody->wkbody_order_no." and
                               wkbody_date_dt > ".$oBody->wkbody_date_dt." order by wkbody_date_dt asc");
  $NbLstPlus = mysql_num_rows($OrdrePlusBody);
  $listPlus = '';
  $NbPls=0;
  if ($NbLstPlus > 0)
  {
     $listPlus .='<ul id="ulHwD'.$oBody->wkbody_order_no.'" class="ulComp" style="display:none;">';
     $listPlus .='<li style="background:url(images/titreTable.jpg);color:#fff;height:25px;'.
                 'font-size:12px;padding:8px 0 4px 8px;">Naviguer dans les instances suivantes</li>';

      while ($oPlus = mysql_fetch_object($OrdrePlusBody))
      {
            $listPlus .= '<li style="padding-left:2px;"><a href="wikiOpen.php?IdOrdre='.$oPlus->wkbody_order_no.'&IdCdn='.
              $oPlus->wkbody_cdn.'&numApp='.$numApp.'&id_seq='.$id_seq.'&id_parc='.$id_parc.
              '&id_grp='.$id_grp.'&id_clan='.$id_clan.'&id_wk='.$id_wk.'">'.
              date('d-m-Y à H\h i\'',$oPlus->wkbody_date_dt).' par '.NomUser($oPlus->wkbody_auteur_no).'</a></li>';
          $NbPls++;
      }
  }
  $listPlus .='</ul>';
  return $listPlus;
}
function listWk($idClan)
{
  GLOBAL $connect,$lg,$numApp,$id_seq,$id_parc,$id_grp,$id_wk,$id_clan;
  $OrdreBody = mysql_query("select * from wikibodies where wkbody_clan_no=".$idClan.
                           " and (wkbody_show_on=1 or(wkbody_show_on=0 and wkbody_auteur_no=".$_SESSION['id_user']."))
                           order by wkbody_order_no,wkbody_date_dt desc");
  $NbLst = mysql_num_rows($OrdreBody);
  $lst = '';
  $NbOrd=0;
  $Nblst=0;
  $orderLst=array();
  if ($NbLst > 0)
  {
      while ($oLst = mysql_fetch_object($OrdreBody))
      {
            array_push($orderLst,$oLst);
            if ($Nblst == 0 || $orderLst[$Nblst-1]->wkbody_order_no != $orderLst[$Nblst]->wkbody_order_no)
            {
               $enieme = ($Nblst == 0) ? 'er' : 'ème' ;
               $NbOrd ++;
               $lst .= '<li class="historyLi" style="margin:0 0 5px 40px;width:150px;"><span>'.$NbOrd.$enieme.' paragraphe</span></li>';
            }
            $lst .= '<li><a href="javascript:void(0);" onClick="javascript:document.location.replace(\'wikiOpen.php?ChxDte='.
                     $oLst->wkbody_date_dt.'&numApp='.$numApp.'&id_seq='.$id_seq.'&id_parc='.
                     $id_parc.'&id_grp='.$id_grp.'&id_clan='.$id_clan.'&id_wk='.$id_wk.'\');">'.
                    date('d-m-Y à H\h i\'',$oLst->wkbody_date_dt).' par '.NomUser($oLst->wkbody_auteur_no).'</a></li>';
          $Nblst++;
      }
  }
  return $lst;
}
function AjtElm($iBody,$oBody,$position)
{
    GLOBAL $connect,$lg,$numApp,$id_seq,$id_parc,$id_grp,$id_wk,$id_clan;
    $html  = '<div id="ajt10000'.$iBody.'" class="ajtWk">';
    $html .= '<div id="ajtPrg10000'.$iBody.'" class="ajtPrgWk">';
    $html .= '<a href="javascript:void(0);" onClick="javascript:$(\'#leBodyP10000'.$iBody.'\').toggle();'.
             '$(\'#newBTitreP10000'.$iBody.'\').focus();$(\'#ajtImg10000'.$iBody.'\').toggle();" '.
             'title="Insérer un nouveau paragraphe ici.">'.
             '<img src="images/prgAjt.gif" border="0"></a>';
    $html .= InsertPrg($iBody,$oBody,$position);
    $html .= '</div>';
    $html .= '<div id="ajtImg10000'.$iBody.'" class="ajtImgWk">';
    $html .= '<a href="javascript:void(0);" onClick="javascript:$(\'#leBodyI10000'.$iBody.'\').toggle();'.
             '$(\'#newBTitreI10000'.$iBody.'\').focus();" title="Insérer une image avec son commentaire ici.">'.
             '<img src="images/img.gif" border="0"></a>';
    $html .= InsertImg($iBody,$oBody,$position);
    $html .= '</div></div>';
  return $html;

}

function InsertPrg($iBody,$oBody,$position)
{
  GLOBAL $connect,$lg,$numApp,$id_seq,$id_parc,$id_grp,$id_wk,$id_clan;
  if ($position == 1)
  {
       $newOrdWk = Donne_ID ($connect,"select max(wkbody_order_no) from wikibodies where wkbody_clan_no=".$_GET['id_clan']);
  }
  elseif ($position == 0)
  {
       $newOrdWk = $oBody->wkbody_order_no;
  }
  $html = '<div id="leBodyP10000'.$iBody.'" class="leBodyAjt">';
  $html .= '<form id="monformP'.$iBody.'" name="monformP'.$iBody.'" '.
           'action="wikiOpen.php?numApp='.$numApp.'&id_seq='.$id_seq.'&id_parc='.
           $id_parc.'&id_grp='.$id_grp.'&id_clan='.$id_clan.'" method="POST">';
  $html .= '<div id="loadPrgP10000'.$iBody.'" class="loadTitImg">';
  $html .= '<input type="hidden" name="ajtNewBodyImg" value="1" />';
  $html .= '<INPUT type="hidden" name="position" value="'.$position.'" />';
  $html .= '<INPUT type="hidden" name="userfile" value="0" />';
  $html .= '<input type="hidden" name="newOrdWk" value="'.$newOrdWk.'" />';
  $html .= '<input type="hidden" name="IdBody" value="'.$oBody->wkbody_cdn.'" />';
  $html .= '<div id="WkBTitreP10000'.$iBody.'" class="WkBTitre">';
  $html .= '<span style="font-size:12px;">Titre du paragraphe </span>';
  $html .= '<input id="newBTitreP10000'.$iBody.'" name="titreImg" class="INPUT" size="80" value="" /></div></div>';
  $html .= '<div style="font-size:12px;clear:both;padding:0 4px 0 4px;">Votre nouveau paragraphe</div>';
  $html .= '<div class="newBody">';
  $html .= '<textarea id="newBodyP10000'.$iBody.'" name="legende" class="textarea"></textarea></div>';
  $html .= '<div class="Bodycone">'.
           '<div class="BodyLink">'.
           '<a href="javascript:checkForm(document.monformP'.$iBody.');" '.
           'onClick="TinyMCE.prototype.triggerSave();" title="Valider cette insertion">'.
           '<img src="images/boutvalid.gif" border="0"></a></div>'.
           '<div id="noImgModP10000'.$iBody.'" class="noImgMod" '.
           'onClick="javascript:$(\'#leBodyP10000'.$iBody.'\').toggle();'.
           '$(\'leBodyP10000'.$iBody.'\').toggle();$(\'#ajtImg10000'.$iBody.'\').toggle();" title="Renoncer à cette insertion.">'.
           '<img src="images/retour15.gif" border="0"></div></div>';
  $html .= '</form></div>';
  return $html;

}

function InsertImg($iBody,$oBody,$position)
{
  GLOBAL $connect,$lg,$numApp,$id_seq,$id_parc,$id_grp,$id_wk,$id_clan,$bouton_gauche,$bouton_droite;
  if ($position == 1)
  {
       $newOrdWk = Donne_ID ($connect,"select max(wkbody_order_no) from wikibodies where wkbody_clan_no=".$_GET['id_clan']);
  }
  elseif ($position == 0)
  {
       $newOrdWk = $oBody->wkbody_order_no;
  }
  $html = '<div id="leBodyI10000'.$iBody.'" class="leBodyAjt">';
  $html .= '<form enctype="multipart/form-data" id="monformI'.$iBody.'" name="monformI'.$iBody.'" '.
           'action="wikiOpen.php?numApp='.$numApp.'&id_seq='.$id_seq.'&id_parc='.
           $id_parc.'&id_grp='.$id_grp.'&id_clan='.$id_clan.'" method="POST">';
  $html .= '<div id="loadImgI10000'.$iBody.'" class="loadTitImg" style="display:block;">';
  $html .= '<div id="loadTitreI10000'.$iBody.'" class="InpImg">'.
           '<span style="font-size:12px;float:left;padding:3px 4px 0 4px;">Charger une image</span>';
  $NbImGal = mysql_num_rows(mysql_query("select * from wikimg"));
  $html .= '</div>';
  $html .= '<div id="InpImgI10000'.$iBody.'" class="InpImg">'.
           '<input type="file" id="fichierI10000'.$iBody.'" class="InpImg" name="userfile" '.
           'onClick="javascript:$(\'#fileI10000'.$iBody.'\').empty();'.
           '$(\'#ZeroI10000'.$iBody.'\').empty();'.
           '$(\'#ImageI10000'.$iBody.'\').empty();" '.
           'title="Ajouter ou modifier une image ne dépassant pas la taille autorisée de 100 Ko aux formats JPEG,JPG,GIF ou PNG" />'.
           '</div></div>';
  if ($NbImGal > 0)
  {
      $html .= '<div style="font-size:12px;float:right top;">'.
               $bouton_gauche.'<a href="wikiGalerie.php?idImage=ImageI10000'.$iBody.
               '&fichier=fichierI10000'.$iBody.'&Zero=ZeroI10000'.$iBody.'&idFile=fileI10000'.$iBody.
               '&insert=1&keepThis=true&TB_iframe=true&height=500&width=620" onClick="$(\'input[id=fichierI10000'.$iBody.']\').empty();"'.
               ' class="thickbox" title="Choisissez une image pour cette insertion" name="Images de la galerie">'.
               'Choisissez une image dans la galerie </a>'.$bouton_droite.'</div>';
  }
  $html .= '<div id="ImageI10000'.$iBody.'"></div>';
  $html .= '<div id="fileI10000'.$iBody.'"></div>';
  $html .= '<div id="ZeroI10000'.$iBody.'"></div>';
  $html .= '<input type="hidden" name="ajtNewBodyImg" value="1" />';
  $html .= '<INPUT type="hidden" name="position" value="'.$position.'" />';
  $html .= '<INPUT type="hidden" name="max_img" value="110000" />';
  $html .= '<input type="hidden" name="newOrdWk" value="'.$newOrdWk.'" />';
  $html .= '<input type="hidden" name="IdBody" value="'.$oBody->wkbody_cdn.'" />';
  $html .= '<div id="WkBTitreI10000'.$iBody.'" class="WkBTitre">';
  $html .= '<span style="font-size:12px;">Titre de l\'image </span>';
  $html .= '<input id="newBTitreI10000'.$iBody.'" name="titreImg" class="INPUT" size="80" value="" /></div>';
  $html .= '<div style="font-size:12px;clear:both;padding:0 4px 0 4px;">Insérez une légende</div>';
  $html .= '<div class="newBody">';
  $html .= '<textarea id="newBodyI10000'.$iBody.'" name="legende" class="textarea"></textarea></div>';
  $html .= '<div class="Bodycone">'.
           '<div class="BodyLink">'.
           '<a href="javascript:checkForm(document.monformI'.$iBody.');" '.
           'onClick="TinyMCE.prototype.triggerSave();" title="Valider cette insertion">'.
           '<img src="images/boutvalid.gif" border="0"></a></div>'.
           '<div id="noImgModI10000'.$iBody.'" class="noImgMod" '.
           'onClick="javascript:$(\'#leBodyI10000'.$iBody.'\').toggle();'.
           '$(\'leBodyI10000'.$iBody.'\').toggle();" title="Renoncer à cette insertion.">'.
           '<img src="images/retour15.gif" border="0"></div></div>';
  $html .= '</form></div>';
  return $html;

}
function vuePlan($iBody,$oBody,$cpt,$typeNum)
{
  GLOBAL $connect,$lg,$numApp,$id_seq,$id_parc,$id_grp,$id_wk,$id_clan;
       $ReqComment = mysql_query("select * from commentaires,utilisateur where comwk_body_no=".$oBody->wkbody_cdn.
                                        " and utilisateur.util_cdn = com_auteur_no order by com_date_dt");
  $NbComment = mysql_num_rows($ReqComment);
  $numComt = 0;
  $html = '<div id="planTitre'.$cpt.'" style="clear:both;margin:0 4px 5px 10px;font-size:13px;'.
           'font-family:tahoma,verdana,arial;cursor:pointer;width:auto;" '.
          'onClick="javascript:$(\'#BlkBody'.$cpt.'\').toggle();" title="Cliquez pour ouvrir/fermer">'.
          '<div id="TypeNum'.$cpt.'" style="clear:both;float:left;font-weight:bold;width:25px;text-decoration:none;padding-right:5px;">'.
           numerotation($typeNum,$cpt).'</div><div style="float:left;text-decoration:underline;">'.
           stripslashes($oBody->wkbody_titre_lb).'</div>';
  $html .= '</div>';
  $listeComment = '';
  while ($itemComment = mysql_fetch_object($ReqComment))
  {
      $numComt++;
      $dateComment = reverse_date(substr($itemComment->com_date_dt,0,10),'-','/');
      $listeComment .= '<div id="'.$numComt.'leComment'.$cpt.'" class="leComment">'.
                       '<div id="'.$numComt.'enteteComment'.$cpt.'" class="enteteComment"> Commentaire ajouté par : '.
                       NomUser($itemComment->com_auteur_no).' le '.$dateComment.'</div>';
      $listeComment .= '<div id="'.$numComt.'corpsComment'.$cpt.'" class="corpsComment">'.
                       html_entity_decode($itemComment->com_comment_cmt,ENT_QUOTES,'ISO-8859-1').'</div></div>';
  }
  if ($NbComment > 0)
  {
     $html .= '<div id="commentBlg'.$cpt.'" class="commentBlg" style="float:left;" onClick="javascript:$(\'#affComment'.$cpt.'\').toggle();">'.
              '<span title="Commentaires">'.$NbComment.'</span></div>';
     $html .= '</div>';
  }
  $html .= '<div id="BlkBody'.$cpt.'" class="BlkBody">';
  if ($oBody->wkbody_img_no > 0)
  {
     $html .= '<div id="ImgShow'.$cpt.'" '.
              'style="clear:both;float:left;width:auto;border:1px solid #24677A;margin:0 4px 5px 20px;padding:10px;">';
     $html .= '<img src="lib/affiche_image.php?provenance=paragraphe&numImg='.$oBody->wkbody_img_no.'">';
     $html .= '</div>';
     $html .= '<div id="legende'.$cpt.'" style="clear:both;font-size:12px;font-weight:bold;padding:4px 0 0 20px;">'.
              ' Légende :</div>';
  }
  $html .= '<div id="planBody'.$cpt.'" style="clear:both;margin:0 4px 5px 20px;font-size:12px;"> ';
  $html .= html_entity_decode($oBody->wkbody_body_cmt,ENT_QUOTES,'ISO-8859-1');
  if ($NbComment > 0)
     $html .= '<div id="affComment'.$cpt.'" class="affComment">'.$listeComment.'</div>';
  $html .= '</div>';
  $html .= '<div id="AjtComment'.$cpt.'" class="AjtComment" onClick="javascript:$(\'#BlkComment'.$cpt.'\').toggle();" '.
           'title="Cliquez pour saisir votre commentaire">Rédigez un commentaire</div>';
  if (rated($oBody->wkbody_cdn) > 0)
  {
        $html .= '<div style="float:right;margin-top:-20px;"><input type="hidden" value='.
                           rateIt($oBody->wkbody_cdn).' step=.5 readonly=true id="backing'.$iBody.'">';
        $debutTitle = ($oBody->wkbody_auteur_no != $_SESSION['id_user']) ? 'Vous avez donné la note de '.myrated($oBody->wkbody_cdn).'/5 à cet article <br /> ' : '';
        $html .= '<div id="rateit'.$iBody.'" title="'.$debutTitle;
        $html .= 'Moyenne sur '.totalrated($oBody->wkbody_cdn).' notes :'.rateIt($oBody->wkbody_cdn).' / 5">';
        $html .= '</div></div>';
        ?>
        <script type ="text/javascript">
                    $(document).ready(function() {
                          $('#rateit<?php echo $iBody;?>').rateit({ max: 5, step: 1, backingfld: '#backing<?php echo $iBody;?>'});
                    });
        </script>
        <?php
  }
  elseif(rated($oBody->wkbody_cdn) == 0 && totalrated($oBody->wkbody_cdn) > 0 && $oBody->wkbody_auteur_no == $_SESSION['id_user'])
  {
        $html .= '<div style="float:right;"><input type="hidden" value='.
                           rateIt($oBody->wkbody_cdn).' step=.5 readonly=true id="backing'.$iBody.'">';
        $html .= '<div id="rateit'.$iBody.'" title="';
        $html .= 'Moyenne sur '.totalrated($oBody->wkbody_cdn).' notes :'.rateIt($oBody->wkbody_cdn).' / 5">';
        $html .= '</div></div>';
        ?>
        <script type ="text/javascript">
                    $(document).ready(function() {
                          $('#rateit<?php echo $iBody;?>').rateit({ max: 5, step: 1, backingfld: '#backing<?php echo $iBody;?>'});
                    });
        </script>
        <?php
  }
  elseif(rated($oBody->wkbody_cdn) == 0 && $oBody->wkbody_auteur_no != $_SESSION['id_user'])
  {
        $html .= '<div style="float:right;margin-top:-20px;"><input type="hidden" value='.
                            rateIt($oBody->wkbody_cdn).' step=1  id="backing'.$iBody.'">';
        $html .= '<div id="rateit'.$iBody.'" title="Cliquez sur le sens '.
                            'interdit pour réinitialiser puis sur une étoile pour attribuer votre note.';
        if (totalrated($oBody->wkbody_cdn) > 0)
            $html .= '<br /> Moyenne sur '.totalrated($oBody->wkbody_cdn).' notes :'.rateIt($oBody->wkbody_cdn).' / 5">';
        else
            $html .= '">';
        $html .= '</div></div>';
        ?>
        <script type ="text/javascript">
             $(document).ready(function() {
                      $('#rateit<?php echo $iBody;?>').rateit({ max: 5, step: 1, backingfld: '#backing<?php echo $iBody;?>'});
             });
             $(document).ready(function() {
                    $('#rateit<?php echo $iBody;?>').bind('rated', function (e) {
                    $(this).rateit('readonly',true);
                    $(this).attr('title', 'Vous lui avez attribué la note de '+ $('#backing<?php echo $iBody;?>').val()+'/ 5');
                    $.ajax({
                                url: 'wikilib.php',
                                data: { value: $('#backing<?php echo $iBody;?>').val(),IdBody : <?php echo $oBody->wkbody_cdn;?> },
                                type: 'POST',
                                beforeSend:function()
                                {
                                    $('#affiche').addClass('Status');
                                    $('#affiche').append('Opération en cours....');
                                },
                                success: function (data) {
                                    $('#affiche').empty();$('#mien').empty();
                                    $('#mien').html('Vous venez d\'attribuer '+$('#backing<?php echo $iBody;?>').val()+'/5 à ce paragraphe.');
                                    $('#mien').show();setTimeout(function() {$('#mien').empty();},7000);
                                }
                            });
                      });
             });
        </script>
        <?php
  }

  $html .= '<div id="BlkComment'.$cpt.'" class="BlkComment" >'.
           addComment($oBody->wkbody_cdn,$numApp,$cpt,1);
  $html .= '</div>';  return $html;
}
function addComment($numBody,$numApp,$cpt,$voirPlan)
{
  GLOBAL $connect,$lg,$id_seq,$id_parc,$id_grp,$id_wk,$id_clan;
  $plan = ($voirPlan == 1) ? "&vuePlan=1" : '';
  $html = '<div id="Cmt">';
  $html  .= '<form id="monformC'.$cpt.'" name="monformC'.$cpt.'" '.
           'action="wikiOpen.php?numBody='.$numBody.'&numApp='.$numApp.'&id_seq='.$id_seq.'&id_parc='.
           $id_parc.'&id_grp='.$id_grp.'&id_clan='.$id_clan.$plan.'" method="POST">';
  $html .= '<textarea id="newCmt'.$cpt.'" name="newCmt" class="textarea"></textarea>';
  $html .= '<div class="BodyLink">'.
           '<a href="javascript:checkFormC(document.monformC'.$cpt.');" '.
           'onClick="TinyMCE.prototype.triggerSave();" title="Valider ce commentaire">'.
           '<img src="images/boutvalid.gif" border="0"></a></div>';
  $html .= '</form>';
  $html .= '</div>';
  return $html;
}

function envoiMailCmt($numBody,$versQui,$Quoi,$titre,$ContentCmt)
{
        GLOBAL $connect;
        $date_messagerie = date("Y/m/d H:i:s");
        $MailCmt = GetDataField ($connect, "select util_email_lb from utilisateur where util_cdn=$versQui","util_email_lb");
        $envoyeur = NomUser($_SESSION['id_user']);
        $entree = 'Un autre commentaire pour votre article ( '.stripslashes($titre).' ) dans le '.$Quoi;
        $etat_MailCmt = GetDataField($connect,"select param_etat_lb from param_foad where param_typ_lb='mailcomment'","param_etat_lb");
        if ($MailCmt != '' && $etat_MailCmt == 'OUI')
             $envoi=mail_attachement($MailCmt , html_entity_decode($entree,ENT_QUOTES,'ISO-8859-1'), html_entity_decode( $entree,ENT_QUOTES,'ISO-8859-1').' '.
                                     " vient d'être rédigé par " .$envoyeur."   :<br />".html_entity_decode($ContentCmt,ENT_QUOTES,'ISO-8859-1'),'none' ,
                                      $_SESSION['email_user'],'', $_SESSION['email_user']);
        $message_base = "Un commentaire <BR>$mess_ag_cordial";
        $max_numero = Donne_ID ($connect,"select max(mess_cdn) from messagerie");
        $requete = mysql_query("INSERT INTO messagerie (mess_cdn,envoyeur,origine,contenu,date,sujet,id_user) VALUES
                               ($max_numero,".$_SESSION['id_user'].",\"".html_entity_decode($entree,ENT_QUOTES,'ISO-8859-1')."\",\"".
                               html_entity_decode("Le commentaire suivant :<br />$ContentCmt <br /> ".
                               "vient d'être rédigé par $envoyeur <br />Cordialement",ENT_QUOTES,'ISO-8859-1')."\",
                               '$date_messagerie','$Quoi',$versQui)");
}

function envoiMailRate($numBody,$Notation,$Quoi,$titre,$versQui)
{
        GLOBAL $connect;
        $date_messagerie = date("d/m/Y H:i:s");
        $MailCmt = GetDataField ($connect, "select util_email_lb from utilisateur where util_cdn=$versQui","util_email_lb");
        $envoyeur = NomUser($_SESSION['id_user']);
        $entree = 'Une de vos insertions dans le '.$Quoi.' a une notation.. sur Formagri' ;
        $suiteEntree = 'Votre article ou paragraphe ( '.stripslashes($titre).' ) a reçu la note de '.$Notation.'/5 aujourd\'hui ('.$date_messagerie.') par '.$envoyeur;
        $etat_MailCmt = GetDataField($connect,"select param_etat_lb from param_foad where param_typ_lb='mailcomment'","param_etat_lb");
        if ($MailCmt != '' && $etat_MailCmt == 'OUI')
             $envoi=mail_attachement($MailCmt , html_entity_decode($entree,ENT_QUOTES,'ISO-8859-1'), html_entity_decode( $suiteEntree,ENT_QUOTES,'ISO-8859-1').' '.
                                     "<br /> Cordialement<br />".$adresse_http,'none' ,
                                      $_SESSION['email_user'],'', $_SESSION['email_user']);
        $max_numero = Donne_ID ($connect,"select max(mess_cdn) from messagerie");
        $requete = mysql_query("INSERT INTO messagerie (mess_cdn,envoyeur,origine,contenu,date,sujet,id_user) VALUES
                               ($max_numero,".$_SESSION['id_user'].",\"".html_entity_decode($entree,ENT_QUOTES,'ISO-8859-1')."\",\"".
                               html_entity_decode($suiteEntree. "<br />Cordialement",ENT_QUOTES,'ISO-8859-1')."\",
                               '$date_messagerie','$Quoi',$versQui)");
}


function rated($numBody)
{
  GLOBAL $connect;
  $sqlMyStar = mysql_num_rows(mysql_query("select * from starating where wkstar_body_no ='$numBody' and
                                          starate_auteur_no=".$_SESSION['id_user']));
  return $sqlMyStar;
}
function myrated($numBody)
{
  GLOBAL $connect;
  $sqlMyRate = mysql_result(mysql_query("select starate_note_nb from starating where wkstar_body_no ='$numBody' and
                                          starate_auteur_no=".$_SESSION['id_user']),0,'starate_note_nb');
  return $sqlMyRate;
}
function totalrated($numBody)
{
  GLOBAL $connect;
  $sqltotalStars = mysql_num_rows(mysql_query("select * from starating where wkstar_body_no ='$numBody'"));
  return $sqltotalStars;
}
function rateIt($numBody)
{
  GLOBAL $connect,$lg,$id_seq,$id_parc,$id_grp,$id_wk,$id_clan;
  $sqlMyStar = rated($numBody);
  $NbrAllStars = totalrated($numBody);
  //$sqlAllStars = mysql_result(mysql_query("select SUM(wkstar_note_nb) from wikistar where wkstar_body_no ='$numBody'"),0);
  $sqlAllStars = mysql_result(mysql_query("select SUM(starate_note_nb) from starating where wkstar_body_no ='$numBody'"),0);
  if ($NbrAllStars > 0 && $sqlAllStars > 0)
     $MoyenneStars = round($sqlAllStars/$NbrAllStars,1);
  else
     $MoyenneStars = 0;
  return $MoyenneStars;
}

function affFoto($Qui)
{
    $reqfoto = mysql_query("select util_photo_lb from utilisateur where util_cdn=".$Qui);
    if (mysql_num_rows($reqfoto) > 0)
       $photo = mysql_result($reqfoto,0,'util_photo_lb');
    if ($photo != "")
    {
      list($w_img, $h_img, $type_img, $attr_img) = getimagesize("../images/$photo");
      return " onMouseOver=\"overlib('', TEXTCOLOR, '#ffffff', TEXTSIZE, 2, ABOVE, WIDTH, $w_img, HEIGHT, $h_img, BACKGROUND, '../images/$photo', PADX, 60, 20, PADY, 20, 20)\" onMouseOut=\"nd()\"";
    }else
      return '';
}

function SupprimeWiki($Id,$Clan)
{
   GLOBAL $connect;
   $suppWk = mysql_query("delete from wiki where wiki_cdn=".$Id);
   $suppWk = mysql_query("delete from wikiapp where wkapp_wiki_no=".$Id);
   $lesBodies = mysql_query("select * from wikibodies where wkbody_clan_no='".$Clan."'");
   if (mysql_num_rows($lesBodies) > 0)
   {
       while ($itemBg = mysql_fetch_object($lesBodies))
       {
            $SuppStar = mysql_query("DELETE from starating where wkstar_body_no = '".$itemBg->wkbody_cdn."'");
            $SuppCmt = mysql_query("DELETE from commentaires where comwk_body_no = '".$itemBg->wkbody_cdn."'");
       }
   }
   $SuppBody = mysql_query("DELETE from wikibodies where wkbody_clan_no = ".$Clan);
   $SuppMeta = mysql_query("DELETE from wikimeta where wkmeta_clan_no = ".$Clan);
}

function getextension($fichier)
{
  $bouts = explode(".", $fichier);
  return array(array_pop($bouts), implode(".", $bouts));
}
function numerotation($type,$Nbre)
{
  switch ($type)
  {
    case 'alpha':
       $alpha = array(1 => 'A',2 => 'B',3 => 'C',4 => 'D',5 => 'E',6 => 'F',7 => 'G',8 => 'H',9 => 'I',10 => 'J',11 => 'K',12 => 'L',13 => 'M',
                  14 => 'N',15 => 'O',16 => 'P',17 => 'Q',18 => 'R',19 => 'S',20 => 'T',21 => 'U',22 => 'V',23 => 'W',24 => 'X',25 => 'Y',26 => 'Z');

       return $alpha[$Nbre];
       break;
    case 'romain':
       $roma = array(1 => 'I',2 => 'II',3 => 'III',4 => 'IV',5 => 'V',6 => 'VI',7 => 'VII',8 => 'VIII',9 => 'IX',10 => 'X',11 => 'XI',12 => 'XII',13 => 'XIII',
                  14 => 'XIV',15 => 'XV',16 => 'XVI',17 => 'XVII',18 => 'XVIII',19 => 'XIX',20 => 'XX',21 => 'XXI',22 => 'XXII',23 => 'XXIII',24 => 'XXIV',25 => 'XXV',26 => 'XXXVI');
       return $roma[$Nbre];
       break;
    case 'numeric':
       $numeric = array(1 => '1',2 => '2',3 => '3',4 => '4',5 => '5',6 => '6',7 => '7',8 => '8',9 => '9',10 => '10',11 => '11',12 => '12',13 => '13',
                  14 => '14',15 => '15',16 => '16',17 => '17',18 => '18',19 => '19',20 => '20',21 => '21',22 => '22',23 => '23',24 => '24',25 => '25',26 => '26');
       return $numeric[$Nbre];
       break;
  }
}
?>
