<?php
  require "./common.php";
  if($num==0 || $ForumName==''){
    Header("Location: $forum_url/$forum_page.$ext?$GetVars");
    exit;
  }

 /////////////////////////////////////////////////////////////////
  // build the search terms array
  // this will build the array to pass to build_sql()

  function build_search_terms($search, $match) {
    $terms=array();

    // if this is an exact phrase match
    if($match==3){
      $terms[]=$search;
    }
    // not exact phrase, break up the terms
    else{
      if ( strstr( $search, '"' ) ){
        //first pull out all the double quoted strings
        if(strstr($search, "\"")){
          $search_string=$search;
          while(preg_match('-*"[^"]*"', $search_string, $match)){
            $terms[]=trim(str_replace("\"", "", $match[0]));
            $search_string=substr(strstr($search_string, $match[0]), strlen($match[0]));
          }
        }
        $search = preg_replace('/-*"[^"]*"/', '', $search );
      }

      //pull out the rest words in the string
      $regular_terms = explode( " ", $search);

      //merge them all together and return
      while (list ($key, $val) = each ($regular_terms)) {
        if($val!="")
          $terms[]=trim($val);
      }
    }
    return $terms;
  }

  /////////////////////////////////////////////////////////////////
  // build the sql statement's where clause
  // this will build the sql based on the given information

  function  build_terms_clause($terms, $date, $fields, $match){
    static $where_clause;
    if(empty($where_clause)){
      if($date!=0){
        $cutoff=date("Y-m-d", mktime(0,0,0,date("m"),date("d")-$date));
        $where_clause .= " datestamp >= '$cutoff' AND ";
      }
      while (list ($junk, $term) = each ($terms)) {
        $cmpfunc="LIKE";
        if(substr($term, 0, 1)=="-"){
          $term=substr($term, 1);
          $cmpfunc="NOT LIKE";
        }
        reset($fields);
        unset($likeArray);
        while (list ($key, $val) = each ($fields)) {
          $likeArray[]=" $val $cmpfunc '%$term%' ";
        }
        $termArray[] = " (".implode( $likeArray, " OR " ).") ";
      }

      $cmptype="AND";
      if($match!=1) $cmptype="OR";
      $where_clause.= " (".implode( $termArray, " $cmptype " ).") ";

      $where_clause.="order by datestamp desc";
    }

    return $where_clause;

  }
  /////////////////////////////////////////////////////////////////
  // build the sql statement
  // this will build the sql based on the given information
  function  build_sql($table_name, $terms, $date, $fields, $match){
    GLOBAL $ForumTableName;
    $SQL = "select $table_name.id, $table_name.thread, author, subject, datestamp, body from $table_name, $table_name"."_bodies where $table_name.id = $table_name"."_bodies.id and $table_name.approved='Y' AND";
    $SQL.=build_terms_clause($terms, $date, $fields, $match);
    return $SQL;
  }
  if (!isset($search)){
    $search="";
  }
  $search=trim(stripslashes($search));
  $searchtext = $search;
  $searchtext = htmlentities($searchtext,ENT_QUOTES,'ISO-8859-1');
  if(!isset($fldauthor) && !isset($fldsubject) && !isset($fldbody)){
    $fields[] = "subject";
    $fields[] = "body";
    $fldauthor=0;
    $fldsubject=1;
    $fldbody=1;
  }
  else{
    empty($fldauthor) ? $fldauthor=0 : $fields[] = "author";
    empty($fldsubject) ? $fldsubject=0 : $fields[] = "subject";
    empty($fldbody) ? $fldbody=0 : $fields[] = "body";
  }

  initvar("date", 30);
  initvar("match", 1);
  initvar("start_num", 0);
