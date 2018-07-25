<?php
if (!isset($_SESSION['id_user']) || $_SESSION['id_user'] == "")
{
  exit();
}
    require("langues/module.inc.php");
    unset($_SESSION['requete_act']);
    echo "<TD width='30%' valign='top' height='100%'>";
    echo "<TABLE bgColor='#CEE6EC' cellspacing='1' cellpadding='0' width='100%' height='100%'>";
    echo "<TR><TD background=\"images/fond_titre_table.jpg\" colspan='1' height='40' align='center' valign='center'>";
    echo "<Font size='3' color='#FFFFFF'><B>$msq_acts</B></FONT></TD></TR>";
    echo "<TR><TD width='100%' valign='top' bgColor='#CEE6EC'>";
    echo "<TABLE cellspacing='1' cellpadding='0' width='100%' border='0'>";
    GLOBAL $connect,$star,$visible,$requete_act,$act_a_modif,$id_ress,$titre_act,
           $medor,$keydesc,$keytitre,$keypub,$proprio;
    if ($typ_user == "RESPONSABLE_FORMATION" || $typ_user == "ADMINISTRATEUR" || $typ_user == "FORMATEUR_REFERENT")
    {
      $serievar = "<TR><TD width='100%' height='100%' valign='center' colspan='3'>";
      $serievar .= "<TABLE bgColor='#CEE6EC' cellspacing='0' cellpadding='4' border='0' width='100%'><TR>";
      $lien= "activite_free.php?creer=1&miens=$miens&lesseq=0";
      $lien = urlencode($lien);
      $lediv = (isset($creer) && $creer == 1 && $modifie_act != 1) ?  "<DIV id = 'seqinv'>" : "<DIV id = 'sequence'>";
      $ico_img = (isset($creer) && $creer == 1 && $modifie_act != 1) ? "><IMG SRC='images/modules/tut_form/iconewmodulb.gif' border='0' width='30' height='30'".bulle($mmsg_act_new,"","LEFT","ABOVE",150) :
          " onmouseover=\"img11.src='images/modules/tut_form/iconewmodulb.gif';overlib('$mmsg_act_new',ol_hpos,LEFT,ABOVE,WIDTH,'150',DELAY,'800',CAPTION, '');\"".
          " onmouseout=\"img11.src='images/modules/tut_form/iconewmodul.gif';nd();\">".
          "<IMG NAME=\"img11\" SRC=\"images/modules/tut_form/iconewmodul.gif\"  border='0' width='30' height='30'".
          " onLoad=\"tempImg=new Image(0,0); tempImg.src='images/modules/tut_form/iconewmodulb.gif'\">";
      $serievar .= "<TD align=center valign='top'>$lediv<A HREF=\"trace.php?link=$lien\" $ico_img<BR>$msq_ajout_act_seq</A></DIV></TD>";
      $act_nombre = mysql_query ("select count(*) from activite");
      $nb_act_nombre = mysql_result($act_nombre,0);
         $requete = "select * from activite where act_auteur_no = $id_user order by act_nom_lb";
         $act_question = mysql_query ("$requete");
         $nb_acts = mysql_num_rows ($act_question);
         if ($nb_acts > 0)
         {
            $lien="activite_free.php?miens=1&medor=1&lesseq=2&ordre_affiche=lenom&titre_act=$mess_liste_vos_act";
            $lien = urlencode($lien);
            $lediv = ($miens == 1 && !isset($creer) && $lesseq == 2) ?  "<DIV id = 'seqinv'>" : "<DIV id = 'sequence'>";
            $ico_img = ($miens == 1 && !isset($creer) && $lesseq == 2) ? "><IMG SRC='images/modules/tut_form/icomodulb.gif' border='0' width='30' height='30'".bulle($mmsg_act_miens_lbrs,"","LEFT","ABOVE",150) :
            " onmouseover=\"img12.src='images/modules/tut_form/icomodulb.gif';overlib('$mmsg_act_miens_lbrs',ol_hpos,LEFT,ABOVE,WIDTH,'150',DELAY,'800',CAPTION, '');\"".
            " onmouseout=\"img12.src='images/modules/tut_form/icomodul.gif';nd();\">".
            "<IMG NAME=\"img12\" SRC=\"images/modules/tut_form/icomodul.gif\"  border='0' width='30' height='30'".
            " onLoad=\"tempImg=new Image(0,0); tempImg.src='images/modules/tut_form/icomodulb.gif'\">";
            $serievar .= "<TD align=center valign='top' nowrap>$lediv<A HREF=\"trace.php?link=$lien\" $ico_img<BR> $mess_mes_act</A></DIV></TD>";
         }
         else
            $serievar .= "<TD align=center valign='top'><IMG SRC='images/modules/tut_form/icomodlib.gif' border='0'><BR><B>$mess_mes_act</B></TD>";
         $requete = "select * from activite order by act_nom_lb";
         $act_question = mysql_query ("$requete");
         $nb_acts = mysql_num_rows ($act_question);
         if ($nb_acts > 0)
         {
            $lien="activite_free.php?lesseq=2&medor=1&ordre_affiche=lenom&titre_act=$mess_tts_act";
            $lien = urlencode($lien);
            $lediv = ($miens == 0 && $proprio == 0 && $lesseq == 2) ?  "<DIV id = 'seqinv'>" : "<DIV id = 'sequence'>";
            $ico_img = ($miens == 0 && $proprio == 0 && $lesseq == 2) ? "><IMG SRC='images/modules/tut_form/icomodlibb.gif' border='0' width='30' height='30'".bulle($mmsg_act_miens_seq,"","LEFT","ABOVE",150) :
            " onmouseover=\"img13.src='images/modules/tut_form/icomodlibb.gif';overlib('$mmsg_act_miens_seq',ol_hpos,LEFT,ABOVE,WIDTH,'150',DELAY,'800',CAPTION, '');\"".
            " onmouseout=\"img13.src='images/modules/tut_form/icomodlib.gif';nd();\">".
            "<IMG NAME=\"img13\" SRC=\"images/modules/tut_form/icomodlib.gif\"  border='0' width='30' height='30'".
            " onLoad=\"tempImg=new Image(0,0); tempImg.src='images/modules/tut_form/icomodlibb.gif'\">";
            $serievar .= "<TD align=center valign='top'>$lediv<A HREF=\"trace.php?link=$lien\" $ico_img<BR>$mess_tts_act</A></DIV></TD>";
         }
         else
            $serievar .= "<TD align=center valign='top'><IMG SRC='images/modules/tut_form/icomodlib.gif' border='0'><BR><B>$mess_liste_vos_act_seq</TD>";
         $serievar .= "</TR>";
         $etat_fav = GetDataField($connect,"select param_etat_lb from param_foad where param_typ_lb='favoris'","param_etat_lb");
         $nb_star = mysql_num_rows(mysql_query("select * from stars where star_user_id='$id_user' and star_type_no=3"));
         $lediv = ($star == 1) ?  "<DIV id = 'seqinv'>" : "<DIV id = 'sequence'>";
         if ($etat_fav == 'OUI' && $nb_star > 0 && $star == 1)
         {
            $serievar .= "<TR><TD align=center valign='top' colspan=3>$lediv<a href=\"javascript:void(0);\">".
                         "<img src='images/starb.gif' border=0><br />Marquées</a></div></TD></TR>";
            $requete_star = "SELECT * from activite,stars where stars.star_item_id=act.seq_cdn and stars.star_user_id= $id_user and stars.star_type_no=3 order by act_nom_lb asc";
         }
         elseif ($etat_fav == 'OUI' && $nb_star > 0 && !isset($star))
         {
             $requete_star = "SELECT * from activite,stars where stars.star_item_id=act_cdn and stars.star_user_id= $id_user and stars.star_type_no=3 order by act_nom_lb asc";
             $act_question = mysql_query ($requete_star);
             $nb_acts = mysql_num_rows ($act_question);
             if ($nb_acts > 0)
             {
                $lien="activite_free.php?prem=1&liste=1&ordre_affiche=lenom&star=1";
                $lien = urlencode($lien);
                $serievar .= "<TR><TD align=center valign='bottom' colspan=3><A HREF=\"trace.php?link=$lien\" ".
                bulle($mess_menu_consult_fav_prop,"","RIGHT","BELOW",180).
                "<img src='images/starfull.gif' border=0><br />Marquées</A></TD></TR>";
             }
         }
         $serievar .= "<TR>";
         $requete = "select * from activite where act_seq_no = 0 order by act_nom_lb";
         $act_question = mysql_query ("$requete");
         $nb_acts = mysql_num_rows ($act_question);
         if ($nb_acts > 0)
         {
            $lien="activite_free.php?lesseq=0&medor=1&ordre_affiche=lenom&titre_act=$mess_liste_act_autres";
            $lien = urlencode($lien);
            $lediv = ($lesseq == 0 && $miens != 1 && !isset($visible)) ?  "<DIV id = 'seqinv'>" : "<DIV id = 'sequence'>";
            $ico_img = ($lesseq == 0 && $miens != 1 && !isset($visible)) ? "><IMG SRC='images/modules/tut_form/icotousmodb.gif' border='0' width='30' height='30'".bulle($mmsg_act_lbtts,"","LEFT","ABOVE",150) :
            " onmouseover=\"img14.src='images/modules/tut_form/icotousmodb.gif';overlib('$mmsg_act_lbtts',ol_hpos,LEFT,ABOVE,WIDTH,'150',DELAY,'800',CAPTION, '');\"".
            " onmouseout=\"img14.src='images/modules/tut_form/icotousmod.gif';nd();\">".
            "<IMG NAME=\"img14\" SRC=\"images/modules/tut_form/icotousmod.gif\"  border='0' width='30' height='30'".
            " onLoad=\"tempImg=new Image(0,0); tempImg.src='images/modules/tut_form/icotousmodb.gif'\">";
            $serievar .= "<TD align=center valign='top'>$lediv<A HREF=\"trace.php?link=$lien\" $ico_img <BR>$mess_liste_act_autres</A></DIV></TD>";
         }else
            $serievar .= "<TD align=center valign='top'><IMG SRC='images/modules/tut_form/icotousmod.gif' border='0'><BR><B>$mess_liste_act_autres</B></TD>";
         $requete = "select * from activite where act_seq_no > 0 order by act_nom_lb";
         $act_question = mysql_query ("$requete");
         $nb_acts = mysql_num_rows ($act_question);
         if ($nb_acts > 0)
         {
            $lien="activite_free.php?lesseq=1&medor=1&ordre_affiche=lenom&titre_act=$mess_liste_act_seq_autres";
            $lien = urlencode($lien);
            $lediv = ($lesseq == 1 && $miens != 1 && !isset($keypub)) ?  "<DIV id = 'seqinv'>" : "<DIV id = 'sequence'>";
            $ico_img = ($lesseq == 1 && $miens != 1 && !isset($keypub)) ? "><IMG SRC='images/modules/tut_form/icomodrefb.gif' border='0' width='30' height='30'".bulle($mmsg_act_seq,"","LEFT","ABOVE",150) :
            " onmouseover=\"img15.src='images/modules/tut_form/icomodrefb.gif';overlib('$mmsg_act_seq',ol_hpos,LEFT,ABOVE,WIDTH,'150',DELAY,'800',CAPTION, '');\"".
            " onmouseout=\"img15.src='images/modules/tut_form/icomodref.gif';nd();\">".
            "<IMG NAME=\"img15\" SRC=\"images/modules/tut_form/icomodref.gif\"  border='0' width='30' height='30'".
            " onLoad=\"tempImg=new Image(0,0); tempImg.src='images/modules/tut_form/icomodrefb.gif'\">";
            $serievar .= "<TD align=center valign='top'>$lediv<A HREF=\"trace.php?link=$lien\" $ico_img<BR>$mess_liste_act_seq_autres</A></DIV></TD>";
         }else
            $serievar .= "<TD align=center valign='top'><IMG SRC='images/modules/tut_form/icomodref.gif' border='0'><BR><B>$mess_liste_act_seq_autres</B></TD>";
            $requete = "select * from activite where act_publique_on = 0 order by act_nom_lb";
            $act_question = mysql_query ("$requete");
            $nb_acts = mysql_num_rows ($act_question);
            if ($nb_acts > 0)
            {
               $lien="activite_free.php?medor=1&visible=1&ordre_affiche=lenom&titre_act=$mess_liste_act_prive";
               $lien = urlencode($lien);
               $lediv = ($lesseq != 1 && $visible == 1) ?  "<DIV id = 'seqinv'>" : "<DIV id = 'sequence'>";
               $ico_img = ($lesseq != 1 && $visible == 1) ? "><IMG SRC='images/modules/tut_form/icomodcadb.gif' border='0' width='30' height='30'".bulle($mmsg_act_lb_nd,"","LEFT","ABOVE",150) :
               " onmouseover=\"img16.src='images/modules/tut_form/icomodcadb.gif';overlib('$mmsg_act_lb_nd',ol_hpos,LEFT,ABOVE,WIDTH,'150',DELAY,'800',CAPTION, '');\"".
               " onmouseout=\"img16.src='images/modules/tut_form/icomodcad.gif';nd();\">".
               "<IMG NAME=\"img16\" SRC=\"images/modules/tut_form/icomodcad.gif\"  border='0' width='30' height='30'".
               " onLoad=\"tempImg=new Image(0,0); tempImg.src='images/modules/tut_form/icomodcadb.gif'\">";
               $serievar .= "<TD align=center valign='top'>$lediv<A HREF=\"trace.php?link=$lien\" $ico_img<BR>$mess_liste_act_prive</A></DIV></TD></TR>";
            }
            else
               $serievar .= "<TD align=center valign='top'><IMG SRC='images/modules/tut_form/icomodcad.gif' border='0'><BR><B>$mess_liste_act_prive</B></TD></TR>";
         $affiche = $serievar;
         $affiche .="<TR><TD colspan=3><HR size='1' color='#50ACBE'></TD></TR>";
         $lien= "wiki/wikiAjout.php?id_seq=0&keepThis=true&TB_iframe=true&height=400&width=650";
         $affiche .= "<TR><TD colspan=3><table width=100%><TR>".
                     "<TD valign='top' align='left'><IMG SRC='wiki/images/wikidoc.gif' border='0' style='margin-left:15px;'></TD>".
                     "<TD valign='top' align='left'>";
         $affiche .= '<DIV id="sequence"><a href="'.$lien.'" class="thickbox" title="'.
                     'Ajouter ou modifier un travail à faire en commun(WikiDoc) : <br />Donnez juste les consignes.'.
                     'Les affectations en binôme, trinôme ou groupe se font sur la page de suivi de l\'apprenant." ';
         $affiche .= 'name="Ajouter ou modifier un travail en commun" >'.
                      'Consulter et gérer des WikiDocs</A></DIV></TD></TR></TABLE></TD></TR>';//
         $lienTinCan = "parseurTinCan.php?id_seq=$act_seq&miens=$miens&lesseq=$lesseq&medor=$medor&keydesc=$keydesc&keytitre=$keytitre&keypub=$keypub&prov=act";
         $lien = urlencode($lienTinCan);
         $affiche .= "<TR><TD align='left' colspan=3><table width=100%><TR><TD align='left'>".
                     "<DIV id='seqinv'>&nbsp;&nbsp;&nbsp;&nbsp;<IMG SRC='images/gest_parc/xApi.gif' border='0'>&nbsp;";
         $affiche .= "&nbsp;<A HREF=\"trace.php?link=$lien\">Importer une activité TinCan(xApi)</A></DIV></TD></TR>";
         echo $affiche;
         if ($lg == "fr")
            setlocale(LC_TIME,'fr_FR');
         elseif($lg == "ru")
            setlocale(LC_TIME,'ru_RU');
         if ($requete_act == "")
             $requete_act = "select * from activite where act_auteur_no = $id_user order by act_nom_lb";
         $_SESSION['requete_act'] = $requete_act;
    }


    $query_act = mysql_query ("$requete_act");
    $nb_act = mysql_num_rows($query_act);
    echo "<TR><TD colspan=2><TABLE width=100% cellpadding=0 cellspacing=2>";
    $act_i = 0;
    while ($act_i < $nb_act){
         $act_num = mysql_result ($query_act,$act_i,"act_cdn");
         $act_seq =  mysql_result ($query_act,$act_i,"act_seq_no");
         $act_auteur =  mysql_result ($query_act,$act_i,"act_auteur_no");
         $nom_createur_act = GetDataField ($connect,"select util_nom_lb from utilisateur where util_cdn = $act_auteur","util_nom_lb");
         $prenom_createur_act = GetDataField ($connect,"select util_prenom_lb from utilisateur where util_cdn = $act_auteur","util_prenom_lb");
         $majuscule_act = "$prenom_createur_act $nom_createur_act";
         $date_creat_act = mysql_result ($query_act,$act_i,"act_create_dt");
         $ch_dtc_act = explode("-",$date_creat_act);
         $dtc_act =  strftime("%A %e %B %Y", mktime(0, 0, 0, $ch_dtc_act[1],$ch_dtc_act[2],$ch_dtc_act[0]));
         $date_modif_act = mysql_result ($query_act,$act_i,"act_modif_dt");
         $ch_dtm_act = explode("-",$date_modif_act);
         $dtm_act =  strftime("%A %e %B %Y", mktime(0, 0, 0, $ch_dtm_act[1],$ch_dtm_act[2],$ch_dtm_act[0]));
         $act_titre = mysql_result ($query_act,$act_i,"act_nom_lb");
         $act_ress = mysql_result ($query_act,$act_i,"act_ress_no");
         $droit_voir_act = mysql_result ($query_act,$act_i,"act_publique_on");
         if ($id_ress == 0)
            $ressource = $msq_aucune;
         else
            $ressource = GetDataField ($connect,"select ress_titre from ressource_new where ress_cdn = $act_ress","ress_titre");
         if ($act_auteur == $id_user)
         {
            echo "<TR><TD align='right' valign='top'>";
            if ($droit_voir_act == 0)
               echo "<IMG SRC=\"images/modules/tut_form/icocadenas.gif\" border='0'>";
            echo "&nbsp;<IMG SRC=\"images/gest_parc/icofeuilb.gif\" border='0'>";
         }else
         {
            echo "<TR><TD valign='top' align='right'>-";
            if ($droit_voir_act == 0)
               echo "&nbsp;<IMG SRC=\"images/modules/tut_form/icocadenas.gif\" border='0'>";
         }
         echo xApiShow($act_num);
         echo "</TD>";
         $lien = "activite_free.php?creer=1&modifie_act=1&act_a_modif=$act_num&id_seq=$act_seq&miens=$miens&lesseq=$lesseq&titre_act=$titre_act&medor=$medor&keydesc=$keydesc&keytitre=$keytitre&keypub=$keypub";
         $lien = urlencode($lien);
         $affichage_act = "&nbsp<B>$msq_activite</B><BR>&nbsp;&nbsp;- $mrc_aut : <B>".
                          addslashes($majuscule_act)."</B><BR>&nbsp;&nbsp;- $mess_menu_gest_seq_ref : ".
                          " <B>$dtc_act</B><BR>&nbsp;&nbsp;- $mess_modif_dt : <B>$dtm_act</B>";
         if ($act_a_modif != $act_num){
            if ($act_auteur == $id_user || $typ_user == "ADMINISTRATEUR")
              echo "<TD valign='top'><DIV id='sequence'><A HREF=\"trace.php?link=$lien\" ".
                   "onMouseOver=\"overlib('$affichage_act',ol_hpos,RIGHT,ABOVE,WIDTH,'300',DELAY,'800',CAPTION, '')\" ".
                   "onMouseOut=\"nd()\" title=\"$mess_modif_base\" target='main'>$act_titre</A></DIV></TD></TR>";
            else
              echo "<TD valign='top'><DIV id='sequence'><A HREF=\"javascript:void(0);\" ".
                   "onMouseOver=\"overlib('$affichage_act',ol_hpos,RIGHT,ABOVE,WIDTH,'300',DELAY,'800',CAPTION, '')\" ".
                   "onMouseOut=\"nd()\" target='main'>$act_titre</A></DIV></TD></TR>";
         }else
            echo "<TD valign='top'><DIV id='seqinv'><A HREF=\"javascript:void(0);\" ".
                 "onMouseOver=\"overlib('$affichage_act',ol_hpos,RIGHT,ABOVE,WIDTH,'300',DELAY,'800',CAPTION, '')\" ".
                 "onMouseOut=\"nd()\" target='main'>$act_titre</A></DIV></TD></TR>";
         $act_i++;
    }
    unset($droit_voir_act);
    echo "</TD></TR></TABLE></TD></TR></TABLE></TD></TR></TABLE></TD></TR></TABLE></TD>";

echo "</TABLE></TD>";
?>