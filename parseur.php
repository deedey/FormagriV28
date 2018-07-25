<?php
// basé sur des méthodes adoptées par :
// Claroline(http://www.claroline.nt) : importLearningPath.php,v 1.21.2.1 2005/06/10 08:44:38 mathieu Exp $
// Ganesha, (http://anemalab.org) et David Douillard.davidcliquot@mediarom.fr
// Moodle (http://www.moodle.org  : Communauté) $Id: lib.php,v 1.47.2.9 2006/02/03 08:51:13 bobopinna Exp $
if (!isset($_SESSION)) session_start();
include ("include/UrlParam2PhpVar.inc.php");
if (!isset($_SESSION['id_user']) || $_SESSION['id_user'] == "")
{
  exit();
}
require 'fonction.inc.php';
require 'graphique/admin.inc.php';
require "lang$lg.inc.php";
require 'class/class_module.php';
require 'class/Class_Rss.php';
require 'fonction_html.inc.php';
dbConnect();
//echo "<pre>";print_r($_POST);echo "</pre>";
$date_dujour = date ("Y-m-d");
include ('style.inc.php');
if (isset($prov) && $prov == "seq")
  $incl = "liste_seq.inc.php";
else
  $incl = "liste_parc.inc.php";
error_reporting (0);
if (isset($_FILES['file']['tmp_name']) && strstr(strtolower($_FILES['file']['name']),".zip") && $zip == 1)
{
    require('class/pclzip.inc.php');
    if ($_FILES['file']['name'] == "")
       $message = strtolower($mess_fichier_no);
    elseif(!is_file($_FILES['file']['tmp_name']))
       $message = $mess_fic_dep_lim." : ".ini_get('upload_max_filesize');
    if ($message != "")
    {
      echo "<CENTER><TABLE bgColor='#298CA0' cellspacing='2' cellpadding='0'><TR>";
      include ($incl);
      echo "<TD valign='top' width='70%' height='100%' bgcolor='#CEE6EC'>";
      echo "<TABLE cellspacing='1' cellpadding='0' width='100%'>";
      echo "<TR><TD background=\"images/fond_titre_table.jpg\" colspan='2' height='40' align='center' valign='center'>";
      echo "<Font size='3' color='#FFFFFF'><B>$mess_imp_sco</B></FONT></TD></TR>";
      echo "<TR height='50'><TD colspan='2' valign='center'><FONT size='2' color='red'><B>$message</B></font></TD><TR>";
      echo fin_tableau($html);
     exit;
    }
    $dir="ressources/".$login."_".$id_user."/ressources/";
    
    $nom = $_FILES['file']['name'];
    $nom_final= "Ressources_Scorm";
    $handle=opendir($dir);
    $i = 0;
    while ($fiche = readdir($handle))
    {
       if ($fiche == $nom_final)
         $i++;
    }
    if ($i == 0)
    {
       $create_rep = $dir."Ressources_Scorm";
       mkdir($create_rep,0775);
       chmod($create_rep,0775);
    }
    list($extension, $nom_rep) = getextension($_FILES['file']['name']);
    $dir = "ressources/".$login."_".$id_user."/ressources/Ressources_Scorm/";
    $fichier = $_FILES['file']['tmp_name'];
    $archive = new PclZip($fichier);
    if (($list = $archive->listContent()) == 0) 
    {
      die("Error : ".$archive->errorInfo(true));
    }
    $affiche_sco.= "<B>$mess_sco_imp : Ressources_Scorm/$nom_rep</B><P>";
    $accord=0;
    for ($i=0; $i<sizeof($list); $i++) {
        $affiche_sco.= "Fichier ".($i+1)." = ".$nom_rep."/".$list[$i]["filename"]."<BR>";
        if (strstr($list[$i]["filename"],"imsmanifest.xml") || strstr($list[$i]["filename"],"imsScormCam.xml")){
           $accord++;
           if ($accord == 1)
           {
              $le_manifeste = $nom_rep."/".$list[$i]["filename"];
           }
        }
    }
    $affiche_sco.= "<P>";
    $dest_file = $dir.$nom;
    if ($accord == 0)
       $message_no .= $mess_noManifest."<BR>";
    if (file_exists($dir.$nom))
       $message_no .= $mess_zip_exist;
    if (!file_exists($dir.$nom) && $accord > 0)
    {
      if (!file_exists($dir.$nom_rep))
      {
         mkdir($dir.$nom_rep,0775);
         chmod ($dir.$nom_rep,0775);
      }
      else
      {
         $nom_rep .="_$date_dujour";
         mkdir($dir.$nom_rep,0775);
         chmod ($dir.$nom_rep,0775);
      }
      $lerepertoire = $dir."$nom_rep/";
      $list = $archive->extract(PCLZIP_OPT_PATH,$lerepertoire,
                               PCLZIP_OPT_REMOVE_PATH,$dir,
                               PCLZIP_OPT_SET_CHMOD, 0775);
       $copier=move_uploaded_file($_FILES['file']['tmp_name'], $dest_file);
    }
    else
    {
      entete_concept($incl,$mess_imp_sco);
      echo "<TR height='50'><TD colspan='2' valign='center'><B>$message_no</B></TD></TR>";
      echo "<TR height='20'><TD colspan='2' valign='center'>&nbsp</TD></TR>";
      $file = "";
      echo "<TR><TD align=left><A HREF=\"javascript:history.go(-1);\" onmouseover=\"img_annonce.src='images/fiche_identite/boutretourb.gif';return true;\" onmouseout=\"img_annonce.src='images/fiche_identite/boutretour.gif'\">";
      echo "<IMG NAME=\"img_annonce\" SRC=\"images/fiche_identite/boutretour.gif\" BORDER='0' onLoad=\"tempImg=new Image(0,0); tempImg.src='images/fiche_identite/boutretourb.gif'\"></A>";
      echo fin_tableau($html);
     exit;
    }
}
elseif((!isset($_FILES['file']['tmp_name']) || !strstr(strtolower($_FILES['file']['name']),".zip")) && isset($zip) && $zip == 1)
{
    include ('style.inc.php');
    $poids = "4,5 Mo";
    if ($_FILES['file']['name'] == "")
       $message = $mess_fichier_no." <BR> &nbsp;&nbsp;et<BR> &nbsp;&nbsp;".strtolower($mess_fic_dep_lim)." $poids<BR>";
    elseif ($_FILES['file']['name'] != "")
       $message = "&nbsp;&nbsp;$mess_nozip";
    if ($message != "")
    {
      entete_concept($incl,$mess_imp_sco);
      echo "<TR height='50'><TD colspan='2' valign='center'><FONT size='2' color='red'><B>&nbsp;&nbsp;$message</B></font><BR></TD></TR>";
      echo "<TR height='20'><TD colspan='2' valign='center'>&nbsp</TD></TR>";
      echo "<TR><TD align=left><A HREF=\"javascript:history.go(-1);\" onmouseover=\"img_annonce.src='images/fiche_identite/boutretourb.gif';return true;\" onmouseout=\"img_annonce.src='images/fiche_identite/boutretour.gif'\">";
      echo "<IMG NAME=\"img_annonce\" SRC=\"images/fiche_identite/boutretour.gif\" BORDER='0' onLoad=\"tempImg=new Image(0,0); tempImg.src='images/fiche_identite/boutretourb.gif'\"></A>";
      echo fin_tableau($html);
     exit;
    }
}
if (((!isset($_FILES['file']['tmp_name']) && !isset($zip) && !isset($_POST['file'])) || (isset($_POST['file']) && !strstr($_POST['file'],"imsmanifest.xml") && !isset($zip))) && !isset($_GET['file'])){
    include ('style.inc.php');
    entete_concept($incl,$mess_imp_sco);
    echo "<TR height='10'><TD colspan='2' valign='top'>&nbsp;</TD><TR>";
    if (isset($_POST['file']) && !strstr($_POST['file'],"imsmanifest.xml"))
        echo "<TR height='30'><TD colspan='2' valign='top'><FONT color='red'><B>$mess_nofile_xml</B></TD><TR>";
    echo "<TR height='10'><TD colspan='2' valign='top'>&nbsp;</TD><TR>";
    echo "<TR><TD colspan='2' width='100%'>";
    echo "<TABLE cellspacing='0' cellpadding='4' width='100%'>";
    echo "<TR><TD colspan='2' valign='top' align='left'><TABLE width='50%'><TR><TD valign='center'><B>$mess_UL_Sco</B></TD>";
    echo "<TD valign='center'><A HREF=\"javascript:void(0);\" ".
           "onclick=\"return overlib('<TABLE><TR><TD width=5></TD><TD>".addslashes($mess_sco_zip)."</TD></TR></TABLE>'".
           ",STICKY,ol_hpos,RIGHT,ABOVE,WIDTH,350,CAPTION,'<TABLE width=100% border=0 cellspacing=2><TR height=20 width=100%>".
           "<TD align=left width=90% nowrap><B>$mess_nota_bene</B></TD></TR></TABLE>')\"".
           " onMouseOut=\"return nd();\"><IMG SRC='images/modules/anoter.gif' border='0'></A>";
    echo "</TD></TR></TABLE></TD><TR>";
    echo "<TR height='5'><TD colspan='2' valign='top'></TD><TR>";
    echo "<TR height='30'><TD width='70%'valign='top'>";
    echo "<FORM  action='parseur.php?zip=1&id_parc=$id_parc&miens=$miens&refer=$refer&prov=$prov' name ='form2' method='POST' enctype='multipart/form-data' target='main'>";
    echo "<INPUT TYPE='file' name='file' size='53' enctype='multipart/form-data'>";
    echo "</TD><TD align='left' nowrap valign='top' width='30%'><A href=\"javascript:document.form2.submit();\" onmouseover=\"img2.src='images/fiche_identite/boutvalidb.gif';return true;\" onmouseout=\"img2.src='images/fiche_identite/boutvalid.gif'\">";
    echo "<IMG NAME=\"img2\" SRC=\"images/fiche_identite/boutvalid.gif\" BORDER='0' onLoad=\"tempImg=new Image(0,0); tempImg.src='images/fiche_identite/boutvalidb.gif'\">";
    echo "</FORM></TD></TR></TABLE></TD></TR>";

    echo "<TR><TD colspan='2' width='100%'>";
    echo "<TABLE cellspacing='0' cellpadding='4' width='100%'>";
    echo "<TR><TD colspan='2' valign='top' align='left'><TABLE width='55%'><TR><TD valign='center'><B>$mess_DL_aicc</B></TD>";
    echo "<TD valign='center'><A HREF=\"javascript:void(0);\" ".
           "onclick=\"return overlib('<TABLE><TR><TD width=5></TD><TD>".addslashes($mess_aicc_zip)."</TD></TR></TABLE>'".
           ",STICKY,ol_hpos,RIGHT,ABOVE,WIDTH,350,CAPTION,'<TABLE width=100% border=0 cellspacing=2><TR height=20 width=100%>".
           "<TD align=left width=90% nowrap><B>$mess_nota_bene</B></TD></TR></TABLE>')\"".
           " onMouseOut=\"return nd();\"><IMG SRC='images/modules/anoter.gif' border='0'></A>";
    echo "</TD></TR></TABLE></TD><TR>";
    echo "<TR height='5'><TD colspan='2' valign='top'></TD><TR>";
    echo "<TR height='30'><TD width='70%'valign='top'>";
    echo "<FORM  action='parseur_aicc.php?zip=1&id_parc=$id_parc&miens=$miens&refer=$refer&prov=$prov' name ='form3' method='POST' enctype='multipart/form-data' target='main'>";
    echo "<INPUT TYPE='file' name='file' size='53' enctype='multipart/form-data'>";
    echo "</TD><TD align='left' nowrap valign='top' width='30%'><A href=\"javascript:document.form3.submit();\" onmouseover=\"img3.src='images/fiche_identite/boutvalidb.gif';return true;\" onmouseout=\"img3.src='images/fiche_identite/boutvalid.gif'\">";
    echo "<IMG NAME=\"img3\" SRC=\"images/fiche_identite/boutvalid.gif\" BORDER='0' onLoad=\"tempImg=new Image(0,0); tempImg.src='images/fiche_identite/boutvalidb.gif'\">";
    echo "</FORM></TD></TR></TABLE>";
    echo fin_tableau($html);
  exit;
}else{
  if ($zip == 1)
  {
     $file = $dir.$le_manifeste;
     if (strstr($le_manifeste, "imsScormCam.xml"))
     {
         $le_manifeste = str_replace('imsScormCam','imsmanifest',$le_manifeste);
         $source = "include/manifestQuetzal.xml";
         copy ($source,$dir.$le_manifeste);
         chmod($dir.$le_manifeste,0755);
         $file = $dir.$le_manifeste;
     }
     if (!file_exists($file))
     {
        include ('style.inc.php');
        entete_concept($incl,$mess_imp_sco);
        echo "<TR height='10'><TD colspan='2' valign='top'><B>$mess_noManifest</B>";
        echo fin_tableau($html);
        exit;
     }
  }
  $profondeur = array();
  $pointeur = array();
  if (isset($_POST['file']))
     $file = $_POST['file'];
  DEFINE("LE_FICHIER",$file);
  $oter = substr(strrchr($file,"/"),1);
  $scorm_path = str_replace($oter,"",$file);
  $sequence[]=array();
  $Seq[]=array();
  $parente['id_scorm']=array();
  $liste_duree = array();
  $sequence['lom']=0;
  $compte_ress = 0;
  $seq['path']=array();
  $DesqSeq = array();
  $AttentLaunch = 1;;
  // Appel à la fonction de création et d'initialisation du parseur
  if (!(list($xml_parser, $fp) = new_xml_parser($file)))
     die("Impossible d'ouvrir le document XML");
  // Traitement de la ressource XML
  while ($data = fread($fp, 4096)){
    if (!xml_parse($xml_parser, $data, feof($fp))){
      die(sprintf("Erreur XML : %s à la ligne %d\n",
              xml_error_string(xml_get_error_code($xml_parser)),
              xml_get_current_line_number($xml_parser)));
    }
  }
  // Libération de la ressource associée au parser
  xml_parser_free($xml_parser);
}
// début des fonctions
function parseur_descripteur($fic,$pointeur){
        global $ids;
        global $reps;
        global $gpointeur, $data, $liste_duree,$imsmd;
;
        $gpointeur=$pointeur;
        ///////// LECTURE *.XML /////////
        $fic=$reps.$fic;
        $xml_parser2 = xml_parser_create();
        xml_set_element_handler($xml_parser2, "Demarre", "Termine");
        xml_set_character_data_handler($xml_parser2, "Donnee");
        //ouvrir le fichier
        if (!($fp = @fopen($fic, "r"))) {
            return(true);
        }//die("Impossible d'ouvrir le fichier XML  : ".$fic);
        $deb = true ;

        while ($data = fread($fp, 4096)) {
                if ( $deb )
                   $encoding=strpos(strtoupper($data),"UTF-8");
                $deb = false ;
                if (!xml_parse($xml_parser2, $data, feof($fp))) {
                        die(sprintf("Erreur XML: %s  ligne %d",
                        xml_error_string(xml_get_error_code($xml_parser2)),
                        xml_get_current_line_number($xml_parser2)));
                }
        }
        xml_parser_free($xml_parser2);
}
///////////////////////////////////////// PARSEUR FIOHIER LOCATION /////////////////////
function Demarre($parser,$nom, $attrs) {
        global $courant;
        global $AttentID;
        global $desc;
        global $general;
        global $langage,$keyword,$espace,$Duree,$Creation,$Cycle,$Contrib,$Dat,$Educ,$TypicTime,$liste_duree,$imsmd,$Centity,$Tit;

        $courant=$nom;
        if ($nom == $imsmd."EDUCATIONAL")
           $Educ = 1;
        if ($nom == $imsmd."TITLE")
           $Tit = 1;
        if ($nom == $imsmd."CENTITY")
           $Centity = 1;
        if ($nom == $imsmd."TYPICALLEARNINGTIME")
           $TypicTime = 1;
        if ($nom == $imsmd."LIFECYCLE")
           $Cycle = 1;
        if ($nom == $imsmd."CONTRIBUTE")
           $Contrib = 1;
        if ($nom == $imsmd."DATE")
           $Dat = 1;
        if ($nom == $imsmd."CATALOGENTRY")
           $CatalEntry = 1;
        if ($nom == $imsmd."KEYWORD"){
           $keyword = 1;
           $espace = 1;
        }
        if ($nom == $imsmd."DESCRIPTION")
           $desc = 1;
        if ($nom == $imsmd."LANGUAGE")
           $langage = 1;
        if ($nom == $imsmd."GENERAL")
           $general = 1;
}

