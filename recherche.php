<?php
if (!isset($_SESSION)) session_start();
if (!isset($_SESSION['id_user']) || $_SESSION['id_user'] == "")
{
  exit();
}
include ("include/UrlParam2PhpVar.inc.php");
require 'config.inc.php';
require 'graphique/admin.inc.php';
require 'fonction.inc.php';
require "lang$lg.inc.php";
require 'fonction_html.inc.php';
require "langues/ress.inc.php";
dbConnect();
GLOBAL $passage;
//include ("click_droit.txt");
//Nettoyer l'url de la catégorie dans table ressource
$requete = mysql_query("update ressource_new set ress_url_lb = '' where ress_titre = '' and ress_auteurs_cmt = ''");
// gestion de l'entree par scenarios
if (isset($acces) && ($acces == "_entree" || $acces == "act_free" || $acces == "vient_de_seq"))
{
   $_SESSION['acces'] = $acces;
   if ($acces == "_entree")
      $lien_de_retour = "sequence_entree";
   elseif ($acces == "act_free")
      $lien_de_retour = "activite_free";
   elseif($acces = "vient_de_seq")
      $lien_de_retour = "sequence";
   else
      $lien_de_retour = "sequence";
}

/*
echo "<pre>";
     print_r($_SESSION);
echo "</pre>";
*/
$date_dujour = date ("Y-m-d");

if (isset($acces) && $acces == 1)
{
  //$acces = "";
  $supprime= mysql_query("DELETE FROM ressource_new where ress_cat_lb ='' and ress_doublon = 2");
}
if (isset($numero_cat) && $numero_cat > 0)
   $req_change_cat = mysql_query( "UPDATE ressource_new SET ress_cat_lb=\"$nom_cat\",ress_modif_dt=\"$date_dujour\",ress_typress_no='$numero_cat' where ress_cdn='$num_change'");
