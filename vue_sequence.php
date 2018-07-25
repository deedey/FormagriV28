<?php
if (!isset($_SESSION)) session_start();
include ("include/UrlParam2PhpVar.inc.php");
if (!isset($_SESSION['id_user']) || $_SESSION['id_user'] == "")
{
  exit();
}
require "graphique/admin.inc.php";
require 'fonction.inc.php';
require "lang$lg.inc.php";
dbConnect();
$bkg = GetDataField($connect,"select param_etat_lb from param_foad where param_typ_lb = 'bkg'","param_etat_lb");
?>
<HTML>
<HEAD>
<STYLE>
body {
scrollbar-face-color: #ffffff;
scrollbar-shadow-color: #ffffff;
scrollbar-highlight-color: #ffffff;
scrollbar-3dlight-color: #ffffff;
scrollbar-darkshadow-color: #ffffff;
scrollbar-track-color: #27909F;
scrollbar-arrow-color: #6f6f6f;
}
BODY { font-family: arial; font-size: 12px; color: #333333 }
TD   { font-family: arial; font-size: 12px; color: #333333 }
TH   { font-family: arial; font-size: 12px; color: #333333 }
A         {font-family:arial;font-size:12px;color:#24677A;text-decoration:none}
A:link    {font-family:arial;font-size:12px;color:#24677A;font-weight:bold}
A:visited {font-family:arial;font-size:12px;color:#24677A;font-weight:bold}
A:hover   {font-family:arial;font-size:12px;color=#D45211;font-weight:bold}
A.off     {font-family:arial;font-size:12px;color:##24677A;font-weight:bold}

#titre A:link{background-repeat:no-repeat;background-position:1% 50%;color:#3BACC4;}
#titre A:visited{background-repeat:no-repeat;background-position:1% 50%;color:#3BACC4;}
#titre A:hover {background-repeat:no-repeat;background-position:1% 50%;color:#D45211;}

#menu A:link{background-repeat:no-repeat;background-position:1% 50%;font-family:arial;font-size:12px;color:#002D44;}
#menu A:visited{background-repeat:no-repeat;background-position:1% 50%;font-family:arial;font-size:12px;color:#002D44;}
#menu A:hover {background-repeat:no-repeat;background-position:1% 50%;font-family:arial;font-size:12px;color:#D45211;}

#sequence A:link{background-repeat:no-repeat;background-position:1% 50%;font-family:arial;font-size:12px;color:#24677A;}
#sequence A:visited{background-repeat:no-repeat;background-position:1% 50%;font-family:arial;font-size:12px;color:#24677A;}
#sequence A:hover {background-repeat:no-repeat;background-position:1% 50%;font-family:arial;font-size:12px;color:#D45211;}

</STYLE>
<TITLE>Formagri</TITLE>
</HEAD>
<?php
if (strstr(getenv("HTTP_USER_AGENT"),"MSIE"))
  $i_body ="<BODY bgcolor='#FFFFFF' marginwidth='0' marginheight='0' leftmargin='0' topmargin='0'";
else
  $i_body ="<BODY bgcolor='#FFFFFF' marginwidth='0' marginheight='0' leftmargin='0' topmargin='0'";
$i_body .= ">";
echo $i_body;
?>
  <div id="overDiv" style="position:absolute; visibility:hiden;z-index:1000;"></div>
  <SCRIPT LANGUAGE="JavaScript" SRC="overlib.js"><!-- overLIB (c) Erik Bosrup --></SCRIPT>
<?php
$act_query = mysql_query ("select * from activite where act_seq_no = $seq  order by activite.act_ordre_nb");
$Nb_act_seq = mysql_num_rows ($act_query);
if ($Nb_act_seq == 0){
   echo "<script language=\"JavaScript\">";
   echo "setTimeout(\"Quit()\",1500);
        function Quit() {
          self.opener=null;self.close();return false;
        }";
   echo "</script>";
   exit();
}else {
   $titre_sequence = GetDataField ($connect,"select seq_titre_lb from sequence where seq_cdn = '$seq'","seq_titre_lb");
   $nom_sequence = GetDataField ($connect,"select seq_desc_cmt from sequence where seq_cdn = '$seq'","seq_desc_cmt");
   $ordre_sequence = GetDataField ($connect,"select seq_ordreact_on from sequence where seq_cdn = '$seq'","seq_ordreact_on");
   if ($nom_sequence == '')
      $nom_sequence = $msq_mess_no_titre;
   $bgcolor1 = '#F4F4F4';
   $bgcolor2 = '#FFFFFF';
   echo "<TABLE><TR><TD width='8'>&nbsp;</TD><TD>";
   echo "<TABLE><TR><TD><font size='3'><B>$msq_seq : <font size='4'>$titre_sequence </B></FONT><P></TD></TR></TABLE>";
   echo "<TABLE><TR><TD>";
   $prerequis="";
   $prereq_query = mysql_query ("SELECT * from prerequis WHERE prereq_seq_no = $seq");
   $nb_prereq = mysql_num_rows ($prereq_query);
   $jj = 0;
   while ($jj < $nb_prereq){
      //on raisonne selon le type de condition
      $type_condition = mysql_result ($prereq_query,$jj,"prereq_typcondition_lb");
      if ($type_condition == 'SEQUENCE') {
         $condition = mysql_result ($prereq_query,$jj,"prereq_seqcondition_no");
         $nom_seq_req = GetDataField ($connect,"select seq_titre_lb from sequence where seq_cdn = $condition","seq_titre_lb");
         $prerequis .= "<BR>- $msq_seq : $nom_seq_req";
      }
      //on a besoin du numero de l'activite pour recuperer les notes
      if ($type_condition == 'ACTIVITE') {
         $condition = mysql_result ($prereq_query,$jj,"prereq_actcondition_no");
         $nom_act_req = GetDataField ($connect,"select act_nom_lb from activite where act_cdn = $condition","act_nom_lb");
         $nom_seq_req = GetDataField ($connect,"select sequence.seq_titre_lb from sequence,activite where sequence.seq_cdn = activite.act_seq_no and activite.act_cdn = $condition","sequence.seq_titre_lb");
         $prerequis .="<BR>- $msq_activite : \"$nom_act_req\" de la ".strtolower($msq_seq)." \"$nom_seq_req\"";
      }
      if ($type_condition == 'NOTE') {
         $condition = mysql_result ($prereq_query,$jj,"prereq_actcondition_no");
         $note_min = mysql_result ($prereq_query,$jj,"prereq_notemin_nb1");
         $note_max = mysql_result ($prereq_query,$jj,"prereq_notemax_nb1");
         $prerequis .= $mess_note_prq."<BR>";
      }
    $jj++;
   }
   if ($prerequis != "")
      $alerter .= "<B>$msq_si_prereq</B>$prerequis";
   if ($alerter != ""){
      echo "<TD width='4%' align='left' valign='top'><font size = '2'><IMG SRC='images/gest_parc/icoflashb.gif' border='0'></TD>".
           "<TD width='60%' align='left' valign='top'><TABLE border='0' cellspacing='0' cellpadding='0' width='100%'><TR>";
      echo "<TD width='6'>&nbsp;</TD><TD valign='top'><font size='2'>$alerter</FONT></TD></TR></TABLE>";
      echo "";
   }
   // Chercher les ressources bibliographiques préconisées par le formateur
   $sql = mysql_query ("SELECT * from favoris where fav_seq_no = '$seq'");
   $nbr_fav = mysql_num_rows($sql);
   if ($nbr_fav > 0){
      $lien = "#";
      $lien = urlencode($lien);
      echo "</TD><TD width='2%' align='left' valign='top'><A href=\"#\" title=\"$msq_voir_fav\" ".
           "onmouseover=\"img_conseil.src='images/gest_parc/icoconseilb.gif';return true;\" ".
           "onmouseout=\"img_conseil.src='images/gest_parc/icoconseil.gif'\">";
      echo "<IMG NAME=\"img_conseil\" SRC=\"images/gest_parc/icoconseil.gif\" BORDER='0' ".
           "onLoad=\"tempImg=new Image(0,0); tempImg.src='images/gest_parc/icoconseilb.gif'\"></A></TD>";
      echo "<TD width='33%' align='left' valign='top'><TABLE border='0' cellspacing='0' cellpadding='0' width='100%'><TR>";
      echo "<TD width='6'>&nbsp;&nbsp;</TD><TD valign='top'><DIV id='sequence'><A href=\"#\" title=\"$msq_voir_fav\">".
           "<font size='2'><B>$mess_menu_prescrites</B></FONT></A></DIV></TD></TR></TABLE>";
   }
   if ($ordre_sequence == "OUI")
      echo "</TD></TR></TABLE><BR><TABLE><TR><TD colspan='4'><font size='2'><B>$mess_seq_ordre</B></FONT></TD></TR></TABLE>";
   else
      echo "</TD></TR></TABLE><BR>";
   echo "<TABLE width='100%' bgColor='#298CA0' cellspacing='2' cellpadding='0'><TR><TD>";
   echo "<TABLE width='100%'  bgcolor='#FFFFFF' cellspacing='1' cellpadding='3'>";
   echo "<TR height='34'>";
   echo "<TD align='left' background=\"images/fond_titre_table.jpg\"><FONT COLOR=white size='2'><b>$msq_activite : ".strtolower($mess_fav_tit)."</b></FONT></TD>";
   echo "<TD  align='left' background=\"images/fond_titre_table.jpg\"><FONT COLOR=white size='2'><b>$msq_consigne_act_form</b></FONT></TD>";
   echo "<TD align='left' background=\"images/fond_titre_table.jpg\"><FONT COLOR=white size='2'><b>$mess_demarrer</b></FONT></TD>";
   echo "<TD align='left' background=\"images/fond_titre_table.jpg\"><FONT COLOR=white size='2'><b>$msq_etat</b></FONT></TD>";
   echo "<TD align='left' background=\"images/fond_titre_table.jpg\"><FONT COLOR=white size='2'><b>$msq_tit_label</b></FONT></TD>";
   //On selectionne le type d'utilisateur (uniquement si ce n'est pas un apprenant ki vient consulter ses activites)car seul l'administrateur et l'auteur de l'activite ont le droit de modifier ou supprimer celle-ci
   $bgcolorC = '#F2EBDC';
   $ii = 0;
   while ($ii != $Nb_act_seq) {
     $id = mysql_result ($act_query,$ii,"act_cdn");
     $nom = mysql_result ($act_query,$ii,"act_nom_lb");
     $ordre[$ii] = mysql_result ($act_query,$ii,"act_ordre_nb");
     $consigne = mysql_result ($act_query,$ii,"act_consigne_cmt");
     $commentaire = mysql_result ($act_query,$ii,"act_commentaire_cmt");
     $ress_ok = mysql_result ($act_query,$ii,"act_ress_on");
     $pass_mult = mysql_result ($act_query,$ii,"act_passagemult_on");
     $acquit = mysql_result ($act_query,$ii,"act_acquittement_lb");
     $devoir = mysql_result ($act_query,$ii,"act_devoirarendre_on");
     $notation = mysql_result ($act_query,$ii,"act_notation_on");
     $flag = mysql_result ($act_query,$ii,"act_flag_on");
     $duree = mysql_result ($act_query,$ii,"act_duree_nb");
     if ($commentaire != "")
        $commentaire1 =addcslashes($commentaire,"\0..\47!@\176..\255");
     else
        $commentaire1 = $mess_no_comment;
     if (!$consigne)
        $consigne = $msq_aucune;
     //Séléction ressource
     $id_ress = mysql_result ($act_query,$ii,"act_ress_no");
     if ($id_ress > 0)
        $lien_ressource = GetDataField ($connect,"select ress_url_lb from ressource_new where ress_cdn = $id_ress","ress_url_lb");
     $lien_ress = str_replace("&","%",$lien_ressource);
     if (strstr($lien_ress,"qcm.php"))
        $non_affic_ress_lien = 0;
     if ($id_ress == 0){
        $non_affic_ress_lien = 1;
        $ressource = $msq_aucune;
     }else{
        $ressource = GetDataField ($connect,"select ress_titre from ressource_new where ress_cdn = $id_ress","ress_titre");
        //Dans le cas ou la ressource a ete supprimee
        if ($ressource == ''){
          $ressource = "<B>$msq_ress_sup</B>";
          $non_affic_ress_lien = 1;
        }else {
          $typ_ress = GetDataField ($connect,"select ress_support from ressource_new where ress_cdn = $id_ress","ress_support");
          $typ_ress = strtoupper ($typ_ress);
        }
     } //fin else

     //Pour meme raison que typ_user, on selectionne auteur
     $id_auteur = GetDataField ($connect,"select seq_auteur_no from sequence where seq_cdn = $seq","seq_auteur_no");
     //On doit savoir s'il doit effectuer activites ds l'ordre ou non pour activer ou non le lien
     $ordre_act = GetDataField ($connect,"select seq_ordreact_on from sequence where seq_cdn = $seq","seq_ordreact_on");
     //On connait l'ordre de l'activite courante  ($ordre)
     if ($ordre[$ii] > 1 && $ii > 0) {
           //On recupere l'etat l'activite precedente
        $ordre_prec = $ordre[$ii-1];
        $act_prec =   GetDataField ($connect,"select act_cdn from activite where act_ordre_nb = $ordre_prec and act_seq_no = $seq","act_cdn");
     }
     echo "<TR>";
     $heure = floor($duree/60);
     $reste=($heure > 0)?$duree%60:$duree;
     $duree=($reste == 0)?$heure.$h:$heure.$h.$reste;
     echo "<TD bgcolor='#EFEFEF' align='left' valign='top'><DIV id='sequence'><A href=\"javascript:void(0)\"".
          " onMouseOver=\"overlib('<TABLE>".
          "<TR><TD width=5></TD><TD>$msq_aff_pass_mult <B>$pass_mult</B></TD></TR>".
          "<TR><TD width=5></TD><TD>$msq_aff_acquit <B>$acquit</B></TD></TR>".
          "<TR><TD width=5></TD><TD>$msq_aff_dev_rend <B>$devoir</B></TD></TR>".
          "<TR><TD width=5></TD><TD>$msq_act_evalue <B>$notation</B></TD></TR>".
          "<TR><TD width=5></TD><TD>$mess_gp_durre_tot_form : <B>$duree</B></TD></TR></TABLE>',ol_hpos,RIGHT,ABOVE,WIDTH,'300')\" ".
          "onMouseOut=\"nd()\">$nom</A></DIV></TD>";
     echo "<TD align='left' bgcolor='#DEE3E7' valign='top'><DIV id='sequence'><A HREF=\"javascript:void(0)\"".
          " onMouseOver=\"overlib('<TABLE><TR><TD width=5></TD><TD>".$commentaire1."</TD></TR></TABLE>',ol_hpos,RIGHT,ABOVE,WIDTH,'270',CAPTION,'<TABLE><TR><TD width=5></TD><TD align=left><FONT SIZE=2 color =#333333><B>$mess_admin_comment</B></FONT><BR></TD</TR></TABLE>')\"".
          " onMouseOut=\"nd()\">$consigne</A></DIV></TD>";
     if ($id_ress > 0 && ($pass_mult == "OUI" || ($pass_mult == "NON" && ($acquit == "FORMATEUR_REFERENT" || $acquit == "RESSOURCE"))) && (($notation == "NON" || $acquit == "FORMATEUR_REFERENT" || $acquit == "RESSOURCE")) && (($ordre_act == "OUI" && $ii == 0 ) || $ordre_act == "NON")){
        echo "<TD align='center' valign='top' bgcolor='#EFEFEF'><A HREF=\"javascript:void(0)\" title=\"$mess_demarrer\" ".
             "onmouseover=\"img$ii.src='images/ecran-annonce/icoGgob.gif';return true;\" onmouseout=\"img$ii.src='images/ecran-annonce/icoGgo.gif'\">";
        echo "<IMG NAME=\"img$ii\" SRC=\"images/ecran-annonce/icoGgo.gif\" BORDER='0' onLoad=\"tempImg=new Image(0,0); tempImg.src='images/ecran-annonce/icoGgob.gif'\"></A></TD>";
     }else
        echo "<TD align='center' valign='top' bgcolor='#EFEFEF'><IMG SRC=\"images/ecran-annonce/icoGgoinactif.gif\" border='0' ALT= \"$mess_inactif\"></TD>";//fin if (($pointeur == 1 && $ordre_act == "OUI") ||($ordre_act == "NON"))
     if ($nom == "Evaluation certificative")
        $actsuiv = $mess_lanc_attente;
     elseif ($ress_ok == 'OUI' && $id_ress == 0)
        $actsuiv = $mess_trvx;
     else
        $actsuiv = $mess_lanc_afaire;
     echo "<td align='left' valign='top' bgcolor='#DEE3E7' nowrap>$actsuiv</td>";
     echo "<TD align='left' valign='top' bgcolor='#EFEFEF' nowrap>&nbsp;</DIV></TD>";
     echo "</TR>";
    $ii++;
  } //fin while ($i != $nb_seq)
  echo "</table></TD></TR></TABLE>";
}
?>