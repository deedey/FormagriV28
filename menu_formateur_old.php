<?php
if (!isset($_SESSION)) session_start();
if (!isset($_SESSION['id_user']) || $_SESSION['id_user'] == "")
{
  exit();
}
require "admin.inc.php";
require "fonction.inc.php";
require "class/ClassMenu.php";
include ('include/UrlParam2PhpVar.inc.php');
require "lang$lg.inc.php";
dbConnect();
session_unregister('typ_user');
session_unregister('requete_parc');
session_unregister('requete_seq');
session_unregister('requete_act');
if (isset($lemode_user) && $lemode_user == 'tout')
{
   session_unregister('mode_user');
   $mode_user = $lemode_user;
   session_register('mode_user');
}
elseif (isset($lemode_user) && $lemode_user == 'rien')
{
   session_unregister('mode_user');
   $mode_user = $lemode_user;
   session_register('mode_user');
}
if ((isset($mode_user) && $mode_user == 'tout') || !isset($mode_user))
   $typ_user=GetDataField ($connect,"select util_typutil_lb from utilisateur where util_cdn='$id_user'","util_typutil_lb");
elseif (isset($mode_user) && $mode_user == 'rien')
{
   if (isset($action) && $action == 'concevoir')
      $typ_user = "RESPONSABLE_FORMATION";
   elseif (isset($action) && $action == 'suivre')
      $typ_user = "FORMATEUR_REFERENT";
   elseif (isset($action) && $action == 'tutorer')
      $typ_user = "TUTEUR";
   else
      $typ_user=GetDataField ($connect,"select util_typutil_lb from utilisateur where util_login_lb='$login'","util_typutil_lb");
}
session_register('typ_user');
//de quel type est l'utilisateur (apprenant, formateur, administrateur)
$nom_user = GetDataField ($connect,"select util_nom_lb from utilisateur where util_cdn = $id_user","util_nom_lb");
$prenom_user = GetDataField ($connect,"select util_prenom_lb from utilisateur where util_cdn = $id_user","util_prenom_lb");
$email=GetDataField ($connect,"select util_email_lb from utilisateur where util_cdn = $id_user","util_email_lb");
$person = $nom_user;
if (empty($entantquetut)) $entantquetut='';
if (empty($entantqueform)) $entantqueform='';
if (empty($entantqueresp)) $entantqueresp='';
if (empty($entantquepresc)) $entantquepresc='';
if ($typ_user == 'TUTEUR' || (isset($entantquetut) && $entantquetut == 1) || (isset($action) && $action == 'tutorer'))
{
  $bandeau = "images/modules/tut_form/bandohovert1.gif";
  $decon = "images/modules/tut_form/boutdeconvert";
  $bando = "images/modules/tut_form/bandovert.jpg";
  $son_profil = "<IMG SRC=\"images/modules/tut_form/icoprotut.jpg\" border='0'>";
  $le_logo = "<IMG SRC=\"images/modules/tut_form/logovert.jpg\" border='0'>";
  $pour_ajout = "<TD valign='bottom'>&nbsp;&nbsp;<FONT SIZE='4' color='#333333'>$profil $mess_typ_tut</FONT><BR>&nbsp;</TD>";
}
elseif (($typ_user == 'FORMATEUR_REFERENT' && isset($action) && $action == 'suivre') || (isset($entantqueform) && $entantqueform == 1) || (isset($action) && $action == 'suivre'))
{
  $bandeau = "images/modules/tut_form/bandohobleu1.gif";
  $decon = "images/modules/tut_form/boutdeconbleu";
  $bando = "images/modules/tut_form/bandobleu.jpg";
  $son_profil = "<IMG SRC=\"images/modules/tut_form/icoproform.jpg\" border='0'>";
  $le_logo = "<IMG SRC=\"images/modules/tut_form/logob.jpg\" border='0'>";
  $pour_ajout = "<TD valign='bottom' nowrap>&nbsp;&nbsp;<FONT SIZE='4' color='#333333'>$profil $msq_formateur</FONT><BR>&nbsp;</TD>";
}
else
{
  $bandeau = "images/ecran-annonce/ongl01.gif";
  $decon = "images/modules/tut_form/boutdeconec";
  $bando = "images/menu/fond_logo_formagri.jpg";
  $le_logo = "<IMG SRC=\"images/logo_formagri.jpg\" border='0'>";
  $son_profil = "";
  $pour_ajout = "";
}
include ("include/stylemenu.inc.php");
echo "<TABLE height='40' border='0' cellspacing='0' cellpadding='0' width='100%'><TR><TD align='left' valign='middle'>";

