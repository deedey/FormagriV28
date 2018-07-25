<?php
if (!isset($_SESSION)) session_start();
include ("include/UrlParam2PhpVar.inc.php");
if (!isset($_SESSION['id_user']) || $_SESSION['id_user'] == "")
{
  exit();
}
require 'graphique/admin.inc.php';
require 'config.inc.php';
require 'fonction.inc.php';
require 'fonction_html.inc.php';
require "langues/ress.inc.php";
require "lang$lg.inc.php";
dbConnect();
$date_dujour = date ("Y-m-d");
include 'style.inc.php';
//include ("click_droit.txt");
// Modification des titre de catégorie apportée le 25/08/03 ainsi que les verrous en cas de libellé déjà existant
if (isset($acces) && $acces == 1)
{
  $acces = 0;
  $supprime= mysql_query("DELETE FROM ressource_new where ress_cat_lb ='' and ress_doublon = 2");
}
if (isset($numero_cat) && $numero_cat > 0)
   $req_change_cat = mysql_query( "UPDATE ressource_new SET ress_modif_dt=\"$date_dujour\",ress_typress_no='$numero_cat' where ress_cat_lb=\"$change_cat\"");
// Suppression d'une catégorie
if (isset($supprimer) && $supprimer == 1 && isset($suppression) && $suppression == 1)
{
  $supprime= mysql_query("DELETE FROM ressource_new where ress_cdn='$num'");
  $supprime= mysql_query("DELETE FROM activite where act_ress_no='$num'");
  unset($supprimer);
  unset($suppression);
  $lien_sous_cat=1;
  $lien = "ressource_admin.php";
  $lien = urlencode($lien);
  echo "<script language=\"JavaScript\">";
     echo "document.location.replace(\"trace.php?link=$lien\")";
  echo "</script>";
  exit();

}
// Modification du titre d'une catégorie
if (isset($modifier) && $modifier == 1 && isset($modification) && $modification == 1)
{
    $new_cat = stripslashes("$new_cat");
    $req_radm = mysql_query("SELECT ress_cat_lb from ressource_new where ress_cat_lb =\"$new_cat\"");
    $nbr_radm = mysql_num_rows ($req_radm);
    if ($nbr_radm > 0)
      $la_cat = mysql_result($req_radm,0,"ress_cat_lb");
    if (ord($new_cat{0}) == 32 || ($nbr_radm > 0 && strcmp($la_cat,$new_cat) == 0)){
      echo "<CENTER><TABLE bgColor='#298CA0' cellspacing='2' cellpadding='0'><TR><TD>";
      echo "<TABLE bgColor='#FFFFFF' cellspacing='1' cellpadding='0' width='100%'>";
      echo "<TR><TD background=\"images/fond_titre_table.jpg\" colspan='2' height='37' align='center' valign='center'>";
      echo "<Font size='3' color='#FFFFFF'><B>$mess_menu_ress_adm</B></FONT></TD></TR>";
      echo "<TR><TD align='center' colspan='2'>";
      if ($nbr_radm > 0)
        echo"<CENTER><FONT SIZE='2'>$mrd_new_exist</FONT></CENTER><P>&nbsp;";
      else
        echo"<CENTER><FONT SIZE='2'>$mrd_no_modif_new</FONT></CENTER><P>&nbsp;";
      echo "</TD></TR><TR><TD align=left colspan=2><A HREF=\"javascript:history.back();\" onmouseover=\"img_annonce.src='images/fiche_identite/boutretourb.gif';return true;\" onmouseout=\"img_annonce.src='images/fiche_identite/boutretour.gif'\">".
         "<IMG NAME=\"img_annonce\" SRC=\"images/fiche_identite/boutretour.gif\" BORDER='0' onLoad=\"tempImg=new Image(0,0); tempImg.src='images/fiche_identite/boutretourb.gif'\"></A></TD></TR>";
      echo "</TABLE></TD></TR></TABLE>";
      exit;
    }else{
      $cat=stripslashes($cat);
      $new_cat = str_replace('&'," $et ",$new_cat);
      $new_cat = str_replace('+'," $et ",$new_cat);
      $new_cat = strip_tags($new_cat);
      $modif_titre = mysql_query("UPDATE ressource_new set ress_modif_dt=\"$date_dujour\",ress_cat_lb =\"$new_cat\" where ress_cat_lb =\"$cat\"");
      unset($modifier);
      unset($modification);
    }
  $flag=1;
  $lien_sous_cat=1;
}
// Ajout d'une catégorie
if (isset($ajout_cat) && $ajout_cat == 1 && isset($admin) && $admin == 1)
{
 $req_radm = mysql_query("SELECT ress_cat_lb from ressource_new where ress_cat_lb =\"$new_cat\"");
 $nbr_radm = mysql_num_rows ($req_radm);
  if (ord($new_cat{0}) == 32 || $nbr_radm > 0)
  {
    entete_simple($mess_menu_ress_adm);
    echo "<TR><TD align='center' colspan='2'>";
    echo"&nbsp;<P>";
    if ($nbr_radm > 0)
       echo"<CENTER><FONT SIZE='2'>$mrd_new_exist</FONT></CENTER><P>&nbsp;";
    else
       echo"<FONT SIZE=2>$mrd_rien</FONT><P>";
    echo "</TD></TR><TR><TD align=left colspan=2><A HREF=\"javascript:history.back();\" onmouseover=\"img_annonce.src='images/fiche_identite/boutretourb.gif';return true;\" onmouseout=\"img_annonce.src='images/fiche_identite/boutretour.gif'\">".
         "<IMG NAME=\"img_annonce\" SRC=\"images/fiche_identite/boutretour.gif\" BORDER='0' onLoad=\"tempImg=new Image(0,0); tempImg.src='images/fiche_identite/boutretourb.gif'\"></A></TD></TR>";
    echo "</TABLE></TD></TR></TABLE></TD></TR></TABLE>";
    exit;
  }
  $new_cat = str_replace('&',$et,$new_cat);
  $new_cat = str_replace('+',$et,$new_cat);
  $new_cat = strip_tags($new_cat);
  $id_ress = Donne_ID ($connect, "select max(ress_cdn) from ressource_new");
  $sql_insere= mysql_query("INSERT INTO ressource_new (ress_cdn,ress_cat_lb,ress_typress_no,ress_url_lb,ress_auteurs_cmt,ress_publique_on,ress_titre,ress_desc_cmt,ress_create_dt,ress_ajout,ress_public_no,ress_type,ress_support,ress_niveau) VALUES ('$id_ress',\"$new_cat\",'$parente','','','OUI','','',\"$date_dujour\",'foad','TOUT','','1','5')");
  $admin = 0;
  $flag=1;
  $lien_sous_cat=1;
  $lien = "ressource_admin.php";
  $lien = urlencode($lien);
  echo "<script language=\"JavaScript\">";
     echo "document.location.replace(\"trace.php?link=$lien\")";
  echo "</script>";
  exit();
}

$nom = $_SESSION['name_user'];
$prenom = $_SESSION['prename_user'];
$dir="ressources/".$login."_".$id_user;

