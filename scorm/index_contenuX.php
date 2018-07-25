<?php
session_start();
header("Access-Control-Allow-Origin: * ");
if ($lg == ""){
  include ('../deconnexion-fr.txt');
  exit();
}
require '../fonction.inc';
require '../admin.inc';
require "../lang$lg.inc";
//include ("click_droit.txt");
dbConnect();
include ('style_blanc.inc');
if ($cours == 1){
$Ext = '_'.$numero_groupe;
   ?>
   <SCRIPT language=javascript>
        function Quitter() {
           Recharger();
        }
        function Quit() {
           Recharger();
//           top.close();
        }
        function Recharger() {
             parent.frames['lanceur'].location.href= "vide.php?titre=<?php echo $msq_tit_label;?>&contenu=<?php echo $mess_quitAct;?>";
        }
   </SCRIPT>
   <?php
}

if ($ecran_new != ""){
   session_unregister('ecran');
   $ecran = $ecran_new;
   session_register('ecran');
}
if ($ecran == "" || $ecran == "normal"){
   $img_ecran = "<IMG SRC='flch_g.gif' border=0 ALT=\"$mess_agecr\">";
   $ecran1 = 'large';
}else{
   $img_ecran1 = "<IMG SRC='flch_d.gif' border=0 ALT=\"$mess_affmenu\">";
   $ecran1 = 'normal';
}
?>
<SCRIPT LANGUAGE="JavaScript">
  function Frame_ouvrir(){
        window.parent.document.body.cols="0%,28%,72%";
  }
  function Frame_fermer(){
        window.parent.document.body.cols="0%,2%,98%";
  }
</SCRIPT>
<?php