//Links used to initiate the sub menus. Pass in the desired submenu index numbers (ie: 0, 1) -->

$lien_accueil1 = "annonce_formateur.php";
$lien_accueil1 = urlencode($lien_accueil1);
$lien_accueil = "menu_formateur.php";
$lien_accueil = urlencode($lien_accueil);
$lien_casier = "modif_rep_fic.php";
$lien_casier = urlencode($lien_casier);
$lien_agenda = "agenda.php?tut=1";
$lien_agenda = urlencode($lien_agenda);
$lien_ressources = "recherche.php?flg=1&acces=1";
$lien_ressources = urlencode($lien_ressources);
$lien_qcm = "menu_qcm.php";
$lien_qcm = urlencode($lien_qcm);
//$lien_biblio = "favoris.php?consulter=1&objet=toutes&toutes=1";
//$lien_biblio = urlencode($lien_biblio);
if (isset($action) && $action == "concevoir")
{
  $parametres = str_replace("|","&",$lien_params);
  $lien_modules = "parcours.php?$parametres";
  $lien_modules = urlencode($lien_modules);
  $lien ="sequence_entree.php?choix_ref=1";
  $lien = urlencode($lien);
  $parametreseq = str_replace("|","&",$lien_paramseq);
  $lien_seq = "sequence_entree.php?$parametreseq";
  $lien_seq = urlencode($lien_seq);
  $lien ="sequence_entree.php?choix_ref=1";
  $lien = urlencode($lien);
  $requete_act1 ="select * from activite where act_auteur_no = $id_user order by act_nom_lb";
  $act_question_miens_lib = mysql_query ($requete_act1);
  $requete_act2 = "select * from activite order by act_nom_lb";
  $act_question_miens = mysql_query ($requete_act2);
  $requete_act3 = "select * from activite where act_seq_no = 0 order by act_nom_lb";
  $act_question_tts_lib = mysql_query ($requete_act3);
  $requete_act4 = "select * from activite where act_seq_no > 0 order by act_nom_lb";
  $act_question_seq = mysql_query ("$requete_act4");
  $requete_act5 = "select * from activite where act_publique_on=0 order by act_nom_lb";
  $act_question_nodupli = mysql_query ($requete_act5);
  $nb_act1 = mysql_num_rows ($act_question_miens_lib);
  $nb_act2 = mysql_num_rows ($act_question_miens);
  $nb_act3 = mysql_num_rows ($act_question_tts_lib);
  $nb_act4 = mysql_num_rows ($act_question_seq);
  $nb_act5 = mysql_num_rows ($act_question_nodupli);
  if ($nb_act1 > 0)
     $lien_act="activite_free.php?lesseq=2&medor=1&miens=1&ordre_affiche=lenom&titre_act=$mess_liste_vos_act";
  elseif ($nb_act1 == 0 && $nb_act2 > 0)
     $lien_act="activite_free.php?lesseq=2&medor=1&ordre_affiche=lenom&titre_act=$mess_liste_vos_act_seq";
  elseif ($nb_act1 == 0 && $nb_act2 == 0 && $nb_act3 > 0)
     $lien_act="activite_free.php?lesseq=0&medor=1&ordre_affiche=lenom&titre_act=$mess_liste_act_autres";
  elseif ($nb_act1 == 0 && $nb_act2 == 0 && $nb_act3 == 0 &&  $nb_act4 > 0)
     $lien_act="activite_free.php?lesseq=1&medor=1&ordre_affiche=lenom&titre_act=$mess_liste_act_seq_autres";
  elseif ($nb_act1 == 0 && $nb_act2 == 0 && $nb_act3 == 0 &&  $nb_act4 == 0 && $nb_act5 > 0)
     $lien_act="activite_free.php?medor=1&lesseq=2&ordre_affiche=lenom&titre_act=$mess_liste_act_prive";
  elseif ($nb_act1 == 0 && $nb_act2 == 0 && $nb_act3 == 0 &&  $nb_act4 == 0 && $nb_act5 == 0)
     $lien_act="activite_free.php?creer=1&miens=1&lesseq=0";
  $lien_act = urlencode($lien_act);

}
$requete_grp = mysql_query ("select distinct prescription.presc_grp_no from prescription,groupe WHERE prescription.presc_formateur_no = '$id_user' AND prescription.presc_grp_no = groupe.grp_cdn order by groupe.grp_nom_lb");
$nomb_grp = mysql_num_rows($requete_grp);
if ($nomb_grp > 0)
{
//   $lien"{|||$mess_menu_suivi $mess_menu_suivi_hg }";
   $num_grp = 0;$ajout_grp='';
   while($num_grp < $nomb_grp)
   {
      $id_grp = mysql_result($requete_grp,$num_grp,"presc_grp_no");
      $nom_grp = GetDataField ($connect,"select grp_nom_lb from groupe where grp_cdn  = $id_grp","grp_nom_lb");
      $carac_grp = strlen($nom_grp);
      if ($carac_grp > 20)
         $nom_grp1 = substr($nom_grp,0,20)."..";
      else
         $nom_grp1 = $nom_grp;
      $lien_grp="annonce_suivi.php?groupe_affiche[$id_grp]=1";
      $lien_grp = urlencode($lien_grp);
      $ajout_grp .="&nbsp;&nbsp;<A href=\"trace.php?link=$lien_grp\" target=\"main\" class=\"bouton2\">&nbsp;&nbsp;$nom_grp1&nbsp;&nbsp;</A>";
      $num_grp++;
   }
}
$lien_referentiel = "referenciel.php";
$lien_referentiel = urlencode($lien_referentiel);
$lien_rf = "modif_rep_fic.php?formateurs=1";
$lien_rf = urlencode($lien_rf);
   // gère le choix via icone pour les modules
   $nb_requete_parc = mysql_result(mysql_query("select parcours_cdn from parcours where parcours_cdn != 0 AND parcours_auteur_no = $id_user"),0,"parcours_cdn");
   if ($nb_requete_parc == 0)
      $lien_params = "prem=1&liste=1&miens_parc=0&refer=2&ordre_affiche=lenom";
   $requete1_parc = "select * from parcours where parcours_cdn != 0 order by parcours_nom_lb";
   $parc_query1 = mysql_query ("$requete1_parc");
   $les_modules = mysql_num_rows ($parc_query1);
   $requete2_parc = "select * from parcours where parcours_cdn != 0 AND parcours_auteur_no = $id_user order by parcours_nom_lb";
   $parc_query2 = mysql_query ("$requete2_parc");
   $mes_modules = mysql_num_rows ($parc_query2);
   if ($mes_modules > 0)
      $lien_params = "prem=1&liste=1&miens_parc=1&id_ref_parc=0&ordre_affiche=lenom";
   elseif ($mes_modules == 0 && $les_modules > 0)
      $lien_params = "prem=1&liste=1&ordre_affiche=lenom&refer=2";
   elseif ($les_modules == 0)
      $lien_params = "liste=1&id_ref_parc=0&choix_ref=1&miens_parc=$miens_parc"; //"prem=1&liste=1&miens_parc=1&ordre_affiche=lenom";
   $nb_requete_parc = mysql_num_rows(mysql_query("select * from parcours where parcours_cdn != 0 AND parcours_auteur_no = $id_user"));
   if ($nb_requete_parc == 0)
      $lien_params = "liste=1&id_ref_parc=0&choix_ref=1&miens_parc=$miens_parc";//"prem=1&liste=1&miens_parc=0&refer=2&ordre_affiche=lenom";
   $lien1_concevoir ="parcours.php?$lien_params";
   $lien1_concevoir = urlencode($lien1_concevoir);
   $lien_envoi = str_replace ("&","|",$lien_params);
   $lien_retour_concevoir = "menu_formateur.php?action=concevoir&lien_params=$lien_envoi";
   $nb_requete_seq = mysql_num_rows(mysql_query("select * from sequence where seq_auteur_no = $id_user"));
   if ($nb_requete_seq == 0)
      $lien_paramseq="prem=1&liste=1&refer=1&id_ref_seq=0&ordre_affiche=lenom";
   $lien1_concevoir ="parcours.php?$lien_params";
   $lien1_concevoir = urlencode($lien1_concevoir);
   $lien_envoi = str_replace ("&","|",$lien_params);
   $lien_retour_concevoir = "menu_formateur.php?action=concevoir&lien_params=$lien_envoi";
   $nb_requete_seq = mysql_num_rows(mysql_query("select * from sequence where seq_auteur_no = $id_user"));
   if ($nb_requete_seq == 0)
      $lien_paramseq="prem=1&liste=1&refer=1&id_ref_seq=0&ordre_affiche=lenom";
   $req_miens_seq ="select * from sequence where seq_auteur_no = $id_user order by seq_titre_lb";
   $req_miens = mysql_query("$req_miens_seq");
   $nb_mes_seq = mysql_num_rows($req_miens);
   $req_seq_tout = "select * from sequence where seq_publique_on=1 OR (seq_publique_on = 0 AND seq_auteur_no = $id_user) order by seq_titre_lb";
   $lien_param_toutseq="prem=1&liste=1&refer=2&ordre_affiche=lenom";
   $req_tout = mysql_query("$req_seq_tout");
   $nb_tout =  mysql_num_rows($req_tout);
   if ($nb_mes_seq > 0)
       $lien_paramseq="prem=1&liste=1&miens=1&id_ref_seq=0&ordre_affiche=lenom&titre=$mess_menu_mes_seq";
   elseif($nb_mes_seq == 0 && $nb_tout > 0)
       $lien_paramseq="prem=1&liste=1&refer=2&id_ref_seq=0&ordre_affiche=lenom&titre=$mess_menu_gest_seq_liste_tts";
   elseif($nb_tout == 0)
       $lien_paramseq="prem=0&liste=0&refer=2&miens=1&id_ref_seq=0&choix_ref=1&ordre_affiche=lenom&titre=$mess_menu_mes_seq";
   $lien_envoi_seq = str_replace ("&","|",$lien_paramseq);
   $lien_menu_seq = "menu_formateur.php?action=concevoir&lien_paramseq=$lien_envoi_seq";
   $lien_menu_seq = urlencode($lien_menu_seq);
   $lien1_menu_seq ="sequence_entree.php?$lien_paramseq";
   $lien1_menu_seq = urlencode($lien1_menu_seq);
   $lien1_suivre = "annonce_suivi.php";
   if ($nomb_grp < 4 && $nomb_grp > 0)
      $lien1_suivre.="?affiche_groupe=3";
   $lien1_suivre = urlencode($lien1_suivre);
   $lien_suivre = "menu_formateur.php?action=suivre";
   $lien1_tutorer = "annonce_tuteur.php?affiche_toutapp=0&activee=1";
   $lien1_tutorer = urlencode($lien1_tutorer);
   $lien_tutorer = "menu_formateur.php?action=tutorer";
