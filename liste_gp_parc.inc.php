<?php
if (!isset($_SESSION['id_user']) || $_SESSION['id_user'] == "")
{
  exit();
}
    require("langues/module.inc.php");
    echo "<TD bgColor='#CEE6EC' width='25%' valign='top' height='100%'><TABLE cellspacing='1' cellpadding='0' width='100%' height='100%'>";
    echo "<TR><TD background=\"images/fond_titre_table.jpg\" colspan='1' height='40' align='center' valign='center'><Font size='3' color='#FFFFFF'><B>$mess_gp_parc_appr</B></FONT></TD></TR>";
    echo "<TR><TD width='100%' height='100%' valign='top'><TABLE bgColor='#CEE6EC' cellspacing='1' cellpadding='2' width='100%' border='0'>";
    $etat_fav = GetDataField($connect,"select param_etat_lb from param_foad where param_typ_lb='favoris'","param_etat_lb");
    if ($typ_user == "RESPONSABLE_FORMATION" || $typ_user == "ADMINISTRATEUR" || $typ_user == "FORMATEUR_REFERENT"){
      $serievar .= "<TR><TD width='100%' valign='center' colspan='3'>";
      $serievar .= "<TABLE bgColor='#CEE6EC' cellspacing='0' cellpadding='2' border='0' width='100%'><TR>";
      $parc_nombre = mysql_query ("select count(*) from parcours where parcours_cdn != 0");
      $nb_parc_nombre = mysql_result($parc_nombre,0);
      if ($nb_parc_nombre > 0)
      {
         $requete = "select * from parcours where parcours_cdn != 0 AND parcours_auteur_no = $id_user AND parcours_type_on = 0 order by parcours_nom_lb";
         $parc_question = mysql_query ($requete);
         $nb_parcs = mysql_num_rows ($parc_question);
         if ($miens_parc == 1) $requete_parc=$requete;
         $lediv = ($miens_parc == 1) ?  "<DIV id = 'seqinv'>" : "<DIV id = 'sequence'>";
         $ico_img = ($miens_parc == 1) ? "><IMG SRC='images/modules/tut_form/icomodulb.gif' border='0' width='30' height='30'".bulle($mmsg_mod_miens,"","LEFT","ABOVE",150) :
          " onmouseover=\"img12.src='images/modules/tut_form/icomodulb.gif';overlib('$mmsg_mod_miens',ol_hpos,LEFT,ABOVE,WIDTH,'150',DELAY,'800',CAPTION, '');return true;\"".
          " onmouseout=\"img12.src='images/modules/tut_form/icomodul.gif';nd();\">".
          "<IMG NAME=\"img12\" SRC=\"images/modules/tut_form/icomodul.gif\"  border='0' width='30' height='30'".
          " onLoad=\"tempImg=new Image(0,0); tempImg.src='images/modules/tut_form/icomodulb.gif'\">";
         if ($nb_parcs > 0)
         {
            $lien="modif_gp.php?id_grp=$id_grp&prem=1&liste=1&miens_parc=1&ordre_affiche=lenom";
            $lien = urlencode($lien);
            $serievar .= "<TD align=center valign='top'>$lediv<A HREF=\"trace.php?link=$lien\" $ico_img<BR>$mess_menu_mon_parc</A></DIV></TD>";
         }
         else
            $serievar .= "<TD align=center valign='top'><IMG SRC='images/modules/tut_form/icomodul.gif' border='0'><BR><B>$mess_menu_mon_parc</B></TD>";
         $requete = "select * from parcours where parcours_cdn != 0 AND parcours_referentiel_no = 0 AND parcours_type_on = 0  order by parcours_nom_lb";
         $parc_question = mysql_query ($requete);
         $nb_parcs = mysql_num_rows ($parc_question);
         if ($refer == 0 && $miens_parc != 1 && isset($refer)) $requete_parc=$requete;
         $lediv = ($refer == 0 && $miens_parc != 1 && isset($refer)) ?  "<DIV id = 'seqinv'>" : "<DIV id = 'sequence'>";
         $ico_img = ($refer == 0 && $miens_parc != 1 && isset($refer)) ? "><IMG SRC='images/modules/tut_form/icomodlibb.gif' border='0' width='30' height='30'".bulle($mmsg_mod_ssref,"","LEFT","ABOVE",150) :
          " onmouseover=\"img13.src='images/modules/tut_form/icomodlibb.gif';overlib('$mmsg_mod_ssref',ol_hpos,LEFT,ABOVE,WIDTH,'150',DELAY,'800',CAPTION, '');return true;\"".
          " onmouseout=\"img13.src='images/modules/tut_form/icomodlib.gif';nd();\">".
          "<IMG NAME=\"img13\" SRC=\"images/modules/tut_form/icomodlib.gif\"  border='0' width='30' height='30'".
          " onLoad=\"tempImg=new Image(0,0); tempImg.src='images/modules/tut_form/icomodlibb.gif'\">";
         if ($nb_parcs > 0)
         {
            $lien="modif_gp.php?id_grp=$id_grp&prem=1&liste=1&refer=0&ordre_affiche=lenom";
            $lien = urlencode($lien);
            $serievar .= "<TD align=center valign='top'>$lediv<A HREF=\"trace.php?link=$lien\" $ico_img<BR>$mess_mod_lib</A></DIV></TD>";
         }
         else
            $serievar .= "<TD align=center valign='top'><IMG SRC='images/modules/tut_form/icomodlib.gif' border='0'><BR><B>$mess_mod_lib</B></TD>";
         $serievar .= "</TR><TR>";
         $nb_star = mysql_num_rows(mysql_query("select * from stars where star_user_id='$id_user' and star_type_no=1"));
         $lediv = ($star == 1) ?  "<DIV id = 'seqinv'>" : "<DIV id = 'sequence'>";
         if ($etat_fav == 'OUI' && $nb_star > 0 && $star == 1){
            $serievar .= "<TD align=center valign='top' colspan=2>$lediv<a href=\"javascript:void(0);\"><img src='images/starb.gif' border=0><br />$msg_mod_mark</a></div></TD>";
            $requete_star = "SELECT * from parcours,stars where stars.star_item_id=parcours.parcours_cdn and stars.star_user_id= $id_user and stars.star_type_no=1 order by parcours_nom_lb, parcours.parcours_type_lb asc";
         }elseif ($etat_fav == 'OUI' && $nb_star > 0 && !isset($star))
         {
             $requete_star = "SELECT * from parcours,stars where stars.star_item_id=parcours.parcours_cdn and stars.star_user_id= $id_user and stars.star_type_no=1 order by parcours_nom_lb, parcours.parcours_type_lb asc";
             $parc_question = mysql_query ($requete_star);
             $nb_parcs = mysql_num_rows ($parc_question);
             if ($nb_parcs > 0)
             {
                $lien="modif_gp.php?id_grp=$id_grp&prem=1&liste=1&star=1&ordre_affiche=lenom";
                $lien = urlencode($lien);
                $serievar .= "<TD align=center valign='bottom' colspan=2><A HREF=\"trace.php?link=$lien\" ".
                bulle($mess_menu_consult_fav_prop,"","RIGHT","BELOW",180).
                "<img src='images/starfull.gif' border=0><br />$msg_mod_mark</A></TD>";
             }
         }
         $serievar .= "</TR><TR>";
         $requete = "select * from parcours where parcours_cdn != 0 AND parcours_referentiel_no > 0 AND parcours_type_on = 0  order by parcours_nom_lb";
         $parc_question = mysql_query ($requete);
         $nb_parcs = mysql_num_rows ($parc_question);
         if ($refer == 1 && $miens_parc != 1) $requete_parc=$requete;
         $lediv = ($refer == 1 && $miens_parc != 1) ?  "<DIV id = 'seqinv'>" : "<DIV id = 'sequence'>";
         $ico_img = ($refer == 1 && $miens_parc != 1) ? "><IMG SRC='images/modules/tut_form/icomodrefb.gif' border='0' width='30' height='30'".bulle($mmsg_mod_ref,"","LEFT","ABOVE",150) :
          " onmouseover=\"img14.src='images/modules/tut_form/icomodrefb.gif';overlib('$mmsg_mod_ref',ol_hpos,LEFT,ABOVE,WIDTH,'150',DELAY,'800',CAPTION, '');return true;\"".
          " onmouseout=\"img14.src='images/modules/tut_form/icomodref.gif';nd();\">".
          "<IMG NAME=\"img14\" SRC=\"images/modules/tut_form/icomodref.gif\"  border='0' width='30' height='30'".
          " onLoad=\"tempImg=new Image(0,0); tempImg.src='images/modules/tut_form/icomodrefb.gif'\">";
         if ($nb_parcs > 0)
         {
            $lien="modif_gp.php?id_grp=$id_grp&prem=1&liste=1&refer=1&ordre_affiche=lenom";
            $lien = urlencode($lien);
            $serievar .= "<TD align=center valign='bottom'>$lediv<A HREF=\"trace.php?link=$lien\" $ico_img <BR>$mess_mod_ref</A></DIV></TD>";
         }
         else
            $serievar .= "<TD align=center valign='top'><IMG SRC='images/modules/tut_form/icomodref.gif' width='30' height='30' border='0'><BR><B>$mess_mod_ref</B></TD>";
         $requete = "select * from parcours where parcours_cdn != 0 AND parcours_type_on = 0 order by parcours_nom_lb";
         if ($refer == 2 && $miens_parc != 1) $requete_parc=$requete;
         $lediv = ($refer == 2 && $miens_parc != 1) ?  "<DIV id = 'seqinv'>" : "<DIV id = 'sequence'>";
         $ico_img = ($refer == 2 && $miens_parc != 1) ? "><IMG SRC='images/modules/tut_form/icotousmodb.gif' border='0' width='30' height='30'".bulle($mmsg_mod_tts,"","LEFT","ABOVE",150) :
          " onmouseover=\"img15.src='images/modules/tut_form/icotousmodb.gif';overlib('$mmsg_mod_tts',ol_hpos,LEFT,ABOVE,WIDTH,'150',DELAY,'800',CAPTION, '');return true;\"".
          " onmouseout=\"img15.src='images/modules/tut_form/icotousmod.gif';nd();\">".
          "<IMG NAME=\"img15\" SRC=\"images/modules/tut_form/icotousmod.gif\"  border='0' width='30' height='30'".
          " onLoad=\"tempImg=new Image(0,0); tempImg.src='images/modules/tut_form/icotousmodb.gif'\">";
         $lien="modif_gp.php?id_grp=$id_grp&prem=1&liste=1&refer=2&ordre_affiche=lenom";
         $lien = urlencode($lien);
         $serievar .= "<TD align='center' valign='bottom'>$lediv<A HREF=\"trace.php?link=$lien\" $ico_img<BR>$mess_mod_tous</A></DIV></TD>";
         $affiche = $serievar ."</TR>";
         $affiche .="<TR><TD colspan=3><HR size='1' color='#50ACBE'></TD></TR></TABLE>";
         echo $affiche;
         if ($star == 1) $requete_parc = $requete_star;
      }
      else
      {
         echo "<TR height='30'><TD width='100%' align='center' valign='top' bgcolor='#F4F4F4'><B>$mess_noparc_const</B></TD></TR>";
        exit;
      }
    }
    $NbGrpParc = mysql_num_rows(mysql_query("select * from groupe_parcours where gp_grp_no =$id_grp"));
    if ($NbGrpParc > 0)
    {
        $new_wiki = 10000+$id_grp;
        $lien= "wiki/wikiAjout.php?id_seq=$new_wiki&keepThis=true&TB_iframe=true&height=400&width=700";
        $contentWk .= "<TR><TD colspan=3><table><TR><TD valign='top' align='left'><IMG SRC='wiki/images/wikidoc.gif' border='0' style='margin-left:15px;'></TD>".
                     "<TD valign='top' align='left'>";
        $contentWk .= '<DIV id="sequence"><a href="'.$lien.'" class="thickbox" title="'.
                     'Ajouter ou modifier un travail à faire en commun(WikiDoc) : <br />Donnez juste les consignes.'.
                     'Les apprenants de la formation peuvent l\'amender ou le modifier ." ';
        $contentWk .= 'name="Ajouter ou modifier un travail en commun" >'.
                      'Consulter et gérer des WikiDocs</A></DIV></TD></TR>';
        echo $contentWk;
        $lien= "mindmap/mindAjout.php?id_grp=$id_grp&keepThis=true&TB_iframe=true&height=350&width=550";
        $contentMM  = "<TR><TD valign='top' align='right'><IMG SRC='mindmap/pix/icon.png' border='0'></TD><TD>";
        $contentMM .= "<DIV id='sequence'><a href=\"$lien\" class='thickbox' ".
                      "title='Ajouter ou modifier une instance de carte heuristique' ";
        $contentMM .= "name='Ajouter ou supprimer une carte heuristique' ".
                      bulle("Ajouter ou supprimer une carte heuristique(MindMap)" ,"","RIGHT","ABOVE",270).
                      "Gérer des MindMaps</A></DIV></TD></TR></TABLE></TD></TR>";
        echo $contentMM;
    }
    if ($lg == "fr")
       setlocale(LC_TIME,'fr_FR');
    elseif($lg == "ru")
       setlocale(LC_TIME,'ru_RU');
    if ($requete_parc == "")
      $requete_parc = "select * from parcours where parcours_cdn != 0 AND parcours_auteur_no = $id_user AND parcours_type_on = 0 order by parcours_nom_lb";
    $req_parc = mysql_query($requete_parc);
    $nb_req_parc = mysql_num_rows ($req_parc);
    $p_i=0;
    while ($p_i < $nb_req_parc)
    {
          $parc_droit = mysql_result ($req_parc,$p_i,"parcours_cdn");
          $req_gpc = mysql_num_rows(mysql_query("select * from groupe_parcours where gp_grp_no =$id_grp and gp_parc_no=$parc_droit"));
          if ($req_gpc > 0)
          {
             $p_i++;
             continue;
          }
          $nom_parcours = mysql_result ($req_parc,$p_i,"parcours_nom_lb");
          $parc_type = mysql_result ($req_parc,$p_i,"parcours_type_on");
          $createur = mysql_result ($req_parc,$p_i,"parcours_auteur_no");
          $nom_createur = GetDataField ($connect,"select util_nom_lb from utilisateur where util_cdn = $createur","util_nom_lb");
          $prenom_createur = GetDataField ($connect,"select util_prenom_lb from utilisateur where util_cdn = $createur","util_prenom_lb");
          $majuscule = "$prenom_createur $nom_createur";
          $date_creat = mysql_result ($req_parc,$p_i,"parcours_create_dt");
          $ch_dtc = explode("-",$date_creat);
          $dtc = $ch_dtc[2].'-'.$ch_dtc[1].'-'.$ch_dtc[0];
          $date_modif = mysql_result ($req_parc,$p_i,"parcours_modif_dt");
          $ch_dtm = explode("-",$date_modif);
          $dtm = $ch_dtm[2].'-'.$ch_dtm[1].'-'.$ch_dtm[0];
          $droit_voir = mysql_result ($req_parc,$p_i,"parcours_publique_on");
          $parc_type = mysql_result ($req_parc,$p_i,"parcours_type_on");
          $id_ref_parc = mysql_result ($req_parc,$p_i,"parcours_referentiel_no");
          $lien = "modif_gp.php?prescrire_grp=1&id_grp=$id_grp&parc=$parc_droit&miens_parc=$miens_parc&refer=$refer&comeback=liste_gp";
          $lien = urlencode($lien);
          $nb_seq_parc_req = mysql_query ("SELECT * from sequence_parcours where seqparc_parc_no = $parc_droit");
          $nb_seq_liste = mysql_num_rows($nb_seq_parc_req);
          if ($nb_seq_liste > 0)
          {
             $s_i = 0;$no_seq = 0;
             while ($s_i < $nb_seq_liste)
             {
                 $sequence = mysql_result ($nb_seq_parc_req,$s_i,"seqparc_seq_no");
                 $nb_seq_act_req = mysql_query ("SELECT count(act_cdn) from activite where act_seq_no = $sequence");
                 $nb_seq_act = mysql_result($nb_seq_act_req,0);
                 if ($nb_seq_act == 0)
                 {
                    $no_seq++;
                    $s_i++;
                    continue;
                 }
               $s_i++;
             }
             $div="DIV id='sequence'";
             if ($createur == $id_user)
             {
                echo "<TR><TD width='5%' align='right' valign='top' nowrap>";
                if ($droit_voir == 0)
                   echo "<IMG SRC=\"images/modules/tut_form/icocadenas.gif\" border='0'>&nbsp;";
                echo "<IMG SRC=\"images/gest_parc/icofeuil.gif\" border='0'>";
             }
             else
             {
                echo "<TR><TD width='5%' align='right' valign='top' nowrap>";
                if ($droit_voir == 0)
                   echo "<IMG SRC=\"images/modules/tut_form/icocadenas.gif\" border='0'>&nbsp;&nbsp;";
                echo "-";
             }
             echo "</TD>";
             if ($no_seq < $nb_seq_liste)
             {
                 $affichage = "<B><font size=2><center>$mess_presc_mod</center></font></B><P>&nbsp;&nbsp;- $mess_nbseq : $nb_seq_liste<BR>&nbsp;&nbsp;- $mrc_aut : <B>".addslashes($majuscule)."</B><BR>&nbsp;&nbsp;- $mess_menu_gest_seq_ref : &nbsp;&nbsp; <B>$dtc</B><BR>&nbsp;&nbsp;- $mess_modif_dt :&nbsp;&nbsp; <B>$dtm</B>";
                 echo "<TD width='95%'><$div><A href=\"trace.php?link=$lien\" onMouseOver=\"overlib('$affichage',ol_hpos,CENTER,ABOVE,WIDTH,'250',DELAY,'800',CAPTION, '')\" onMouseOut=\"nd()\">$nom_parcours</DIV></TD></TR>";
             }
             else
             {
                $affichage = "&nbsp;<B>$msq_parc</B><BR>&nbsp;&nbsp;- $mess_nbseq : $nb_seq_liste<BR>&nbsp;&nbsp;- $mrc_aut : <B>".addslashes($majuscule)."</B><BR>&nbsp;&nbsp;- $mess_menu_gest_seq_ref : &nbsp;&nbsp; <B>$dtc</B><BR>&nbsp;&nbsp;- $mess_modif_dt :&nbsp;&nbsp; <B>$dtm</B>";
                echo "<TD width='95%'><$div><A href=\"#\" onMouseOver=\"overlib('$affichage',ol_hpos,CENTER,ABOVE,WIDTH,'250',DELAY,'800',CAPTION, '')\" onMouseOut=\"nd()\">$nom_parcours</DIV></TD></TR>";
             }
          }
      $p_i++;
    }
    echo "</TABLE></TD></TR></TABLE></TD>";
?>
