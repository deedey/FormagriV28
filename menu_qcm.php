<?php
if (!isset($_SESSION)) session_start();
if (!isset($_SESSION['id_user']) || $_SESSION['id_user'] == "")
{
  exit();
}
include ("include/UrlParam2PhpVar.inc.php");
require 'fonction.inc.php';
require 'admin.inc.php';
require "fonction_html.inc.php";
require "langues/module.inc.php";
require "langues/ress.inc.php";
require "lang$lg.inc.php";
dbConnect();
$date_op = date("Y-m-d H:i:s" ,time());
$heure_fiche = substr($date_op,11);
$date_fiche = substr($date_op,0,10);
include ('style.inc.php');
if (isset($_GET['code']) && $_GET['code'] > 0 && !isset($_GET['ressource']))
  $mess_notif = "L'archive Zip a été décompressée et étudiée et le Quizz QTI a été importé.<br />".
                "Vous le distinguez dans la liste car il est entouré de signes *** de couleur rouge.<br />".
                "Vous pourrez ainsi l'incorporer dans votre index de ressources.";
?>
<style>
.bouton_vert{font-family: arial;font-weight: bold;float: left;color: #FFFFFF;margin-left:5px;padding-top: 3px;padding-bottom: 2px;padding-left: 5px;padding-right: 5px;border: 0px solid #24677A;background-image:url(images/ecran-annonce/ongl01.gif);}
.bouton_hover{font-family: arial;font-weight: bold;color: #D45211;float: left;text-align: right;margin-left:5px;padding-top: 2px;padding-bottom: 1px;padding-left: 4px;padding-right: 4px;border: 1px solid #24677A;background-image:url(images/ecran-annonce/ongl02.gif);}
</style>
<script type="text/javascript" src="OutilsJs/lib/ajax/ajax_cms.js"></script>
<div id="affiche" class="Status"></div>
<div id="mon_contenu" class="cms"
        onClick="javascript:$(document).ready(function() {if ($.browser.msie) {$('#mon_contenu').hide();}else{$('#mon_contenu').hide('slow');}})"
        <?php echo "title=\"$mess_clkF\">";?>
</div>
<?php
if ($suppression == 1)
{
      $efface_sql= mysql_query("DELETE FROM qcm_linker WHERE qcmlinker_param_no='$idParam'");
      $efface_sql= mysql_query("DELETE FROM qcm_param WHERE ordre='$idParam'");
      $efface_sql= mysql_query("DELETE FROM ressource_new WHERE ress_url_lb like '%qcm.php?code=$idParam'");
      $mess_notif = "Le Qcm a été supprimé, mais ses questions font désormais partie de votre bibliothèque de questions ";
}
if (isset($mess_notif) && $mess_notif != '')
   echo notifier($mess_notif);
entete_simple($mess_menu_gest_qcm);
echo "<tr><td style=\"padding-top:2px;\">";
$lien_qcmS = "qcmTrousScorm.php?creation=1";
$lien_qcmS = urlencode($lien_qcmS);
$lien_ImportQti = "qti_import.php?keepThis=true&TB_iframe=true&height=270&width=800";
if (isset($auteur) && $auteur > 0)

   echo "<div id='cnslt' style=\"float:left;padding-left:8px;\">".
        "<a href=\"trace.php?link=".urlencode("menu_qcm.php")."\" class='bouton_new'>$mmsg_noAffTt</a></div>";

else
{
   $lien="qcm_create.php?action=create&keepThis=true&TB_iframe=true&height=700&width=950";
   $lien = urlencode($lien);
   echo "<a href=\"trace.php?link=$lien\" class='thickbox'".
        " name=\"Création d'un nouveau Qcm\">".
        "<div id='creer'  class='bouton_vert' ".
        "onMouseOver=\"\$(this).removeClass();\$(this).addClass('bouton_hover');\" ".
        "onMouseOut=\"\$(this).removeClass();\$(this).addClass('bouton_vert');\">$mmsg_qcm_cr</div></a>";
   echo "<a href=\"trace.php?link=$lien_qcmS\"><div id='trous' class='bouton_vert' ".
        "onMouseOver=\"\$(this).removeClass();\$(this).addClass('bouton_hover');\" ".
        "onMouseOut=\"\$(this).removeClass();\$(this).addClass('bouton_vert');\">Créer ou modifier un Qcm SCORM à Trous</div></a>";
   echo "<div id='trous' style=\"float:left;padding-left:8px;\">".
        "<a href=\"$lien_ImportQti\" class='thickbox' title ='Importer une archive ZIP au format QTI 1.2 ou 2.0'".
        " name='Import de QTI 1.2 ou 2.0'><div class='bouton_vert' ".
        "onMouseOver=\"\$(this).removeClass();\$(this).addClass('bouton_hover');\" ".
        "onMouseOut=\"\$(this).removeClass();\$(this).addClass('bouton_vert');\">".
        "Importer une archive QTI</div></a></div>";
}
echo aide_div("qcm",8,0,0,0)."</td></tr>";
if (isset($auteur) && $auteur > 0)
   $champ_search = "where qcm_auteur_no='$auteur'";
//$limit = "limit $debut_liste,".$_SESSION['qcm_pages'];
echo "<tr><td style=\" background-color: '#ffffff';\" colspan='2' width='100%'>".
         "<table cellpadding='2' cellspacing='2' border='0' width='100%'>";
//echo "<tr><td class='sous_titre' style=\"padding:5px;\">$mmsg_menu_qcm</td></tr>";
echo "<tr><td width='100%'>";
echo "<div id='content_qcm'><table cellpadding='4' cellspacing='1' border='0' width='100%'>\n";
if (isset($_POST['passerelle']) && $_POST['passerelle'] == 1)
{
    $req=mysql_query("SELECT ress_cdn from ressource_new where ress_cat_lb = '".$_POST['dom']."'");
    $res_req=mysql_result($req,0);
    $linker = "qcm.php?code=".$_POST['ordre_code'];
    $lien = urlencode("recherche.php?flg=1&doublon=$doublon&rep=$linker&lien_sous_cat=0&parente=$res_req&ajouter=1&code=$ordre_code&org=qcm&id_seq=$id_seq&id_parc=$id_parc&consult=1");
    echo "<script language=\"JavaScript\">";
    echo " document.location.replace(\"trace.php?link=$lien\")";
    echo "</script>";
    exit;
}
if (isset($ressource) && $ressource == 1)
{
      echo "<FORM name='form' action='menu_qcm.php' method='POST'>";
      echo"<INPUT TYPE=HIDDEN name='passerelle' value=1>";
      echo"<INPUT TYPE=HIDDEN name='ordre_code' value= $code>";
      echo"<TR><TD colspan='2' valign='top'><table><tr><td><B>$mess_cqcm_cat</B></td>";
      $req_mod=mysql_query("select distinct ress_cat_lb from ressource_new order by ress_cat_lb asc");
      $res_mod=mysql_num_rows($req_mod);
      $mm=0;
      echo"<td><SELECT name='dom'>";
      while($mm < $res_mod)
      {
        $dom=mysql_result($req_mod,$mm,"ress_cat_lb");
        echo "<OPTION>$dom</OPTION>";
        $mm++;
      }
      echo "</SELECT>";
      echo "<A HREF=\"javascript:document.form.submit();\" ".
           "onmouseover=\"img1.src='images/fiche_identite/boutvalidb$suffixer.gif';return true;\" ".
           "onmouseout=\"img1.src='images/fiche_identite/boutvalid$suffixer.gif'\">".
           "<IMG NAME=\"img1\" SRC=\"images/fiche_identite/boutvalid$suffixer.gif\" BORDER='0' ".
           "onLoad=\"tempImg=new Image(0,0); tempImg.src='images/fiche_identite/boutvalidb$suffixer.gif'\"></A>";
      echo "</td></tr></FORM></td></tr></table></td></tr></table>";
      exit;
}
$req_mod = mysql_query("SELECT * FROM qcm_param $champ_search order by titre_qcm asc");
$res_mod = mysql_num_rows($req_mod);
echo "<tr>";
echo "<td class='barre_titre' valign='top' width='250'>$mmsg_qcmDisp</td>";
echo "<td class='barre_titre' valign='top' nowrap>$qf_nq</td>";
echo "<td class='barre_titre' valign='top'>$mrc_aut</td>";
echo "<td class='barre_titre' align='center' valign='top'>$mess_modif_base</td>";
echo "<td class='barre_titre' align='center' valign='top'>$mess_ag_supp</td>";
echo "<td class='barre_titre' align='center' valign='top'>Indexer</td>";
echo "<td class='barre_titre' align='center' valign='top'><span title=\"Exporter ce QCM au format QTI-1.2 en package Scorm-1.2. ".
     "Vous pourrez ainsi l'importer comme séquence Scorm\">Export QTI-1.2</span></td>";
echo "<td class='barre_titre' align='center' valign='top'><span title=\"Exporter ce QCM au format QTI-2.0. ".
     "Vous pourrez ainsi l'importer dans une plateforme compatible\">Export QTI-2.0</span></td>";
echo "</tr>";
$mm=0;
while ($mm < $res_mod)
{
          $titre = mysql_result($req_mod,$mm,"titre_qcm");
          $ordre = mysql_result($req_mod,$mm,"ordre");
          $auteur = mysql_result($req_mod,$mm,"qcm_auteur_no");
          $n_pages = mysql_result($req_mod,$mm,"n_pages");
          $nom_auteur = GetDataField ($connect,"select util_nom_lb from utilisateur where util_cdn='$auteur'","util_nom_lb");
          $prenom_auteur = GetDataField ($connect,"select util_prenom_lb from utilisateur where util_cdn='$auteur'","util_prenom_lb");
          $verif = mysql_result(mysql_query("select count(*) from qcm_linker where qcmlinker_param_no = $ordre"),0);
          if ($verif > 0)
          {
             $compteur++;
             echo couleur_tr($compteur,'');
             $ajoutSpan = "<span style='color:red;'>***</span>";
             $marqueur = (isset($_GET['code']) && $_GET['code'] == $ordre) ? $ajoutSpan : '';
             echo "<td width='30%' valign='middle' align='left'>".
                  "<a href=\"trace.php?link=".urlencode("qcm.php?code=$ordre")."\" target='_blank' ".
                  bulle($mmsg_qcmOpn,"","CENTER","ABOVE",90).$marqueur.$titre.$marqueur."</a></td>";
             $nbrpg = ($n_pages > 1) ? "questions" : "question";
             echo "<td width='12%'>$verif $nbrpg</td>";
             echo "<td width='15%'><a href=\"trace.php?link=".urlencode("menu_qcm.php?auteur=$auteur")."\" ".
                  bulle ("$mmsg_qcmAffAut $nom_auteur $prenom_auteur","","CENTER","ABOVE",140)."$nom_auteur $prenom_auteur</a></td>";
             if ($typ_user == 'ADMINISTRATEUR' || $auteur == $id_user)
             {
                $lien = "qcm_create.php?action=modifier&idParam=$ordre&keepThis=true&TB_iframe=true&height=700&width=960";
                $lien = urlencode($lien);
                echo "<td width='10%' align='center'><a href=\"trace.php?link=$lien\" class='thickbox' ".
                     "title=\"$mess_modif_base\" name = \"$mess_modif_base le QCM de titre originel : ".NewHtmlentities($titre,ENT_QUOTES)." \">".
                     "<IMG SRC=\"images/repertoire/icoGrenomfich20.gif\" border=0></A></td>";
                $lien = "menu_qcm.php?suppression=1&auteur=$auteur&idParam=$ordre";
                $lien = urlencode($lien);
                $req_verif = mysql_result(mysql_query("SELECT count(activite.act_cdn) from activite,ressource_new where ressource_new.ress_url_lb like \"%qcm.php?code=$ordre%\" and activite.act_ress_no = ressource_new.ress_cdn"),0);
                if ($req_verif == 0)
                   echo "<td width='10%' align='center'><A href=\"javascript:void(0);\" onclick=\"javascript:confm('trace.php?link=$lien');\" ".
                            bulle($mmsg_qcmSupp,"","CENTER","ABOVE",120).
                            "<IMG SRC=\"images/messagerie/icopoubelressour.gif\" border='0'></A></td>";
                else
                   echo "<td align='center'><IMG SRC=\"images/repertoire/icoptiinterdit.gif\" border='0' title=\"".
                        "Ce Qcm est lié à $req_verif activité(s). Il ne peut être supprimé.\" style=\"cursor:help;\"></td>";
                $ress_verif = mysql_result(mysql_query("SELECT count(*) from ressource_new where ress_url_lb like \"%qcm.php?code=$ordre%\""),0);
                echo "<TD align='center'><A href=\"menu_qcm.php?ressource=1&code=$ordre\"";
                if ($ress_verif > 0)
                {
                   $ress_cat = mysql_result(mysql_query("SELECT ress_cat_lb from ressource_new where ress_url_lb like \"%qcm.php?code=$ordre%\""),0,"ress_cat_lb");
                   echo " title = \"Ce qcm est déjà inscrit dans l'index des ressources au moins dans la catégorie <span style='color:red;'>".NewHtmlentities($ress_cat,ENT_QUOTES)."</span>\"";
                   echo "><IMG SRC=\"images/disconnectRouge.gif\" border=0></A></TD>";
                }
                else
                   echo "><IMG SRC=\"images/disconnect.gif\" border=0></A></TD>";

             }
             elseif ($auteur != $id_user && $typ_user != "ADMINISTRATEUR")
             {
                 echo "<td>&nbsp;</td>";
                 echo "<TD align='center' valign='top'><A href=\"javascript:void(0);\" ".
                           bulle("$prenom_auteur $nom_auteur","","CENTER","ABOVE",100).
                          "<IMG SRC=\"images/repertoire/icoptiinterdit.gif\" BORDER=0></A></td><TD>&nbsp;</TD>";
              }
              else
                 echo "<TD>&nbsp;</TD><TD>&nbsp;</TD>";
              echo "<TD align='center' valign='top' width='12%'><A href=\"export_Qti.php?code=$ordre&version=12\" target='_blank' title=".
                   "\"Exporter ce QCM au format QTI-1.2 en package Scorm-1.2. ".
                   "Vous pourrez ainsi l'importer comme séquence Scorm\"> ".
                   "<IMG SRC=\"images/ecran-annonce/icoGgo.gif\" BORDER=0></A></td>";
              echo "<TD align='center' valign='top' width='12%'><A href=\"export_Qti.php?code=$ordre&version=20\" target='_blank' title=".
                   "\"Exporter ce QCM au format QTI-2.0 ".
                   "pour l'utiliser sur une plateforme compatible (Moodle)\"> ".
                   "<IMG SRC=\"images/ecran-annonce/icoGgo.gif\" BORDER=0></A></td>";

             $html .="</tr>";
          }
          elseif ($verif == 0)
          {
             $compteur++;
             echo couleur_tr($compteur,'');
             $ajoutSpan = "<span style='color:red;'>***</span>";
             $marqueur = (isset($_GET['code']) && $_GET['code'] == $ordre) ? $ajoutSpan : '';
             echo "<td width='30%' valign='middle'><a href=\"javascript:void(0\" ".
                  bulle('Ce Qcm ne comporte aucune question' ,"","CENTER","ABOVE",150).$marqueur.$titre.$marqueur."</a></td>";
             echo "<td width='12%'>Pas de questions</td>";
             echo "<td width='15%'><a href=\"trace.php?link=".urlencode("menu_qcm.php?auteur=$auteur")."\" ".
                  bulle ("$mmsg_qcmAffAut $nom_auteur $prenom_auteur","","CENTER","ABOVE",140)."$nom_auteur $prenom_auteur</a></td>";
             if ($typ_user == 'ADMINISTRATEUR' || $auteur == $id_user)
             {
                $lien = "qcm_create.php?action=modifier&idParam=$ordre&keepThis=true&TB_iframe=true&height=700&width=950";
                $lien = urlencode($lien);
                echo "<td width='10%' align='center'><a href=\"trace.php?link=$lien\" class='thickbox' ".
                     "title=\"$mess_modif_base\" name = \"$mess_modif_base le QCM de titre originel : ".NewHtmlentities($titre,ENT_QUOTES)." \">".
                     "<IMG SRC=\"images/repertoire/icoGrenomfich20.gif\" border=0></A></td>";
                $lien = "creation_qcm.php?modification=1&suppression=1&auteur=$auteur&intitule=$titre|$ordre";
                $lien = urlencode($lien);
                echo "<td width='10%' align='center'><A href=\"javascript:void(0);\" onclick=\"javascript:confm('trace.php?link=$lien');\" ".
                     bulle($mmsg_qcmSupp,"","CENTER","ABOVE",120).
                     "<IMG SRC=\"images/messagerie/icopoubelressour.gif\" border='0'></A></td>";
                 echo "<TD>&nbsp;</TD><TD>&nbsp;</TD><TD>&nbsp;</TD>";
             }
             else
                 echo "<TD>&nbsp;</TD><TD>&nbsp;</TD><TD>&nbsp;</TD><TD>&nbsp;</TD><TD>&nbsp;</TD>";
             $html .="</tr>";
          }
    $mm++;
}
echo "</table></div></td></tr></table>";
echo fin_tableau('');
echo $html.'<div id="mien" class="cms"></div></body></html>';
?>