/*
  if($ActiveForums>1){
    $nav = "<div class=nav><a href=\"$post_page.$ext?f=$num$GetVars\">".$lStartTopic."</font></a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href=\"$list_page.$ext?f=$num$GetVars\">".$lGoToTop."</font></a>&nbsp;</font></div>";
  }
  else{
    $nav = "<div class=nav><a href=\"$post_page.$ext?f=$num$GetVars\">".$lStartTopic."</font></a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href=\"$list_page.$ext?f=$num$GetVars\">".$lGoToTop."</font></a>&nbsp;</font></div>";
  }
*/
  if($search!=""){
    $terms = build_search_terms($search, $match);
    if(count($terms)>0){
      if(isset($x)){
        list($action,$start_num)=explode(",", $x);
      }

      $SQL=build_sql($ForumTableName, $terms, $date, $fields, $match);

//      echo "\n<!-- $SQL -->\n";

      $q->query($DB, $SQL);

      if($err=$q->error()){
        echo $err;
      }
      else{
        $totalFound=$q->numrows();
        $q->seek($start_num);
        $message = $q->getrow();
        $rowcount=0;
        while(is_array($message)){
          $rowcount++;
          $messages[]=$message;
          $message = $q->getrow();
        }
        $q->free();
      }
      $rows = @count($messages);
    }

  }

  $sTitle=" ".strtolower($lSearch);

  if(file_exists("$include_path/header_$ForumConfigSuffix.php")){
    include "$include_path/header_$ForumConfigSuffix.php";
  }
  else{
    include "$include_path/header.php";
  }
?>
<table width="<?php echo $ForumTableWidth; ?>" border="0" cellspacing="0" cellpadding="3">
  <tr>

    <td <?php echo bgcolor($ForumNavColor); ?> valign="bottom" nowrap><font color="<?php echo $ForumNavFontColor; ?>">
