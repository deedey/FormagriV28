<?php
if (!isset($_SESSION)) session_start();
if (!isset($_SESSION['id_user']) || $_SESSION['id_user'] == "")
{
  exit();
}
include ("include/UrlParam2PhpVar.inc.php");
require 'admin.inc.php';
require 'fonction.inc.php';
require "lang$lg.inc.php";
require "fonction_html.inc.php";
require "langues/module.inc.php";
dbConnect();
//............................................................................
//de quel type est l'utilisateur (apprenant, formateur, administrateur)
$nom_user=GetDataField ($connect,"select util_nom_lb from utilisateur where util_cdn='$id_user'","util_nom_lb");
$prenom_user=GetDataField ($connect,"select util_prenom_lb from utilisateur where util_cdn='$id_user'","util_prenom_lb");
include 'style.inc.php';
echo '<div id="mien" class="cms"></div>';
if ($creation == 1)
{
   $variant = ($modifier == 1) ? $ord : $ordre;
   $titrer = GetDataField ($connect,"select titre_qcm from qcm_param where ordre='$variant'","titre_qcm");
   entete_simple($titrer);
}else
   entete_simple($mess_menu_gest_qcm);
echo "<tr><td style=\"align:'center'; height:50px; padding-top:3px;\" valign='top'>";
if ((isset($creation_qcm) && $creation_qcm == 1) || (isset($modification) && $modification == 1) || (isset($supprimer) && $supprimer == 1) || (isset($consulter) && $consulter == 1))
      echo "<A href=\"menu_qcm.php\" class='bouton_new'>$mess_acc</A>";
echo aide_div("qcm",8,0,0,0);
echo "</td></tr>";
echo "<CENTER>";
//...........................................................................
if (isset($venu) && $venu == 'act')
{
   if (isset($_GET['acced']))
   {
       $_SESSION['acced'] = $_GET['acced'];
   }
   if (isset($_GET['params_qcm']))
   {
       $parametres_qcm = $_GET['params_qcm'];
       $_SESSION['parametres_qcm'] = $_GET['params_qcm'];
   }
}
if ($passerelle == 1 )
{
    $passerelle=0;
    $req=mysql_query("SELECT ress_cdn from ressource_new where ress_cat_lb = '$dom'");
    $res_req=mysql_result($req,0);
    $linker = "qcm.php?code=$ordre_code";
    $lien = urlencode("recherche.php?flg=1&doublon=$doublon&rep=$linker&lien_sous_cat=0&parente=$res_req&ajouter=1&code=$ordre_code&org=qcm&id_seq=$id_seq&id_parc=$id_parc&consult=1");
    echo "<script language=\"JavaScript\">";
    echo " document.location.replace(\"trace.php?link=$lien\")";
    echo "</script>";
    exit;
}
if ($creation==1 && $debut==1)
{
  $debut=0;
  $requete=mysql_query("SELECT MAX(ordre) FROM qcm_param where qcm_auteur_no='$id_user'");
  $ordre=mysql_result($requete,0);
}
if (isset($mess_notif) && $mess_notif != '')
    echo notifier($mess_notif);

