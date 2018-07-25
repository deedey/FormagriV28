<TABLE width=<?php echo $ForumTableWidth;?> cellspacing=0 cellpadding=3 border=0>
<TR>
    <TD HEIGHT=21 bgcolor="#000080"><FONT face="Arial,Helvetica" COLOR="#FFFFFF">&nbsp;<?php echo $lTopics;?></FONT></TD>
    <TD HEIGHT=21 bgcolor="#000080" NOWRAP WIDTH=150><FONT face="Arial,Helvetica" COLOR="#FFFFFF"><?php echo $lAuthor;?>&nbsp;</FONT></TD>
    <TD HEIGHT=21 bgcolor="#000080" NOWRAP WIDTH=40><FONT face="Arial,Helvetica" COLOR="#FFFFFF"><?php echo $lDate;?></FONT></TD>
    <TD HEIGHT=21 bgcolor="#000080" NOWRAP WIDTH=40><FONT face="Arial,Helvetica" COLOR="#FFFFFF">Actions</FONT></TD>
</TR>

<?php
require "./common.php";
$nav = '';
if (!isset($navigate) || empty($navigate)) $navigate = 0;

if (isset($q)) {
  $sSQL="SELECT id, name, table_name, parent, folder, description FROM ".$pho_main." WHERE active=1 AND id=$num";
  if ($SortForums) $sSQL.=" ORDER BY name";
  $q->query($DB, $sSQL);
  $rec=$q->getrow();
}
else {
  $rec = '';
}

if (is_array($rec)) {
  $empty=false;
  $name=$rec["name"];
  $table=$rec["table_name"];
  $i++;
  $num=$rec["id"];
  if (!$rec["folder"]) {
    $sSQL = "SELECT * from $table WHERE approved='N' ORDER BY datestamp DESC";
    $pq=new query($DB, $sSQL);
    $pq->query($DB, $sSQL);
    $x=1;
    while ($tam=$pq->getrow()) {
      $subject=$tam["subject"];
      $id=$tam["id"];
      $topic=$tam["thread"];
      $person=$tam["author"];
      $datestamp = dateFormat($tam["datestamp"]);
      $approved = $tam["approved"];
      if (($x%2)==0) { $bgcolor=$ForumTableBodyColor1; }
      else { $bgcolor=$ForumTableBodyColor2; }
      $x++;
      $nav.='<TR><TD '.bgcolor($bgcolor).'>';
      $nav.="<A HREF=\"$forum_url/$read_page.$ext?admview=1&f=$num&i=".$tam["id"]."&t=${topic}\">";
      $nav.="<FONT FACE=\"Arial,Helvetica\">$subject</FONT></A></TD>";
      $nav.='<TD '.bgcolor($bgcolor)."><FONT FACE=\"Arial,Helvetica\">$person</FONT></TD><TD ".bgcolor($bgcolor).">";
      $nav.="<FONT face=\"Arial,Helvetica\" SIZE=-2>$datestamp</FONT></TD>";
      $nav.='<TD '.bgcolor($bgcolor)."><A HREF=\"${myname}?page=recentadmin&action=del&type=quick&id=${id}";
      $nav.="&num=${num}&navigate=${navigate}&thread=${topic}\"><FONT face=\"Arial,Helvetica\" SIZE=-2>Delete</FONT></A>&nbsp;|&nbsp;";
      $nav.="<A HREF=\"${myname}?page=edit&srcpage=recentadmin&id=${id}&num=${num}&navigate=${navigate}&mythread=${topic}\">";
      $nav.="<FONT face=\"Arial,Helvetica\" SIZE=-2>Edit</FONT></A>&nbsp;|&nbsp;";
      $nav.="<A HREF=\"${myname}?page=recentadmin&action=moderate&approved=${approved}&id=${id}&num=${num}&navigate=${navigate}";
      $nav.="&mythread=${topic}\"><FONT face=\"Arial,Helvetica\" SIZE=-2>";
      if ($approved == 'Y') { $nav.="Hide"; } else { $nav.="Approve"; }
      $nav.="</FONT></A></TD></TR>\n";
    }
  }
  $rec=$q->getrow();
}
else {
  $nav.="No active forums";
}

$nav.='</TABLE>';
print "$nav";
?>