//if ($typ_user != "FORMATEUR_REFERENT"){
  // Option prescrire
$requete_presc1 = mysql_query ("select distinct prescription.presc_grp_no from prescription,groupe WHERE prescription.presc_grp_no = groupe.grp_cdn AND ( prescription.presc_prescripteur_no = '$id_user' OR groupe.grp_tuteur_no = '$id_user')");
$nomb_presc1 = mysql_num_rows($requete_presc1);
$requete_grp1 = mysql_query ("select grp_cdn from groupe WHERE grp_resp_no = '$id_user' OR grp_tuteur_no = '$id_user'");
$nomb_grp1 = mysql_num_rows($requete_grp1);
$lien1_presc = "annonce_presc.php";
$nb_grp_u = mysql_num_rows(mysql_query ("select grp_cdn from groupe"));
if ((($nomb_grp1 < 4 || $nomb_presc1 < 4) && $typ_user != "ADMINISTRATEUR") || ($typ_user == "ADMINISTRATEUR" && $nb_grp_u < 4))
   $lien1_presc .= "?affiche_groupe=3";
$lien1_presc = urlencode($lien1_presc);
$lien2_presc = "gestion_groupe.php?cms=1&ordre_affiche=nom_groupe";
$lien2_presc = urlencode($lien2_presc);
$lien3_presc = "gestion_groupe.php?creation_groupe=1";
$lien3_presc = urlencode($lien3_presc);
$lien_presc = "menu_prescripteur.php?action=prescrire";
  // l'option Administrer
