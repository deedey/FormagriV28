<?php /* Easy Admin */
// Set up variables
  $t_gif="<IMG SRC=\"$forum_url/images/t.gif\" WIDTH=12 HEIGHT=21 BORDER=0>";
  $l_gif="<IMG SRC=\"$forum_url/images/l.gif\" WIDTH=12 HEIGHT=21 BORDER=0>";
  $p_gif="<IMG SRC=\"$forum_url/images/p.gif\" WIDTH=9 HEIGHT=21 BORDER=0>";
  $m_gif="<IMG SRC=\"$forum_url/images/m.gif\" WIDTH=9 HEIGHT=21 BORDER=0>";
  $c_gif="<IMG SRC=\"$forum_url/images/c.gif\" WIDTH=9 HEIGHT=21 BORDER=0>";
  $i_gif="<IMG SRC=\"$forum_url/images/i.gif\" WIDTH=12 HEIGHT=21 BORDER=0>";
  $n_gif="<IMG SRC=\"$forum_url/images/n.gif\" WIDTH=9 HEIGHT=21 BORDER=0>";
  $space_gif="<IMG SRC=\"$forum_url/images/trans.gif\" WIDTH=5 HEIGHT=21 BORDER=0>";
  $trans_gif="<IMG SRC=\"$forum_url/images/trans.gif\" WIDTH=12 HEIGHT=21 BORDER=0>";

  $cutoff = 800; // See the faq.

  if (isset($mythread)) $thread = $mythread;

  if (!isset($navigate)) $navigate = 0;


