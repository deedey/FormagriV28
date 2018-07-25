<?php
//error_reporting(E_ALL);

if (!isset($_SESSION)) session_start();
if (!isset($_SESSION['id_user']) || $_SESSION['id_user'] == "")
{
  exit();
}
include ("include/UrlParam2PhpVar.inc.php");
require 'fonction.inc.php';
require 'admin.inc.php';
require "lang$lg.inc.php";
require "fonction_html.inc.php";
require "class/class_module.php";
require "langues/formation.inc.php";
require "langues/prescription.inc.php";
require "langues/graphique.inc.php";
dbConnect();
$date_op = date("Y-m-d H:i:s" ,time());
$heure_fiche = substr($date_op,11);
$date_fiche = substr($date_op,0,10);
// affecter groupe et tuteur en insertion
include ("style.inc.php");
  ?>
  <script type="text/javascript" src="OutilsJs/lib/ajax/ajax_cms.js"></script>
  <div id="affiche" class="Status"></div>
  <div id="mon_contenu" class="mon_contenu"
        onClick="javascript:$(document).ready(function() {if ($.browser.msie) {$('#mon_contenu').hide();}else{$('#mon_contenu').empty();}})"
        <?php echo "title=\"$mess_clkF\">";?>
  </div>
  <?php
if ($mode_user == 'tout')
     $typ_user = GetDataField ($connect,"select util_typutil_lb from utilisateur where util_cdn='$id_user'","util_typutil_lb");
else
     $typ_user = "RESPONSABLE_FORMATION";
