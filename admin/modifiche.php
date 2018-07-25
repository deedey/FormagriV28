<?php
if (!isset($_SESSION)) session_start();
if (!isset($_SESSION['id_user']) || $_SESSION['id_user'] == "")
{
  exit();
}
// Affichage des connections d'un utilisateur ordonées par date
require '../admin.inc.php';
require '../fonction.inc.php';
include ('../include/UrlParam2PhpVar.inc.php');
require "../lang$lg.inc.php";
require '../fonction_html.inc.php';
require '../class/class_admin.php';
//include ("click_droit.txt");
dbConnect();

include ('../style.inc.php');
?>
<SCRIPT type="text/javascript" SRC="ajax_modifiche.js"></SCRIPT>
<div id="affiche" class="Status"></div>
<div id="mon_contenu" class="cms"
        onClick="javascript:$(document).ready(function() {if ($.browser.msie) {$('div.cms').hide();}else{$('div.cms').hide('slow');}})"
        <?php echo "title=\"$mess_clkF\">".stripslashes($mess_notif);?>
</div>

<SCRIPT type="text/javascript">
function valide_checked()
{
  if (document.getElementById("supp_photo").checked == true)
     document.getElementById("supp_photo").value='on';
  else
     document.getElementById("supp_photo").value='off';
}
function checkForm(frm) {
  var ErrMsg = "<?php echo $mess_info_no;?>\n";
  var lenInit = ErrMsg.length;
  if (isEmpty(frm.email)==false){
    if (isEmail(frm.email)==false)
     ErrMsg += ' - <?php echo $mess_admin_email;?>\n';
  }
  <?php if ($mafiche == 1){?>
//     if (isEmpty(frm.passe)==true)
//       ErrMsg += ' - <?php echo $mess_admin_passe;?>\n';
  <?php }?>
  if (ErrMsg.length > lenInit)
    alert(ErrMsg);
}
function isEmail(elm) {
  if (elm.value.indexOf(" ") + "" == "-1" && elm.value.indexOf("@") + "" != "-1" && (elm.value.lastIndexOf(".") > elm.value.indexOf("@")) && elm.value != "")
     return true;
  else
     return false;
}
function isEmpty(elm) {
  var elmstr = elm.value + "";
  if (elmstr.length == 0)
    return true;
  return false;
}
</SCRIPT>
<?php
  if ($complement == 1)
  {
     echo "<TABLE background=\"../images/menu/fond_logo_formagri.jpg\" border='0' cellspacing='0' cellpadding='0' width='100%'>".
          "<TR width='100%'><TD align=left width='800'><IMG SRC=\"../images/logo_formagri.jpg\" border='0'></TD>";
     $lien="delog.php";
     $lien = urlencode($lien);
     echo "<TD align='right' valign='bottom'><A href=\"../trace.php?link=$lien\" title=\"$mess_dcnx\"".
        " onmouseover=\"img_dec.src='../images/complement/boutdeconecb.gif';return true;\" onmouseout=\"img_dec.src='../images/complement/boutdeconec.gif'\">";
     echo "<IMG NAME=\"img_dec\" SRC=\"../images/complement/boutdeconec.gif\" BORDER='0'".
        " onLoad=\"tempImg=new Image(0,0); tempImg.src='../images/complement/boutdeconecb.gif'\"></A></TD></TR></TABLE>";
     echo "<TABLE background=\"../images/ecran_annonce/bando.gif\" cellspacing='0' cellpadding='0' width='100%' border='0'>".
        "<TR width='100%'><TD align='left' width='100%' valign='top'><IMG SRC=\"../images/complement/soustitre.gif\" border='0'>".
        "</TD></TR></TABLE>&nbsp;<P>";
  }

  $agent=getenv("HTTP_USER_AGENT");
  $liste=mysql_query("select * from utilisateur where util_cdn='$num'");
  $nom = mysql_result($liste,0,"util_nom_lb");
  $prenom = mysql_result($liste,0,"util_prenom_lb");
  $photo = mysql_result($liste,0,"util_photo_lb");
  $email = mysql_result($liste,0,"util_email_lb");
  $logue = mysql_result($liste,0,"util_login_lb");
  $logue_cas = mysql_result($liste,0,"util_logincas_lb");
  $passe = mysql_result($liste,0,"util_motpasse_lb");
  $tel = mysql_result($liste,0,"util_tel_lb");
  $webmail = mysql_result($liste,0,"util_urlmail_lb");
  $type =mysql_result($liste,0,"util_typutil_lb");
  $commentaire =mysql_result($liste,0,"util_commentaire_cmt");
  $blocage = mysql_result($liste,0,"util_blocageutilisateur_on");
  $inscripteur = mysql_result($liste,0,"util_auteur_no");
  $flag = mysql_result($liste,0,"util_flag");
  if ($mafiche != 1)
    $titre .= "$mess_admin_modif_profil $prenom $nom";
  else
    $titre .= "$mess_menu_profil";
  $nb_fois++;
  echo "<CENTER><TABLE bgColor='#298CA0' cellspacing='2' cellpadding='0' width='850' border='0'><TR><TD width='900'> ";
  echo "<TABLE bgcolor='#FFFFFF' width='900' cellspacing='1' cellpadding='0' border='0'>";
  echo "<TR><TD background=\"../images/fond_titre_table.jpg\" width='900' height='40' align='center' valign='center'>";
  echo "<Font size='3' color='#FFFFFF'><B>$titre</B></FONT></TD></TR>";
  echo aide_simple("fiche");
  echo "<FORM NAME=\"form4\" id=\"form4\">";// action=\"modif_ok.php\"
  echo "<INPUT TYPE='HIDDEN' name='annu' value=\"$annu\">";
  echo "<INPUT TYPE='HIDDEN' name='inscripteur' value=\"$inscripteur\">";
  echo "<INPUT TYPE='HIDDEN' name='photo' value=\"$photo\">";
  echo "<INPUT TYPE='HIDDEN' name='num' value=\"$num\">";
  echo "<INPUT TYPE='HIDDEN' name='id_grp' value=\"$id_grp\">";
  echo "<INPUT TYPE='HIDDEN' name='mafiche' value=\"$mafiche\">";
  echo "<INPUT TYPE='HIDDEN' name='numero_groupe' value=\"$numero_groupe\">";
  echo "<INPUT TYPE='HIDDEN' name='complement' value=\"$complement\">";
  echo "<INPUT TYPE='HIDDEN' name='vient_de_menu' value=\"$vient_de_menu\">";
  echo "<INPUT TYPE='HIDDEN' name='full' value=\"$full\">";
  echo "<TR><TD><TABLE bgcolor='#FFFFFF' cellspacing='0' width='900' border='0'>";
  echo "<TR><TD rowspan='5' valign='top'>";
  echo "<IMG SRC=\"../images/fiche_identite/grofiche.gif\" border='0' valign=\"top\">";
  echo "<TD height='20' colspan='3'>&nbsp;</TD></TR><TR>";
  if ($mafiche == 1)
  {
   echo "<TD nowrap align='right' valign='center'><B>$mess_admin_passe&nbsp;&nbsp;&nbsp;</B></TD>";
   echo "<TD nowrap align='left' valign='center'><INPUT TYPE='PASSWORD'  name='passe' align='middle' valign='center'>".
        "&nbsp;&nbsp;<B><FONT color='red'>*$madm_avert</B></TD>";
  }else
    echo "<TD height='40'>&nbsp;</TD><TD width='400'>&nbsp;</TD>";
  echo "<TD rowspan='9' width='200' valign='top'>".
       "<TABLE bgColor='#298CA0' cellspacing='2' cellpadding='1'>".
       "<TR><TD valign='top' bgColor='#FFFFFF'>";
  if ($photo != ""){
    if ($lien_nom == "")
      echo "<IMG SRC=\"../images/$photo\" border='0'>";
    else
      echo "<IMG SRC=\"../images/$lien_nom\" border='0'>";
  }else{
    if ($lien_nom == "")
      echo "<IMG SRC=\"../images/ecran_profil/ombre.jpg\" border='0'>";
    else
      echo "<IMG SRC=\"../images/$lien_nom\" border='0'>";
  }
  echo "</TD></TR></TABLE><TABLE border='0' cellspacing='0'cellpadding='0'><TR><TD nowrap align='left'><BR>";
  if ($photo != "")
  {
      $lien = "charge_photo.php?num=$num&annu=$annu&photog=1&id_grp=$id_grp&mafiche=$mafiche&complement=$complement&full=$full&vient_de_menu=$vient_de_menu&entantqueresp=$entantqueresp";
      $lien= urlencode($lien);
      echo "<A HREF=\"javascript:void(0);\" title =\"$mess_modif_photo\" onClick=\"open('../trace.php?link=$lien','window','scrollbars=no,resizable=yes,width=400,height=140,left=300,top=300')\"".
           " onmouseover=\"img10.src='../images/fiche_identite/boutfichierb.gif';return true;\" ".
           "onmouseout=\"img10.src='../images/fiche_identite/boutfichier.gif'\">".
           "<IMG NAME=\"img10\" SRC=\"../images/fiche_identite/boutfichier.gif\" align=\"absmiddle\" BORDER='0' ".
           "onLoad=\"tempImg=new Image(0,0); tempImg.src='../images/fiche_identite/boutfichierb.gif'\">";
           "</A>";
  }
  else
  {
      $lien = "charge_photo.php?num=$num&annu=$annu&photog=0&id_grp=$id_grp&mafiche=$mafiche&complement=$complement&full=$full&vient_de_menu=$vient_de_menu&entantqueresp=$entantqueresp";
      $lien= urlencode($lien);
      echo "<A HREF=\"javascript:void(0);\" title =\"$mess_modif_photo\" onClick=\"open('../trace.php?link=$lien','window','scrollbars=no,resizable=yes,width=400,height=140,left=300,top=300')\"".
           " onmouseover=\"img10.src='../images/fiche_identite/boutfichierb.gif';return true;\" ".
           "onmouseout=\"img10.src='../images/fiche_identite/boutfichier.gif'\">".
           "<IMG NAME=\"img10\" SRC=\"../images/fiche_identite/boutfichier.gif\" align=\"absmiddle\" BORDER='0' ".
           "onLoad=\"tempImg=new Image(0,0); tempImg.src='../images/fiche_identite/boutfichierb.gif'\">";
           "</A>";
  }
  if ($mafiche != 1)
  {
     if ($photo != "")
        echo "</TD></TR><TR><TD><INPUT TYPE='CHECKBOX' name='supp_photo' id='supp_photo' onClick=\"valide_checked()\" value='0' title=\"$mess_ag_supp\">";
  }
  echo "</TD></TR><TR><TD><BR>* <small><B>$insc_pds_foto</B><small></TD></TR></TABLE>";
  echo "</TD></TR>";
  echo "<TR><TD height='60'>&nbsp;</TD><TD width='400'>&nbsp;</TD></TR>";
  echo "<TR><TD nowrap align='right'><B>$mess_admin_nom&nbsp;&nbsp;&nbsp;</B></TD>";
  echo "<TD nowrap align='left' width='250'>";
  if ($mafiche !=1)
     echo "<INPUT TYPE='text' class='INPUT' name='nom' value=\"$nom\" align='middle'>";
  else
     echo "<B>$nom</B><INPUT TYPE='HIDDEN' name='nom' value=\"$nom\" align='middle'>";
  echo "<INPUT TYPE='HIDDEN'  name='lien_nom' value=\"$lien_nom\" align='middle'>";
  echo "</TD></TR>";
  echo "<TR><TD nowrap align='right'><B>$mess_admin_prenom&nbsp;&nbsp;&nbsp;</B></TD><TD nowrap align='left'>";
  if ($mafiche !=1)
     echo "<INPUT TYPE='text' class='INPUT'  name='prenom' value=\"$prenom\" align='middle'>";
  else
     echo "<B>$prenom</B><INPUT TYPE='HIDDEN'  name='prenom' value=\"$prenom\" align='middle'>";
  echo "</TD><TD></TD></TR>";
  echo "<TR><TD nowrap height='30' align='right'colspan='2'><B>$mess_login_cas&nbsp;&nbsp;&nbsp;</B></TD><TD nowrap align='left'>";
  echo "<INPUT TYPE='text' class='INPUT'  name='logue_cas' value=\"$logue_cas\" align='middle'>";
  echo "</TD><TD></TD></TR>";
