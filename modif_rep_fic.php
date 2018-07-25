<?php
if (!isset($_SESSION)) session_start();
if (!isset($_SESSION['id_user']) || $_SESSION['id_user'] == "")
{
  exit();
}
require "admin.inc.php";
include ("include/UrlParam2PhpVar.inc.php");
require 'fonction.inc.php';
require "lang$lg.inc.php";
require 'fonction_html.inc.php';
dbConnect();
//include ("click_droit.txt");

if (isset($fermer) && $fermer == 1)
{
   echo "<script language=\"JavaScript\">";
   echo "setTimeout(\"Quit()\",800);
        function Quit() {
          self.opener=null;self.close();return false;
        }
        </SCRIPT>";
   exit();
}
$agent=$_SERVER["HTTP_USER_AGENT"];
if (strstr(strtolower($agent),"mac") || strstr(strtolower($agent),"konqueror") || strstr(strtolower($agent),"safari") || strtolower(strstr(getenv("HTTP_USER_AGENT"),"chrome")))
  $mac=1;
$date_insert = date("Y-m-d H:i:s");
//Affichage initial
if (!isset($parent) && isset($base))
   $parent = $base;
if (isset($communes_groupe) && $communes_groupe == 1 && $typ_user == "APPRENANT")
{
   $req_grp = mysql_query("select utilgr_groupe_no from utilisateur_groupe where utilgr_utilisateur_no = $id_user ");
   $nomb_grp = mysql_num_rows($req_grp);
   if ($nomb_grp > 1)
   {
      $id_grp = $numero_groupe;
      $nom_grp =GetDataField ($connect,"select grp_nom_lb from groupe where grp_cdn = $id_grp","grp_nom_lb");
      $titre = "$mess_menu_casier_groupe $nom_grp";
      $message="";
   }
   elseif ($nomb_grp == 1)
   {
      $id_grp = mysql_result($req_grp,0,"utilgr_groupe_no");
      $nom_grp =GetDataField ($connect,"select grp_nom_lb from groupe where grp_cdn = $id_grp","grp_nom_lb");
      $titre = "$mess_menu_casier_groupe $nom_grp";
      $message="";
   }
   else
   {
      $lien = "vide.php?ret=accueil&titre=$mess_casier_rep_source&contenu=$mess_cas_no_grp";
      $lien = urlencode($lien);
      echo "<script language=\"JavaScript\">";
      echo "document.location.replace(\"trace.php?link=$lien\")";
      echo "</script>";
      exit();
   }
   $dir="ressources/groupes/".$id_grp;
}
elseif (isset($communes_groupe) && $communes_groupe == 1 && $typ_user == "TUTEUR")
{
    if ($numero_groupe > 0)
       $id_grp = $numero_groupe;
    $dir="ressources/groupes/".$id_grp;
    $nom_grp =GetDataField ($connect,"select grp_nom_lb from groupe where grp_cdn = $id_grp","grp_nom_lb");
    $titre = "$mess_menu_casier_groupe $nom_grp";
    $message="";
}
elseif (isset($communes_groupe) && $communes_groupe == 1 && $typ_user != "APPRENANT" && $typ_user != "TUTEUR" && ((isset($tous) && $tous != 1) || !isset($tous)))
{
    $dir="ressources/groupes/$id_grp";
    $titre = $mess_cas_ech_grp;
}
elseif (isset($communes_groupe) && $communes_groupe == 1 && $typ_user != "APPRENANT" && $typ_user != "TUTEUR" && $tous == 1)
{
    $dir="ressources/groupes";
    $titre = $mess_cas_ech_grp;
}
elseif ($typ_user == "TUTEUR" && (!isset($communes_groupe) || (isset($communes_groupe) && $communes_groupe != 1)) && $formateurs != 1)
{
    $dir="ressources/".$login."_".$id_user."/ressources";
    $titre = $mess_casier_rep_source;
}
elseif (isset($formateurs) && $formateurs == 1)
{
    $dir="ressources/formateurs";
    $titre = $mess_cas_ech_form;
}
else
{
    $dir="ressources/".$login."_".$id_user;
    $titre = $mess_casier_rep_source;
}

if (isset($dir) && !is_dir($dir))
{
   include 'style.inc.php';
   $mess_notif = "<h1><font color='red'>le répertoire auquel vous voulez accéder n'existe pas</font></h1>".
   "<p>Adressez-vous à votre administrateur ou à votre responsable de formation</br>".
   "afin qu'il contacte de service de maintenance</p>";
   echo notifier($mess_notif);
   ?>
   <script type="text/javascript">
      setTimeout(function() {history.go(-1);},5000);
   </script>
   <?php
   echo '<div id="mien" class="cms"></div>';
   exit;
}
$mon_dossier = $login."_".$id_user;
if (isset($parent) && isset($fichier) && $fichier == $mon_dossier)
   $titre = $mess_casier_rep_source;
if (isset($parent) && isset($nom_grp))
   $titre = $nom_grp;
// teste l'existence du casier ou bureau virtuel de l'utilisateur
$pere = opendir("ressources");
$casier=$login."_".$id_user;
while ($file = readdir($pere))
{
   if ($file == $casier)
   {
      $existe=1;
      break;
   }
   else
      $existe=0;
}
// fin du test du casier

// Casier tel qu'il apparaît au lancement
if (isset($communes_groupe) && $communes_groupe == 1 && $typ_user == "APPRENANT")
   $base="ressources/groupes/".$id_grp;
elseif (isset($communes_groupe) && $communes_groupe == 1 && $typ_user == "TUTEUR")
   $base="ressources/groupes";
elseif (isset($communes_groupe) && $communes_groupe == 1 && $typ_user != "APPRENANT" && $typ_user != "TUTEUR")
   $base="ressources/groupes";
elseif (isset($formateurs) && $formateurs == 1 && ($typ_user != "APPRENANT"))
   $base="ressources/formateurs";
else
   $base="ressources/".$login."_".$id_user;
if (!isset($direct))
{
   include 'style.inc.php';
   $req_presc = mysql_query("SELECT COUNT(*) from utilisateur_groupe where utilgr_utilisateur_no = $id_user");
   $nb_presc = mysql_result($req_presc,0);
   if (!$login || !$id_user || $existe == 0 || ($nb_presc == 0 && $typ_user == "APPRENANT"))
   {
      entete_simple_rep($mess_casier_rep_source);
      echo "<TR><TD><CENTER>&nbsp;<P><FONT SIZE='2'>$mess_casier_nocasier</FONT><P>&nbsp;";
      echo fin_tableau('');
      exit;
   }
   $telecharge=0;
   $parent=$dir;
   $mess_notif =  str_replace("%20"," ",$message);
   if ($dir == $base){
      $titre =str_replace("devoirs",$mess_trx_rendus,$titre);
      $titre =str_replace("ressources",$mess_mes_docs,$titre);
   }
   entete_simple_rep($titre);
   echo "<TR><TD width=100% colspan='2'><table width=100% cellspacing='1' cellpadding='3' border='0'>";
   list_dir();
   echo fin_tableau('');
   exit;
}

// Si on vient d'une navigation au sein de l'arborescence
if (isset($direct) && $direct == "dossier" && !isset($flag) && ((isset($objet) && $objet != "supprimer" && $objet != "supp_lot") || !isset($objet)) )
{
     if (isset($parent) && isset($id_grp) && $id_grp > 0)
     {
         $nom_grp =GetDataField ($connect,"select grp_nom_lb from groupe where grp_cdn = $id_grp","grp_nom_lb");
         $fichier = "$mess_menu_casier_groupe $nom_grp";
     }
     include "style.inc.php";
     $dir = $sousdos;
     if ($dir == $base || $dir == "$base/ressources" || $dir == "$base/devoirs")
     {
        $fichier =str_replace("devoirs",$mess_trx_rendus,$fichier);
        $fichier =str_replace("ressources",$mess_mes_docs,$fichier);
     }
     if ($typ_user == "APPRENANT" && strstr($dir,"$base/devoirs/"))
     {
        $nom_grp = GetDataField ($connect,"select grp_nom_lb from groupe where grp_cdn = $fichier","grp_nom_lb");
        $fichier = $nom_grp;
     }
     if ($typ_user != "APPRENANT" && strstr($dir,"$base/devoirs/") && strstr($parent,"--"))
     {
        $der_doss = substr(strrchr($parent,"/"),1);
        $item = explode("--",$der_doss);
        $requete = mysql_query("SELECT count(*) from utilisateur where util_typutil_lb = 'APPRENANT' AND util_cdn = '$item[1]' AND util_login_lb = '$item[0]'");
        $nb_count = mysql_result($requete,0);
        if ($nb_count == 1)
        {
          $nom_grp = GetDataField ($connect,"select grp_nom_lb from groupe where grp_cdn = $fichier","grp_nom_lb");
          $fichier = $nom_grp;
        }
     }
     if ($typ_user != "APPRENANT" && strstr($dir,"$base/devoirs/") && strstr($fichier,"--"))
     {
        $item = explode("--",$fichier);
        $requete = mysql_query("SELECT count(*) from utilisateur where util_typutil_lb = 'APPRENANT' AND util_cdn = '$item[1]' AND util_login_lb = '$item[0]'");
        $nb_count = mysql_result($requete,0);
        if ($nb_count == 1)
        {
          $nom = GetDataField ($connect,"select util_nom_lb from utilisateur where util_typutil_lb = 'APPRENANT' AND util_cdn = '$item[1]' AND util_login_lb = '$item[0]'","util_nom_lb");
          $prenom = GetDataField ($connect,"select util_prenom_lb from utilisateur where util_typutil_lb = 'APPRENANT' AND util_cdn = '$item[1]' AND util_login_lb = '$item[0]'","util_prenom_lb");
          $fichier = $nom." ".$prenom;
        }
     }
     if ($parent && $id_grp)
     {
         $nom_grp =GetDataField ($connect,"select grp_nom_lb from groupe where grp_cdn = $id_grp","grp_nom_lb");
         $fichier = "$nom_grp";
     }
     $lien = "taille.php?qui=dossier&ou=$dir";
     $lien = urlencode($lien);
     entete_simple_rep($fichier);
     echo "<TR><TD width=100% colspan='2'><table width=100% cellspacing='1' cellpadding='3' border='0' border='0'>";
     list_dir();
     echo fin_tableau('');
     exit;
}
elseif (isset($direct) && $direct == "dossier" && isset($flag))
{
     include "style.inc.php";
     if (isset($parent) && $parent=="ressources")
        $parent=$base;
     $mon_dossier = $login."_".$id_user;
     if (isset($fichier) && $fichier == $mon_dossier)
        $fichier=$titre;
     $dir=$parent;
     if (isset($dir) && isset($base) && $dir == $base || $dir=="$base/ressources" || $dir=="$base/devoirs")
     {
        $fichier =str_replace("devoirs",$mess_trx_rendus,$fichier);
        $fichier =str_replace("ressources",$mess_mes_docs,$fichier);
     }
     if (isset($parent) && $parent && isset($flag) && $flag == 1 && isset($id_grp) &&
        isset($communes_groupe) && $communes_groupe == 1 && $dir != $base)
     {
        $nom_grp =GetDataField ($connect,"select grp_nom_lb from groupe where grp_cdn = $id_grp","grp_nom_lb");
        $fichier = "$mess_menu_casier_groupe $nom_grp";
     }
     if (isset($parent) && $parent == "ressources/$login"."_$id_user/ressources")
        $fichier =$mess_mes_docs;
     if (isset($formateurs) && $formateurs == 1 && isset($flag) && $flag == 1)
        $fichier = $mess_cas_ech_form;
     if (isset($communes_groupe) && $communes_groupe == 1 && isset($flag) && $flag == 1 && isset($dir) && isset($base) && $dir == $base)
        $fichier = $mess_cas_ech_grp;

     $lien = "taille.php?qui=dossier&ou=$dir";
     $lien = urlencode($lien);
     entete_simple_rep($titre);
     echo "<TR><TD width=100% colspan='2'><table width=100% cellspacing='1' cellpadding='3' border='0' border=1>";
     list_dir();
     echo "</TD></TR></TABLE>";
   $flag=0;
exit;
}
$rallonge_test = $rallonge;
$directory = $rallonge;
if (strstr($rallonge,"\\\\"))
   $le_slash  = "\\\\";
elseif (strstr($rallonge,"\\"))
   $le_slash  = "\\";
else
   $le_slash  = "|||";
if (isset($objet) && $objet == "mouver")
{
   include 'style.inc.php';
   if (strstr($rallonge,"Ressources_Scorm") || strstr($rallonge,"Ressources_Aicc"))
      $aff_mouve = "Les ressources aux standards Scorm ou Aicc ne sont pas déplaçables";
   else{
     if (!isset($lien_origine) || (isset($lien_origine) && $lien_origine == ''))
     {
        $lien_origine = $directory.$fic;
        $_SESSION['lien_origine'] = $lien_origine;
        if (isset($lien_origine))
           $aff_mouve = $mess_depl_arbo;
        else
           $aff_mouve = "";
        $_SESSION['aff_mouve'] = $aff_mouve;
     }
   }
   $dir=dirname($rallonge);
   $parent=$base;
   entete_simple_rep($titre);
   echo "<TR><TD width=100% colspan='2'><table width=100% cellspacing='1' cellpadding='3' border='0'>";
   list_dir();
   echo fin_tableau('');
   exit;
}

