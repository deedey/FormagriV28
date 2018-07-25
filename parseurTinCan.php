<?php
if (!isset($_SESSION)) session_start();
include ("include/UrlParam2PhpVar.inc.php");
if (!isset($_SESSION['id_user']) || $_SESSION['id_user'] == "")
{
  exit();
}
require 'fonction.inc.php';
require 'graphique/admin.inc.php';
require "lang$lg.inc.php";
require 'class/class_module.php';
require 'class/Class_Rss.php';
require 'fonction_html.inc.php';
require 'xml2json/xml2array.php';
//require 'xml2json/xml2json.php';
dbConnect();
//echo "<pre>";print_r($_POST);echo "</pre>";exit;
$date_dujour = date ("Y-m-d");
include ('style.inc.php');
if (isset($prov) && $prov == "seq")
  $incl = "liste_seq.inc.php";
elseif(isset($prov) && $prov == "act")
  $incl = "liste_act.inc.php";
elseif(isset($prov) && $prov == "parc")
  $incl = "liste_parc.inc.php";
$leManifeste = array();
$accord = 0;
$request = $_SERVER['QUERY_STRING'];
ini_set('error_reporting','E_ALL');

if (isset($_GET['file']) && $_GET['file'] != '' )
{
    if (strstr($_SERVER['HTTP_REFERER'],'modif_rep_fic.php'))
        $request = 'activite_free.php?lesseq=0&medor=1&ordre_affiche=lenom&titre_act=Activités libres';
    echo "<CENTER><TABLE bgColor='#298CA0' cellspacing='2' cellpadding='0'><TR>";
    echo "<TD valign='top' width='70%' height='100%' bgcolor='#CEE6EC'>";
    echo "<TABLE cellspacing='1' cellpadding='0' width='100%'>";
    echo "<TR><TD background=\"images/fond_titre_table.jpg\" colspan='2' height='40' align='center' valign='center'>";
    echo "<Font size='3' color='#FFFFFF'><B>Parseur Tincan</B></FONT></TD></TR>";
    $fichier = $_GET['file'];
    $NomFichier = str_replace('/','',str_replace(dirname($_GET['file']),'',$_GET['file']));
    $xmlStringContents = html_entity_decode(file_get_contents($fichier),ENT_QUOTES,'iso-8859-1');
    $xmlStringContents = trim($xmlStringContents);
    //echo htmlentities($xmlStringContents,ENT_QUOTES,'iso-8859-1');
    $TincanContents = xml2array($xmlStringContents);
    $nbreActs = (isset($TincanContents['tincan']['activities']['activity'][0])) ? count($TincanContents['tincan']['activities']['activity']) : 1;
    $racine = $TincanContents['tincan']['activities']['activity'];
    if($nbreActs > 1 && strtolower($racine[0]['attr']['type']) == 'course')
    {
     $activityId = $racine[0]['attr']['id'];
     $activityName= $racine[0]['name']['value'];
     if (isset($racine[0]['description']['value']))
        $activityDescription= $racine[0]['description']['value'];
     else
        $activityDescription= "Pas de description";
     $activityLaunch= $adresse_http.'/'.dirname($_GET['file']).'/'.$racine[0]['launch']['value'];
    }
    elseif ($nbreActs == 1)
    {
       $activityId = $racine['attr']['id'];
       $activityName= $racine['name']['value'];
       if (isset($racine['description']['value']))
          $activityDescription= $racine['description']['value'];
       else
          $activityDescription= "Pas de description";
     $activityLaunch= $adresse_http.'/'.dirname($_GET['file']).'/'.$racine['launch']['value'];
    }
    else
    {
        echo "Le fichier ".$fichier." n'est pas conforme";
    
    }
    //print '<pre>';print_r($TincanContents);echo "</pre>lien = $activityLaunch .' et '.$activityId".htmlentities(modif_az_qw($activityId),ENT_QUOTES,'iso-8859-1')."</TD></TR>";exit;
    $id_act = Donne_ID ($connect, "select max(act_cdn) from activite");
    $duree = 15;
    $id_seq = 0;
    $num_act = 1;
    $nb_requete= mysql_num_rows(mysql_query("SELECT * FROM ressource_new where ress_cat_lb = \"xApi TinCan\""));
    if ($nb_requete == 0)
    {
       $id_new_parent = Donne_ID ($connect, "select max(ress_cdn) from ressource_new");
       $sql_insere= mysql_query("INSERT INTO ressource_new (ress_cdn,ress_cat_lb,ress_typress_no,ress_create_dt,ress_ajout) ".
                    "VALUES ('$id_new_parent',\"xApi TinCan\",'0',\"$date_dujour\",'foad')");
       $parente = $id_new_parent;
    }
    else
       $parente = GetDataField($connect,"SELECT ress_cdn FROM ressource_new WHERE ress_cat_lb = \"xApi TinCan\"".
                                        " AND ress_typress_no = 0 AND ress_titre =''","ress_cdn");
    $id_new_ress = Donne_ID ($connect, "select max(ress_cdn) from ressource_new");
    $sql_insere= mysql_query("INSERT INTO ressource_new (ress_cdn,ress_cat_lb,ress_typress_no,ress_url_lb,".
                 "ress_auteurs_cmt,ress_publique_on,ress_titre,ress_desc_cmt,ress_create_dt,ress_ajout,".
                 "ress_public_no,ress_type,ress_support,ress_doublon,ress_niveau) VALUES ".
                 "('$id_new_ress',\"xApi TinCan\",'$parente',\"".$activityLaunch."\",\"xApi\",'NON',\"".
                 htmlentities(modif_az_qw($activityName),ENT_QUOTES,'iso-8859-1')."\",\"".
                 htmlentities(modif_az_qw($activityDescription),ENT_QUOTES,'iso-8859-1').
                 "\",\"$date_dujour\",\"$login\",'TOUT',\"XAPI\",".
                 "\"Url\",'1','1')");

    $insert_act_query = mysql_query ("insert into activite values ($id_act,$id_seq,$num_act,\"".
                      str_replace("\"","'",$activityName)."\",\"".
                      htmlentities(modif_az_qw($activityDescription),ENT_QUOTES,'iso-8859-1')."\",\"".
                      htmlentities(modif_az_qw($activityId),ENT_QUOTES,'iso-8859-1')."\",'OUI',$id_new_ress,".
                      "\"$duree\",\"OUI\",\"RESSOURCE\",\"OUI\",\"NON\",".
                      "$id_user,\"$date_dujour\",\"$date_dujour\",'OUI',1)");
    //$insert_rss = rss :: ajout('activite',$id_user,$id_act);
    $id_actdev = Donne_ID ($connect, "select max(actdev_cdn) from activite_devoir");
    $changer_dev = mysql_query ("insert into activite_devoir values($id_actdev,$id_act,\"xApi TinCan\")");
    echo '<TR><TD> Vous retrouverez ces ou cette activité(s) parmi vos activités LIBRES.<br />'.
         $activityId.'<br />'.modif_az_qw($activityName).'<br />'.modif_az_qw($activityDescription).'<br />'.$activityLaunch.'<br />';
    echo fin_tableau($html);
   $lien = "activite_free.php?lesseq=0&medor=1&ordre_affiche=lenom&titre_act=Activités libres";
   $lien = urlencode($lien);
   echo "<script language=\"JavaScript\">";
       echo "setTimeout(function() {document.location.replace('trace.php?link=$lien');},5000)";
   echo "</script>";
  exit;
}


