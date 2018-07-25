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
//------------------------------------
include ("include/UrlParam2PhpVar.inc.php");
if (!isset($_SESSION['id_user']) || $_SESSION['id_user'] == "")
{
  exit();
}
require "admin.inc.php";
require 'fonction.inc.php';
require 'fonction_html.inc.php';
require 'include/fonction_annonce.php';
require "lang$lg.inc.php";
//include ("click_droit.txt");
dbConnect();
if (isset($_SESSION['mode_user']) && $_SESSION['mode_user'] == 'tout')
   $typ_user = GetDataField ($connect,"select util_typutil_lb from utilisateur where util_cdn='$id_user'","util_typutil_lb");
else
   $typ_user = "FORMATEUR_REFERENT";
$_SESSION['typ_user'] = $typ_user;
include ('include/entete_annonce_inc.php');
//------------------------------------------------------------------------------------------------------------------
$Register = base64url_encode($_COOKIE["monID"].'_'.$_COOKIE["monLogin"].'|'.str_replace('http://','',str_replace('.educagri.fr','',$adresse_http)));
if ($typ_user == "RESPONSABLE_FORMATION")
    $hgrp = 1;
echo "<CENTER><TABLE bgColor='#298CA0' cellspacing='2' cellpadding='0' width='97%' border=0><TR><TD>";
echo "<TABLE bgColor='#FFFFFF' cellspacing='1' cellpadding='0' width='100%' border=0>";
echo "<TR><TD background=\"images/fond_titre_table.jpg\"  height='37' align='center' ".
     "valign='middle' colspan='2'><Font size='3' color='#FFFFFF'><strong>$mess_suivi_app_form</strong></FONT></TD></TR>";
