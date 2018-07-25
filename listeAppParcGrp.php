<?php
session_start();
if (!isset ($_SESSION['id_user']) || $_SESSION['id_user'] == "")
{
        exit();
}
include ("include/UrlParam2PhpVar.inc.php");
require 'admin.inc.php';
require 'fonction.inc.php';
require "lang$lg.inc.php";
require 'fonction_html.inc.php';
dbConnect();
function jour_mois($mois, $an)
{
        return date("t", mktime(0, 0, 0, $mois, 1, $an));
}
if ($lg == "fr")
        setlocale(LC_TIME, 'fr_FR');
elseif($lg == "ru")
  setlocale(LC_TIME, 'ru_RU');
$date_op = date("Y-m-d H:i:s", time());
$heure_fiche = substr($date_op, 11);
$date_fiche = substr($date_op, 0, 10);
$today = date("d/n/Y");
include 'style.inc.php';
if (!empty ($_POST))
{
    include ("include/AppParcGrpClass.php");
}
if (isset($mess_notif) && $mess_notif != '')
{
        echo '<script language="JavaScript">setTimeout(function() {$("#patience").css("display","none");},7000);</script>';
        echo notifier($mess_notif);
}
if (isset($action) && $action == 'ajouter')
    $le_message = "Procéder à l\'ajout";
elseif (isset($action) && $action == 'supprimer')
    $le_message = $mess_admin_valid_supp;
?>
<script language="JavaScript">
   $(document).ready(function(){
     $('#patience').css('display','none');
   });
function checkAllStudent(formulaire, cNom, etat)
{
     for (i=0,n=formulaire.elements.length;i<n;i++)
          if (formulaire.elements[i].className.indexOf(cNom) !=-1)
          {
                formulaire.elements[i].checked = !formulaire.elements[i].checked;
          }
}
function checkAll(formulaire, cNom, etat)
{
     for (i=0,n=formulaire.elements.length;i<n;i++)
          if (formulaire.elements[i].className.indexOf(cNom) !=-1)
          {
                formulaire.elements[i].checked = etat;
          }
}

function Uncheck(formulaire, cNom, etat)
{
     for (i=0,n=formulaire.elements.length;i<n;i++)
     {
          if (formulaire.elements[i].className.indexOf(cNom) !=-1)
          {
                formulaire.elements[i].checked = etat;
          }
     }

}
// Gestion du formulaire de confirmation de la suppression ou de l'ajout de modules et de sequences par lot
function confAlert(url) {
   ShowAlert2('JavaScript:document.'+url+'.submit()',
              'Confirmation',
              '<?php  echo "$le_message <br />$mess_op_irrev";?>',
              '<?php echo $adresse_http;?>/images/Exclamation.gif',
              ['Confirmer', 'Annuler'],
              ['JavaScript:HideAlert2(1,"'+url+'")','JavaScript:HideAlert2(2,"'+url+'")'],
              400,
              '<?php echo $adresse_http;?>/images/close.gif',
              url
              );
}
</script>
<?php

// Principal
echo "<FORM name='formule' id='formule' action=\"#\" method='post'>";
echo "<div style='width:500px;margin:0 0 10px 12px;'><TABLE bgColor='#298CA0' cellspacing='2' cellpadding='0' width='98%'><TR>";
echo "<TD valign='top'><TABLE bgColor='#FFFFFF' cellspacing='0' border=0 cellpadding='0' width='100%'>".
     "<TR><TD valign='top' width='100%'><TABLE cellspacing='1' border=0 cellpadding='0' width='100%'>".
     "<TR><TD background=\"images/fond_titre_table.jpg\" colspan='2' height='40' align='left' valign='center'>".
     "<Font size='3' color='#FFFFFF'><B>".
     "&nbsp;1- Sélectionnez les apprenants concernés</FONT></b></TD></TR>".
     "<TR><TD valign='top'><TABLE width=100% bgColor='#FFFFFF' cellspacing='0' cellpadding='4' border='0'><TR><TD valign='top'>";
