<?php
if (!isset($_SESSION)) session_start();
if (!isset($_SESSION['id_user']) || $_SESSION['id_user'] == "")
{
  exit();
}
include "include/UrlParam2PhpVar.inc.php";
require 'admin.inc.php';
require "lang$lg.inc.php";
require 'fonction.inc.php';
require "fonction_html.inc.php";
require "class/ClassImg.php";
require "class/ClassQti_12.php";
require "class/ClassQti_2X.php";
require "class/export_xml.php";
//include ("click_droit.txt");
dbConnect();
if (!empty($_GET['linker']) && $_GET['linker'] != '')
{
  unset($_SESSION['QcmTime']);
  unset($_SESSION['TabQcm']);
  unset($_SESSION['moyenneQcm']);
  unset($_SESSION['FileQcm']);
  ForceFileDownload(urldecode($_GET['linker']),'zip');
  exit();
}
if ($lg == "fr")
   setlocale(LC_TIME,'fr_FR');
elseif($lg == "ru")
   setlocale(LC_TIME,'ru_RU');
$date_cour = date ("Y-n-d");
$ch_dt= explode ("-",$date_cour);
$dte = strftime("%A %e %B %Y", mktime(0, 0, 0, $ch_dt[1],$ch_dt[2],$ch_dt[0]));
$auteur = modif_nom($_SESSION['prename_user'])." ".modif_nom($_SESSION['name_user']);
$_SESSION['QcmTime'] = time();
unset($_SESSION['TabQcm']);
$dirQti = ($version == 12) ? 'Export_Qti_12' : 'Export_Qti_20';
$handle=opendir($dirQti);
while ($fiche = readdir($handle))
{
   list($extension, $nom) = getextension($fiche);
   if ($fiche != '.' && $fiche != '..' && strstr($fiche,'.') && $fiche != 'Qplayer_config.xml' &&
      (strtolower($extension) == 'zip' || strtolower($extension) == 'xml' ||
       strtolower($extension) == 'png' || strtolower($extension) == 'gif' || strtolower($extension) == 'jpg'))
             unlink ($dirQti."/".$fiche);
}
closedir($handle);
$nomQcm = array();
// Recherche au sein de la base QCM des enregistrements relevant des paramètres numero_qcm et code
$reqQcm = mysql_query("SELECT * FROM qcm_param,qcm_donnees,qcm_linker WHERE
                      ordre='$code' and ordre = qcmlinker_param_no and
                      qcm_data_cdn = qcmlinker_data_no order by qcmlinker_number_no");
if (mysql_num_rows($reqQcm) > 0)
{
   $compteur = 0;
   $TabQcm = array();
   while($itemQcm = mysql_fetch_object($reqQcm))
   {
        array_push($TabQcm,$itemQcm);
        $TabQcm[$compteur]->REF = "MCQ_".$_SESSION['QcmTime']."_".$compteur;
        $TabQcm[$compteur]->question = str_replace('&quot;','',$TabQcm[$compteur]->question);
        $TabQcm[$compteur]->question = str_replace('"','',$TabQcm[$compteur]->question);
        for ($i=1; $i < 11; $i++)
        {
                $propos = $i."_prop";
                $TabQcm[$compteur]->$propos = str_replace('"','',$TabQcm[$compteur]->$propos);
                $TabQcm[$compteur]->$propos = str_replace('&quot;','',$TabQcm[$compteur]->$propos);
        }

        $compteur++;
   }
}
if (!empty($TabQcm[0]->question))
   $_SESSION['TabQcm'] = $TabQcm;

$nom_auteur = GetDataField ($connect,"select util_nom_lb from utilisateur where util_cdn='".$_SESSION['TabQcm'][0]->qcm_auteur_no."'","util_nom_lb");
$prenom_auteur = GetDataField ($connect,"select util_prenom_lb from utilisateur where util_cdn='".$_SESSION['TabQcm'][0]->qcm_auteur_no."'","util_prenom_lb");
$author = modif_nom($prenom_auteur).' '. modif_nom($nom_auteur);
//---------------------------------------------------------------------------------------------------------
$content = array();
switch ($version)
{
    case 12 :
         for ($i = 0;$i < $compteur; $i++)
         {
              echo "<br>Question n°".($i+1)." : ".$_SESSION['TabQcm'][$i]->question."<br>\n";
              if ($_SESSION['TabQcm'][$i]->typ_img != '')
              {
                  if (strstr($_SESSION['TabQcm'][$i]->typ_img,"jp"))
                     echo "<img src='ressources/".$_SESSION['TabQcm'][$i]->image."'><br>";
                  $fichier = "ressources/".$_SESSION['TabQcm'][$i]->image;
                  $tabDim = getimagesize($fichier);
                  list($extension, $nom) = getextension($fichier);
                  if (!$extension || ($extension != '' && $extension != 'jpeg' && $extension != 'jpg' && $extension != 'gif' && $extension != 'png'))
                      $_SESSION['TabQcm'][$i]->typ_img = '';
                  else
                  {
                    $dest_image = str_replace("ressources/","",str_replace("qcm_images/","",$nom)).".".$extension;
                    copy($fichier,$dirQti."/".$dest_image);
                    chmod($dirQti."/".$dest_image,0777);

                    $Image = new Image($fichier);
                    if ($tabDim[0] >= $tabDim[1] && $tabDim[0] > 300)
                        $Image->width(300);
                    elseif ($tabDim[1] >= $tabDim[0] && $tabDim[1] > 180)
                        $Image->height(180);
                    else
                        $Image->width($tabDim[0]);
                    $Image->save();
                  }

              }
              $content[$i] = expQti12::enteteXmlQtiMC($dte,$auteur,$typ_user,$adresse_http);
              $content[$i] .= expQti12::IntroQtiMC($i);
              $content[$i] .= expQti12::QuestTxt($i);
              $content[$i] .= expQti12::ReponsesQcm($i);
              $content[$i] .= expQti12::FeedbackQcm($i);
              $LnQ = $i+1;
              $nomQcm[$i] = 'Qcm_'.($LnQ).'.xml';
              $_SESSION['FileQcm'][$i] = $nomQcm[$i];
              echo $content[$i];
              $dirXML = $dirQti."/".$nomQcm[$i];
              $fp = fopen($dirXML, "w+");
                  $fw = fwrite($fp, $content[$i]);
              fclose($fp);
              chmod ($dirXML, 0777);
         }
         $contentM = expQti12::enteteXmlQtiMC($dte,$auteur,$typ_user,$adresse_http);
         $contentM .= expQti12::IntroManifestQtiMC($compteur);
         $dirMf = $dirQti.'/'.'Qmanifest.xml';
         $fp = fopen($dirMf, "w+");
            $fw = fwrite($fp, $contentM);
         fclose($fp);
         chmod ($dirMf, 0777);
         $contentScorm = exp_xml::entete_manifest($dte,$auteur,$typ_user,$adresse_http);
         $contentScorm .= expQti12::ScormEntete($compteur,$author);
         $dirScorm = $dirQti.'/'.'imsmanifest.xml';
         $fp = fopen($dirScorm, "w+");
               $fw = fwrite($fp, $contentScorm);
         fclose($fp);
         chmod ($dirScorm, 0777);
         include_once("class/archive.inc.php");
         $dossier = $dirQti;
         $zipper = new zip_file("scormQti_".$_SESSION['QcmTime'].".zip");
         $zipper->set_options(array('basedir'=>$dossier));
         $handle=opendir($dossier);
         while ($fiche = readdir($handle))
         {
                if ($fiche != '.' && $fiche != '..')
                $zipper->add_files($fiche);
         }
         closedir($handle);
         $zipper->create_archive();
         chmod($dirQti."/scormQti_".$_SESSION['QcmTime'].".zip",0775);
         $linker = $dirQti."/scormQti_".$_SESSION['QcmTime'].".zip";
         echo "<script language='JavaScript'>";
                  echo "setTimeout(\"document.location.replace('export_Qti.php?linker=".urlencode($linker)."')\",1500);";
         echo "</script>";
         break;
    case '20' :
         for ($i = 0;$i < $compteur; $i++)
         {
              echo "<br>Question n°".($i+1)." : ".$_SESSION['TabQcm'][$i]->question."<br>\n";
              if ($_SESSION['TabQcm'][$i]->typ_img != '')
              {
                  echo "<img src='ressources/".$_SESSION['TabQcm'][$i]->image."'><br>";
                  $fichier = "ressources/".$_SESSION['TabQcm'][$i]->image;
                  $tabDim = getimagesize($fichier);
                  list($extension, $nom) = getextension($fichier);
                  if (!$extension || ($extension != '' && $extension != 'jpeg' && $extension != 'jpg' && $extension != 'gif' && $extension != 'png'))
                      $_SESSION['TabQcm'][$i]->typ_img = '';
                  else
                  {
                    $dest_image = str_replace("ressources/","",str_replace("qcm_images/","",$nom)).".".$extension;
                    copy($fichier,$dirQti."/".$dest_image);
                    chmod($dirQti."/".$dest_image,0777);

                    $Image = new Image($fichier);
                    if ($tabDim[0] >= $tabDim[1] && $tabDim[0] > 300)
                        $Image->width(300);
                    elseif ($tabDim[1] >= $tabDim[0] && $tabDim[1] > 180)
                        $Image->height(180);
                    else
                        $Image->width($tabDim[0]);
                    $Image->save();
                  }

              }
              $LeType = ($_SESSION['TabQcm'][$i]->multiple == 0) ? "choice" : "choiceMultiple";
              if ($LeType == "choiceMultiple")
                 $content[$i] = expQti20::MultipleChoice($i);
              else
                 $content[$i] = expQti20::SingleChoice($i);
              $LnQ = $i+1;
              $nomQcm[$i] = 'Qcm_'.($LnQ).'.xml';
              $_SESSION['FileQcm'][$i] = $nomQcm[$i];
              echo $content[$i];
              $dirXML = $dirQti."/".$nomQcm[$i];
              $fp = fopen($dirXML, "w+");
                  $fw = fwrite($fp, $content[$i]);
              fclose($fp);
              chmod ($dirXML, 0777);
         }
         $contentMfst = expQti20::enteteXmlQtiMC($dte,$auteur,$typ_user,$adresse_http);
         $contentMfst .= expQti20::EnteteManifest($compteur,$author);
         $contentMfst .= expQti20::RessManifest($compteur,$author);
         $dirMfst = $dirQti.'/'.'imsmanifest.xml';
         $fp = fopen($dirMfst, "w+");
               $fw = fwrite($fp, $contentMfst);
         fclose($fp);
         chmod ($dirMfst, 0777);
         include_once("class/archive.inc.php");
         $dossier = $dirQti;
         $zipper = new zip_file("Qti-20_".$_SESSION['QcmTime'].".zip");
         $zipper->set_options(array('basedir'=>$dossier));
         $handle=opendir($dossier);
         while ($fiche = readdir($handle))
         {
                if ($fiche != '.' && $fiche != '..')
                $zipper->add_files($fiche);
         }
         closedir($handle);
         $zipper->create_archive();
         chmod($dirQti."/Qti-20_".$_SESSION['QcmTime'].".zip",0775);
         $linker = $dirQti."/Qti-20_".$_SESSION['QcmTime'].".zip";
         echo "<script language='JavaScript'>";
                  echo "setTimeout(\"document.location.replace('export_Qti.php?linker=".urlencode($linker)."')\",1500);";
         echo "</script>";
         break;
}
//echo "<pre>";print_r($_SESSION['TabQcm']);echo "</pre>";
function getextension($fichier)
{
  $bouts = explode(".", $fichier);
  return array(array_pop($bouts), implode(".", $bouts));
}
?>