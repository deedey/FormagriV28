<?php
if (!isset($_SESSION)) session_start();
$aSuperGlobal = array ('_GET','_FILES','_COOKIE','_POST','_COOKIE','_SESSION');
foreach ($aSuperGlobal as $superGlobal)
{
       foreach ($GLOBALS[$superGlobal] as $key => $superGlobalVal)
       {
               $$key = $superGlobalVal;
       }
}
if (isset($ajout_mod_app) && $ajout_mod_app == 1)
{
   if (!isset($_SESSION['id_user']) || $_SESSION['id_user'] == "")
    {
       exit();
    }
   require '../admin.inc.php';
   require '../fonction.inc.php';
   require '../fonction_html.inc.php';
   require '../langues/prescription.inc.php';
   require '../langues/formation.inc.php';
   require '../class/class_formation.php';
   require '../class/class_suivi.inc.php';
   require "../lang".$_SESSION['lg'].".inc.php";
   dbConnect();
   include('../style.inc.php');
}
?>
<SCRIPT language=JavaScript>
function checkForm(frm)
{
  var ErrMsg = "<?php echo $mess_info_no;?>\n";
  var lenInit = ErrMsg.length;
  if (isEmpty(frm.appel_parc)==true )
    ErrMsg += ' - <?php echo $msq_parc;?>\n';
  if (isEmpty(frm.appel_form)==true)
    ErrMsg += ' - <?php echo $msq_formateur;?>\n';
  if (isEmpty(frm.date_deb)==true)
    ErrMsg += ' - <?php echo $mess_gp_date_deb;?>\n';
  if (isEmpty(frm.date_fin)==true)
    ErrMsg += ' - <?php echo $mess_gp_date_fin;?>\n';
  if (ErrMsg.length > lenInit)
    alert(ErrMsg);
  else{
    frm.submit();
    self.close();
  }
}
function checkFormulaire(frm)
{
  var ErrMsg = "<?php echo $mess_info_no;?>\n";
  var lenInit = ErrMsg.length;
  if (isEmpty(frm.form_ref)==true || frm.form_ref.value == -1)
    ErrMsg += ' - <?php echo $msq_formateur;?>\n';
  if (isEmpty(frm.date_deb)==true)
    ErrMsg += ' - <?php echo $mess_gp_date_deb;?>\n';
  if (isEmpty(frm.date_fin)==true)
    ErrMsg += ' - <?php echo $mess_gp_date_fin;?>\n';
  if (ErrMsg.length > lenInit)
    alert(ErrMsg);
  else{
    frm.submit();
  }
}
function isEmpty(elm)
{
  var elmstr = elm.value + "";
  if (elmstr.length == 0)
    return true;
  return false;
}
</SCRIPT>
<?php
 $today = date ("d/n/Y");
 $ch_date_fin = explode ("/",$today);
 $day = $ch_date_fin[0];
 if (strlen($day) == 1)
   $day = "0$day";
 $month = $ch_date_fin[1];
 if (strlen($month) == 1)
   $month = "0$month";
 $year_jour = $ch_date_fin[2];
 $day_jour = "$day";
 $month_jour = "/$month/";
 $datedujour = $day_jour.$month_jour.$year_jour;
 if (isset($parc))
 {
    $parc_query_presc = mysql_query("SELECT * from parcours where parcours_cdn = $parc");
    $date_creat = mysql_result ($parc_query_presc,0,"parcours_create_dt");
    $ch_dtc = explode("-",$date_creat);
    $dtc = strftime("%A %e %B %Y", mktime(0, 0, 0, $ch_dtc[1],$ch_dtc[2],$ch_dtc[0]));
    $date_modif = mysql_result ($parc_query_presc,0,"parcours_modif_dt");
    $ch_dtm = explode("-",$date_modif);
    $dtm = strftime("%A %e %B %Y", mktime(0, 0, 0, $ch_dtm[1],$ch_dtm[2],$ch_dtm[0]));
    $createur = mysql_result ($parc_query_presc,0,"parcours_auteur_no");
    $nom_parcours = mysql_result ($parc_query_presc,0,"parcours_nom_lb");
    $description = mysql_result ($parc_query_presc,0,"parcours_desc_cmt");
    $description = html_entity_decode($description,ENT_QUOTES,'ISO-8859-1');
    $id_ref_parc = mysql_result ($parc_query_presc,0,"parcours_referentiel_no");
    if ($id_ref_parc > 0)
    {
       $nom_referentiel = GetDataField ($connect,"select ref_desc_cmt from referentiel where ref_cdn = $id_ref_parc","ref_desc_cmt");
       $desc = $nom_referentiel;
    }
    $type_parcours = getdatafield ($connect,"SELECT parcours_type_lb from parcours where parcours_cdn = $parc","parcours_type_lb");
    $nom_createur = GetDataField ($connect,"select util_nom_lb from utilisateur where util_cdn = $createur","util_nom_lb");
    $prenom_createur = GetDataField ($connect,"select util_prenom_lb from utilisateur where util_cdn = $createur","util_prenom_lb");
    $nomb_seq = mysql_result(mysql_query ("SELECT count(seqparc_seq_no) FROM sequence_parcours WHERE
                                           seqparc_parc_no = $parc ORDER BY seqparc_ordre_no asc"),0);
    $seq_parc_query_presc = mysql_query ("select sum(sequence.seq_duree_nb) from
                                          sequence,sequence_parcours where seq_cdn = seqparc_seq_no AND
                                          seqparc_parc_no = $parc AND seq_type_on = '0'");
    $duree_parc = duree_calc(mysql_result($seq_parc_query_presc,0));
 }
 $nom_prescripteur = GetDataField ($connect,"select util_nom_lb from utilisateur where util_cdn = $id_user","util_nom_lb");
 $prenom_prescripteur = GetDataField ($connect,"select util_prenom_lb from utilisateur where util_cdn = $id_user","util_prenom_lb");
 $util = ($utilisateur > 0) ? $utilisateur : '-1';
//    echo "<pre>".print_r ($_SESSION)."</pre>";
 if (isset($ajout_mod_app) && $ajout_mod_app == 1)
      echo "<FORM name=\"MForm\" action=\"../prescription.php?sq=$sq&pc=$pc&rf=$rf&comeback=$comeback&parc=$parc&util=$util&kaler=$kaler&presc=$presc&insert_presc=1&id_ref=$id_ref&numero_groupe=$numero_groupe\" method='post' target='main'>";
 else
      echo "<TR><FORM name=\"MForm\" action=\"prescription.php?sq=$sq&pc=$pc&rf=$rf&comeback=$comeback&parc=$parc&util=$util&kaler=$kaler&presc=$presc&insert_presc=1&id_ref=$id_ref\" method='post' target='main'>";
 if (!isset($parc))
 {
    echo "<input type=hidden name='numero_groupe' value='$numero_groupe'>";
    echo "<input type=hidden name='utilisateur' value='$util'>";
    echo "<center><table bgColor='#298CA0' cellspacing='2' cellpadding='0' width='98%'><tbody><tr><td>";
    echo "<table bgColor='#FFFFFF' cellspacing='1' cellpadding='0' width='100%'><tbody>";
    echo "<tr><td background=\"../images/fond_titre_table.jpg\" colspan='2' height='40' align='center' valign='center'>".
         "<Font size='3' color='#FFFFFF'><B>$mess_ajt_mod</FONT></td></tr>";
    echo '<TR><TD colspan="2"><table cellpadding="4" cellspacing="0">';
   ?>
    <script type="text/javascript">
     $().ready(function() {

        function findValueCallback(event, data, formatted) {
                $("<li>").text( !data ? "No match!" : "Selected: " + formatted).appendTo("#result");
        }

        function formatItem(row) {
                return row[0];
        }

        function formatResult(row) {
                return row[0] + "              | id: " + row[1];
        }


        <?php
        if (isset($ajout_mod_app) && $ajout_mod_app == 1)
        {
        ?>
             $("#appel_parc").autocompleting("../prescription/ajout_parc_app.php", {
        <?php
        }
        else
        {
        ?>
             $("#appel_parc").autocompleting("prescription/ajout_parc_app.php", {
        <?php
        }
        ?>
                delay: 150,
                width: 250,
                max: 15,
                formatItem: formatItem,
                formatResult: formatResult,
                minChars: 1,
                selectFirst: false
        });
        <?php
        if (isset($ajout_mod_app) && $ajout_mod_app == 1)
        {
        ?>
             $("#appel_form").autocompleting("../prescription/ajout_form_app.php", {
                delay: 150,
                width: 250,
                max: 15,
                formatItem: formatItem,
                formatResult: formatResult,
                minChars: 1,
                selectFirst: false
        });
        <?php
        }
        ?>
     });
     </script>
    <?php
    echo "<tr height='40'><td valign='top'><b>$msq_prescripteur</b></td><TD valign='top' nowrap><b>$nom_prescripteur $prenom_prescripteur</b</td></tr>";
    echo "<tr height='40'><td valign='top'><b>$msq_parc</b></td>";
    echo '<TD valign="top"><input type="text" name="appel_parc" id="appel_parc" value= "'.$msg_ajt_mod_app.'"  onClick="javascript:var changer=getElementById(\'appel_parc\');changer.value=\'\';"/></TD></tr>';
    $presc_query = mysql_query ("SELECT util_cdn from utilisateur where (util_typutil_lb = 'RESPONSABLE_FORMATION' or util_typutil_lb = 'ADMINISTRATEUR') AND util_flag = 0");
    $nb_presc = mysql_num_rows ($presc_query);
    $param = $id_user;
 }
 else
    echo '<TD></TD>';
 if ($desc == "")
    $mesg_nom = "<B>$mess_desc : </B>".addslashes($description)."<BR><B>$mrc_aut : </B>$prenom_createur $nom_createur <BR><B>$mess_menu_gest_seq_ref :</B> $dtc<BR><B>$mess_modif_dt :</B> $dtm<BR>$mess_nbseq :<B> $nomb_seq</B>";
 else
    $mesg_nom = "<B>$msq_act_ref</B> $desc <BR><B>$mess_desc : </B>".str_replace('"'," ",$description)."<BR><B>$mrc_aut : </B>$prenom_createur $nom_createur  <BR><B>$mess_menu_gest_seq_ref :</B> $dtc<BR><B>$mess_modif_dt :</B> $dtm<BR>$mess_nbseq :<B> $nomb_seq</B>";
 if (isset($parc))
 {
    echo "<TD align='left' valign='top'><a href=\"javascript:void(0);\" target='main'  ".bulle($mesg_nom,"","LEFT","ABOVE",320)."&nbsp;";
    echo "$nom_parcours</TD valign='top'><TD align=left valign='top'><B>$duree_parc</B></TD>";
 }
 if (!isset($parc))
    echo "<tr height='40'><td valign='top'><b>$msq_formateur</b></td>";
 echo "<TD nowrap  valign='top'>";
 $form_query = mysql_query ("SELECT util_cdn from utilisateur where (util_typutil_lb = 'FORMATEUR_REFERENT' or util_typutil_lb = 'RESPONSABLE_FORMATION' or util_typutil_lb = 'ADMINISTRATEUR') AND util_flag = 0");
 $nb_form = mysql_num_rows ($form_query);
 $param = $form_ref;
 if ($nb_form == 0)
    echo $mess_noform_ref;
 elseif(isset($parc))
    Ascenseur_mult ("form_ref","SELECT util_cdn,util_nom_lb,util_prenom_lb from utilisateur where (util_typutil_lb = 'FORMATEUR_REFERENT' or util_typutil_lb = 'RESPONSABLE_FORMATION' or util_typutil_lb = 'ADMINISTRATEUR') AND util_flag = 0 order by util_nom_lb ASC",$connect,$param);
 elseif(!isset($parc))
    echo '<input type="text" name="appel_form" id="appel_form" value= "'.$mesg_listform.'" onClick="javascript:var changer=getElementById(\'appel_form\');changer.value=\'\';"/>';

 echo "</TD>";

 if (!isset($parc))
 {
    echo "</tr>";
    echo "<tr height='40'><TD><b>$mess_gp_date_deb</b></TD><TD nowrap valign='top' align='left'><INPUT TYPE= TEXT class='INPUT_AC' NAME='date_deb' id='ddtp1000' value= '$datedujour' MAXLENGTH='10' size='10' title=\"$cal_click\" onfocus=\"this.select();lcs(this)\" onclick=\"event.cancelBubble=true;this.select();lcs(this)\" onChange=\"var ddep1000=document.getElementById('ddtp1000');validateDate(ddep1000,'$datedujour','ddtp1000');\"></TD></tr>";
    echo "<tr height='40'><TD><b>$mess_gp_date_fin</b></TD><TD nowrap valign='top' align='left'><INPUT TYPE= TEXT class='INPUT_AC' NAME='date_fin' id='dfnp1000' value= '' MAXLENGTH='10' size='10' title=\"$cal_click\" onfocus=\"this.select();lcs(this)\" onclick=\"event.cancelBubble=true;this.select();lcs(this)\" onChange=\"var dffp1000=document.getElementById('dfnp1000');validateDate(dffp1000,'','dfnp1000');\"></TD></tr>";
 }else
 {
    if (strstr($_SERVER['REQUEST_URI'],'modif_gp.php'))
       echo "<input type='hidden' name='groupe' value='$id_grp'>";

    echo "<TD nowrap valign='top' align='left'><INPUT TYPE= TEXT class='INPUT_AC' NAME='date_deb' id='ddtp$parc' value= '$datedujour' MAXLENGTH='10' size='10' title=\"$cal_click\" onfocus=\"this.select();lcs(this)\" onclick=\"event.cancelBubble=true;this.select();lcs(this)\" onChange=\"var ddep$parc=document.getElementById('ddtp$id_parc');validateDate(ddep$parc,'$datedujour','ddtp$parc');\"></TD>";
    echo "<TD nowrap valign='top' align='left'><INPUT TYPE= TEXT class='INPUT_AC' NAME='date_fin' id='dfnp$parc' value= '' MAXLENGTH='10' size='10' title=\"$cal_click\" onfocus=\"this.select();lcs(this)\" onclick=\"event.cancelBubble=true;this.select();lcs(this)\" onChange=\"var dffp$parc=document.getElementById('dfnp$parc');validateDate(dffp$parc,'','dfnp$parc');\"></TD>";
 }
 if (!isset($parc))
 {
    echo "<tr height='40'><TD></TD>";
    echo "<TD align='left' valign='top'><A HREF=\"javascript:checkForm(document.MForm);\" onmouseover=\"img1.src='../images/fiche_identite/boutvalidb.gif';return true;\" onmouseout=\"img1.src='../images/fiche_identite/boutvalid.gif'\">".
         "<IMG NAME=\"img1\" SRC=\"../images/fiche_identite/boutvalid.gif\" BORDER='0' onLoad=\"tempImg=new Image(0,0); tempImg.src='../images/fiche_identite/boutvalidb.gif'\"></A></TD></FORM></TR>";
    echo "<tr height='60'><TD colspan='2'></TD></tr></table></TD></TR></table>";

 }
 else
    echo "<TD align='left' valign='top'><A HREF=\"javascript:checkFormulaire(document.MForm);\" onmouseover=\"img1.src='images/fiche_identite/boutvalidb.gif';return true;\" onmouseout=\"img1.src='images/fiche_identite/boutvalid.gif'\">".
         "<IMG NAME=\"img1\" SRC=\"images/fiche_identite/boutvalid.gif\" BORDER='0' onLoad=\"tempImg=new Image(0,0); tempImg.src='images/fiche_identite/boutvalidb.gif'\"></A></TD></FORM></TR>";
 // if (!isset($parc))
// echo "<TR height='5'><TD colspan='10' height='5'><HR size=1 color='#D45211' width='75%'></TD>";
 if (isset($parc) && (!isset($ajout_parc) || (isset($ajout_parc) && $ajout_parc == 0)))
 {
    $Ext = "_$id_grp";
    //$id_grp = $numero_groupe;
    $grp_parc = mysql_result(mysql_query("select count(*) from utilisateur_groupe where utilgr_groupe_no=$id_grp"),0);
    $champ_encours =  ($id_classe == 1 || $grp_parc == 0) ? "seqparc_seq_no" : "presc_seq_no";
    if ($id_classe == 1 || $grp_parc == 0)
           $seq_query_presc = mysql_query ("SELECT seqparc_seq_no FROM sequence_parcours WHERE ".
                                           "seqparc_parc_no = $parc ORDER BY seqparc_ordre_no asc");
    elseif ($id_classe == 0 && $grp_parc > 0)
           $seq_query_presc = mysql_query ("SELECT DISTINCT prescription$Ext.presc_seq_no,sequence_parcours.seqparc_ordre_no FROM
                                            sequence_parcours, prescription$Ext WHERE
                                            seqparc_parc_no = '$parc' AND presc_seq_no = seqparc_seq_no AND
                                            presc_parc_no = sequence_parcours.seqparc_parc_no ORDER BY seqparc_ordre_no ASC");
    $nb_seq_presc = mysql_num_rows ($seq_query_presc);
    echo "<TR bgcolor='#EFEFEF'><TD>&nbsp;</TD>";
    echo "<TD colspan='3' align='left'><b>$mess_gp_seq_parc</b></TD>";
    echo "<TD height='20' align='center' width='70'><b>$mess_gp_duree</b></TD></TR>";
    $i = 0;
    $num_seq = array();
    $ordre_s = array();
    while ($i != $nb_seq_presc)
    {
      $duree = 0;
      $seq = mysql_result ($seq_query_presc,$i,$champ_encours);
      $comb = GetDataField ($connect,"select seq_type_on from sequence where
                                      seq_cdn = $seq","seq_type_on");
      if ($comb == 1)
      {
         $i++;
         continue;
      }
      $num_seq[$i] = $seq;
      if ($id_classe == 0)
      {
         $ordre_seq = GetDataField ($connect,"select seqparc_ordre_no from sequence_parcours where
                                             seqparc_seq_no = $seq and
                                             seqparc_parc_no = $parc","seqparc_ordre_no");
         $ordre_s[$i] = $ordre_seq;
         if ($i > 0)
         {
            $seq_precedent = $num_seq[$i-1];
            $ordre_precedent = $ordre_s[$i-1];
         }
      }
      $j++;
      $duree_seq = GetDataField ($connect,"select seq_duree_nb from sequence where seq_cdn=$seq","seq_duree_nb");
      $nom_seq = GetDataField ($connect,"select seq_titre_lb from sequence where seq_cdn=$seq","seq_titre_lb");
      $type_seq = GetDataField ($connect,"select seq_type_lb from sequence where seq_cdn=$seq","seq_type_lb");
    //***************************************************************************************************
      echo couleur_tr($j-1,'');
      if ($signal[$seq] == 1)
         $nom_seq .=" *";
      if (strstr($type_seq,"SCORM"))
         $logo_sco = "<TD><IMG SRC='images/gest_parc/scorm.gif' border='0' alt=\"$mess_seq_sco\"></TD>";
      else
         $logo_sco = "<TD>- </TD>";
      echo  "$logo_sco<TD colspan=3><B><FONT size='1'>$nom_seq</FONT></B></TD>";
      $laduree = ($duree_seq == 0) ? "" : duree_calc($duree_seq);
      echo "<TD align='center' nowrap width='70'><FONT size='1'>$laduree</FONT></td>";
      echo "</TR>";
      $i++;
    }
}
?>
