<?php
//connexion à la base
function dbConnect (){
   require "admin.inc";
   global $connect;
   $connect=mysql_connect("$adresse","$log","$mdp") or die ("<CENTER><A style='color:white;background-color:red'><BIG><B> Désolé, problème de serveur </B></BIG></BODY></HTML>");
   mysql_select_db("$bdd",$connect) or die ("<CENTER><A style='color:white;background-color:red'><BIG><B> Désolé, problème de serveur  </B></BIG></BODY></HTML>");
}
//connexion à la base posfix
function Connecter($site){
   require "admin.inc";
   global $connecter;
   $connecter=mysql_connect("$adresse","$log","$mdp") or die ("<CENTER><A style='color:white;background-color:red'><BIG><B> Désolé, problème de serveur </B></BIG></BODY></HTML>");
   mysql_select_db("$site",$connecter) or die ("<CENTER><A style='color:white;background-color:red'><BIG><B> Désolé, problème de serveur  </B></BIG></BODY></HTML>");
}
function requete($quoi,$tbl,$condition){
   $query = mysql_query ("SELECT $quoi FROM $tbl WHERE $condition");
   if (mysql_num_rows($query) > 0)
      return $query;
   else
      return FALSE ;
}
//Genere un ID
function Donne_ID ($connect,$requete){
   $query = mysql_query ($requete);
   $res = mysql_result ($query,0);
   if (!$res){
      $res = 1;
      return $res;
   }
   else
      return ++$res;
}
//Genere un ID pour postfix

function IDMAX ($connecter,$requete){
   $query = mysql_query ($requete);
   $res = mysql_result ($query,0);
   if (!$res){
      $res = 1;
      return $res;
   }
   else
      return ++$res;
}
//création de menu déroulant à partir d'une table
//fonction pour ascenceur simple
function Ascenseur($Nom,$req,$connexion,$param){
   echo "<select name=$Nom>";
   echo "<OPTION value='-1'>- - - - - -</OPTION>";
   $res=mysql_query($req);
   $nbLig=mysql_num_rows($res);
   $i=0;
   while ($i<$nbLig){
     $issue = @mysql_result($res,$i,mysql_field_name($res,0));
     if ($issue == $param)
        echo "<option value = ".@mysql_result($res,$i,mysql_field_name($res,0))." selected>".@mysql_result ($res,$i,mysql_field_name($res,1))."</option>";
     else
        echo "<option value = ".@mysql_result($res,$i,mysql_field_name($res,0)).">".@mysql_result ($res,$i,mysql_field_name($res,1))."</option>";
     $i++;
   }
   echo "</select>";
}
function Ascenseur_grp($Nom,$req,$connexion,$param){
   echo "<select name=$Nom>";
   echo "<OPTION value='-1'>- - - - - -</OPTION>";
   $res=mysql_query($req);
   $nbLig=mysql_num_rows($res);
   $i=0;
   while ($i<$nbLig){
     $issue = @mysql_result($res,$i,mysql_field_name($res,0));
     $nom_grp = @mysql_result($res,$i,mysql_field_name($res,1));
     $carac_grp = strlen($nom_grp);
     if ($carac_grp > 34)
        $nom1 = substr($nom_grp,0,34)."..";
     else
        $nom1 = $nom_grp;
     if ($issue == $param)
        echo "<option value = ".@mysql_result($res,$i,mysql_field_name($res,0))." title=\"$nom_grp\" selected />$nom1</option>";
     else
        echo "<option value = ".@mysql_result($res,$i,mysql_field_name($res,0))." title=\"$nom_grp\" />$nom1</option>";
     $i++;
   }
   echo "</select>";
}

