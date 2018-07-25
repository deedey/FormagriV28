<?php
if (!isset($_SESSION)) session_start();
if (!isset($_SESSION['id_user']) || $_SESSION['id_user'] == "")
{
  exit();
}
require "admin.inc.php";
require 'fonction.inc.php';
include ('include/UrlParam2PhpVar.inc.php');
require "lang$lg.inc.php";
//include ("click_droit.txt");
dbConnect();
unset($_SESSION['typ_user']);
$typ_user = "TUTEUR";
$_SESSION['typ_user'] = $typ_user;
//de quel type est l'utilisateur (apprenant, formateur, administrateur)
$nom_user = GetDataField ($connect,"select util_nom_lb from utilisateur where util_cdn = $id_user","util_nom_lb");
$prenom_user = GetDataField ($connect,"select util_prenom_lb from utilisateur where util_cdn = $id_user","util_prenom_lb");
$email=GetDataField ($connect,"select util_email_lb from utilisateur where util_cdn = $id_user","util_email_lb");
$person = $nom_user;
$bandeau = "images/modules/tut_form/bandohovert1.gif";
$decon = "images/modules/tut_form/boutdeconvert";
$bando = "images/modules/tut_form/bandovert.jpg";
$son_profil = "<IMG SRC=\"images/modules/tut_form/icoprotut.jpg\" border='0'>";
$le_logo = "<IMG SRC=\"images/modules/tut_form/logovert.jpg\" border='0'>";
$pour_ajout = "<TD valign='bottom'>&nbsp;&nbsp;<FONT SIZE='4' color='#333333'>$profil $mess_typ_tut</FONT><BR>&nbsp;</TD>";
include ("include/stylemenu.inc.php");
?>
<TABLE height=40 border="0" cellspacing="0" cellpadding="0" width=100%><TR><TD align="left" valign="center">
<!--Links used to initiate the sub menus. Pass in the desired submenu index numbers (ie: 0, 1) -->
<?php
$lien_accueil = "annonce_tuteur.php?affiche_toutapp=1&activee=1";
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
$requete_grp = mysql_query ("select distinct utilisateur_groupe.utilgr_groupe_no from ".
                             "tuteur,utilisateur_groupe,groupe where ".
                             "utilisateur_groupe.utilgr_groupe_no = groupe.grp_cdn AND ".
                             "utilisateur_groupe.utilgr_utilisateur_no = tuteur.tut_apprenant_no AND ".
                             "tuteur.tut_tuteur_no= $id_user ORDER BY groupe.grp_nom_lb");
$nomb_grp = mysql_num_rows($requete_grp);
if ($nomb_grp > 0){
   $num_grp = 0;
   while($num_grp < $nomb_grp){
      $id_grp = mysql_result($requete_grp,$num_grp,"utilgr_groupe_no");
      $nom_grp = GetDataField ($connect,"select grp_nom_lb from groupe where grp_cdn  = $id_grp","grp_nom_lb");
      $id_forum = GetDataField ($connect,"select id from forums where name='$nom_grp'","id");
      $carac_grp = strlen($nom_grp);
      if ($carac_grp > 20)
         $nom_grp1 = substr($nom_grp,0,20)."..";
      else
         $nom_grp1 = $nom_grp;
      $lien_forum="forum/list.php?f=$id_forum&collapse=1";
      $lien_forum = urlencode($lien_forum);
      $ajout_grp .="&nbsp;&nbsp;<A href=\"trace.php?link=$lien_forum\" target=\"main\" class=\"bouton2\" title=\"".addslashes($nom_grp)."\">&nbsp;&nbsp;$nom_grp1&nbsp;&nbsp;</A>";
      $num_grp++;
   }
}
/*
&nbsp; <A HREF="<?php  echo "trace.php?link=$lien_ressources";?>" target='main' onMouseover="showit(-1)" class="bouton1">  &nbsp;&nbsp;<?php  echo $mess_menu_ress ;?>&nbsp;&nbsp;  </A>
*/
?>
<TABLE border="0" cellspacing="0" cellpadding="0" width='100%'><TR><TD background="images/menu/fond_menu.jpg" height='20' valign="center">
<A HREF="<?php  echo "trace.php?link=$lien_accueil";?>" target='main' onMouseover="showit(-1)" class="bouton1">&nbsp;&nbsp;<?php  echo $mess_acc ;?>&nbsp;&nbsp;</A>
&nbsp; <A HREF="javascript:void(0);" onMouseover="showit(2)" class="bouton1">  &nbsp;&nbsp;<?php  echo $mess_menu_com ;?>&nbsp;&nbsp;  </A>
&nbsp; <A HREF="<?php  echo "trace.php?link=$lien_agenda";?>" target='main' onMouseover="showit(-1)" class="bouton1">  &nbsp;&nbsp;<?php  echo $mess_menu_agenda ;?>&nbsp;&nbsp;  </A>
&nbsp; <A HREF="javascript:void(0);" onMouseover="showit(0)" class="bouton1">&nbsp;&nbsp;  <?php  echo $mess_casier_rep_source ;?>&nbsp;&nbsp;  </A>
&nbsp; <A HREF="javascript:void(0);" onMouseover="showit(3)" class="bouton1">&nbsp;&nbsp;<?php  echo $mess_menu_aide ;?>&nbsp;&nbsp;</A>
<?php /*
if ($nomb_grp > 0){?>
     &nbsp; <A HREF="javascript:void(0);" onMouseover="showit(4)" class="bouton1">  &nbsp;&nbsp;<?php  echo "$mess_menu_forum" ;?>&nbsp;&nbsp;  </A>
<?php }*/
  echo "</TD></TR><TR><TD background=\"images/modules/tut_form/bandohovert.gif\" height='20' valign='center'>";
?>

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
           ' &nbsp;&nbsp;<?php echo "$mess_menu_forum $mess_menu_forum_lib";?>&nbsp;&nbsp;</a>&nbsp; &nbsp;&nbsp;<?php }?>'+
           '<?php  if ($etat_chat == "OUI"){?><a href="javascript:void(0);" class="bouton2" '+
           'onclick="window.open(\'trace.php?link=<?php echo $lien_chat;?>\',\'\',\'scrollbars=1,resizable=yes,width=400,height=305\')">'+
           '&nbsp;&nbsp;<?php  echo $mess_menu_chat ;?>&nbsp;&nbsp;</a><?php }?></TD></TR></TABLE>'
<?php
$lien_outils = "moteurs.php?plugins=1";
$lien_outils = urlencode($lien_outils);
$lien_faq = "faq.php";
$lien_faq = urlencode($lien_faq);
?>
submenu[3]='<a href="<?php  echo "trace.php?link=$lien_outils";?>" class="bouton2" target="main">&nbsp;&nbsp;<?php  echo addslashes($mess_menu_plug) ;?>&nbsp;&nbsp;</a>&nbsp; &nbsp;&nbsp;<a href="<?php  echo "trace.php?link=$lien_faq";?>" class="bouton2" target="main">&nbsp;&nbsp;<?php  echo $mess_menu_forum_formagri ;?>&nbsp;&nbsp;</a></TD></TR></TABLE>'
<?php
include ('include/footer_menu.inc.php');
?>
</TD></TR></TABLE>
</body>
</html>