$lien1_adm = "admin.php?annu=APPRENANT&id_grp=-1";
$lien1_adm = urlencode($lien1_adm);
$lien_adm = "menu_admin.php";
//}
?>
<TABLE border="0" cellspacing="0" cellpadding="0" width='100%'><TR><TD background="images/menu/fond_menu.jpg" height="20" valign="middle" width="78%">
<A HREF="<?php  echo "trace.php?link=$lien_accueil1";?>" target='main' onMouseover="showit(-1)" class="bouton1" onClick="javascript:document.location.replace('<?php  echo $lien_accueil ;?>');">&nbsp;&nbsp;<?php  echo $mess_acc ;?>&nbsp;&nbsp;</A>
<?php if ( isset($action) && $action == "concevoir")
{?>
&nbsp; <A HREF="javascript:void(0);" target='main' onMouseover="showit(6)" class="bouton1">  &nbsp;&nbsp;<?php  echo $mess_gest_modseq ;?>&nbsp;&nbsp; </A>
&nbsp; <A HREF="javascript:void(0);" target="main" onMouseover="showit(7)" class="bouton1"> &nbsp;&nbsp;<?php  echo $mess_menu_ress;?>&nbsp;&nbsp;</A>
&nbsp; <A HREF="<?php  echo "trace.php?link=$lien_referentiel";?>" target='main' onMouseover="showit(-1)" class="bouton1">  &nbsp;&nbsp;<?php  echo $mess_refers ;?>&nbsp;&nbsp;</A>
<?php
}?>
<?php if (isset($action) && $action == "suivre")
{
   if ($nomb_grp > 0)?>
     &nbsp; <A HREF="javascript:void(0);" onMouseover="showit(5)" class="bouton1"> &nbsp;&nbsp;<?php  echo $mess_menu_suivi ;?>&nbsp;&nbsp;  </A>
<?php
}?>
&nbsp; <A HREF="javascript:void(0);" onMouseover="showit(2)" class="bouton1">  &nbsp;&nbsp;<?php  echo $mess_menu_com ;?>&nbsp;&nbsp;  </A>
&nbsp; <A HREF="javascript:void(0);" onMouseover="showit(0)" class="bouton1">&nbsp;&nbsp;  <?php  echo $mess_casier_rep_source ;?>&nbsp;&nbsp;  </A>
<?php if ((isset($action) && $action == "suivre") ||(isset($entantquetut) &&  $entantquetut == 1) || (isset($action) && $action == 'tutorer'))
{?>
  &nbsp; <A HREF="<?php  echo "trace.php?link=$lien_agenda";?>" target='main' onMouseover="showit(-1)" class="bouton1">  &nbsp;&nbsp;<?php  echo $mess_menu_agenda ;?>&nbsp;&nbsp;  </A>
<?php
}?>
&nbsp; <A HREF="javascript:void(0);" onMouseover="showit(3)" class="bouton1">&nbsp;&nbsp;<?php  echo $mess_menu_aide ;?>&nbsp;&nbsp;</A>
</TD>
<?php
/*<TD bgcolor="#037285" rowspan='2' valign="middle" align="left" height="40" width="2%">
<IMG SRC="images/menu/degrade_menu.jpg" border ="0"></TD>
*/
if ((isset($entantquetut) && $entantquetut == 1) || (isset($action) && $action == 'tutorer'))
   echo "<TD background=\"$bandeau\" rowspan='2' valign='middle' align='left' height='40' width='20%'>";
