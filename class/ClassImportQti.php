<?php
if (!isset($_SESSION)) session_start();
class ImpQti
{
    public static function Qti20($compteur,$NewIdQcm,$list,$ddj)
    {
       GLOBAL $connect,$note;
       for ($cx =1; $cx < ($compteur+1); $cx++)
       {
            $ImgQ = '';
            $image = 'non';
            $typ_img = '';
            if (isset($_SESSION['XmlFichier'][$cx]['assessmentItem']['responseDeclaration']['attr']['cardinality']) &&
                (strtolower($_SESSION['XmlFichier'][$cx]['assessmentItem']['responseDeclaration']['attr']['cardinality']) == 'single' ||
                strtolower($_SESSION['XmlFichier'][$cx]['assessmentItem']['responseDeclaration']['attr']['cardinality']) == 'multiple'))
            {
                $titreQ = ImpQti::titreQAsmt($cx);
                $ImgQ = ImpQti::ImgQAsmt($cx);
                $nbLines = count($_SESSION['XmlFichier'][$cx]['assessmentItem']['itemBody']['choiceInteraction']['simpleChoice']);
                if ($ImgQ != '')
                {
                   //echo $ImgQ."ici et la <br />";
                   if (strstr($ImgQ,'|'))
                   {
                     $TabImg = explode('|',$ImgQ);
                     $typ_img = $TabImg[0];
                     $ifImage = $TabImg[1];
                   }
                   else
                     $ifImage = $ImgQ;
                   list($extension, $nom) = ImpQti::getextension($ifImage);
                   if (!strstr($ImgQ,'|'))
                     $typ_img = "image/$extension";
                   for ($i=0; $i<sizeof($list); $i++)
                   {
                      if (strstr($list[$i]["filename"],$ifImage))
                      {
                          $adrImg = $list[$i]["filename"];
                          $destImg ="ressources/qcm_images/".$nom."_".$ddj.".".$extension;
                          copy($adrImg,$destImg);
                          chmod($destImg,0775);
                          $image = str_replace("ressources/","",$destImg);
                          //echo "<br /> adresse image".$destImg ."<br />";
                      }
                   }

                }
                $NewIdData =  Donne_ID ($connect,"SELECT max(qcm_data_cdn) from qcm_donnees");
                $NewQcm =  mysql_query ("insert into qcm_donnees (qcm_data_cdn,qcmdata_auteur_no,n_lignes,question,typ_img,image) ".
                                        "values('".$NewIdData."','".$_SESSION['id_user']."','".$nbLines."','".NewHtmlentities($titreQ,ENT_QUOTES)."','".$typ_img."',\"".$image."\")");
                $NewIdLinker =  Donne_ID ($connect,"SELECT max(qcmlinker_cdn) from qcm_linker");
                $NewQcmLinker =  mysql_query ("insert into qcm_linker (qcmlinker_cdn,qcmlinker_param_no,qcmlinker_data_no,qcmlinker_number_no) ".
                                           "values('".$NewIdLinker."','".$NewIdQcm."','".$NewIdData."','".$cx."')");
            }
            if (isset($_SESSION['XmlFichier'][$cx]['assessmentItem']['responseDeclaration']['attr']['cardinality']) &&
                strtolower($_SESSION['XmlFichier'][$cx]['assessmentItem']['responseDeclaration']['attr']['cardinality']) == 'single')
                $_SESSION['content'][$cx] = ImpQti::choice20($cx,$NewIdData);
            elseif (isset($_SESSION['XmlFichier'][$cx]['assessmentItem']['responseDeclaration']['attr']['cardinality']) &&
                    strtolower($_SESSION['XmlFichier'][$cx]['assessmentItem']['responseDeclaration']['attr']['cardinality']) == 'multiple')
                $_SESSION['content'][$cx] = ImpQti::multiple20($cx,$NewIdData);
            //echo "<pre>";print_r($_SESSION['XmlFichier'][$cx]);echo"</pre>";
       }
    }
    public static function choice20($cx,$QcmIdData)
    {
       GLOBAL $connect,$note;
        //retourner (n°page=compteur,nbre de lignes,question,"qcm_data",blob='',typ_img,nprop,prop jusqu'à 10,
        //nvalue,values jusqu'à 10,note, multiple,image
        $nbSol = count($_SESSION['XmlFichier'][$cx]['assessmentItem']['itemBody']['choiceInteraction']['simpleChoice']);
        for ($i = 1;$i < ($nbSol+1); $i++)
        {
           $l = $i-1;
           $propos = $i."_prop";
           $val = $i."_val";
           $props = $_SESSION['XmlFichier'][$cx]['assessmentItem']['itemBody']['choiceInteraction']['simpleChoice'][$l]['value'];
//echo "<br>propos = $props";
//echo "<br>".$_SESSION['XmlFichier'][$cx]['assessmentItem']['responseDeclaration']['correctResponse']['value']['value']." et ".
//$_SESSION['XmlFichier'][$cx]['assessmentItem']['itemBody']['choiceInteraction']['simpleChoice'][$l]['attr']['identifier']."<br>";
           $PointVal = ($_SESSION['XmlFichier'][$cx]['assessmentItem']['responseDeclaration']['correctResponse']['value']['value'] ==
           $_SESSION['XmlFichier'][$cx]['assessmentItem']['itemBody']['choiceInteraction']['simpleChoice'][$l]['attr']['identifier']) ? 1 : 0;
           $requete = mysql_query("update qcm_donnees set ".$propos." = \"".NewHtmlentities($props,ENT_QUOTES)."\", ".$val." = '".$PointVal."' where qcm_data_cdn = '".$QcmIdData."'");
           $note += $PointVal;
        }
        $requete = mysql_query("update qcm_donnees set note = '1' where qcm_data_cdn = '".$QcmIdData."'");
        return true;
    }