if ($creation==1 && $debut==0)
{
/*
echo "<pre>";
     print_r($_POST);
echo "</pre>";
*/
   echo" <TR><TD colspan=2 bgColor='#FFFFFF'>";
   if (!isset($i)) $i=0;
   if (($i+1) == $nomb_p && isset($ajout_question) && $ajout_question == 1)
   {
      $nomb_p++;
      $sql_ajt_nbrp = mysql_query("update qcm_param set n_pages = $nomb_p where ordre='$ord'");
      if ($modifier == 1)
      {
              $id_new_don = Donne_ID ($connect,"SELECT max(qcm_data_cdn) from qcm_donnees");
              $requete = mysql_query("INSERT INTO qcm_donnees (qcm_data_cdn,qcmdata_auteur_no,img_blb,typ_img,multiple,note,question,image) VALUES ($id_new_don,'".$_SESSION['id_user']."',\" \",\" \",0,0,\"\",\"\")");
              $idNewLink = Donne_ID ($connect,"SELECT max(qcmlinker_cdn) from qcm_linker");
              $requete = mysql_query("INSERT INTO qcm_linker values('$idNewLink','$ord','$id_new_don','$nomb_p')");
      }
   }
   if (!isset($_SESSION['compteur']))
      $_SESSION['compteur'] = 0;
   while($i <= $nomb_p)
   {
      $n=$i+1;
      if ((!isset($ajout_ligne) || (isset($ajout_ligne) && $ajout_ligne == 0)) && (!isset($ajout_question) || (isset($ajout_question) && $ajout_question == 0)) && $retour != 0 && $n>1 && (($rep[1] == "" && $rep[2] == "") || ($validite[1] == "" && $validite[2] == "" && $validite[3] == "" && $validite[4] == "" && $validite[5] == "") || $multiple == "" || $note == "" || $question == ""))
      {
          echo "<B>$mess_gen_ins</B><P>";
          $i =$i-1;
          $lien = "creation_qcm.php?note=$note&retour=1&creation=1&debut=0&rep[1]=$rep[1]&rep[2]=$rep[2]&rep[3]=$rep[3]&rep[4]=$rep[4]&rep[5]=$rep[5]&rep[6]=$rep[6]&rep[7]=$rep[7]&rep[8]=$rep[8]&rep[9]=$rep[9]&rep[10]=$rep[10]&validite[1]=$validite[1]&validite[2]=$validite[2]&validite[3]=$validite[3]&validite[4]=$validite[4]&validite[5]=$validite[5]&question=$question&multiple=$multiple&i=$i&modifier=$modifier&ord=$ord&nomb_p=$nomb_p&duree=$duree&ordre=$ordre&moyenne=$moyenne&nomb_l=$nomb_l";
          echo "<A href=\"$lien\">$mess_form_retour</A></td></tr></table></td></tr></table>";
         exit;
      }
      if ($modifier==1 && $i < $nomb_p)
      {
          $sql_donnees = mysql_query("SELECT * FROM qcm_donnees,qcm_linker  WHERE qcmlinker_number_no='$n' and
                                     qcmlinker_data_no=qcm_data_cdn and qcmlinker_param_no='$ord'");
          $nbr_enr=mysql_num_rows($sql_donnees);
          if ($nbr_enr == 0)
          {
             $modifier = 0;
             $nomb_l = 3;
          }else
          {
             $nomb_l=mysql_result($sql_donnees,0,"n_lignes");
             $idData=mysql_result($sql_donnees,0,"qcm_data_cdn");
          }
      }
      if (isset($new_line) && $new_line != $nomb_l)
      {
             $insert_chp = mysql_query("update qcm_donnees,qcm_linker set n_lignes = '$new_line' WHERE
                                        qcmlinker_number_no='$n' and qcmlinker_data_no=qcm_data_cdn and AND qcmlinker_param_no='$ord'");
             $nomb_l = $new_line;
      }
      if (isset($sup_reponse) && $sup_reponse == 1)
      {
         $nomb_l--;
         $insert_chp = mysql_query("update qcm_donnees,qcm_linker set n_lignes = '$nomb_l' WHERE
                                   qcmlinker_number_no='$n' and qcmlinker_data_no=qcm_data_cdn and AND qcmlinker_param_no='$ord'");
      }
      if ($inserer==1)
      {
      //echo "<pre>";print_r($_POST);echo"</pre>";
      if (is_uploaded_file($_FILES['userfile']['tmp_name']))
      {
//        $fichier_source=$userfile;
        list($extension,$nom) = getextension($_FILES["userfile"]["name"]);
        if (strtolower($extension) != "gif" && strtolower($extension) != "png" && strtolower($extension) != "jpg" && strtolower($extension) != "jpeg")
           $mess_notif1 = $_FILES["userfile"]["name"]." : extension du fichier non autorisée.";
        else
        {
          $nom_cours=$_FILES['userfile']['name'];
          $type_img=$_FILES['userfile']['type'];
          $cours_data=file_get_contents($_FILES['userfile']['tmp_name']);
          $type = GetImageSize($_FILES['userfile']['tmp_name']);
          $type_image="";
          if ($type[2]==1) {
              $type_image=".gif";
          }
          if ($type[2]==2) {
              $type_image=".jpg";
          }
          if ($type[2]==3) {
              $type_image=".png";
          }
          $extension = (isset($ord) && $ord > 0) ? $ord : $ordre;
          $handle=opendir("ressources");
          $drap=0;
          while ($file = readdir($handle))
          {
                 if ($file == "qcm_images")
                 {
                     $dir_fic="ressources/qcm_images/";
                     $fichier_final = $dir_fic."qcm".$extension.$i.$type_image;
                     $drap=1;
                     break;
                 }
          }
          closedir($handle) ;
          if ($drap==0)
          {
              chdir("ressources");
              mkdir("qcm_images",0777);
              $dir_fic = "ressources/qcm_images/";
              $fichier_final=$dir_fic."qcm".$extension.$i.$type_image;
//              chdir("../../../../");
          }
          $dest_file= $fichier_final;
          $fichier_final="qcm_images/qcm".$extension.$i.$type_image;
          $source_file=$userfile;
          $copier=copy($source_file , $dest_file);
        }
        else
        {
           $fichier_final="non";
           $cours_data = '';
           //echo notifier("le format de l'image n'est pas correct");
        }
      }
      else
      {
           $fichier_final="non";
          $cours_data = '';
          //if (isset($userfile_name) && !strstr($userfile_name,'.'))
             //echo notifier($mmsg_qcmNoImg);
      }
      $numero=$ord;
      $j=0;
      if ($nomb_l == 1)
         $nomb_l++;
      while ($j < $nomb_l)
      {
        $nn=$j+1;
        $point=$nn."_prop";
        $rep[$nn] = str_replace('|','-',$rep[$nn]);
        $rep[$nn] = str_replace('}}','-',$rep[$nn]);
        if (!$rep[$nn])
          BREAK;
        $val=$nn."_val";
        if ($validite[$nn])
          $validite[$nn]=1;
        else
          $validite[$nn]=0;
//        if ($multiple){$multiple=1;}else{$multiple=0;}
        if ($modifier == 1)
        {
           if ($supp_image == TRUE)
              $fichier_final = 'non';
           elseif ($supp_image == FALSE && !$userfile)
              $fichier_final = 'nochange';

           $flag1=1;
           if ($fichier_final == 'non')
              $req = "UPDATE qcm_donnees SET n_lignes='$nn',multiple='$multiple',note='$note',question=\"$question\",image=\"$fichier_final\",$val='$validite[$nn]',$point=\"$rep[$nn]\" WHERE  qcm_data_cdn='$idData'";//code='$ord' AND numero_page='$i'
           elseif ($fichier_final == 'nochange')
              $req = "UPDATE qcm_donnees SET n_lignes='$nn',multiple='$multiple',note='$note',question=\"$question\",$val='$validite[$nn]',$point=\"$rep[$nn]\" WHERE  qcm_data_cdn='$idData'";// code='$ord' AND numero_page='$i'
           else
              $req = "UPDATE qcm_donnees SET n_lignes='$nn',img_blb=\"".addslashes($cours_data)."\",typ_img=\"$type_img\",multiple='$multiple',note='$note',question=\"$question\",image=\"$fichier_final\",$val='$validite[$nn]',$point=\"$rep[$nn]\" WHERE qcm_data_cdn='$idData'";//code='$ord' AND numero_page='$i'
           $requete_insert = mysql_query($req);
        }
        else
        {
           if ($j > 0)
              $req=mysql_query("UPDATE qcm_donnees SET n_lignes=\"$nn\",$val=\"$validite[$nn]\",$point=\"$rep[$nn]\" WHERE qcm_data_cdn='$ord'");
           else
           {
              $id_new_don = Donne_ID ($connect,"SELECT max(qcm_data_cdn) from qcm_donnees");
              $_SESSION['compteur']++;
              $requete = mysql_query("INSERT INTO qcm_donnees (qcm_data_cdn,n_lignes,qcmdata_auteur_no,img_blb,typ_img,multiple,note,question,image,$val,$point) VALUES ($id_new_don,'$nn','".$_SESSION['id_user']."',\"".addslashes($cours_data)."\",\"$type_img\",\"$multiple\",\"$note\",\"$question\",\"$fichier_final\",\"$validite[$nn]\",\"$rep[$nn]\")");
              $idLinker = Donne_ID ($connect,"SELECT max(qcmlinker_cdn) from qcm_linker");
              $request =mysql_query("INSERT INTO qcm_linker VALUES ('".$idLinker."','".$ordre."','".$id_new_don."','".$_SESSION['compteur']."')");
              $req=mysql_query($requete);
              $code=$ordre;
              $req=mysql_query("SELECT MAX(qcm_data_cdn) FROM qcm_donnees");
              $ord=mysql_result($req,0);
           }
        }
        $j++;
       }
    }
    if ($i==$nomb_p && $flag1==1)
    {
      $mess_notif = $mess_cqcm_modif_ok;
      $lien = "menu_qcm.php?mess_notif=$mess_notif";
      $lien = urlencode($lien);
      echo "<script language=\"JavaScript\">";
         echo "document.location.replace(\"trace.php?link=$lien\")";
      echo "</script>";
      exit;
    }

    if ($i == $nomb_p && $flag1==0)
    {
        $requete = mysql_query("select * from qcm_linker where qcmlinker_param_no = '".$code."' order by qcmlinker_cdn");
        if ($requete == true && mysql_num_rows($requete) > 0)
        {
          $nbQuest = mysql_num_rows($requete);
          for($i = 1; $i < ($nbQuest+1);$i++)
          {
             $l = $i-1;
             $idUpdt = mysql_result($requete,$l,'qcmlinker_cdn');
             $req_updt = mysql_query("update qcm_linker set qcmlinker_number_no = '".$i."' where qcmlinker_cdn = '".$idUpdt."'");
          }
          $req_updt = mysql_query("update qcm_param set n_pages = '".$nbQuest."' where ordre = '".$code."'");
        }
      unset($_SESSION['compteur']);
      echo"<TR><TD colspan='2' valign='top'><B>$mess_cqcm_mess_ins</B></td></tr>";
      echo "<FORM name='form' action='creation_qcm.php' method='POST'>";
      echo"<INPUT TYPE=HIDDEN name='passerelle' value=1>";
      echo"<INPUT TYPE=HIDDEN name='ordre_code' value= $code>";
      echo"<TR><TD colspan='2' valign='top'><table><tr><td><B>$mess_cqcm_cat</B></td>";
      $req_mod=mysql_query("select distinct ress_cat_lb from ressource_new order by ress_cat_lb asc");
      $res_mod=mysql_num_rows($req_mod);
      $mm=0;
      echo"<td><SELECT name='dom'>";
      while($mm < $res_mod)
      {
        $dom=mysql_result($req_mod,$mm,"ress_cat_lb");
        echo "<OPTION>$dom</OPTION>";
        $mm++;
      }
      echo "</SELECT>";
      echo "<A HREF=\"javascript:document.form.submit();\" ".
           "onmouseover=\"img1.src='images/fiche_identite/boutvalidb$suffixer.gif';return true;\" ".
           "onmouseout=\"img1.src='images/fiche_identite/boutvalid$suffixer.gif'\">".
           "<IMG NAME=\"img1\" SRC=\"images/fiche_identite/boutvalid$suffixer.gif\" BORDER='0' ".
           "onLoad=\"tempImg=new Image(0,0); tempImg.src='images/fiche_identite/boutvalidb$suffixer.gif'\"></A>";
      echo "</td></tr></FORM></td></tr></table></td></tr></table>";
      exit;
    }
      $xx = $i+1;
  ?>
  <SCRIPT language=JavaScript>
    function checkFormCreation(frm) {
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
    function isEmpty(elm) {
      var elmstr = elm.value + "";
      if (elmstr.length == 0)
       return true;
     return false;
    }
  </SCRIPT>
  <?php
      echo "<tr><td colspan='2'><table border='0'><tr><td colspan='2'>";
      if (!isset($nomb_l) || (isset($nomb_l) && $nomb_l == 0)) $nomb_l = 3;
      echo "<FORM id='creation' name='creation' action=\"creation_qcm.php?ord=$ord&ordre=$ordre&retour=0&code=$code&nomb_l=$nomb_l\" method=\"POST\" enctype=\"multipart/form-data\">";
      echo "<INPUT TYPE=\"HIDDEN\" name=\"i\" value=$xx>";
//      echo "<INPUT TYPE=\"HIDDEN\" name=\"insert\" value=\"$numero\">";
      echo "<INPUT TYPE=\"HIDDEN\" name=\"inserer\" value=\"1\">";
      echo "<INPUT TYPE=\"HIDDEN\" name=\"debut\" value=\"0\">";
      echo "<INPUT TYPE=\"HIDDEN\" name=\"creation\" value=\"1\">";
      if($modifier == 1)
      {
          echo"<INPUT TYPE=HIDDEN name=\"modifier\" value=\"1\">";
          echo"<INPUT TYPE=HIDDEN name=\"flag1\" value=\"$flag1\">";
      }
      echo '<INPUT TYPE="HIDDEN" name="nomb_p" value="'.$nomb_p.'">';
      echo "<B><Font color=marroon size=2>$xx$mess_cqcm_quest_suiv </font></B></TD></TR>";
      echo '<TR><TD colspan="2" ><TABLE><tbody>';
      echo '<TR><TD align="left"><B>'.$mess_cqcm_nbr_rep.'</B></TD><TD>';
      if (!isset($nomb_l) || $nomb_l == 0)
      {
          $nomb_l = 3;
      }
      elseif (isset($ajout_ligne) && $ajout_ligne == 1)
      {
         $nomb_l = $new_line;
      }

      echo "<form name='form'>";
      echo "<SELECT name='select' class='SELECT' onChange=\"appel_w1(form.select.options[selectedIndex].value);\">";
      echo "<option>$nomb_l $mess_cqcm_rep</option>";
      for ($item=2; $item < 7; $item++)
      {
          $lien = urlencode("creation_qcm.php?ajout_ligne=1&modifier=$modifier&creation=$creation&debut=$debut&inserer=$inserer&nomb_p=$nomb_p&new_line=$item&moyenne=$moyenne&ord=$ord&ordre=$ordre&retour=$retour&code=$code&i=$i");
          echo "<option value=\"trace.php?link=$lien\">$item</option>";
      }
      echo "</SELECT></TD></TR>";
      echo "<TR><TD valign='top'>Formulez ici $mess_cqcm_quest_rep</TD>";
      if  ($modifier == 1)
      {
           $question = mysql_result($sql_donnees,0,"question");
           $note = mysql_result($sql_donnees,0,"note");
           $multiple = mysql_result($sql_donnees,0,"multiple");
           $imager = mysql_result($sql_donnees,0,"image");
      }
      echo '<TD align="left" valign="top">';
      echo "<INPUT TYPE=HIDDEN name=\"nomb_l\" value='$nomb_l'>";
      echo '<TEXTAREA  name="question" cols="40" rows="4" title="'.$mess_cqcm_ins_q.'">';
      if ($retour == 1 || $modifier==1)
         echo $question;
      echo '</TEXTAREA></TD></TR>';
      echo '<TR><TD align="left">'.$mess_cqcm_note1.'<BR>';
      echo '<FONT COLOR=red size="1">Attention !! Une réponse en moins là où plusieurs réponses sont attendues équivaut à une note de Zéro (0).</font></TD>';
      echo '<TD align="left"><INPUT TYPE="text" class="INPUT"  name="note" size="2" maxlength="2"';
      if ($retour == 1 || $modifier==1)
         echo ' value= '.$note;
      echo '></TD></TR>';
      echo '<TR><TD align="left">'.$mess_cqcm_img.'</TD>';
      echo '<TD valign="top">';
      if ($modifier == 1)
         echo "<INPUT TYPE=\"CHECKBOX\" name=\"supp_image\" title=\"$mess_ag_supp\">&nbsp;&nbsp;";
      echo '<INPUT TYPE="file" class="INPUT" size="45" name="userfile" enctype="multipart/form-data">';
      if ($modifier == 1 && $imager != 'non')
         echo "&nbsp;&nbsp;<img src = 'ressources/$imager' width='15' heigth='15'>";
      echo '</TD></TR>';
      echo '<TR><TD align="left">'.$mess_cqcm_rep_mult2.'</TD>';
      if ($multiple == 1)
      {
          echo '<TD><INPUT TYPE="RADIO" CHECKED name="multiple" value="1" title="'.$mess_cqcm_rep_mult2.'"></TD></TR>';
          echo '<TR><TD align="left">'.$mess_cqcm_rep_un2.'</TD>';
          echo '<TD><INPUT TYPE="RADIO" name="multiple" value="0" title="'.$mess_cqcm_rep_un2.'"></TD></TR>';
      }
      else
      {
          echo '<TD><INPUT TYPE="RADIO" name="multiple" value="1" title="'.$mess_cqcm_rep_mult2.'"></TD></TR>';
          echo '<TR><TD align="left">'.$mess_cqcm_rep_un2.'</TD>';
          echo '<TD><INPUT TYPE="RADIO" CHECKED name="multiple" value="0" title="'.$mess_cqcm_rep_un2.'"></TD></TR>';
      }
      $j=0;
      $pointeur=array();
      if ($retour == 0)
         $rep=array();
      $validite=array();
      if (!isset($nomb_l) || (isset($nomb_l) && $nomb_l == 0))
         $nomb_l = 2;
      while ($j < $nomb_l)
      {
        $nn=$j+1;
        echo "<INPUT TYPE=\"HIDDEN\"  name='pointeur[$nn]' value=\"".$nn."_prop\">".
             "<INPUT TYPE=\"HIDDEN\"  name='nn' value=\"$nn>\"".
             "<TR style=\"height:30px;\"><TD>";
        if($modifier==1)
        {
          $nnn=$nn."_prop";
          $mmm=$nn."_val";
          $rep[$nn]=mysql_result($sql_donnees,0,$nnn);
          $valeur[$nn]=mysql_result($sql_donnees,0,$mmm);
        }
        echo "<B>$mess_repatt  $nn</B></TD>";
        echo "<TD><INPUT TYPE=\"text\" class=\"INPUT\"  name=\"rep[$nn]\"  size=\"60\" value=\"".$rep[$nn]."\" ".
             "maxlength=\"250\" title=\"$mess_cqcm_prop $nn\"></TD>";
        if ($modifier == 1 && $valeur[$nn] == 1)
            echo "<TD><INPUT TYPE=\"CHECKBOX\" name=\"validite[$nn]\" title=\"$mess_cqcm_rep_ok\" checked><BR></TD>";
        else
           echo "<TD><INPUT TYPE=\"CHECKBOX\" name=\"validite[$nn]\" title=\"$mess_cqcm_rep_ok\"><BR></TD>";
        if ($nn == $nomb_l && $nomb_l > 2)
        {
           $lien = "creation_qcm.php?sup_reponse=1&modifier=$modifier&creation=$creation&debut=$debut&inserer=$inserer&nomb_p=$nomb_p&nomb_l=$nomb_l&moyenne=$moyenne&ord=$ord&ordre=$ordre&retour=$retour&code=$code&i=$i";
           $lien = urlencode($lien);
           echo "<td style=\"align:'center';\"><A href=\"trace.php?link=$lien\">".
                "<IMG SRC=\"images/messagerie/icoGpoubel.gif\" height=\"20\" width=\"15\" BORDER=0></A></td>";
        }
        echo "</TR>";
        $j++;
      }
      if (($i+1) == $nomb_p)
      {
         $lien = "creation_qcm.php?ajout_question=1&modifier=$modifier&creation=$creation&debut=$debut&inserer=$inserer&nomb_p=$nomb_p&nomb_l=$nomb_l&moyenne=$moyenne&ord=$ord&ordre=$ordre&retour=$retour&code=$code&i=$i";
         $lien = urlencode($lien);
         echo "<TR><td></td><td style=\"align:'center';height: 40px;\">$bouton_gauche<A href=\"trace.php?link=$lien\">".
              "$mess_qcm_ajt_qst</A>$bouton_droite</td></tr>";
      }
       echo "<TR><td></td><td style=\"font-size: 10px;\">$mess_vf</td></tr>";
       echo "<TR><td></td><td style=\"align:'center';height: 40px;\"><A HREF=\"javascript:checkFormCreation(document.creation);\" ".
            " onClick=\"TinyMCE.prototype.triggerSave();\" onmouseover=\"img1.src='images/fiche_identite/boutvalidb$suffixer.gif';return true;\" ".
            "onmouseout=\"img1.src='images/fiche_identite/boutvalid$suffixer.gif'\">".
            "<IMG NAME=\"img1\" SRC=\"images/fiche_identite/boutvalid$suffixer.gif\" BORDER='0' ".
            "onLoad=\"tempImg=new Image(0,0); tempImg.src='images/fiche_identite/boutvalidb$suffixer.gif'\"></A>
            </FORM></td></tr></table></td></tr></table>";
      exit;
   }
exit;
}
if ($ini_creation==1 && (!isset($modifier) || (isset($modifier) && $modifier==0)))
{
  echo "<TR><TD colspan=2 bgColor='#FFFFFF'>";
  $ini_creation=0;
  $result_sql=mysql_query("SELECT * FROM qcm_param where qcm_auteur_no='$id_user'");
  $insert=mysql_num_rows($result_sql);
  $insert++;
  $duree = ($horaire*60) + $minutage;
  $der_num = Donne_ID ($connect,"select max(ordre) from qcm_param");
  $sql = mysql_query("INSERT INTO qcm_param (ordre,qcm_auteur_no,n_pages,titre_qcm,duree,moyenne) VALUES($der_num,\"$id_user\",\"$nomb_p\",\"$titre\",\"$duree\",'$moyenne')");
  $lien = "creation_qcm.php?modifier=0&creation=1&debut=1&inserer=0&nomb_p=$nomb_p&nomb_l=$nomb_l&moyenne=$moyenne";
  $lien = urlencode($lien);
  print "<script language=\"JavaScript\">";
       print "document.location.replace(\"trace.php?link=$lien\")";
  print "</script>";
  echo "</td></tr></table></td></tr></table>";
exit;
}

if ($ini_creation==1 && $modifier==1)
{
   $duree = ($horaire*60) + $minutage;
  echo "<TR><TD colspan=2 bgColor='#FFFFFF'>";
  if (!$nomb_p || !$duree || !$moyenne || !$titre)
  {
    echo "$mess_gen_ins<P>";
    $lien = "creation_qcm.php?modification=1&titre=$titre_entier&numero=$numero&ord=$ord&nomb_p=$nomb_p&duree=$duree&moyenne=$moyenne&nomb_l=$nomb_l";
    $lien=urlencode($lien);
    echo "<A href=\"trace.php?link=$lien\">$mess_form_retour</A></td></tr></table></td></tr></table>";
    exit;
  }
  $ini_creation=0;$modifier=0;
  $sql=mysql_query("UPDATE qcm_param SET n_pages=\"$nomb_p\",duree=\"$duree\",titre_qcm=\"$titre\",moyenne='$moyenne' where ordre=\"$ord\"");
  $lien="creation_qcm.php?modifier=1&creation=1&debut=0&inserer=0&numero=$numero&insert=$ord&ord=$ord&nomb_p=$nomb_p&moyenne=$moyenne";
  $lien = urlencode($lien);
  print "<script language=\"JavaScript\">";
       print "document.location.replace(\"trace.php?link=$lien\")";
  print "</script>";
  echo "</td></tr></table></td></tr></table>";
exit;
}

if (($supprimer==1 || $modifier==1) && (!isset($ini_creation) || (isset($ini_creation) && $ini_creation == 0)))
{
  echo "<tr><td colspan='2'><FORM name='formodifsupp' action=\"creation_qcm.php?modification=1\" method='POST'>";
  if ($supprimer==1)
  {
    $verifier = 1;
    echo "<tr><td colspan='2'><B>$mmsg_qcm_sp&nbsp;&nbsp;&nbsp;</B></td></tr>";
    echo "<INPUT TYPE=HIDDEN name='suppression' value=1>";
    $supprimer=0;
    echo "<tr><td valign='top' colspan='2'><table><tr><td style=\"height:35px\"> <B>$mess_cqcm_quoi_sup  &nbsp;&nbsp;&nbsp;</B>";
  }
  else
  {
    echo "<tr><td colspan='2'><B>$mess_cqcm_form_modif&nbsp;&nbsp;&nbsp;  </B></td></tr>";
    echo "<tr><td colspan='2'><table><tr><td style=\"height:35px\"> $mess_cqcm_quoi_modif</td>";
  }
  $champ_search = ($typ_user='ADMINISTRATEUR') ? "" : "where qcm_auteur_no=\"$id_user\"";
  $req_mod=mysql_query("SELECT titre_qcm,ordre FROM qcm_param $champ_search order by titre_qcm asc");
  $res_mod=mysql_num_rows($req_mod);
  $mm=0;
  echo "<td><SELECT name='intitule' class='SELECT'>";
  while($mm<$res_mod)
  {
    $titre=mysql_result($req_mod,$mm,"titre_qcm");
    $ordre=mysql_result($req_mod,$mm,"ordre");
    if ($verifier == 1)
    {
      $req_verif = mysql_result(mysql_query("SELECT count(activite.act_cdn) from activite,ressource_new where ressource_new.ress_url_lb like \"%qcm.php?code=$ordre%\" and activite.act_ress_no = ressource_new.ress_cdn"),0);
      if ($req_verif == 0)
        echo "<OPTION value=\"$titre|$ordre\">$titre</OPTION>";
    }
    else
        echo "<OPTION value=\"$titre|$ordre\">$titre</OPTION>";
  $mm++;
  }
  echo "</SELECT></td>";
  echo "<td><A HREF=\"javascript:document.formodifsupp.submit();\" ".
       "onmouseover=\"img1.src='images/fiche_identite/boutvalidb$suffixer.gif';return true;\" ".
       "onmouseout=\"img1.src='images/fiche_identite/boutvalid$suffixer.gif'\">".
       "<IMG NAME=\"img1\" SRC=\"images/fiche_identite/boutvalid$suffixer.gif\" BORDER='0' ".
       "onLoad=\"tempImg=new Image(0,0); tempImg.src='images/fiche_identite/boutvalidb$suffixer.gif'\"></A></td></tr><table>";
  echo "</td></tr></form>";
  echo "</table></td></tr></table>";
  exit;
}

if ($modification == 1)
{
   $tab = explode('|',$intitule);
   $titre_entier = $tab[0];
   $requete=mysql_query("SELECT * FROM qcm_param where
                        ordre = '".$tab[1]."' and qcm_auteur_no = '$auteur'");
    $ord=mysql_result($requete,0,"ordre");
    $nomb_p=mysql_result($requete,0,"n_pages");
    $duree=mysql_result($requete,0,"duree");
    $titre=mysql_result($requete,0,"titre_qcm");
    $moyenne=mysql_result($requete,0,"moyenne");
    if ($suppression == 1)
    {
      $suppression=0;
      $req_supp = mysql_query("SELECT * FROM qcm_param,qcm_linker,qcm_donnees where
                               ordre = ".$ord." and qcm_auteur_no = '$auteur' and
                               qcmlinker_param_no= ordre and qcmlinker_data_no=qcm_data_cdn");
      if (mysql_num_rows($req_supp) > 0)
      {
         while ($itemSupp = mysql_fetch_object($req_supp))
         {
             $efface_sql= mysql_query("DELETE FROM qcm_donnees WHERE qcm_data_cdn=".$itemSupp->qcm_data_cdn);
         }
      }
      $efface_sql= mysql_query("DELETE FROM qcm_linker WHERE qcmlinker_param_no='$ord'");
      $efface_sql= mysql_query("DELETE FROM qcm_param WHERE ordre='$ord'");
      $efface_sql= mysql_query("DELETE FROM ressource_new WHERE ress_titre='$titre' AND ress_ajout='$login'");
      $mess_notif = "$mess_cqcm_supp_ress1 $titre $mess_cqcm_supp_ress2";
      $lien = "menu_qcm.php?mess_notif=$mess_notif";
      $lien = urlencode($lien);
      echo "<script language=\"JavaScript\">";
         echo "document.location.replace(\"trace.php?link=$lien\")";
      echo "</script>";
    }
  ?>
  <SCRIPT language=JavaScript>
    function checkFormModif(frm) {
      var ErrMsg = "<?php echo $mess_info_no;?>\n\n";
      var lenInit = ErrMsg.length;
      if (isEmpty(frm.titre)==true)
        ErrMsg += ' - <?php echo $msq_titre;?>\n';
      if (isEmpty(frm.nomb_p)==true)
        ErrMsg += ' - <?php echo $mess_cqcm_nbr_ques;?>\n';
      if (isEmpty(frm.moyenne)==true)
        ErrMsg += ' - <?php echo $mess_moy_qcm;?>\n';
      if (isEmpty(frm.horaire)==true && isEmpty(frm.minutage)==true)
        ErrMsg += ' - <?php echo $msq_horaire_act_form;?>\n';
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
    <tr><td colspan='2'><table><tbody>
    <FORM id='modifier' name='modifier' action="creation_qcm.php?modifier=1" method="POST">
      <INPUT type="HIDDEN" name="numero" value="<?php echo $numero;?>">
      <INPUT type="HIDDEN" name="titre_entier" value="<?php echo $titre_entier;?>">
      <INPUT type="HIDDEN" name="ord" value="<?php echo $ord;?>">
      <INPUT TYPE="HIDDEN" name="ini_creation" value="1">
      <TR><TD><B><?php  echo $mess_cqcm_tit_qcm;?></B></TD><TD>
      <INPUT TYPE="text" class="INPUT"  name="titre" align="left" size="80" value="<?php echo $titre;?>" title="<?php  echo $mess_cqcm_tit_qcm;?>"></TD></TR>
      <TR><TD><B><?php  echo $mess_cqcm_nbr_ques;?></B></TD><TD><B>
      <INPUT TYPE="hidden" class="INPUT"  name="nomb_p" align="left" size="1" value="<?php echo $nomb_p;?>" title="<?php  echo $mess_cqcm_nbr_ques;?>">
      <?php  echo " ---------> $nomb_p";?></B></TD></TR>
      <TR><TD><B><?php  echo $mess_moy_qcm ;?></B></TD><TD>
      <INPUT TYPE="text" class="INPUT"  name="moyenne" align="left" size="2" value="<?php echo $moyenne;?>" title="<?php  echo $mess_moy_detail_qcm ;?>"></TD></TR>
      <?php
      echo "<TR><TD nowrap><B>$mess_cqcm_tps</B></TD><TD align='left'><TABLE border='0' cellspacing='0'><TR>";
      $reste = $duree%60;
      $heure = floor($duree/60);
      echo "<TD><INPUT TYPE='text' class='INPUT' name='horaire' value='$heure' size='2' maxlength = '3' align='center'>$h </TD>";
      echo "<TD><INPUT TYPE='text' class='INPUT' name='minutage' value='$reste' size='2' maxlength = '2' align='center'>$mn</TD>";
      echo "</td></tr></table></td></tr>";
      echo "<tr><td style=\"align:'center';height: 40px;\"><A HREF=\"javascript:checkFormModif(document.modifier);\" ".
            "onmouseover=\"img1.src='images/fiche_identite/boutvalidb$suffixer.gif';return true;\" ".
            "onmouseout=\"img1.src='images/fiche_identite/boutvalid$suffixer.gif'\">".
            "<IMG NAME=\"img1\" SRC=\"images/fiche_identite/boutvalid$suffixer.gif\" BORDER='0' ".
            "onLoad=\"tempImg=new Image(0,0); tempImg.src='images/fiche_identite/boutvalidb$suffixer.gif'\"></A>
            </td></tr></FORM></tbody></table></td></tr></table></td></tr></table>";
  exit;
}
if ($consulter == 1)
{
    $html .= "<TR><TD colspan='2'><B>$mmsg_qcm_cs</B><p></TD></TR>\n";
    $html .= "<TR><TD colspan='2'><form name='form' id='form'><B>$mess_cqcm_tit_qcm</B>&nbsp;&nbsp;&nbsp;\n";
    $champ_search = ($typ_user = 'ADMINISTRATEUR') ? "" : "where qcm_auteur_no=\"$id_user\"";
    $req_mod = mysql_query("SELECT * FROM qcm_param $champ_search order by titre_qcm asc");
    $res_mod = mysql_num_rows($req_mod);
    $mm=0;
    $html .= "<SELECT name='select' class='SELECT' onChange=\"appel_wpop(form.select.options[selectedIndex].value);document.location='#sommet';\">";
    $html .= "<OPTION value=\"#\">- - - - choisissez - - - - </OPTION>\n";
    while ($mm < $res_mod)
    {
          $titre=mysql_result($req_mod,$mm,"titre_qcm");
          $ordre=mysql_result($req_mod,$mm,"ordre");
          $n_pages=mysql_result($req_mod,$mm,"n_pages");
          $verif = mysql_result(mysql_query("select count(*) from qcm_linker where qcmlinker_param_no = $ordre"),0);
          if ($verif == $n_pages)
             $html .= "<OPTION value=\"trace.php?link=".urlencode("qcm.php?code=$ordre")."\">$titre</OPTION>";
        $mm++;
     }
     $html .= "</SELECT></td></tr></FORM>";
     $html .= "</table></td></tr></table>";
     echo $html;
     exit;
}
if ($creation_qcm == 1)
{
   if (isset($id_activit) && $id_activit > 0 && isset($venu) && $venu == 'act')
      $_SESSION['id_activit'] = $id_activit;
   ?>
  <SCRIPT language=JavaScript>
    function checkFormCreate(frm) {
      var ErrMsg = "<?php echo $mess_info_no;?>\n\n";
      var lenInit = ErrMsg.length;
      if (isEmpty(frm.titre)==true)
        ErrMsg += ' - <?php echo $mess_cqcm_tit_qcm;?>\n';
      if (isEmpty(frm.nomb_p)==true)
        ErrMsg += ' - <?php echo $mess_cqcm_nbr_ques;?>\n';
      if (isEmpty(frm.moyenne)==true)
        ErrMsg += ' - <?php echo $mess_moy_qcm;?>\n';
      if (isEmpty(frm.horaire)==true && isEmpty(frm.minutage)==true)
        ErrMsg += ' - <?php echo $msq_horaire_act_form;?>\n';
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
    <FORM  id='create' name='create' action="creation_qcm.php" method="POST">
    <INPUT TYPE="HIDDEN" name="ini_creation" value="1">
    <TR><TD colspan=2 bgColor='#FFFFFF'><TABLE cellpadding="3" border='0'>
    <TR>
    <TD>
      <B><?php  echo $mess_cqcm_tit_qcm;?></B></TD><TD>
      <INPUT TYPE="text" class="INPUT"  name="titre" align="left" size="60" value="<?php echo $titre;?>">
    </TD>
    </TR>
    <TR>
    <TD>
    <B><?php  echo $mess_cqcm_nbr_ques;?></B></TD><TD>
    <INPUT TYPE="text" class="INPUT"  name="nomb_p" align="left" size="1" value="<?php echo $nomb_p;?>" title="<?php  echo $mess_cqcm_nbr_ques;?>">
    </TD>
    </TR>
    <TR>
    <TD>
      <B><?php  echo $mess_moy_qcm ;?></B></TD><TD>
      <INPUT TYPE="text" class="INPUT" name="moyenne" align="left" size="2" value="<?php echo $moyenne;?>" title="<?php  echo $mess_moy_detail_qcm ;?>">
    </TD>
    </TR>
    <?php
      echo "<TR><TD nowrap><B>$mess_cqcm_tps</B></TD><TD align='left'><TABLE border='0' cellspacing='0'><TR>";
      echo "<TD><INPUT  TYPE='text' class='INPUT' name='horaire' size='2' maxlength = '3' align='center'>$h </TD>";
      echo "<TD><INPUT  TYPE='text' class='INPUT' name='minutage' size='2' maxlength = '2' align='center'>$mn</TD>";
      echo "</td></tr></table></td></tr>";
      echo "<tr>";
      echo "<td style=\"align:'center';height: 50px;\" valign='middle'><A HREF=\"javascript:checkFormCreate(document.create);\" ".
            "onmouseover=\"img1.src='images/fiche_identite/boutvalidb$suffixer.gif';return true;\" ".
            "onmouseout=\"img1.src='images/fiche_identite/boutvalid$suffixer.gif'\">".
            "<IMG NAME=\"img1\" SRC=\"images/fiche_identite/boutvalid$suffixer.gif\" BORDER='0' ".
            "onLoad=\"tempImg=new Image(0,0); tempImg.src='images/fiche_identite/boutvalidb$suffixer.gif'\"></A>
            </td></tr></FORM></table></td></tr></table>";
}
echo "</BODY></HTML>";
function getextension($fichier)
{
  $bouts = explode(".", $fichier);
  return array(array_pop($bouts), implode(".", $bouts));
}
?>