if (isset($_FILES['file']['tmp_name']) && strstr(strtolower($_FILES['file']['name']),".zip") && $zip == 1)
{
    require('class/pclzip.inc.php');
    if ($_FILES['file']['name'] == "")
       $message = strtolower($mess_fichier_no);
    elseif(!is_file($_FILES['file']['tmp_name']))
       $message = $mess_fic_dep_lim." : ".ini_get('upload_max_filesize');
    if ($message != "")
    {
      echo "<CENTER><TABLE bgColor='#298CA0' cellspacing='2' cellpadding='0'><TR>";
      include ($incl);
      echo "<TD valign='top' width='70%' height='100%' bgcolor='#CEE6EC'>";
      echo "<TABLE cellspacing='1' cellpadding='0' width='100%'>";
      echo "<TR><TD background=\"images/fond_titre_table.jpg\" colspan='2' height='40' align='center' valign='center'>";
      echo "<Font size='3' color='#FFFFFF'><B>$mess_imp_sco</B></FONT></TD></TR>";
      echo "<TR height='50'><TD colspan='2' valign='center'><FONT size='2' color='red'><B>$message</B></font></TD><TR>";
      echo fin_tableau($html);
     exit;
    }
    $dir="ressources/".$login."_".$id_user."/ressources/";
    
    $nom = $_FILES['file']['name'];
    $nom_final= "Ressources_TC";
    $handle=opendir($dir);
    $i = 0;
    while ($fiche = readdir($handle))
    {
       if ($fiche == $nom_final)
         $i++;
    }
    if ($i == 0)
    {
       $create_rep = $dir."Ressources_TC";
       mkdir($create_rep,0775);
       chmod($create_rep,0775);
    }
    list($extension, $nom_rep) = getextension($_FILES['file']['name']);
    $dir = "ressources/".$login."_".$id_user."/ressources/Ressources_TC/";
    $fichier = $_FILES['file']['tmp_name'];
    $archive = new PclZip($fichier);
    if (($list = $archive->listContent()) == 0) 
    {
      die("Error : ".$archive->errorInfo(true));
    }
    $affiche_sco.= "<B>$mess_sco_imp : Ressources_TC/$nom_rep</B><P>";
    $accord=0;
    for ($i=0; $i<sizeof($list); $i++)
    {
        $affiche_sco.= "Fichier ".($i+1)." = ".$nom_rep."/".$list[$i]["filename"]."<BR>";
        if (strstr($list[$i]["filename"],"tincan.xml"))
        {
           $leManifeste[$accord] = $dir.$nom_rep."/".$list[$i]["filename"];
           $accord++;
        }
    }
    $affiche_sco.= "<P>";
    $dest_file = $dir.$nom;
    if ($accord == 0)
       $message_no .= "Le fichier de description de contenu \"TINCAN.XML\" n'existe pas dans le package Zip fourni.".
                      "Veuillez le vérifier hors plate-forme.<BR />";
    if (file_exists($dir.$nom))
       $message_no .= $mess_zip_exist;
    if (!file_exists($dir.$nom) && $accord > 0)
    {
      if (!file_exists($dir.$nom_rep))
      {
         mkdir($dir.$nom_rep,0775);
         chmod ($dir.$nom_rep,0775);
      }
      else
      {
         $nom_rep .="_$date_dujour";
         mkdir($dir.$nom_rep,0775);
         chmod ($dir.$nom_rep,0775);
      }
      $lerepertoire = $dir."$nom_rep/";
      $list = $archive->extract(PCLZIP_OPT_PATH,$lerepertoire,
                               PCLZIP_OPT_REMOVE_PATH,$dir,
                               PCLZIP_OPT_SET_CHMOD, 0775);
       $copier = move_uploaded_file($_FILES['file']['tmp_name'], $dest_file);
    }
    else
    {
      entete_concept($incl,"Importer une séquence au standard TinCanApi (xApi)");
      echo "<TR height='50'><TD colspan='2' valign='center'><B>$message_no</B></TD></TR>";
      echo "<TR height='20'><TD colspan='2' valign='center'>&nbsp</TD></TR>";
      $file = "";
      echo "<TR><TD align=left><A HREF=\"javascript:history.go(-1);\" onmouseover=\"img_annonce.src='images/fiche_identite/boutretourb.gif';return true;\" onmouseout=\"img_annonce.src='images/fiche_identite/boutretour.gif'\">";
      echo "<IMG NAME=\"img_annonce\" SRC=\"images/fiche_identite/boutretour.gif\" BORDER='0' onLoad=\"tempImg=new Image(0,0); tempImg.src='images/fiche_identite/boutretourb.gif'\"></A>";
      echo fin_tableau($html);
     exit;
    }
}
elseif((!isset($_FILES['file']['tmp_name']) || !strstr(strtolower($_FILES['file']['name']),".zip")) && isset($zip) && $zip == 1)
{
    include ('style.inc.php');
    $poids = "4,5 Mo";
    if ($_FILES['file']['name'] == "")
       $message = $mess_fichier_no." <BR> &nbsp;&nbsp;et<BR> &nbsp;&nbsp;".strtolower($mess_fic_dep_lim)." $poids<BR>";
    elseif ($_FILES['file']['name'] != "")
       $message = "&nbsp;&nbsp;$mess_nozip";
    if ($message != "")
    {
      entete_concept($incl,$mess_imp_sco);
      echo "<TR height='50'><TD colspan='2' valign='center'><FONT size='2' color='red'><B>&nbsp;&nbsp;$message</B></font><BR></TD></TR>";
      echo "<TR height='20'><TD colspan='2' valign='center'>&nbsp</TD></TR>";
      echo "<TR><TD align=left><A HREF=\"javascript:history.go(-1);\" onmouseover=\"img_annonce.src='images/fiche_identite/boutretourb.gif';return true;\" onmouseout=\"img_annonce.src='images/fiche_identite/boutretour.gif'\">";
      echo "<IMG NAME=\"img_annonce\" SRC=\"images/fiche_identite/boutretour.gif\" BORDER='0' onLoad=\"tempImg=new Image(0,0); tempImg.src='images/fiche_identite/boutretourb.gif'\"></A>";
      echo fin_tableau($html);
     exit;
    }
}
if (((!isset($_FILES['file']['tmp_name']) && !isset($zip) && !isset($_POST['file'])) || (isset($_POST['file']) && !strstr($_POST['file'],"tincan.xml") && !isset($zip))) && !isset($_GET['file'])){
    include ('style.inc.php');
    entete_concept($incl,"Importer une séquence au standard TinCanApi (xApi)");
    echo "<TR height='10'><TD colspan='2' valign='top'>&nbsp;</TD><TR>";
    if (isset($_POST['file']) && !strstr($_POST['file'],"tincan.xml"))
        echo "<TR height='30'><TD colspan='2' valign='top'><FONT color='red'><B>$mess_nofile_xml</B></TD><TR>";
    echo "<TR height='10'><TD colspan='2' valign='top'>&nbsp;</TD><TR>";
    echo "<TR><TD colspan='2' width='100%'>";
    echo "<TABLE cellspacing='0' cellpadding='4' width='100%' border=0>";
    echo "<TR><TD colspan='2' valign='top' align='left'><TABLE width='70%' border=0><TR><TD valign='center'><B>Télécharger une archive(zip) contenant une activité au format TinCan</B></TD>";
    echo "<TD valign='center'><A HREF=\"javascript:void(0);\" ".
           "onclick=\"return overlib('<TABLE><TR><TD width=5></TD><TD>".addslashes($mess_xApi_zip)."</TD></TR></TABLE>'".
           ",STICKY,ol_hpos,RIGHT,ABOVE,WIDTH,350,CAPTION,'<TABLE width=100% border=0 cellspacing=2><TR height=20 width=100%>".
           "<TD align=left width=90% nowrap><B>$mess_nota_bene</B></TD></TR></TABLE>')\"".
           " onMouseOut=\"return nd();\"><IMG SRC='images/modules/anoter.gif' border='0'></A>";
    echo "</TD></TR>";
    echo "<TR height='5'><TD colspan='2' valign='top'></TD><TR>";
    echo "<TR height='30'><TD width='70%'valign='top'>";
    echo "<FORM  action='parseurTinCan.php?zip=1&$request' name ='form2' method='POST' enctype='multipart/form-data' target='main'>";
    echo "<INPUT TYPE='file' name='file' size='53' enctype='multipart/form-data'>";
    echo "</TD><TD align='left' nowrap valign='top' width='30%'><A href=\"javascript:document.form2.submit();\" onmouseover=\"img2.src='images/fiche_identite/boutvalidb.gif';return true;\" onmouseout=\"img2.src='images/fiche_identite/boutvalid.gif'\">";
    echo "<IMG NAME=\"img2\" SRC=\"images/fiche_identite/boutvalid.gif\" BORDER='0' onLoad=\"tempImg=new Image(0,0); tempImg.src='images/fiche_identite/boutvalidb.gif'\">";
    echo "</FORM></TABLE></TD></TR></TD></TR></TABLE></TD></TR>";
    echo "<TR height='5'><TD colspan='2' valign='top'></TD><TR></TABLE>";
    echo fin_tableau($html);
  exit;
}
else
{
    $NbrManifest = count($leManifeste);
    //echo $NbrManifest.'<br />';
    for ($iManif = 0;$iManif < $NbrManifest;$iManif++)
    {
       $NomFichier = str_replace('/','',str_replace(dirname($leManifeste[$iManif]),'',$leManifeste[$iManif]));
       $directoire = dirname($leManifeste[$iManif]);
       $xmlStringContents = file_get_contents($leManifeste[$iManif]);
       //echo $leManifeste[$iManif].'--'.$xmlStringContents;
       $TincanContents = xml2array($xmlStringContents);
       //print '<pre>';print_r($TincanContents);echo '</pre></TD></TR>';exit;
       $nbreActs = (isset($TincanContents['tincan']['activities']['activity'][0])) ? count($TincanContents['tincan']['activities']['activity']) : 1;
       $racine = $TincanContents['tincan']['activities']['activity'];
       if($nbreActs > 1 && (strtolower($racine[0]['attr']['type']) == 'course' || strstr(strtolower($racine[0]['attr']['type']),'course')))
       {
           $activityId = $racine[0]['attr']['id'];
           $activityName= $racine[0]['name']['value'];
           if (isset($racine[0]['description']['value']))
               $activityDescription= $racine[0]['description']['value'];
           else
               $activityDescription= "Pas de description";
           $activityLaunch= $adresse_http.'/'.$directoire.'/'.$racine[0]['launch']['value'];
       }
       elseif ($nbreActs == 1)
       {
            $activityId = $racine['attr']['id'];
            $activityName= $racine['name']['value'];
            if (isset($racine['description']['value']))
                 $activityDescription= $racine['description']['value'];
            else
                $activityDescription= "Pas de description";
            $activityLaunch= $adresse_http.'/'.$directoire.'/'.$racine['launch']['value'];
       }
       else
       {
          echo "Le fichier ".$fichier." n'est pas conforme";
          exit;
       }
       $Entete = '<TR><TD>'. $activityId.'<br />'.modif_az_qw($activityName).'<br />'.modif_az_qw($activityDescription).'<br />'.$activityLaunch.'<br />';
       if (!isset($activityName))
          exit;
       $id_act = Donne_ID ($connect, "select max(act_cdn) from activite");
       $duree = 15;
       if (isset($id_seq) && $id_seq > 0)
           $num_act = Donne_ID ($connect,"select max(act_ordre_nb) from activite where act_seq_no = $id_seq");
       else
       {
           $id_seq = 0;
           $num_act = 1;
       }
       if ($id_seq > 0)
       {
         $duree_seq = GetDataField ($connect,"select seq_duree_nb from sequence where seq_cdn=$id_seq","seq_duree_nb");
         $duree_seq+=$duree;
         $upd_seq = mysql_query ("UPDATE sequence set seq_duree_nb = $duree_seq, seq_modif_dt = \"$date_dujour\" where seq_cdn = $id_seq");
         $modifie_rss = rss :: modifie('sequence',$id_user,$id_seq);
       }
       $nb_requete= mysql_num_rows(mysql_query("SELECT * FROM ressource_new where ress_cat_lb = \"xApi TinCan\""));
       if ($nb_requete == 0)
       {
          $id_new_parent = Donne_ID ($connect, "select max(ress_cdn) from ressource_new");
          $sql_insere= mysql_query("INSERT INTO ressource_new (ress_cdn,ress_cat_lb,ress_typress_no,ress_create_dt,ress_ajout) ".
                    "VALUES ('$id_new_parent',\"xApi TinCan\",'0',\"$date_dujour\",'foad')");
          $parente = $id_new_parent;
       }
       else
          $parente = GetDataField($connect,"SELECT ress_cdn FROM ressource_new WHERE ress_cat_lb = \"xApi TinCan\"".
                                        " AND ress_typress_no = 0 AND ress_titre =''","ress_cdn");
       $id_new_ress = Donne_ID ($connect, "select max(ress_cdn) from ressource_new");
       $sql_insere= mysql_query("INSERT INTO ressource_new (ress_cdn,ress_cat_lb,ress_typress_no,ress_url_lb,".
                 "ress_auteurs_cmt,ress_publique_on,ress_titre,ress_desc_cmt,ress_create_dt,ress_ajout,".
                 "ress_public_no,ress_type,ress_support,ress_doublon,ress_niveau) VALUES ".
                 "('$id_new_ress',\"xApi TinCan\",'$parente',\"".$activityLaunch."\",\"xApi\",'NON',\"".
                 htmlentities(modif_az_qw($activityName),ENT_QUOTES,'iso-8859-1')."\",\"".
                 htmlentities(modif_az_qw($activityDescription),ENT_QUOTES,'iso-8859-1').
                 "\",\"$date_dujour\",\"$login\",'TOUT',\"XAPI\",".
                 "\"Url\",'1','1')");

       $insert_act_query = mysql_query ("insert into activite values ($id_act,$id_seq,$num_act,\"".
                      str_replace("\"","'",$activityName)."\",\"".
                      htmlentities(modif_az_qw($activityDescription),ENT_QUOTES,'iso-8859-1')."\",\"".
                      htmlentities(modif_az_qw($activityId),ENT_QUOTES,'iso-8859-1')."\",'OUI',$id_new_ress,".
                      "\"$duree\",\"OUI\",\"RESSOURCE\",\"OUI\",\"NON\",".
                      "$id_user,\"$date_dujour\",\"$date_dujour\",'OUI',1)");
       //$insert_rss = rss :: ajout('activite',$id_user,$id_act);
       $id_actdev = Donne_ID ($connect, "select max(actdev_cdn) from activite_devoir");
       $changer_dev = mysql_query ("insert into activite_devoir values($id_actdev,$id_act,\"xApi TinCan\")");
       if (isset($id_seq) && $id_seq > 0)
       {
          InsertTinCanAct($id_seq,$id_act);
       }

    }
}
// début des fonctions
/*
*/
include ('style.inc.php');
entete_concept($incl,$mess_imp_sco);
if ($message != '')
   echo "<TR height='50'><TD valign= 'center' colspan='2' width='100%'><Font size='3'><B>$message</B></FONT></TD></TR>";
