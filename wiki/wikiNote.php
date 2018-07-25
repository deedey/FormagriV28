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
require "../langues/formation.inc.php";
require "../langues/module.inc.php";
require "wikiClass.php";
dbConnect();
include ("../style.inc.php");
$content = '';
if (!empty($_GET['numApp'])) $numApp = $_GET['numApp'];
if (!empty($_GET['IdWk'])) $IdWk = $_GET['IdWk'];
$content .= '<div class="sous_titre" style="font-family:arial,verdana;font-size:13px;width:auto;">'.
            'Evaluation de la participation de '.
            NomUser($_GET['numApp']).' à ce travail de groupe.</div>';
$content .= '<div class="NoteliNum" style="clear:both;float:left;">';
$content .= '<div style="float:left;padding:3px;background:url(\'images/fleche.gif\') right no-repeat;cursor:pointer;width:135px;" '.
            'onClick="javascript:var monTop=Top(this);var monLeft=Left(this);'.
            '$(\'#ul1a2\').css(\'top\',monTop+23);'.
            '$(\'#ul1a2\').css(\'left\',monLeft);'.
            '$(\'#ul1a2\').toggle();" title="Choisissez ce mode de notation : Ouvrir / Fermer">Notation de 1 à 20</div>';
$content .= '<ul id="ul1a2" style="display:none;">';
for ($a = 1;$a < 21;$a++)
{
    $content .= ajtNote($numApp,$IdWk,$a);
}
$content .= '</ul></div></div>';
$content .= '<div class="NoteliAlpha" style="float:left;">';
$content .= '<div style="float:left;padding:3px;background:url(\'images/fleche.gif\') right no-repeat;cursor:pointer;width:80px;" '.
            'onClick="javascript:var monTop=Top(this);var monLeft=Left(this);'.
            '$(\'#ulAaE\').css(\'top\',monTop+23);'.
            '$(\'#ulAaE\').css(\'left\',monLeft);'.
            '$(\'#ulAaE\').toggle();" title="Choisissez ce mode de notation : Ouvrir / Fermer">de A à E</div>';
$content .= '<ul id="ulAaE" style="display:none;">';
for ($a = 1;$a < 6;$a++)
{
    $content .= ajtNote($numApp,$IdWk,numerotation('alpha',$a));
}
$content .= '</ul></div></div>';
$content .= '<div class="NoteliAnA" style="float:left;">';
$content .= '<div style="float:left;padding:3px;background:url(\'images/fleche.gif\') right no-repeat;cursor:pointer;width:120px;" '.
            'onClick="javascript:var monTop=Top(this);var monLeft=Left(this);'.
            '$(\'#ulAnA\').css(\'top\',monTop+23);'.
            '$(\'#ulAnA\').css(\'left\',monLeft);'.
            '$(\'#ulAnA\').toggle();" title="Choisissez ce mode de notation : Ouvrir / Fermer">Acquis ou N/A</div>';
$content .= '<ul id="ulAnA" style="display:none;">';
$content .= ajtNote($numApp,$IdWk,'Acquis');
$content .= ajtNote($numApp,$IdWk,'Non acquis');
$content .= '</ul></div></div>';
// affichage
echo  $content;
echo '<div id="affiche"></div>';
echo '<div id="mien" class="cms" '.
     'title="Cliquer pour fermer cette alerte" onClick="javascript:$(this).empty();";></div>';

//fonctions
function ajtNote($numApp,$IdWk,$Note)
{
    $html = '<li id="ajt'.$Note.'" style="clear:both;padding:2px 2px 2px 8px;font-size:12px;font-family:arial,verdana;cursor:pointer;" '.
                                   'onMouseOver="javascript:$(this).css(\'color\',\'#D45211\');" '.
                                   'onMouseOut="javascript:$(this).css(\'color\',\'#24677A\');" '.
                                   'onClick="javascript:$.ajax({type: \'GET\', '.
                                              'url: \'wikilib.php\', '.
                                              'data: \'numApp='.$numApp.'&ajtNote=1&IdWk='.$IdWk.'&Note='.$Note.'\', '.
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
?>