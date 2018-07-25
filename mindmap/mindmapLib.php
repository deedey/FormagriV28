<?php
if (!isset($_SESSION)) session_start();
if (!isset($_SESSION['id_user']) || $_SESSION['id_user'] == "")
{
  exit();
}
header("Content-type  : text/plain");
header("Cache-Control: no-cache, max-age=0");
require "../fonction.inc.php";
require "../admin.inc.php";
include "../include/UrlParam2PhpVar.inc.php";
require "../fonction_html.inc.php";
require "mindmapClass.php";
dbConnect();
$date_op = date("Y-m-d H:i:s" ,time());
$heure_fiche = substr($date_op,11);
$date_fiche = substr($date_op,0,10);
//recupération du lien et du contenu via ajax
if (!empty($_GET['ajtClan']) && $_GET['ajtClan'] == 1)
{
    $reqMM=mysql_num_rows(mysql_query("select * from mindmapapp where mmapp_app_no='".$_GET['numApp']."' and
                      mmapp_seq_no='".$_GET['id_seq']."' and mmapp_parc_no='".$_GET['id_parc']."' and
                      mmapp_grp_no='".$_GET['id_grp']."'"));
    if ($reqMM == 0)
    {
        $date_deb = GetDataField ($connect,"select mmapp_db_dt from mindmapapp where
                                          mmapp_clan_nb ='".$_GET['id_clan']."' and mmapp_seq_no ='".
                                          $_GET['id_seq']."' and mmapp_grp_no ='".
                                          $_GET['id_grp']."' and mmapp_parc_no ='".
                                          $_GET['id_parc']."' and mmapp_mindmap_no ='".
                                          $_GET['id_mindmap']."'","mmapp_db_dt");
        $date_fin = GetDataField ($connect,"select mmapp_df_dt from mindmapapp where
                                          mmapp_clan_nb ='".$_GET['id_clan']."' and mmapp_seq_no ='".
                                          $_GET['id_seq']."' and mmapp_grp_no ='".
                                          $_GET['id_grp']."' and mmapp_parc_no ='".
                                          $_GET['id_parc']."' and mmapp_mindmap_no ='".
                                          $_GET['id_mindmap']."'","mmapp_df_dt");
        $id_mm = Donne_ID ($connect,"select max(mmapp_cdn) from mindmapapp");
        $req = mysql_query("insert into mindmapapp values ('$id_mm','".$_GET['id_mindmap']."','".$_GET['numApp']."','".
                      $_GET['id_seq']."','".$_GET['id_parc']."','".$_GET['id_grp']."','".$_GET['id_clan']."',\"".
                      $date_deb."\",\"".$date_fin."\")");
        $req = mysql_query("insert into mindmapnote values ('','$id_mm','NULL')");
        echo "ok";
    }
    else
    {
       echo "no";
    }
}
if (!empty($_GET['ajtNewClan']) && $_GET['ajtNewClan'] == 1)
{
       $date = date("Y-m-d H:i:s");
       $Ext = '_'.$_GET['id_grp'];
       $date_deb = GetDataField ($connect,"select presc_datedeb_dt from prescription$Ext where
                                                        presc_seq_no = '".$_GET['laSeq']."' and
                                                        presc_parc_no =' ".$_GET['id_parc']."'and
                                                        presc_utilisateur_no = '".$_GET['numApp']."' and
                                                        presc_grp_no = '".$_GET['id_grp']."'","presc_datedeb_dt");
       $date_fin = GetDataField ($connect,"select presc_datefin_dt from prescription$Ext where
                                                        presc_seq_no = '".$_GET['laSeq']."' and
                                                        presc_parc_no = '".$_GET['id_parc']."' and
                                                        presc_utilisateur_no = '".$_GET['numApp']."' and
                                                        presc_grp_no = '".$_GET['id_grp']."'","presc_datefin_dt");
        $IDmindmap = Donne_ID ($connect,"select max(mindmap_cdn) from mindmap");
        $id_mm = Donne_ID ($connect,"select max(mmapp_cdn) from mindmapapp");
        $id_clan = Donne_ID ($connect,"select max(mmapp_clan_nb) from mindmapapp");
        $nbClan = mysql_num_rows(mysql_query("select * from mindmaphistory where mindmap_clan_no > 0 and mindhisto_map_no = ".$_GET['id_mindmap']));
        $charger = mysql_query("select * from mindmap where mindmap_cdn = ".$_GET['id_mindmap']);
        $XmlData = mysql_fetch_object($charger);
        $xmlBase = '<MindMap>
                  <MM>
                     <Node x_Coord="400" y_Coord="270">
                        <Text>'.addslashes($XmlData->mindmap_titre_lb).' -'.$nbClan.'</Text>
                        <Format Underlined="0" Italic="0" Bold="0">
                        <Font>Trebuchet MS</Font>
                        <FontSize>14</FontSize>
                        <FontColor>ffffff</FontColor>
                        <BackgrColor>ff0000</BackgrColor>
                        </Format>
                     </Node>
                  </MM>
               </MindMap>';
         //créer une occurence dans Mindmap
         if ($nbClan > 0)
         {
              $inserer= mysql_query("insert into mindmap values($IDmindmap,".$XmlData->mindmap_seq_no.",".$XmlData->mindmap_grp_no.",\"".
                              $XmlData->mindmap_titre_lb.' -'.$nbClan."\",\"".$XmlData->mindmap_intro_cmt."\",".$XmlData->mindmap_introformat_nb.",".
                              $XmlData->mindmap_auteur_no.",1,\"".htmlentities(stripslashes($xmlBase),ENT_QUOTES,'ISO-8859-1')."\",\"".$date_deb."\",".
                              "\"".$date_fin."\",1,0,".$XmlData->mindmap_idlock_no.")");
              $id_max = Donne_ID ($connect,"SELECT max(mindhisto_cdn) from mindmaphistory");
              $ReqUpdateXml = mysql_query('INSERT INTO `mindmaphistory` VALUES('.$id_max.','.$IDmindmap.','.$XmlData->mindmap_auteur_no.
                                    ','.$id_clan.' ,"'.htmlentities(stripslashes($xmlBase),ENT_QUOTES,'ISO-8859-1').'", "'.$date.'")');
              $req = mysql_query("insert into mindmapapp values ('$id_mm','".$IDmindmap."','".$_GET['numApp']."','".
                                 $_GET['laSeq']."','".$_GET['id_parc']."','".$_GET['id_grp']."','".$id_clan."',\"".
                                 $date_deb."\",\"".$date_fin."\")");
              $req = mysql_query("insert into mindmapnote values ('','$id_mm','NULL')");
         }
         else
         {
              $req = mysql_query("insert into mindmapapp values ('$id_mm','".$id_mindmap."','".$_GET['numApp']."','".
                                 $_GET['laSeq']."','".$_GET['id_parc']."','".$_GET['id_grp']."','".$id_clan."',\"".
                                 $date_deb."\",\"".$date_fin."\")");
              $req = mysql_query("insert into mindmapnote values ('','$id_mm','NULL')");
              $req = mysql_query("update mindmaphistory set mindmap_clan_no = $id_clan where mindhisto_map_no = ".$_GET['id_mindmap']);
         }
}
if (!empty($_GET['modifie']) && $_GET['modifie'] == 1)
{
         $modifie= mysql_query("update ".$_GET['table']." set `".$_GET['champ']."`=\"".urldecode($_GET['new'])."\" where `".
                         $_GET['cdn']."` = '".$_GET['id']."'");
}
if(!empty($_GET['ajtNote']) && $_GET['ajtNote'] == 1)
{
  $oExist = mysql_num_rows(mysql_query("select * from mindmapnote where mmnote_app_no ='".$_GET['IdMM']."'"));
  if ($oExist == 1)
     $requete=mysql_query("update mindmapnote set mmnote_note_lb = '".$_GET['Note']."' where mmnote_app_no ='".$_GET['IdMM']."'");
  else
     $requete=mysql_query("insert into mindmapnote values('','".$_GET['IdMM']."','".$_GET['Note']."')");
}
if(!empty($_GET['suppNote']) && $_GET['suppNote'] == 1)
     $requete=mysql_query("delete from mindmapnote  where mmnote_app_no ='".$_GET['IdMM']."'");
?>