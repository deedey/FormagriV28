<?php
session_start();
if (!isset($_SESSION['IDUSER']) || $_SESSION['IDUSER'] == "")
{
  exit();
}
require ("../admin.inc.php");
require ("../fonction.inc.php");
require ("../fonction_html.inc.php");
require ("image_resize_class.php");
dbConnect();
include ("../include/varGlobals.inc.php");
$Response=array();
$result=array();
$aujourdhui = date("d/m/Y H:i:s" ,time());
$log_der = mysql_query("SELECT * from log WHERE login='".$_SESSION['login']."' order by log_cdn desc limit 1");
$date_der = mysql_result($log_der,0,'date_debut');
$heure_der = mysql_result($log_der,0,'heure_debut');
$dateComp =  $date_der." ".$heure_der;
// Récupération du nombre de postts dans les forums
$majuscule = $_SESSION['prenom']." ".$_SESSION['nom'];
$nomGrp = "groupe".$_GET['id_grp'];
$nom_grpBodies = $nomGrp."_bodies";
$req_mess_for = mysql_query ("select * from $nomGrp,$nom_grpBodies where datestamp > '$dateComp' and ".
                "author != '$majuscule' and approved='Y' and $nomGrp.id = $nom_grpBodies.id");
$nbr_mess_for = mysql_num_rows($req_mess_for);
      $content .= '<div style="clear:both;margin-bottom:2px;font-size:12px;color:#000;cursor:pointer;" >'.
                  '<a class="ui-btn ui-shadow ui-btn-corner-all ui-mini ui-btn-inline ui-btn-icon-left '.
                  'ui-btn-up-b" data-wrapperels="span" data-iconshadow="true" data-shadow="true" '.
                  'data-corners="true" href="#" data-role="button" data-mini="true" data-inline="true" '.
                  'data-theme="b" onClick="$(\'#commencer\').load(\'formateur/acces_forum.inc.php\');" >'.
                  '<span class="ui-btn-inner"><span class="ui-btn-text">Revenir à la liste des forums</span>'.
                  '</span></a></div>';
if ($nbr_mess_for > 0)
{
       while ($row = mysql_fetch_object($req_mess_for))
       {
            $result[] = $row;
       }
       for ($i=0;$i<$nbr_mess_for;$i++)
       {
           $couleur = (($i/2) > floor($i/2)) ? 'background-color:#eee' : 'background-color:#fff';
           $fichier = $result[$i]->attachment;
           if ($fichier != "")
           {
              if (!strstr(strtolower($fichier),'jpg') && !strstr(strtolower($fichier),'jpeg') &&
                 !strstr(strtolower($fichier),'gif') && !strstr(strtolower($fichier),'png'))
                       $leFile = '<div><img src='.$_SESSION['LMS'].'/images/messagerie/icoGtrombon.gif"></div>';
              else
              {
                       $img= $_SESSION['LMS'].'/ressources/forums/groupe'.$_GET['id_grp'].'/'.$fichier;
                       $leFile = '<div style="float:left;padding:4px;">'.
                                 '<span style="font-weight:bold;font-size:12px;">Image jointe</span>'.
                                 '<br/><img src="'.$img.'" style="padding:5px;border:1px solid #bbb;"></div>';
              }
          }else
               $leFile ='';
           $content .= "<div id='msg".$i."'style='clear:both;float:left;max-width:610px;".$couleur.
                  ";font-size:12px;width:95%;padding:4px;margin-top:6px;color:#000; border:2px solid #999;'>".
                  "<div style='margin:4px;padding:2px;'><span style='font-weight:bold;'>Date : </span>".$result[$i]->datestamp."</div>".
                  "<div style='margin:4px;padding:2px;'><span style='font-weight:bold;'>Auteur : </span>".$result[$i]->author."</div>".
                  "<div style='margin:4px;padding:2px;'><span style='font-weight:bold;'>Sujet : </span>".$result[$i]->subject."</div>".
                  "<div style='margin:4px;padding:2px;background-color:#ddd; border:1px solid #bbb;max-width:600px;'>".
                  "<span style='font-weight:bold;'>Message : </span>".
                  html_entity_decode($result[$i]->body,ENT_QUOTES,'ISO-8859-1')."</div>".$leFile.
                  "</div>";
      }
      $content .= '<div style="clear:both;margin-bottom:2px;font-size:12px;color:#000;cursor:pointer;" >'.
                  '<a class="ui-btn ui-shadow ui-btn-corner-all ui-mini ui-btn-inline ui-btn-icon-left '.
                  'ui-btn-up-b" data-wrapperels="span" data-iconshadow="true" data-shadow="true" '.
                  'data-corners="true" href="#" data-role="button" data-mini="true" data-inline="true" '.
                  'data-theme="b" onClick="$(\'#commencer\').load(\'formateur/acces_forum.inc.php\');" >'.
                  '<span class="ui-btn-inner"><span class="ui-btn-text">Revenir à la liste des forums</span>'.
                  '</span></a></div>';
      echo utf2Charset(stripslashes($content),"iso-8859-1");
}
?>