//création de menu déroulant à partir d'une table
//fonction pour ascenseur à affichage de plusieurs paramètres
function Ascenseur_mult($Nom,$req,$connexion,$param){
   echo "<select name=$Nom>";
   echo "<OPTION value='-1'>- - - - - - -</OPTION>";
   $res=mysql_query($req);
   $nbLig=mysql_num_rows($res);
   $i=0;
   while ($i<$nbLig){
     $nommer = @mysql_result ($res,$i,mysql_field_name($res,1));
     if (strlen($nommer) > 40)
        $nom = substr($nommer,0,40)."...";
     else
        $nom = $nommer;
     $prenommer = @mysql_result ($res,$i,mysql_field_name($res,2));
     if (strlen($prenommer) > 40)
        $prenom = substr($prenommer,0,40)."...";
     else
        $prenom = $prenommer;
     $issue = @mysql_result($res,$i,mysql_field_name($res,0));
     if ($issue == $param)
        echo "<option value = ".@mysql_result($res,$i,mysql_field_name($res,0))." selected>$prenom &nbsp;&nbsp;-&nbsp;&nbsp;$nom </option>";
     else
        echo "<option value = ".@mysql_result($res,$i,mysql_field_name($res,0)).">$prenom &nbsp;&nbsp;-&nbsp;&nbsp;$nom</option>";
     $i++;
   }
   echo "</select>";
}
//fonction pour ascenceur affichage
function Ascenseur_affichage($Nom,$req,$connexion,$param,$utilisateur,$lg)
{
   global $connect;
   require "lang$lg.inc";
   $res=mysql_query($req);
   $nbLig=mysql_num_rows($res);
   $i=0;
   while ($i<$nbLig){
     $nom_grp = @mysql_result($res,$i,mysql_field_name($res,0));
     $carac_grp = strlen($nom_grp);
     if ($carac_grp > 33)
        $issue = substr($nom_grp,0,31)."..";
     else
        $issue = $nom_grp;
     $id_grp = GetDataField ($connect,"SELECT grp_cdn from groupe where grp_nom_lb = \"$nom_grp\"","grp_cdn");
     $nom_app = GetDataField ($connect,"SELECT util_nom_lb  from utilisateur where util_cdn = $utilisateur","util_nom_lb");
     $prenom_app = GetDataField ($connect,"SELECT util_prenom_lb from utilisateur where util_cdn = $utilisateur","util_prenom_lb");
     $majuscule =$nom_app." ".$prenom_app;
     $compte_presc=mysql_query("SELECT count(*) from prescription_$id_grp where presc_utilisateur_no=$utilisateur");
     $nbr_presc=mysql_result($compte_presc,count);
     if ($nbr_presc > 0)
        echo "<TR><TD nowrap align=left><DIV id='sequence'><LI><A HREF=\"gest_parc_frm1.php?saut=1&utilisateur=$utilisateur&a_faire=1&numero_groupe=$id_grp\" title =\"$mess_lanc_mess1 $majuscule\n$mess_lanc_mess2\">$issue</A></DIV></TD></TR>";
     else
        echo "<TR><TD nowrap align=left><LI><IMG SRC=\"images/quoi.gif\" border='0' width='12' height='12' title =\"$majuscule : $mess_gp_tut_nopresc\">&nbsp;&nbsp;$issue</TD></TR>";
     $i++;
   }
}

//recupere les donnees d'une colonne specifique ($field)
function GetDataField ($connect,$req,$field){
   $query = mysql_query($req);
   $Nb_Lig = mysql_num_rows($query);
   $i=0;
   while ($i != $Nb_Lig){
      $res = mysql_result ($query,$i,$field);
      $i++;
   }
   return $res;
}
// Parse une URL pour vérifier son existence
function url_exists($url) {
  $a_url = parse_url($url);
  if (!isset($a_url['port']))
     $a_url['port'] = 80;
  $errno = 0;
  $errstr = '';
  $timeout = 30;
  if (isset($a_url['host']) && $a_url['host']!=gethostbyname($a_url['host'])){
      $fid = fsockopen($a_url['host'], $a_url['port'], $errno, $errstr, $timeout);
      if (!$fid)
         return false;
      $page = isset($a_url['path'])  ?$a_url['path']:'';
      $page .= isset($a_url['query'])?'?'.$a_url['query']:'';
      fputs($fid, 'HEAD '.$page.' HTTP/1.0'."\r\n".'Host: '.$a_url['host']."\r\n\r\n");
      $head = fread($fid, 4096);
      fclose($fid);
      return preg_match('#^HTTP/.*\s+[200|302]+\s#i', $head);
  }else
     return false;
}


// Lit et retourne le contenu d'un repertoire
// deux fonctions renvoyant la taille d'un fichier et sa date de modification



function taille($fichier){
  $size_unit="o";
  $taille=filesize($fichier);
  if ($taille >= 1073741824)
     $taille = round($taille / 1073741824 * 100) / 100 . " g".$size_unit;
  elseif ($taille >= 1048576)
     $taille = round($taille / 1048576 * 100) / 100 . " m".$size_unit;
  elseif ($taille >= 1024)
     $taille = round($taille / 1024 * 100) / 100 . " k".$size_unit;
  else
     $taille=$taille. " ".$size_unit;
  if($taille==0)
     $taille="-";
  return $taille;
}

