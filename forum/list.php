<?php
require "./common.php";
if(!isset($arrive)) $arrive='';
$GetVars .="&arrive=$arrive";
//  if (isset($nb_posts) && $nb_posts > 0)
//      $acter = mysql_query("update forums set display = 60 where id=$f");
//$acter = mysql_query("update forums set display ='$nb_posts' where id=$f");
  $thread=$t;
  if (isset($al))
     $action=$al;
  if($num==0 || $ForumName==''){
    Header("Location: $forum_url?$GetVars");
    exit;
  }
  $phcollapse="phorum-collapse-$ForumTableName";
  $new_cookie="phorum-new-$ForumTableName";
  $haveread_cookie="phorum-haveread-$ForumTableName";
  if($UseCookies){
    if ($r==1) {
      $sSQL = "Select max(id) as max_id FROM $ForumTableName WHERE approved='Y'";
      $q->query($DB, $sSQL);
      $aryRow=$q->getrow();
      if(isset($aryRow['max_id'])){
        $max_id=$aryRow['max_id'];
        $$new_cookie=$max_id;
        SetCookie($new_cookie,$$new_cookie,time()+ 31536000);
        SetCookie($haveread_cookie,$$new_cookie);        //destroy session cookie
        unset($$haveread_cookie);
      }
    }

    if(!IsSet($$new_cookie)){
      $$new_cookie='0';
    }

    $use_haveread=false;
    if(IsSet($$haveread_cookie)) {
      $arr=explode(".", $$haveread_cookie);
      $old_message=reset($arr);
      array_walk($arr, "explode_haveread");
      $use_haveread=true;
    }
    else{
      $old_message=$$new_cookie;
    }

    if(IsSet($collapse)){
      $$phcollapse=$collapse;
      SetCookie("phorum-collapse-$ForumTableName",$collapse,time()+ 31536000);
    }
    elseif(!isset($$phcollapse)){
      $$phcollapse=$ForumCollapse;
    }

  }
  else{
    if(IsSet($collapse)){
      $$phcollapse=$collapse;
    }
    else{
      $$phcollapse=$ForumCollapse;
    }
  }
  if($DB->type=="sybase") {
    $limit="";
    $q->query($DB, "set rowcount $ForumDisplay");
  }
  elseif($DB->type=="postgresql"){
    $limit="";
    $q->query($DB, "set QUERY_LIMIT TO '$ForumDisplay'");
  }
  else{
   $sSQL = "select display as display from forums where id='$f'";
   $q->query($DB, $sSQL);
   if($q->numrows()>0){
     $rec=$q->getrow();
     $lmt = isset($rec["display"]) ? $rec["display"] : 0;
     $limit ="";
//      $limit =" limit $lmt";
    }else
      $limit ="";
//   $limit=" limit $ForumDisplay";
  }

  if($thread==0 || $action==0){
    $sSQL = "Select max(thread) as thread from $ForumTableName where approved='Y'";
    $q->query($DB, $sSQL);
    if($q->numrows()>0){
      $rec=$q->getrow();
      $maxthread = isset($rec["thread"]) ? $rec["thread"] : 0;
    }
    else{
      $maxthread=0;
    }
    if ($maxthread > $cutoff) {
      $cutoff_thread=$maxthread-$cutoff;
    } else {
      $cutoff_thread = 0;
    }
    if($$phcollapse==0){
      $sSQL = "Select thread from $ForumTableName where thread > $cutoff_thread and approved='Y' order by thread desc".$limit;
    }
    else{
      $sSQL = "Select thread, count(id) as tcount, max(datestamp) as latest, max(id) as maxid from $ForumTableName where approved='Y' AND thread > $cutoff_thread group by thread order by thread desc".$limit;
    }
  }else{
    if($action==1){
      $cutoff_thread=$thread+$cutoff;
      $sSQL = "Select thread from $ForumTableName where approved='Y' AND thread < $cutoff_thread AND thread > $thread order by thread".$limit;
      $q=new query($DB, $sSQL);
      $rec=$q->getrow();
      if(!empty($rec["thread"])){
        $keepgoing=true;
        $x=0;
        while (is_array($rec) && $keepgoing){
          $thread = $rec["thread"];
          $rec=$q->getrow();
          $x++;
        }
      }
      $thread=$thread+1;
    }
    if ($thread > $cutoff) {
      $cutoff_thread=$thread-$cutoff;
    } else {
      $cutoff_thread = 0;
    }
    if($$phcollapse==0){
      $sSQL = "Select thread from $ForumTableName where approved='Y' and thread < $thread and thread > $cutoff_thread order by thread desc".$limit;
    }
    else{
      $sSQL = "Select thread, COUNT(id) AS tcount, MAX(datestamp) AS latest, MAX(id) AS maxid FROM $ForumTableName WHERE approved='Y' AND thread < $thread AND thread > $cutoff_thread GROUP BY thread ORDER BY thread DESC".$limit;
    }
  }

  $thread_list = new query($DB, $sSQL);

  if($DB->type=="sybase") {
    $q->query($DB, "set rowcount 0");
  }
  elseif($DB->type=="postgresql"){
    $q->query($DB, "set QUERY_LIMIT TO '0'");
  }

  $rec=$thread_list->getrow();

  if(empty($rec["thread"]) && $action!=0){
    Header("Location: $list_page.$ext?f=$num$GetVars");
    exit();
  }

  if(isset($rec['thread'])){
    $max=$rec["thread"];
    $keepgoing=true;
    $x=0;
    while (is_array($rec)){
      $threads[]=$rec;
      $min=$rec["thread"];
      $rec=$thread_list->getrow();
    }
  }
  else{
    $threads="";
    $max=0;
    $min=0;
  }
