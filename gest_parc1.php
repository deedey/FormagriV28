<?php
session_start();
include ("include/UrlParam2PhpVar.inc.php");
if (!isset($_SESSION['id_user']) | $_SESSION['id_user'] == "")
{
  exit();
}
error_reporting (E_ALL);
require 'admin.inc.php';
require 'fonction.inc.php';
require 'fonction_html.inc.php';
require "lang$lg.inc.php";
require "class/class_module.php";
dbConnect();
$Ext="_$numero_groupe";
if ($lg == "ru"){
  $code_langage = "ru";
  $charset = "Windows-1251";
  putenv("TZ=Europe/Moscow");
}elseif ($lg == "fr"){
  $code_langage = "fr";
  $charset = "iso-8859-1";
  putenv("TZ=Europe/Paris");
}elseif ($lg == "en"){
  $code_langage = "en";
  $charset = "iso-8859-1";
}
include ("include/varGlobals.inc.php");
$currentUser = nomUser($_SESSION['id_user']);
switch ($_SESSION['typ_user'])
{
   case 'ADMINISTRATEUR' : $RoleUser = "Adm";break;
   case 'APPRENANT' : $RoleUser = "App";break;
   case 'RESPONSABLE_FORMATION' : $RoleUser = "RF";break;
   case 'FORMATEUR_REFERENT' : $RoleUser = "FR";break;
   case 'TUTEUR' : $RoleUser = "Tut";break;
}
?>
<HTML>
<HEAD>
<META HTTP-EQUIV="Content-Type" content="text/html; charset=<?php  echo $charset;?>">
<META HTTP-EQUIV="Content-Language" CONTENT="<?php  echo $code_langage ;?>">
<META HTTP-EQUIV="Expires" CONTENT="Fri, Jan 01 1900 00:00:00 GMT">
<META NAME="ROBOTS" CONTENT="No Follow">
<META HTTP-EQUIV="Cache-Control" CONTENT="no-cache, must-revalidate">
<META HTTP-EQUIV="Pragma" CONTENT="no-cache">
<link rel="stylesheet" type="text/css" href="general.css" />
<link rel="stylesheet" type="text/css" href="admin/style_admin.css" />
<link rel="stylesheet" type="text/css" href="OutilsJs/style_jquery.css" />
<script Language="Javascript" type="text/javascript" src="OutilsJs/jquery-144.js"></script>
<script Language="Javascript" type="text/javascript" src="OutilsJs/lib/ajax/ajax_cms.js"></script>
<SCRIPT  LANGUAGE="JavaScript1.2" SRC="calendrier_<?php echo $lg;?>.js"></SCRIPT>
<script type="text/javascript" src="<?php echo $monURI; ?>/OutilsJs/jquery.tooltip.pack.js"></script><SCRIPT Language="Javascript">
$(document).ready(function(){
                   $("a").tooltip({showURL: false});
                   $("div").tooltip({showURL: false});
                   $("span").tooltip({showURL: false});
                   $("img").tooltip({showURL: false});
                   $("li").tooltip({showURL: false});
                   $("input").tooltip({showURL: false});
                   setTimeout(function() {$("#mien").empty();},7000);
});
function fsub() {
        document.MForm.submit();
}

function TryCallFunction() {
        var sd = document.MForm.mydate1.value.split("\/");
        document.MForm.iday.value = sd[1];
        document.MForm.imonth.value = sd[0];
        document.MForm.iyear.value = sd[2];
}

function Today() {
        var dd = new Date();
        return((dd.getMonth()+1) + "/" + dd.getDate() + "/" + dd.getFullYear());
}
</SCRIPT>
<SCRIPT LANGUAGE="JavaScript">
function popup(f,nom, w, h) {
   window.open(f, nom, 'resizable,screenX=0,screenY=0,scrollbars=yes,menubar=yes,width=' + w + ',height=' + h);
}

function tinypopup(f,nom, w, h) {
   window.open(f, nom, 'resizable,screenX=0,screenY=0,scrollbars=no,menubar=no,width=' + w + ',height=' + h);
}
</script>
<SCRIPT LANGUAGE="JavaScript">
function appel_w(sel_val) {
var fset=sel_val.substring(0,2);
var f2=sel_val;
var url1 = ""+f2+"";
if ( fset == "tr" ) parent.main.location=url1
}
</script>
<script language="javascript" src="functions.js"></script>
<script type="text/javascript">
   var _gaq=_gaq||[];var pluginUrl = '//www.google-analytics.com/plugins/ga/inpage_linkid.js';_gaq.push(['_require', 'inpage_linkid', pluginUrl]);_gaq.push(['_setAccount','UA-42020054-1']);_gaq.push(['_trackPageview']);_gaq.push(['_setCustomVar', 1, window.location.host, window.location.host, 1]);_gaq.push(['_setDomainName','none']);(function(){var ga=document.createElement('script');ga.type='text/javascript';ga.async=true;ga.src=('https:'==document.location.protocol?'https://ssl':'http://www')+'.google-analytics.com/ga.js';var s=document.getElementsByTagName('script')[0];s.parentNode.insertBefore(ga,s)})();
</script>
<TITLE>Formagri :: <?php echo str_replace('.educagri.fr','',str_replace('cfppa-','',str_replace('ef-','',$_SERVER['SERVER_NAME']))).
                         ' :: '.$RoleUser.' :: '.$currentUser;?></TITLE>