function date_modif($fichier){
  $tmp = filemtime($fichier);
  return date("d/m/Y",$tmp);
}

// lit l'image qui correspond à un type de fichier et la retourne sous la forme mimetype(nomdufichier,"image")
function mimetype($fichier,$quoi){
        if(is_dir($fichier))$image="repertoire/icoptidossier.gif";
        else if(eregi("\.mid$",$fichier))$image="mid.gif";
        else if(eregi("\.txt$",$fichier))$image="txt.gif";
        else if(eregi("\.js$",$fichier))$image="js.gif";
        else if(eregi("\.gif$",$fichier))$image="gif.gif";
        else if(eregi("\.jpg$",$fichier))$image="jpg.gif";
        else if(eregi("\.html$",$fichier))$image="html.gif";
        else if(eregi("\.htm$",$fichier))$image="html.gif";
        else if(eregi("\.rar$",$fichier))$image="rar.gif";
        else if(eregi("\.gz$",$fichier))$image="zip.gif";
        else if(eregi("\.tar$",$fichier))$image="zip.gif";
        else if(eregi("\.tar.gz$",$fichier))$image="zip.gif";
        else if(eregi("\.ra$",$fichier))$image="ram.gif";
        else if(eregi("\.ram$",$fichier))$image="ram.gif";
        else if(eregi("\.rm$",$fichier))$image="ram.gif";
        else if(eregi("\.pl$",$fichier))$image="pl.gif";
        else if(eregi("\.zip$",$fichier))$image="zip.gif";
        else if(eregi("\.wav$",$fichier))$image="wav.gif";
        else if(eregi("\.php$",$fichier))$image="php.gif";
        else if(eregi("\.exe$",$fichier))$image="exe.gif";
        else if(eregi("\.bmp$",$fichier))$image="bmp.gif";
        else if(eregi("\.png$",$fichier))$image="gif.gif";
        else if(eregi("\.css$",$fichier))$image="css.gif";
        else if(eregi("\.mp3$",$fichier))$image="mp3.gif";
        else if(eregi("\.xls$",$fichier))$image="excel.gif";
        else if(eregi("\.csv$",$fichier))$image="excel.gif";
        else if(eregi("\.doc$",$fichier))$image="doc.gif";
        else if(eregi("\.pdf$",$fichier))$image="pdf.gif";
        else if(eregi("\.mov$",$fichier))$image="mov.gif";
        else if(eregi("\.avi$",$fichier))$image="avi.gif";
        else if(eregi("\.mpg$",$fichier))$image="mpg.gif";
        else if(eregi("\.mpeg$",$fichier))$image="mpeg.gif";
        else if(eregi("\.xml$",$fichier))$image="ico_xml.jpg";
        else if(eregi("\.xsd$",$fichier))$image="ico_xsd.jpg";
        else if(eregi("\.rtf$",$fichier))$image="rtf.gif";
        else if(eregi("\.qcf$",$fichier))$image="qcf.gif";
        else if(eregi("\.sql$",$fichier))$image="sql.gif";
        else $image="defaut.gif";
        if($quoi=="image")
          return $image;
        else
          return "Ce n'est pas une image";
}

//Retourne le email si il est OK.
function verifie_email($email){
  if (strlen($email) < 6)
     return "$email : Email trop court";
  if (strlen($email) > 255)
     return "$email : Email trop long";
  if (!ereg("@", $email))
     return "$email : Le email n'a pas d'arobase (@)";
  if (preg_match_all("/([^a-zA-Z0-9_\@\.\-])/i", $email, $trouve))
     return "$email ---> [".implode($email, $trouve[0])."] : caractère interdit dans un email";
  if (!preg_match("/^([a-zA-Z0-9_]|\\-|\\.)+@(([a-z0-9_]|\\-)+\\.)+[a-z]{2,4}\$/i", $email))
     return "$email : ce n'est pas la forme d'un email.";
//  list($compte, $domaine)=explode("@", $email, 2);
//  if (!checkdnsrr($domaine, "MX")){
//    return "$domaine : le domaine de $email n'existe pas";
//  }
   return $email;
}