<?php
    echo "<div id='home' style=\"float:left;margin-left:2px;margin-right:10px;height:25px;padding-top:5px;\">".
         "<a href=\"$list_page.$ext?f=$num\" class= 'bouton_new'>$mess_acc</a>".
         "</div>";
    echo "<div id='aide' style=\"float:left;height:25px;padding-top:5px;\">".
         "<a href=\"http://wikiformagri.educagri.fr/doku.php?id=recherche_avancee\" class= 'bouton_new' target='_blank'>$mess_menu_aide</a>".
         "</div></td></tr></table>";

  if(@is_array($terms)){

?>
<table width="<?php echo $ForumTableWidth; ?>" border="0" cellspacing="0" cellpadding="3">
  <tr>

    <td <?php echo bgcolor($ForumNavColor); ?> valign="TOP" nowrap><font color="<?php echo $ForumNavFontColor; ?>">
<?php
?>

<table width="<?php echo $ForumTableWidth; ?>" cellspacing="0" cellpadding="4" border="0">
  <tr>
   <td <?php echo bgcolor($ForumTableHeaderColor); ?> valign="TOP" nowrap><font color="<?php echo $ForumTableHeaderFontColor; ?>">&nbsp;<B><?php echo "$lSearchResults : $totalFound";?></B></font></td>
  </tr>
<tr><td width="<?php echo $ForumTableWidth; ?>" valign="TOP"><font color="<?php echo $ForumTableBodyFontColor2; ?>">
<?php
    if($rows>0){
      $message=current($messages);
      $count=$start_num;
      While(is_array($message)){
        $count=$count+1;
        if(!isset($top_id)){
          $top_id=$message["id"];
        }
        if (($count/2) == ceil($count/2))
           echo "<dl  style=\"background-color:'#ffffff'\">";
        else
           echo "<dl style=\"background-color:'#F4F4F4';\">";
        echo "<dt><b>$count. </b><a href=\"$read_page.$ext?f=$num&i=".$message["id"]."&t=".$message["thread"]."$GetVars\"><b>".chop($message["subject"])."</b></a> - ".chop($message["author"])."<br>\n<dd>";
        $text=chop(substr($message["body"], 0, 200));
        if(function_exists("strip_tags")){
          $text=strip_tags($text);
        }
        else{
          $text=preg_replace("/<[^>]*>/", "", $text);
          $text=preg_replace("/^<[^>]*>/", "", $text);
          $text=preg_replace("/<[^>]*>$/", "", $text);
        }
        echo $text."<br>";
        echo "<font size=-2>$lDate: ".dateFormat($message["datestamp"])."</font><br>\n";
        echo "</dl><p>\n";
        $last_id=$message["id"];
        $message=next($messages);
      }
    }
    else{
      echo $lNoMatches;
    }
?>
</font>
</td></tr></table>
<BR>
<?php
 }
  if(@is_array($terms)){}else{
?>
<table  width="<?php echo $ForumTableWidth; ?>" border="0" cellspacing="0" cellpadding="2">
  <tr>
    <td <?php echo bgcolor($ForumTableHeaderColor); ?> valign="TOP" nowrap><font color="<?php echo $ForumTableHeaderFontColor; ?>">&nbsp;<B><?php echo $lSearch;?></B></font></td>
  </tr>
  <tr>
    <td align="CENTER" valign="MIDDLE">
    <br>
<table cellspacing="0" cellpadding="2" border="0">
<form action="<?php echo "search_avc.$ext"; ?>" method="GET">
<?php echo $PostVars; ?>
<input type="Hidden" name="f" value="<?php echo $num; ?>">
<input type="Hidden" name="num" value="<?php echo $num; ?>">
<tr>
    <td></td>
    <td><input type="Text" name="search" size="30" value="<?php echo $searchtext; ?>">
    &nbsp;<input type="Submit" value="<?php echo $mess_gen_cherche;?>">&nbsp;&nbsp;</td>
</tr>
<tr>
    <td align="right">&nbsp;</td>
    <td><font color="<?php echo $ForumTableBodyFontColor2; ?>"><select name="match"><option value="1" <?php if($match==1) echo "selected"; ?>><?php echo $lSearchAllWords ?></option><option value="2" <?php if($match==2) echo "selected"; ?>><?php echo $lSearchAnyWords ?></option><option value="3" <?php if($match==3) echo "selected"; ?>><?php echo $lSearchPhrase ?></option></select>&nbsp;&nbsp;&nbsp;&nbsp;<select name="date"><option value="30" <?php if($date==30) echo "selected"; ?>><?php echo $lSearchLast30; ?></option><option value="60" <?php if($date==60) echo "selected"; ?>><?php echo $lSearchLast60; ?></option><option value="90" <?php if($date==90) echo "selected"; ?>><?php echo $lSearchLast90; ?></option><option value="180" <?php if($date==180) echo "selected"; ?>><?php echo $lSearchLast180; ?></option><option value="0" <?php if($date==0) echo "selected"; ?>><?php echo $lSearchAllDates; ?></option></select></font></td>
</tr>
<tr>
    <td align="right">&nbsp;</td>
    <td><font color="<?php echo $ForumTableBodyFontColor2; ?>"><input type="checkbox" name="fldauthor" value="1" <?php if($fldauthor==1)  echo "checked"; ?>> <?php echo $lAuthor; ?>&nbsp;&nbsp;&nbsp;<input type="checkbox" name="fldsubject" value="1" <?php if($fldsubject==1)  echo "checked"; ?>> <?php echo $lFormSubject; ?>&nbsp;&nbsp;&nbsp;<input type="checkbox" name="fldbody" value="1" <?php if($fldbody==1)  echo "checked"; ?>> <?php echo $lMessageBodies; ?>&nbsp;&nbsp;&nbsp;</font></td>
</tr>
</table>
</font><br></td>
</td>
</tr>
</table>
</form>
<?php
}
/*
<p>
<table width="<?php echo $ForumTableWidth; ?>" border="0" cellspacing="0" cellpadding="2">
  <tr>
    <td <?php echo bgcolor($ForumTableHeaderColor); ?> valign="TOP" nowrap><font color="<?php echo $ForumTableHeaderFontColor; ?>">&nbsp;<B><?php echo $lSearchTips;?></B></font></td>
  </tr>
  <tr>
    <td width="<?php echo $ForumTableWidth; ?>" align="LEFT" valign="MIDDLE" <?php echo bgcolor($ForumTableBodyColor2); ?>><font color="<?php echo $ForumTableBodyFontColor2; ?>"><?php echo $lTheSearchTips;?><br></font></td>
  </tr>
</table>
*/
  if(file_exists("$include_path/footer_$ForumConfigSuffix.php")){
    include "$include_path/footer_$ForumConfigSuffix.php";
  }
  else{
    include "$include_path/footer.php";
  }
?>