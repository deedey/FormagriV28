<?php
//connexion ‡ la base
function dbConnect()
{
   global $bdd,$adresse,$log,$mdp;
   $connect = mysql_connect("$adresse","$log","$mdp") or die ("<CENTER><A style='color:white;background-color:red'><BIG><B> DÈsolÈ, problËme de serveur </B></BIG></BODY></HTML>");
   mysql_select_db("$bdd",$connect) or die ("<CENTER><A style='color:white;background-color:red'><BIG><B> DÈsolÈ, problËme de serveur  </B></BIG></BODY></HTML>");
   $_SESSION['connect'] = $connect;
   $reqSet = mysql_query("SET SESSION connect_timeout=100");
   $reqSet = mysql_query("SET SESSION net_read_timeout = 100");
   $reqSet = mysql_query("SET SESSION net_write_timeout =100");
   $reqSet = mysql_query("SET SESSION query_cache_limit = 6");
   $reqSet = mysql_query("SET SESSION max_heap_table_size = 32");
   $reqSet = mysql_query("SET SESSION tmp_table_size = 32");
}
//connexion ‡ la base posfix
function Connecter($site)
{
   global $connecter,$adresse,$log,$mdp;
   $connecter=mysql_connect("$adresse","$log","$mdp") or die ("<CENTER><A style='color:white;background-color:red'><BIG><B> DÈsolÈ, problËme de serveur </B></BIG></BODY></HTML>");
   mysql_select_db("$site",$connecter) or die ("<CENTER><A style='color:white;background-color:red'><BIG><B> DÈsolÈ, problËme de serveur  </B></BIG></BODY></HTML>");
}
function requete($quoi,$tbl,$condition)
{
   $query = mysql_query ("SELECT $quoi FROM $tbl WHERE $condition");
   if (mysql_num_rows($query) > 0)
      return $query;
   else
      return FALSE ;
}
function requete_order($quoi,$tbl,$condition,$ordre)
{
   $query = mysql_query ("SELECT $quoi FROM $tbl WHERE $condition ORDER BY $ordre");
   if (mysql_num_rows($query) > 0)
      return $query;
   else
      return FALSE ;
}
function req_del($tbl,$condition)
{
   if ($condition != '')
      $query = mysql_query ("delete from $tbl where $condition");
   else
      $query = mysql_query ("delete from $tbl");
}
//Genere un ID
function Donne_ID ($connect,$requete)
{
   $query = mysql_query ($requete);
   $res = mysql_result ($query,0);
   if (!$res)
   {
      $res = 1;
      return $res;
   }
   else
      return ++$res;
}
//Genere un ID pour postfix