// Sélection et Affichage des parents principaux (caractéristique: ne contiennent pas d'url)
$resultat_sql=  mysql_query ("select * from ressource_new  where ress_typress_no=0 AND ress_titre='' AND ress_url_lb='' order by ress_cat_lb asc");
$nombre= mysql_num_rows ($resultat_sql);
entete_simple($mess_menu_ress_adm);
echo '<tr><td>'.aide_div("ressources",0,0,2,0);
echo '<div style="float:left;margin-left:8px;"><a href="MindMapRess.php" class="bouton_new"'.
      ' title="Générer une carte heuristique des catégories">Arbre des catégories</a></div>';
$ReqMe = mysql_query("select * from ressource_new where ress_titre != ''");
$nbrReqMe = mysql_num_rows($ReqMe);
if ($nbrReqMe > 0)
    echo '<div style="float:left;margin-left:1px;"><a href="MindMapMesRess.php?vueHtml=1&json=1"'.
         ' title="Important!! La virgule sert de séparateur" class="bouton_new">Export csv</a></div>';
echo '</td></tr>';
if (!isset($ajouter) && !isset($inserer))
  echo"<TR><TD><TABLE align=Top width=100% border='0'><TR><TD align=left>";
if (isset($lien_sous_cat) && $lien_sous_cat == 1)
{
  $lien="ressource_admin.php?admin=1&flg=$flg&favoris=$favoris&parcours=$parcours&id_parc=$id_parc&doublon=$doublon&rep=$rep&id_ref=$id_ref&id_seq=$id_seq&id_act=$id_act&modif_act=$modif_act&creat=$creat";
  $lien = urlencode($lien);
    echo "<DIV>";
  echo"<A href=\"trace.php?link=$lien\" class='sequence'><B>$mess_accueil $signe</B></A>";
}
//Affichage de l'arborescence supérieure
    $pointeur=$parente;
    $sous_c = array();
    $parbis = array();
    $par1 = array();
    $pr = 0;
    while ($pointeur != 0)
    {
      $ptr=0;
      $resultat=mysql_query ("select * from ressource_new where ress_cdn=$pointeur");
      $nbr_result=  mysql_num_rows ($resultat);
      $sous_c[$pr]= mysql_result($resultat,$ptr,"ress_cat_lb");
      $parbis[$pr]=  mysql_result($resultat,$ptr,"ress_typress_no");
      $par1[$pr]= mysql_result($resultat,$ptr,"ress_cdn");
      $pointeur=$parbis[$pr];
      $pr++;
    }
    $pr--;
    $ptr=0;// pointeur nécessaire pour l'affichage de l'ajout
    while ($pr >-1)
    {
      $ptr++;
      $lien="ressource_admin.php?flg=$flg&admin=$admin&favoris=$favoris&parcours=$parcours&id_parc=$id_parc&doublon=$doublon&rep=$rep&lien_sous_cat=1&parente=$par1[$pr]&sous_catalogue=$sous_c[$pr]&cat=$cat&sous_cat=$sous_c[$pr]&niveau=1&id_ref=$id_ref&id_seq=$id_seq&id_act=$id_act&modif_act=$modif_act&modif_act=$modif_act&creat=$creat";
      $lien = urlencode($lien);
      if ($ajouter == 1 && !$inserer)
      {
       if ($pr == 0)
         $ajout_ajout .= " <span style=\"font-weight:bold;color:#D45211\"> $sous_c[$pr] </span></div>";
       else
         $ajout_ajout .= "<A href=\"trace.php?link=$lien\" class='sequence'><B> $sous_c[$pr] </B></A>$signe";
      }
      else
      {
       if ($pr == 0)
         echo " <span style=\"font-weight:bold;color:#D45211\"> $sous_c[$pr] </span></div>";
       else
         echo "<A href=\"trace.php?link=$lien\" class='sequence'><B> $sous_c[$pr] </B></A>$signe";
      }
      $pr--;
    }
//Affichage de la catégorie courante
    $pr++;
    if (isset($sous_c[$pr]))
       $catego = strtoupper($sous_c[$pr]);
//Affichage de la structure d'accueil à arborescence nulle
 if ((!isset($lien_sous_cat) || (isset($lien_sous_cat) && $lien_sous_cat == 0)) &&
     (!isset($ajouter) || (isset($ajouter) &&  $ajouter== 0)) &&
     (!isset($modifier) || (isset($ajouter) &&  $ajouter== 0)) &&
     (!isset($supprimer) || (isset($ajouter) &&  $ajouter== 0)))
 {
  echo"<TABLE align=Top width='100%' cellspacing='5' cellpadding='8'><TR>";
  $i=0;
  while ($i != $nombre){
   $l=$i+1;
   $cat = mysql_result($resultat_sql,$i,"ress_cat_lb");
   $pater = mysql_result($resultat_sql,$i,"ress_cdn");
   //Détermination des sous-catégories
    echo"<TD ALIGN=left>";
    if (($i/2) == (floor($i/2))){
      echo"</TD></TR><TR><TD ALIGN=left width = '50%'><TABLE width='100%'><TR>";
    }else {
      echo"<TD ALIGN=left width = '50%'><TABLE width='100%'><TR>";
    }
      $lien="ressource_admin.php?flg=$flg&doublon=$doublon&rep=$rep&lien_sous_cat=1&parente=$pater&niveau=1&cat=$cat&sous_catalogue=$cat";
      $lien = urlencode($lien);
      echo"<TD width='85%'><DIV id='sequence'><A href=\"trace.php?link=$lien\"><B>  $cat </B></A></DIV></TD>";
    if ($typ_user == "ADMINISTRATEUR"){
       $ress_exist = mysql_query ("select * from ressource_new where ress_typress_no = '$pater' AND ress_titre='' AND ress_url_lb=''");
       $Nb_ress = mysql_num_rows ($ress_exist);
       if ($Nb_ress == 0)
       {
          $lien="ressource_admin.php?flg=$flg&lien_sous_cat=0&admin=1&supprimer=1&num=$pater";
          $lien = urlencode($lien);
          echo "<TD width='5%'><A href=\"trace.php?link=$lien\"><B><IMG SRC=\"images/messagerie/icopoubelressour.gif\" height=\"20\" width=\"15\" align='bottom' ALT=\"$mrd_sup\" BORDER=0></B></A></TD>";
       }else
         echo "<TD width='5%'>&nbsp;</TD>";
       $lien="ressource_admin.php?flg=$flg&lien_sous_cat=0&admin=1&modifier=1&num=$pater&cat=$cat&parente=$pater";
       $lien = urlencode($lien);
       echo "<TD width='5%'><A href=\"trace.php?link=$lien\"><B><IMG SRC=\"images/repertoire/icoGrenomfich20.gif\" align=bottom ALT=\"$mrd_modif_tit : $cat\" BORDER=0></B></A></TD>";
       $lien = "ressource_admin.php?flg=$flg&lien_sous_cat=0&parente=$pater&parcours=$parcours&ajouter=1&admin=1";
       $lien = urlencode($lien);
       echo" <TD width='5%'><A href=\"trace.php?link=$lien\"><IMG SRC=\"images/repertoire/icoajoutarboadmin.gif\" ALT=\"$mrd_ajout\" BORDER=0></A></TD></TR>";
    }
      $sous_resultat_sql=mysql_query ("select * from ressource_new where ress_typress_no=$pater AND ress_titre='' AND ress_url_lb='' group by ress_cat_lb");
      $nbr_sous_result=  mysql_num_rows ($sous_resultat_sql);
      if  ($nbr_sous_result != 0){
        $nbr_compte=$nbr_sous_result-1;
        $niveau=1;
        $sous_i=0;
        echo "<TR><TD colspan='4'><DIV id='titre'>";
        while ($sous_i<3){
          $sous_cat= mysql_result($sous_resultat_sql,$sous_i,"ress_cat_lb");
          $parentebis=  mysql_result($sous_resultat_sql,$sous_i,"ress_typress_no");
          $parente1= mysql_result($sous_resultat_sql,$sous_i,"ress_cdn");
          $lien="ressource_admin.php?flg=$flg&doublon=$doublon&rep=$rep&lien_sous_cat=1&parentebis=$parentebis&parente=$parente1&niveau=$niveau&cat=$cat&sous_catalogue=$sous_cat";
          $lien = urlencode($lien);
          echo"<A href=\"trace.php?link=$lien\"><B> $sous_cat </B></A> ";
          if (($sous_i != 2 && ($nbr_sous_result > 3 || $nbr_sous_result == 3)) || ($sous_i != 1 && $nbr_sous_result < 3 && $nbr_sous_result > 1)){
            echo" , ";
          }elseif ($sous_i == $nbr_compte && ($nbr_sous_result < 3 || $nbr_sous_result == 3)){
            echo".";
          }else{
            echo"...";
          }
          $sous_i++;
          if($sous_i == $nbr_sous_result){
            break;
          }
        }
     $niveau=0;
     echo "</DIV></TD></TR></TABLE>";
   }else
     echo "</TABLE>";
  $i++;
  }
   echo"</FONT></CENTER></TD></TR>";
 }
