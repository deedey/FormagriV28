<?php
if (!isset($_SESSION)) session_start();
if (!isset($_SESSION['id_user']) || $_SESSION['id_user'] == "")
{
  exit();
}
include ("include/UrlParam2PhpVar.inc.php");
require 'fonction.inc.php';
require 'graphique/admin.inc.php';
require "lang$lg.inc.php";
require "fonction_html.inc.php";
require "class/class_formation.php";
require "langues/formation.inc.php";
dbConnect();
$date_op = date("Y-m-d H:i:s" ,time());
$heure_fiche = substr($date_op,11);
$date_fiche = substr($date_op,0,10);
$fichier = '';
$cpt = 0;
//dey Dfoad
if (isset($export_grp) && $export_grp == 1 && !empty($groupe))
{
   $req_appgrp =  mysql_query ("select utilgr_utilisateur_no from utilisateur_groupe where utilgr_groupe_no=$groupe order by utilgr_utilisateur_no");
   $nb_appgrp = mysql_num_rows($req_appgrp);
   if ($nb_appgrp > 0)
   {
      $i=0;
      while ($i < $nb_appgrp)
      {
          $num = mysql_result($req_appgrp,$i,"utilgr_utilisateur_no");
          $req_util = mysql_query ("select * from utilisateur where util_cdn = $num");
          while ($item = mysql_fetch_object($req_util))
          {
               $fichier .= $item->util_nom_lb.";".
                           $item->util_prenom_lb.";".
                           $item->util_email_lb.";".
                           $item->util_tel_lb.";".
                           $item->util_urlmail_lb.";".
                           $item->util_typutil_lb.";".
                           $item->util_login_lb.";".
                           $item->util_motpasse_lb.";".
                           $item->util_logincas_lb;
          }
          $fichier .="\n";
          $cpt++;
          $i++;
      }
   }
   $req_formgrp =  mysql_query ("select grp_resp_no,grp_tuteur_no from groupe where grp_cdn=$groupe");
   $resp = mysql_result($req_formgrp,0,"grp_resp_no");
   $req_util = mysql_query ("select * from utilisateur where util_cdn = $resp");
   if (mysql_num_rows($req_util) > 0)
   {
      while ($item = mysql_fetch_object($req_util))
      {
         $fichier .= $item->util_nom_lb.";".
                     $item->util_prenom_lb.";".
                     $item->util_email_lb.";".
                     $item->util_tel_lb.";".
                     $item->util_urlmail_lb.";".
                     $item->util_typutil_lb.";".
                     $item->util_login_lb.";".
                     $item->util_motpasse_lb.";".
                     $item->util_logincas_lb."\n";
                     $cpt++;
      }
   }
   $tut = mysql_result($req_formgrp,0,"grp_tuteur_no");
   if ($tut > 0 && $tut != $resp)
   {
      $req_util = mysql_query ("select * from utilisateur where util_cdn = $tut");
      if (mysql_num_rows($req_util) > 0)
      {
         while ($item = mysql_fetch_object($req_util))
         {
            $fichier .= $item->util_nom_lb.";".
                     $item->util_prenom_lb.";".
                     $item->util_email_lb.";".
                     $item->util_tel_lb.";".
                     $item->util_urlmail_lb.";".
                     $item->util_typutil_lb.";".
                     $item->util_login_lb.";".
                     $item->util_motpasse_lb.";".
                     $item->util_logincas_lb."\n";
         }
         $cpt++;
      }
   }
   //
   $req_formgrp =  mysql_query ("select distinct gp_formateur_no from groupe_parcours where gp_grp_no='$groupe' and NOT gp_formateur_no = $resp and  NOT gp_formateur_no = $tut");
   $n_form = mysql_num_rows($req_formgrp);
   if ($n_form > 0)
   {
     $i=0;
     while ($i < $n_form)
     {
        $form = mysql_result($req_formgrp,$i,"gp_formateur_no");
        $req_util = mysql_query ("select * from utilisateur where util_cdn = '$form'");
        while ($item = mysql_fetch_object($req_util))
        {
           $fichier .= $item->util_nom_lb.";".
                     $item->util_prenom_lb.";".
                     $item->util_email_lb.";".
                     $item->util_tel_lb.";".
                     $item->util_urlmail_lb.";".
                     $item->util_typutil_lb.";".
                     $item->util_login_lb.";".
                     $item->util_motpasse_lb.";".
                     $item->util_logincas_lb;
        }
       $cpt++;
       $i++;
     }
     $fichier .="\n";
   }

   if ($cpt > 0)
   {
      $dir_file="ressources/".$login."_".$id_user."/ressources/export_groupe.txt";
      $fp = fopen($dir_file, "w+");
         $fw = fwrite($fp, trim($fichier));
      fclose($fp);
      chmod($dir_file,0775);
      ForceFileDownload($dir_file,'ascii');
   }
}
// fin export de liste d'utilisateurs dans une formation
if (isset($modif_resp) && $modif_resp == 1)
{
     $id_email = GetDataField ($connect,"SELECT util_email_lb  from utilisateur where util_cdn = $id_resp","util_email_lb");
     $id_pass = GetDataField ($connect,"SELECT util_motpasse_lb  from utilisateur where util_cdn = $id_resp","util_motpasse_lb");
     $nom_grp = GetDataField ($connect,"select grp_nom_lb from groupe where grp_cdn  = $id_grp","grp_nom_lb");
     $id_forum = GetDataField ($connect,"select id from forums where name='$nom_grp'","id");
     $sql= mysql_query("update groupe set grp_resp_no = $id_resp where grp_cdn = $id_grp");
     $sql= mysql_query("update forums set mod_email='$id_email',mod_pass='$id_pass' where id = $id_forum");
     echo stripslashes($mess_notif);
     exit();
}
if (isset($modif_autres) && $modif_autres == 1)
{
     $sql= mysql_query("update groupe set grp_resp_no = $id_resp,grp_tuteur_no='$id_tut',grp_publique_on='$publique',grp_flag_on='$accession',grp_classe_on='$id_classe',grp_datemodif_dt=\"$date_op\" where grp_cdn = $id_grp");
     echo stripslashes($mess_notif);
     exit();
}
if (isset($creation_groupe) && $creation_groupe == 1 && isset($creer_groupe) && $creer_groupe == 1)
{
  if (isset($modif_grp) && $modif_grp == 1)
  {
    if ($nouveau == "" || $obj_grp == "" || $desc_grp == "")
    {
      include ("style.inc.php");
      entete_simple($mess_menu_creer_grp);
      echo "<TR><TD align='middle'>";
      echo "<FONT size='2'>$msq_oubli_champ_oblig</FONT><P>";
      $html = '';
      echo fin_tableau($html);
      exit();
    }
    $nouveau = str_replace("/","-",$nouveau);
    $nouveau = str_replace('&',$et,$nouveau);
    $nouveau = str_replace(':',"-",$nouveau);
    $nouveau = str_replace('?',"-",$nouveau);
    $nouveau = str_replace('@',"-",$nouveau);
    $nouveau = str_replace('\"',"-",$nouveau);
    $nouveau = str_replace('\'',"-",$nouveau);
    $sql = mysql_query("update groupe set grp_nom_lb = \"$nouveau\",grp_commentaire_cmt =\"".htmlentities($comment_grp,ENT_QUOTES,'iso-8859-1').
                       "\",grp_formobject_lb = \"".htmlentities($obj_grp,ENT_QUOTES,'iso-8859-1')."\",grp_formdesc_cmt = \"".
                       htmlentities($desc_grp,ENT_QUOTES,'iso-8859-1')."\",grp_tuteur_no='$id_tut',grp_publique_on='$publique',".
                       "grp_classe_on='$classe',grp_flag_on='$accession',grp_datemodif_dt=\"$date_op\" where grp_cdn = $numero");
    $nom_table = "groupe".$numero;
    $dupli = mysql_query("update forums set name = \"$nouveau\",table_name = '$nom_table',allow_uploads='$OkUpld' where id = $num_forum");
    $message = " $mess_modif_carac_grp $nouveau ";
    $fichier = '';
    $dir= "/forum/admin/forums/$num_forum.php";
    $fl = fopen($repertoire.$dir,"r");
    while (!feof($fl))
    {
      $ligne = fgets($fl, 4096);
      if (strstr($ligne, "ForumAllowUploads"))
      {
        $ligne = "   \$ForumAllowUploads = '$OkUpld';\n";
      }
      $fichier .= $ligne;
    }
    fclose ($fl);
    $fl = fopen($repertoire.$dir,"w");
    fputs ($fl,$fichier);
    fclose ($fl);
    $dir_num="ressources";
    $handle=opendir($dir_num);
     $drap=0;
     while ($file = readdir($handle))
     {
      if ($file == "forums")
      {
         chmod ($dir_num."/forums",0777);
         $drap=1;
          break;
      }
     }
     closedir($handle) ;
     if ($drap == 0)
     {
      mkdir ($dir_num."/forums",0777);
      chmod ($dir_num."/forums",0777);
     }
     $dir_num="ressources/forums";
     $handle=opendir($dir_num);
     $drap=0;
     while ($file = readdir($handle))
     {
      if ($file == "$nom_table")
      {
         chmod ($dir_num."/$nom_table",0777);
         $drap=1;
          break;
      }
     }
     closedir($handle) ;
     if ($drap == 0)
     {
      mkdir ($dir_num."/$nom_table",0777);
      chmod ($dir_num."/$nom_table",0777);
     }
  }
  elseif (isset($supp_grp) && $supp_grp == 1)
  {
    $nom_table = "groupe".$numero;
    $dir=$repertoire."/ressources/groupes/$numero";
    viredir($dir,$s_exp);
    $dir=$repertoire."/ressources/forums/$nom_table";
    viredir($dir,$s_exp);
    $nom_grp_supp = GetDataField ($connect,"SELECT grp_nom_lb from groupe where grp_cdn = $numero","grp_nom_lb");
    $message = " $nom_grp_supp : $mess_supp_grp_fin ";
    $efface_grp = mysql_query("DELETE from groupe where grp_cdn = $numero");
    $efface_rdv_grp = mysql_query("DELETE from rendez_vous where rdv_grp_no = $numero");
    $efface_grp_prescription = mysql_query("drop table IF EXISTS prescription_$numero");
    $efface_grp_suivi1 = mysql_query("drop table IF EXISTS suivi1_$numero");
    $efface_grp_suivi2 = mysql_query("drop table IF EXISTS suivi2_$numero");
    $efface_grp_suivi3 = mysql_query("drop table IF EXISTS suivi3_$numero");
    $efface_grp_scorUtil = mysql_query("drop table IF EXISTS scorm_util_module_$numero");
    $efface_grp_parc = mysql_query("DELETE from groupe_parcours where gp_grp_no = $numero");
    $nom_table = "groupe".$numero;
    $efface_grp_forum = mysql_query("drop table $nom_table");
    $forums_supp = $nom_table."_bodies";
    $efface_grp_forum = mysql_query("drop table $forums_supp");
    $id_forum = GetDataField ($connect,"SELECT id from forums where table_name = \"$nom_table\"","id");
    $efface_forums = mysql_query("DELETE from forums where id=$id_forum");
    $req_compter_forums = mysql_query("SELECT count(*) from forums");
    $nb_forums = mysql_result($req_compter_forums,0);
    $fichier='';
    $dir="/forum/config/forums.php";
    $fl = fopen($repertoire.$dir,"r");
    while (!feof($fl))
    {
      $ligne = fgets($fl, 4096);
      if (strstr($ligne, "ActiveForums"))
      {
        $ligne = "   \$ActiveForums = \"$nb_forums\";\n";
      }
      if (strstr($ligne, "ForumDisplay"))
      {
        $ligne = "   \$ForumDisplay = '';\n";
      }
      if (strstr($ligne, "ForumAllowHTML"))
      {
        $ligne = "   \$ForumAllowHTML = 'Y';\n";
      }
      if (strstr($ligne, "ForumAllowUploads"))
      {
        $ligne = "   \$ForumAllowUploads = '$OkUpld';\n";
      }
      $fichier .= $ligne;
    }
    fclose ($fl);
    $fl = fopen($repertoire.$dir,"w");
    fputs ($fl,$fichier);
    fclose ($fl);
    // Création du fichier n.php
    $fic=$repertoire."/forum/admin/forums/".$id_forum.".php";
    unlink($fic);
    // fin de la routine de suppression du forum et du groupe
    $lien = "gestion_groupe.php?cms=1&ordre_affiche=nom_groupe&message=$message";
    $lien = urlencode($lien);
    print("<SCRIPT language=javascript>");
       print("parent.logo.location.reload()");
    print("</SCRIPT>");
    echo "<script language=\"JavaScript\">";
      echo "document.location.replace(\"trace.php?link=$lien\");";
    echo "</script>";
    exit();
  }
  else
  {
    if ($nouveau == "" || $obj_grp == "" || $desc_grp == "")
    {
      include ("style.inc.php");
      entete_simple($mess_menu_creer_grp);
      echo "<TR><TD align='middle'>";
      echo "<FONT size='2'>$msq_oubli_champ_oblig</FONT><P>";
      $html = '';
      echo fin_tableau($html);
      exit;
    }
    if ( strstr("$nouveau","'"))
    {
       include "style.inc.php";
      entete_simple($mess_menu_creer_grp);
      echo "<TR><TD align='middle'>";
      echo "<CENTER><FONT size='2'>$msq_guillemets \"$nouveau\"</FONT><P>";
      $html = '';
      echo fin_tableau($html);
      exit;
    }
    $nouveau = str_replace("/","-",$nouveau);
    $nouveau = str_replace('&',$et,$nouveau);
    $nouveau = str_replace(':',"-",$nouveau);
    $nouveau = str_replace('?',"-",$nouveau);
    $nouveau = str_replace('@',"-",$nouveau);
    $nouveau = str_replace('\"',"-",$nouveau);
    $nouveau = str_replace('\'',"-",$nouveau);
    $message = " $nouveau : $grp_new_forum ";
    $numero = Donne_ID ($connect,"SELECT max(grp_cdn) from groupe");
    $sql= mysql_query("INSERT INTO groupe VALUES ('$numero',\"$nouveau\",\"".htmlentities($comment_grp,ENT_QUOTES,'iso-8859-1').
                      "\",\"".htmlentities($obj_grp,ENT_QUOTES,'iso-8859-1')."\",\"".htmlentities($desc_grp,ENT_QUOTES,'iso-8859-1').
                      "\",'$id_user',$publique,$id_tut,$classe,$accession,\"$date_op\",\"$date_op\")");

    $dir="ressources/groupes";
    $nouveau_rep = $numero;
    mkdir($dir.'/'.$nouveau_rep,0775);
    $nom_table = "groupe".$numero;
  //routine de création de forum pour le nouveau groupe
    $id_email = GetDataField ($connect,"SELECT util_email_lb  from utilisateur where util_cdn = $id_user","util_email_lb");
    $id_pass = GetDataField ($connect,"SELECT util_motpasse_lb  from utilisateur where util_cdn = $id_user","util_motpasse_lb");
    $id_max = Donne_ID ($connect,"SELECT max(id) from forums");
    $dupli=mysql_query("INSERT INTO forums (id, name, active, description, config_suffix, folder,".
                       " parent, display, table_name, moderation, mod_email, mod_pass, email_list, ".
                       "email_return, email_tag, check_dup, multi_level, collapse, flat, staff_host, ".
                       "lang, html, table_width, table_header_color, table_header_font_color, ".
                       "table_body_color_1, table_body_color_2, table_body_font_color_1, table_body_font_color_2, ".
                       "nav_color, nav_font_color, allow_uploads) VALUES (".
                       "$id_max,\"$nouveau\", '1', 'Groupe: ', '', '0', '0', '1000', '$nom_table', 'n', '$id_email', '$ForumModPass', '',".
                       " '', '', '0', '1', '1', '0', '', 'lang/french.php', 'N', '540', ".
                       "'#000080', '#FFFFFF', '#FFFFFF', '#FFFFEA', '#000000', '#000000', '#FFFFEA', '#000000', '$OkUpld')");
    $dupli= mysql_query("CREATE TABLE $nom_table (
    id bigint(20) unsigned NOT NULL default '0',
    datestamp datetime NOT NULL default '0000-00-00 00:00:00',
    thread int(11) NOT NULL default '0',
    parent int(11) NOT NULL default '0',
    author char(37) NOT NULL default '',
    subject char(255) NOT NULL default '',
    email char(200) NOT NULL default '',
    attachment char(64) NOT NULL default '',
    host char(50) NOT NULL default '',
    email_reply char(1) NOT NULL default 'N',
    approved char(1) NOT NULL default 'N',
    msgid char(100) NOT NULL default '',
    PRIMARY KEY (id),
    KEY author(author),
    KEY datestamp(datestamp),
    KEY subject(subject),
    KEY thread(thread),
    KEY parent(parent),
    KEY approved(approved),
    KEY msgid(msgid)
    ) ENGINE=MyISAM");
    $compl_nom_table= $nom_table."_bodies";
    $dupli = mysql_query("CREATE TABLE $compl_nom_table (
    id bigint(20) unsigned NOT NULL auto_increment,
    body text NOT NULL,
    thread int(11) NOT NULL default '0',
    PRIMARY KEY (id),
    KEY thread(thread)
    ) ENGINE=MyISAM");
  }
  $nb_for = mysql_query("SELECT count(*) from forums");
  $nb_forums = mysql_result($nb_for,0);
  if (isset($modif_grp) && $modif_grp == 1)
  {
      $id_max = $num_forum;
  }
  $fichier = '';
  $dir="/forum/config/forums.php";
  $fl = fopen($repertoire.$dir,"r");
  while (!feof($fl))
  {
      $ligne = fgets($fl, 4096);
      if (strstr($ligne, "ActiveForums"))
      {
        $ligne = "   \$ActiveForums = \"$nb_forums\";\n";
      }
      if (strstr($ligne, "ForumDisplay"))
      {
        $ligne = "   \$ForumDisplay = '';\n";
      }
      if (strstr($ligne, "ForumAllowHTML"))
      {
        $ligne = "   \$ForumAllowHTML = 'Y';\n";
      }
      if (strstr($ligne, "ForumAllowUploads"))
      {
        $ligne = "   \$ForumAllowUploads = '$OkUpld';\n";
      }
      if (strstr($ligne, "uploadDir"))
      {
        $ligne = "  \$uploadDir= str_replace('//','/',\$repertoire.\"/ressources/forums\");\n";
      }
      if (strstr($ligne, "uploadUrl"))
      {
        $ligne = "  \$uploadUrl=\$adresse_http.\"/ressources/forums\";\n";
      }
      $fichier .= $ligne;
  }
  fclose ($fl);
  $fl = fopen($repertoire.$dir,"w");
  fputs ($fl,$fichier);
  fclose ($fl);
  // Création du fichier n.php
  $fichier = "";
  $dir="/forum/admin/forums/1.php";
  $fl = fopen($repertoire.$dir,"r");
  while (!feof($fl))
  {
      $ligne = fgets($fl, 4096);
      if (strstr($ligne, "ForumActive"))
      {
        $ligne = "  \$ForumActive = \"$id_max\";\n";
      }
      if (strstr($ligne, "ForumName"))
      {
        $ligne = "  \$ForumName = \"$nouveau\";\n";
      }
      if (strstr($ligne, "ForumDescription"))
      {
        $ligne = "  \$ForumDescription = \"$mpr_grpmin: \";\n";
      }
      if (strstr($ligne, "ForumTableName"))
      {
        $ligne = "  \$ForumTableName = \"$nom_table\";\n";
      }
      if (strstr($ligne, "ForumDisplay"))
      {
        $ligne = "  \$ForumDisplay = '1000';\n";
      }
      if (strstr($ligne, "ForumTableBodyColor2"))
      {
        $ligne = "  \$ForumTableBodyColor = '#F4F4F4';\n";
      }
      if (strstr($ligne, "ForumTableBodyColor2"))
      {
        $ligne = "  \$ForumTableBodyColor = '#F4F4F4';\n";
      }
      if (strstr($ligne, "ForumAllowHTML"))
      {
        $ligne = "  \$ForumAllowHTML = 'Y';\n";
      }
      if (strstr($ligne, "ForumAllowUploads"))
      {
        $ligne = "  \$ForumAllowUploads = '$OkUpld';\n";
      }
      $fichier .= $ligne;
  }
  fclose ($fl);
  $dir="/forum/admin/forums/".$id_max.".php";
  $fl = fopen($repertoire.$dir,"w");
  fputs ($fl,$fichier);
  fclose ($fl);
  chmod($repertoire.$dir,0777);
  $dir_num="ressources";
  $handle=opendir($dir_num);
  $drap=0;
  while ($file = readdir($handle))
  {
      if ($file == "forums")
      {
         chmod ($dir_num."/forums",0777);
         $drap=1;
          break;
      }
  }
  closedir($handle) ;
  if ($drap == 0)
  {
      mkdir ($dir_num."/forums",0777);
      chmod ($dir_num."/forums",0777);
  }
  $dir_num="ressources/forums";
  $handle=opendir($dir_num);
  $drap=0;
  while ($file = readdir($handle))
  {
      if ($file == "$nom_table")
      {
         chmod ($dir_num."/$nom_table",0777);
         $drap=1;
          break;
      }
  }
  closedir($handle) ;
  if ($drap == 0)
  {
      mkdir ($dir_num."/$nom_table",0777);
      chmod ($dir_num."/$nom_table",0777);
  }
  // fin de la routine de création d'un forum pour le nouveau groupe

  //--------------- création des tables prescription_num suivi1_num, suivi2_num et suivi3_num--------- pour le nouveau groupe

$dupli= mysql_query("CREATE TABLE IF NOT EXISTS prescription_$numero (
  presc_cdn smallint(5) unsigned NOT NULL auto_increment,
  presc_seq_no smallint(5) unsigned NOT NULL default '0',
  presc_parc_no smallint(5) NOT NULL default '0',
  presc_utilisateur_no smallint(5) unsigned NOT NULL default '0',
  presc_datedeb_dt date default NULL,
  presc_datefin_dt date default NULL,
  presc_prescripteur_no smallint(5) unsigned NOT NULL default '0',
  presc_formateur_no smallint(5) unsigned NOT NULL default '0',
  presc_grp_no smallint(3) NOT NULL default '0',
  presc_ordre_no smallint(2) NOT NULL default '1',
  PRIMARY KEY  (presc_cdn)) ENGINE=MyISAM ") ;
$dupli= mysql_query("CREATE TABLE IF NOT EXISTS suivi1_$numero (
  suivi_cdn int(6) unsigned NOT NULL auto_increment,
  suivi_utilisateur_no smallint(5) unsigned NOT NULL default '0',
  suivi_act_no smallint(5) unsigned NOT NULL default '0',
  suivi_seqajout_no smallint(5) NOT NULL default '0',
  suivi_etat_lb enum('PRESENTIEL','A FAIRE','EN COURS','ATTENTE','TERMINE')  NOT NULL default 'A FAIRE',
  suivi_fichier_lb text ,
  suivi_note_nb1 varchar(12)  default NULL,
  suivi_commentaire_cmt longtext,
  suivi_date_debut_dt datetime NOT NULL default '0000-00-00 00:00:00',
  suivi_date_fin_dt datetime NOT NULL default '0000-00-00 00:00:00',
  suivi_grp_no smallint(4) NOT NULL default '0',
  PRIMARY KEY  (suivi_cdn) ) ENGINE=MyISAM");
$dupli= mysql_query("CREATE TABLE IF NOT EXISTS suivi2_$numero (
  suiv2_cdn smallint(5) unsigned NOT NULL auto_increment,
  suiv2_utilisateur_no smallint(5) unsigned NOT NULL default '0',
  suiv2_seq_no smallint(5) unsigned NOT NULL default '0',
  suiv2_etat_lb enum('A FAIRE','EN COURS','ATTENTE','TERMINE') default 'A FAIRE',
  suiv2_duree_nb smallint(3) unsigned NOT NULL default '0',
  suiv2_ordre_no smallint(2) NOT NULL default '1',
  suiv2_grp_no smallint(4) NOT NULL default '0',
  PRIMARY KEY  (suiv2_cdn))  ENGINE=MyISAM");
$dupli= mysql_query("CREATE TABLE IF NOT EXISTS suivi3_$numero (
  suiv3_cdn smallint(5) unsigned NOT NULL auto_increment,
  suiv3_utilisateur_no smallint(5) unsigned NOT NULL default '0',
  suiv3_parc_no smallint(5) unsigned NOT NULL default '0',
  suiv3_etat_lb enum('A FAIRE','EN COURS','ATTENTE','TERMINE') default 'A FAIRE',
  suiv3_duree_nb smallint(3) unsigned NOT NULL default '0',
  suiv3_grp_no smallint(4) NOT NULL default '0',
  PRIMARY KEY  (suiv3_cdn))  ENGINE=MyISAM");
$dupli = mysql_query("CREATE TABLE IF NOT EXISTS `scorm_util_module_$numero` (
  `user_module_cdn` int(10) NOT NULL auto_increment,
  `user_module_no` int(8) NOT NULL default '0',
  `mod_module_no` int(8) NOT NULL default '0',
  `mod_grp_no` smallint(4) NOT NULL default '0',
  `lesson_location` varchar(255) NOT NULL default '',
  `lesson_mode` enum('BROWSE','NORMAL','REVIEW') NOT NULL default 'NORMAL',
  `lesson_status` enum('NOT ATTEMPTED','PASSED','FAILED','COMPLETED','BROWSED','INCOMPLETE','UNKNOWN') NOT NULL default 'NOT ATTEMPTED',
  `entry` enum('AB-INITIO','RESUME','') NOT NULL default 'AB-INITIO',
  `raw` tinyint(4) NOT NULL default '-1',
  `scoreMin` tinyint(4) NOT NULL default '-1',
  `scoreMax` tinyint(4) NOT NULL default '-1',
  `total_time` varchar(13) NOT NULL default '0000:00:00.00',
  `session_time` varchar(13) NOT NULL default '0000:00:00.00',
  `suspend_data` text NOT NULL,
  `comments` text NOT NULL,
  `comments_from_lms` text NOT NULL,
  `credit` enum('CREDIT','NO-CREDIT') NOT NULL default 'NO-CREDIT',
  `exit` varchar(8) NOT NULL default '',
  `last_acces` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`user_module_cdn`))  ENGINE=MyISAM");
 //---------------------- fin création des tables de prescription-------------------------pour le nouveau groupe
  $lien = "gestion_groupe.php?cms=1&ordre_affiche=nom_groupe&message=$message";
  $lien = urlencode($lien);
    print("<SCRIPT language=javascript>");
      print("parent.logo.location.reload()");
    print("</SCRIPT>");
    echo "<script language=\"JavaScript\">";
      echo "document.location.replace(\"trace.php?link=$lien\");";
    echo "</script>";
  exit();
}