// ------------------------------------------------------------------------
// barre_navigation
// ------------------------------------------------------------------------
function barre_navigation($nbtotal,$nbenr,$cfg_nbres_ppage,$debut,$cfg_nb_pages,$criteres,$type,$typ_user,$publique,$login,$flg){
    // --------------------------------------------------------------------
       $lien_on         = '&nbsp;<A HREF="{cible}">{lien}</A>&nbsp;';
       $lien_off        = '&nbsp;{lien}&nbsp;';
    // --------------------------------------------------------------------

    $query  = $criteres.'&type='.$type.'&recherche=1&debut=';

    // début << .
    // --------------------------------------------------------------------
    if ($debut >= $cfg_nbres_ppage){
        $cible = $query.(0);
        $image = image_html('images/gauche_on.gif');
        $lien = str_replace('{lien}', $image.$image, $lien_on);
        $lien = str_replace('{cible}', $cible, $lien);
    }else{
        $image = image_html('images/gauche_off.gif');
        $lien = str_replace('{lien}', $image.$image, $lien_off);
    }
    $barre .= $lien."&nbsp;<B>&middot;</B>";
    // précédent < .
    // --------------------------------------------------------------------
    if ($debut >= $cfg_nbres_ppage){
        $cible = $query.($debut-$cfg_nbres_ppage);
        $image = image_html('images/gauche_on.gif');
        $lien = str_replace('{lien}', $image, $lien_on);
        $lien = str_replace('{cible}', $cible, $lien);
    }else{
        $image = image_html('images/gauche_off.gif');
        $lien = str_replace('{lien}', $image, $lien_off);
    }
    $barre .= $lien."&nbsp;<B>&middot;</B>";


    // pages 1 . 2 . 3 . 4 . 5 . 6 . 7 . 8 . 9 . 10
    // -------------------------------------------------------------------

    if ($debut >= ($cfg_nb_pages * $cfg_nbres_ppage)){
        $cpt_fin = ($debut / $cfg_nbres_ppage) + 1;
        $cpt_deb = $cpt_fin - $cfg_nb_pages + 1;
    }else{
        $cpt_deb = 1;
        $cpt_fin = (int)($nbtotal / $cfg_nbres_ppage);
        if (($nbtotal % $cfg_nbres_ppage) != 0) $cpt_fin++;
        if ($cpt_fin > $cfg_nb_pages) $cpt_fin = $cfg_nb_pages;
    }
    for ($cpt = $cpt_deb; $cpt <= $cpt_fin; $cpt++){
        if ($cpt == ($debut / $cfg_nbres_ppage) + 1){
            $barre .= "<A CLASS='off'>&nbsp;".$cpt."&nbsp;</A> ";
        }else{
            $barre .= "<A HREF='".$query.(($cpt-1)*$cfg_nbres_ppage);
            $barre .= "'>&nbsp;".$cpt."&nbsp;</A> ";
        }
   }

   // suivant . >
    // --------------------------------------------------------------------
    if ($debut + $cfg_nbres_ppage < $nbtotal){
        $cible = $query.($debut+$cfg_nbres_ppage);
        $image = image_html('images/droite_on.gif');
        $lien = str_replace('{lien}', $image, $lien_on);
        $lien = str_replace('{cible}', $cible, $lien);
    }else{
        $image = image_html('images/droite_off.gif');
        $lien = str_replace('{lien}', $image, $lien_off);
    }
    $barre .= "&nbsp;<B>&middot;</B>".$lien;

    // fin . >>
    // --------------------------------------------------------------------
    $fin = ($nbtotal - ($nbtotal % $cfg_nbres_ppage));
    if (($nbtotal % $cfg_nbres_ppage) == 0)
      $fin = $fin - $cfg_nbres_ppage;
    if ($fin != $debut){
        $cible = $query.$fin;
        $image = image_html('images/droite_on.gif');
        $lien = str_replace('{lien}', $image.$image, $lien_on);
        $lien = str_replace('{cible}', $cible, $lien);
    }else{
        $image = image_html('images/droite_off.gif');
        $lien = str_replace('{lien}', $image.$image, $lien_off);
    }
    $barre .= "<B>&middot;</B>&nbsp;".$lien;

    return($barre);
}


// ------------------------------------------------------------------------
// image_html
// ------------------------------------------------------------------------
function image_html($img, $align = "absmiddle"){
    $taille = @getimagesize($img);
    return '<IMG SRC="'.$img.'" '.$taille[3].' BORDER=0 ALIGN="'.$align.'">';
}

