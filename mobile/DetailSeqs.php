<?php
session_start();
if (!isset($_SESSION['IDUSER']) || $_SESSION['IDUSER'] == "")
{
  exit();
}
require ("../admin.inc.php");
require ("../fonction.inc.php");
require ("../fonction_html.inc.php");
require ("../langfr.inc.php");
dbConnect();
include ("../include/varGlobals.inc.php");
$content='';
if (isset($_GET['toutes']))
{
   if (isset($_GET['SD']))
     $req = mysql_query("select * from prescription_".$_GET['id_grp'].",suivi2_".$_GET['id_grp'].",sequence where
                   presc_utilisateur_no = '".$_SESSION['IDUSER']."' and suiv2_utilisateur_no='".$_SESSION['IDUSER'].
                   "' and suiv2_seq_no=presc_seq_no and presc_seq_no=seq_cdn and
                   presc_datefin_dt < CURRENT_DATE and (suiv2_etat_lb = 'A FAIRE' || suiv2_etat_lb = 'EN COURS') 
                   order by presc_datefin_dt desc");
   elseif(isset($_GET['SU']))
     $req = mysql_query("select * from prescription_".$_GET['id_grp'].",suivi2_".$_GET['id_grp'].",sequence where
                   presc_utilisateur_no = '".$_SESSION['IDUSER']."' and suiv2_utilisateur_no='".$_SESSION['IDUSER'].
                   "' and suiv2_seq_no=presc_seq_no and presc_seq_no=seq_cdn and
                   (presc_datefin_dt > CURRENT_DATE + INTERVAL 1 DAY) AND 
                   (presc_datefin_dt < CURRENT_DATE + INTERVAL 11 DAY) and
                    (suiv2_etat_lb = 'A FAIRE' || suiv2_etat_lb = 'EN COURS') order by presc_datefin_dt desc");
   $nb_req = mysql_num_rows($req);//echo $nb_req;exit;
   if ($nb_req > 0)
   {

       while ($row = mysql_fetch_object($req))
       {
            $result[] = $row;
       }
       $leTitre = (isset($_GET['SD'])) ? 'Liste des séquences Hors Délais' : 'Liste des séquences urgentes';
       $content .=  "<div id='msg".$i."' style='clear:both;float:left;max-width:610px;background-color:yellow;".
                    "font-size:14px;width:95%;padding:4px;margin-top:6px;color:#000; border:2px solid green;'> ".
                    "<div style='margin:4px;padding:2px;'><span style='font-weight:bold;'>".$leTitre ."</span></div>".
                    "<div style='margin:4px;padding:2px;font-size:11px;'>".
                    "(Cliquez sur les boutons pour dérouler le détail des activités de la séquence)</div></div>";
       for ($i=0;$i<$nb_req;$i++)
       {
           $couleur = (($i/2) > floor($i/2)) ? 'background-color:#fff' : 'background-color:#eee';
           $module=GetDataField ($connect,"select parcours_nom_lb from parcours WHERE parcours_cdn = '".
                                           $result[$i]->presc_parc_no."'","parcours_nom_lb");
           $nom_formateur=GetDataField ($connect,"select util_nom_lb from utilisateur WHERE util_cdn = '".
                                                 $result[$i]->presc_formateur_no."'","util_nom_lb");
           $prenom_formateur=GetDataField ($connect,"select util_prenom_lb from utilisateur WHERE util_cdn = '".
                                                 $result[$i]->presc_formateur_no."'","util_prenom_lb");
           $majuscule = $prenom_formateur." ".$nom_formateur;
           $tab = explode(' ',trim($result[$i]->seq_type_lb));
           $letype = $tab[0];
           $TypeSeq = (isset($_GET['SD'])) ? 'SD' : 'SU';
           $content .= "<div id='msg".$i."' style='clear:both;float:left;max-width:610px;".$couleur.
                       ";font-size:12px;width:95%;padding:4px;margin-top:6px;color:#000; border:2px solid #000;' >".
                       "<div style='margin:4px;padding:2px;'><span style='font-weight:bold;'>".
                       "Date de fin : </span>".reverse_date($result[$i]->presc_datefin_dt,'-','-')."</div>".
                       "<div style='margin:4px;padding:2px;'><span style='font-weight:bold;'>".
                       "Formateur : </span>".$majuscule."</div>".
                       "<div style='margin:4px;padding:2px;'><span style='font-weight:bold;'>".
                       "Cette séquence appartient au module : </span>".$module."</div>".
                       "<div style='margin:4px;padding:2px;'><span style='font-weight:bold;'>".
                       "Titre de la séquence : </span>".$result[$i]->seq_titre_lb."</div>".
                       "<div style='margin:4px;padding:2px;background-color:#ddd; border:1px solid #bbb;max-width:600px;'>".
                       "<span style='font-weight:bold;'>Description : </span>".
                       html_entity_decode($result[$i]->seq_desc_cmt,ENT_QUOTES,'ISO-8859-1')."</div>";
           $content .= '<div style="clear:both;margin-bottom:2px;font-size:12px;color:#000;cursor:pointer;" >'.
                       '<a class="ui-btn ui-shadow ui-btn-corner-all ui-mini ui-btn-inline ui-btn-icon-left '.
                       'ui-btn-up-b" data-wrapperels="span" data-iconshadow="true" data-shadow="true" '.
                       'data-corners="true" href="#" data-role="button" data-mini="true" data-inline="true" '.
                       ' data-theme="b" onClick="$(\'#commencer\').load(\'detailSeqs.php?'.
                       $TypeSeq.'=1&seq='.$result[$i]->presc_seq_no.'&parc='.
                       $result[$i]->presc_parc_no.'&id_grp='.$_GET['id_grp'].'&type='.$letype.'\');" >'.
                       '<span class="ui-btn-inner"><span class="ui-btn-text">Activités de cette séquence</span>'.
                       '</span></a></div>';
           $content .=  "</div>";
      }
      echo utf2Charset(stripslashes($content),"iso-8859-1");
   }
}
elseif(isset($_GET['seq']))
{
    if ($_GET['type'] == 'NORMAL')
         $req=mysql_query("select * from suivi1_".$_GET['id_grp'].",activite where
                          suivi_utilisateur_no='".$_SESSION['IDUSER']."' and suivi_act_no=act_cdn and
                          act_seq_no='".$_GET['seq']."' and suivi_grp_no='".$_GET['id_grp']."' order by act_cdn");
    else
         $req=mysql_query("select * from scorm_util_module_".$_GET['id_grp'].",scorm_module where
                          user_module_no='".$_SESSION['IDUSER']."' and mod_module_no=mod_cdn and
                          mod_seq_no='".$_GET['seq']."' and mod_parc_no='".$_GET['parc']."' and
                          mod_grp_no='".$_GET['id_grp']."' order by mod_cdn");

    $nb_req = mysql_num_rows($req);
    $TypeSeq = (isset($_GET['SD'])) ? 'SD' : 'SU';
    $leTitre = (isset($_GET['SD'])) ? 'Revenir aux séquences Hors Délais' : 'Revenir aux séquences urgentes';
    if ($nb_req > 0)
    {

       while ($row = mysql_fetch_object($req))
       {
            $result[] = $row;
       }
       $module=GetDataField ($connect,"select parcours_nom_lb from parcours WHERE parcours_cdn = '".$_GET['parc']."'","parcours_nom_lb");
       $sequence=GetDataField ($connect,"select seq_titre_lb from sequence WHERE seq_cdn = '".$_GET['seq']."'","seq_titre_lb");
       $content .= "<div id='msg".$i."' style='clear:both;float:left;max-width:610px;background-color:yellow;".
                   "font-size:14px;width:95%;padding:4px;margin-top:6px;color:#000; border:2px solid green;'> ".
                   "<div style='margin:4px;padding:2px;'><span style='font-weight:bold;'>Module : </span>".$module."</div>".
                   "<div style='margin:4px;padding:2px;'><span style='font-weight:bold;'>Séquence : </span>".$sequence."</div>";
       $content .= '<div style="clear:both;margin-bottom:2px;font-size:12px;color:#000;cursor:pointer;" >'.
                  '<a class="ui-btn ui-shadow ui-btn-corner-all ui-mini ui-btn-inline ui-btn-icon-left '.
                  'ui-btn-up-b" data-wrapperels="span" data-iconshadow="true" data-shadow="true" '.
                  'data-corners="true" href="#" data-role="button" data-mini="true" data-inline="true" '.
                  ' data-theme="b" onClick="$(\'#commencer\').load(\'DetailsSeqs.php?'.
                  $TypeSeq.'=1&toutes=1&id_grp='.$_GET['id_grp'].'\');" >'.
                  '<span class="ui-btn-inner"><span class="ui-btn-text">'.$leTitre.'</span>'.
                  '</span></a></div>';
       $content .= "</div>";
       for ($i=0;$i<$nb_req;$i++)
       {
          $couleur = (($i/2) > floor($i/2)) ? 'background-color:#fff' : 'background-color:#eee';
          $content .= "<div id='msg".$i."'style='clear:both;float:left;max-width:610px;".$couleur.";".
                      "font-size:12px;width:95%;padding:4px;margin-top:6px;color:#000; border:2px solid #000;'> ";

          if ($_GET['type'] == 'NORMAL')
          {
             $content .= "<div style='margin:4px;padding:2px;'><span style='font-weight:bold;'>Nom de l'activité : ".
                       "</span>".$result[$i]->act_nom_lb."</div>".
                       "<div style='margin:4px;padding:2px;background-color:#ddd; border:1px solid #bbb;max-width:600px;'>".
                       "<span style='font-weight:bold;'>Consigne : </span>".
                       html_entity_decode($result[$i]->act_consigne_cmt,ENT_QUOTES,'ISO-8859-1')."</div>";
             if ($result[$i]->act_commentaire_cmt != '')
                $content .= "<div style='margin:4px;padding:2px;'><span style='font-weight:bold;'>Commentaire : </span>".
                           html_entity_decode($result[$i]->act_commentaire_cmt,ENT_QUOTES,'ISO-8859-1')."</div>";
             $content .= "<div style='margin:4px;padding:2px;'><span style='font-weight:bold;'>".
                       "Etat actuel : </span>".$result[$i]->suivi_etat_lb."</div>".
                       "<div style='margin:4px;padding:2px;'><span style='font-weight:bold;'>".
                       "Activité notée : </span>".$result[$i]->act_notation_on."</div>".
                       "<div style='margin:4px;padding:2px;'><span style='font-weight:bold;'>".
                       "Devoir à rendre : </span>".$result[$i]->act_devoirarendre_on."</div>";
             if ($result[$i]->act_duree_nb > 0)
                $content .= "<div style='margin:4px;padding:2px;'><span style='font-weight:bold;'>".
                            "Durée : </span>".duree_calc($result[$i]->act_duree_nb)."</div>";
          }
          else
          {
             if ($result[$i]->lesson_status == "COMPLETED" || $result[$i]->lesson_status == "PASSED") $etat_img = $mess_fait;
             if ($result[$i]->lesson_status == "NOT ATTEMPTED") $etat_img = $mess_lanc_afaire;
             if ($result[$i]->lesson_status == "FAILED") $etat_img = $mess_echec;
             if ($result[$i]->lesson_status == "INCOMPLETE") $etat_img = $mess_nt;
             if ($result[$i]->lesson_status == "BROWSED") $etat_img = $mess_vu;
             $content .= "<div style='margin:4px;padding:2px;'><span style='font-weight:bold;'>Nom du module scorm : ".
                       "</span>".$result[$i]->mod_titre_lb."</div>";
             if ($result[$i]->mod_desc_cmt != '')
                $content .= "<div style='margin:4px;padding:2px;background-color:#ddd; border:1px solid #bbb;max-width:600px;'>".
                            "<span style='font-weight:bold;'>Description du scorm: </span>".
                            html_entity_decode($result[$i]->mod_desc_cmt,ENT_QUOTES,'ISO-8859-1')."</div>";
             if ($result[$i]->mod_duree_nb > 0)
                $content .= "<div style='margin:4px;padding:2px;'><span style='font-weight:bold;'>".
                            "Durée : </span>".duree_calc($result[$i]->mod_duree_nb)."</div>";
             if ($result[$i]->last_acces != '0000-00-00 00:00:00')
                $content .= "<div style='margin:4px;padding:2px;'><span style='font-weight:bold;'>".
                            "Dernier accès : </span>".$result[$i]->last_acces."</div>";
             $content .= "<div style='margin:4px;padding:2px;'><span style='font-weight:bold;'>".
                         "Etat actuel : </span>".$etat_img."</div>";
             if ($result[$i]->lesson_location != '')
                $content .= "<div style='margin:4px;padding:2px;'><span style='font-weight:bold;'>".
                            "Page atteinte : </span>".$result[$i]->lesson_location."</div>";
             if ($result[$i]->raw > 0)
                $content .= "<div style='margin:4px;padding:2px;'><span style='font-weight:bold;'>".
                            "Résultat obtenu : </span>".$result[$i]->raw."</div>";
          }
          $content .= "</div>";
      }
      echo utf2Charset(stripslashes($content),"iso-8859-1");
    }
    else
    {
       $content = "<div style='clear:both;float:left;max-width:610px;background-color:yellow;".
                   "font-size:14px;width:95%;padding:4px;margin-top:6px;color:#000; border:2px solid green;'>".
                   "Rien à afficher semble-t'il, en raison d'une incohérence ";
       $content .= '<div style="clear:both;margin-bottom:2px;font-size:12px;color:#000;cursor:pointer;" >'.
                  '<a class="ui-btn ui-shadow ui-btn-corner-all ui-mini ui-btn-inline ui-btn-icon-left '.
                  'ui-btn-up-b" data-wrapperels="span" data-iconshadow="true" data-shadow="true" '.
                  'data-corners="true" href="#" data-role="button" data-mini="true" data-inline="true" '.
                  'data-icon="check" data-theme="b" onClick="$(\'#commencer\').load(\'DetailsSeqs.php?'.
                  $TypeSeq.'=1&toutes=1&id_grp='.$_GET['id_grp'].'\');" >'.
                  '<span class="ui-btn-inner"><span class="ui-btn-text">'.$leTitre.'</span>'.
                  '<span class="ui-icon ui-icon-check ui-icon-shadow">&nbsp;</span></span></a></div>';
       $content = "</div>";

       echo utf2Charset("$content","iso-8859-1");
    }

}
?>
