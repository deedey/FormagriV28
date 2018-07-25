<?php
 /****************************************** ***************
  *                                                         *
  * Copyright  formagri/cnerta/eduter/enesad                *
  * Dey Bendifallah                                         *
  * Ce script fait partie intégrante du LMS Formagri.       *
  * Il peut être modifié ou utilisé à d'autres fins.        *
  * Il est libre et sous licence GPL                        *
  * Les auteurs n'apportent aucune garantie                 *
  *                                                         *
  **********************************************************/
if (!isset($_SESSION)) session_start();
if (!isset($_SESSION['id_user']) || $_SESSION['id_user'] == "")
{
  exit();
}
include ("include/UrlParam2PhpVar.inc.php");

require 'fonction.inc.php';
require 'graphique/admin.inc.php';
require "lang$lg.inc.php";
require "fonction_html.inc.php";
require "class/class_module.php";
require ('class/Class_Rss.php');
require ('langues/module.inc.php');

dbConnect();
if (isset($_SESSION['acces']))
  unset($_SESSION['acces']);
$date_dujour = date ("Y-m-d");
$html = "";
$seqduref = (GetDataField ($connect,"select param_etat_lb from param_foad where param_typ_lb = 'seqduref'","param_etat_lb") == 'OUI' ) ? 1 : 0;
$nom_user = $_SESSION['name_user'];
//Fichier gérant les séquences et les activités (CMS)
include ('style.inc.php');
ini_set('error_reporting','E_ALL');
?>
<div id="affiche" class="Status"></div>
<div id="mon_contenu" class="cms" onClick="javascript:$(document).ready(function() {if ($.browser.msie) {$('#mon_contenu').hide();}else{$('#mon_contenu').hide('slow');}})"<?php echo " title=\"$mess_clkF\"></div>";
if ($id_ref_parc > 0 && $toutes_seq == 1 && !$activite)
  $leref = $id_ref_parc;
elseif ($id_ref > 0 && $toutes_seq != 1 && !$activite)
  $leref = $id_ref;
elseif ($id_ref_parc > 0 && $toutes_seq != 1 && !$activite)
  $leref = $id_ref_parc;
if ($parcours == 1)
{
   $nom_parc = GetDataField ($connect,"select parcours_nom_lb from parcours where parcours_cdn = '$id_parc'","parcours_nom_lb");
   $desc_parc = GetDataField ($connect,"select parcours_desc_cmt from parcours where parcours_cdn = '$id_parc'","parcours_desc_cmt");
   $sous_titre = "<B>$msq_parc :</B> $nom_parc";
}
if ($leref > 0 && !$activite)
{
  $referentiel = GetDataField ($connect,"select ref_nomabrege_lb from referentiel where ref_cdn = $leref","ref_nomabrege_lb");
  $nom_referentiel = GetDataField ($connect,"select ref_desc_cmt  from referentiel where ref_cdn = $leref","ref_desc_cmt");
  if ($nom_referentiel == "")
    $nom_referentiel = GetDataField ($connect,"select ref_nomabrege_lb from referentiel where ref_cdn = $leref","ref_nomabrege_lb");
    $nbr_caract = strlen($nom_referentiel);
    $nom_ref = $nom_referentiel;
    $sous_titre .= "<BR><B>$msq_ref :</B> $referentiel -->&nbsp;<small>".html_entity_decode($nom_ref,ENT_QUOTES,'iso-8859-1')."</small>";
}elseif ($id_ref == 0 && !$activite && !$prem)
    $sous_titre .= "<BR><B>$seq_hrs_ref</B>";
