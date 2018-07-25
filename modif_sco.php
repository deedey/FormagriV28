<?php
/*
modification après le 15/11/06
--------------------------------------
gestion ascenseur du prérequis et gestion du visible dans la modification avec ajout de la fonction
Ascenseur_sans_blanc($Nom,$req,$connexion,$param)
*/
if (!isset($_SESSION)) session_start();
include ("include/UrlParam2PhpVar.inc.php");
if (!isset($_SESSION['id_user']) || $_SESSION['id_user'] == "")
{
  exit();
}
require 'admin.inc.php';
require 'fonction.inc.php';
require "lang$lg.inc.php";
require "fonction_html.inc.php";
dbConnect();
include 'style.inc.php';
$req_seq = requete("*","sequence"," seq_cdn = '$id_seq'");
if ($req_seq == false)
{
  echo "Il n'y a aucun résultat pour cette requete";
  exit;
}
if ($prov == "seq")
{
  $incl = "liste_seq.inc.php";
  $suite_url ="_entree";
  $suiteVars = "";
}
else
{
  $suiteVars = "&parcours=$parcours&id_parc=$id_parc";
  $incl = "liste_parc.inc.php";
}
while ($item = mysql_fetch_object($req_seq)) {
      $id_auteur = $item->seq_auteur_no;
      $titre_sequence = $item->seq_titre_lb;
      $nom_sequence = $item->seq_desc_cmt;
      $droit_dupli_seq = $item->seq_publique_on;
}
      $launch = GetDataField ($connect,"select mod_launch_lb from scorm_module where mod_cdn = '$id_act'","mod_launch_lb");
      ?>
      <SCRIPT language=JavaScript>
      function checkForm(frm) {
      var ErrMsg = "<?php echo $mess_info_no;?>\n";
      var lenInit = ErrMsg.length;
      if (isEmpty(frm.nom)==true)
        ErrMsg += ' - <?php echo $msq_tit_form;?>\n';
      if (isEmpty(frm.description)==true)
        ErrMsg += ' - <?php echo $msq_consigne_act_form;?>\n';
        <?php /*
          if ($launch != ""){?>
      if (isEmpty(frm.horaire)==true && isEmpty(frm.minutage)==true)
        ErrMsg += ' - <?php echo $msq_horaire_act_form;?>\n';
        <?php }*/
        ?>
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
if ($nom_sequence == '')
   $nom_sequence = $msq_no_decrite;
$bgcolor2 = '#2b677a';
$bgcolor1 = '#F8F2E4';
$letitre ="$msq_seq : $titre_sequence";
if (!isset($_SESSION['requete_parc']))
   $_SESSION['requete_parc']=$requete_parc;
if (!isset($_SESSION['requete_seq']))
   $_SESSION['requete_seq']=$requete_seq;
echo "<CENTER><TABLE bgColor='#298CA0' cellspacing='2' cellpadding='0' width='98%'>";
include ("$incl");

echo "<TD valign='top' width='70%' height='100%' bgColor='#FFFFFF'>";
echo "<form name='form1' action=\"sequence$suite_url.php?liste=$liste&consult_act=1&modif=1$suiteVars&proprio=$proprio&refer=$refer&id_act=$id_act&id_seq=$id_seq&id_ress=$id_ress&ordre1=$ordre&id_ref=$id_ref&id_ref_parc=$id_ref_parc&auteur=$auteur&miens=$miens&miens_parc=$miens_parc&parc=$id_parc&liste_act_seq=$liste_act_seq\" target='main' method='post'>";
echo "<TABLE cellspacing='1' cellpadding='0' width='100%'>";
echo "<TR><TD background=\"images/fond_titre_table.jpg\" colspan='2' height='40' align='center' valign='center'><Font size='3' color='#FFFFFF'><B>$letitre</B></FONT></TD></TR>";
$req_mod = requete("*","scorm_module"," mod_cdn = '$id_act'");
if ($req_mod == false){
  echo "Il n'y a aucun résultat pour cette requete";
  exit;
}
$code = array();
$id_act_seq = array();
while ($item = mysql_fetch_object($req_mod)) {
      $id = $item->mod_cdn;
      $nom = $item->mod_titre_lb;
      $description = $item->mod_desc_cmt;
      $consigne = $item->mod_consigne_cmt;
      $motclef = $item->mod_motclef_lb;
      $numero = $item->mod_numero_lb;
      $launch = $item->mod_launch_lb;
      $visible = $item->mod_visible;
      $duree = $item->mod_duree_nb;
      $prerequis = $item->mod_prereq_lb;
      $score_min = $item->mod_masteryscore;
      $maxtime = $item->mod_maxtimeallowed;
      if ($visible == 'TRUE')
         $visible1 = 'OUI';
      else
         $visible1 = 'NON';
      if ($prerequis != ""){
         $prereq1 = GetDataField ($connect,"select mod_cdn from scorm_module where mod_numero_lb = \"$prerequis\" AND mod_seq_no = '$id_seq'","mod_cdn");
         $prereq = GetDataField ($connect,"select mod_titre_lb from scorm_module where mod_cdn = \"$prereq1\"","mod_titre_lb");
      }else
         $prereq = '';
      echo "<TR><TD nowrap><TABLE cellspacing='0' cellpadding='4'>";
      echo "<INPUT TYPE='HIDDEN' class='INPUT' name='mod_seq' value=\"SCORM\" align='middle'></TD></TR>";
      echo "<INPUT TYPE='HIDDEN' class='INPUT' name='visible' value=\"$visible\" align='middle'></TD></TR>";
      if ($launch == "")
         echo "<TR height='40' ><TD nowrap valign='center' colspan='2'><B>$mess_act_label</B></TD></TR>";
      echo "<TR><TD nowrap><B>$msq_tit_form</B></TD><TD nowrap>";
      echo "<INPUT TYPE='text' class='INPUT' name='nom' value=\"".stripcslashes($nom)."\" size='75' align='middle'></TD></TR>";
      echo "<TR><TD nowrap valign='top'><B>$msq_desc_seq</B></TD>";
      echo "<TD nowrap><TEXTAREA class='TEXTAREA' name='description' rows='6' cols='80' align='middle'>".
           html_entity_decode($description,ENT_QUOTES,'iso-8859-1')."</TEXTAREA></TD></TR>";
      if ($launch != ""){
         echo "<TR><TD nowrap><B>$msq_consigne_act_form</B></TD>";
         echo "<TD nowrap><TEXTAREA class='TEXTAREA' rows='6' cols='80' align=\"middle\" name=\"consigne\">".
              html_entity_decode($consigne,ENT_QUOTES,'iso-8859-1')."</TEXTAREA><br></TD></TR>";
      }
      echo "<TR><TD nowrap valign='top'><B>$mess_motclef</B></TD><TD nowrap>";
      echo "<TEXTAREA class='TEXTAREA' name='motclef' rows='5' cols='80' align='middle'>".
           html_entity_decode($motclef,ENT_QUOTES,'iso-8859-1')."</TEXTAREA></TD></TR>";
      if ($launch != ""){
         echo "<TR><TD nowrap><B>$msq_duree_seq</B></TD><TD><TABLE cellspacing='0'><TR>";
         if ($duree > 0){
           $reste = $duree%60;
           $heure = floor($duree/60);
           if ($reste == 0)
             $duree1 = $heure.$h;
           else
             $duree1 = $heure.$h.$reste;
         }
         if ($heure == 0 && $reste == 0){
           echo "<TD><INPUT TYPE='text' class='INPUT' name='horaire' size='2' maxlength = '3' align='center'>$h </TD>";
           echo "<TD><INPUT TYPE='text' class='INPUT' name='minutage' size='2' maxlength = '2' align='center'>$mn</TD></TR></TABLE>";
         }else{
           echo "<TD><INPUT TYPE='text' class='INPUT' name='horaire' value='$heure' size='2' maxlength = '3'>$h </TD>";
           echo "<TD><INPUT TYPE='text' class='INPUT' name='minutage' value='$reste' size='2'  maxlength = '2'>$mn</TD>";
           echo "</TR></TABLE>";
         }
         echo "</TD></TR>";
         if ($maxtime != '' && strlen($maxtime) > 9){
            $liste_duree = explode(":",$maxtime);
            $maxtime = ($liste_duree[0]*60)+$liste_duree[1];
         }
         echo "<TR><TD nowrap valign='top'><B>$mess_dur_max ($mn)</B></TD><TD nowrap valign='top'>";
         echo "<INPUT TYPE='text' class='INPUT' name='maxtime' value=\"$maxtime\" size='4' Maxlength='4' align='middle'></TD></TR>";
         echo "<TR><TD nowrap valign='top'><B>$mess_sco_min (/100)</B></TD><TD nowrap valign='top'>";
         echo "<INPUT TYPE='text' class='INPUT' name='score_min' value='$score_min' size='3' Maxlength='3' align='middle'></TD></TR>";
         echo "<TR><TD nowrap valign='top'><B>$mrc_ins_adr</B></TD>";
         if (strstr($launch,"http://"))
         {
            $link = substr($launch,0,31)."..........";
            echo "<TD valign='top'><B>$link</B></TD>";
         }
         elseif (strstr($launch,"ressources/") && !strstr($launch,"http://"))
         {
            $link = $launch;
            echo "<TD valign='top'><INPUT TYPE='text' class='INPUT' name='link' value=\"$link\" size='75' align='middle'></TD></TR>";
         }
/*
         echo "<TR><TD nowrap valign='top'><B>$mess_visible</B></TD>";
         echo "<TD valign='top'><SELECT name='visible'>";
         if ($visible == "TRUE"){
            echo "<OPTION value=\"$visible\"selected>$visible1</OPTION>";
            echo "<OPTION value=\"FALSE\">$mess_non</OPTION></TD></TR>";
         }elseif($visible == "FALSE"){
            echo "<OPTION value=\"$visible\"selected>$visible1</OPTION>";
            echo "<OPTION value=\"TRUE\">$mess_oui</OPTION></TD></TR>";
         }
*/
         echo "<TR><TD nowrap valign='top'><B>$msq_si_prereq</B></TD>";
         echo "<TD valign='top'>";
         //echo "<INPUT TYPE='text' class='INPUT' name='prereq' value=\"$prereq\" size='75' align='middle'>";
         $param = ($prerequis != "") ? $prerequis : "";
         Ascenseur_sans_blanc ("prereq","SELECT mod_numero_lb,mod_titre_lb from scorm_module where mod_seq_no=$id_seq and mod_cdn < $id and mod_niveau_no > 0 ORDER BY mod_cdn",$connect,$param);
         echo "</TD></TR>";
      }else
         echo "<INPUT TYPE='HIDDEN' name='link' value='rien'>";
}
if (($id_auteur == $id_user || $typ_user == 'ADMINISTRATEUR') && $modif_act == 1){
    echo "<TR><TD>&nbsp;</TD><TD align='left' height='50'><A HREF=\"javascript:checkForm(document.form1);\" onmouseover=\"img1.src='images/fiche_identite/boutvalidb$suffixer.gif';return true;\" onmouseout=\"img1.src='images/fiche_identite/boutvalid$suffixer.gif'\">".
        "<IMG NAME=\"img1\" SRC=\"images/fiche_identite/boutvalid$suffixer.gif\" BORDER='0' onLoad=\"tempImg=new Image(0,0); tempImg.src='images/fiche_identite/boutvalidb$suffixer.gif'\"></A>";
}
echo "</TABLE></TD></TR></FORM></TABLE></TD></TR></TABLE></TD></TR></TABLE>";
exit;

?>