if ($mafiche == 1)
{
  echo "<INPUT TYPE='HIDDEN'  name='type' value=\"$type\" align='middle'>";

?>
   <TR>
      <TD nowrap align='right' colspan='2' height='30'>
         <B><?php echo $mess_auth_new_mdp ;?>&nbsp;&nbsp;&nbsp;</B>
      </TD>
      <TD nowrap align='left'>
         <INPUT TYPE="password" class='INPUT'  name="passe1" value="" align="middle">
      </TD>
   </TR>
   <TR>
      <TD nowrap align='right' colspan='2' height='30'>
         <B><?php echo $mess_confirm ;?>&nbsp;&nbsp;&nbsp;</B>
      </TD>
      <TD nowrap align='left'>
         <INPUT TYPE="password" class='INPUT' name="passe2" value="" align="middle">
      </TD>
   </TR>
<?php
}
elseif ($typ_user == "ADMINISTRATEUR" && $mafiche != 1 && $type == "APPRENANT")
{
    echo "<TR><TD nowrap align='right' colspan='2' height='30'><B>$mess_admin_login&nbsp;&nbsp;&nbsp;</B></TD>";
    echo "<TD nowrap align='left' height='30'> <INPUT TYPE='text' class='INPUT'  ".
         "name='lelogue' value=\"$logue\" size='25' align='middle' ".
         bulle($mess_chglog_avert,"","LEFT","ABOVE",350)."</TD><TR>";
}
?>
   <TR>
    <?php
     if ($mafiche == 1)
    {
    ?>
      <TD nowrap align='right' colspan='2' height='30'>
    <?php
    }else
    {
    ?>
       <TD nowrap align='right' height='30' colspan='2'>
    <?php
    }
    ?>
       <B><?php echo $mess_admin_email ;?>&nbsp;&nbsp;&nbsp;</B>
      </TD>
      <TD nowrap align='left'>
         <INPUT TYPE="TEXT"  class='INPUT'   name="email" id="mail" value="<?php  echo $email ;?>" size="25" align="middle">
         <script type="text/javascript">
                 var mail = new LiveValidation('mail');
                 mail.add( Validate.Presence );
                 mail.add( Validate.Email );
                 mail.add( Validate.Length, { minimum: 8, maximum: 40 } );
         </script>
      </TD>
      <TD nowrap></TD>
   </TR>
   <TR>
      <TD nowrap align='right' colspan='2' height='30'>
         <B><?php echo $mess_admin_tel ;?>&nbsp;&nbsp;&nbsp;</B>
      </TD>
      <TD nowrap align='left'>
         <INPUT TYPE="TEXT"  class='INPUT' name="tel" id='tel' value="<?php  echo $tel ;?>" align="middle">
         <script type="text/javascript">
                 //var tel = new LiveValidation('tel');
                 //tel.add(Validate.Numericality);
         </script>

      </TD>
      <TD nowrap></TD>
   </TR>
   <TR>
      <TD nowrap align='right' colspan='2' height='30'>
         <B><?php echo $mess_admin_webmail ;?>&nbsp;&nbsp;&nbsp;</B>
      </TD>
      <TD nowrap align='left'>
         <INPUT TYPE="TEXT"class='INPUT' name="webmail" value="<?php  echo $webmail ;?>" size="30" align="middle">
      </TD>
      <TD nowrap></TD>
   </TR>
  <?php
     echo "<TR><TD nowrap align='right' colspan=2><B>$mess_admin_comment</B></TD>";
     echo "<TD nowrap align='left'><TEXTAREA name='commentaire' id='commentaire' rows='10' cols='80' class='TEXTAREA' ".
          " align='middle'>".$commentaire."</TEXTAREA></TD><TD nowrap></TD></TR>";

