<?php
//connexion à la base
  function dbConnect ()
  {
           require "admin.inc.php";
           global $connect;
           $connect=mysql_connect("$adresse","$log","$mdp") or die ("<CENTER><A style='color:white;background-color:red'><BIG><B> Désolé, problème de serveur </B></BIG></BODY></HTML>");
           mysql_select_db("$bdd",$connect) or die ("<CENTER><A style='color:white;background-color:red'><BIG><B> Désolé, problème de serveur  </B></BIG></BODY></HTML>");
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
//création de menu déroulant à partir d'une table
//fonction pour ascenceur simple
function Ascenseur($Nom,$req,$connexion,$param)
        {
        ?>
                <select name=<? echo $Nom;?>>
        <?
                $res=mysql_query($req);
                $nbLig=mysql_num_rows($res);
                $i=0;
                while($i<$nbLig)
                {
                  $issue = @mysql_result($res,$i,mysql_field_name($res,0));
                  if ($issue == $param){
        ?>               <option value = <? echo @mysql_result($res,$i,mysql_field_name($res,0));?> selected> <? echo @mysql_result ($res,$i,mysql_field_name($res,1));?> </option>
        <?        }else{
        ?>               <option value = <? echo @mysql_result($res,$i,mysql_field_name($res,0));?> > <? echo @mysql_result ($res,$i,mysql_field_name($res,1));?> </option>
        <?        }
                $i++;

                }
        ?>
                </select>
        <?

}

//création de menu déroulant à partir d'une table
//fonction pour ascenceur simple
function Ascenseur_mult($Nom,$req,$connexion,$param)
        {
        ?>
                <select name=<? echo $Nom;?>>
        <?
                $res=mysql_query($req);
                $nbLig=mysql_num_rows($res);
                $i=0;
                while($i<$nbLig)
                {
                      $nommer = mysql_result ($res,$i,mysql_field_name($res,1));
                      if (strlen($nommer) > 40) $nom = substr($nommer,0,40)."..."; else $nom = $nommer;
                      $prenommer = mysql_result ($res,$i,mysql_field_name($res,2));
                      if (strlen($prenommer) > 40) $prenom = substr($prenommer,0,40)."..."; else $prenom = $prenommer;
                  $issue = @mysql_result($res,$i,mysql_field_name($res,0));
                  if ($issue == $param){
        ?>               <option value = <? echo @mysql_result($res,$i,mysql_field_name($res,0));?> selected> <? echo "$prenom &nbsp;&nbsp;-&nbsp;&nbsp;$nom";?> </option>
        <?        }else{
        ?>
                        <option value = <? echo @mysql_result($res,$i,mysql_field_name($res,0));?> > <? echo "$prenom &nbsp;&nbsp;-&nbsp;&nbsp;$nom";?> </option>
        <?       }
                $i++;

                }
        ?>
                </select>
        <?

}

//recupere les donnees d'une colonne specifique ($field)
function GetDataField ($connect,$req,$field)
{
 $query = mysql_query($req);
 $Nb_Lig = mysql_num_rows($query);
 $i=0;
 while ($i != $Nb_Lig)
     {
      $res = mysql_result ($query,$i,$field);
      $i++;
      }
 return $res;
}



// Lit et retourne le contenu d'un repertoire
// deux fonctions renvoyant la taille d'un fichier et sa date de modification



function taille($fichier)
        {
        $size_unit="o";
        $taille=filesize($fichier);
        if ($taille >= 1073741824) {$taille = round($taille / 1073741824 * 100) / 100 . " g".$size_unit;}
        elseif ($taille >= 1048576) {$taille = round($taille / 1048576 * 100) / 100 . " m".$size_unit;}
        elseif ($taille >= 1024) {$taille = round($taille / 1024 * 100) / 100 . " k".$size_unit;}
        else {$taille=$taille. " ".$size_unit;}
        if($taille==0) {$taille="-";}
        return $taille;
        }

function date_modif($fichier)
        {
        $tmp = filemtime($fichier);
        return date("d/m/Y",$tmp);
        }




// lit l'image qui correspond à un type de fichier et la retourne sous la forme mimetype(nomdufichier,"image")
function mimetype($fichier,$quoi)
{
        if(is_dir($fichier))$image="repertoire/icoptidossier.gif";
        else if(strstr($fichier,".mid"))$image="mid.gif";
        else if(strstr($fichier,".txt"))$image="txt.gif";
        else if(strstr($fichier,".js"))$image="js.gif";
        else if(strstr($fichier,".gif"))$image="gif.gif";
        else if(strstr($fichier,".jpg"))$image="jpg.gif";
        else if(strstr($fichier,".html"))$image="html.gif";
        else if(strstr($fichier,".htm"))$image="html.gif";
        else if(strstr($fichier,".rar"))$image="rar.gif";
        else if(strstr($fichier,".gz"))$image="zip.gif";
        else if(strstr($fichier,".tar"))$image="zip.gif";
        else if(strstr($fichier,".tar.gz"))$image="zip.gif";
        else if(strstr($fichier,".ra"))$image="ram.gif";
        else if(strstr($fichier,".ram"))$image="ram.gif";
        else if(strstr($fichier,".rm"))$image="ram.gif";
        else if(strstr($fichier,".pl"))$image="pl.gif";
        else if(strstr($fichier,".zip"))$image="zip.gif";
        else if(strstr($fichier,".wav"))$image="wav.gif";
        else if(strstr($fichier,".php"))$image="php.gif";
        else if(strstr($fichier,".exe"))$image="exe.gif";
        else if(strstr($fichier,".bmp"))$image="bmp.gif";
        else if(strstr($fichier,".png"))$image="gif.gif";
        else if(strstr($fichier,".css"))$image="css.gif";
        else if(strstr($fichier,".mp3"))$image="mp3.gif";
        else if(strstr($fichier,".xls"))$image="excel.gif";
        else if(strstr($fichier,".csv"))$image="excel.gif";
        else if(strstr($fichier,".doc"))$image="doc.gif";
        else if(strstr($fichier,".pdf"))$image="pdf.gif";
        else if(strstr($fichier,".mov"))$image="mov.gif";
        else if(strstr($fichier,".avi"))$image="avi.gif";
        else if(strstr($fichier,".flv"))$image="avi.gif";
        else if(strstr($fichier,".mp4"))$image="avi.gif";
        else if(strstr($fichier,".mpg"))$image="mpg.gif";
        else if(strstr($fichier,".mpeg"))$image="mpeg.gif";
        else if(strstr($fichier,".xml"))$image="ico_xml.jpg";
        else if(strstr($fichier,".xsd"))$image="ico_xsd.jpg";
        else if(strstr($fichier,".rtf"))$image="rtf.gif";
        else if(strstr($fichier,".qcf"))$image="qcf.gif";
        else if(strstr($fichier,".sql"))$image="sql.gif";
        else if(strstr($fichier,".odf"))$image="icones_files/odf.png";
        else if(strstr($fichier,".odp"))$image="icones_files/odp.png";
        else if(strstr($fichier,".ods"))$image="icones_files/ods.png";
        else if(strstr($fichier,".odt"))$image="icones_files/odt.png";
        else $image="defaut.gif";
        if($quoi=="image")
          return $image;
        else
          return "Ce n'est pas une image";
}

/* Verifie l'existence du domaine pour l'email en cours

ifsnow's email valid check function SnowCheckMail Ver 0.1

funtion SnowCheckMail ($Email,$Debug=false)

$Email : E-Mail address to check.
$Debug : Variable for debugging.

* Can use everybody if use without changing the name of function.

Reference : O'REILLY - Internet Email Programming

HOMEPAGE : http://www.hellophp.com

ifsnow is korean phper. Is sorry to be unskillful to English. *^^*;;

========================================================================= */

function SnowCheckMail($email,$Debug=false)
{
    global $HTTP_HOST;
    $Return =array();
    // Variable for return.
    // $Return[0] : [true|false]
    // $Return[1] : Processing result save.

    if (!preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/i", $email)) {
        $Return[0]=false;
        $Return[1]="${email} n'est pas valable.";
        if ($Debug) echo "Erreur : {$email} n'est pas valable.<br>";
        return $Return[1];
    }
    else if ($Debug) echo "Confirmation : {$email} n'est pas valable.<br>";

    list ( $Username, $Domain ) = explode ("@",$email);

    // That MX(mail exchanger) record exists in domain check .
    // checkdnsrr function reference : http://www.php.net/manual/en/function.checkdnsrr.php
    if ( checkdnsrr ( $Domain, "MX" ) )  {
        if($Debug) echo "Confirmation : MX enregistrement du domaine {$Domain} existe bel et bien.<br>";
        // If MX record exists, save MX record address.
        // getmxrr function reference : http://www.php.net/manual/en/function.getmxrr.php
        if ( getmxrr ($Domain, $MXHost))  {
      if($Debug) {
                echo "Confirmation: l'adresse est confirmée.<br>";
              for ( $i = 0,$j = 1; $i < count ( $MXHost ); $i++,$j++ ) {
            echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Result($j) - $MXHost[$i]<BR>";
        }
            }
        }
        // Getmxrr function does to store MX record address about $Domain in arrangement form to $MXHost.
        // $ConnectAddress socket connection address.
        $ConnectAddress = $MXHost[0];
    }
    else {
        // If there is no MX record simply @ to next time address socket connection do .
        $ConnectAddress = $Domain;
        if ($Debug) echo "Confirmation : Le domaine {$Domain} n'existe pas.Cet Email n'est donc pas valable<br>";
    }

    // fsockopen function reference : http://www.php.net/manual/en/function.fsockopen.php
    $Connect = fsockopen ( $ConnectAddress, 25 );

    // Success in socket connection
    if ($Connect)
    {
        if ($Debug) echo "Connection réussie {$ConnectAddress} SMTP.<br>";
        // Judgment is that service is preparing though begin by 220 getting string after connection .
        // fgets function reference : http://www.php.net/manual/en/function.fgets.php
        if ( preg_match ( "/^220/", $Out = fgets ( $Connect, 1024 ) ) ) {

            // Inform client's reaching to server who connect.
            fputs ( $Connect, "Salut $HTTP_HOST\r\n" );
                if ($Debug) echo "Run : Salut $HTTP_HOST<br>";
            $Out = fgets ( $Connect, 1024 ); // Receive server's answering cord.

            // Inform sender's address to server.
            fputs ( $Connect, "MAIL FROM: <{$email}>\r\n" );
                if ($Debug) echo "Run : MAIL FROM: &lt;{$email}&gt;<br>";
            $From = fgets ( $Connect, 1024 ); // Receive server's answering cord.

            // Inform listener's address to server.
            fputs ( $Connect, "RCPT TO: <{$email}>\r\n" );
                if ($Debug) echo "Run : RCPT TO: &lt;{$email}&gt;<br>";
            $To = fgets ( $Connect, 1024 ); // Receive server's answering cord.

            // Finish connection.
            fputs ( $Connect, "QUIT\r\n");
                if ($Debug) echo "Run : QUIT<br>";

            fclose($Connect);

                // Server's answering cord about MAIL and TO command checks.
                // Server about listener's address reacts to 550 codes if there does not exist
                // checking that mailbox is in own E-Mail account.
                if ( !preg_match ( "/^250/", $From ) || !preg_match ( "/^250/", $To )) {
                    $Return[0]=false;
                    $Return[1]="${email} n'est pas une adresse valide (domaine inexistant).";
                    if ($Debug) echo "{$email} n'est pas une adresse valide (domaine inexistant).<br>";
                    return $Return[1];
                }
        }
    }
    // Failure in socket connection
    else {
        $Return[0]=false;
        $Return[1]="Impossible de se connecter à ce serveur d'Email ({$ConnectAddress}).Ce n'est pas une bonne adresse";
        if ($Debug) echo "Impossible de se connecter à ce serveur d'Email ({$ConnectAddress}).Ce n'est pas une bonne adresse<br>";
        return $Return[1];
    }
    $Return[0]=true;
    $Return[1]=$email;
    return $Return[1];
}


//Retourne le email si il est OK.
function verifie_email($email)
{
 $email = strtolower($email);
  if (strlen($email) < 6) {
    return "\"$email\" : Email trop court";
  }
  if (strlen($email) > 255) {
    return "\"$email\" : Email trop long";
  }
  if (!preg_match("/@/", $email)){
    return "\"$email\" : Le email n'a pas d'arobase (@)";
  }
  if (preg_match_all("/([^a-zA-Z0-9_\@\.\-])/i", $email, $trouve)){
    return "\"$email\" : caractère(s) interdit dans un email (\".implode(\", \", $trouve[0]).\").";
  }
  if (!preg_match("/^([a-z0-9_]|\\-|\\.)+@(([a-z0-9_]|\\-)+\\.)+[a-z]{2,4}\$/i", $email)){
    return "\"$email\" : ce n'est pas la forme d'un email.";
  }
  list($compte, $domaine)=explode("@", $email, 2);
  if (!checkdnsrr($domaine, "MX")){
    return "\"$email\" : Ce domaine ($domaine) n'accepte pas les emails";
  }
  return $email;
}

// ------------------------------------------------------------------------
// barre_navigation
// ------------------------------------------------------------------------
function barre_navigation($nbtotal,
                          $nbenr,
                          $cfg_nbres_ppage,
                          $debut, $cfg_nb_pages,
                          $criteres,$type,$typ_user,$publique,$login,$flg)
{
    // --------------------------------------------------------------------
       $lien_on         = '&nbsp;<A HREF="{cible}">{lien}</A>&nbsp;';
       $lien_off        = '&nbsp;{lien}&nbsp;';
    // --------------------------------------------------------------------

    $query  = $criteres.'&type='.$type.'&recherche=1&debut=';

    // début << .
    // --------------------------------------------------------------------
    if ($debut >= $cfg_nbres_ppage)
    {
        $cible = $query.(0);
        $image = image_html('images/gauche_on.gif');
        $lien = str_replace('{lien}', $image.$image, $lien_on);
        $lien = str_replace('{cible}', $cible, $lien);
    }
    else
    {
        $image = image_html('images/gauche_off.gif');
        $lien = str_replace('{lien}', $image.$image, $lien_off);
    }
    $barre .= $lien."&nbsp;<B>&middot;</B>";


    // précédent < .
    // --------------------------------------------------------------------
    if ($debut >= $cfg_nbres_ppage)
    {
        $cible = $query.($debut-$cfg_nbres_ppage);
        $image = image_html('images/gauche_on.gif');
        $lien = str_replace('{lien}', $image, $lien_on);
        $lien = str_replace('{cible}', $cible, $lien);
    }
    else
    {
        $image = image_html('images/gauche_off.gif');
        $lien = str_replace('{lien}', $image, $lien_off);
    }
    $barre .= $lien."&nbsp;<B>&middot;</B>";


    // pages 1 . 2 . 3 . 4 . 5 . 6 . 7 . 8 . 9 . 10
    // -------------------------------------------------------------------

    if ($debut >= ($cfg_nb_pages * $cfg_nbres_ppage))
    {
        $cpt_fin = ($debut / $cfg_nbres_ppage) + 1;
        $cpt_deb = $cpt_fin - $cfg_nb_pages + 1;
    }
    else
    {
        $cpt_deb = 1;

        $cpt_fin = (int)($nbtotal / $cfg_nbres_ppage);
        if (($nbtotal % $cfg_nbres_ppage) != 0) $cpt_fin++;

        if ($cpt_fin > $cfg_nb_pages) $cpt_fin = $cfg_nb_pages;
    }

    for ($cpt = $cpt_deb; $cpt <= $cpt_fin; $cpt++)
    {
        if ($cpt == ($debut / $cfg_nbres_ppage) + 1)
        {
            $barre .= "<A CLASS='off'>&nbsp;".$cpt."&nbsp;</A> ";
        }
        else
        {
            $barre .= "<A HREF='".$query.(($cpt-1)*$cfg_nbres_ppage);
            $barre .= "'>&nbsp;".$cpt."&nbsp;</A> ";
        }
    }


    // suivant . >
    // --------------------------------------------------------------------
    if ($debut + $cfg_nbres_ppage < $nbtotal)
    {
        $cible = $query.($debut+$cfg_nbres_ppage);
        $image = image_html('images/droite_on.gif');
        $lien = str_replace('{lien}', $image, $lien_on);
        $lien = str_replace('{cible}', $cible, $lien);
    }
    else
    {
        $image = image_html('images/droite_off.gif');
        $lien = str_replace('{lien}', $image, $lien_off);
    }
    $barre .= "&nbsp;<B>&middot;</B>".$lien;

    // fin . >>
    // --------------------------------------------------------------------
    $fin = ($nbtotal - ($nbtotal % $cfg_nbres_ppage));
    if (($nbtotal % $cfg_nbres_ppage) == 0) $fin = $fin - $cfg_nbres_ppage;

    if ($fin != $debut)
    {
        $cible = $query.$fin;
        $image = image_html('images/droite_on.gif');
        $lien = str_replace('{lien}', $image.$image, $lien_on);
        $lien = str_replace('{cible}', $cible, $lien);
    }
    else
    {
        $image = image_html('images/droite_off.gif');
        $lien = str_replace('{lien}', $image.$image, $lien_off);
    }
    $barre .= "<B>&middot;</B>&nbsp;".$lien;

    return($barre);
}


// ------------------------------------------------------------------------
// image_html
// ------------------------------------------------------------------------
function image_html($img, $align = "absmiddle")
{
    $taille = @getimagesize($img);
    return '<IMG SRC="'.$img.'" '.$taille[3].' BORDER=0 ALIGN="'.$align.'">';
}

// ------------------------------------------------------------------------
// afficher_ligne
// ------------------------------------------------------------------------
function afficher_ligne($cpt, $enr, $login, $typ_user,$flg)
{
    // alternance des couleurs des lignes de réponses
    $tit= htmlspecialchars($enr['ress-titre'],ENT_QUOTES,'ISO-8859-1');
    $ur= htmlspecialchars($enr['ress_url_lb'],ENT_QUOTES,'ISO-8859-1');
    $publique= htmlspecialchars($enr['ress_publique_on'],ENT_QUOTES,'ISO-8859-1');
    $auteur_ajout= htmlspecialchars($enr['ress_ajout'],ENT_QUOTES,'ISO-8859-1');
    $couleur = ($cpt % 2)
                  ? '#EEEEEE'
                  : '#FFFFFF';
   if (($typ_user != "APPRENANT") && ($publique=="NON")){
     return;
   }
    echo '<TR BGCOLOR="'.$couleur.'">';

    // n° de  réponse
    echo '<TD ALIGN="right" WRAP WIDTH="5%">&nbsp;<B>'.$cpt.'</B>.&nbsp;</TD>';

    // Titre et Url
    echo '<TD ALIGN="left"  WRAP WIDTH="25%">&nbsp;';
//     echo "<FONT COLOR=red><B>ressource non autorisée</B></FONT>";
   if ((((($typ_user == "RESPONSABLE_FORMATION" || $typ_user == "ADMINISTRATEUR" || $auteur_ajout == $login)  && $publique=="NON")) || ($publique=="OUI" && $typ_user != "APPRENANT")) && $ur != ''){
   if ((strstr($ur,"ParWeb")) || (strstr($ur,"parweb"))  || (strstr($ur,"Legweb"))  || (strstr($ur,"legweb")) || (strstr($ur,"Tatweb"))  || (strstr($ur,"tatweb")) || (strstr($ur,"Qcmweb"))  || (strstr($ur,"qcmweb")) || (strstr($ur,"Elaweb")) || (strstr($ur,"elaweb"))){
      $ur .= "&nom=pat&prenom=del&email=totot.fr@educ.fr&cacherresultat=1&pathscore=";
   }
   if (strstr($ur,"qcm")){
      $lien=$ur."&provenance=recherche";
      echo"<A href='#' onclick=\"window.open('$lien','','resizable=yes,scrollbars=yes,status=no')\">";
   }else{
      echo "<A href='#' onclick=\"window.open('$ur','','resizable=yes,scrollbars=yes,status=no')\">";
   }
    echo $tit;
    echo "</A>";
   }else{
    echo "<B>$tit</B>";
   }
    echo '&nbsp;</TD>';

    // Catégorie
    $sous_catalogue =htmlspecialchars($enr['ress_cat_lb'],ENT_QUOTES,'ISO-8859-1');
    $parente = GetDataField ($connect,"select ress_cdn from ressource_new where ress_cat_lb = \"$sous_catalogue\" AND ress_titre=''","ress_cdn");
    echo '<TD ALIGN="left"  NOWRAP WIDTH="10%">&nbsp;';
    $lien="recherche.php?flg=$flg&lien_sous_cat=1&parente=$parente&sous_catalogue=$sous_catalogue";
    $lien = urlencode($lien);
    echo "<A HREF=\"trace.php?link=$lien\"><IMG SRC=\"images/droite_on.gif\" BORDER=0 ALT=\"Cliquez ici pour afficher toutes les ressources liées à cette catégorie\">";
    echo "<SMALL>$sous_catalogue</SMALL>";
    echo "</A>";
    echo '&nbsp;</TD>';

    // Descriptif
    echo '<TD WIDTH="60%">&nbsp;<SMALL><U>Auteur(s):';
    echo htmlspecialchars($enr['ress_auteurs_cmt'],ENT_QUOTES,'ISO-8859-1');echo"</U>&nbsp;&nbsp;";
    echo htmlspecialchars($enr['ress_desc_cmt'],ENT_QUOTES,'ISO-8859-1');
    echo '&nbsp;</SMALL></TD>';

    echo '</TR>';
}
function viredir($dir){
 $dossier=opendir($dir);
  $total = 0;
  while ($fichier = readdir($dossier)) {
    $l = array('.', '..');
    if (!in_array( $fichier, $l)){
      if ($s_exp == "Ms"){
        if (is_dir($dir."\\".$fichier)){
          $total += viredir("$dir\\$fichier");
        }else{
          unlink("$dir\\$fichier");
          $total++;
        }
      }else{
        if (is_dir($dir."/".$fichier)){
          $total += viredir("$dir/$fichier");
        }else{
          unlink("$dir/$fichier");
          $total++;
        }
      }
    }
  }
  @closedir($dossier);
  rmdir($dir);
  return $total;
}
?>