$envoi=array();
$util=array();
$rqList = "select * from utilisateur,utilisateur_groupe where ".
                  "utilisateur_groupe.utilgr_utilisateur_no=utilisateur.util_cdn AND ".
                  "utilisateur_groupe.utilgr_groupe_no = '".$id_grp."' ".
                  "group by util_cdn order by utilisateur.util_nom_lb";
$rsList = mysql_query($rqList);
$nombre = mysql_num_rows($rsList);
$compteur=0;
if ($nombre > 0)
{

     $content .= '<div style="margin:8px;padding:6px 0 6px 2px;width:500px;max-height:250px;'.
                 'overflow:auto;align:left;border:1px solid #298CA0;">';
     while ($item = mysql_fetch_object($rsList))
      {
                      $id_util = $item->util_cdn;
                      $nom_user=$item->util_nom_lb;
                      $prenom_user=$item->util_prenom_lb;
                      $majuscule = "$nom_user $prenom_user";
                      $id_photo = $item->util_photo_lb;
                      $content .= "<div style='clear:both;float:left;margin-left:30px;'>";
                      $content .= "<INPUT TYPE='hidden' name='util[$compteur]' value='$id_util' />";
                      $ajout_box = "<INPUT TYPE='checkbox' name='envoi[$compteur]' id='env$compteur' class='apprenant' style='cursor:pointer;' ".
                                   bullet("Cochez pour sélectionner ou désélectionner ce(t) ".strtolower($mess_typ_app), "", "RIGHT", "ABOVE", 240)." />";
                      $content .= "<div style='float:left;min-width:220px;width:auto;' id='affCoul$compteur'>";
                      if ($id_photo == '')
                         $content .= "\n$ajout_box<A HREF=\"javascript:void(0);\" style ='cursor:default !important;'>$majuscule</A>";
                      else
                      {
                         list($w_img, $h_img, $type_img, $attr_img) = getimagesize("images/$id_photo");
                         $content .= "\n$ajout_box<A  HREF=\"javascript:void(0);\" style ='cursor:default !important;' ".
                              "onMouseOver=\"overlib('', TEXTCOLOR, '#ffffff', TEXTSIZE, 2, WIDTH, $w_img, HEIGHT, $h_img, BACKGROUND, ".
                              "'images/$id_photo', PADX, 60, 20, PADY, 20, 20,DELAY,500);\" ".
                              "onMouseOut=\"nd();\">$majuscule</A>";
                      }
                    $content .= "</div></div>";
                $compteur++;
       }
       $content .= "<INPUT TYPE='hidden' name='compteApp' value='$compteur' />";
       $content .= "</div></TD></TR></TABLE></TD></TR></TABLE></TD></TR></TABLE></TD></TR></TABLE>";
       $content .= '</div>';
       $content2 = '<div style="margin-left:12px;">'.$bouton_gauche.'<A href="javascript:void(0);" '.
                   'onClick="checkAllStudent(document.getElementById(\'formule\'), \'apprenant\', this.checked);" '.
                   'title="Cliquez sur ce bouton pour sélectionner ou déselectionner  tous les '.strtolower($mes_des_app).'">'.
                   $mess_InvSlct.'</A>'.$bouton_droite.' </div>';
       $content1 = '<div style="margin-left:12px;cursor:pointer;">'.$bouton_gauche1.'<input type="checkbox" name="inverser" id="inverser" '.
                   'onClick="checkAll(document.getElementById(\'formule\'), \'apprenant\', this.checked);" style="cursor:pointer;" '.
                   bullet("Cochez cette case pour sélectionner ou désélectionner tous les ".
                   strtolower($mes_des_app), "", "RIGHT", "BELOW", 240) .' />'.
                   '<span style="font-weight:bold;padding:0 6px 4px 6px;">'.
                   'Sélection / Désélection</span>'.$bouton_droite1.' </div>';
}
echo $content1.$content;
if ($action == 'ajouter')
{
   echo "<div style='clear:both;min-height:20px;height:30px;margin:0 0 10px 0; padding-left:4px;' >".
        "<div style='clear:both;float:left;padding:8px 0 0 12px;height:22px;cursor:pointer;' id='mod'>".
        "<input type='checkbox' name='repare' id='modifier' /></div>".
        "<div id='modif' style='float:left;padding:10px 4px 0 5px;font-size:11px;font-family:arial;font-weight:bold;height:20px;cursor:pointer;'>".
        "<span style='color:#D45211;'>".
        "Tous les apprenants sélectionnés héritent des modifications de dates et formateurs et quelque soit leur prescription précédente. </span>".
        "</div></div>";
  ?>
  <script type="text/javascript">
   $(document).ready(function(){
     $(document).delegate('#modifier','change',function()
     {
        if($("input[name='repare']:checked").val() == 'on')
        {
           $('#modif').css('background','#D4E7ED');
           $('#mod').css('background','#D4E7ED');
        }
        else
        {
           $('#modif').css('background','#FFFFFF');
           $('#mod').css('background','#FFFFFF');
        }
     });
     $(document).delegate('#modif','click',function()
     {
        if($("input[name='repare']:checked").val() == 'on')
        {
           $("input[name='repare']").check('off');
           $('#modif').css('background','#FFFFFF');
           $('#mod').css('background','#FFFFFF');
        }
        else
        {
           $("input[name='repare']").check('on');
           $('#modif').css('background','#D4E7ED');
           $('#mod').css('background','#D4E7ED');
        }
     });
   });
  </script>

  <?php
}
if ($action == 'ajouter')
{
   echo "<div style='clear:both;min-height:20px;height:30px;margin:0 0 10px 0; padding-left:4px;' >".
        "<div style='clear:both;float:left;padding:8px 0 0 12px;height:22px;cursor:pointer;' id='modAct'>".
        "<input type='checkbox' name='repareAct' id='modifierAct' /></div>".
        "<div id='modifAct' style='float:left;padding:10px 4px 0 5px;font-size:11px;font-family:arial;".
        "font-weight:bold;height:20px;cursor:pointer;'>".
        "<span style='color:#D45211;'>".
        "Ajoute les nouvelles activités aux apprenants sélectionnés qui ont déjà validé leurs séquences.</span>".
        "</div></div>";
   ?>
   <script type="text/javascript">
   $(document).ready(function(){
        jQuery.fn.check =  function() {
             return this.each(function() {
                this.checked = !this.checked;
            });
        };
     $(document).delegate('#modifierAct','change',function()
     {
        if($("input[name='repareAct']:checked").val() == 'on')
        {
           $('#modifAct').css('background','#D4E7ED');
           $('#modAct').css('background','#D4E7ED');
        }
        else
        {
           $('#modifAct').css('background','#FFFFFF');
           $('#modAct').css('background','#FFFFFF');
        }
     });
     $(document).delegate('#modifAct','click',function()
     {
        if($("input[name='repareAct']:checked").val() == 'on')
        {
           $("input[name='repareAct']").check('off');
           $('#modifAct').css('background','#FFFFFF');
           $('#modAct').css('background','#FFFFFF');
        }
        else
        {
           $("input[name='repareAct']").check('on');
           $('#modifAct').css('background','#D4E7ED');
           $('#modAct').css('background','#D4E7ED');
        }
     });
   });
   </script>
   <?php
}
echo "<div style='clear:both;width:100%;'><CENTER><TABLE bgColor='#298CA0' cellspacing='2' cellpadding='0' width='98%'><TR>";
echo "<TD valign='top'><TABLE bgColor='#FFFFFF' cellspacing='0' border=0 cellpadding='0' width='100%' height='100%'>".
     "<TR><TD valign='top' width='100%' height='100%'>";
