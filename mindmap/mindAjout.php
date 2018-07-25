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
require  "mindmapClass.php";
dbConnect();
setlocale(LC_TIME,'fr_FR');
?>
<html>
<HEAD>
<!Quirks mode -->
     <META HTTP-EQUIV="X-UA-Compatible" content="IE=8" />
<META HTTP-EQUIV="Content-Type" content="text/html; charset=iso-8859-1">
<META HTTP-EQUIV="Content-Language" CONTENT="fr-FR">
<META HTTP-EQUIV="Expires" CONTENT="Fri, Jan 01 1900 00:00:00 GMT">
<META NAME="ROBOTS" CONTENT="No Follow">
<META HTTP-EQUIV="Cache-Control" CONTENT="no-cache, must-revalidate">
<META HTTP-EQUIV="Pragma" CONTENT="no-cache">
<link rel="shortcut icon" href="<?php echo $_SESSION['monURI'];?>/favicon.ico" type="image/x-icon" />
<link rel="stylesheet" type="text/css" href="<?php echo $_SESSION['monURI'];?>/mindmap/css/general.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo $_SESSION['monURI'];?>/mindmap/css/box.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo $_SESSION['monURI'];?>/mindmap/css/menu_vert.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo $_SESSION['monURI'];?>/mindmap/css/menu_compare.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo $_SESSION['monURI'];?>/mindmap/css/mindmap.css"/>
<script type="text/javascript" src="javascript/jquery.min.js"></script>
<script type="text/javascript" src="javascript/box.js"></script>
<script language="javascript" type="text/javascript" src="<?php echo $monURI;?>/tinymce/jscripts/tiny_mce/tiny_mce.js"></script>
<SCRIPT Language="Javascript">
</SCRIPT>
<script language="javascript">
  tinyMCE.init({
  // General options
  mode : "textareas",
  theme : "advanced",
  language : "fr",
  force_br_newlines : true,
  force_p_newlines : false,
  forced_root_block : '',
  plugins : "style,layer,table,advhr,advimage,advlink,inlinepopups,preview,media,contextmenu,paste,directionality,xhtmlxtras",
  theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,formatselect,fontsizeselect,|,cut,copy,paste,pastetext,pasteword",
  theme_advanced_buttons2 : "",
  theme_advanced_buttons3 : "",
  theme_advanced_toolbar_location : "top",
  theme_advanced_toolbar_align : "left",
  width:500,height:140,
  template_external_list_url : "js/template_list.js",
  external_link_list_url : "js/link_list.js",
  external_image_list_url : "js/image_list.js",
  media_external_list_url : "js/media_list.js"
 });

 function Unlocker(){
     $.ajax ({type: "GET",url:"unlock.php",data:"id_grp='.$id_grp.'"});
 }
