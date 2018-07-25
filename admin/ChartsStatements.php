<?php
if (!isset($_SESSION)) session_start();
include ("../include/UrlParam2PhpVar.inc.php");
require '../admin.inc.php';
require '../fonction.inc.php';
require "../lang$lg.inc.php";
require '../fonction_html.inc.php';
dbConnect();
//include ('../style.inc.php');
$currentUser = nomUser($_SESSION['id_user']);
switch ($_SESSION['typ_user'])
{
   case 'ADMINISTRATEUR' : $RoleUser = "Adm";break;
   case 'APPRENANT' : $RoleUser = "App";break;
   case 'RESPONSABLE_FORMATION' : $RoleUser = "RF";break;
   case 'FORMATEUR_REFERENT' : $RoleUser = "FR";break;
   case 'TUTEUR' : $RoleUser = "Tut";break;
}
if (isset($_SERVER['HTTP_REFERER']) && !strstr($_SERVER['HTTP_REFERER'],$adresse_http))
{
   echo "<script language=\"JavaScript\">";
   echo "document.location.replace(\"$adresse_http/index.php\")";
   echo "</script>";
   exit();
}

$ContentHead = '';
$ContentHead .= '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
                <html>
                <HEAD>
                <!Quirks mode -->
                <META HTTP-EQUIV="X-UA-Compatible" content="IE=8" />
                <META HTTP-EQUIV="Content-Type" content="text/html; charset='.$charset.'">
                <META HTTP-EQUIV="Content-Language" CONTENT="'.$code_langage.'">
                <META HTTP-EQUIV="Expires" CONTENT="Fri, Jan 01 1900 00:00:00 GMT">
                <META NAME="ROBOTS" CONTENT="No Follow">
                <META HTTP-EQUIV="Cache-Control" CONTENT="no-cache, must-revalidate">
                <META HTTP-EQUIV="Pragma" CONTENT="no-cache">';

$ContentHead .= '<TITLE>Formagri :: '.str_replace('.educagri.fr','',str_replace('cfppa-','',str_replace('ef-','',$_SERVER['SERVER_NAME']))).
                  ' :: '.$RoleUser.' :: '.$currentUser.'</TITLE>';
$ContentHead .= '<link rel="shortcut icon" href="/images/icone.ico" type="image/x-icon" />
                <link rel="stylesheet" type="text/css" href="../general.css" />
                <link rel="stylesheet" type="text/css" href="../admin/style_admin.css" />
                <link rel="stylesheet" type="text/css" href="../OutilsJs/style_jquery.css" />
                <link rel="stylesheet" type="text/css" href="../OutilsJs/lib/simplePagination.css"/>
                <script type="text/javascript" src="../OutilsJs/jquery-182-min.js"></script>
                <script type="text/javascript" src="../OutilsJs/lib/jquery.simplePagination.js"></script>';
$ContentHead .= '<div id="overDiv" style="position:absolute; visibility:hidden;z-index:1000;"></div>
                <SCRIPT type="text/javascript" SRC="'.$adresse_http.'/overlib.js"><!-- overLIB (c) Erik Bosrup --></SCRIPT>';
echo $ContentHead;
if (empty($_POST))
{
   $DateFin = date('Y-m-d');
   $DateDebut =   date('Y-m-d',time() - (61 * 24 * 60 * 60));
   $nbParPage =  10;
}
else
{
      $nbParPage = $_POST['nbParPage'];
      $DateFin = $_POST['endDateInput'];
      $DateDebut = $_POST['startDateInput'];
}
if (isset($_GET['who']))
   $suitable = 'who='.$_GET['who'];
else
{
   echo 'Vous ne pouvez pas visualiser cette page';
   exit;
}
if (isset($_GET['groupe']))
{
   $suitable .= '&groupe='.$_GET['groupe'];
   $TitreGraphe = "Nombre de statements pour la période";
   $GetTitre = 'Affichage Statements Apprenant :'.$_GET['who'];
}
else
{
   $TitreGraphe = "Nombre de statements et d\'usagers pour la période";
   $GetTitre = 'Affichage Statements Usagers';
}