if (isset($tout_lu) && $tout_lu == 1)
{
  $sqlfrm = mysql_query("select * from $ForumTableName where thread = $thread");
  $nb_sqlfrm = mysql_num_rows($sqlfrm);
  if ($nb_sqlfrm > 0)
  {
     while ($item = mysql_fetch_object($sqlfrm))
     {
         $reqFlect = mysql_result(mysql_query("select count(*) from forum_lecture where
                                               forlec_forum_no=$num and
                                               forlec_user_no=$id_user and
                                               forlec_topic_no=".$item->id),0);
         if ($reqFlect == 0)
         {
            $req_flag = mysql_query("INSERT INTO forum_lecture (forlec_topic_no,forlec_forum_no,forlec_user_no)
                                     VALUES ('".$item->id."','$f','$id_user')");
         }
     }
  }
}

  if($$phcollapse==0){
    $sSQL = "Select id,parent,thread,subject,author,datestamp from $ForumTableName where approved='Y' AND thread<=$max and thread>=$min order by thread desc, id asc";
  }else{
    $sSQL = "Select id,thread,subject,author,datestamp from $ForumTableName where approved='Y' AND
             thread = id AND thread<=$max AND thread>=$min order by thread desc";
  }

  $msg_list = new query($DB, $sSQL);

  $rec=$msg_list->getrow();
  while(is_array($rec)){
    $headers[]=$rec;
    $rec=$msg_list->getrow();
  }

  $rows=@count($headers);

  if(file_exists("$include_path/header_$ForumConfigSuffix.php")){
    include "$include_path/header_$ForumConfigSuffix.php";
  }
  else{
    include "$include_path/header.php";
  }

  if($$phcollapse==0){
    $collapse_link = "<div id='ouvre' style=\"float:left;padding-right:10px;\">".
                     "<a href=\"$list_page.$ext?f=$num&collapse=1$GetVars\" class='bouton_new'>$lCollapseThreads</A></div>";
  }
  else{
    $collapse_link = "<div id='ferme' style=\"float:left;padding-right:10px;\">".
                     "<a href=\"$list_page.$ext?f=$num&collapse=0$GetVars\" class='bouton_new'>$lViewThreads</A></div>";
  }
  $collapse_link .= "<div id='cherche' style=\"float:left;padding-right:10px;\">".
                    "<a href=\"search_avc.$ext?f=$num$GetVars\" class= 'bouton_new'>$lavSearch</a></div>";
  if ($f == 3 && $typ_user == 'APPRENANT')
     $collapse_link .= aide_div("forum_libre_apprenant",0,0,0,0);
  if ($f == 1 && $typ_user != 'APPRENANT')
     $collapse_link .= aide_div("forum_formateur",0,0,0,0);
  elseif ($f == 3 && $typ_user != 'APPRENANT')
     $collapse_link .= aide_div("forum_libre_formateur",0,0,0,0);
  elseif ($f > 5 && $typ_user == 'APPRENANT')
     $collapse_link .= aide_div("forum_apprenant",0,0,0,0);
  elseif (($f > 5 || $f == 0 ) && $typ_user != 'APPRENANT')
     $collapse_link .= aide_div("forum_formateur",0,0,0,0);
  if($ActiveForums > 1 && (!isset($arrive) || (isset($arrive) && $arrive !='activite')))
  {
    $nav = "<TABLE width=100% border='0' cellspacing='0' cellpadding='6'>".
          "<TR><TD align='center' nowrap valign='bottom'><div id='new_subject' style=\"float:left;padding-right:10px;\">".
          "<A HREF=\"$post_page.$ext?f=$num$GetVars\"".
          " class='bouton_new'>$lStartTopic</A></div>$collapse_link";

    echo $PostVars;
    if ($rows < $lmt)
      echo "</TR></TABLE>";
  }/*else
     $nav = "<font size=1><a href=\"$post_page.$ext?f=$num$GetVars\">".$lStartTopic."</a>".
            "&nbsp;&nbsp;|&nbsp;&nbsp;$collapse_link&nbsp;&nbsp;|&nbsp;&nbsp;".
            "<a href=\"$search_page.$ext?f=$num$GetVars\">".$lSearch."</font></a></font>";*/
  $pagenav="";
  if (empty($pagenav))
     $pagenav="&nbsp;";
  if($ActiveForums > 1 && (!isset($arrive) || (isset($arrive) && $arrive == '')))
  {// || (isset($arrive) && $arrive !='activite')))
     echo '<table width="100%" cellspacing="0" cellpadding="0" border="0" bgcolor="#FFFFFF">'.
          '<tr height="10"><td></td></tr>'.
          '<tr><td nowrap>'.$nav.'</td></tr>';
     echo "<tr><td align='left' nowrap valign='bottom'><form name='form1' action=\"$search_page.$ext\" method='GET'>".
          "<input type=\"Hidden\" name=\"f\" value=\"$num\">".
          "<input type=\"Hidden\" name=\"num\" value=\"$num\">".
          "<input type=\"Hidden\" name=\"match\" value=\"2\">".
          "<input type=\"Hidden\" name=\"date\" value=\"0\">".
          "<input type=\"Hidden\" name=\"fldauthor\" value=\"1\">".
          "<input type=\"Hidden\" name=\"fldsubject\" value=\"1\">".
          "<input type=\"Hidden\" name=\"fldbody\" value=\"1\">".
          "<input type=\"Text\" class='INPUT' style=\"float:left;\" id='search' name=\"search\" size=\"30\" value=\" $lSearch\" ".
          "onClick=\"javascript:var changer=getElementById('search');changer.value='';\">".
          "<A HREF=\"javascript:document.form1.submit();\" class='bouton_new' style=\"float:left;margin-left:5px;\">Ok</A></form></td>";
     echo "</tr></table>";
  }
  elseif($ActiveForums > 1 && isset($arrive) && $arrive != '')
  {
     echo '<table width="100%" cellspacing="0" cellpadding="0" border="0" bgcolor="#FFFFFF"><tr><td>';
  }
  if (!$ForumMultiLevel || $$phcollapse)
  {
    echo "</td></tr>";
    echo "<tr><td width=100% colspan='3'><TABLE width=100% bgcolor='#FFFFFF' cellspacing='0' cellpadding='0'><tr><td>";
    include "$include_path/threads.php";
    echo "</td></tr></table>";
  }
  else
  {
    echo "</TD></TR>";
    echo "<TR><TD width=100% colspan='3'><TABLE width=100% bgcolor='#FFFFFF' cellspacing='0' cellpadding='0'><TR><TD>";
    include "$include_path/multi-threads.php";
    echo "</TD></TR></TABLE>";
  }
  if (file_exists("$include_path/footer_$ForumConfigSuffix.php"))
     include "$include_path/footer_$ForumConfigSuffix.php";
  else
     include "$include_path/footer.php";

?>