<?php
if (!isset($_SESSION)) session_start();
if (!isset($_SESSION['id_user']) || $_SESSION['id_user'] == "")
{
  exit();
}
require "admin.inc.php";
require 'fonction.inc.php';
include ('include/UrlParam2PhpVar.inc.php');
require 'class/ClassMenu.php';
require "lang$lg.inc.php";
//include ("click_droit.txt");
dbConnect();
$typ_user=GetDataField ($connect,"select util_typutil_lb from utilisateur where util_login_lb='$login'","util_typutil_lb");
$_SESSION['typ_user'] = $typ_user;
$nom_user = GetDataField ($connect,"select util_nom_lb from utilisateur where util_cdn = $id_user","util_nom_lb");
$prenom_user = GetDataField ($connect,"select util_prenom_lb from utilisateur where util_cdn = $id_user","util_prenom_lb");
$email=GetDataField ($connect,"select util_email_lb from utilisateur where util_cdn = $id_user","util_email_lb");
$person = $nom_user;
$bandeau = "images/modules/tut_form/bandohojaune.gif";
$decon = "images/modules/tut_form/boutdeconjaune";
$bando = "images/modules/tut_form/bandojaune.jpg";
$son_profil = "<IMG SRC=\"images/modules/tut_form/icoproadmin.jpg\" border='0'>";
$le_logo = "<IMG SRC=\"images/modules/tut_form/logoj.jpg\" border='0'>";
$pour_ajout = "<TD valign='bottom'>&nbsp;&nbsp;<FONT SIZE='4' color='#333333'>$profil $mess_typ_adm</FONT><BR>&nbsp;</TD>";
include ("include/stylemenu.inc.php");
?>
<TABLE height=40 border="0" cellspacing="0" cellpadding="0" width=100%><TR><TD align="left" valign="center">
<!--Links used to initiate the sub menus. Pass in the desired submenu index numbers (ie: 0, 1) -->
<?php
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
$lien_referentiel = "referenciel.php";
$lien_referentiel = urlencode($lien_referentiel);
$lien_rf = "modif_rep_fic.php?formateurs=1";
$lien_rp = urlencode($lien_rp);
if ($action == "concevoir"){
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
}
$requete_grp = mysql_query ("select * from groupe_parcours,groupe WHERE
                             gp_grp_no = grp_cdn group by grp_cdn
                             order by grp_nom_lb");
$nomb_grp = mysql_num_rows($requete_grp);
if ($nomb_grp > 0){
   $num_grp = 0;
   while($num_grp < $nomb_grp)
   {
      $id_grp = mysql_result($requete_grp,$num_grp,"grp_cdn");
      $nb_presc = mysql_num_rows(mysql_query ("select * from prescription_$id_grp where presc_formateur_no = $id_user"));
      if ($nb_presc > 0)
      {
         $nom_grp = GetDataField ($connect,"select grp_nom_lb from groupe where grp_cdn  = $id_grp","grp_nom_lb");
         $carac_grp = strlen($nom_grp);
         if ($carac_grp > 20)
             $nom_grp1 = substr($nom_grp,0,20)."..";
         else
             $nom_grp1 = $nom_grp;
         $lien_grp="annonce_suivi.php?groupe_affiche[$id_grp]=1";
         $lien_grp = urlencode($lien_grp);
         $ajout_grp .="&nbsp;&nbsp;<A href=\"trace.php?link=$lien_grp\" target=\"main\" class=\"bouton2\">&nbsp;&nbsp;$nom_grp1&nbsp;&nbsp;</A>";
      }
      $num_grp++;
   }
}
$lien_referentiel = "referenciel.php?flg=1";
$lien_referentiel = urlencode($lien_referentiel);
$lien_rf = "modif_rep_fic.php?formateurs=1";
$lien_rp = urlencode($lien_rp);
   // gère le choix via icone pour les modules
   $requete1_parc = "select * from parcours where parcours_cdn != 0 order by parcours_type_lb,parcours_nom_lb";
   $parc_query = mysql_query ("$requete1_parc");
   $les_modules = mysql_num_rows ($parc_query);
   $requete2_parc = "select * from parcours where parcours_cdn != 0 AND parcours_auteur_no = $id_user  order by parcours_type_lb,parcours_nom_lb";
   $parc_query = mysql_query ("$requete2_parc");
   $mes_modules = mysql_num_rows ($parc_query);
   if ($mes_modules > 0)
      $lien_params = "prem=1&liste=1&miens_parc=1&id_ref_parc=0&ordre_affiche=lenom";
   elseif ($mes_modules == 0 && $les_modules > 0)
      $lien_params = "prem=1&liste=1&refer=2&ordre_affiche=lenom";
   elseif ($les_modules == 0)
      $lien_params = "prem=1&liste=1&miens_parc=1&ordre_affiche=lenom";
   $lien1_concevoir ="parcours.php?$lien_params";
   $lien1_concevoir = urlencode($lien1_concevoir);
   $lien_envoi = str_replace ("&","|",$lien_params);
   $lien_retour_concevoir = "menu_formateur.php?action=concevoir&lien_params=$lien_envoi";
    $req_miens_seq ="select * from sequence where seq_auteur_no = $id_user";
    $req_miens = mysql_query("$req_miens_seq");
    $nb_mes_seq = mysql_num_rows($req_miens);
    $req_seq_tout = "select * from sequence where seq_publique_on=1 OR (seq_publique_on = 0 AND seq_auteur_no = $id_user)";
    $lien_param_toutseq="prem=1&liste=1&refer=2&ordre_affiche=lenom";
    $req_tout = mysql_query("$req_seq_tout");
    $nb_tout =  mysql_num_rows($req_tout);
    if ($nb_mes_seq > 0)
       $lien_paramseq="prem=1&liste=1&miens=1&id_ref_seq=0&ordre_affiche=lenom";
    elseif($nb_mes_seq == 0 && $nb_tout > 0)
       $lien_paramseq="prem=1&liste=1&refer=2&id_ref_seq=0&ordre_affiche=lenom";
    elseif($nb_tout == 0)
       $lien_paramseq="prem=0&liste=0&refer=2&miens=1&id_ref_seq=0&choix_ref=1&ordre_affiche=lenom";
    $lien_envoi_seq = str_replace ("&","|",$lien_paramseq);
    $lien_menu_seq = "menu_formateur.php?action=concevoir&lien_paramseq=$lien_envoi_seq";
    $lien_menu_seq = urlencode($lien_menu_seq);
    $lien1_menu_seq ="sequence_entree.php?$lien_paramseq";
    $lien1_menu_seq = urlencode($lien1_menu_seq);
   $lien1_suivre = "annonce_suivi.php";
   if ($nomb_grp < 7 && $nomb_grp > 0) $lien1_suivre.="?affiche_groupe=3";
   $lien1_suivre = urlencode($lien1_suivre);
   $lien_suivre = "menu_formateur.php?action=suivre";
   $lien1_tutorer = "annonce_tuteur.php?affiche_toutapp=0&activee=1";
   $lien1_tutorer = urlencode($lien1_tutorer);
   $lien_tutorer = "menu_formateur.php?action=tutorer";
  // Option prescrire
if ($nomb_grp > 0)
{
   $num_grp = 0;
   while($num_grp < $nomb_grp)
   {
      $id_grp = mysql_result($requete_grp,$num_grp,"grp_cdn");
      $nomb_presc1 = mysql_num_rows(mysql_query ("select* from prescription_$id_grp WHERE presc_prescripteur_no = $id_user"));
    $num_grp++;
   }
}
$requete_grp1 = mysql_query ("select grp_cdn from groupe WHERE grp_resp_no = '$id_user' OR grp_tuteur_no = '$id_user'");
$nomb_grp1 = mysql_num_rows($requete_grp1);
$lien1_presc = "annonce_presc.php";
$nb_grp_u = mysql_num_rows(mysql_query ("select grp_cdn from groupe"));
if ((($nomb_grp1 < 4 || (isset($nomb_presc1) && $nomb_presc1 < 4)) && $typ_user != "ADMINISTRATEUR") || ($typ_user == "ADMINISTRATEUR" && $nb_grp_u < 4))
   $lien1_presc .= "?affiche_groupe=3";
$lien1_presc = urlencode($lien1_presc);
$lien2_presc = "gestion_groupe.php?cms=1&ordre_affiche=nom_groupe";
$lien2_presc = urlencode($lien2_presc);
$lien3_presc = "gestion_groupe.php?creation_groupe=1";
$lien3_presc = urlencode($lien3_presc);
$lien_presc = "menu_prescripteur.php?action=prescrire";
// gestion des utilisateurs
$lien_app="admin.php?annu=APPRENANT&ok=non&id_grp=-1";
$lien_app = urlencode($lien_app);
$lien_respf="admin.php?annu=RESPONSABLE_FORMATION";
$lien_respf = urlencode($lien_respf);
$lien_format="admin.php?annu=FORMATEUR_REFERENT";
$lien_format = urlencode($lien_format);
$lien_tuteur="admin.php?annu=TUTEUR";
$lien_tuteur = urlencode($lien_tuteur);
$lien_admin="admin.php?annu=ADMINISTRATEUR";
$lien_admin = urlencode($lien_admin);
// Administration
$lien1_adm = "admin.php?annu=ADMINISTRATEUR";
$lien1_adm = urlencode($lien1_adm);
$lien_adm = "menu_admin.php";
//$lien_ftp="ftp.php";
//$lien_ftp = urlencode($lien_ftp);
$lien_insc = "inscription.php";
$lien_insc = urlencode($lien_insc);
$lien_insc_all="inscription_groupee.php";
$lien_insc_all = urlencode($lien_insc_all);
$lien_modif = "admin_gere.php?interface=1";
$lien_modif = urlencode($lien_modif);
$lien_admress = "admin_gere.php?ressources=1";
$lien_admress = urlencode($lien_admress);
$lien_serv = "taille.php";
$lien_serv = urlencode($lien_serv);
$lien_ech_grp="modif_rep_fic.php?communes_groupe=1&tous=1";
$lien_ech_grp = urlencode($lien_ech_grp);
$lien_stats = "stats.php";
$lien_stats = urlencode($lien_stats);
?>
<TABLE border="0" cellspacing="0" cellpadding="0" width='100%'><TR><TD background="images/menu/fond_menu.jpg" height="20" valign="center" width="78%">
<A HREF="<?php  echo "trace.php?link=$lien_accueil1";?>" target='main' onMouseover="showit(-1)" class="bouton1" onClick="javascript:document.location.replace('<?php  echo $lien_accueil ;?>');">&nbsp;&nbsp;<?php  echo $mess_acc ;?>&nbsp;&nbsp;</A>
&nbsp; <A HREF="javascript:void(0);" onMouseover="showit(4)" class="bouton1">  &nbsp;&nbsp;<?php  echo $mess_systeme ;?>&nbsp;&nbsp;  </A>
&nbsp; <A HREF="<?php  echo "trace.php?link=$lien_admress";?>" target='main' onMouseover="showit(-1)" class="bouton1">  &nbsp;&nbsp;<?php  echo $mess_menu_ress ;?>&nbsp;&nbsp;  </A>
&nbsp; <A HREF="javascript:void(0);" onMouseover="showit(5)" class="bouton1">  &nbsp;&nbsp;<?php  echo $mess_utils ;?>&nbsp;&nbsp;  </A>
&nbsp; <A HREF="javascript:void(0);" onMouseover="showit(2)" class="bouton1">  &nbsp;&nbsp;<?php  echo $mess_menu_com ;?>&nbsp;&nbsp;  </A>
<?php /*
&nbsp; <A HREF="javascript:void(0);" onMouseover="showit(0)" class="bouton1">&nbsp;&nbsp;  <?php  echo $mess_casier_rep_source ;?>&nbsp;&nbsp;  </A>
&nbsp; <A HREF="javascript:void(0);" onMouseover="showit(4)" class="bouton1">  &nbsp;&nbsp;<?php  echo $mess_menu_admin ;?>&nbsp;&nbsp;  </A>
*/?>
&nbsp; <A HREF="javascript:void(0);" onMouseover="showit(3)" class="bouton1">&nbsp;&nbsp;<?php  echo $mess_menu_aide ;?>&nbsp;&nbsp;</A></TD>
<TD bgcolor="#B9832B" rowspan='2' valign="center" align="left" height="40" width="20%">&nbsp;&nbsp;
<?php
$html_concevoir = MenuConception($lien1_concevoir,$lien_retour_concevoir);
echo $html_concevoir;
$html_tut = MenuTuteur($lien1_tutorer,$lien_tutorer);
echo $html_tut;
$html_form = MenuFormateur($lien1_suivre,$lien_suivre);
echo $html_form;
$html_presc = MenuPrescripteur($lien_presc);
echo $html_presc;
$html_adm = MenuAdmin($lien1_adm,$lien_adm);
echo $html_adm; ;
?>
</TD></TR><TR><TD  bgcolor="#B9832B" height='20' valign="center">

<!-- Edit the dimensions of the below, plus background color-->
<ilayer name="dep1" width=800 height=20>
 <layer name="dep2" width=800 height=20>
 </layer>
</ilayer>
<div id="describe" width:800px;height:20px" onMouseover="clear_delayhide()" ></div>
<script language="JavaScript1.2">
/*
Tabs Menu (mouseover)- By Dynamic Drive
For full source code and more DHTML scripts, visit http://www.dynamicdrive.com
This credit MUST stay intact for use
*/
var submenu=new Array()
//Set submenu contents. Expand as needed. For each content, make sure everything exists on ONE LINE. Otherwise, there will be JS errors.
submenu[0]='<A HREF="<?php  echo "trace.php?link=$lien_casier";?>" target="main" class="bouton2">  &nbsp;&nbsp;<?php  echo $mess_menu_mon_casier ;?>&nbsp;&nbsp;  </A> &nbsp;&nbsp;<a href="<?php  echo "trace.php?link=$lien_rf";?>" target="main" class="bouton2">&nbsp;&nbsp;<?php  echo $mess_menu_casier_groupe;?>&nbsp;&nbsp;</A> &nbsp;&nbsp;<a href="<?php  echo "trace.php?link=$lien_ech_grp";?>" target="main" class="bouton2">&nbsp;&nbsp;<?php  echo addslashes($mess_menu_casier_groupe)." ".$desgroupes;?>&nbsp;&nbsp;</A></B></TD></TR></TABLE>'
submenu[1]='<a href="favoris.php?consulter=1&objet=toutes&toutes=1" target="main" class="bouton2">&nbsp;&nbsp;<?php  echo $mess_menu_prescrites ;?>&nbsp;&nbsp;</a> <?php  $cdi = GetDataField($connect,"select param_etat_lb from param_foad where param_typ_lb='cdi'","param_etat_lb");if ($cdi != ""){?><a href="<?php echo $cdi;?>" target="_blank" class="bouton2">&nbsp;&nbsp;<?php echo $mess_menu_bas_doc;?>&nbsp;&nbsp;</a><?php }?></TD></TR></TABLE>'
<?php
$etat_chat = GetDataField($connect,"select param_etat_lb from param_foad where param_typ_lb='chat'","param_etat_lb");
$etat_flib = GetDataField($connect,"select param_etat_lb from param_foad where param_typ_lb='forum_libre'","param_etat_lb");
$lien_mail = "messagerie.php?vient_de_menu=menu";
$lien_mail = urlencode($lien_mail);
$lien_chat = "connecte1.php";
$lien_chat = urlencode($lien_chat);
$lien_gest_forum="forum/admin/index.php?page=manage";
$lien_gest_forum = urlencode($lien_gest_forum);
$lien_consult_forum="forum/index.php?f=0&collapse=0";
$lien_consult_forum = urlencode($lien_consult_forum);
?>
submenu[2]='<a href="<?php  echo "trace.php?link=$lien_mail";?>" target="main" class="bouton2">'+
           '&nbsp;&nbsp;<?php  echo $mess_menu_mail ;?>&nbsp;&nbsp;</a>&nbsp;'+
           '<A href="trace.php?link=<?php echo $lien_gest_forum;?>" target="main" class="bouton2">'+
           '&nbsp;&nbsp;<?php echo $mess_menu_gest_forum;?>&nbsp;&nbsp;</A>&nbsp;'+
           '<A href="trace.php?link=<?php echo $lien_consult_forum;?>" target="main" class="bouton2">'+
           ' &nbsp;&nbsp;<?php echo $mess_menu_consult_forum;?>&nbsp;&nbsp;</a>'+
           '<?php  if ($etat_chat == "OUI"){?>&nbsp;<a href="javascript:void(0);" class="bouton2" '+
           'onclick="window.open(\'trace.php?link=<?php echo $lien_chat;?>\',\'\',\'scrollbars=1,resizable=yes,width=400,height=305\')">'+
           '&nbsp;&nbsp;<?php  echo $mess_menu_chat ;?>&nbsp;&nbsp;</a><?php }?></TD></TR></TABLE>'
<?php
$lien_outils = "moteurs.php?plugins=1";
$lien_outils = urlencode($lien_outils);
$lien_faq = "faq.php";
$lien_faq = urlencode($lien_faq);
/*
&nbsp;&nbsp;<A HREF="<?php  echo "jabber.php";?>" target="main" class="bouton2">  &nbsp;&nbsp; <?php  echo "Jabber";?>&nbsp;&nbsp;</A>
<?php if ($id_user == 1) {?><A HREF="<?php  echo "server/webservice.php";?>" target="main" class="bouton2">  &nbsp;&nbsp; <?php  echo "Messagerie sur ef-ev";?>&nbsp;&nbsp;</A>&nbsp;&nbsp;<?php }?>
submenu[5]='<a href="<?php  echo "trace.php?link=$lien_app";?>" class="bouton2" target="main">&nbsp;&nbsp;<?php  echo $mes_des_app;?>&nbsp;&nbsp;</A>&nbsp;<A href="<?php  echo "trace.php?link=$lien_tuteur";?>" class="bouton2" target="main">&nbsp;&nbsp;<?php  echo $mes_des_tut;?>&nbsp;&nbsp;</a>&nbsp;<a href="<?php  echo "trace.php?link=$lien_format";?>" class="bouton2" target="main">&nbsp;&nbsp;<?php  echo $mes_des_fr;?>&nbsp;&nbsp;</a>&nbsp;<A href="<?php  echo "trace.php?link=$lien_respf";?>" class="bouton2" target="main">&nbsp;&nbsp;<?php  echo $mes_des_rf;?>&nbsp;&nbsp;</a>&nbsp;<a href="<?php  echo "trace.php?link=$lien_admin";?>" class="bouton2" target="main">&nbsp;&nbsp;<?php  echo $mes_des_adm;?>&nbsp;&nbsp;</a>&nbsp;</TD></TR></TABLE>'
submenu[4]='<?php if ($id_user == 1) {?><A HREF="<?php  echo "Ldap2Mysql/index.php";?>" target="main" class="bouton2">  &nbsp;&nbsp; <?php  echo $mess_auth_ldap;?>&nbsp;&nbsp;</A>&nbsp;&nbsp;<?php }?>&nbsp;&nbsp;<a href="<?php  echo "trace.php?link=$lien_modif";?>" class="bouton2" target="main">&nbsp;&nbsp;<?php  echo $mess_interface ;?>&nbsp;&nbsp;</A>&nbsp;<A HREF="<?php  echo "trace.php?link=$lien_stats";?>" target="main" class="bouton2">&nbsp;&nbsp;<?php  echo $mess_stats ;?></A>&nbsp;<a href="javascript:void(0);" class="bouton2" onclick="window.open(\'trace.php?link=<?php echo $lien_serv;?>\',\'\',\'scrollbars=1,resizable=yes,width=350,height=120\');" class="bouton2">&nbsp;&nbsp;<?php  echo $taille_serveur ;?>&nbsp;&nbsp;</a></TD></TR></TABLE>'
*/
?>
submenu[3]='<a href="<?php  echo "trace.php?link=$lien_outils";?>" class="bouton2" target="main">&nbsp;&nbsp;<?php  echo addslashes($mess_menu_plug) ;?>&nbsp;&nbsp;</A>&nbsp;<A href="<?php  echo "trace.php?link=$lien_faq";?>" class="bouton2" target="main">&nbsp;&nbsp;<?php  echo $mess_menu_forum_formagri ;?>&nbsp;&nbsp;</A></TD></TR></TABLE>'
submenu[4]='<A HREF="<?php  echo "Ldap2Mysql/index.php";?>" target="main" class="bouton2">  &nbsp;&nbsp; <?php  echo $mess_auth_ldap;?>&nbsp;&nbsp;</A>&nbsp;&nbsp;&nbsp;&nbsp;<a href="<?php  echo "trace.php?link=$lien_modif";?>" class="bouton2" target="main">&nbsp;&nbsp;<?php  echo $mess_interface ;?>&nbsp;&nbsp;</A>&nbsp;<A HREF="<?php  echo "trace.php?link=$lien_stats";?>" target="main" class="bouton2">&nbsp;&nbsp;<?php  echo $mess_stats ;?></A>&nbsp;<a href="javascript:void(0);" class="bouton2" onclick="window.open(\'trace.php?link=<?php echo $lien_serv;?>\',\'\',\'scrollbars=1,resizable=yes,width=350,height=120\');" class="bouton2">&nbsp;&nbsp;<?php  echo $taille_serveur ;?>&nbsp;&nbsp;</a></TD></TR></TABLE>'
submenu[5]='<A HREF="<?php echo "trace.php?link=$lien_insc";?>" target="main" class="bouton2">&nbsp;<?php echo "$mess_insc_titre_ind";?>&nbsp;</A><A HREF="<?php echo "trace.php?link=$lien_insc_all";?>" target="main" class="bouton2">&nbsp;<?php echo "$mess_insc_titre_grp";?>&nbsp;</A><A href="<?php echo "trace.php?link=$lien_app";?>" class="bouton2" target="main">&nbsp;<?php echo $mes_des_app;?>&nbsp;</A><A href="<?php echo "trace.php?link=$lien_tuteur";?>" class="bouton2" target="main">&nbsp;<?php echo $mes_des_tut;?>&nbsp;</A><A href="<?php echo "trace.php?link=$lien_format";?>" class="bouton2" target="main">&nbsp;<?php echo $mes_des_fr;?>&nbsp;</A><A href="<?php echo "trace.php?link=$lien_respf";?>" class="bouton2" target="main">&nbsp;<?php echo $mes_des_rf;?>&nbsp;</A><A href="<?php echo "trace.php?link=$lien_admin";?>" class="bouton2" target="main">&nbsp;<?php echo $mes_des_adm;?>&nbsp;</A></TD></TR></TABLE>'

<?php
include ('include/footer_menu.inc.php');
?>
</TD></TR></TABLE>
</body>
</html>
