<?php
if (!isset($_SESSION)) session_start();
include ("include/UrlParam2PhpVar.inc.php");
if (!isset($_SESSION['id_user']) || $_SESSION['id_user'] == "")
{
  exit();
}
require "admin.inc.php";
require 'fonction.inc.php';
require "lang$lg.inc.php";
require 'fonction_html.inc.php';
require 'class/class_admin.php';
require 'langues/notif.inc.php';
require 'langues/adm.inc.php';
//include ("click_droit.txt");
dbConnect();
$agent=$_SERVER["HTTP_USER_AGENT"];
if (strstr(strtolower($agent),"mac") || strstr(strtolower($agent),"konqueror") || strstr(strtolower($agent),"safari"))
    $mac=1;
if (strstr(strtolower($agent),"win"))
    $win=1;
// echo "<pre>"; print_r($_POST);echo "</pre>";exit;
if (isset($_POST['liste_export']) && $_POST['liste_export']==1)
{
   $req_app = mysql_query("select util_cdn from utilisateur where util_typutil_lb='".$_POST['annu']."' order by util_nom_lb");
   $nb_app = mysql_num_rows($req_app);
   $ia=0; $fichier=''; $fichier1=''; $cpt=0;
   $ajtx = ($s_exp == 'lx') ? "\n": "\r\n";
   while ($ia < $nb_app){
       $num = mysql_result($req_app,$ia,"util_cdn");
       if (isset($exp_app[$num]) && $exp_app[$num] == 'on')
       {
          $cpt++;
          $ajt = ($cpt > 1 && $cpt < $nb_app+2) ? $ajtx : '';
          $fichier .= $ajt.trim($liste_exp[$num]);
          $fichier1 .= $ajt.trim($liste1_exp[$num]);
       }
       $ia++;
   }
   if ($cpt > 0){
      $dir_file="ressources/".$login."_".$id_user."/ressources/liste_export.txt";
      $fp = fopen($dir_file, "w+");
      $fw = fwrite($fp, "$mess_nb_app : $cpt");
      $fw = fwrite($fp, "\r\n\r\n$mess_liste_slog\r\n\r\n$fichier\r\n\r\n$mess_liste_log\r\n\r\n$fichier1");
      fclose($fp);
      ForceFileDownload($dir_file,'ascii');
   exit();
  }
}
if (isset($_POST['supprime_liste']) && $_POST['supprime_liste']== 1)
{
   $req_app = mysql_query("select utilisateur.util_cdn from utilisateur where util_typutil_lb='$annu'");
   $nb_app = mysql_num_rows($req_app);
   $ia = 0;
   $dpass = 0;
   $mess_notif = '';
   while ($ia < $nb_app){
      $num = mysql_result($req_app,$ia,"util_cdn");
      if (isset($exp_app[$num]) && $exp_app[$num] == 'on'){
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
         $requete_grp = mysql_query ("select * from utilisateur_groupe where utilgr_utilisateur_no = $num");
         $nb_grp_parc = mysql_num_rows($requete_grp);
         if ($nb_grp_parc > 0)
         {
            $gp=0;
            while ($gp < $nb_grp_parc)
            {
                  $id_grp = mysql_result($requete_grp,$gp,"utilgr_groupe_no");
                  $effacer_psc = mysql_query("delete from prescription_$id_grp where presc_utilisateur_no = '$num'");
                  $effacer_prc = mysql_query("delete from suivi3_$id_grp where suiv3_utilisateur_no = '$num'");
                  $effacer_seq = mysql_query("delete from suivi2_$id_grp where suiv2_utilisateur_no = '$num'");
                  $effacer_act = mysql_query("delete from suivi1_$id_grp where suivi_utilisateur_no = '$num'");
                  $effacer_act = mysql_query("delete from scorm_util_module_$id_grp where suivi_utilisateur_no = '$num'");
              $gp++;
            }
         }
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
         $effacer_blogShr = mysql_query("delete from blogshare where bgshr_auteur_no = '$num'");
         $effacer_stars = mysql_query("delete from starating where starate_auteur_no = '$num'");
         $effacer_comments = mysql_query("delete from commentaires where com_auteur_no = '$num'");
         $effacer_Fmod = mysql_query("delete from forums_modules where fm_auteur_no = '$num'");
         $effacer_trac = mysql_query("delete from traceur where traceur_util_no = '$num'");
         $nom_num  = GetdataField ($connect,"select util_nom_lb from utilisateur where util_cdn='$num'","util_nom_lb");
         $prenom_num  = GetdataField ($connect,"select util_prenom_lb from utilisateur where util_cdn='$num'","util_prenom_lb");
         $effacer = mysql_query("delete from utilisateur where util_cdn=$num");
         $dpass++;
         if ($dpass > 1)
            $mess_notif .= "<br />";
         $mess_notif .= "$mess_admin_sup_fiche_deb $prenom_num $nom_num $mess_admin_sup_fiche_fin";

      }
    $ia++;
   }
   $id_grp = -1;
}
// Affichage principal
include 'style.inc.php';
?>
<script type="text/javascript" src="OutilsJs/lib/ajax/ajax_cms.js"></script>
<div id="affiche" class="Status"></div>
<div id="mon_contenu" class="cms"
        onClick="javascript:$(document).ready(function() {if ($.browser.msie) {$('#mon_contenu').hide();}else{$('#mon_contenu').hide('slow');}})"
        <?php echo "title=\"$mess_clkF\">";?>
</div>
<?php
if ($annu == 'APPRENANT' && isset($message_envoye) && $message_envoye != '')
   $suite =  " : ".$message_envoye;
if ($annu == 'APPRENANT' && (!isset($message_envoye) || (isset($message_envoye) && $message_envoye == '')))
   $suite =  " : ".strtolower($mes_des_app);
if ($annu == 'RESPONSABLE_FORMATION')
   $suite = " : ".strtolower($mes_des_rf);
if ($annu == 'FORMATEUR_REFERENT')
   $suite = " : ".strtolower($mes_des_fr);
if ($annu == 'TUTEUR')
   $suite = " : ".strtolower($mes_des_tut);
if ($annu == 'ADMINISTRATEUR')
   $suite = " : ".strtolower($mes_des_adm);
if ($mess_aff != "")
   $suite .="<BR><FONT COLOR=white size=2>$mess_aff</FONT>";

