<?php
if (!isset($_SESSION)) session_start();
include ("include/UrlParam2PhpVar.inc.php");
if (!isset($_SESSION['id_user']) || $_SESSION['id_user'] == "")
{
  exit();
}
require "admin.inc.php";
require 'fonction.inc.php';
require 'fonction_html.inc.php';
require 'langues/graphique.inc.php';
require "lang$lg.inc.php";
//include ("click_droit.txt");
dbConnect();
include 'style.inc.php';
echo '<link rel="stylesheet" type="text/css" href="graphique/graphique.css" />';

//de quel type est l'utilisateur (apprenant, formateur, administrateur)
if ($groupe != 1)
{
   if ($utilisateur)
   {
      $nom_user=GetDataField ($connect,"select util_nom_lb from utilisateur where util_cdn='$utilisateur'","util_nom_lb");
      $prenom_user=GetDataField ($connect,"select util_prenom_lb from utilisateur where util_cdn='$utilisateur'","util_prenom_lb");
      $num_app = $utilisateur;
    }
    else
    {
      $nom_user=GetDataField ($connect,"select util_nom_lb from utilisateur where util_cdn='$id_user'","util_nom_lb");
      $prenom_user=GetDataField ($connect,"select util_prenom_lb from utilisateur where util_cdn='$id_user'","util_prenom_lb");
      $num_app = $id_user;
    }
   $req_grp = mysql_query("select utilgr_groupe_no from utilisateur_groupe where utilgr_utilisateur_no = $num_app");
   $nomb_grp = mysql_num_rows($req_grp);
  if ($nomb_grp > 0)
  {
    $id_grp = $numero_groupe;
    $nom_grp = GetDataField ($connect,"select grp_nom_lb from groupe where grp_cdn=$numero_groupe","grp_nom_lb");
    $message="";
  }
  else
  {
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
     $frame_gh = 200+20*$nbapp;
   }elseif ($nbrj > 91 && $nbrj < 360){
     $frame_gw = 800;
     $frame_gh = 160+20*$nbapp;
   }else{
     $frame_gw = 300+$nbrj;
     $frame_gh = 140+20*$nbapp;
   }
   $nom_grp = GetDataField ($connect,"select grp_nom_lb from groupe where grp_cdn=$numero_groupe","grp_nom_lb");
   $req_grp = mysql_query("select utilgr_utilisateur_no from utilisateur_groupe where utilgr_groupe_no = $numero_groupe");
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
$dir = "graphique";
$handle=opendir($dir);
$i=0;
while ($file = readdir($handle))
{
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
if ($groupe != 1)
{
   if ($typ_user != "APPRENANT")
     $titre = "$prenom_user $nom_user : $mess_lanc_ev_form  $nom_grp";
   elseif ($typ_user == "APPRENANT")
     $titre = "$mess_plan_maform $nom_grp";
   entete_simple($titre);
   echo "<TR><TD align='left'><TABLE bgColor='#FFFFFF' cellspacing='0' cellpadding='3' width='100%' border=0>";
   echo "<TR><TD colspan='3' align='left'>";
}
elseif ($groupe == 1)
{//$titre = "$mess_plan_inscrit $nom_grp
     if ($typ_user != "APPRENANT")
       $titre = "$mess_pg_tit1 $nom_grp";
     elseif($typ_user == "APPRENANT" && $planning == "mercure")
        $titre = "$mess_pg_tit1 $nom_grp";
     elseif ($typ_user == "APPRENANT" && $planning == "normal")
       $titre = "$mess_pg_tit1 $nom_grp</B></font>";
     entete_simple($titre);
     echo "<TR><TD align='left'><TABLE bgColor='#FFFFFF' cellspacing='0' cellpadding='3' width='100%' border=0>";
     if ($planning == "mercure")
     {
          $planning = "normal";
          $position = $mess_plan_maform;
     }
     else
     {
          $planning = "mercure";
          $position = $mess_plan_maform;
     }
     if ($typ_user == "APPRENANT")
     {
         $lien = "lancement.php?plan=plan_parc&planning=$planning";
         $titreur = $mess_menu_planning;
     }
     else
     {
         $lien = "lancement.php?numero_groupe=$id_grp&id_grp=$id_grp&groupe=$groupe&hgrp=$hgrp&planning=$planning&entantquetut=$entantquetut&entantqueobs=$entantqueobs";
         $titreur = $mess_pg_tit3;
     }
     $suite = "<div id='other' style=\"float:left;padding-right:8px;\">".
              "<A href=\"$lien\" target='main' class='bouton_new'>$titreur</A></div>";
     echo "<TR><TD colspan='3' valign='center'>$suite";
     if ($typ_user == 'APPRENANT')
        echo aide_div("planning_apprenant",0,0,0,0);
     else
        echo aide_div("planning_formateur",0,0,0,0);
     echo "</td></tr>";
     echo "<tr><td colspan='3' valign='center'>";
     if ($planning == "normal")
       echo "<IFRAME height=$frame_gh width='$frame_gw' bgColor='#FFFFFF' Marginwidth=0 marginheight=0 hspace=0".
            " vspace=0 frameborder=0  scrolling=no bordercolor='#000000' ".
            "SRC=\"graphique/planning_grp.php?numero_groupe=$numero_groupe&id_grp=$id_grp&groupe=$groupe&entantquetut=$entantquetut&entantqueobs=$entantqueobs\"></IFRAME>";
     elseif ($planning == "mercure")
       echo "<script type='text/javascript'>".
                 "document.location.replace(\"graphique/avanti.php?numero_groupe=$numero_groupe&id_grp=$id_grp&groupe=$groupe&entantquetut=$entantquetut&entantqueobs=$entantqueobs\");".
            "</script>";
     echo "</TD></TR>";
     echo "<TR><TD align=left><A HREF=\"javascript:history.back()\" ".
            "onmouseover=\"img_annonce.src='images/fiche_identite/boutretourb$suffixer.gif';return true;\" ".
            "onmouseout=\"img_annonce.src='images/fiche_identite/boutretour$suffixer.gif'\">";
     echo "<IMG NAME=\"img_annonce\" SRC=\"images/fiche_identite/boutretour$suffixer.gif\" BORDER='0' ".
            "onLoad=\"tempImg=new Image(0,0); tempImg.src='images/fiche_identite/boutretourb$suffixer.gif'\"></A></TD>";
     if ($planning == "normal")
     {
        echo "<TD></TD><TD valign='center'>";
        echo "<div id='comment' "."style=\"font-weight: normal;border: 1px solid #24677A; padding:4px;margin-left:100px; margin-bottom: 8px;text-align: left; width: 340px;\">";
        if ($typ_user != "APPRENANT")
        {
         echo "<span class='carre_rouge'></span>$msgrph_lgred <br /><br />";
        }
        echo "<span class='trait_vertic'></span>$msgrph_lgdtj";
        echo "<br /><br /><span class='tiret_hauteur'></span>$msgrph_lgreg</div></TD>";
        echo "</TR>";
     }
      else
      {
        echo "</td></TR>";
      }
     echo "</TABLE></TD></TR></TABLE></TD></TR></TABLE></BODY></HTML>";
   exit;
}
if ($typ_user == "APPRENANT" && $planning == "normal")
{
   echo "<TR><TD colspan='3' valign='center'>";
   $req_grp = mysql_query("select utilgr_groupe_no from utilisateur_groupe where utilgr_utilisateur_no = $id_user");
   $nomb_grp = mysql_num_rows($req_grp);
   if ($nomb_grp != 0)
   {
      $id_grp = $numero_groupe;
      $lien = "lancement.php?id_grp=$id_grp&groupe=1&planning=mercure";
      $lien = urlencode($lien);
      echo "<div id='planning' style=\"float:left;padding-left:3px;padding-right:8px;\">".
           "<a href=\"trace.php?link=$lien\" class='bouton_new'>$mess_plan_inscrit</a></div>";
   }
   echo aide_div("planning_apprenant",0,0,0,0);
   echo "</td></tr>";

   echo "<TR><TD colspan='3' valign='center'>".
        "<IMG SRC=\"graphique/planning.php?numero_groupe=$numero_groupe&utilisateur=$utilisateur&hgrp=$hgrp&plan=$plan&parcours=$parcours&scormOk=$scormOk\">";
}
elseif ($typ_user != "APPRENANT")
       echo "<IFRAME height=$frame_gh width='$frame_gw' bgColor='#FFFFFF' Marginwidth=0 marginheight=0 ".
            " hspace=0 vspace=0 frameborder=0 scrolling=no bordercolor='#000000' ".
            "SRC=\"graphique/planning.php?numero_groupe=$numero_groupe&utilisateur=$utilisateur&hgrp=$hgrp&plan=$plan&parcours=$parcours&scormOk=$scormOk\">".
            "</IFRAME>";
elseif ($typ_user == "APPRENANT" && $planning == "mercure")
   echo "<IMG SRC=\"graphique/planning_mercure.php?numero_groupe=$numero_groupe&utilisateur=$utilisateur&plan=$plan&parcours=$parcours&scormOk=$scormOk\">";
echo "</TD></TR>";
echo "<TR><TD height='6'></TD></TR>";

if ($typ_user != "APPRENANT")
{
   echo "</TD></TR><TR><TD align=left><A HREF=\"javascript:history.back()\" onmouseover=\"img_annonce.src='images/fiche_identite/boutretourb$suffixer.gif';return true;\" onmouseout=\"img_annonce.src='images/fiche_identite/boutretour$suffixer.gif'\">";
   echo "<IMG NAME=\"img_annonce\" SRC=\"images/fiche_identite/boutretour$suffixer.gif\" BORDER='0' onLoad=\"tempImg=new Image(0,0); tempImg.src='images/fiche_identite/boutretourb$suffixer.gif'\"></A>";
}else{
        echo "<TR><TD  valign='middle' align=left>";
        echo "<A HREF=\"javascript:history.back()\" onmouseover=\"img_annonce.src='images/fiche_identite/boutretourb$suffixer.gif';return true;\" onmouseout=\"img_annonce.src='images/fiche_identite/boutretour$suffixer.gif'\">";
        echo "<IMG NAME=\"img_annonce\" SRC=\"images/fiche_identite/boutretour$suffixer.gif\" BORDER='0' onLoad=\"tempImg=new Image(0,0); tempImg.src='images/fiche_identite/boutretourb$suffixer.gif'\"></A></td>";
        echo "<TD align=venter colspan=2 valign='middle'><div id='comment' "."style=\"font-weight: normal;border: 1px solid #24677A; padding:4px;margin-left:100px; margin-bottom: 8px;text-align: left; width: 340px;\">";
        echo "<span class='trait_vertic'></span>$msgrph_lgdtj";
        echo "<br /><br /><span class='tiret_hauteur'></span>$msgrph_lgreg</div>";
}

?>
</TD></TR></TABLE></TD></TR></TABLE>
</BODY>
</HTML>