elseif (($typ_user == 'FORMATEUR_REFERENT' && isset($action) && $action == 'suivre') || (isset($entantqueform) && $entantqueform == 1) || (isset($action) && $action == 'suivre'))
   echo "<TD background=\"$bandeau\" rowspan='2' valign='middle' align='left' height='40' width='20%'>";
else
   echo "<TD  bgcolor='#037285' rowspan='2' valign='middle' align='left' height='40' width='20%'>";
echo "&nbsp;&nbsp;&nbsp;&nbsp;";
$html_concevoir = MenuConception($lien1_concevoir,$lien_retour_concevoir);
//if ($typ_user != 'TUTEUR' && (!isset($action) || (isset($action) && $action == 'concevoir')) || $typ_user == 'ADMINISTRATEUR')
//if (!isset($action) || (isset($action) && $action == 'concevoir') || $typ_user == 'ADMINISTRATEUR')
    echo $html_concevoir;
$html_tut = MenuTuteur($lien1_tutorer,$lien_tutorer);
//if ($typ_user == 'TUTEUR' || !isset($action) || $typ_user == 'ADMINISTRATEUR')
//if (!isset($action) || (isset($action) && $action == 'tutorer') || $typ_user == 'ADMINISTRATEUR')
    echo $html_tut;
$html_form = MenuFormateur($lien1_suivre,$lien_suivre);
//if ($typ_user != 'TUTEUR' && (!isset($action) || (isset($action) && $action == 'suivre')) || $typ_user == 'ADMINISTRATEUR')
//if (!isset($action) || (isset($action) && $action == 'suivre')) || $typ_user == 'ADMINISTRATEUR')
    echo $html_form;