if ($annu == "APPRENANT")
{
   $grostitre = "$mess_menu_gest_util $suite";
   if ($notification != "")
     echo "<CENTER><TABLE width='98%'><TR><TD colspan='2' align='middle'><FONT COLOR=white size='2'>$notification</FONT></TD></TR>";
   else
     echo "<CENTER><TABLE width='98%'>";
}
?>
<SCRIPT language=JavaScript>
    function checkForm(frm) {
      var ErrMsg = "<?php echo $mess_info_no;?>\n";
      var lenInit = ErrMsg.length;
      if (isEmpty(frm.search)== true)
        ErrMsg += ' - <?php echo $mess_admin_nom;?>\n';
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
if (isset($annu) && $annu == "APPRENANT" && ($typ_user == "ADMINISTRATEUR" || $typ_user == "RESPONSABLE_FORMATION"))
{
   echo "<TR><TD align='middle'>";
   if ($via_menu == 1)// quand on vient du menu Communication>>Annuaire>>des apprenants pour n'afficher que le déroulant qui permet de choisir entre les groupes d'appartenance
     echo "<center><TABLE border='0' width='98%'><TR><TD align='center' nowrap>";
   if ($via_menu == 1){// quand on vien du menu Communication>>Annuaire>>des apprenants On ferme dès qu'on a affiché le déroulant
      exit();
   }
}
if (isset($mess_notif) && $mess_notif != '')
{
      echo notifier($mess_notif);
}
// Procédure d'affichage des utilisateurs
  $bgcolor2 = '#2B677A';
  $bgcolor1 = '#F4F4F4';
  if ($annu == 'APPRENANT')
  {
    $resp_grp = GetDataField ($connect,"select grp_resp_no from groupe where grp_cdn='$id_grp'","grp_resp_no");
    if ($resp_grp == $id_user)
      $affiche_droit = 1;
  }
  if ($annu != 'APPRENANT' && $typ_user != 'APPRENANT')
  {
    $grostitre .= "<CENTER><FONT COLOR=white size='3'><b>$mess_menu_gest_util $suite</B></FONT>";
    if ($notification != "")
       echo "<FONT COLOR=white size='2'>$notification</FONT><P>";
  }
  if ($typ_user == 'APPRENANT')
  {
   $req_grp = mysql_query("select utilgr_groupe_no from utilisateur_groupe where utilgr_utilisateur_no = $id_user");
   $nomb_grp = mysql_num_rows($req_grp);
   if ($nomb_grp == 0)
   {
     $lien = "vide.php?ret=accueil&titre=$mess_menu_gest_util&contenu=$mess_admin_no_grp";
     $lien = urlencode($lien);
     echo "<script language=\"JavaScript\">";
     echo "document.location.replace(\"trace.php?link=$lien\")";
     echo "</script>";
     exit();
   }
    $num_grp =  GetDataField ($connect,"select utilgr_groupe_no from utilisateur_groupe where utilgr_utilisateur_no = $id_user","utilgr_groupe_no");
    $nom_grp = GetDataField ($connect,"select grp_nom_lb  from groupe where grp_cdn='$num_grp'","grp_nom_lb");
    $grostitre .= "$mess_menu_gest_util $mess_menu_gestion_grp $nom_grp";
    echo "<CENTER><FONT COLOR=white size='2'>$mess_admin_liste_app</FONT><P>";
  }
  ?>
        <script language="JavaScript" type="text/javascript">        <!--
        function CheckAll() {
          for (var j = 0; j < document.formx.elements.length; j++) {
            if(document.formx.elements[j].type == 'checkbox'){
               document.formx.elements[j].checked = !(document.formx.elements[j].checked);
            }
          }
        }
        //--></script>
     <?php
     entete_simple($grostitre);
     if ($typ_user == "RESPONSABLE_FORMATION" || $typ_user == "ADMINISTRATEUR"){
        $discrimin = ($annu == 'APPRENANT') ? '&id_grp=-1' : '';
        $lien_search="admin.php?annu=$annu".$discrimin."&entantqueresp=$entantqueresp";
     }
     echo "<tr><td colspan='2'>";
     if ($typ_user == "ADMINISTRATEUR")
     {
       $lien = "message.php?type=tous&keepThis=true&TB_iframe=true&height=455&width=700";
       $lien = urlencode($lien);
       $titre = "$mess_alert $pour ".strtolower($mess_menu_mail_tous);
       echo "<div id='form' style=\"float:left;padding-left:3px;padding-right:8px;\">".
            "<div id ='bout_msg' style='margin-top:0px;'>".
            "<A HREF=\"trace.php?link=$lien\" class='thickbox' style='color:fff;' ".
            "onMouseOver=\"\$(this).css('color','#D45211');\" onMouseOut=\"\$(this).css('color','#ffffff');\" title=\"$titre\">";
       echo "Annonce</A></div></div><div style=\"float:left;padding-right:8px;\">".
            "<a href='admin/ChartsStatements.php?who=admin".TinCanTeach ('formateur|0|0|0|0',$adresse_http.'/admin/ChartsStatements.php?who=admin',$adresse_http.'/Suivi')."' target='_blank' class='bouton_new' ".
            bulle("Suivi de tous les usagers dans le LRS -Learning Record Storage- de Formagri","","RIGHT","BELOW",210).
            "Tracking xAPI</a></div>";
       echo aide_div("suivi_inscription",0,0,0,0)."</td></tr>";
     }
     else
        echo aide_simple("inscrits")."</td></tr>";
     echo "<tr><td colspan='2'>";
     echo "<TABLE cellspacing='1' cellpadding='4' border='0' width='98%'><TR><TD colspan=2 style=\"height:30px;padding-top:10px;\">";
     echo "<div id='search' style=\"float:left;margin-right:10px;\">";
     echo "$mess_rech_nom_adm</div><div id='form1' style=\"float:left;margin-right:40px;\"><form name='form1' action='$lien_search' method='POST'>";
     echo "<INPUT type='text' class='INPUT' style=\"float:left;margin-right:5px;\" name='search' size='20'>";
     echo "<A HREF=\"javascript:checkForm(document.form1);\" class='bouton_new' style=\"float:left;margin-right:10px;\"> Ok </A></form></div>";
     echo "<div id='form_who' style=\"float:left;margin-right:10px;\">";
     echo "<form name='form'>";
     echo "<SELECT name='select' class='SELECT' onChange=\"javascript:appel_w(form.select.options[selectedIndex].value);\">";
     if ($id_grp > 0 && $message_envoye == "" && $annu == 'APPRENANT')
     {
         $nom_grp = GetDataField ($connect,"select grp_nom_lb from groupe where grp_cdn=$id_grp","grp_nom_lb");
         $message_envoye = "$mess_suite_ap_grp $nom_grp";
         echo "<OPTION>$message_envoye</OPTION>";
     }
     elseif ($message_envoye == "" && $annu == 'TUTEUR')
     {
         $message_envoye = "$mes_des_tut";
         echo "<OPTION>$message_envoye</OPTION>";
     }
     elseif ($message_envoye == "" && $annu == 'FORMATEUR_REFERENT')
     {
         $message_envoye = "$mes_des_fr";
         echo "<OPTION>$message_envoye</OPTION>";
     }
     elseif ($message_envoye == "" && $annu == 'RESPONSABLE_FORMATION')
     {
         $message_envoye = "$mes_des_rf";
         echo "<OPTION>$message_envoye</OPTION>";
     }
     elseif ($message_envoye == "" && $annu == 'ADMINISTRATEUR' && $typ_user == 'ADMINISTRATEUR')
     {
         $message_envoye = "$mes_des_adm";
         echo "<OPTION>$message_envoye</OPTION>";
     }else
         echo "<OPTION>- - - -Afficher - - - -</OPTION>";
     $message_envoye = $mess_menu_mail_app;//Ce qui apparait par défaut dans le déroulant
     if ($typ_user == "ADMINISTRATEUR")
     {
        $lien_adm="admin.php?annu=ADMINISTRATEUR&entantqueresp=$entantqueresp&message_envoye=$message_envoye";
        $lien_adm = urlencode($lien_adm);
        echo "<OPTION value='trace.php?link=$lien_adm'>$mes_des_adm</OPTION>";
     }
     $lien_rf="admin.php?annu=RESPONSABLE_FORMATION&entantqueresp=$entantqueresp&message_envoye=$message_envoye";
     $lien_rf = urlencode($lien_rf);
     echo "<OPTION value='trace.php?link=$lien_rf'>$mes_des_rf</OPTION>";
     $lien_fr="admin.php?annu=FORMATEUR_REFERENT&entantqueresp=$entantqueresp&message_envoye=$message_envoye";
     $lien_fr = urlencode($lien_fr);
     echo "<OPTION value='trace.php?link=$lien_fr'>$mes_des_fr</OPTION>";
     $lien_tut="admin.php?annu=TUTEUR&entantqueresp=$entantqueresp&message_envoye=$message_envoye";
     $lien_tut = urlencode($lien_tut);
     echo "<OPTION value='trace.php?link=$lien_tut'>$mes_des_tut</OPTION>";
     $lien_app="admin.php?annu=APPRENANT&id_grp=-1&entantqueresp=$entantqueresp&message_envoye=$message_envoye";
     $lien_app = urlencode($lien_app);
     echo "<OPTION value='trace.php?link=$lien_app'>$mess_menu_mail_app</OPTION>";
     $message_envoye = "$mess_menu_mail_app  $mess_menu_app_ss_aff";
     $lien="admin.php?annu=APPRENANT&id_grp=0&entantqueresp=$entantqueresp&message_envoye=$message_envoye";
     $lien = urlencode($lien);
     if ($typ_user == "RESPONSABLE_FORMATION" || ($typ_user == "ADMINISTRATEUR" && $entantqueresp == 1))
        $non_grp = mysql_query("select distinct util_cdn,util_email_lb,util_nom_lb,util_prenom_lb FROM utilisateur WHERE util_typutil_lb = '$annu' AND util_auteur_no = $id_user");
     elseif ($typ_user == "ADMINISTRATEUR" && $entantqueresp != 1)
        $non_grp = mysql_query("select distinct util_cdn,util_email_lb,util_nom_lb,util_prenom_lb FROM utilisateur where util_typutil_lb = '$annu'");
     $nb_non_grp = mysql_num_rows($non_grp);
     if ($nb_non_grp > 0 && $annu == 'APPRENANT')
        echo "<OPTION value='trace.php?link=$lien'>$mess_menu_mail_app $mess_menu_app_ss_aff</OPTION>";
     else
        echo "<OPTION value='trace.php?link=$lien'>$message_envoye</OPTION>";
//   if ($annu == 'APPRENANT'){
     if ($typ_user == "ADMINISTRATEUR" && $entantqueresp != 1)
        $grp_req = mysql_query("SELECT * FROM groupe ORDER BY grp_nom_lb ASC");
     else
        $grp_req = mysql_query("SELECT * FROM groupe WHERE (grp_publique_on=1 || (grp_publique_on=0 AND grp_resp_no = $id_user)) AND grp_flag_on=1 ORDER BY grp_nom_lb ASC");
     $nbr = mysql_num_rows($grp_req);
     if ($nbr > 0)
     {
       $i = 0;
       while ($i < $nbr)
       {
        $message_envoye = "";
        $id_group = mysql_result($grp_req,$i,"grp_cdn");
        $nb_user_grp = mysql_result(mysql_query("SELECT count(utilgr_utilisateur_no) FROM utilisateur_groupe where utilgr_groupe_no = $id_group"),0);
        if ($nb_user_grp > 0)
        {
          $nom_grp = mysql_result($grp_req,$i,"grp_nom_lb");
          if ($typ_user == "ADMINISTRATEUR" && $entantqueresp != 1)
             $nb_insc_grp = mysql_result(mysql_query("SELECT count(utilgr_utilisateur_no) FROM utilisateur_groupe where utilgr_groupe_no = $id_group"),0);
          else
             $nb_insc_grp = mysql_result(mysql_query("SELECT count(utilgr_utilisateur_no) FROM utilisateur_groupe,utilisateur where utilgr_groupe_no = $id_group and utilgr_utilisateur_no=util_cdn and util_auteur_no=$id_user"),0);
          if ($nb_insc_grp > 0)
          {
             $message_envoye = "$mess_suite_ap_grp $nom_grp";
             $lien="admin.php?annu=APPRENANT&id_grp=$id_group&presc=1&entantqueresp=$entantqueresp&message_envoye1=$message_envoye&message_envoye=$message_envoye";
             $lien = urlencode($lien);
             echo "<OPTION value='trace.php?link=$lien'>$message_envoye</OPTION>";
             $message_envoye = "";
          }
        }
       $i++;
       }
     }
     echo "</SELECT></div></form>";
     echo "</TD></TR></TABLE>";
  if ($typ_user == "ADMINISTRATEUR" && $entantqueresp != 1)
  {
     $champ_rech = "util_typutil_lb = '$annu'";
     if (isset($search) && $search != '')
         $champ_rech .= " AND util_nom_lb like '$search%'";
     $requete = "select distinct util_cdn,util_email_lb,util_nom_lb,util_prenom_lb FROM utilisateur WHERE $champ_rech order by util_nom_lb ASC";
     $non_grp = mysql_query($requete);
  }
  else
  {
     if ($id_grp > 0)
     {
       $champ_rech = "util_typutil_lb = '$annu' AND util_auteur_no = $id_user AND utilisateur.util_cdn = utilisateur_groupe.utilgr_utilisateur_no AND utilisateur_groupe.utilgr_groupe_no = $id_grp";
       if (isset($search) && $search != '')
          $champ_rech .= " AND util_nom_lb like '$search%'";
       $requete = "select distinct utilisateur.util_cdn,utilisateur.util_email_lb,utilisateur.util_nom_lb,utilisateur.util_prenom_lb FROM utilisateur,utilisateur_groupe WHERE $champ_rech  order by utilisateur.util_nom_lb ASC";
       $non_grp = mysql_query($requete);
     }
     else
     {
       $champ_rech = "util_typutil_lb = '$annu' AND util_auteur_no = $id_user";
       if (isset($search) && $search != '')
          $champ_rech .= " AND util_nom_lb like '$search%'";
       $requete = "select distinct util_cdn,util_email_lb,util_nom_lb,util_prenom_lb FROM utilisateur WHERE $champ_rech order by util_nom_lb ASC";
       $non_grp = mysql_query($requete);
     }
  }
  $nb_non_grp = mysql_num_rows($non_grp);
  if ($nb_non_grp == 0)
  {
   if (isset($search) && $search != '')
       echo notifier($msgadm_sch_nmdb." : "."[$search] $mess_dans ".ucfirst(strtolower($annu))." <br />".$msgadm_nosch_dv);
    boutret(1,0);
    echo fin_tableau('');
    exit;
  }
  else
  {
     if (isset($search) && $search != '')
        echo notifier($nb_non_grp." ".strtolower($annu)."(s) ".$msgadm_sch_nb." [$search]");
  }
   echo "<TR><TD align='center'><TABLE bgColor='#FFFFFF' border='0' width='100%' cellpadding='0' cellspacing='0'><TR><TD>";
  echo "<TABLE width='100%' cellspacing=6 border='0'>";
  echo "<tr bgcolor=$bgcolor2>";
  echo "<td height='20' nowrap><FONT COLOR=white><b>$mess_admin_nom &nbsp;&nbsp;&nbsp;$mess_admin_prenom</b></FONT></td>";
  if (!$annu)
     echo "<td height='20'><FONT COLOR=white><b>$mess_admin_role</b></FONT></td>";
  echo "<td height='20'><FONT COLOR=white><b>$mess_admin_email</b></td>";
  $etat_mp = GetDataField($connect,"select param_etat_lb from param_foad where param_typ_lb='multi-centre'","param_etat_lb");
  if ($typ_user == 'ADMINISTRATEUR' || $typ_user == "RESPONSABLE_FORMATION")
  {
    echo "<TD height='20'><FONT COLOR=white><b>$mess_admin_tel</b></FONT></TD>";
    echo "<TD height='20'><FONT COLOR=white><b>$mess_admin_login</b></TD>";
    echo "<TD height='20' nowrap><TABLE cellpadding='0' cellspacing='2'><TR>";
    echo "<td nowrap width='49%'><FONT COLOR=white><b>$mess_passe </b></FONT></TD>";
    echo "<TD nowrap width='17%'><FONT COLOR=white><b>| $mess_codes_acces </b></FONT></TD>";
    echo "<TD nowrap width='17%'><FONT COLOR=white><b>| $mess_disc</b></FONT></TD>";
    if ($etat_mp == 'OUI')
       echo "<TD nowrap width='17%'><DIV id='mp'><FONT COLOR=white><b>| <A HREF=\"javascript:void(0);\" ".bulle($mess_titre_mp,"","LEFT","BELOW",250)."$mess_mp</A></DIV></TD>";
    else
       echo "<TD nowrap width='17%'>&nbsp;</TD>";
    echo "</tr></table></td>";
    echo "<TD height='20'><FONT COLOR=white><b>$mess_connect</b></FONT></TD>";
    echo "<TD height='20'><FONT COLOR=white><b>$mess_fiche_prof</b></FONT></TD>";
    if (($typ_user == 'ADMINISTRATEUR' && isset($entantqueresp) && $entantqueresp == 1) || $typ_user == 'RESPONSABLE_FORMATION')
       echo "<TD height='20' colspan='2' align='middle'>";
    else
       echo "<TD height='20' colspan='2'>";
    echo "<FONT COLOR=white><b>$mess_slct</b></FONT></TD>";
  }
  echo "</TR>";
  if ($typ_user == 'ADMINISTRATEUR' && (!isset($entantqueresp) || $entantqueresp != 1) && ($annu != "APPRENANT" || ($annu == "APPRENANT" && $id_grp < 0)))
  {
      if ($annu != "APPRENANT")
      {
        $affiche_ecran .= "<TR>";
        $affiche_ecran .= "<TD colspan=6 align='middle' valign='top'>&nbsp;</td>";
      }
      else
        $affiche_ecran .= "<TD colspan=3 align='middle' valign='top'>&nbsp;</td>";
      $affiche_ecran .= "<TD align='right' valign='top'></TD><TD align='right' valign='top'></TD>";
  }
  if (($typ_user == 'ADMINISTRATEUR' || $typ_user == "RESPONSABLE_FORMATION") && (!isset($entantqueresp) || $entantqueresp != 1) && ($annu != 'APPRENANT' || ($annu == 'APPRENANT' && $id_grp < 0 && $nb_non_grp > 0)))
  {
         if ($typ_user == "RESPONSABLE_FORMATION")
            $affiche_ecran .= "<TR><TD colspan=8 align='middle'>&nbsp;</td>";
         $affiche_ecran .= "<TD align='center' valign='top'><FORM name='formx' METHOD='POST'>";
         $affiche_ecran .= "<INPUT TYPE='checkbox' onClick=\"CheckAll();\" ".bulle($mess_inv_sel,"","LEFT","BELOW",200)."</TD>";
    $affiche_ecran .= "</TR>";
  }
  elseif (($typ_user == 'ADMINISTRATEUR' || $typ_user == "RESPONSABLE_FORMATION") && (!isset($entantqueresp) || $entantqueresp != 1) && $annu == 'APPRENANT' && $id_grp > -1 && $nb_non_grp > 0)
  {
         if ($id_grp == 0 )
            $affiche_ecran .= "<TD colspan = '3'>&nbsp;</TD>";
         $affiche_ecran .= "<TD colspan = '2'>&nbsp;</TD><TD align='center' valign='top'><FORM name='formx' METHOD='POST'>";
         $affiche_ecran .= "<INPUT TYPE='checkbox' onClick=\"CheckAll();\" ".bulle($mess_inv_sel,"","LEFT","BELOW",200)."</TD>";
    $affiche_ecran .= "</TR>";
  }
  elseif (($typ_user == "RESPONSABLE_FORMATION" || ($typ_user == 'ADMINISTRATEUR' && isset($entantqueresp) && $entantqueresp == 1)) && $annu == "APPRENANT" && $nb_non_grp > 0)
  {
         if (($typ_user == "RESPONSABLE_FORMATION" || $typ_user == 'ADMINISTRATEUR') && $id_grp < 0)
            $nbcolonnes = 5;
         else
            $nbcolonnes = 2;
         $affiche_ecran .= " <TD colspan = '$nbcolonnes'>&nbsp;</TD><TD align='center' valign='top'><FORM name='formx' METHOD='POST'>";
         $affiche_ecran .= "<INPUT TYPE='checkbox' onClick=\"CheckAll();\" ".bulle($mess_inv_sel,"","LEFT","BELOW",200)."</TD>";
    $affiche_ecran .= "</TR>";
  }
if ($typ_user == "ADMINISTRATEUR" || $typ_user == "RESPONSABLE_FORMATION")
{
  $req_gp = mysql_query("select * from groupe ORDER BY grp_nom_lb");
  $nomb_gp = mysql_num_rows($req_gp);
  $ii = 0;
  if ($annu == "APPRENANT" && $id_grp < 0 && $nomb_gp > 0)
  {
    if ($typ_user == "ADMINISTRATEUR" && (!isset($entantqueresp) || $entantqueresp != 1))
    {
       $champ_rech = "util_typutil_lb = '$annu'";
       if (isset($search) && $search != '')
          $champ_rech .= " AND util_nom_lb like '$search%'";
       $non_grp = mysql_query("select distinct util_cdn,util_email_lb,util_nom_lb,util_prenom_lb FROM utilisateur where $champ_rech order by util_nom_lb ASC");
    }
    else
    {
       $champ_rech = "util_typutil_lb = '$annu' AND util_auteur_no = $id_user";
       if (isset($search) && $search != '')
          $champ_rech .= " AND util_nom_lb like '$search%'";
       $non_grp = mysql_query("select distinct util_cdn,util_email_lb,util_nom_lb,util_prenom_lb FROM utilisateur where $champ_rech order by util_nom_lb ASC");
    }
    $nb_non_grp = mysql_num_rows($non_grp);
    $compteur = 0;
    while ($ii < $nb_non_grp)
    {
      $num = mysql_result($non_grp,$ii,"util_cdn");
      $req_grp = mysql_query("select count(utilgr_groupe_no) from utilisateur_groupe where utilgr_utilisateur_no = $num");
      $nomb_grp = mysql_result($req_grp,0);
      if ($nomb_grp == 0)
      {
          $req_util = mysql_query("select * from utilisateur where util_cdn = $num");
          $nom = mysql_result($req_util,0,"util_nom_lb");
          $prenom = mysql_result($req_util,0,"util_prenom_lb");
          $email = mysql_result($req_util,0,"util_email_lb");
          $logue = mysql_result($req_util,0,"util_login_lb");
          $logue_cas = mysql_result($req_util,0,"util_logincas_lb");
          $util_auteur = mysql_result($req_util,0,"util_auteur_no");
          $photo = mysql_result($req_util,0,"util_photo_lb");
          $passe = mysql_result($req_util,0,"util_motpasse_lb");
          $tel = mysql_result($req_util,0,"util_tel_lb");
          $webmail = mysql_result($req_util,0,"util_urlmail_lb");
          if ($webmail == "http://")
            $webmail= "";
          $compteur++;
          if ($compteur == 1)
          {
            $titre_rub = $mpr_list_form;
            $titre_rub .= " ".$mess_menu_app_ss_aff;
            if (!isset($passe_ecran) || $passe_ecran == 0)
                echo "<TR><TD colspan=3 align='left'><b>$titre_rub</b></TD>$affiche_ecran";
            $passe_ecran++;
            echo "</TR>";
          }
          echo couleur_tr($compteur,30);
          $lien = "prescription.php?id_util=$num&identite=1&affiche_fiche_app=1";
          $lien = urlencode($lien);
          echo "<TD height='20' align='left' nowrap><DIV id='sequence' style='float:left;'><A HREF=\"javascript:void(0);\" ".
               "onclick=\"window.open('trace.php?link=$lien','','width=680,height=380,scrollbars=yes,resizable=yes,status=no')\"title=\"$mess_suite_fp\">";

          if ($util_auteur == $id_user)
             echo "<IMG SRC=\"images/gest_parc/icofeuil.gif\" border='0'>";
          echo "&nbsp;$nom $prenom</A></DIV>";
          echo msgInst($num,"apprenant car il est connecté");
          echo "</TD>";
          $lien = "mail.php?contacter=1&num=$num";
          $lien = urlencode($lien);
          echo "<TD><DIV id='sequence'><A HREF=\"javascript:void(0);\" onclick=\"window.open('trace.php?link=$lien','','width=620,height=520,resizable=yes,status=no')\" ".bulle($mess_ecrire,"","LEFT","BELOW",150)."$email</A></DIV></TD>";
          echo" <td height='20' align='left'>$tel</td>
          <td height='20' align='left'>$logue</td>
          <td nowrap><table cellpadding='2' cellspacing='0' width='100%' border='0'><TR>";
          echo "<TD height='20' align='left' width='31%'>$passe</TD>";
          //$lien = "admin.php?clef=1&num=$num&annu=$annu&id_grp=$id_grp&entantqueresp=$entantqueresp";
          //$lien = urlencode($lien);
          $verifier = verifie_email($email);
          if ($email != "" && $verifier == TRUE)
          {
          $titre = "$mess_envoi_codes $nom $prenom";
             echo "<TD width='17%' align=center><A href=\"#\" onclick=\"javascript:sendData('','admin/clef.php?num=$num','post');\" ".
             bulle($titre,"","LEFT","BELOW",200)."<IMG SRC=\"images/complement/icocle_envoi.gif\" height=\"16\" width=\"26\" BORDER=0></A></TD>";
          $titre = "";
          }
          else
             echo "<TD width='17%'>&nbsp;</TD>";
          $lien = "taille.php?qui=autre&logue=$logue&num=$num";
          $lien = urlencode($lien);
          echo "<TD width='17%' align=center><A href=\"javascript:void(0);\" target='main' ".
               "onclick=\"javascript:window.open('trace.php?link=$lien', 'Espace', 'height=150,width=350,resizable=yes');\"".
               bulle($taille_serveur,"","LEFT","BELOW",150)."<IMG SRC=\"images/repertoire/icodisket.gif\" height=\"15\" width=\"15\" BORDER=0></A></TD>";
          $AjoutBulle = "<p>$mess_multi_pf";
          $lien = "admin_gere.php?bprea=1&num=$num&id_grp=$id_grp";
          $lien = urlencode($lien);
          if ($etat_mp == 'OUI')
            echo "<TD width='17%'><A href=\"trace.php?link=$lien\" target='main' ".
                 bulle($AjoutBulle,"","LEFT","BELOW",270)."$mess_mp</A></TD>";
          echo "</TR></TABLE></TD>";
          $titre = '';
          $titre = "$mess_admin_bilan_connect $nom $prenom";
          $lien = "admin/connections.php?connection=1&logue=$logue&annu=$annu&id_grp=$id_grp&par_mois=1&id_util=$num&entantqueresp=$entantqueresp";
          $lien = urlencode($lien);
          echo "<td height='20' align='center'><a href=\"trace.php?link=$lien\"  target='main'".bulle($titre,"","LEFT","BELOW",250).
               "<IMG SRC=\"images/disconnect.gif\" ALT=\"\" BORDER=0></A></td>";
          $titre = '';
          $lien = "admin/modifiche.php?modifier=1&num=$num&annu=$annu&id_grp=$id_grp&entantqueresp=$entantqueresp";
          $lien = urlencode($lien);
          $titre = "$mess_admin_modif_profil $nom $prenom";
          if ($photo != "")
             $image = "<IMG SRC=\"images/$photo\" width='19' height='25' border='0'>";
          else
             $image = "<IMG SRC=\"images/repertoire/icoptisilhouet.gif\" width='19' height='25' border='0'>";
          echo "<td height='20' align='middle'><a href=\"trace.php?link=$lien\" target='main'".bulle($titre,"","LEFT","BELOW",150).
               "$image</A></td>";
          $titre = '';
          $lien = "admin_gere.php?supprimer=1&util=$type&num=$num&annu=$annu&id_grp=$id_grp&entantqueresp=$entantqueresp";
          $lien = urlencode($lien);
          echo "<td height='20' align='middle'><a href=\"javascript:void(0);\" onclick=\"javascript:return(confm('trace.php?link=$lien'));\" ".
               bulle($mess_admin_sup_util,"","LEFT","BELOW",150).
               "<IMG SRC=\"images/messagerie/icopoubelressour.gif\" BORDER=0></A></td>";
          if ($typ_user == 'ADMINISTRATEUR' || $typ_user == "RESPONSABLE_FORMATION")
          {
             $liste_exp[$num] = "$nom;$prenom;$email;$tel;$webmail;$logue_cas";
             $liste1_exp[$num] = "$nom;$prenom;$logue;$passe;$email;$tel;$webmail;$logue_cas";
             $affiche_title = addslashes($mess_export_app);
             echo "<INPUT TYPE='HIDDEN' NAME='liste_exp[$num]' VALUE =\"$liste_exp[$num]\">";
             echo "<INPUT TYPE='HIDDEN' NAME='entantqueresp' VALUE =\"$entantqueresp\">";
             echo "<INPUT TYPE='HIDDEN' NAME='annu' VALUE =\"$annu\">";
             echo "<INPUT TYPE='HIDDEN' NAME='id_grp' VALUE =\"$id_grp\">";
             echo "<INPUT TYPE='HIDDEN' NAME='liste1_exp[$num]' VALUE =\"$liste1_exp[$num]\">";
             echo "<TD height='20' align='middle'><INPUT TYPE='checkbox' NAME=exp_app[$num]' ".bulle($affiche_title,"","LEFT","BELOW",110)."</TD>";
           }
           echo "</TR>";
      }
      else
      {
          $ii++;
          continue;
      }// finif ($nomb_grp == 0){
      $ii++;
    }// fin while ($ii < $nb_non_grp){
  }// if ($annu == "APPRENANT" && $id_grp < 0 && $nomb_gp > 0){
  if ($typ_user == "ADMINISTRATEUR" && (!isset($entantqueresp) || $entantqueresp != 1) && $annu == "APPRENANT" && $ii > 0)
     echo "<TR><TD colspan='10' align='center'>&nbsp;</TD></TR>";
}
if ($typ_user == "ADMINISTRATEUR" || $typ_user == "RESPONSABLE_FORMATION")
{
  if ($annu == "APPRENANT")
  {
    $req_gp = mysql_query("select * from groupe");
    $nomb_gp = mysql_num_rows($req_gp);
    if ($nomb_gp > 0)
    {
      if ($id_grp < 0 && $typ_user == "ADMINISTRATEUR" && (!isset($entantqueresp) || $entantqueresp != 1) && (isset($presc) && $presc == 1))
      {
        $champ_rech = "(utilisateur.util_typutil_lb = '$annu' AND utilisateur.util_cdn = utilisateur_groupe.utilgr_utilisateur_no AND utilisateur_groupe.utilgr_groupe_no = groupe.grp_cdn) and utilisateur.util_auteur_no = $id_user";
        if (isset($search) && $search != '')
            $champ_rech .= " AND util_nom_lb like '$search%'";
        $liste=mysql_query("select distinct utilisateur.util_cdn from utilisateur,utilisateur_groupe,groupe where $champ_rech order by utilisateur.util_nom_lb,utilisateur.util_prenom_lb ASC");
      }
      elseif ($id_grp < 0 && $typ_user == "ADMINISTRATEUR"  && (!isset($entantqueresp) || $entantqueresp != 1) && (!isset($presc) || $presc != 1))
      {
        $champ_rech = "(utilisateur.util_typutil_lb = '$annu' AND utilisateur.util_cdn = utilisateur_groupe.utilgr_utilisateur_no AND utilisateur_groupe.utilgr_groupe_no = groupe.grp_cdn)";
        if (isset($search) && $search != '')
            $champ_rech .= " AND util_nom_lb like '$search%'";
        $liste=mysql_query("select distinct utilisateur.util_cdn from utilisateur,utilisateur_groupe,groupe where $champ_rech order by utilisateur.util_nom_lb,utilisateur.util_prenom_lb ASC");
      }
      elseif ($id_grp < 0 && ($typ_user == "RESPONSABLE_FORMATION" || ($typ_user == "ADMINISTRATEUR" && $entantqueresp == 1)))
      {
        $champ_rech = "(utilisateur.util_typutil_lb = '$annu' AND utilisateur.util_cdn = utilisateur_groupe.utilgr_utilisateur_no AND utilisateur_groupe.utilgr_groupe_no = groupe.grp_cdn) and utilisateur.util_auteur_no = $id_user";
        if (isset($search) && $search != '')
            $champ_rech .= " AND util_nom_lb like '$search%'";
        $liste=mysql_query("select distinct utilisateur.util_cdn from utilisateur,utilisateur_groupe,groupe where $champ_rech order by utilisateur.util_nom_lb,utilisateur.util_prenom_lb ASC");
      }
      if ($id_grp > 0 && $resp_grp == $id_user && ($typ_user == "RESPONSABLE_FORMATION" || ($typ_user == "ADMINISTRATEUR" && $entantqueresp == 1)))
      {
        $champ_rech = "(utilisateur_groupe.utilgr_groupe_no = $id_grp AND utilisateur_groupe.utilgr_utilisateur_no = utilisateur.util_cdn)";
        if (isset($search) && $search != '')
            $champ_rech .= " AND util_nom_lb like '$search%'";
        $liste=mysql_query("select distinct utilisateur.util_cdn FROM utilisateur,utilisateur_groupe where $champ_rech order by utilisateur.util_nom_lb,utilisateur.util_prenom_lb ASC");
      }
      elseif ($id_grp > 0 && $typ_user == "ADMINISTRATEUR" && (!isset($entantqueresp) || $entantqueresp != 1))
      {
        $champ_rech = "(utilisateur_groupe.utilgr_groupe_no = $id_grp AND utilisateur_groupe.utilgr_utilisateur_no = utilisateur.util_cdn)";
        if (isset($search) && $search != '')
            $champ_rech .= " AND util_nom_lb like '$search%'";
        $liste=mysql_query("select distinct utilisateur.util_cdn FROM utilisateur,utilisateur_groupe where $champ_rech order by utilisateur.util_nom_lb,utilisateur.util_prenom_lb ASC");
      }
      elseif ($id_grp > 0 && $resp_grp != $id_user && ($typ_user == "RESPONSABLE_FORMATION" || ($typ_user == "ADMINISTRATEUR" && $entantqueresp == 1)))
      {
        $champ_rech = "(utilisateur_groupe.utilgr_groupe_no = $id_grp AND utilisateur_groupe.utilgr_utilisateur_no = utilisateur.util_cdn) and utilisateur.util_auteur_no = $id_user";
        if (isset($search) && $search != '')
            $champ_rech .= " AND util_nom_lb like '$search%'";
        $liste=mysql_query("select distinct utilisateur.util_cdn FROM utilisateur,utilisateur_groupe where $champ_rech order by utilisateur.util_nom_lb,utilisateur.util_prenom_lb ASC");
      }
      if ($id_grp == 0 && $typ_user == "ADMINISTRATEUR" && (!isset($entantqueresp) || $entantqueresp != 1))
      {
        $champ_rech = "util_typutil_lb = '$annu'";
        if (isset($search) && $search != '')
            $champ_rech .= " AND util_nom_lb like '$search%'";
        $liste=mysql_query("select distinct util_cdn FROM utilisateur where $champ_rech order by util_nom_lb,utilisateur.util_prenom_lb ASC");
      }
      elseif ($id_grp == 0 && ($typ_user == "RESPONSABLE_FORMATION" || ($typ_user == "ADMINISTRATEUR" && $entantqueresp == 1)))
      {
        $champ_rech = "util_typutil_lb = '$annu' AND util_auteur_no = $id_user";
        if (isset($search) && $search != '')
            $champ_rech .= " AND util_nom_lb like '$search%'";
        $liste=mysql_query("select distinct util_cdn FROM utilisateur where $champ_rech order by util_nom_lb,utilisateur.util_prenom_lb ASC");
      }
    }
    else
    {
      $champ_rech = "util_typutil_lb = '$annu'";
      if (isset($search) && $search != '')
         $champ_rech .= " AND util_nom_lb like '$search%'";
      $liste=mysql_query("select distinct utilisateur.util_cdn from utilisateur where $champ_rech order by util_nom_lb,utilisateur.util_prenom_lb ASC");
    }
  }
  else
  {
   //    $champ_rech = ($typ_user == "RESPONSABLE_FORMATION") ? "util_typutil_lb = '$annu' and  util_auteur_no = $id_user" : "util_typutil_lb = '$annu'";
    $champ_rech = "util_typutil_lb = '$annu'";
    if (isset($search) && $search != '')
       $champ_rech .= " AND util_nom_lb like '$search%'";
    $liste=mysql_query("select utilisateur.util_cdn from utilisateur where $champ_rech order by util_nom_lb,utilisateur.util_prenom_lb ASC");
  }

}
$nbr = mysql_num_rows($liste);
$i = 0;
$j = 0;
$compteur = 0;
while ($i < $nbr)
{
  $affiche_droit = 0;
  $grp1 = $grp;
  $type1 = $type;
  $num = mysql_result($liste,$i,"util_cdn");
  $req_util = mysql_query("select * from utilisateur where util_cdn = $num");
  $nom = mysql_result($req_util,0,"util_nom_lb");
  $prenom = mysql_result($req_util,0,"util_prenom_lb");
  $email = mysql_result($req_util,0,"util_email_lb");
  $logue = mysql_result($req_util,0,"util_login_lb");
  $logue_cas = mysql_result($req_util,0,"util_logincas_lb");
  $inscripteur = mysql_result($req_util,0,"util_auteur_no");
  $photo = mysql_result($req_util,0,"util_photo_lb");
  $passe = mysql_result($req_util,0,"util_motpasse_lb");
  $tel = mysql_result($req_util,0,"util_tel_lb");
  $webmail = mysql_result($req_util,0,"util_urlmail_lb");
  if ($webmail == "http://")
     $webmail= "";
  $type=GetDataField ($connect,"select util_typutil_lb from utilisateur where util_cdn='$num'","util_typutil_lb");
  if ($type == "APPRENANT" && ($typ_user == 'ADMINISTRATEUR' || $typ_user == 'RESPONSABLE_FORMATION'))
  {
    if ($inscripteur == $id_user)
       $affiche_droit = 1;
    $req_grp = mysql_query("select utilgr_groupe_no from utilisateur_groupe where utilgr_utilisateur_no = $num");
    $nomb_grp = mysql_num_rows($req_grp);
    if ($nomb_grp != 0 && $id_grp != 0)
    {
      $num_grp = mysql_result($req_grp,0,"utilgr_groupe_no");
      $grp =GetDataField ($connect,"select grp_nom_lb from groupe where grp_cdn = $num_grp","grp_nom_lb");
      $resp_grp = GetDataField ($connect,"select grp_resp_no from groupe where grp_cdn='$num_grp'","grp_resp_no");
    }
    elseif($nomb_grp != 0 && $id_grp == 0)
    {
      $i++;
      continue;
    }
    elseif($nomb_grp == 0)
    {
      $grp="";
    }
  }
  if ($compteur == 0)
  {
     if ($annu == "APPRENANT" && $id_grp < 0)
     {
        $titre_rub = $mess_insc_1f;
        echo "<TR><TD colspan=3 align='left'><b>$titre_rub</b></td>";
     }
     elseif ($annu == "APPRENANT" && ($id_grp == 0 || $nomb_grp == 0))
     {
        $titre_rub = $mpr_list_form." ".$mess_menu_app_ss_aff;
        echo "<TR><TD colspan=3 align='left'><b>$titre_rub</b></td>";
     }
     else
        echo "<TR><TD colspan=6 align='left'>";
     if ($passe_ecran == 0 || !isset($passe_ecran))
       echo $affiche_ecran;
  }
  if (!isset($search) || (isset($search) && $search == '') && $type == "APPRENANT" && (($id_grp > 0 && $passee == 0) ||
     ($id_grp < 0 && $passage == 0)) && $typ_user == 'RESPONSABLE_FORMATION' && ($affiche_droit == 1 && ($id_grp < 0 || $id_grp == 0)))
  {
      echo "<TR><TD colspan=2 align='left'><b>$message</b></td>";
      $passage++;
  }
  echo "</TR>";
  echo couleur_tr($compteur+1,30);
      $serie = "";
       $group_inscr = mysql_query("SELECT grp_cdn from groupe,utilisateur_groupe WHERE
                                   utilisateur_groupe.utilgr_utilisateur_no = $num AND
                                   utilisateur_groupe.utilgr_groupe_no = groupe.grp_cdn ORDER BY grp_nom_lb ASC");
       $nbr_grp_inscr = mysql_num_rows($group_inscr);
       if ($nbr_grp_inscr != 0)
       {
         $serie .="<B><FONT size=2>$nbr_grp_inscr $mess_grp_form_suiv </B></FONT>";
         $g = 0;
         while ($g < $nbr_grp_inscr)
         {
            $id_grp = mysql_result($group_inscr,$g,"grp_cdn");
            $nom_grp = GetDataField ($connect,"SELECT grp_nom_lb from groupe where grp_cdn  = $id_grp","grp_nom_lb");
            $serie .= "<LI>$nom_grp</LI>";
          $g++;
         }
       }
      $compteur++;
      $majuscule =$nom." ".$prenom;
      $lien = "prescription.php?id_util=$num&identite=1&affiche_fiche_app=1";
      $lien = urlencode($lien);
      echo "<TD height='20' align='left' nowrap><DIV id='sequence' style='float:left;'><A HREF=\"javascript:void(0);\" ".
               "onclick=\"window.open('trace.php?link=$lien','','width=680,height=380,scrollbars=yes,resizable=yes,status=no')\"";
      $serie_tot = '';
      if ($annu == "APPRENANT" && $nbr_grp_inscr > 0)
      {
         echo bulle ($serie,"","RIGHT","ABOVE",220);
      }
      else
      {
         if ($annu == 'TUTEUR')
            $serie_tot .= serie_tut($num);
         if ($annu == 'FORMATEUR_REFERENT'){
            $serie_tot .= serie_form($num);
            $serie_tot .= serie_tut($num);
         }
         if ($annu == 'RESPONSABLE_FORMATION' || $annu == 'ADMINISTRATEUR')
         {
            $serie_tot .= serie_resp($num);
            $serie_tot .= serie_presc($num);
            $serie_tot .= serie_form($num);
            $serie_tot .= serie_tut($num);
         }

         $serie_tot .= serie_sup($num);
         if ($serie_tot != '')
             echo bulle ($serie_tot,"","RIGHT","ABOVE",220);
         else
            echo " title=\"$mess_suite_fp\">";
      }
      if ($inscripteur == $id_user)
         echo "<IMG SRC=\"images/gest_parc/icofeuil.gif\" border='0'>";
      echo "&nbsp;$majuscule</A></DIV>";
      echo msgInst($num,strtolower($annu)." car il est connecté");
      echo "</TD>";
      if (!$annu)
         echo "<TD height='20' align='left'>$type</TD>";
      $lien = "mail.php?contacter=1&num=$num";
      $lien = urlencode($lien);
      echo "<TD><DIV id='sequence'><A HREF=\"javascript:void(0);\" ".
           "onclick=\"window.open('trace.php?link=$lien','','width=680,height=520,resizable=yes,status=no')\" ".
           "title='$mess_ecrire'>$email</A></DIV></TD>";
    if (($typ_user == 'ADMINISTRATEUR' || ($typ_user == "RESPONSABLE_FORMATION" && $inscripteur == $id_user)) ||
       ($type == 'APPRENANT' && $affiche_droit == 1 && $typ_user == 'RESPONSABLE_FORMATION'))
    {
      echo" <td height='20' align='left'>$tel</td>
      <td height='20' align='left'>$logue</td>
      <TD nowrap><TABLE cellspacing=2 width=100% border='0'><TR><TD height='20' align='left' width='31%'>";
      $nbr_control++;
      if ($annu == "ADMINISTRATEUR")
         echo "******</TD>";
      else
         echo "$passe</TD>";
          $verifier = verifie_email($email);
          if ($email != "" && $verifier == TRUE)
          {
             $titre = "$mess_envoi_codes $nom $prenom";
             echo "<TD width='17%' align=center><A href=\"#\" onclick=\"javascript:sendData('2','admin/clef.php?num=$num','post');\" ".
                   bulle($titre,"","LEFT","BELOW",250).
                  "<IMG SRC=\"images/complement/icocle_envoi.gif\" height=\"16\" width=\"26\" BORDER=0></A></TD>";
             $titre = '';
          }
          else
             echo "<TD width='17%'>&nbsp;</TD>";
      $lien = "taille.php?qui=autre&logue=$logue&num=$num";
      $lien = urlencode($lien);
      echo "<TD nowrap align='center' width='17%'><A href=\"javascript:void(0);\"  target='main' ".
           "onclick=\"javascript:window.open('trace.php?link=$lien', 'Espace', 'height=150,width=350,resizable=yes');\"".
           bulle($taille_serveur,"","LEFT","BELOW",100).
           "<IMG SRC=\"images/repertoire/icodisket.gif\" height=\"15\" width=\"15\" BORDER=0></A></TD>";
      $AjoutBulle = '';
      $tailleBulle = 300;
      $AjTBd = '';
      $AjTBd = "<span style='color:green;font_weight:bold;'> ***</span>";
      $CentresMulti = mysql_query('select distinct uc_centre_lb from user_centre where uc_iduser_no = '.$num);
      $NbrCentresMulti = mysql_num_rows($CentresMulti);
      if ($NbrCentresMulti > 0)
      {
                $AjoutBulle .= '<b> Inscrit(e) sur les sites suivants :</b> <br /> ';
                while ($ItemCentreMulti = mysql_fetch_object($CentresMulti))
                {
                   if (!strstr($AjoutBulle,str_replace("http://","",str_replace("ef-","",str_replace(".educagri.fr","",$ItemCentreMulti->uc_centre_lb)))))
                      $AjoutBulle .= ' - '.$ItemCentreMulti->uc_centre_lb.'<br />';
                }
                $AjoutBulle .= "<br />";
      }
      $AjoutBulle .= "$mess_multi_pf";
      $lien = "admin_gere.php?bprea=1&num=$num&id_grp=$id_grp";
      $lien = urlencode($lien);
      $multi = (strstr($AjoutBulle, 'suivants')) ? "<span style='color:#800000;'>$mess_mp</span>" : "$mess_mp";
      if ($etat_mp == 'OUI')
         echo "<TD width='17%'><A href=\"trace.php?link=$lien\"  target='main' ".
              bulle($AjoutBulle,"","CENTER","ABOVE",$tailleBulle)."$multi</A></TD>";
      echo "</TR></TABLE></TD>";
      $titre = "$mess_admin_bilan_connect $majuscule";
      $lien = "admin/connections.php?connection=1&logue=$logue&annu=$annu&id_grp=$id_grp&par_mois=1&id_util=$num&entantqueresp=$entantqueresp";
      $lien = urlencode($lien);
      $titre = '';
      $titre = "$mess_admin_bilan_connect :  $majuscule";
      echo "<td height='20' align='center'><A HREF=\"trace.php?link=$lien\"  target='main'".bulle($titre,"","LEFT","BELOW",270).
           "<IMG SRC=\"images/disconnect.gif\" BORDER=0></A></td>";
      $titre = '';
      $lien = "admin/modifiche.php?modifier=1&num=$num&annu=$annu&id_grp=$id_grp&entantqueresp=$entantqueresp";
      $lien = urlencode($lien);
      $titre ="$mess_admin_modif_profil $majuscule";
      if ($photo != "")
         $image = "<IMG SRC=\"images/$photo\" width='19' height='25' border='0'>";
      else
         $image = "<IMG SRC=\"images/repertoire/icoptisilhouet.gif\" width='19' height='25' border='0'>";
      echo "<td height='20' align='middle'><a href=\"trace.php?link=$lien\"  target='main' ".
      bulle($titre,"","LEFT","BELOW",150).
           "$image</A></td>";
      $titre = '';
      $bloque = GetDataField ($connect,"select util_blocageutilisateur_on from utilisateur where util_cdn='$num'",
                             "util_blocageutilisateur_on");
      $flag = GetDataField ($connect,"select util_flag from utilisateur where util_cdn='$num'","util_flag");
      if ($bloque == "NON")
      {
         if ($annu != "APPRENANT")
         {
            $req_ress = mysql_query ("select count(*) from ressource_new where ress_ajout = '$logue' or
                                     ress_url_lb like '%$logue"."_".$num."%'");
            $nb_ress = mysql_result($req_ress,0);
            $req_util = mysql_query ("select count(*) from utilisateur where util_auteur_no = '$num'");
            $nb_util = mysql_result($req_util,0);
            $requete_grp = mysql_query ("select grp_cdn from groupe");
            $nb_grp_parc = mysql_num_rows($requete_grp);
            if ($nb_grp_parc > 0)
            {
                $gp=0;
                while ($gp < $nb_grp_parc)
                {
                       $id_grp = mysql_result($requete_grp,$gp,"grp_cdn");
                       $req_formateur = mysql_query ("select count(*) from prescription_$id_grp where presc_formateur_no = '$num'");
                       $nb_formateur = mysql_result($req_formateur,0);
                       $req_prescripteur = mysql_query ("select count(*) from prescription_$id_grp where presc_prescripteur_no = '$num'");
                       $nb_prescripteur = mysql_result($req_prescripteur,0);
                       $req_scorm = mysql_query ("select count(*) from scorm_module where mod_launch_lb like '%$logue"."_".$num."%'");
                       $nb_scorm = mysql_result($req_scorm,0);
                  $gp++;
                }
            }
            $req_parc = mysql_query ("select count(*) from parcours where parcours_auteur_no = '$num'");
            $nb_parc = mysql_result($req_parc,0);
            $req_grp = mysql_query ("select count(*) from groupe where grp_resp_no = '$num'");
            $nb_grp = mysql_result($req_grp,0);
            $req_seq = mysql_query ("select count(*) from sequence where seq_auteur_no = '$num'");
            $nb_parc = mysql_result($req_seq,0);
            $req_act = mysql_query ("select count(*) from activite where act_auteur_no = '$num'");
            $nb_act = mysql_result($req_act,0);
            $req_tut = mysql_query ("select count(*) from tuteur where tut_tuteur_no = '$num'");
            $nb_tut = mysql_result($req_tut,0);
            if ($nb_ress == 0 && $nb_scorm == 0 && $nb_util == 0 && $nb_parc == 0 &&
               $nb_act == 0 && $nb_grp == 0 && $nb_tut == 0 && $nb_prescripteur == 0& $nb_formateur == 0)
              $ok_efface = 1;
            else
              $ok_efface = 0;
         }
         $lien = "admin_gere.php?supprimer=1&util=$type&ok_efface=$ok_efface&num=$num&annu=$annu".
                 "&id_grp=$id_grp&entantqueresp=$entantqueresp";
         $lien = urlencode($lien);
         if ($annu == "APPRENANT" || $ok_efface == 1)
         {
            $titre = "$mess_admin_sup_util :  $majuscule";
            echo "<td height='20' align='middle'><a href=\"javascript:void(0);\" onclick=\"javascript:return(confm('trace.php?link=$lien'));\" ".
                 bulle($titre,"","LEFT","BELOW",150).
                 "<IMG SRC=\"images/messagerie/icopoubelressour.gif\" BORDER=0></A></td>";
            $titre = '';
         }
         else
         {
            echo "<td height='20' align='middle'><a href=\"trace.php?link=$lien\" target='main' title=\"\"".bulle($mess_admin_blocage,"","LEFT","BELOW",130).
                 "<IMG SRC=\"images/ecran-annonce/icoblocage.gif\" BORDER=0></A></td>";
         }
         if ($typ_user == 'ADMINISTRATEUR' || $typ_user == "RESPONSABLE_FORMATION")
         {
           $liste_exp[$num] = "$nom;$prenom;$email;$tel;$webmail;$logue_cas";
           $liste1_exp[$num] = "$nom;$prenom;$logue;$passe;$email;$tel;$webmail;$logue_cas";
           $affiche_title = addslashes($mess_export_app);
           echo "<INPUT TYPE='HIDDEN' NAME='liste_exp[$num]' VALUE =\"$liste_exp[$num]\">";
           echo "<INPUT TYPE='HIDDEN' NAME='entantqueresp' VALUE =\"$entantqueresp\">";
           echo "<INPUT TYPE='HIDDEN' NAME='annu' VALUE =\"$annu\">";
           echo "<INPUT TYPE='HIDDEN' NAME='id_grp' VALUE =\"$id_grp\">";
           echo "<INPUT TYPE='HIDDEN' NAME='liste1_exp[$num]' VALUE =\"$liste1_exp[$num]\">";
           echo "<TD height='20' align='middle'><INPUT TYPE='checkbox' NAME='exp_app[$num]' ".
                bulle($affiche_title,"","LEFT","ABOVE",150)."</TD> ";
         }
      }
      elseif($bloque == "OUI" && $flag == 1)
         echo "<td height='20' align='middle'><Font color=red size=1>$mess_bloc_adm</font></td>";
      elseif($bloque == "OUI" && $flag != 1)
         echo "<td height='20' align='middle'><Font color=red size=1>$mess_bloc_mdp</font></td>";
    }
    echo "</TR>";
$i++;
}
if ($nbr_control > 0 && ($typ_user == "RESPONSABLE_FORMATION" || $typ_user == 'ADMINISTRATEUR') &&
   ($annu != 'APPRENANT' || ($annu == 'APPRENANT' && $id_grp < 0 && $nb_non_grp > 0)))
{
    echo "<TR>";
    echo "<TD colspan='8' align=right>";
    if ($annu == 'APPRENANT')
    {
       echo "<INPUT TYPE='HIDDEN' id='supprime_liste' name='supprime_liste' value='0'>";
       echo "<INPUT TYPE='image' SRC=\"images/messagerie/boutsupprim.gif\" BORDER='0' ".
            " onClick=\"javascript:$('[name=supprime_liste]').val('1');document.formx.submit();\" ".
            bulle($mess_sup_list_sel,"","LEFT","ABOVE",150);
    }
    echo "</TD>";
    echo "<INPUT TYPE='HIDDEN' NAME='message_envoye1' VALUE =\"$message_envoye1\">";
    echo "<INPUT TYPE='HIDDEN' NAME='presc' VALUE ='1'>";
    echo "<INPUT TYPE='HIDDEN' NAME='&ok' VALUE ='non'>";
    echo "<TD colspan='2' align=center>";
    echo "<INPUT TYPE='HIDDEN' id='liste_export' name='liste_export' value='0'>";
    echo "<INPUT TYPE='image' SRC='images/fiche_identite/boutexport.gif' ".
         "  onClick=\"javascript:$('[name=liste_export]').val('1');document.formx.submit();\"  ".
         bulle($mess_export_app_tit,"","LEFT","ABOVE",320);
    echo "</TD></TR>";

}
elseif ($nbr_control > 0 && $typ_user == 'ADMINISTRATEUR' &&
       (!isset($entantqueresp) || $entantqueresp != 1) && $annu == 'APPRENANT' && $id_grp > -1 && $nb_non_grp > 0)
{
    echo "<TR>";
    echo "<TD colspan='8' align=right>";
    echo "<INPUT TYPE='HIDDEN' id='supprime_liste' name='supprime_liste' value='0'>";
    echo "<INPUT TYPE='image' SRC=\"images/messagerie/boutsupprim.gif\" BORDER='0' ".
         " onClick=\"javascript:$('[name=supprime_liste]').val('1');document.formx.submit();\" ".
         bulle($mess_sup_list_sel,"","LEFT","ABOVE",150);
    echo "</TD>";
    echo "<INPUT TYPE='HIDDEN' NAME='message_envoye1' VALUE =\"$message_envoye1\">";
    echo "<INPUT TYPE='HIDDEN' NAME='presc' VALUE ='1'>";
    echo "<TD colspan='2' align=center>";
    echo "<INPUT TYPE='HIDDEN' id='liste_export' name='liste_export' value='0'>";
    echo "<INPUT TYPE='image' SRC='images/fiche_identite/boutexport.gif' ".
           "  onClick=\"javascript:$('[name=liste_export]').val('1');document.formx.submit();\" ".
           bulle($mess_export_app_tit,"","LEFT","ABOVE",320);
    echo "</TD></TR>";
}
elseif ($nbr_control > 0 && ($typ_user == "RESPONSABLE_FORMATION" ||
       ($typ_user == 'ADMINISTRATEUR' && isset($entantqueresp) && $entantqueresp == 1)) &&
       $annu == "APPRENANT" && $nb_non_grp > 0)
{
    echo "<TR>";
    echo "<TD colspan='8' align=right>";
    echo "<INPUT TYPE='HIDDEN' id='supprime_liste' name='supprime_liste' value='0'>";
    echo "<INPUT TYPE='image' SRC=\"images/messagerie/boutsupprim.gif\" BORDER='0' ".
         "  onClick=\"javascript:$('[name=supprime_liste]').val('1');document.formx.submit();\" ".
         bulle($mess_sup_list_sel,"","LEFT","ABOVE",150);
    echo "</TD>";
    echo "<INPUT TYPE='HIDDEN' NAME='message_envoye1' VALUE =\"$message_envoye1\">";
    echo "<INPUT TYPE='HIDDEN' NAME='presc' VALUE ='1'>";
    echo "<INPUT TYPE='HIDDEN' id='liste_export' name='liste_export' value='0'>";
    echo "<TD colspan='2' align=center>";
    echo "<INPUT TYPE='image' SRC='images/fiche_identite/boutexport.gif' ".
         " onClick=\"javascript:$('[name=liste_export]').val('1');document.formx.submit();\" ".
         bulle($mess_export_app_tit,"","LEFT","ABOVE",320);
    echo "</TD></TR>";
}
echo "</FORM></TABLE></CENTER>";
echo '<div id="mien" class="cms"></div>';
echo "</BODY></HTML>";
?>
