<?php
if (!isset($_SESSION)) session_start();
if (!isset($_SESSION['id_user']) || $_SESSION['id_user'] == "")
{
  exit();
}
if (isset($_SESSION['acces']) && $_SESSION['acces'] !='')
  unset($_SESSION['acces']);
include ("include/UrlParam2PhpVar.inc.php");
require 'graphique/admin.inc.php';
require 'fonction.inc.php';
require 'fonction_html.inc.php';
require 'class/class_module.php';
require "lang$lg.inc.php";
require 'class/Class_Rss.php';
dbConnect();
include ('style.inc.php');
$etat_fav = GetDataField($connect,"select param_etat_lb from param_foad where param_typ_lb='favoris'","param_etat_lb");
?>
<div id="affiche" class="Status"></div>
<script type="text/javascript">
$(document).ready(function()
{
    $('#mon_contenu').click(function()
    {
      if ($.browser.msie) {
      $(this).hide();
      }else{
      $(this).hide('slow');
      }
    });
});
</script>
<div id="mon_contenu" class="cms"  <?php echo "title=\"$mess_clkF\"></div>";
$date_op = date("Y-m-d H:i:s");
$date_dujour = date ("Y-m-d");
$heure_fiche = substr($date_op,11);
$date_fiche = substr($date_op,0,10);
if ($inserer_act == 1 && $ajouter == 1)
{
   //echo"<pre>";print_r( $_GET);print_r($_POST);echo"</pre>"; exit;
   $typo = ($ressnorok == 'OUI') ? 'A FAIRE' : 'PRESENTIEL';
   $id_suivi = Donne_ID ($connect,"SELECT max(suivi_cdn) from suivi1_$numero_groupe");
   $ins_suivi = mysql_query ("INSERT into suivi1_$numero_groupe (suivi_cdn,suivi_utilisateur_no,suivi_seqajout_no,suivi_act_no,suivi_etat_lb,suivi_grp_no) values ($id_suivi,$utilisateur,$sequence,$id_act,\"$typo\",$numero_groupe)");
   $actnom = GetDataField ($connect,"select act_nom_lb from activite where act_cdn = $id_act","act_nom_lb");
   $qualite = $msq_formateur;
   $action_fiche = $mess_menu_presc;
   $commentaire = $mess_fiche_actlib." : $actnom";
   $new_fiche = Donne_ID ($connect,"select max(fiche_cdn) from fiche_suivi");
   $req_fiche = mysql_query("INSERT INTO fiche_suivi (fiche_cdn,fiche_utilisateur_no,fiche_auteur_no,fiche_qualite_lb,fiche_date_dt,fiche_heure_dt,fiche_commentaire_cmt,fiche_grp_no,fiche_parc_no,fiche_seq_no,fiche_act_no,fiche_typaction_lb) VALUES($new_fiche,$utilisateur,$id_user,'$qualite','$date_fiche','$heure_fiche',\"$commentaire\",$numero_groupe,$id_parc,$id_seq,$id_act,\"$action_fiche\")");
   $lien = "details_parc.php?".str_replace("|","&",$params);
   $lien = urlencode($lien);
    echo "<script language=\"JavaScript\">";
      echo "document.location.replace(\"trace.php?link=$lien\")";
    echo "</script>";
}
if ($message != "")
   echo notifier($message);
