<?php
/****************************************** ***************
*                                                         *
* Copyright  formagri/cnerta/eduter/enesad                *
* Dey Bendifallah                                         *
* Ce script fait partie intégrante du LMS Formagri.     *
* Il peut étre modifié ou utilisé é d'autres fins.*
* Il est libre et sous licence GPL                        *
* Les auteurs n'apportent aucune garantie                 *
*                                                         *
**********************************************************/

if (!isset($_SESSION)) session_start();
include ("include/UrlParam2PhpVar.inc.php");
if (!isset($_SESSION['id_user']) || $_SESSION['id_user'] == "")
{
  exit();
}
require 'graphique/admin.inc.php';
require 'fonction.inc.php';
require "lang$lg.inc.php";
require "fonction_html.inc.php";
require "class/class_module.php";
require ('class/Class_Rss.php');
require ('langues/module.inc.php');
dbConnect();
$date_dujour = date ("Y-m-d");
$html = "";
include 'style.inc.php';
$seqduref = (GetDataField ($connect,"select param_etat_lb from param_foad where param_typ_lb = 'seqduref'","param_etat_lb") == 'OUI' ) ? 1 : 0;
if (isset($_SESSION['acces']))
{
  unset($_SESSION['acces']);
  $requete_seq = $requete_sequence;
  $_SESSION['requete_sequence'] = $requete_sequence;
}
if (isset($accede) && $accede == "_entree")
  unset($_SESSION['accede']);
//Fichier gérant les séquences et les activités
if (isset($id_ref_seq) && $id_ref_seq > 0 && !isset($activite))
{
  $referentiel = GetDataField ($connect,"select ref_nomabrege_lb from referentiel where ref_cdn = $id_ref_seq","ref_nomabrege_lb");
  $nom_referentiel = GetDataField ($connect,"select ref_desc_cmt  from referentiel where ref_cdn = $id_ref_seq","ref_desc_cmt");
  if ($nom_referentiel == "")
    $nom_referentiel = GetDataField ($connect,"select ref_nomabrege_lb from referentiel where ref_cdn = $id_ref_seq","ref_nomabrege_lb");
    $nbr_caract = strlen($nom_referentiel);
    $nom_ref = $nom_referentiel;
    $sous_titre= "&nbsp;<B>$msq_ref :</B> $referentiel -->&nbsp;<small>".html_entity_decode($nom_ref,ENT_QUOTES,'iso-8859-1')."</small>";
}
elseif (isset($id_ref_seq) && $id_ref_seq == 0 && !isset($activite) && !isset($prem))
    $sous_titre= "&nbsp;<B>$seq_hrs_ref</B>";
if ((isset($creation) && $creation == 1) || (isset($choix_ref) && $choix_ref == 1)) {
    $message = "<center><B>$msq_crea</B></center>";
}
$nom_user = $_SESSION['name_user'];
$etat_fav = GetDataField($connect,"select param_etat_lb from param_foad where param_typ_lb='favoris'","param_etat_lb");
if (isset($aut_ajx) && $aut_ajx > 0)
{
   $_SESSION['aut_ajx'] = $aut_ajx;
   $_SESSION['liste_act_seq'] = $liste_act_seq;
}
?>
<script type="text/javascript" src="OutilsJs/lib/ajax/ajax_cms.js"></script>
<div id="affiche" class="Status"></div>
<div id="mon_contenu" class="cms"
        onClick="javascript:$(document).ready(function() {if ($.browser.msie) {$('#mon_contenu').hide();}else{$('#mon_contenu').hide('slow');}})"
        <?php echo "title=\"$mess_clkF\">";?>
