<?php
session_start();
include ("../include/UrlParam2PhpVar.inc.php");
if (!isset($_SESSION['id_user']) || $_SESSION['id_user'] == "")
{
  exit();
}
require "../admin.inc.php";
require '../fonction.inc.php';
require "../lang$lg.inc.php";
require "../fonction_html.inc.php";
require "../langues/formation.inc.php";
require "../langues/module.inc.php";
require  "wikiClass.php";
dbConnect();
setlocale(LC_TIME,'fr_FR');
$nom_user = $_SESSION["name_user"];
$prenom_user = $_SESSION["prename_user"];
$email_user = $_SESSION["email_user"];
$leJour = date("Y/m/d H:i:s" ,time());
$date_cour = date ("Y-m-d");
if (!empty($_GET['numApp'])) $numApp = $_GET['numApp'];
if (!empty($_GET['id_seq'])) $id_seq = $_GET['id_seq'];
if (!empty($_GET['id_parc'])) $id_parc = $_GET['id_parc'];
if (!empty($_GET['id_grp'])) $numero_groupe = $_GET['id_grp'];
if (!empty($_GET['numero_groupe'])) $numero_groupe = $_GET['numero_groupe'];
if (!empty($_GET['numeroApp'])) $numeroApp = $_GET['numeroApp'];
if (!empty($_GET['id_clan'])) $id_clan = $_GET['id_clan'];
$resp_grp = GetDataField($connect, "select grp_resp_no from groupe WHERE grp_cdn = $numero_groupe", "grp_resp_no");
if ((isset($suppWiki) && $suppWiki == 1) && ($_SESSION['id_user'] == $resp_grp || $_SESSION['typ_user'] == 'ADMINISTRATEUR'))
   SupprimeWiki($_GET['WikiId'],$_GET['id_clan']);
$content = '';
include ("../style.inc.php");
$content.= '<div id="ListeWk" style="clear:both;float:left;background-color: #eee;border:1px solid #ccc;'.
           'margin:5px 2px 0 2px;padding:4px;max-height:250px;overflow-y:auto;width:530px;">';
$reqWk = mysql_query("select * from wiki,wikiapp where wiki_seq_no='".$_GET['id_seq'].
                     "' and wkapp_seq_no=wiki_seq_no and wkapp_wiki_no=wiki_cdn group by wiki_cdn order by wiki_ordre_no");
if (mysql_num_rows($reqWk) > 0)
{
   $i=0;
   while ($oWiki = mysql_fetch_object($reqWk))
   {
       //echo "<pre>";print_r($oWiki);echo "</pre>";
       $content .= '<div style="width:520px;font-size:12px;">';
       $content .= '<div style="clear:both;float:left;margin-top:10px;font-size:12px;font-weight:bold;">'.$oWiki->wiki_ordre_no.'-</div>';
       $content .= '<div style="float:left;margin:10px 4px 0 5px;cursor:pointer;width:420px;">'.$oWiki->wiki_consigne_cmt.'</div>';
       $lien = "wikiOpen.php?numApp=$id_app&id_seq=$id_seq&id_parc=$id_parc&id_grp=$numero_groupe&id_clan=".
                $oWiki->wkapp_clan_nb."&id_wk=".$oWiki->wkapp_cdn;
       $content .= '<div style="float:left;margin:10px 4px 0 5px;cursor:pointer;" '.
                   'title="Cliquez sur la flèche pour accéder au document en ligne." '.
                   'onClick="window.open(\''.$lien.'\',null,\'status=no, directories=no,copyhistory=0,'.
                   'titlebar=no, toolbar=yes, location=no, menubar=yes, scrollbars=yes, resizable=yes\');">'.
                   '<img src="../images/ecran-annonce/icoGgo.gif" border="0"></div>';
       if ($_SESSION['id_user'] == $resp_grp || $_SESSION['typ_user'] == 'ADMINISTRATEUR')
       {
          $lien = "wikiGrp.php?numApp=$id_app&id_seq=$id_seq&id_parc=$id_parc&id_grp=$numero_groupe&suppWiki=1&WikiId=".
                   $oWiki->wiki_cdn."&id_clan=".$oWiki->wkapp_clan_nb."&id_wk=".$oWiki->wkapp_cdn;
          $content .= '<div style="float:left;margin:10px 4px 0 5px;cursor:pointer;" '.
                      'title="Supprimer définitivement ce thème ainsi que tout ce qu\'il comporte de contenu." '.
                      'onClick="document.location.replace(\''.$lien.'\');">'.
                      '<img src="images/supp.png" border="0"></div>';
       }
       $content .= '</div>';
   }
   $i++;
}
else
{
        $lien= "wikiAjout.php?id_seq=$id_seq";
        echo "<script language='JavaScript'>";
             echo "document.location.replace(\"$lien\");";
        echo "</script>";
}
$content.= '</div>';
echo $content;
echo '<div id="affiche"></div>';
echo '<div id="mien" class="cms" title="Cliquer pour fermer cette alerte" onClick="javascript:$(this).empty();";></div>';

?>