// Declare functions

  function echo_data($image, $topic, $row_color){
    if (!isset($_SESSION)) session_start();
    GLOBAL $read_page,$ext,$id,$myname,$max, $forum_url,$lg;
    GLOBAL $space_gif,$num,$old_message,$navigate;
    require ("../admin.inc.php");
    require ("../lang$lg.inc.php");
    $thread_total="";

    if(($row_color%2)==0)
    {
      $bgcolor="#F4F4F4";
      $font_color="#000000";
    }
    else{
      $bgcolor="#FFFFFF";
      $font_color="#000000";
    }

    $subject ="<TABLE CELLSPACING=0 CELLPADDING=0 BORDER=0";
    if($bgcolor!=""){
        $subject.=" BGCOLOR=\"".$bgcolor."\"";
    }
    $subject.=">\n";
    $subject.="<TR>\n<TD>";
    $subject.=$space_gif;
    $subject.=$image."</TD>\n<TD><DIV id='sequence'>&nbsp;";
    $subject.="<a href=\"../$read_page.$ext?admview=1&f=$num&i=".$topic["id"];
    $subject.="&t=".$topic["thread"]."\">".$topic["subject"]."</a>";
    $author = $topic["author"];
    $approved = $topic["approved"];
    $datestamp = dateFormat($topic["datestamp"]);
    $subject.="&nbsp;&nbsp;";
    $subject.="</DIV></TD>\n</TR>\n</TABLE>";
    $tt=$topic["thread"];
    $verif_ress = mysql_query("SELECT count(ress_cdn) FROM ressource_new WHERE ress_url_lb like '%f=$num%' AND ress_url_lb like '%&t=$tt'");
    $nb_ress = mysql_result($verif_ress,0);
    $bouton_gauche = "<TABLE cellpadding='0' cellspacing='0' border=0><TR><TD><IMG SRC='../images/cg.gif' border='0'></TD><TD background='../images/milieu.gif' nowrap align='center'><DIV id='sequence'>&nbsp;";
    $bouton_droite = "&nbsp;</DIV></TD><TD><IMG SRC='../images/cd.gif' border='0'></TD><TR></TABLE>";
    echo "<TR VALIGN=middle><TD bgcolor=$bgcolor>$subject</TD>".
         "<TD bgcolor=$bgcolor nowrap><FONT face='Arial,Helvetica' COLOR='$font_color'>$author</FONT></TD>".
         "<TD bgcolor=$bgcolor nowrap><FONT face='Arial,Helvetica' SIZE='-2' COLOR='$font_color'>$datestamp&nbsp;</FONT></TD>";
    if ($nb_ress == 0)
    {
            echo "<TD align='left' nowrap  bgcolor=$bgcolor><A href=\"index.php?page=easyadmin&action=del&type=quick&id=".$topic['id']."&num=$num&navigate=$navigate&thread=$max\" class='sequence'>";
            echo "$mess_ag_supp</A></TD>";
    }
    if($nb_ress > 0)
    {// && $topic["id"] == $topic["thread"]){
             echo "<TD bgcolor=$bgcolor ><FONT face='Arial,Helvetica' COLOR='$font_color'><small>$messFfil</small></FONT></TD>";
    }
    echo "<TD align='left' nowrap bgcolor=$bgcolor>".
         "<A href=\"index.php?page=edit&srcpage=easyadmin&id=".$topic["id"]."&num=$num&navigate=$navigate&mythread=$max\" class='sequence'>".
         "$mess_modif_base</A></TD>";
    if($nb_ress > 0)
    {// && $topic["id"] == $topic["thread"]){
        echo "<TD bgcolor=$bgcolor></TD>";
    }
    else
    {
        echo "<TD align='left' nowrap bgcolor=$bgcolor><A href=\"index.php?page=easyadmin&action=moderate&approved=$approved&id=".$topic["id"]."&num=$num&navigate=$navigate&mythread=$max\" class='sequence'>";
        if ($approved == 'Y')
        {
            echo "$messFmasq";
        }
        else
        {
            echo "$messFdmasq";
        }
        echo "</A></TD>";
    }
    echo "</TR>";
  }
  function thread($seed=0){
    GLOBAL $row_color_cnt;
    GLOBAL $messages,$threadtotal;
    GLOBAL $font_color, $bgcolor;
    GLOBAL $t_gif,$l_gif,$p_gif,$m_gif,$c_gif,$i_gif,$n_gif,$trans_gif;
    $image="";
    $images="";
    if(!IsSet($row_color_cnt)){
      $row_color_cnt=0;
    }
    $row_color_cnt++;
    if($seed!="0"){
      $parent=$messages[$seed]["parent"];
      if($parent!=0){
        if(!IsSet($messages[$parent]["images"])){
          $messages[$parent]["images"]="";
        }
        $image=$messages[$parent]["images"];
        if($messages[$parent]["max"]==$messages[$seed]["id"]){
          $image.=$l_gif;
        }
        else{
          $image.=$t_gif;
        }
      }

      if(@is_array($messages[$seed]["replies"])){
        if(IsSet($messages[$parent]["images"])){
          $messages[$seed]["images"]=$messages[$parent]["images"];
          if($seed==$messages["$parent"]["max"]){
            $messages[$seed]["images"].=$trans_gif;
          }
          else{
            $messages[$seed]["images"].=$i_gif;
          }
        }
        $image.=$m_gif;
      }
      else{
        if($messages[$seed]["parent"]!=0){
          $image.=$c_gif;
        }
        else{
          if($threadtotal[$messages[$seed]["thread"]]>1){
            $image.=$p_gif;
          }
          else{
            $image.=$n_gif;
          }
        }
      }
      echo_data($image, $messages[$seed], $row_color_cnt);
    }//end of: if($seed!="0")
    if(@is_array($messages[$seed]["replies"])){
      $count=count($messages[$seed]["replies"]);
      for($x=1;$x<=$count;$x++){
        $key=key($messages[$seed]["replies"]);
        thread($key);
        next($messages[$seed]["replies"]);
      }
    }
  }

// Begin main()
  if($DB->type=="sybase") {
    $limit="";
    $q->query($DB, "set rowcount $ForumDisplay");
  }
  elseif($DB->type=="postgresql"){
    $limit="";
    $q->query($DB, "set QUERY_LIMIT TO '$ForumDisplay'");
  }
  else{
    $limit="";// limit $ForumDisplay";
  }
  if($thread==0 || $navigate==0){
    $sSQL = "Select max(thread) as thread from $ForumTableName";
    $q->query($DB, $sSQL);
    if($q->numrows()>1){
      $rec=$q->getrow();
      $maxthread=$rec["thread"];
    }
    else{
      $maxthread=0;
    }
    $cutoff_thread=$maxthread-$cutoff;
    $sSQL = "Select thread from $ForumTableName where thread > $cutoff_thread order by thread desc".$limit;
  }
  else{
    if($navigate==1){
      $cutoff_thread=$thread+$cutoff;
      $sSQL = "Select thread from $ForumTableName where thread < $cutoff_thread and thread > $thread order by thread".$limit;
      $q=new query($DB, $sSQL);
      if($rows=$q->numrows()){
        $rec=$q->getrow();
               while (is_array($rec)){
          $thread = $rec["thread"];
          $rec=$q->getrow();
        }
      }
      $thread=$thread+1;
    }
    $cutoff_thread=$thread-$cutoff;
    $sSQL = "Select thread from $ForumTableName where thread < $thread and thread > $cutoff_thread order by thread desc".$limit;
  }
  $thread_list = new query($DB, $sSQL);
  if($DB->type=="sybase") {
    $limit="";
    $q->query($DB, "set rowcount 0");
  }
  elseif($DB->type=="postgresql"){
    $q->query($DB, "set QUERY_LIMIT TO '0'");
  }
  $rows = $thread_list->numrows();
