<?php
  if (!isset($_SESSION)) session_start();
  include ("../include/UrlParam2PhpVar.inc.php");
  include "param.php";
  require "../lang$lg.inc.php";
  require "../fonction_html.inc.php";

if ($lg == "ru"){
  $code_langage = "ru";
  $charset = "Windows-1251";
  putenv("TZ=Europe/Moscow");
}elseif ($lg == "fr"){
  $code_langage = "fr";
  $charset = "iso-8859-1";
  putenv("TZ=Europe/Paris");
}elseif ($lg == "en"){
  $code_langage = "en";
  $charset = "iso-8859-1";
}
/*
+-------------------------------------------------+
|        TJSChat Version 0.95                     |
|        MODULE DE CHAT en PHP4-MySQL             |
+-------------------------------------------------+
| Auteur    : Olivier HONDERMARCK                 |
| Web       : http://www.toutjavascript.com       |
|           : http://www.toutjavascript.com/chat  |
| Mail      : webmaster@toutjavascript.com        |
| Création  :  8 juin 2001                        |
+-------------------------------------------------+
*/
?>
<HTML>
<HEAD>
<META HTTP-EQUIV="Content-Type" content="text/html; charset=<?php echo $charset;?>">
<META HTTP-EQUIV="Content-Language" CONTENT="<?php echo $code_langage ;?>">
<META HTTP-EQUIV="Expires" CONTENT="Fri, Jan 01 1900 00:00:00 GMT">
<META NAME="ROBOTS" CONTENT="No Follow">
<META HTTP-EQUIV="Cache-Control" CONTENT="no-cache, must-revalidate">
<META HTTP-EQUIV="Pragma" CONTENT="no-cache">
<link REL='StyleSheet' TYPE='text/css' HREF='chat.css'>
<script type="text/javascript" src="/OutilsJs/jquery-144.js"></script>
<TITLE><?php print($param["title"]); ?></TITLE>
<SCRIPT LANGUAGE="JavaScript">
function fermeture() {
<?php
                $leCours ='usager`|0|0|0|0';
                $course =  base64url_encode($leCours);
                echo "window.open('quit.php?user=".$user.
                    TinCanTeach ('usager|0|0|0|0','quit.php?user='.$user,'http://formagri.com/Chat')."','','width=550,height=100,resizable=no,status=no');";
?>
}
</SCRIPT>
</HEAD>
<BODY background="../images/fondtitre.jpg" onLoad='top.load_main=1' onunload='fermeture()'>

<?php
$bouton_gauche = "<TABLE cellpadding='0' cellspacing='0' border=0><TR><TD><IMG SRC='../images/complement/cg.gif' border='0'></TD>".
                 "<TD background='../images/complement/milieu.gif' nowrap align='center'><DIV id='sequence'>&nbsp;";
$bouton_droite = "&nbsp;</DIV></TD><TD><IMG SRC='../images/complement/cd.gif' border='0'></TD><TR></TABLE>";
$style1="position:absolute;width:".($param["chat_form_width"]+2)."px;height:".($param["chat_form_height"]+2)."px;top:".($param["chat_form_top"])."px;left:".$param["chat_form_left"]."px;border-style:solid;border-width:1px;border-color:".$param["chat_form_border_color"].";background-Color:".$param["chat_bg_color"]."overflow: auto;";

$style2="position:absolute;width:".$param["chat_form_width"]."px;height:".$param["chat_form_height"]."px;top:0px;left:0px;background-Color:".$param["chat_bg_color"].";clip:rect(0 ".$param["chat_form_width"]." ".$param["chat_form_height"]." 0);"."overflow: auto;";

$style3="position:absolute;width:".$param["chat_form_width"]."px;height:".$param["chat_form_height"]."px;top:".($param["chat_form_top"]+$param["chat_form_height"]+5)."px;left:".$param["chat_form_left"]."px;"."overflow: auto;";
$style4="padding-top:5px;padding-bottom:5px;font-size:15px;font-weight:bold;color:#ffffff;background-image:url(../images/fond_titre_table.jpg);";
//entete_simple($param["text_intro"]);
print ($param["text_intro"]);
if (isset($message))
 echo "<FONT SIZE=1>$message</FONT>";

?>

<DIV style="<?php print($style1); ?>">
        <DIV style="<?php print($style2); ?>">
                <DIV id="layermsg" style="position:absolute;top:0px;left:0px;">
                    <?php print($param["text_connecting"]); ?>
                </DIV>
        </DIV>
</DIV>

<DIV id="layerform" style="<?php print($style3); ?>">
<SCRIPT language=javascript>
function ChangeStyle(f) {
        f.msg.style.color=f.color.options[f.color.selectedIndex].value;
        f.msg.focus();
}
function AddText(text) {
        f=document.forms[0];
        f.msg.value=f.msg.value+text;
        f.msg.focus();
}
</SCRIPT>
<FORM name=post onSubmit="top.SendMsg(this); return false;">
         <TABLE border=0><TR><TD><SMALL><B><?php print($param["text_your_msg"]."</B></SMALL>"); ?><INPUT type=text name=msg  maxlength=<?php print($param["chat_msg_max_size"]); ?> style="width:<?php print($param["chat_form_width"]-80);?>px;background-Color:<?php print($param["chat_bg_color"]); ?> "></TD>
         <?php
         echo "<TD valign='bottom'><INPUT TYPE='IMAGE' NAME='SUBMIT' SRC='../images/fiche_identite/boutvalid$suffixer.gif'></TD></TR>";
         //<TD valign='bottom'>$bouton_gauche<A HREF=\"javascript:top.SendMsg(this); return false;\">$mess_gen_envoi</A>$bouton_droite</TD></TR>";
         echo "<TR><TD>";
                print("<SMALL><B>".$param["couleur"]."</B></SMALL> <SELECT name=color style='background-Color:".$param["chat_bg_color"].";color:".$param["chat_msg_color1"]."' onChange='ChangeStyle(this.form)'>");
                for ($i=1;$i<=$param["chat_msg_nb_colors"];$i++) {
                        print("<OPTION style='color:".$param["chat_msg_color".$i]."' value='".$param["chat_msg_color".$i]."'>".$param["chat_msg_color".$i]."</OPTION>");
                }
                print("        </SELECT></TD><TD align='center'>");

                print("  $bouton_gauche<A HREF=\"javascript:void(0);\" onclick='javascript:top.Quitter()' target=_top>".$param["quit"]."</A>$bouton_droite</TD></TR></TABLE>");
                if (isset($message) && strstr($message,'vous demande'))
                {
                    echo "<div style='display:block;' onClick='$(this).hide();'>
                         <audio controls autoplay><source src='".$_SESSION['monURI']."/images/config/Dring.mp3'  type='audio/mpeg'/></audio></div>";
                }

        ?>
</FORM>
</DIV>
</BODY></HTML>