$date_op = date("Y-m-d H:i:s");
$heure_fiche = substr($date_op,11);
$date_fiche = substr($date_op,0,10);
$type_sequence = getdatafield ($connect,"SELECT seq_type_lb from sequence where seq_cdn = $id_seq","seq_type_lb");
if ($cours == 1){
    $id_app = ($utilisateur > 0)? $utilisateur : $id_user;
    if ($id_parc > 0){
      $action_fiche = $mess_suivi_titre;
      $nom_user = GetDataField ($connect,"select util_nom_lb from utilisateur where util_cdn = $id_user","util_nom_lb");
      $prenom_user = GetDataField ($connect,"select util_prenom_lb from utilisateur where util_cdn = $id_user","util_prenom_lb");
      $qualite = "Apprenant";
    }
    $commentaire = $mess_lanc_act;
    $new_fiche = Donne_ID ($connect,"select max(fiche_cdn) from fiche_suivi");
    $req_fiche = mysql_query("INSERT INTO fiche_suivi
                              (fiche_cdn,fiche_utilisateur_no,fiche_auteur_no,fiche_qualite_lb,
                              fiche_date_dt,fiche_heure_dt,fiche_commentaire_cmt,fiche_grp_no,
                              fiche_parc_no,fiche_seq_no,fiche_act_no,fiche_typaction_lb) VALUES
                              ($new_fiche,$id_app,$id_user,'$qualite','$date_fiche','$heure_fiche',
                              \"$commentaire\",$numero_groupe,$id_parc,$id_seq,$scormid,\"$action_fiche\")");
    $requete_seq = mysql_query("UPDATE suivi2$Ext set suiv2_etat_lb ='EN COURS' WHERE
                                suiv2_seq_no = '$id_seq' AND
                                suiv2_utilisateur_no = '$id_user' AND
                                suiv2_etat_lb !='TERMINE'");

   $scorm_actuel = $scormid;
   if ($ecran == '' || !isset($ecran) || $ecran == 'normal')
      echo "<A HREF=\"javascript:parent.index_contenu.location.href='index_contenuX.php?id_parc=$id_parc&id_seq=$id_seq&scormid=$scormid&cours=1&ecran_new=large&actuelle=$actuelle'\" onclick=\"javascript:Frame_fermer();\"  title=\"$mess_agecr\">$img_ecran</A><BR>";
   else
      echo "<A HREF=\"javascript:parent.index_contenu.location.href='index_contenuX.php?id_parc=$id_parc&id_seq=$id_seq&scormid=$scormid&cours=1&ecran_new=normal&actuelle=$actuelle'\" onclick=\"javascript:Frame_ouvrir();\" title=\"$mess_affmenu\">$img_ecran1</A><BR>";
}
//$largeur = ($cours == 1) ? '70%': '100%';
$affiche_entete.= "<CENTER><TABLE bgColor='#298CA0' cellspacing='2' cellpadding='0' width=100%><TR><TD>";
$affiche_entete.= "<TABLE bgColor='#FFFFFF' cellspacing='1' cellpadding='0' width='100%'>";
$affiche_entete.= "<TR><TD background=\"../images/fond_titre_table.jpg\" colspan='3' height='34' align='center' valign='center'><Font size='3' color='#FFFFFF'><B>Votre cours</B></FONT>";
$affiche_entete.= "</TD></TR>";
$req_seq = requete_order("*","scorm_module"," mod_seq_no = '$id_seq'","mod_cdn ASC");
if (!isset($id_parc))
   $id_parc = GetDataField ($connect,"select seqparc_parc_no from sequence_parcours where seqparc_seq_no = $id_seq","seqparc_parc_no");
$req_seq = requete_order("*","scorm_module"," mod_seq_no = '$id_seq' AND mod_visible = 'TRUE'","mod_cdn ASC");
if ($req_seq == false){
  echo "Il n'y a aucun résultat pour cette requete";
  exit;
}else{
  $nb_mod = mysql_num_rows($req_seq);
  if ($cours == 1 && $nb_mod > 5){
     $affiche_entete.= "<TR height='50'><TD nowrap align='center' valign='center'>$bouton_gauche<A href=\"javascript:Quitter();\">$mess_gen_valider</A>$bouton_droite</TD>";
     $affiche_entete.= "<TD nowrap align='center' valign='center'>$bouton_gauche<A href=\"javascript:Quit();\">$mess_menu_quit</A>$bouton_droite</TD><TD valign='center'>";
  }elseif ($cours == 1 && $nb_mod < 6)
     $affiche_entete.= "<TR height='30'><TD width='30%'>&nbsp;</TD><TD valign='center' width='30%'>&nbsp</TD><TD valign='center' width='30%'>";
  elseif ($typ_user != 'APPRENANT')
     $affiche_entete.= "<TR height='30'><TD>&nbsp;</TD></TR>";
}
if (!isset($id_parc))
   $id_parc = GetDataField ($connect,"select seqparc_parc_no from sequence_parcours where seqparc_seq_no = $id_seq","seqparc_parc_no");

$affiche_index.= "<TR><TD width=100% colspan='3'>";
$ordrepere = GetDataField ($connect,"
                               SELECT mod_pere_lb
                               FROM scorm_module
                               WHERE mod_cdn = '$scormid' AND mod_seq_no ='$id_seq'","mod_pere_lb");
$req_seq1 = requete_order("mod_cdn","scorm_module"," mod_pere_lb = \"$ordrepere\" AND mod_seq_no ='$id_seq'","mod_cdn ASC");
if ($req_seq1 == TRUE){
     $scopere = GetDataField ($connect,"
                               SELECT mod_cdn
                               FROM scorm_module
                               WHERE mod_numero_lb = \"$ordrepere\" AND mod_content_type_lb='LABEL' AND mod_seq_no ='$id_seq'","mod_cdn");

     $i=0;$j=0;
     while ($item = mysql_fetch_object($req_seq1)) {
        $i++;
        $scoid = $item->mod_cdn;
        $scoOk = GetDataField ($connect,"
                               SELECT lesson_status
                               FROM scorm_util_module$Ext
                               WHERE mod_module_no = '$scoid' AND
                               user_module_no = '$id_user'","lesson_status");
        if ($scoOk == "COMPLETED" || $scoOk == "PASSED")
           $j++;
     }
     if ($j == $i){
        $sql2 = "UPDATE scorm_util_module$Ext SET
                `lesson_status` = 'COMPLETED',
                `entry` = 'RESUME',
                `credit` = 'CREDIT'
               WHERE `user_module_no` = '$id_user'
                 AND `mod_module_no` ='$scopere'";
        $requete = mysql_query($sql2);
        if ($cours == 1)
           $requete_seq = mysql_query("UPDATE suivi2$Ext set suiv2_etat_lb ='TERMINE' WHERE suiv2_seq_no = '$id_seq' AND suiv2_utilisateur_no = '$id_user'");
     }
}
$agent=getenv("HTTP_USER_AGENT");
$sco[]=array();
$nb_items = mysql_num_rows($req_seq);
if ($nb_items > 1)
   $affiche_index .="\n<ul class=\"myTree\"><li class=\"treeItem\"> \n";
$i=0;
while ($i < $nb_items) {
      $sco['id_mod'][$i] = mysql_result($req_seq,$i,'mod_cdn');
      $scormid = $sco['id_mod'][$i];
      $sco['titre_mod'][$i] = trim(mysql_result($req_seq,$i,'mod_titre_lb'));
      $sco['type_mod'][$i] = mysql_result($req_seq,$i,'mod_content_type_lb');
      $sco['visible'][$i] = mysql_result($req_seq,$i,'mod_visible');
      $sco['url_mod'][$i] = mysql_result($req_seq,$i,'mod_launch_lb');
      $sco['ordre_mod'][$i] = mysql_result($req_seq,$i,'mod_numero_lb');
      $sco['mod_ordre'][$i] = mysql_result($req_seq,$i,'mod_ordre_no') ;
      $sco['parent_mod'][$i] = mysql_result($req_seq,$i,'mod_pere_lb');
      $sco['niveau_mod'][$i] = mysql_result($req_seq,$i,'mod_niveau_no');
      $sco['datafromlms'][$i] = mysql_result($req_seq,$i,'mod_datafromlms');
      $sco['prereq_mod'][$i] = mysql_result($req_seq,$i,'mod_prereq_lb');
  $i++;
}
$i=0;
while ($i < $nb_items) {
      $launcher='';$ajoutULD='';$ajoutULF = "";$nb_enfants='';
      unset($etat_prereq); unset($code_prereq);unset($titre_prereq);unset($noLink);
   if ($cours == 1){
      if ($sco['prereq_mod'][$i] != ""){
         $code_prereq = GetDataField ($connect,"select mod_cdn from scorm_module where mod_numero_lb = \"".$sco['prereq_mod'][$i]."\" AND mod_seq_no = '$id_seq'","mod_cdn");
         if ($code_prereq > 0){
            $etat_prereq = GetDataField ($connect,"select lesson_status from scorm_util_module$Ext where
                                                   mod_module_no = '$code_prereq' AND
                                                   user_module_no='$id_app'","lesson_status");
            $titre_prereq = GetDataField ($connect,"select mod_titre_lb from scorm_module where mod_cdn = '$code_prereq'","mod_titre_lb");
            if ($etat_prereq == "NOT ATTEMPTED" || $etat_prereq == "INCOMPLETE" || $etat_prereq == "BROWSED")
               $noLink = 1;
         }
      }
      $etat = GetDataField ($connect,"select lesson_status from scorm_util_module$Ext where
                                      mod_module_no = '".$sco['id_mod'][$i]."' AND
                                      user_module_no='$id_app'","lesson_status");
      if ($etat == "COMPLETED" || $etat == "PASSED") $etat_img = "<IMG SRC=\"../images/media/completed.gif\" border=0 ALT = \"$mess_fait\">";
      if ($etat == "NOT ATTEMPTED") $etat_img = "<IMG SRC=\"../images/media/notattempted.gif\" border=0 ALT = \"$mess_lanc_afaire\">";
      if ($etat == "FAILED") $etat_img = "<IMG SRC=\"../images/media/failed.gif\" border=0 ALT = \"$mess_echec\">";
      if ($etat == "INCOMPLETE") $etat_img = "<IMG SRC=\"../images/media/incomplete.gif\" border=0 ALT = \"$mess_nt\">";
      if ($etat == "BROWSED") $etat_img = "<IMG SRC=\"../images/media/browsed.gif\" border=0 ALT = \"$mess_vu\">";
   }
      for ($aa=1;$aa < $sco['niveau_mod'][$i];$aa++){
        $sco['espacer'][$i] .= "&nbsp;&nbsp;&nbsp;&nbsp;";
      }
      $retrait =$sco['espacer'][$i];

      $Gparent='';$GpereBis ='';
      $nb_enfants = mysql_num_rows(mysql_query("SELECT mod_cdn FROM scorm_module WHERE mod_pere_lb = \"".$sco['ordre_mod'][$i]."\" AND mod_seq_no = '$id_seq'"));
      if (($i+1) < $nb_items)
         $prochain_niveau = $sco['niveau_mod'][$i+1];
      if (($i+2) < $nb_items)
         $niveau_suivant = $sco['niveau_mod'][$i+2];
      if ($nb_enfants > 0)
          $ajoutULD = "\n<ul style=\"display: block;\">\n<li class=\"treeItem\">\n";
      elseif($nb_enfants == 0)
          $ajoutULD = "\n</li><li class=\"treeItem\">\n";
      if ($prochain_niveau+1 == $sco['niveau_mod'][$i] && ($i+1) < $nb_items)
          $ajoutULD .= "\n</li>\n</ul>\n<li class=\"treeItem\">\n";
//      elseif (($i+1) == $nb_items)
//          $ajoutULD = "\n</li>\n";
      elseif ($prochain_niveau+2 == $sco['niveau_mod'][$i] && ($i+2) < $nb_items)
          $ajoutULF = "\n</li>\n</ul>\n</li>\n<li class=\"treeItem\">\n";
      elseif ($prochain_niveau+3 == $sco['niveau_mod'][$i] && ($i+3) < $nb_items)
          $ajoutULF = "\n</li>\n</ul>\n</li>\n</ul>\n</li>\n<li class=\"treeItem\">\n";
      elseif($prochain_niveau == $sco['niveau_mod'][$i])
         $ajoutULF = "\n";
      elseif($prochain_niveau > $sco['niveau_mod'][$i])
          $ajoutULF = "";
      elseif($prochain_niveau < $sco['niveau_mod'][$i])
          $ajoutULF = "\n</ul>\n</li>\n<li class=\"treeItem\">\n";
      if (isset($id_dvlp)){
         $Gparent  = GetDataField ($connect,"select mod_pere_lb from scorm_module where mod_numero_lb = \"$ordre\" AND mod_seq_no ='$id_seq'","mod_pere_lb");
         if ($sco['niveau_mod'][$i] > 2)
            $GpereBis = GetDataField ($connect,"select mod_pere_lb from scorm_module where mod_numero_lb=\"".$sco['parent_mod'][$i]."\" AND mod_seq_no ='$id_seq'","mod_pere_lb");
      }
      $div = ($actuelle == $sco['id_mod'][$i]) ? "<span class='scoseq'>" : "<span class='seqsco'>";
      $limite = ($cours == 1)? 30 : 34;
      $limite1 = ($limite == 30)? 28 : 32;
      if (strlen($sco['titre_mod'][$i]) > $limite){
        $nom1 = substr($sco['titre_mod'][$i],0,$limite1)."..";
        $alter = $sco['titre_mod'][$i];
      }else{
        $nom1 = $sco['titre_mod'][$i];
        $alter= "";
      }
      if (!strstr($sco['url_mod'][$i],"http://") && $sco['url_mod'][$i] != '')
        $launcher = "../".$sco['url_mod'][$i];
      elseif (strstr($sco['url_mod'][$i],"http://"))
        $launcher = $sco['url_mod'][$i];
      if ($sco['datafromlms'][$i] != '')
         $launcher .="?".$sco['datafromlms'][$i];
      if ($cours == 1 && $launcher != "" && $noLink != 1 && !$utilisateur){
         $scormid = $sco['id_mod'][$i];
         $lien_index = urlencode("index_contenuX.php?id_parc=$id_parc&id_seq=$id_seq&cours=$cours&scormid=$scormid&actuelle=$scormid");
         $affiche_index .= "$ajoutULF $retrait $div<A HREF=\"javascript: void(0);\" ".
                "onClick=\"parent.location.replace('lancer_scoX.php?lien=$launcher&lien_index=$lien_index&cours=$cours&id_seq=$id_seq&id_parc=$id_parc&scormid=$scormid&actuelle=$scormid&id_util=$id_util);\" title=\"$alter\">".
                $etat_img."&nbsp;".$nom1."</A></span>$ajoutULD";
      }elseif ($cours == 1 && (($launcher == "" && $noLink != 1) || $utilisateur > 0))
         $affiche_index .= "$ajoutULF $retrait $div".$etat_img."&nbsp;"."<B><acronym title=\"".$sco['titre_mod'][$i]."\">".$nom1."</acronym></B>$ajoutULD";
      elseif ($cours == 1 && $noLink == 1)
         $affiche_index .= "$ajoutULF $retrait $div".$etat_img."&nbsp;"."<B>&nbsp;<acronym title=\"$msq_act_prereq : $titre_prereq\">".$nom1."</acronym></B>$ajoutULD";
      elseif ($cours != 1 || !isset($cours)){
        if ($sco['parent_mod'][$i] != $ordre || ($sco['parent_mod'][$i] == $ordre && (($id_dvlp == $sco['id_mod'][$i] && $dvlp == 1) || !isset($id_dvlp) || $dvlp == 0 ))){
          if ($sco['url_mod'][$i] != '' && $actuelle != $sco['id_mod'][$i])
            $affiche_index .= "$ajoutULF $retrait $div<A HREF=\"$launcher\" title = \"".$sco['titre_mod'][$i]."\" target='contenu' ".
                              "onclick = \"javascript:document.location.replace('index_contenuX.php?id_seq=$id_seq&id_parc=$id_parc&actuelle=".
                              $sco['id_mod'][$i]."&dvlp=$dvlp&id_dvlp=$id_dvlp&ordre=$ordre');\">$nom1</A></span>$ajoutULD ";
          elseif ($sco['url_mod'][$i] != '' && $actuelle == $sco['id_mod'][$i])
            $affiche_index .= "$ajoutULF $retrait $div $nom1<A HREF=\"javascript:void(0);\"></A></span>$ajoutULD";
          else{
            if (!isset($id_dvlp) || ($dvlp == 0 && $id_dvlp == $sco['id_mod'][$i]) || $id_dvlp != $sco['id_mod'][$i])
              $affiche_index .= "$ajoutULF $retrait <B>&nbsp;<acronym title=\"".$sco['titre_mod'][$i]."\">".$nom1."</acronym></B>$ajoutULD ";
            elseif ($dvlp == 1 && $id_dvlp == $sco['id_mod'][$i])
              $affiche_index .= "$ajoutULF".$retrait."<B>&nbsp;<acronym title=\"".$sco['titre_mod'][$i]."\">".$nom1."</acronym></B>$ajoutULD ";
          }
        }
      }
  $i++;
}
if ($nb_items > 1)
   if ($sco['niveau_mod'][$nb_items-1] > 1){
      for ($j=1;$j < $sco['niveau_mod'][$nb_items-1];$j++){
           $affiche_index .= "\n</li>\n</ul>\n";
      }
   }
    if ($cours == 1){
      // Gestion des Fleches de Navigation
      $req_nav = mysql_query("SELECT mod_cdn,mod_launch_lb,mod_prereq_lb,mod_datafromlms FROM scorm_module WHERE mod_launch_lb != '' AND mod_seq_no = '$id_seq' ORDER by mod_cdn");
      $nbr_sconav = mysql_num_rows($req_nav);
         $i=0;
         while ($i < $nbr_sconav) {
           $affiche_suivant = 0;
           $sconavid = mysql_result($req_nav,$i,"mod_cdn");
           if ($i < $nbr_sconav-1){
               $sconavprereq = mysql_result($req_nav,$i+1,"mod_prereq_lb");
               if ($sconavprereq != ""){
                  $code_prereq = GetDataField ($connect,"select mod_cdn from scorm_module where mod_numero_lb = \"".$sconavprereq."\" AND mod_seq_no = '$id_seq'","mod_cdn");
                  if ($code_prereq > 0){
                     $etat_prereq = GetDataField ($connect,"select lesson_status from scorm_util_module$Ext where mod_module_no = '$code_prereq' AND user_module_no='$id_app'","lesson_status");
                     $titre_prereq = GetDataField ($connect,"select mod_titre_lb from scorm_module where mod_cdn = '$code_prereq'","mod_titre_lb");
                     if ($etat_prereq == "NOT ATTEMPTED" || $etat_prereq == "INCOMPLETE" || $etat_prereq == "BROWSED")
                        $affiche_suivant = 1;
                  }
               }
           }
           if ($scorm_actuel == $sconavid && $i < $nbr_sconav-1 && $affiche_suivant == 0){
              $ScoSuiv = mysql_result($req_nav,$i+1,"mod_cdn");
              $Suiv_lch = mysql_result($req_nav,$i+1,"mod_launch_lb");
              $Suiv_DFLMS = mysql_result($req_nav,$i+1,"mod_datafromlms");
              if (!strstr($Suiv_lch,"http://"))
                 $Suiv_lch = "../".$Suiv_lch;
              elseif (strstr($Suiv_lch,"http://"))
                 $Suiv_lch = $Suiv_lch;
              if ($Suiv_DFLMS != '')
                 $Suiv_lch .="?".$Suiv_DFLMS;
           }
           if ($scorm_actuel == $sconavid && $i > 0){
              $ScoPrec = mysql_result($req_nav,$i-1,"mod_cdn");
              $Prec_lch = mysql_result($req_nav,$i-1,"mod_launch_lb");
              $Prec_DFLMS = mysql_result($req_nav,$i-1,"mod_datafromlms");
              if (!strstr($Prec_lch,"http://"))
                 $Prec_lch = "../".$Prec_lch;
              elseif (strstr($Suiv_lch,"http://"))
                 $Prec_lch = $Prec_lch;
              if ($Prec_DFLMS != '')
                 $Prec_lch .="?".$Prec_DFLMS;
           }
          $i++;
         }
         if ($ScoPrec != ''){
            $lien_index = urlencode("index_contenuX.php?id_parc=$id_parc&id_seq=$id_seq&cours=$cours&scormid=$ScoPrec&actuelle=$ScoPrec");
            $navigation .= "<A HREF=\"javascript:void(0);\" ".
                "onClick=\"javascript:parent.location.replace('lancer_scoX.php?lien=$Prec_lch&lien_index=$lien_index&cours=$cours&id_seq=$id_seq&id_parc=$id_parc&scormid=$ScoPrec&actuelle=$ScoPrec'');\" title=\"$mess_prec\">".
                "<IMG SRC='../images/precedent.gif' border='0' Alt=\"$mess_prec\"></A>";
         }
         if ($ScoSuiv != ''){
            $lien_index = urlencode("index_contenuX.php?id_parc=$id_parc&id_seq=$id_seq&cours=$cours&scormid=$ScoSuiv&actuelle=$ScoSuiv");
            $navigation .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<A HREF=\"javascript:void(0);\" ".
                "onClick=\"javascript:parent.location.replace('lancer_scoX.php?lien=$Suiv_lch&lien_index=$lien_index&cours=$cours&id_seq=$id_seq&id_parc=$id_parc&scormid=$ScoSuiv&actuelle=$ScoSuiv');\" title=\"$mess_suiv\">".
                "<IMG SRC='../images/suivant.gif' border='0' Alt=\"$mess_suiv\"></A>";
         }
    }
if ($ecran == "" || $ecran == "normal"){
   echo $affiche_entete;
   if ($cours == 1){
      $navigation .= "</TD></TR>";
      echo $navigation;
   }
   echo $affiche_index."</TD></TR>";
   if ($cours == 1){
     echo "<TR height='50'><TD nowrap align='center' valign='bottom'>$bouton_gauche<A href=\"javascript:Quitter();\">$mess_gen_valider</A>$bouton_droite</TD>";
     echo "<TD nowrap align='center' valign='bottom'>$bouton_gauche<A href=\"javascript:Quit();\">$mess_menu_quit</A>$bouton_droite</TD><TD valign='bottom' nowrap>";
     echo $navigation;
   }
   echo "</TABLE></TD></TR></TABLE>";
}
?>
<script type="text/javascript">
$(document).ready(
        function()
        {
                tree = $('#myTree');
                $('li', tree.get(0)).each(
                        function()
                        {
                                subbranch = $('ul', this);
                                if (subbranch.size() > 0) {
                                        if (subbranch.eq(0).css('display') == 'none') {
                                                $(this).prepend('<img src="../images/plus.gif"  class="expandImage" />');
                                        } else {
                                                $(this).prepend('<img src="../images/moins.gif" class="expandImage" />');
                                        }
                                } else {
                                        $(this).prepend('<img src="spacer.gif" class="expandImage" />');
                                }
                        }
                );
                $('img.expandImage', tree.get(0)).click(
                        function()
                        {
                                if (this.src.indexOf('spacer') == -1) {
                                        subbranch = $('ul', this.parentNode).eq(0);
                                        if (subbranch.css('display') == 'none') {
                                                subbranch.show();
                                                this.src = '../images/moins.gif';
                                        } else {
                                                subbranch.hide();
                                                this.src = '../images/plus.gif';
                                        }
                                }
                        }
                );
        }
);
</script>
</body>
</html>
