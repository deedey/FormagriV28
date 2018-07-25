<?php
//Definition des pre-requis
if (isset($def_prereq) && $def_prereq == 1)
{
      ?>
      <SCRIPT language="JavaScript">
      function checkForm1(frm) {

      var ErrMsg = "<?php echo $mess_info_no;?>\n\n";
      var lenInit = ErrMsg.length;
      if (isEmpty(frm.search)==true)
         ErrMsg += ' - <?php echo $msgNoSeq;?>\n';
      if (isEmpty(frm.search)==false && frm.seq == undefined)
         ErrMsg += ' - <?php echo "La séquence doit être choisie parmi la liste des séquences existantes.";?>\n';
      if (ErrMsg.length > lenInit)
        alert(ErrMsg);
      else
        frm.submit();
      }
      function isEmpty(elm) {
        var elmstr = elm.value + "";
      if (elmstr.length == 0)
       return true;
      return false;
      }
      </SCRIPT>

      <?php
    if (!isset($ext) || (isset($ext) && $ext == ''))
       $id_ref_parc = GetDataField ($connect,"select parcours_referentiel_no from parcours where parcours_cdn = $id_parc","parcours_referentiel_no");
    $letitre =  $msq_definir_prereq;
    GLOBAL $id_prereq,$nb_act_seq;
    echo "<form name='form8' action=\"sequence$ext.php?choix_seq=1&parcours=$parcours&liste=$liste&id_parc=$id_parc&proprio=$proprio&refer=$refer&id_ref=$id_ref&id_ref_parc=$id_ref_parc&id_prereq=$id_prereq&id_seq=$id_seq&acti_seq=$action_seq&nb_act_seq=$nb_act_seq&action_seq=$action_seq&titre_act=$titre_act&note_min=$note_min&note_max=$note_max&ajout_prereq=1&miens=$miens&miens_parc=$miens_parc&parc=$id_parc&liste_act_seq=$liste_act_seq\" target='main' method='post'>";
    if (isset($ext) && $ext == "_entree")
       entete_concept("liste_seq.inc.php",$letitre);
    else
       entete_concept("liste_parc.inc.php",$letitre);
    echo aide_simple("sequence");
    echo "<tr><td style=\"padding-top:30px;\" align='center'><table cellpadding='6' cellspacing='4' border='0'>";
    echo "<TR><TD valign='top'><div id=lelabel style=\"float:left;font-size:12px;padding-left: 120px;\">1 - $msq_typ_prereq :</div>".
         "<div id='leselect' style=\"float:left;padding-left: 40px;\">
         <SELECT name='typ_prereq' class='SELECT' style=\"font-size:13px;font-weight:bold;\" value='$typ_prereq'>
            <OPTION value='SEQUENCE' >$msq_prereq_seq</OPTION>
            <OPTION value='ACTIVITE' >$msq_activite</OPTION>
            <OPTION value='NOTE'>$msq_note</OPTION>
          </SELECT>
          </div></td></tr>";
          echo "<tr><td><div id='lesec' style=\"float:left;padding-left: 120px;font-size:12px;\">2 - $msgchxSeq :</div>";
//         <OPTION value='NOTE'><?php  echo $msq_note</OPTION></TD>".
    echo "<TR><TD style='min-height:80px;height:80px;float:left;padding-left: 120px;' valign='top'>".
         "<b>&nbsp;Faites une recherche de séquence par mot-clef en saisissant 3 caractères au moins</b><br />";
    $table="sequence";
    $fieldLabel="seq_titre_lb";
    $fieldId="seq_cdn";
    $fieldCond=" AND seq_cdn != $id_seq ";// AND seq_auteur_no = $id_user
    $HideLabel="seq";
    include ("OutilsJs/DivPopulator/DivPopulator.php");
    echo "</TD></TR>";
    echo "<TR><TD align='left' height='50' style=\"float:left;padding-left:120px;\">";
    echo "<div id='retour'style=\"float:left;padding-left:12px;padding-right:20px;\">".
         "$bouton_gauche<a href=\"sequence$ext.php?liste=1&consult=1&parcours=$parcours&id_parc=$id_parc&id_ref_seq=$id_ref_seq&id_seq=$id_seq&proprio=$proprio&refer=$refer&id_ref=$id_ref&id_ref_parc=$id_ref_parc&miens=$miens\" target='main'>".
         "$msgRetSeq</a>$bouton_droite</div>";
    echo "<div id='valid' style=\"float:left;padding-left:40px; cursor:pointer;\"><A HREF=\"javascript:checkForm1(document.form8);\" ".
         "onmouseover=\"img1.src='images/fiche_identite/boutvalidb$suffixer.gif';return true;\" ".
         "onmouseout=\"img1.src='images/fiche_identite/boutvalid$suffixer.gif'\">".
         "<IMG NAME=\"img1\" SRC=\"images/fiche_identite/boutvalid$suffixer.gif\" BORDER='0' ".
         "onLoad=\"tempImg=new Image(0,0); tempImg.src='images/fiche_identite/boutvalidb$suffixer.gif'\"></A></div></td></FORM></tr>";
    echo "<tr><td><div id='vld' style=\"float:left;padding-left: 120px;font-size:12px;\">$msgCarSeq </td></tr>";
    echo "<tr><td style=\"float:left;padding-left: 120px;height:10px;\">&nbsp; </td></tr><table>";
    echo fin_tableau('');
    exit;
}//fin if ($def_prereq == 1)