// ------------------------------------------------------------------------
// afficher_ligne
// ------------------------------------------------------------------------
function afficher_ligne($cpt, $enr, $login, $typ_user, $flg, $lg, $adres){
   global $connect;
  require "lang$lg.inc";
  require "graphique/admin.inc";
  //include ("click_droit.txt");
  dbConnect();
    $tit= htmlspecialchars($enr['ress_titre']);
    $ur= htmlspecialchars($enr['ress_url_lb']);
    $publicite= htmlspecialchars($enr['ress_publique_on']);
    $auteur_ajout= htmlspecialchars($enr['ress_ajout']);
    // alternance des couleurs des lignes de réponses
    $couleur = ($cpt % 2)? '#EEEEEE' : '#FFFFFF';
    echo '<TR BGCOLOR="'.$couleur.'">';
    // n° de  réponse
    echo '<TD ALIGN="right" WRAP WIDTH="5%" valign="top">&nbsp;<B>'.$cpt.'</B>.&nbsp;</TD>';

    // Titre et Url
    echo '<TD ALIGN="left"  WRAP WIDTH="25%" valign="top">';

   if ((($typ_user == "ADMINISTRATEUR" || $auteur_ajout == $login)  && $publicite=="NON") || ($publicite=="OUI" && $typ_user != "APPRENANT") && $ur != ''){
      $req_serv = mysql_query("select * from serveur_ressource");
      $nb_req_serv = mysql_num_rows($req_serv);
      if ($nb_req_serv > 0){
         $ir = 0;
         while ($ir < $nb_req_serv){
             $adr = mysql_result($req_serv,$ir,"serveur_nomip_lb");
             $params = mysql_result($req_serv,$ir,"serveur_param_lb");
             $label = mysql_result($req_serv,$ir,"serveur_label_lb");
             if ($label != ""){
                if (strstr($ur,$adr) && strstr($ur,$label)){
                   $ur = str_replace("&label=$label","",$ur);
                   $ur .= $params;
                   $transit = 1;
                   break;
                }
             }elseif ($label == "" && strstr($ur,"label=")){
                $ir++;
                continue;
             }elseif ($label == "" && !strstr($ur,"label=")){
                if (strstr($ur,$adr)){
                   $ur .= $params;
                   $transit = 1;
                   break;
                }
             }
             $ir++;
         }
         if ($transit == 1){
            $ur=urldecode($ur);
            echo "<A HREF=\"javascript:void(0);\" onclick=\"window.open('$ur','','resizable=yes,scrollbars=yes,status=no')\">";
            $transit = 0;
            $traspasse = 1;
         }
      }
     if (strstr($ur,"http://www.editions.educagri.fr/educagriNet")){
        $ur .="&url=$url_ress&auth_cdn=$auth_cdn";
        $ur = urlencode($ur);
        echo "<A HREF='#' onclick=\"window.open('trace.php?link=$ur','','status=no,directories=no,copyhistory=0,titlebar=no,toolbar=no,location=no,menubar=yes,scrollbars=yes,resizable=yes')\">";
     }elseif (strstr($ur,"qcm")){
        $ur .="&provenance=recherche";
        echo "<A HREF='#' onclick=\"window.open('lancer_ressource.php?lien=$ur','','status=no,directories=no,copyhistory=0,titlebar=no,toolbar=no,location=no,menubar=yes,scrollbars=yes,resizable=yes')\">";
     }elseif(strstr(strtolower($ur),".doc") || strstr(strtolower($ur),".xls") || strstr(strtolower($ur),".xlt")){
        echo "<A HREF=\"$ur\" target='_blank'>";
     }elseif ($traspasse != 1) {
        $traspasse = 0;
        echo "<A HREF='#' onclick=\"window.open('lancer_ressource.php?lien=$ur','','status=no,directories=no,copyhistory=0,titlebar=no,toolbar=no,location=no,menubar=yes,scrollbars=yes,resizable=yes')\">";
     }
     echo $tit;
     echo "</A>";
   }else{
    echo "<B>$tit</B>";
   }
    echo '&nbsp;</TD>';
    // Catégorie
    $sous_catalogue =htmlspecialchars($enr['ress_cat_lb']);
    $parente = GetDataField ($connect,"select ress_cdn from ressource_new where ress_cat_lb = \"$sous_catalogue\" AND ress_titre =''","ress_cdn");
    echo '<TD ALIGN="left"  NOWRAP WIDTH="10%" valign="top">&nbsp;';
    $lien = $adres."?flg=$flg&lien_sous_cat=1&parente=$parente&sous_catalogue=$sous_catalogue";
    $lien = urlencode($lien);
    echo "<A HREF=\"trace.php?link=$lien\" title=\"$mess_ress_tts_cat\"><IMG SRC=\"images/droite_on.gif\" BORDER=0 ALT=\"$mess_ress_tts_cat\">";
    echo "<SMALL>$sous_catalogue</SMALL>";
    echo "</A>";
    echo '&nbsp;</TD>';

    // Descriptif
    if ($tit != ""){
      echo "<TD WIDTH=\"60%\"><SMALL><B>$mrc_auteur :&nbsp;";
      echo htmlspecialchars($enr['ress_auteurs_cmt']);
      echo "</B>&nbsp;&nbsp;";
    }else
      echo "<TD WIDTH=\"60%\"><SMALL>&nbsp;&nbsp;";
    echo "<BR>".htmlspecialchars($enr['ress_desc_cmt']);
    echo '&nbsp;</SMALL></TD>';
    echo '</TR>';
}
//---------------------------------------------------------------
function viredir($dir,$s_exp){
  $dossier=opendir($dir);
  $total = 0;
  while ($fichier = readdir($dossier)) {
   $l = array('.', '..');
   if (!in_array( $fichier, $l)){
     if ($s_exp == "lx"){
       if (is_dir($dir."/".$fichier))
         $total += viredir("$dir/$fichier",$s_exp);
       else{
         unlink("$dir/$fichier");
         $total++;
       }
     }else{
       if (is_dir($dir."\\".$fichier))
         $total += viredir("$dir\\$fichier",$s_exp);
       else{
         unlink("$dir\\$fichier");
         $total++;
       }
     }
   }
  }
 @closedir($dossier);
 rmdir($dir);
 return $total;
}
// Calcul de la taille d'un dossier