</div>
<?php
if (isset($_GET['dupli_seq']) && $_GET['dupli_seq'] == 1 &&  (!isset($copier_seq) || (isset($copier_seq) && $copier_seq= '')))
{
  ?>
  <SCRIPT language=JavaScript>
    function checkForm(frm)
        {
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
    function isEmpty(elm)
        {
      var elmstr = elm.value + "";
      if (elmstr.length == 0)
       return true;
     return false;
    }
    function isVide(elm)
        {
               if(!elm[0].checked && !elm[1].checked){
                    return true;
               }
      return false;
    }
  </SCRIPT>
  <?php
    entete_concept("liste_seq.inc.php",$mess_dupli_tit);
    echo aide_simple("sequence");
    $seq_query = requete("*","sequence","seq_cdn = $id_seq");
    $nb_seq = mysql_num_rows ($seq_query);
    $DataSeq = mysql_fetch_object($seq_query);
    $nom = $DataSeq->seq_titre_lb;
    $desc = html_entity_decode($DataSeq->seq_desc_cmt,ENT_QUOTES,'iso-8859-1');
    $mots_clef = html_entity_decode($DataSeq->seq_mots_clef,ENT_QUOTES,'iso-8859-1');
    $duree = $DataSeq->seq_duree_nb;
    $droit_voir = $DataSeq->seq_publique_on;
    $type_seq = $DataSeq->seq_type_on;
    $ordre_act = $DataSeq->seq_ordreact_on;
    $mode_seq = $DataSeq->seq_type_lb;
    echo "<FORM NAME='form1' ACTION=\"sequence_entree.php?liste=$liste&parcours=$parcours&id_parc=$id_parc&id_seq=$id_seq&id_ref_seq=$id_ref_seq&dupli_seq=1&copier_seq=1&droit_voir=$droit_voir&duree=$duree\" METHOD='POST' target='main'>";
    echo "<INPUT TYPE='HIDDEN' name='nom' value=\"$nom\">";
    echo "<INPUT TYPE='HIDDEN' name='mode_seq' value=\"$mode_seq\">";
    echo "<INPUT TYPE='HIDDEN' name='mots_clef' value='none'>";
    echo "<TR><TD colspan='2'><TABLE width='100%' cellspacing='1' cellpadding='3'>";
    if ($sous_titre != '')
       echo "<TR><TD colspan='2' class='sous_titre'>$sous_titre</TD></TR>";
    echo "<TR><TD colspan='2' valign='top'><Font size='2'><B>$mess_modif_dupli</B></TD></TR>";
    echo "<TR><TD nowrap><B>$msq_titre</B></TD>";
    echo "<TD nowrap><INPUT TYPE='TEXT'class='INPUT' name='titre' size='75' value=\"$nom\" align='middle'></TD></TR>";
    echo "<TR><TD nowrap><B>$msq_desc_seq</B></TD>";
    echo "<TD nowrap><TEXTAREA name='description' align='middle' rows='6' cols='80' class='TEXTAREA'>$desc</TEXTAREA><br></TD></TR>";
//    echo "<TR><TD width='33%'><B>$mess_motclef</B></TD><TD align=left>";
//    echo "<TEXTAREA class='TEXTAREA' name='mots_clef' align='middle' rows='5' cols='80'>$mots_clef</TEXTAREA></TD></TR>";
    echo "<TR><TD nowrap><B>$mess_extension</B></TD>";
    echo "<TD nowrap><INPUT type='text' class='INPUT' name='extension' size='20'>&nbsp;&nbsp;&nbsp;&nbsp;";
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
    echo "<TR height=50><TD>&nbsp;</TD><TD align='left' valign=center><A HREF=\"javascript:checkForm(document.form1);\" onClick=\"TinyMCE.prototype.triggerSave();\" onmouseover=\"img1.src='images/fiche_identite/boutvalidb$suffixer.gif';return true;\" onmouseout=\"img1.src='images/fiche_identite/boutvalid$suffixer.gif'\">".
         "<IMG NAME=\"img1\" SRC=\"images/fiche_identite/boutvalid$suffixer.gif\" BORDER='0' onLoad=\"tempImg=new Image(0,0); tempImg.src='images/fiche_identite/boutvalidb$suffixer.gif'\"></A>";
    echo "</TD></TR></form></TABLE>";
    fin_tableau($html);
  exit;
}
if (isset($dupli_seq) && $dupli_seq == 1 && isset($_GET['copier_seq']) && $_GET['copier_seq'] == 1)
{
    $id_new_seq = Donne_ID ($connect,"select max(seq_cdn) from sequence");
    if ($extension != "")
        $titre .=" $extension";
    else
        $titre .=" ($nom_user)";
    $ordre_act = ($ordre_act == 1) ? "OUI" : "NON";
    $titre = str_replace("\"","'",html_entity_decode($titre,ENT_QUOTES,'iso-8859-1'));
    $description = DelAmp(htmlentities($description,ENT_QUOTES,'iso-8859-1'));
    $mots_clef = DelAmp(htmlentities($mots_clef,ENT_QUOTES,'iso-8859-1'));
    $insert_new_seq = mysql_query ("INSERT INTO sequence VALUES ($id_new_seq,\"$titre\",\"$description\",\"$mots_clef\",\"$ordre_act\",$duree,$id_user,\"$date_dujour\",\"$date_dujour\",$droit_voir_seq,$type_seq,'$mode_seq')");
    $insert_rss = rss :: ajout('sequence',$id_user,$id_new_seq);
    $id_ref_seq = GetDataField ($connect,"SELECT seqref_referentiel_no FROM sequence_referentiel WHERE seqref_seq_no = $id_seq","seqref_referentiel_no");
    $id_seqref = Donne_ID ($connect,"SELECT MAX(seqref_cdn) FROM sequence_referentiel");
    $autre_ref = mysql_query ("INSERT INTO sequence_referentiel VALUES ($id_seqref,$id_new_seq,$id_ref_seq)");
    if (strstr($mode_seq,"SCORM"))
        $act_query = requete_order("*","scorm_module","mod_seq_no = $id_seq","mod_cdn");
    else
        $act_query = requete_order("*","activite","act_seq_no = $id_seq AND ".
                               "(act_publique_on = 1 OR (act_publique_on=0 AND act_auteur_no=$id_user))"
                               ,"act_ordre_nb");
     $Nb_act_seq = mysql_num_rows ($act_query);
    if ($Nb_act_seq == 0) 
       echo "$msq_noact<br><br>";
    else
    {
       if (strstr($mode_seq,"SCORM"))
       {
            while ($DataSco = mysql_fetch_object($act_query)){
               $id_sco = $DataSco->mod_cdn;
               $nommer = str_replace("\"","'",$DataSco->mod_titre_lb);
               $desc = str_replace("\"","'",DelAmp($DataSco->mod_desc_cmt));
               $consigne = str_replace("\"","'",DelAmp($DataSco->mod_consigne_cmt));
               $clef = str_replace("\"","'",DelAmp($DataSco->mod_motclef_lb));
                if ($extension != "")
                  $nommer .=" $extension";
                else
                  $nommer .=" ($nom_user)";
                $id_new_mod = Donne_ID ($connect,"SELECT MAX(mod_cdn) FROM scorm_module");
                $insert_new_mod = mysql_query ("INSERT INTO scorm_module VALUES
                                                   ($id_new_mod,-1,$id_new_seq,
                                                   \"$nommer\",\"$desc\",\"$consigne\",\"$clef\",
                                                   \"".$DataSco->mod_visible."\",\"".$DataSco->mod_duree_nb."\",".$DataSco->mod_niveau_no.",
                                                   \"".$DataSco->mod_launch_lb."\",\"".$DataSco->mod_numero_lb."\",\"".$DataSco->mod_ordre_no."\",
                                                   \"".$DataSco->mod_pere_lb."\",\"".$DataSco->mod_pere_no."\",\"".$DataSco->mod_content_type_lb."\",
                                                   \"".$DataSco->mod_prereq_lb."\",\"".$DataSco->mod_maxtimeallowed."\",\"".$DataSco->mod_timelimitaction."\",
                                                   \"".$DataSco->mod_datafromlms."\",\"".$DataSco->mod_masteryscore."\")");
            }
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
                                                ",\"".$nommer."\",\"".str_replace("\"","'",DelAmp($DataAct->act_consigne_cmt)).
                                                "\",\"".str_replace("\"","'",DelAmp($DataAct->act_commentaire_cmt)).
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
    }
    $lien = "sequence_entree.php?consult=1&parcours=$parcours&liste=$liste&id_seq=$id_new_seq&id_ref_seq=$id_ref_seq&miens=$miens";
    $lien = urlencode($lien);
    echo "<script language=\"JavaScript\">";
       echo " document.location.replace(\"trace.php?link=$lien\")";
    echo "</script>";
   exit();
}
if ($creation == 1)
{
    echo "<FONT SIZE='3'>$message</FONT><center>";
    $lien="referenciel.php?acces=_entree";
    $lien = urlencode($lien);
    echo "<DIV id='sequence'><A href=\"trace.php?link=$lien\" target='main'><font color='#000099'><b>$msq_choix_ref</b></FONT></A></DIV>";
}

if ($prem == 1)
{

  if (isset($liste) && $liste == 0)
  {
    $titre = "$mess_menu_gest_seq : ".strtolower($mess_menu_gest_seq_ref);
    entete_concept("liste_seq.inc.php",$titre);
    echo aide_simple("sequence");
    if ($sous_titre != '')
       echo "<TR><TD class='sous_titre'>$sous_titre</TD></TR>";
    $lien="referenciel.php?prem=1&acces=_entree";
    $lien = urlencode($lien);
    echo "<TR><TD align='left'>&nbsp;<P><DIV id='sequence'><a href=\"trace.php?link=$lien\" target='main'>$seq_chx_ref</a><P>";
    $lien="sequence_entree.php?choix_ref=1&id_ref=0";
    $lien = urlencode($lien);
    echo "<a href=\"trace.php?link=$lien\" target='main'>$seq_hrs_ref</a></DIV><P>";
    fin_tableau();
  }
  else
  {
     if ($liste == 1 && $medor == 1)
     {
        $letitre = "$mess_menu_gest_seq_liste_tts $mess_filtre";
        if ($keydesc == "" && $keytitre == "" && $keypub == "" && $type_lb == "" && $keypresc == "" )
        {
           $ordre_affiche = "lenom";
           $refer = 2;
           $medor = 0;
        }
        if ($keydesc != "" && $keytitre == "" && $keypub == "")
           $champ_rech = "(sequence.seq_desc_cmt like '%$keydesc%' OR sequence.seq_desc_cmt like '$keydesc%' OR sequence.seq_desc_cmt like '%$keydesc')";
        elseif ($keydesc == "" && $keytitre != "" && $keypub == "")
           $champ_rech = "(sequence.seq_titre_lb like '%$keytitre%' OR sequence.seq_titre_lb like '$keytitre%' OR sequence.seq_titre_lb like '%$keytitre')";
        elseif ($keydesc == "" && $keytitre == "" && $keypub != "")
           $champ_rech = "sequence.seq_publique_on = '$keypub'";
        elseif ($keydesc != "" && $keytitre != "" && $keypub == "")
           $champ_rech = "(sequence.seq_titre_lb like '%$keytitre%' OR sequence.seq_titre_lb like '$keytitre%' OR sequence.seq_titre_lb like '%$keytitre') AND (sequence.seq_desc_cmt like '%$keydesc%' OR sequence.seq_desc_cmt like '$keydesc%' OR sequence.seq_desc_cmt like '%$keydesc')";
        elseif ($keydesc == "" && $keytitre != "" && $keypub != "")
           $champ_rech = "sequence.seq_publique_on = '$keypub' AND (sequence.seq_titre_lb like '%$keytitre%' OR sequence.seq_titre_lb like '$keytitre%' OR sequence.seq_titre_lb like '%$keytitre')";
        elseif ($keydesc != "" && $keytitre == "" && $keypub != "")
           $champ_rech = "sequence.seq_publique_on = '$keypub' AND (sequence.seq_desc_cmt like '%$keydesc%' OR sequence.seq_desc_cmt like '$keydesc%' OR sequence.seq_desc_cmt like '%$keydesc')";
        elseif ($keydesc != "" && $keytitre != "" && $keypub != "")
           $champ_rech = "sequence.seq_publique_on = '$keypub' AND (sequence.seq_titre_lb like '%$keytitre%' OR sequence.seq_titre_lb like '$keytitre%' OR sequence.seq_titre_lb like '%$keytitre') AND (sequence.seq_desc_cmt like '%$keydesc%' OR sequence.seq_desc_cmt like '$keydesc%' OR sequence.seq_desc_cmt like '%$keydesc')";
        if ($keypresc == 1 && $champ_rech == "")
           $champ_rech .= " sequence.seq_type_on = 1";
        elseif ($keypresc == 1 && $champ_rech != "")
           $champ_rech .= " AND sequence.seq_type_on = 1";
        if (!empty($keypresc) && $keypresc == 0 && $champ_rech == "")
           $champ_rech .= " sequence.seq_type_on = 0";
        elseif (!empty($keypresc) && $keypresc == 0 && $champ_rech != "")
           $champ_rech .= " AND sequence.seq_type_on = 0";
        if ($type_lb == "SCORM" && $champ_rech != "")
           $champ_ajout = " AND sequence.seq_type_lb != 'NORMAL' ";
        elseif ($type_lb == "NORMAL" && $champ_rech != "")
           $champ_ajout = " AND sequence.seq_type_lb = 'NORMAL' ";
        elseif ($type_lb == "SCORM" && $champ_rech == "")
           $champ_ajout = " sequence.seq_type_lb != 'NORMAL' ";
        elseif ($type_lb == "NORMAL" && $champ_rech == "")
           $champ_ajout = " sequence.seq_type_lb = 'NORMAL' ";
        if ($champ_ajout != "")
           $champ_rech .= $champ_ajout;
        if ($ordre_affiche == "lenom")
          $requete_seq = "select * from sequence where $champ_rech order by seq_titre_lb asc";
        elseif ($ordre_affiche == "lauteur")
          $requete_seq ="select * from sequence,utilisateur where $champ_rech AND sequence.seq_auteur_no = utilisateur.util_cdn order by utilisateur.util_nom_lb asc";
        elseif ($ordre_affiche == "leref" && $refer > 0)
          $requete_seq = "select * from sequence,sequence_referentiel,referentiel where $champ_rech AND sequence.seq_cdn = sequence_referentiel.seqref_seq_no AND sequence_referentiel.seqref_referentiel_no = referentiel.ref_cdn AND sequence_referentiel.seqref_referentiel_no > 0 order by referentiel.ref_nomabrege_lb";
        elseif ($ordre_affiche == "leref" && $refer == 0)
          $requete_seq = "select * from sequence,sequence_referentiel,referentiel where $champ_rech AND sequence.seq_cdn = sequence_referentiel.seqref_seq_no AND sequence_referentiel.seqref_referentiel_no = 0 order by  seq_titre_lb asc";
     }
     if ($liste == 1 && $medor != 1 && $titre != '' && $star != 1)
        $letitre = $titre;
     elseif ($star == 1)
           $titre = "Mes sequences favorites";
     elseif ($titre == '')
     {
        if ($liste == 1 && $refer == 0 && $proprio == "" && $medor != 1 && $titre != '')
           $letitre = $mess_menu_gest_seq_liste_sref;
        elseif ($liste == 1 && $refer == 0 && $proprio == "" && $medor != 1 && $titre != '')
           $letitre = $mess_menu_gest_seq_liste_sref;
        elseif ($liste == 1 && $refer == 2 && $proprio  == "" && $medor != 1 && $titre != '')
           $letitre = $mess_menu_gest_seq_liste_tts;
        elseif ($liste == 1 && $miens == 1 && $medor != 1 && $titre != '')
           $letitre = $mess_menu_mes_seq;
     }
     if ($liste == 1 && $proprio > 0 && $medor != 1)
     {
        $nom_createur = GetDataField ($connect,"select util_nom_lb from utilisateur where util_cdn = $proprio","util_nom_lb");
        $prenom_createur = GetDataField ($connect,"select util_prenom_lb from utilisateur where util_cdn = $proprio","util_prenom_lb");
        $letitre = "$mess_menu_gest_seq_liste_tts $de $prenom_createur $nom_createur";
        if ($ordre_affiche == "lenom")
          $requete_seq = "select * from sequence where sequence.seq_auteur_no = '$proprio' order by seq_titre_lb asc";
        elseif ($ordre_affiche == "leref" && $refer > 0)
          $requete_seq = "select * from sequence,sequence_referentiel,referentiel where sequence.seq_auteur_no = '$proprio' AND sequence.seq_cdn = sequence_referentiel.seqref_seq_no AND sequence_referentiel.seqref_referentiel_no = referentiel.ref_cdn AND sequence_referentiel.seqref_referentiel_no > 0 order by referentiel.ref_nomabrege_lb";
        elseif ($ordre_affiche == "leref" && $refer == 0)
          $requete_seq = "select * from sequence,sequence_referentiel,referentiel where sequence.seq_auteur_no = '$proprio' AND sequence.seq_cdn = sequence_referentiel.seqref_seq_no AND sequence_referentiel.seqref_referentiel_no = referentiel.ref_cdn AND sequence_referentiel.seqref_referentiel_no > 0 order by referentiel.ref_nomabrege_lb";
     }

     if ($liste == 1 && $refer == 0 && $proprio == "" && $medor != 1)
     {
        //$letitre = "$mess_menu_gest_seq_liste_sref";
        if ($ordre_affiche == "lenom")
          $requete_seq = "select * from sequence,sequence_referentiel where sequence.seq_cdn = sequence_referentiel.seqref_seq_no AND sequence_referentiel.seqref_referentiel_no = 0 order by sequence.seq_titre_lb asc";
        elseif ($ordre_affiche == "lauteur")
          $requete_seq = "select * from sequence,sequence_referentiel,utilisateur where sequence.seq_cdn = sequence_referentiel.seqref_seq_no AND sequence_referentiel.seqref_referentiel_no = 0 AND sequence.seq_auteur_no = utilisateur.util_cdn order by utilisateur.util_nom_lb asc";
     }
     if ($liste == 1 && $refer == 1 && $proprio  == "" && $medor != 1)
     {
        //$letitre = "$mess_menu_gest_seq_liste_ref";
        if ($ordre_affiche == "lenom")
          $requete_seq = "select * from sequence,sequence_referentiel where sequence.seq_cdn = sequence_referentiel.seqref_seq_no AND sequence_referentiel.seqref_referentiel_no > 0 order by sequence.seq_titre_lb asc";
        elseif ($ordre_affiche == "lauteur")
          $requete_seq = "select * from sequence,sequence_referentiel,utilisateur where sequence.seq_cdn = sequence_referentiel.seqref_seq_no AND sequence_referentiel.seqref_referentiel_no > 0 AND sequence.seq_auteur_no = utilisateur.util_cdn order by utilisateur.util_nom_lb,sequence.seq_titre_lb asc";
        elseif ($ordre_affiche == "leref")
          $requete_seq = "select * from sequence,sequence_referentiel,referentiel where sequence.seq_cdn = sequence_referentiel.seqref_seq_no AND sequence_referentiel.seqref_referentiel_no > 0 AND sequence_referentiel.seqref_referentiel_no = referentiel.ref_cdn order by referentiel.ref_nomabrege_lb,sequence.seq_titre_lb asc";
     }
     if ($liste == 1 && $refer == 2 && $proprio  == "" && $medor != 1)
     {
        //$letitre = "$mess_menu_gest_seq_liste_tts";
        if ($ordre_affiche == "lenom")
        {
           if ($type_on == 1)
              $requete_seq = "select * from sequence where seq_type_on = 1 AND seq_publique_on = 1 order by seq_titre_lb";
           else
              $requete_seq = "select * from sequence order by sequence.seq_titre_lb asc";
        }elseif ($ordre_affiche == "lauteur")
          $requete_seq = "select * from sequence,utilisateur  where sequence.seq_auteur_no = utilisateur.util_cdn order by utilisateur.util_nom_lb,sequence.seq_titre_lb asc";
     }
     if ($liste == 1 && $miens == 1 && $medor != 1)
     {
        //$letitre = "$mess_menu_mes_seq";
        $requete_seq = "select * from sequence where sequence.seq_auteur_no = $id_user order by seq_titre_lb asc";
     }
     if ($liste == 1 && $star == 1)
     {
        $letitre = "Mes favoris";
        if ($ordre_affiche == "lenom")
          $requete_seq = "SELECT * from sequence,stars where stars.star_item_id=sequence.seq_cdn and stars.star_user_id= $id_user and stars.star_type_no=2 order by seq_titre_lb, seq_type_lb asc";
        elseif ($ordre_affiche == "lauteur")
          $requete_seq = "SELECT * from sequence,utilisateur,stars where sequence.seq_auteur_no = utilisateur.util_cdn and stars.star_item_id=sequence.seq_cdn and stars.star_user_id='$id_user' and stars.star_type_no=2 order by utilisateur.util_nom_lb asc,sequence.seq_titre_lb";
     }
     if ($titre != '' )
        $letitre = $titre;
    $seq_query = mysql_query ("$requete_seq");
    $nb_seq = mysql_num_rows ($seq_query);
    $affiche_seq = $nb_seq;
    if ($nb_seq == 0)
    {
       entete_concept("liste_seq.inc.php",$letitre);
       echo aide_simple("sequence");
       if ($sous_titre != '')
          echo "<TR><TD class='sous_titre'>$sous_titre</TD></TR>";
       echo "<TR><TD><b>$msq_noseq</b><P></TD></TR>";
       if ($proprio > 0)
           echo boutret(1,1);
       echo "</TABLE></TD></TR></TABLE>";
    }else
    {
       if (!isset($_SESSION['nbr_pgs_seq']))
          $nbr_pgs = GetDataField($connect,"select param_etat_lb from param_foad where param_typ_lb='nb_pg_seq'","param_etat_lb");
       else
          $nbr_pgs = $_SESSION['nbr_pgs_seq'];
       if ($nb_seq > $nbr_pgs)
       {
          $nb_pages = ceil($nb_seq/$nbr_pgs);
          $debut_liste = $nbr_pgs*$page;
          $le_debut = $debut_liste+1;
          if ($page > 0)
             $page_ret = $page-1;
          $page++;
          if ($debut_liste > $nb_seq)
          {
             $debut_liste = 0;
             $le_debut = 1;
             $page = 0;
          }
          if ($debut_liste == 0)
             $le_debut = 1;
       }
       if ($nb_seq > $nbr_pgs)
          $requete_seq .=" limit $debut_liste,$nbr_pgs";
       $_SESSION['requete_seq'] = $requete_seq;
       $lecho = (($debut_liste + $nbr_pgs) < $nb_seq) ? strval($le_debut)." - ".strval($debut_liste+$nbr_pgs) : strval($le_debut)." - $nb_seq";
       //$lecho = ((($debut_liste + $nbr_pgs) < $nb_seq) && (($debut_liste + $nbr_pgs) > ($nbr_pgs + 1))) ? strval($le_debut+1)." - ".strval($debut_liste+$nbr_pgs);
       $seq_query =  mysql_query($requete_seq);

       $nb_seq = mysql_num_rows($seq_query);
       if (isset($message) && $message != '')
         echo notifier($message);
       entete_concept("liste_seq.inc.php",$letitre);
       echo aide_simple("sequence");
       $bgcolor2 = '#2b677a';
       $bgcolor1 = '#F8F2E4';
       //---------------------------------------------------
       if (isset($medor) && $medor == 1){
         echo "<TR><TD colspan='2'><B>$mess_filtapp </B>  $msq_titre ";
         if ($keytitre != "") echo "<font color='#D45211'><B>$keytitre</B></FONT>, ";else echo "<B>$mess_nofiltre</B>, ";
         echo "$msq_desc ";
         if ($keydesc != "") echo "<font color='#D45211'><B>$keydesc</B></FONT>, ";else echo "<B>$mess_nofiltre</B>, ";
         echo "Type : ";
         if ($type_lb != '') echo "<font color='#D45211'><B>$type_lb</B></font>, ";elseif($type_lb == '') echo "<B>$mess_nofiltre</B>, ";
         echo "$mess_visdup : ";
         if ($keypub == 1) echo "<font color='#D45211'><B>$mess_oui</B></FONT>";elseif($keypub == 0 && $keypub != "") echo "<font color='#D45211'><B>$mess_non</B></FONT>";else echo "<B>$mess_nofiltre</B>";
         echo "&nbsp;&nbsp;&nbsp;$mess_presc : ";
         if ($keypresc == 1) echo "<font color='#D45211'><B>$mess_non</B></font>";elseif($keypresc == 0 && $keypresc != "") echo "<font color='#D45211'><B>$mess_oui</B></font>";else echo "<B>$mess_nofiltre</B>";
         echo "</TD></TR>";
       }
       echo "<TR><TD colspan='2'><table cellpadding='3' cellspacing='4' width='98%' border='0'>";
          $affiche_rech = "";
          $affiche_rech = "<TD align='left'><form name='form'>";
          $affiche_rech .= "<SELECT name='select' class='SELECT' onChange=javascript:appel_w(form.select.options[selectedIndex].value)>";
          $affiche_rech .= "<OPTION selected></OPTION>";
          $req_aut_seq = requete_order("DISTINCT seq_auteur_no,util_nom_lb","sequence as SEQ,utilisateur as UT","SEQ.seq_auteur_no = UT.util_cdn","UT.util_nom_lb");
          $nb_aut_seq = mysql_num_rows($req_aut_seq);
          while ($data = mysql_fetch_object($req_aut_seq)) {
             $nom_aut = $data -> util_nom_lb;
             $num_aut = $data -> seq_auteur_no;
             $lemien = ($num_aut == $id_user) ? $miens : "";
             $lien = "sequence_entree.php?prem=1&liste=1&ordre_affiche=lenom&medor=1&proprio=$num_aut&miens=$lemien&vient_de_search=$vient_de_search&refer=$refer";
             $lien =  urlencode($lien);
             $affiche_rech .= "<OPTION value='trace.php?link=$lien'>$nom_aut</OPTION>";
          }
          $affiche_rech .= "</SELECT></TD>";
       echo "<FORM name='form1' ACTION=\"sequence_entree.php?prem=1&liste=1&ordre_affiche=lenom&medor=1&vient_de_search=1&refer=$refer&miens=$miens&proprio=$proprio\" METHOD='POST' target='main'>";
       echo "<TR><TD nowrap colspan=7><B>$mrc_rech $mess_ParAutSuit</B></TD></TR>";
       echo "<TR>";
       if ($nb_aut_seq > 1)
          echo "<TD nowrap>$mrc_aut</TD>";
       echo "<TD nowrap>$msq_titre</TD><TD nowrap>$msq_desc</TD><TD nowrap>Type</TD><TD nowrap>$mess_visdup</TD><TD nowrap>$mess_presc</TD><TD nowrap>&nbsp;</TD></TR>";
       echo "<TR>";
       if ($nb_aut_seq > 1)
         echo $affiche_rech;
       echo "<TD nowrap><INPUT TYPE='text'  class='INPUT'   name='keytitre' size='20' align='middle'></TD>";
       echo "<TD nowrap><INPUT TYPE='text'  class='INPUT'   name='keydesc' size='20' align='middle'></TD>";
         echo "<TD nowrap><SELECT name='type_lb' class='SELECT'>";
         echo "<OPTION></OPTION>";
         echo "<OPTION value='NORMAL'>NORMAL</OPTION>";
         echo "<OPTION value='SCORM'>SCORM</OPTION>";
         echo "</SELECT></TD>";
         echo "<TD nowrap>";
         echo "<SELECT name='keypub' class='SELECT'>";
           echo "<OPTION></OPTION>";
           echo "<OPTION value='1'>$mess_oui</OPTION>";
           echo "<OPTION value='0'>$mess_non</OPTION>";
         echo "</SELECT></TD>";
         echo "<TD nowrap>";
         echo "<SELECT name='keypresc' class='SELECT'>";
           echo "<OPTION></OPTION>";
           echo "<OPTION value='0'>$mess_oui</OPTION>";
           echo "<OPTION value='1'>$mess_non</OPTION>";
         echo "</SELECT></TD>";
       echo "<TD align='center'><A HREF=\"javascript:document.form1.submit();\" onmouseover=\"img1.src='images/fiche_identite/boutvalidb$suffixer.gif';return true;\" onmouseout=\"img1.src='images/fiche_identite/boutvalid$suffixer.gif'\">".
       "<IMG NAME=\"img1\" SRC=\"images/fiche_identite/boutvalid$suffixer.gif\" BORDER='0' onLoad=\"tempImg=new Image(0,0); tempImg.src='images/fiche_identite/boutvalidb$suffixer.gif'\"></A>";
       echo "</TD></TR></form></TABLE>";
       echo "<TR height='5'><TD colspan='2'>&nbsp;</TD></TR>";
     //--------------------------------------------------------------------------
       echo "<TR><TD width='100%'><table cellpadding='6' width='100%'>";
       echo "<tr bgcolor=$bgcolor2>";
       $affiche_nb = ($affiche_seq > $nbr_pgs) ? "[$lecho] sur $affiche_seq" : "$affiche_seq";
       $choixNbAff = "<div id='cNA' style=\"float:left;padding-right:4px;cursor:pointer;\">".
                     "<input type='text' class='INPUT' id='NBS' name='nbr_pgs_seq' value='$nbr_pgs' size='2' maxlength='2' style='width:20px;' ".
                     bullet($msg_modNbPg,"","RIGHT","BELOW",180).
                     "onBlur=\"javascript:var nbs=getElementById(NBS);appelle_ajax('admin/modif_nb.php?nbrAff_seq='+NBS.value);\$(mien).empty();\" /></div>";
       //star
       if ($etat_fav == 'OUI')
       {
           $lien_star_search="sequence_entree.php?star=1&prem=$prem&liste=$liste&refer=$refer&ordre_affiche=$ordre_affiche&medor=$medor&type_lb=$type_lb&keytitre=$keytitre&keypub=$keypub&keydesc=$keydesc&keypresc=$keypresc&proprio=$proprio&vient_de_search=$vient_de_search&miens=$miens&page=$page_ret&debut_liste=$debut_liste&titre=".addslashes($letitre);
           $nb_star = mysql_num_rows(mysql_query("select * from stars where star_user_id='$id_user' and star_type_no=2"));
           if ($nb_star > 0)
           {
               $ajt_star = "<div id='starfull' style=\"float:left;padding-right:4px;cursor:pointer;\" " .
                           bullet($mess_menu_consult_fav_prop,"","RIGHT","BELOW",180)." onclick=\"javascript:parent.main.location.replace('trace.php?link=".urlencode($lien_star_search)."');\"><img src='images/starfull.gif' border=0></div>";
           }else
               $ajt_star = "<div id='starfull' style=\"float:left;padding-right:4px;cursor:pointer;\"></div>";
       }
        else
            $ajt_star = "";
       echo "<td height='20' align='left' nowrap>$ajt_star<div id='mpwo' style=\"float:left;padding-right:4px;padding-top:3px;\"><FONT COLOR=white><b>$msq_seq&nbsp;&nbsp;($affiche_nb)</div>";
       $affiche_bulle = bulle($mess_ord_alpha,"","LEFT","",150);
       if ($page > 1)
            echo " <div id='flcG' style=\"float:left;padding-right:4px;padding-top:5px;cursor:pointer;\">".
                 "<A HREF=\"sequence_entree.php?prem=$prem&liste=$liste&refer=$refer&ordre_affiche=$ordre_affiche&medor=$medor&type_lb=$type_lb&keytitre=$keytitre&keypub=$keypub&keydesc=$keydesc&keypresc=$keypresc&proprio=$proprio&vient_de_search=$vient_de_search&miens=$miens&page=$page_ret&debut_liste=$debut_liste&star=$star&titre=".addslashes($letitre)."\">".
                 "<img src='images/ecran-annonce/icogog.gif' border='0' title='$mess_page_prec'></A></div>";
       echo $choixNbAff;
       if ($nb_seq == $nbr_pgs && $affiche_seq > $debut_liste+$nbr_pgs)
            echo "<div id='flcD' style=\"float:left;padding-right:4px;padding-top:5px;cursor:pointer;\">".
                 "<A HREF=\"sequence_entree.php?prem=$prem&liste=$liste&refer=$refer&ordre_affiche=$ordre_affiche&medor=$medor&type_lb=$type_lb&keytitre=$keytitre&keypub=$keypub&keydesc=$keydesc&keypresc=$keypresc&proprio=$proprio&vient_de_search=$vient_de_search&miens=$miens&page=$page&debut_liste=$debut_liste&titre=".addslashes($letitre)."&star=$star\">".
                 "<img src='images/ecran-annonce/icogod.gif' border='0' title='$mess_page_suiv'></A></div>";
        if ($ordre_affiche != "lenom" && $miens != 1){
          if ($proprio > 0)
            echo "&nbsp;<A HREF=\"sequence_entree.php?prem=1&liste=1&ordre_affiche=lenom&proprio=$proprio&vient_de_search=$vient_de_search&miens=$miens&refer=$refer&titre=".addslashes($letitre)."\"><img src='images/modules/tut_form/flechehaut.gif' border=0></A>";
          elseif ($medor == 1)
            echo "&nbsp;<A HREF=\"sequence_entree.php?prem=1&liste=$liste&refer=2&ordre_affiche=lenom&medor=1&keytitre=$keytitre&type_lb=$type_lb&keypub=$keypub&keypresc=$keypresc&keydesc=$keydesc&proprio=$proprio&vient_de_search=$vient_de_search&miens=$miens&titre=".addslashes($letitre)."\"".
                 "$affiche_bulle<img src='images/modules/tut_form/flechehaut.gif' border=0></A>";
          else
            echo "&nbsp;<A HREF=\"sequence_entree.php?prem=1&liste=$liste&refer=$refer&ordre_affiche=lenom&titre=".addslashes($letitre)."\"".
                 "$affiche_bulle<img src='images/modules/tut_form/flechehaut.gif' border=0></A>";
        }
        echo "</td>";
        if ($miens != 1 && $proprio == "")
          echo "<td  height='20' align='left'><FONT COLOR=white><b>$mrc_aut</b></FONT>";

        if ($ordre_affiche != "lauteur" && $miens != 1 && $proprio == "")
          echo "&nbsp;<A HREF=\"sequence_entree.php?prem=1&liste=$liste&ordre_affiche=lauteur&medor=$medor&type_lb=$type_lb&keytitre=$keytitre&keypub=$keypub&keydesc=$keydesc&keypresc=$keypresc&proprio=$proprio&vient_de_search=$vient_de_search&miens=$miens&refer=$refer&titre=".addslashes($letitre)."\"".
               "$affiche_bulle<img src='images/modules/tut_form/flechehaut.gif' border=0></A>";
        echo "</td>";
        if ($miens == 1 || $proprio > 0 || $medor == 1 || ($refer < 3 && $refer != 0)){
          echo "<td  height='20' align='left' nowrap><FONT COLOR=white><b>$msq_ref_ass</b></FONT>";
          if ($refer < 2 && $miens != 1){
             if ($ordre_affiche != "leref" && $miens != 1 && $medor != 1){
                if ($proprio > 0)
                   echo "&nbsp;<A HREF=\"sequence_entree.php?prem=1&liste=1&ordre_affiche=leref&proprio=$proprio&vient_de_search=$vient_de_search&miens=$miens&refer=$refer&titre=".addslashes($letitre)."\"".
                        "$affiche_bulle<img src='images/modules/tut_form/flechehaut.gif' border=0></A>";
                else
                   echo "&nbsp;<A HREF=\"sequence_entree.php?prem=1&liste=$liste&ordre_affiche=leref&vient_de_search=$vient_de_search&miens=$miens&refer=$refer&titre=".addslashes($letitre)."\"".
                        "$affiche_bulle<img src='images/modules/tut_form/flechehaut.gif' border=0></A>";
             }
          }
          echo "</td>";
       }else
          echo "</td>";
       echo "<TD align='left'><FONT COLOR=white><b>$mess_modif_base</b></FONT></TD>";
       echo "<TD align='left'><FONT COLOR=white><b>$mess_ag_supp</b></FONT></TD>";
       echo "<TD align='left'><FONT COLOR=white><b>$mess_dupliquer</b></FONT></TD>";
       echo "</TR>";
       $i=0;
       while ($i < $nb_seq) {
         $id_sequence = mysql_result ($seq_query,$i,"seq_cdn");
         $nom = mysql_result ($seq_query,$i,"seq_titre_lb");
         $seq_type = mysql_result ($seq_query,$i,"seq_type_on");
         $duree_seq_act = mysql_result ($seq_query,$i,"seq_duree_nb");
         $droit_voir = mysql_result ($seq_query,$i,"seq_publique_on");
         $mod_seq = mysql_result ($seq_query,$i,"seq_type_lb");
         $createur = mysql_result ($seq_query,$i,"seq_auteur_no");
         $description = html_entity_decode(mysql_result($seq_query,$i,"seq_desc_cmt"),ENT_QUOTES,'iso-8859-1');
         $description = str_replace ("'","\'",$description);
         $icono = "";
         $nom_createur = GetDataField ($connect,"select util_nom_lb from utilisateur where util_cdn = $createur","util_nom_lb");
         $prenom_createur = GetDataField ($connect,"select util_prenom_lb from utilisateur where util_cdn = $createur","util_prenom_lb");
              //star
              //$lien_star = "formation/star.php?numero=$numero&dl=dejalu$i&renonlu=1&ret=$ret&complement=$complement&i=$i";
              if ($etat_fav == 'OUI' && $seq_type == 0)
              {
                $nbr_star = mysql_num_rows(mysql_query("select * from stars where star_user_id='$id_user' and star_item_id='$id_sequence' and star_type_no=2"));
                if ($nbr_star > 0)
                {
                    $lien_star = "formation/star.php?numero=$id_sequence&dl=star$i&ajt=starfull&vider=1&i=$i&type=2";
                    $div_star = "<div id='star$i' class='star'>".
                              "<div id='lance$i' class='star' title=\"$msg_fav_ot\"".
                              "onclick=\"javascript:appelle_ajax('$lien_star'); ".
                              "\$('#mien').hide();\$('#star$i').html('');".
                              "addContent_star('$lien_star'); \"> ".
                              "<img src='images/starfull.gif' border=0></div></div>";
                 }
                 else
                 {
                    $lien_star = "formation/star.php?numero=$id_sequence&dl=star$i&ajt=starfull&remplir=1&i=$i&type=2";
                    $div_star = "<div id='star$i' class='star'>".
                              "<div id='lance$i' class='star' title=\"$mess_menu_ajout_favori\" ".
                              "onclick=\"javascript:appelle_ajax('$lien_star'); ".
                              "\$('#mien').hide();\$('#star$i').html(''); ".
                              "addContent_star('$lien_star');\"> " .
                              "<img src='images/starempty.gif' border=0></div></div>";
                  }
              }
              else
              {
                 $div_star = nbsp(1).image_decale("<img src='archives/spacer.gif' border='0'>",2);
              }
         if (strstr($mod_seq,"SCORM"))
             $count_act = mysql_query ("SELECT count(*) FROM scorm_module WHERE mod_seq_no=$id_sequence");
         else
             $count_act = mysql_query ("SELECT count(*) FROM activite WHERE act_seq_no=$id_sequence");
         $nb_act_seq = mysql_result($count_act,0);
         if ($nb_act_seq > 0 && $duree_seq_act > 0)
            $duree = duree_calc($duree_seq_act);
         $req_parc = mysql_query ("SELECT parcours.parcours_nom_lb FROM parcours,sequence_parcours WHERE sequence_parcours.seqparc_seq_no=$id_sequence AND sequence_parcours.seqparc_parc_no=parcours.parcours_cdn group by parcours.parcours_nom_lb order by parcours.parcours_nom_lb");
         $nb_parc_seq = mysql_num_rows ($req_parc);
         $la_serie = "";
         if ($nb_parc_seq > 0){
            $la_serie = "<B>".addslashes($seq_parc_exist)."</B>";
            $psi=0;
              while ($psi < $nb_parc_seq){
               $p_nom = mysql_result($req_parc,$psi,"parcours_nom_lb");
               $la_serie .="<LI>".$p_nom."</LI>";
               $psi++;
            }
         }
         if ($nb_act_seq > 0)
            $la_serie .= "<B>$msq_detail_seq</B><BR>$msq_nbr_act : $nb_act_seq";
         if ($duree_seq_act > 0)
            $la_serie .= "<BR>$msq_duree_seq : $duree";
         $seq_ref_query = mysql_query ("select seqref_referentiel_no from sequence_referentiel where seqref_seq_no = $id_sequence");
         $nb_seq_ref = mysql_num_rows ($seq_ref_query);
         if ($nb_seq_ref == 0)
         {
           $ajout_ref = mysql_query("INSERT INTO sequence_referentiel VALUES ('',$id_sequence,0)");
           $seq_ref_query = mysql_query ("select seqref_referentiel_no from sequence_referentiel where seqref_seq_no = $id_sequence");
           $nb_seq_ref = mysql_num_rows ($seq_ref_query);
         }
         if ($nb_seq_ref > 0)
         {
           $id_ref_seq = GetDataField ($connect,"select seqref_referentiel_no from sequence_referentiel where seqref_seq_no = $id_sequence","seqref_referentiel_no");
           if ($id_ref_seq == 0)
             $nom_referentiel = $msq_sl;
           else
             $nom_referentiel = GetDataField ($connect,"select ref_nomabrege_lb from referentiel where ref_cdn = $id_ref_seq","ref_nomabrege_lb");
           $lien = "sequence_entree.php?utilisateur=$utilisateur&liste=1&flg_seq=$i&id_seq=$id_sequence&consult=1&id_ref_seq=$id_ref_seq&apprenant=$apprenant&refer=$refer&miens=$miens&vient_de_search=$vient_de_search&proprio=$proprio";
           $lien = urlencode($lien);
           echo couleur_tr($i+1,'');
           $combi = ($droit_voir == 0 && $seq_type == 1) ? 0 : 1;
           if ($combi == 1 || (isset($seq_auteur) && $seq_auteur == $id_user))
           {
              $la_suite = '';
              echo "<TD valign='top'><div style='float:left;'>$div_star<a href=\"trace.php?link=$lien\"";
           }
           else
           {
              $la_suite = $mess_construction;
              echo "<TD valign='top'><div style='float:left;'>$div_star<a href=\"javascript:void(0)\"";
           }
           echo " target='main'";
           if ($la_serie != "")
              echo bulle($la_serie,"<B>$la_suite</B","RIGHT","",312);
           else
              echo ">";
           echo "&nbsp;$nom</A></div></TD>";
           if ($miens != 1 && $proprio == "")
           {
             $lien="sequence_entree.php?liste=1&prem=1&ordre_affiche=lenom&vient_de_search=$vient_de_search&miens=$miens&proprio=$createur&refer=$refer";
             $lien = urlencode($lien);
             echo "<TD nowrap valign='top'><A HREF=\"trace.php?link=$lien\" TITLE = \"$msq_consult_seq $de $prenom_createur $nom_createur\">$prenom_createur $nom_createur</A></TD>";
           }
           if ($refer > 0  || $miens == 1 || $proprio > 0 || $medor == 1)
              echo "<TD valign='top'>".html_entity_decode($nom_referentiel,ENT_QUOTES,'ISO-8859-1')."</TD>";
         }
         else
         {
           $nbr_ref = 0;
           while ($nbr_ref < $nb_seq_ref)
           {
             $id_ref_seq = mysql_result($seq_ref_query,$nbr_ref,"seqref_referentiel_no");
             if ($id_ref_seq == 0)
               $nom_referentiel = $msq_sl;
             else
               $nom_referentiel = GetDataField ($connect,"select ref_nomabrege_lb from referentiel where ref_cdn = $id_ref_seq","ref_nomabrege_lb");
             $lien="sequence_entree.php?liste=1&flg_seq=$i&id_seq=$id_sequence&consult=1&id_ref_seq=$id_ref_seq&apprenant=$apprenant&refer=$refer&miens=$miens";
             $lien = urlencode($lien);
             echo couleur_tr($i+1,'');
             echo "<TD valign='top'><DIV id='sequence'><a href=\"trace.php?link=$lien\"";
             echo " target='main'";
             $le_message = "<B>$seq_parc_exist</B> : $la_serie";
             if ($la_serie != "")
                 echo bulle($le_message,"<center>$msq_desc_titre</center>","RIGHT","",310);
             else
                 echo ">";
             echo "$nom</a></DIV></TD>";
             if ($proprio == "" && $miens != 1)
               echo "<TD valign='top' nowrap>".$prenom_createur." ".$nom_createur."</TD>";
             echo "<TD valign='top'>".html_entity_decode($nom_referentiel,ENT_QUOTES,'ISO-8859-1')."</TD>";
            $nbr_ref++;
           }
         }
         if ($createur == $id_user || $typ_user == 'ADMINISTRATEUR')
         {
            $lien="sequence_entree.php?liste=$liste&action_seq=1&id_seq=$id_sequence&nb_act_seq=$nb_act_seq&duree=$duree_new&ordre_act=$ordre_act&id_ref_seq=$id_ref_seq&droit_voir=$droit_voir&miens=$miens";
            $lien = urlencode($lien);
            echo "<td  align='middle' valign='top'><a href=\"trace.php?link=$lien\" target='main'".
            bulle($mess_modif_seq,"","LEFT","",110).
            "<IMG SRC=\"images/repertoire/icoGrenomfich20.gif\" BORDER=0></A></td>";
            $id_seq3_query = mysql_query ("select count(seqparc_cdn) from sequence_parcours where seqparc_seq_no = $id_sequence");
            $nbr_seq3 = mysql_result($id_seq3_query,0);
            $requete_grp = mysql_query ("select * from groupe");
            $nb_grp_parc = mysql_num_rows($requete_grp);
            if ($nb_grp_parc > 0)
            {
                $gp=0;$nb_presc_seq_parc=0;
                while ($gp < $nb_grp_parc)
                {
                       $id_grp = mysql_result($requete_grp,$gp,"grp_cdn");
                       $nom_grp = mysql_result($requete_grp,$gp,"grp_nom_lb");
                       $nb_presc = mysql_num_rows(mysql_query("select * from prescription_$id_grp where presc_seq_no=$id_sequence"));
                       $nb_presc_seq_parc += $nb_presc;
                       $gp++;
                }
            }
            if ($nb_presc_seq_parc == 0 && $nbr_seq3 == 0)
            {
               $lien="sequence_entree.php?liste=$liste&consult=1&supp=1&id_seq=$id_sequence&id_ref_seq=$id_ref_seq&refer=$refer&miens=$miens";
               $lien = urlencode($lien);
               echo "<td align='center' valign='top'><a href=\"javascript:void(0);\" onclick=\"javascript:return(confm('trace.php?link=$lien'));\" target='main'".bulle($msq_etiq_sup,"","LEFT","",180).
                    "<IMG SRC=\"images/messagerie/icopoubelressour.gif\" border='0'></A></td>";
            }
            else
            {
                   if ($nb_presc_seq_parc > 0)
                      $msg_interdit = $msq_nosup_seq;
                   elseif ($nb_presc_seq_parc == 0 && $nbr_seq3 > 0)
                      $msg_interdit = $mess_seq_nosup_parc;
                   $mess_interdiction = "<td align='center' valign='top'><IMG SRC=\"images/repertoire/icoptiinterdit.gif\" border='0' ".
                                        bulle($msg_interdit,"","LEFT","",180)."</td>";

                   echo  $mess_interdiction;
            }
         }else
            echo "<TD>&nbsp;</TD><TD>&nbsp;</TD>";
         if ($droit_voir == 1){
               $lien="sequence_entree.php?liste=$liste&dupli_seq=1&id_seq=$id_sequence&id_ref_seq=$id_ref_seq&miens=$miens";
               $lien = urlencode($lien);
               echo "<TD nowrap align='center' valign='top'><A href=\"trace.php?link=$lien\" target='main'".bulle($msq_dupli_seq,"","LEFT","",110).
                    "<IMG SRC=\"images/repertoire/icoptiedit.gif\" border='0'></A></TD>";
         }else
            echo "<TD>&nbsp;</TD>";
         echo "</TR>";
       $i++;
       }
       echo "</TABLE></TD></TR></TABLE>";
    }
  }
}
//Creation sequence
//On vient de choisir le referentiel
if ($choix_ref == 1) {

/*
*/
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
    $titre = $msq_cree_seq;
    entete_concept("liste_seq.inc.php",$titre);
    echo aide_simple("sequence");
    echo "<FORM NAME='form1' action=\"sequence_entree.php?activite=1&insert_seq=1&parcours=$parcours&liste=$liste&id_parc=$id_parc&id_ref_seq=$id_ref_seq&seq=$seq&typ_prereq=$typ_prereq&note_max=$note_max&note_min=$note_min&act=$act&miens=$miens\"  method='POST' target='main'>";
    echo "<TR><TD colspan='2'><TABLE width=100%>";    //laisser la possibilite d'associer d'autres referentiels
    $referentiel = $msq_sra; //$msq_slchr;
    echo "<TR><TD colspan='2'><Font size='2'><B>$msq_act_ref</B> $referentiel</TD></TR>";
    echo "<TR><TD nowrap width=35%><B>$msq_titre</B></TD><TD nowrap><INPUT TYPE='text'  class='INPUT' name='titre' size ='75' align='middle' ></TD></TR>";
    echo "<TR><TD nowrap width=35%><B>$msq_desc</B></TD>";
    echo "<TD nowrap width=35%><TEXTAREA name='description' class='TEXTAREA' align='middle' rows='6' cols='80'>$description</TEXTAREA><br></TD></TR>";
//       echo "<TR><TD width='33%'><B>$mess_motclef</B></TD><TD align=left>";
//       echo "<TEXTAREA class='TEXTAREA' name='cles_seq' align='middle' rows='5' cols='80'>$cles_seq</TEXTAREA></TD></TR>";
    echo "<input type='hidden' name='cles_seq' value=\"$cles_seq\">";
    echo "<TR><TD nowrap><TABLE cellpadding='0' cellspacing='0' border='0'>";
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
    echo "<TR height='50'><TD></TD><TD align='left' valign= 'center'><A HREF=\"javascript:checkForm(document.form1);\" onClick=\"TinyMCE.prototype.triggerSave();\" onmouseover=\"img1.src='images/fiche_identite/boutvalidb$suffixer.gif';return true;\" onmouseout=\"img1.src='images/fiche_identite/boutvalid$suffixer.gif'\">".
         "<IMG NAME=\"img1\" SRC=\"images/fiche_identite/boutvalid$suffixer.gif\" BORDER='0' onLoad=\"tempImg=new Image(0,0); tempImg.src='images/fiche_identite/boutvalidb$suffixer.gif'\"></A>";
    echo "</TD></TR></FORM></TABLE></TD></TR></TABLE>";
} //fin if ($choix_ref == 1)

//Creation activite et insertion sequence

if ($activite == 1)
{
   //Verifier que les champs obligatoires sont bien remplis
   if ($titre == "" && $insert_seq == 1)
   {
      $letitre= "$msq_oubli_tit_seq";
      entete_concept("liste_seq.inc.php",$letitre);
      echo aide_simple("sequence");
      if ($sous_titre != '')
          echo "<TR><TD class='sous_titre'>$sous_titre</TD></TR>";
      $lien="sequence_entree.php?choix_ref=1&id_ref_seq=$id_ref_seq&parcours=$parcours&liste=$liste&id_parc=$id_parc&miens=$miens";
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
        $id_seq = Donne_ID ($connect, "select max(seq_cdn) from sequence");
        $insert_seq_query = mysql_query ("insert into sequence values ($id_seq,\"".str_replace("\"","'",$titre)."\",\"".htmlentities($description,ENT_QUOTES,'iso-8859-1')."\",\"".htmlentities($cles_seq,ENT_QUOTES,'iso-8859-1')."\",\"$ordre_act\",0,'$id_user',\"$date_dujour\",\"$date_dujour\",'$droit_voir_seq','$seq_type','NORMAL')");
        if ($seqduref == 1 && (isset($_POST['dureeRef']) && $_POST['dureeRef'] > 0) )
           $insertSeqReq = mysql_query ("insert into seq_duree_ref values (NULL,$id_seq,'".$_POST['dureeRef']."')");
        $insert_rss = rss :: ajout('sequence',$id_user,$id_seq);
        $seq_ref_query = mysql_query ("select seqref_referentiel_no from sequence_referentiel where seqref_seq_no = $id_seq");
        $nb_seq_ref = mysql_num_rows ($seq_ref_query);
        if ($nb_seq_ref == 0)
        {
           $id_seqref = Donne_ID ($connect,"select max(seqref_cdn) from sequence_referentiel");
           $insert_refseq_query = mysql_query ("insert into sequence_referentiel values ($id_seqref,$id_seq,$id_ref_seq)");
        }
        $titre = "";
        $droit_voir="";
      }// fin if ($insert_seq == 1)
      if ($insert_act == 1)
      {
         //Verifier que champs obligatoires sont remplis
         if ($titre == '')
         {
            $letitre= $msq_creat_act;
            entete_concept("liste_seq.inc.php",$letitre);
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
            if ($id_seq > 0)
            {
              $duree_seq = GetDataField ($connect,"select seq_duree_nb from sequence where seq_cdn=$id_seq","seq_duree_nb");
              $duree_seq+=$duree;
              $upd_seq = mysql_query ("UPDATE sequence set seq_duree_nb = $duree_seq, seq_modif_dt = \"$date_dujour\" where seq_cdn = $id_seq");
              $modifie_rss = rss :: modifie('sequence',$id_user,$id_seq);
            }
            if ($id_ress > 0 && $ress_norok == "NON")
            {
              $ress_norok == "OUI";
              $flag = 1;
            }
            if ($id_ress == 0 && $ress_norok == 'NON')
            {
               $flag = 1;
               $insert_act_query = mysql_query ("insert into activite values ($id_act,$id_seq,$num_act,\"".str_replace("\"","'",$titre)."\",\"".
                                                htmlentities($consigne,ENT_QUOTES,'iso-8859-1')."\",\"".
                                                htmlentities($commentaire,ENT_QUOTES,'iso-8859-1')."\",'$ress_norok',0,".
                                                "\"$duree\",\"$pass_mult\",\"$acquit\",\"$notation\",\"$dev_a_rendre\",".
                                                "$id_user,\"$date_dujour\",\"$date_dujour\",$droit_voir_act,$flag)");
               $insert_rss = rss :: ajout('activite',$id_user,$id_act);
      //dey Dfoad   insertion
              if (!empty($_FILES['userfile']['tmp_name']))
                 include("saveMedia.php");
              // Cas de nouvelle activité ou de modification d'activité sans pendant devoir
              $id_actdev = Donne_ID ($connect, "select max(actdev_cdn) from activite_devoir");
              $changer_dev = mysql_query ("insert into activite_devoir values($id_actdev,$id_act,\"$typdev\")");
                if ($id_seq > 0)
               {
                   $requete_grp = mysql_query ("select * from groupe");
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
                                             $ins_suivi = mysql_query ("insert into suivi1_$id_grp(suivi_cdn,suivi_utilisateur_no,suivi_act_no,suivi_etat_lb,suivi_grp_no) values ($id_suivi,$ut,$id_act,\"PRESENTIEL\",$grpe)");
                                         }
                                      $kk++;
                                  } //fin while ($kk != $nb_ut)
                              }
                         $gp++;
                       }
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
              $lien="sequence_entree.php?renvoi=1&liste=$liste&id_act=$id_act&id_seq=$id_seq&choix_ress=1&parcours=$parcours&id_parc=$id_parc&activite=1&id_ref_seq=$id_ref_seq&acquit=$acquit&pass_mult=$pass_mult&dev_a_rendre=$dev_a_rendre&notation=$notation&duree=$duree&droit_voir=$droit_voir&id_ress=$id_ress&miens=$miens";
              $lien=urlencode($lien);
              $renvoi =  "<DIV id='sequence'><A href=\"trace.php?link=$lien\" target='main'>$mess_form_retour</A></DIV>";
              if ($acquit != "RESSOURCE" && ($pass_mult == "NON" || $pass_mult == "OUI") && $notation== "OUI" && $dev_a_rendre == "NON" && ($id_ress > 0 || $ress_norok == 'OUI'))
                 $acquit = "FORMATEUR_REFERENT";
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
              {
                $flag = 1;
              }
              elseif($ress_norok == "OUI")
                $flag = 0;
              $id_ress = 0;
              $duree = ($horaire*60) + $minutage;
              $insert_act_query = mysql_query ("insert into activite values ($id_act,$id_seq,$num_act,\"".str_replace("\"","'",$titre)."\",\"".htmlentities($consigne,ENT_QUOTES,'iso-8859-1')."\",\"".htmlentities($commentaire,ENT_QUOTES,'iso-8859-1')."\",'$ress_norok',$id_ress,\"$duree\",\"$pass_mult\",\"$acquit\",\"$notation\",\"$dev_a_rendre\",$id_user,\"$date_dujour\",\"$date_dujour\",$droit_voir_act,$flag)");
              $insert_rss = rss :: ajout('activite',$id_user,$id_act);
      //dey Dfoad   insertion
              if (!empty($_POST['userfile']))
                 include("saveMedia.php");
              // Cas de nouvelle activité ou de modification d'activité sans pendant devoir
                 $id_actdev = Donne_ID ($connect, "select max(actdev_cdn) from activite_devoir");
                 $changer_dev = mysql_query ("insert into activite_devoir values($id_actdev,$id_act,\"$typdev\")");
              if ($id_seq > 0)
              {
                   $requete_grp = mysql_query ("select * from groupe");
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
                                             $ins_suivi = mysql_query ("insert into suivi1_$id_grp(suivi_cdn,suivi_utilisateur_no,suivi_act_no,suivi_etat_lb,suivi_grp_no) values ($id_suivi,$ut,$id_act,\"PRESENTIEL\",$grpe)");
                                         }
                                      $kk++;
                                  } //fin while ($kk != $nb_ut)
                              }
                         $gp++;
                       }
                   }
             }
         }// if else !=id_ress
       } //fin else ($titre==''....)
       if ($id_ress == 0 && $ress_norok == "OUI")
       {
           $letitre= $mess_ass_act;
           entete_concept("liste_seq.inc.php",$letitre);
           echo aide_simple("sequence");
           echo "<TR><TD><TABLE border=0 cellpadding='4' cellspacing = '4'>";
           // ressource sujet forum
           $chaine_act="&liste=$liste&id_seq=$id_seq&proprio=$proprio&refer=$refer&id_ref_seq=$id_ref_seq&miens=$miens&acces=_entree";
           $params = str_replace("&","|",$chaine_act);
           $forum_act  = $id_act;
           $_SESSION['forum_act'] = $forum_act;
           $_SESSION['chaine_act'] = $chaine_act;
           $lien="sequence_entree.php?id_seq=$id_seq&consult_act=1&encreation=1&parcours=$parcours&liste=$liste&id_parc=$id_parc&id_ref_seq=$id_ref_seq&miens=$miens";
           $lien = urlencode($lien);
           echo "<TR height='40'><TD align='left' nowrap>$bouton_gauche<A href=\"trace.php?link=$lien\" target='main'>$mess_plustard</A>$bouton_droite</TD></TR>";
           echo "<TR height='40'><TD valign='top'><Font size='2'><B>$mess_assress</B></TD></TR>";//$msq_ass_ress_form$mess_assres_comp
           $lien="recherche.php?id_act=$id_act&acces=_entree&parcours=$parcours&liste=$liste&id_parc=$id_parc&id_ref_seq=$id_ref_seq&modif_act=1&id_seq=$id_seq";
           $lien = urlencode($lien);
           echo "<TR><TD align='left' nowrap valign='top'>$bouton_gauche<A href=\"trace.php?link=$lien\" target='main'>$mess_ressidx</A>$bouton_droite</TD><TD valign='top'>$mess_goidx</TD></TR>";
           $lien="charge_ressource_vrac.php?id_act=$id_act&dou=_entree&params=$params&charger_fichier=1";
           $lien = urlencode($lien);
           echo "<TR height='40'><TD align='left' nowrap valign='top'>$bouton_gauche";
           echo "<A HREF=\"#\" title =\"$mess_ajt_ress_act\" onClick=\"window.open('trace.php?link=$lien','','scrollbars=yes,resizable=yes,width=500,height=300,left=250,top=300')\">".
                "$mess_ajt_ress_act</A>$bouton_droite</TD><TD>$mess_ajt_complement</TD></TR>";
           $lien="charge_ressource_vrac.php?id_act=$id_act&dou=_entree&params=$params&charger_url=1";
           $lien = urlencode($lien);
           echo "<TR height='40'><TD align='left' nowrap valign='top'>$bouton_gauche";
           echo "<A HREF=\"javascript:void(0);\" title =\"$mess_ajt_url_act\" onClick=\"window.open('trace.php?link=$lien','','scrollbars=yes,resizable=yes,width=500,height=300,left=250,top=300')\">$mess_ajt_url_act</A>$bouton_droite</TD><TD></TD></TR>";
           //quizz é créer
           $lien="creation_qcm.php?creation_qcm=1&params_qcm=$params&id_activit=$id_act&venu=act&acced=_entree";
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
           $lien="sequence_entree.php?liste=$liste&modif_act=1&id_act=$id_act&id_seq=$id_seq&choix_ress=1&parcours=$parcours&id_parc=$id_parc&action_act=1&id_ref_seq=$id_ref_seq&id_ress=$id_ress&miens=$miens";
           $lien=urlencode($lien);
           echo "<TR height='15'><TD colspan='2'>&nbsp;</td></tr>";
           echo "<TR><TD align='left' nowrap valign='top'>$bouton_gauche<A href=\"trace.php?link=$lien\" target='main'>$mess_ret</A>$bouton_droite</TD></TR>";//retour
           $html = "";
           echo fin_tableau($html);
         exit();
       }
       $lien="sequence_entree.php?id_seq=$id_seq&consult_act=1&encreation=1&parcours=$parcours&liste=$liste&id_parc=$id_parc&id_parc=$id_parc&parcours=$parcours&id_ref_seq=$id_ref_seq&miens=$miens&vient_de_search=$vient_de_search";
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
         $seq_init =  mysql_result ($activite_query,0,"act_seq_no");
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
         $titre = str_replace("\"","'",$titre);
         $consigne = str_replace("<BR>","",$consigne);
         $consigne = str_replace("\"","'",$consigne);
         $commentaire = str_replace("<BR>","",$commentaire);
         $commentaire = str_replace("\"","'",$commentaire);
         if ($id_ress > 0 && $ress_norok == "NON")
           $ress_norok == "OUI";
         if ($id_ress == 0 && $ress_norok == "NON")
         {
           $flag = 1;
         }
         elseif($id_ress == 0 && $ress_norok == "OUI")
           $flag = 0;
         $requete_grp = mysql_query ("select * from groupe order by grp_nom_lb");
         $nb_grp = mysql_num_rows($requete_grp);
         if ($nb_grp > 0)
         {
            $gp=0;
            while ($gp < $nb_grp)
            {
                 $id_grp = mysql_result($requete_grp,$gp,"grp_cdn");
                 $nomb = mysql_num_rows(mysql_query ("select * from suivi1_$id_grp where suivi_act_no = $id_act"));
                 $verif_activ += $nomb;
             $gp++;
            }
         }
         if ($seq_init == 0 && $auteur == $id_user && $verif_activ == 0)
         {
            $insert_act_exist = mysql_query ("UPDATE activite SET act_seq_no = '$id_seq', act_ordre_nb = '$new_ordre' WHERE act_cdn = '$id_act'");
            $modifie_rss = rss :: modifie('activite',$id_user,$id_act);
            $id_nouv = $id_act;
         }
         else
         {
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
            $insert_act_exist = mysql_query ("INSERT INTO activite values ($id_nouv,$id_seq,$new_ordre,\"".str_replace("\"","'",$titre)."\",\"$consigne\",\"$commentaire\",'$ress_norok',$id_ress,\"$duree\",\"$pass_mult\",\"$acquit\",\"$notation\",\"$dev_a_rendre\",'$id_user',\"$date_dujour\",\"$date_dujour\",'$droit_voir_act','$flag')");
            $insert_rss = rss :: ajout('activite',$id_user,$id_nouv);
         }
         if ($id_seq > 0)
         {
                   $requete_grp = mysql_query ("select * from groupe");
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
         }
         if ($id_ress == 0 && $ress_norok == "OUI" && $ins_ch_act != 1)
         {
           $letitre= $mess_ass_act;
           entete_concept("liste_seq.inc.php",$letitre);
           echo aide_simple("sequence");
           echo "<TR><TD><TABLE border=0 cellpadding='4' cellspacing = '4'>";
           // ressource sujet forum
           $chaine_act="&liste=$liste&id_seq=$id_seq&proprio=$proprio&refer=$refer&id_ref_seq=$id_ref_seq&miens=$miens&acces=_entree";
           $params = str_replace("&","|",$chaine_act);
           $forum_act  = $id_act;
           $_SESSION['forum_act'] = $forum_act;
           $_SESSION['chaine_act'] = $chaine_act;
           $lien="sequence_entree.php?id_seq=$id_seq&consult_act=1&parcours=$parcours&liste=$liste&id_parc=$id_parc&id_ref_seq=$id_ref_seq&miens=$miens";
           $lien = urlencode($lien);
           echo "<TR height='40'><TD align='left' nowrap>$bouton_gauche<A href=\"trace.php?link=$lien\" target='main'>$mess_plustard</A>$bouton_droite</TD></TR>";
           echo "<TR height='40'><TD valign='top'><Font size='2'><B>$mess_assress</B></TD></TR>";//$msq_ass_ress_form$mess_assres_comp
           $lien="recherche.php?acces=_entree&id_act=$id_act&parcours=$parcours&liste=$liste&id_parc=$id_parc&id_ref_seq=$id_ref_seq&modif_act=1&id_seq=$id_seq";
           $lien = urlencode($lien);
           echo "<TR><TD align='left' nowrap valign='top'>$bouton_gauche<A href=\"trace.php?link=$lien\" target='main'>$mess_ressidx</A>$bouton_droite</TD><TD valign='top'>$mess_goidx</TD></TR>";
           $lien="charge_ressource_vrac.php?id_act=$id_act&dou=_entree&params=$params&charger_fichier=1";
           $lien = urlencode($lien);
           echo "<TR><TD align='left' nowrap colspan='2'>$bouton_gauche";
           echo "<A HREF=\"#\" title =\"$mess_ajt_ress_act\" onClick=\"window.open('trace.php?link=$lien','','scrollbars=no,resizable=yes,width=400,height=140,left=450,top=400')\"".
                 bulle($mess_ajt_complement,"","RIGHT","ABOVE",312)."$mess_ajt_ress_act</A>$bouton_droite</TD></TR>";
           $lien="charge_ressource_vrac.php?id_act=$id_act&dou=_entree&params=$params&charger_url=1";
           $lien = urlencode($lien);
           echo "<TR height='40'><TD align='left' nowrap valign='top'>$bouton_gauche";
           echo "<A HREF=\"#\" title =\"$mess_ajt_ress_act\" onClick=\"window.open('trace.php?link=$lien','','scrollbars=yes,resizable=yes,width=500,height=300,left=250,top=300')\">".
                "$mess_ajt_ress_act</A>$bouton_droite</TD><TD>$mess_ajt_complement</TD></TR>";
           //quizz é créer
           $lien="creation_qcm.php?creation_qcm=1&params_qcm=$params&id_activit=$id_act&venu=act&acced=_entree";
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
           $lien="sequence_entree.php?liste=$liste&modif_act=1&id_act=$id_act&id_seq=$id_seq&choix_ress=1&action_act=1&id_ref_seq=$id_ref_seq&id_ress=$id_ress&miens=$miens";
           $lien=urlencode($lien);
           echo "<TR height='15'><TD colspan='2'>&nbsp;</td></tr>";
           echo "<TR><TD align='left' nowrap valign='top'>$bouton_gauche<A href=\"trace.php?link=$lien\" target='main'>$mess_ret</A>$bouton_droite</TD></TR>";//retour
           $html = "";
           echo fin_tableau($html);
         exit();
         }
         $lien="sequence_entree.php?id_seq=$id_seq&consult_act=1&parcours=$parcours&liste=$liste&id_parc=$id_parc&id_ref_seq=$id_ref_seq&miens=$miens&vient_de_search=$vient_de_search";
         $lien = urlencode($lien);
         echo "<script language=\"JavaScript\">";
         echo " document.location.replace(\"trace.php?link=$lien\")";
         echo "</script>";
       exit();
      }
      elseif ($ins_ch_act == 1 && $id_act == -1)
      {
         $message = $devoir_choix;
         $lien = "sequence_entree.php?liste=$liste&id_seq=$id_seq&parcours=$parcours&id_parc=$id_parc&choix_act=1&id_ref_seq=$id_ref_seq&message=$message&miens=$miens&vient_de_search=$vient_de_search";
         $lien = urlencode($lien);
         echo "<script language=\"JavaScript\">";
         echo " document.location.replace(\"trace.php?link=$lien\")";
         echo "</script>";
       exit();
      }
      ?>
      <SCRIPT language=JavaScript>
      function checkForm(frm) {
      var ErrMsg = "<?php echo $mess_info_no;?>\n\n";
      var lenInit = ErrMsg.length;
      if (isEmpty(frm.titre)==true)
        ErrMsg += ' - <?php echo $msq_tit_form;?>\n';
      if (isEmpty(frm.consigne)==true)
        ErrMsg += ' - <?php echo $msq_consigne_act_form;?>\n';
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
      if ($id_seq > 0)
         $titre_seq = GetDataField ($connect,"select seq_titre_lb from sequence where seq_cdn = $id_seq","seq_titre_lb");
      $letitre= $msq_ajout_act_seq;
      entete_concept("liste_seq.inc.php",$letitre);
      echo aide_simple("sequence");
      if ($id_seq > 0)
          echo "<TR><TD colspan=2 class='sous_titre'>$msq_seq : $titre_seq</TD></TR>";
      echo "<FORM Name='form1' enctype='multipart/form-data' action=\"sequence_entree.php?activite=1&insert_act=1&parcours=$parcours&liste=$liste&id_parc=$id_parc&id_seq=$id_seq&id_ress=$id_ress&id_ref_seq=$id_ref_seq&miens=$miens&\" method='POST'>";
      echo "<TR><TD><TABLE width='100%' cellspacing='1' cellpadding='3'>";
      if ($choix_ress == 1)
      {
         echo "<TR><TD nowrap><small>$msq_ress_act</small></TD><TD nowrap>";
         $titre_ress = GetDataField ($connect,"select ress_titre from ressource_new where ress_cdn = $id_ress","ress_titre");
         echo "$titre_ress</TD></TR>";//}
         echo "<TR><TD nowrap><small>$mrc_cat</small></TD><TD nowrap>";
         $cat_ress = GetDataField ($connect,"select ress_cat_lb from ressource_new where ress_cdn = $id_ress","ress_cat_lb");
         echo "$cat_ress</TD></TR>";//}
      }
      echo "<TR><TD nowrap><B>$msq_tit_form</B></TD>";
      echo "<TD nowrap><INPUT TYPE=\"TEXT\" class='INPUT' name=\"titre\" align=\"middle\" size='75' value=\"$titre\"><br></TD></TR>";
      echo "<TR><TD nowrap><B>$msq_consigne_act_form</B></TD>";
      echo "<TD nowrap><TEXTAREA rows='6' cols='80' class='TEXTAREA' align=\"middle\" name=\"consigne\">$consigne</TEXTAREA><br></TD></TR>";
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
          $lien="charge_ressource_vrac.php?id_act=$id&dou=act_free&media=1&charger_fichier=1";
          $lien = urlencode($lien);
          echo "<TD align='left' nowrap valign='top'><div style='clear:both;float:left;'>$bouton_gauche";
          echo "<A HREF=\"javascript:void(0)\" onClick=\"window.open('trace.php?link=$lien','','scrollbars=yes,resizable=yes,width=500,height=300,left=250,top=300')\" ".
                bulle("Vous retrouverez ce fichier (mp3, swf ou flv uniquement) dans votre répertoire dans le dossier Ressources Media","","RIGHT","ABOVE",220).
                "Télécharger le fichier (mp3, swf ou flv)</A>$bouton_droite</div>";
          if($media_act != "")
          {
                  $actit = $id;
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
      echo "<TD nowrap><TEXTAREA rows='6' cols='80' class='TEXTAREA' align=\"middle\" name=\"commentaire\">$commentaire</TEXTAREA><br></TD></TR>";
      echo "<TR><TD nowrap><B>$msq_ass_ress_form</B></TD><TD>";
      if ($ress_norok == 'OUI')
      {
         echo "$mess_oui&nbsp;&nbsp;<INPUT type='radio' name='ress_norok' value='OUI' checked>&nbsp;&nbsp;&nbsp;&nbsp;";
         echo "$mess_non&nbsp;&nbsp;<INPUT type='radio' name='ress_norok' value='NON'>";
      }
      elseif ($ress_norok == 'NON')
      {
         echo "$mess_oui&nbsp;&nbsp;<INPUT type='radio' name='ress_norok' value='OUI'>&nbsp;&nbsp;&nbsp;&nbsp;";
         echo "$mess_non&nbsp;&nbsp;<INPUT type='radio' name='ress_norok' value='NON' checked>";
      }
      else
      {
         echo "$mess_oui&nbsp;&nbsp;<INPUT type='radio' name='ress_norok' value='OUI'>&nbsp;&nbsp;&nbsp;&nbsp;";
         echo "$mess_non&nbsp;&nbsp;<INPUT type='radio' name='ress_norok' value='NON'>";
      }
      echo "</TD></TR>";
      echo "<TR><TD nowrap><B>$msq_droit_voir</B></TD>";
      echo "<TD nowrap>";
      if ($droit_voir_act == "")
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
      echo "<TR><TD nowrap><B>$msq_duree_form</B></TD><TD align='left'><TABLE border='0' cellspacing='0'><TR>";
      if ($titre == '' || $ins_ch_act == 1 || $renvoi == 1)
      {
         $reste = $duree%60;
         $heure = floor($duree/60);
         if ($duree == 0 || !isset($duree))
         {
           echo "<TD><INPUT TYPE='TEXT' name='horaire' size='2' maxlength = '3' align='center'>$h </TD>";
           echo "<TD><INPUT TYPE='TEXT' name='minutage' size='2' maxlength = '2' align='center'>$mn</TD>";
         }
         else
         {
           echo "<TD><INPUT TYPE='TEXT' name='horaire' value='$heure' size='2' maxlength = '3' align='center'>$h </TD>";
           echo "<TD><INPUT TYPE='TEXT' name='minutage' value='$reste' size='2' maxlength = '2' align='center'>$mn</TD>";
         }
      }
      echo "</TR></TABLE></TD></TR>";
      echo "<TR><TD nowrap><B>$msq_pass_act_form</B></TD><TD nowrap>";
      //Si on choisit une activite existante, on affiche les renseignements la concernant
      if ($ins_ch_act == 1)
      {
         echo "<INPUT type=hidden  name='pass_mult' value='$pass_mult'>";
         echo "$pass_mult";
         echo"<br>";
      }
      else
      {
         if ($renvoi == 1 && $pass_mult == 'OUI')
         {
            echo "$mess_oui&nbsp;&nbsp;<INPUT type='radio' name='pass_mult' value='OUI' checked>&nbsp;&nbsp;&nbsp;&nbsp;";
            echo "$mess_non&nbsp;&nbsp;<INPUT type='radio' name='pass_mult' value='NON'>";
         }
         elseif ($renvoi == 1 && $pass_mult == 'NON')
         {
            echo "$mess_oui&nbsp;&nbsp;<INPUT type='radio' name='pass_mult' value='OUI'>&nbsp;&nbsp;&nbsp;&nbsp;";
            echo "$mess_non&nbsp;&nbsp;<INPUT type='radio' name='pass_mult' value='NON' checked>";
         }
         else
         {
            echo "$mess_oui&nbsp;&nbsp;<INPUT type='radio' name='pass_mult' value='OUI'>&nbsp;&nbsp;&nbsp;&nbsp;";
            echo "$mess_non&nbsp;&nbsp;<INPUT type='radio' name='pass_mult' value='NON'>";
         }
         echo "<br>";
      }
      echo "</TD></TR>";
      echo "<TR><TD nowrap><B>$msq_acquit_form</B></TD><TD nowrap>";
      if ($ins_ch_act == 1)
      {
         echo "<INPUT type=hidden  name='acquit' value='$acquit'>";
         echo "$acquit<br>";
      }
      else
      {
         if ($renvoi == 1 &&  $acquit== 'APPRENANT')
         {
            echo ucfirst(strtolower($msq_apprenant))."&nbsp;&nbsp;<INPUT type='radio' name='acquit' value='APPRENANT' checked>&nbsp;&nbsp;&nbsp;&nbsp;";
            echo ucfirst(strtolower($msq_formateur))."&nbsp;&nbsp;<INPUT type='radio' name='acquit' value='FORMATEUR_REFERENT'>&nbsp;&nbsp;&nbsp;&nbsp;";
            echo ucfirst(strtolower($msq_ress))."&nbsp;&nbsp;<INPUT type='radio' name='acquit' value='RESSOURCE' onclick=\"window.alert('$mess_avrt_act_ress');\">";
         }
         elseif ($renvoi == 1 &&  $acquit== 'FORMATEUR_REFERENT')
         {
            echo ucfirst(strtolower($msq_apprenant))."&nbsp;&nbsp;<INPUT type='radio' name='acquit' value='APPRENANT'>&nbsp;&nbsp;&nbsp;&nbsp;";
            echo ucfirst(strtolower($msq_formateur))."&nbsp;&nbsp;<INPUT type='radio' name='acquit' value='FORMATEUR_REFERENT' checked>&nbsp;&nbsp;&nbsp;&nbsp;";
            echo ucfirst(strtolower($msq_ress))."&nbsp;&nbsp;<INPUT type='radio' name='acquit' value='RESSOURCE' onclick=\"window.alert('$mess_avrt_act_ress');\">";
         }
         elseif ($renvoi == 1 &&  $acquit== 'RESSOURCE')
         {
            echo ucfirst(strtolower($msq_apprenant))."&nbsp;&nbsp;<INPUT type='radio' name='acquit' value='APPRENANT'>&nbsp;&nbsp;&nbsp;&nbsp;";
            echo ucfirst(strtolower($msq_formateur))."&nbsp;&nbsp;<INPUT type='radio' name='acquit' value='FORMATEUR_REFERENT'>&nbsp;&nbsp;&nbsp;&nbsp;";
            echo ucfirst(strtolower($msq_ress))."&nbsp;&nbsp;<INPUT type='radio' name='acquit' value='RESSOURCE' checked>";
         }
         else
         {
            echo ucfirst(strtolower($msq_apprenant))."&nbsp;&nbsp;<INPUT type='radio' name='acquit' value='APPRENANT'>&nbsp;&nbsp;&nbsp;&nbsp;";
            echo ucfirst(strtolower($msq_formateur))."&nbsp;&nbsp;<INPUT type='radio' name='acquit' value='FORMATEUR_REFERENT'>&nbsp;&nbsp;&nbsp;&nbsp;";
            echo ucfirst(strtolower($msq_ress))."&nbsp;&nbsp;<INPUT type='radio' name='acquit' value='RESSOURCE' onclick=\"window.alert('$mess_avrt_act_ress');\">";
         }
      }
      echo "<br></TD></TR>";
      echo "<TR><TD nowrap><B>$msq_dev_act_form</B></TD><TD nowrap>";
      if ($ins_ch_act == 1)
      {
         echo "<INPUT type=hidden  name='dev_a_rendre' value='$dev_a_rendre'>";
         echo "$dev_a_rendre";
      }
      else
      {
         if ($renvoi == 1 && $dev_a_rendre == 'OUI')
         {
            echo "$mess_oui&nbsp;&nbsp;<INPUT type='radio' name='dev_a_rendre' value='OUI' checked>&nbsp;&nbsp;&nbsp;&nbsp;";
            echo "$mess_non&nbsp;&nbsp;<INPUT type='radio' name='dev_a_rendre' value='NON'>";
         }
         elseif ($renvoi == 1 && $dev_a_rendre == 'NON')
         {
            echo "$mess_oui&nbsp;&nbsp;<INPUT type='radio' name='dev_a_rendre' value='OUI'>&nbsp;&nbsp;&nbsp;&nbsp;";
            echo "$mess_non&nbsp;&nbsp;<INPUT type='radio' name='dev_a_rendre' value='NON' checked>";
         }
         else
         {
            echo "$mess_oui&nbsp;&nbsp;<INPUT type='radio' name='dev_a_rendre' value='OUI'>&nbsp;&nbsp;&nbsp;&nbsp;";
            echo "$mess_non&nbsp;&nbsp;<INPUT type='radio' name='dev_a_rendre' value='NON'>";
         }
      }
      echo "<br></TD></TR>";
      echo "<TR><TD nowrap><B>$msq_not_act_form</B></TD><TD nowrap>";
      if ($ins_ch_act == 1)
      {
         echo "<INPUT type=hidden  name='notation' value='$notation'>";
         echo "$notation";
      }
      else
      {
         if ($renvoi == 1 && $notation == 'OUI')
         {
            echo "$mess_oui&nbsp;&nbsp;<INPUT type='radio' name='notation' value='OUI' checked>&nbsp;&nbsp;&nbsp;&nbsp;";
            echo "$mess_non&nbsp;&nbsp;<INPUT type='radio' name='notation' value='NON'>";
         }
         elseif ($renvoi == 1 && $notation == 'NON')
         {
            echo "$mess_oui&nbsp;&nbsp;<INPUT type='radio' name='notation' value='OUI'>&nbsp;&nbsp;&nbsp;&nbsp;";
            echo "$mess_non&nbsp;&nbsp;<INPUT type='radio' name='notation' value='NON' checked>";
         }
         else
         {
            echo "$mess_oui&nbsp;&nbsp;<INPUT type='radio' name='notation' value='OUI'>&nbsp;&nbsp;&nbsp;&nbsp;";
            echo "$mess_non&nbsp;&nbsp;<INPUT type='radio' name='notation' value='NON'>";
         }
      }
      echo "</TD></TR>";
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
          /*if ($dev_act != 'xApi TinCan')
               echo "<option value='xApi TinCan'>xApi TinCan</option>";*/
          echo "</select></TD></TR>";
      echo boutret(1,0);
      echo "</TD><TD align='left'><A HREF=\"javascript:checkForm(document.form1);\" onClick=\"TinyMCE.prototype.triggerSave();\" onmouseover=\"img1.src='images/fiche_identite/boutvalidb$suffixer.gif';return true;\" onmouseout=\"img1.src='images/fiche_identite/boutvalid$suffixer.gif'\">".
       "<IMG NAME=\"img1\" SRC=\"images/fiche_identite/boutvalid$suffixer.gif\" BORDER='0' onLoad=\"tempImg=new Image(0,0); tempImg.src='images/fiche_identite/boutvalidb$suffixer.gif'\"></A>";
      echo "</TD><TR>";
      echo "</FORM></TABLE>";
      fin_tableau($html);
    }//fin if ($titre = '')
}
//Choix d'une activité existante é inserer dans la sequence
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
    $suffix = "_entree";
    $afficher_choix = ChoixActModSeq($id_seq);
    if (!isset($action_seq) || $action_seq != 1)
    {

       $titre_seq = GetDataField ($connect,"select seq_titre_lb from sequence where seq_cdn = $id_seq","seq_titre_lb");
       $letitre= $msq_choix_act;
       entete_concept("liste_seq.inc.php",$letitre);
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
    $nombre_seq = 0;
    if ($supp == 1 && $parcours != 1)
    {
       $requete_grp = mysql_query ("select * from groupe");
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
         echo "<CENTER><TABLE bgColor='#298CA0' cellspacing='2' cellpadding='0'><TR>";
         include ("liste_seq.inc.php");
         echo "<TD valign='top' width='70%' height='100%' bgcolor='#FFFFFF'>";
         echo "<TABLE bgColor='#FFFFFF' cellspacing='1' cellpadding='0' width='100%'>";
         echo "<TR><TD><Font size='2'>$msq_nosup_seq</TD></TR>";
         echo boutret(1,0);
         fin_tableau($html);
        exit;
      }
      $seq_parc = mysql_query ("select * from sequence_parcours where seqparc_seq_no = $id_seq");
      $nbre_seq = mysql_num_rows($seq_parc);
      if ($nbre_seq == 1)
         $seq_supp = mysql_query ("delete from sequence_parcours where seqparc_seq_no = $id_seq");
      $supp_seq_ref = mysql_query ("delete from sequence_referentiel where seqref_seq_no = $id_seq");
      $req_supp_act = mysql_query ("select * from activite where act_seq_no = $id_seq");
      $nbre_act = mysql_num_rows($req_supp_act);
      if ($nbre_act> 0){
         while ($item = mysql_fetch_object($req_supp_act)) {
             $id_act_supp = $item -> act_cdn;
             $supp_star = mysql_query("delete from stars where star_item_id = '$id_act_supp' and star_type_no='3'");
             $seq_supp = mysql_query ("delete from activite where act_cdn = $id_act_supp");
             $supp_rss = rss :: supprime('activite',$id_act_supp);
             //dey Dfoad
                $act_media = mysql_query("delete from activite_media where actmedia_act_no = $id_act_supp");
         }
      }
      $supp_mod_seq = mysql_query ("delete from scorm_module where mod_seq_no = $id_seq");
      $supp_prereq = mysql_query ("delete from prerequis where prereq_seq_no = $id_seq");
      $del_seqprereq = mysql_query ("delete from prerequis where prereq_seqcondition_no = $id_seq");
      $supp_star = mysql_query("delete from stars where star_item_id = '$id_seq' and star_type_no='2'");
      $supp_seq = mysql_query ("delete from sequence where seq_cdn=$id_seq");
      $supp_rss = rss :: supprime('sequence',$id_seq);
      $message = $mess_laseq." ".addslashes($son_nom)." - ".$seq_supp_ok;
        //supprimer aussi pre-requis dans lesquels la sequence fait partie du pre-requis

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
        $upd_seq = mysql_query ("update sequence set seq_titre_lb = \"".str_replace("\"","'",$nom)."\",seq_desc_cmt = \"".
                                htmlentities($description,ENT_QUOTES,'iso-8859-1')."\",seq_mots_clef = \"".
                                htmlentities($mots_clef,ENT_QUOTES,'iso-8859-1')."\",".
                                "seq_ordreact_on = \"$ordre_des_act\",seq_publique_on = '$droit_voir_seq', ".
                                "seq_modif_dt = \"$date_dujour\", seq_type_on = $type_seq where seq_cdn = $id_seq");
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
      }
      $message = $mess_suit_dch;
    }
    if ($modif_type == 1)
    {
       $upd_seq = mysql_query ("update sequence set seq_type_on = 1, seq_modif_dt = \"$date_dujour\" where seq_cdn = $id_seq");
       $modifie_rss = rss :: modifie('sequence',$id_user,$id_seq);
       $message =  $mess_laseq." ".addslashes($son_nom).' '.$mess_admin_fic_modif_fin;
    }
    if ($modif_type_normal == 1)
    {
       $upd_seq = mysql_query ("update sequence set seq_type_on = 0, seq_modif_dt = \"$date_dujour\" where seq_cdn = $id_seq");
       $modifie_rss = rss :: modifie('sequence',$id_user,$id_seq);
       $message = addslashes($son_nom).' '.$mess_admin_fic_modif_fin;
    }
    if ($modif_seqref == 1)
    {
       $upd_seq = mysql_query ("update sequence_referentiel set seqref_referentiel_no='$id_ref_seq' where seqref_seq_no = $id_seq");
       $message = $mess_suit_dch;
    }
    //Ajout d'une sequence existante dans un autre referentiel
    if ($ajout_seq == 1)
    {
        $id_seqref = Donne_ID ($connect,"select max(seqref_cdn) from sequence_referentiel");
        $autre_ref = mysql_query ("insert into sequence_referentiel values ($id_seqref,$id_seq,$id_ref_seq)");
        $message = $mess_suit_dch;
    }

    //Si l'on vient d'un parcours, on n'effectue pas les memes requetes. On selectionne les seq par rapport au parcours sinon par rapport au referentiel
    if (!isset($liste) || $liste == '')
        $seq_query = mysql_query ("select * from sequence,sequence_referentiel where seqref_referentiel_no = $id_ref_seq AND seqref_seq_no = seq_cdn order by seq_cdn");
    elseif ($liste == 1)
       $seq_query = mysql_query ("select * from sequence where seq_cdn = $id_seq");
  $nb_seq = mysql_num_rows ($seq_query);
  if ($nb_seq == 0 && !$parcours && $id_ref_seq == 0) {
      $lien = "sequence_entree.php?prem=1&liste=1&refer=$refer&miens=$miens&ordre_affiche=lenom&miens=$miens&message=$message";
      $lien = urlencode($lien);
      echo "<script language=\"JavaScript\">";
      echo "document.location.replace(\"trace.php?link=$lien\")";
      echo "</script>";
      exit();
    }
    elseif($nb_seq == 0 && !$parcours && $id_ref_seq > 0 && $supp != 1)
    {
      $lien = "sequence_entree.php?choix_ref=1&id_ref_seq=$id_ref_seq&miens=$miens&message=$message";
      $lien = urlencode($lien);
      echo "<script language=\"JavaScript\">";
      echo "document.location.replace(\"trace.php?link=$lien\")";
      echo "</script>";
      exit();
    }
    elseif($nb_seq == 0 && !$parcours && $id_ref_seq > 0 && $supp == 1)
    {
      $lien = "sequence_entree.php?prem=1&liste=1&refer=1&ordre_affiche=lenom&miens=$miens&message=$message";
      $lien = urlencode($lien);
      echo "<script language=\"JavaScript\">";
      echo "document.location.replace(\"trace.php?link=$lien\")";
      echo "</script>";
      exit();
    }
    else
    {
       if (!isset($id_ref_seq))
       {
          $id_ref_seq = GetDataField ($connect,"select seqref_referentiel_no from sequence_referentiel where seqref_seq_no = $id_seq","seqref_referentiel_no");
          if ($id_ref_seq > 0)
          {
             $referentiel = GetDataField ($connect,"select ref_nomabrege_lb from referentiel where ref_cdn = $id_ref_seq","ref_nomabrege_lb");
             $nom_referentiel = GetDataField ($connect,"select ref_desc_cmt  from referentiel where ref_cdn = $id_ref_seq","ref_desc_cmt");
             if ($nom_referentiel == "")
                $nom_referentiel = GetDataField ($connect,"select ref_nomabrege_lb from referentiel where ref_cdn = $id_ref_seq","ref_nomabrege_lb");
             //$sous_titre= "<BR><FONT color='white' size=2><B><BR>$msq_ref</B>: $referentiel-->&nbsp;<small>$nom_referentiel</small></FONT></CENTER>";
          }
       }
       if (isset($message) && $message != '')
           echo notifier($message);
       $letitre= $mess_menu_gest_seq;
       entete_concept("liste_seq.inc.php",$letitre);
       echo aide_simple("sequence");
       if ($message_alerte != "")
          echo "<TR><TD align=left valign='bottom'><Font size='2'><IMG SRC='images/ecran-annonce/icoalertw.gif' border='0'>&nbsp;&nbsp;$message_alerte</FONT></TD></TR>";
       if ($sous_titre != '')
          echo "<TR><TD colspan='2' class='sous_titre'>$sous_titre</TD></TR>";
       $bgcolor2 = '#2B677A';
       $bgcolor1 = '#F8F2E4';
       echo "<TR><TD><TABLE width=100% cellspacing='1' cellpadding='5'><TR bgcolor=$bgcolor2>";
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
       echo "</tr>";
       $i = 0;
       $id_seq_parc = array();
       while ($i != $nb_seq)
       {
               $l = $i+1;
               $id = mysql_result ($seq_query,$i,"seq_cdn");
               $id_seq_parc[$i] = $id;
               if (($auteur_parc == $id_user || $typ_user == 'ADMINISTRATEUR') && $parcours == 1 && $nb_seq > 1)
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
               $droit_voir = mysql_result ($seq_query,$i,"seq_publique_on");
               $mod_seq = mysql_result ($seq_query,$i,"seq_type_lb");
               $seq_type = mysql_result ($seq_query,$i,"seq_type_on");
               $ordre_act = mysql_result ($seq_query,$i,"seq_ordreact_on");
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
               if (!strstr($mod_seq,"SCORM"))
               {
                   $nb_act_seq_query = mysql_query ("select count(act_cdn) from activite where act_seq_no = $id");
                   $nb_act_seq = mysql_result ($nb_act_seq_query,0);
                   $sql_duree = mysql_query ("SELECT act_duree_nb from activite where act_seq_no = $id");
                   $duree_srch = "act_duree_nb";
               }
               else
               {
                   $nb_act_seq_query = mysql_query ("select count(mod_cdn) from scorm_module where mod_seq_no = $id AND mod_launch_lb !=''");
                   $nb_act_seq = mysql_result ($nb_act_seq_query,0);
                   $sql_duree = mysql_query ("SELECT mod_duree_nb from scorm_module where mod_seq_no = $id AND mod_duree_nb != ''");
                   $duree_srch = "mod_duree_nb";
               }
               $nb_duree = mysql_num_rows ($sql_duree);
               $iduree = 0;$duree_new = 0;
               while ($iduree < $nb_duree)
               {
                 $duree_new += mysql_result($sql_duree,$iduree,$duree_srch);
                 $iduree++;
               }
               $corrige_duree = mysql_query ("UPDATE sequence SET seq_duree_nb = \"$duree_new\",seq_modif_dt = \"$date_dujour\" WHERE seq_cdn= $id");
               $requete_grp = mysql_query ("select * from groupe");
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
               if ($id_ref_seq > 0)
               {
                 $desc_ref = GetDataField ($connect,"select ref_desc_cmt from referentiel where ref_cdn = \"$id_ref_seq\"","ref_desc_cmt");
                 if ($desc_ref == "")
                 {
                   $desc_ref = GetDataField ($connect,"select ref_nomabrege_lb from referentiel where ref_cdn = $id_ref_seq","ref_nomabrege_lb");
                 }
                 $niv_ref = GetDataField ($connect,"select ref_denom_lb  from referentiel where ref_cdn = \"$id_ref_seq\"","ref_denom_lb");
               }
               else
               {
                 $desc_ref = "$msq_slcp $prenom_auteur  $nom_auteur";
                 $niv_ref = $msq_nd ;
               }
               $seq_prereq = mysql_query ("select * from prerequis where prereq_seq_no = $id");
               $nb_prereq = mysql_num_rows ($seq_prereq);
               echo couleur_tr($i+1,'');
               if (($auteur_parc == $id_user || $typ_user == 'ADMINISTRATEUR') && $parcours == 1 && $nb_seq > 1)
               {
                  if ($i < $nb_seq -1)
                  {
                     $lien="sequence_entree.php?liste=$liste&consult=1&parcours=$parcours&id_parc=$id_parc&modif=1&descendre=1&seq_actuelle=$seq_actuelle&seq_precedente=$seq_precedente&seq_suivante=$seq_suivante&id_ref_seq=$id_ref_seq&refer=$refer&miens=$miens";
                     $lien = urlencode($lien);
                     echo "<TD width='20' align='middle' valign='top'><A HREF=\"trace.php?link=$lien\" target='main' TITLE=\"$mess_increment\"><IMG SRC=\"images/modules/tut_form/flechebas.gif\" TITLE=\"$mess_increment\" border='0'></A></td>";
                  }
                  else
                    echo"<td align='middle'>&nbsp;</td>";
                  if ($i > 0)
                  {
                     $lien="sequence_entree.php?liste=$liste&consult=1&parcours=$parcours&id_parc=$id_parc&modif=1&monter=1&seq_actuelle=$seq_actuelle&seq_precedente=$seq_precedente&seq_suivante=$seq_suivante&id_ref_seq=$id_ref_seq&refer=$refer&miens=$miens";
                     $lien = urlencode($lien);
                     echo "<TD width='15' height='20' align='middle' valign='top'><A HREF=\"trace.php?link=$lien\" target='main' TITLE=\"$mess_decrement\"><IMG SRC=\"images/modules/tut_form/flechehaut.gif\" TITLE=\"$mess_decrement\" border='0'></TD>";
                  }
                  else
                    echo"<td align='middle'>&nbsp;</td>";
               }
                echo "<td align='left' valign='top'>";
                  // affichage du nom de la séquence
               $req_parc_seq = mysql_query ("SELECT parcours.parcours_nom_lb FROM parcours,sequence_parcours WHERE sequence_parcours.seqparc_seq_no=$id AND sequence_parcours.seqparc_parc_no=parcours.parcours_cdn group by parcours.parcours_nom_lb order by parcours.parcours_nom_lb");
               $nb_parc_seq = mysql_num_rows ($req_parc_seq);
               $la_serie = "";
               if ($nb_parc_seq > 0){
                  $psi=0;
                  while ($psi < $nb_parc_seq){
                     $p_nom = mysql_result($req_parc_seq,$psi,"parcours_nom_lb");
                     $la_serie .="<LI>".$p_nom."</LI>";
                     $psi++;
                  }
               }
               $lien = "sequence_entree.php?liste=$liste&id_seq=$id&consult_act=1&parcours=$parcours&liste=$liste&id_ref_seq=$id_ref_seq&miens=$miens";
               $lien = urlencode($lien);
               $le_mode = "";
               if (strstr($mod_seq,"SCORM"))
                  $le_mode = "<IMG SRC='images/gest_parc/scorm.gif' border='0' alt=\"$mess_seq_sco\">&nbsp;";
               if ($nb_act_seq == 0)
                 echo "<B>$le_mode $nom</B>";
               else
               {
                 echo "$div<A HREF=\"trace.php?link=$lien\" target='main'";
                 if ($la_serie != "")
                    echo bulle($la_serie,"<TABLE><TR><TD width=5></TD><TD align=left><FONT SIZE=2 color =#333333><B>$seq_parc_exist</B></FONT><BR></TD</TR></TABLE>","RIGHT","",330);
                 else
                    echo ">";
                 echo "$le_mode $nom</A><BR>&nbsp;&nbsp;&nbsp;&nbsp;<small>".stripslashes($desc_seq)."</small>";
               }
               echo"<BR>";
               if ($id_ref_seq > 0)
                 echo "<SMALL>$msq_ref_format&nbsp;<I>$niv_ref</I>&nbsp;-->&nbsp;".html_entity_decode($desc_ref,ENT_QUOTES,'ISO-8859-1')."</SMALL></DIV>";
               else
                 echo "<SMALL>".html_entity_decode($desc_ref,ENT_QUOTES,'ISO-8859-1')."</SMALL></DIV>";
             // affichage des autres éléments de la séquence
             // affichage des autres éléments de la séquence
               if (strstr($mod_seq,"SCORM"))
               {
                  $nb_mod_sco = mysql_result(mysql_query ("select count(*) from scorm_module where mod_seq_no = $id AND (mod_content_type_lb = 'SCORM' OR mod_content_type_lb = 'ASSET' OR mod_content_type_lb = 'AICC_HACP' OR  mod_content_type_lb = 'AICC_API')"),0);
                  echo "</td><td align='middle' valign='top'>$nb_mod_sco</td>";
               }
               else
                  echo "</td><td align='middle' valign='top'>$nb_act_seq</td>";
               if (strstr($mod_seq,"SCORM"))
               {
                  $seq_duree = strval($duree_new);
                  $duree = duree_calc($duree_new);
               }
               else
                  $duree = duree_calc($duree_new);
               echo "<td align='middle' valign='top'>$duree</td>";
               if ($seqduref == 1 && $dureeRefSeq > 0)
                   echo "<td align='middle' valign='top'>".duree_calc($dureeRefSeq)."</td>";
               elseif ($seqduref == 1 && $dureeRefSeq == '')
                   echo "<td align='middle' valign='top'></td>";
               if ($nb_prereq == 0)
                   echo "<td align='middle' valign='top'>$msq_noprereq</td>";
               else
               {
                   $lien="sequence_entree.php?liste=$liste&aff_prereq=1&parcours=$parcours&id_parc=$id_parc&id_seq=$id&id_ref_seq=$id_ref_seq&titre=".addslashes($nom)."&miens=$miens";
                   $lien = urlencode($lien);
                   echo "<td align='middle' valign='top'><a href=\"trace.php?link=$lien\" target='main'>$mess_oui</a></td>";
               }
               $lien = "favoris.php?ajouter=1&seq=$id&accede=_entree&dhou=1";
               $lien = urlencode($lien);
               if ($id_auteur == $id_user || $typ_user == 'ADMINISTRATEUR')
                   echo  "<TD align='middle' valign='top'><a href=\"#\" ".
                         "onclick=\"window.open('trace.php?link=$lien','','width=680,height=500,resizable=yes,status=no')\" ".
                         bulle(NewHtmlentities($msq_prec_fav),"","RIGHT","ABOVE",200).
                         "<IMG SRC=\"images/modules/tut_form/icoresapprenant.gif\" border='0'></A></TD>";
               else
                   echo "<TD align='middle' valign='top'></TD>";
                 $lien = "favoris.php?consulter=1&seq=$id&dhou=1";
                 $lien = urlencode($lien);
                // Chercher les ressources bibliographiques préconisées par le formateur
                 $sql = mysql_query ("SELECT * from favoris where fav_seq_no = '$id'");
                 $nbr_fav = mysql_num_rows($sql);
                 if ($nbr_fav > 0)
                 {
                   echo  "<TD valign='top' align='middle'><a href=\"#\" ".
                         "onclick=\"window.open('trace.php?link=$lien','','width=680,height=390,resizable=yes,status=no')\" ".
                         bulle(NewHtmlentities($msq_voir_fav),"","RIGHT","ABOVE",200).
                         "<IMG SRC=\"images/modules/tut_form/icoacresbiblio.gif\" border='0'></A></TD>";
                 }
                 else
                   echo "<TD>&nbsp;</TD>";
                //Droit de suppression et de modification
                 if (!$parcours && ($id_auteur == $id_user || $typ_user == 'ADMINISTRATEUR'))
                 {
                   $lien="sequence_entree.php?liste=$liste&action_seq=1&id_seq=$id&nb_act_seq=$nb_act_seq&duree=$duree_new&ordre_act=$ordre_act&id_ref_seq=$id_ref_seq&droit_voir=$droit_voir&miens=$miens";
                   $lien = urlencode($lien);
                   echo "<td  align='middle' valign='top'><a href=\"trace.php?link=$lien\" target='main'".bulle($mess_modif_seq,"","LEFT","",100).
                        "<IMG SRC=\"images/repertoire/icoGrenomfich20.gif\" border='0'></A></td>";
                   $id_seq3_query = mysql_query ("select count(seqparc_cdn) from sequence_parcours where seqparc_seq_no = $id");
                   $nbr_seq3 = mysql_result($id_seq3_query,0);
                   $requete_grp = mysql_query ("select * from groupe");
                   $nb_grp_parc = mysql_num_rows($requete_grp);
                   if ($nb_grp_parc > 0)
                   {
                       $gp=0;$nb_presc_seq_parc = 0;
                       while ($gp < $nb_grp_parc)
                       {
                              $id_grp = mysql_result($requete_grp,$gp,"grp_cdn");
                              $nom_grp = mysql_result($requete_grp,$gp,"grp_nom_lb");
                              $nb_presc = mysql_num_rows(mysql_query("select * from prescription_$id_grp where presc_seq_no=$id"));
                              $nb_presc_seq_parc += $nb_presc;
                         $gp++;
                       }
                   }
                   if ($nb_presc_seq_parc == 0 && $nbr_seq3 == 0)
                   {
                     $lien="sequence_entree.php?liste=$liste&consult=1&supp=1&id_seq=$id&id_ref_seq=$id_ref_seq&refer=$refer&miens=$miens";
                     $lien = urlencode($lien);
                     echo "<td align='middle' valign='top'><a href=\"javascript:void(0);\" onclick=\"javascript:return(confm('trace.php?link=$lien'));\" target='main' ".bulle($msq_etiq_sup,"","LEFT","",180).
                          "<IMG SRC=\"images/messagerie/icopoubelressour.gif\" border='0'></A></td>";
                   }
                   else
                   {
                      if ($nb_presc_seq_parc > 0)
                        $msg_interdit = $msq_nosup_seq;
                      elseif ($nb_presc_seq_parc == 0 && $nbr_seq3 > 0)
                        $msg_interdit = $mess_seq_nosup_parc;
                      $mess_interdiction = "<td align='center' valign='top'><A HREF=\"javascript:void(0);\" style='cursor:help;' ".
                                           bulle($msg_interdit,"","LEFT","",180).
                                           "<IMG SRC=\"images/repertoire/icoptiinterdit.gif\" border='0'></td>";
                      echo $mess_interdiction;
                   }
                   if ($droit_voir == 1)
                   {
                      $lien="sequence_entree.php?liste=$liste&dupli_seq=1&id_seq=$id&id_ref_seq=$id_ref_seq&miens=$miens";
                      $lien = urlencode($lien);
                      echo "<TD nowrap align='center' valign='top'><A href=\"trace.php?link=$lien\" target='main'".bulle($msq_dupli_seq,"","LEFT","",110).
                           "<IMG SRC=\"images/repertoire/icoptiedit.gif\" BORDER=0></A></TD>";
                   }
                   else
                        echo "<TD>&nbsp;</TD>";
                   echo "</TR></TABLE>";
                 }
                 else
                    echo "<TD>&nbsp;</TD><TD>&nbsp;</TD><TD>&nbsp;</TD>";
              $i++;
              } //fin while ($i != $nb_seq)
              if (!$parcours && $id_auteur != $id_user  && $typ_user != 'ADMINISTRATEUR')
                  echo "</TD></TR></TABLE>";
              if (!$parcours && ($id_auteur == $id_user || $typ_user == 'ADMINISTRATEUR'))
                   echo "</TD></TR><TR><TD>";
              echo "<TABLE border=0 cellpadding='4' cellspacing = '4'><TR>";
              if (!$parcours && ($id_auteur == $id_user || $typ_user == 'ADMINISTRATEUR'))
              {
                   // debut modif referentiel
                 if ($id_ref_seq == 0)
                 {
                     $lien="referenciel.php?acces=_entree&passe_acces=1&consult=1&modif_seqref=1&id_seq=$id";
                     $lien = urlencode($lien);
                     echo "<TD nowrap>$bouton_gauche<a href=\"trace.php?link=$lien\" target='main'>$mess_sq_ref_mod</a>$bouton_droite</TD>";
                 }
                 else
                 {
                     $lien="sequence_entree.php?liste=1&consult=1&modif_seqref=1&id_ref=0&id_seq=$id";
                     $lien = urlencode($lien);
                     echo "<TD nowrap>$bouton_gauche<A href=\"trace.php?link=$lien\" target='main'>$seq_hrs_ref</A>$bouton_droite</TD>";
                     $lien="referenciel.php?acces=_entree&passe_acces=1&consult=1&modif_seqref=1&id_seq=$id";
                     $lien = urlencode($lien);
                     if ($id_ref_seq > 0)
                     echo "<TD nowrap>$bouton_gauche<a href=\"trace.php?link=$lien\" target='main'>$msq_go_aut_ref</a>$bouton_droite";
                 }
                 if (!strstr($mod_seq,"SCORM"))
                 {
                      $lien="export_seq_xml.php?id_seq=$id";
                      echo "<TD nowrap>$bouton_gauche<a href=\"$lien\">Export Scorm de la séquence</a>$bouton_droite</TD>";
                 }
              }
              if (!$parcours && $nb_act_seq > 0 && $message_alerte == "" && strstr($mod_seq,"SCORM"))
              {
                 $launch = mysql_result(requete_order("*","scorm_module"," mod_seq_no = '$id' AND mod_launch_lb != ''","mod_cdn ASC"),0,"mod_launch_lb");
                if(!strstr($launch,"http://"))
                {
                  $launch = "../".$launch;
                  $supplement = "";
                }
                else
                  $supplement = "X";
                 if (strstr($mod_seq,"SCORM"))
                 {
                    $lien = "scorm/lancer_sco$supplement.php?id_seq=$id&dou=sequence";
                    $largeur=1015;
                    $hauteur=768;
                 }
                 else
                 {
                    $lien = "vue_sequence.php?seq=$id";
                    $largeur=800;
                    $hauteur=400;
                 }
                 $lien = urlencode($lien);
                 if ($id_ref_seq > 0)
                    echo "</TD></TR><TR><TD nowrap colspan='2'>";
                 else
                    echo "<TD nowrap>";
                 echo "$bouton_gauche<A HREF=\"#\" onclick=\"javascript:window.open('trace.php?link=$lien','','top=0,left=0,width=$largeur,height=$hauteur,resizable=yes,status=no,scrollbars=yes')\">$mess_vue_app</A>$bouton_droite";
              }
                 echo "</TD></TR>";
          }//fin else ($nb_seq == 0)
          if ($vient_de_search != 1)
            echo boutret(1,0);
          else{
            $lien = "sequence_entree.php?prem=1&liste=1&refer=$refer&ordre_affiche=lenom&miens=$miens&proprio=$proprio";
            $lien = urlencode($lien);
            echo "<TR height='50'><TD valign='center'><A HREF=\"trace.php?link=$lien\" onmouseover=\"img_annonce.src='images/fiche_identite/boutretourb$suffixer.gif';return true;\" onmouseout=\"img_annonce.src='images/fiche_identite/boutretour$suffixer.gif'\">";
            echo "<IMG NAME=\"img_annonce\" SRC=\"images/fiche_identite/boutretour$suffixer.gif\" BORDER='0' onLoad=\"tempImg=new Image(0,0); tempImg.src='images/fiche_identite/boutretourb$suffixer.gif'\"></A>";
          }
          echo "</TD></TR></TABLE>";
          fin_tableau($html);

}//fin if($consult == 1)