// Création d'un nouveau groupe
if (isset($creation_groupe) && $creation_groupe == 1 && (!isset($creer_groupe) || (isset($creer_groupe) && $creer_groupe !=1)))
{
  include ("style.inc.php");
  if (!isset($modif_grp))
  {
       entete_simple($mess_creer_format);
       echo "<TR><TD style='text-align:left;' colspan='2'>";
       echo "<TABLE cellspacing='1' cellpadding='8' width='100%' border='0'>";
       echo "<CENTER><FORM NAME='MForm' action=\"gestion_groupe.php\" method='post'>";
       echo "<input type=hidden name='creation_groupe' value='1' />".
            " <input type=hidden name='creer_groupe' value='1' />";
       echo"<TR><TD>".nbsp(8)."<B>$mpr_aj_newgrp</B></TD>";
       echo"<TD style='text-align:left;' colspan='2' valign='top'><INPUT TYPE=TEXT class='INPUT' NAME=nouveau SIZE='40'>";
       echo anoter($mess_avert_titre,"");
       echo "</TD></TR>";
       echo"<TR><TD style='text-align:left;' nowrap valign='top'>".nbsp(8)."<B>$mess_gp_obj_form</B></TD>";
       echo"<TD style='text-align:left;' colspan='2'><TEXTAREA NAME='obj_grp' class='TXTAREA' rows='4' cols='80'></TEXTAREA></TD></TR>";
       echo"<TR><TD style='text-align:left;' valign='top'>".nbsp(8)."<B>$mess_gp_desc_form</B></TD>";
       echo"<TD style='text-align:left;' colspan='2'><TEXTAREA NAME='desc_grp'  class='TXTAREA' rows='4' cols='80'></TEXTAREA>";
       echo"<TR><TD style='text-align:left;' valign='top'>".nbsp(8)."<B>$mess_admin_comment</B></TD>";
       echo"<TD style='text-align:left;' colspan='2'><TEXTAREA NAME='comment_grp' class='TXTAREA' rows='4' cols='80'></TEXTAREA></TD></TR>";
       echo "<TR><TD colspan='3'><TABLE cellpadding=4 cellspacing='20' border='0'>";
       echo "<tr><td><B>$mess_superviseur</B><br />";
       Ascenseur_mult ("id_tut","select util_cdn,util_prenom_lb,util_nom_lb from utilisateur where util_typutil_lb != 'APPRENANT' AND util_blocageutilisateur_on ='NON' AND util_flag = 0 ORDER BY util_nom_lb ASC",$connect,$param);$param = "";
       echo "</TD>";
       echo "<TD nowrap><B>$mess_classe</B><br />";
       echo "<SELECT  name='classe' class='SELECT' size=1>";
       echo "<OPTION value='1' selected>$mess_oui</OPTION>";
       echo "<OPTION value='0'>$mess_non</OPTION>";
       echo "</SELECT>";
       echo anoter($mess_classe_a,"");
       echo "</TD>";
       echo "<TD nowrap><B>$mess_presc_mut</B><br />";
       echo "<SELECT  name='publique' class='SELECT' size=1>";
       echo "<OPTION value='1' selected>$mess_oui</OPTION>";
       echo "<OPTION value='0'>$mess_non</OPTION>";
       echo "</SELECT></TD>";
       echo "<TD nowrap><B>$mess_presc_acces</B><br />";
       echo "<SELECT class='SELECT' name='accession' size=1>";
       echo "<OPTION value='1' selected>$mess_oui</OPTION>";
       echo "<OPTION value='0'>$mess_non</OPTION>";
       echo "</SELECT></TD>";
       echo "<TD nowrap><B>Forum avec fichier_joint</B><br />";
       echo "<SELECT class='SELECT' name='OkUpld' size=1>";
       echo "<OPTION value='Y' selected>$mess_oui</OPTION>";
       echo "<OPTION value='N'>$mess_non</OPTION>";
       echo "</SELECT></TD>";
       echo "</tr></table></TD></TR>";
       echo "<TR height='50'>";
       echo "<TD align=left valign='center'><A HREF=\"javascript:history.back();\"
            onmouseover=\"img_annonce.src='images/fiche_identite/boutretourb$suffixer.gif';return true;\"
            onmouseout=\"img_annonce.src='images/fiche_identite/boutretour$suffixer.gif'\">";
       echo "<IMG NAME=\"img_annonce\" SRC=\"images/fiche_identite/boutretour$suffixer.gif\" BORDER='0'
            onLoad=\"tempImg=new Image(0,0); tempImg.src='images/fiche_identite/boutretourb$suffixer.gif'\"></A></TD>";
       echo "<TD align='left' valign='center' colspan='2'><A HREF=\"javascript:document.MForm.submit();\"
            onClick='TinyMCE.prototype.triggerSave();'
             onmouseover=\"img1.src='images/fiche_identite/boutvalidb$suffixer.gif';return true;\"
             onmouseout=\"img1.src='images/fiche_identite/boutvalid$suffixer.gif'\">".
            "<IMG NAME=\"img1\" SRC=\"images/fiche_identite/boutvalid$suffixer.gif\" BORDER='0'
            onLoad=\"tempImg=new Image(0,0); tempImg.src='images/fiche_identite/boutvalidb$suffixer.gif'\"></A>";
      echo "</TD></TR></TABLE></TD></TR></TABLE></TD></TR></TABLE>";
  }
  else
  {
       $requete = requete("*","groupe","groupe.grp_cdn = $numero");
       while ($item =  mysql_fetch_object($requete))
       {
              $nom_grp = $item->grp_nom_lb;
              $num_forum = GetDataField ($connect,"SELECT id from forums where name = \"$nom_grp\"","id");
              $OkUpld = GetDataField ($connect,"SELECT allow_uploads from forums where name = \"$nom_grp\"","allow_uploads");
              $comment_grp = $item->grp_commentaire_cmt;
              $id_com = str_replace("'","\'",$comment_grp);
              $obj_grp = $item->grp_formobject_lb;
              $desc_grp = $item->grp_formdesc_cmt;
              $publique = $item->grp_publique_on;
              $crea_grp = $item->grp_resp_no;
              $id_tut = $item->grp_tuteur_no;
              $classe = $item->grp_classe_on;
              $accession = $item->grp_flag_on;
              $date_creat_grp = $item->grp_datecreation_dt;
              $date_modif_grp = $item->grp_datemodif_dt;
       }
       $heure_creat = substr($date_creat_grp,11);
       $date_creat = reverse_date(substr($date_creat_grp,0,10),"-","/");
       $heure_modif = substr($date_modif_grp,11);
       $date_modif = reverse_date(substr($date_modif_grp,0,10),"-","/");
       $letitre = "$mpr_gest_grp : ".strtolower($mess_menu_modif_qcm);
       entete_simple($letitre);
       echo "<TR><TD colspan='2'>";
       echo "<TABLE cellspacing='1' cellpadding='8' width='100%' border='0'>";
       echo "<CENTER><FORM NAME='MForm' action=\"gestion_groupe.php\" method='post'>";
       echo "<input type=hidden name='creation_groupe' value='1' />".
            " <input type=hidden name='creer_groupe' value='1' />";
       echo "<TR><TD style='text-align:left;'><B>$mpr_aj_newgrp</B></TD>";
       echo "<TD style='text-align:left;'><INPUT TYPE=TEXT class='INPUT' NAME=nouveau VALUE=\"$nom_grp\" SIZE=40></TD>";
       echo "<TD style='text-align:left;' width='50%' valign='center'>";
       echo anoter($mess_avert_titre,"");
       echo "</TD></TR>";
       echo "<TR><TD style='text-align:left;' nowrap valign='top'><B>$mess_gp_obj_form</B></TD>";
       echo "<TD style='text-align:left;' colspan='2'><TEXTAREA NAME='obj_grp' class='TXTAREA' rows='4' cols='80'>".html_entity_decode($obj_grp,ENT_QUOTES,'iso-8859-1')."</TEXTAREA></TD></TR>";
       echo "<TR><TD style='text-align:left;' valign='top'><B>$mess_gp_desc_form</B></TD>";
       echo "<TD style='text-align:left;' colspan='2'><TEXTAREA NAME='desc_grp' class='TXTAREA' rows='4' cols='80'>".html_entity_decode($desc_grp,ENT_QUOTES,'iso-8859-1')."</TEXTAREA>";
       echo "<TR><TD style='text-align:left;' valign='top'><B>$mess_admin_comment *</B></TD>";
       echo "<TD style='text-align:left;' colspan='2'><TEXTAREA NAME='comment_grp' class='TXTAREA' rows='4' cols='80'>".html_entity_decode($comment_grp,ENT_QUOTES,'iso-8859-1')."</TEXTAREA></TD></TR>";
       echo "<TR><TD valign='top' style='text-align:left;'><B>$mess_menu_creat_qcm</B></TD>".
            "<TD valign='top' style='text-align:left;'>$date_creat</TD></TR>";
       echo "<TR><TD valign='top' style='text-align:left;'><B>$mess_modif_dt</B></TD>".
            "<TD valign='top' style='text-align:left;'>$date_modif</TD></TR>";
       echo "<TR><TD colspan='3'><TABLE cellpadding=4 cellspacing='20' border=0>";
       echo "<tr><td><B>$mess_superviseur</B><br />";
       $param = $id_tut;
       Ascenseur_mult ("id_tut","select util_cdn,util_prenom_lb,util_nom_lb from utilisateur where util_typutil_lb != 'APPRENANT' AND util_blocageutilisateur_on ='NON' AND util_flag = 0 ORDER BY util_nom_lb ASC",$connect,$param);$param = "";
       echo "</TD>";
       $nbr_app = mysql_result(mysql_query("SELECT count(utilgr_groupe_no) from utilisateur_groupe where utilgr_groupe_no = $numero"),0);
       $nbr_presc = mysql_result(mysql_query("SELECT count(presc_grp_no) from prescription_$numero"),0);
       echo "<TD nowrap valign='bottom'><B>$mess_classe</B><br />";
       if ($nbr_app == 0 || ($nbr_presc == 0 && $nbr_app > 0))
       {
          echo "<SELECT  name='classe' class='SELECT' size=1>";
          if ($classe == 1)
          {
             echo "<OPTION value='1' selected>$mess_oui</OPTION>";
             echo "<OPTION value='0'>$mess_non</OPTION>";
          }
          else
          {
             echo "<OPTION value='0' selected>$mess_non</OPTION>";
             echo "<OPTION value='1'>$mess_oui</OPTION>";
          }
          echo "</SELECT>";
       }
       else
       {
          $aff_classe = ($classe == 1) ? "OUI" : "NON";
          echo $aff_classe;
          echo "<INPUT type='HIDDEN' class='INPUT' name='classe' value='$classe'>";
       }
       echo anoter($mess_classe_a,"");
       echo "</TD>";
       echo "<TD nowrap valign='top'><B>$mess_presc_mut</B><br />";
       echo "<SELECT class='SELECT' name='publique' size=1>";
       if ($publique == 1)
       {
         echo "<OPTION value='1' selected>$mess_oui</OPTION>";
         echo "<OPTION value='0'>$mess_non</OPTION>";
       }
       elseif ($publique == 0)
       {
         echo "<OPTION value='0' selected>$mess_non</OPTION>";
         echo "<OPTION value='1' >$mess_oui</OPTION>";
       }
       echo "</SELECT></TD>";
       echo "<TD nowrap><B>$mess_presc_acces</B><br />";
       echo "<SELECT class='SELECT' name='accession' size=1>";
       if ($accession == 1)
       {
         echo "<OPTION value='1' selected>$mess_oui</OPTION>";
         echo "<OPTION value='0'>$mess_non</OPTION>";
       }
       elseif ($accession == 0)
       {
         echo "<OPTION value='0' selected>$mess_non</OPTION>";
         echo "<OPTION value='1' >$mess_oui</OPTION>";
       }
       echo "</SELECT></TD>";
       echo "<TD nowrap><B>Forum avec Fichier-joint</B><br />";
       echo "<SELECT class='SELECT' name='OkUpld' size=1>";
       if ($OkUpld == 'Y')
       {
         echo "<OPTION value='Y' selected>$mess_oui</OPTION>";
         echo "<OPTION value='N'>$mess_non</OPTION>";
       }
       else
       {
         echo "<OPTION value='N' selected>$mess_non</OPTION>";
         echo "<OPTION value='Y' >$mess_oui</OPTION>";
       }
       echo "</SELECT></TD>";
       echo "</TR></TABLE></TD></TR>";
       echo "<INPUT TYPE=HIDDEN NAME=num_forum VALUE='$num_forum'>";
       echo "<INPUT TYPE=HIDDEN NAME=numero VALUE='$numero'>";
       echo "<INPUT TYPE=HIDDEN NAME=modif_grp VALUE='$modif_grp'>";
       echo "<TR height='50'><TD align=left valign='center'><A HREF=\"javascript:history.back();\" onmouseover=\"img_annonce.src='images/fiche_identite/boutretourb$suffixer.gif';return true;\" onmouseout=\"img_annonce.src='images/fiche_identite/boutretour$suffixer.gif'\">";
       echo "<IMG NAME=\"img_annonce\" SRC=\"images/fiche_identite/boutretour$suffixer.gif\" BORDER='0' onLoad=\"tempImg=new Image(0,0); tempImg.src='images/fiche_identite/boutretourb$suffixer.gif'\"></A></TD>";
       echo "<TD valign='center' colspan='2'><A HREF=\"javascript:document.MForm.submit();\" onmouseover=\"img1.src='images/fiche_identite/boutvalidb$suffixer.gif';return true;\" onmouseout=\"img1.src='images/fiche_identite/boutvalid$suffixer.gif'\">".
            "<IMG NAME=\"img1\" SRC=\"images/fiche_identite/boutvalid$suffixer.gif\" BORDER='0' onLoad=\"tempImg=new Image(0,0); tempImg.src='images/fiche_identite/boutvalidb$suffixer.gif'\"></A></TD></TR>";
       echo "<TR><TD align='left'>* <SMALL>$non_vis_app</SMALL></TD></TR>";
      echo "</TABLE></TD></TR></TABLE>";
  }
  echo "</TD></TR></TABLE></FORM></TD></TR></TABLE>";
  exit;
}// fin création modif
if (isset($cms) && $cms == 1)
{
  include ("style.inc.php");
  ?>
  <script type="text/javascript" src="OutilsJs/lib/ajax/ajax_cms.js"></script>
  <div id="affiche" class="Status"></div>
  <div id="mon_contenu" class="cms"
        onClick="javascript:$(document).ready(function() {if ($.browser.msie) {$('#mon_contenu').hide();}else{$('#mon_contenu').hide('slow');}})"
        <?php echo "title=\"$mess_clkF\">".stripslashes($mess_notif);?>
  </div>
  <?php
  if ($mode_user == 'tout')
     $typ_user = GetDataField ($connect,"select util_typutil_lb from utilisateur where util_cdn='$id_user'","util_typutil_lb");
  else
     $typ_user = "RESPONSABLE_FORMATION";
  if (isset($message) && $message != '')
      echo notifier($message);
  entete_simple($mess_suiv_form_presc);
  if ($ret_supp_tous == 1 || $ret_supp == 1)
  {
     $nom_du_grp =GetDataField ($connect,"SELECT grp_nom_lb from groupe where grp_cdn = $le_groupe","grp_nom_lb");
     $le_message = ($ret_supp_tous == 1) ?  $mess_supp_tts_form : $mess_supp_app_form;
     echo notifier("$le_message : $nom_du_grp");
  }
  $lien = "gestion_groupe.php?creation_groupe=1";
  $lien = urlencode($lien);
  echo "<tr><td><table><tr><td align='left' valign='center' style=\"padding: 6px;\">";
  echo "<A href=\"trace.php?link=$lien\" class='bouton_new'> $mess_new_format</A></td>".
       aide("modifier_gerer_formations",'')."</tr></table></td></tr>";
  echo "<TR><TD bgcolor='#FFFFFF' colspan='2'><TABLE cellspacing='1' cellpadding='3' width='100%'>";
  echo "<TR bgcolor='#2B677A' height='30'><TD valign='top'><FONT COLOR=white><B>$mess_gp_nom_grp</B></FONT>";
  if ($ordre_affiche == "nom_resp_form" || $ordre_affiche == "nom_tuteur")
    echo "<A HREF=\"gestion_groupe.php?cms=1&ordre_affiche=nom_groupe\"><img src='images/fleche1.gif' border=0></A>";
  echo "</TD>";
  echo "<TD valign='top'><FONT COLOR=white><B>$mess_resp</B></FONT>";
  if ($ordre_affiche == "nom_groupe" || $ordre_affiche == "nom_tuteur")
  {
    echo "<A HREF=\"gestion_groupe.php?cms=1&ordre_affiche=nom_resp_form\"><img src='images/fleche1.gif' border=0></A>";
  }
  echo "</TD>";
  echo "<TD valign='top'><FONT COLOR=white><B>$mess_superviseur</B></FONT>";
  if ($ordre_affiche == "nom_resp_form" || $ordre_affiche == "nom_groupe")
    echo "<A HREF=\"gestion_groupe.php?cms=1&ordre_affiche=nom_tuteur\"><img src='images/fleche1.gif' border=0></A>";
  echo "</TD>";
  echo "<TD ALIGN='CENTER' valign='top'><FONT COLOR=white><B>$mess_presc_acces</B></FONT></TD>";
  echo "<TD ALIGN='CENTER' valign='top'><FONT COLOR=white><B>$mess_presc_mut</B></FONT></TD>";
  echo "<TD ALIGN='CENTER' valign='top'><FONT COLOR=white><B>$mess_classe</B></FONT></TD>";
  // dey Dfoad
  echo "<TD ALIGN='CENTER' valign='top'><FONT COLOR=white><b>Exporter les usagers</b></FONT></TD>";
  echo "<TD ALIGN='CENTER' valign='top'><FONT COLOR=white><b>Schéma.formation</b></FONT></TD>";
  echo "<TD ALIGN='CENTER' valign='top'><FONT COLOR=white><b>$mess_modif_base</b></FONT></TD>";
  echo "<TD ALIGN='CENTER' valign='top'><FONT COLOR=white><b>$mess_parc_form</b></FONT></TD>";
  echo "<TD ALIGN='CENTER' valign='top'><FONT COLOR=white><b>$mes_des_app</b></FONT></TD>";
  echo "<TD ALIGN='CENTER' valign='top'><FONT COLOR=white><b>$mess_alrt</b></FONT></TD>";
  echo "<TD ALIGN='CENTER' valign='top'><FONT COLOR=white><b>Wiki(s)</b></FONT></TD>";
  echo "<TD ALIGN='CENTER' valign='top'><FONT COLOR=white><b>$mess_menu_forum</b></FONT></TD>";
  echo "<TD ALIGN='CENTER' valign='top'><FONT COLOR=white><b>$mess_ag_supp</b></FONT></TD></TR>";
  if($ordre_affiche == "nom_groupe")
    $req_grp = mysql_query("SELECT grp_cdn FROM groupe order by grp_nom_lb asc");
  elseif ($ordre_affiche == "nom_resp_form")
    $req_grp = mysql_query("SELECT groupe.grp_cdn FROM groupe,utilisateur where groupe.grp_resp_no = utilisateur.util_cdn order by utilisateur.util_nom_lb,groupe.grp_nom_lb asc");
  elseif ($ordre_affiche == "nom_tuteur")
    $req_grp = mysql_query("SELECT groupe.grp_cdn FROM groupe,utilisateur where groupe.grp_tuteur_no = utilisateur.util_cdn order by utilisateur.util_nom_lb,groupe.grp_nom_lb asc");
  $nbr_grp = mysql_num_rows($req_grp);
  $i=0;
  while ($i < $nbr_grp)
  {
    $n = mysql_result($req_grp,$i,"grp_cdn");
    $id_classe = GetDataField ($connect,"select grp_classe_on from groupe where grp_cdn = $n","grp_classe_on");
    $nom_grp = GetDataField ($connect,"SELECT grp_nom_lb from groupe where grp_cdn  = $n","grp_nom_lb");
    $comment_grp = GetDataField ($connect,"SELECT grp_commentaire_cmt from groupe where grp_cdn  = $n","grp_commentaire_cmt");
    $id_com = str_replace("'","\'",$comment_grp);
    $obj_grp = GetDataField ($connect,"SELECT grp_formobject_lb from groupe where grp_cdn  = $n","grp_formobject_lb");
    $desc_grp = GetDataField ($connect,"SELECT grp_formdesc_cmt from groupe where grp_cdn  = $n","grp_formdesc_cmt");
    $publique_grp = GetDataField ($connect,"SELECT grp_publique_on from groupe where grp_cdn  = $n","grp_publique_on");
    $acces_grp = GetDataField ($connect,"SELECT grp_flag_on from groupe where grp_cdn  = $n","grp_flag_on");
    $crea_grp = GetDataField ($connect,"SELECT grp_resp_no from groupe where grp_cdn  = $n","grp_resp_no");
    $resp_grp_nom = GetDataField ($connect,"SELECT util_nom_lb from utilisateur where util_cdn='$crea_grp'","util_nom_lb");
    $resp_grp_prenom = GetDataField ($connect,"SELECT util_prenom_lb from utilisateur where util_cdn='$crea_grp'","util_prenom_lb");
    $tuteur_grp = GetDataField ($connect,"SELECT grp_tuteur_no from groupe where grp_cdn  = $n","grp_tuteur_no");
    $tut_grp_nom = GetDataField ($connect,"SELECT util_nom_lb from utilisateur where util_cdn='$tuteur_grp'","util_nom_lb");
    $tut_grp_prenom = GetDataField ($connect,"SELECT util_prenom_lb from utilisateur where util_cdn='$tuteur_grp'","util_prenom_lb");
    $req_app_grp = mysql_query ("SELECT COUNT(*) from utilisateur_groupe where utilgr_groupe_no = $n");
    $nbre_app_grp = mysql_result($req_app_grp,0);
    $majuscule = $resp_grp_nom." ".$resp_grp_prenom;
    $majuscule_tut = $tut_grp_nom." ".$tut_grp_prenom;
    if ($typ_user == "ADMINISTRATEUR" || $id_user == $crea_grp || ($publique_grp == 1 && $acces_grp == 1))
    {
       $xx++;
       echo couleur_tr($xx,'');
       $grp_parc = mysql_query ("SELECT gp_parc_no from groupe_parcours where gp_grp_no =$n ORDER BY gp_ordre_no");
       $nb_f = mysql_num_rows ($grp_parc);
       if ($nb_f >0 )
       {
          $liste_parc ="<FONT COLOR=marroon><B><U>$mess_list_parc_grp</U></B></FONT><BR>";
          $nnn = 0;
          while ($nnn < $nb_f)
          {
              $cc = $nnn+1;
              $num_parc = mysql_result($grp_parc,$nnn,"gp_parc_no");
              $nom_parcours = getdatafield ($connect,"SELECT parcours_nom_lb from parcours where parcours_cdn = $num_parc","parcours_nom_lb");
              $nom_parc = str_replace("'","\'",$nom_parcours);
              $liste_parc .="$cc - $nom_parc<BR>";
            $nnn++;
          }
          echo "<TD style='text-align:left;'><a href=\"#\" target='main' ".
               "onclick=\"window.open('groupe.php?n=$n','','scrollbars,resizable=yes,height=340, width=600')\" ".
               "onMouseOver=\"overlib('".NewHtmlentities($liste_parc,ENT_QUOTES)."',ol_hpos,LEFT,BELOW,WIDTH,'150')\" ".
               "onMouseOut=\"nd()\">$nom_grp</A></FONT></TD>";
       }
       else
          echo "<TD style='text-align:left;'><a href=\"#\" target='main' ".
               "onclick=\"window.open('groupe.php?n=$n','','scrollbars,resizable=yes,height=340, width=600')\">$nom_grp</A></FONT></TD>";
       if ($typ_user != "ADMINISTRATEUR")
       {
          echo "<TD style='text-align:left;'>$majuscule</TD>";
       }
       else
       {
          echo "<TD align='left'><form name='form'>";
          echo "<SELECT name='select' class='SELECT' onChange=javascript:appelle_ajax(form.select.options[selectedIndex].value);document.location='#sommet';>";
          $req_resp_form = mysql_query("SELECT util_cdn,util_nom_lb,util_prenom_lb from utilisateur where util_typutil_lb = 'RESPONSABLE_FORMATION' or util_typutil_lb = 'ADMINISTRATEUR' ORDER BY util_nom_lb");
          $nbr_resp = mysql_num_rows($req_resp_form);
          if (strlen($majuscule) > 16)
             $majuscule = substr($majuscule,0,16)."..";
          echo "<OPTION value=\"gestion_groupe.php?cms=1&modif_resp=1&id_resp=$resp_grp&id_tut=$id_tut&id_grp=$n&publique=$publique_grp&accession=$acces_grp&id_classe=$id_classe&ordre_affiche=nom_groupe&mess_notif=$message\" value='$resp_grp'>$majuscule</OPTION>";
          $jj=0;
          $message=" $mess_modif_resp_grp $nom_grp ";
          while ($jj <$nbr_resp)
          {
             $id_resp = mysql_result($req_resp_form,$jj,"util_cdn");
             $nom_resp = mysql_result($req_resp_form,$jj,"util_nom_lb");
             $prenom_resp = mysql_result($req_resp_form,$jj,"util_prenom_lb");
             $majuscule =$nom_resp." ".$prenom_resp;
             if (strlen($majuscule) > 16)
                $majuscule = substr($majuscule,0,16)."..";
             echo "<OPTION value=\"gestion_groupe.php?cms=1&modif_resp=1&id_resp=$id_resp&id_tut=$id_tut&id_grp=$n&publique=$publique_grp&accession=$acces_grp&id_classe=$id_classe&ordre_affiche=nom_groupe&mess_notif=$message\">$majuscule</OPTION>";
           $jj++;
          }
          echo "</SELECT></TD></FORM>";
       }
       if ($id_user == $crea_grp || $typ_user == "ADMINISTRATEUR")
       {
          $req_tut_form = mysql_query("SELECT util_cdn,util_nom_lb,util_prenom_lb from utilisateur where util_typutil_lb != 'APPRENANT' ORDER BY util_nom_lb");
          $nbr_tut = mysql_num_rows($req_tut_form);
          echo "<TD align='left'><form name='form3$i'>";
          echo "<SELECT name='select3$i' class='SELECT' onChange=javascript:appelle_ajax(form3$i.select3$i.options[selectedIndex].value);document.location='#sommet';>";
          if (strlen($majuscule_tut) > 16)
             $majuscule_tut = substr($majuscule_tut,0,16)."..";
          echo "<OPTION value=\"gestion_groupe.php?cms=1&modif_autres=1&id_tut=$id_tut&id_resp=$crea_grp&id_grp=$n&publique=$publique_grp&accession=$acces_grp&id_classe=$id_classe&ordre_affiche=nom_groupe&mess_notif=$message\">$majuscule_tut</OPTION>";
          $jk=0;
          $message="$mess_modif_tut_grp $nom_grp";
          while ($jk <$nbr_tut)
          {
             $id_tut = mysql_result($req_tut_form,$jk,"util_cdn");
             $nom_tut = mysql_result($req_tut_form,$jk,"util_nom_lb");
             $prenom_tut = mysql_result($req_tut_form,$jk,"util_prenom_lb");
             $majuscule_tut = $nom_tut." ".$prenom_tut;
             if (strlen($majuscule_tut) > 16)
                $majuscule_tut = substr($majuscule_tut,0,16)."..";
             echo "<OPTION value=\"gestion_groupe.php?cms=1&modif_autres=1&id_tut=$id_tut&id_resp=$crea_grp&id_grp=$n&publique=$publique_grp&accession=$acces_grp&id_classe=$id_classe&ordre_affiche=nom_groupe&mess_notif=$message\">$majuscule_tut</OPTION>";
            $jk++;
          }
          echo "</SELECT></TD></FORM>";
       }
       else
          echo "<TD>$majuscule_tut</TD>";
       if ($id_user != $crea_grp && $typ_user != "ADMINISTRATEUR")
       {
             $aff_acces = ($acces_grp == 1) ? "OUI" : "NON";
             echo "<TD ALIGN='CENTER'><b>$aff_acces</b></TD>";
             $aff_publique = ($publique_grp == 1) ? "OUI" : "NON";
             echo "<TD ALIGN='CENTER'><b>$aff_publique</b></TD>";
             $aff_classe = ($id_classe == 1) ? "OUI" : "NON";
             echo "<TD ALIGN='CENTER'><b>$aff_classe</b></TD>";
       }
       $okay = 0;
       $okay_miens = 0;
       if ($id_user == $crea_grp || $typ_user == "ADMINISTRATEUR")//if ($id_user == $crea_grp || $typ_user == "ADMINISTRATEUR")
       {
          echo "<TD align='middle'><form name=\"form5$i\">";
          echo "<select name=\"select5$i\" class='SELECT' onChange=javascript:appelle_ajax(form5$i.select5$i.options[selectedIndex].value);document.location='#sommet';>";
          if ($acces_grp == 1)
          {
            $message="$nom_grp : $msgrp_acces";
            echo "<OPTION value=\"gestion_groupe.php?cms=1&modif_autres=1&id_tut=$tuteur_grp&id_resp=$crea_grp&id_grp=$n&publique=$publique_grp&accession=1&id_classe=$id_classe&ordre_affiche=nom_groupe&mess_notif=$message\">$mess_oui</OPTION>";
            $message="$nom_grp : $msgrp_noacces";
            echo "<OPTION value=\"gestion_groupe.php?cms=1&modif_autres=1&id_tut=$tuteur_grp&id_resp=$crea_grp&id_grp=$n&publique=$publique_grp&accession=0&id_classe=$id_classe&ordre_affiche=nom_groupe&mess_notif=$message\">$mess_non</OPTION>";
          }
          if ($acces_grp == 0)
          {
            $message="$nom_grp : $msgrp_noacces";
            echo "<OPTION value=\"gestion_groupe.php?cms=1&modif_autres=1&id_tut=$tuteur_grp&id_resp=$crea_grp&id_grp=$n&publique=$publique_grp&accession=0&id_classe=$id_classe&ordre_affiche=nom_groupe&mess_notif=$message\">$mess_non</OPTION>";
            $message="$nom_grp : $msgrp_acces";
            echo "<OPTION value=\"gestion_groupe.php?cms=1&modif_autres=1&id_tut=$tuteur_grp&id_resp=$crea_grp&id_grp=$n&publique=$publique_grp&accession=1&id_classe=$id_classe&ordre_affiche=nom_groupe&mess_notif=$message\">$mess_oui</OPTION>";
          }
          echo "</select></td></form>";
          echo "<TD align='middle'><form name=\"form4$i\">";
          echo "<select name=\"select4$i\" class='SELECT' onChange=javascript:appelle_ajax(form4$i.select4$i.options[selectedIndex].value);document.location='#sommet';>";
          if ($publique_grp == 1)
          {
            $message="$nom_grp : $msgrp_mut";
            echo "<OPTION value=\"gestion_groupe.php?cms=1&modif_autres=1&id_tut=$tuteur_grp&id_resp=$crea_grp&id_grp=$n&publique=1&accession=$acces_grp&id_classe=$id_classe&ordre_affiche=nom_groupe&mess_notif=$message\">$mess_oui</OPTION>";
            $message="$nom_grp : $msgrp_nomut";
            echo "<OPTION value=\"gestion_groupe.php?cms=1&modif_autres=1&id_tut=$tuteur_grp&id_resp=$crea_grp&id_grp=$n&publique=0&accession=$acces_grp&id_classe=$id_classe&ordre_affiche=nom_groupe&mess_notif=$message\">$mess_non</OPTION>";
          }
          if ($publique_grp == 0)
          {
            $message="$nom_grp : $msgrp_nomut";
            echo "<OPTION value=\"gestion_groupe.php?cms=1&modif_autres=1&id_tut=$tuteur_grp&id_resp=$crea_grp&id_grp=$n&publique=0&accession=$acces_grp&id_classe=$id_classe&ordre_affiche=nom_groupe&mess_notif=$message\">$mess_non</OPTION>";
            $message="$nom_grp : $msgrp_mut";
            echo "<OPTION value=\"gestion_groupe.php?cms=1&modif_autres=1&id_tut=$tuteur_grp&id_resp=$crea_grp&id_grp=$n&publique=1&accession=$acces_grp&id_classe=$id_classe&ordre_affiche=nom_groupe&mess_notif=$message\">$mess_oui</OPTION>";
          }
          echo "</select></td></form>";
          $nbr_app = mysql_num_rows(mysql_query("SELECT utilgr_groupe_no from utilisateur_groupe where utilgr_groupe_no = $n"));
          $nbr_presc = mysql_num_rows(mysql_query("SELECT presc_grp_no from prescription_$n"));
          if ($nbr_app == 0 || ($nbr_presc == 0 && $nbr_app > 0))
          {
             echo "<TD align='middle'><form name=\"form6$i\">";
             echo "<SELECT name=\"select6$i\" class='SELECT' onChange=javascript:appelle_ajax(form6$i.select6$i.options[selectedIndex].value);document.location='#sommet';>";
             if ($id_classe == 1)
             {
                $message="$nom_grp : $msgrp_ind";
                echo "<OPTION value=\"gestion_groupe.php?cms=1&modif_autres=1&id_tut=$tuteur_grp&id_resp=$crea_grp&id_grp=$n&publique=$publique_grp&accession=$acces_grp&id_classe=1&ordre_affiche=nom_groupe&mess_notif=$message\">$mess_oui</OPTION>";
                $message="$nom_grp : $msgrp_noind";
                echo "<OPTION value=\"gestion_groupe.php?cms=1&modif_autres=1&id_tut=$tuteur_grp&id_resp=$crea_grp&id_grp=$n&publique=$publique_grp&accession=$acces_grp&id_classe=0&ordre_affiche=nom_groupe&mess_notif=$message\">$mess_non</OPTION>";
             }
             if ($id_classe == 0)
             {
                $message="$nom_grp : $msgrp_noind";
                echo "<OPTION value=\"gestion_groupe.php?cms=1&modif_autres=1&id_tut=$tuteur_grp&id_resp=$crea_grp&id_grp=$n&publique=$publique_grp&accession=$acces_grp&id_classe=0&ordre_affiche=nom_groupe&mess_notif=$message\">$mess_non</OPTION>";
                $message="$nom_grp : $msgrp_ind";
                echo "<OPTION value=\"gestion_groupe.php?cms=1&modif_autres=1&id_tut=$tuteur_grp&id_resp=$crea_grp&id_grp=$n&publique=$publique_grp&accession=$acces_grp&id_classe=1&ordre_affiche=nom_groupe&mess_notif=$message\">$mess_oui</OPTION>";
             }
             echo "</SELECT></TD></FORM>";
          }
          else
          {
             $aff_classe = ($id_classe == 1) ? "OUI" : "NON";
             echo "<TD ALIGN='CENTER'><b>$aff_classe</b></TD>";
          }
//-------------------------------------------------------------------------
     //dey Dfoad
        if ($id_user == $crea_grp || $typ_user == "ADMINISTRATEUR")
        {
            $lien = "gestion_groupe.php?export_grp=1&groupe=$n";
            echo "<TD align=center><a href=\"$lien\" ".bulle("Exporter la liste des apprenants et intervenants de cette formation","","RIGHT","ABOVE",205);
            echo "<IMG SRC=\"images/repertoire/icoexport.gif\" BORDER=0></A></TD>";
            echo "<TD align=center>";
            if ($nb_f > 0)
            {
                $lien = "MindMapper.php?id_grp=$n";
                echo "<div style='clear:both;float:left;'>$bouton_gauche<a href=\"$lien\" ".
                     "  onClick=\"javascript:simplejQ_Ajax('/admin/InsereTrace.php?lelien=".urlencode($lien)."');\" ".
                     "title='Voir le schéma de cette formation'>";
                echo "MindMap</A>$bouton_droite</div>";
                $lien = "MindMapper.php?id_grp=$n&zip=1";
                echo "<div style='float:left;'><a href=\"$lien\" ".
                     " onClick=\"javascript:simplejQ_Ajax('/admin/InsereTrace.php?lelien=".urlencode($lien)."');\"".
                     " title=\"Exporter l'archive de cette formation\">";
                echo "<IMG SRC=\"images/repertoire/icoexport.gif\" BORDER=0 style='margin-left:8px;'></A></div>";
            }
            echo "</TD>";
        }
        else
           echo "<td>&nbsp;</td>";
        $nb_miens_parcs = mysql_result(mysql_query("select count(parcours_cdn) from parcours where parcours_cdn != 0 AND parcours_auteur_no = $id_user AND parcours_type_on = 0"),0);
        $suite_req = ($nb_miens_parcs > 0) ? "&miens_parc=1" : "&refer=2";
        $lien = "gestion_groupe.php?creation_groupe=1&modif_grp=1&numero=$n";
        $lien = urlencode($lien);
        echo "<TD align=center><a href=\"trace.php?link=$lien\" target='main'".bulle($msq_modifier,"","RIGHT","ABOVE",80);
        echo "<IMG SRC=\"images/repertoire/icoGrenomfich20.gif\" BORDER=0></A></TD>";
        if ($nb_f > 0)
        {
          $lien = "modif_gp.php?id_grp=$n".$suite_req;
          $lien = urlencode($lien);
          echo "<TD align=center><a href=\"trace.php?link=$lien\" target='main'".bulle($modif_parc_grp,"","RIGHT","ABOVE",120);
          echo "<IMG SRC=\"images/modules/tut_form/icomodiparc1.gif\" BORDER=0 ></A></TD>";
        }
        else
        {
          //$lien = "prescription.php?prem=1&presc=groupe&id_grp=$n&groupe=$n";//$mess_presc_formul
          $lien = "modif_gp.php?id_grp=$n".$suite_req;
          $lien = urlencode($lien);
          echo "<TD align=center><a href=\"trace.php?link=$lien\" target='main'".bulle($modif_parc_grp,"","RIGHT","ABOVE",120);
          echo "<IMG SRC=\"images/modules/tut_form/icoformulprescip.gif\" width='25' height='20' BORDER='0'></A></TD>";
        }
      }
      else
      {
           echo "<td>&nbsp;</td><td>&nbsp;</td>";
      }
      ///fin de admin et creagroupe
//          echo "<TD>&nbsp;</TD>";
//        $cherche_grp_parc = mysql_query ("SELECT utilgr_utilisateur_no from utilisateur_groupe where utilgr_groupe_no = $n");
//        $nb_fois = mysql_num_rows ($cherche_grp_parc);
          $req_grp_util = mysql_query("SELECT utilgr_utilisateur_no from utilisateur_groupe where utilgr_groupe_no = $n");
          $numb = mysql_num_rows($req_grp_util);
          $numb_miens = mysql_num_rows(mysql_query("SELECT util_cdn from utilisateur where util_auteur_no = $id_user"));
          if ($numb > 0)
             $okay = 1;
          if ($numb_miens > 0)
             $okay_miens = 1;

        if ($okay == 1 && ($typ_user == 'ADMINISTRATEUR' || $crea_grp == $id_user))
        {
          $lien="gestion_affectation.php?affecte_groupe=1&grp_resp=$n";
          $lien = urlencode($lien);
          $titrer = "$list_app_grp:<BR> $nbre_app_grp ".strtolower($mpr_list_form);
          echo "<td align='middle'><A href=\"trace.php?link=$lien\" target='main'".bulle($titrer,"","CENTER","ABOVE",220);
          echo "<IMG SRC=\"images/modules/tut_form/icolistapprenant.gif\" border=0></A></td>";
          $titrer = "";
        }
        elseif (($okay_miens == 1 && $publique_grp == 1 && $acces_grp == 1) || ($typ_user == 'ADMINISTRATEUR' || $crea_grp == $id_user))
        {

          $lien="gestion_affectation.php?affecte_groupe=1&grp_resp=$n"; //&affiche_groupe=$n
          $lien = urlencode($lien);
          $titrer = $mess_aff_mesapp;
          if ($typ_user != 'ADMINISTRATEUR' && $crea_grp != $id_user)
             echo "<td colspan ='2'></td>";
          echo "<td align='middle'><A href=\"trace.php?link=$lien\" target='main'".bulle($titrer,"","CENTER","ABOVE",220);
          $img_insere =  ($nb_f > 0) ? "icolistapprenant.gif" : "icoresapprenant.gif";
          echo "<IMG SRC=\"images/modules/tut_form/icoresapprenant.gif\" border=0></A></td>";
          $titrer = "";
          $okay_miens = 0;
        }
        else
          echo "<td>&nbsp;</td>";
        $nb_miens_insc = mysql_result(mysql_query("select count(util_cdn) from utilisateur where util_typutil_lb='APPRENANT' and util_blocageutilisateur_on = 'NON' and util_auteur_no = $id_user"),0);
        if ($typ_user == 'ADMINISTRATEUR' || $crea_grp == $id_user)
        {
        $lien = "message.php?type=groupe&num=$n";
        $lien = urlencode($lien);
        $titre_mess = "$mess_alert $pour ".strtolower($mess_menu_mail_app)."&nbsp;$mess_menu_gestion_grp $nom_grp";
        if ($okay == 1)
        {
          echo "<TD align=center><A HREF=\"#\" onclick=\"window.open('trace.php?link=$lien','','width=680,height=380,resizable=yes,status=no')\"".
                bulle($titre_mess,"","LEFT","ABOVE",220);
          echo "<IMG SRC=\"images/modules/icoanoter.gif\" BORDER=0></A></TD>";
          $okay = 0;
        }
        else
          echo "<TD>&nbsp;</TD>";
        // Debut Wiki
        $numWkG = 10000+$n;
        $nbWkGrp = mysql_num_rows(mysql_query("select * from wikiapp where wkapp_seq_no=$numWkG"));
        if ($nbWkGrp > 0)
        {
                 $titrer = "Wiki(s)";
                 $lien = "wiki/wikiGrp.php?id_seq=$numWkG&id_parc=10000&id_grp=$n&keepThis=true&TB_iframe=true&height=260&width=550";
                 echo "<td align='center'>$bouton_gauche<A HREF=\"$lien\"  class='thickbox'".
                      " name='Wiki(s) de la formation ' title='Documents en ligne communs à cette formation'>".
                      "$titrer</A>$bouton_droite</td>";
                 $titrer ='';
        }else
          echo "<TD>&nbsp;</TD>";
          //@forum
          $id_forum = GetDataField($connect, "select id from forums where name ='$nom_grp'", "id");
          echo "<TD align='center' valign='center'>";
         $reqFlect = mysql_result(mysql_query("select count(*) from forum_lecture where
                                               forlec_forum_no=$id_forum"),0);
         if ($nbr_app == 0 && $reqFlect > 0)
         {
           echo ' <div id="ReinitForum"><img src="images/suppression1.gif" border="0" width="15" style="cursor:pointer;"
            onClick="ReinitForum('.$id_forum.');" title="Remettre à zéro le compteur de consultattion du forum"></div>';
         }
         else
         {
          $Register = base64url_encode($_COOKIE["monID"].'_'.$_COOKIE["monLogin"].'|'.str_replace('http://','',str_replace('.educagri.fr','',$adresse_http)));
          $comment_forum = GetDataField($connect, "select name from forums where id='$id_forum'", "name");
          $course = base64url_encode('formateur|0|0|-|'.$n);
          $lien_forum = 'forum/list.php?f='.$id_forum.'&collapse=1'.TinCanTeach ('formateur|0|0|-|'.$n,'forum/list.php?f='.$id_forum.'&collapse=1',$adresse_http.'/Forums - ');
          $titrer = $mess_menu_forum." ".$comment_forum;
          echo "<A HREF=\"$lien_forum\" target='blank' ".
             bulle($titrer,"","LEFT","ABOVE",100)."<img src='images/forum/icoforum.gif' border='0'></A>";
        }
        echo "</TD>";
        //@supprimer
        $parc_gp_query = mysql_query ("SELECT gp_parc_no from groupe_parcours where gp_grp_no = $n");
        $nb_gp = mysql_num_rows ($parc_gp_query);
        if ($numb == 0 && $nb_gp == 0)
        {
          $lien = "gestion_groupe.php?creation_groupe=1&creer_groupe=1&supp_grp=1&numero=$n";
          $lien = urlencode($lien);
          echo "<td align=center><a href=\"trace.php?link=$lien\" target='main' ".bulle($mess_ag_supp,"","LEFT","ABOVE",80);
          echo "<IMG SRC=\"images/messagerie/icoGpoubel.gif\" height=\"20\" width=\"15\" BORDER=0></A></td>";
        }
        else
          echo "<TD align='center'><a href=\"javascript:void(0);\" ".bulle($mess_nosup_grp,"","LEFT","ABOVE",220).
               "<IMG SRC=\"images/repertoire/icoptiinterdit.gif\" BORDER=0></TD>";
       }
       else
          echo "<td colspan=9>&nbsp;</td>";
       }
       echo "</TR>";
      //}
     $i++;
    }
 echo"</TABLE></TD></TR></TABLE></TD></TR></TABLE></TD></TR></TABLE></CENTER>";
}
?>
<div id="affiche" class="Status"></div>
<script type="text/javascript">
$(document).ready(function()
{
    $('#mon_contenu').click(function()
    {
      if ($.browser.msie) {
      $(this).hide();
      }else{
      $(this).hide('slow');
      }
    });
}

</script>
<div id="mon_contenu" class="cms"  <?php echo "title=\"$mess_clkF\";"?>></div>
<div id='mien' class='cms'></div>
</body>
</html>

