<?php
session_start();
include ("include/UrlParam2PhpVar.inc.php");
if (!isset($_SESSION['id_user']) || $_SESSION['id_user'] == "")
{
  exit();
}
require "graphique/admin.inc.php";
require 'fonction.inc.php';
require "lang$lg.inc.php";
include ('include/varGlobals.inc.php');
dbConnect();

if ($lg == "ru")
{
  $police="arial";
  $code_langage = "ru";
  $charset = "cp-1251";
}
elseif ($lg == "fr")
{
  $police="arial";
  $code_langage = "fr";
  $charset = "iso-8859-1";
}
elseif ($lg == "en")
{
  $police="arial";
  $code_langage = "en";
  $charset = "iso-8859-1";
}
$Ext = '_'.$numero_groupe;
$nom_user = $_SESSION['name_user'];
$prenom_user = $_SESSION['prename_user'];
$email_user = $_SESSION['email_user'];
$lien_retour = "http://".$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
$nom_grp =GetDataField ($connect,"select grp_nom_lb from groupe WHERE grp_cdn = $numero_groupe","grp_nom_lb");
$carac_grp = strlen($nom_grp);
if ($carac_grp > 40)
   $nom_grp2 = substr($nom_grp,0,40)."...";
else
   $nom_grp2 = $nom_grp;
if ($carac_grp > 10)
   $nom_grp1 = substr($nom_grp,0,8)."...";
else
   $nom_grp1 = $nom_grp;
