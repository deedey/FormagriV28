<?php
require "common.php";
require ('../admin.inc.php');

  if($ActiveForums==1){
    $sSQL="Select id, folder from $pho_main where active=1";
    $q->query($DB, $sSQL);
    $rec=$q->getrow();
    if($rec["folder"]==0){
      header("Location: $forum_url/$list_page.$ext?f=$rec[id]$GetVars");
      exit();
    }
  }

  $title = $lForumList;
  if(file_exists("$include_path/header_$ForumConfigSuffix.php")){
    include "$include_path/header_$ForumConfigSuffix.php";
  }
  else{
    include "$include_path/header.php";
  }
  if($f!=0){
    $level='';
    if($ForumParent!=0){
      $level="f=$ForumParent";
    }
    $table_width=$ForumTableWidth;
    $table_header_color=$ForumTableHeaderColor;
    $table_header_font_color=$ForumTableHeaderFontColor;
    $table_body_color_1=$ForumTableBodyColor1;
    $table_body_font_color_1=$ForumTableBodyFontColor1;
    $nav_color=$ForumNavColor;
    $nav_font_color=$ForumNavFontColor;
    $req_forum = mysql_query ("select name from forums where id =$f");
    $nom_forum = my_sql_result($req_forum,0,"name");
    echo "<CENTER><Font color=green><B>$mess_for_esp $nom_forum</B></FONT></CENTER>";
?>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <TD style='text-align:left;' <?php echo bgcolor($nav_color); ?> valign="TOP" nowrap><DIV id='sequence'><a href="<?php echo "$forum_page.$ext?$level$GetVars"; ?>"><font color="<?php echo $nav_font_color; ?>"><?php echo $lUpLevel; ?></font></a></font></div></td>
  </tr>
</table>
<?php
  }else{
    $table_width=$default_table_width;
    $table_header_color=$default_table_header_color;
    $table_header_font_color=$default_table_header_font_color;
    $table_body_color_1=$default_table_body_color_1;
    $table_body_font_color_1=$default_table_body_font_color_1;
    $nav_color=$default_nav_color;
    $nav_font_color=$default_nav_font_color;
  }
?>
<table width="100%" cellspacing="0" cellpadding="4" border="0">
<tr>
    <TD style='text-align:left;' colspan="2"><?php echo "<div id='aide' style=\"float:left;\">".
                    aide_div("forum_formateur",0,0,0,0); ?></td>
</tr>
<tr bgcolor='#2b677a' height="25">
    <TD style='text-align:left;' width="40%"><FONT color="<?php echo $table_header_font_color; ?>">&nbsp;<B><?php echo $lAvailableForums;?></B></font></td>
    <TD style='text-align:left;' width="60%"><FONT color="<?php echo $table_header_font_color; ?>">&nbsp;<B>Nombre de messages</B></font></td>
</tr>
<?php
  if(isset($q)){
    $sSQL="Select id, name, table_name, parent, folder, description from ".$pho_main." where active=1 and parent=$f";
    if ($arrive == "sequence" || $arrive == "activite")
      $sSQL .= " and description = \"Groupe:\"";
    if($SortForums) $sSQL.=" order by name";
    $q->query($DB, $sSQL);
    $rec=$q->getrow();
  } else {
    $rec = "";
  }
  if(is_array($rec)){
    while(is_array($rec)){
      $mon_cpt++;
      $empty=false;
      $name=$rec["name"];
      $num=$rec["id"];
      if(!$rec["folder"]){
        $sSQL="select count(*) as posts from $rec[table_name] where approved='Y'";
        $tq = new query($DB, $sSQL);
        if($tq->numrows()){
          $trec=$tq->getrow();
          $num_posts=$trec["posts"];
        }
        else{
          $num_posts='0';
        }
        $sSQL="select max(datestamp) as max_date from $rec[table_name] where approved='Y'";
        $tq->query($DB, $sSQL);
        $trec=$tq->getrow();
        if(empty($trec["max_date"])){
          $last_post_date="";
        }
        else{
          $last_post_date=dateFormat($trec["max_date"]);
        }
      }
      if ($name == 'Forum Libre')
         $etat_flib = GetDataField($connect,"select param_etat_lb from param_foad where param_typ_lb='forum_libre'","param_etat_lb");

      if (((($arrive == "sequence" || $arrive == "activite") && $num_posts > 0) || !isset($arrive)) && (($name == 'Forum Libre' && $etat_flib == 'OUI') || $name != 'Forum Libre')){
        echo couleur_tr($mon_cpt,30);
        echo "<TD style='text-align:left;' width=\"40%\">";
        echo "<div id='sequence'><FONT size=3>";
        if($rec["folder"]){
          echo "<b>&nbsp;<a href=\"$forum_page.$ext?f=$num$GetVars&collapse=$collapse&arrive=$arrive\">$name</a></b></font></div></td>";
          echo "<TD style='text-align:left;' width=\"60%\"><font size=-1 color=\"$table_body_font_color_1\">&nbsp;&nbsp;$lForumFolder</font></td></tr>";
        }else{
          echo "<b>&nbsp;<a href=\"$list_page.$ext?f=$num$GetVars&collapse=$collapse&arrive=$arrive\">$name</a></b></font></div></td>";
          echo "<TD style='text-align:left;' width=\"60%\"><font size=-1>&nbsp;&nbsp;$lNumPosts: <b>$num_posts</b>";
          if(!empty($trec["max_date"]))
            echo "&nbsp;&nbsp;&nbsp;&nbsp;$lLastPostDate: <b>$last_post_date</b></font>";
          echo "</td></tr>";
        }
        if ($rec["description"] != "" && $rec["description"] != "Groupe:"){
          echo  couleur_tr($mon_cpt,30);
          echo "<TD style='text-align:left;' colspan=2><font size=-1 color=\"$table_body_font_color_1\">&nbsp;";
          echo $rec["description"];
          echo "</font></td></tr>\n";
        }
      }
      $rec=$q->getrow();
    }
?>
<?php
  }
  else{
?>
<tr>
    <TD style='text-align:left;' width="100%" colspan=2 <?php echo bgcolor($table_body_color_1); ?>><FONT color="<?php echo $table_body_font_color_1; ?>">&nbsp;<?php echo $lNoActiveForums;?></font></td>
</tr>
<?php
  }
?>
</table>
<?php
  if(file_exists("$include_path/footer_$ForumConfigSuffix.php")){
    include "$include_path/footer_$ForumConfigSuffix.php";
  }
  else{
    include "$include_path/footer.php";
  }
?>