// Suppression d'un élément du référentiel
if (isset($supprimer) && $supprimer == 1)
{
  $supprimer = 0;
  $parente = GetDataField($connect,"SELECT ress_cdn from ressource_new where ress_cat_lb = '$categ_modif' AND ress_titre = ''","ress_cdn");
  $supprime= mysql_query("DELETE FROM ressource_new where ress_cdn='$num'");
  $supprime= mysql_query("update activite set act_ress_no=0 where act_ress_no='$num'");
  $parente = GetDataField ($connect,"SELECT ress_cdn from ressource_new where ress_cat_lb = '$categ_modif' AND ress_titre = ''","ress_cdn");
  $sous_catalogue = $categ_modif;
  $mess_notif = $msgRess_supOk;
  unset($supprimer);
}
// Procédure d'insertion d'un enregistrement ajouté avec réinitialisation des flags
if (isset($modifier) && $modifier == 1 && isset($inserer) && $inserer == 1)
{
    if ((!isset($rl) || (isset($rl) && $rl == '')) &&
       !is_file($_FILES['userfile']['tmp_name']) && (isset($sup) && ($sup == 'Web' || $sup == 'Url')))
    {
      include 'style.inc.php';
      echo "<CENTER><table bgColor='#298CA0' cellspacing='2'><tr><td>";
      echo "<table bgColor='#FFFFFF' cellspacing='1' width='100%'>";
      echo "<tr><td background=\"images/fond_titre_table.jpg\" colspan='2' height='34' align='center' valign='center'>";
      echo "<Font size='3' color='#FFFFFF'><strong>$mess_menu_cdr</strong></FONT></td></tr>";
      echo "<tr><td align='center' colspan='2'>";
      echo"<FONT SIZE='2'>$mess_verif_oubli</FONT></CENTER><P>&nbsp;";
      echo "</td></tr></table></td></tr></table>";
    exit;
    }
    if (isset($_FILES['userfile']['tmp_name']) && $_FILES['userfile']['name'] != "")
    {
      $nom_fichier = $_FILES['userfile']['name'];
      $taille_file = $_FILES['userfile']['size'];
      $longueur = strlen($_FILES['userfile']['name']);
      $extension = substr($_FILES['userfile']['name'],$longueur-4,4);
      $source_file=$_FILES['userfile']['tmp_name'];
      $fichier_test = $nom_fichier;
      $fichier_test = modif_nom($fichier_test);
      list($extension,$nom) = getextension($_FILES['userfile']['tmp_name']);
      if (in_array(strtolower($extension), array("exe","sh","py","ida","idq","asp","cer","cdx","asa","idc","cfm","dbm","php","php4","inc","shtml","cgi")))
      {
        include 'style.inc.php';
        echo "<CENTER><table bgColor='#298CA0' cellspacing='2'><tr><td>";
        echo "<table bgColor='#FFFFFF' cellspacing='1' width='100%'>";
        echo "<tr><td background=\"images/fond_titre_table.jpg\" colspan='2' height='34' align='center' valign='center'>";
        echo "<Font size='3' color='#FFFFFF'><strong>$mess_menu_cdr</strong></FONT></td></tr>";
        echo "<tr><td align='center' colspan='2'>";
        echo"<FONT SIZE='2'>".$_FILES['userfile']['name']." : $mess_fic_exe</FONT></CENTER><P>&nbsp;";
        echo "</td></tr></table></td></tr></table>";
        exit;
      }
      echo "ici";
      $dir="ressources/".$login."_".$id_user."/ressources";
      $dest_file=$dir."/".$fichier_test;
      $copier= move_uploaded_file($source_file , $dest_file);
      $rl = $adresse_http."/".$dir."/".$fichier_test;
    }
  $inserer=0;
  $insere = mysql_query("UPDATE ressource_new SET ress_cat_lb=\"$categ_modif\",ress_typress_no=\"$parente\",ress_url_lb=\"$rl\",".
                        "ress_auteurs_cmt=\"$aut\",ress_publique_on=\"$pub\",ress_titre=\"$tit\",ress_desc_cmt=\"".
                        htmlentities(html_entity_decode($desc,ENT_QUOTES.'iso-8859-1'),ENT_QUOTES.'iso-8859-1').
                        "\",ress_modif_dt=\"$date_dujour\",ress_type=\"$but\",ress_support=\"$sup\",ress_niveau=\"$niv\" WHERE ress_cdn=\"$num\"");
  $parente = GetDataField ($connect,"SELECT ress_cdn from ressource_new where ress_cat_lb = '$categ_modif' AND ress_titre = ''","ress_cdn");
  $sous_catalogue = $categ_modif;$detail=1;
  unset($_POST['modifier']);$_POST['modifier']=0;
}
// Procédure d'insertion d'un enregistrement modifié avec réinitialisation des flags
if (isset($ajouter) && $ajouter==1 && isset($inserer) && $inserer==1)
{

   if ((!isset($rl) || (isset($rl) && $rl == '')) &&
       !isset($_FILES['userfile']['name']) && (isset($sup) && ($sup == 'Web' || $sup == 'Url')))
   {
    include 'style.inc.php';
    echo "<CENTER><table bgColor='#298CA0' cellspacing='2'><tr><td>";
    echo "<table bgColor='#FFFFFF' cellspacing='1' width='100%'>";
    echo "<tr><td background=\"images/fond_titre_table.jpg\" colspan='2' height='34' align='center' valign='center'>";
    echo "<Font size='3' color='#FFFFFF'><strong>$mrc_ajout : $mess_mot_lk</strong></FONT></td></tr>";
    echo "<tr><td align='center' colspan='2'>";
    echo"<strong>$mess_verif_oubli rep=$rep</strong><P>";
    $num_cat_ajt = GetDataField ($connect,"SELECT ress_cdn from ressource_new where ress_cat_lb = '$categ_ajout' AND ress_titre = ''","ress_cdn");
    $lien = "recherche.php?lien_de_retour=$lien_de_retour&flg=$flg&rep=$rep&parente=$num_cat_ajt&ajouter=1&tit=$tit&desc=$desc&aut=$aut&rl=$rl&sup=$sup&doublon=$doublon&recherche=$recherche&categ_ajout=$categ_ajout&lien_sous_cat=1&miens=$miens&miens_parc=$miens_parc&parc=$id_parc&liste_act_seq=$liste_act_seq";
    $lien = urlencode($lien);
    echo "</td></tr><tr><td width='100%' colspan='2'><table width='100%' bgcolor='#CEE6EC'>".
         "<tr><td align=left><A HREF=\"trace.php?link=$lien\" ".
         "onmouseover=\"img_annonce.src='images/fiche_identite/boutretourb.gif';return true;\" ".
         "onmouseout=\"img_annonce.src='images/fiche_identite/boutretour.gif'\">";
    echo "<IMG NAME=\"img_annonce\" SRC=\"images/fiche_identite/boutretour.gif\" BORDER='0' ".
         "onLoad=\"tempImg=new Image(0,0); tempImg.src='images/fiche_identite/boutretourb.gif'\"></A>";
    echo "</td></tr></table></td></tr></table></td></tr></table>";
   exit();
  }
  $inserer=0;
  $ajouter=0;
  if (isset($rl))
     $sub=substr($rl,0,7);
  if (isset($sub) && $sub == "qcm.php" && isset($rl))
  {
     $url = (strstr($rl,'qcm.php?code=')) ? $adresse_http."/".$rl : $rl;
     $id_ress = Donne_ID ($connect,"select max(ress_cdn) from ressource_new");
     $sql_insere= mysql_query("INSERT INTO ressource_new (ress_cdn,ress_cat_lb,ress_typress_no,ress_url_lb,".
                              "ress_auteurs_cmt,ress_publique_on,ress_titre,ress_desc_cmt,ress_create_dt,".
                              "ress_ajout,ress_type,ress_support,ress_niveau) VALUES ".
                              "('$id_ress',\"$categ_ajout\",\"$parente\",\"$url\",\"$aut\",\"$pub\",\"$tit\",\"".
                               htmlentities(html_entity_decode($desc,ENT_QUOTES.'iso-8859-1'),ENT_QUOTES.'iso-8859-1').
                               "\",\"$date_dujour\",\"$login\",\"$but\",\"$sup\",\"$niv\")");
     $sql_insere= mysql_query("update activite set act_ress_no='$id_ress' where act_cdn = $id_activit");
     if (isset($parametres_qcm) && $parametres_qcm != '')
     {
       $les_params = str_replace("|","&",$parametres_qcm);
       if (isset($acced) && $acced == "act_free")
           $lien="activite_free.php?consult_act=1&id_act=$id_activit".$les_params;
       elseif (isset($acced) && $acced == "_entree")
           $lien="sequence_entree.php?consult_act=1".$les_params;
       elseif(isset($acced) && $acced == "sequence")
           $lien="sequence.php?consult_act=1".$les_params;
       $lien=urlencode($lien);
       unset($_SESSION['acced']);
       unset($_SESSION['parametres_qcm']);
       unset($_SESSION['id_activit']);
       $agent=getenv("HTTP_USER_AGENT");
       if (strstr($agent,"MSIE"))
       {
          echo "<SCRIPT Language=\"Javascript\">";
              echo "window.parent.opener.location.replace(\"trace.php?link=$lien\");";
          echo "</SCRIPT>";
       }
       else
       {
          echo "<SCRIPT Language=\"Javascript\">";
              echo "parent.parent.opener.location.replace(\"trace.php?link=$lien\");";
          echo "</SCRIPT>";
       }
       ?>
       <SCRIPT language=javascript>
           setTimeout("Quit()",500);
           function Quit() {
              self.opener=null;self.close();return false;
           }
       </SCRIPT>
       <?php
       exit();
     }
           $lien="menu_qcm.php";
           $lien=urlencode($lien);
           echo "<SCRIPT Language=\"Javascript\">";
              echo "document.location.replace(\"trace.php?link=$lien\");";
           echo "</SCRIPT>";
  }
  // Deuxième passage dans la procédure ajouter dans nouvelle catégorie: il n'y a plus qu'à insérer la nouvelle catégorie et le parent
  if (isset($doublon) && $doublon == 2)
  {
     $passage++;
     $sql_update=mysql_query("UPDATE ressource_new SET ress_cat_lb=\"$categ_ajout\",ress_modif_dt=\"$date_dujour\",".
                             "ress_typress_no=\"$parente\",ress_doublon=\"$doublon\" ".
                             "WHERE ress_cat_lb=\"\" AND ress_cdn=\"".$_SESSION['id_ress_doublon']."\"");
     $doublon = 0;
     unset($_SESSION['id_ress_doublon']);
     $rep="";
     $flag=1;
  }
// premier passage dans la procédure ajouter dans deux catégories différentes
  if (isset($doublon) && $doublon == "OUI" && (!isset($flag) || (isset($flag) && $flag == 0)))
  {
    $rep=$tit;
    $doublon = 2;
    $passage++;
   if (isset($_FILES['userfile']['tmp_name'])  && $_FILES['userfile']['name'] != "")
   {
     $source_file = $_FILES['userfile']['tmp_name'];
     $fichier_test=$_FILES['userfile']['name'];
     $fichier_test=modif_nom($fichier_test);
     list($extension,$nom)=getextension($fichier_test);
     if (in_array(strtolower($extension), array("ida","idq","asp","cer","cdx","asa","idc","cfm","dbm","php","php4","inc","shtml","cgi")))
     {
       include 'style.inc.php';
       echo "<CENTER><table bgColor='#298CA0' cellspacing='2'><tr><td>";
       echo "<table bgColor='#FFFFFF' cellspacing='1' width='100%'>";
       echo "<tr><td background=\"images/fond_titre_table.jpg\" colspan='2' height='34' align='center' valign='center'>";
       echo "<Font size='3' color='#FFFFFF'><strong>$mess_menu_cdr</strong></FONT></td></tr>";
       echo "<tr><td align='center' colspan='2'>";
       echo"<FONT SIZE='2'>$mess_fic_exe</FONT></CENTER><P>&nbsp;";
       echo "</td></tr><tr><td width='100%' colspan='2'>".
            "<table width='100%' bgcolor='#CEE6EC'><tr><td align=left>".
            "<A HREF=\"javascript:history.back();\" onmouseover=\"img_annonce.src='images/fiche_identite/boutretourb.gif';return true;\" ".
            "onmouseout=\"img_annonce.src='images/fiche_identite/boutretour.gif'\">";
       echo "<IMG NAME=\"img_annonce\" SRC=\"images/fiche_identite/boutretour.gif\" BORDER='0' ".
            "onLoad=\"tempImg=new Image(0,0); tempImg.src='images/fiche_identite/boutretourb.gif'\"></A>";
       echo "</td></tr></table></td></tr></table></td></tr></table>";
       exit;
     }
     $dir="ressources/".$login."_".$id_user."/ressources";
     $dest_file=$dir."/".$fichier_test;
     $copier= copy($source_file , $dest_file);
     $rl = $adresse_http."/".$dir."/".$fichier_test;
    //fin du test
   }
   elseif (!isset($_FILES['userfile']['tmp_name'])  && strstr($rl,"ressources/".$login."_".$id_user."/ressources") && !strstr($rl,$adresse_http))
      $rl = $adresse_http."/".$rl;
   $id_ress = Donne_ID ($connect,"select max(ress_cdn) from ressource_new");
   $sql_insere= mysql_query("INSERT INTO ressource_new (ress_cdn,ress_cat_lb,ress_typress_no,ress_url_lb,ress_auteurs_cmt,ress_publique_on,".
                            "ress_titre,ress_desc_cmt,ress_create_dt,ress_ajout,ress_type,ress_support,ress_niveau) VALUES ".
                            "('$id_ress',\"$categ_ajout\",\"$parente\",\"$rl\",\"$aut\",\"$pub\",\"".
                            NewHtmlentities($tit,ENT_QUOTES)."\",\"".htmlentities(html_entity_decode($desc,ENT_QUOTES.'iso-8859-1'),ENT_QUOTES.'iso-8859-1').
                            "\",\"$date_dujour\",\"$login\",\"$but\",\"$sup\",\"$niv\")");
   $id_ress = Donne_ID ($connect,"select max(ress_cdn) from ressource_new");
   $sql_insere= mysql_query("INSERT INTO ressource_new (ress_cdn,ress_typress_no,ress_url_lb,ress_auteurs_cmt,ress_publique_on,ress_titre,".
                            "ress_desc_cmt,ress_create_dt,ress_ajout,ress_public_no,ress_support,ress_niveau) VALUES ".
                            "('$id_ress',\"$parente\",\"$rl\",\"$aut\",\"$pub\",\"".NewHtmlentities($tit,ENT_QUOTES)."\",\"".
                               htmlentities(html_entity_decode($desc,ENT_QUOTES.'iso-8859-1'),ENT_QUOTES.'iso-8859-1').
                               "\",\"$date_dujour\",\"$login\",\"$but\",\"$sup\",\"$niv\")");
   $_SESSION['id_ress_doublon'] = $id_ress;

  }
  // Sinon dans une seule catégorie
  elseif(isset($doublon) && $doublon == 0 &&  (!isset($flag) || (isset($flag) && $flag == 0)))
  {
    if (isset($_FILES['userfile']['tmp_name']) && $_FILES['userfile']['name'] != "")
    {
      $fichier_test=$_FILES['userfile']['name'];
      $fichier_test=modif_nom($fichier_test);
      list($extension,$nom)=getextension($fichier_test);
      if (in_array(strtolower($extension), array("ida","idq","asp","cer","cdx","asa","idc","cfm","dbm","php","php4","inc","shtml","cgi")))
      {
        include 'style.inc.php';
        echo "<CENTER><table bgColor='#298CA0' cellspacing='2'><tr><td>";
        echo "<table bgColor='#FFFFFF' cellspacing='1' width='100%'>";
        echo "<tr><td background=\"images/fond_titre_table.jpg\" colspan='2' height='34' align='center' valign='center'>";
        echo "<Font size='3' color='#FFFFFF'><strong>$mess_menu_cdr</strong></FONT></td></tr>";
        echo "<tr><td align='center' colspan='2'>";
        echo"<FONT SIZE='2'>$mess_fic_exe</FONT></CENTER><P>&nbsp;";
        echo "</td></tr><tr><td width='100%' colspan='2'><table width='100%' bgcolor='#CEE6EC'>".
             "<tr><td align=left><A HREF=\"javascript:history.back();\" ".
             "onmouseover=\"img_annonce.src='images/fiche_identite/boutretourb.gif';return true;\" ".
             "onmouseout=\"img_annonce.src='images/fiche_identite/boutretour.gif'\">";
        echo "<IMG NAME=\"img_annonce\" SRC=\"images/fiche_identite/boutretour.gif\" BORDER='0' ".
             "onLoad=\"tempImg=new Image(0,0); tempImg.src='images/fiche_identite/boutretourb.gif'\"></A>";
        echo "</td></tr></table></td></tr></table></td></tr></table>";
        exit;
      }
      $dir="ressources/".$login."_".$id_user."/ressources";
      $source_file = $_FILES["userfile"]["tmp_name"];
      $dest_file=$dir."/".$fichier_test;
      $copier= move_uploaded_file($_FILES['userfile']['tmp_name'], $dest_file);
      $rl = $adresse_http."/".$dir."/".$fichier_test;
    }
    elseif (!isset($_FILES['userfile']['tmp_name']) && strstr($rl,"ressources/".$login."_".$id_user."/ressources") && !strstr($rl,$adresse_http))
      $rl = $adresse_http."/".$rl;
    $id_ress = Donne_ID ($connect,"select max(ress_cdn) from ressource_new");
    $sql_insere= mysql_query("INSERT INTO ressource_new (ress_cdn,ress_cat_lb,ress_typress_no,ress_url_lb,".
                             "ress_auteurs_cmt,ress_publique_on,ress_titre,ress_desc_cmt,ress_create_dt,ress_ajout,".
                             "ress_type,ress_support,ress_niveau) VALUES ('$id_ress',\"$categ_ajout\",\"$parente\",\"$rl\",\"$aut\",\"$pub\",\"".
                             NewHtmlentities($tit,ENT_QUOTES)."\",\"".htmlentities(html_entity_decode($desc,ENT_QUOTES.'iso-8859-1'),ENT_QUOTES.'iso-8859-1').
                             "\",\"$date_dujour\",\"$login\",\"$but\",\"$sup\",\"$niv\")");
  }
  $parente = GetDataField ($connect,"SELECT ress_cdn from ressource_new where ress_cat_lb = '$categ_ajout' AND ress_titre = ''","ress_cdn");
  $sous_catalogue = $categ_ajout;
  $_POST['ajouter'] = 0;
  unset($_POST['ajouter']);
}
//de quel type est l'utilisateur (apprenant, formateur, tuteur, formateur référent,administrateur)
$nom = GetDataField ($connect,"select util_nom_lb from utilisateur where util_cdn = $id_user","util_nom_lb");
$prenom = GetDataField ($connect,"select util_prenom_lb from utilisateur where util_cdn = $id_user","util_prenom_lb");
$dir="ressources/".$login."_".$id_user;
// Sélection et Affichage des parents principaux (caractéristique: ne contiennent pas d'url)
$comptage_sql=mysql_query ("select count(*) from ressource_new where ress_typress_no = 0");
$comptage=  mysql_result ($comptage_sql,0);
$passage= ceil($comptage/2);
$resultat_sql=  mysql_query ("select * from ressource_new  where ress_typress_no=0 AND ress_titre='' AND ress_url_lb='' order by ress_cat_lb asc");
$nombre= mysql_num_rows ($resultat_sql);
if ($nombre == 0)
{
     echo "<HTML><HEAD><TITLE>$mrc_titre</TITLE></HEAD><BODY><h2>$mrc_no_cat</h2>";
     exit;
}
else
{
 include ('style.inc.php');
?>
<script type="text/javascript" src="OutilsJs/lib/ajax/ajax_cms.js"></script>
<div id="affiche" class="Status"></div>
<div id="mon_contenu" class="cms"  onClick="javascript:$(document).ready(function() {if ($.browser.msie) {$('#mon_contenu').hide();}else{$('#mon_contenu').hide('slow');}})"
        <?php echo "title=\"$mess_clkF\">";?>
</div>
<?php
if ($mess_notif != '')
   echo notifier($mess_notif);
 // Nombre de réponses par page
 $cfg_nbres_ppage = GetDataField($connect,"select param_etat_lb from param_foad where param_typ_lb='nbr_pages_ress'","param_etat_lb");
 entete_simple($mess_menu_cdr);
 if ($typ_user != "APPRENANT" && $typ_user != "TUTEUR")
 {
   if ($flg == 0)
      $message_flg = $mrc_flg0;
   else
      $message_flg = '';
 }
 if ($message_flg != '')
   echo "<tr><td><div id='message_flg' class='sous_titre'>$message_flg</div></td></tr>";
 echo '<tr><td>'.aide_div("recherche",0,0,2,0);
 echo '<div style="float:left;margin-left:15px;"><a href="MindMapRess.php" class="bouton_new"'.
      ' title="Générer une carte heuristique des catégories">Arbre des catégories</a></div>';
 echo '<div style="float:left;margin-left:1px;"><a href="MindMapRess.php?zip=1" class="bouton_new">Zip</a></div>';
 $ReqMe = mysql_query("select * from ressource_new where (ress_url_lb like \"%ressources/%\" OR ress_url_lb like \"%qcm.php%\")
                         and ress_ajout= \"".$_SESSION['login']."\"");
 $nbrReqMe = mysql_num_rows($ReqMe);
 $ordre = ($nbrReqMe > 200) ? ' (Ordre ascendant)' : '';
 if ($nbrReqMe > 0)
 {
    echo '<div style="float:left;margin-left:15px;"><a href="MindMapMesRess.php" class="bouton_new"'.
         ' title="Générer une carte heuristique de mes propres ressources">Arbre de mes ressources '.$ordre.'</a></div>';
    echo '<div style="float:left;margin-left:1px;"><a href="MindMapMesRess.php?zip=1" class="bouton_new">Zip</a></div>';
    if ($nbrReqMe > 200)
    {
       echo '<div style="float:left;margin-left:15px;"><a href="MindMapMesRess.php?ordre=1" class="bouton_new"'.
            ' title="Générer la carte dans un ordre descendant en raison des limitations de l\'outil">'.
            'Arbre de mes ressources (Ordre desccendant)</a></div>';
       echo '<div style="float:left;margin-left:1px;"><a href="MindMapMesRess.php?zip=1&ordre=1" class="bouton_new">Zip</a></div>';
    }
    echo '<div style="float:left;margin-left:1px;"><a href="MindMapMesRess.php?vueHtml=1" class="bouton_new" target="_blank">Vue html</a></div>';
 }
 echo '</td></tr>';
/*
if ((($typ_user == 'FORMATEUR_REFERENT') || ($typ_user=='RESPONSABLE_FORMATION') || ($typ_user=='ADMINISTRATEUR')) && $ptr > 1 && $flg == 1 && $favoris != 1){
       $lien="recherche.php?lien_de_retour=$lien_de_retour&page=$nb_pages&flg=$flg&favoris=$favoris&parcours=$parcours&id_parc=$id_parc&proprio=$proprio&refer=$refer&doublon=$doublon&categ_ajout=$categ_ajout&rep=$rep&$lien_sous_cat=0&parente=$parente&ajouter=1&id_act=$id_act&modif_act=$modif_act&creat=$creat&miens=$miens&miens_parc=$miens_parc&parc=$id_parc&liste_act_seq=$liste_act_seq";
       $lien = urlencode($lien);
       echo"<div id='ajout' style=\"float:left;padding-left: 5px;\"><A HREF=\"trace.php?link=$lien\" class= 'bouton_new'>$mrc_ajout</A></div></td></tr>";
}
*/
echo"<tr><td width='100%'><table valign='top' width='100%'><tr><td align=left>";
if ($lien_sous_cat || $recherche == 1 || ($ajouter == 1 && !$inserer))
{
  $lien="recherche.php?lien_de_retour=$lien_de_retour&flg=$flg&favoris=$favoris&parcours=$parcours&id_parc=$id_parc&proprio=$proprio&refer=$refer&doublon=$doublon&rep=$rep&id_ref=$id_ref&id_ref_parc=$id_ref_parc&id_seq=$id_seq&id_act=$id_act&modif_act=$modif_act&creat=$creat&miens=$miens&miens_parc=$miens_parc&parc=$id_parc&liste_act_seq=$liste_act_seq";
  $lien = urlencode($lien);
  echo"<A HREF=\"trace.php?link=$lien\">";
  if ($lien_sous_cat || $ajouter == 1)
    echo "<strong>$mess_accueil </strong></A><small>$signe</small>";
  elseif ($recherche == 1)
    echo "<strong>$mess_accueil </strong></A>";
}
//Affichage de l'arborescence supérieure
    $pointeur=$parente;
    $sous_c=array();
    $parbis=array();
    $par1=array();
    $pr=0;
    while ($pointeur != 0)
    {
      $ptr=0;
      $resultat=mysql_query ("select * from ressource_new where ress_cdn=$pointeur");
      $nbr_result=  mysql_num_rows ($resultat);
      $sous_c[$pr]= mysql_result($resultat,$ptr,"ress_cat_lb");
      $parbis[$pr]=  mysql_result($resultat,$ptr,"ress_typress_no");
      $par1[$pr]= mysql_result($resultat,$ptr,"ress_cdn");
      $pointeur=$parbis[$pr];
      $pr++;
    }
    $pr--;
    $ptr=0;// pointeur nécessaire pour l'affichage de l'ajout
    while ($pr >-1)
    {
      $ptr++;
      $lien="recherche.php?lien_de_retour=$lien_de_retour&flg=$flg&favoris=$favoris&parcours=$parcours&id_parc=$id_parc&proprio=$proprio&refer=$refer&doublon=$doublon&rep=$rep&lien_sous_cat=1&parente=$par1[$pr]&sous_catalogue=$sous_c[$pr]&cat=$cat&sous_cat=$sous_c[$pr]&niveau=1&id_ref=$id_ref&id_ref_parc=$id_ref_parc&id_seq=$id_seq&id_act=$id_act&modif_act=$modif_act&creat=$creat&miens=$miens&miens_parc=$miens_parc&parc=$id_parc&liste_act_seq=$liste_act_seq";
      $lien = urlencode($lien);
      if ($pr == 0)
        echo " <font color='#D45211'><strong> $sous_c[$pr]</strong></font> ";
      else
        echo "<A HREF=\"trace.php?link=$lien\"><strong> $sous_c[$pr] </strong></A>$signe";
      $pr--;
    }
//Affichage de la catégorie courante
    $pr++;
    if (isset($sous_c[$pr]))
       $catego=strtoupper($sous_c[$pr]);
     echo "<table width=100%>";
     echo "<tr><td height='20' nowrap width=100%>";
//   if ($flg == 1 && !$id_seq && !$ajouter && !$modifier && !$supprimer){
   if ((!isset($ajouter) || $ajouter==0) && (!isset($modifier) || $modifier==0))
   {
     echo "<table width=100%><TR bgcolor='#F4F4F4'><td height='30' nowrap width=100% >";
     echo "<table><tr><form name='form1' method=POST action=\"recherche.php?lien_de_retour=$lien_de_retour&flg=$flg&favoris=$favoris\">";
     echo "<td align=left style=\"font-family:arial;font-weight:bold;font-size:12px;\">$mrc_rech</td>";
     echo "<td align=left><input type=text class='INPUT' name='mots' size=30></td>";
     echo "<input type='hidden' name='recherche' value='1'>";
     echo "<input type='hidden' name='type' value='TOUT'>";
      if (!isset($_SESSION['getVarsRech']) && isset($lien_de_retour) && $lien_de_retour != '')
      {
         $getVarsRech = "$lien_de_retour.php?action_act=1&parcours=$parcours&id_parc=$id_parc&proprio=$proprio&refer=$refer&choix_ress=1&id_ref=$id_ref&id_ref_parc=$id_ref_parc&id_seq=$id_seq&id_act=$id_act&modif_act=$modif_act&reselect=1&miens=$miens&miens_parc=$miens_parc&parc=$id_parc&liste_act_seq=$liste_act_seq&message=$mmsg_RessOk";
         $_SESSION['getVarsRech'] = $getVarsRech;
      }
      if (isset($_SESSION['accede']) && $_SESSION['accede'] == '_entree' && !isset($_SESSION['getVarsRech']))
      {
         $getVarsRech = "favoris.php?ajouter=1&seq=$seq&via=1";
         $_SESSION['getVarsRech'] = $getVarsRech;
      }
     echo "<td align='left'><A HREF=\"javascript:document.form1.submit();\" class='bouton_new'>Ok</A></td>";
     if (isset($_SESSION['org']))
        echo "<td align=right>$bouton_gauche<A href=\"menu_qcm.php\">$msgrech_retqcm</A>$bouton_droite</td>";
     echo "</tr></FORM></table></td></tr></table>";
    }
     // Procédure de recherche par mot clé
if ($recherche == 1)
{
/*echo "<pre>";
     print_r($_SESSION);
echo "</pre>";
*/
// on regarde si il faut rechercher des mots clés
  $mots = str_replace('%', '', $mots);
  $mots = str_replace('_', '', $mots);
  $mots = trim($mots);
  if ($mots != '')
  {
     $where  = " WHERE (ress_titre LIKE '%".addslashes($mots)."%' OR ";
     $where .= "ress_cat_lb LIKE '%$mots%' OR ";
     $where .= "ress_cat_lb LIKE '$mots%' OR ";
     $where .= "ress_cat_lb LIKE '%$mots' OR ";
     $where .= "ress_url_lb LIKE '%$mots%' OR ";
     $where .= "ress_titre LIKE '$mots%' OR ";
     $where .= "ress_desc_cmt LIKE '% ".addslashes($mots)."%' OR ";
     $where .= "ress_auteurs_cmt LIKE '$mots%' OR ";
     $where .= "ress_auteurs_cmt LIKE '%$mots%' OR ";
     $where .= "ress_support LIKE '%".addslashes($mots)."%') AND ress_cat_lb != 'Forums'";
     if ($typ_user != "ADMINISTRATEUR")
        $where .= " AND (ress_publique_on='OUI' OR (ress_publique_on='NON' AND ress_ajout='$login'))";
     $criteres = "?lien_de_retour=$lien_de_retour&flg=$flg&parcours=$parcours&id_parc=$id_parc&id_act=$id_act&proprio=$proprio&refer=$refer&mots=".urlencode($mots);
  }
  else
  {
     $where = '';
     $criteres = "?";
  }
  // Recherche du nombre de lignes
  $sql  = 'SELECT count(*) ';
  $sql .= 'FROM '.$nom_table;
  $sql .= $where;
  $resultat = mysql_query($sql);
  $enr = mysql_fetch_array($resultat);
  if (($nbtotal = $enr[0]) == 0)
    echo "<tr><td><strong>$no_rep_found</strong></td></tr></table></td></table>";
  else
  {
    if (!isset($debut))
      $debut = 0;
    // recherche des réponses
    $sql  = 'SELECT '.$champs.' ';
    $sql .= 'FROM '.$nom_table.' ';
    $sql .= $where;
    $sql .= ' ORDER BY ress_cdn DESC';
    $sql .= ' LIMIT '.$debut.','.$cfg_nbres_ppage;
    $resultat = mysql_query($sql);
    $nbenr = mysql_num_rows($resultat);
    // plage de réponses
    $barre_nav  = '<CENTER><table BORDER=0 WIDTH="100%" CELLPADDING=3 CELLSPACING=1>';
    $barre_nav .= '<tr><td BGCOLOR=#CEE6EC WIDTH="40%" ALIGN="left">';
    $barre_nav .= $mess_qcm_reponse.'(s) &nbsp;<strong>'.($debut + 1).'</strong> - <strong>'.($debut + $nbenr).'</strong>';
    $barre_nav .= ' / <strong>'.($nbtotal).'</strong></td>';
    // barre de navigation
    $barre_nav .= "<td  BGCOLOR=#CEE6EC ALIGN='left' WIDTH='60%'>&nbsp;";
    if ($nbtotal > $cfg_nbres_ppage)
    {
            $barre_nav .= barre_navigation($nbtotal, $nbenr,
                                       $cfg_nbres_ppage,
                                       $debut, $cfg_nb_pages,
                                       $criteres,$type,$typ_user,$publique,$login,$flg,$lg);
    }
    $barre_nav .= "</td></tr></table></td></tr></table>";
    // affichage de la barre de navigation
    echo $barre_nav;
    // affichage des données
    $cpt = 0;
   echo "<table BORDER=0 CELLPADDING=1 CELLSPACING=1 WIDTH='100%'><TR BGCOLOR='#2b677a'>
    <td class='barre_titre'>$msq_aff_ordre</td>
    <td class='barre_titre'>$msq_ress</td>
    <td class='barre_titre'>$mrc_cat</td>
    <td class='barre_titre'>$mess_desc</td>";
    if (isset($lien_de_retour) && $lien_de_retour != '')
       echo "<td class='barre_titre'></td>";
    echo "</tr>";
    $adres = "recherche.php";
    while ($enr = mysql_fetch_array($resultat))
    {
              echo afficher_ligne($debut + $cpt + 1, $enr, $login, $typ_user,$flg,$lg, $adres,$criteres);
      $cpt++;
    }
    if ($cpt == 2 || $nbtotal <= $cfg_nbres_ppage)
      echo "</table></td></tr></table>";
    else
      echo "</table>";
    // 2ème barre après les résultats si nécessaire
    if ($nbtotal > $cfg_nbres_ppage && $cpt > ($cfg_nbres_ppage / 2))
    {
        echo $barre_nav;
    }
    elseif ($nbtotal <= $cfg_nb_pages)
      echo "";
    else
      echo "</td></tr></table>";
  }
      echo "</td></tr></table></td></tr></table>";
  exit;
}

//Affichage de la structure d'accueil à arborescence nulle
if ((!$lien_sous_cat) && (!$ajouter) && (!$modifier))
{
  echo"<table align=Top width=100%><tr><td width=50%><table>";
  $i=0;
  while ($i != $nombre)
  {
   $l=$i+1;
   $cat = mysql_result($resultat_sql,$i,"ress_cat_lb");
   $pater = mysql_result($resultat_sql,$i,"ress_cdn");
   //Détermination des sous-catégories
    if ($i/2 != floor($i/2))
      echo"<td width=50% ALIGN=left>";
    else
      echo"<tr><td ALIGN=left>";
    $lien="recherche.php?lien_de_retour=$lien_de_retour&flg=$flg&favoris=$favoris&sous_catalogue=$cat&parcours=$parcours&id_parc=$id_parc&proprio=$proprio&refer=$refer&doublon=$doublon&rep=$rep&lien_sous_cat=1&parente=$pater&niveau=1&cat=$cat&id_ref=$id_ref&id_ref_parc=$id_ref_parc&id_seq=$id_seq&id_act=$id_act&modif_act=$modif_act&creat=$creat&miens=$miens&miens_parc=$miens_parc&parc=$id_parc&liste_act_seq=$liste_act_seq";
    $lien = urlencode($lien);
    echo"<table width=100%><tr><td><DIV id='sequence'><A HREF=\"trace.php?link=$lien\"><strong> $cat </strong></A></DIV></td></tr><tr><td><DIV id='titre'>";
    $sous_resultat_sql=mysql_query ("select * from ressource_new where ress_typress_no='$pater' AND ress_titre='' AND ress_url_lb=''");
    $nbr_sous_result=  mysql_num_rows ($sous_resultat_sql);
    if  ($nbr_sous_result > 0)
    {
        $nbr_compte=$nbr_sous_result-1;
        $niveau=1;
        $sous_i=0;
        while ($sous_i < 3)
        {
          $sous_cat= mysql_result($sous_resultat_sql,$sous_i,"ress_cat_lb");
          $parentebis=  mysql_result($sous_resultat_sql,$sous_i,"ress_typress_no");
          $parente1= mysql_result($sous_resultat_sql,$sous_i,"ress_cdn");
          $lien="recherche.php?lien_de_retour=$lien_de_retour&flg=$flg&favoris=$favoris&parcours=$parcours&id_parc=$id_parc&proprio=$proprio&refer=$refer&doublon=$doublon&rep=$rep&lien_sous_cat=1&parentebis=$parentebis&parente=$parente1&niveau=$niveau&cat=$cat&sous_catalogue=$sous_cat&id_ref=$id_ref&id_ref_parc=$id_ref_parc&id_seq=$id_seq&id_act=$id_act&modif_act=$modif_act&creat=$creat&miens=$miens&miens_parc=$miens_parc&parc=$id_parc&liste_act_seq=$liste_act_seq";
          $lien = urlencode($lien);
          echo "<A HREF=\"trace.php?link=$lien\"> $sous_cat </A>";
          if (($sous_i != 2 && ($nbr_sous_result > 3 || $nbr_sous_result == 3)) || ($sous_i != 1 && $nbr_sous_result < 3 && $nbr_sous_result > 1))
            echo" , ";
          elseif ($sous_i == $nbr_compte && ($nbr_sous_result < 3 || $nbr_sous_result == 3))
            echo".</DIV></td></tr></table>";
          else
            echo"...</DIV></td></tr></table>";
          $sous_i++;
          if($sous_i == $nbr_sous_result)
          {
            break;
          }
        }
        $niveau=0;
      }else
         echo"&nbsp;</DIV></td></tr></table>";

      if ($i/2 != floor($i/2))
        echo"</td></tr>";
      else
        echo"</td>";
  $i++;
  }
 }
}