$url = parse_url($_SERVER['REQUEST_URI']);
$resultUrl=array();
parse_str($url['query'],$resultUrl);
if (isset($resultUrl['endpoint']))
{

echo "<script type='text/javascript' src='../lib/TinCanGeneric/scripts/TinCanJS/build/tincan-min.js'></script>";

echo '<script type="text/javascript">
  FormagriExample = {};
  var getTitre = "'.$GetTitre.'";
  FormagriExample.CourseActivity = {
    id: "http://formagri.com/Suivi",
    definition: {
        type: "http://adlnet.gov/expapi/activities/course",
        name: {
            "fr-FR": "formagri.com/suivi - Tin Can Course"
        },
        description: {
            "fr-FR": getTitre
        }
    }
  };

  FormagriExample.getContext = function(parentActivityId) {
    var ctx = {
        contextActivities: {
            grouping: {
                id: FormagriExample.CourseActivity.id
            }
        }
    };
    if (parentActivityId !== undefined && parentActivityId !== null) {
        ctx.contextActivities.parent = {
            id: parentActivityId
        };
    }
    return ctx;
  };
  var tincan = new TinCan (
  {
    url: window.location.href,
    activity: {
       id: "Suivi : " + getTitre,
       definition: {
          name: {
             "fr-FR": "Suivi : " + getTitre
          },
          description: {
             "fr-FR":  "Opération tracking via LRS."
          }
       }
    }
  }
  );

  tincan.sendStatement(
            {
                verb: "experienced",
                context: FormagriExample.getContext(
                    FormagriExample.CourseActivity.id
                )
            },
            function () {}
  );
</script>
';
}
?>
<style>
.GeneralStmt{clear:both;float:left;width:100%;height:100%;}
.MenuStmt{clear:both;float:left;width:16%;height:95%;min-width:170px;margin-right:1px;background-color:#efefef;border:1px solid #bbb;}
.MenuDessus{clear:both;float:left;height:40px;border-bottom:2px solid #ccc;margin:10px 0 5px 0;width:100%;}
.TitreMenuStmt{float:left;font-size:25px;color:#aaa;text-align:center;padding-left:20px;}
.TitreMenuStmt span{font-size:16px;color:#aaa;text-align:center;}
.ImgStmt{z-index:23;position:absolute;top:0px;right:0px;padding:4px;background-color:maroon;color:#fff;font-size:10px;font-weight:bold;}
.GereDate{clear:both;float:left;padding:2px;border:1px solid #aaa;margin:5px;width:90%;}
.FormInputStmt{clear:both;float:left;margin:10px 0 0 5px;}
.FormInputStmt span{margin-right:5px;font-size:12px;}
.ButtonStmtSub{margin:10px 0 0 35px;}
.RenduDatas{float:left;height:95%;width:82%;}
.ChartDivStmt{clear:both;float:left;width:99%;height:auto;min-height:350px;margin-bottom:5px;background:#eee;}
.SelecteurStmt{clear:both;float:left;border:1px solid #bbb;width:99%;background:#eee;}
.LeStatutStmt{clear:both;float:left;border:1px solid #bbb;color:white;font-size:10px;font-family:arial;padding:1px;margin-top:2px;}
.statutStmt{clear:both;float:left;background:#aaa;width:auto;color:white;font-size:10px;font-family:arial;padding:2px;}
.RetLigne{clear:both;float:left;margin:5px;}
.RetLigne span {clear:both;float:left;font-size:12px;margin:2px 0 0 5px;margin:5px;}
.SelectVerbs {clear:both;font-size:14px;margin:0 0 0 15px;}
</style>
    <!--Load the AJAX API-->
<script type="text/javascript" src="https://www.google.com/jsapi"></script>
    <!--
<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
    -->
<script type="text/javascript">

    // Load the Visualization API and the piechart package.
    google.load('visualization', '1.1', {'packages':['corechart']});

    // Set a callback to run when the Google Visualization API is loaded.
    google.setOnLoadCallback(drawChart);

    function drawChart()
    {
        $.ajax({
          url: 'http://lms.annulab.com/TinCanApi/ChartData.php',
          data: 'stats=1&<?php echo "mois1=$DateDebut&mois2=$DateFin&$suitable";?>',
          dataType:'json',
         success:function(Response)
         {
            callback(Response);
          }
          });
          //alert(jsonData);
     }
     function callback(Response)
     {

         // Create our data table out of JSON data loaded from server.
         var data = new google.visualization.DataTable(Response); //alert(Response);
         // Instantiate and draw our chart, passing in some options.
         var chart = new google.visualization.ScatterChart(document.getElementById('chart_div'));
         var options = { title: '<?php echo $TitreGraphe;?>',backgroundColor:'#eee',
                     hAxis: {title: 'Jours',
                             textStyle: {color: '#000', fontName: 'Arial', fontSize: 10},
                             //format : 'd/M/yy',
                             gridlines: { color: '#bbb', count: -1,
                                          units: {
                                               days: {format: ['d/M/yy']},
                                               hours: {format: ['HH:mm:ss','ha']},
                                               }
                                        }
                            },
                     vAxis: {textStyle: {color: '#000', fontName: 'Arial', fontSize: 14},
                             baselineColor: '#336699',minValue: 0,
                             gridlines: { color: '#bbb', count: 5}
                            },
                     series: { 0: {targetAxisIndex: 0},
                               1: {targetAxisIndex: 1}
                             },
                     vAxes: {0: {title: 'Nombre de statements'},
                             1: {title: 'Nombre d\'usagers'}
                             },
                     curveType: 'function',
                     vAxis: {viewWindow: {min:0} },
                     lineWidth: 2,pointSize: 6,dataOpacity:0.3,
                     colors: ['#171CF1', '#097138']
         };
        chart.draw(data,options);
    }
</script>

<script language='javascript'>
var secs;
var timerID = null;
var timerRunning = false;
var delay = 1000 ;
InitializeTimer();
function InitializeTimer()
{
    secs = 1;
   StartTheTimer();
}

function StopTheClock()
{
    if(timerRunning)
        clearTimeout(timerID);
    timerRunning = false;
}
function StartTheTimer()
{
        secs = secs - 1;
        var seconde = (secs < 2) ? ' seconde ' : ' secondes';
        if (secs < 61)
          var affSec = secs;
        if (secs > 60 && secs < 121)
          var affSec = '1 mn ' + (secs - 60);
        if (secs > 120 && secs < 181)
          var affSec = '2 mn ' + (secs - 120);
        $('#statut').html("Formagri version 2.8 -Septembre 2015- Cnerta/Eduter/AgroSupDijon. Ré-initialisation dans <span style='font-size:11px;font-weight:bold;'>"+ affSec + seconde+"</span>");
        timerRunning = true;
        if (secs == 0)
        {
            if ($('#Verbs').text() == 'Recharger les Etats')
            {
               var MonOption = $('#verbatim').text();
               verbe(MonOption);
            }
            else
            {
                $.ajax({
                   type: 'GET',
                   url: 'http://lms.annulab.com/TinCanApi/statementsCharts.php',
                   data: 'statements=all&nbParPage=<?php echo "$nbParPage&mois1=$DateDebut&mois2=$DateFin&$suitable";?>',
                   success: function(msg)
                   {
                        $("#loader_div").hide();
                        $('#selecteur').html(msg);
                   }
                });
            }
            secs = 180;
        }
        timerID = setTimeout('StartTheTimer()', delay);
}
function recharge(nbPages)
{
    $.ajax({
    type: 'GET',
    url: 'http://lms.annulab.com/TinCanApi/statementsCharts.php',
    data: 'statements=all&nbParPage='+nbPages+'&<?php echo "mois1=$DateDebut&mois2=$DateFin&$suitable";?>',
    beforeSend: function ()
                   {
                        $("#loader_div").show();
                        $('#selecteur').hide();
                        $('#SelectVerbs').hide();
                        $('#Verbs').text('Actions');
                   },
                    success: function(msg)
                   {
                        $("#loader_div").hide();
                        $('#selecteur').html(msg);
                        $('#selecteur').show();
                   }
           });
}
function printPage(nbPages)
{
    $.ajax({
    type: 'GET',
    url: 'http://lms.annulab.com/TinCanApi/statementsCharts.php',
    data: 'printPage=1&statements=all&nbParPage='+nbPages+'&<?php echo "mois1=$DateDebut&mois2=$DateFin&$suitable";?>',
    beforeSend: function ()
                {
                    $("#loader_div").show();
                },
    success: function(msg)
             {
                  $("#loader_div").hide();
                  PopupPrint(msg);
             }
    });
}

</script>
<script language='javascript'>
function verbe(Verb)
{
    $.ajax({
    type: 'GET',
    url: 'http://lms.annulab.com/TinCanApi/statementsCharts.php',
    data: 'LeVerbe='+Verb+'&statements=all&nbParPage=<?php echo "$nbParPage&mois1=$DateDebut&mois2=$DateFin&$suitable";?>',
    beforeSend: function () {
                    $("#loader_div").show();
                    //$('#selecteur').hide();
                   },
                    success: function(msg)
                   {
                        $("#loader_div").hide();
                        $('#selecteur').html(msg);
                        $('#selecteur').show();
                   }
           });
}
function verbose(Verb)
{
    $.ajax({
    type: 'GET',
    url: 'http://lms.annulab.com/TinCanApi/statementsCharts.php',
    data: 'verb='+Verb+'&statements=all&nbParPage=<?php echo "$nbParPage&mois1=$DateDebut&mois2=$DateFin&$suitable";?>',
    beforeSend: function () {
                    $("#loader_div").show();
                    $('#SelectVerbs').hide();
                   },
                    success: function(msg)
                   {
                        $("#loader_div").hide();
                        $('#SelectVerbs').html(msg);
                        $('#SelectVerbs').show();
                        $('#SelectVerbs').css('margin','10px 0 10px 20px');
                        $('#Verbs').css('margin-left','10px');
                        $('#Verbs').text('Recharger les Etats');
                        $('#Verbs').attr('OnClick','verbe($("#verbatim").text());');
                   }
           });
}
function callJson(JsonID)
{

    $.ajax({
    type: 'GET',
    url: 'http://lms.annulab.com/TinCanApi/statementsCharts.php',
    data: 'JsonID='+JsonID,
    beforeSend: function () {
                    $("#loader_div").show();
                   },
                    success: function(msg)
                   {
                        $("#loader_div").hide();
                        $('#AfficheJson_'+JsonID).html(msg);
                        $('#AfficheJson_'+JsonID).show();
                   }
           });
}

function updated(element)
{
    var idx=element.selectedIndex;
    var val=element.options[idx].value;
    var content=element.options[idx].innerHTML;
    $("#verbatim").text(val);
    verbe(val);
}

function PopupPrint(data)
{
    var mywindow = window.open('', 'selecteur', 'fullscreen=yes');
    mywindow.document.write('<html><head><title>Statements de la période du <?php echo $DateDebut." à ".$DateFin;?></title>');
    mywindow.document.write('<link rel="stylesheet" href="../general.css" type="text/css" />');
    mywindow.document.write('</head><body >');
    mywindow.document.write(data);
    mywindow.document.write('</body></html>');
    mywindow.document.close(); // necessary for IE >= 10
    mywindow.focus(); // necessary for IE >= 10
    mywindow.print();
    mywindow.close();
    return true;
}
</script>
  <!--[if IE]>
        <script src="../OutilsJs/lib/better/better-dom-legacy.js"></script>
    <![endif]-->
  <div id="loader_div" class="ImgStmt">Patientez pendant le chargement.....</div>
<div style="width:100%">
  <div id="Menu" class="MenuStmt">
    <div id="MenuDessus" class="MenuDessus">
      <div id="titreMenu" class="TitreMenuStmt">
           MENU
      </div>
    </div>
    <div class="GereDate">
      <div class="RetLigne">
         <span title= "La plage de dates ne doit en aucun cas dépasser 1 mois">
            Choisissez une période
         </span>
      </div>
      <form id="dateRange" name="dateRange" role="form" method="POST">
      <div class="FormInputStmt">
        <span>du</span>
        <input type="date" id="startDateInput" class="INPUT" name="startDateInput" size= "15" value="<?php echo $DateDebut;?>" data-format="dd-MM-yyyy" placeholder="dd-mm-yyyy"/>
      </div>
      <div class="FormInputStmt">
        <span>au</span>
        <input type="date" id="endDateInput" class="INPUT" name="endDateInput" size= "15" value="<?php echo $DateFin;?>" data-format="dd-MM-yyyy" placeholder="dd-mm-yyyy"/>
      </div>
      <div class="FormInputStmt">
          <label for="combien"><span>Etats par page:</span></label>
            <select name="nbParPage" selected=<?php echo $nbParPage;?> class="SELECT">
                  <option value=<?php echo $nbParPage.">".$nbParPage;?></option>
                  <option value=10>10</option>
                  <option value=12>12</option>
                  <option value=15>15</option>
                  <option value=20>20</option>
                  <option value=30>30</option>
            <select>
      </div>
      <div  class="FormInputStmt">
        <button type="button" id="updateGraph" title="Afficher le tableau des états pour la période et le nombre d'états/page choisis." class="ButtonStmtSub" onClick="CompareDate($('#startDateInput').val(), $('#endDateInput').val());">
         Afficher
        </button>
      </div>
    </div>
    </form>
    <div class="GereDate">
    <div class="FormInputStmt">
      <span style="margin-left:20px;">Recharger les Etats</span>
      <button type="button" id="Recharge" title="Recharger le tableau des états pour la période en cours." class="ButtonStmtSub" onClick="recharge(<?php echo $nbParPage;?>);">
         Recharger
      </button>
    </div>
    <div class="FormInputStmt" style="width:100%">
      <span style="margin-left:20px;">Etats / Action</span>
      <div id="SelectVerbs" class="SelectVerbs"></div>
      <button type="button" id="Verbs"  title="Afficher le tableau de sélection des divers états couverts par la période en cours." class="ButtonStmtSub" onClick="verbose(1);">
        Actions
      </button>
    </div>
    <div class="FormInputStmt">
      <span style="margin-left:20px;">Imprimer les Etats</span>
      <button type="button" id="Imprimer" title="Imprimer le tableau intégral des états pour la période en cours." class="ButtonStmtSub" onClick="printPage(<?php echo $nbParPage;?>);">
         Imprimer
      </button>
    </div>
    </div>
  </div>
  <div id="RenduDatas" class="RenduDatas">
    <div id="chart_div" class="ChartDivStmt">
    </div>
    <div id="selecteur" class="SelecteurStmt">
    </div>
    <div id="LeStatut" class="LeStatutStmt">
       <div id="statut" class="statutStmt">
            Formagri version 2.8 -Septembre 2015- Cnerta/Eduter/AgroSupDijon.
       </div>
    </div>
    <div id="verbatim" style="display:none"></div>
  </div>
</div>
<script type="text/javascript" language="javascript">
   //recharge(<?php echo $nbParPage;?>);
   function CompareDate(d1,d2) {
       var dateOne = new Date(d1);
       var dateTwo = new Date(d2);
       if (dateOne > dateTwo) {
            alert("Attention: La date de fin est antérieure à la date de début.");
       }
       else if ((dateTwo.valueOf() - dateOne.valueOf()) > 5400000000) {
            alert("Attention: l'écart doit être de 2 mois au maximum.");
       }
       else{
           document.forms["dateRange"].submit();
       }
    }

</script>
<script src="../OutilsJs/lib/better/better-dom.js"></script>
<script src="../OutilsJs/lib/better/better-i18n-plugin.js"></script>
<script src="../OutilsJs/lib/better/better-dateinput-polyfill.js"></script>
<script type='text/javascript'>
    var _gaq=_gaq||[];var pluginUrl = '//www.google-analytics.com/plugins/ga/inpage_linkid.js';_gaq.push(['_require', 'inpage_linkid', pluginUrl]);_gaq.push(['_setAccount','UA-42020054-1']);_gaq.push(['_trackPageview']);_gaq.push(['_setCustomVar', 1, window.location.host, window.location.host, 1]);_gaq.push(['_setDomainName','none']);(function(){var ga=document.createElement('script');ga.type='text/javascript';ga.async=true;ga.src=('https:'==document.location.protocol?'https://ssl':'http://www')+'.google-analytics.com/ga.js';var s=document.getElementsByTagName('script')[0];s.parentNode.insertBefore(ga,s)})();
</script>