if (isset($supp_tut) && $supp_tut == 1)
{
   $supprimer = mysql_query("DELETE FROM tuteur where tut_tuteur_no='$num_tut' AND tut_apprenant_no = '$num_app'");
   $nom_tut = GetDataField ($connect,"SELECT util_nom_lb from utilisateur where util_cdn='$num_tut'","util_nom_lb");
   $prenom_tut = GetDataField ($connect,"SELECT util_prenom_lb from utilisateur where util_cdn='$num_tut'","util_prenom_lb");
   $nom_app = GetDataField ($connect,"SELECT util_nom_lb from utilisateur where util_cdn='$num_app'","util_nom_lb");
   $prenom_app = GetDataField ($connect,"SELECT util_prenom_lb from utilisateur where util_cdn='$num_app'","util_prenom_lb");
   $mess_notif = "$prenom_tut $nom_tut $msgrp_notut $prenom_app $nom_app";
}
if (isset($ajout_app) && $ajout_app == 1)
{
  $exist_app = mysql_result(mysql_query("select count(*) from utilisateur_groupe where utilgr_utilisateur_no = $id_app and utilgr_groupe_no =$grp_resp"),0);
  if ($exist_app > 0)
  {
      $mess_notif = $msgPresc_No;
  }
  else
  {
   $responsable_grp = GetDataField ($connect,"SELECT grp_resp_no from groupe where grp_cdn  = $grp_resp","grp_resp_no");
   $nom_grp = GetDataField ($connect,"SELECT grp_nom_lb from groupe where grp_cdn  = $grp_resp","grp_nom_lb");
   $requete = mysql_query("SELECT * from groupe_parcours where gp_grp_no = '$grp_resp' order by gp_ordre_no asc");
   $nombre = mysql_num_rows($requete);
   $prescripteur = $id_user;
   $inscripteur = GetDataField ($connect,"select util_auteur_no from utilisateur where util_cdn='$utilisateur'","util_auteur_no");
   if ($nombre > 0)
   {
      $scormOk = 0;
      $nn = 0;$no=0;
      while ($nn < $nombre)
      {
            $num_parc = mysql_result($requete,$nn,"gp_parc_no");
            $titre_parc = GetDataField ($connect,"select parcours_nom_lb from parcours where parcours_cdn = $num_parc","parcours_nom_lb");
            $num_form = mysql_result($requete,$nn,"gp_formateur_no");
            $num_ordre = mysql_result($requete,$nn,"gp_ordre_no");
            $date_dgp = mysql_result($requete,$nn,"gp_db_dt");
            $nb_jdgp_req = mysql_query ("SELECT TO_DAYS('$date_dgp')");
            $nb_dgp = mysql_result ($nb_jdgp_req,0);
            $date_fgp = mysql_result($requete,$nn,"gp_df_dt");
            $nb_jfgp_req = mysql_query ("SELECT TO_DAYS('$date_fgp')");
            $nb_fgp = mysql_result ($nb_jfgp_req,0);
            $date_jour = date("Y-n-d");
            $nb_jours_req = mysql_query ("SELECT TO_DAYS('$date_jour')");
            $nb_jours_cour = mysql_result ($nb_jours_req,0);
            $nj_dif = $nb_fgp - $nb_dgp;
            //dey Dfoad-------------------------------------------------
            $nj_dif1 = $nb_fgp - $nb_jours_cour;
            $req_cfg = mysql_query("select ucfg_affgrp_on from user_config where ucfg_user_no = '$id_user'");
            $nb_cfg = mysql_num_rows($req_cfg);
            if ($nb_cfg == 1 && mysql_result($req_cfg,0,"ucfg_affgrp_on") == 1)
            {
               if ($nj_dif1 < 0)
               {
                  $mess_notif .= "<br />le module - ".strip_tags(addslashes($titre_parc)).
                                 " - est défini pour une durée négative ($nj_dif1). Il n'a pas été prescrit<br />".
                                 "Vous pourrez le lui prescrire individuellement en adaptant la bonne date";
                  $no++;
                  $nn++;
                  continue;
               }
               $date_deb = (($nb_dgp - $nb_jours_cour) > 0) ? $date_dgp : date("Y-n-d");
               $date_fin = $date_fgp;
            }
            elseif ($nb_cfg == 0 || ($nb_cfg == 1 && mysql_result($req_cfg,0,"ucfg_affgrp_on") == 0))
            {
                if ($nb_cfg == 0)
                    $ajt_cfg = mysql_query("insert into user_config values('','$id_user','0','0')");
               //-------------------------------------------------------------- partie ancienne----------------------
               $nj_fut = $nb_jours_cour + $nj_dif;
               $nouveau = mysql_query ("SELECT FROM_DAYS('$nj_fut')");
               if ($nb_jours_cour > $nb_dgp )
               {
                  $date_deb = (($nb_dgp - $nb_jours_cour) > 0) ? $date_dgp : date("Y-n-d");
                  $date_fin = mysql_result ($nouveau,0);
               }
               else
               {
                  $date_deb = $date_dgp;
                  $date_fin = $date_fgp;
               }
               //-----------------------------------------------------fin partie ancienne---------------------------------------------------
            }
            //fin dey Dfoad---------------------------------------------------------
            $id_ref = GetDataField ($connect,"SELECT parcours_referentiel_no from parcours where parcours_cdn = $num_parc","parcours_referentiel_no");
               //insertion dans la table inscription
               $id_suivi3 = Donne_ID ($connect,"SELECT max(suiv3_cdn) from suivi3_$grp_resp");
               $seq_parc_query = mysql_query ("SELECT sum(sequence.seq_duree_nb) from sequence,sequence_parcours where sequence_parcours.seqparc_parc_no = $num_parc and  sequence.seq_cdn = sequence_parcours.seqparc_seq_no");
               $duree_parc = mysql_result($seq_parc_query,0);
               $ins_suivi3 = mysql_query ("insert into suivi3_$grp_resp values ($id_suivi3,$id_app,$num_parc,'A FAIRE',$duree_parc,'$grp_resp')");
               if ($prescripteur == $inscripteur)
                  $qualite = $msq_prescripteur;
               elseif ($inscripteur == $id_user)
                  $qualite = $mess_inscripteur;
               elseif ($id_prescripteur != $inscripteur && $inscripteur != $id_user)
                  $qualite = $mess_typ_adm;
               $action_fiche = $mess_aff_forma;
               $commentaire = $mess_presc_fiche." ".$titre_parc ;
               $new_fiche = Donne_ID ($connect,"select max(fiche_cdn) from fiche_suivi");
               $req_fiche = mysql_query("INSERT INTO fiche_suivi (fiche_cdn,fiche_utilisateur_no,fiche_auteur_no,fiche_qualite_lb,fiche_date_dt,fiche_heure_dt,fiche_commentaire_cmt,fiche_grp_no,fiche_parc_no,fiche_seq_no,fiche_act_no,fiche_typaction_lb) VALUES($new_fiche,$id_app,$id_user,'$qualite','$date_fiche','$heure_fiche',\"$commentaire\",$grp_resp,$num_parc,0,0,\"$action_fiche\")");
               $seq_parc_query = mysql_query ("SELECT seqparc_seq_no,seqparc_ordre_no from sequence_parcours where seqparc_parc_no = $num_parc order by seqparc_cdn asc");
               $nb_seq = mysql_num_rows ($seq_parc_query);
               $pp = 0;
               while ($pp != $nb_seq)
               {
                      $kk = 0;
                      $seq = mysql_result ($seq_parc_query,$pp,"seqparc_seq_no");
                      $ordre_seq = mysql_result ($seq_parc_query,$pp,"seqparc_ordre_no");
                      $cherche_util = mysql_query ("SELECT presc_utilisateur_no from prescription_$grp_resp where presc_seq_no = $seq AND presc_utilisateur_no = $id_app");
                      $nb_fois = mysql_num_rows ($cherche_util);
                      if ($nb_fois == 0)
                      {
                           //Ici il faut faire le choix du prescripteur (soi --> $id_user ou le responsable du groupe $responsable_grp
                           // $prescripteur = $responsable_grp;
                           $formateur = $num_form;
                           // insertion dans la fiche_navette
                           if ($prescripteur == $inscripteur)
                             $qualite = $msq_prescripteur;
                           elseif ($inscripteur == $id_user)
                             $qualite = $mess_inscripteur;
                           elseif ($id_prescripteur != $inscripteur && $inscripteur != $id_user)
                             $qualite = $mess_typ_adm;
                           $action_fiche = $mess_aff_forma;
                           $titre_seq = GetDataField ($connect,"select seq_titre_lb from sequence where seq_cdn=$seq","seq_titre_lb");
                           $commentaire = $mess_presc_fiche." ".$titre_seq."\n $msq_parc : $titre_parc" ;
                           $new_fiche = Donne_ID ($connect,"select max(fiche_cdn) from fiche_suivi");
                           $req_fiche = mysql_query("INSERT INTO fiche_suivi (fiche_cdn,fiche_utilisateur_no,fiche_auteur_no,fiche_qualite_lb,fiche_date_dt,fiche_heure_dt,fiche_commentaire_cmt,fiche_grp_no,fiche_parc_no,fiche_seq_no,fiche_act_no,fiche_typaction_lb) VALUES($new_fiche,$id_app,$id_user,'$qualite','$date_fiche','$heure_fiche',\"$commentaire\",'$grp_resp',$num_parc,$seq,0,\"$action_fiche\")");
                           $id_presc = Donne_ID ($connect,"SELECT max(presc_cdn) from prescription_$grp_resp");
                           $ins_presc = mysql_query ("insert into prescription_$grp_resp values ($id_presc,$seq,$num_parc,$id_app,'$date_deb','$date_fin',$prescripteur,$formateur,'$grp_resp',$num_ordre)");
                           //Lors de prescription, activite et seq sont à faire
                           $duree_sequence = GetDataField ($connect,"SELECT seq_duree_nb from sequence where seq_cdn = '$seq'","seq_duree_nb");
                           $id_suivi2 = Donne_ID ($connect,"SELECT max(suiv2_cdn) from suivi2_$grp_resp");
                           $ins_suivi2 = mysql_query ("insert into suivi2_$grp_resp values ($id_suivi2,$id_app,$seq,'A FAIRE',$duree_sequence,$ordre_seq,'$grp_resp')");
                           //Selection des modulescorm de la sequence
                           $type_sequence = GetDataField ($connect,"SELECT seq_type_lb from sequence where seq_cdn = '$seq'","seq_type_lb");
                           $scormOk = (strstr($type_sequence,"SCORM")) ? 1 : 0;
                           if ($scormOk == 1)
                           {
                              $mod_query = mysql_query ("SELECT * from scorm_module where mod_seq_no = $seq order by mod_cdn");
                              $nb_mod = mysql_num_rows ($mod_query);
                              $k = 0;
                              while ($k != $nb_mod)
                              {
                                $id_mod= mysql_result ($mod_query,$k,"mod_cdn");
                                $id_suivi = Donne_ID ($connect,"SELECT max(user_module_cdn) from scorm_util_module_$grp_resp");
                                $ins_suivi = mysql_query ("insert into scorm_util_module_$grp_resp (user_module_cdn,user_module_no,mod_module_no,mod_grp_no) values ('$id_suivi','$id_app','$id_mod','$grp_resp')");
                               $k++;
                              } //fin while ($k != $nb_act)
                           }
                           else
                           {
                              //Selection des activites de la sequence
                              $act_query = mysql_query ("SELECT * from activite where act_seq_no = $seq");
                              $nb_act = mysql_num_rows ($act_query);
                              $k = 0;
                              while ($k != $nb_act)
                              {
                                  $act = mysql_result ($act_query,$k,"act_cdn");
                                  $act_flag = mysql_result ($act_query,$k,"act_flag_on");
                                  $ress_on = mysql_result ($act_query,$k,"act_ress_on");
                                  $ress = mysql_result ($act_query,$k,"act_ress_no");
                                  $id_suivi = Donne_ID ($connect,"SELECT max(suivi_cdn) from suivi1_$grp_resp");
                                  if ($ress == 0 && $ress_on == 'OUI')
                                     $ins_suivi = mysql_query ("insert into suivi1_$grp_resp (suivi_cdn,suivi_utilisateur_no,suivi_act_no,suivi_grp_no,suivi_etat_lb) values ($id_suivi,$id_app,$act,'$grp_resp','A FAIRE')");
                                  elseif ($ress == 0 && $ress_on == 'NON')
                                     $ins_suivi = mysql_query ("insert into suivi1_$grp_resp (suivi_cdn,suivi_utilisateur_no,suivi_act_no,suivi_grp_no,suivi_etat_lb) values ($id_suivi,$id_app,$act,'$grp_resp','PRESENTIEL')");
                                  else
                                     $ins_suivi = mysql_query ("insert into suivi1_$grp_resp (suivi_cdn,suivi_utilisateur_no,suivi_act_no,suivi_grp_no,suivi_etat_lb) values ($id_suivi,$id_app,$act,'$grp_resp','A FAIRE')");
                                 $k++;
                              } //fin while ($k != $nb_act)
                           }
                     }//if (nombre de fois
                     $pp++;
            }  //fin while ($pp != $nb_seq)
            $nn++;
      }//while ($nn < $nombre){
    }
    $nom_app = GetDataField ($connect,"SELECT util_nom_lb from utilisateur where util_cdn='$id_app'","util_nom_lb");
    $prenom_app = GetDataField ($connect,"SELECT util_prenom_lb from utilisateur where util_cdn='$id_app'","util_prenom_lb");
    if (($no < $nombre && $nombre > 0) || $nombre == 0)
    {
       $id_grp_utl = Donne_ID ($connect,"SELECT max(utilgr_cdn) from utilisateur_groupe");
       $inserer = mysql_query("INSERT INTO utilisateur_groupe (utilgr_cdn,utilgr_groupe_no,utilgr_utilisateur_no) VALUES ('$id_grp_utl','$grp_resp','$id_app')");
       $WkGrp = 10000 + $grp_resp;
       $reqWk = mysql_query("select * from wiki,wikiapp where wiki_seq_no= ".$WkGrp.
                     " and wkapp_seq_no=wiki_seq_no and wkapp_wiki_no=wiki_cdn group by wiki_cdn order by wiki_ordre_no");
       if (mysql_num_rows($reqWk) > 0)
       {
          while($itemWk = mysql_fetch_object($reqWk))
          {
            $id_wk = Donne_ID ($connect,"select max(wkapp_cdn) from wikiapp");
            $req = mysql_query("insert into wikiapp values ('$id_wk','".$itemWk->wiki_cdn."','".$id_app."','".
                      $WkGrp."','10000','".$grp_resp."','".$itemWk->wkapp_clan_nb."',\"".
                      date("Y-m-d")."\",\"".date("Y-m-d")."\")");
          }
       }
       $mess_notif .= "$prenom_app $nom_app $msgrp_inscOk : $nom_grp";
    }
    if (isset($ajout_grp) && $ajout_grp == 1)
      $grp_resp = 0;
  }
   unset($id_app);
}// fin $ajout_app
if (isset($mess_notif) && $mess_notif != '')
      echo notifier($mess_notif);
