<?php
if (!isset($_SESSION)) session_start();
if (!isset($_SESSION['id_user']) || $_SESSION['id_user'] == "")
{
  exit();
}
include ("include/UrlParam2PhpVar.inc.php");
require 'admin.inc.php';
require "lang$lg.inc.php";
require 'fonction.inc.php';
require "fonction_html.inc.php";
require "langues/module.inc.php";
//include ("click_droit.txt");
dbConnect();
//............................................................................
//de quel type est l'utilisateur (apprenant, formateur, administrateur)
$nom_user=GetDataField ($connect,"select util_nom_lb from utilisateur where util_cdn='$id_user'","util_nom_lb");
$prenom_user=GetDataField ($connect,"select util_prenom_lb from utilisateur where util_cdn='$id_user'","util_prenom_lb");
//...........................................................................
// Recherche au sein de la base QCM des enregistrements relevant des paramètres numero_qcm et code
$req=mysql_query("SELECT duree,titre_qcm FROM qcm_param WHERE ordre='$code'");
$duree=mysql_result($req,0,"duree");
$le_titre=strip_tags(mysql_result($req,0,"titre_qcm"));
?>
<HTML>
<HEAD>
  <TITLE><?php  echo "**** $le_titre";?></TITLE>
</HEAD>
<BODY marginwidth="0" marginheight="0" leftmargin="0" topmargin="0">
<?php
include 'style.inc.php';
entete_simple($le_titre);
if ($typ_user != 'APPRENANT')
   echo "<TR><TD bgcolor='#FFFFFF'>";
if (!$application)
    $debut=1;
