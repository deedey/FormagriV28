<?php
if (!isset($_SESSION)) session_start();
include ("include/UrlParam2PhpVar.inc.php");
if (!isset($_SESSION['id_user']) || $_SESSION['id_user'] == "")
{
  exit();
}
require "admin.inc.php";
require 'fonction.inc.php';
require 'fonction_html.inc.php';
require "lang$lg.inc.php";
//include ("click_droit.txt");
dbConnect();
// suppression d'une connection dans la table log
  $agent=getenv("HTTP_USER_AGENT");
  if (strstr($agent,"Mac") || strstr($agent,"Konqueror"))
    $mac=1;
  if (strstr($agent,"Win"))
    $win=1;
if (isset($moteurs) && $moteurs == 1)
{
  include 'style.inc.php';
    echo "<CENTER><TABLE bgColor='#298CA0' cellspacing='2'><TR><TD>";
    echo "<TABLE bgColor='#FFFFFF' cellspacing='1' border='0'>";
    echo "<TR><TD background=\"images/fond_titre_table.jpg\" colspan='2' height='40' align='center' valign='center'><Font size='3' color='#FFFFFF'><B>$mess_mot_tit</B></FONT></TD></TR>";
  ?>
    <tr><TD>
   <CENTER>
      <TABLE cellPadding=2 border=0>
        <TBODY>
    <tr>
    <td height="20" align='middle' bgcolor="#2B677A"><FONT COLOR=white><b><?php  echo $mess_mot_lk ;?></b></FONT></td>
    <td align='middle' bgcolor="#5b8bab"><FONT COLOR=white><b><?php  echo $mess_mot_req ;?></b></FONT></td>
    <td align='middle'></td></tr>
     <tr bgcolor="#F4F4F4">
          <TD><A target="_blank" href="http://www.google.fr/"><IMG src="images/moteurs/google.gif"   border=0></A></TD>
          <TD><form action="http://www.google.fr/search" name=f target=_blank>
            <input maxLength=256 size=50 name=q value="">
             <input name=hl type=hidden value=fr>
            </TD><TD> <INPUT TYPE="image" NAME="Valider" SRC="images/chercher-lav_<?php echo $lg;?>.gif" ALT="<?php  echo $mess_gen_cherche;?>" name=btnG> </TD><TD></FORM></TD></TR>
     <tr bgcolor="#FFFFFF">
          <TD><A target=_blank href="http://www.voila.fr/"><IMG src="images/moteurs/voila.gif" border=0></A></TD>
         <TD><form ACTION="http://r.voila.fr/se" METHOD="GET" target=_blank name=ke>
                  <input type=hidden name=sev value=2>
                   <input type=hidden name=lg value=fr>
                   <input type=hidden name=dblg value=fr>
                    <input type=hidden name=db value=web>
                    <input type=hidden name=ctx value=voila>
                    <input type=hidden name=ref value=lr_voila>
                    <input type=text class=txt name=kw  maxLength=256 size=50 value=""></TD>
                    <TD>
                     <INPUT TYPE="image" NAME="Valider" SRC="images/chercher-lav_<?php echo $lg;?>.gif" ALT="<?php  echo $mess_gen_cherche;?>">
                    </TD>
                    <TD>
            </FORM>
          </TD>
          </TR>
          <tr bgcolor="#F4F4F4">
          <TD><A target=_blank href="http://www.nomade.tiscali.fr/"><IMG
            src="images/moteurs/Nomade.gif" border=0></A></TD>
          <TD>
            <FORM name=nomade1 ACTION="http://rechercher.nomade.fr/recherche.asp" target=_blank OnSubmit=valide("nomade1");>
            <input type=hidden name="MT">
            <input type=hidden name="GL" value="INTL">
          <INPUT TYPE=text NAME="s" VALUE="" maxLength=256 size=50>
          </TD><TD><INPUT TYPE="image" NAME="Valider" SRC="images/chercher-lav_<?php echo $lg;?>.gif" ALT="<?php  echo $mess_gen_cherche;?>"> </TD><TD></FORM></TD></TR>
          <tr bgcolor="#FFFFFF">
          <TD><A target="_blank"
            href="http://fr.altavista.com"><IMG
            src="images/moteurs/altavist.gif"
          border=0></A></TD>
          <TD>
            <FORM action=http://www.altavista.digital.com/cgi-bin/query
            method=get target=_blank><INPUT name=q  size=50> <INPUT type=hidden value=fr
            name=country> <INPUT type=hidden value=fr name=lang> <INPUT
            type=hidden value=web name=what> <INPUT type=hidden value=.
            name=fmt> <INPUT type=hidden value=q name=pg>
            </TD><TD><INPUT TYPE="image" NAME="Valider" SRC="images/chercher-lav_<?php echo $lg;?>.gif" ALT="<?php  echo $mess_gen_cherche;?>"></TD><TD></FORM></TD></TR>
            <tr bgcolor="#F4F4F4">
          <TD><A target="_blank"
            href="http://www.yahoo.fr/"><IMG
            src="images/moteurs/YAHOO.gif" border=0></A></TD>
          <TD>
            <FORM action=http://search.yahoo.fr/search/fr method=get target=_blank>
            <INPUT name=p  size=50></TD><TD>
            <INPUT TYPE="image" NAME="Valider" SRC="images/chercher-lav_<?php echo $lg;?>.gif" ALT="<?php  echo $mess_gen_cherche;?>" name=Go...></TD><TD></FORM></TD></TR>
      </TBODY>
      </TABLE>
<?php
         echo "</TD></TR></TABLE></TD></TR></TABLE>";
echo "</BODY></HTML>";
} // fin if moteurs == 1
if (!empty($mess_notif))
   echo notifier($mess_notif);
