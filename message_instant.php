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
$nom=GetDataField ($connect,"select util_nom_lb from utilisateur where util_cdn='$num'","util_nom_lb");
$prenom=GetDataField ($connect,"select util_prenom_lb from utilisateur where util_cdn='$num'","util_prenom_lb");
$agent = $_SERVER['HTTP_USER_AGENT'];
if (isset($_POST['charger']) && $_POST['charger'] == 1)
{
      $id_new_causer = Donne_ID ($connect,"SELECT max(causer_cdn) from causer");
      $req = "insert into causer values($id_new_causer,".$_SESSION['id_user'].",".$_POST['num'].",\" ".strip_tags(NewHtmlEntityDecode($_POST['message']))."\")";
      $requete = mysql_query($req);
       ?>
       <SCRIPT language=javascript>
         setTimeout("Quit()",500);
          function Quit() {
           self.opener=null;self.close();return false;
          }
       </SCRIPT>
      <?php
      exit();

}
include ('style.inc.php');
?>
<SCRIPT language=JavaScript>
function checkForm(frm) 
{
  var ErrMsg = "<?php echo $mess_info_no;?>\n";
  var lenInit = ErrMsg.length;
     if (isEmpty(frm.message)==true)
       ErrMsg += ' - <?php echo "message vide";?>\n';
  if (ErrMsg.length > lenInit)
    alert(ErrMsg);
  else
    frm.submit();
}
function isEmpty(elm) 
{
  var elmstr = elm.value + "";
  if (elmstr.length == 0)
    return true;
  return false;
}
</SCRIPT>
<?php
echo "<CENTER><TABLE bgColor='#298CA0' cellspacing='1' cellpadding ='0' width='100%'><TR><TD width='100%'>";
echo "<TABLE bgColor='#FFFFFF' cellspacing='1' cellpadding ='4' width='100%'>";
echo "<TR><TD bgcolor=\"#D88888\" colspan='2' height='40' align='center' valign='center'>".
     "<Font size='3' color='#FFFFFF'><B>Message instantané à $prenom $nom</B></FONT></TD></TR>";
echo "<FORM NAME='form1' METHOD='POST' action='message_instant.php'>";
echo "<TR><TD><span style='color:red;fontsize:9px;font-family:arial;'>* Pas plus de 60 caractères</span><br />";
echo "<INPUT TYPE='text' class='INPUT' name='message' size='60' maxlength='60' />";
echo "<INPUT TYPE='hidden' name='charger' value='1' /></TD>";
echo "<INPUT TYPE='hidden' name='num' value='$num' /></TD>";
 echo "<TD align='left' valign='bottom'><A href=\"javascript:checkForm(document.form1);\" onmouseover=\"img4.src='images/fiche_identite/boutvalidb.gif';return true;\" onmouseout=\"img4.src='images/fiche_identite/boutvalid.gif'\">";
 echo "<IMG NAME=\"img4\" SRC=\"images/fiche_identite/boutvalid.gif\" BORDER='0' onLoad=\"tempImg=new Image(0,0); tempImg.src='images/fiche_identite/boutvalidb.gif'\"></A>";
echo "</TD></TR></FORM></TABLE></TD></TR></TABLE></body></html>";
exit;
?>