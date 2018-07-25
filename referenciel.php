<?php
if (!isset($_SESSION)) session_start();
if (!isset($_SESSION['id_user']) || $_SESSION['id_user'] == "")
{
  exit();
}
require 'admin.inc.php';
require 'fonction.inc.php';
include ("include/UrlParam2PhpVar.inc.php");
require "lang$lg.inc.php";
require 'fonction_html.inc.php';
dbConnect();
$html = "";
/*
echo "<pre>";
     print_r($_POST);print_r($_GET);
echo "</pre>";
*/
 if (isset($_GET['acces']) && $_GET['acces'] == "_entree" && isset($_GET['passe_acces']) && $_GET['passe_acces'] == 1)
{
  unset ($_SESSION['acces']);
  $_SESSION['acces'] = $acces;
  $requete_sequence = $requete_seq;
  $_SESSION['requete_sequence'] = $requete_sequence;
  $passe_acces = 0;
}
if ($lg == "fr")
{
  $param_sql = mysql_query("SELECT * FROM param_referentiel ORDER BY paramref_cdn");
  $nbr_param = mysql_num_rows($param_sql);
  $i = 0;
  $param = array ();
  $param_type = array ();
  $param_abr = array ();
  while ($i < $nbr_param)
  {
    $param[$i +1] = clean_text(mysql_result($param_sql, $i, "paramref_nom_lb$lg"));
    $param_type[$i +1] = clean_text(mysql_result($param_sql, $i, "paramref_type_lb"));
    $param_abr[$i +1] = clean_text(mysql_result($param_sql, $i, "paramref_nomabr_lb$lg"));
    $i++;
  }
}
include 'style.inc.php';
if (isset($rea) && $rea == 1)
{
  entete_simple($m_ref_ret_dup);
  echo "<TR><TD colspan='2'><FONT SIZE='2'> $nom_diplo ---> $titre_dupli<BR>$m_ref_ret_dup1</B></FONT></TD></TR>";
  echo boutret(1, 1);
  echo "</TABLE></TD></TR></TABLE>";
  $num_chang = GetDataField($connect, "select ref_cdn from referentiel where ref_nomabrege_lb=\"$titre_dupli\" AND ref_parent_no= '$parente' AND ref_dom_lb=\"$matiere\"", "ref_cdn");
  $sr_sql = mysql_query("SELECT * FROM referentiel  WHERE ref_cdn = '$num_chang'");
  $nbr_sous_result = mysql_num_rows($sr_sql);
  $i = 0;
  while ($i < $nbr_sous_result)
  {
    $desc = clean_text(mysql_result($sr_sql, $i, 'ref_desc_cmt'));
    $dom = mysql_result($sr_sql, $i, 'ref_dom_lb');
    $nom_r = clean_text(mysql_result($sr_sql, $i, 'ref_nom_lb'));
    $sous_cat = mysql_result($sr_sql, $i, 'ref_nomabrege_lb');
    $obj = mysql_result($sr_sql, $i, "ref_denom_lb");
    $num_dupli = mysql_result($sr_sql, $i, "ref_cdn");
    $niv = mysql_result($sr_sql, $i, "ref_niv_no");
    $patre = mysql_result($sr_sql, $i, "ref_parent_no");
    $nom_p = mysql_result($sr_sql, $i, "ref_nomparent_lb");
    $auteur_dupli = $titre_dupli . "_" . $nom;
    $nom_abr = $nom . "_" . $sous_cat;
    $nom_patre = $nom . "_" . $nom_p;
    $num_max1 = Donne_ID($connect, "select max(ref_cdn) from referentiel");
    $req1 = mysql_query("INSERT INTO referentiel (ref_cdn,ref_desc_cmt,ref_dom_lb,ref_nom_lb,ref_nomabrege_lb,ref_denom_lb,ref_auteur_lb,ref_local_on,ref_niv_no,ref_parent_no,ref_nomparent_lb) VALUES ('$num_max1',\"$desc\",\"$dom\",\"$nom_r\",\"$nom_abr\",\"" . $param[3] . "\",\"$login\",'local',\"$niv\",'6',\"" . $param[1] . "\")");
    $num_max2 = $num_max1 +1;
    $new_pere = $num_max1;
    $new_nom_abr = $nom_abr;
    $req2 = mysql_query("INSERT INTO referentiel (ref_cdn,ref_desc_cmt,ref_dom_lb,ref_nom_lb,ref_nomabrege_lb,ref_denom_lb,ref_auteur_lb,ref_local_on,ref_niv_no,ref_parent_no,ref_nomparent_lb) VALUES ('$num_max2',\"$desc\",\"$dom\",\"$nom_r\",\"$new_nom_abr\"," . $param[4] . ",\"$login\",'local',\"$niv\",\"$num_max1\",\"$nom_abr\")");
    $mon_param1 = $param[5];
    $objectif_sr_sql = mysql_query("SELECT * FROM referentiel  WHERE ref_parent_no = \"$num_dupli\" AND ref_denom_lb=\"$mon_param1\"");
    $obj_nbr_sous_result = mysql_num_rows($objectif_sr_sql);
    $j = 0;
    while ($j < $obj_nbr_sous_result)
    {
      $obj_desc = clean_text(mysql_result($objectif_sr_sql, $j, "ref_desc_cmt"));
      $obj_dom = clean_text(mysql_result($objectif_sr_sql, $j, "ref_dom_lb"));
      $obj_nom_r = clean_text(mysql_result($objectif_sr_sql, $j, "ref_nom_lb"));
      $obj_sous_cat = clean_text(mysql_result($objectif_sr_sql, $j, "ref_nomabrege_lb"));
      $obj_obj = clean_text(mysql_result($objectif_sr_sql, $j, "ref_denom_lb"));
      $obj_num_dupli = mysql_result($objectif_sr_sql, $j, "ref_cdn");
      $obj_niv = mysql_result($objectif_sr_sql, $j, "ref_niv_no");
      $obj_patre = mysql_result($objectif_sr_sql, $j, "ref_parent_no");
      $obj_nom_p = clean_text(mysql_result($objectif_sr_sql, $j, "ref_nomparent_lb"));
      $obj_auteur_dupli = $titre_dupli . "_" . $nom;
      $obj_nom_abr = $nom . "_" . $obj_sous_cat;
      $obj_nom_patre = $new_nom_abr;
      $obj_num_max = Donne_ID($connect, "select max(ref_cdn) from referentiel");
      $obj_new_pere = $num_max1;
      $obj_req = mysql_query("INSERT INTO referentiel (ref_cdn,ref_desc_cmt,ref_dom_lb,ref_nom_lb,ref_nomabrege_lb,ref_denom_lb,ref_auteur_lb,ref_local_on,ref_niv_no,ref_parent_no,ref_nomparent_lb) VALUES ('$obj_num_max',\"$obj_desc\",\"$obj_dom\",\"$obj_nom_r\",\"$obj_nom_abr\",\"$obj_obj\",\"$login\",'local',\"$obj_niv\",\"$obj_new_pere \",\"$obj_nom_patre\")");
      $mon_param2 = $param[6];
      $s_objectif_sr_sql = mysql_query("SELECT * FROM referentiel  WHERE ref_parent_no = \"$obj_num_dupli\" AND ref_denom_lb= \"$mon_param2\"");
      $s_obj_nbr_sous_result = mysql_num_rows($s_objectif_sr_sql);
      $k = 0;
      while ($k < $s_obj_nbr_sous_result)
      {
        $s_obj_desc = clean_text(mysql_result($s_objectif_sr_sql, $k, "ref_desc_cmt"));
        $s_obj_dom = clean_text(mysql_result($s_objectif_sr_sql, $k, "ref_dom_lb"));
        $s_obj_nom_r = clean_text(mysql_result($s_objectif_sr_sql, $k, "ref_nom_lb"));
        $s_obj_sous_cat = clean_text(mysql_result($s_objectif_sr_sql, $k, "ref_nomabrege_lb"));
        $s_obj_obj = clean_text(mysql_result($s_objectif_sr_sql, $k, "ref_denom_lb"));
        $s_obj_num_dupli = mysql_result($s_objectif_sr_sql, $k, "ref_cdn");
        $s_obj_niv = mysql_result($s_objectif_sr_sql, $k, "ref_niv_no");
        $s_obj_patre = mysql_result($s_objectif_sr_sql, $k, "ref_parent_no");
        $s_obj_nom_p = clean_text(mysql_result($s_objectif_sr_sql, $k, "ref_nomparent_lb"));
        $s_obj_auteur_dupli = $titre_dupli . "_" . $nom;
        $s_obj_nom_abr = $nom . "_" . $s_obj_sous_cat;
        $s_obj_nom_patre = $nom . "_" . $s_obj_nom_p;
        $s_obj_num_max = Donne_ID($connect, "select max(ref_cdn) from referentiel");
        $s_obj_new_pere = GetDataField($connect, "select ref_cdn from referentiel where ref_nomabrege_lb = \"$obj_nom_abr\" ", "ref_cdn");
        $s_obj_req = mysql_query("INSERT INTO referentiel (ref_cdn,ref_desc_cmt,ref_dom_lb,ref_nom_lb,ref_nomabrege_lb,ref_denom_lb,ref_auteur_lb,ref_local_on,ref_niv_no,ref_parent_no,ref_nomparent_lb) VALUES ('$s_obj_num_max',\"$s_obj_desc\",\"$s_obj_dom\",\"$s_obj_nom_r\",\"$s_obj_nom_abr\",\"$s_obj_obj\",\"$login\",'local',\"$s_obj_niv\",\"$s_obj_new_pere \",\"$s_obj_nom_patre\")");
        $k++;
      }
      $j++;
    }
    $i++;
  }
  exit;
}
// Procï¿½dure de dï¿½placement dans la structure d'un rï¿½fï¿½rentiel d'un ï¿½lement de ce rï¿½fï¿½rentiel
if (isset($structure) && $structure == 1)
{
  $structure = 0;
  $chang_struct = mysql_query("UPDATE referentiel SET ref_parent_no=\"$grandpa\",ref_denom_lb=\"$new_objet\",ref_nomparent_lb=\"$nom_pere\" where ref_cdn=$parente");
}
if (isset($modif_param) && $modif_param == 1 && isset($inserer) && $inserer == 1)
{
  $param_entree = $param_t . $i;
  for ($i = 1; $i < 5; $i++)
  {
    $param_entree = $param_t[$i];
    if ($param_entree == " ")
      $oubli++;
  }
  if ($oubli > 0)
  {
    include 'style.inc.php';
    $letitre = "$mess_menu_gest_ref : $ref_modif_param";
    entete_simple($letitre);
    echo "<TR><TD colspan='2'><FONT SIZE='2'>$mess_verif_oubli</FONT></TD></TR>";
    echo boutret(1, 1);
    echo "</TABLE></TD></TR></TABLE>";
    exit ();
  }
  else
  {
    for ($i = 1; $i < 7; $i++)
    {
      $param_entree = $param_t[$i];
      $param_abrev = $abrev[$i];
      $param_ref = $param[$i];
      $req_param = mysql_query("UPDATE param_referentiel SET paramref_nom_lb$lg = \"$param_entree\" WHERE paramref_cdn = $i");
      $req_param = mysql_query("UPDATE param_referentiel SET paramref_nomabr_lb$lg = \"$param_abrev\" WHERE paramref_cdn = $i");
      if ($param_entree != $param_ref)
        $req_refer = mysql_query("UPDATE referentiel SET ref_denom_lb = \"$param_entree\" WHERE  ref_denom_lb=\"$param_ref\"");
    }
  }
  $lien = "referenciel.php?flg=1";
  $lien = urlencode($lien);
  echo "<script language=\"JavaScript\">";
  echo "document.location.replace(\"trace.php?link=$lien\")";
  echo "</script>";
  exit ();
}
// Suppression d'un ï¿½lï¿½ment du rï¿½fï¿½rentiel
if (isset($supprimer) && $supprimer == 1 && isset($suppression) && $suppression == 1)
{
  $supprimer = 0;
  $suppression = 0;
  $supprime = mysql_query("DELETE FROM referentiel where ref_cdn = $num");
  $supprime = mysql_query("update sequence_referentiel set seqref_referentiel_no = 0 where seqref_referentiel_no = $num");
  $supprime = 0;
  $suppression = 0;
}
// Procï¿½dure de modification d'un enregistrement ajoutï¿½ avec rï¿½initialisation des flags
if (isset($modifier) && $modifier == 1 && isset($inserer) && $inserer == 1)
{
  if ($_POST['abrege'] == "")
  {
    $letitre = $mess_menu_gest_ref;
    entete_simple($letitre);
    echo "<TR><TD colspan='2'><FONT SIZE='2'>$mess_verif_oubli</FONT></TD></TR>";
    echo boutret(1, 0);
    echo fin_tableau($html);
    exit ();
  }
  if (isset($_POST['ajoutdom']) && $_POST['ajoutdom'] == 'on')
  {
    $remplacer = mysql_query("UPDATE referentiel SET ref_dom_lb=\"".$_POST['domnew']."\" WHERE ref_cdn=".$_POST['num']);
    $dom = $_POST['domnew'];
  }
  //  if ($parente == 0)
  $insere = mysql_query("UPDATE referentiel SET ref_nomparent_lb=\"$nom_pere\",ref_nomabrege_lb=\"$abrege\",ref_parent_no=\"$parente\",ref_local_on=\"$pub\",ref_auteur_lb=\"$login\",ref_nom_lb=\"$diplo\",ref_desc_cmt=\"".NewHtmlentities($desc,ENT_QUOTES)."\",ref_dom_lb=\"$dom\",ref_denom_lb=\"$denomin\",ref_niv_no=\"$niv\" WHERE ref_cdn=$num");
  if ($new_nom != $abrege)
  {
    $remplacer = mysql_query("UPDATE referentiel SET ref_nomparent_lb=\"$new_nom\" WHERE ref_nomparent_lb=\"$abrege\" AND ref_parent_no=\"$parente\"");
    $remplacer = mysql_query("UPDATE referentiel SET ref_nomabrege_lb=\"$new_nom\" WHERE ref_nomabrege_lb=\"$abrege\"");
  }
  $update = 1;
}
// Procï¿½dure d'insertion d'un enregistrement modifiï¿½ avec rï¿½initialisation des flags
if (isset($ajouter) && $ajouter == 1 && isset($inserer) && $inserer == 1)
{
  if ($_POST['desc'] == "" || $_POST['abrege'] == "" || ($_POST['dom'] == "" && $_POST['domnew'] == ""))
  {
    include 'style.inc.php';
    $letitre = $mess_menu_gest_ref;
    entete_simple($letitre);
    echo "<TR><TD colspan='2'><FONT SIZE='2'>$mess_verif_oubli</FONT></TD></TR>";
    echo boutret(2, 0);
    echo fin_tableau($html);
    exit ();
  }
  if ($denomin != $param[1] && $denomin != $param[2] && $denomin != $param[3])
    $abrege = $obj . $abrege;
  if (isset($_POST['ajoutdom']) && $_POST['ajoutdom'] == 'on')
    $dom = $_POST['domnew'];
    $id_new_ref = Donne_ID ($connect,"select max(ref_cdn) from referentiel");
    $sql_insere = mysql_query("INSERT INTO referentiel (ref_nomparent_lb,ref_parent_no,ref_local_on,ref_nom_lb,ref_nomabrege_lb,ref_desc_cmt,ref_niv_no,ref_dom_lb,ref_cdn,ref_auteur_lb,ref_denom_lb) VALUES (\"$nom_pere\",\"$parente\",\"$pub\",\"$diplo\",\"$abrege\",\"".NewHtmlentities($desc,ENT_QUOTES)."\",\"$niv\",\"$dom\",'$id_new_ref','$login',\"$denomin\")");
  $update = 1;
  //if ($parente == 0)
}
if (isset($update) && $update == 1)
{
  $inserer = 0;
  $modifier = 0;
  $ajouter = 0;
}
$nom = $_SESSION['name_user'];
$prenom = $_SESSION['prename_user'];
$dir = "ressources/" . $login . "_" . $id_user;
// Sï¿½lection et Affichage des pï¿½res principaux (caractï¿½ristique: ne contiennent pas d'url)
$resultat_sql = mysql_query("select * from referentiel  where ref_parent_no=0 ORDER BY ref_nomabrege_lb");
$nombre = mysql_num_rows($resultat_sql);
if ($nombre == 0 && (!isset($modif_param) || (isset($modif_param) && $modif_param != 1)) && (!isset($passer) || (isset($passer) && $passer != 1)))
{
  $letitre = $mess_menu_gest_ref;
  entete_simple($letitre);
  echo "<TR><TD colspan='2'><FONT SIZE='2'>$mrc_no_cat</FONT></TD></TR>";
    if ($typ_user == "ADMINISTRATEUR" && $lg == "fr" && $table != 1)
    {
      $lien = "referenciel.php?flg=1&modif_param=1&lien_sous_cat=0";
      $lien = urlencode($lien);
      echo "<TR><TD colspan='2'><A HREF=\"trace.php?link=$lien\" target='main' " . bulle("$ref_modif_param", "", "LEFT", "", 120) . "$ref_modif_param</A> : " . $param[4] . " , " . $param[5] . " etc....</TD></TR>";
    }
    echo "<TR><TD><HR size=4 align=left >";
    $lien = "referenciel.php?passer=1&flg=$flg&lien_sous_cat=1&marqueur=1&parente=0&ajouter=1&obj=".$param[1]."&denomin=".$param[1];
    $lien = urlencode($lien);
    $labulle = "$m_ref_aj_el --> <B>$obj</B>";
    echo " <P align=center> $bouton_gauche<A href=\"trace.php?link=$lien\"" . bulle($labulle, "", "CENTER", "ABOVE", 200) . " $m_ref_ajel ".$param[1]."</A>$bouton_droite</CENTER>";
    echo "</TD></TR>";
  echo boutret(1, 0);
  echo fin_tableau($html);
  exit ();
}
else
{
  //Affichage de l'arborescence supï¿½rieure
  $pointeur = $parente;
  $sous_c = array ();
  $matier = array ();
  $parbis = array ();
  $par1 = array ();
  $objt = array ();
  $pr = 0;
  while ($pointeur != 0)
  {
    $ptr = 0;
    $resultat = mysql_query("select * from referentiel where ref_cdn=$pointeur");
    $nbr_result = mysql_num_rows($resultat);
    $objt[$pr] = clean_text(mysql_result($resultat, $ptr, "ref_denom_lb"));
    $sous_c[$pr] = clean_text(mysql_result($resultat, $ptr, "ref_nomabrege_lb"));
    $matier[$pr] = clean_text(mysql_result($resultat, $ptr, "ref_dom_lb"));
    $parbis[$pr] = mysql_result($resultat, $ptr, "ref_parent_no");
    $par1[$pr] = mysql_result($resultat, $ptr, "ref_cdn");
    $pointeur = $parbis[$pr];
    $pr++;
  }
  $pr--;
  while ($pr > -1)
  {
    $lien = "referenciel.php?flg=$flg&kaler=$kaler&presc=$presc&matiere=$matier[$pr]&objectif=$objt[$pr]&lien_sous_cat=1&parente=$par1[$pr]&cat=$cat&sous_cat=$sous_c[$pr]&niveaux=1&prem=$prem&modif_seqref=$modif_seqref&modif_parcref=$modif_parcref&consult=$consult&ajout_seq=$ajout_seq&id_seq=$id_seq&parcours=$parcours&nom=$nom&description=$description&id_parc=$id_parc&proprio=$proprio&refer=$refer&change_ref=$change_ref&nb_seq=$nb_seq&prescription=$prescription&inscription_app=$inscription_app&parc=$id_parc&miens_parc=$miens_parc";
    $lien = urlencode($lien);
    if ($pr == 0)
    {
      $miettes .= "$signe <span style=\" color:#D45211;font-weight: bold;\">$sous_c[$pr] </span>";
      $ajout_obj = "$objt[$pr]";
    }
    else
    {
      $miettes .= "$signe<A href=\"trace.php?link=$lien\"><B> $sous_c[$pr] </B></A> ";
      $ajout_obj = "$objt[$pr]";
    }
    $pr--;
  }
  $pr++;
  if ($ajouter == 1 && !$inserer)
    $letitre = "$mess_menu_gest_ref : <SMALL>$ref_creat_ref_opt $denomin</SMALL>";
  elseif ($modifier == 1 && !$inserer)
    $letitre = "$mess_menu_gest_ref : <SMALL>$ref_modif_ref_opt $denomin</SMALL>";
  elseif ($modif_param == 1 && !$inserer)
    $letitre = "$mess_menu_gest_ref : $ref_modif_param";
  elseif ($supprimer == 1)
    $letitre = "$mess_menu_gest_ref : $ref_supp_tit";
  elseif (!isset ($ajout_obj) || $ajout_obj == '')
    $letitre = "<center><Font color='white' size='3'><B>$mess_annonce_rdf</B></font>";
  else
    $letitre = "<center><Font color='white' size='3'><B>$mess_annonce_rdf : $sous_c[$pr] </FONT><Font color='white' size='2'><B> [" . $objt[$pr] . "]</B></font>";
  entete_simple($letitre);
  echo "<TR><TD><DIV id='sequence'>";
  if ($lien_sous_cat == 1 || isset($table) || (($ajouter == 1 || $modifier == 1 || $modif_param == 1) && $inserer != 1) || ($supprimer == 1 && $suppression != 1))
  {
    $lien = "referenciel.php?flg=$flg&caler=$caler&prem=$prem&modif_seqref=$modif_seqref&modif_parcref=$modif_parcref&consult=$consult&ajout_seq=$ajout_seq&id_seq=$id_seq&parcours=$parcours&nom=$nom&description=$description&id_parc=$id_parc&proprio=$proprio&refer=$refer&change_ref=$change_ref&nb_seq=$nb_seq&prescription=$prescription&inscription_app=$inscription_app&id_ref=$id_ref&id_ref_parc=$id_ref_parc&miens=$miens&miens_parc=$miens_parc&parc=$id_parc&liste_act_seq=$liste_act_seq";
    $lien = urlencode($lien);
    echo "<A href=\"trace.php?link=$lien\"><B>$mess_accueil</B></A>";
  }
  echo $miettes;
  //Affichage de la structure d'accueil ï¿½ arborescence nulle
  if ($lien_sous_cat != 1 && (($ajouter != 1 && $modifier != 1) || (isset($update) && $update == 1)) && $modif_param != 1 && $supprimer != 1)
  {
    if ($typ_user == "ADMINISTRATEUR" && $lg == "fr" && $table != 1)
    {
      $lien = "referenciel.php?flg=$flg&modif_param=1&lien_sous_cat=0";
      $lien = urlencode($lien);
      echo "<A HREF=\"trace.php?link=$lien\" target='main' " . bulle("$ref_modif_param", "", "LEFT", "", 120) . "$ref_modif_param</A> : " . $param[4] . " , " . $param[5] . " etc....<BR>";
    }
    echo "</DIV></TD></TR>";
    echo "<TR><TD>";
    $req_dger = mysql_query("SELECT COUNT(*) from referentiel where ref_auteur_lb = 'DGER'");
    $nb_dger = mysql_result($req_dger,0);
    if ($nb_dger > 100 && !$table)
      echo "<IMG SRC='images/ecran-annonce/icoalertw.gif' border='0'><SMALL> $mess_avert_ref</SMALL>";
  }
}
if ((($ajouter != 1 && $modifier != 1) || (isset($update) && $update == 1)) &&
     !isset($modif_param) && !isset($supprimer) && !isset($table) && !isset($modif_param))
{
  echo "<TABLE width='98%'><TR><TD align=left>";
  echo "<HR size=4  align=left>";
  //    echo "<SMALL>$m_ref_arb<BR> ";
  //    echo "<IMG SRC=\"images/exclam.gif\" BORDER=0 width='12' height='12'>$m_ref_der<BR>";
  // Affichage des enregistrements d'une sous-catï¿½gorie en cours
  if ($lien_sous_cat != 1 || $parente == 0)
    $requete = "SELECT * from referentiel where ref_parent_no = '0'";
  else
    $requete = "select * from referentiel where ref_parent_no='$parente' GROUP BY ref_dom_lb,ref_nomabrege_lb order by ref_nomabrege_lb asc";
  $req2 = mysql_query($requete);
  if ($req2 == FALSE)
    exit;
  $nbrt = mysql_num_rows($req2);
  $init = mysql_result($req2, 0, "ref_auteur_lb");
  $obj = mysql_result($req2, 0, "ref_denom_lb");
  $bande_titre = "<TABLE cellpadding='3' cellspacing='2' border=0 width='98%'>";
  $bande_titre .= "<TR bgcolor='#2B677A'>";
  $bande_titre .= "<TD valign='top'><FONT COLOR=white><b>$msq_aff_ordre</b></FONT></TD>";
  $bande_titre .= "<TD valign='top'><FONT COLOR=white><b>$mess_arbre</b></FONT></TD>";
  if ($obj == $param[4] && $flg == 1 && $init == 'DGER' && ($typ_user == 'RESPONSABLE_FORMATION' || $typ_user == 'ADMINISTRATEUR'))
    $bande_titre .= "<TD valign='top' nowrap><FONT COLOR=white><b>$mess_dupliquer</b></FONT></TD>";
  $bande_titre .= "<TD valign='top'><FONT COLOR=white><b>$mess_label : $obj [$mess_nb_items]</b></FONT></TD>";
  $bande_titre .= "<TD valign='top'><FONT COLOR=white><b>$m_ref_dom / $mess_niveau</b></TD>";
  $bande_titre .= "<TD valign='top'><FONT COLOR=white><b>$mess_desc </b></FONT></TD>";
  if (!$flg && ($id_seq > 0 || $id_parc > 0))
    $bande_titre .= "<TD valign='top'><FONT COLOR=white><b>$msq_inserer</b></FONT></TD>";
  if (!$flag && !$id_parc && !$id_seq)
  {
    //if ($obj == $param[3] || $obj == $param[4] || $obj == $param[5])
      $bande_titre .= "<TD valign='top'><FONT COLOR=white><b>$msq_parc</b></FONT></TD>";
    $bande_titre .= "<TD valign='top'><FONT COLOR=white><b>$mess_modif_base</b></FONT></TD>";
    $bande_titre .= "<TD height='20' align='middle' valign='top'><FONT COLOR=white><b>$mess_ag_supp</b></FONT></TD>";
    if ($obj != $param[6])
      $bande_titre .= "<TD height='20' align='middle' valign='top'><FONT COLOR=white><b>$mess_ajouter</b></FONT></TD>";
  }
  $bande_titre .= "</TR>";
  echo $bande_titre;
  $l = 0;
  while ($l < $nbrt)
  {
    $control_seq = 0;
    $control_parc = 0;
    $control_child = 0;
    $num = mysql_result($req2, $l, "ref_cdn");
    $par = mysql_result($req2, $l, "ref_parent_no");
    $obj = clean_text(mysql_result($req2, $l, "ref_denom_lb"));
    $init = mysql_result($req2, $l, "ref_auteur_lb");
    $categ = mysql_result($req2, $l, "ref_nomabrege_lb");
    $descrip = clean_text(mysql_result($req2, $l, "ref_desc_cmt"));
    $niv = mysql_result($req2, $l, "ref_niv_no");
    $titr = mysql_result($req2, $l, "ref_nom_lb");
    $sup = mysql_result($req2, $l, "ref_dom_lb");
    $sous_cat = $categ;
    $responsable = $init;
    $parente1 = $num;
    $parente = $par;
    $nom_diplo = $tit;
    $matiere = $sup;
    $parental[$i] = $parente;
    $parental1[$i] = $parente1;
    $longueur = strlen($sous_cat);
    if ($obj == $param[1])
      $denom = $param[2];
    if ($obj == $param[2])
      $denom = $param[3];
    if ($obj == $param[3])
      $denom = $param[4];
    if ($obj == $param[4])
      $denom = $param[5];
    if ($obj == $param[5])
      $denom = $param[6];
    echo couleur_tr($l +1, '');
    if ($par > 0 || $lien_sous_cat != 1)
    {
      $nb_enfants = 0;
      $requete = "SELECT count(*) FROM referentiel WHERE ref_parent_no = '$num'";
      $nb_enfants = mysql_result(mysql_query($requete),0);
      $lien = "referenciel.php?flg=$flg&kaler=$kaler&presc=$presc&objectif=$obj&matiere=$matiere&lien_sous_cat=1&i=$i&sous_cat=".addslashes($sous_cat)."&detail=1&parente=$parental1[$i]&niveaux=$niveaux&prem=$prem&modif_seqref=$modif_seqref&consult=$consult&modif_parcref=$modif_parcref&parcours=$parcours&ajout_seq=$ajout_seq&id_seq=$id_seq&nom=$nom&description=$description&id_parc=$id_parc&proprio=$proprio&refer=$refer&change_ref=$change_ref&nb_seq=$nb_seq&prescription=$prescription&inscription_app=$inscription_app&parc=$id_parc&miens_parc=$miens_parc";
      $lien = urlencode($lien);
      if ($par == 0)
        $encore = " : &nbsp;$titr ";
      else
        $encore = "";
      if ($nb_enfants > 0)
        $afficher = " <A href=\"trace.php?link=$lien\"><B>  $sous_cat $encore [$nb_enfants]</B></A>";
      else
        $afficher = " <B>  $sous_cat $encore</B>";
      echo "<TD valign='top'><B>" . ($l +1) . "</B></TD>";
      //dey
      //$lien_arb = "archives/arbrefer.php?num=$parental1[$i]";
      $lien = "referenciel.php?flg=$flg&kaler=$kaler&presc=$presc&objectif=$obj&matiere=$matiere&lien_sous_cat=0&table=1&i=$i&sous_cat=$sous_cat&detail=0&parente=$parental1[$i]&niveaux=$niveaux&prem=$prem&modif_seqref=$modif_seqref&consult=$consult&modif_parcref=$modif_parcref&ajout_seq=$ajout_seq&parcours=$parcours&id_seq=$id_seq&nom=$nom&description=$description&id_parc=$id_parc&proprio=$proprio&refer=$refer&change_ref=$change_ref&nb_seq=$nb_seq&prescription=$prescription&inscription_app=$inscription_app&parc=$id_parc&miens_parc=$miens_parc";
      $lien = urlencode($lien);
      echo "<TD valign='top' align='center'><A href=\"trace.php?link=$lien\" " . bulle($m_ref_der, "", "RIGHT", "ABOVE", 120) . "<IMG SRC=\"images/exclam.gif\" BORDER=0></A></TD>";
      // Permet de dupliquer un rï¿½fï¿½rentiel national afin de le rï¿½amï¿½nager variable---->$rea=1
      if (($obj == $param[4]) && $flg == 1 && $responsable == 'DGER' && ($typ_user == 'RESPONSABLE_FORMATION' || $typ_user == 'ADMINISTRATEUR'))
      {
        $titre_dupli = trim($sous_cat);
        $lien = "referenciel.php?rea=1&choix_nom=0&nom_diplo=$nom_diplo&matiere=$matiere&titre_dupli=$titre_dupli&flg=$flg&kaler=$kaler&presc=$presc&objectif=$obj&lien_sous_cat=0&table=1&i=$i&sous_cat=$sous_cat&detail=0&parente=$parente&niveaux=$niveaux&prem=$prem&ajout_seq=$ajout_seq&parcours=$parcours&id_seq=$id_seq&nom=$nom&description=$description&id_parc=$id_parc&change_ref=$change_ref&nb_seq=$nb_seq&prescription=$prescription&inscription_app=$inscription_app";
        $lien = urlencode($lien);
        echo " <TD valign='top' align='center'><A href=\"trace.php?link=$lien\" " . bulle($m_ref_dupli, "", "CENTER", "ABOVE", 160) . "<IMG SRC=\"images/repertoire/icoptiedit.gif\" width='20' height='20' BORDER=0></A></TD>";
      }
      echo "<TD valign='top'> $afficher</B></TD><TD valign='top'><SMALL> $sup  $mess_niveau : $niv</TD><TD valign='top'><SMALL>$descrip<BR></SMALL></TD>";
      $afficher = "";
      //On doit savoir si l'on est dans creation de sequence ou de parcours
      if ($parcours == 1)
      {
        if ($modif_parcref == 1)
          $lien_ins = "parcours.php?consult=$consult&id_ref_parc=$num&modif_parcref=$modif_parcref&id_parc=$id_parc&proprio=$proprio&refer=$refer&liste=1&parc=$id_parc&miens_parc=$miens_parc";
        else
          $lien_ins = "parcours.php?id_ref_parc=$num&choix_ref=1&miens_parc=$miens_parc&refer=$refer&parc=$id_parc&miens_parc=$miens_parc&miens=$miens";
      }
      elseif ($change_ref == 1) $lien_ins = "parcours.php?nom=$nom&description=$description&id_parc=$id_parc&proprio=$proprio&refer=$refer&id_ref_parc=$num&action_parc=1&nb_seq=$nb_seq&parc=$id_parc&miens_parc=$miens_parc&miens=$miens";
      elseif ($prescription == 1) $lien_ins = "prescription.php?id_ref=$num&prem=0&prescription=1&kaler=$kaler&numero_groupe=$numero_groupe&presc=$presc";
      elseif ($inscription_app == 1) $lien_ins = "prescription.php?id_ref=$num&prem=0&inscription_app=$inscription_app";
      else
      { //Test pour savoir si l'on consulte ou si l'on crï¿½e
        if ($ajout_seq == 1)
          $lien_ins = "sequence$acces.php?consult=1&id_ref=$num&id_ref_seq=$num&ajout_seq=$ajout_seq&id_seq=$id_seq&id_parc=$id_parc&proprio=$proprio&refer=$refer&parc=$id_parc&miens_parc=$miens_parc&miens=$miens&id_ref_parc=$id_ref_parc";
        elseif ($modif_seqref == 1) $lien_ins = "sequence$acces.php?consult=$consult&id_ref=$num&id_ref_seq=$num&modif_seqref=$modif_seqref&id_seq=$id_seq&id_parc=$id_parc&proprio=$proprio&refer=$refer&liste=1&parc=$id_parc&miens_parc=$miens_parc&miens=$miens&id_ref_parc=$id_ref_parc";
        else
          $lien_ins = "sequence$acces.php?choix_ref=1&id_ref=$num&id_ref_seq=$num&id_parc=$id_parc&proprio=$proprio&refer=$refer&parc=$id_parc&miens_parc=$miens_parc&miens=$miens&id_ref_parc=$id_ref_parc";
      }
      if (!$id_parc && !$id_seq && $obj != $param[1])
      {
        echo "<TD valign='top'>";
        // vï¿½rifie si un parcours existe dï¿½jï¿½ pour cet ï¿½lï¿½ment du rï¿½fï¿½rentiel
        $sql_parcours = mysql_query("SELECT COUNT(*) from parcours WHERE parcours_referentiel_no='$num'");
        $res_parcours = mysql_result($sql_parcours,0);
        if ($res_parcours != 0)
          $parc_exist = 1;
        else
          $parc_exist = 0;
        if ($parc_exist)
        {
          //            echo"<TD valign='top'><IMG SRC=\"images/croixbleue.gif\" border='0'></TD>";
          echo "<FORM name=\"form$num\">";
          echo "<img src='images/croixbleue.gif' title='$msq_parc'><SELECT name=\"select$num\" onChange=javascript:appel_w(form$num.select$num.options[selectedIndex].value)>";
          $sql_parc = mysql_query("SELECT parcours_cdn,parcours_nom_lb from parcours WHERE parcours_referentiel_no='$num'");
          $nbr_parc_ref = mysql_num_rows($sql_parc);
          if ($nbr_parc_ref > 0)
          {
            echo "<OPTION selected>- - - - - - - -</OPTION>";
            $ip_p = 0;
            while ($ip_p < $nbr_parc_ref)
            {
              $control_parc = 1;
              $num_parc = mysql_result($sql_parc, $ip_p, "parcours_cdn");
              $nom_parc = mysql_result($sql_parc, $ip_p, "parcours_nom_lb");
              $droit_voir = GetDataField($connect, "select parcours_publique_on from parcours where parcours_cdn = '$num_parc'", "parcours_publique_on");
              $auteur_parc = GetDataField($connect, "select parcours_auteur_no from parcours where parcours_cdn = '$num_parc'", "parcours_auteur_no");
              if ($requete_parc == "" || !isset ($requete_parc))
                $requete_parc = "SELECT * from parcours where parcours_referentiel_no > 0  order by parcours.parcours_type_lb,parcours_nom_lb asc";
              $lien = "parcours.php?liste=1&consult=1&parcours=1&id_parc=$num_parc&refer=1&id_ref_parc=$num&parc=$num_parc&miens_parc=$miens_parc&miens=$miens";
              $lien = urlencode($lien);
              if ($droit_voir == 1 || $typ_user == "ADMINISTRATEUR" || ($droit_voir == 0 && $auteur_parc == $id_user))
                echo "<OPTION value='trace.php?link=$lien'>$nom_parc</OPTION>";
              else
                echo "<OPTION>$nom_parc &nbsp;(NA)</OPTION>";
              $ip_p++;
            }
          }
          echo "</SELECT></FORM>";
        }
      // Fin de la vï¿½rification d'existence de parcours*/
      // vï¿½rifie si une sï¿½quence existe dï¿½jï¿½ pour cet ï¿½lï¿½ment du rï¿½fï¿½rentiel
        $sql_sequence = mysql_query("SELECT COUNT(*) from sequence_referentiel WHERE seqref_referentiel_no='$num'");
        $res_sql_sequence = mysql_result($sql_sequence,0);
        if ($res_sql_sequence != 0)
          $seq_exist = 1;
        else
          $seq_exist = 0;
        if ($seq_exist == 1 && $parc_exist == 1)
          $comptnum = $num +100;
        elseif ($seq_exist == 1 && $parc_exist == 0) $comptnum = $num;
        if ($seq_exist)
        {
          //            echo"<TD valign='top'><IMG SRC=\"images/croixrouge.gif\" border='0'></TD>";
          $sql_sequence = mysql_query("select seqref_seq_no from sequence_referentiel where seqref_referentiel_no='$num'");
          $guide = "seqref_seq_no";
          $sseq = mysql_num_rows($sql_sequence);
          $nbr_seq = 0;
          echo "<FORM name=\"form$comptnum\">";
          echo "<img src='images/croixrouge.gif' title='$msq_seq'><SELECT name=\"select$comptnum\" onChange=javascript:appel_w(form$comptnum.select$comptnum.options[selectedIndex].value)>";
          echo "<OPTION>- - - - - - - -</OPTION>";
          while ($nbr_seq < $sseq)
          {
            if ($id_parc == "")
               $acces = "_entree";
            $control_seq = 1;
            $num_sequence = mysql_result($sql_sequence, $nbr_seq, $guide);
            $numero_referentiel = GetDataField($connect, "select seqref_referentiel_no from sequence_referentiel where seqref_seq_no = '$num_sequence'", "seqref_referentiel_no");
            $titre_sequence = GetDataField($connect, "select seq_titre_lb from sequence where seq_cdn = '$num_sequence'", "seq_titre_lb");
            $droit_voir = GetDataField($connect, "select seq_publique_on from sequence where seq_cdn = '$num_sequence'", "seq_publique_on");
            $auteur_seq = GetDataField($connect, "select seq_auteur_no from sequence where seq_cdn = '$num_sequence'", "seq_auteur_no");
            if ($requete_seq == "" || !isset ($requete_seq))
              $requete_seq = "select * from sequence,sequence_referentiel where sequence.seq_cdn = sequence_referentiel.seqref_seq_no AND sequence_referentiel.seqref_referentiel_no = 0 order by sequence.seq_titre_lb asc";
            $lien = "sequence$acces.php?liste=1&consult=1&id_seq=$num_sequence&refer=1&id_ref=$num&id_ref_seq=$num&parc=$id_parc&miens_parc=$miens_parc&miens=$miens&id_ref_parc=$id_ref_parc";
            $lien = urlencode($lien);
            if ($droit_voir == 1 || $typ_user == "ADMINISTRATEUR" || ($droit_voir == 0 && $auteur_seq == $id_user))
              echo "<OPTION value='trace.php?link=$lien'>$titre_sequence</OPTION>";
            else
              echo "<OPTION>$titre_sequence &nbsp;(NA)</OPTION>";
            $nbr_seq++;
          }
          echo "</SELECT></FORM>";
        }
        echo "</td>";
      }
      elseif ($parcours != 1 && $change_ref != 1 && $ajout_seq != 1 && $modif_seqref != 1)
          echo "<TD>&nbsp;</TD>";
      // Fin if $seq_exist
      // fin de vï¿½rification
      if (($typ_user == "FORMATEUR_REFERENT") || ($typ_user == "RESPONSABLE_FORMATION") || ($typ_user == "ADMINISTRATEUR"))
      {
        if (($prescription == 1 || $parcours == 1) && ($obj != $param[1] || ($parc_exist == 1 && $obj != $param[1])))
        {
          echo "<TD valign='top' align='center'>";
          $lien = $lien_ins;
          $lien = urlencode($lien);
          echo "<A href=\"trace.php?link=$lien\" " . bulle($m_ref_cre_parc, "", "LEFT", "", 120) . "<IMG SRC=\"images/modules/tut_form/icosequen20.gif\" BORDER=0></A>";
          echo "</TD>";
        }
        elseif ($id_seq > 0 && ($obj != $param[1] || ($seq_exist == 1 && $obj != $param[1])))
        {
          echo "<TD valign='top' align='center'>";
          $lien = $lien_ins;
          $lien = urlencode($lien);
          echo "<A href=\"trace.php?link=$lien\" " . bulle($m_ref_cre_seq, "", "LEFT", "", 120) . "<IMG SRC=\"images/modules/tut_form/icosequen20.gif\" BORDER=0></A>";
          echo "</TD>";
        }
      }
      $sql = mysql_query("select * from referentiel where ref_parent_no='$num'");
      $res_sql = mysql_num_rows($sql);
      if ($res_sql > 0)
        $control_child = 1;
      if (($typ_user ==  "ADMINISTRATEUR" || $init == $login) && !$id_parc && !$id_seq)
      {
        $lien = "referenciel.php?flg=$flg&kaler=$kaler&presc=$presc&kaler=$kaler&presc=$presc&marqueur=1&denomin=$obj&parente=$parente&lien_sous_cat=0&modifier=1&obj=$obj&num=$num&parcours=$parcours&ajout_seq=$ajout_seq&id_seq=$id_seq&nom=$nom&description=$description&id_parc=$id_parc&proprio=$proprio&refer=$refer&change_ref=$change_ref&nb_seq=$nb_seq&prescription=$prescription&inscription_app=$inscription_app&parc=$id_parc&miens_parc=$miens_parc&id_ref_parc=$id_ref_parc";
        $lien = urlencode($lien);
        echo "<TD valign='top' align='center'><A href=\"trace.php?link=$lien\" " . bulle("$msq_modifier", "", "RIGHT", "ABOVE", 120) .
             "<IMG SRC=\"images/repertoire/icoGrenomfich20.gif\" BORDER=0></A></TD>";
        $lien = "referenciel.php?flg=$flg&kaler=$kaler&presc=$presc&lien_sous_cat=0&supprimer=1&parente=$parente&num=$num&prem=$prem&modif_seqref=$modif_seqref&consult=$consult&modif_parcref=$modif_parcref&parcours=$parcours&ajout_seq=$ajout_seq&id_seq=$id_seq&nom=$nom&description=$description&id_parc=$id_parc&proprio=$proprio&refer=$refer&change_ref=$change_ref&nb_seq=$nb_seq&prescription=$prescription&inscription_app=$inscription_app&parc=$id_parc&miens_parc=$miens_parc&id_ref_parc=$id_ref_parc";
        $lien = urlencode($lien);
        if ($control_seq != 1 && $control_parc != 1 && $control_child != 1)
          echo "<TD valign='top' align='center'><A href=\"javascript:void(0);\" onclick=\"javascript:confm('trace.php?link=$lien');\" " .
               bulle($m_ref_supp_elem, "", "CENTER", "ABOVE", 160) . "<IMG SRC=\"images/messagerie/icopoubelressour.gif\" BORDER=0></A></TD>";
        else
          echo "<TD valign='top' align='center'><A HREF=javascript:void(0); " . bulle("$ref_no_supp", "", "CENTER", "ABOVE", 220) .
               "<IMG SRC=\"images/repertoire/icoptiinterdit.gif\" BORDER=0></A></TD>";
        if ($obj == $param[1])
          $denom = $param[2];
        if ($obj == $param[2])
          $denom = $param[3];
        if ($obj == $param[3])
          $denom = $param[4];
        if ($obj == $param[4])
          $denom = $param[5];
        if ($obj == $param[5])
          $denom = $param[6];
        if ($cat == $param[6])
          $obj_affiche = $obj;
        if ($obj != $param[6])
        {
          $lien = "referenciel.php?flg=$flg&kaler=$kaler&presc=$presc&marqueur=1&$lien_sous_cat=0&parente=$num&ajouter=1&obj=$denom&denomin=$denom&parcours=$parcours&prem=$prem&modif_seqref=$modif_seqref&consult=$consult&modif_parcref=$modif_parcref&ajout_seq=$ajout_seq&id_seq=$id_seq&nom=$nom&description=$description&id_parc=$id_parc&proprio=$proprio&refer=$refer&change_ref=$change_ref&nb_seq=$nb_seq&prescription=$prescription&inscription_app=$inscription_app&parc=$id_parc&miens_parc=$miens_parc&id_ref_parc=$id_ref_parc";
          $lien = urlencode($lien);
          echo "<TD valign='top' align='center'><A href=\"trace.php?link=$lien\" " .
               bulle("$m_ref_aj_enf : <b>$denom</b>", "", "CENTER", "ABOVE", 200) .
               "<IMG SRC=\"images/repertoire/icoajoutarboadmin.gif\" BORDER=0></A></TD>";
        }
        else
          echo "<TD>&nbsp;</TD>";
      }
      else
             echo "<TD>&nbsp;</TD><TD>&nbsp;</TD><TD>&nbsp;</TD>";
      echo "</TR>";
    }
    $l++;
  }
  echo "</TABLE></TD></TR>";
  if (($typ_user == "RESPONSABLE_FORMATION" || $typ_user == "ADMINISTRATEUR") && $init != 'DGER')
  {
    echo "<TR><TD><HR size=4 align=left >";
    $lien = "referenciel.php?flg=$flg&kaler=$kaler&presc=$presc&$lien_sous_cat=0&marqueur=1&parente=$par&ajouter=1&obj=$obj&denomin=$obj&prem=$prem&modif_seqref=$modif_seqref&consult=$consult&modif_parcref=$modif_parcref&parcours=$parcours&ajout_seq=$ajout_seq&id_seq=$id_seq&nom=$nom&description=$description&id_parc=$id_parc&proprio=$proprio&refer=$refer&change_ref=$change_ref&nb_seq=$nb_seq&prescription=$prescription&inscription_app=$inscription_app&parc=$id_parc&miens_parc=$miens_parc&id_ref_parc=$id_ref_parc";
    $lien = urlencode($lien);
    $labulle = "$m_ref_aj_el --> <B>$obj</B>";
    echo " <P align=center> $bouton_gauche<A href=\"trace.php?link=$lien\" " . bulle($labulle, "", "CENTER", "ABOVE", 200) .
         " $m_ref_ajel $obj</A>$bouton_droite</CENTER>";
    echo "</TD></TR>";
  }
  echo "</TABLE>";
}
$denom = $affiche_denom;
$lien_sous_cat = 0;
if ($modif_param == 1 && !$inserer)
{
  echo "<CENTER><FORM NAME='form1' ACTION=\"referenciel.php?flg=$flg&modif_param=1\" METHOD='POST' target='main'><TABLE cellspacing='5' cellpadding='5'>";
  echo "<TR><TD align='center'><B>$mess_fav_tit</B></TD><TD><B>$mess_abreviation</B></TD><TD align='center'><B>$mess_fav_tit</B></TD><TD><B>$mess_abreviation</B></TD></TR>";
  echo "<TR>";
  echo "<TD><INPUT TYPE='text' class='INPUT' NAME=\"param_t[1]\" SIZE='30' VALUE=\"" . $param[1] . "\"></TD>";
  echo "<TD><INPUT TYPE='text' class='INPUT' NAME=\"abrev[1]\" SIZE='2' MAXLENGTH='3' VALUE=\"" . $param_abr[1] . "\"></TD>";
  echo "<TD><INPUT TYPE='text' class='INPUT' NAME=\"param_t[2]\" SIZE='30' VALUE=\"" . $param[2] . "\"></TD>";
  echo "<TD><INPUT TYPE='text' class='INPUT' NAME=\"abrev[2]\" SIZE='2' MAXLENGTH='3' VALUE=\"" . $param_abr[2] . "\"></TD>";
  echo "</TR>";
  echo "<TR><TD colspan='2' align='center'><B>$mess_fav_tit</B></TD><TD colspan='2' align='left'><B>$mess_abreviation</B></TD></TR>";
  for ($i = 3; $i < 7; $i++)
  {
    echo "<TR>";
    echo "<TD colspan='2' align='right'><INPUT TYPE='text' class='INPUT' NAME=\"param_t[$i]\" SIZE='30' VALUE=\"" . $param[$i] . "\"></TD>";
    echo "<TD colspan='2' align='left'><INPUT TYPE='text' class='INPUT' NAME=\"abrev[$i]\" SIZE='2' MAXLENGTH='3' VALUE=\"" . $param_abr[$i] . "\"></TD>";
    echo "</TR>";
  }
  echo "<INPUT TYPE='HIDDEN' NAME='lien_sous_cat' VALUE='1'>";
  echo "<INPUT TYPE='HIDDEN' NAME='inserer' VALUE='1'>";
  echo "<TR><TD colspan='1' align='left'>";
  echo boutret(1, 0);
  echo "</TD><TD align='center' colspan='2'><A HREF=\"javascript:document.form1.submit();\" onmouseover=\"img1.src='images/fiche_identite/boutvalidb.gif';return true;\" onmouseout=\"img1.src='images/fiche_identite/boutvalid.gif'\">" . "<IMG NAME=\"img1\" SRC=\"images/fiche_identite/boutvalid.gif\" BORDER='0' onLoad=\"tempImg=new Image(0,0); tempImg.src='images/fiche_identite/boutvalidb.gif'\"></A>";
  echo "</TD></TR></TABLE></FORM>";
  echo fin_tableau($html);
  exit;
}
// Procï¿½dure d'ajout d'un rï¿½fï¿½rentiel ou d'un ï¿½lï¿½ment d'un rï¿½fï¿½rentiel
if ($ajouter == 1 && !$inserer)
{
  $ajouter = 0;
  $sql_ajout = mysql_query("select * from referentiel where ref_cdn='$parente'");
  $res_sql = mysql_num_rows($sql_ajout);
  if ($res_sql > 0)
  {
    $i = 0;
    while ($i < $res_sql)
    {
      $categ_ajout = mysql_result($sql_ajout, $i, "ref_nomabrege_lb");
      $nom_pere = mysql_result($sql_ajout, $i, "ref_nomparent_lb");
      $par = mysql_result($sql_ajout, $i, "ref_parent_no");
      $denom = mysql_result($sql_ajout, $i, "ref_denom_lb");
      $diplo = mysql_result($sql_ajout, $i, "ref_nom_lb");
      $nival = mysql_result($sql_ajout, $i, "ref_niv_no");
      $matiere = mysql_result($sql_ajout, $i, "ref_dom_lb");
      $i++;
    }
  }
  echo "<CENTER><FORM NAME='form1' ACTION=\"referenciel.php?flg=$flg&ajouter=1&objectif=$obj&inserer=1&lien_sous_cat=1&matiere=$matiere\" METHOD='POST' target='main'><TABLE>";
  if (!$marqueur)
    echo "<INPUT TYPE='HIDDEN' NAME='nom_pere' VALUE=\"$nom_pere\"'>";
  if ($marqueur == 1 && $parente != 0)
    $nom_du_parent = GetDataField($connect, "select ref_nomabrege_lb from referentiel where ref_cdn = $parente", "ref_nomabrege_lb");
  if ($marqueur != 1 && $parente == 0)
    $denom = $param[2];
  if ($marqueur == 1)
    echo "<INPUT TYPE='HIDDEN' NAME='nom_pere' VALUE=\"$nom_du_parent\"";
  echo "<INPUT TYPE='HIDDEN' NAME='lien_sous_cat' VALUE='1'>";
  echo "<INPUT TYPE='HIDDEN' NAME='parente' VALUE='$parente'>";
  if ($login == "DGER")
  {
    echo "<TR><TD><B><small>$m_ref_nat_loc</small></B></TD><TD>
                           <SELECT  name='pub' size='1'>
                            <OPTION value='local'>$m_ref_loc</OPTION>
                            <OPTION value='national'>$m_ref_nat</OPTION>
                           </SELECT></TD></TR>";
  }
  else
    echo "<INPUT TYPE='HIDDEN' NAME='pub' VALUE='local'>";
  if ($marqueur == 1)
  {
    if ($denom == $param[1])
    {
      $abrege = $param_abr[2];
      $denome = $param[2];
    }
    elseif ($denom == $param[2])
    {
      $abrege = $param_abr[3];
      $denome = $param[3];
    }
    elseif ($denom == $param[3])
    {
      $abrege = $param_abr[4];
      $denome = $param[4];
    }
    elseif ($denom == $param[4])
    {
      $abrege = $param_abr[5];
      $denome = $param[5];
    }
    elseif ($denom == $param[5])
    {
      $abrege = $param_abr[6];
      $denome = $param[6];
    }
  }
  else
  {
    if ($denom == $param[1])
    {
      $abrege = $param_abr[1];
      $denome == $param[1];
    }
    elseif ($denom == $param[2])
    {
      $abrege = $param_abr[2];
      $denome = $param[2];
    }
    elseif ($denom == $param[3])
    {
      $abrege = $param_abr[3];
      $denome = $param[3];
    }
    elseif ($denom == $param[4])
    {
      $abrege = $param_abr[4];
      $denome = $param[4];
    }
    elseif ($denom == $param[5])
    {
      $abrege = $param_abr[5];
      $denome = $param[5];
    }
    elseif ($denom == $param[6])
    {
      $abrege = $param_abr[6];
      $denome = $param[6];
    }
  }
  if ($parente == 0)
    echo "<INPUT TYPE='HIDDEN' NAME='denomin' VALUE=\"" . $param[1] . "\">";
  else
    echo "<INPUT TYPE='HIDDEN' NAME='denomin' VALUE=\"$denome\">";
  if ($categ_ajout != "")
    $existant = $categ_ajout . "-" . $abrege;
  else
    $existant = $abrege;
  echo "<INPUT TYPE='HIDDEN' NAME='obj' VALUE=\"\">";
  echo "<TR><TD>$mpr_aj_newgrp</TD><TD valign='bottom'>";
  if (isset ($categ_ajout) && $denome != $param[1] && $denome != $param[2] && $denome != $param[3])
  {

    $req_existant = mysql_query("SELECT count(*) FROM referentiel where ref_nomabrege_lb like \"$existant%\" AND ref_denom_lb =\"$denome\"");
    $nb_exist = mysql_result($req_existant,0);
    $new_num = $nb_exist +1;
    echo "<INPUT TYPE='text' class='INPUT' NAME='abrege' SIZE=120 VALUE=\"\"  title = \"$mess_ref_indic\">";
  }
  else
    echo "<INPUT TYPE='text' class='INPUT' NAME='abrege' VALUE=\"\" SIZE=90";
  echo "</TD></TR>";
/*
  echo "<TR><TD>$m_ref_denom </TD>";
  echo "<TD><INPUT TYPE='text' class='INPUT' NAME='diplo' SIZE=80 VALUE=\"$diplo\"></TD></TR>";
*/
  echo "<INPUT TYPE='HIDDEN' NAME='diplo' VALUE=\"$diplo\">";
  echo "<TR><TD>Description</TD>";
  echo "<TD><TEXTAREA class='TEXTAREA' name=desc cols=120 rows=8></textarea></TD></TR>";
  echo "<TR><TD>$m_ref_dom </TD><TD>";
  //if ($denom == $param[1] || $denom == $param[2])
   // echo "<INPUT TYPE='text' class='INPUT' NAME='dom' SIZE='35'>";
  //else
  //{
    $param = $matiere;
    $nbdom = mysql_num_rows(mysql_query("select ref_dom_lb from referentiel where ref_dom_lb !=\"\" and ref_dom_lb != '-1' GROUP BY ref_dom_lb"));
    if ($nbdom > 0)
       Ascenseur("dom", "select ref_dom_lb from referentiel where ref_dom_lb !=\"\" and ref_dom_lb != '-1' GROUP BY ref_dom_lb", $connect, $param);
    echo'<INPUT TYPE="CHECKBOX"  name="ajoutdom" align="middle"   title="Pour ajouter un nouveau domaine">';
    echo "<INPUT TYPE='text' class='INPUT' name='domnew' size='35'>";
  //}
  echo "</TD></TR>";
  echo "<TR><TD>$mrc_niv </TD><TD>
             <SELECT  name='niv' size='1'>
                 <OPTION selected>$nival</OPTION>
                 <OPTION>$mrc_6 </OPTION>
                 <OPTION>$mrc_5 </OPTION>
                 <OPTION>$mrc_4 </OPTION>
                 <OPTION>$mrc_3 </OPTION>
                 <OPTION>$mrc_2 </OPTION>
                 <OPTION>$mrc_1 </OPTION>
             </SELECT></TD></TR><TR height='15'><TD>&nbsp;</TD></TR>";
  echo boutret(1, 0);
  echo "</TD><TD align='left'><A HREF=\"javascript:document.form1.submit();\" onmouseover=\"img1.src='images/fiche_identite/boutvalidb.gif';return true;\" onmouseout=\"img1.src='images/fiche_identite/boutvalid.gif'\">" . "<IMG NAME=\"img1\" SRC=\"images/fiche_identite/boutvalid.gif\" BORDER='0' onLoad=\"tempImg=new Image(0,0); tempImg.src='images/fiche_identite/boutvalidb.gif'\"></A>";
  echo "</TD></TR></TABLE>";
  echo "</FORM>";
  echo fin_tableau($html);
  exit;
}
// Procï¿½dure de modification d'un rï¿½fï¿½rentiel ou d'un ï¿½lï¿½ment d'un rï¿½fï¿½rentiel
if ($modifier == 1 && !$inserer)
{
  $ajouter = 0;
  $sql_modif = mysql_query("select * from referentiel where ref_cdn='$num'");
  $res_sql = mysql_num_rows($sql_modif);
  $i = 0;
  while ($i < $res_sql)
  {
    $categ_ajout = clean_text(mysql_result($sql_modif, $i, "ref_nomabrege_lb"));
    $par = mysql_result($sql_modif, $i, "ref_parent_no");
    $nom_pere = clean_text(mysql_result($sql_modif, $i, "ref_nomparent_lb"));
    $denomin = clean_text(mysql_result($sql_modif, $i, "ref_denom_lb"));
    $diplo = clean_text(mysql_result($sql_modif, $i, "ref_nom_lb"));
    $nival = mysql_result($sql_modif, $i, "ref_niv_no");
    $desc = clean_text(mysql_result($sql_modif, $i, "ref_desc_cmt"));
    $aut = mysql_result($sql_modif, $i, "ref_auteur_lb");
    $dom = clean_text(mysql_result($sql_modif, $i, "ref_dom_lb"));
    $i++;
  }
  echo "<FORM NAME='form1' ACTION=\"referenciel.php?flg=$flg&modifier=1&inserer=1&parente=$par&lien_sous_cat=1\" METHOD='POST' target='main'>";
  echo "<INPUT TYPE='HIDDEN' NAME='num' VALUE=$num>";
  echo "<INPUT TYPE='HIDDEN' NAME='parente' VALUE=$par>";
  echo "<INPUT TYPE='HIDDEN' NAME='nom_pere' VALUE=\"$nom_pere\">";
  echo "<INPUT TYPE='HIDDEN' NAME='denomin' VALUE=\"$denomin\">";
  echo "<INPUT TYPE='HIDDEN' NAME='diplo' VALUE=\"$diplo\">";
  echo "<INPUT TYPE='HIDDEN' NAME='pub' VALUE='local'>";
  echo "<INPUT TYPE='HIDDEN' NAME='niv' VALUE='$nival'>";
  echo "<INPUT TYPE='HIDDEN' NAME='abrege'  VALUE=\"$categ_ajout\">";
  echo "<center><TABLE>";
  echo "<TR><TD> Nom abrégé</TD>";
  echo "<TD><INPUT TYPE='text' class='INPUT' name='new_nom' size='100' Value=\"$categ_ajout\">";
  echo "</TD></TR>";
  echo "<TR><TD> $m_ref_obj \"$denomin\"</TD>";
  echo "<TD><TEXTAREA class='TEXTAREA' name=desc cols=120 rows=8>$desc</textarea>";
  echo "</TD></TR>";
  echo "<TR><TD>$m_ref_dom </TD><TD>";
  $param = "$dom";
  $nbdom = mysql_num_rows(mysql_query("select ref_dom_lb from referentiel where ref_dom_lb !=\"\" and ref_dom_lb != '-1' GROUP BY ref_dom_lb"));
  if ($nbdom > 0)
      Ascenseur("dom", "select ref_dom_lb from referentiel where ref_dom_lb !=\"\" and ref_dom_lb != '-1' GROUP BY ref_dom_lb", $connect, $param);
  echo'<INPUT TYPE="CHECKBOX"  name="ajoutdom" align="middle"   title='.$msgRefAjtDom.'>';
  echo "<INPUT TYPE='text' class='INPUT' name='domnew' size='60'>";
  echo "</TD></TR>";

  echo "<TR><TD>$mrc_niv</TD><TD>
             <SELECT  name='niv' size='1'>
                 <OPTION selected>$nival</OPTION>
                 <OPTION>$mrc_6 </OPTION>
                 <OPTION>$mrc_5 </OPTION>
                 <OPTION>$mrc_4 </OPTION>
                 <OPTION>$mrc_3 </OPTION>
                 <OPTION>$mrc_2 </OPTION>
                 <OPTION>$mrc_1 </OPTION>
             </SELECT></TD></TR>";
  echo boutret(1, 0);
  echo "</TD><TD align='left'><A HREF=\"javascript:document.form1.submit();\" onmouseover=\"img1.src='images/fiche_identite/boutvalidb.gif';return true;\" onmouseout=\"img1.src='images/fiche_identite/boutvalid.gif'\">" . "<IMG NAME=\"img1\" SRC=\"images/fiche_identite/boutvalid.gif\" BORDER='0' onLoad=\"tempImg=new Image(0,0); tempImg.src='images/fiche_identite/boutvalidb.gif'\"></A>";
  echo "</TD></TR></TABLE>";
  echo "</FORM>";
  echo fin_tableau($html);
  exit;
}
// Procï¿½dure de suppression d'un ï¿½lï¿½ment de rï¿½fï¿½rentiel
if ($supprimer == 1 && !$suppression)
{
  $sql = mysql_query("select * from referentiel where ref_parent_no='$num'");
  $res_sql = mysql_num_rows($sql);
  if ($res_sql > 0)
  {
    echo "$m_ref_nosup1<BR><BR>";
    echo "$m_ref_nosup2<BR><BR>";
    echo fin_tableau($html);
    exit;
  }
  echo "<P><CENTER><FONT SIZE='2'> $m_ref_supconf</FONT><BR>";
  echo "<FORM  NAME='form1' ACTION=\"referenciel.php?flg=$flg&parente=$parente\" METHOD='POST' target='main'>";
  echo "<INPUT TYPE='HIDDEN' NAME='supprimer' VALUE='1'>";
  echo "<INPUT TYPE='HIDDEN' NAME='suppression' VALUE='1'>";
  echo "<INPUT TYPE='HIDDEN' NAME='lien_sous_cat' VALUE='1'>";
  echo "<INPUT TYPE='HIDDEN' NAME='num' VALUE= '$num'>";
  echo "<A HREF=\"javascript:history.back();\" onmouseover=\"img_annonce.src='images/fiche_identite/boutretourb.gif';return true;\" onmouseout=\"img_annonce.src='images/fiche_identite/boutretour.gif'\">";
  echo "<IMG NAME=\"img_annonce\" SRC=\"images/fiche_identite/boutretour.gif\" BORDER='0' onLoad=\"tempImg=new Image(0,0); tempImg.src='images/fiche_identite/boutretourb.gif'\"></A>";
  echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<A HREF=\"javascript:document.form1.submit();\" onmouseover=\"img1.src='images/fiche_identite/boutvalidb.gif';return true;\" onmouseout=\"img1.src='images/fiche_identite/boutvalid.gif'\">" . "<IMG NAME=\"img1\" SRC=\"images/fiche_identite/boutvalid.gif\" BORDER='0' onLoad=\"tempImg=new Image(0,0); tempImg.src='images/fiche_identite/boutvalidb.gif'\"></A>";
  echo "<P></FORM></CENTER>";
  echo fin_tableau($html);
  exit;
}
// Gestion de la mise ï¿½ plat d'une table rï¿½fï¿½rentiel ï¿½ partir d'un ï¿½lï¿½ment de ce rï¿½fï¿½rentiel
if ($table == 1 & !$lien_sous_cat)
{
  echo "<TABLE border=0><TR><TD valign='top' colspan=2>";
  //Affichage de l'arborescence supï¿½rieure
  echo "<hr size=4 width=780 align=left >";
  echo "<CENTER><B>$mref_map_tab</B></CENTER></TD></TR><TR><TD valign='top' style='text-align:left;'>";
  $pointeur = $parente;
  $responsable = array ();
  $sous_c = array ();
  $desc = array ();
  $dom = array ();
  $nom_r = array ();
  $nom_pere = array ();
  $parbis = array ();
  $par1 = array ();
  $obj = array ();
  $pr = 0;
  while ($pointeur != 0)
  {
    $ptr = 0;
    $resultat = mysql_query("select * from referentiel where ref_cdn=$pointeur");
    $nbr_result = mysql_num_rows($resultat);
    $responsable[$pr] = mysql_result($resultat, $ptr, "ref_auteur_lb");
    $nom_pere[$pr] = clean_text(mysql_result($resultat, $ptr, "ref_nomparent_lb"));
    $objt[$pr] = clean_text(mysql_result($resultat, $ptr, "ref_denom_lb"));
    $desc[$pr] = clean_text(mysql_result($resultat, $ptr, "ref_desc_cmt"));
    $dom[$pr] = clean_text(mysql_result($resultat, $ptr, "ref_dom_lb"));
    $sous_c[$pr] = clean_text(mysql_result($resultat, $ptr, "ref_nomabrege_lb"));
    $nom_r[$pr] = clean_text(mysql_result($resultat, $ptr, "ref_nom_lb"));
    $parbis[$pr] = mysql_result($resultat, $ptr, "ref_parent_no");
    $par1[$pr] = mysql_result($resultat, $ptr, "ref_cdn");
    $pointeur = $parbis[$pr];
    $ptr++;
    $pr++;
  }
  $pr--;
  while ($pr > -1)
  {
    $chang_haut = $pr +1;
    $chang_bas = $pr -1;
    echo "<UL><TABLE border=0><TR><TD width=100% valign='top'><LI TYPE=\"square\" style='text-align:left;'>";
    $comptage_req = mysql_query("SELECT ref_cdn from referentiel WHERE ref_parent_no='".$par1[$pr]."'");
    $comptage = mysql_num_rows($comptage_req);
    $lien = "referenciel.php?flg=$flg&kaler=$kaler&presc=$presc&objectif=$objt[$pr]&lien_sous_cat=1&parente=$par1[$pr]&cat=$cat&sous_cat=$sous_c[$pr]&niveaux=1&prem=$prem&modif_seqref=$modif_seqref&consult=$consult&modif_parcref=$modif_parcref&parcours=$parcours&ajout_seq=$ajout_seq&id_seq=$id_seq&nom=$nom&description=$description&id_parc=$id_parc&proprio=$proprio&refer=$refer&change_ref=$change_ref&nb_seq=$nb_seq&prescription=$prescription&inscription_app=$inscription_app&parc=$id_parc&miens_parc=$miens_parc";
    $lien = urlencode($lien);
    if ($comptage > 0)
      echo "&nbsp;&nbsp;<A href=\"trace.php?link=$lien\"> $sous_c[$pr] </A>";
    else
      echo "<B> $sous_c[$pr] </B>";
    if ($objt[$pr] == $param[3])
      echo "<SMALL>&nbsp; : $nom_r[$pr] </SMALL>";
    if ($desc[$pr] != "")
      echo "<SMALL><SMALL><BR>$desc[$pr]</SMALL></SMALL>";
    $p = $pr +1;
    $pp = $pr -1;
    // vï¿½rifie si un parcours existe dï¿½jï¿½ pour cet ï¿½lï¿½ment du rï¿½fï¿½rentiel
    if ($typ_user != "APPRENANT")
    {
      $sql_parcours = mysql_query("SELECT COUNT(*) from parcours WHERE parcours_referentiel_no='$par1[$pr]'");
      $res_parcours = mysql_result($sql_parcours,0);
      if ($res_parcours != 0)
        $parc_exist = 1;
      else
        $parc_exist = 0;
      if ($parc_exist == 1)
      {
        $num = $par1[$pr];
        echo "<TD style='text-align:left;'><IMG SRC=\"images/croixbleue.gif\" border='0'></TD>";
        echo "<TD style='text-align:left;'><FORM name=\"form$num\">";
        echo "<SELECT name=\"select$num\" onChange=javascript:appel_w(form$num.select$num.options[selectedIndex].value)>";
        $sql_parc = mysql_query("SELECT parcours_cdn,parcours_nom_lb from parcours WHERE parcours_referentiel_no='$par1[$pr]'");
        $nbr_parc_ref = mysql_num_rows($sql_parc);
        if ($nbr_parc_ref > 0)
        {
          echo "<OPTION selected>- - - - - - - -</OPTION>";
          $ip_p = 0;
          while ($ip_p < $nbr_parc_ref)
          {
            $control_parc = 1;
            $num_parc = mysql_result($sql_parc, $ip_p, "parcours_cdn");
            $nom_parc = mysql_result($sql_parc, $ip_p, "parcours_nom_lb");
            $droit_voir = GetDataField($connect, "select parcours_publique_on from parcours where parcours_cdn = '$num_parc'", "parcours_publique_on");
            $auteur_parc = GetDataField($connect, "select parcours_auteur_no from parcours where parcours_cdn = '$num_parc'", "parcours_auteur_no");
            $lien = "parcours.php?liste=1&consult=1&parcours=1&id_parc=$num_parc&refer=1&id_ref_parc=$num&parc=$num_parc";
            $lien = urlencode($lien);
            if ($droit_voir == 1 || $typ_user == "ADMINISTRATEUR" || ($droit_voir == 0 && $auteur_parc == $id_user))
              echo "<OPTION value='trace.php?link=$lien'>$nom_parc</OPTION>";
            else
              echo "<OPTION>$nom_parc &nbsp;(NA)</OPTION>";
            $ip_p++;
          }
          echo "</TD></SELECT></FORM>";
        }
      }
      else
        echo "<TD>&nbsp;</TD><TD>&nbsp;</TD>";
    }
    // Fin de la vï¿½rification d'existence de parcours
    // vï¿½rifie si une sï¿½quence existe dï¿½jï¿½ pour cet ï¿½lï¿½ment du rï¿½fï¿½rentiel
    $sql_sequence = mysql_query("SELECT COUNT(*) from sequence_referentiel WHERE seqref_referentiel_no='$par1[$pr]'");
    $res_sql_sequence = mysql_result($sql_sequence,0);
    if ($res_sql_sequence != 0)
      $seq_exist = 1;
    else
      $seq_exist = 0;
    if ($seq_exist == 1 && $parc_exist == 1)
      $comptnum = $par1[$pr] + 1000;
    elseif ($seq_exist == 1 && $parc_exist == 0) $comptnum = $par1[$pr];
    if ($seq_exist)
    {
      echo "<TD style='text-align:left;'><IMG SRC=\"images/croixrouge.gif\" border='0'></TD>";
      $sql_sequence = mysql_query("select seqref_seq_no from sequence_referentiel where seqref_referentiel_no='$par1[$pr]'");
      $guide = "seqref_seq_no";
      $sseq = mysql_num_rows($sql_sequence);
      $nbr_seq = 0;
      echo "<TD style='text-align:left;'><FORM name=\"form$comptnum\">";
      echo "<SELECT name=\"select$comptnum\" onChange=javascript:appel_w(form$comptnum.select$comptnum.options[selectedIndex].value)>";
      echo "<OPTION>- - - - - - - -</OPTION>";
      while ($nbr_seq < $sseq)
      {
        if ($id_parc == "")
          $acces = "_entree";
        $control_seq = 1;
        $num_sequence = mysql_result($sql_sequence, $nbr_seq, $guide);
        $numero_referentiel = GetDataField($connect, "select seqref_referentiel_no from sequence_referentiel where seqref_seq_no = '$num_sequence'", "seqref_referentiel_no");
        $titre_sequence = GetDataField($connect, "select seq_titre_lb from sequence where seq_cdn = '$num_sequence'", "seq_titre_lb");
        $droit_voir = GetDataField($connect, "select seq_publique_on from sequence where seq_cdn = '$num_sequence'", "seq_publique_on");
        $auteur_seq = GetDataField($connect, "select seq_auteur_no from sequence where seq_cdn = '$num_sequence'", "seq_auteur_no");
        $lien = "sequence$acces.php?liste=1&consult=1&id_seq=$num_sequence&refer=1&id_ref=$par1[$pr]&id_ref_seq=$par1[$pr]&parc=$id_parc&miens_parc=$miens_parc&id_ref_parc=$id_ref_parc";
        $lien = urlencode($lien);
        if ($droit_voir == 1 || $typ_user == "ADMINISTRATEUR" || ($droit_voir == 0 && $auteur_seq == $id_user))
          echo "<OPTION value='trace.php?link=$lien'>$titre_sequence</OPTION>";
        else
          echo "<OPTION>$titre_sequence &nbsp;(NA)</OPTION>";
        $nbr_seq++;
      }
      echo "</SELECT></TD></FORM>";
    }
    else
      echo "<TD>&nbsp;</TD><TD>&nbsp;</TD>";
    // Fin if $seq_exist// fin affichage des sequences
    // fin de vï¿½rification
    echo "</TR></TABLE></LI>";
    $pr--;
  }
  $pr++;
  $niveaux++;
  $sous_resultat_sql = mysql_query("SELECT * FROM referentiel  WHERE ref_parent_no='$parente' GROUP BY ref_nomabrege_lb,ref_dom_lb");
  $nbr_sous_result = mysql_num_rows($sous_resultat_sql);
  $i = 0;
  $parental1 = array ();
  $parental = array ();
  $desc = array ();
  $dom = array ();
  $nom_r = array ();
  $nom_pere = array ();
  $objt = array ();
  echo "<UL>";
  while ($i < $nbr_sous_result)
  {
    $desc[$i] = clean_text(mysql_result($sous_resultat_sql, $i, "ref_desc_cmt"));
    $dom[$i] = clean_text(mysql_result($sous_resultat_sql, $i, "ref_dom_lb"));
    $nom_pere[$i] = clean_text(mysql_result($sous_resultat_sql, $i, "ref_nomparent_lb"));
    $nom_r[$i] = clean_text(mysql_result($sous_resultat_sql, $i, "ref_nom_lb"));
    $sous_cat = clean_text(mysql_result($sous_resultat_sql, $i, "ref_nomabrege_lb"));
    $objt[$i] = clean_text(mysql_result($sous_resultat_sql, $i, "ref_denom_lb"));
    $parente1 = mysql_result($sous_resultat_sql, $i, "ref_cdn");
    $parente = mysql_result($sous_resultat_sql, $i, "ref_parent_no");
    $parental[$i] = $parente;
    $parental1[$i] = $parente1;
    echo "<TABLE><TR><TD valign='top' style='text-align:left;'><LI TYPE=\"disc\" style='text-align:left;'>";
    $p = $i -1;
    $pp = $i +1;
    $comptage_req = mysql_query("SELECT COUNT(*) from referentiel WHERE ref_parent_no='$parente1'");
    $comptage = mysql_result($comptage_req,0);
    if ($comptage > 0)
    {
      $lien = "referenciel.php?flg=$flg&kaler=$kaler&presc=$presc&objectif=$objt[$i]&lien_sous_cat=0&table=1&i=$i&sous_cat=$sous_cat&detail=0&parente=$parental1[$i]&niveaux=$niveaux&prem=$prem&modif_seqref=$modif_seqref&consult=$consult&modif_parcref=$modif_parcref&parcours=$parcours&ajout_seq=$ajout_seq&id_seq=$id_seq&nom=$nom&description=$description&id_parc=$id_parc&proprio=$proprio&refer=$refer&change_ref=$change_ref&nb_seq=$nb_seq&prescription=$prescription&inscription_app=$inscription_app&parc=$id_parc&miens_parc=$miens_parc&id_ref_parc=$id_ref_parc";
      $lien = urlencode($lien);
      echo "<A href=\"trace.php?link=$lien\" " . bulle($m_ref_der, "", "RIGHT", "ABOVE", 120) . "<IMG SRC=\"images/exclam.gif\" BORDER=0></A>";
      $lien = "referenciel.php?flg=$flg&kaler=$kaler&presc=$presc&objectif=$objt[$i]&lien_sous_cat=1&i=$i&sous_cat=$sous_cat&detail=1&parente=$parental1[$i]&niveaux=$niveaux&prem=$prem&modif_seqref=$modif_seqref&consult=$consult&modif_parcref=$modif_parcref&parcours=$parcours&ajout_seq=$ajout_seq&id_seq=$id_seq&nom=$nom&description=$description&id_parc=$id_parc&proprio=$proprio&refer=$refer&change_ref=$change_ref&nb_seq=$nb_seq&prescription=$prescription&inscription_app=$inscription_app&parc=$id_parc&miens_parc=$miens_parc&id_ref_parc=$id_ref_parc";
      $lien = urlencode($lien);
      echo "&nbsp;&nbsp;<A href=\"trace.php?link=$lien\">$sous_cat</A>";
    }
    else
      echo "<B> $sous_cat</B>";
    // vï¿½rifie si une sï¿½quence existe dï¿½jï¿½ pour cet ï¿½lï¿½ment du rï¿½fï¿½rentiel
    if ($typ_user != "APPRENANT")
    {
      $sql_parcours = mysql_query("SELECT COUNT(*) from parcours WHERE parcours_referentiel_no='$parente1'");
      $res_parcours = mysql_result($sql_parcours,0);
      if ($res_parcours != 0)
        $parc_exist = 1;
      else
        $parc_exist = 0;
      if ($parc_exist == 1)
      {
        $num = $parente1;
        echo "</TD><TD style='text-align:left;'><IMG SRC=\"images/croixbleue.gif\" border='0'></TD>";
        echo "<TD style='text-align:left;'><FORM name=\"form$num\">";
        echo "<SELECT name=\"select$num\" onChange=javascript:appel_w(form$num.select$num.options[selectedIndex].value)>";
        $sql_parc = mysql_query("SELECT parcours_cdn,parcours_nom_lb from parcours WHERE parcours_referentiel_no='$parente1'");
        $nbr_parc_ref = mysql_num_rows($sql_parc);
        if ($nbr_parc_ref > 0)
        {
          echo "<OPTION selected>- - - - - - - -</OPTION>";
          $ip_p = 0;
          while ($ip_p < $nbr_parc_ref)
          {
            $control_parc = 1;
            $num_parc = mysql_result($sql_parc, $ip_p, "parcours_cdn");
            $nom_parc = mysql_result($sql_parc, $ip_p, "parcours_nom_lb");
            $droit_voir = GetDataField($connect, "select parcours_publique_on from parcours where parcours_cdn = '$num_parc'", "parcours_publique_on");
            $auteur_parc = GetDataField($connect, "select parcours_auteur_no from parcours where parcours_cdn = '$num_parc'", "parcours_auteur_no");
            $lien = "parcours.php?liste=1&consult=1&parcours=1&id_parc=$num_parc&refer=1&id_ref_parc=$num&parc=$num_parc";
            $lien = urlencode($lien);
            if ($droit_voir == 1 || $typ_user == "ADMINISTRATEUR" || ($droit_voir == 0 && $auteur_parc == $id_user))
              echo "<OPTION value='trace.php?link=$lien'>$nom_parc</OPTION>";
            else
              echo "<OPTION>$nom_parc &nbsp;(NA)</OPTION>";
            $ip_p++;
          }
          echo "</TD></SELECT></FORM>";
        }
      }
      else
        echo "<TD>&nbsp;</TD><TD>&nbsp;</TD>";
    }
    // Fin de la vï¿½rification d'existence de parcours
    // vï¿½rifie si une sï¿½quence existe dï¿½jï¿½ pour cet ï¿½lï¿½ment du rï¿½fï¿½rentiel
    $sql_sequence = mysql_query("SELECT COUNT(*) from sequence_referentiel WHERE seqref_referentiel_no='$parente1'");
    $res_sql_sequence = mysql_result($sql_sequence,0);
    if ($res_sql_sequence != 0)
      $seq_exist = 1;
    else
      $seq_exist = 0;
    if ($seq_exist == 1 && $parc_exist == 1)
      $comptnum = $par1[$pr] + 1000;
    elseif ($seq_exist == 1 && $parc_exist == 0) $comptnum = $parente1;
    if ($seq_exist)
    {
      echo "<TD style='text-align:left;'><IMG SRC=\"images/croixrouge.gif\" border='0'></TD>";
      $sql_sequence = mysql_query("select seqref_seq_no from sequence_referentiel where seqref_referentiel_no='$parente1'");
      $guide = "seqref_seq_no";
      $sseq = mysql_num_rows($sql_sequence);
      $nbr_seq = 0;
      echo "<TD style='text-align:left;'><FORM name=\"form$comptnum\">";
      echo "<SELECT name=\"select$comptnum\" onChange=javascript:appel_w(form$comptnum.select$comptnum.options[selectedIndex].value)>";
      echo "<OPTION>- - - - - - - -</OPTION>";
      while ($nbr_seq < $sseq)
      {
        if ($id_parc == "")
          $acces = "_entree";
        $control_seq = 1;
        $num_sequence = mysql_result($sql_sequence, $nbr_seq, $guide);
        $numero_referentiel = GetDataField($connect, "select seqref_referentiel_no from sequence_referentiel where seqref_seq_no = '$num_sequence'", "seqref_referentiel_no");
        $titre_sequence = GetDataField($connect, "select seq_titre_lb from sequence where seq_cdn = '$num_sequence'", "seq_titre_lb");
        $droit_voir = GetDataField($connect, "select seq_publique_on from sequence where seq_cdn = '$num_sequence'", "seq_publique_on");
        $auteur_seq = GetDataField($connect, "select seq_auteur_no from sequence where seq_cdn = '$num_sequence'", "seq_auteur_no");
        $lien = "sequence$acces.php?liste=1&consult=1&id_seq=$num_sequence&refer=1&id_ref=$par1[$pr]&id_ref_seq=$par1[$pr]&parc=$id_parc&id_ref_parc=$id_ref_parc";
        $lien = urlencode($lien);
        if ($droit_voir == 1 || $typ_user == "ADMINISTRATEUR" || ($droit_voir == 0 && $auteur_seq == $id_user))
          echo "<OPTION value='trace.php?link=$lien'>$titre_sequence</OPTION>";
        else
          echo "<OPTION>$titre_sequence &nbsp;(NA)</OPTION>";
        $nbr_seq++;
      }
      echo "</SELECT></TD></FORM>";
    }
    else
      echo "<TD>&nbsp;</TD><TD>&nbsp;</TD>";
    // Fin if $seq_exist// fin affichage des sequences
    // fin de vï¿½rification
    echo "</TR></TABLE></li>";
    if ($desc[$i] != "")
      echo "<SMALL><SMALL>&nbsp;$dom[$i]-->&nbsp;$desc[$i]</SMALL></SMALL>";
    else
    {
      if ($parental1[$i] != 0)
        echo "<SMALL><SMALL>$dom[$pp]&nbsp; &nbsp; <b><u>$m_ref_dop :</u></b>$nom_r[$i]</SMALL></SMALL>";
      if ($parental1[$i] == 0)
        echo "<SMALL><SMALL><b><u>$m_ref_dip :</u></b>$nom_r[$i]</SMALL></SMALL>";
    }
    echo "</LI>";
    $i++;
  }
  echo "</UL>";
}
echo "</TR></TABLE>";
echo "</TD></TR></TABLE></TD></TR></TABLE>";
echo "</BODY></HTML>";
?>