echo"</td></tr></table></td></tr></table>";
//if (!$lien_sous_cat)
//  echo"<hr size=2 width=100% align=center color=silver>";
if ($doublon == 'OUI')
 echo"<Font color=red><SMALL><strong>$mrc_nav</strong></SMALL></Font></CENTER>";
// Affichage des composants d'une arborescence avec compteur d'enregistrements
if ((!$lien_sous_cat) && (!$ajouter) && (!$modifier))
{ 
  echo "</td></tr></table></td></tr></table>";
  echo "</td></tr></table></td></tr></table>";
  exit;
}
if (!$ajouter && !$modifier)
{
     $niveau++;
     $sous_resultat_sql=mysql_query ("SELECT * FROM ressource_new  WHERE ress_typress_no='$parente' AND ress_titre='' AND ress_url_lb=''");
     $nbr_sous_result= mysql_num_rows($sous_resultat_sql);
     echo"<table width=100% border='0'><tr>";
     if ($nbr_sous_result > 0)
     {
        echo "<td colspan='2' class='categorie' align='middle' width=100%>".
             "<table cellpadding='3' width='100%' border='0'><TR height='5'><td colspan=2'></td></tr><tr>";
     }
     $i=0;
     $parental1=array();
     $parental=array();
     while ($i < $nbr_sous_result){
       $sous_cat = mysql_result($sous_resultat_sql,$i,"ress_cat_lb");
       $parente1 = mysql_result($sous_resultat_sql,$i,"ress_cdn");
       $parente2 = mysql_result($sous_resultat_sql,$i,"ress_typress_no");
       $parental[$i] = $parente2;
       $parental1[$i] = $parente1;
       if ($typ_user == "ADMINISTRATEUR")
         $req = mysql_query("SELECT COUNT(*)  from ressource_new where (ress_cat_lb=\"$sous_cat\" OR ress_typress_no='$parente1') AND ress_titre != ''");
       else
         $req = mysql_query("SELECT COUNT(*)  from ressource_new where (ress_cat_lb=\"$sous_cat\" OR ress_typress_no='$parente1') AND ress_titre != '' AND ((ress_publique_on = 'NON' AND ress_ajout = '$login') OR ress_publique_on = 'OUI')");
       $nombre = mysql_result($req,0);
       echo"<td>";
       if (($i/2) == (floor($i/2)))
         echo"</td></tr><tr><td>";
       else
         echo"<td>";
       // Affiche Arobace en cas d'existence de sous-catégories
         $req_sql = mysql_query ("SELECT  COUNT(*) FROM ressource_new  WHERE ress_typress_no='$parente1' AND ress_titre = ''");
         $resreq = mysql_result($req_sql,0);
         if ($resreq != 0)
           $ss_cat_exist = 1;
         else
           $ss_cat_exist = 0;
       // Fin d'affichage d'arobace
        $lien="recherche.php?lien_de_retour=$lien_de_retour&flg=$flg&favoris=$favoris&parcours=$parcours&id_parc=$id_parc&proprio=$proprio&refer=$refer&doublon=$doublon&rep=$rep&lien_sous_cat=1&i=$i&sous_catalogue=$sous_cat&detail=1&parente=$parental1[$i]&niveau=$niveau&id_ref=$id_ref&id_ref_parc=$id_ref_parc&id_seq=$id_seq&id_act=$id_act&modif_act=$modif_act&creat=$creat&miens=$miens&miens_parc=$miens_parc&parc=$id_parc&liste_act_seq=$liste_act_seq";
        $lien = urlencode($lien);
         if ($ss_cat_exist){
           echo "<IMG SRC=\"images/plus.gif\" border='0'>&nbsp;";
         }else
           echo "<IMG SRC=\"images/spacer.gif\" border='0'>".nbsp(4);
        echo"<A HREF=\"trace.php?link=$lien\" class='sequence'> $sous_cat </A>  ";
         if ($nombre > 1)
             $aff_nbre = "($nombre ".strtolower($msq_ress)."s)";
         elseif ($nombre == 1)
             $aff_nbre = "($nombre ".strtolower($msq_ress).")";
         else
             $aff_nbre = "";
         echo "$aff_nbre</SMALL>";
       $i++;
     }
     echo"</td></tr></table>";
     if ($nbr_sous_result > 0)
       echo "</td></tr><TR height='5'><td colspan=2'></td></tr></table>";
//     echo"<hr size=2 width=100% align=center color=silver>";

// Affichages des enregistrements d'une sous-catégorie en cours
     if (empty($page))
       $page = 1;
     $nb_affiche = GetDataField($connect,"select param_etat_lb from param_foad where param_typ_lb='nbr_pages_ress'","param_etat_lb");
     if ($typ_user == "ADMINISTRATEUR")
       $req_nb_aff = mysql_query("select count(*) from ressource_new where ress_cat_lb=\"$sous_catalogue\" AND ress_titre != '' ORDER BY ress_titre ASC");
     else
       $req_nb_aff = mysql_query("select count(*) from ressource_new where ress_cat_lb=\"$sous_catalogue\" AND ress_titre != '' AND ((ress_publique_on='NON' AND ress_ajout='$login') OR ress_publique_on='OUI') ORDER BY ress_titre ASC ");
     $total_aff = mysql_result($req_nb_aff,0);
     $debut = ($page - 1) * $nb_affiche;
     if ($typ_user == "ADMINISTRATEUR")
       $req2=mysql_query("select * from ressource_new where ress_cat_lb=\"$sous_catalogue\" AND ress_titre != '' ORDER BY ress_titre ASC LIMIT $debut,$nb_affiche");
     else
       $req2=mysql_query("select * from ressource_new where ress_cat_lb=\"$sous_catalogue\" AND ress_titre != '' AND ((ress_publique_on='NON' AND ress_ajout='$login') OR ress_publique_on='OUI') ORDER BY ress_titre ASC LIMIT $debut,$nb_affiche");
     if (!$req2){
       echo $mrc_rien;
       exit;
     }
     if ($total_aff > 1)
        $aff_tot = strtolower($msq_ress)."s";
     elseif ($total_aff == 1)
        $aff_tot = strtolower($msq_ress);
     else
        $aff_tot = "";
     $nbrt= mysql_num_rows($req2);
     echo"<table align=Top width=100%>";
     if ($total_aff > $nb_affiche){
       echo "<TR bgcolor='#CEE6EC'><td align=left>$mess_admin_total : $total_aff $aff_tot</td><td align=middle>";
       $nb_pages = ceil($total_aff/$nb_affiche);
       if ($page == 1){
         echo "<IMG SRC=\"images/gauche_off.gif\" border=0></a>";
         echo "<IMG SRC=\"images/gauche_off.gif\" border=0></a>&nbsp;&nbsp;";
       }else{
         $lien = "recherche.php?lien_de_retour=$lien_de_retour&flg=$flg&favoris=$favoris&parcours=$parcours&id_parc=$id_parc&proprio=$proprio&refer=$refer&doublon=$doublon&rep=$rep&lien_sous_cat=1&parente=$par1[$pr]&sous_catalogue=$sous_c[$pr]&cat=$cat&sous_cat=$sous_c[$pr]&niveau=1&id_ref=$id_ref&id_ref_parc=$id_ref_parc&id_seq=$id_seq&id_act=$id_act&modif_act=$modif_act&creat=$creat&miens=$miens&miens_parc=$miens_parc&parc=$id_parc&liste_act_seq=$liste_act_seq&page=1&total_aff=$total_aff";
         $lien = urlencode($lien);
         echo "<a HREF=\"trace.php?link=$lien\"><IMG SRC=\"images/gauche_on.gif\" border=0></a>";
         echo "<a HREF=\"trace.php?link=$lien\"><IMG SRC=\"images/gauche_on.gif\" border=0></a>&nbsp;&nbsp;";
       }
       if ($page == 1)
         echo "<IMG SRC=\"images/gauche_off.gif\" border=0></a>&nbsp;&nbsp;";
       else{
         $page_avant = $page-1;
         $lien = "recherche.php?lien_de_retour=$lien_de_retour&flg=$flg&favoris=$favoris&parcours=$parcours&id_parc=$id_parc&proprio=$proprio&refer=$refer&doublon=$doublon&rep=$rep&lien_sous_cat=1&parente=$par1[$pr]&sous_catalogue=$sous_c[$pr]&cat=$cat&sous_cat=$sous_c[$pr]&niveau=1&id_ref=$id_ref&id_ref_parc=$id_ref_parc&id_seq=$id_seq&id_act=$id_act&modif_act=$modif_act&creat=$creat&miens=$miens&miens_parc=$miens_par&parc=$id_parc&liste_act_seq=$liste_act_seq&page=$page_avant&total_aff=$total_aff";
         $lien = urlencode($lien);
         echo "<a HREF=\"trace.php?link=$lien\"><IMG SRC=\"images/gauche_on.gif\" border=0></a>&nbsp;&nbsp;";
       }
       for ($affichage = 1;$affichage <= $nb_pages;$affichage++){
         if ($page == $affichage){
           if ($nb_pages >15)
             echo "<FONT SIZE='1'> $affichage</FONT>";
           else
             echo " $affichage";
        }else {
           $lien = "recherche.php?lien_de_retour=$lien_de_retour&flg=$flg&favoris=$favoris&parcours=$parcours&id_parc=$id_parc&proprio=$proprio&refer=$refer&doublon=$doublon&rep=$rep&lien_sous_cat=1&parente=$par1[$pr]&sous_catalogue=$sous_c[$pr]&cat=$cat&sous_cat=$sous_c[$pr]&niveau=1&id_ref=$id_ref&id_ref_parc=$id_ref_parc&id_seq=$id_seq&id_act=$id_act&modif_act=$modif_act&creat=$creat&miens=$miens&miens_parc=$miens_parc&parc=$id_parc&liste_act_seq=$liste_act_seq&page=$affichage&total_aff=$total_aff";
           $lien = urlencode($lien);
           if ($nb_pages > 15)
             echo "<a HREF=\"trace.php?link=$lien\"><FONT SIZE='1'> $affichage</FONT></a>";
           else
             echo "<a HREF=\"trace.php?link=$lien\"> $affichage</a>";
         }
         echo "&nbsp;&nbsp;";
       }
       if ($page == $nb_pages)
         echo "<IMG SRC=\"images/droite_off.gif\" border=0></a>&nbsp;&nbsp;";
       else{
         $page_suivant = $page+1;
         $lien = "recherche.php?lien_de_retour=$lien_de_retour&flg=$flg&favoris=$favoris&parcours=$parcours&id_parc=$id_parc&proprio=$proprio&refer=$refer&doublon=$doublon&rep=$rep&lien_sous_cat=1&parente=$par1[$pr]&sous_catalogue=$sous_c[$pr]&cat=$cat&sous_cat=$sous_c[$pr]&niveau=1&id_ref=$id_ref&id_ref_parc=$id_ref_parc&id_seq=$id_seq&id_act=$id_act&modif_act=$modif_act&creat=$creat&miens=$miens&miens_parc=$miens_parc&parc=$id_parc&liste_act_seq=$liste_act_seq&page=$page_suivant&total_aff=$total_aff";
         $lien = urlencode($lien);
         echo "<a HREF=\"trace.php?link=$lien\"><IMG SRC=\"images/droite_on.gif\" border=0></a>&nbsp;&nbsp;";
       }
       if ($page == $nb_pages){
         echo "<IMG SRC=\"images/droite_off.gif\" border=0></a>";
         echo "<IMG SRC=\"images/droite_off.gif\" border=0></a>&nbsp;&nbsp;";
       }else{
         $lien = "recherche.php?lien_de_retour=$lien_de_retour&flg=$flg&favoris=$favoris&parcours=$parcours&id_parc=$id_parc&proprio=$proprio&refer=$refer&doublon=$doublon&rep=$rep&lien_sous_cat=1&parente=$par1[$pr]&sous_catalogue=$sous_c[$pr]&cat=$cat&sous_cat=$sous_c[$pr]&niveau=1&id_ref=$id_ref&id_ref_parc=$id_ref_parc&id_seq=$id_seq&id_act=$id_act&modif_act=$modif_act&creat=$creat&miens=$miens&miens_parc=$miens_parc&parc=$id_parc&liste_act_seq=$liste_act_seq&page=$nb_pages&total_aff=$total_aff";
         $lien = urlencode($lien);
         echo "<a HREF=\"trace.php?link=$lien\"><IMG SRC=\"images/droite_on.gif\" border=0></a>";
         echo "<a HREF=\"trace.php?link=$lien\"><IMG SRC=\"images/droite_on.gif\" border=0></a>&nbsp;&nbsp;";
       }
       echo"</td></tr>";
    }
       $l=0;
       while ($l < $nbrt){
         echo couleur_tr($l+1,'')."<td colspan=2 width=100%><table colspan=2 width=100% border='0'><tr><td width=75%>";
         $num= mysql_result($req2,$l,"ress_cdn");
         $par= mysql_result($req2,$l,"ress_typress_no");
         $init= mysql_result($req2,$l,"ress_ajout");
         $auteur_ress= mysql_result($req2,$l,"ress_auteurs_cmt");
         $categ= mysql_result($req2,$l,"ress_cat_lb");
         $descrip= html_entity_decode(mysql_result($req2,$l,"ress_desc_cmt"),ENT_QUOTES,'iso-8859-1');
         $liens= mysql_result($req2,$l,"ress_url_lb");
         $doub= mysql_result($req2,$l,$double);
         $niv= mysql_result($req2,$l,"ress_niveau");
         $publique= mysql_result($req2,$l,"ress_publique_on");
         $titr= html_entity_decode(mysql_result($req2,$l,"ress_titre"),ENT_QUOTES,'iso-8859-1');
         $sup= mysql_result($req2,$l,"ress_support");
         $resstype= mysql_result($req2,$l,"ress_type");
         $assise = 1;// Flag servant à faire afficher un lien uniquement si un lien existe......pour le faire fonctionner le remettre à 0
         $avertir = 0;
         if ($liens != "")
           $assise=1;
         else
           $assise=0;
         if ($id_seq > 0 && $favoris == 1){
            $nom_seq = GetDataField ($connect,"select seq_titre_lb from sequence where seq_cdn=$id_seq","seq_titre_lb");
            $lien = "favoris.php?ajouter=1&seq=$id_seq&via=1&id_ress=$num";
            $lien = urlencode($lien);
            echo "<input type=checkbox ".bullet(NewHtmlentities($mrc_clik." ".$nom_seq),"$mrc_refer","RIGHT","ABOVE",300)." onclick=\"window.location='trace.php?link=$lien';return true;\">";
         }
//         if ($par !=0 && $titr !="" && ((($typ_user == "ADMINISTRATEUR" || $login == $init) && $publique=="NON") || $publique == "OUI")){
         if ($titr !="" && ((($typ_user == "ADMINISTRATEUR" || $login == $init) && $publique=="NON") || $publique == "OUI"))
         {
            if ($assise == 1 && ((($typ_user == "ADMINISTRATEUR" || $init == $login) && $publique=="NON") || $publique == "OUI"))
              $ok=1;
            else
              $liens=0;
            if ($assise == 1)
            {
               $req_serv = mysql_query("select * from serveur_ressource");
               $nb_req_serv = mysql_num_rows($req_serv);
               if ($nb_req_serv > 0)
               {
                  $transit = 0;
                  $ir = 0;
                  while ($ir < $nb_req_serv)
                  {
                     $adr = mysql_result($req_serv,$ir,"serveur_nomip_lb");
                     $params = mysql_result($req_serv,$ir,"serveur_param_lb");
                     $label = mysql_result($req_serv,$ir,"serveur_label_lb");
                     if ($label != "")
                     {
                        if (strstr($liens,$adr) && strstr($liens,$label))
                        {
                           $liens = str_replace("%label=$label","",$liens);
                           $liens .= $params;
                           $transit = 1;
                           break;
                        }
                     }
                     elseif ($label == "" && strstr($liens,"label="))
                     {
                        $ir++;
                        continue;
                     }
                     elseif ($label == "" && !strstr($liens,"label="))
                     {
                        if (strstr($liens,$adr)){
                           $liens .= $params;
                           $transit = 1;
                           break;
                        }
                     }
                   $ir++;
                  }
                  $liens = urldecode($liens);
                  if ($transit == 1)
                  {
                     echo "<A HREF='javascript:void(0);' onclick=\"window.open('$liens','','resizable=yes,scrollbars=yes,status=no')\">";
                     echo "<strong>$titr </strong></A><SMALL> ($mrc_auteur:$auteur_ress,&nbsp;$categ&nbsp;,&nbsp;$sup&nbsp;,&nbsp;$resstype)";
                  }
               }
               if ($liens == "")
               {
                  $descrip = str_replace("\r\n","<br />",$descrip);
                  echo "<A HREF='javascript:void(0);' ".bulle(NewHtmlentities("<strong>".addslashes($mrc_tit_ress)."</strong> : ".$titr."<br /><strong>".$mrc_auteur."</strong> : ".$auteur_ress."<br /><strong>$mess_desc/".$mrc_mod_emp."</strong> : ".$descrip."<br /><strong>".$mrc_opr."</strong> : ".$resstype."<br /><strong>".$mrc_sup_ress."</strong> : $sup"),$ress_ss_lien,"RIGHT","ABOVE",300)."</A>";
//                  "onMouseOver=\"overlib('<strong>".addslashes($mrc_tit_ress)."</strong> : ".addslashes($titr)."<br /><strong>".addslashes($mrc_auteur)."</strong> : ".addslashes($auteur_ress)."<br /><strong>$mess_desc/".addslashes($mrc_mod_emp)."</strong> : ".addslashes($descrip)."<br /><strong>".addslashes($mrc_opr)."</strong> : ".addslashes($resstype)."<br /><strong>".addslashes($mrc_sup_ress)."</strong> : $sup',ol_hpos,RIGHT,WIDTH,'300',CAPTION, '<center>$ress_ss_lien</center>')\" onMouseOut=\"nd()\"><strong>$titr </strong></A>";
                  echo "<strong>$titr</strong><SMALL> ($mrc_auteur:$auteur_ress,&nbsp;$categ&nbsp;,&nbsp;$sup&nbsp;,&nbsp;$resstype)&nbsp;,&nbsp;";
               }
               elseif ((strstr($liens,"ParWeb")) || (strstr($liens,"parweb"))  || (strstr($liens,"Legweb"))  || (strstr($liens,"legweb")) || (strstr($liens,"Tatweb"))  || (strstr($liens,"tatweb")) || (strstr($liens,"Qcmweb"))  || (strstr($liens,"qcmweb")) || (strstr($liens,"Elaweb")) || (strstr($liens,"elaweb")))
               {
                  $liens .= "&nom=pat&prenom=del&email=totot.fr@educ.fr&pathscore=";
                  echo "<A HREF='javascript:void(0);' onclick=\"window.open('$liens','','resizable=yes,scrollbars=yes,status=no')\">";
                  echo "<strong>$titr </strong></A><SMALL> ($mrc_auteur:$auteur_ress,&nbsp;$categ&nbsp;,&nbsp;$sup&nbsp;,&nbsp;$resstype)";
               }
               elseif (strstr($liens,"bder"))
               {
                  $avertir = 1;
                  $lien=$liens;
                  $lien = urlencode($lien);
                  echo "<A HREF='javascript:void(0);' onclick=\"window.open('trace.php?link=$lien','','resizable=yes,scrollbars=yes,status=no')\" onMouseOver=\"overlib('$mrc_bder &nbsp;<font color=red>$sup</font> ..',ol_hpos,RIGHT,WIDTH,'250',CAPTION, '<center>$mrc_ress</center>')\" onMouseOut=\"nd()\"><strong>$titr </strong></A>";
                  echo "<SMALL> ($mrc_auteur:$auteur_ress,&nbsp;$categ&nbsp;,&nbsp;$sup&nbsp;,&nbsp;$resstype)&nbsp;,&nbsp;";
               }
               elseif (strstr($liens,"qcm.php"))
               {
                 $lien=$liens."&provenance=recherche";
                 $lien = urlencode($lien);
                 echo"<A HREF='javascript:void(0);' onclick=\"window.open('trace.php?link=$lien','','resizable=yes,scrollbars=yes,status=no')\">";
                 echo "<strong>$titr </strong></A><SMALL> ($mrc_auteur:$auteur_ress,&nbsp;$categ&nbsp;,&nbsp;$sup&nbsp;,&nbsp;$resstype)";
               }
               elseif (strstr(strtolower($liens),"educagrinet"))
               {
                 $lien = str_replace("acces.html","direct.html",$liens)."&url=$url_ress&auth_cdn=$auth_cdn";
                 $lien = urlencode($lien);
                 echo"<A HREF='javascript:void(0);' onclick=\"window.open('trace.php?link=$lien','','resizable=yes,scrollbars=yes,status=no')\">";
                 echo "<strong>$titr </strong></A><SMALL> ($mrc_auteur:$auteur_ress,&nbsp;$categ&nbsp;,&nbsp;$sup&nbsp;,&nbsp;$resstype)";
               }
               elseif (strstr(strtolower($liens),".doc") || strstr(strtolower($liens),".xls") || strstr(strtolower($liens),".xlt"))
               {
                 echo "<A HREF='$liens' target='_blank'><strong>$titr</strong></A><SMALL> ($mrc_auteur:$auteur_ress,&nbsp;$categ&nbsp;,&nbsp;$sup&nbsp;,&nbsp;$resstype)";
               }
               elseif(strstr(strtolower($liens),".flv") ||
                      strstr(strtolower($liens),".mp3") ||
                      strstr(strtolower($liens),".swf") ||
                      strstr(strtolower($liens),".mp4") ||
                      strstr(strtolower($media_act),'.webm') ||
                      strstr(strtolower($media_act),'.ogv'))
               {
                 $actit++;
                 $media_act = $liens;
                 $ajoutLink= (strstr($media_act,'http://')) ? "" : "$monURI/";
                 echo "<A HREF='javascript:void(0);' onclick=\"window.open('lanceMedia.php?id_ress=$num','','resizable=yes,scrollbars=yes,status=no')\">";
                 echo " <strong>$titr</strong></A><SMALL> ($mrc_auteur:$auteur_ress,&nbsp;$categ&nbsp;$sup&nbsp;,&nbsp;$resstype)<br />";
                 if(strstr(strtolower($liens),".flv") ||
                      strstr(strtolower($liens),".mp3") ||
                      strstr(strtolower($liens),".swf"))
                 {
                    $largeur = "220";
                    $hauteur = "140";
                    echo "<div id='insertMedia$num'>";
                    echo '<div id="player'.$actit.'" style="clear:both;"></div>';
                    echo '<script type="text/javascript">
	                         var s'.$actit.' = new SWFObject("ressources/flvplayer.swf","single","'.$largeur.'","'.$hauteur.'","7");
	                         s'.$actit.'.addParam("allowscriptaccess","always");
	                         s'.$actit.'.addParam("allowfullscreen","true");
	                         s'.$actit.'.addParam("wmode","transparent");
	                         s'.$actit.'.addVariable("file","'.$ajoutLink.$media_act.'");
	                         s'.$actit.'.addVariable("image","images/menu/logformb.gif");
	                         s'.$actit.'.addVariable("backcolor","0xFFFFFF");
	                         s'.$actit.'.addVariable("frontcolor","0x000000");
	                         s'.$actit.'.addVariable("lightcolor","0xFF0000");
	                         s'.$actit.'.addVariable("screencolor","0x000000");
	                         s'.$actit.'.write("player'.$actit.'");
                       </script>';
                    echo "</div>";
                 }
                 else
                 {
                    $largeur = "220";
                    $hauteur = "140";
                    echo '<iframe src="lanceMedia.php?id_ress='.$num.'&largeur='.$largeur.'&hauteur='.$hauteur.
                    '" width="220" height="140" frameborder=0 scrolling="no"></iframe>';
                 }
               }
               elseif ($transit != 1 && $resstype != 'XAPI')
               {
                  echo "<A HREF='javascript:void(0);' onclick=\"window.open('$liens','','resizable=yes,scrollbars=yes,status=no')\">";
                  echo"<strong>$titr</strong></A><SMALL> ($mrc_auteur:$auteur_ress,&nbsp;$categ&nbsp;$sup&nbsp;,&nbsp;$resstype)";
               }
               elseif ($resstype == 'XAPI')
               {
                        $commentAct = GetdataField ($connect,"select act_commentaire_cmt from activite where act_ress_no=$num","act_commentaire_cmt");
                        $commentaire = html_entity_decode($commentAct,ENT_QUOTES,'iso-8859-1');
                        $lien = $liens.TinCanTeach ('teacher|0|0|0|0',$liens,$commentaire);
                         echo "<A HREF='javascript:void(0);' onclick=\"window.open('$lien','','resizable=yes,scrollbars=yes,status=no')\">";
                         echo"<strong>$titr</strong></A><SMALL> ($mrc_auteur:$auteur_ress,&nbsp;$categ&nbsp;$sup&nbsp;,&nbsp;$resstype)";
               }
             }
             else
             {
                  $descrip = str_replace("\r\n","<br />",$descrip);
                  echo "<A HREF=\"javascript:void(0);\" style=\"cursor:help;\" ".
                        bulle(NewHtmlentities("<strong>".addslashes($mrc_tit_ress)."</strong> : ".
                        $titr."<br /><strong>".$mrc_auteur."</strong> : ".
                       $auteur_ress."<br /><strong>$mess_desc/".$mrc_mod_emp."</strong> : ".
                       $descrip."<br /><strong>".$mrc_opr."</strong> : ".$resstype."<br /><strong>".
                       $mrc_sup_ress."</strong> : $sup"),$ress_ss_lien,"RIGHT","ABOVE",300);
//                  "onMouseOver=\"overlib('<strong>".addslashes($mrc_tit_ress)."</strong> : ".addslashes($titr)."<br /><strong>".addslashes($mrc_auteur)."</strong> : ".addslashes($auteur_ress)."<br /><strong>$mess_desc/".addslashes($mrc_mod_emp)."</strong> : ".addslashes($descrip)."<br /><strong>".addslashes($mrc_opr)."</strong> : ".addslashes($resstype)."<br /><strong>".addslashes($mrc_sup_ress)."</strong> : $sup',ol_hpos,RIGHT,WIDTH,'300',CAPTION, '<center>$ress_ss_lien</center>')\" onMouseOut=\"nd()\"><strong>$titr </strong></A>";
                  echo "<strong>$titr</strong></A><SMALL> ($mrc_auteur:$auteur_ress,&nbsp;$categ&nbsp;,&nbsp;$sup&nbsp;,&nbsp;$resstype)&nbsp;,&nbsp;";
                //echo"<strong>$titr </strong><SMALL> ($mrc_auteur:$auteur_ress,&nbsp;$categ&nbsp;$sup&nbsp;,&nbsp;$mess_niveau&nbsp;$niv&nbsp;,&nbsp;$resstype)";
             }
             if ((($typ_user == "FORMATEUR_REFERENT") || ($typ_user=="RESPONSABLE_FORMATION") || ($typ_user=="ADMINISTRATEUR")) && $flg == 0)
             {
               $passe_ici = 1;
                //Test pour savoir si l'on modifie une activite ou si l'on en crée une
                //flag permettant de savoir qu'on est en mode insertion pour ne pas afficher modif et supp plus loin
                if ($creat == 1)
                {
                    $lien="$lien_de_retour.php?activite=1&parcours=$parcours&id_parc=$id_parc&proprio=$proprio&refer=$refer&id_ress=$num&choix_ress=1&id_ref=$id_ref&id_ref_parc=$id_ref_parc&id_seq=$id_seq&miens=$miens&miens_parc=$miens_parc&parc=$id_parc&liste_act_seq=$liste_act_seq&message=$mmsg_RessOk";
                    $lien = urlencode($lien);
                    echo "&nbsp;&nbsp;<A HREF=\"trace.php?link=$lien\"";
                    if ($avertir == 0)
                      echo bulle($mrc_ins_seq,"","LEFT","ABOVE",150)."<IMG SRC=\"images/modules/tut_form/icosequen20.gif\" BORDER=0></A>";
                    else
                    {
                      bulle(NewHtmlentities("$mrc_lk_act $sup <br /> $mrc_ass_ress"),$mrc_ress,"CENTER","ABOVE",250).
                      "<IMG SRC=\"images/modules/tut_form/icosequen20.gif\"  BORDER=0></A>";
                    }
                }
                else
                {
                    $lien="$lien_de_retour.php?action_act=1&parcours=$parcours&id_parc=$id_parc&proprio=$proprio&refer=$refer&id_ress=$num&choix_ress=1&id_ref=$id_ref&id_ref_parc=$id_ref_parc&id_seq=$id_seq&id_act=$id_act&modif_act=$modif_act&reselect=1&miens=$miens&miens_parc=$miens_parc&parc=$id_parc&liste_act_seq=$liste_act_seq&message=$mmsg_RessOk";
                    $lien = urlencode($lien);
                    echo "&nbsp;&nbsp;<A HREF=\"trace.php?link=$lien\"";
                    if ($avertir == 0)
                      echo bulle($mrc_ins_seq,"","LEFT","ABOVE",150).
                      "<IMG SRC=\"images/modules/tut_form/icosequen20.gif\" width='20' height='20' BORDER=0></A>";
                    else
                      echo bulle("$mrc_ins_ok $sup $mrc_dispo",$mrc_ress,"LEFT","ABOVE",250).
                      "<IMG SRC=\"images/modules/tut_form/icosequen20.gif\"  BORDER=0 ></A>";
               }
             }
             echo "</td>";
             if (($login == $init || $typ_user == "ADMINISTRATEUR") && $passe_ici != 1 && $favoris != 1 && $creat != 1)
             {
               $lien="recherche.php?lien_de_retour=$lien_de_retour&page=$page&flg=$flg&favoris=$favoris&categ=$categ&parcours=$parcours&id_parc=$id_parc&proprio=$proprio&refer=$refer&doublon=$doublon&rep=$rep&lien_sous_cat=0&modifier=1&parente=$parente&num=$num&id_ref=$id_ref&id_ref_parc=$id_ref_parc&id_seq=$id_seq&id_act=$id_act&modif_act=$modif_act&creat=$creat&miens=$miens&miens_parc=$miens_parc&parc=$id_parc&liste_act_seq=$liste_act_seq";
               $lien = urlencode($lien);
               echo "<td>&nbsp;&nbsp;<A HREF=\"trace.php?link=$lien\" ".
                     bulle($mrc_app_modif,"","LEFT","ABOVE",150).
                    "<IMG SRC=\"images/repertoire/icoGrenomfich20.gif\" BORDER=0></A></td>";
                 $act_suivi = mysql_query ("select * from activite where activite.act_ress_no = $num");
                 $Nb_act_suivi = mysql_num_rows ($act_suivi);
                 if ($Nb_act_suivi == 0)
                 {

                   $lien="recherche.php?lien_de_retour=$lien_de_retour&recherche=0&categ_modif=$categ&page=$page&flg=$flg&favoris=$favoris&parcours=$parcours&id_parc=$id_parc&proprio=$proprio&refer=$refer&detail=1&lien_sous_cat=1&parente=$parente&supprimer=1&num=$num&id_ref=$id_ref&id_ref_parc=$id_ref_parc&id_seq=$id_seq&id_act=$id_act&modif_act=$modif_act&creat=$creat&miens=$miens&miens_parc=$miens_parc&parc=$id_parc&liste_act_seq=$liste_act_seq";
                   $lien = urlencode($lien);
                   echo "<td>&nbsp;&nbsp;<A href=\"javascript:void(0);\" ".
                        "onclick=\"javascript:return(confm('trace.php?link=$lien'));\" ".
                        bulle($mrc_supp,"","LEFT","ABOVE",150).
                        "<IMG SRC=\"images/messagerie/icopoubelressour.gif\" align='bottom' BORDER=0></A></td>";
                 }
                 else
                 {
                   $nom_act = GetdataField ($connect,"select act_nom_lb from activite where act_ress_no=$num","act_nom_lb");
                   $lien_act = GetdataField ($connect,"select act_cdn from activite where act_ress_no=$num","act_cdn");
                   $nom_act_seq = GetdataField ($connect,"select seq_titre_lb from activite,sequence where activite.act_cdn=$lien_act AND activite.act_seq_no = sequence.seq_cdn","seq_titre_lb");
                   $lien = "act_ress.php?id_ress=$num";
                   $lien = urlencode($lien);
                   $label_aff = html_entity_decode("$lien_ress_act : $nom_act $act_seq_ofress : $nom_act_seq. $modif2supp",ENT_QUOTES,'iso-8859-1');
                   echo "<td>&nbsp;&nbsp;<A HREF=\"javascript:void(0);\" ".
                        "onclick=\"window.open('trace.php?link=$lien','','width=650,height=300,resizable=yes,status=no')\" ".
                        bulle($label_aff,"","LEFT","ABOVE",250)."<IMG SRC=\"images/repertoire/icoptiinterdit.gif\" border=0></A></td>";
                 }
                 if ($change_categorie == 1 && $num_change == $num && !isset($numero_cat))
                 {
                    $req_cat = mysql_query("select ress_cat_lb,ress_cdn from ressource_new where
                                           ress_titre = \"\" AND  ress_url_lb =\"\"
                                           GROUP BY ress_cat_lb
                                           ORDER BY ress_cat_lb");
                    echo "<form name=\"form\">";
                    echo "<td><SELECT name=\"select\" onChange=javascript:appel_w(form.select.options[selectedIndex].value)>";
                    echo "<OPTION>$categ</OPTION>";
                    $nb_req_cat = mysql_num_rows($req_cat);
                    $cat_cpt = 0;
                    while ($cat_cpt < $nb_req_cat)
                    {
                       $num_cat = mysql_result($req_cat,$cat_cpt,"ress_cdn");
                       $nom_cat = mysql_result($req_cat,$cat_cpt,"ress_cat_lb");
                       $carac_cat = strlen($nom_cat);
                       if ($carac_cat > 35)
                          $nom_cat1 = substr($nom_cat,0,33)."..";
                       else
                          $nom_cat1 = $nom_cat;
                       $lien="recherche.php?lien_de_retour=$lien_de_retour&change_categorie=1&num_change=$num&numero_cat=$num_cat&nom_cat=$nom_cat&flg=$flg&favoris=$favoris&parcours=$parcours&id_parc=$id_parc&proprio=$proprio&refer=$refer&doublon=$doublon&rep=$rep&lien_sous_cat=1&parente=$par1[$pr]&sous_catalogue=$sous_c[$pr]&cat=$cat&sous_cat=$sous_c[$pr]&niveau=1&id_ref=$id_ref&id_ref_parc=$id_ref_parc&id_seq=$id_seq&id_act=$id_act&modif_act=$modif_act&creat=$creat&miens=$miens&miens_parc=$miens_parc&parc=$id_parc&liste_act_seq=$liste_act_seq&page=$page&total_aff=$total_aff\"";
                       $lien = urlencode($lien);
                       echo "<OPTION value=\"trace.php?link=$lien\">$nom_cat1</OPTION>";
                       $cat_cpt++;
                    }
                    echo "</SELECT></td></FORM>";
                 }
                 else
                 {
                    $lien = "recherche.php?lien_de_retour=$lien_de_retour&change_categorie=1&num_change=$num&flg=$flg&favoris=$favoris&parcours=$parcours&id_parc=$id_parc&proprio=$proprio&refer=$refer&doublon=$doublon&rep=$rep&lien_sous_cat=1&parente=$par1[$pr]&sous_catalogue=$sous_c[$pr]&cat=$cat&sous_cat=$sous_c[$pr]&niveau=1&id_ref=$id_ref&id_ref_parc=$id_ref_parc&id_seq=$id_seq&id_act=$id_act&modif_act=$modif_act&creat=$creat&miens=$miens&miens_parc=$miens_parc&parc=$id_parc&liste_act_seq=$liste_act_seq&page=$page&total_aff=$total_aff";
                    $lien = urlencode($lien);
                    echo "<td>&nbsp;&nbsp;<A HREF=\"trace.php?link=$lien\" ".
                         bulle($mess_chg_cat,"","LEFT","ABOVE",250).
                         "<IMG SRC=\"images/aller_retour_haut.gif\" border=0></A></td>";
                 }
             }
             if (($login == $init || $typ_user == "ADMINISTRATEUR") && $passe_ici != 1 && $favoris != 1 && $creat != 1)
             {
               if ($cat_cpt > 0)
                   echo"</tr><tr><td colspan=4>$descrip</td></tr>";
               else
                   echo"</tr><tr><td colspan=5>$descrip</td></tr>";
             }
             else
                echo"</tr><tr><td>$descrip</td></tr>";
         }
         $passe_ici = 0;
         $l++;
         echo "</table>";
       }
     if ($total_aff > $nb_affiche)
     {
       echo "<TR bgcolor='#CEE6EC'><td align=left>$mess_admin_total : $total_aff  $aff_tot</td><td align=middle>";
       $nb_pages = ceil($total_aff/$nb_affiche);
       if ($page == 1)
       {
         echo "<IMG SRC=\"images/gauche_off.gif\" border=0></a>";
         echo "<IMG SRC=\"images/gauche_off.gif\" border=0></a>&nbsp;&nbsp;";
       }
       else
       {
         $lien = "recherche.php?lien_de_retour=$lien_de_retour&flg=$flg&favoris=$favoris&parcours=$parcours&id_parc=$id_parc&proprio=$proprio&refer=$refer&doublon=$doublon&rep=$rep&lien_sous_cat=1&parente=$par1[$pr]&sous_catalogue=$sous_c[$pr]&cat=$cat&sous_cat=$sous_c[$pr]&niveau=1&id_ref=$id_ref&id_ref_parc=$id_ref_parc&id_seq=$id_seq&id_act=$id_act&modif_act=$modif_act&creat=$creat&miens=$miens&miens_parc=$miens_parc&parc=$id_parc&liste_act_seq=$liste_act_seq&page=1&total_aff=$total_aff";
         $lien = urlencode($lien);
         echo "<a HREF=\"trace.php?link=$lien\"><IMG SRC=\"images/gauche_on.gif\" border=0></a>";
         echo "<a HREF=\"trace.php?link=$lien\"><IMG SRC=\"images/gauche_on.gif\" border=0></a>&nbsp;&nbsp;";
       }
       if ($page == 1)
       {
         echo "<IMG SRC=\"images/gauche_off.gif\" border=0></a>&nbsp;&nbsp;";
       }
       else
       {
         $page_avant = $page-1;
         $lien = "recherche.php?lien_de_retour=$lien_de_retour&flg=$flg&favoris=$favoris&parcours=$parcours&id_parc=$id_parc&proprio=$proprio&refer=$refer&doublon=$doublon&rep=$rep&lien_sous_cat=1&parente=$par1[$pr]&sous_catalogue=$sous_c[$pr]&cat=$cat&sous_cat=$sous_c[$pr]&niveau=1&id_ref=$id_ref&id_ref_parc=$id_ref_parc&id_seq=$id_seq&id_act=$id_act&modif_act=$modif_act&creat=$creat&miens=$miens&miens_parc=$miens_parc&parc=$id_parc&liste_act_seq=$liste_act_seq&page=$page_avant&total_aff=$total_aff";
         $lien = urlencode($lien);
         echo "<a HREF=\"trace.php?link=$lien\"><IMG SRC=\"images/gauche_on.gif\" border=0></a>&nbsp;&nbsp;";
       }
       for ($affichage = 1;$affichage <= $nb_pages;$affichage++)
       {
         if ($page == $affichage)
         {
           if ($nb_pages >15)
             echo "<FONT SIZE='1'> $affichage</FONT>";
           else
            echo " $affichage</font>";
         }
         else
         {
           $lien = "recherche.php?lien_de_retour=$lien_de_retour&flg=$flg&favoris=$favoris&parcours=$parcours&id_parc=$id_parc&proprio=$proprio&refer=$refer&doublon=$doublon&rep=$rep&lien_sous_cat=1&parente=$par1[$pr]&sous_catalogue=$sous_c[$pr]&cat=$cat&sous_cat=$sous_c[$pr]&niveau=1&id_ref=$id_ref&id_ref_parc=$id_ref_parc&id_seq=$id_seq&id_act=$id_act&modif_act=$modif_act&creat=$creat&miens=$miens&miens_parc=$miens_parc&parc=$id_parc&liste_act_seq=$liste_act_seq&page=$affichage&total_aff=$total_aff";
           $lien = urlencode($lien);
           if ($nb_pages > 15)
             echo "<a HREF=\"trace.php?link=$lien\"><FONT SIZE='1'> $affichage</FONT></a>";
           else
             echo "<a HREF=\"trace.php?link=$lien\"> $affichage</a>";
         }
         echo "&nbsp;&nbsp;";
       }
       if ($page == $nb_pages)
       {
         echo "<IMG SRC=\"images/droite_off.gif\" border=0></a>&nbsp;&nbsp;";
       }
       else
       {
         $page_suivant = $page+1;
         $lien = "recherche.php?lien_de_retour=$lien_de_retour&flg=$flg&favoris=$favoris&parcours=$parcours&id_parc=$id_parc&proprio=$proprio&refer=$refer&doublon=$doublon&rep=$rep&lien_sous_cat=1&parente=$par1[$pr]&sous_catalogue=$sous_c[$pr]&cat=$cat&sous_cat=$sous_c[$pr]&niveau=1&id_ref=$id_ref&id_ref_parc=$id_ref_parc&id_seq=$id_seq&id_act=$id_act&modif_act=$modif_act&creat=$creat&miens=$miens&miens_parc=$miens_parc&parc=$id_parc&liste_act_seq=$liste_act_seq&page=$page_suivant&total_aff=$total_aff";
         $lien = urlencode($lien);
         echo "<a HREF=\"trace.php?link=$lien\"><IMG SRC=\"images/droite_on.gif\" border=0></a>&nbsp;&nbsp;";
       }
       if ($page == $nb_pages)
       {
         echo "<IMG SRC=\"images/droite_off.gif\" border=0></a>";
         echo "<IMG SRC=\"images/droite_off.gif\" border=0></a>&nbsp;&nbsp;";
       }
       else
       {
         $lien = "recherche.php?lien_de_retour=$lien_de_retour&flg=$flg&favoris=$favoris&parcours=$parcours&id_parc=$id_parc&proprio=$proprio&refer=$refer&doublon=$doublon&rep=$rep&lien_sous_cat=1&parente=$par1[$pr]&sous_catalogue=$sous_c[$pr]&cat=$cat&sous_cat=$sous_c[$pr]&niveau=1&id_ref=$id_ref&id_ref_parc=$id_ref_parc&id_seq=$id_seq&id_act=$id_act&modif_act=$modif_act&creat=$creat&miens=$miens&miens_parc=$miens_parc&parc=$id_parc&liste_act_seq=$liste_act_seq&page=$nb_pages&total_aff=$total_aff";
         $lien = urlencode($lien);
         echo "<a HREF=\"trace.php?link=$lien\"><IMG SRC=\"images/droite_on.gif\" border=0></a>";
         echo "<a HREF=\"trace.php?link=$lien\"><IMG SRC=\"images/droite_on.gif\" border=0></a>&nbsp;&nbsp;";
       }
       echo"</td></tr>";
    }
    echo "</table></CENTER>";
//    if ($nbrt != 0)
//       echo"<hr size=2 width=100% align=center color=silver>";
//dey ici
//    if ((($typ_user == "FORMATEUR_REFERENT") || ($typ_user=="RESPONSABLE_FORMATION") || ($typ_user=="ADMINISTRATEUR")) && $ptr > 1 && $flg == 1 && $favoris != 1)
    if ((($typ_user == "FORMATEUR_REFERENT") || ($typ_user=="RESPONSABLE_FORMATION") || ($typ_user=="ADMINISTRATEUR")) && $flg == 1 && $favoris != 1)
    {
       $lien="recherche.php?page=$nb_pages&flg=$flg&doublon=$doublon&rep=$rep&lien_sous_cat=0&parente=$parente&ajouter=1";
       $lien = urlencode($lien);
       echo"<br />$bouton_gauche<A HREF=\"trace.php?link=$lien\">$mrc_ajout</A>$bouton_droite<br />";
    }
    $lien_sous_cat=0;
    echo "</td></tr></table></td></tr></table>";
}
// formulaire d'ajout d'un enregistrement soit à partir du moteur, soit provenant du bureau d'un formateur autorisé
if (($ajouter==1 || $modifier == 1) && !$inserer)
{
  ?>
  <SCRIPT language=JavaScript>
    function checkForm(frm) {
      var ErrMsg = "<?php echo $mess_info_no;?>\n";
      var lenInit = ErrMsg.length;
      if (isEmpty(frm.aut)==true)
        ErrMsg += ' - <?php echo $mrc_aut;?>\n';
      if (isEmpty(frm.tit)==true)
        ErrMsg += ' - <?php echo $mrc_tit_ress;?>\n';
      if (isEmpty(frm.desc)==true)
        ErrMsg += ' - <?php echo $mess_desc;?>\n';
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
}
if ($ajouter==1 && !$inserer)
{
  $ajouter=0;
  $sql_ajout=mysql_query("select ress_cat_lb,ress_typress_no from ressource_new where ress_cdn='$parente'");
  $res_sql=  mysql_num_rows($sql_ajout);
  $i=0;
  while ($i < $res_sql)
  {
    $categ_ajout= mysql_result($sql_ajout,$i,"ress_cat_lb");
    $par= mysql_result($sql_ajout,$i,"ress_typress_no");
    $i++;
  }
  echo "<CENTER>";
// Teste la provenance de la requête
  if ($rep != "" && (!isset($doublon) || (isset($doublon) && $doublon =='')))
  {
    echo" <Font SIZE='2'>$mrcadr_aut</FONT>";
    $repc=str_replace("%","&",$rep);
    $rep=$repc;
  }
  $form = '<FORM NAME="form1" ACTION="recherche.php" METHOD="POST" ENCTYPE="multipart/form-data">'.
          '<INPUT TYPE="HIDDEN" NAME="ajouter" VALUE=1>'.
          '<INPUT TYPE="HIDDEN" NAME="recherche" VALUE=0>'.
          '<INPUT TYPE="HIDDEN" NAME="inserer" VALUE=1>'.
          '<INPUT TYPE="HIDDEN" NAME="flg" VALUE="'.$flg.'">'.
          '<INPUT TYPE="HIDDEN" NAME="favoris" VALUE="'.$favoris.'">'.
          '<INPUT TYPE="HIDDEN" NAME="categ_ajout" VALUE="'.$categ_ajout.'">'.
          '<INPUT TYPE="HIDDEN" NAME="lien_sous_cat" VALUE=1>'.
          '<INPUT TYPE="HIDDEN" NAME="parente" VALUE="'.$parente.'">'.
          '<INPUT TYPE="HIDDEN" NAME="page" VALUE="'.$page.'">';
  echo $form;

if ($rep != "" && isset($doublon) && $doublon !='')
{
  $repc=str_replace("%","&",$rep);
  $rep=$repc;
  echo"<INPUT TYPE=HIDDEN NAME=rep VALUE=\"$rep\">";
  echo"<INPUT TYPE=HIDDEN NAME=doublon VALUE='2'>";
  echo"<INPUT TYPE=SUBMIT NAME=envoi VALUE=\"$mrc_conf_ajout\">";
  echo "</td></tr></table></td></tr></table>";
  exit;
}
echo "<table>";
if (isset($code) && $code > 0)
{
   $tit = GetDataField ($connect,"select titre_qcm from qcm_param where ordre='$code'","titre_qcm");
   if (isset($_POST['org']) && $_POST['org'] == 'qcm')
      $_SESSION['org'] = $org;

}
?>
<tr><td class='label_base'><?php  echo $mrc_tit_ress ;?></td>
<td><INPUT TYPE="TEXT" class='INPUT' NAME="tit" SIZE=60 VALUE="<?php  echo $tit ;?>">
</td></tr>
<tr><td class='label_base'><?php  echo $mrc_aut ;?></td><td>
<INPUT TYPE="TEXT" class='INPUT' NAME="aut" SIZE=60 VALUE=<?php  echo $aut;?>>
</td></tr>
<tr><td class='label_base' valign='top'><?php  echo $mrc_desc_ress ;?></td>
<td><TEXTAREA class='TEXTAREA' name="desc" cols=90 rows=10><?php  echo $desc ;?></textarea>
</td></tr>
<tr><td class='label_base'><?php  echo $mrc_publi ;?></td><td>
     <SELECT  name="pub" size="1">
         <OPTION value ="OUI"><?php  echo $mess_oui;?></OPTION>
         <OPTION value ="NON"><?php  echo $mess_non;?></OPTION>
     </SELECT>
</td></tr>
<tr><td class='label_base'><?php  echo $mrc_opr;?></td><td>
     <SELECT  name="but" size="1">
         <OPTION value ="ACCOMPAGNEMENT"><?php  echo $mrc_acc;?></OPTION>
         <OPTION value ="ACQUISITION"><?php  echo $mrc_acq ;?></OPTION>
         <OPTION value ="EVALUATION"><?php  echo $mrc_eva ;?></OPTION>
         <OPTION value ="INFORMATION"><?php  echo $mrc_inf ;?></OPTION>
         <OPTION value ="INITIATION"><?php  echo $mrc_ini ;?></OPTION>
         <OPTION value ="MULTIFONCTION"><?php  echo $mrc_mltf ;?></OPTION>
         <OPTION value ="PERFECTIONNEMENT"><?php  echo $mrc_perf ;?></OPTION>
         <OPTION value ="POSITIONNEMENT"><?php  echo $mrc_pos ;?></OPTION>
         <OPTION value ="SENSIBILISATION"><?php  echo $mrc_sens ;?></OPTION>
         <OPTION value ="COURS"><?php  echo $mrc_crs;?></OPTION>
         <OPTION value ="EXERCICE"><?php  echo $mrc_exe ;?></OPTION>
         <OPTION value ="ACTIVITES MULTIPLES"><?php  echo $mrc_actmul ;?></OPTION>
         <OPTION value ="INFORMATION COMPLEMENTAIRE"><?php  echo $mrc_infcomp ;?></OPTION>
         <OPTION value ="APPLICATION/TP"><?php  echo $mrc_aptp ;?></OPTION>
     </SELECT>
</td></tr>
<?php
if (!$rep)
{?>
<tr><td class='label_base'><?php  echo $mrc_sup_ress ;?></td><td>
     <SELECT  name="sup" size="1">
         <OPTION value ="Web"><?php  echo $mrc_web ;?></OPTION>
         <OPTION value ="Url"><?php  echo $mrc_url ;?></OPTION>
         <OPTION value ="Livre"><?php  echo $mrc_liv ;?></OPTION>
         <OPTION value ="BROCHURE"><?php  echo $mrc_broc ;?></OPTION>
         <OPTION value ="PERIODIQUE"><?php  echo $mrc_period ;?></OPTION>
         <OPTION value ="Vidéo"><?php  echo $mrc_video ;?></OPTION>
         <OPTION value ="AUDIO"><?php  echo $mrc_audio;?></OPTION>
         <OPTION value ="DIAPOSITIVES"><?php  echo $mrc_diapo ;?></OPTION>
         <OPTION value ="CD/DVD/Disquette"><?php  echo $mrc_cd_dvd ;?></OPTION>
         <OPTION value ="ANIMATION PEDAGOGIQUE"><?php  echo $mrc_anim ;?></OPTION>
         <OPTION value ="AUTRES"><?php  echo $mrc_autres ;?></OPTION>
     </SELECT>
</td></tr>
<?php
}
else
  echo "<INPUT TYPE='HIDDEN' NAME='sup' VALUE='Url'>";
echo "<INPUT TYPE='HIDDEN' NAME='niv' VALUE='1'>";
if (!isset($code) || (isset($code) && $code == 0))
{
  echo '<tr><td class="label_base">'.$mrc_doub.'</td>
          <td><SELECT  name="doublon" size="1">
            <OPTION VALUE="NON">'.$mess_non.'</OPTION>
            <OPTION VALUE="OUI">'.$mess_oui.'</OPTION>
          </SELECT>
        </td></tr>';
}
if ($rep != "" && !$doublon)
{
  echo"<tr><td></td>";
  echo"<td><INPUT TYPE=HIDDEN NAME=rl value=\"$rep\"></td></tr>";
}
else
{
  echo"<tr><td class='label_base'>$mrc_ins_adr</td><td>";
  echo"<INPUT TYPE=TEXT NAME='rl' SIZE=80></td></tr>";
}
if ($rep == "")
{
  echo"<tr><td align='middle' colspan=2> OU </td></tr>";
  echo "<tr><td class='label_base'>";
  echo "$mess_cas_tel_fic</td><td>";
  echo "<INPUT TYPE='HIDDEN' NAME='objet' VALUE='telecharger'>";
  echo "<INPUT type='file' name='userfile' size='60' enctype=\"multipart/form-data\"></td></tr>";
//  echo "<INPUT type='hidden' name='MAX_FILE_SIZE' value='500000'>";
}
   echo "<TR height='40'><td>".
        "<A HREF=\"javascript:history.back();\" style=\"margin-left:10px;margin-bottom:10px;\" ".
        "onmouseover=\"img_annonce.src='images/fiche_identite/boutretourb.gif';return true;\" ".
        "onmouseout=\"img_annonce.src='images/fiche_identite/boutretour.gif'\">";
   echo "<IMG NAME=\"img_annonce\" SRC=\"images/fiche_identite/boutretour.gif\" BORDER='0' ".
        "onLoad=\"tempImg=new Image(0,0); tempImg.src='images/fiche_identite/boutretourb.gif'\"></A></td>";
   echo "<td><A HREF=\"javascript:checkForm(document.form1);\" style=\"margin-bottom:10px;\" ".
        "onClick=\"TinyMCE.prototype.triggerSave();\" ".
        "onmouseover=\"img1.src='images/fiche_identite/boutvalidb.gif';return true;\" ".
        "onmouseout=\"img1.src='images/fiche_identite/boutvalid.gif'\">".
       "<IMG NAME=\"img1\" SRC=\"images/fiche_identite/boutvalid.gif\" BORDER='0' ".
       "onLoad=\"tempImg=new Image(0,0); tempImg.src='images/fiche_identite/boutvalidb.gif'\"></A>";
echo "</td></tr></FORM></table>";
echo '<div id="mien" class="cms"></div>';
echo "</td></tr></table></td></tr></table>";
exit;
}
// formulaire de modification
if ($modifier==1 && !$inserer)
{
  $modifier=0;
  $sql_modif=mysql_query("select * from ressource_new where ress_cdn=$num");
  $res_sql=  mysql_num_rows($sql_modif);
  $i=0;
  while ($i<$res_sql)
  {
    $categ_modif= mysql_result($sql_modif,$i,"ress_cat_lb");
    $num= mysql_result($sql_modif,$i,"ress_cdn");
    $par= mysql_result($sql_modif,$i,"ress_typress_no");
    $lien= mysql_result($sql_modif,$i,"ress_url_lb");
    $aut= mysql_result($sql_modif,$i,"ress_auteurs_cmt");
    $pub= mysql_result($sql_modif,$i,"ress_publique_on");
    $tit= mysql_result($sql_modif,$i,"ress_titre");
    $desc= mysql_result($sql_modif,$i,"ress_desc_cmt");
    $publicc= mysql_result($sql_modif,$i,"ress_public_no");
    $sup= mysql_result($sql_modif,$i,"ress_support");
    $niv= mysql_result($sql_modif,$i,"ress_niveau");
    $but= mysql_result($sql_modif,$i,"ress_type");

  $i++;
  }
  ?>
  <FORM NAME="form1" ACTION="recherche.php" METHOD="POST" target="main" ENCTYPE="multipart/form-data">
  <INPUT TYPE="HIDDEN" NAME="modifier" VALUE=1>
  <INPUT TYPE="HIDDEN" NAME="favoris" VALUE=<?php  echo"\"$favoris\"";?>>
  <INPUT TYPE="HIDDEN" NAME="flg" VALUE=<?php  echo"\"$flg\"";?>>
  <INPUT TYPE="HIDDEN" NAME="num" VALUE=<?php  echo"\"$num\"";?>>
  <INPUT TYPE="HIDDEN" NAME="inserer" VALUE=1>
  <INPUT TYPE="HIDDEN" NAME="categ_modif" VALUE=<?php  echo"\"$categ_modif\"";?>>
  <INPUT TYPE="HIDDEN" NAME="cat" VALUE=<?php  echo"\"$categ_modif\"";?>>
  <INPUT TYPE="HIDDEN" NAME="lien_sous_cat" VALUE=1>
  <INPUT TYPE="HIDDEN" NAME="parente" VALUE=<?php  echo"\"$parente\"";?>>
  <INPUT TYPE="HIDDEN" NAME="page" VALUE=<?php  echo $page;?>>
  <INPUT TYPE="HIDDEN" NAME="mess_notif" VALUE=<?php  echo "\"$mmsg_RessOk\"";?>>
  <CENTER>
  <table>
  <tr><td class='label_base'><?php  echo $mrc_tit_ress ;?></td>
  <td><INPUT TYPE="TEXT" class='INPUT' NAME="tit" SIZE=80 VALUE=<?php  echo"\"$tit\"";?>>
  </td></tr>
  <tr><td class='label_base'><?php  echo $mrc_aut ;?></td><td>
  <INPUT TYPE="TEXT" class='INPUT' NAME="aut" SIZE=80 VALUE=<?php  echo"\"$aut\"";?>>
  </td></tr>
  <tr><td class='label_base' valign='top'><?php  echo $mrc_desc_ress ;?></td>
  <td><TEXTAREA class='TEXTAREA' style="font-size:11px; font-family: arial;" name=desc cols=90 rows=10><?php  echo "$desc";?></textarea>
  </td></tr>
  <tr><td class='label_base'><?php  echo $mrc_publi ;?></td><td>
     <SELECT  name="pub" size="1">
         <OPTION selected><?php  echo $pub ?></OPTION>
         <OPTION value ="OUI"><?php  echo $mess_oui;?></OPTION>
         <OPTION value ="NON"><?php  echo $mess_non;?></OPTION>
     </SELECT>
  </td></tr>
  <tr><td class='label_base'><?php  echo $mrc_opr;?></td><td>
     <SELECT  name="but" size="1">
         <OPTION selected><?php  echo $but ?></OPTION>
         <OPTION value ="ACCOMPAGNEMENT"><?php  echo $mrc_acc;?></OPTION>
         <OPTION value ="ACQUISITION"><?php  echo $mrc_acq ;?></OPTION>
         <OPTION value ="EVALUATION"><?php  echo $mrc_eva ;?></OPTION>
         <OPTION value ="INFORMATION"><?php  echo $mrc_inf ;?></OPTION>
         <OPTION value ="INITIATION"><?php  echo $mrc_ini ;?></OPTION>
         <OPTION value ="MULTIFONCTION"><?php  echo $mrc_mltf ;?></OPTION>
         <OPTION value ="PERFECTIONNEMENT"><?php  echo $mrc_perf ;?></OPTION>
         <OPTION value ="POSITIONNEMENT"><?php  echo $mrc_pos ;?></OPTION>
         <OPTION value ="SENSIBILISATION"><?php  echo $mrc_sens ;?></OPTION>
         <OPTION value ="COURS"><?php  echo $mrc_crs;?></OPTION>
         <OPTION value ="EXERCICE"><?php  echo $mrc_exe ;?></OPTION>
         <OPTION value ="ACTIVITES MULTIPLES"><?php  echo $mrc_actmul ;?></OPTION>
         <OPTION value ="INFORMATION COMPLEMENTAIRE"><?php  echo $mrc_infcomp ;?></OPTION>
         <OPTION value ="APPLICATION/TP"><?php  echo $mrc_aptp ;?></OPTION>
     </SELECT>
  </td></tr>
  <?php
  if ($categ_modif == 'xApi TinCan')
  {
     echo '<INPUT TYPE="HIDDEN" NAME="sup" VALUE="Url">';
     echo '<INPUT TYPE="HIDDEN" NAME="rl" VALUE="'.$lien.'">';
  }
  else
  {
    ?>
    <tr><td class='label_base' valign='top'><?php  echo $mrc_sup_ress ;?></td><td>
     <SELECT  name="sup" size="1">
         <OPTION selected><?php  echo $sup;?></OPTION>
         <OPTION value ="Web"><?php  echo $mrc_web ;?></OPTION>
         <OPTION value ="Url"><?php  echo $mrc_url ;?></OPTION>
         <OPTION value ="Livre"><?php  echo $mrc_liv ;?></OPTION>
         <OPTION value ="BROCHURE"><?php  echo $mrc_broc ;?></OPTION>
         <OPTION value ="PERIODIQUE"><?php  echo $mrc_period ;?></OPTION>
         <OPTION value ="Vidéo"><?php  echo $mrc_video ;?></OPTION>
         <OPTION value ="AUDIO"><?php  echo $mrc_audio;?></OPTION>
         <OPTION value ="DIAPOSITIVES"><?php  echo $mrc_diapo ;?></OPTION>
         <OPTION value ="CD/DVD/Disquette"><?php  echo $mrc_cd_dvd ;?></OPTION>
         <OPTION value ="ANIMATION PEDAGOGIQUE"><?php  echo $mrc_anim ;?></OPTION>
         <OPTION value ="AUTRES"><?php  echo $mrc_autres ;?></OPTION>
     </SELECT>
    </td></tr>
    <?php
  }
  if ($categ_modif == 'xApi TinCan')
        echo "<tr><td valign='bottom' ><div id='lbl_fic' ".
          "style='display:none;text-align: right;font-weight:bold;'>$mess_file'</div></td>";
  elseif ($categ_modif != 'xApi TinCan' && $recharge != 1)
  {
  ?>
  <tr><td valign='bottom' ><div id='lbl_fic' style="display:none;text-align: right;font-weight:bold;"><?php echo $mess_file;?></div></td>
  <td>
    <div id='retour' style="display:none"
        onClick = "javascript:
            $(document).ready(function() {
              $('#recharge').show();
              $('#lbl_rl').show();
              $('#ipt_rl').show();
              $('#lbl_fic').hide();
              $('#inputer').hide();
              $('#retour').hide();
            })">

     <?php echo "$bouton_gauche<a href='javascript:void(0);'>$messRes_Url</a>$bouton_droite<strong> ou $mrc_upload_autres :</strong>";?>
  </div>
    <div id='inputer' style="display:none"><INPUT type="file" class="INPUT" name="userfile" size="60">
    </div>
    <div id='recharge'
       onClick = "javascript:
            $(document).ready(function() {
             $('#recharge').hide();
             $('#lbl_rl').hide();
             $('#ipt_rl').hide();
             $('#lbl_fic').show();
             $('#inputer').show();
             $('#retour').show();
            })">


  <?php
  echo "$bouton_gauche<a href='javascript:void(0);'>$mrc_upload_autres</a>$bouton_droite<strong> ou $messRes_Url :</strong></div>";
  echo "</td></tr>";
  echo "<tr><td class='label_base'><div id='lbl_rl'>$mrc_ins_adr</div></td>";
  echo "<td><div id='ipt_rl'><INPUT TYPE='text' class='INPUT' NAME='rl'  SIZE='80' VALUE=\"$lien\"></div></td>";
  echo "</tr>";

  }

  echo "<INPUT TYPE='HIDDEN' NAME='niv' VALUE='1'>";
   echo "<TR height='40'><td>".
        "<A HREF=\"javascript:history.back();\" style=\"margin-left:10px;margin-bottom:10px;\" ".
        "onmouseover=\"img_annonce.src='images/fiche_identite/boutretourb.gif';return true;\" ".
        "onmouseout=\"img_annonce.src='images/fiche_identite/boutretour.gif'\">";
   echo "<IMG NAME=\"img_annonce\" SRC=\"images/fiche_identite/boutretour.gif\" BORDER='0' ".
        "onLoad=\"tempImg=new Image(0,0); tempImg.src='images/fiche_identite/boutretourb.gif'\"></A></td>";
   echo "<td><A HREF=\"javascript:checkForm(document.form1);\" style=\"margin-bottom:10px;\" ".
        "onClick=\"TinyMCE.prototype.triggerSave();\" ".
        "onmouseover=\"img1.src='images/fiche_identite/boutvalidb.gif';return true;\" ".
        "onmouseout=\"img1.src='images/fiche_identite/boutvalid.gif'\">".
       "<IMG NAME=\"img1\" SRC=\"images/fiche_identite/boutvalid.gif\" BORDER='0' ".
       "onLoad=\"tempImg=new Image(0,0); tempImg.src='images/fiche_identite/boutvalidb.gif'\"></A>";
    echo "</td></tr></table></FORM></td></tr></table></td></tr></table></BODY></HTML>";
   exit;
}
function getextension($fichier){
 $bouts = explode(".", $fichier);
  return array(array_pop($bouts), implode(".", $bouts));
  exit;
}
?>
</BODY></HTML>