function IDMAX ($connecter,$requete)
{
   $query = mysql_query ($requete);
   $res = mysql_result ($query,0);
   if (!$res)
   {
      $res = 1;
      return $res;
   }
   else
      return ++$res;
}
function NewHtmlentities($content)
{
   return htmlentities($content,ENT_QUOTES,'ISO-8859-1');

}
function NewHtmlEntityDecode($content)
{
   return html_entity_decode($content,ENT_QUOTES,'ISO-8859-1');

}
function NewHtmlspecialchars($content)
{
   return htmlspecialchars($content,ENT_QUOTES,'ISO-8859-1');

}
//fonction pour ascenceur simple
function Ascenseur($Nom,$req,$connexion,$param)
{
   GLOBAL $connect;
   echo "<select name=$Nom style=\"color:#333333;background-color:#FFFFFF;font-size:12px;font-family:arial; border:1px solid #002D44;\">";
   echo "<OPTION value='-1'>- - - - - -</OPTION>";
   $res=mysql_query($req);
   $nbLig=mysql_num_rows($res);
   $i=0;
   while ($i<$nbLig)
   {
     $issue = @mysql_result($res,$i,mysql_field_name($res,0));
     if ($issue == $param)
        echo "<option value = \"".@mysql_result($res,$i,mysql_field_name($res,0))."\" selected>".retrecir(@mysql_result ($res,$i,mysql_field_name($res,1)))."</option>";
     else
        echo "<option value = \"".@mysql_result($res,$i,mysql_field_name($res,0))."\">".retrecir(@mysql_result ($res,$i,mysql_field_name($res,1)))."</option>";
     $i++;
   }
   echo "</select>";
}
function Ascenseur_sans_blanc($Nom,$req,$connexion,$param)
{
   GLOBAL $connect;
   echo "<select name=$Nom style=\"color:#333333;background-color:#FFFFFF;font-size:12px;font-family:arial; border:1px solid #002D44;\">";
   if ($param == '')
      echo "<OPTION value='-1'>- - - - - -</OPTION>";
   $res=mysql_query($req);
   $nbLig=mysql_num_rows($res);
   $i=0;
   while ($i<$nbLig)
   {
     $issue = @mysql_result($res,$i,mysql_field_name($res,0));
     if ($issue == $param)
     {
        echo "<option value = \"".@mysql_result($res,$i,mysql_field_name($res,0))."\" selected>".retrecir(@mysql_result ($res,$i,mysql_field_name($res,1)))."</option>";
     }
     $i++;
   }
   if ($param != '')
      echo "<OPTION value='-1'>- - - - - -</OPTION>";
   $i=0;
   while ($i<$nbLig)
   {
     $issue = @mysql_result($res,$i,mysql_field_name($res,0));
     if ($issue != $param)
        echo "<option value = \"".@mysql_result($res,$i,mysql_field_name($res,0))."\">".retrecir(@mysql_result ($res,$i,mysql_field_name($res,1)))."</option>";
     $i++;
   }
   echo "</select>";
}
function Ascenseur_sans_blanc_large($Nom,$req,$connexion,$param)
{
   GLOBAL $connect;
   echo "<select name=$Nom style=\"color:#333333;background-color:#FFFFFF;font-size:12px;font-family:arial; border:1px solid #002D44;\">";
   if ($param == '')
      echo "<OPTION value='-1'>- - - - - -</OPTION>";
   $res=mysql_query($req);
   $nbLig=mysql_num_rows($res);
   $i=0;
   while ($i<$nbLig)
   {
     $issue = @mysql_result($res,$i,mysql_field_name($res,0));
     if ($issue == $param)
     {
        echo "<option value = \"".@mysql_result($res,$i,mysql_field_name($res,0))."\" selected>".@mysql_result ($res,$i,mysql_field_name($res,1))."</option>";
     }
     $i++;
   }
   if ($param != '')
      echo "<OPTION value='-1'>- - - - - -</OPTION>";
   $i=0;
   while ($i<$nbLig)
   {
     $issue = @mysql_result($res,$i,mysql_field_name($res,0));
     if ($issue != $param)
        echo "<option value = \"".@mysql_result($res,$i,mysql_field_name($res,0))."\">".@mysql_result ($res,$i,mysql_field_name($res,1))."</option>";
     $i++;
   }
   echo "</select>";
}
function EduNet($linker)
{
  if (strstr($linker,"http://www.editions.educagri.fr/educagriNet") || strstr($linker,"educagrinet.educagri.fr"))
     return TRUE;
  else
     return FALSE;
}
function retrecir($lenom)
{
     $longueur = strlen($lenom);
     if ($longueur > 20)
        $le_nom = substr($lenom,0,18)."..";
     else
        $le_nom = $lenom;
     return $le_nom;
}
function Ascenseur_grp($Nom,$req,$connexion,$param)
{
   GLOBAL $connect;
   echo "<select name=$Nom style=\"color:#333333;background-color:#FFFFFF;font-size:12px;font-family:arial; border:1px solid #002D44;\">";
   echo "<OPTION value='-1'>- - - - - -</OPTION>";
   $res=mysql_query($req);
   $nbLig=mysql_num_rows($res);
   $i=0;
   while ($i<$nbLig)
   {
     $issue = @mysql_result($res,$i,mysql_field_name($res,0));
     $nom_grp = @mysql_result($res,$i,mysql_field_name($res,1));
     $carac_grp = strlen($nom_grp);
     if ($carac_grp > 34)
        $nom1 = substr($nom_grp,0,34)."..";
     else
        $nom1 = $nom_grp;
     if ($issue == $param)
        echo "<option value = \"".@mysql_result($res,$i,mysql_field_name($res,0))."\" title=\"$nom_grp\" selected />$nom1</option>";
     else
        echo "<option value = \"".@mysql_result($res,$i,mysql_field_name($res,0))."\" title=\"$nom_grp\" />$nom1</option>";
     $i++;
   }
   echo "</select>";
}
//crÈation de menu dÈroulant ‡ partir d'une table
//fonction pour ascenseur ‡ affichage de plusieurs paramËtres
function Ascenseur_mult($Nom,$req,$connexion,$param)
{
   GLOBAL $connect;
   echo "<select name=$Nom style=\"color:#333333;background-color:#FFFFFF;font-size:12px;font-family:arial; border:1px solid #002D44;\">";
   echo "<OPTION value='-1'>- - - - - - -</OPTION>";
   $res=mysql_query($req);
   $nbLig=mysql_num_rows($res);
   $i=0;
   while ($i<$nbLig)
   {
     $nommer = @mysql_result ($res,$i,mysql_field_name($res,1));
     if (strlen($nommer) > 12)
        $nom = substr($nommer,0,10)."..";
     else
        $nom = $nommer;
     $prenommer = @mysql_result ($res,$i,mysql_field_name($res,2));
     if (strlen($prenommer) > 6)
        $prenom = substr($prenommer,0,5)."..";
     else
        $prenom = $prenommer;
     $issue = @mysql_result($res,$i,mysql_field_name($res,0));
     if ($issue == $param)
        echo "<option value = \"".@mysql_result($res,$i,mysql_field_name($res,0))."\" selected>$nom &nbsp;&nbsp;-&nbsp;&nbsp;$prenom </option>";
     else
        echo "<option value = \"".@mysql_result($res,$i,mysql_field_name($res,0))."\">$nom &nbsp;&nbsp;-&nbsp;&nbsp;$prenom</option>";
     $i++;
   }
   echo "</select>";
}
function Ascenseur_mult_tot($Nom,$req,$connexion,$param)
{
   GLOBAL $connect;
   echo "<select name=$Nom style=\"color:#333333;background-color:#FFFFFF;font-size:12px;font-family:arial; border:1px solid #002D44;\">";
   echo "<OPTION value='-1'>- - - - - - -</OPTION>";
   $res=mysql_query($req);
   $nbLig=mysql_num_rows($res);
   $i=0;
   while ($i<$nbLig)
   {
     $nommer = @mysql_result ($res,$i,mysql_field_name($res,1));
     $nom = $nommer;
     $prenommer = @mysql_result ($res,$i,mysql_field_name($res,2));
     $prenom = $prenommer;
     $issue = @mysql_result($res,$i,mysql_field_name($res,0));
     if ($issue == $param)
        echo "<option value = \"".@mysql_result($res,$i,mysql_field_name($res,0))."\" selected>$nom &nbsp;&nbsp;-&nbsp;&nbsp;$prenom </option>";
     else
        echo "<option value = \"".@mysql_result($res,$i,mysql_field_name($res,0))."\">$nom &nbsp;&nbsp;-&nbsp;&nbsp;$prenom</option>";
     $i++;
   }
   echo "</select>";
}
//fonction pour ascenceur affichage
function Ascenseur_affichage($Nom,$req,$connexion,$param,$utilisateur,$lg)
{
   GLOBAL $connect;
   require "lang$lg.inc.php";
   $res=mysql_query($req);
   $nbLig=mysql_num_rows($res);
   $i=0;
   echo "<tr><td align=left>";
   while ($i < $nbLig)
   {
     if ($i == 0)
        echo "<ul>";
     $id_grp = @mysql_result($res,$i,mysql_field_name($res,0));
     $nom_grp = GetDataField ($connect,"SELECT grp_nom_lb from groupe where grp_cdn = '$id_grp'","grp_nom_lb");
     $carac_grp = strlen($nom_grp);
     if ($carac_grp > 33)
        $issue = substr($nom_grp,0,31)."..";
     else
        $issue = $nom_grp;
     $nom_app = GetDataField ($connect,"SELECT util_nom_lb  from utilisateur where util_cdn = $utilisateur","util_nom_lb");
     $prenom_app = GetDataField ($connect,"SELECT util_prenom_lb from utilisateur where util_cdn = $utilisateur","util_prenom_lb");
     $majuscule =$nom_app." ".$prenom_app;
     $n_presc = mysql_num_rows(mysql_query("select presc_cdn from prescription_$id_grp where presc_utilisateur_no=$utilisateur"));
     if ($n_presc > 0)
        echo "<li class='sequence'><a href=\"gest_parc_frm1.php?saut=1&utilisateur=$utilisateur&a_faire=1&numero_groupe=$id_grp \"".
             bulle("$mess_lanc_mess1 $majuscule<br />$mess_auth_click $mess_lanc_mess2","","RIGHT","ABOVE",200)." $issue</a></li>";
     else
        echo "<li><IMG SRC=\"images/quoi.gif\" border='0' width='12' height='12' style=\"cursor: help;\" ".
              bulle("$majuscule : $mess_gp_tut_nopresc","","RIGHT","ABOVE",200)." &nbsp;&nbsp;$issue</li>";
     if ($i == $nbLig-1) echo "</ul>";
     $i++;
   }
   echo "</td></tr>";
}