    public static function multiple20($cx,$QcmIdData)
    {
        GLOBAL $note;
        //retourner (n°page=compteur,nbre de lignes,question,"qcm_data",blob='',typ_img,nprop,prop jusqu'à 10,
        //nvalue,values jusqu'à 10,note, multiple,image
        $nbSol = count($_SESSION['XmlFichier'][$cx]['assessmentItem']['itemBody']['choiceInteraction']['simpleChoice']);
        for ($i = 1;$i < ($nbSol+1); $i++)
        {
           $l = $i-1;
           $propos = $i."_prop";
           $val = $i."_val";
           $props = utf8_decode_si_utf8(trim($_SESSION['XmlFichier'][$cx]['assessmentItem']['itemBody']['choiceInteraction']['simpleChoice'][$l]['value']));
//echo "<br>propos = $props";
           if (isset($_SESSION['XmlFichier'][$cx]['assessmentItem']['responseDeclaration']['mapping']['attr']['upperBound']))
              $nbrResponses = $_SESSION['XmlFichier'][$cx]['assessmentItem']['responseDeclaration']['mapping']['attr']['upperBound'];
           else
              $nbrResponses = count($_SESSION['XmlFichier'][$cx]['assessmentItem']['responseDeclaration']['correctResponse']['value']);
           $rsp = 0;
           while ($rsp < $nbrResponses)
           {
              $PointVal = ($_SESSION['XmlFichier'][$cx]['assessmentItem']['responseDeclaration']['correctResponse']['value'][$rsp]['value'] ==
              $_SESSION['XmlFichier'][$cx]['assessmentItem']['itemBody']['choiceInteraction']['simpleChoice'][$l]['attr']['identifier']) ? 1 : 0;
//echo "<br>".$_SESSION['XmlFichier'][$cx]['assessmentItem']['responseDeclaration']['correctResponse']['value'][$rsp]['value']." et ".
//$_SESSION['XmlFichier'][$cx]['assessmentItem']['itemBody']['choiceInteraction']['simpleChoice'][$l]['attr']['identifier']." et point = $PointVal resp = $rsp et val= $val<br>";
              $requete = mysql_query("update qcm_donnees set ".$propos." = \"".NewHtmlentities(utf8_decode($props),ENT_QUOTES)."\", ".$val." = '".$PointVal."' where qcm_data_cdn = '".$QcmIdData."'");
//echo"update qcm_donnees set ".$propos." = \"".NewHtmlentities($props,ENT_QUOTES)."\", ".$val." = '".$PointVal."' where qcm_data_cdn = '".$QcmIdData."' <br>";
              $note += $PointVal;
              if ($PointVal == 1)
                 break;
              $rsp++;
           }
        }
        $requete = mysql_query("update qcm_donnees set note = '1', multiple='1' where qcm_data_cdn = '".$QcmIdData."'");
        //echo "<br>note == $note <br />";
        return true;
    }
    public static function titreQAsmt($cx)
    {
        $data = utf8_decode(trim($_SESSION['XmlFichier'][$cx]['assessmentItem']['attr']['title']));
        if (isset($_SESSION['XmlFichier'][$cx]['assessmentItem']['itemBody']['choiceInteraction']['prompt']['value']))
            $data .= '....'.utf8_decode(trim($_SESSION['XmlFichier'][$cx]['assessmentItem']['itemBody']['choiceInteraction']['prompt']['value']));
        return $data;
    }
    public static function ImgQAsmt($cx)
    {
        if (isset($_SESSION['XmlFichier'][$cx]['assessmentItem']['itemBody']['object']))
        {
            $data = $_SESSION['XmlFichier'][$cx]['assessmentItem']['itemBody']['object']['attr']['type'];
            $data .= '|';
            $data .= $_SESSION['XmlFichier'][$cx]['assessmentItem']['itemBody']['object']['attr']['data'];
        }
        elseif (isset($_SESSION['XmlFichier'][$cx]['assessmentItem']['itemBody']['p'][0]['img']))
            $data = $_SESSION['XmlFichier'][$cx]['assessmentItem']['itemBody']['p'][0]['img']['attr']['src'];
        elseif (isset($_SESSION['XmlFichier'][$cx]['assessmentItem']['itemBody']['p'][1]['img']))
            $data = $_SESSION['XmlFichier'][$cx]['assessmentItem']['itemBody']['p'][1]['img']['attr']['src'];
        else
            $data = '';
        return $data;
    }


