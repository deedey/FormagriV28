<?php
if (!isset($_SESSION)) session_start();
include ("include/UrlParam2PhpVar.inc.php");
if (!isset($_SESSION['id_user']) || $_SESSION['id_user'] == "")
{
  exit();
}
require "admin.inc.php";
require 'fonction.inc.php';
require "lang$lg.inc.php";
//include ("click_droit.txt");
dbConnect();
include 'style.inc.php';
//de quel type est l'utilisateur (apprenant, formateur, administrateur)
if ($groupe != 1){
   if ($utilisateur){
      $nom_user=GetDataField ($connect,"select util_nom_lb from utilisateur where util_cdn='$utilisateur'","util_nom_lb");
      $prenom_user=GetDataField ($connect,"select util_prenom_lb from utilisateur where util_cdn='$utilisateur'","util_prenom_lb");
      $num_app = $utilisateur;
    }else{
      $nom_user=GetDataField ($connect,"select util_nom_lb from utilisateur where util_cdn='$id_user'","util_nom_lb");
      $prenom_user=GetDataField ($connect,"select util_prenom_lb from utilisateur where util_cdn='$id_user'","util_prenom_lb");
      $num_app = $id_user;
    }
   $req_grp = mysql_query("select utilgr_groupe_no from utilisateur_groupe where utilgr_utilisateur_no = $num_app");
   $nomb_grp = mysql_num_rows($req_grp);
  if ($nomb_grp > 0){
    $id_grp = $numero_groupe;
    $nom_grp = GetDataField ($connect,"select grp_nom_lb from groupe where grp_cdn=$numero_groupe","grp_nom_lb");
    $message="";
  }else{
    echo "<CENTER><FONT COLOR='white' size='3'><B>$mess_menu_planning</B></font>";
    $message = "&nbsp;<P><FONT size=2>$mess_lanc_noplan</FONT></CENTER><P>";
    echo $message;
    exit;
  }
  if ($typ_user != "APPRENANT")
  {
     if ($plan == "plan_parc")
     {
         $parc_cours = mysql_query ("select count(distinct presc_parc_no) from prescription_$numero_groupe where
                                     presc_utilisateur_no = $utilisateur");
         $nombre1 = mysql_result($parc_cours,0);
         $req_min = mysql_query ("select presc_datedeb_dt from prescription_$numero_groupe where
                                  presc_utilisateur_no = $utilisateur
                                  order bypresc_datedeb_dt");
         $req_max = mysql_query ("selectpresc_datefin_dt from prescription_$numero_groupe where
                                  presc_utilisateur_no = $utilisateur
                                  order by presc_datefin_dt desc");
     }
     elseif ($plan == "plan_seq" || $plan == "plan_mixte")
     {
         $type_parcours = getdatafield ($connect,"SELECT parcours_type_lb from parcours where parcours_cdn = $parcours","parcours_type_lb");
         if (strstr($type_parcours,"SCORM"))
            $scormOk = 1;
         else
            $scormOk = 0;
         $seq_cours = mysql_query ("select count(presc_seq_no) from prescription_$numero_groupe where
                                    presc_utilisateur_no = $utilisateur and
                                    presc_parc_no= $parcours");
         $nombre1 = mysql_result($seq_cours,0);
         $req_min = mysql_query ("select presc_datedeb_dt from prescription_$numero_groupe where
                                  presc_utilisateur_no = $utilisateur AND
                                  presc_parc_no= $parcours
                                  order by presc_datedeb_dt");
         $req_max = mysql_query ("select presc_datefin_dt from prescription_$numero_groupe where
                                  presc_utilisateur_no = $utilisateur AND
                                  presc_parc_no= $parcours
                                  order by presc_datefin_dt desc");
     }
     $date_min = mysql_result($req_min,0,"presc_datedeb_dt");
     $nb_jours_req_min = mysql_query ("select TO_DAYS('$date_min')");
     $nb_jours_min = mysql_result ($nb_jours_req_min,0);
     $nbr_max = mysql_num_rows($req_max);
     $date_max = mysql_result($req_max,0,"presc_datefin_dt");
     $nb_jours_req_max = mysql_query ("select TO_DAYS('$date_max')");
     $nb_jours_max = mysql_result ($nb_jours_req_max,0);
     $nbrj = $nb_jours_max-$nb_jours_min;
     if ($nbrj < 92)
     {
        $frame_gw = 800;
        $frame_gh = 220+30*$nombre1;
     }
     elseif ($nbrj > 91 && $nbrj < 360)
     {
        $frame_gw = 800;
        $frame_gh = 160+30*$nombre1;
     }
     else
     {
        $frame_gw = 600+$nbrj;
        $frame_gh = 140+30*$nombre1;
     }
//     echo "$frame_gw = 800;elseif ($nbrj > 91 && $nbrj < 360){";exit;
  }
}
else
{
   $liste_apprenant = mysql_query("SELECT count(utilgr_cdn) from utilisateur_groupe where utilisateur_groupe.utilgr_groupe_no = '$id_grp'");
   $nbapp = mysql_result($liste_apprenant,0);
   $req_min = mysql_query ("select presc_datedeb_dt from
                            prescription_$id_grp,utilisateur_groupe where
                            presc_utilisateur_no = utilisateur_groupe.utilgr_utilisateur_no and
                            utilisateur_groupe.utilgr_groupe_no=$id_grp
                            order by presc_datedeb_dt asc");
   $date_min = mysql_result($req_min,0,"presc_datedeb_dt");
   $nb_jours_req_min = mysql_query ("select TO_DAYS('$date_min')");
   $nb_jours_min = mysql_result ($nb_jours_req_min,0);
   $req_max = mysql_query ("select presc_datefin_dt from
                            prescription_$id_grp,utilisateur_groupe where
                            presc_utilisateur_no = utilisateur_groupe.utilgr_utilisateur_no and
                            utilisateur_groupe.utilgr_groupe_no=$id_grp
                            order by presc_datefin_dt asc");
   $nbr_max = mysql_num_rows($req_max);
   $date_max = mysql_result($req_max,$nbr_max-1,"presc_datefin_dt");
   $nb_jours_req_max = mysql_query ("select TO_DAYS('$date_max')");
   $nb_jours_max = mysql_result ($nb_jours_req_max,0);
   $nbrj = $nb_jours_max-$nb_jours_min;
   if ($nbrj < 92){
     $frame_gw = 800;
     $frame_gh = 220+30*$nbapp;
   }elseif ($nbrj > 91 && $nbrj < 360){
     $frame_gw = 800;
     $frame_gh = 180+30*$nbapp;
   }else{
     $frame_gw = 300+$nbrj;
     $frame_gh = 160+30*$nbapp;
   }
   $nom_grp = GetDataField ($connect,"select grp_nom_lb from groupe where grp_cdn=$numero_groupe","grp_nom_lb");
   $req_grp = mysql_query("select utilgr_groupe_no from utilisateur_groupe where utilgr_groupe_no = $numero_groupe");
   $j = mysql_num_rows($req_grp);
   if ($j < 7)
     $frame = 200+460;
   elseif ($j > 6 && $j < 12)
     $frame = 200+$j*60;
   elseif ($j > 11 && $j < 20)
     $frame = 200+$j*40;
   elseif ($j > 19 && $j < 30)
     $frame = 200+$j*30;
   else
     $frame = 200+$j*20;
}
//     echo "$date_fin   nbrj =$nbrj et frame = $frame j = $j height=$frame_gh width='$frame_gw'";
$dir = $rep_graph;
$handle=opendir($dir);
$i=0;
while ($file = readdir($handle)){
  list($extension,$nom)=getextension($file);
  if (in_array($extension, array("png"))) {
     $fichier = $dir.$file;
     unlink($fichier);
  }
  $i++;
}
closedir($handle) ;

function getextension($fichier){
 $bouts = explode(".", $fichier);
  return array(array_pop($bouts), implode(".", $bouts));
  exit;
}
if ($groupe != 1){
   if ($typ_user != "APPRENANT")
     $titre = "$prenom_user $nom_user : $mess_lanc_ev_form  $nom_grp";
   elseif ($typ_user == "APPRENANT")
     $titre = "$mess_plan_maform $nom_grp";
   echo "<CENTER><TABLE bgColor='#298CA0' cellspacing='2' cellpadding='0' width='98%' border=0><TR><TD>";
   echo "<TABLE bgColor='#FFFFFF' cellspacing='1' cellpadding='0' width='100%'>";
   echo "<TR><TD background=\"images/fond_titre_table.jpg\" height='37' align='center' valign='center'><Font size='3' color='#FFFFFF'><B>$titre</B></FONT></TD></TR>";
   echo "<TR><TD align='left'><TABLE bgColor='#CEE6EC' cellspacing='0' cellpadding='3' width='100%' border=0>";
   echo "<TR><TD colspan='3' align='left'>";
}elseif ($groupe == 1){
     if ($typ_user != "APPRENANT")
        $titre = "$mess_menu_planning_grp $nom_grp";
     elseif($typ_user == "APPRENANT" && $planning == "mercure")
        $titre = "$mess_plan_inscrit $nom_grp";
     elseif ($typ_user == "APPRENANT" && $planning == "normal")
       $titre = "$mess_plan_inscrit $nom_grp</B></font>";
     echo "<CENTER><TABLE bgColor='#298CA0' cellspacing='2' cellpadding='0' width='98%' border=0><TR><TD>";
     echo "<TABLE bgColor='#FFFFFF' cellspacing='1' cellpadding='0' width='100%' border=0>";
     echo "<TR><TD background=\"images/fond_titre_table.jpg\" height='37' align='center' valign='center'><Font size='3' color='#FFFFFF'><B>$titre</B></FONT></TD></TR>";
     echo "<TR><TD align='left'><TABLE bgColor='#CEE6EC' cellspacing='0' cellpadding='3' width='100%' border=0>";
     echo "<TR><TD colspan='3' align='center'>";
//     if (($typ_user == "APPRENANT" && $planning == "normal") || $typ_user != "APPRENANT")
     if ($planning == "normal")
       echo "<IFRAME height=$frame_gh width='$frame_gw' bgColor='#CEE6EC' Marginwidth=0 marginheight=0 hspace=0 vspace=0 frameborder=0  scrolling=no bordercolor='#000000' SRC=\"graphique/planning_grp.php?numero_groupe=$numero_groupe&id_grp=$id_grp&groupe=$groupe\"></IFRAME><BR>";
     elseif ($planning == "mercure")
       echo "<IMG SRC=\"graphique/planning_grp_mercure.php?numero_groupe=$numero_groupe&id_grp=$id_grp&groupe=$groupe\">";
     echo "</TD></TR>";
//     if ($typ_user == "APPRENANT"){
       if ($planning == "mercure"){
          $planning = "normal";
          $position = $mess_plan_maform;
       }else{
          $planning = "mercure";
          $position = $mess_plan_maform;
       }
       if ($typ_user == "APPRENANT")
         $lien = "lancement.php?plan=plan_parc&planning=$planning";
       else
         $lien = "lancement.php?numero_groupe=$id_grp&id_grp=$id_grp&groupe=$groupe&hgrp=$hgrp&planning=$planning";
       if ($planning == "mercure"){
           echo "<TR><TD align='left' valign='center'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".
                "$bouton_gauche<A href=\"$lien\" target='main'>$mess_aventi</A>$bouton_droite</TD>";
           echo "<TD align='center' valign='center'><IMG SRC=\"images/graphe/icoGtrace.gif\" border='0'></TD>".
                  "<TD valign='center'><FONT COLOR='#333333' size='2'>$mess_pf_vert</font></TD>";
           echo "</TR>";
       }
       echo "<TR><TD height='6'>";
       if ($typ_user != "APPRENANT" && $planning == "normal"){
         echo "</TD></TR><TR><TD align='center'>";
         echo "<IFRAME height=200 width='$frame' bgColor='#CEE6EC' Marginwidth=0 marginheight=0 hspace=0 vspace=0 frameborder=0 scrolling=no bordercolor='#000000' ".
              "SRC=\"graphique/cours_attente.php?numero_groupe=$numero_groupe&hgrp=$hgrp&utilisateur=$utilisateur&plan=groupe&id_grp=$id_grp&groupe=$groupe&entantquetut=$entantquetut&entantquepresc=$entantquepresc&scormOk=$scormOk\"></IFRAME>";
         echo "</TD><TD><TABLE BORDER='0'><TR><TD><font color=green size=-2><B>$mess_lanc_leg</B><BR>";
         ?>
         </TD></TR><TR><TD valign=top><BR>
         <IMG SRC="graphique/orange.gif"><?php  echo $mess_lanc_afaire;?><BR>
         <IMG SRC="graphique/bleu.gif"><?php  echo $mess_lanc_encours;?><BR>
         <IMG SRC="graphique/vert.gif"><?php  echo $mess_lanc_attente;?><BR>
         <IMG SRC="graphique/gris.gif"><?php  echo $mess_lanc_termine;?>
         </TD></TR></TABLE>
         <?php
       }
//       if ($typ_user != "APPRENANT"){
          echo "</TD></TR><TR><TD align=left><A HREF=\"javascript:history.back()\" ".
            "onmouseover=\"img_annonce.src='images/fiche_identite/boutretourb$suffixer.gif';return true;\" ".
            "onmouseout=\"img_annonce.src='images/fiche_identite/boutretour$suffixer.gif'\">";
          echo "<IMG NAME=\"img_annonce\" SRC=\"images/fiche_identite/boutretour$suffixer.gif\" BORDER='0' ".
            "onLoad=\"tempImg=new Image(0,0); tempImg.src='images/fiche_identite/boutretourb$suffixer.gif'\"></A>";
//       }
       echo "</TD></TR></TABLE></TD></TR></TABLE></TD></TR></TABLE></BODY></HTML>";
   exit;
}
if ($typ_user == "APPRENANT" && $planning == "normal")
   echo "<IMG SRC=\"graphique/planning.php?numero_groupe=$numero_groupe&utilisateur=$utilisateur&hgrp=$hgrp&plan=$plan&parcours=$parcours&scormOk=$scormOk\">";
elseif ($typ_user != "APPRENANT")
       echo "<IFRAME height=$frame_gh width='$frame_gw' bgColor='#CEE6EC' Marginwidth=0 marginheight=0 hspace=0 vspace=0 frameborder=0  scrolling=no bordercolor='#000000' SRC=\"graphique/planning.php?numero_groupe=$numero_groupe&utilisateur=$utilisateur&hgrp=$hgrp&plan=$plan&parcours=$parcours&scormOk=$scormOk\"></IFRAME>";
elseif ($typ_user == "APPRENANT" && $planning == "mercure")
   echo "<IMG SRC=\"graphique/planning_mercure.php?numero_groupe=$numero_groupe&utilisateur=$utilisateur&plan=$plan&parcours=$parcours&scormOk=$scormOk\">";
echo "</TD></TR>";
if ($typ_user == "APPRENANT"){
   if ($planning == "mercure"){
     $planning = "normal";
     $position = $mess_plan_maform;
   }else{
     $planning = "mercure";
     $position = $mess_plan_maform;
   }
   $req_grp = mysql_query("select utilgr_groupe_no from utilisateur_groupe where utilgr_utilisateur_no = $id_user");
   $nomb_grp = mysql_num_rows($req_grp);
   if ($nomb_grp != 0){
      $id_grp = $numero_groupe;
      $lien = "lancement.php?id_grp=$id_grp&groupe=1&planning=mercure";
      $lien = urlencode($lien);
      echo "<TR><TD align=left valign='center'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".
           "<A href=\"trace.php?link=$lien\" title=\"$mess_menu_planning_grp\"target=main".
           " onmouseover=\"img_pg.src='images/graphe/boutplaninscritb$suffixer.gif';return true;\"".
           " onmouseout=\"img_pg.src='images/graphe/boutplaninscrit$suffixer.gif'\">".
           "<IMG NAME=\"img_pg\" SRC=\"images/graphe/boutplaninscrit$suffixer.gif\" BORDER='0' alt=\"$mess_menu_planning_grp\"".
           " onLoad=\"tempImg=new Image(0,0); tempImg.src='images/graphe/boutplaninscritb$suffixer.gif'\"></A></TD>";
      if ($planning == "mercure")
         echo "<TD align='center' valign='center'><IMG SRC=\"images/graphe/icoGtrace.gif\" border='0'></TD>".
              "<TD valign='center'><FONT COLOR='#333333' size='2'>$mess_pf_vert</font></TD>";
      echo "</TR>";
   }
   echo "<TR><TD height='6'></TD></TR>";
}

if ($plan != "plan_parc" && $plan != "groupe"){
     if ($typ_user != "APPRENANT"){
     ?>
       <TD align="right">
       <IFRAME height=200 width=800  bgColor='#CEE6EC' Marginwidth=0 marginheight=0 hspace=0 vspace=0 frameborder=0  scrolling=no bordercolor="#000000"
       SRC="graphique/cours_attente.php?numero_groupe=<?php echo $numero_groupe;?>&hgrp=<?php echo $hgrp;?>&utilisateur=<?php echo $utilisateur;?>&plan=<?php echo $plan;?>&parcours=<?php echo "$parcours&scormOk=$scormOk&entantquepresc=$entantquepresc";?>">
       </IFRAME>
       </TD><TD><TABLE><TR><TD><font color=green size=-2><B><?php  echo $mess_lanc_leg ;?></B><BR>
       </TD></TR><TR><TD valign=top><BR>
       <IMG SRC="graphique/orange.gif"><?php  echo $mess_lanc_afaire;?><BR>
       <IMG SRC="graphique/bleu.gif"><?php  echo $mess_lanc_encours;?><BR>
       <IMG SRC="graphique/vert.gif"><?php  echo $mess_lanc_attente;?><BR>
       <IMG SRC="graphique/marron.gif">&nbsp;<?php  echo $mess_lanc_presentiel;?><BR>
       <IMG SRC="graphique/gris.gif"><?php  echo $mess_lanc_termine;?>
       </TD></TR></TABLE>
    <?php
    }
}elseif ($plan == "plan_parc"){
     if ($typ_user != "APPRENANT"){
     ?>
       <TD align="right">
       <IFRAME height=200 width=800 bgColor='#CEE6EC' Marginwidth=0 marginheight=0 hspace=0 vspace=0 frameborder=0  scrolling=no bordercolor="#000000"
       SRC="graphique/cours_attente.php?numero_groupe=<?php echo $numero_groupe;?>&hgrp=<?php echo $hgrp;?>&utilisateur=<?php echo $utilisateur;?>&plan=<?php echo $plan;?>&parcours=<?php echo "$parcours&scormOk=$scormOk&entantquepresc=$entantquepresc";?>">
       </IFRAME>
       </TD><TD><TABLE><TR><TD><font color=green size=-2><B><?php  echo $mess_lanc_leg ;?></B><BR>
       </TD></TR><TR><TD valign=top><BR>
       <IMG SRC="graphique/orange.gif"><?php  echo $mess_lanc_afaire;?><BR>
       <IMG SRC="graphique/bleu.gif"><?php  echo $mess_lanc_encours;?><BR>
       <IMG SRC="graphique/vert.gif"><?php  echo $mess_lanc_attente;?><BR>
       <IMG SRC="graphique/gris.gif"><?php  echo $mess_lanc_termine;?>
       </TD></TR></TABLE>
      <?php
     }
}
if ($typ_user != "APPRENANT"){
   echo "</TD></TR><TR><TD align=left><A HREF=\"javascript:history.back()\" onmouseover=\"img_annonce.src='images/fiche_identite/boutretourb$suffixer.gif';return true;\" onmouseout=\"img_annonce.src='images/fiche_identite/boutretour$suffixer.gif'\">";
   echo "<IMG NAME=\"img_annonce\" SRC=\"images/fiche_identite/boutretour$suffixer.gif\" BORDER='0' onLoad=\"tempImg=new Image(0,0); tempImg.src='images/fiche_identite/boutretourb$suffixer.gif'\"></A>";
}
?>
</TD></TR></TABLE></TD></TR></TABLE>
</BODY>
</HTML>