//recupere les donnees d'une colonne specifique ($field)
function GetDataField($connect,$req,$field)
{
   global $connect;
   $query = mysql_query($req);//if ($query == FALSE) echo $req;
   if ($query == TRUE)
   {
       $Nb_Lig = mysql_num_rows($query);
       if ($Nb_Lig > 0)
       {
          $i=0;
          while ($i != $Nb_Lig)
          {
             $res = mysql_result ($query,$i,$field);
             $i++;
          }
          return $res;
       }
       else
          return FALSE;
   }
   else
      return FALSE;
}

function NomUser($num)
{
   GLOBAL $connect,$lg;
   $nomUser = GetDataNP ($connect,"select util_nom_lb,util_prenom_lb from utilisateur where ".
                                 "util_cdn = '".$num."'","util_nom_lb,util_prenom_lb");
   return $nomUser;
}

function GetDataNP ($connect,$req,$field)
{
   $res="";
   $query = mysql_query($req);
   $Nb_Lig = mysql_num_rows($query);
   $tab = explode(",",$field);
   if ($Nb_Lig > 0)
   {
      $i=0;
      while ($i != $Nb_Lig)
      {
         $res .= mysql_result ($query,$i,$tab[0]);
         $res .="  ".mysql_result ($query,$i,$tab[1]);
         $i++;
      }
      return $res;
   }
   else
      return FALSE;
}

