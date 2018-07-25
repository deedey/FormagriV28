<?php
Class RSS 
{

       public static function ajout($type_insert,$num_auteur,$num_insert)
      {
      	  GLOBAL $connect;
          $date_rss = strtotime(date("Y-m-d H:i:s" ,time()),0);
          $id_rss = Donne_ID ($connect,"SELECT max(rss_cdn) from rss");
          $ajouter = mysql_query("INSERT INTO rss VALUES ('$id_rss',\"$type_insert\",'$num_insert','$num_auteur','$date_rss','Ajout')");
      }
      public static function   modifie($type_insert,$num_auteur,$num_insert)
      {
     	  GLOBAL $connect;
          $date_rss = strtotime(date("Y-m-d H:i:s" ,time()),0);
          $date_compare = $date_rss-86400;
          $verifie = requete_order("rss_cdn","rss","rss_date_lb > '$date_compare' AND rss_id_no = '$num_insert' AND rss_type_lb='$type_insert' AND rss_action_lb='Modification'","rss_date_lb desc");
          if ($verifie == FALSE)
          {
             $id_rss = Donne_ID ($connect,"SELECT max(rss_cdn) from rss");
             $modifier = mysql_query("INSERT INTO rss VALUES ('$id_rss',\"$type_insert\",'$num_insert','$num_auteur','$date_rss','Modification')");
          }
          else
          {
             $id_rss = mysql_result($verifie,0,'rss_cdn');
             $modifier = mysql_query("UPDATE rss SET rss_date_lb = '$date_rss' WHERE rss_cdn = '$id_rss'");
          }
      }
       public static  function  supprime($type_insert,$num_insert)
      {
          $supprimer = mysql_query("DELETE FROM rss WHERE rss_id_no = '$num_insert' AND rss_type_lb='$type_insert'");
      }

}
?>