// This needs to be fixed...
//  if($rows==0 && $navigate!=0){
//    Header("Location: $list_page.$ext?num=$num$GetVars");
//    exit();
//  }
  $rec=$thread_list->getrow();
  if(isset($rec['thread'])){
    $max=$rec["thread"];
          while (is_array($rec)){
      $min=$rec["thread"];
      $rec=$thread_list->getrow();
    }
  }
  else{
    $max=0;
    $min=0;
  }
  $sSQL = "Select id,parent,thread,subject,author,datestamp,approved from $ForumTableName where thread<=$max and thread>=$min order by thread desc, id asc";
  $msg_list = new query($DB, $sSQL);
  $row=$msg_list->firstrow();
  if(is_array($row)){
    if(!$read){
      $rec=$thread_list->firstrow();
      while(is_array($rec)){
        $thd=$rec["thread"];
        if(!isset($rec["tcount"])) $rec["tcount"]=0;
        $tcount=$rec["tcount"];
        $threadtotal[$thd]=$tcount;
        $rec=$thread_list->getrow();
      }
    }
    else{
      $threadtotal[$thread]=$msg_list->numrows();
    }
    $topics["max"]="0";
    $topics["min"]="0";
    While(is_array($row)){
      $x="".$row["id"]."";
      $p="".$row["parent"]."";
      $messages["$x"]=$row;
      $messages["$p"]["replies"]["$x"]="$x";
      $messages["$p"]["max"]=$row["id"];
      if(!isset($messages["max"])) $messages["max"]=0;
      if(!isset($messages["min"])) $messages["min"]=0;
      if($messages["max"]<$row["thread"]) $messages["max"]=$row["thread"];
      if($messages["min"]>$row["thread"]) $messages["min"]=$row["thread"];
      $row=$msg_list->getrow();
    }
  }

$sSQL="Select name from forums where id=$num";
$q->query($DB, $sSQL);
$row=$q->getrow();
$nom_grp = $row["name"];
 $le_titre= "<CENTER><FONT COLOR='white' size='3'><B>$mess_gest_forum_form : $nom_grp</B></FONT>";
// $le_titre .= "<BR><font color=white size='2'>$mess_mod_forum</FONT>";
 $lien = "../list.php?f=$num&collapse=0";
 echo "<TABLE bgColor='#298CA0' cellspacing='2' cellpadding='0' width='98%' ><TR><TD width='100%'>";
 echo "<TABLE bgColor='#FFFFFF' cellspacing='1' cellpadding='0' width='100%'>";
 echo "<TR><TD background=\"$adresse_http/images/fond_titre_table.jpg\" height='36' align='center' valign='center'>$le_titre</TD></TR>";
 echo"<TR><TD style=\"padding-left: 4px; height: 26px;\"><A href=\"$lien\" class='bouton_new'><font size='2'>$mess_consult_ceforum<FONT></A>";
?>
</TD></TR><TR>
<TD>
<TABLE WIDTH="100%" CELLSPACING=0 CELLPADDING=0 BORDER=0>
<TR bgcolor="#336666">
    <TD HEIGHT=21><FONT face="Arial,Helvetica" COLOR="#FFFFFF">&nbsp;<B><?php echo $lTopics;?></B></FONT></TD>
    <TD HEIGHT=21 NOWRAP WIDTH=150><FONT face="Arial,Helvetica" COLOR="#FFFFFF"><B><?php echo $lAuthor;?></B>&nbsp;</FONT></TD>
    <TD HEIGHT=21 NOWRAP WIDTH=40><FONT face="Arial,Helvetica" COLOR="#FFFFFF"><B><?php echo $lDate;?></B></FONT></TD>
    <TD HEIGHT=21 NOWRAP colspan=3><FONT face="Arial,Helvetica" COLOR="#FFFFFF"><B><?php echo $messAction;?></B></FONT></TD>
</TR>
<?php
  thread();
echo '</TABLE></TD></TR></TABLE></TD></TR></TABLE>
</BODY></HTML>';
?>