$sequence[] = array ();
$nom_grp = GetDataField($connect, "select grp_nom_lb from groupe where grp_cdn  = $id_grp", "grp_nom_lb");
$resp_grp = GetDataField($connect, "select grp_resp_no from groupe where grp_cdn  = $id_grp", "grp_resp_no");
$parc_query = mysql_query("select * from parcours,groupe_parcours where
                               groupe_parcours.gp_grp_no = $id_grp AND
                               parcours.parcours_cdn = groupe_parcours.gp_parc_no
                               order by groupe_parcours.gp_ordre_no asc");
$nb_parc = mysql_num_rows($parc_query);
$num_pg = array ();
$i_parc = $nb_parc;
echo "<TABLE cellspacing='1' border=0 cellpadding='0' width='100%'  height='100%'>";
$titre = "&nbsp;2- Individualisation pour les apprenants sélectionnés de la formartion  $nom_grp";
echo "<TR><TD background=\"images/fond_titre_table.jpg\" colspan='2' height='40' align='left' valign='center'><B>".
     "<Font size='3' color='#FFFFFF'>$titre</FONT></b></TD></TR>";
//********************************
$bgcolorB = '#2B677A';
$bgcolorA = '#FFEDD9';
echo "<TR><TD valign='top' style=\"height:100%;\"><TABLE width=100% bgColor='#FFFFFF' cellspacing='0' cellpadding='4' border='0'>";
echo "<INPUT TYPE='HIDDEN' NAME='modifier' VALUE='1'>";
echo "<TR height='25'>";
echo "<TD class='barre_titre'>$mess_gp_parc_appr</TD>";
echo "<TD class='barre_titre' style=\"text-align :left;\">$mess_gp_duree</TD>";
echo "<TD class='barre_titre'>$msq_formateur</TD>";
echo "<TD class='barre_titre'>$mess_gp_date_deb</TD>";
echo "<TD class='barre_titre'>$mess_gp_date_fin</TD>";
echo "</TR>";
$p = 0;
$debut_parc = array ();
$date_f = array ();
$fin_parc = array ();
$formateur_parc = array ();
$num_parc = array ();
while ($p < $nb_parc)
{
        $id_parc = mysql_result($parc_query, $p, "gp_parc_no");
        $debut_parc[$id_parc] = mysql_result($parc_query, $p, "gp_db_dt");
        $fin_parc[$id_parc] = mysql_result($parc_query, $p, "gp_df_dt");
        $date_f[$p] = $fin_parc[$id_parc];
        $num_parc[$p] = $id_parc;
        $formateur_parc[$id_parc] = mysql_result($parc_query, $p, "gp_formateur_no");
        $nom = mysql_result($parc_query, $p, "parcours_nom_lb");
        echo "<TR bgcolor=#e9e9e9>";
        echo "<TD align='left'><div style ='clear:both;float:left;margin-right:40px;'>";
        echo $bouton_gauche1.'<input type="checkbox" name="pere'.$p.'_p" id="pere'.$p.'_p" class="pere'.$p.'_p" '.
                      bullet("Cochez cette case pour sélectionner ou ".
                      "désélectionner la totalité des ".strtolower($msq_seq)."(s) de ce ".
                      strtolower($msq_parc), "", "RIGHT", "ABOVE", 240).'" '.
                      'onClick="checkAll(document.getElementById(\'formule\'), \'parcours'.$p.'_p\', this.checked);" />'.
                      '<span style="padding:5px;font-weight:bold;">'.ucfirst($action).' ce '.
                      strtolower($msq_parc).' </span>'.$bouton_droite1.'</div>';
        echo "<div style ='float:left;padding-top:4px;'><a href=\"javascript:void(0);\" style ='cursor:default !important;'>";
        echo "$nom</A></div></TD>";
        $seq_parc_query = mysql_query("select sum(sequence.seq_duree_nb) from sequence,sequence_parcours
                                       where sequence.seq_cdn = sequence_parcours.seqparc_seq_no AND
                                             sequence_parcours.seqparc_parc_no = $id_parc");
        $duree_parc = mysql_result($seq_parc_query, 0);
        $duree_parc = duree_calc($duree_parc);
        $parc_duree = ($duree_parc == '0h') ? "-" : $duree_parc;
        echo "<TD nowrap style=\"background-color:#e9e9e9; font-size:12px;font-family: arial;".
       "font-weight:bold; text-align: left;\">$parc_duree</TD><TD>";
        $form_mod_nom = GetDataField($connect, "SELECT util_nom_lb from utilisateur where
                                         util_cdn='$formateur_parc[$id_parc]'", "util_nom_lb");
        $form_mod_prenom = GetDataField($connect, "SELECT util_prenom_lb from utilisateur where
                                            util_cdn='$formateur_parc[$id_parc]'", "util_prenom_lb");
        $param = $formateur_parc[$id_parc];
        Ascenseur_mult_tot("form_ref[$p]", "select util_cdn,util_nom_lb,util_prenom_lb from utilisateur where
                                 (util_typutil_lb = 'FORMATEUR_REFERENT' or
                                 util_typutil_lb = 'RESPONSABLE_FORMATION' or
                                 util_typutil_lb = 'ADMINISTRATEUR') AND
                                 util_flag = 0
                                 order by util_nom_lb ASC", $connect, $param);
        $ch_date_deb = explode("-", $debut_parc[$id_parc]);
        $date_min = "$ch_date_deb[2]/$ch_date_deb[1]/$ch_date_deb[0]";
        $ch_date_fin = explode("-", $fin_parc[$id_parc]);
        $date_max = "$ch_date_fin[2]/$ch_date_fin[1]/$ch_date_fin[0]";
        echo "</TD><TD><INPUT TYPE=TEXT class='INPUT' NAME=date_debut[$p] value= '$date_min' MAXLENGTH='10' ".
       "size='10' onfocus=\"this.select();lcs(this)\" onclick=\"event.cancelBubble=true;this.select();lcs(this)\"></TD>";
        echo "<INPUT TYPE='HIDDEN' NAME='module[$p]' VALUE='$id_parc'>";
        echo "<TD align='left'><INPUT TYPE= TEXT class='INPUT' NAME=date_finale[$p] value= '$date_max' MAXLENGTH='10' ".
       "size='10' onfocus=\"this.select();lcs(this)\" onclick=\"event.cancelBubble=true;this.select();lcs(this)\"></TD>";
        echo "</TR><TR>";
        echo "<TD colspan='2'>";
        //Sequences a faire dans le parcours déroulé
        $ouvrir = 'parcours';
        $parc_ouvert = $id_parc;
        $grp_parc = mysql_result(mysql_query("select count(*) from utilisateur_groupe where utilgr_groupe_no=$id_grp"),0);
        $seq_query = mysql_query("SELECT * FROM sequence_parcours, sequence WHERE
                                           seqparc_parc_no = '$id_parc' AND
                                           seqparc_seq_no = seq_cdn group by seq_cdn
                                           ORDER BY seqparc_ordre_no ASC");
        $nb_seq = mysql_num_rows($seq_query);
        echo "<TABLE width='100%' cellpadding=0 cellspacing=0>";
        echo "<TR bgcolor=#F4F4F4>";
        echo "<TD align='left'><b>$mess_gp_seq_parc</b></TD>";
        echo "<TD style=\"background-color:#EFEFEF; height:20px; width:70px;font-size: 12px;".
       "font-family: arial;font-weight:bold; text-align: center;\">$mess_gp_duree</TD>";
        echo "</TR>";
        $i = 0;
        $num_seq = array ();
        $ordre_s = array ();
        while ($i != $nb_seq)
        {
                $duree = 0;
                $seq = mysql_result($seq_query, $i, "seq_cdn");
                $desc_seq = mysql_result($seq_query, $i, "seq_desc_cmt");
                $duree_seq = mysql_result($seq_query, $i, "seq_duree_nb");
                $nom_seq = mysql_result($seq_query, $i, "seq_titre_lb");
                $type_seq = mysql_result($seq_query, $i, "seq_type_lb");
                $comb = mysql_result($seq_query, $i, "seq_type_on");
                $ordre_seq = mysql_result($seq_query, $i, "seqparc_ordre_no");
                if ($comb == 1)
                {
                        $i++;
                        continue;
                }
                //***************************************************************************************************
                $logo_sco = "<input type='checkbox' name='sequence[$p][$i]' id='sequence[$p][$i]' class=\"parcours$p"."_p\" ".
                " onChange=\"Uncheck(document.getElementById('formule'), 'pere$p"."_p', this.unchecked);\" ".
                bullet("Cochez cette case pour sélectionner ou désélectionner cette ".strtolower($msq_seq), "", "RIGHT", "ABOVE", 240)."' />";
                echo "<INPUT TYPE='HIDDEN' NAME='num_seq[$p][$i]' VALUE='$seq'>";
                if (strstr($type_seq, "SCORM"))
                        $logo_sco .= "<IMG SRC='images/gest_parc/scorm.gif' border='0' alt=\"$mess_seq_sco\">";
                else
                        $logo_sco .= " ";
                echo couleur_tr($i,'15')."<TD style='font-family: arial;font-size:11px;'>$logo_sco $nom_seq</TD>";
                echo "<INPUT TYPE='HIDDEN' NAME='duree_seq[$p][$i]' VALUE='$duree_seq'>";
                if ($duree_seq > 0)
                        $duree_seq = duree_calc($duree_seq);
                $laduree = ($duree_seq == 0) ? "-" : $duree_seq;
                echo "<TD align='center' style=\"background-color:#EFEFEF; width:70px;font-size: 12px;font-family: arial; text-align: center;\">".
         "$laduree</td>";
                echo "</TR>";
                $i++;
        } //  fin while ($i != $nb_seq)
        echo '</div></TABLE></TD></TR>';
        $n=$i-1;
        echo "<INPUT TYPE='HIDDEN' NAME='i[$p]' VALUE='$n'>";
        $p++;
}
if ($nb_parc > 0)
{
        echo "<INPUT TYPE='HIDDEN' NAME='id_grp' VALUE='$id_grp' />";
        echo "<INPUT TYPE='HIDDEN' NAME='p' VALUE='$p' />";
        echo "<INPUT TYPE='HIDDEN' NAME='action' VALUE='".$action."' />";
        echo "</TD></TR><TR bgcolor='#FFFFFF'><TD colspan='6' width='100%' align='right' style=\"padding-right:10px;\">";
}
        echo "<A href=\"#\" onclick=\"javascript:setTimeout(function() {\$('#patience').css('display','block');},7000);".
             "return(confAlert('formule'));\" TITLE=\"$mess_gen_valider\" ";
        echo " onmouseover=\"img2.src='images/fiche_identite/boutvalidb.gif';return true;\" ".
             "onmouseout=\"img2.src='images/fiche_identite/boutvalid.gif'\">";
        echo "<IMG NAME=\"img2\" SRC=\"images/fiche_identite/boutvalid.gif\" BORDER='0' ".
             "onLoad=\"tempImg=new Image(0,0); tempImg.src='images/fiche_identite/boutvalidb.gif'\"></A>";
echo "</TD></TR></TABLE></TD></TR></TABLE></TD></TR></TABLE></div></form>";

echo '<div id="patience" style="padding:4px;top:40px;left:550px;border:2px solid red;'.
     'position:absolute;text-align:center;font-size:14px;font-family:arial,tahoma;font-weight:bold;">'.
     'Cette opération nécessite un certain temps pour son traitement<br />'.
     'Veuillez patienter jusqu\'au rechargement de la page...Merci<br />'.
     '<img src="images/timer.gif" border="0" style="margin:15px;"></div>';
?>
<DIV ID=Main>
     <!-- A Separate Layer for the Calendar -->
     <!-- Make sure to use the name Calendar for this layer -->
     <SCRIPT Language="Javascript" TYPE="text/javascript">
             Calendar.CreateCalendarLayer(10, 275, "");
     </SCRIPT>
</DIV>