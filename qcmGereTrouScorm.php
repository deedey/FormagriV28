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
require("class/export_xml.php");
dbConnect();
if ($lg == "fr")
   setlocale(LC_TIME,'fr_FR');
elseif($lg == "ru")
   setlocale(LC_TIME,'ru_RU');
$date_cour = date ("Y-n-d");
$ch_dt= explode ("-",$date_cour);
$dte = modif_az_qw(strftime("%A %e %B %Y", mktime(0, 0, 0, $ch_dt[1],$ch_dt[2],$ch_dt[0])));
$auteur = stripslashes($_SESSION['prename_user']).' '.stripslashes($_SESSION['name_user']);
if (!empty($_GET['download']) && $_GET['download'] == 1)
{
   $file_path = dirname($_GET['dossier']) ;
   $nom = $_GET['nom'].'.zip';
   $file =$file_path.'/'.$nom;
   ForceFileDownload($file,'application/zip');
   exit();
}
if (!empty($_GET['file']))
   $_SESSION['XmlFichier'] = $_GET['file'].'.xml';
$xmlStringContents = file_get_contents($_SESSION['XmlFichier']);
require_once("xml2json/xml2json.php");
$_SESSION['XmlFile'] = xml2json::transformXmlStringToJson($xmlStringContents);
$nbInteractions = count($_SESSION['XmlFile']['evaluation']['interaction']);
if (!empty($_POST['modifieQcm']) && $_POST['modifieQcm'] == 1)
{
   if (!empty($_POST['modifieParam']) && $_POST['modifieParam'] == 1)
   {
      $_SESSION['XmlFile']['evaluation']['@attributes']['masteryScore'] = stripslashes(utf8_encode($_POST['moyenne']));
      $_SESSION['XmlFile']['evaluation']['@attributes']['label'] =  stripslashes(utf8_encode($_POST['titre']));
   }
   elseif(!empty($_POST['modifieData']) && $_POST['modifieData'] == 1)
   {
      $i = $_POST['numero']-1;
      $TitreQ = exp_xml::controlTQ($_POST['intitule']);
      $_SESSION['XmlFile']['evaluation']['interaction'][$i]['@attributes']['label'] = stripslashes(utf8_encode($TitreQ));
      $_SESSION['XmlFile']['evaluation']['interaction'][$i]['@attributes']['weighting'] = stripslashes(utf8_encode($_POST['poids']));
      for($j=0;$j<4;$j++)
      {
          $prop = 'p'.$j;
          $propOk = exp_xml::controlSlash($_POST[$prop]);
          $_SESSION['XmlFile']['evaluation']['interaction'][$i]['choice'][$j]['@attributes']['label'] = stripslashes(utf8_encode($propOk));
          if ($_POST['oneC'] == ($j+1))
             $_SESSION['XmlFile']['evaluation']['interaction'][$i]['choice'][$j]['@attributes']['correct'] = "true";
          else
             unset($_SESSION['XmlFile']['evaluation']['interaction'][$i]['choice'][$j]['@attributes']['correct']);
      }
   }
   $affiche =  exp_xml::entete_qcm($dte,$auteur,$_SESSION['typ_user'],$adresse_http);
   $affiche .= "<evaluation id = \"".$_SESSION['XmlFile']['evaluation']['@attributes']['id']. "\" ";
   $affiche .= "label = \"".$_SESSION['XmlFile']['evaluation']['@attributes']['label']. "\" ";
   $affiche .= "masteryScore = \"".$_SESSION['XmlFile']['evaluation']['@attributes']['masteryScore']. "\" ";
   $affiche .= "positiveFeedback = \"".utf8_encode('Bonne réponse'). "\" ";
   $affiche .= "negativeFeedback = \"".utf8_encode('Mauvaise réponse'). "\" >\n";
   for ($i=0;$i<$nbInteractions;$i++)
   {
      $affiche .= "<interaction id = \"".$_SESSION['XmlFile']['evaluation']['interaction'][$i]['@attributes']['id']."\" ";
      $affiche .=  "label = \"".$_SESSION['XmlFile']['evaluation']['interaction'][$i]['@attributes']['label']."\" ";
      $affiche .=  "type = \"".$_SESSION['XmlFile']['evaluation']['interaction'][$i]['@attributes']['type']. "\" ";
      $affiche .=  "weighting = \"".$_SESSION['XmlFile']['evaluation']['interaction'][$i]['@attributes']['weighting']. "\" >\n";
      for($j=0;$j<4;$j++)
      {
          $affiche .=    "<choice label = \"". $_SESSION['XmlFile']['evaluation']['interaction'][$i]['choice'][$j]['@attributes']['label']. "\" ";
          if (!empty($_SESSION['XmlFile']['evaluation']['interaction'][$i]['choice'][$j]['@attributes']['correct']))
             $affiche .=    "  correct = \"". $_SESSION['XmlFile']['evaluation']['interaction'][$i]['choice'][$j]['@attributes']['correct']. "\" /> \n ";
          else
             $affiche .=  " /> \n ";
      }
      $affiche .=  " </interaction>\n ";
   }
   $affiche .=  " </evaluation> ";
   $fp = fopen($_SESSION['XmlFichier'], "w");
       $fw = fwrite($fp, $affiche);
   fclose($fp);
   //header ("Content-Type: text/xml");
   //echo $affiche;
   //exit;
}
include ('style.inc.php');
?>
<style>
#listQuest{
    clear:both;float:left;text-align:left;margin:2px 0 2px 8px;padding:0 4px 0 4px;
    min-width:20%;max-width:98%;border:1px solid #24677A;background-color: #eee;
}
.tDTable{padding:2px;}
.leTD { padding-top: 5px;padding-bottom: 5px;margin-top: 5px;margin-bottom: 5px;}
</style>