echo"</TABLE>";
     if ($typ_user=="ADMINISTRATEUR" && $lien_sous_cat == 0 && !$ajouter && !$modifier && !$supprimer){
        $lien = "ressource_admin.php?flg=$flg&lien_sous_cat=0&parente=0&ajouter=1&admin=1";
        $lien = urlencode($lien);
        echo" <BR>$bouton_gauche<A href=\"trace.php?link=$lien\">$mrd_ajt</A>$bouton_droite<BR>";
     }
//if (!$lien_sous_cat){
//  echo"<hr size=2 width=760 align=center color=silver>";
//}
if (isset($doublon) && $doublon==2)
{
 echo"<Font color=red><SMALL><B>$mrc_nav</B></SMALL></Font></CENTER>";
}
// Affichage des composants d'une arborescence avec compteur d'enregistrements
if ((!isset($lien_sous_cat)) && (!isset($ajouter)) && (!isset($modifier)) && (!isset($supprimer)))
{
  echo "</TD></TR></TABLE>";
  echo "</TD></TR></TABLE></TD></TR></TABLE>";
  exit;
}
if (!isset($ajouter) && !isset($modifier) && !isset($supprimer))
{
     $niveau++;
     $sous_resultat_sql=mysql_query ("SELECT * FROM ressource_new  WHERE ress_typress_no='$parente' AND ress_titre='' AND ress_url_lb='' GROUP BY ress_cat_lb");
     $nbr_sous_result= mysql_num_rows($sous_resultat_sql);
     echo"<hr size=2 width=760 align=center color=silver>";
     echo"<TABLE width='100%' border='0'><TR><TD>";
     $i=0;
     $parental1=array();
     $parental=array();
     while ($i<$nbr_sous_result){
       $sous_cat = mysql_result($sous_resultat_sql,$i,"ress_cat_lb");
       $parente1 = mysql_result($sous_resultat_sql,$i,"ress_cdn");
       $parente2 = mysql_result($sous_resultat_sql,$i,"ress_typress_no");
       $parental[$i] = $parente2;
       $parental1[$i] = $parente1;
       $req = mysql_query("SELECT COUNT(*)  from ressource_new where ress_cat_lb=\"$sous_cat\" AND ress_titre != ''");
       $nomb1 = mysql_result($req,0);
       $req = mysql_query("SELECT COUNT(*)  from ressource_new where ress_typress_no='$parente1' AND ress_titre != ''");
       $nomb2 = mysql_result($req,0);
       if (($i/2) == (floor($i/2)))
          echo"</TD></TR><TR><TD ALIGN=left width = '45%'><TABLE width='100%' border='0'><TR>";
       else
          echo"<TD width = '10%'>&nbsp;</TD><TD ALIGN=left width = '45%'><TABLE width='100%' border='0'><TR>";
       // Affiche Arobace en cas d'existence de sous-catégories
         $req_sql = mysql_query ("SELECT  COUNT(*) FROM ressource_new  WHERE ress_typress_no='$parente1' AND ress_titre='' AND ress_url_lb=''");
         $resreq = mysql_result($req_sql,0);
         if ($resreq != 0)
            $ss_cat_exist = 1;
         else
            $ss_cat_exist = 0;
         if ($resreq != 0)
            $nombre = $nomb1+$nomb2;
         else
            $nombre = $nomb1;
       // Fin d'affichage d'arobace
        $lien="ressource_admin.php?flg=$flg&favoris=$favoris&parcours=$parcours&id_parc=$id_parc&doublon=$doublon&rep=$rep&lien_sous_cat=1&i=$i&sous_catalogue=$sous_cat&detail=1&parente=$parental1[$i]&niveau=$niveau&id_ref=$id_ref&id_seq=$id_seq&id_act=$id_act&modif_act=$modif_act&creat=$creat";
        $lien = urlencode($lien);
        if ($ss_cat_exist)
           echo "<TD width='75%'><IMG SRC=\"images/plus.gif\" border='0'>&nbsp;";
        else
           echo "<TD width='75%'><IMG SRC=\"images/spacer.gif\" border='0'>".nbsp(4);
        echo"<A HREF=\"trace.php?link=$lien\" class='sequence'> $sous_cat </A>  ";
         if ($nombre > 1)
             $aff_nbre = "($nombre ".strtolower($msq_ress)."s)";
         elseif ($nombre == 1)
             $aff_nbre = "($nombre ".strtolower($msq_ress).")";
         else
             $aff_nbre = "";
         echo "$aff_nbre</TD>";
         if ($typ_user == "ADMINISTRATEUR" && $nombre == 0)
         {
            $ress_exist = mysql_query ("select * from ressource_new where ress_typress_no = $parente1 and $nombre = '0'");
            $Nb_ress = mysql_num_rows ($ress_exist);
            if ($Nb_ress == 0)
            {
               $lien="ressource_admin.php?flg=$flg&lien_sous_cat=0&admin=1&supprimer=1&num=$parente1&parente=$parente";
               $lien = urlencode($lien);
               echo "<TD width='5%'><A href=\"trace.php?link=$lien\"><B><IMG SRC=\"images/repertoire/icopoubelressour.gif\" height=\"20\" width=\"15\" align='bottom' ALT=\"$mrd_sup\" BORDER=0></B></A></TD>";
            }
            else
              echo "<TD width='5%'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</TD>";
         }
         else
            echo "<TD width='5%'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</TD>";
         $lien="ressource_admin.php?flg=$flg&lien_sous_cat=0&admin=1&modifier=1&num=$parente1&cat=$sous_cat";
         $lien = urlencode($lien);
         echo "<TD width='5%'><A href=\"trace.php?link=$lien\"><B><IMG SRC=\"images/repertoire/icoGrenomfich20.gif\" align=bottom ALT=\"$mrd_modif_tit\" BORDER=0></B></A></TD>";
         if ($typ_user=="ADMINISTRATEUR")
         {
            $lien = "ressource_admin.php?flg=$flg&$lien_sous_cat=0&parente=$parente1&ajouter=1&admin=1";
            $lien = urlencode($lien);
            echo" <TD width='5%'><A href=\"trace.php?link=$lien\"><IMG SRC=\"images/repertoire/icoajoutarboadmin.gif\" ALT=\"$mrd_ajout \" BORDER=0></A></TD>";
         }else
            echo "<TD width='5%'>&nbsp;</TD>";
         if (!isset($par1[$pr])) $par1[$pr] = '';
         if (!isset($sous_c[$pr])) $sous_c[$pr] = '';
         $lien = "ressource_admin.php?change_categorie=1&change_num=$parente1&flg=$flg&lien_sous_cat=1&parente=$par1[$pr]&sous_catalogue=$sous_c[$pr]&cat=$cat&sous_cat=$sous_c[$pr]&niveau=1&page=$page&total_aff=$total_aff";
         $lien = urlencode($lien);
         $aff_lien  = "<TD>&nbsp;&nbsp;<A HREF=\"trace.php?link=$lien\" onMouseOver=\"overlib('".addslashes($mess_chg_catcat);
         if ($ss_cat_exist > 0)
            $aff_lien .= "  ".addslashes($mess_chg_catcat1);
         $aff_lien .= "',ol_hpos,LEFT,ABOVE,WIDTH,'250',DELAY,'800',CAPTION, '')\" onMouseOut=\"nd()\"><IMG SRC=\"images/aller_retour_haut.gif\" border=0></A></TD>";
         echo $aff_lien;
         if ($change_categorie == 1 && $change_num == $parente1 && !isset($numero_cat)){
            $req_cat = mysql_query("select ress_cat_lb,ress_cdn from ressource_new where ress_titre = \"\" AND  ress_url_lb =\"\" GROUP BY ress_cat_lb ORDER BY ress_cat_lb");
            $categ_parent = GetDataField ($connect,"select ress_cat_lb from ressource_new where ress_cdn = $parente2","ress_cat_lb");
            echo "<form name=\"form\">";
            echo "<TD><SELECT name=\"select\" onChange=javascript:appel_w(form.select.options[selectedIndex].value)>";
            echo "<OPTION>$categ_parent</OPTION>";
            $nb_req_cat = mysql_num_rows($req_cat);
            $cat_cpt = 0;
            while ($cat_cpt < $nb_req_cat){
                  $numero_cat = mysql_result($req_cat,$cat_cpt,"ress_cdn");
                  $new_nom_cat = mysql_result($req_cat,$cat_cpt,"ress_cat_lb");
                  $carac_cat = strlen($new_nom_cat);
                  if ($carac_cat > 35)
                     $nom_cat1 = substr($new_nom_cat,0,33)."..";
                  else
                     $nom_cat1 = $new_nom_cat;

                  $lien="ressource_admin.php?change_categorie=1&numero_cat=$numero_cat&change_num=$parente1&change_cat=$sous_cat&flg=$flg&lien_sous_cat=1&parente=$par1[$pr]&sous_catalogue=$sous_c[$pr]&cat=$cat&sous_cat=$sous_c[$pr]&niveau=1&page=$page&total_aff=$total_aff\"";
                  $lien = urlencode($lien);
                  echo "<OPTION value=\"trace.php?link=$lien\" title=\"$new_nom_cat\">$nom_cat1</OPTION>";
                  $cat_cpt++;
            }
            echo "</SELECT></TD></FORM>";
         }else
            echo "<TD width='5%'>&nbsp;</TD>";
         echo"</TR></TABLE></TD>";
       $i++;
     }
     echo"</TD></TR></TABLE>";
     // Affichages des enregistrements d'une sous-catégorie en cours
     if (empty($page)) $page = 1;
     $nb_affiche = 4;
     $req_nb_aff = mysql_query("select count(*) from ressource_new where ress_cat_lb=\"$sous_catalogue\" AND ress_titre != ''");
     $total_aff = mysql_result($req_nb_aff,0);
     $debut = ($page - 1) * $nb_affiche;
     $req2=mysql_query("select * from ressource_new where ress_cat_lb=\"$sous_catalogue\" AND ress_titre != '' LIMIT $debut,$nb_affiche");
     if (!$req2){
       echo $mrc_rien;
       exit;
     }
     if ($total_aff > 1)
        $aff_tot = strtolower($msq_ress)."s";
     elseif ($total_aff == 1)
        $aff_tot = strtolower($msq_ress);
     else
        $aff_tot = "";
     $nbrt= mysql_num_rows($req2);
     if ($req_nb_aff > 0 && isset($aff_nbre))
         echo"<hr size=2 width=760 align=center color=silver>";
     echo"<TABLE align=Top width=100%>";
     if ($total_aff > $nb_affiche){
       echo "<TR bgcolor='#CEE6EC'><TD align=left><SMALL><font color=marroon><B> $mess_admin_total: $total_aff $aff_tot</B></font></SMALL></TD><TD align=middle>";
       $nb_pages = ceil($total_aff/$nb_affiche);
       if ($page == 1){
         echo "<IMG SRC=\"images/gauche_off.gif\" border=0></a>";
         echo "<IMG SRC=\"images/gauche_off.gif\" border=0></a>&nbsp;&nbsp;&nbsp;&nbsp;";
       }else{
         $lien = "ressource_admin.php?flg=$flg&favoris=$favoris&parcours=$parcours&id_parc=$id_parc&doublon=$doublon&rep=$rep&lien_sous_cat=1&parente=$par1[$pr]&sous_catalogue=$sous_c[$pr]&cat=$cat&sous_cat=$sous_c[$pr]&niveau=1&id_ref=$id_ref&id_seq=$id_seq&id_act=$id_act&modif_act=$modif_act&creat=$creat&page=1&total_aff=$total_aff";
         $lien = urlencode($lien);
         echo "<a href=\"trace.php?link=$lien\"><IMG SRC=\"images/gauche_on.gif\" border=0></a>";
         echo "<a href=\"trace.php?link=$lien\"><IMG SRC=\"images/gauche_on.gif\" border=0></a>&nbsp;&nbsp;&nbsp;&nbsp;";
       }
       if ($page == 1){
         echo "<IMG SRC=\"images/gauche_off.gif\" border=0></a>&nbsp;&nbsp;&nbsp;&nbsp;";
       }else{
         $page_avant = $page-1;
         $lien = "ressource_admin.php?flg=$flg&favoris=$favoris&parcours=$parcours&id_parc=$id_parc&doublon=$doublon&rep=$rep&lien_sous_cat=1&parente=$par1[$pr]&sous_catalogue=$sous_c[$pr]&cat=$cat&sous_cat=$sous_c[$pr]&niveau=1&id_ref=$id_ref&id_seq=$id_seq&id_act=$id_act&modif_act=$modif_act&creat=$creat&page=$page_avant&total_aff=$total_aff";
         $lien = urlencode($lien);
         echo "<a href=\"trace.php?link=$lien\"><IMG SRC=\"images/gauche_on.gif\" border=0></a>&nbsp;&nbsp;&nbsp;&nbsp;";
       }
       for ($affichage = 1;$affichage <= $nb_pages;$affichage++){
         if ($page == $affichage)
           echo "<font color=marroon><B> $affichage</B></font>";
         else {
           $lien = "ressource_admin.php?flg=$flg&favoris=$favoris&parcours=$parcours&id_parc=$id_parc&doublon=$doublon&rep=$rep&lien_sous_cat=1&parente=$par1[$pr]&sous_catalogue=$sous_c[$pr]&cat=$cat&sous_cat=$sous_c[$pr]&niveau=1&id_ref=$id_ref&id_seq=$id_seq&id_act=$id_act&modif_act=$modif_act&creat=$creat&page=$affichage&total_aff=$total_aff";
           $lien = urlencode($lien);
           echo "<a href=\"trace.php?link=$lien\"> $affichage</a>";
         }
         echo "&nbsp;&nbsp;&nbsp;&nbsp;";
       }
       if ($page == $nb_pages){
         echo "<IMG SRC=\"images/droite_off.gif\" border=0></a>&nbsp;&nbsp;&nbsp;&nbsp;";
       }else{
         $page_suivant = $page+1;
         $lien = "ressource_admin.php?flg=$flg&favoris=$favoris&parcours=$parcours&id_parc=$id_parc&doublon=$doublon&rep=$rep&lien_sous_cat=1&parente=$par1[$pr]&sous_catalogue=$sous_c[$pr]&cat=$cat&sous_cat=$sous_c[$pr]&niveau=1&id_ref=$id_ref&id_seq=$id_seq&id_act=$id_act&modif_act=$modif_act&creat=$creat&page=$page_suivant&total_aff=$total_aff";
         $lien = urlencode($lien);
         echo "<a href=\"trace.php?link=$lien\"><IMG SRC=\"images/droite_on.gif\" border=0></a>&nbsp;&nbsp;&nbsp;&nbsp;";
       }
       if ($page == $nb_pages){
         echo "<IMG SRC=\"images/droite_off.gif\" border=0></a>";
         echo "<IMG SRC=\"images/droite_off.gif\" border=0></a>&nbsp;&nbsp;&nbsp;&nbsp;";
       }else{
         $lien = "ressource_admin.php?flg=$flg&favoris=$favoris&parcours=$parcours&id_parc=$id_parc&doublon=$doublon&rep=$rep&lien_sous_cat=1&parente=$par1[$pr]&sous_catalogue=$sous_c[$pr]&cat=$cat&sous_cat=$sous_c[$pr]&niveau=1&id_ref=$id_ref&id_seq=$id_seq&id_act=$id_act&modif_act=$modif_act&creat=$creat&page=$nb_pages&total_aff=$total_aff";
         $lien = urlencode($lien);
         echo "<a href=\"trace.php?link=$lien\"><IMG SRC=\"images/droite_on.gif\" border=0></a>";
         echo "<a href=\"trace.php?link=$lien\"><IMG SRC=\"images/droite_on.gif\" border=0></a>&nbsp;&nbsp;&nbsp;&nbsp;";
       }
       echo"</TD></TR>";
    }
       $l=0;
       while ($l<$nbrt){
         echo couleur_tr($l+1,'');
         echo "<TD colspan=2>";
         $num= mysql_result($req2,$l,"ress_cdn");
         $par= mysql_result($req2,$l,"ress_typress_no");
         $init= mysql_result($req2,$l,"ress_ajout");
         $auteur_ress= mysql_result($req2,$l,"ress_auteurs_cmt");
         $categ= mysql_result($req2,$l,"ress_cat_lb");
         $descrip= mysql_result($req2,$l,"ress_desc_cmt");
         $liens= mysql_result($req2,$l,"ress_url_lb");
         $doub= mysql_result($req2,$l,"ress_doublon");
         $niv= mysql_result($req2,$l,"ress_niveau");
         $publique= mysql_result($req2,$l,"ress_publique_on");
         $titr= mysql_result($req2,$l,"ress_titre");
         $sup= mysql_result($req2,$l,"ress_support");
         $assise = 0;$avertir = 0;
         if ($sup == "Url" || $sup == "Web")
           $assise=1;
         $resstype= mysql_result($req2,$l,$type_ress);
         if ($id_seq>0 && $favoris == 1 && $liens){
            $nom_seq = GetDataField ($connect,"select seq_titre_lb from sequence where seq_cdn=$id_seq","seq_titre_lb");
            $lien = "favoris.php?ajouter=1&seq=$id_seq&via=1&id_ress=$num";
            $lien = urlencode($lien);
            ?><input type=checkbox  onMouseOver="overlib('<?php  echo "<FONT COLOR=blue>$mrc_clik<FONT COLOR=red><B>$nom_seq</B></FONT>..</FONT>";?>',ol_hpos,RIGHT,WIDTH,'300',CAPTION, '<center><?php  echo $mrc_refer;?></center>')" onMouseOut="nd()" onclick="window.location='trace.php?link=<?php  echo $lien;?>';return true;"><?php
         }
         if (($titr !="") && ((($typ_user != "APPRENANT")  && ($publique=="NON")) || ($publique == "OUI"))){
            if ($assise == 1 && ((($typ_user == "ADMINISTRATEUR" || $init == $login) && $publique=="NON") || $publique == "OUI"))
              $ok=1;
            else
              $liens=0;
            if ($assise == 1){
               $req_serv = mysql_query("select * from serveur_ressource");
               $nb_req_serv = mysql_num_rows($req_serv);
               if ($nb_req_serv > 0){
                  $transit = 0;
                  $ir = 0;
                  while ($ir < $nb_req_serv){
                     $adr = mysql_result($req_serv,$ir,"serveur_nomip_lb");
                     $params = mysql_result($req_serv,$ir,"serveur_param_lb");
                     $label = mysql_result($req_serv,$ir,"serveur_label_lb");
                     if ($label != ""){
                        if (strstr($liens,$adr) && strstr($liens,$label)){
                           $liens = str_replace("%label=$label","",$liens);
                           $liens .= $params;
                           $transit = 1;
                           break;
                        }
                     }elseif ($label == "" && strstr($liens,"label=")){
                        $ir++;
                        continue;
                     }elseif ($label == "" && !strstr($liens,"label=")){
                        if (strstr($liens,$adr)){
                           $liens .= $params;
                           $transit = 1;
                           break;
                        }
                     }
                   $ir++;
                  }
                  $liens = urldecode($liens);
                  if ($transit == 1){
                     echo "<A HREF='#' onclick=\"window.open('$liens','','resizable=yes,scrollbars=yes,status=no')\">";
                     echo "<B>$titr </B></A><SMALL> ($mrc_auteur:$auteur_ress,&nbsp;$categ&nbsp;,&nbsp;$sup&nbsp;,&nbsp;$mess_niveau&nbsp;$niv&nbsp;,&nbsp;$resstype)";
                  }
               }
               if ($liens == ""){
                  $descrip = str_replace("\r\n","<BR>",$descrip);
                  echo "<A HREF=\"javascript=void(0);\" onMouseOver=\"overlib('<B>".addslashes($mrc_tit_ress)."</B> : ".
                        addslashes($titr)."<BR><B>".addslashes($mrc_auteur)."</B> : ".
                        addslashes($auteur_ress)."<BR><B>$mess_desc/".addslashes($mrc_mod_emp)."</B> : ".
                        htmlspecialchars(addslashes($descrip))."<BR><B>".addslashes($mrc_opr,ENT_QUOTES,'ISO-8859-1')."</B> : ".
                        addslashes($resstype)."<BR><B>".addslashes($mrc_sup_ress)."</B> : $sup',ol_hpos,RIGHT,WIDTH,300,CAPTION, ".
                        "'<center>$ress_ss_lien</center>')\" onMouseOut=\"nd()\"><B>$titr </B></A>";
                  echo "<B>$titr</B><SMALL> ($mrc_auteur:$auteur_ress,&nbsp;$categ&nbsp;,&nbsp;$sup&nbsp;,&nbsp;$mess_niveau&nbsp;$niv&nbsp;,&nbsp;$resstype)&nbsp;,&nbsp;";
               }elseif ((strstr($liens,"ParWeb")) || (strstr($liens,"parweb"))  || (strstr($liens,"Legweb"))  || (strstr($liens,"legweb")) || (strstr($liens,"Tatweb"))  || (strstr($liens,"tatweb")) || (strstr($liens,"Qcmweb"))  || (strstr($liens,"qcmweb")) || (strstr($liens,"Elaweb")) || (strstr($liens,"elaweb"))){
                  $liens .= "&nom=pat&prenom=del&email=totot.fr@educ.fr&pathscore=";
                  echo "<A HREF='#' onclick=\"window.open('$liens','','resizable=yes,scrollbars=yes,status=no')\">";
                  echo "<B>$titr </B></A><SMALL> ($mrc_auteur:$auteur_ress,&nbsp;$categ&nbsp;,&nbsp;$sup&nbsp;,&nbsp;$mess_niveau&nbsp;$niv&nbsp;,&nbsp;$resstype)";
               }elseif (strstr($liens,"bder")){
                  $avertir = 1;
                  $lien=$liens;
                  $lien = urlencode($lien);
                  echo "<A HREF='#' onclick=\"window.open('trace.php?link=$lien','','resizable=yes,scrollbars=yes,status=no')\" onMouseOver=\"overlib('$mrc_bder &nbsp;<font color=red>$sup</font> ..',ol_hpos,RIGHT,WIDTH,'250',CAPTION, '<center>$mrc_ress</center>')\" onMouseOut=\"nd()\"><B>$titr </B></A>";
                  echo "<SMALL> ($mrc_auteur:$auteur_ress,&nbsp;$categ&nbsp;,&nbsp;$sup&nbsp;,&nbsp;$mess_niveau&nbsp;$niv&nbsp;,&nbsp;$resstype)&nbsp;,&nbsp;";
               }elseif (strstr($liens,"qcm.php")){
                 $lien=$liens."&provenance=recherche";
                 $lien = urlencode($lien);
                 echo"<A HREF='#' onclick=\"window.open('trace.php?link=$lien','','resizable=yes,scrollbars=yes,status=no')\">";
                 echo "<B>$titr </B></A><SMALL> ($mrc_auteur:$auteur_ress,&nbsp;$categ&nbsp;,&nbsp;$sup&nbsp;,&nbsp;$mess_niveau&nbsp;$niv&nbsp;,&nbsp;$resstype)";
               }elseif (strstr($liens,"http://www.editions.educagri.fr/educagriNet")){
                 $lien=$liens."&url=$url_ress&auth_cdn=$auth_cdn";
                 $lien = urlencode($lien);
                 echo"<A HREF='#' onclick=\"window.open('trace.php?link=$lien','','resizable=yes,scrollbars=yes,status=no')\">";
                 echo "<B>$titr </B></A><SMALL> ($mrc_auteur:$auteur_ress,&nbsp;$categ&nbsp;,&nbsp;$sup&nbsp;,&nbsp;$mess_niveau&nbsp;$niv&nbsp;,&nbsp;$resstype)";
               }elseif (strstr(strtolower($liens),".doc") || strstr(strtolower($liens),".xls") || strstr(strtolower($liens),".xlt")){
                 echo "<A HREF='$liens' target='_blank'><B>$titr</B></A><SMALL> ($mrc_auteur:$auteur_ress,&nbsp;$categ&nbsp;,&nbsp;$sup&nbsp;,&nbsp;$mess_niveau&nbsp;$niv&nbsp;,&nbsp;$resstype)";
               }elseif ($transit != 1){
                  echo "<A HREF='#' onclick=\"window.open('$liens','','resizable=yes,scrollbars=yes,status=no')\">";
                  echo"<B>$titr</B></A><SMALL> ($mrc_auteur:$auteur_ress,&nbsp;$categ&nbsp;$sup&nbsp;,&nbsp;$mess_niveau&nbsp;$niv&nbsp;,&nbsp;$resstype)";
               }
             }else
                echo"<B>$titr </B><SMALL> ($mrc_auteur:$auteur_ress,&nbsp;$categ&nbsp;$sup&nbsp;,&nbsp;$mess_niveau&nbsp;$niv&nbsp;,&nbsp;$resstype)";
         }
         echo "</TD></TR>";
         $l++;
       }
     if ($total_aff > $nb_affiche){
       echo "<TR bgcolor='#CEE6EC'><TD align=left><SMALL><font color=marroon><B> $mess_admin_total: $total_aff $aff_tot</B></font></SMALL></TD><TD align=middle>";
       $nb_pages = ceil($total_aff/$nb_affiche);
       if ($page == 1){
         echo "<IMG SRC=\"images/gauche_off.gif\" border=0></a>";
         echo "<IMG SRC=\"images/gauche_off.gif\" border=0></a>&nbsp;&nbsp;&nbsp;&nbsp;";
       }else{
         $lien = "ressource_admin.php?flg=$flg&favoris=$favoris&parcours=$parcours&id_parc=$id_parc&doublon=$doublon&rep=$rep&lien_sous_cat=1&parente=$par1[$pr]&sous_catalogue=$sous_c[$pr]&cat=$cat&sous_cat=$sous_c[$pr]&niveau=1&id_ref=$id_ref&id_seq=$id_seq&id_act=$id_act&modif_act=$modif_act&creat=$creat&page=1&total_aff=$total_aff";
         $lien = urlencode($lien);
         echo "<a href=\"trace.php?link=$lien\"><IMG SRC=\"images/gauche_on.gif\" border=0></a>";
         echo "<a href=\"trace.php?link=$lien\"><IMG SRC=\"images/gauche_on.gif\" border=0></a>&nbsp;&nbsp;&nbsp;&nbsp;";
       }
       if ($page == 1){
         echo "<IMG SRC=\"images/gauche_off.gif\" border=0></a>&nbsp;&nbsp;&nbsp;&nbsp;";
       }else{
         $page_avant = $page-1;
         $lien = "ressource_admin.php?flg=$flg&favoris=$favoris&parcours=$parcours&id_parc=$id_parc&doublon=$doublon&rep=$rep&lien_sous_cat=1&parente=$par1[$pr]&sous_catalogue=$sous_c[$pr]&cat=$cat&sous_cat=$sous_c[$pr]&niveau=1&id_ref=$id_ref&id_seq=$id_seq&id_act=$id_act&modif_act=$modif_act&creat=$creat&page=$page_avant&total_aff=$total_aff";
         $lien = urlencode($lien);
         echo "<a href=\"trace.php?link=$lien\"><IMG SRC=\"images/gauche_on.gif\" border=0></a>&nbsp;&nbsp;&nbsp;&nbsp;";
       }
       for ($affichage = 1;$affichage <= $nb_pages;$affichage++){
         if ($page == $affichage)
           echo "<font color=marroon><B> $affichage</B></font>";
         else {
           $lien = "ressource_admin.php?flg=$flg&favoris=$favoris&parcours=$parcours&id_parc=$id_parc&doublon=$doublon&rep=$rep&lien_sous_cat=1&parente=$par1[$pr]&sous_catalogue=$sous_c[$pr]&cat=$cat&sous_cat=$sous_c[$pr]&niveau=1&id_ref=$id_ref&id_seq=$id_seq&id_act=$id_act&modif_act=$modif_act&creat=$creat&page=$affichage&total_aff=$total_aff";
           $lien = urlencode($lien);
           echo "<a href=\"trace.php?link=$lien\"> $affichage</a>";
         }
         echo "&nbsp;&nbsp;&nbsp;&nbsp;";
       }
       if ($page == $nb_pages){
         echo "<IMG SRC=\"images/droite_off.gif\" border=0></a>&nbsp;&nbsp;&nbsp;&nbsp;";
       }else{
         $page_suivant = $page+1;
         $lien = "ressource_admin.php?flg=$flg&favoris=$favoris&parcours=$parcours&id_parc=$id_parc&doublon=$doublon&rep=$rep&lien_sous_cat=1&parente=$par1[$pr]&sous_catalogue=$sous_c[$pr]&cat=$cat&sous_cat=$sous_c[$pr]&niveau=1&id_ref=$id_ref&id_seq=$id_seq&id_act=$id_act&modif_act=$modif_act&creat=$creat&page=$page_suivant&total_aff=$total_aff";
         $lien = urlencode($lien);
         echo "<a href=\"trace.php?link=$lien\"><IMG SRC=\"images/droite_on.gif\" border=0></a>&nbsp;&nbsp;&nbsp;&nbsp;";
       }
       if ($page == $nb_pages){
         echo "<IMG SRC=\"images/droite_off.gif\" border=0></a>";
         echo "<IMG SRC=\"images/droite_off.gif\" border=0></a>&nbsp;&nbsp;&nbsp;&nbsp;";
       }else{
         $lien = "ressource_admin.php?flg=$flg&favoris=$favoris&parcours=$parcours&id_parc=$id_parc&doublon=$doublon&rep=$rep&lien_sous_cat=1&parente=$par1[$pr]&sous_catalogue=$sous_c[$pr]&cat=$cat&sous_cat=$sous_c[$pr]&niveau=1&id_ref=$id_ref&id_seq=$id_seq&id_act=$id_act&modif_act=$modif_act&creat=$creat&page=$nb_pages&total_aff=$total_aff";
         $lien = urlencode($lien);
         echo "<a href=\"trace.php?link=$lien\"><IMG SRC=\"images/droite_on.gif\" border=0></a>";
         echo "<a href=\"trace.php?link=$lien\"><IMG SRC=\"images/droite_on.gif\" border=0></a>&nbsp;&nbsp;&nbsp;&nbsp;";
       }
       echo"</TD></TR>";
    }
       echo "</TABLE>";
       $lien_sous_cat=0;
  echo "</TD></TR></TABLE></TD></TR></TABLE>";
}
// formulaire d'ajout d'un enregistrement soit à partir du moteur, soit provenant du bureau d'un formateur autorisé
if (isset($ajouter) && $ajouter==1 && !isset($inserer))
{
  ?>
  <SCRIPT language=JavaScript>
    function checkForm(frm) {
      var ErrMsg = "<?php echo $mess_info_no;?>\n";
      var lenInit = ErrMsg.length;
      if (isEmpty(frm.new_cat)==true)
        ErrMsg += ' - <?php echo $mrc_cat;?>\n';
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
  echo "<TABLE bgColor='#FFFFFF' cellspacing='1' cellpadding='6' width=100%>";
  echo "<FORM NAME='form1' ACTION=\"ressource_admin.php?ajout_cat=1&admin=1&parente=$parente&flag=$flag&sous_catalogue=$cat&lien_sous_cat=1&cat=$cat\" METHOD='POST' target='main'>";
  echo "<INPUT TYPE='HIDDEN' NAME='favoris' VALUE=\"$favoris\">";
  echo "<INPUT TYPE='HIDDEN' NAME='flg' VALUE='$flg'>";
  echo "<INPUT TYPE='HIDDEN' NAME='parente' VALUE='$parente'>";
  echo "<INPUT TYPE='HIDDEN' NAME='sous_catalogue' VALUE='$cat'>";
  echo "<TR><TD colspan='2' bgcolor='white'>$ajout_ajout</TD></TR>";
  echo "<TR height='40'><TD colspan='2'><span class='sous_titre'>$mrd_form_aj_cat</span></TD></TR>";
  echo "<TR height='40'><TD valign='top' colspan='2'><B>$mrd_tit_cat<BR>$mess_let_reserv :<B> & </B>$et<B> +</B></TD></TR>";
  echo "<TR><TD valign='top' colspan='2'><INPUT TYPE='text' class='INPUT' NAME='new_cat' SIZE=60></TD></TR>";
  echo "<TR><TD align='left'><A HREF=\"javascript:history.back();\" onmouseover=\"img_annonce.src='images/fiche_identite/boutretourb.gif';return true;\" onmouseout=\"img_annonce.src='images/fiche_identite/boutretour.gif'\">";
  echo "<IMG NAME=\"img_annonce\" SRC=\"images/fiche_identite/boutretour.gif\" BORDER='0' onLoad=\"tempImg=new Image(0,0); tempImg.src='images/fiche_identite/boutretourb.gif'\"></A></td>";
  echo "<TD align=left><A HREF=\"javascript:checkForm(document.form1);\" onmouseover=\"img1.src='images/fiche_identite/boutvalidb.gif';return true;\" onmouseout=\"img1.src='images/fiche_identite/boutvalid.gif'\">".
       "<IMG NAME=\"img1\" SRC=\"images/fiche_identite/boutvalid.gif\" BORDER='0' onLoad=\"tempImg=new Image(0,0); tempImg.src='images/fiche_identite/boutvalidb.gif'\"></A></TD></TR>";
  echo "</TD></TR></FORM></TABLE></TD></TR></TABLE>";
  exit;
}

// Procédure de suppression d'une ressource
if (isset($supprimer) && $supprimer==1 && !isset($suppression))
{
  $categSupp = GetDataField ($connect,"select ress_cat_lb from ressource_new where ress_cdn = $num","ress_cat_lb");
  $sql_ress=mysql_query("SELECT * FROM activite WHERE act_ress_no = '$num'");
  $nbr_ress=mysql_num_rows($sql_ress);
  if ($nbr_ress != 0)
  {
     echo $mrc_nosup;
     echo "<TABLE width=100% bgcolor='#FFFFFF'><TR><TD><A HREF=\"javascript:history.back();\" onmouseover=\"img_annonce.src='images/fiche_identite/boutretourb.gif';return true;\" onmouseout=\"img_annonce.src='images/fiche_identite/boutretour.gif'\">";
     echo "<IMG NAME=\"img_annonce\" SRC=\"images/fiche_identite/boutretour.gif\" BORDER='0' onLoad=\"tempImg=new Image(0,0); tempImg.src='images/fiche_identite/boutretourb.gif'\"></A>";
     echo "</TD></TR></TABLE></TD></TR></TABLE></TD></TR></TABLE>";
     exit;
  }
  elseif ($admin == 1)
     echo "$mrd_sup_cat_conf : \"$categSupp\"</b><BR>";
  else
     echo "$mrc_conf_sup : \"<b>$categSupp</b>\"</b><BR>";
  echo "<FORM name='form1' ACTION=\"ressource_admin.php\" METHOD='POST' target='main'>";
  echo "<INPUT TYPE='HIDDEN' NAME='supprimer' VALUE=1>";
  echo "<INPUT TYPE='HIDDEN' NAME='suppression' VALUE=1>";
  echo "<INPUT TYPE='HIDDEN' NAME='flg' VALUE='$flg'>";
  echo "<INPUT TYPE='HIDDEN' NAME='parente' VALUE='$parente'>";
  echo "<INPUT TYPE='HIDDEN' NAME='lien_sous_cat=1' VALUE='$lien_sous_cat'>";
  echo "<INPUT TYPE='HIDDEN' NAME='num' VALUE='$num'>";
  echo "<TABLE width=100% bgcolor='#FFFFFF'><TR><TD><A HREF=\"javascript:history.back();\" onmouseover=\"img_annonce.src='images/fiche_identite/boutretourb.gif';return true;\" onmouseout=\"img_annonce.src='images/fiche_identite/boutretour.gif'\">";
  echo "<IMG NAME=\"img_annonce\" SRC=\"images/fiche_identite/boutretour.gif\" BORDER='0' onLoad=\"tempImg=new Image(0,0); tempImg.src='images/fiche_identite/boutretourb.gif'\"></A>";
  echo "</TD><TD align='left'><A HREF=\"javascript:document.form1.submit();\" onmouseover=\"img1.src='images/fiche_identite/boutvalidb.gif';return true;\" onmouseout=\"img1.src='images/fiche_identite/boutvalid.gif'\">".
       "<IMG NAME=\"img1\" SRC=\"images/fiche_identite/boutvalid.gif\" BORDER='0' onLoad=\"tempImg=new Image(0,0); tempImg.src='images/fiche_identite/boutvalidb.gif'\"></A>";
  echo "</TD></TR></TABLE></FORM>";
  echo "</TD></TR></TABLE></TD></TR></TABLE>";
}
if (isset($modifier) && $modifier == 1 && !isset($modification))
{
  ?>
  <SCRIPT language=JavaScript>
    function checkForm(frm) {
      var ErrMsg = "<?php echo $mess_info_no;?>\n";
      var lenInit = ErrMsg.length;
      if (isEmpty(frm.new_cat)==true)
        ErrMsg += ' - <?php echo $mrc_cat;?>\n';
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
  echo "<tr><td colspan=2 height='45' valign='middle'><span class='sous_titre'>$mrd_form_aj_cat</span></td></tr>";
  echo "<FORM NAME='form1' ACTION=\"ressource_admin.php?modifier=1&modification=1&admin=1&cat=$cat&num=$num\" METHOD='POST' target='main' ENCTYPE='multipart/form-data'>";
  echo "<INPUT TYPE='HIDDEN' NAME='favoris' VALUE=\"$favoris\">";
  echo "<INPUT TYPE='HIDDEN' NAME='flg' VALUE='$flg'>";
  echo "<INPUT TYPE='HIDDEN' NAME='lien_sous_cat=1' VALUE='$lien_sous_cat'>";
  echo "<INPUT TYPE='HIDDEN' NAME='parente' VALUE='$parente'>";
  echo "<TR><TD><strong>$mrd_new_tit_cat<BR>$mess_let_reserv : & $et +</strong></TD>";
  echo "<TD><INPUT TYPE='text' class='INPUT' NAME='new_cat' value=\"".stripslashes($cat)."\" SIZE='60'></TD></TR>";
  echo "<TR height='45'><TD valign='middle'><A HREF=\"javascript:history.back();\" onmouseover=\"img_annonce.src='images/fiche_identite/boutretourb.gif';return true;\" onmouseout=\"img_annonce.src='images/fiche_identite/boutretour.gif'\">";
  echo "<IMG NAME=\"img_annonce\" SRC=\"images/fiche_identite/boutretour.gif\" BORDER='0' onLoad=\"tempImg=new Image(0,0); tempImg.src='images/fiche_identite/boutretourb.gif'\"></A></TD>";
  echo "<TD align='left' valign='middle'><A HREF=\"javascript:checkForm(document.form1);\" onmouseover=\"img1.src='images/fiche_identite/boutvalidb.gif';return true;\" onmouseout=\"img1.src='images/fiche_identite/boutvalid.gif'\">".
       "<IMG NAME=\"img1\" SRC=\"images/fiche_identite/boutvalid.gif\" BORDER='0' onLoad=\"tempImg=new Image(0,0); tempImg.src='images/fiche_identite/boutvalidb.gif'\"></A></TD></TR>";
  echo "</FORM>";
  echo "<tr><td height='45'>&nbsp;</td></tr></TABLE>";
  echo "</TD></TR></TABLE></TD></TR></TABLE>";
  exit;
}
?>
</BODY></HTML>
