<?php
        require_once "langues/ress.inc.php";
        $date_media = date ("d-m-Y");
          $directory = $repertoire."/ressources/".$login."_".$id_user."/ressources";
          $list_file=array();
          if (!file_exists($directory."/Ressources_Media"))
          {
              mkdir($directory."/Ressources_Media",0775);
              chmod($directory."/Ressources_Media",0775);
          }
          $dir = "ressources/".$login."_".$id_user."/ressources/Ressources_Media";
          $list_file=explode('.',$_FILES['userfile']['name']);
          $letitre = $titre;
          $fichier_test = modif_nom($list_file[0]."_".time().".".$list_file[1]);
          $dest_file=$directory."/Ressources_Media/".$fichier_test;
          $handle=opendir($dir);
          $i_file=0;
          while ($file = readdir($handle))
          {
             if ($file == $fichier_test)
                $i_file++;
          }
          closedir($handle);
          $source_file=$_FILES['userfile']['tmp_name'];
          $copier= move_uploaded_file($source_file , $dest_file);
          $rl = $adresse_http."/".$dir."/".$fichier_test;
          //fin du test
          $la_cat = "Ressources Multimedia";
          $la_souscat = "Liaison vers consignes-Média";
          $requete= mysql_query("SELECT count(*) FROM ressource_new where ress_cat_lb = \"$la_cat\"");
          $nb_requete= mysql_result($requete,0);
          if ($nb_requete == 0)
          {
              $id_new_ress = Donne_ID ($connect, "select max(ress_cdn) from ressource_new");
              $sql_insere= mysql_query("INSERT INTO ressource_new (ress_cdn,ress_cat_lb,ress_typress_no,ress_create_dt,ress_ajout) VALUES ('$id_new_ress',\"$la_cat\",'0',\"$date_dujour\",'foad')");
              $id_new_ress2 = Donne_ID ($connect, "select max(ress_cdn) from ressource_new");
              $sql_insere= mysql_query("INSERT INTO ressource_new (ress_cdn,ress_cat_lb,ress_typress_no,ress_create_dt,ress_ajout) VALUES ('$id_new_ress2',\"$la_souscat\",'$id_new_ress',\"$date_dujour\",'foad')");
              $parente = $id_new_ress;
          }
          else
              $parente = GetDataField($connect,"SELECT ress_cdn FROM ressource_new WHERE ress_cat_lb = \"$la_cat\" AND ress_typress_no = 0 AND ress_titre =\"\"","ress_cdn");
          $id_new_ress = Donne_ID ($connect, "select max(ress_cdn) from ressource_new");
          $sql_insere= mysql_query("INSERT INTO ressource_new (ress_cdn,ress_cat_lb,ress_typress_no,ress_url_lb,ress_auteurs_cmt,ress_publique_on,ress_titre,ress_desc_cmt,ress_create_dt,ress_ajout,ress_public_no,ress_type,ress_support,ress_doublon,ress_niveau) VALUES ('$id_new_ress',\"$la_souscat\",'$parente',\"$rl\",\"Inconnu\",'NON',\"$letitre\",\"$mess_no_comment\",\"$date_dujour\",\"$login\",'TOUT',\"ACTIVITES MULTIPLES\",\"Url\",'1','1')");
          $id_new_actmed = Donne_ID ($connect, "select max(actmedia_cdn) from activite_media");
          $requete= mysql_query("INSERT INTO activite_media values('$id_new_actmed','$id_act','$id_new_ress')");
?>
