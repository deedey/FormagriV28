<?php
$pluginname = "replace";
$plugindesc = "simple text replacment plugin";
$pluginversion = "0.1";
if(isset($pluginprops)) {
  if(isset($HTTP_POST_VARS["key1"])) {
    $num=1;
    $data="<?php\n\$pluginreplace = array();\n";
    while(isset(${"key".$num})) {
      if(!empty(${"key".$num}) && !empty(${"val".$num})) {
        $data .= "\$pluginreplace['".addslashes(stripslashes(${"key".$num}))."']='".addslashes(stripslashes(${"val".$num}))."';\n";
        $pluginreplace[${"key".$num}]=${"val".$num};
      }
      $num++;
    }
    $data .= "?>\n";
    if($fp = @fopen("./plugin/$plugindirname/settings.php","w")) {
      fputs($fp,$data);
      fclose($fp);
    } else {
      echo("Permission denied for writing \"plugin/$plugindirname/settings.php\", please check file permissions.\n<br>");
      echo("Optionally, you may issue the following command from the phorum directory:<br>\n");
      echo("<p><div align=\"left\">cat &lt;&lt;EOF &gt; plugin/$plugindirname/settings.php<br>\n".
           nl2br(htmlspecialchars(str_replace("\$","\\$",$data),ENT_QUOTES,'ISO-8859-1'))."EOF</div></p>\n");
    }
  } else {
    include("./plugin/replace/settings.php");
  }
echo("<table border=\"1\" cellspacing=\"0\" cellpadding=\"3\"><tr>");
echo("<td align=\"center\" valign=\"middle\" bgcolor=\"#000080\" colspan=\"2\"><font face=\"Arial,Helvetica\" color=\"#FFFFFF\"><b>Manage Plugins - $pluginname</b></td></tr>");
echo("<form action=\"$PHP_SELF?$QUERY_STRING\" method=\"POST\">\n");

$num=1;
while(list($key,$val) = each($pluginreplace)) {
  echo("<tr><td align=\"right\" valign=\"middle\" bgcolor=\"#FFFFFF\"><font face=\"Arial,Helvetica\">");

  echo("<b>$num</b>. Replace <INPUT TYPE=\"text\" NAME=\"key$num\" VALUE=\"".htmlspecialchars($key,ENT_QUOTES,'ISO-8859-1')."\">");
  echo("</font></td><td align=\"right\" valign=\"middle\" bgcolor=\"#FFFFFF\"><font face=\"Arial,Helvetica\">");
  echo(" with <INPUT TYPE=\"text\" NAME=\"val$num\" VALUE=\"".htmlspecialchars($val,ENT_QUOTES,'ISO-8859-1')."\"><br>\n");
  echo("</font></td></tr>");
  $num++;
}

$i=$num+5;
while($i>$num) {
  echo("<tr><td align=\"right\" valign=\"middle\" bgcolor=\"#FFFFFF\"><font face=\"Arial,Helvetica\">");
  echo("<b>New</b>. Replace <INPUT TYPE=\"text\" NAME=\"key$num\">");
  echo("</font></td><td align=\"right\" valign=\"middle\" bgcolor=\"#FFFFFF\"><font face=\"Arial,Helvetica\">");
  echo("with <INPUT TYPE=\"text\" NAME=\"val$num\"><br>\n");
  echo("</font></td></tr>");
  $num++;
}

  echo("<tr><td align=\"center\" bgcolor=\"#FFFFFF\" colspan=\"2\"><font face=\"Arial,Helvetica\">");

echo("<input type=\"submit\" value=\" Apply \">\n");
echo("</form>\n</td></tr>");
echo("</tr>\n</table>");

}
?>
