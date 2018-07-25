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
if (!empty($_GET['zipQ']) && $_GET['zipQ'] == 1)
{
  $file ="ressources/".$login."_".$id_user."/ressources/QcmScormTrous/".$_GET['nom'].".zip";
  ForceFileDownload($file,'application/zip');
  exit();
}
if ($lg == "fr")
   setlocale(LC_TIME,'fr_FR');
elseif($lg == "ru")
   setlocale(LC_TIME,'ru_RU');
$date_cour = date ("Y-n-d");
$ch_dt= explode ("-",$date_cour);
$dte = modif_az_qw(strftime("%A %e %B %Y", mktime(0, 0, 0, $ch_dt[1],$ch_dt[2],$ch_dt[0])));
$auteur = stripslashes($_SESSION['prename_user']).' '.stripslashes($_SESSION['name_user']);
if (!empty($_SESSION['XmlFile']))
     unset($_SESSION['XmlFile']);
include ('style.inc.php');
$AvertQcm = "<b>Rappel:<br />Ce questionnaire est au format XML, ".
    "aussi soyez attentif à ne valider que ce qui est bien configuré.</b><br />Afin d'éviter tout disfonctionnement de votre ".
    "questionnaire par la suite vous devez vous assurer avant même de valider que:<br />
    - L'intitulé de la question doit comporter autant de série de <b>3 points</b> que de trous à combler ainsi: <b>texte ... texte</b><br />
    - Ces 3 points doivent être obligatoirement précédés d'un espace<br />
    - Une proposition doit contenir autant d'éléments que de trous<br />
    - Les éléments doivent être obligatoirement séparés par un espace + / + un espace ainsi : <b>texte / texte</b><br />
    Vous pouvez transformer ce questionnaire en un questionnaire à réponse unique
    si vous insérez les <b>3 points à la fin de l'intitulé</b><br />
    La réponse unique choisie viendra alors s'y insérer.<br />
    Cela permet de transformer cet outil en questionnaire à choix unique.";
$AvertQcm1 = '<span style="font-weight:bold;">Information importante:</span><br />
    <span style="font-weight:normal;">
    Cet outil vous proposera la création d\'un questionnaire composé <span style="font-weight:bold;">au minimum</span> :<br />
    - d\'une activité dans l\'archive,<br />
    - de 3 questions par activité.<br />
    Allez au bout de votre test s\'il s\'agit d\'un simple test et suivez bien les consignes.</span>';