if (isset($affecte_groupe) && $affecte_groupe == 1 && (!isset($insert_groupe) || (isset($insert_groupe) && $insert_groupe != 1)))
{
         $id_classe = GetDataField ($connect,"select grp_classe_on from groupe where grp_cdn = $grp_resp","grp_classe_on");
         if ($typ_user == "ADMINISTRATEUR" || $typ_user == "RESPONSABLE_FORMATION")
         {
             if ($grp_resp == 0)
                 $titre = "$mess_suite_tit_presc";
             else
             {
                 $nom_grp =GetDataField ($connect,"SELECT grp_nom_lb from groupe where grp_cdn = $grp_resp","grp_nom_lb");
                 $crea_grp = GetDataField ($connect,"SELECT grp_resp_no from groupe where grp_cdn  = $grp_resp","grp_resp_no");
                 $tut_grp = GetDataField ($connect,"SELECT grp_tuteur_no from groupe where grp_cdn  = $grp_resp","grp_tuteur_no");
                 $resp_grp_nom = GetDataField ($connect,"SELECT util_nom_lb from utilisateur where util_cdn='$crea_grp'","util_nom_lb");
                 $resp_grp_prenom = GetDataField ($connect,"SELECT util_prenom_lb from utilisateur where util_cdn='$crea_grp'","util_prenom_lb");
                 if ($tut_grp > 0){
                     $tut_grp_nom = GetDataField ($connect,"SELECT util_nom_lb from utilisateur where util_cdn='$tut_grp'","util_nom_lb");
                     $tut_grp_prenom = GetDataField ($connect,"SELECT util_prenom_lb from utilisateur where util_cdn='$tut_grp'","util_prenom_lb");
                 }
                 $titre = "$mess_suite_ap_grp  \"$nom_grp\"";
                 $titre_s .= "$mess_resp : $resp_grp_prenom $resp_grp_nom, ";
                 if ($tut_grp > 0)
                     $titre_s .= " $mess_tut_forma : $tut_grp_prenom $tut_grp_nom";
                 if ($effet == 1)
                 {
                     $sous_titre = "<font size='2'><strong>$mess_suit_dch</strong></font>";
                     $effet = 0;
                 }
             }
         }
         if (isset($ret_supp) && $ret_supp == 1)
         {
            $nom_app = GetDataField ($connect,"SELECT util_nom_lb from utilisateur where util_cdn='$num_app'","util_nom_lb");
            $prenom_app = GetDataField ($connect,"SELECT util_prenom_lb from utilisateur where util_cdn='$num_app'","util_prenom_lb");
            $mess_notif = "$prenom_app $nom_app $mess_supp_app_form : $nom_grp";
         }
         //notification d'événement via le div 'mien' et la variable 'notif'
         entete_simple($titre);
         if ($grp_resp > 0)
            echo "<tr><td colspan='2' align='left'><div class='sous_titre'>$titre_s</div></td></tr>";
         if ( $sous_titre != "")
            echo "<tr><td colspan='2' align='left'><div class='sous_titre'>$sous_titre</div></td></tr>";
         echo "<tr bgcolor= \"#FFFFFF\"><td style=\"height: 25px;\">";
         if ($grp_resp != 0 && $grp_resp != 'tous'){
            $req_grp_util = mysql_query("SELECT utilgr_utilisateur_no from utilisateur_groupe where utilgr_groupe_no = $grp_resp");
            $numb = mysql_num_rows($req_grp_util);
            $g=0;
            while ($g < $numb)
            {
                   $app_grp = mysql_result($req_grp_util,$g,"utilgr_utilisateur_no");
                   $req_presc_util = mysql_query("SELECT count(*) from prescription_$grp_resp where presc_utilisateur_no = $app_grp");
                   $numb_presc = mysql_result($req_presc_util,0);
                   if ($numb_presc > 0){
                       $okay = 1;
                       break;
                   }
                   $g++;
            }
         }
         $lien = "lancement.php?numero_groupe=$grp_resp&id_grp=$grp_resp&groupe=1&planning=normal";
         $lien = urlencode($lien);
         if ($okay == 1)
         {
           echo "<div id='grph' style=\"float:left; margin-left: 2px; margin-right: 6px;\">";
           echo "<A HREF=\"trace.php?link=$lien\" target='main' class='bouton_new'>";
           echo "$msgrph_avanti</A></div>";
         }
         echo "<div id='aide' style=\"float:left; margin-left: 2px;\">".
              aide_div("affectation",0,0,0,0)."</div></td></tr>";
      //  ajout apprenant au sommet
      if ($grp_resp > 0)
      {
    //dey Dfoad
      $req_cfg = mysql_query("select ucfg_affgrp_on from user_config where ucfg_user_no = '$id_user'");
      $nb_cfg = mysql_num_rows($req_cfg);
      if ($nb_cfg == 0)
         $ajt_cfg = mysql_query("insert into user_config values('','$id_user','0','0')");
       $chx_date = mysql_result(mysql_query("select ucfg_affgrp_on from user_config where ucfg_user_no = '$id_user'"),0,"ucfg_affgrp_on");
       echo "<TR height='30'><TD align='left'>";
       if ($chx_date == '0')
       {
          ?>
         <div class='sous_titre' style='margin:4px 20px 4px 30px;'>
           <div style="clear:both;">
               <div  style="float:left;margin-left:10px;">
                  <input type="radio" name="chxDt" id="chxDt" checked onMouseDown="javascript:$.ajax({
                                              type: 'GET',
                                              url: 'admin/config.php',
                                              data: 'AffectApp=1&chxDt=2',
                                              beforeSend:function(){
                                                 $('#affiche').addClass('Status');
                                                 $('#affiche').append('Opération en cours....');
                                              },
                                              success: function(msg){
                                                 $('#mien').css('padding','4px');
                                                 $('#mien').show();$('#mien').html(msg);
                                                 $('#affiche').empty();
                                                 $('#bouton1').html('<span style=\'color:#D45211;font-weight:bold;\'>Votre configuration actuelle</span> : La date de fin de prescription pour tout nouvel apprenant sera <b>calculée</b> et donc décalée de telle sorte que la durée soit calquée sur celle de la formation.');
                                                 $('#bouton2').html('<b>Choisir cette configuration</b> : La date de fin de prescription de tout nouvel apprenant inscrit sera celle des différents modules de la formation quelle que soit la durée restante de la formation.');
                                              }});
                                              setTimeout(function() {
                                                 $('#mien').hide();
                                              },5000);" />
               </div>
               <div id="bouton1" style="margin-left:30px;">
                   <span style="color:#D45211;font-weight:bold;">
                         Votre configuration actuelle
                   </span> :
                   La date de fin de prescription pour tout nouvel apprenant sera
                   <b>calculée</b> et donc décalée de telle sorte que la durée soit calquée sur celle de la formation.
               </div>
         </div>
         <div style="clear:both;margin-top:10px;">
              <div  style="float:left;margin-left:10px;">
                <input type="radio" name="chxDt" id="chxDt" onMouseDown="javascript:$.ajax({
                                              type: 'GET',
                                              url: 'admin/config.php',
                                              data: 'AffectApp=1&chxDt=1',
                                              beforeSend:function(){
                                                  $('#affiche').addClass('Status');
                                                  $('#affiche').append('Opération en cours....');
                                              },
                                              success: function(msg){
                                                  $('#mien').css('padding','4px');
                                                  $('#mien').show();$('#mien').html(msg);
                                                  $('#affiche').empty();
                                                  $('#bouton1').html('<b>Choisir cette configuration</b> : La date de fin de prescription pour tout nouvel apprenant sera <b>calculée</b> et donc décalée de telle sorte que la durée soit calquée sur celle de la formation.');
                                                  $('#bouton2').html('<span style=\'color:#D45211;font-weight:bold;\'>Votre configuration actuelle</span> : La date de fin de prescription de tout nouvel apprenant inscrit sera celle des différents modules de la formation quelle que soit la durée restante de la formation.');
                                              }});
                                              setTimeout(function() {
                                                  $('#mien').hide();
                                              },5000);" />
              </div>
              <div id="bouton2" style="margin-left:30px;"><b>Choisir cette configuration</b> :
                   La date de fin de prescription de tout nouvel apprenant inscrit sera celle des différents modules de la formation
                   quelle que soit la durée restante de la formation.
              </div>
         </div>
       </div>
       <?php
      }
      elseif ($chx_date == '1')
      {
          ?>
         <div class='sous_titre' style='margin:4px 20px 4px 30px;'>
           <div style="clear:both;">
              <div  style="float:left;margin-left:10px;">
                 <input  type="radio" name="chxDt" id="chxDt" checked onMouseDown="javascript:$.ajax({type: 'GET',url: 'admin/config.php',data: 'AffectApp=1&chxDt=1',
                                              beforeSend:function(){$('#affiche').addClass('Status');$('#affiche').append('Opération en cours....');},
                                              success: function(msg){$('#mien').css('padding','4px');$('#mien').show();$('#mien').html(msg);$('#affiche').empty();
                                                   $('#bouton2').html('<b>Choisir cette configuration</b> : La date de fin de prescription pour tout nouvel apprenant sera <b>calculée</b> et donc décalée de telle sorte que la durée soit calquée sur celle de la formation.');
                                                   $('#bouton1').html('<span style=\'color:#D45211;font-weight:bold;\'>Votre configuration actuelle</span> : La date de fin de prescription de tout nouvel apprenant inscrit sera celle des différents modules de la formation quelle que soit la durée restante de la formation.');
                                              }});setTimeout(function() {$('#mien').hide();},5000);" />

              </div>
              <div id="bouton1" style="margin-left:30px;"><span style="color:#D45211;font-weight:bold;">Votre configuration actuelle </span> :
                   La date de fin de prescription de tout nouvel apprenant inscrit sera celle des différents modules de la formation
                   quelle que soit la durée restante de la formation.
              </div>
          </div>
          <div style="clear:both;margin-top:10px;">
            <div  style="float:left;margin-left:10px;">
               <input type="radio" name="chxDt" id="chxDt" onMouseDown="javascript:$.ajax({type: 'GET',url: 'admin/config.php',data: 'AffectApp=1&chxDt=2',
                                              beforeSend:function(){$('#affiche').addClass('Status');$('#affiche').append('Opération en cours....');},
                                              success: function(msg){$('#mien').css('padding','4px');$('#mien').show();$('#mien').html(msg);$('#affiche').empty();
                                                   $('#bouton2').html('<span style=\'color:#D45211;font-weight:bold;\'>Votre configuration actuelle</span> : La date de fin de prescription pour tout nouvel apprenant sera <b>calculée</b> et donc décalée de telle sorte que la durée soit calquée sur celle de la formation.');
                                                   $('#bouton1').html('<b>Choisir cette configuration</b> : La date de fin de prescription de tout nouvel apprenant inscrit sera celle des différents modules de la formation quelle que soit la durée restante de la formation.');
                                              }});setTimeout(function() {$('#mien').hide();},5000);" />
            </div>
            <div id="bouton2" style="margin-left:30px;"><b>Choisir cette configuration</b> :
                   La date de fin de prescription pour tout nouvel apprenant sera
                   <b>calculée</b> et donc décalée de telle sorte que la durée soit calquée sur celle de la formation.
            </div>
         </div>
        </div>
       <?php
      }
       echo "</TD></TR>";
   // fin de dey Dfoad
         echo "<tr><td><span style=\"font-weight:bold;font-size:12px;\">$msgrp_ajtapform</span></td></tr>";
         echo "<tr>";
         echo "<td valign='top' colspan='3'>";
         if ($typ_user == "RESPONSABLE_FORMATION" || $typ_user == "ADMINISTRATEUR")
            $list_app = requete_order("util_cdn,util_nom_lb,util_prenom_lb,util_auteur_no","utilisateur","util_typutil_lb = 'APPRENANT' AND util_flag = 0","util_nom_lb,util_prenom_lb ASC");
         if ($list_app)
         {
            echo "<form name='formAjout' id='formAjout'>";
            echo "<SELECT name='selectAjout' class='SELECT' onChange=\"appel_w(formAjout.selectAjout.options[selectedIndex].value);\">";//;
            echo "<option> - - - - - - -</option>";
            while ($item = mysql_fetch_object($list_app))
            {
                $id_app = $item->util_cdn;
                $exist_app = mysql_result(mysql_query("select count(*) from utilisateur_groupe where utilgr_utilisateur_no = $id_app AND utilgr_groupe_no = $grp_resp"),0);
                if ($exist_app == 0)
                {
                   $nom_app = $item->util_nom_lb;
                   $prenom_app = $item->util_prenom_lb;
                   $id_auteur = $item->util_auteur_no;
                   $le_nom = $nom_app." ".$prenom_app;
                   $lien = urlencode("gestion_affectation.php?ajout_app=1&affecte_groupe=1&grp_resp=$grp_resp&id_app=$id_app");
                   if (($typ_user == 'RESPONSABLE_FORMATION' && ($crea_grp == $id_user || $id_auteur == $id_user)) || $typ_user == 'ADMINISTRATEUR')
                      echo "<option value=\"trace.php?link=$lien\">$le_nom</option>";
                }
            }
            echo "</select></form></td>";
         }
         //$passe_ici = 1;
      }//fin if $grp_resp > 0
         echo "<tr><td>";
         echo "<center><table cellspacing='0' cellpadding='3' align='top' width='100%' border='0'>";
         echo "<tbody><tr bgcolor='#2B677A' style=\"height: 25px;\">";
         echo "<td></td><td align=left><font COLOR=white><strong>$msq_apprenant(s)</strong></font></td>";
          if ($grp_resp > 0 || $grp_resp == "tous")
            echo "<td colspan=2>";
          elseif ($grp_resp == 0 && $typ_user == "ADMINISTRATEUR")
            echo "<td colspan=2>";
          else
            echo "<td colspan=2>";
          echo "<font COLOR=WHITE><strong>$mpr_tut </strong></font></td>";
          echo "<td><font COLOR=WHITE><strong> $mpr_chx_tut</strong></font></td>";
          if ($typ_user == "ADMINISTRATEUR")
          {
             echo "<td><font COLOR=white><strong>$msg_ajtF</strong></font></td>";
             if ($grp_resp > 0)
             {
                echo "<td align=center><font COLOR=white><strong>".ucfirst(strtolower($mpr_suivi))."</strong></font></td>";
                if ($id_classe == 1 || $classe == 1)
                   echo "<td align=center><font COLOR=white><strong>$mess_parc_form</strong></font></td>";
                else
                   echo "<td>&nbsp;</td>";
                echo "<td align='middle'><font COLOR=white><strong>$mess_bilan</strong></font></td>";
                echo "<td align=center><font COLOR=white><strong>$mess_alrt</strong></font></td>";
                echo "<td align='middle'><font COLOR=white><strong>$mess_ag_supp</strong></font></td>";
             }
             elseif($grp_resp == 0)
                echo "<td><font COLOR=white><strong>$msg_frm_presc</strong></font></td>";
             echo "</tr>";
          }
          elseif ($typ_user == "RESPONSABLE_FORMATION" && $grp_resp > 0)
             echo "<td align='middle'><font COLOR=white><strong>$mess_ag_supp</strong></font></td></tr>";
          if ($typ_user == "ADMINISTRATEUR")
          {
            if ($grp_resp > 0 && $grp_resp != "tous" )
              $requete ="SELECT * FROM utilisateur,utilisateur_groupe WHERE util_typutil_lb = 'APPRENANT' AND util_cdn =utilgr_utilisateur_no AND utilgr_groupe_no = $grp_resp ORDER BY util_nom_lb,util_prenom_lb asc";
            elseif ($grp_resp == 0)
              $requete ="SELECT * from utilisateur WHERE util_typutil_lb = 'APPRENANT' ORDER BY utilisateur.util_nom_lb,utilisateur.util_prenom_lb asc";
            else
              $requete ="SELECT * from utilisateur WHERE util_typutil_lb = 'APPRENANT' ORDER BY utilisateur.util_nom_lb,utilisateur.util_prenom_lb asc";
          }
          elseif ($typ_user == "RESPONSABLE_FORMATION")
          {
            if ($grp_resp > 0 && $grp_resp != "tous")
            {
            if ($mode_insc == 1)
               $requete ="SELECT * from utilisateur,utilisateur_groupe WHERE util_typutil_lb = 'APPRENANT' AND util_cdn = utilgr_utilisateur_no AND utilgr_groupe_no = $grp_resp AND util_auteur_no = $id_user ORDER BY util_nom_lb,util_prenom_lb asc";
             else
                $requete ="SELECT * from utilisateur,utilisateur_groupe WHERE util_typutil_lb = 'APPRENANT' AND util_cdn = utilgr_utilisateur_no AND utilgr_groupe_no = $grp_resp ORDER BY util_nom_lb,util_prenom_lb asc";
            }
            elseif ($grp_resp == "tous")
            {
              $requete ="SELECT * from utilisateur,utilisateur_group,groupe WHERE util_typutil_lb = 'APPRENANT' AND util_cdn = utilgr_utilisateur_no AND utilgr_groupe_no =grp_cdn AND grp_resp_no = $id_user ORDER BY util_nom_lb,util_prenom_lb asc";
            }
            elseif ($grp_resp == 0)
            {
              $req_gp = mysql_query("SELECT * from utilisateur_groupe");
              $nomb_gp = mysql_num_rows($req_gp);
              if ($nomb_gp > 0)
                 $requete ="SELECT * from utilisateur utilisateur_groupe WHERE util_typutil_lb = 'APPRENANT' AND util_auteur_no= $id_user AND util_cdn != utilgr_utilisateur_no GROUP BY util_cdn ORDER BY util_nom_lb,util_prenom_lb asc";
              else
                 $requete ="SELECT * from utilisateur WHERE util_typutil_lb = 'APPRENANT' AND util_auteur_no= $id_user ORDER BY util_nom_lb,util_prenom_lb asc";
            }
          }
          $user_query = mysql_query ("$requete");
          $nb_user = mysql_num_rows ($user_query);
          if ($nb_user == 0)
          {
             echo notifier($mpr_noapp_insc);
             echo "</td></tr></tbody></table>";
          }
          else
          {
            $id_app=array();
            $id_tut=array();
            $id_grp=array();
            $change_tut=array();
            $sup_tut=array();
            $change_grp=array();
            $j = 0;
            while ($j < $nb_user)
            {
              $id_grp[$j] = "";
              $id_app[$j] = mysql_result($user_query,$j,"util_cdn");
              $req_grp = mysql_query("select utilgr_groupe_no from utilisateur_groupe where utilgr_utilisateur_no = $id_app[$j] ");
              $nomb_grp = mysql_num_rows($req_grp);
              if ($nomb_grp != 0 && $grp_resp != 'tous'  && $grp_resp != 0)
                 $id_grp[$j] = $grp_resp;
              if ($id_grp[$j] != ''  && $grp_resp == 0 && $grp_resp != 'tous')
              {
                $j++;
                continue;
              }
              $inscripteur = GetDataField ($connect,"SELECT util_auteur_no from utilisateur where util_cdn = $id_app[$j]","util_auteur_no");
              if ($typ_user == 'RESPONSABLE_FORMATION' && $crea_grp != $id_user && $inscripteur != $id_user)
              {
                 $j++;
                 continue;
              }
              $nom = mysql_result($user_query,$j,"util_nom_lb");
              $prenom = mysql_result($user_query,$j,"util_prenom_lb");
              $id_email = GetDataField ($connect,"SELECT util_email_lb  from utilisateur where util_cdn = $id_app[$j]","util_email_lb");
              $id_photo = GetDataField ($connect,"SELECT util_photo_lb  from utilisateur where util_cdn = $id_app[$j]","util_photo_lb");
              $id_login = GetDataField ($connect,"SELECT util_login_lb  from utilisateur where util_cdn = $id_app[$j]","util_login_lb");
              $id_webmail = GetDataField ($connect,"SELECT util_urlmail_lb  from utilisateur where util_cdn = $id_app[$j]","util_urlmail_lb");
              $id_tel = GetDataField ($connect,"SELECT util_tel_lb  from utilisateur where util_cdn = $id_app[$j]","util_tel_lb");
              $id_com = GetDataField ($connect,"SELECT util_commentaire_cmt  from utilisateur where util_cdn = $id_app[$j]","util_commentaire_cmt");
//              $id_tut[$j] = GetDataField ($connect,"SELECT tut_tuteur_no  from tuteur where tut_apprenant_no = $id_app[$j]","tut_tuteur_no");
              // procédure de regroupement de emails pour un envoi groupé
              if ($j == ($nb_user-1))
                 $send_to .=$id_email;
              else
                 $send_to .= "$id_email,";
              if (isset($id_tut[$j]) && $id_tut[$j] !=0)
              {
                 $nom_tut = GetDataField ($connect,"SELECT util_nom_lb  from utilisateur where util_cdn = $id_tut[$j]","util_nom_lb");
                 $prenom_tut = GetDataField ($connect,"SELECT util_prenom_lb  from utilisateur where util_cdn = $id_tut[$j]","util_prenom_lb");
              }
              $id_com = str_replace("'","\'",$id_com);
              $id_util =$id_app[$j];
              $numerotation = $j+1;
              $req_tut = mysql_query("SELECT tut_tuteur_no from tuteur where tut_apprenant_no = $id_util order by tut_cdn");
              $nomb_tut = mysql_num_rows($req_tut);
              if ($nomb_tut > 0)
                 $hautTR =25+$nomb_tut*22;
              else
                 $hautTR = 25;
              echo couleur_tr($j+1,$hautTR);
              echo "<td align=left valign='top'>";
              $lien = "admin/ChartsStatements.php?groupe=$grp_resp&who=".$id_util."_".$id_login."|".
                      str_replace('http://','',str_replace('.educagri.fr','',$nom_url)).
                      TinCanTeach ('formateur|0|0|0|0',$adresse_http.'/admin/ChartsStatements.php?groupe='.$grp_resp.'&who='.
                      $id_util.'_'.$id_login.'|'.str_replace('http://','',str_replace('.educagri.fr','',$nom_url)),$adresse_http.'/Suivi');
              echo "<script language='javascript'>
                       $.ajax({
                           type: 'GET',
                           url: 'http://formagri.educagri.fr/TinCanApi/statementsCharts.php',
                           data: 'comptage=1&groupe=$grp_resp&who=".$id_util."_".$id_login."|".str_replace('http://','',str_replace('.educagri.fr','',$nom_url))."',
                           success: function(msg)
                           {
                               if (msg == 'Rien')
                               $('#BilanAcces_$id_util').css('display','none');
                               else
                               {
                               $('#BilanAcces_$id_util').html('<img src=\"images/icones/ampoule18.gif\" ".
                               " border=0 style=\"padding-bottom:3px;\">');
                               }
                               //alert(msg);
                           }
                       });
                  </script>";
              echo "<div><A HREF = \"$lien\" title='Historique des accès et bilan sur le LRS' target='blank'>".
              "<div id='BilanAcces_$id_util' style='clear:both:float:left;width:20px;'></div></A></div>";
              echo "</td>";
              $majuscule =$nom." ".$prenom;
              $lien = "prescription.php?affiche_fiche_app=1&identite=1&id_util=$id_util&stopper=1";
              $lien = urlencode($lien);
              echo "<td nowrap valign='top'><div id='sequence' style='float:left;'><A HREF=\"javascript:void(0);\" ".
                   "onclick=\"window.open('trace.php?link=$lien','','width=600,height=380,resizable=yes,status=no');\"";
              if ($id_photo != ""){
                 list($w_img, $h_img, $type_img, $attr_img) = getimagesize("images/$id_photo");
                 echo " onMouseOver=\"overlib('', TEXTCOLOR, '#ffffff', TEXTSIZE, 2, WIDTH, $w_img, HEIGHT, $h_img, BACKGROUND, 'images/$id_photo', PADX, 60, 20, PADY, 20, 20);\" onMouseOut=\"nd();\"";
              }else
                echo " target='main' ".bulle($mess_suite_fp,"","CENTER","",120);
              echo "<strong> $majuscule</strong></A></div>";
              echo msgInst($id_app[$j],"apprenant car il est connecté")."</td>";
              $utilisateur = $id_app[$j];
              if ($grp_resp > 0)
              {
                $compte_presc=mysql_query("SELECT count(*) from prescription_$grp_resp where presc_utilisateur_no=$id_util");
                $nbr_presc=mysql_result($compte_presc,0);
              }
              echo"<td colspan='2' style=\"width:150px;\" valign='top' align='left'>";
              echo "<div id='tut$j' class='tuteurs' style=\"width:150px;\"><table cellspacing='0' cellpadding='2' border='0' width=100%><tbody>";
              if ($nomb_tut > 0)
              {
                $i_t = 0;
                while ($i_t < $nomb_tut)
                {
                   echo "<tr><td width='90%'>";
                   $num_tut = mysql_result($req_tut,$i_t,"tut_tuteur_no");
                   $nom_tut=GetDataField ($connect,"SELECT util_nom_lb from utilisateur where util_cdn='$num_tut'","util_nom_lb");
                   $prenom_tut=GetDataField ($connect,"SELECT util_prenom_lb from utilisateur where util_cdn='$num_tut'","util_prenom_lb");
                   $photo_tut = GetDataField ($connect,"SELECT util_photo_lb from utilisateur where util_cdn = $num_tut","util_photo_lb");
                   $lien="prescription.php?affiche_fiche_app=1&identite=1&id_util=$num_tut&stopper=1";
                   $lien= urlencode($lien);
                   echo "<div id='sequence'><A HREF=\"javascript:void(0);\" onclick=\"window.open('trace.php?link=$lien','','width=600,height=340,resizable=yes,status=no')\"";
                   if ($photo_tut != "")
                   {
                     list($w_img, $h_img, $type_img, $attr_img) = getimagesize("images/$photo_tut");
                     echo " onMouseOver=\"overlib('',CENTER,ABOVE,TEXTCOLOR, '#ffffff', TEXTSIZE, 2, WIDTH,$w_img, HEIGHT, $h_img, BACKGROUND, 'images/$photo_tut', PADX, 60, 20, PADY, 20, 20);\" onMouseOut=\"nd();\">";
                   }
                   else
                   {
                     $titrer="$mpr_consult_fic $prenom_tut $nom_tut";echo bulle($titrer,"","LEFT","",160);
                     $titrer = "";
                   }
                   echo "<span style='font-size:11px;font-weight:bold;'> $prenom_tut $nom_tut</span></A></div></td>";
                   if ($inscripteur == $id_user || $crea_grp = $id_user || $typ_user == "ADMINISTRATEUR")
                   {
                     $lien = "gestion_affectation.php?affecte_groupe=1&grp_resp=$grp_resp&supp_tut=1&num_app=$id_util&num_tut=$num_tut";
                     $lien=urlencode($lien);
                     echo "<td width='10%'><A href=\"trace.php?link=$lien\" ".
                          "onclick=\"return(conf());\" target='main'".
                          bulle($mess_ag_supp,"","LEFT","",100).
                          "<IMG SRC=\"images/messagerie/icoGpoubel.gif\" width=\"10\" height=\"15\" BORDER=0></A></td>";
                   }
                   else
                      echo "<td>&nbsp;</td>";
                  $i_t++;
                }
                echo "</tr></tbody></table>";
                echo "</div></td>";
              }
              elseif ($nomb_tut == 0 && ($typ_user == "RESPONSABLE_FORMATION" && $inscripteur == $id_user) || $typ_user == "ADMINISTRATEUR" || $crea_grp = $id_user)
              {
                  //$message = "$mpr_chx_tut";
                  echo "<tr><td></td></tr>";
                  echo "</tbody></table>";
                  echo "</div></td>";
              }
              echo "<td valign='top'>";
              if (($typ_user == "RESPONSABLE_FORMATION" && $inscripteur == $id_user) || $typ_user == "ADMINISTRATEUR" || $crea_grp = $id_user){
                if ($typ_user == "RESPONSABLE_FORMATION" && ($inscripteur == $id_user || $crea_grp = $id_user))
                   $list_tut = requete_order("util_cdn,util_nom_lb,util_prenom_lb","utilisateur","util_typutil_lb != 'APPRENANT' AND util_flag = 0","util_nom_lb,util_prenom_lb ASC");
                elseif($typ_user == "ADMINISTRATEUR")
                   $list_tut = requete_order("util_cdn,util_nom_lb,util_prenom_lb","utilisateur","util_typutil_lb != 'APPRENANT' AND util_flag = 0","util_nom_lb,util_prenom_lb ASC");
                echo "<form name='form$j' id='form$j'>";
                echo "<SELECT name='select$j' class='SELECT' onChange=\"javascript:if (form$j.select$j.options[selectedIndex].value != '- - - - - - - -')".
                     "{".
                     "    appelle_ajax(form$j.select$j.options[selectedIndex].value);".
                     "    var mon_content=document.getElementById('mien');mon_content.style.visibility='hidden';".
                     "    addContent(form$j.select$j.options[selectedIndex].value);".
                     "}".
                     "document.location='#sommet';\">";
                echo "<option value = '- - - - - - - -'>- - - - - - - -</option>";
                while ($item = mysql_fetch_object($list_tut))
                {
                      $id_tut = $item->util_cdn;
                      $nom_tut = $item->util_nom_lb;
                      $prenom_tut = $item->util_prenom_lb;
                      $le_nom = $nom_tut." ".$prenom_tut;
                      $lg_nom = strlen($le_nom);
                      $son_nom = ($lg_nom > 22) ? substr($le_nom,0,20).".." : $le_nom  ;
                      echo "<option value=\"formation/gere_tut.php?ajout_tut=1&idtut=tut$j&id_tut=$id_tut&grp_resp=$grp_resp&id_util=$id_util\">$son_nom</option>";
                }
                echo "</select></form></td>";
              }
            if ($typ_user == "ADMINISTRATEUR")
            {
                $id_grp[$j] = GetDataField ($connect,"SELECT utilgr_groupe_no from utilisateur_groupe where utilgr_utilisateur_no = ".$id_app[$j],"utilgr_groupe_no");
                echo "<td valign='top'>";
                if ($typ_user == "ADMINISTRATEUR" || ($typ_user == "RESPONSABLE_FORMATION" && $inscripteur == $id_user))
                {
                   if (!$id_grp[$j] && $grp_resp != "tous")
                      $req = "SELECT distinct grp_cdn,grp_nom_lb from groupe where (grp_publique_on=1 OR (grp_publique_on=0 AND grp_resp_no=$id_user)) AND grp_flag_on=1 ORDER BY grp_nom_lb";
                   else
                      $req = "SELECT distinct grp_cdn,grp_nom_lb from groupe,utilisateur_groupe where (groupe.grp_cdn != utilisateur_groupe.utilgr_groupe_no AND utilisateur_groupe.utilgr_utilisateur_no=".$id_app[$j].") AND (grp_publique_on=1 OR (grp_publique_on=0 AND grp_resp_no=$id_user)) AND grp_flag_on=1 ORDER BY grp_nom_lb";
                   echo "<form name='formg$j' id='formg$j'>";
                   echo "<SELECT name='selectg$j' class='SELECT' onChange=\"appel_w(formg$j.selectg$j.options[selectedIndex].value);document.location='#sommet';\">";//;
                   echo "<option> - - - - - - -</option>";
                   $res_grp = mysql_query($req);
                   $nbLig = mysql_num_rows($res_grp);
                   $rq=0;
                   if ($nbLig > 0)
                   {
                       while ($item = mysql_fetch_object($res_grp))
                       {
                              $num_grp = $item->grp_cdn;
                              $nom_grp = $item->grp_nom_lb;
                              $carac_grp = strlen($nom_grp);
                              if ($carac_grp > 22)
                                 $le_nom = substr($nom_grp,0,20)."..";
                              else
                                 $le_nom = $nom_grp;
                              $req_grp_exist = mysql_query("SELECT count(*) from utilisateur_groupe where utilgr_groupe_no= $num_grp AND utilgr_utilisateur_no = ".$id_app[$j]);
                              $grp_exist = mysql_result($req_grp_exist,0);
                              if ($grp_exist == 0)
                              {
                                 $lien = urlencode("gestion_affectation.php?ajout_app=1&ajout_grp=1&affecte_groupe=1&grp_resp=$num_grp&id_app=$id_util");
                                 echo "<option value=\"trace.php?link=$lien\">$le_nom</option>";
                              }
                       }
                   }
                   echo "</select></form></td>";
                }
                if ($grp_resp < 1)
                {
                   $param = "";
                   echo "<td nowrap valign='top'><table><tbody>";
                      Ascenseur_affichage ("id_grp","SELECT distinct groupe.grp_cdn from groupe,utilisateur_groupe where groupe.grp_cdn = utilisateur_groupe.utilgr_groupe_no AND utilisateur_groupe.utilgr_utilisateur_no=$id_app[$j] ORDER BY grp_nom_lb",$connect,$param,$id_app[$j],$lg);
                   echo "</tbody></table></td>";
                }
             if ($nbr_presc > 0 && $grp_resp > 0)
             {
                $lien="gest_parc_frm1.php?saut=1&a_faire=1&utilisateur=$utilisateur&numero_groupe=$grp_resp&tout=1&graph=1";
                $lien = urlencode($lien);
                $titrer = "$mess_lanc_mess1 $prenom $nom<br />$mess_auth_click $mess_lanc_mess2";
                echo "<td align='middle' width='20' valign='top'><div id='sequence'><A href=\"trace.php?link=$lien\" target='main' ".bulle($titrer,"","RIGHT","",220).
                     "<img SRC=\"images/ecran-annonce/icogotr.gif\" border='0' ></A></div></td>";
                     $titrer = "";
             }
             elseif ($grp_resp > 0 && $nbr_presc == 0)
                echo "<td>&nbsp;</td>";
              // Afficher la fiche de renseignements de l'apprenant....................
             if ($id_grp[$j] != "" && $grp_resp > 0)
             {
                $numero_groupe=$id_grp[$j];
                $inscripteur = GetDataField ($connect,"SELECT util_auteur_no from utilisateur where util_cdn = $id_app[$j]","util_auteur_no");
                $nomb_grp_util = 0;
                $req_grp_util = mysql_query("select utilgr_groupe_no from utilisateur_groupe where utilgr_utilisateur_no = $id_app[$j]");
                $nomb_grp_util = mysql_num_rows($req_grp_util);
                if ($grp_resp == 0)
                {
                  $compte_presc=mysql_query("SELECT count(*) from prescription_$grp_resp where presc_utilisateur_no=$utilisateur");
                  $nbr_presc=mysql_result($compte_presc,0);
                }
                if (($id_classe == 1 || $classe == 1) && $grp_resp > 0)
                {
                   if ($nbr_presc > 0 && $grp_resp > 0)
                   {
                      $lien = "modif_parc.php?a_faire=1&utilisateur=$utilisateur&hgrp=$hgrp&numero_groupe=$grp_resp";
                      $lien = urlencode($lien);
                      if ($inscripteur == $id_user || $typ_user == "ADMINISTRATEUR")
                      {
                         $titrer = "$mess_menu_mod_presc $de $nom $prenom";
                         echo "<td align=middle valign='top'><a href=\"trace.php?link=$lien\" target='main' ".bulle($titrer,"","RIGHT","",180).
                              "<IMG SRC=\"images/modules/tut_form/pictoarbo.gif\"  border=0></A></td>";
                         $titrer = "";
                      }
                      else
                         echo "<td>&nbsp;</td>";

                   }
                   elseif (($nomb_grp_util == 1 || $nbr_presc == 0) && $grp_resp > 0 && $inscripteur == $id_user)
                   {
//                      $lien = "prescription.php?numero_groupe=$grp_resp&id_grp=$grp_resp&prem=1&presc=appren&utilisateur=$utilisateur";
                      $lien = "modif_parc.php?a_faire=1&utilisateur=$utilisateur&hgrp=$hgrp&numero_groupe=$grp_resp";
                      $lien = urlencode($lien);
                      echo "<td align='center' valign='top'><a href=\"trace.php?link=$lien\" target='main' ".bulle($mess_menu_presc,"","RIGHT","",180).
                           "<IMG SRC=\"images/modules/tut_form/icoformulprescip.gif\" width='25' height='20' border=0 alt=\"$mess_menu_presc\"></A></td>";
                   }
                   else
                      echo "<td>&nbsp;</td>";

                }
                else
                      echo "<td>&nbsp;</td>";
                if ($grp_resp != "tous" && isset($grp_resp))
                {
                   $nbr_trq = mysql_result(mysql_query ("select count(*) from traque where traque.traq_util_no = $utilisateur".
                                          " AND traque.traq_grp_no= $grp_resp"),0);
                   if ($nbr_trq > 0)
                   {
                      $lien = "bilan.php?bilan=1&utilisateur=$utilisateur&numero_groupe=$grp_resp";
                      $lien = urlencode($lien);
                      $titrer = "$mess_menu_bilan $de $majuscule";
                      echo "<td align='middle' valign='top'><A  HREF=\"javascript:void(0);\" ".
                           "onclick=\"open('trace.php?link=$lien','window','scrollbars,resizable=yes,width=550,height=500')\" ".
                           bulle($titrer,"","CENTER","",180).
                           "<IMG SRC=\"images/ecran-annonce/icohisto.gif\" border=0 height='24' width='30'></A></td>";
                        $titrer = "";
                   }
                   else
                      echo "<td>&nbsp;</td>";
                }
                if ($grp_resp > 0)
                {
                    $lien = "message.php?type=apprenant&num=$utilisateur&son_groupe=$grp_resp";
                    $lien = urlencode($lien);
                    $titrer = "$mess_alert $pour $prenom $nom";
                    echo "<td align=center valign='top'><A HREF=\"#\" onclick=\"window.open('trace.php?link=$lien','','width=600,height=300,resizable=yes,status=no')\" ".
                    bulle($titrer,"","LEFT","",160)."<IMG SRC=\"images/modules/anoter.gif\" BORDER=0></A></td>";
                    $titrer = "";
                }
             }
            }
            if (($inscripteur == $id_user || $typ_user == 'ADMINISTRATEUR' || $crea_grp == $id_user) && $grp_resp > 0)
            {
                   if ($nbr_presc > 0)
                      $le_message = $mpr_sup_presc;
                   else
                      $le_message = $mpr_sup_grp;
                   $lien = "prescription.php?supprimer_app=1&num=$utilisateur&id_grp=$grp_resp";
                   $lien = urlencode($lien);
                   $titrer = $le_message;
                   echo "<td align=middle valign='top'><A href=\"trace.php?link=$lien\" ".
                        "onclick=\"return(conf());\" target='main'".
                        bulle($titrer,"","LEFT","",220).
                        "<IMG SRC=\"images/messagerie/icopoubelressour.gif\" border=0></A></td>";
                   $titrer = "";
            }
            echo"</tr>";
           $j++;
          }

      }
      if ($grp_resp > 0 and $nb_user > 6)
      {
         echo "<tr><td colspan='3'><span style=\"font-weight:bold;font-size:12px;\">$msgrp_ajtapform</span></td></tr>";
         echo "<tr>";
         echo "<td valign='top' colspan='3'>";
         if ($typ_user == "RESPONSABLE_FORMATION" || $typ_user == "ADMINISTRATEUR")
            $list_app = requete_order("util_cdn,util_nom_lb,util_prenom_lb,util_auteur_no","utilisateur","util_typutil_lb = 'APPRENANT' AND util_flag = 0","util_nom_lb,util_prenom_lb ASC");
         if ($list_app)
         {
            echo "<form name='form' id='form'>";
            echo "<SELECT name='select' class='SELECT' onChange=\"appel_w(form.select.options[selectedIndex].value);document.location='#sommet';\">";//;
            echo "<option> - - - - - - -</option>";
            while ($item = mysql_fetch_object($list_app))
            {
                $id_app = $item->util_cdn;
                $exist_app = mysql_result(mysql_query("select count(*) from utilisateur_groupe where utilgr_utilisateur_no = $id_app AND utilgr_groupe_no = $grp_resp"),0);
                if ($exist_app == 0)
                {
                   $nom_app = $item->util_nom_lb;
                   $prenom_app = $item->util_prenom_lb;
                   $id_auteur = $item->util_auteur_no;
                   $le_nom = $nom_app." ".$prenom_app;
                   $lien = urlencode("gestion_affectation.php?ajout_app=1&affecte_groupe=1&grp_resp=$grp_resp&id_app=$id_app");
                   if (($typ_user == 'RESPONSABLE_FORMATION' && ($crea_grp == $id_user || $id_auteur == $id_user)) || $typ_user == 'ADMINISTRATEUR')
                      echo "<option value=\"trace.php?link=$lien\">$le_nom</option>";
                }
            }
            echo "</select></form></td>";
         }
         $passe_ici = 1;
      }//fin if $grp_resp > 0
      if ($typ_user == "RESPONSABLE_FORMATION" && ((isset($passe_ici) && $passe_ici == 0) || !isset($passe_ici))) echo "<tr><td colspan=3 align='right'>";else echo "<td align='center' valign='top'>";
      if ($typ_user == "ADMINISTRATEUR" && ((isset($passe_ici) && $passe_ici == 0) || !isset($passe_ici))) echo "<tr><td colspan=6 align='right' valign='top'>";else echo "<td align='right' valign='top'>";
      $crea_grp = GetDataField ($connect,"SELECT grp_resp_no from groupe where grp_cdn  = $grp_resp","grp_resp_no");
      if (($typ_user == "ADMINISTRATEUR" || $crea_grp == $id_user) && $grp_resp > 0 && $nb_user > 1)
      {
         $lien = "prescription.php?supp_tous=1&id_grp=$grp_resp";
         $lien = urlencode($lien);
         echo "<td valign='top'>$bouton_gauche<A href=\"trace.php?link=$lien\" onclick=\"return(conf());\" target='main' ".
              bulle($msgPrscSuptts,"","RIGHT","ABOVE",180)." $mess_tt_supp</A>$bouton_droite</td>";
      }
      if ($typ_user == "RESPONSABLE_FORMATION" || $typ_user == "ADMINISTRATEUR")
         echo "</tr></tbody></table>";
      echo "</td></tr></tbody></table></td></tr></tbody></table>";
}// fin affecte_groupe et tuteur à un apprenant
/*
$html=ob_get_clean();
echo htmlCompress($html);
*/
function htmlCompress($str)
{
$str = preg_replace("/[[:space:]]+/", ' ', $str);
$str = str_replace("> <", '><', $str);
$str = str_replace(" >", '>', $str);
$str = str_replace("< ", '<', $str);
return str_replace("\r\n", '', $str);
}
?>
<div id="mien" class="cms"></div>
</body>
</html>