if ($ajouter == 1)
{
  ?>
  <SCRIPT language=JavaScript>
    function checkForm(frm) {
      var ErrMsg = "<?php echo $mess_info_no;?>\n";
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

    $letitre= $msq_choix_act;
    entete_simple($letitre);
    echo aide_simple("activite");
    echo "<TR><TD colspan='2' class='sous_titre'>$mess_act_libres</TD></TR>";
    //Sélection des activités
    $act_query = mysql_query ("select act_cdn,act_nom_lb from activite where act_seq_no = 0");
    $Nb_act = mysql_num_rows ($act_query);
    if ($Nb_act == 0)
    {
      echo "<TR><TD>$msq_noact<br></TD></TR>";
    }
    else
    {
       if (isset($medor) && $medor == 1)
       {
        if ($keydesc != "" && $keytitre == "" && $keypub == "")
           $champ_rech = "activite.act_consigne_cmt like \"%$keydesc%\"";
        elseif ($keydesc == "" && $keytitre != "" && $keypub == "")
           $champ_rech = "activite.act_nom_lb like \"%$keytitre%\"";
        elseif ($keydesc == "" && $keytitre == "" && $keypub != "")
           $champ_rech = "(activite.act_publique_on = '$keypub')";
        elseif ($keydesc != "" && $keytitre != "" && $keypub == "")
           $champ_rech = "activite.act_nom_lb like \"%$keytitre%\" AND activite.act_consigne_cmt like \"%$keydesc%\"";
        elseif ($keydesc == "" && $keytitre != "" && $keypub != "")
           $champ_rech = "activite.act_publique_on = '$keypub' AND (activite.act_nom_lb like \"%$keytitre%\"";
        elseif ($keydesc != "" && $keytitre == "" && $keypub != "")
           $champ_rech = "activite.act_publique_on = '$keypub' AND activite.act_consigne_cmt like \"%$keydesc%\"";
        elseif ($keydesc != "" && $keytitre != "" && $keypub != "")
           $champ_rech = "activite.act_publique_on = '$keypub' AND activite.act_nom_lb like \"%$keytitre%\" AND activite.act_consigne_cmt like \"%$keydesc%\"";
        elseif ($keydesc == "" && $keytitre == "" && $keypub == "")
           $champ_rech = "(activite.act_cdn > '0')";
        // fin de configuration des champs à discriminer
        $act_query = mysql_query ("select activite.act_cdn,activite.act_nom_lb,activite.act_ress_on,activite.act_consigne_cmt,utilisateur.util_nom_lb,utilisateur.util_prenom_lb from activite,utilisateur where $champ_rech AND utilisateur.util_cdn = activite.act_auteur_no AND act_seq_no = 0 AND (activite.act_publique_on = 1 OR (activite.act_publique_on = 0 AND activite.act_auteur_no = $id_user)) order by activite.act_nom_lb,utilisateur.util_nom_lb asc");
        $nb_act = mysql_num_rows($act_query);
        if ($nb_act ==0)
          echo "<TR><TD colspan='2'><B>$mess_no_occur</B></TD></TR>";
        echo "<TR><TD colspan='2'><B>$mess_filtapp </B>  $msq_titre ";
        if (isset($keytitre) && $keytitre != "") echo "<font color='#D45211'><B>$keytitre</B></font> , ";else echo "<B>$mess_nofiltre</B>, ";
        echo "$msq_aff_cons ";
        if (isset($desckey) && $desckey != "") echo "<font color='#D45211'><B>$desckey</B></font> , ";else echo "<B>$mess_nofiltre</B>, ";
        echo "$mess_visdup : ";
        if (isset($keypub) && $keypub == 1) echo "<font color='#D45211'><B>$mess_oui</B></font> ";elseif($keypub == 0 && $keypub != "") echo "<font color='#D45211'><B>$mess_non</B></font> ";else echo "<B>$mess_nofiltre</B>";
        echo "</TD></TR>";
       }
       $liste = (isset($liste))  ? $liste: '';
       echo "<TR><TD colspan='2'><table cellpadding='6' cellspacing='0' width='80%' border='0'>";
       echo "<FORM name='form2' ACTION=\"activite_free.php?ajouter=1&lesseq=$lesseq&sequence=$sequence&utilisateur=$utilisateur&id_seq=$id_seq&id_ref=$id_ref&id_act=$id_act&id_seq=$id_seq&id_parc=$id_parc&numero_groupe=$numero_groupe&parcours=$parcours&liste=$liste&id_parc=$id_parc&miens=$miens&medor=1&vient_de_search=1&params=$params\" METHOD='POST'>";
       echo "<TR bgcolor= '#F4F4F4'><TD nowrap colspan=7><B>$mrc_rech</B></TD></TR>";
       echo "<TR><TD nowrap>$msq_titre</TD><TD nowrap><INPUT TYPE='text' class='INPUT'  name='keytitre' size='20' align='middle'></TD>";
       echo "<TD nowrap>$msq_aff_cons</TD><TD nowrap><INPUT TYPE='text' class='INPUT'  name='keydesc' size='20' align='middle'></TD>";
       echo "<TD nowrap>$mess_visdup</TD>";
       echo "<TD nowrap>";
       echo "<SELECT name='keypub' class='SELECT'>";
       echo "<OPTION></OPTION>";
       echo "<OPTION value='1'>$mess_oui</OPTION>";
       echo "<OPTION value='0'>$mess_non</OPTION>";
       echo "</SELECT></TD>";
       echo "<TD align='center'><A HREF=\"javascript:document.form2.submit();\" onmouseover=\"img1.src='images/fiche_identite/boutvalidb.gif';return true;\" onmouseout=\"img1.src='images/fiche_identite/boutvalid.gif'\">".
       "<IMG NAME=\"img1\" SRC=\"images/fiche_identite/boutvalid.gif\" BORDER='0' onLoad=\"tempImg=new Image(0,0); tempImg.src='images/fiche_identite/boutvalidb.gif'\"></A>";
       echo "</TD></TR></form></TABLE></TD></TR>";
       echo "<TR height='5'><TD colspan='2'>&nbsp;</TD></TR>";
       if ($medor == 1 && $nb_act > 0)
       {
         echo "<TR><TD colspan='2'><table cellpadding='6' cellspacing='2' width='100%' border='0'>";
         echo "<TR bgcolor='#2b677a'>";
         echo "<TD height='20' align='left'><FONT COLOR=white><b>$msq_activite</b></FONT></TD>";
         echo "<TD height='20' align='left'><FONT COLOR=white><b>$mrc_aut</b></FONT></TD>";
         echo "<TD height='20' align='left'><FONT COLOR=white><b>$msq_aff_cons</b></FONT></TD></TR>";
         $i = 0;
         while ($i < $nb_act)
         {
           $id_act = mysql_result ($act_query,$i,"act_cdn");
           $titre = mysql_result ($act_query,$i,"act_nom_lb");
           $ressnorok = mysql_result ($act_query,$i,"act_ress_on");
           $consigne = mysql_result ($act_query,$i,"act_consigne_cmt");
           $nom_auteur = mysql_result ($act_query,$i,"util_nom_lb");
           $prenom_auteur = mysql_result ($act_query,$i,"util_prenom_lb");
           $consigne = str_replace("\r"," ",$consigne);
           $consigne = str_replace("<BR>"," ",$consigne);
           echo couleur_tr($i+1,'');
           echo "<TD height='20' align='left' valign='top'>";
           $lien ="activite_free.php?inserer_act=1&ajouter=1&sequence=$sequence&lesseq=$lesseq&utilisateur=$utilisateur&activite=1&params=$params&parcours=$parcours&liste=$liste&id_act=$id_act&id_seq=$id_seq&id_parc=$id_parc&numero_groupe=$numero_groupe&ressnorok=$ressnorok&ins_ch_act=1&id_ref=$id_ref&miens=$miens&vient_de_search=$vient_de_search";
           $lien = urlencode($lien);
           echo "<DIV id='sequence'><A HREF=\"trace.php?link=$lien\" ".bulle($mess_ajt_act_seq,"","LEFT","ABOVE",150)."$titre</A></DIV></TD>";
           echo "<TD height='20' align='left' valign='top'>$prenom_auteur $nom_auteur</TD>";
           echo "<TD height='20' align='left' valign='top'>".html_entity_decode($consigne,ENT_QUOTES,'iso-8859-1')."</TD></TR>";
         $i++;
         }
         echo "</TABLE></TD></TR>";
       }
       if ($medor != 1 || ($medor == 1 && $nb_act == 0))
       {
         if ($medor != 1)
           echo "<TR><TD align=left colspan=2><A HREF=\"javascript:history.back();\" onmouseover=\"img_annonce.src='images/fiche_identite/boutretourb.gif';return true;\" onmouseout=\"img_annonce.src='images/fiche_identite/boutretour.gif'\">".
                "<IMG NAME=\"img_annonce\" SRC=\"images/fiche_identite/boutretour.gif\" BORDER='0' onLoad=\"tempImg=new Image(0,0); tempImg.src='images/fiche_identite/boutretourb.gif'\"></A></TD></TR>";
       }
    }
    echo "</TABLE></TD></TR></TABLE></TD></TR></TABLE>";
  exit;
}// fin if ($ajouter == 1)
if ($insert_act == 1)
{
        if ($titre != '' && $forum != 1)
        {
            if ($modifie_act == 1)
              $num_act = $ordre_act;
            else
            {
              $num_act = Donne_ID ($connect,"select max(act_ordre_nb) from activite where act_seq_no = 0");
              $id_act = Donne_ID ($connect, "select max(act_cdn) from activite");
              $auteur = $id_user;
            }
            $duree = ($horaire*60) + $minutage;
            if ($id_ress > 0 && $ress_norok == "NON")
            {
              $ress_norok == "OUI";
              $flag = 1;
            }
            if ($id_ress == 0 && $ress_norok == 'NON')
            {
               $flag = 1;
               if ($modifie_act == 1)
               {
                 $upd_act = mysql_query ("update activite set act_nom_lb=\"".$titre."\",act_ordre_nb = '$ordre_act',act_consigne_cmt=\"".
                                         htmlentities($consigne,ENT_QUOTES,'iso-8859-1').
                                         "\",act_commentaire_cmt=\"".htmlentities($commentaire,ENT_QUOTES,'iso-8859-1').
                                         "\",act_ress_on ='$ress_norok',act_ress_no ='$id_ress',act_duree_nb='$duree',".
                                         "act_passagemult_on='$pass_mult',act_acquittement_lb='$acquit',act_notation_on='$notation',".
                                         "act_devoirarendre_on='$dev_a_rendre',act_publique_on='$droit_voir_act',act_auteur_no='$auteur', ".
                                         "act_modif_dt = \"$date_dujour\",act_flag_on='$flag' where act_cdn = '$id_act'");

                 $requete_grp = mysql_query ("select * from groupe order by grp_cdn");
                 $nb_grp = mysql_num_rows($requete_grp);
                 if ($nb_grp > 0)
                 {
                     $gp=0;
                     while ($gp < $nb_grp)
                     {
                         $id_grp = mysql_result($requete_grp,$gp,"grp_cdn");
                         $nbsuivis = mysql_num_rows(mysql_query("select * from suivi1_$id_grp where suivi_act_no =$id_act"));
                         if ($nbsuivis > 0)
                             $changer_suivi = mysql_query ("update suivi1_$id_grp set suivi_etat_lb = 'PRESENTIEL' where suivi_act_no =$id_act AND suivi_etat_lb != 'TERMINE'");
                      $gp++;
                     }
                 }
                 $modifie_rss = rss :: modifie('activite',$id_user,$id_act);

               }
               else
               {
                 $insert_act_query = mysql_query ("insert into activite values ($id_act,$id_seq,$num_act,\"".$titre."\",\"".htmlentities($consigne,ENT_QUOTES,'iso-8859-1')."\",\"".htmlentities($commentaire,ENT_QUOTES,'iso-8859-1')."\",'$ress_norok',0,\"$duree\",\"$pass_mult\",\"$acquit\",\"$notation\",\"$dev_a_rendre\",$auteur,\"$date_dujour\",\"$date_dujour\",$droit_voir_act,$flag)");
                 $insert_rss = rss :: ajout('activite',$id_user,$id_act);
               }
            }
            else
            {
              if ($id_ress > 0 && $ress_norok == 'OUI')
                 $actype = GetDataField ($connect,"select ress_type from ressource_new where ress_cdn = $id_ress","ress_type");
              $titre = stripslashes($titre);
              $consigne=stripslashes($consigne);
              // teste la pertinence des composants de l'activité et renvoi au besoin au formulaire en concervant les données
              $lien="activite_free.php?renvoi=1&id_act=$id_act&id_seq=0&choix_ress=1&creer=1&acquit=$acquit&pass_mult=$pass_mult&dev_a_rendre=$dev_a_rendre&notation=$notation&duree=$duree&droit_voir=$droit_voir&titre=$titre&consigne=$consigne&commentaire=$commentaire&id_ress=$id_ress&miens=$miens";
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
                $id_ress = 0;
              }
              elseif($ress_norok == "OUI" && $id_ress == 0)
                 $flag = 0;
              elseif($modifie_act == 1 && $id_ress > 0)
                 $flag = 1;
              if ($modifie_act != 1)
                 $id_ress = 0;
              $duree = ($horaire*60) + $minutage;
              if ($modifie_act == 1)
              {
                 $upd_act = mysql_query ("update activite set act_nom_lb=\"".$titre."\",act_ordre_nb = '$ordre_act',act_consigne_cmt=\"".
                                         htmlentities($consigne,ENT_QUOTES,'iso-8859-1')."\",act_commentaire_cmt=\"".
                                         htmlentities($commentaire,ENT_QUOTES,'iso-8859-1').
                                         "\",act_ress_on ='$ress_norok',act_ress_no ='$id_ress',act_duree_nb='$duree',".
                                         "act_passagemult_on='$pass_mult',act_acquittement_lb='$acquit',act_notation_on='$notation',".
                                         "act_devoirarendre_on='$dev_a_rendre',act_publique_on='$droit_voir_act',act_auteur_no='$auteur', ".
                                         "act_modif_dt = \"$date_dujour\",act_flag_on='$flag' where act_cdn = '$id_act'");

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
                            $changer_suivi = mysql_query ("update suivi1_$id_grp set suivi_etat_lb = 'A FAIRE' where suivi_act_no =$id_act AND suivi_etat_lb != 'ATTENTE' AND suivi_etat_lb != 'TERMINE'");
                       $gp++;
                     }
                 }
                 $modifie_rss = rss :: modifie('activite',$id_user,$id_act);

              }
              else
              {
                 $insert_act_query = mysql_query ("insert into activite values ($id_act,$id_seq,$num_act,\"".$titre."\",\"".
                                                  htmlentities($consigne,ENT_QUOTES,'iso-8859-1').
                                                  "\",\"".htmlentities($commentaire,ENT_QUOTES,'iso-8859-1').
                                                  "\",'$ress_norok',$id_ress,\"$duree\",\"$pass_mult\",\"$acquit\",".
                                                  "\"$notation\",\"$dev_a_rendre\",$auteur,\"$date_dujour\",\"$date_dujour\",0,$flag)");
                 $insert_rss = rss :: ajout('activite',$id_user,$id_act);
              }
         }// if else !=id_ress
  
              if (empty($_GET['modifie_act']) && !empty($_FILES['userfile']['tmp_name']))
                 include("saveMedia.php");
              if ($modifie_act == 1 && isset($okDev) && $okDev == 1)
              {
                 $changer_dev = mysql_query ("update activite_devoir set actdev_dev_lb = \"$typdev\" where actdev_act_no =$id_act");
              }
              elseif (($modifie_act == 1 && !isset($okDev)) || empty($modifie_act))
              {
                 // Cas de nouvelle activité ou de modification d'activité sans pendant devoir
                 $id_actdev = Donne_ID ($connect, "select max(actdev_cdn) from activite_devoir");
                 $changer_dev = mysql_query ("insert into activite_devoir values($id_actdev,$id_act,\"$typdev\")");
              }
       } //fin else ($titre==''....)
       $id_ress = GetDataField ($connect,"select act_ress_no from activite where act_cdn = $id_act","act_ress_no");
       if (isset($ress_norok) && $ress_norok == 'OUI' && ($id_ress == 0 || ($id_ress > 0 && isset($new_act) && $new_act == 1)) && ((isset($modifie_act) && $modifie_act == 1) || (isset($new) && $new == 1)))
       {
           $letitre= $mess_ass_act;
           entete_concept("liste_act.inc.php",$letitre);
           echo aide_simple("activite");
           //echo "<TR height='25'><TD colspan='2'>&nbsp;</TD></TR>";
           echo "<TD valign='top' width='70%'  bgColor='#FFFFFF'>";
           echo "<TABLE border=0 cellpadding='4' cellspacing = '4'>";
           // ressource sujet forum
           $chaine_act="&miens=$miens&acces=act_free&medor=$medor&titre_activite=$titre&titre_act=$titre_act&keydesc=$keydesc&keytitre=$keytitre&proprio=$proprio&miens=$miens&keypub=$keypub";
           $params = str_replace("&","|",$chaine_act);
           $forum_act  = $id_act;
           $_SESSION['forum_act'] = $forum_act;
           $_SESSION['chaine_act'] = $chaine_act;
           $lien="activite_free.php?titre_act=$titre_act&id_act=$id_act&consult_act=1&lesseq=$lesseq&&miens=$miens&medor=$medor&proprio=$proprio&keydesc=$keydesc&keytitre=$keytitre&keypub=$keypub";
           $lien = urlencode($lien);
           echo "<TR height='40'><TD align='left' nowrap>$bouton_gauche<A href=\"trace.php?link=$lien\" target='main'>$mess_plustard</A>$bouton_droite</TD></TR>";
           echo "<TR height='40'><TD valign='top'><Font size='2'><B>$mess_assress</B></TD></TR>";//$msq_ass_ress_form$mess_assres_comp
           $lien="recherche.php?id_act=$id_act&acces=act_free&flag=0&miens=$miens&medor=$medor";
           $lien = urlencode($lien);
           echo "<TR><TD align='left' nowrap valign='top'>$bouton_gauche<A href=\"trace.php?link=$lien\" target='main'>$mess_ressidx</A>$bouton_droite</TD><TD valign='top'>$mess_goidx</TD></TR>";
           $lien="charge_ressource_vrac.php?id_act=$id_act&dou=act_free&params=$params&charger_fichier=1";
           $lien = urlencode($lien);
           echo "<TR height='40'><TD align='left' nowrap valign='top'>$bouton_gauche";
           echo "<A HREF=\"javascript:void(0)\" onClick=\"window.open('trace.php?link=$lien','','scrollbars=yes,resizable=yes,width=500,height=300,left=250,top=300')\">".
                "$mess_ajt_ress_act</A>$bouton_droite</TD><TD>$mess_ajt_complement</TD></TR>";
           $lien="charge_ressource_vrac.php?id_act=$id_act&dou=act_free&params=$params&charger_url=1";
           $lien = urlencode($lien);
           echo "<TR height='30'><TD align='left' nowrap colspan='2' valign='top'>$bouton_gauche";
           echo "<A HREF=\"javascript:void(0)\" onClick=\"window.open('trace.php?link=$lien','','scrollbars=yes,resizable=yes,width=500,height=300,left=250,top=300')\">$mess_ajt_url_act</A>$bouton_droite</TD></TR>";
           //quizz à créer
           $lien="creation_qcm.php?creation_qcm=1&params_qcm=$params&id_activit=$id_act&venu=act&acced=act_free";
           $lien = urlencode($lien);
           // lien vers in fil de discussion d'un forum
           $lien="forum/index.php?f=0&collapse=1&arrive=activite";
           $lien = urlencode($lien);
           echo "<TR height='30'><TD colspan='2' valign='top'><Font size='2'>&nbsp;</TD></TR>";
           echo "<TR><TD align='left' nowrap valign='top'>$bouton_gauche<A href=\"trace.php?link=$lien\" target='main'>$mess_fld</A>$bouton_droite</TD><TD valign='top'>$mess_fldplus</TD></TR>";//$msq_ass_ress_forum
           $lien = "activite_free.php?creer=1&modifie_act=1&medor=$medor&act_a_modif=$id_act&id_seq=$id_seq&miens=$miens&lesseq=$lesseq";
           $lien = urlencode($lien);
           echo "<TR height='15'><TD colspan='2' valign='top'><Font size='2'>&nbsp;</TD></TR>";
           echo "<TR><TD align='left' nowrap valign='top'>$bouton_gauche<A href=\"trace.php?link=$lien\" target='main'>$mess_ret</A>$bouton_droite</TD><TD valign='top'>$mess_fldplus</TD></TR>";//$msq_ass_ress_forum
           echo "</TABLE></TD></TR></TABLE></TD></TR></TABLE>";
         exit();
       }
}
if ($creer == 1)
{
      ?>
      <SCRIPT language=JavaScript>
      function checkForm(frm) {
      var ErrMsg = "<?php echo $mess_info_no;?>\n\n";
      var lenInit = ErrMsg.length;
      //dey Dfoad
      <?php
      if (!isset($act_a_modif))
      {
      ?>
         if (isEmpty(frm.userfile)== false)
         {
          if ( frm.userfile.value != "" && strstr(frm.userfile.value,'.swf')!= true && strstr(frm.userfile.value,'.mp3')!= true && strstr(frm.userfile.value,'.flv') != true )
             ErrMsg += ' - seuls les fichiers SWF, FLV ou MP3 sont autorisés pour accompagner la consigne\n';
         }
      <?php
      }
      else
      {
             $req_typdev = mysql_num_rows(mysql_query("select * from activite_devoir where actdev_act_no = $act_a_modif"));
             $dev_act = "";
             if ($req_typdev > 0)
                 $dev_act = GetDataField ($connect,"select actdev_dev_lb from activite_devoir where
                                            actdev_act_no = $act_a_modif ","actdev_dev_lb");
      }
      ?>
      if (isEmpty(frm.titre)==true)
        ErrMsg += ' - <?php echo $msq_tit_form;?>\n';
      if (isEmpty(frm.consigne)==true)
        ErrMsg += ' - <?php echo $msq_consigne_act_form;?>\n';
      <?php
      if (isset($act_a_modif) && isset($req_typdev) && $dev_act == 'xApi TinCan')
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
      //       type d'activité
      //dey Dfoad
      if ($modifie_act == 1)
      {
          $act_query = mysql_query("select * from activite where act_cdn=$act_a_modif");
          $ia = 0;
          $id = mysql_result ($act_query,$ia,"act_cdn");
          $titre = mysql_result ($act_query,$ia,"act_nom_lb");
          $id_seq = mysql_result ($act_query,$ia,"act_seq_no");
          $ordre_act = mysql_result ($act_query,$ia,"act_ordre_nb");
          $flag = mysql_result ($act_query,$ia,"act_flag_on");
          $id_ress = mysql_result ($act_query,$ia,"act_ress_no");
          $duree = mysql_result ($act_query,$ia,"act_duree_nb");
          $ress_norok = mysql_result ($act_query,$ia,"act_ress_on");
          $consigne = html_entity_decode(mysql_result ($act_query,$ia,"act_consigne_cmt"),ENT_QUOTES,'iso8859-1');
          $commentaire = html_entity_decode(mysql_result ($act_query,$ia,"act_commentaire_cmt"),ENT_QUOTES,'iso8859-1');
          $pass_mult = mysql_result ($act_query,$ia,"act_passagemult_on");
          $acquit = mysql_result ($act_query,$ia,"act_acquittement_lb");
          $dev_a_rendre = mysql_result ($act_query,$ia,"act_devoirarendre_on");
          $notation = mysql_result ($act_query,$ia,"act_notation_on");
          $auteur = mysql_result ($act_query,$ia,"act_auteur_no");
          $droit_voir_act = mysql_result ($act_query,$ia,"act_publique_on");
      }
     if ($id_seq > 0)
      {
         $titre_seq = GetDataField ($connect,"select seq_titre_lb from sequence where seq_cdn = $id_seq","seq_titre_lb");
         $letitre = "$msq_activite : $msq_modifier";
      }
      elseif ($modifie_act == 1)
         $letitre= "$titre : $msq_modifier";
      else
         $letitre= $msq_ajout_act_seq;
      echo "<CENTER><TABLE bgColor='#298CA0' cellspacing='1' cellpadding='0' width='98%'><TR>";
      include ("liste_act.inc.php");
      echo "<TD valign='top' width='70%'  bgColor='#FFFFFF'>";//
      echo "<FORM Name='form1' ENCTYPE='multipart/form-data' action=\"activite_free.php?modifie_act=$modifie_act&medor=$medor&insert_act=1&consult_act=1&titre_act=$titre_act&miens=$miens&lesseq=$lesseq\" method='POST'>";
      echo "<TABLE bgColor='#FFFFFF' cellspacing='1' cellpadding='0' width='100%'>";
         echo "<INPUT type='HIDDEN' name='keytitre' value=\"$keytitre\">";
         echo "<INPUT type='HIDDEN' name='keydesc' value=\"$keydesc\">";
         echo "<INPUT type='HIDDEN' name='keypub' value=\"$keypub\">";
         echo "<INPUT type='HIDDEN' name='medor' value='$medor'>";
         echo "<INPUT type='HIDDEN' name='id_seq' value='$id_seq'>";
      echo "<TR><TD background=\"images/fond_titre_table.jpg\" colspan='1' height='40' align='center' valign='center'><Font size='3' color='#FFFFFF'><B>$letitre</B></FONT></TD></TR>";
      echo aide_simple("activite");
      if ($modifie_act == 1 && $id > 0)
      {
             $req_typdev = mysql_result(mysql_query("select count(*) from activite_devoir where actdev_act_no = $id"),0);
             $dev_act = "";
             if ($req_typdev > 0)
             {
                 $dev_act = GetDataField ($connect,"select actdev_dev_lb from activite_devoir where
                                            actdev_act_no = $id ","actdev_dev_lb");
                 echo "<INPUT type='HIDDEN' name='okDev' value='1' />";
             }
             else
                 $dev_act = "Pas de devoir";

      }
      else
      {
             $dev_act = "Pas de devoir";
      }
       if ($id_seq > 0)
          echo "<TR><TD class='sous_titre'><B>$msq_seq :</B> $titre_seq</TD></TR>";
      if ($medor == 1)
         $miens = 1;
      echo "<TR><TD bgColor='#FFFFFF'><TABLE height='100%' widht='90%' cellspacing='1' cellpadding='4'><TR><TD>";
      if ($modifie_act == 1)
      {
         echo "<INPUT type='HIDDEN' name='keytitre' value=\"$keytitre\">";
         echo "<INPUT type='HIDDEN' name='keydesc' value=\"$keydesc\">";
         echo "<INPUT type='HIDDEN' name='keypub' value=\"$keypub\">";
         echo "<INPUT type='HIDDEN' name='medor' value='$medor'>";
         echo "<INPUT type='HIDDEN' name='id_seq' value='$id_seq'>";
         echo "<INPUT type='HIDDEN' name='titre_act' value=\"$titre_act\">";
         echo "<INPUT type='HIDDEN' name='auteur' value='$auteur'>";
         echo "<INPUT type='HIDDEN' name='ordre_act' value='$ordre_act'>";
         echo "<INPUT type='HIDDEN' name='id_act' value='$id'>";
         echo "<INPUT type='HIDDEN' name='typdev' value='xApi TinCan'>";
     }
     else
     {
        echo "<INPUT type='HIDDEN' name='id_seq' value='0'>";
        echo "<INPUT type='HIDDEN' name='new' value='1'>";
     }
     echo "</td></tr>";
     if ($modifie_act == 1 && $ress_norok == 'OUI')
     {
         if (isset($dev_act) && $dev_act == 'xApi TinCan')
             echo "<TR><TD></td><td><div class='SOUS_TITRE' style='color:red;font-size:12px;'><B>".
                  "Cette ressource est au standard<span style='color:blue;font-size:14px;'> TinCan xApi ".
                  "</span> et seuls certains champs sont modifiables</B></div></td></tr>";
         echo "<TR><TD nowrap valign='top'><B>$msq_ress_assoc</B></TD><TD nowrap><table><tbody><tr><td colspan='2'>";
     }
     else
         echo "<TR><TD colspan='2'><table><tbody><tr><td>";
     echo "<DIV id='laressource'>";
     if ($id_ress > 0 )
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
    if ($modifie_act == 1 && (!isset($dev_act) || (isset($dev_act) && $dev_act != 'xApi TinCan')))
    {
       $lien="activite_free.php?modifie_act=$modifie_act&medor=$medor&insert_act=1&ress_norok=$ress_norok&titre_act=$titre_act&new_act=1&id_act=$id&miens=$miens&lesseq=$lesseq";
       $lien = urlencode($lien);
       if ($id_ress == 0  && $ress_norok == 'OUI')
         echo "<tr><td>$bouton_gauche<a href=\"trace.php?link=$lien\" target='main'>$msq_ass_ress_form</A>$bouton_droite</td>";
       elseif ($id_ress > 0 && $ress_norok == 'OUI')
         echo "<tr><td>$bouton_gauche<a href=\"trace.php?link=$lien\" target='main'><div id='ajoute_ress'>$msq_ass_ress</div></A>$bouton_droite</td>";
       if ($ress_norok == 'OUI' && $id_ress > 0)
       {
            echo "<td><div id='supp_ress' name='supp_ress' class='seqcla' ".
                 "onclick=\"javascript:if (conf() == true){appel_simple('formation/ress_supp.php?id_act=$id&flag=$flag&ress_norok=$ress_norok');".
                 "var mon_content=document.getElementById('laressource');mon_content.innerHTML='';".
                 "var content=document.getElementById('supp_ress');content.style.visibility='hidden';".
                 "var mon_titre_ress=document.getElementById('ajoute_ress');mon_titre_ress.innerHTML='$msq_ass_ress_form';".
                 "var mon_ress=document.getElementById('idress');mon_ress.innerHTML='<input type=hidden name=\'id_ress\' value=\'\'>';}\">".
                 "$bouton_gauche1<font color='#24677A'><B>$msq_slk_ress</B> </font>$bouton_droite1</div></td></tr>";
       }
    }
      echo "</tbody></table></TD></TR></DIV>";
      echo "<INPUT type='HIDDEN' name='ordre_act' value='$ordre_act'>";
      echo "<TR><TD nowrap><B>$msq_tit_form</B></TD>";
      echo "<TD nowrap><INPUT TYPE=\"TEXT\" name=\"titre\" align=\"middle\" size='60' value=\"$titre\"><br></TD></TR>";
      echo "<TR><TD nowrap><B>$msq_consigne_act_form</B></TD>";
      echo "<TD nowrap><TEXTAREA class='TEXTAREA' rows='6' cols='75' align=\"middle\" name=\"consigne\">$consigne</TEXTAREA><br></TD></TR>";
//dey Dfoad
      if (!empty($id) && $modifie_act == 1)
      {
          $req_media = mysql_result(mysql_query("select count(*) from activite_media where actmedia_act_no = $id"),0);
          $media_act = "";
          if ($req_media > 0)
          {
             $media_act = GetDataField ($connect,"select ress_url_lb from ressource_new,activite_media where
                                            ress_cdn = actmedia_ress_no and actmedia_act_no =$id ","ress_url_lb");
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

      if (empty($id))
      {
         echo "<TR><TD><div style='display:none;'><b>Insérer une consigne multimédia </b>(facultatif)</td>".
              "<td><div style='color:red;font-weight:bold;font-size:10px;display:none;'>";
         echo "<INPUT TYPE='file' name='userfile' ENCTYPE='multipart/form-data'></div></div></TD></TR>";
      }
      //fin Dey
   if (isset($dev_act) && $dev_act == 'xApi TinCan')
   {
     if ($duree > 0){
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
      echo "<INPUT TYPE='HIDDEN' name='ordre' value='$ordre'>";
      echo "<INPUT type='HIDDEN' name='ress_norok' value='OUI' />";
      echo "<INPUT type='HIDDEN' name='droit_voir_act' value='1' />";
      echo "<INPUT type='HIDDEN' name='pass_mult' value='OUI' />";
      echo "<INPUT type='HIDDEN' name='acquit' value='RESSOURCE' />";
      echo "<INPUT type='HIDDEN' name='dev_a_rendre' value='NON' />";
      echo "<INPUT type='HIDDEN' name='typdev' value='xApi TinCan' />";
      echo "<INPUT type='HIDDEN' name='notation' value='OUI' /></TD></TR>";
   }
   else
   {
      echo "<TR><TD nowrap><B>$mess_admin_comment</B></TD>";
      echo "<TD nowrap><TEXTAREA class='TEXTAREA' rows='6' cols='75' align=\"middle\" name=\"commentaire\">$commentaire</TEXTAREA><br></TD></TR>";
      echo "<TR><TD nowrap><B>$msq_ass_ress_form</B></TD>";
      echo "<TD nowrap>";
      if ($modifie_act != 1)
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
      if ($modifie_act != 1)
      {
            echo "$mess_oui&nbsp;&nbsp;<INPUT type='radio' name='droit_voir_act' value='1'>&nbsp;&nbsp;&nbsp;&nbsp;";
            echo "$mess_non&nbsp;&nbsp;<INPUT type='radio' name='droit_voir_act' value='0'>";
      }
      else
      {
         if (isset($droit_voir_act) && $droit_voir_act == 1)
         {
            echo "$mess_oui&nbsp;&nbsp;<INPUT type='radio' name='droit_voir_act' value='1' checked>&nbsp;&nbsp;&nbsp;&nbsp;";
            echo "$mess_non&nbsp;&nbsp;<INPUT type='radio' name='droit_voir_act' value='0'>";
         }
         elseif ((isset($droit_voir_act) && $droit_voir_act == 0) || !isset($droit_voir_act))
         {
            echo "$mess_oui&nbsp;&nbsp;<INPUT type='radio' name='droit_voir_act' value='1'>&nbsp;&nbsp;&nbsp;&nbsp;";
            echo "$mess_non&nbsp;&nbsp;<INPUT type='radio' name='droit_voir_act' value='0' checked>";
         }
      }
      echo "</TD></TR>";
      echo "<TR><TD nowrap><B>$msq_duree_form</B></TD><TD align='left'><TABLE border='0' cellspacing='0'><TR>";
      if ($titre == '' || $modifie_act == 1)
      {
         $reste = $duree%60;
         $heure = floor($duree/60);
         if ($duree == 0 || !isset($duree))
         {
           echo "<TD><INPUT TYPE='text' class='INPUT' name='horaire' size='2' maxlength = '3' align='center'>$h </TD>";
           echo "<TD><INPUT TYPE='text' class='INPUT' name='minutage' size='2' maxlength = '2' align='center'>$mn</TD>";
         }
         else
         {
           echo "<TD><INPUT TYPE='text' class='INPUT' name='horaire' value='$heure' size='2' maxlength = '3' align='center'>$h </TD>";
           echo "<TD><INPUT TYPE='text' class='INPUT' name='minutage' value='$reste' size='2' maxlength = '2' align='center'>$mn</TD>";
         }
      }
      echo "</TR></TABLE></TD></TR>";
      echo "<TR><TD nowrap><B>$msq_pass_act_form</B></TD><TD nowrap>";
      if ($modifie_act != 1)
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
      if ($modifie_act == 1 &&  $acquit == 'APPRENANT')
      {
            echo ucfirst(strtolower($msq_apprenant))."&nbsp;&nbsp;<INPUT type='radio' name='acquit' value='APPRENANT' checked>&nbsp;&nbsp;&nbsp;&nbsp;";
            echo ucfirst(strtolower($msq_formateur))."&nbsp;&nbsp;<INPUT type='radio' name='acquit' value='FORMATEUR_REFERENT'>&nbsp;&nbsp;&nbsp;&nbsp;";
            echo ucfirst(strtolower($msq_ress))."&nbsp;&nbsp;<INPUT type='radio' name='acquit' value='RESSOURCE' onclick=\"window.alert('$mess_avrt_act_ress');\">";
      }
      elseif ($modifie_act == 1 && $acquit == 'FORMATEUR_REFERENT')
      {
            echo ucfirst(strtolower($msq_apprenant))."&nbsp;&nbsp;<INPUT type='radio' name='acquit' value='APPRENANT'>&nbsp;&nbsp;&nbsp;&nbsp;";
            echo ucfirst(strtolower($msq_formateur))."&nbsp;&nbsp;<INPUT type='radio' name='acquit' value='FORMATEUR_REFERENT' checked>&nbsp;&nbsp;&nbsp;&nbsp;";
            echo ucfirst(strtolower($msq_ress))."&nbsp;&nbsp;<INPUT type='radio' name='acquit' value='RESSOURCE' onclick=\"window.alert('$mess_avrt_act_ress');\">";
      }
      elseif ($modifie_act == 1 &&  $acquit == 'RESSOURCE')
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
      echo "<br></TD></TR>";
      echo "<TR><TD nowrap valign='center'><B>$msq_dev_act_form</B></TD><TD nowrap valign='bottom'>".
           "<TABLE cellpadding='0' cellspacing='0' border=0><TR><TD nowrap valign='center'>";
      if ($modifie_act == 1 && $dev_a_rendre == 'OUI')
      {
            echo "$mess_oui&nbsp;&nbsp;<INPUT type='radio' name='dev_a_rendre' value='OUI' checked>&nbsp;&nbsp;&nbsp;&nbsp;";
            echo "$mess_non&nbsp;&nbsp;<INPUT type='radio' name='dev_a_rendre' value='NON'>";
      }
      elseif ($modifie_act == 1 && $dev_a_rendre == 'NON')
      {
            echo "$mess_oui&nbsp;&nbsp;<INPUT type='radio' name='dev_a_rendre' value='OUI'>&nbsp;&nbsp;&nbsp;&nbsp;";
            echo "$mess_non&nbsp;&nbsp;<INPUT type='radio' name='dev_a_rendre' value='NON' checked>";
      }
      else
      {
            echo "$mess_oui&nbsp;&nbsp;<INPUT type='radio' name='dev_a_rendre' value='OUI'>&nbsp;&nbsp;&nbsp;&nbsp;";
            echo "$mess_non&nbsp;&nbsp;<INPUT type='radio' name='dev_a_rendre' value='NON'>";
      }
      echo "<br></TD></TR></TABLE></TD></TR>";
      echo "<TR><TD nowrap><B>$msq_not_act_form</B></TD><TD nowrap>";
      if ($modifie_act == 1 && $notation == 'OUI')
      {
            echo "$mess_oui&nbsp;&nbsp;<INPUT type='radio' name='notation' value='OUI' checked>&nbsp;&nbsp;&nbsp;&nbsp;";
            echo "$mess_non&nbsp;&nbsp;<INPUT type='radio' name='notation' value='NON'>";
      }
      elseif ($modifie_act == 1 && $notation == 'NON')
      {
            echo "$mess_oui&nbsp;&nbsp;<INPUT type='radio' name='notation' value='OUI'>&nbsp;&nbsp;&nbsp;&nbsp;";
            echo "$mess_non&nbsp;&nbsp;<INPUT type='radio' name='notation' value='NON' checked>";
      }
      else
      {
            echo "$mess_oui&nbsp;&nbsp;<INPUT type='radio' name='notation' value='OUI'>&nbsp;&nbsp;&nbsp;&nbsp;";
            echo "$mess_non&nbsp;&nbsp;<INPUT type='radio' name='notation' value='NON'>";
      }
      echo "</TD></TR>";
      echo "<TR><TD style='font-weight:bold;'>Type de devoir</td>";
      echo "<td><SELECT class='SELECT' id='typdev' name='typdev' style='font-weight:bold;font-size:13px;'>";
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
      echo "</SELECT></TD></TR>";
   }
      
   echo "<TR height='50'>";
   echo "<TD>&nbsp;</TD><TD align='left' valign ='bottom'><A HREF=\"javascript:checkForm(document.form1);\" onClick=\"TinyMCE.prototype.triggerSave();\" onmouseover=\"img1.src='images/fiche_identite/boutvalidb.gif';return true;\" onmouseout=\"img1.src='images/fiche_identite/boutvalid.gif'\">".
           "<IMG NAME=\"img1\" SRC=\"images/fiche_identite/boutvalid.gif\" BORDER='0' onLoad=\"tempImg=new Image(0,0); tempImg.src='images/fiche_identite/boutvalidb.gif'\"></A>";
   echo "</TD></TR></TABLE></FORM>";
   echo "</TD></TR></TABLE></TD></TR></TABLE>";
   exit;
}
require_once ('class/Class_Rss.php');
    if ($supp_act == 1)
    {
       $act_query = mysql_query("delete from stars where star_item_id = '$act_a_supp' and star_type_no='3'");
       $act_query = mysql_query("delete from activite where act_cdn = $act_a_supp");
       $supp_rss = rss :: supprime('activite',$act_a_supp);
       //dey Dfoad
          $act_media = mysql_query("delete from activite_media where actmedia_act_no = $act_a_supp");
          $act_devoir = mysql_query("delete from activite_devoir where actdev_act_no = $act_a_supp");
    }
    if ($dupli_act == 1)
       ActiviteDupli($act_a_dupli);
    if ($action_act== 1 && $id_ress > 0)
    {
        $req_updt_act = mysql_query("UPDATE activite set act_ress_no=$id_ress,act_flag_on = 1,act_modif_dt = '$date_dujour' WHERE act_cdn = $id_act");
        $modifie_rss = rss :: modifie('activite',$id_user,$id_act);
        $id_seq = GetDataField ($connect,"select act_seq_no from activite where act_cdn = $id_act","act_seq_no");
        if ($id_seq > 0)
        {
          $titre_act = $mess_liste_vos_act_seq;
          $lesseq = 1;
          $requete_act = "select * from activite where act_auteur_no = $id_user and act_seq_no > 0 order by act_nom_lb";
        }
        else
        {
          $titre_act = $mess_liste_vos_act;
          $lesseq = 0;
          $requete_act = "select * from activite where act_auteur_no = $id_user and act_seq_no=0 order by act_nom_lb";
        }
    }

    if ($forum == 1 || strstr($url_ress,"forum/read.php"))
    {
    
          $requete_act = "select * from activite where act_auteur_no = $id_user and act_seq_no=0 order by act_nom_lb";
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
            $le_lien = str_replace("&arrive=activite","","$adresse_http/forum/read.php?$lien_act");
            $list_item = explode("&",$lien_act);
            $item1 = explode("=",$list_item[0]);
            $num_forum = $item1[1];
            $nom_forum = GetDataField ($connect,"select name from forums where id='$num_forum'","name");
            //@forum
            $titre_activite = GetDataField ($connect,"select act_nom_lb from activite where act_cdn=$id_act","act_nom_lb");
            if ($titre_activite != '' && !strstr($titre_activite,$msq_ass_ress_forum))
               $titre_forum = $titre_activite."  (".$msq_ass_ress_forum." ".$nom_forum.")";
            else
               $titre_forum = $msq_ass_ress_forum." ".$nom_forum;
            $desc_forum = $mess_desc_forum;
            $verif_exist = GetDataField($connect,"SELECT act_ress_no FROM activite WHERE act_cdn = $id_act","act_ress_no");
            if ($verif_exist > 0)
            {
              $verif_other =mysql_query("SELECT count(act_ress_no) FROM activite WHERE act_ress_no = $verif_exist");
              $nb_verif_other = mysql_result($verif_other,0);
              if ($nb_verif_other == 1 && strstr($url_ress,"forum/read.php"))
              {
                $id_ress =$verif_exist;
                $sql_update= mysql_query("UPDATE ressource_new SET ress_modif_dt=\"$date_dujour\",ress_url_lb=\"$le_lien\" WHERE ress_cdn = $verif_exist");
              }
              else
              {
                $id_ress = Donne_ID ($connect,"select max(ress_cdn) from ressource_new");
                $sql_insere= mysql_query("INSERT INTO ressource_new (ress_cdn,ress_cat_lb,ress_typress_no,ress_url_lb,ress_auteurs_cmt,ress_publique_on,ress_titre,ress_desc_cmt,ress_create_dt,ress_ajout,ress_type,ress_support,ress_niveau) VALUES ('$id_ress',\"Forums\",$new_catid,\"$le_lien\",\"forum\",\"NON\",\"".str_replace("\"","'",$titre_forum)."\",\"$desc_forum\",\"$date_dujour\",\"$login\",\"ACQUISITION\",\"Web\",\"1\")");
              }
            }
            else
            {
              $id_ress = Donne_ID ($connect,"select max(ress_cdn) from ressource_new");
              $sql_insere= mysql_query("INSERT INTO ressource_new (ress_cdn,ress_cat_lb,ress_typress_no,ress_url_lb,ress_auteurs_cmt,ress_publique_on,ress_titre,ress_desc_cmt,ress_create_dt,ress_ajout,ress_type,ress_support,ress_niveau) VALUES ('$id_ress',\"Forums\",$new_catid,\"$le_lien\",\"forum\",\"NON\",\"".str_replace("\"","'",$titre_forum)."\",\"$desc_forum\",\"$date_dujour\",\"$login\",\"ACQUISITION\",\"Web\",\"1\")");
            }
            $req_updt_act =mysql_query("UPDATE activite set act_ress_no=$id_ress,act_flag_on = 1 WHERE act_cdn = $id_act");
            $modifie_rss = rss :: modifie('activite',$id_user,$id_act);
          }
          elseif (!isset($forum) && strstr($url_ress,"forum/read.php") && $arrive == "activite")
          {
              $id_ress = Donne_ID ($connect,"select max(ress_cdn) from ressource_new");
              $sql_insere= mysql_query("INSERT INTO ressource_new (ress_cdn,ress_cat_lb,ress_typress_no,ress_url_lb,ress_auteurs_cmt,ress_publique_on,ress_titre,ress_desc_cmt,ress_ajout,ress_type,ress_support,ress_niveau) VALUES ('$id_ress',\"Forums\",$new_catid,\"$url_ress\",\"forum\",\"NON\",\"".str_replace("\"","'",$titre_ress)."\",\"$desc_ress\",\"$date_dujour\",\"$login\",\"ACQUISITION\",\"Web\",\"1\")");
          }
     }
if ((isset($consult_act) && $consult_act == 1) || (isset($action_act) && $action_act == 1))
{
   $laliste = "liste_act.inc.php";
   include ('include/consult_act.inc.php');
   exit;
}
    //Sélection des activités
       if ($medor == 1)
       {
        $_SESSION['requete_act'] = $requete_act;
        if ($keydesc != "" && $keytitre == "" && $keypub == "")
           $champ_rech = "activite.act_consigne_cmt like \"%$keydesc%\"";
        elseif ($keydesc == "" && $keytitre != "" && $keypub == "")
           $champ_rech = "activite.act_nom_lb like \"%$keytitre%\"";
        elseif ($keydesc == "" && $keytitre == "" && $keypub != "")
           $champ_rech = "(activite.act_publique_on = '$keypub')";
        elseif ($keydesc != "" && $keytitre != "" && $keypub == "")
           $champ_rech = "activite.act_nom_lb like \"%$keytitre%\" AND activite.act_consigne_cmt like \"%$keydesc%\"";
        elseif ($keydesc == "" && $keytitre != "" && $keypub != "")
           $champ_rech = "activite.act_publique_on = '$keypub' AND activite.act_nom_lb like \"%$keytitre%\"";
        elseif ($keydesc != "" && $keytitre == "" && $keypub != "")
           $champ_rech = "activite.act_publique_on = '$keypub' AND activite.act_consigne_cmt like \"%$keydesc%\"";
        elseif ($keydesc != "" && $keytitre != "" && $keypub != "")
           $champ_rech = "activite.act_publique_on = '$keypub' AND activite.act_nom_lb like \"%$keytitre%\" AND activite.act_consigne_cmt like \"%$keydesc%\"";
        elseif ($keydesc == "" && $keytitre == "" && $keypub == "")
           $champ_rech = "activite.act_cdn > 0";
        // fin de configuration des champs à discriminer
        if ($miens == 1)
        {
           if ($lesseq == 0)
             $requete_act = "select * from activite where $champ_rech AND activite.act_seq_no = 0 AND activite.act_auteur_no = $id_user order by activite.act_nom_lb asc";
           elseif ($lesseq == 1)
             $requete_act = "select * from activite where $champ_rech AND activite.act_seq_no != 0 AND activite.act_auteur_no = $id_user order by activite.act_nom_lb asc";
           elseif ($lesseq == 2)
             $requete_act = "select * from activite where $champ_rech AND activite.act_auteur_no = $id_user order by activite.act_nom_lb asc";
        }
        elseif ($proprio > 0)
        {
           if ($lesseq == 0)
             $requete_act = "select * from activite where $champ_rech AND activite.act_seq_no = 0 AND activite.act_auteur_no = $proprio order by activite.act_nom_lb asc";
           elseif ($lesseq == 1)
             $requete_act = "select * from activite where $champ_rech AND activite.act_seq_no > 0 AND activite.act_auteur_no = $proprio order by activite.act_nom_lb asc";
           elseif ($lesseq == 2)
             $requete_act = "select * from activite where $champ_rech AND activite.act_auteur_no = $proprio order by activite.act_nom_lb asc";
        }
        elseif ($visible == 1)
        {
           $requete_act = "select * from activite where $champ_rech AND act_publique_on = 0 order by activite.act_nom_lb asc";
        }
        else
        {
           if ($lesseq == 0)
             $requete_act = "select * from activite where $champ_rech AND activite.act_seq_no = 0 order by activite.act_nom_lb asc";
           elseif ($lesseq == 1)
             $requete_act = "select * from activite where $champ_rech AND activite.act_seq_no > 0 order by activite.act_nom_lb asc";
           elseif ($lesseq == 2)
             $requete_act = "select * from activite where $champ_rech order by activite.act_nom_lb asc";
        }
       }
       if (!isset($requete_act) || empty($requete_act))
       {
          $requete_act0 ="select * from activite where act_auteur_no = $id_user order by act_nom_lb";
          $act_question_tts_act = mysql_query ($requete_act0);
          $requete_act1 ="select * from activite where act_auteur_no = $id_user and act_seq_no=0 order by act_nom_lb";
          $act_question_miens_lib = mysql_query ($requete_act1);
          $requete_act2 = "select * from activite where act_auteur_no = $id_user and act_seq_no>0 order by act_nom_lb";
          $act_question_miens = mysql_query ("$requete_act2");
          $requete_act3 = "select * from activite where act_seq_no=0 and (act_publique_on=1 or (act_publique_on=0 and act_auteur_no=$id_user)) order by act_nom_lb";
          $act_question_tts_lib = mysql_query ("$requete_act3");
          $requete_act4 = "select * from activite where act_seq_no > 0  and (act_publique_on=1 or (act_publique_on=0 and act_auteur_no=$id_user)) order by act_nom_lb";
          $act_question_tts = mysql_query ("$requete_act4");
          $nb_act0 = mysql_num_rows ($act_question_tts_act);
          $nb_act1 = mysql_num_rows ($act_question_miens_lib);
          $nb_act2 = mysql_num_rows ($act_question_miens);
          $nb_act3 = mysql_num_rows ($act_question_tts_lib);
          $nb_act4 = mysql_num_rows ($act_question_tts);
          if ($nb_act0 > 0)
             $requete_act="select * from activite where act_auteur_no = $id_user order by act_nom_lb";
          if ($nb_act1 > 0)
             $requete_act="select * from activite where act_auteur_no = $id_user and act_seq_no=0 order by act_nom_lb";
          elseif ($nb_act1 == 0 && $nb_act2 > 0)
             $requete_act="select * from activite where act_auteur_no = $id_user and act_seq_no>0 order by act_nom_lb";
          elseif ($nb_act1 == 0 && $nb_act2 == 0 && $nb_act3 > 0)
             $requete_act="select * from activite where act_seq_no=0 and (act_publique_on=1 or (act_publique_on=0 and act_auteur_no=$id_user)) order by act_nom_lb";
          elseif ($nb_act1 == 0 && $nb_act2 == 0 && $nb_act3 == 0 &&  $nb_act4 > 0)
             $requete_act="select * from activite where act_seq_no>0  and (act_publique_on=1 or (act_publique_on=0 and act_auteur_no=$id_user)) order by act_nom_lb";
          elseif ($nb_act1 == 0 && $nb_act2 == 0 && $nb_act3 == 0 &&  $nb_act4 == 0)
          {
             $lien_act="activite_free.php?creer=1&lesseq=2&miens=1&ordre_affiche=lenom&titre_act=$mess_liste_vos_act";
             $lien= urlencode($lien);
             echo "<script language=\"JavaScript\">";
             echo "document.location.replace(\"trace.php?link=$lien\")";
             echo "</script>";
             exit();
          }
       }
       if ($star == 1)
       {
          $letitre = "Mes favoris";
          $requete_act = "SELECT * from activite,stars where stars.star_item_id=activite.act_cdn and stars.star_user_id= $id_user and stars.star_type_no=3 order by act_nom_lb asc";
       }

       $requete_act = str_replace($encas,"",$requete_act);
       $act_query = mysql_query ($requete_act);
       $nb_act = mysql_num_rows($act_query);
       $affiche_act = $nb_act;
       if (!isset($_SESSION['nbr_pgs_act']))
          $nbr_pgs = GetDataField($connect,"select param_etat_lb from param_foad where param_typ_lb='nb_pg_act'","param_etat_lb");
       else
          $nbr_pgs = $_SESSION['nbr_pgs_act'];
       if ($nb_act > $nbr_pgs)
       {
          $nb_pages = ceil($nb_act/$nbr_pgs);
          $debut_liste = $nbr_pgs*$page;
          $le_debut = $debut_liste+1;
          if ($page > 0)
             $page_ret = $page-1;
          $page++;
          if ($debut_liste > $nb_act)
          {
             $debut_liste = 0;
             $le_debut = 1;
             $page = 0;
          }
          if ($debut_liste == 0)
             $le_debut = 1;
          $encas = " limit $debut_liste,$nbr_pgs";
       }
//       if ($nb_act > 45 && $medor != 1)
       if ($nb_act > $nbr_pgs)
          $requete_act .= $encas;
       $_SESSION['requete_act'] = $requete_act;
       $lecho = (($debut_liste + $nbr_pgs) < $nb_act) ? strval($le_debut)." - ".strval($debut_liste+$nbr_pgs) : strval($le_debut)." - $nb_act";
       $act_query = mysql_query ("$requete_act");
       $nb_act = mysql_num_rows($act_query);
       if ($medor != 1)
       {
         echo "<CENTER><TABLE bgColor='#298CA0' cellspacing='1' cellpadding='0' width='98%'><TR>";
         include ("liste_act.inc.php");
         echo "<TD valign='top' width='70%' height='100%' bgcolor='#FFFFFF'>";
         echo "<TABLE bgColor='#FFFFFF' cellspacing='1' cellpadding='0' border='0' width=100%>";
         echo "<TR><TD background=\"images/fond_titre_table.jpg\" colspan='2' height='40' align='center' valign='center'>".
              "<Font size='3' color='#FFFFFF'><B>".stripcslashes($titre_act)."</B></FONT></TD></TR>";
       }
         if ($medor == 1)
         {
            if ($proprio > 0)
            {
                   $nom_createur = GetDataField ($connect,"select util_nom_lb from utilisateur where util_cdn = $proprio","util_nom_lb");
                   $prenom_createur = GetDataField ($connect,"select util_prenom_lb from utilisateur where util_cdn = $proprio","util_prenom_lb");
                   $titre_act ="$mess_tts_act $de $prenom_createur $nom_createur";
            }
            echo "<CENTER><TABLE bgColor='#298CA0' cellspacing='1' cellpadding='0' width='98%'><TR>";
            include ("liste_act.inc.php");
            echo "<TD valign='top' width='70%' height='100%' bgcolor='#FFFFFF'>";
            echo "<TABLE bgColor='#FFFFFF' cellspacing='1' cellpadding='0' border='0' width=100%>";
            echo "<TR><TD background=\"images/fond_titre_table.jpg\" colspan='2' height='40' align='center' valign='center'>".
              "<Font size='3' color='#FFFFFF'><B>".stripcslashes($titre_act)."</B></FONT></TD></TR>";
         }
          echo aide_simple("activite");
          if ($medor == 1)
          {
             if ($nb_act == 0)
                echo "<TR><TD colspan='2' width='100%'><B>$mess_no_occur</B></TD></TR>";
             echo "<TR><TD colspan='2'><B>$mess_filtapp </B>  $msq_titre ";
             if ($keytitre != "") echo "<font color='#D45211'><B>".stripslashes($keytitre)."</B></font> , ";else echo "<B>$mess_nofiltre</B>, ";
             echo "$msq_aff_cons : ";
             if ($keydesc != "") echo "<font color='#D45211'><B>".stripslashes($keydesc)."</B></font> , ";else echo "<B>$mess_nofiltre</B>, ";
             echo "$mess_visdup : ";
             if ($keypub == 1) echo "<font color='#D45211'><B>$mess_oui</B></font> ";elseif($keypub == 0 && $keypub != "") echo "<font color='#D45211'><B>$mess_non</B></font> ";else echo "<B>$mess_nofiltre</B>";
             echo "</TD></TR>";
          }
//          $keytitre = "";$keypub = "";$keydesc = "";
          echo "<TR><TD colspan='2'><table cellpadding='6' cellspacing='0' width='100%' border='0'>";
          $affiche_rech = "";
          $affiche_rech = "<TD align='left'><form name='form'>";
          $affiche_rech .= "<SELECT name='select' class='SELECT' onChange=javascript:appel_w(form.select.options[selectedIndex].value)>";
          $affiche_rech .= "<OPTION selected></OPTION>";
          $req_aut_act = requete_order("DISTINCT act_auteur_no,util_nom_lb","activite as ACT,utilisateur as UT","ACT.act_auteur_no = UT.util_cdn","UT.util_nom_lb");
          $nb_aut_act = mysql_num_rows($req_aut_act);
          while ($data = mysql_fetch_object($req_aut_act))
          {
             $nom_aut = $data -> util_nom_lb;
             $num_aut = $data -> act_auteur_no;
             $miens_act = ($num_aut == $id_user) ? $miens : "";
             $lien = "activite_free.php?lesseq=$lesseq&ordre_affiche=lenom&proprio=$num_aut&medor=1&miens=$miens_act";
             $lien =  urlencode($lien);
             $affiche_rech .= "<OPTION value='trace.php?link=$lien'>$nom_aut</OPTION>";
          }
          $affiche_rech .= "</SELECT></TD>";
          echo "<FORM name='form2' ACTION='activite_free.php?lesseq=$lesseq&miens=$miens&medor=1&vient_de_search=1&titre_act=$titre_act' METHOD='POST'>";
          echo "<TR bgcolor= '#F4F4F4'><TD nowrap colspan='5'><B>$mrc_rech $mess_ParAutSuit</B></TD></TR>";
          echo "<TR>";
          if ($nb_aut_act > 1)
          echo "<TD nowrap>$mrc_aut</TD>";
          echo "<TD nowrap>$msq_titre</TD><TD nowrap>$msq_aff_cons</TD><TD nowrap>$mess_visdup</TD></TR>";
          echo "<TR>";
          if ($nb_aut_act > 1)
              echo $affiche_rech;
          echo "<TD nowrap><INPUT TYPE='text' class='INPUT' name='keytitre' size='20' align='middle'></TD>";
          echo "<TD nowrap><INPUT TYPE='text' class='INPUT' name='keydesc' size='20' align='middle'></TD>";
          echo "<INPUT type='HIDDEN' name='titre_act' value=\"$titre_act\">";
          echo "<TD nowrap>";
          echo "<SELECT name='keypub' class='SELECT'>";
          echo "<OPTION></OPTION>";
          echo "<OPTION value='1'>$mess_oui</OPTION>";
          echo "<OPTION value='0'>$mess_non</OPTION>";
          echo "</SELECT></TD>";
          echo "<TD align='center'><A HREF=\"javascript:document.form2.submit();\" onmouseover=\"img1.src='images/fiche_identite/boutvalidb.gif';return true;\" onmouseout=\"img1.src='images/fiche_identite/boutvalid.gif'\">".
               "<IMG NAME=\"img1\" SRC=\"images/fiche_identite/boutvalid.gif\" BORDER='0' onLoad=\"tempImg=new Image(0,0); tempImg.src='images/fiche_identite/boutvalidb.gif'\"></A>";
         echo "</TD></TR></form></TABLE></TD></TR>";
         echo "<TR height='5'><TD colspan='2'>&nbsp;</TD></TR>";
//       }
       if ($nb_act > 0)
       {
         echo"</TD></TR>";//</TABLE></TD></TR>
         echo "<TR><TD colspan='2' bgcolor='#FFFFFF'><table cellpadding='6' cellspacing='2' width='100%' border='0'>";
         echo "<TR bgcolor='#2b677a'>";
         $affiche_nb = ($affiche_act > $nbr_pgs) ? "[$lecho] sur $affiche_act" : "$affiche_act";
         $choixNbAff = "<div id='cNA' style=\"float:left;padding-right:4px;cursor:pointer;\">".
                     "<input type='text' class='INPUT' id='NBS' name='nbr_pgs_act' value='$nbr_pgs' size='2' maxlength='2' style='width:20px;' ".
                     bullet($msg_modNbPg,"","RIGHT","BELOW",180).
                     "onBlur=\"javascript:var nbs=getElementById(NBS);".
                     "appelle_ajax('admin/modif_nb.php?nbrAff_act='+NBS.value);\$(mien).empty();\" /></div>";
         if ($etat_fav == 'OUI')
         {
           $lien_star_search="activite_free.php?star=1&lesseq=$lesseq&miens=$miens&titre_act=$titre_act&medor=$medor&proprio=$proprio&keytitre=$keytitre&keypub=$keypub&keydesc=$keydesc&vient_de_search=$vient_de_search&page=$page_ret&debut_liste=$debut_liste&encas=$encas";
           $nb_star = mysql_num_rows(mysql_query("select * from stars where star_user_id='$id_user' and star_type_no=3"));
           if ($nb_star > 0)
           {
               $ajt_star = "<div id='starfull' class='star' " .
                           bullet($mess_menu_consult_fav_prop,"","RIGHT","BELOW",120)." onclick=\"javascript:parent.main.location.replace('trace.php?link=".urlencode($lien_star_search)."');\"><img src='images/starfull.gif' border=0></div>";
           }else
               $ajt_star = "<div id='starfull'  class='star'></div>";
         }
          else
            $ajt_star = "";
         echo "<TD height='20' align='left' nowrap>$ajt_star<div id='mpwo' style=\"float:left;padding-top:4px;padding-right:4px;\"><FONT COLOR=white><b>$msq_activite&nbsp;&nbsp;($affiche_nb)</div>";
         if ($page > 1)
            echo "<div id='flcG' style=\"float:left;padding-right:4px;padding-top:5px;cursor:pointer;\">".
                 "<A HREF=\"activite_free.php?lesseq=$lesseq&miens=$miens&titre_act=$titre_act&medor=$medor&proprio=$proprio&keytitre=$keytitre&keypub=$keypub&keydesc=$keydesc&vient_de_search=$vient_de_search&page=$page_ret&debut_liste=$debut_liste&encas=$encas\">".
                 "<img src='images/ecran-annonce/icogog.gif' border='0' title='$mess_page_prec'></A></div>";
            echo $choixNbAff;
         if ($nb_act == $nbr_pgs && $affiche_act > $debut_liste+$nbr_pgs)
            echo "<div id='flcG' style=\"float:left;padding-right:4px;padding-top:5px;cursor:pointer;\">".
                 "<A HREF=\"activite_free.php?lesseq=$lesseq&miens=$miens&titre_act=$titre_act&medor=$medor&proprio=$proprio&keytitre=$keytitre&keypub=$keypub&keydesc=$keydesc&vient_de_search=$vient_de_search&page=$page&debut_liste=$debut_liste&encas=$encas\">".
                 "<img src='images/ecran-annonce/icogod.gif' border='0' title='$mess_page_suiv'></A></div>";
         echo " </b></FONT></DIV></TD>";
         echo "<TD height='20' align='left'><FONT COLOR=white><b>$msq_ress_act</b></FONT></TD>";
         if (($miens != 1 || !isset($miens)) && ($proprio < 1 || !isset($proprio)))
             echo "<TD height='20' align='left'><FONT COLOR=white><b>$mrc_aut</b></FONT></TD>";
         echo "<TD height='20' align='left'><FONT COLOR=white><b>$msq_aff_cons</b></FONT></TD>";
         echo "<TD height='20' align='left'><FONT COLOR=white><b>$mess_modif_base</b></FONT></TD>";
         if ($lesseq == 0)
            echo "<TD height='20' align='left'><FONT COLOR=white><b>$mess_ag_supp</b></FONT></TD>";
         echo "<TD height='20' align='left'><FONT COLOR=white><b>$mess_dupliquer</b></FONT></TD>";
         echo "</TR>";
         $i = 0;
         while ($i < $nb_act)
         {
           $la_serie = "";$liens = "";
           $id_act = mysql_result($act_query,$i,"act_cdn");
           $id_seq = mysql_result($act_query,$i,"act_seq_no");
           $id_ress = mysql_result($act_query,$i,"act_ress_no");
           $titre = NewHtmlEntityDecode(mysql_result($act_query,$i,"act_nom_lb"));
           $droit_voir_act = mysql_result($act_query,$i,"act_publique_on");
           $consigne = html_entity_decode(DelAmp(mysql_result ($act_query,$i,"act_consigne_cmt")),ENT_QUOTES,'iso-8859-1');
           $commentaire = html_entity_decode(DelAmp(mysql_result ($act_query,$i,"act_commentaire_cmt")),ENT_QUOTES,'iso-8859-1');
           $act_auteur = mysql_result($act_query,$i,"act_auteur_no");
           $nom_auteur = GetDataField($connect,"select util_nom_lb from utilisateur where util_cdn = $act_auteur","util_nom_lb");
           $prenom_auteur = GetDataField($connect,"select util_prenom_lb from utilisateur where util_cdn = $act_auteur","util_prenom_lb");
           //$consigne = str_replace("\r","<BR>",$consigne);
           //$consigne = str_replace("\n","<BR>",$consigne);
           //dey Dfoad
              $req_media = mysql_result(mysql_query("select count(*) from activite_media where actmedia_act_no = $id_act"),0);
              $media_act = "";
              if ($req_media > 0)
              {
                 $media_act = GetDataField ($connect,"select ress_url_lb from ressource_new,activite_media where
                                            ress_cdn = actmedia_ress_no and actmedia_act_no =$id_act ","ress_url_lb");
              }
           $majuscule_act = "$prenom_auteur $nom_auteur";
           $date_creat_act = mysql_result ($act_query,$i,"act_create_dt");
           $ch_dtc_act = explode("-",$date_creat_act);
           $dtc_act =  strftime("%A %e %B %Y", mktime(0, 0, 0, $ch_dtc_act[1],$ch_dtc_act[2],$ch_dtc_act[0]));
           $date_modif_act = mysql_result ($act_query,$i,"act_modif_dt");
           $ch_dtm_act = explode("-",$date_modif_act);
           $dtm_act =  strftime("%A %e %B %Y", mktime(0, 0, 0, $ch_dtm_act[1],$ch_dtm_act[2],$ch_dtm_act[0]));
           if ($act_auteur != $id_user)
              $la_serie = $mrc_aut." : ".$majuscule_act."<BR>";
           $la_serie .= "&nbsp;- $mess_menu_gest_seq_ref : &nbsp;&nbsp; <B>$dtc_act</B><BR>&nbsp;&nbsp;- $mess_modif_dt :&nbsp;&nbsp; <B>$dtm_act</B><BR>";
           echo couleur_tr($i+1,'');
           echo "<TD height='20' align='left' valign='top'>";
           if ($etat_fav == 'OUI')
              {
                $nbr_star = mysql_num_rows(mysql_query("select * from stars where star_user_id='$id_user' and star_item_id='$id_act' and star_type_no=3"));
                if ($nbr_star > 0)
                {
                    $lien_star = "formation/star.php?numero=$id_act&dl=star$i&vider=1&i=$i&type=3";
                    $div_star = "<div id='star$i' class='star' ".
                              bullet($msg_fav_ot,"","RIGHT","BELOW",120)."><div id='lance' ".
                              "onclick=\"javascript:appelle_ajax('$lien_star');".
                              "\$('#mien').hide();\$('#star$i').html('');addContent_star('$lien_star');\"> ".
                              "<img src='images/starfull.gif' border=0></div></div>";
                 }
                 else
                 {
                    $lien_star = "formation/star.php?numero=$id_act&dl=star$i&remplir=1&i=$i&type=3";
                    $div_star = "<div id='star$i' class='star' ".
                              bullet($mess_menu_ajout_favori,"","RIGHT","BELOW",130)."><div id='lance' ".
                              "onclick=\"javascript:appelle_ajax('$lien_star'); ".
                              "\$('#mien').hide();\$('#star$i').html('');addContent_star('$lien_star');\"> ".
                              "<img src='images/starempty.gif' border=0></div></div>";
                  }
           }
           if ($lesseq == 1)
           {
              $req_seq = mysql_query ("SELECT seq_titre_lb FROM sequence WHERE seq_cdn = $id_seq");
              $la_serie .= "<B>".addslashes($mess_act_seq_inclus)."</B>";
              $p_nom = mysql_result($req_seq,0,"seq_titre_lb");
              $la_serie .="<LI>".$p_nom."</LI>";
           }
           if ($lesseq == 0)
           {
              $nb_req_seq = 0;
              $requete_grp = mysql_query ("select * from groupe order by grp_nom_lb");
              $nb_grp = mysql_num_rows($requete_grp);
              if ($nb_grp > 0)
              {
                  $gp=0;
                  while ($gp < $nb_grp)
                  {
                         $id_grp = mysql_result($requete_grp,$gp,"grp_cdn");
                         $grp_seq = mysql_result($requete_grp,$gp,"grp_nom_lb");
                         $req_seq = mysql_query ("SELECT suivi_seqajout_no,suivi_utilisateur_no FROM suivi1_$id_grp WHERE suivi_act_no = $id_act and suivi_seqajout_no > 0");
                         $nb_req_seq = mysql_num_rows($req_seq);
                         if ($nb_req_seq > 0)
                         {
                             $la_serie = "<B>".addslashes($mess_seq_actlib_presc)."</B> : ";
                             $psi=0;
                             while ($psi < $nb_req_seq)
                             {
                                $seq = mysql_result($req_seq,$psi,"suivi_seqajout_no");
                                $util = mysql_result($req_seq,$psi,"suivi_utilisateur_no");
                                $nom_util = GetDataField ($connect,"select util_nom_lb from utilisateur where util_cdn = $util","util_nom_lb");
                                $prenom_util = GetDataField ($connect,"select util_prenom_lb from utilisateur where util_cdn = $util","util_prenom_lb");
                                $titre_seq = GetDataField ($connect,"SELECT seq_titre_lb FROM sequence WHERE seq_cdn = $seq","seq_titre_lb");
                                $la_serie .="<LI><U>".$titre_seq."</U> $mess_app_actlib_presc : <U>$nom_util $prenom_util</U>  ".$mess_formation_in." : <U>$grp_seq</U></LI>";
                               $psi++;
                             }
                         }
                       $gp++;
                  }
              }
           }
           $ajoute_lien = "";
           $transit = 0;
           $req_typdev = mysql_result(mysql_query("select count(*) from activite_devoir where actdev_act_no = $id_act"),0);
           $dev_act = "";
           if ($req_typdev > 0)
                 $dev_act = GetDataField ($connect,"select actdev_dev_lb from activite_devoir where
                                            actdev_act_no = $id_act ","actdev_dev_lb");
           else
                 $dev_act = "Pas de devoir";
           if ($id_ress > 0)
           {
               $liens = GetDataField ($connect,"select ress_url_lb from ressource_new where ress_cdn = $id_ress","ress_url_lb");
               if( serveur_externe($liens) == $liens)
               {
                 $liens=urldecode(serveur_externe($liens));
                 $ajoute_lien = "<A HREF=\"javascript:void(0);\" onclick=\"window.open('$liens','',' resizable=yes,scrollbars=yes,status=no')\"";
               }
               if (strstr(strtolower($liens),"educagrinet") && ($transit == 0 || !isset($transit)))
               {
                 $lien = str_replace('acces.html','direct.html',$liens)."&url=$url_ress&auth_cdn=$auth_cdn";
                 $lien = urlencode($lien);
                 $ajoute_lien = "<A HREF=\"javascript:void(0)\" onclick=\"window.open('trace.php?link=$lien','','resizable=yes,scrollbars=yes,status=no')\"";
               }
               elseif (!strstr(strtolower($liens),"educagrinet") && ($transit == 0 || !isset($transit)) && $dev_act != 'xApi TinCan')
               {
                  if ($liens == "")
                     $ajoute_lien = "<A HREF=\"javascript:void(0);\" onclick=\"window.open('ress_virtuel.php?id_ress=$id_ress&id_act=$id_act','','width=600, height=400,resizable=yes,scrollbars=yes,status=no')\" ";
                  elseif (strstr(strtolower($liens),".flv") ||
                          strstr(strtolower($liens),".mp3") ||
                          strstr(strtolower($liens),".swf") ||
                          strstr(strtolower($liens),".mp4") ||
                          strstr(strtolower($liens),".ogv") ||
                          strstr(strtolower($liens),".webm"))
                     $liens = "lanceMedia.php?id_ress=$id_ress";
                  else
                     $ajoute_lien = "<A HREF=\"$liens\" target='_blank' ";
               }
               elseif($dev_act == 'xApi TinCan')
               {
                         $lien = $liens.TinCanTeach ('teacher|0|0|'.$id_act.'|0',$liens,$commentaire);
                         $ajoute_lien = "<A href=\"javascript:void(0);\" onclick=\"window.open('$lien','','resizable=yes,scrollbars=yes,status=no')\"";
               }
           }
           echo "$div_star";
           if ($act_auteur == $id_user && $miens != 1)
           {
              echo "<div style=\"float:left;padding-right:4px;\"><IMG SRC=\"images/gest_parc/icofeuilb.gif\" border='0'></div>";
              if ($droit_voir_act == 0)
                 echo "<div style=\"float:left;padding-right:4px;\"><IMG SRC=\"images/modules/tut_form/icocadenas.gif\" border='0' ".
                       bulle($mess_notdupli,"","LEFT","ABOVE",100)."</div>";
           }
           else
           {
              if ($droit_voir_act == 0)
                 echo "<div style=\"float:left;padding-right:4px;\"><IMG SRC=\"images/modules/tut_form/icocadenas.gif\" border='0' ".
                       bulle($mess_notdupli,"","LEFT","ABOVE",100)."</div>";
           }
           if ($id_ress == 0)
              $la_serie .= "<BR>$mess_gp_noress_ass";
           if ($la_serie != "")
              $ajoute_lien .= "";
           echo "<div style=\"float:left\"><A HREF=\"activite_free.php?consult_act=1&id_act=$id_act&laliste=liste_act.inc\" ".
                bulle($la_serie,"","RIGHT","ABOVE",312)."$titre</A></div></TD>";
           if ($liens != "" && $id_ress > 0)
           {
              echo "<TD valign='top'>$ajoute_lien ".
                   "onmouseover=\"img$i.src='images/ecran-annonce/icoGgob.gif';return true;\" onmouseout=\"img$i.src='images/ecran-annonce/icoGgo.gif'\">".
                   "<IMG NAME=\"img$i\" SRC=\"images/ecran-annonce/icoGgo.gif\" BORDER='0' ".
                   "onLoad=\"tempImg=new Image(0,0); tempImg.src='images/ecran-annonce/icoGgob.gif'\"></A></TD>";
           }
           elseif ($liens == "" && $id_ress > 0)
           {
              $le_lien = "ress_virtuel.php?id_ress=$id_ress&id_act=$id_act";
              echo "<TD valign='top'><Div id='sequence'><A HREF=\"javascript:void(0);\"".
                   " onClick=\"window.open('$le_lien',".
                   "'null','status=yes, directories=no,copyhistory=0, titlebar=no, toolbar=no, location=no, menubar=no, scrollbars=auto, resizable=yes');\"".
                   "onmouseover=\"img$i.src='images/ecran-annonce/icoGgob.gif';return true;\" onmouseout=\"img$i.src='images/ecran-annonce/icoGgo.gif'\">".
                   "<IMG NAME=\"img$i\" SRC=\"images/ecran-annonce/icoGgo.gif\" BORDER='0' ".
                   "onLoad=\"tempImg=new Image(0,0); tempImg.src='images/ecran-annonce/icoGgob.gif'\"></A></DIV></TD>";
           }
           elseif ($id_ress == 0)
               echo "<TD valign='top'>&nbsp;</TD>";
           if (($miens != 1 || !isset($miens)) && ($proprio < 1 || !isset($proprio)))
               echo "<TD height='20' align='left' valign='top'>$prenom_auteur $nom_auteur</TD>";
             //dey Dfoad
           $req_typdev = mysql_result(mysql_query("select count(*) from activite_devoir where actdev_act_no = $id_act"),0);
           $dev_act = "";
           if ($req_typdev > 0)
           {
                 $dev_act = GetDataField ($connect,"select actdev_dev_lb from activite_devoir where
                                            actdev_act_no = $id_act ","actdev_dev_lb"); 
           }
           else
                 $dev_act = "Pas de devoir";
           $class_act =  GetDataField ($connect,"select actdevico_style_lb from actdev_icone where
                                            actdevico_type_lb = \"$dev_act\" ","actdevico_style_lb");

           echo "<TD align='left' valign='top' ><div $class_act> ".ltrim($consigne)."</div>";
           //dey Dfoad
           if($media_act != "")
           {
                  $actit = $id_act;
                  $largeur = "220";
                  $hauteur = "140";
                  echo "<br />&nbsp";
                  include ("media.php");
           }
           if ($act_auteur == $id_user || $typ_user == "ADMINISTRATEUR")
           {
              $lien = "activite_free.php?creer=1&modifie_act=1&medor=$medor&act_a_modif=$id_act&id_seq=$id_seq&miens=$miens&lesseq=$lesseq&titre_act=$titre_act";
              $lien = urlencode($lien);
              echo "<TD width='2%' align='middle' valign='top'><a href=\"trace.php?link=$lien\" target='main'".
                   bulle($mess_modif_base,"","LEFT","ABOVE",40)."<IMG SRC=\"images/repertoire/icoGrenomfich20.gif\" border=0></A></TD>";
           }
           else
              echo "<TD>&nbsp;</TD>";
           if ($lesseq == 0)
           {
              if ($nb_req_seq == 0 && ($act_auteur == $id_user || $typ_user == "ADMINISTRATEUR"))
              {
                 $lien = "activite_free.php?supp_act=1&act_a_supp=$id_act&id_seq=$id_seq&miens=$miens&lesseq=$lesseq&titre_act=$titre_act";
                 echo "<TD align='middle' valign='top'><A href=\"javascript:void(0);\" onclick=\"javascript:return(confm('$lien'));\" target='main'".
                      bulle($msq_sup_act,"","LEFT","ABOVE",150).
                      "<IMG SRC=\"images/messagerie/icopoubelressour.gif\" height=\"20\" width=\"15\" BORDER=0></A></td>";
              }
              elseif ($nb_req_seq > 0 && ($act_auteur == $id_user || $typ_user == "ADMINISTRATEUR"))
              {
                 echo "<TD align='middle' valign='top'><A href=\"javascript:void(0); style='cursor:help;'\" ".
                      bulle($msq_act_no_supp,"","LEFT","ABOVE",250)."<IMG SRC=\"images/repertoire/icoptiinterdit.gif\" BORDER='0' ></A></td>";
              }
              elseif ($act_auteur != $id_user && $typ_user != "ADMINISTRATEUR")
              {
                 echo "<TD align='middle' valign='top'><A href=\"javascript:void(0);\" style='cursor:help;' ".
                      bulle("<B>$mrc_aut</B> : ($prenom_auteur $nom_auteur" ,"","LEFT","ABOVE",150).
                      "<IMG SRC=\"images/repertoire/icoptiinterdit.gif\" BORDER=0></A></td>";
              }
              else
                 echo "<TD>&nbsp;</TD>";
           }
           if ($droit_voir_act == 1)
           {
              $lien = "activite_free.php?dupli_act=1&act_a_dupli=$id_act&id_seq=0&miens=$miens&lesseq=$lesseq&titre_act=$titre_act";
              $lien = urlencode($lien);
              echo "<TD width='2%' align='middle' valign='top'><a href=\"trace.php?link=$lien\" target='main' ".
                   bulle($mess_dupli_act,"","LEFT","ABOVE",250)."<IMG SRC=\"images/repertoire/icoptiedit.gif\" BORDER=0></A></TD></TR>";
           }
           else
              echo "<TD>&nbsp;</TD>";
           echo "</TR>";
          $i++;
         }
         echo "</TABLE></TD></TR>";
    }
    echo "</TABLE></TD></TR></TABLE></TD></TR></TABLE>";
echo "<div id='mien' class='cms'></div></BODY></HTML>";
exit();
?>