//Lors de la consultation, on peut modifier,supprimer, ajouter des activités
if ($action_seq == 1)
{

/*
*/
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
    entete_concept("liste_seq.inc.php",$letitre);
    echo aide_simple("sequence");
    $requete = requete("*","sequence","seq_cdn = '$id_seq'");
    $data_seq = mysql_fetch_object($requete);
    $nom = $data_seq->seq_titre_lb;
    $type_seq = $data_seq->seq_type_on;
    $mod_seq = $data_seq->seq_type_lb;
    $seq_duree = $data_seq->seq_duree_nb;
    $description = html_entity_decode($data_seq->seq_desc_cmt,ENT_QUOTES,'iso-8859-1');
    $mots_clef = html_entity_decode($data_seq->seq_mots_clef,ENT_QUOTES,'iso-8859-1');
    $droit_voir_seq = $data_seq->seq_publique_on;
    $ordre_des_act = $data_seq->seq_ordreact_on;
  if ($choix != 1 && $choix_act != 1)
  {
    echo "<FORM name='form1' action=\"sequence_entree.php?liste=$liste&consult=1&modif=1&id_seq=$id_seq&id_ref_seq=$id_ref_seq&miens=$miens\" target='main' method='post'>";
    if ($type_seq == 1 && $droit_voir_seq == 1)
    {
       if ($sous_titre != '')
          $sous_titre .= "<BR><B>".$mess_seq_type." ".strtolower($mess_exp_type);
       else
          $sous_titre = "<B>".$mess_seq_type." ".strtolower($mess_exp_type);
    }
    if ($sous_titre != '')
       echo "<TR><TD colspan='2' class='sous_titre'>$sous_titre</TD></TR>";
    echo "<TR><TD colspan='2'><TABLE border=0>";
    echo "<TR><TD nowrap valign='top'><B>$msq_titre</B></TD><TD nowrap>";
    echo "<INPUT TYPE='TEXT' class='INPUT' name='nom' size='75' value=\"$nom\" align='middle'>";
    $nb_preq = mysql_result(mysql_query("select count(*) from prerequis where prereq_seq_no=$id_seq"),0);
    $l_aide = anoter($mess_dupli_presc,"350");
    echo "<TR><TD nowrap width=30%><B>$msq_desc</B></TD><TD nowrap width=35%><TEXTAREA name='description' class='TEXTAREA' align='middle' rows='6' cols='80'>$description</TEXTAREA><br></TD></TR>";
    $seq_prereq = mysql_query ("select seq_cdn,seq_titre_lb from sequence,sequence_referentiel where seqref_referentiel_no = $id_ref_seq AND seqref_seq_no = seq_cdn AND (seq_publique_on = 1 OR (seq_publique_on = 0 AND seq_auteur_no = $id_user))");
    $nb_seq_prereq = mysql_num_rows ($seq_prereq);
    $lien="sequence_entree.php?liste=$liste&aff_prereq=1&id_seq=$id_seq&id_ref_seq=$id_ref_seq&titre=".addslashes($nom)."&miens=$miens";
    $lien = urlencode($lien);
    $lien1="sequence_entree.php?liste=$liste&def_prereq=1&nom=$nom&duree=$duree&nb_act_seq=$nb_act_seq&acti_seq=$action_seq&id_ref_seq=$id_ref_seq&id_seq=$id_seq&miens=$miens";
    $lien1 = urlencode($lien1);
    echo "<TR><td style=\"font-weight:bold;\">$msgPrpSeq</td>";
    if ($nb_preq == 0)
    {
       echo "<td><A href=\"trace.php?link=$lien1\" target='main'>$msq_def_prereq</A></td></tr>";
    }
    else
    {
       echo "<td><A href=\"trace.php?link=$lien\" target='main'>$mess_menu_consult_favori</A></td></tr>";
    }
    echo "<input type='hidden' name='mots_clef' value=\"$mots_clef\">";
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
    if ($ordre_des_act == 'OUI') {
            echo "$mess_oui&nbsp;<INPUT type='radio' name='ordre_des_act' value='OUI' checked>&nbsp;&nbsp;&nbsp;&nbsp;";
            echo "$mess_non&nbsp;<INPUT type='radio' name='ordre_des_act' value='NON'>";
    }else{
            echo "$mess_oui&nbsp;<INPUT type='radio' name='ordre_des_act' value='OUI'>&nbsp;&nbsp;&nbsp;&nbsp;";
            echo "$mess_non&nbsp;<INPUT type='radio' name='ordre_des_act' value='NON' checked>";
    }
  }
  elseif ($choix == 1 || $choix_act == 1)
  {         // fin de $choix != 1 provenant de list_parc.inc
    if ($type_seq == 0 && $droit_voir_seq == 1)
         $sous_titre .= "<BR><B>".$mess_seq_type." ".strtolower($mess_exp_type)."</B>";
    if ($sous_titre != '')
       echo "<TR><TD colspan='2' class='sous_titre'>$sous_titre</TD></TR>";
    echo "<TR><TD colspan='2'><TABLE>";
    echo "<TR><TD valign='top' width='40%'><B>$msq_titre</B></TD><TD valign='top' width='60%'>$nom</TD></TR>";
    echo "<TR><TD valign='top'><B>$mess_desc</B></TD><TD valign='top'>".
         html_entity_decode($description,ENT_QUOTES,'iso-8859-1')."</TD></TR>";
//    echo "<TR><TD valign='top'><B>$mess_motclef</B></TD><TD valign='top'>$mots_clef</TD></TR>";
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
    echo "</TD></TR><TR height='25'><TD nowrap><B>$msq_duree_seq</B></TD><TD nowrap>$duree</TD></TR>";
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
       echo "<TR height='25'><TD nowrap><B>$msq_nbr_act</B>&nbsp;&nbsp;&nbsp;&nbsp;</TD><TD nowrap>$nb_mod_sco</TD></TR>";
    }else
    {
       $nb_actif = mysql_num_rows(mysql_query ("select act_cdn from activite where act_seq_no =$id_seq"));
       echo "<TR height='25'><TD nowrap><B>$msq_nbr_act</B>&nbsp;&nbsp;&nbsp;&nbsp;</TD><TD nowrap><b>$nb_actif</b></TD></TR>";
    }
    if ($choix != 1 && $choix_act != 1){
        echo boutret(1,0);
        echo "</TD><TD align='left' valign='center'><A HREF=\"javascript:checkForm(document.form1);\" onClick=\"TinyMCE.prototype.triggerSave();\" onmouseover=\"img1.src='images/fiche_identite/boutvalidb$suffixer.gif';return true;\" onmouseout=\"img1.src='images/fiche_identite/boutvalid$suffixer.gif'\">".
         "<IMG NAME=\"img1\" SRC=\"images/fiche_identite/boutvalid$suffixer.gif\" BORDER='0' onLoad=\"tempImg=new Image(0,0); tempImg.src='images/fiche_identite/boutvalidb$suffixer.gif'\"></A>";
        echo "</TD></TR>";
    }
    echo "</FORM><TR width=100%><TD width=100% colspan='2'>&nbsp;</TD></TR>";

    if (!strstr($mod_seq,"SCORM"))
    {
       echo "<TR><TD colspan='3' align='middle' width='100%'>".
            "<div style='clear:both;float:left;background-color:#F4F4F4;padding:20px;border:1px solid #298CA0;margin-left:50px;'>";
       $lien="sequence_entree.php?liste=$liste&id_seq=$id_seq&proprio=$proprio&refer=$refer&activite=1&id_ref=$id_ref&miens=$miens";
       $lien = urlencode($lien);
       echo "<div style='clear:both;float:left;'>".
       "<A href=\"trace.php?link=$lien\" target='main'".
            " onmouseover=\"img110.src='images/modules/icoajoutsequenb.gif';return true;\"".
            " onmouseout=\"img110.src='images/modules/icoajoutsequen.gif'\">".
            "<IMG NAME=\"img110\" SRC=\"images/modules/icoajoutsequen.gif\"  border='0' width='30' height='30'".
            " onLoad=\"tempImg=new Image(0,0); tempImg.src='images/modules/icoajoutsequenb.gif'\">".
            "<BR>$msq_crea_act_seq</A></div>";
       $nb_stars = mysql_num_rows(mysql_query("select * from stars where star_user_id='$id_user' and star_type_no=3"));
       if ($nb_stars > 0)
       {
          $lien_star = "sequence_entree.php?rechercher=1&choix_act=1&star=1&medor=1".getenv("QUERY_STRING");
          $lien_star = urlencode($lien_star);
       }
       if ($rechercher == 1 || $medor == 1)
       {
          if ($nb_stars > 0 && $etat_fav == 'OUI' && $star !=1)
          {
              echo "<div style='float:left; margin-left:30px;'>";
              echo "<a href=\"trace.php?link=$lien_star\" target='main'".
                    bulle($mess_menu_consult_fav_prop,"","RIGHT","BELOW",180).
                   " <img src='images/starfull.gif' border=0><br /><br />$mess_ag_choix $msgseq_mark</a></td><td width=40%>";
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
          $lien="sequence_entree.php?rechercher=1&choix_act=1".getenv("QUERY_STRING");
          $lien = urlencode($lien);
           echo "<div style='float:left; margin-left:30px;'><A href=\"trace.php?link=$lien\" target='main'".
              " onmouseover=\"img111.src='images/modules/icorechsequenb.gif';return true;\"".
              " onmouseout=\"img111.src='images/modules/icorechsequen.gif'\">".
              "<IMG NAME=\"img111\" SRC=\"images/modules/icorechsequen.gif\"  border='0' width='30' height='30'".
              " onLoad=\"tempImg=new Image(0,0); tempImg.src='images/modules/icorechsequenb.gif'\">".
              "<BR>$msq_choix_act</A></div>";
       }
       echo "</div></TD></TR></TABLE>";
    }
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
    echo "</TABLE></TD></TR></TABLE>";
  }
//Accés aux activités a partir du nom de la séquence
if ($consult_act == 1)
{

     if (isset($chaine_act))
     {
       $id_act = $forum_act;
       unset($_SESSION['chaine_act']);
       unset($_SESSION['forum_act']);
     }
     if ($modif == 1 && !strstr($mod_seq,"SCORM"))
     {
        if (isset($modif_ressource) && $modif_ressource == 1)
        {
           $letitre= $mess_ass_act;
           entete_concept("liste_seq.inc.php",$letitre);
           echo aide_simple("sequence");
           echo "<TR><TD><TABLE border=0 cellpadding='4' cellspacing = '4'>";
           // ressource sujet forum
           $chaine_act="&liste=$liste&id_seq=$id_seq&proprio=$proprio&refer=$refer&id_ref_seq=$id_ref_seq&miens=$miens&acces=_entree";
           $params = str_replace("&","|",$chaine_act);
           $forum_act  = $id_act;
           $_SESSION['forum_act'] = $forum_act;
           $_SESSION['chaine_act'] = $chaine_act;
           $lien="sequence_entree.php?id_seq=$id_seq&consult_act=1&encreation=1&parcours=$parcours&liste=$liste&id_parc=$id_parc&id_ref_seq=$id_ref_seq&miens=$miens";
           $lien = urlencode($lien);
           echo "<TR height='40'><TD align='left' nowrap>$bouton_gauche<A href=\"trace.php?link=$lien\" target='main'>$mess_plustard</A>$bouton_droite</TD></TR>";
           echo "<TR height='40'><TD valign='top'><Font size='2'><B>$mess_assress</B></TD></TR>";//$msq_ass_ress_form$mess_assres_comp
           $lien="recherche.php?id_act=$id_act&acces=_entree&parcours=$parcours&liste=$liste&id_parc=$id_parc&id_ref_seq=$id_ref_seq&modif_act=1&id_seq=$id_seq";
           $lien = urlencode($lien);
           echo "<TR><TD align='left' nowrap valign='top'>$bouton_gauche<A href=\"trace.php?link=$lien\" target='main'>$mess_ressidx</A>$bouton_droite</TD><TD valign='top'>$mess_goidx</TD></TR>";
           $lien="charge_ressource_vrac.php?id_act=$id_act&dou=_entree&params=$params&charger_fichier=1";
           $lien = urlencode($lien);
           echo "<TR height='40'><TD align='left' nowrap valign='top'>$bouton_gauche";
           echo "<A HREF=\"#\" title =\"$mess_ajt_ress_act\" onClick=\"window.open('trace.php?link=$lien','','scrollbars=yes,resizable=yes,width=500,height=300,left=250,top=300')\">".
                "$mess_ajt_ress_act</A>$bouton_droite</TD><TD>$mess_ajt_complement</TD></TR>";
           $lien="charge_ressource_vrac.php?id_act=$id_act&dou=_entree&params=$params&charger_url=1";
           $lien = urlencode($lien);
           echo "<TR height='40'><TD align='left' nowrap valign='top'>$bouton_gauche";
           echo "<A HREF=\"javascript:void(0);\" title =\"$mess_ajt_url_act\" onClick=\"window.open('trace.php?link=$lien','','scrollbars=yes,resizable=yes,width=500,height=300,left=250,top=300')\">$mess_ajt_url_act</A>$bouton_droite</TD><TD></TD></TR>";
           //quizz é créer
           $lien="creation_qcm.php?creation_qcm=1&params_qcm=$params&id_activit=$id_act&venu=act&acced=_entree";
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
           $lien="sequence_entree.php?liste=$liste&modif_act=1&id_act=$id_act&id_seq=$id_seq&choix_ress=1&parcours=$parcours&id_parc=$id_parc&action_act=1&id_ref_seq=$id_ref_seq&id_ress=$id_ress&miens=$miens";
           $lien=urlencode($lien);
           echo "<TR height='15'><TD colspan='2'>&nbsp;</td></tr>";
           echo "<TR><TD align='left' nowrap valign='top'>$bouton_gauche<A href=\"trace.php?link=$lien\" target='main'>$mess_ret</A>$bouton_droite</TD></TR>";//retour
           $html = "";
           echo fin_tableau($html);
           exit;
        }
        if ($id_ress > 0)
        {
          $url_ress = GetDataField($connect,"SELECT ress_url_lb FROM ressource_new WHERE ress_cdn=$id_ress","ress_url_lb");
          $titre_ress = GetDataField($connect,"SELECT ress_titre FROM ressource_new WHERE ress_cdn=$id_ress","ress_titre");
          $desc_ress = GetDataField($connect,"SELECT ress_desc_cmt FROM ressource_new WHERE ress_cdn=$id_ress","ress_desc_cmt");
        }
        if ($forum == 1 || strstr($url_ress,"forum/read.php"))
        {
          $verif_sql = mysql_query("SELECT ress_cdn FROM ressource_new WHERE ress_cat_lb=\"Forums\" AND ress_cdn = ress_typress_no");
          $nbr_f = mysql_num_rows($verif_sql);
          if ($nbr_f == 0){
            $new_catid = Donne_ID ($connect,"select max(ress_cdn) from ressource_new");
            $sql_insere= mysql_query("INSERT INTO ressource_new (ress_cdn,ress_cat_lb,ress_typress_no,ress_url_lb,ress_auteurs_cmt,ress_publique_on,ress_titre,ress_desc,ress_create_dt,ress_ajout,ress_public_no,ress_type,ress_support,ress_niveau) VALUES ('$new_catid',\"Forums\",'$new_catid','','','OUI','','',\"$date_dujour\",'foad','TOUT','','1','1')");
          }else
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
              if ($nb_verif_other == 1 && strstr($url_ress,"forum/read.php")){
                $id_ress =$verif_exist;
                $sql_update= mysql_query("UPDATE ressource_new SET ress_modif_dt=\"$date_dujour\",ress_url_lb=\"$le_lien\" WHERE ress_cdn = $verif_exist");
              }else{
                $id_ress = Donne_ID ($connect,"select max(ress_cdn) from ressource_new");
                $sql_insere= mysql_query("INSERT INTO ressource_new (ress_cdn,ress_cat_lb,ress_typress_no,ress_url_lb,ress_auteurs_cmt,ress_publique_on,ress_titre,ress_desc_cmt,ress_create_dt,ress_ajout,ress_type,ress_support,ress_niveau) VALUES ('$id_ress',\"Forums\",$new_catid,\"$le_lien\",\"forum\",\"NON\",\"$titre_forum\",\"$desc_forum\",\"$date_dujour\",\"$login\",\"ACQUISITION\",\"Web\",\"1\")");
              }
            }else{
              $id_ress = Donne_ID ($connect,"select max(ress_cdn) from ressource_new");
              $sql_insere= mysql_query("INSERT INTO ressource_new (ress_cdn,ress_cat_lb,ress_typress_no,ress_url_lb,ress_auteurs_cmt,ress_publique_on,ress_titre,ress_desc_cmt,ress_create_dt,ress_ajout,ress_type,ress_support,ress_niveau) VALUES ('$id_ress',\"Forums\",$new_catid,\"$le_lien\",\"forum\",\"NON\",\"$titre_forum\",\"$desc_forum\",\"$date_dujour\",\"$login\",\"ACQUISITION\",\"Web\",\"1\")");
            }
          }elseif (!isset($forum) && strstr($url_ress,"forum/read.php") && $arrive == "sequence"){
              $id_ress = Donne_ID ($connect,"select max(ress_cdn) from ressource_new");
              $sql_insere= mysql_query("INSERT INTO ressource_new (ress_cdn,ress_cat_lb,ress_typress_no,ress_url_lb,ress_auteurs_cmt,ress_publique_on,ress_titre,ress_desc_cmt,ress_create_dt,ress_ajout,ress_type,ress_support,ress_niveau) VALUES ('$id_ress',\"Forums\",$new_catid,\"$url_ress\",\"forum\",\"NON\",\"$titre_ress\",\"$desc_ress\",\"$date_dujour\",\"$login\",\"ACQUISITION\",\"Web\",\"1\")");
          }
          $act_query = mysql_query("SELECT * FROM activite WHERE act_cdn=$id_act");
//        $nom = mysql_result ($act_query,0,"act_nom_lb");
          $auteur = mysql_result ($act_query,0,"act_auteur_no");
          $duree = mysql_result ($act_query,0,"act_duree_nb");
          $ordre = mysql_result ($act_query,0,"act_ordre_nb");
          if ($forum == 1)
          {
            $nom = mysql_result ($act_query,0,"act_nom_lb");
            $consigne = html_entity_decode(mysql_result ($act_query,0,"act_consigne_cmt"),ENT_QUOTES,'iso-8859-1');
            $commentaire = html_entity_decode(mysql_result ($act_query,0,"act_commentaire_cmt"),ENT_QUOTES,'iso-8859-1');
            $ress_norok = mysql_result ($act_query,0,"act_ress_on");
            $flag = mysql_result ($act_query,0,"act_flag_on");
            $pass_mult = mysql_result ($act_query,0,"act_passagemult_on");
            $acquit = mysql_result ($act_query,0,"act_acquittement_lb");
            $notation = mysql_result ($act_query,0,"act_notation_on");
            $droit_voir = mysql_result ($act_query,0,"act_publique_on");
            $dev_a_rendre = mysql_result ($act_query,0,"act_devoirarendre_on");
            $id_seq = GetDataField ($connect,"select act_seq_no from activite where act_cdn = $id_act","act_seq_no");
            $url_ress = GetDataField($connect,"SELECT ress_url_lb FROM ressource_new WHERE ress_cdn=$id_ress","ress_url_lb");
          }elseif(!isset($forum)  && !strstr($url_ress,"forum/read.php"))
            $nom = mysql_result ($act_query,0,"act_nom_lb");

        }// fin de if forum .....
        if ($supp_ress == 'on' && strstr($url_ress,"forum/read.php"))
           $requete = mysql_query("DELETE FROM ressource_new WHERE ress_cdn =$id_ress");
        if ($id_ress > 0 && $ress_norok == 'NON' && !isset($supp_ress))
           $ress_norok = 'OUI';
        if (($supp_ress == 'on' && !strstr($url_ress,"forum/read.php")) || $ress_norok == "NON"){
           $flag = 1;
           $id_ress = 0;
        }elseif ($id_ress == 0 && $ress_norok == "OUI")
           $flag = 0;
        elseif ($id_ress > 0 && $ress_norok == "NON")
           $id_ress = 0;
        elseif ($id_ress > 0)
           $flag = 1;
        $requete_grp = mysql_query ("select * from groupe");
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
        $lien="sequence_entree.php?liste=$liste&modif_act=1&id_act=$id_act&id_seq=$id_seq&choix_ress=1&parcours=$parcours&id_parc=$id_parc&action_act=1&id_ref_seq=$id_ref_seq&id_ress=$id_ress&miens=$miens";
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
        //on intervertit l'ordre des activites
        //on verifie que le nvel ordre n'est pas sup au nb d'activites
        $nb_act_query = mysql_query ("select count(act_cdn) from activite where act_seq_no = $id_seq");
        $nb_act = mysql_result ($nb_act_query,0);
        if ($ordre < $nb_act || $ordre == $nb_act)
        {
            $actinv = GetDataField ($connect,"select act_cdn from activite where act_ordre_nb = $ordre and act_seq_no = $id_seq","act_cdn");
            if (isset($ordre1))
               $upd_actinv = mysql_query ("update activite set act_ordre_nb = '$ordre1', act_modif_dt = \"$date_dujour\" where act_cdn = $actinv");
            $modifie_rss = rss :: modifie('activite',$id_user,$actinv);
            //Mise a jour de l'activite en cours
            //dey Dfoad
            $req_typdev = mysql_result(mysql_query("select count(*) from activite_devoir where actdev_act_no = $id_act"),0);
            if ($req_typdev > 0)
                 $req_devoir = mysql_query("update activite_devoir set actdev_dev_lb=\"$typdev\" where actdev_act_no='$id_act'");
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
            $son_nom_act = GetDataField ($connect,"select act_nom_lb from activite where act_cdn=$id_act","act_nom_lb");
            $message = $son_nom_act." ".$mess_admin_fic_modif_fin;
        }else
            echo $msq_bad_ordre;
        if ($id_ress != 0)
        {
           $requete_grp = mysql_query ("select * from groupe");
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
                                  htmlentities($description,ENT_QUOTES,'iso-8859-1')."\",mod_consigne_cmt=\"".
                                  htmlentities($consigne,ENT_QUOTES,'iso-8859-1')."\",mod_motclef_lb=\"".
                                  htmlentities($motclef,ENT_QUOTES,'iso-8859-1')."\" where mod_cdn = '$id_act'");
       else
          $upd_act = mysql_query ("update scorm_module set mod_titre_lb=\"$nom\",mod_desc_cmt=\"".
                                  htmlentities($description,ENT_QUOTES,'iso-8859-1')."\",mod_consigne_cmt=\"".
                                  htmlentities($consigne,ENT_QUOTES,'iso-8859-1')."\",mod_motclef_lb=\"".
                                  htmlentities($motclef,ENT_QUOTES,'iso-8859-1')."\",mod_duree_nb='$duree',mod_maxtimeallowed=\"$max_T\",".
                                  "mod_masteryscore='$score_min',mod_visible=\"$visible\",mod_prereq_lb = \"$prereq\" where mod_cdn = '$id_act'");
       $son_nom_act = GetDataField ($connect,"select mod_titre_lb from scorm_module where mod_cdn=$id_act","mod_titre_lb");
       $message = $son_nom_act." ".$mess_admin_fic_modif_fin;
    }// if (modif == 1)