     public static function manageQTI12Assessment($compteur,$ddj)
     {
                  if (!empty($_SESSION['note']))
                        $_SESSION['note'] = 0;
                  $content = '';
                  $comptageItems = 0;$nbChoice = 0;
                  $titreQcm = utf8_decode_si_utf8(trim($_SESSION['XmlFichier'][$compteur]['questestinterop']['assessment']['attr']['title']));
                  if (isset($_SESSION['XmlFichier'][$compteur]['questestinterop']['assessment']['section']))
                  {//  Q/Ass//Sect
                     //$nbSect = count($_SESSION['XmlFichier'][$compteur]['questestinterop']['assessment']['section']);
                     if (isset($_SESSION['XmlFichier'][$compteur]['questestinterop']['assessment']['section'][0]))
                     {//  Q/Ass//Sect +
                         $sct = 0;
                         while (isset($_SESSION['XmlFichier'][$compteur]['questestinterop']['assessment']['section'][$sct]))
                         {
                            $nbSousSect = 0;
                            if (isset($_SESSION['XmlFichier'][$compteur]['questestinterop']['assessment']['section'][$sct]['section']))
                                 $nbSousSect = count($_SESSION['XmlFichier'][$compteur]['questestinterop']['assessment']['section'][$sct]['section']);
                            if ($nbSousSect == 0)
                            {//  Q/Ass//Sect/Sect
                              if (isset($_SESSION['XmlFichier'][$compteur]['questestinterop']['assessment']['section'][$sct]['item']['presentation']))
                              {//  Q/Ass//Sect/Sect +/Itm
                                  $countitem = 1; $comptageItems = 1;
                                  if (isset($_SESSION['XmlFichier'][$compteur]['questestinterop']['assessment']['section'][$sct]['item']['presentation']['flow']['response_lid']['render_choice'])
                                      || isset($_SESSION['XmlFichier'][$compteur]['questestinterop']['assessment']['section'][$sct]['item']['presentation']['response_lid']['render_choice']))
                                  {
                                      $nbChoice++;
                                      if ($nbChoice == 1 && $_SESSION['Param'] == 0)
                                      {
                                      //echo "ici";
                                          $NewIdQcm = ImpQti::ParamOk($ddj);
                                      }
                                   }
                                   $Item = $_SESSION['XmlFichier'][$compteur]['questestinterop']['assessment']['section'][$sct]['item'];
                                   $countitem++;
                                   $comptageItems++;
                                   $compteurItemTotal++;
                                   $ManageItem = ImpQti::GereItem($Item,$ddj,$NewIdQcm,$compteurItemTotal);
                               }//fin  Q/Ass//Sect/Sect +/Itm
                               else
                               {
                                $countitem = 0;
                                while (isset($_SESSION['XmlFichier'][$compteur]['questestinterop']['assessment']['section'][$sct]['item'][$countitem]))
                                {//  Q/Ass//Sect/Sect+/Itm +
                                   if (isset($_SESSION['XmlFichier'][$compteur]['questestinterop']['assessment']['section'][$sct]['item'][$countitem]['presentation']['flow']['response_lid']['render_choice'])
                                       || isset($_SESSION['XmlFichier'][$compteur]['questestinterop']['assessment']['section'][$sct]['item'][$countitem]['presentation']['response_lid']['render_choice']))
                                       $nbChoice++;
                                   if ($nbChoice == 1 && $_SESSION['Param'] == 0)
                                   {
                                       $NewIdQcm = ImpQti::ParamOk($ddj);
                                   }
                                   $Item = $_SESSION['XmlFichier'][$compteur]['questestinterop']['assessment']['section'][$sct]['item'][$countitem];
                                   $countitem++;
                                   $comptageItems++;
                                   $compteurItemTotal++;
                                   $ManageItem = ImpQti::GereItem($Item,$ddj,$NewIdQcm,$compteurItemTotal);
                                }//fin  Q/Ass//Sect/Sect+/Itm +
                               }
                              }
                              elseif ($nbSousSect > 0)
                              {//  Q/Ass//Sect/Sect +
                                 $comptageItems = 0;$sousSct = 0;
                                 while (isset($_SESSION['XmlFichier'][$compteur]['questestinterop']['assessment']['section'][$sct]['section'][$sousSct]))
                                 {//  Q/Ass//Sect/Sect +
                                      $countitem = 0;
                                      if (isset($_SESSION['XmlFichier'][$compteur]['questestinterop']['assessment']['section'][$sct]['section'][$sousSct]['item']['presentation']))
                                      {//  Q/Ass//Sect/Sect +/Itm
                                           $countitem = 1;
                                           if (isset($_SESSION['XmlFichier'][$compteur]['questestinterop']['assessment']['section'][$sct]['section'][$sousSct]['item']['presentation']['flow']['response_lid']['render_choice'])
                                       || isset($_SESSION['XmlFichier'][$compteur]['questestinterop']['assessment']['section'][$sct]['section'][$sousSct]['item']['presentation']['response_lid']['render_choice']))
                                           {
                                               $nbChoice++;
                                               if ($nbChoice == 1 && $_SESSION['Param'] == 0)
                                               {
                                                   $NewIdQcm = ImpQti::ParamOk($ddj);
                                               }
                                           }
                                           $Item = $_SESSION['XmlFichier'][$compteur]['questestinterop']['assessment']['section'][$sct]['section'][$sousSct]['item'];
                                           $comptageItems++;
                                           $compteurItemTotal++;
                                           $ManageItem = ImpQti::GereItem($Item,$ddj,$NewIdQcm,$compteurItemTotal);
                                      }//fin  Q/Ass//Sect/Sect +/Itm
                                      else
                                      {
                                          $countitem = 0;
                                          while (isset($_SESSION['XmlFichier'][$compteur]['questestinterop']['assessment']['section'][$sct]['section'][$sousSct]['item'][$countitem]))
                                          {//  Q/Ass//Sect/Sect +/Itm +
                                               if (isset($_SESSION['XmlFichier'][$compteur]['questestinterop']['assessment']['section'][$sct]['section'][$sousSct]['item'][$countitem]['presentation']['flow']['response_lid']['render_choice'])
                                                   || isset($_SESSION['XmlFichier'][$compteur]['questestinterop']['assessment']['section'][$sct]['section'][$sousSct]['item'][$countitem]['presentation']['response_lid']['render_choice']))
                                                   $nbChoice++;
                                               if ($nbChoice == 1 && $_SESSION['Param'] == 0)
                                               {
                                                   $NewIdQcm = ImpQti::ParamOk($ddj);
                                               }
                                               $Item = $_SESSION['XmlFichier'][$compteur]['questestinterop']['assessment']['section'][$sct]['section'][$sousSct]['item'][$countitem];
                                               $countitem++;
                                               $comptageItems++;
                                               $compteurItemTotal++;
                                               $ManageItem = ImpQti::GereItem($Item,$ddj,$NewIdQcm,$compteurItemTotal);
                                          }//fin  Q/Ass//Sect/Sect +/Itm +
                                      }
                                      $sousSct++;
                                 }//fin  Q/Ass//Sect/Sect +
                              }
                              $sct++;
                            }
                         }
                         elseif (!isset($_SESSION['XmlFichier'][$compteur]['questestinterop']['assessment']['section'][0]) &&
                                 isset($_SESSION['XmlFichier'][$compteur]['questestinterop']['assessment']['section']))
                         {
                            $nbSousSect = 0;
                            if (isset($_SESSION['XmlFichier'][$compteur]['questestinterop']['assessment']['section']['section']))
                                 $nbSousSect = count($_SESSION['XmlFichier'][$compteur]['questestinterop']['assessment']['section']['section']);
                            if (!isset($_SESSION['XmlFichier'][$compteur]['questestinterop']['assessment']['section']['section']))
                            {//  Q/Ass//Sect/Sect
                              if (isset($_SESSION['XmlFichier'][$compteur]['questestinterop']['assessment']['section']['item']['presentation']))
                              {//  Q/Ass//Sect/Sect +/Itm
                                  $countitem = 1; $comptageItems = 1;
                                  if (isset($_SESSION['XmlFichier'][$compteur]['questestinterop']['assessment']['section']['item']['presentation']['flow']['response_lid']['render_choice'])
                                       || isset($_SESSION['XmlFichier'][$compteur]['questestinterop']['assessment']['section']['item']['presentation']['response_lid']['render_choice']))
                                  {
                                      $nbChoice++;
                                      if ($nbChoice == 1 && $_SESSION['Param'] == 0)
                                      {
                                          $NewIdQcm = ImpQti::ParamOk($ddj);
                                      }
                                   }
                                 $Item = $_SESSION['XmlFichier'][$compteur]['questestinterop']['assessment']['section']['item'];
                                 $compteurItemTotal++;
                                 $comptageItems++;
                                 $ManageItem = ImpQti::GereItem($Item,$ddj,$NewIdQcm,$compteurItemTotal);
                               }//fin  Q/Ass//Sect/Sect +/Itm
                               elseif (isset($_SESSION['XmlFichier'][$compteur]['questestinterop']['assessment']['section']['item'][0]))
                               {
                                $countitem = 0;
                                while (isset($_SESSION['XmlFichier'][$compteur]['questestinterop']['assessment']['section']['item'][$countitem]))
                                {//  Q/Ass//Sect/Sect+/Itm +
                                   if (isset($_SESSION['XmlFichier'][$compteur]['questestinterop']['assessment']['section']['item'][$countitem]['presentation']['flow']['response_lid']['render_choice'])
                                       || isset($_SESSION['XmlFichier'][$compteur]['questestinterop']['assessment']['section']['item'][$countitem]['presentation']['response_lid']['render_choice']))
                                       $nbChoice++;
                                   if ($nbChoice == 1 && $_SESSION['Param'] == 0)
                                   {
                                          $NewIdQcm = ImpQti::ParamOk($ddj);
                                   }
                                   $Item = $_SESSION['XmlFichier'][$compteur]['questestinterop']['assessment']['section']['item'][$countitem];
                                   $countitem++;
                                   $comptageItems++;
                                   $compteurItemTotal++;
                                   $ManageItem = ImpQti::GereItem($Item,$ddj,$NewIdQcm,$compteurItemTotal);
                                }//fin  Q/Ass//Sect/Sect+/Itm +
                               }
                              }
                              elseif (isset($_SESSION['XmlFichier'][$compteur]['questestinterop']['assessment']['section']['section']))
                              {//  Q/Ass//Sect/Sect +
                                 $comptageItems = 0;$sousSct = 0;
                                 while (isset($_SESSION['XmlFichier'][$compteur]['questestinterop']['assessment']['section']['section'][$sousSct]))
                                 {//  Q/Ass//Sect/Sect +
                                      $countitem = 0;
                                      if (isset($_SESSION['XmlFichier'][$compteur]['questestinterop']['assessment']['section']['section'][$sousSct]['item']['presentation']))
                                      {//  Q/Ass//Sect/Sect +/Itm
                                           $countitem = 1;;
                                           if (isset($_SESSION['XmlFichier'][$compteur]['questestinterop']['assessment']['section']['section'][$sousSct]['item']['presentation']['flow']['response_lid']['render_choice'])
                                       || isset($_SESSION['XmlFichier'][$compteur]['questestinterop']['assessment']['section'][$sousSct]['item'][$countitem]['presentation']['response_lid']['render_choice']))
                                           {
                                               $nbChoice++;
                                               if ($nbChoice == 1 && $_SESSION['Param'] == 0)
                                               {
                                                   $NewIdQcm = ImpQti::ParamOk($ddj);
                                               }
                                               $Item = $_SESSION['XmlFichier'][$compteur]['questestinterop']['assessment']['section']['section'][$sousSct]['item'];
                                               $comptageItems++;
                                               $compteurItemTotal++;
                                               $ManageItem = ImpQti::GereItem($Item,$ddj,$NewIdQcm,$compteurItemTotal);
                                           }
                                      }//fin  Q/Ass//Sect/Sect +/Itm
                                      else
                                      {
                                          $countitem = 0;
                                          while (isset($_SESSION['XmlFichier'][$compteur]['questestinterop']['assessment']['section']['section'][$sousSct]['item'][$countitem]))
                                          {//  Q/Ass//Sect/Sect +/Itm +
                                               if (isset($_SESSION['XmlFichier'][$compteur]['questestinterop']['assessment']['section']['section'][$sousSct]['item'][$countitem]['presentation']['flow']['response_lid']['render_choice'])
                                       || isset($_SESSION['XmlFichier'][$compteur]['questestinterop']['assessment']['section']['section'][$sousSct]['item'][$countitem]['presentation']['response_lid']['render_choice']))
                                                   $nbChoice++;
                                               if ($nbChoice == 1 && $_SESSION['Param'] == 0)
                                               {
                                                   $NewIdQcm = ImpQti::ParamOk($ddj);
                                               }
                                               $Item = $_SESSION['XmlFichier'][$compteur]['questestinterop']['assessment']['section']['section'][$sousSct]['item'][$countitem];
                                               $countitem++;
                                               $comptageItems++;
                                               $compteurItemTotal++;
                                               $ManageItem = ImpQti::GereItem($Item,$ddj,$NewIdQcm,$compteurItemTotal);
                                          }//fin  Q/Ass//Sect/Sect +/Itm +
                                      }
                                      $sousSct++;
                                 }//fin  Q/Ass//Sect/Sect +
                              }
                            }
                         }
      return $NewIdQcm;
    }
    public static function manageQTI12Section($compteur,$ddj)
    {
         GLOBAL $compteurItems,$compteurItemTotal;
          if (!empty($_SESSION['note']))
              $_SESSION['note'] = 0;
          $content = '';
                     $nbSect = count($_SESSION['XmlFichier'][$compteur]['questestinterop']['section']);
                     if ($nbSect > 1 && isset($_SESSION['XmlFichier'][$compteur]['questestinterop']['section'][1]))
                     {//  Q/Ass//Sect +
                         $nbChoice = 0;
                         for ($sct = 0; $sct < $nbSect; $sct++)
                         {/// debut de for section
                            $nbSousSect = 0;
                            if (isset($_SESSION['XmlFichier'][$compteur]['questestinterop']['section'][$sct]['section']))
                                 $nbSousSect = count($_SESSION['XmlFichier'][$compteur]['questestinterop']['section'][$sct]['section']);
                            if ($nbSousSect == 0)
                            {//  Q/Ass//Sect/Sect
                              if (isset($_SESSION['XmlFichier'][$compteur]['questestinterop']['section'][$sct]['item']['presentation']))
                              {//  Q/Ass//Sect/Sect +/Itm
                                  $countitem = 1; $comptageItems = 1;
                                  if (isset($_SESSION['XmlFichier'][$compteur]['questestinterop']['section'][$sct]['item']['presentation']['flow']['response_lid']['render_choice'])
                                     || isset($_SESSION['XmlFichier'][$compteur]['questestinterop']['section'][$sct]['item']['presentation']['response_lid']['render_choice']))
                                  {
                                      $nbChoice++;
                                      if ($nbChoice == 1 && $_SESSION['Param'] == 0)
                                      {
                                          $NewIdQcm = ImpQti::ParamOk($ddj);
                                      }
                                   }
                                 $Item = $_SESSION['XmlFichier'][$compteur]['questestinterop']['section'][$sct]['item'];
                                 $comptageItems++;
                                 $compteurItemTotal++;
                                 $ManageItem = ImpQti::GereItem($Item,$ddj,$NewIdQcm,$compteurItemTotal);
                               }//fin  Q/Ass//Sect/Sect +/Itm
                               else
                               {
                                $countitem = 0;
                                while (isset($_SESSION['XmlFichier'][$compteur]['questestinterop']['section'][$sct]['item'][$countitem]))
                                {//  Q/Ass//Sect/Sect+/Itm +
                                   if (isset($_SESSION['XmlFichier'][$compteur]['questestinterop']['section'][$sct]['item'][$countitem]['presentation']['flow']['response_lid']['render_choice'])
                                     || isset($_SESSION['XmlFichier'][$compteur]['questestinterop']['section'][$sct]['item']['presentation']['response_lid']['render_choice']))
                                       $nbChoice++;
                                   if ($nbChoice == 1 && $_SESSION['Param'] == 0)
                                   {
                                          $NewIdQcm = ImpQti::ParamOk($ddj);
                                   }
                                   $Item = $_SESSION['XmlFichier'][$compteur]['questestinterop']['section'][$sct]['item'][$countitem];
                                   $countitem++;
                                   $comptageItems++;
                                   $compteurItemTotal++;
                                   $ManageItem = ImpQti::GereItem($Item,$ddj,$NewIdQcm,$compteurItemTotal);
                                }//fin  Q/Ass//Sect/Sect+/Itm +
                               }
                              }
                              elseif ($nbSousSect > 0)
                              {//  Q/Ass//Sect/Sect +
                                 $comptageItems = 0;$sousSct = 0;
                                 while (isset($_SESSION['XmlFichier'][$compteur]['questestinterop']['section'][$sct]['section'][$sousSct]))
                                 {//  Q/Ass//Sect/Sect +
                                      $countitem = 0;
                                      if (isset($_SESSION['XmlFichier'][$compteur]['questestinterop']['section'][$sct]['section'][$sousSct]['item']['presentation']))
                                      {//  Q/Ass//Sect/Sect +/Itm
                                           $countitem = 1;
                                           if (isset($_SESSION['XmlFichier'][$compteur]['questestinterop']['section'][$sct]['section'][$sousSct]['item']['presentation']['flow']['response_lid']['render_choice'])
                                     || isset($_SESSION['XmlFichier'][$compteur]['questestinterop']['section'][$sct]['section'][$sousSct]['item']['presentation']['response_lid']['render_choice']))
                                           {
                                               $nbChoice++;
                                               if ($nbChoice == 1 && $_SESSION['Param'] == 0)
                                               {
                                                   $NewIdQcm = ImpQti::ParamOk($ddj);
                                               }
                                               $Item = $_SESSION['XmlFichier'][$compteur]['questestinterop']['section'][$sct]['section'][$sousSct]['item'];
                                           }
                                           $comptageItems++;
                                           $compteurItemTotal++;
                                           $ManageItem = ImpQti::GereItem($Item,$ddj,$NewIdQcm,$compteurItemTotal);
                                      }//fin  Q/Ass//Sect/Sect +/Itm
                                      else
                                      {
                                          $countitem = 0;
                                          while (isset($_SESSION['XmlFichier'][$compteur]['questestinterop']['section'][$sct]['section'][$sousSct]['item'][$countitem]))
                                          {//  Q/Ass//Sect/Sect +/Itm +
                                               if (isset($_SESSION['XmlFichier'][$compteur]['questestinterop']['section'][$sct]['section'][$sousSct]['item'][$countitem]['presentation']['flow']['response_lid']['render_choice'])
                                                   || isset($_SESSION['XmlFichier'][$compteur]['questestinterop']['section'][$sct]['section'][$sousSct]['item']['presentation']['response_lid']['render_choice']))
                                                   $nbChoice++;
                                               if ($nbChoice == 1 && $_SESSION['Param'] == 0)
                                               {
                                                   $NewIdQcm = ImpQti::ParamOk($ddj);
                                               }
                                               $Item = $_SESSION['XmlFichier'][$compteur]['questestinterop']['section'][$sct]['section'][$sousSct]['item'][$countitem];
                                               $countitem++;
                                               $comptageItems++;
                                               $compteurItemTotal++;
                                               $ManageItem = ImpQti::GereItem($Item,$ddj,$NewIdQcm,$compteurItemTotal);
                                          }//fin  Q/Ass//Sect/Sect +/Itm +
                                      }
                                      $sousSct++;
                                 }//fin  Q/Ass//Sect/Sect +
                              }//fin de for section
                            }
                         }
                         elseif (!isset($_SESSION['XmlFichier'][$compteur]['questestinterop']['section'][0]))
                         {
                            $nbSousSect = 0;
                            if (isset($_SESSION['XmlFichier'][$compteur]['questestinterop']['section']['section']))
                                 $nbSousSect = count($_SESSION['XmlFichier'][$compteur]['questestinterop']['section']['section']);
                            if (!isset($_SESSION['XmlFichier'][$compteur]['questestinterop']['section']['section']))
                            {//  Q/Ass//Sect/Sect
                              if (isset($_SESSION['XmlFichier'][$compteur]['questestinterop']['section']['item']['presentation']))
                              {//  Q/Ass//Sect/Sect +/Itm
                                  $countitem = 1; $comptageItems = 1;
                                  if (isset($_SESSION['XmlFichier'][$compteur]['questestinterop']['section']['item']['presentation']['flow']['response_lid']['render_choice'])
                                     || isset($_SESSION['XmlFichier'][$compteur]['questestinterop']['section']['item']['presentation']['response_lid']['render_choice']))
                                  {
                                      $nbChoice++;
                                      if ($nbChoice == 1 && $_SESSION['Param'] == 0)
                                      {
                                          $NewIdQcm = ImpQti::ParamOk($ddj);
                                      }
                                   }
                                 $Item = $_SESSION['XmlFichier'][$compteur]['questestinterop']['section']['item'];
                                 $compteurItemTotal++;
                                 $ManageItem = ImpQti::GereItem($Item,$ddj,$NewIdQcm,$compteurItemTotal);
                               }//fin  Q/Ass//Sect/Sect +/Itm
                               else
                               {
                                $countitem = 0;
                                while (isset($_SESSION['XmlFichier'][$compteur]['questestinterop']['section']['item'][$countitem]))
                                {//  Q/Ass//Sect/Sect+/Itm +
                                   if (isset($_SESSION['XmlFichier'][$compteur]['questestinterop']['section']['item'][$countitem]['presentation']['flow']['response_lid']['render_choice'])
                                     || isset($_SESSION['XmlFichier'][$compteur]['questestinterop']['section']['item']['presentation']['response_lid']['render_choice']))
                                       $nbChoice++;
                                   if ($nbChoice == 1 && $_SESSION['Param'] == 0)
                                   {
                                          $NewIdQcm = ImpQti::ParamOk($ddj);
                                   }
                                   $Item = $_SESSION['XmlFichier'][$compteur]['questestinterop']['section']['item'][$countitem];
                                   $countitem++;
                                   $comptageItems++;
                                   $compteurItemTotal++;
                                   $ManageItem = ImpQti::GereItem($Item,$ddj,$NewIdQcm,$compteurItemTotal);
                                }//fin  Q/Ass//Sect/Sect+/Itm +
                               }
                              }
                              elseif (isset($_SESSION['XmlFichier'][$compteur]['questestinterop']['section']['section']))
                              {//  Q/Ass//Sect/Sect +
                                 $comptageItems = 0;$sousSct = 0;
                                 while (isset($_SESSION['XmlFichier'][$compteur]['questestinterop']['section']['section'][$sousSct]))
                                 {//  Q/Ass//Sect/Sect +
                                      $countitem = 0;
                                      if (isset($_SESSION['XmlFichier'][$compteur]['questestinterop']['section']['section'][$sousSct]['item']['presentation']))
                                      {//  Q/Ass//Sect/Sect +/Itm
                                           $countitem = 1;;
                                           if (isset($_SESSION['XmlFichier'][$compteur]['questestinterop']['section']['section'][$sousSct]['item']['presentation']['flow']['response_lid']['render_choice'])
                                     || isset($_SESSION['XmlFichier'][$compteur]['questestinterop']['section']['section'][$sousSct]['item']['presentation']['response_lid']['render_choice']))
                                           {
                                               $nbChoice++;
                                               if ($nbChoice == 1 && $_SESSION['Param'] == 0)
                                               {
                                                   $NewIdQcm = ImpQti::ParamOk($ddj);
                                                   $Item = $_SESSION['XmlFichier'][$compteur]['questestinterop']['section']['section'][$sousSct]['item'];
                                               }
                                               $comptageItems++;
                                               $compteurItemTotal++;
                                               $ManageItem = ImpQti::GereItem($Item,$ddj,$NewIdQcm,$compteurItemTotal);
                                           }
                                      }//fin  Q/Ass//Sect/Sect +/Itm
                                      else
                                      {
                                          $countitem = 0;
                                          while (isset($_SESSION['XmlFichier'][$compteur]['questestinterop']['section']['section'][$sousSct]['item'][$countitem]))
                                          {//  Q/Ass//Sect/Sect +/Itm +
                                               if (isset($_SESSION['XmlFichier'][$compteur]['questestinterop']['section']['section'][$sousSct]['item'][$countitem]['presentation']['flow']['response_lid']['render_choice'])
                                     || isset($_SESSION['XmlFichier'][$compteur]['questestinterop']['section']['section'][$sousSct]['item'][$countitem]['presentation']['response_lid']['render_choice']))
                                                   $nbChoice++;
                                               if ($nbChoice == 1 && $_SESSION['Param'] == 0)
                                               {
                                                   $NewIdQcm = ImpQti::ParamOk($ddj);
                                               }
                                               $Item = $_SESSION['XmlFichier'][$compteur]['questestinterop']['section']['section'][$sousSct]['item'][$countitem];
                                               $countitem++;
                                               $comptageItems++;
                                               $compteurItemTotal++;
                                               $ManageItem = ImpQti::GereItem($Item,$ddj,$NewIdQcm,$compteurItemTotal);
                                          }//fin  Q/Ass//Sect/Sect +/Itm +
                                      }
                                      $sousSct++;
                                 }//fin  Q/Ass//Sect/Sect +
                              }
                            }
      return $NewIdQcm;
    }