if (isset($plugins) && $plugins == 1)
{
 include 'style.inc.php';

?>
<div id="affiche" class="Status"></div>
<script type="text/javascript">
$(document).ready(function()
{
    $('#mon_contenu').click(function()
    {
      if ($.browser.msie) {
      $(this).hide();
      }else{
      $(this).hide('slow');
      }
    });
});
</script>
<div id="mon_contenu" class="cms"  <?php echo "title=\"$mess_clkF\"></div>";
  $requete = mysql_query ("select * from plugins");
  $nbr = mysql_num_rows($requete);
  if ($nbr == 0)
    $flag = 0;
  else
    $flag = 1;
  if ($flag == 0 && $typ_user != 'ADMINISTRATEUR')
  {
    echo "<P><CENTER><FONT COLOR=red><BIG><B>$mess_noplug</FONT></CENTER><P>" ;
    exit;
  }
  entete_simple($mess_menu_plug);
  echo "<TR><TD style=\"padding-top:2px;padding-bottom:2px;\">";
   if ($typ_user == 'ADMINISTRATEUR' && (!isset($ajout) || (isset($ajout) && $ajout != 1)) && (!isset($modif) || (isset($modif) && $modif != 1)))
   {
      echo "<div id='ajout' style=\"foat:left;padding-left:2px;\">".
           "<A href = \"moteurs.php?ajout=1\" class='bouton_new' target= 'main'>$mess_ajt_plug</A></div>" ;
   }
  echo aide_div("plugins",8,0,0,0)."</td></tr>";
  echo "<TR><TD colspan='2' width='100%'><TABLE cellspacing='1' cellpadding = '6' width='100%'><TR height='30'>";
  echo "<td bgcolor='#2B677A'><FONT COLOR=white><b>$mess_mot_lk</b></FONT></td>";
  echo "<td bgcolor='#2B677A' nowrap><FONT COLOR=white><b>$mess_menu_plug</b></FONT></td>";
  echo "<td bgcolor='#2B677A'><FONT COLOR=white><b>$mess_desc</b></FONT></td>";
  if ($typ_user == 'ADMINISTRATEUR')
  {
//     echo "<td align='middle' bgcolor='#5b8bab'><FONT COLOR=white><b>$mess_plug_adr</b></FONT></td>";
    echo "<TD bgcolor='#2B677A' align='left'><FONT COLOR=white><b>$mess_modif_base</b></FONT></TD>";
    echo "<TD bgcolor='#2B677A' align='left'><FONT COLOR=white><b>$mess_ag_supp</b></FONT></TD>";
  }
  echo "</tr>";
  if ($typ_user != "APPRENANT" && $typ_user != "TUTEUR")
     {
      echo "<TR bgcolor='#F4F4F4'>";
      echo "<td align='middle'><A href=\"http://www.lucagalli.net/cgi-bin/countdown.pl?quizfaber291eng.zip\" target='_blank'><IMG SRC = \"images/moteurs/logquizfaber.jpg\" alt= \"$mess_telecharger\" border='0'></td>";
      echo "<td align='left'><b>Quizz Faber</b><BR>$mess_qf_tit</td>";
      echo "<td align='left'>$plug_quizz</td><TD>&nbsp;</TD><TD>&nbsp;</TD></TR>";
      echo "<TR bgcolor='#FFFFFF'><td align='middle'><A href=\"http://web.uvic.ca/hrd/halfbaked/#downloads\" target='_blank'><IMG SRC = \"images/moteurs/logpotatoes.gif\" alt= \"$mess_telecharger\" border='0'></td>";
      echo "<td align='left'><b>Hot Potatoes</b><BR>$mess_hp_tit</td>";
      echo "<td align='left'>$plug_quizz</td><TD>&nbsp;</TD><TD>&nbsp;</TD></TR>";
      echo "<TR bgcolor='#F4F4F4'><td align='middle'><A href=\"http://ef-dev.educagri.fr/QuizzUtil.zip\" target='_blank'><IMG SRC = \"images/moteurs/logmoulinette.gif\" alt= \"$mess_telecharger\" border='0'></td>";
      echo "<td align='left'>$mess_mouli_tit</td>";
      echo "<td align='left'>$plug_moulin</td><TD>&nbsp;</TD><TD>&nbsp;</TD></TR>";
  }
  $i = 0;
   while ($i < $nbr){
     $num = mysql_result($requete,$i,'plug_cdn');
     $nom = mysql_result($requete,$i,'plug_tit_lb');
     $adresse = mysql_result($requete,$i,'plug_adr_cmt');
     $img = mysql_result($requete,$i,'plug_img_lb');
     $desc = mysql_result($requete,$i,'plug_desc_cmt');
      echo couleur_tr($i,'');
      echo "<td align='middle'><A href=\"$adresse\" target='_blank'><IMG SRC = \"images/$img\" alt= \"$mess_telecharger\" border='0'></td>";
      echo "<td align='left'><b>$nom</b></td>";
      echo "<td align='left'>$desc</td>";
      if ($typ_user == 'ADMINISTRATEUR')
      {
        $lien = "moteurs.php?modif=1&num=$num";
        $lien = urlencode($lien);
        echo "<td height='20' align='middle'><a href=\"trace.php?link=$lien\"  target='main'>";
        echo "<IMG SRC=\"images/repertoire/icoGrenomfich20.gif\" height=\"20\" width=\"20\" ALT=\"$mess_modif_base\" BORDER=0></A></td>";
        $lien = "moteurs.php?supprimer=1&num=$num";
        $lien = urlencode($lien);
        echo "<td height='20' align='middle'><a href=\"trace.php?link=$lien\"  target='main'>";
        echo "<IMG SRC=\"images/messagerie/icopoubelressour.gif\" height=\"20\" width=\"15\" ALT=\"$mess_ag_supp\" BORDER=0></A></td>";
      }elseif ($typ_user != 'APPRENANT' && $typ_user != 'ADMINISTRATEUR')
        echo "<td>&nbsp;</td><td>&nbsp;</td>";
      echo "</tr>";
   $i++;
   }
  echo "</TABLE></TD></TR></TABLE></TD></TR></TABLE>";
}
if (isset($ajout) && $ajout == 1 && !isset($telecharger))
{
   include 'style.inc.php';
  echo "<CENTER><TABLE bgColor='#298CA0' cellspacing='2'><TR><TD>";
  echo "<TABLE bgColor='#FFFFFF' cellspacing='1'  cellpadding='0' width='100%'>";
  echo "<TR><TD background=\"images/fond_titre_table.jpg\" colspan='3' height='37' align='center' valign='center'><Font size='3' color='#FFFFFF'><B>$mess_ajt_plug</B></FONT>";
  echo "</TD></TR><TR><TD bgColor='#CEE6EC'><TABLE cellspacing='1'  cellpadding='6'><TR><TD>";
   echo "<FORM name='form' action=\"moteurs.php?ajout=1&telecharger=1\" method='POST' ENCTYPE='multipart/form-data'>";
   echo "<B>$mess_plug_nom_img</B></TD>";
   echo "<TD>";
    if ($mac != 1){
          if (strstr(getenv("HTTP_USER_AGENT"),"MSIE")){
            echo "<DIV style=\"position :absolute ;\">";
            echo "<INPUT TYPE='file' name='userfile' style=\"filter :alpha(opacity=0)\"".
                 "onfocus=\"document.form.txtFile.value=document.form.userfile.value\" ".
                 "onchange=\"document.form.txtFile.value=document.form.userfile.value\">";
            echo "</DIV>";
            echo "<input type='text' class='INPUT'  name='txtFile'";
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
            echo "<INPUT TYPE='text' class='INPUT'  name='txtFile' onMouseover=\"makevisible(this,0)\" onMouseout=\"makevisible(this,0)\">".
                 "<IMG NAME=\"img8\" SRC=\"images/fiche_identite/boutfichier.gif\" align=\"absmiddle\" BORDER='0' ".
                 "onLoad=\"tempImg=new Image(0,0); tempImg.src='images/fiche_identite/boutfichierb.gif'\">";
          }
    }else
          echo "<INPUT TYPE='file' name='userfile' enctype='multipart/form-data'>";
   echo "</TD></TR>";
   echo "<INPUT type='hidden' name='MAX_FILE_SIZE' value='200000'>";
   echo "<TR><TD><B>$mess_plug_titre</B></TD>";
   echo "<TD><INPUT type='text' class='INPUT'  name='titre' size=60 value=''><BR></TD></TR>";
   echo "<TR><TD><B>$mess_plug_link</B></TD>";
   echo "<TD><INPUT type='text' class='INPUT'  name='url' size='60' value=''><BR></TD></TR>";
   echo "<TR><TD><B>$mess_plug_desc</B></TD>";
   echo "<TD><INPUT type='text' class='INPUT'  name='descript' size='60' value=''><BR></TD></TR>";
   echo "<TR><TD></TD><TD align='left'><A HREF=\"javascript:document.form.submit();\" onmouseover=\"img1.src='images/fiche_identite/boutvalidb.gif';return true;\" onmouseout=\"img1.src='images/fiche_identite/boutvalid.gif'\">";
   echo "<IMG NAME=\"img1\" SRC=\"images/fiche_identite/boutvalid.gif\" BORDER='0' onLoad=\"tempImg=new Image(0,0); tempImg.src='images/fiche_identite/boutvalidb.gif'\"></A></TD></TR> ";
   echo "<TR><TD align=left><A HREF=\"javascript:history.back();\" onmouseover=\"img_annonce.src='images/fiche_identite/boutretourb.gif';return true;\" onmouseout=\"img_annonce.src='images/fiche_identite/boutretour.gif'\">";
   echo "<IMG NAME=\"img_annonce\" SRC=\"images/fiche_identite/boutretour.gif\" BORDER='0' onLoad=\"tempImg=new Image(0,0); tempImg.src='images/fiche_identite/boutretourb.gif'\"></A></TD></TR>";
   echo "</TD></TR></TABLE></FORM></TD></TR></TABLE></TD></TR></TABLE>";
exit;
}
if (isset($ajout) && $ajout == 1 && isset($telecharger) && $telecharger == 1 && isset($_FILES["userfile"]["tmp_name"]))
{
    echo "<CENTER><FONT COLOR=blue><BIG><B>$mess_plug_ajout</B></BIG></FONT>" ;
    $nom_final = $_FILES["userfile"]["name"];
    $dir="images";
    $dest_file = $dir."/".$nom_final;
    $fichier = $dir."/".$nom_final;
    list($extension,$nom) = getextension($_FILES["userfile"]["name"]);
    if (strtolower($extension) != "gif" && strtolower($extension) != "png" && strtolower($extension) != "jpg" && strtolower($extension) != "jpeg")
    {
           $mess_notif = $_FILES["userfile"]["name"]." : extension du fichier non autorisée.";
    }
    else
    {
        $source_file=$_FILES["userfile"]["tmp_name"];
        $copier=move_uploaded_file($source_file , $dest_file);
        $id = Donne_ID ($connect,"select max(plug_cdn) from plugins");
        $change_etat = mysql_query("insert into plugins values ($id,\"$titre\",\"$nom_final\",\"$url\",\"$descript\")");
        echo "</center>";
        $mess_notif = $_FILES["userfile"]["name"]." : a été téléchargé.";
    }
    $lien="moteurs.php?plugins=1&mess_notif=$mess_notif";
    $lien=urlencode($lien);
    echo "<script language='JavaScript'>
            document.location.replace(\"trace.php?link=$lien\");
    </script>";
exit;
}
if (isset($modif) && $modif == 1 && !isset($telecharger))
{
  include 'style.inc.php';
  echo "<CENTER><TABLE bgColor='#298CA0' cellspacing='2'><TR><TD>";
  echo "<TABLE bgColor='#FFFFFF' cellspacing='1'  cellpadding='0' width='100%'>";
  echo "<TR><TD background=\"images/fond_titre_table.jpg\" colspan='3' height='37' align='center' valign='center'><Font size='3' color='#FFFFFF'><B>$mess_menu_plug</B></FONT></TD></TR>";
  echo "<TR><TD bgColor='#CEE6EC'>";
    $nom =GetDataField ($connect,"select plug_tit_lb from plugins where plug_cdn='$num'","plug_tit_lb");
    $adr =GetDataField ($connect,"select plug_adr_cmt from plugins where plug_cdn='$num'","plug_adr_cmt");
    $img =GetDataField ($connect,"select plug_img_lb from plugins where plug_cdn='$num'","plug_img_lb");
    $desc =GetDataField ($connect,"select plug_desc_cmt from plugins where plug_cdn='$num'","plug_desc_cmt");
    echo "<FORM name='form' action=\"moteurs.php?modif=1&telecharger=1&num=$num\" method='POST' ENCTYPE='multipart/form-data'>";
    echo "<TABLE bgColor='#CEE6EC' cellspacing='1'  cellpadding='4'><TR><TD><B>$mess_plug_nom_img</B></TD><TD>";
    if ($mac != 1){
          if (strstr(getenv("HTTP_USER_AGENT"),"MSIE")){
            echo "<DIV style=\"position :absolute ;\">";
            echo "<INPUT TYPE='file' name='userfile' style=\"filter :alpha(opacity=0)\"".
                 "onfocus=\"document.form.txtFile.value=document.form.userfile.value\" ".
                 "onchange=\"document.form.txtFile.value=document.form.userfile.value\">";
            echo "</DIV>";
            echo "<input type='text' class='INPUT'  name='txtFile'";
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
            echo "<INPUT TYPE='text' class='INPUT'  name='txtFile' onMouseover=\"makevisible(this,0)\" onMouseout=\"makevisible(this,0)\">".
                 "<IMG NAME=\"img8\" SRC=\"images/fiche_identite/boutfichier.gif\" align=\"absmiddle\" BORDER='0' ".
                 "onLoad=\"tempImg=new Image(0,0); tempImg.src='images/fiche_identite/boutfichierb.gif'\">";
          }
    }
    else
          echo "<INPUT TYPE='file' name='userfile' enctype='multipart/form-data'>";
    echo "</TD></TR>";
    echo "<INPUT type='hidden' name='MAX_FILE_SIZE' value='100000'>
        <INPUT type='hidden' name='img' value='$img'>
        <TR><TD><B>$mess_plug_titre</B></TD>
        <TD><INPUT type='text' class='INPUT'  name='titre' value='$nom' size=80></SMALL></TD></TR>
        <TR><TD><B>$mess_plug_link</B></B></TD>
        <TD><INPUT type='text' class='INPUT'  name='url' value='$adr' size=80></SMALL></TD></TR>
        <TR><TD><B>$mess_plug_desc</B></B></TD>
        <TD><TEXTAREA class='TEXTAREA' name='descript' row='2' cols='60'>$desc</TEXTAREA></TD></TR>";
    echo "<TR><TD></TD><TD align='left'><A HREF=\"javascript:document.form.submit();\" onmouseover=\"img1.src='images/fiche_identite/boutvalidb.gif';return true;\" onmouseout=\"img1.src='images/fiche_identite/boutvalid.gif'\">";
    echo "<IMG NAME=\"img1\" SRC=\"images/fiche_identite/boutvalid.gif\" BORDER='0' onLoad=\"tempImg=new Image(0,0); tempImg.src='images/fiche_identite/boutvalidb.gif'\"></A></TD></TR> ";
    echo "<TR><TD align=left><A HREF=\"javascript:history.back();\" onmouseover=\"img_annonce.src='images/fiche_identite/boutretourb.gif';return true;\" onmouseout=\"img_annonce.src='images/fiche_identite/boutretour.gif'\">";
    echo "<IMG NAME=\"img_annonce\" SRC=\"images/fiche_identite/boutretour.gif\" BORDER='0' onLoad=\"tempImg=new Image(0,0); tempImg.src='images/fiche_identite/boutretourb.gif'\"></A></TD></TR>";
   echo "</TABLE></FORM></TD></TR></TABLE></TD></TR></TABLE>";
exit;
}
if (isset($modif) && $modif == 1 && isset($telecharger) && $telecharger == 1)
{
  if (isset($_FILES["userfile"]["tmp_name"]))
  {
    list($extension,$nom) = getextension($_FILES["userfile"]["name"]);
    if (strtolower($extension) != "gif" && strtolower($extension) != "png" && strtolower($extension) != "jpg" && strtolower($extension) != "jpeg")
    {
           $mess_notif = $_FILES["userfile"]["name"]." : extension du fichier non autorisée.";
           $nom_final = $img;
    }
    else
    {
      $nom_final = $_FILES["userfile"]["name"];
      $dir="images";
      $dest_file = $dir."/".$nom_final;
      $fichier = $dir."/".$nom_final;
      $source_file = $_FILES["userfile"]["tmp_name"];
      $copier=move_uploaded_file($source_file , $dest_file);
      $mess_notif = $_FILES["userfile"]["name"]." : a été téléchargé.";
    }
  }
  else
      $nom_final = $img;
  $change_etat = mysql_query("update plugins set plug_tit_lb=\"$titre\",plug_img_lb=\"$nom_final\",plug_adr_cmt=\"$url\",plug_desc_cmt=\"$descript\" where plug_cdn=$num");
  echo "</center>";
  $lien="moteurs.php?plugins=1&mess_notif=$mess_notif";
  $lien=urlencode($lien);
  echo "<script language=\"JavaScript\">";
    echo "document.location.replace(\"trace.php?link=$lien\")";
  echo "</script>";
exit;
}
if (isset($supprimer) && $supprimer == 1)
{
  $requete = mysql_query ("delete from plugins where plug_cdn='$num'");
  $lien="moteurs.php?plugins=1";
  $lien=urlencode($lien);
  echo "<script language=\"JavaScript\">";
  echo "document.location.replace(\"trace.php?link=$lien\")";
  echo "</script>";
exit;
}
function getextension($fichier)
{
  $bouts = explode(".", $fichier);
  return array(array_pop($bouts), implode(".", $bouts));
}
?>