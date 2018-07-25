<?php
      entete_concept("$laliste","$mess_pop_act_tit");
      echo "<TD valign='top' width='70%' bgColor='#cee6ec' height='100%'>";
      echo "<TABLE cellspacing='1' cellpadding='4' width='100%' border='0'>";
      echo "<tr bgcolor='#2b677a' height='30'>";
      echo "<TD align='left' valign='center'><FONT COLOR=white><b>$msq_activite</b></FONT></TD>";
      echo "<TD align='left' valign='center'><FONT COLOR=white><b>$msq_aff_cons</b></FONT></TD>";
      echo "<TD align='left' valign='center'><FONT COLOR=white><b>$msq_duree_seq</b></FONT></TD>";
      echo "<TD align='left' valign='center'><FONT COLOR=white><b>$msq_ress_assoc</b></FONT></TD>";
      echo "<TD align='left' valign='center'><FONT COLOR=white><b>$mess_modif_base</b></FONT></TD>";
      echo "<TD align='left' valign='center'><FONT COLOR=white><b>$mess_ag_supp</b></FONT></TD>";
      echo "<TD align='left' valign='center'><FONT COLOR=white><b>$mess_dupliquer</b></FONT></TD>";
      echo "</TR>";
      $act_query = requete("*","activite","act_cdn = '$id_act'");
      $id = mysql_result ($act_query,0,"act_cdn");
      $nom = mysql_result ($act_query,0,"act_nom_lb");
      $flag = mysql_result ($act_query,0,"act_flag_on");
      $ress_norok = mysql_result ($act_query,0,"act_ress_on");
      //$consigne = mysql_result ($act_query,0,"act_consigne_cmt");
      //$commentaire = mysql_result ($act_query,0,"act_commentaire_cmt");
      $pass_mult = mysql_result ($act_query,0,"act_passagemult_on");
      $acquit = mysql_result ($act_query,0,"act_acquittement_lb");
      $devoir = mysql_result ($act_query,0,"act_devoirarendre_on");
      $notation = mysql_result ($act_query,0,"act_notation_on");
      $duree = mysql_result ($act_query,0,"act_duree_nb");
      $auteur = mysql_result ($act_query,0,"act_auteur_no");
      $droit_voir_act = mysql_result ($act_query,0,"act_publique_on");
      $consigne = html_entity_decode(DelAmp(mysql_result ($act_query,0,"act_consigne_cmt")),ENT_QUOTES,'iso-8859-1');
      $commentaire = html_entity_decode(mysql_result ($act_query,0,"act_commentaire_cmt"),ENT_QUOTES,'iso-8859-1');
           //dey Dfoad
              $req_media = mysql_result(mysql_query("select count(*) from activite_media where actmedia_act_no = $id"),0);
              $media_act = "";
              if ($req_media > 0)
              {
                 $media_act = GetDataField ($connect,"select ress_url_lb from ressource_new,activite_media where
                                            ress_cdn = actmedia_ress_no and actmedia_act_no =$id ","ress_url_lb");
              }
      $aff_cadenas = "";
      $l = $i+1;
      if (!$consigne)
         $consigne = $msq_aucune;
      //Séléction ressource
      $id_ress = mysql_result ($act_query,$i,"act_ress_no");
      $non_affic_ress_lien = 0;
      if ($id_ress == 0)
         $ressource = $msq_aucune;
      else {
         $ressource = GetDataField ($connect,"select ress_titre from ressource_new where ress_cdn = $id_ress","ress_titre");
         $url_ressource = GetDataField ($connect,"select ress_url_lb from ressource_new where ress_cdn = $id_ress","ress_url_lb");
         //Dans le cas ou la ressource a ete supprimee
         if ($url_ressource == '')
            $ressource = $ressource;
         else {
            $typ_ress = GetDataField ($connect,"select ress_support from ressource_new where ress_cdn = $id_ress","ress_support");
            $typ_ress = strtoupper ($typ_ress);
         }
      } //fin else

      echo "<tr bgcolor= \"#F4F4F4\">";
     /*
      if ($commentaire != "")
         $commentaire1 =$commentaire;//addcslashes($commentaire,"\0..\47!@\176..\255");
      else
         $commentaire1 = $mess_no_comment;
      */
      echo "<TD valign='top'>";
      echo "<A href=\"#\"";
      $titre_bulle ="$msq_aff_pass_mult <B>$pass_mult</B><BR>$msq_aff_acquit <B>$acquit</B><BR>$msq_aff_dev_rend <B>$devoir</B><BR>$msq_act_evalue <B>$notation</B>";
      echo bulle($titre_bulle,$msq_fic_act,"RIGHT","",250);
      $titre_bulle = "";
      echo "$nom</A></td>";

      echo "<TD valign='top'><DIV id='sequence'>";
      //dey Dfoad
             $req_typdev = mysql_result(mysql_query("select count(*) from activite_devoir where actdev_act_no = $id"),0);
             $dev_act = "";
             if ($req_typdev > 0)
             {
                 $dev_act = GetDataField ($connect,"select actdev_dev_lb from activite_devoir where
                                            actdev_act_no = $id ","actdev_dev_lb");
            }
             else
                 $dev_act = "Pas de devoir";
            $class_act =  GetDataField ($connect,"select actdevico_style_lb from actdev_icone where
                                            actdevico_type_lb = \"$dev_act\" ","actdevico_style_lb");

           echo "<div $class_act> $consigne</A></div></DIV>";
      //dey Dfoad
              if($media_act != "")
              {
                  $actit = $id;
                  $largeur = "220";
                  $hauteur = "140";
                  echo "<br />&nbsp;<div id='insertMedia'>";
                      include ("media.php");
                  echo "</div>";
              }
      echo "</TD>";
      $duree = duree_calc($duree);
      echo "<td align='middle' valign='top'>$duree</td>";
      if (strstr($url_ressource,"?"))
         $aj="&";
      else
         $aj="?";
      if ((strstr($url_ressource,"ParWeb")) || (strstr($url_ressource,"parweb")) || (strstr($url_ressource,"Legweb"))  || (strstr($url_ressource,"legweb")) || (strstr($url_ressource,"Tatweb"))  || (strstr($url_ressource,"tatweb")) || (strstr($url_ressource,"Qcmweb"))  || (strstr($url_ressource,"qcmweb")) || (strstr($url_ressource,"Elaweb")) || (strstr($url_ressource,"elaweb")))
         $suite="&nom=$nom_user&prenom=$prenom_user&email=$email_user";
      else
         $suite="";
      if ($id_ress > 0 && $url_ressource == "")
      {
         $lien= "ress_virtuel.php?id_ress=$id_ress&id_act=$id";
         $lien = urlencode($lien);
         echo "<td align='left' valign='top'><DIV id='sequence'><A href='#' ".
              "onclick=\"window.open('trace.php?link=$lien','','resizable=yes,scrollbars=yes,status=no,width=700,height=400')\">".
              "$ressource</a></DIV></td>";
      }
      elseif ($id_ress > 0 && ((strstr(strtolower($url_ressource),".doc") || strstr(strtolower($url_ressource),".xls") || strstr(strtolower($url_ressource),".xlt"))) && ($auteur == $id_user || $typ_user == 'ADMINISTRATEUR' || $droit_voir_act == 1))
      {
        if (serveur_externe($url_ressource) == $url_ressource)
           $lien = urldecode(serveur_externe($url_ressource));
        echo "<TD valign='top'><DIV id='sequence'><A href=\"$lien\" target='_blank'>$ressource</A></DIV>";
      }
      elseif ($id_ress > 0 && $url_ressource != "" && ($auteur == $id_user || $typ_user == 'ADMINISTRATEUR' || $droit_voir_act == 1))
      {
         if (strstr(strtolower($url_ressource),"educagrinet"))
            $url_ressource = str_replace("acces.html","direct.html",$url_ressource)."&url=$url_ress&auth_cdn=$auth_cdn";
         elseif (serveur_externe($url_ressource) == $url_ressource)
           $lien = urldecode(serveur_externe($url_ressource));
         elseif($dev_act == 'xApi TinCan')
         {
             $lien = $liens.TinCanTeach ('teacher|0|'.$id_seq.'|'.$id_act.'|0',$liens,$commentaire);
         }
         if (strstr(strtolower($url_ressource),".flv") ||
             strstr(strtolower($url_ressource),".mp3") ||
             strstr(strtolower($url_ressource),".swf") ||
             strstr(strtolower($url_ressource),".mp4") ||
             strstr(strtolower($url_ressource),".ogv") ||
             strstr(strtolower($url_ressource),".webm"))
            $lien = "lanceMedia.php?id_ress=$id_ress";
         echo "<TD align='left' valign='top'><DIV id='sequence'><A href=\"$lien\" target='blank'>$ressource</a></DIV></td>";
      }
      else
      {
            if ($auteur != $id_user && $droit_voir_act == 0 && $id_ress > 0 && $url_ressource != "")
               $aff_cadenas = "<IMG SRC=\"images/modules/tut_form/icocadenas.gif\" border='0'>&nbsp;";
            echo "<TD align='left' valign='top'>$aff_cadenas $ressource</td>";
      }
      if ($auteur == $id_user || $typ_user == "ADMINISTRATEUR")
      {
              $lien = "activite_free.php?creer=1&modifie_act=1&medor=$medor&act_a_modif=$id_act&id_seq=$id_seq&miens=$miens&lesseq=$lesseq&titre_act=$titre_act";
              $lien = urlencode($lien);
              echo "<TD width='2%' align='middle' valign='top'><a href=\"trace.php?link=$lien\" target='main'".
                   bulle($mess_modif_base,"","CENTER","ABOVE",100).
                   "<IMG SRC=\"images/repertoire/icoGrenomfich20.gif\" border=0></A></TD>";
      }
      else
              echo "<TD>&nbsp;</TD>";
      if ($lesseq == 0)
      {
              if ($nb_req_seq == 0 && ($auteur == $id_user || $typ_user == "ADMINISTRATEUR"))
              {
                 $lien = "activite_free.php?supp_act=1&act_a_supp=$id_act&id_seq=$id_seq&miens=$miens&lesseq=$lesseq&titre_act=$titre_act";
                 echo "<TD align='middle' valign='top'><A HREF=\"$lien\" onclick=\"return(conf());\" target='main'".
                      bulle($msq_sup_act,"","CENTER","ABOVE",100).
                      "<IMG SRC=\"images/messagerie/icoGpoubel.gif\" height=\"20\" width=\"15\" BORDER=0></A></td>";
              }
              elseif ($nb_req_seq > 0 && ($auteur == $id_user || $typ_user == "ADMINISTRATEUR"))
              {
                 echo "<TD align='middle' valign='top'><A href=\"javascript:void(0);\"".
                      bulle($msq_act_no_supp,"","CENTER","ABOVE",100).
                      "<IMG SRC=\"images/repertoire/icoptiinterdit.gif\" height=\"15\" width=\"15\" BORDER=0 ></A></td>";
              }
              elseif ($auteur != $id_user && $typ_user != "ADMINISTRATEUR")
              {
                 echo "<TD align='middle' valign='top'><A href=\"javascript:void(0);\" ".
                      bulle("$prenom_auteur $nom_auteur","","CENTER","ABOVE",100).
                      "<IMG SRC=\"images/repertoire/icoptiinterdit.gif\" height='15' width='15' BORDER=0></A></td>";
              }
              else
                 echo "<TD>&nbsp;</TD>";
      }
      else
                 echo "<TD>&nbsp;</TD>";
      if ($droit_voir_act == 1)
      {
              $lien = "activite_free.php?dupli_act=1&act_a_dupli=$id_act&id_seq=0&miens=$miens&lesseq=$lesseq&titre_act=$titre_act";
              $lien = urlencode($lien);
              echo "<TD width='2%' align='middle' valign='top'><a href=\"trace.php?link=$lien\" target='main'".
                   bulle($mess_dupli_act,"","CENTER","ABOVE",100).
                   "<IMG SRC=\"images/repertoire/icoGeditfich.gif\" height=\"20\" width=\"20\" BORDER=0></A></TD></TR>";
      }
      else
              echo "<TD>&nbsp;</TD></TR>";
      echo "</TABLE></TD></TR></TABLE>";
?>