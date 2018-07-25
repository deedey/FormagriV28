<?php
if (!isset($_SESSION)) session_start();
include ("include/UrlParam2PhpVar.inc.php");
if (!isset($_SESSION['id_user']) || $_SESSION['id_user'] == "")
{
  exit();
}
require 'fonction.inc.php';
require 'graphique/admin.inc.php';
require "lang$lg.inc.php";
require("class/import_aicc.php");
require 'class/class_module.php';
require 'class/Class_Rss.php';
require("langues/xml.inc.php");
require 'fonction_html.inc.php';
dbConnect();
$date_dujour = date ("Y-m-d");
if (isset($prov) && $prov == "seq")
  $incl = "liste_seq.inc.php";
else
  $incl = "liste_parc.inc.php";
include ('style.inc.php');
//error_reporting (0);
if (isset($_FILES['file']['tmp_name']) && strstr(strtolower($_FILES['file']['name']),".zip") && isset($zip) && $zip == 1)
{
    require('class/pclzip.inc.php');
    $poids = "10 Mo";
    if ($_FILES['file']['name'] == "")
       $message = strtolower($mess_fichier_no);
    elseif(!is_file($_FILES['file']['tmp_name']))
       $message = $mess_fic_dep_lim." : ".ini_get('upload_max_filesize');
    if ($message != "")
    {
       entete_concept("$incl",$mess_imp_sco);
       $affiche = affiche_contenu($message);
       $affiche .= boutret(1,0);
       $affiche .= fin_tableau($html);
       echo $affiche;
     exit;
    }
    $dir="ressources/".$login."_".$id_user."/ressources/";
    list($extension,$nom_rep) = getextension($_FILES['file']['name']);
    $nom = $_FILES['file']['name'];
    $nom_final= "Ressources_Aicc";
    $handle=opendir($dir);
    $i = 0;
    while ($fiche = readdir($handle))
    {
       if ($fiche == $nom_final)
         $i++;
    }
    if ($i == 0)
    {
       $create_rep = $dir."Ressources_Aicc";
       mkdir($create_rep,0775);
       chmod($create_rep,0775);
    }
       $dir = "ressources/".$login."_".$id_user."/ressources/Ressources_Aicc/";
       $fichier = $_FILES['file']['tmp_name'];
       $archive = new PclZip($_FILES['file']['tmp_name']);
       if (($list = $archive->listContent()) == 0)
       {
          die("Error : ".$archive->errorInfo(true));
       }
       $affiche_sco.= "<B>$mess_sco_imp : Ressources_Aicc/$nom_rep</B><P>";
       $Contenus = '';
       $corrompu = 0;
       $accord = 0;
       $dire = '';
       $message_no = '';
       for ($i=0; $i<sizeof($list); $i++)
       {
        list($extension,$nom) = getextension($list[$i]["filename"]);
        if (in_array(strtolower($extension), array("exe","sh","py", "ida","idq","asp","cer","cdx","asa","idc","cfm","dbm","php","php4","inc","shtml","cgi")))
        {
           $corrompu++;
           $Contenus .= $list[$i]["filename"]." : extension du fichier non autorisée.<br /> ";
        }
        $affiche_sco.= "Fichier ".($i+1)." = ".$nom_rep."/".$list[$i]["filename"]."<BR>";
        if (strstr(strtoupper($list[$i]["filename"]),".CRS")){
           $accord++;
           $crs_file = $dir.$nom_rep."/".$list[$i]["filename"];
        }else
           $dire .= " \"CRS\" ";
        if (strstr(strtoupper($list[$i]["filename"]),".DES")){
           $accord++;
           $des_file = $dir.$nom_rep."/".$list[$i]["filename"];

        }else
           $dire .= " \"DES\" ";
        if (strstr(strtoupper($list[$i]["filename"]),".CST")){
           $accord++;
           $cst_file = $dir.$nom_rep."/".$list[$i]["filename"];
        }else
           $dire .= " \"CST\" ";
        if (strstr(strtoupper($list[$i]["filename"]),".AU"))
        {
           $accord++;
           $au_file = $dir.$nom_rep."/".$list[$i]["filename"];
       }
       else
           $dire .= " \"AU\" ";
      }
      $dest_file = $dir.$_FILES['file']['name'];
      if ($accord < 4 || !isset($accord) || (isset($corrompu) && $corrompu > 0))
      {
         $message_no = "Fichier(s) introuvable(s) : $dire<br />".$Contenus;
      }
      if (file_exists($dir.$nom))
         $message_no .= $mess_zip_exist;
      if ($accord == 4 && ($corrompu == 0 || !isset($corrompu)))
      {
       if (!file_exists($dir.$nom_rep))
       {
         mkdir($dir.$nom_rep,0775);
         chmod ($dir.$nom_rep,0775);
       }
       else
       {
         $nom_rep .="_$date_dujour";
         mkdir($dir.$nom_rep,0775);
         chmod ($dir.$nom_rep,0775);
       }
       $lerepertoire = $dir."$nom_rep/";
       $list = $archive->extract(PCLZIP_OPT_PATH,$lerepertoire,
                               PCLZIP_OPT_REMOVE_PATH,$dir,
                               PCLZIP_OPT_SET_CHMOD, 0775);
       $copier=move_uploaded_file($_FILES['file']['tmp_name'] , $dest_file);
      }
      else
      {
       entete_concept("$incl",$mess_imp_sco);
       $affiche = affiche_contenu ($message_no);
       $affiche .= boutret(1,0);
       $affiche .=fin_tableau($html);
       echo $affiche;
       $file = "";
      exit;
      }
      if (isset($accord) && $accord == 4 && ((isset($corrompu) && $corrompu == 0) || !isset($corrompu)))
      {
        $donnees = parse_aicc :: parse_crs($crs_file);
        $tab_crs = explode('|',$donnees);
        $nb_tab_crs = count($tab_crs);
        for ($i = 0;$i < $nb_tab_crs; $i++)
       {
          $tab2_crs[$i] = explode('=',$tab_crs[$i]);
          switch (strtolower($tab2_crs[$i][0]))
          {
             case "course_creator" : $sequence['auteur'][0] = str_replace("\"","'",trim($tab2_crs[$i][1]));$auteur = $sequence['auteur'][0];break;
             case "course_title" : $sequence['titre'][0] =  str_replace("\"","'",trim($tab2_crs[$i][1]));$titre = $sequence['titre'][0];break;
             case "description" : $sequence['description'][0] =  str_replace("\"","'",trim($tab2_crs[$i][1]));$description = $sequence['description'][0];break;
             case "max_fields_cst" : $nb_cst = $tab2_crs[$i][1];break;
             case "total_aus" : $nb_au = $tab2_crs[$i][1];break;
             case "total_blocks" : $nb_blocks = $tab2_crs[$i][1];break;
             case "level" : $niveau = $tab2_crs[$i][1];break;
          }
        }
        $donnees_des_file = parse_aicc :: modifie_aicc($des_file);
        if ($donnees_des_file == FALSE)
        {
         $faux++;
         $afficher_faux .= "<LI>".$des_file." :<BR>".$mess_fic_corrompu."</LI>";
        }
        $nb_tab_des = parse_aicc :: compte_des($des_file);
//      echo $des_file."<BR>";
        $donnees_cst_file = parse_aicc :: modifie_aicc($cst_file);
        if ($donnees_cst_file == FALSE)
        {
         $faux++;
         $afficher_faux .= "<LI>".$cst_file." :<BR>".$mess_fic_corrompu."</LI>";
        }
//      echo $cst_file."<BR>";
      $donnees_au_file = parse_aicc :: modifie_aicc($au_file);
      if ($donnees_au_file == FALSE)
      {
         $faux++;
         $afficher_faux .= "<LI>".$au_file." :<BR>".$mess_fic_corrompu."</LI>";
      }
//      echo $au_file."<BR>";
      if (isset($faux) && $faux > 0)
      {
         entete_concept("$incl",$mess_fic_corrompu);
         $affiche = affiche_contenu($afficher_faux);
         $affiche .= boutret(1,0);
         $affiche .= fin_tableau($html);
         echo $affiche;
         exit;
      }
      $donnees_cst = parse_aicc :: parse_cst($cst_file,$nb_blocks,$nb_au);
      //tableau des DES
      $handle = fopen($des_file, "r");
      $compteur_des = 0;
      while (!feof($handle)) 
      {
        $ligne = fgets($handle, 4096);
        if (strlen(trim($ligne)) == "")
             continue;
        $tab_des = explode('",',$ligne);
        $nb_tab_des = count($tab_des);
        if ($compteur_des == 0)
           $nbr_comp_des = $nb_tab_des;
        if ($nb_tab_des != $nbr_comp_des && $compteur_des > 0 && strlen($ligne) > 2)
        {
           entete_concept("$incl",$mess_fic_corrompu);
           $afficher_faux = $des_file." :<BR>".$mess_fic_corrompu;
           $affiche = affiche_contenu($afficher_faux);
           $affiche .= boutret(1,0);
           $affiche .= fin_tableau($html);
           echo $affiche;
           exit;
        }
        if ($nb_tab_des == $nbr_comp_des && strlen($ligne) > 2)
        {
           for ($i=0;$i<$nb_tab_des;$i++)
           {
               $data_des[$compteur_des][$i] = str_replace('"','',$tab_des[$i]);
           }
        }
        $compteur_des++;
      }
      fclose ($handle);
      //tableau des AU
      $handle = fopen($au_file, "r");
      $compteur_au = 0;
      while (!feof($handle)) 
      {
        $ligne = fgets($handle, 4096);
        if (strlen(trim($ligne)) == "")
            continue;
        $tab_au = explode('",',$ligne);
        $nb_tab_au = count($tab_au);
        if ($compteur_au == 0)
          $nbr_comp_au = $nb_tab_au;
        if ($nb_tab_au != $nbr_comp_au && $compteur_au > 0 && strlen($ligne) > 2)
        {
           entete_concept("$incl",$mess_fic_corrompu);
           $afficher_faux = $au_file." :<BR>".$mess_fic_corrompu;
           $affiche = affiche_contenu($afficher_faux);
           $affiche .= boutret(1,0);
           $affiche .= fin_tableau($html);
           echo $affiche;
           exit;
        }
        if ($nb_tab_au == $nbr_comp_au && strlen($ligne) > 2)
        {
           for ($i=0;$i<$nb_tab_au;$i++)
           {
               $data_au[$compteur_au][$i] = str_replace('"','',$tab_au[$i]);
           }
        }
        $compteur_au++;
      }
      fclose ($handle);
      //Comparaison et récupération des données
      if (!strstr($donnees_cst,'|'))
      {
         $blk[0] = $donnees_cst;
         $n_blks = 1;
      }
      else
      {
         $blk = explode('|',$donnees_cst);
         $n_blks = count($blk);
      }
      $mod['id']=array();
      $mod['id_au']=array();
      $mod['titre']=array();
      $mod['description']=array();
      $mod['type']=array();
      $mod['max_time_allowed']=array();
      $mod['file_name']=array();
      $mod['max_score']=array();
      $mod['mastery_score']=array();
      $mod['time_limit_action']=array();
      $mod['web_launch']=array();
      $mod['parent']=array();
      $mod['niveau']=array();
      $nb_B = 0;
      $nb_AU = 0;
      $duree_seq = '';
      for ($i = 0;$i < $n_blks; $i++)
      {
          if (!strstr($blk[$i],'*'))
          {
             for ($j = 1;$j < $compteur_des;$j++)
             {
                if (trim($blk[$i]) == $data_des[$j][0])
                {
                   for ($x = 0;$x < $nb_tab_des;$x++)
                   {
                      if (strstr(strtolower($data_des[0][$x]),'system_id'))
                         $mod['id'][$j] = $data_des[$j][$x];
                      if (strstr(strtolower($data_des[0][$x]),'title'))
                         $mod['titre'][$j] = $data_des[$j][$x];
                      if (strstr(strtolower($data_des[0][$x]),'description'))
                         $mod['description'][$j] = $data_des[$j][$x];
                   }//for ($x = 1;$x < 5;$x++){
                }
                for ($jj = 0;$jj < $compteur_au;$jj++)
                {
                   if (trim($blk[$i]) == $data_au[$jj][0])
                   {
                      $mod['id_au'][$jj] = $data_au[$jj][0];
                      if ( isset($mod['id_au'][$jj]) && isset($mod['id'][$j]) && $mod['id_au'][$jj] == $mod['id'][$j])
                      {
                         for ($x = 1;$x < 13;$x++)
                         {
                            if (isset($data_au[0][$x]) && strstr(strtolower($data_au[0][$x]),'type'))
                               $mod['type'][$j] = $data_au[$jj][$x];
                            if (isset($data_au[0][$x]) && strstr(strtolower($data_au[0][$x]),'max_time_allowed'))
                               $mod['max_time_allowed'][$j] = $data_au[$jj][$x];
                            if (isset($data_au[0][$x]) && strstr(strtolower($data_au[0][$x]),'file_name'))
                               $mod['file_name'][$j] = $data_au[$jj][$x];
                            if (isset($data_au[0][$x]) && strstr(strtolower($data_au[0][$x]),'max_score'))
                               $mod['max_score'][$j] = $data_au[$jj][$x];
                            if (isset($data_au[0][$x]) && strstr(strtolower($data_au[0][$x]),'mastery_score'))
                               $mod['mastery_score'][$j] = $data_au[$jj][$x];
                            if (isset($data_au[0][$x]) && strstr(strtolower($data_au[0][$x]),'time_limit_action'))
                               $mod['time_limit_action'][$j] = $data_au[$jj][$x];
                            if (isset($data_au[0][$x]) && strstr(strtolower($data_au[0][$x]),'web_launch'))
                               $mod['web_launch'][$j] = $data_au[$jj][$x];
                         }//for ($x = 1;$x < 13;$x++){
                         $mod['parent'][$j] = "";
                         $mod['niveau'][$j] = 1;
                      }//if ($mod['id_au'][$jj] == $mod['id'][$j]){
                    }//if ($blk[$i] == $data_au[$jj][0]){
                 }//for ($jj = 0;$jj < $compteur_au;$jj++){
             }//for ($j = 0;$j < $compteur_des;$j++){
          }elseif (strstr($blk[$i],'*'))
          {
             $partie = explode('*',$blk[$i]);
             if (strstr($blk[$i],'-'))
             {
                $item_au = explode('-',$partie[1]);
                $nb_item_au = count($item_au);
                for ($j = 1;$j < $compteur_des;$j++)
                {
                   for ($x = 0;$x < $nb_tab_des;$x++){
                        if (strstr(strtolower($data_des[0][$x]),'system_id'))
                           $mod['id'][$j] = $data_des[$j][$x];
                        if (strstr(strtolower($data_des[0][$x]),'title'))
                           $mod['titre'][$j] = $data_des[$j][$x];
                        if (strstr(strtolower($data_des[0][$x]),'description'))
                           $mod['description'][$j] = $data_des[$j][$x];
                   }//for ($x = 1;$x < 5;$x++){
                   for ($ii = 0;$ii < $nb_item_au;$ii++)
                   {
                      if (trim($item_au[$ii]) == $data_des[$j][0])
                      {
                         for ($x = 0;$x < $nb_tab_des;$x++)
                         {
                            if (strstr(strtolower($data_des[0][$x]),'system_id'))
                                $mod['id'][$j] = $data_des[$j][$x];
                            if (strstr(strtolower($data_des[0][$x]),'title'))
                                $mod['titre'][$j] = $data_des[$j][$x];
                            if (strstr(strtolower($data_des[0][$x]),'description'))
                                $mod['description'][$j] = $data_des[$j][$x];
                         }//for ($x = 1;$x < 5;$x++){
                      }
                   }
                   for ($jj = 0;$jj < $compteur_au;$jj++)
                   {
                      for ($ii = 0;$ii < $nb_item_au;$ii++)
                      {
                          if (trim($item_au[$ii]) == $data_au[$jj][0])
                          {
                             $mod['id_au'][$jj] = $data_au[$jj][0];
                             if ($mod['id_au'][$jj] == $mod['id'][$j])
                             {
                                for ($x = 1;$x < 13;$x++)
                                {
                                  if (isset($data_au[0][$x]))
                                  {
                                    if ( strstr(strtolower($data_au[0][$x]),'type'))
                                      $mod['type'][$j] = $data_au[$jj][$x];
                                    if (strstr(strtolower($data_au[0][$x]),'max_time_allowed'))
                                      $mod['max_time_allowed'][$j] = $data_au[$jj][$x];
                                    if (strstr(strtolower($data_au[0][$x]),'file_name'))
                                      $mod['file_name'][$j] = $data_au[$jj][$x];
                                    if (strstr(strtolower($data_au[0][$x]),'max_score'))
                                      $mod['max_score'][$j] = $data_au[$jj][$x];
                                    if (strstr(strtolower($data_au[0][$x]),'mastery_score'))
                                      $mod['mastery_score'][$j] = $data_au[$jj][$x];
                                    if (strstr(strtolower($data_au[0][$x]),'time_limit_action'))
                                      $mod['time_limit_action'][$j] = $data_au[$jj][$x];
                                    if (strstr(strtolower($data_au[0][$x]),'web_launch'))
                                      $mod['web_launch'][$j] = $data_au[$jj][$x];
                                  }
                                }//for ($x = 1;$x < 13;$x++){
                                $mod['parent'][$j] = $partie[0];
                                $mod['niveau'][$j] = 2;
                             }//if ($mod['id_au'][$jj] == $mod['id'][$j]){
                          }//if (trim($item_au[$ii]) == $data_au[$jj][0]){
                      }//for ($ii = 0;$ii < $nb_item_au;$ii++){
                   }//for ($jj = 0;$jj < $compteur_au;$jj++){
                }//for ($j = 0;$j < $compteur_des;$j++){
             }elseif (!strstr($blk[$i],'-'))
             {//if (strstr($blk[$i],'-')){
                for ($j = 1;$j < $compteur_des;$j++)
                {
                   if (trim($partie[0]) == $data_des[$j][0])
                   {
                      for ($x = 0;$x < $nb_tab_des;$x++)
                      {
                         if (strstr(strtolower($data_des[0][$x]),'system_id'))
                           $mod['id'][$j] = $data_des[$j][$x];
                         if (strstr(strtolower($data_des[0][$x]),'title'))
                           $mod['titre'][$j] = $data_des[$j][$x];
                         if (strstr(strtolower($data_des[0][$x]),'description'))
                           $mod['description'][$j] = $data_des[$j][$x];
                      }//for ($x = 1;$x < 5;$x++){
                   }
                }
                for ($j = 1;$j < $compteur_des;$j++)
                {
                   if (trim($partie[1]) == $data_des[$j][0])
                   {
                     for ($x = 0;$x < $nb_tab_des;$x++)
                     {
                        if (strstr(strtolower($data_des[0][$x]),'system_id'))
                          $mod['id'][$j] = $data_des[$j][$x];
                        if (strstr(strtolower($data_des[0][$x]),'title'))
                          $mod['titre'][$j] = $data_des[$j][$x];
                        if (strstr(strtolower($data_des[0][$x]),'description'))
                          $mod['description'][$j] = $data_des[$j][$x];
                     }//for ($x = 1;$x < 5;$x++){
                     $mod['niveau'][$j] = (strstr($mod['id'][$j],'B')) ? 1 : 2;
                   }
                   for ($jj = 0;$jj < $compteur_au;$jj++)
                   {
                      if (trim($partie[1]) == $data_au[$jj][0])
                      {
                         $mod['id_au'][$jj] = $data_au[$jj][0];
                         if ($mod['id_au'][$jj] != "" && $mod['id_au'][$jj] == $mod['id'][$j])
                         {
                           for ($x = 1;$x < 13;$x++)
                           {
                             if (isset($data_au[0][$x]))
                             {
                                if (strstr(strtolower($data_au[0][$x]),'type'))
                                $mod['type'][$j] = $data_au[$jj][$x];
                                if (strstr(strtolower($data_au[0][$x]),'max_time_allowed'))
                                $mod['max_time_allowed'][$j] = $data_au[$jj][$x];
                                if (strstr(strtolower($data_au[0][$x]),'file_name'))
                                $mod['file_name'][$j] = $data_au[$jj][$x];
                                if (strstr(strtolower($data_au[0][$x]),'max_score'))
                                $mod['max_score'][$j] = $data_au[$jj][$x];
                                if (strstr(strtolower($data_au[0][$x]),'mastery_score'))
                                $mod['mastery_score'][$j] = $data_au[$jj][$x];
                                if (strstr(strtolower($data_au[0][$x]),'time_limit_action'))
                                $mod['time_limit_action'][$j] = $data_au[$jj][$x];
                                if (strstr(strtolower($data_au[0][$x]),'web_launch'))
                                $mod['web_launch'][$j] = $data_au[$jj][$x];
                             }
                           }//for ($x = 1;$x < 13;$x++){
                           $mod['parent'][$j] = $partie[0];
                           $mod['niveau'][$j] = 2;
                         }//if ($mod['id_au'][$j] != ""){
                      }//if (trim($partie[1]) == $data_au[$j][0]){
                   }//for ($jj = 0;$jj < $compteur_au;$jj++){
                }//for ($j = 0;$j < $compteur_des;$j++){
             }//else de if(strstr($blk[$i],'-')){
          }//else de if (!strstr($blk[$i],'*')){
      }//for ($i = 0;$i < $n_blks; $i++){
    }//
    $duree_seq = 0;
    for ($j = 1;$j < $compteur_des;$j++)
    {
           if (isset($mod['id'][$j]) && strstr($mod['id'][$j],'A') && isset($mod['max_time_allowed'][$j]) && $mod['max_time_allowed'][$j] != '')
           {
               $temps = $mod['max_time_allowed'][$j];
               $new_temps = ($temps != '') ? aicc_modifie_time($temps) : '';
               if (strlen($new_temps) > 9)
               {
                  $temp = agrege_time($temps);
                  $duree_seq += $temp;
               }
               $mod['max_time_allowed'][$j] = $new_temps;
           }
        if (isset($mod['id'][$j]) && strstr($mod['id'][$j],'B'))
           $nb_B++;
        if (isset($mod['id'][$j]) && strstr($mod['id'][$j],'A'))
           $nb_AU++;
    }
// Insertion dans la base de données

    $id_new_seq = Donne_ID ($connect,"SELECT max(seq_cdn) from sequence");
    $insert_new_seq = mysql_query ("insert into sequence values ($id_new_seq,\"$titre\",\"$description\",\"$auteur\",'OUI','$duree_seq',$id_user,\"$date_dujour\",\"$date_dujour\",1,0,\"SCORM AICC\")");
    $supp_rss = rss :: ajout('sequence',$id_user,$id_new_seq);
    if ($id_parc > 0)
    {
//         $id_new_parcseq = Donne_ID ($connect,"SELECT max(seqparc_cdn) from sequence_parcours");
//         $new_ordre = mysql_result(mysql_query ("SELECT count(seqparc_cdn) from sequence_parcours where seqparc_parc_no = $id_parc"),0);
//         $new_ordre++;
//         $insert_parcseq = mysql_query("insert into sequence_parcours values('$id_new_parcseq','$id_new_seq','$id_parc','$new_ordre')");
    }else
      $id_parc = -1;
    $id_seqref = Donne_ID ($connect,"select max(seqref_cdn) from sequence_referentiel");
    $insert_ref_seq = mysql_query ("insert into sequence_referentiel values ($id_seqref,$id_new_seq,'0')");
    //affichage sequence
    $affiche_sco .= "<BR>Séquence AICC<BR>Titre : $titre<BR>Description : $description<BR>Type = SCORM AICC<BR>";
    if ($duree_seq > 0)
        $affiche_sco .= "Durée = $duree_seq'<BR>";
    $affiche_sco .= "Nombre d'Unités Assignables (AU -> activités liées) = $nb_AU<BR>";
    $affiche_sco .= "Nombre de blocks (Label non liés) = $nb_B<P>";
        // fin affichage sequence
    for ($j = 1;$j < $compteur_des;$j++)
    {
        $scormtype = '';
        $num_parent = '';
        $path = '';
        $temps = (isset($mod['max_time_allowed'][$j]) && $mod['max_time_allowed'][$j] != '' ) ? agrege_time($mod['max_time_allowed'][$j]) : '';
        if (isset($mod['id'][$j]) && strstr($mod['id'][$j],'A'))
           $path = $mod['file_name'][$j];
        if (isset($mod['id'][$j]) && strstr($mod['id'][$j],'A') && strstr($path,'http://'))
           $scormtype = 'AICC_HACP';
        elseif (isset($mod['id'][$j]) && strstr($mod['id'][$j],'A') && !strstr($path,'http://'))
        {
           if ($mod['file_name'][$j]{0} == "/")
              $path = $dir.$nom_rep.$path;
           else
              $path = $dir.$nom_rep."/".$path;
           $scormtype = 'AICC_API';
        }
        if (isset($mod['id'][$j]) && strstr($mod['id'][$j],'B'))
        {
           $nb_parent = $j;
           $scormtype = 'AICC_LABEL';
        }
        if (isset($mod['id'][$j]) && strstr($mod['id'][$j],'A') && $mod['niveau'][$j] > 1)
           $num_parent = $nb_parent;
        elseif((isset($mod['niveau'][$j]) && $mod['niveau'][$j] < 2) ||
               ((isset($mod['niveau'][$j]) && $mod['niveau'][$j] == '') || !isset($mod['niveau'][$j])))
           $mod['niveau'][$j] = 1;
        if ((isset($mod['time_limit_action'][$j]) && $mod['time_limit_action'][$j] == '' || !isset($mod['time_limit_action'][$j])))
           $mod['time_limit_action'][$j] = 'continue,no message';
        $id_new_mod = Donne_ID ($connect,"SELECT max(mod_cdn) from scorm_module");
        //$mod['niveau'][$j] = (isset($mod['description'][$j])) ? $mod['description'][$j] : '';
        if (isset($mod['id'][$j]) && strstr($mod['id'][$j],'A') && isset($mod['titre'][$j]) && $mod['titre'][$j] != '')
        {
            $insert_new_mod = mysql_query ("INSERT INTO scorm_module VALUES ($id_new_mod,$id_parc,$id_new_seq,\"".$mod['titre'][$j]."\",\"".
                                            trim($mod['description'][$j])."\",\"\",\"\",\"TRUE\",\"$temps\",\"".$mod['niveau'][$j]."\",".
                                            "\"$path\",\"".$mod['id'][$j]."\",'$j',\"".$mod['parent'][$j]."\",\"".$num_parent."\",\"$scormtype\",".
                                            "\"\",\"".$mod['max_time_allowed'][$j]."\",\"".$mod['time_limit_action'][$j]."\",\"".
                                    $mod['web_launch'][$j]."\",\"".$mod['mastery_score'][$j]."\")");
        }
        elseif (isset($mod['titre'][$j]) && $mod['titre'][$j] != "" && isset($mod['id'][$j]) && strstr($mod['id'][$j],'B'))
        {
            $insert_new_mod = mysql_query ("INSERT INTO scorm_module VALUES ($id_new_mod,$id_parc,$id_new_seq,\"".$mod['titre'][$j]."\",\"".
                                            trim($mod['description'][$j])."\",\"\",\"\",\"TRUE\",\"\",\"".$mod['niveau'][$j]."\",".
                                            "\"\",\"".$mod['id'][$j]."\",\"$j\",\"\",\"\",\"$scormtype\",\"\",\"\",\"\",\"\",\"\")");
        }
        if (isset($mod['id'][$j]) && $mod['id'][$j] !='')
        {
           $affiche_sco .= $mod['id'][$j]."  <BR>Type = $scormtype".
                           "<BR>Titre = ".$mod['titre'][$j].
                           "<BR>Description = ".trim($mod['description'][$j]);
           if (strstr($mod['id'][$j],'A'))
              $affiche_sco .= "<BR>Url = $path";
           $affiche_sco .= "<P>";
        }

    }
    if ($prov != "seq")
       $seq_insert_mod = add_seq_user($id_new_seq);
}
elseif((!isset($_FILES['file']['tmp_name']) || !strstr(strtolower($_FILES['file']['name']),".zip"))  && isset($zip) && $zip == 1)
{
    include ('style.inc.php');
    $poids = "4,5 Mo";
    if ($_FILES['file']['name'] == "")
       $message = $mess_fichier_no." <BR> &nbsp;&nbsp;et<BR> &nbsp;&nbsp;".strtolower($mess_fic_dep_lim)." $poids<BR>";
    elseif ($_FILES['file']['name'] != "")
       $message = "&nbsp;&nbsp;$mess_nozip";
    if ($message != "")
    {
       entete_concept("$incl",$mess_imp_sco);
       $affiche = affiche_contenu($message);
       $affiche .= boutret(1,0);
       $affiche .= fin_tableau($html);
       echo $affiche;
     exit;
    }
}
function getextension($file)
{
  $bouts = explode(".", $file);
  return array(array_pop($bouts), implode(".", $bouts));
}

entete_concept($incl,$mess_imp_sco);
if ($message != '')
   echo "<TR height='50'><TD valign= 'center' colspan='2' width='100%'><Font size='3'><B>$message</B></FONT></TD></TR>";
$affiche = affiche_contenu($affiche_sco);
//$affiche .= boutret(1,0);
$affiche .= fin_tableau($html);
echo $affiche;
?>