$le_vrai_type = GetDataField ($connect,"select util_typutil_lb from utilisateur where util_login_lb='$login'","util_typutil_lb");
if ($le_vrai_type == "RESPONSABLE_FORMATION" || $le_vrai_type == "ADMINISTRATEUR")
{
   $html_presc = MenuPrescripteur($lien_presc);
//   if ($typ_user == "RESPONSABLE_FORMATION" || !isset($action) || $typ_user == 'ADMINISTRATEUR')
      echo $html_presc;
   if ($le_vrai_type == 'ADMINISTRATEUR')
   {
      $html_adm = MenuAdmin($lien1_adm,$lien_adm);
      echo $html_adm;
   }
}else
  echo "&nbsp;&nbsp;&nbsp;&nbsp;";
if ((isset($entantquetut) && $entantquetut == 1) || (isset($action) && $action == 'tutorer'))
  echo "</TD></TR><TR><TD background=\"$bandeau\" height='20' valign='bottom' style=\"padding-top:2px;\">";
elseif (($typ_user == 'FORMATEUR_REFERENT' && isset($action) && $action == 'suivre') || (isset($entantqueform) && $entantqueform == 1) || (isset($action) && $action == 'suivre'))
  echo "</TD></TR><TR><TD background=\"$bandeau\" height='20' valign='bottom' style=\"padding-top:2px;\">";
else
  echo "</TD></TR><TR><TD background=\"images/ecran-annonce/ongl01.gif\" height='20' valign='bottom' style=\"padding-top:2px;\">";
?>

<!-- Edit the dimensions of the below, plus background color-->
<ilayer name="dep1" width=800 height=20>
 <layer name="dep2" width=800 height=20>
 </layer>