$fichier_pdf ="
<HTML>
<HEAD>
<META HTTP-EQUIV=\"Content-Type\" content=\"text/html; charset=$charset\">
<META HTTP-EQUIV=\"Content-Language\" CONTENT=\"$code_langage\">
<META HTTP-EQUIV=\"Expires\" CONTENT=\"Fri, Jan 01 1900 00:00:00 GMT\">
<META NAME=\"ROBOTS\" CONTENT=\"No Follow\">
<META HTTP-EQUIV=\"Cache-Control\" CONTENT=\"no-cache, must-revalidate\">
<META HTTP-EQUIV=\"Pragma\" CONTENT=\"no-cache\">
<TITLE>Date&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Signature de l'apprenant
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
Date
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
Signature du responsable</TITLE>
<STYLE>
BODY { font-family: $police; font-size: 12px; color: #333333 }
TD   { font-family: $police; font-size: 12px; color: #333333 }
TH   { font-family: $police; font-size: 12px; color: #333333 }
A         {font-family:$police;font-size:12px;color:#24677A;text-decoration:none}
A:link    {font-family:$police;font-size:12px;color:#24677A;font-weight:bold}
A:visited {font-family:$police;font-size:12px;color:#24677A;font-weight:bold}
A:hover   {font-family:$police;font-size:12px;color=#D45211;font-weight:bold}
A.off     {font-family:$police;font-size:12px;color:#24677A;font-weight:bold}

#titre A:link{background-repeat:no-repeat;background-position:1% 50%;color:#3BACC4;}
#titre A:visited{background-repeat:no-repeat;background-position:1% 50%;color:#3BACC4;}
#titre A:hover {background-repeat:no-repeat;background-position:1% 50%;color:#D45211;}

#menu A:link{background-repeat:no-repeat;background-position:1% 50%;color:#002D44;}
#menu A:visited{background-repeat:no-repeat;background-position:1% 50%;color:#002D44;}
#menu A:hover {background-repeat:no-repeat;background-position:1% 50%;color:#D45211;}

#sequence A:link{background-repeat:no-repeat;background-position:1% 50%;color:#24677A;}
#sequence A:visited{background-repeat:no-repeat;background-position:1% 50%;color:#24677A;}
#sequence A:hover {background-repeat:no-repeat;background-position:1% 50%;color:#D45211;}

#seqinv A:link{background-repeat:no-repeat;background-position:1% 50%;color:#D45211;}
#seqinv A:visited{background-repeat:no-repeat;background-position:1% 50%;color:#D45211;}
#seqinv A:hover {background-repeat:no-repeat;background-position:1% 50%;color:#24677A;}

#parcours A:link{background-repeat:no-repeat;background-position:1% 50%;color:#002D44;}
#parcours A:visited{background-repeat:no-repeat;background-position:1% 50%;color:#002D44;}
#parcours A:hover {background-repeat:no-repeat;background-position:1% 50%;color:#FFFFFF;}

#parcseqtype A:link{background-repeat:no-repeat;background-position:1% 50%;color:red;}
#parcseqtype A:visited{background-repeat:no-repeat;background-position:1% 50%;color:red;}
#parcseqtype A:hover {background-repeat:no-repeat;background-position:1% 50%;color:#D45211;}

.clq {LEFT: 0px; VISIBILITY: hidden; POSITION: absolute; TOP: 0px}
.mar { font-family: arial;font-size:9px;;color='#800000' }
.small {font-family:arial;color:navy;font-size:11px;}
.admin {font-family:arial;color:#9999FF;font-size:13px}
.texte {font-family:arial;color:navy;font-size:13px}
.Softricks_Calendar {
        position: absolute;
        visibility: visible;
        top: 200;
        left: 10;
        height: 250;
        width: 260;
}
</STYLE>
</HEAD>
<BODY bgcolor='#FFFFFF' marginwidth='0' marginheight='0' leftmargin='0'topmargin='0'>
<div id=\"overDiv\" style=\"position:absolute; visibility:hidden;z-index:1000;\"></div>
<SCRIPT LANGUAGE=\"JavaScript\" SRC=\"overlib.js\"><!-- overLIB (c) Erik Bosrup --></SCRIPT>";
  // Début de la bloucle (parc/seq/activités
$fichier_pdf=  '';
if ($a_faire == 1)  {
    if ($id_ref>0){
      $referentiel = GetDataField ($connect,"select ref_nomabrege_lb from referentiel where ref_cdn = $id_ref","ref_nomabrege_lb");
      $nom_referentiel = GetDataField ($connect,"select ref_desc_cmt  from referentiel where ref_cdn = $id_ref","ref_desc_cmt");
      if ($nom_referentiel == "")
         $nom_referentiel = GetDataField ($connect,"select ref_nomabrege_lb from referentiel where ref_cdn = $id_ref","ref_nomabrege_lb");
    }
    if ($utilisateur > 0 && ($typ_user == "FORMATEUR_REFERENT" || ($typ_user == "RESPONSABLE_FORMATION" && $hgrp == 1))){
       $parc_query = mysql_query ("select suiv3_parc_no from suivi3$Ext,prescription$Ext,tuteur where
                                   prescription$Ext.presc_parc_no = suivi3$Ext.suiv3_parc_no and
                                   prescription$Ext.presc_utilisateur_no=$utilisateur and
                                   prescription$Ext.presc_utilisateur_no = $utilisateur and
                                   (prescription$Ext.presc_formateur_no=$id_user OR
                                   (tuteur.tut_apprenant_no = prescription$Ext.presc_utilisateur_no AND
                                   tuteur.tut_tuteur_no = $id_user)) AND
                                   prescription$Ext.presc_grp_no = $numero_groupe AND
                                   suivi3$Ext.suiv3_grp_no = $numero_groupe
                                   group by suivi3$Ext.suiv3_parc_no
                                   order by prescription$Ext.presc_ordre_no,prescription$Ext.presc_cdn");
       $num_app = $utilisateur;
    }elseif ($utilisateur > 0 &&  $typ_user == "TUTEUR"){
       $parc_query = mysql_query ("select suiv3_parc_no from suivi3$Ext,prescription$Ext where
                                   prescription$Ext.presc_parc_no = suivi3$Ext.suiv3_parc_no and
                                   prescription$Ext.presc_utilisateur_no=$utilisateur AND
                                   prescription$Ext.presc_grp_no = $numero_groupe AND
                                   suivi3$Ext.suiv3_grp_no = $numero_groupe
                                   group by suivi3$Ext.suiv3_parc_no
                                   order by prescription$Ext.presc_ordre_no,prescription$Ext.presc_cdn");
       $num_app = $utilisateur;
    }elseif ($utilisateur > 0 && ($typ_user == "ADMINISTRATEUR" || ($typ_user == "RESPONSABLE_FORMATION" && $hgrp != 1))){
       $parc_query = mysql_query ("select all suiv3_parc_no from suivi3$Ext,prescription$Ext where
                                   prescription$Ext.presc_parc_no = suivi3$Ext.suiv3_parc_no and
                                   prescription$Ext.presc_utilisateur_no=$utilisateur AND
                                   prescription$Ext.presc_grp_no = $numero_groupe AND
                                   suivi3$Ext.suiv3_grp_no = $numero_groupe
                                   group by suivi3$Ext.suiv3_parc_no
                                   order by prescription$Ext.presc_ordre_no,prescription$Ext.presc_cdn");
       $num_app = $utilisateur;
    }elseif ($typ_user == "APPRENANT"){
       $parc_query = mysql_query ("select all suiv3_parc_no from suivi3$Ext,prescription$Ext where
                                   presc_parc_no = suiv3_parc_no and
                                   presc_utilisateur_no = $id_user AND
                                   presc_grp_no = $numero_groupe AND
                                   suiv3_grp_no = $numero_groupe
                                   group by suiv3_parc_no 
                                   order by presc_ordre_no,presc_cdn");
       $num_app = $id_user;
    }
    
    $nom_user = GetDataField ($connect,"select util_nom_lb from utilisateur where util_cdn='$num_app'","util_nom_lb");
    $prenom_user = GetDataField ($connect,"select util_prenom_lb from utilisateur where util_cdn='$num_app'","util_prenom_lb");
    $inscripteur = GetDataField ($connect,"select util_auteur_no from utilisateur where util_cdn='$num_app'","util_auteur_no");
    $nb_parc = mysql_num_rows ($parc_query);
    if ($nb_parc == 0 && $typ_user == "APPRENANT"){
       $fichier_pdf .=  "<CENTER><TABLE border='0' cellspacing='2' cellpadding='0'><TR><TD>";
       $fichier_pdf .=  "<TABLE border='0' cellspacing='2' cellpadding='0'>";
       $fichier_pdf .=  "<TR><TD colspan='2' height='40' align='center' valign='center'><Font size='3' color='#FFFFFF'><B>$mess_menu_suivi</B></FONT></TD></TR>";
       $fichier_pdf .=  "<TR><TD><Font size='2'>$mess_gp_tut_nopresc1</TD></TR>";
       $fichier_pdf .=  "<TR><TD align=left><A HREF=\"javascript:history.back();\" TITLE=\"$alter\" onmouseover=\"img_annonce.src='$adresse_http/images/gest_parc/boutretour2b.gif';return true;\" onmouseout=\"img_annonce.src='$adresse_http/images/gest_parc/boutretour2.gif'\">";
       $fichier_pdf .=  "<IMG NAME=\"img_annonce\" SRC=\"$adresse_http/images/gest_parc/boutretour2.gif\" border='0' onLoad=\"tempImg=new Image(0,0); tempImg.src='$adresse_http/images/gest_parc/boutretour2b.gif'\"></A></TD>";
       $fichier_pdf .=  "</TD></TR></TABLE></TD></TR></TABLE>";
       exit;
    }elseif ($nb_parc == 0 && $typ_user != "APPRENANT"){
       $fichier_pdf .=  "<CENTER><TABLE border='0' cellspacing='2' cellpadding='0'><TR><TD>";
       $fichier_pdf .=  "<TABLE border='0' cellspacing='1' cellpadding='0'>";
       $fichier_pdf .=  "<TR><TD colspan='2' height='40' align='center' valign='center'><Font size='3' color='#FFFFFF'><B>$mess_menu_suivi</B></FONT></TD></TR>";
       $fichier_pdf .=  "<TR><TD><Font size='2'>$mess_gp_tut_nopresc</TD></TR>";
       $fichier_pdf .=  "<TR><TD align=left><A HREF=\"javascript:history.back();\" TITLE=\"$alter\" onmouseover=\"img_annonce.src='$adresse_http/images/gest_parc/boutretour2b.gif';return true;\" onmouseout=\"img_annonce.src='$adresse_http/images/gest_parc/boutretour2.gif'\">";
       $fichier_pdf .=  "<IMG NAME=\"img_annonce\" SRC=\"$adresse_http/images/gest_parc/boutretour2.gif\" border='0' onLoad=\"tempImg=new Image(0,0); tempImg.src='$adresse_http/images/gest_parc/boutretour2b.gif'\"></A></TD>";
       $fichier_pdf .=  "</TD></TR></TABLE></TD></TR></TABLE>";
       exit;
    }
    $fichier_pdf .=  "<CENTER><TABLE border='1' cellspacing='2' cellpadding='1' ><TR><TD width='100%'>";
    $fichier_pdf .=  "<TABLE border='0' cellspacing='1' cellpadding='0' width='100%' border='1'>";
    $letitre = "<TABLE border='0' width='100%' height='36' cellspacing='0' cellpadding='0' border='0'><TR>";
    $date_heure = date("d - m - Y    H : i : s" ,time());
   if ($utilisateur > 0)
      $letitre .= "<TD width='100%' nowrap align='center'><FONT color='#000000'><H3><B>$mess_menu_suivi : ".ucfirst(strtolower($nom_user))." ".ucfirst(strtolower($prenom_user))." $nom_grp1  [ $mess_mess_date : $date_heure ]</B></H3></FONT></TD>";
   else
      $letitre .= "<TD width='100%' nowrap align='center'><FONT color='#000000'><H3><B>$mess_menu_sal_cours $nom_grp2  [ $mess_mess_date : $date_heure ]</B></H3></FONT></TD>";
   $letitre .= "</TR></TABLE>";
   $fichier_pdf .=  "<TR><TD background=\"$adresse_http/images/fond_titre_table.jpg\" height='36' align='center' valign='center' width='90%'>$letitre</TD><TD width='10%' valign='center' align='center'><B>$mess_signat</B></TD></TR>";

//--------------------------Début de la boucle parcours > sequences > activités--------------------------------------------
//-------------------------------------------------------------------------------------------------------------------------
    $fichier_pdf .=  "<TR><TD width = '90%'>";
    $fichier_pdf .=  "<TABLE border='0' width='100%' cellspacing='1' cellpadding='0'>";
    $fichier_pdf .=  "<TR height='25'>";
    $fichier_pdf .=  "<TD align='left'>&nbsp;</TD>";
    $fichier_pdf .=  "<TD align='left' width='66%' nowrap><b>&nbsp;&nbsp;$mess_gp_parc_appr&nbsp;&nbsp;</b></TD>";
    $fichier_pdf .=  "<TD align='left' width='8%' nowrap><b>&nbsp;&nbsp;$mess_gp_duree&nbsp;&nbsp;</b></TD>";
    $fichier_pdf .=  "<TD align='left' width='8%' nowrap><b>&nbsp;&nbsp;$mess_gp_date_deb&nbsp;&nbsp;</b></TD>";
    $fichier_pdf .=  "<TD align='left' width='8%' nowrap><b>&nbsp;&nbsp;$mess_gp_date_fin&nbsp;&nbsp;</b></TD>";
    $fichier_pdf .=  "<TD align='left' width='8%' nowrap><b>&nbsp;&nbsp;$mess_gp_etat&nbsp;&nbsp;</b></TD>";
    $fichier_pdf .=  "</TR>";
    $p=0;
    while ($p < $nb_parc ) {
          $compte++;
          $fin_parc=0;
          $id_parc = mysql_result ($parc_query,$p,"suiv3_parc_no");
          $etat_parc =  GetDataField ($connect,"select suiv3_etat_lb from suivi3$Ext where
                                                suiv3_parc_no = $id_parc and
                                                suiv3_utilisateur_no = $num_app AND
                                                suiv3_grp_no = $numero_groupe","suiv3_etat_lb");
          $type_parcours =  GetDataField ($connect,"select parcours_type_lb from parcours where parcours_cdn = $id_parc","parcours_type_lb");
          $scormOk = strstr($type_parcours,"SCORM") ? 1 : 0;
              if ($id_parc != 0)
              {
                 $createur = GetDataField ($connect,"select parcours_auteur_no from parcours where parcours_cdn = $id_parc","parcours_auteur_no");
                 $nom = GetDataField ($connect,"select parcours_nom_lb from parcours where parcours_cdn = $id_parc","parcours_nom_lb");
                 $description = GetDataField ($connect,"select parcours_desc_cmt from parcours where parcours_cdn = $id_parc","parcours_desc_cmt");
                 $id_ref_parc = GetDataField ($connect,"select parcours_referentiel_no from parcours where parcours_cdn = $id_parc","parcours_referentiel_no");
                 $nom_referentiel = GetDataField ($connect,"select ref_desc_cmt from referentiel where ref_cdn = $id_ref_parc","ref_desc_cmt");
                 $desc = str_replace ("'","\'",$nom_referentiel);
                 $nom_createur = GetDataField ($connect,"select util_nom_lb from utilisateur where util_cdn = $createur","util_nom_lb");
                 $prenom_createur = GetDataField ($connect,"select util_prenom_lb from utilisateur where util_cdn = $createur","util_prenom_lb");
              }else{
                 $nom = GetDataField ($connect,"select parcours_nom_lb from parcours where parcours_cdn = $id_parc","parcours_nom_lb");
                 $description = GetDataField ($connect,"select parcours_desc_cmt from parcours where parcours_cdn = $id_parc","parcours_desc_cmt");
                 $desc = $mess_gp_nolien_ref;
                 $nom_createur = $mess_gen_formagri;
                 $prenom_createur = "";//$mess_gen_gen_formagri;
                 $nom_referentiel = $mess_gp_nolien_ref;
              }
              $nom = strip_tags($nom);
              $nom_parc=$nom;
              $lien = "";
              if ($deroule_parc[$p] == 1 && ($id_parc == $parc || $parc_ouvert == $id_parc) && (!isset($switch) || (isset($switch) && $switch != 1)))
                $deroulee_parc[$p] = 0;
              else
                $deroulee_parc[$p] = 1;
              if (($deroule_parc[$p] == 1 || $parc_ouvert == $id_parc) || $actif_parc == $id_parc || $tout == 1){
                 $fichier_pdf .=  "<TR height='30' width='100%'>";
                 $lien = "";
                 $fichier_pdf .=  "<TD align='center' valign='top' width='3%'>";
                 $fichier_pdf .=  "&nbsp;</TD>";
                 $lien = "";
                 $fichier_pdf .=  "<TD align='left' valign='top'><DIV id='parcours'>&nbsp;&nbsp;$nom:&nbsp; <SMALL>".
                                  strip_tags(html_entity_decode($description,ENT_QUOTES,'ISO-8859-1'))."</SMALL></DIV></TD>";
              }else{
                 $fichier_pdf .=  "<TR height='30'>";
                 $lien = "";
                 $fichier_pdf .=  "<TD align='center' valign='top' width='3%'>&nbsp;</TD>";

                 $lien = "";
                 $fichier_pdf .=  "<TD align='left' valign='top'><DIV id='sequence'>&nbsp;&nbsp;$nom:&nbsp <SMALL>".
                                  strip_tags(html_entity_decode($description,ENT_QUOTES,'ISO-8859-1'))."</SMALL></DIV></TD>";
              }
              $req_min= mysql_query("select presc_datedeb_dt from prescription$Ext where
                                     presc_utilisateur_no = $num_app and
                                     presc_parc_no = $id_parc AND
                                     presc_grp_no = $numero_groupe
                                     order by presc_datedeb_dt asc");
              $date_min = mysql_result($req_min,0,"presc_datedeb_dt");
              $nb_jours_req_min = mysql_query ("select TO_DAYS('$date_min')");
              $req_max= mysql_query("select presc_datefin_dt from prescription$Ext where
                                     presc_utilisateur_no = $num_app and
                                     presc_parc_no = $id_parc AND
                                     presc_grp_no = $numero_groupe
                                     order by presc_datefin_dt asc");
              $nbr_max = mysql_num_rows($req_max);
              $date_max = mysql_result($req_max,$nbr_max-1,"presc_datefin_dt");
              $nb_jours_req_max = mysql_query ("select TO_DAYS('$date_max')");
//              if ($scormOk == 1)
                 $seq_parc_query = mysql_query ("SELECT sum(sequence.seq_duree_nb) from sequence,sequence_parcours where sequence_parcours.seqparc_parc_no = $id_parc and  sequence.seq_cdn = sequence_parcours.seqparc_seq_no");
//              else
//                 $seq_parc_query = mysql_query ("select sum(activite.act_duree_nb) from suivi2$Ext,prescription$Ext,suivi1$Ext,activite where activite.act_seq_no = suivi2$Ext.suiv2_seq_no and suivi2$Ext.suiv2_seq_no = prescription$Ext.presc_seq_no and prescription$Ext.presc_utilisateur_no=$num_app and suivi1$Ext.suivi_act_no = activite.act_cdn and prescription$Ext.presc_parc_no=$id_parc and suivi1$Ext.suivi_utilisateur_no = $num_app and suivi2$Ext.suiv2_utilisateur_no = $num_app and prescription$Ext.presc_utilisateur_no = $num_app");
              $duree_parc = mysql_result($seq_parc_query,0);
              $duree_parc = duree_calc($duree_parc);
              if (($deroule_parc[$p] == 1 || $parc_ouvert == $id_parc) || $actif_parc == $id_parc || $tout == 1)
                 $fichier_pdf .=  "<TD align='left' valign='top' nowrap><B>&nbsp;&nbsp;$duree_parc&nbsp;&nbsp;</B></TD>";
              else
                 $fichier_pdf .=  "<TD align='left' valign='top' nowrap >&nbsp;&nbsp;$duree_parc&nbsp;&nbsp;</TD>";
              $ch_date_deb = explode ("-",$date_min);
              $date_min = "$ch_date_deb[2]/$ch_date_deb[1]/$ch_date_deb[0]";
              $ch_date_fin = explode ("-",$date_max);
              $date_max = "$ch_date_fin[2]/$ch_date_fin[1]/$ch_date_fin[0]";
              if ($deroule_parc[$p] == 1){
                 $fichier_pdf .=  "<TD align='left' valign='top'><B>&nbsp;&nbsp;$date_min</B></TD>";
                 $fichier_pdf .=  "<TD align='left' valign='top'><B>&nbsp;&nbsp;$date_max</B></TD>";
              }else{
                 $fichier_pdf .=  "<TD align='left' valign='top'>&nbsp;&nbsp;$date_min&nbsp;&nbsp;</TD>";
                 $fichier_pdf .=  "<TD align='left' valign='top'>&nbsp;&nbsp;$date_max&nbsp;&nbsp;</TD>";
              }
              if ($etat_parc == "TERMINE")
                 $parcsuiv = $mess_lanc_termine;
              elseif ($etat_parc == "A FAIRE")
                 $parcsuiv = $mess_lanc_afaire;
              elseif ($etat_parc == "EN COURS")
                 $parcsuiv = $mess_lanc_encours;
              elseif ($etat_parc == "ATTENTE")
                 $parcsuiv = $mess_lanc_attente;
              $parc_suivi = $parcsuiv;
              if ($deroule_parc[$p] == 1)
                 $fichier_pdf .=  "<TD align='left' valign='top' nowrap><B>&nbsp;&nbsp;$parc_suivi&nbsp;&nbsp;</B></TD>";
              else
                 $fichier_pdf .=  "<TD align='left' valign='top' nowrap>&nbsp;&nbsp;$parc_suivi&nbsp;&nbsp;</TD>";
              if ($utilisateur > 0){
                 $nb_sans_seq = 0;
                 $prescripteur = GetDataField ($connect,"select presc_prescripteur_no from prescription$Ext where
                                                         presc_parc_no = $id_parc and
                                                         presc_utilisateur_no = $num_app AND
                                                         presc_grp_no = $numero_groupe","presc_prescripteur_no");
                 $formateur = GetDataField ($connect,"select presc_formateur_no from prescription$Ext where
                                                      presc_parc_no = $id_parc and
                                                      presc_utilisateur_no = $num_app AND
                                                      presc_grp_no = $numero_groupe","presc_formateur_no");
                 $sans_seq = mysql_query ("select presc_seq_no from prescription$Ext where
                                           presc_parc_no = $id_parc and
                                           presc_utilisateur_no = $num_app AND
                                           presc_grp_no = $numero_groupe");
                 $nb_sans_seq = mysql_num_rows ($sans_seq);
                 $signal=0;
              }
              $fichier_pdf .=  "</TR>";
  //Sequences a faire dans le parcours déroulé
  if ((($deroule_parc[$p] == 1 && $parc == $id_parc) || $tout == 1) || (isset($vp) && $vp == 1 && $id_parc == $parc_ouvert)){
   $ouvrir = 'parcours';
   $parc_ouvert = $id_parc;
       if ($utilisateur &&  $typ_user == "TUTEUR"){
          $seq_query = mysql_query ("SELECT DISTINCT suiv2_etat_lb,suiv2_seq_no FROM
                                     suivi2$Ext,prescription$Ext WHERE
                                     suivi2$Ext.suiv2_seq_no = prescription$Ext.presc_seq_no AND
                                     prescription$Ext.presc_utilisateur_no=$utilisateur AND
                                     prescription$Ext.presc_parc_no=$id_parc AND
                                     prescription$Ext.presc_grp_no = $numero_groupe AND
                                     suiv2_utilisateur_no = $utilisateur AND
                                     suiv2_grp_no = $numero_groupe
                                     ORDER BY prescription$Ext.presc_datefin_dt,prescription$Ext.presc_ordre_no,suivi2$Ext.suiv2_ordre_no ASC");
          $num_app = $utilisateur;
       }elseif ($utilisateur && ($typ_user == "ADMINISTRATEUR"  || $typ_user == "FORMATEUR_REFERENT" || $typ_user == "RESPONSABLE_FORMATION")){
          $seq_query = mysql_query ("SELECT DISTINCT suiv2_etat_lb,suiv2_seq_no FROM
                                     suivi2$Ext,prescription$Ext WHERE
                                     suivi2$Ext.suiv2_seq_no = prescription$Ext.presc_seq_no AND
                                     prescription$Ext.presc_utilisateur_no=$utilisateur AND
                                     prescription$Ext.presc_parc_no=$id_parc AND
                                     prescription$Ext.presc_grp_no = $numero_groupe AND
                                     suiv2_utilisateur_no = $utilisateur AND
                                     suiv2_grp_no = $numero_groupe
                                     ORDER BY prescription$Ext.presc_datefin_dt,prescription$Ext.presc_ordre_no,suivi2$Ext.suiv2_ordre_no ASC");
          $num_app = $utilisateur;
       }elseif(!$utilisateur){
          $seq_query = mysql_query ("SELECT DISTINCT suiv2_etat_lb,suiv2_seq_no FROM
                                     suivi2$Ext,prescription$Ext WHERE
                                     suivi2$Ext.suiv2_seq_no = prescription$Ext.presc_seq_no AND
                                     prescription$Ext.presc_parc_no=$id_parc AND
                                     prescription$Ext.presc_grp_no = $numero_groupe AND
                                     suiv2_utilisateur_no = $id_user AND
                                     suiv2_grp_no = $numero_groupe
                                     ORDER BY prescription$Ext.presc_datefin_dt,prescription$Ext.presc_ordre_no,suivi2$Ext.suiv2_ordre_no ASC");
          $num_app = $id_user;
       }
       $nb_seq = mysql_num_rows ($seq_query);
       $fichier_pdf .= "<TR><TD valign='top' align='right' width='3%'>&nbsp;</TD>";
       $fichier_pdf .= "<TD colspan='5' align='left'>";
       $fichier_pdf .= "<TABLE border='0' width='95%' cellspacing='1' cellpadding='3'>";
       $fichier_pdf .= "<TR height='30' width='100%'>";
       $fichier_pdf .= "<TD width='1%'>&nbsp;</TD>";
       $fichier_pdf .= "<TD align='left' width='68%'><b>&nbsp;&nbsp;$mess_gp_seq_parc&nbsp;&nbsp;</b></TD>";
       $fichier_pdf .= "<TD align='left' width='6%'><b>&nbsp;&nbsp;$msq_prereq&nbsp;&nbsp;</b></TD>";
       $fichier_pdf .= "<TD align='left' width='6%'><b>&nbsp;&nbsp;$mess_gp_duree&nbsp;&nbsp;</b></TD>";
       $fichier_pdf .= "<TD align='left' width='6%'><b>&nbsp;&nbsp;$mess_gp_date_deb&nbsp;&nbsp;</b></TD>";
       $fichier_pdf .= "<TD align='left' width='6%'><b>&nbsp;&nbsp;$mess_gp_date_fin&nbsp;&nbsp;</b></TD>";
       $fichier_pdf .= "<TD align='left' width='6%'><b>&nbsp;&nbsp;$mess_gp_etat&nbsp;&nbsp;</b></TD>";
       $fichier_pdf .= "</TR>";
       if ($utilisateur)
          $num_app = $utilisateur;
       else
          $num_app = $id_user;
       $compteur_fin_seq=0;
       $i = 0;
       while ($i != $nb_seq) {
              $compte++;
              $seq = mysql_result ($seq_query,$i,"suiv2_seq_no");
              $etat = mysql_result ($seq_query,$i,"suiv2_etat_lb");
              $desc_seq = GetDataField ($connect,"select seq_desc_cmt from sequence where seq_cdn=$seq","seq_desc_cmt");
              $type_sequence = GetDataField ($connect,"SELECT seq_type_lb from sequence where seq_cdn = '$seq'","seq_type_lb");
              if (strstr($type_sequence,"SCORM"))
                 $scormOk = 1;
              else
                 $scormOk = 0;
              $formateur = GetDataField ($connect,"select presc_formateur_no from prescription$Ext where
                                                   presc_seq_no = $seq and
                                                   presc_utilisateur_no = $num_app AND
                                                   presc_grp_no = $numero_groupe","presc_formateur_no");
              $prescripteur = GetDataField ($connect,"select presc_prescripteur_no from prescription$Ext where
                                                      presc_seq_no = $seq and
                                                      presc_utilisateur_no = $num_app AND
                                                      presc_grp_no = $numero_groupe","presc_prescripteur_no");
              $nom_form=GetDataField ($connect,"select util_nom_lb from utilisateur where util_cdn='$formateur'","util_nom_lb");
              $prenom_form=GetDataField ($connect,"select util_prenom_lb from utilisateur where util_cdn='$formateur'","util_prenom_lb");
              $prerequis="";
              //$proposable est un flag qui sert a savoir si la seq est proposable ou non
              // => 0 : NON     => 1 : OUI
              //on regarde prerequis => sequence proposable ou non
              $prereq_query = mysql_query ("select * from prerequis where prereq_seq_no = $seq");
              $nb_prereq = mysql_num_rows ($prereq_query);
              if ($etat == "TERMINE")
                 $compteur_fin_seq++;
              if ($compteur_fin_seq == $nb_seq && $i+1 == $nb_seq)
                 $fin_parc=1;
              if ($nb_prereq != 0) {
                $nb_proposable = 0;
                $jj = 0;
                while ($jj < $nb_prereq){
                  //on raisonne selon le type de condition
                  $type_condition = mysql_result ($prereq_query,$jj,"prereq_typcondition_lb");
                  if ($type_condition == 'SEQUENCE') {
                      $condition = mysql_result ($prereq_query,$jj,"prereq_seqcondition_no");
                      $etat_seq_req = GetDataField ($connect,"select suiv2_etat_lb from suivi2$Ext where
                                                              suiv2_seq_no = $condition and
                                                              suiv2_utilisateur_no = $num_app and
                                                              suiv2_grp_no = $numero_groupe","suiv2_etat_lb");
                      if ($etat_seq_req != 'TERMINE')  {
                          $motif = $mess_gp_seq_preq;
                          $proposable = 0;
                          $prerequis = "";
                      }else {
                        $nb_proposable++;
                      }
                  }

                  //on a besoin du numero de l'activite pour recuperer les notes
                  if ($type_condition == 'ACTIVITE') {
                      $condition = mysql_result ($prereq_query,$jj,"prereq_actcondition_no");
                      $etat_act_req = GetDataField ($connect,"select suivi_etat_lb from suivi1$Ext where
                                                              suivi_act_no = $condition and
                                                              suivi_utilisateur_no = $num_app and
                                                              suivi_grp_no = $numero_groupe","suivi_etat_lb");
                      if ($etat_act_req != 'TERMINE') {
                          $motif = $mess_gp_aut_act;
                          $proposable = 0;
                          $prerequis = "";//$mess_act_prq." <B>".str_replace("'","|",$act_prereq)."</B> ".$mess_no_fin;
                      }else {
                        $nb_proposable++;
                      }
                  }

                  if ($type_condition == 'NOTE') {
                     $condition = mysql_result ($prereq_query,$jj,"prereq_actcondition_no");
                      $etat_act_req = GetDataField ($connect,"select suivi_etat_lb from suivi1$Ext where
                                                              suivi_act_no = $condition and
                                                              suivi_utilisateur_no = $num_app and
                                                              suivi_grp_no = $numero_groupe","suivi_etat_lb");
                     if ($etat_act_req != 'TERMINE') {
                          $motif = $mess_gp_act_preq;
                          $proposable = 0;
                     }else{
                      $note_min = mysql_result ($prereq_query,$jj,"prereq_notemin_nb1");
                      $note_max = mysql_result ($prereq_query,$jj,"prereq_notemax_nb1");
                      $note_obtenue = GetDataField ($connect,"select suivi_note_nb1 from suivi1$Ext where
                                                              suivi_act_no = $condition and
                                                              suivi_utilisateur_no = $num_app and
                                                              suivi_grp_no = $numero_groupe"  ,"suivi_note_nb1");
                      if (($note_obtenue < $note_min || $note_obtenue > $note_max) || $note_obtenue == "acquis")  {
                        $motif = $mess_gp_note;
                        $proposable = 0;
                        $prerequis = "";//$mess_note_prq;
                      }else{
                        $nb_proposable++;
                      }
                    }
                  }
              $jj++;
              }
              if ($nb_proposable == $nb_prereq){
                 $proposable=1;
                 $prerequis="OK";
              }else
                 $proposable = 0;
            } //fin if ($nb_prereq !=0)
               $act_query = mysql_query ("select * from activite where act_seq_no = $seq order by act_ordre_nb");
               $Nb_act_seq = mysql_num_rows ($act_query);
               $aq = 0;
               $encore = 0;
            if ($utilisateur > 0){
              $date_deb = GetDataField ($connect,"select presc_datedeb_dt from prescription$Ext where
                                                  presc_seq_no = $seq and
                                                  presc_utilisateur_no = $utilisateur AND
                                                  presc_grp_no = $numero_groupe","presc_datedeb_dt");
              $date_fin = GetDataField ($connect,"select presc_datefin_dt from prescription$Ext where
                                                  presc_seq_no = $seq and
                                                  presc_utilisateur_no = $utilisateur AND
                                                  presc_grp_no = $numero_groupe","presc_datefin_dt");
            }else{
              $date_deb = GetDataField ($connect,"select presc_datedeb_dt from prescription$Ext where
                                                  presc_seq_no = $seq and
                                                  presc_utilisateur_no = $id_user AND
                                                  presc_grp_no = $numero_groupe","presc_datedeb_dt");
              $date_fin = GetDataField ($connect,"select presc_datefin_dt from prescription$Ext where
                                                  presc_seq_no = $seq and
                                                  presc_utilisateur_no = $id_user AND
                                                  presc_grp_no = $numero_groupe","presc_datefin_dt");
            }
              $today = date("Y/n/d");
              //On compare la date de deb a la date d'aujourd'hui pour savoir s'il peut encore faire la sequence
             //Pour simplifier le test, on selctionne le nombre de jours passes depuis an 0 pour $today et $date_deb
              $nb_date_fin_query = mysql_query ("select TO_DAYS('$date_fin')");
              $nb_date_fin = mysql_result ($nb_date_fin_query,0);
              $nb_date_deb_query = mysql_query ("select TO_DAYS('$date_deb')");
              $nb_date_deb = mysql_result ($nb_date_deb_query,0);
              $nb_today_query = mysql_query ("select TO_DAYS('$today')");
              $nb_today = mysql_result ($nb_today_query,0);
              for ($nn = 1;$nn < 10;$nn++){
                if ($nb_date_fin == ($nb_today+$nn) && $etat != "TERMINE")
                   $avertisseur = 1;
              }
              $depasse=0;

              $ch_date_deb = explode ("-",$date_deb);
              $date_deb = "$ch_date_deb[2]/$ch_date_deb[1]/$ch_date_deb[0]";
              $nom = GetDataField ($connect,"select seq_titre_lb from sequence where seq_cdn=$seq","seq_titre_lb");
              $nom = strip_tags($nom);
              $nom_seq = $nom;
              if ($utilisateur > 0){
                  $date_fin = GetDataField ($connect,"select presc_datefin_dt from prescription$Ext where
                                                      presc_seq_no = $seq and
                                                      presc_utilisateur_no = $utilisateur AND
                                                      presc_grp_no = $numero_groupe","presc_datefin_dt");
                  if ($scormOk == 1)
                    $duree_seq = GetDataField ($connect,"SELECT seq_duree_nb from sequence where seq_cdn = '$seq'","seq_duree_nb");
                  else{
                    $duree_sequence = mysql_query ("select sum(activite.act_duree_nb) from activite,suivi1$Ext where
                                                    activite.act_cdn = suivi1$Ext.suivi_act_no and
                                                    activite.act_seq_no=$seq and
                                                    suivi1$Ext.suivi_utilisateur_no =$utilisateur and
                                                    suivi1$Ext.suivi_grp_no =$numero_groupe");
                    $duree_seq = mysql_result($duree_sequence,0);
                  }
              }else{
                  $date_fin = GetDataField ($connect,"select presc_datefin_dt from prescription$Ext where
                                                      presc_seq_no = $seq and
                                                      presc_utilisateur_no = $id_user AND
                                                      presc_grp_no = $numero_groupe","presc_datefin_dt");
                  if ($scormOk == 1)
                    $duree_seq = GetDataField ($connect,"SELECT seq_duree_nb from sequence where seq_cdn = '$seq'","seq_duree_nb");
                  else{
                    $duree_sequence = mysql_query ("select sum(activite.act_duree_nb) from activite,suivi1$Ext where
                                                    activite.act_cdn = suivi1$Ext.suivi_act_no and
                                                    activite.act_seq_no=$seq and
                                                    suivi1$Ext.suivi_utilisateur_no =$id_user and
                                                    suivi1$Ext.suivi_grp_no =$numero_groupe");
                    $duree_seq = mysql_result($duree_sequence,0);
                  }
              }
              $ch_date_fin = explode ("-",$date_fin);
              $date_fin = "$ch_date_fin[2]/$ch_date_fin[1]/$ch_date_fin[0]";
              if ($nb_date_fin < $nb_today && $etat != "TERMINE"){
                 $depasse=1;
                 $motif = "<SMALL>$mess_acces_depasse : $date_fin</SMALL>";
              }
              if ($nb_date_fin < $nb_today && $etat == "TERMINE")
                 $autorise=1;
              if ($nb_date_deb <= $nb_today)
                  $visible = 1;
              else
                  $visible = 0;
                  //Besoin pour ouvrir les activites a partir de la sequence
            if ($utilisateur > 0){
                  $id_ref = GetDataField ($connect,"select insc_referentiel_no from inscription where insc_apprenant_no = $utilisateur","insc_referentiel_no");
                  $apprenant=0;
            }else{
                  $id_ref = GetDataField ($connect,"select insc_referentiel_no from inscription where insc_apprenant_no = $id_user","insc_referentiel_no");
                  $apprenant=1;
            }
            if ($desc_ref > 0){
              $nom_referentiel = GetDataField ($connect,"select ref_desc_cmt  from referentiel where ref_cdn = $id_ref","ref_desc_cmt");
              if ($nom_referentiel == "")
                $nom_referentiel = GetDataField ($connect,"select ref_nomabrege_lb from referentiel where ref_cdn = $id_ref","ref_nomabrege_lb");
            }
                  $ii=$i-1;
                  if ($nom_user == "Test")
                     $proposable = 1;
                  if (($autorise == 1 || $depasse != 1) && (($nb_prereq > 0 && $proposable == 1) || $nb_prereq == 0) && ((($marqueur == 1 && $marqueur[$ii] != 1) || $encore ==0) || ($utilisateur))){
                      $accord = 1;
                      if ((isset($deroule[$i]) && $deroule[$i] == 1 && $id_seq == $seq) || ($seq == $seq_ouverte && $seq_ouverte > 0) && $switch != 1)
                         $deroulee[$i] = 0;
                      else
                         $deroulee[$i] = 1;
                      if (isset($deroule[$i]) && $deroule[$i] == 1 || ($seq == $seq_ouverte && $seq_ouverte>0) || $tout == 1){
                        $fichier_pdf .=  "<TR>";
                        $lien = "";
                        $fichier_pdf .=  "<TD align='left' colspan='2'><DIV id='parcours'>";
                      }else{
                        $fichier_pdf .=  "<TR>";
                        $lien = "";
                        $fichier_pdf .=  "<TD align='left' colspan='2'><DIV id='seqinv'>";
                      }
                      $lien="";
                      if (isset($deroule[$i]) && $deroule[$i] == 1 || ($seq == $seq_ouverte && $seq_ouverte>0) || $tout == 1)
                         $fichier_pdf .=   "$nom&nbsp;&nbsp;&nbsp;";
                      else
                         $fichier_pdf .=   "$nom&nbsp;&nbsp;&nbsp;";
                      $lien = "";
                      if ($visible == 0){
                        $motif = "&nbsp;<SMALL>$msq_mess_deb_apres<SMALL>";
                        $fichier_pdf .=  "$motif";
                      }
                     $id_ref_seq = GetDataField ($connect,"select seqref_referentiel_no from sequence_referentiel where seqref_seq_no = '$seq'","seqref_referentiel_no");
                      $desc_ref = GetDataField ($connect,"select ref_desc_cmt  from referentiel where ref_cdn = '$id_ref_seq'","ref_desc_cmt");
                      if ($desc_ref == ""){
                        $desc_ref = GetDataField ($connect,"select ref_nomabrege_lb from referentiel where ref_cdn = $id_ref_seq","ref_nomabrege_lb");
                      }
                      $fichier_pdf .=  "<BR><SMALL> ".strip_tags(html_entity_decode($desc_seq,ENT_QUOTES,'ISO-8859-1'))."</SMALL></DIV></TD>";
                 }else{
                   $accord = 0;
                     if (isset($deroule[$i]) && $deroule[$i] == 1 || ($seq == $seq_ouverte && $seq_ouverte>0) || $tout == 1){
                        $lien = "";
                        $fichier_pdf .=  "<TR>";
                     }else{
                        $lien = "";
                        $fichier_pdf .=  "<TR>";
                     }
                      $id_ref_seq = GetDataField ($connect,"select seqref_referentiel_no from sequence_referentiel where seqref_seq_no = '$seq'","seqref_referentiel_no");
                      $desc_ref = GetDataField ($connect,"select ref_desc_cmt  from referentiel where ref_cdn = '$id_ref_seq'","ref_desc_cmt");
                      if ($desc_ref == ""){
                        $desc_ref = GetDataField ($connect,"select ref_nomabrege_lb from referentiel where ref_cdn = $id_ref_seq","ref_nomabrege_lb");
                      }
                      if (isset($deroule[$i]) && $deroule[$i] == 1 || ($seq == $seq_ouverte && $seq_ouverte>0) || $tout == 1){
                        $lien = "";
                        $fichier_pdf .=  "<TD align='left' colspan=2><DIV id='parcours'>$nom";
                      }else{
                        $lien = "";
                        $fichier_pdf .=  "<TD align='left' colspan=2><DIV id='seqinv'>$nom";
                      }
                      $fichier_pdf .=  " -->$motif<BR><SMALL>".strip_tags(html_entity_decode($desc_seq,ENT_QUOTES,'ISO-8859-1'))."</SMALL></DIV></TD>";
                  }
                  if ($nb_prereq == 0)
                    $fichier_pdf .=  "<TD align='left' nowrap><B>&nbsp;&nbsp;$mess_gp_aucun&nbsp;&nbsp;</B></TD>";
                  else{
                    $nom_prereq = GetDataField ($connect,"select seq_titre_lb from sequence where seq_cdn=$condition","seq_titre_lb");
                    $lien="";
                    if (isset($deroule[$i]) && $deroule[$i] == 1 || ($seq == $seq_ouverte && $seq_ouverte>0) || $tout == 1)
                       $fichier_pdf .=  "<TD align='left' nowrap><DIV id='parcours'>&nbsp;&nbsp;$msq_prereq&nbsp;$prerequis&nbsp;&nbsp;</DIV></TD>";
                    else
                       $fichier_pdf .=  "<TD align='left' nowrap><DIV id='sequence'>&nbsp;&nbsp;$msq_prereq&nbsp;$prerequis&nbsp;&nbsp;</DIV></TD>";
                  }
                  $duree = duree_calc($duree_seq);
                  if (isset($deroule[$i]) && $deroule[$i] == 1 && $id_seq == $seq)
                     $fichier_pdf .=  "<TD align='left' nowrap><B>&nbsp;&nbsp;$duree&nbsp;&nbsp;</B></TD>";
                  else
                     $fichier_pdf .=  "<TD align='left' nowrap>&nbsp;&nbsp;$duree&nbsp;&nbsp;</TD>";
                  if ($utilisateur > 0 && ($typ_user == "ADMINISTRATEUR" || $id_user == $formateur || $id_user == $prescripteur))
                      $date_debut = "<DIV id='sequence'>$date_deb</DIV>";
                  else
                      $date_debut = $date_deb;
                  if (isset($deroule[$i]) && $deroule[$i] == 1 && $id_seq == $seq)
                     $fichier_pdf .=  "<TD align='left' nowrap valign='center'><B>$date_debut</B></TD>";
                  else
                     $fichier_pdf .=  "<TD align='left' nowrap valign='center'>$date_debut</TD>";
                  if ($utilisateur > 0 && ($typ_user == "ADMINISTRATEUR"  || $id_user == $formateur || $id_user == $prescripteur))
                      $date_finale = "<DIV id='sequence'>$date_fin</DIV>";
                  else
                      $date_finale = $date_fin;
                  if (isset($deroule[$i]) && $deroule[$i] == 1 && $id_seq == $seq)
                     $fichier_pdf .=  "<TD align='left' nowrap valign='center'><B>$date_finale</B></TD>";
                  else
                     $fichier_pdf .=  "<TD align='left' nowrap valign='center'>$date_finale</TD>";
                   if ($etat == "TERMINE")
                     $actsuiv = $mess_lanc_termine;
                   elseif ($etat == "A FAIRE")
                     $actsuiv = $mess_lanc_afaire;
                   elseif ($etat == "EN COURS")
                     $actsuiv = $mess_lanc_encours;
                   elseif ($etat == "ATTENTE")
                     $actsuiv = $mess_lanc_attente;
                   $seq_suivi = $actsuiv;
                  if ($depasse == 1 && $etat != "TERMINE"){
                    if ($utilisateur && ($typ_user == "ADMINISTRATEUR"  || $id_user == $formateur || $id_user == $prescripteur))
                      $etat = "<DIV id='sequence'>&nbsp;&nbsp;$mess_gp_hsd&nbsp;&nbsp;</DIV>";
                    else
                      $etat = "&nbsp;&nbsp;$mess_gp_hsd&nbsp;&nbsp;";
                  }
                  if ($avertisseur == 1 && isset($deroule[$i]) && $deroule[$i] == 1 && $id_seq == $seq) {
                    $fichier_pdf .=  "<TD align='left' nowrap><B>&nbsp;&nbsp;$seq_suivi&nbsp;&nbsp;</B></TD>";
                    $avertisseur=0;
                  }elseif ($avertisseur == 1 && $deroule[$i] == 0) {
                    $fichier_pdf .=  "<TD align='left' nowrap>&nbsp;&nbsp;$seq_suivi&nbsp;&nbsp;</TD>";
                    $avertisseur=0;
                  }elseif (isset($deroule[$i]) && $deroule[$i] == 1 && $id_seq == $seq)
                   $fichier_pdf .=  "<TD align='left' nowrap><B>&nbsp;&nbsp;$seq_suivi&nbsp;&nbsp;</B></TD>";
                  else
                   $fichier_pdf .=  "<TD align='left' nowrap>&nbsp;&nbsp;$seq_suivi&nbsp;&nbsp;</TD>";
              if ($utilisateur > 0)
              {
                 $prescripteur = GetDataField ($connect,"select presc_prescripteur_no from prescription$Ext where
                                                         presc_seq_no = $seq and
                                                         presc_utilisateur_no = $utilisateur AND
                                                         presc_grp_no = $numero_groupe","presc_prescripteur_no");
                 $formateur = GetDataField ($connect,"select presc_formateur_no from prescription$Ext where
                                                      presc_seq_no = $seq and
                                                      presc_utilisateur_no = $utilisateur AND
                                                      presc_grp_no = $numero_groupe","presc_formateur_no");
                 $sans_prereq = mysql_query ("select prereq_seq_no from prerequis where prereq_seqcondition_no = $seq");
                 $nb_sans_prereq = mysql_num_rows ($sans_prereq);
                 $signal=0;
                 if ($nb_sans_prereq > 0){
                    $sequence = mysql_result($sans_prereq,0,"prereq_seq_no");
                    $seq_cible = mysql_query ("select suiv2_seq_no from suivi2$Ext where
                                               suiv2_utilisateur_no = $utilisateur AND
                                               suiv2_grp_no = $numero_groupe");
                    $nb_seq_cible = mysql_num_rows($seq_cible);
                    $nsc=0;
                    while ($nsc < $nb_seq_cible){
                      $sequence_cible = mysql_result($seq_cible,$nsc,"suiv2_seq_no");
                      if ($sequence == $sequence_cible){
                        $signal++;
                      }
                    $nsc++;
                    }
                 }
              }
       $fichier_pdf .=  "</TR>";

  if (((isset($deroule[$i]) && $deroule[$i] == 1 && $id_seq == $seq) || $tout == 1) || (isset($vp) && $vp == 1 && $seq == $seq_ouverte)){
    $ouvrir = '';
    $seq_ouverte = $seq;
    if ($typ_user == 'APPRENANT'){
      if ($scormOk == 1)
         $act_query = mysql_query ("select * from scorm_module,scorm_util_module$Ext WHERE
                                    scorm_module.mod_seq_no = $seq AND
                                    mod_module_no = scorm_module.mod_cdn AND
                                    user_module_no = $id_user AND
                                    mod_grp_no = $numero_groupe
                                    order by scorm_module.mod_cdn");
      else
         $act_query = mysql_query ("select * from activite,suivi1$Ext WHERE
                                    (activite.act_seq_no = $seq OR suivi_seqajout_no = $seq) AND
                                    suivi1$Ext.suivi_act_no = activite.act_cdn AND
                                    suivi1$Ext.suivi_utilisateur_no = $id_user AND
                                    suivi1$Ext.suivi_grp_no = $numero_groupe
                                    order by suivi1$Ext.suivi_seqajout_no,activite.act_ordre_nb");
    }else{
      if ($scormOk == 1)
         $act_query = mysql_query ("select * from scorm_module,scorm_util_module$Ext WHERE
                                    mod_seq_no = $seq AND
                                    mod_module_no = scorm_module.mod_cdn AND
                                    user_module_no = $utilisateur AND
                                    mod_grp_no = $numero_groupe
                                    order by mod_cdn");
      else
         $act_query = mysql_query ("select * from activite,suivi1$Ext WHERE
                                    (activite.act_seq_no = $seq OR suivi_seqajout_no = $seq) AND
                                    suivi1$Ext.suivi_act_no = activite.act_cdn AND
                                    suivi1$Ext.suivi_utilisateur_no = $utilisateur AND
                                    suivi1$Ext.suivi_grp_no = $numero_groupe
                                    order by suivi1$Ext.suivi_seqajout_no,activite.act_ordre_nb");
    }
      $Nb_act_seq = mysql_num_rows ($act_query);
    if ($Nb_act_seq == 0)
        $fichier_pdf .=  "<TR><TD align='left'><B>$msq_noact.</B><br><br></TD></TR></table></TD></TR></table>";
    else {
       if ($utilisateur > 0){
         $id_nom = GetDataField ($connect,"select util_nom_lb  from utilisateur where util_cdn = $utilisateur","util_nom_lb");
         $id_prenom = GetDataField ($connect,"select util_prenom_lb  from utilisateur where util_cdn = $utilisateur","util_prenom_lb");
         $id_app = $utilisateur;
       }else{
         $id_app = $id_user;}
         $suivi_etat_seq = GetDataField ($connect,"select suiv2_etat_lb from suivi2$Ext where
                                                   suiv2_seq_no = '$seq' and
                                                   suiv2_utilisateur_no = $id_app AND
                                                   suiv2_grp_no = $numero_groupe","suiv2_etat_lb");
         $titre_sequence = GetDataField ($connect,"select seq_titre_lb from sequence where seq_cdn = '$seq'","seq_titre_lb");
         $nom_sequence = GetDataField ($connect,"select seq_desc_cmt from sequence where seq_cdn = '$seq'","seq_desc_cmt");
         if ($nom_sequence == '')
           $nom_sequence = $msq_no_decrite;
           $fichier_pdf .=  "<TR><TD align='left' width='1%' colspan='1'>&nbsp;</TD><TD colspan='6' align='left'>";
           $fichier_pdf .=  "<TABLE border='1' width='98%' cellspacing='1' cellpadding='3'>";
           $fichier_pdf .=  "<TR height='30'>";
           $fichier_pdf .=  "<TD align='left' width='20%'><b>$msq_acts </b></TD>";
           $fichier_pdf .=  "<TD align='left' width='40%'><b>$msq_aff_cons </b></TD>";
           $fichier_pdf .=  "<TD align='left' width='4%'><b>$msq_duree_seq </b></TD>";
           $fichier_pdf .=  "<TD align='left' width='5%'><b>$msq_etat </b></TD>";
           $fichier_pdf .=  "<TD align='left' width='5%'><b>$msq_tit_label</b></TD>";
           $fichier_pdf .=  "<TD align='left' width='5%'><b>Nombre d'accès</b></TD>";
           $fichier_pdf .=  "<TD align='left' width='5%'><b>Temps consacré</b></TD>";
           $fichier_pdf .=  "<TD align='left' width='5%'><b>Dates d'accès</b></TD>";
           $fichier_pdf .=  "</TR>";
             //On selectionne le type d'utilisateur (uniquement si ce n'est pas un apprenant ki vient consulter ses activites)car seul l'administrateur et l'auteur de l'activite ont le droit de modifier ou supprimer celle-ci
       if ($scormOk == 0){
          $ii = 0;
          while ($ii != $Nb_act_seq) {
               $id_suivi = mysql_result ($act_query,$ii,"suivi_cdn");
               $id = mysql_result ($act_query,$ii,"act_cdn");
               $nom = mysql_result ($act_query,$ii,"act_nom_lb");
               $nom = strip_tags($nom);
               $ordre[$ii] = mysql_result ($act_query,$ii,"act_ordre_nb");
               $consigne = mysql_result ($act_query,$ii,"act_consigne_cmt");
               $pass_mult = mysql_result ($act_query,$ii,"act_passagemult_on");
               $acquit = mysql_result ($act_query,$ii,"act_acquittement_lb");
               $devoir = mysql_result ($act_query,$ii,"act_devoirarendre_on");
               $notation = mysql_result ($act_query,$ii,"act_notation_on");
               $flag = mysql_result ($act_query,$ii,"act_flag_on");
               if (!$consigne)
                 $consigne = $msq_aucune;
               //Séléction ressource
               $id_ress = mysql_result ($act_query,$ii,"act_ress_no");
               //Pour meme raison que typ_user, on selectionne auteur
               $id_auteur = GetDataField ($connect,"select seq_auteur_no from sequence where seq_cdn = $seq","seq_auteur_no");
               if ($utilisateur > 0)
                 $id_formateur = GetDataField ($connect,"select presc_formateur_no from prescription$Ext where
                                                         presc_seq_no = $seq and
                                                         presc_utilisateur_no=$utilisateur AND
                                                         presc_grp_no = $numero_groupe","presc_formateur_no");

               //Si c'est l'apprenant qui consulte les seq et activites a faire
               //On doit savoir s'il doit effectuer activites ds l'ordre ou non pour activer ou non le lien
               if ($apprenant == 1) {
                   $id_apprenant = $id_user;
                   $ordre_act = GetDataField ($connect,"select seq_ordreact_on from sequence where seq_cdn = $seq","seq_ordreact_on");
                  //On connait l'ordre de l'activite courante  ($ordre)
                   if ($ordre[$ii] > 1 && $ii > 0) {
                       //On recupere l'etat l'activite precedente
                       $ordre_prec = $ordre[$ii-1];
                       $act_prec =   GetDataField ($connect,"select act_cdn from activite where act_ordre_nb = $ordre_prec and act_seq_no = $seq","act_cdn");
                       $etat_act = GetDataField ($connect,"select suivi_etat_lb from suivi1$Ext where
                                                           suivi_act_no = $act_prec and
                                                           suivi_utilisateur_no = $id_apprenant and
                                                           suivi_grp_no = $numero_groupe","suivi_etat_lb");
                    }
               } //fin if ($apprenant == 1)
               $duree = mysql_result ($act_query,$ii,"act_duree_nb");
               
               //On passe certaines infos par URL, cela evite de refaire une requete
               //on affiche le lien pour un apprenant que si activite precedente terminee
               // GESTION DE L'ORDRE ET DE L'ETAT DES ACTIVITES POUR UN APPRENANT OU SON FORMATEUR
               $fichier_pdf .=  "<TR>";
               //dey Dfoad
               $req_typdev = mysql_result(mysql_query("select count(*) from activite_devoir where actdev_act_no = $id"),0);
               $dev_act = "";
               if ($req_typdev > 0)
                 $dev_act = GetDataField ($connect,"select actdev_dev_lb from activite_devoir where ".
                                          "actdev_act_no = $id ","actdev_dev_lb");
               $xapi = ($dev_act == 'xApi TinCan') ? "xApi-" : '';
               if ($etat_act == 'TERMINE' || $ordre[$ii] != 0 || $typ_user != "NULL" || $ordre_act == "NON"){
                  $fichier_pdf .=  "<TD align='left' valign='top'>";
                   $lien= $lien_retour;
                   $nom_act= str_replace("'","\'",$nom);
                   if ($pass_mult == "OUI")
                     $aff_pass_mult = $mess_oui;
                   elseif($pass_mult == "NON")
                     $aff_pass_mult = $mess_non;
                   if ($acquit == "FORMATEUR_REFERENT")
                     $aff_acquit = strtoupper($msq_formateur);
                   elseif ($acquit == "APPRENANT")
                     $aff_acquit = $mess_typ_app;
                   elseif ($acquit == "RESSOURCE")
                     $aff_acquit = strtoupper($msq_ress);
                   elseif ($acquit == "AUTO")
                     $aff_acquit = strtoupper($msq_auto);
                   if ($devoir == "OUI")
                     $aff_devoir = $mess_oui;
                   elseif($devoir == "NON")
                     $aff_devoir = $mess_non;
                   if ($notation == "OUI")
                     $aff_notation = $mess_oui;
                   elseif($notation == "NON")
                     $aff_notation = $mess_non;
                   $fichier_pdf .=  "<DIV id='parcours'>$xapi ".str_replace('-',' ',$nom)."</DIV></TD>";
               }else
                  $fichier_pdf .=  "<TD align='left' valign='top'>$xapi ".str_replace('-',' ',$nom)."</TD>";
               $aff_consigne = strip_tags(html_entity_decode(html_entity_decode($consigne,ENT_QUOTES,'ISO-8859-1'),ENT_QUOTES,'ISO-8859-1'));
               $fichier_pdf .=  "<TD align='left' valign='top'><small>".str_replace('-',' ',$aff_consigne)."</small></TD>";
               $duree = duree_calc($duree);
               $fichier_pdf .=  "<TD align='left' valign='top' nowrap>$duree</TD>";
                         if ($utilisateur > 0)
                           $actform = GetDataField ($connect,"select presc_formateur_no from prescription$Ext where
                                                              presc_seq_no = $seq and
                                                              presc_utilisateur_no = $utilisateur and
                                                              presc_grp_no = $numero_groupe","presc_formateur_no");
                         else
                           $actform = GetDataField ($connect,"select presc_formateur_no from prescription$Ext where
                                                              presc_seq_no = $seq and
                                                              presc_utilisateur_no = $id_user and
                                                              presc_grp_no = $numero_groupe","presc_formateur_no");
                        $ordre_act = GetDataField ($connect,"select seq_ordreact_on from sequence where seq_cdn = $seq","seq_ordreact_on");
                        if ($utilisateur > 0)
                           $etat_act = GetDataField ($connect,"select suivi_etat_lb from suivi1$Ext where
                                                               suivi_act_no = $id and
                                                               suivi_utilisateur_no = $utilisateur and
                                                               suivi_grp_no = $numero_groupe","suivi_etat_lb");
                        else
                        {
                           $etat_act = GetDataField ($connect,"select suivi_etat_lb from suivi1$Ext where suivi_act_no = $id and  suivi_utilisateur_no = $id_user","suivi_etat_lb");
                           $email_user = GetDataField ($connect, "select util_email_lb from utilisateur where util_cdn=$id_user","util_email_lb");
                           $nom_user=GetDataField ($connect,"select util_nom_lb from utilisateur where util_cdn='$id_user'","util_nom_lb");
                           $prenom_user=GetDataField ($connect,"select util_prenom_lb from utilisateur where util_cdn='$id_user'","util_prenom_lb");
                        }
                        if ($etat_act == "TERMINE")
                           $fin_ordre_non++;
                        if ((($etat_act == "TERMINE" && $notation == "NON") || $etat_act == "A FAIRE" || $etat_act == "EN COURS") && $ordre[$ii] >0  && ($typ_user == "NULL"))
                        {
                           $marqueur[$ii] = 1;
                           $iii = $ii-1;
                           if ($etat_act != "TERMINE" && $ordre_act == "OUI")
                              $pointeur++;
                           if ($ii == 0 && !$utilisateur)
                             $pointer = 1;
                           if ($Nb_act_seq == $ii+1 && $ordre_act == "OUI")
                             $fin_seq=1;
                           if ($Nb_act_seq == $ii+1 && $ordre_act == "NON" && $fin_ordre_non == $Nb_act_seq)
                             $fin_seq = 1;
                        }
                 if ($utilisateur > 0 || $typ_user =="NULL"){
                   if ($utilisateur > 0){
                      $actform = GetDataField ($connect,"select presc_formateur_no from prescription$Ext where
                                                         presc_seq_no = $seq and
                                                         presc_utilisateur_no = $utilisateur AND
                                                         presc_grp_no = $numero_groupe","presc_formateur_no");
                      $prescripteur = GetDataField ($connect,"select presc_prescripteur_no from prescription$Ext where
                                                              presc_seq_no = $seq and
                                                              presc_utilisateur_no = $utilisateur AND
                                                              presc_grp_no = $numero_groupe","presc_prescripteur_no");
                      $actsuivi = GetDataField ($connect,"select suivi_etat_lb from suivi1$Ext where
                                                          suivi_act_no = $id and
                                                          suivi_utilisateur_no = $utilisateur and
                                                          suivi_grp_no = $numero_groupe","suivi_etat_lb");
                      $suivi_fichier = GetDataField ($connect,"select suivi_fichier_lb from suivi1$Ext where suivi_act_no = $id and suivi_utilisateur_no = $utilisateur","suivi_fichier_lb");
                   }else{
                      $actform = GetDataField ($connect,"select presc_formateur_no from prescription$Ext where
                                                         presc_seq_no = $seq and
                                                         presc_utilisateur_no = $id_user AND
                                                         presc_grp_no = $numero_groupe","presc_formateur_no");
                      $prescripteur = GetDataField ($connect,"select presc_prescripteur_no from prescription$Ext where
                                                              presc_seq_no = $seq and
                                                              presc_utilisateur_no = $id_user AND
                                                              presc_grp_no = $numero_groupe","presc_prescripteur_no");
                      $actsuivi = GetDataField ($connect,"select suivi_etat_lb from suivi1$Ext where
                                                          suivi_act_no = $id and
                                                          suivi_utilisateur_no = $id_user and
                                                          suivi_grp_no = $numero_groupe","suivi_etat_lb");
                      $suivi_fichier = GetDataField ($connect,"select suivi_fichier_lb from suivi1$Ext where
                                                               suivi_act_no = $id and
                                                               suivi_utilisateur_no = $id_user and
                                                               suivi_grp_no = $numero_groupe","suivi_fichier_lb");
                   }
                   if ($suivi_fichier != "" && strstr($suivi_fichier,"qf_"))
                     $tip_fic = substr($suivi_fichier, 0, 3);
                   if ($actsuivi == "TERMINE")
                     $actsuiv = $mess_fait;
                   elseif ($actsuivi == "A FAIRE" && $flag == 1)
                     $actsuiv = $mess_lanc_afaire;
                   elseif ($actsuivi == "EN COURS")
                     $actsuiv = $mess_lanc_encours;
                   elseif ($actsuivi == "ATTENTE"){
                     $actsuiv = $mess_lanc_attente;//formateur sur etat et apprenant sur acq/eval
                     $actsuivalid = $mess_fait;//apprenant
                   }elseif ($actsuivi == "PRESENTIEL")
                     $actsuiv = $mess_lanc_attente;
                   elseif ($actsuivi == "A FAIRE" && $flag == 0)
                     $actsuiv = $mess_trvx;
                   $etat_activite = $actsuiv;
                   if ($actsuivi == 'TERMINE' && $utilisateur > 0 && ($id_user == $actform || $id_user == $prescripteur || $id_user == $inscripteur)){
                     $nom_user=GetDataField ($connect,"select util_nom_lb from utilisateur where util_cdn='$utilisateur'","util_nom_lb");
                     $prenom_user=GetDataField ($connect,"select util_prenom_lb from utilisateur where util_cdn='$utilisateur'","util_prenom_lb");
                     $lien = "";
                     $fichier_pdf .=  "<TD align='left' valign='top' nowrap>$etat_activite</TD>";
                   }else
                     $fichier_pdf .=  "<TD align='left' valign='top' nowrap>$etat_activite</TD>";
                   if ($utilisateur > 0)
                     $nom_app = GetDataField ($connect,"select util_nom_lb  from utilisateur where util_cdn = $utilisateur","util_nom_lb");
                   if ($utilisateur > 0)
                     $prenom_app = GetDataField ($connect,"select util_prenom_lb  from utilisateur where util_cdn = $utilisateur","util_prenom_lb");
                 }//fin if ($origine == "liste")
                 if ($ii == 0 && $utilisateur == "")
                    $pointer = 1;
                 if ($Nb_act_seq == $ii+1 && $ordre_act == "OUI")
                    $fin_seq=1;
                 if ($Nb_act_seq == $ii+1 && $ordre_act == "NON" && $fin_ordre_non == $Nb_act_seq)
                   $fin_seq = 1;
                 $actvalid = GetDataField ($connect,"select act_acquittement_lb from activite where act_seq_no = $seq and act_cdn = $id","act_acquittement_lb");
                 if ($utilisateur > 0)
                    $actnote = GetDataField ($connect,"select suivi_note_nb1 from suivi1$Ext where
                                                       suivi_act_no = $id and
                                                       suivi_utilisateur_no= $utilisateur and
                                                       suivi_grp_no = $numero_groupe","suivi_note_nb1");
                 $ress_type = GetDataField ($connect,"select ress_type from ressource_new where ress_cdn = $id_ress","ress_type");
                 if (($id_user == $actform  && $actsuivi == "PRESENTIEL" && ($actvalid == "FORMATEUR_REFERENT" || $actvalid == "APPRENANT")) || (($id_user == $actform)  && ($actsuivi == "ATTENTE" || ($actsuivi == "EN COURS" && $actvalid == "APPRENANT"))) || (($actvalid == "FORMATEUR_REFERENT" || $actvalid == "APPRENANT") && $apprenant == 1 && $actsuivi == "EN COURS") || ($pass_mult == "OUI" && ($actsuivi == "EN COURS") && $pointeur == 1)){
                    $lien="suivi.php?numero_groupe=$numero_groupe&hgrp=$hgrp&prov=vp&actform=$actform&utilisateur=$utilisateur&id_parc=$id_parc&id_act=$id&ress_type=$ress_type&id_seq=$seq&pointer=$pointer&validation=1&fin_seq=$fin_seq&sequ=$i&parc=$id_parc&visible=$visible&accord=$accord&prq=$prerequis&depasse=$depasse&proposable=$proposable";
                    if ($utilisateur > 0 &&  $actnote == "" && $notation == "OUI")
                       $lien .="&noter=1";
                   elseif (($utilisateur > 0 &&  $actnote != "" && $notation == "OUI") || ($utilisateur=="" && $actnote == "" && $notation == "OUI") || ($actsuivi == "PRESENTIEL" && $notation == "NON"))
                       $lien .="&envoyer=1";
                    $fichier_pdf .=  "<TD valign='top'><center><input type=checkbox  onMouseOver=\"overlib('$msq_cliq";
                    if ($utilisateur == ""){
                       if ($notation == "OUI" && $devoir == "OUI")
                          $fichier_pdf .= "<B> $msq_envoi_dev";
                       else
                          $fichier_pdf .=  "$msq_clore_act1 <FONT COLOR=#686840>$prenom_form  $nom_form</FONT> $msq_clore_act2 ";
                       $test_fin = $ii+1;
                       if ( $ordre_act == "OUI" && $test_fin < $Nb_act_seq)
                          $fichier_pdf .=  "$mess_lib_act";
                    }elseif ($notation == "OUI" && $devoir == "OUI" && $actnote != "")
                       $fichier_pdf .= " $msq_renvoi_dev <FONT COLOR=#686840> $prenom_app $nom_app</FONT><BR><B>$trvx_avert</B>";
                    elseif ($notation == "OUI" && $devoir == "OUI" && ($actnote == 0 || $actnote == ''))
                       $fichier_pdf .=  " $msq_don_note";
                    elseif ($notation == "OUI" && $devoir == "NON" && $actnote == '')
                        $fichier_pdf .=  " $msq_don_note1";
                    else
                       $fichier_pdf .=  " $msq_clore_act_app <FONT COLOR=#686840>$prenom_app $nom_app</FONT>";
                    $fichier_pdf .=  "',ol_hpos,LEFT,WIDTH,'300',CAPTION, '<center>$msq_tit_label</center>')\" onMouseOut=\"nd()\" onclick=\"window.location='trace.php?link=$lien';return true;\"></center></TD>";
                 }else{
                    if ($actsuivi == "TERMINE" && $utilisateur == ""){
                       $note = GetDataField ($connect,"select suivi_note_nb1 from suivi1$Ext
                                                       where suivi_act_no = $id and
                                                       suivi_utilisateur_no = $id_user and
                                                       suivi_grp_no = $numero_groupe","suivi_note_nb1");
                       if ($note == "Acquis")
                         $note = $mess_suivi_acq;
                       elseif ($note == "Non acquis")
                         $note = $mess_suivi_noacq;
                       $no_suivi = GetDataField ($connect,"select suivi_cdn from suivi1$Ext where
                                                           suivi_act_no = $id and
                                                           suivi_utilisateur_no = $id_user and
                                                           suivi_grp_no = $numero_groupe","suivi_cdn");
                       if ($note !="" && (($notation == "OUI" && $devoir == "OUI" && $actvalid == "FORMATEUR_REFERENT") || $tip_fic != "")){
                         $lien = "";
                         $fichier_pdf .=  "<TD align='center' nowrap valign='top'>";
                         $fichier_pdf .=  "$note</TD>";
                       }elseif ($note != "" && ($notation == "OUI" || ($notation == "NON" && $suivi_fichier != '')) && $devoir == "NON")
                         $fichier_pdf .=  "<TD align='center' valign='top' nowrap>$note</TD>";
                       else
                         $fichier_pdf .=  "<TD align='center' valign='top'>$msq_valide</TD>";
                    }elseif (($id_user == $actform || $id_user == $prescripteur || $id_user == $inscripteur || $typ_user == "ADMINISTRATEUR")  && $actsuivi == "TERMINE" && $utilisateur)
                    {
                       $note = GetDataField ($connect,"select suivi_note_nb1 from suivi1$Ext where
                                                       suivi_act_no = $id and
                                                       suivi_utilisateur_no = $utilisateur and
                                                       suivi_grp_no = $numero_groupe","suivi_note_nb1");
                       if ($note == "Acquis")
                         $note = $mess_suivi_acq;
                       elseif ($note == "Non acquis")
                         $note = $mess_suivi_noacq;
                       $no_suivi = GetDataField ($connect,"select suivi_cdn from suivi1$Ext where
                                                           suivi_act_no = $id and
                                                           suivi_utilisateur_no = $utilisateur and
                                                           suivi_grp_no = $numero_groupe","suivi_cdn");
                       if ($note != "" && (($notation == "OUI" && $devoir == "OUI" && $actvalid == "FORMATEUR_REFERENT") || $tip_fic != "")){
                         $lien = "";
                         $fichier_pdf .=  "<TD align='center' valign='top' nowrap>";
                         $fichier_pdf .= "$note</TD>";
                       }elseif ($note != "" && ($notation == "OUI" || ($notation == "NON" && $suivi_fichier != '')) && $devoir == "NON")
                         $fichier_pdf .=  "<TD align='center' valign='top' nowrap>$note</TD>";
                       else
                         $fichier_pdf .=  "<TD align='center' valign='top'>$msq_valide</TD>";
                    }elseif ($actvalid == "APPRENANT" && $apprenant == 1 && $actsuivi == "TERMINE" && $utilisateur == ""){
                       $fichier_pdf .=  "<TD align='center'>$msq_valide</TD>";
                    }else
                       $fichier_pdf .=  "<TD align='center'>&nbsp;</TD>";
                 }
                 $deroule[$i]=0;
                 $traquer = AffiDureeAct($id,$scormOk,$id_app,$numero_groupe);
                 $tabTrq = explode('|',$traquer);
                 $nbTrq = $tabTrq[0];
                 $dureeActivite = $tabTrq[1];
                 $dureeTotale += $dureeActivite;
                 $fichier_pdf .=  "<TD align='center' valign='top'>".$nbTrq."</TD>";
                 $fichier_pdf .=  "<TD align='center' valign='top'>".AfficheDureeAN($dureeActivite,$nbTrq)."</TD>".
                                  "<TD align='center' valign='top' nowrap>".$tabTrq[2]."</TD></TR>";
               $ii++;
               } // while ($ii != $Nb_act_seq) {
          }
          elseif ($scormOk == 1)
          {
              $ii = 0;
              $code = array();
              $id_act_seq = array();
              while ($ii != $Nb_act_seq) {
                  $affiche_scorm ='';
                  $id = mysql_result ($act_query,$ii,"mod_cdn");
                  $code[$id]=$ii;
                  $nom = mysql_result ($act_query,$ii,"mod_titre_lb");
                  $nom = strip_tags($nom);
                  $description = mysql_result ($act_query,$ii,"mod_desc_cmt");
                  $motclef = mysql_result ($act_query,$ii,"mod_motclef_lb");
                  $numero = mysql_result ($act_query,$ii,"mod_numero_lb");
                  $launch = mysql_result ($act_query,$ii,"mod_launch_lb");
                  $duree = mysql_result ($act_query,$ii,"mod_duree_nb");
                  $prerequis = mysql_result ($act_query,$ii,"mod_prereq_lb");
                  $score_max = mysql_result ($act_query,$ii,"mod_masteryscore");
                  $maxtime = mysql_result ($act_query,$ii,"mod_maxtimeallowed");
                  $status = mysql_result ($act_query,$ii,"lesson_status");
                  if ($status == "COMPLETED" || $status == "PASSED")
                     $status = $mess_fait;
                  elseif ($status == "NOT ATTEMPTED")
                     $status = $mess_lanc_afaire;
                  elseif ($status == "INCOMPLETE")
                     $status = $mess_lanc_encours;
                  elseif ($status == "FAILED")
                     $status = $mess_echec;//formateur sur etat et apprenant sur acq/eval
                   elseif ($status == "BROWSED" || $status == "UNKNOWN")
                     $status = $mess_vu;
                 if ($maxtime != '' && strlen($maxtime) > 9){
                    if (strstr($maxtime,'t') || strstr($maxtime,'T'))
                       $maxtime = substr($maxtime,13);
                    $liste_duree = explode(":",$maxtime);
                    $maxtime = ($liste_duree[0]*60)+$liste_duree[1];
                 }
                 if ($prerequis != ""){
                    $prereq1 = GetDataField ($connect,"select mod_cdn from scorm_module where mod_numero_lb = \"$prerequis\"","mod_cdn");
                    If (isset($code[$prereq1]))
                       $prereq = $code[$prereq1]+1;
                 }else
                    $prereq = '';
                 $affiche_scorm .= "<TR bgcolor= \"#F4F4F4\">";
                 $l = $ii+1;
                 if ($launch == '')
                 {
                    $affiche_scorm = "<TD bgcolor= '#eeeeee' valign='top' colspan='8' align='left'><B>$nom</B></TD>";
                 }
                 else
                 {
                     $affiche_scorm .= "<TD valign='top'>$nom</TD>";
                     if (strlen(strip_tags(NewHtmlEntityDecode($description))) < 30)
                        $aff_description = $description;
                     else
                         $aff_description = substr(strip_tags(NewHtmlEntityDecode($description)),0,20)."....etc...";
                     $affiche_scorm .= "<TD valign='top'>$aff_description";
                     if ($motclef !="")
                        $affiche_scorm .= " ===> $mess_motclef = ".strip_tags(NewHtmlEntityDecode($motclef))."</TD>";
                     else
                         $affiche_scorm .="</TD>";
                     if ($duree != 0)
                        $affiche_scorm .= "<TD valign='top'>$duree'</TD>";
                     else
                         $affiche_scorm .= "<TD valign='top'>&nbsp;</TD>";
                     $affiche_scorm .= "<TD valign='top'>$mess_seq_sco</TD>";
                     $affiche_scorm .= "<TD valign='top' nowrap>$status</TD>";
                     $traquer = AffiDureeAct($id,$scormOk,$id_app,$numero_groupe);
                     $tabTrq = explode('|',$traquer);
                     $nbTrq = $tabTrq[0];
                     $dureeActivite = $tabTrq[1];
                     $dureeTotale += $dureeActivite;
                     $affiche_scorm .=  "<TD align='center' valign='top'>".$nbTrq."</TD>";
                     $affiche_scorm .=  "<TD align='center' valign='top'>".AfficheDureeAN($dureeActivite,$nbTrq)."</TD>";
                     $affiche_scorm .=  "<TD valign='top' nowrap>".$tabTrq[2]."</TD>";
                 }
                 $fichier_pdf .= "$affiche_scorm</TR>";
               $ii++;
              }
          }
          $reqWk=mysql_query("select * from wikiapp,wiki where wkapp_app_no='$id_app' and
                                   wkapp_seq_no='$seq' and wkapp_parc_no='$id_parc' and
                                   wkapp_grp_no='$numero_groupe' and wkapp_wiki_no = wiki_cdn");
               $nbWk = mysql_num_rows($reqWk);
               if ($nbWk == 1)
               {
                  $oWk = mysql_fetch_object($reqWk);
                  $reqClan = mysql_query("select * from wikiapp where wkapp_clan_nb='".$oWk->wkapp_clan_nb."' and
                                        wkapp_seq_no='$seq' and wkapp_parc_no='$id_parc' and
                                        wkapp_grp_no='$numero_groupe'");
                  $NbBodyWk = mysql_num_rows(mysql_query("select wkbody_cdn from wikibodies where wkbody_auteur_no ='".
                                                         $id_app."' and wkbody_clan_no ='".$oWk->wkapp_clan_nb."'"));
                  $NbMetaWk = mysql_num_rows(mysql_query("select wkmeta_cdn from wikimeta where wkmeta_auteur_no ='".
                                                        $id_app."' and wkmeta_clan_no ='".$oWk->wkapp_clan_nb."'"));
                  $NoteWk = GetDataField ($connect,"select wknote_note_lb from wikinote where wknote_app_no ='".$oWk->wkapp_cdn."'","wknote_note_lb");
                  if ($NoteWk != 'NULL')
                     $_SESSION['NoteWk'] = $NoteWk;
                  else
                     $_SESSION['NoteWk'] = '';
                  $nbClan = mysql_num_rows($reqClan);
                  if ($nbClan > 1)
                  {
                     $listeClan = 'à faire avec ';
                     while ($oClan = mysql_fetch_object($reqClan))
                     {
                         if ($oClan->wkapp_app_no != $id_app)
                         {

                               $nomApp = GetDataNP ($connect,"select util_nom_lb,util_prenom_lb from utilisateur where util_cdn = '".
                                                   $oClan->wkapp_app_no."'","util_nom_lb,util_prenom_lb");
                               $listeClan .= $nomApp.',';
                         }

                     }
                  }
                  else
                     $listeClan .= 'A faire seul';
                  $cols = ($scormOk == 1)? 6:2 ; //'<td bgcolor="#efefef"></td>'.
                  $fichier_pdf .= '<tr style="background-color:#D4E7ED;">'.
                                  '<td valign="top">Document en ligne</div></td>';
                  if ($utilisateur > 0)
                      $fichier_pdf .= '<td valign="top">Groupe : '.$listeClan.'</a></td>';
                  $fichier_pdf .= '<td colspan="'.$cols.'" valign="top">'.$oWk->wiki_consigne_cmt.'</td>';
                  if (!empty($_SESSION['NoteWk']))
                     $fichier_pdf .= "<td valign='top'>Fait</td>";
                  elseif ($NbBodyWk > 0 || $NbMetaWk > 0)
                     $fichier_pdf .= "<td valign='top' nowrap>En cours</td>";
                  elseif ($NbBodyWk == 0 && $NbMetaWk == 0)
                     $fichier_pdf .= "<td valign='top' nowrap>A faire</td>";
                  if (!empty($_SESSION['NoteWk']))
                  {
                     $fichier_pdf .= "<td valign='top' align='center'>".$_SESSION['NoteWk']."</td>";
                  }
                  else
                      $fichier_pdf .= '<td valign="top" align="center">&nbsp;</td>';
                  if ($utilisateur > 0)
                     unset($_SESSION['NoteWk']);
                  $fichier_pdf .= '</tr>';
               }
               //fin wiki
               //mindmap
               $reqMM=mysql_query("select * from mindmapapp,mindmap where mmapp_app_no='$id_app' and
                                   mmapp_seq_no='$seq' and mmapp_parc_no='$id_parc' and
                                   mmapp_grp_no='$numero_groupe' and mmapp_mindmap_no = mindmap_cdn");
               $nbMM = mysql_num_rows($reqMM);
               if ($nbMM == 1)
               {
                  $oMM = mysql_fetch_object($reqMM);
                  $reqClanMM = mysql_query("select * from mindmapapp where mmapp_clan_nb='".$oMM->mmapp_clan_nb."' and
                                        mmapp_seq_no='$seq' and mmapp_parc_no='$id_parc' and
                                        mmapp_grp_no='$numero_groupe'");
                  $NoteMM = GetDataField ($connect,"select mmnote_note_lb from mindmapnote where mmnote_app_no ='".$oMM->mmapp_cdn."'","mmnote_note_lb");
                  if ($NoteMM != 'NULL')
                     $_SESSION['NoteMM'] = $NoteMM;
                  else
                     $_SESSION['NoteMM'] = '';
                  $nbClanMM = mysql_num_rows($reqClanMM);
                  if ($nbClanMM > 1)
                  {
                     $listeClanMM = 'Carte heuristique avec ';
                     while ($oClanMM = mysql_fetch_object($reqClanMM))
                     {
                         if ($oClanMM->mmapp_app_no != $id_app)
                         {

                               $nomApp = GetDataNP ($connect,"select util_nom_lb,util_prenom_lb from utilisateur where util_cdn = '".
                                                   $oClanMM->mmapp_app_no."'","util_nom_lb,util_prenom_lb");
                               $listeClanMM .= ' <strong>'.$nomApp.'</strong>,';
                         }

                     }
                  }
                  else
                     $listeClanMM .= 'Carte heuristique : pour l\'instant seul';

                  $cols = ($scormOk == 1) ? 1 : 2 ;
                  $fichier_pdf .= '<tr><td valign="top"><div title="'.$listeClanMM.'">MindMap</div>';
                  $fichier_pdf .= '</td><td colspan="'.$cols.'"><div style="text-align:left;">';
                  $fichier_pdf .= NewHtmlEntityDecode($oMM->mindmap_intro_cmt,ENT_QUOTES).'</div></td>';
                  $nbMM = mysql_num_rows(mysql_query("select * from mindmaphistory where
                                        mindhisto_auteur_no = $id_app and mindmap_clan_no = ".$oMM->mmapp_clan_nb));
                  if (!empty($_SESSION['NoteMM']))
                     $fichier_pdf .= "<td valign='top'>Fait</td>";
                  elseif ($NbMM == 0)
                     $fichier_pdf .= "<td valign='top' nowrap>A faire</td>";
                  if (!empty($_SESSION['NoteMM']) && empty($utilisateur))
                  {
                     $fichier_pdf .= "<td valign='top' align='center'><strong>".$_SESSION['NoteMM']."</strong></td>";
                  }
                  elseif (empty($_SESSION['NoteMM']) && $id_app > 0  && $nbMM == 0)
                  {
                      $fichier_pdf .= '<td valign="top" align="center">&nbsp;</td>';
                  }
                  if ($utilisateur > 0 && $id_formateur == $id_user)
                      $fichier_pdf .= '<td valign="top" align="center">&nbsp;</td>';
                      $fichier_pdf .= '<td colspan="2">&nbsp;</td>';
                  $fichier_pdf .= '</tr>';
               }
               //fin mindmap
              //$fichier_pdf .=  "<TR height='10'><TD colspan='7'>&nbsp;</TD></TR>";
            } //fin else ($Nb__seq ...)
          $fichier_pdf .=  "</TABLE></TD></TR>";
         } // fin if (((isset($deroule[$i]) && $deroule[$i] == 1 && $id_seq == $seq) || $tout == 1) || ($vp == 1 && $seq == $seq_ouverte)){
         $i++;
         $pointeur = 0;
         $visible = 0;
       }//fin while ($i != $nb_seq)
       //$fichier_pdf .= "<TR height='10'><TD colspan='5'>&nbsp;</TD></TR>";


       $fichier_pdf .=  "</TABLE></TD></TR>";
     }// fin if deroule[$i]_parc
     $p++;
  }// fin while ($p < $nb_parc)
 if ($tout == 1)
    $fichier_pdf .=  "<TR><TD width='10%' valign='top'>&nbsp;</TD><td><center><TABLE border=1 cellspacing=4 cellpadding=5>".
                     "<TR><TD nowrap colspan=2 align=center>".
                     "Durée totale calculée ou estimée en l'absence de tracking : ".AfficheDureeAN($dureeTotale,0).
                     "</span></td></TR></TABLE></td></TR></TABLE></TD><TD width='10%' valign='top'>&nbsp;</TD></TR>".
                     "</TABLE></TD></TR></TABLE>";
    //echo $fichier_pdf; exit;
    $nom_user = modif_nom($nom_user);
    $nom_grp1 = modif_nom($nom_grp1);
    $entree_html = "$nom_grp1"."_$nom_user.html";
    $sortie_pdf = "$nom_grp1"."_$nom_user.pdf";
    $dir_app_html="ressources/".$login."_".$id_user."/ressources/".$entree_html;
    $dir_app_pdf="ressources/".$login."_".$id_user."/ressources/".$sortie_pdf;
    $fp = fopen($dir_app_html, "w+");
    $fw = fwrite($fp, $fichier_pdf);
    fclose($fp);
    $type_lang = "--charset $charset";
    $orientation = "--landscape";
    $lier = "--no-links";
    $bodyfont = "--bodyfont $police";
    $textfont = "--textfont $police";
    $margehaut = "--top 5mm";
    if ($inscripteur == $id_user || $typ_user == "ADMINISTRATEUR")
       $margebas = "--bottom 20mm";
    else
       $margebas = "--bottom 2mm";
    $margegauche = "--left 5mm";
    $margedroite = "--right 5mm";
//    $fontsigne = "--headfootfont Helvetica-Bold";
    $permissions = "permissions no-modify";
    $titrer = "--no-title";
    if ($inscripteur == $id_user || $typ_user == "ADMINISTRATEUR")
       $pied = "--footer ct/";
    else
       $pied = "--footer ../";
    passthru ("/usr/bin/htmldoc $orientation $type_lang $lier $bodyfont $textfont $margehaut $margebas $margegauche $margedroite $permissions $pied $titrer --header ... --pagelayout twoleft --quiet -t pdf --webpage $dir_app_html > $dir_app_pdf");
    $lien = "$dir_app_pdf";
    //$lien = urlencode($lien);

    echo "<script language=\"JavaScript\">";
       echo "document.location.replace(\"$lien\")";
    echo "</script>";

    unlink($dir_app_html);
/*    ForceFileDownload($lien,'binary');*/
    exit;
}
?>