if (isset($objet) && $objet == "fin_mouve")
{
   include 'style.inc.php';
     $source_file = $lien_origine;
     $liste_fichier = explode("/",$source_file);
     $nb_liste = count($liste_fichier);
     $fichier = $liste_fichier[$nb_liste-1];
     $dest_file=$directory."/".$fichier;
     if ($source_file != $dest_file)
     {
        $copier=copy($source_file , $dest_file);
        chmod ($dest_file,0777);
        unlink($lien_origine);
     }
     unset($_SESSION['lien_origine']);
     unset($_SESSION['aff_mouve']);
     $aff_mouve = $mess_depl_ok;
     $lien_ress = str_replace($repertoire,"",str_replace("\\","/",$lien_origine));
     $lien_ress_http = $adresse_http."/".$lien_ress;
     $aremplacer = $liste_fichier[0]."/".$liste_fichier[1]."/".$liste_fichier[2]."/".$liste_fichier[3]."/";
     $nom_ancien = str_replace("$aremplacer","","$lien_origine");
     $nom_nouveau = str_replace("$aremplacer","","$dest_file");
     if ((strstr($nom_nouveau,'ressources/formateurs') || strstr($nom_nouveau,'ressources/groupes')) && (strstr($nom_ancien,'ressources/formateurs') || strstr($nom_ancien,'ressources/groupes')))
        $req_echg = mysql_query("UPDATE echange_grp SET ech_path_lb = \"$nom_nouveau\" WHERE ech_path_lb = \"$nom_ancien\"");
     if ((strstr($nom_nouveau,'ressources/formateurs') || strstr($nom_nouveau,'ressources/groupes')) && !strstr($nom_ancien,'ressources/formateurs') && !strstr($nom_ancien,'ressources/groupes'))
     {
         $id_max = Donne_ID ($connect,"select max(ech_cdn) from echange_grp");
         if ($communes_groupe == 1)
            $requete = mysql_query("INSERT INTO echange_grp (ech_cdn,ech_path_lb,ech_grp_no,ech_auteur_no,ech_date_dt) VALUES ('$id_max',\"$nom_nouveau\",'$id_grp','$id_user','$date_insert')");
         elseif ($formateurs == 1)
            $requete = mysql_query("INSERT INTO echange_grp (ech_cdn,ech_path_lb,ech_grp_no,ech_auteur_no,ech_date_dt) VALUES ('$id_max',\"$nom_nouveau\",0,'$id_user','$date_insert')");
     }elseif (!strstr($nom_nouveau,'ressources/formateurs') && !strstr($nom_nouveau,'ressources/groupes') && (strstr($nom_ancien,'ressources/formateurs') || strstr($nom_ancien,'ressources/groupes')))
            $requete = mysql_query("DELETE FROM echange_grp WHERE ech_path_lb = \"$nom_ancien\"");
     $new_file = str_replace($repertoire,"",str_replace("\\","/",$dest_file));
     $new_file_http = $adresse_http."/".$new_file;
     $sql=mysql_query("SELECT ress_cdn,ress_url_lb FROM ressource_new WHERE ress_url_lb=\"$lien_ress\" OR ress_url_lb=\"$lien_ress_http\"");
     $nbr_result=mysql_num_rows($sql);
     if ($nbr_result > 0){
        for($i=0;$i<$nbr_result;$i++){
           $id_ress = mysql_result($sql,$i,"ress_cdn");
           $url = mysql_result($sql,$i,"ress_url_lb");
           $linker = ($url == $lien_ress) ? $new_file : $new_file_http;
           $repare = mysql_query("UPDATE ressource_new SET ress_url_lb = \"$linker\" WHERE ress_cdn = $id_ress");
       }
     }
   $lien_origine = '';
   $dir=dirname($rallonge);
   $parent=$base;
   entete_simple_rep($titre);
   echo "<TR><TD width=100% colspan='2'><table width=100% cellspacing='1' cellpadding='3' border='0'>";
   list_dir();
   echo fin_tableau('');
   exit;
}
if (isset($objet) && $objet == "zipper")
{
   include 'style.inc.php';
   include_once("class/archive.inc.php");
   $fichier = $directory;
   if ($direct_zip != "mon_dossier")
   {
     list($extension, $nom) = getextension($fic);
     $ziper = new zip_file($nom.".zip");
     $ziper->set_options(array('basedir'=>$fichier));
     $ziper->add_files($fic);
     $ziper->create_archive();
     $parent=$base;
     $dir=$dos;
   }
   else
   {
     $test = new zip_file("../".$nom_dossier.".zip");
     $test->set_options(array('basedir'=>$fichier));
     $handle=opendir($fichier);
     while ($fiche = readdir($handle))
     {
       if ($fiche != '.' && $fiche != '..')
          $test->add_files("$fiche");
     }
     $test->create_archive();
     closedir($handle);
     $dir=dirname($rallonge);
     $parent=$base;
   }
   if ($dir == $base || $dir=="$base/ressources" || $dir=="$base/devoirs")
   {
     $titre =str_replace("devoirs",$mess_trx_rendus,$titre);
     $titre =str_replace("ressources",$mess_mes_docs,$titre);
   }
   entete_simple($titre);
   echo "<TR><TD><table width=100%>";
   list_dir();
   echo fin_tableau('');
   exit;
}
if (isset($objet) && $objet == "dezipper")
{
   list($extension, $nom) = getextension($fic);
   if ($extension == 'zip')
   {
     $corrompu = 0;$content = '';
     $fichier = $directory.$fic;
     require_once('class/pclzip.inc.php');
     $archive = new PclZip($fichier);
     if (($list_zip = $archive->listContent()) == 0)
     {
        die("Error : ".$archive->errorInfo(true));
     }
     for ($i=0; $i < sizeof($list_zip); $i++)
     {
        list($extension,$nom)=getextension($list_zip[$i]["filename"]);
        if (strstr($list_zip[$i]["filename"],"deb") || strstr($list_zip[$i]["filename"],"'") || strstr($list_zip[$i]["filename"],",") || strstr($list_zip[$i]["filename"],"é"))
        {
           $corrompu++;
           $content .= $list_zip[$i]["filename"]." <br /> ";
        }
        if (in_array(strtolower($extension), array("exe","sh","py", "ida","idq","asp","cer","cdx","asa","idc","cfm","dbm","php","php4","inc","shtml","cgi")))
        {
           $corrompu++;
           $content .= $list_zip[$i]["filename"]." : extension du fichier non autorisée. Demandez à votre administrateur de l'insérer en FTP<br /> ";
        }
     }
     if (isset($corrompu) && $corrompu > 0)
     {
        include 'style.inc.php';
        $message = " Attention : Le nom de ce(s) fichier(s) est(sont) corrompu(s). <br />".
                   "Peut-être un problème de nommage des fichiers : ni espace ni accent etc.. <br />";
        $mess_notif = $message.'<br />'.$content;
        $parent=$base;
        $dir=$dos;
        if ($dir == $base || $dir=="$base/ressources" || $dir=="$base/devoirs")
        {
          $titre = str_replace("devoirs",$mess_trx_rendus,$titre);
          $titre = str_replace("ressources",$mess_mes_docs,$titre);
        }
        entete_simple_rep($titre);
        echo "<TR><TD width=100% colspan='2'><table width=100% cellspacing='1' cellpadding='3' border='0'>";
        list_dir();
        echo fin_tableau('');
        exit;
     }
     if (!file_exists($directory.$nom))
     {
        mkdir($directory.$nom,0777);
        chmod ($directory.$nom,0777);
     }
     $lerepertoire = $directory."$nom/";
     $list = $archive->extract(PCLZIP_OPT_PATH,$lerepertoire,
                               PCLZIP_OPT_REMOVE_PATH,$directory,
                               PCLZIP_OPT_SET_CHMOD, 0777);
   }
   else
   {
     $fichier = $directory.$fic;
     $aller = zip($fichier);
   }
   include 'style.inc.php';
   $parent=$base;
   $dir=$dos;
   if ($dir == $base || $dir=="$base/ressources" || $dir=="$base/devoirs")
   {
     $titre =str_replace("devoirs",$mess_trx_rendus,$titre);
     $titre =str_replace("ressources",$mess_mes_docs,$titre);
   }
   entete_simple_rep($titre);
   echo "<TR><TD width=100% colspan='2'><table width=100% cellspacing='1' cellpadding='3' border='0'>";
   list_dir();
   echo fin_tableau('');
   exit;
}
// revient de edit_box et sauve_page
if (isset($objet) && $objet == "vient_edit")
{
   include 'style.inc.php';
   $rep_insert = $rallonge."/".$nouveau_rep;
   $parent=$base;
   $dir=$dos;
   if ($dir == $base || $dir=="$base/ressources" || $dir=="$base/devoirs")
   {
     $titre =str_replace("devoirs",$mess_trx_rendus,$titre);
     $titre =str_replace("ressources",$mess_mes_docs,$titre);
   }
   $letitre = $titre." : ".strtolower($mess_telecharge);
   entete_simple_rep($letitre);
   echo "<TR><TD width=100% colspan='2'><table width=100% cellspacing='1' cellpadding='3' border='0'>";
   if (isset($message) && $message != "")
      $mess_notif = $message;
   list_dir();
   echo fin_tableau('');
   exit;
}
// Création d'un nouveau dossier
if (isset($objet) && $objet == "cree_rep")
{
   include 'style.inc.php';
   chdir($directory);
   $fichier_test = $nouveau_rep;
   $nom_final=modif_nom($fichier_test);
   $handle=opendir('./');
   $i=0;
   while ($file = readdir($handle))
   {
     if ($file == $nom_final)
     {
        entete_simple_rep($mess_menu_mon_casier);
        echo "<TR><TD><FONT SIZE='2'>$cas_nom_dossier_exist</B></FONT></TD></TR>";
        echo fin_tableau('');
        exit;
     }
     $i++;
   }
   mkdir($nom_final,0777);
   chmod($nom_final,0777);
   if ($communes_groupe == 1 || $formateurs == 1)
   {
     if ($s_exp == "lx")
        $rep_insert = $rallonge."/".$nom_final;
     $id_max = Donne_ID ($connect,"select max(ech_cdn) from echange_grp");
     if ($communes_groupe == 1)
       $requete = mysql_query("INSERT INTO echange_grp (ech_cdn,ech_path_lb,ech_grp_no,ech_auteur_no,ech_date_dt) VALUES ('$id_max',\"$rep_insert\",'$id_grp','$id_user','$date_insert')");
     else
       $requete = mysql_query("INSERT INTO echange_grp (ech_cdn,ech_path_lb,ech_grp_no,ech_auteur_no,ech_date_dt) VALUES ('$id_max',\"$rep_insert\",0,'$id_user','$date_insert')");
   }
   $parent=dirname($rep_insert);
   $message = str_replace(" ","%20",$message);
   $lien_retour = "modif_rep_fic.php?mess_notif=$message&id_grp=$id_grp&formateurs=$formateurs&communes_groupe=$communes_groupe&rallonge=$rep_insert&fichier=$nom_final&parent=$parent&sousdos=$dos&dossier=$rep_insert&direct=dossier";
   $nouveau_rep=" ";
   $nom_final = " ";
      $mess_notif = $message;
   $lien_retour = urlencode($lien_retour);
   echo "<script language=\"JavaScript\">";
   echo "document.location.replace(\"trace.php?link=$lien_retour\")";
   echo "</script>";
   exit;

}
//....................................
if (isset($objet) && $objet=="dossier_ftp"){
  include "style.inc.php";
  if ($doc_fic_name == "")
     $message ="<FONT size='2'>".strtolower($mess_fichier_no)."</font>";
  elseif( !is_file($doc_fic))
     $message ="$mess_fic_dep_lim 2000 Ko";
  else{
    $user=$bdd;
    $localdir = $doc_fic;
    $fichier_test = $doc_fic_name;
    $nom = modif_nom($fichier_test);
    $message = "$mess_casier_fichier [$nom] $mess_casier_fichier_suite";
    $remotedir = "/$bdd/$rallonge/$nom";
    $port=21;
    $ftpc=ftp_connect($host);
    $result_login=ftp_login($ftpc, $user, $passwd);
    if ((!$result_login) || (!$ftpc)){
      return "impossible de se connecter";
      die;
    }
    $coller = ftp_put($ftpc,$remotedir,$localdir,FTP_BINARY );
    $path = $dos."/$doc_fic_name";
    if ($communes_groupe == 1 || $formateurs == 1){
     $id_max = Donne_ID ($connect,"select max(ech_cdn) from echange_grp");
     if ($communes_groupe == 1)
       $requete = mysql_query("INSERT INTO echange_grp (ech_cdn,ech_path_lb,ech_grp_no,ech_auteur_no,ech_date_dt) VALUES ('$id_max',\"$path\",'$id_grp','$id_user','$date_insert')");
     else
       $requete = mysql_query("INSERT INTO echange_grp (ech_cdn,ech_path_lb,ech_grp_no,ech_auteur_no,ech_date_dt) VALUES ('$id_max',\"$path\",0,'$id_user','$date_insert')");
    }
  }
  $parent=$base;
  $dir=$dos;
  if ($dir == $base || $dir=="$base/ressources" || $dir=="$base/devoirs"){
     $titre =str_replace("devoirs",$mess_trx_rendus,$titre);
     $titre =str_replace("ressources",$mess_mes_docs,$titre);
  }
   entete_simple_rep($titre);
   if (isset($message) && $message != "")
       $mess_notif = "<B>".strtolower($mess_telecharge)."<B> ".$message;
   echo "<TR><TD width=100% colspan='2'><table width=100% cellspacing='1' cellpadding='3' border='0'>";
   list_dir();
   echo fin_tableau('');
  exit;
}
if (isset($objet) && $objet == "supp_lot"){
   include "style.inc.php";
   $handle = opendir($directory);
   $Cpt = 0;
   $nbrsup = count($fiche);
   while ($file = readdir($handle))
   {
     if (!is_dir($file) && $file != '.' && $file != '..' && strstr($file,".") && strlen($file) > 1)
     {
        $compte++;
        $fichier = $directory.$fiche[$compte];
       for ($i=1;$i< ($nbrsup+1);$i++){
          if  ( isset($fiche[$i]) && isset($supprime[$i]) && $file == $fiche[$i] && $supprime[$i] == 'on')
          {
            if ($Cpt == 3){$message .="<br />";$Cpt = 0;}
            $message .= "&nbsp;[$file]";
            unlink($fichier);
            $Cpt++;
          }
       }
     }
   }
   if ($message != '')
      $mess_notif = $mess_fic_suplot." : ".$message;
   $parent=$base;
   $dir=$dos;
   $titre = $titre_dossier;
   if ($dir == $base || $dir=="$base/ressources" || $dir=="$base/devoirs"){
      $titre =str_replace("devoirs",$mess_trx_rendus,$titre);
      $titre =str_replace("ressources",$mess_mes_docs,$titre);
   }
   $titre = $titre_dossier;
   entete_simple_rep($titre);
   echo "<TR><TD width=100% colspan='2'><table width=100% cellspacing='1' cellpadding='3' border='0'>";
   list_dir();
   echo fin_tableau('');
   exit;
}
//....................................
// téléchargement d'un fichier
if (isset($objet) && $objet == "telecharger" && isset($_FILES["userfile"]["tmp_name"]))
{

     $userfile = $_FILES["userfile"]["tmp_name"];
     include "style.inc.php";
     if ($typ_user == "APPRENANT")
       $poids = "500 Ko";
     else
       $poids = "5 Mo";
      $nom_final = $_FILES['userfile']['name'];
     if ($nom_final == "")
        $message = strtolower($mess_fichier_no);
     elseif(!is_file($_FILES["userfile"]["tmp_name"]))
        $message = "$mess_fic_dep_lim $poids<BR>";
     if ($message != "")
     {
        $parent=$base;
        $dir=$dos;
        if ($dir == $base || $dir=="$base/ressources" || $dir=="$base/devoirs")
        {
          $titre =str_replace("devoirs",$mess_trx_rendus,$titre);
          $titre =str_replace("ressources",$mess_mes_docs,$titre);
        }
        entete_simple_rep($titre);
        if (isset($message) && $message != "")
            $mess_notif = "<B>".strtolower($mess_telecharge)."</B> : ".$message;
        echo "<TR><TD width=100% colspan='2'><table width=100% cellspacing='1' cellpadding='3' border='0'>";
        list_dir();
        echo fin_tableau('');
        exit;
      }
      list($extension,$nom)=getextension($_FILES['userfile']['name']);
      if (in_array(strtolower($extension), array("exe","sh","py","ida","idq","asp","cer","cdx","asa","idc","cfm","dbm","php","php4","inc","shtml","cgi")))
      {
        $parent=$base;
        $dir=$dos;
        if ($dir == $base || $dir=="$base/ressources" || $dir=="$base/devoirs")
        {
          $titre = str_replace("devoirs",$mess_trx_rendus,$titre);
          $titre = str_replace("ressources",$mess_mes_docs,$titre);
        }
        entete_simple_rep($titre);
        $mess_notif = $mess_fic_exe;
        echo "<TR><TD width=100% colspan='2'><table width=100% cellspacing='1' cellpadding='3' border='0'>";
        list_dir();
        echo fin_tableau('');
        exit;
     }
     if ($_FILES['userfile']['name'] != "")
     {
        $fichier_test = $_FILES['userfile']['name'];
        if ($communes_groupe == 1)
        {
            $nom_final = modif_nom($fichier_test);
            $file_extension = substr(strrchr($nom_final,"." ),1);
            $nom_file = str_replace(".$file_extension",'',$nom_final);
            $nom_final = $nom_file.'_'.time().'.'.$file_extension;
        }
        else
            $nom_final = modif_nom($fichier_test);
        $dest_file = $directory."/".$nom_final;
        $source_file = $_FILES["userfile"]["tmp_name"];
        $copier = move_uploaded_file($_FILES["userfile"]["tmp_name"] , $dest_file);
        chmod($dest_file,0777);
        $rep_insert = $rallonge."/".$nom_final;
        if ($communes_groupe == 1 || $formateurs == 1)
        {
           $id_max = Donne_ID ($connect,"select max(ech_cdn) from echange_grp");
           if ($communes_groupe == 1)
              $requete = mysql_query("INSERT INTO echange_grp (ech_cdn,ech_path_lb,ech_grp_no,ech_auteur_no,ech_date_dt) VALUES ('$id_max',\"$rep_insert\",'$id_grp','$id_user','$date_insert')");
           else
             $requete = mysql_query("INSERT INTO echange_grp (ech_cdn,ech_path_lb,ech_grp_no,ech_auteur_no,ech_date_dt) VALUES ('$id_max',\"$rep_insert\",0,'$id_user','$date_insert')");
        }
        $message = "$mess_casier_fichier [$nom_final] $mess_casier_fichier_suite";
   }
   else
        $message = $mess_fichier_no;
   $parent=$base;
   $dir=$dos;
   if ($dir == $base || $dir=="$base/ressources" || $dir=="$base/devoirs")
   {
       $titre =str_replace("devoirs",$mess_trx_rendus,$titre);
       $titre =str_replace("ressources",$mess_mes_docs,$titre);
   }
   entete_simple_rep($titre);
   echo "<TR><TD width=100% colspan='2'><table width=100% cellspacing='1' cellpadding='3' border='0'>";
   if (isset($message) && $message != "")
      $mess_notif = $message;
   list_dir();
   echo fin_tableau('');
 exit;
}

