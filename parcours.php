<?php
if (!isset($_SESSION)) session_start();
include ("include/UrlParam2PhpVar.inc.php");
if (!isset($_SESSION['id_user']) || $_SESSION['id_user'] == "")
{
  exit();
}
require 'admin.inc.php';
require 'fonction.inc.php';
require 'fonction_html.inc.php';
require "lang$lg.inc.php";
require "langues/xml.inc.php";
require "langues/module.inc.php";
require 'class/class_module.php';
dbConnect();

/*
echo "<pre>";
print_r($db_list);
echo "</pre>";
*/
include 'style.inc.php';

$etat_fav = GetDataField($connect,"select param_etat_lb from param_foad where param_typ_lb='favoris'","param_etat_lb");
if (strstr($_SERVER["HTTP_USER_AGENT"],"Firefox") && strstr($_SERVER["HTTP_USER_AGENT"],"Windows"))
  $alter = "";
if (isset($aut_ajx) && $aut_ajx > 0)
{
   $_SESSION['aut_ajx']=$aut_ajx;
}
$date_dujour = date ("Y-m-d");
$date_op = date("Y-m-d H:i:s" ,time());
$heure_fiche = substr($date_op,11);
$date_fiche = substr($date_op,0,10);
$html = "";
/*
      if (isEmpty(frm.description)==true)
        ErrMsg += ' - <?php echo $msq_desc;?>\n';
*/
?>

  <script type="text/javascript" src="OutilsJs/lib/ajax/ajax_cms.js"></script>
  <div id="affiche" class="Status"></div>
  <div id="mon_contenu" class="mon_contenu"
        onClick="javascript:$(document).ready(function() {if ($.browser.msie) {$('#mon_contenu').hide();}else{$('#mon_contenu').empty();}})"
        <?php echo "title=\"$mess_clkF\">";?>
  </div>

  <SCRIPT language=JavaScript>
    function checkForm(frm) {
      var ErrMsg = "<?php echo $mess_info_no;?>\n\n";
      var lenInit = ErrMsg.length;
      <?php if ($dupli_parc==1){?>
      if (isEmpty(frm.titre)==true)
        ErrMsg += ' - <?php echo $msq_titre;?>\n';
      <?php }else{?>
      if (isEmpty(frm.nom)==true)
        ErrMsg += ' - <?php echo $mess_parc_tit;?>\n';
      <?php }?>
      if (isEmpty(frm.description)==true)
        ErrMsg += ' - <?php echo $msq_desc;?>\n';
      <?php if(!$choix_seq){?>
      if (isVide(frm.type_parc)==true)
        ErrMsg += ' - <?php echo $mess_presc_ok;?>\n';
      if (isVide(frm.droit_voir)==true)
        ErrMsg += ' - <?php echo $msq_droit_voir;?>\n';
      <?php }?>
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
//Gestion de parcours
if (isset($creation) && $creation == 1)
{
    $lien = "referenciel.php";
    $lien = urlencode($lien);
    echo "<A href=\"trace.php?link=$lien\" target='main'><B>$msq_choix_ref</B></A>";
}
if (isset($dupli_parc) && $dupli_parc == 1 && (!isset($copier_parc) || (isset($copier_parc) && $copier_parc= '')))
{
    entete_concept("liste_parc.inc.php",$mess_dupli_mod);
    echo aide_simple("module");
    echo "<TR><TD colspan='2'>";
    $parc_query = requete("*","parcours","parcours_cdn = $id_parc");
    $item = mysql_fetch_object($parc_query);
    $nom = $item->parcours_nom_lb;
    $desc = html_entity_decode($item->parcours_desc_cmt,ENT_QUOTES,'iso-8859-1');
    $cles = html_entity_decode($item->parcours_mots_clef,ENT_QUOTES,'iso-8859-1');
    $droit_voir = $item->parcours_publique_on;
    $type_parc = $item->parcours_type_on;
    $mode_parc =$item-> parcours_type_lb;
    $id_ref_parc = $item->parcours_referentiel_no;
    echo "<FORM name='form1' ACTION=\"parcours.php?liste=$liste&utilisateur=$utilisateur&parcours=$parcours&id_parc=$id_parc&proprio=$proprio&refer=$refer&id_referentiel_parc=$id_ref_parc&dupli_parc=1&copier_parc=1&parc=$id_parc&toutes_seq=$toutes_seq&miens=$miens&miens_parc=$miens_parc\" METHOD='POST' target='main'>";
    echo "<INPUT TYPE='HIDDEN'  name='mode_parc' value=\"$mode_parc\" align='middle'>";
    echo "<INPUT TYPE='HIDDEN'  name='nom' value=\"".trim($nom)."\" align='middle'>";
    echo "<TABLE><TR><TD nowrap><B>$msq_titre</B></TD>";
    echo "<TD nowrap><INPUT TYPE='TEXT' class='INPUT'  name='titre' size='75' value=\"".trim($nom)."\" align='middle'></TD></TR>";
    echo "<TR><TD nowrap><B>$msq_desc_seq</B></TD>";
    echo "<TD nowrap><TEXTAREA class='TEXTAREA' name='description' align='middle' rows='6' cols='80'>".$desc."</TEXTAREA><br></TD></TR>";
    echo "<TR><TD width='33%'><B>Mots-clé</B></TD><TD align=left>";
    echo "<TEXTAREA class='TEXTAREA' name='cles' align='middle' rows='5' cols='80'>".$cles."</TEXTAREA><br></TD></TR>";
    echo "<TR><TD nowrap><B>$mess_extension</B></TD><TD nowrap><INPUT type='text' class='INPUT'  name='extension' size='20'> ";
    echo anoter($mess_extens_dupli,"350");
    echo "</TD></TR>";
    echo "<TR height='25'><TD nowrap>$mess_presc_ok</TD><TD nowrap>";
    if ($type_parc == 1) {
       echo "$mess_oui&nbsp;<INPUT type='radio' name='type_parc' value='0'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
       echo "$mess_non&nbsp;<INPUT type='radio' name='type_parc' value='1' checked>";
    }elseif ($type_parc == 0) {
       echo "$mess_oui&nbsp;<INPUT type='radio' name='type_parc' value='0' checked>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
       echo "$mess_non&nbsp;<INPUT type='radio' name='type_parc' value='1'>";
    }
    echo anoter($mess_dupli_presc,"350");
    echo "</TD></TR><TR height='25'><TD nowrap>$msq_droit_voir</TD><TD nowrap>";
    if ($droit_voir == 1) {
       echo "$mess_oui&nbsp;<INPUT type='radio' name='droit_voir' value='1' checked>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
       echo "$mess_non&nbsp;<INPUT type='radio' name='droit_voir' value='0'>";
    }elseif ($droit_voir == 0) {
       echo "$mess_oui&nbsp;<INPUT type='radio' name='droit_voir' value='1'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
       echo "$mess_non&nbsp;<INPUT type='radio' name='droit_voir' value='0' checked>";
    }
    echo "</TD></TR></TABLE></TD></TR><TR height='50' bgColor=#EFEFEF >";
    echo "<TD>&nbsp;</TD><TD align='center'><A HREF=\"javascript:checkForm(document.form1);\" onClick=\"TinyMCE.prototype.triggerSave();\" onmouseover=\"img1.src='images/fiche_identite/boutvalidb$suffixer.gif';return true;\" onmouseout=\"img1.src='images/fiche_identite/boutvalid$suffixer.gif'\">".
         "<IMG NAME=\"img1\" SRC=\"images/fiche_identite/boutvalid$suffixer.gif\" BORDER='0' onLoad=\"tempImg=new Image(0,0); tempImg.src='images/fiche_identite/boutvalidb$suffixer.gif'\"></A>";
    echo "</TD></TR></form></TABLE>";
    echo fin_tableau($html);
   exit;
}
if (isset($dupli_parc) && $dupli_parc == 1 && isset($_GET['copier_parc']) && $_GET['copier_parc'] == 1)
{
   $nom_user = $_SESSION['name_user'];
   $id_new_parc = Donne_ID ($connect,"SELECT max(parcours_cdn) from parcours");
   $description = DelAmp(htmlentities($description,ENT_QUOTES,'iso-8859-1'));
   $cles = DelAmp(htmlentities($cles,ENT_QUOTES,'iso-8859-1'));
    if ($titre == $nom)
    {
      if ($extension != "")
        $titre .=" $extension";
      else
        $titre .=" ($nom_user)";
    }
   $titre = trim(str_replace("\"","'",$titre));
   $seq_query = requete_order ("*","sequence,sequence_parcours","seqparc_parc_no = $id_parc AND ".
                               "seqparc_seq_no = seq_cdn AND ".
                               "(seq_publique_on=1 OR (seq_publique_on=0 AND seq_auteur_no=$id_user))","seqparc_ordre_no");
   $nb_seq = mysql_num_rows ($seq_query);
   if ($nb_seq == 0)
       echo "$msq_noseq_parc<br>";
   else
   {
    $insert_new_parc = mysql_query ("insert into parcours values ($id_new_parc,\"".$titre."\",\"$description\",\"$cles\",\"$id_referentiel_parc\",$id_user,\"$date_dujour\",\"$date_dujour\",$droit_voir,$type_parc,'$mode_parc')");
    while ($data_seq =  mysql_fetch_object($seq_query))
    {
     $id_seq = $data_seq->seq_cdn;
     $mode_seq =  $data_seq->seq_type_lb;
     if (strstr($mode_seq,"SCORM"))
        $act_query = requete_order("*","scorm_module","mod_seq_no = $id_seq","mod_cdn");
     else
        $act_query = requete_order("*","activite","act_seq_no = $id_seq AND ".
                               "(act_publique_on = 1 OR (act_publique_on=0 AND act_auteur_no=$id_user))"
                               ,"act_ordre_nb");
     $Nb_act_seq = mysql_num_rows ($act_query);
     $nom_seq =  str_replace("\"","'",$data_seq->seq_titre_lb);
     if ($extension != "")
       $nom_seq .=" $extension";
     else
       $nom_seq .=" ($nom_user)";
     $desc_seq =  str_replace("\"","'",DelAmp($data_seq->seq_desc_cmt));
     $cles_seq =  str_replace("\"","'",DelAmp($data_seq->seq_mots_clef));
     $ordre_act = $data_seq->seq_ordreact_on;
     $duree_seq =  $data_seq->seq_duree_nb;
     $type_seq =  $data_seq->seq_type_on;
     $droit_seq =  $data_seq->seq_publique_on;
     if ($Nb_act_seq == 0)
       echo "$msq_noact<br><br>";
     else
     {
          $id_new_seq = Donne_ID ($connect,"SELECT max(seq_cdn) from sequence");
          $insert_new_seq = mysql_query ("insert into sequence values ($id_new_seq,\"".$nom_seq."\",\"$desc_seq\",\"$cles_seq\",\"$ordre_act\",\"$duree_seq\",'$id_user',\"$date_dujour\",\"$date_dujour\",1,0,'$mode_seq')");
          $id_new_seqparc = Donne_ID ($connect,"SELECT max(seqparc_cdn) from sequence_parcours");
          $id_ordre = Donne_ID ($connect,"select seqparc_ordre_no from sequence_parcours where seqparc_seq_no=$id_seq and seqparc_parc_no=$id_parc");
          $insert_new_seqparc = mysql_query ("insert into sequence_parcours values ($id_new_seqparc,\"$id_new_seq\",\"$id_new_parc\",$id_ordre)");
          $id_ref_seq = GetDataField ($connect,"select seqref_referentiel_no from sequence_referentiel where seqref_seq_no = $id_seq","seqref_referentiel_no");
          $id_seqref = Donne_ID ($connect,"SELECT max(seqref_cdn) from sequence_referentiel");
          $id_ref_seq = GetDataField ($connect,"SELECT seqref_referentiel_no from sequence_referentiel where seqref_seq_no = '$id_seq'","seqref_referentiel_no");
          $insert_seqref = mysql_query ("insert into sequence_referentiel values ($id_seqref,$id_new_seq,$id_ref_seq)");

          if (strstr($mode_seq,"SCORM"))
          {
            while ($DataSco = mysql_fetch_object($act_query))
            {
            
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
                                                   ($id_new_mod,$id_parc,$id_new_seq,
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
             while ($data_act = mysql_fetch_object($act_query))
             {
                $nommer = str_replace("\"","'",$data_act->act_nom_lb);
                if ($extension != "")
                  $nommer .=" $extension";
                else
                  $nommer .=" ($nom_user)";
                $id_new_act = Donne_ID ($connect,"SELECT max(act_cdn) FROM activite");
                $insert_new_act = mysql_query ("INSERT INTO activite VALUES ($id_new_act,$id_new_seq,'".$data_act->act_ordre_nb."',\"".$nommer."\",".
                              "\"".str_replace("\"","'",DelAmp($data_act->act_consigne_cmt))."\",\"".
                              str_replace("\"","'",DelAmp($data_act->act_commentaire_cmt))."\",".
                              "'".$data_act->act_ress_on."',".$data_act->act_ress_no.",".$data_act->act_duree_nb.",".
                              "\"".$data_act->act_passagemult_on."\",\"".$data_act->act_acquittement_lb."\",\"".$data_act->act_notation_on."\",".
                              "\"".$data_act->act_devoirarendre_on."\",'$id_user',\"$date_dujour\",\"$date_dujour\",".
                              "'".$data_act->act_publique_on."',".$data_act->act_flag_on.")");
             }
        }//fin if (strstr($mod_seq,"SCORM")){
     }//fin de if ($Nb_act_seq == 0) {
    }// fin de while ($j != $nb_seq) {
   }// fin de if ($Nb_seq == 0) {
   $lien="parcours.php?liste=$liste&consult=1&parcours=1&id_parc=$id_new_parc&ok_dupli=1&ancien_parc=$id_parc&id_ref_parc=$id_referentiel_parc&miens=$miens&miens_parc=$miens_parc&parc=$id_new_parc&toutes_seq=$toutes_seq";
   $lien = urlencode($lien);
   echo "<script language='JavaScript'>";
   echo "document.location.replace(\"trace.php?link=$lien\")";
   echo "</script>";
   exit();
}
if (isset($prem) && $prem == 1)
{
  if (isset($liste) && $liste == 0)
  {
      entete_concept("liste_parc.inc.php",$mess_menu_gest_parc." : ".strtolower($mess_menu_gest_seq_ref));
    echo aide_simple("module");
    echo "<TR><TD align='center'>";
    $lien = "referenciel.php?parcours=1&choix_ref=1&miens=$miens&miens_parc=$miens_parc&refer=$refer";
    $lien = urlencode($lien);
    echo "<BR><DIV id='sequence'><a href=\"trace.php?link=$lien\" target='main'>$parc_chx_ref</a><P>";
    $lien = "parcours.php?liste=1&id_ref_parc=0&choix_ref=1&miens_parc=$miens_parc&miens=$miens";
    $lien = urlencode($lien);
    echo "<a href=\"trace.php?link=$lien\" target='main'>$parc_hrs_ref</a></DIV><P>";
    echo fin_tableau($html);
  }
  else
  {
//    if ($typ_user == 'ADMINISTRATEUR'){
     if (isset($mess_notif) && $mess_notif != '')
         echo notifier($mess_notif);
     if (isset($liste) && $liste == 1 && isset($medor) && $medor == 1)
     {
       if (isset($proprio) && $proprio > 0)
       {
         $nom_createur = GetDataField ($connect,"select util_nom_lb from utilisateur where util_cdn = $proprio","util_nom_lb");
         $prenom_createur = GetDataField ($connect,"select util_prenom_lb from utilisateur where util_cdn = $proprio","util_prenom_lb");
         $letitre = "$mess_mod_tous $mess_filtre $de $prenom_createur $nom_createur";
       }
       else
         $letitre = "$mess_menu_gest_parc : $mess_mod_tous $mess_filtre";
        if ($keydesc == "" && $keytitre == "" && $keypub == "" && $keypresc == "")
        {
           $ordre_affiche = "lenom";
           $refer = 2;
           $medor = 0;
        }
        if ($keydesc != "" && $keytitre == "" && $keypub == "")
           $champ_rech = "parcours.parcours_desc_cmt like '%$keydesc%'";
        elseif ($keydesc == "" && $keytitre != "" && $keypub == "")
           $champ_rech = "parcours.parcours_nom_lb like '%$keytitre%'";
        elseif ($keydesc == "" && $keytitre == "" && $keypub != "")
           $champ_rech = "parcours.parcours_publique_on = '$keypub'";
        elseif ($keydesc != "" && $keytitre != "" && $keypub == "")
           $champ_rech = "(parcours.parcours_nom_lb like '%$keytitre%' AND parcours.parcours_desc_cmt like '%$keydesc%'')";
        elseif ($keydesc == "" && $keytitre != "" && $keypub != "")
           $champ_rech = "(parcours.parcours_publique_on = '$keypub' AND parcours.parcours_nom_lb like '%$keytitre%')";
        elseif ($keydesc != "" && $keytitre == "" && $keypub != "")
           $champ_rech = "(parcours.parcours_publique_on = '$keypub' AND parcours.parcours_desc_cmt like '%$keydesc%')";
        elseif ($keydesc != "" && $keytitre != "" && $keypub != "")
           $champ_rech = "(parcours.parcours_publique_on = '$keypub' AND parcours.parcours_nom_lb like '%$keytitre%' AND parcours.parcours_desc_cmt like '%$keydesc%')";
       if (isset($type_on) && $type_on == 1)
           $champ_rech .= " parcours_type_on = 1 and parcours_publique_on = 1";
       if (isset($keypresc) && $keypresc == 1  && $keypresc != '' && $champ_rech == "")
           $champ_rech .= " parcours_type_on = 1";
        elseif (isset($keypresc) &&$keypresc == 1  && $keypresc != '' && $champ_rech != "")
           $champ_rech .= " AND parcours_type_on = 1";
        if (isset($keypresc) && $keypresc == 0  && $keypresc != '' && $champ_rech == "")
           $champ_rech .= " parcours_type_on = 0";
        elseif (isset($keypresc) && $keypresc == 0  && $keypresc != '' && $champ_rech != "")
           $champ_rech .= " AND parcours_type_on = 0";
        if ($ordre_affiche == "lenom" && $proprio > 0)
          $requete_parc = "select * from parcours where $champ_rech  AND parcours.parcours_cdn > 0 AND parcours.parcours_auteur_no = '$proprio' order by parcours.parcours_type_lb,parcours_nom_lb asc";
        if ($ordre_affiche == "lenom" && $proprio == "")
          $requete_parc = "select * from parcours where $champ_rech  AND parcours.parcours_cdn > 0 order by parcours.parcours_type_lb,parcours_nom_lb asc";
        elseif ($ordre_affiche == "lauteur" && $proprio > 0)
          $requete_parc = "select * from parcours,utilisateur where $champ_rech  AND parcours.parcours_cdn > 0 AND parcours.parcours_auteur_no = '$proprio' order by utilisateur.util_nom_lb asc,parcours.parcours_type_lb";
        elseif ($ordre_affiche == "lauteur" && $proprio == "")
          $requete_parc = "select * from parcours,utilisateur where $champ_rech  AND parcours.parcours_cdn > 0 AND parcours.parcours_auteur_no = utilisateur.util_cdn order by utilisateur.util_nom_lb asc,parcours.parcours_type_lb";
        elseif ($ordre_affiche == "leref" && $refer > 0 && $proprio > 0)
          $requete_parc = "select * from parcours,referentiel where $champ_rech  AND parcours.parcours_cdn > 0 AND parcours.parcours_referentiel_no = referentiel.ref_cdn AND parcours.parcours_referentiel_no > 0 AND parcours.parcours_auteur_no = '$proprio' order by referentiel.ref_nomabrege_lb,parcours.parcours_type_lb";
        elseif ($ordre_affiche == "leref" && $refer > 0 && $proprio == "")
          $requete_parc = "select * from parcours,referentiel where $champ_rech  AND parcours.parcours_cdn > 0 AND parcours.parcours_referentiel_no = referentiel.ref_cdn AND parcours.parcours_referentiel_no > 0 order by referentiel.ref_nomabrege_lb,parcours.parcours_type_lb";
        elseif ($ordre_affiche == "leref" && $refer == 0 && $proprio > 0)
          $requete_parc = "select * from parcours where $champ_rech  AND parcours.parcours_referentiel_no = 0 AND parcours.parcours_auteur_no = '$proprio' order by parcours.parcours_type_lb,parcours_nom_lb asc";
        elseif ($ordre_affiche == "leref" && $refer == 0 && $proprio == "")
          $requete_parc = "select * from parcours where $champ_rech AND parcours.parcours_referentiel_no = 0 order by parcours.parcours_type_lb,parcours_nom_lb asc";
     }
      if (isset($liste) && $liste == 1 && isset($proprio) && $proprio > 0 && ((isset($medor) && $medor != 1) || !isset($medor)))
      {
        $nom_createur = GetDataField ($connect,"select util_nom_lb from utilisateur where util_cdn = $proprio","util_nom_lb");
        $prenom_createur = GetDataField ($connect,"select util_prenom_lb from utilisateur where util_cdn = $proprio","util_prenom_lb");
        $letitre = "$mess_mod_tous $de $prenom_createur $nom_createur";
        if ($ordre_affiche == "lenom")
          $requete_parc = "select * from parcours where parcours_auteur_no = '$proprio'  order by parcours.parcours_type_lb,parcours_nom_lb asc";
        elseif ($ordre_affiche == "leref" && $refer > 0)
          $requete_parc = "select * from parcours,referentiel where parcours.parcours_auteur_no = '$proprio'  AND parcours.parcours_referentiel_no > 0 AND parcours.parcours_referentiel_no = referentiel.ref_cdn order by referentiel.ref_nomabrege_lb,parcours.parcours_type_lb";
        elseif ($ordre_affiche == "leref" && $refer == 0)
          $requete_parc = "select * from parcours where parcours_auteur_no = '$proprio' AND parcours.parcours_referentiel_no > 0 order by parcours.parcours_type_lb, parcours.parcours_nom_lb";
      }
      if (isset($liste) && $liste == 1 && $refer == 0 && $proprio == "" && $medor != 1)
      {
        $letitre = "$mess_mod_lib";
        if ($ordre_affiche == "lenom")
          $requete_parc = "SELECT * from parcours where parcours_referentiel_no=0  AND parcours_cdn > 0 order by parcours.parcours_type_lb,parcours_nom_lb asc";
        elseif ($ordre_affiche == "lauteur")
          $requete_parc = "SELECT * from parcours,utilisateur where parcours.parcours_referentiel_no=0  AND parcours.parcours_cdn > 0 AND parcours.parcours_auteur_no = utilisateur.util_cdn order by utilisateur.util_nom_lb asc,parcours.parcours_type_lb";
      }
      if ($liste == 1 && $miens_parc == 1 && $proprio == "" && $medor != 1)
      {
        $letitre = $mess_menu_mon_parc;
        if ($ordre_affiche == "lenom")
          $requete_parc = "SELECT * from parcours where parcours_cdn > 0  AND parcours_auteur_no = $id_user order by parcours.parcours_type_lb,parcours_nom_lb asc";
      }
      if ($liste == 1 && $refer == 1 && $proprio == "" && $medor != 1)
      {
        $letitre = "$mess_mod_ref";
        if ($ordre_affiche == "lenom")
          $requete_parc = "SELECT * from parcours where parcours_referentiel_no > 0  order by parcours.parcours_type_lb,parcours_nom_lb asc";
        elseif ($ordre_affiche == "lauteur")
          $requete_parc = "SELECT * from parcours,referentiel,utilisateur where parcours.parcours_referentiel_no > 0  AND parcours.parcours_referentiel_no = referentiel.ref_cdn AND parcours.parcours_auteur_no = utilisateur.util_cdn order by utilisateur.util_nom_lb asc,parcours.parcours_type_lb";
        elseif ($ordre_affiche == "leref")
          $requete_parc = "SELECT * from parcours,referentiel where parcours_referentiel_no > 0  AND parcours.parcours_referentiel_no = referentiel.ref_cdn order by referentiel.ref_nomabrege_lb asc,parcours.parcours_type_lb";
      }
      if ($liste == 1 && $refer == 2 && $proprio == "" && $medor != 1)
      {
        $letitre = "$mess_mod_tous";
        if ($ordre_affiche == "lenom"){
           if ($type_on == 1)
              $requete_parc = "select * from parcours where parcours_cdn > 0 and parcours_type_on = 1 and parcours_publique_on = 1 order by parcours_nom_lb,parcours_auteur_no";
           else
              $requete_parc = "SELECT * from parcours where parcours_cdn > 0  order by parcours.parcours_type_lb,parcours_nom_lb asc";
        }elseif ($ordre_affiche == "lauteur")
          $requete_parc = "SELECT * from parcours,utilisateur where parcours.parcours_cdn > 0  AND parcours.parcours_auteur_no = utilisateur.util_cdn order by utilisateur.util_nom_lb asc,parcours.parcours_type_lb";
      }
      if ($titre != '' && $letitre == $mess_mod_tous)
         $letitre = $titre;
      if ($liste == 1 && $star == 1)
      {
        $letitre = "Mes favoris";
        if ($ordre_affiche == "lenom")
          $requete_parc = "SELECT * from parcours,stars where stars.star_item_id=parcours.parcours_cdn and stars.star_user_id= $id_user and stars.star_type_no=1 order by parcours_nom_lb, parcours.parcours_type_lb asc";
        elseif ($ordre_affiche == "lauteur")
          $requete_parc = "SELECT * from parcours,utilisateur,stars where parcours.parcours_auteur_no = utilisateur.util_cdn and stars.star_item_id=parcours.parcours_cdn and stars.star_user_id='$id_user' and stars.star_type_no=1 order by utilisateur.util_nom_lb asc,parcours.parcours_nom_lb";
      }
      if (!isset($_SESSION['nbr_pgs_mod']))
          $nbr_pgs = GetDataField($connect,"select param_etat_lb from param_foad where param_typ_lb='nb_pg_mod'","param_etat_lb");
      else
          $nbr_pgs = $_SESSION['nbr_pgs_mod'];
    $parc_query = mysql_query($requete_parc);
    $nb_parc = mysql_num_rows($parc_query);
    if ($nb_parc == 0)
    {
         entete_concept("liste_parc.inc.php",$letitre);
         echo aide_simple("module");
         echo "<TR><TD colspan='2'><Font color='red'><B> $mpa_noparc </B><Font></TD></TR> ";
         echo fin_tableau($html);
         exit();
    }
    $affiche_parc = $nb_parc;
    if ($nb_parc > $nbr_pgs)
    {
          $nb_pages = ceil($nb_parc/$nbr_pgs);
          $debut_liste = $nbr_pgs*$page;
          $le_debut = $debut_liste+1;
          if ($page > 0)
             $page_ret = $page-1;
          $page++;
          if ($debut_liste > $nb_parc)
          {
             $debut_liste = 0;
             $le_debut = 1;
             $page = 0;
          }
          if ($debut_liste == 0)
             $le_debut = 1;
          $requete_parc .=" limit $debut_liste,$nbr_pgs";
    }
    $_SESSION['requete_parc']=$requete_parc;
    entete_concept("liste_parc.inc.php",$letitre);
    echo aide_simple("module");
       //---------------------------------------------------------------------------------
       if ($medor == 1)
       {
         echo "<TR><TD colspan='2'><B>$mess_filtapp </B>  $msq_titre ";
         if ($keytitre != "") echo "<font color='#D45211'><B>$keytitre</B></font>, ";else echo "<B>$mess_nofiltre</B>, ";
         echo "&nbsp;&nbsp;&nbsp;$msq_desc ";
         if ($keydesc != "") echo "<font color='#D45211'><B>$keydesc</B></font>, ";else echo "<B>$mess_nofiltre</B>, ";
         echo "&nbsp;&nbsp;&nbsp;$mess_visdup : ";
         if ($keypub == 1) echo "<font color='#D45211'><B>$mess_oui</B></font>";elseif($keypub == 0 && $keypub != "") echo "<font color='#D45211'><B>$mess_non</B></font>";else echo "<B>$mess_nofiltre</B>";
         echo "&nbsp;&nbsp;&nbsp;$mess_presc : ";
         if ($keypresc == 1) echo "<font color='#D45211'><B>$mess_non</B></font>";elseif($keypresc == 0 && $keypresc != "") echo "<font color='#D45211'><B>$mess_oui</B></font>";else echo "<B>$mess_nofiltre</B>";
         echo "</TD></TR>";
       }
       echo "<TR><TD colspan='2'><table cellspacing='6' width='100%'>";
          $affiche_rech = "";
          $affiche_rech = "<TD align='left'><form name='form'>";
          $affiche_rech .= "<SELECT name='select' class='SELECT' onChange=javascript:appel_w(form.select.options[selectedIndex].value)>";
          $affiche_rech .= "<OPTION selected></OPTION>";
          $req_aut_mod = requete_order("DISTINCT parcours_auteur_no,util_nom_lb","parcours,utilisateur","parcours.parcours_auteur_no = utilisateur.util_cdn","utilisateur.util_nom_lb");
          $nb_aut_mod = mysql_num_rows($req_aut_mod);
          while ($data = mysql_fetch_object($req_aut_mod)){
             $nom_aut = $data -> util_nom_lb;
             $num_aut = $data -> parcours_auteur_no;
             $miens = ($num_aut == $id_user) ? $miens : "";
             $miens_parcours = ($num_aut == $id_user) ? $miens_parcours : "";
             $lien = "parcours.php?prem=1&liste=1&refer=$refer&ordre_affiche=lenom&proprio=$num_aut&medor=1&miens=$miens&miens_parc=$miens_parcours";
             $lien =  urlencode($lien);
             $affiche_rech .= "<OPTION value='trace.php?link=$lien'>$nom_aut</OPTION>";
          }
          $affiche_rech .= "</SELECT></TD>";
       echo "<FORM name='form1' ACTION=\"parcours.php?prem=1&liste=1&ordre_affiche=lenom&medor=1&vient_de_search=1&refer=$refer&miens_parc=$miens_parc&miens=$miens&proprio=$proprio\" METHOD='POST' target='main'>";
       echo "<TR bgcolor= '#F4F4F4'><TD nowrap colspan='6'><B>$mrc_rech $mess_ParAutSuit</B></TD></TR>";
       echo "<TR>";
       if ($nb_aut_mod > 1)
          echo "<TD nowrap>$mrc_aut</TD>";
       echo "<TD nowrap>$msq_titre</TD><TD nowrap>$msq_desc</TD><TD nowrap>$mess_visdup</TD><TD nowrap>$mess_presc</TD></TR>";
       echo "<TR>";
       if ($nb_aut_mod > 1)
         echo $affiche_rech;
       echo "<TD nowrap><INPUT TYPE='text'  class='INPUT'   name='keytitre' size='20' align='middle'></TD>";
       echo "<TD nowrap><INPUT TYPE='text'  class='INPUT'   name='keydesc' size='20' align='middle'></TD>";
         echo "<TD nowrap><SELECT name='keypub' class='SELECT'>";
         echo "<OPTION></OPTION>";
         echo "<OPTION value='1'>$mess_oui</OPTION>";
         echo "<OPTION value='0'>$mess_non</OPTION>";
         echo "</SELECT></TD>";
         echo "<TD nowrap><SELECT name='keypresc' class='SELECT'>";
         echo "<OPTION></OPTION>";
         echo "<OPTION value='0'>$mess_oui</OPTION>";
         echo "<OPTION value='1'>$mess_non</OPTION>";
         echo "</SELECT></TD>";
       echo "<TD align='center'><A HREF=\"javascript:document.form1.submit();\" onmouseover=\"img1.src='images/fiche_identite/boutvalidb$suffixer.gif';return true;\" onmouseout=\"img1.src='images/fiche_identite/boutvalid$suffixer.gif'\">".
       "<IMG NAME=\"img1\" SRC=\"images/fiche_identite/boutvalid$suffixer.gif\" BORDER='0' onLoad=\"tempImg=new Image(0,0); tempImg.src='images/fiche_identite/boutvalidb$suffixer.gif'\"></A>";
       echo "</TD></TR></form></TABLE>";
       //--------------------------------------------------------------------------
    if ($nb_parc == 0)
    {
       echo "<TR ><TD align='center' valign='center'>&nbsp;<P><B>$mpa_noparc</B><P></TD></TR>";
      echo "</TABLE></TD></TR></TABLE>";
    }
    else
    {
       $lecho = (($debut_liste + $nbr_pgs) < $nb_parc) ? strval($le_debut)." - ".strval($debut_liste+$nbr_pgs) : strval($le_debut)." - $nb_parc";
       $parc_query = mysql_query($requete_parc);
       $nb_parc = mysql_num_rows ($parc_query);
       echo "<TR><TD colspan='2'>";
       $bgcolor2 = '#2b677a';
       $bgcolor1 = '#F8F2E4';
       echo "<TR><TD width='100%'><table cellpadding='6' width='100%'>";
       echo "<tr bgcolor=$bgcolor2>";
       $affiche_nb = ($affiche_parc > $nbr_pgs) ? "[$lecho] sur $affiche_parc" : "$affiche_parc";
       $choixNbAff = "<div id='cNA' style=\"float:left;padding-right:4px;cursor:pointer;\">".
                     "<input type='text' class='INPUT' id='NBS' name='nbr_pgs_mod' value='$nbr_pgs' size='2' maxlength='2' style='width:20px;' ".
                     bullet($msg_modNbPg,"","RIGHT","BELOW",180).
                     "onBlur=\"javascript:var nbs=getElementById(NBS);".
                     "appelle_ajax('admin/modif_nb.php?nbrAff_mod='+NBS.value);\$(mien).empty();\" /></div>";
       //star
       if ($etat_fav == 'OUI')
       {
           $lien_star_search="parcours.php?star=1&prem=1&liste=$liste&ordre_affiche=$ordre_affiche&medor=$medor&type_lb=$type_lb&type_on=$type_on&keytitre=$keytitre&keypub=$keypub&keydesc=$keydesc&keypresc=$keypresc&proprio=$proprio&vient_de_search=$vient_de_search&miens_parc=$miens_parc&miens=$miens&refer=$refer&parc=$id_parc";
           $nb_star = mysql_num_rows(mysql_query("select * from stars where star_user_id='$id_user' and star_type_no=1"));
           if ($nb_star > 0)
           {
               $ajt_star = "<div id='starfull' style=\"float:left;padding-right:4px;cursor:pointer;\" " .
                           bullet($mess_menu_consult_fav_prop,"","RIGHT","BELOW",120).
                           " onclick=\"javascript:parent.main.location.replace('trace.php?link=".urlencode($lien_star_search)."');\">".
                           "<img src='images/starfull.gif' border=0></div>";
           }else
               $ajt_star = "<div id='starfull' style=\"float:left;padding-right:4px;cursor:pointer;\"></div>";
       }
        else
            $ajt_star = "";
       echo "<td  height='20' align='left' nowrap style=\"padding-bottom:4px;\" nowrap>$ajt_star<div id='mpwo' style=\"float:left;padding-right:4px;padding-top:3px;\"><FONT COLOR=white><b>$msq_parc&nbsp;&nbsp;($affiche_nb)</div>";
       if ($page > 1)
              echo " <div id='flcG' style=\"float:left;padding-right:4px;padding-top:5px;cursor:pointer;\">".
                   "<A HREF=\"parcours.php?prem=$prem&liste=$liste&refer=$refer&ordre_affiche=$ordre_affiche&medor=$medor&type_lb=$type_lb&type_on=$type_on&keytitre=$keytitre&keypub=$keypub&keydesc=$keydesc&keypresc=$keypresc&proprio=$proprio&vient_de_search=$vient_de_search&miens_parc=$miens_parc&page=$page_ret&debut_liste=$debut_liste&star=$star\">".
                   "<img src='images/ecran-annonce/icogog.gif' border='0' title='$mess_page_prec'></A></div>";
       echo $choixNbAff;
       if ($nb_parc == $nbr_pgs && $affiche_parc > $debut_liste+$nbr_pgs)
              echo "<div id='flcD' style=\"float:left;padding-right:4px;padding-top:5px;cursor:pointer;\">".
                   "<A HREF=\"parcours.php?prem=$prem&liste=$liste&refer=$refer&ordre_affiche=$ordre_affiche&medor=$medor&type_lb=$type_lb&type_on=$type_on&keytitre=$keytitre&keypub=$keypub&keydesc=$keydesc&keypresc=$keypresc&proprio=$proprio&vient_de_search=$vient_de_search&miens_parc=$miens_parc&page=$page&debut_liste=$debut_liste&star=$star\">".
                   "<img src='images/ecran-annonce/icogod.gif' border='0' title='$mess_page_suiv'></A></div>";
       $affiche_bulle = " onMouseOver=\"overlib('<TABLE><TR><TD width=5></TD><TD>".addslashes($mess_ord_alpha)."</TD></TR></TABLE>',".
                       "ol_hpos,LEFT,WIDTH,'150',DELAY,500,CAPTION,'')\" onMouseOut=\"nd()\"";
       if ($miens_parc != 1 && $ordre_affiche != "lenom"){
          if ($proprio > 0)
             echo "&nbsp;<A HREF=\"parcours.php?prem=1&liste=1&ordre_affiche=lenom&proprio=$proprio&vient_de_search=$vient_de_search&miens_parc=$miens_parc&refer=$refer\" ".
                       " onMouseOver=\"overlib('<TABLE><TR><TD width=5></TD><TD>".addslashes($mess_ord_alpha)."</TD></TR></TABLE>',".
                       "ol_hpos,LEFT,WIDTH,'150',DELAY,500,CAPTION,'')\" onMouseOut=\"nd()\"><img src='images/fleche1.gif' border=0></A>";
          elseif ($medor == 1)
            echo "&nbsp;<A HREF=\"parcours.php?prem=1&liste=$liste&refer=2&ordre_affiche=lenom&medor=1&type_lb=$type_lb&type_on=$type_on&keytitre=$keytitre&keypub=$keypub&keydesc=$keydesc&keypresc=$keypresc&proprio=$proprio&vient_de_search=$vient_de_search&miens_parc=$miens_parc\" ".
                 "$affiche_bulle><img src='images/fleche1.gif' border=0></A>";
          else
             echo "&nbsp;<A HREF=\"parcours.php?prem=1&liste=$liste&refer=$refer&ordre_affiche=lenom\" ".
                  "$affiche_bulle><img src='images/fleche1.gif' border=0></A>";
       }
       echo "</b></FONT></DIV></td>";
       if ($miens_parc != 1 && $proprio == "")
       {
          echo "<td  height='20' align='left'><FONT COLOR=white><b>$mrc_aut</b></FONT>";
          if ($ordre_affiche != "lauteur")
             echo "&nbsp;<A HREF=\"parcours.php?prem=1&liste=$liste&ordre_affiche=lauteur&medor=$medor&type_lb=$type_lb&type_on=$type_on&keytitre=$keytitre&keypub=$keypub&keydesc=$keydesc&keypresc=$keypresc&proprio=$proprio&vient_de_search=$vient_de_search&miens_parc=$miens_parc&miens=$miens&refer=$refer&parc=$id_parc\" ".
                  "$affiche_bulle><img src='images/fleche1.gif' border=0></A>";
       }
       echo "</td>";
        if ($miens_parc == 1 || $proprio > 0 || $medor == 1 || ($refer < 3 && $refer != 0)){
          echo "<td  height='20' align='left'><FONT COLOR=white><b>$msq_ref_ass</b></FONT>";
          if ($refer < 2 && $miens_parc != 1){
             if ($miens_parc != 1){
               if ($ordre_affiche != "leref" && $miens_parc != 1 && $medor != 1)
                if ($proprio > 0)
                  echo "&nbsp;<A HREF=\"parcours.php?prem=1&liste=1&refer=$refer&ordre_affiche=leref&proprio=$proprio&miens=$miens&miens_parc=$miens_parc\" ".
                       "$affiche_bulle><IMG SRC='images/fleche1.gif' border=0></A>";
                else
                  echo "&nbsp;<A HREF=\"parcours.php?prem=1&liste=$liste&refer=$refer&ordre_affiche=leref&proprio=$proprio\" ".
                       "$affiche_bulle><IMG SRC='images/fleche1.gif' border=0></A>";
             }
          }
          echo "</td>";
       }
       echo "<TD align='left'><FONT COLOR=white><b>$mess_modif_base</b></FONT></TD>";
       echo "<TD align='left'><FONT COLOR=white><b>$mess_ag_supp</b></FONT></TD>";
       echo "<TD align='left'><FONT COLOR=white><b>$mess_dupliquer</b></FONT></TD>";
       echo "</tr>";
       $i=0;
       while ($i < $nb_parc)
       {
              $id_parc = mysql_result ($parc_query,$i,"parcours_cdn");
              $nom = mysql_result ($parc_query,$i,"parcours_nom_lb");
              $parc_type = mysql_result ($parc_query,$i,"parcours_type_on");
              $mod_parc = mysql_result ($parc_query,$i,"parcours_type_lb");
              $droit_voir_parc = mysql_result ($parc_query,$i,"parcours_publique_on");
              $createur = mysql_result ($parc_query,$i,"parcours_auteur_no");
              $description = mysql_result ($parc_query,$i,"parcours_desc_cmt");
              $id_ref_parc = mysql_result ($parc_query,$i,"parcours_referentiel_no");
              $id_seq_query = mysql_query ("SELECT seqparc_seq_no from sequence_parcours where seqparc_parc_no = $id_parc");
              $nb_seq2 = mysql_num_rows ($id_seq_query);
              $duree_parc = 0;
              $j=0;
              $nb_sequence_type = 0;
              $nb_seq_type = 0;
              while ($j < $nb_seq2)
              {
                $id_seq = mysql_result ($id_seq_query,$j,"seqparc_seq_no");
                $seq_type = GetDataField ($connect,"SELECT seq_type_on from sequence where seq_cdn = $id_seq","seq_type_on");
                $sequence_type = GetDataField ($connect,"SELECT seq_type_lb from sequence where seq_cdn = $id_seq","seq_type_lb");
                $seq_droit = GetDataField ($connect,"SELECT seq_publique_on from sequence where seq_cdn = $id_seq","seq_publique_on");
                if ($seq_type == 1 && $seq_droit == 1)
                   $nb_seq_type++;
                if (strstr($sequence_type,'SCORM'))
                   $nb_sequence_type++;
                $duree_seq = GetDataField ($connect,"SELECT seq_duree_nb from sequence where seq_cdn = $id_seq","seq_duree_nb");
                $duree_parc+=$duree_seq;
                $j++;
              } //fin while ($j != $nb_seq)
              if ($id_ref_parc == 0)
                $nom_referentiel = $msq_pl;
              else
                $nom_referentiel = GetDataField ($connect,"SELECT ref_nomabrege_lb from referentiel where ref_cdn = $id_ref_parc","ref_nomabrege_lb");
              $nom_createur = GetDataField ($connect,"SELECT util_nom_lb from utilisateur where util_cdn = $createur","util_nom_lb");
              $prenom_createur = GetDataField ($connect,"SELECT util_prenom_lb from utilisateur where util_cdn = $createur","util_prenom_lb");
              $affiche_liste="";
              //star
              //$lien_star = "formation/star.php?numero=$numero&dl=dejalu$i&renonlu=1&ret=$ret&complement=$complement&i=$i";
              if ($etat_fav == 'OUI' && $parc_type == 0)
              {
                $nbr_startot = mysql_num_rows(mysql_query("select * from stars where star_user_id='$id_user' and star_type_no=1"));
                $nbr_star = mysql_num_rows(mysql_query("select * from stars where star_user_id='$id_user' and star_item_id='$id_parc' and star_type_no=1"));
                if ($nbr_star > 0)
                {
                    $lien_star = "formation/star.php?numero=$id_parc&dl=star$i&vider=1&i=$i&type=1";
                    $div_star = "<div id='star$i' class='star' ".
                              bullet($msg_fav_ot,"","RIGHT","BELOW",120)."><div id='lance' ".
                              "onclick=\"javascript:appelle_ajax('$lien_star'); ".
                              "\$('#mien').hide();\$('#star$i').html('');addContent_star('$lien_star');\"> ".
                              "<img src='images/starfull.gif' border=0></div></div>";
                 }
                 else
                 {
                    $lien_star = "formation/star.php?numero=$id_parc&dl=star$i&remplir=1&i=$i&type=1";
                    $div_star = "<div id='star$i' class='star' ".
                              bullet($mess_menu_ajout_favori,"","RIGHT","BELOW",130)."><div id='lance' ".
                              "onclick=\"javascript:appelle_ajax('$lien_star'); ".
                              "\$('#mien').hide();\$('#star$i').html('');addContent_star('$lien_star');\"> ".
                              "<img src='images/starempty.gif' border=0></div></div>";
                  }
              }
              else
              {
                 $div_star = nbsp(1).image_decale("<img src='archives/spacer.gif' border='0'>",2);
              }
              $requete_grp = mysql_query ("select * from groupe order by grp_nom_lb");
              $nb_grp_parc = mysql_num_rows($requete_grp);
              if ($nb_grp_parc > 0)
              {
                $gp=0;
                $nbprsc=0;
                while ($gp < $nb_grp_parc)
                {
                  $id_grp = mysql_result($requete_grp,$gp,"grp_cdn");
                  $nom_grp = mysql_result($requete_grp,$gp,"grp_nom_lb");
                  $nb_presc = mysql_num_rows(mysql_query("select * from prescription_$id_grp where presc_parc_no=$id_parc"));
                  if ($nb_presc > 0 && $nbprsc == 0)
                       $affiche_liste="<B>$mess_form_parcactif</B>";
                  $nbprsc += $nb_presc;
                  if ($nb_presc > 0)
                     $affiche_liste .="<LI>$nom_grp</LI>";
                $gp++;
                }
              }
              $seq_parc_query = mysql_query ("select count(*) from sequence_parcours where sequence_parcours.seqparc_parc_no = $id_parc");
              $nb_seqparc = mysql_result($seq_parc_query,0);
              if ($nb_seqparc > 0)
                 $affiche_liste .="<B>$mpa_voir_parc</B><BR>$mess_nbseq : $nb_seqparc";
              $seq_parc_duree = mysql_query ("select sum(activite.act_duree_nb) from sequence_parcours,activite where activite.act_seq_no = sequence_parcours.seqparc_seq_no AND sequence_parcours.seqparc_parc_no = $id_parc");
              $duree_parc = mysql_result($seq_parc_duree,0);
              $heure = floor($duree_parc/60);
              if ($heure > 0)
                 $reste_parc = $duree_parc%60;
              else
                 $reste_parc = $duree_parc;
              if ($reste_parc == 0)
                 $duree_parc1 = $heure.$h;
              else
                 $duree_parc1 = $heure.$h.$reste_parc;
              if ($duree_parc > 0)
                 $affiche_liste .="<BR>$msq_duree_seq : $duree_parc1";
              $lien = "parcours.php?liste=$liste&consult=1&parcours=$parcours&id_parc=$id_parc&proprio=$proprio&refer=$refer&miens_parc=$miens_parc&refer=$refer&parc=$id_parc&id_ref_parc=$id_ref_parc";
              $lien = urlencode($lien);
              echo couleur_tr($i+1,'');
              echo "<TD valign='top' style='text-align:left;'>$div $div_star<a href=\"trace.php?link=$lien\" target='main'";
              if ($affiche_liste !="")
                  echo bullet($affiche_liste,"","RIGHT","ABOVE",232);
              echo ">$nom</A></DIV></TD>";
              if ($miens_parc != 1 && $proprio == "")
              {
                  $lien="parcours.php?utilisateur=$utilisateur&liste=1&proprio=$createur&prem=1&ordre_affiche=lenom";
                  $lien = urlencode($lien);
                  $affiche_auteur = "$msq_consult_mod $de $prenom_createur $nom_createur";
                  echo "<TD nowrap valign='top' style='text-align:left;'><DIV id='sequence'><A HREF=\"trace.php?link=$lien\" ".
                       bulle($affiche_auteur,"","LEFT","ABOVE",150).
                       "$prenom_createur $nom_createur</A></DIV></TD>";
              }
              if ($refer > 0  || $miens_parc == 1 || $proprio > 0 || $medor == 1)
                echo "<TD valign='top' style='text-align:left;'>".html_entity_decode($nom_referentiel,ENT_QUOTES,'ISO-8859-1')."</TD>";
              if ($createur == $id_user || $typ_user == 'ADMINISTRATEUR')
              {
                   $lien = "parcours.php?liste=$liste&action_parc=1&parcours=$parcours&id_ref_parc=$id_ref_parc&droit_voir=$droit_voir_parc&nb_seq=$nb_seq&duree_parc=$duree_parc&id_ref=$id_ref&id_ref_parc=$id_ref_parc&miens_parc=$miens_parc&miens=$miens&refer=$refer&parc=$id_parc&id_parc=$id_parc&proprio=$proprio&refer=$refer";
                   $lien = urlencode($lien);
                   echo "<td align=center valign='top'><a href=\"trace.php?link=$lien\" target='main' ".
                        bulle($mpa_mod_parc,"","LEFT","ABOVE",150).
                        "<IMG SRC=\"images/repertoire/icoGrenomfich20.gif\" BORDER=0></A></td>";
                   if ($nbprsc == 0)
                   {
                      $lien = "parcours.php?liste=1&consult=1&supp=1&parcours=$parcours&id_parc=$id_parc&proprio=$proprio&refer=$refer&id_ref_parc=$id_ref_parc&ordre_affiche=lenom&miens=$miens&miens_parc=$miens_parc&refer=$refer&parc=$id_parc";
                      $lien = urlencode($lien);
                      echo "<td align=center valign='top'><a href=\"javascript:void(0);\" ".
                           "onclick=\"javascript:return(confm('trace.php?link=$lien'));\" target='main' ".
                           bulle($mpa_sup_parc,"","LEFT","ABOVE",150).
                           "<IMG SRC=\"images/messagerie/icopoubelressour.gif\" BORDER=0></A></td>";
                   }
                   else
                      echo "<td align=center valign='top'><a href=\"javascript:void(0);\" style='cursor:help;' ".
                           bulle($mpa_nosup_parc,"","LEFT","ABOVE",150).
                           "<IMG SRC=\"images/repertoire/icoptiinterdit.gif\" BORDER=0></A></td>";
              }
              else
                 echo "<TD>&nbsp;</TD><TD>&nbsp;</TD>";
              if ($droit_voir_parc == 1){// Droit de voir et dupliquer le parcours
                  $id_ref_parc = GetDataField ($connect,"SELECT parcours_referentiel_no from parcours where parcours_cdn = '$id_parc'","parcours_referentiel_no");
                  $lien="parcours.php?liste=$liste&dupli_parc=1&parcours=$parcours&id_parc=$id_parc&proprio=$proprio&refer=$refer&id_ref=$id_ref&id_ref_parc=$id_ref_parc&parc=$id_parc&miens=$miens&miens_parc=$miens_parc";
                  $lien = urlencode($lien);
                  echo "<TD nowrap align=center valign='top'><A href=\"trace.php?link=$lien\" target='main' ".
                       bulle($msq_dupli_parc,"","LEFT","ABOVE",150).
                       "<IMG SRC=\"images/repertoire/icoptiedit.gif\" BORDER=0></A></TD>";

              }else
                 echo "<TD>&nbsp;</TD>";
              echo "</TR>";
       $i++;
       }
       echo "</TABLE></TD></TR>";
       echo "</TABLE></TD></TR></TABLE>";
    }
  }
}
//insertion du parcours
if ($insert == 1)
{
    require_once ('class/Class_Rss.php');
    $id_parc = Donne_ID ($connect,"SELECT max(parcours_cdn) from parcours");
    if ($type_parc == 1)
    {
      $parc_type = 1;
    }
    else
      $parc_type = 0;
    $ins_parc = mysql_query ("INSERT INTO parcours VALUES ($id_parc,\"".NewHtmlentities($nom,ENT_QUOTES)."\",\"".htmlentities($description,ENT_QUOTES,'iso-8859-1')."\",\"".htmlentities($cles_parc,ENT_QUOTES,'iso-8859-1').
                             "\",0,$id_user,\"$date_dujour\",\"$date_dujour\",$droit_voir,$parc_type,'NORMAL')");
    $insert_rss = rss :: ajout('module',$id_user,$id_parc);
}
if ($seq > 0)
  $insert_seq = 1;
elseif ($seq == -1)
{
  $message = $devoir_choix;
  $lien = "parcours.php?liste=$liste&choix_seq=1&parcours=$parcours&id_parc=$id_parc&proprio=$proprio&refer=$refer&voir_seqs=1&choix_ref=1&id_ref_parc=$id_ref_parc&mess_notif=$message&droit_voir=$droit_voir";
  $lien = urlencode($lien);
  echo "<script language=\"JavaScript\">";
  echo " document.location.replace(\"trace.php?link=$lien\")";
  echo "</script>";
  exit();
}
elseif ($premier == 1)
{
  $lien = "parcours.php?liste=$liste&consult=1&parcours=$parcours&id_ref_parc=$id_ref_parc&droit_voir=$droit_voir&nb_seq=$nb_seq&duree_parc=$duree_parc&id_ref=$id_ref&id_ref_parc=$id_ref_parc&miens_parc=$miens_parc&miens=$miens&refer=$refer&parc=$id_parc&id_parc=$id_parc&proprio=$proprio";
  $lien = urlencode($lien);
  echo "<script language=\"JavaScript\">";
  echo " document.location.replace(\"trace.php?link=$lien\")";
  echo "</script>";
  exit();
}
//Insertion des sequences dans le parcours
if ($insert_seq == 1)
   $seq_insert_mod = add_seq_user($seq);
//CMS modules
if ($choix_ref == 1)
{
    if ($supp == 1)
        $del_seq = mysql_query ("DELETE from sequence_parcours where seqparc_parc_no = $id_parc AND seqparc_seq_no = $id_seq");
    if ($choix_seq == 1 || $insert_seq == 1)
    {
       $id_ref_parc = GetDataField ($connect,"select parcours_referentiel_no from parcours where parcours_cdn = $id_parc","parcours_referentiel_no");
       $id_ref_p = $id_ref_parc;
       if ($id_ref_p == 0)
         $referentiel = strtolower($msq_sra);
       else
         $referentiel = GetDataField ($connect,"SELECT ref_nomabrege_lb from referentiel where ref_cdn = $id_ref_parc","ref_nomabrege_lb");
    }
    else
      $referentiel = strtolower($msq_sra);
    $message1 = "<B>$msq_ref_ass</B> :";
    $message1 .= " $referentiel";
    entete_concept("liste_parc.inc.php",$mess_new_module);
    echo aide_simple("module");
    echo "<TR><TD colspan='3'>";
    echo "<TABLE cellpadding='3' cellspacing='2' width='100%' border='0'>";
    echo "<TR height='40'><TD>&nbsp;</TD><TD valign='bottom'>$message1</TD></TR>";
    if ($choix_seq == 1)
    {
       echo "<FORM NAME='form2' ACTION=\"parcours.php?liste=$liste&insert=1&choix_seq=1&parcours=$parcours&id_parc=$id_parc&proprio=$proprio&refer=$refer&choix_ref=$choix_ref&id_ref_parc=$id_ref_parc&miens_parc=$miens_parc&refer=$refer&parc=$id_parc&miens=$miens\" method='POST' target='main'>";
       echo "<TR><TD width='33%'><B>$mess_parc_tit</B></TD><TD align=left>";
       echo stripslashes($nom);
       echo "</TD></TR><TR><TD width='33%'><B>$msq_desc_seq</B></TD><TD align=left>";
       echo stripslashes($description);
       echo "<TR height='25'><TD nowrap><B>$mess_presc_ok</B></TD><TD nowrap>";
       if ($type_parc == 1)
          echo $mess_non;
       elseif ($type_parc == 0)
          echo $mess_oui;
       echo "</TD></TR><TR height='25'><TD nowrap><B>$msq_droit_voir</B></TD><TD nowrap>";
       if ($droit_voir == 1)
          echo $mess_oui;
       elseif ($droit_voir == 0)
          echo $mess_non;
       echo "</TD></TR>";
    }
    else
    {
       echo "<FORM NAME='form2' ACTION=\"parcours.php?liste=$liste&insert=1&choix_seq=1&parcours=$parcours&id_parc=$id_parc&proprio=$proprio&refer=$refer&choix_ref=$choix_ref&id_ref_parc=0&miens_parc=$miens_parc&refer=$refer&parc=$id_parc&miens=$miens&premier=1\" method='POST' target='main'>";
       echo "<TR><TD width='33%'><B>$mess_parc_tit</B></TD><TD align=left>";
       echo "<INPUT TYPE='text'  class='INPUT'  name='nom' size='75' align='middle'></TD></TR>";
       echo "<TR><TD width='33%'><B>$msq_desc_seq</B></TD><TD align=left>";
       echo "<TEXTAREA class='TEXTAREA' name='description' align='middle' rows='6' cols='80'>$description</TEXTAREA></TD></TR>";
       echo "<INPUT TYPE='hidden' name='cles_parc' value=''";
//       echo "<TR><TD width='33%'><B>Mots-clé</B></TD><TD align=left>";
//       echo "<TEXTAREA class='TEXTAREA' name='cles_parc' align='middle' rows='5' cols='80'>$cles_parc</TEXTAREA></TD></TR>";
       echo "<TR><TD nowrap><TABLE cellpadding='0' cellspacing='0' border='0'>";
       echo "<TR height='25'><TD nowrap><B>$mess_presc_ok</B></TD></TR>";
       echo "<TR height='25'><TD nowrap><B>$msq_droit_voir&nbsp;&nbsp;&nbsp;</B></TD></TR></TABLE></TD>";
       echo "<TD><TABLE cellpadding='0' cellspacing='0' border='0'><TR height='25'><TD nowrap>";
       echo "$mess_oui&nbsp;<INPUT type='radio' name='type_parc' value='0'>&nbsp;&nbsp;&nbsp;</TD><TD nowrap>&nbsp;&nbsp;&nbsp;";
       echo "$mess_non&nbsp;<INPUT type='radio' name='type_parc' value='1'>&nbsp;&nbsp;&nbsp;</TD><TD nowrap rowspan='2' valign='center'>&nbsp;&nbsp;&nbsp;";
       echo anoter($mess_dupli_presc,"350");
       echo "</TD></TR><TR height='25'><TD>";
       echo "$mess_oui&nbsp;<INPUT type='radio' name='droit_voir' value='1'>&nbsp;&nbsp;&nbsp;</TD><TD nowrap>&nbsp;&nbsp;&nbsp;";
       echo "$mess_non&nbsp;<INPUT type='radio' name='droit_voir' value='0'>&nbsp;&nbsp;&nbsp;</TD>";
       echo "</TR></TABLE></TD></TR>";
       echo "<TR height='50'><TD>&nbsp;</TD><TD align='left' valign='bottom'><A HREF=\"javascript:checkForm(document.form2);\" onClick=\"TinyMCE.prototype.triggerSave();\" onmouseover=\"img1.src='images/fiche_identite/boutvalidb$suffixer.gif';return true;\" onmouseout=\"img1.src='images/fiche_identite/boutvalid$suffixer.gif'\">".
            "<IMG NAME=\"img1\" SRC=\"images/fiche_identite/boutvalid$suffixer.gif\" BORDER='0' onLoad=\"tempImg=new Image(0,0); tempImg.src='images/fiche_identite/boutvalidb$suffixer.gif'\"></A>";
       echo "</TD></TR></FORM>";
       echo "</TABLE></TD></TR>";
    }
    // faire a partir de seconde fois, plus de <input...>, slt pour ajouter sequences
   echo "</TABLE></TD></TR>";
   if ($insert_seq == 1 || $voir_seqs == 1)
   {
         echo "<TR><TD nowrap>$mpa_lis_seq :</TD><TD nowrap>";
            //on affiche la liste des seq au fur et a mesure
            $list_seq_query = mysql_query ("SELECT seq_cdn,seq_titre_lb from sequence,sequence_parcours where seqparc_parc_no = $id_parc and seqparc_seq_no = seq_cdn ORDER BY seqparc_ordre_no");
            $nb_seq_list = mysql_num_rows ($list_seq_query);
            $i = 0;
            while ($i != $nb_seq_list)
            {
                  $titre = mysql_result ($list_seq_query,$i,"seq_titre_lb");
                  $id_seq = mysql_result ($list_seq_query,$i,"seq_cdn");
                  echo $titre;
                  if ($nb_seq_list == 1)
                    $voir_seqs = 0;
                  else
                    $voir_seqs = 1;
                  $requete_grp = mysql_query ("select * from groupe_parcours,groupe WHERE
                                               gp_grp_no = grp_cdn group by grp_cdn
                                               order by grp_nom_lb");
                  $nomb_grp = mysql_num_rows($requete_grp);
                  if ($nomb_grp > 0)
                  {
                       $num_grp = 0;
                       while($num_grp < $nomb_grp)
                       {
                         $id_grp = mysql_result($requete_grp,$num_grp,"grp_cdn");
                         $nb_seq_parc_presc = mysql_num_rows(mysql_query ("SELECT * from prescription_$id_grp where presc_parc_no = $id_parc and presc_seq_no=$id_seq"));
                         $nbpresc +=$nb_seq_parc_presc;
                       }
                  }
                  if ($nbpresc == 0)
                  {
                     $lien = "parcours.php?liste=$liste&choix_ref=1&choix_seq=1&voir_seqs=$voir_seqs&supp=1&parcours=$parcours&id_parc=$id_parc&proprio=$proprio&refer=$refer&id_ref_parc=$id_ref_parc&id_seq=$id_seq&miens_parc=$miens_parc&refer=$refer";
                     $lien = urlencode($lien);
                     echo "&nbsp;<a href=\"javascript:void(0);\" onclick=\"javascript:return(confm('trace.php?link=$lien'));\" target=\"main\"><IMG SRC=\"images/messagerie/icoGpoubel.gif\" height=\"20\" width=\"15\" ALT=\"$mpa_supp_seq\" BORDER=0></A><br>";
                  }
                  else
                     echo "<IMG SRC=\"images/repertoire/icoptiinterdit.gif\" height=\"15\" width=\"15\" ALT=\"$msq_nosup_seq\" BORDER=0 ".
                          bulle($msq_nosup_seq,"","CENTER","ABOVE",150)."<br>";
                  $i++;
            } //fin while
   }
   echo "</TD></TR>";
   //
 //On insere les sequences dans le parcours
   if ($choix_seq == 1)
   {
      echo "</TD></TR>";
     if ($id_ref_parc == 0)
     {
       $lien="sequence.php?liste=$liste&choix_ref=1&parcours=1&id_parc=$id_parc&proprio=$proprio&refer=$refer&id_ref_parc=$id_ref_parc";
       $lien = urlencode($lien);
       echo "<TR height='40'><TD align='left'><a href=\"trace.php?link=$lien\" target='main'>$msq_creer_seq_parc</a></TD></TR>";
     }
     echo "</FORM>";
     $le_type = GetDataField ($connect,"select parcours_type_on from parcours where parcours_cdn = $id_parc","parcours_type_on");
      if ($medor == 1)
      {
        if ($keydesc != "" && $keytitre == "" && $keypub == "")
           $champ_rech = "(sequence.seq_desc_cmt like \"%$keydesc%\" OR sequence.seq_desc_cmt like \"$keydesc%\" OR sequence.seq_desc_cmt like \"%$keydesc\")";
        elseif ($keydesc == "" && $keytitre != "" && $keypub == "")
           $champ_rech = "(sequence.seq_titre_lb like \"%$keytitre%\" OR sequence.seq_titre_lb like \"$keytitre%\" OR sequence.seq_titre_lb like \"%$keytitre\")";
        elseif ($keydesc == "" && $keytitre == "" && $keypub != "")
           $champ_rech = "sequence.seq_publique_on = '$keypub'";
        elseif ($keydesc != "" && $keytitre != "" && $keypub == "")
           $champ_rech = "(sequence.seq_titre_lb like \"%$keytitre%\" OR sequence.seq_titre_lb like \"$keytitre%\" OR sequence.seq_titre_lb like \"%$keytitre\") AND (sequence.seq_desc_cmt like \"%$keydesc%\" OR sequence.seq_desc_cmt like \"$keydesc%\" OR sequence.seq_desc_cmt like \"%$keydesc\")";
        elseif ($keydesc == "" && $keytitre != "" && $keypub != "")
           $champ_rech = "sequence.seq_publique_on = '$keypub' AND (sequence.seq_titre_lb like \"%$keytitre%\" OR sequence.seq_titre_lb like \"$keytitre%\" OR sequence.seq_titre_lb like \"%$keytitre\")";
        elseif ($keydesc != "" && $keytitre == "" && $keypub != "")
           $champ_rech = "sequence.seq_publique_on = '$keypub' AND (sequence.seq_desc_cmt like \"%$keydesc%\" OR sequence.seq_desc_cmt like \"$keydesc%\" OR sequence.seq_desc_cmt like \"%$keydesc\")";
        elseif ($keydesc != "" && $keytitre != "" && $keypub != "")
           $champ_rech = "sequence.seq_publique_on = '$keypub' AND (sequence.seq_titre_lb like \"%$keytitre%\" OR sequence.seq_titre_lb like \"$keytitre%\" OR sequence.seq_titre_lb like \"%$keytitre\") AND (sequence.seq_desc_cmt like \"%$keydesc%\" OR sequence.seq_desc_cmt like \"$keydesc%\" OR sequence.seq_desc_cmt like \"%$keydesc\")";
        elseif ($keydesc == "" && $keytitre == "" && $keypub == "" && $type_lb == ""){
           $ordre_affiche = "lenom";
           $refer = 2;
           $medor = 0;
        }
        if ($type_lb == "SCORM")
           $champ_rech .= " AND sequence.seq_type_lb != 'NORMAL'";
        elseif ($type_lb == "NORMAL")
           $champ_rech .= " AND sequence.seq_type_lb == 'NORMAL'";
        elseif ($type_lb == "SCORM" && $champ_rech == "")
           $champ_rech .= " sequence.seq_type_lb != 'NORMAL'";
        elseif ($type_lb == "NORMAL" && $champ_rech == "")
           $champ_rech .= " sequence.seq_type_lb = 'NORMAL'";
        if ($keypub == "")
           $champ_rech = $champ_rech;
        elseif ($keypub == 0)
           $champ_rech .= " AND sequence.seq_publique_on = '0' AND seq_auteur_no = $id_user";
        elseif ($keypub == 1)
           $champ_rech .= " AND sequence.seq_publique_on = '1'";
        if ($keypresc == 1 && $champ_rech == "")
           $champ_rech .= " seq_type_on = 1";
        elseif ($keypresc == 1 && $champ_rech != "")
           $champ_rech .= " AND seq_type_on = 1";
        if (isset($keypresc) && $keypresc != "" && $keypresc == 0 && $champ_rech == "")
           $champ_rech .= " seq_type_on = 0";
        elseif (isset($keypresc) && $keypresc != "" && $keypresc == 0 && $champ_rech != "")
           $champ_rech .= " AND seq_type_on = 0";
        if ($le_type == 0)
          $seq_query = mysql_query ("select * from sequence where $champ_rech and seq_type_lb = 'NORMAL' and sequence.seq_type_on = 0 order by seq_titre_lb asc");
        else
          $seq_query = mysql_query ("select * from sequence where $champ_rech and seq_type_lb = 'NORMAL' and sequence.seq_type_on = 1 order by seq_titre_lb asc");
    // fin de configuration des champs à discriminer
        $nb_seq = mysql_num_rows($seq_query);
        if ($nb_seq ==0)
          echo "<TR><TD colspan='2'><B>$mess_no_occur</B></TD></TR>";
       }
       echo "<TR><TD colspan='4'><table cellpadding='4' cellspacing='0' width='80%' border='0'>";
       echo "<FORM name='form5' ACTION=\"parcours.php?utilisateur=$utilisateur&action_parc=1&id_seq=$id_seq&id_ref_parc=$id_ref_parc&parcours=$parcours&liste=$liste&id_parc=$id_parc&proprio=$proprio&refer=$refer&miens_parc=$miens_parc&medor=1&vient_de_search=1&parc=$id_parc&miens=$miens\" METHOD='POST' target='main'>";
       echo "<TR bgcolor= '#F4F4F4'><TD nowrap colspan='7'><B>$mrc_rech</B></TD></TR>";
       echo "<TR><TD nowrap>$msq_titre</TD><TD nowrap><INPUT TYPE='text'  class='INPUT'   name='keytitre' size='20' align='middle'></TD>";
       echo "<TD nowrap>$msq_desc</TD><TD nowrap><INPUT TYPE='text'  class='INPUT'   name='keydesc' size='20' align='middle'></TD>";
         echo "<TD nowrap>Type</TD>";
         echo "<TD nowrap><SELECT name='type_lb' class='SELECT'>";
         echo "<OPTION></OPTION>";
         echo "<OPTION value='NORMAL'>NORMAL</OPTION>";
         echo "<OPTION value='SCORM'>SCORM</OPTION>";
         echo "</SELECT></TD>";
       if ($typ_user == "FORMATEUR_REFERENT")
         echo "<INPUT TYPE='HIDDEN'  name='keypub' value='1' align='middle'>";
       else
       {
         echo "<TD nowrap>$mess_visdup</TD>";
         echo "<TD nowrap>";
         echo "<SELECT name='keypub' class='SELECT'>";
           echo "<OPTION></OPTION>";
           echo "<OPTION value='1'>$mess_oui</OPTION>";
           echo "<OPTION value='0'>$mess_non</OPTION>";
         echo "</SELECT></TD>";
       }
       echo "<TD align='center'><A HREF=\"javascript:document.form5.submit();\" onmouseover=\"img1.src='images/fiche_identite/boutvalidb$suffixer.gif';return true;\" onmouseout=\"img1.src='images/fiche_identite/boutvalid$suffixer.gif'\">".
       "<IMG NAME=\"img1\" SRC=\"images/fiche_identite/boutvalid$suffixer.gif\" BORDER='0' onLoad=\"tempImg=new Image(0,0); tempImg.src='images/fiche_identite/boutvalidb$suffixer.gif'\"></A>";
       echo "</TD></TR>";
       if ($medor == 1 && $nb_seq > 0)
       {
        echo "<TR><TD colspan='2'><B>$mess_nbseq  :  $nb_seq &nbsp;&nbsp;&nbsp;</B>";
        if ($keytitre != "") echo "<B>$msq_titre : <font color='#D45211'>$keytitre</B></font> , ";//else echo "<B>$mess_nofiltre</B>, ";
        if ($keydesc != "") echo "<B>$msq_desc : <font color='#D45211'>$keydesc</B></font> , ";//else echo "<B>$mess_nofiltre</B>, ";
        if ($type_lb != "") echo "<B>$mess_type : <font color='#D45211'>$type_lb</B></font> , ";//else echo "<B>$mess_nofiltre</B>, ";
        if ($keypub == 1) echo "<B>$mess_visdup :<font color='#D45211'>$mess_oui</B></font> ";elseif(isset($keypub) && $keypub == 0 && $keypub != "") echo "$mess_visdup :<font color='#D45211'><B>$mess_non</B></font> ";//else echo "<B>$mess_nofiltre</B>";
        if (isset($keypresc) && $keypresc != "" && $keypresc == 0) echo "<B>$mess_presc :<font color='#D45211'>$mess_oui</B></font> ";elseif( $keypresc == 1) echo "$mess_presc :<font color='#D45211'><B>$mess_non</B></font> ";//else echo "<B>$mess_nofiltre</B>";
        echo "</TD></TR>";
       }
       echo "</form></TABLE></TD></TR>";
       if ($medor == 1 && $nb_seq > 0)
       {
         echo "<TR><TD colspan='2'><table cellpadding='6' cellspacing='2' width='100%' border='0'>";
         echo "<TR bgcolor='#336666'>";
         echo "<TD height='20' align='middle'><FONT COLOR=white><b>$msq_seq</b></FONT></TD>";
         echo "<TD height='20' align='middle'><FONT COLOR=white><b>$mrc_aut</b></FONT></TD>";
         echo "<TD height='20' align='middle'><FONT COLOR=white><b>$msq_ref_ass</b></FONT></TD></TR>";
         $i = 0;
         while ($i < $nb_seq)
         {
           $id_sequence = mysql_result ($seq_query,$i,"seq_cdn");
           $nom = mysql_result ($seq_query,$i,"seq_titre_lb");
           $createur = mysql_result ($seq_query,$i,"seq_auteur_no");
           $type_seq = mysql_result ($seq_query,$i,"seq_type_on");
           $mod_seq = mysql_result ($seq_query,$i,"seq_type_lb");
           $droit_voir_seq = mysql_result ($seq_query,$i,"seq_publique_on");
           if ($type_seq == 1 && $droit_voir_seq == 1)
           {
              $div = "<DIV id='sequence'>";
              $icono = "<IMG SRC='images/modules/icotype.gif' border='0'>";
           }
           else
           {
              $div = "<DIV id='sequence'>";
              $icono = "";
           }
           if (strstr($mod_seq,"SCORM") && $icono == ""){
              $div = "<DIV id='sequence'>";
              $icono .= "<IMG SRC='images/gest_parc/scorm.gif' border='0' alt=\"$mess_seq_sco\">";
           }elseif (strstr($mod_seq,"SCORM") && $icono != "")
              $icono .= "&nbsp<IMG SRC='images/gest_parc/scorm.gif' border='0' alt=\"$mess_seq_sco\">";
           if ($typ_user != 'ADMINISTRATEUR' && $droit_voir_seq == 0 && $createur != $id_user){
            $i++;
            continue;
           }
           $nom_createur = GetDataField ($connect,"select util_nom_lb from utilisateur where util_cdn = $createur","util_nom_lb");
           $prenom_createur = GetDataField ($connect,"select util_prenom_lb from utilisateur where util_cdn = $createur","util_prenom_lb");
           $req_parc = mysql_query ("SELECT parcours.parcours_nom_lb FROM parcours,sequence_parcours WHERE sequence_parcours.seqparc_seq_no=$id_sequence AND sequence_parcours.seqparc_parc_no=parcours.parcours_cdn group by parcours.parcours_nom_lb order by parcours.parcours_type_lb, parcours.parcours_nom_lb");
           $nb_parc_seq = mysql_num_rows ($req_parc);
           $la_serie = "";
           if ($nb_parc_seq > 0)
           {
              $psi=0;
              while ($psi < $nb_parc_seq)
              {
               $p_nom = mysql_result($req_parc,$psi,"parcours_nom_lb");
               $la_serie .="<LI>".$p_nom."</LI>";
               $psi++;
              }
           }
           $seq_ref_query = mysql_query ("select seqref_referentiel_no from sequence_referentiel where seqref_seq_no = $id_sequence");
           $nb_seq_ref = mysql_num_rows ($seq_ref_query);
           if ($nb_seq_ref == 1)
           {
             $id_ref = GetDataField ($connect,"select seqref_referentiel_no from sequence_referentiel where seqref_seq_no = $id_sequence","seqref_referentiel_no");
             if ($id_ref == 0)
               $nom_referentiel = $msq_sl;
             else
               $nom_referentiel = GetDataField ($connect,"select ref_nomabrege_lb from referentiel where ref_cdn = $id_ref","ref_nomabrege_lb");
             $lien="parcours.php?liste=$liste&action_parc=1&insert_seq=1&id_ref=$id_ref&id_ref_parc=$id_ref_parc&parcours=$parcours&id_parc=$id_parc&proprio=$proprio&refer=$refer&seq=$id_sequence&miens_parc=$miens_parc&refer=$refer";
             $lien = urlencode($lien);
             echo couleur_tr($i+1,'');
             echo "<TD nowrap>$div";
             if ($droit_voir_seq == 0)
                echo "<IMG SRC=\"images/modules/tut_form/icocadenas.gif\" border='0'>";
             echo "&nbsp;<A HREF=\"trace.php?link=$lien\"";
             if ($la_serie != "")
               echo " onMouseOver=\"overlib('<TABLE><TR><TD width=5></TD><TD>".addslashes($la_serie)."</TD></TR></TABLE>',ol_hpos,RIGHT,DELAY,'800',WIDTH,'312',CAPTION,'<TABLE><TR><TD width=5></TD><TD align=left><FONT SIZE=2 color =#333333><B>".addslashes($seq_parc_exist)."</B></FONT><BR></TD</TR></TABLE>')\" onMouseOut=\"nd()\"";
             echo " target='main'>$icono $nom</A></DIV></TD>";
             if (($miens_parc != 1 || ($miens_parc == 1 && $vient_de_search == 1)) && $proprio == "")
             {
               $lien="parcours.php?utilisateur=$utilisateur&action_parc=1&id_parc=$id_parc&proprio=$proprio&refer=$refer&ordre_affiche=lenom&vient_de_search=$vient_de_search&miens_parc=$miens_parc&refer=$refer";
               $lien = urlencode($lien);
               echo "<TD nowrap><DIV id='sequence'><A HREF=\"trace.php?link=$lien\" TITLE = \"$msq_consult_mod $de $prenom_createur $nom_createur\">$prenom_createur $nom_createur</A></DIV></TD>";
             }
             if ($refer > 0  || $miens_parc == 1 || $proprio > 0 || $medor == 1)
                echo "<TD>".html_entity_decode($nom_referentiel,ENT_QUOTES,'ISO-8859-1')."</TD>";
             echo "</TR>";
           }
           else
           {
             $nbr_ref = 0;
             while ($nbr_ref <$nb_seq_ref)
             {
               $id_ref = mysql_result ($seq_ref_query,$nbr_ref,"seqref_referentiel_no");
               if ($id_ref == 0)
                 $nom_referentiel = $msq_sl;
               else
                 $nom_referentiel = GetDataField ($connect,"select ref_nomabrege_lb from referentiel where ref_cdn = $id_ref","ref_nomabrege_lb");
               $lien="parcours.php?liste=$liste&consult=1&modif_parc=1&id_ref=$id_ref&id_ref_parc=$id_ref_parc&parcours=$parcours&id_parc=$id_parc&proprio=$proprio&refer=$refer&miens_parc=$miens_parc&refer=$refer&droit_voir=$droit_voir";
               $lien = urlencode($lien);
               echo couleur_tr($i+1,'');
               echo "<TD nowrap>$div";
               if ($droit_voir_seq == 0)
                  echo "<IMG SRC=\"images/modules/tut_form/icocadenas.gif\" border='0'>";
               echo "&nbsp;<A HREF=\"trace.php?link=$lien\"";
               if ($la_serie != "")
                 echo " onMouseOver=\"overlib('<B>".addslashes($seq_parc_exist)."</B> : ".addslashes($la_serie)."',ol_hpos,RIGHT,WIDTH,'310',CAPTION,'<center>$msq_desc_titre</center>')\" onMouseOut=\"nd()\"";
               echo " target='main'>$icono $nom</A></DIV></TD>";
               if ($proprio == "" && $miens_parc != 1)
                 echo "<TD nowrap>".$prenom_createur." ".$nom_createur."</TD>";
               echo "<TD>".html_entity_decode($nom_referentiel,ENT_QUOTES,'ISO-8859-1')."</TD>";
               echo "</TR>";
              $nbr_ref++;
             }
           }
         $i++;
         }
       }
       echo"</TD</TR>";
     echo "<INPUT TYPE='HIDDEN'  name='id_ref' value='$id_ref'>";
     $lien= "parcours.php?liste=$liste&consult=1&parcours=$parcours&id_parc=$id_parc&proprio=$proprio&refer=$refer&id_ref=$id_ref&id_ref_parc=$id_ref_parc&miens_parc=$miens_parc&refer=$refer&parc=$id_parc&miens=$miens";
     $lien=urlencode($lien);
    echo fin_tableau($html);
   }     //fin if ($choix_seq == 1)
} //fin if ($choix_ref == 1)


//Affichage des parcours par referentiel
if ($consult == 1)
{
     //Suppression d'un parcours
    require_once ('class/Class_Rss.php');
    if ($supp == 1)
    {
   // à remettre pour supprimer les prescriptions concernant ce parcours supprimé
        $requete_grp = mysql_query ("select * from groupe_parcours order by gp_grp_no");
        $nb_grp_parc = mysql_num_rows($requete_grp);
        if ($nb_grp_parc > 0)
        {
                $gp=0;
                while ($gp < $nb_grp_parc)
                {
                  $id_grp = mysql_result($requete_grp,$gp,"gp_grp_no");
                  $nb_presc = mysql_num_rows(mysql_query("select * from prescription_$id_grp where presc_parc_no=$id_parc"));
                  $nb_presc_parc += $nb_presc;
                $gp++;
                }
        }
          if ($nb_presc_parc != 0)
          {
            $list_seq_query = mysql_query ("SELECT seq_cdn from sequence,sequence_parcours where sequence_parcours.seqparc_parc_no = $id_parc and sequence_parcours.seqparc_seq_no = sequence.seq_cdn");
            $nb_seq_list = mysql_num_rows ($list_seq_query);
            if ($nb_seq_list !=0)
            {
                $i = 0;
                while ($i != $nb_seq_list) {
                       $id_seq = mysql_result ($list_seq_query,$i,"seq_cdn");
                $i++;
                } //fin while
            }
            $message_supp= $mpa_nosup_parc;
          }
          else
          {
            $nom_parc = GetDataField ($connect,"SELECT parcours_nom_lb from parcours where parcours_cdn = '$id_parc'","parcours_nom_lb");
            $mod_parc = GetDataField ($connect,"SELECT parcours_type_lb from parcours where parcours_cdn = '$id_parc'","parcours_type_lb");
            $del_parc = mysql_query ("DELETE from parcours where parcours_cdn = $id_parc");
            $del_for_parc = mysql_query ("DELETE from forums_modules where fm_module_no = $id_parc");
            $del_parc_for = mysql_query ("DELETE from parcours_forums where parcforum_parc_no = $id_parc");
            $del_star = mysql_query ("DELETE from stars where star_item_id = $id_parc and star_type_no=1");
            $supp_rss = rss :: supprime('module',$id_parc);
            if (strstr($mod_parc,"SCORM"))
            {
               $sup_req = mysql_query("SELECT seqparc_seq_no from sequence_parcours where seqparc_parc_no = $id_parc");
               $nb_sup_seq = mysql_num_rows($sup_req);
               for ($is = 0;$is < $nb_sup_seq;$is++){
                 $id_supp = mysql_result($sup_req,$is,"seqparc_seq_no");
                 $del_seq = mysql_query("DELETE from sequence where seq_cdn = $id_supp");
                 $del_star = mysql_query ("DELETE from stars where star_item_id = $id_supp and star_type_no='2'");
                 $del_seq_prereq = mysql_query("DELETE from prerequis where prereq_seqcondition_no = $id_supp OR  prereq_seq_no = $id_supp");
                 $del_seq_ref = mysql_query("DELETE from sequence_referentiel where seqref_seq_no = $id_supp");
               }
            }
            $sup_trq = mysql_query("SELECT mod_cdn from scorm_module where mod_parc_no = $id_parc");
            $nb_sup_trq = mysql_num_rows($sup_trq);
            if ($nb_sup_trq > 0)
            {
               for ($is = 0;$is < $nb_sup_trq;$is++)
               {
                  $id_supp_mod = mysql_result($sup_trq,$is,"mod_cdn");
                  $del_mod_trq = mysql_query ("DELETE from traque where traq_mod_no = $id_supp_mod");
               }
            }
            $del_mod = mysql_query ("DELETE from scorm_module where mod_parc_no = $id_parc");
            $del_mod_trq = mysql_query ("DELETE from traque where mod_parc_no = $id_parc");
            $del_seqparc = mysql_query ("DELETE from sequence_parcours where seqparc_parc_no = $id_parc");
            $del_grpparc = mysql_query ("DELETE from groupe_parcours where gp_parc_no = $id_parc");
            $message_supp= $mess_lemod." - ".$nom_parc." - ".$parc_supp_ok;
          }
      $lien = "parcours.php?prem=1&liste=1&ordre_affiche=lenom&mess_notif=$message_supp&refer=$refer&id_ref_parc=$id_ref_parc&parc=$id_parc&miens=$miens&miens_parc=$miens_parc";
      $lien = urlencode($lien);
      echo "<script language=\"JavaScript\">";
      echo "document.location.replace(\"trace.php?link=$lien\")";
      echo "</script>";
      exit();
    }

    if ($modif_parc == 1)
    {
        if (isset($_POST['cree_forum']) && $_POST['cree_forum'] == 1)
        {
           $requete = mysql_query("insert into parcours_forums(parcforum_parc_no,parcforum_create_dt) values ($id_parc,\"$date_dujour\")");
        }
        $upd_parc = mysql_query ("update parcours set parcours_modif_dt = \"$date_dujour\",
                                  parcours_publique_on = '$droit_voir_parc1', parcours_type_on = '$parc_type', parcours_nom_lb = \"".
                                  NewHtmlentities($nom,ENT_QUOTES)."\",parcours_desc_cmt = \"".htmlentities($description,ENT_QUOTES,'iso-8859-1')."\",
                                  parcours_mots_clef = \"".htmlentities($clefs,ENT_QUOTES,'iso-8859-1').
                                  "\",parcours_referentiel_no = $id_ref_parc where parcours_cdn = $id_parc");
        $modifie_rss = rss :: modifie('module',$id_user,$id_parc);
        $mess_notif = $mess_lemod." - ".$nom." - ".$mod_mod_ok;
    }
    if ($modif_parcref == 1)
    {
        $upd_parc = mysql_query ("update parcours set parcours_referentiel_no = '$id_ref_parc' where parcours_cdn = '$id_parc'");
        $nom = GetDataField ($connect,"SELECT parcours_nom_lb from parcours where parcours_cdn = '$id_parc'","parcours_nom_lb");
        $mess_notif = $mess_suit_dch;
    }
    if (isset($ok_dupli) && $ok_dupli == 1)
    {
        $nom = GetDataField ($connect,"SELECT parcours_nom_lb from parcours where parcours_cdn = '$ancien_parc'","parcours_nom_lb");
        $mess_notif = $mess_lemod." - ".$nom." - ".$mess_dupok;
    }
    if ($liste == 1)
      $parc_query = mysql_query ("SELECT * from parcours where parcours_cdn = $id_parc");
    else
    {
      if ($typ_user == "FORMATEUR_REFERENT" || $typ_user == "RESPONSABLE_FORMATION" )
        $parc_query = mysql_query ("SELECT * from parcours where parcours_referentiel_no = $id_ref_parc and parcours_cdn != 0 and parcours_auteur_no = $id_user and parcours_cdn = $id_parc");
      else
        $parc_query = mysql_query ("SELECT * from parcours where parcours_referentiel_no = $id_ref_parc and parcours_cdn != 0 and parcours_cdn = $id_parc");
      if ($typ_user == "FORMATEUR_REFERENT")
        $parc_query = mysql_query ("SELECT * from parcours where parcours_referentiel_no = $id_ref_parc and parcours_cdn != 0 and parcours_auteur_no = $id_user and parcours_cdn = $id_parc");
      else
        $parc_query = mysql_query ("SELECT * from parcours where parcours_referentiel_no = $id_ref_parc and parcours_cdn != 0 and parcours_cdn = $id_parc");
    }
    $nb_parc = mysql_num_rows ($parc_query);
    if ($id_ref_parc == 0)
       $nom_referentiel = $msq_pl;
    else
       $nom_referentiel = GetDataField ($connect,"SELECT ref_nomabrege_lb from referentiel where ref_cdn = $id_ref_parc","ref_nomabrege_lb");
    if ($nom_referentiel == "")
       $nom_referentiel = $mpa_niv_dip;
    $bgcolor2 = '#2B677A';
    $bgcolor1 = '#F2EBDC';
    $letitre = $mess_menu_gest_parc;
    if ($id_ref_parc == 0)
       $sous_titre = "<B>$msq_ref_ass : </B>".strtolower($msq_sra);
    else
       $sous_titre = "<B>$msq_ref_ass : </B>".html_entity_decode($nom_referentiel,ENT_QUOTES,'iso-8859-1');
    if (isset($mess_notif) && $mess_notif != '')
         echo notifier($mess_notif);
    entete_concept("liste_parc.inc.php",$letitre);
    echo aide_simple("module");
    echo "<TR><TD width=88%><TABLE width='100%' bgColor='#FFFFFF' cellspacing='1' cellpadding='4'>";
    if ($sous_titre != '')
       echo "<TR><TD colspan='6' class='sous_titre'>$sous_titre</TD></TR>";
    echo "<TR bgcolor=$bgcolor2>";
    echo "<TD  height='20' align='left'><FONT COLOR=white><b>$msq_parc</b></FONT></TD>";
    echo "<TD width='5%' height='20' align='left' nowrap><FONT COLOR=white><b>$mpa_nbr_seq</b></FONT></TD>";
    echo "<TD width='8%' height='20' align='left'><FONT COLOR=white><b>$mess_gp_duree</b></FONT></TD>";
    echo "<TD align='left'><FONT COLOR=white><b>$mess_modif_base</b></FONT></TD>";
    echo "<TD align='left'><FONT COLOR=white><b>$mess_ag_supp</b></FONT></TD>";
    echo "<TD align='left'><FONT COLOR=white><b>$mess_dupliquer</b></FONT></TD>";
    echo "</TR>";
        $typ_user = GetDataField ($connect,"SELECT util_typutil_lb from utilisateur where util_cdn = $id_user","util_typutil_lb");
        $i=0;
        while ($i != $nb_parc) {
              $id_parc = mysql_result ($parc_query,$i,"parcours_cdn");
              $nom = mysql_result ($parc_query,$i,"parcours_nom_lb");
              $parc_type = mysql_result ($parc_query,$i,"parcours_type_on");
              $mod_parc = mysql_result ($parc_query,$i,"parcours_type_lb");
              $droit_voir = mysql_result ($parc_query,$i,"parcours_publique_on");
              $description = html_entity_decode(mysql_result ($parc_query,$i,"parcours_desc_cmt"),ENT_QUOTES,'iso-8859-1');
              $cles = html_entity_decode(mysql_result ($parc_query,$i,"parcours_mots_clef"),ENT_QUOTES,'iso-8859-1');
              $id_ref_parc = mysql_result ($parc_query,$i,"parcours_referentiel_no");
              $nb_seq_parc_query = mysql_query ("SELECT count(seqparc_cdn) from sequence_parcours where seqparc_parc_no = $id_parc");
              $nb_seq = mysql_result ($nb_seq_parc_query,0);
              $desc_ref = GetDataField ($connect,"SELECT ref_nom_lb from referentiel where ref_cdn = '$id_ref_parc'","ref_nom_lb");
              if ($desc_ref == "")
                 $desc_ref = $mpa_nodesc;
              $niv_ref = GetDataField ($connect,"SELECT ref_denom_lb from referentiel where ref_cdn = '$id_ref_parc'","ref_denom_lb");
              if ($parc_type == 1 && $droit_voir == 1){
                 $div = "<DIV id='sequence'>";
                 $icono = "<IMG SRC='images/modules/icotype.gif' border='0'>";
              }else{
                 $div = "<DIV id='sequence'>";
                 $icono = "";
              }
              if (strstr($mod_parc,"SCORM") && $icono == ""){
                 $div = "<DIV id='sequence'>";
                 $icono = "<IMG SRC='images/gest_parc/scorm.gif' border='0' alt=\"Module SCORM\">";
              }elseif (strstr($mod_parc,"SCORM") && $icono != "")
                 $icono .= "<IMG SRC='images/gest_parc/scorm.gif' border='0' alt=\"Module SCORM\">";
              if (strstr($mod_seq,"SCORM") && $icono == ""){
                 $div = "<DIV id='sequence'>";
                 $icono .= "<IMG SRC='images/gest_parc/scorm.gif' border='0' alt=\"$mess_seq_sco\">";
              }elseif (strstr($mod_seq,"SCORM") && $icono != "")
                 $icono .= "&nbsp<IMG SRC='images/gest_parc/scorm.gif' border='0' alt=\"$mess_seq_sco\">";

              //Duree du parcours
              $id_seq_query = mysql_query ("SELECT seqparc_seq_no from sequence_parcours where seqparc_parc_no = $id_parc");
              $nb_seq2 = mysql_num_rows ($id_seq_query);
              $duree_parc = 0;
              $j=0;
              echo couleur_tr($i,20);
              $nb_sequence_type = 0;
              while ($j < $nb_seq2) {
                $id_seq = mysql_result ($id_seq_query,$j,"seqparc_seq_no");
                $act_ress_seq = mysql_query("SELECT act_ress_no from activite where act_seq_no = $id_seq");
                if ($act_ress_seq == TRUE)
                {
                   while($item = mysql_fetch_object($act_ress_seq))
                   {
                      $ress = GetDataField ($connect,"select ress_url_lb from ressource_new where ress_cdn = ".$item->act_ress_no,"ress_url_lb");
                      if (strstr($ress,'qcm.php?'))
                      {
                         $qcm_Ok++;
                         break;
                      }
                   }
                }
                $seq_type = GetDataField ($connect,"SELECT seq_type_on from sequence where seq_cdn = $id_seq","seq_type_on");
                $sequence_type = GetDataField ($connect,"SELECT seq_type_lb from sequence where seq_cdn = $id_seq","seq_type_lb");
                $seq_droit = GetDataField ($connect,"SELECT seq_publique_on from sequence where seq_cdn = $id_seq","seq_publique_on");
                if ($seq_type == 1 && $seq_droit == 1)
                   $nb_seq_type++;
                if (strstr($sequence_type,'SCORM'))
                   $nb_sequence_type++;
                $duree_seq = GetDataField ($connect,"SELECT seq_duree_nb from sequence where seq_cdn = $id_seq","seq_duree_nb");
                $duree_parc+=$duree_seq;
                $j++;
              } //fin while ($j != $nb_seq)
              $heure = floor($duree_parc/60);
              if ($heure > 0)
                 $reste = $duree_parc%60 ;
              else
                 $reste = $duree_parc;
              if ($reste == 0)
                 $duree = $heure." $h";
              else
                 $duree = $heure." $h ".$reste;
              $parc_grp = mysql_query ("SELECT * FROM groupe_parcours,groupe WHERE gp_parc_no = $id_parc AND gp_grp_no = groupe.grp_cdn AND (groupe.grp_flag_on = 1 OR (groupe.grp_flag_on = 0 AND groupe.grp_resp_no = $id_user)) GROUP BY gp_grp_no ORDER BY gp_grp_no");
              $nb_grp_parc = mysql_num_rows($parc_grp);
              $affiche_liste="";
              if ($nb_grp_parc > 0)
              {
                $affiche_liste="<B>$mess_form_parcactif</B>";
                $gp=0;
                while ($gp < $nb_grp_parc)
                {
                  $grp_id = mysql_result($parc_grp,$gp,"grp_cdn");
                  $nom_grp = mysql_result($parc_grp,$gp,"grp_nom_lb");
                  $nb_presc = mysql_num_rows(mysql_query("select * from prescription_$grp_id where presc_parc_no=$id_parc"));
                  $nbprsc += $nb_presc;
                  $affiche_liste .="<LI>$nom_grp</LI>";
                $gp++;
                }
              }
              $lien = "sequence.php?toutes_seq=1&liste=$liste&id_ref_parc=$id_ref_parc&id_parc=$id_parc&proprio=$proprio&refer=$refer&consult=1&parcours=1&dvp=$droit_voir&miens_parc=$miens_parc&refer=$refer&parc=$id_parc";
              $lien = urlencode($lien);
              echo "<td align='left' valign='top'>$div";
              if ($nb_seq > 0)
              {
                 echo "<A HREF=\"trace.php?link=$lien\" target='main'";
                 if ($affiche_liste !="")
                    echo " onMouseOver=\"overlib('<TABLE><TR><TD width=5></TD><TD>".addslashes($affiche_liste)."</TD></TR></TABLE>',ol_hpos,RIGHT,ABOVE,DELAY,'800',WIDTH,'232')\" onMouseOut=\"nd()\"";
                 echo ">$icono $nom</A>";
              }else
                 echo "<B>$icono $nom</B>";
              echo "&nbsp;:&nbsp;$description</DIV></td>";
              echo "<td align='middle' valign='top'>$nb_seq</td>";
              echo "<td align='middle' valign='top'>$duree</td>";
              $id_auteur = mysql_result ($parc_query,$i,"parcours_auteur_no");
              if ($id_auteur == $id_user || $typ_user == 'ADMINISTRATEUR')
              {
                   $lien = "parcours.php?liste=$liste&action_parc=1&parcours=$parcours&id_ref_parc=$id_ref_parc&droit_voir=$droit_voir&nb_seq=$nb_seq&duree_parc=$duree_parc&id_ref=$id_ref&id_ref_parc=$id_ref_parc&miens_parc=$miens_parc&miens=$miens&refer=$refer&parc=$id_parc&id_parc=$id_parc&proprio=$proprio&refer=$refer";
                   $lien = urlencode($lien);
                   echo "<td width='5%' align=center valign='top'><a href=\"trace.php?link=$lien\" target='main'><IMG SRC=\"images/repertoire/icoGrenomfich20.gif\" height=\"20\" width=\"20\" border='0' ".
                        bulle($mpa_mod_parc,"","LEFT","ABOVE",130)."</A></td>";
                   if ($nbprsc == 0)
                   {
                      $lien = "parcours.php?liste=1&consult=1&supp=1&parcours=$parcours&id_parc=$id_parc&proprio=$proprio&refer=$refer&id_ref_parc=$id_ref_parc&ordre_affiche=lenom&miens=$miens&miens_parc=$miens_parc&refer=$refer&parc=$id_parc";
                      $lien = urlencode($lien);
                      echo "<td width='5%' align=center valign='top'><a href=\"javascript:void(0);\" onclick=\"javascript:return(confm('trace.php?link=$lien'));\" target='main'><IMG SRC=\"images/messagerie/icopoubelressour.gif\" height=\"20\" width=\"15\" ALT=\"$mpa_sup_parc\" BORDER=0></A></td>";
                   }else
                      echo "<td width='5%' align=center valign='top'><IMG SRC=\"images/repertoire/icoptiinterdit.gif\" border='0' ".
                           bulle($mpa_nosup_parc,"","LEFT","ABOVE",220)."</A></td>";
              }
              else
                 echo "<TD>&nbsp;</TD><TD>&nbsp;</TD>";
              if ($droit_voir == 1)
              {
                 $id_ref_parc = GetDataField ($connect,"SELECT parcours_referentiel_no from parcours where parcours_cdn = '$id_parc'","parcours_referentiel_no");
                 $lien="parcours.php?liste=$liste&dupli_parc=1&parcours=$parcours&id_parc=$id_parc&proprio=$proprio&refer=$refer&id_ref=$id_ref&id_ref_parc=$id_ref_parc&parc=$id_parc&miens=$miens&miens_parc=$miens_parc";
                 $lien = urlencode($lien);
                 echo "<TD nowrap align='center' valign='top'><A HREF=\"trace.php?link=$lien\" target='main'".
                      bulle($msq_dupli_parc,"","LEFT","ABOVE",130).
                      "<IMG SRC=\"images/repertoire/icoptiedit.gif\" BORDER='0'></A></TD>";
              }else
                 echo "<TD>&nbsp;</TD>";
       $i++;
       echo"</TR>";
       } //fin while ($i != $nb_parc)
       echo "</table></center><P>";
       echo "<TABLE border=0 cellpadding='4' cellspacing = '4'><TR>";
       if ($id_ref_parc == 0)
       {
          $lien="referenciel.php?parcours=1&consult=1&modif_parcref=1&id_parc=$id_parc&proprio=$proprio&refer=$refer&parc=$id_parc";
          $lien = urlencode($lien);
          echo "<TD nowrap>$bouton_gauche<a href=\"trace.php?link=$lien\" target='main'>$mess_prc_ref_mod</a>$bouton_droite</TD>";
       }
       else
       {
          $lien="parcours.php?liste=1&consult=1&modif_parcref=1&id_ref_parc=0&id_parc=$id_parc&proprio=$proprio&refer=$refer&parc=$id_parc";
          $lien = urlencode($lien);
          echo "<TD nowrap>$bouton_gauche<a href=\"trace.php?link=$lien\" target='main'>$parc_hrs_ref</a>$bouton_droite</TD>";
          $lien="referenciel.php?parcours=1&consult=1&modif_parcref=1&id_parc=$id_parc&proprio=$proprio&refer=$refer&parc=$id_parc";
          $lien = urlencode($lien);
          echo "<TD nowrap colspan='2'>$bouton_gauche<a href=\"trace.php?link=$lien\" target='main'>$msq_go_aut_ref</a>$bouton_droite</TD>";
       }
       echo "<TR>";
/*
       if ($droit_voir_parc == 1){// Droit de voir et dupliquer le parcours
          $id_ref_parc = GetDataField ($connect,"SELECT parcours_referentiel_no from parcours where parcours_cdn = '$id_parc'","parcours_referentiel_no");
          $lien="parcours.php?liste=$liste&dupli_parc=1&parcours=$parcours&id_parc=$id_parc&proprio=$proprio&refer=$refer&id_ref=$id_ref&id_ref_parc=$id_ref_parc&parc=$id_parc&miens=$miens&miens_parc=$miens_parc";
          $lien = urlencode($lien);
          echo "<TD nowrap>$bouton_gauche<A href=\"trace.php?link=$lien\" target='main'>$msq_dupli_parc</A>$bouton_droite</TD>";
       }
*/
       if ($id_auteur == $id_user || $typ_user == 'ADMINISTRATEUR')
       {
          $liste_param = "liste=$liste&export_parc=1&parcours=$parcours&id_parc=$id_parc&proprio=$proprio&refer=$refer&id_ref_parc=$id_ref_parc&id_ref=$id_ref&parc=$id_parc&miens=$miens&miens_parc=$miens_parc";
          $liste_paramb = "liste=$liste&export_parc=1&structure=1&parcours=$parcours&id_parc=$id_parc&proprio=$proprio&refer=$refer&id_ref_parc=$id_ref_parc&id_ref=$id_ref&parc=$id_parc&miens=$miens&miens_parc=$miens_parc";
          $liste_vars = addslashes(str_replace("&","|","parc=$id_parc&id_ref=$id_ref&miens=$miens&miens_parc=$miens_parc&parcours=$parcours&id_parc=$id_parc&proprio=$proprio&refer=$refer&id_ref_parc=$id_ref_parc&miens=$miens"));
          $lien="export_parcours.php?$liste_param&liste_vars=$liste_vars";
          $lienb="export_parcours.php?$liste_paramb&liste_vars=$liste_vars";
          $lienc="export_parc_xml.php?id_parc=$id_parc";
          $lien = urlencode($lien);
          $lienb = urlencode($lienb);
          //$lienc = urlencode($lienc);
          if (strstr($adresse_http,"educagri.fr") && $nb_seq2 > 0 && !strstr($mod_parc,"SCORM") && $nb_sequence_type == 0 && (!isset($qcm_Ok) || (isset($qcm_Ok) && $qcm_Ok < 1)))
          {
             echo "<TD nowrap>$bouton_gauche<A href=\"trace.php?link=$lienb\" target='main'>$mess_exp_parc_struc</A>$bouton_droite</TD>";
             echo "<TD nowrap>$bouton_gauche<a href=\"trace.php?link=$lien\" target='main'>$mess_exp_parc</a>$bouton_droite</TD>";
             if ($nb_sequence_type == 0 || !isset($nb_sequence_type))
                echo "<TD nowrap>$bouton_gauche<a href=\"$lienc\">$mess_exparcSco</a>$bouton_droite</TD>";
       }
       echo "</TR></TABLE>";
    }
    echo "</TD></TR><TR height='20'><TD></TD></TR></TABLE>";
    echo fin_tableau($html);
} //Fin if ($consult == 1)

//Modification d'un parcours
if ($action_parc == 1)
{
  $mod_parc = GetDataField ($connect,"SELECT parcours_type_lb from parcours where parcours_cdn = $id_parc","parcours_type_lb");
  if ($gauche != 1){
  ?>
  <SCRIPT language=JavaScript>
    function checkForm(frm) {
      var ErrMsg = "<?php echo $mess_info_no;?>\n\n";
      var lenInit = ErrMsg.length;
      if (isEmpty(frm.nom)==true)
        ErrMsg += ' - <?php echo $mess_parc_tit;?>\n';
      <?php if ($choix_seq != 1 && $gauche != 1 && !strstr($mod_parc,"SCORM")){?>
      if (isEmpty(frm.description)==true)
        ErrMsg += ' - <?php echo $msq_desc;?>\n';
      if (isVide(frm.parc_type)==true)
        ErrMsg += ' - <?php echo $mess_presc_ok;?>\n';
      if (isVide(frm.droit_voir_parc1)==true)
        ErrMsg += ' - <?php echo $msq_droit_voir;?>\n';
      <?php }?>
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
  }
    if ($supp == 1)
    {
        $requete_grp = mysql_query ("select * from groupe_parcours order by gp_grp_no");
        $nb_grp_parc = mysql_num_rows($requete_grp);
        if ($nb_grp_parc > 0)
        {
                $gp=0;
                while ($gp < $nb_grp_parc)
                {
                  $id_grp = mysql_result($requete_grp,$gp,"gp_grp_no");
                  $nb_presc = mysql_num_rows(mysql_query("select * from prescription_$id_grp where presc_parc_no=$id_parc and presc_seq_no=$id_seq"));
                  $nb_presc_seq_parc += $nb_presc;
                $gp++;
                }
        }
        if ($nb_presc_seq_parc == 0)
        {
          $delete = mysql_query ("DELETE from sequence_parcours where seqparc_parc_no = $id_parc AND seqparc_seq_no = $id_seq");
          $update = mysql_query ("UPDATE scorm_module set mod_parc_no='-1' where mod_parc_no = $id_parc AND mod_seq_no = $id_seq");
        }
        $nb_seq_parc_query = mysql_query ("SELECT count(*) from sequence_parcours where seqparc_parc_no = $id_parc");
        $nb_seq = mysql_result ($nb_seq_parc_query,0);
     }
    $letitre= "$mess_menu_mon_parc : ".strtolower($mess_modif_base);
    entete_concept("liste_parc.inc.php",$letitre);
    echo aide_simple("module");
    $sql_parc = mysql_query("SELECT * from parcours where parcours_cdn = $id_parc");
    if ($sql_parc == TRUE)
    {
       $description = html_entity_decode(mysql_result ($sql_parc,0,"parcours_desc_cmt"),ENT_QUOTES,'iso-8859-1');
       $clef = html_entity_decode(mysql_result ($sql_parc,0,"parcours_mots_clef"),ENT_QUOTES,'iso-8859-1');
       $parc_type =  mysql_result ($sql_parc,0,"parcours_type_on");
       $mod_parc =  mysql_result ($sql_parc,0,"parcours_type_lb");
       $droit_voir_parc1 =  mysql_result ($sql_parc,0,"parcours_publique_on");
       $id_ref_parc =  mysql_result ($sql_parc,0,"parcours_referentiel_no");
       $nom =  mysql_result ($sql_parc,0,"parcours_nom_lb");
       $parc_auteur =  mysql_result ($sql_parc,0,"parcours_auteur_no");
    }
    $nom = stripslashes($nom);
    echo " <TR><TD colspan='2'><TABLE cellspacing='1' cellpadding='4' width='90%' border='0'>";
    if ($id_ref_parc == 0)
       $referentiel = strtolower($msq_sra);
    else
       $referentiel = GetDataField ($connect,"SELECT ref_nomabrege_lb from referentiel where ref_cdn = $id_ref_parc","ref_nomabrege_lb");
    $sous_titre = "<B>$msq_parc : </B>$nom<BR>";
    $sous_titre .= "<B>$msq_ref_ass : </B>$referentiel";
    if ($sous_titre != '')
       echo "<TR><TD colspan='2' class='sous_titre'>$sous_titre</TD></TR>";
    if ($gauche != 1)
      echo "<FORM name='form2' action=\"parcours.php?liste=$liste&consult=1&modif_parc=1&id_ref_parc=$id_ref_parc&parcours=$parcours&miens_parc=$miens_parc&miens=$miens&refer=$refer&parc=$parc&id_parc=$id_parc&proprio=$proprio&refer=$refer\" method='POST' target='main'>";
    if ($parc_type == 1 && $droit_voir_parc1)
      echo "<TR><TD nowrap width='35%'>&nbsp;</TD><TD>".$mess_parc_type." ".strtolower($mess_exp_type)."</TD></TR>";
    echo "<TR height='30'><TD width='35%'><B>$mess_parc_tit</B></TD><TD nowrap>";
    if ($gauche == 1)
       echo "$nom";
    else
       echo " <INPUT TYPE=TEXT class='INPUT' name='nom' value=\"$nom\" size='75' align=middle>";
    echo " </TD></TR>";
    echo "<TR><TD valign='top'><B>$msq_desc_seq</B></TD><TD>";
    if ($gauche == 1)
       echo "$description";
    else
       echo "<TEXTAREA class='TEXTAREA' name='description' align='middle' rows='6' cols='80'>$description</TEXTAREA>";
    echo "</TD></TR>";
/*
    echo " <TR><TD nowrap valign='top'><B>Mots-clef</B></TD><TD>";
    if ($gauche == 1)
       echo "$cles";
    else
       echo "<TEXTAREA class='TEXTAREA' name='clefs' align='middle' rows='5' cols='80'>$cles</TEXTAREA>";
    echo "</TD></TR>";
*/
    if (strstr($mod_parc,"SCORM"))
       echo "<TR><TD nowrap valign='top'><B>$mess_elt_sco</B></TD><TD>$mess_dupliparc_sco</TD></TR>";
    if ($choix_seq == 1 || $gauche == 1){
      echo "<TR><TD nowrap><B>$mess_presc_ok</B></TD><TD nowrap valign=top>";
      if ($parc_type == 1)
         echo $mess_non;
      elseif ($parc_type == 0)
         echo $mess_oui;
      echo "</TD></TR><TR height='25'><TD><B>$msq_droit_voir</B>&nbsp;&nbsp;&nbsp;</TD><TD nowrap>";
       if ($droit_voir_parc1 == 1 || $droit_voir_parc1 == "")
          echo $mess_oui;
       elseif ($droit_voir_parc1 == 0)
          echo $mess_non;
       echo "</TD></TR>";
    }
    else
    {
      echo "<TR><TD width=100%><TABLE width=100% border='0'>";
      echo "<TR height='25'><TD nowrap><B>$mess_presc_ok</B></TD></TR>";
      echo "<TR height='25'><TD nowrap><B>$msq_droit_voir</B>&nbsp;&nbsp;&nbsp;</TD></TR></TABLE></TD>";
      echo "<TD width=100%><TABLE cellpadding='0' cellspacing='0' border='0'><TR><TD nowrap valign=top>";
      if ($parc_type == 1)
      {
         echo "$mess_oui&nbsp;<INPUT type='radio' name='parc_type' value='0'>&nbsp;&nbsp;&nbsp;</TD><TD nowrap>";
         echo "$mess_non&nbsp;<INPUT type='radio' name='parc_type' value='1' checked>&nbsp;&nbsp;&nbsp;</TD><TD nowrap rowspan='2' valign='center'>";
      }
      elseif ($parc_type == 0)
      {
         echo "$mess_oui&nbsp;<INPUT type='radio' name='parc_type' value='0' checked>&nbsp;&nbsp;&nbsp;</TD><TD nowrap>";
         echo "$mess_non&nbsp;<INPUT type='radio' name='parc_type' value='1'>&nbsp;&nbsp;&nbsp;</TD><TD nowrap rowspan='2' valign='center'>";
      }
      echo anoter($mess_dupli_presc,"350");
         echo "</TD></TR><TR><TD nowrap valign=top>";
      if (strstr($mod_parc,"SCORM"))
      {
         if ($droit_voir_parc1 == 1)
         {
            echo "$mess_oui&nbsp;<INPUT type='radio' name='droit_voir_parc1' value='1' checked>&nbsp;&nbsp;&nbsp;</TD><TD>";
            echo "$mess_non&nbsp;&nbsp;&nbsp;<IMG SRC='images/gest_parc/unchecked.gif' border='0' ALT=\"$mess_elt_sco : $mess_not_param\">&nbsp;&nbsp;&nbsp;";
         }elseif ($droit_voir_parc1 == 0)
         {
            echo "$mess_oui&nbsp;&nbsp;&nbsp;<IMG SRC='images/gest_parc/unchecked.gif' border='0' ALT=\"$mess_elt_sco : $mess_not_param\">&nbsp;&nbsp;&nbsp;</TD><TD>";
            echo "$mess_non&nbsp;<INPUT type='radio' name='droit_voir_parc1' value='0' checked>&nbsp;&nbsp;&nbsp;";
         }
      }else{
         if ($droit_voir_parc1 == 1)
         {
            echo "$mess_oui&nbsp;<INPUT type='radio' name='droit_voir_parc1' value='1' checked>&nbsp;&nbsp;&nbsp;</TD><TD>";
            echo "$mess_non&nbsp;<INPUT type='radio' name='droit_voir_parc1' value='0'>";
         }
         elseif ($droit_voir_parc1 == 0)
         {
            echo "$mess_oui&nbsp;<INPUT type='radio' name='droit_voir_parc1' value='1'></TD><TD>";
            echo "$mess_non&nbsp;<INPUT type='radio' name='droit_voir_parc1' value='0' checked>&nbsp;&nbsp;&nbsp;";
         }
      }
      echo "</TD></TR></TABLE></TD></TR>";
    }
    $list_seq_query = mysql_query ("SELECT seq_cdn,seq_titre_lb,seq_type_lb from
                                   sequence,sequence_parcours where
                                   seqparc_parc_no = $id_parc and seqparc_seq_no = seq_cdn
                                   ORDER BY seqparc_ordre_no");
    $nb_seq_list = mysql_num_rows ($list_seq_query);
    //gestion forum module (forums transversaux)
    //----------------------------------------------------------
    if ($nb_seq_list > 0)
    {
        $strAjt = "$mess_creer&nbsp;<INPUT type='checkbox' name='cree_forum' value='1' ".
                  bulle($msgCreForMod,"","LEFT","ABOVE",200);
        $SQL = "select * from parcours_forums where parcforum_parc_no = $id_parc";
        $nb_pf = mysql_num_rows(mysql_query($SQL));
        echo "<TR height='30'><TD width='35%'>&nbsp;<B>$msg_formod</B></TD><TD nowrap valign='top'>";
        if ($nb_pf > 0)
        {
           if ($gauche == 1)
              echo "le forum a été créé.";
           else
           {
                $SQL = "select * from forums_modules where fm_module_no = $id_parc";
                $NbPosts = mysql_num_rows(mysql_query($SQL));
                if ($NbPosts > 0)
                {
                   echo "<IMG SRC=\"images/repertoire/icoptiinterdit.gif\" BORDER=0 style=\"cursor:help;\"".
                        bulle($msg_FMNosup,"","RIGHT","BELOW",180);
                }
                else
                {
                   $lien = "formation/supp_forum_parc.php?id_parc=$id_parc";
                   echo "<div id='modforum' style=\"cursor:pointer;font-weight:bold;\"> ".
                     "&nbsp;&nbsp;<IMG SRC='images/messagerie/icoGpoubel.gif' height='20' width='15' BORDER='0' title='$msg_SupForMod' ".
                     "onclick=\"javascript:appelle_ajax('$lien');".
                     "\$('#modforum').empty();\$('#modforum').addClass('individ1');\$('#modforum').html('$msg_ForSupOk');\">";
                }
                echo "<A href='forum_module.php?id_parc=$id_parc' ".
                      bulle("$NbPosts $mess_mess_mess2 : ".$mess_menu_consult_favori,"","RIGHT","BELOW",170).
                      "<IMG SRC=\"images/modules/visible.gif\" BORDER=0></a>";
           }
        }
        else
        {
            echo "<div id='forummod'>$strAjt";
        }
        echo "</div></TD></TR>";
    }
    //------------------------------------------------------------------
    if ($gauche != 1)
    {
       echo "<TR height=50><TD>&nbsp;</TD><TD align='left' valign='center'>"."
            <A HREF=\"javascript:checkForm(document.form2);\" ".
            "onClick=\"TinyMCE.prototype.triggerSave();\" ".
            "onmouseover=\"img1.src='images/fiche_identite/boutvalidb$suffixer.gif';return true;\" ".
            "onmouseout=\"img1.src='images/fiche_identite/boutvalid$suffixer.gif'\">".
            "<IMG NAME=\"img1\" SRC=\"images/fiche_identite/boutvalid$suffixer.gif\" BORDER='0' ".
            "onLoad=\"tempImg=new Image(0,0); tempImg.src='images/fiche_identite/boutvalidb$suffixer.gif'\"></A>";
       echo "</TD></TR></FORM>";
    }
    echo "<TR><TD valign='top' width='32%'><B>&nbsp;$mpa_lis_seq </B></TD><TD nowrap valign='top'>";
    //Affichage des sequences comprises dans le parcours
    if ($nb_seq_list == 0)
    {
       echo $msq_noseq_parc;
    }
    else
    {
       $i = 0;
       $requete_grp = mysql_query ("select * from groupe_parcours order by gp_grp_no");
       $nb_grp_parc = mysql_num_rows($requete_grp);
       while ($i != $nb_seq_list)
       {
             $titre = mysql_result ($list_seq_query,$i,"seq_titre_lb");
             $mod_seq = mysql_result ($list_seq_query,$i,"seq_type_lb");
             $id_seq = mysql_result ($list_seq_query,$i,"seq_cdn");
             if (strstr($mod_seq,"SCORM"))
                echo "<IMG SRC=\"images/gest_parc/scorm.gif\"  BORDER=0 ".
                     bulle($mess_elt_sco,"","LEFT","ABOVE",200)."</A>&nbsp;&nbsp;";
             echo $titre;
             if ($nb_grp_parc > 0)
             {
                $gp=0;$nb_presc_seq_parc=0;
                while ($gp < $nb_grp_parc)
                {
                  $id_grp = mysql_result($requete_grp,$gp,"gp_grp_no");
                  $nb_presc = mysql_num_rows(mysql_query("select * from prescription_$id_grp where presc_parc_no=$id_parc and presc_seq_no = $id_seq"));
                  $nb_presc_seq_parc += $nb_presc;
                 $gp++;
                }
            }
            if ($nb_presc_seq_parc == 0)
            {
                $lien = "parcours.php?liste=$liste&action_parc=1&supp=1&parcours=$parcours&id_parc=$id_parc&proprio=$proprio&refer=$refer&id_ref_parc=$id_ref_parc&id_seq=$id_seq&miens_parc=$miens_parc&refer=$refer&parc=$id_parc";
                $lien = urlencode($lien);
                echo "&nbsp;&nbsp;<a href=\"javascript:void(0);\" ".
                     "onclick=\"javascript:return(confm('trace.php?link=$lien'));\" target=\"main\" ".
                     bulle($mpa_supp_seq,"","LEFT","ABOVE",150).
                     "<IMG SRC=\"images/messagerie/icoGpoubel.gif\" height=\"20\" width=\"15\" BORDER=0></A><br>";
             }
             else
                echo "&nbsp;&nbsp;<IMG SRC=\"images/repertoire/icoptiinterdit.gif\" height=\"15\" width=\"15\" BORDER=0 ".
                     bulle($msq_nosup_seq,"","LEFT","ABOVE",200)."</A><br>";
             $i++;
       } //fin while
    }
    echo "</TD></TR>";
       echo "<TR><TD>&nbsp;</TD><TD colspan='2' align='middle'>".
            "<TABLE bgColor='#298CA0' cellspacing='0' cellpadding='1' width='100%'>";
       echo "<TR><TD width='100%'><TABLE bgColor='#F4F4F4' cellspacing='0' cellpadding='0' width='100%' border='0'>";
       echo "<TR><TD colspan=3 align='center' height='80px'>".
            "<TABLE cellspacing='0' cellpadding='3' width='100%' border='0'>".
            "<TR height='5'><TD colspan=2'></TD></TR>";
       $lien = "sequence.php?liste=$liste&choix_ref=1&parcours=1&id_parc=$id_parc&proprio=$proprio&refer=$refer&parc=$id_parc&id_ref_parc=$id_ref_parc&gauche=$gauche";
       $lien = urlencode($lien);
       echo "<TR><TD align='center' width='30%' valign='top'><A href=\"trace.php?link=$lien\" target='main'".
            " onmouseover=\"img110.src='images/modules/icoajoutsequenb.gif';return true;\"".
            " onmouseout=\"img110.src='images/modules/icoajoutsequen.gif'\">".
            "<IMG NAME=\"img110\" SRC=\"images/modules/icoajoutsequen.gif\"  border='0' width='30' height='30'".
            " onLoad=\"tempImg=new Image(0,0); tempImg.src='images/modules/icoajoutsequenb.gif'\"><BR>$msq_cree_seq</A></TD>";

       $lien = "parcours.php?rechercher=1&".str_replace("&insert_seq=1","",getenv("QUERY_STRING"));
       $lien = str_replace("&seq=","&noseq=",$lien);
       $lien = urlencode($lien);
       $nb_stars = mysql_num_rows(mysql_query("select * from stars where star_user_id='$id_user' and star_type_no=2"));
       if ($nb_stars > 0)
       {
          $lien_star = "parcours.php?rechercher=1&star=1&medor=1&".str_replace("&insert_seq=1","",getenv("QUERY_STRING"));
          $lien_star = str_replace("&seq=","&noseq=",$lien_star);
          $lien_star = urlencode($lien_star);
       }
       echo "<TD align='center' width='70%' valign='top'>";
       if ($rechercher == 1 || $medor == 1)
       {
          if ($nb_stars > 0 && $etat_fav == 'OUI' && $star !=1)
          {
              echo "<table cellspacing='0' cellpadding='0' width=100%><tr><td width=60% align='center'><a href=\"trace.php?link=$lien_star\" target='main'>".
                   " <img src='images/starfull.gif' border=0><br />&nbsp;<br />$mess_AjStar</a></td><td width=40% align='center' valign='top'>";
           }
           echo "<IMG SRC='images/modules/icorechsequenb.gif' border='0'><br />".
               "<FONT color='#D45211'><B>$mess_insert_seq</B></FONT>";
           if ($nb_stars > 0 &&  $star !=1)
              echo "</TD></TR></TABLE>";
           echo "</TD></TR></TABLE></TD></TR>";
       }
       else
       {
          if ($nb_stars > 0 && $etat_fav == 'OUI')
          {
              echo "<table cellspacing='0' cellpadding='0' width=100%><tr><td width=40% align='center' ><a href=\"trace.php?link=$lien_star\" target='main'>".
                   " <img src='images/starfull.gif' border=0><br />&nbsp;<br />$mess_AjStar</a></td><td width=60% align='center' valign='top' >";
           }
           echo "<A href=\"trace.php?link=$lien\" target='main'".
              " onmouseover=\"img111.src='images/modules/icorechsequenb.gif';return true;\"".
              " onmouseout=\"img111.src='images/modules/icorechsequen.gif'\">".
              "<IMG NAME=\"img111\" SRC=\"images/modules/icorechsequen.gif\"  border='0' width='30' height='30'".
              " onLoad=\"tempImg=new Image(0,0); tempImg.src='images/modules/icorechsequenb.gif'\">".
              "<br />$mess_insert_seq</A>";
           if ($nb_stars > 0)
              echo "</TD></TR></TABLE>";
           echo "</TD></TR></TABLE></TD></TR>";
         }
        echo "</TABLE></TD></TR></TABLE></TD></TR></TABLE></TD></TR>";
    if ($rechercher == 1 || $medor == 1)
    {
        $le_type = GetDataField ($connect,"select parcours_type_on from parcours where parcours_cdn = $id_parc","parcours_type_on");
        echo"<TR height='5'><TD colspan='2'>&nbsp;</TD></TR>";
      if ($medor == 1)
      {
        if ($keydesc != "" && $keytitre == "" && $keypub == "")
           $champ_rech = "(sequence.seq_desc_cmt like \"%$keydesc%\" OR sequence.seq_desc_cmt like \"$keydesc%\" OR sequence.seq_desc_cmt like \"%$keydesc\")";
        elseif ($keydesc == "" && $keytitre != "" && $keypub == "")
           $champ_rech = "(sequence.seq_titre_lb like \"%$keytitre%\" OR sequence.seq_titre_lb like \"$keytitre%\" OR sequence.seq_titre_lb like \"%$keytitre\")";
        elseif ($keydesc == "" && $keytitre == "" && $keypub != "")
           $champ_rech = "sequence.seq_publique_on = '$keypub'";
        elseif ($keydesc != "" && $keytitre != "" && $keypub == "")
           $champ_rech = "(sequence.seq_titre_lb like \"%$keytitre%\" OR sequence.seq_titre_lb like \"$keytitre%\" OR sequence.seq_titre_lb like \"%$keytitre\") AND (sequence.seq_desc_cmt like \"%$keydesc%\" OR sequence.seq_desc_cmt like \"$keydesc%\" OR sequence.seq_desc_cmt like \"%$keydesc\")";
        elseif ($keydesc == "" && $keytitre != "" && $keypub != "")
           $champ_rech = "sequence.seq_publique_on = '$keypub' AND (sequence.seq_titre_lb like \"%$keytitre%\" OR sequence.seq_titre_lb like \"$keytitre%\" OR sequence.seq_titre_lb like \"%$keytitre\")";
        elseif ($keydesc != "" && $keytitre == "" && $keypub != "")
           $champ_rech = "sequence.seq_publique_on = '$keypub' AND (sequence.seq_desc_cmt like \"%$keydesc%\" OR sequence.seq_desc_cmt like \"$keydesc%\" OR sequence.seq_desc_cmt like \"%$keydesc\")";
        elseif ($keydesc != "" && $keytitre != "" && $keypub != "")
           $champ_rech = "sequence.seq_publique_on = '$keypub' AND (sequence.seq_titre_lb like \"%$keytitre%\" OR sequence.seq_titre_lb like \"$keytitre%\" OR sequence.seq_titre_lb like \"%$keytitre\") AND (sequence.seq_desc_cmt like \"%$keydesc%\" OR sequence.seq_desc_cmt like \"$keydesc%\" OR sequence.seq_desc_cmt like \"%$keydesc\")";
        elseif ($keydesc == "" && $keytitre == "" && $keypub == "" && $type_lb == "" && $keypresc == "" && $star != 1){
           $ordre_affiche = "lenom";
           $refer = 2;
           $medor = 0;
        }
        if ($type_lb == "SCORM" && $champ_rech != "")
           $champ_rech .= " AND sequence.seq_type_lb != 'NORMAL'";
        elseif ($type_lb == "NORMAL" && $champ_rech != "")
           $champ_rech .= " AND sequence.seq_type_lb = 'NORMAL'";
        elseif ($type_lb == "SCORM" && $champ_rech == "")
           $champ_rech .= " sequence.seq_type_lb != 'NORMAL'";
        elseif ($type_lb == "NORMAL" && $champ_rech == "")
           $champ_rech .= " sequence.seq_type_lb = 'NORMAL'";
        if ($keypresc == 1 && $champ_rech == "")
           $champ_rech .= " seq_type_on = 1";
        elseif ($keypresc == 1 && $champ_rech != "")
           $champ_rech .= " AND seq_type_on = 1";
        if (isset($keypresc) && $keypresc != "" && $keypresc == 0 && $champ_rech == "")
           $champ_rech .= " seq_type_on = 0";
        elseif (isset($keypresc) && $keypresc != "" && $keypresc == 0 && $champ_rech != "")
           $champ_rech .= " AND seq_type_on = 0";
        if ($champ_rech != '')
        {
            $seq_query = mysql_query ("select * from sequence where $champ_rech order by seq_titre_lb asc");
            $nb_seq = mysql_num_rows($seq_query);
        }
        elseif($star == 1 && $etat_fav == 'OUI')
        {
          $seq_query = mysql_query ("SELECT * from sequence,stars where stars.star_item_id=sequence.seq_cdn and stars.star_user_id= $id_user and stars.star_type_no=2 order by seq_titre_lb, seq_type_lb asc");
          $nb_seq = mysql_num_rows($seq_query);
        }
        if ($nb_seq ==0)
          echo "<TR><TD colspan='2'><B>$mess_no_occur</B></TD></TR>";
        echo "<TR><TD colspan='2'><B>$mess_nbseq  :  $nb_seq &nbsp;&nbsp;&nbsp;</B>";
        if ($keytitre != "") echo "<B>$msq_titre : <font color='#D45211'>$keytitre</B></font> , ";//else echo "<B>$mess_nofiltre</B>, ";
        if ($keydesc != "") echo "<B>$msq_desc : <font color='#D45211'>$keydesc</B></font> , ";//else echo "<B>$mess_nofiltre</B>, ";
        if ($type_lb != "") echo "<B>$mess_type : <font color='#D45211'>$type_lb</B></font> , ";//else echo "<B>$mess_nofiltre</B>, ";
        if ($keypub == 1)
            echo "<B>$mess_visdup : <font color='#D45211'>$mess_oui</B></font> , ";
        elseif(isset($keypub) && $keypub == 0 && $keypub != "")
            echo "$mess_visdup : <font color='#D45211'><B>$mess_non</B></font> , ";
        if (isset($keypresc) && $keypresc != "" && $keypresc == 0)
            echo "<B>$mess_presc : <font color='#D45211'>$mess_oui</B></font> ";
        elseif( $keypresc == 1)
            echo "$mess_presc : <font color='#D45211'><B>$mess_non</B></font> ";
        echo "</TD></TR>";
       }
       echo "<TR><TD colspan='2'><table cellpadding='6' cellspacing='0' width='100%' border='0'>";
       echo "<FORM name='form5' ACTION=\"parcours.php?gauche=$gauche&action_parc=1&id_seq=$id_seq&id_ref=$id_ref&id_ref_parc=$id_ref_parc&parcours=$parcours&liste=$liste&id_parc=$id_parc&proprio=$proprio&refer=$refer&miens_parc=$miens_parc&vient_de_search=1\" METHOD='POST' target='main'>";
       echo "<INPUT TYPE='HIDDEN' NAME ='requete_parc' VALUE=\"$requete_parc\">";
       echo "<TR><TD nowrap colspan=6><B>$mrc_rech</B></TD></TR>";
       echo "<TD nowrap>$msq_titre</TD><TD nowrap>$msq_desc</TD><TD nowrap>$mess_type</TD>".
            "<TD nowrap>$mess_visdup</TD><TD nowrap>$mess_presc</TD><TD nowrap>&nbsp;</TD></TR>";
       echo "<TR>";
       echo "<TD nowrap><INPUT TYPE='text'  class='INPUT'  name='keytitre' size='20' align='middle'></TD>";
       echo "<TD nowrap><INPUT TYPE='text'  class='INPUT'  name='keydesc' size='20' align='middle'></TD>";
         echo "<TD nowrap><SELECT name='type_lb' class='SELECT'>";
         echo "<OPTION></OPTION>";
         echo "<OPTION value='NORMAL'>NORMAL</OPTION>";
         echo "<OPTION value='SCORM'>SCORM</OPTION>";
         echo "</SELECT></TD>";
         echo "<TD nowrap><SELECT name='keypub' class='SELECT'>";
         echo "<OPTION></OPTION>";
         echo "<OPTION value='1'>$mess_oui</OPTION>";
         echo "<OPTION value='0'>$mess_non</OPTION>";
         echo "</SELECT></TD>";
         echo "<TD nowrap><SELECT name='keypresc' class='SELECT'>";
         echo "<OPTION></OPTION>";
         echo "<OPTION value='0'>$mess_oui</OPTION>";
         echo "<OPTION value='1'>$mess_non</OPTION>";
         echo "</SELECT></TD>";
         if (isset($nb_seq_ins))
            echo "<INPUT TYPE='HIDDEN' name='nb_seq_ins' value='$nb_seq_ins'>";
       echo "<INPUT TYPE='HIDDEN' name='parcours' value='1'>";
       echo "<INPUT TYPE='HIDDEN' name='medor' value='1'>";
       echo "<TD align='center'><A HREF=\"javascript:document.form5.submit();\" ".
            "onmouseover=\"img1.src='images/fiche_identite/boutvalidb$suffixer.gif';return true;\" ".
            "onmouseout=\"img1.src='images/fiche_identite/boutvalid$suffixer.gif'\">".
            "<IMG NAME=\"img1\" SRC=\"images/fiche_identite/boutvalid$suffixer.gif\" BORDER='0' ".
            "onLoad=\"tempImg=new Image(0,0); tempImg.src='images/fiche_identite/boutvalidb$suffixer.gif'\"></A>";
       echo "</TD></form></TR></TABLE></TD></TR>";
       if ($medor == 1 && $nb_seq > 0)
       {
         echo "<TR><TD colspan='2'><table cellpadding='6' cellspacing='2' width='100%' border='0'>";
         echo "<TR bgcolor='#336666'>";
         echo "<TD height='20' align='left'><FONT COLOR=white><b>$msq_seq ";
         if ($star == 1) echo "$msgseq_mark";
         echo "</b></FONT></TD>";
         echo "<TD height='20' align='left'><FONT COLOR=white><b>$mrc_aut</b></FONT></TD>";
         echo "<TD height='20' align='left'><FONT COLOR=white><b>$msq_ref_ass</b></FONT></TD></TR>";
         $i = 0;
         while ($i < $nb_seq)
         {
           $id_sequence = mysql_result ($seq_query,$i,"seq_cdn");
           $nom = mysql_result ($seq_query,$i,"seq_titre_lb");
           $createur = mysql_result ($seq_query,$i,"seq_auteur_no");
           $mod_seq = mysql_result ($seq_query,$i,"seq_type_lb");
           $son_type = mysql_result ($seq_query,$i,"seq_type_on");
           $droit_voir_seq = mysql_result ($seq_query,$i,"seq_publique_on");
           if ($typ_user == 'FORMATEUR_REFERENT' && $droit_voir_seq == 0 && $createur != $id_user){
            $i++;
            continue;
           }
           if ($son_type == 1 && $droit_voir_seq == 1){
                 $div = "<DIV id='sequence'>";
                 $icono = "<IMG SRC='images/modules/icotype.gif' border='0'>";
           }elseif ($son_type == 1 && $droit_voir_seq == 0){
                 $i++;
                 continue;
           }else{
                 $div = "<DIV id='sequence'>";
                 $icono = "";
           }
           if (strstr($mod_seq,"SCORM") && $icono == ""){
              $div = "<DIV id='sequence'>";
              $icono .= "<IMG SRC='images/gest_parc/scorm.gif' border='0' alt=\"$mess_seq_sco\">";
           }elseif (strstr($mod_seq,"SCORM") && $icono != "")
              $icono .= "&nbsp<IMG SRC='images/gest_parc/scorm.gif' border='0' alt=\"$mess_seq_sco\">";
           $nom_createur = GetDataField ($connect,"select util_nom_lb from utilisateur where util_cdn = $createur","util_nom_lb");
           $prenom_createur = GetDataField ($connect,"select util_prenom_lb from utilisateur where util_cdn = $createur","util_prenom_lb");
           $req_parc = mysql_query ("SELECT parcours.parcours_nom_lb FROM parcours,sequence_parcours WHERE sequence_parcours.seqparc_seq_no=$id_sequence AND sequence_parcours.seqparc_parc_no=parcours.parcours_cdn group by parcours.parcours_nom_lb order by parcours.parcours_type_lb, parcours.parcours_nom_lb");
           $nb_parc_seq = mysql_num_rows ($req_parc);
           $la_serie = "";
           if ($nb_parc_seq > 0){
              $psi=0;
              while ($psi < $nb_parc_seq){
               $p_nom = mysql_result($req_parc,$psi,"parcours_nom_lb");
               $la_serie .="<LI>".$p_nom."</LI>";
               $psi++;
              }
           }
           $seq_ref_query = mysql_query ("select seqref_referentiel_no from sequence_referentiel where seqref_seq_no = $id_sequence");
           $nb_seq_ref = mysql_num_rows ($seq_ref_query);
           if ($nb_seq_ref == 1){
             $id_ref = GetDataField ($connect,"select seqref_referentiel_no from sequence_referentiel where seqref_seq_no = $id_sequence","seqref_referentiel_no");
             if ($id_ref == 0)
               $nom_referentiel = $msq_sl;
             else
               $nom_referentiel = GetDataField ($connect,"select ref_nomabrege_lb from referentiel where ref_cdn = $id_ref","ref_nomabrege_lb");
             $lien="parcours.php?liste=$liste&action_parc=1&insert_seq=1&id_ref=$id_ref&id_ref_parc=$id_ref_parc&parcours=$parcours&id_parc=$id_parc&proprio=$proprio&refer=$refer&seq=$id_sequence&son_type=$son_type&le_type=$parc_type&miens_parc=$miens_parc&refer=$refer&parc=$id_parc";
             $lien = urlencode($lien);
             echo couleur_tr($i+1,'');
             echo "<TD nowrap>$div";
             if ($droit_voir_seq == 0)
                echo "<IMG SRC=\"images/modules/tut_form/icocadenas.gif\" border='0'>";
             if ($createur == $id_user)
                echo "<IMG SRC=\"images/gest_parc/icofeuilb.gif\" border='0'>";
             echo "&nbsp;<A HREF=\"trace.php?link=$lien\"";
             if ($la_serie != "")
               echo " onMouseOver=\"overlib('<TABLE><TR><TD width=5></TD><TD>".addslashes($la_serie)."</TD></TR></TABLE>',".
                    "ol_hpos,RIGHT,WIDTH,'330',DELAY,'800',CAPTION,'<TABLE><TR><TD width=5></TD>".
                    "<TD align=left><FONT SIZE=2 color =#333333><B>".addslashes($seq_parc_exist).
                    "</B></FONT><BR></TD</TR></TABLE>')\" onMouseOut=\"nd()\"";
             else
               echo " onMouseOver=\"overlib('<TABLE><TR><TD width=5></TD><TD>".addslashes($mess_into_seqmod).
                    "</TD></TR></TABLE>',ol_hpos,RIGHT,WIDTH,'250',DELAY,'800')\" onMouseOut=\"nd()\"";
             echo " target='main'>$icono $nom</a></DIV></TD>";
             if (($miens_parc != 1 || ($miens_parc == 1 && $vient_de_search == 1)) && $proprio == ""){
               $lien="parcours.php?utilisateur=$utilisateur&action_parc=1&id_parc=$id_parc&proprio=$proprio&refer=$refer&ordre_affiche=lenom&vient_de_search=$vient_de_search&miens_parc=$miens_parc&refer=$refer&parc=$id_parc";
               $lien = urlencode($lien);
               echo "<TD nowrap><DIV id='sequence'><A HREF=\"trace.php?link=$lien\" ".
                    "TITLE = \"$msq_consult_seq $de $prenom_createur $nom_createur\">$prenom_createur $nom_createur</A></DIV></TD>";
             }
             if ($refer > 0  || $miens_parc == 1 || $proprio > 0 || $medor == 1)
                echo "<TD>".html_entity_decode($nom_referentiel,ENT_QUOTES,'ISO-8859-1')."</TD>";
             echo "</TR>";
           }else{
             $nbr_ref = 0;
             while ($nbr_ref <$nb_seq_ref){
               $id_ref = mysql_result ($seq_ref_query,$nbr_ref,"seqref_referentiel_no");
               if ($id_ref == 0)
                 $nom_referentiel = $msq_sl;
               else
                 $nom_referentiel = GetDataField ($connect,"select ref_nomabrege_lb from referentiel where ref_cdn = $id_ref","ref_nomabrege_lb");
               $lien="parcours.php?liste=$liste&consult=1&modif_parc=1&id_ref_parc=$id_ref_parc&parcours=$parcours&id_parc=$id_parc&proprio=$proprio&refer=$refer&miens_parc=$miens_parc&refer=$refer&droit_voir=$droit_voir&parc=$id_parc";
               $lien = urlencode($lien);
               echo couleur_tr($i+1,'');
               echo "<TD nowrap>$div";
               if ($droit_voir_seq == 0)
                 echo "<IMG SRC=\"images/modules/tut_form/icocadenas.gif\" border='0'>";
               if ($createur == $id_user)
                 echo "<IMG SRC=\"images/gest_parc/icofeuilb.gif\" border='0'>";
               echo "&nbsp;<A HREF=\"trace.php?link=$lien\"";
               if ($la_serie != "")
                 echo " onMouseOver=\"overlib('<B>".addslashes($seq_parc_exist)."</B> : ".
                      addslashes($la_serie)."',ol_hpos,RIGHT,DELAY,'800',WIDTH,'310',CAPTION,'<center>$msq_desc_titre</center>')\"".
                      " onMouseOut=\"nd()\"";
               echo " target='main'>$nom</a></DIV></TD>";
               if ($proprio == "" && $miens_parc != 1)
                 echo "<TD nowrap>".$prenom_createur." ".$nom_createur."</TD>";
               echo "<TD>".html_entity_decode($nom_referentiel,ENT_QUOTES,'ISO-8859-1')."</TD>";
               echo "</TR>";
              $nbr_ref++;
             }
           }
         $i++;
         }
       echo"</TABLE></TD</TR>";
       }
     }// fin rechercher == 1
     echo "</TABLE>";
    echo fin_tableau($html);
} //fin if ($action_parc == 1)
//include ("click_droit.txt");
echo "<div id='mien' class='cms'></div>
</body></html>";
?>