echo "<TR><TD colspan='2' width='100%' bgcolor='#CEE6EC'>";
echo $Entete.'<p>'.$affiche_sco;

echo fin_tableau($html);
echo "</BODY></HTML>";

// Fonctions
function getextension($file)
{
  $bouts = explode(".", $file);
  return array(array_pop($bouts), implode(".", $bouts));
}
function InsertTinCanAct($id_seq,$id_act)
{
    GLOBAL $connect,$lg;
    $requete_grp = mysql_query ("select * from groupe");
    $nb_grp_parc = mysql_num_rows($requete_grp);
    if ($nb_grp_parc > 0)
    {
        $gp=0;
        while ($gp < $nb_grp_parc)
        {
            $id_grp = mysql_result($requete_grp,$gp,"grp_cdn");
            $nom_grp = mysql_result($requete_grp,$gp,"grp_nom_lb");
            $act_suivi = mysql_query ("select * from suivi2_$id_grp where suiv2_seq_no = $id_seq");
            $nb_ut = mysql_num_rows ($act_suivi);
            if ($nb_ut >0)
            {
                $kk = 0;
                while ($kk != $nb_ut)
                {
                     $is = mysql_result ($act_suivi,$kk,"suiv2_cdn");
                     $ut = mysql_result ($act_suivi,$kk,"suiv2_utilisateur_no");
                     $grpe =$id_grp;
                     $verif_seq = mysql_result($act_suivi,$kk,"suiv2_etat_lb");
                     if ($verif_seq != "TERMINE")
                     {
                         $id_suivi = Donne_ID ($connect,"select max(suivi_cdn) from suivi1_$id_grp");
                         $ins_suivi = mysql_query ("insert into suivi1_$id_grp(suivi_cdn,suivi_utilisateur_no,suivi_act_no,suivi_etat_lb,suivi_grp_no) ".
                                                   "values ($id_suivi,$ut,$id_act,\"A FAIRE\",$grpe)");
                     }
                     $kk++;
                } 
            }
            $gp++;
        }
    }
}

?>