if ($creation == 1 || $choix_ref == 1)
{
    $message = "<center><B>$msq_crea</B></center>";
}
if (isset($message) && $message != '' && ($creation != 1 || $choix_ref != 1))
{
   echo notifier($message);
}
$etat_fav = GetDataField($connect,"select param_etat_lb from param_foad where param_typ_lb='favoris'","param_etat_lb");
if (isset($_GET['dupli_seq']) && $_GET['dupli_seq'] == 1 &&  (!isset($copier_seq) || (isset($copier_seq) && $copier_seq= '')))
{
  ?>
  <SCRIPT language=JavaScript>
    function checkForm(frm) {
      var ErrMsg = "<?php echo $mess_info_no;?>\n\n";
      var lenInit = ErrMsg.length;
      if (isEmpty(frm.titre)==true)
        ErrMsg += ' - <?php echo $msq_titre;?>\n';
      if (isEmpty(frm.description)==true)
        ErrMsg += ' - <?php echo $msq_desc;?>\n';
      if (isVide(frm.type_seq)==true)
        ErrMsg += ' - <?php echo $mess_presc_ok;?>\n';
      if (isVide(frm.droit_voir_seq)==true)
        ErrMsg += ' - <?php echo $msq_droit_voir;?>\n';
      if (isVide(frm.ordre_act)==true)
        ErrMsg += ' - <?php echo addslashes($msq_act_ord);?>\n';
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
    function isVide(elm) {
               if(!elm[0].checked && !elm[1].checked){
                    return true;
               }
      return false;
    }
  </SCRIPT>
  <?php
    entete_concept("liste_parc.inc.php",$mess_dupli_tit);
    echo aide_simple("sequence");
    $seq_query = requete("*","sequence","seq_cdn = $id_seq");
    $nb_seq = mysql_num_rows ($seq_query);
    $DataSeq = mysql_fetch_object($seq_query);
    $nom = $DataSeq->seq_titre_lb;
    $desc = html_entity_decode($DataSeq->seq_desc_cmt,ENT_QUOTES,'iso-8859-1');
    $mots_clef = html_entity_decode($DataSeq->seq_mots_clef,ENT_QUOTES,'iso-8859-1');
    $duree = $DataSeq->seq_duree_nb;
    $droit_voir_seq = $DataSeq->seq_publique_on;
    $type_seq = $DataSeq->seq_type_on;
    $ordre_act = $DataSeq->seq_ordreact_on;
    $mode_seq = $DataSeq->seq_type_lb;
    echo "<TR><TD colspan='2'>";
    echo "<FORM NAME='form1' ACTION=\"sequence.php?liste=$liste&utilisateur=$utilisateur&parcours=$parcours&id_parc=$id_parc&proprio=$proprio&refer=$refer&id_seq=$id_seq&id_ref=$id_ref&id_ref_parc=$id_ref_parc&dupli_seq=1&copier_seq=1&droit_voir=$droit_voir&duree=$duree&parc=$id_parc&toutes_seq=$toutes_seq\" METHOD='POST' target='main'>";
    echo "<INPUT TYPE='HIDDEN'  name='nom' value=\"$nom\" align='middle'>";
    echo "<INPUT TYPE='HIDDEN'  name='mode_seq' value=\"$mode_seq\" align='middle'>";
    echo "<input type='hidden' name='mots_clef' value='none'>";
    echo "<TABLE width='100%' cellspacing='1' cellpadding='3'>";
    echo "<TR><TD colspan='2' class='sous_titre'>$sous_titre</TD></TR>";
    echo "<TR><TD valign='top'><Font size='2'><B>$mess_modif_dupli</B></TD></TR>";
    echo "<TR><TD nowrap><B>$msq_titre</B></TD>";
    echo "<TD nowrap><INPUT TYPE='text' class='INPUT'  name='titre' size='75' value=\"$nom\" align='middle'></TD></TR>";
    echo "<TR><TD nowrap><B>$msq_desc_seq</B></TD>";
    echo "<TD nowrap><TEXTAREA class='TEXTAREA' name='description' align='middle' rows='6' cols='80'>$desc</TEXTAREA><br></TD></TR>";
    echo "<TR><TD nowrap><B>$mess_extension</B></TD>";
    echo "<TD nowrap><INPUT type='text' class='INPUT'  name='extension' size='20'>&nbsp;&nbsp;&nbsp;&nbsp;";
    echo anoter($mess_extens_dupli,'');
    echo "</TD></TR>";
    $l_aide = anoter($mess_dupli_presc,'');
    echo "<TR><TD nowrap><TABLE cellpadding='0' cellspacing='0' border='0'>";
    echo "<TR height='30'><TD><B>$mess_presc_ok</B></TD></TR>";
    echo "<TR height='30'><TD nowrap><B>$msq_droit_voir</B></TD></TR></TABLE></TD>";
    echo "<TD nowrap><TABLE cellpadding='0' cellspacing='0' border='0'><TR height='30'><TD nowrap>";
    if ($type_seq == 1) {
       echo "$mess_oui&nbsp;<INPUT type='radio' name='type_seq' value='0'>&nbsp;&nbsp;&nbsp;&nbsp;</TD><TD>";
       echo "$mess_non&nbsp;<INPUT type='radio' name='type_seq' value='1' checked>&nbsp;</TD><TD rowspan='2' valign='center'>";
    }elseif ($type_seq == 0) {
       echo "$mess_oui&nbsp;<INPUT type='radio' name='type_seq' value='0' checked>&nbsp;&nbsp;&nbsp;&nbsp;</TD><TD>";
       echo "$mess_non&nbsp;<INPUT type='radio' name='type_seq' value='1'>&nbsp;</TD><TD rowspan='2' valign='center'>";
    }
    echo "$l_aide</TD></TR><TR height='30'><TD>";
    if ($droit_voir_seq == 1 || $droit_voir_seq == '') {
       echo "$mess_oui&nbsp;<INPUT type='radio' name='droit_voir_seq' value='1' checked>&nbsp;</TD><TD>";
       echo "$mess_non&nbsp;<INPUT type='radio' name='droit_voir_seq' value='0'>";
    }elseif ($droit_voir_seq == 0) {
       echo "$mess_oui&nbsp;<INPUT type='radio' name='droit_voir_seq' value='1'>&nbsp;</TD><TD>";
       echo "$mess_non&nbsp;<INPUT type='radio' name='droit_voir_seq' value='0' checked>";
    }
    echo "</TD></TR></TABLE></TD></TR>";
    echo "</TD></TR><TR height='25'><TD nowrap><B>$msq_ordre_act</B></TD><TD nowrap>";
    if ($ordre_act == 'OUI') {
            echo "$mess_oui&nbsp;<INPUT type='radio' name='ordre_act' value='1' checked>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
            echo "$mess_non&nbsp;<INPUT type='radio' name='ordre_act' value='0'>";
    }else{
            echo "$mess_oui&nbsp;<INPUT type='radio' name='ordre_act' value='1'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
            echo "$mess_non&nbsp;<INPUT type='radio' name='ordre_act' value='0' checked>";
    }
    echo "</TD></TR>";
    echo "<TR><TD nowrap><B>$msq_duree_seq</B></TD>";
    echo "<TD nowrap>";
    $duree = duree_calc($duree);
    echo $duree;
    echo "</TD></TR>";
    echo "<TR height=50><TD>&nbsp;</TD><TD align='left' valign='center'><A HREF=\"javascript:checkForm(document.form1);\" onClick=\"TinyMCE.prototype.triggerSave();\" onmouseover=\"img1.src='images/fiche_identite/boutvalidb$suffixer.gif';return true;\" onmouseout=\"img1.src='images/fiche_identite/boutvalid$suffixer.gif'\">".
         "<IMG NAME=\"img1\" SRC=\"images/fiche_identite/boutvalid$suffixer.gif\" BORDER='0' onLoad=\"tempImg=new Image(0,0); tempImg.src='images/fiche_identite/boutvalidb$suffixer.gif'\"></A>";
    echo "</TD></TR></form></TABLE>";
    fin_tableau($html);echo "</body></html>";
  exit;
}
if (isset($dupli_seq) && $dupli_seq == 1 && isset($_GET['copier_seq']) && $_GET['copier_seq'] == 1)
{
    $id_new_seq = Donne_ID ($connect,"select max(seq_cdn) from sequence");
    if ($titre == $nom)
    {
      if ($extension != "")
        $titre .=" $extension";
      else
        $titre .=" ($nom_user)";
    }
    $ordre_act = ($ordre_act == 1) ? "OUI" : "NON";
    $description = DelAmp(htmlentities($description,ENT_QUOTES,'iso-8859-1'));
    $mots_clef = DelAmp(htmlentities($mots_clef,ENT_QUOTES,'iso-8859-1'));
    $insert_new_seq = mysql_query ("insert into sequence values ($id_new_seq,\"".str_replace("\"","'",$titre)."\",\"$description\",\"$mots_clef\",\"$ordre_act\",$duree,$id_user,\"$date_dujour\",\"$date_dujour\",$droit_voir_seq,$type_seq,'$mode_seq')");
    $insert_rss = rss :: ajout('sequence',$id_user,$id_new_seq);
    $id_ref_seq = GetDataField ($connect,"select seqref_referentiel_no from sequence_referentiel where seqref_seq_no = $id_seq","seqref_referentiel_no");
    $id_seqref = Donne_ID ($connect,"select max(seqref_cdn) from sequence_referentiel");
    $autre_ref = mysql_query ("insert into sequence_referentiel values ($id_seqref,$id_new_seq,$id_ref)");
    $act_query = requete_order("*","activite","act_seq_no = $id_seq and act_publique_on = 1","act_ordre_nb");
    $Nb_act_seq = mysql_num_rows ($act_query);
    if ($Nb_act_seq == 0)
    {
       echo "$msq_noact<br><br>";
    }
    else
    {
            while ($DataAct = mysql_fetch_object($act_query))
            {
               $nommer =  str_replace("\"","'",$DataAct->act_nom_lb);
               if ($extension != "")
                  $nommer .= " $extension";
               else
                  $nommer .=" ($nom_user)";
               $id_new_act = Donne_ID ($connect,"SELECT MAX(act_cdn) FROM activite");
               $insert_new_act = mysql_query ("INSERT INTO activite VALUES ($id_new_act,$id_new_seq,".$DataAct->act_ordre_nb.
                                                ",\"".$nommer."\",\"".DelAmp($DataAct->act_consigne_cmt)."\",\"".DelAmp($DataAct->act_commentaire_cmt).
                                                "\",'".$DataAct->act_ress_on."',".$DataAct->act_ress_no.
                                                ",".$DataAct->act_duree_nb.",\"".$DataAct->act_passagemult_on.
                                                "\",\"".$DataAct->act_acquittement_lb."\",\"".$DataAct->act_notation_on.
                                                "\",\"".$DataAct->act_devoirarendre_on."\",'$id_user',\"$date_dujour\",".
                                                "\"$date_dujour\",'".$DataAct->act_publique_on."',".$DataAct->act_flag_on.")");
               $insert_rss = rss :: ajout('activite',$id_user,$id_new_act);
               $req_typdev = mysql_num_rows(mysql_query("select *from activite_devoir where actdev_act_no = ".$DataAct->act_cdn));
               $dev_act = "";
               if ($req_typdev > 0)
                  $dev_act = GetDataField ($connect,"select actdev_dev_lb from activite_devoir where
                                            actdev_act_no = '".$DataAct->act_cdn."'","actdev_dev_lb");
               else
                  $dev_act = "Pas de devoir";
               $id_actdev = Donne_ID ($connect, "select max(actdev_cdn) from activite_devoir");
               $changer_dev = mysql_query ("insert into activite_devoir values($id_actdev,$id_new_act,\"$dev_act\")");
            }
    }
   $lien = "sequence.php?consult=1&parcours=$parcours&liste=$liste&id_parc=$id_parc&proprio=$proprio&refer=$refer&id_seq=$id_new_seq&id_ref=$id_ref&id_ref_parc=$id_ref_parc&miens=$miens&miens_parc=$miens_parc&parc=$id_parc&toutes_seq=$toutes_seq";
   $lien = urlencode($lien);
   echo "<script language=\"JavaScript\">";
   echo " document.location.replace(\"trace.php?link=$lien\")";
   echo "</script>";
   exit();
}
if ($creation == 1)
{
    echo "<FONT SIZE='3'>$message</FONT><center>";
    $lien="referenciel.php";
    $lien = urlencode($lien);
    echo "<DIV id='sequence'><A href=\"trace.php?link=$lien\" target='main'><font color='#000099'><b>$msq_choix_ref</b></FONT></A></DIV>";
}
if ($choix_ref == 1)
{



  ?>
  <SCRIPT language=JavaScript>
    function checkForm(frm) {
      var ErrMsg = "<?php echo $mess_info_no;?>\n\n";
      var lenInit = ErrMsg.length;
      if (isEmpty(frm.titre)==true)
        ErrMsg += ' - <?php echo $msq_titre;?>\n';
      if (isEmpty(frm.description)==true)
        ErrMsg += ' - <?php echo $msq_desc;?>\n';
      <?php
       if ($seqduref == 1)
       {
        ?>
         if (isEmpty(frm.dureeRef)== false)
         {
             if (isNaN(frm.dureeRef.value))
              ErrMsg += ' -  La durée référencée n\'est pas valide\n';
         }
        <?php
       }
     ?>
      if (isVide(frm.type)==true)
        ErrMsg += ' - <?php echo $mess_presc_ok;?>\n';
      if (isVide(frm.type)==true)
      if (isVide(frm.droit_voir_seq)==true)
        ErrMsg += ' - <?php echo $msq_droit_voir;?>\n';
      if (isVide(frm.ordre_act)==true)
        ErrMsg += ' - <?php echo addslashes($msq_act_ord);?>\n';
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
    function isVide(elm) {
               if(!elm[0].checked && !elm[1].checked){
                    return true;
               }
      return false;
    }
  </SCRIPT>
  <?php
   //insertion des pre-requis
    if ($insert_prereq == 1)
    {
       $id_prereq = Donne_ID ($connect,"select max(prereq_cdn) from prerequis");
       $prerequis = mysql_query ("insert into prerequis(prereq_cdn,prereq_seq_no,prereq_typcondition_lb,prereq_seqcondition_no) values ($id_prereq,$id_seq,\"$typ_prereq\",$seq)");
       $upd_act_prereq = mysql_query ("update prerequis set prereq_actcondition_no = $act where prereq_cdn = $id_prereq");
       $upd_note_prereq = mysql_query ("update prerequis set prereq_notemin_nb1 = $note_min,prereq_notemax_nb1 = $note_max where prereq_cdn = $id_prereq");
    }
    $titre = "$mess_bas_gest_seq : $mess_menu_gest_seq_ref";
    entete_concept("liste_parc.inc.php",$titre);
    echo aide_simple("sequence");
    echo "<TR><TD colspan='2'>";
    echo "<FORM NAME='form1' action=\"sequence.php?activite=1&insert_seq=1&parcours=$parcours&liste=$liste&id_parc=$id_parc&proprio=$proprio&refer=$refer&id_ref=$id_ref&id_ref_parc=$id_ref_parc&seq=$seq&typ_prereq=$typ_prereq&note_max=$note_max&note_min=$note_min&act=$act&miens=$miens&miens_parc=$miens_parc&parc=$id_parc&gauche=$gauche\"  method='POST' target='main'>";
    echo "<TABLE width=100% cellspacing='1' cellpadding='2' border=0>";    //laisser la possibilite d'associer d'autres referentiels
    $id_ref = 0;
    $referentiel = $msq_sra; //$msq_slchr;
    echo "<TR><TD colspan='2'><Font size='2'><B>$msq_act_ref</B> $referentiel</TD></TR>";
    echo "<TR><TD nowrap width=35%><B>$msq_titre</B></TD><TD nowrap><INPUT TYPE='text'  class='INPUT' name='titre' size ='75' align='middle' ></TD></TR>";
    echo "<TR><TD nowrap width=35%><B>$msq_desc</B></TD>";
    echo "<TD nowrap width=35%><TEXTAREA name='description' class='TEXTAREA' align='middle' rows='6' cols='80'>$description</TEXTAREA><br></TD></TR>";
    echo "<TR><TD nowrap>";
    echo "<input type='hidden' name='cles_seq' value=\"$cles_seq\">";
    echo "<TABLE cellpadding='0' cellspacing='0' border='0'>";
    echo "<TR height='30'><TD><B>$mess_presc_ok</B></TD></TR>";
    echo "<TR height='30'><TD nowrap><B>$msq_droit_voir</B></TD></TR></TABLE></TD>";
    echo "<TD nowrap height=100%><TABLE cellpadding='0' cellspacing='0' border='0' height=100%><TR><TD nowrap>";
    echo "$mess_oui&nbsp;&nbsp;<INPUT type='radio' name='type' value='0'>&nbsp;&nbsp;&nbsp;&nbsp;</TD><TD>";
    echo "$mess_non&nbsp;&nbsp;<INPUT type='radio' name='type' value='1'>&nbsp;</TD><TD rowspan='2' valign='center'>";
    echo anoter($mess_dupli_presc,"350");
    echo "</TD></TR><TR><TD nowrap>";
    echo "$mess_oui&nbsp;&nbsp;<INPUT type='radio' name='droit_voir_seq' value='1'>&nbsp;</TD><TD>";
    echo "$mess_non&nbsp;&nbsp;<INPUT type='radio' name='droit_voir_seq' value='0'>";
    echo "</TD></TR></TABLE></TD></TR>";
    echo "<TR><TD nowrap width=35%><B>$msq_act_ord</B></TD><TD nowrap>";
    echo "$mess_oui&nbsp;&nbsp;<INPUT type='radio' name='ordre_act' value='OUI'>&nbsp;&nbsp;&nbsp;&nbsp;";
    echo "$mess_non&nbsp;&nbsp;<INPUT type='radio' name='ordre_act' value='NON'>";
    echo "</TD></TR>";
    if ($seqduref == 1)
    {
       echo "<TR height='25'><TD nowrap><B>Durée du référentiel en minutes</TD>";
       echo "<TD nowrap><INPUT type='text' name='dureeRef' size='2' value=''></TD></TR>";
    }
    echo "<TR height='50'><TD>&nbsp;</TD><TD align='left'>".
         "<A HREF=\"javascript:checkForm(document.form1);\" onClick=\"TinyMCE.prototype.triggerSave();\" ".
         "onmouseover=\"img1.src='images/fiche_identite/boutvalidb$suffixer.gif';return true;\" ".
         "onmouseout=\"img1.src='images/fiche_identite/boutvalid$suffixer.gif'\">".
         "<IMG NAME=\"img1\" SRC=\"images/fiche_identite/boutvalid$suffixer.gif\" BORDER='0' ".
         "onLoad=\"tempImg=new Image(0,0); tempImg.src='images/fiche_identite/boutvalidb$suffixer.gif'\"></A>";
    echo "</TD></TR></TABLE></FORM></TD></TR></TABLE>";
} //fin if ($choix_ref == 1)
/*
if (isset($consult_activ) && $consult_activ == 1){
   include ('include/consult_act.inc.php');
  exit;
}
*/
//Creation activite et insertion sequence

if ($activite == 1)
{
   //Verifier que les champs obligatoires sont bien remplis
   if ($titre == "" && $insert_seq == 1)
   {
      $letitre= "$msq_oubli_tit_seq";
      entete_concept("liste_parc.inc.php",$letitre);
      echo aide_simple("sequence");
      echo "<TR><TD><Font size='2' class='sous_titre'>$sous_titre</TD></TR>";
      $lien="sequence.php?choix_ref=1&id_ref=$id_ref&id_ref_parc=$id_ref_parc&parcours=$parcours&liste=$liste&id_parc=$id_parc&proprio=$proprio&refer=$refer&miens=$miens&miens_parc=$miens_parc&parc=$id_parc&liste_act_seq=1";
      $lien = urlencode($lien);
      echo "<TR><TD><DIV id='sequence'><a href=\"trace.php?link=$lien\" target='main'>$msq_ret_crea_seq</a></DIV></TD></TR>";
      echo boutret(1,0);
      fin_tableau($html);
      exit;
   }
   else
   {
      if ($insert_seq == 1)
      {
        if ($type == 1)
        {
           $seq_type = 1;
        }
        else
           $seq_type = 0;
        $id_seq = Donne_ID ($connect, "select max(seq_cdn) from sequence");
        $insert_seq_query = mysql_query ("insert into sequence values ($id_seq,\"".str_replace("\"","'",$titre)."\",\"".htmlentities($description,ENT_QUOTES,'iso-8859-1')."\",\"".htmlentities($cles_seq,ENT_QUOTES,'iso-8859-1')."\",\"$ordre_act\",0,'$id_user',\"$date_dujour\",\"$date_dujour\",'$droit_voir_seq','$seq_type','NORMAL')");
        if ($seqduref == 1 && (isset($_POST['dureeRef']) && $_POST['dureeRef'] > 0) )
           $insertSeqReq = mysql_query ("insert into seq_duree_ref values (NULL,$id_seq,'".$_POST['dureeRef']."')");
        $insert_rss = rss :: ajout('sequence',$id_user,$id_seq);
        $seq_ref_query = mysql_query ("select seqref_referentiel_no from sequence_referentiel where seqref_seq_no = $id_seq");
        $nb_seq_ref = mysql_num_rows ($seq_ref_query);
        $id_seqref = Donne_ID ($connect,"select max(seqref_cdn) from sequence_referentiel");
        if ($nb_seq_ref == 0)
        {
           if ($gauche == 1)
             $insert_refseq_query = mysql_query ("insert into sequence_referentiel values ($id_seqref,$id_seq,0)");
           else
             $insert_refseq_query = mysql_query ("insert into sequence_referentiel values ($id_seqref,$id_seq,$id_ref)");
        }
        else
             $insert_refseq_query = mysql_query ("insert into sequence_referentiel values ($id_seqref,$id_seq,$id_ref)");
        $titre = "";
        $droit_voir="";
      }// fin if ($insert_seq == 1)

      if ($insert_seq == 1 && $parcours == 1)
      {
             $id_seqparc = Donne_ID ($connect,"select max(seqparc_cdn) from sequence_parcours");
             $id_ordre = Donne_ID ($connect,"select max(seqparc_ordre_no) from sequence_parcours where seqparc_parc_no=$id_parc");
             $ins_seqparc = mysql_query ("insert into sequence_parcours values ($id_seqparc,$id_seq,$id_parc,$id_ordre)");
      }
      if ($gauche == 1)
      {
         $lien = "parcours.php?liste=$liste&consult=1&id_seq=$id_seq&parcours=$parcours&id_parc=$id_parc&proprio=$proprio&refer=$refer&id_ref=$id_ref&id_ref_parc=$id_ref_parc&miens=$miens&miens_parc=$miens_parc&refer=$refer&parc=$id_parc&gauche=1&liste_act_seq=1&liste_seq=1";
         $lien = urlencode($lien);
         echo "<script language=\"JavaScript\">";
           echo "document.location.replace(\"trace.php?link=$lien\")";
         echo "</script>";
       exit();
      }
      if ($insert_act == 1)
      {
         //Verifier que champs obligatoires sont remplis
         if ($titre == '' || $consigne == '')
         {
            $letitre= $msq_creat_act;
            entete_concept("liste_parc.inc.php",$letitre);
            echo aide_simple("sequence");
            echo "<TR><TD><CENTER><Font size='2'>$msq_oubli_champ_oblig</FONT></CENTER></TD></TR>";
            echo boutret(1,0);
            fin_tableau($html);
          exit;
         }
         else
         {
            $id_act = Donne_ID ($connect, "select max(act_cdn) from activite");
            $duree = ($horaire*60) + $minutage;
            $num_act = Donne_ID ($connect,"select max(act_ordre_nb) from activite where act_seq_no = $id_seq");
            $duree_seq = GetDataField ($connect,"select seq_duree_nb from sequence where seq_cdn=$id_seq","seq_duree_nb");
            $duree_seq+=$duree;
            $upd_seq = mysql_query ("UPDATE sequence set seq_duree_nb = $duree_seq where seq_cdn = $id_seq");
            $modifie_rss = rss :: modifie('sequence',$id_user,$id_seq);
            if ($id_ress > 0 && $ress_norok == "NON")
            {
              $ress_norok == "OUI";
              $flag = 1;
            }
            if ($id_ress == 0 && $ress_norok == 'NON')
            {
      //dey Dfoad   insertion
              if (!empty($_FILES['userfile']['tmp_name']))
                 include("saveMedia.php");
              // Cas de nouvelle activité ou de modification d'activité sans pendant devoir
                 $id_actdev = Donne_ID ($connect, "select max(actdev_cdn) from activite_devoir");
                 $changer_dev = mysql_query ("insert into activite_devoir values($id_actdev,$id_act,\"$typdev\")");

               $flag = 1;
               $insert_act_query = mysql_query ("insert into activite values ($id_act,$id_seq,$num_act,\"".str_replace("\"","'",$titre)."\",\"".htmlentities($consigne,ENT_QUOTES,'iso-8859-1')."\",\"".htmlentities($commentaire,ENT_QUOTES,'iso-8859-1')."\",'$ress_norok',0,\"$duree\",\"$pass_mult\",\"$acquit\",\"$notation\",\"$dev_a_rendre\",$id_user,\"$date_dujour\",\"$date_dujour\",$droit_voir_act,$flag)");
               $insert_rss = rss :: ajout('activite',$id_user,$id_act);
               $requete_grp = mysql_query ("select * from groupe order by grp_nom_lb");
               $nb_grp_parc = mysql_num_rows($requete_grp);
               if ($nb_grp_parc > 0)
               {
                       $gp=0;
                       while ($gp < $nb_grp_parc)
                       {
                              $id_grp = mysql_result($requete_grp,$gp,"grp_cdn");
                              $act_suivi = mysql_query ("select * from suivi2_$id_grp where suiv2_seq_no = $id_seq");
                              $nb_ut = mysql_num_rows ($act_suivi);
                              if ($nb_ut >0)
                              {
                                  $kk = 0;
                                  while ($kk != $nb_ut)
                                  {
                                         $is = mysql_result ($act_suivi,$kk,"suiv2_cdn");
                                         $ut = mysql_result ($act_suivi,$kk,"suiv2_utilisateur_no");
                                         $grpe =$id_grp;
                                         $verif_seq = mysql_result($act_suivi,$kk,"suiv2_etat_lb");
                                         if ($verif_seq != "TERMINE")
                                         {
                                             $id_suivi = Donne_ID ($connect,"select max(suivi_cdn) from suivi1_$id_grp");
                                             $ins_suivi = mysql_query ("insert into suivi1_$id_grp(suivi_cdn,suivi_utilisateur_no,suivi_act_no,suivi_etat_lb,suivi_grp_no) values ($id_suivi,$ut,$id_act,\"PRESENTIEL\",$grpe)");
                                         }
                                      $kk++;
                                  } //fin while ($kk != $nb_ut)
                              }
                         $gp++;
                       }
               }
             }
             else
             {
              if ($id_ress > 0 && $ress_norok == 'OUI')
                 $actype = GetDataField ($connect,"select ress_type from ressource_new where ress_cdn = $id_ress","ress_type");
              $titre = stripslashes($titre);
              $consigne=stripslashes($consigne);
              // teste la pertinence des composants de l'activité et renvoi au besoin au formulaire en concervant les données
              $lien="sequence.php?renvoi=1&liste=$liste&id_act=$id_act&id_seq=$id_seq&choix_ress=1&parcours=$parcours&id_parc=$id_parc&proprio=$proprio&refer=$refer&activite=1&id_ref=$id_ref&id_ref_parc=$id_ref_parc&id_ress=$id_ress&miens=$miens&miens_parc=$miens_parc";
              $lien=urlencode($lien);
              $renvoi =  "<DIV id='sequence'><A href=\"trace.php?link=$lien\" target='main'>$mess_form_retour</A></DIV>";
              if ($acquit != "RESSOURCE" && ($pass_mult == "NON" || $pass_mult == "OUI") && $notation== "OUI" && $dev_a_rendre == "NON" && ($id_ress > 0 || $ress_norok == 'OUI'))
                 $acquit = "FORMATEUR_REFERENT";
/*
              if ($acquit == "RESSOURCE" && ($id_ress > 0 || $ress_norok == 'OUI')){
                $pass_mult = "NON";
                $dev_a_rendre = "NON";
                $notation = "OUI";
              }
*/
              if ($acquit == "RESSOURCE" && $ress_norok == 'NON')
              {
                $pass_mult = "NON";
                $acquit = "FORMATEUR_REFERENT";
                $dev_a_rendre = "NON";
                $notation = "OUI";
              }
              if ($notation == "OUI" && $dev_a_rendre == "OUI" && $acquit != "FORMATEUR_REFERENT" && $id_ress > 0 && $ress_norok == 'OUI')
                $acquit = "FORMATEUR_REFERENT";
              if ($ress_norok == "NON")
                $flag = 1;
              elseif($ress_norok == "OUI")
                $flag = 0;
              $id_ress = 0;
              $duree = ($horaire*60) + $minutage;
              $insert_act_query = mysql_query ("insert into activite values ($id_act,$id_seq,$num_act,\"".
                                               str_replace("\"","'",$titre)."\",\"".htmlentities($consigne,ENT_QUOTES,'iso-8859-1').
                                               "\",\"".htmlentities($commentaire,ENT_QUOTES,'iso-8859-1').
                                               "\",'$ress_norok',$id_ress,\"$duree\",\"$pass_mult\",\"$acquit\",\"$notation\",".
                                               "\"$dev_a_rendre\",$id_user,\"$date_dujour\",\"$date_dujour\",$droit_voir_act,$flag)");
              $insert_rss = rss :: ajout('activite',$id_user,$id_act);
      //dey Dfoad   insertion
              if (!empty($_POST['userfile']))
                 include("saveMedia.php");
              // Cas de nouvelle activité ou de modification d'activité sans pendant devoir
                 $id_actdev = Donne_ID ($connect, "select max(actdev_cdn) from activite_devoir");
                 $changer_dev = mysql_query ("insert into activite_devoir values($id_actdev,$id_act,\"$typdev\")");

              $requete_grp = mysql_query ("select * from groupe order by grp_nom_lb");
              $nb_grp_parc = mysql_num_rows($requete_grp);
              if ($nb_grp_parc > 0)
              {
                       $gp=0;
                       while ($gp < $nb_grp_parc)
                       {
                              $id_grp = mysql_result($requete_grp,$gp,"grp_cdn");
                              $nom_grp = mysql_result($requete_grp,$gp,"grp_nom_lb");
                              $act_suivi = mysql_query ("select * from suivi2_$id_grp where suiv2_seq_no = $id_seq");
                              $nb_ut = mysql_num_rows ($act_suivi);
                              if ($nb_ut >0)
                              {
                                  $kk = 0;
                                  while ($kk != $nb_ut)
                                  {
                                         $is = mysql_result ($act_suivi,$kk,"suiv2_cdn");
                                         $ut = mysql_result ($act_suivi,$kk,"suiv2_utilisateur_no");
                                         $grpe =$id_grp;
                                         $verif_seq = mysql_result($act_suivi,$kk,"suiv2_etat_lb");
                                         if ($verif_seq != "TERMINE")
                                         {
                                             $id_suivi = Donne_ID ($connect,"select max(suivi_cdn) from suivi1_$id_grp");
                                             $ins_suivi = mysql_query ("insert into suivi1_$id_grp(suivi_cdn,suivi_utilisateur_no,suivi_act_no,suivi_etat_lb,suivi_grp_no) values ($id_suivi,$ut,$id_act,\"A FAIRE\",$grpe)");
                                         }
                                      $kk++;
                                  } //fin while ($kk != $nb_ut)
                              }
                         $gp++;
                       }
              }
            }// if else !=id_ress
       } //fin else ($titre==''....)
       if ($id_ress == 0 && $ress_norok == "OUI")
       {
           $letitre= $mess_ass_act;
           entete_concept("liste_parc.inc.php",$letitre);
           echo aide_simple("sequence");
           echo "<TR><TD><TABLE border=0 cellpadding='4' cellspacing = '4'>";
          // ressource sujet forum
           $chaine_act="&parcours=$parcours&liste=$liste&id_parc=$id_parc&id_act=$id_act&id_seq=$id_seq&proprio=$proprio&refer=$refer&id_ref=$id_ref&id_ref_parc=$id_ref_parc&miens=$miens&miens_parc=$miens_parc&parc=$id_parc&gauche=$gauche&liste_act_seq=1";
           $params = str_replace("&","|",$chaine_act);
           $forum_act  = $id_act;
           $_SESSION['forum_act'] = $forum_act;
           $_SESSION['chaine_act'] = $chaine_act;
           $lien="sequence.php?id_seq=$id_seq&consult_act=1&encreation=1&parcours=$parcours&liste=$liste&id_parc=$id_parc&proprio=$proprio&refer=$refer&id_ref=$id_ref&id_ref_parc=$id_ref_parc&miens=$miens&miens_parc=$miens_parc&parc=$id_parc&gauche=1&liste_act_seq=1&liste_seq=$liste_seq";
           $lien = urlencode($lien);
           echo "<TR height='40'><TD align='left' nowrap>$bouton_gauche<A href=\"trace.php?link=$lien\" target='main'>$mess_plustard</A>$bouton_droite</TD></TR>";
           echo "<TR height='40'><TD valign='top'><Font size='2'><B>$mess_assress</B></TD></TR>";//$msq_ass_ress_form$mess_assres_comp
           $lien="recherche.php?acces=vient_de_seq&id_act=$id_act&parcours=$parcours&liste=$liste&id_parc=$id_parc&proprio=$proprio&refer=$refer&id_ref=$id_ref&id_ref_parc=$id_ref_parc&modif_act=1&id_seq=$id_seq&miens=$miens&miens_parc=$miens_parc&parc=$id_parc&liste_act_seq=1&liste_seq=$liste_seq";
           $lien = urlencode($lien);
           echo "<TR><TD align='left' nowrap valign='top'>$bouton_gauche<A href=\"trace.php?link=$lien\" target='main'>$mess_ressidx</A>$bouton_droite</TD><TD valign='top'>$mess_goidx</TD></TR>";
           //echo "<TR height='40'><TD colspan='2'><Font size='2'><B>$mess_ajt_resdirect</B></TD></TR>";
           //echo "<TR height='40'><TD colspan='2'><Font size='2'>$mess_ajt_complement</TD></TR>";
           $lien="charge_ressource_vrac.php?id_act=$id_act&dou=&params=$params&charger_fichier=1";
           $lien = urlencode($lien);
           echo "<TR height='40'><TD align='left' nowrap valign='top'>$bouton_gauche";
           echo "<A HREF=\"#\" title =\"$mess_ajt_ress_act\" onClick=\"window.open('trace.php?link=$lien','','scrollbars=yes,resizable=yes,width=500,height=300,left=250,top=300')\">".
                "$mess_ajt_ress_act</A>$bouton_droite</TD><TD>$mess_ajt_complement</TD></TR>";
           $lien="charge_ressource_vrac.php?id_act=$id_act&dou=&params=$params&charger_url=1";
           $lien = urlencode($lien);
           echo "<TR height='40'><TD align='left' nowrap colspan='2'>$bouton_gauche";
           echo "<A HREF=\"#\" title =\"$mess_ajt_url_act\" onClick=\"window.open('trace.php?link=$lien','','scrollbars=yes,resizable=yes,width=500,height=300,left=250,top=300')\">$mess_ajt_url_act</A>$bouton_droite</TD></TR>";
           //quizz à créer
           $lien="creation_qcm.php?creation_qcm=1&params_qcm=$params&id_activit=$id_act&venu=act&acced=sequence";
           $lien = urlencode($lien);
           /*
           echo "<TR height='30'><TD colspan='2' valign='top'><Font size='2'>&nbsp;</TD></TR>";
           echo "<TR><TD align='left' nowrap valign='top'>$bouton_gauche<A href=\"javascript:void(0);\"".
                " onClick=\"window.open('trace.php?link=$lien','','scrollbars=no,resizable=no')\">".
                "$mess_ptiqcm</A>$bouton_droite</TD><TD valign='top'>$mess_alrt_qcm</TD></TR>";
           */
           // lien vers in fil de discussion d'un forum
           $lien="forum/index.php?f=0&collapse=1&arrive=sequence";
           $lien = urlencode($lien);
           echo "<TR><TD align='left' nowrap valign='top'>$bouton_gauche<A href=\"trace.php?link=$lien\" target='main'>".
                "$mess_fld</A>$bouton_droite</TD><TD valign='top'>$mess_fldplus</TD></TR>";//$msq_ass_ress_forum
           $lien="sequence.php?liste=$liste&modif_act=1&id_act=$id_act&id_seq=$id_seq&choix_ress=1&parcours=$parcours&id_parc=$id_parc&proprio=$proprio&refer=$refer&action_act=1&id_ref=$id_ref&id_ref_parc=$id_ref_parc&id_ress=$id_ress&miens=$miens&miens_parc=$miens_parc&parc=$id_parc";
           $lien = urlencode($lien);
           echo "<TR height='15'><TD colspan='2'>&nbsp;</TD></TR><TR><TD align='left' nowrap valign='top'>".
                "$bouton_gauche<A href=\"trace.php?link=$lien\" target='main'>$mess_ret</A>$bouton_droite</TD></TR>";//retour
           echo "<TR height='15'><TD colspan='2'>&nbsp;</TD></TR></TABLE>";
           $html = "";
           echo fin_tableau($html);
         exit();
       }
       $lien="sequence.php?id_seq=$id_seq&consult_act=1&encreation=1&parcours=$parcours&liste=$liste&id_parc=$id_parc&proprio=$proprio&refer=$refer&id_parc=$id_parc&proprio=$proprio&refer=$refer&parcours=$parcours&id_ref=$id_ref&id_ref_parc=$id_ref_parc&miens=$miens&miens_parc=$miens_parc&vient_de_search=$vient_de_search&parc=$id_parc&liste_act_seq=1";
       $lien = urlencode($lien);
       echo "<script language=\"JavaScript\">";
       echo " document.location.replace(\"trace.php?link=$lien\")";
       echo "</script>";
       exit();
     } //fin if ($insert_act == 1)


     if ($ins_ch_act == 1 && $id_act > -1)
     {
         //On associe une activite existante a la sequence
         //Selection des infos necessaires
         $activite_query =  mysql_query ("select * from activite where act_cdn = $id_act");
         $titre = mysql_result ($activite_query,0,"act_nom_lb");
         $lasequence = mysql_result ($activite_query,0,"act_seq_no");
         $consigne = mysql_result ($activite_query,0,"act_consigne_cmt");
         $commentaire = mysql_result ($activite_query,0,"act_commentaire_cmt");
         $id_ress = mysql_result ($activite_query,0,"act_ress_no");
         $ress_norok = mysql_result ($activite_query,0,"act_ress_on");
         $duree = mysql_result ($activite_query,0,"act_duree_nb");
         $pass_mult = mysql_result ($activite_query,0,"act_passagemult_on");
         $acquit = mysql_result ($activite_query,0,"act_acquittement_lb");
         $notation = mysql_result ($activite_query,0,"act_notation_on");
         $dev_a_rendre = mysql_result ($activite_query,0,"act_devoirarendre_on");
         $auteur = mysql_result ($activite_query,0,"act_auteur_no");
         $droit_voir_act = mysql_result ($activite_query,0,"act_publique_on");
         $flag = mysql_result ($activite_query,0,"act_flag_on");
         $id_nouv = Donne_ID ($connect,"select max(act_cdn) from activite");
         $new_ordre =Donne_ID ($connect,"select max(act_ordre_nb) from activite where act_seq_no = $id_seq order by act_ordre_nb");
         if ($id_ress > 0 && $ress_norok == "NON")
           $ress_norok == "OUI";
         if ($id_ress == 0 && $ress_norok == "NON")
           $flag = 1;
         elseif($id_ress == 0 && $ress_norok == "OUI")
           $flag = 0;
         if ($lasequence == 0 && ($auteur == $id_user || $typ_user='ADMINISTRATEUR'))
         {
                $requete_grp = mysql_query ("select * from groupe");
                $nb_grp_parc = mysql_num_rows($requete_grp);
                $nb_fois_ajt=0;
                if ($nb_grp_parc > 0)
                {
                       $gp=0;
                       while ($gp < $nb_grp_parc)
                       {
                              $id_grp = mysql_result($requete_grp,$gp,"grp_cdn");
                              $nb_fois_ajt += mysql_num_rows(mysql_query("SELECT * FROM suivi1_$id_grp WHERE suivi_act_no = $id_act"));
                              $gp++;
                       }
                }
                if ($nb_fois_ajt == 0)
                {
                   $insert_act_exist = mysql_query("UPDATE activite SET act_seq_no='$id_seq',act_ordre_nb='$new_ordre' WHERE act_cdn = '$id_act'");
                   $modifie_rss = rss :: modifie('activite',$id_user,$id_act);
                }
                $id_nouv = $id_act;
         }
         else
         {
            $id_nouv = Donne_ID ($connect,"select max(act_cdn) from activite");
            $insert_act_exist = mysql_query ("INSERT INTO activite values ($id_nouv,$id_seq,$new_ordre,\"".str_replace("\"","'",$titre).
                                             "\",\"".htmlentities($consigne,ENT_QUOTES,'iso-8859-1')."\",\"".
                                             htmlentities($commentaire,ENT_QUOTES,'iso-8859-1')."\",'$ress_norok',$id_ress,".
                                             "\"$duree\",\"$pass_mult\",\"$acquit\",\"$notation\",\"$dev_a_rendre\",'$id_user',".
                                             "\"$date_dujour\",\"$date_dujour\",'$droit_voir_act','$flag')");
            $modifie_rss = rss :: ajout('activite',$id_user,$id_nouv);
            //dey Dfoad
               $req_media = mysql_query ("select * from activite_media where actmedia_act_no = $id_act");
               $nb_media = mysql_num_rows($req_media);
               if ($nb_media > 0)
                  $leMedia = mysql_result($req_media,0,"actmedia_ress_no");
               $req_devoir = mysql_query ("select * from activite_devoir where actdev_act_no = $id_act");
               $nb_devoir = mysql_num_rows($req_devoir);
               if ($nb_media > 0)
                  $leDevoir = mysql_result($req_devoir,0,"actdev_dev_lb");
               $insert_media = mysql_query ("INSERT INTO activite_media values ('',$id_nouv,$leMedia)");
               $insert_devoir = mysql_query ("INSERT INTO activite_devoir values ('',$id_nouv,\"$leDevoir\")");

         }
         $requete_grp = mysql_query ("select * from groupe order by grp_nom_lb");
         $nb_grp_parc = mysql_num_rows($requete_grp);
         if ($nb_grp_parc > 0)
         {
                       $gp=0;
                       while ($gp < $nb_grp_parc)
                       {
                              $id_grp = mysql_result($requete_grp,$gp,"grp_cdn");
                              $nom_grp = mysql_result($requete_grp,$gp,"grp_nom_lb");
                              $act_suivi = mysql_query ("select * from suivi2_$id_grp where suiv2_seq_no = $id_seq");
                              $nb_ut = mysql_num_rows ($act_suivi);
                              if ($nb_ut >0)
                              {
                                  $kk = 0;
                                  while ($kk != $nb_ut)
                                  {
                                         $is = mysql_result ($act_suivi,$kk,"suiv2_cdn");
                                         $ut = mysql_result ($act_suivi,$kk,"suiv2_utilisateur_no");
                                         $grpe =$id_grp;
                                         $verif_seq = mysql_result($act_suivi,$kk,"suiv2_etat_lb");
                                         if ($verif_seq != "TERMINE")
                                         {
                                             if ($id_ress == 0 && $ress_norok == "NON")
                                                 $etat_new = "PRESENTIEL";
                                             else
                                                 $etat_new = "A FAIRE";
                                             $id_suivi = Donne_ID ($connect,"select max(suivi_cdn) from suivi1_$id_grp");
                                             $ins_suivi = mysql_query ("insert into suivi1_$id_grp(suivi_cdn,suivi_utilisateur_no,suivi_act_no,suivi_etat_lb,suivi_grp_no) values ($id_suivi,$ut,$id_nouv,\"$etat_new\",$grpe)");
                                         }
                                      $kk++;
                                  } //fin while ($kk != $nb_ut)
                              }
                         $gp++;
                       }
         }
         if ($id_ress == 0 && $ress_norok == "OUI" && $ins_ch_act != 1)
         {
           $letitre= $mess_ass_act;
           entete_concept("liste_parc.inc.php",$letitre);
           echo aide_simple("sequence");
           echo "<TR><TD><TABLE border=0 cellpadding='4' cellspacing = '4'>";
           // ressource sujet forum
           $chaine_act="&parcours=$parcours&liste=$liste&id_parc=$id_parc&id_act=$id_act&proprio=$proprio&refer=$refer&id_ref=$id_ref&id_ref_parc=$id_ref_parc&id_seq=$id_seq";
           $forum_act  = $id_act;
           $_SESSION['forum_act'] = $forum_act;
           $_SESSION['chaine_act'] = $chaine_act;
           $lien="sequence.php?id_seq=$id_seq&consult_act=1&parcours=$parcours&liste=$liste&id_parc=$id_parc&proprio=$proprio&refer=$refer&id_ref=$id_ref&id_ref_parc=$id_ref_parc&miens=$miens&miens_parc=$miens_parc&parc=$id_parc&liste_act_seq=1";
           $lien = urlencode($lien);
           echo "<TR height='40'><TD align='left' nowrap>$bouton_gauche<A href=\"trace.php?link=$lien\" target='main'>$mess_plustard</A>$bouton_droite</TD></TR>";
           echo "<TR height='40'><TD valign='top'><Font size='2'><B>$mess_assress</B></TD></TR>";//$msq_ass_ress_form$mess_assres_comp
           $lien="recherche.php?acces=vient_de_seq&id_act=$id_act&parcours=$parcours&liste=$liste&id_parc=$id_parc&proprio=$proprio&refer=$refer&id_ref=$id_ref&id_ref_parc=$id_ref_parc&modif_act=1&id_seq=$id_seq&miens=$miens&miens_parc=$miens_parc&parc=$id_parc&liste_act_seq=1";
           $lien = urlencode($lien);
           echo "<TR><TD align='left' nowrap valign='top'>$bouton_gauche<A href=\"trace.php?link=$lien\" target='main'>$mess_ressidx</A>$bouton_droite</TD><TD valign='top'>$mess_goidx</TD></TR>";
           $lien="charge_ressource_vrac.php?id_act=$id_act&dou=&params=$params&charger_fichier=1";
           $lien = urlencode($lien);
           echo "<TR height='40'><TD align='left' nowrap valign='top'>$bouton_gauche";
           echo "<A HREF=\"#\" title =\"$mess_ajt_ress_act\" onClick=\"window.open('trace.php?link=$lien','','scrollbars=yes,resizable=yes,width=500,height=300,left=250,top=300')\">".
                "$mess_ajt_ress_act</A>$bouton_droite</TD><TD>$mess_ajt_complement</TD></TR>";
           $lien="charge_ressource_vrac.php?id_act=$id_act&dou=&params=$params&charger_url=1";
           $lien = urlencode($lien);
           echo "<TR height='40'><TD align='left' nowrap valign='top'>$bouton_gauche";
           echo "<A HREF=\"javascript:void(0);\" title =\"$mess_ajt_url_act\" onClick=\"window.open('trace.php?link=$lien','','scrollbars=yes,resizable=yes,width=500,height=300,left=250,top=300')\">$mess_ajt_url_act</A>$bouton_droite</TD><TD></TD></TR>";
           //quizz à créer
           $lien="creation_qcm.php?creation_qcm=1&id_activit=$id_act&venu=act&acced=sequence&params_qcm=$params";
           $lien = urlencode($lien);
           /*
           echo "<TR height='30'><TD colspan='2' valign='top'><Font size='2'>&nbsp;</TD></TR>";
           echo "<TR><TD align='left' nowrap valign='top'>$bouton_gauche<A href=\"javascript:void(0);\"".
                " onClick=\"window.open('trace.php?link=$lien','','scrollbars=no,resizable=no')\">".
                "$mess_ptiqcm</A>$bouton_droite</TD><TD valign='top'>$mess_alrt_qcm</TD></TR>";
           */
           // lien vers in fil de discussion d'un forum
           $lien="forum/index.php?f=0&collapse=1&arrive=sequence";
           $lien = urlencode($lien);
           echo "<TR><TD align='left' nowrap valign='top'>$bouton_gauche<A href=\"trace.php?link=$lien\" target='main'>$mess_fld</A>$bouton_droite</TD><TD valign='top'>$mess_fldplus</TD></TR>";//$msq_ass_ress_forum
           $lien="sequence.php?liste=$liste&modif_act=1&id_act=$id_act&id_seq=$id_seq&choix_ress=1&parcours=$parcours&id_parc=$id_parc&proprio=$proprio&refer=$refer&action_act=1&id_ref=$id_ref&id_ref_parc=$id_ref_parc&id_ress=$id_ress&miens=$miens&miens_parc=$miens_parc&parc=$id_parc";
           $lien = urlencode($lien);
           echo "<TR height='15'><TD colspan='2'>&nbsp;</TD></TR><TR><TD align='left' nowrap valign='top'>".
                "$bouton_gauche<A href=\"trace.php?link=$lien\" target='main'>$mess_ret</A>$bouton_droite</TD></TR>";//retour
           $html = "";
           echo fin_tableau($html);
         exit();
         }
         $lien="sequence.php?id_seq=$id_seq&consult_act=1&parcours=$parcours&liste=$liste&id_parc=$id_parc&proprio=$proprio&refer=$refer&id_ref=$id_ref&id_ref_parc=$id_ref_parc&miens=$miens&miens_parc=$miens_parc&vient_de_search=$vient_de_search&parc=$id_parc&liste_act_seq=1";
         $lien = urlencode($lien);
         echo "<script language=\"JavaScript\">";
         echo " document.location.replace(\"trace.php?link=$lien\")";
         echo "</script>";
       exit();
      }
      elseif ($ins_ch_act == 1 && $id_act == -1)
      {
         $message = $devoir_choix;
         $lien = "sequence.php?liste=$liste&id_seq=$id_seq&consult_act=1&parcours=$parcours&id_parc=$id_parc&proprio=$proprio&refer=$refer&choix_act=1&id_ref=$id_ref&id_ref_parc=$id_ref_parc&message=$message&miens=$miens&miens_parc=$miens_parc&vient_de_search=$vient_de_search&parc=$id_parc&liste_act_seq=1";
         $lien = urlencode($lien);
         echo "<script language=\"JavaScript\">";
         echo " document.location.replace(\"trace.php?link=$lien\")";
         echo "</script>";
       exit();
      }
/*
*/
      ?>
      <SCRIPT language=JavaScript>
      function checkForm(frm) {
      var ErrMsg = "<?php echo $mess_info_no;?>\n\n";
      var lenInit = ErrMsg.length;
      if (isEmpty(frm.titre)==true)
        ErrMsg += ' - <?php echo $msq_tit_form;?>\n';
      if (isEmpty(frm.consigne)==true)
        ErrMsg += ' - <?php echo $msq_consigne_act_form;?>\n';
      //dey dfoad
      if (frm.userfile.value != "" && strstr(frm.userfile.value,'.swf')!= true && strstr(frm.userfile.value,'.mp3')!= true && strstr(frm.userfile.value,'.flv') != true )
      {
         ErrMsg += ' - seuls les fichiers SWF, FLV ou MP3 sont autorisés pour accompagner la consigne\n';
         $("#loader").css("background","url('OutilsJs/images/loading.gif') no-repeat");
      }
      if (isVide(frm.ress_norok)==true)
        ErrMsg += ' - <?php echo $msq_ass_ress_form;?>\n';
      if (isVide(frm.droit_voir_act)==true)
        ErrMsg += ' - <?php echo $msq_droit_voir;?>\n';
      if (isEmpty(frm.horaire)==true && isEmpty(frm.minutage)==true)
        ErrMsg += ' - <?php echo $msq_horaire_act_form;?>\n';
      if (isVide(frm.pass_mult)==true)
        ErrMsg += ' - <?php echo $msq_pass_act_form;?>\n';
      if (isVide1(frm.acquit)==true)
        ErrMsg += ' - <?php echo $msq_acquit_form;?>\n';
      if (isVide(frm.dev_a_rendre)==true)
        ErrMsg += ' - <?php echo addslashes($msq_dev_act_form);?>\n';
      if (isVide(frm.notation)==true)
        ErrMsg += ' - <?php echo addslashes($msq_not_act_form);?>\n';
      if (document.getElementsByName("notation")[0].checked == true)
          var note = document.getElementsByName("notation")[0];
      else
          var note = "";
      if (document.getElementsByName("acquit")[0].checked == true)
          var acq = document.getElementsByName("acquit")[0];
      else
          var acq = "";
      if (document.getElementsByName("acquit")[2].checked == true)
          var acq_ress = document.getElementsByName("acquit")[2];
      else
          var acq_ress = "";
      if (document.getElementsByName("dev_a_rendre")[0].checked == true)
          var devoir = document.getElementsByName("dev_a_rendre")[0];
      else
          var devoir = "";
      if(note.value == "OUI" && acq.value == "APPRENANT")
          ErrMsg += ' - <?php echo $mess_autoeval?>\n';
      if(devoir.value == "OUI" && acq_ress.value == "RESSOURCE")
          ErrMsg += ' - <?php echo $mess_ress_dev?>\n';
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
    function isVide(elm) {
       if(!elm[0].checked && !elm[1].checked){
         return true;
       }
      return false;
    }
    function isVide1(elm) {
       if(!elm[0].checked && !elm[1].checked && !elm[2].checked){
         return true;
       }
      return false;
    }
    </SCRIPT>
    <?php
    $titre_seq = GetDataField ($connect,"select seq_titre_lb from sequence where seq_cdn = $id_seq","seq_titre_lb");
    $letitre= $msq_ajout_act_seq;
    entete_concept("liste_parc.inc.php",$letitre);
    echo aide_simple("sequence");
      echo "<TR><TD><Font size='2'>&nbsp;$msq_seq : $titre_seq</TD></TR>";
      echo "<FORM Name='form1' enctype='multipart/form-data' action=\"sequence.php?activite=1&insert_act=1&parcours=$parcours&liste=$liste&id_parc=$id_parc&proprio=$proprio&refer=$refer&id_seq=$id_seq&id_ress=$id_ress&id_ref=$id_ref&id_ref_parc=$id_ref_parc&miens=$miens&miens_parc=$miens_parc&parc=$id_parc&liste_act_seq=1\" method='POST'>";
      echo "<TR><TD><TABLE widht='100%' cellspacing = '1' cellpadding = '2'>";
      if ($choix_ress == 1)
      {
         echo "<TR><TD nowrap><small>$msq_ress_act</small></TD><TD nowrap>";
         $titre_ress = GetDataField ($connect,"select ress_titre from ressource_new where ress_cdn = $id_ress","ress_titre");
         echo "$titre_ress</TD></TR>";//}
      }
      echo "<TR><TD nowrap><B>$msq_tit_form</B></TD>";
      echo "<TD nowrap><INPUT TYPE=\"TEXT\" class='INPUT'  name=\"titre\" align=\"middle\" size='75' value=\"$titre\"></TD></TR>";
      echo "<TR><TD nowrap><B>$msq_consigne_act_form</B></TD>";
      echo "<TD nowrap><TEXTAREA class='TEXTAREA' rows='6' cols='80' align=\"middle\" name=\"consigne\">".stripcslashes($consigne)."</TEXTAREA></TD></TR>";
      //dey Dfoad
     //ajout formulaire
      if (empty($ins_ch_act))
      {
         echo "<TR><TD><b>Insérer une consigne multimédia </b>(facultatif)</td>".
              "<td><div style='color:red;font-weight:bold;font-size:10px;'>";
         echo "<INPUT TYPE='file' name='userfile' enctype='multipart/form-data'></div></TD></TR>";
      }

      elseif ($ins_ch_act == 1)
      {
          $req_media = mysql_result(mysql_query("select count(*) from activite_media where actmedia_act_no = $id_act"),0);
          $media_act = "";
          if ($req_media > 0)
          {
             $media_act = GetDataField ($connect,"select ress_url_lb from ressource_new,activite_media where
                                            ress_cdn = actmedia_ress_no and actmedia_act_no =$id_act ","ress_url_lb");
             echo "<TR><TD valign='top'><B><div id='titleMedia'>Modifier la consigne multimédia<div></B></TD>";
          }
          else
             echo "<TR><TD valign='top'><B><div id='titleMedia'>Insérer une consigne multimédia<div></B></TD>";
          $lien="charge_ressource_vrac.php?id_act=$id_act&dou=act_free&media=1&charger_fichier=1";
          $lien = urlencode($lien);
          echo "<TD align='left' nowrap valign='top'><div style='clear:both;float:left;'>$bouton_gauche";
          echo "<A HREF=\"javascript:void(0)\" onClick=\"window.open('trace.php?link=$lien','','scrollbars=yes,resizable=yes,width=500,height=300,left=250,top=300')\" ".
                bulle("Vous retrouverez ce fichier (mp3, swf ou flv uniquement) dans votre répertoire dans le dossier Ressources Media","","RIGHT","ABOVE",220).
                "Télécharger le fichier (mp3, swf ou flv)</A>$bouton_droite</div>";
          if($media_act != "")
          {
                  $actit = $id_act;
                  echo '<div id="suppMedia" style="float:left;margin:4px 0 0 6px;"><a href="javascript:void(0);" name="suppMedia" '.
                       bullet("Cliquez ici pour supprimer la consigne multimédia en cours.","","RIGHT","ABOVE",240) .
                       ' onClick = "javascript:if (conf() == true){$.ajax({type: \'GET\',
                                              url: \'formation/gereMedia.php\',
                                              data: \'id_act='.$actit.'&suppMedia=1\',
                                              beforeSend:function()
                                              {
                                                 $(\'#affiche\').addClass(\'Status\');
                                                 $(\'#affiche\').append(\'Opération en cours....\');
                                              },
                                              success: function(){
                                                   $(\'#player'.$actit.'\').css(\'display\',\'none\');
                                                   $(\'#suppMedia\').css(\'display\',\'none\');
                                                   $(\'#mien\').empty();
                                                   $(\'#mien\').html(\'Vous venez de supprimer la consigne multimédia.\');
                                                   $(\'#titleMedia\').html(\'Insérer de nouveau une consigne multimédia.\');
                                                   $(\'#titleMedia\').css(\'font-weight\',\'bold\');
                                                   $(\'#affiche\').empty();
                                                   $(\'#mien\').show();
                                              }
                                        });};
                                        setTimeout(function() {$(\'#mien\').empty();},7000);" ><img src="images/supp.png" border="0"></a></div> ';
                  $largeur = "220";
                  $hauteur = "140";
                  echo "<div id='insertMedia'>";
                      include ("media.php");
                  echo "</div>";
          }
          echo "</TD></TR>";
      }
      echo "<TR><TD nowrap><B>$mess_admin_comment</B></TD>";
      echo "<TD nowrap><TEXTAREA class='TEXTAREA' rows='6' cols='80' align=\"middle\" name=\"commentaire\">".stripcslashes($commentaire)."</TEXTAREA></TD></TR>";
      echo "<TR><TD nowrap><B>$msq_ass_ress_form</B></TD><TD>";
      if ($ress_norok == 'OUI'){
         echo "$mess_oui&nbsp;&nbsp;<INPUT type='radio' name='ress_norok' value='OUI' checked>&nbsp;&nbsp;&nbsp;&nbsp;";
         echo "$mess_non&nbsp;&nbsp;<INPUT type='radio' name='ress_norok' value='NON'>";
      }elseif ($ress_norok == 'NON'){
         echo "$mess_oui&nbsp;&nbsp;<INPUT type='radio' name='ress_norok' value='OUI'>&nbsp;&nbsp;&nbsp;&nbsp;";
         echo "$mess_non&nbsp;&nbsp;<INPUT type='radio' name='ress_norok' value='NON' checked>";
      }else{
         echo "$mess_oui&nbsp;&nbsp;<INPUT type='radio' name='ress_norok' value='OUI'>&nbsp;&nbsp;&nbsp;&nbsp;";
         echo "$mess_non&nbsp;&nbsp;<INPUT type='radio' name='ress_norok' value='NON'>";
      }
      echo "</TD></TR>";
      echo "<TR><TD nowrap><B>$msq_droit_voir</B></TD>";
      echo "<TD nowrap>";
      if ($droit_voir_act == "") {
            echo "$mess_oui&nbsp;&nbsp;<INPUT type='radio' name='droit_voir_act' value='1'>&nbsp;&nbsp;&nbsp;&nbsp;";
            echo "$mess_non&nbsp;&nbsp;<INPUT type='radio' name='droit_voir_act' value='0'>";
      }elseif ($droit_voir_act == 1) {
            echo "$mess_oui&nbsp;&nbsp;<INPUT type='radio' name='droit_voir_act' value='1' checked>&nbsp;&nbsp;&nbsp;&nbsp;";
            echo "$mess_non&nbsp;&nbsp;<INPUT type='radio' name='droit_voir_act' value='0'>";
      }elseif ($droit_voir_act == 0) {
            echo "$mess_oui&nbsp;&nbsp;<INPUT type='radio' name='droit_voir_act' value='1'>&nbsp;&nbsp;&nbsp;&nbsp;";
            echo "$mess_non&nbsp;&nbsp;<INPUT type='radio' name='droit_voir_act' value='0' checked>";
      }
      echo "</TD></TR>";
      echo "<TR><TD nowrap><B>$msq_duree_form</B></TD><TD align='left'><TABLE border='0' cellspacing='0'><TR>";
      if ($titre == '' || $ins_ch_act == 1 || $renvoi == 1){
         $reste = $duree%60;
         $heure = floor($duree/60);
         if ($duree == 0 || !isset($duree)){
           echo "<TD><INPUT TYPE='text'  class='INPUT'  name='horaire' size='2' maxlength = '3' align='center'>&nbsp;$h </TD>";
           echo "<TD><INPUT TYPE='text'  class='INPUT'  name='minutage' size='2' maxlength = '2' align='center'>&nbsp;$mn</TD>";
         }else{
           echo "<TD><INPUT TYPE='text'  class='INPUT'  name='horaire' value='$heure' size='2' maxlength = '3' align='center'>&nbsp;$h </TD>";
           echo "<TD><INPUT TYPE='text'  class='INPUT'  name='minutage' value='$reste' size='2' maxlength = '2' align='center'>&nbsp;$mn</TD>";
         }
      }
      echo "</TR></TABLE></TD></TR>";
      echo "<TR><TD nowrap><B>$msq_pass_act_form</B></TD><TD nowrap>";
      //Si on choisit une activite existante, on affiche les renseignements la concernant
      if ($ins_ch_act == 1) {
         echo "<INPUT type=hidden  name='pass_mult' value='$pass_mult'>";
         echo "$pass_mult";
         echo"<br>";
      }else{
         if ($renvoi == 1 && $pass_mult == 'OUI'){
            echo "$mess_oui&nbsp;&nbsp;<INPUT type='radio' name='pass_mult' value='OUI' checked>&nbsp;&nbsp;&nbsp;&nbsp;";
            echo "$mess_non&nbsp;&nbsp;<INPUT type='radio' name='pass_mult' value='NON'>";
         }elseif ($renvoi == 1 && $pass_mult == 'NON'){
            echo "$mess_oui&nbsp;&nbsp;<INPUT type='radio' name='pass_mult' value='OUI'>&nbsp;&nbsp;&nbsp;&nbsp;";
            echo "$mess_non&nbsp;&nbsp;<INPUT type='radio' name='pass_mult' value='NON' checked>";
         }else{
            echo "$mess_oui&nbsp;&nbsp;<INPUT type='radio' name='pass_mult' value='OUI'>&nbsp;&nbsp;&nbsp;&nbsp;";
            echo "$mess_non&nbsp;&nbsp;<INPUT type='radio' name='pass_mult' value='NON'>";
         }
         echo "<br>";
      }
      echo "</TD></TR>";
      echo "<TR><TD nowrap><B>$msq_acquit_form</B></TD><TD nowrap>";
      if ($ins_ch_act == 1) {
         echo "<INPUT type=hidden  name='acquit' value='$acquit'>";
         echo "$acquit<br>";
      }else{
         if ($renvoi == 1 &&  $acquit== 'APPRENANT'){
            echo ucfirst(strtolower($msq_apprenant))."&nbsp;&nbsp;<INPUT type='radio' name='acquit' value='APPRENANT' checked>&nbsp;&nbsp;&nbsp;&nbsp;";
            echo ucfirst(strtolower($msq_formateur))."&nbsp;&nbsp;<INPUT type='radio' name='acquit' value='FORMATEUR_REFERENT'>&nbsp;&nbsp;&nbsp;&nbsp;";
            echo ucfirst(strtolower($msq_ress))."&nbsp;&nbsp;<INPUT type='radio' name='acquit' value='RESSOURCE' onclick=\"window.alert('$mess_avrt_act_ress');\">";
         }elseif ($renvoi == 1 &&  $acquit== 'FORMATEUR_REFERENT'){
            echo ucfirst(strtolower($msq_apprenant))."&nbsp;&nbsp;<INPUT type='radio' name='acquit' value='APPRENANT'>&nbsp;&nbsp;&nbsp;&nbsp;";
            echo ucfirst(strtolower($msq_formateur))."&nbsp;&nbsp;<INPUT type='radio' name='acquit' value='FORMATEUR_REFERENT' checked>&nbsp;&nbsp;&nbsp;&nbsp;";
            echo ucfirst(strtolower($msq_ress))."&nbsp;&nbsp;<INPUT type='radio' name='acquit' value='RESSOURCE' onclick=\"window.alert('$mess_avrt_act_ress');\">";
         }elseif ($renvoi == 1 &&  $acquit== 'RESSOURCE'){
            echo ucfirst(strtolower($msq_apprenant))."&nbsp;&nbsp;<INPUT type='radio' name='acquit' value='APPRENANT'>&nbsp;&nbsp;&nbsp;&nbsp;";
            echo ucfirst(strtolower($msq_formateur))."&nbsp;&nbsp;<INPUT type='radio' name='acquit' value='FORMATEUR_REFERENT'>&nbsp;&nbsp;&nbsp;&nbsp;";
            echo ucfirst(strtolower($msq_ress))."&nbsp;&nbsp;<INPUT type='radio' name='acquit' value='RESSOURCE' checked>";
         }else{
            echo ucfirst(strtolower($msq_apprenant))."&nbsp;&nbsp;<INPUT type='radio' name='acquit' value='APPRENANT'>&nbsp;&nbsp;&nbsp;&nbsp;";
            echo ucfirst(strtolower($msq_formateur))."&nbsp;&nbsp;<INPUT type='radio' name='acquit' value='FORMATEUR_REFERENT'>&nbsp;&nbsp;&nbsp;&nbsp;";
            echo ucfirst(strtolower($msq_ress))."&nbsp;&nbsp;<INPUT type='radio' name='acquit' value='RESSOURCE' onclick=\"window.alert('$mess_avrt_act_ress');\">";
         }
      }
      echo "<br></TD></TR>";
      echo "<TR><TD nowrap><B>$msq_dev_act_form</B></TD><TD nowrap>";
      if ($ins_ch_act == 1) {
         echo "<INPUT type=hidden  name='dev_a_rendre' value='$dev_a_rendre'>";
         echo "$dev_a_rendre";
      }else{
         if ($renvoi == 1 && $dev_a_rendre == 'OUI'){
            echo "$mess_oui&nbsp;&nbsp;<INPUT type='radio' name='dev_a_rendre' value='OUI' checked>&nbsp;&nbsp;&nbsp;&nbsp;";
            echo "$mess_non&nbsp;&nbsp;<INPUT type='radio' name='dev_a_rendre' value='NON'>";
         }elseif ($renvoi == 1 && $dev_a_rendre == 'NON'){
            echo "$mess_oui&nbsp;&nbsp;<INPUT type='radio' name='dev_a_rendre' value='OUI'>&nbsp;&nbsp;&nbsp;&nbsp;";
            echo "$mess_non&nbsp;&nbsp;<INPUT type='radio' name='dev_a_rendre' value='NON' checked>";
         }else{
            echo "$mess_oui&nbsp;&nbsp;<INPUT type='radio' name='dev_a_rendre' value='OUI'>&nbsp;&nbsp;&nbsp;&nbsp;";
            echo "$mess_non&nbsp;&nbsp;<INPUT type='radio' name='dev_a_rendre' value='NON'>";
         }
      }
      echo "<br></TD></TR>";
      echo "<TR><TD nowrap><B>$msq_not_act_form</B></TD><TD nowrap>";
      if ($ins_ch_act == 1) {
         echo "<INPUT type=hidden  name='notation' value='$notation'>";
         echo "$notation";
      }else{
         if ($renvoi == 1 && $notation == 'OUI'){
            echo "$mess_oui&nbsp;&nbsp;<INPUT type='radio' name='notation' value='OUI' checked>&nbsp;&nbsp;&nbsp;&nbsp;";
            echo "$mess_non&nbsp;&nbsp;<INPUT type='radio' name='notation' value='NON'>";
         }elseif ($renvoi == 1 && $notation == 'NON'){
            echo "$mess_oui&nbsp;&nbsp;<INPUT type='radio' name='notation' value='OUI'>&nbsp;&nbsp;&nbsp;&nbsp;";
            echo "$mess_non&nbsp;&nbsp;<INPUT type='radio' name='notation' value='NON' checked>";
         }else{
            echo "$mess_oui&nbsp;&nbsp;<INPUT type='radio' name='notation' value='OUI'>&nbsp;&nbsp;&nbsp;&nbsp;";
            echo "$mess_non&nbsp;&nbsp;<INPUT type='radio' name='notation' value='NON'>";
         }
      }
      echo "<br></TD></TR>";
      //dey Dfoad
          if ($ins_ch_act == 1 && $id_act > 0)
          {
             $req_typdev = mysql_result(mysql_query("select count(*) from activite_devoir where actdev_act_no = $id_act"),0);
             $dev_act = "";
             if ($req_typdev > 0)
             {
                 $dev_act = GetDataField ($connect,"select actdev_dev_lb from activite_devoir where
                                            actdev_act_no = $id_act ","actdev_dev_lb");
                 echo "<INPUT type='HIDDEN' name='okDev' value='1'>";
             }
             else
                 $dev_act = "Pas de devoir";

          }
          else
          {
             $dev_act = "Pas de devoir";
          }
          echo "<TR><TD style='font-weight:bold;'>Type de devoir</td>";
          echo "<td><select class='SELECT' id='typdev' name='typdev' style='font-weight:bold;font-size:13px;'>";
          echo "<option value='$dev_act'>$dev_act</option>";
          if ($dev_act != 'Pas de devoir')
             echo "<option value='Pas de devoir'>Pas de devoir</option>";
          if ($dev_act != 'Autocorrectif')
             echo "<option value='Autocorrectif'>Autocorrectif</option>";
          if ($dev_act != 'Correction')
             echo "<option value='Correction'>Correction</option>";
          if ($dev_act != 'A renvoyer')
             echo "<option value='A renvoyer'>A renvoyer</option>";
/*          if ($dev_act != 'xApi TinCan')
               echo "<option value='xApi TinCan'>xApi TinCan</option>";*/
          echo "</select></TD></TR>";
      echo "</TD></TR><TR height='50'>";
      echo "<TD>&nbsp;</TD><TD align='left'><A HREF=\"javascript:checkForm(document.form1);\" onClick=\"TinyMCE.prototype.triggerSave();\" onmouseover=\"img1.src='images/fiche_identite/boutvalidb$suffixer.gif';return true;\" onmouseout=\"img1.src='images/fiche_identite/boutvalid$suffixer.gif'\">".
       "<IMG NAME=\"img1\" SRC=\"images/fiche_identite/boutvalid$suffixer.gif\" BORDER='0' onLoad=\"tempImg=new Image(0,0); tempImg.src='images/fiche_identite/boutvalidb$suffixer.gif'\"></A>";
      echo "</TD><TR></FORM></TABLE>";
      fin_tableau($html);
    }//fin if ($titre = '')
}
//Choix d'une activité existante à inserer dans la sequence
if ($choix_act == 1)
{

?>
<SCRIPT language=JavaScript>
    function checkForm(frm) {
      var ErrMsg = "<?php echo $mess_info_no;?>\n\n";
      var lenInit = ErrMsg.length;
      if (isEmpty(frm.id_act)== true)
        ErrMsg += ' - <?php echo $msq_choix_activ;?>\n';
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
   $afficher_choix = ChoixActModSeq($id_seq);
   if (!isset($action_seq) || $action_seq != 1){

      $titre_seq = GetDataField ($connect,"select seq_titre_lb from sequence where seq_cdn = $id_seq","seq_titre_lb");
      $letitre= $msq_choix_act;
      entete_concept("liste_parc.inc.php",$letitre);
      echo aide_simple("sequence");
      echo "<TR height='30'><TD colspan='2' align=left><Font size='2'><B>$msq_seq : </B>$titre_seq</font></TD></TR>";
      echo $afficher_choix;
   }
}//fin if ($choix_act == 1)


//Consultation sequence
if ($consult == 1)
{

    //Suppression d'une sequence  et des activites la concernant
    if (isset($id_seq) && $id_seq > 0)
       $son_nom = GetDataField ($connect,"select seq_titre_lb from sequence where seq_cdn=$id_seq","seq_titre_lb");
    if ($supp == 1 && $toutes_seq != 1)
    {
       $requete_grp = mysql_query ("select * from groupe order by grp_nom_lb");
       $nb_grp = mysql_num_rows($requete_grp);
       if ($nb_grp > 0)
       {
            $gp=0;
            while ($gp < $nb_grp)
            {
                 $id_grp = mysql_result($requete_grp,$gp,"grp_cdn");
                 $nomb = mysql_num_rows(mysql_query ("select * from suivi2_$id_grp where suiv2_seq_no = $id_seq"));
                 $nombre_seq += $nomb;
             $gp++;
            }
        }
        if ($nombre_seq > 0)
        {
         echo "<CENTER><TABLE bgColor='#298CA0' cellspacing='2' cellpadding='0' width='98%'>";
         include ("liste_parc.inc.php");
         echo "<TD valign='top' width='70%' bgColor='#FFFFFF' height='100%'>";
         echo "<TABLE bgColor='#FFFFFF' cellspacing='1' cellpadding='0' width='100%'>";
         echo "<TR><TD><Font size='2'>$msq_nosup_seq</TD></TR>";
         echo boutret(1,0);
         echo fin_tableau($html);
        exit;
        }
      $seq_parc = mysql_query ("select * from sequence_parcours where seqparc_seq_no = $id_seq");
      $nbre_seq = mysql_num_rows($seq_parc);
      $message = addslashes($son_nom).' '.$seq_supp_ok;
      if ($nbre_seq > 1)
         $seq_supp = mysql_query ("delete from sequence_parcours where seqparc_seq_no = $id_seq AND seqparc_parc_no = $id_parc");
      else
      {
         $reqseq_parc = mysql_query ("select count(*) from sequence_parcours where seqparc_parc_no = $id_parc");
         $nbre_seq = mysql_result($reqseq_parc,0);
         $supp_seq_ref = mysql_query ("delete from sequence_referentiel where seqref_seq_no = $id_seq");
         $seq_supp = mysql_query ("delete from sequence_parcours where seqparc_seq_no = $id_seq AND seqparc_parc_no = $id_parc");
         $req_supp_act = mysql_query ("select * from activite where act_seq_no = $id_seq");
         $nbre_act = mysql_num_rows($req_supp_act);
         if ($nbre_act > 0)
         {
             while ($item = mysql_fetch_object($req_supp_act)) {
                    $id_act_supp = $item -> act_cdn;
                    $supp_star = mysql_query("delete from stars where star_item_id = '$id_act_supp' and star_type_no='3'");
                    $seq_supp = mysql_query ("delete from activite where act_cdn = $id_act_supp");
                    //dey Dfoad
                       $act_media = mysql_query("delete from activite_media where actmedia_act_no = $id_act_supp");
                    $supp_rss = rss :: supprime('activite',$id_act_supp);
             }
         }
         $supp_act_seq = mysql_query ("delete from activite where act_seq_no = $id_seq");
         $supp_mod_seq = mysql_query ("delete from scorm_module where mod_seq_no = $id_seq");
         $supp_prereq = mysql_query ("delete from prerequis where prereq_seq_no = $id_seq");
         $del_seqprereq = mysql_query ("delete from prerequis where prereq_seqcondition_no = $id_seq");
         $supp_star = mysql_query("delete from stars where star_item_id = '$id_seq' and star_type_no='2'");
         $supp_seq = mysql_query ("delete from sequence where seq_cdn=$id_seq");
         $supp_rss = rss :: supprime('sequence',$id_seq);
      }
        //supprimer aussi pre-requis dans lesquels la sequence fait partie du pre-requis
    }
    elseif ($supp == 1 && $toutes_seq == 1)
    {
        $seq_supp = mysql_query ("delete from sequence_parcours where seqparc_seq_no = $id_seq and seqparc_parc_no = $id_parc");
        $reqseq_parc = mysql_query ("select count(*) from sequence_parcours where seqparc_parc_no = $id_parc");
        $nbre_seq = mysql_result($reqseq_parc,0);
        $message = $mess_laseq." ".addslashes($son_nom)." - ".$seq_supp_ok;
    }
    if ($modif == 1)
    {
      if  ($monter == 1 || $descendre == 1)
      {
        if ($monter == 1)
          $nouvel_ordre = GetDataField ($connect,"select seqparc_ordre_no from sequence_parcours where seqparc_seq_no=$seq_precedente and seqparc_parc_no=$id_parc","seqparc_ordre_no");
        else
          $nouvel_ordre = GetDataField ($connect,"select seqparc_ordre_no from sequence_parcours where seqparc_seq_no=$seq_suivante and seqparc_parc_no=$id_parc","seqparc_ordre_no");
        $ancien_ordre = GetDataField ($connect,"select seqparc_ordre_no from sequence_parcours where seqparc_seq_no=$seq_actuelle and seqparc_parc_no=$id_parc","seqparc_ordre_no");
        $modif_ordre = mysql_query("UPDATE sequence_parcours SET seqparc_ordre_no = $nouvel_ordre where seqparc_seq_no = $seq_actuelle and seqparc_parc_no=$id_parc");
        if ($monter == 1)
           $modif_ordre = mysql_query("UPDATE sequence_parcours SET seqparc_ordre_no = $ancien_ordre where seqparc_seq_no = $seq_precedente and seqparc_parc_no=$id_parc");
        else
           $modif_ordre = mysql_query("UPDATE sequence_parcours SET seqparc_ordre_no = $ancien_ordre where seqparc_seq_no = $seq_suivante and seqparc_parc_no=$id_parc");
      }
      else
      {
/*
echo "<pre>";
     print_r($_POST);print_r($_GET);
echo "</pre>";exit;
*/
        $upd_seq = mysql_query ("update sequence set seq_titre_lb = \"".str_replace("\"","'",$nom)."\",seq_desc_cmt = \"".
                                htmlentities($description,ENT_QUOTES,'iso-8859-1')."\",seq_mots_clef = \"".
                                htmlentities($mots_clef,ENT_QUOTES,'iso-8859-1')."\",".
                                "seq_ordreact_on = \"$ordre_des_act\",seq_modif_dt = \"$date_dujour\",seq_publique_on = '$droit_voir_seq', ".
                                "seq_type_on = $type_seq where seq_cdn = $id_seq");
        if ($seqduref == 1 && isset($_POST['dureeRef']))
        {
           $reqDureeSeq = mysql_query("select seqduref_duree_nb from seq_duree_ref where seqduref_seq_no= $id_seq");
           if (mysql_num_rows($reqDureeSeq) > 0 && $_POST['dureeRef'] > 0)
              $updateSeqRef = mysql_query ("update seq_duree_ref set seqduref_duree_nb='".$_POST['dureeRef']."' where seqduref_seq_no = '$id_seq'");
           elseif (mysql_num_rows($reqDureeSeq) > 0 && $_POST['dureeRef'] == "")
              $delSeqRef = mysql_query ("delete from seq_duree_ref  where seqduref_seq_no = '$id_seq'");
           else
              $insertSeqRef = mysql_query ("insert into seq_duree_ref values (NULL,$id_seq,'".$_POST['dureeRef']."')");
        }
        $modifie_rss = rss :: modifie('sequence',$id_user,$id_seq);
        $message = $mess_laseq." ".addslashes($son_nom).' '.$mess_admin_fic_modif_fin;
      }
    }
    if ($modif_type == 1){
       $upd_seq = mysql_query ("update sequence set seq_modif_dt = \"$date_dujour\", seq_type_on = 1, seq_publique_on = 1 where seq_cdn = $id_seq");
        $modifie_rss = rss :: modifie('sequence',$id_user,$id_seq);
       $message = $mess_suit_dch;
    }
    if ($modif_type_normal == 1){
       $upd_seq = mysql_query ("update sequence set seq_type_on = 0, seq_modif_dt = \"$date_dujour\", seq_publique_on = 1 where seq_cdn = $id_seq");
       $modifie_rss = rss :: modifie('sequence',$id_user,$id_seq);
       $supp_req = mysql_query ("delete from sequence_parcours where seqparc_seq_no = $id_seq");
       $message = $mess_suit_dch;
    }
    if ($modif_seqref == 1)
    {
       $upd_seq = mysql_query ("update sequence_referentiel set seqref_referentiel_no='$id_ref' where seqref_seq_no = $id_seq");
       $message = $mess_suit_dch;
    }
    //Ajout d'une sequence existante dans un autre referentiel
    if ($ajout_seq == 1)
    {
        $id_seqref = Donne_ID ($connect,"select max(seqref_cdn) from sequence_referentiel");
        $autre_ref = mysql_query ("INSERT INTO sequence_referentiel values ($id_seqref,$id_seq,$id_ref)");
        $message = $mess_suit_dch;
    }

    //Si l'on vient d'un parcours, on n'effectue pas les memes requetes. On selectionne les seq par rapport au parcours sinon par rapport au referentiel
    if ($nbre_seq == 0 && $supp == 1)
    {
      $lien = "parcours.php?liste=1&consult=1&parcours=1&id_parc=$id_parc&proprio=$proprio&refer=$refer&id_ref=$id_ref&id_ref_parc=$id_ref_parc&miens_parc=$miens_parc&miens=$miens&parc=$id_parc&message=$message";
      $lien = urlencode($lien);
      echo "<script language=\"JavaScript\">";
      echo "document.location.replace(\"trace.php?link=$lien\")";
      echo "</script>";
      exit();
    }
//  if ($typ_user == 'ADMINISTRATEUR' || $typ_user == 'RESPONSABLE_FORMATION' || $typ_user == 'FORMATEUR_REFERENT'){
    if ($toutes_seq == 1 || ($supp == 1 && $nbre_seq > 0))
        $seq_query = mysql_query ("select * from sequence,sequence_parcours where seqparc_parc_no = $id_parc and seqparc_seq_no = seq_cdn order by sequence_parcours.seqparc_ordre_no");
    elseif (!$liste && !$toutes_seq)
        $seq_query = mysql_query ("select * from sequence,sequence_referentiel where seqref_referentiel_no = $id_ref AND seq_type_lb = 'NORMAL' and seqref_seq_no = seq_cdn order by seq_cdn");
    elseif ($liste && !$toutes_seq)
       $seq_query = mysql_query ("select * from sequence where seq_cdn = $id_seq");
/*  }else{
    if ($toutes_seq == 1 || ($supp == 1 && $nbre_seq > 0))
        $seq_query = mysql_query ("select * from sequence,sequence_parcours where seqparc_parc_no = $id_parc and seqparc_seq_no = seq_cdn AND (seq_publique_on = 1 OR (seq_publique_on = 0 AND seq_auteur_no = $id_user)) order by sequence_parcours.seqparc_ordre_no");
    elseif (!$liste && !$toutes_seq)
        $seq_query = mysql_query ("select * from sequence,sequence_referentiel where seqref_referentiel_no = $id_ref and seqref_seq_no = seq_cdn AND seq_type_lb = 'NORMAL' AND (seq_publique_on = 1 OR (seq_publique_on = 0 AND seq_auteur_no = $id_user)) order by seq_cdn");
    elseif ($liste && !$toutes_seq)
        $seq_query = mysql_query ("select * from sequence where seq_cdn = $id_seq AND (seq_publique_on = 1 OR (seq_publique_on = 0 AND seq_auteur_no = $id_user))");
  }
*/
  $nb_seq = mysql_num_rows ($seq_query);
  if ($nb_seq == 0 && !$toutes_seq && $id_ref == 0)
  {
      $lien = "sequence.php?prem=1&liste=1&refer=$refer&miens=$miens&miens_parc=$miens_parc&ordre_affiche=lenom&message=$message";
      $lien = urlencode($lien);
      echo "<script language=\"JavaScript\">";
      echo "document.location.replace(\"trace.php?link=$lien\")";
      echo "</script>";
      exit();
  }
  elseif($nb_seq == 0 && $toutes_seq == 1)
  {
      $lien = "parcours.php?liste=1&consult=1&parcours=$parcours&id_parc=$id_parc&proprio=$proprio&refer=$refer&id_ref=$id_ref&id_ref_parc=$id_ref_parc&parc=$id_parc&message=$message";
      $lien = urlencode($lien);
      echo "<script language=\"JavaScript\">";
      echo "document.location.replace(\"trace.php?link=$lien\")";
      echo "</script>";
      exit();
  }
  elseif($nb_seq == 0 && !$toutes_seq && $id_ref > 0 && $supp != 1)
  {
      $lien = "sequence.php?choix_ref=1&id_ref=$id_ref&id_ref_parc=$id_ref_parc&miens=$miens&miens_parc=$miens_parc&parc=$id_parc&message=$message";
      $lien = urlencode($lien);
      echo "<script language=\"JavaScript\">";
      echo "document.location.replace(\"trace.php?link=$lien\")";
      echo "</script>";
      exit();
  }
  elseif($nb_seq == 0 && !$toutes_seq && $id_ref > 0 && $supp == 1)
  {
      $lien = "sequence.php?prem=1&liste=1&refer=1&ordre_affiche=lenom&miens=$miens&miens_parc=$miens_parc&parc=$id_parc&message=$message";
      $lien = urlencode($lien);
      echo "<script language=\"JavaScript\">";
      echo "document.location.replace(\"trace.php?link=$lien\")";
      echo "</script>";
      exit();
  }
  else
  {
       if (!isset($id_ref))
       {
          if ($toutes_seq == 1)
             $id_ref_parc = GetDataField ($connect,"select parcours_referentiel_no from parcours where parcours_cdn = $id_parc","parcours_referentiel_no");
          else
             $id_ref = GetDataField ($connect,"select seqref_referentiel_no from sequence_referentiel where seqref_seq_no = $id_seq","seqref_referentiel_no");
          if ($id_ref > 0){
             $referentiel = GetDataField ($connect,"select ref_nomabrege_lb from referentiel where ref_cdn = $id_ref","ref_nomabrege_lb");
             $nom_referentiel = GetDataField ($connect,"select ref_desc_cmt  from referentiel where ref_cdn = $id_ref","ref_desc_cmt");
             if ($nom_referentiel == "")
                $nom_referentiel = GetDataField ($connect,"select ref_nomabrege_lb from referentiel where ref_cdn = $id_ref","ref_nomabrege_lb");
          }
       }
       if (isset($message) && $message != '')
            echo notifier($message);
       $letitre= $mess_menu_gest_seq;
       echo "<CENTER><TABLE bgColor='#298CA0' cellspacing='2' cellpadding='0' width='98%'>";
       if ($parcours == 1 || $requete_parc !="")
       {
         include ("liste_parc.inc.php");
         $id_ref_parc = GetDataField ($connect,"select parcours_referentiel_no from parcours where parcours_cdn = $id_parc","parcours_referentiel_no");
         echo "<TD width='70%' valign='top' height='100%' bgColor='#FFFFFF'>";
         $letitre= $mess_menu_gest_parc;
       }else{
         echo "<TR><TD>";
         $letitre= $mess_menu_gest_seq;
       }
       echo "<TABLE cellspacing='1' cellpadding='0' width='100%'>";
       echo "<TR><TD background=\"images/fond_titre_table.jpg\" height='40' align='center' valign='center'><Font size='3' color='#FFFFFF'><B>$letitre</B></FONT></TD></TR>";
       echo aide_simple("sequence");
       echo "<TR><TD width=100%>";
       echo "<TABLE cellspacing='0'  cellpadding='0' width='100%' border=0>";
       echo "<TR><TD colspan='2' class='sous_titre'>$sous_titre</TD></TR>";
       $bgcolor2 = '#2B677A';
       $bgcolor1 = '#F8F2E4';
       echo "<TR><TD valign='top' align='center' width='100%'><table bgColor='#FFFFFF' width='100%' cellpadding='5' cellspacing='1'><tr bgcolor=$bgcolor2 height='35'>";
       $auteur_parc = GetDataField ($connect,"select parcours_auteur_no from parcours where parcours_cdn = $id_parc","parcours_auteur_no");
       if ($parcours == 1 && $nb_seq > 1)
       {
          if ($auteur_parc == $id_user || $typ_user == 'ADMINISTRATEUR'){
            echo "<TD width='5' height='20' align='middle'><IMG SRC=\"images/modules/tut_form/flechebas.gif\" TITLE=\"$mess_increment\" border='0'></TD>";
            echo "<TD width='5' height='20' align='middle'><IMG SRC=\"images/modules/tut_form/flechehaut.gif\" TITLE=\"$mess_decrement\" border='0'></TD>";
          }
       }
       echo "<td width='55%' valign='top'><FONT COLOR=white><b>$mpa_nbr_seq</b></FONT></td>";
       echo "<td width='5%' height='20' valign='top'><FONT COLOR=white><b>$msq_acts</b></FONT></td>";
       echo "<td width='7%' height='20' valign='top'><FONT COLOR=white><b>$msq_duree_seq</b></td>";
       if ($seqduref == 1)
          echo "<td width='7%' height='20' valign='top'><FONT COLOR=white><b>Durée de référence</b></td>";
       echo "<td width='7%' height='20' valign='top'><FONT COLOR=white><b>$msq_si_prereq</b></FONT></td>";
       echo "<TD width='20' height='20' valign='top'><FONT COLOR=white><b>$mess_ajt_biblio</b></FONT></TD>";
       echo "<TD width='20' height='20' valign='top'><FONT COLOR=white><b>$mess_biblio</b></FONT></TD>";
       echo "<TD width='20' height='20' valign='top'><FONT COLOR=white><b>$mess_modif_base</b></FONT></TD>".
            "<TD width='20' height='20' valign='top'><FONT COLOR=white><b>$mess_ag_supp</b></FONT></TD>".
            "<TD width='20' height='20' valign='top'><FONT COLOR=white><b>$mess_dupliquer</b></FONT></TD>";
       echo "</TR>";
       $i = 0;
       $nb_seq_scorm = 0;
       $id_seq_parc = array();
       while ($i < $nb_seq)
       {
               $icono ='';$le_mode = '';
               $l = $i+1;
               $id = mysql_result ($seq_query,$i,"seq_cdn");
               $id_seq_parc[$i] = $id;
               if ($parcours == 1 && $nb_seq > 1)
               {
                 if ($i > 0)
                   $seq_precedente = $id_seq_parc[$i-1];
                 if ($i < $nb_seq-1)
                   $seq_suivante = mysql_result ($seq_query,$l,"seq_cdn");
                 else
                   $seq_suivante = "";

               }
               $seq_actuelle = $id;
               $nom = mysql_result ($seq_query,$i,"seq_titre_lb");
               $seq_type = mysql_result ($seq_query,$i,"seq_type_on");
               $mod_seq = mysql_result ($seq_query,$i,"seq_type_lb");
               $seq_duree = mysql_result ($seq_query,$i,"seq_duree_nb");
               $seq_dv = mysql_result ($seq_query,$i,"seq_publique_on");
               if ($seq_dv == 1)
                  $nb_seq_dv++;
               $div = "<DIV id='sequence'>";
/*
              if ($seq_type == 1 && $seq_dv == 1){
                 $div = "<DIV id='sequence'>";
                 $icono = "<IMG SRC='images/modules/icotype.gif' border='0'>";
              }else{
                 $div = "<DIV id='sequence'>";
                 $icono = "";
              }
*/
               $ordre_act = mysql_result ($seq_query,$i,"seq_ordreact_on");
               $droit_voir_seq = mysql_result ($seq_query,$i,"seq_publique_on");
               $desc_seq = html_entity_decode(mysql_result ($seq_query,$i,"seq_desc_cmt"),ENT_QUOTES,'iso-8859-1');
               //$desc_seq = str_replace("\r\n","<BR>",$desc_seq);
               if ($seqduref == 1)
               {
                   $reqDureeSeq = mysql_query("select seqduref_duree_nb from seq_duree_ref where seqduref_seq_no= $id");
                   if (mysql_num_rows($reqDureeSeq) > 0)
                      $dureeRefSeq = mysql_result($reqDureeSeq,0,"seqduref_duree_nb");
                   else
                      $dureeRefSeq = "";
               }
               if ($desc_seq != "")
                 $desc_seq= addcslashes($desc_seq,"\0..\47!@\176..\255");
               if (!strstr($mod_seq,"SCORM")){
                   $nb_act_seq_query = mysql_query ("select count(act_cdn) from activite where act_seq_no = $id");
                   $nb_act_seq = mysql_result ($nb_act_seq_query,0);
                   $sql_duree = mysql_query ("SELECT act_duree_nb from activite where act_seq_no = $id");
                   $duree_srch = "act_duree_nb";
               }else{
                   $nb_act_seq_query = mysql_query ("select count(mod_cdn) from scorm_module where mod_seq_no = $id AND mod_launch_lb !=''");
                   $nb_act_seq = mysql_result ($nb_act_seq_query,0);
                   $sql_duree = mysql_query ("SELECT mod_duree_nb from scorm_module where mod_seq_no = $id AND mod_duree_nb != ''");
                   $duree_srch = "mod_duree_nb";
               }

               $nb_duree = mysql_num_rows ($sql_duree);
               $iduree = 0;$duree_new = 0;
               while ($iduree<$nb_duree){
                 $duree_new+= mysql_result($sql_duree,$iduree,$duree_srch);
                 $iduree++;
               }
               $corrige_duree = mysql_query ("UPDATE sequence SET seq_duree_nb = \"$duree_new\",seq_modif_dt = \"$date_dujour\" WHERE seq_cdn= $id");
               $requete_grp = mysql_query ("select * from groupe order by grp_nom_lb");
               $nb_grp = mysql_num_rows($requete_grp);
               if ($nb_grp > 0)
               {
                   $gp=0;
                   while ($gp < $nb_grp)
                   {
                      $id_grp = mysql_result($requete_grp,$gp,"grp_cdn");
                      $nomb = mysql_num_rows(mysql_query ("select * from suivi2_$id_grp where suiv2_seq_no = $id"));
                      if ($nomb > 0)
                         $corrige_duree = mysql_query ("UPDATE suivi2_$id_grp SET suiv2_duree_nb = \"$duree_new\" WHERE suiv2_seq_no= $id");
                      $gp++;
                   }
               }
               //Pour meme raison que typ_user, on selectionne auteur
               $id_auteur = GetDataField ($connect,"select seq_auteur_no from sequence where seq_cdn = $id","seq_auteur_no");
               $nom_auteur = GetDataField ($connect,"select util_nom_lb from utilisateur where util_cdn = $id_auteur","util_nom_lb");
               $prenom_auteur = GetDataField ($connect,"select util_prenom_lb from utilisateur where util_cdn = $id_auteur","util_prenom_lb");
               $id_ref_seq = GetDataField ($connect,"select seqref_referentiel_no from sequence_referentiel where seqref_seq_no = \"$id\"","seqref_referentiel_no");
               if ($id_ref_seq > 0){
                 $desc_ref = GetDataField ($connect,"select ref_desc_cmt from referentiel where ref_cdn = \"$id_ref_seq\"","ref_desc_cmt");
                 if ($desc_ref == ""){
                   $desc_ref = GetDataField ($connect,"select ref_nomabrege_lb from referentiel where ref_cdn = $id_ref_seq","ref_nomabrege_lb");
                 }
                 $niv_ref = GetDataField ($connect,"select ref_denom_lb  from referentiel where ref_cdn = \"$id_ref_seq\"","ref_denom_lb");
               }else{
                 $desc_ref = "$msq_slcp $prenom_auteur  $nom_auteur";
                 $niv_ref = $msq_nd ;
               }
               $seq_prereq = mysql_query ("select * from prerequis where prereq_seq_no = $id");
               $nb_prereq = mysql_num_rows ($seq_prereq);
               echo couleur_tr($i+1,'');
               if (($auteur_parc == $id_user || $typ_user == 'ADMINISTRATEUR') && $parcours == 1 && $nb_seq > 1){
                if ($mod_seq == 0 && $parcours == 1 && $nb_seq > 1){
                  if ($i < $nb_seq -1){
                     $lien="sequence.php?liste=$liste&toutes_seq=$toutes_seq&consult=1&parcours=$parcours&id_parc=$id_parc&proprio=$proprio&refer=$refer&modif=1&descendre=1&seq_actuelle=$seq_actuelle&seq_precedente=$seq_precedente&seq_suivante=$seq_suivante&id_ref=$id_ref&id_ref_parc=$id_ref_parc&refer=$refer&parc=$id_parc&miens=$miens&miens_parc=$miens_parc";
                     $lien = urlencode($lien);
                     echo "<TD width='20' align='middle' valign='top'><A HREF=\"trace.php?link=$lien\" target='main' TITLE=\"$mess_increment\"><IMG SRC=\"images/modules/tut_form/flechebas.gif\" TITLE=\"$mess_increment\" border='0'></A></td>";
                  }else
                    echo"<td align='middle'>&nbsp;</td>";
                  if ($i > 0){
                     $lien="sequence.php?liste=$liste&toutes_seq=$toutes_seq&consult=1&parcours=$parcours&id_parc=$id_parc&proprio=$proprio&refer=$refer&modif=1&monter=1&seq_actuelle=$seq_actuelle&seq_precedente=$seq_precedente&seq_suivante=$seq_suivante&id_ref=$id_ref&id_ref_parc=$id_ref_parc&refer=$refer&parc=$id_parc&miens=$miens&miens_parc=$miens_parc";
                     $lien = urlencode($lien);
                     echo "<TD width='15' height='20' align='middle' valign='top'><A HREF=\"trace.php?link=$lien\" target='main' TITLE=\"$mess_decrement\"><IMG SRC=\"images/modules/tut_form/flechehaut.gif\" TITLE=\"$mess_decrement\" border='0'></TD>";
                  }else
                    echo"<td align='middle'>&nbsp;</td>";
                }
               }
                echo "<td align='left' valign='top'>";
                  // affichage du nom de la séquence
               $req_parc_seq = mysql_query ("SELECT parcours.parcours_nom_lb FROM parcours,sequence_parcours WHERE sequence_parcours.seqparc_seq_no=$id AND sequence_parcours.seqparc_parc_no != $id_parc AND sequence_parcours.seqparc_parc_no=parcours.parcours_cdn GROUP BY parcours.parcours_nom_lb order by parcours.parcours_nom_lb");
               $nb_parc_seq = mysql_num_rows ($req_parc_seq);
               $la_serie = "";
               if ($nb_parc_seq > 0)
               {
                  $psi=0;
                  while ($psi < $nb_parc_seq){
                     $p_nom = mysql_result($req_parc_seq,$psi,"parcours_nom_lb");
                     $la_serie .="<LI>".$p_nom."</LI>";
                     $psi++;
                  }
               }
               $lien="sequence.php?liste=$liste&id_seq=$id&consult_act=1&parcours=$parcours&liste=$liste&id_parc=$id_parc&proprio=$proprio&refer=$refer&id_ref=$id_ref&id_ref_parc=$id_ref_parc&miens=$miens&miens_parc=$miens_parc&refer=$refer&parc=$id_parc&liste_act_seq=1";
               $lien = urlencode($lien);
               if ($nb_act_seq == 0)
                 echo "<B>$nom</B>";
               else{
                 if ($mod_seq == 0)
                 {
                   echo $div;
                   echo "<A HREF=\"trace.php?link=$lien\" target='main' title=\"$msq_list_act\" ";
                 }
                 else
                   echo "$div<A HREF=\"javascript:void(0);\" title=\"$msq_list_act\" ";
                 if ($la_serie != "")
                    echo bulle($la_serie,"$seq_parc_exist","RIGHT","ABOVE",330);
                 else
                    echo ">";
                 echo "$nom</A>&nbsp;&nbsp;&nbsp;&nbsp;<small>".stripslashes($desc_seq)."</small>";
               }
               echo"<BR>";
               if ($id_ref_seq > 0)
                 echo "<SMALL>$msq_ref_format&nbsp;<I>$niv_ref</I>&nbsp;-->&nbsp; ".html_entity_decode($desc_ref,ENT_QUOTES,'ISO-8859-1')."</SMALL></DIV>";
               else
                 echo "<SMALL>".html_entity_decode($desc_ref,ENT_QUOTES,'ISO-8859-1')."</SMALL></DIV>";
             // affichage des autres éléments de la séquence
               if (strstr($mod_seq,"SCORM"))
               {
                  $nb_mod_sco = mysql_result(mysql_query ("select count(*) from scorm_module where mod_seq_no = $id AND (mod_content_type_lb = 'SCORM' OR mod_content_type_lb = 'ASSET' OR mod_content_type_lb = 'AICC_HACP' OR  mod_content_type_lb = 'AICC_API')"),0);
                  echo "</td><td align='middle' valign='top'>$nb_mod_sco</td>";
                  $nb_seq_scorm++;
               }
               else
                  echo "</td><td align='middle' valign='top'>$nb_act_seq</td>";
               if (strstr($mod_seq,"SCORM"))
               {
                  $seq_duree = strval($seq_duree);
                  $duree = duree_calc($seq_duree);
               }
               else
                  $duree = duree_calc($seq_duree);
               echo "<td align='middle' valign='top'>$duree</td>";
               if ($seqduref == 1 && $dureeRefSeq > 0)
                   echo "<td align='middle' valign='top'>".duree_calc($dureeRefSeq)."</td>";
               elseif ($seqduref == 1  && ($dureeRefSeq == '' || $dureeRefSeq == 0))
                   echo "<td align='middle' valign='top'></td>";
               if ($nb_prereq == 0)
                   echo "<td align='middle' valign='top'>$msq_noprereq</td>";
               else
               {
                   $lien="sequence.php?liste=$liste&aff_prereq=1&parcours=$parcours&id_parc=$id_parc&proprio=$proprio&refer=$refer&id_seq=$id&id_ref=$id_ref&id_ref_parc=$id_ref_parc&miens=$miens&miens_parc=$miens_parc&parc=$id_parc";
                   $lien = urlencode($lien);
                   echo "<td align='middle' valign='top'><A HREF=\"trace.php?link=$lien\" ".
                   bulle($msgConsPreq,"","CENTER","ABOVE",150)."$mess_oui</A></td>";
               }
                $lien = "favoris.php?ajouter=1&seq=$id&accede=_entree&dhou=1";
                $lien = urlencode($lien);
                 if ($auteur_parc == $id_user || $id_auteur == $id_user || $typ_user == 'ADMINISTRATEUR')
                   echo  "<TD align='middle' valign='top'><a href=\"#\" ".
                         "onclick=\"window.open('trace.php?link=$lien','','width=680,height=500,resizable=yes,status=no')\" ".
                         bulle(NewHtmlentities($msq_prec_fav),"","LEFT","ABOVE",200).
                         "<IMG SRC=\"images/modules/tut_form/icoresapprenant.gif\" border=0></A></TD>";
                 else
                   echo "<TD align='middle' valign='top'></TD>";
                 $sql = mysql_query ("SELECT * from favoris where fav_seq_no = '$id'");
                 $nbr_fav = mysql_num_rows($sql);
                 if ($nbr_fav > 0)
                 {
                   $lien = "favoris.php?consulter=1&seq=$id";
                   $lien = urlencode($lien);
                   echo  "<TD align='middle' valign='top'><a href=\"#\" ".
                         "onclick=\"window.open('trace.php?link=$lien','','width=680,height=390,resizable=yes,status=no')\" ".
                         bulle(NewHtmlentities($msq_voir_fav),"","LEFT","ABOVE",200).
                         "<IMG SRC=\"images/modules/tut_form/icoacresbiblio.gif\" border=0></a></TD>";
                 }else
                   echo "<TD align='middle' valign='top'></TD>";
                 $parc_type = GetDataField ($connect,"SELECT parcours_type_on from parcours where parcours_cdn = '$id_parc'","parcours_type_on");
                 if ($auteur_parc == $id_user || $id_auteur == $id_user || $typ_user == 'ADMINISTRATEUR')
                 {
                   $lien="sequence.php?toutes_seq=$toutes_seq&liste=$liste&action_seq=1&parcours=$parcours&id_parc=$id_parc&proprio=$proprio&refer=$refer&id_seq=$id&nb_act_seq=$nb_act_seq&duree=$duree_new&ordre_act=$ordre_act&id_ref=$id_ref&id_ref_parc=$id_ref_parc&refer=$refer&droit_voir=$droit_voir&miens=$miens&miens_parc=$miens_parc&parc=$id_parc";
                   $lien = urlencode($lien);
                   if ($id_auteur == $id_user || $typ_user == 'ADMINISTRATEUR')
                      echo "<td align='middle' valign='top'><A HREF=\"trace.php?link=$lien\" target='main'".bulle($mess_modif_seq,"","LEFT","ABOVE",120).
                           "<IMG SRC=\"images/repertoire/icoGrenomfich20.gif\" BORDER=0></A></td>";
                   else
                      echo "<TD>&nbsp;</TD>";
                   $requete_grp = mysql_query ("select * from groupe order by grp_nom_lb");
                   $nb_grp_parc = mysql_num_rows($requete_grp);
                   if ($nb_grp_parc > 0)
                   {
                      $gp=0;
                      while ($gp < $nb_grp_parc)
                      {
                            $id_grp = mysql_result($requete_grp,$gp,"grp_cdn");
                            $nom_grp = mysql_result($requete_grp,$gp,"grp_nom_lb");
                            $nb_presc = mysql_num_rows(mysql_query("select * from prescription_$id_grp where presc_parc_no=$id_parc"));
                            $nb_presc_seq_parc += $nb_presc;
                            $gp++;
                      }
                   }
                   $seq_mod = mysql_query ("select count(seqparc_seq_no) from sequence_parcours where seqparc_seq_no = $id AND seqparc_parc_no != $id_parc");
                   $nb_seq_mod = mysql_result($seq_mod,0);
                   if ($nb_presc_seq_parc == 0 && ($auteur_parc == $id_user || $typ_user == 'ADMINISTRATEUR'))
                   {
                     if ($toutes_seq == 1)
                        $msg_sup = $mpa_supp_seq;
                     else
                        $msg_sup = $msq_etiq_sup;
                     $lien="sequence.php?toutes_seq=$toutes_seq&liste=$liste&consult=1&parcours=$parcours&id_parc=$id_parc&proprio=$proprio&refer=$refer&supp=1&id_seq=$id&id_ref=$id_ref&id_ref_parc=$id_ref_parc&refer=$refer&miens=$miens&miens_parc=$miens_parc&parc=$id_parc";
                     $lien = urlencode($lien);
                     if ($toutes_seq == 1 || ($nb_seq_mod == 0 && $toutes_seq == 0))
                         echo "<td align='middle' valign='top'><A href=\"javascript:void(0);\" ".
                              "onclick=\"javascript:return(confm('trace.php?link=$lien'));\" target='main' ".
                              bulle($msg_sup,"","LEFT","ABOVE",200).
                              "<IMG SRC=\"images/messagerie/icopoubelressour.gif\" BORDER=0></A></td>";
                     else
                         echo "<td align='middle' valign='top'><A HREF=\"javascript:void(0);\" style='cursor:help' ".
                              bulle($msq_nosup_seq,"","LEFT","ABOVE",150).
                              "<IMG SRC=\"images/repertoire/icoptiinterdit.gif\" BORDER=0></td>";
                   }
                   elseif(strstr($mod_seq,"SCORM"))
                      echo "<td align='middle' valign='top'><IMG SRC=\"images/repertoire/icoptiinterdit.gif\" BORDER=0 ".
                      bulle($mess_seq_sco,"","LEFT","ABOVE",150)."</td>";
                   else
                      echo "<td align='middle' valign='top'><A HREF=\"javascript:void(0);\" style='cursor:help' ".
                           bulle($msq_nosup_seq,"","LEFT","ABOVE",150).
                           "<IMG SRC=\"images/repertoire/icoptiinterdit.gif\" BORDER=0></td>";
                 }
                 else
                    echo "<TD>&nbsp;</TD><TD>&nbsp;</TD>";

                 if ($droit_voir_seq == 1)
                 {
                    $lien="sequence.php?toutes_seq=$toutes_seq&liste=$liste&dupli_seq=1&id_seq=$id&parcours=$parcours&id_parc=$id_parc&proprio=$proprio&refer=$refer&id_ref=$id_ref&id_ref_parc=$id_ref_parc&miens=$miens&miens_parc=$miens_parc&parc=$id_parc";
                    $lien = urlencode($lien);
                    echo "<TD  align='middle' valign='top'><A href=\"trace.php?link=$lien\" target='main' ".bulle($msq_dupli_seq,"","LEFT","ABOVE",150).
                      "<IMG SRC=\"images/repertoire/icoptiedit.gif\" BORDER=0></A></TD>";
                 }
                 else
                    echo "<TD>&nbsp;</TD>";

              $i++;
              } //fin while ($i != $nb_seq)
              echo "</TR><TR height ='50'><TD colspan='10'>";
              echo "<TABLE border=0 cellpadding='4' cellspacing = '4'><TR>";
              if (($id_auteur == $id_user || $typ_user == 'ADMINISTRATEUR') && ($toutes_seq != 1 || ($toutes_seq == 1 && $nb_seq == 1))){
                   // debut modif referentiel
                   if ($id_ref_seq == 0)
                   {
                     $lien="referenciel.php?consult=1&modif_seqref=1&id_seq=$id&refer=$refer&id_ref_parc=$id_ref_parc&liste=$liste&id_parc=$id_parc&proprio=$proprio&refer=$refer&miens=$miens&miens_parc=$miens_parc&parc=$id_parc";
                     $lien = urlencode($lien);
                     echo "<TD nowrap>$bouton_gauche<a href=\"trace.php?link=$lien\" target='main'>$mess_sq_ref_mod</a>$bouton_droite</TD>";
                   }else{
                     $lien="sequence.php?liste=1&consult=1&modif_seqref=1&id_ref=0&id_seq=$id&refer=$refer&id_ref_parc=$id_ref_parc&parcours=$parcours&liste=$liste&id_parc=$id_parc&proprio=$proprio&refer=$refer&miens=$miens&miens_parc=$miens_parc&parc=$id_parc";
                     $lien = urlencode($lien);
                     echo "<TD nowrap>$bouton_gauche<a href=\"trace.php?link=$lien\" target='main'>$seq_hrs_ref</a>$bouton_droite</TD>";
                     $lien="referenciel.php?consult=1&modif_seqref=1&id_seq=$id&refer=$refer&id_ref_parc=$id_ref_parc&liste=$liste&id_parc=$id_parc&proprio=$proprio&refer=$refer&miens=$miens&miens_parc=$miens_parc&parc=$id_parc";
                     $lien = urlencode($lien);
                     echo "<TD nowrap>$bouton_gauche<a href=\"trace.php?link=$lien\" target='main'>$msq_go_aut_ref</a>$bouton_droite</TD>";
                   }
                   if (!strstr($mod_seq,"SCORM"))
                   {
                      $lien="export_seq_xml.php?id_seq=$id";
                      echo "<TD nowrap>$bouton_gauche<a href=\"$lien\">Export Scorm de la séquence</a>$bouton_droite</TD>";
                   }
                   if ($nb_act_seq > 0 && strstr($mod_seq,"SCORM")){
                      $launch = mysql_result(requete_order("*","scorm_module"," mod_seq_no = '$id' AND mod_launch_lb != ''","mod_cdn ASC"),0,"mod_launch_lb");
                      if (!strstr($launch,"http://")){
                           $launch = "../".$launch;
                           $supplement = "";
                      }else
                           $supplement = "X";
                      if (strstr($mod_seq,"SCORM")){
                         $lien = "scorm/lancer_sco$supplement.php?id_seq=$id&dou=sequence";
                         $largeur=1015;
                         $hauteur=768;
                      }else{
                         $lien = "vue_sequence.php?seq=$id";
                         $largeur=800;
                         $hauteur=400;
                      }
                      $lien = urlencode($lien);
                      if ($id_ref_seq > 0)
                          echo "</TR><TR>";
                      echo "<TD nowrap>$bouton_gauche<A HREF=\"#\" onclick=\"javascript:window.open('trace.php?link=$lien','','top=0,left=0,width=$largeur,height=$hauteur,resizable=yes,status=no,scrollbars=yes')\">$mess_vue_app</A>$bouton_droite</TD>";
                   }
                   echo "</TR></TABLE>";
              }

              if ($toutes_seq == 1 && $nb_seq_scorm == 0 && ($droit_voir == 1 || ($droit_voir == 0 && ($createur == $id_user || $typ_user == 'ADMINISTRATEUR')))){
/*
                    $id_ref_parc = GetDataField ($connect,"SELECT parcours_referentiel_no from parcours where parcours_cdn = '$id_parc'","parcours_referentiel_no");
                    $lien="parcours.php?liste=$liste&dupli_parc=1&parcours=$parcours&id_parc=$id_parc&proprio=$proprio&refer=$refer&id_ref=$id_ref&id_ref_parc=$id_ref_parc&parc=$id_parc&miens=$miens&miens_parc=$miens_parc";
                    $lien = urlencode($lien);
                    echo "<TD nowrap>$bouton_gauche<a href=\"trace.php?link=$lien\" target='main'>$msq_dupli_parc</a>$bouton_droite</TD>";
*/                  echo "</TR></TABLE>";
              }

          }//fin else ($nb_seq == 0)
          echo "</TD></TR></TABLE>";
          echo fin_tableau($html);

}//fin if($consult == 1)

//Lors de la consultation, on peut modifier,supprimer, ajouter des activités
if ($action_seq == 1)
{

  ?>
  <SCRIPT language=JavaScript>
    function checkForm(frm) {
      var ErrMsg = "<?php echo $mess_info_no;?>\n\n";
      var lenInit = ErrMsg.length;
      if (isEmpty(frm.nom)==true)
        ErrMsg += ' - <?php echo $msq_titre;?>\n';
      if (isEmpty(frm.description)==true)
        ErrMsg += ' - <?php echo $msq_desc;?>\n';
     <?php
       if ($seqduref == 1)
       {
        ?>
          if (isEmpty(frm.dureeRef)== false){
             if (isNaN(frm.dureeRef.value))
                ErrMsg += ' -  La durée référencée n\'est pas valide\n';
          }
         <?php
        }
     ?>
      if (isVide(frm.type_seq)==true)
        ErrMsg += ' - <?php echo $mess_presc_ok;?>\n';
      if (isVide(frm.droit_voir_seq)==true)
        ErrMsg += ' - <?php echo $msq_droit_voir;?>\n';
      if (isVide(frm.ordre_des_act)==true)
        ErrMsg += ' - <?php echo addslashes($msq_act_ord);?>\n';
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
    function isVide(elm) {
               if(!elm[0].checked && !elm[1].checked){
                    return true;
               }
      return false;
    }
  </SCRIPT>
  <?php
    //insertion des pre-requis
    if ($insert_prereq == 1){
       $id_prereq = Donne_ID ($connect,"select max(prereq_cdn) from prerequis");
       $prerequis = mysql_query ("insert into prerequis(prereq_cdn,prereq_seq_no,prereq_typcondition_lb,prereq_seqcondition_no) values ($id_prereq,$id_seq,\"$typ_prereq\",$seq)");
       $upd_act_prereq = mysql_query ("update prerequis set prereq_actcondition_no = $act where prereq_cdn = $id_prereq");
       $upd_note_prereq = mysql_query ("update prerequis set prereq_notemin_nb1 = $note_min,prereq_notemax_nb1 = $note_max where prereq_cdn = $id_prereq");
    }
    //Creer modif supprimer ossi ds activites
    $letitre= $msq_detail_seq;
    entete_concept("liste_parc.inc.php",$letitre);
    echo aide_simple("sequence");
    $id_ref_parc = GetDataField ($connect,"select parcours_referentiel_no from parcours where parcours_cdn = $id_parc","parcours_referentiel_no");
    $requete = requete("*","sequence","seq_cdn = '$id_seq'");
    $data_seq = mysql_fetch_object($requete);
    $nom = $data_seq->seq_titre_lb;
    $type_seq = $data_seq->seq_type_on;
    $mod_seq = $data_seq->seq_type_lb;
    $seq_duree = $data_seq->seq_duree_nb;
    $description = $data_seq->seq_desc_cmt;
    $mots_clef = $data_seq->seq_mots_clef;
    $droit_voir_seq = $data_seq->seq_publique_on;
    $ordre_des_act = $data_seq->seq_ordreact_on;
    if ($seqduref == 1)
    {
       $reqDureeSeq = mysql_query("select seqduref_duree_nb from seq_duree_ref where seqduref_seq_no= $id_seq");
       if (mysql_num_rows($reqDureeSeq) > 0)
          $dureeRefSeq = mysql_result($reqDureeSeq,0,"seqduref_duree_nb");
       else
          $dureeRefSeq = "";
    }
  if ($choix != 1 && $choix_act != 1)
  {
    echo "<FORM name='form1' action=\"sequence.php?liste=$liste&consult=1&modif=1&parcours=$parcours&id_parc=$id_parc&proprio=$proprio&refer=$refer&id_seq=$id_seq&id_ref=$id_ref&id_ref_parc=$id_ref_parc&miens=$miens&miens_parc=$miens_parc&parc=$id_parc\" target='main' method='post'>";
    echo "<TR><TD colspan='2'><TABLE>";
    echo "<TR><TD colspan='2' align=left valign='center' class='sous_titre' style='margin-right:10px;'>$sous_titre";
    if ($type_seq == 1 && $droit_voir_seq == 1)
    {
       echo "<BR>".$mess_seq_type." ".strtolower($mess_exp_type);
       //echo "<INPUT TYPE='HIDDEN' name='droit_voir' value='1'>";
    }
    echo "</TD></TR>";
    $nb_preq = mysql_result(mysql_query("select count(*) from prerequis where prereq_seq_no=$id_seq"),0);
    echo "<TR><TD nowrap valign='top'><B>$msq_titre</B></TD><TD nowrap>";
    echo "<INPUT TYPE='text' class='INPUT' name='nom' size='75' value=\"$nom\" align='middle'>";
    $l_aide = anoter($mess_dupli_presc,"350");
    echo "<TR><TD nowrap width=30% valign='top'><B>$msq_desc</B></TD>";
    echo "<TD nowrap width=35%><TEXTAREA class='TEXTAREA' name='description' align='middle' rows='6' cols='80'>".
         html_entity_decode($description,ENT_QUOTES,'iso-8859-1')."</TEXTAREA><br></TD></TR>";
    $lien="sequence.php?liste=$liste&aff_prereq=1&parcours=$parcours&id_parc=$id_parc&proprio=$proprio&refer=$refer&id_seq=$id_seq&id_ref=$id_ref&id_ref_parc=$id_ref_parc&miens=$miens&miens_parc=$miens_parc&parc=$id_parc";
    $lien = urlencode($lien);
    $lien1="sequence.php?liste=$liste&def_prereq=1&nb_act_seq=$nb_act_seq&acti_seq=$action_seq&id_ref=$id_ref&id_ref_parc=$id_ref_parc&id_seq=$id_seq&miens=$miens&miens_parc=$miens_parc&parc=$id_parc&id_parc=$id_parc&proprio=$proprio&refer=$refer";
    $lien1 = urlencode($lien1);
    echo "<tr><td style=\"font-weight:bold;\">$msgPrpSeq</td>";
    if ($nb_preq == 0)
    {
       echo "<td>$bouton_gauche<A href=\"trace.php?link=$lien1\" target='main'>$msq_def_prereq</A>$bouton_droite</td></tr>";
    }
    else
    {
       echo "<td>$bouton_gauche<A href=\"trace.php?link=$lien\" target='main'>$mess_menu_consult_favori</A>$bouton_droite</td></tr>";
    }
    echo "<input type='hidden' name='mots_clef' value=\"".html_entity_decode($mots_clef,ENT_QUOTES,'iso-8859-1')."\">";
    echo "<TR><TD nowrap><TABLE cellpadding='0' cellspacing='0' border='0'>";
    echo "<TR height='30'><TD><B>$mess_presc_ok</B></TD></TR>";
    echo "<TR height='30'><TD nowrap><B>$msq_droit_voir</B></TD></TR></TABLE></TD>";
    echo "<TD nowrap><TABLE cellpadding='0' cellspacing='0' border='0'><TR height='30'><TD nowrap>";
    if ($type_seq == 1) {
       echo "$mess_oui&nbsp;<INPUT type='radio' name='type_seq' value='0'>&nbsp;&nbsp;&nbsp;&nbsp;</TD><TD>";
       echo "$mess_non&nbsp;<INPUT type='radio' name='type_seq' value='1' checked>&nbsp;</TD><TD rowspan='2' valign='center'>";
    }elseif ($type_seq == 0) {
       echo "$mess_oui&nbsp;<INPUT type='radio' name='type_seq' value='0' checked>&nbsp;&nbsp;&nbsp;&nbsp;</TD><TD>";
       echo "$mess_non&nbsp;<INPUT type='radio' name='type_seq' value='1'>&nbsp;</TD><TD rowspan='2' valign='center'>";
    }
    echo "$l_aide</TD></TR><TR height='30'><TD>";
    if ($droit_voir_seq == 1 || $droit_voir_seq == ''){
       echo "$mess_oui&nbsp;<INPUT type='radio' name='droit_voir_seq' value='1' checked>&nbsp;</TD><TD>";
       echo "$mess_non&nbsp;<INPUT type='radio' name='droit_voir_seq' value='0'>";
    }elseif ($droit_voir_seq == 0) {
       echo "$mess_oui&nbsp;<INPUT type='radio' name='droit_voir_seq' value='1'>&nbsp;</TD><TD>";
       echo "$mess_non&nbsp;<INPUT type='radio' name='droit_voir_seq' value='0' checked>";
    }
    echo "</TD></TR></TABLE></TD></TR>";
    echo "<TR height='25'><TD nowrap><B>$msq_ordre_act</TD><TD nowrap>";
    if ($ordre_des_act == 'OUI') {
            echo "$mess_oui&nbsp;<INPUT type='radio' name='ordre_des_act' value='OUI' checked>&nbsp;&nbsp;&nbsp;&nbsp;";
            echo "$mess_non&nbsp;<INPUT type='radio' name='ordre_des_act' value='NON'>";
    }else{
            echo "$mess_oui&nbsp;<INPUT type='radio' name='ordre_des_act' value='OUI'>&nbsp;&nbsp;&nbsp;&nbsp;";
            echo "$mess_non&nbsp;<INPUT type='radio' name='ordre_des_act' value='NON' checked>";
    }
  }
  elseif ($choix == 1 || $choix_act == 1)
  {          // fin de $choix != 1 provenant de list_parc.inc
    echo "<TR><TD colspan='2' align=left valign='center' class='sous_titre'>$sous_titre";
    if ($type_seq == 1 && $droit_voir_seq == 1)
         echo "<BR><B>".$mess_seq_type." ".strtolower($mess_exp_type)."</B>";
    echo "<BR>&nbsp;</TD></TR><TR><TD colspan='2'><TABLE>";
    echo "<TR><TD valign='top' width='40%'><B>$msq_titre</B></TD><TD valign='top' width='60%'>$nom</TD></TR>";
    echo "<TR><TD valign='top'><B>$mess_desc</B></TD><TD valign='top'>".
         html_entity_decode($description,ENT_QUOTES,'iso-8859-1')."</TD></TR>";
//    echo "<TR><TD valign='top'><B>$mess_motclef</B></TD><TD  valign='top'>$mots_clef</TD></TR>";
    $type_sequ = ($type_seq == 0) ? "OUI" : "NON";
    echo "<TR><TD nowrap valign='top'><B>$mess_presc_ok</B></TD><TD nowrap valign='top'>$type_sequ</TD></TR>";
    $dvs = ($droit_voir_seq == 1) ? "OUI" : "NON";
    echo "<TR><TD nowrap valign='top'><B>$msq_droit_voir</B></TD><TD nowrap valign='top'>$dvs</TD></TR>";
  }

    if (strstr($mod_seq,"SCORM")){
       $seq_duree = strval($seq_duree);
       $duree = duree_calc($seq_duree);
    }else
       $duree = duree_calc($seq_duree);
    echo "</TD></TR><TR height='25'><TD nowrap><B>$msq_duree_seq</B></TD><TD nowrap>$duree";
    if ($seqduref == 1)
    {
       $reqDureeSeq = mysql_query("select seqduref_duree_nb from seq_duree_ref where seqduref_seq_no= $id_seq");
       if (mysql_num_rows($reqDureeSeq) > 0)
          $dureeRefSeq = mysql_result($reqDureeSeq,0,"seqduref_duree_nb");
       else
          $dureeRefSeq = "";
       echo "<TR height='25'><TD nowrap><B>Durée du référentiel en minutes</TD>";
       echo "<TD nowrap><INPUT type='text' name='dureeRef' size='2' value='".$dureeRefSeq."'></TD></TR>";
    }
    if (strstr($mod_seq,"SCORM")){
       $nb_mod_label = mysql_result(mysql_query ("select count(*) from scorm_module where mod_seq_no = $id_seq AND (mod_content_type_lb = 'LABEL' || mod_content_type_lb = 'AICC_LABEL')"),0);
       $nb_mod_sco = mysql_result(mysql_query ("select count(*) from scorm_module where mod_seq_no = $id_seq AND (mod_content_type_lb = 'SCORM' OR mod_content_type_lb = 'ASSET' OR mod_content_type_lb = 'AICC_HACP' OR  mod_content_type_lb = 'AICC_API')"),0);
       echo "</TD></TR><TR height='25'><TD nowrap><B>$msq_nbr_act</B>&nbsp;&nbsp;&nbsp;&nbsp;</TD><TD nowrap>$nb_mod_sco</TD></TR>";
    }else{
       $nb_act_seq = mysql_result(mysql_query ("select count(*) from activite where act_seq_no = $id_seq"),0);
       echo "</TD></TR><TR height='25'><TD nowrap><B>$msq_nbr_act</B>&nbsp;&nbsp;&nbsp;&nbsp;</TD><TD nowrap>$nb_act_seq</TD></TR>";
    }
    if ($choix != 1){
       echo "<TR height='30'><TD>&nbsp;</TD><TD align='left' valign='center'><A HREF=\"javascript:checkForm(document.form1);\" onClick=\"TinyMCE.prototype.triggerSave();\" onmouseover=\"img1.src='images/fiche_identite/boutvalidb$suffixer.gif';return true;\" onmouseout=\"img1.src='images/fiche_identite/boutvalid$suffixer.gif'\">".
            "<IMG NAME=\"img1\" SRC=\"images/fiche_identite/boutvalid$suffixer.gif\" BORDER='0' onLoad=\"tempImg=new Image(0,0); tempImg.src='images/fiche_identite/boutvalidb$suffixer.gif'\"></A>";
       echo "</TD></TR></FORM>";
    }
    echo "<TR width=100%><TD width=100% colspan='2'>&nbsp;</TD></TR>";

    if (!strstr($mod_seq,"SCORM"))
    {
       echo "<TR><TD colspan='3' align='middle' width='100%'>".
            "<div style='clear:both;float:left;background-color:#F4F4F4;padding:20px;border:1px solid #298CA0;margin-left:50px;'>";
       $lien="sequence.php?liste=$liste&id_seq=$id_seq&parcours=$parcours&id_parc=$id_parc&proprio=$proprio&refer=$refer&activite=1&id_ref=$id_ref&miens=$miens&miens_parc=$miens_parc&parc=$id_parc&id_ref_parc=$id_ref_parc&liste_act_seq=1";
       $lien = urlencode($lien);
       echo "<div style='clear:both;float:left;'>".
            "<A href=\"trace.php?link=$lien\" target='main'".
            " onmouseover=\"img110.src='images/modules/icoajoutsequenb.gif';return true;\"".
            " onmouseout=\"img110.src='images/modules/icoajoutsequen.gif'\">".
            "<IMG NAME=\"img110\" SRC=\"images/modules/icoajoutsequen.gif\"  border='0' width='30' height='30'".
            " onLoad=\"tempImg=new Image(0,0); tempImg.src='images/modules/icoajoutsequenb.gif'\"><BR>$msq_crea_act_seq</A></div>";
       $nb_stars = mysql_num_rows(mysql_query("select * from stars where star_user_id='$id_user' and star_type_no=3"));
       if ($nb_stars > 0)
       {
          $lien_star = "sequence.php?rechercher=1&choix_act=1&star=1&medor=1".getenv("QUERY_STRING");
          $lien_star = urlencode($lien_star);
       }
       if ($rechercher == 1 || $medor == 1)
       {
          if ($nb_stars > 0 && $etat_fav == 'OUI' && $star !=1)
          {
              echo "<div style='float:left; margin-left:30px;'>";
              echo "<a href=\"trace.php?link=$lien_star\" target='main'".
                    bulle($mess_menu_consult_fav_prop,"","RIGHT","BELOW",180).
                   " <img src='images/starfull.gif' border=0><br /><br />$mess_ag_choix $msgseq_mark</a></div>";
           }
           echo "<div style='float:left; margin-left:30px;'><IMG SRC='images/modules/icorechsequenb.gif' border='0'><br />".
                "<FONT color='#D45211'><B>$msq_choix_act</B></FONT></div>";

       }
       else
       {
          if ($nb_stars > 0 && $etat_fav == 'OUI')
          {
              echo "<div style='float:left; margin-left:30px;'><a href=\"trace.php?link=$lien_star\" target='main'".
                    bulle($mess_menu_consult_fav_prop,"","RIGHT","BELOW",180).
                   " <img src='images/starfull.gif' border=0><br /><br />$mess_ag_choix $msgseq_mark</a></div>";
           }
          $lien="sequence.php?rechercher=1&choix_act=1".getenv("QUERY_STRING");
          $lien = urlencode($lien);
           echo "<div style='float:left; margin-left:30px;'><A href=\"trace.php?link=$lien\" target='main'".
              " onmouseover=\"img111.src='images/modules/icorechsequenb.gif';return true;\"".
              " onmouseout=\"img111.src='images/modules/icorechsequen.gif'\">".
              "<IMG NAME=\"img111\" SRC=\"images/modules/icorechsequen.gif\"  border='0' width='30' height='30'".
              " onLoad=\"tempImg=new Image(0,0); tempImg.src='images/modules/icorechsequenb.gif'\">".
              "<br />$msq_choix_act</A></div>";
       }
       echo "</div></TD></TR></TABLE>";
       if ($rechercher == 1)
       {
          ?>
          <SCRIPT language=JavaScript>
           function check1Form(frm) {
                    var ErrMsg = "<?php echo $mess_info_no;?>\n\n";
                    var lenInit = ErrMsg.length;
                    if (isEmpty(frm.id_act)== true)
                        ErrMsg += ' - <?php echo $msq_choix_activ;?>\n';
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
         echo $afficher_choix;
       }
    }
    echo "</TD></TR></TABLE>";
    echo fin_tableau($html);
}
//Accès aux activités a partir du nom de la séquence
if ($consult_act == 1)
{

     if (isset($chaine_act))
     {
       $id_act = $forum_act;
       unset($_SESSION['chaine_act']);
       unset($_SESSION['forum_act']);
     }
    if ($dupli_act == 1)
       ActiviteDupli($act_a_dupli);

     if ($modif == 1 && !strstr($mod_seq,"SCORM"))
     {
        if (isset($modif_ressource) && $modif_ressource == 1)
        {
           $letitre= $mess_ass_act;
           entete_concept("liste_parc.inc.php",$letitre);
           echo aide_simple("sequence");
           echo "<TR><TD><TABLE border=0 cellpadding='4' cellspacing = '4'>";
          // ressource sujet forum
           $titre = GetDataField ($connect,"select act_nom_lb from activite where act_cdn=$id_act","act_nom_lb");
           $chaine_act="&parcours=$parcours&liste=$liste&id_parc=$id_parc&id_act=$id_act&id_seq=$id_seq&proprio=$proprio&refer=$refer&id_ref=$id_ref&id_ref_parc=$id_ref_parc&miens=$miens&miens_parc=$miens_parc&parc=$id_parc&gauche=$gauche&liste_act_seq=1";
           $params = str_replace("&","|",$chaine_act);
           $forum_act  = $id_act;
           $_SESSION['forum_act'] = $forum_act;
           $_SESSION['chaine_act'] = $chaine_act;
           $lien="sequence.php?id_seq=$id_seq&consult_act=1&encreation=1&parcours=$parcours&liste=$liste&id_parc=$id_parc&proprio=$proprio&refer=$refer&id_ref=$id_ref&id_ref_parc=$id_ref_parc&miens=$miens&miens_parc=$miens_parc&parc=$id_parc&gauche=1&liste_act_seq=1&liste_seq=$liste_seq";
           $lien = urlencode($lien);
           echo "<TR height='40'><TD align='left' nowrap>$bouton_gauche<A href=\"trace.php?link=$lien\" target='main'>$mess_plustard</A>$bouton_droite</TD></TR>";
           echo "<TR height='40'><TD valign='top'><Font size='2'><B>$mess_assress</B></TD></TR>";//$msq_ass_ress_form$mess_assres_comp
           $lien="recherche.php?acces=vient_de_seq&id_act=$id_act&parcours=$parcours&liste=$liste&id_parc=$id_parc&proprio=$proprio&refer=$refer&id_ref=$id_ref&id_ref_parc=$id_ref_parc&modif_act=1&id_seq=$id_seq&miens=$miens&miens_parc=$miens_parc&parc=$id_parc&liste_act_seq=1&liste_seq=$liste_seq";
           $lien = urlencode($lien);
           echo "<TR><TD align='left' nowrap valign='top'>$bouton_gauche<A href=\"trace.php?link=$lien\" target='main'>$mess_ressidx</A>$bouton_droite</TD><TD valign='top'>$mess_goidx</TD></TR>";
           //echo "<TR height='40'><TD colspan='2'><Font size='2'><B>$mess_ajt_resdirect</B></TD></TR>";
           //echo "<TR height='40'><TD colspan='2'><Font size='2'>$mess_ajt_complement</TD></TR>";
           $lien="charge_ressource_vrac.php?id_act=$id_act&dou=&params=$params&charger_fichier=1";
           $lien = urlencode($lien);
           echo "<TR height='40'><TD align='left' nowrap valign='top'>$bouton_gauche";
           echo "<A HREF=\"#\" title =\"$mess_ajt_ress_act\" onClick=\"window.open('trace.php?link=$lien','','scrollbars=yes,resizable=yes,width=500,height=300,left=250,top=300')\">".
                "$mess_ajt_ress_act</A>$bouton_droite</TD><TD>$mess_ajt_complement</TD></TR>";
           $lien="charge_ressource_vrac.php?id_act=$id_act&dou=&params=$params&charger_url=1";
           $lien = urlencode($lien);
           echo "<TR height='40'><TD align='left' nowrap colspan='2'>$bouton_gauche";
           echo "<A HREF=\"#\" title =\"$mess_ajt_url_act\" onClick=\"window.open('trace.php?link=$lien','','scrollbars=yes,resizable=yes,width=500,height=300,left=250,top=300')\">$mess_ajt_url_act</A>$bouton_droite</TD></TR>";
           //quizz à créer
           $lien="creation_qcm.php?creation_qcm=1&params_qcm=$params&id_activit=$id_act&venu=act&acced=sequence";
           $lien = urlencode($lien);
           /*
           echo "<TR height='30'><TD colspan='2' valign='top'><Font size='2'>&nbsp;</TD></TR>";
           echo "<TR><TD align='left' nowrap valign='top'>$bouton_gauche<A href=\"javascript:void(0);\"".
                " onClick=\"window.open('trace.php?link=$lien','','scrollbars=no,resizable=no')\">".
                "$mess_ptiqcm</A>$bouton_droite</TD><TD valign='top'>$mess_alrt_qcm</TD></TR>";
           */
           // lien vers in fil de discussion d'un forum
           $lien="forum/index.php?f=0&collapse=1&arrive=sequence";
           $lien = urlencode($lien);
           echo "<TR><TD align='left' nowrap valign='top'>$bouton_gauche<A href=\"trace.php?link=$lien\" target='main'>$mess_fld</A>$bouton_droite</TD><TD valign='top'>$mess_fldplus</TD></TR>";//$msq_ass_ress_forum
           $lien="sequence.php?liste=$liste&modif_act=1&id_act=$id_act&id_seq=$id_seq&choix_ress=1&parcours=$parcours&id_parc=$id_parc&proprio=$proprio&refer=$refer&action_act=1&id_ref=$id_ref&id_ref_parc=$id_ref_parc&id_ress=$id_ress&miens=$miens&miens_parc=$miens_parc&parc=$id_parc";
           $lien = urlencode($lien);
           echo "<TR height='15'><TD colspan='2'>&nbsp;</TD></TR><TR><TD align='left' nowrap valign='top'>".
                "$bouton_gauche<A href=\"trace.php?link=$lien\" target='main'>Retour</A>$bouton_droite</TD></TR>";//retour
           echo "</TABLE>";
           $html = "";
           echo fin_tableau($html);
         exit();
        }
        if ($id_ress > 0)
        {
           $req_ress = requete("*","ressource_new","ress_cdn = '$id_ress'");
           $data_ress = mysql_fetch_object($req_ress);
           $url_ress = $data_ress->ress_url_lb;
           $titre_ress = $data_ress->ress_titre;
           $desc_ress = $data_ress->ress_desc_cmt;
        }
        if ($forum == 1 || strstr($url_ress,"forum/read.php"))
        {
          $verif_sql = mysql_query("SELECT ress_cdn FROM ressource_new WHERE ress_cat_lb=\"Forums\" AND ress_cdn = ress_typress_no");
          $nbr_f = mysql_num_rows($verif_sql);
          if ($nbr_f == 0)
          {
            $new_catid = Donne_ID ($connect,"select max(ress_cdn) from ressource_new");
            $sql_insere= mysql_query("INSERT INTO ressource_new (ress_cdn,ress_cat_lb,ress_typress_no,ress_url_lb,ress_auteurs_cmt,ress_publique_on,ress_titre,ress_desc_cmt,ress_create_dt,ress_ajout,ress_public_no,ress_type,ress_support,ress_niveau) VALUES ('$new_catid',\"Forums\",'$new_catid','','','OUI','','',\"$date_dujour\",'foad','TOUT','','1','1')");
          }
          else
            $new_catid = GetDataField($connect,"SELECT ress_cdn FROM ressource_new WHERE ress_cat_lb=\"Forums\" AND ress_cdn = ress_typress_no","ress_cdn");
          if ($forum == 1)
          {
            $lien_act = str_replace("|","&",$lien_act);
            $lien_act = str_replace("!","=",$lien_act);
            $le_lien = str_replace("&arrive=sequence","","$adresse_http/forum/read.php?$lien_act");
            $list_item = explode("&",$lien_act);
            $item1 = explode("=",$list_item[0]);
            $num_forum = $item1[1];
            $nom_forum = GetDataField ($connect,"select name from forums where id='$num_forum'","name");
//@forum
            if (isset($titre_activite) && $titre_activite != '' && !strstr($titre_activite,$msq_ass_ress_forum))
               $titre_forum = $titre_activite."  (".$msq_ass_ress_forum." ".$nom_forum.")";
            else
               $titre_forum = $msq_ass_ress_forum." ".$nom_forum;
            $desc_forum = $mess_desc_forum;
            $verif_exist = GetDataField($connect,"SELECT act_ress_no FROM activite WHERE act_cdn = $id_act","act_ress_no");
            if ($verif_exist > 0){
              $verif_other =mysql_query("SELECT count(act_ress_no) FROM activite WHERE act_ress_no = $verif_exist");
              $nb_verif_other = mysql_result ($verif_other,0);
              if ($nb_verif_other == 1 && strstr($url_ress,"forum/read.php"))
              {
                $id_ress =$verif_exist;
                $sql_update= mysql_query("UPDATE ressource_new SET ress_modif_dt=\"$date_dujour\",ress_url_lb=\"$le_lien\" WHERE ress_cdn = $verif_exist");
              }
              else
              {
                $id_ress = Donne_ID ($connect,"select max(ress_cdn) from ressource_new");
                $sql_insere= mysql_query("INSERT INTO ressource_new (ress_cdn,ress_cat_lb,ress_typress_no,ress_url_lb,ress_auteurs_cmt,ress_publique_on,ress_titre,ress_desc_cmt,ress_create_dt,ress_ajout,ress_type,ress_support,ress_niveau) VALUES ('$id_ress',\"Forums\",$new_catid,\"$le_lien\",\"forum\",\"NON\",\"$titre_forum\",\"$desc_forum\",\"$date_dujour\",\"$login\",\"ACQUISITION\",\"Web\",\"1\")");
              }
            }
            else
            {
              $id_ress = Donne_ID ($connect,"select max(ress_cdn) from ressource_new");
              $sql_insere= mysql_query("INSERT INTO ressource_new (ress_cdn,ress_cat_lb,ress_typress_no,ress_url_lb,ress_auteurs_cmt,ress_publique_on,ress_titre,ress_desc_cmt,ress_create_dt,ress_ajout,ress_type,ress_support,ress_niveau) VALUES ('$id_ress',\"Forums\",$new_catid,\"$le_lien\",\"forum\",\"NON\",\"$titre_forum\",\"$desc_forum\",\"$date_dujour\",\"$login\",\"ACQUISITION\",\"Web\",\"1\")");
            }
          }
          elseif (!isset($forum) && strstr($url_ress,"forum/read.php") && $arrive == "sequence")
          {
              $id_ress = Donne_ID ($connect,"select max(ress_cdn) from ressource_new");
              $sql_insere= mysql_query("INSERT INTO ressource_new (ress_cdn,ress_cat_lb,ress_typress_no,ress_url_lb,ress_auteurs_cmt,ress_publique_on,ress_titre,ress_desc_cmt,,ress_create_dt,ress_ajout,ress_type,ress_support,ress_niveau) VALUES ('$id_ress',\"Forums\",$new_catid,\"$url_ress\",\"forum\",\"NON\",\"$titre_ress\",\"$desc_ress\",\"$date_dujour\",\"$login\",\"ACQUISITION\",\"Web\",\"1\")");
          }
          $act_query = mysql_query("SELECT * FROM activite WHERE act_cdn=$id_act");
          $auteur = mysql_result ($act_query,0,"act_auteur_no");
          $duree = mysql_result ($act_query,0,"act_duree_nb");
          $ordre = mysql_result ($act_query,0,"act_ordre_nb");
          if ($forum == 1)
          {
          //@forum
            $nom = mysql_result ($act_query,0,"act_nom_lb");
            $consigne = html_entity_decode(mysql_result ($act_query,0,"act_consigne_cmt"),ENT_QUOTES,'iso-8859-1');
            $commentaire = html_entity_decode(mysql_result ($act_query,0,"act_commentaire_cmt"),ENT_QUOTES,'iso-8859-1');
            $ress_norok = mysql_result ($act_query,0,"act_ress_on");
            $flag = mysql_result ($act_query,0,"act_flag_on");
            $pass_mult = mysql_result ($act_query,0,"act_passagemult_on");
            $acquit = mysql_result ($act_query,0,"act_acquittement_lb");
            $notation = mysql_result ($act_query,0,"act_notation_on");
            $droit_voir_act = mysql_result ($act_query,0,"act_publique_on");
            $dev_a_rendre = mysql_result ($act_query,0,"act_devoirarendre_on");
            $id_seq = GetDataField ($connect,"SELECT act_seq_no FROM activite WHERE act_cdn = $id_act","act_seq_no");
            $url_ress = GetDataField($connect,"SELECT ress_url_lb FROM ressource_new WHERE ress_cdn=$id_ress","ress_url_lb");
          }
          elseif(!isset($forum)  && !strstr($url_ress,"forum/read.php"))
            $nom = mysql_result ($act_query,0,"act_nom_lb");

        }// fin de if forum .....
        if ($supp_ress == 'on' && strstr($url_ress,"forum/read.php"))
           $requete = mysql_query("DELETE FROM ressource_new WHERE ress_cdn =$id_ress");
        if ($id_ress > 0 && $ress_norok == 'NON' && !isset($supp_ress))
           $ress_norok = 'OUI';
        if (($supp_ress == 'on' && !strstr($url_ress,"forum/read.php")) || $ress_norok == "NON")
        {
           $flag = 1;
           $id_ress = 0;
        }elseif ($id_ress == 0 && $ress_norok == "OUI")
           $flag = 0;
        elseif ($id_ress > 0)
           $flag = 1;
        $requete_grp = mysql_query ("select * from groupe order by grp_nom_lb");
        $nb_grp = mysql_num_rows($requete_grp);
        if ($nb_grp > 0)
        {
            $gp=0;
            while ($gp < $nb_grp)
            {
              $id_grp = mysql_result($requete_grp,$gp,"grp_cdn");
              $nbsuivis = mysql_num_rows(mysql_query("select * from suivi1_$id_grp where suivi_act_no =$id_act"));
              if ($nbsuivis > 0)
              {
                 if ((($supp_ress == 'on' && $flag == 1) || $id_ress == 0) && $ress_norok == "NON")
                 {
                       $id_ress = 0;
                       $changer_suivi = mysql_query ("update suivi1_$id_grp set suivi_etat_lb = 'PRESENTIEL' where suivi_act_no =$id_act AND suivi_etat_lb != 'TERRMINE'");
                 }
                 elseif ($supp_ress == 'on' && $flag == 1 && $ress_norok == "OUI")
                 {
                        $id_ress = 0;
                        $flag = 0;
                        $changer_suivi = mysql_query ("update suivi1_$id_grp set suivi_etat_lb = 'A FAIRE' where suivi_act_no =$id_act AND suivi_etat_lb != 'ATTENTE' AND suivi_etat_lb != 'TERMINE'");
                 }
                 elseif ($forum == 1 || strstr($url_ress,"forum/read.php"))
                 {
                         $pass_mult = "OUI";
                         $acquit = "APPRENANT";
                         $dev_a_rendre = "NON";
                         $notation = "NON";
                         //@forum
                         if (isset($titre_activite) && $titre_activite != '' && !strstr($titre_activite,$msq_ass_ress_forum))
                             $titre_forum = $titre_activite."  (".$msq_ass_ress_forum." ".$nom_forum.")";
                         else
                             $titre_forum = $msq_ass_ress_forum." ".$nom_forum;
                         //           $nom = $titre_forum;
                         $flag = 1;
                         $changer_suivi = mysql_query ("update suivi1_$id_grp set suivi_etat_lb = 'A FAIRE' where suivi_act_no =$id_act AND suivi_etat_lb != 'ATTENTE' AND suivi_etat_lb != 'TERMINE'");
                 }
              }
             $gp++;
            }
        }
        if ($forum != 1)
          $duree = ($horaire*60) + $minutage;
        // teste la pertinence des composants de l'activité et renvoi au besoin au formulaire en concervant les données
        $lien="sequence.php?liste=$liste&modif_act=1&id_act=$id_act&id_seq=$id_seq&choix_ress=1&parcours=$parcours&id_parc=$id_parc&proprio=$proprio&refer=$refer&action_act=1&id_ref=$id_ref&id_ref_parc=$id_ref_parc&id_ress=$id_ress&miens=$miens&miens_parc=$miens_parc&parc=$id_parc";
        $lien=urlencode($lien);
        $renvoi =  "<DIV id='sequence'><A href=\"trace.php?link=$lien\" target='main'>$mess_form_retour</A></DIV>";
        if ($acquit != "RESSOURCE" && ($pass_mult == "NON" || $pass_mult == "OUI") && $notation== "OUI" && $dev_a_rendre == "NON" && ($id_ress > 0 || $ress_norok == 'OUI'))
           $acquit = "FORMATEUR_REFERENT";
/*
        if ($acquit == "RESSOURCE" && ($id_ress > 0 || $ress_norok == 'OUI')){
           $pass_mult = "NON";
           $dev_a_rendre = "NON";
           $notation = "OUI";
        }
*/
        if ($acquit == "RESSOURCE" && $ress_norok == 'NON')
           $acquit = "FORMATEUR_REFERENT";
        if ($notation == "OUI" && $dev_a_rendre == "OUI" && $acquit != "FORMATEUR_REFERENT" && $id_ress > 0 && $ress_norok == 'OUI')
           $acquit = "FORMATEUR_REFERENT";
        //on intervertit l'ordre des activites
        //on verifie que le nvel ordre n'est pas sup au nb d'activites
        $nb_act_query = mysql_query ("select count(act_cdn) from activite where act_seq_no = $id_seq");
        $nb_act = mysql_result ($nb_act_query,0);
        if ($ordre < $nb_act || $ordre == $nb_act)
        {
            $actinv = GetDataField ($connect,"select act_cdn from activite where act_ordre_nb = $ordre and act_seq_no = $id_seq","act_cdn");
            if (isset($ordre1))
               $upd_actinv = mysql_query ("update activite set act_ordre_nb = '$ordre1',act_modif_dt = \"$date_dujour\" where act_cdn = $actinv");
            $modifie_rss = rss :: modifie('activite',$id_user,$actinv);
            //Mise a jour de l'activite en cours
            $auteur = GetDataField ($connect,"select act_auteur_no from activite where act_cdn = $id_act","act_auteur_no");
            //dey Dfoad
               $req_typdev = mysql_result(mysql_query("select count(*) from activite_devoir where actdev_act_no = $id_act"),0);
               if ($req_typdev > 0)
               {
                   $req_devoir = mysql_query("update activite_devoir set actdev_dev_lb=\"$typdev\" where actdev_act_no='$id_act'");
               }
               else
                 $req_devoir = mysql_query("insert into activite_devoir values('','$id_act',\"$typdev\")");
            $upd_act = mysql_query ("update activite set act_nom_lb=\"".str_replace("\"","'",$nom).
                                    "\",act_ordre_nb = '$ordre',act_consigne_cmt=\"".
                                    htmlentities($consigne,ENT_QUOTES,'iso-8859-1')."\",act_commentaire_cmt=\"".
                                    htmlentities($commentaire,ENT_QUOTES,'iso-8859-1').
                                    "\",act_ress_on ='$ress_norok',act_ress_no ='$id_ress',act_duree_nb='$duree',".
                                    "act_passagemult_on='$pass_mult',act_acquittement_lb='$acquit',".
                                    "act_notation_on='$notation',act_devoirarendre_on='$dev_a_rendre',".
                                    "act_publique_on='$droit_voir_act',act_auteur_no='$auteur',".
                                    "act_modif_dt = \"$date_dujour\",act_flag_on='$flag' where act_cdn = '$id_act'");
            $modifie_rss = rss :: modifie('activite',$id_user,$id_act);
        }
        else
            echo $msq_bad_ordre;
        if ($id_ress != 0)
        {
           $requete_grp = mysql_query ("select * from groupe order by grp_nom_lb");
           $nb_grp = mysql_num_rows($requete_grp);
           if ($nb_grp > 0)
           {
               $gp=0;
               while ($gp < $nb_grp)
               {
                 $id_grp = mysql_result($requete_grp,$gp,"grp_cdn");
                 $requete = mysql_query ("update suivi1_$id_grp set suivi_etat_lb='A FAIRE' where suivi_etat_lb = 'PRESENTIEL' and suivi_act_no = $id_act");
               $gp++;
               }
           }
        }
    }
    elseif ($modif == 1 && strstr($mod_seq,"SCORM"))
    {
       if  ($prereq == -1)
           $prereq = "";
       $h_T = floor(intval($maxtime)/60);
       $mn_T = intval($maxtime)%60;
       if ($mn_T < 10) $ajoutmn = "0";
       if ($h_T < 10) $ajout = "000";elseif ($h_T < 100) $ajout = "00";elseif ($h_T < 1000) $ajout = "0";
       $max_T = $ajout.$h_T.":".$ajoutmn.$mn_T.":00.00";
       $duree = ($horaire*60) + $minutage;
       if ($link == 'rien')
          $upd_act = mysql_query ("update scorm_module set mod_titre_lb=\"".str_replace("\"","'",$nom)."\",mod_desc_cmt=\"".
                                  htmlentities($description,ENT_QUOTES,'iso-8859-1').
                                  "\",mod_consigne_cmt=\"".htmlentities($consigne,ENT_QUOTES,'iso-8859-1').
                                  "\",mod_motclef_lb=\"".htmlentities($motclef,ENT_QUOTES,'iso-8859-1')."\" where mod_cdn = '$id_act'");
       else
          $upd_act = mysql_query ("update scorm_module set mod_titre_lb=\"".str_replace("\"","'",$nom)."\",mod_desc_cmt=\"".
                                  htmlentities($description,ENT_QUOTES,'iso-8859-1').
                                 "\",mod_consigne_cmt=\"".htmlentities($consigne,ENT_QUOTES,'iso-8859-1').
                                  "\",mod_motclef_lb=\"".htmlentities($motclef,ENT_QUOTES,'iso-8859-1').
                                  "\",mod_duree_nb='$duree',mod_maxtimeallowed=\"$max_T\",mod_masteryscore='$score_min',".
                                  "mod_visible=\"$visible\",mod_prereq_lb = \"$prereq\" where mod_cdn = '$id_act'");
    }// if (modif == 1)
    if  ($monter == 1 || $descendre == 1)
    {
          if ($monter == 1)
            $nouvel_ordre = GetDataField ($connect,"select act_ordre_nb from activite where act_cdn=$act_precedente and act_seq_no=$id_seq","act_ordre_nb");
          else
            $nouvel_ordre = GetDataField ($connect,"select act_ordre_nb from activite where act_cdn =$act_suivante and act_seq_no=$id_seq","act_ordre_nb");
          $ancien_ordre = GetDataField ($connect,"select act_ordre_nb from activite where act_cdn =$act_actuelle and act_seq_no=$id_seq","act_ordre_nb");
          $ancien_act = GetDataField ($connect,"select act_cdn from activite where act_ordre_nb = $nouvel_ordre and act_seq_no = $id_seq","act_cdn");
          $modif_ordre = mysql_query("UPDATE activite SET act_ordre_nb = $nouvel_ordre,act_modif_dt = \"$date_dujour\" where act_cdn = $act_actuelle and act_seq_no=$id_seq");
          $modifie_rss = rss :: modifie('activite',$id_user,$act_actuelle);
          $modif_ordre = mysql_query("UPDATE activite SET act_ordre_nb = $ancien_ordre,act_modif_dt = \"$date_dujour\" where act_cdn = $ancien_act");
          $modifie_rss = rss :: modifie('activite',$id_user,$ancien_act);
    }
    if ($supp == 1)
    {
       //il faut supprimer la duree de l'act a la duree de la seq
       $requete_grp = mysql_query ("select * from groupe order by grp_nom_lb");
       $nb_grp = mysql_num_rows($requete_grp);
       if ($nb_grp > 0)
       {
            $gp=0;$Nb_act_suivi=0;
            while ($gp < $nb_grp)
            {
                 $id_grp = mysql_result($requete_grp,$gp,"grp_cdn");
                 $reqSuivi = mysql_query("select * from suivi1_$id_grp where suivi_act_no = $id_act and (suivi_etat_lb != 'A FAIRE' or (suivi_etat_lb = 'A FAIRE' and suivi_commentaire_cmt != 'Activité repassé')");
                 $nomb = ($reqSuivi == TRUE) ? mysql_num_rows($reqSuivi): 0;
                 $Nb_act_suivi += $nomb;
             $gp++;
            }
       }
      if ($Nb_act_suivi == 0)
      {
        $duree_act = GetDataField ($connect,"select act_duree_nb from activite where act_cdn=$id_act","act_duree_nb");
        $duree_seq = GetDataField ($connect,"select seq_duree_nb from sequence where seq_cdn = $id_seq","seq_duree_nb");
        $duree_seq-=$duree_act;
        $upd_dureeseq = mysql_query ("update sequence set seq_duree_nb = $duree_seq, seq_modif_dt = \"$date_dujour\" where seq_cdn = $id_seq");
        $modifie_rss = rss :: modifie('sequence',$id_user,$id_seq);
        $supp_star = mysql_query("delete from stars where star_item_id = '$id_act' and star_type_no='3'");
        //dey Dfoad
           $act_media = mysql_query("delete from activite_media where actmedia_act_no = $id_act");
           $act_devoir = mysql_query("delete from activite_devoir where actdev_act_no = $id_act");

        $del_act = mysql_query ("delete from activite where act_cdn = $id_act");
        $supp_rss = rss :: supprime('activite',$id_act);
        $del_req = mysql_query ("delete from prerequis where prereq_actcondition_no = $id_act");
        $requete_grp = mysql_query ("select * from groupe");
        $nb_grp = mysql_num_rows($requete_grp);
        if ($nb_grp > 0)
        {
            $gp=0;
            while ($gp < $nb_grp)
            {
                 $id_grp = mysql_result($requete_grp,$gp,"grp_cdn");
                 $del_sui = mysql_query ("delete from suivi1_$id_grp where suivi_act_no = $id_act");
             $gp++;
            }
        }
        // remet les compteurs d'activités dans l'ordre
        $sql = mysql_query("SELECT act_cdn,act_ordre_nb,act_duree_nb FROM activite WHERE act_seq_no = $id_seq ORDER BY act_ordre_nb ASC");
        $res = mysql_num_rows($sql);
        $x = 0;
        while ($x < $res)
        {
          $ordre_act = mysql_result($sql,$x,"act_cdn");
          $duree_act = mysql_result($sql,$x,"act_duree_nb");
          $duree_tot+=$duree_act;
          $y = $x+1;
          $nouvel_ordre = $y;
          $correction = mysql_query("UPDATE activite SET act_ordre_nb = $nouvel_ordre,act_modif_dt = \"$date_dujour\" where act_cdn = $ordre_act");
          $modifie_rss = rss :: modifie('activite',$id_user,$ordre_act);
         $x++;
        }
        $duree_seq = mysql_query ("UPDATE sequence set seq_duree_nb = $duree_tot,seq_modif_dt = \"$date_dujour\" where seq_cdn = $id_seq");
        $modifie_rss = rss :: modifie('sequence',$id_user,$id_seq);
        $requete_grp = mysql_query ("select * from groupe order by grp_nom_lb");
        $nb_grp = mysql_num_rows($requete_grp);
        if ($nb_grp > 0)
        {
            $gp=0;
            while ($gp < $nb_grp)
            {
                 $id_grp = mysql_result($requete_grp,$gp,"grp_cdn");
                 $duree_seq_suiv = mysql_query ("UPDATE suivi2_$id_grp set suiv2_duree_nb = '$duree_tot' where suiv2_seq_no = '$id_seq'");
             $gp++;
            }
        }
       }
       else
         echo $msq_act_no_supp;
      // fin if ($Nb_act_suivi == 0)
    }// if ($supp ==1)
// fin supprime activité

    $mod_seq = GetDataField ($connect,"select seq_type_lb from sequence where seq_cdn = '$id_seq'","seq_type_lb");
    if ($mod_seq == 'NORMAL')
       $act_query = mysql_query ("select * from activite where act_seq_no = $id_seq order by act_ordre_nb");
    else
       $act_query = mysql_query ("select * from scorm_module where mod_seq_no = $id_seq order by mod_cdn");
    $nb_act_seq = mysql_num_rows ($act_query);
    if ($mod_seq != 'NORMAL')
    {
         $id_ref_parc = GetDataField ($connect,"select parcours_referentiel_no from parcours where parcours_cdn = $id_parc","parcours_referentiel_no");
         $id_auteur = GetDataField ($connect,"select seq_auteur_no from sequence where seq_cdn = '$id_seq'","seq_auteur_no");
         $titre_sequence = GetDataField ($connect,"select seq_titre_lb from sequence where seq_cdn = '$id_seq'","seq_titre_lb");
         $type_sequence = GetDataField ($connect,"select seq_type_lb from sequence where seq_cdn = '$id_seq'","seq_type_lb");
         $nom_sequence = GetDataField ($connect,"select seq_desc_cmt from sequence where seq_cdn = '$id_seq'","seq_desc_cmt");
         $droit_dupli_seq = GetDataField ($connect,"select seq_publique_on from sequence where seq_cdn = '$id_seq'","seq_publique_on");
         $id_ref_seq = GetDataField ($connect,"select seqref_referentiel_no from sequence_referentiel where seqref_seq_no = '$id_seq'","seqref_referentiel_no");
         if ($nom_sequence == '')
            $nom_sequence = $msq_no_decrite;
         $bgcolor2 = '#2b677a';
         $bgcolor1 = '#F8F2E4';
         $letitre ="$msq_seq : $titre_sequence";
         entete_concept("liste_parc.inc.php",$letitre);
         echo aide_simple("sequence");
         $id_ref_parc = GetDataField ($connect,"select parcours_referentiel_no from parcours where parcours_cdn = $id_parc","parcours_referentiel_no");
         if ($sous_titre != '')
            echo "<TR><TD colspan='2' class='sous_titre'>$sous_titre</TD></TR>";
         echo "<TR><TD width='100%' colspan='2' ><TABLE cellspacing='1' cellpadding='5' width='100%' bgColor='#FFFFFF'>";
         echo "<TR bgcolor=$bgcolor2  height='30'>";
         echo "<TD align='left' valign='top'><FONT COLOR=white><b>$msq_aff_ordre</b></FONT></TD>";
         echo "<TD align='left' valign='top'><FONT COLOR=white><b>$msq_activite</b></FONT></TD>";
         echo "<TD align='left' valign='top'><FONT COLOR=white><b>$msq_desc_seq</b></FONT></TD>";
         echo "<TD align='left' valign='top'><FONT COLOR=white><b>$mess_motclef</b></FONT></TD>";
         echo "<TD align='left' valign='top'><FONT COLOR=white><b>$msq_duree_seq</b></FONT></TD>";
         echo "<TD align='left' valign='top'><FONT COLOR=white><b>$mess_dur_max</b></FONT></TD>";
//         echo "<TD align='left' valign='top'><FONT COLOR=white><b>$mess_visible</b></FONT></TD>";    //***
         echo "<TD align='left' valign='top'><FONT COLOR=white><b>$msq_si_prereq </b></FONT></TD>";
         echo "<TD align='left' valign='top'><FONT COLOR=white><b>$mess_sco_min</b></FONT></TD>";
         echo "<TD align='left' valign='top'><FONT COLOR=white><b>$msq_ress_assoc</b></FONT></TD>";
         echo "<TD align='left' valign='top'><FONT COLOR=white><b>$mess_modif_base</b></FONT></TD>";
         echo "</TR>";
         $i = 0;
         $code = array();
         $id_act_seq = array();
         while ($i != $nb_act_seq)
         {
               $affiche_scorm ='';
               $id = mysql_result ($act_query,$i,"mod_cdn");
               $code[$id]=$i;
               $nom = mysql_result ($act_query,$i,"mod_titre_lb");
               $description = html_entity_decode(mysql_result($act_query,$i,"mod_desc_cmt"),ENT_QUOTES,'iso-8859-1');
               $motclef = html_entity_decode(mysql_result ($act_query,$i,"mod_motclef_lb"),ENT_QUOTES,'iso-8859-1');
               $numero = mysql_result ($act_query,$i,"mod_numero_lb");
               $launch = mysql_result ($act_query,$i,"mod_launch_lb");
               $visible = mysql_result ($act_query,$i,"mod_visible");
               $duree = mysql_result ($act_query,$i,"mod_duree_nb");
               $datafromlms = mysql_result ($act_query,$i,"mod_datafromlms");
               $prerequis = mysql_result ($act_query,$i,"mod_prereq_lb");
               $score_max = mysql_result ($act_query,$i,"mod_masteryscore");
               $maxtime = mysql_result ($act_query,$i,"mod_maxtimeallowed");
               $aff_cadenas = "<IMG SRC=\"images/modules/tut_form/icocadenas.gif\" border='0'>";
               if ($visible == 'TRUE')
                  $visib = 'OUI';
               else
                  $visib = 'NON';
               if ($maxtime != '' && strlen($maxtime) > 9)
               {
                  if (strstr($maxtime,'t') || strstr($maxtime,'T'))
                     $maxtime = substr($maxtime,13);
                  $liste_duree = explode(":",$maxtime);
                  $maxtime = ($liste_duree[0]*60)+$liste_duree[1];
               }
               if ($prerequis != "")
               {
                  $prereq1 = GetDataField ($connect,"select mod_cdn from scorm_module where mod_numero_lb = \"$prerequis\"","mod_cdn");
                  $prereq = $code[$prereq1]+1;
               }
               else
                  $prereq = '';
               $affiche_scorm .= "<TR bgcolor= \"#F4F4F4\">";
               if ($launch == '')
                  $nom = "<B>$nom</B>";
               $l = $i+1;
               $affiche_scorm .= "<TD valign='top'>$l</TD>";
               $affiche_scorm .= "<TD valign='top'>$aff_cadenas&nbsp;$nom</TD>";
               $affiche_scorm .= "<TD valign='top'>$description</TD>";
               $affiche_scorm .= "<TD valign='top'>$motclef</TD>";
               if ($duree != 0)
                  $affiche_scorm .= "<TD valign='top'>".duree_calc($duree)."</TD>";
               else
                  $affiche_scorm .= "<TD valign='top'>&nbsp;</TD>";
               if ($maxtime != '')
                  $affiche_scorm .= "<TD valign='top'>".duree_calc($maxtime)."</TD>";
               else
                  $affiche_scorm .= "<TD valign='top'>&nbsp;</TD>";
//               $affiche_scorm .= "<TD valign='top'>$visib</TD>";
               $affiche_scorm .= "<TD valign='top'>$prereq</TD>";
               $affiche_scorm .= "<TD valign='top'>$score_max</TD>";
               if ($launch != '')
               {
                 if (strstr($type_sequence,"SCORM") && !strstr($type_sequence,"AICC"))
                 {
                    if (!strstr($launch,"http://") && $launch != '')
                    {
                       $launch = $launch;
                       $supplement = "";
                    }elseif (strstr($launch,"http://"))
                    {
                       $supplement = "X";
                    }
                    if ($datafromlms)
                       $launch .="?".$datafromlms;
                    if (strstr($launch,$adresse_http) == TRUE)
                       $scorm_externe = 1;
                    else
                       $scorm_externe = 0;
                    $launcher = $launch;
                 }
                 elseif (strstr($type_sequence,"SCORM") && strstr($type_sequence,"AICC"))
                 {
                    /*if (!strstr($launch,"http://") && $launch != '')
                       $launch = $launch;
                    elseif (strstr($launch,"http://"))
                       $launch = $launch;
                    //$session_lms = md5($login)."|".$sco['id_mod'][$i]."|$seq|$id_parc|$id_user|$numero_groupe";
                    //$suitable = "aicc_sid=$session_lms&aicc_url=".urlencode("$adresse_http/scorm/aicc.php")."&vendor_param=".$sco['datafromlms'][$i];
                    */

                    $launcher = $launch."?aicc_sid=&aicc_url=$suitable&vendor_param=".$datafromlms;
                    $lelaunch = $launch;
                    $launch= urlencode($launch);
                 }
                $lien_index = urlencode("index_contenu$supplement.php?id_seq=$id_seq&id_parc=$id_parc&scormid=$id");
                $le_linker = ($nb_act_seq > 0) ? "scorm/lancer_sco$supplement.php?lien=$launch&lien_index=$lien_index&id_seq=$id_seq&id_parc=$id_parc&scormid=$id&dou=sequence" : $lelaunch;
                //$lien = urlencode($launch);
                if (strstr($launch,'educagrinet'))
                   $affiche_scorm .= "<TD valign='top'><A HREF=\"javascript:void(0);\" onClick=\"window.open('$lelaunch',null,'status=no, directories=no,copyhistory=0,".
                           "titlebar=no, toolbar=yes, location=no, menubar=yes, scrollbars=yes, resizable=yes');\"";
                else
                   $affiche_scorm .= "<TD valign='top'><A HREF=\"javascript: void(0);\" ".
                           "onClick=\"window.open('$le_linker',null,'status=no, directories=no,copyhistory=0,".
                           "titlebar=no, toolbar=yes, location=no, menubar=yes, scrollbars=yes, resizable=yes');\"";
                $affiche_scorm .= " onmouseover=\"img$i.src='images/ecran-annonce/icoGgob.gif';return true;\"".
                           " onmouseout=\"img$i.src='images/ecran-annonce/icoGgo.gif'\">".
                           "<IMG NAME=\"img$i\" SRC=\"images/ecran-annonce/icoGgo.gif\" BORDER='0'".
                           " onLoad=\"tempImg=new Image(0,0); tempImg.src='images/ecran-annonce/icoGgob.gif'\"></A></TD>";
               }
               else
                 $affiche_scorm .= "<TD valign='top'><IMG SRC=\"images/ecran-annonce/icoGgoinactif.gif\" BORDER='0'></TD>";
               if ($id_auteur == $id_user || $typ_user == 'ADMINISTRATEUR')
               {
                  $lien = "modif_sco.php?liste=$liste&parcours=$parcours&id_parc=$id_parc&proprio=$proprio&refer=$refer&id_ref=$id_ref&id_ref_parc=$id_ref_parc&id_seq=$id_seq&id_act=$id&modif_act=1&miens=$miens&miens_parc=$miens_parc&parc=$id_parc";
                  $lien = urlencode($lien);
                  $affiche_scorm .= "<TD width='2%' align='middle' valign='top'><a href=\"trace.php?link=$lien\" target='main'>".
                                    "<IMG SRC=\"images/repertoire/icoGrenomfich.gif\" height=\"20\" width=\"20\" TITLE=\"$msq_modifier\" BORDER=0></A></td></TR>";
               }
               else
                  $affiche_scorm .= "<TD>&nbsp;</TD></TR>";
               echo "$affiche_scorm";
            $i++;
         }
         echo "</TABLE>";
         echo fin_tableau($html);
         exit;
    }
    else
    {
       if ($nb_act_seq == 0)
       {
         $titre_sequence = GetDataField ($connect,"select seq_titre_lb from sequence where seq_cdn = '$id_seq'","seq_titre_lb");
         $id_ref_parc = GetDataField ($connect,"select parcours_referentiel_no from parcours where parcours_cdn = $id_parc","parcours_referentiel_no");
         $letitre = "$mess_bas_gest_seq : $titre_sequence";
         entete_concept("liste_parc.inc.php",$letitre);
         echo aide_simple("sequence");
         echo "<TR><TD class='sous-titre'>$msq_noact</TD></TR>";
         echo "<TR><TD><DIV id='sequence'><a href=\"sequence.php?liste=$liste&id_seq=$id_seq&parcours=$parcours&activite=1&id_parc=$id_parc&proprio=$proprio&refer=$refer&id_ref=$id_ref&id_ref_parc=$id_ref_parc&miens=$miens&miens_parc=$miens_parc&parc=$id_parc\" target='main'>$msq_crea_act_seq</a>";
      }
      else
      {
         $id_ref_parc = GetDataField ($connect,"select parcours_referentiel_no from parcours where parcours_cdn = $id_parc","parcours_referentiel_no");
         $id_auteur = GetDataField ($connect,"select seq_auteur_no from sequence where seq_cdn = '$id_seq'","seq_auteur_no");
         $titre_sequence = GetDataField ($connect,"select seq_titre_lb from sequence where seq_cdn = '$id_seq'","seq_titre_lb");
         $nom_sequence = GetDataField ($connect,"select seq_desc_cmt from sequence where seq_cdn = '$id_seq'","seq_desc_cmt");
         $droit_dupli_seq = GetDataField ($connect,"select seq_publique_on from sequence where seq_cdn = '$id_seq'","seq_publique_on");
         $id_ref_seq = GetDataField ($connect,"select seqref_referentiel_no from sequence_referentiel where seqref_seq_no = '$id_seq'","seqref_referentiel_no");
         if ($nom_sequence == '')
            $nom_sequence = $msq_no_decrite;
         $bgcolor2 = '#2b677a';
         $bgcolor1 = '#F8F2E4';
         $letitre ="$msq_seq : $titre_sequence";
         entete_concept("liste_parc.inc.php",$letitre);
         echo aide_simple("sequence");
         $id_ref_parc = GetDataField ($connect,"select parcours_referentiel_no from parcours where parcours_cdn = $id_parc","parcours_referentiel_no");
         if ($sous_titre != '')
             echo "<TR><TD colspan='2' class='sous_titre'>$sous_titre</TD></TR>";
         echo "<TR><TD width='100%' colspan='2' ><TABLE cellspacing='1' cellpadding='5' width='100%' bgColor='#FFFFFF'>";
         echo "<TR bgcolor=$bgcolor2  height='30'>";
         if ($id_auteur == $id_user || $typ_user == 'ADMINISTRATEUR')
         {
           echo "<TD align='middle'><IMG SRC=\"images/modules/tut_form/flechebas.gif\" TITLE=\"$mess_increment\" border='0'></TD>";
           echo "<TD align='middle'><IMG SRC=\"images/modules/tut_form/flechehaut.gif\" TITLE=\"$mess_decrement\" border='0'></TD>";
         }
         echo "<TD align='left' valign='center'><FONT COLOR=white><b>$msq_activite</b></FONT></TD>";
         echo "<TD align='left' valign='center'><IMG SRC=\"images/i-lav.gif\" border=0 width='15' height='15' ></TD>";
         echo "<TD align='left' valign='center'><FONT COLOR=white><b>$msq_aff_ordre</b></FONT></TD>";
         echo "<TD align='left' valign='center'><FONT COLOR=white><b>$msq_aff_cons</b></FONT></TD>";
         echo "<TD align='left' valign='center'><FONT COLOR=white><b>$msq_duree_seq</b></FONT></TD>";
         echo "<TD align='left' valign='center'><FONT COLOR=white><b>$msq_ress_assoc</b></FONT></TD>";
         echo "<TD align='left' valign='center'><FONT COLOR=white><b>$mess_modif_base</b></FONT></TD>";
         echo "<TD align='left' valign='center'><FONT COLOR=white><b>$mess_ag_supp</b></FONT></TD>";
         echo "<TD align='left' valign='center'><FONT COLOR=white><b>$mess_dupliquer</b></FONT></TD>";
         echo "</TR>";
         $i = 0;
         $id_act_seq = array();
         while ($i != $nb_act_seq)
         {
               $id = mysql_result ($act_query,$i,"act_cdn");
               $nom = mysql_result ($act_query,$i,"act_nom_lb");
               $ordre = mysql_result ($act_query,$i,"act_ordre_nb");
               $flag = mysql_result ($act_query,$i,"act_flag_on");
               $ress_norok = mysql_result ($act_query,$i,"act_ress_on");
               $consigne = html_entity_decode(DelAmp(mysql_result($act_query,$i,"act_consigne_cmt")),ENT_QUOTES,'iso-8859-1');
               $commentaire = html_entity_decode(mysql_result($act_query,$i,"act_commentaire_cmt"),ENT_QUOTES,'iso-8859-1');
               $pass_mult = mysql_result ($act_query,$i,"act_passagemult_on");
               $acquit = mysql_result ($act_query,$i,"act_acquittement_lb");
               $devoir = mysql_result ($act_query,$i,"act_devoirarendre_on");
               $notation = mysql_result ($act_query,$i,"act_notation_on");
               $auteur = mysql_result ($act_query,$i,"act_auteur_no");
               $droit_voir_act = mysql_result ($act_query,$i,"act_publique_on");
               $aff_cadenas = "";
           //dey Dfoad
              $req_media = mysql_result(mysql_query("select count(*) from activite_media where actmedia_act_no = $id"),0);
              $media_act = "";
              if ($req_media > 0)
              {
                 $media_act = GetDataField ($connect,"select ress_url_lb from ressource_new,activite_media where
                                            ress_cdn = actmedia_ress_no and actmedia_act_no =$id ","ress_url_lb");
              }
               $l = $i+1;
               $id_act_seq[$i] = $id;
               if (($typ_user == "ADMINISTRATEUR" || $id_auteur == $id_user) && $nb_act_seq > 1)
               {
                 if ($i > 0)
                   $act_precedente = $id_act_seq[$i-1];
                 if ($i < ($nb_act_seq-1))
                   $act_suivante = mysql_result ($act_query,$l,"act_cdn");
                 else
                   $act_suivante = "";
               }
               $act_actuelle = $id;
               if (!$consigne)
                  $consigne = $msq_aucune;
               //Séléction ressource
               $id_ress = mysql_result ($act_query,$i,"act_ress_no");
               $non_affic_ress_lien = 0;
               if ($id_ress == 0)
                 $ressource = $msq_aucune;
               else
               {
                  $ressource = GetDataField ($connect,"select ress_titre from ressource_new where ress_cdn = $id_ress","ress_titre");
                  $url_ressource = GetDataField ($connect,"select ress_url_lb from ressource_new where ress_cdn = $id_ress","ress_url_lb");
                  if ($dev_act == 'xApi TinCan')
                     $url_ressource .= TinCanTeach ('teacher|'.$id_parc.'|'.$id_seq.'|'.$id.'|0',$url_ressource,$commentaire);

                  //Dans le cas ou la ressource a ete supprimee
                  if ($url_ressource == '')
                       $ressource = $ressource;
                  else
                  {
                       $typ_ress = GetDataField ($connect,"select ress_support from ressource_new where ress_cdn = $id_ress","ress_support");
                       $typ_ress = strtoupper ($typ_ress);
                  }
                } //fin else

                $duree = mysql_result ($act_query,$i,"act_duree_nb");
                echo couleur_tr($i+1,25);
                if ($auteur != $id_user && $droit_voir_act == 0)// && $id_ress > 0 && $url_ressource != ""
                   $aff_cadenas = "<IMG SRC=\"images/modules/tut_form/icocadenas.gif\" border='0'>&nbsp;";
                else
                   $aff_cadenas = "";
                if ($id_auteur == $id_user || $typ_user == 'ADMINISTRATEUR')
                {
                   if ($i < ($nb_act_seq-1))
                   {
                       $lien = "sequence.php?consult_act=1&descendre=1&id_parc=$id_parc&proprio=$proprio&refer=$refer&id_act=$id&id_seq=$id_seq&act_precedente=$act_precedente&act_actuelle=$act_actuelle&act_suivante=$act_suivante&parcours=$parcours&liste=$liste&id_ref=$id_ref&id_ref_parc=$id_ref_parc&miens=$miens&miens_parc=$miens_parc&parc=$id_parc&liste_act_seq=1";
                       $lien = urlencode($lien);
                       echo "<TD height='20' align='middle' valign='top'><A HREF=\"trace.php?link=$lien\" target='main' title = '$mess_increment'><IMG SRC=\"images/modules/tut_form/flechebas.gif\" TITLE=\"$mess_increment\"  border='0'></TD>";
                   }
                   else
                        echo "<TD></TD>";
                   if ($i > 0)
                   {
                       $lien = "sequence.php?consult_act=1&monter=1&id_parc=$id_parc&proprio=$proprio&refer=$refer&id_act=$id&id_seq=$id_seq&act_precedente=$act_precedente&act_actuelle=$act_actuelle&act_suivante=$act_suivante&parcours=$parcours&liste=$liste&id_ref=$id_ref&id_ref_parc=$id_ref_parc&miens=$miens&miens_parc=$miens_parc&parc=$id_parc&liste_act_seq=1";
                       $lien = urlencode($lien);
                       echo "<TD height='20' align='middle' valign='top'><A HREF=\"trace.php?link=$lien\" target='main' title = '$mess_decrement'><IMG SRC=\"images/modules/tut_form/flechehaut.gif\" TITLE=\"$mess_decrement\" border='0'></TD>";
                   }
                   else
                        echo "<TD></TD>";
                   }
                   /*
                   if ($commentaire != "")
                     $commentaire1 = (strstr($commentaire,'&amp')) ? htmlentities(DelAmp($commentaire,ENT_QUOTES,'iso-8859-1')) : htmlentities($commentaire,ENT_QUOTES,'iso-8859-1');
                   else
                     $commentaire1 = $mess_no_comment;
                   */

//dey Dfoad <span au lieu de <a>
                   echo "<TD valign='top'><span style='font-weight:bold;'> $aff_cadenas$nom</span></TD>";
                   echo "<TD valign='top'><A href=\"javascript:void(0);\"";
                   $titre_bulle ="$msq_aff_pass_mult <B>$pass_mult</B><BR>$msq_aff_acquit <B>$acquit</B><BR>$msq_aff_dev_rend <B>$devoir</B><BR>$msq_act_evalue <B>$notation</B>";
                   echo bulle($titre_bulle,$msq_fic_act,"RIGHT","",250);
                   $titre_bulle = "";
                   echo "<IMG SRC=\"images/i-lav.gif\" border=0 width='15' height='15'></A></td>";
                   echo "<td align='middle' valign='top'>$ordre</td>";
           //dey Dfoad
             $req_typdev = mysql_result(mysql_query("select count(*) from activite_devoir where actdev_act_no = $id"),0);
             $dev_act = "";
             if ($req_typdev > 0)
             {
                 $dev_act = GetDataField ($connect,"select actdev_dev_lb from activite_devoir where
                                            actdev_act_no = $id ","actdev_dev_lb");
            }
             else
                 $dev_act = "Pas de devoir";
            $class_act =  GetDataField ($connect,"select actdevico_style_lb from actdev_icone where
                                            actdevico_type_lb = \"$dev_act\" ","actdevico_style_lb");

           echo "<TD align='left' valign='top' ><div $class_act> ".ltrim(html_entity_decode($consigne,ENT_QUOTES,'iso-8859-1'))."</div>";
              if($media_act != "")
              {
                  $actit = $id;
                  $largeur = "220";
                  $hauteur = "140";
                  echo "<br />&nbsp;<div id='insertMedia'>";
                      include ("media.php");
                  echo "</div>";
              }
        echo "</td>";
                   $duree = duree_calc($duree);
                   echo "<td align='middle' valign='top'>$duree</td>";
                   if (strstr($url_ressource,"?"))
                      $aj="&";
                   else
                      $aj="?";
                   if ((strstr($url_ressource,"ParWeb")) || (strstr($url_ressource,"parweb")) || (strstr($url_ressource,"Legweb"))  || (strstr($url_ressource,"legweb")) || (strstr($url_ressource,"Tatweb"))  || (strstr($url_ressource,"tatweb")) || (strstr($url_ressource,"Qcmweb"))  || (strstr($url_ressource,"qcmweb")) || (strstr($url_ressource,"Elaweb")) || (strstr($url_ressource,"elaweb")))
                      $suite="&nom=$nom_user&prenom=$prenom_user&email=$email_user";
                   else
                      $suite="";
                   if ($id_ress > 0 && $url_ressource == "")
                   {
                      $lien= "ress_virtuel.php?id_ress=$id_ress&id_act=$id";
                      $lien = urlencode($lien);
                      echo "<td align='left' valign='top'><DIV id='sequence'><A href='#' onclick=\"window.open('trace.php?link=$lien','','resizable=yes,scrollbars=yes,status=no,width=700,height=400')\">$ressource</a></DIV></td>";
                   }
                   elseif ($id_ress > 0 &&
                          ((strstr(strtolower($url_ressource),".doc") ||
                          strstr(strtolower($url_ressource),".xls") ||
                          strstr(strtolower($url_ressource),".xlt"))) &&
                          ($auteur == $id_user || $typ_user == 'ADMINISTRATEUR' || $droit_voir_act == 1))
                   {
                      $url_ressource = serveur_externe($url_ressource);
                      $lien=urldecode($url_ressource);
                      echo "<TD valign='top'><DIV id='sequence'><A href=\"$lien\" target='_blank'>$ressource</A></DIV>";
                   }
                   elseif (strstr(strtolower($url_ressource),".flv") ||
                           strstr(strtolower($url_ressource),".mp3") ||
                           strstr(strtolower($url_ressource),".swf" ||
                           strstr(strtolower($url_ressource),".mp4") ||
                           strstr(strtolower($url_ressource),".ogv") ||
                           strstr(strtolower($url_ressource),".webm")) &&
                          ($auteur == $id_user || $typ_user == 'ADMINISTRATEUR' || $droit_voir_act == 1))
                   {
                     $lien = "lanceMedia.php?id_ress=$id_ress";
                     echo "<TD align='left' valign='top'><DIV id='sequence'><A href=\"javascript:void(0);\" onclick=\"window.open('$lien','','resizable=yes,scrollbars=yes,status=no')\">$ressource</a></DIV></td>";
                   }
                   elseif ($id_ress > 0 && $url_ressource != "" &&
                          ($auteur == $id_user || $typ_user == 'ADMINISTRATEUR' || $droit_voir_act == 1))
                   {
                      if ($dev_act != 'xApi TinCan')
                      {
                        if (strstr(strtolower($url_ressource),"educagrinet"))
                          $url_ressource = str_replace("acces.html","direct.html",$url_ressource)."&url=$url_ress&auth_cdn=$auth_cdn";
                        else
                          $url_ressource = serveur_externe($url_ressource);
                        $lien=urldecode($url_ressource);
                        echo "<TD align='left' valign='top'><DIV id='sequence'><A href=\"javascript:void(0);\" onclick=\"window.open('$lien','','resizable=yes,scrollbars=yes,status=no')\">$ressource</a></DIV></td>";
                      }
                      else
                      {
                         $lien = $url_ressource.TinCanTeach ('teacher|'.$id_parc.'|'.$id_seq.'|'.$id.'|0',$url_ressource,$commentaire);
                         echo "<TD align='left' valign='top'><DIV id='sequence'><A href=\"javascript:void(0);\" onclick=\"window.open('$lien','','resizable=yes,scrollbars=yes,status=no')\">$ressource</a></DIV></td>";
                      }
                   }
                   else
                      echo "<TD align='left' valign='top'>$ressource</td>";
                   if ($auteur == $id_user || $typ_user == 'ADMINISTRATEUR')
                   {
                     $lien = "sequence.php?liste=$liste&action_act=1&parcours=$parcours&id_parc=$id_parc&proprio=$proprio&refer=$refer&id_ref=$id_ref&id_ref_parc=$id_ref_parc&id_seq=$id_seq&id_act=$id&id_ress=$id_ress&modif_act=1&miens=$miens&miens_parc=$miens_parc&parc=$id_parc&liste_act_seq=1";
                     $lien = urlencode($lien);
                     echo "<TD width='2%' align='middle' valign='top'><a href=\"trace.php?link=$lien\" target='main'>".
                          "<IMG SRC=\"images/repertoire/icoGrenomfich20.gif\" TITLE=\"$msq_modifier\" BORDER=0></A></td>";
                   }
                   else
                     echo "<TD>&nbsp;</TD>";
                   if ($auteur == $id_user || $typ_user == 'ADMINISTRATEUR')
                   {
                     $requete_grp = mysql_query ("select * from groupe order by grp_nom_lb");
                     $nb_grp = mysql_num_rows($requete_grp);
                     if ($nb_grp > 0)
                     {
                           $gp=0;$Nb_act_suivi= 0;
                           while ($gp < $nb_grp)
                           {
                                  $id_grp = mysql_result($requete_grp,$gp,"grp_cdn");
                                  $nomb = mysql_num_rows(mysql_query ("select * from suivi1_$id_grp where suivi_act_no = $id"));
                                  $Nb_act_suivi += $nomb;
                              $gp++;
                           }
                     }
                     if ($Nb_act_suivi == 0)
                     {
                        $lien = "sequence.php?consult_act=1&parcours=$parcours&liste=$liste&id_parc=$id_parc&proprio=$proprio&refer=$refer&supp=1&id_act=$id&id_seq=$id_seq&id_ref=$id_ref&id_ref_parc=$id_ref_parc&miens=$miens&miens_parc=$miens_parc&parc=$id_parc&liste_act_seq=1&toutes_seq=$toutes_seq";
                        $lien = urlencode($lien);
                        echo "<TD width='2%' align='middle' valign='top'><a href=\"javascript:void(0);\" ".
                             "onclick=\"javascript:return(confm('trace.php?link=$lien'));\" target='main'>".
                             "<IMG SRC=\"images/messagerie/icopoubelressour.gif\" TITLE=\"$msq_sup_act\" BORDER=0></A></td>";
                     }
                     else
                        echo "<TD width='2%' align='middle' valign='top'>".
                             "<IMG SRC=\"images/repertoire/icoptiinterdit.gif\" BORDER=0 ".
                             bulle($msq_act_no_supp,"","LEFT","",180)."</td>";
                   }
                   else
                     echo "<TD>&nbsp;</TD>";
                   if ($id_seq > 0 && (($auteur == $id_user && $droit_voir_act == 0) || $droit_voir_act == 1))
                   {
                        $lien = "sequence.php?consult_act=1&parcours=$parcours&liste=$liste&id_parc=$id_parc&proprio=$proprio&refer=$refer&dupli_act=1&act_a_dupli=$id&id_act=$id&id_seq=$id_seq&id_ref=$id_ref&id_ref_parc=$id_ref_parc&miens=$miens&miens_parc=$miens_parc&parc=$id_parc&liste_act_seq=1&toutes_seq=$toutes_seq";
                        $lien = urlencode($lien);
                        echo "<TD width='2%' align='middle' valign='top'><a href=\"trace.php?link=$lien\"".
                             " target='main'".bulle($mess_dupli_act,"","LEFT","ABOVE",220).
                             "<IMG SRC=\"images/repertoire/icoptiedit.gif\" BORDER=0></A></TD>";
                   }
                   else
                     echo "<TD>&nbsp;</TD>";

               $i++;
               } //fin while ($i != $nb_seq)
               echo "</TR></TABLE>";
               //Lorsqu'un apprenant consulte les activites a partir des sequences qu'il a a faire ou en cours, il ne doit pas avoir accès à ces liens
               //Le test doit se faire aussi pour enlever le lien d'association de ressource
               $id_auteur = GetDataField ($connect,"select seq_auteur_no from sequence where seq_cdn = $id_seq","seq_auteur_no");
               // Droit de l'auteur à ajouter une activité à la séquence
               echo "</TD></TR><TR height='50'><TD><TABLE cellpadding='4' cellspacing='4'><TR>";
/*               if ($id_auteur == $id_user){
                  $lien="sequence.php?liste=$liste&id_seq=$id_seq&parcours=$parcours&id_parc=$id_parc&proprio=$proprio&refer=$refer&choix_act=1&id_ref=$id_ref&id_ref_parc=$id_ref_parc&miens=$miens&miens_parc=$miens_parc&parc=$id_parc&liste_act_seq=1";
                  $lien = urlencode($lien);
                  echo "<TD nowrap>$bouton_gauche<A href=\"trace.php?link=$lien\" target='main'>$msq_choix_act</A>$bouton_droite</TD>";
               }

               if ($id_auteur == $id_user || $typ_user == "RESPONSABLE_FORMATION" || $typ_user == "FORMATEUR_REFERENT" || $typ_user == "ADMINISTRATEUR"){
                  if ($id_auteur == $id_user || $droit_dupli_seq == 1){
                    $lien="sequence.php?liste=$liste&dupli_seq=1&id_seq=$id_seq&parcours=$parcours&id_parc=$id_parc&proprio=$proprio&refer=$refer&id_ref=$id_ref&id_ref_parc=$id_ref_parc&miens=$miens&miens_parc=$miens_parc&parc=$id_parc&liste_act_seq=1";
                    $lien = urlencode($lien);
                    echo "<TD nowrap>$bouton_gauche<A href=\"trace.php?link=$lien\" target='main'>$msq_dupli_seq</A>$bouton_droite</TD>";
                  }
               }
*/
               $lien = "vue_sequence.php?seq=$id_seq";
               $lien = urlencode($lien);
               echo "<TD nowrap>$bouton_gauche<A HREF=\"#\" onclick=\"javascript:window.open('trace.php?link=$lien','','top=0,left=0,width=800,height=400,resizable=yes,status=no,scrollbars=yes')\">$mess_vue_app</A>$bouton_droite</TD>";
               if (!strstr($mod_seq,"SCORM") && ($id_auteur == $id_user || $typ_user == 'ADMINISTRATEUR'))
               {
                   $lien="export_seq_xml.php?id_seq=$id_seq";
                   echo "<TD nowrap>$bouton_gauche<a href=\"$lien\">Export Scorm de la séquence</a>$bouton_droite</TD>";
               }
               echo "</TR></TABLE><TD></TR>";
             } //fin else ($Nb_act_seq ...)
          echo "</TABLE></TD></TR></TABLE>";
      }
} // fin if ($consult_act == 1)
if ($action_act == 1)
{
      ?>
      <SCRIPT language=JavaScript>
      function checkForm(frm) {
      var ErrMsg = "<?php echo $mess_info_no;?>\n\n";
      var lenInit = ErrMsg.length;
      if (isEmpty(frm.nom)==true)
        ErrMsg += ' - <?php echo $msq_tit_form;?>\n';
      if (isEmpty(frm.consigne)==true)
        ErrMsg += ' - <?php echo $msq_consigne_act_form;?>\n';
      <?php
      if ($modif_act == 1)
      {
             $req_typdev = mysql_num_rows(mysql_query("select * from activite_devoir where actdev_act_no = $id_act"));
             $dev_act = "";
             if ($req_typdev > 0)
                 $dev_act = GetDataField ($connect,"select actdev_dev_lb from activite_devoir where
                                            actdev_act_no = $id_act ","actdev_dev_lb");
      }
      if (isset($modif_act) && isset($req_typdev) && $dev_act == 'xApi TinCan')
         echo '';
      else
      {
      ?>
      if (isVide(frm.ress_norok)==true)
        ErrMsg += ' - <?php echo $msq_ass_ress_form;?>\n';
      if (isVide(frm.droit_voir_act)==true)
        ErrMsg += ' - <?php echo $msq_droit_voir;?>\n';
      if (isEmpty(frm.horaire)==true && isEmpty(frm.minutage)==true)
        ErrMsg += ' - <?php echo $msq_horaire_act_form;?>\n';
      if (isVide(frm.pass_mult)==true)
        ErrMsg += ' - <?php echo $msq_pass_act_form;?>\n';
      if (isVide1(frm.acquit)==true)
        ErrMsg += ' - <?php echo $msq_acquit_form;?>\n';
      if (isVide(frm.dev_a_rendre)==true)
        ErrMsg += ' - <?php echo addslashes($msq_dev_act_form);?>\n';
      if (isVide(frm.notation)==true)
        ErrMsg += ' - <?php echo addslashes($msq_not_act_form);?>\n';
      if (document.getElementsByName("notation")[0].checked == true)
          var note = document.getElementsByName("notation")[0];
      else
          var note = "";
      if (document.getElementsByName("acquit")[0].checked == true)
          var acq = document.getElementsByName("acquit")[0];
      else
          var acq = "";
      if (document.getElementsByName("acquit")[2].checked == true)
          var acq_ress = document.getElementsByName("acquit")[2];
      else
          var acq_ress = "";
      if (document.getElementsByName("dev_a_rendre")[0].checked == true)
          var devoir = document.getElementsByName("dev_a_rendre")[0];
      else
          var devoir = "";
      if(note.value == "OUI" && acq.value == "APPRENANT")
          ErrMsg += ' - <?php echo $mess_autoeval?>\n';
      if(devoir.value == "OUI" && acq_ress.value == "RESSOURCE")
          ErrMsg += ' - <?php echo $mess_ress_dev?>\n';
      <?php
      }
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
    function isVide(elm) {
       if(!elm[0].checked && !elm[1].checked){
         return true;
       }
      return false;
    }
    function isVide1(elm) {
       if(!elm[0].checked && !elm[1].checked && !elm[2].checked){
         return true;
       }
      return false;
    }
    </SCRIPT>
      <?php
      $bgcolor2 = '#2b677a';
      $bgcolor1 = '#F8F2E4';
    $letitre = $msq_dat_act;
    entete_concept("liste_parc.inc.php",$letitre);
    echo aide_simple("sequence");
    echo "<form name='form1' action=\"sequence.php?liste=$liste&consult_act=1&modif=1&parcours=$parcours&id_parc=$id_parc&proprio=$proprio&refer=$refer&id_act=$id_act&id_seq=$id_seq&ordre1=$ordre&id_ref=$id_ref&id_ref_parc=$id_ref_parc&auteur=$auteur&miens=$miens&miens_parc=$miens_parc&parc=$id_parc&liste_act_seq=1\" target='main' method='post'>";
    //On reselectionne certaines infos car on ne peut pas toutes les passer par URL (pb d'URL trop longue!!!!)
    $act_query = mysql_query("SELECT * FROM activite WHERE act_cdn=$id_act");
    $nom = mysql_result ($act_query,0,"act_nom_lb");
    $auteur = mysql_result ($act_query,0,"act_auteur_no");
    $consigne = mysql_result ($act_query,0,"act_consigne_cmt");
    $commentaire = mysql_result ($act_query,0,"act_commentaire_cmt");
    $ordre = mysql_result ($act_query,0,"act_ordre_nb");
    $ress_norok = mysql_result ($act_query,0,"act_ress_on");
    $flag = mysql_result ($act_query,0,"act_flag_on");
    $duree = mysql_result ($act_query,0,"act_duree_nb");
    $pass_mult = mysql_result ($act_query,0,"act_passagemult_on");
    $acquit = mysql_result ($act_query,0,"act_acquittement_lb");
    $notation = mysql_result ($act_query,0,"act_notation_on");
    $droit_voir_act = mysql_result ($act_query,0,"act_publique_on");
    $dev_a_rendre = mysql_result ($act_query,0,"act_devoirarendre_on");
    $id_seq = GetDataField ($connect,"select act_seq_no from activite where act_cdn = $id_act","act_seq_no");
    $consigne = str_replace("<BR>","",$consigne);
    $consigne = str_replace("\"","'",$consigne);
    $commentaire = str_replace("<BR>","",$commentaire);
    $commentaire = str_replace("\"","'",$commentaire);
    //Si l'on vient de d'associer une autre ressource, on doit reselectionner les infos suivantes
    if ($choix_ress == 1)
    {
        $nom= str_replace ("%","'",$nom);
        $id_seq = GetDataField ($connect,"select act_seq_no from activite where act_cdn = $id_act","act_seq_no");
        if ($id_ress != 0)    //Dans cas ou l'on vient de prerequis
           $ressource = GetDataField ($connect,"select ress_titre from ressource_new where ress_cdn = $id_ress","ress_titre");
    }
    echo "<TR><TD colspan='2'><TABLE width='100%' cellspacing = '1' cellpadding = '2'>";
    if (isset($dev_act) && $dev_act == 'xApi TinCan')
        echo "<TR><TD></td><td><div class='SOUS_TITRE' style='color:red;font-size:12px;'>".
             "<B>Cette ressource est au standard<span style='color:blue;font-size:14px;'> TinCan xApi".
             "</span> et seuls certains champs sont modifiables</B></div></td></tr>";
    echo "<TR><TD  valign='top'><B>$msq_ress_assoc</B></TD><TD><table><tbody><tr><td colspan='2'>";
    echo "<DIV id='laressource'>";
    if ($id_ress != 0 )
    {
       $titre_ress = GetDataField ($connect,"select ress_titre from ressource_new where ress_cdn = $id_ress","ress_titre");
       echo stripslashes($titre_ress);
       $cat_ress = GetDataField ($connect,"select ress_cat_lb from ressource_new where ress_cdn = $id_ress","ress_cat_lb");
        if (strstr($cat_ress,$mess_menu_forum))
          echo "&nbsp;&nbsp;&nbsp;&nbsp;<B><acronym title=\"$mess_noacces_idx\">$mrc_cat : $cat_ress</acronym></B></td></tr>";//}
        elseif ($cat_ress == $mess_ress_direct_act)
          echo "&nbsp;&nbsp;&nbsp;&nbsp;  <B>$mrc_cat : $cat_ress</B></td></tr>";//}
        else
          echo "&nbsp;&nbsp;&nbsp;&nbsp; <B>$mrc_cat : $cat_ress</B></td></tr>";//}
    }
    else //fin if ($choix_ress == 1)
       echo stripslashes($ressource)."</td></tr>";
    echo "</div>";
    echo "<div id='idress'><input type='hidden' name='id_ress' value='$id_ress'></div>";
    if ($modif_act == 1 && $apprenant != 1 && $utilisateur == "" && (!isset($dev_act) || (isset($dev_act) && $dev_act != 'xApi TinCan')))
    {
       $retour_modif = str_replace('&','|',$_SERVER['QUERY_STRING']);
       $lien="sequence.php?retour_modif=$retour_modif&liste=$liste&consult_act=1&id_act=$id_act&modif=1&modif_ressource=1&parcours=$parcours&id_parc=$id_parc&proprio=$proprio&refer=$refer&id_act=$id_act&id_seq=$id_seq&id_ress=$id_ress&ordre1=$ordre&id_ref=$id_ref&id_ref_parc=$id_ref_parc&auteur=$auteur&miens=$miens&miens_parc=$miens_parc&parc=$id_parc&liste_act_seq=1";
       $lien = urlencode($lien);
       if ($id_ress == 0  && $ress_norok == 'OUI')
         echo "<tr><td>$bouton_gauche<a href=\"trace.php?link=$lien\" target='main'>$msq_ass_ress_form</A>$bouton_droite</td>";
       elseif ($id_ress > 0 && $ress_norok == 'OUI')
         echo "<tr><td>$bouton_gauche<a href=\"trace.php?link=$lien\" target='main'><div id='ajoute_ress'>$msq_ass_ress</div></A>$bouton_droite</td>";
       if ($ress_norok == 'OUI' && $id_ress > 0)
       {
            echo "<td><div id='supp_ress' name='supp_ress' class='seqcla' ".
                 "onclick=\"javascript:if (conf() == true){appel_simple('formation/ress_supp.php?id_act=$id_act&flag=$flag&ress_norok=$ress_norok');".
                 "var mon_content=document.getElementById('laressource');mon_content.innerHTML='';".
                 "var content=document.getElementById('supp_ress');content.style.visibility='hidden';".
                 "var mon_titre_ress=document.getElementById('ajoute_ress');mon_titre_ress.innerHTML='$msq_ass_ress_form';".
                 "var mon_ress=document.getElementById('idress');mon_ress.innerHTML='<input type=hidden name=\'id_ress\' value=\'\'>';}\">".
                 "$bouton_gauche1<font color='#24677A'><B>$msq_slk_ress</B> </font>$bouton_droite1</div></td></tr>";
       }
    }
     echo "</tbody></table></TD></TR></div>";
     echo "<TR><TD nowrap><B>$msq_tit_form</B></TD><TD nowrap>";
     echo "<INPUT TYPE='text' class='INPUT' name='nom' value=\"".stripcslashes(NewHtmlEntityDecode($nom))."\" size='75' align='middle'></TD></TR>";
     echo "<TR><TD nowrap valign='top'><B>$msq_consigne_act_form</B></TD>";
     echo "<TD nowrap><TEXTAREA class='TEXTAREA' name='consigne' rows='6' cols='80' align='middle'>".
           html_entity_decode($consigne,ENT_QUOTES,'iso-8859-1')."</TEXTAREA></TD></TR>";
//dey Dfoad
          $req_media = mysql_result(mysql_query("select count(*) from activite_media where actmedia_act_no = $id_act"),0);
          $media_act = "";
          if ($req_media > 0)
          {
             $media_act = GetDataField ($connect,"select ress_url_lb from ressource_new,activite_media where
                                            ress_cdn = actmedia_ress_no and actmedia_act_no =$id_act ","ress_url_lb");
             echo "<TR><TD valign='top'><B><div id='titleMedia'>Modifier la consigne multimédia<div></B></TD>";
          }
          else
             echo "<TR><TD valign='top'><B><div id='titleMedia'>Insérer une consigne multimédia<div></B></TD>";
          $lien="charge_ressource_vrac.php?id_act=$id_act&dou=&media=1&charger_fichier=1";
          $lien = urlencode($lien);
          echo "<TD align='left' nowrap valign='top'><div style='clear:both;float:left;'>$bouton_gauche";
          echo "<A HREF=\"javascript:void(0)\" onClick=\"window.open('trace.php?link=$lien','','scrollbars=yes,resizable=yes,width=500,height=300,left=250,top=300')\" ".
                bulle("Vous retrouverez ce fichier (mp3, swf ou flv uniquement) dans votre répertoire dans le dossier Ressources Media","","RIGHT","ABOVE",220).
                "Télécharger le fichier (mp3, swf ou flv)</A>$bouton_droite</div>";
          if($media_act != "")
          {
                  $actit = $id_act;
                  echo '<div id="suppMedia" style="float:left;margin:4px 0 0 6px;"><a href="javascript:void(0);" name="suppMedia" '.
                       bullet("Cliquez ici pour supprimer la consigne multimédia en cours.","","RIGHT","ABOVE",240) .
                       ' onClick = "javascript:if (conf() == true){$.ajax({type: \'GET\',
                                              url: \'formation/gereMedia.php\',
                                              data: \'id_act='.$actit.'&suppMedia=1\',
                                              beforeSend:function()
                                              {
                                                 $(\'#affiche\').addClass(\'Status\');
                                                 $(\'#affiche\').append(\'Opération en cours....\');
                                              },
                                              success: function(){
                                                   $(\'#player'.$actit.'\').css(\'display\',\'none\');
                                                   $(\'#suppMedia\').css(\'display\',\'none\');
                                                   $(\'#mien\').empty();
                                                   $(\'#mien\').html(\'Vous venez de supprimer la consigne multimédia.\');
                                                   $(\'#titleMedia\').html(\'Insérer de nouveau une consigne multimédia.\');
                                                   $(\'#titleMedia\').css(\'font-weight\',\'bold\');
                                                   $(\'#affiche\').empty();
                                                   $(\'#mien\').show();
                                              }
                                        });};
                                        setTimeout(function() {$(\'#mien\').empty();},7000);" ><img src="images/supp.png" border="0"></a></div> ';
                  $largeur = "220";
                  $hauteur = "140";
                  echo "<div id='insertMedia'>";
                      include ("media.php");
                  echo "</div>";
          }
          echo "</TD></TR>";
   if (isset($dev_act) && $dev_act == 'xApi TinCan')
   {
      if ($duree){
         $reste = $duree%60;
         $heure = floor($duree/60);
         $duree1 = duree_calc($seq_duree);
         if ($duree == 0){
            $duree1 = "5".$mn;
            $duree = 5;
         }
      }
      echo "<TR><TD nowrap><B>$msq_duree_form</B></TD><TD><TABLE cellspacing='0'><TR>";
      if ($heure == 0 && $reste == 0)
      {
         echo "<TD><INPUT TYPE='text' class='INPUT' name='horaire' size='2' maxlength = '3' align='center'>$h </TD>";
         echo "<TD><INPUT TYPE='text' class='INPUT' name='minutage' size='2' maxlength = '2' align='center'>$mn</TD></TR></TABLE>";
      }
      else
      {
         echo "<TD><INPUT TYPE='text' class='INPUT' name='horaire' value='$heure' size='2' maxlength = '3' align='center'>$h </TD>";
         echo "<TD><INPUT TYPE='text' class='INPUT' name='minutage' value='$reste' size='2' maxlength = '2' align='center'>$mn</TD></TR></TABLE>";
      }
      echo "</TD></TR>";
      echo "<TR><TD>";
      echo "<INPUT type='HIDDEN' name='ress_norok' value='OUI' />";
      echo "<INPUT type='HIDDEN' name='droit_voir_act' value='1' />";
      echo "<INPUT type='HIDDEN' name='pass_mult' value='OUI' />";
      echo "<INPUT type='HIDDEN' name='acquit' value='RESSOURCE' />";
      echo "<INPUT type='HIDDEN' name='dev_a_rendre' value='NON' />";
      echo "<INPUT TYPE='HIDDEN' name='ordre' value='$ordre'>";
      echo "<INPUT type='HIDDEN' name='typdev' value='xApi TinCan' />";
      echo "<INPUT type='HIDDEN' name='notation' value='OUI' /></TD></TR>";
   }
   else
   {

     echo "<TR><TD nowrap valign='top'><B>$mess_admin_comment</B></TD><TD nowrap>";
     echo "<TEXTAREA class='TEXTAREA' name='commentaire' rows='6' cols='80' align='middle'>".
          html_entity_decode($commentaire,ENT_QUOTES,'iso-8859-1')."</TEXTAREA></TD></TR>";
      echo "<TR><TD nowrap><B>$msq_ass_ress_form</B></TD>";
      echo "<TD nowrap>";
      if ($ress_norok == '')
      {
         echo "$mess_oui&nbsp;&nbsp;<INPUT type='radio' name='ress_norok' value='OUI'>&nbsp;&nbsp;&nbsp;&nbsp;";
         echo "$mess_non&nbsp;&nbsp;<INPUT type='radio' name='ress_norok' value='NON'>";
      }
      elseif ($ress_norok == 'OUI')
      {
         echo "$mess_oui&nbsp;&nbsp;<INPUT type='radio' name='ress_norok' value='OUI' checked>&nbsp;&nbsp;&nbsp;&nbsp;";
         echo "$mess_non&nbsp;&nbsp;<INPUT type='radio' name='ress_norok' value='NON'>";
      }
      elseif ($ress_norok == 'NON')
      {
         echo "$mess_oui&nbsp;&nbsp;<INPUT type='radio' name='ress_norok' value='OUI'>&nbsp;&nbsp;&nbsp;&nbsp;";
         echo "$mess_non&nbsp;&nbsp;<INPUT type='radio' name='ress_norok' value='NON' checked>";
      }
      echo "</TD></TR>";
      echo "<TR><TD nowrap><B>$msq_droit_voir</B></TD>";
      echo "<TD nowrap>";
      if ($droit_voir_act == '')
      {
            echo "$mess_oui&nbsp;&nbsp;<INPUT type='radio' name='droit_voir_act' value='1'>&nbsp;&nbsp;&nbsp;&nbsp;";
            echo "$mess_non&nbsp;&nbsp;<INPUT type='radio' name='droit_voir_act' value='0'>";
      }
      elseif ($droit_voir_act == 1)
      {
            echo "$mess_oui&nbsp;&nbsp;<INPUT type='radio' name='droit_voir_act' value='1' checked>&nbsp;&nbsp;&nbsp;&nbsp;";
            echo "$mess_non&nbsp;&nbsp;<INPUT type='radio' name='droit_voir_act' value='0'>";
      }
      elseif ($droit_voir_act == 0)
      {
            echo "$mess_oui&nbsp;&nbsp;<INPUT type='radio' name='droit_voir_act' value='1'>&nbsp;&nbsp;&nbsp;&nbsp;";
            echo "$mess_non&nbsp;&nbsp;<INPUT type='radio' name='droit_voir_act' value='0' checked>";
      }
      echo "</TD></TR>";
      echo "<TR><TD nowrap><B>$msq_pos_seq</B></TD><TD nowrap>";
      echo "<INPUT TYPE='text'  class='INPUT'  name='ordre' value='$ordre' align='middle' size=1></TD></TR>";
      if ($duree){
         $reste = $duree%60;
         $heure = floor($duree/60);
         $duree1 = duree_calc($seq_duree);
         if ($duree == 0){
            $duree1 = "5".$mn;
            $duree = 5;
         }
      }
      echo "<TR><TD nowrap><B>$msq_duree_form</B></TD><TD><TABLE cellspacing='0'><TR>";
      if ($heure == 0 && $reste == 0)
      {
         echo "<TD><INPUT TYPE='text' class='INPUT' name='horaire' size='2' maxlength = '3' align='center'>$h </TD>";
         echo "<TD><INPUT TYPE='text' class='INPUT' name='minutage' size='2' maxlength = '2' align='center'>$mn</TD></TR></TABLE>";
      }
      else
      {
         echo "<TD><INPUT TYPE='text' class='INPUT' name='horaire' value='$heure' size='2' maxlength = '3' align='center'>$h </TD>";
         echo "<TD><INPUT TYPE='text' class='INPUT' name='minutage' value='$reste' size='2' maxlength = '2' align='center'>$mn</TD></TR></TABLE>";
      }
      echo "</TD></TR>";
      echo "<TR><TD nowrap><B>$msq_pass_act_form</B></TD><TD nowrap>";
      if ($pass_mult == '')
      {
            echo "$mess_oui&nbsp;&nbsp;<INPUT type='radio' name='pass_mult' value='OUI'>&nbsp;&nbsp;&nbsp;&nbsp;";
            echo "$mess_non&nbsp;&nbsp;<INPUT type='radio' name='pass_mult' value='NON'>";
      }
      elseif ($pass_mult == 'NON')
      {
            echo "$mess_oui&nbsp;&nbsp;<INPUT type='radio' name='pass_mult' value='OUI'>&nbsp;&nbsp;&nbsp;&nbsp;";
            echo "$mess_non&nbsp;&nbsp;<INPUT type='radio' name='pass_mult' value='NON' checked>";
      }
      elseif ($pass_mult == 'OUI')
      {
            echo "$mess_oui&nbsp;&nbsp;<INPUT type='radio' name='pass_mult' value='OUI' checked>&nbsp;&nbsp;&nbsp;&nbsp;";
            echo "$mess_non&nbsp;&nbsp;<INPUT type='radio' name='pass_mult' value='NON'>";
      }
      echo "<br>";
      echo "</TD></TR>";
      echo "<TR><TD nowrap><B>$msq_acquit_form</B></TD><TD nowrap>";
      if ($acquit== '')
      {
            echo ucfirst(strtolower($msq_apprenant))."&nbsp;&nbsp;<INPUT type='radio' name='acquit' value='APPRENANT'>&nbsp;&nbsp;&nbsp;&nbsp;";
            echo ucfirst(strtolower($msq_formateur))."&nbsp;&nbsp;<INPUT type='radio' name='acquit' value='FORMATEUR_REFERENT'>&nbsp;&nbsp;&nbsp;&nbsp;";
            echo ucfirst(strtolower($msq_ress))."&nbsp;&nbsp;<INPUT type='radio' name='acquit' value='RESSOURCE' onclick=\"window.alert('$mess_avrt_act_ress');\">";
      }elseif ($acquit== 'APPRENANT'){
            echo ucfirst(strtolower($msq_apprenant))."&nbsp;&nbsp;<INPUT type='radio' name='acquit' value='APPRENANT' checked>&nbsp;&nbsp;&nbsp;&nbsp;";
            echo ucfirst(strtolower($msq_formateur))."&nbsp;&nbsp;<INPUT type='radio' name='acquit' value='FORMATEUR_REFERENT'>&nbsp;&nbsp;&nbsp;&nbsp;";
            echo ucfirst(strtolower($msq_ress))."&nbsp;&nbsp;<INPUT type='radio' name='acquit' value='RESSOURCE' onclick=\"window.alert('$mess_avrt_act_ress');\">";
      }elseif ($acquit== 'FORMATEUR_REFERENT'){
            echo ucfirst(strtolower($msq_apprenant))."&nbsp;&nbsp;<INPUT type='radio' name='acquit' value='APPRENANT'>&nbsp;&nbsp;&nbsp;&nbsp;";
            echo ucfirst(strtolower($msq_formateur))."&nbsp;&nbsp;<INPUT type='radio' name='acquit' value='FORMATEUR_REFERENT' checked>&nbsp;&nbsp;&nbsp;&nbsp;";
            echo ucfirst(strtolower($msq_ress))."&nbsp;&nbsp;<INPUT type='radio' name='acquit' value='RESSOURCE' onclick=\"window.alert('$mess_avrt_act_ress');\">";
      }elseif ($acquit== 'RESSOURCE'){
            echo ucfirst(strtolower($msq_apprenant))."&nbsp;&nbsp;<INPUT type='radio' name='acquit' value='APPRENANT'>&nbsp;&nbsp;&nbsp;&nbsp;";
            echo ucfirst(strtolower($msq_formateur))."&nbsp;&nbsp;<INPUT type='radio' name='acquit' value='FORMATEUR_REFERENT'>&nbsp;&nbsp;&nbsp;&nbsp;";
            echo ucfirst(strtolower($msq_ress))."&nbsp;&nbsp;<INPUT type='radio' name='acquit' value='RESSOURCE' checked>";
      }
      echo "<br></TD></TR>";
      echo "<TR><TD nowrap><B>$msq_dev_act_form</B></TD><TD nowrap>";
      if ($dev_a_rendre == 'OUI'){
            echo "$mess_oui&nbsp;&nbsp;<INPUT type='radio' name='dev_a_rendre' value='OUI' checked>&nbsp;&nbsp;&nbsp;&nbsp;";
            echo "$mess_non&nbsp;&nbsp;<INPUT type='radio' name='dev_a_rendre' value='NON'>";
      }elseif ($dev_a_rendre == 'NON'){
            echo "$mess_oui&nbsp;&nbsp;<INPUT type='radio' name='dev_a_rendre' value='OUI'>&nbsp;&nbsp;&nbsp;&nbsp;";
            echo "$mess_non&nbsp;&nbsp;<INPUT type='radio' name='dev_a_rendre' value='NON' checked>";
      }else{
            echo "$mess_oui&nbsp;&nbsp;<INPUT type='radio' name='dev_a_rendre' value='OUI'>&nbsp;&nbsp;&nbsp;&nbsp;";
            echo "$mess_non&nbsp;&nbsp;<INPUT type='radio' name='dev_a_rendre' value='NON'>";
      }
      echo "<br></TD></TR>";
      echo "<TR><TD nowrap><B>$msq_not_act_form</B></TD><TD nowrap>";
      if ($notation == 'OUI'){
            echo "$mess_oui&nbsp;&nbsp;<INPUT type='radio' name='notation' value='OUI' checked>&nbsp;&nbsp;&nbsp;&nbsp;";
            echo "$mess_non&nbsp;&nbsp;<INPUT type='radio' name='notation' value='NON'>";
      }elseif ($notation == 'NON'){
            echo "$mess_oui&nbsp;&nbsp;<INPUT type='radio' name='notation' value='OUI'>&nbsp;&nbsp;&nbsp;&nbsp;";
            echo "$mess_non&nbsp;&nbsp;<INPUT type='radio' name='notation' value='NON' checked>";
      }else{
            echo "$mess_oui&nbsp;&nbsp;<INPUT type='radio' name='notation' value='OUI'>&nbsp;&nbsp;&nbsp;&nbsp;";
            echo "$mess_non&nbsp;&nbsp;<INPUT type='radio' name='notation' value='NON'>";
      }
      echo "</TD></TR>";
//dey Dfoad  formulaire
          $req_typdev = mysql_result(mysql_query("select count(*) from activite_devoir where actdev_act_no = $id_act"),0);
          $dev_act = "";
          if ($req_typdev > 0)
          {
              $dev_act = GetDataField ($connect,"select actdev_dev_lb from activite_devoir where
                                            actdev_act_no = $id_act","actdev_dev_lb");
          }
          else
              $dev_act = "Pas de devoir";
          echo "<TR><TD style='font-weight:bold;'>Type de devoir</td>";
          echo "<td><select class='SELECT' id='typdev' name='typdev' style='font-weight:bold;font-size:13px;'>";
          echo "<option value='$dev_act'>$dev_act</option>";
          if ($dev_act != 'Pas de devoir')
             echo "<option value='Pas de devoir'>Pas de devoir</option>";
          if ($dev_act != 'Autocorrectif')
             echo "<option value='Autocorrectif'>Autocorrectif</option>";
          if ($dev_act != 'Correction')
             echo "<option value='Correction'>Correction</option>";
          if ($dev_act != 'A renvoyer')
             echo "<option value='A renvoyer'>A renvoyer</option>";
          /*if ($dev_act != 'xApi TinCan')
             echo "<option value='xApi TinCan'>xApi TinCan</option>";*/
          echo "</select>";
          echo "</TD></TR>";
    }
          echo "<TR><TD>&nbsp;</TD><TD align='left' height='50'> ";
      if (($auteur == $id_user || $typ_user == 'ADMINISTRATEUR') && $modif_act == 1){
         echo "<A HREF=\"javascript:checkForm(document.form1);\" ".
         "onClick=\"TinyMCE.prototype.triggerSave();\" ".
         "onmouseover=\"img1.src='images/fiche_identite/boutvalidb$suffixer.gif';return true;\" ".
         "onmouseout=\"img1.src='images/fiche_identite/boutvalid$suffixer.gif'\">".
        "<IMG NAME=\"img1\" SRC=\"images/fiche_identite/boutvalid$suffixer.gif\" BORDER='0' ".
         "onLoad=\"tempImg=new Image(0,0); tempImg.src='images/fiche_identite/boutvalidb$suffixer.gif'\"></A>";
  }
  echo "</TD></TR></TABLE></TD></TR></FORM></TABLE></TD></TR></TABLE>";
  exit;
} //fin if ($action_act == 1)
$ext = '';
include('include/prerequis.inc.php');
//include 'click_droit.txt';
echo "<div id='mien' class='cms'></div>";
echo "</BODY></HTML>";
?>