function DirSize($path , $recursive=TRUE){
  $result = 0;
  if(!is_dir($path) || !is_readable($path))
   return 0;
  $fd = dir($path);
  while($file = $fd->read()){
   if(($file != ".") && ($file != "..")){
    if(@is_dir("$path$file/"))
     $result += $recursive?DirSize("$path$file/"):0;
    else
     $result += filesize("$path$file");
   }
  }
  $fd->close();
  return $result;
}

function modif_nom($fichier_test){
    $fichier_test = strtr($fichier_test,
    'ÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ',
    'AAAAAACEEEEIIIIOOOOOUUUUYaaaaaaceeeeiiiioooooouuuuyy');
    // remplacer les caracteres autres que lettres, chiffres et point par _
    $fichier_test = preg_replace('/([^\/.a-z0-9]+)/i', '_',$fichier_test);
  return $fichier_test;
}

function recharge() {
    $agent=getenv("HTTP_USER_AGENT");
    if (strstr($agent,"MSIE")){
       echo "<SCRIPT Language=\"Javascript\">";
       echo "window.parent.opener.location.reload();";
       echo "</SCRIPT>";
    }else{
       echo "<SCRIPT Language=\"Javascript\">";
       echo "parent.parent.opener.location.reload();";
       echo "</SCRIPT>";
    }
}
function ForceFileDownload($file){
    $filesize = @filesize($file);
    header("Content-Disposition: attachment; filename=".$file);
    header("Content-Type: application/octet-stream");
    header("Cache-Control: private",false);
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("Content-Type: application/force-download");
    header("Content-Type: application/x-download");
    header("Content-Transfer-Encoding: ascii");
    header("Pragma:no-cache");
    header("Expires:0");
    @set_time_limit(600);
    readfile($file);
}
//Fonction de transformation d'UTF-8 en ISO au cas ou c'est de l'UTF-8 (gestion multilingue)
function seems_utf8($str){
   for ($i=0; $i<strlen($str); $i++){
       if (ord($str[$i]) < 0x80)
          continue; // 0bbbbbbb
       elseif ((ord($str[$i]) & 0xE0) == 0xC0) $n=1; // 110bbbbb
       elseif ((ord($str[$i]) & 0xF0) == 0xE0) $n=2; // 1110bbbb
       elseif ((ord($str[$i]) & 0xF8) == 0xF0) $n=3; // 11110bbb
       elseif ((ord($str[$i]) & 0xFC) == 0xF8) $n=4; // 111110bb
       elseif ((ord($str[$i]) & 0xFE) == 0xFC) $n=5; // 1111110b
       else
           return false; // Does not match any model
       for ($j=0; $j<$n; $j++){ // n bytes matching 10bbbbbb follow ?
           if ((++$i == strlen($str)) || ((ord($str[$i]) & 0xC0) != 0x80))
              return false;
       }
   }
   return true;
}

function utf8_decode_si_utf8($str) {
   return seems_utf8($str)? utf8_decode($str): $str;
}
?>