</ilayer>
<div id="describe" style="width:800px;height:20px;" onMouseover="clear_delayhide();" ></div>
<script language="JavaScript1.2" type="text/javascript">
/*
Tabs Menu (mouseover)- By Dynamic Drive
For full source code and more DHTML scripts, visit http://www.dynamicdrive.com
This credit MUST stay intact for use
*/
var submenu=new Array()
//Set submenu contents. Expand as needed. For each content, make sure everything exists on ONE LINE. Otherwise, there will be JS errors.
submenu[0]='<A HREF="<?php  echo "trace.php?link=$lien_casier";?>" target="main" class="bouton2">  &nbsp;&nbsp;<?php  echo $mess_menu_mon_casier ;?>&nbsp;&nbsp;  </A> &nbsp;&nbsp;<a href="<?php  echo "trace.php?link=$lien_rf";?>" target="main" class="bouton2">&nbsp;&nbsp;<?php  echo $mess_menu_casier_groupe;?>&nbsp;&nbsp;</A></B></TD></TR></TABLE>'
submenu[1]='<a href="favoris.php?consulter=1&objet=toutes&toutes=1" target="main" class="bouton2">&nbsp;&nbsp;<?php  echo $mess_menu_prescrites ;?>&nbsp;&nbsp;</a> <?php  $cdi = GetDataField($connect,"select param_etat_lb from param_foad where param_typ_lb='cdi'","param_etat_lb");if ($cdi != ""){?><a href="<?php echo $cdi;?>" target="_blank" class="bouton2">&nbsp;&nbsp;<?php echo $mess_menu_bas_doc;?>&nbsp;&nbsp;</a><?php }?></TD></TR></TABLE>'
<?php
$etat_chat = GetDataField($connect,"select param_etat_lb from param_foad where param_typ_lb='chat'","param_etat_lb");
$etat_flib = GetDataField($connect,"select param_etat_lb from param_foad where param_typ_lb='forum_libre'","param_etat_lb");
$etat_rss = GetDataField($connect,"select param_etat_lb from param_foad where param_typ_lb='rss'","param_etat_lb");
$nb_rss = mysql_num_rows(mysql_query("select rss_cdn from rss"));
$lien_mail = "messagerie.php?vient_de_menu=menu";
$lien_mail = urlencode($lien_mail);
$lien_forum="forum/list.php?f=1&collapse=1";
$lien_forum = urlencode($lien_forum);
$lien_for_lib = "forum/list.php?f=3&collapse=1";
$lien_for_lib = urlencode($lien_for_lib);
$lien_chat = "connecte1.php";
$lien_chat = urlencode($lien_chat);
?>
submenu[2]='<a href="<?php  echo "trace.php?link=$lien_mail";?>" target="main" class="bouton2">'+
           '&nbsp;&nbsp;<?php  echo $mess_menu_mail ;?>&nbsp;&nbsp;</a>'+
           '&nbsp;&nbsp; &nbsp;<?php  if ($lien_forum != ""){?><A href="trace.php?link=<?php echo $lien_forum;?>" target="main" class="bouton2">'+
           '&nbsp;&nbsp;<?php echo "$mess_menu_forum $mess_cadre_pedago";?>&nbsp;&nbsp;</A><?php }?>'+
           '&nbsp; &nbsp;&nbsp;<?php  if ($etat_flib == "OUI"){?><A href="trace.php?link=<?php echo $lien_for_lib;?>" target="main" class="bouton2">'+
           ' &nbsp;&nbsp;<?php echo "$mess_menu_forum $mess_menu_forum_lib";?>&nbsp;&nbsp;</a>&nbsp; &nbsp;&nbsp;<?php }?>'+
           '<?php  if ($etat_chat == "OUI"){?><a href="javascript:void(0);" class="bouton2" '+
           'onclick="window.open(\'trace.php?link=<?php echo $lien_chat;?>\',\'chat_formagri\',\'scrollbars=1,resizable=yes,width=400,height=305\')">'+
           '&nbsp;&nbsp;<?php  echo $mess_menu_chat ;?>&nbsp;&nbsp;</a><?php }?></TD></TR></TABLE>'
