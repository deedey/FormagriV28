<?php
if (!isset($_SESSION)) session_start();
include ("include/UrlParam2PhpVar.inc.php");
if (!isset($_SESSION['id_user']) || $_SESSION['id_user'] == "")
{
  exit();
}
require 'graphique/admin.inc.php';
require 'fonction.inc.php';
require "lang$lg.inc.php";
require 'fonction_html.inc.php';
require 'class/class_admin.php';
require 'langues/notif.inc.php';
require 'langues/adm.inc.php';
//include ("click_droit.txt");
dbConnect();
setlocale(LC_TIME,'fr_FR');
$agent=$_SERVER["HTTP_USER_AGENT"];
if (strstr(strtolower($agent),"mac") || strstr(strtolower($agent),"konqueror") || strstr(strtolower($agent),"safari"))
    $mac=1;
if (strstr(strtolower($agent),"win"))
    $win=1;
if (isset($save_sql) && $save_sql == 1)
{
  $date_en_cours = date("Y-n-d");
  $ch_date = explode("-",$date_en_cours);
  $date_save = "$ch_date[2]_$ch_date[1]_$ch_date[0]";
  require("class/MySQLDump.php");
  $dumper = new MySQLDump($bdd);
  $dumper->writeDump();
  include_once("class/archive.inc.php");
  $fichier = "ressources/$login"."_".$id_user."/ressources/bdd_formagri";
  $mon_zip = new zip_file("../bdd_formagri.zip");
  $mon_zip->set_options(array('basedir'=>$fichier));
  $handle=opendir($fichier);
  while ($fiche = readdir($handle))
  {
       if ($fiche != '.' && $fiche != '..')
          $mon_zip->add_files($fiche);
  }
  $mon_zip->create_archive();
  closedir($handle);
  $dir = "ressources/".$login."_".$id_user."/ressources/bdd_formagri";
  if (file_exists($dir))
     viredir($dir,$s_exp);
  //rmdir($dir);
  $file ="ressources/".$login."_".$id_user."/ressources/bdd_formagri.zip";
  ForceFileDownload($file,'binary');
  exit();
}
if (isset($bprea) && $bprea == 1 && (!isset($insertion) || (isset($insertion) && $insertion == 0)))
{
    include 'style.inc.php';
    $nom_user=GetDataField ($connect,"select util_nom_lb from utilisateur WHERE util_cdn = '$num'","util_nom_lb");
    $prenom_user=GetDataField ($connect,"select util_prenom_lb from utilisateur WHERE util_cdn = '$num'","util_prenom_lb");
    $majuscule = $prenom_user." ".$nom_user;
    $titre = $mess_insc_mp." ". $majuscule;
    entete_simple($titre);
    echo "<TR><TD><TABLE cellpadding=6 bgColor='#FFFFFF' width='80%'>";
    $requete= mysql_query ("SELECT uc_centre_lb FROM user_centre WHERE uc_iduser_no = $num");
    $nb_centre = mysql_num_rows ($requete);
    echo "<tr><td  height='20' colspan='2'align='left' valign='top'>&nbsp;<TD></TR>";
    if ($nb_centre > 0)
    {
      echo "<tr><td  height='40' align='left' valign='top'>$majuscule $mess_insc_cmpl : </TD><TD>";
      $i = 0;
      while ($i < $nb_centre)
      {
            $centre = mysql_result($requete,$i,"uc_centre_lb");
            echo "<LI><B>$centre</B></LI>";
            $i++;
      }
    }
    ?>
    <SCRIPT language=JavaScript>
    function checkForm(frm) {
     var ErrMsg = "<?php echo $mess_info_no;?>\n";
     var lenInit = ErrMsg.length;
     if (isEmpty(frm.lms)==true)
        ErrMsg += ' - Centre\n';
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
    echo "<center><FORM name='form' action=\"admin_gere.php?\" method=\"POST\">";
    echo "<tr><td height='20' align='left'>$mess_new_lms</td>";
    echo "<td  height='20' align='left'><INPUT type='text' class='INPUT' name='lms' size='40'></td>".
         "<td style=\"color: #D45211;\">$mess_new_lms_hlp</td>";
    echo "<INPUT type='hidden' name='majuscule' value=\"$majuscule\">";
    echo "<INPUT type='hidden' name='bprea' value='1'>";
    echo "<INPUT type='hidden' name='id_grp' value='$id_grp'>";
    echo "<INPUT type='hidden' name='insertion' value='1'>";
    echo "<INPUT type='hidden' name='num' value='$num'><BR>";
    echo "</TR></FORM>";
    echo "<tr><td  height='20' align='left' valign='top'>&nbsp;<TD></TR>";
    echo boutret(1,0);
    echo "</TD><TD align='center'><A HREF=\"javascript:checkForm(document.form);\" onmouseover=\"img1.src='images/fiche_identite/boutvalidb.gif';return true;\" onmouseout=\"img1.src='images/fiche_identite/boutvalid.gif'\">".
         "<IMG NAME=\"img1\" SRC=\"images/fiche_identite/boutvalid.gif\" BORDER='0' onLoad=\"tempImg=new Image(0,0); tempImg.src='images/fiche_identite/boutvalidb.gif'\"></A>";
    echo "</TD></TR></TABLE>";
    echo fin_tableau('');
  exit;
}
if (isset($bprea) && $bprea == 1 && isset($insertion) && $insertion == 1)
{
   include 'style.inc.php';
   $requete= mysql_query ("SELECT uc_centre_lb FROM user_centre WHERE uc_iduser_no = $num");
   $nb_centre = mysql_num_rows ($requete);
   if ($nb_centre > 0)
   {
      $i = 0;
      while ($i < $nb_centre)
      {
            $centre = mysql_result($requete,$i,"uc_centre_lb");
            if ($centre == $lms)
            {
               $titre = "$mess_insc_titre $de $majuscule";
               entete_simple($titre);
               echo "<TABLE cellpadding=6 bgColor='#CEE6EC' width='100%'>";
               echo "<TR><TD align='center' colspan='2' height='60'> $majuscule $mess_insc_cmpl : <Font color='red'><B>$lms</B></FONT></TD></TR>";
               echo boutret(2,0);
               echo "</TD></TR></TABLE>";
               echo fin_tableau('');
             exit();
            }
      $i++;
      }
   }
  // requete pour voir si la plateforme d'appel est déjà intégrée. Si NON on l'intègre dans la liste des plate-formes autorisées
  $lms_origin = $bdd;
  if (strstr($lms_origin,"ef-"))
     $lms_origin = substr($bdd,3);
  $nb_req = mysql_result(mysql_query("SELECT count(*) FROM user_centre WHERE uc_centre_lb like \"%$lms_origin%\" AND uc_iduser_no = $num"),0);
  if ($nb_req == 0)
  {
     $id_max = Donne_ID ($connect,"select max(uc_cdn) from user_centre");
     $requete = mysql_query ("INSERT INTO user_centre values($id_max,$num,\"$lms_origin\")");
  }
  $id_max = Donne_ID ($connect,"select max(uc_cdn) from user_centre");

  $requete = mysql_query ("INSERT INTO user_centre values($id_max,$num,\"$lms\")");
  $req_util = mysql_query("select * from utilisateur where util_cdn = $num");
  $id_util = mysql_result($req_util,0,"util_cdn");
  $nom = mysql_result($req_util,0,"util_nom_lb");
  $prenom = mysql_result($req_util,0,"util_prenom_lb");
  $email = mysql_result($req_util,0,"util_email_lb");
  $logue = mysql_result($req_util,0,"util_login_lb");
  $type_user = mysql_result($req_util,0,"util_typutil_lb");
  $util_auteur = mysql_result($req_util,0,"util_auteur_no");
  $commentaire = mysql_result($req_util,0,"util_commentaire_cmt");
  $photo = mysql_result($req_util,0,"util_photo_lb");
  $passe = mysql_result($req_util,0,"util_motpasse_lb");
  $tel = mysql_result($req_util,0,"util_tel_lb");
  $webmail = mysql_result($req_util,0,"util_urlmail_lb");
  $requete = mysql_query ("INSERT INTO users values($id_util,\"$nom\",\"$prenom\",\"$photo\",\"$email\",\"$tel\",\"$webmail\",\"$type_user\",\"$logue\",\"$passe\",0,\"NON\",\"$commentaire\",$util_auteur)");
  $mess_notif = "$nom $prenom $ntf_adm_mc $lms";
  if ($type_user == "APPRENANT")
      $lien = "admin.php?annu=APPRENANT&ok=non&id_grp=$id_grp&mess_notif=$mess_notif";
  else
      $lien = "admin.php?annu=$type_user&mess_notif=$mess_notif";
  $lien = urlencode($lien);
  echo "<script language=\"JavaScript\">";
  echo "document.location.replace(\"trace.php?link=$lien\")";
  echo "</script>";
  exit;
}
if (isset($ecran) && $ecran == 1)
{
  $ecran=0;
  if ($typ_ecran == "n")
  {
    $remplace = mysql_query("update parametre set param_ecran='NORMAL' where param_user='$type'");
    $mess_notif = $msgadm_scNrm;
  }
  elseif ($typ_ecran == "m")
  {
    $remplace = mysql_query("update parametre set param_ecran='MEDIAN' where param_user='$type'");
    $mess_notif = $msgadm_scMed;
  }
  elseif ($typ_ecran == "p")
  {
    $remplace = mysql_query("update parametre set param_ecran='PLEIN' where param_user='$type'");
  }
  $type="";
  echo  stripslashes($mess_notif);
  exit();
}
include 'style.inc.php';
?>
<script type="text/javascript" src="OutilsJs/lib/ajax/ajax_cms.js"></script>
<div id="affiche" class="Status"></div>
<div id="mon_contenu" class="cms"
        onClick="javascript:$(document).ready(function() {if ($.browser.msie) {$('#mon_contenu').hide();}else{$('#mon_contenu').hide('slow');}})"
        <?php echo "title=\"$mess_clkF\">";?>
</div>
<?php
if (isset($interface) && $interface == 1)
{
    if (isset($mess_notif) && $mess_notif != '')
        echo notifier($mess_notif);
    $titre = $mess_admin_modif_forma;
    entete_simple($titre);
    echo "<TR><TD colspan=2 width='100%'><TABLE cellspacing='0'  cellpadding = '3' bgColor='#FFFFFF' border=0>";
    echo "<TR height='30'><TD align='left'>";
    $lien = "admin_gere.php?logo=1";
    $lien = urlencode($lien);
    echo"<a href=\"trace.php?link=$lien\" target='main'>$mess_admin_modif_logo</A></TD></TR>";
    echo "<TR height='30'><TD align='left'>";
    $lien = "admin_gere.php?index=1";
    $lien = urlencode($lien);
    echo"<a href=\"trace.php?link=$lien\" target='main'>$mess_admin_modif_img</A></TD></TR>";
    //dey Dfoad
       $style_devoirs = GetDataField($connect,"select param_etat_lb from param_foad where param_typ_lb='style_devoirs'","param_etat_lb");
       echo "<TR height='30'><TD align='left'>";
       echo"<a href=\"javascript:void(0);\" onClick=\"javascript:\$('#le_style').css('display','block');\">".
           "Changer la feuille de style de la consigne des activités</A>";
       echo "<div id='le_style' style='display:none;border:1px solid #24677A;width:450px;'>";
       ?>
     <div style="clear:both;font-size:10px;margin:4px;padding-top:3px;">
            <label for="lestyle"><span style="color:red;font-size:12px;">*</span>
            Votre feuille de style ne doit pas comporter d'extension <span style="color:#D45211;font-weight:bold;font-size:11px;"> .css</span><br />
            Si le fichier se nomme <span style="font-weight:bold;color:#D45211;font-size:11px;"> MesDevoirs.css</span>,
            saisissez seulement&nbsp; <span style="color:#D45211;font-weight:bold;font-size:11px;"> MesDevoirs</span><br />
            (il doit se trouver obligatoirement à la racine du répertoire "Ressources" )</label>
     </div>
     <div style="clear:both;float:left;margin:4px;">
            <input type="text" class="INPUT" id="lestyle" name="lestyle" value="<?php echo $style_devoirs;?>" />
     </div>
     <div style="float:left;margin:4px;">
         <input type="submit" value="Valider" onMouseDown="javascript:$.ajax({type: 'GET',
                                              url: 'admin/modif_nb.php',
                                              data: 'interface=1&lestyle='+escape($('#lestyle').val()),
                                              beforeSend:function()
                                              {
                                                 $('#affiche').addClass('Status');
                                                 $('#affiche').append('Opération en cours....');
                                              },
                                              success: function(msg){
                                                   $('#mien').css('padding','4px');
                                                   $('#mien').show();
                                                   $('#mien').html(msg);
                                                   $('#affiche').empty();
                                                   $('#le_style').css('display','none');
                                              }
                                        });
                                        setTimeout(function() {$('#mien').hide();},7000);" />
       </div>
       <?php
       echo "<div style='clear:both;width:80px;margin:6px;' class='sous_titre' id='simple'>".
            "<a href=\"javascript:void(0);\" onClick=\"javascript:\$('#le_style').css('display','none');\">".
           "Abandonner</A></div>";
       echo "</TD></TR>";
    // dey new
    $lien = "admin_gere.php?save_sql=1";
    echo "<TR height='30'><TD colspan=2>";
    echo "<A HREF=\"$lien\" target='main'>$mess_backup_bdd</A></TD></TR>";
    echo "<TR height='5'><TD colspan=2></td></tr>";
    $req = mysql_result(mysql_query("select count(*) from trace"),0);
    $firstDate = mysql_result(mysql_query("select min(trace_date_dt) from trace"),0);
    $ch_dt = explode("-",$firstDate);
    $laDate = strftime("%A %e %B %Y", mktime(0, 0, 0, $ch_dt[1],$ch_dt[2],$ch_dt[0]));
    $date_jour = date("Y-n-d");
    $nb_jours_req = mysql_query ("SELECT TO_DAYS('$date_jour')");
    $nb_jours_cour = mysql_result ($nb_jours_req,0);
    $nb_jours_supp = $nb_jours_cour-10;
    $new_nb = mysql_query ("SELECT FROM_DAYS('$nb_jours_supp')");
    $new_date = mysql_result($new_nb,0);
    $ch_ndt = explode("-",$new_date);
    $newDate = strftime("%A %e %B %Y", mktime(0, 0, 0, $ch_ndt[1],$ch_ndt[2],$ch_ndt[0]));
    if ($req > 1000)
    {
       echo "<TR height='30'><TD align='left' style='font-weight:bold;'>".
            "<div style='clear:both;float:left;padding-top:6px;'>La table de tracking compte <u>$req</u> enregistrements depuis le <u>$laDate</u>.".nbsp(3)."</div>";
       ?>
       <a href="javascript:void();"  onClick="javascript:$.ajax({type: 'GET',
                                              url: 'admin/modif_nb.php',
                                              data: 'interface=1&suppTrace=1',
                                              beforeSend:function()
                                              {
                                                 $('#affiche').addClass('Status');
                                                 $('#affiche').append('Opération en cours....');
                                              },
                                              success: function(msg){
                                                   $('#mien').css('padding','4px');
                                                   $('#mien').show();
                                                   $('#mien').html(msg);
                                                   $('#affiche').empty();
                                              }
                                        });
                                        setTimeout(function() {$('#mien').hide();},5000);">
       <?php
       echo "<div style='float:left;' class='sous_titre' ".
       bulle("Cliquez ici pour la réinitialiser à 10 jours, soit à partir du <b>$newDate</b>","","RIGHT","ABOVE",160).
       "Réinitialiser</A></div></TD></TR>";
    }
    echo "<TR><TD colspan=2 style=\"font-weight:bold;\">";
    echo "$mess_item_page</TD></TR>";
    echo "<TR><TD colspan=2 valign='middle' style=\"border:1px solid #002D45; width:auto;\"><TABLE cellspacing='0' cellpadding = '0' border='0'><TR>";
    if (isset($item) && $item !='')
       Conception :: MdfPgs($nb_pages,$item);
    for ($j = 1;$j < 4;$j++)
    {
        if ($j == 1) $nom[$j] = $msgadm_mod; elseif ($j == 2) $nom[$j] = $msgadm_seq; elseif ($j == 3) $nom[$j] = $msq_acts;
        if ($j == 1) $dim[$j] = "nb_pg_mod"; elseif ($j == 2) $dim[$j] = "nb_pg_seq"; elseif ($j == 3) $dim[$j] = "nb_pg_act";
       $nbr_pgs = GetDataField($connect,"select param_etat_lb from param_foad where param_typ_lb='$dim[$j]'","param_etat_lb");
       echo "<TD style=\"font-size: 11px;font-weight: bold; padding-left:3px;padding-top: 3px;\">".
            "<form name='form$j'>&nbsp;&nbsp;".$nom[$j]."&nbsp;&nbsp;".
            "<SELECT name='select$j' class='SELECT' ".
            "onChange=\"javascript:appelle_ajax(form$j.select$j.options[selectedIndex].value);".
            "\$(mien).empty();\">";
       $nbr_pgs_bis = ($nbr_pgs < 10) ? "0" : "";
       $mess_notif1 = $nom[$j]." : ".$msgadm_nbr_aff." ".$nbr_pgs_bis.$nbr_pgs." ";
       echo "<OPTION VALUE=\"admin/modif_nb.php?interface=1&item=".$dim[$j]."&nb_pages=$nbr_pgs&mess_notif=$mess_notif1\" selected>".
            "$nbr_pgs_bis$nbr_pgs</OPTION>";
       for ($i=10;$i<101;$i+=10)
       {
          $mess_notif1 = $nom[$j]." : ".$msgadm_nbr_aff." ".$i." ";
          if ($i < 10)
             echo "<OPTION value=\"admin/modif_nb.php?interface=1&item=".$dim[$j]."&nb_pages=$i&mess_notif=$mess_notif1\">0$i</OPTION>";
          else
             echo "<OPTION value=\"admin/modif_nb.php?interface=1&item=".$dim[$j]."&nb_pages=$i&mess_notif=$mess_notif1\">$i</OPTION>";
       }
       echo "</SELECT></TD></FORM>";
    }
    echo "</TR></TABLE></TD></TR>";
    echo "<TR height='5'><TD colspan=2></td></tr>";
    echo "<TR height='30'><TD align='left'>";
    $etat_fav = GetDataField($connect,"select param_etat_lb from param_foad where param_typ_lb='favoris'","param_etat_lb");
    echo "<div class=\"Oui\" id=\"Oui8\" ".
         "onClick=\"javascript:appelle_ajax('admin/modif_nb.php?interface=1&chge_fav=1');".
         "\$(mien).empty();\" ".
         "onmouseOver=\"style.color='#D45211';overlib('".addslashes($msgadm_clkEtat)."',ol_hpos,RIGHT,ABOVE,WIDTH,200,DELAY,800,CAPTION,'');\" ".
         " onmouseOut=\"style.color='#24677A';nd();\">$msgadm_msgfav : $etat_fav</div></TD></TR>";
    echo "<TR height='30'><TD align='left'>";
    $etat_seqduref = GetDataField($connect,"select param_etat_lb from param_foad where param_typ_lb='seqduref'","param_etat_lb");
    echo "<div class=\"Oui\" id=\"Oui9\"  onClick=\"javascript:
                        \$.ajax({
                        type: 'GET',
                        url: 'admin/modif_nb.php',
                        data: 'interface=1&chge_seqduref=1',
                        beforeSend:function(){
                            \$('#affiche').addClass('Status');
                            \$('#affiche').append('Opération en cours....');
                        },
                        success: function(msg){
                           \$('#mien').empty();
                           if (msg == 'NON')
                           {
                              \$('#mien').html('Durée de la séquence issue d\'un référentiel de formation désactivée.');
                              \$('#Oui9').html('Renseigner la durée de la séquence issue d\'un référentiel de formation : NON');
                           }
                           else
                           {
                              \$('#mien').html('Durée de la séquence issue d\'un référentiel de formation activée');
                              \$('#Oui9').html('Renseigner la durée de la séquence issue d\'un référentiel de formation : OUI');
                           }
                           \$('#affiche').empty();
                           \$('#mien').show();
                           setTimeout(function() {\$('#mien').empty();},7000);
                        }
                    });\" ".
         "onmouseOver=\"style.color='#D45211';overlib('".addslashes($msgadm_clkEtat)."',ol_hpos,RIGHT,ABOVE,WIDTH,200,DELAY,800,CAPTION,'');\" ".
         " onmouseOut=\"style.color='#24677A';nd();\">Renseigner la durée de la séquence issue d'un référentiel de formation : $etat_seqduref</div></TD></TR>";
    echo "<TR height='30'><TD align='left'>";
    $etat_messInsc = GetDataField($connect,"select param_etat_lb from param_foad where param_typ_lb='mess_inscription'","param_etat_lb");
    echo "<div class=\"Oui\" id=\"Oui10\"  onClick=\"javascript:
                        \$.ajax({
                        type: 'GET',
                        url: 'admin/modif_nb.php',
                        data: 'interface=1&chge_messInsc=1',
                        beforeSend:function(){
                            \$('#affiche').addClass('Status');
                            \$('#affiche').append('Opération en cours....');
                        },
                        success: function(msg){
                           \$('#mien').empty();
                           if (msg == 'NON')
                           {
                              \$('#mien').html('Message d\'inscription personnalisée désactivé.');
                              \$('#Oui10').html('Personnaliser le message envoyé lors de l\'inscription : NON');
                              \$('#MessInsc').css('display','none');
                           }
                           else
                           {
                              \$('#mien').html('Message d\'inscription personnalisée activé');
                              \$('#Oui10').html('Personnaliser le message envoyé lors de l\'inscription : OUI');
                              \$('#MessInsc').css('display','block');
                           }
                           \$('#affiche').empty();
                           \$('#mien').show();
                           setTimeout(function() {\$('#mien').empty();},7000);
                        }
                    });\" ".
         "onmouseOver=\"style.color='#D45211';overlib('".addslashes($msgadm_clkEtat)."',ol_hpos,RIGHT,ABOVE,WIDTH,200,DELAY,800,CAPTION,'');\" ".
         " onmouseOut=\"style.color='#24677A';nd();\">Personnaliser le message envoyé lors de l'inscription : $etat_messInsc";

    echo "</div>";
    $messageInsc = GetDataField($connect,"select param_etat_lb from param_foad where param_typ_lb='mess_inscription'","param_etat_lb");
    if ($messageInsc == 'OUI')
    {
       echo '<div id="MessInsc" style="margin-left:30px;text-decoration:underline;display:block;">'.
            '<a href="admin/msgInsc.php?ici=1&keepThis=true&TB_iframe=true&height=440&width=650" '.
            'class="thickbox" name="Modification des portions de messages envoyés lors de l\'inscription">'.
            'Modifier les portions de messages envoyés lors d\'une inscription</A></div>';
    }
    else
    {
       echo '<div id="MessInsc" style="margin-left:30px;text-decoration:underline;display:none;">'.
            '<a href="admin/msgInsc.php?ici=1&keepThis=true&TB_iframe=true&height=440&width=650" '.
            'class="thickbox" name="Modification des portions de messages envoyés lors de l\'inscription">'.
            'Modifier les portions de messages envoyés lors d\'une inscription</A></div>';
    }
    echo "</TD></TR>";
    echo "<TR height='30'><TD align='left'>";
    $etat_mailComment = GetDataField($connect,"select param_etat_lb from param_foad where param_typ_lb='mailcomment'","param_etat_lb");
    echo "<div class=\"Oui\" id=\"Oui11\"  onClick=\"javascript:
                        \$.ajax({
                        type: 'GET',
                        url: 'admin/modif_nb.php',
                        data: 'interface=1&chge_mailcomment=1',
                        beforeSend:function(){
                            \$('#affiche').addClass('Status');
                            \$('#affiche').append('Opération en cours....');
                        },
                        success: function(msg){
                           \$('#mien').empty();
                           if (msg == 'NON')
                           {
                              \$('#mien').html('Envoi de mail suite à un commentaire ou notation sur Wiki ou Blog désactivé.');
                              \$('#Oui11').html('Autoriser l\'envoi de mail suite à des commentaires ou notations sur Blog ou Wiki  : NON');
                           }
                           else
                           {
                              \$('#mien').html('Envoi de mail suite à un commentaire ou notation sur Wiki ou Blog activé.');
                              \$('#Oui11').html('Autoriser l\'envoi de mail suite à des commentaires ou notations sur Blog ou Wiki : OUI');
                           }
                           \$('#affiche').empty();
                           \$('#mien').show();
                           setTimeout(function() {\$('#mien').empty();},7000);
                        }
                    });\" ".
         "onmouseOver=\"style.color='#D45211';overlib('".addslashes($msgadm_clkEtat)."',ol_hpos,RIGHT,ABOVE,WIDTH,200,DELAY,800,CAPTION,'');\" ".
         " onmouseOut=\"style.color='#24677A';nd();\">Autoriser l'envoi de mail suite à des commentaires ou notations sur Blog ou Wiki : $etat_mailComment</div>";
    echo "</TD></TR>";
    $etat_rss = GetDataField($connect,"select param_etat_lb from param_foad where param_typ_lb='rss'","param_etat_lb");
    echo "<TR height='30'><TD align='left'>";
    echo "<div class=\"Oui\" id=\"Oui3\" ".
         "onClick=\"javascript:appelle_ajax('admin/modif_nb.php?interface=1&chge_Rss=1');".
         "\$(mien).empty();\" ".
         "onmouseOver=\"style.color='#D45211';overlib('".addslashes($msgadm_clkEtat)."',ol_hpos,RIGHT,ABOVE,WIDTH,200,DELAY,800,CAPTION,'');\" ".
         " onmouseOut=\"style.color='#24677A';nd();\">$msgadm_msgrss : $etat_rss</div></TD></TR>";
    if (strstr($adresse_http,"educagri.fr"))
    {
       $etat_mp = GetDataField($connect,"select param_etat_lb from param_foad where param_typ_lb='multi-centre'","param_etat_lb");
       echo "<TR height='30'><TD align='left'>";
       echo "<div class=\"Oui\" id=\"Oui0\" ".
         "onClick=\"javascript:appelle_ajax('admin/modif_nb.php?interface=1&chge_mp=1');".
         "\$(mien).empty();\" ".
         "onmouseOver=\"style.color='#D45211';overlib('".addslashes($msgadm_clkEtat)."',ol_hpos,RIGHT,ABOVE,WIDTH,200,DELAY,800,CAPTION,'');\" ".
         " onmouseOut=\"style.color='#24677A';nd();\">$msgadm_msgmc : $etat_mp</div></TD></TR>";
    }
    $etat_flib = GetDataField($connect,"select param_etat_lb from param_foad where param_typ_lb='forum_libre'","param_etat_lb");
    echo "<TR height='30'><TD align='left'>";
    echo "<div class=\"Oui\" id=\"Oui2\" ".
         "onClick=\"javascript:appelle_ajax('admin/modif_nb.php?interface=1&chge_flib=1');".
         "\$(mien).empty();\" ".
         "onmouseOver=\"style.color='#D45211';overlib('".addslashes($msgadm_clkEtat)."',ol_hpos,RIGHT,ABOVE,WIDTH,200,DELAY,800,CAPTION,'');\" ".
         " onmouseOut=\"style.color='#24677A';nd();\">$msgadm_msgflib : $etat_flib</div></TD></TR>";
    $etat_chat = GetDataField($connect,"select param_etat_lb from param_foad where param_typ_lb='chat'","param_etat_lb");
    echo "<TR height='30'><TD align='left'>";
    echo "<div class=\"Oui\" id=\"Oui1\" ".
         "onClick=\"javascript:appelle_ajax('admin/modif_nb.php?interface=1&chge_chat=1');".
         "\$(mien).empty();parent.logo.location.reload();\" ".
         "onmouseOver=\"style.color='#D45211';overlib('".addslashes($msgadm_clkEtat)."',ol_hpos,RIGHT,ABOVE,WIDTH,200,DELAY,800,CAPTION,'');\" ".
         " onmouseOut=\"style.color='#24677A';nd();\">$msgadm_msgchat : $etat_chat</div></TD></TR>";
    $affiche_ecran = "<tr><td valign='top' style=\"font-weight:bold;\">".$msgadm_ecrmod."</td></tr>".
                     "<tr><td valign='middle' style=\"border:1px solid #002D45;\">".
                     "<table cellpadding='0' cellspacing='0' border='0'><tr>";
    $ecrole = array();
    $ecrnom = array();
    $ecrole[0] = "APPRENANT";$ecrnom[0] = strtolower($mes_des_app);
    $ecrole[1] = "TUTEUR";$ecrnom[1] = strtolower($mes_des_tut);
    $ecrole[2] = "FORMATEUR_REFERENT";$ecrnom[2] = strtolower($mes_des_fr);
    $ecrole[3] = "RESPONSABLE_FORMATION";$ecrnom[3] = strtolower($mes_des_rf);
    $ecrole[4] = "ADMINISTRATEUR";$ecrnom[4] = strtolower($mes_des_adm);
    for ($i=0;$i<5;$i++)
    {
       $annu = $ecrole[$i];
       $affiche_ecran .= "<td style=\"padding-top:4px;padding-left:12px;padding-bottom:4px;font-weight:bold;width:auto;\">".$ecrnom[$i]."<div id='".$ecrnom[$i]."' style=\"float:left;\">";
       $type_ecran  = GetdataField ($connect,"select param_ecran from parametre where param_user='".$ecrole[$i]."'","param_ecran");
       $typ_ecran = ($type_ecran == "MEDIAN") ? 'm' : 'n';
       $affiche_ecran .= "<FORM name=\"form$i$i\">";
       $affiche_ecran .= "<SELECT name=\"select$i$i\" class='SELECT' style=\"float:left;margin-bottom:0px;\" onChange=\"javascript:appelle_ajax(form$i$i.select$i$i.options[selectedIndex].value);\">";
       if ($type_ecran =="MEDIAN") $ecr_typ = $mess_admin_median;
       if ($type_ecran =="NORMAL") $ecr_typ = $mess_admin_normal;
       $affiche_ecran .= "<OPTION value=\"admin/ecran_type.php?ecran=1&typ_ecran=$typ_ecran&type=$annu&annu=$annu\">$ecr_typ</OPTION>";
       $affiche_ecran .= "<OPTION value=\"admin/ecran_type.php?ecran=1&typ_ecran=m&type=$annu&annu=$annu\">$mess_admin_median</OPTION>";
       $affiche_ecran .= "<OPTION value=\"admin/ecran_type.php?ecran=1&typ_ecran=n&type=$annu&annu=$annu\">$mess_admin_normal</OPTION>";
       $affiche_ecran .= "</SELECT></form></div></td>";
    }
    echo $affiche_ecran."</tr></table>";
    echo fin_tableau('');
    echo '<div id="mien" class="cms"></div>';
  exit();
}

