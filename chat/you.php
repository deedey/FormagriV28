<?php

  if (!isset($_SESSION)) session_start();

  include "param.php";

  include "f_chat.php";

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
<TITLE><?php print($param["title"]); ?></TITLE>

<SCRIPT language=javascript>

var H=new Object;

<?php

print(" var d=new Date();");

print(" d.setYear(".date("Y").");");

print(" d.setMonth(".date("m").");");

print(" d.setDate(".date("d").");");

print(" d.setHours(".date("H").");");

print(" d.setMinutes(".date("i").");");

print(" d.setSeconds(".date("s").");");

?>

var local=new Date();

var delta=d.getTime()-local.getTime();

function AffHorloge() {

        d=new Date();

        d.setSeconds(d.getSeconds()+delta/1000);

        var heure=d.getHours();

        var min=d.getMinutes();

        var sec=d.getSeconds();

        if (heure<10) heure="0"+heure;

        if (min<10) min="0"+min;

        if (sec<10) sec="0"+sec;

        contenu="<SPAN style='"+H.CSS+"'>"+heure+":"+min+":"+sec+"</SPAN>";



        if (document.layers) {

                document.layers["horloge"].document.write(contenu);

                document.layers["horloge"].document.close();

        }

        if (document.all) {horloge.innerHTML=contenu;}

        setTimeout("AffHorloge()",1000);

}



function InitHorloge(X,Y,CSS) {

        H.CSS=CSS;H.X=X;H.Y=Y;

        if (document.all) {

                document.write("<DIV id='horloge' style='position:absolute;top:"+Y+";left:"+X+";visibility:show'></DIV>");

                AffHorloge();

        }

        if (document.layers) {

                document.write("<LAYER name='horloge' top='"+Y+"' left='"+X+"' visibility='show'></LAYER>");

                setTimeout('AffHorloge()',200);

        }

}

</SCRIPT>

</HEAD>

<BODY>

<BR>
<?php
if (strstr($_SERVER['HTTP_USER_AGENT'],"MSIE")){
  print ($param["text_server"])."<BR>";
  ?>
   <SCRIPT language="javascript">

        InitHorloge(60,17,"<?php print($param["chat_style_hour"]); ?>");

   </SCRIPT>
   <?php
}
print($param["text_user"]); ?> <?php print($user); ?>
<BR>
<A HREF="#" onclick= window.open('messages.php','','scrollbars,resizable=yes,width=500,height=600')><small><?php print($param["voir_msg"]); ?></small></A><BR>


</BODY></HTML>