//on choisit les prerequis
if ($choix_seq == 1)
{
      ?>
      <SCRIPT language=JavaScript>
      function checkForm(frm) {
      var ErrMsg = "<?php echo $mess_info_no;?>\n\n";
      var lenInit = ErrMsg.length;
      var ok = 0;
      for (var j = 0; j < document.form1.elements.length; j++)
      {
          if (document.form1.elements[j].type == 'radio' && document.form1.elements[j].checked)
          {
             ok ++;
          }
      }

      if (ok == 0 && isEmpty(frm.act)==true)
        ErrMsg += ' - <?php echo $msq_act_prereq;?>\n';
      if (ErrMsg.length > lenInit)
        alert(ErrMsg);
      else
        frm.submit();
      }
      function isEmpty(elm)
      {
          var elmstr = elm.value + "";
          if (elmstr.length == 0)
              return true;
          return false;
      }
      </SCRIPT>
      <?php
       if (isset($id_parc) && $ext == '')
           $id_ref_parc = GetDataField ($connect,"select parcours_referentiel_no from parcours where parcours_cdn = $id_parc","parcours_referentiel_no");
       if ($acti_seq == 1)
       {
           $lien = "sequence$ext.php?liste=$liste&insert_prereq=1&seq=$seq&typ_prereq=$typ_prereq&parcours=$parcours&id_parc=$id_parc&proprio=$proprio&refer=$refer&id_ref=$id_ref&id_ref_parc=$id_ref_parc&nom=$nom&duree=$duree&id_seq=$id_seq&action_seq=1&nb_act_seq=$nb_act_seq&miens=$miens&miens_parc=$miens_parc";
       }
       elseif ($ajout_prereq == 1)
       {
           $lien = "sequence$ext.php?liste=$liste&aff_prereq=1&typ_prereq=$typ_prereq&parcours=$parcours&id_parc=$id_parc&proprio=$proprio&refer=$refer&seq=$seq&id_seq=$id_seq&id_ref=$id_ref&id_ref_parc=$id_ref_parc&insert_prereq=1&miens=$miens&miens_parc=$miens_parc&parc=$id_parc&liste_act_seq=$liste_act_seq";
       }
       else
       {
           $lien = "sequence$ext.php?liste=$liste&proprio=$proprio&refer=$refer&seq=$seq&typ_prereq=$typ_prereq&id_ref=$id_ref&id_ref_parc=$id_ref_parc&id_seq=$id_seq&miens=$miens&miens_parc=$miens_parc&parc=$id_parc&liste_act_seq=$liste_act_seq";
       }
       if ($typ_prereq == 'ACTIVITE' || $typ_prereq == 'NOTE')
       {
         $letitre =  $msq_definir_prereq;
         if ($ext == "_entree")
            entete_concept("liste_seq.inc.php",$letitre);
         else
            entete_concept("liste_parc.inc.php",$letitre);
         echo aide_simple("sequence");

         echo "<form name='form1' action=\"$lien\" target='main' method='post'>";
         echo "<TR>";
         echo "<TD nowrap valign='top' colspan=2 style=\"font-weight:bold; padding:15px;\">";
         $titre_seq = GetDataField ($connect,"select seq_titre_lb from sequence where seq_cdn = $seq","seq_titre_lb");
         echo "<tr><td style=\"padding-top:20px;\" align='center'><table cellpadding='8' cellspacing='4' border='0'>";
         echo "<TR><TD valign='top' style=\"float:left; font-size:12px;\"><div id='chxact'>4 - $msgchXAct :</div></td></tr>";
         echo "<TR><TD nowrap style=\"font-size:12px; padding-left:10px;\">";
         if ( $typ_prereq == 'ACTIVITE')
         {
              $titre_seq = GetDataField ($connect,"select seq_titre_lb from sequence where seq_cdn = $seq","seq_titre_lb");
              $mess_notif = "$msgchxseqOk : $titre_seq";
              echo notifier($mess_notif); $mess_notif="";
              $req = requete_order("*","activite","act_seq_no = $seq","act_ordre_nb");
              if ($req == FALSE)
              {
                 echo "Il n'y a aucune activité dans cette sequence";
                 echo fin_tableau('');
                 exit;
              }
              while ($item = mysql_fetch_object($req))
              {
                  $num_act = $item->act_cdn;
                  $nom_act = $item->act_nom_lb;
                  echo "<input type='radio' name='act' value='".$num_act."'style=\"font-size:11px;\"> &nbsp;&nbsp;<small>$nom_act</small><br/><option>";
              }
              //Ascenseur ("act","select act_cdn,act_nom_lb from activite where act_seq_no = $seq AND (act_publique_on = 1 OR (act_publique_on = 0 AND act_auteur_no = $id_user)) order by act_cdn",$connect,$param);
           }
           elseif ( $typ_prereq == 'NOTE')
           {
              Ascenseur ("act","select act_cdn,act_nom_lb from activite where act_notation_on= 'OUI' AND act_seq_no = $seq AND (act_publique_on = 1 OR (act_publique_on = 0 AND act_auteur_no = $id_user)) order by act_cdn",$connect,$param);
              echo "<BR></TD></TR>";
              echo "<TR><TD nowrap style=\"font-size:12px; padding-left:10px;\">$msq_note_min</TD>";
              echo "<TD nowrap><INPUT TYPE='TEXT' class='INPUT'  name='note_min' align='middle'><br></TD></TR>";
              echo "<TR><TD nowrap style=\"font-size:12px; padding-left:10px;\">$msq_note_max</TD>";
              echo "<TD nowrap>";
              echo "<INPUT TYPE='TEXT' class='INPUT'  name='note_max' align='middle'><br></TD></TR>";
         }//fin if ($typ_prereq == 'NOTE')
            //echo boutret(1,0);
             echo "<tr><td style=\"float:left;font-size:12px;\">5 - ".
                  "$msqVld</td></tr>" ;
            echo "<tr><td style=\"float:left;padding-left:10px;\">".
                 "<A HREF=\"javascript:checkForm(document.form1);\" ".
                 " onmouseover=\"img1.src='images/fiche_identite/boutvalidb$suffixer.gif';return true;\"".
                 " onmouseout=\"img1.src='images/fiche_identite/boutvalid$suffixer.gif'\">".
                 "<IMG NAME=\"img1\" SRC=\"images/fiche_identite/boutvalid$suffixer.gif\" BORDER='0' ".
                 "onLoad=\"tempImg=new Image(0,0); tempImg.src='images/fiche_identite/boutvalidb$suffixer.gif'\"></A>";
            echo "</td></FORM></tr>";
            $lien = "sequence$ext.php?liste=$liste&def_prereq=1&parcours=$parcours&id_parc=$id_parc&proprio=$proprio&refer=$refer&id_ref=$id_ref&id_ref_parc=$id_ref_parc&miens=$miens&miens_parc=$miens_parc&parc=$id_parc&liste_act_seq=$liste_act_seq";
            $lien = urlencode($lien);
            echo "<tr><td style=\"float:left;\"><div id='btn1' style=\"float:left;padding-left:10px; padding-right:60px;\">$bouton_gauche<A href=\"trace.php?link=$lien\" target='main'>$msq_ret_page_prec</A>$bouton_droite</div>";
            $lien = "sequence$ext.php?liste=1&consult=1&parcours=$parcours&id_parc=$id_parc&proprio=$proprio&refer=$refer&id_ref=$id_ref&id_ref_parc=$id_ref_parc&id_seq=$id_seq&miens=$miens&miens_parc=$miens_parc&parc=$id_parc&liste_act_seq=$liste_act_seq";
            $lien = urlencode($lien);
            echo "<div id='btn2' style=\"float:left;\">$bouton_gauche<A href=\"trace.php?link=$lien\" target='main'>$msq_detail_seq</A>$bouton_droite</div>";
            //$lien = "sequence.php?liste=$liste&choix_ref=1&id_ref=$id_ref&id_ref_parc=$id_ref_parc&id_parc=$id_parc&proprio=$proprio&refer=$refer&miens=$miens&miens_parc=$miens_parc&parc=$id_parc&liste_act_seq=$liste_act_seq";
            //$lien = urlencode($lien);
            //echo "<TD nowrap>$bouton_gauche<a href=\"trace.php?link=$lien\" target='main'>$msq_revenir_crea_seq</a>$bouton_droite</TD>";
            echo "</td></TR></TABLE>";
            echo fin_tableau('');
            exit;
       } //fin if ($typ_prereq == 'ACTIVITE' ...)
       else
       {
          $lien = urlencode($lien);
          echo "<script language=\"JavaScript\">";
          echo "document.location.replace(\"trace.php?link=$lien\")";
          echo "</script>";
          exit();
        }
} //fin if ($choix_seq == 1)