<SCRIPT language="javascript" type="text/javascript">
        function checkFormQuestion(frm) {
                 var ErrMsg = "<?php echo $mess_info_no;?>\n\n";
                 var lenInit = ErrMsg.length;
                 if (isEmpty(frm.intitule)==true)
                    ErrMsg += ' - <?php echo "Phrase comportant des trous sous la forme de 3 points \"...\"";?>\n';
                 if (isEmpty(frm.poids)==true)
                    ErrMsg += ' - <?php echo "Coefficient ou poids de la question au sein de l\'exercice";?>\n';
                 if (isEmpty(frm.p0)==true)
                    ErrMsg += ' - <?php echo "Première proposition";?>\n';
                 if (isEmpty(frm.p1)==true)
                    ErrMsg += ' - <?php echo "Deuxième proposition";?>\n';
                 if (isEmpty(frm.p2)==true)
                    ErrMsg += ' - <?php echo "Troisième proposition";?>\n';
                 if (isEmpty(frm.p3)==true)
                    ErrMsg += ' - <?php echo "Quatrième proposition";?>';
                 if (ErrMsg.length > lenInit)
                    alert(ErrMsg);
                 else
                    frm.submit();
        }
        function checkFormQcm(frm) {
                 var ErrMsg = "<?php echo $mess_info_no;?>\n\n";
                 var lenInit = ErrMsg.length;
                 if (isEmpty(frm.titre)==true)
                    ErrMsg += ' - <?php echo $mess_cqcm_tit_qcm;?>\n';
                 if (isEmpty(frm.moyenne)==true)
                    ErrMsg += ' - <?php echo "MasteryScore";?>\n';
                 if (ErrMsg.length > lenInit)
                    alert(ErrMsg);
                 else
                    frm.submit();
        }
        function isEmpty(elm) {
                 var elmstr = elm.value + "";
                 if (elmstr.length == 0)
                    return true;
                 return false;
        }
</SCRIPT>

<?php
if (isset($_GET['Ajout']) && $_GET['Ajout'] == 1)
{
   echo '<div class="SOUS_TITRE" style="width:450px;margin-bottom:10px;">'.
        'Vous venez d\'ajouter de questionnaire "'.$_GET['LeFichier'].'" à votre Quizz "'.$_GET['LeDossier'].'"</div>';
}
$echoList = '<div id="listQuest"><a href="qcmGereTrouScorm.php?idHome=1" '.
            'title="Modifier les éléments de base du questionnaire"><img src="images/homeQcm.jpg" style="margin-top:2px;border:0;"></a>';