?>
<style>
.webwidget_vertical_menu ul{padding: 0px;margin: 0px;font-family: Arial;}
.webwidget_vertical_menu{position:absolute;z-index:929;left:290px;}
.webwidget_vertical_menu ul li{list-style: none;position: relative;margin-left:2px;padding-top:4px;}
.webwidget_vertical_menu ul li a{padding-left: 15px;text-decoration: none;}
.webwidget_vertical_menu ul li ul{display: none;position: absolute;background-color: #fff;z-index: 999999;}
.webwidget_vertical_menu ul li ul li{margin: 0px;border:none;}
.webwidget_vertical_menu ul li ul li ul{}
.webwidget_vertical_menu_down_drop{background-position: right center;background-repeat:no-repeat !important;}
.webwidget_vertical_menu ul li li{font-weight: normal;}
.tDTable{padding:2px;}
.tDajout{font-weight:bold;color:#336699;border:1px solid red;padding:5px;width:580px;background:#efefef;}
.leTD{padding:10px 0 5px 15px;}
.pereQcm{clear:both;float:left;font-weight:bold;padding:2px 4px 0 0;}
.tableForm{background-color:#eee;padding:2px;border:1px solid #24677A;}
</style>
<script type="text/javascript" src="OutilsJs/lib/webwidget_vertical_menu.js"></script>
<SCRIPT language="javascript" type="text/javascript">
        $(function() {
                $("#webwidget_vertical_menu").webwidget_vertical_menu({
                    menu_width: '200',
                    menu_height: '25',
                    menu_margin: '2',
                    menu_text_size: '12',
                    menu_text_color: '#666',
                    menu_background_color: '#D4E7ED',
                    menu_border_size: '1',
                    menu_border_color: '#24677A',
                    menu_border_style: 'solid',
                    menu_background_hover_color: '#ccc',
                    directory: '/formagri/OutilsJs/images'
                });
        });
        function checkFormQcm(frm) {
                 var ErrMsg = "<?php echo $mess_info_no;?>\n\n";
                 var lenInit = ErrMsg.length;
                 if (isEmpty(frm.titre)==true)
                    ErrMsg += ' - <?php echo $mess_cqcm_tit_qcm;?>\n';
                 <?php
                 if (empty($_SESSION['Folder'])){
                 ?>
                     if (isEmpty(frm.nomDossier)==true)
                         ErrMsg += ' - <?php echo "Nom du dossier contenant tout le Scorm";?>\n';
                 <?php
                 }
                 ?>
                 if (isEmpty(frm.nomFichier)==true)
                    ErrMsg += ' - <?php echo "Nom du fichier à créer";?>\n';
                 if (isEmpty(frm.moyenne)==true)
                    ErrMsg += ' - <?php echo "MasteryScore : score à atteindre pour une validation du test";?>\n';
                 if (ErrMsg.length > lenInit)
                    alert(ErrMsg);
                 else
                    frm.submit();
        }
        function checkFormCreerM(frm) {
                 var ErrMsg = "<?php echo $mess_info_no;?>\n\n";
                 var lenInit = ErrMsg.length;
                 if (isEmpty(frm.titre)==true)
                    ErrMsg += ' - <?php echo $mess_cqcm_tit_qcm;?>\n';
                 if (isEmpty(frm.motscles)==true)
                    ErrMsg += ' - <?php echo "Mots-Clefs";?>';
                 if (ErrMsg.length > lenInit)
                    alert(ErrMsg);
                 else
                    frm.submit();
        }
        function checkFormQuestion(frm) {
                 var ErrMsg = "<?php echo $mess_info_no;?>\n\n";
                 var lenInit = ErrMsg.length;
                 if (isEmpty(frm.intitule)==true)
                    ErrMsg += ' - <?php echo "Phrase comportant des trous sous la forme de 3 points \"...\"";?>\n';
                 if (isEmpty(frm.poids)==true)
                    ErrMsg += ' - <?php echo "Coefficient ou poids de la question au sein de l\'exercice";?>\n';
                 if (isEmpty(frm.one)==true)
                    ErrMsg += ' - <?php echo "Première proposition";?>\n';
                 if (isEmpty(frm.two)==true)
                    ErrMsg += ' - <?php echo "Deuxième proposition";?>\n';
                 if (isEmpty(frm.three)==true)
                    ErrMsg += ' - <?php echo "Troisième proposition";?>\n';
                 if (isEmpty(frm.four)==true)
                    ErrMsg += ' - <?php echo "Quatrième proposition";?>';
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
if (isset($_GET['NewFile']))
   entete_simple("Ajout de questionnaire au Quizz : ".$_GET['LeDossier']);
else
   entete_simple("Création de l'archive Scorm d'un QCM à trous");
$banqueOk = 0;
if (file_exists("ressources/".$login."_".$id_user."/ressources/QcmScormTrous/BanqueQuizzATrous"))
{
    $handle=opendir("ressources/".$login."_".$id_user."/ressources/QcmScormTrous/BanqueQuizzATrous");
    while ($fiche = readdir($handle))
    {
       if (strstr($fiche,'.xml'))
       {
          $banqueOk = 1;
          break;
       }
    }
    closedir($handle);
}
function listDir($path , $recursive=TRUE)
{
  GLOBAL $k,$change,$tourniquet,$banqueOk,$result_nb;
  $tourniquet = 1;
  $result = 0;
  if (!is_dir($path) || !is_readable($path))
     return 0;
  $fd = dir($path);
  while($file = $fd->read())
  {
   if(($file != ".") && ($file != ".."))
   {
      if (@is_dir("$path$file/"))
      {
           $change = (isset($_SESSION['data1']) && $_SESSION['data1'] == $file) ? 0 : 1 ;
           $_SESSION['data1'] = $file;
           $_SESSION['okFile'] = 1;
           $result_nb += $recursive?listDir("$path$file/"):0;
           if ($tourniquet == 0)
              $tourniquet++;
      }
      else
      {

            if (strstr($file,'.html'))
            {
               $k++;
               if ($k == 1)
               {
                   $data = '<tr><td colspan="2"><div id="modif" class="SOUS_TITRE" '.
                            'style="width:250px;cursor:pointer;clear:both;float:left;margin-bottom:2px;" '.
                            'onClick="$(\'#webwidget_vertical_menu\').toggle();$(\'#formCreate\').css(\'display\',\'none\');" '.
                            'title="Cliquez ici afficher ou cacher les questionnaires à modifier">'.
                            'Modifier les questionnaires existants</div>';
                   $data .= '<div id="webwidget_vertical_menu" class="webwidget_vertical_menu" style="display:none;float:left;">';
               }
               $NomFile = str_replace('.html','',"$file");
               $NomPathFile = str_replace('.html','',"$path$file");
               if ($tourniquet ==  0)
               {
                  $data .= '<li><a HREF="qcmGereTrouScorm.php?file='.$NomPathFile.'&keepThis=true&TB_iframe=true&height=500&width=720" '.
                           ' onClick= "$(\'#webwidget_vertical_menu\').css(\'display\',\'none\');" '.
                           'class="thickbox"  title = "Cliquez ici pour modifier ce questionnaire" '.
                           'name="Modification du questionnaire: '.$NomFile.'">'.$NomFile.' </a></li>';
               }
               else
               {
                  if ($k > 1)
                     $data = '</ul></li></ul>';
                     if ($banqueOk == 1)
                         $ajoutQuest = '<a HREF="qcmTrousScorm.php?LeDossier='.$_SESSION['data1'].'&NewFile='.$NomPathFile.'&keepThis=true&TB_iframe=true&height=500&width=720" '.
                                   ' onClick= "$(\'#webwidget_vertical_menu\').css(\'display\',\'none\');" '.
                                   'class="thickbox" name="Ajouter un questionnaire existant à ce Quizz" '.$_SESSION['data1'].
                                   ' title = "Ajouter un questionnaire existant à ce Quizz">';
                     else
                         $ajoutQuest = '<a HREF="javascript:void(0);">';
                  $data .= '<ul>';
                  $data .= '<li style="cursor:pointer;">'.$ajoutQuest.'&nbsp;&nbsp; Qcm : '.$_SESSION['data1'].'</a>';
                  $data .= '<ul><li><a HREF="qcmGereTrouScorm.php?file='.$NomPathFile.'&keepThis=true&TB_iframe=true&height=500&width=720" '.
                           ' onClick= "$(\'#webwidget_vertical_menu\').css(\'display\',\'none\');" '.
                           'class="thickbox" title = "Cliquez ici pour modifier ce questionnaire"'.
                           ' name="Modification du questionnaire: '.$NomFile.'">'.$NomFile.' </a></li>';
                  $tourniquet = 0;

               }

            }
           $result_nb++;
      }
   }
  }
  $fd->close();
  if (!empty($_SESSION['data1']))
     unset($_SESSION['data1']);
     if (isset($data))
        echo $data;
}
if (!empty($_GET['abandon']) && $_GET['abandon'] == 1)
{
    $dirVir ="ressources/".$login."_".$id_user."/ressources/QcmScormTrous/";
    $handle=opendir($dirVir);
    $iv = 0;
    while (is_dir && $fiche = readdir($handle))
    {
       if ($fiche == $_SESSION['Folder'])
          $iv++;
    }
    closedir($handle);
    if ($iv > 0)
       vireDir($dirVir.$_SESSION['Folder'],$s_exp);
    unset($_SESSION['Folder']);
}
if (!empty($_GET['creation']) && $_GET['creation'] == 1)
{
    $dir="ressources/".$login."_".$id_user."/ressources/";
    $nom_final = "QcmScormTrous";
    $handle=opendir($dir);
    $i = 0;
    while ($fiche = readdir($handle))
    {
       if ($fiche == $nom_final)
         $i++;
    }
    if ($i == 0)
    {
       $create_rep = $dir."QcmScormTrous";
       mkdir($create_rep,0775);
       chmod($create_rep,0775);
       $create_rep = $dir."QcmScormTrous/BanqueQuizzATrous";
       mkdir($create_rep,0775);
       chmod($create_rep,0775);
    }
    closedir($handle);
    $dir="ressources/".$login."_".$id_user."/ressources/QcmScormTrous/";
    $nom_final = "BanqueQuizzATrous";
    $handle=opendir($dir);
    $i = 0;
    while ($fiche = readdir($handle))
    {
       if ($fiche == $nom_final)
         $i++;
    }
    if ($i == 0)
    {
       $create_rep = $dir."BanqueQuizzATrous";
       mkdir($create_rep,0775);
       chmod($create_rep,0775);
    }
    closedir($handle);
    $dir = "ressources/".$login."_".$id_user."/ressources/QcmScormTrous/";
    listDir($dir , $recursive=TRUE);
    if ($_SESSION['okFile'] == 1)
    {

       echo '</ul></li></ul></div></td></tr><tr><td colspan="2" style="padding:5px 0 0 5px;"></td></tr>';
       unset($_SESSION['okFile']);
       echo '<tr><td colspan="2" class="tDTable"><div id="Creatif" class="SOUS_TITRE" '.
            'style="width:250px;cursor:pointer;clear:both;float:left;margin-bottom:2px;" '.
            'onClick="$(\'#formCreate\').toggle();$(\'#webwidget_vertical_menu\').css(\'display\',\'none\');" '.
            'title="Cliquez ici pour afficher/cacher le formulaire de création d\'un nouveau questionnaire">'.
            'Créer un nouveau questionnaire</div>';
    }
    ?>
    <tr><td colspan="2" class="tDTable"><div id="Creatif">
            <table class="tableForm" id="formCreate" style="display:block;"><tbody>
    <FORM id='creer' name='creer' action="qcmTrousScorm.php" method="POST">
      <INPUT type="HIDDEN" name="creationQcm" value="1">
      <TR><TD colspan="2" align="left"><div id="averti" class="SOUS_TITRE"<?php  echo $AvertQcm1;?></div></TD><TD>
      <TR><TD align="left"><B><?php  echo $mess_cqcm_tit_qcm;?></B></TD><TD>
      <INPUT TYPE="text" class="INPUT"  name="titre" align="left" size="80" value=""
            title="<?php  echo $mess_cqcm_tit_qcm;?>"></TD></TR>
      <?php
      if (empty($_SESSION['Folder'])){ ?>
      <TR><TD align="left"><B><?php  echo "Nom du dossier";?></B></TD><TD align="left">
      <INPUT TYPE="text" class="INPUT"  name="nomDossier" align="left" size="15" maxlength="15" value=""
            title="<?php  echo "Nom du dossier dans lequel vous allez créer le Qcm Scorm et ses dépendances (sans accents et sans espaces)";?>"></TD></TR>
      <?php
      }
      ?>
      <TR><TD align="left"><B><?php  echo "Nom du fichier (sans extension)";?></B></TD><TD align="left">
      <INPUT TYPE="text" class="INPUT"  name="nomFichier" align="left" size="20" maxlength="20" value=""
            title="<?php  echo "Nom du fichier pour cette activité (sans accents et sans espaces)";?>">.xml</TD></TR>
      <TR><TD align="left"><B><?php  echo "MasteryScore" ;?></B></TD><TD align="left">
      <INPUT TYPE="text" class="INPUT" name="moyenne" align="left" size="2" maxlength="2" value=""
           title="<?php  echo "Le MasteryScore est la note sur 100 à partir ".
           "de laquelle le QCM sera-t'il considéré comme acquis." ;?>"></TD></TR>
      <?php
      echo "<tr><td class=\"leTD\"><A HREF=\"javascript:checkFormQcm(document.creer);\" class = 'bouton_new'>".
            "Créer le questionnaire</A></td>";
      if (!empty($_SESSION['Folder']))
          echo "<td class=\"leTD\"><A HREF=\"qcmTrousScorm.php?abandon=1&creation=1;\" class = 'bouton_new' ".
               "title=\"Attention!! cliquez sur ce bouton supprime tout ce ".
               "que vous avez entrepris jusqu'à présent n le quizz en cours\">".
               "Abandonner le questionnaire en cours</A></td></tr></FORM></tbody></table></div>";
      echo "</td></tr>";
    }
if (!empty($_POST['creationQcm']) && $_POST['creationQcm'] == 1)
{
    if (empty($_SESSION['Folder']))
    {
       $nom_final = modif_nom($_POST['nomDossier']);
       $_SESSION['Folder'] = $nom_final;
       $dir = "ressources/".$login."_".$id_user."/ressources/QcmScormTrous/";
       $handle=opendir($dir);
       $j = 0;
       while (is_dir && $fiche = readdir($handle))
       {
              if ($fiche == $nom_final)
                  $j++;
       }
       if ($j == 0)
       {
           $create_rep = $dir.$nom_final;
           mkdir($create_rep,0775);
           chmod($create_rep,0775);
       }
    }
    else
       $nom_final = $_SESSION['Folder'];
    $dir = "ressources/".$login."_".$id_user."/ressources/QcmScormTrous/$nom_final/";
    $_SESSION['RepScorm'] = $dir;
    $nom_final = modif_nom(str_replace('.xml','',$_POST['nomFichier']));
    $handle=opendir($dir);
    $k = 0;$l=0;$NbrQ=1;
    while (false !== ($fiche = readdir($handle)))
    {
       if ($fiche == $nom_final.'.xml')
         $k++;
       if (strstr($fiche,$nom_final))
         $l++;
       if (strstr($fiche,'.xml'))
         $NbrQ++;
    }
    closedir($handle);
    $nom_fichier = ($k > 0) ? $nom_final."_$l.xml" : $nom_final.'.xml';
    $i=0;$j=0;$l=0;
    $lentete = new exp_xml;
    $afficher = $lentete->entete_qcm($dte,$auteur,$_SESSION['typ_user'],$adresse_http);
    $contenuExp = $afficher;
    $contenuExp .= exp_xml::tagDebQuestion_qcm(strip_tags(clean_text($_POST['titre'])),$_POST['moyenne'],$NbrQ);
    $fp = fopen($_SESSION['RepScorm'].$nom_fichier, "c+");
       $fw = fwrite($fp, $contenuExp);
    fclose($fp);
    $_SESSION['QcmFile'] = $_SESSION['RepScorm'].$nom_fichier;
    $_SESSION['QcmTitre'] = strip_tags(clean_text($_POST['titre']));
    chmod ($dir.$nom_fichier, 0775);
    echo '<tr><td><div class="SOUS_TITRE" style="width:650px;margin-bottom:10px;">'.$AvertQcm.'</div></td></tr>';
    echo '<tr><td><a HREF="qcmTrousScorm.php?createQ=1" class="bouton_new" title = "Cliquez ici pour créer votre première question">'.
         'Première question du questionnaire</a></td></tr>';

}
if (!empty($_GET['createQ']) && $_GET['createQ'] == 1)
{
    $dir = $_SESSION['QcmFile'];
    $fl = fopen($dir,"r");
    $i=1;
    while (!feof($fl))
    {
      $ligne = fgets($fl, 4096);
      if (strstr($ligne, '</interaction>'))
         $i++;
    }
    fclose ($fl);
    $newId = $i;
    ?>
    <tr><td colspan='2' class="tDTable">
    <div class="SOUS_TITRE" style="width:650px;margin-bottom:10px;"><?php  echo $AvertQcm;?></div>
    <table class="tableForm"><tbody>
    <FORM id='question' name='question' action="qcmTrousScorm.php" method="POST">
      <INPUT type="HIDDEN" name="createQcm" value="1">
      <INPUT type="HIDDEN" name="numero" value="<?php echo $newId;?>">
      <TR><TD align="left"><B><?php  echo "Intitulé de la question";?></B></TD><TD align="left">
      <INPUT TYPE="text" class="INPUT"  name="intitule" align="left" size="100" value=""
            title="<?php  echo "Insérez une série de trois points minimum en lieu et place du mot ou des mots à positionner";?>"></TD></TR>
      <TR><TD align="left"><B><?php  echo "Poids de la question dans cet exercice";?></B></TD><TD align="left">
      <INPUT TYPE="text" class="INPUT"  name="poids" align="left" size="1" maxlength="1" value=""
            title="<?php  echo "Coefficient pour le calcul de la moyenne de l'exercice";?>"></TD></TR>
      <TR><TD colspan='2' align="center"><B><u>Choisissez une réponse correcte parmi les quatre propositions ci-dessous</u></B></TD></TR>
      <TR><TD align="left"><B><?php  echo "Première proposition" ;?></B></TD><TD align="left">
      <INPUT TYPE="text" class="INPUT" name="one" align="left" size="50" value=""
           title="<?php  echo "Pour plus d'un mot dans la phrase, séparez les mots avec un espace + '/'" ;?>">&nbsp;&nbsp;
      <INPUT TYPE="radio" name="oneC" value='1' checked /></TD></TR>
      <TR><TD align="left"><B><?php  echo "Deuxième  proposition" ;?></B></TD><TD align="left">
      <INPUT TYPE="text" class="INPUT" name="two" align="left" size="50" value=""
           title="<?php  echo "Pour plus d'un mot dans la phrase, séparez les mots avec un espace +  '/'" ;?>">&nbsp;&nbsp;
      <INPUT TYPE="radio" name="oneC" value='2' /></TD></TR>
      <TR><TD align="left"><B><?php  echo "Troisième  proposition" ;?></B></TD><TD align="left">
      <INPUT TYPE="text" class="INPUT" name="three" align="left" size="50" value=""
           title="<?php  echo "Pour plus d'un mot dans la phrase, séparez les mots avec un espace +  '/'" ;?>">&nbsp;&nbsp;
      <INPUT TYPE="radio" name="oneC" value='3' /></TD></TR>
      <TR><TD align="left"><B><?php  echo "Quatrième  proposition" ;?></B></TD><TD align="left">
      <INPUT TYPE="text" class="INPUT" name="four" align="left" size="50" value=""
           title="<?php  echo "Pour plus d'un mot dans la phrase, séparez les mots avec un espace +  '/'" ;?>">&nbsp;&nbsp;
      <INPUT TYPE="radio" name="oneC" value='4' /></TD></TR>
      <tr><td align="left"><A HREF="javascript:checkFormQuestion(document.question);">
          <IMG SRC="images/fiche_identite/boutvalid.gif" BORDER='0'></A></td></tr></FORM></tbody></table>
<?php
}
if (!empty($_POST['createQcm']) && $_POST['createQcm'] == 1)
{
    $Num = $_POST['numero'];
    $contenuExp = '    <interaction id="'.$_POST['numero'].'" label="'.stripslashes(utf8_encode($_POST['intitule'])).
                  '" type="choice" weighting="'.$_POST['poids'].'">'."\n";
    $contenuExp .= '        <choice label="'.stripslashes(utf8_encode($_POST['one'])).'"';
    if ($_POST['oneC'] == 1)
        $contenuExp .= '  correct="true"';
    $contenuExp .= " />\n";
    $contenuExp .= '        <choice label="'.stripslashes(utf8_encode($_POST['two'])).'"';
    if ($_POST['oneC'] == 2)
        $contenuExp .= '  correct="true"';
    $contenuExp .= " />\n";
    $contenuExp .= '        <choice label="'.stripslashes(utf8_encode($_POST['three'])).'"';
    if ($_POST['oneC'] == 3)
        $contenuExp .= '  correct="true"';
    $contenuExp .= " />\n";
    $contenuExp .= '        <choice label="'.stripslashes(utf8_encode($_POST['four'])).'"';
    if ($_POST['oneC'] == 4)
        $contenuExp .= '  correct="true"';
    $contenuExp .= " />\n";
    $contenuExp .= "    </interaction>\n";
    $fp = fopen($_SESSION['QcmFile'], "a+");
       $fw = fwrite($fp, $contenuExp);
    fclose($fp);
    if (!empty($_SESSION['QcmTitre']))
    {
        echo '<tr><td colspan="2" class="leTD">Vous êtes en mode "création de questions".'.
             'pour l\'exercice intitulé <B>'.$_SESSION['QcmTitre'].'</B></td></tr>';
    }
    echo '<tr><td><a HREF="qcmTrousScorm.php?createQ=1" class="bouton_new" title="">'.
         'Ajouter la question n°'.($_POST['numero']+1).'</a></td></tr>';
    if ($_POST['numero'] > 2)
    {
        echo '<tr><td colspan="2" class="leTD"><B>Vous pouvez créer un autre exercice.'.
             'qui fera partie d\'un QCM à multiples activités<br /> pour '.
             'lesquelles vous pourrez ou non appliquer un principe de pré-recquis<br />'.
             'Il faudra au préalable finaliser le Qcm courant.</B></td></tr>';
        echo '<tr><td><a HREF="qcmTrousScorm.php?endQ=1" class="bouton_new" title="Cliquez ici pour clore cet exercice.">'.
             'Finaliser le questionnaire courant</a></td></tr>';
    }
}
if (!empty($_GET['endQ']) && $_GET['endQ'] == 1)
{
    $dir = $_SESSION['QcmFile'];
    $fl = fopen($dir,"r");
    $x=0;
    while (!feof($fl))
    {
      $ligne = fgets($fl, 4096);
      if (strstr($ligne, '</evaluation>'))
         $x++;
    }
    fclose ($fl);
    if ($x == 0)
    {
        $contenuExp = "</evaluation>";
        $fp = fopen($_SESSION['QcmFile'], "a+");
              $fw = fwrite($fp, $contenuExp);
        fclose($fp);
    }
    $dir = $_SESSION['RepScorm'];
    $handle=opendir($dir);
    while (false !== ($fiche = readdir($handle)))
    {
       if (strstr($fiche,'.xml'))
       {
         $_SESSION['final'] = 1;
       }

    }
    closedir($handle);

    $fileHtml = exp_xml::create_html_trous($_SESSION['QcmFile']);
    $dir = str_replace('xml','html',$_SESSION['QcmFile']);
    $fp = fopen($dir,"a+");
       $fw = fwrite($fp, $fileHtml);
    fclose($fp);
    echo '<tr><td colspan="2" class="leTD">Vous venez de clore un exercice Qcm intitulé <B><u>"'.
         $_SESSION['QcmTitre'].'"</u></B></td></tr>';
    echo '<tr><td colspan="2" class="leTD"><B>Vous pouvez créer un autre exercice '.
             'qui fera partie d\'un QCM à multiples activités<br /> pour '.
             'lesqueslles vous pourrez ou non appliquer un principe de pré-recquis.</B></td></tr>';
    echo '<tr><td class="leTD"><a HREF="qcmTrousScorm.php?creation=1" class="bouton_new" '.
         'title="Cliquez ici pour créer un autre exercice.">Nouvel exercice</a></td></tr>';

    if (!empty($_SESSION['final']) && $_SESSION['final'] == 1)
    {
            echo '<tr><td colspan="2" class="leTD"><B>Vous pouvez finaliser la création des questions.</B></td></tr>';
            echo '<tr><td class="leTD"><a HREF="qcmTrousScorm.php?CreeM=1" class="bouton_new" '.
                 'title="Cliquez ici pour créer l\'archive Scorm.">Créer le Zip de ce Qcm à trous</a></td></tr>';
    }
}
if (!empty($_GET['CreeM']) && $_GET['CreeM'] == 1)
{
?>
    <tr><td colspan='2' class="tDTable"><table class="tableForm"><tbody>
    <FORM id='creerM' name='creerM' action="qcmTrousScorm.php" method="POST">
      <INPUT type="HIDDEN" name="creationManifest" value="1">
      <TR><TD><B><?php  echo "Titre du Qcm que vous désirez générer";?></B></TD><TD>
      <INPUT TYPE="text" class="INPUT"  name="titre" align="left" size="40"  maxlength="40" value=""
            title="<?php  echo "Choisissez un titre court( < 40 caractères) et explicite car ce sera le titre la séquence";?>"></TD></TR>
      <TR><TD><B><?php  echo "Mots clefs de ce Qcm";?></B></TD><TD>
      <INPUT TYPE="text" class="INPUT"  name="motscles" align="left" size="80" maxlength="80" value=""
            title="<?php  echo "Séparés par des virgules";?>"></TD></TR></FORM></tbody></table>
      <?php
      echo "<tr><td class=\"leTD\"><A HREF=\"javascript:checkFormCreerM(document.creerM);\">".
            "<IMG SRC=\"images/fiche_identite/boutvalid.gif\" BORDER='0'></A></td></tr></FORM></tbody></table>";
}
if (!empty($_POST['creationManifest']) && $_POST['creationManifest'] == 1)
{
    $handle=opendir($_SESSION['RepScorm']);
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
    $manifest .= exp_xml::metaTrous($_SESSION['email_user'],$_POST['titre'],$_POST['motscles'],$adresse_http);
    $manifest .= "<organizations default=\"M0\">\n".
                 "    <organization identifier=\"M0\" structure=\"hierarchical\">\n".
                 "        <title>".stripslashes(utf8_encode($_POST['titre']))."</title>\n";
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
    $dir = $_SESSION['RepScorm'];
    $fp = fopen($_SESSION['RepScorm']."imsmanifest.xml", "w");
       $fw = fwrite($fp, $manifest);
    fclose($fp);
    $handle1=opendir("QcmScorm");
    while ($fiche = readdir($handle1))
    {
       if ($fiche != '.' && $fiche != '..')
          copy ("QcmScorm/".$fiche,$_SESSION['RepScorm'].$fiche);
    }
    closedir($handle1);
    $nom = modif_nom($_POST['titre']);
    include_once("class/archive.inc.php");
    $zipper = new zip_file("../".$nom.".zip");
    $dossier = substr($_SESSION['RepScorm'], 0, -1);
    $zipper->set_options(array('basedir'=>$dossier));
    $handle=opendir($dossier);
    while ($fiche = readdir($handle))
    {
       if ($fiche != '.' && $fiche != '..')
          $zipper->add_files($fiche);
    }
    $zipper->create_archive();
    closedir($handle);
    $dir = $_SESSION['RepScorm'];
    $handle1=opendir($dir);
    while ($fiche = readdir($handle1))
    {
       if ($fiche != '.' && $fiche != '..')
       {
          if (!file_exists("ressources/".$login."_".$id_user."/ressources/QcmScormTrous/BanqueQuizzATrous/".$fiche) && strstr($fiche,'.xml') && !strstr($fiche,'manifest'))
             copy ($_SESSION['RepScorm'].$fiche,"ressources/".$login."_".$id_user."/ressources/QcmScormTrous/BanqueQuizzATrous/".$fiche);
       }
    }
    closedir($handle1);
    unset($_SESSION['QcmFile']);
    unset($_SESSION['QcmTitre']);
    unset($_SESSION['Folder']);
    unset($_SESSION['RepScorm']);
    echo '<tr><td colspan="2" class="leTD"><B>Vous allez créer le Manifeste qui gèrera votre questionnaire.'.
         '<br /Cela assemblera tous les exercices créés précédemment</B></td></tr>';
    echo '<tr><td class="leTD">'.$bouton_gauche.'<a href="qcmTrousScorm.php?zipQ=1&nom='.$nom.'" '.
         'title="Cliquez ici assembler vos questionnaires<br />'.
         'Attention !!! Notez que ce dossier est supprimé dès la création de l\'archive.">'.
         'Télécharger le Zip de ce Qcm à trous</a>'.$bouton_droite.'</td></tr>';
}
if (isset($_GET['NewFile']) && file_exists("ressources/".$login."_".$id_user."/ressources/QcmScormTrous/BanqueQuizzATrous"))
{
    echo '<tr><td colspan="2" class="leTD">'.
         '<div class="tDajout">'.
         'En cliquant sur un questionnaire, vous l\'ajouterez automatiquement.'.
         '<br />Après cela vous serez obligés de l\'afficher en mode MODIFICATION et devrez regénérer l\'archive.<br />'.
         'Une étiquette vous donne le nombre de questions composant chaque questionnaire.</div></td></tr>';
    echo '<tr><td  class="leTD" style="overflow:auto;height:30px;">';
    $CountFiche = 0;
    $handle=opendir("ressources/".$login."_".$id_user."/ressources/QcmScormTrous/BanqueQuizzATrous");
    while ($fiche = readdir($handle))
    {
       if ($fiche != '.' && $fiche != '..' && !file_exists("ressources/".$login."_".$id_user."/ressources/QcmScormTrous/".$_GET['LeDossier']."/$fiche"))
       {
            $LeMot = '</interaction>';
            $LeContenu = file_get_contents("ressources/".$login."_".$id_user."/ressources/QcmScormTrous/BanqueQuizzATrous/".$fiche);
            $Combien = substr_count($LeContenu, $LeMot);
            echo   '<div style="margin:8px;padding:2px;float:left;">'.
                   '<a class="bouton_new" href="qcmTrousScorm.php?CopyFile='.$fiche.'&LeDossier='.$_GET['LeDossier'].
                   '" title="Cliquez pour ajouter ce questionnaire au Quizz '.
                   $_GET['LeDossier'].' <b>Attention !</b> Il comporte '.$Combien.' questions">'.
                   str_replace(".xml","",$fiche).'</a></div>';
            $CountFiche++;
       }
    }
    closedir($handle);
    if ($CountFiche == 0)
        echo '<div class="tDajout">'.
             'Il n\'existe dans la banque de questionnaires aucun autre quizz abouti à partir duquel vous pouvez piocher.'.
             '<br /> Il faut en créer d\'autres au préalable afin d\'alimenter votre banque.</div>';
    echo '</td></tr>';
}
if (isset($_GET['CopyFile']) && isset($_GET['LeDossier']))
{
            copy ("ressources/".$login."_".$id_user."/ressources/QcmScormTrous/BanqueQuizzATrous/".$_GET['CopyFile'],"ressources/".$login."_".$id_user."/ressources/QcmScormTrous/".$_GET['LeDossier']."/".$_GET['CopyFile']);
            $fileHtml = exp_xml::create_html_trous($_GET['CopyFile']);
            $dir = str_replace('xml','html',"ressources/".$login."_".$id_user."/ressources/QcmScormTrous/".$_GET['LeDossier']."/".$_GET['CopyFile']);
            $fp = fopen($dir,"w");
              $fw = fwrite($fp, $fileHtml);
            fclose($fp);
   echo "<script language=\"JavaScript\">";
      echo "document.location.replace('qcmGereTrouScorm.php?Ajout=1&LeDossier=".$_GET['LeDossier']."&LeFichier=".str_replace('.xml','',$_GET['CopyFile'])."&file=ressources/admin_1/ressources/QcmScormTrous/".$_GET['LeDossier']."/".str_replace('.xml','',$_GET['CopyFile'])."&keepThis=true&TB_iframe=true&height=500&width=720')";
   echo "</script>";

}

echo fin_tableau('');
echo '<div id="mien" class="cms"></div>';
?>
</body></html>