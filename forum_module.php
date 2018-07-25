<?php
if (!isset($_SESSION)) session_start();
if (!isset($_SESSION['id_user']) || $_SESSION['id_user'] == "")
{
  exit();
}
include ("include/UrlParam2PhpVar.inc.php");
require 'admin.inc.php';
require 'fonction.inc.php';
require "lang$lg.inc.php";
require "langues/module.inc.php";
require 'fonction_html.inc.php';
dbConnect();
include ('style.inc.php');
$leJour = date("Y/m/d H:i:s" ,time());
if (isset($_GET['module']))
{
   $Rsql = mysql_query("select * from forums_modules where fm_module_no = ".$_GET['module']);
   $titmod = GetDataField ($connect,"select parcours_nom_lb from parcours where parcours_cdn = ".$_GET['module'],"parcours_nom_lb");
   $autmod = GetDataField ($connect,"select parcours_auteur_no from parcours where parcours_cdn = ".$_GET['module'],"parcours_auteur_no");
}
elseif(isset($_GET['id_parc']))
{
   $module= $_GET['id_parc'];
   $Rsql = mysql_query("select * from forums_modules where fm_module_no = ".$_GET['id_parc']);
   $titmod = GetDataField ($connect,"select parcours_nom_lb from parcours where parcours_cdn = ".$_GET['id_parc'],"parcours_nom_lb");
   $autmod = GetDataField ($connect,"select parcours_auteur_no from parcours where parcours_cdn = ".$_GET['id_parc'],"parcours_auteur_no");
}
?>
<script type="text/javascript" src="OutilsJs/lib/ajax/ajax_cms.js"></script>
<div id="affiche" class="Status"></div>
<div id="mon_contenu" class="mon_contenu"
        onClick="javascript:$(document).ready(function() {if ($.browser.msie) {$('#mon_contenu').hide();}else{$('#mon_contenu').empty();}})"
        <?php echo "title=\"$mess_clkF\">";?>
</div>
<?php

$titre = "$msg_formod :  $titmod";
entete_simple($titre);
echo "<TR><TD colspan='2' style='background-color:#FFFFFF;margin:10px; width='96%'>".
     "<table cellpadding='4' cellspacing='0' border='0' width='96%'>";
echo "<TR><TD colspan='2' style='margin:12px;'>";
$NbRm = mysql_num_rows($Rsql);
if ($autmod == $_SESSION['id_user'] || $_SESSION['typ_user'] == 'ADMINISTRATEUR')
{
    echo "<div class='sous_titre' style='float:left;margin-right:8px;padding-right:8px;cursor:pointer;color:#24677A;".
         "font-weight:bold;' onClick=\"javascript:top.main.location.href=".
         "'parcours.php?liste=1&consult=1&parc=$module&id_parc=$module&miens_parc=1&refer=&liste_seq=1';";
    echo "if (parent.frames[1].name == 'principal')";
    echo "{top.logo.location.href='menu_formateur.php?action=concevoir&lien_params=prem=1|liste=1|miens_parc=1|id_ref_parc=0|ordre_affiche=lenom';}";
    echo "\">$msg_ConsMod</div>";
}
if ($NbRm > 0)
{
     echo "<div id='ceci' class='sous_titre' style='float:left;margin-right:8px;padding-right:8px;cursor:pointer;color:#24677A;".
          "font-weight:bold;' onClick=\"javascript:document.location.href='forum_module.php?id_parc=$module';\">$msg_RmtSjt</div>";
}
if (isset($_GET['form_forum']) && $_GET['form_forum'] == 1 && !isset($_GET['new']))
{
     echo "<div class='sous_titre' style='float:left;margin-right:8px;padding-right:8px;cursor:pointer;color:#24677A;".
          "font-weight:bold;' onClick=\"javascript:document.location.href='forum_module.php?module=$module&num=$grandPa';\">$msg_RmtFil</div>";
}
echo "<div class='sous_titre' style='float:left;cursor:pointer;font-weight:bold;color:#24677A;' ".
     "onClick=\"javascript:document.location.href='forum_module.php?form_forum=1&module=$module&new=1';\">".
     $msg_NewSjt."</div></TD></TR>";
