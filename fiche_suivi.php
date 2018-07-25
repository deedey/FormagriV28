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
//de quel type est l'utilisateur (apprenant, formateur, administrateur)
$nom_user = $_SESSION["name_user"];
$prenom_user = $_SESSION["prename_user"];
$email=$_SESSION["email_user"];
if (isset($lien_details_parc))
   $lien_details_parc= str_replace("|","&",$lien_details_parc);
if (isset($lien_details_parc) && strstr($lien_details_parc,"details_parc.php"))
  $_SESSION['lien_details_parc'] = $lien_details_parc;
include ("style.inc.php");
if ($typ_user == "APPRENANT")
  $id_app = $id_user;
else
  $id_app = $utilisateur;
$Ext = '_'.$numero_groupe;
$nom_app = GetDataField ($connect,"select util_nom_lb from utilisateur where util_cdn = $id_app","util_nom_lb");
$prenom_app = GetDataField ($connect,"select util_prenom_lb from utilisateur where util_cdn = $id_app","util_prenom_lb");
if (isset($supprimer) && $supprimer == 1)
{
  $requete = mysql_query("delete from fiche_suivi where fiche_cdn = '$num'");
  $consultation = 1;
}
if (isset($consultation) && $consultation == 1)
{
  $titre_seq = GetDataField ($connect,"select seq_titre_lb from sequence where seq_cdn=$id_seq","seq_titre_lb");
  $titre_parc = GetDataField ($connect,"select parcours_nom_lb from parcours where parcours_cdn=$id_parc","parcours_nom_lb");
  $type_seq = GetDataField ($connect,"select seq_type_lb from sequence where seq_cdn=$id_seq","seq_type_lb");
  entete_simple($mess_fiche_suivi);
  echo "<TR><TD class='sous_titre'><Font size='2'>$msq_seq : $titre_seq<br />$msq_parc : $titre_parc";
  if ($typ_user != "APPRENANT")
     echo "<br />$msq_apprenant : $prenom_app $nom_app";
  echo "</FONT></TD></TR>";
  echo "<TR><TD colspan='2' width='100%'><TABLE width='100%'>";
  if (isset($mess_ajoute) && $mess_ajoute != "")
     echo "<TR><TD align='left' valign='center'><Font size='2'><B>".stripslashes($mess_ajoute)."</B></FONT></TD></TR>";
  $mess_ajoute = "";
  if (isset($utilisateur) && $utilisateur > 0)
  {
    $lien = "fiche_suivi.php?utilisateur=$utilisateur&ajouter=1&objet=observation&id_seq=$id_seq&id_parc=$id_parc&numero_groupe=$numero_groupe";
    $lien = urlencode($lien);
    echo "<TR><TD valign='center'><DIV id='sequence'>&nbsp;<BR><A HREF=\"trace.php?link=$lien\" >$mess_ajt_obs</A><P>";
    echo "</DIV></TD></TR>";
  }
  $req_fiche_tot = mysql_query("SELECT count(fiche_seq_no) FROM fiche_suivi where fiche_utilisateur_no = $id_app AND fiche_seq_no = $id_seq");
  $nb_fiche_tot = mysql_result($req_fiche_tot,0);
  if ($nb_fiche_tot > 0)
     echo "<TR><TD><B>$mess_menu_consult_favori</B><UL><DIV id='sequence'>";
  else
     echo "<TR><TD><B>$mess_menu_consult_favori</B><UL>";
  $lien="fiche_suivi.php?utilisateur=$utilisateur&consulter=1&objet=prescription&event=1&id_seq=$id_seq&id_parc=$id_parc&numero_groupe=$numero_groupe&ordre=asc";
  $lien = urlencode($lien);
  $req_fiche = mysql_query("SELECT count(fiche_cdn) FROM fiche_suivi where (fiche_typaction_lb =\"Gestion des prescriptions\" OR fiche_typaction_lb =\"prescription\" OR fiche_typaction_lb =\"Affectation à une formation\") AND fiche_act_no = 0 AND fiche_utilisateur_no = $id_app AND fiche_seq_no = $id_seq");
  $nb_fiche = mysql_result($req_fiche,0);
  if ($nb_fiche > 0)
     echo "<LI type=\"square\"><A HREF=\"trace.php?link=$lien\">$mess_cons_seq</A></LI>";
  else
     echo "<LI type=\"square\"><B>$mess_cons_seq</B></LI>";
  $lien="fiche_suivi.php?utilisateur=$utilisateur&consulter=1&objet=suivi&event=1&id_seq=$id_seq&id_parc=$id_parc&numero_groupe=$numero_groupe&ordre=asc";
  $lien = urlencode($lien);
  $req_fiche = mysql_query("SELECT count(fiche_cdn) FROM fiche_suivi where fiche_typaction_lb =\"suivi pedagogique\" AND fiche_utilisateur_no = $id_app AND fiche_seq_no = $id_seq AND fiche_act_no > 0");
  $nb_fiche = mysql_result($req_fiche,0);
  if ($nb_fiche > 0)
     echo "<LI type=\"square\"><A HREF=\"trace.php?link=$lien\">$mess_fiche_actseq</A></LI>";
  else
     echo "<LI type=\"square\"><B>$mess_fiche_actseq</B></LI>";
  $lien="fiche_suivi.php?utilisateur=$utilisateur&consulter=1&objet=tout&event=1&id_seq=$id_seq&id_parc=$id_parc&numero_groupe=$numero_groupe&ordre=asc";
  $lien = urlencode($lien);
  $req_fiche = mysql_query("SELECT count(fiche_cdn) FROM fiche_suivi where fiche_typaction_lb !=\"\" AND fiche_utilisateur_no = $id_app AND fiche_seq_no = $id_seq");
  $nb_fiche = mysql_result($req_fiche,0);
  if ($nb_fiche > 0)
     echo "<LI type=\"square\"><A HREF=\"trace.php?link=$lien\">$mess_fiche_tout</A><P></LI>";
  else
     echo "<LI type=\"square\"><B>$mess_fiche_tout</B></LI>";
  $lien="fiche_suivi.php?utilisateur=$utilisateur&consulter=1&objet=observation&event=2&id_parc=$id_parc&numero_groupe=$numero_groupe&id_seq=$id_seq";
  $lien = urlencode($lien);
  $req_fiche = mysql_query("SELECT count(fiche_cdn) FROM fiche_suivi where (fiche_autraction_lb =\"observation\" OR fiche_autraction_lb =\"appreciation\") AND fiche_utilisateur_no = $id_app AND fiche_seq_no = $id_seq");
  $nb_fiche = mysql_result($req_fiche,0);
  if ($nb_fiche > 0)
     echo "<LI type=\"square\"><A HREF=\"trace.php?link=$lien\">$mess_fiche_liste_obs</A></LI>";
  else
     echo "<LI type=\"square\"><B>$mess_fiche_liste_obs</B></LI>";
  if ($nb_fiche_tot > 0)
    echo "</DIV></UL><P></TD></TR>";
  else
    echo "</UL><P></TD></TR>";
  echo "</TABLE></TD></TR>";

  echo "</TABLE></TD></TR></TABLE>";
  exit;
}
if (isset($consulter) && $consulter == 1)
{
  if (isset($id_seq) && $id_seq > 0)
  {
     $titre_seq = GetDataField ($connect,"select seq_titre_lb from sequence where seq_cdn=$id_seq","seq_titre_lb");
     $type_seq = GetDataField ($connect,"select seq_type_lb from sequence where seq_cdn=$id_seq","seq_type_lb");
     $titre_parc = GetDataField ($connect,"select parcours_nom_lb from parcours where parcours_cdn=$id_parc","parcours_nom_lb");
  }
  if (isset($numero_groupe) && $numero_groupe > 0)
     $nom_grp = GetDataField ($connect,"select grp_nom_lb from groupe where grp_cdn=$numero_groupe","grp_nom_lb");

  $afficher1 = "<center><TABLE bgColor='#298CA0' cellspacing='2' width='100%'><TR><TD width='100%'>";
  $afficher1 .= "<TABLE bgColor='#FFFFFF' cellspacing='1' width='100%'>";
  $afficher2 .= "<TR><TD background=\"images/fond_titre_table.jpg\" colspan='2' height='40' align='center' valign='center'><Font size='3' color='#FFFFFF'><B>$mess_fiche_suivi";
  $afficher = "<tr><td class='sous_titre' style=\"margin:3px;\">";
  if (isset($id_grp) && $id_grp > 0 && ((isset($objet) && $objet != "tutorat") || !isset($objet)))
  {
     $nom_grp = GetDataField ($connect,"select grp_nom_lb from groupe where grp_cdn  = $id_grp","grp_nom_lb");
     $afficher .= "<Font size='2'><strong>$mess_formation : $nom_grp</FONT><br />";
  }
  if (isset($id_seq) && $id_seq > 0)
     $afficher .= "<Font size='2'><b>$msq_seq : $titre_seq</b><br />$msq_parc : $titre_parc</FONT>";
  if (isset($numero_groupe) && $numero_groupe > 0)
     $afficher .= "<br /><Font size='2'>$mess_formation : $nom_grp</FONT>";
  if ($typ_user != "APPRENANT")
     $afficher .= "<br /><Font size='2'>$msq_apprenant : $prenom_app $nom_app</FONT>";
  $afficher .= "<TR><TD colspan='2' width='100%'><TABLE width='100%'>";
  if (isset($event) && $event == 1)
  {
    if (isset($objet) && $objet == "prescription")
    {
      $discriminant = "(fiche_typaction_lb = 'Gestion des prescriptions' OR fiche_typaction_lb = 'prescription' OR fiche_typaction_lb = 'Affectation à une formation')";
      $disc = " : $gest_presc";
    }elseif (isset($objet) && $objet == "suivi")
    {
      $discriminant = "fiche_typaction_lb = 'suivi pedagogique'";
      $disc = " : $mess_gp_tit_suiv_ped";
    }elseif (isset($objet) && $objet == "tutorat")
    {
      $discriminant = "fiche_typaction_lb = 'Tutorat'";
      $disc = " : $mess_tutorat";
    }elseif (isset($objet) && $objet == "tout")
      $discriminant = "fiche_typaction_lb != ''";
  }elseif (isset($event) && $event == 2)
  {
    if (isset($objet) && ($objet == "observation" || $objet == "appreciation")){
      $discriminant = "fiche_autraction_lb = 'observation' OR fiche_autraction_lb = 'appreciation'";
      $disc = " : $mess_obs_fs";
    }
  }
  echo "$afficher1\n$afficher2 $disc</TD></TR>";
  echo $afficher."</td></tr>";
  if ($ordre == "desc")
  {
    $ordre = "asc";
    $lien_image = "<IMG SRC=\"images/fleche0.gif\" border=0>";
  }
  elseif (!isset($ordre) || $ordre == "asc")
  {
    $ordre = "desc";
    $lien_image = "<IMG SRC=\"images/fleche1.gif\" border=0>";
  }
  $lien_date = "fiche_suivi.php?".getenv("QUERY_STRING")."&ordre=$ordre";
  $lien_date = urlencode($lien_date);
  echo "<TR height='25'>";
  echo "<TD align='left' width='8%' nowrap class='barre_titre'>&nbsp;&nbsp;$mess_mess_date";
  echo "&nbsp;&nbsp;<A href=\"trace.php?link=$lien_date\">$lien_image</A>";
  echo "&nbsp;&nbsp;</TD>";
  echo "<TD align='left' width='8%' nowrap class='barre_titre'>&nbsp;&nbsp;$heur&nbsp;&nbsp;</TD>";
  echo "<TD align='left' width='60%' nowrap class='barre_titre'>&nbsp;&nbsp;$mess_fiche_action&nbsp;&nbsp;</TD>";
//  echo "<TD align='left' width='8%' nowrap class='barre_titre'>&nbsp;&nbsp;$mrc_aut&nbsp;&nbsp;</TD>";
//  echo "<TD align='left' width='8%' nowrap class='barre_titre'>&nbsp;&nbsp;$mess_qualite&nbsp;&nbsp;</TD>";
//  echo "<TD align='left' width='8%' nowrap><b><FONT COLOR=white>&nbsp;&nbsp;$mess_typaction&nbsp;&nbsp;</FONT></b></TD>";
  if ($typ_user != "APPRENANT" && isset($event) && $event == 2)
  {
    echo "<TD align='left' width='4%' class='barre_titre'>$mess_modif_base</TD>";
    echo "<TD align='left' width='4%' class='barre_titre'>$mess_ag_supp</TD>";
  }
  echo "</TR>";
  if (isset($vientde) && $vientde == "annonce")
     $champ_ajout = "AND (fiche_date_dt > \"$date_der\" OR fiche_date_dt = \"$date_der\") AND fiche_auteur_no != $id_user";
  else
     $champ_ajout = "";
  if (isset($id_seq) && $id_seq > 0)
     $req_f = "SELECT * FROM fiche_suivi WHERE fiche_seq_no = $id_seq AND fiche_utilisateur_no = $id_app AND $discriminant $champ_ajout order by fiche_date_dt $ordre,fiche_heure_dt $ordre";
  elseif (isset($tuteur_vue) && $tuteur_vue == 1 && strstr($discriminant,"Tutorat"))
     $req_f = "SELECT * FROM fiche_suivi WHERE fiche_utilisateur_no = $id_app AND $discriminant $champ_ajout order by fiche_date_dt $ordre,fiche_heure_dt $ordre";
  elseif (isset($tuteur_vue) && $tuteur_vue == 1)
     $req_f = "SELECT * FROM fiche_suivi WHERE fiche_utilisateur_no = $id_app AND fiche_grp_no = $id_grp AND $discriminant $champ_ajout order by fiche_date_dt $ordre,fiche_heure_dt $ordre";
  $req_fiche = mysql_query($req_f);
  $nb_fiche = mysql_num_rows($req_fiche);
  $i = 0;
  while ($i < $nb_fiche)
  {
     $titre_act = "";
     $seq_fiche=0;
     $contenu_titre="";
     $num_fiche = mysql_result($req_fiche,$i,"fiche_cdn");
     $date_fiche1 = mysql_result($req_fiche,$i,"fiche_date_dt");
     $heure_fiche = mysql_result($req_fiche,$i,"fiche_heure_dt");
     $auteur_fiche = mysql_result($req_fiche,$i,"fiche_auteur_no");
     $grp_fiche = mysql_result($req_fiche,$i,"fiche_grp_no");
     $parc_fiche = mysql_result($req_fiche,$i,"fiche_parc_no");
     $seq_fiche = mysql_result($req_fiche,$i,"fiche_seq_no");
     $act_fiche = mysql_result($req_fiche,$i,"fiche_act_no");
     $qualite_fiche = mysql_result($req_fiche,$i,"fiche_qualite_lb");
     $contenu_fiche = mysql_result($req_fiche,$i,"fiche_commentaire_cmt");
     $type_seq = GetDataField ($connect,"select seq_type_lb from sequence where seq_cdn=$seq_fiche","seq_type_lb");
     if (isset($event) && $event == 1)
     {
       $fiche_action = mysql_result($req_fiche,$i,"fiche_typaction_lb");
       if ($fiche_action == "suivi pedagogique" && $act_fiche > 0)
       {
          if ($type_seq == 'NORMAL')
             $titre_act = GetDataField ($connect,"select act_nom_lb from activite where act_cdn=$act_fiche","act_nom_lb");
          else
             $titre_act = GetDataField ($connect,"select mod_titre_lb from scorm_module where mod_cdn=$act_fiche","mod_titre_lb");
       }
     }
     else
       $fiche_action = mysql_result($req_fiche,$i,"fiche_autraction_lb");
     $ch_date_fic = explode ("-",$date_fiche1);
     $date_fiche = "$ch_date_fic[2]-$ch_date_fic[1]-$ch_date_fic[0]";
     $nom_aut = GetDataField ($connect,"select util_nom_lb from utilisateur where util_cdn=$auteur_fiche","util_nom_lb");
     $prenom_aut = GetDataField ($connect,"select util_prenom_lb from utilisateur where util_cdn=$auteur_fiche","util_prenom_lb");
     if (isset($tuteur_vue) && $tuteur_vue == 1 && ($date_fiche1 > $date_der || $date_fiche1 == $date_der))
        $ajt = "<img src='forum/images/flag.gif' style=\"width:7px;height:12px;\" border='0'>";
     else
        $ajt = "";
     echo couleur_tr($i+1,20);
     echo "<TD align='left' valign='top' nowrap>$ajt $date_fiche&nbsp;&nbsp;</TD>";
     echo "<TD align='left' valign='top' nowrap>&nbsp;&nbsp;$heure_fiche&nbsp;&nbsp;</TD>";
     if (!isset($id_seq) && $seq_fiche > 0){
       $nom_parc = GetDataField ($connect,"select parcours_nom_lb from parcours where parcours_cdn=$parc_fiche","parcours_nom_lb");
       $nom_seq = GetDataField ($connect,"select seq_titre_lb from sequence where seq_cdn=$seq_fiche","seq_titre_lb");
       $contenu_titre = "<B>$msq_parc : $nom_parc\n$msq_seq : $nom_seq</B>";
       if (isset($titre_act) && $titre_act != "")
          $contenu_titre .="\n<B>$msq_activite : $titre_act</B>";
       $contenu_fiche = "$contenu_titre<br />$contenu_fiche";
     }elseif(isset($id_seq) && $seq_fiche > 0 && $titre_act != ""){
       $contenu_titre .="<B>$msq_activite : $titre_act</B>";
       $contenu_fiche = "$contenu_titre<br />$contenu_fiche";
     }
     echo "<TD align='left' valign='top'>".str_replace("\n","<BR>",$contenu_fiche);
     echo "&nbsp;&nbsp;</TD>";
//     if ($qualite_fiche != "Apprenant" )
//     {
//        echo "<TD align='left' valign='top'>&nbsp;$prenom_aut $nom_aut&nbsp;</TD>";
//        echo "<TD align='left' valign='top'>&nbsp;&nbsp;$qualite_fiche&nbsp;&nbsp;</TD>";
//     }else
//       echo "<TD align='left' valign='top'>&nbsp;</TD><TD align='left' valign='top'>&nbsp;</TD>";
//     echo "<TD align='left' valign='top'>&nbsp;&nbsp;&nbsp;$fiche_action&nbsp;</TD>";
     if ($typ_user != "APPRENANT" && $auteur_fiche == $id_user && $event == 2)
     {
        $lien = "fiche_suivi.php?utilisateur=$utilisateur&modifier=1&objet=$objet&id_seq=$id_seq&num=$num_fiche&id_parc=$id_parc&numero_groupe=$numero_groupe&event=$event";
        $lien = urlencode($lien);
        echo "<TD align='center' valign='top' width='4%' bgcolor = '#F9F9F9'>&nbsp;&nbsp;".
             "<A HREF=\"trace.php?link=$lien\"><IMG SRC=\"images/repertoire/icoGrenomfich.gif\" height=\"20\" width=\"20\" border='0'></A>&nbsp;&nbsp;</TD>";
        if ($nb_fiche > 1)
          $lien = "fiche_suivi.php?".getenv("QUERY_STRING")."&ordre=$ordre&supprimer=1&num=$num_fiche";
        elseif ($nb_fiche == 1)
          $lien = "fiche_suivi.php?consultation=1&utilisateur=$utilisateur&id_seq=$id_seq&supprimer=1&id_parc=$id_parc&numero_groupe=$numero_groupe&num=$num_fiche";
        $lien = urlencode($lien);
        echo "<TD align='center' valign='top' width='4%' bgcolor = '#F9F9F9'>&nbsp;&nbsp;<A href=\"javascript:void(0);\" ".
             "onclick=\"javascript:return(confm('trace.php?link=$lien'));\">".
             "<IMG SRC=\"images/messagerie/icopoubelressour.gif\" border='0'></A>&nbsp;&nbsp;</TD>";
     }
     echo "</TR>";
    $i++;
  }
  echo "</TABLE>";
  if (!isset($tuteur_vue))
      echo "<TR><TD align=left align='left' valign='center'>&nbsp;&nbsp;&nbsp;&nbsp;<A HREF=\"javascript:history.back();\" ".
           "onmouseover=\"img_annonce.src='images/fiche_identite/boutretourb.gif';return true;\" ".
           "onmouseout=\"img_annonce.src='images/fiche_identite/boutretour.gif'\">".
           "<IMG NAME=\"img_annonce\" SRC=\"images/fiche_identite/boutretour.gif\" BORDER='0' ".
           "onLoad=\"tempImg=new Image(0,0); tempImg.src='images/fiche_identite/boutretourb.gif'\"></A></TD></TR>";
  echo "</TABLE></TD></TR></TABLE>";
  exit;
}