// cas ou on renomme le fichier
if (isset($objet) && $objet == "renommer"){
   include "style.inc.php";
   if (!$nouveau)
   {
      $nombre=0;
      $no_renomme = 0;
      $renomme_lien=$rallonge_test.$fic;
      $sql=mysql_query("SELECT ress_cdn FROM ressource_new WHERE ress_url_lb=\"$renomme_lien\"");
      $nbr_result=mysql_num_rows($sql);
      if ($nbr_result !=0 )
      {
        while($nombre<$nbr_result)
        {
          $numero_lien= mysql_result ($sql,$nombre,"ress_cdn");
          $req_grp = mysql_query ("select distinct utilgr_groupe_no from utilisateur_groupe");
          if ($req_grp)
          {
              while ($item = mysql_fetch_object($req_grp))
              {
                     $num_grp = $item->utilgr_groupe_no;
                     $sql_ress=mysql_query("SELECT * FROM activite,suivi1_$num_grp WHERE
                                            suivi_act_no = activite.act_cdn AND
                                            activite.act_ress_no = '$numero_lien'");
                     $nbr_ress=mysql_num_rows($sql_ress);
                     if ($nbr_ress != 0)
                         $no_renomme++;
                     $nombre++;
              }
           }
        }
      }
      $parent=$base;
      $dir=$dos;
      ?>
        <SCRIPT language=JavaScript>
          function checkForm10(frm) {
            var ErrMsg = "<?php echo $mess_info_no;?>\n";
            var lenInit = ErrMsg.length;
            if (isEmpty(frm.nouveau)==true)
              ErrMsg += ' - <?php echo $mess_suivi_nom_fic;?>\n';
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
      if ($no_renomme == 0)
      {
           entete_simple_rep($titre);
           if (isset($message) && $message != "")
               $mess_notif = strtolower($mess_casier_ren_fic)." ".$message;
           echo "<TR><TD width=100% colspan='2'><table width=100% cellspacing='1' cellpadding='3' border='0'>";
           echo "<FORM name='form10' action=\"modif_rep_fic.php\" target='main' method='POST'>";
           echo "<TR><TD>";
           echo "$mess_casier_renom_fic</TD><TD>";
           echo "<INPUT TYPE='text' class='INPUT'  NAME='nouveau' VALUE='$fic' size=25 MAXLENGTH='90'>";
           echo "<INPUT TYPE='HIDDEN' NAME='objet' VALUE='renommer'>";
           echo "<INPUT TYPE='HIDDEN' NAME='fic' VALUE='$fic'>";
           echo "<INPUT TYPE='HIDDEN' NAME='direct' VALUE='$direct'>";
           echo "<INPUT TYPE='HIDDEN' NAME='dos' VALUE='$dos'>";
           echo "<INPUT TYPE='HIDDEN' NAME='rallonge' VALUE='$rallonge'>";
           echo "<INPUT TYPE='HIDDEN' NAME='id_grp' VALUE='$id_grp'>";
           echo "<INPUT TYPE='HIDDEN' NAME='communes_groupe' VALUE='$communes_groupe'>";
           echo "<INPUT TYPE='HIDDEN' NAME='formateurs' VALUE='$formateurs'>";
           echo "</TD><TD align='center'><A HREF=\"modif_rep_fic.php?fermer=1\" onclick=\"javascript:checkForm10(document.form10);\" ".
                "onmouseover=\"img1.src='images/fiche_identite/boutvalidb.gif';return true;\" ".
                "onmouseout=\"img1.src='images/fiche_identite/boutvalid.gif'\">".
                "<IMG NAME=\"img1\" SRC=\"images/fiche_identite/boutvalid.gif\" BORDER='0' ".
                "onLoad=\"tempImg=new Image(0,0); tempImg.src='images/fiche_identite/boutvalidb.gif'\"></A>";
           echo "</TD></FORM></TR></TABLE></TD></TR></TABLE>";
          exit;
      }
      else
           $message = "$mess_casier_nul_supp";
      exit;
   }
   if (isset($direct) && $direct=="application"){
        list($extension,$nom)=getextension($nouveau);
        if (in_array(strtolower($extension), array("ida","idq","asp","cer","cdx","asa","idc","cfm","dbm","php","php4","inc","shtml","cgi"))){
          $message= $cas_fic_exe;
          $parent=$base;
          $dir=$dos;
          if ($dir == $base || $dir=="$base/ressources" || $dir=="$base/devoirs"){
            $titre =str_replace("devoirs",$mess_trx_rendus,$titre);
            $titre =str_replace("ressources",$mess_mes_docs,$titre);
          }
          entete_simple_rep($titre);
          if (isset($message) && $message != "")
              $mess_notif = $message;
          echo "<TR><TD width=100% colspan='2'><table width=100% cellspacing='1' cellpadding='3' border='0'>";
          list_dir();
          echo fin_tableau('');
         exit;
        }
        $fichier_test = $nouveau;
        $nouveau = modif_nom($fichier_test);
        $new=$directory.$nouveau;
        $ancien=$directory.$fic;
        rename($ancien, $new);
        $path= $dos."/".$nouveau;
        $ancien_path = $dos."/".$fic;
        if ($communes_groupe == 1 || $formateurs == 1)
            $requete = mysql_query("UPDATE echange_grp SET ech_path_lb = \"$path\" WHERE ech_path_lb = \"$ancien_path\"");
        $message = $mess_casier_fichier." [".$fic."] ".$mess_casier_renom." [".$nouveau."]";
        $nouveau="";
        $parent=$base;
        $dir=$dos;
        if ($dir == $base || $dir=="$base/ressources" || $dir=="$base/devoirs"){
          $titre =str_replace("devoirs",$mess_trx_rendus,$titre);
          $titre =str_replace("ressources",$mess_mes_docs,$titre);
        }
        entete_simple_rep($titre);
        echo "<TR><TD width=100% colspan='2'><table width=100% cellspacing='1' cellpadding='3' border='0'>";
        if (isset($message) && $message != "")
            $mess_notif = $message;
        list_dir();
        echo fin_tableau('');
    exit;
   }
exit;
}
// cas ou on supprime le fichier
if (isset($objet) && $objet == "supprimer")
{
   include "style.inc.php";
   if (isset($direct) && $direct=="application")
   {
      // Supprime les références de ce lien dans la table "$mess_cas_ress" et "ACITIVITES"
      $supprime_lien=$rallonge_test.$fic;
      $sql=mysql_query("SELECT ress_cdn FROM ressource_new WHERE ress_url_lb=\"$supprime_lien\"");
      $nbr_result=mysql_num_rows($sql);
      if ($nbr_result !=0 ){
        $nombre=0;
        $no_supp = 0;$passage = 1;
          $numero_lien= mysql_result ($sql,$nombre,"ress_cdn");
          $req_grp = mysql_query ("select distinct utilgr_groupe_no from utilisateur_groupe");
          if ($req_grp)
          {
              while ($item = mysql_fetch_object($req_grp))
              {
                     $num_grp = $item->utilgr_groupe_no;
                     $sql_ress=mysql_query("SELECT * FROM activite,suivi1_$num_grp WHERE
                                            suivi_act_no = activite.act_cdn AND
                                            activite.act_ress_no = '$numero_lien'");
                     $nbr_ress=mysql_num_rows($sql_ress);
                     if ($nbr_ress != 0)
                         $no_supp++;
                     $nombre++;
              }
           }
      }
      else
        $no_supp = 0;
      $path = str_replace("\\","/",$rallonge.$fic);
      $fichier=$directory.$fic;
      if ($no_supp == 0 && !$passage)
      {
            unlink($fichier);
            $requete = mysql_query("DELETE from echange_grp WHERE ech_path_lb = \"$path\"");
            $message = "[$fic] $mess_casier_fic_sup";
      }
      elseif($no_supp == 0 && $passage == 1)
      {
            unlink($fichier);
            $requete = mysql_query("DELETE from echange_grp WHERE ech_path_lb = \"$path\"");
            $debut_message = "";
            $message = "$debut_message  $mess_casier_fichier [$fic] $mess_casier_fic_sup  $mess_casier_retour </b>";
            $effacer_lien=mysql_query ("DELETE FROM ressource_new where ress_cdn = '$numero_lien'");
            $effacer_lien=mysql_query ("DELETE FROM activite where act_ress_no = '$numero_lien'");
      }
      else
      {
            $debut_message = $mess_casier_nul_supp;
            $message = "$debut_message";
      }
      // Fin de procédure de suppression dans les autres tables
      $parent=$base;
      $dir=$dos;
      if ($dir == $base || $dir=="$base/ressources" || $dir=="$base/devoirs")
      {
        $titre =str_replace("devoirs",$mess_trx_rendus,$titre);
        $titre =str_replace("ressources",$mess_mes_docs,$titre);
      }
      entete_simple_rep($titre);
      if (isset($message) && $message != "")
      {
           $mess_notif = $message;
      echo "<TR><TD width=100% colspan='2'><table width=100% cellspacing='1' cellpadding='3' border='0'>";
      list_dir();
      echo fin_tableau('');
       exit;
      }
   }
   if(isset($direct) &&  $direct=="dossier")
   {
     if (isset($_GET['nv']) && $_GET['nv'] == 1)
     {
       $dossier_supp = $fichier;
       $fichier=$directory.$fic;
       $path = $parent."/".$fichier;
       $dossier_supp = $fichier;
       $fichier=$directory.$fic;
       $ral = dirname($rallonge);
       $grand_pa = dirname($parent);
       $neo_fiche = substr(strrchr($parent, "/"), 1);
       viredir($fichier,$s_exp);
       $message =  " [$dossier_supp] : $mess_casier_dos_sup";
       $message = str_replace(" ","%20",$message);
       $lien_retour="modif_rep_fic.php?mess_notif=$message&id_grp=$id_grp&formateurs=$formateurs&communes_groupe=$communes_groupe&rallonge=$ral&fichier=$neo_fiche&parent=$grand_pa&sousdos=$parent&parent=$dir&dossier=$rep&direct=dossier";
       $lien_retour=urlencode($lien_retour);
     }
     else
     {
      $handle=opendir($directory);
      $i=0;
      while ($fil = readdir($handle))
      {
        if ($fil != '.' && $fil != '..')
        {
          $message =  $mess_casier_sup_no;
          echo $message;
          $parent=$base;
          $dir=$dos;
          $letitre = "$mess_casier_rep_source : $mess_menu_supp_qcm";
          entete_simple_rep($letitre);
          if (isset($message) && $message != "")
             echo "<TR><TD align='left' colspan='2'><FONT SIZE='2'>$message</font></TD></TR>";
          echo fin_tableau('');
          exit;
        }
      $i++;
      }
      closedir($handle) ;
      $path = $parent."/".$fichier;
      $dossier_supp = $fichier;
      $fichier=$directory.$fic;
      $ral = dirname($rallonge);
      $grand_pa = dirname($parent);
      $neo_fiche = substr(strrchr($parent, "/"), 1);
      $requete = mysql_query("DELETE from echange_grp WHERE ech_path_lb = \"$path\" AND ech_auteur_no = $id_user");
      //echo $directory;exit;
      $d=dirname($directory);
      $handle=opendir($d);
         $cok = rmdir($directory);
      closedir($handle);
      if ($cok == true)
         $message =  " [$dossier_supp] : $mess_casier_dos_sup";
      else
         $message =  " [$dossier_supp] : Ce fichier n'a pas été supprimé , désolé !! Un dysfonctionnement de votre serveur, voyez avec l'administrateur";
      $message = str_replace(" ","%20",$message);
      $lien_retour="modif_rep_fic.php?mess_notif=$message&id_grp=$id_grp&formateurs=$formateurs&communes_groupe=$communes_groupe&rallonge=$ral&fichier=$neo_fiche&parent=$grand_pa&sousdos=$parent&parent=$dir&dossier=$rep&direct=dossier";
      $lien_retour=urlencode($lien_retour);
     echo "<script language=\"JavaScript\">";
     echo "document.location.replace(\"trace.php?link=$lien_retour\")";
     echo "</script>";
   }
  exit;
  }
}

// cas ou on édite le fichier
if ((isset($objet) && $objet == "editer") && !isset($sauver) && (isset($direct) && $direct=="application"))
{
include "style.inc.php";
      echo "<CENTER><table bgColor='#298CA0' cellspacing='2' cellpadding='0'><TR><TD>";
      echo "<table bgColor='#FFFFFF' cellspacing='1' cellpadding='0' width='100%'>";
      echo "<TR><TD background=\"images/fond_titre_table.jpg\" colspan='2' height='36' align='center' valign='center'><Font size='3' color='#FFFFFF'><B>$fic<BR><FONT SIZE='2'>$mess_casier_editer</B></FONT>";
      echo "</TD></TR><TR><TD colspan='2'>";
      $fichier=$fic;
      echo "<CENTER><form action=\"modif_rep_fic.php\" method='POST'>";
      echo "<INPUT TYPE='hidden' NAME='objet' VALUE='editer'>";
      echo "<INPUT TYPE='hidden' NAME='sauver' VALUE='1'>";
      echo "<INPUT TYPE='hidden' NAME='direct' VALUE='application'>";
      echo "<INPUT TYPE='HIDDEN' NAME='id_grp' VALUE='$id_grp'>";
      echo "<INPUT TYPE='HIDDEN' NAME='communes_groupe' VALUE='$communes_groupe'>";
      echo "<INPUT TYPE='HIDDEN' NAME='formateurs' VALUE='$formateurs'>";
      echo "<INPUT TYPE='hidden' NAME='director' VALUE='$directory'>";
      echo "<INPUT TYPE='hidden' NAME='fic' VALUE='$fic'>";
      echo "<INPUT TYPE='hidden' NAME='rallonge' VALUE='$rallonge'>";
      echo "<INPUT TYPE='hidden' NAME='dos' VALUE='$dos'>";
      echo "<TEXTAREA  NAME='code' rows='16' cols='100' wrap='ON' class='TEXTAREA'>";
      $fp=fopen($directory.$fic,"r");
      while (!feof($fp)){
        $tmp=fgets($fp,4096);
        $tmp=str_replace("<","&lt;",$tmp);
        echo "$tmp";
        }
       fclose($fp);
       echo "</TEXTAREA>\n";
       if  ($typ_user != "APPRENANT"){
         echo "<br><br><INPUT TYPE='image' NAME='SUBMIT' SRC=\"images/enregistrer-lav.gif\" title=\"$mess_casier_save\">";
         echo "</form>";
       }
      echo "</TD></TR></TABLE></TD></TR></TABLE>";
  exit;
}

if (isset($objet) && $objet == "editer" && isset($sauver) && isset($direct) && $direct == "application")
{
include "style.inc.php";
  $fp=fopen($director.$fic,"w");
  $code=str_replace('\"','"',$code);
  $code=str_replace("\'","'",$code);
  $code=stripslashes($code);
  $code=str_replace("&lt;","<",$code);
  fputs ($fp,$code);
  fclose($fp);
  $parent=$base;
  $dir=$dos;
  if ($dir == $base || $dir=="$base/ressources" || $dir=="$base/devoirs"){
    $titre =str_replace("devoirs",$mess_trx_rendus,$titre);
    $titre =str_replace("ressources",$mess_mes_docs,$titre);
  }
  $letitre = "$mess_casier_fichier \"$fic\" $mess_casier_sauve";
   entete_simple_rep($letitre);
   echo "<TR><TD width=100% colspan='2'><table width=100% cellspacing='1' cellpadding='3' border='0'>";
   list_dir();
   echo fin_tableau('');
  exit;
}

if (($typ_user !="APPRENANT") && (isset($objet) && $objet=="telecharge"))
{
     include "style.inc.php";
     echo "<CENTER><table bgColor='#298CA0' cellspacing='2' cellpadding='0'><TR><TD>";
     echo "<table bgColor='#FFFFFF' cellspacing='1' cellpadding='0' width='100%'>";
     echo "<TR><TD background=\"images/fond_titre_table.jpg\" colspan='2' height='36' align='center' valign='center'><Font size='3' color='#FFFFFF'><B>$titre<BR></B></FONT></TD></TR>";
     echo "<TR bgcolor='#CEE6EC'><TD colspan='2'><FONT SIZE='2'>&nbsp;&nbsp;&nbsp;&nbsp;".$nombre_fichiers."&nbsp;".strtolower($mess_casier_fat)."</TD></TR>";

     echo "<form name='form' action=\"modif_rep_fic.php\" method='post' enctype='multipart/form-data'>";
     echo "<INPUT TYPE='HIDDEN' NAME='objet' VALUE='charge_multi'>";
     echo "<INPUT TYPE='HIDDEN' NAME='direct' VALUE='1'>";
     echo "<INPUT TYPE='HIDDEN' NAME='id_grp' VALUE='$id_grp'>";
     echo "<INPUT TYPE='HIDDEN' NAME='communes_groupe' VALUE='$communes_groupe'>";
     echo "<INPUT TYPE='HIDDEN' NAME='formateurs' VALUE='$formateurs'>";
     echo "<INPUT TYPE='HIDDEN' NAME='dos' VALUE='$dos'>";
     echo "<INPUT TYPE='HIDDEN' NAME='rallonge' VALUE='$rallonge'>";
     echo "<INPUT type='HIDDEN' name='MAX_FILE_SIZE' value='500000'>";
     $i=0;
     while ($i<$nombre_fichiers)
     {
        echo "<TR><TD colspan='2'height='30'>&nbsp;&nbsp;&nbsp;&nbsp;<input name=\"userfile[$i]\" type='file' size='60'>&nbsp;&nbsp;&nbsp;&nbsp;</TD></TR>";
       $i++;
     }
     echo"<INPUT TYPE='HIDDEN' NAME='i' VALUE='$i'>";
     echo "<TR bgcolor='#CEE6EC' height='45'><TD colspan='2' align='center'><A HREF=\"javascript:document.form.submit();\" onmouseover=\"img1.src='images/fiche_identite/boutvalidb.gif';return true;\" onmouseout=\"img1.src='images/fiche_identite/boutvalid.gif'\">".
         "<IMG NAME=\"img1\" SRC=\"images/fiche_identite/boutvalid.gif\" BORDER='0' onLoad=\"tempImg=new Image(0,0); tempImg.src='images/fiche_identite/boutvalidb.gif'\"></A>";
     echo "</TD></TR></form></TABLE></TD></TR></TABLE>";
     echo "&nbsp;<P>";
     $parent=$base;
     $dir=$dos;
     if ($dir == $base || $dir == "$base/ressources" || $dir == "$base/devoirs")
     {
       $titre =str_replace("devoirs",$mess_trx_rendus,$titre);
       $titre =str_replace("ressources",$mess_mes_docs,$titre);
     }
     entete_simple_rep($titre);
     echo "<TR><TD width=100% colspan='2'><table width=100% cellspacing='1' cellpadding='3' border='0'>";
     list_dir();
     echo "</TD></TR></TABLE>";
     echo fin_tableau('');
     exit;
}


if (($typ_user !="APPRENANT") && ($i) && ($objet=="charge_multi"))
{
 include "style.inc.php";
   $pointeur=0;
   while ($pointeur<$i){
     if (!$userfile[$pointeur]){
        $avert++;
        $pointeur++;
        continue;
     }
     $fichier_test=$userfile_name[$pointeur];
     $nom_final = modif_nom($fichier_test);
     list($extension,$nom)=getextension($nom_final);
     if (in_array(strtolower($extension), array("ida","idq","asp","cer","cdx","asa","idc","cfm","dbm","php","php4","inc","shtml","cgi"))){
        $interdit++;
        $pointeur++;
        continue;
     }
     $dest_file=$directory."/".$nom_final;
     $source_file=$userfile[$pointeur];
     $copier=move_uploaded_file($source_file , $dest_file);
     chmod ($dest_file,0777);
     $rep_insert = $rallonge."/".$nom_final;
     if ($communes_groupe == 1 || $formateurs == 1)
     {
       $id_max = Donne_ID ($connect,"select max(ech_cdn) from echange_grp");
       if ($communes_groupe == 1)
         $requete = mysql_query("INSERT INTO echange_grp (ech_cdn,ech_path_lb,ech_grp_no,ech_auteur_no,ech_date_dt) VALUES ('$id_max',\"$rep_insert\",'$id_grp','$id_user','$date_insert')");
       else
         $requete = mysql_query("INSERT INTO echange_grp (ech_cdn,ech_path_lb,ech_grp_no,ech_auteur_no,ech_date_dt) VALUES ('$id_max',\"$rep_insert\",0,'$id_user','$date_insert')");
     }
  $pointeur++;
   }
   if ($dir == $base || $dir=="$base/ressources" || $dir=="$base/devoirs"){
     $titre =str_replace("devoirs",$mess_trx_rendus,$titre);
     $titre =str_replace("ressources",$mess_mes_docs,$titre);
   }
   if ($interdit > 0)
      $message = "$interdit $mess_fic_nbr_no_exe";
   if ($avert > 0){
      $message = "$avert $mess_fic_nbr_no_ko";
      if ($typ_user !="ADMINISTRATEUR")
         $message .= $mess_fic_adm_wrt;
   }
   $parent=$base;
   $dir=$dos;
   entete_simple_rep($titre);
   if (isset($message) && $message != "")
      $mess_notif = $message;
   echo "<TR><TD width=100% colspan='2'><table width=100% cellspacing='1' cellpadding='3' border='0'>";
   list_dir();
   echo fin_tableau('');
   $telecharge=0;
exit;
}

// Fonction d'affichage du répertoire courant

function list_dir()
{
 GLOBAL $connect,$agent,$id_user,$dir,$parent,$rallonge,$typ_user,$mac,$nom_grp,
        $base,$communes_groupe,$id_grp,$formateurs,$affiche_nom,$ap,
        $lg,$login,$lien_origine,$aff_mouve,$aff_mouve_fini,
        $mess_notif,$bouton_gauche,$bouton_droite;
 require "lang$lg.inc.php";
 require "admin.inc.php";
 require_once('class/pclzip.inc.php');
 $style = "clear:both;background-color: #fad163;font-family: arial,tahoma,serif;font-weight:bold;";
 $style.= "text-align: left;color: #003333;font-size: 12px;";
 $style.= "margin-top: -58px;left: 4px;position:absolute;display :block;";
 $style.= "cursor: pointer;padding: 2px;";
 if (isset($mess_notif) && $mess_notif != '')
 {
     echo '<div id="mien" style="'.$style.'"';?>
          onClick="javascript:$(document).ready(function() {if ($.browser.msie) {$('#mien').hide();}else{$('#mien').hide('slow');}})"
          <?php echo "title=\"$mess_clkF\">".stripslashes($mess_notif).
          '</div>';
 }
 if (isset($aff_mouve) && $aff_mouve != '')
 {
     echo '<div id="mien" style="'.$style.'"';?>
          onClick="javascript:$(document).ready(function() {if ($.browser.msie) {$('#mien').hide();}else{$('#mien').hide('slow');}})"
          <?php echo "title=\"$mess_clkF\">".stripslashes($aff_mouve).
          '</div>';
 }
 if ($typ_user == "TUTEUR" && $dir == $base && $communes_groupe != 1 && $formateurs != 1)
    $dir .= "/ressources";
 chdir($dir);
 $parent=dirname($dir);
 $handle=opendir("./");
 $temp=$dir;
 $der_dos = substr(strrchr($dir,"/"),1);
 $der_dossier = substr(strrchr($parent,"/"),1);
 $long=strlen($base)+1;
 $suite = substr($temp,$long);
 $le_suivant = $base."/$id_grp";
 if ($communes_groupe == 1 && (($typ_user == "APPRENANT" && $base == $dir) || ($typ_user == "TUTEUR" && $dir == $le_suivant)))
    $suite =GetDataField ($connect,"select grp_nom_lb from groupe where grp_cdn = $id_grp","grp_nom_lb");
 if ($communes_groupe == 1 && $typ_user != "APPRENANT" && $typ_user != "TUTEUR" && $dir != $base && $id_grp > 0)
 {
    $lasuite =GetDataField ($connect,"select grp_nom_lb from groupe where grp_cdn = $id_grp","grp_nom_lb");
    $suite = str_replace($id_grp,$lasuite,$suite);
 }
 if ($typ_user == "APPRENANT" && strstr($parent,"devoirs"))
 {
    $suite = GetDataField ($connect,"select grp_nom_lb from groupe where grp_cdn = $der_dos","grp_nom_lb");
    $suite = "$mess_devoirs"."/".$suite;
 }
 if ($typ_user != "APPRENANT" && strstr($dir,"$base/devoirs/") && strstr($parent,"--"))
 {
    $der_doss = substr(strrchr($parent,"/"),1);
    $item = explode("--",$der_doss);
    $requete = mysql_query("SELECT count(*) from utilisateur where util_typutil_lb = 'APPRENANT' AND util_cdn = '$item[1]' AND util_login_lb = '$item[0]'");
    $nb_count = mysql_result($requete,0);
    if ($nb_count == 1)
    {
       $nom = GetDataField ($connect,"select util_nom_lb from utilisateur where util_typutil_lb = 'APPRENANT' AND util_cdn = '$item[1]' AND util_login_lb = '$item[0]'","util_nom_lb");
       $prenom = GetDataField ($connect,"select util_prenom_lb from utilisateur where util_typutil_lb = 'APPRENANT' AND util_cdn = '$item[1]' AND util_login_lb = '$item[0]'","util_prenom_lb");
       $nom_grp = GetDataField ($connect,"select grp_nom_lb from groupe where grp_cdn = $der_dos","grp_nom_lb");
       $der_doss = str_replace($item[1],$prenom,$der_doss);
       $der_doss = str_replace($item[0],$nom,$der_doss);
       $der_doss = str_replace("--"," ",$der_doss);
       $suite = "$mess_devoirs"."/$der_doss/".$nom_grp;
    }
 }
  if ($typ_user != "APPRENANT" && strstr($dir,"$base/devoirs/") && strstr($der_dos,"--"))
  {
     $item = explode("--",$der_dos);
     $requete = mysql_query("SELECT count(*) from utilisateur where util_typutil_lb = 'APPRENANT' AND util_cdn = '$item[1]' AND util_login_lb = '$item[0]'");
     $nb_count = mysql_result($requete,0);
     if ($nb_count == 1){
       $nom = GetDataField ($connect,"select util_nom_lb from utilisateur where util_typutil_lb = 'APPRENANT' AND util_cdn = '$item[1]' AND util_login_lb = '$item[0]'","util_nom_lb");
       $prenom = GetDataField ($connect,"select util_prenom_lb from utilisateur where util_typutil_lb = 'APPRENANT' AND util_cdn = '$item[1]' AND util_login_lb = '$item[0]'","util_prenom_lb");
       $der_doss = str_replace($item[1],$prenom,$der_dos);
       $der_doss = str_replace($item[0],$nom,$der_doss);
       $der_doss = str_replace("--"," ",$der_doss);
       $suite = "$mess_devoirs"."/".$der_doss;
     }
  }
  if ($communes_groupe == 1 || $formateurs == 1)
     $aff_rep = $suite;
  else
     $aff_rep = "$mess_casier_rep_source"."/".$suite;
  if ($parent == "ressources")
     $aff_rep = "$mess_casier_rep_source";
  if ($communes_groupe == 1 && ($typ_user == "APPRENANT" || $typ_user == "TUTEUR"))
     $nom_grp =GetDataField ($connect,"select grp_nom_lb from groupe where grp_cdn = $id_grp","grp_nom_lb");

  $lar_col = 12;
  $style_td_titre ="color: #FFFFFF;font-weight: bold; height:'30px'; font-family: arial,tahoma,serif;text-align: 'left';";
  $insert_style = "<td style=\"$style_td_titre\" valign=top>";
  $bande_titre = "<tr bgcolor='#2b677a'>";
  $bande_titre .= "$insert_style $mess_type</td>";
  $bande_titre .= "$insert_style $mess_nfd </td>";
  $bande_titre .= "$insert_style $mess_nbrfic</td>";
  $bande_titre .= "$insert_style $mess_taille</td>";
  $bande_titre .= "$insert_style $mess_der_insert</td>";
  if ($communes_groupe == 1 || $formateurs == 1)
     $bande_titre .= "$insert_style $mrc_aut</td>";
  $bande_titre .= "$insert_style $mess_rnmr</td>";
  $bande_titre .= "$insert_style $edit</td>";
  $bande_titre .= "$insert_style $mess_idx</td>";
  $bande_titre .= "$insert_style $mess_dplc</td>";
  $bande_titre .= "$insert_style $mess_zip/$mess_dezip</td>";
  $bande_titre .= "$insert_style $mess_ag_supp</td>";
  $bande_titre .= "</tr>";
  echo "<tr><td colspan='$lar_col' bgcolor='#FFFFFF'>";
  echo "<table width='100%' cellpadding='3' cellspacing='0' border='0'><tr>";
  if ($dir == $base || ($typ_user == 'TUTEUR' && $dir == "$base/ressources"))
     $rallonge_taille = $base;
  else
     $rallonge_taille = $rallonge;
  if ($communes_groupe == 1)
     $lien = "taille.php?qui=echange_groupe&director=$rallonge_taille";
  elseif ($formateurs == 1)
     $lien = "taille.php?qui=echange_formateur&director=$rallonge_taille";
  else
     $lien = "taille.php?qui=perso&director=$rallonge_taille";
  //$lien = urlencode($lien);
  echo "<td><div id='taille' style=\"float:left;padding-right:10px;\"><a href=\"javascript:void(0);\" class= 'bouton_new' ".
       "onclick=\"javascript:window.open('$lien', 'Espace', 'height=150,width=350,resizable=yes');\" ".
       "title=\"$taille_serveur\">$mess_pds_rep</a></div>";
  if ($formateurs == 1)
      echo aide_div("dossier_partages",0,0,0,0);
  elseif ($communes_groupe == 1 && $typ_user == 'APPRENANT')
      echo aide_div("dossiers_partages_apprenant",0,0,0,0);
  elseif ($communes_groupe == 1 && $typ_user != 'APPRENANT')
      echo aide_div("dossier_grp",0,0,0,0);
  elseif($typ_user != 'APPRENANT')
      echo aide_div("dossier_form",0,0,0,0);
  elseif($typ_user == 'APPRENANT')
      echo aide_div("dossier_apprenant",0,0,0,0);
  echo "</td></tr></table></td></tr>";
  if (($dir != $base && $communes_groupe != 1) ||
     ($dir != "$base/$id_grp" && $communes_groupe == 1 && $typ_user != 'APPRENANT') ||
     ($dir != "$base" && $communes_groupe == 1 && $typ_user == 'APPRENANT') ||
     ($dir == "$base/$id_grp" && $communes_groupe == 1 && $typ_user == 'ADMINISTRATEUR'))
  {
       $lien = "modif_rep_fic.php?id_grp=$id_grp&fichier=$der_dossier&formateurs=$formateurs&communes_groupe=$communes_groupe&direct=dossier&parent=$parent&flag=1&rallonge=$parent";
       $lien = urlencode($lien);
       $aff_rep =str_replace("devoirs","$mess_trx_rendus",$aff_rep);
       $aff_rep =str_replace("$mess_casier_rep_source/ressources","$mess_casier_rep_source/$mess_mes_docs",$aff_rep);
       echo "<tr><td colspan='$lar_col' bgcolor='#FFFFFF'>";
       echo "<table width='100%' cellpadding='3' cellspacing='0' border='0'>";
       echo "<tr><td valign='middle' width=5%><a href=\"trace.php?link=$lien\" title=\"$mess_casier_deroule\" ".
            " onmouseover=\"img_fj.src='images/repertoire/icoGdosparb.gif';return true;\"".
            " onmouseout=\"img_fj.src='images/repertoire/icoGdospar.gif'\">".
            "<IMG NAME=\"img_fj\" SRC=\"images/repertoire/icoGdospar.gif\" BORDER='0' alt=\"$mess_casier_deroule\"".
            " onLoad=\"tempImg=new Image(0,0); tempImg.src='images/repertoire/icoGdosparb.gif'\"></a></td>";
       echo "<td valign='middle' align=left><B>$aff_rep</B></td></tr></table></td></tr>";
 }
  echo $bande_titre;
  $tab_file = array();
  $compte_file = 0;
  while ($file = readdir($handle)){
    if (is_dir($file) && $file != '.' && $file != '..')
    {
       $content = '';
       $compte_file++;

       $enfant = 0;
       $opale = 0;
       $mon_dir = opendir($file);
       if (isset($OpaleManifest))
          unset($OpaleManifest);
       if (isset($IsTincan))
           unset($IsTincan);
       $IsTincan = 0;
       while ($fils = readdir($mon_dir))
       {
          if ($fils != '.' && $fils != '..')
          {
             if ($fils == 'tincan.xml')
                $IsTincan = 1;
             $enfant ++;
             if ($fils == 'res' || $fils == 'co' || $fils == 'skin' || $fils == 'tplRes' || $fils == 'svc' ||
                  $fils == 'jslib' || $fils == 'transf' || $fils == 'wdgt' || $fils == 'imsmanifest.xml')
             {
                
                if ($fils == 'imsmanifest.xml')
                {
                   //mettre en commentaire $OpaleManifest = 1;)
                  $leXml = file_get_contents("$file/$fils");
                  //$leXml = htmlentities($leXml,ENT_QUOTES,'iso-8859-1');
                  $NbItems = substr_count(strtolower($leXml), '</item>');
                  $OpaleManifest = ($NbItems == 1) ? 1 : 0;//echo $NbItems.'<br />';
                }
                else
                  $opale++;
             }
          }
       }
       closedir($mon_dir) ;

       $droit=1;
       if (strstr($dir,"devoirs") && $typ_user == "APPRENANT")
       {
         $nom_grp =GetDataField ($connect,"select grp_nom_lb from groupe where grp_cdn = $file","grp_nom_lb");
       }
       if ($communes_groupe == 1 && $dir == $base && $typ_user != "APPRENANT" && $typ_user != "TUTEUR")
       {
           $nom_grp =GetDataField ($connect,"select grp_nom_lb from groupe where grp_cdn = $file","grp_nom_lb");
           $resp_grp =GetDataField ($connect,"select grp_resp_no from groupe where grp_cdn = $file","grp_resp_no");
           $id_grp = $file;
           if ($typ_user == "FORMATEUR_REFERENT" || $typ_user == "RESPONSABLE_FORMATION")
           {
              $requete = mysql_query ("select distinct utilgr_utilisateur_no from prescription_$id_grp,utilisateur_groupe,groupe,tuteur".
                         " WHERE  (presc_utilisateur_no = utilgr_utilisateur_no AND ".
                         "(presc_formateur_no = '$id_user' OR presc_prescripteur_no = '$id_user') ".
                         "AND utilgr_groupe_no = presc_grp_no ".
                         "AND utilgr_groupe_no = '$id_grp') OR (utilgr_groupe_no = grp_cdn ".
                         "AND groupe.grp_resp_no = $id_user AND groupe.grp_cdn = '$id_grp')");
             $nombre = mysql_num_rows($requete);
              if ($nombre > 0)
                 $droit = 1;
              else
                 $droit = 0;
           }
       }
       if ($droit == 1)
       {
           $rep =$dir."/".$file;
           $sousrep = $dir."/".$file;
           $rallongee=$dir;
           $rallonge=$rallongee."/".$file;
           $fic_img=mimetype($file,"image");
           $lien = "modif_rep_fic.php?id_grp=$id_grp&formateurs=$formateurs&nom_grp=$nom_grp&communes_groupe=$communes_groupe&rallonge=$rallonge&sousdos=$sousrep&parent=$dir&dossier=$rep&fichier=$file&direct=dossier";
           //$lien = urlencode($lien);
           if (strstr($dir,"devoirs") && $der_dos == "devoirs" && $typ_user != "APPRENANT" && strstr($file,"--"))
           {
             $item = explode("--",$file);
             $requete = mysql_query("SELECT count(*) from utilisateur where util_typutil_lb = 'APPRENANT' AND util_cdn = '$item[1]' AND util_login_lb = '$item[0]'");
             $nb_count = mysql_result($requete,0);
             if ($nb_count == 1)
             {
                $prenom = GetDataField ($connect,"select util_prenom_lb from utilisateur where util_typutil_lb = 'APPRENANT' AND util_cdn = '$item[1]' AND util_login_lb = '$item[0]'","util_prenom_lb");
                $lenom = GetDataField ($connect,"select util_nom_lb from utilisateur where util_typutil_lb = 'APPRENANT' AND util_cdn = '$item[1]' AND util_login_lb = '$item[0]'","util_nom_lb");
                $affiche_nom = str_replace($item[0],$lenom,$file);
                $affiche_nom = str_replace($item[1],$prenom,$affiche_nom);
                $affiche_nom = str_replace("--"," ",$affiche_nom);
             }
             else
             {
                $affiche_nom = $messNV;
             }
             $suite_mess = $affiche_nom;
           }
           elseif (strstr($parent,"devoirs") && strstr($dir,"--") && $typ_user != "APPRENANT" && !strstr($file,"--"))
           {
              $nom_grp =GetDataField ($connect,"select grp_nom_lb from groupe where grp_cdn = $file","grp_nom_lb");
              $suite_mess = $nom_grp;
           }
           elseif ($typ_user == "APPRENANT" || $typ_user == "TUTEUR")
             $suite_mess = $nom_grp;
           elseif (($typ_user != "APPRENANT" || $typ_user == "TUTEUR") && !strstr($parent,"devoirs"))
           {
             if ($communes_groupe == 1)
               $suite_mess = $nom_grp;
             else
               $suite_mess = $file;
           }
           if ($file == "devoirs" && $typ_user == "TUTEUR"){}else{
              $afficher = "$mess_casier_derep $suite_mess";
              $content .= "<TD style=\"width: 30px; text-align:left;\"><A HREF=\"$lien\" ".
                   bulle($afficher,"","LEFT","ABOVE",250).
                   "<img src=\"images/$fic_img\" border='0'></A></TD>";
              $afficher = "";
           }
           $lien = "modif_rep_fic.php?id_grp=$id_grp&formateurs=$formateurs&nom_grp=$nom_grp&communes_groupe=$communes_groupe&rallonge=$rallonge&sousdos=$sousrep&parent=$dir&dossier=$rep&fichier=$file&direct=dossier";
           //$lien = urlencode($lien);
           if ($communes_groupe == 1 && $base == $dir && $typ_user != "APPRENANT" && $typ_user != "TUTEUR")
              $afficher = $nom_grp;
           elseif ($file == "devoirs" && $base == $dir)
              $afficher = "$mess_trx_rendus";
           elseif ($file == "ressources" && $base == $dir)
              $afficher = "$mess_mes_docs";
           elseif (strstr($dir,"devoirs") && $typ_user == "APPRENANT")
              $afficher = $nom_grp;
           elseif (strstr($parent,"devoirs") && strstr($dir,"--") && $typ_user != "APPRENANT")
              $afficher = $nom_grp;
           elseif (strstr($dir,"devoirs") && $der_dos == "devoirs" && $typ_user != "APPRENANT" && strstr($file,"--"))
              $afficher = $affiche_nom;
           else
              $afficher = $file;
           if ($file == "devoirs" && $typ_user == "TUTEUR"){}else{
             $content .= "<TD nowrap valign='center' width='".(strlen($afficher)*6+40)."' align=left>";
             $content .= "<DIV id='sequence' style='nowrap;'><span style='float:left;'><A href=\"$lien\" title=\"$mess_casier_derep $suite_mess\">";
             $content .= "$afficher</A></span>";

             $sqlOpale = mysql_query("SELECT ress_cdn,ress_cat_lb FROM ressource_new WHERE ress_url_lb like '%$file/index.html%' ");
             $nbr_Opale = mysql_num_rows($sqlOpale);
             //if ($nbr_Opale == 0)
             //echo "$file $opale > 2 && $nbr_Opale == 0 && $IsTincan == 0 && (!isset($OpaleManifest) || (isset($OpaleManifest) && $OpaleManifest == 1)<br>";
             if ($opale > 2 && $nbr_Opale == 0 && $IsTincan == 0 &&
                (!isset($OpaleManifest) || (isset($OpaleManifest) && $OpaleManifest == 1))  &&
                 !strstr($rallonge,"Ressources_Scorm") && !strstr($rallonge,"Ressources_Aicc"))
             {
                 $suiteOpale = (isset($OpaleManifest) && $OpaleManifest == 1) ? '&manifest=1' : '';
                 $content .=  "<span style='float:left;margin-left:4px;'><A href=\"javascript:void(0);\" onClick=\"javascript:
                 $.ajax({
                         type: 'GET',
                         url: 'admin/modif_nb.php',
                         data: 'opale=1$suiteOpale&titre=$file&RepTincan=$rallonge',
                         success: function(msg)
                         {
                               alert(msg);
                               setTimeout(function(){document.location.reload()},200);
                         }
                 });\" ".
                 " title=\"Transformer cette ressource Opale en ressource xAPI TinCan. Vous pourrez ainsi activer un tracking total sur le LRS (Learning Record Storage) de Formagri.\">";
                $content .= "<img src='images/gest_parc/xApi.gif' border='0' style='float:left;'></A></span></DIV>";
             }

             $content .= "</TD>";
             if ($enfant > 0)
                $content .= "<TD nowrap valign='center'>".descendance($file."/", $recursive=TRUE)."</td>";
             else
                $content .= "<TD nowrap valign='center'>&nbsp;</td>";
             $content .= "<TD nowrap valign='center'>".
                  result_taille(DirSize($file.'/',$recursive=TRUE))."</TD>".
                  "<TD valign='center'>".date_modif($file)."</TD>";
           }
           if ($communes_groupe == 1){
             $resp_grp =GetDataField ($connect,"select grp_resp_no from groupe where grp_cdn = $id_grp","grp_resp_no");
             if ($typ_user == "RESPONSABLE_FORMATION")
             {
               if ($resp_grp == $id_user)
                 $mon_droit = 1;
             }
             elseif ($typ_user == "ADMINISTRATEUR")
                 $mon_droit = 1;
           }
           $requete = mysql_query("SELECT ech_auteur_no from echange_grp where ech_path_lb = '$rep'");
           $nbr_req = mysql_num_rows($requete);
           if ($nbr_req == 1)
           {
              $id_util = mysql_result($requete,0,"ech_auteur_no");
              $typ_util  = GetdataField ($connect,"select util_typutil_lb from utilisateur where util_cdn='$id_util'","util_typutil_lb");
              $nom_util  = GetdataField ($connect,"select util_nom_lb from utilisateur where util_cdn='$id_util'","util_nom_lb");
              $prenom_util  = GetdataField ($connect,"select util_prenom_lb from utilisateur where util_cdn='$id_util'","util_prenom_lb");
              $photo  = GetdataField ($connect,"select util_photo_lb from utilisateur where util_cdn='$id_util'","util_photo_lb");
              $majuscule = $prenom_util." ".$nom_util;
              $majuscule = ucwords(strtolower($majuscule));
              if ($photo != "")
                 $image = "<IMG SRC=\"images/$photo\" width='25' height='25' border='0'>";
              else
                 $image = "<IMG SRC=\"images/repertoire/icoptisilhouet.gif\" width='25' height='25' border='0'>";
              $lien = "prescription.php?id_util=$id_util&identite=1&affiche_fiche_app=1";
              $lien = urlencode($lien);
              $afficher = "$mess_fav_aut : $majuscule --> $mess_suite_fp";
              if ($file == "devoirs" && $typ_user == "TUTEUR")
              {}
              else
              {
                $content .= "<TD align='center'><A HREF=\"trace.php?link=$lien\" ".
                     bulle($afficher,"","LEFT","ABOVE",250).
                     $image."</A></TD>";
                $afficher = "";
              }
           }
           else
           {
              if ($file == "devoirs" && $typ_user == "TUTEUR")
              {}
              else
              {
                 $content .= "<TD>&nbsp;</TD>";
              }
           }
           $poids_doss = result_taille(DirSize($file.'/',$recursive=TRUE));
           $requete = mysql_query("SELECT ech_auteur_no from echange_grp where ech_path_lb = '$rep' AND ech_auteur_no = $id_user");
           $nbr_req = mysql_num_rows($requete);
           if ($communes_groupe == 1 || $formateurs == 1)
           {
              $content .= "<TD>&nbsp;</TD><TD>&nbsp;</TD><TD>&nbsp;</TD><TD align='center'>";
              if (isset($lien_origine) && $lien_origine != '')
              {
                 $lien = "modif_rep_fic.php?id_grp=$id_grp&formateurs=$formateurs&communes_groupe=$communes_groupe&rallonge=$rallonge&objet=fin_mouve&dos=$rep&fic=$file&direct=application";
                 $lien = urlencode($lien);
                 $content .= "<A href=\"trace.php?link=$lien\" ".bulle("$mess_rep_ins_rep","","LEFT","ABOVE",200);
                 $content .= "<IMG SRC=\"images/aller_retour_haut.gif\" border=0></A>";
              }
              $content .= "</TD>";

              $lien = "modif_rep_fic.php?id_grp=$id_grp&formateurs=$formateurs&communes_groupe=$communes_groupe&rallonge=$rallonge&objet=zipper&sousdos=$rep&parent=$dir&fic=$file&direct_zip=mon_dossier&nom_dossier=$file&direct=application";
              $lien = urlencode($lien);
              $content .= "<TD align='center'><A href=\"trace.php?link=$lien\" ".bulle("$mess_zip","","LEFT","ABOVE",60);;
              $content .= "<IMG SRC=\"images/zip.gif\" border=0></A></TD>";

           }
           if ($communes_groupe != 1 && $formateurs != 1)
           {
              $content .= "<TD>&nbsp;</TD>";

              if (isset($lien_origine) && $lien_origine != '' && !strstr($rallonge,'messagerie') && !strstr($rallonge,'devoirs'))
              {
                 $lien = "modif_rep_fic.php?id_grp=$id_grp&formateurs=$formateurs&communes_groupe=$communes_groupe&rallonge=$rallonge&objet=fin_mouve&dos=$rep&fic=$file&direct=application";
                 $lien = urlencode($lien);
                 $content .= "<TD>&nbsp;</TD><TD align='center'><A href=\"trace.php?link=$lien\" ".bulle("$mess_rep_ins_rep","","LEFT","ABOVE",200);
                 $content .= "<IMG SRC=\"images/aller_retour_haut.gif\" border=0></A></TD>";
              }
              else
                 $content .= "<TD>&nbsp;</TD><TD>&nbsp;</TD>";
              $lien = "modif_rep_fic.php?id_grp=$id_grp&formateurs=$formateurs&communes_groupe=$communes_groupe&rallonge=$rallonge&objet=zipper&sousdos=$rep&parent=$dir&fic=$file&direct_zip=mon_dossier&nom_dossier=$file&direct=application";
              $lien = urlencode($lien);
              $content .= "<TD align='center'><A href=\"trace.php?link=$lien\" ".bulle("$mess_zip","","LEFT","ABOVE",60);
              $content .= "<IMG SRC=\"images/zip.gif\" border=0></A></TD>";
           }
           elseif ($communes_groupe != 1 && $formateurs != 1 && $typ_user != "APPRENANT" && $base == $dir)
              $content .= "<TD colspan='2'>&nbsp;</TD>";
           elseif (strstr($rallonge,"$mess_devoirs") && $typ_user == "APPRENANT")
              $content .= "<TD colspan='3'>&nbsp;</TD>";
           if ($dir == $base && ($formateurs !=1 || (strstr($dir,"$base/devoirs") && $typ_user == 'APPRENANT') || (($communes_groupe == 1 || $formateurs ==1) && ($nbr_req == 0 && $typ_user != 'ADMINISTRATEUR')) || ($dir != $base && $formateurs !=1 && (($file == 'devoirs' && $typ_user == 'APPRENANT') || ($communes_groupe == 1 && $mon_droit == 1 && $file > 0) || ($communes_groupe == 1 && $nom_grp != '')))))
           {
              if ($file == "devoirs" && $typ_user == "TUTEUR"){}else
              {
                $content .= "<TD colspan='3'>&nbsp;</TD>";
              }
           }
           else
           {
              $lien = "modif_rep_fic.php?id_grp=$id_grp&formateurs=$formateurs&communes_groupe=$communes_groupe&objet=supprimer&rallonge=$rallonge&sousdos=$rep&parent=$dir&fichier=$file&direct=dossier";
              $lien = urlencode($lien);
              if ($affiche_nom == $messNV)
                  $lien .="%26nv%3D1";
              if ($enfant == 0 && !strstr($file,"messagerie"))
              {
                 if ((!strstr($rallonge,"$mess_devoirs") && $typ_user == 'APPRENANT') || $typ_user != 'APPRENANT')
                 {
                     $afficher = "$mess_casier_sup_rep_ck $aff_rep/$file<BR>$mess_casier_cond_sup_rep";
                     $content .= "<TD align='center'><A href=\"javascript:void(0);\" onclick=\"javascript:return(confm('trace.php?link=$lien'));\"".
                          " onmouseover=\"img_sup$compte_file.src='images/messagerie/icoGpoubelb.gif';overlib('".addslashes($afficher)."',ol_hpos,LEFT,ABOVE,WIDTH,'250',DELAY,'800')\"".
                          " onmouseout=\"img_sup$compte_file.src='images/messagerie/icoGpoubel.gif';nd();\">".
                          "<IMG NAME=\"img_sup$compte_file\" SRC=\"images/messagerie/icoGpoubel.gif\" width='13' height='18' BORDER='0'ALT=\"$mess_casier_supfic\"".
                          " onLoad=\"tempImg=new Image(0,0); tempImg.src='images/messagerie/icoGpoubelb.gif'\"></A></TD>";
                          $afficher = "";
                 }
              }
              elseif ($enfant > 0 && (strstr($rallonge,$mess_devoirs) && $typ_user != 'APPRENANT' && $affiche_nom == $messNV))
              {
                     $afficher = "$mess_casier_sup_rep_ck $aff_rep/$file<br /><strong>$messNV : <br />$messNoAppNV</strong>";
                     $content .= "<TD align='center'><A href=\"javascript:void(0);\" onclick=\"javascript:return(confm('trace.php?link=$lien'));\"".
                          " onmouseover=\"img_sup$compte_file.src='images/messagerie/icoGpoubelb.gif';overlib('".addslashes($afficher)."',ol_hpos,LEFT,ABOVE,WIDTH,'250',DELAY,'800')\"".
                          " onmouseout=\"img_sup$compte_file.src='images/messagerie/icoGpoubel.gif';nd();\">".
                          "<IMG NAME=\"img_sup$compte_file\" SRC=\"images/messagerie/icoGpoubel.gif\" width='13' height='18' BORDER='0'ALT=\"$mess_casier_supfic\"".
                          " onLoad=\"tempImg=new Image(0,0); tempImg.src='images/messagerie/icoGpoubelb.gif'\"></A></TD>";
                          $afficher = "";
              }
              elseif (strstr($rallonge,"$mess_devoirs") && $typ_user == 'APPRENANT')
              {
                 $content .= "<TD>&nbsp;</TD>";
              }
              else
              {
                 $afficher = "$mess_nb_enf_dos : $enfant<br /> $mess_taille = $poids_doss<BR>$mess_casier_cond_sup_rep";
                 $content .= "<TD align='center'><A HREF=\"javascript:void(0);\" ".bulle($afficher,"","LEFT","ABOVE",250)."<img src=\"images/repertoire/icoptiinterdit.gif\" border='0'></A></TD>";
                 $afficher = "";
              }
           }
           if ($file == "devoirs" && $typ_user == "TUTEUR")
           {}
           else
             $content .= "</TR>";
       }
       $tab_file[strtolower($file)] = $content;
    }
 }
 if (count($tab_file) > 0)
 {
    ksort($tab_file);
    /* echo "<pre>";
          print_r($nom_file);
       echo "</pre>";
    */
    $ap = 0;
    foreach ($tab_file as $key => $val)
    {
         $ap++;
         echo couleur_tr($ap+1,'');
         echo $val;
    }
 }
 closedir($handle);
 $tab_file = NULL;
 $handle=opendir("./");
 $passe_box = 0;
 $compteur_file=0;
 $compter_fichiers = 0;
  while ($file = readdir($handle))
  {
    if (!is_dir($file) && $file != '.' && $file != '..')
    {
        $content = '';
        $compte_file++;
        $compteur_file++;
        if ($communes_groupe != 1 && $formateurs != 1 && $typ_user != "APPRENANT")
        {
           $passe_box = 1;
           $content .= "<FORM name='form_supp' action =\"modif_rep_fic.php?objet=supp_lot\" METHOD='POST'>";
           ?>
           <script language="JavaScript" type="text/javascript">        <!--
              function CheckAll() {
                for (var j = 0; j < document.form_supp.elements.length; j++) {
                   if(document.form_supp.elements[j].type == 'checkbox'){
                     document.form_supp.elements[j].checked = !(document.form_supp.elements[j].checked);
                   }
                }
               }
           //--></script>
           <?php
        }
        $content .= "<TD style='text-align:left;'>";
        $rep=$dir;
        $rallongee=$dir;
        $rallonge=$rallongee."/";
        list($extension,$nom)= getextension($file);
        if (in_array(strtolower($extension), array("htm")) && $typ_user != "APPRENANT" &&
            !strstr($rallonge,"Ressources_Scorm") && !strstr($rallonge,"Ressources_Aicc"))
        {
                 $ContentFileQF = file_get_contents($file);
                 if (strstr($ContentFileQF,'QuizFaber') && strstr($ContentFileQF,'session_start'))
                 {
                    $ContentFileQFz = str_replace(urldecode("%3C%3F+session_start%28%29+%3F%3E"),'',$ContentFileQF);
                    $ContentFileQFz = str_replace(urldecode("%3C%3Fphp+session_start%28%29+%3F%3E"),'',$ContentFileQFz);
                    $fpQz = fopen($file, "w+");
                        $fwQz = fwrite($fpQz,$ContentFileQFz);
                    fclose($fpQz);
                    chmod($file,0775);
                 }
        }
        elseif(in_array(strtolower($extension), array("php","html")) && $typ_user != "APPRENANT" &&
                 !strstr($rallonge,"Ressources_Scorm") && !strstr($rallonge,"Ressources_Aicc"))
        {
                 $ContentFileQF = file_get_contents($file);
                 if (strstr($ContentFileQF,'QuizFaber') && !strstr($ContentFileQF,"var id_act=LireCookie('id_act');") &&
                     strstr($ContentFileQF,"quiz_main.document.tracking.aza.value"))
                 {
                    $ContentFileQF1 = str_replace("var qmakeURL","var id_act=LireCookie('id_act');\n".
                                                            "var monpath = LireCookie('monpath');\n".
                                                            "function getCookieVal(offset)\n".
                                                            "{\n".
                                                            "   var endstr=document.cookie.indexOf (';', offset);\n".
                                                            "   if (endstr==-1)\n".
                                                            "   endstr=document.cookie.length;\n".
                                                            "   return unescape(document.cookie.substring(offset, endstr));\n".
                                                            "}\n".
                                                            "function LireCookie(nom)\n".
                                                            "{\n".
                                                            "   var arg=nom+'=';\n".
                                                            "   var alen=arg.length;\n".
                                                            "   var clen=document.cookie.length;\n".
                                                            "   var i=0;\n".
                                                            "   while (i<clen)\n".
                                                            "   {\n".
                                                            "         var j=i+alen;\n".
                                                            "         if (document.cookie.substring(i, j)==arg)\n".
                                                            "            return getCookieVal(j);\n".
                                                            "         i=document.cookie.indexOf(' ',i)+1;\n".
                                                            "         if (i==0)\n".
                                                            "            break;\n".
                                                            "   }\n".
                                                            "   return null;\n".
                                                            "}\n".
                                                            "var qmakeURL",$ContentFileQF);
                    $ContentFileQFz = str_replace("action='/act_qf.php'","action='\"+monpath+\"/act_qf.php'",$ContentFileQF1);
                    $ContentFileQFz = str_replace("='media/'","='\"+monpath+\"/images/media/'",$ContentFileQFz);
                    $ContentFileQFz = str_replace("name='id_act' value='".urldecode("%3C%3F+echo+\$id_act+%3F%3E")."'","name='id_act' value='\"+id_act+\"'",$ContentFileQFz);
                    $ContentFileQFz = str_replace("var mediaDir  = 'media';","var mediaDir  = monpath+'/images/media';",$ContentFileQFz);
                    $ContentFileQFz = str_replace(urldecode("%3C%3F+session_start%28%29+%3F%3E"),'',$ContentFileQFz);
                    $ContentFileQFz = str_replace(urldecode("%3C%3Fphp+session_start%28%29+%3F%3E"),'',$ContentFileQFz);
                    $fpQz = fopen($nom.".html", "w+");
                        $fwQz = fwrite($fpQz,$ContentFileQFz);
                    fclose($fpQz);
                    chmod($nom.".html",0775);
                    if (strstr($file,'.php'))
                    {
                        $AncienFile = $file;
                        unlink($file);
                        $file = $nom.".html";
                        $reqRess = mysql_query("update ressource_new set `ress_url_lb` = replace (`ress_url_lb`, '$AncienFile', '$file')
                                               where `ress_url_lb` like '%".$rallonge.$AncienFile."%'");
                    }
                 }
        }
        $content .= "<img src=\"images/".mimetype($file,"image")."\"></TD>";
        $aller_fichier = $rep."/".$file;
        if ($communes_groupe != 1 && $formateurs != 1 && $typ_user != "APPRENANT" )
        {
           $content .= "<INPUT type='hidden' name= 'formateurs' value=\"$formateurs\">";
           $content .= "<INPUT type='hidden' name= 'communes_groupe' value=\"$communes_groupe\">";
           $content .= "<INPUT type='hidden' name= 'id_grp' value=\"$id_grp\">";
           $content .= "<INPUT type='hidden' name= 'rallonge' value=\"$rallonge\">";
           $content .= "<INPUT type='hidden' name= 'dos' value=\"$rep\">";
           $content .= "<INPUT type='hidden' name= 'direct' value=\"dossier\">";
           $content .= "<INPUT type='hidden' name= \"fiche[$compteur_file]\" value=\"$file\">";
           $content .= "<INPUT type='hidden' name= 'titre_dossier' value=\"$der_dos\">";
        }
        $lien = $aller_fichier;
        if (strstr(strtolower($lien),".doc") || strstr(strtolower($lien),".xls") || strstr(strtolower($lien),".xlt"))
            $content .= "<TD style='text-align:left;' nowrap width='".(strlen($file)*6+40)."' valign='center'><DIV id='sequence'><A href=\"$lien\" target='_blank'>$file</A>";
        elseif (strstr(strtolower($lien),".flv") ||
                strstr(strtolower($lien),".mp3") ||
                strstr(strtolower($lien),".mp4") ||
                strstr(strtolower($lien),'.webm') ||
                strstr(strtolower($lien),'.ogv'))
        {
                 $actit++;
                 $media_act = $lien;
                 if (strstr(strtolower($lien),".flv") ||
                    strstr(strtolower($lien),".mp3"))
                 {
                    $actit = $compteur_file;
                    $media_act = $adresse_http.'/'.$lien;
                    $largeur = "154";
                    $hauteur = "100";
                    $content .= "<TD style='text-align:left;' nowrap width='".(strlen($file)*6+40)."' valign='center'><table><tr><td><DIV id='sequence'>".
                                "<A href='#' onclick=\"javascript:window.open('$lien','','resizable=yes,scrollbars=no,location=no,status=no,width=640, height=480')\">$file</A></td></tr>";
                    $content .= "<TR><TD><div id='insertMedia$compte_file'>";
                    $content .= '<div id="player'.$actit.'" style="clear:both;"></div>';
                    $content .= '<script type="text/javascript">
	                         var s'.$actit.' = new SWFObject("ressources/flvplayer.swf","single","'.$largeur.'","'.$hauteur.'","7");
	                         s'.$actit.'.addParam("allowscriptaccess","always");
	                         s'.$actit.'.addParam("allowfullscreen","true");
	                         s'.$actit.'.addParam("wmode","transparent");
	                         s'.$actit.'.addVariable("file","'.$media_act.'");
	                         s'.$actit.'.addVariable("image","images/menu/logformb.gif");
	                         s'.$actit.'.addVariable("backcolor","0xFFFFFF");
	                         s'.$actit.'.addVariable("frontcolor","0x000000");
	                         s'.$actit.'.addVariable("lightcolor","0xFF0000");
	                         s'.$actit.'.addVariable("screencolor","0x000000");
	                         s'.$actit.'.write("player'.$actit.'");
                       </script>';
                    $content .= "</div></TD></TR></TABLE>";
                 }
                 else
                 {
                    $largeur = "154";
                    $hauteur = "100";
                    $content .= "<TD style='text-align:left;' nowrap width='".(strlen($file)*6+40)."' valign='center'>".
                                "<table cellspacing=0 cellpadding=0><tr><td><DIV id='sequence'> ".
                                "<A href='#' onclick=\"javascript:window.open('lanceMedia.php?id_rep=".urlencode($lien).
                                "','','resizable=yes,scrollbars=yes,status=no')\">$file</A></td></tr>";
                    $content .= '<TR><TD><iframe src="lanceMedia.php?id_rep='.urlencode($lien).'&largeur='.$largeur.'&hauteur='.$hauteur.
                    '" width="174" height="120" frameborder=0 scrolling="no"></iframe></TD></TR></TABLE>';
                 }
        }
        else
            $content .= "<TD style='text-align:left;' nowrap width='".(strlen($file)*6+40)."' valign='center'>".
                        "<DIV id='sequence'><A href='#' onclick=\"javascript:window.open('$lien','','resizable=yes,scrollbars=yes,status=no')\">$file</A>";
        $content .= "</TD><td>&nbsp;</td><TD nowrap valign='center'>".
                    taille($file)."</TD>".
                    "<TD valign='center'>".date_modif($file)."</TD>";
        if ($communes_groupe == 1 || $formateurs == 1)
        {
             $nbr_req = 0;
             $mon_droit = 0;
             if ($communes_groupe == 1)
             {
                $resp_grp =GetDataField ($connect,"select grp_resp_no from groupe where grp_cdn = $id_grp","grp_resp_no");
                if ($typ_user == "RESPONSABLE_FORMATION")
                {
                   if ($resp_grp == $id_user)
                      $mon_droit = 1;
                }
                if ($typ_user == "ADMINISTRATEUR")
                   $mon_droit = 1;
             }
             $requete = mysql_query("SELECT ech_auteur_no from echange_grp where ech_path_lb = '$aller_fichier'");
             $nbr_req = mysql_num_rows($requete);
             $id_util = '';$typ_util = '';
             if ($nbr_req == 1)
             {
                $id_util = mysql_result($requete,0,"ech_auteur_no");
                $typ_util  = GetdataField ($connect,"select util_typutil_lb from utilisateur where util_cdn='$id_util'","util_typutil_lb");
                $nom_util  = GetdataField ($connect,"select util_nom_lb from utilisateur where util_cdn='$id_util'","util_nom_lb");
                $prenom_util  = GetdataField ($connect,"select util_prenom_lb from utilisateur where util_cdn='$id_util'","util_prenom_lb");
                $photo  = GetdataField ($connect,"select util_photo_lb from utilisateur where util_cdn='$id_util'","util_photo_lb");
                $majuscule = $prenom_util." ".$nom_util;
                $majuscule = ucwords(strtolower($majuscule));
                $lien = "prescription.php?id_util=$id_util&identite=1&affiche_fiche_app=1";
                $lien = urlencode($lien);
                $afficher = "$mess_fav_aut : $majuscule --> $mess_suite_fp";
                if ($photo != "")
                      $image = "<IMG SRC=\"images/$photo\" width='25' height='25' border='0'>";
                else
                      $image = "<IMG SRC=\"images/repertoire/icoptisilhouet.gif\" width='25' height='25' border='0'>";
                $content .= "<TD align='center'><A HREF=\"trace.php?link=$lien\" ".
                     bulle($afficher,"","LEFT","ABOVE",250).
                     "$image</A></TD>";
                $afficher='';
                if ($id_util == $id_user)
                    $mon_droit = 1;
             }
             else
                $content .= "<TD>&nbsp;</TD>";
             if (($nbr_req > 0 && ($id_util == $id_user || $mon_droit == 1)) || $typ_user == "ADMINISTRATEUR")
             {
               $lien = "modif_rep_fic.php?id_grp=$id_grp&formateurs=$formateurs&communes_groupe=$communes_groupe&rallonge=$rallonge&objet=renommer&dos=$rep&fic=$file&direct=application";
               //$lien = urlencode($lien);
               $content .= "<TD align='center'><A href=\"javascript:void(0);\" ".
                    "onClick=\"javascript:window.open('$lien','','scrollbars=no,resizable=yes,width=400,height=60,left=300,top=300')\"".
                    bulle("$mess_casier_ren_fic","","LEFT","ABOVE",150).
                    "<img src=\"images/repertoire/icoGrenomfich20.gif\" border=\"0\"></A></TD>";
               list($extension,$nom)=getextension($file);
               if (in_array(strtolower($extension), array("txt","des","crs","au","cst","ort")))
               {
                  $lien = "modif_rep_fic.php?id_grp=$id_grp&formateurs=$formateurs&communes_groupe=$communes_groupe&rallonge=$rallonge&objet=editer&dos=$rep&fic=$file&direct=application";
                  $lien = urlencode($lien);
                  $content .= "<TD align='center'><A href=\"trace.php?link=$lien\" ".
                              bulle("$mess_casier_edfic","","LEFT","ABOVE",120);
                  $content .= "<img src=\"images/repertoire/icoptiedit.gif\" border='0'></A></TD>";
               }
               elseif ((in_array(strtolower($extension), array("html")) ||
                       in_array(strtolower($extension), array("htm"))))
               {
                 $lien = "edit_box.php?id_grp=$id_grp&rallonge=$rep&formateurs=$formateurs&communes_groupe=$communes_groupe&objet=&dos=$rep&fichier=$file&direct=1";
                 //$lien = urlencode($lien);
                 $content .= "<TD align='center'><A href=\"#\" ".
                      "onclick=\"javascript:window.open('$lien','','width=800,height=700,resizable=yes,scrollbar=yes,status=no')\"".
                      bulle("$mess_casier_edfic"."$mess_casier_edfic1","","LEFT","ABOVE",250).
                      "<img src=\"images/repertoire/icoptiedit.gif\" border='0'></A></TD>";
               }
               else
                  $content .= "<TD>&nbsp;</TD>";
               if ((!isset($lien_origine) || $lien_origine == '') &&
                  (($nbr_req > 0 || ($mon_droit == 1 && $id_user != "ADMINISTRATEUR") || $typ_user == "ADMINISTRATEUR")))
               {
                  $lien = "modif_rep_fic.php?id_grp=$id_grp&formateurs=$formateurs&communes_groupe=$communes_groupe&rallonge=$rallonge&objet=mouver&dos=$rep&fic=$file&direct=application";
                  $lien = urlencode($lien);
                  $content .= "<TD>&nbsp;</TD><TD align='center'><A href=\"trace.php?link=$lien\" ".
                       bulle("$mess_depl_ficrep","","LEFT","ABOVE",250).
                       "<IMG SRC=\"images/aller_retour_haut.gif\" border=0></A></TD>";
               }
               else
                  $content .= "<TD>&nbsp;</TD>";
               if ((in_array(strtolower($extension), array("zip")) || in_array(strtolower($extension), array("gz"))) &&
                  ((strstr($dir,"/ressources")) || (($formateurs == 1 || $communes_groupe == 1) && ($id_user == $id_util || $typ_user == "ADMINISTRATEUR"))))
               {
                  $lien = "modif_rep_fic.php?id_grp=$id_grp&formateurs=$formateurs&communes_groupe=$communes_groupe&rallonge=$rallonge&objet=dezipper&dos=$rep&fic=$file&direct=application";
                  $lien = urlencode($lien);
                  $fichier = $file;
                  $larchive = new PclZip($fichier);
                  $nbzip = sizeof($larchive->listContent());
                  $titre_bulle = "$mess_dezip <br /> $mess_nb_enf_dos = $nbzip";
                  $content .= "<TD align='center'><A href=\"trace.php?link=$lien\" ".
                       bulle($titre_bulle,"","LEFT","ABOVE",210).
                       "<IMG SRC=\"images/zip.gif\" border=0></A></TD>";
                  $passager=1;
                }
                elseif ((!in_array(strtolower($extension), array("gz")) && !in_array(strtolower($extension), array("zip")) &&
                       strtolower($file) != "imsmanifest.xml" && strtolower($file) != "tincan.xml") && ($typ_user != "APPRENANT") &&
                       ((strstr($dir,"/ressources")) || (($formateurs == 1 || $communes_groupe == 1) && $typ_user == "ADMINISTRATEUR")))
                {
                  $lien = "modif_rep_fic.php?id_grp=$id_grp&formateurs=$formateurs&communes_groupe=$communes_groupe&rallonge=$rallonge&objet=zipper&dos=$rep&fic=$file&direct=application";
                  $lien = urlencode($lien);
                  $content .= "<TD align='center'><A href=\"trace.php?link=$lien\" ".
                       bulle("$mess_zip","","LEFT","ABOVE",50).
                       "<IMG SRC=\"images/zip.gif\" border=0></A></TD>";
                }else
                  $content .= "<TD>&nbsp;</TD>";
               $lien = "modif_rep_fic.php?id_grp=$id_grp&formateurs=$formateurs&communes_groupe=$communes_groupe&objet=supprimer&rallonge=$rallonge&dos=$rep&fic=$file&direct=application";
               $lien = urlencode($lien);
               $content .= "<TD align='center'><A href=\"javascript:void(0);\" onclick=\"javascript:return(confm('trace.php?link=$lien'));\"".
                      " onmouseover=\"img_sup$compte_file.src='images/messagerie/icoGpoubelb.gif';overlib('$mess_casier_supfic',ol_hpos,LEFT,ABOVE,WIDTH,'150',DELAY,'800')\"".
                      " onmouseout=\"img_sup$compte_file.src='images/messagerie/icoGpoubel.gif';nd();\">".
                      "<IMG NAME=\"img_sup$compte_file\" SRC=\"images/messagerie/icoGpoubel.gif\" BORDER='0' width='13' height='18'".
                      " onLoad=\"tempImg=new Image(0,0); tempImg.src='images/messagerie/icoGpoubelb.gif'\"></A></TD>";

             }
             elseif($nbr_req > 0 && ($mon_droit != 1 || $typ_user == "ADMINISTRATEUR"))
             {
               $content .= "<TD colspan='3'>&nbsp;</TD>";
               if ($typ_user != "APPRENANT" && in_array(strtolower($extension), array("txt")))
                  $content .= "<TD colspan='2'>&nbsp;</TD>";
             }
             else
                  $content .= "<TD colspan='6'>&nbsp;</TD>";
        }
        if ($communes_groupe != 1 && $formateurs != 1 && (!strstr($rallonge,"$mess_devoirs") || (strstr($rallonge,"$mess_devoirs") && $typ_user != "APPRENANT")))
        {
         $test_ress = $rallonge;
         $lien_ress = $test_ress.$file;
         $lien_ress_http = "$adresse_http/".$lien_ress;
         $sql=mysql_query("SELECT ress_cdn,ress_cat_lb FROM ressource_new WHERE ress_url_lb=\"$lien_ress\" OR ress_url_lb=\"$lien_ress_http\"");
         $nbr_result=mysql_num_rows($sql);
         $sql_messagerie=mysql_query("SELECT mess_cdn FROM messagerie WHERE mess_fichier_lb=\"$lien_ress\" AND id_user=$id_user");
         $nbr_result2=mysql_num_rows($sql_messagerie);
         if (strstr($dir,"devoirs") && $typ_user == "APPRENANT")
         {
             $content .=  "<TD>&nbsp;</TD><TD>&nbsp;</TD>";
         }else
         {
           if ($nbr_result2 > 0)
              $content .= "<TD align='center'><A HREF=\"javascript:void(0);\" ".
                   bulle("$mess_casier_messag","","LEFT","ABOVE",280).
                   "<IMG SRC=\"images/repertoire/icoptiinterdit.gif\" border=0></A></TD>";
           elseif ($nbr_result == 0 && !strstr($file,"bdd_formagri.zip")  &&
                  !strstr($rallonge,"Ressources_Scorm") && !strstr($rallonge,"Ressources_Aicc"))
           {
             $lien = "modif_rep_fic.php?id_grp=$id_grp&formateurs=$formateurs&communes_groupe=$communes_groupe&rallonge=$rallonge&objet=renommer&dos=$rep&fic=$file&direct=application";
             //$lien = urlencode($lien);
             $content .= "<TD align='center'><A href=\"javascript:void(0);\" ".
                  "onClick=\"javascript:window.open('$lien','','scrollbars=no,resizable=yes,width=400,height=60,left=300,top=300')\"".
                  bulle("$mess_casier_ren_fic","","LEFT","ABOVE",150).
                  "<img src=\"images/repertoire/icoGrenomfich20.gif\" border=\"0\"></A></TD>";
           }elseif($nbr_result > 0 && !strstr($file,"bdd_formagri.zip"))
           {
             $cat = mysql_result($sql,0,"ress_cat_lb");
             $content .= "<TD align='center'>";
             $affiche = "$cas_ress_ref1 [$cat].<BR> $cas_ress_ref3.";
             $content .= "<A HREF=\"javascript:void(0);\"".bulle($affiche,"","LEFT","ABOVE",250).
                  "<IMG SRC=\"images/repertoire/icoptiinterdit.gif\" border=0></A></TD>";
           }else
             $content .="<TD></TD>";
          list($extension,$nom)=getextension($file);
          if (in_array(strtolower($extension), array("txt","des","crs","au","cst","ort")) && $typ_user != "APPRENANT" &&
             $nbr_result == 0 && !strstr($rallonge,"Ressources_Scorm") && !strstr($rallonge,"Ressources_Aicc"))
          {
            $lien = "modif_rep_fic.php?id_grp=$id_grp&formateurs=$formateurs&communes_groupe=$communes_groupe&rallonge=$rallonge&objet=editer&dos=$rep&fic=$file&direct=application";
            $lien = urlencode($lien);
            $content .= "<TD align='center'><A href=\"trace.php?link=$lien\" ".
                 bulle("$mess_casier_edfic"."$mess_casier_edfic2","","LEFT","ABOVE",150).
                 "<img src=\"images/repertoire/icoptiedit.gif\" border='0'></A></TD>";
          }
          elseif ((in_array(strtolower($extension), array("html")) ||
                  in_array(strtolower($extension), array("htm"))) && $typ_user != "APPRENANT" &&
                  !strstr($rallonge,"Ressources_Scorm") && !strstr($rallonge,"Ressources_Aicc"))
          {
             $lien = "edit_box.php?id_grp=$id_grp&rallonge=$rep&formateurs=$formateurs&communes_groupe=$communes_groupe&objet=&dos=$rep&fichier=$file&direct=1";
             //$lien = urlencode($lien);
             $content .= "<TD align='center'><A href=\"#\" ".
                  "onclick=\"javascript:window.open('$lien','','width=800,height=700,resizable=yes,scrollbar=yes,status=no')\"".
                  bulle("$mess_casier_edfic"."$mess_casier_edfic1","","LEFT","ABOVE",250).
                  "<img src=\"images/repertoire/icoptiedit.gif\" border='0'></A></TD>";
          }
          else
            $content .="<TD></TD>";
            if (($nbr_result2 > 0 && strstr($dir,"messagerie")) || strstr($file,"bdd_formagri.zip"))
              $content .= "<TD></TD>";
            elseif ($typ_user != "APPRENANT" && $typ_user != "TUTEUR" && ((strstr($dir,"/ressources")) ||
                 ($formateurs == 1 && $typ_user == "ADMINISTRATEUR")) && !strstr($rallonge,"Ressources_Scorm") && !strstr($rallonge,"Ressources_Aicc"))
            {
            $lien = "recherche.php?flg=1&rep=".$rep."/".$file;
            $lien = urlencode($lien);
            $content .= "<TD align='center'><A href=\"trace.php?link=$lien\"".
                 bulle("$mess_casier_cre_lk"."$mess_casier_go_cat","","LEFT","ABOVE",250).
                 "<IMG SRC=\"images/disconnect.gif\" border=0></A></TD>";
            }else
               $content .="<TD></TD>";
            $content .= "<TD align='center'>";
            if ((!isset($lien_origine) || $lien_origine == '') &&
             !strstr($rallonge,"Ressources_Scorm") && !strstr($rallonge,"Ressources_Aicc") &&
             !strstr($file,"bdd_formagri.zip") && $base != $dir)
            {
               $lien = "modif_rep_fic.php?id_grp=$id_grp&formateurs=$formateurs&communes_groupe=$communes_groupe&rallonge=$rallonge&objet=mouver&dos=$rep&fic=$file&direct=application";
               $lien = urlencode($lien);
               $content .= "<A href=\"trace.php?link=$lien\" ".
                    bulle("$mess_depl_ficrep","","LEFT","ABOVE",250).
                    "<IMG SRC=\"images/aller_retour_haut.gif\" border=0></A>";
            }
            $content .= "</TD>";
            if (($nbr_result2 > 0 && strstr($dir,"messagerie")) || strstr($file,"bdd_formagri.zip"))
            {
               $passager=1;
            }elseif ((in_array(strtolower($extension), array("zip")) ||
                  in_array(strtolower($extension), array("gz"))) && ($typ_user != "APPRENANT") &&
                  ((strstr($dir,"/ressources")) || ($formateurs == 1 && $typ_user == "ADMINISTRATEUR")))
            {
                $lien = "modif_rep_fic.php?id_grp=$id_grp&formateurs=$formateurs&communes_groupe=$communes_groupe&rallonge=$rallonge&objet=dezipper&dos=$rep&fic=$file&direct=application";
                $lien = urlencode($lien);
                $fichier = $file;
                $larchive = new PclZip($fichier);
                $nbzip = sizeof($larchive->listContent());
                $titre_bulle = "$mess_dezip <br /> $mess_nb_enf_dos = $nbzip";
                $content .= "<TD align='center'><A href=\"trace.php?link=$lien\" ".
                     bulle($titre_bulle,"","LEFT","ABOVE",210).
                     "<IMG SRC=\"images/zip.gif\" border=0></A></TD>";
                $passager=1;
            }

            if (($nbr_result2 > 0 && strstr($dir,"messagerie")) || strstr($file,"bdd_formagri.zip") && (!isset($passager) || $passager != 1))
                $content .= "<TD></TD>";//
            elseif ((!in_array(strtolower($extension), array("gz")) &&
                 !in_array(strtolower($extension), array("zip")) &&
                 strtolower($file) != "imsmanifest.xml" && strtolower($file) != "tincan.xml") && ($typ_user != "APPRENANT") &&
                 ((strstr($dir,"/ressources")) || ($formateurs == 1 && $typ_user == "ADMINISTRATEUR")))
            {
                $lien = "modif_rep_fic.php?id_grp=$id_grp&formateurs=$formateurs&communes_groupe=$communes_groupe&rallonge=$rallonge&objet=zipper&dos=$rep&fic=$file&direct=application";
                $lien = urlencode($lien);
                $content .= "<TD align='center'><A href=\"trace.php?link=$lien\" ".
                     bulle("$mess_zip","","LEFT","ABOVE",50).
                     "<IMG SRC=\"images/zip.gif\" border=0></A></TD>";
            }
            elseif (strtolower($file) == "imsmanifest.xml" &&
                $typ_user != "APPRENANT" && $typ_user != "TUTEUR" && strstr($dir,"/Ressources_Scorm"))
            {
               $lien = "$adresse_http/parseur.php?file=$aller_fichier&prov=seq";
               $lien = urlencode($lien);
               $content .= "<TD align='center'><A href=\"trace.php?link=$lien\" ".
                    bulle("$mes_parse_xml","","LEFT","ABOVE",250).
                    "<IMG SRC=\"images/ico_xml.jpg\" border=0></A></TD>";
            }
            elseif (strtolower($file) == "tincan.xml" && $typ_user != "APPRENANT" && $typ_user != "TUTEUR")
            {
               //&& (strstr($dir,"/Ressources_TC") || strstr($dir,"/Ressources_TinCan"))
               $lien = "$adresse_http/parseurTinCan.php?file=$aller_fichier&prov=act";
               $lien = urlencode($lien);
               $content .= "<TD align='center'><A href=\"trace.php?link=$lien\" ".
                    bulle("Cliquez ici pour créer automatiquement une activité libre au standard TinCan Api xApi.","","LEFT","ABOVE",250).
                    "<img src='images/gest_parc/xApi.gif' border='0'></A></TD>";
            }
            elseif (!isset($passager) || $passager != 1)
               $content .="<TD></TD>";
            elseif (strstr($file,"bdd_formagri.zip"))
               $content .="<TD>&nbsp;</TD>";
            if ($nbr_result2 > 0 && strstr($dir,"messagerie"))
               $content .= "<TD align='center'><A HREF=\"javascript:void(0);\" ".
                    bulle("$mess_casier_messag","","LEFT","ABOVE",280).
                    "<IMG SRC=\"images/repertoire/icoptiinterdit.gif\" border=0></A></TD>";
            elseif ($nbr_result == 0 )
            {
                if ($typ_user != "APPRENANT")
                {
                    $content .= "<TD align='center'><input type='checkbox' name='supprime[$compteur_file]' ".bulle("$mess_cochez"." ".strtolower("$mess_casier_supfic"),"","LEFT","ABOVE",150)."</TD>";
                    $compter_fichiers++;
                }else
                {
                    $lien = "modif_rep_fic.php?id_grp=$id_grp&formateurs=$formateurs&communes_groupe=$communes_groupe&objet=supprimer&rallonge=$rallonge&dos=$rep&fic=$file&direct=application";
                    $lien = urlencode($lien);
                    $content .= "<TD align='center'><A href=\"javascript:void(0);\" onclick=\"javascript:return(confm('trace.php?link=$lien'));\"".
                         " onmouseover=\"img_sup$compte_file.src='images/messagerie/icoGpoubelb.gif';overlib('$mess_casier_supfic',ol_hpos,LEFT,ABOVE,WIDTH,'250',DELAY,'800')\"".
                         " onmouseout=\"img_sup$compte_file.src='images/messagerie/icoGpoubel.gif';nd();\">".
                         "<IMG NAME=\"img_sup$compte_file\" SRC=\"images/messagerie/icoGpoubel.gif\" BORDER='0' width='13' height='18' ALT=\"$mess_casier_supfic\"".
                         " onLoad=\"tempImg=new Image(0,0); tempImg.src='images/messagerie/icoGpoubelb.gif'\"></A></TD>";
                }
            }
            else
            {
               $cat = mysql_result($sql,0,"ress_cat_lb");
               $affiche = "$cas_ress_ref1 [$cat].<BR> $cas_ress_ref2.";
               $content .= "<TD align='center'><A HREF=\"javascript:void(0);\" ".
                    bulle($affiche,"","LEFT","ABOVE",250).
                    "<IMG SRC=\"images/repertoire/icoptiinterdit.gif\" border=0></A></TD>";
             }
           }
           if ($typ_user == "APPRENANT")
               $content .="<TD></TD>";
          $passager = 0;
      }
      elseif(strstr($rallonge,"$mess_devoirs") && $typ_user == "APPRENANT")
          $content .= "<TD colspan='6'>&nbsp;</TD>";
       echo"</TR>";
       $tab_file[strtolower($file)] = $content;
    }
 }
 if (count($tab_file) > 0){
    ksort($tab_file);
    foreach ($tab_file as $key => $val) {
         $ap++;
         echo couleur_tr($ap+1,'');
     echo $val;
    }
 }
 closedir($handle);
 if ($typ_user != "APPRENANT" && $communes_groupe != 1 && $formateurs != 1 && $compter_fichiers > 0){
   echo "<tr bgcolor='#FFFFFF'><td colspan='10' align='right'><b>$mess_codec</b></td>".
        "<td align='center'><input type='checkbox' onClick=\"CheckAll();\"></td></tr>";
   echo "<tr bgcolor='#FFFFFF'><td colspan='10'>&nbsp;</td><td align='center'>$bouton_gauche".
        "<A href=\"javascript:void(0);\" onclick=\"javascript:return(confm('javascript:document.form_supp.submit()'));\">".
        "$mess_ag_supp</A>$bouton_droite</td>";
   echo "<td colspan='2'>&nbsp;</td></tr>";
 }
 if ($passe_box == 1)
   echo "</FORM>";
 $tab_file = NULL;
 $rallong= $dir;
  echo "</TABLE>";
 if ((strstr($dir,"devoirs") && $typ_user == "APPRENANT") || $login == 'demo' || $login == 'invite'){}else{
    echo "<table bgcolor='#FFFFFF' cellspacing='0' cellpadding=4' width=100% border='0'>";
    if (($dir == $base || ($communes_groupe == 1 && $typ_user == "APPRENANT")) && $formateurs !=1){}else{
      echo "<CENTER>";
      $passe = 1;
        // Création d'un dossier---------------------------------------------------------------------------
        ?>
        <script language="JavaScript" type="text/javascript">
          function checkForm2(frm) {
            var ErrMsg = "<?php echo "$mess_info_no";?>\n";
            var lenInit = ErrMsg.length;
            if (isEmpty(frm.nouveau_rep)==true)
              ErrMsg += ' - <?php echo "$mess_casier_dossier";?>\n';
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

        echo "<TR height='20'><TD></TD></TR>";
        echo "<FORM name='form2' action=\"modif_rep_fic.php\" method=\"POST\">";
        echo "<TR>";
        echo "<TD nowrap valign='middle' align='right'><B>$mess_cas_cre_dos</B><br />&nbsp;</TD>";
        echo "<TD valign='top' style='text-align:left;'>";
        echo "<INPUT TYPE=\"text\" class='INPUT' NAME=\"nouveau_rep\" VALUE=\"\" MAXLENGTH=\"20\">";
        echo "<INPUT TYPE=\"HIDDEN\" NAME=\"objet\" VALUE=\"cree_rep\">";
        echo "<INPUT TYPE=\"HIDDEN\" NAME=\"direct\" VALUE='1'>";
        echo "<INPUT TYPE=\"HIDDEN\" NAME=\"id_grp\" VALUE='$id_grp'>";
        echo "<INPUT TYPE=\"HIDDEN\" NAME=\"communes_groupe\" VALUE='$communes_groupe'>";
        echo "<INPUT TYPE=\"HIDDEN\" NAME=\"formateurs\" VALUE='$formateurs'>";
        echo "<INPUT TYPE=\"HIDDEN\" NAME=\"dos\" VALUE=\"$dir\">";
        echo "<INPUT TYPE=\"HIDDEN\" NAME=\"rallonge\" VALUE=\"$rallong\">";
        echo "<INPUT TYPE=\"HIDDEN\" NAME=\"fichier\" VALUE=\"$der_dossier\">";
        echo "<br /><A href=\"javascript:checkForm2(document.form2);\" onmouseover=\"img2.src='images/fiche_identite/boutvalidb.gif';return true;\" onmouseout=\"img2.src='images/fiche_identite/boutvalid.gif'\">";
        echo "<IMG NAME=\"img2\" SRC=\"images/fiche_identite/boutvalid.gif\" BORDER='0' onLoad=\"tempImg=new Image(0,0); tempImg.src='images/fiche_identite/boutvalidb.gif'\"></A>";
        echo "</TD></TR></FORM>";
    }
    if ($dir == $base)
       echo "</TABLE>";
    if ($dir != $base || ($communes_groupe == 1 && $dir !== $base && $typ_user == "APPRENANT"))
    {
        if (!isset($passe))
        {
           echo "<CENTER>";
           $passe = 1;
        }
        // Upload fichier <500 ko---------------------------------------------------------------------------
        if ($communes_groupe == 1 && $typ_user == "APPRENANT")
           echo "<table bgcolor='#FFFFFF' cellspacing='0' cellpadding='1' width='100%'><TR height='50'>";
        else
           echo "<TR>";
        echo "<FORM NAME='form4' action=\"modif_rep_fic.php\" method=\"POST\" ENCTYPE=\"multipart/form-data\">";
        if ($typ_user == "APPRENANT")
           echo "<TD nowrap valign='middle' align='right'><B>$mess_cas_tel_fic</B><br />&nbsp;</TD><TD valign='top' style='text-align:left;'>";
        else
           echo "<TD nowrap  valign='middle' align='right'><B>$mess_cas_tel_fic1</B><br />&nbsp;</TD><TD valign='top' style='text-align:left;'>";
        echo "<INPUT TYPE=\"HIDDEN\" NAME=\"objet\" VALUE=\"telecharger\">";
        echo "<INPUT TYPE=\"HIDDEN\" NAME=\"direct\" VALUE=\"1\">";
        echo "<INPUT TYPE=\"HIDDEN\" NAME=\"id_grp\" VALUE=\"$id_grp\">";
        echo "<INPUT TYPE=\"HIDDEN\" NAME=\"communes_groupe\" VALUE=\"$communes_groupe\">";
        echo "<INPUT TYPE=\"HIDDEN\" NAME=\"formateurs\" VALUE=\"$formateurs\">";
        echo "<INPUT TYPE=\"HIDDEN\" NAME=\"dos\" VALUE=\"$dir\">";
        echo "<INPUT TYPE=\"HIDDEN\" NAME=\"rallonge\" VALUE=\"$rallong\">";
        if ($typ_user == "APPRENANT")
            echo "<INPUT type=\"hidden\" name=\"MAX_FILE_SIZE\" value=\"500000\">";
        else
            echo "<INPUT type=\"hidden\" name=\"MAX_FILE_SIZE\" value=\"5000000\">";
        echo "<INPUT TYPE=\"HIDDEN\" NAME=\"fichier\" VALUE=\"$der_dossier\">";
        echo "<INPUT TYPE='file' class='INPUT' name='userfile' enctype='multipart/form-data'>";

        echo "<BR><A href=\"javascript:document.form4.submit();\" onmouseover=\"img4.src='images/fiche_identite/boutvalidb.gif';return true;\" onmouseout=\"img4.src='images/fiche_identite/boutvalid.gif'\">";
        echo "<IMG NAME=\"img4\" SRC=\"images/fiche_identite/boutvalid.gif\" BORDER='0' onLoad=\"tempImg=new Image(0,0); tempImg.src='images/fiche_identite/boutvalidb.gif'\">";
        echo "</TD></TR></FORM>";
          if ($typ_user != "APPRENANT")
          {
            // Edition de FICHIER----------------------------------------------------------------------
            echo "<TR height='25'><TD align='right'><B>$mess_cree_html</B></TD>";
            $lien = "edit_box.php?direct=1&parent=$parent&rallonge=$dir&id_grp=$id_grp&communes_groupe=$communes_groupe&formateurs=$formateurs&dos=$dir&objet=new";
            echo "<TD><A href=\"javascript:void(0);\" class='bouton_new' ".
                 "onclick=\"window.open('$lien','','width=800,height=700,resizable=yes,scrollbar=yes,status=no');\" ".
                 "TITLE=\"$mess_gen_valider\">&nbsp;Ok&nbsp;</a></TD>";
            echo "</TD><TD align='right'></TD></TR><TR height='10'><TD></TD></TR>";
          }
        echo "<TR height='15'><TD></TD></TR></TABLE>";
    }
 }
echo '<div id="mien" class="cms"></div>';
}
// Dezippe un fichier d'extension zip ou gz
function zip($fichier)
{
  if (!file_exists($fichier))
    return -1;
  $total = 0;
  list($extension, $nom) = getextension($fichier);
  if (in_array($extension, array("zip", "z", "gz")))
  {
    // un fichier zippe, on le dezippe
    $gp = gzopen("$fichier", "r+");
    if (file_exists($nom))
      return -1;
    $fp = fopen($nom, "w+");
    if ($fp < 0)
      return -1;
    while(!gzeof($gp))
    {
      $total += fwrite($fp, gzread($gp, 8*1024));
      // on utilise ici la taille de buffer standard
    }
  }else{
    // un fichier a compresser
    $fp = fopen($fichier, "r+");
    if (file_exists("$fichier.gz"))
      return -1;
    $gp = gzopen("$fichier.gz", "w+");
    if ($gp < 0)
      return -1;
    while(!feof($fp))
    {
      $total += gzwrite($gp, fread($fp, 8*1024));
      // on utilise ici la taille de buffer standard
    }
  }
  return $total;
}

function getextension($fichier)
{
  $bouts = explode(".", $fichier);
  return array(array_pop($bouts), implode(".", $bouts));
}
function zipper ($fichier)
{
  $zip = zip_open($fichier);

  if ($zip)
  {

     while ($zip_entry = zip_read($zip))
     {
        echo "Nom :               " . zip_entry_name($zip_entry) . "\n";
        echo "Taille réelle du fichier : "  . zip_entry_filesize($zip_entry) . "\n";
        echo "Taille compressée du fichier : " . zip_entry_compressedsize($zip_entry) . "\n";
        echo "Méthode de compression : " . zip_entry_compressionmethod($zip_entry) . "\n";

        if (zip_entry_open($zip, $zip_entry, "r"))
        {
            echo "Contenu du fichier : \n";
            $buf = zip_entry_read($zip_entry, zip_entry_filesize($zip_entry));
            echo "$buf\n";

            zip_entry_close($zip_entry);
        }
        echo "\n";

     }

    zip_close($zip);
  }
}
?>