function Termine($parser,$nom){
        global $courant;
        global $CatalEntry;
        global $desc;
        global $general;
        global $langage,$keyword,$espace,$Duree,$Creation,$Cycle,$Contrib,$Dat,$Educ,$TypicTime,$liste_duree,$imsmd,$Centity,$Tit;

        if ($nom == $imsmd."EDUCATIONAL")
           $Educ = 0;
        if ($nom == $imsmd."TITLE")
           $Tit = 0;
        if ($nom == $imsmd."CENTITY")
           $Centity = 0;
        if ($nom == $imsmd."TYPICALLEARNINGTIME")
           $TypicTime = 0;
        if ($nom=="LIFECYCLE")
           $Cycle = 0;
        if ($nom=="CONTRIBUTE")
           $Contrib = 0;
        if ($nom == $imsmd."DATE")
           $Dat = 0;
        if ($nom == $imsmd."CATALOGENTRY")
           $CatalEntry = 0;
        if ($nom == $imsmd."LANGUAGE")
           $langage = 0;
        if ($nom=="CATALOGENTRY")
           $CatalEntry = 0;
        if ($nom == $imsmd."KEYWORD"){
           $keyword = 0;
           $espace = 0;
        }
        if ($nom == $imsmd."DESCRIPTION")
           $desc = 0;
        if ($nom == $imsmd."GENERAL")
           $general = 0;
        $courant = "";
}