for ($i=0;$i<$nbInteractions;$i++)
{
    if (!empty($_GET['idQuest']) && $_GET['idQuest'] > 0)
        $ajt = (($_GET['idQuest']-1) == $i) ? 'color:#D45211 !important;' : '';
    else
       $ajt = '';
    $echoList .= '<span ><a href="qcmGereTrouScorm.php?idQuest='.
                 $_SESSION['XmlFile']['evaluation']['interaction'][$i]['@attributes']['id'].'" '.
                 'style="margin:0 0 0 12px;'.$ajt.'" title="Aller à la question <b>'.
                 str_replace('"','-',clean_text($_SESSION['XmlFile']['evaluation']['interaction'][$i]['@attributes']['label'])).'</b>">'.
                 $_SESSION['XmlFile']['evaluation']['interaction'][$i]['@attributes']['id'].'</a><span> ';

}
$echoList .= '</div>';
echo $echoList;
$AvertQcm = "<b>Rappel:</b><br />Ce questionnaire est au format XML,
    - L'intitulé de la question doit comporter autant de série de <b>3 points</b> ".
    "que de trous à combler ainsi: <b>texte ... texte</b><br />
    - Ces 3 points doivent être obligatoirement précédés d'un espace<br />
    - Une proposition doit contenir autant d'éléments que de trous<br />
    - Les éléments doivent être obligatoirement séparés par un espace + / + un espace ainsi : <b>texte / texte</b><br />
    Vous pouvez transformer ce questionnaire en un questionnaire à réponse unique
    si vous insérez les <b>3 points à la fin de l'intitulé<br />
    La réponse unique choisie viendra alors s'y insérer.<br />
    Cela permet de transformer cet outil en questionnaire à choix unique.";