function serveur_externe($url_ressource)
{
   $req_serv = mysql_query("select * from serveur_ressource");
   $nb_req_serv = mysql_num_rows($req_serv);
   if ($nb_req_serv > 0)
   {
       $ssi = 0;
       while ($ssi < $nb_req_serv)
       {
             $adr = mysql_result($req_serv,$ssi,"serveur_nomip_lb");
             $params = mysql_result($req_serv,$ssi,"serveur_param_lb");
             $label = mysql_result($req_serv,$ssi,"serveur_label_lb");
             if ($label != "")
             {
                 if (strstr($url_ressource,$adr) && strstr($url_ressource,$label))
                 {
                    $url_ressource = str_replace("%label=$label","",$url_ressource);
                    $url_ressource .= $params;
                    break;
                 }
             }
             elseif ($label == "" && strstr($url_ressource,"label="))
             {
                  $ssi++;
                  continue;
             }
             elseif ($label == "" && !strstr($url_ressource,"label="))
             {
                  if (strstr($url_ressource,$adr))
                  {
                       $url_ressource .= $params;
                       break;
                  }
             }
          $ssi++;
       }
   }
   return $url_ressource;
}
// affiche durÈe de l'activitÈ
function AffiDureeAct($act,$scorm,$id_app,$numero_groupe)
{
     GLOBAL $connect;
     $minutes_total = 0;
     $item = ($scorm == 1) ? "traq_mod_no" : "traq_act_no";
     $req_traq = mysql_query("select * from traque where
                              traq_util_no = $id_app AND
                              traque.traq_grp_no = $numero_groupe AND
                              $item = $act
                              ORDER BY traq_date_dt,traq_hd_dt asc");
     $nbr_trq = mysql_num_rows($req_traq);
     $i=0;$dateValid = '';
     while ($i < $nbr_trq)
     {
       $num_traq = mysql_result($req_traq,$i,"traq_cdn");
       $date_trq = mysql_result($req_traq,$i,"traq_date_dt");
       $hd = mysql_result($req_traq,$i,"traq_hd_dt");
       $hf = mysql_result($req_traq,$i,"traq_hf_dt");
       $h_fin = $hf;
       if ($hf == "00:00:00")
       {
         $ch_heure_deb = explode (":",$hd);
         $heure_deb = $ch_heure_deb[0];
         $minutes_deb = $ch_heure_deb[1];
         $sec_deb = $ch_heure_deb[2];
         $duree_act = GetDataField ($connect,"SELECT act_duree_nb FROM activite where act_cdn = $act","act_duree_nb");
         $mfin = $heure_deb*60 + $duree_act;
         $hfin = floor($mfin/60);
         $mreste = $mfin%60;
         if ($mreste == 0)
            $hfin = "$hfin:$minutes_deb:$sec_deb";
         else
         {
            $min_fin = $minutes_deb+$mreste;
            if ($min_fin > 59)
            {
              $hfin = $hfin+1;
              $min_fin = $min_fin - 60;
              if (strlen($min_fin) <10) $min_fin="0".$min_fin;
              if ($hfin > 24)
                $hfin = $hfin - 24;
            }
            $hfin = "$hfin:$min_fin:$sec_deb";
         }
        $req = mysql_query ("UPDATE traque SET traq_hf_dt = \"$hfin\" where traq_cdn = $num_traq");
       }
       if (strstr($date_trq,'-'))
          $ch_date = explode("-",$date_trq);
       elseif (strstr($date_trq,'/'))
          $ch_date = explode("/",$date_trq);
       $date_traq = $ch_date[2]."/".$ch_date[1]."/".$ch_date[0];
       if ($i > 0)
          $dateValid .= '<br />';
       $dateValid .= $date_traq;
       $hf = GetDataField ($connect,"SELECT traq_hf_dt FROM traque where traq_cdn = $num_traq","traq_hf_dt");
       $ch_heure_fin = explode (":",$hf);
       $hf = $ch_heure_fin[0];
       $minutes_fin = $ch_heure_fin[1];
       $ch_heure_deb = explode (":",$hd);
       $heure_deb = $ch_heure_deb[0];
       if ($hf < $heure_deb && $h_fin != "00:00:00")
         $hf +=24;
       $minutes_deb = $ch_heure_deb[1];
       $dif_heures=$hf-$heure_deb;
       $minutes_plus = ($dif_heures == 0) ? 0 : $dif_heures*60;
       if (($minutes_fin > $minutes_deb) || ($minutes_fin == $minutes_deb))
       {
          $minutes = $minutes_fin-$minutes_deb;
          $minutes_rest = $minutes;
       }
       else
       {
          $dif_heures--;
          $minutes_plus = 60-$minutes_deb+$minutes_fin;
          $minutes = $dif_heures*60;
          $minutes_rest = $minutes_plus;
       }
       $minutes_total += $minutes+$minutes_plus;
       if (isset($hfn) && $hfn == 1)
        $minutes_total += 1;
       $hfn = 0;
     $i++;
     }
     return $nbr_trq."|".$minutes_total."|".$dateValid;
}
function AfficheDureeAN($dureeTotale,$nbTrq)
{
  GLOBAL $lg;
  require ("lang$lg.inc.php");
  if ($dureeTotale == 0)
     return "0 mn";
  else
  {
       $heure = floor($dureeTotale/60);
       if ($heure > 0)
           $reste = $dureeTotale%60;
       else
         $reste = $dureeTotale;
       if ($reste == 0 && $heure > 0)
         $dureeAffichee = $heure."$h";
       elseif ($reste == 0 && $heure == 0)
         $dureeAffichee = "< 1$mn";
       elseif ($reste > 0 && $heure == 0)
         $dureeAffichee = $reste.$mn;
       else
         $dureeAffichee = $heure.$h.' '.$reste.$mn;
      return $dureeAffichee;
  }
}

// Parse une URL pour vÈrifier son existence
function url_exists($url)
{
  $a_url = parse_url($url);
  if (!isset($a_url['port']))
     $a_url['port'] = 80;
  $errno = 0;
  $errstr = '';
  $timeout = 30;
  if (isset($a_url['host']) && $a_url['host']!=gethostbyname($a_url['host']))
  {
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
//crÈation de menu dÈroulant ‡ partir d'une table
// generateur de mot de passe
function formagri_genmotpass($long)
{
    mt_srand((double)microtime()*1000000);
    $voyelles = array("a", "e", "i", "o", "u");
    $consonnes = array("b", "c", "d", "f", "g", "h", "j", "k", "l", "m", "n", "p", "q", "r", "s", "t", "v", "w","z", "tr",
    "cr", "br", "fr", "th", "dr","gr", "ch", "ph", "st", "sp", "pr", "sl", "cl","pl");
    $num_voyelles = count($voyelles);
    $num_consonnes = count($consonnes);
    $password = "";
    for($i = 0; $i < $long; $i++)
    {
        $password .= $consonnes[mt_rand(0, $num_consonnes - 1)] . $voyelles[mt_rand(0, $num_voyelles - 1)];
    }
    return substr($password, 0, $long);
}
function affiche_creneau($cren,$lg)
{
    require("lang$lg.inc.php");
    if ($cren == 1)  $crn = "$entre 8$h $et 9$h";
    if ($cren == 2)  $crn = "$entre 9$h $et 10$h";
    if ($cren == 3)  $crn = "$entre 10$h $et 11$h";
    if ($cren == 4)  $crn = "$entre 11$h $et 12$h";
    if ($cren == 5)  $crn = "$entre 12$h $et 13$h";
    if ($cren == 6)  $crn = "$entre 13$h $et 14$h ";
    if ($cren == 7)  $crn = "$entre 14$h $et 15$h ";
    if ($cren == 8)  $crn = "$entre 15$h $et 16$h";
    if ($cren == 9)  $crn = "$entre 16$h $et 17$h ";
    if ($cren == 10)  $crn = "$entre 17$h $et 18$h";
    if ($cren == 11)  $crn = "$entre 18$h $et 19$h ";
    if ($cren == 12)  $crn = "$entre 19$h $et 20$h";
    if ($cren == 13)  $crn = "$entre 20$h $et 21$h ";
    if ($cren == 14)  $crn = "$entre 21$h $et 22$h";
    if ($cren == 15)  $crn = "$entre 22$h $et 23$h ";
    return $crn;
}

/**
 * File: $Id$
*
* Short description of purpose of file
*
* @package validation
* @copyright (C) 2003 by the Xaraya Development Team.
*/
// Taken from http://www.zend.com/codex.php?id=449&single=1
/* =======================================================================
  ifsnow's email valid check function SnowCheckMail Ver 0.1
  funtion SnowCheckMail ($Email,$Debug=false)
  $Email : E-Mail address to check.
  $Debug : Variable for debugging.

* Can use everybody if use without changing the name of function.

Reference : O'REILLY - Internet Email Programming
  HOMEPAGE : http://www.hellophp.com
  ifsnow is korean phper. Is sorry to be unskillful to English. *^^*;;
  ========================================================================= */

//Retourne le email si il est OK.
function verifie_email($email)
{
  if (strlen($email) < 6)
     return "$email : Email trop court";
  if (strlen($email) > 255)
     return "$email : Email trop long";
  if (!strstr( $email,"@"))
     return "$email : Le email n'a pas d'arobase (@)";
  if (preg_match_all("/([^a-zA-Z0-9_\@\.\-])/i", $email, $trouve))
     return "$email ---> [".implode($email, $trouve[0])."] : caractËre interdit dans un email";
  if (!preg_match("/^([a-zA-Z0-9_]|\\-|\\.)+@(([a-z0-9_]|\\-)+\\.)+[a-z]{2,4}\$/i", $email))
     return "$email : ce n'est pas la forme d'un email.";
   return $email;
}

// ------------------------------------------------------------------------
// barre_navigation
// ------------------------------------------------------------------------
function barre_navigation($nbtotal,$nbenr,$cfg_nbres_ppage,$debut,$cfg_nb_pages,$criteres,$type,$typ_user,$publique,$login,$flg)
{
    // --------------------------------------------------------------------
       $lien_on         = '&nbsp;<A HREF="{cible}">{lien}</A>&nbsp;';
       $lien_off        = '&nbsp;{lien}&nbsp;';
    // --------------------------------------------------------------------

    $query  = $criteres.'&type='.$type.'&recherche=1&debut=';

    // dÈbut << .
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
    // prÈcÈdent < .
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
        $cpt_fin = intval($nbtotal / $cfg_nbres_ppage);
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
    if (($nbtotal % $cfg_nbres_ppage) == 0)
      $fin = $fin - $cfg_nbres_ppage;
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
function afficher_ligne($cpt, $enr, $login, $typ_user, $flg, $lg, $adres,$criteres)
{
    GLOBAL $connect;
    require "lang$lg.inc.php";
    require "graphique/admin.inc.php";
    dbConnect();
    $id_ress = $enr['ress_cdn'];
    $tit = htmlspecialchars($enr['ress_titre'],ENT_QUOTES,'ISO-8859-1');
    $ur = htmlspecialchars($enr['ress_url_lb'],ENT_QUOTES,'ISO-8859-1');
    $publicite = htmlspecialchars($enr['ress_publique_on'],ENT_QUOTES,'ISO-8859-1');
    $auteur_ajout=  htmlspecialchars($enr['ress_ajout'],ENT_QUOTES,'ISO-8859-1');
    // alternance des couleurs des lignes de rÈponses
    echo couleur_tr($cpt+1,'');
    // n∞ de  rÈponse
    echo '<TD WRAP WIDTH="2%" valign="top">&nbsp;<B>'.$cpt.'</B>&nbsp;</TD>';

    // Titre et Url
    echo '<TD ALIGN="left"  WRAP WIDTH="25%" valign="top">';

   if ((($typ_user == "ADMINISTRATEUR" || $auteur_ajout == $login)  && $publicite=="NON") || ($publicite=="OUI" && $typ_user != "APPRENANT") && $ur != '')
   {
      $req_serv = mysql_query("select * from serveur_ressource");
      $nb_req_serv = mysql_num_rows($req_serv);
      if ($nb_req_serv > 0)
      {
         $ir = 0;
         while ($ir < $nb_req_serv)
         {
             $adr = mysql_result($req_serv,$ir,"serveur_nomip_lb");
             $params = mysql_result($req_serv,$ir,"serveur_param_lb");
             $label = mysql_result($req_serv,$ir,"serveur_label_lb");
             if ($label != "")
             {
                if (strstr($ur,$adr) && strstr($ur,$label))
                {
                   $ur = str_replace("%label=$label","",$ur);
                   $ur .= $params;
                   $transit = 1;
                   break;
                }
             }elseif ($label == "" && strstr($ur,"label="))
             {
                $ir++;
                continue;
             }elseif ($label == "" && !strstr($ur,"label="))
             {
                if (strstr($ur,$adr)){
                   $ur .= $params;
                   $transit = 1;
                   break;
                }
             }
             $ir++;
         }
         if ($transit == 1)
         {
            $ur = urldecode($ur);
            echo "<A HREF=\"javascript:void(0);\" onclick=\"window.open('$ur','','resizable=yes,scrollbars=yes,status=no')\">";
            $transit = 0;
            $traspasse = 1;
         }
      }
     if (EduNet($ur) == TRUE)
     {
        $ur .="&url=$url_ress&auth_cdn=$auth_cdn";
        $ur = urlencode($ur);
        echo "<A HREF='#' style=\"padding-left: 2px;padding-right: 4px;\" ".
             "onclick=\"window.open('trace.php?link=$ur','','status=no,directories=no,copyhistory=0,titlebar=no,toolbar=no,location=no,menubar=yes,scrollbars=yes,resizable=yes')\">";
     }
     elseif (strstr($ur,"qcm"))
     {
        $ur .="&provenance=recherche";
        echo "<A HREF='#' style=\"padding-left: 2px;padding-right: 4px;\" ".
             "onclick=\"window.open('lancer_ressource.php?lien=$ur','','status=no,directories=no,copyhistory=0,titlebar=no,toolbar=no,location=no,menubar=yes,scrollbars=yes,resizable=yes')\">";
     }
     elseif(strstr(strtolower($ur),".doc") || strstr(strtolower($ur),".xls") || strstr(strtolower($ur),".xlt"))
     {
        echo "<A HREF=\"$ur\" style=\"padding-left: 2px;padding-right: 4px;\" target='_blank'>";
     }
     elseif ($traspasse != 1)
     {
        $traspasse = 0;
        echo "<A HREF='#' style=\"padding-left: 2px;padding-right: 4px;\" onclick=\"window.open('lancer_ressource.php?lien=".urlencode($ur).
             "','','status=no,directories=no,copyhistory=0,titlebar=no,toolbar=no,location=no,menubar=yes,scrollbars=yes,resizable=yes')\">";
     }
     echo strip_tags(html_entity_decode($tit,ENT_QUOTES,'ISO-8859-1'));
     echo "</A>";
   }
   else
   {
    echo "<span style=\"padding-left: 2px;padding-right: 4px;font-weight: bold;\">".
         strip_tags(html_entity_decode($tit,ENT_QUOTES,'ISO-8859-1'))."</span>";
   }
    echo '&nbsp;</TD>';
    // CatÈgorie
    $sous_catalogue = $enr['ress_cat_lb'];
    $parente = GetDataField ($connect,"select ress_cdn from ressource_new where ress_cat_lb = \"$sous_catalogue\" AND ress_titre =''","ress_cdn");
    echo '<TD ALIGN="left"  NOWRAP WIDTH="10%" valign="top">';
    $lien = $adres."?flg=$flg&lien_sous_cat=1&parente=$parente&sous_catalogue=$sous_catalogue";
    $lien = urlencode($lien);
    echo "<A HREF=\"trace.php?link=$lien\" style=\"padding-left: 2px;padding-right: 4px;\" ".
          bulle("$mess_ress_tts_cat","","RIGHT","BELOW",180).$sous_catalogue."</A>";
    echo '</TD>';

    // Descriptif
    if ($tit != "")
    {
      echo "<TD WIDTH=\"60%\"><span style=\"padding-left: 2px;padding-right: 4px;font-weight: bold;font-size: 10px;\">$mrc_auteur :";
      echo $enr['ress_auteurs_cmt'];
      echo "</span>";
    }
    else
      echo "<TD WIDTH=\"60%\"><span style=\"padding-left: 2px;padding-right: 4px;font-weight: bold;font-size: 10px;\">";
    echo "<br />".html_entity_decode($enr['ress_desc_cmt'],ENT_QUOTES,'iso-8859-1');
    echo "</span></TD>";
    if (isset($_SESSION['getVarsRech']))
    {
       $lien = $_SESSION['getVarsRech']."&id_ress=$id_ress";
       echo "<td><a href=\"$lien\">".
            "<IMG SRC=\"images/modules/tut_form/icosequen20.gif\" BORDER='0' ALT=\"$mrc_ins_seq\"></A></td>";
    }
    echo '</TR>';
}
//---------------------------------------------------------------
function viredir($dir,$s_exp)
{
  $dossier=opendir($dir);
  $total = 0;
  while ($fichier = readdir($dossier))
  {
   $l = array('.', '..');
   if (!in_array( $fichier, $l))
   {
       if (is_dir($dir."/".$fichier))
         $total += viredir("$dir/$fichier",$s_exp);
       else
       {
         unlink("$dir/$fichier");
         $total++;
       }
     }
  }
 @closedir($dossier);
 rmdir($dir);
 return $total;
}
// Calcul de la taille d'un dossier

function DirSize($path , $recursive=TRUE)
{
  $result = 0;
  if (!is_dir($path) || !is_readable($path))
     return 0;
  $fd = dir($path);
  while($file = $fd->read())
  {
    if(($file != ".") && ($file != ".."))
    {
      if (@is_dir("$path$file/"))
           $result += $recursive?DirSize("$path$file/"):0;
      else
           $result += filesize("$path$file");
    }
  }
  $fd->close();
  return $result;
}

function descendance($path , $recursive=TRUE)
{
  $result = 0;
  $result_nb = 0;
  if (!is_dir($path) || !is_readable($path))
     return 0;
  $fd = dir($path);
  while($file = $fd->read())
  {
    if(($file != ".") && ($file != ".."))
    {
      if (@is_dir("$path$file/"))
           $result_nb += $recursive?descendance("$path$file/"):0;
      else
           $result_nb++;
    }
  }
  $fd->close();
  return $result_nb;
}
function result_taille($nb)
{
  if ($nb > 0)
  {
      if ($nb >= 1000000)
         $taille = round(($nb/1000000),2)." Mo";
      elseif ($nb < 1000000 && $nb >= 1000)
         $taille = round(($nb/1000),2)." Ko";
      elseif ($nb < 1000)
         $taille = ($nb)." o";
  }
  else
      $taille = '';
  return $taille;
}
// Lit et retourne le contenu d'un repertoire
// deux fonctions renvoyant la taille d'un fichier et sa date de modification



function taille($fichier)
{
  $size_unit="o";
  $taille=filesize($fichier);
  if ($taille >= 1000000000)
     $taille = round($taille / 1000000000 * 100) / 100 . " K".$size_unit;
  elseif ($taille >= 1000000)
     $taille = round($taille / 1000000 * 100) / 100 . " M".$size_unit;
  elseif ($taille >= 1000)
     $taille = round($taille / 1000 * 100) / 100 . " K".$size_unit;
  else
     $taille=$taille. " ".$size_unit;
  if($taille==0)
     $taille="-";
  return $taille;
}

function date_modif($fichier)
{
  $tmp = filemtime($fichier);
  return date("d/m/Y",$tmp);
}

// lit l'image qui correspond ‡ un type de fichier et la retourne sous la forme mimetype(nomdufichier,"image")
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

function modif_nom($fichier_test)
{
    $fichier_test = strtr($fichier_test,
    '¿¡¬√ƒ≈«»… ÀÃÕŒœ“”‘’÷Ÿ⁄€‹›‡·‚„‰ÂÁËÈÍÎÏÌÓÔÚÛÙıˆ˘˙˚¸˝ˇ',
    'AAAAAACEEEEIIIIOOOOOUUUUYaaaaaaceeeeiiiioooooouuuuyy');
    // remplacer les caracteres autres que lettres, chiffres et point par _
    $fichier_test = preg_replace('/([^\/.a-z0-9]+)/i', '_',$fichier_test);
  return $fichier_test;
}

function modif_az_qw($fichier_test)
{
    $fichier_test = strtr($fichier_test,
    '¿¡¬√ƒ≈«»… ÀÃÕŒœ“”‘’÷Ÿ⁄€‹›‡·‚„‰ÂÁËÈÍÎÏÌÓÔÚÛÙıˆ˘˙˚¸˝ˇ',
    'AAAAAACEEEEIIIIOOOOOUUUUYaaaaaaceeeeiiiioooooouuuuyy');
  return $fichier_test;
}

function modif_special_nom($fichier_test)
{
    $fichier_test = strtr($fichier_test,
    '¿¡¬«»… ÀÃÕŒœ“”‘’÷Ÿ⁄€‹›',
    'AAACEEEEIIIIOOOOOUUUUY');
  return $fichier_test;
}

function recharge()
{
    $agent=getenv("HTTP_USER_AGENT");
    if (strstr($agent,"MSIE"))
    {
       echo "<SCRIPT Language=\"Javascript\">";
       echo "window.parent.opener.location.reload();";
       echo "</SCRIPT>";
    }
    else
    {
       echo "<SCRIPT Language=\"Javascript\">";
       echo "parent.parent.opener.location.reload();";
       echo "</SCRIPT>";
    }
}
function ForceFileDownload($file,$type)
{
    $len = filesize($file);
    $filename = basename($file);
    $file_extension = strtolower(substr(strrchr($filename,"." ),1));
    $filesize = @filesize($file);
    switch( $file_extension )
    {
        case "pdf": $ctype="application/pdf"; break;
        case "exe": $ctype="application/octet-stream"; break;
        case "zip": $ctype="application/zip"; break;
        case "doc": $ctype="application/msword"; break;
        case "xls": $ctype="application/vnd.ms-excel"; break;
        case "ppt": $ctype="application/vnd.ms-powerpoint"; break;
        case "gif": $ctype="image/gif"; break;
        case "png": $ctype="image/png"; break;
        case "jpeg":
        case "jpg": $ctype="image/jpg"; break;
        case "mp3": $ctype="audio/mpeg"; break;
        case "wav": $ctype="audio/x-wav"; break;
        case "mpeg":
        case "mpg":
        case "mpe": $ctype="video/mpeg"; break;
        case "mov": $ctype="video/quicktime"; break;
        case "avi": $ctype="video/x-msvideo"; break;
      default: $ctype="application/force-download";
    }
    header('Content-Type: application/octet-stream');
    header("Content-Transfer-Encoding: $ctype");
    header("Content-disposition: attachment; filename=\"" . basename($file) . "\"");
    readfile($file);
}

//Fonction de transformation d'UTF-8 en ISO au cas ou c'est de l'UTF-8 (gestion multilingue)
function seems_utf8($str)
{
   for ($i=0; $i<strlen($str); $i++)
   {
       if (ord($str[$i]) < 0x80)
          continue; // 0bbbbbbb
       elseif ((ord($str[$i]) & 0xE0) == 0xC0) $n=1; // 110bbbbb
       elseif ((ord($str[$i]) & 0xF0) == 0xE0) $n=2; // 1110bbbb
       elseif ((ord($str[$i]) & 0xF8) == 0xF0) $n=3; // 11110bbb
       elseif ((ord($str[$i]) & 0xFC) == 0xF8) $n=4; // 111110bb
       elseif ((ord($str[$i]) & 0xFE) == 0xFC) $n=5; // 1111110b
       else
           return false; // Does not match any model
       for ($j=0; $j<$n; $j++)
       { // n bytes matching 10bbbbbb follow ?
           if ((++$i == strlen($str)) || ((ord($str[$i]) & 0xC0) != 0x80))
              return false;
       }
   }
   return true;
}

function utf8_decode_si_utf8($str)
{
   return seems_utf8($str)? utf8_decode($str): $str;
}

//Gestion dates
function date_forum($date)
{
      $dater = explode(" ",$date);
      $heure = substr($date,11);
      $ch_date = explode ("-",$dater[0]);
      $ladate = "$ch_date[2]/$ch_date[1]/$ch_date[0]";
      return "$ladate|$heure";
}

function le_mois($mois)
{
      GLOBAL $lg;
      require_once "lang$lg.inc.php";
      if ($lg == "fr")
        $mois_list = array("", "Janvier", "FÈvrier", "Mars", "Avril", "Mai", "Juin", "Juillet", "Ao˚t", "Septembre", "Octobre", "Novembre", "DÈcembre");
      elseif ($lg == "ru")
        $mois_list = array("","ﬂÌ‚‡¸","‘Â‚‡Î¸","Ã‡Ú","¿ÔÂÎ¸","Ã‡È","»˛Ì¸","»˛Î¸","¿‚„ÛÒÚ","—ÂÌÚˇ·¸","ŒÍÚˇ·¸","ÕÓˇ·¸","ƒÂÍ‡·¸");
      elseif ($lg == "en")
        $mois_list = array("","January","February","March","April","May","June","Julliet","August","September","October","November","December");

      return $mois_list[$mois];
}

function nb_jours($la_date)
{
      $nb_date_query = mysql_query ("select TO_DAYS('$la_date')");
      $nb = mysql_result ($nb_date_query,0);
      return $nb;
}

function nb_jours_from($la_date)
{
      $nb_date_query = mysql_query ("select FROM_DAYS('$la_date')");
      $nb = mysql_result ($nb_date_query,0);
      return $nb;
}

function duree_calc($duree)
{
  GLOBAL $lg;
  require "lang$lg.inc.php";
  $heure = floor($duree/60);
  if ($heure > 0)
    $reste = $duree%60;
  else
    $reste = $duree;
  if ($reste != 0 && strlen(strval($reste)) == 1)
     $reste = "0".$reste;
  if ($reste != 0)
    $duree = $heure."$h".$reste;
  else
    $duree = $heure."$h";
  return $duree;
}

function DateTiretInv($ladate,$sepIn,$sepOut)
{
   $sepEn = (!empty($sepIn)) ? $sepIn : "-";
   $sepSt = (!empty($sepOut)) ? $sepOut : "-";
   $ch_date_fin = explode($sepIn,$ladate);
   $jour = $ch_date_fin[2];
   $mois = $ch_date_fin[1];
   $an = $ch_date_fin[0];
   $date = $jour.$sepSt.$mois.$sepSt.$an;
 return $date;
}

function reverse_date($date,$separateur,$separateur_new)
{
   $ch_date = explode("$separateur",$date);
   $date = $ch_date[2]."$separateur_new".$ch_date[1]."$separateur_new".$ch_date[0];
   return $date;
}

function scorm_add_time($a, $b)
{

    $aes = explode(':',$a);
    $bes = explode(':',$b);
    $aseconds = explode('.',$aes[2]);
    $bseconds = explode('.',$bes[2]);
    $change = 0;

    $acents = 0;  //Cents
    if (count($aseconds) > 1)
        $acents = $aseconds[1];
    $bcents = 0;
    if (count($bseconds) > 1)
        $bcents = $bseconds[1];
    $cents = $acents + $bcents;
    $change = floor($cents / 100);
    $cents = $cents - ($change * 100);
    if (floor($cents) < 10)
        $cents = '0'. $cents;

    $secs = $aseconds[0] + $bseconds[0] + $change;  //Seconds
    $change = floor($secs / 60);
    $secs = $secs - ($change * 60);
    if (floor($secs) < 10)
        $secs = '0'. $secs;

    $mins = $aes[1] + $bes[1] + $change;   //Minutes
    $change = floor($mins / 60);
    $mins = $mins - ($change * 60);
    if ($mins < 10)
        $mins = '0' .  $mins;

    $hours = $aes[0] + $bes[0] + $change;  //Hours
    if ($hours < 10)
        $hours = '000' . $hours;
    elseif ($hours < 100 && $hours > 9)
        $hours = '00' . $hours;
    elseif ($hours < 1000 && $hours > 99)
        $hours = '0' . $hours;
    $temps = $hours . ":" . $mins . ":" . $secs . '.' . $cents;
    return $temps;
}
function scorm_modifie_time($temps)
{
    $cents = '';
    $liste = explode(':',$temps);
    $seconds = explode('.',$liste[2]);
    if ($seconds[1] < 10 && isset($seconds[1]))
       $cents = '0'.$seconds[1];
    elseif (strlen($cents) == 1 && $seconds[1] > 10)
       $cents = $seconds[1]."0";
    elseif (strlen($cents) == 1 && $seconds[1] == 0)
       $cents = $seconds[1]."0";
    elseif (!isset($seconds[1]))
       $cents = "00";
    else
       $cents = $seconds[1];
    if (strlen($seconds[0]) == 1)
       $secs = '0'.$seconds[0];
    else
       $secs = $seconds[0];
    if ($liste[1] > 60)
       $mins = '0'.$liste[1];
    else
       $mins = $liste[1];
    $hours = $liste[0];
    if ($hours < 10 && strlen($hours) == 1)
        $hours = '000' . $hours;
    elseif ($hours < 10 && strlen($hours) == 2)
        $hours = '00' . $hours;
    elseif ($hours < 10 && strlen($hours) == 3)
        $hours = '0' . $hours;
    elseif ($hours < 100 && $hours > 9 && strlen($hours) == 3)
        $hours = '0' . $hours;
    $temps = $hours . ":" . $mins . ":" . $secs . '.' . $cents;
    return $temps;
}
function PTHMS($temps)
{
  $heure = '';
   if (strstr($temps,'H'))
    $Tab1 = explode('H',$temps);
   if (strstr($temps,'M'))
   {
    $Tab2 = explode('M',$Tab1[1]);
      $heure = ($Tab2[0] > 0) ? $Tab2[0].'h' : '';
   }
   else
      $heure = ($Tab2[1] > 0) ? $Tab1[1].'h' : '';
   if (isset($Tab2[1]) && strstr($temps,'S'))
   {
       $Tab3 = explode('S',$Tab2[1]);
       $heure .= ($Tab3[0] > 0) ? $Tab3[0].'mn' : '';
       if (!empty($Tab3[1]))
          $heure .= $Tab3[1].'sec';
   }
   elseif (isset($Tab2[1]) && !strstr($temps,'S'))
       $heure .= ($Tab2[1] > 0) ? $Tab2[1].'mn' : '';
   return $heure;
}

function agrege_time($temps)
{
    if (strstr($temps,'.'))
    {
       if (strstr($temps,'.'))
         $temps = substr($temps,0,-3);
       $liste = explode(':',$temps);
       $sec = ($liste[2] != '00' && intval($liste[2]) > 29) ? 1 : 0;
       $temp = intval($liste[1])+intval($liste[0])*60+$sec;
    }
    else
      $temp = 0;
    return $temp;
}

function aicc_modifie_time($temps)
{
    if (!strstr($temps,'.'))
       $temps .= ".00";
    scorm_modifie_time($temps);
    return $temps;
}

function remplace_text($text)
{
    $text = str_replace('√©','È',$text);
    $text = str_replace('‚Äô','\'',$text);
    $text = str_replace('¬Æ','Æ',$text);
    //$text = str_replace('\\\r','',$text);
    return $text;
}

function IsIPv6($ip)
{
   if(!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6))
       return FALSE;
   return true;
}

function mail_attachement($sendto , $subject , $msg , $userfile ,$reply, $nom ,$from)
{
   GLOBAL $s_exp;
   if ($s_exp == "lx")
      $rn="\n";
   else
      $rn="\r\n";
   $limite = "_parties_".md5(uniqid (rand()));
   $mail_mime = "Date: ".date("l j F Y, G:i")."$rn";
   $mail_mime .= "MIME-Version: 1.0$rn";
   $mail_mime .= "Content-Type: multipart/mixed;$rn";
   $mail_mime .= " boundary=\"----=$limite\"$rn$rn";

   //Le message en texte simple pour les navigateurs qui n'acceptent pas le HTML
   $texte = "This is a multi-part message in MIME format.$rn";
   $texte .= "Ceci est un message au format MIME.$rn";
   $texte .= "------=$limite$rn";
   $texte .= "Content-Type: text/html; charset=\"iso-8859-1\"$rn";
   $texte .= "Content-Transfer-Encoding: 7bit$rn$rn";
   $texte .= $msg;
   $texte .= "$rn$rn";
   //le fichier
   if ($userfile != "none")
   {
      $attachement = "------=$limite$rn";
      $attachement .= "Content-Type: application/octet-stream; name=\"$nom\"$rn";
      $attachement .= "Content-Transfer-Encoding:base64$rn";
      $attachement .= "Content-Disposition: attachment;filename=\"$nom\"$rn$rn";
      $fp = fopen($userfile, "rb");
      $buff = fread($fp, filesize($userfile));
      fclose($fp);
      $attachement .= chunk_split(base64_encode($buff));
      $attachement .= "$rn$rn$rn------=".$limite."$rn";
   }
   else
      $attachement = "";
   return mail($sendto, $subject, $texte.$attachement, "Reply-to: ".$reply.$rn."From:".$from.$rn.$mail_mime, "-f $from");
}

function ResizeImage($im,$maxwidth,$maxheight,$name)
{
        $width = imagesx($im);
        $height = imagesy($im);
        if(($maxwidth && $width > $maxwidth) || ($maxheight && $height > $maxheight))
        {
                if($maxwidth && $width > $maxwidth)
                {
                        $widthratio = $maxwidth/$width;
                        $RESIZEWIDTH=true;
                }
                if($maxheight && $height > $maxheight)
                {
                        $heightratio = $maxheight/$height;
                        $RESIZEHEIGHT=true;
                }
                if($RESIZEWIDTH && $RESIZEHEIGHT)
                {
                        if($widthratio < $heightratio)
                        {
                                $ratio = $widthratio;
                        }
                        else
                        {
                                $ratio = $heightratio;
                        }
                }
                elseif($RESIZEWIDTH)
                {
                        $ratio = $widthratio;
                }
                elseif($RESIZEHEIGHT)
                {
                        $ratio = $heightratio;
                }
                $newwidth = $width * $ratio;
                $newheight = $height * $ratio;
                if(function_exists("imagecopyresampled"))
                {
                      $newim = imagecreatetruecolor($newwidth, $newheight);
                      imagecopyresampled($newim, $im, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
                }
                else
                {
                        $newim = imagecreate($newwidth, $newheight);
                      imagecopyresized($newim, $im, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
                }
                ImageJpeg ($newim,$name . ".jpg");
                ImageDestroy ($newim);
        }
        else
           ImageJpeg ($im,$name . ".jpg");
}
?>