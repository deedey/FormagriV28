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
require "../lang$lg.inc.php";
require "../fonction_html.inc.php";
require "../class/class_module.php";
require "wikiClass.php";
dbConnect();
$date_op = date("Y-m-d H:i:s" ,time());
$heure_fiche = substr($date_op,11);
$date_fiche = substr($date_op,0,10);
//recupération du lien et du contenu via ajax
if (isset($_GET['suppComm']) && $_GET['IdComm'] > 0)
{
   $supprimmer = mysql_query("delete from commentaires where com_cdn =".$_GET['IdComm']);
   $ReqComment = mysql_query("select * from commentaires,utilisateur where comwk_body_no=".$_GET['IdBody']."
                              and utilisateur.util_cdn = com_auteur_no order by com_date_dt");
   $NbComment = mysql_num_rows($ReqComment);
   echo $NbComment;
}
if (isset($_POST['IdBody']) && $_POST['IdBody'] > 0)
{
      $id_wk = Donne_ID ($connect,"select max(starate_cdn) from starating");
      $req = mysql_query("insert into starating (starate_cdn,starate_auteur_no,wkstar_body_no,starate_note_nb,starate_date_dt)
                          values($id_wk,".$_SESSION['id_user'].",".$_POST['IdBody'].",".$_POST['value'].",'".time()."')");
      $reqWkAuteur = GetDataField($connect,"select wkbody_auteur_no from wikibodies where wkbody_cdn=".$_POST['IdBody'], "wkbody_auteur_no");
      $reqWkTitre = GetDataField($connect,"select wkbody_titre_lb from wikibodies where wkbody_cdn=".$_POST['IdBody'], "wkbody_titre_lb");
      $envoiMail = envoiMailRate($_POST['IdBody'],$_POST['value'],'Wiki',htmlspecialchars($reqWkTitre,ENT_QUOTES,'ISO-8859-1'),$reqWkAuteur);
}
if (!empty($_GET['ajtClan']) && $_GET['ajtClan'] == 1)
{
    $reqWk=mysql_num_rows(mysql_query("select * from wikiapp where wkapp_app_no='".$_GET['numApp']."' and
                      wkapp_seq_no='".$_GET['id_seq']."' and wkapp_parc_no='".$_GET['id_parc']."' and
                      wkapp_grp_no='".$_GET['id_grp']."'"));
    if ($reqWk == 0)
    {
        $date_deb = GetDataField ($connect,"select wkapp_db_dt from wikiapp where
                                          wkapp_clan_nb ='".$_GET['id_clan']."' and wkapp_seq_no ='".
                                          $_GET['id_seq']."' and wkapp_grp_no ='".
                                          $_GET['id_grp']."' and wkapp_parc_no ='".
                                          $_GET['id_parc']."' and wkapp_wiki_no ='".
                                          $_GET['id_wiki']."'","wkapp_db_dt");
        $date_fin = GetDataField ($connect,"select wkapp_df_dt from wikiapp where
                                          wkapp_clan_nb ='".$_GET['id_clan']."' and wkapp_seq_no ='".
                                          $_GET['id_seq']."' and wkapp_grp_no ='".
                                          $_GET['id_grp']."' and wkapp_parc_no ='".
                                          $_GET['id_parc']."' and wkapp_wiki_no ='".
                                          $_GET['id_wiki']."'","wkapp_df_dt");
        $id_wk = Donne_ID ($connect,"select max(wkapp_cdn) from wikiapp");
        $req = mysql_query("insert into wikiapp values ('$id_wk','".$_GET['id_wiki']."','".$_GET['numApp']."','".
                      $_GET['id_seq']."','".$_GET['id_parc']."','".$_GET['id_grp']."','".$_GET['id_clan']."',\"".
                      $date_deb."\",\"".$date_fin."\")");
        $req = mysql_query("insert into wikinote values ('','$id_wk','NULL')");
        echo "ok";
    }
    else
    {
       echo "no";
    }
}
if (!empty($_GET['ajtNewClan']) && $_GET['ajtNewClan'] == 1)
{
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
        $id_wk = Donne_ID ($connect,"select max(wkapp_cdn) from wikiapp");
        $id_clan = Donne_ID ($connect,"select max(wkapp_clan_nb) from wikiapp");
        $req = mysql_query("insert into wikiapp values ('$id_wk','".$_GET['id_wiki']."','".$_GET['numApp']."','".
                      $_GET['laSeq']."','".$_GET['id_parc']."','".$_GET['id_grp']."','".$id_clan."',\"".
                      $date_deb."\",\"".$date_fin."\")");
        $req = mysql_query("insert into wikinote values ('','$id_wk','NULL')");

}
if (!empty($_GET['modifie']) && $_GET['modifie'] == 1)
{
         $modifie= mysql_query("update ".$_GET['table']." set `".$_GET['champ']."`=\"".urldecode($_GET['new'])."\" where `".
                         $_GET['cdn']."` = '".$_GET['id']."'");
}
if (!empty($_GET['ordreCreate']) && $_GET['ordreCreate'] == 1)
{
   $nbOrdre = mysql_num_rows(mysql_query("select * from ".$_GET['table']." where wiki_seq_no='".$_GET['id_seq'].
                            "' and wiki_ordre_no='".$_GET['new']."'"));
   if ($nbOrdre == 0)
      $reqWk = mysql_query("update ".$_GET['table']." set wiki_ordre_on='1',wiki_ordre_no='".$_GET['new']."' where ".
                         $_GET['cdn']." = '".$_GET['id']."'");
   else
      echo "No";
}
if(!empty($_GET['valid_public']) && $_GET['valid_public'] == 1 )
{
  $Pub = GetDataField ($connect,"select wkbody_show_on from wikibodies where wkbody_cdn ='".$_GET['idPub']."'","wkbody_show_on");
  if ($Pub == 1)
  {
     $requete=mysql_query("update wikibodies set wkbody_show_on = 0 where wkbody_cdn='".$_GET['idPub']."'");
     $retour='<img src="images/invisible.gif" border="0">';
  }
  else
  {
     $requete=mysql_query("update wikibodies set wkbody_show_on = 1 where wkbody_cdn='".$_GET['idPub']."'");
     $retour='<img src="images/visible.gif" border="0">';
  }
  echo $retour;
}
if(!empty($_GET['ajtNote']) && $_GET['ajtNote'] == 1)
{
  $oExist = mysql_num_rows(mysql_query("select * from wikinote where wknote_app_no ='".$_GET['IdWk']."'"));
  if ($oExist == 1)
     $requete=mysql_query("update wikinote set wknote_note_lb = '".$_GET['Note']."' where wknote_app_no ='".$_GET['IdWk']."'");
  else
     $requete=mysql_query("insert into wikinote values('','".$_GET['IdWk']."','".$_GET['Note']."')");
}
?>