</script>
<TITLE>Carte heuristique</TITLE>
</HEAD>
<SCRIPT language=JavaScript>
function checkForm(frm) {
  var ErrMsg = "Informations manquantes\n";
  var lenInit = ErrMsg.length;
  if (isEmpty(frm.titre)==true)
    ErrMsg += ' - Iitre de la carte heuristique\n';
  if (isEmpty(frm.consigne)==true)
    ErrMsg += ' - Consigne liée à la carte\n';
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
$leBody = '';
if (isset($_GET['fromWhere']) && $_GET['fromWhere'] == 'suivi')
{
   $leBody = '<body onunload="javascript=Unlocker();">';
}
else
   $leBody .='<body>';
echo $leBody;
$nom_user = $_SESSION["name_user"];
$prenom_user = $_SESSION["prename_user"];
$email_user = $_SESSION["email_user"];
$leJour = date("Y/m/d H:i:s" ,time());
$date_cour = date ("Y-m-d");
if (!empty($_POST['id_seq']))
   $id_seq = $_POST['id_seq'];
elseif (!empty($_GET['id_seq']))
   $id_seq = $_GET['id_seq'];
else
   $id_seq = 0;
if (!empty($_POST['id_grp']))
   $id_grp = $_POST['id_grp'];
elseif (!empty($_GET['id_grp']))
   $id_grp = $_GET['id_grp'];
else
   $id_grp = 0;
if (!empty($_GET['idMap'])) $idMap = $_GET['idMap'];
$content = '';
if (!empty($_GET['ajt']) && $_GET['ajt'] == 1)
{
    $xmldata = '<MindMap>
                  <MM>
                     <Node x_Coord="400" y_Coord="270">
                        <Text>'.$_POST['titre'].'</Text>
                        <Format Underlined="0" Italic="0" Bold="0">
                        <Font>Trebuchet MS</Font>
                        <FontSize>14</FontSize>
                        <FontColor>ffffff</FontColor>
                        <BackgrColor>ff0000</BackgrColor>
                        </Format>
                     </Node>
                  </MM>
               </MindMap>';
    $date = date("Y-m-d H:i:s");
    $id = Donne_ID ($connect,"SELECT max(mindmap_cdn) from mindmap");
    $id_histo = Donne_ID ($connect,"SELECT max(mindhisto_cdn) from mindmaphistory");
    $inserer= mysql_query("insert into mindmap values($id,$id_seq,$id_grp,\"".$_POST['titre'].
                           "\",\"".htmlentities($_POST['consigne'],ENT_QUOTES,'ISO-8859-1')."\",1,".
                           $_SESSION['id_user'].",1,\"".htmlentities($xmldata,ENT_QUOTES,'ISO-8859-1')."\",\"".
                           $date."\",\"".$date."\",1,1,".$_SESSION['id_user'].")");
    $inserer= mysql_query("insert into mindmaphistory values($id_histo,$id,".$_SESSION['id_user'].
                           ",0,\"".htmlentities($xmldata,ENT_QUOTES,'ISO-8859-1')."\",\"".$date."\")");
    $mess_notif ="Cette occurence a été ajoutée";
}
if (!empty($_GET['supp']) && $_GET['supp'] == 1)
{
    $supprimer = mysql_query("delete from mindmap where mindmap_cdn=".$_GET['idMap']);
    $supprimer = mysql_query("delete from mindmaphistory where mindhisto_map_no=".$idMap);
    $mess_notif ="Cette occurence a été supprimée";
}
if (!empty($mess_notif))
   echo notifier($mess_notif);
if (isset($_GET['supp']) || isset($_GET['ajt']))
{      ?>
       <SCRIPT language=javascript>
         setTimeout("Quit()",500);
          function Quit() {
           parent.main.tb_remove();
          }
       </SCRIPT>
       <?php
}
$suite = ($id_seq > 0) ? 'séquence' : 'formation';
$Provenance = ($id_seq > 0) ? 'SEQ' : 'GRP';
if ($id_seq > 0)
   $ReqMind = mysql_query("select * from mindmap,sequence where mindmap_seq_no = ".$id_seq.
                          " and mindmap_seq_no= seq_cdn order by mindmap_create_dt asc");
elseif ($id_grp > 0)
{
   $ReqMind = mysql_query("select * from mindmap,groupe where mindmap_grp_no = ".$id_grp.
                          " and mindmap_grp_no = grp_cdn order by mindmap_create_dt asc");
}
if(mysql_num_rows($ReqMind) > 0)
{
      $content .='<div id="ListMap" class="SOUS_TITRE">MindMaps liées à cette '.$suite.'</div>';
      $content .='<div id="LaListMap" class="individ">';
      while ($mindmap = mysql_fetch_object($ReqMind))
      {
         $auteur = ($id_seq > 0) ? $mindmap->seq_auteur_no : $mindmap->grp_resp_no;
         $content .='<div id="ItemMap" class="ItemMap">'.$mindmap->mindmap_titre_lb;
         if (isset($numero_groupe) || $auteur == $_SESSION['id_user'] || $mindmap->mindmap_auteur_no == $_SESSION['id_user'])
         {
            $content .= '<div class="ImgMap" title="Visualiser cette carte.">'.
                        '<a href="javascript:void(0);" onClick="window.open(\'index.php?id='.
                        $mindmap->mindmap_cdn.'&Provenance='.$Provenance.'\',\'null\',\'status=no, directories=no,copyhistory=0,'.
                        'titlebar=no, toolbar=yes, location=no, menubar=yes, scrollbars=yes, resizable=yes\');parent.tb_remove();">'.
                        '<img src="../images/ecran-annonce/icoGgo.gif" border="0"></a></div>';
            if ($mindmap->mindmap_auteur_no == $_SESSION['id_user']  && !isset($_GET['fromWhere']))
            {
               $Maps = mysql_query("select * from mindmaphistory where mindhisto_map_no = ".$mindmap->mindmap_cdn ." AND ".$mindmap->mindmap_auteur_no." != ".$_SESSION['id_user']);
               
               $NbMap = mysql_num_rows($Maps);
               if ( $NbMap == 0)
                   $content .= '<div class="ImgMap" title="Supprimer cette carte. Personne ne l\'a encore modifiée."> '.
                               '<a href="mindAjout.php?supp=1&idMap='.$mindmap->mindmap_cdn.'">'.
                               '<img src="../images/supp.png" border="0"></a></div>';
            }
         }
         $content .='</div>';
      }
      $content .='</div>';

   }
if ($id_seq > 0)
   $reqSeq = mysql_query("select * from sequence where seq_cdn='".$id_seq."'");
elseif ($id_grp > 0)
   $reqSeq = mysql_query("select * from groupe where grp_cdn='".$id_grp."'");
$oReq = mysql_fetch_object($reqSeq);
$auteurID = ($id_seq > 0) ? $oReq->seq_auteur_no : $oReq->grp_resp_no;
if ($auteurID == $_SESSION['id_user'] && !isset($_GET['fromWhere']))
{
      $lien= "mindAjout.php?ajt=1";
      $content .='<div id="ListMap" class="SOUS_TITRE" style="cursor:pointer;margin:15px 0 10px 0;" '.
                 ' onclick="$(\'#ajtWk\').toggle();$(\'#ListMap\').toggle();$(\'#LaListMap\').toggle();"> '.
                 'Ajouter une <b>"Carte heuristique"</b> ou <b>Afficher</b> les cartes liées à cette '.$suite.'</div>';
      $content .= '<div id="ajtWk" style="clear:both;display:none;">';
      $content .= '<form name="Form1" method="post" action="'.$lien.'" id="mindmapform">'.
                        '<input type="text" class="INPUT" style="clear:both;margin:15px 0 10px 0;" name="titre" '.
                        'size="50" placeholder="Titre de la carte heuristique" value="" />'.
                        '<textarea name="consigne" style="clear:both;margin:15px 0 10px 0;"></textarea>'.
                        '<input type="hidden" name="id_seq" value="'.$id_seq.'" />'.
                        '<input type="hidden" name="id_grp" value="'.$id_grp.'" />'.
                        '<div id="bouton1" style="clear:both;margin:2px 0 0 4px;">'.
                            '<a href="javascript:checkForm(document.Form1);" onClick="TinyMCE.prototype.triggerSave();">Valider</a>'.
                        '</div>'.
                  '</form>'.
                  '</div>';
}

echo $content;
echo '<div id="affiche"></div>';
echo '<div id="mien" class="cms" title="Cliquer pour fermer cette alerte" onClick="javascript:$(this).empty();";></div>';

?>
</body></html>