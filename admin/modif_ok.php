<?php
if (!isset($_SESSION)) session_start();
if (!isset($_SESSION['id_user']) || $_SESSION['id_user'] == "")
{
  exit();
}
header("Content-type  : text/plain");
header("Cache-Control: no-cache, max-age=0");
// Affichage des connections d'un utilisateur ordonées par date
include ('../include/UrlParam2PhpVar.inc.php');
require "../admin.inc.php";
require "../fonction.inc.php";
require "../lang$lg.inc.php";
require '../fonction_html.inc.php';
require '../class/class_admin.php';
dbConnect();

  if ($nom == "" || $prenom == "" || ord($nom{0}) == 32 || ord($prenom{0}) == 32)
  {
      echo $msq_oubli_champ_oblig;
     exit;
  }
  $photo_exist = GetdataField ($connect,"select util_photo_lb from utilisateur where util_cdn='$num'","util_photo_lb");
  $dir = $repertoire."/images/galerie";
  if (isset($_FILES["userfile"]["tmp_name"]))
  {
    $longueur = strlen($_FILES["userfile"]["name"]);
    $extension = substr($_FILES["userfile"]["name"],$longueur-4,4);
    $taille_file= filesize($_FILES["userfile"]["tmp_name"]);
    if (strtolower($extension) == ".gif" || strtolower($extension) == ".png"  || strtolower($extension) == ".jpg")
      $type_image = 1;
    else
      $type_image = 0;
  }
  if (isset($_FILES["userfile"]["tmp_name"]) && $taille_file < 20000 && $type_image == 1){
     $fichier_test = $_FILES["userfile"]["name"];
     $le_nom = modif_nom($fichier_test);
     $nom_final = "galerie/".$nom."_".$le_nom;
     $final_nom = "galerie/".$nom."_".$le_nom;
     $dest_file = $repertoire."/images/".$final_nom;
     $source_file = $_FILES["userfile"]["tmp_name"];
     move_uploaded_file($source_file, $dest_file);
  }
  elseif (isset($le_nom) && $le_nom == "" && $photo_exist == '')
     $nom_final = "";
  elseif (isset($taille_file) && ($taille_file > 20000 || $type_image == 0) && $photo_exist == '')
     $nom_final = "";
  elseif (isset($supp_photo) && $supp_photo == 'on')
  {
     $handle = opendir($repertoire."/images/galerie");
     while ($file = readdir($handle))
     {
         if (strstr($file,$nom."_".$prenom))
            unlink($repertoire."/images/galerie/".$file);
     }
     $nom_final = NULL;
     $DelPhoto = mysql_query("update utilisateur where set util_photo_lb = 'NULL' where util_cdn='$num'");
  }
  elseif (isset($le_nom) && $le_nom == "" && $photo_exist != '' && isset($supp_photo) && $supp_photo != 'on')
     $nom_final = $photo_exist;
  elseif (isset($taille_file) && ($taille_file > 20000 || $type_image == 0) && $photo_exist != '')
     $nom_final = $photo_exist;
  else
     $nom_final = '';
 $ancien_passe  = GetdataField ($connect,"select util_motpasse_lb from utilisateur where util_cdn='$num'","util_motpasse_lb");
 $logue  = GetdataField ($connect,"select util_login_lb from utilisateur where util_cdn='$num'","util_login_lb");
 if ($mafiche == 1)
 {
   if ($passe == $ancien_passe)
   {
     if ($passe1 != "" && $passe1 != $passe2)
     {
        echo $mess_auth_mdp_diff;
       exit;
     }
     elseif($passe1 == "" && $passe2 == "")
       $sql = mysql_query("UPDATE  utilisateur SET util_nom_lb=\"$nom\",util_prenom_lb=\"$prenom\",
                           util_photo_lb=\"$nom_final\",util_email_lb=\"$email\",util_tel_lb=\"$tel\",
                           util_urlmail_lb = \"$webmail\",util_motpasse_lb = \"$ancien_passe\",
                           util_logincas_lb = \"$logue_cas\",util_typutil_lb = \"$type\",
                           util_login_lb = \"$logue\",util_commentaire_cmt=\"".
                           addslashes($commentaire)."\" where util_cdn = \"$num\"");
      elseif($passe1 != "" && $passe1 == $passe2)
       $sql = mysql_query("UPDATE  utilisateur SET util_nom_lb=\"$nom\",util_prenom_lb=\"$prenom\",
                          util_photo_lb=\"$nom_final\",util_email_lb=\"$email\",util_tel_lb=\"$tel\",
                          util_urlmail_lb = \"$webmail\",util_motpasse_lb = \"$passe1\",
                          util_logincas_lb = \"$logue_cas\",util_typutil_lb = \"$type\",
                          util_login_lb = \"$logue\",util_commentaire_cmt=\"".addslashes($commentaire).
                          "\" where util_cdn = \"$num\"");
     if ($lien_nom != "")
     {
         if ($photo_exist != '' && $photo_exist != 'NULL')
         {
            $handle = opendir($repertoire."/images/galerie");
            $neoPhotoFile = str_replace('galerie/','',$lien_nom);
            while ($file = readdir($handle))
            {
                  if (strstr($file,$nom."_".$prenom) && $file != $neoPhotoFile)
                     unlink($repertoire."/images/galerie/".$file);
            }
         }
         $sql = mysql_query("UPDATE utilisateur SET util_photo_lb=\"$lien_nom\" where util_cdn = '$num'");
     }

   }
   elseif($passe != $ancien_passe)
   {
     echo  utf2Charset($mess_auth_mdp_faux,$charset);
    exit;
   }
 }
 else
 {
   if ($blocage == "NON")
      $sql = mysql_query("UPDATE  utilisateur SET util_flag = '0' where util_cdn = \"$num\"");
   if ($blocage == "OUI")
      $sql = mysql_query("UPDATE  utilisateur SET util_flag = '1' where util_cdn = \"$num\"");
   $tempor = modif_nom($lelogue);
   $lelogue=$tempor;
   if ($typ_user == 'ADMINISTRATEUR' && $lelogue !='' && $lelogue != $logue)
   {
      $req1 = mysql_query("select * from utilisateur where util_login_lb = '$lelogue'");
      $nomb = mysql_num_rows($req1);
      if ($nomb > 0)
      {
         $nomb++;
         $lelogue .= $nomb;
      }
      $new = $repertoire."/ressources/".$lelogue."_".$num;
      $ancien = $repertoire."/ressources/".$logue."_".$num;
      rename($ancien,$new);
      $chaine = $logue."_".$num;
      $chaine_new = $lelogue."_".$num;
      $sql = mysql_query("UPDATE log set login = \"$lelogue\" where login = \"$logue\"");
      $sql = mysql_query("UPDATE trace set trace_login_lb = \"$lelogue\" where trace_login_lb  = \"$logue\"");
      $sql = mysql_query("UPDATE utilisateur SET util_auteur_no='$inscripteur',util_nom_lb=\"$nom\",util_prenom_lb=\"$prenom\",util_login_lb=\"$lelogue\",util_logincas_lb=\"$logue_cas\",util_photo_lb=\"$nom_final\",util_email_lb=\"$email\",util_tel_lb=\"$tel\",util_urlmail_lb = \"$webmail\",util_typutil_lb = \"$type\",util_blocageutilisateur_on = \"$blocage\",util_commentaire_cmt=\"".addslashes($commentaire)."\" where util_cdn = \"$num\"");
      if ($type == "APPRENANT")
      {
          $req_grp = mysql_query("select utilgr_groupe_no from utilisateur_groupe WHERE utilgr_utilisateur_no = $num");
          if (mysql_num_rows($req_grp) > 0)
          {
              while ($item_grp = mysql_fetch_object($req_grp))
              {
                    $grpId = $item_grp->utilgr_groupe_no;
                    $sql1 = mysql_query("SELECT suivi_cdn,suivi_fichier_lb from suivi1_$grpId where suivi_utilisateur_no='$num'");
                    $nb_sql = mysql_num_rows($sql1);
                    if ($nb_sql > 0)
                    {
                        $xx = 0;
                        while ($xx < $nb_sql)
                        {
                               $id_fic = mysql_result($sql1,$xx,"suivi_cdn");
                               $fiche = mysql_result($sql1,$xx,"suivi_fichier_lb");
                               if (strstr($fiche,$chaine))
                               {
                                   $fiche_new = str_replace($chaine,$chaine_new,$fiche);
                                   $req = mysql_query("UPDATE suivi1_$grpId set suivi_fichier_lb=\"$fiche_new\" WHERE suivi_cdn = '$id_fic'");
                               }
                               $xx++;
                        }
                    }
               }
          }
      }
   }
   else
      $sql = mysql_query("UPDATE utilisateur SET util_auteur_no='$inscripteur',util_nom_lb=\"$nom\",util_prenom_lb=\"$prenom\",util_logincas_lb=\"$logue_cas\",util_photo_lb=\"$nom_final\",util_email_lb=\"$email\",util_tel_lb=\"$tel\",util_urlmail_lb = \"$webmail\",util_typutil_lb = \"$type\",util_login_lb = \"$logue\",util_blocageutilisateur_on = \"$blocage\",util_commentaire_cmt=\"".addslashes($commentaire)."\" where util_cdn = \"$num\"");
   if ($lien_nom != "")
      $sql = mysql_query("UPDATE utilisateur SET util_photo_lb=\"$lien_nom\" where util_cdn = '$num'");
 }
 sleep(1);
 if ($mafiche == 1)
 {
     $suite = $mess_reload;
     echo utf2Charset($mess_suit_dch,$charset);
 }
 else
 {
     $mess_aff = "$mess_admin_fic_modif_deb $prenom $nom $mess_admin_fic_modif_fin".$test;
     echo  utf2Charset($mess_aff,$charset);
     //echo  $mess_aff;
 }
?>