function Donnee($parser, $donnee){
        global $courant;
        global $ids;
        global $CatalEntry;
        global $sequence;
        global $desc;
        global $gpointeur;
        global $general;
        global $langage,$keyword,$espace,$Duree,$Creation,$Cycle,$Contrib,$Dat,$Educ,$TypicTime,$liste_duree,$imsmd,$Centity,$Tit;
        $nom = $courant;
        if ( $nom == $imsmd."LANGSTRING"){
           if ($CatalEntry)
              $Ids = utf8_decode_si_utf8(trim($donnee));
           if ($general && $Tit && !$sequence['titre'][$gpointeur]){
              $sequence['titre'][$gpointeur] = utf8_decode_si_utf8(trim($donnee));
              $Tit = 0;
           }
        }
        if ( $nom == $imsmd."LANGSTRING" && $desc && $general){
           $sequence['description'][$gpointeur] = utf8_decode_si_utf8(trim($donnee)) ;
           $desc = 0;
           $general = 0;
        }
        if ($Contrib && $Cycle){
           if ($nom == $imsmd."VCARD" && $Centity){
              $seq_aut = utf8_decode_si_utf8(trim($donnee));
              $seq_aut = substr(substr($seq_aut,0,-10),12);
              $sequence['auteur'][$gpointeur] = $seq_aut;
              $Centity = 0;
           }
           if ($nom == $imsmd."DATETIME" && $Dat){
              $sequence['creation'][$gpointeur] = utf8_decode_si_utf8(trim($donnee)) ;
              $Dat = 0;
           }
        }
        if ($nom == $imsmd."DATETIME" && $Educ && $TypicTime){
           $duree = utf8_decode_si_utf8(trim($donnee)) ;
           if (strstr($duree,'t') || strstr($duree,'T'))
              $duree = substr($duree,11);
           $liste_duree = explode(":",$duree);
           $duree = ($liste_duree[0]*60)+$liste_duree[1];
           $sequence['duree'][$gpointeur] = $duree;
           $Educ = 0;
           $TypicTime = 0;
           $liste_duree = "";
        }
        if ($nom == $imsmd."LANGSTRING" && $keyword){
           if (strlen($sequence['keyword'][$gpointeur]) > 2 && $espace == 1)
              $sequence['keyword'][$gpointeur] .= ", ".utf8_decode_si_utf8(trim($donnee));
           else
              $sequence['keyword'][$gpointeur] .= utf8_decode_si_utf8(trim($donnee));
           $keyword=0;
        }
        if (($nom == $imsmd."LANGUAGE")&& $general)
           $sequence['langage'][$gpointeur] = utf8_decode_si_utf8(trim($donnee)) ;
}

