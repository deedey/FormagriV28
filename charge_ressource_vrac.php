<?php
if (!isset($_SESSION)) session_start();
if (!isset($_SESSION['id_user']) || $_SESSION['id_user'] == "")
{
  exit();
}
include ("include/UrlParam2PhpVar.inc.php");
require 'graphique/admin.inc.php';
require 'fonction.inc.php';
require 'fonction_html.inc.php';
require "lang$lg.inc.php";
require "langues/ress.inc.php";
//include ("click_droit.txt");
dbConnect();
$date_dujour = date ("Y-m-d");
$date_media = date ("d-m-Y");
$agent=$_SERVER["HTTP_USER_AGENT"];
if (isset($charger) && $charger == 1)
{
  if (isset($charger_fichier) && $charger_fichier == 1)
  {
    $slash = "/";
    if(is_file($_FILES['userfile']['tmp_name']))
    {
       $directory = $repertoire."/ressources/".$login."_".$id_user."/ressources";
       list($extension,$nom) = getextension($_FILES["userfile"]["name"]);
       if (in_array(strtolower($extension), array("exe","sh","py", "ida","idq","asp","cer","cdx","asa","idc","cfm","dbm","php","php4","inc","shtml","cgi")))
       {
           $mess_notif1 = $_FILES["userfile"]["name"]." : extension du fichier non autorisée.";
       }
       else
       {
        if ($_POST['media'] == 1)
        {
          $list_file=array();
          if (!file_exists($directory."/Ressources_Media"))
              mkdir($directory."/Ressources_Media",0775);
          $dir = "ressources/".$login."_".$id_user."/ressources/Ressources_Media";
          $list_file=explode('.',$_FILES['userfile']['name']);
          $fichier_test = modif_nom($list_file[0]."_".time().".".$list_file[1]);
          $dest_file=$directory."/Ressources_Media/".$fichier_test;
        }
        else
        {
          if (!file_exists($directory."/ressources_vrac"))
              mkdir($directory."/ressources_vrac",0775);
          $dir = "ressources/".$login."_".$id_user."/ressources/ressources_vrac";
          $fichier_test = modif_nom($_FILES['userfile']['name']);
          $dest_file=$directory."/ressources_vrac/".$fichier_test;
        }
       $handle=opendir($dir);
       $i_file=0;
       while ($file = readdir($handle)){
          if ($file == $fichier_test)
            $i_file++;
       }
       closedir($handle);
       if ($i_file > 0 && $media != 1)
          $message = "* <small>$mess_fic_idem $mess_fic_chx_autre<small>";
       else
       {
          $source_file=$_FILES['userfile']['tmp_name'];
          $copier= move_uploaded_file($source_file , $dest_file);
          $rl = $adresse_http."/".$dir."/".$fichier_test;
          //fin du test
          $nom_act = GetDataField ($connect,"select act_nom_lb from activite where act_cdn = $id_act","act_nom_lb");
          $la_cat = ($media == 1) ? "Ressources Multimedia" : "$mess_ress_vrac";
          $la_souscat = ($media == 1) ? "Liaison vers consignes-Média" : "$mess_ress_direct_act";
          $requete= mysql_query("SELECT count(*) FROM ressource_new where ress_cat_lb = \"$la_cat\"");
          $nb_requete= mysql_result($requete,0);
          if ($nb_requete == 0)
          {
              $id_new_ress = Donne_ID ($connect, "select max(ress_cdn) from ressource_new");
              $sql_insere= mysql_query("INSERT INTO ressource_new (ress_cdn,ress_cat_lb,ress_typress_no,ress_create_dt,ress_ajout) VALUES ('$id_new_ress',\"$la_cat\",'0',\"$date_dujour\",'foad')");
              $id_new_ress2 = Donne_ID ($connect, "select max(ress_cdn) from ressource_new");
              $sql_insere= mysql_query("INSERT INTO ressource_new (ress_cdn,ress_cat_lb,ress_typress_no,ress_create_dt,ress_ajout) VALUES ('$id_new_ress2',\"$la_souscat\",'$id_new_ress',\"$date_dujour\",'foad')");
              $parente = $id_new_ress;
          }
          else
              $parente = GetDataField($connect,"SELECT ress_cdn FROM ressource_new WHERE ress_cat_lb = \"$la_cat\" AND ress_typress_no = 0 AND ress_titre =\"\"","ress_cdn");
          $id_new_ress = Donne_ID ($connect, "select max(ress_cdn) from ressource_new");
          $sql_insere= mysql_query("INSERT INTO ressource_new (ress_cdn,ress_cat_lb,ress_typress_no,ress_url_lb,ress_auteurs_cmt,ress_publique_on,ress_titre,ress_desc_cmt,ress_create_dt,ress_ajout,ress_public_no,ress_type,ress_support,ress_doublon,ress_niveau) VALUES ('$id_new_ress',\"$la_souscat\",'$parente',\"$rl\",\"Inconnu\",'NON',\"$titre\",\"$mess_no_comment\",\"$date_dujour\",\"$login\",'TOUT',\"ACTIVITES MULTIPLES\",\"Url\",'1','1')");
          if ($_POST['media'] == 1)
          {
              $req_media = mysql_result(mysql_query("select count(*) from activite_media where actmedia_act_no = $id_act"),0);
              if ($req_media > 0)
              {
                  $req = mysql_query("update activite_media set actmedia_ress_no ='$id_new_ress' where actmedia_act_no = $id_act");
                  $message = "Le fichier média lié à la consigne a été modifié";
              }
              else
              {
                  $id_new_actmed = Donne_ID ($connect, "select max(actmedia_cdn) from activite_media");
                  $requete= mysql_query("INSERT INTO activite_media values('$id_new_actmed','$id_act','$id_new_ress')");
                  $message = "Le fichier média a été lié à la consigne";
              }
          }
          else
              $requete= mysql_query("UPDATE activite set act_ress_no = $id_new_ress,act_flag_on = 1 where act_cdn = $id_act");
          
          $les_params = str_replace("|","&",$params);
          if ($media == 1)
          {
                     echo "<SCRIPT Language=\"Javascript\">;
                     window.opener.location.reload();
                     setTimeout(\"Quit()\",500);
                     function Quit() {
                          self.opener=null;self.close();return false;
                     }";
                echo "</SCRIPT>";
                exit;
          }
          elseif ($dou == "act_free" && $media != 1)
             $lien="activite_free.php?creer=1&modifie_act=1&act_a_modif=$id_act".$les_params."&message=$mmsg_RessOk";
          else
             $lien="sequence$dou.php?action_act=1&modif_act=1&id_act=$id_act&id_ress=$id_new_ress".$les_params."&message=$mmsg_RessOk";
          $lien=urlencode($lien);
          echo "<SCRIPT Language=\"Javascript\">";
            echo "window.opener.location.replace(\"trace.php?link=$lien\")";
          echo "</SCRIPT>";
          ?>

          <SCRIPT language=javascript>
            setTimeout("Quit()",500);
              function Quit() {
                 self.opener=null;self.close();return false;
              }
          </SCRIPT>
          <?php
         exit();
       }
      }
      if (isset($mess_notif1))
      {
           $message = "<div style='color:red;font-weight:bold;'>Attention !! $mess_notif1</div>";
      }
     }
     else
       $message = "* <small>$mess_fic_dep_lim ".ini_get('upload_max_filesize')."o<small>";

  }
  elseif (isset($charger_url) && $charger_url == 1)
  {
          $rl = $userfile;
          $nom_act = GetDataField ($connect,"select act_nom_lb from activite where act_cdn = $id_act","act_nom_lb");
          $requete= mysql_query("SELECT count(*) FROM ressource_new where ress_cat_lb = \"$mess_ress_vrac\"");
          $nb_requete= mysql_result($requete,0);
          if ($nb_requete == 0)
          {
              $id_new_ress = Donne_ID ($connect, "select max(ress_cdn) from ressource_new");
              $sql_insere= mysql_query("INSERT INTO ressource_new (ress_cdn,ress_cat_lb,ress_typress_no,ress_create_dt,ress_ajout) VALUES ('$id_new_ress',\"$mess_ress_vrac\",'0',\"$date_dujour\",'foad')");
              $id_new_ress2 = Donne_ID ($connect, "select max(ress_cdn) from ressource_new");
              $sql_insere= mysql_query("INSERT INTO ressource_new (ress_cdn,ress_cat_lb,ress_typress_no,ress_create_dt,ress_ajout) VALUES ('$id_new_ress2',\"$mess_ress_direct_act\",'$id_new_ress',\"$date_dujour\",'foad')");
              $parente = $id_new_ress;
          }
          else
              $parente = GetDataField($connect,"SELECT ress_cdn FROM ressource_new WHERE ress_cat_lb = \"$mess_ress_vrac\" AND ress_typress_no = 0 AND ress_titre =\"\"","ress_cdn");
          $id_new_ress = Donne_ID ($connect, "select max(ress_cdn) from ressource_new");
          $sql_insere= mysql_query("INSERT INTO ressource_new (ress_cdn,ress_cat_lb,ress_typress_no,ress_url_lb,ress_auteurs_cmt,ress_publique_on,ress_titre,ress_desc_cmt,ress_create_dt,ress_ajout,ress_public_no,ress_type,ress_support,ress_doublon,ress_niveau) VALUES ('$id_new_ress',\"$mess_ress_direct_act\",'$parente',\"$rl\",\"Inconnu\",'NON',\"$titre\",\"$mess_no_comment\",\"$date_dujour\",\"$login\",'TOUT',\"ACTIVITES MULTIPLES\",\"Url\",'1','5')");
          $requete= mysql_query("UPDATE activite set act_ress_no = $id_new_ress,act_flag_on = 1 where act_cdn = $id_act");
          $les_params = str_replace("|","&",$params);
          if ($dou == "act_free")
             $lien="activite_free.php?creer=1&modifie_act=1&act_a_modif=$id_act".$les_params."&message=$mmsg_RessOk";
          else
             $lien="sequence$dou.php?action_act=1&modif_act=1&id_act=$id_act&id_ress=$id_new_ress".$les_params."&message=$mmsg_RessOk";
          $lien=urlencode($lien);
          echo "<SCRIPT Language=\"Javascript\">";
            echo "window.opener.location.replace(\"trace.php?link=$lien\")";
          echo "</SCRIPT>";
          ?>
          <SCRIPT language=javascript>
            setTimeout("Quit()",500);
              function Quit() {
                 self.opener=null;self.close();return false;
              }
          </SCRIPT>
          <?php
         exit();
  }
}
  //de quel type est l'utilisateur (apprenant, formateur, administrateur)
include ('style.inc.php');
?>
<SCRIPT language=JavaScript>
function checkForm(frm,media) {
  var ErrMsg = "<?php echo $mess_info_no;?>\n\n";
  var lenInit = ErrMsg.length;
     if (media == 1 && strstr(frm.userfile.value,'.swf')!= true && strstr(frm.userfile.value,'.mp3')!= true && strstr(frm.userfile.value,'.flv') != true )
         ErrMsg += ' - seuls les fichiers SWF, FLV ou MP3 sont autorisés\n';
     if (isEmpty(frm.userfile)==true)
         ErrMsg += ' - <?php echo $mess_casier_fat;?>\n';
     if (isEmpty(frm.titre)== true)
         ErrMsg += ' - <?php echo $msq_titre;?>\n';
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
if ($media == 1)
{
    entete_simple("Associer une ressource multimédia à la consigne");
    $formulaire = "document.form1,1";
    $mess_titres=$mess_titresadd1;
}
else
{
    entete_simple($mess_ass_act);
    $formulaire = "document.form1,0";
    $mess_titres=$mess_titresadd;
}
if ($message != "")
   echo "<TR><TD colspan='2' align='left'><b>$message</b></TD></TR>";
echo "<TR><TD colspan='2' align='left'>$mess_titres</TD></TR>";
if (isset($charger_fichier) && $charger_fichier == 1)
{
  echo "<FORM id='form1' NAME='form1' METHOD='POST' ENCTYPE=\"multipart/form-data\" action=\"charge_ressource_vrac.php\">";
  echo "<TR><TD colspan='2' align='left'><b>$mess_titnewress</b>&nbsp;&nbsp;&nbsp;&nbsp;";
  if (isset($_POST['titre']))
      echo "<INPUT type='TEXT' name='titre' size='55' value=\"".$_POST['titre']."\"></TD></TR>";
  else
      echo "<INPUT type='TEXT' name='titre' size='55'></TD></TR>";
  echo "<INPUT type='HIDDEN' name='dou' value='$dou'>";
  echo "<INPUT type='HIDDEN' name='media' value='$media'>";
  echo "<INPUT type='HIDDEN' name='charger' value='1'>";
  echo "<INPUT type='HIDDEN' name='charger_fichier' value='1'>";
  echo "<INPUT type='HIDDEN' name='id_act' value='$id_act'>";
  echo "<INPUT type='HIDDEN' name='params' value='$params'>";
  echo "<TR><TD>";
  echo "<INPUT TYPE='file' name='userfile' enctype='multipart/form-data'>";
  echo "</TD><TD><A href=\"javascript:checkForm($formulaire);\" onmouseover=\"img4.src='images/fiche_identite/boutvalidb.gif';return true;\" onmouseout=\"img4.src='images/fiche_identite/boutvalid.gif'\">";
  echo "<IMG NAME=\"img4\" SRC=\"images/fiche_identite/boutvalid.gif\" BORDER='0' onLoad=\"tempImg=new Image(0,0); tempImg.src='images/fiche_identite/boutvalidb.gif'\"></A>";
  echo "</TD></TR></FORM></TABLE></TD></TR></TABLE></body></html>";
  exit;
}
elseif (isset($charger_url) && $charger_url == 1)
{
  echo "<FORM id='form1' NAME='form1' METHOD='POST' action=\"charge_ressource_vrac.php\">";
  echo "<TR><TD colspan='2' align='left'><b>$mess_titnewress</b>&nbsp;&nbsp;&nbsp;&nbsp;";
  echo "<INPUT type='TEXT'  name='titre' size='55'></TD></TR>";
  echo "<INPUT type='HIDDEN' name='dou' value='$dou'>";
  echo "<INPUT type='HIDDEN' name='charger' value='1'>";
  echo "<INPUT type='HIDDEN' name='charger_url' value='1'>";
  echo "<INPUT type='HIDDEN' name='id_act' value='$id_act'>";
  echo "<INPUT type='HIDDEN' name='params' value='$params'>";
  echo "<TR><TD>";
  echo "<INPUT TYPE=TEXT name='userfile' size='60' value=\"http://\">";
  echo "</TD><TD><A href=\"javascript:checkForm($formulaire);\" onmouseover=\"img4.src='images/fiche_identite/boutvalidb.gif';return true;\" onmouseout=\"img4.src='images/fiche_identite/boutvalid.gif'\">";
  echo "<IMG NAME=\"img4\" SRC=\"images/fiche_identite/boutvalid.gif\" BORDER='0' onLoad=\"tempImg=new Image(0,0); tempImg.src='images/fiche_identite/boutvalidb.gif'\"></A>";
  echo "</TD></TR></FORM></TABLE></TD></TR></TABLE></body></html>";
  exit;
}
function getextension($fichier)
{
  $bouts = explode(".", $fichier);
  return array(array_pop($bouts), implode(".", $bouts));
}

?>