if (isset($_GET['form_forum']) && $_GET['form_forum'] == 1)
{
   if (isset($_GET['new']) && $_GET['new'] == 1)
   {
      $num = 0;
      $grandPa = Donne_ID ($connect,"SELECT max(fm_cdn) from forums_modules");
   }
   else
   {
      $sql = mysql_query("select * from forums_modules where fm_module_no = $module and fm_cdn=$num");
      $nombre = mysql_num_rows($sql);
      $affiche = '';
      if ($nombre > 0)
      {
         while ($item = mysql_fetch_object($sql))
         {
               $x++;
               $numero = $item->fm_cdn;
               $auteur = $item->fm_auteur_no;
               $visible = $item->fm_visible_on;
               $sujet = NewHtmlentities($item->fm_sujet_lb,ENT_QUOTES);
               $date = $item->fm_datetime_dt;
               $bodi = $item->fm_body_lb;
               $sqlAut = mysql_query("select * from utilisateur where util_cdn = $auteur");
               $nb_auteur = mysql_num_rows($sqlAut);
               if ($nb_auteur > 0)
               {
                  $nom = mysql_result($sqlAut,0,'util_nom_lb');
                  $prenom = mysql_result($sqlAut,0,'util_prenom_lb');
                  $photo = mysql_result($sqlAut,0,'util_photo_lb');
                  $email = mysql_result($sqlAut,0,'util_email_lb');
                  if ($id_photo != "")
                     list($w_img, $h_img, $type_img, $attr_img) = getimagesize("images/$id_photo");
                  $message_mail= addslashes($mess_mail_avert)." $prenom  $nom";
                  $subject = addslashes($sujet);
                  $lien = "mail.php?dou=forum&contacter=1&a_qui=$email&sujet=$subject&message_mail=$message_mail";
                  $author = "<A HREF=\"javascript:void(0);\" onclick=\"javascript:window.open('$lien','','left=0, top=0, width=680,height=520,resizable=yes,scrollbars=yes, menubar=0,location=0, toolbar=0');\" title=\"$mess_ecrire\"";
                  $author .= ">$nom $prenom</A>";
                  $affiche .= couleur_tr($x,'').'<td width="20%" valign="top">';
                  if ($photo != '')
                  {
                    $taille_logo = getimagesize("images/".$photo);
                    if ($taille_logo[1] > 60){
                       $largeur_logo=intval(ceil($taille_logo[0]/$taille_logo[1])*50);
                       $hauteur_logo=intval(ceil($taille_logo[0]/$taille_logo[1])*60);
                    }else{
                       $largeur_logo=$taille_logo[0];
                       $hauteur_logo=$taille_logo[1];
                    }
                    $affiche .= '<img src="images/'.$photo.'" width="'.$largeur_logo.'" height="'.$hauteur_logo.'" border="0">';
                  }
                  else
                    $affiche .= '<IMG SRC="images/repertoire/icoptisilhouet.gif" width="19" height="25" border="0">';
                  $affiche .= '</td><td valign="top">'.$author.'<br>'.$date.'</td><td valign="top">'.NewHtmlEntityDecode($bodi).'</td>';
               }
               else
               {
                  $nom = 'Inconnu';
                  $prenom = 'inconnu';
                  $photo = '<IMG SRC="images/repertoire/icoptisilhouet.gif" width="19" height="25" border="0">';
                  $affiche .= couleur_tr($x,'').'<td width="20%" valign="top">'.$photo.'</td>';
                  $affiche .= '<td valign="top">'.$nom.' '.$prenom.'<br>'.$date.'</td>'.
                              '<td valign="top"> '.NewHtmlEntityDecode($bodi).'</td>';
               }
         $affiche .= "</table></td></tr>";
         }
      }
   }
   $affiche .= "<tr><td colspan=2 style='height:40px;text-align:left;font-weight:bold;font-size:13px;margin:15px;background-color:#e2e2e2'>".
               $mess_repondre."</td></tr>";
   echo $affiche;
   $affiche = '';
   include ('forum_module_form.php');
   exit;
}
if (isset($_GET['insert_post']) && $_GET['insert_post'] == 1)
{
   $num = $_GET['grandPa'];
   if (isset($_GET['modif']) && $_GET['modif'] != 1)
   {
     if ($_GET['new'] == 1)
       $lien = "forum_module.php?id_parc=$module";
     else
       $lien = "forum_module.php?module=".$_GET['module']."&num=$num";
     $requete = mysql_query("insert into forums_modules values(NULL,$module,$parent,\"$sujet\",$id_user,1,\"".NewHtmlentities($contenu)."\",\"$leJour\")");
   }
   elseif (isset($_GET['modif']) && $_GET['modif'] == 1)
   {
     $requete = mysql_query("update forums_modules set fm_body_lb=\"".NewHtmlentities($contenu)."\",fm_sujet_lb=\"$sujet\" where fm_cdn=$modif_num");
   }
}
if (!isset($_GET['id_parc']))
{
   $contentSup.= "<TR><TD> <div class='sous_titre' style='width:210px;background-color:#e2f4e2;'>$msg_LstMsg</div></TD></TR>";
   $sql = mysql_query("select * from forums_modules where fm_module_no = $module and fm_cdn=$num");
   $nombre = mysql_num_rows($sql);
   if ($nombre > 0)
   {
      while ($item = mysql_fetch_object($sql))
      {
        $numero = $item->fm_cdn;
        $auteur = $item->fm_auteur_no;
        $visible = $item->fm_visible_on;
        if ($visible == 1 || ($visible == 0 && $autmod == $_SESSION['id_user']) || $_SESSION['typ_user'] == 'ADMINISTRATEUR')
        {
           $grandPa = $num;
           $sqlAut = mysql_query("select * from utilisateur where util_cdn = $auteur");
           $nb_auteur = mysql_num_rows($sqlAut);
           if ($nb_auteur > 0)
           {
               $nom = mysql_result($sqlAut,0,'util_nom_lb');
               $prenom = mysql_result($sqlAut,0,'util_prenom_lb');
           }
           else
           {
               $nom = 'Inconnu';
               $prenom = 'inconnu';
           }
           $contentSup.= '<TR style="background-color:#e2e2e2;color:#24677A;'.
                         'font-weight:bold;height:17px;padding:2px;">';
           $contentSup.= "<TD valign='top'><div id=$numero style='margin-left:5px;cursor:pointer;font-weight:bold;color:#24677A;'".
                         " onclick=\"javascript:document.location.href='forum_module.php?form_forum=1&module=$module&grandPa=$num&num=$numero';\"".
                         bulle($mess_repondre,"","CENTER","ABOVE",60).
                         $item->fm_sujet_lb."</div></TD>";
           $contentSup.= "<TD valign='top'>".html_entity_decode($item->fm_body_lb,ENT_QUOTES,'iso-8859-1')."</TD>";
           $contentSup.= "<TD valign='top'> $nom $prenom </TD>";
           $contentSup.= "<TD valign='top'> ".$item->fm_datetime_dt."</TD>";
           if ($autmod == $_SESSION['id_user'] || $_SESSION['typ_user'] == 'ADMINISTRATEUR')
           {

               $contentSup.= "<TD valign='top'><a href='forum_module.php?form_forum=1&modif=1&modif_num=$numero&module=$module&num=$numero&grandPa=$numero'>".
                             "<img src='images/repertoire/icoGrenomfich20.gif' border=0></a></TD>";
               $mon_img1 = "<IMG SRC='images/modules/visible.gif' BORDER=0>";
               $mon_img2 = "<IMG SRC='images/modules/invisible.gif' BORDER=0>";
               $lien1 = "formation/forum_mod_modif.php?numero=$numero&dl=oeil$numero&objet=invisible&id_parc=$module";
               $lien2 = "formation/forum_mod_modif.php?numero=$numero&dl=oeil$numero&objet=visible&id_parc=$module";
               if ($visible == 1)
                   $contentSup.= "<td><div id='oeil$numero'><A href=\"javascript:void(0);\" ".
                                 "onClick=\"javascript:appelle_ajax('$lien1'); ".
                                 "\$('#oeil$numero').empty();addContent_forum('$lien1');\" ".
                                 bulle($hide,"","LEFT","ABOVE",80).
                                 "$mon_img1</a></div></td>";
               elseif($visible == 0)
                   $contentSup.= "<td><div id='oeil$numero'><A href=\"javascript:void(0);\" ".
                                 "onClick=\"javascript:appelle_ajax('$lien2');".
                                 "\$('#oeil$numero').empty();addContent_forum('$lien2');\" ".
                                 bulle($makevisible,"","LEFT","ABOVE",100).
                                 "$mon_img2</a></div></td>";
           }
           $contentSup.= "</TR>";
           $contentSup.= fn_reverse_aff($numero);
        }
      }
      $content = $contentSup;
   }
}
else
{
  if ($NbRm > 0)
     $content .= "<TR><TD><div class='sous_titre' style='width:140px;background-color:#e2f4e2;margin-bottom:4px;'>$msg_Lst_sbjt</div></TD></TR>";
  $requete = mysql_query("select * from forums_modules where fm_module_no = ".$_GET['id_parc']." and fm_parent_no=0 order by fm_cdn");
  $comptChilds = 0;
  while ($row = mysql_fetch_object($requete))
  {
     $comptChilds++;
     $childs = 0;
     $ajtTD = "";
     $finTD="";
     $num = $row->fm_cdn;
     $visible = $row->fm_visible_on;
     if ($visible == 1 || ($visible == 0 && $autmod == $_SESSION['id_user']) || $_SESSION['typ_user'] == 'ADMINISTRATEUR')
     {
        $module = $row->fm_module_no;
        $nbCh = fn_reverse($num);
        $auteur = $row->fm_auteur_no;
        $sqlAut = mysql_query("select * from utilisateur where util_cdn = $auteur");
        $nb_auteur = mysql_num_rows($sqlAut);
        if ($nb_auteur > 0)
        {
           $nom = mysql_result($sqlAut,0,'util_nom_lb');
           $prenom = mysql_result($sqlAut,0,'util_prenom_lb');
        }
        else
        {
           $nom = 'Inconnu';
           $prenom = 'inconnu';
        }
       if ($nbCh > 0)
        {
           $ajtTD = "style=\"color:#24677A;margin-top:2px;font-weight:bold;cursor:pointer;\" onclick=\"javascript:document.location.href='forum_module.php?module=".$module."&num=".$num."';\"";
           $finTD = '</div>';
//           $content.= '<TR style="background-color:#e2e2e2;cursor:pointer;color:#24677A;'.
//                      'font-weight:bold;height:17px;padding:2px;">';
        }
        else
           $ajtTD = '';
//           $content.= '<TR>';
       //$content =' <tr id ="tr'.$num.'">';
       $content.= couleur_tr($comptChilds,'');
       $content.= "<TD valign='top' $ajtTD><div id=$num  style='margin-left:5px;cursor:pointer;font-weight:bold;color:#24677A;'".
                      " onclick=\"javascript:document.location.href='forum_module.php?form_forum=1&module=$module&num=$num&grandPa=$num';\">".
                      $row->fm_sujet_lb."</div></TD>";
       $content.= "<TD valign='top'> ".NewHtmlEntityDecode($row->fm_body_lb)."</TD>";
       $content.= "<TD valign='top'> $nom $prenom </TD>";
       $content.= "<TD valign='top'> ".$row->fm_datetime_dt."</TD>";
       if ($nbCh > 0)
       {
          $content.="<TD valign='top' $ajtTD> $nbCh </TD>";
       }
       else
       {
          $content.= "<TD>0</TD>";
       }
       if ($autmod == $_SESSION['id_user'] || $_SESSION['typ_user'] == 'ADMINISTRATEUR')
       {
          $mon_img1 = "<IMG SRC='images/modules/visible.gif' BORDER=0>";
          $mon_img2 = "<IMG SRC='images/modules/invisible.gif' BORDER=0>";
          $lien1 = "formation/forum_mod_modif.php?numero=$num&dl=oeil$num&objet=invisible&id_parc=$module";
          $lien2 = "formation/forum_mod_modif.php?numero=$num&dl=oeil$num&objet=visible&id_parc=$module";
          if ($visible == 1)
              $content.= "<td valign='top'><div id='oeil$num'><A href=\"javascript:void(0);\" ".
                         "onClick=\"javascript:appelle_ajax('$lien1'); ".
                         "\$('#oeil$num').empty();addContent_forum('$lien1');\" ".
                         bulle($hide,"","LEFT","ABOVE",80).
                         "$mon_img1</a></div></td>";
          elseif($visible == 0)
              $content.= "<td valign='top'><div id='oeil$num'><A href=\"javascript:void(0);\" ".
                         "onClick=\"javascript:appelle_ajax('$lien2');".
                         "\$('#oeil$num').empty();addContent_forum('$lien2');\" ".
                         bulle($makevisible,"","LEFT","ABOVE",100).
                         "$mon_img2</a></div></td>";
       }
       $content.= "</TR>";
     }// if visible
  }
}
echo $content;
echo "<div id='mien' class='cms'></div></body></html>";
function fn_reverse($num)
{
   GLOBAL $lg,$module,$childs,$autmod;
   $sql = mysql_query("select * from forums_modules where fm_module_no = $module and fm_parent_no=$num");
   $nombre = mysql_num_rows($sql);
   if ($nombre > 0)
   {
      while ($item = mysql_fetch_object($sql))
      {
        $numero = $item->fm_cdn;
        $visible = $item->fm_visible_on;
        if ($visible == 1 || ($visible == 0 && $autmod == $_SESSION['id_user']) || $_SESSION['typ_user'] == 'ADMINISTRATEUR')
        {
           $childs ++;
           fn_reverse($numero);
        }
      }

   }
   return $childs;
}
function fn_reverse_aff($num)
{
   GLOBAL $lg,$module,$contentAjt,$compteur,$grandPa,$compTT,$autmod;
   require ("lang$lg.inc.php");
   $boule_gif = "<img src='images/forum/boule.jpg' border='0'>";
   $trans_gif = "<img src='forum/images/trans.gif' border='0'>";
   $compTT++;
   $sql = mysql_query("select * from forums_modules where fm_module_no = $module and fm_parent_no=$num");
   $nombre = mysql_num_rows($sql);
   if ($nombre > 0)
   {
      $compteur++;
      while ($item = mysql_fetch_object($sql))
      {
        $numero = $item->fm_cdn;
        $visible = $item->fm_visible_on;
        if ($visible == 1 || ($visible == 0 && $autmod == $_SESSION['id_user']) || $_SESSION['typ_user'] == 'ADMINISTRATEUR')
        {

           $parent = $item->fm_parent_no;
           $auteur = $item->fm_auteur_no;
           $sqlAut = mysql_query("select * from utilisateur where util_cdn = $auteur");
           $nb_auteur = mysql_num_rows($sqlAut);
           $sql1 = mysql_query("select * from forums_modules where fm_module_no = $module and fm_parent_no=$numero");
           $nombre1 = mysql_num_rows($sql1);
           if ($nb_auteur > 0)
           {
               $nom = mysql_result($sqlAut,0,'util_nom_lb');
               $prenom = mysql_result($sqlAut,0,'util_prenom_lb');
           }
           else
           {
              $nom = 'Inconnu';
              $prenom = 'inconnu';
           }
           //$contentAjt.=' <tr id ="tr'.($numero).'">';<div onClick=\"\$('#tr".($parent)."').toggle();\">".$parent."</div>
           $contentAjt.= couleur_tr($compTT,'');
           $contentAjt.= "<TD><div id=$numero style='margin-left:5px;cursor:pointer;font-weight:bold;color:#24677A;'".
                      " onclick=\"javascript:document.location.href='forum_module.php?form_forum=1&module=$module&num=$numero&grandPa=$grandPa';\"".
                      bulle($mess_repondre,"","CENTER","ABOVE",60);
           $contentAjt.= tiret($compteur*2).$boule_gif.tiret(1);
           $contentAjt.= $item->fm_sujet_lb."</div></TD>";
           $contentAjt.= "<TD valign='top'><div id='obj$numero'> ".
                         html_entity_decode($item->fm_body_lb,ENT_QUOTES,'iso-8859-1')."</div></TD>";

           $contentAjt.= "<TD valign='top'> $nom $prenom </TD>";
           $contentAjt.= "<TD valign='top'>".$item->fm_datetime_dt."</TD>";
           if ($autmod == $_SESSION['id_user'] || $_SESSION['typ_user'] == 'ADMINISTRATEUR')
           {
               $contentAjt.= "<TD valign='top'><a href='forum_module.php?form_forum=1&modif=1&modif_num=$numero&module=$module&num=$numero&grandPa=$grandPa'>".
                             "<img src='images/repertoire/icoGrenomfich20.gif' border=0></a></TD>";
               $mon_img1 = "<IMG SRC='images/modules/visible.gif' BORDER=0>";
               $mon_img2 = "<IMG SRC='images/modules/invisible.gif' BORDER=0>";
               $lien1 = "formation/forum_mod_modif.php?numero=$numero&dl=oeil$numero&objet=invisible&id_parc=$module";
               $lien2 = "formation/forum_mod_modif.php?numero=$numero&dl=oeil$numero&objet=visible&id_parc=$module";
               if ($visible == 1)
                   $contentAjt.= "<td valign='top'><div id='oeil$numero'><A href=\"javascript:void(0);\" ".
                                 "onClick=\"javascript:appelle_ajax('$lien1'); ".
                                 "\$('#oeil$numero').empty();addContent_forum('$lien1');\" ".
                                 bulle($hide,"","LEFT","ABOVE",80).
                                 "$mon_img1</a></div></td>";
               elseif($visible == 0)
                   $contentAjt.= "<td valign='top'><div id='oeil$numero'><A href=\"javascript:void(0);\" ".
                                 "onClick=\"javascript:appelle_ajax('$lien2');".
                                 "\$('#oeil$numero').empty();addContent_forum('$lien2');\" ".
                                 bulle($makevisible,"","LEFT","ABOVE",100).
                                 "$mon_img2</a></div></td>";
           }
           $contentAjt.= "</TR>";
           if ($nombre1 > 0)
              fn_reverse_aff($numero);
           else
           {
              $compTT++;
           }
        }
      }
      $compteur--;
   }
   return $contentAjt;
}
?>
