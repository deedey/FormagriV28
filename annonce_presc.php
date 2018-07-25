<?php
if (!isset($_SESSION)) session_start();
include "include/UrlParam2PhpVar.inc.php";
if (!isset ($_SESSION['id_user']) || $_SESSION['id_user'] == "")
{
        exit ();
}
require "admin.inc.php";
require 'fonction.inc.php';
require 'include/fonction_annonce.php';
require 'langues/prescription.inc.php';
require 'fonction_html.inc.php';
require "lang$lg.inc.php";
//include ("click_droit.txt");
dbConnect();
include ('include/entete_annonce_inc.php');
unset($_SESSION['typ_user']);
if (isset($_SESSION['mode_user']) && $_SESSION['mode_user'] == 'tout')
        $typ_user = GetDataField($connect, "select util_typutil_lb from utilisateur where util_cdn='$id_user'", "util_typutil_lb");
else
        $typ_user = "RESPONSABLE_FORMATION";
$_SESSION['typ_user'] = $typ_user;
//------------------------------------------------------------------------------------------------------------------
$Register = base64url_encode($_COOKIE["monID"].'_'.$_COOKIE["monLogin"].'|'.str_replace('http://','',str_replace('.educagri.fr','',$adresse_http)));
if ($typ_user == "RESPONSABLE_FORMATION")
        $req_grp_form = mysql_query("select distinct groupe.grp_cdn from groupe WHERE
                                                groupe.grp_resp_no = '$id_user'
                                                order by groupe.grp_nom_lb");
elseif ($typ_user == "ADMINISTRATEUR")
        $req_grp_form = mysql_query("select distinct groupe.grp_cdn from groupe,utilisateur_groupe
                                        WHERE utilisateur_groupe.utilgr_groupe_no = groupe.grp_cdn
                                        order by groupe.grp_nom_lb");
$nomb_grp_form = mysql_num_rows($req_grp_form);
if ($affiche_groupe == 3)
{
        $chx = 0;
        $sens_aff = $mess_replier;
}
else
{
        $chx = 3;
        $sens_aff = $mess_deplier;
}
if ($nomb_grp_form == 0)
{
   $nomb_appPresc = 0;
   $NbrGrpPrescrit=0;
   $ListeGrp='';
   $req_grp = mysql_query("select * from groupe order by groupe.grp_nom_lb");
   $nbrgrp = mysql_num_rows($req_grp);
   if ($nbrgrp > 0)
   {
     while ($item = mysql_fetch_object($req_grp))
     {
       $Ext = "_".$item->grp_cdn;
       $req_GrpPresc = mysql_query("select * from prescription$Ext where presc_prescripteur_no = $id_user");
       if ( mysql_num_rows($req_GrpPresc) > 0)
       {
          $NbrGrpPrescrit++;
          $ListeGrp .= "$item->grp_cdn|";
       }
     }
   }
}
entete_simple($mess_suivi_app_presc);
$lien = "annonce_presc.php?affiche_groupe=$chx";
$lien = urlencode($lien);
if ($nomb_grp_form > 0)
{
        echo "<tr><td><table><tr><td align='left' valign='middle' style=\"padding: 6px;\">";
        echo "<A href=\"trace.php?link=$lien\" class='bouton_new'>$sens_aff</A></td>" .
             aide("suivi_pedagogique_resp", '') . "</tr></table></td></tr>";
        echo "<TR height='50'><TD colspan='2' bgcolor='#FFFFFF'><TABLE width='100%' cellspacing='0' cellpadding='5' border='0'>";
        $log_der = Donne_ID($connect, "SELECT max(log_cdn) from log WHERE login='$login' AND date_fin !='0000-00-00'");
        if ($log_der != 1)
        {
                $log = $log_der -1;
                $date_der = GetDataField($connect, "select date_fin from log WHERE log_cdn = $log", "date_fin");
                $heure_der = GetDataField($connect, "select heure_fin from log WHERE log_cdn = $log", "heure_fin");
                $date_compare = $date_der . " " . $heure_der;
        }
        $i = 0;
        while ($i < $nomb_grp_form)
        {
                $nb_fiche_presc = 0;
                $nb_fiche_act = 0;
                $nb_fiche_apprec = 0;
                $nb_fiche_obs = 0;
                $id_grp = mysql_result($req_grp_form, $i, "grp_cdn");
                $grp_parc = mysql_query("SELECT gp_parc_no from groupe_parcours where gp_grp_no =$id_grp ORDER BY gp_ordre_no");
                $nb_f = mysql_num_rows($grp_parc);
                $nb_app_gp = mysql_num_rows(mysql_query("select utilgr_cdn from utilisateur_groupe WHERE utilisateur_groupe.utilgr_groupe_no = $id_grp"));
                if ($nb_app_gp > 0)
                {
                        $nom_grp = GetDataField($connect, "select grp_nom_lb from groupe WHERE grp_cdn = $id_grp", "grp_nom_lb");
                        $resp_grp = GetDataField($connect, "select grp_resp_no from groupe WHERE grp_cdn = $id_grp", "grp_resp_no");
                        $carac_grp = strlen($nom_grp);
                        if ($carac_grp > 33)
                                $nom_grp1 = substr($nom_grp, 0, 31) . "..";
                        else
                                $nom_grp1 = $nom_grp;
                        $id_classe = GetDataField($connect, "SELECT grp_classe_on from groupe where grp_cdn  = $id_grp", "grp_classe_on");
                        $desc_grp = GetDataField($connect, "select grp_formobject_lb from groupe WHERE grp_cdn = $id_grp", "grp_formobject_lb");
                        $desc_grp = str_replace(chr(146), chr(39), $desc_grp);
                        $desc1_grp = addcslashes($desc_grp, "\0..\47!@\176..\255");
                        if ((!isset ($groupe_affiche[$id_grp]) || $groupe_affiche[$id_grp] == 0))
                        {
                                $choix = 1;
                                $mess_aff = $list_app_grp;
                        }
                        else
                        {
                                $choix = 0;
                                $mess_aff = $mess_close_list;
                        }
                        if ($id_classe == 0)
                                $mess_aff .= "<br />$mess_indiv_alrt";
                        if ((isset($groupe_affiche[$id_grp]) && $groupe_affiche[$id_grp] == 1) || $affiche_groupe == 3 || $nomb_grp_form == 1)
                                echo "<tr><td colspan='6' style=\"padding-left: 3px;padding-right: 3px;\">".
                                     "<div id='tut_separe' class='individ'><table width='100%' border='0'>";
                        else
                                echo "<tr><td colspan='6' style=\"padding-left: 3px;padding-right: 3px;\"><table width=100% border='0'>";
                        if ($i > 0)
                                echo "<TR height='1'><TD colspan = '5' bgcolor='#FFFFFF'></TD></TR>";
                        echo couleur_tr($i +1, 30);
                        $lien = "annonce_presc.php?groupe_affiche[$id_grp]=$choix";
                        $lien = urlencode($lien);
                        echo "<td style=\"width:'3%';\" valign='middle'>";
                        if ($choix == 1)
                        {
                                echo "<A HREF=\"trace.php?link=$lien\"" .
                                     " onmouseover=\"img_flb$i.src='images/gest_parc/flechv01b.gif';" .
                                     "overlib('" . addslashes($mess_aff) . "',ol_hpos,RIGHT,ABOVE,WIDTH,'250',DELAY,'500');return true;\"" .
                                     " onmouseout=\"img_flb$i.src='images/gest_parc/flechv01.gif';nd();\">" .
                                     "<IMG NAME=\"img_flb$i\" SRC=\"images/gest_parc/flechv01.gif\" border='0' valign='top'" .
                                     " onLoad=\"tempImg=new Image(0,0); tempImg.src='images/gest_parc/flechv01b.gif'\"></A>";
                        }
                        else
                        {
                                echo "<A HREF=\"trace.php?link=$lien\"" .
                                " onmouseover=\"img_flh$i.src='images/gest_parc/flech02b.gif';" .
                                "overlib('" . addslashes($mess_aff) . "',ol_hpos,RIGHT,ABOVE,WIDTH,'250',DELAY,'500');return true;\"" .
                                " onmouseout=\"img_flh$i.src='images/gest_parc/flech02.gif';nd();\">" .
                                "<IMG NAME=\"img_flh$i\" SRC=\"images/gest_parc/flech02.gif\" border='0' valign='top'" .
                                " onLoad=\"tempImg=new Image(0,0); tempImg.src='images/gest_parc/flech02b.gif'\"></A>";
                        }
                        if ($resp_grp == $id_user)
                                echo "&nbsp;&nbsp;<IMG SRC=\"images/gest_parc/icofeuilb.gif\" border='0'>";
                        else
                                echo nbsp(4);
                        echo "</td>";
                        $lien = "groupe.php?n=$id_grp";
                        $lien = urlencode($lien);
                        $grp_parc = mysql_query("SELECT gp_parc_no from groupe_parcours where gp_grp_no =$id_grp ORDER BY gp_ordre_no");
                        $nb_f = mysql_num_rows($grp_parc);
                        if ($nb_f > 0)
                        {
                                $liste_parc = "<FONT COLOR='marroon'><strong><U>$mess_list_parc_grp</U></strong></FONT><br />";
                                $nnn = 0;
                                while ($nnn < $nb_f)
                                {
                                        $cc = $nnn +1;
                                        $num_parc = mysql_result($grp_parc, $nnn, "gp_parc_no");
                                        $nom_parcours = getdatafield($connect, "SELECT parcours_nom_lb from parcours where parcours_cdn = $num_parc", "parcours_nom_lb");
                                        $nom_parc = str_replace("'", "\'", str_replace('"', "-",html_entity_decode($nom_parcours,ENT_QUOTES,'iso-8859-1')));
                                        $liste_parc .= "$cc - $nom_parc<br />";
                                        $nnn++;
                                }
                                echo "<td width='27%'><A HREF=\"javascript:void(0);\" " .
                                "onClick=\"window.open('trace.php?link=$lien','','scrollbars,resizable=yes,height=340, width=600')\" " .
                                bulle($liste_parc, "", "RIGHT", "ABOVE", 240) . "$nom_grp1</A></td>";
                        }
                        else
                        {
                                echo "<td width='27%'><a href=\"javascript:void(0);\" " .
                                "onClick=\"window.open('trace.php?link=$lien','','scrollbars,resizable=yes,height=340, width=600');\">$nom_grp1</A></td>";
                        }
                        $titrer = "Mailing";
                        $lien = "annuaire.php?vientde=annonce&id_grp=$id_grp"; //&entantque=presc
                        $lien = urlencode($lien);
                          echo "<td valign='middle' width='40%'><table border='0' width='100%'><tr><td>$bouton_gauche<A HREF=\"javascript:void(0);\" " .
                               "onclick=\"window.open('trace.php?link=$lien','','width=680,height=510,resizable=yes,status=no')\" ".
                               bulle("$mess_mail_avert $mess_mail_cert_app", "", "CENTER", "ABOVE", 220) ."$titrer</A>$bouton_droite</td>";
                                $titrer ='';
                         $lien = "message.php?type=groupe&num=$id_grp";
                         $lien = urlencode($lien);
                         $titre_mess = "$mess_alert $pour " . strtolower($mess_menu_mail_app) . "&nbsp;$mess_menu_gestion_grp $nom_grp";
                         if ($id_classe == 1)
                            echo "<td>$bouton_gauche<A HREF=\"javascript:void(0);\" " .
                                  "onclick=\"window.open('trace.php?link=$lien','','width=680,height=380,resizable=yes,status=no')\" " .
                                  bulle($titre_mess, "", "CENTER", "ABOVE", 220) . "$mess_alert</A>$bouton_droite</td>";
                         else
                            echo "<td valign='middle'>$bouton_gauche<A HREF=\"javascript:void(0);\" " .
                                  "onclick=\"window.open('trace.php?link=$lien','','width=680,height=380,resizable=yes,status=no')\" " .
                                  bulle($titre_mess, "", "CENTER", "ABOVE", 220) . "$mess_alert</A>$bouton_droite</TD>";
                         if ($nb_f > 0 && $id_classe == 1)
                         {
                                $titrer = "+ Ajout Modules/Seqs";
                                $lien = "listeAppParcGrp.php?id_grp=$id_grp&action=ajouter"; //&entantque=presc
                                $lien = urlencode($lien);
                                echo "<td>$bouton_gauche<A HREF=\"trace.php?link=$lien\" ".
                                     "title='Ajouter des modules ou des séquences par lot pour cette formation invidualisée'>".
                                     "$titrer</A>$bouton_droite</td>";
                                $titrer ='';
                                $titrer = "x Supp. Modules/Seqs";
                                $lien = "listeAppParcGrp.php?id_grp=$id_grp&action=supprimer"; //&entantque=presc
                                $lien = urlencode($lien);
                                echo "<td>$bouton_gauche<A HREF=\"trace.php?link=$lien\" ".
                                     "title='Supprimer des modules ou des séquences par lot pour cette formation invidualisée'>".
                                     "$titrer</A>$bouton_droite</td></tr></table></TD>";
                                $titrer ='';
                        }
                        elseif ($nb_f == 0 || $id_classe == 0)
                        {
                          $letitre = ($id_classe == 0) ? "Cette formation n'est pas individualisée" : "Cette formation ne comporte aucun module";
                          $titrer = "+ Ajout Modules/Seqs";
                          echo "<td style='cursor:default;'>$bouton_gauche<acronym title=\"$letitre\"><b>$titrer</b></acronym>$bouton_droite</td>";
                                $titrer = "x Supp. Modules/Seqs";
                          echo "<td style='cursor:default;'>$bouton_gauche<acronym title=\"$letitre\"><b>$titrer</b></acronym>$bouton_droite</td></tr></table></TD>";
                        }
                        //-----------------------------------fin Dey
                        $numWkG = 10000+$id_grp;
                        $nbWkGrp = mysql_num_rows(mysql_query("select * from wikiapp where wkapp_seq_no=$numWkG"));
                        if ($nb_f > 0 || $nbWkGrp > 0)
                        {
                           echo "<td nowrap width=20%>";
                           if ($nb_f > 0)
                           {
                               $lien = "MindMapper.php?id_grp=$id_grp";
                               echo "<div style='clear:both;float:left;'>$bouton_gauche<a href=\"$lien\" ".
                                    " onClick=\"javascript:simplejQ_Ajax('/admin/InsereTrace.php?lelien=".urlencode($lien)."');\" ".
                                    "title='Voir le schéma de cette formation'>";
                               echo "MindMap</A>$bouton_droite</div>";
                           }
                           
                           // Debut Wiki
                           if ($nbWkGrp > 0)
                           {
                                $titrer = "Wiki(s)";
                                $lien = "wiki/wikiGrp.php?id_seq=$numWkG&id_parc=10000&id_grp=$id_grp&keepThis=true&TB_iframe=true&height=260&width=550";
                                echo "<div style='float:right;margin-left:2px;'>$bouton_gauche<A HREF=\"$lien\"  class='thickbox'".
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
                        $lien = "lancement.php?numero_groupe=$id_grp&id_grp=$id_grp&groupe=1&hgrp=$hgrp&planning=normal"; //&entantquepresc=1";
                        $lien = urlencode($lien);
                        $titrer = $mpr_consult_grp . " " . $nom_grp;
                        echo "<TD align='center' valign='middle' width='10%'>$bouton_gauche<A HREF=\"trace.php?link=$lien\"" .
                        bulle($titrer, "", "CENTER", "ABOVE", 250) . "$mess_aventi</A>$bouton_droite</TD>";
                        $titrer = '';
                        $lien_rp = "modif_rep_fic.php?id_grp=$id_grp&formateurs=&nom_grp=$nom_grp&communes_groupe=1&rallonge=ressources/groupes/$id_grp&sousdos=ressources/groupes/$id_grp&parent=ressources/groupes&dossier=ressources/groupes/$id_grp&fichier=$id_grp&direct=dossier";
                        $lien_rp = urlencode($lien_rp);
                        echo "<TD align='center' valign='middle' width='30%' nowrap>";
                        echo "$bouton_gauche<A HREF=\"trace.php?link=$lien_rp\">$mess_menu_casier_groupe $mess_menu_gestion_grp</A>$bouton_droite</TD>";
                        // accès forum du groupe
                        $id_forum = GetDataField($connect, "select id from forums where name ='$nom_grp'", "id");
                        $comment_forum = GetDataField($connect, "select name from forums where id='$id_forum'", "name");
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
                          "url(\"images/ecran-annonce/ongl01.gif\");margin-left:2px;'></div></A></div>".
                          "</td></tr></table></TD>";
                        echo "</TR>";
                        $presc_no = 0;
                        if ((isset($groupe_affiche[$id_grp]) && $groupe_affiche[$id_grp] == 1) || $affiche_groupe == 3 || $nomb_grp_form == 1)
                        {
                                if ($typ_user == "RESPONSABLE_FORMATION")
                                {
                                        $req_app = mysql_query("SELECT distinct util_cdn from
                                                                utilisateur,utilisateur_groupe,groupe where
                                                                utilisateur.util_cdn = utilisateur_groupe.utilgr_utilisateur_no AND
                                                                ((utilisateur.util_auteur_no = $id_user AND
                                                                utilisateur_groupe.utilgr_groupe_no = $id_grp) OR
                                                                (utilisateur_groupe.utilgr_groupe_no = $id_grp AND
                                                                groupe.grp_cdn = $id_grp AND
                                                                groupe.grp_resp_no = $id_user))
                                                                order by utilisateur.util_nom_lb");
                                        $presc_no = 1;
                                }
                                elseif ($typ_user == "ADMINISTRATEUR")
                                {
                                        $req_app = mysql_query("SELECT distinct util_cdn from
                                                                utilisateur,utilisateur_groupe,groupe where
                                                                (utilisateur.util_cdn = utilisateur_groupe.utilgr_utilisateur_no AND
                                                                utilisateur_groupe.utilgr_groupe_no = $id_grp)
                                                                order by utilisateur.util_nom_lb");
                                }
                                $nomb_app = mysql_num_rows($req_app);
                                echo "<TR>";
                                $wdtTD = ($wk == 1) ? 7 : 6;
                                echo "<TD colspan='$wdtTD' width='100%' align='right'><TABLE cellspacing=1 cellpadding=3 border='0' width=100%>";
                                echo "<tr bgcolor=\"#2B677A\"><TD width='40' style=\"background: '#FFFFFF';\">&nbsp;</TD>";
                                echo "<TD width='180' valign='top'><FONT COLOR= 'white' SIZE='1'><strong>$msq_apprenant</strong></FONT></TD>";
                                $titrer = $mess_mail_avert . " " . $mess_mail_cert_app;
                                $lien = "annuaire.php?vientde=annonce&id_grp=$id_grp"; //&entantque=presc
                                $lien = urlencode($lien);
                                echo "<TD width='15' valign='middle' align='left'><A HREF=\"javascript:void(0);\"" .
                                " onclick=\"open('trace.php?link=$lien','window','scrollbars=no,resizable=yes,width=680,height=520')\"" .
                                bulle($titrer, "", "CENTER", "ABOVE", 220) . "<IMG SRC=\"images/ecran-annonce/icolettrew.gif\" BORDER='0'></A></TD>";
                                $titrer = '';
                                echo "<TD width='50' valign='top' align='center'><FONT COLOR= 'white' SIZE='1'><strong>$mess_alrt</strong></FONT></TD>";
                                echo "<TD width='50' valign='top' align='center'><FONT COLOR= 'white' SIZE='1'><strong>$mpr_suivi</strong></FONT></TD>";
                                if ($id_classe == 1)
                                        echo "<TD width='50' valign='top'><FONT COLOR= 'white' SIZE='1'><strong>$mess_grp_indiv</strong></FONT></TD>";
                                echo "<TD width='95' valign=top><FONT COLOR= 'white' SIZE='1'><strong>$mess_gp_seq_parc " . strtolower($mess_gp_hsd) . "</strong></FONT></TD>";
                                echo "<TD width='95' valign=top><FONT COLOR= 'white' SIZE='1'><strong>$mess_seq_urgent</strong></FONT></TD>";
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
                                                $id_app = mysql_result($req_app, $n_app, "util_cdn");
                                                $req_identite = mysql_query("SELECT * from utilisateur where util_cdn = $id_app");
                                                $nb_identite = mysql_num_rows($req_identite);
                                                $id_login = mysql_result($req_identite, 0, "util_login_lb");
                                                $id_nom = mysql_result($req_identite, 0, "util_nom_lb");
                                                $id_prenom = mysql_result($req_identite, 0, "util_prenom_lb");
                                                $id_type = mysql_result($req_identite, 0, "util_typutil_lb");
                                                $id_email = mysql_result($req_identite, 0, "util_email_lb");
                                                $id_photo = mysql_result($req_identite, 0, "util_photo_lb");
                                                $majuscule1 = $id_prenom . " " . $id_nom;
                                                $aa = $n_app +200 * $i;
                                                $mm = $aa +50000;
                                                echo couleur_tr($n_app +1, 30);
                                                echo "<TD width='40' style=\"background: '#FFFFFF';\">";

                                                if ($_SESSION['onLine'] == 1)
                                                {
                                                   $lien = "admin/ChartsStatements.php?groupe=$id_grp&who=".$id_app."_".$id_login."|".str_replace('http://','',str_replace('.educagri.fr','',$nom_url));
                                                   echo "<script language='javascript'>
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
                                                                    " border=0 style=\"padding-bottom:3px;\">');
                                                                    $('#LrsAcces_$id_app').css('display','block');
                                                                 }
                                                                 //alert(msg);
                                                             }
                                                     });
                                                     </script>";
                                                    echo "<div id='LrsAcces_$id_app' style='display:none'><A HREF = \"$lien\" ".
                                                        "title='Historique des accès et bilan sur le LRS' target='blank'>".
                                                        "<div id='BilanAcces_$id_app' style='clear:both;float:left;width:20px;'></div></A></div>";
                                                }
                                                echo '</td>';
                                                echo "<TD valign='top' width='171' nowrap>";
                                                $lien = "prescription.php?affiche_fiche_app=1&hgrp=$hgrp&id_util=$id_app&identite=1&stopper=1";
                                                $lien = urlencode($lien);
                                                $nbr_trac = mysql_result(mysql_query("select count(*) from traceur where
                                                                                     traceur_util_no = $id_app AND
                                                                                     traceur_grp_no=$id_grp"),0);
                                                $nbr_log = mysql_result(mysql_query("select count(*) from log where login = '$id_login'"),0);
                                                if ($nbr_trac == 0)
                                                        $encore = "<IMG SRC='images/ecran-annonce/icoabsent.gif' width='5' height='10' border='0' " . bulle($mess_no_actif, "", "CENTER", "ABOVE", 100);
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
                                                        echo "$le_div<A HREF=\"javascript:void(0);\" onclick=\"window.open('trace.php?link=$lien','','scrollbars=yes,resizable=yes,width=820,height=600')\" " .
                                                        "title=\"$mess_suite_fp$nolog\"><strong>$id_nom $id_prenom</strong></A>  $encore</DIV>";
                                                }
                                                else
                                                {
                                                        list ($w_img, $h_img, $type_img, $attr_img) = getimagesize("images/$id_photo");
                                                        echo "$le_div<A HREF=\"javascript:void(0);\" onclick=\"window.open('trace.php?link=$lien','','scrollbars=yes,resizable=yes,width=820,height=600')\" " .
                                                        "onMouseOver=\"overlib('', TEXTCOLOR, '#ffffff', TEXTSIZE, 2, WIDTH, $w_img, HEIGHT, $h_img, BACKGROUND," .
                                                        " 'images/$id_photo', PADX, 60, 20, PADY, 20, 20)\" onMouseOut=\"nd()\" title = \"$nolog\"><strong>$id_nom $id_prenom</strong></A>  $encore</DIV>";
                                                }
                                                echo msgInst($id_app,"apprenant car il est connecté");

                                                $lien = "mail.php?contacter=1&a_qui=$id_email&num=$id_app";
                                                $lien = urlencode($lien);
                                                echo "<TD><A HREF=\"javascript:void(0);\" onclick=\"window.open('trace.php?link=$lien','','width=680,height=520,resizable=yes,status=no')\"" .
                                                " onmouseover=\"img_lettre$i.src='images/ecran-annonce/icolettrebw.gif';return true;\"" .
                                                " onmouseout=\"img_lettre$i.src='images/ecran-annonce/icolettrew.gif'\"><IMG NAME=\"img_lettre$i\" SRC=\"images/ecran-annonce/icolettrew.gif\"" .
                                                " BORDER='0' onLoad=\"tempImg=new Image(0,0); tempImg.src='images/ecran-annonce/icolettrebw.gif'\"></A></TD>";
                                                $lien = "message.php?type=apprenant&num=$id_app&son_groupe=$id_grp";
                                                $lien = urlencode($lien);
                                                $titrer = "$mess_alert $pour $id_prenom $id_nom";
                                                echo "<td align=center valign='top'><A HREF=\"#\" onclick=\"window.open('trace.php?link=$lien','','width=600,height=320,resizable=yes,status=no')\" " .
                                                bulle($titrer, "", "LEFT", "", 160) . "<IMG SRC=\"images/modules/icoanoter.gif\" BORDER=0></A></td>";
                                                $titrer = "";
                                                $nb_fiche_presc = 0;
                                                $nb_fiche_act = 0;
                                                $nb_fiche_apprec = 0;
                                                $nb_fiche_obs = 0;
                                                $req_presc_grp = mysql_query("SELECT * from prescription_$id_grp WHERE
                                                                                                    presc_utilisateur_no = $id_app");
                                                $nb_presc_grp = mysql_num_rows($req_presc_grp);
                                                if ($nb_presc_grp > 0)
                                                {
                                                        $lien1 = "menu_prescripteur.php?action=prescrire";
                                                        $lien1 = urlencode($lien1);
                                                        $lien = "gest_frm_rappel1.php?utilisateur=$id_app&a_faire=1&le_groupe=$id_grp&id_grp=$id_grp&numero_groupe=$id_grp&graph=1&tout=1&vient_de_grp=1";
                                                        $lien = urlencode($lien); 
                                                        echo "<TD align='center' valign='top'><A HREF=\"trace.php?link=$lien\" target='main'" .
                                                        " onclick=\"javascript:parent.logo.location.replace('trace.php?link=$lien1')\" TITLE=\"\"" .
                                                        " onmouseover=\"img$aa.src='images/ecran-annonce/icoGgob.gif';return true;\" onmouseout=\"img$aa.src='images/ecran-annonce/icoGgo.gif'\">" .
                                                        "<IMG NAME=\"img$aa\" SRC=\"images/ecran-annonce/icoGgo.gif\" BORDER='0' onLoad=\"tempImg=new Image(0,0); tempImg.src='images/ecran-annonce/icoGgob.gif'\"></A></TD>";
                                                }
                                                else
                                                        echo "<TD width='200'><acronyme id='$nom_grp'>$msgNoPrsc</acronyme></TD>";
                                                if ($id_classe == 1)
                                                {
                                                        $lien = "modif_parc.php?a_faire=1&utilisateur=$id_app&hgrp=$hgrp&numero_groupe=$id_grp";
                                                        $lien = urlencode($lien);
                                                        echo "<TD align='center' valign='top'><A href=\"trace.php?link=$lien\" target='main' title=\"$mess_menu_mod_presc $de $majuscule1\">
                                                                                      <IMG SRC=\"images/modules/tut_form/pictoarbo.gif\" border=0 alt=\"$mess_menu_mod_presc $de $majuscule1\"></A></TD>";
                                                }
                                                $nb_seq_ret = '';
                                                $nb_seq_ret = cherche_seq($id_app, $id_grp);
                                                $ret_seq = explode("*", $nb_seq_ret);
                                                $ma_liste = explode("|", $ret_seq[0]);
                                                $seq_depasse = $ma_liste[0];
                                                if ($seq_depasse > 0)
                                                {
                                                        $lien = "gest_frm_rappel1.php?utilisateur=$id_app&a_faire=1&le_groupe=$id_grp&id_grp=$id_grp&numero_groupe=$id_grp&graph=1&tout=1&vient_de_grp=1";
                                                        $lien = urlencode($lien);
                                                        echo "<TD align='center' valign='top' width='100'><A HREF=\"trace.php?link=$lien\" target='main'><strong>$seq_depasse</strong></A></TD>";
                                                } else
                                                        echo "<TD width='100' valign='top'>&nbsp;</TD>";
                                                $seq_urgent = $ma_liste[1];
                                                if ($seq_urgent > 0)
                                                {
                                                        $lien = "gest_frm_rappel1.php?utilisateur=$id_app&a_faire=1&le_groupe=$id_grp&id_grp=$id_grp&numero_groupe=$id_grp&graph=1&tout=1&vient_de_grp=1";
                                                        $lien = urlencode($lien); //entantque=presc&
                                                        echo "<TD align='center' valign='top' width='100'><A HREF=\"trace.php?link=$lien\" target='main'><strong>$seq_urgent</strong></A></TD>";
                                                }
                                                else
                                                        echo "<TD width='100'>&nbsp;</TD>";
                                                $req_fiche_tut = mysql_query("SELECT * FROM fiche_suivi WHERE
                                                                                                    fiche_utilisateur_no = $id_app AND
                                                                                                    fiche_typaction_lb = 'Tutorat' AND
                                                                                                    fiche_date_dt >= '$date_der'");
                                                $nb_fiche_tut = mysql_num_rows($req_fiche_tut);
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
                                                        $lien = "fiche_suivi.php?utilisateur=$id_app&consulter=1&objet=prescription&event=1&ordre=asc&tuteur_vue=1&date_der=$date_der&id_grp=$id_grp&vientde=annonce";
                                                        $lien = urlencode($lien);
                                                        echo "<TD align='center' valign='top' width='30'><DIV id='sequence'>".
                                                             "<A HREF=\"trace.php?link=$lien&keepThis=true&TB_iframe=true&height=455&width=700\"".
                                                             " class='thickbox'>$nb_fiche_presc</A></DIV></TD>";
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
                                                        $lien = "fiche_suivi.php?utilisateur=$id_app&consulter=1&objet=suivi&event=1&ordre=asc&tuteur_vue=1&date_der=$date_der&id_grp=$id_grp&vientde=annonce";
                                                        $lien = urlencode($lien);
                                                        echo "<TD align='center' valign='top' width='30'><DIV id='sequence'>".
                                                             "<A HREF=\"trace.php?link=$lien&keepThis=true&TB_iframe=true&height=455&width=700\"".
                                                             " class='thickbox'>$nb_fiche_act</A></DIV></TD>";
                                                }
                                                else
                                                        echo "<TD align='center' valign='top' width='30'>&nbsp;</TD>";
                                                $req_fiche_obs = mysql_query("SELECT * FROM fiche_suivi WHERE
                                                                                                    fiche_utilisateur_no = $id_app AND
                                                                                                    fiche_auteur_no != $id_user AND
                                                                                                    fiche_grp_no = $id_grp AND
                                                                                                    (fiche_autraction_lb = 'observation' OR fiche_autraction_lb = 'appreciation') AND
                                                                                                    fiche_date_dt >= '$date_der'");
                                                $nb_fiche_obs = mysql_num_rows($req_fiche_obs);
                                                if ($nb_fiche_obs > 0)
                                                {
                                                        $lien = "fiche_suivi.php?utilisateur=$id_app&consulter=1&objet=observation&event=2&ordre=asc&tuteur_vue=1&date_der=$date_der&id_grp=$id_grp&vientde=annonce";
                                                        $lien = urlencode($lien);
                                                        echo "<TD align='center' valign='top' width='30'><DIV id='sequence'>".
                                                             "<A HREF=\"trace.php?link=$lien&keepThis=true&TB_iframe=true&height=455&width=700\"".
                                                             " class='thickbox'>$nb_fiche_obs</A></DIV></TD>";
                                                }
                                                else
                                                        echo "<TD align='center' valign='top' width='30'>&nbsp;</TD>";
                                                $nom_grp = GetDataField($connect, "select grp_nom_lb from groupe where grp_cdn  = $id_grp", "grp_nom_lb");
                                                $id_forum = GetDataField($connect, "select id from forums where name='$nom_grp'", "id");
                                                $nom_forum = GetDataField($connect, "select table_name from forums where name='$nom_grp'", "table_name");
                                                $req_forum = mysql_query("SELECT * from $nom_forum where author = \"$majuscule1\" AND email = \"$id_email\" and approved='Y'");
                                                $nbr_posts = mysql_num_rows($req_forum);
                                                if ($nbr_posts > 0)
                                                {
                                                        $lien = "popup_forums.php?NbrPost=$nbr_posts&id_forum=$id_forum&utilisateur=$id_app&id_grp=$id_grp";
                                                        $lien = urlencode($lien);
                                                        echo "<TD align='center' valign='top' width='50'>" .
                                                        "<A HREF=\"trace.php?link=$lien&keepThis=true&TB_iframe=true&height=455&width=700\"".
                                                        " class='thickbox'>$nbr_posts</A></TD>";
                                                }
                                                else
                                                        echo "<TD width='70'>&nbsp;</TD>";
                                                $req_traq = mysql_query("select traq_cdn from traque where traq_util_no=$id_app AND traq_grp_no= $id_grp");
                                                $nbr_trq = mysql_num_rows($req_traq);
                                                if ($nbr_trq > 0)
                                                {
                                                        $lien_bilan = "bilan.php?bilan=1&utilisateur=$id_app&numero_groupe=$id_grp&keepThis=true&TB_iframe=true&height=455&width=700";
                                                        //$lien_bilan = urlencode($lien_bilan);
                                                        echo "<TD align='center' valign='top'><DIV id='sequence'><A HREF=\"$lien_bilan\"  class='thickbox'" .
                                                        " onmouseover=\"img_regroup$n_app.src='images/bilan/icogroupb.gif';return true;\"" .
                                                        " onmouseout=\"img_regroup$n_app.src='images/bilan/icogroup.gif'\">" .
                                                        "<IMG NAME=\"img_regroup$n_app\" SRC=\"images/bilan/icogroup.gif\" BORDER='0' width='28' height='25'" .
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
                        if ((isset($groupe_affiche[$id_grp]) && $groupe_affiche[$id_grp] == 1) || $affiche_groupe == 3 || $nomb_grp_form == 1)
                        {
                                echo "<tr height='6'><td></td></tr></table></div></td></tr>";
                                echo "<TR><TD width='100%' height='20'></TD></TR>";
                        }
                        else
                                echo "<tr height='6'><td></td></tr></table></td></tr>";
                }
                $i++;
        }
}
 else
{
        echo "<TR><TD align='center'>$mess_alrt_rf</TD></TR>";
}
echo "</TABLE></TD></TR></TABLE></TD></TR></TABLE></BODY></HTML>";
?>