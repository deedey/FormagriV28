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
dbConnect();
if ($lg == "fr")
   setlocale(LC_TIME,'fr_FR');
elseif($lg == "ru")
   setlocale(LC_TIME,'ru_RU');
$date_cour = date ("Y-n-d");
$ch_dt= explode ("-",$date_cour);
$dte = strftime("%A %e %B %Y", mktime(0, 0, 0, $ch_dt[1],$ch_dt[2],$ch_dt[0]));
$auteur = stripslashes($_SESSION['prename_user']).' '.stripslashes($_SESSION['name_user']);
$content = '';
include ("style.inc.php");
if (isset($_GET['action']))
    $action = $_GET['action'];
elseif (isset($_POST['action']))
    $action = $_POST['action'];
if (isset($_GET['idParam']))
    $idParam = $_GET['idParam'];
elseif (isset($_POST['idParam']))
    $idParam = $_POST['idParam'];
if (isset($_GET['idQuest']))
    $idQuest = $_GET['idQuest'];
elseif (isset($_POST['idQuest']))
    $idQuest = $_POST['idQuest'];
?>
<style>
.bouton_vert{font-family: arial;font-weight: bold;float: left;color: #FFFFFF;padding-top: 3px;padding-bottom: 2px;padding-left: 5px;padding-right: 5px;border: 0px solid #24677A;background-image:url(images/ecran-annonce/ongl01.gif);}
.bouton_hover{font-family: arial;font-weight: bold;color: #D45211;float: left;text-align: right;padding-top: 2px;padding-bottom: 1px;padding-left: 4px;padding-right: 4px;border: 1px solid #24677A;background-image:url(images/ecran-annonce/ongl02.gif);}
#listQuest{
    clear:both;float:left;text-align:left;margin:2px 0 2px 8px;padding:0 4px 0 4px;
    min-width:20%;max-width:98%;border:1px solid #24677A;background-color: #eee;
}
.leTD { padding-top: 5px;padding-bottom: 5px;margin-top: 5px;margin-bottom: 5px;}
.tDTable{padding:2px;}
.pereQcm{clear:both;float:left;font-weight:bold;padding:2px 4px 0 0;}
.tableForm{background-color:#eee;padding:2px;border:1px solid #24677A;}
</style>
<SCRIPT language=JavaScript>
      function checkFormCreation(frm)
      {
               var ErrMsg = "<?php echo $mess_info_no;?>\n\n";
               var lenInit = ErrMsg.length;
               if (isEmpty(frm.question)==true)
                  ErrMsg += ' - <?php echo $msq_titre;?>\n';
               if (isEmpty(frm.note)==true)
                  ErrMsg += ' - <?php echo "Note à attribuer";?>\n';
               if (ErrMsg.length > lenInit)
                  alert(ErrMsg);
               else
                  frm.submit();
      }
      function checkFormModif(frm)
      {
               var ErrMsg = "<?php echo $mess_info_no;?>\n\n";
               var lenInit = ErrMsg.length;
               if (isEmpty(frm.titre)==true)
                   ErrMsg += ' - <?php echo $msq_titre;?>\n';
               if (isEmpty(frm.moyenne)==true)
                   ErrMsg += ' - <?php echo $mess_moy_qcm;?>\n';
               if (isEmpty(frm.moyenne)==false && isNaN(frm.moyenne.value))
                  ErrMsg += ' - <?php echo "Donnez une note inférieure à 20 et supérieure à 0";?>\n';
               if (isEmpty(frm.horaire)==true && isEmpty(frm.minutes)==true)
                   ErrMsg += ' - <?php echo $msq_horaire_act_form;?>\n';
               if (ErrMsg.length > lenInit)
                   alert(ErrMsg);
               else
                   frm.submit();
      }
      function checkForm1(frm) {
               var ErrMsg = "<?php echo $mess_info_no;?>\n\n";
               var lenInit = ErrMsg.length;
               if (isEmpty(frm.search)==true)
                  ErrMsg += " - Vous n'avez choisi aucune question\n";
               if (isEmpty(frm.search)== false && frm.idQuest == undefined)
                  ErrMsg += " - <?php echo "Ce titre n'est pas le résultat d'une recherche.";?>\n";
               if (ErrMsg.length > lenInit)
                  alert(ErrMsg);
               else
                   frm.submit();
      }
      function isEmpty(elm)
      {
               var elmstr = elm.value + "";
               if (elmstr.length == 0)
                  return true;
               return false;
      }
</SCRIPT>
<?php
 $Klik = "class='bouton_vert' ".
        "onMouseOver=\"\$(this).removeClass();\$(this).addClass('bouton_hover');\" ".
        "onMouseOut=\"\$(this).removeClass();\$(this).addClass('bouton_vert');\"";
 $AvertQcm = "<b>Rappel:</b><br />Vous allez créer un Qcm sur Formagri. ".
            "Cette page vous permet de saisir les informations relatives au QCM. ".
            "Vous pourrez ensuite soit créer des questions à choix simple ou multiple, ".
            "ou encore en choisir dans votre banque de questions à condition que vous en ayez déjà ".
            "créées auparavant. Vous n'êtes pas autorisés pour l'instant du moins à choisir ".
            "une question parmi celles créées par vos collègues. Ceci viendra dans un deuxième temps ".
            "lorsque seront mises en place les procédures d'autorisation.";
if (isset($action) && $action == 'ajoutQuestion')
{
    $idLinker =  Donne_ID ($connect,"SELECT max(qcmlinker_cdn) from qcm_linker");
    $idNumber =  Donne_ID ($connect,"SELECT max(qcmlinker_number_no) from qcm_linker where qcmlinker_param_no = '".$idParam."'");
    $ReqInsert = mysql_query("insert into qcm_linker values($idLinker,'".$idParam."','".$idQuest."','".$idNumber."')");
    $NbQuest = mysql_result(mysql_query("select count(*) from qcm_linker where qcmlinker_param_no = '".$idParam."'"),0);
    $ReqInsert = mysql_query("update qcm_param set n_pages = '$NbQuest' where ordre='$idParam'");
}
if (isset($_GET['suppQuest']) && $_GET['suppQuest'] == '1')
{
    $ReqDel = mysql_query("delete from qcm_linker where qcmlinker_param_no='$idParam' and qcmlinker_data_no='$idQuest'");
    $requete = mysql_query("select * from qcm_linker where qcmlinker_param_no = '".$idParam."' order by qcmlinker_cdn");
    if ($requete == true && mysql_num_rows($requete) > 0)
    {
          $nbQuest = mysql_num_rows($requete);
          for($i = 1; $i < ($nbQuest+1);$i++)
          {
             $l = $i-1;
             $idUpdt = mysql_result($requete,$l,'qcmlinker_cdn');
             $req_updt = mysql_query("update qcm_linker set qcmlinker_number_no = '".$i."' where qcmlinker_cdn = '".$idUpdt."'");
          }
          $req_updt = mysql_query("update qcm_param set n_pages = '".$nbQuest."' where ordre = '".$idParam."'");
    }
    $action = 'CreateQuestion';
    $NbAff = 3;
}
if (isset($action) && $action == 'insert')
{
    $duree = (isset($_POST['horaire']) && $_POST['horaire'] > 0) ?  ($_POST['horaire']*60 + $_POST['minutes']) : $_POST['minutes'];
    if (!isset($_POST['typeAction']))
    {
        $idParam =  Donne_ID ($connect,"SELECT max(ordre) from qcm_param");
        $requete = mysql_query("insert into qcm_param values('".$idParam."','".$_SESSION['id_user']."','0','".
                                $duree."','0','".NewHtmlentities(strip_tags($_POST['titre']),ENT_QUOTES)."','".$_POST['moyenne']."')");
    }
    elseif(isset($_POST['typeAction']) && $_POST['typeAction'] == 'modifier')
    {
        $requete = mysql_query("update qcm_param set duree='$duree', titre_qcm='".
                   NewHtmlentities(strip_tags($_POST['titre']),ENT_QUOTES)."', moyenne = '".$_POST['moyenne']."' where ordre = '".$idParam."'");
    }
    $content .= '<tr><td colspan="2"><div class="SOUS_TITRE" style="width:600px;margin-bottom:10px;">'.$AvertQcm.'</div><table><tbody>'.
                '<tr><td><B>'.$mess_cqcm_tit_qcm.'</B></TD><TD><div class="input" style="padding:2px;">'.
                stripslashes($_POST['titre']).'</div></td></tr>'.
                '<tr><td><B>'.$mess_moy_qcm.'</B></TD><TD><div class="input" style="padding:2px;">'.
                $_POST['moyenne'].'</div></td></tr>';
    $content .= "<tr><td nowrap><B>$mess_cqcm_tps</B></TD>";
    $content .= "<TD><div class='input' style='padding:2px;'>".$duree." $mn</div></TD>";
    $content .= "</td></tr>";
    $content .= "<tr><td colspan='2'><a href='qcm_create.php?action=modifier&idParam=$idParam' ".
                "title='Modifier les caractéristiques de ce QCM ou Ajouter des questions' ".
                "class='bouton_new'>Gérer ce Qcm</a></td></tr></table>";
    $content .= "</td></tr></table></td></tr></table>";

    echo $content;
  exit();
}
if (isset($action) && ($action == 'updateQuestion' ||  $action == 'InsertQuestion'))
{
        //echo "<pre>";print_r($_POST);echo "</pre>";exit;
        if ($action =='InsertQuestion')
        {
           $idQuest = Donne_ID ($connect,"SELECT max(qcm_data_cdn) from qcm_donnees");
           $NewQcm =  mysql_query ("insert into qcm_donnees (qcm_data_cdn,qcmdata_auteur_no,n_lignes,typ_img,image) values('".
                                   $idQuest."','".$_SESSION['id_user']."','".$_POST['nbProp']."','','non')");
           $idLinker =  Donne_ID ($connect,"SELECT max(qcmlinker_cdn) from qcm_linker");
           $idNumber =  Donne_ID ($connect,"SELECT max(qcmlinker_number_no) from qcm_linker where qcmlinker_param_no = '".$idParam."'");
           $ReqInsert = mysql_query("insert into qcm_linker values($idLinker,'".$idParam."','".$idQuest."','".$idNumber."')");
           $NbQuest = mysql_result(mysql_query("select count(*) from qcm_linker where qcmlinker_param_no = '".$idParam."'"),0);
           $ReqInsert = mysql_query("update qcm_param set n_pages = '$NbQuest' where ordre='$idParam'");
           $action = $_POST['SuiteAction'];
           $newQ = 1;
        }
        if (is_uploaded_file($_FILES['userfile']['tmp_name']) && strstr($_FILES['userfile']['type'],'image'))
        {
            $nom_cours=$_FILES['userfile']['name'];
            $type_img=$_FILES['userfile']['type'];
            $cours_data=file_get_contents($_FILES['userfile']['tmp_name']);
            $type=GetImageSize($_FILES['userfile']['tmp_name']);
            $type_image="";
            if ($type[2]==1)
                $type_image=".gif";
            elseif ($type[2]==2)
                $type_image=".jpg";
            elseif ($type[2]==3)
                $type_image=".png";
            $extension = '_'.time();
            $handle=opendir("ressources");
            $drap=0;
            while ($file = readdir($handle))
            {
                 if ($file == "qcm_images")
                 {
                     $drap=1;
                     break;
                 }
            }
            closedir($handle) ;
            if ($drap == 0)
            {
              mkdir("ressources/qcm_images");
              chmod("ressources/qcm_images",0775);
            }
            $dir_fic = "ressources/qcm_images/";
            $dest_file = "qcm".$extension.$type_image;
            $copier=copy($_FILES['userfile']['tmp_name'] , $dir_fic.$dest_file);
            $requete = mysql_query("update qcm_donnees set typ_img='$type_img', image='qcm_images/$dest_file' where qcm_data_cdn =".$idQuest);
        }
        elseif (!is_uploaded_file($_FILES['userfile']['tmp_name']) && isset($_POST['supp_image']) && $_POST['supp_image'] == 'on')
            $requete = mysql_query("update qcm_donnees set typ_img='', image='non' where qcm_data_cdn =".$idQuest);
        $requete = mysql_query("update qcm_donnees set question='".NewHtmlentities(strip_tags($_POST['question']),ENT_QUOTES)."', multiple='".
                              $_POST['multiple']."',note='".$_POST['note']."',n_lignes='".$_POST['nbProp'].
                              "' where qcm_data_cdn =".$idQuest);
        for ($i = 1;$i < ($_POST['nbProp']+1);$i++)
        {
            $prop = $i."_prop";$propos = $i."_prop";
            $val = $i."_val";
            $cheked = (isset($_POST[$val]) &&  $_POST[$val] == 'on') ? 1 : 0;
            $requete = mysql_query("update qcm_donnees set $prop = '".$_POST[$propos]."', $val = '".
                                   $cheked."' where qcm_data_cdn =".$idQuest);
        }
        if (isset($_SESSION['RepAff']))
           unset($_SESSION['RepAff']);
        $mess_notif = (isset($newQ)) ? "Vous venez de créer la question en cours" : "La question en cours a été modifiée";
}
if (isset($mess_notif) && $mess_notif != '')
   echo notifier($mess_notif);

if (isset($action) && ($action == 'create' || $action == 'modifier'))
{
    if (empty($idParam))
    {

        $reqExist = mysql_query("select * from qcm_param where qcm_auteur_no = ".$_SESSION['id_user']." and n_pages = '0'");
    }
    if ($action == 'modifier')
    {
       $requete = mysql_query("select * from qcm_param where ordre = ".$idParam);
       if (mysql_num_rows($requete) == 1)
       {
          $item = mysql_fetch_object($requete);
          $ordre = $item->ordre;
          $titre = $item->titre_qcm;
          $moyenne = $item->moyenne;
          $duree = $item->duree;
          $minutes = $duree%60;
          $heure = floor($duree/60);
       }
    }
    if (!empty($idParam))
    {
       $content = "<tr><td colspan='2'><table border='0'>";
       $content .= echoList($idParam,1,1,0);
       $content .= "<tr><td colspan=2><div id='chercher' style='display:none;'>";
       $content .= '<form id="rech" name="rech" action="qcm_create.php" method="POST">'.
                   '<input type="hidden" name="action" value="ajoutQuestion">'.
                   '<input type="hidden" name="idParam" value="'.$idParam.'">'.
                   '<div style="clear:both;float:left;">';
       echo $content;
       $content = '';
       $table="qcm_donnees";
       $fieldLabel="question";
       $fieldId="qcm_data_cdn";
       $fieldCond=' AND qcmdata_auteur_no = '.$_SESSION['id_user'];
       $HideLabel="idQuest";
       include ("OutilsJs/DivPopulator/DivPopulator.php");
       $content .= '</div>';
       echo $content;
       $content = '';
       $content .= "<div style='float:left;margin-left:460px;margin-top:10px;'>".
                   "<a HREF=\"javascript:checkForm1(document.rech);\">".
                   "<IMG SRC=\"images/fiche_identite/boutvalid.gif\" BORDER='0'\"></a></div>";
       $content .= "<div style='float:left;margin-left:10px;margin-top:13px;'>".
                   "<a HREF=\"javascript:void(0);\" class='bouton_new' ".
                   "onClick=\"javascript:\$('#chercher').hide();\$('#rech').hide();\$('#modifier').show();\$('#QcmForm').show();\">".
                   "Retour</a></div>";
       $content .= "</form></div></td></tr>";
    }
    if ($action != 'modifier')
        $content .= "<tr><td colspan='2'>".
                    "<div class='SOUS_TITRE' style='clear:both;width:570px;margin:0 20px 10px 10px;'>".
                    $AvertQcm."</div></td></tr>";
    $content .= "<tr><td colspan='2'><div id='QcmForm' style='margin:0 20px 10px 10px;'>".
                "<FORM id='modifier' name='modifier' action=\"qcm_create.php\" method='POST'>";
    if ($action == 'modifier' && !empty($idParam))
    {
          $content .= '<input type="HIDDEN" name="typeAction" value="modifier">';
          $content .= '<input type="HIDDEN" name="idParam" value="'.$idParam.'">';
    }
    $content .= '<input type="HIDDEN" name="action" value="insert">';
    $content .= '<table border="0"><tr><td colspan="2" class="SOUS_TITRE"><TABLE><tbody>
                <tr><TD colspan="2"><B>'.$mess_cqcm_tit_qcm.'</B>
                <input TYPE="text" class="input"  name="titre" align="left" size="60"
                value="'.$titre.'" title="'.$mess_cqcm_tit_qcm.'"></td></tr>
                <tr><td><B>'.$mess_moy_qcm.'</B></TD><TD>
                <input TYPE="text" class="input" name="moyenne" align="left" maxlength="2" size="2"
                value="'.$moyenne.'" title="'.$mess_moy_detail_qcm.'"></td></tr>';
    $content .= "<tr><td nowrap><B>".$mess_cqcm_tps."</B></TD><TD align='left'><TABLE border='0' cellspacing='0'><tr>";
    $content .= "<TD><input TYPE='text' class='input' name='horaire' value='$heure' size='2' maxlength = '3' align='center'>$h </TD>";
    $content .= "<TD><input TYPE='text' class='input' name='minutes' value='$minutes' size='2' maxlength = '2' align='center'>$mn</TD>";
    $content .= "</td></tr></table></td></tr>";
    $content .= "<tr><td style=\"align:'center';height: 40px;\">".
                "<a href=\"javascript:checkFormModif(document.modifier);\">".
                 "<img src='images/fiche_identite/boutvalid.gif' border='0'></A>".
                " </td></tr></tbody></table></td></tr></table></FORM></div>";
    $content .= "</td></tr></table></td></tr></table>";
    echo $content;
  exit();
}
if (isset($action) && ($action == 'modifQuestion' || $action == 'ajoutQuestion' || $action == 'updateQuestion'))
{
   $requete = mysql_query("select * from qcm_donnees where qcm_data_cdn =".$idQuest);
   if (mysql_num_rows($requete) == 1)
   {
      echo "<tr><td colspan='2'><table border='0'>";
      echo echoList($idParam,1,1,$idQuest);
      echo "<tr><td colspan=2><div id='chercher' style='display:none;'>";
      echo '<form id="rech" name="rech" action="qcm_create.php" method="POST">'.
           '<input type="hidden" name="action" value="ajoutQuestion">'.
           '<input type="hidden" name="idParam" value="'.$idParam.'">'.
           '<div style="clear:both;float:left;">';
      $table="qcm_donnees";
      $fieldLabel="question";
      $fieldId="qcm_data_cdn";
      $fieldCond=' AND qcmdata_auteur_no = '.$_SESSION['id_user'];
      $HideLabel="idQuest";
      include ("OutilsJs/DivPopulator/DivPopulator.php");
      echo '</div>';
      echo "<div style='float:left;margin-left:460px;margin-top:10px;'>".
           "<a HREF=\"javascript:checkForm1(document.rech);\">".
           "<IMG SRC=\"images/fiche_identite/boutvalid.gif\" BORDER='0'\"></a></div>";
      echo "<div style='float:left;margin-left:10px;margin-top:13px;'>".
           "<a HREF=\"javascript:void(0);\" class='bouton_new' ".
           "onClick=\"javascript:\$('#chercher').hide();\$('#rech').hide();\$('#creation').show();\$('#QcmForm').show();\">".
           "Retour</a></div>";
      echo "</form></div></td></tr>";
      $itemData = mysql_fetch_object($requete);
      echo "<tr><td colspan=2>".
           "<div id='QcmForm'><FORM id='creation' name='creation' action=\"qcm_create.php\" ".
           "method=\"POST\" enctype=\"multipart/form-data\">";
      echo '<input TYPE="HIDDEN" name="idQuest" value="'.$itemData->qcm_data_cdn.'">'.
           '<input type="hidden" name="idParam" value="'.$idParam.'">';
      echo "<input TYPE=\"HIDDEN\" name='action' value='updateQuestion'>";
      echo '<table border="0"><tr><td colspan="2" class="SOUS_TITRE"><TABLE><tbody>';
      $question = strip_tags($itemData->question);
      $note = $itemData->note;
      $multiple = $itemData->multiple;
      $imager = $itemData->image;
      echo "<tr><td valign='top' width='200px;'>Formulez ici $mess_cqcm_quest_rep</TD>";
      echo '<TD align="left" valign="top">';
      echo '<input TYPE="text" class="input" name="question" size="60" value= "'.
            $question.'" title="'.$mess_cqcm_ins_q.'"></td></tr>';
      echo '<tr><td align="left" colspan=2>'.$mess_cqcm_note1;
      echo '<input TYPE="text" class="input" '.
           'title="<FONT COLOR=red size=1>Attention !! Une réponse en moins là où plusieurs
            réponses sont attendues équivaut à une note de Zéro (0).</font>" name="note" size="2" '.
            'maxlength="2" value= '.$note.' ></td></tr>';
      echo '<tr><td align="left" colspan=2>Image &nbsp;&nbsp;';
      if ($imager != 'non' && is_file("ressources/$imager"))
         echo '<input TYPE="CHECKBOX" name="supp_image" title="'.$mess_ag_supp.'"> &nbsp;&nbsp;';
      echo '<input TYPE="file" class="input" size="45" title="'.$mess_cqcm_img.'" '.
           'name="userfile" enctype="multipart/form-data">';
      if ($imager != 'non' && is_file("ressources/$imager"))
         echo "&nbsp;&nbsp;<img src = 'ressources/$imager' width='35' heigth='35' align='absmiddle' style='padding-bottom:5px;'>";
      echo '</td></tr>';
      echo '<tr><td align="left" colspan=2>'.$mess_cqcm_rep_mult2;
      if ($multiple == 1)
      {
          echo '<input TYPE="RADIO" CHECKED name="multiple" value="1" title="'.$mess_cqcm_rep_mult2.'"></td></tr>';
          echo '<tr><td align="left" colspan=2>'.$mess_cqcm_rep_un2;
          echo '<input TYPE="RADIO" name="multiple" value="0" title="'.$mess_cqcm_rep_un2.'"></td></tr>';
      }
      else
      {
          echo '<input TYPE="RADIO" name="multiple" value="1" title="'.$mess_cqcm_rep_mult2.'"></td></tr>';
          echo '<tr><td align="left" colspan=2>'.$mess_cqcm_rep_un2;
          echo '<input TYPE="RADIO" CHECKED name="multiple" value="0" title="'.$mess_cqcm_rep_un2.'"></td></tr>';
      }
      $j=0;$nbProp = 0;
      for ($prop = 1;$prop < 11;$prop++)
      {
         $propos = $prop."_prop";
         $valeur = $prop."_val";
         if ($itemData->$propos != '')
         {
             echo "<tr style=\"height:30px;\"><td colspan=2>";
             echo "<B>$mess_repatt  $prop</B>".nbsp(4);
             echo "<input TYPE=\"text\" class=\"input\"  name=\"$propos\"  size=\"50\" value=\"".strip_tags($itemData->$propos)."\" ".
                  "maxlength=\"250\" title=\"$mess_cqcm_prop $prop\">".nbsp(4);
             if ($itemData->$valeur == 1)
                 echo "<input TYPE=\"CHECKBOX\" name=\"$valeur\" title=\"$mess_cqcm_rep_ok\" checked><br></td>";
             else
                 echo "<input TYPE=\"CHECKBOX\" name=\"$valeur\" title=\"$mess_cqcm_rep_ok\"><br></td>";
             echo "</tr>";
             $nbProp++;
         }
         $j++;
      }
      echo "<input TYPE=\"HIDDEN\" name=\"nbProp\" value='".$nbProp."'>";
      echo "<tr><td colspan='2'>";
      echo "<div style='clear:both;float:left;margin-left:250px;padding-top:3px;'>".
           "<a href='qcm_create.php?suppQuest=1&idQuest=$idQuest&idParam=$idParam' title='Ôter cette question du QCM en cours'>".
           "<div id='suppQuest' ".$Klik.">Supprimer</div></a></div>";
      echo "<div style='float:left;margin-left:50px;'><a href=\"javascript:checkFormCreation(document.creation);\" ".
            " onClick=\"TinyMCE.prototype.triggerSave();\">".
            "<img src='images/fiche_identite/boutvalid.gif' border='0'></A></div></td></tr></table>
            </FORM></div></td></tr></table>";
      echo "</td></tr></table></td></tr></table></td></tr></table>";
   }
   exit;
}

if (isset($action) && $action == 'CreateQuestion')
{
      echo "<tr><td colspan='2'><table border='0'>";
      echo echoList($idParam,1,0,$idQuest);
      echo "<tr><td colspan=2><div id='chercher' style='display:none;'>";
      echo '<form id="rech" name="rech" action="qcm_create.php" method="POST">'.
           '<input type="hidden" name="action" value="ajoutQuestion">'.
           '<input type="hidden" name="provenance" value="recherche">'.
           '<input type="hidden" name="idParam" value="'.$idParam.'">'.
           '<div style="clear:both;float:left;">';
      $table="qcm_donnees";
      $fieldLabel="question";
      $fieldId="qcm_data_cdn";
      $fieldCond=' AND qcmdata_auteur_no = '.$_SESSION['id_user'];
      $HideLabel="idQuest";
      include ("OutilsJs/DivPopulator/DivPopulator.php");
      echo '</div>';
      echo "<div style='float:left;margin-left:460px;margin-top:10px;'>".
           "<a HREF=\"javascript:checkForm1(document.rech);\">".
           "<IMG SRC=\"images/fiche_identite/boutvalid.gif\" BORDER='0'\"></a></div>";
      echo "<div style='float:left;margin-left:10px;margin-top:13px;'>".
           "<a HREF=\"javascript:void(0);\" class='bouton_new' ".
           "onClick=\"javascript:\$('#chercher').hide();\$('#rech').hide();\$('#creation').show();\$('#QcmForm').show();\">".
           "Retour</a></div>";
      echo "</form></div> </td></tr>";
      echo "<tr><td colspan=2>".
           "<div id='QcmForm'><FORM id='creation' name='creation' action=\"qcm_create.php\" ".
           "method=\"POST\" enctype=\"multipart/form-data\">";
      echo "<input TYPE=\"HIDDEN\" name=\"idParam\" value='$idParam'>";
      echo '<input TYPE="HIDDEN" name="action" value="InsertQuestion">';
      echo '<input TYPE="HIDDEN" name="SuiteAction" value="modifQuestion">';
      echo '<table border="0"><tr><td colspan="2" class="SOUS_TITRE"><TABLE><tbody>';
      echo "<tr><td valign='top' width='200px;'>Formulez ici $mess_cqcm_quest_rep</TD>";
      echo '<TD align="left" valign="top">';
      echo '<input TYPE="text" class="input" name="question" size="60" value= "'.
            strip_tags($question).'"title="'.$mess_cqcm_ins_q.'"></td></tr>';
      echo '<tr><td align="left" colspan=2>'.$mess_cqcm_note1;
      echo '<input TYPE="text" class="input" '.
           'title="<FONT COLOR=red size=1>Attention !! Une réponse en moins là où plusieurs
            réponses sont attendues équivaut à une note de Zéro (0).</font>" name="note" size="2" '.
            'maxlength="2" value="" ></td></tr>';
      echo '<tr><td align="left" colspan=2>Image &nbsp;&nbsp;&nbsp;&nbsp;';
      echo '<input TYPE="file" class="input" size="45" title="'.$mess_cqcm_img.'" '.
           'name="userfile" enctype="multipart/form-data">';
      echo '</td></tr>';
      echo '<tr><td align="left" colspan="2"><span style="padding: 0 10px 8px 0;">'.$mess_cqcm_rep_mult2.'</span>';
      echo '<input TYPE="RADIO" name="multiple"  value="0" title="'.$mess_cqcm_rep_mult2.'"></td></tr>';
      echo '<tr><td align="left" colspan="2"><span style="padding: 0 10px 8px 0;">'.$mess_cqcm_rep_un2.'</span>';
      echo '<input TYPE="RADIO" CHECKED name="multiple" value="1" title="'.$mess_cqcm_rep_un2.'"></td></tr>';
      echo '<tr><td align="left" colspan=2>';
      $j=0;$Nj=0;
      if (!isset($_SESSION['RepAff']))
         $_SESSION['RepAff'] = 3;
      else
         $_SESSION['RepAff'] = $NbAff;
      for ($prop = 1;$prop < 8;$prop++)
      {
         $propos = $prop."_prop";
         $valeur = $prop."_val";
         if ($prop < ($_SESSION['RepAff']+1))
         {
            echo "<div id='$propos' style=\"height:30px;display:block;clear:both;float:left;\">";
            $Nj++;
         }
         else
             echo "<div id='$propos' style=\"height:30px;display:none;clear:both;float:left;\">";
         echo "<div style=\"float:left;padding-top:5px;\"><B>$mess_repatt  $prop</B></div>";
         echo "<div style=\"float:left;padding-left:10px;\"><input TYPE=\"text\" ".
              "class=\"input\" name=\"$propos\"  size=\"50\" value=\"\" ".
              "maxlength=\"250\" title=\"$mess_cqcm_prop $prop\"></div>";
         echo "<div style=\"float:left;padding-left:10px;\"><input TYPE=\"CHECKBOX\" ".
              "name=\"$valeur\" title=\"$mess_cqcm_rep_ok\"></div>";
         if ($prop == $_SESSION['RepAff'])
         {
             echo '<div style="float:left;cursor:pointer;" '.
                  'onClick="document.location.replace(\'qcm_create.php?action=CreateQuestion&idParam='.
                            $idParam.'&NbAff='.($_SESSION['RepAff']-1).'\')">'.
                  '<IMG SRC="images/messagerie/icoGpoubel.gif" height="20" width="15" BORDER="0">'.
                  '</div>';
         }
         echo "</div>";
         $j++;
      }
      echo '<div id="ajtR" style="clear:both;cursor:pointer;" '.$Klik.
           ' onClick="document.location.replace(\'qcm_create.php?action=CreateQuestion&idParam='.
           $idParam.'&NbAff='.($_SESSION['RepAff']+1).'\')">'.
           'Ajouter une réponse supplémentaire'.
           '</div>';
      echo "</td></tr>";
      echo "<input TYPE=\"HIDDEN\" name=\"nbProp\" value='".$_SESSION['RepAff']."'>";
      echo "<tr><td></td><td style=\"align:'center';height: 40px;\">".
           "<a href=\"javascript:checkFormCreation(document.creation);\" ".
            " onClick=\"TinyMCE.prototype.triggerSave();\" ".
            "onmouseover=\"img1.src='images/fiche_identite/boutvalidb$suffixer.gif';return true;\" ".
            "onmouseout=\"img1.src='images/fiche_identite/boutvalid$suffixer.gif'\">".
            "<IMG NAME=\"img1\" SRC=\"images/fiche_identite/boutvalid$suffixer.gif\" BORDER='0' ".
            "onLoad=\"tempImg=new Image(0,0); tempImg.src='images/fiche_identite/boutvalidb$suffixer.gif'\"></A></td></tr></table>
            </FORM></div></td></tr></table></td></tr></table>";
      echo "</td></tr></table></td></tr></table>";
   exit;
}
echo $html.'<div id="mien" class="cms"></div></body></html>';
function echoList($idParam,$search,$img,$idQ)
{
    GLOBAL $ajt;
    $echoList = '<tr><td colspan="2"><div id="listQuest"><a href="qcm_create.php?action=modifier&idParam='.$idParam.'" '.
                'title="Modifier les éléments de base du questionnaire">'.
                '<img src="images/homeQcm.jpg" style="margin-top:2px;border:0;"></a>';
    $request = mysql_query("select * from qcm_linker,qcm_donnees where qcmlinker_param_no = ".$idParam.
                           " and qcmlinker_data_no=qcm_data_cdn order by qcmlinker_number_no");
    $nbQuestions = mysql_num_rows($request);
    if ($nbQuestions > 0)
    {
        for ($i=0;$i<$nbQuestions;$i++)
        {
             $DataNo = mysql_result($request,$i,"qcmlinker_data_no");
             $DataQuest = strip_tags(mysql_result($request,$i,"question"));
             if ($idQ == $DataNo)
                  $ajt = 'color:#D45211 !important;';
             else
                 $ajt = '';
             $echoList .= '<a href="qcm_create.php?action=modifQuestion&idParam='.$idParam.'&idQuest='.$DataNo.'" '.
                          'style="margin:0 0 0 12px;'.$ajt.'" title="Modifier la question <b>'.
                          $DataQuest.'</b>"><span style="padding-bottom:3px;">'.($i+1).'</span></a> ';
        }
    }
    if ($search == 1)
    {
            $echoList .= '<a href="javascript:void(0);" onClick="javascript:
                          $(\'#chercher\').show();
                          $(\'#rech\').show();
                          $(\'#QcmForm\').hide();
                          $(\'#creation\').hide();" '.
                         'style="margin:0 0 0 12px;'.$ajt.'" title="Chercher une question dans votre banque">'.
                         '<img src="images/loupe1.gif" border="0"></a> ';
    }
    if ($img == 1)
    {
            $echoList .= '<a href="qcm_create.php?action=CreateQuestion&idParam='.$idParam.'&NbAff=3" '.
                          'style="margin:0 0 0 12px;'.$ajt.'" title="Créer une nouvelle question">'.
                          '<img src="images/flecheBleue.gif" border="0"></a>';
    }
    $echoList .= '</div></td></tr>';
    return $echoList;
 }
?>