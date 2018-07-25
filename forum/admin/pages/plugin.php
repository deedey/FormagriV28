<?php
/* Plugin Managment */

if(@$do == "props" && !empty($plugin)):
  $plugindirname = $plugin;
  $pluginprops = TRUE;
  include("./plugin/$plugin/admin.php");
else:
?>
<table border="1" cellspacing="0" cellpadding="3">
<tr>
<td align="center" valign="middle" bgcolor="#000080"><font face="Arial,Helvetica" color="#FFFFFF"><b>Manage Plugins</b></td>
</tr>
<tr>
<?php
  $dir = opendir("./plugin/");
  $num=0;
  while($plugindirname = readdir($dir)) {
    if($plugindirname[0] != ".") {
      if(@file_exists("./plugin/$plugindirname/plugin.php") && file_exists("./plugin/$plugindirname/admin.php")) {
         unset($pluginname); unset($plugindesc); unset($pluginversion);
        include("./plugin/$plugindirname/admin.php");
        if(isset($pluginname) && isset($plugindesc) && isset($pluginversion)) {
          echo("<tr><td align=\"left\" valign=\"middle\" bgcolor=\"#FFFFFF\"><font face=\"Arial,Helvetica\">");
          echo "<b>$pluginname</b> v$pluginversion";
          echo "<div align=\"right\"><a href=\"$myname?page=plugin&plugin=$plugindirname&do=deactivate\">On/Off</a> | <a href=\"$myname?page=plugin&plugin=$plugindirname&do=props\">Properties</a><br>\n";
          echo("</td></tr><tr><td align=\"left\" valign=\"middle\" bgcolor=\"#FFFFFF\"><font face=\"Arial,Helvetica\">");
          echo "$plugindesc";
          echo "</font>\n";
          echo "</td>\n";
          $num++;
        }
      }
    }
  }
  closedir($dir);
  if($num == 0) {
echo("<tr><td align=\"left\" valign=\"middle\" bgcolor=\"#FFFFFF\">
<font face=\"Arial,Helvetica\">");
    echo("There are no plugins with admin support installed.");
echo("</font>
</td>\n<tr>");
  }
?>
</tr>
</table>
<?php endif; ?>
