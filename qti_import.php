<?php
if (!isset($_SESSION)) session_start();

if (!isset($_SESSION['id_user']) || $_SESSION['id_user'] == "")
{
  exit();
}
include ("include/UrlParam2PhpVar.inc.php");
require 'admin.inc.php';
require 'fonction.inc.php';
require "fonction_html.inc.php";
require "lang$lg.inc.php";
require("class/ClassImportQti.php");
require("xml2json/xml2array.php");
dbConnect();

if ($lg == "fr")
   setlocale(LC_TIME,'fr_FR');
elseif($lg == "ru")
   setlocale(LC_TIME,'ru_RU');
$ddj = time();
$date_cour = date ("Y-n-d");
$ch_dt= explode ("-",$date_cour);
$dte = strftime("%A %e %B %Y", mktime(0, 0, 0, $ch_dt[1],$ch_dt[2],$ch_dt[0]));
$auteur = stripslashes($_SESSION['prename_user']).' '.stripslashes($_SESSION['name_user']);

if (isset($_FILES['file']['tmp_name']) && !strstr(strtolower($_FILES['file']['name']),".zip") && isset($zip) && $zip == 1)
{
      echo "<CENTER><TABLE bgColor='#298CA0' cellspacing='2' cellpadding='0'><TR>";
      echo "<TD valign='top' width='70%' height='100%' bgcolor='#CEE6EC'>";
      echo "<TABLE cellspacing='1' cellpadding='0' width='100%'>";
      echo "<TR><TD background=\"images/fond_titre_table.jpg\" colspan='2' height='40' align='center' valign='center'>";
      echo "<Font size='3' color='#FFFFFF'><B>Importation d'un Qcm au format Qti</B></FONT></TD></TR>";
      echo "<TR height='50'><TD colspan='2' valign='center'><FONT size='2' color='red'>".
           "<B>Votre fichier n'est pas une archive</B></font></TD><TR>";
      echo "<TR height='50'><TD colspan='2' valign='center'><a href='javascript:history.back();'>".
           "<img src='images/fiche_identite/boutretour.gif'></a></TD><TR>";
      echo fin_tableau($html);
     exit;

}
if (isset($_FILES['file']['tmp_name']) && strstr(strtolower($_FILES['file']['name']),".zip") && isset($zip) && $zip == 1)
{
    require('class/pclzip.inc.php');
    include ('style.inc.php');
    $poids = "8 Mo";
    if ($_FILES['file']['name'] == "")
       $message = strtolower($mess_fichier_no);
    elseif(!is_file($_FILES['file']['tmp_name']))
       $message = $mess_fic_dep_lim." ".$poids;
    if ($message != "")
    {
      echo "<CENTER><TABLE bgColor='#298CA0' cellspacing='2' cellpadding='0'><TR>";
      echo "<TD valign='top' width='70%' height='100%' bgcolor='#CEE6EC'>";
      echo "<TABLE cellspacing='1' cellpadding='0' width='100%'>";
      echo "<TR><TD background=\"images/fond_titre_table.jpg\" colspan='2' height='40' align='center' valign='center'>";
      echo "<Font size='3' color='#FFFFFF'><B>Importation d'un Qcm au format Qti</B></FONT></TD></TR>";
      echo "<TR height='50'><TD colspan='2' valign='center'><FONT size='2' color='red'><B>$message</B></font></TD><TR>";
      echo "<TR height='50'><TD colspan='2' valign='center'><a href='javascript:history.back();'>".
           "<img src='images/fiche_identite/boutretour.gif'></a></TD><TR>";
      echo fin_tableau($html);
     exit;
    }
    //echo $_FILES['file']['name'];
    $fichier = $_FILES['file']['tmp_name'];
    $file = $_FILES['file']['tmp_name'];
    $archive = new PclZip($file);
    if (($list = $archive->listContent()) == 0)
    {
      die("Error : ".$archive->errorInfo(true));
    }

    for ($i=0; $i<sizeof($list); $i++)
    {
//echo $list[$i]["filename"]."<br>";
        if (strstr($list[$i]["filename"],"imsmanifest.xml"))
        {
           mkdir("ressources/temp_$ddj",0775);
           chmod ("ressources/temp_$ddj",0775);
           $lerepertoire = "ressources/temp_$ddj/";
           $list = $archive->extract(PCLZIP_OPT_PATH,$lerepertoire,
                                     PCLZIP_OPT_SET_CHMOD, 0775);
           $accord = 1;
        }
    }
    if (isset($accord) && $accord == 1)
    {
       $xmlStringContents = str_replace("imsmd:","",str_replace("adlcp:","",str_replace("imsqti:","",file_get_contents($lerepertoire.'/'."imsmanifest.xml"))));
       $NotQti = (strstr(strtolower(NewHtmlentities($xmlStringContents,ENT_QUOTES)),'scorm') ||
                  strstr(strtolower(NewHtmlentities($xmlStringContents,ENT_QUOTES)),'adl')) ? 1 : 0;
       $compteur = 0;
       $comtent = array();
       $QcmParams = array();
       $QcmData = array();
       $Xml = array();
       $_SESSION['XmlFile'] = xml2array($xmlStringContents);
       if ($NotQti == 1)
       {
         $dir = substr($lerepertoire,0,-1);
         echo "L'archive que vous essayez d'importer est au format SCORM et non QTI";
         viredir($dir,$s_exp);
         exit;
       }
       if (isset($_SESSION['XmlFile']['manifest']['resources']['resource']['attr']['href']))
       {
            $compteur++;
            $_SESSION['dataFichier'][$compteur] = str_replace("imsmd:","",str_replace("imsqti:","",file_get_contents($lerepertoire.'/'.$_SESSION['XmlFile']['manifest']['resources']['resource']['attr']['href'])));
            $_SESSION['XmlFichier'][$compteur] = xml2array($_SESSION['dataFichier'][$compteur]);
              //echo "<pre>";print_r($_SESSION['XmlFichier'][$compteur]);echo"</pre>---------------------------------------------";exit;
            if (strstr(NewHtmlentities($_SESSION['dataFichier'][$compteur],ENT_QUOTES), "questestinterop"))
               $accord = 0;
       }
       if (isset($_SESSION['XmlFile']['manifest']['resources']['resource'][1]['attr']['href']))
       {
          $res = 0;
           while (isset($_SESSION['XmlFile']['manifest']['resources']['resource'][$res]))
           {
                if (isset($_SESSION['XmlFile']['manifest']['resources']['resource'][$res]['metadata']['lom']['general']['identifier']['value']) &&
                          strstr(strtolower($_SESSION['XmlFile']['manifest']['resources']['resource'][$res]['metadata']['lom']['general']['identifier']['value']),"choice"))
                {
                    $compteur++;
                    $_SESSION['dataFichier'][$compteur] = str_replace("imsmd:","",str_replace("imsqti:","",file_get_contents($lerepertoire.'/'.$_SESSION['XmlFile']['manifest']['resources']['resource'][$res]['attr']['href'])));
                    $_SESSION['XmlFichier'][$compteur] = xml2array($_SESSION['dataFichier'][$compteur]);
                    if (strstr(NewHtmlentities($_SESSION['dataFichier'][$compteur],ENT_QUOTES), "questestinterop"))
                    {
                         $accord = 0;
                    }
                }
                if (!isset($_SESSION['XmlFile']['manifest']['resources']['resource'][$res]['metadata']['lom']['general']['identifier']['value']))
                {
                   $compteur++;
                   if (isset($_SESSION['XmlFile']['manifest']['resources']['resource'][$res]['attr']['href']))
                   {
                     $_SESSION['dataFichier'][$compteur] = str_replace("imsmd:","",str_replace("imsqti:","",file_get_contents($lerepertoire.'/'.$_SESSION['XmlFile']['manifest']['resources']['resource'][$res]['attr']['href'])));
                     if (strstr(NewHtmlentities($_SESSION['dataFichier'][$compteur],ENT_QUOTES), "questestinterop"))
                     {
                         $_SESSION['XmlFichier'][$compteur] = xml2array($_SESSION['dataFichier'][$compteur]);
                         $accord = 0;
                     }
                   }
                }
             $res++;
           }
       }
       if ($accord == 1)
       {
           $titreQcm = utf8_decode_si_utf8(trim($_SESSION['XmlFile']['manifest']['metadata']['lom']['general']['title']['langstring']['value']));
           $NbPages = $compteur;
           $NewIdQcm =  Donne_ID ($connect,"SELECT max(ordre) from qcm_param");
           $NewQcm =  mysql_query ("insert into qcm_param (ordre,qcm_auteur_no,n_pages,duree,titre_qcm) values('".$NewIdQcm."','".
                             $_SESSION['id_user']."','".$NbPages."','15',\"".NewHtmlentities($titreQcm,ENT_QUOTES)."\")");
          $data = ImpQti::Qti20($compteur,$NewIdQcm,$list,$ddj);
       }
    }
    if (!isset($accord) || (isset($accord) && $accord != 1))
    {
        mkdir("ressources/temp_$ddj",0775);
        chmod ("ressources/temp_$ddj",0775);
        $lerepertoire = "ressources/temp_$ddj/";
        $list = $archive->extract(PCLZIP_OPT_PATH,$lerepertoire,
                                PCLZIP_OPT_SET_CHMOD, 0775);
        $compteur = 0;$xml = 0;
        for ($i=0; $i<sizeof($list); $i++)
        {
             if (strstr($list[$i]["filename"],".xml") && !strstr($list[$i]["filename"],"imsmanifest.xml"))
             {
        //echo "<br>".$list[$i]["filename"]."<br>";
                   $compteur++;
                   $_SESSION['dataFichier'][$compteur] = str_replace("imsmd:","",str_replace("imsqti:","",file_get_contents($list[$i]["filename"])));
                   $_SESSION['XmlFichier'][$compteur] = xml2array($_SESSION['dataFichier'][$compteur]);
                   if (strstr(NewHtmlentities($_SESSION['dataFichier'][$compteur],ENT_QUOTES), "questestinterop"))
                       $Qti = "1.2";
                   elseif (strstr(NewHtmlentities($_SESSION['dataFichier'][$compteur],ENT_QUOTES), "assessmentItem"))
                       $Qti = "2.0";
                   else
                   {
                       echo "Cette archive ne contient pas de QTI.";
                       exit();
                   }
                   $xml = 1;
             }
        }
        if ($xml == 0)
        {
           echo "Cette archive ne contient aucun fichier XML.";
           exit();
        }
        $_SESSION['list'] = $list;
        $_SESSION['Param'] = 0;
        switch ($Qti)
        {
           case '1.2':
               //echo "<br>compteur = $compteur<br>";
              $compteurItemTotal = 0;
              //echo "<pre>";print_r($_SESSION['XmlFichier'][$compteur]);echo"</pre>---------------------------------------------";
                  if ($compteur == 1 && isset($_SESSION['XmlFichier'][$compteur]['questestinterop']['assessment']))
                  {//  Q/Ass
                      $NewIdQcm = ImpQti:: manageQTI12Assessment($compteur,$ddj);
                  }//fin  Q/Ass
                  elseif ($compteur == 1 && isset($_SESSION['XmlFichier'][$compteur]['questestinterop']['section']))
                  {//  Q/Sect
                      $NewIdQcm = ImpQti:: manageQTI12Section($compteur,$ddj);
                  }
                  elseif ($compteur == 1 && isset($_SESSION['XmlFichier'][$compteur]['questestinterop']['item']))
                  {
                      $NewIdQcm = ImpQti:: manageQTI12Item($compteur,$ddj);
                  }//fin Q/I+
                  elseif ($compteur > 1)
                  {
                          echo "Cette archive QTI 1.2 contient plus d'un fichier XML. <br>".
                             "Assurez-vous qu'un seul fichier XML se trouve dans l'archive que vous désirez imorter";
                  }
                  //ho $content;
              //echo "<pre>";print_r($_SESSION['XmlFichier'][$compteur]);echo"</pre>---------------------------------------------";
             //echo "<pre>";print_r($content);echo"</pre>---------------------------------------------";
              break;
           case '2.0':
                      $NbPages = $compteur;
                      $NewIdQcm =  Donne_ID ($connect,"SELECT max(ordre) from qcm_param");
                      $titreQcm = "AB_Qti20_".$ddj;
                      $NewQcm =  mysql_query ("insert into qcm_param (ordre,qcm_auteur_no,n_pages,duree,titre_qcm) values('".$NewIdQcm."','".
                                 $_SESSION['id_user']."','".$NbPages."','15',\"".NewHtmlentities($titreQcm,ENT_QUOTES)."\")");
                      $data = ImpQti::Qti20($compteur,$NewIdQcm,$list,$ddj);
               break;
           default :
               echo "Il n'y a aucun fichier QTI valide dans cette archive";
        }
    }
    if (isset($NewIdQcm) && $NewIdQcm > 0)
    {
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
        else
            $reqDel = mysql_query("delete from qcm_param where ordre = '$NewIdQcm'");
    }
    if (isset($_SESSION['dataFichier'])) unset($_SESSION['dataFichier']);
    if (isset($_SESSION['XmlFichier'])) unset($_SESSION['XmlFichier']);
    if (isset($_SESSION['XmlFile'])) unset($_SESSION['XmlFile']);
    if (isset($_SESSION['note'])) unset($_SESSION['note']);
    if (isset($_SESSION['list'])) unset($_SESSION['list']);
    if (isset($_SESSION['Param'])) unset($_SESSION['Param']);
    $dir = substr($lerepertoire,0,-1);
    viredir($dir,$s_exp);
    ?>
    <script LANGUAGE="JavaScript" type="text/javascript">
       //parent.location.replace('menu_qcm.php?code=<?php echo $NewIdQcm;?>');
    </script>
     <?php
    exit;
}
elseif((!isset($_FILES['file']['tmp_name']) || !strstr(strtolower($_FILES['file']['name']),".zip")) && isset($zip) && $zip == 1)
{
    include ('style.inc.php');
    $poids = "4,5 Mo";
    if ($_FILES['file']['name'] == "")
       $message = $mess_fichier_no." <BR> &nbsp;&nbsp;et<BR> &nbsp;&nbsp;".strtolower($mess_fic_dep_lim)." $poids<BR>";
    elseif ($_FILES['file']['name'] != "")
       $message = "&nbsp;&nbsp;$mess_nozip";
    if ($message != ""){
      entete_concept($incl,"Import d'une archive au format QTI");
      echo "<TR height='50'><TD colspan='2' valign='center'><FONT size='2' color='red'><B>&nbsp;&nbsp;$message</B></font><BR></TD></TR>";
      echo "<TR height='20'><TD colspan='2' valign='center'>&nbsp</TD></TR>";
      echo "<TR><TD align=left><A HREF=\"javascript:history.go(-1);\" onmouseover=\"img_annonce.src='images/fiche_identite/boutretourb.gif';return true;\" onmouseout=\"img_annonce.src='images/fiche_identite/boutretour.gif'\">";
      echo "<IMG NAME=\"img_annonce\" SRC=\"images/fiche_identite/boutretour.gif\" BORDER='0' onLoad=\"tempImg=new Image(0,0); tempImg.src='images/fiche_identite/boutretourb.gif'\"></A>";
      echo fin_tableau($html);
     exit;
    }
}
if ((!isset($_FILES['file']['tmp_name']) && !isset($zip)) || (isset($_FILES['file']['tmp_name']) && !isset($zip)))
{
    include ('style.inc.php');
    entete_simple("Tranformer une archive QTI au format Formagri");
    echo "<TR height='10'><TD colspan='2' valign='top'>&nbsp;</TD><TR>";
    echo "<TR><TD colspan='2' width='100%'>";
    echo "<TABLE cellspacing='0' cellpadding='4'>";
    echo "<TR><TD colspan='2' valign='top' align='left'><TABLE width='50%'><TR><TD valign='top'>".
         "<span style='font-size:13px;font-family:arial,verdana;font-weight:bold;'>Télécharger une archive QTI</span>".nbsp(10);;
    echo "<A HREF=\"javascript:void(0);\" ".
           "onclick=\"return overlib('<TABLE><TR><TD width=5></TD><TD>Cette archive doit obligatoirement être au format .ZIP".
           "</TD></TR></TABLE>'".
           ",STICKY,ol_hpos,RIGHT,ABOVE,WIDTH,350,CAPTION,'<TABLE width=100% border=0 cellspacing=2><TR height=20 width=100%>".
           "<TD align=left width=90% nowrap><B>$mess_nota_bene</B></TD></TR></TABLE>')\"".
           " onMouseOut=\"return nd();\"><IMG SRC='images/modules/anoter.gif' border='0'></A></TD>";
    echo "</TR></TABLE></TD><TR>";
    echo "<TR><TD colspan='2' valign='top'><div style='border:1px solid red;padding:5px;font-size:12px;font-family:arial,verdana;'>".
         "Le contenu de votre archive ZIP devra être conforme aux spécifications QTI 1.2 ou 2.0.".
           "<br />Sachant que certains outils comme QuestionMarkPerception prennent certaines libertés avec les standards, ".
           "<br />Formagri garantit exclusivement l'import des parties conformes incluses dans toute archive générée par ce type d'outil.".
           "<br />Formagri respecte scrupuleusement les standards QTI mais n'importe pour l'heure que les questions".
           "<br />à choix unique ou multiple textuels et pas les autres types de quizzes (Ordonnancement, appariement, etc..)</div></TD><TR>";
    echo "<TR height='5'><TD colspan='2' valign='top'></TD><TR>";
    echo "<TR height='30'><TD width='70%'valign='top'>";
    echo "<FORM  action='qti_import.php?zip=1' name ='form2' method='POST' enctype='multipart/form-data'>";
    echo "<INPUT TYPE='file' name='file' size='53' enctype='multipart/form-data'>";
    echo "</TD><TD align='left' nowrap valign='top' width='30%'><A href=\"javascript:document.form2.submit();\" onmouseover=\"img2.src='images/fiche_identite/boutvalidb.gif';return true;\" onmouseout=\"img2.src='images/fiche_identite/boutvalid.gif'\">";
    echo "<IMG NAME=\"img2\" SRC=\"images/fiche_identite/boutvalid.gif\" BORDER='0' onLoad=\"tempImg=new Image(0,0); tempImg.src='images/fiche_identite/boutvalidb.gif'\">";
    echo "</FORM></TD></TR></TABLE></TD></TR>";
    echo fin_tableau($html);
  exit;
}

?>