// if (modif == 1)
    if  ($monter == 1 || $descendre == 1)
    {
          if ($monter == 1)
            $nouvel_ordre = GetDataField ($connect,"select act_ordre_nb from activite where act_cdn=$act_precedente and act_seq_no=$id_seq","act_ordre_nb");
          else
            $nouvel_ordre = GetDataField ($connect,"select act_ordre_nb from activite where act_cdn =$act_suivante and act_seq_no=$id_seq","act_ordre_nb");
          $ancien_ordre = GetDataField ($connect,"select act_ordre_nb from activite where act_cdn =$act_actuelle and act_seq_no=$id_seq","act_ordre_nb");
          $modif_ordre = mysql_query("UPDATE activite SET act_ordre_nb = $nouvel_ordre, act_modif_dt = \"$date_dujour\" where act_cdn = $act_actuelle and act_seq_no=$id_seq");
          $ancien_act = GetDataField ($connect,"select act_cdn from activite where act_ordre_nb = $nouvel_ordre and act_seq_no = $id_seq","act_cdn");
          $modifie_rss = rss :: modifie('activite',$id_user,$act_actuelle);
          $modif_ordre = mysql_query("UPDATE activite SET act_ordre_nb = $ancien_ordre, act_modif_dt = \"$date_dujour\" where act_cdn = $ancien_act");
          $modifie_rss = rss :: modifie('activite',$id_user,$ancien_act);
    }
    if ($supp == 1)
    {
       //il faut supprimer la duree de l'act a la duree de la seq
       $requete_grp = mysql_query ("select * from groupe");
       $nb_grp = mysql_num_rows($requete_grp);
       if ($nb_grp > 0)
       {
            $gp=0;$Nb_act_suivi = 0;
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
        $son_nom_act = GetDataField ($connect,"select act_nom_lb from activite where act_cdn=$id_act","act_nom_lb");
        $duree_act = GetDataField ($connect,"select act_duree_nb from activite where act_cdn=$id_act","act_duree_nb");
        $duree_seq = GetDataField ($connect,"select seq_duree_nb from sequence where seq_cdn = $id_seq","seq_duree_nb");
        $duree_seq-=$duree_act;
        $upd_dureeseq = mysql_query ("update sequence set seq_duree_nb = $duree_seq, seq_modif_dt = \"$date_dujour\" where seq_cdn = $id_seq");
        $modifie_rss = rss :: modifie('sequence',$id_user,$id_seq);
        $supp_star = mysql_query("delete from stars where star_item_id = '$id_act' and star_type_no='3'");
        $del_act = mysql_query ("delete from activite where act_cdn = $id_act");
            //dey Dfoad
               $req_typdev = mysql_result(mysql_query("select count(*) from activite_devoir where actdev_act_no = $id_act"),0);
               if ($req_typdev > 0)
               {
                   $req_devoir = mysql_query("update activite_devoir set actdev_dev_lb=\"$typdev\" where actdev_act_no='$id_act'");
               }
               else
                 $req_devoir = mysql_query("insert into activite_devoir values('','$id_act',\"$typdev\")");
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
        $message = $son_nom_act." ".$mess_admin_sup_mess_fin;
        // remet les compteurs d'activités dans l'ordre
        $sql = mysql_query("SELECT act_cdn,act_ordre_nb,act_duree_nb FROM activite WHERE act_seq_no = $id_seq ORDER BY act_ordre_nb ASC");
        $res = mysql_num_rows($sql);
        $x = 0;
        $duree_tot=0;
        while ($x < $res)
        {
          $ordre_act = mysql_result($sql,$x,"act_cdn");
          $duree_act = mysql_result($sql,$x,"act_duree_nb");
          $duree_tot += $duree_act;
          $y = $x+1;
          $nouvel_ordre = $y;
          $correction = mysql_query("UPDATE activite SET act_ordre_nb = $nouvel_ordre, act_modif_dt = \"$date_dujour\" where act_cdn = $ordre_act");
          $modifie_rss = rss :: modifie('activite',$id_user,$ordre_act);
         $x++;
        }
        $duree_seq = mysql_query ("UPDATE sequence set seq_duree_nb = $duree_tot, seq_modif_dt = \"$date_dujour\" where seq_cdn = $id_seq");
        $modifie_rss = rss :: modifie('sequence',$id_user,$id_seq);
        $requete_grp = mysql_query ("select * from groupe");
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
// fin modif
    $mod_seq = GetDataField ($connect,"select seq_type_lb from sequence where seq_cdn = '$id_seq'","seq_type_lb");
    if ($mod_seq == 'NORMAL')
       $act_query = mysql_query ("select * from activite where act_seq_no = $id_seq order by act_ordre_nb");
    else
       $act_query = mysql_query ("select * from scorm_module where mod_seq_no = $id_seq order by mod_cdn");
    $nb_act_seq = mysql_num_rows ($act_query);
    if ($mod_seq != 'NORMAL')
    {
         $id_auteur = GetDataField ($connect,"select seq_auteur_no from sequence where seq_cdn = '$id_seq'","seq_auteur_no");
         $titre_sequence = GetDataField ($connect,"select seq_titre_lb from sequence where seq_cdn = '$id_seq'","seq_titre_lb");
         $nom_sequence = GetDataField ($connect,"select seq_desc_cmt from sequence where seq_cdn = '$id_seq'","seq_desc_cmt");
         $droit_dupli_seq = GetDataField ($connect,"select seq_publique_on from sequence where seq_cdn = '$id_seq'","seq_publique_on");
         $id_ref = GetDataField ($connect,"select seqref_referentiel_no from sequence_referentiel where seqref_seq_no = '$id_seq'","seqref_referentiel_no");
         if ($nom_sequence == '')
            $nom_sequence = $msq_no_decrite;
         $bgcolor2 = '#2b677a';
         $bgcolor1 = '#F8F2E4';
         $letitre ="$msq_seq : $titre_sequence";
         if (isset($message) && $message != '')
             echo notifier($message);
         entete_concept("liste_seq.inc.php",$letitre);
         echo aide_simple("sequence");
         if ($id_ref > 0 && $sous_titre != '')
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
               $description = html_entity_decode(mysql_result ($act_query,$i,"mod_desc_cmt"),ENT_QUOTES,'iso-8859-1');
               $motclef = html_entity_decode(mysql_result ($act_query,$i,"mod_motclef_lb"),ENT_QUOTES,'iso-8859-1');
               $numero = mysql_result ($act_query,$i,"mod_numero_lb");
               $launch = mysql_result ($act_query,$i,"mod_launch_lb");
               $visible = mysql_result ($act_query,$i,"mod_visible");
               $duree = mysql_result ($act_query,$i,"mod_duree_nb");
               $prerequis = mysql_result ($act_query,$i,"mod_prereq_lb");
               $score_max = mysql_result ($act_query,$i,"mod_masteryscore");
               $maxtime = mysql_result ($act_query,$i,"mod_maxtimeallowed");
               $aff_cadenas = "<IMG SRC=\"images/modules/tut_form/icocadenas.gif\" border='0'>";
               if ($visible == 'TRUE')
                  $visib = 'OUI';
               else
                  $visib = 'NON';
               if ($maxtime != '' && strlen($maxtime) > 9){
                  if (strstr($maxtime,'t') || strstr($maxtime,'T'))
                     $maxtime = substr($maxtime,13);
                  $liste_duree = explode(":",$maxtime);
                  $maxtime = ($liste_duree[0]*60)+$liste_duree[1];
               }
               if ($prerequis != ""){
                  $prereq1 = GetDataField ($connect,"select mod_cdn from scorm_module where mod_numero_lb = \"$prerequis\"","mod_cdn");
                  $prereq = $code[$prereq1]+1;
               }else
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
                  /*  if (!strstr($launch,"http://") && $launch != '')
                       $launch = $launch;
                    elseif (strstr($launch,"http://"))
                       $launch = $launch;
                    //$session_lms = md5($login)."|".$sco['id_mod'][$i]."|$seq|$id_parc|$id_user|$numero_groupe";
                    //$suitable = "aicc_sid=$session_lms&aicc_url=".urlencode("$adresse_http/scorm/aicc.php")."&vendor_param=".$sco['datafromlms'][$i];
                   */
                    $launcher = $launch."?aicc_sid=&aicc_url=$suitable&vendor_param=".$datafromlms;
                    $lelaunch = $launch;
                    $launch = urlencode($launch);
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
                  $lien = "modif_sco.php?liste=$liste&parcours=$parcours&id_parc=$id_parc&proprio=$proprio&refer=$refer&id_ref=$id_ref&id_ref_parc=$id_ref_parc&id_seq=$id_seq&id_act=$id&modif_act=1&miens=$miens&miens_parc=$miens_parc&parc=$id_parc&liste_act_seq=$liste_act_seq&prov=seq";
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
         fin_tableau($html);
         exit;
   }else{
      if ($id_seq == 0)
      {
       $ordre_req = "act_auteur_no";
       $letitre = $mess_act_libres;
     }
     else
     {
       $id_auteur = GetDataField ($connect,"select seq_auteur_no from sequence where seq_cdn = '$id_seq'","seq_auteur_no");
       $titre_sequence = GetDataField ($connect,"select seq_titre_lb from sequence where seq_cdn = '$id_seq'","seq_titre_lb");
       $nom_sequence = GetDataField ($connect,"select seq_desc_cmt from sequence where seq_cdn = '$id_seq'","seq_desc_cmt");
       $droit_dupli_seq = GetDataField ($connect,"select seq_publique_on from sequence where seq_cdn = '$id_seq'","seq_publique_on");
       $id_ref_seq = GetDataField ($connect,"select seqref_referentiel_no from sequence_referentiel where seqref_seq_no = '$id_seq'","seqref_referentiel_no");
       if ($nom_sequence == '')
          $nom_sequence = $msq_no_decrite;
       $letitre ="$msq_seq : $titre_sequence";
       $ordre_req = "act_ordre_nb";
     }
     $act_query = mysql_query ("select * from activite where act_seq_no = $id_seq order by $ordre_req");
     $nb_act_seq = mysql_num_rows ($act_query);
     if ($nb_act_seq == 0 && ($id_user == $id_auteur || $typ_user == 'ADMINISTRATEUR'))
     {
         $letitre = "$mess_bas_gest_seq : $titre_sequence";
         entete_concept("liste_seq.inc.php",$letitre);
         echo aide_simple("sequence");
         echo "<TR><TD><Font size='2'>$msq_noact</TD></TR>";
         echo "<TR><TD><DIV id='sequence'><a href=\"sequence_entree.php?liste=$liste&id_seq=$id_seq&parcours=$parcours&activite=1&id_parc=$id_parc&id_ref_seq=$id_ref_seq&miens=$miens\" target='main'>$msq_crea_act_seq</a>";
     }
     elseif ($nb_act_seq == 0 && ($id_user != $id_auteur && $typ_user != 'ADMINISTRATEUR')) {
         $message_alerte = $mess_nopublic_act;
         $lien = "sequence_entree.php?liste=1&flg_seq=$flg_seq&parcours=$parcours&id_seq=$id_seq&id_parc=$id_parc&consult=1&id_ref_seq=$id_ref_seq&miens=$miens&message_alerte=$message_alerte";
         $lien = urlencode($lien);
         echo "<script language=\"JavaScript\">";
         echo "document.location.replace(\"trace.php?link=$lien\")";
         echo "</script>";
     }
     else
     {
         $bgcolor2 = '#2b677a';
         $bgcolor1 = '#F8F2E4';
         entete_concept("liste_seq.inc.php",$letitre);
         echo aide_simple("sequence");
         if ($id_ref_seq > 0 && $sous_titre != '')
              echo "<TR><TD class='sous_titre'>$sous_titre</TD></TR>";
         echo "<P><TR><TD width='100%'><TABLE width='100%' cellspacing='1' cellpadding='4'>";
         echo "<TR bgcolor=$bgcolor2>";
         if (($id_auteur == $id_user || $typ_user == 'ADMINISTRATEUR') && $id_seq > 0){
           echo "<TD height='20' align='left'><IMG SRC=\"images/modules/tut_form/flechebas.gif\" TITLE=\"$mess_increment\" border='0'></TD>";
           echo "<TD height='20' align='left'><IMG SRC=\"images/modules/tut_form/flechehaut.gif\" TITLE=\"$mess_decrement\" border='0'></TD>";
         }
         echo "<TD height='20' align='left'><FONT COLOR=white><b>$msq_activite</b></FONT></TD>";
         echo "<TD height='20' align='left'><IMG SRC=\"images/i-lav.gif\" border=0 width='15' height='15' ></TD>";
         if ($id_seq > 0)
           echo "<TD height='20' align='left'><FONT COLOR=white><b>$msq_aff_ordre</b></FONT></TD>";
         echo "<TD height='20' align='left'><FONT COLOR=white><b>$msq_aff_cons</b></FONT></TD>";
         echo "<TD height='20' align='left'><FONT COLOR=white><b>$msq_duree_seq</b></FONT></TD>";
         echo "<TD height='20' align='left'><FONT COLOR=white><b>$msq_ress_assoc</b></FONT></TD>";
         echo "<TD align='left'><FONT COLOR=white><b>$mess_modif_base</b></FONT></TD>".
              "<TD align='left'><FONT COLOR=white><b>$mess_ag_supp</b></FONT></TD>";
         if ($id_seq > 0)
            echo "<TD height='20' align='left'><FONT COLOR=white><b>$mess_dupliquer</b></FONT></TD>";
         echo "</TR>";
          $i = 0;
          $id_act_seq = array();
           while ($i < $nb_act_seq) {
               $id = mysql_result ($act_query,$i,"act_cdn");
               $nom = mysql_result ($act_query,$i,"act_nom_lb");
               $ordre = mysql_result ($act_query,$i,"act_ordre_nb");
               $flag = mysql_result ($act_query,$i,"act_flag_on");
               $id_ress = mysql_result ($act_query,$i,"act_ress_no");
               $duree = mysql_result ($act_query,$i,"act_duree_nb");
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
//               $consigne1 = str_replace("<BR>","",$consigne);
//               $commentaire1 = str_replace("<BR>","",$commentaire);
               if ($dupli_act == 1 && $flag == 1 && $id_act == $id){
                 $nom_user = GetDataField ($connect,"select util_nom_lb from utilisateur where util_cdn = $id_user","util_nom_lb");
                 $nom_act = $nom." ($nom_user)";
                 $dupli_act = 0;
                 $num_act = Donne_ID ($connect,"select max(act_ordre_nb) from activite where act_seq_no = 0");
                 $id_nouv = Donne_ID ($connect,"select max(act_cdn) from activite");
                 $insert_act_exist = mysql_query ("INSERT INTO activite values ($id_nouv,0,$num_act,\"".str_replace("\"","'",$nom_act)."\",\"".
                                                  htmlentities($consigne,ENT_QUOTES,'iso-8859-1')."\",\"".
                                                  htmlentities($commentaire,ENT_QUOTES,'iso-8859-1')."\",'$ress_norok',$id_ress,\"$duree\",\"$pass_mult\",\"$acquit\",".
                                                  "\"$notation\",\"$devoir\",'$id_user',\"$date_dujour\",\"$date_dujour\",'$droit_voir_act','$flag')");
                 $insert_rss = rss :: ajout('activite',$id_user,$id_nouv);
               }
               $l = $i+1;
               $id_act_seq[$i] = $id;
               if (($id_auteur == $id_user || $typ_user == 'ADMINISTRATEUR') && $nb_act_seq > 1)
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
               $non_affic_ress_lien = 0;
               if ($id_ress == 0)
                 $ressource = $msq_aucune;
               else {
                  $ressource = GetDataField ($connect,"select ress_titre from ressource_new where ress_cdn = $id_ress","ress_titre");
                  $url_ressource = GetDataField ($connect,"select ress_url_lb from ressource_new where ress_cdn = $id_ress","ress_url_lb");
                  //Dans le cas ou la ressource a ete supprimee
                  if ($url_ressource == '')
                       $ressource = $ressource;
                  else {
                       $typ_ress = GetDataField ($connect,"select ress_support from ressource_new where ress_cdn = $id_ress","ress_support");
                       $typ_ress = strtoupper ($typ_ress);
                  }
               } //fin else

                   if ($auteur != $id_user  && $droit_voir_act == 0)// && $id_ress > 0 && $url_ressource != ""
                      $aff_cadenas = "<IMG SRC=\"images/modules/tut_form/icocadenas.gif\" border='0'>&nbsp;";
                   else
                      $aff_cadenas = "";
                   echo couleur_tr($i+1,'');
                   if (($auteur == $id_user || $typ_user == 'ADMINISTRATEUR') && $id_seq > 0)
                   {
                     if ($i < ($nb_act_seq-1))
                     {
                       $lien = "sequence_entree.php?consult_act=1&descendre=1&id_parc=$id_parc&id_act=$id&id_seq=$id_seq&act_precedente=$act_precedente&act_actuelle=$act_actuelle&act_suivante=$act_suivante&parcours=$parcours&liste=$liste&id_ref_seq=$id_ref_seq&miens=$miens";
                       $lien = urlencode($lien);
                       echo "<TD height='20' align='middle' valign='top'><A HREF=\"trace.php?link=$lien\" target='main' title = '$mess_increment'><IMG SRC=\"images/modules/tut_form/flechebas.gif\" TITLE=\"$mess_increment\"  border='0'></TD>";
                     }
                     else
                        echo "<TD></TD>";
                     if ($i > 0)
                     {
                       $lien = "sequence_entree.php?consult_act=1&monter=1&id_parc=$id_parc&id_act=$id&id_seq=$id_seq&act_precedente=$act_precedente&act_actuelle=$act_actuelle&act_suivante=$act_suivante&parcours=$parcours&liste=$liste&id_ref_seq=$id_ref_seq&miens=$miens";
                       $lien = urlencode($lien);
                       echo "<TD height='20' align='middle' valign='top'><A HREF=\"trace.php?link=$lien\" target='main' title = '$mess_decrement'><IMG SRC=\"images/modules/tut_form/flechehaut.gif\" TITLE=\"$mess_decrement\" border='0'></TD>";
                     }
                     else
                        echo "<TD></TD>";
                   }
                   /*
                   if ($commentaire != "")
                     $commentaire1 = $commentaire;//addcslashes($commentaire,"\0..\47!@\176..\255");
                   else
                     $commentaire1 = $mess_no_comment;
                     */
//dey Dfoad <span au lieu de <a>
                   echo "<TD valign='top'><span style='font-weight:bold;'> $aff_cadenas $nom</span></TD>";
                   echo "<TD valign='top'><span style='cursor:help;' ";
                   $titre_bulle ="$msq_aff_pass_mult <B>$pass_mult</B><BR>$msq_aff_acquit <B>$acquit</B><BR>$msq_aff_dev_rend <B>$devoir</B><BR>$msq_act_evalue <B>$notation</B>";
                   echo bulle($titre_bulle,$msq_fic_act,"LEFT","ABOVE",250);
                   $titre_bulle = "";
                   echo "<IMG SRC=\"images/i-lav.gif\" border=0 width='15' height='15'></span></td>";
                   if ($id_seq > 0)
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
                          ($auteur == $id_user || $droit_voir_act == 1 || $typ_user == 'ADMINISTRATEUR'))
                          {
                      $lien = $url_ressource;
                      $url_ressource = serveur_externe($url_ressource);
                      $lien=urldecode($url_ressource);
                      echo "<TD valign='top'><DIV id='sequence'><A href=\"$lien\" target='_blank'>$ressource</A></DIV>";
                   }
                   elseif (strstr(strtolower($url_ressource),".flv") ||
                           strstr(strtolower($url_ressource),".mp3") ||
                           strstr(strtolower($url_ressource),".swf")  ||
                           strstr(strtolower($url_ressource),".mp4") ||
                           strstr(strtolower($url_ressource),".ogv") ||
                           strstr(strtolower($url_ressource),".webm")&&
                          ($auteur == $id_user || $typ_user == 'ADMINISTRATEUR' || $droit_voir_act == 1))
                   {
                     $lien = "lanceMedia.php?id_ress=$id_ress";
                     echo "<TD align='left' valign='top'><DIV id='sequence'><A href=\"javascript:void(0);\" onclick=\"window.open('$lien','','resizable=yes,scrollbars=yes,status=no')\">$ressource</a></DIV></td>";
                   }
                   elseif ($id_ress > 0 && $url_ressource != "" &&
                         ($auteur == $id_user || $droit_voir_act == 1 || $typ_user == 'ADMINISTRATEUR'))
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
                      elseif ($dev_act == 'xApi TinCan')
                      {
                         $lien = $url_ressource.TinCanTeach ('teacher|0|'.$id_seq.'|'.$id.'|0',$url_ressource,$commentaire);
                         echo "<TD align='left' valign='top'><DIV id='sequence'><A href=\"javascript:void(0);\" onclick=\"window.open('$lien','','resizable=yes,scrollbars=yes,status=no')\">$ressource</a></DIV></td>";
                      }
                   }
                   else
                      echo "<TD align='left' valign='top'>$ressource</td>";
                   if ($auteur == $id_user || $typ_user == 'ADMINISTRATEUR')
                   {
                     $lien = "sequence_entree.php?liste=$liste&action_act=1&parcours=$parcours&id_parc=$id_parc&id_ref_seq=$id_ref_seq&id_seq=$id_seq&id_act=$id&id_ress=$id_ress&modif_act=1&miens=$miens";
                     $lien = urlencode($lien);
                     echo "<TD width='2%' align='middle' valign='top'><a href=\"trace.php?link=$lien\" target='main'>".
                          "<IMG SRC=\"images/repertoire/icoGrenomfich20.gif\" alt=\"$msq_modifier\" BORDER=0></A></td>";
                   }
                   else
                     echo "<TD>&nbsp;</TD>";
                   if (($id_seq > 0 || $id_seq == 0) && ($auteur == $id_user || $typ_user == 'ADMINISTRATEUR'))
                   {
                     $requete_grp = mysql_query ("select * from groupe");
                     $nb_grp = mysql_num_rows($requete_grp);
                     if ($nb_grp > 0)
                     {
                           $gp=0;$Nb_act_suivi = 0;
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
                        $lien = "sequence_entree.php?consult_act=1&parcours=$parcours&liste=$liste&id_parc=$id_parc&supp=1&id_act=$id&id_seq=$id_seq&id_ref_seq=$id_ref_seq&miens=$miens";
                        $lien = urlencode($lien);
                        echo "<TD width='2%' align='middle' valign='top'><a href=\"javascript:void(0);\" onclick=\"javascript:return(confm('trace.php?link=$lien'));\" target='main'".
                             bulle($msq_sup_act,"","RIGHT","ABOVE",140).
                             "<IMG SRC=\"images/messagerie/icopoubelressour.gif\" border=0></A></td>";
                     }
                     else
                     {
                        echo "<TD valign='top' width='2%' align='middle'".
                             bulle($msq_act_no_supp,"","LEFT","ABOVE",240).
                             "<IMG SRC=\"images/repertoire/icoptiinterdit.gif\" border='0'></td>";
                     }
                   }
                   else
                     echo "<TD>&nbsp;</TD>";
                   if ($id_seq > 0 && (($auteur == $id_user && $droit_voir_act == 0) || $droit_voir_act == 1))
                   {
                     $lien = "sequence_entree.php?consult_act=1&dupli_act=1&liste=$liste&id_parc=$id_parc&id_act=$id&id_seq=$id_seq&id_ref_seq=$id_ref_seq&miens=$miens";
                     $lien = urlencode($lien);
                     echo "<TD width='2%' align='middle' valign='top'><a href=\"trace.php?link=$lien\" target='main' ".
                          bulle($mess_dupli_act,"","LEFT","",220)."<IMG SRC=\"images/repertoire/icoptiedit.gif\" border='0'></A></TD>";
                   }
                   else
                     echo "<TD>&nbsp;</TD>";
               $i++;
               } //fin while ($i != $nb_seq)
               if ($id_seq > 0)
               {
                 echo "</TR></TABLE>";
                 $id_auteur = GetDataField ($connect,"select seq_auteur_no from sequence where seq_cdn = $id_seq","seq_auteur_no");
                 echo "</TD></TR><TR height='50'><TD><TABLE cellpadding='4' cellspacing='4'><TR>";
                 $lien = "vue_sequence.php?seq=$id_seq";
                 $lien = urlencode($lien);
                 echo "<TD nowrap>$bouton_gauche<A HREF=\"#\" onclick=\"javascript:window.open('trace.php?link=$lien','','top=0,left=0,width=800,height=400,resizable=yes,status=no,scrollbars=yes')\">$mess_vue_app</A>$bouton_droite</TD>";
                 if (!strstr($mod_seq,"SCORM") && ($id_auteur == $id_user || $typ_user == 'ADMINISTRATEUR'))
                 {
                      $lien="export_seq_xml.php?id_seq=$id_seq";
                      echo "<TD nowrap>$bouton_gauche<a href=\"$lien\">Export Scorm de la séquence</a>$bouton_droite</TD>";
                 }
                 echo "</TR></TABLE></TD></TR>";
               }
               else
                 echo "</TR></TABLE></TD></TR>";
          } //fin else ($Nb_act_seq ...)
    }
    if ($vient_de_search != 1 && $encreation != 1)
       echo boutret(1,1);
    echo "</TABLE></TD></TR></TABLE>";
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
    //On reselectionne certaines infos car on ne peut pas toutes les passer par URL (pb d'URL trop longue!!!!)
    //Si l'on vient de d'associer une autre ressource, on doit reselectionner les infos suivantes
    $letitre = $msq_dat_act;

    entete_concept("liste_seq.inc.php",$letitre);
    echo aide_simple("sequence");
    if ($choix_ress == 1) {
        $nom= str_replace ("%","'",$nom);
        $id_seq = GetDataField ($connect,"select act_seq_no from activite where act_cdn = $id_act","act_seq_no");
        if ($id_ress != 0)    //Dans cas ou l'on vient de prerequis
           $ressource = GetDataField ($connect,"select ress_titre from ressource_new where ress_cdn = $id_ress","ress_titre");
    }
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
    $nom = str_replace("\"","'",$nom);