if (isset($defaut) && $defaut == 1)
{
   if (isset($mess_notif) && $mess_notif != '')
      echo notifier($mess_notif);
   entete_simple($mess_admin_modif_forma);
   echo "<tr><td  height='20' align='center'>";
   echo"<CENTER><FONT SIZE='2'>$mess_admin_mess_def</FONT></CENTER><P>&nbsp;$mess_admin_mess_ret";
   $change_etat = mysql_query("update param_foad set param_etat_lb='' where param_typ_lb='logo'");
   $change_etat = mysql_query("update param_foad set param_etat_lb='images/fondtitre.jpg' where param_typ_lb='bckgr_img'");
   $change_etat = mysql_query("update param_foad set param_etat_lb='' where param_typ_lb='url'");
   $change_etat = mysql_query("update param_foad set param_etat_lb='images/menu/haut_forma_nofse.jpg' where param_typ_lb='bienvenue_img'");
   $change_etat = mysql_query("update param_foad set param_etat_lb='images/menu/haut_forma_nofse.jpg' where param_typ_lb='bienvenue1_img'");
   $change_etat = mysql_query("update param_foad set param_etat_lb='FFFFFF' where param_typ_lb='couleur_fond'");
    echo "</TD></TR>";
    echo boutret(1,0);
    echo fin_tableau('');
    echo "<script language=\"JavaScript\">";
    echo "document.location.replace(\"admin_gere.php?interface=1&mess_notif=$mess_notif\")";
    echo "</script>";
   exit;
}
if (isset($logo) && $logo == 1 && !isset($telecharger))
{
   entete_simple($mess_admin_modif_forma);
   echo "<TR><TD bgColor='#CEE6EC'>";
   echo "<TABLE cellspacing='1' cellpadding='4' border='0'><TR>";
   echo "<FORM name='form' action=\"admin_gere.php?logo=1&telecharger=1\" method=\"POST\" ENCTYPE=\"multipart/form-data\">";
   echo "<TD width=20% valign='top'><B>$mess_admin_tel_logo </B></TD><TD valign='top'> ";
    if ($mac != 1)
    {
          if (strstr(getenv("HTTP_USER_AGENT"),"MSIE"))
          {
            echo "<DIV style=\"position :absolute ;\">";
            echo "<INPUT TYPE='file' name='userfile' style=\"filter :alpha(opacity=0)\"".
                 "onfocus=\"document.form.txtFile.value=document.form.userfile.value\" ".
                 "onchange=\"document.form.txtFile.value=document.form.userfile.value\">";
            echo "</DIV>";
            echo "<input type='text' class='INPUT' name='txtFile'";
            echo " onmouseover=\"img8.src='images/fiche_identite/boutfichierb.gif';return true;\" ".
                 "onmouseout=\"img8.src='images/fiche_identite/boutfichier.gif'\">".
                 "<IMG NAME=\"img8\" SRC=\"images/fiche_identite/boutfichier.gif\" align=\"absmiddle\" BORDER='0' ".
                 "onLoad=\"tempImg=new Image(0,0); tempImg.src='images/fiche_identite/boutfichierb.gif'\">";
          }
          else
          {
            echo "<DIV style=\"position :absolute ;\">";
            echo "<INPUT TYPE='file' name='userfile'  style=\"filter:alpha(opacity=1); -moz-opacity: .01;\"".
                 "onfocus=\"document.form.txtFile.value=document.form.userfile.value\" ".
                 "onchange=\"document.form.txtFile.value=document.form.userfile.value\">";
            echo "</DIV>";
            echo "<INPUT TYPE='text' class='INPUT' name='txtFile' ".
                 "onMouseover=\"makevisible(this,0)\" ".
                 "onMouseout=\"makevisible(this,0)\">".
                 "<IMG NAME=\"img8\" SRC=\"images/fiche_identite/boutfichier.gif\" align=\"absmiddle\" BORDER='0' ".
                 "onLoad=\"tempImg=new Image(0,0); tempImg.src='images/fiche_identite/boutfichierb.gif'\">";
          }
    }
    else
          echo "<INPUT TYPE='file' name='userfile' size='40' enctype='multipart/form-data'>";
   echo "</TD><TD valign='top'>$mess_admin_load_logo</TD></TR>";
   echo "<INPUT type='hidden' name='mess_notif' value=\"$msg_logo_new\">";
   echo "<TR><TD align=left valign='top'><B>$mess_admin_modif_link </B></TD>";
   echo "<TD colspan='2' valign='top'><INPUT type='text' class='INPUT' name='url' size='70'></TD></TR>";
   echo "<TR><TD align=left valign='top'><B>$mess_admin_modif_lbl </B></TD>";
   echo "<TD colspan='2'><INPUT type='text' class='INPUT' name='label_url' size='50'></TD></TR>";
   echo "<TD>&nbsp;</TD><TD align='left' colspan='2' ><A HREF=\"javascript:document.form.submit();\" ".
        "onmouseover=\"img1.src='images/fiche_identite/boutvalidb.gif';return true;\" ".
        "onmouseout=\"img1.src='images/fiche_identite/boutvalid.gif'\">".
         "<IMG NAME=\"img1\" SRC=\"images/fiche_identite/boutvalid.gif\" BORDER='0' ".
         "onLoad=\"tempImg=new Image(0,0); tempImg.src='images/fiche_identite/boutvalidb.gif'\"></A></TD></TR>";
   echo "</FORM>";
   $lien = "admin_gere.php?logo=2&mess_notif=$msg_logo_def";
   $lien = urlencode($lien);
   echo "<TR height='50'><TD>&nbsp;</TD><TD align=left colspan='2' valign=bottom>".
        "$bouton_gauche<a href=\"trace.php?link=$lien\" target='main'>$mess_admin_ret_def</A>$bouton_droite</TD></TR>";
   echo boutret(1,1);
   echo "</TABLE>";
   echo fin_tableau('');
  exit;
}
if (isset($logo) && $logo == 1 && isset($telecharger) && $telecharger == 1)
{
   if (isset($_FILES["userfile"]["tmp_name"]))
   {
      list($extension,$nom) = getextension($_FILES["userfile"]["name"]);
      if (strtolower($extension) != "gif" && strtolower($extension) != "png" &&
          strtolower($extension) != "jpg" && strtolower($extension) != "jpeg")
           $mess_notif = $_FILES["userfile"]["name"]." : extension du fichier non autorisée.<br /> ".
                         "seules les extensions gif, jpg et png sont autorisées ";
      else
      {
         $userfile = $_FILES["userfile"]["tmp_name"];
         $nom_final=$_FILES["userfile"]["name"];
         $dir="ressources";
         $dest_file = $repertoire."/".$dir."/".$nom_final;
         $fichier = $dir."/".$nom_final;
         $source_file=$userfile;
         $copier=move_uploaded_file($source_file , $dest_file);
         $change_etat = mysql_query("update param_foad set param_etat_lb=\"$fichier\" where param_typ_lb='logo'");
      }
   }
   if ($url != "")
      $change_etat = mysql_query("update param_foad set param_etat_lb=\"$url\" where param_typ_lb='url'");
   if ($label_url != "")
      $change_etat = mysql_query("update param_foad set param_etat_lb=\"$label_url\" where param_typ_lb='label_url'");
    echo "<script language=\"JavaScript\">";
        echo "document.location.replace(\"admin_gere.php?interface=1&mess_notif=$mess_notif\")";
    echo "</script>";
   exit;
}
if (isset($logo) && $logo == 2)
{
   $change_etat = mysql_query("update param_foad set param_etat_lb='images/educagri.gif' where param_typ_lb='logo'");
   $change_etat = mysql_query("update param_foad set param_etat_lb='http://www.chlorofil.fr' where param_typ_lb='url'");
   $change_etat = mysql_query("update param_foad set param_etat_lb=\"Le site web de l'enseignement agricole\" where param_typ_lb='label_url'");
   echo "<script language=\"JavaScript\">";
       echo "document.location.replace(\"admin_gere.php?interface=1&mess_notif=$mess_notif\")";
   echo "</script>";
  exit;
}
if (isset($ressources) && $ressources == 1)
{
    entete_simple($mess_admin_modif_forma);
    echo "<TR><TD colspan=2 width='100%'><TABLE cellspacing='1'  cellpadding = '4' width='100%'>";
    echo "<TR><TD height='50' align='left'>";
    $lien = "ressource_admin.php?flg=1&acces=1";
    $lien = urlencode($lien);
    $nbr_pgs = GetDataField($connect,"select param_etat_lb from param_foad where param_typ_lb='nbr_pages_ress'","param_etat_lb");
    echo"<DIV id='sequence'><a href=\"trace.php?link=$lien\" target='main'>$mess_menu_ress_adm</A></DIV></TD></TR>";
    echo "<TR><TD height='50' align='left' style=\"font-family:arial;font-weight:bold;\">";
    echo $mess_adm_mod_npages;
    echo "<form name='monform' id='monform' method='post'>";
    echo "<SELECT name='select' class='SELECT' onChange=\"javascript:appelle_ajax(monform.select.options[selectedIndex].value);\">";
    if ($nbr_pgs < 10)$nbr_pgs_bis = "0";else $nbr_pgs_bis = "";
    echo "<OPTION VALUE=\"admin/modif_nb.php?ressources=1&nbr_pgs=$nbr_pgs&mess_notif=$mesg_idx\">$nbr_pgs_bis".$nbr_pgs."</OPTION>";
    for ($i=3;$i<21;$i++)
    {
          $mesg_idx = $msgadm_pgs_idx." ".$i;
          if ($i < 10)
             echo "<OPTION value=\"admin/modif_nb.php?ressources=1&nbr_pgs=$i&mess_notif=$mesg_idx\">0$i</OPTION>";
          else
             echo "<OPTION value=\"admin/modif_nb.php?ressources=1&nbr_pgs=$i&mess_notif=$mesg_idx\">$i</OPTION>";
    }
    echo "</SELECT></TD></TR></form>";
    echo "<TR><TD height='30 align='left'>";
    $lien = "admin_gere.php?ajt_serv=1";
    $lien = urlencode($lien);
    echo"<DIV id='sequence'><a href=\"trace.php?link=$lien\" target='main'>$mess_adm_ajtserv</A></DIV></TD></TR>";
    echo "<TR><TD height='15 align='left'>";
    echo fin_tableau('');
  exit;
}
if (isset($ajt_serv) && $ajt_serv == 1)
{
   if (isset($supp) && $supp == 1)
     $req_supp = mysql_query("delete from serveur_ressource where serveur_cdn = $serveur");
   if (isset($modif) && $modif == 1)
     $req_modif = mysql_query("update serveur_ressource set serveur_nomip_lb=\"$adres\",serveur_param_lb=\"$parametres\",serveur_label_lb=\"$label\" where serveur_cdn = '$serveur'");
   if (isset($ajout) && $ajout == 1)
   {
       $new_id = Donne_ID ($connect,"select max(serveur_cdn) from serveur_ressource");
       $req_ajt = mysql_query("INSERT INTO serveur_ressource VALUES($new_id,\"$adres\",\"$parametres\",\"$label\")");
   }
   if (isset($mess_notif) && $mess_notif != '')
        echo notifier($mess_notif." : ".$adres);
   entete_simple($mess_adm_ajtserv);
   $req_serv = mysql_query("select * from serveur_ressource");
   $nb_req_serv = mysql_num_rows($req_serv);
   if ($nb_req_serv > 0)
   {
      echo "<TR><TD bgcolor='#FFFFFF' colspan=2><TABLE width='100%' cellspacing='0' cellpadding = '6'>";
      echo "<TR bgcolor='#2B677A'>";
         echo "<TD height='20' align='middle' nowrap><FONT COLOR=white><b>$mess_fav_adr_site/$mess_admin_adr_ip</b></FONT></TD>";
         echo "<TD height='20' align='middle'><FONT COLOR=white><b>$mess_params</b></FONT></TD>";
         echo "<TD height='20' align='middle'><FONT COLOR=white><b>$mess_label</b></FONT></TD>";
         echo "<TD align='middle'><FONT COLOR=white><b>$mess_fiche_prof</b></FONT></TD>";
         echo "<TD align='middle'><FONT COLOR=white><b>$mess_ag_supp</b></FONT></TD>";
         echo "</TR>";
      $i = 0;
      while ($i < $nb_req_serv)
      {
         echo couleur_tr($i+1,'');
         $id_serv = mysql_result($req_serv,$i,"serveur_cdn");
         $adr = mysql_result($req_serv,$i,"serveur_nomip_lb");
         $params = mysql_result($req_serv,$i,"serveur_param_lb");
         $label = mysql_result($req_serv,$i,"serveur_label_lb");
         echo "<TD align='left'>$adr</TD>";
         echo "<TD align='left'>$params</TD>";
         echo "<TD align='left'>$label</TD>";
         $lien = "admin_gere.php?ajt_serv=1&modifier=1&serveur=$id_serv";
         $lien = urlencode($lien);
         echo "<TD width='2%' align='middle'><a href=\"trace.php?link=$lien\"><IMG SRC=\"images/repertoire/icoGrenomfich.gif\" width=\"20\" height=\"20\" TITLE=\"$msq_modifier\" BORDER=0></A></td>";
         $lien = "admin_gere.php?ajt_serv=1&supp=1&serveur=$id_serv&le_nom=$adr&mess_notif=$msgadm_supserv";
         $lien = urlencode($lien);
         echo "<TD width='2%' align='middle'><a href=\"javascript:void(0);\" ".
              "onclick=\"javascript:return(confm('trace.php?link=$lien'));\">".
              "<IMG SRC=\"images/messagerie/icoGpoubel.gif\" height=\"20\" width=\"15\" BORDER=0></A></td></TR>";
        $i++;
      }
      echo "</TABLE></TD></TR>";
   }
   echo "<FORM Name='form' action=\"admin_gere.php?ajt_serv=1\" method=\"POST\">";
   echo "<TR><TD bgcolor='#FFFFFF' colspan=2><TABLE width='100%'>";
   if ($modifier == 1)
   {
      $adr = GetDataField($connect,"select serveur_nomip_lb from serveur_ressource where serveur_cdn='$serveur'","serveur_nomip_lb");
      $params = GetDataField($connect,"select serveur_param_lb from serveur_ressource where serveur_cdn='$serveur'","serveur_param_lb");
      $label = GetDataField($connect,"select serveur_label_lb from serveur_ressource where serveur_cdn='$serveur'","serveur_label_lb");
      echo "<INPUT TYPE='HIDDEN' name='modif' value = 1>";
      echo "<INPUT TYPE='HIDDEN' name='serveur' value = '$serveur'>";
      echo "<INPUT TYPE='HIDDEN' name='mess_notif' value = \"$msgadm_modserv\">";
      echo "<INPUT TYPE='HIDDEN' name='le_nom' value = \"$adr\">";
   }
   else
   {
      echo "<INPUT TYPE='HIDDEN' name='mess_notif' value = \"$msgadm_ajtserv\">";
      echo "<INPUT TYPE='HIDDEN' name='le_nom' value = \"$adr\">";
      echo "<INPUT TYPE='HIDDEN' name='ajout' value = 1";
      echo "<TR height='50'><TD height='40' colspan=4 align='left' valign='center' bgcolor = '#CEE6EC'><FONT size=3><B>$mess_ajout_item<B></FONT></TD></TR>";
   }
   echo "<TR height='35'><TD><B>$mess_fav_adr_site/$mess_admin_adr_ip</B></TD>";
   echo "<TD colspan=3><INPUT type=\"text\" name=\"adres\" size=\"45\" value=\"";
   if ($modifier == 1)
      echo "$adr";
   echo "\"></TD></TR>";
   echo "<TR height='35'><TD><B>$mess_params</B></TD>";
   echo "<TD colspan=3><INPUT type=\"text\" name=\"parametres\" size=\"80\" value=\"";
   if ($modifier == 1)
     echo "$params";
   echo "\"></TD></TR>";
   echo "<TR height='35'><TD>$mess_label_detail</TD>";
   echo "<TD colspan=3><INPUT type=\"text\" name=\"label\" size=\"30\" value=\"";
   if ($modifier == 1)
     echo "$label";
   echo "\">";
    echo nbsp(4)."<A HREF=\"javascript:void(0);\" title='$mess_menu_aide' ".
           "onclick=\"return overlib('<TABLE><TR><TD width=5></TD><TD>".addslashes($mess_label_avert)."</TD></TR></TABLE>'".
           ",STICKY,ol_hpos,RIGHT,ABOVE,WIDTH,350,CAPTION,'<TABLE width=100% border=0 cellspacing=2><TR height=20 width=100%>".
           "<TD align=left width=90% nowrap><B>$mess_menu_aide</B></TD></TR></TABLE>')\"".
           " onMouseOut=\"return nd();\"><IMG SRC='images/modules/tut_form/icoaide.gif' border='0'></A>";
   echo "</TD></TR>";
   echo boutret(1,0);
   echo "</TD><TD align='left'><A HREF=\"javascript:document.form.submit();\" onmouseover=\"img1.src='images/fiche_identite/boutvalidb.gif';return true;\" onmouseout=\"img1.src='images/fiche_identite/boutvalid.gif'\">".
         "<IMG NAME=\"img1\" SRC=\"images/fiche_identite/boutvalid.gif\" BORDER='0' onLoad=\"tempImg=new Image(0,0); tempImg.src='images/fiche_identite/boutvalidb.gif'\"></A>";
   echo "</TD></FORM></TR></TABLE>";
   echo fin_tableau('');
  exit;
}
if (isset($cdr_cdi) && $cdr_cdi == 1)
{
   entete_simple($mess_admin_modif_forma);
   echo "<tr><td  height='20' align='center' colspan='2'>";
   echo "&nbsp;<P><CENTER><FONT SIZE='2'>$mess_adm_mod_cdi</FONT></CENTER><P>&nbsp;";
   $cdi = GetDataField($connect,"select param_etat_lb from param_foad where param_typ_lb='cdi'","param_etat_lb");
   echo "<center><FORM Name='form' action=\"admin_gere.php?cdr_cdi=2\" method=\"POST\">";
   echo "<INPUT type=\"text\" name=\"cdi\" size=\"60\" value=\"$cdi\">";
   echo "</TD></TR>";
   boutret(1,0);
   echo "</TD><TD align='left'><A HREF=\"javascript:document.form.submit();\" onmouseover=\"img1.src='images/fiche_identite/boutvalidb.gif';return true;\" onmouseout=\"img1.src='images/fiche_identite/boutvalid.gif'\">".
         "<IMG NAME=\"img1\" SRC=\"images/fiche_identite/boutvalid.gif\" BORDER='0' onLoad=\"tempImg=new Image(0,0); tempImg.src='images/fiche_identite/boutvalidb.gif'\"></A>";
   echo "</FORM>";
   echo fin_tableau('');
  exit;
}
if (isset($cdr_cdi) && $cdr_cdi == 2)
{
   entete_simple($mess_admin_modif_forma);
   echo "<tr><td  height='20' align='center'>";
   echo "&nbsp;<P><CENTER><FONT SIZE='2'>$mess_adm_mod_cdi_ok</FONT></CENTER><P>&nbsp;";
   $modif_cdr = mysql_query("update param_foad set param_etat_lb='$cdi' where param_typ_lb='cdi'");
   echo fin_tableau('');
  exit;
}
if (isset($img) && $img == 2){
   entete_simple($mess_admin_modif_forma);
   echo "<tr><td  height='20' align='center'>";
   echo "&nbsp;<P><CENTER><FONT SIZE='2'>$mess_admin_new_logo<BR>$mess_admin_mess_ret</FONT></CENTER><P>&nbsp;";
    $change_etat = mysql_query("update param_foad set param_etat_lb='images/fondtitre.jpg' where param_typ_lb='bckgr_img'");
   echo fin_tableau('');
  exit;
}
if (isset($img) && $img == 1 && !isset($telecharger))
{
   entete_simple($mess_admin_modif_forma);
   echo "<tr><td  height='20' align='center' colspan=2>";
   echo "&nbsp;<P><CENTER><FONT SIZE='2'>$mess_admin_laod_bkg</FONT></CENTER><P>&nbsp;";
   echo "<FORM name='form' action=\"admin_gere.php?img=1&telecharger=1\" method='POST' ENCTYPE='multipart/form-data'>";
   echo "<INPUT type=\"file\" name=\"userfile\" enctype=\"multipart/form-data\">";
   echo "<INPUT type=\"hidden\" name=\"MAX_FILE_SIZE\" value=\"200000\"><P>";
   echo "</TD></TR>";
   boutret(1,0);
   echo "</TD><TD align='left'><A HREF=\"javascript:document.form.submit();\" onmouseover=\"img1.src='images/fiche_identite/boutvalidb.gif';return true;\" onmouseout=\"img1.src='images/fiche_identite/boutvalid.gif'\">".
         "<IMG NAME=\"img1\" SRC=\"images/fiche_identite/boutvalid.gif\" BORDER='0' onLoad=\"tempImg=new Image(0,0); tempImg.src='images/fiche_identite/boutvalidb.gif'\"></A>";
   echo "</FORM>";
   $lien = "admin_gere.php?img=2";
   $lien = urlencode($lien);
   echo"</TD></TR><TR><TD align=center colspan=2>$bouton_gauche<a href=\"trace.php?link=$lien\"  target='main'>$mess_admin_ret_img</A>$bouton_droite<P>";
   echo fin_tableau('');
  exit;
}
if (isset($img) && $img == 1 && isset($telecharger) && $telecharger == 1)
{
   entete_simple($mess_admin_modif_forma);
   echo "<tr><td  height='20' align='center'>";
   echo "&nbsp;<P><CENTER><FONT SIZE='2'>$mess_admin_new_img_bg<BR>$mess_admin_mess_ret</FONT></CENTER><P>&nbsp;";
   list($extension,$nom) = getextension($_FILES["userfile"]["name"]);
      if (strtolower($extension) != "gif" && strtolower($extension) != "png" &&
          strtolower($extension) != "jpg" && strtolower($extension) != "jpeg")
           $mess_notif = $_FILES["userfile"]["name"]." : extension du fichier non autorisée.<br /> ".
                         "seules les extensions gif, jpg et png sont autorisées ";
   else
   {
    $userfile = $_FILES["userfile"]["tmp_name"];
    $nom_final=$_FILES["userfile"]["name"];
    $dir="ressources";
    $dest_file = $repertoire."/".$dir."/".$nom_final;
    $fichier = $dir."/".$nom_final;
    $source_file=$userfile;
    $copier=move_uploaded_file($source_file , $dest_file);
    $change_etat = mysql_query("update param_foad set param_etat_lb='$fichier' where param_typ_lb='bckgr_img'");
   }
   echo fin_tableau('');
  exit;
}
if (isset($index) && $index == 2)
{
   entete_simple($mess_admin_modif_forma);
   echo "<tr><td  height='20' align='center'>";
   echo "&nbsp;<P><CENTER><FONT SIZE='2'>$mess_admin_img_home<BR>$mess_admin_mess_ret</FONT></CENTER><P>&nbsp;";
   $change_etat = mysql_query("update param_foad set param_etat_lb='images/menu/haut_forma_nofse.jpg' where param_typ_lb='bienvenue_img'");
   $change_etat = mysql_query("update param_foad set param_etat_lb='images/menu/haut_forma_nofse.jpg' where param_typ_lb='bienvenue1_img'");
   echo fin_tableau('');
  exit;
}
if (isset($index) && $index == 1 && !isset($telecharger))
{
        ?>
<script language=javascript>
function Couleur()
{
        fenetreA=window.open("popup_couleurs.php?origine=form.txtcouleur","Couleur","status=no,location=no,toolbar=no,directories=no,resizable=yes,width=420,height=390,top=100,left=100");
        fenetreA.focus();
}
</script>
<?php
   entete_simple($mess_admin_modif_forma);
   echo "<TR><TD bgColor='#FFFFFF'><TABLE cellspacing='1' cellpadding='3'>";
   echo "<TR height=50><TD align='left' valign='center'><B>$mess_admin_new_img1</B></TD>";
   echo "<FORM name='form' action=\"admin_gere.php?index=1&telecharger=1\" method='POST' ENCTYPE='multipart/form-data'>";
   echo "<TD valign='center'>";
   if ($mac != 1)
   {
          if (strstr(getenv("HTTP_USER_AGENT"),"MSIE")){
            echo "<DIV style=\"position :absolute ;\">";
            echo "<INPUT TYPE='file' name='userfile' style=\"filter :alpha(opacity=0)\"".
                 "onfocus=\"document.form.txtFile.value=document.form.userfile.value\" ".
                 "onchange=\"document.form.txtFile.value=document.form.userfile.value\">";
            echo "</DIV>";
            echo "<input type='text' class='INPUT' name='txtFile'";
            echo " onmouseover=\"img8.src='images/fiche_identite/boutfichierb.gif';return true;\" ".
                 "onmouseout=\"img8.src='images/fiche_identite/boutfichier.gif'\">".
                 "<IMG NAME=\"img8\" SRC=\"images/fiche_identite/boutfichier.gif\" align=\"absmiddle\" BORDER='0' ".
                 "onLoad=\"tempImg=new Image(0,0); tempImg.src='images/fiche_identite/boutfichierb.gif'\">";
          }else{
            echo "<DIV style=\"position :absolute ;\">";
            echo "<INPUT TYPE='file' name='userfile'  style=\"filter:alpha(opacity=1); -moz-opacity: .01;\"".
                 "onfocus=\"document.form.txtFile.value=document.form.userfile.value\" ".
                 "onchange=\"document.form.txtFile.value=document.form.userfile.value\">";
            echo "</DIV>";
            echo "<INPUT TYPE='text' class='INPUT' name='txtFile' onMouseover=\"makevisible(this,0)\" onMouseout=\"makevisible(this,0)\">".
                 "<IMG NAME=\"img8\" SRC=\"images/fiche_identite/boutfichier.gif\" align=\"absmiddle\" BORDER='0' ".
                 "onLoad=\"tempImg=new Image(0,0); tempImg.src='images/fiche_identite/boutfichierb.gif'\">";
          }
    }
    else
          echo "<INPUT TYPE='file' name='userfile' size='40' enctype='multipart/form-data'>";
    echo "</TD><TD  height='20' align='left' valign='center'>$mess_admin_new_img2</TD></TR>";
    echo "<TR><TD><B>$mess_adm_fd_ecr</B></TD><TD><TABLE cellpadding='3' cellspacing='1'>";
      echo "<TR>";
        echo "<TD><IMG SRC=\"images/blanc.gif\" border = \"0\"><BR><INPUT type=radio name=\"couleur\" value=\"FFFFFF\"></TD>";
        echo "<TD><IMG SRC=\"images/vert-jaune_808000.gif\" border = \"0\"><BR><INPUT type=radio name=\"couleur\" value=\"808000\"></TD>";
        echo "<TD><IMG SRC=\"images/silver_c0c0c0.gif\" border = \"0\"><BR><INPUT type=radio name=\"couleur\" value=\"c0c0c0\"></TD>";
        echo "<TD><IMG SRC=\"images/vert-bleu_008080.gif\" border = \"0\" alt =\"Vert de la plateforme\"><BR><INPUT type=radio name=\"couleur\" value=\"023E3B\"></TD>";
        echo "<TD><IMG SRC=\"images/marron_400000.gif\" border = \"0\"><BR><INPUT type=radio name=\"couleur\" value=\"400000\"></TD>";
        echo "<TD><IMG SRC=\"images/jaune-pale_ffffc0.gif\" border = \"0\"><BR><INPUT type=radio name=\"couleur\" value=\"ffffc0\"></TD>";
        echo "<TD><A href=\"javascript:Couleur();\">$mess_aut_clr</A><BR>";
        echo "<input type='text' class='INPUT' name='txtcouleur' size=7>";
        echo "</TD>";
      echo "</TR>";
     echo "</TABLE></TD></TR>";
   echo "<TR><TD></TD><TD align='left'><A HREF=\"javascript:document.form.submit();\" onmouseover=\"img1.src='images/fiche_identite/boutvalidb.gif';return true;\" onmouseout=\"img1.src='images/fiche_identite/boutvalid.gif'\">".
         "<IMG NAME=\"img1\" SRC=\"images/fiche_identite/boutvalid.gif\" BORDER='0' onLoad=\"tempImg=new Image(0,0); tempImg.src='images/fiche_identite/boutvalidb.gif'\"></A></TD></TR>";
    $lien = "admin_gere.php?index=2";
    $lien = urlencode($lien);
    echo "<TR height=50><TD></TD><TD valign=bottom>$bouton_gauche<a href=\"trace.php?link=$lien\"  target='main'>$mess_admin_ret_def</A>$bouton_droite</TD></TR>";
    boutret(1,1);
    echo "</FORM></TABLE>";
    echo fin_tableau('');
 exit;
}
if (isset($index) && $index == 1 && isset($telecharger) && $telecharger == 1)
{
   $dir="ressources";
   if (isset($_FILES["userfile"]["tmp_name"]))
   {
      list($extension,$nom) = getextension($_FILES["userfile"]["name"]);
      if (strtolower($extension) != "gif" && strtolower($extension) != "png" &&
          strtolower($extension) != "jpg" && strtolower($extension) != "jpeg")
           $mess_notif = $_FILES["userfile"]["name"]." : extension du fichier non autorisée.<br /> ".
                         "seules les extensions gif, jpg et png sont autorisées ";
      else
      {
          $userfile = $_FILES["userfile"]["tmp_name"];
          $nom_final=$_FILES["userfile"]["name"];
          $dest_file=$repertoire.'/'.$dir.'/'.$nom_final;
          $fichier = $dir."/".$nom_final;
          $source_file=$userfile;
          $copier=move_uploaded_file($source_file , $dest_file);
          $change_etat = mysql_query("update param_foad set param_etat_lb='$fichier' where param_typ_lb='bienvenue_img'");
      }
   }
   if (isset($couleur) && $couleur != "")
     $change_etat = mysql_query("update param_foad set param_etat_lb='$couleur' where param_typ_lb='couleur_fond'");
   elseif (isset($txtcouleur) && $txtcouleur != "")
     $change_etat = mysql_query("update param_foad set param_etat_lb='$txtcouleur' where param_typ_lb='couleur_fond'");
   $telecharger = 0;
   if (isset($nom_final) && $nom_final != '')
      $mess_notif = $msgadm_new_img;
   if ((isset($couleur) &&$couleur != "") || (isset($txtcouleur) && $txtcouleur != ""))
      $mess_notif .= "<br />".$msgadm_new_color;
   echo "<script language=\"JavaScript\">";
      echo "document.location.replace(\"admin_gere.php?interface=1&mess_notif=$mess_notif\")";
   echo "</script>";
  exit;
}
// Confirmation de suppression d'un utilisateur
if (isset($supprimer) && $supprimer == 1 && isset($annu) && $annu != 'APPRENANT' && !isset($suppression))
{
   $titre = "$activ_le $mess_admin_blocage";
    entete_simple($titre);
    echo "<FORM name='form' ACTION=\"admin_gere.php?num=$num&util=$util&annu=$annu\" METHOD='POST' target='main'>";
    echo "<INPUT TYPE='HIDDEN' NAME='ok_efface' VALUE='$ok_efface'>";
    echo "<INPUT TYPE='HIDDEN' NAME='supprimer' VALUE=1>";
    echo "<INPUT TYPE='HIDDEN' NAME='suppression' VALUE=1>";
    echo "<INPUT TYPE='HIDDEN' NAME='son_type' VALUE='$util'>";
    echo "<TR><TD bgcolor='#FFFFFF' align=center colspan=2><TABLE cellspacing='2'  cellpadding = '5' width='100%' border='0'>";
    echo "<TR><TD height='10' colspan=2>&nbsp;</TD></TR>";
    echo "<TR><TD height='60' colspan=2 valign='center' nowrap><B>$mess_admin_autre_form</B></TD></TR><TR><TD>";
    Ascenseur_mult ("id_form","select util_cdn,util_prenom_lb,util_nom_lb from utilisateur where util_typutil_lb != 'APPRENANT' AND util_blocageutilisateur_on ='NON' AND util_flag = 0 ORDER BY util_nom_lb ASC",$connect,$param);$param = "";
    echo "<TD align='left' valign='center'><A HREF=\"javascript:document.form.submit();\" onmouseover=\"img1.src='images/fiche_identite/boutvalidb.gif';return true;\" onmouseout=\"img1.src='images/fiche_identite/boutvalid.gif'\">".
         "<IMG NAME=\"img1\" SRC=\"images/fiche_identite/boutvalid.gif\" BORDER='0' onLoad=\"tempImg=new Image(0,0); tempImg.src='images/fiche_identite/boutvalidb.gif'\"></A></TD>";
    echo "</TD></TR>";
    boutret(1,1);
    echo "</FORM></TABLE>";
    echo fin_tableau('');
exit;
}
// Suppression d'un apprenant
if (isset($supprimer) && $supprimer == 1 && isset($annu) && $annu == "APPRENANT")
{
  $reqUtil=mysql_query("select * from utilisateur_groupe where utilgr_utilisateur_no = '$num'");
  if (mysql_num_rows($reqUtil) > 0)
  {
     while ($item = mysql_fetch_object($reqUtil))
     {
        $id_grp = $item->utilgr_groupe_no;
        $effacer_psc = mysql_query("delete from prescription_$id_grp where presc_utilisateur_no = '$num'");
        $effacer_prc = mysql_query("delete from suivi3_$id_grp where suiv3_utilisateur_no = '$num'");
        $effacer_seq = mysql_query("delete from suivi2_$id_grp where suiv2_utilisateur_no = '$num'");
        $effacer_act = mysql_query("delete from suivi1_$id_grp where suivi_utilisateur_no = '$num'");
        $effacer_sco = mysql_query("delete from scorm_util_module_$id_grp where user_module_no = '$num'");
     }
  }
  $login_user=GetDataField ($connect,"select util_login_lb from utilisateur where util_cdn='$num'","util_login_lb");
  $dir = $repertoire."/ressources/".$login_user."_".$num;
  if (file_exists($dir))
     viredir($dir,$s_exp);
  $effacer_msg = mysql_query("delete from messagerie where id_user = '$num'");
  $effacer_msg = mysql_query("delete from message where msg_apprenant_no = '$num'");
  $effacer_fiche = mysql_query("delete from fiche_suivi where fiche_utilisateur_no = '$num'");
  $effacer_ech_grp = mysql_query("delete from echange_grp where ech_auteur_no = '$num'");
  $effacer_for_lect = mysql_query("delete from forum_lecture where forlec_user_no = '$num'");
  $effacer_ins = mysql_query("delete from inscription where insc_apprenant_no = '$num'");
  $effacer_usr = mysql_query("delete from users where util_cdn = '$num'");
  $effacer_usr_ctr = mysql_query("delete from user_centre where uc_iduser_no = '$num'");
  $effacer_int = mysql_query("delete from scorm_interact where sci_user_no = '$num'");
  $effacer_obj = mysql_query("delete from scorm_objectives where scob_user_no = '$num'");
  $effacer_rdv = mysql_query("delete from rendez_vous where rdv_apprenant_no = '$num' or rdv_util_no = '$num'");
  $effacer_tut = mysql_query("delete from tuteur where tut_apprenant_no = '$num'");
  $effacer_grp = mysql_query("delete from utilisateur_groupe where utilgr_utilisateur_no = '$num'");
  $effacer_traq = mysql_query("delete from traque where traq_util_no = '$num'");
  $effacer_w = mysql_query("delete from wikiapp where wkapp_app_no = '$num'");
  $effacer_wik = mysql_query("delete from wikibodies where wkbody_auteur_no = '$num'");
  $effacer_wiki = mysql_query("delete from wikimeta where wkmeta_auteur_no = '$num'");
  $effacer_MM = mysql_query("delete from mindmaphystory where mindhisto_auteur_no = '$num'");
  $effacer_Map = mysql_query("delete from mindmapapp where mmapp_app_no = '$num'");
  $effacer_b = mysql_query("delete from blogapp where bgapp_app_no = '$num'");
  $effacer_bl = mysql_query("delete from blog where blog_auteur_no = '$num'");
  $effacer_blo = mysql_query("delete from blogbodies where bgbody_auteur_no = '$num'");
  $effacer_blog = mysql_query("delete from blogmeta where bgmeta_auteur_no = '$num'");
  $effacer_stars = mysql_query("delete from starating where starate_auteur_no = '$num'");
  $effacer_comments = mysql_query("delete from commentaires where com_auteur_no = '$num'");
  $effacer_trac = mysql_query("delete from traceur where traceur_util_no = '$num'");
  $nom_num  = GetdataField ($connect,"select util_nom_lb from utilisateur where util_cdn='$num'","util_nom_lb");
  $prenom_num  = GetdataField ($connect,"select util_prenom_lb from utilisateur where util_cdn='$num'","util_prenom_lb");
  $effacer = mysql_query("delete from utilisateur where util_cdn=$num");
  $mess_notif = "$mess_admin_sup_fiche_deb $prenom_num $nom_num $mess_admin_sup_fiche_fin";
  $lien = urlencode("admin.php?annu=$annu&id_grp=$id_grp&mess_notif=$mess_notif");
  $lien = urlencode($lien);
  echo "<script language=\"JavaScript\">";
      echo "document.location.replace(\"trace.php?link=$lien\")";
  echo "</script>";
  exit;
}
// Suppression d'un utilisateur  autre que apprenant
if (isset($supprimer) && $supprimer == 1 && isset($suppression) && $suppression == 1 && isset($annu) && $annu != "APPRENANT")
{
    $login_user=GetDataField ($connect,"select util_login_lb from utilisateur where util_cdn='$num'","util_login_lb");
    $nom  = GetdataField ($connect,"select util_nom_lb from utilisateur where util_cdn='$num'","util_nom_lb");
    $prenom  = GetdataField ($connect,"select util_prenom_lb from utilisateur where util_cdn='$num'","util_prenom_lb");
// A introduire ici la suppression des séquences et activités des apprenants dans suivi1, suivi2 et suivi3 correspondant aux séquences suivies par cet utilisateur
    if ($id_form != -1)
    {
        $login_form = GetDataField ($connect,"select util_login_lb from utilisateur where util_cdn='$id_form'","util_login_lb");
        $remplacer_form = mysql_query("update tuteur set tut_tuteur_no = '$id_form' where tut_tuteur_no = $num");
        $remplacer_form = mysql_query("update groupe set grp_resp_no = '$id_form' where grp_resp_no = $num");
        $remplacer_form = mysql_query("update sequence set seq_auteur_no ='$id_form' where seq_auteur_no=$num");
        $remplacer_form = mysql_query("update parcours set parcours_auteur_no = '$id_form' where parcours_auteur_no=$num");
        $remplacer_form = mysql_query("update activite set act_auteur_no = '$id_form' where act_auteur_no = $num");
        $remplacer_form = mysql_query("update ressource_new set ress_ajout = '$login_form' where ress_ajout = '$login_user'");
        $remplacer_form = mysql_query("update referentiel set ref_auteur_lb = '$login_form' where ref_auteur_lb = '$login_user'");
        $remplacer_form = mysql_query("update echange_grp set ech_auteur_no = '$id_form' where ech_auteur_no = '$num'");
        $remplacer_inscripteur = mysql_query("update utilisateur set util_auteur_no = '$id_form' where util_auteur_no = $num");
        $reqUtil=mysql_query("select * from utilisateur_groupe");
        if (mysql_num_rows($reqUtil) > 0)
        {
            while ($item = mysql_fetch_object($reqUtil))
            {
               $id_grp = $item->utilgr_groupe_no;
               $remplacer_presc = mysql_query("update prescription_$id_grp set presc_prescripteur_no = '$id_form' where presc_prescripteur_no=$num");
               $remplacer_form = mysql_query("update prescription_$id_grp set presc_formateur_no = '$id_form' where presc_formateur_no=$num");
            }
        }
    }
    if ($ok_efface == 1 && $id_form != -1)
    {
      $dir = $repertoire."/ressources/".$login_user."_".$num;
      if (file_exists($dir))
         viredir($dir,$s_exp);
      $effacer_for_lect = mysql_query("delete from forum_lecture where forlec_user_no = '$num'");
      $effacer_util = mysql_query("DELETE FROM utilisateur where util_cdn = $num");
      $mess_notif = "$mess_admin_sup_fiche_deb $prenom $nom $mess_admin_sup_mess_fin";
   }
   else
   {
      $bloquer = mysql_query("update utilisateur set util_blocageutilisateur_on = 'OUI',util_flag = '1' where util_cdn=$num");
      $mess_notif = "$mess_admin_blocage activé pour $prenom $nom";
   }
   $lien = urlencode("admin.php?annu=$annu&mess_notif=$mess_notif");
   echo "<script language=\"JavaScript\">";
       echo "document.location.replace(\"trace.php?link=$lien\")";
   echo "</script>";
   exit();
}
function getextension($fichier)
{
  $bouts = explode(".", $fichier);
  return array(array_pop($bouts), implode(".", $bouts));
}
?>
<div id="mien" class="cms"></div>
</body></html>