// Fonction associée à l’événement début d’élément
function debutElement($parser, $name, $attrs){
   global $currentTag;
   global $NumAcTot;
   global $sequence,$Seq;
   global $Itemencours,$ItemPr,$compte_ress;
   global $prerequis;
   global $mastery;
   global $AttentLocation;
   global $IdSeq;
   global $AttentNom,$passe_deja;
   global $maxtime,$Niveau,$timelimiteaction;
   global $ids,$titre;
   global $datafromlms;
   global $desc,$keywords,$titre,$langage,$visible,$scormtype,$imsmd,$NbOne,$VerSchema,$org_pass;
   global $parente;
   global $version,$organisation,$organisations,$metadata,$localiser;
   $currentTag = $name;
   if ($name == "IMSMD:LOM")
      $imsmd = "IMSMD:";
  if ($name == "GENERAL" || $imsmd == "IMSMD:")
     $sequence['lom'] = 1;
  if ($name == "METADATA")
     $metadata = 1;
  if ($name == "ADLCP:LOCATION" && $metadata == 1 && $organisation == 1)
     $localiser = 1;
  if ($name == "ORGANIZATIONS"){
     $organisations = 1;
     $org_pass = 1;
  }
  if ($name == "ORGANIZATION"){
     $organisation = 1;
     $AttentNom=1;
     while (list ($key, $val) = each ($attrs)){
        if ($key == "IDENTIFIER")
           $IdSeq = utf8_decode_si_utf8(trim($val));
     }
  }
  if ($name == "SCHEMAVERSION"){
     $version = 1;
     $sequence['version'][$NumAcTot] = "";
  }
  if ($name == "SCHEMA" && $NbOne > 0){
     $VerSchema = 1;
     $sequence['version'][1] = "";
  }
  if ($name == "ADLCP:LOCATION")
     $AttentLocation = 1;
  if ($name == "ADLCP:TIMELIMITACTION"){
     $timelimiteaction = 1;
     $sequence['timelimiteaction'][$NumAcTot] = "";
  }
  if ($name == "DESCRIPTION")
     $description = 1;
  if ($name == "LANGUAGE")
     $langage = 1;
  if ($name == "TITLE") {
     $titre = 1;
     $sequence['titre'][$NumAcTot] = "";
  }
  if ($name == "KEYWORDS")
     $keywords = 1;
  if ($name == "ITEM"){
     $ItemPr++;
     $Itemencours = 1;
     $prerequis = 1;
     $datafromlms = 1;
     $mastery = 1;
     $maxtime = 1;
     $visible = 1;
     $NumAcTot++;
     $Niveau = $Niveau+1;
     while (list ($key, $val) = each ($attrs)){

        if ($key == "IDENTIFIER"){
           $sequence['id_scorm'][$NumAcTot] = utf8_decode_si_utf8(trim($val));
           $parente['$id_scorm'][$Niveau] = utf8_decode_si_utf8(trim($val));
           if (isset($parente['$id_scorm'][$Niveau-1])){
              $sequence['parente'][$NumAcTot] = $parente['$id_scorm'][$Niveau-1];
              $sequence['niveau'][$NumAcTot] = $Niveau;
           }
           if (($ItemPr == 1 && $NumAcTot == 1) || $Niveau == 1){
              $sequence['niveau'][$NumAcTot] = 1;
              $sequence['parente'][$NumAcTot] = $IdSeq;
           }
        }
        if ($key == "IDENTIFIERREF"){
           $sequence['ref'][$NumAcTot] = utf8_decode_si_utf8(trim($val));
           if ($compte_ress > 0){
              for ($as=1;$as < $compte_ress+1;$as++){
                  if ($sequence['path'][$NumAcTot] == '' && $sequence['ref'][$NumAcTot] == $Seq['identifier'][$as]){
                     $sequence['path'][$NumAcTot] = $Seq['path'][$as];
                     $sequence['scormtype'][$NumAcTot] = $Seq['scormtype'][$as];
                     continue;
                  }
              }
          }
        }
        if ($key == "ISVISIBLE" && (!isset($sequence['ref'][$NumAcTot]) ||
           (isset($sequence['ref'][$NumAcTot]) && $sequence['ref'][$NumAcTot] == '')) && $compte_ress > 0)
                   $sequence['path'][$NumAcTot] = "";
        if ($key == "ISVISIBLE")
           $sequence['visible'][$NumAcTot] = utf8_decode_si_utf8(trim($val));
     }
  }
  if ($name == 'RESOURCE' && $NumAcTot > 0 && $ItemPr > 0){
     $i = 0;
     $ids = '';
     $href = '';
     $scormtype = '';
     $AttentLocation = 1;
     while (list($key,$val)=each($attrs)){
        if ($key == "IDENTIFIER")
           $ids=(utf8_decode_si_utf8(trim($val)));
        if ($key == "HREF")
           $href=(utf8_decode_si_utf8(trim($val)));
        if ($key == "ADLCP:SCORMTYPE")
           $scormtype=(utf8_decode_si_utf8(trim($val)));
     }
     while($i<$NumAcTot+1){
        if (!isset($sequence['ref'][$i])){
           $sequence['ref'][$i] = " ";
           $sequence['scormtype'][$i] = " ";
        }elseif ($ids == $sequence['ref'][$i] || $sequence['ref'][$i] == ""){
           $sequence['path'][$i] = utf8_decode_si_utf8($href);
           $sequence['scormtype'][$i] = utf8_decode_si_utf8($scormtype);
        }
        $i++;
     }
  }elseif ($name == 'RESOURCE' && $NumAcTot == 0 && $ItemPr == 0 && !$org_pass){
     $idf = '';$scormtype='';$href='';
     while (list($key,$val)=each($attrs)){
        if ($key == "IDENTIFIER")
           $idf = utf8_decode_si_utf8(trim($val));
        if ($key == "HREF")
           $href = utf8_decode_si_utf8(trim($val));
        if ($key == "ADLCP:SCORMTYPE")
           $scormtype = utf8_decode_si_utf8(trim($val));
        if ($idf != '' && $href != '' && $scormtype != ''){
           $compte_ress++;
           $Seq['identifier'][$compte_ress] = $idf;
           $Seq['path'][$compte_ress] = $href;
           $Seq['scormtype'][$compte_ress] = $scormtype;
        }
     }
  }elseif ($name == 'RESOURCE' && $NumAcTot == 0 && $NbOne == 0 && $org_pass == 1){
     $i = 0;
     $ids = '';
     $href = '';
     $scormtype = '';
     $AttentLocation = 1;
     while (list($key,$val)=each($attrs)){
        if ($key == "IDENTIFIER")
           $ids=(utf8_decode_si_utf8(trim($val)));
        if ($key == "HREF")
           $href=(utf8_decode_si_utf8(trim($val)));
        if ($key == "ADLCP:SCORMTYPE")
           $scormtype=(utf8_decode_si_utf8(trim($val)));
     }
     if (($scormtype == "sco" || $scormtype == "asset")){
        $NbOne++;
        $sequence['path'][$NbOne] = utf8_decode_si_utf8($href);
        $sequence['scormtype'][$NbOne] = utf8_decode_si_utf8($scormtype);
        $sequence['id_scorm'][$NbOne] = utf8_decode_si_utf8($ids);
     }
  }

}
function finElement($parser, $name){
  global $currentTag,$NumAcTot,$sequence,$DescSeq,$Niveau,$version,$organisation,$organisations,$metadata,$NbOne,$VerSchema,$org_pass,$Seq;
  global $AttentLocation,$titre,$localiser;
  global $AttentLaunch,$passe_deja,$ItemPr,$compte_ress;
  global $scorm_path,$lom,$imsmd;
  $currentTag = $name;

  if ($name == "ITEM"){
     $AttentLaunch = 0;
     $datafromlms = 0;
     $Niveau = $Niveau-1;
  }
  if ($metadata == 1 && $organisation == 1 && $name == "ADLCP:LOCATION")
     $localiser = 0;
  if ($name == "METADATA")
     $metadata = 0;
  if ($name == "ORGANIZATIONS")
     $organisations = 0;
  if ($name == "ORGANIZATION")
     $organisation = 0;
  if ($name == "SCHEMAVERSION")
     $version = 0;
  if ($name == "SCHEMA")
     $VerSchema = 0;
  if ($name=="DESCRIPTION")
     $desc = 0;
  if ($name == "LANGUAGE")
     $langage = 0;
  if ($name == "TITLE")
     $titre = 0;
  if ($name == "KEYWORDS")
     $keywords = 0;
  if ($name == "RESOURCE")
     $AttentLocation = 0;
}
function elementData($parser, $data)
{
        global $currentTag;
        $name= $currentTag;
        global $Itemencours,$ItemPr,$compte_ress;
        global $prerequis;
        global $gpointeur;
        global $AttentLaunch;
        global $AttentLocation,$passe_deja,$localiser;
        global $mastery;
        global $sequence,$DescSeq,$Seq;
        global $desc,$keywords,$titre,$langage,$version,$visible,$imsmd,$organisation,$organisations,$metadata,$NbOne,$VerSchema,$org_pass;
        global $NumAcTot;
        global $NomSequence,$VerSeq,$DescSeq;
        global $Launch;
        global $maxtime;
        global $timelimiteaction;
        global $datafromlms;
        global $Niveau;
        global $AttentNom;
        global $ids;
//        $data=addslashes($data);

  switch ($name) {
    case "TITLE":
           if (!isset($sequence['titre'][$NumAcTot]))
               $sequence['titre'][$NumAcTot] = " ";
           $sequence['titre'][$NumAcTot] = $sequence['titre'][$NumAcTot].utf8_decode_si_utf8($data);
//           $sequence['niveau'][$NumAcTot] = $Niveau;
           $Itemencours = 0;
           $sequence['titre'][$NumAcTot] = remplace_text($sequence['titre'][$NumAcTot]);

           if ($AttentNom) {
              $NomSequence = utf8_decode_si_utf8($data);
              $AttentNom = 0;
           }
          break;
    case "LANGSTRING":
        if ($desc) {
           $sequence['description'][$NumAcTot] = utf8_decode_si_utf8($data);
           $desc = 0;
        }
        if ($titre) {
           if (!isset($sequence['titre'][$NbOne]) && $NbOne == 1 && !$organisation)
              $sequence['titre'][$NbOne] = utf8_decode_si_utf8($data);
           else
              $sequence['titre'][$NumAcTot]=utf8_decode_si_utf8($data);
           $titre=0;
        }
        if ($keywords) {
           $sequence['keywords'][$NumAcTot]=utf8_decode_si_utf8($data);
           $keywords=0;
        }
        break;
    case "SCHEMAVERSION":
        if ($version && $metadata && !$organisation  && $NbOne == 1)
           $sequence['version'][1] .= " ".utf8_decode_si_utf8($data);
        if ($version && !$metadata && !$organisation)
           $sequence['version'][$NumAcTot] = utf8_decode_si_utf8($data);
        elseif ($version && $metadata && $organisation && !strstr($VerSeq,utf8_decode_si_utf8($data)))
           $VerSeq .= utf8_decode_si_utf8($data);
        break;
    case "SCHEMA":
        if ($VerSchema && $metadata && !$organisation && $NbOne == 1){
           $sequence['version'][1] = utf8_decode_si_utf8($data);}
        if ($metadata && $organisation && !strstr($VerSeq,utf8_decode_si_utf8($data)))
           $VerSeq .= utf8_decode_si_utf8($data);
        break;
    case "ADLCP:MASTERYSCORE":
        if ($mastery){
           $sequence['mastery'][$NumAcTot]=utf8_decode_si_utf8($data);
           $mastery=0;
        }
        break;
    case "ADLCP:TIMELIMITACTION":
        if ($timelimiteaction){
           $sequence['timelimiteaction'][$NumAcTot]=utf8_decode_si_utf8($data);
           $timelimiteaction=0;
        }
        break;
    case "ADLCP:DATAFROMLMS":
        if ($datafromlms){
           if (!isset($sequence['datafromlms'][$NumAcTot]))
              $sequence['datafromlms'][$NumAcTot]=" ";
           $sequence['datafromlms'][$NumAcTot]=$sequence['datafromlms'][$NumAcTot].utf8_decode_si_utf8($data);
        }
        break;
     case "ADLCP:PREREQUISITES" :
           if ($prerequis){
              $sequence['prerequis'][$NumAcTot]=utf8_decode_si_utf8($data);
              $prerequis=0;
           }
           break;
     case "ADLCP:MAXTIMEALLOWED" :
           if ($maxtime){
              $sequence['maxtime'][$NumAcTot]=utf8_decode_si_utf8($data);
              $maxtime=0;
           }
           break;
     case "ADLCP:LOCATION":
         if ($NbOne == 1 && $AttentLocation && ($sequence['scormtype'][1] == "sco" || $sequence['scormtype'][1] == "asset")){
                  $AttentLocation=0;
                  $sequence['location'][1] = utf8_decode_si_utf8($data);
         }else{
           for ($i=1;$i<$NumAcTot+1;$i++) {
               if (($ids == $sequence['ref'][$i]) && $AttentLocation && !$organisation) {
                  $AttentLocation=0;
                  $sequence['location'][$i] = utf8_decode_si_utf8($data);
                  $i=$NumAcTot;
               }elseif ($metadata && $organisation && $localiser && $sequence['location'][$i] == ''){
//               }elseif (($ids == $sequence['ref'][$i]) && $metadata && $organisation && $localiser && $sequence['location'][$i] == ''){
                  $sequence['location'][$i] = utf8_decode_si_utf8($data);
               }
           }
           if ($metadata && $organisation && $Niveau == 0 && !$passe_deja){
              $sequence['location'][0] = utf8_decode_si_utf8($data);
              $passe_deja = 1;
           }
         }
          break;
   }
}
// Fonction associée à l’événement de détection d'un appel d'entité externe
function externalEntityRefHandler($parser,$openEntityNames,$base,$systemId,$publicId){
   if ($systemId){
      if (!list($parser, $fp) = new_xml_parser($systemId)){
         printf("Impossible d'ouvrir %s à %s\n",$openEntityNames,$systemId);
         return FALSE;
      }
      while ($data = fread($fp, 4096)){
         if (!xml_parse($parser, $data, feof($fp))){
             printf("Erreur XML : %s à la ligne %d lors du traitement de l'entité %s\n",
                   xml_error_string(xml_get_error_code($parser)),xml_get_current_line_number($parser),$openEntityNames);
             xml_parser_free($parser);
             return FALSE;
         }
      }
      xml_parser_free($parser);
      return TRUE;
   }
   return FALSE;
}
// Fonction de création du parser et d'affectation des fonctions aux gestionnaires d'événements
function new_xml_parser($file)
{
   global $scorm_path;
   global $parser_file;
   //création du parseur
   $xml_parser = xml_parser_create();
   //Activation du respect de la casse du nom des éléments XML
   xml_parser_set_option($xml_parser, XML_OPTION_CASE_FOLDING, 1);
   //Déclaration des fonctions à rattacher au gestionnaire d'événement
   xml_set_element_handler($xml_parser, "debutElement", "finElement");
   xml_set_character_data_handler($xml_parser, "elementData");
   xml_set_external_entity_ref_handler($xml_parser, "externalEntityRefHandler");
   //Ouverture du fichier
   if (!($fp = @fopen($file, "r")))
      return FALSE;
   //Transformation du parseur en un tableau
   if (!is_array($parser_file))
      settype($parser_file, "array");
   $parser_file[$xml_parser] = $file;
   return array($xml_parser, $fp);
}
function getextension($file)
{
  $bouts = explode(".", $file);
  return array(array_pop($bouts), implode(".", $bouts));
}

