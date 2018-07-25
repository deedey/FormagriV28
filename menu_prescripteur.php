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
unset($_SESSION['typ_user']);
if (isset($mode_user) && $mode_user == 'tout')
   $typ_user = GetDataField ($connect,"select util_typutil_lb from utilisateur where util_cdn='$id_user'","util_typutil_lb");
else
   $typ_user = "RESPONSABLE_FORMATION";
$_SESSION['typ_user'] = $typ_user;
//de quel type est l'utilisateur (apprenant, formateur, administrateur)
$nom_user = GetDataField ($connect,"select util_nom_lb from utilisateur where util_cdn = $id_user","util_nom_lb");
$prenom_user = GetDataField ($connect,"select util_prenom_lb from utilisateur where util_cdn = $id_user","util_prenom_lb");
$email=GetDataField ($connect,"select util_email_lb from utilisateur where util_cdn = $id_user","util_email_lb");
$person = $nom_user;
$bandeau = "images/modules/tut_form/bandohoviol.jpg";
$decon = "images/modules/tut_form/boutdeconviol";
$bando = "images/modules/tut_form/bandoviol.jpg";
$son_profil = "<IMG SRC=\"images/modules/tut_form/icoprorespon.jpg\" border='0'>";
$le_logo = "<IMG SRC=\"images/modules/tut_form/logoviol.jpg\" border='0'>";
$pour_ajout = "<TD valign='bottom'>&nbsp;&nbsp;<FONT SIZE='4' color='#FFFFFF'>$profil $mess_typ_rf</FONT>&nbsp;&nbsp;<BR>&nbsp;</TD>";
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
$lien_creat_grp = "gestion_groupe.php?creation_groupe=1";
$lien_creat_grp = urlencode($lien_creat_grp);

