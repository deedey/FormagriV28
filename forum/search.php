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
          while(preg_match('/-*"[^"]*"/', $search_string, $match)){
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
  if($ActiveForums>1){
    $nav = "<div class=nav><a href=\"$post_page.$ext?f=$num$GetVars\" class='bouton_new' style=\"float: left;margin-right: 30px;\">".$lStartTopic."</font></a><a href=\"$list_page.$ext?f=$num$GetVars\" class='bouton_new' style=\"float: left;margin-right: 30px;\">".$lGoToTop."</font></a></font></div>";
  }
  else{
    $nav = "<div class=nav><a href=\"$post_page.$ext?f=$num$GetVars\" class='bouton_new' style=\"float: left;margin-right: 30px;\">".$lStartTopic."</font></a><a href=\"$list_page.$ext?f=$num$GetVars\" class='bouton_new' style=\"float: left;margin-right: 30px;\">".$lGoToTop."</font></a></font></div>";
  }
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

  echo '<table width="'.$ForumTableWidth.'" border="0" cellspacing="0" cellpadding="3" align="left">'.
       '<tr><td bgcolor='.$ForumNavColor.' valign="TOP" nowrap><font color="$ForumNavFontColor">';
  echo "<div id='acceuil' style=\"float:left;margin-left:2px;margin-right:10px;\">".
         "<a href=\"$list_page.$ext?f=$num\" class= 'bouton_new'>$mess_acc</a>".
         "</div>";
  echo "<div id='aide' style=\"float:left;\">".
       "<a href=\"../../aide/forum.html\" class= 'bouton_new' target='_blank'>$mess_menu_aide</a>".
       "</div></font></td></tr></table>";
  if(@is_array($terms)){
    echo '<div style="clear:both;"><table width="'.$ForumTableWidth.'" cellspacing="0" cellpadding="4" border="0">'.
         '<tr><td '.bgcolor($ForumTableHeaderColor).' valign="TOP" nowrap><font color="'.$ForumTableHeaderFontColor.'">'.
         '&nbsp;<B>'.$lSearchResults.' : '.$totalFound.'</B></font></td>'.
         '</tr><tr><td width="'.$ForumTableWidth.'" valign="TOP" >'.
         '<font color="'.$ForumTableBodyFontColor2.'">';
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
        echo "<font size=-2>$lDate: ".dateFormat($message["datestamp"])."</font><br>\n";
        echo $text."<br>";
        echo "</dl><p>\n";
        $last_id=$message["id"];
        $message=next($messages);
      }
    }
    else{
      echo $lNoMatches;
    }
    echo "</font></td></tr></table></div><BR>";
  }
  if(file_exists("$include_path/footer_$ForumConfigSuffix.php")){
    include "$include_path/footer_$ForumConfigSuffix.php";
  }
  else{
    include "$include_path/footer.php";
  }
?>