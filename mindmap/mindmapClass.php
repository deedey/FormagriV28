<?php
function compareWk($id,$numero)
{
  GLOBAL $connect,$lg,$Provenance;
  $reqBefore = mysql_query("select * from mindmaphistory where mindhisto_map_no = $id and
                            mindhisto_cdn < $numero order by mindhisto_create_dt desc");
  $reqActu = mysql_query("select * from mindmaphistory where mindhisto_map_no = $id and
                            mindhisto_cdn = $numero");
  $oActu = mysql_fetch_object($reqActu);
  $reqAfter = mysql_query("select * from mindmaphistory where mindhisto_map_no = $id and
                            mindhisto_cdn > $numero order by mindhisto_create_dt asc");
  $html = '';
  if (mysql_num_rows($reqBefore) > 0 || mysql_num_rows($reqAfter) > 0)
      $html = '<span title="Cliquez sur les flèches pour ouvrir ou refermer les éléments" '.
              'style="cursor:help;padding-left:12px;">Comparer<br /></span>';

  if (mysql_num_rows($reqBefore) > 0)
  {
        $html .= CompGauche(CompareWkMoins($id,$numero,$oActu),300,$numero);
  }
  $html .= '<a href="javascript:void(0);" title="L\'instance courante a été validée le '.
           reverse_date(substr(mysql_result($reqActu,0,'mindhisto_create_dt'),0,10),'-','-').' à '.
           substr(mysql_result($reqActu,0,'mindhisto_create_dt'),11,8).
           ' par '.NomUser(mysql_result($reqActu,0,'mindhisto_auteur_no')).
           '"><img src="images/centre.gif" border="0" style="margin:0 0 1px 0;"></a>';
  if (mysql_num_rows($reqAfter) > 0)
  {
        $html .= CompDroite(CompareWkPlus($id,$numero,$oActu),300,$numero);
  }
  if ($html != '')
     return $html;
}
function CompareWkMoins($id,$numero,$oActu)
{
   GLOBAL $connect,$lg,$Provenance;
  $OrdreMoinsBody = mysql_query("select * from mindmaphistory where mindhisto_map_no = $id and
                            mindhisto_cdn < $numero order by mindhisto_create_dt desc");
  $NbLstMoins = mysql_num_rows($OrdreMoinsBody);
  $listMoins = '';
  $NbMns=0;
  if ($NbLstMoins > 0)
  {
     $listMoins .='<ul id="ulCompG'.$numero.'" class="ulComp" style="display:none;">';
     $listMoins .='<li style="background:url(images/titreTable.jpg);color:#fff;height:25px;font-size:11px;'.
                  'padding:8px 0 4px 8px;">Comparer avec les instances précédentes</li>';

     while ($oMoins = mysql_fetch_object($OrdreMoinsBody))
     {
            $listMoins .= '<li style="padding-left:2px;"><a href="javascript:void(0);" '.
                          'onClick="window.open(\'mindDiffText.php?id='.$id.'&numRef='.$oMoins->mindhisto_cdn.
                         '&numero='.$numero.'&IdDt='.$oMoins->mindhisto_create_dt.'&actuDt='.$oActu->mindhisto_create_dt.
                         '&UserActu='.$oActu->mindhisto_auteur_no.'&IdUser='.$oMoins->mindhisto_auteur_no.'\''.
                         ',\'Comparaison des deux contenus\',\'status=no, directories=no,copyhistory=0,titlebar=no,'.
                         ' toolbar=no, location=no, menubar=no, scrollbars=yes, resizable=yes,width=630,height=350,top=200,left=200\');" '.
                         'title="Comparer le contenu affiché avec cette instance du paragraphe" '.
                         'name="Comparaison des deux contenus."> le '.
                         reverse_date(substr($oMoins->mindhisto_create_dt,0,10),'-','-').'   à   '.
                         substr($oMoins->mindhisto_create_dt,11,8).' par '.NomUser($oMoins->mindhisto_auteur_no).'</a></li>';
          $NbMns++;
      }
  }
  $listMoins .='</ul>';
  return $listMoins;
}
function CompareWkPlus($id,$numero,$oActu)
{
  GLOBAL $connect,$lg,$Provenance;
  $OrdrePlusBody = mysql_query("select * from mindmaphistory where mindhisto_map_no = $id and
                            mindhisto_cdn > $numero order by mindhisto_create_dt asc");
  $NbLstPlus = mysql_num_rows($OrdrePlusBody);
  $listPlus = '';
  $NbPls=0;
  if ($NbLstPlus > 0)
  {
     $listPlus .='<ul id="ulCompD'.$numero.'" class="ulComp" style="display:none;">';
     $listPlus .='<li style="background:url(images/titreTable.jpg);color:#fff;height:25px;font-size:11px;'.
                 'padding:8px 0 4px 8px;">Comparer avec les instances suivantes</li>';

      while ($oPlus = mysql_fetch_object($OrdrePlusBody))
      {
            $listPlus .= '<li style="padding-left:2px;"><a href="javascript:void(0);" '.
                          'onClick="window.open(\'mindDiffText.php?id='.$id.'&numRef='.$oPlus->mindhisto_cdn.
                         '&numero='.$numero.'&IdDt='.$oPlus->mindhisto_create_dt.'&actuDt='.$oActu->mindhisto_create_dt.
                         '&UserActu='.$oActu->mindhisto_auteur_no.'&IdUser='.$oPlus->mindhisto_auteur_no.'\''.
                         ',\'Comparaison des deux contenus\',\'status=no,directories=no,copyhistory=0,titlebar=no,'.
                         ' toolbar=no,location=no,menubar=no,scrollbars=yes,resizable=yes,width=630,height=350,top=200,left=200\');" '.
                         'title="Comparer le contenu affiché avec cette instance du paragraphe" '.
                         'name="Comparaison des deux contenus."> le '.
                         reverse_date(substr($oPlus->mindhisto_create_dt,0,10),'-','-').'   à   '.
                         substr($oPlus->mindhisto_create_dt,11,8).' par '.NomUser($oPlus->mindhisto_auteur_no).'</a></li>';
          $NbPls++;
      }
  }
  $listPlus .='</ul>';
  return $listPlus;
}
function HwWk($id,$numero)
{
  GLOBAL $connect,$lg,$Provenance;
  $reqBefore = mysql_query("select * from mindmaphistory where mindhisto_map_no = $id and
                            mindhisto_cdn < $numero order by mindhisto_create_dt desc");
  $reqActu = mysql_query("select * from mindmaphistory where mindhisto_map_no = $id and
                            mindhisto_cdn = $numero");
  $reqAfter = mysql_query("select * from mindmaphistory where  mindhisto_map_no = $id and
                            mindhisto_cdn > $numero order by mindhisto_create_dt asc");
  $html = '';
  if (mysql_num_rows($reqBefore) > 0 || mysql_num_rows($reqAfter) > 0)
      $html = '<span title="Cliquez sur les flèches pour ouvrir ou refermer les historiques" '.
              'style="cursor:help;padding-left:12px;">Naviguez dans l\'historique<br /></span>';

  if (mysql_num_rows($reqBefore) > 0)
  {
        $html .= HwGauche(HwWkMoins($id,$numero),300,$numero);
  }
  $html .= '<a href="javascript:void(0);" title="L\'instance courante a été validée le '.
           reverse_date(substr(mysql_result($reqActu,0,'mindhisto_create_dt'),0,10),'-','-').' à '.
           substr(mysql_result($reqActu,0,'mindhisto_create_dt'),11,8).
           ' par '.NomUser(mysql_result($reqActu,0,'mindhisto_auteur_no')).
           '"><img src="images/centre.gif" border="0" style="margin:0 0 1px 0;"></a>';
  if (mysql_num_rows($reqAfter) > 0)
  {
        $html .= HwDroite(HwWkPlus($id,$numero),300,$numero);
  }
  if ($html != '')
     return $html;
}
function HwWkMoins($id,$numero)
{
   GLOBAL $connect,$lg,$Provenance;
  $OrdreMoinsBody = mysql_query("select * from mindmaphistory where mindhisto_map_no = $id and
                            mindhisto_cdn < $numero order by mindhisto_create_dt desc");
  $NbLstMoins = mysql_num_rows($OrdreMoinsBody);
  $listMoins = '';
  $NbMns=0;
  if ($NbLstMoins > 0)
  {
     $listMoins .='<ul id="ulHwG'.$numero.'" class="ulComp" style="display:none;">';
     $listMoins .='<li style="background:url(images/titreTable.jpg);color:#fff;height:25px;font-size:11px;'.
                  'padding:8px 0 4px 8px;">Naviguer dans les instances précédentes</li>';

     while ($oMoins = mysql_fetch_object($OrdreMoinsBody))
     {
            $listMoins .= '<li style="padding-left:4px;"><a href="index.php?id='.$id.'&Provenance='.$Provenance.'&numero='.
                          $oMoins->mindhisto_cdn.'">'.reverse_date(substr($oMoins->mindhisto_create_dt,0,10),'-','-').'   à   '.
                          substr($oMoins->mindhisto_create_dt,11,8).' par '.NomUser($oMoins->mindhisto_auteur_no).'</a></li>';
          $NbMns++;
      }
  }
  $listMoins .='</ul>';
  return $listMoins;
}

function HwWkPlus($id,$numero)
{
  GLOBAL $connect,$lg,$Provenance;
  $OrdrePlusBody = mysql_query("select * from mindmaphistory where mindhisto_map_no = $id and
                            mindhisto_cdn > $numero order by mindhisto_create_dt asc");
  $NbLstPlus = mysql_num_rows($OrdrePlusBody);
  $listPlus = '';
  $NbPls=0;
  if ($NbLstPlus > 0)
  {
     $listPlus .='<ul id="ulHwD'.$numero.'" class="ulComp" style="display:none;">';
     $listPlus .='<li style="background:url(images/titreTable.jpg);color:#fff;height:25px;font-size:11px;'.
                 'padding:8px 0 4px 8px;">Naviguer dans les instances suivantes</li>';
      while ($oPlus = mysql_fetch_object($OrdrePlusBody))
      {
            $listPlus .= '<li style="padding-left:2px;"><a href="index.php?id='.$id.'&Provenance='.$Provenance.'&numero='.
                          $oPlus->mindhisto_cdn.'">'.reverse_date(substr($oPlus->mindhisto_create_dt,0,10),'-','-').'   à   '.
                          substr($oPlus->mindhisto_create_dt,11,8).' par '.NomUser($oPlus->mindhisto_auteur_no).'</a></li>';
          $NbPls++;
      }
  }
  $listPlus .='</ul>';
  return $listPlus;
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