if (((isset($ajouter) && $ajouter == 1)|| (isset($modifier) && $modifier == 1)) && isset($inserer) && $inserer == 1)
{
  if (isset($ajouter) && $ajouter == 1)
  {
   $date_op = date("Y-m-d H:i:s");
   $heure_fiche = substr($date_op,11);
   $date_fiche = substr($date_op,0,10);
   $tuteur_query = mysql_query("select tut_tuteur_no from tuteur where tut_apprenant_no = $utilisateur");
   $Nb_Tut = mysql_num_rows($tuteur_query);
   $t_t=0;
   if ($Nb_Tut > 0)
   {
     while ($t_t < $Nb_Tut)
     {
      $numero_tut = mysql_result($tuteur_query,$t_t,"tut_tuteur_no");
      if ($numero_tut == $id_user)
      {
        $tuteurbon = 1;
        break;
      }
      $t_t++;
     }
   }
   $reqPresc = "select * from prescription$Ext where presc_seq_no = $id_seq and presc_parc_no =$id_parc and presc_utilisateur_no = $utilisateur";
   $PrescReq = mysql_query($reqPresc);
   if (mysql_num_rows($PrescReq) > 0)
      $oReqPresc = mysql_fetch_object($PrescReq);
   $formateur =  $oReqPresc->presc_formateur_no;
   $prescripteur = $oReqPresc->presc_prescripteur_no;
   $inscripteur = GetDataField ($connect,"select util_auteur_no from utilisateur where util_cdn='$utilisateur'","util_auteur_no");
   if ($formateur == $id_user)
     $qualite = $msq_formateur;
   if ($prescripteur == $inscripteur)
      $qualite = $msq_prescripteur;
   elseif ($inscripteur == $id_user)
      $qualite = $mess_inscripteur;
   elseif ($prescripteur != $id_user && $inscripteur != $id_user && $formateur != $id_user && $typ_user == "ADMINISTRATEUR")
      $qualite = $mess_typ_adm;
   elseif ($tuteurbon == 1)
     $qualite = $mess_typ_tut;
   else
     $qualite = "Inconnu";
   $titre_seq = GetDataField ($connect,"select seq_titre_lb from sequence where seq_cdn=$id_seq","seq_titre_lb");
   $action_fiche = $objet;
   $id_grp = $numero_groupe;
   $new_fiche = Donne_ID ($connect,"select max(fiche_cdn) from fiche_suivi");
   $req_fiche = mysql_query("INSERT INTO fiche_suivi (fiche_cdn,fiche_utilisateur_no,fiche_auteur_no,fiche_qualite_lb,fiche_date_dt,fiche_heure_dt,fiche_commentaire_cmt,fiche_grp_no,fiche_parc_no,fiche_seq_no,fiche_act_no,fiche_autraction_lb) VALUES($new_fiche,$utilisateur,$id_user,'$qualite','$date_fiche','$heure_fiche',\"$commentaire\",$id_grp,$id_parc,$id_seq,0,\"$action_fiche\")");
  }elseif ($modifier == 1)
     $req_fiche = str_replace("\n","<BR>",mysql_query("UPDATE fiche_suivi set fiche_commentaire_cmt = \"$commentaire\" WHERE fiche_cdn = $num"));
   if ($ajouter == 1){
      $vers = "consultation";
   }else{
      $vers = "consulter";
   }
   $lien = "fiche_suivi.php?utilisateur=$utilisateur&id_seq=$id_seq&$vers=1&objet=$objet&id_parc=$id_parc&numero_groupe=$numero_groupe&event=$event";
   $lien = urlencode($lien);
   echo "<script language='JavaScript'>";
      echo "document.location.replace(\"trace.php?link=$lien\");";
   echo "</script>";
   exit();
}
elseif (((isset($ajouter) && $ajouter == 1)|| (isset($modifier) && $modifier == 1)) &&
        (!isset($inserer) ||(isset($inserer) && $inserer != 1)))
{
  ?>
  <SCRIPT language=JavaScript>
    function checkForm(frm) {
      var ErrMsg = "<?php echo $mess_info_no;?>\n";
      var lenInit = ErrMsg.length;
      if (isEmpty(frm.commentaire)==true)
        ErrMsg += ' - <?php echo $mess_insert_nt;?>\n';
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
  if (isset($objet) && ($objet == "observation" || $objet == "appreciation"))
  {
     $titre_ajout = $mess_ajt_obs;
     $affiche = $mess_insert_nt;
  }
  entete_simple($titre_ajout);
  echo "<TR><TD colspan='2' width='100%'><TABLE border='0' width='100%'>";
  $titre_seq = GetDataField ($connect,"select seq_titre_lb from sequence where seq_cdn=$id_seq","seq_titre_lb");
  if (isset($modifier) && $modifier == 1)
     $commentaire = str_replace("<BR>","\n",GetDataField ($connect,"select fiche_commentaire_cmt from fiche_suivi where fiche_cdn =$num","fiche_commentaire_cmt"));
  echo "<TR><TD bgcolor='#FFFFFF' colspan='2'height='40' valign='center' width='100%'><Font size='2'>&nbsp;&nbsp;$msq_seq :  $titre_seq</FONT></TD></TR>";
  echo "<FORM NAME='form1' action=\"fiche_suivi.php?numero_groupe=$numero_groupe&utilisateur=$utilisateur&ajouter=$ajouter&modifier=$modifier&inserer=1&objet=$objet&id_seq=$id_seq&event=$event&num=$num&id_parc=$id_parc\" method='post'>";

  echo "<TR height='20'><TD colspan=2>&nbsp;</TD></TR>";
  echo "<TR><TD nowrap colspan=2>&nbsp;<b>$affiche &nbsp;&nbsp;&nbsp;</b></TD></TR>";
  echo "<TR><TD nowrap colspan=2><TEXTAREA  name='commentaire' rows='8' cols='60' align='middle'>$commentaire</TEXTAREA></TD></TR>";
  echo "<TR height='60'><TD align=left align='left' valign='center'>&nbsp;&nbsp;&nbsp;&nbsp;<A HREF=\"javascript:history.back();\" ".
       "onmouseover=\"img_annonce.src='images/fiche_identite/boutretourb.gif';return true;\" ".
       "onmouseout=\"img_annonce.src='images/fiche_identite/boutretour.gif'\">".
       "<IMG NAME=\"img_annonce\" SRC=\"images/fiche_identite/boutretour.gif\" BORDER='0' ".
       "onLoad=\"tempImg=new Image(0,0); tempImg.src='images/fiche_identite/boutretourb.gif'\"></A>";
  echo "</TD><TD align='center' valign='center'><A HREF=\"javascript:checkForm(document.form1);\"  ".
       "onClick=\"TinyMCE.prototype.triggerSave();\" ".
       "onmouseover=\"img1.src='images/fiche_identite/boutvalidb.gif';return true;\" ".
       "onmouseout=\"img1.src='images/fiche_identite/boutvalid.gif'\">".
       "<IMG NAME=\"img1\" SRC=\"images/fiche_identite/boutvalid.gif\" BORDER='0' onLoad=\"tempImg=new Image(0,0); tempImg.src='images/fiche_identite/boutvalidb.gif'\"></A>";
  echo "</TABLE></TD></TR></TABLE></TD></TR></TABLE></center></body></html>";
 exit;
}
?>