<?php
if (!isset($_SESSION)) session_start();
include ("include/UrlParam2PhpVar.inc.php");
if (!isset($_SESSION['id_user']) || $_SESSION['id_user'] == "")
{
  exit();
}
require "admin.inc.php";
require 'fonction.inc.php';
require 'fonction_html.inc.php';
require "lang$lg.inc.php";
require "langues/xml.inc.php";
dbConnect();
//include 'style.inc.php';
if ($lg == "fr")
   setlocale(LC_TIME,'fr_FR');
elseif($lg == "ru")
   setlocale(LC_TIME,'ru_RU');
$date_cour = date ("Y-n-d");
$ch_dt= explode ("-",$date_cour);
$dte = strftime("%A %e %B %Y", mktime(0, 0, 0, $ch_dt[1],$ch_dt[2],$ch_dt[0]));
$queryP = requete("*","parcours","parcours_cdn = $id_parc");
$queryPS = requete_order("seqparc_seq_no","sequence_parcours","seqparc_parc_no = $id_parc","seqparc_ordre_no");
if ($queryPS == false)
{
  echo $msq_noseq_parc;
  exit;
}
$dir="export_manifest/ressources";
$handle=opendir($dir);
while ($fiche = readdir($handle))
{
   list($extension, $nom) = getextension($fiche);
   if ($fiche != '.' && $fiche != '..' && $fiche != 'index.htm' &&
       $fiche != 'index_blanc.htm' && $fiche != 'index_presentiel.htm')
         unlink ($dir."/".$fiche);
      unlink ("export_manifest/imsmanifest.xml");
}
while ($item = mysql_fetch_object($queryP))
{
      $auteur_parc_no = $item->parcours_auteur_no;
      $NomParcours = html_entity_decode($item->parcours_nom_lb,ENT_QUOTES,'iso-8859-1');
      $DescParcours = html_entity_decode($item->parcours_desc-cmt,ENT_QUOTES,'iso-8859-1');
      $nom_aut_parc = GetDataField ($connect,"select util_nom_lb from utilisateur where util_cdn = '$auteur_parc_no'","util_nom_lb");
      $prenom_aut_parc = GetDataField ($connect,"select util_prenom_lb from utilisateur where util_cdn = '$auteur_parc_no'","util_prenom_lb");
      $nom_user = GetDataField ($connect,"select util_nom_lb from utilisateur where util_cdn = '$id_user'","util_nom_lb");
      $prenom_user = GetDataField ($connect,"select util_prenom_lb from utilisateur where util_cdn = '$id_user'","util_prenom_lb");
      $auteur_parc = $nom_user." ".$prenom_user;
      $vcard_parc = $nom_aut_parc." ".$prenom_aut_parc;
      $creation_parc = $item->parcours_create_dt;
      $modif_parc = $item->parcours_modif_dt;
      $date_creat_parc = ($modif_parc != '0000-00-00') ? $modif_parc : $creation_parc;

      require("class/export_xml.php");
      $lentete = new exp_xml;
      $afficher = $lentete->entete_manifest($dte,$auteur_parc,$typ_user,$adresse_http);
      $contenuExp .= $afficher;
      $contenuExp .= "<organizations default=\"M0\">\n".
                     "<organization identifier=\"M0\">\n".
                     "<title>".utf8_encode(strip_tags($NomParcours))."</title>\n";
      $contenuExp .= "<item identifier=\"M0_01\" isvisible=\"true\">\n".
                         "<title>".utf8_encode(strip_tags($NomParcours))."</title>\n";
      while ($item2 = mysql_fetch_object($queryPS))
      {
          $queryS = requete("*","sequence","seq_cdn = ".$item2->seqparc_seq_no);
          while ($item1 = mysql_fetch_object($queryS))
          {
             $mS++;
             $auteur_seq_no = $item1->seq_auteur_no;
             $TitreSeq = html_entity_decode($item1->seq_titre_lb,ENT_QUOTES,'iso-8859-1');
             $DescSeq = html_entity_decode($item1->seq_desc_cmt,ENT_QUOTES,'iso-8859-1');
             $MotCleSeq = html_entity_decode($item1->seq_mots_clef,ENT_QUOTES,'iso-8859-1');
             $nom_aut_seq = GetDataField ($connect,"select util_nom_lb from utilisateur where util_cdn = '$auteur_seq_no'","util_nom_lb");
             $prenom_aut_seq = GetDataField ($connect,"select util_prenom_lb from utilisateur where util_cdn = '$auteur_seq_no'","util_prenom_lb");
             $vcard_seq = $nom_aut_seq." ".$prenom_aut_seq;
             $creation_seq = $item1->seq_create_dt;
             $modif_seq = $item1->seq_modif_dt;
             $date_creat_seq = ($modif_seq != '0000-00-00') ? $modif_seq : $creation_seq;
             $contenuExp .= "<item identifier=\"M0_01$mS\" isvisible=\"true\">\n".
                         "<title>".utf8_encode(trim(str_replace("\"","-",str_replace("&nbsp;"," ",strip_tags($TitreSeq)))))."</title>\n";
             $queryA =  requete_order("*","activite","act_seq_no = ".$item2->seqparc_seq_no,"act_ordre_nb");
             $mA = 0;
             while ($item3 = mysql_fetch_object($queryA))
             {
                   unset($ressOk);
                   $contenuMetaAct = "";
                   $contenuFileAct = "";
                   $ress = "";
                   $mA++;
                   $id_act = $item3->act_cdn;
                   $id_ress = $item3->act_ress_no;
                   $auteur_act_no = $item3->act_auteur_no;
                   $titreAct = html_entity_decode($item3->act_nom_lb,ENT_QUOTES,'iso-8859-1');
                   $ConsigneActif = clean_text($item3->act_consigne_cmt);
                   $ConsigneAct = html_entity_decode($ConsigneActif,ENT_QUOTES,'iso-8859-1');
                   $nom_aut_act = GetDataField ($connect,"select util_nom_lb from utilisateur where util_cdn = '$auteur_act_no'","util_nom_lb");
                   $prenom_aut_act = GetDataField ($connect,"select util_prenom_lb from utilisateur where util_cdn = '$auteur_act_no'","util_prenom_lb");
                   $vcard_act = $nom_aut_act." ".$prenom_aut_act;
                   $creation_act = $item3->act_create_dt;
                   $modif_act = $item3->act_modif_dt;
                   $date_creat_act = ($modif_act != '0000-00-00') ? $modif_act : $creation_act;
                   $Identite = "R_S01".$mS.$mA;
                   $contenuExp .= "<item identifier=\"M0_01".$mS.$mA."\" identifierref=\"R_S01".$mS.$mA."\" isvisible=\"true\">\n".
                                   "<title>".utf8_encode(trim(str_replace("\"","-",str_replace("&nbsp;"," ",strip_tags($titreAct)))))."</title>\n";
                   if ($item3->act_ress_no > 0 && $item3->act_ress_on == "OUI")
                   {
                      $ress = GetDataField ($connect,"select ress_url_lb from ressource_new where ress_cdn = ".$item3->act_ress_no,"ress_url_lb");
                      $TabFile = explode('/',$ress);
                      $NbrTab = count($TabFile);
                      $fichier = $TabFile[$NbrTab-1];
                      list($extension, $nom) = getextension($fichier);
                      if (!in_array(strtolower($extension), array("html","shtml","phtml","htm","php","asp","cgi")) &&
                         (strstr($ress,$adresse_http.'/ressources/') || substr($ress,0,strlen('ressources/'))))
                         {
                           $adrs = str_replace($adresse_http.'/','',$ress);
                           if (file_exists($adrs))
                           {
                              copy($adrs,"export_manifest/ressources/$fichier");
                              chmod("export_manifest/ressources/$fichier",0775);
                              $ress = "ressources/$fichier";
                           }
                         }
                      $ressOk = 1;
                   }
                   elseif ($item3->act_ress_no == 0 && $item3->act_ress_on == "OUI")
                   {
                      if ($ConsigneAct == "")
                         $ress = "ressources/index_blanc.htm";
                      else
                         $ress = createFile($ConsigneAct,$Identite);
                   }
                   elseif ($item3->act_ress_no == 0 && $item3->act_ress_on == "NON")
                   {
                      if ($ConsigneAct == "")
                         $ress = "ressources/index_presentiel.htm";
                      else
                         $ress = createFile($ConsigneAct,$Identite);
                   }
                   $contenuMetaAct = "<general>\n".
                            "<title>\n".
                            "<langstring><![CDATA[".utf8_encode(trim(str_replace("\"","-",str_replace("&nbsp;"," ",strip_tags($titreAct)))))."]]></langstring>\n".
                            "</title>\n".
                            "<description>\n".
                            "<langstring><![CDATA[".utf8_encode(trim(str_replace("\"","-",str_replace("&nbsp;"," ",strip_tags($ConsigneAct)))))."]]></langstring>\n".
                            "</description>\n".
                            "</general>\n".
                            "<lifecycle><version><langstring xml:lang=\"fr-FR\"></langstring></version>\n".
                            "<status><source><langstring xml:lang=\"x-none\">LOMv1.0</langstring></source>\n".
                            "<value><langstring xml:lang=\"x-none\">Final</langstring></value></status>\n".
                            "<contribute><role><source><langstring xml:lang=\"x-none\">LOMv1.0</langstring></source>\n".
                            "<value><langstring xml:lang=\"x-none\">".utf8_encode($mrc_aut)."</langstring></value></role>\n".
                            "<centity><vcard>begin:vcard fn: ".utf8_encode($vcard_act)." end:vcard</vcard></centity>\n".
                            "<date><datetime>".utf8_encode($date_creat_act)."</datetime>\n".
                            "<description><langstring xml:lang=\"fr-FR\">".utf8_encode($messxml_dtmod)."</langstring>\n".
                            "</description></date></contribute>\n".
                            "</lifecycle>\n".
                            "</lom>\n";
                   if ($item1->seq_ordreact_on == "OUI" && $mA > 1)
                   {
                      $mAvant = $mA-1;
                      $contenuExp .= "<adlcp:prerequisites type=\"aicc_script\"><![CDATA[M0_01".$mS.$mAvant."]]></adlcp:prerequisites>\n";
                   }
                   $numero =$mS.$mA;
                   $affiche_header = exp_xml ::header_act_location($ress);
                   $contenuFileAct .= $affiche_header;
                   $contenuFileAct .= $contenuMetaAct;
                   $dir_act = $dir."/R_S01$numero.xml";
                   $fp = fopen($dir_act, "w+");
                   $fw = fwrite($fp, $contenuFileAct);
                   fclose($fp);
                   chmod ($dir_act, 0775);
                   $afficher = exp_xml :: entete_act_manifest($numero,$id_act,$ress);
                   $contenuRess .= $afficher;
                   $affiche_suite = exp_xml :: suite_act_manifest($ress);
                   $contenuRess .= $affiche_suite;
                   $affiche_fin = exp_xml :: fin_act_manifest($ress);
                   $contenuRess .= $affiche_fin;
                   $duree_act = exp_xml :: modifie_time_xml($item3->act_duree_nb);
                   $contenuExp .= "<adlcp:maxtimeallowed>".utf8_encode($duree_act)."</adlcp:maxtimeallowed>\n";
                   $contenuExp .= "</item>\n";
             }
             $contenuSeq = "<general>\n".
                            "<title>\n".
                            "<langstring><![CDATA[".utf8_encode(trim(str_replace("\"","-",str_replace("&nbsp;"," ",$TitreSeq))))."]]></langstring>\n".
                            "</title>\n".
                            "<language>\nfr-Fr</language>\n".
                            "<description>\n".
                            "<langstring><![CDATA[".utf8_encode(trim(str_replace("\"","-",str_replace("&nbsp;"," ",$DescSeq))))."]]></langstring>\n".
                            "</description>\n".
                            "<keywords>\n".
                            "<langstring><![CDATA[".utf8_encode(trim(str_replace("\"","-",str_replace("&nbsp;"," ",$MotCleSeq))))."]]></langstring>\n".
                            "</keywords>\n".
                            "</general>\n".
                            "<lifecycle><version><langstring xml:lang=\"fr-FR\"></langstring></version>\n".
                            "<status><source><langstring xml:lang=\"x-none\">LOMv1.0</langstring></source>\n".
                            "<value><langstring xml:lang=\"x-none\">Final</langstring></value></status>\n".
                            "<contribute><role><source><langstring xml:lang=\"x-none\">LOMv1.0</langstring></source>\n".
                            "<value><langstring xml:lang=\"x-none\">".utf8_encode($mrc_aut)."</langstring></value></role>\n".
                            "<centity><vcard>begin:vcard fn: ".utf8_encode($vcard_seq)." end:vcard</vcard></centity>\n".
                            "<date><datetime>".utf8_encode($date_creat_seq)."</datetime>\n".
                            "<description><langstring xml:lang=\"fr-FR\">".utf8_encode($messxml_dtmod)."</langstring>\n".
                            "</description></date></contribute>\n".
                            "</lifecycle>\n".
                            "</lom>\n";
                            $numero =$mS;
                            $affiche_header =exp_xml :: header_act_location($ress);
                            $contenuFileSeq = $affiche_header;
                            $contenuFileSeq .= $contenuSeq;
                            $dir_seq = $dir."/R_S01$numero.xml";
                            $fp = fopen($dir_seq, "w+");
                            $fw = fwrite($fp, $contenuFileSeq);
                            fclose($fp);
                            chmod ($dir_seq, 0775);
             $contenuExp .= "<metadata>\n".
                            "<schema>ADL SCORM</schema>\n".
                            "<schemaversion>1.2</schemaversion>\n".
                            "<adlcp:location>R_S01$mS.xml</adlcp:location>\n".
                            "</metadata>".
                            "</item>\n";
          }
      }
      $contenuModule = "<general>\n".
                       "<title>\n".
                       "<langstring><![CDATA[".utf8_encode(trim(str_replace("\"","-",str_replace("&nbsp;"," ",$mess_tit_manifest))))."]]></langstring>\n".
                       "</title>\n".
                       "<language>\nfr-FR</language>\n".
                       "<description>\n".
                       "<langstring><![CDATA[".utf8_encode(trim(str_replace("\"","-",str_replace("&nbsp;"," ",$DescParcours))))."]]></langstring>\n".
                       "</description>\n".
                       "</general>\n".
                       "<lifecycle><version><langstring xml:lang=\"fr-FR\"></langstring></version>\n".
                       "<status><source><langstring xml:lang=\"x-none\">LOMv1.0</langstring></source>\n".
                       "<value><langstring xml:lang=\"x-none\">Final</langstring></value></status>\n".
                       "<contribute><role><source><langstring xml:lang=\"x-none\">LOMv1.0</langstring></source>\n".
                       "<value><langstring xml:lang=\"x-none\">".utf8_encode($mrc_aut)."</langstring></value></role>\n".
                       "<centity><vcard>begin:vcard fn: ".utf8_encode($vcard_parc)." end:vcard</vcard></centity>\n".
                       "<date><datetime>".utf8_encode($date_creat_parc)."</datetime>\n".
                       "<description><langstring xml:lang=\"fr-FR\">".utf8_encode($messxml_dtmod)."</langstring>\n".
                       "</description></date></contribute>\n".
                       "</lifecycle>\n".
                       "</lom>\n";
                       $affiche_header =exp_xml :: header_act_location("1");
                       $contenuFileMod = $affiche_header;
                       $contenuFileMod .= $contenuModule;
                       $dir_mod = $dir."/R_S01.xml";
                       $fp = fopen($dir_mod, "w+");
                       $fw = fwrite($fp, $contenuFileMod);
                       fclose($fp);
                       chmod ($dir_mod, 0775);
      $contenuExp .= "</item>\n".
                     "<metadata>\n".
                     "<schema>ADL SCORM</schema>\n".
                     "<schemaversion>1.2</schemaversion>\n".
                     "<adlcp:location>R_S01.xml</adlcp:location>\n".
                     "</metadata>\n".
                     "</organization>\n".
                     "</organizations>\n".
                     "<resources>\n";
      $contenuExp .= $contenuRess;
      $contenuExp .= "</resources>\n</manifest>";
}
$dir_app="export_manifest/imsmanifest.xml";
$fp = fopen($dir_app, "w+");
$fw = fwrite($fp, $contenuExp);
fclose($fp);
chmod ($dir_app, 0775);
include_once("class/archive.inc.php");
$dossier = "export_manifest";
$zipper = new zip_file("ressources/scorm_export.zip");
$zipper->set_options(array('basedir'=>$dossier));
$handle=opendir($dossier);
while ($fiche = readdir($handle))
{
   if ($fiche != '.' && $fiche != '..')
      $zipper->add_files($fiche);
}
closedir($handle);
$zipper->create_archive();
$linker = "export_manifest/ressources/scorm_export.zip";
ForceFileDownload($linker,'zip');
exit;
function getextension($fichier)
{
  $bouts = explode(".", $fichier);
  return array(array_pop($bouts), implode(".", $bouts));
}
function createFile($consigne,$Identite)
{
   GLOBAL $dir;
    $contentHTML = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<META HTTP-EQUIV="Expires" CONTENT="Fri, Jan 01 1900 00:00:00 GMT">
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta http-equiv="content-language" content="en">
<meta name="author" content="Formagri">
<META NAME="Creation_Date" CONTENT="08/15/2000">
<META HTTP-EQUIV="Pragma" CONTENT="no-cache">
<META HTTP-EQUIV="Cache-Control" CONTENT="no-cache">
<script language=javascript src="../APIWrapper.js"></script>
<script language=javascript src="../SCOFunctions.js"></script>
<title>Pas de lien vers une ressource</title>
</head>
<body>
</head>
<body onunload="return unloadPage(\'incomplete\')">
  <H4>'.clean_text($consigne).'</H4>
        <script language="javascript">
        loadPage();
        var   studentName = "!";
        var   lmsStudentName = doLMSGetValue(  "cmi.core.student_name" );

        if ( lmsStudentName  != "" )
        {
           studentName = " " + lmsStudentName +   "!";
        }

        document.write("Cliquer sur le bouton -Valider- pour enregistrer votre passage, " +studentName);
        </script>


        <hr>
        <form>
          <table cols="3" width="100%" align="center">
            <tr>
              <td  align="middle"><input type = "button" value = " '.
              ' Validez  " onClick = "doQuit(\'completed\')" id=button2 name=button2></td>
            </tr>
          </table>
        </form>
 </body>
</html>';
    $dir_app=$dir."/$Identite.html";
    $fp = fopen($dir_app, "w+");
      $fw = fwrite($fp, $contentHTML);
    fclose($fp);
    chmod ($dir_app, 0775);
    return "ressources/$Identite.html";
}
?>