/*
    $consigne = str_replace("<BR>","",$consigne);
    $consigne = str_replace("\"","'",$consigne);
    $commentaire = str_replace("\"","'",$commentaire);
    $commentaire = str_replace("<BR>","",$commentaire);
*/
    echo "<tr><td><table cellpadding='3' cellspacing='2'><tbody>";
    echo "<form name='form1' action=\"sequence_entree.php?liste=$liste&consult_act=1&modif=1&id_act=$id_act&id_seq=$id_seq&ordre1=$ordre&id_ref_seq=$id_ref_seq&auteur=$auteur&miens=$miens\" target='main' method='post'>";
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
    echo "</DIV>";
    echo "<div id='idress'><input type='hidden' name='id_ress' value='$id_ress'></div>";
    if ($modif_act == 1 && $apprenant != 1 && $utilisateur == "" && (!isset($dev_act) || (isset($dev_act) && $dev_act != 'xApi TinCan')))
    {
       $lien="sequence_entree.php?liste=$liste&consult_act=1&modif=1&modif_ressource=1&id_act=$id_act&id_seq=$id_seq&ordre1=$ordre&id_ref_seq=$id_ref_seq&auteur=$auteur&miens=$miens";
       $lien = urlencode($lien);
       if ($id_ress == 0  && $ress_norok == 'OUI')
         echo "<tr><td>$bouton_gauche<a href=\"trace.php?link=$lien\" target='main'>$msq_ass_ress_form</A>$bouton_droite</div></td>";
       elseif ($id_ress > 0 && $ress_norok == 'OUI')
       {
         echo "<tr><td>$bouton_gauche<a href=\"trace.php?link=$lien\" target='main'><div id='ajoute_ress'>$msq_ass_ress</div></A>$bouton_droite</td>";
       }
       if ($ress_norok == 'OUI' && $id_ress > 0)
       {
            echo "<td><div id='supp_ress' name='supp_ress' class='seqcla' ".
                 "onclick=\"javascript:if (conf() == true){appel_simple('formation/ress_supp.php?id_act=$id_act&flag=$flag&ress_norok=$ress_norok');".
                 "var mon_content=document.getElementById('laressource');mon_content.innerHTML='';".
                 "var content=document.getElementById('supp_ress');content.style.visibility='hidden';".
                 "var mon_titre_ress=document.getElementById('ajoute_ress');mon_titre_ress.innerHTML='$msq_ass_ress_form';".
                 "var mon_ress=document.getElementById('idress');mon_ress.innerHTML='<input type=hidden name=\'id_ress\' value=\'\'>';}\">".
                 "$bouton_gauche<font color='#24677A'><B>$msq_slk_ress</B></font> $bouton_droite</div></td></tr>";
       }
    }
     echo "</tbody></table></TD></TR></DIV>";
     echo "<TR><TD nowrap><B>$msq_tit_form</B></TD><TD nowrap>";
     echo "<INPUT TYPE='TEXT' class='INPUT' name='nom' value=\"".stripcslashes(NewHtmlEntityDecode($nom))."\" size='75' align='middle'></TD></TR>";
     echo "<TR><TD nowrap><B>$msq_consigne_act_form</B></TD><TD nowrap>";
     echo "<TEXTAREA class='TEXTAREA' name='consigne' rows='6' cols='80' align='middle'>".
           html_entity_decode($consigne,ENT_QUOTES,'iso-8859-1')."</TEXTAREA>";
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
          $lien="charge_ressource_vrac.php?id_act=$id_act&dou=_entree&media=1&charger_fichier=1";
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
     echo "</TD></TR><TR><TD nowrap><B>$mess_admin_comment</B></TD><TD nowrap>";
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
      echo "<INPUT TYPE='TEXT' class='INPUT' name='ordre' value='$ordre' align='middle' size=1></TD></TR>";
      if ($duree)
      {
         $reste = $duree%60;
         $heure = floor($duree/60);
         $duree1 = duree_calc($seq_duree);
         if ($duree == 0)
         {
            $duree1 = "5".$mn;
            $duree = 5;
         }
      }
      echo "<TR><TD nowrap><B>$msq_duree_form</B></TD><TD><TABLE cellspacing='0'><TR>";
      if ($heure == 0 && $reste == 0){
         echo "<TD><INPUT TYPE='TEXT' class='INPUT' name='horaire' value='' size='2' maxlength = '3' align='center'>$h </TD>";
         echo "<TD><INPUT TYPE='TEXT' class='INPUT' name='minutage' value='' size='2' maxlength = '2' align='center'>$mn</TD></TR></TABLE>";
      }else{
         echo "<TD><INPUT TYPE='TEXT' class='INPUT' class='INPUT' name='horaire' value='$heure' size='2' maxlength = '3' align='center'>$h </TD>";
         echo "<TD><INPUT TYPE='TEXT' class='INPUT' name='minutage' value='$reste' size='2' maxlength = '2' align='center'>$mn</TD></TR></TABLE>";
      }
      echo "</TD></TR>";
      echo "<TR><TD nowrap><B>$msq_pass_act_form</B></TD><TD nowrap>";
      if ($pass_mult == ''){
            echo "$mess_oui&nbsp;&nbsp;<INPUT type='radio' name='pass_mult' value='OUI'>&nbsp;&nbsp;&nbsp;&nbsp;";
            echo "$mess_non&nbsp;&nbsp;<INPUT type='radio' name='pass_mult' value='NON'>";
      }elseif ($pass_mult == 'NON'){
            echo "$mess_oui&nbsp;&nbsp;<INPUT type='radio' name='pass_mult' value='OUI'>&nbsp;&nbsp;&nbsp;&nbsp;";
            echo "$mess_non&nbsp;&nbsp;<INPUT type='radio' name='pass_mult' value='NON' checked>";
      }elseif ($pass_mult == 'OUI'){
            echo "$mess_oui&nbsp;&nbsp;<INPUT type='radio' name='pass_mult' value='OUI' checked>&nbsp;&nbsp;&nbsp;&nbsp;";
            echo "$mess_non&nbsp;&nbsp;<INPUT type='radio' name='pass_mult' value='NON'>";
      }
      echo "<br>";
      echo "</TD></TR>";
      echo "<TR><TD nowrap><B>$msq_acquit_form</B></TD><TD nowrap>";
      if ($acquit== ''){
            echo ucfirst(strtolower($msq_apprenant))."&nbsp;&nbsp;<INPUT type='radio' name='acquit' value='APPRENANT'>&nbsp;&nbsp;&nbsp;&nbsp;";
            echo ucfirst(strtolower($msq_formateur))."&nbsp;&nbsp;<INPUT type='radio' name='acquit' value='FORMATEUR_REFERENT'>&nbsp;&nbsp;&nbsp;&nbsp;";
            echo ucfirst(strtolower($msq_ress))."&nbsp;&nbsp;<INPUT type='radio' name='acquit' value='RESSOURCE' onclick=\"window.alert('$mess_avrt_act_ress');\">";
      }elseif ($acquit== 'APPRENANT'){
            echo ucfirst(strtolower($msq_apprenant))."&nbsp;&nbsp;<INPUT type='radio' name='acquit' value='APPRENANT' checked>&nbsp;&nbsp;&nbsp;&nbsp;";
            echo ucfirst(strtolower($msq_formateur))."&nbsp;&nbsp;<INPUT type='radio' name='acquit' value='FORMATEUR_REFERENT'>&nbsp;&nbsp;&nbsp;&nbsp;";
            echo ucfirst(strtolower($msq_ress))."&nbsp;&nbsp;<INPUT type='radio' name='acquit' value='RESSOURCE' onclick=\"window.alert('$mess_avrt_act_ress');\">";
      }elseif ($acquit== 'FORMATEUR_REFERENT'){
            echo ucfirst(strtolower($msq_apprenant))."&nbsp;&nbsp;<INPUT type='radio' name='acquit' value='APPRENANT' checked>&nbsp;&nbsp;&nbsp;&nbsp;";
            echo ucfirst(strtolower($msq_formateur))."&nbsp;&nbsp;<INPUT type='radio' name='acquit' value='FORMATEUR_REFERENT' checked>&nbsp;&nbsp;&nbsp;&nbsp;";
            echo ucfirst(strtolower($msq_ress))."&nbsp;&nbsp;<INPUT type='radio' name='acquit' value='RESSOURCE' onclick=\"window.alert('$mess_avrt_act_ress');\">";
      }elseif ($acquit== 'RESSOURCE'){
            echo ucfirst(strtolower($msq_apprenant))."&nbsp;&nbsp;<INPUT type='radio' name='acquit' value='APPRENANT' checked>&nbsp;&nbsp;&nbsp;&nbsp;";
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
   if (($auteur == $id_user || $typ_user == 'ADMINISTRATEUR') && $modif_act == 1)
   {
       echo boutret(1,0);
    echo "</TD><TD align='left' height='50'><A HREF=\"javascript:checkForm(document.form1);\" onClick=\"TinyMCE.prototype.triggerSave();\" onmouseover=\"img1.src='images/fiche_identite/boutvalidb$suffixer.gif';return true;\" onmouseout=\"img1.src='images/fiche_identite/boutvalid$suffixer.gif'\">".
        "<IMG NAME=\"img1\" SRC=\"images/fiche_identite/boutvalid$suffixer.gif\" BORDER='0' onLoad=\"tempImg=new Image(0,0); tempImg.src='images/fiche_identite/boutvalidb$suffixer.gif'\"></A>";
   }else
     echo boutret(1,0);
  echo "</TD></TR></form></TABLE></TD></TR></TABLE></TD></TR></tbody></TABLE>";
  exit;
} //fin if ($action_act == 1)
$ext='_entree';
include('include/prerequis.inc.php');
//include 'click_droit.txt';
  echo "<div id='mien' class='cms'></div>";
echo "</BODY></HTML>";
?>