if ($debut==1)
{
  $req_sql=mysql_query("SELECT n_pages,duree FROM qcm_param WHERE ordre='$code'");
  $nomb_p=mysql_result($req_sql,0,"n_pages");
  $duree=mysql_result($req_sql,0,"duree");
  $duree=duree_calc($duree);
  echo"<TR><TD><TABLE bgcolor='#FFFFFF' cellpadding='5' cellspacing='5'><TR align=top><TD><B>$mess_qcm_avis1 $duree</B></TD>";//.<br />$mess_qcm_avis2
  echo "</TR>";

  $sql=mysql_query("SELECT * FROM qcm_linker,qcm_donnees WHERE qcmlinker_param_no='$code' and
                    qcmlinker_data_no= qcm_data_cdn ORDER BY qcmlinker_number_no asc");
  $res_sql=mysql_num_rows($sql);
  $page = array();
  $question = array();
  $note = array();
  $multiple =array();
  $image = array();
  $val_val = array();
  $multi = array();
  $i=0;$total_points=0;
//  while ($i<$nomb_p)
  while ($i<$res_sql)
  {
    $n=$i+1;
    $multi[$n] = 0;
    $DataNo = mysql_result($sql,$i,"qcm_data_cdn");
    $page[$n]=mysql_result($sql,$i,"qcmlinker_number_no");
    $question[$n]=strip_tags(mysql_result($sql,$i,"question"));
    $nomb_lig_pp[$n]=mysql_result($sql,$i,"n_lignes");
    $note[$n]=mysql_result($sql,$i,"note");
    for ($valeur=1;$valeur<11;$valeur++)
    {
      $intitule=$valeur."_val";
      $val_val[$valeur]=mysql_result($sql,$i,$intitule);
      if ($val_val[$valeur] == 1)
        $multi[$n]++;
    }
    $total +=$note[$n];
    $multiple[$n]=mysql_result($sql,$i,"multiple");
    $image[$n]=mysql_result($sql,$i,"image");
    $img[$n]=mysql_result($sql,$i,"img_blb");
    $typ_img[$n]=mysql_result($sql,$i,"typ_img");
    echo "<TR align=top><TD colspan='2'>";
    echo $mess_qcm_quest_n."<B> $page[$n]</B><br />";
    echo $mess_qcm_quest_tit." <B>". NewHtmlEntityDecode($question[$n])."</B><br />";
    echo "$msq_note <B> $note[$n] </B><br />";
    echo "$mess_qcm_rep_pls<B> $multi[$n]<br /></td></tr>";
    echo "<TR><TD valign=top>";
    echo "<FORM name='form' action=\"qcm.php?inserer=1&application=1&code=$code\" method='POST'>";
    echo "<INPUT TYPE='HIDDEN' name=\"page[$n]\" value=\"$page[$n]\">";
    echo "<INPUT TYPE='HIDDEN' name=\"note[$n]\" value=\"$note[$n]\">";
    echo "<INPUT TYPE='HIDDEN' name=\"multiple[$n]\" value=\"$multiple[$n]\">";
    echo "<INPUT TYPE='HIDDEN' name=\"multi[$n]\" value=\"$multi[$n]\">";
    echo "<INPUT TYPE='HIDDEN' name=\"question[$n]\" value=\"$question[$n]\">";
//    echo "<INPUT TYPE='HIDDEN' name=\"nomb_p\" value=\"$nomb_p\">";
    echo "<INPUT TYPE='HIDDEN' name=\"nomb_pages\" value=\"$res_sql\">";
    echo "<INPUT TYPE='HIDDEN' name=\"nomb_lig_pp[$n]\" value=\"$nomb_lig_pp[$n]\">";
    echo "<INPUT TYPE='HIDDEN' name=\"id_act\" value=\"$id_act\">";
    // Boucle pour récupérer les réponses
    $pointeur=array();
    $rep=array();
    $reponse_juste=array();
    $validite=array();
    $validation=array();
    $cocher = ($multiple[$n] == 1) ? "CHECKBOX" : "CHECKBOX";
    echo "<div id='formid' class='le_formulaire_qcm'><TABLE border='0' bgcolor='#cee6ec' cellpadding='5' cellspacing='5' width='100%'>";
    $compteur=$compteur+$j;
    $flag=0;
    for ($j=0;$j<$nomb_lig_pp[$n];$j++)
    {
           $nn=$j+1;
           $point=$nn."_prop";
           $compt=$compteur+$nn;
           $rep[$compt] = mysql_result($sql,$i,$point);
           $val=$nn."_val";
           $validite[$compt]=mysql_result($sql,$i,$val);
           if ($validite[$compt] == 1)
           {
               $reponse_juste[$n] = $rep[$compt];
               $total_points++;
           }
           echo '<INPUT TYPE="HIDDEN"  name="rep['.$compt.']" value="'.$rep[$compt].'">';
           echo '<INPUT TYPE="HIDDEN"  name="validite['.$compt.']" value="'.$validite[$compt].'">';
           if (isset($reponse_juste[$n]))
              echo '<INPUT TYPE="HIDDEN"  name="reponse_juste['.$n.']" value="'.$reponse_juste[$n].'">';
           echo '<TR><TD style="font-weight:bold;color:#24677A;"><label for="validation['.$compt.']" style="cursor:pointer;">'.$nn.'</label></TD>';
           echo "<TD style=\"width:'90%';font-weight:bold;color:#24677A;\"><label for=\"validation[$compt]\" style=\"cursor:pointer;\">$rep[$compt]</label></TD>";
           echo '<TD style="text-align:right;"><INPUT TYPE="'.$cocher.'" style="cursor:pointer;text-align:right;"  id="validation['.$compt.']" name="validation['.$compt.']" title="'.$mess_qcm_oui_non.' "><br /></TD></TR>';

    }
    echo "</TABLE></TD>";
    if ($image[$n] != "non" && strstr($image[$n],'.'))
    {
          $ma_liste = list($width, $height, $type, $attr) = getimagesize("ressources/".$image[$n]);
          if ($ma_liste[0] > 300 || $ma_liste[1] > 200)
          {
             $ma_largeur = ceil($ma_liste[0]/4);
             $ma_hauteur = ceil($ma_liste[1]/4);
             echo "<TD><a href=\"javascript:void(0);\" ".
                  "onClick=\"window.open('affiche_image.php?code=$DataNo','Image','resizable=yes,scrollbars=yes,status=no,menubar=no')\" ".
                  bulle($mmsg_qcm_TImg,"","CENTER","ABOVE",120).
                  "<IMG SRC='ressources/$image[$n]' width='$ma_largeur' height='$ma_hauteur' border='0'></TD>";
          }
          else
             echo "<TD><IMG SRC='ressources/$image[$n]' border='0'></TD>";
          echo "<INPUT TYPE='HIDDEN' name='image[$n]' value=\"$image[$n]\">";
    }
    echo "</TR><tr height='20'><td colspan='2'></TD> </TR>";
    $i++;
  }
  echo '<INPUT TYPE="HIDDEN"  name="total_points" value="'.$total_points.'">';
  echo "<INPUT TYPE='HIDDEN' name='le_titre' value=\"$le_titre\">";
  echo "<INPUT TYPE='HIDDEN'  name='total' value='$total'>";
  echo "<tr height='20'><td colspan='2'><A HREF=\"javascript:document.form.submit();\" ".
           "onmouseover=\"img1.src='images/fiche_identite/boutvalidb$suffixer.gif';return true;\" ".
           "onmouseout=\"img1.src='images/fiche_identite/boutvalid$suffixer.gif'\">".
           "<IMG NAME=\"img1\" SRC=\"images/fiche_identite/boutvalid$suffixer.gif\" BORDER='0' ".
           "onLoad=\"tempImg=new Image(0,0); tempImg.src='images/fiche_identite/boutvalidb$suffixer.gif'\"></A></td></tr>";
  echo "</FORM></td></tr></table></div></td></tr></table></td></tr></table></BODY></HTML>";

  exit;
}
// Fin de la procédure de récupération des variables
if ($inserer == 1)
{
/*
echo "<pre>";
     print_r($_POST);
echo "</pre>";
*/
  $insere=0;
  $note_totale=0;
  $compteur=0;
  if ($typ_user == 'APPRENANT')
  {
    $actseq = GetDataField ($connect,"select act_seq_no from activite where act_cdn = $id_act","act_seq_no");
    $actform = GetDataField ($connect,"select presc_formateur_no from prescription_$numero_groupe where presc_seq_no = $actseq and presc_utilisateur_no = $id_user","presc_formateur_no");
    $req2=mysql_query("SELECT util_login_lb,util_nom_lb FROM utilisateur WHERE util_cdn='$actform'");
    $id_tut = $actform;
    $nom_tut=mysql_result($req2,0,"util_nom_lb");
    $login_tut=mysql_result($req2,0,"util_login_lb");
    $dir_app="ressources/".$login."_".$id_user."/devoirs";
    $dir_tut="ressources/".$login_tut."_".$id_tut."/devoirs";
    //Vérifie si l'apprenant possede un dossier à son nom dans le tiroir "DEVOIRS" de son formateur sinon il en crée un
    $handle1=opendir($dir_app);
    $drap1=0;
    while ($file = readdir($handle1))
    {
      if ($file == $numero_groupe)
      {
          $dir_app="ressources/".$login."_".$id_user."/devoirs/$numero_groupe";
          $fichier_app = $dir_app."/qcm_formagri_".$code.".html";
          $drap1 = 1;
          break;
      }
    }
    closedir($handle1);
    if ($drap1 == 0)
    {
       chdir($dir_app);
       mkdir($numero_groupe,0777);
       chmod($numero_groupe,0777);
       $dir_app="ressources/".$login."_".$id_user."/devoirs/$numero_groupe";
       $fichier_app = $dir_app."/qcm_formagri_".$code.".html";
       chdir("../../../");
    }
    //Vérifie si l'apprenant possede un dossier à son nom dans le tiroir "DEVOIRS" de son formateur sinon il en crée un
    $handle=opendir($dir_tut);
    $i=0;$drap=0;
    while ($file = readdir($handle))
    {
        if ($file == "$login--$id_user")
        {
          $dir_tut="ressources/".$login_tut."_".$id_tut."/devoirs/"."$login--$id_user";
          $drap=1;
        }
    }
    if ($drap == 0)
    {
        chdir($dir_tut);
        mkdir("$login--$id_user",0775);
        $dir_tut=$repertoire."/ressources/".$login_tut."_".$id_tut."/devoirs/"."$login--$id_user";
        chdir("../../../");
    }
    $handle=opendir($dir_tut);
    $i=0;$drap=0;
    while ($file = readdir($handle))
    {
        if ($file == $numero_groupe)
        {
          $dir_tut="ressources/".$login_tut."_".$id_tut."/devoirs/$login--$id_user/".$numero_groupe;
          $fichier_tut=$dir_tut."/qcm_formagri_".$code."_".$nom_user.".html";
          $drap=1;
        }
    }
    closedir($handle) ;
    if ($drap==0)
    {
        chdir($dir_tut);
        mkdir($numero_groupe,0775);
        $dir_tut=$repertoire."/ressources/".$login_tut."_".$id_tut."/devoirs/$login--$id_user/".$numero_groupe;
        $fichier_tut=$dir_tut."/qcm_formagri_".$code."_".$nom_user.".html";
        chdir("../../../../");
    }
    //fin du test

  }// fin de if(apprenant)
  $content='';
  $content_app ="<HTML><head><title>$mess_qcm_rendu_titre</TITLE></HEAD><BODY bgcolor='#FFFFFF'><P><table bgcolor='#FFFFFF' width=100%>\n\n";
  $content_tut ="<HTML><head><title>$mess_qcm_rendu_titre</TITLE></HEAD><BODY bgcolor='#FFFFFF'><P><table bgcolor='#FFFFFF' width=100%>\n\n";
  $suite_deb = ($typ_user == 'APPRENANT') ? "$mess_qcm_rendu_tut $nom_tut" : "$mess_qcm_rendu_app $prenom_user $nom_user";
  $content_fin .= "<tr><td style=\"font-weight:bold;height:40px;\">$suite_deb</td></tr>";
  $i=0;
//  while ($i<$nomb_p)
  while ($i<$nomb_pages)
  {
    $n=$i+1;
    $image = GetDataField ($connect,"SELECT image FROM qcm_donnees,qcm_linker WHERE qcmlinker_param_no='$code' and
                                     qcmlinker_data_no=qcm_data_cdn and qcmlinker_number_no='$n'","image");
    $content .= "<tr><td><table padding: 4px;\" cellspacing='20'><tr><td colspan=2>";
    $content .= "$mess_qcm_quest_n<B>$page[$n]</B><br />\n";
    $content .= "$mess_qcm_quest_tit<B>".stripslashes($question[$n])."</B><br />\n";
    $content .= "$mess_qcm_quest_not<B>$note[$n]</B><br />\n";
    if ($multiple[$n] == 1)
       $content .="$mess_qcm_qrm<br />\n";
    $content .= "</td></tr><tr><td><div id='formid' class='le_formulaire_qcm'><TABLE border='0' bgcolor='#cee6ec' cellpadding='5' cellspacing='5' width=100%>";
    $compteur=$compteur+$j;
    $j = 0;$passe=0;
    $note_obtenue = 0;
    while ($j < $nomb_lig_pp[$n])
    {
      $nn = $j+1;
      $compt = $compteur+$nn;
      $content .= "<tr>";
      if (isset($validation[$compt]) && $validation[$compt] == 'on')
        $validation[$compt] = 1;
      else
        $validation[$compt] = 0;
      if (isset($validation[$compt]) && $validation[$compt] == 1  && $validite[$compt] == 1)
      {
        $note_obtenue ++;
        $content .="<td valign='top' style=\"font-weight:bold;width:'90%';\">".stripslashes($rep[$compt])."</td><td align='right'><img src=\"$adresse_http/images/media/completed.gif\" border='0'></td></tr>\n";
      }
      elseif (isset($validation[$compt]) && $validation[$compt] == 1 && $validite[$compt] == 0)
      {
            $mauvais=1;
            $content .="<td valign='top' style=\"font-weight:bold;width:'90%';\">".stripslashes($rep[$compt])."</td><td align='right'><img src=\"$adresse_http/images/media/failed.gif\" border='0'></td></tr>\n";
      }
      elseif (isset($validation[$compt]) && $validation[$compt] == 0 && $validite[$compt] == 1)
      {
            $mauvais=1;
            $content .="<td valign='top' style=\"font-weight:bold;width:'90%';\">".stripslashes($rep[$compt])."</td><td align='right'><img src=\"$adresse_http/images/media/incomplete.gif\" border='0'></td></tr>\n";
      }
      elseif (isset($validation[$compt]) && $validation[$compt] == 0 && $validite[$compt] == 0)
            $content .="<td valign='top'  style=\"font-weight:bold;width:'90%';\">".stripslashes($rep[$compt])."</td><td align='right'><img src=\"$adresse_http/images/media/notattempted.gif\" border='0'></td></tr>\n";

      if ($note_obtenue == $multi[$n] && $multi[$n] >= 1)
        $nota = $note[$n];
      elseif ($note_obtenue != $multi[$n] && $multi[$n] > 1)
        $nota = 0;
      if (($nn == $nomb_lig_pp[$n]) && ($mauvais == 0))
        $content .="<tr><td colspan='2'>$mess_qcm_nbrp <B>$nota</B></td></tr>";
      if (($nn == $nomb_lig_pp[$n]) && ($mauvais == 1))
        $content .="<tr><td colspan='2'>$mess_qcm_nbrp <B>0</B></td></tr>";
      $j++;
    }
      $content .="</table></div>\n\n";
      $content .="</td>\n";
    if (strtolower($image) != 'non' && strstr($image,'.'))
    {
      $ma_liste = list($width, $height, $type, $attr) = getimagesize("ressources/".$image);
      if ($ma_liste[0] > 300 || $ma_liste[1] > 200)
      {
             $ma_largeur = ceil($ma_liste[0]/4);
             $ma_hauteur = ceil($ma_liste[1]/4);
             $content .= "<TD valign='middle'><a href=\"javascript:void(0);\" title = \"$mmsg_qcm_TImg\" ".
                  "onClick=\"window.open('".$adresse_http."/ressources/$image','Image','resizable=yes,scrollbars=yes,status=no,menubar=no')\">".
                  "<IMG SRC='".$adresse_http."/ressources/$image' width='$ma_largeur' height='$ma_hauteur' border='0'>".
                  "</TD></tr></table></td></tr>";
          }
          else
             $content .= "<TD valign='top'><IMG SRC='".$adresse_http."/ressources/$image' border='0'></TD></tr></table></td></tr>";
    //if (strtolower($image) != 'non' && strstr($image,'.'))
       //$content .= "<td valign='middle'><img src=\"/ressources/$image\"></td></tr></table></td></tr>";
   } else
       $content .= "<td valign='top'></td></tr></table></td></tr>";
    $content .="<tr><td colspan='2' style=\"height:25px;\">&nbsp;</td></tr>\n";
    $i++;
    if ($mauvais == 0 )
      $note_totale +=$nota;
    else
      $mauvais=0;
  }
  $content_fin .="<tr><td colspan='2'><B>$mess_qcm_noqcm $note_totale  &nbsp;/&nbsp; $total</B></td></tr>\n";
  $content .="<tr><td colspan='2' style=\"height:25px;\">&nbsp;</td></tr>\n";
  $content_app .= $content_fin.$content;
  $content_tut .= $content_fin.$content;
  if ($typ_user == 'APPRENANT')
  {
     $fp_app=fopen($fichier_app,"w") or DIE ("$mess_qcm_nowrt $fichier_app");
     fputs($fp_app,$content_app);
     fclose( $fp_app);
     $fp_tut=fopen($fichier_tut,"w") or DIE ("$mess_qcm_nowrt $fichier_tut");
     fputs($fp_tut,$content_tut);
     fclose( $fp_tut);
     $acquittement = ceil($note_totale*20/$total);
     $moyenne = GetDataField ($connect,"select moyenne from qcm_param where ordre='$code'","moyenne");
     $pass_mult = GetDataField ($connect,"select act_passagemult_on from activite where act_cdn='$id_act'","act_passagemult_on");
     if ($acquittement < $moyenne)
     {
        $message = "$mess_qcm_mess_acq1 $acquittement $mess_qcm_mess_acq2<br />";
        if ($pass_mult == 'OUI')
           $change_etat = mysql_query("update suivi1_$numero_groupe set suivi_etat_lb='A FAIRE' where suivi_act_no=$id_act and suivi_utilisateur_no=$id_user");
        $change_comment = mysql_query("update suivi1_$numero_groupe set suivi_commentaire_cmt ='Activité repassée' where suivi_act_no=$id_act and suivi_utilisateur_no=$id_user");
        $change_note = mysql_query("update suivi1_$numero_groupe set suivi_note_nb1='non acquis' where suivi_act_no=$id_act and suivi_utilisateur_no=$id_user");
     }
     else
     {
        $change_etat = mysql_query("update suivi1_$numero_groupe set suivi_etat_lb='TERMINE' where suivi_act_no=$id_act and suivi_utilisateur_no=$id_user");
        $change_note = mysql_query("update suivi1_$numero_groupe set suivi_note_nb1='acquis' where suivi_act_no=$id_act and suivi_utilisateur_no=$id_user");
     }
      $nom_du_qcm = "ressources/".$login."_".$id_user."/devoirs/$numero_groupe/qcm_formagri_".$code.".html";
      $n_qcm = "qcm_formagri_".$code.".html";
      $inserer_fichier = mysql_query("update suivi1_$numero_groupe set suivi_fichier_lb='$nom_du_qcm' where suivi_act_no=$id_act and suivi_utilisateur_no=$id_user and suivi_grp_no=$numero_groupe");

      echo" <TR><TD colspan=2 bgColor='#FFFFFF'><P>";
      echo "<B>$message $mess_qcm_cop_env &nbsp;&nbsp;&nbsp;</B><P>";
      $direction = "ressources/".$login."_".$id_user."/devoirs/$numero_groupe/".$n_qcm;
      echo "<A href=\"$direction\"><IMG SRC='images/qcm_dev.gif' border=0></A></td></tr>";
      //echo "</td></tr></table></td></tr></table>";
      echo "<tr><td colspan='2' style=\"height:25px;\">&nbsp;</td></tr>\n";
      echo $content_app."</TD></TR></TABLE></TD></TR></TABLE>";
      $agent=getenv("HTTP_USER_AGENT");
      if (strstr($agent,"MSIE"))
      {
         echo "<SCRIPT Language=\"Javascript\">";
            echo "window.parent.opener.location.reload();";
         echo "</SCRIPT>";
      }
      else
      {
         echo "<SCRIPT Language=\"Javascript\">";
            echo "parent.parent.opener.location.reload();";
         echo "</SCRIPT>";
      }
  }
  else
      echo $content_tut."</TD></TR></TABLE></TD></TR></TABLE>";
}//Fin de la sauvegarde chez l'apprenant et le tuteur
echo "</BODY></HTML>";
$content = '';
?>

