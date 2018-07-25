<?php
if (!isset($_SESSION)) session_start();
if (!isset($_SESSION['id_user']) || $_SESSION['id_user'] == "")
{
  exit();
}
header("Content-type  : text/plain");
header("Cache-Control: no-cache, max-age=0");
include "../include/UrlParam2PhpVar.inc.php";
require "../admin.inc.php";
require "../fonction.inc.php";
require "../lang$lg.inc.php";
require "../fonction_html.inc.php";
require "../class/class_module.php";
require "blogClass.php";
dbConnect();
$date_op = date("Y-m-d H:i:s" ,time());
$heure_fiche = substr($date_op,11);
$date_fiche = substr($date_op,0,10);
//recupération du lien et du contenu via ajax
if (isset($_GET['suppComm']) && $_GET['IdComm'] > 0)
{
   $supprimmer = mysql_query("delete from commentaires where com_cdn =".$_GET['IdComm']);
   $ReqComment = mysql_query("select * from commentaires,utilisateur where combg_body_no=".$_GET['IdBody']."
                              and utilisateur.util_cdn = com_auteur_no order by com_date_dt");
   $NbComment = mysql_num_rows($ReqComment);
   echo $NbComment;
}
if (isset($_POST['IdBody']) && $_POST['IdBody'] > 0)
{
      $id_bg = Donne_ID ($connect,"select max(starate_cdn) from starating");
      $req = mysql_query("insert into starating (starate_cdn,starate_auteur_no,bgstar_body_no,starate_note_nb,starate_date_dt)
                          values($id_bg,".$_SESSION['id_user'].",".$_POST['IdBody'].",".$_POST['value'].",'".time()."')");
      $reqBgAuteur = GetDataField($connect,"select bgbody_auteur_no from blogbodies where bgbody_cdn=".$_POST['IdBody'], "bgbody_auteur_no");
      $reqBgTitre = GetDataField($connect,"select bgbody_titre_lb from blogbodies where bgbody_cdn=".$_POST['IdBody'], "bgbody_titre_lb");
      $envoiMail = envoiMailRate($_POST['IdBody'],$_POST['value'],'Blog',htmlspecialchars($reqBgTitre,ENT_QUOTES,'ISO-8859-1'),$reqBgAuteur);
}
if (!empty($_GET['ajtClan']) && $_GET['ajtClan'] == 1)
{
    $reqBg=mysql_num_rows(mysql_query("select * from blogapp where bgapp_app_no='".$_GET['numApp']."' and
                      bgapp_seq_no='".$_GET['id_seq']."' and bgapp_parc_no='".$_GET['id_parc']."' and
                      bgapp_grp_no='".$_GET['id_grp']."'"));
    if ($reqBg == 0)
    {
        $date_deb = GetDataField ($connect,"select bgapp_db_dt from blogapp where
                                          bgapp_clan_nb ='".$_GET['id_clan']."' and  bgapp_grp_no ='".
                                          $_GET['id_grp']."' and bgapp_blog_no ='".
                                          $_GET['id_blog']."'","bgapp_db_dt");
        $date_fin = GetDataField ($connect,"select bgapp_df_dt from blogapp where
                                          bgapp_clan_nb ='".$_GET['id_clan']."' and bgapp_grp_no ='".
                                          $_GET['id_grp']."' and bgapp_blog_no ='".
                                          $_GET['id_blog']."'","bgapp_df_dt");
        $id_bg = Donne_ID ($connect,"select max(bgapp_cdn) from blogapp");
        $req = mysql_query("insert into blogapp values ('$id_bg','".$_GET['id_blog']."','".
                            $_GET['numApp']."','".$_GET['id_grp']."','".$_GET['numApp']."',\"".
                            $date_deb."\",\"".$date_fin."\")");
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
                                                        presc_utilisateur_no = '".$_GET['numApp']."'","presc_datedeb_dt");
       $date_fin = GetDataField ($connect,"select presc_datefin_dt from prescription$Ext where
                                                        presc_utilisateur_no = '".$_GET['numApp']."'","presc_datefin_dt");
        $id_bg = Donne_ID ($connect,"select max(bgapp_cdn) from blogapp");
        $id_clan = Donne_ID ($connect,"select max(bgapp_clan_nb) from blogapp");
        $req = mysql_query("insert into blogapp values ('$id_bg','".$_GET['id_blog']."','".
                           $_GET['numApp']."','".$_GET['id_grp']."','".$_GET['numApp']."',\"".
                           $date_deb."\",\"".$date_fin."\")");
}
if (!empty($_GET['modifie']) && $_GET['modifie'] == 1)
{
         $modifie= mysql_query("update ".$_GET['table']." set `".$_GET['champ']."`=\"".urldecode($_GET['new'])."\" where `".
                         $_GET['cdn']."` = '".$_GET['id']."'");
}
if(!empty($_GET['valid_public']) && $_GET['valid_public'] == 1 )
{
  $Pub = GetDataField ($connect,"select bgbody_show_on from blogbodies where bgbody_cdn ='".$_GET['idPub']."'","bgbody_show_on");
  if ($Pub == 1)
  {
     $requete=mysql_query("update blogbodies set bgbody_show_on = 0 where bgbody_cdn='".$_GET['idPub']."'");
     $retour='<img src="images/invisible.gif" border="0">';
  }
  else
  {
     $requete=mysql_query("update blogbodies set bgbody_show_on = 1 where bgbody_cdn='".$_GET['idPub']."'");
     $retour='<img src="images/visible.gif" border="0">';
  }
  echo $retour;
}
if(!empty($_GET['partage']) && $_GET['partage'] == 1 )
{
  if (isset($_GET['grp']) && $_GET['grp'] == 1)
  {
     $requete=mysql_query("update blogshare set bgshr_grp_no=".$_GET['groupe'].",bgshr_apps_on=0,bgshr_all_on=0 where bgshr_auteur_no=".$_SESSION['id_user']);
     $retour='Seuls les apprenants et formateurs de la formation choisie auront accès à votre blog';
  }
  elseif (isset($_GET['app']) && $_GET['app'] == 1)
  {
     $requete=mysql_query("update blogshare set bgshr_grp_no=0,bgshr_apps_on=1,bgshr_all_on=0 where bgshr_auteur_no=".$_SESSION['id_user']);
     $retour='Tous les apprenants de toutes vos formations auront accès à votre blog';
  }
  elseif (isset($_GET['all']) && $_GET['all'] == 1)
  {
     $requete=mysql_query("update blogshare set bgshr_grp_no=0,bgshr_apps_on=0,bgshr_all_on=1 where bgshr_auteur_no=".$_SESSION['id_user']);
     $retour='Tous les apprenants et formateurs ayant une quelconque relation avec vous auront accès à votre blog';
  }
  elseif (isset($_GET['img']) && $_GET['img'] == 1)
  {
     $requete=mysql_query("update blogshare set bgshr_img_on=1 where bgshr_auteur_no=".$_SESSION['id_user']);
     $retour='Mes images ou photos du blog feront partie de la galerie publique et pourront être utilisées dans les autres blogs';
  }
  elseif (isset($_GET['img']) && $_GET['img'] == 0)
  {
     $requete=mysql_query("update blogshare set bgshr_img_on=0 where bgshr_auteur_no=".$_SESSION['id_user']);
     $retour='Mes images ou photos du blog ne feront pas partie de la galerie publique et ne pourront plus être utilisées dans les autres blogs';
  }
  echo utf2Charset($retour,$_SESSION['charset']);
}
?>