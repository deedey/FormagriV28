<?php
if (!isset($_SESSION)) session_start();
include ("../include/UrlParam2PhpVar.inc.php");
if (!isset($_SESSION['id_user']) || $_SESSION['id_user'] == "")
{
  exit();
}
require "../admin.inc.php";
require '../fonction.inc.php';
require "../lang$lg.inc.php";
require "../fonction_html.inc.php";
require "mindmapClass.php";
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
<link rel="shortcut icon" href="../favicon.ico" type="image/x-icon" />
<link rel="stylesheet" type="text/css" href="css/general.css"/>
<link rel="stylesheet" type="text/css" href="css/menu_compare.css"/>
<link rel="stylesheet" type="text/css" href="css/mindmap.css"/>
<script type="text/javascript" src="javascript/jquery.min.js"></script>
<script type="text/javascript" src="../OutilsJs/jquery.tooltip.pack.js">
</head>
<body>
<script type="text/javascript" language="javascript">
    $(document).ready(function(){
      $("a").tooltip({showURL: false});
      $("div").tooltip({showURL: false});
      $("span").tooltip({showURL: false});
      $("li").tooltip({showURL: false});
      $("input").tooltip({showURL: false});
    });
</script>

<?php
$content = '';
if (!empty($_GET['numApp'])) $numApp = $_GET['numApp'];
if (!empty($_GET['IdMM'])) $IdMM = $_GET['IdMM'];
$content .= '<div class="sous_titre" style="clear:both;float:left;font-family:arial,verdana;'.
            'font-size:13px;max-width:90% !important;">'.
            '<div style="float:left;">Evaluation de la participation de '.
            NomUser($_GET['numApp']).' à la construction de cette carte heuristique.</div>';

$oExist = mysql_query("select * from mindmapnote where mmnote_app_no ='".$IdMM."'");
$NbNote = mysql_num_rows($oExist);
if ($NbNote > 0 && mysql_result($oExist,0,"mmnote_note_lb") != 'NULL')
    $content .= '<div class="NteObt">'.
                '<div style="float:left"> Note actuelle obtenue: <u>'.
                mysql_result($oExist,0,"mmnote_note_lb").' </u></div>'.suppNote($numApp,$IdMM).'</div>';
$content .= '</div>';
$content .= '<div class="NoteliNum" style="clear:both;float:left;">';
$content .= '<div class="NoteAlpha" '.
            'onClick="javascript:var x=$(this).offset();var monTop=x.top;var monLeft=x.left;'.
            '$(\'#ul1a2\').css(\'top\',monTop+23);'.
            '$(\'#ul1a2\').css(\'left\',monLeft);'.
            '$(\'#ul1a2\').toggle();" title="Choisissez ce mode de notation : Ouvrir / Fermer">Notation de 1 à 20</div>';
$content .= '<ul id="ul1a2" style="display:none;">';
for ($a = 1;$a < 21;$a++)
{
    $content .= ajtNote($numApp,$IdMM,$a);
}
$content .= '</ul></div></div>';
$content .= '<div class="NoteliAlpha" style="float:left;">';
$content .= '<div class="NoteAE" '.
            'onClick="javascript:var x=$(this).offset();var monTop=x.top;var monLeft=x.left;'.
            '$(\'#ulAaE\').css(\'top\',monTop+23);'.
            '$(\'#ulAaE\').css(\'left\',monLeft);'.
            '$(\'#ulAaE\').toggle();" title="Choisissez ce mode de notation : Ouvrir / Fermer">de A à E</div>';
$content .= '<ul id="ulAaE" style="display:none;">';
for ($a = 1;$a < 6;$a++)
{
    $content .= ajtNote($numApp,$IdMM,numerotation('alpha',$a));
}
$content .= '</ul></div></div>';
$content .= '<div class="NoteliAnA" style="float:left;">';
$content .= '<div class="NoteANA" '.
            'onClick="javascript:var x=$(this).offset();var monTop=x.top;var monLeft=x.left;'.
            '$(\'#ulAnA\').css(\'top\',monTop+23);'.
            '$(\'#ulAnA\').css(\'left\',monLeft);'.
            '$(\'#ulAnA\').toggle();" title="Choisissez ce mode de notation : Ouvrir / Fermer">Acquis ou N/A</div>';
$content .= '<ul id="ulAnA" style="display:none;">';
$content .= ajtNote($numApp,$IdMM,'Acquis');
$content .= ajtNote($numApp,$IdMM,'Non acquis');
$content .= '</ul></div></div>';
// affichage
echo  $content;
echo '<div id="affiche"></div>';
echo '<div id="mien" class="cms" '.
     'title="Cliquer pour fermer cette alerte" onClick="javascript:$(this).empty();";></div>';

//fonctions
function ajtNote($numApp,$IdMM,$Note)
{
    $html = '<li id="ajt'.$Note.'" class="FnAjtNte" '.
            'onMouseOver="javascript:$(this).css(\'color\',\'#D45211\');" '.
            'onMouseOut="javascript:$(this).css(\'color\',\'#24677A\');" '.
            'onClick="javascript:$.ajax({type: \'GET\', '.
                                         'url: \'mindmaplib.php\', '.
                                         'data: \'numApp='.$numApp.'&ajtNote=1&IdMM='.$IdMM.'&Note='.$Note.'\', '.
                                         'beforeSend:function() '.
                                         '{ '.
                                            '$(\'#affiche\').addClass(\'Status\'); '.
                                            '$(\'#affiche\').append(\'Opération en cours....\'); '.
                                         '}, '.
                                         'success: function() '.
                                         '{ '.
                                            '$(\'#mien\').empty(); '.
                                            '$(\'#mien\').html(\' Vous avez attribué la note : '.$Note.' à '.NomUser($numApp).'\'); '.
                                            '$(\'#affiche\').empty(); '.
                                            'setTimeout(function() {$(\'#mien\').empty();},5000); '.
                                            'setTimeout(function() {parent.location.reload();},500);'.
                                         '}'.
            '});" '.
            'title="Attribuez la note <strong>- '.$Note.'</strong> - à <strong>'.NomUser($numApp).'</strong>.">'.$Note.'</li>';
   return $html;
}
function suppNote($numApp,$IdMM)
{
    $html = '<div id="supp'.$IdMM.'" class="SuppNote" '.
            'onMouseOver="javascript:$(this).css(\'color\',\'#D45211\');" '.
            'onMouseOut="javascript:$(this).css(\'color\',\'#24677A\');" '.
            'onClick="javascript:$.ajax({type: \'GET\', '.
                                         'url: \'mindmaplib.php\', '.
                                         'data: \'numApp='.$numApp.'&suppNote=1&IdMM='.$IdMM.'\', '.
                                         'beforeSend:function() '.
                                         '{ '.
                                            '$(\'#affiche\').addClass(\'Status\'); '.
                                            '$(\'#affiche\').append(\'Opération en cours....\'); '.
                                         '}, '.
                                         'success: function() '.
                                         '{ '.
                                            '$(\'#mien\').empty(); '.
                                            '$(\'#mien\').html(\' Vous avez supprimé sa note à '.NomUser($numApp).'\'); '.
                                            '$(\'#affiche\').empty(); '.
                                            'setTimeout(function() {$(\'#mien\').empty();},5000); '.
                                            'setTimeout(function() {parent.location.reload();},500);'.
                                         '}'.
            '});" '.
            'title="Supprimer cette note"><img src="../images/supp.png" border="0"></div>';
   return $html;
}
?>