</HEAD>
<?php
$Ext = '_'.$numero_groupe;
$image = GetDataField($connect,"select param_etat_lb from param_foad where param_cdn=1","param_etat_lb");
?>
<BODY marginwidth="0" marginheight="0" leftmargin="0" topmargin="0">
  <div id="overDiv" style="position:absolute; visibility:hidden;z-index:1000;"></div>
  <SCRIPT LANGUAGE="JavaScript" SRC="overlib.js"><!-- overLIB (c) Erik Bosrup --></SCRIPT>
  <?php
  $id_classe = GetDataField($connect,"select grp_classe_on from groupe where grp_cdn = $numero_groupe","grp_classe_on");
  $order_appear = ($id_classe == 0) ? "prescription$Ext.presc_ordre_no" : "prescription$Ext.presc_ordre_no,prescription$Ext.presc_cdn";
    if (isset($utilisateur) && $utilisateur > 0 &&  $typ_user == "TUTEUR")
    {
      $parc_query = mysql_query ("SELECT suiv3_parc_no from  suivi3$Ext,prescription$Ext WHERE prescription$Ext.presc_parc_no = suiv3_parc_no and prescription$Ext.presc_utilisateur_no=$utilisateur AND prescription$Ext.presc_grp_no=$numero_groupe group by suiv3_parc_no order by prescription$Ext.presc_ordre_no,prescription$Ext.presc_cdn");
      $num_app = $utilisateur;
    }
    elseif (isset($utilisateur) && $utilisateur > 0 && ($typ_user == "ADMINISTRATEUR" || $typ_user == "FORMATEUR_REFERENT" || $typ_user == "RESPONSABLE_FORMATION" ))
    {
      $parc_query = mysql_query ("SELECT suiv3_parc_no from  suivi3$Ext,prescription$Ext WHERE
                                  prescription$Ext.presc_parc_no = suiv3_parc_no and
                                  prescription$Ext.presc_utilisateur_no=$utilisateur and
                                  prescription$Ext.presc_grp_no=$numero_groupe
                                  group by suiv3_parc_no
                                  order by $order_appear");
      $num_app = $utilisateur;
    }
    elseif(!isset($utilisateur) || (isset($utilisateur) && $utilisateur == 0))
    {
      if ($numero_groupe > 0)
      {
        $parc_query = mysql_query ("SELECT suiv3_parc_no from  suivi3$Ext,prescription$Ext WHERE
                                    prescription$Ext.presc_parc_no = suiv3_parc_no and
                                    prescription$Ext.presc_utilisateur_no = $id_user and
                                    prescription$Ext.presc_grp_no=$numero_groupe
                                    group by suiv3_parc_no
                                    order by $order_appear");
      }
      else
      {
        $parc_query = mysql_query ("SELECT suiv3_parc_no from  suivi3$Ext,prescription$Ext WHERE
                                    prescription$Ext.presc_parc_no = suiv3_parc_no and
                                    prescription$Ext.presc_utilisateur_no = $id_user and
                                    prescription$Ext.presc_grp_no = $numero_groupe
                                    group by suiv3_parc_no
                                    order by $order_appear");
      }
      $num_app = $id_user;
    }
    $nom_util=GetDataField ($connect,"SELECT util_nom_lb from utilisateur WHERE util_cdn='$num_app'","util_nom_lb");
    $prenom_util=GetDataField ($connect,"SELECT util_prenom_lb from utilisateur WHERE util_cdn='$num_app'","util_prenom_lb");
    $nb_parc = mysql_num_rows ($parc_query);
    if ($nb_parc == 0 && $typ_user == "APPRENANT")
       exit();
    elseif ($nb_parc == 0 && $typ_user != "APPRENANT")
       exit();
       $bgcolorB = '#400000';
       $bgcolorA = '#F2EBDC';
       echo "<TABLE WIDTH='100%' border='0' cellspacing='0' cellpadding='0'><TR><TD>";
       if (isset($utilisateur) && $utilisateur > 0)
       {
         $lien="gest_parc.php?numero_groupe=$numero_groupe&hgrp=$hgrp&switch=1&vp=1&accord=$accord&utilisateur=$utilisateur&a_faire=1&act_open=$act_open&seq_ouverte=$id_seq&parc_ouvert=$parc&formation=$formation";
         $lien = urlencode($lien);
         $message = $vue_plane;
       }
       else
       {
         $lien="gest_parc.php?numero_groupe=$numero_groupe&switch=1&vp=1&accord=$accord&a_faire=1&act_open=$act_open&seq_ouverte=$id_seq&parc_ouvert=$parc&formation=$formation";
         $lien = urlencode($lien);
         $message = $mess_bas_print;
       }
       if ($graph == 1 || !isset($graph))
       {
         $suite="";
         $suit=1;
         $grap=0;
       }
       elseif ($graph == 0)
       {
         $suite = 1;
         $suit="";
         $grap=1;
       }
       if ($formation == 1)
       {
          $actif_seq = "";
          $seq_ouverte = 0;
          $seq_ouvrir = 0;
       }
       if (isset($parc_ouvrir))
         $cible = $parc_ouvrir;
       elseif (isset($parc))
         $cible = $parc;
       $voir_parc = !isset($parc) ? mysql_result ($parc_query,0,"suiv3_parc_no") : $parc;
       $nom_grp =GetDataField ($connect,"select grp_nom_lb from groupe WHERE grp_cdn = $numero_groupe","grp_nom_lb");
       $lien1 = "gest_frm_rappel$suite.php?hgrp=$hgrp&utilisateur=$utilisateur&a_faire=1&tout=$tout&graph=$grap&numero_groupe=$numero_groupe&formation=$formation";
       $lien1 = urlencode($lien1);
       if ($tout != 1)
         $lien2 = "gest_parc1.php?numero_groupe=$numero_groupe&hgrp=$hgrp&tout=1&utilisateur=$utilisateur&a_faire=1&parc_ouvrir=$actif_parc&seq_ouvrir=$actif_seq&graph=$graph&formation=$formation";
       elseif($tout == 1 && $utilisateur > 0)
         $lien2 = "gest_frm_rappel$suit.php?numero_groupe=$numero_groupe&hgrp=$hgrp&saut=1&utilisateur=$utilisateur&a_faire=1&tout=0&graph=$graph&formation=$formation";
       elseif($tout == 1 && !$utilisateur)
       {
         $lien2 = "gest_frm_rappel$suit.php?a_faire=1&tout=0&graph=$graph&formation=$formation";
       }
       if ($formation == 1)
          $formation=0;
       elseif ($formation == 0 || !isset($formation))
          $formation=1;
       $url_origine = $_SERVER['REQUEST_URI'];
       $lien_droit = "details_parc.php?hgrp=$hgrp&utilisateur=$utilisateur&a_faire=1&numero_groupe=$numero_groupe&saut=1&parc_voir=$cible";
       $lien_droit = urlencode($lien_droit);
       echo "<TABLE WIDTH='100%'height='58' border='0' background='images/gest_parc/fondbleu.gif' cellspacing='0' cellpadding='0'>";
       echo "<TR>";
        echo "<TD align='center' width='20%'><A href=\"trace.php?link=$lien\" target='main' ".
             "onmouseover=\"img_imprim.src='images/gest_parc/icoimprimb.gif';".
             "overlib('<TABLE><TR><TD width=5></TD><TD>".addslashes(trim($clic_imp_form))."</TD></TR></TABLE>',ol_hpos,RIGHT,BELOW,WIDTH,'160',DELAY,800,CAPTION,'');".
             "return true;\" onmouseout=\"img_imprim.src='images/gest_parc/icoimprim.gif';nd();\">";
        echo "<IMG NAME=\"img_imprim\" SRC=\"images/gest_parc/icoimprim.gif\" BORDER='0' ".
             "onLoad=\"tempImg=new Image(0,0); tempImg.src='images/gest_parc/icoimprimb.gif'\"></A>";
        echo "</TD>";
       echo "<TD align='middle' width='60%'><DIV id='titre'>";
       $carac_grp = strlen($nom_grp);
       //YM modif 50 remplace 30
       if ($carac_grp > 50)
         $nom_grp1 = substr($nom_grp,0,51)."...";
       else
         $nom_grp1 = $nom_grp;
       if (isset($utilisateur) && $utilisateur > 0)
       {
             echo "<A HREF=\"trace.php?link=$lien_droit\" target='principal' ".
                  "onclick=\"javascript:document.location.replace('$url_origine&formation=$formation')\" ".
                  bulle("$mess_menu_present : $nom_grp","","CENTER","BELOW",160);
          echo "<span class='user'>$prenom_util $nom_util</span><BR><span class='form'>$nom_grp1</span></A>";
       }
       else
       {
             echo "<A HREF=\"trace.php?link=$lien_droit\" target='principal' ".
                  "onclick=\"javascript:document.location.replace('$url_origine&formation=$formation')\" ".
                  bulle("$mess_menu_present : $nom_grp","","CENTER","BELOW",140);
             echo "<span class='form'>$nom_grp1</span></A>";
       }
       echo "</DIV></TD>";
       if ($suite == 1)
       {
        echo "<TD align='left' width='20%'><A href=\"trace.php?link=$lien1\" target='main'".
             " onmouseover=\"img_diode.src='images/gest_parc/icodiodeb.gif';".
             "overlib('<TABLE><TR><TD width=5></TD><TD>".addslashes(trim($mess_evo_seq_ok))."</TD></TR></TABLE>',ol_hpos,LEFT,BELOW,WIDTH,'160',DELAY,800,CAPTION,''); ".
             "return true;\" onmouseout=\"img_diode.src='images/gest_parc/icodiode.gif';nd();\">";
        echo "<IMG NAME=\"img_diode\" SRC=\"images/gest_parc/icodiode.gif\" BORDER='0' ".
             "onLoad=\"tempImg=new Image(0,0); tempImg.src='images/gest_parc/icodiodeb.gif'\"></A></TD>";
       }
       else
       {
        echo "<TD align='left' width='20%'><A href=\"trace.php?link=$lien1\" target='main' ".
             "onmouseover=\"img_diode.src='images/gest_parc/icodiode.gif';".
             "overlib('<TABLE><TR><TD width=5></TD><TD>".addslashes(trim($mess_evo_seq_no))."</TD></TR></TABLE>',ol_hpos,LEFT,BELOW,WIDTH,'160',DELAY,800,CAPTION,''); ".
             "return true;\"onmouseout=\"img_diode.src='images/gest_parc/icodiodeb.gif';nd();\">";
        echo "<IMG NAME=\"img_diode\" SRC=\"images/gest_parc/icodiodeb.gif\" BORDER='0' ".
             "onLoad=\"tempImg=new Image(0,0); tempImg.src='images/gest_parc/icodiode.gif'\"></A></TD>";
       }
       echo "</TR></TABLE>";
       echo "<TABLE bgcolor='#CCE6EC' border='0' width='100%' cellspacing='0' cellpadding='0'>".
            "<TR><TD><TABLE  border='0' width='100%' cellspacing='0' cellpadding='0'>";
       echo "<TR><TD align='right'>";
       if (strstr($lien2,"gest_frm_rappel"))
         $envoyer_vers = "main";
       else
         $envoyer_vers = "idx";
       $lien2 = urlencode($lien2);
       if ($tout != 1)
       {
         echo "<A href=\"trace.php?link=$lien2\" target='$envoyer_vers' title=\"$mess_gp_tt_det_form\"".
          " onmouseover=\"img_flb.src='images/gest_parc/icoBflechbasb.gif';return true;\"".
          " onmouseout=\"img_flb.src='images/gest_parc/icoBflechbas.gif'\">".
          "<IMG NAME=\"img_flb\" SRC=\"images/gest_parc/icoBflechbas.gif\" BORDER='0' valign='top' alt=\"$mess_gp_tt_det_form\"".
          " onLoad=\"tempImg=new Image(0,0); tempImg.src='images/gest_parc/icoBflechbasb.gif'\"></A>";
       }
       elseif($tout == 1)
       {
         echo "<A href=\"trace.php?link=$lien2\" target='$envoyer_vers' title=\"$mess_gp_no_det_form\"".
          " onmouseover=\"img_flh.src='images/gest_parc/icoBflechhob.gif';return true;\"".
          " onmouseout=\"img_flh.src='images/gest_parc/icoBflechho.gif'\">".
          "<IMG NAME=\"img_flh\" SRC=\"images/gest_parc/icoBflechho.gif\" BORDER='0' valign='top' alt=\"$mess_gp_no_det_form\"".
          " onLoad=\"tempImg=new Image(0,0); tempImg.src='images/gest_parc/icoBflechhob.gif'\"></A>";
       }
       echo "</TD></TR>";
       if ($id_classe == 0)
       {
          echo "<TR><TD align='center' valign='top' height='30'>";
              $lien = "MindMapper.php?id_grp=$numero_groupe";
              echo "<div style='text-align:center;padding:0 0 12px 60px;'>".
                   "<a class='bouton_new' href='$lien' title='Voir la carte heuristique de cette formation'".
                   "  onClick=\"javascript:simplejQ_Ajax('/admin/InsereTrace.php?lelien=".urlencode($lien)."');\">";
              echo "Carte heuristique de la formation</A></div>";
          echo "</TD></TR>";
       }
       $p=0;
       while ($p < $nb_parc)
       {
              $fin_parc=0;unset($scormOk);
              $id_parc = mysql_result ($parc_query,$p,"suiv3_parc_no");
              $etat_parc =  GetDataField ($connect,"SELECT suiv3_etat_lb from suivi3$Ext WHERE
                                                    suiv3_parc_no = $id_parc AND
                                                    suiv3_utilisateur_no = $num_app and
                                                    suiv3_grp_no = $numero_groupe","suiv3_etat_lb");

// ajouté le 30/06/08-----------------------------------------------Dey-
              if ($id_classe == 0)
                  ClassSeqActualiseOrdre($id_parc,$num_app,$numero_groupe);
//-------------------------------------------------------------------
              if ($id_parc != 0)
              {
                $type_parcours = getdatafield ($connect,"SELECT parcours_type_lb from parcours where
                                                         parcours_cdn = $id_parc","parcours_type_lb");
                $createur = GetDataField ($connect,"SELECT parcours_auteur_no FROM parcours WHERE
                                                    parcours_cdn = $id_parc","parcours_auteur_no");
                $nom = GetDataField ($connect,"SELECT parcours_nom_lb FROM parcours WHERE parcours_cdn = $id_parc","parcours_nom_lb");
                $nbr_carac = strlen($nom);
                $description = GetDataField ($connect,"SELECT parcours_desc_cmt FROM parcours WHERE
                                                       parcours_cdn = $id_parc","parcours_desc_cmt");
                $id_ref_parc = GetDataField ($connect,"SELECT parcours_referentiel_no FROM parcours WHERE
                                                       parcours_cdn = $id_parc","parcours_referentiel_no");
                $nom_referentiel = GetDataField ($connect,"SELECT ref_desc_cmt FROM referentiel WHERE
                                                           ref_cdn = $id_ref_parc","ref_desc_cmt");
                $desc = str_replace ("'","\'",$nom_referentiel);
                $nom_createur = GetDataField ($connect,"SELECT util_nom_lb FROM utilisateur WHERE util_cdn = $createur","util_nom_lb");
                $prenom_createur = GetDataField ($connect,"SELECT util_prenom_lb FROM utilisateur WHERE
                                                           util_cdn = $createur","util_prenom_lb");
              }
              else
              {
                $nom = GetDataField ($connect,"SELECT parcours_nom_lb FROM parcours WHERE parcours_cdn = $id_parc","parcours_nom_lb");
                $nbr_carac = strlen($nom);
                $description = GetDataField ($connect,"SELECT parcours_desc_cmt FROM parcours WHERE
                                                       parcours_cdn = $id_parc","parcours_desc_cmt");
                $desc = $mess_gp_nolien_ref;
                $description = "<FONT COLOR='silver'>$mess_gp_parc_0_desc</FONT>";
                $nom_createur = $mess_gen_formagri;
                $prenom_createur = $mess_gen_gen_formagri;
                $nom_referentiel = $mess_gp_nolien_ref;
              }
              if ($nbr_carac > 71)
                 $nom_parc = substr(NewHtmlEntityDecode($nom),0,70)."..";
              else
                 $nom_parc = $nom;
              $nom_affiche= str_replace("'","|",$nom);
              //$lien = "gest_parc1.php?formation=0&graph=$graph&tout=$tout&numero_groupe=$numero_groupe&hgrp=$hgrp&consult=1&parcours=$parcours&id_parc=$id_parc&id_ref_parc=$id_ref_parc";
              $deroulee_parc[$p] = 1;
              $lien = "gest_parc1.php?formation=0&graph=$graph&tout=$tout&parc=$id_parc&deroule_parc[$p]=$deroulee_parc[$p]&numero_groupe=$numero_groupe&hgrp=$hgrp&accord=$accord&utilisateur=$utilisateur&a_faire=1&parc_ouvrir=$id_parc";
              $lien = urlencode($lien);
              $lien1 = "details_parc.php?numero_groupe=$numero_groupe&hgrp=$hgrp&id_parc=$id_parc&ouvrir=parcours&utilisateur=$utilisateur&origine=$origine";
              $lien1 = urlencode($lien1);
              if ($tout == 1)
                 echo "<TR>";
              else
                 echo "<TR>";
              echo "<TD align='left' height = '20' valign='top'><DIV id='menu'>&nbsp;&nbsp;".
                   "<A HREF=\"trace.php?link=$lien\" target='idx' title = \"$nom\" ".
                   "onclick='parent.principal.location=\"trace.php?link=$lien1\"'>$nom_parc</A></DIV></TD></TR>";
       //Sequences a faire dans le parcours déroulé
  if (((isset($deroule_parc[$p]) && $deroule_parc[$p] == 1 && $parc == $id_parc) || $tout == 1 || $actif_parc == $id_parc) || ($vn == 1 && $id_parc == $parc_ouvert))
  {
       $order_apparition = ($id_classe == 0) ? "suivi2$Ext.suiv2_ordre_no asc" : "suivi2$Ext.suiv2_ordre_no asc";
//     $order_apparition = ($id_classe == 0) ? "suivi2$Ext.suiv2_ordre_no asc" : "prescription$Ext.presc_datefin_dt,prescription$Ext.presc_ordre_no,suivi2$Ext.suiv2_ordre_no ASC";
     $parc_ouvert = $id_parc;
       if (isset($utilisateur) && $utilisateur > 0 &&  $typ_user == "TUTEUR")
       {
          $seq_query = mysql_query ("SELECT DISTINCT suivi2$Ext.suiv2_cdn FROM
                                     suivi2$Ext,prescription$Ext WHERE
                                     suivi2$Ext.suiv2_seq_no = prescription$Ext.presc_seq_no AND
                                     prescription$Ext.presc_parc_no=$id_parc AND
                                     prescription$Ext.presc_grp_no = $numero_groupe AND
                                     suivi2$Ext.suiv2_grp_no = $numero_groupe AND
                                     suivi2$Ext.suiv2_utilisateur_no = $utilisateur AND
                                     prescription$Ext.presc_utilisateur_no=$utilisateur
                                     order by $order_apparition");
          $num_app = $utilisateur;
       }
       elseif (isset($utilisateur) && $utilisateur > 0 && ($typ_user == "ADMINISTRATEUR"  || $typ_user == "FORMATEUR_REFERENT" || $typ_user == "RESPONSABLE_FORMATION"))
       {
          $seq_query = mysql_query ("SELECT suivi2$Ext.suiv2_cdn FROM
                                     suivi2$Ext,prescription$Ext WHERE
                                     suivi2$Ext.suiv2_seq_no = prescription$Ext.presc_seq_no AND
                                     prescription$Ext.presc_parc_no = $id_parc AND
                                     prescription$Ext.presc_grp_no = $numero_groupe AND
                                     suivi2$Ext.suiv2_grp_no = $numero_groupe AND
                                     suivi2$Ext.suiv2_utilisateur_no = $utilisateur AND
                                     prescription$Ext.presc_utilisateur_no = $utilisateur
                                     ORDER BY $order_apparition");
          $num_app = $utilisateur;
       }
       elseif(!$utilisateur)
       {
          $seq_query = mysql_query ("SELECT suivi2$Ext.suiv2_cdn FROM
                                     suivi2$Ext,prescription$Ext WHERE
                                     suivi2$Ext.suiv2_seq_no = prescription$Ext.presc_seq_no AND
                                     prescription$Ext.presc_parc_no=$id_parc AND
                                     prescription$Ext.presc_grp_no = $numero_groupe AND
                                     suivi2$Ext.suiv2_grp_no = $numero_groupe AND
                                     suivi2$Ext.suiv2_utilisateur_no = $id_user AND
                                     prescription$Ext.presc_utilisateur_no = $id_user
                                     ORDER BY $order_apparition");
          $num_app = $id_user;
       }
       $nb_seq = mysql_num_rows ($seq_query);
       $bgcolor2 = '#408080';
       $bgcolor1 = '#f3fCE3';
       echo "<TR><TD width='100%'><table width='100%' border='0' cellspacing='0'>";
       if ($utilisateur > 0)
          $num_app = $utilisateur;
       else
          $num_app = $id_user;
       $i = 0;
       while ($i != $nb_seq)
       {
              unset($scormOk);
//              $seq = mysql_result ($seq_query,$i,"suiv2_seq_no");
              $son_id = mysql_result ($seq_query,$i,"suiv2_cdn");
              $seq = GetDataField ($connect,"SELECT suiv2_seq_no from suivi2$Ext WHERE
                                             suiv2_cdn= $son_id AND
                                             suivi2$Ext.suiv2_grp_no = $numero_groupe","suiv2_seq_no");
              $etat = GetDataField ($connect,"SELECT suiv2_etat_lb from suivi2$Ext WHERE
                                              suiv2_cdn= $son_id AND
                                              suivi2$Ext.suiv2_grp_no = $numero_groupe","suiv2_etat_lb");
              $nom_sequence = GetDataField ($connect,"SELECT seq_titre_lb from sequence WHERE seq_cdn= $seq","seq_titre_lb");
              $type_sequence = GetDataField ($connect,"SELECT seq_type_lb from sequence where seq_cdn = '$seq'","seq_type_lb");
              if (strstr($type_sequence,"SCORM"))
              {
                 $scormOk = 1;
                 $icono = "<IMG SRC='images/gest_parc/scorm.gif' border='0' title=\"$mess_seq_sco\">";
              }
              else
              {
                 $scormOk = 0;
                 $icono = "";
                 $xApi_sequence = mysql_query ("SELECT act_cdn FROM activite,activite_devoir WHERE
                                                 act_cdn = actdev_act_no AND act_seq_no = $seq AND
                                                 actdev_dev_lb = 'xApi TinCan'");
                 if  (mysql_num_rows($xApi_sequence) > 0)
                    $icono = "<img src='images/gest_parc/xApi.gif' border='0' ".
                             "title=\"Contient au moins une activité au standard xApi TinCan\">";

              }
              $nom_seq = $nom_sequence;
              $desc_seq = GetDataField ($connect,"SELECT seq_desc_cmt from sequence WHERE seq_cdn=$seq","seq_desc_cmt");
              $formateur = GetDataField ($connect,"SELECT presc_formateur_no from prescription$Ext WHERE
                                                   presc_seq_no = $seq and
                                                   presc_grp_no = $numero_groupe AND
                                                   presc_utilisateur_no = $num_app","presc_formateur_no");
              $nom_form=GetDataField ($connect,"SELECT util_nom_lb from utilisateur WHERE util_cdn='$formateur'","util_nom_lb");
              $prenom_form=GetDataField ($connect,"SELECT util_prenom_lb from utilisateur WHERE util_cdn='$formateur'","util_prenom_lb");
              $majuscule = ucwords($prenom_form)." ".ucwords($nom_form);//YM
              $prerequis="";
              $prereq_query = mysql_query ("SELECT * from prerequis WHERE prereq_seq_no = $seq");
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
                      $verif_seq_req = mysql_query ("select count(presc_seq_no) from prescription$Ext where
                                                     presc_seq_no = $condition and
                                                     presc_utilisateur_no = $num_app and
                                                     presc_grp_no = $numero_groupe");
                      $nb_verif_req = mysql_result($verif_seq_req,0);
                      if ($nb_verif_req > 0 ){
                         $etat_seq_req = GetDataField ($connect,"select suiv2_etat_lb from suivi2$Ext where
                                                              suiv2_seq_no = $condition and
                                                              suiv2_utilisateur_no = $num_app AND
                                                              suivi2$Ext.suiv2_grp_no = $numero_groupe","suiv2_etat_lb");
                         $nom_seq_req = GetDataField ($connect,"select seq_titre_lb from sequence where
                                                               seq_cdn = $condition","seq_titre_lb");
                         if ($etat_seq_req != 'TERMINE')
                         {
                             $motif = $mess_gp_seq_preq;
                            $proposable = 0;
                            $prerequis = $mess_seq_prq." <B>".$nom_seq_req."</B> ".$mess_no_fin;
                         }else {
                            $nb_proposable++;
                         }
                      }
                  }

                  //on a besoin du numero de l'activite pour recuperer les notes
                  if ($type_condition == 'ACTIVITE') {
                      $condition = mysql_result ($prereq_query,$jj,"prereq_actcondition_no");
                      $num_seq_req = GetDataField ($connect,"select act_seq_no from activite where act_cdn = $condition","act_seq_no");
                      $verif_seq_req = mysql_query ("select count(presc_seq_no) from prescription$Ext where
                                                     presc_seq_no = $num_seq_req and
                                                     presc_utilisateur_no = $num_app and
                                                     presc_grp_no = $numero_groupe");
                      $nb_verif_req = mysql_result($verif_seq_req,0);
                      if ($nb_verif_req > 0 )
                      {
                         $etat_act_req = GetDataField ($connect,"select suivi_etat_lb from suivi1$Ext where
                                                              suivi_act_no = $condition and
                                                              suivi_utilisateur_no = $num_app AND
                                                              suivi1$Ext.suivi_grp_no = $numero_groupe","suivi_etat_lb");
                         if ($etat_act_req != 'TERMINE') {
                             $motif = $mess_gp_aut_act;
                             $proposable = 0;
                             $prerequis = $mess_act_prq." <B>".str_replace("'","|",$act_prereq)."</B> ".$mess_no_fin;
                         }else {
                             $nb_proposable++;
                         }
                      }
                  }

                  if ($type_condition == 'NOTE') {
                     $condition = mysql_result ($prereq_query,$jj,"prereq_actcondition_no");
                      $etat_act_req = GetDataField ($connect,"select suivi_etat_lb from suivi1$Ext where
                                                              suivi_act_no = $condition and
                                                              suivi_utilisateur_no = $num_app AND
                                                              suivi1$Ext.suivi_grp_no = $numero_groupe","suivi_etat_lb");
                     if ($etat_act_req != 'TERMINE') {
                          $motif = $mess_gp_act_preq;
                          $proposable = 0;
                     }else{
                      $note_min = mysql_result ($prereq_query,$jj,"prereq_notemin_nb1");
                      $note_max = mysql_result ($prereq_query,$jj,"prereq_notemax_nb1");
                      $note_obtenue = GetDataField ($connect,"select suivi_note_nb1  from suivi1$Ext where
                                                              suivi_act_no = $condition and
                                                              suivi_utilisateur_no = $num_app AND
                                                              suivi1$Ext.suivi_grp_no = $numero_groupe"  ,"suivi_note_nb1");
                      if (($note_obtenue < $note_min || $note_obtenue > $note_max) || $note_obtenue == "acquis")  {
                        $motif = $mess_gp_note;
                        $proposable = 0;
                        $prerequis = $mess_note_prq;
                      }else{
                        $nb_proposable++;
                      }
                    }
                  }
              $jj++;
              }
              if ($nb_proposable == $nb_prereq)
              {
                 $proposable=1;
                 $prerequis="OK";
              }else{
                 $proposable = 0;
              }
            } //fin if ($nb_prereq !=0)
            else
              $proposable = 1;
            if ($scormOk == 1)
               $act_query = mysql_query ("select * from scorm_module,scorm_util_module$Ext where
                                          scorm_module.mod_seq_no = $seq and scorm_module.mod_content_type_lb != 'LABEL' and
                                          scorm_util_module$Ext.mod_module_no = scorm_module.mod_cdn and
                                          scorm_util_module$Ext.user_module_no = $num_app and
                                          scorm_util_module$Ext.mod_grp_no = $numero_groupe");
            else
               $act_query = mysql_query ("select * from activite,suivi1$Ext where
                                          activite.act_seq_no = $seq AND
                                          suivi1$Ext.suivi_act_no = activite.act_cdn and
                                          suivi1$Ext.suivi_utilisateur_no = $num_app AND
                                          suivi1$Ext.suivi_grp_no = $numero_groupe");

            $Nb_act_seq = mysql_num_rows($act_query);
            $aq = 0;
            $encore = 0;
              $date_deb = GetDataField ($connect,"SELECT presc_datedeb_dt FROM prescription$Ext WHERE
                                                  presc_seq_no = $seq AND
                                                  presc_utilisateur_no = $num_app AND
                                                  presc_grp_no = $numero_groupe","presc_datedeb_dt");
              $date_fin = GetDataField ($connect,"SELECT presc_datefin_dt FROM prescription$Ext WHERE
                                                  presc_seq_no = $seq AND
                                                  presc_utilisateur_no = $num_app AND
                                                  presc_grp_no = $numero_groupe","presc_datefin_dt");
              $today = date("Y-m-d");
              //On compare la date de deb a la date d'aujourd'hui pour savoir s'il peut encore faire la sequence
              //Pour simplifier le test, on SELECTionne le nombre de jours passes depuis an 0 pour $today et $date_deb
              $nb_date_fin_query = mysql_query ("SELECT TO_DAYS('$date_fin')");
              $nb_date_fin = mysql_result ($nb_date_fin_query,0);
              $nb_date_deb_query = mysql_query ("SELECT TO_DAYS('$date_deb')");
              $nb_date_deb = mysql_result ($nb_date_deb_query,0);
              $nb_today_query = mysql_query ("SELECT TO_DAYS('$today')");
              $nb_today = mysql_result ($nb_today_query,0);
              for ($nn = 1;$nn < 10;$nn++){
                if ($nb_date_fin == ($nb_today+$nn) && $etat != "TERMINE") {
                   $avertisseur = 1;
                }
              }
              $depasse=0;
              if ($nb_date_fin < $nb_today)
                 $depasse=1;
              if ($nb_date_fin < $nb_today && $etat == "TERMINE")
                 $autorise=1;
              if ($nb_date_deb <= $nb_today)
                 $visible = 1;
              else
                 $visible = 0;
              $ch_date_deb = explode ("-",$date_deb);
              $date_deb = "$ch_date_deb[2]/$ch_date_deb[1]/$ch_date_deb[0]";
              $date_fin = GetDataField ($connect,"SELECT presc_datefin_dt FROM prescription$Ext WHERE
                                                  presc_seq_no = $seq AND
                                                  presc_utilisateur_no = $num_app AND
                                                  presc_grp_no = $numero_groupe","presc_datefin_dt");
              if ($scorm_Ok == 1)
                 $duree_seq = GetDataField ($connect,"SELECT seq_duree_nb from sequence where seq_cdn = '$seq'","seq_duree_nb");
              else{
                 $duree_sequence = mysql_query ("SELECT SUM(activite.act_duree_nb) FROM activite,suivi1$Ext WHERE
                                                 activite.act_cdn = suivi1$Ext.suivi_act_no AND
                                                 activite.act_seq_no=$seq AND
                                                 suivi1$Ext.suivi_utilisateur_no =$num_app AND
                                                 suivi1$Ext.suivi_grp_no = $numero_groupe");
                 $duree_seq = mysql_result($duree_sequence,0);
              }
            $ch_date_fin = explode ("-",$date_fin);
            $date_fin = "$ch_date_fin[2]/$ch_date_fin[1]/$ch_date_fin[0]";

            //Besoin pour ouvrir les activites a partir de la sequence
            if ($utilisateur > 0)
                  $apprenant=0;
            else
                  $apprenant=1;
            $ii=$i-1;
            if ($nom_user == "Test")
               $proposable = 1;
            if ($autorise == 1 ||($nb_today <= $nb_date_fin) && (($nb_prereq > 0 && ($proposable == 1 || $nb_verif_req == 0)) || $nb_prereq == 0) && ((($marqueur == 1 && $marqueur[$ii] != 1) || $encore ==0) || ($utilisateur)))
               $accord = 1;
            else
               $accord = 0;
            if ($tout == 1)
            {
              $parc_actif = $id_parc;
              $seq_actif = $seq;
            }
            if ($tout != 1){
              if ((isset($deroule[$i]) && $deroule[$i] == 1 && $id_seq == $seq) || ($seq == $seq_ouverte && $seq_ouverte > 0) && (!isset($switch) || (isset($switch) && $switch != 1)))
               $deroulee[$i] = 0;
              else
               $deroulee[$i] = 1;
            }
            if ($seq == $actif_seq){
              $deroulee[$i] = 0;$deroule_parc[$p] = 1;
            }else{
              $deroulee[$i] = 1;$deroule_parc[$p] = 1;
            }
            if (!isset($seq_actif)) $seq_actif = '';
            if (!isset($parc_actif)) $parc_actif = '';
            $lien="gest_parc1.php?formation=0&graph=$graph&numero_groupe=$numero_groupe&depasse=$depasse&proposable=$proposable&visible=$visible&hgrp=$hgrp&utilisateur=$utilisateur&a_faire=1&id_seq=$seq&actif_seq=$seq_actif&actif_parc=$parc_actif&parc=$id_parc&deroule[$i]=$deroulee[$i]&deroule_parc[$p]=$deroule_parc[$p]&tout=$tout&voir_parc=$id_parc";
            $lien = urlencode($lien);
            if ($utilisateur > 0 && $formateur == $id_user){
               $image_affiche1 = "images/gest_parc/icofeuil.gif";
               $image_affiche2 = "images/gest_parc/icofeuilb.gif";
            }elseif (!$utilisateur){
               $image_affiche1 = "images/gest_parc/icofeuilb.gif";
               $image_affiche2 = "images/gest_parc/icofeuil.gif";
            }elseif($utilisateur > 0 && $formateur != $id_user){
               //$image_affiche1 = "images/spacer.gif";
               $image_affiche1 = "images/gest_parc/icofeuilb.gif";
               $image_affiche2 = "images/gest_parc/icofeuil.gif";

            }
            $lien1 ="details_parc.php?numero_groupe=$numero_groupe&hgrp=$hgrp&prq=$prerequis&accord=$accord&depasse=$depasse&visible=$visible&id_parc=$id_parc&seq=$seq&ouvrir=sequence&utilisateur=$utilisateur&scormOk=$scormOk";
            $lien1 = urlencode($lien1);
            $nom_sequence=str_replace("\"","'",$nom_sequence);//YM
            if (($tout != 1 && isset($deroule[$i]) && $deroule[$i] == 1 || ($seq == $seq_ouverte && $seq_ouverte > 0) || $seq == $actif_seq) || ($seq_ouvrir == $seq && $tout == 1)){
              echo "<TR bgcolor='white'><TD width='5%'></TD><td align='left' width='70%' colspan='2'><DIV id='seqinv'><A HREF=\"javascript:void(0);\" ".
                    " onMouseOver=\"overlib('<TABLE><TR><TD width=5></TD><TD>".addslashes(trim($nom_sequence))."<br><strong>$msq_formateur :</strong> ".addslashes($majuscule)."</TD></TR></TABLE>',ol_hpos,RIGHT,ABOVE,WIDTH,'170',DELAY,800,CAPTION,'')\" onMouseOut=\"nd()\"".
                    "onClick='parent.principal.location=\"trace.php?link=$lien1\"'; ".
                    "onMouseOver=\"img$seq.src='$image_affiche2';return true;\" onmouseout=\"img$seq.src='$image_affiche1'\">".
                    "<IMG NAME=\"img$seq\" SRC=\"$image_affiche1\" BORDER='0' width='8'  onLoad=\"tempImg=new Image(0,0); tempImg.src='$image_affiche2'\">".
                    "&nbsp;&nbsp;&nbsp;$icono&nbsp;&nbsp;$nom_seq</A></DIV></TD>";
               $seq_active = $seq;
               $couleur_0 = "#FFFFFF";
            }else{
              echo "<TR><TD width='5%'></TD><td align='left' width='70%' colspan='2'><DIV id='sequence'><A HREF=\"trace.php?link=$lien\" target='idx' ".
                    " onMouseOver=\"overlib('<TABLE><TR><TD width=5></TD><TD>".addslashes(trim($nom_sequence))."<br><strong>$msq_formateur :</strong> ".addslashes($majuscule)."</TD></TR></TABLE>',ol_hpos,RIGHT,ABOVE,WIDTH,'170',DELAY,800,CAPTION,'')\" onMouseOut=\"nd()\"".
                    "onClick='parent.principal.location=\"trace.php?link=$lien1\"'; ".
                    "onMouseOver=\"img$seq.src='$image_affiche1';return true;\" onmouseout=\"img$seq.src='$image_affiche2'\">".
                    "<IMG NAME=\"img$seq\" SRC=\"$image_affiche2\" BORDER='0' width='8'  onLoad=\"tempImg=new Image(0,0); tempImg.src='$image_affiche1'\">".
                    "&nbsp;&nbsp;&nbsp;$icono&nbsp;&nbsp;$nom_seq</A></DIV></TD>";
              $couleur_0 = "#CEE6EC";
            }
            if ($graph == 1)
            {
               if ($scormOk == 1)
                  $act_termine = mysql_query ("select count(mod_cdn) from scorm_module,scorm_util_module$Ext where
                                               scorm_module.mod_cdn = scorm_util_module$Ext.mod_module_no and
                                               scorm_module.mod_seq_no=$seq and scorm_module.mod_content_type_lb != 'LABEL' and
                                               scorm_util_module$Ext.user_module_no=$num_app AND
                                               scorm_util_module$Ext.mod_grp_no=$numero_groupe and
                                               (scorm_util_module$Ext.lesson_status='COMPLETED' OR
                                               scorm_util_module$Ext.lesson_status='PASSED' OR
                                               scorm_util_module$Ext.lesson_status='FAILED')");
               else
                  $act_termine = mysql_query ("select count(act_cdn) from activite,suivi1$Ext where
                                               activite.act_cdn = suivi1$Ext.suivi_act_no AND
                                               activite.act_seq_no = $seq AND
                                               suivi1$Ext.suivi_utilisateur_no = $num_app AND
                                               suivi1$Ext.suivi_grp_no = $numero_groupe AND
                                               suivi1$Ext.suivi_etat_lb = 'TERMINE'");
               $nb_acterm = mysql_result($act_termine,0);
               $pourcent= ($Nb_act_seq > 0 && $nb_acterm > 0)? round($nb_acterm/$Nb_act_seq,2)*100 : 0;
               if ($pourcent > 9 && $pourcent < 100)
                  $texte = "<font color='$couleur_0'>0</font>".$pourcent."%";
               elseif ($pourcent < 10)
                  $texte = "<font color='$couleur_0'>00</font>".$pourcent."%";
               else
                  $texte = $pourcent."%";
               if ($Nb_act_seq > 0)
                  echo "<TD align='right' width='30%' nowrap>".
                   "<IMG SRC=\"image_create.php?utilisateur=$num_app&detail=0&seq=$seq&nb_act=$Nb_act_seq&scormOk=$scormOk&numero_groupe=$numero_groupe\" title=\"$mess_nbact_pourcent\"><FONT color='#002D44'>&nbsp;<B>$texte</B></FONT></TD></TR>";
               else
                  echo "<TD align='right' width='30%' nowrap>&nbsp;</TD></TR>";
            }
            // Inscription de la dernière séquence vue dans la table traceur
            if ($typ_user == 'APPRENANT' && ((((isset($deroule[$i]) && $deroule[$i] == 1) || $seq == $actif_seq) && $tout != 1) || ($tout == 1 && ($actif_seq == $seq || $seq_ouverte == $seq))))
            {
              $der_details = "details_parc.php?scormOk=$scormOk&numero_groupe=$numero_groupe&hgrp=$hgrp&prq=$prerequis&accord=$accord&proposable=$proposable&autorise=$autorise&depasse=$depasse&visible=$visible&id_parc=$id_parc&seq=$seq&ouvrir=sequence&seq_ouverte=$seq&parc_ouvert=$id_parc";
              $der_gest = "gest_parc1.php?scormOk=$scormOk&numero_groupe=$numero_groupe&depasse=$depasse&proposable=$proposable&visible=$visible&hgrp=$hgrp&prq=$prerequis&a_faire=1&seq_ouverte=$seq&parc_ouvert=$id_parc&actif_seq=$seq&actif_parc=$parc_actif&id_seq=$seq&parc=$id_parc&deroule_parc[$p]=$deroule_parc[$p]";
              $req = mysql_query("SELECT traceur_cdn from traceur WHERE traceur_util_no = $id_user AND traceur_grp_no = $numero_groupe");
              $nbr_trac = mysql_num_rows($req);
              $date_cour = date ("Y/n/d");
              if ($nbr_trac == 1)
              {
                 $requete = mysql_query("UPDATE traceur SET traceur_der_details = \"$der_details\",traceur_der_gest1 = \"$der_gest\",traceur_date_dt = \"$date_cour\" WHERE traceur_util_no = $id_user AND traceur_grp_no = $numero_groupe");
              }else{
                 $id_traceur = Donne_ID ($connect,"SELECT MAX(traceur_cdn) FROM traceur");
                 $requete = mysql_query("INSERT INTO traceur (traceur_cdn,traceur_util_no,traceur_der_details,traceur_der_gest1,traceur_date_dt,traceur_grp_no) VALUES ($id_traceur,$id_user,\"$der_details\",\"$der_gest\",\"$date_cour\",$numero_groupe)");
              }
            }
        $i++;
        if ($i == $nb_seq)
          echo "<TR><TD width=100% height='10'colspan='4'></TD></TR>";
      }
      echo "</TABLE>";
    }
    $p++;
    echo "<TR><TD width=100% background=\"images/gest_parc/tiret.gif\" border='0'></TD></TR>";
    echo "<TR><TD width=100% height='5'colspan='4'></TD></TR>";
  }
  echo "</TABLE>";

?>
</BODY>
</HTML>