// fin des fonctions
for($i=0;$i<$NumAcTot+1;$i++) {
  if (isset($sequence['location'][$i]) && strlen(trim($sequence['location'][$i])) > 0)
  {
     $sequence['keyword'][$i]="";
     parseur_descripteur($scorm_path.$sequence['location'][$i],$i);
  }elseif ($sequence['lom'] == 1)// dans le cas ou le LOM se trouve dans le manifest : cas de e-learningmaker de Doceo
     parseur_descripteur(LE_FICHIER,$i);
}
$number_parent = array();
if ($IdSeq){
   $affiche_sco.= "<BR><B>Nom de la séquence</B> = ".$NomSequence."<BR>";
   if ($sequence['auteur'][0] != "")$affiche_sco.= "<B>Auteur</B> = ".$sequence['auteur'][0]."<BR>";
   if ($sequence['creation'][0] != "")$affiche_sco.= "<B>date de creation</B> = ".$sequence['creation'][0]."<BR>";
   if ($sequence['description'][0] != "")$affiche_sco.= "<B>Description de la séquence</B> = ".$sequence['description'][0]."<BR>";
   if ($sequence['keyword'][0] != "")$affiche_sco.= "<B>Mots-clef</B> = ".$sequence['keyword'][0]."<BR>";
   $affiche_sco.= "<B>Référence de la séquence</B> = ".$IdSeq."<BR>";
   $affiche_sco.= "<B>Version</B> = ".$VerSeq."<BR>";
   for($i=1;$i<$NumAcTot+1;$i++) {
     if (isset($sequence['duree'][$i]))
        $duree_totale += $sequence['duree'][$i];
   }
   if ($duree_totale > 0)
     $affiche_sco.= "<B>Durée totale en mn</B> = ".$duree_totale."<BR>";
   $la_version = "";
   $decrire = "";
   if (strstr(strtolower($VerSeq),'scorm')){
     $la_version = "SCORM";
     if (strstr($VerSeq,'1.2'))
        $la_version .= " 1.2";
     elseif(strstr($VerSeq,'1.3'))
        $la_version .= " 2004";
   }else
        $la_version = "SCORM 1.2";
   if ($NomSequence == "" || strtolower($NomSequence) == "none")
      $titre = "Ce module n'a pas de titre";
   else
      $titre = $NomSequence;
   if (((!isset($sequence['description'][0]) || (isset($sequence['description'][0]) && $sequence['description'][0] == "")) ||
      isset($sequence['description'][0]) && strtolower($sequence['description'][0]) == "none"))
      $decrire = "Pas de description";
   else
      $decrire = $sequence['description'][0];
   if (((!isset($sequence['auteur'][0]) || (isset($sequence['auteur'][0]) && $sequence['auteur'][0] == "")) ||
      isset($sequence['auteur'][0]) && strtolower($sequence['auteur'][0]) == "none"))
      $decrire .= "<BR>Auteur(s) : inconnu(s)";
   else
      $decrire .= $sequence['auteur'][0];
   if (((!isset($sequence['keyword'][0]) || (isset($sequence['keyword'][0]) && $sequence['keyword'][0] == "")) ||
      isset($sequence['keyword'][0]) && strtolower($sequence['keyword'][0]) == "none"))
      $clef = "aucun";
   else
      $clef = $sequence['keyword'][0];
   if (((!isset($sequence['creation'][0]) || (isset($sequence['creation'][0]) && $sequence['creation'][0] == "")) ||
      isset($sequence['creation'][0]) && strtolower($sequence['creation'][0]) == "none"))
      $la_date = str_replace("/","-",$sequence['creation'][0]);
   else
      $la_date = $date_dujour;
   if ($sequence['scormtype'][$i] != "asset")
   {
      $decrire =str_replace("\"","'",$decrire);
      $la_date = ($la_date == "") ? $date_dujour : $la_date;
      $id_new_seq = Donne_ID ($connect,"SELECT max(seq_cdn) from sequence");
      $insert_new_seq = mysql_query ("insert into sequence values ($id_new_seq,\"$titre\",\"$decrire\",\"$clef\",'OUI','$duree_totale',$id_user,\"".$la_date."\",\"$date_dujour\",1,0,\"$la_version\")");
      $insert_rss = rss :: ajout('sequence',$id_user,$id_new_seq);
      $id_seqref = Donne_ID ($connect,"select max(seqref_cdn) from sequence_referentiel");
      $insert_ref_seq = mysql_query ("insert into sequence_referentiel values ($id_seqref,$id_new_seq,'0')");
   }else
      $affiche_sco.= "<BR><Font color='red'><B>$mess_asset_xml</B></FONT><BR>";

}
if (isset($NbOne) && $NbOne == 1){
   $NumAcTot = 1;
   if (strlen(trim($sequence['location'][$NumAcTot])) > 0){
     $sequence['keyword'][$NumAcTot]="";
     parseur_descripteur($scorm_path.$sequence['location'][$NumAcTot],1);
   }
}
$efface = 0;
for($i=1;$i<$NumAcTot+1;$i++) {
   $invalide = 0;
   if ($i == 1)
   {
      if ($duree_totale == 0 || !$duree_totale)
         $duree_totale = 5;
      if (isset($sequence['creation'][1]) && $sequence['creation'][1] != "" && strlen($sequence['creation'][1]) == 10)
         $la_date = str_replace("/","",$sequence['creation'][1]);
      else
         $la_date = "2000-01-01";
      if (((!isset($sequence['titre'][1]) || (isset($sequence['titre'][1]) && $sequence['titre'][1] == "")) ||
            isset($sequence['titre'][1]) && strtolower($sequence['titre'][1]) == "none"))
      {
         if ($sequence['scormtype'][$i] != "sco")
            $titre = str_replace("(","",str_replace(")","",$NomSequence));
         else
            $titre = "Sans titre";
      }else
         $titre = str_replace("(","",str_replace(")","",$sequence['titre'][$i]));
      if (((!isset($sequence['description'][1]) || (isset($sequence['description'][1]) && $sequence['description'][1] == "")) ||
      isset($sequence['description'][1]) && strtolower($sequence['description'][1]) == "none"))
         $decrire = "Pas de description";
      else
         $decrire = $sequence['description'][0];
      if ($sequence['auteur'][0] == "" || strtolower($sequence['auteur'][0]) == "none")
         $decrire .= "<BR>Auteur(s) : inconnu";
      else
         $decrire .= "<BR>".$sequence['auteur'][0];
      if ($nb_parc > 0)
         $titre .= "_".$nb_parc;
//      if ($nb_parc == 0 && $sequence['scormtype'][$i] != "asset"){
   }
   $affiche_sco.= "<BR>id_scorm = ".$sequence['id_scorm'][$i]."<BR>";
   $affiche_sco.= "titre = ".$sequence['titre'][$i]."<BR>";
   $affiche_sco.= "description = ".$sequence['description'][$i]."<BR>";
   $affiche_sco.= "path = ".$sequence['path'][$i]."<BR>";
//   if (isset($sequence['scormtype'][$i]) && $sequence['scormtype'][$i]!= "sco" && $sequence['scormtype'][$i] != "asset")
   if ($sequence['path'][$i] == "")
      $scormtype = 'LABEL';
   elseif(strtoupper($sequence['scormtype'][$i]) == "ASSET")
      $scormtype = 'ASSET';
   else
      $scormtype = 'SCORM';
   if (strstr($sequence['path'][$i],"?"))
      $laseq = str_replace(strrchr($sequence['path'][$i],"?"),"",$sequence['path'][$i]);
   else
      $laseq = $sequence['path'][$i];
   if(!strstr($scorm_path.$sequence['path'][$i],"http://")){
      if (!file_exists($scorm_path.$laseq) && ($scormtype == 'SCORM' || $scormtype == 'ASSET')){
         $efface++;
         $invalide = 1;
      }
   }
   $sequence['path'][$i]=urldecode($sequence['path'][$i]);
   if ($invalide > 0)
      $affiche_sco .= "<Font color='red'><B>".$mess_nopathsco." : ".$sequence['path'][$i]."</B></FONT><BR>";
   $affiche_sco.= "parente = ".$sequence['parente'][$i]."<BR>";
   $affiche_sco.= "date de creation = ".$sequence['creation'][$i]."<BR>";
   $affiche_sco.= "auteur = ".$sequence['auteur'][$i]."<BR>";
   // lié à la ressource durée d'apprentissage estimé : non limitatif
   $affiche_sco.= "durée = ".$sequence['duree'][$i]."<BR>";
   $affiche_sco.= "visible = ".$sequence['visible'][$i]."<BR>";
   // note minimum à obtenir pour l'acquisition
   $affiche_sco.= "mastery = ".$sequence['mastery'][$i]."<BR>";
   $affiche_sco.= "scormtype = ".$sequence['scormtype'][$i]."<BR>";
   $affiche_sco.= "prerequis = ".$sequence['prerequis'][$i]."<BR>";
   $affiche_sco.= "maxtime = ".$sequence['maxtime'][$i]."<BR>";
   //action entreprise au-dela du temps défini par maxtimeallowwed ://exit,message//exit,no message
   //continue,message//continue,no message (leplus courant et le moins limitatif : cas d'un document à consulter)
   $affiche_sco.= "timelimiteaction = ".$sequence['timelimiteaction'][$i]."<BR>";
   $affiche_sco.= "niveau = ".$sequence['niveau'][$i]."<BR>";
   $affiche_sco.= "location = ".$sequence['location'][$i]."<BR>";
   $affiche_sco.= "datafromlms = ".$sequence['datafromlms'][$i]."<BR>";
   $affiche_sco.= "keywords = ".$sequence['keywords'][$i]."<BR>";
   // lié à la ressource mots-clef liée au sco ou à l'asset
   $affiche_sco.= "keyword = ".$sequence['keyword'][$i]."<BR>";
   $affiche_sco.= "langage = ".$sequence['langage'][$i]."<BR>";
      $titre = str_replace("\"","-",$sequence['titre'][$i]);
      if ($sequence['description'][$i] !='')
         $description = str_replace("\"","-",$sequence['description'][$i]);
      else
         $description = 'Pas de description';
      if ($sequence['visible'][$i] != '')
         $visible = $sequence['visible'][$i];
      else
         $visible = 'TRUE';
      $niveau = $sequence['niveau'][$i];
      if ($sequence['path'][$i] != '' && !strstr($sequence['path'][$i],"http://"))
         $path = $scorm_path.$sequence['path'][$i];
      elseif ($sequence['path'][$i] != '' && strstr($sequence['path'][$i],"http://"))
         $path = $sequence['path'][$i];
      else
         $path = $sequence['path'][$i];
      $scormid = $sequence['id_scorm'][$i];
      $parente = $sequence['parente'][$i];
      if ($parente == "$IdSeq" || $i == 1)
         $number_parent[$i] = 0;
      else
         $number_parent[$i] = GetDataField ($connect,"select mod_ordre_no from scorm_module where mod_seq_no = '$id_new_seq' AND mod_numero_lb=\"$parente\"","mod_ordre_no");
      if (($sequence['duree'][$i] == 0 || $sequence['duree'][$i] == '') && $sequence['maxtime'][$i] != '' && strlen($sequence['maxtime'][$i]) > 9){
           $liste_duree = explode(":",$sequence['maxtime'][$i]);
           $maxitime = ($liste_duree[0]*60)+$liste_duree[1];
           $duree_seq = $maxitime;
      }else
          $duree_seq = $sequence['duree'][$i];
      $params = '';
      $prerequis = $sequence['prerequis'][$i];
      $maxtime = $sequence['maxtime'][$i];
      if ($sequence['timelimiteaction'][$i] != '')
         $limiteaction = $sequence['timelimiteaction'][$i];
      else
         $limiteaction = 'continue,no message';
      $datafromlms = $sequence['datafromlms'][$i];
      $mastery = $sequence['mastery'][$i];
      $motclef =  str_replace("\"","-",$sequence['keyword'][$i]);
      if ($id_parc == 0 || !isset($id_parc))
         $id_parc = -1;
      $description =str_replace("\"","'",$description);
      $id_new_mod = Donne_ID ($connect,"SELECT max(mod_cdn) from scorm_module");
      $insert_new_mod = mysql_query ("insert into scorm_module values ($id_new_mod,$id_parc,$id_new_seq,\"$titre\",\"$description\",\"\",\"$motclef\",\"$visible\",'$duree_seq','$niveau',\"$path\",\"$scormid\",'$i',\"$parente\",".$number_parent[$i].",\"$scormtype\",\"$prerequis\",\"$maxtime\",\"$limiteaction\",\"$datafromlms\",\"$mastery\")");
}
if ($efface > 0){
   $req_del_parc = mysql_query("DELETE FROM scorm_module WHERE mod_seq_no = $id_new_seq");
   $req_del_parc = mysql_query("DELETE FROM sequence WHERE seq_cdn = $id_new_seq");
   $supp_rss = rss :: supprime('sequence',$id_new_seq);
   $req_del_parc = mysql_query("DELETE FROM sequence_referentiel WHERE seqref_seq_no = $id_new_seq");
   $req_del_parc = mysql_query("DELETE FROM sequence_parcours WHERE seqparc_seq_no = $id_new_seq");
   $message = $mess_no_sco;
}elseif ($prov != "seq")
   add_seq_user($id_new_seq);
include ('style.inc.php');
entete_concept($incl,$mess_imp_sco);
if ($message != '')
   echo "<TR height='50'><TD valign= 'center' colspan='2' width='100%'><Font size='3'><B>$message</B></FONT></TD></TR>";
echo "<TR><TD colspan='2' width='100%' bgcolor='#CEE6EC'>";
echo $affiche_sco;
echo fin_tableau($html);
echo "</BODY></HTML>";
?>