if (!empty($_GET['idQuest']) && $_GET['idQuest'] > 0)
{
   $numero = $_GET['idQuest']-1;
   echo '<div style="clear:both;">';
   entete_simple("Modification d'un questionnaire de QCM à trous");
   ?>
    <tr><td colspan='2' class="tDTable">
    <div class="SOUS_TITRE" style="width:650px;margin-bottom:10px;"><?php  echo $AvertQcm;?></div>
    <table class="tableForm"><tbody>
    <FORM id='question' name='question' action="qcmGereTrouScorm.php" method="POST">
      <INPUT type="HIDDEN" name="modifieQcm" value="1">
      <INPUT type="HIDDEN" name="modifieData" value="1">
      <INPUT type="HIDDEN" name="numero" value="<?php echo $_SESSION['XmlFile']['evaluation']['interaction'][$numero]['@attributes']['id'];?>">
      <TR><TD><B><?php  echo "Intitulé de la question";?></B></TD><TD>
      <INPUT TYPE="text" class="INPUT"  name="intitule" align="left" size="100" value="<?php echo str_replace('"','-',clean_text($_SESSION['XmlFile']['evaluation']['interaction'][$numero]['@attributes']['label'])) ;?>"
            title="<?php  echo "Insérez une série de trois points minimum en lieu et place du mot ou des mots à positionner";?>"></TD></TR>
      <TR><TD><B><?php  echo "Poids de la question dans cet exercice";?></B></TD><TD>
      <INPUT TYPE="text" class="INPUT"  name="poids" align="left" size="1" maxlength="1" value="<?php echo clean_text($_SESSION['XmlFile']['evaluation']['interaction'][$numero]['@attributes']['weighting']) ;?>"
            title="<?php  echo "Coefficient pour le calcul de la moyenne de l'exercice";?>"></TD></TR>
      <TR><TD colspan='2' align="center"><B><u>Choisissez une réponse correcte parmi les quatre propositions ci-dessous</u></B></TD></TR>
      <TR><TD><B><?php  echo "Première proposition" ;?></B></TD><TD>
      <INPUT TYPE="text" class="INPUT" name="p0" align="left" size="50" value="<?php echo clean_text($_SESSION['XmlFile']['evaluation']['interaction'][$numero]['choice'][0]['@attributes']['label']) ;?>"
           title="<?php  echo "Pour plus d'un mot dans la phrase, séparez les mots avec un espace + '/'" ;?>">&nbsp;&nbsp;
      <?php
      if (!empty($_SESSION['XmlFile']['evaluation']['interaction'][$numero]['choice'][0]['@attributes']['correct']))
            echo '<INPUT TYPE="radio" name="oneC" value="1" checked />';
      else
            echo '<INPUT TYPE="radio" name="oneC" value="1" />';
      ?>
      </TD></TR>
      <TR><TD><B><?php  echo "Deuxième  proposition" ;?></B></TD><TD>
      <INPUT TYPE="text" class="INPUT" name="p1" align="left" size="50" value="<?php echo clean_text($_SESSION['XmlFile']['evaluation']['interaction'][$numero]['choice'][1]['@attributes']['label']) ;?>"
           title="<?php  echo "Pour plus d'un mot dans la phrase, séparez les mots avec un espace +  '/'" ;?>">&nbsp;&nbsp;
      <?php
      if (!empty($_SESSION['XmlFile']['evaluation']['interaction'][$numero]['choice'][1]['@attributes']['correct']))
            echo '<INPUT TYPE="radio" name="oneC" value="2" checked />';
      else
            echo '<INPUT TYPE="radio" name="oneC" value="2" />';
      ?>
      </TD></TR>
      <TR><TD><B><?php  echo "Troisième  proposition" ;?></B></TD><TD>
      <INPUT TYPE="text" class="INPUT" name="p2" align="left" size="50" value="<?php echo clean_text($_SESSION['XmlFile']['evaluation']['interaction'][$numero]['choice'][2]['@attributes']['label']) ;?>"
           title="<?php  echo "Pour plus d'un mot dans la phrase, séparez les mots avec un espace +  '/'" ;?>">&nbsp;&nbsp;
      <?php
      if (!empty($_SESSION['XmlFile']['evaluation']['interaction'][$numero]['choice'][2]['@attributes']['correct']))
            echo '<INPUT TYPE="radio" name="oneC" value="3" checked />';
      else
            echo '<INPUT TYPE="radio" name="oneC" value="3" />';
      ?>
      </TD></TR>
      <TR><TD><B><?php  echo "Quatrième  proposition" ;?></B></TD><TD>
      <INPUT TYPE="text" class="INPUT" name="p3" align="left" size="50" value="<?php echo clean_text($_SESSION['XmlFile']['evaluation']['interaction'][$numero]['choice'][3]['@attributes']['label']) ;?>"
           title="<?php  echo "Pour plus d'un mot dans la phrase, séparez les mots avec un espace +  '/'" ;?>">&nbsp;&nbsp;
      <?php
      if (!empty($_SESSION['XmlFile']['evaluation']['interaction'][$numero]['choice'][3]['@attributes']['correct']))
            echo '<INPUT TYPE="radio" name="oneC" value="4" checked />';
      else
            echo '<INPUT TYPE="radio" name="oneC" value="4" />';
      ?>
      </TD></TR>
      <?php
      echo '<tr><td colspan="2" class="leTD"><A HREF="javascript:checkFormQuestion(document.question);" class = "bouton_new">
          Validez la modification de cette question</A></td></tr></FORM></tbody></table></div>';
      echo '</td></tr>';
   echo fin_tableau('');
   echo '</div>';
   echo $echoList;

}
elseif ((!empty($_GET['idHome']) && $_GET['idHome'] == 1) || empty($_GET['idQuest']))
{
    ?>
    <tr><td colspan="2" class="tDTable"><div id="Creatif" style="clear:both;float:left;">
            <table class="tableForm" id="formCreate" style="display:block;"><tbody>
    <FORM id='creer' name='questionnaire' action="qcmGereTrouScorm.php" method="POST">
      <INPUT type="HIDDEN" name="modifieQcm" value="1">
      <INPUT type="HIDDEN" name="modifieParam" value="1">
      <TR><TD><B><?php echo $mess_cqcm_tit_qcm;?></B></TD><TD>
      <INPUT TYPE="text" class="INPUT"  name="titre" align="left" size="80"
           value="<?php echo str_replace('"','-',clean_text($_SESSION['XmlFile']['evaluation']['@attributes']['label'])) ;?>"
           title="<?php echo $mess_cqcm_tit_qcm;?>"></TD></TR>
      <TR><TD><B><?php echo "MasteryScore" ;?></B></TD><TD>
      <INPUT TYPE="text" class="INPUT" name="moyenne" align="left" size="2" maxlength="2"
           value="<?php echo $_SESSION['XmlFile']['evaluation']['@attributes']['masteryScore'];?>"
           title="<?php echo "Le MasteryScore est la note sur 100 à partir ".
           "de laquelle le QCM sera-t'il considéré comme acquis." ;?>"></TD></TR>
      <?php
      echo "<tr><td colspan='2' class=\"leTD\"><div style='clear:both;float:left;'>".
           "<A HREF=\"javascript:checkFormQcm(document.questionnaire);\" class = 'bouton_new'>".
            "Modifier le questionnaire</A></div>";
      if (!empty($_GET['createZip']) && $_GET['createZip'] == 1)
      {
          $chemin = dirname($_SESSION['XmlFichier']);
          $tab = explode('/',$chemin);
          $NbT = count($tab);
          $nomFolder = $tab[$NbT-1];
          echo '<div style="float:left;margin-left:30px;"><a href="qcmGereTrouScorm.php?download=1&dossier='.$chemin.'&nom='.$nomFolder.'" '.
               ' class = "bouton_new" title="Télécharger le\'archive créée">Télécharger le Zip de ce Qcm à trous</a></div></td></tr></div>';
      }
      else
      {
          echo '<div style="float:left;margin-left:30px;">'.
               '<A HREF="qcmGereTrouScorm.php?createZip=1" class = "bouton_new">'.
               'Regénérer l\'archive Scorm de ce questionnaire</A></div></td></tr></div>';
      }
      echo "</td></tr></FORM></tbody></table>";
      echo "<tr><td colspan='2'><A HREF=\"".str_replace('xml','html',$_SESSION['XmlFichier'])."\" target='_Blank' class = 'bouton_new'>".
           "Tester le questionnaire</A>";
   echo fin_tableau('');
}
if (!empty($_GET['createZip']) && $_GET['createZip'] == 1)
{
    $chemin = dirname($_SESSION['XmlFichier']);
    $tab = explode('/',$chemin);
    $NbT = count($tab);
    $nomFolder = $tab[$NbT-1];
    $RepScorm = "ressources/".$login."_".$id_user."/ressources/QcmScormTrous/$nomFolder/";
    $handle=opendir($RepScorm);
    $f = 1;$fichier = array();
    while (false !== ($fiche = readdir($handle)))
    {
       if (strstr($fiche,'.html'))
       {
          $fichier[$f] = str_replace('.html','',$fiche);
          $f++;
       }
    }
    closedir($handle);
    $manifest = exp_xml::entete_manifest($dte,$auteur,$_SESSION['typ_user'],$adresse_http);
    $manifest .= exp_xml::metaTrous($_SESSION['email_user'],$_SESSION['XmlFile']['evaluation']['@attributes']['label'],$_POST['motscles'],$adresse_http);
    $manifest .= "<organizations default=\"M0\">\n".
                 "    <organization identifier=\"M0\" structure=\"hierarchical\">\n".
                 "        <title>".stripslashes(utf8_encode($_SESSION['XmlFile']['evaluation']['@attributes']['label']))."</title>\n";
    for ($i=1;$i < $f;$i++)
    {
         $manifest .= "           <item identifier=\"M0_0$i\" identifierref=\"RES-0$i\" isvisible=\"true\">
                         <title>".$fichier[$i]."</title>
         </item>\n";
    }
    $manifest .= "     </organization>\n </organizations>\n<resources>\n";
    for ($i=1;$i < $f;$i++)
    {
         $leTitre = $fichier[$i];
         $manifest .= "      <resource identifier=\"RES-0$i\" type=\"webcontent\" href=\"$leTitre.html\">
                        <file href=\"$leTitre.html\" />
                        <file href=\"cmi.js\" />
                        <file href=\"qcm.swf\" />
                        <file href=\"$leTitre.xml\" />
         </resource>\n";
    }
    $manifest .= "</resources>\n</manifest>";
    $dir = $RepScorm;
    $fp = fopen($RepScorm."imsmanifest.xml", "w");
       $fw = fwrite($fp, $manifest);
    fclose($fp);
    $handle1=opendir("QcmScorm");
    while ($fiche = readdir($handle1))
    {
       if ($fiche != '.' && $fiche != '..')
       {
          copy ("QcmScorm/".$fiche,"ressources/".$login."_".$id_user."/ressources/QcmScormTrous/$nomFolder/$fiche");
       }
    }
    closedir($handle1);
    $dir = "ressources/".$login."_".$id_user."/ressources/QcmScormTrous/$nomFolder/";
    $handle5=opendir($dir);
    while (false !== ($fiche = readdir($handle5)))
    {
      if (strstr($fiche,".xml") && !strstr($fiche,"manifest"))
          copy ("ressources/".$login."_".$id_user."/ressources/QcmScormTrous/$nomFolder/$fiche","ressources/".$login."_".$id_user."/ressources/QcmScormTrous/BanqueQuizzATrous/$fiche");
    }
    closedir($handle5);
    $dir = "ressources/".$login."_".$id_user."/ressources/QcmScormTrous/";
    $handle=opendir($chemin);
    while (false !== ($fiche = readdir($handle)))
    {
      if ($fiche == $nomFolder.'.zip')
          unlink('ressources/'.$login.'_'.$id_user.'/ressources/QcmScormTrous/'.$nomFolder.'.zip');
    }
    include_once("class/archive.inc.php");
    $handle=opendir($dir);
    $zipper = new zip_file("../".$nomFolder.".zip");
    $zipper->set_options(array('basedir'=>$chemin));
    $handle=opendir($chemin);
    while ($fiche = readdir($handle))
    {
       if ($fiche != '.' && $fiche != '..')
          $zipper->add_files($fiche);
    }
    $zipper->create_archive();
    closedir($handle);
}
?>
<div id="mien" class="cms"></div>
</body></html>
