<?php
if (!isset($_SESSION)) session_start();
include ("../include/UrlParam2PhpVar.inc.php");
if (!isset($_SESSION['id_user']) || $_SESSION['id_user'] == "")
{
  exit();
}
require "../admin.inc.php";
require '../fonction.inc.php';
require '../fonction_html.inc.php';
require "../lang$lg.inc.php";
dbConnect();
include '../style.inc.php';
if ($insert_mess == 1)
{
   $requete = mysql_query("update message_inscription set mi_text_cmt=\"".$_POST['contenu1']."\" where mi_cdn = 1");
   $requete = mysql_query("update message_inscription set mi_text_cmt=\"".$_POST['contenu3']."\" where mi_cdn = 2");
   $requete = mysql_query("update message_inscription set mi_text_cmt=\"".$_POST['contenu2']."\" where mi_cdn = 3");
   $requete = mysql_query("update message_inscription set mi_text_cmt=\"".$_POST['contenu6']."\" where mi_cdn = 4");
   $requete = mysql_query("update message_inscription set mi_text_cmt=\"".$_POST['contenu4']."\" where mi_cdn = 5");
   echo " <div>Insertion réussie : Voici le message </div><br />".
        "<div style='color:#336699; border:1px solid red;background-color:#f4f4f4;padding:10px;width:600px;font-size:12px;'>";
   echo nl2br(stripslashes($_POST['contenu1'])).' <br /><br />';
   echo nl2br(stripslashes($_POST['contenu3'])).' <span style="color:red;">le login qui m\'est attribué </span><br />';
   echo nl2br(stripslashes($_POST['contenu2'])).' <span style="color:red;">le mot de passe qui m\'est attribué </span> <br />';
   echo nl2br(stripslashes($_POST['contenu6'])).' <span style="color:red;">http://ef-maplateforme.educagri.fr</span><br />';
   echo nl2br(stripslashes($_POST['contenu4'])).' <br /><br /></div>';
   exit();
}
 //--------------------------
 ?>
  <SCRIPT language=JavaScript>
    function checkForm(frm) {
      var ErrMsg = "<?php echo $mess_info_no;?>\n";
      var lenInit = ErrMsg.length;
      if (isEmpty(frm.contenu1)==true)
        ErrMsg += ' - Le premier élément \n';
      if (isEmpty(frm.contenu3)==true)
        ErrMsg += ' - Le deuxième élément\n';
      if (isEmpty(frm.contenu2)==true)
        ErrMsg += ' - Le troisième élément\n';
      if (isEmpty(frm.contenu6)==true)
        ErrMsg += ' - Le quatrième élément\n';
      if (isEmpty(frm.contenu4)==true)
        ErrMsg += ' - Le cinquième élément\n';

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
  $requete = mysql_query("SELECT * from message_inscription");
  echo "<TABLE bgColor='#FFFFFF' width='100%'>";
  echo '<FORM NAME="MForm1" id="MForm" ACTION="msgInsc.php?insert_mess=1" METHOD="post">';
  while ($itemMsg = mysql_fetch_object($requete))
  {
    if ($itemMsg->mi_cdn == 1)
        echo '<TR><TD nowrap valign ="center"><B> Portion1  </B></TD><TD nowrap valign ="center">
         <TEXTAREA  name="contenu1" rows="4" cols="60" align="middle">'.$itemMsg->mi_text_cmt.'</TEXTAREA>
         </TD></TR>';
    if ($itemMsg->mi_cdn == 2)
       echo '<TR><TD nowrap valign ="center"><B> Portion2 </B></TD><TD nowrap valign ="center">
         <INPUT TYPE="TEXT"  name="contenu3" size="60" value = "'.$itemMsg->mi_text_cmt.'" />
         </TD></TR>';
    if ($itemMsg->mi_cdn == 3)
       echo '<TR><TD nowrap valign ="center"><B> Portion3  </B></TD><TD nowrap valign ="center">
         <INPUT TYPE="TEXT"  name="contenu2" size="60" value = "'.$itemMsg->mi_text_cmt.'" />
         </TD></TR>';
    if ($itemMsg->mi_cdn == 4)
       echo '<TR><TD nowrap valign ="center"><B> Portion4  </B></TD><TD nowrap valign ="center">
         <INPUT TYPE="TEXT"  name="contenu6" size="60" value = "'.$itemMsg->mi_text_cmt.'" />
         </TD></TR>';
    if ($itemMsg->mi_cdn == 5)
       echo '<TR><TD nowrap valign ="center"><B> Portion5  </B></TD><TD nowrap valign ="center">
         <TEXTAREA  name="contenu4" rows="10" cols="60" align="middle">'.$itemMsg->mi_text_cmt.'</TEXTAREA>
         </TD></TR>';
    }
  echo '<TD align="left"><A HREF="javascript:checkForm(document.MForm1);"
       onmouseover="img1.src=\'../images/fiche_identite/boutvalidb.gif\';return true;"
       onmouseout="img1.src=\'../images/fiche_identite/boutvalid.gif\'">'.
       '<IMG NAME="img1" SRC="../images/fiche_identite/boutvalid.gif" BORDER="0"
       onLoad="tempImg=new Image(0,0); tempImg.src=\'../images/fiche_identite/boutvalidb.gif\'"></A>';
  echo "</FORM></TABLE>";
  //------------------------------------

?>