//Affichage des prérequis concernant une séquence a partir du lien dans le tableau d'affichage des sequences
if ($aff_prereq == 1)
{
    if ($supp == 1)
        $del_prereq = mysql_query ("delete from prerequis where prereq_cdn = $id_prereq");
    //Modification des prérequis
    if ($modif == 1)
    {
        $upd_prereq = mysql_query ("update prerequis set prereq_seq_no = $id_seq, prereq_typcondition_lb = \"$typ_prereq\", prereq_seqcondition_no = $seq where prereq_cdn = $id_prereq");
        if ($typ_prereq == 'ACTIVITE' || $typ_prereq == 'NOTE')
            $upd_prereq2 = mysql_query ("update prerequis set prereq_actcondition_no = $act where prereq_cdn = $id_prereq");
        else
            $upd_prereq2 = mysql_query ("update prerequis set prereq_actcondition_no = 'NULL' where prereq_cdn = $id_prereq");
        if ($typ_prereq == 'NOTE')
            $upd_prereq3 = mysql_query ("update prerequis set prereq_notemin_nb1 = $note_min,prereq_notemax_nb1 = $note_max where prereq_cdn = $id_prereq");
        else
            $upd_prereq3 = mysql_query ("update prerequis set prereq_notemin_nb1 = 'NULL',prereq_notemax_nb1 = 'NULL' where prereq_cdn = $id_prereq");
    }//fin if ($modif == 1)

    //insertion des pre-requis
    if ($insert_prereq == 1)
    {
       $titre_seq = GetDataField ($connect,"select seq_titre_lb from sequence where seq_cdn = $id_seq","seq_titre_lb");
       if ($typ_prereq == "SEQUENCE")
       {
          $nbr_prq = mysql_result(mysql_query("select count(*) from prerequis where prereq_typcondition_lb='SEQUENCE' and prereq_seqcondition_no = '$seq' and prereq_seq_no = $id_seq"),0);
          if ($nbr_prq > 0)
          {
             $mess_notif = $titre_seq." ".$msg_DjaPrq;
             echo notifier($mess_notif); $mess_notif="";
             $passe++;
          }
       }
       if ($typ_prereq == "ACTIVITE")
       {
          $nbr_prq = mysql_result(mysql_query("select count(*) from prerequis where prereq_typcondition_lb='ACTIVITE' and prereq_seqcondition_no = '$seq' and prereq_actcondition_no = '$act' and prereq_seq_no = $id_seq"),0);
          if ($nbr_prq > 0)
          {
             $titre_act = GetDataField ($connect,"select act_nom_lb from activite where act_cdn = $act","act_nom_lb");
             $mess_notif = $titre_act." ".$msg_DjaPrq;
             echo notifier($mess_notif); $mess_notif="";
             $passe++;
          }
       }
     if (!isset($passe) || (isset($passe) && $passe == 0))
     {
        if ($typ_prereq == "ACTIVITE")
        {
          $titre_act = GetDataField ($connect,"select act_nom_lb from activite where act_cdn = $act","act_nom_lb");
          $mess_notif = $titre_act." : ".$msgActSeqReq." : ".$titre_seq;
          echo notifier($mess_notif); $mess_notif="";
        }
        elseif ($typ_prereq == "SEQUENCE")
        {
          $titre_newseq = GetDataField ($connect,"select seq_titre_lb from sequence where seq_cdn = $seq","seq_titre_lb");
          $mess_notif = "$msgChxSeq : $titre_newseq";
          echo notifier($mess_notif); $mess_notif="";
        }
        $id_prereq = Donne_ID ($connect,"select max(prereq_cdn) from prerequis");
        $prerequis = mysql_query ("insert into prerequis(prereq_cdn,prereq_seq_no,prereq_typcondition_lb,prereq_seqcondition_no) values ($id_prereq,$id_seq,'$typ_prereq',$seq)");
        $upd_act_prereq = mysql_query ("update prerequis set prereq_actcondition_no = $act where prereq_cdn = $id_prereq");
        $upd_note_prereq = mysql_query ("update prerequis set prereq_notemin_nb1 = $note_min,prereq_notemax_nb1 = $note_max where prereq_cdn = $id_prereq");
      }
    }
    //on est oblige d'effectuer a nveau la req car on ne peut pas passer l'id de la requete par URL
    $seq_prereq = mysql_query ("select * from prerequis where prereq_seq_no = $id_seq");
    $nb_prereq = mysql_num_rows ($seq_prereq);
    if (isset($id_parc) && $ext == '')
       $id_ref_parc = GetDataField ($connect,"select parcours_referentiel_no from parcours where parcours_cdn = $id_parc","parcours_referentiel_no");

    $letitre = "$msq_prereq_seq : ".stripslashes($titre);
    if ($ext == "_entree")
       entete_concept("liste_seq.inc.php",$letitre);
    else
       entete_concept("liste_parc.inc.php",$letitre);
    echo aide_simple("sequence");
    if ($nb_prereq == 0)
    {
      echo "<TR><TD class='sous-titre'>$msq_no_prereq</TD></TR>";
    }
    else
    {
      GLOBAL $nom,$nb_act_seq,$ajout_prereq,$id_prereq;
      echo "<form action=\"sequence$ext.php?choix_seq=1&parcours=$parcours&liste=$liste&id_parc=$id_parc&proprio=$proprio&refer=$refer&id_ref=$id_ref&id_ref_parc=$id_ref_parc&id_prereq=$id_prereq&id_seq=$id_seq&acti_seq=$action_seq&nom=$nom&duree=$duree&nb_act_seq=$nb_act_seq&action_seq=$action_seq&titre_act=$titre_act&note_min=$note_min&note_max=$note_max&ajout_prereq=$ajout_prereq&miens=$miens&miens_parc=$miens_parc&parc=$id_parc&liste_act_seq=$liste_act_seq\" target='main' method='post'>";
      echo "<TR><TD><table width='100%' cellpadding=4 cellspacing=1>";
      echo "<tr bgcolor='#2b677a'>";
      echo "<td width='20%' align='left'><FONT COLOR=white><b>$msq_typ_prereq</b></FONT></td>";
      echo "<td width='40%' align='left'><FONT COLOR=white><b>$msq_seq</b></FONT></td>";
      echo "<td width='40%' align='left'><FONT COLOR=white><b>$msq_activite</b></FONT></td>";
//      echo "<td width='5%' align='left'><FONT COLOR=white><b>$mess_modif_base</b></FONT></td>";
      echo "<td width='5%' align='left'><FONT COLOR=white><b>$mess_ag_supp</b></FONT></td>";
      //echo "<td width='15%' height='13' align='middle'><FONT COLOR=white><b>$msq_note_min</b></FONT></td>";
      //echo "<td width='15%' height='22' align='middle'><FONT COLOR=white><b>$msq_note_max</b></FONT></td>";
      echo "</tr>";
      $i = 0;
      while ($i != $nb_prereq)
      {
           $typ_prereq = mysql_result ($seq_prereq,$i,"prereq_typcondition_lb");
           $seq = mysql_result ($seq_prereq,$i,"prereq_seqcondition_no");

           $titre_seq = GetDataField ($connect,"select seq_titre_lb from sequence where seq_cdn = $seq","seq_titre_lb");
           if ($typ_prereq == 'ACTIVITE' || $typ_prereq == 'NOTE')
           {
               $act = mysql_result ($seq_prereq,$i,"prereq_actcondition_no");
               $titre_act = GetDataField ($connect,"select act_nom_lb from activite where act_cdn = $act","act_nom_lb");
           }//fin if ($typ_preq === 'ACTIVITE' .....)
           else
           {
                 $titre_act = $msq_tts_seq_prereq;
                 $note_min = $msq_pas_defini;
                 $note_max = $msq_pas_defini;
           }
           if ($typ_prereq == 'ACTIVITE')
           {
               $note_min = $msq_pas_defini;
               $note_max = $msq_pas_defini;
           }
           if ($typ_prereq == 'NOTE')
           {
               $note_min = mysql_result ($seq_prereq,$i,"prereq_notemin_nb1");
               $note_max = mysql_result ($seq_prereq,$i,"prereq_notemax_nb1");
           }
           echo couleur_tr($i+1,'');
           echo "<td>$typ_prereq</td>";

           //On passe certaines infos par URL, cela evite de rafaire une requete
           if (isset($venir) && $venir == "g_p")
              echo "<td >".html_entity_decode($titre_seq,ENT_QUOTES,'ISO-8859-1')."</td>";
           else
           {
              $lien="sequence$ext.php?liste=$liste&utilisateur=$utilisateur&consult_act=1&parcours=$parcours&id_parc=$id_parc&proprio=$proprio&refer=$refer&id_seq=$seq&id_ref=$id_ref&id_ref_parc=$id_ref_parc&miens=$miens&miens_parc=$miens_parc";
              $lien = urlencode($lien);
              echo "<td><a href=\"trace.php?link=$lien\" target='main'>".html_entity_decode($titre_seq,ENT_QUOTES,'ISO-8859-1')."</a></td>";
           }
           if ($typ_prereq == 'ACTIVITE' || $typ_prereq == 'NOTE')
           {
               //On a besoin de ressource pour afficher details sur activite
               $id_ress = GetDataField ($connect,"select act_ress_no from activite where act_cdn = $act","act_ress_no");
               $lien="sequence$ext.php?liste=$liste&utilisateur=$utilisateur&parcours=$parcours&consult_act=1&id_parc=$id_parc&id_seq=$seq&proprio=$proprio&refer=$refer&choix_ress=1&id_act=$act&id_ref=$id_ref&id_ref_parc=$id_ref_parc&id_ress=$id_ress&miens=$miens&miens_parc=$miens_parc";
               $lien = urlencode($lien);
               echo "<td>".html_entity_decode($titre_act,ENT_QUOTES,'ISO-8859-1')."</td>";
           }
           else
              echo "<td>".html_entity_decode($titre_act,ENT_QUOTES,'ISO-8859-1')."</td>";
/*          echo "<td align='middle'>$note_min</td>";
          echo "<td align='middle'>$note_max</td>";
*/
          $id_auteur = GetDataField ($connect,"select seq_auteur_no from sequence where seq_cdn = $id_seq","seq_auteur_no");
          $id_prereq = mysql_result ($seq_prereq,$i,"prereq_cdn");
//          $seq_preq = mysql_query ("select * from suivi2 where suiv2_seq_no = $seq && suiv2_etat_lb != 'A FAIRE'");
/*          $nombre_seq = mysql_num_rows($seq_preq);
          if (($typ_user == "ADMINISTRATEUR" || $typ_user == "RESPONSABLE_FORMATION" || $id_auteur == $id_user) && $venir !="g_p")
          {
             $lien="sequence$ext.php?liste=$liste&action_prereq=1&parcours=$parcours&id_parc=$id_parc&proprio=$proprio&refer=$refer&id_prereq=$id_prereq&id_seq=$id_seq&titre_seq=$titre_seq&titre_act=$titre_act&note_min=$note_min&note_max=$note_max&id_ref=$id_ref&id_ref_parc=$id_ref_parc&seq=$seq&typ_prereq=$typ_prereq&miens=$miens&miens_parc=$miens_parc&parc=$id_parc&liste_act_seq=$liste_act_seq";
             $lien = urlencode($lien);
             echo "<td width='5%'><a href=\"trace.php?link=$lien\" target='main'><IMG SRC=\"images/repertoire/icoGrenomfich20.gif\" height=\"20\" width=\"20\" TITLE=\"$msq_modif_prereq\" BORDER=0></A></td>";
          }
          //elseif ($nombre_seq > 0)
          //  echo "<td width='5%' align='middle'><IMG SRC=\"images/repertoire/icoptiinterdit.gif\" height=\"15\" width=\"15\" TITLE=\"$msq_seq_entamee\" border=0></TD>";
          $seq_preq = mysql_query ("select * from suivi2 where suiv2_seq_no = $seq");
          $nombre_seq = mysql_num_rows($seq_preq);
          $interdit = 0;
          if ($nombre_seq >0){
             $esp = 0;
             while ($esp < $nombre_seq){
               $etat_seq_prereq = mysql_result($seq_preq,$esp,"suiv2_etat_lb");
               if ($etat_seq_prereq != 'A FAIRE' && $venir !="g_p"){
                  echo "<td width='5%' align='middle'><IMG SRC=\"images/repertoire/icoptiinterdit.gif\" height=\"15\" width=\"15\" TITLE=\"$msq_seq_entamee\" border=0></TD>";
                  $interdit=1;
                  break;
               }
               $esp++;
             }
          }
*/
          if (($typ_user == "ADMINISTRATEUR" || $typ_user == "RESPONSABLE_FORMATION" || $id_auteur == $id_user) &&
             ((isset($venir) && $venir !="g_p") || !isset($venir)) && ((isset($interdit) && $interdit == 0) || !isset($interdit)))
          {
//          if ($interdit == 0 && $venir !="g_p" && ($typ_user != "TUTEUR" && $typ_user != "APPRENANT")){
             $lien="sequence$ext.php?liste=$liste&aff_prereq=1&parcours=$parcours&id_parc=$id_parc&proprio=$proprio&refer=$refer&supp=1&id_prereq=$id_prereq&id_ref=$id_ref&id_ref_parc=$id_ref_parc&id_seq=$id_seq&miens=$miens&miens_parc=$miens_parc&parc=$id_parc&liste_act_seq=$liste_act_seq";
             $lien = urlencode($lien);
             echo "<td width='5%' align='center'><a href=\"javascript:void(0);\" ".
                  "onclick=\"javascript:return(confm('trace.php?link=$lien'));\" target='main'>".
                  "<IMG SRC=\"images/messagerie/icopoubelressour.gif\" TITLE=\"$msq_sup_prereq\" BORDER=0></A></td>";
          }
          echo "</tr>";
          $i++;
      }  //fin while ($i != $nb_prereq)
      echo "</table></center><br><br>";
    } //fin else ($nb_prereq == 0)
    echo "</TD></FORM></TR></TD></TR><TR><TD colspan='2'>";
    echo "<TABLE border=0 cellpadding='4' cellspacing = '4'><TR>";
      if ((!isset($venir) || isset($venir) && $venir != "g_p") && $typ_user != "TUTEUR" && $typ_user != "APPRENANT")
      {
        $lien="sequence$ext.php?liste=$liste&ajout_seq_prereq=1&def_prereq=1&parcours=$parcours&id_parc=$id_parc&proprio=$proprio&refer=$refer&id_seq=$id_seq&id_ref=$id_ref&id_ref_parc=$id_ref_parc&ajout_prereq=1&miens=$miens&miens_parc=$miens_parc&parc=$id_parc&liste_act_seq=$liste_act_seq";
        $lien = urlencode($lien);
        if ($typ_user == "ADMINISTRATEUR" || $typ_user == "RESPONSABLE_FORMATION" || $id_auteur == $id_user)
           echo "<TD align='left' nowrap>$bouton_gauche<a href=\"trace.php?link=$lien\" target='main'>$msq_ajout_prereq</a>$bouton_droite</TD>";
        $lien="sequence$ext.php?liste=1&consult=1&parcours=$parcours&id_parc=$id_parc&proprio=$proprio&refer=$refer&id_ref=$id_ref&id_ref_parc=$id_ref_parc&id_seq=$id_seq&miens=$miens&miens_parc=$miens_parc&parc=$id_parc&liste_act_seq=$liste_act_seq";
        $lien = urlencode($lien);
        echo "<TD align='left' nowrap>$bouton_gauche<a href=\"trace.php?link=$lien\" target='main'>$msq_consult_seq</a>$bouton_droite</TD>";
/*
        if ($parcours)
        {
           $lien = "parcours.php?liste=$liste&consult=1&parcours=$parcours&id_parc=$id_parc&proprio=$proprio&refer=$refer&id_ref=$id_ref&id_ref_parc=$id_ref_parc&parc=$id_parc&liste_act_seq=$liste_act_seq";
           $lien = urlencode($lien);
           echo "<TD align='left' nowrap>$bouton_gauche<a href=\"trace.php?link=$lien\" target='main'>$msq_voir_parc</a>$bouton_droite</TD>";
        }//fin if ($parcours)
*/
      }
      else
      {
        $lien="gest_parc.php?ret=1&utilisateur=$utilisateur&a_faire=1&parcours=$parcours&liste=$liste&vp=1&seq_ouverte=$seq_ouverte&parc_ouvert=$parc_ouvert&id_parc=$id_parc&proprio=$proprio&refer=$refer&id_ref=$id_ref&id_ref_parc=$id_ref_parc&id_seq=$id_seq&parc=$id_parc&liste_act_seq=$liste_act_seq";
        $lien = urlencode($lien);
        echo "<TD align='left' nowrap>$bouton_gauche<a href=\"trace.php?link=$lien\" target='main'>$mpr_ret_suiv</a>$bouton_droite</TD>";
      }
      echo "</TR></TABLE>";
      echo fin_tableau($html);
  exit;

} //fin if ($aff_prereq == 1)
?>