if ($mafiche !=1)
{?>
   <TR>
      <TD nowrap align='right' colspan='2' height='30'>
         <B><?php echo $mess_admin_blocage ;?>&nbsp;&nbsp;&nbsp;</B>
      </TD>
      <TD nowrap align='left'>
         <SELECT  name="blocage" class='SELECT' size="1">
         <OPTION SELECTED value="<?php echo $blocage;?>"><?php  echo $blocage ;?></OPTION>
         <OPTION value="NON"><?php echo $mess_non;?></OPTION>
         <OPTION value="OUI"><?php echo $mess_oui;?></OPTION>
         </SELECT>
      </TD>
      <TD nowrap></TD>
   </TR>
   <TR>
      <TD nowrap align='right' colspan='2' height='30'>
         <B><?php echo $mess_inscripteur."&nbsp;&nbsp;&nbsp;";?> </B>
      </TD>
      <TD nowrap align='left'>
      <?php
      if ($inscripteur > 0)
      {
         $nom_inscripteur=GetDataField ($connect,"select util_nom_lb from utilisateur where util_cdn='$inscripteur'","util_nom_lb");
         $prenom_inscripteur=GetDataField ($connect,"select util_prenom_lb from utilisateur where util_cdn='$inscripteur'","util_prenom_lb");
         echo "<INPUT TYPE='HIDDEN' name='entantqueresp' value='$entantqueresp'>";
         echo "<INPUT TYPE='HIDDEN' name='id_grp' value='$id_grp'>";
         if (($typ_user == 'ADMINISTRATEUR' || $typ_user == 'RESPONSABLE_FORMATION') && $type == 'APPRENANT')
         {
           $param = $inscripteur;
           Ascenseur_mult ("inscripteur","select util_cdn,util_nom_lb,util_prenom_lb from utilisateur where (util_typutil_lb = 'RESPONSABLE_FORMATION' or util_typutil_lb = 'ADMINISTRATEUR') AND util_flag = 0 order by util_nom_lb ASC",$connect,$param);
         }
         else
         {
           echo $prenom_inscripteur." ".$nom_inscripteur;
           echo "<INPUT TYPE='HIDDEN' NAME='inscripteur' VALUE='$inscripteur'>";
         }
      }
      else
         echo "<INPUT TYPE='HIDDEN' NAME='inscripteur' VALUE='$inscripteur'>";
      echo "</TD></TR>";
      if ($annu != "APPRENANT")
      {
            $nb_prescripteur = 0;$nb_formateur = 0;
            $reqUtil=mysql_query("select * from utilisateur_groupe");
            if (mysql_num_rows($reqUtil) > 0)
            {
                while ($item = mysql_fetch_object($reqUtil))
                {
                       $id_grp = $item->utilgr_groupe_no;
                       $req_prescripteur = mysql_query ("select count(*) from prescription_$id_grp where presc_prescripteur_no = '$num'");
                       $nb_prescripteur += mysql_result($req_prescripteur,0);
                       $req_formateur = mysql_query ("select count(*) from prescription_$id_grp where presc_formateur_no = '$num'");
                       $nb_formateur += mysql_result($req_formateur,0);
                }
            }
            $req_grp = mysql_query ("select count(*) from groupe where grp_resp_no = '$num'");
            $nb_grp = mysql_result($req_grp,0);
            $req_util = mysql_query ("select count(*) from utilisateur where util_auteur_no = '$num'");
            $nb_util = mysql_result($req_util,0);
            $req_parc = mysql_query ("select count(*) from parcours where parcours_auteur_no = '$num'");
            $nb_parc = mysql_result($req_parc,0);
            $req_seq = mysql_query ("select count(*) from sequence where seq_auteur_no = '$num'");
            $nb_parc = mysql_result($req_seq,0);
            $req_act = mysql_query ("select count(*) from activite where act_auteur_no = '$num'");
            $nb_act = mysql_result($req_act,0);
            $req_rdv = mysql_query ("select count(*) from rendez_vous where rdv_tuteur_no = '$num' OR rdv_util_no = '$num'");
            $nb_rdv = mysql_result($req_rdv,0);
            $req_tut = mysql_query ("select count(*) from tuteur where tut_tuteur_no = '$num'");
            $nb_tut = mysql_result($req_tut,0);
            if ($type == "RESPONSABLE_FORMATION" && $nb_util == 0 && $nb_grp == 0 && $nb_prescripteur == 0 && $nb_formateur == 0 && $nb_parc == 0 && $nb_act == 0 && $nb_tut == 0 && $nb_rdv == 0){
              $ok_modifie = 1;
              $ce_typo = $mess_typ_rf;
            }elseif ($type == "FORMATEUR_REFERENT" && $nb_parc == 0 && $nb_act == 0 && $nb_tut == 0 && $nb_rdv == 0 && $nb_formateur == 0){
              $ok_modifie = 1;
              $ce_typo = $mess_typ_fr;
            }elseif ($type == "TUTEUR" && $nb_tut == 0 && $nb_rdv == 0){
              $ok_modifie = 1;
              $ce_typo = $mess_typ_tut;
            }elseif ($type == "ADMINISTRATEUR")
              $ce_typo = $mess_typ_adm;
            else
              $ok_modifie = 0;

      }
      else
      {
        $nb_util_grp = mysql_result(mysql_query("select count(*) from utilisateur_groupe where utilgr_utilisateur_no = '$num'"),0);
        $nb_util_tut = mysql_result(mysql_query("select count(*) from tuteur where  tut_apprenant_no = '$num'"),0);
        $ce_typo = $mess_typ_app;
        echo "<INPUT TYPE='HIDDEN' NAME='type' VALUE=\"$type\">";
      }
      if ($typ_user == "ADMINISTRATEUR" && (($type !="APPRENANT" && $ok_modifie == 1) || ($type =="APPRENANT" && $nb_util_grp == 0 && $nb_util_tut)))
      {
        echo "<TR><TD nowrap align='right' colspan='2' height='30'> <B>$mess_admin_utilisateur&nbsp;&nbsp;&nbsp;</B></TD><TD nowrap>";
        echo "<SELECT  name='type' class='SELECT' size='1'>";
        echo "<OPTION value = \"$type\" SELECTED>$ce_typo</OPTION>";
        echo "<OPTION value = 'TUTEUR'>$mess_typ_tut</OPTION>";
        echo "<OPTION value = 'FORMATEUR_REFERENT'>$mess_typ_fr</OPTION>";
        echo "<OPTION value = 'RESPONSABLE_FORMATION'>$mess_typ_rf</OPTION>";
        echo "<OPTION value = 'ADMINISTRATEUR'>$mess_typ_adm</OPTION>";
        echo "</SELECT></TD><TD nowrap><INPUT TYPE='HIDDEN' NAME='flag' VALUE=$flag></TD></TR>";
     }
     elseif($typ_user == "ADMINISTRATEUR" && $type == "RESPONSABLE_FORMATION")
     {
        echo "<TR><TD nowrap align='right' colspan='2' height='30'> <B>$mess_admin_utilisateur&nbsp;&nbsp;&nbsp;</B></TD><TD nowrap>";
        echo "<SELECT  name='type' class='SELECT' size='1'>";
        echo "<OPTION value = '$type' SELECTED>$mess_typ_rf</OPTION>";
        echo "<OPTION value = 'ADMINISTRATEUR'>$mess_typ_adm</OPTION>";
        echo "</SELECT></TD><TD nowrap><INPUT TYPE='HIDDEN' NAME='flag' VALUE=$flag></TD></TR>";
     }
     elseif($typ_user == "ADMINISTRATEUR" && $type == "FORMATEUR_REFERENT")
     {
        echo "<TR><TD nowrap align='right' colspan='2' height='30'> <B>$mess_admin_utilisateur&nbsp;&nbsp;&nbsp;</B></TD><TD nowrap>";
        echo "<SELECT  name='type' class='SELECT' size='1'>";
        echo "<OPTION value = '$type' SELECTED>$mess_typ_fr</OPTION>";
        echo "<OPTION value = 'RESPONSABLE_FORMATION'>$mess_typ_rf</OPTION>";
        echo "<OPTION value = 'ADMINISTRATEUR'>$mess_typ_adm</OPTION>";
        echo "</SELECT></TD><TD nowrap><INPUT TYPE='HIDDEN' NAME='flag' VALUE=$flag></TD></TR>";
     }
     elseif($typ_user == "ADMINISTRATEUR" && $type == "TUTEUR")
     {
        echo "<TR><TD nowrap align='right' colspan='2' height='30'> <B>$mess_admin_utilisateur&nbsp;&nbsp;&nbsp;</B></TD><TD nowrap>";
        echo "<SELECT  name='type' class='SELECT' size='1'>";
        echo "<OPTION  value = '$type' SELECTED>$mess_typ_tut</OPTION>";
        echo "<OPTION value = 'FORMATEUR_REFERENT'>$mess_typ_fr</OPTION>";
        echo "<OPTION value = 'RESPONSABLE_FORMATION'>$mess_typ_rf</OPTION>";
        echo "<OPTION value = 'ADMINISTRATEUR'>$mess_typ_adm</OPTION>";
        echo "</SELECT></TD><TD nowrap><INPUT TYPE='HIDDEN' NAME='flag' VALUE=$flag></TD></TR>";
     }
     elseif($typ_user == "ADMINISTRATEUR" && $type == "ADMINISTRATEUR")
        echo "<INPUT TYPE='HIDDEN' NAME='type' VALUE=\"$type\">";
     echo "<INPUT TYPE='HIDDEN' NAME='nb_fois' VALUE='$nb_fois'>";

 }
 else
 {
    echo "<INPUT TYPE='HIDDEN' NAME='mafiche' VALUE='$mafiche'>";
    echo "<INPUT TYPE='HIDDEN' NAME='blocage' VALUE='$blocage'>";
    echo "<INPUT TYPE='HIDDEN' NAME='type' VALUE='$typ_user'>";
    echo "<INPUT TYPE='HIDDEN' NAME='nb_fois' VALUE='$nb_fois'>";
 }
 echo "</TD><TR>";
  if (!isset($mafiche) || (isset($mafiche) && ($typ_user == "RESPONSABLE_FORMATION" || $typ_user == "ADMINISTRATEUR") && $mafiche == 1))
    echo "<TR height='40'><TD align=left colspan='2' valign='bottom'>".
         "<A HREF=\"javascript:history.go(-$nb_fois);\" ".
         "onmouseover=\"img_annonce.src='../images/fiche_identite/boutretourb.gif';return true;\" ".
         "onmouseout=\"img_annonce.src='../images/fiche_identite/boutretour.gif'\">".
         "<IMG NAME=\"img_annonce\" SRC=\"../images/fiche_identite/boutretour.gif\" BORDER='0' ".
         "onLoad=\"tempImg=new Image(0,0); tempImg.src='../images/fiche_identite/boutretourb.gif'\"></A></TD><TD>";
  else
    echo "<TR height='40'><TD colspan='2'>";
  echo "<input type='image' src='../images/fiche_identite/boutvalid.gif' ".
       "onClick=\"javascript:TinyMCE.prototype.triggerSave();checkForm(document.form4);document.location='#sommet';".
       "lanceRequest('modif_ok.php');\">";//document.form4.submit();
 echo "</TD></TR></FORM>";
 $refer = getenv("HTTP_REFERER");
  if ($typ_user == "TUTEUR" || $typ_user == "FORMATEUR_REFERENT")
  {
     if ($vient_de_menu != 'menu')
     {
        if ($typ_user == "TUTEUR")
           $lien = "annonce_tuteur.php?affiche_toutapp=1&activee=1";
        else
           $lien = "annonce_formateur.php";
        $lien = urlencode($lien);
        echo "</TR><TR><TD width='100%' colspan='8'><TABLE width='100%'><TR height='40'><TD align=left valign='bottom'><A HREF=\"../trace.php?link=$lien\" onmouseover=\"img_annonce.src='../images/fiche_identite/boutretourb.gif';return true;\" onmouseout=\"img_annonce.src='../images/fiche_identite/boutretour.gif'\">";
        echo "<IMG NAME=\"img_annonce\" SRC=\"../images/fiche_identite/boutretour.gif\" BORDER='0' onLoad=\"tempImg=new Image(0,0); tempImg.src='../images/fiche_identite/boutretourb.gif'\"></A></TD>";
        echo "</TR></TABLE></TD></TR></TABLE></TD></TR></TABLE></TD></TR></TABLE>";
     }
  }
  if ($complement == 1 || $vient_de_menu == "menu")
  {
     if ($complement == 1)
         $lien = "annonce_grp.php?full=$full&complement=$complement";
     else
         $lien = "annonce_grp.php?full=$full&vient_de_menu=$vient_de_menu";
    $lien = urlencode($lien);
    echo "<TR height='40'><TD align=left colspan='4' valign='bottom'><A HREF=\"../trace.php?link=$lien\" onmouseover=\"img_annonce.src='../images/fiche_identite/boutretourb.gif';return true;\" onmouseout=\"img_annonce.src='../images/fiche_identite/boutretour.gif'\">";
    echo "<IMG NAME=\"img_annonce\" SRC=\"../images/fiche_identite/boutretour.gif\" BORDER='0' onLoad=\"tempImg=new Image(0,0); tempImg.src='../images/fiche_identite/boutretourb.gif'\"></A></TD></TR>";
  }
  echo "</TABLE></body></html>";
 exit;
//}
?>