<?php
$lien_outils = "moteurs.php?plugins=1";
$lien_outils = urlencode($lien_outils);
$lien_faq = "faq.php?entantquetut=$entantquetut&entantqueform=$entantqueform&entantquepresc=$entantquepresc";
$lien_faq = urlencode($lien_faq);
$lien_rss = "utilitaires/Rss/appel_rss.php";
$lien_rss = urlencode($lien_rss);
?>
submenu[3]='<a href="<?php  echo "trace.php?link=$lien_outils";?>" class="bouton2" target="main">&nbsp;&nbsp;<?php  echo addslashes($mess_menu_plug) ;?>&nbsp;&nbsp;</a>&nbsp; &nbsp;&nbsp;<a href="<?php  echo "trace.php?link=$lien_faq";?>" class="bouton2" target="main">&nbsp;&nbsp;<?php  echo $mess_menu_forum_formagri ;?>&nbsp;&nbsp;</a></TD></TR></TABLE>'
<?php
if (empty($lien_act)) $lien_act ="";
/*
submenu[4]='<A HREF="<?php  echo "trace.php?link=$lien_ressources";?>" target="main" class="bouton2">  &nbsp;&nbsp;<?php  echo $mess_menu_cdr;?>&nbsp;&nbsp;</A>&nbsp;&nbsp; &nbsp;&nbsp;<A HREF="<?php  echo "trace.php?link=$lien_referentiel";?>" target="main" class="bouton2">  &nbsp;&nbsp;<?php  echo $mess_annonce_rdf;?>&nbsp;&nbsp;  </A></TD></TR></TABLE>'
if ($typ_user == "ADMINISTRATEUR"){?>&nbsp;&nbsp; &nbsp;&nbsp;<a href="trace.php?link=<?php echo $lien_biblio;?>" class="bouton2" target ="main">&nbsp;&nbsp;<?php  echo $mess_menu_prescrites;?>&nbsp;&nbsp;</A><?php }
*/
if ($nomb_grp > 0)
{
?>
  submenu[5]='<?php  echo $ajout_grp;?></TD></TR></TABLE>'
<?php
}
?>
submenu[6]='<A HREF="<?php  echo "trace.php?link=$lien1_concevoir";?>" target="main" class="bouton2">  '+
           '&nbsp;&nbsp; <?php  echo $mess_menu_mon_parc;?>&nbsp;&nbsp;</A>&nbsp;&nbsp;&nbsp;'+
           '<A HREF="<?php  echo "trace.php?link=$lien1_menu_seq";?>" target="main" class="bouton2">'+
           '  &nbsp;&nbsp; <?php  echo $mess_menu_mes_seq;?></A>&nbsp;&nbsp;&nbsp;'+
           '<A HREF="<?php  echo "trace.php?link=$lien_act";?>" target="main" class="bouton2">'+
           '  &nbsp;&nbsp; <?php  echo $mess_mes_act;?></A><?php
           if ($etat_rss == "OUI" && $nb_rss > 0)
           {?>&nbsp;&nbsp;&nbsp;'+
           '<A HREF="<?php  echo "trace.php?link=$lien_rss";?>" target="main" class="bouton2">'+
           '  &nbsp;&nbsp; <?php  echo $mess_rss;?></A><?php
           }?></TD></TR></TABLE>'

submenu[7]='<A HREF="<?php  echo "trace.php?link=$lien_ressources";?>" target="main" class="bouton2">'+
           '  &nbsp;&nbsp; <?php  echo $mess_annonce_vdv;?>&nbsp;&nbsp;</A>&nbsp;&nbsp;&nbsp;'+
           '<A HREF="<?php  echo "trace.php?link=$lien_qcm";?>" target="main" class="bouton2">'+
           '  &nbsp;&nbsp; <?php  echo $mess_menu_gest_qcm;?></A></TD></TR></TABLE>'

<?php
include ('include/footer_menu.inc.php');
?>
</TD></TR></TABLE>
</body>
</html>