echo "<TR><TD colspan='2'>";
?>
<SCRIPT language=JavaScript type="text/javascript">
    function checkForm(frm)
    {
          var ErrMsg = "<?php echo $mess_info_no;?>\n";
          var lenInit = ErrMsg.length;
          if (isEmpty(frm.liste_envoi)==true)
             ErrMsg += ' - <?php echo $mess_email_dest;?>\n';
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
</SCRIPT>
<?php
$requete_grp = mysql_query ("select * from groupe order by grp_nom_lb");
$nomb_grp = mysql_num_rows($requete_grp);
if ($nomb_grp > 0)
{
   $num_grp = 0;
   $nomb_grp_form=0;
   while ($item_grp = mysql_fetch_object($requete_grp))
   {
      $id_grp = $item_grp->grp_cdn;
      $req_grp_form = mysql_query ("select prescription_$id_grp.presc_grp_no from
                                    prescription_$id_grp WHERE
                                    presc_formateur_no = '$id_user' group by presc_grp_no");
      $nomb_grp_form = mysql_num_rows($req_grp_form);
      if ($nomb_grp_form == 0)
          continue;
      if (isset($affiche_groupe) && $affiche_groupe == 3)
      {
        $chx = 0;
        $sens_aff = $mess_replier;
      }
      else
      {
        $chx = 3;
        $sens_aff = $mess_deplier;
      }
      echo "<TABLE width='100%' border=0>";
      $lien = "annonce_suivi.php?affiche_groupe=$chx";
      $lien = urlencode($lien);
      if ($nomb_grp_form > 0 && $num_grp == 0)
      {
         echo "<tr><td><table><tr><td align='left' valign='middle' style=\"padding: 6px;\">";
         echo "<A href=\"trace.php?link=$lien\" class='bouton_new'>$sens_aff</A></td>".
              aide("suivi_pedagogique_form",'')."</tr></table></td></tr>";
      }
      $nom_grp =$item_grp->grp_nom_lb;
      $resp_grp =$item_grp->grp_resp_no;
      $id_classe = $item_grp->grp_classe_on;
      $carac_grp = strlen($nom_grp);
      if ($carac_grp > 33)
         $nom_grp1 = substr($nom_grp,0,31)."..";
      else
         $nom_grp1 = $nom_grp;
      $desc_grp =$item_grp->grp_formobject_lb;
      if ($id_classe == 0)
      {
          $desc_grp = "<BR>$mess_indiv_alrt";
          $desc_grp =str_replace(chr(146),chr(39),$desc_grp);
          $desc1_grp =addcslashes($desc_grp,"\0..\47!@\176..\255");
      }
      else
          $desc1_grp = "";
      if ($nomb_grp_form > 0)
      {
        echo "<TR height='30'>".
             "<TD colspan='6' style=\"color: #FFFFFF;\" width='100%'>".
             "<TABLE border='0' width='100%' cellspacing='0' cellpadding='0'>";
        $log_der = Donne_ID($connect,"SELECT max(log_cdn) from log WHERE login='$login' AND date_fin !='0000-00-00'");
        if ($log_der != 1)
        {
          $log = $log_der-1;
          $date_der = GetDataField ($connect,"select date_fin from log WHERE log_cdn = $log","date_fin");
          $heure_der = GetDataField ($connect,"select heure_fin from log WHERE log_cdn = $log","heure_fin");
          $date_compare =  $date_der." ".$heure_der;
        }
        $i=0;
        while ($i < $nomb_grp_form)
        {
             $nb_fiche_presc = 0;
             $nb_fiche_act = 0;
             $nb_fiche_apprec = 0;
             $nb_fiche_obs = 0;
             if ((!isset($groupe_affiche[$id_grp]) || $groupe_affiche[$id_grp] == 0))
             {
               $choix = 1;$mess_aff = $list_app_grp;
             }
             else
             {
               $choix = 0;$mess_aff = $mess_close_list;
             }
             if ((isset($groupe_affiche[$id_grp]) && $groupe_affiche[$id_grp] == 1) || (isset($affiche_groupe) && $affiche_groupe == 3))
                echo "<tr><td colspan='6' style=\"padding-left: 3px;padding-right: 3px;\">".
                     "<div id='tut_separe' class='individ'><table width=100%>";
             else
                echo "<tr><td colspan='6' style=\"padding-left: 3px;padding-right: 3px;\"><table width=100%>";
             if ($i > 0)
                echo "<TR height='2'><TD colspan = '5' bgcolor='#FFFFFF'></TD></TR>";
             echo couleur_tr($i+1,30);
             $lien = "annonce_suivi.php?groupe_affiche[$id_grp]=$choix";
             $lien=urlencode($lien);
             echo "<TD valign='middle'>";
             if ($choix == 1)
             {
                echo "<A HREF=\"trace.php?link=$lien\"".
                     " onmouseover=\"img_flb$i.src='images/gest_parc/flechv01b.gif';".
                     "overlib('".addslashes($mess_aff)."',ol_hpos,RIGHT,ABOVE,WIDTH,'250',DELAY,'500');return true;\"".
                     " onmouseout=\"img_flb$i.src='images/gest_parc/flechv01.gif';nd();\">".
                     "<IMG NAME=\"img_flb$i\" SRC=\"images/gest_parc/flechv01.gif\" border='0' valign='top'".
                     " onLoad=\"tempImg=new Image(0,0); tempImg.src='images/gest_parc/flechv01b.gif'\"></A>";
             }else
             {
                echo "<A HREF=\"trace.php?link=$lien\"".
                     " onmouseover=\"img_flh$i.src='images/gest_parc/flech02b.gif';".
                     "overlib('".addslashes($mess_aff)."',ol_hpos,RIGHT,ABOVE,WIDTH,'250',DELAY,'500');return true;\"".
                     " onmouseout=\"img_flh$i.src='images/gest_parc/flech02.gif';nd();\">".
                     "<IMG NAME=\"img_flh$i\" SRC=\"images/gest_parc/flech02.gif\" border='0' valign='top'".
                     " onLoad=\"tempImg=new Image(0,0); tempImg.src='images/gest_parc/flech02b.gif'\"></A>";
             }
             if ($resp_grp == $id_user)
                echo nbsp(1)."<IMG SRC=\"images/gest_parc/icofeuilb.gif\" border='0'>".nbsp(1);
             else
                echo nbsp(4);
             echo "<A HREF=\"trace.php?link=$lien\"";
             echo " onMouseOver=\"overlib('$mess_aff',ol_hpos,RIGHT,ABOVE,WIDTH,'200',DELAY,'800',CAPTION, '<strong>".
                  addslashes($nom_grp).$desc1_grp."</strong>')\" onMouseOut=\"nd()\">$nom_grp1</A></TD>";
             // Debut Wiki
             $grp_parc = mysql_query("SELECT gp_parc_no from groupe_parcours where gp_grp_no =$id_grp ORDER BY gp_ordre_no");
             $nb_f = mysql_num_rows($grp_parc);
             $numWkG = 10000+$id_grp;
             $nbWkGrp = mysql_num_rows(mysql_query("select * from wikiapp where wkapp_seq_no=$numWkG"));
             if ($nb_f > 0 || $nbWkGrp > 0)
             {
                           echo "<td nowrap width=20%>";
                           if ($nb_f > 0)
                           {
                               $lien = "MindMapper.php?id_grp=$id_grp";
                               echo "<div style='clear:both;float:left;'>$bouton_gauche<a href=\"$lien\" title='Voir le schéma de cette formation'>";
                               echo "MindMap</A>$bouton_droite</div>";
                           }
                           // Debut Wiki
                           if ($nbWkGrp > 0)
                           {
                                $titrer = "Wiki(s)";
                                $lien = "wiki/wikiGrp.php?id_seq=$numWkG&id_parc=10000&id_grp=$id_grp&keepThis=true&TB_iframe=true&height=260&width=550";
                                echo "<div style='float:right;margin-left:12px;'>$bouton_gauche<A HREF=\"$lien\"  class='thickbox'".
                                     " name='Wiki(s) de la formation ' title='Documents en ligne communs à cette formation'>".
                                     "$titrer</A>$bouton_droite</div>";
                                $titrer ='';
                           }
                           $wk = 1;
                           echo "</td>";
             }
             else
             {
                           echo "<td nowrap width=20%>&nbsp;</td>";
                           $wk = 0;
             }
                        // fin Wiki
             $lien = "lancement.php?numero_groupe=$id_grp&id_grp=$id_grp&groupe=1&hgrp=1";
             $lien = urlencode($lien);
             $titrer =$mpr_consult_grp." ".$nom_grp;
             echo "<TD align='center' valign='middle' width='15%'>$bouton_gauche<A HREF=\"trace.php?link=$lien\"".
                   bulle($titrer,"","CENTER","ABOVE",250)."$mess_aventi</A>$bouton_droite</TD>";
             $titrer='';
             $lien_rp = "modif_rep_fic.php?id_grp=$id_grp&formateurs=&nom_grp=$nom_grp&communes_groupe=1".
                        "&rallonge=ressources/groupes/$id_grp&sousdos=ressources/groupes/$id_grp".
                        "&parent=ressources/groupes&dossier=ressources/groupes/$id_grp&fichier=$id_grp&direct=dossier";
             $lien_rp = urlencode($lien_rp);
             echo "<TD align='center' valign='middle' width='25%' nowrap>";
             echo "$bouton_gauche<A HREF=\"trace.php?link=$lien_rp\">$mess_menu_casier_groupe $mess_menu_gestion_grp</A>$bouton_droite</TD>";
             // accès forum du groupe
             $id_forum = GetDataField($connect, "select id from forums where name ='$nom_grp'", "id");
             $comment_forum = GetDataField($connect, "select name from forums where id='$id_forum'", "name");
             $course = base64url_encode('formateur|0|0|-|'.$id_grp);
             $lien_forum = 'forum/list.php?f='.$id_forum.'&collapse=1'.TinCanTeach ('formateur|0|0|-|'.$id_grp,'forum/list.php?f='.$id_forum.'&collapse=1',$adresse_http.'/Forums - ');
             if ($_SESSION['onLine'] == 1)
             {
                           $leCours =($utilisateur > 0) ? '|0|0|-|'.$id_grp : '|0|0|0|'.$id_grp;
                           $course =  base64url_encode('formateur'.$leCours);
                           $lien_forum = 'forum/list.php?f='.$id_forum.'&collapse=1'.TinCanTeach ('apprenant'.$leCours,'forum/list.php?f='.$id_forum.'&collapse=1',$adresse_http.'/Forums - ');
                           echo '<TD align="center" valign="middle" width="30%"><table cellpadding=0 cellspacing=0><tr><td>'.$bouton_gauche.'<div id="forum" style="float:left;padding-right:4px;">'.
                                '<A HREF=\''.$lien_forum.'\'><div id="forum_'.$id_forum.'" style="float:left;">'.$mess_menu_forum.' '.
                                $mess_menu_gestion_grp.'</div></A></div>'.$bouton_droite.'</td><td>';
             }
             else
             {
                           $lien = "forum/list.php?f=$id_forum&collapse=0";
                           $lien=urlencode($lien);
                           echo  "<TD align='left'><A HREF='trace.php?link=$lien'>$mess_menu_forum $mess_menu_gestion_grp</A></TD>";
             }
             $lien = "admin/statements.php?course=$course&utilisateur=$utilisateur&registration=$Register&Activity=forum&collapse=1&keepThis=true&TB_iframe=true&height=450&width=750";
             if ($_SESSION['onLine'] == 1)
             {
                      echo "<div id='statement_F_$id_forum' style='float:left;display:none;'>
                             <script language='javascript'>
                             $.ajax({
                                type: 'GET',
                                url: 'http://lms.annulab.com/TinCanApi/statements.php',
                                data: 'verb=1&utilisateur=$utilisateur&course=$course&registration=$Register',
                                success: function(msg)
                                {
                                    if (msg == 'En attente')
                                        $('#tincan_F_$id_forum').css('display','none');
                                    else
                                    {
                                        $('#xApi_F_$id_forum').html('<img src=\"images/icones/ampoule18.gif\" ".
                                        " border=0 title=\"Consultations du forum\" style=\"float:left;padding-bottom:3px;\">');
                                        $('#tincan_F_$id_forum').css('display','block');
                                    }
                                }
                            });
                           </script></div>";
                        echo "<div id='tincan_F_$id_forum' style='float:left;display:none;'><A HREF = \"$lien\" ".
                          "name='Forum ".$nom_grpe." : historique' class='thickbox'>".
                          "<div id='xApi_F_$id_forum' style='float:left;background-image:".
                          "url(\"images/ecran-annonce/ongl01.gif\");margin-left:2px;'></div></A></div></td></tr></table></TD>";
             }
             echo "</TR>";
             if ((isset($groupe_affiche[$id_grp]) && $groupe_affiche[$id_grp] == 1) || (isset($affiche_groupe) && $affiche_groupe == 3))
             {
               $req_app = mysql_query("SELECT distinct prescription_$id_grp.presc_utilisateur_no from
                                       prescription_$id_grp,utilisateur where
                                       presc_formateur_no = $id_user and
                                       presc_utilisateur_no = utilisateur.util_cdn
                                       order by utilisateur.util_nom_lb");
               $nomb_app = mysql_num_rows($req_app);
               echo "<TR>";
               $wdtTD = ($wk == 1) ? 6 : 5;
               echo "<TD colspan='$wdtTD' width='100%' align='right'><TABLE cellspacing=1 cellpadding=3 border='0'>";
               echo "<TR bgcolor=\"#2B677A\"><TD width='40' bgcolor=\"#FFFFFF\">&nbsp;</TD>";
               echo "<TD  width='180' valign='top'><FONT COLOR= 'white' SIZE='1'><strong>$msq_apprenant</strong></FONT></TD>";
               if ($nomb_app > 1)
               {
                  $lien= "annuaire.php?vientde=annonce&id_grp=$id_grp";//&entantque=form
                  $lien = urlencode($lien);
                  echo "<TD width='50' valign='middle' align='left'><A HREF=\"javascript:void(0);\"".
                       " onclick=\"open('trace.php?link=$lien','window','scrollbars=no,resizable=yes,width=680,height=520')\"".
                       " onMouseOver=\"overlib('".addslashes("$mess_mail_avert $mess_mail_cert_app")."',ol_hpos,".
                       "CENTER,ABOVE,WIDTH,'220',DELAY,'500',CAPTION, '')\"".
                       " onMouseOut=\"nd()\"><IMG SRC=\"images/ecran-annonce/icolettrew.gif\" BORDER='0'></A></TD>";
               }
               else
                  echo "<TD width='50' valign='top' align='center'>&nbsp;</TD>";
               echo "<TD width='50' valign='top' align='center'><FONT COLOR= 'white' SIZE='1'><strong>$mpr_suivi</strong></FONT></TD>";
//               if ($id_classe == 1)
               echo "<TD width='50' valign='top'><FONT COLOR= 'white' SIZE='1'><strong>$mess_grp_indiv</strong></FONT></TD>";
               echo "<TD width='95' valign=top><FONT COLOR= 'white' SIZE='1'><strong>$mess_gp_seq_parc ".
                    strtolower($mess_gp_hsd)."</strong></FONT></TD>";
               echo "<TD width='95' valign=top><FONT COLOR= 'white' SIZE='1'><strong>$mess_seq_urgent</strong></FONT></TD>";
               echo "<TD width='50' valign='top'><FONT COLOR= 'white' SIZE='1'><strong>$mess_trx_rendus</strong></FONT></TD>";
//               echo "<TD width='50' valign='top' align='center'><FONT COLOR= 'white' SIZE='1'><strong>$mess_tutorat</strong></FONT></TD>";
               echo "<TD width='50' valign='top'><FONT COLOR= 'white' SIZE='1'><strong>$mess_presc_fs</strong></FONT></TD>";
               echo "<TD width='50' valign='top'><FONT COLOR= 'white' SIZE='1'><strong>$msq_acts</strong></FONT></TD>";
               echo "<TD width='50' valign='top'><FONT COLOR= 'white' SIZE='1'><strong>$mess_obs_fs</strong></FONT></TD>";
               echo "<TD width='50' valign=top><FONT COLOR= 'white' SIZE='1'><strong>$mess_ajt_frm</strong></FONT></TD>";
               echo "<TD width='50' valign='top'><FONT COLOR= 'white' SIZE='1'><strong>$mess_bilan</strong></FONT></TD>";
               echo "</TR>";
               if ($nomb_app > 0)
               {
                  $n_app = 0;
                  while ($n_app < $nomb_app)
                  {
                       $id_app = mysql_result($req_app,$n_app,"presc_utilisateur_no");
                       $req_identite = mysql_query("SELECT * from utilisateur where util_cdn = $id_app");
                       $nb_identite = mysql_num_rows($req_identite);
                       $id_login = mysql_result($req_identite,0,"util_login_lb");
                       $id_nom = mysql_result($req_identite,0,"util_nom_lb");
                       $id_prenom = mysql_result($req_identite,0,"util_prenom_lb");
                       $id_type = mysql_result($req_identite,0,"util_typutil_lb");
                       $id_email = mysql_result($req_identite,0,"util_email_lb");
                       $id_photo = mysql_result($req_identite,0,"util_photo_lb");
                       $majuscule1 = $id_prenom." ".$id_nom;
                       $aa = $n_app+200*$i;
                       $mm = $aa+50000;
                       echo couleur_tr($n_app+1,30);
                       echo "<TD width='40' bgcolor= '#FFFFFF'>&nbsp;</TD><TD valign='top' width='171' nowrap>";
                       $lien = "admin/ChartsStatements.php?groupe=$id_grp&who=".$id_app."_".$id_login."|".str_replace('http://','',str_replace('.educagri.fr','',$nom_url));
                       if ($_SESSION['onLine'] == 1)
                       $traqXapi = "<script language='javascript'>
                            $.ajax({
                                    type: 'GET',
                                    url: 'http://lms.annulab.com/TinCanApi/statementsCharts.php',
                                    data: 'comptage=1&groupe=$id_grp&who=".$id_app."_".$id_login."|".str_replace('http://','',str_replace('.educagri.fr','',$nom_url))."',
                                    success: function(msg)
                                    {
                                       if (msg == 'Rien')
                                           $('#BilanAcces_$id_app').css('display','none');
                                       else
                                       {
                                           $('#BilanAcces_$id_app').html('<img src=\"images/icones/ampoule18.gif\" ".
                                           " border=0 title=\"Bilan sur le LRS\" style=\"padding-bottom:3px;\">');
                                           $('#LrsAcces_$id_app').css('display','block');
                                       }
                                    //alert(msg);
                                    }
                            });
                            </script>";
                       $traqXapi .= "<div id='LrsAcces_$id_app'style='display:none;float:left;'><A HREF = \"$lien\" title='Historique des accès' target='blank'>".
                                    "<div id='BilanAcces_$id_app' style='clear:both:float:left;width:20px;'></div></A></div>";
                       $lien="prescription.php?affiche_fiche_app=1&hgrp=$hgrp&id_util=$id_app&identite=1&stopper=1";
                       $lien = urlencode($lien);
                       $nbr_trac = mysql_result(mysql_query("select count(*) from traceur where
                                                             traceur_util_no = $id_app AND
                                                             traceur_grp_no=$id_grp"),0);
                       $nbr_log = mysql_result(mysql_query("select count(*) from log where login = '$id_login'"),0);
                       if ($nbr_trac == 0)
                          $encore = "<IMG SRC='images/ecran-annonce/icoabsent.gif' width='5' height='10' border='0' ".
                          bulle(NewHtmlentities($mess_no_actif),"","CENTER","ABOVE",100);
                       else
                          $encore = "";
                       if ($nbr_log == 0)
                       {
                          $le_div = "<DIV id='seqgris' style='float:left;'>";
                          $nolog = ". << $no_connect >>";
                       }
                       else
                       {
                          $le_div = "<DIV id='sequence' style='float:left;'>";
                          $nolog = "";
                       }
                       if ($id_photo == '')
                       {
                          echo $traqXapi.$le_div."<A HREF=\"javascript:void(0);\" onclick=\"window.open('trace.php?link=$lien','',".
                               "'scrollbars=yes,resizable=yes,width=820,height=600');\" ".
                               "title=\"$mess_suite_fp$nolog\"><strong>$id_nom $id_prenom</strong></A>  $encore</DIV>";
                       }
                       else
                       {
                          list($w_img, $h_img, $type_img, $attr_img) = getimagesize("images/$id_photo");
                          echo $traqXapi.$le_div."<A HREF=\"javascript:void(0);\" onclick=\"window.open('trace.php?link=$lien','',".
                               "'scrollbars=yes,resizable=yes,width=820,height=600')\" ".
                               "onMouseOver=\"overlib('', TEXTCOLOR, '#ffffff', TEXTSIZE, 2, WIDTH, $w_img, HEIGHT, $h_img, BACKGROUND,".
                               " 'images/$id_photo', PADX, 60, 20, PADY, 20, 20);\" onMouseOut=\"nd();\" title = \"$nolog\">".
                               "<strong>$id_nom $id_prenom</strong></A>  $encore</DIV>";
                       }
                        echo msgInst($id_app,"apprenant car il est connecté");

                        $lien= "mail.php?contacter=1&a_qui=$id_email&num=$id_app";
                        $lien = urlencode($lien);
                        echo "<TD><A HREF=\"javascript:void(0);\" ".
                             "onclick=\"window.open('trace.php?link=$lien','','width=680,height=520,resizable=yes,status=no')\"".
                             " onmouseover=\"img_lettre$i.src='images/ecran-annonce/icolettrebw.gif';return true;\"".
                             " onmouseout=\"img_lettre$i.src='images/ecran-annonce/icolettrew.gif'\">".
                             "<IMG NAME=\"img_lettre$i\" SRC=\"images/ecran-annonce/icolettrew.gif\"".
                             " BORDER='0' onLoad=\"tempImg=new Image(0,0); tempImg.src='images/ecran-annonce/icolettrebw.gif'\"></A></TD>";
                      $nb_fiche_presc = 0;
                      $nb_fiche_act = 0;
                      $nb_fiche_apprec = 0;
                      $nb_fiche_obs = 0;
                      $req_presc_grp = mysql_query("SELECT COUNT(*) from prescription_$id_grp WHERE
                                                    presc_utilisateur_no = $id_app AND
                                                    presc_grp_no = $id_grp");
                      $nb_presc_grp = mysql_result($req_presc_grp,0);
                      if ($nb_presc_grp > 0)
                      {
                         $lien1 = "menu_formateur.php?action=suivre";
                         $lien1 = urlencode($lien1);
                         $lien = "gest_frm_rappel1.php?utilisateur=$id_app&a_faire=1&le_groupe=$id_grp&id_grp=$id_grp".
                                 "&numero_groupe=$id_grp&graph=1&tout=0&vient_de_grp=1";
                         $lien=urlencode($lien);//entantque=form&
                         echo "<TD align='center' valign='top'><A HREF=\"trace.php?link=$lien\" target='main'".
                              " onclick=\"javascript:parent.logo.location.replace('trace.php?link=$lien1')\" TITLE=\"\"".
                              " onmouseover=\"img$aa.src='images/ecran-annonce/icoGgob.gif';return true;\" ".
                              "onmouseout=\"img$aa.src='images/ecran-annonce/icoGgo.gif'\">".
                              "<IMG NAME=\"img$aa\" SRC=\"images/ecran-annonce/icoGgo.gif\" BORDER='0' ".
                              "onLoad=\"tempImg=new Image(0,0); tempImg.src='images/ecran-annonce/icoGgob.gif'\"></A></TD>";
                      }
                      else
                         echo  "<TD width='200'><acronyme id='$nom_grp'>$nom_grp1</acronyme></TD>";
                      if ($id_classe == 1)
                      {
                         $lien = "modif_parc.php?a_faire=1&utilisateur=$id_app&hgrp=$hgrp&numero_groupe=$id_grp";
                         $lien = urlencode($lien);
                         echo "<TD align='center' valign='top'><A href=\"trace.php?link=$lien\" ".
                              "target='main' title=\"$mess_menu_mod_presc $de $majuscule1\">
                              <IMG SRC=\"images/modules/tut_form/pictoarbo.gif\" border=0 ".
                              "alt=\"$mess_menu_mod_presc $de $majuscule1\"></A></TD>";
                      }
                      else
                         echo "<TD align='center' valign='top'><A HREF=\"javascript:void(0);\" style=\"cursor:help;\" ".
                              "onMouseOver=\"overlib('".addslashes($mess_indiv_alrt)."',ol_hpos,ABOVE,WIDTH,'200',DELAY,'100',CAPTION, '')\" ".
                              "onMouseOut=\"nd()\"><IMG SRC=\"images/modules/tut_form/icomodiparc1na.gif\" border=0></A></TD>";
                      $nb_seq_ret = cherche_seq($id_app,$id_grp);
                      $ret_seq = explode("*",$nb_seq_ret);
                      $ma_liste = explode("|",$ret_seq[0]);
                      $seq_depasse = $ma_liste[0];
                      if ($seq_depasse > 0)
                      {
                         $lien = "gest_frm_rappel1.php?utilisateur=$id_app&a_faire=1&le_groupe=$id_grp&id_grp=$id_grp".
                                 "&numero_groupe=$id_grp&graph=1&tout=0&vient_de_grp=1";
                         $lien=urlencode($lien);//entantque=form&
                         echo "<TD align='center' valign='top' width='100'>".
                              "<A HREF=\"trace.php?link=$lien\" target='main'>$seq_depasse</A></TD>";
                      }
                      else
                      {
                         echo  "<TD width='100' valign='top'>&nbsp;</TD>";
                      }
                      $seq_urgent = $ma_liste[1];
                      if ($seq_urgent > 0)
                      {
                         $lien = "gest_frm_rappel1.php?utilisateur=$id_app&a_faire=1&le_groupe=$id_grp&id_grp=$id_grp".
                                 "&numero_groupe=$id_grp&graph=1&tout=0&vient_de_grp=1";
                         $lien=urlencode($lien);//entantque=form&
                         echo "<TD align='center' valign='top' width='100'>".
                              "<A HREF=\"trace.php?link=$lien\" target='main'>$seq_urgent</A></TD>";
                      }
                      else
                         echo  "<TD width='100'>&nbsp;</TD>";
                      $nb_file = 0;
                      $act_ficreq = mysql_query ("select count(suivi1_$id_grp.suivi_fichier_lb) from
                                                  suivi1_$id_grp,activite,prescription_$id_grp where
                                                  suivi_act_no=activite.act_cdn AND
                                                  act_devoirarendre_on = 'OUI' AND
                                                  act_notation_on = 'OUI' AND
                                                  act_seq_no = presc_seq_no AND
                                                  presc_formateur_no=$id_user AND
                                                  presc_utilisateur_no=$id_app AND
                                                  suivi_utilisateur_no=$id_app AND
                                                  presc_grp_no=$id_grp AND
                                                  suivi_grp_no = $id_grp AND
                                                  suivi_etat_lb = 'ATTENTE' AND
                                                  suivi_fichier_lb != ''");
                      $nb_file = mysql_result($act_ficreq,0);
                      if ($nb_file > 0)
                      {
                         $lien = "gest_frm_rappel1.php?utilisateur=$id_app&a_faire=1&le_groupe=$id_grp&id_grp=$id_grp".
                                 "&numero_groupe=$id_grp&graph=1&tout=0&vient_de_grp=1";
                         $lien=urlencode($lien);//entantque=form&
                         echo "<TD align='center' valign='top' width='100'>".
                              "<A HREF=\"trace.php?link=$lien\" target='main'>$nb_file</A></TD>";
                      }
                      else
                         echo "<TD align='center' valign='top' width='30'>&nbsp;</TD>";
                      // fiche navette prescriptions
                      $req_fiche_presc = mysql_query("SELECT * FROM fiche_suivi WHERE
                                                      fiche_utilisateur_no = $id_app AND
                                                      fiche_grp_no = $id_grp AND
                                                      fiche_auteur_no != $id_user AND
                                                      (fiche_typaction_lb = 'Gestion des prescriptions' OR
                                                      fiche_typaction_lb = 'Prescription') AND
                                                      fiche_date_dt >= '$date_der'");
                      $nb_fiche_presc = mysql_num_rows($req_fiche_presc);
                      if ($nb_fiche_presc > 0)
                      {
                          $lien="fiche_suivi.php?utilisateur=$id_app&consulter=1&objet=prescription&event=1".
                                "&ordre=asc&tuteur_vue=1&date_der=$date_der&id_grp=$id_grp&vientde=annonce";
                          $lien=urlencode($lien);
                          echo "<TD align='center' valign='top' width='30'><DIV id='sequence'>".
                               "<A HREF=\"trace.php?link=$lien&keepThis=true&TB_iframe=true&height=455&width=700\" class='thickbox'>".
                               "$nb_fiche_presc</A></DIV></TD>";
                      }
                      else
                          echo "<TD align='center' valign='top' width='30'>&nbsp;</TD>";
                      $req_fiche_act = mysql_query("SELECT * FROM fiche_suivi WHERE
                                                    fiche_utilisateur_no = $id_app AND
                                                    fiche_auteur_no != $id_user AND
                                                    fiche_grp_no = $id_grp AND
                                                    fiche_typaction_lb = 'suivi pedagogique' AND
                                                    fiche_date_dt >= '$date_der'");
                      $nb_fiche_act = mysql_num_rows($req_fiche_act);
                      if ($nb_fiche_act > 0)
                      {
                         $lien="fiche_suivi.php?utilisateur=$id_app&consulter=1&objet=suivi&event=1&ordre=asc".
                               "&tuteur_vue=1&date_der=$date_der&id_grp=$id_grp&id_grp=$id_grp&vientde=annonce";
                         $lien=urlencode($lien);
                         echo "<TD align='center' valign='top' width='30'><DIV id='sequence'>".
                              "<A HREF=\"trace.php?link=$lien&keepThis=true&TB_iframe=true&height=455&width=700\"".
                              " class='thickbox'>$nb_fiche_act</A></DIV></TD>";
                      }
                      else
                         echo "<TD align='center' valign='top' width='30'>&nbsp;</TD>";
                      // fiche navette Observations
                      $req_fiche_obs = mysql_query("SELECT * FROM fiche_suivi WHERE
                                                    fiche_utilisateur_no = $id_app AND
                                                    fiche_auteur_no != $id_user AND
                                                    fiche_grp_no = $id_grp AND
                                                    (fiche_autraction_lb = 'observation' OR
                                                    fiche_autraction_lb = 'appreciation') AND
                                                    fiche_date_dt >= '$date_der'");
                      $nb_fiche_obs = mysql_num_rows($req_fiche_obs);
                      if ($nb_fiche_obs > 0)
                      {
                         $lien="fiche_suivi.php?utilisateur=$id_app&consulter=1&objet=observation&event=2".
                               "&ordre=asc&tuteur_vue=1&date_der=$date_der&id_grp=$id_grp&vientde=annonce";
                         $lien=urlencode($lien);
                         echo "<TD align='center' valign='top' width='30'><DIV id='sequence'>".
                              "<A HREF=\"trace.php?link=$lien&keepThis=true&TB_iframe=true&height=455&width=700\" class='thickbox'>".
                              "$nb_fiche_obs</A></DIV></TD>";
                      }
                      else
                         echo "<TD align='center' valign='top' width='30'>&nbsp;</TD>";
                      // Participations au forum
                      $nom_grp = GetDataField ($connect,"select grp_nom_lb from groupe where grp_cdn  = $id_grp","grp_nom_lb");
                      $id_forum = GetDataField ($connect,"select id from forums where name='$nom_grp'","id");
                      $nom_forum = GetDataField ($connect,"select table_name from forums where name='$nom_grp'","table_name");
                      $req_forum =mysql_query("SELECT * from $nom_forum where author = \"$majuscule1\" AND ".
                                              "email = \"$id_email\" and approved='Y'");
                      $nbr_posts = mysql_num_rows($req_forum);
                      if ($nbr_posts > 0)
                      {
                          $lien = "popup_forums.php?NbrPost=$nbr_posts&id_forum=$id_forum&utilisateur=$id_app&id_grp=$id_grp";
                          $lien=urlencode($lien);
                          echo  "<TD align='center' valign='top' width='50'>".
                                "<A HREF=\"trace.php?link=$lien&keepThis=true&TB_iframe=true&height=455&width=700\"".
                                " class='thickbox'>$nbr_posts</A></TD>";
                      }
                      else
                          echo  "<TD width='70'>&nbsp;</TD>";
                      // Historique d'activités
                      $req_traq = mysql_query("select traq_cdn from traque where traq_util_no=$id_app AND traq_grp_no= $id_grp");
                      $nbr_trq = mysql_num_rows($req_traq);
                      if ($nbr_trq > 0)
                      {
                         $lien_bilan = "bilan.php?bilan=1&utilisateur=$id_app&numero_groupe=$id_grp&keepThis=true&TB_iframe=true&height=455&width=700";
                         //$lien_bilan = urlencode($lien_bilan);
                         echo "<TD align='center' valign='top'><DIV id='sequence'><A HREF=\"$lien_bilan\" class='thickbox'".
                              " onmouseover=\"img_regroup$n_app.src='images/bilan/icogroupb.gif';return true;\"".
                              " onmouseout=\"img_regroup$n_app.src='images/bilan/icogroup.gif'\">".
                              "<IMG NAME=\"img_regroup$n_app\" SRC=\"images/bilan/icogroup.gif\" BORDER='0' width='28' height='25'".
                              " onLoad=\"tempImg=new Image(0,0); tempImg.src='images/bilan/icogroupb.gif'\"></A></DIV></TD>";
                      }
                      else
                         echo "<TD align='center' valign='top' width='30'>&nbsp;</TD>";
                     echo "</TR>";
                     $n_app++;
                  }
                  echo "</TABLE></TD></TR>";
               }
             }
               if ((isset($groupe_affiche[$id_grp]) && $groupe_affiche[$id_grp] == 1) || (isset($affiche_groupe) && $affiche_groupe == 3))
               {
                   echo "<tr height='6'><td></td></tr></table></div></td></tr>";
                   echo "<TR><TD width='100%' height='20'></TD></TR></table>";
               }
               else
                   echo "<tr height='6'><td></td></tr></table></td></tr></td></tr></table>";
        $i++;
      }
      echo "</TD></TR></TABLE>";
    }
  $num_grp++;
  }
  echo "</TD></TR></TABLE>";
}
// Fin d'affichage
?>
