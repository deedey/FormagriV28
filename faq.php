<?php
if (!isset($_SESSION)) session_start();
include ("include/UrlParam2PhpVar.inc.php");
if (!isset($_SESSION['id_user']) || $_SESSION['id_user'] == "")
{
  exit();
}
require "admin.inc.php";
require 'fonction.inc.php';
require 'fonction_html.inc.php';
require "lang$lg.inc.php";
//include ("click_droit.txt");
dbConnect();
include ('style.inc.php');
if ($cms == 1){
  entete_simple($mess_menu_forum_formagri);
  echo aide_simple("faq");
  ?>
  <SCRIPT language=JavaScript>
    function checkForm(frm) {
      var ErrMsg = "<?php echo $mess_info_no;?>\n";
      var lenInit = ErrMsg.length;
//      if (isEmpty(frm.question)==true)
//        ErrMsg += ' - <?php echo $mess_cqcm_ins_q;?>\n';
//      if (isEmpty(frm.reponse)==true)
//        ErrMsg += ' - <?php echo $mess_cqcm_rep;?>\n';
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
  if (($ajouter == 1 || $modifier == 1) && $inserer !=1)
  {
    if ($modifier == 1)
    {
      $sql = mysql_query ("SELECT * from faq where faq_cdn = $num");
      $question = mysql_result($sql,0,"faq_question_lb_".$lg);
      $reponse = mysql_result($sql,0,"faq_reponse_lb_".$lg);
      $typ_util = mysql_result($sql,0,"faq_typutil_lb");
    }
    echo "<TR><TD><TABLE bgcolor='#FFFFFF' cellspacing='1' cellpadding=5 border=0 width=100%>";
    echo "<center><FORM NAME='form1' action=\"faq.php?inserer=1&modifier=$modifier&ajouter=$ajouter\" target='main' method='post'>";
    if ($modifier == 1)
       echo "<INPUT TYPE='HIDDEN' name='num' value='$num'>";
    echo "<TR><TD nowrap align='right' valign='top'><b>$mess_cqcm_ins_q *</b></TD>";
    echo "<TD nowrap><TEXTAREA class='TEXTAREA' name='question' rows='6' cols='100'>$question</TEXTAREA></TD></TR>";
    echo "<TR><TD nowrap align='right' valign='top'><b>$mess_cqcm_rep *</b></TD>";
    echo "<TD nowrap><TEXTAREA class='TEXTAREA'  name='reponse' rows='16' cols='100'>$reponse</TEXTAREA></TD></TR>";
    echo "<TR><TD nowrap align='right' valign='top'><b>$profil *</b></TD><TD nowrap>";
    echo "<SELECT  name='typ_util' size='1'>";
    if ($typ_util != "")
    echo   "<OPTION value = '$typ_util' selected>".ucfirst(strtolower($typ_util))."</OPTION>";
    echo   "<OPTION value = 'APPRENANT'>$mess_typ_app</OPTION>".
           "<OPTION value = 'TUTEUR'>$mess_typ_tut</OPTION>".
           "<OPTION value = 'FORMATEUR_REFERENT'>$mess_typ_fr</OPTION>".
           "<OPTION value = 'RESPONSABLE_FORMATION'>$mess_typ_rf</OPTION>".
           "<OPTION value = 'ADMINISTRATEUR'>$mess_typ_adm</OPTION>".
         "</SELECT></TD></TR>";
    echo "<TR><TD></TD><TD align='left'><A HREF=\"javascript:checkForm(document.form1);\" onmouseover=\"img1.src='images/fiche_identite/boutvalidb.gif';return true;\" onmouseout=\"img1.src='images/fiche_identite/boutvalid.gif'\">";
    echo "<IMG NAME=\"img1\" SRC=\"images/fiche_identite/boutvalid.gif\" BORDER='0' onLoad=\"tempImg=new Image(0,0); tempImg.src='images/fiche_identite/boutvalidb.gif'\"></A></TD></TR> ";
    echo "</FORM></TABLE>";
  }
  echo "</TD></TR></TABLE></TD></TR></TABLE>";
  exit;
}
if ($supprimer == 1)
  $saisie = mysql_query ("DELETE FROM faq WHERE faq_cdn='$num'");
if ($inserer == 1)
{
//   $reponse = str_replace("\n","<BR>",$reponse);
   if ($ajouter == 1 && $question != '' && $reponse != '' && strlen($question < 10) && strlen($reponse < 10))
   {
      $id_max = Donne_ID ($connect,"select max(faq_cdn) from faq");
      $saisie = mysql_query ("INSERT INTO faq (faq_cdn,faq_question_lb_$lg,faq_reponse_lb_$lg,faq_auteur_no,faq_typutil_lb) VALUES ($id_max,\"$question\",\"$reponse\",$id_user,\"$typ_util\")");
   }
   elseif($modifier == 1)
      $saisie = mysql_query ("UPDATE faq set faq_question_lb_$lg=\"$question\",faq_reponse_lb_$lg=\"$reponse\",faq_typutil_lb=\"$typ_util\" WHERE faq_cdn='$num'");
}
?>
    <!-- Collapsible tables list scripts -->
    <script type="text/javascript" language="javascript">
    <!--
    var isDOM      = (typeof(document.getElementsByTagName) != 'undefined'
                      && typeof(document.createElement) != 'undefined')
                   ? 1 : 0;
    var isIE4      = (typeof(document.all) != 'undefined'
                      && parseInt(navigator.appVersion) >= 4)
                   ? 1 : 0;
    var isNS4      = (typeof(document.layers) != 'undefined')
                   ? 1 : 0;
    var capable    = (isDOM || isIE4 || isNS4)
                   ? 1 : 0;
    // Uggly fix for Opera and Konqueror 2.2 that are half DOM compliant
    if (capable) {
        if (typeof(window.opera) != 'undefined') {
            capable = 0;
        }
        else if (typeof(navigator.userAgent) != 'undefined') {
            var browserName = ' ' + navigator.userAgent.toLowerCase();
           if (browserName.indexOf('konqueror') > 0) {
                capable = 0;
            }
        } // end if... else if...
    } // end if
    var fontFamily = 'arial';
    var fontSize   = '12px';
    var fontBig    = '12px';
    var fontSmall  = '12px';
    var isServer   = true;
    //-->
    </script>
<script language="javascript1.2">
var isExpanded   = false;
var imgOpened    = new Image(19,13);
imgOpened.src    = 'images/vide.gif';
var imgClosed    = new Image(13,19);
imgClosed.src    = 'images/vide.gif';

function reDo() {
  if (innerWidth != origWidth || innerHeight != origHeight)
    location.reload(true);
} // end of the 'reDo()' function

if (isNS4) {
  var origWidth  = innerWidth;
  var origHeight = innerHeight;
  onresize       = reDo;
}

function nsGetIndex(el) {
  var ind       = null;
  var theLayers = document.layers;
  var layersCnt = theLayers.length;
  for (var i = 0; i < layersCnt; i++) {
    if (theLayers[i].id == el) {
      ind = i;
      break;
    }
  }
  return ind;
} // end of the 'nsGetIndex()' function

function nsArrangeList() {
  if (typeof(firstInd) != 'undefined' && firstInd != null) {
    var theLayers = document.layers;
    var layersCnt = theLayers.length;
    var nextY     = theLayers[firstInd].pageY + theLayers[firstInd].document.height;
    for (var i = firstInd + 1; i < layersCnt; i++) {
      if (theLayers[i].visibility != 'hide') {
        theLayers[i].pageY = nextY;
        nextY              += theLayers[i].document.height;
      }
    }
  }
} // end of the 'nsArrangeList()' function
function nsShowAll() {
  var theLayers = document.layers;
  var layersCnt = theLayers.length;
  for (i = firstInd; i < layersCnt; i++) {
    theLayers[i].visibility = 'show';
  }
} // end of the 'nsShowAll()' function


function initIt()
{
  if (!capable || !isServer)
    return;

  var tempColl    = null;
  var tempCollCnt = null;
  var i           = 0;

  if (isDOM) {
    tempColl    = document.getElementsByTagName('DIV');
    tempCollCnt = tempColl.length;
    for (i = 0; i < tempCollCnt; i++) {
      if (tempColl[i].id == expandedDb)
        tempColl[i].style.display = 'block';
      else if (tempColl[i].className == 'child')
        tempColl[i].style.display = 'none';
    }
  } // end of the DOM case
  else if (isIE4) {
    tempColl    = document.all.tags('DIV');
    tempCollCnt = tempColl.length;
    for (i = 0; i < tempCollCnt; i++) {
      if (tempColl(i).id == expandedDb)
        tempColl(i).style.display = 'block';
      else if (tempColl(i).className == 'child')
        tempColl(i).style.display = 'none';
    }
  } // end of the IE4 case
  else if (isNS4) {
    var theLayers  = document.layers;
    var layersCnt  = theLayers.length;
    for (i = 0; i < layersCnt; i++) {
      if (theLayers[i].id == expandedDb)
        theLayers[i].visibility   = 'show';
      else if (theLayers[i].id.indexOf('Child') != -1)
        theLayers[i].visibility   = 'hide';
      else
        theLayers[i].visibility   = 'show';
    }
    nsArrangeList();
  } // end of the NS4 case
} // end of the 'initIt()' function
function expandBase(el, unexpand)
{
  if (!capable)
    return;

  var whichEl = null;
  var whichIm = null;

  if (isDOM) {
    whichEl = document.getElementById(el + 'Child');
    whichIm = document.getElementById(el + 'Img');
    if (whichEl.style.display == 'none' && whichIm) {
      whichEl.style.display  = 'block';
      whichIm.src            = imgOpened.src;
    }
    else if (unexpand) {
      whichEl.style.display  = 'none';
      whichIm.src            = imgClosed.src;
    }
  } // end of the DOM case
  else if (isIE4) {
    whichEl = document.all(el + 'Child');
    whichIm = document.images.item(el + 'Img');
    if (whichEl.style.display == 'none') {
      whichEl.style.display  = 'block';
      whichIm.src            = imgOpened.src;
    }
    else if (unexpand) {
      whichEl.style.display  = 'none';
      whichIm.src            = imgClosed.src;
    }
  } // end of the IE4 case
  else if (isNS4) {
    whichEl = document.layers[el + 'Child'];
    whichIm = document.layers[el + 'Parent'].document.images['imEx'];
    if (whichEl.visibility == 'hide') {
      whichEl.visibility  = 'show';
      whichIm.src         = imgOpened.src;
    }
    else if (unexpand) {
      whichEl.visibility  = 'hide';
      whichIm.src         = imgClosed.src;
    }
    nsArrangeList();
  } // end of the NS4 case
} // end of the 'expandBase()' function

window.onload = initIt;
</script>
<?php
entete_simple($mess_menu_forum_formagri);
  echo "<TR><TD style=\"padding-top:2px;padding-bottom:2px;\">";
   if ($typ_user == 'ADMINISTRATEUR' && $ajout != 1 && $modif != 1)
   {
      echo "<div id='ajout' style=\"foat:left;padding-left:2px;\">".
           "<A href = \"faq.php?cms=1&ajouter=1\" class='bouton_new' target= 'main'>$mess_ajout_item</A></div>" ;
   }
  echo aide_div("faq",8,0,0,0)."</td></tr>";
echo "<TR><TD width='100%'><TABLE cellspacing='1' cellpadding=5 border=0 width='100%'>\n";
if ($typ_user == "ADMINISTRATEUR")
  $requete = mysql_query("SELECT * from faq order by faq_typutil_lb,faq_cdn");
elseif ($typ_user == "RESPONSABLE_FORMATION")
  $requete = mysql_query("SELECT * from faq where faq_typutil_lb != 'ADMINISTRATEUR' order by faq_cdn");
elseif ($typ_user == "FORMATEUR_REFERENT")
  $requete = mysql_query("SELECT * from faq where (faq_typutil_lb != 'ADMINISTRATEUR' AND faq_typutil_lb != 'RESPONSABLE_FORMATION') order by faq_cdn");
elseif ($typ_user == "TUTEUR")
  $requete = mysql_query("SELECT * from faq where (faq_typutil_lb != 'ADMINISTRATEUR' AND faq_typutil_lb != 'RESPONSABLE_FORMATION' AND faq_typutil_lb != 'FORMATEUR_REFERENT') order by faq_cdn");
elseif ($typ_user == "APPRENANT")
  $requete = mysql_query("SELECT * from faq where faq_typutil_lb='APPRENANT' order by faq_cdn");
$nb_faq = mysql_num_rows($requete);
if ($nb_faq == 0)
{
  $requete = mysql_query("SELECT * from faq where faq_typutil_lb='APPRENANT' order by faq_cdn");
  $nb_faq = mysql_num_rows($requete);
  $message = $mess_faq_vide;
  echo "<TR><TD width='100%' colspan='3'>$message</TD></TR>";
}
$faq_s_a = mysql_query("SELECT count(*) from faq where faq_auteur_no=0");
$nb_sa = mysql_result($faq_s_a,0);
$i = 0;
while ($i < $nb_faq)
{
  $l=$i+1;
  $num = mysql_result($requete,$i,"faq_cdn");
  $question = mysql_result($requete,$i,"faq_question_lb_$lg");
  $auteur = mysql_result($requete,$i,"faq_auteur_no");
  $typ_util = mysql_result($requete,$i,"faq_typutil_lb");
  $reponse = mysql_result($requete,$i,"faq_reponse_lb_$lg");
  $reponse = str_replace("\n","<BR>",$reponse);
  if ($typ_util == 'APPRENANT')
     $typ_utilb = $mess_typ_app;
  if ($typ_util == 'TUTEUR' || $entantquetut == 1)
     $typ_utilb = $mess_typ_tut;
  if ($typ_util == 'FORMATEUR_REFERENT' || $entantqueform == 1)
     $typ_utilb = $mess_typ_fr;
  if ($typ_util == 'RESPONSABLE_FORMATION' || $entantquepresc == 1)
     $typ_utilb = $mess_typ_rf;
  if ($typ_util == 'ADMINISTRATEUR')
     $typ_utilb = $mess_typ_adm;
  $type2 = $typ_utilb;
  echo couleur_tr($l,'');
  echo "<TD valign='top' width='20px'>\n";
  echo "<div id='el".$l."Parent' class='parent'>\n";
  echo "<A class='parent' href=\"#\" onclick=\"if (capable) {expandBase('el".$l."', true); return false;}\">\n".
       "<img name='imEx' id='el".$l."Img' src=\"images/vide.gif\" border='0'>$l</A>\n".
       "</div></TD><TD valign='top'><div id='el".$l."Parent' class='parent'><A class='parent' href=\"#\" onclick=\"if (capable) {expandBase('el".$l."', true); return false;}\">".
       "$question</A></div></TD>\n";
  echo "<TD><div id='el".$l."Child' class='child' style='margin-bottom: 5px'>\n$reponse</div></TD>";
  if ($typ_user == "ADMINISTRATEUR")
  {//$nb_faq > $nb_sa &&  && $auteur > 0){
      if ($type2 != $type1)
          echo "<TD align='left' valign='top'>$type2</TD>";
      else
          echo "<TD align='left' valign='top'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\"&nbsp;&nbsp;\"</TD>";
      $lien = "faq.php?cms=1&modifier=1&num=$num";
      $lien = urlencode($lien);
      echo "<TD align='center' valign='top'><A HREF=\"trace.php?link=$lien\" title =\"$mrc_app_modif\">";
      echo "<IMG SRC=\"images/repertoire/icoGrenomfich20.gif\" height=\"20\" width=\"20\" ALT=\"$mrc_app_modif\" border=0></TD>";
      $lien = "faq.php?supprimer=1&num=$num";
      $lien = urlencode($lien);
      echo "<TD align='center' valign='top'> <A href=\"trace.php?link=$lien\" title=\"$mess_ag_supp\" onclick=\"return(conf());\"".
           " onmouseover=\"img_p$i.src='images/messagerie/icoGpoubelb.gif';return true;\"".
           " onmouseout=\"img_p$i.src='images/messagerie/icoGpoubel.gif'\">".
           "<IMG NAME=\"img_p$i\" SRC=\"images/messagerie/icoGpoubel.gif\" width='15' height='20' BORDER='0'".
           " onLoad=\"tempImg=new Image(0,0); tempImg.src='images/messagerie/icoGpoubelb.gif'\"></A>";
      echo "</TD></TR>";
  }
    else
      echo "</TR>";
    $type1 = $typ_utilb;
  $i++;
}
echo "</TABLE></TD></TR></TABLE></TD></TR></TABLE>";
?>
    <!-- Arrange collapsible/expandable db list at startup -->
    <script type="text/javascript" language="javascript1.2">
    <!--
    if (isNS4) {
      firstEl  = 'el1Parent';
      firstInd = nsGetIndex(firstEl);
      nsShowAll();
      nsArrangeList();
    }
    expandedDb = '';
    //-->
    </script>


</body>
</html>