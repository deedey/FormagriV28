<?php
   require("langues/module.inc.php");
   $serievar = '';
// pour une inclusion dans liste_seq.php tableau de gauche des listes de séquences
    echo "<TD width='30%' valign='top' height='100%'><TABLE bgColor='#CEE6EC' cellspacing='1' cellpadding='0' width='100%' height='100%'>";
    echo "<TR><TD background=\"images/fond_titre_table.jpg\" colspan='1' height='40' align='center' valign='center'><Font size='3' color='#FFFFFF'><B>$mess_gp_seq_parc</B></FONT></TD></TR>";
    echo "<TR><TD width='100%' height='100%' valign='top'><TABLE bgColor='#CEE6EC' cellspacing='1' cellpadding='2' width='100%' border='0'>";
    if ($typ_user == "RESPONSABLE_FORMATION" || $typ_user == "ADMINISTRATEUR" || $typ_user == "FORMATEUR_REFERENT"){
      $serievar .= "<TR><TD width='100%' valign='center' colspan='3'>";
      $serievar .= "<TABLE bgColor='#CEE6EC' cellspacing='0' cellpadding='4' border='0' width='100%'><TR>";
      $lien= "sequence_entree.php?liste=1&id_ref_seq=0&choix_ref=1&miens=$miens";
      $lien = urlencode($lien);
      $lediv = ($choix_ref == 1) ?  "<DIV id = 'seqinv'>" : "<DIV id = 'sequence'>";
      $ico_img = ($choix_ref == 1) ? "><IMG SRC='images/modules/tut_form/iconewmodulb.gif' border='0' width='30' height='30'".bulle($mmsg_seq_new,"","LEFT","ABOVE",150) :
          " onmouseover=\"img11.src='images/modules/tut_form/iconewmodulb.gif';overlib('$mmsg_seq_new',ol_hpos,LEFT,ABOVE,WIDTH,'150',DELAY,'800',CAPTION, '');return true;\"".
          " onmouseout=\"img11.src='images/modules/tut_form/iconewmodul.gif';nd();\">".
          "<IMG NAME=\"img11\" SRC=\"images/modules/tut_form/iconewmodul.gif\"  border='0' width='30' height='30'".
          " onLoad=\"tempImg=new Image(0,0); tempImg.src='images/modules/tut_form/iconewmodulb.gif'\">";
      $serievar .= "<TD align=center valign='top'>$lediv<A HREF=\"trace.php?link=$lien\" $ico_img<BR>$mess_newseq</A></DIV></TD>";
      $seq_nombre = mysql_query ("select count(*) from sequence ");
      $nb_seq_nombre = mysql_result($seq_nombre,0);
         $seq_question = mysql_query ("select * from sequence where seq_auteur_no = $id_user order by seq_titre_lb");
         $nb_seqs = mysql_num_rows ($seq_question);
         if ($nb_seqs > 0){
            $lien="sequence_entree.php?prem=1&liste=1&miens=1&ordre_affiche=lenom&titre=$mess_menu_mes_seq";
            $lien = urlencode($lien);
            $lediv = ($miens == 1 && $choix_ref != 1 && $type_on != 1) ?  "<DIV id = 'seqinv'>" : "<DIV id = 'sequence'>";
            $ico_img = ($miens == 1 && $choix_ref != 1 && $type_on != 1) ? "><IMG SRC='images/modules/tut_form/icomodulb.gif' border='0' width='30' height='30'".bulle($mmsg_seq_miens,"","LEFT","ABOVE",150) :
            " onmouseover=\"img12.src='images/modules/tut_form/icomodulb.gif';overlib('$mmsg_seq_miens',ol_hpos,LEFT,ABOVE,WIDTH,'150',DELAY,'800',CAPTION, '');return true;\"".
            " onmouseout=\"img12.src='images/modules/tut_form/icomodul.gif';nd();\">".
            "<IMG NAME=\"img12\" SRC=\"images/modules/tut_form/icomodul.gif\"  border='0' width='30' height='30'".
            " onLoad=\"tempImg=new Image(0,0); tempImg.src='images/modules/tut_form/icomodulb.gif'\">";
            $serievar .= "<TD align=center valign='top' nowrap>$lediv<A HREF=\"trace.php?link=$lien\" $ico_img<BR> $mess_menu_mes_seq</A></DIV></TD>";
         }else
            $serievar .= "<TD align=center valign='top'><IMG SRC='images/modules/tut_form/icomodlib.gif' border='0'><BR><B>$mess_menu_mes_seq</B></TD>";
         $seq_question = mysql_query ("select * from sequence order by seq_titre_lb");
         $nb_seqs = mysql_num_rows ($seq_question);
         if ($nb_seqs > 0){
            $lien="sequence_entree.php?prem=1&liste=1&refer=2&ordre_affiche=lenom&titre=$mess_menu_gest_seq_liste_tts";
            $lien = urlencode($lien);
            $lediv = ($refer == 2 && $choix_ref != 1 && $type_on != 1) ?  "<DIV id = 'seqinv'>" : "<DIV id = 'sequence'>";
            $ico_img = ($refer == 2 && $choix_ref != 1 && $type_on != 1) ? "><IMG SRC='images/modules/tut_form/icotousmodb.gif' border='0' width='30' height='30'".bulle($mmsg_seq_tts,"","LEFT","ABOVE",150) :
            " onmouseover=\"img15.src='images/modules/tut_form/icotousmodb.gif';overlib('$mmsg_seq_tts',ol_hpos,LEFT,ABOVE,WIDTH,'150',DELAY,'800',CAPTION, '');return true;\"".
            " onmouseout=\"img15.src='images/modules/tut_form/icotousmod.gif';nd();\">".
            "<IMG NAME=\"img15\" SRC=\"images/modules/tut_form/icotousmod.gif\"  border='0' width='30' height='30'".
            " onLoad=\"tempImg=new Image(0,0); tempImg.src='images/modules/tut_form/icotousmodb.gif'\">";
            $serievar .= "<TD align=center valign='top'>$lediv<A HREF=\"trace.php?link=$lien\" $ico_img<BR>$mess_menu_gest_seq_liste_tts</A></DIV></TD>";
         }else
            $serievar .= "<TD align=center valign='top'><IMG SRC='images/modules/tut_form/icotousmod.gif' border='0'><BR><B>$mess_menu_gest_seq_liste_tts</B></TD>";
         $serievar .= "</TR>";
         $serievar .= "</TR>";
         $etat_fav = GetDataField($connect,"select param_etat_lb from param_foad where param_typ_lb='favoris'","param_etat_lb");
         $nb_star = mysql_num_rows(mysql_query("select * from stars where star_user_id='$id_user' and star_type_no=2"));
         $lediv = ($star == 1) ?  "<DIV id = 'seqinv'>" : "<DIV id = 'sequence'>";
         if ($etat_fav == 'OUI' && $nb_star > 0 && $star == 1)
         {
            $serievar .= "<TR><TD align=center valign='top' colspan=3>$lediv<a href=\"javascript:void(0);\">".
                         "<img src='images/starb.gif' border=0><br />$msg_seq_mark</a></div></TD></TR>";
            $requete_star = "SELECT * from sequence,stars where stars.star_item_id=sequence.seq_cdn and stars.star_user_id= $id_user and stars.star_type_no=2 order by seq_titre_lb, seq_type_lb asc";
         }
         elseif ($etat_fav == 'OUI' && $nb_star > 0 && !isset($star))
         {
             $requete_star = "SELECT * from sequence,stars where stars.star_item_id=seq_cdn and stars.star_user_id= $id_user and stars.star_type_no=2 order by seq_titre_lb, seq_type_lb asc";
             $seq_question = mysql_query ($requete_star);
             $nb_seqs = mysql_num_rows ($seq_question);
             if ($nb_seqs > 0)
             {
                $lien="sequence_entree.php?prem=1&liste=1&ordre_affiche=lenom&star=1";
                $lien = urlencode($lien);
                $serievar .= "<TR><TD align=center valign='bottom' colspan=3><A HREF=\"trace.php?link=$lien\" ".
                bulle($mess_menu_consult_fav_prop,"","RIGHT","BELOW",180).
                "<img src='images/starfull.gif' border=0><br />$msg_seq_mark</A></TD></TR>";
             }
         }
         $serievar .= "<TR>";
         $seq_question = mysql_query ("select * from sequence,sequence_referentiel where sequence.seq_cdn = sequence_referentiel.seqref_seq_no AND sequence_referentiel.seqref_referentiel_no = 0 AND (seq_publique_on=1 OR (seq_publique_on = 0 AND seq_auteur_no = $id_user)) order by seq_titre_lb");
         $nb_seqs = mysql_num_rows ($seq_question);
         if ($nb_seqs > 0)
         {
            $lien="sequence_entree.php?prem=1&liste=1&refer=0&ordre_affiche=lenom&titre=$mess_menu_gest_seq_liste_sref";
            $lien = urlencode($lien);
            $lediv = ($refer == 0 && $choix_ref != 1 && $miens != 1 && isset($refer) && $type_on != 1 && $ordre_affiche != 'leref' && $proprio == 0) ?  "<DIV id = 'seqinv'>" : "<DIV id = 'sequence'>";
            $ico_img = ($refer == 0 && $choix_ref != 1 && $miens != 1 && isset($refer) && $type_on != 1 && $ordre_affiche != 'leref' && $proprio == 0) ? "><IMG SRC='images/modules/tut_form/icomodlibb.gif' border='0' width='30' height='30'".bulle($mmsg_seq_ssref,"","LEFT","ABOVE",150) :
            " onmouseover=\"img13.src='images/modules/tut_form/icomodlibb.gif';overlib('$mmsg_seq_ssref',ol_hpos,LEFT,ABOVE,WIDTH,'150',DELAY,'800',CAPTION, '');return true;\"".
            " onmouseout=\"img13.src='images/modules/tut_form/icomodlib.gif';nd();\">".
            "<IMG NAME=\"img13\" SRC=\"images/modules/tut_form/icomodlib.gif\"  border='0' width='30' height='30'".
            " onLoad=\"tempImg=new Image(0,0); tempImg.src='images/modules/tut_form/icomodlibb.gif'\">";
            $serievar .= "<TD align=center valign='top'>$lediv<A HREF=\"trace.php?link=$lien\" $ico_img<BR>$mess_menu_gest_seq_liste_sref</A></DIV></TD>";
         }else
            $serievar .= "<TD align=center valign='top'><IMG SRC='images/modules/tut_form/icomodlib.gif' border='0'><BR><B>$mess_menu_gest_seq_liste_sref</TD>";
         $seq_question = mysql_query ("select * from sequence,sequence_referentiel where sequence.seq_cdn = sequence_referentiel.seqref_seq_no AND sequence_referentiel.seqref_referentiel_no > 0 AND (seq_publique_on=1 OR (seq_publique_on = 0 AND seq_auteur_no = $id_user)) order by seq_titre_lb");
         $nb_seqs = mysql_num_rows ($seq_question);
         if ($nb_seqs > 0){
            $lien="sequence_entree.php?prem=1&liste=1&refer=1&ordre_affiche=lenom&titre=$mess_menu_gest_seq_liste_ref";
            $lien = urlencode($lien);
            $lediv = ($refer == 1 && $choix_ref != 1 && $type_on != 1) ?  "<DIV id = 'seqinv'>" : "<DIV id = 'sequence'>";
            $ico_img = ($refer == 1 && $choix_ref != 1 && $type_on != 1) ? "><IMG SRC='images/modules/tut_form/icomodrefb.gif' border='0' width='30' height='30'".bulle($mmsg_seq_ref,"","LEFT","ABOVE",150) :
            " onmouseover=\"img14.src='images/modules/tut_form/icomodrefb.gif';overlib('$mmsg_seq_ref',ol_hpos,LEFT,ABOVE,WIDTH,'150',DELAY,'800',CAPTION, '');return true;\"".
            " onmouseout=\"img14.src='images/modules/tut_form/icomodref.gif';nd();\">".
            "<IMG NAME=\"img14\" SRC=\"images/modules/tut_form/icomodref.gif\"  border='0' width='30' height='30'".
            " onLoad=\"tempImg=new Image(0,0); tempImg.src='images/modules/tut_form/icomodrefb.gif'\">";
            $serievar .= "<TD align=center valign='top'>$lediv<A HREF=\"trace.php?link=$lien\" $ico_img <BR>$mess_menu_gest_seq_liste_ref</A></DIV></TD>";
         }else
            $serievar .= "<TD align=center valign='top'><IMG SRC='images/modules/tut_form/icomodref.gif' border='0'><BR><B>$mess_menu_gest_seq_liste_ref</B></TD>";
//---------------------------------------------
         $seq_question = mysql_query ("select * from sequence WHERE seq_type_on = 1 AND seq_publique_on = 1 order by seq_titre_lb");
         $nb_seqs = mysql_num_rows ($seq_question);
         $lediv = ($choix_ref != 1 && $type_on == 1) ?  "<DIV id = 'seqinv'>" : "<DIV id = 'sequence'>";
         $ico_img = ($choix_ref != 1 && $type_on == 1) ? "><IMG SRC='images/modules/tut_form/icomodultypb.gif' border='0' width='30' height='30'".bulle($mmsg_seq_type,"","LEFT","ABOVE",150) :
          " onmouseover=\"img16.src='images/modules/tut_form/icomodultypb.gif';overlib('$mmsg_seq_type',ol_hpos,LEFT,ABOVE,WIDTH,'150',DELAY,'800',CAPTION, '');return true;\"".
          " onmouseout=\"img16.src='images/modules/tut_form/icomodultyp.gif';nd();\">".
          "<IMG NAME=\"img16\" SRC=\"images/modules/tut_form/icomodultyp.gif\"  border='0' width='30' height='30'".
          " onLoad=\"tempImg=new Image(0,0); tempImg.src='images/modules/tut_form/icomodultypb.gif'\">";
         if ($nb_seqs > 0)
         {
            $lien="sequence_entree.php?prem=1&liste=1&refer=2&ordre_affiche=lenom&type_on=1&titre=$mess_seqs_type";
            $lien = urlencode($lien);
            $serievar .= "<TD align=center valign='top'>$lediv<A HREF=\"trace.php?link=$lien\" $ico_img <BR>$mess_seqs_type</A></DIV></TD></TR>";
         }
         else
            $serievar .= "<TD align=center valign='top'><IMG SRC='images/modules/tut_form/icomodultyp.gif' border='0'><BR><B>$mess_seqs_type</B></TD></TR>";
         $affiche = $serievar ."</TR>";
         $affiche .="<TR><TD colspan=3><HR size='1px' color='#50ACBE'></TD></TR></TABLE>";
         echo $affiche;
    }

?>
