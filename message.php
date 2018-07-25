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
dbConnect();
include 'style.inc.php';
//include ("click_droit.txt");
$today = date("Y-m-d");
$ch_date_fin = explode ("-",$today);
$day = $ch_date_fin[2];
$month = $ch_date_fin[1];
$year = $ch_date_fin[0];
if ($supprimer == 1)
{
   $requete = mysql_query("DELETE FROM message WHERE msg_cdn = '$id_max'");
   if (isset($nbr) && $nbr > 1)
      $voir_message = 1;
}
if ($voir_message == 1)
{
  $suiteReq = ($_SESSION['typ_user'] == 'ADMINISTRATEUR') ? "": "msg_auteur_no = $id_user AND ";
  if ($type == "apprenant")
     $requete = mysql_query("SELECT * from message where $suiteReq msg_apprenant_no = $num AND msg_groupe_no = $son_groupe");
  elseif($type =="groupe")
     $requete = mysql_query("SELECT * from message where $suiteReq msg_groupe_no=$num AND msg_apprenant_no = 0");
  elseif($type =="tous")
     $requete = mysql_query("SELECT * from message where $suiteReq msg_tous_on=1");
  $nb_msg = mysql_num_rows($requete);
  if ($nb_msg == 0)
  {
    entete_simple($mess_alert);
    echo"<TR><TD height='50' align='center'><FONT SIZE='2'>$mess_aff</FONT>";
    echo "</TD></TR></TABLE></TD></TR></TABLE>";
    exit;
  }
  if ($type == "apprenant")
  {
     $nom_app = GetDataField ($connect,"SELECT util_nom_lb  from utilisateur where util_cdn = $num","util_nom_lb");
     $prenom_app = GetDataField ($connect,"SELECT util_prenom_lb  from utilisateur where util_cdn = $num","util_prenom_lb");
     $suite = "&nbsp;".strtolower($mess_admin_sup_app_deb)."&nbsp;$prenom_app $nom_app</font>";
  }
  elseif($type == "groupe")
  {
     $nom_grp =GetDataField ($connect,"SELECT grp_nom_lb from groupe where grp_cdn = $num","grp_nom_lb");
     $suite = "&nbsp;".strtolower($mess_menu_mail_app)."&nbsp;$mess_menu_gestion_grp $nom_grp</font>";
  }
  elseif($type =="tous")
     $suite = "&nbsp;".strtolower($mess_menu_mail_tous)."</font>";
  entete_simple("$mess_alert $pour $suite");
  //echo "<TR><TD colspan='2' class='sous_titre'>$pour $suite</TD></TR>";
  $i=0;
  while ($i < $nb_msg)
  {
     $id_msg = mysql_result($requete,$i,"msg_cdn");
     $contenus =mysql_result($requete,$i,"msg_contenu_cmt");
     $auteur =mysql_result($requete,$i,"msg_auteur_no");
     $contenu = html_entity_decode($contenus,ENT_QUOTES,'ISO-8859-1');
     $date_deb = mysql_result($requete,$i,"msg_dhdeb_dt");
     $ch_date_deb = explode ("-",$date_deb);
     $date_deb1 =$ch_date_deb[2]."-".$ch_date_deb[1]."-".$ch_date_deb[0];
     $date_fin = mysql_result($requete,$i,"msg_dhfin_dt");
     $ch_date_fin = explode ("-",$date_fin);
     $date_fin1 =$ch_date_fin[2]."-".$ch_date_fin[1]."-".$ch_date_fin[0];
     $suiteAdmin = ($_SESSION['typ_user'] == 'ADMINISTRATEUR') ? "<TD nowrap valign='top'>".NomUser($auteur)."</TD>" : "";
     $contents .= couleur_tr($i+1,'')."<TD align='left' valign='top'>$contenu</TD>$suiteAdmin".
                                      "<TD nowrap valign='top'>$date_deb1</TD><TD nowrap valign='top'>$date_fin1</TD>";
     //if ($date_fin > $today || $date_fin == $today)
     //{
       $o_k = 1;
       $lien = "message.php?modifier=1&id_max=$id_msg&type=$type&num=$num&son_groupe=$son_groupe";
       $lien = urlencode($lien);
       $contents .= "<TD align='center' valign='top'><A HREF=\"trace.php?link=$lien\" ".bulle($mrc_app_modif,"","CENTER","ABOVE",120);
       $contents .= "<IMG SRC=\"images/repertoire/icoGrenomfich20.gif\" border=0></TD>";
       $lien = "message.php?supprimer=1&id_max=$id_msg&type=$type&num=$num&nbr=$nb_msg&son_groupe=$son_groupe";
       $lien = urlencode($lien);
       $contents .= "<TD align='center' valign='top'><A href=\"javascript:void(0);\" ".
                    "onclick=\"javascript:confm('trace.php?link=$lien');\" ".
                    bulle($mess_admin_label_sup,"","CENTER","ABOVE",120).
                    "<IMG SRC=\"images/messagerie/icopoubelressour.gif\" border=0></TD>";
     //}
     $contents .= "</TR>";
    $i++;
  }
  echo "<TR><TD><TABLE width=100% Cellpadding='4' cellspacing='4' width='100%' bgColor='#FFFFFF'><TR bgcolor='#2B677A'>";
  echo "<TD><Font color='white'><B>$mess_mail_mess</B></font></TD>";
  if ($_SESSION['typ_user'] == 'ADMINISTRATEUR')
     echo "<TD><Font color='white'><B>$mrc_aut</B></font></TD>";
  echo "<TD><Font color='white'><B>$mess_admin_date_debut</B></font></TD>";
  echo "<TD><Font color='white'><B>$mess_admin_date_fin</B></font></TD>";
  if ($o_k == 1)
  {
     echo "<TD><Font color='white'><B>$mess_modif_base</B></font></TD>";
     echo "<TD><Font color='white'><B>$mess_ag_supp</B></font></TD></TR>";
  }
  echo $contents;
  echo "<TR><TD align=left><A HREF=\"javascript:history.back();\" onmouseover=\"img_annonce.src='images/fiche_identite/boutretourb.gif';return true;\" onmouseout=\"img_annonce.src='images/fiche_identite/boutretour.gif'\">";
  echo "<IMG NAME=\"img_annonce\" SRC=\"images/fiche_identite/boutretour.gif\" BORDER='0' onLoad=\"tempImg=new Image(0,0); tempImg.src='images/fiche_identite/boutretourb.gif'\"></A>";
  echo "</TABLE>";
  echo "</TD></TR>";
  echo "</TD></TR></TABLE></TD></TR></TABLE>";
exit;
}
if ($insert_mess == 1)
{
  function jour_mois($mois,$an)
  {
    return date("t",mktime(0,0,0,$mois,1,$an));
  }
    if ($iday1 && $imonth1 && $iyear1)
      $date_deb="$imonth1/$iday1/$iyear1";
    $ch_date_deb = explode ("/",$date_deb);
    $date_deb = "$ch_date_deb[2]-$ch_date_deb[1]-$ch_date_deb[0]";
    $jour=intval($ch_date_deb[0]);
    $mois=intval($ch_date_deb[1]);
    $an=$ch_date_deb[2];
    $retour=@jour_mois($mois,$an);
    if ($jour > $retour && $mois < 13)
    {
      $mess_aff .= "$il_nya_que <B>$retour</B> $mess_jours $en <B>".le_mois($mois)." $an</B>";
      $signal = 1;
    }
    if ($iday && $imonth && $iyear)
      $date_fin="$imonth/$iday/$iyear";
    $ch_date_fin = explode ("/",$date_fin);
    $date_fin = "$ch_date_fin[2]-$ch_date_fin[1]-$ch_date_fin[0]";
    $jour=intval($ch_date_fin[0]);
    $mois=intval($ch_date_fin[1]);
    $an=$ch_date_fin[2];
    $retour=@jour_mois($mois,$an);
    if($jour > $retour && $mois < 13)
    {
      if ($mess_aff != '')
         $mess_aff .= '<br />';
      $mess_aff .= "$il_nya_que <B>$retour</B> $mess_jours $en <B>".le_mois($mois)." $an</B>";
      $signal = 1;
    }
    if ($signal == 1)
    {
      $lien = "message.php?type=$type&num=$num&son_groupe=$son_groupe&mess_notif=$mess_aff";
      $lien = urlencode($lien);
      echo "<script language=\"JavaScript\">";
      echo "       document.location.href=\"trace.php?link=$lien\"";
      echo "</script>";
     exit();
    }
    $contenu = str_replace("\n","<br />",$contenu);
    if ($modifier == 1){
        $req_modif = mysql_query("UPDATE message SET msg_contenu_cmt=\"".htmlentities($contenu)."\",msg_dhdeb_dt=\"$date_deb\",msg_dhfin_dt =\"$date_fin\" where msg_cdn= '$id_max'");
    }else{
      $id_max = Donne_ID ($connect,"SELECT max(msg_cdn) from message");
      if ($type == "apprenant")
        $requete = mysql_query("INSERT INTO message VALUES($id_max,\"".htmlentities($contenu,ENT_QUOTES,'ISO-8859-1')."\",$id_user,$son_groupe,$num,0,\"$date_deb\",\"$date_fin\")");
      elseif($type =="groupe")
        $requete = mysql_query("INSERT INTO message VALUES($id_max,\"".htmlentities($contenu,ENT_QUOTES,'ISO-8859-1')."\",$id_user,$num,0,0,\"$date_deb\",\"$date_fin\")");
      elseif($type =="tous")
        $requete = mysql_query("INSERT INTO message VALUES('$id_max',\"".htmlentities($contenu,ENT_QUOTES,'ISO-8859-1')."\",'$id_user','0','0','1',\"$date_deb\",\"$date_fin\")");
    }
    $lien = "message.php?type=$type&num=$num&son_groupe=$son_groupe&confirmation=1&modif=$modif";
    $lien = urlencode($lien);
    echo "<script language=\"JavaScript\">";
      echo "document.location.replace(\"trace.php?link=$lien\")";
    echo "</script>";
  exit();
}
 //--------------------------
 ?>
  <SCRIPT language=JavaScript>
    function checkForm(frm) {
      var ErrMsg = "<?php echo $mess_info_no;?>\n";
      var lenInit = ErrMsg.length;
      if (isEmpty(frm.contenu)==true)
        ErrMsg += ' - <?php echo addslashes($mess_alert);?>\n';
      if (isEmpty(frm.imonth1)==true)
        ErrMsg += ' - <?php echo "Jour début";?>\n';
      if (isEmpty(frm.iday1)==true)
        ErrMsg += ' - <?php echo "Mois début";?>\n';
      if (isEmpty(frm.iday1)==false && isvaleur(frm.iday1) > 12)
        ErrMsg += ' - <?php echo "Il n\'y a que 12 mois dans l\'année";?>\n';
      if (isEmpty(frm.iyear1)==true)
       ErrMsg += ' - <?php echo "Année début";?>\n';
      if (isEmpty(frm.imonth)==true)
        ErrMsg += ' - <?php echo "Jour fin";?>\n';
     if (isEmpty(frm.iday)==true)
        ErrMsg += ' - <?php echo "Mois fin";?>\n';
      if (isEmpty(frm.iday)==false && isvaleur(frm.iday) > 12)
        ErrMsg += ' - <?php echo "Il n\'y a que 12 mois dans l\'année";?>\n';
      if (isEmpty(frm.iyear)==true)
       ErrMsg += ' - <?php echo "Année fin";?>\n';
      if (isEmpty(frm.iyear1)==false && isEmpty(frm.iyear)==false && isvaleur(frm.iyear1) > isvaleur(frm.iyear))
        ErrMsg += ' - <?php echo "L\'année de fin doit être supérieure à l\'année de début";?>\n';
      if (isEmpty(frm.iyear1)==false && isEmpty(frm.iyear)==false && isvaleur(frm.iyear1) == isvaleur(frm.iyear) && isvaleur(frm.imonth1) > isvaleur(frm.imonth) && isvaleur(frm.iday1) == isvaleur(frm.iday))
        ErrMsg += ' - <?php echo "Le jour de fin doit être supérieur ou égal au jour de début pour une même année et même mois";?>\n';
      if (isEmpty(frm.iyear1)==false && isEmpty(frm.iyear)==false && isvaleur(frm.iyear1) == isvaleur(frm.iyear) && isvaleur(frm.iday1) > isvaleur(frm.iday))
        ErrMsg += ' - <?php echo "Le mois de fin doit être supérieur ou égal au mois de début pour une même année";?>\n';

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
    function isvaleur(elm) {
      var valeur = elm.value;
      return valeur;
    }
  </SCRIPT>
<?php
  //------------------------------------
if ($type == "apprenant")
{
   $nom_app = GetDataField ($connect,"SELECT util_nom_lb from utilisateur where util_cdn = $num","util_nom_lb");
   $prenom_app = GetDataField ($connect,"SELECT util_prenom_lb from utilisateur where util_cdn = $num","util_prenom_lb");
   $suite = "&nbsp;".strtolower($mess_admin_sup_app_deb)."&nbsp;$prenom_app $nom_app";
   $requete = mysql_query("SELECT * from message where msg_apprenant_no = $num AND msg_auteur_no = $id_user AND msg_groupe_no = $son_groupe");
}
elseif($type =="groupe"){
   $nom_grp =GetDataField ($connect,"SELECT grp_nom_lb from groupe where grp_cdn = $num","grp_nom_lb");
   $suite = "&nbsp;".strtolower($mess_menu_mail_app)."&nbsp;$mess_menu_gestion_grp $nom_grp";
   $requete = mysql_query("SELECT * from message where msg_groupe_no = $num AND msg_auteur_no = $id_user AND msg_apprenant_no = 0");
}
elseif($type =="tous")
{
   $suite= "&nbsp;".strtolower($mess_menu_mail_tous);
   $requete = mysql_query("SELECT * from message where msg_tous_on = 1 AND msg_auteur_no = $id_user ");
}
$nb_msg = mysql_num_rows($requete);
if ($confirmation == 1 && $modif == 1)
   echo notifier($mess_msg_modif);
elseif ($confirmation == 1 && $modif != 1)
   echo notifier($mess_msg_insert);
entete_simple("$mess_alert $pour $suite");
if (isset($mess_notif) && $mess_notif != '')
   echo notifier($mess_notif);
echo aide("annonce",20);
//echo "<TR><TD colspan='2' class='sous_titre'>$pour $suite</TD></TR>";
if ($nb_msg > 0 && $modifier != 1)
{
  $lien = "message.php?voir_message=1&type=$type&num=$num&son_groupe=$son_groupe";
  $lien = urlencode($lien);
  echo "<TR><TD><TABLE cellspacing='1' cellpadding='4' bgColor='#FFFFFF'><TR>".
       "<TD valign='top'><A HREF=\"trace.php?link=$lien\" title =\"$mess_voir_msg $suite\">";
  echo "<IMG SRC=\"images/icovoir.gif\" ALT=\"$mess_voir_msg $suite\" border=0></A></TD>".
       "<TD valign='middle'>$mess_voir_msg $suite</TD></TR></TABLE></TD></TR>";
}
if ($modifier == 1)
{
  $requete = mysql_query("SELECT * from message where msg_cdn = $id_max");
  $contenu = mysql_result($requete,0,"msg_contenu_cmt");
  $contenu = str_replace("<BR>","",$contenu);
  $auteur = mysql_result($requete,0,"msg_auteur_no");
  $groupe = mysql_result($requete,0,"msg_groupe_no");
  $apprenant = mysql_result($requete,0,"msg_apprenant_no");
  $tous = mysql_result($requete,0,"msg_tous_on");
  $date_deb = mysql_result($requete,0,"msg_dhdeb_dt");
  $date_fin = mysql_result($requete,0,"msg_dhfin_dt");
  $ch_date_deb = explode ("-",$date_deb);
  $day = $ch_date_deb[2];
  $month = $ch_date_deb[1];
  $year = $ch_date_deb[0];
  $ch_date_fin = explode ("-",$date_fin);
  $day1 = $ch_date_fin[2];
  $month1 = $ch_date_fin[1];
  $year1 = $ch_date_fin[0];
  if ($tous == 1){
    $type="tous";
    $num=1;
  }elseif ($groupe > 0 && $apprenant == 0){
    $type = "groupe";
    $num = $groupe;
  }elseif ($groupe > 0 && $apprenant > 0){
    $type = "apprenant";
    $num = $apprenant;
  }
  echo "<FORM NAME=\"MForm\" ACTION=\"message.php?insert_mess=1&modifier=1&type=$type&num=$num&son_groupe=$son_groupe&id_max=$id_max&modif=1\" METHOD='post'>";
}else
  echo "<FORM NAME=\"MForm\" ACTION=\"message.php?insert_mess=1&type=$type&num=$num&son_groupe=$son_groupe\" METHOD='post'>";
?>
 <TR><TD valign ='center'><TABLE bgColor='#FFFFFF' width='100%'>
 <TR>
      <TD nowrap valign ='center'>
           <?php echo "<B>$mess_gp_date_deb *</B>";?>
      </TD>
      <TD nowrap valign ='center'><TABLE><TR>
      <input type="hidden" name=txt_custom value="AppendOrReplace=Replace;AppendChar=';';CloseOnSelect=Yes;ReturnData=Date;Title=Calendrier;InlineX=360;InlineY=70;CurrentDate=Today;SelectAfter=Today-30;SelectBefore=Today+30;AllowWeekends=No;Resizable=Yes;CallFunction=TryCallFunction1;PopupX=300;PopupY=300;Nav=Yes;SmartNav=Yes;Fix=No;WeekStart=1;Weekends=06">
      <TD nowrap valign ='center'><input type="TEXT" class='INPUT'  name="imonth1" value="<?php echo $day;?>" size="2" MAXLENGTH="2"></TD>
      <TD nowrap valign ='center'><input type="TEXT" class='INPUT'  name="iday1" value="<?php echo $month;?>" size="2" MAXLENGTH="2"></TD>
      <TD nowrap valign ='center'><input type="TEXT" class='INPUT'  name="iyear1" value="<?php echo $year;?>" size="4" MAXLENGTH="4"></TD>
      <input type="hidden" value="" name="ma_date" size=10>
      <TD nowrap valign ='center'><a href="javascript:show_calendar('MForm.ma_date','09', '2002', 'DD/MM/YYYY', 'INLINE', MForm.txt_custom.value);" onmouseover="window.status='<?php  echo "$cal_click"  ;?>';return true;" onmouseout="window.status='';return true;"><img src="images/agenda/icocalend.gif" border=0 alt="<?php  echo "$cal_click"  ;?>"></a></TD>
     </TR></TABLE></TD>
   </TR>
   <TR>
      <TD nowrap valign ='center'>
          <B> <?php echo "$mess_gp_date_fin *";?> </B>
      </TD>
      <TD valign ='center'><TABLE><TR>
      <input type="hidden" name=txt_custom1 value="AppendOrReplace=Replace;AppendChar=';';CloseOnSelect=Yes;ReturnData=Date;Title=<Calendrier;InlineX=360;InlineY=70;CurrentDate=Today;SelectAfter=Today-30;SelectBefore=Today+30;AllowWeekends=No;Resizable=Yes;CallFunction=TryCallFunction;PopupX=300;PopupY=300;Nav=Yes;SmartNav=Yes;Fix=No;WeekStart=1;Weekends=06">
      <TD nowrap valign ='center'><input type="TEXT" class='INPUT'  name="imonth" value="<?php  if (isset($day1)) echo $day1; else echo $day;?>" size="2" MAXLENGTH="2"></TD>
      <TD nowrap valign ='center'><input type="TEXT" class='INPUT'  name="iday" value="<?php if (isset($month1)) echo $month1; else echo $month;?>" size="2" MAXLENGTH="2"></TD>
      <TD nowrap valign ='center'><input type="TEXT" class='INPUT'  name="iyear" value="<?php if (isset($year1)) echo $year1; else echo $year;?>" size="4" MAXLENGTH="4"></TD>
      <input type="hidden" value="" name="mydate1">
      <TD nowrap valign ='center'><a href="javascript:show_calendar('MForm.mydate1','09', '2002', 'DD/MM/YYYY', 'INLINE', MForm.txt_custom1.value);" onmouseover="window.status='<?php  echo "$cal_click"  ;?>';return true;" onmouseout="window.status='';return true;"><img src="images/agenda/icocalend.gif" border=0 alt="<?php  echo "$cal_click";?>"></a></TD>
     </TR></TABLE></TD>
   <TR>
      <TD nowrap valign ='center'>
          <B> <?php echo $mess_mail_mess." *";?>  </B>
      </TD>
      <TD nowrap valign ='center'>
         <TEXTAREA class='TEXTAREA'  name="contenu" rows="8" cols="80" align="middle"><?php echo stripslashes($contenu);?></TEXTAREA>
               <BR>
      </TD>
   </TR>
  <TR>
<?php
if ($modifier == 1){
  echo "<TD align=left><A HREF=\"javascript:history.back();\" onmouseover=\"img_annonce.src='images/fiche_identite/boutretourb.gif';return true;\" onmouseout=\"img_annonce.src='images/fiche_identite/boutretour.gif'\">";
  echo "<IMG NAME=\"img_annonce\" SRC=\"images/fiche_identite/boutretour.gif\" BORDER='0' onLoad=\"tempImg=new Image(0,0); tempImg.src='images/fiche_identite/boutretourb.gif'\"></A></TD>";
  echo "<TD align='left'><A HREF=\"javascript:checkForm(document.MForm);\" onmouseover=\"img1.src='images/fiche_identite/boutvalidb.gif';return true;\" onmouseout=\"img1.src='images/fiche_identite/boutvalid.gif'\">".
       "<IMG NAME=\"img1\" SRC=\"images/fiche_identite/boutvalid.gif\" BORDER='0' onLoad=\"tempImg=new Image(0,0); tempImg.src='images/fiche_identite/boutvalidb.gif'\"></A>";
}else{
  echo "<TD></TD><TD align='left'><A HREF=\"javascript:checkForm(document.MForm);\" onClick=\"TinyMCE.prototype.triggerSave();\" onmouseover=\"img1.src='images/fiche_identite/boutvalidb.gif';return true;\" onmouseout=\"img1.src='images/fiche_identite/boutvalid.gif'\">".
       "<IMG NAME=\"img1\" SRC=\"images/fiche_identite/boutvalid.gif\" BORDER='0' onLoad=\"tempImg=new Image(0,0); tempImg.src='images/fiche_identite/boutvalidb.gif'\"></A>";
}
echo "</TD></TR></FORM></TABLE>";
echo "</TD></TR></TABLE></TD></TR></TABLE>\n";
//------------------------------------------
?>
<div id="mien" class="cms"></div>
<DIV ID=Main>
  <!-- A Separate Layer for the Calendar -->
  <!-- Make sure to use the name Calendar for this layer -->
  <SCRIPT Language="Javascript" TYPE="text/javascript">
      Calendar.CreateCalendarLayer(10, 275, "");
  </SCRIPT>
</DIV>
</body></html>