$requete_grp = mysql_query ("select * from groupe_parcours,groupe WHERE
                             gp_grp_no = grp_cdn group by grp_cdn
                             order by grp_nom_lb");
$nomb_grp = mysql_num_rows($requete_grp);
if ($nomb_grp > 0)
{
//   $lien"{|||$mess_menu_suivi $mess_menu_suivi_hg }";
   $num_grp = 0;
   while($num_grp < $nomb_grp)
   {
      $id_grp = mysql_result($requete_grp,$num_grp,"grp_cdn");
      $tut_grp = mysql_result($requete_grp,$num_grp,"grp_tuteur_no");
      $nb_prescform = mysql_num_rows(mysql_query("select presc_cdn from prescription_$id_grp where presc_formateur_no = $id_user"));
      $nb_prescpresc = mysql_num_rows(mysql_query("select presc_cdn from prescription_$id_grp where presc_prescripteur_no = $id_user"));
      if ($nb_prescform > 0)
      {
        $nom_grp = mysql_result($requete_grp,$num_grp,"grp_nom_lb");
        $carac_grp = strlen($nom_grp);
        if ($carac_grp > 20)
         $nom_grp1 = substr($nom_grp,0,20)."..";
        else
         $nom_grp1 = $nom_grp;
        $lien_grp="annonce_suivi.php?groupe_affiche[$id_grp]=1";
        $lien_grp = urlencode($lien_grp);
        $ajout_grp .="&nbsp;&nbsp;<A href=\"trace.php?link=$lien_grp\" target=\"main\" class=\"bouton2\">&nbsp;&nbsp;$nom_grp1&nbsp;&nbsp;</A>";
      }
      if ($tut_grp == $id_user)
         $nomb_grp1++;
      $num_grp++;
   }
}
$lien_referentiel = "referenciel.php";
$lien_referentiel = urlencode($lien_referentiel);
$lien_rf = "modif_rep_fic.php?formateurs=1";
$lien_rp = urlencode($lien_rp);
// gère le choix via icone pour les modules
// pour le renvoi Concevoir
$requete1_parc = "select * from parcours where parcours_cdn != 0 order by parcours_type_lb,parcours_nom_lb";
$parc_query = mysql_query ("$requete1_parc");
$les_modules = mysql_num_rows ($parc_query);
$requete2_parc = "select * from parcours where parcours_cdn != 0 AND ".
                  "parcours_auteur_no = $id_user ".
                  "order by parcours_type_lb,parcours_nom_lb";
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
// l'option formateur
$lien1_suivre = "annonce_suivi.php";
if ($nomb_grp < 4 && $nomb_grp > 0) $lien1_suivre.="?affiche_groupe=3";
$lien1_suivre = urlencode($lien1_suivre);
$lien_suivre = "menu_formateur.php?action=suivre";
// l'option tuteur
$lien1_tutorer = "annonce_tuteur.php?affiche_toutapp=0&activee=1";
$lien1_tutorer = urlencode($lien1_tutorer);
$lien_tutorer = "menu_formateur.php?action=tutorer";
//   $lien_tutorer = "menu_tuteur.php";
// Option prescrire
$lien1_presc = "annonce_presc.php";
$nb_grp_u = mysql_num_rows(mysql_query ("select grp_cdn from groupe"));
if ((($nomb_grp1 < 4 || $nb_prescpresc < 4) && $typ_user != "ADMINISTRATEUR") || ($typ_user == "ADMINISTRATEUR" && $nb_grp_u < 4))
   $lien1_presc .= "?affiche_groupe=3";
$lien1_presc = urlencode($lien1_presc);
$lien2_presc = "gestion_groupe.php?cms=1&ordre_affiche=nom_groupe";
$lien2_presc = urlencode($lien2_presc);
$lien3_presc = "gestion_groupe.php?creation_groupe=1";
$lien3_presc = urlencode($lien3_presc);
$lien_presc = "menu_prescripteur.php?action=prescrire";
$lien4_presc = "admin.php?annu=APPRENANT&id_grp=-1";
if ($typ_user == "ADMINISTRATEUR")
  $lien4_presc .= "&entantqueresp=1";
$lien4_presc = urlencode($lien4_presc);
// l'option Administrer
$lien1_adm = "admin.php?annu=ADMINISTRATEUR";
$lien1_adm = urlencode($lien1_adm);
$lien_adm = "menu_admin.php";
// liste des liens pour la gestion des formations
$lien_cms = "gestion_groupe.php?cms=1&ordre_affiche=nom_groupe";
$lien_cms = urlencode($lien_cms);
$lien_presc_grp = "prescription.php?prem=1&presc=groupe";
$lien_presc_grp = urlencode($lien_presc_grp);
// Liste des liens pour la gestion apprenant
$lien_insc = "inscription.php";
$lien_insc = urlencode($lien_insc);
$lien_insc_all="inscription_groupee.php";
$lien_insc_all = urlencode($lien_insc_all);
$lien_affecte="gestion_affectation.php?affecte_groupe=1&grp_resp=0";
$lien_affecte = urlencode($lien_affecte);
?>
<TABLE border="0" cellspacing="0" cellpadding="0" width='100%'><TR><TD background="images/menu/fond_menu.jpg" height="20" valign="center" width="78%">
<A HREF="<?php  echo "trace.php?link=$lien_accueil1";?>" target='main' onMouseover="showit(-1)" class="bouton1" onClick="javascript:document.location.replace('<?php  echo $lien_accueil ;?>');">&nbsp;&nbsp;<?php  echo $mess_acc ;?>&nbsp;&nbsp;</A>
<?php if ($action == "prescrire")
{?>
&nbsp; <A HREF="javascript:void(0);" target='main' onMouseover="showit(6)" class="bouton1">  &nbsp;&nbsp;<?php  echo $mess_menu_creer_grp ;?>&nbsp;&nbsp; </A>
&nbsp; <A HREF="javascript:void(0);" target='main' onMouseover="showit(7)" class="bouton1">  &nbsp;&nbsp;<?php  echo $mess_menu_gest_util ;?>&nbsp;&nbsp; </A>
<?php }?>

<?php if ($action == "adm")
{
   if ($nomb_grp > 0)?>
     &nbsp; <A HREF="javascript:void(0);" onMouseover="showit(5)" class="bouton1"> &nbsp;&nbsp;<?php  echo $mess_menu_suivi ;?>&nbsp;&nbsp;  </A>
<?php }?>
&nbsp; <A HREF="javascript:void(0);" onMouseover="showit(2)" class="bouton1">  &nbsp;&nbsp;<?php  echo $mess_menu_com ;?>&nbsp;&nbsp;  </A>
&nbsp; <A HREF="<?php  echo "trace.php?link=$lien_agenda";?>" target='main' onMouseover="showit(-1)" class="bouton1">  &nbsp;&nbsp;<?php  echo $mess_menu_agenda ;?>&nbsp;&nbsp;  </A>
&nbsp; <A HREF="javascript:void(0);" onMouseover="showit(0)" class="bouton1">&nbsp;&nbsp;  <?php  echo $mess_casier_rep_source;?>&nbsp;&nbsp;  </A>
&nbsp; <A HREF="javascript:void(0);" onMouseover="showit(3)" class="bouton1">&nbsp;&nbsp;<?php  echo $mess_menu_aide ;?>&nbsp;&nbsp;</A>
</TD>
<TD bgcolor="#823690" rowspan='2' valign="center" align="left" height="40" width="20%">&nbsp;&nbsp;
<?php
if (isset($mode_user) && $mode_user == 'tout')
{
    $html_concevoir = MenuConception($lien1_concevoir,$lien_retour_concevoir);
    echo $html_concevoir;
    $html_tut = MenuTuteur($lien1_tutorer,$lien_tutorer);
    echo $html_tut;
    $html_form = MenuFormateur($lien1_suivre,$lien_suivre);
    echo $html_form;
    $html_presc = MenuPrescripteur($lien_presc);
    echo $html_presc;
    if ($typ_user == 'ADMINISTRATEUR'){
       $html_adm = MenuAdmin($lien1_adm,$lien_adm);
       echo $html_adm;
    }
}
else
{
    $html_concevoir = MenuConception($lien1_concevoir,$lien_retour_concevoir);
//    if ($typ_user == 'FORMATEUR_REFERENT')
        echo $html_concevoir;
    $html_tut = MenuTuteur($lien1_tutorer,$lien_tutorer);
//    if ($typ_user == 'TUTEUR')
        echo $html_tut;
    $html_form = MenuFormateur($lien1_suivre,$lien_suivre);
//    if ($typ_user == 'FORMATEUR_REFERENT')
        echo $html_form;
// else
    $le_vrai_type = GetDataField ($connect,"select util_typutil_lb from utilisateur where util_login_lb='$login'","util_typutil_lb");
    if ($le_vrai_type == "RESPONSABLE_FORMATION" || $le_vrai_type == "ADMINISTRATEUR")
    {
           $html_presc = MenuPrescripteur($lien_presc);
//           if ($le_vrai_type == "RESPONSABLE_FORMATION")
               echo $html_presc;
           if ($le_vrai_type == 'ADMINISTRATEUR')
           {
               $html_adm = MenuAdmin($lien1_adm,$lien_adm);
               echo $html_adm;
           }
    }
}
?>
</TD></TR><TR><TD bgcolor="#823690" height='20' valign="center">

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
submenu[0]='<A HREF="<?php  echo "trace.php?link=$lien_casier";?>" target="main" class="bouton2">  &nbsp;&nbsp;<?php  echo $mess_menu_mon_casier ;?>&nbsp;&nbsp;  </A> &nbsp;&nbsp;<a href="<?php  echo "trace.php?link=$lien_rf";?>" target="main" class="bouton2">&nbsp;&nbsp;<?php  echo $mess_menu_casier_groupe;?>&nbsp;&nbsp;</A></B></TD></TR></TABLE>'
submenu[1]='<a href="favoris.php?consulter=1&objet=toutes&toutes=1" target="main" class="bouton2">&nbsp;&nbsp;<?php  echo $mess_menu_prescrites ;?>&nbsp;&nbsp;</a> <?php  $cdi = GetDataField($connect,"select param_etat_lb from param_foad where param_typ_lb='cdi'","param_etat_lb");if ($cdi != ""){?><a href="<?php echo $cdi;?>" target="_blank" class="bouton2">&nbsp;&nbsp;<?php echo $mess_menu_bas_doc;?>&nbsp;&nbsp;</a><?php }?></TD></TR></TABLE>'
<?php
$etat_chat = GetDataField($connect,"select param_etat_lb from param_foad where param_typ_lb='chat'","param_etat_lb");
$etat_flib = GetDataField($connect,"select param_etat_lb from param_foad where param_typ_lb='forum_libre'","param_etat_lb");
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
           '&nbsp;&nbsp;<?php  echo $mess_menu_mail ;?>&nbsp;&nbsp;</a>&nbsp;&nbsp; &nbsp;'+
           '<?php  if ($lien_forum != ""){?><A href="trace.php?link=<?php echo $lien_forum;?>" target="main" class="bouton2">'+
           '&nbsp;&nbsp;<?php echo "$mess_menu_forum $mess_cadre_pedago";?>&nbsp;&nbsp;</A><?php }?>'+
           '&nbsp; &nbsp;&nbsp;<?php  if ($etat_flib == "OUI"){?><A href="trace.php?link=<?php echo $lien_for_lib;?>" target="main" class="bouton2">'+
           ' &nbsp;&nbsp;<?php echo "$mess_menu_forum $mess_menu_forum_lib";?>&nbsp;&nbsp;</a>'+
           '&nbsp; &nbsp;&nbsp;<?php } if ($etat_chat == "OUI"){?><a href="javascript:void(0);" class="bouton2" '+
           'onclick="window.open(\'trace.php?link=<?php echo $lien_chat;?>\',\'\',\'scrollbars=1,resizable=yes,width=400,height=305\')">'+
           '&nbsp;&nbsp;<?php  echo $mess_menu_chat ;?>&nbsp;&nbsp;</a><?php }?></TD></TR></TABLE>'
<?php
$lien_outils = "moteurs.php?plugins=1";
$lien_outils = urlencode($lien_outils);
$lien_faq = "faq.php";
$lien_faq = urlencode($lien_faq);
?>
submenu[3]='<a href="<?php  echo "trace.php?link=$lien_outils";?>" class="bouton2" target="main">&nbsp;&nbsp;<?php  echo addslashes($mess_menu_plug) ;?>&nbsp;&nbsp;</a>&nbsp; &nbsp;&nbsp;<a href="<?php  echo "trace.php?link=$lien_faq";?>" class="bouton2" target="main">&nbsp;&nbsp;<?php  echo $mess_menu_forum_formagri ;?>&nbsp;&nbsp;</a></TD></TR></TABLE>'
submenu[4]='<A HREF="<?php  echo "trace.php?link=$lien_ressources";?>" target="main" class="bouton2">  &nbsp;&nbsp;<?php  echo $mess_menu_ress ;?>&nbsp;&nbsp;</A>&nbsp;&nbsp; &nbsp;&nbsp;<A HREF="<?php  echo "trace.php?link=$lien_referentiel";?>" target="main" class="bouton2">  &nbsp;&nbsp;<?php  echo $mess_annonce_rdf;?>&nbsp;&nbsp;  </A></TD></TR></TABLE>'
<?php
if ($nomb_grp > 0){?>
  submenu[5]='<?php  echo $ajout_grp;?></TD></TR></TABLE>'
<?php }
/*
submenu[6]='<A href="<?php  echo "trace.php?link=$lien_creat_grp";?>" target="main" class="bouton2">&nbsp;&nbsp; <?php  echo "$mess_new_format";?></A>&nbsp;&nbsp;&nbsp;<A HREF="<?php  echo "trace.php?link=$lien_cms";?>" target="main" class="bouton2">  &nbsp;&nbsp; <?php  echo $mess_suiv_form_presc;?>&nbsp;&nbsp;</A>&nbsp;&nbsp;&nbsp;<A HREF="<?php  echo "trace.php?link=$lien_presc_grp";?>" target="main" class="bouton2">  &nbsp;&nbsp; <?php  echo "$mpr_presc_form";?></A></TD></TR></TABLE>'
nbsp;&nbsp;&nbsp;<A HREF="<?php  echo "trace.php?link=$lien_affecte";?>" target="main" class="bouton2">  &nbsp;&nbsp; <?php  echo "$mess_menu_insc_grp";?>&nbsp;&nbsp;</A>&nbsp;&nbsp;&nbsp;<A HREF="<?php  echo "trace.php?link=$lien4_presc";?>" target="main" class="bouton2">&nbsp;&nbsp;<?php  echo $mess_gest_mes_app;?> &nbsp;&nbsp;</A>
*/
?>
submenu[6]='<A HREF="<?php  echo "trace.php?link=$lien_cms";?>" target="main" class="bouton2">  &nbsp;&nbsp; <?php  echo $mess_suiv_form_presc;?>&nbsp;&nbsp;</A>&nbsp;&nbsp;&nbsp;<A HREF="<?php  echo "trace.php?link=$lien1_presc";?>" target="main" class="bouton2">  &nbsp;&nbsp; <?php  echo $mess_mes_apprenants;?></A></TD></TR></TABLE>'
submenu[7]='<A HREF="<?php  echo "trace.php?link=$lien_insc";?>" target="main" class="bouton2">  &nbsp;&nbsp; <?php  echo $mess_insc_titre_ind;?>&nbsp;&nbsp;</A>&nbsp;&nbsp;&nbsp;<A HREF="<?php  echo "trace.php?link=$lien_insc_all";?>" target="main" class="bouton2">  &nbsp;&nbsp; <?php  echo $mess_insc_titre_grp ;?>&nbsp;&nbsp;</A>&nbsp;&nbsp;&nbsp;<?php if ($typ_user == 'ADMINISTRATEUR'){?><A HREF="<?php  echo "trace.php?link=$lien_affecte";?>" target="main" class="bouton2">  &nbsp;&nbsp; <?php  echo "$mess_menu_insc_grp";?>&nbsp;&nbsp;</A>&nbsp;&nbsp;&nbsp;<?php }?><A HREF="<?php  echo "trace.php?link=$lien4_presc";?>" target="main" class="bouton2">&nbsp;&nbsp;<?php  echo $mess_gest_mes_app;?> &nbsp;&nbsp;</A></TD></TR></TABLE>'

<?php
include ('include/footer_menu.inc.php');
?>
</TD></TR></TABLE>
</body>
</html>