    public static function manageQTI12Item($compteur,$ddj)
    {
          if (!empty($_SESSION['note']))
               $_SESSION['note'] = 1;
          $content = "";
                          $countitem = 0;$nbChoice = 0;
                          if (isset($_SESSION['XmlFichier'][$compteur]['questestinterop']['item']['presentation']))
                          {//  Q/Sect/Itm // Dey ici le problème du FLOW
                               if (isset($_SESSION['XmlFichier'][$compteur]['questestinterop']['item']['presentation']['flow']['response_lid']['render_choice'])
                                   || isset($_SESSION['XmlFichier'][$compteur]['questestinterop']['item']['presentation']['response_lid']['render_choice']))
                                      $nbChoice++;
                               if ($nbChoice == 1 && $_SESSION['Param'] == 0)
                               {
                                   $NewIdQcm = ImpQti::ParamOk($ddj);
                               }
                               $Item = $_SESSION['XmlFichier'][$compteur]['questestinterop']['item'];
                               $comptageItems++;
                               $compteurItemTotal++;
                               if (isset($_SESSION['XmlFichier'][$compteur]['questestinterop']['item']['presentation']['flow'])
                                   || isset($_SESSION['XmlFichier'][$compteur]['questestinterop']['item']['presentation']))
                                          $ManageItem = ImpQti::GereItem($Item,$ddj,$NewIdQcm,$compteurItemTotal);
                          }//fin Q/I unique
                          elseif(isset($_SESSION['XmlFichier'][$compteur]['questestinterop']['item'][0]['presentation']))
                          {
                                 while (isset($_SESSION['XmlFichier'][$compteur]['questestinterop']['item'][$countitem]))
                                 {//  Q/Sect/Itm +
                                      if (isset($_SESSION['XmlFichier'][$compteur]['questestinterop']['item'][$countitem]['presentation']['flow']['response_lid']['render_choice'])
                                         || isset($_SESSION['XmlFichier'][$compteur]['questestinterop']['item'][$countitem]['presentation']['response_lid']['render_choice']))
                                             $nbChoice++;
                                      if ($nbChoice == 1 && $_SESSION['Param'] == 0)
                                      {
                                          $NewIdQcm = ImpQti::ParamOk($ddj);
                                      }
                                      $Item = $_SESSION['XmlFichier'][$compteur]['questestinterop']['item'][$countitem];
                                      $comptageItems++;
                                      $compteurItemTotal++;
                                      //$ManageItem = ImpQti::GereItem($Item,$ddj,$NewIdQcm,$compteurItemTotal);
                                      if (isset($_SESSION['XmlFichier'][$compteur]['questestinterop']['item'][$countitem]['presentation']['flow'])
                                          || isset($_SESSION['XmlFichier'][$compteur]['questestinterop']['item'][$countitem]['presentation']))
                                          $ManageItem = ImpQti::GereItem($Item,$ddj,$NewIdQcm,$compteurItemTotal);
                                      $countitem++;
                                  }
                          }
         return $NewIdQcm;
    }
    public static function ParamOk($ddj)
    {
       GLOBAL $connect;
       $_SESSION['Param'] = 1;
       $NewIdQcm =  Donne_ID ($connect,"SELECT max(ordre) from qcm_param");
       $titreQcm = "A_Qti12_".$ddj;
       $NewQcm =  mysql_query ("insert into qcm_param (ordre,qcm_auteur_no,n_pages,duree,titre_qcm) values('".$NewIdQcm."','".
                                  $_SESSION['id_user']."','1','15',\"".NewHtmlentities(utf8_decode($titreQcm),ENT_QUOTES)."\")");
       return $NewIdQcm;
    }
    public static function GereItem($Item,$ddj,$NewIdQcm,$compteurItemTotal)
    {
       GLOBAL $connect,$passage;
        if (!empty($_SESSION['note']))
              $_SESSION['note'] = 0;
        $effacer = 0;
        $traiteItem = (isset($Item['presentation']['flow'])) ? $Item['presentation']['flow'] : $Item['presentation'];
        if(isset($traiteItem['response_lid'][0]) || isset($traiteItem['response_lid'][1]))
        {
               echo "Ce QTI est de type monoItem et pluriQuestions. Spécifique à QuestionMark. Il ne respecte pas l'organisation en Item du Quizz";
               return false;
        }
        if (!isset($traiteItem['response_lid']['render_choice']))
        {
               echo "<br>Cette question n'est pas à response simple ou multiple<br>";
               return false;
        }
        $typeItem = (!isset($traiteItem['response_lid']['attr']['rcardinality']) ||
           (isset($traiteItem['response_lid']['attr']['rcardinality']) &&
            strtolower($traiteItem['response_lid']['attr']['rcardinality']) == 'multiple')) ? 1 : 0;
        if (isset($traiteItem['response_lid']['render_choice']['response_label'][0]))
            $nbProp = count($traiteItem['response_lid']['render_choice']['response_label']);
        elseif (isset($traiteItem['response_lid']['render_choice']['flow_label'][0]))
            $nbProp = count($traiteItem['response_lid']['render_choice']['flow_label']);
        elseif (isset($traiteItem['response_lid'][0]))
        {
           echo "Ce QTI est de type monoItem et pluriQuestions. Spécifique à QuestionMark. Il ne respecte pas l'organisation en Item du Quizz";
           return false;
        }
        if (isset($traiteItem['material'][0]['mattext']['value']))
            $mattext = $traiteItem['material'][0]['mattext']['value'];
        elseif (isset($traiteItem['material'][1]['mattext']['value']))
            $mattext = $traiteItem['material'][1]['mattext']['value'];
        else
            $mattext = '';
        if (isset($traiteItem['material'][0]['matimage']))
            $matimage = $traiteItem['material'][0]['matimage'];
        elseif (isset($traiteItem['material'][1]['matimage']))
            $matimage = $traiteItem['material'][1]['matimage'];
        elseif (isset($traiteItem['material']['matimage']))
            $matimage = $traiteItem['material']['matimage'];
        elseif (isset($traiteItem['response_lid']['material']['matimage']))
            $matimage = $traiteItem['response_lid']['material']['matimage'];
        else
            $matimage = '';
        if (isset($traiteItem['material']['mattext'][0]))
        {
           $nbMattext = count($traiteItem['material']['mattext']);
           for ($mt = 0; $mt < $nbMattext; $mt++)
           {
              $titreQuestion .= $traiteItem['material']['mattext'][$mt]['value'].' ';
              if (isset($traiteItem['material']['matemtext'][$mt]))
                 $titreQuestion .= $traiteItem['material']['matemtext'][$mt]['value'].' ';
           }
        }
        elseif (isset($traiteItem['response_lid']['material']['mattext']))
           $titreQuestion = $traiteItem['response_lid']['material']['mattext']['value'];
        elseif (isset($Item['attr']['title']) && !isset($traiteItem['response_lid']['material']['mattext']) && $mattext != '')
           $titreQuestion = $mattext;
        elseif (isset($Item['attr']['title']) && !isset($traiteItem['response_lid']['material']['mattext']) && $mattext == '')
           $titreQuestion = $Item['attr']['title'];
        else
           $titreQuestion = $traiteItem['material']['mattext']['value'];
        if (isset($traiteItem['material']['mattext']) && !isset($traiteItem['material']['mattext'][0]))
           $titreQuestion = $traiteItem['material']['mattext']['value'];
        $image = 'non';
        $typ_img = '';
        //echo "<br>material" ;
        if (isset($traiteItem['response_lid']['material']['matimage']) ||
            isset($matimage['attr']['imagtype']) || isset($matimage['attr']['uri']))
        {
        //echo "ici et là ------------------------------------------------------------------------------------";
            if (isset($matimage['attr']['type']))
                  $typ_img = $matimage['attr']['type'];
            elseif(isset($matimage['attr']['imagtype']))
                  $typ_img = $matimage['attr']['imagtype'];
            if (isset($matimage['attr']['data']))
                  $ifImage = $matimage['attr']['data'];
            elseif (isset($matimage['attr']['uri']))
                  $ifImage = $matimage['attr']['uri'];
            if (isset($ifImage))
            {
        //echo "ici et pas là ------------------------------------------------------------------------------------";
                  list($extension, $nom) = ImpQti::getextension($ifImage);
                  if (empty($typ_img))
                     $typ_img = "image/$extension";
                  for ($i=0; $i<sizeof($_SESSION['list']); $i++)
                  {
                      if (strstr($_SESSION['list'][$i]["filename"],$ifImage))
                      {
                         $adrImg = $_SESSION['list'][$i]["filename"];
                         //echo "adrImg  $adrImg";
                         $destImg ="ressources/qcm_images/".$nom."_".$ddj.".".$extension;
                         copy($adrImg,$destImg);
                         chmod($destImg,0775);
                         $image = str_replace("ressources/","",$destImg);
                         //echo "<br>$adrImg ------ $destImg<BR>";
                      }
                  }
            }

        }
//echo "<p>nbProp => $nbProp et  titreQuestion => ".NewHtmlentities(utf8_decode($titreQuestion),ENT_QUOTES);
        $NewIdData =  Donne_ID ($connect,"SELECT max(qcm_data_cdn) from qcm_donnees");
        $NewQcm =  mysql_query ("insert into qcm_donnees (qcm_data_cdn,qcmdata_auteur_no,n_lignes,question,typ_img,image) values('".$NewIdData.
                        "','".$_SESSION['id_user']."','".$nbProp."','".NewHtmlentities(utf8_decode(strip_tags($titreQuestion)),ENT_QUOTES).
                        "','".$typ_img."','".$image."')");
        $NewIdLinker =  Donne_ID ($connect,"SELECT max(qcmlinker_cdn) from qcm_linker");
        $NewQcmLinker =  mysql_query ("insert into qcm_linker (qcmlinker_cdn,qcmlinker_param_no,qcmlinker_data_no,qcmlinker_number_no) ".
                                           "values('".$NewIdLinker."','".$NewIdQcm."','".$NewIdData."','1')");
        for ($i = 1;$i < ($nbProp+1); $i++)
        {
           $l = $i-1;
           $propos = $i."_prop";
           $val = $i."_val";
           if (isset($traiteItem['response_lid']['render_choice']['flow_label']))
           {
               $traiteResponse = $traiteItem['response_lid']['render_choice']['flow_label'][$l]['response_label'];
               $traitIdent = $traiteItem['response_lid']['render_choice']['flow_label'][$l]['response_label']['attr']['ident'];
           }
           elseif (isset($traiteItem['response_lid']['render_choice']['response_label'][$l]['flow_mat']))
           {
               $traiteResponse = $traiteItem['response_lid']['render_choice']['response_label'][$l]['flow_mat'];
               $traitIdent = $traiteItem['response_lid']['render_choice']['response_label'][$l]['attr']['ident'];
           }
           elseif (isset($traiteItem['response_lid']['render_choice']['response_label'][$l]['material']))
           {
               $traiteResponse = $traiteItem['response_lid']['render_choice']['response_label'][$l];
               $traitIdent = $traiteItem['response_lid']['render_choice']['response_label'][$l]['attr']['ident'];
           }
           if (!isset($traiteResponse['material']['mattext']) &&
               isset($traiteResponse['material']['matimage']))
           {
               $requete =  mysql_query("delete from qcm_donnees where qcm_data_cdn = '".$NewIdData."'");
               //echo "<br>".$traiteResponse['material']['matimage']['attr']['entityref']." --> image<br>";
              return false;
           }
           $props = $traiteResponse['material']['mattext']['value'];
//echo "<br>propos = <br>".NewHtmlentities(utf8_decode($props),ENT_QUOTES)."<br>";
//echo "<br> Varequal value = ".$Item['resprocessing']['respcondition'][0]['conditionvar']['varequal']['value'].
//     "traitIdent == $traitIdent<br>";
           $PointVal = 0;
           if (isset($Item['resprocessing']['respcondition'][0]['conditionvar']['varequal'][0]) ||
               isset($Item['resprocessing']['respcondition'][1]['conditionvar']['varequal'][0]))
           {
              $nbResCond = count($Item['resprocessing']['respcondition']);
              //echo "<br> nombre de conditions $nbResCond<br>";
              $RsP = 0;
              while (isset($Item['resprocessing']['respcondition'][$RsP]))
              {
                 if (isset($Item['resprocessing']['respcondition'][$RsP]['conditionvar']['varequal'][0]))
                 {
                     $vEq = 0;
                     While (isset($Item['resprocessing']['respcondition'][$RsP]['conditionvar']['varequal'][$vEq]))
                     {
                        $valResp = $i.'_val';
                        //echo "<br>valResp-----------$valResp ".strtolower($Item['resprocessing']['respcondition'][$RsP]['attr']['title'])." == 'correct'<br>";
                        if ($Item['resprocessing']['respcondition'][$RsP]['conditionvar']['varequal'][$vEq]['value'] == $traitIdent &&
                            ((isset($Item['resprocessing']['respcondition'][$RsP]['attr']['title']) &&
                             strtolower($Item['resprocessing']['respcondition'][$RsP]['attr']['title']) == 'correct') ||
                             !isset($Item['resprocessing']['respcondition'][$RsP]['attr']['title'])))
                        {
                            //echo "<br> trait Ident  $traitIdent  et valeur=".$Item['resprocessing']['respcondition'][$RsP]['conditionvar']['varequal'][$vEq]['value']."<br>";
                            $PointVal = 1;
                            $_SESSION["note"] += $PointVal; echo "<br>PointVal = $PointVal; la note est de ...".$_SESSION["note"]."<br>";
                            $requete = mysql_query("update qcm_donnees set ".$valResp." = '".$PointVal."' where qcm_data_cdn = '".$NewIdData."'");
                            //echo "<br>--------------update qcm_donnees set ".$valResp." = '".$PointVal."' where qcm_data_cdn = '".$NewIdData."'<br>";
                            //continue;
                        }
                        $vEq++;
                     }
                  }
                  elseif (isset($Item['resprocessing']['respcondition'][$RsP]['conditionvar']['varequal']['value']) &&
                          $Item['resprocessing']['respcondition'][$RsP]['conditionvar']['varequal']['value'] == $traitIdent)
                  {
                        $valResp = $i.'_val';
                        //echo "<br> trait Ident  $traitIdent  et valeur=".$Item['resprocessing']['respcondition'][$RsP]['conditionvar']['varequal']['value']."<br>";
                        $PointVal = 1;
                        $_SESSION["note"] += $PointVal; echo "<br>PointVal = $PointVal; la note est de ...".$_SESSION["note"]."<br>";
                        $requete = mysql_query("update qcm_donnees set ".$valResp." = '".$PointVal."' where qcm_data_cdn = '".$NewIdData."'");
                        //echo "<br>c'est plutot là----------update qcm_donnees set ".$valResp." = '".$PointVal."' where qcm_data_cdn = '".$NewIdData."'<br>";
                  }
                  $RsP++;
               }
               $requete = mysql_query("update qcm_donnees set ".$propos." = \"".NewHtmlentities(utf8_decode($props),ENT_QUOTES)."\" where qcm_data_cdn = '".$NewIdData."'");
               //echo "<br>update qcm_donnees set ".$propos." = \"".NewHtmlentities(utf8_decode($props),ENT_QUOTES)."\" where qcm_data_cdn = '".$NewIdData."'<br>";
           }
           elseif (isset($Item['resprocessing']['respcondition'][0]['conditionvar']['varequal']['value']) ||
                  isset($Item['resprocessing']['respcondition'][1]['conditionvar']['varequal']))
           {
              $nbResCond = count($Item['resprocessing']['respcondition']);
              //echo "<br> nombre de conditions $nbResCond<br>";
              $RsP = 0;
              while (isset($Item['resprocessing']['respcondition'][$RsP]))
              {
                  $valResp = $i.'_val';
                  if ($Item['resprocessing']['respcondition'][$RsP]['conditionvar']['varequal']['value'] == $traitIdent &&
                      !isset($Item['resprocessing']['respcondition'][$RsP]['setvar']) &&
                      isset($Item['resprocessing']['respcondition'][$RsP]['displayfeedback']['attr']['linkrefid']) &&
                      strtolower($Item['resprocessing']['respcondition'][$RsP]['displayfeedback']['attr']['linkrefid']) == 'correct')
                  {
                     $PointVal = 1;
                     if (!isset($_SESSION["note"])) $_SESSION["note"] = 0;
                     $_SESSION["note"] += $PointVal;
                     $requete = mysql_query("update qcm_donnees set ".$valResp." = '".$PointVal."' where qcm_data_cdn = '".$NewIdData."'");
                     //echo "<br>" .$Item['resprocessing']['respcondition'][$RsP]['conditionvar']['varequal']['value']." == $traitIdent<br> ".
                     //"c'est là------------update qcm_donnees set ".$valResp." = '".$PointVal."' where qcm_data_cdn = '".$NewIdData."'<br>";
                  }
                  elseif ($Item['resprocessing']['respcondition'][$RsP]['conditionvar']['varequal']['value'] == $traitIdent &&
                          (isset($Item['resprocessing']['respcondition'][$RsP]['setvar']) &&
                          ((isset($Item['resprocessing']['respcondition'][$RsP]['setvar']['attr']['varname']) &&
                          strtolower($Item['resprocessing']['respcondition'][$RsP]['setvar']['attr']['varname']) == 'correct') ||
                          $Item['resprocessing']['respcondition'][$RsP]['setvar']['value'] > 0 ||
                          strtolower($Item['resprocessing']['respcondition'][$RsP]['setvar']['value']) == 'true')))
                  {
                     $PointVal = 1;
                     if (!isset($_SESSION["note"])) $_SESSION["note"] = 0;
                     $_SESSION["note"] += $PointVal;
                     $requete = mysql_query("update qcm_donnees set ".$valResp." = '".$PointVal."' where qcm_data_cdn = '".$NewIdData."'");
                     //echo "<br> c'est ici---------------update qcm_donnees set ".$valResp." = '".$PointVal."' where qcm_data_cdn = '".$NewIdData."'<br>";
                  }
                  elseif ($Item['resprocessing']['respcondition'][$RsP]['conditionvar']['varequal']['value'] == $traitIdent &&
                          isset($Item['resprocessing']['respcondition'][$RsP]['setvar'][0]))
                  {
                     $stv=0;
                     while (isset($Item['resprocessing']['respcondition'][$RsP]['setvar'][$stv]))
                     {
                     //echo "<br> aaaaaaaa------".$Item['resprocessing']['respcondition'][$RsP]['setvar'][$stv]['value']."--------aaaaaa<br>";
                        if ((isset($Item['resprocessing']['respcondition'][$RsP]['setvar'][$stv]['attr']['varname']) &&
                            strtolower($Item['resprocessing']['respcondition'][$RsP]['setvar'][$stv]['attr']['varname']) == 'correct') ||
                            $Item['resprocessing']['respcondition'][$RsP]['setvar'][$stv]['value'] > 0 ||
                            strtolower($Item['resprocessing']['respcondition'][$RsP]['setvar'][$stv]['value']) == 'true')
                        {
                              $PointVal = 1;
                              $passage++;
                              if (!isset($_SESSION["note"])) $_SESSION["note"] = 0;
                              $_SESSION["note"] += $PointVal;
                              $requete = mysql_query("update qcm_donnees set ".$valResp." = '".$PointVal."' where qcm_data_cdn = '".$NewIdData."'");
                              //echo "<br> c'est plutot---------------update qcm_donnees set ".$valResp." = '".$PointVal."' where qcm_data_cdn = '".$NewIdData."'<br>";
                              $stv++;
                        }
                        $stv++;
                     }
                  }
                  $RsP++;
               }
               $requete = mysql_query("update qcm_donnees set ".$propos." = \"".NewHtmlentities(utf8_decode($props),ENT_QUOTES)."\" where qcm_data_cdn = '".$NewIdData."'");
           }
           elseif (isset($Item['resprocessing']['respcondition']['conditionvar']['varequal']['value']))
           {
              if (!isset($Item['resprocessing']['respcondition']['setvar']) &&
                  $Item['resprocessing']['respcondition']['conditionvar']['varequal']['value'] == $traitIdent &&
                 isset($Item['resprocessing']['respcondition']['displayfeedback']['attr']['linkrefid']) &&
                 strtolower($Item['resprocessing']['respcondition']['displayfeedback']['attr']['linkrefid']) == 'correct')
              {
                 $PointVal = 1;
                 if (!isset($_SESSION["note"])) $_SESSION["note"] = 0;
                 $_SESSION["note"] += $PointVal;
              }
              elseif (isset($Item['resprocessing']['respcondition']['setvar']) &&
                     $Item['resprocessing']['respcondition']['conditionvar']['varequal']['value'] == $traitIdent  &&
                     ((isset($Item['resprocessing']['respcondition']['setvar']['attr']['varname']) &&
                     strtolower($Item['resprocessing']['respcondition']['setvar']['attr']['varname']) == 'correct') ||
                     $Item['resprocessing']['respcondition']['setvar']['value'] > 0 ||
                     strtolower($Item['resprocessing']['respcondition']['setvar']['value']) == 'true'))
              {
                 $PointVal = 1;
                 if (!isset($_SESSION["note"])) $_SESSION["note"] = 0;
                 $_SESSION["note"] += $PointVal;
              }
              $requete = mysql_query("update qcm_donnees set ".$propos." = \"".NewHtmlentities(utf8_decode($props),ENT_QUOTES)."\", ".$val." = '".$PointVal."' where qcm_data_cdn = '".$NewIdData."'");
              //echo "<br>update qcm_donnees set ".$propos." = \"".NewHtmlentities(utf8_decode($props),ENT_QUOTES)."\",".$val." = '".$PointVal."' where qcm_data_cdn = '".$NewIdData."'<br>";
           }
           elseif (!isset($Item['resprocessing']['respcondition']))
           {
               $requete = mysql_query("delete from qcm_donnees where qcm_data_cdn = '".$NewIdData."'");
               $requete = mysql_query("delete from qcm_linker where qcmlinker_data_no = '".$NewIdData."'");
               $effacer = 1;
           }
           //if ($effacer == 0)
              //$requete = mysql_query("update qcm_donnees set ".$propos." = \"".NewHtmlentities(utf8_decode($props),ENT_QUOTES)."\", ".$val." = '".$PointVal."' where qcm_data_cdn = '".$NewIdData."'");
        }
        if ($effacer == 0)
             $requete = mysql_query("update qcm_donnees set note = '1', multiple = '".$typeItem."' where qcm_data_cdn = '".$NewIdData."'");
        // Réorganisation de la table QCM_DONNEES
        $requete = mysql_query("select * from qcm_linker where qcmlinker_param_no = '".$NewIdQcm."' order by qcmlinker_cdn");
        if ($requete == true && mysql_num_rows($requete) > 0)
        {
          $nbQuest = mysql_num_rows($requete);
          for($i = 1; $i < ($nbQuest+1);$i++)
          {
             $l = $i-1;
             $idUpdt = mysql_result($requete,$l,'qcmlinker_cdn');
             $req_updt = mysql_query("update qcm_linker set qcmlinker_number_no = '".$i."' where qcmlinker_cdn = '".$idUpdt."'");
          }
          $req_updt = mysql_query("update qcm_param set n_pages = '".$nbQuest."' where ordre = '".$NewIdQcm."'");
        }
        return true;
//echo "<br>nbProp ===> $nbProp et compteur $compteurItemTotal et typeItem $typeItem<br>";
        return $NewIdQcm;
    }

    public static function getextension($fichier)
    {
      $bouts = explode(".", $fichier);
      return array(array_pop($bouts), implode(".", $bouts));
    }
}
?>