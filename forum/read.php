<?php
  $read=true;
  require "common.php";
  $thread=$t;
  $action=$a;
  $id=$i;
  if(isset($v)) $v=='f' ? $flat=1 : $flat=0;

  if($num==0 || $ForumName==''){
                Header("Location: $forum_page.$ext?$GetVars");
                exit;
        }
  if($id==0 && $action==0){
                Header("Location: $list_page.$ext?f=$num&collapse=0");
                exit;
        }

  $phcollapse="phorum-collapse-$ForumTableName";
  $phflat="phorum-flat-$ForumTableName";
  $new_cookie="phorum-new-$ForumTableName";
  $haveread_cookie="phorum-haveread-$ForumTableName";

  if($UseCookies)
  {

    if(IsSet($flat))
    {
      $$phflat=$flat;
      SetCookie("phorum-flat-$ForumTableName",$flat,time()+ 31536000);
    }
    elseif(!isset($$phflat)){
      $$phflat=$ForumFlat;
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
}
  else{
    if(IsSet($flat)){
      $$phflat=$flat;
    }
    else{
      $$phflat=$ForumFlat;
    }
    if(IsSet($collapse)){
      $$phcollapse=$collapse;
    }
    else{
      $$phcollapse=$ForumCollapse;
    }
  }

  if($admview!=1) {
    $limitApproved=" and approved='Y'";
  } else {
    $limitApproved="";
  }
  if($action!=0 && ($action==1 || $action==2)){
    if($DB->type=="sybase") {
      $limit="";
      $q->query($DB, "set rowcount $ForumDisplay");
    }
    elseif($DB->type=="postgresql"){
      $limit="";
      $q->query($DB, "set QUERY_LIMIT TO 1");
    }
    else{
      $limit="";
    }
    switch($action){
      case 2:
        $cutoff_thread=$thread-$cutoff;
        $sSQL="Select thread, id from $ForumTableName where thread<$thread and thread>$cutoff_thread and id=thread".$limitApproved." order by thread desc".$limit;
        break;
      case 1:
        $cutoff_thread=$thread+$cutoff;
        $sSQL="Select thread, id from $ForumTableName where thread<$cutoff_thread and thread>$thread and id=thread".$limitApproved." order by thread asc".$limit;
        break;
    }

    $msg = new query($DB, $sSQL);

    if($DB->type=="sybase") {
      $limit="";
      $q->query($DB, "set rowcount 0");
    }
    elseif($DB->type=="postgresql"){
      $q->query($DB, "set QUERY_LIMIT TO '0'");
    }

    if($msg->numrows()==0){
                  Header("Location: $list_page.$ext?f=$num$GetVars&collapse=0");
                  exit;
          }

    $tres=$msg->getrow();
    $id = $tres["id"];
    $thread = $tres["thread"];
  }

  $sSQL = "Select * from $ForumTableName where thread=$thread".$limitApproved." order by id";
  $msg_list = new query($DB, $sSQL);
  $rec=$msg_list->getrow();
  $x=0;
  While(is_array($rec)){
    $headers[]=$rec;
    if($rec["id"]==$id) $loc=$x;
    $rec=$msg_list->getrow();
    $x++;
  }

  if ($$phflat) {
    if ($admview==1) {
      $sSQL = "Select * from $ForumTableName"."_bodies where thread=".$thread." ORDER BY id";
    }
    $sSQL = "SELECT $ForumTableName.id AS id, $ForumTableName.thread AS thread, body from $ForumTableName, ".$ForumTableName."_bodies WHERE $ForumTableName.approved = 'Y' AND $ForumTableName.thread = ".$thread." AND $ForumTableName.id = ".$ForumTableName."_bodies.id ORDER BY id";
//  $sSQL = "SELECT a.id AS id, a.thread AS thread, b.body AS body from ".$ForumTableName." AS a, ".$ForumTableName."_bodies AS b WHERE a.approved = 'Y' AND a.thread = ".$thread." AND a.id = b.id ORDER BY id";
  } else {
    $sSQL = "Select * from $ForumTableName"."_bodies where id=".$id;
  }
  $msg_body = new query($DB, $sSQL);
  $rec=$msg_body->getrow();
  While(is_array($rec)){
    $bodies[]=$rec;
    $rec=$msg_body->getrow();
  }
  $msg_body->free();
  $header_rows=count($headers);
  $body_rows=count($bodies);
  $next_thread = "f=$num&t=$thread&a=2$GetVars";
  $prev_thread = "f=$num&t=$thread&a=1$GetVars";
  if(!$$phflat){
    if($loc+1==$header_rows){
      $next_link = $next_thread;
    }
    else{
      $next_loc = $loc+1;
      $next_id = $headers[$next_loc]["id"];
      $next_link = "f=$num&i=$next_id&t=$thread$GetVars";
    }

    if($loc==0){
      $prev_link = $prev_thread;
    }
    else{
      $prev_loc = $loc-1;
      $prev_id = $headers[$prev_loc]["id"];
      $prev_link = "f=$num&i=$prev_id&t=$thread$GetVars";
    }

    if(empty($haveread[$id]) && $UseCookies && $id > $old_message){
      if(empty($$haveread_cookie)){
        $haveread[$$new_cookie] = true;
        $$haveread_cookie=$$new_cookie;
      }
      $$haveread_cookie.=".";
      $$haveread_cookie.="$id";
      $haveread[$id] = true;
      SetCookie("phorum-haveread-$ForumTableName",$$haveread_cookie,0);
    }
    $max_id=$id;
  }
  else{
    $prev_link=$prev_thread;
    $next_link=$next_thread;
    $lNextMessage=$lNextTopic;
    $lPreviousMessage=$lPreviousTopic;
    if($UseCookies){
      $madechange=false;
      reset($headers);
      $row=current($headers);
      while(!empty($row["id"])){
        if(empty($haveread[$row["id"]]) && $row["id"] > $old_message){
          $madechange=true;
          if(empty($$haveread_cookie)){
            $haveread[$$new_cookie] = true;
            $$haveread_cookie=$$new_cookie;
          }
          $$haveread_cookie.=".";
          $$haveread_cookie.=$row["id"];
        }
        $haveread[$row["id"]] = true;
        $max_id=$row["id"];
        $row=next($headers);
      }
      if ($madechange) {
        SetCookie($haveread_cookie,$$haveread_cookie,0);
      }
    }
  }

  if($UseCookies){
    if($$new_cookie<$max_id){
      $$new_cookie=$max_id;
      SetCookie($new_cookie,$$new_cookie,time()+ 31536000);
    }
  }
  $subject = chop($headers[$loc]["subject"]);
  $rawsub=str_replace("</b>", "", $subject);
  $rawsub=str_replace("<b>", "", $subject);
  $title = " - ".$rawsub;
  if(file_exists("$include_path/header_$ForumConfigSuffix.php"))
  {
    include "$include_path/header_$ForumConfigSuffix.php";
  }
  else{
    include "$include_path/header.php";
  }
  $toThread = $thread + 1;
  $GetVars .= "&arrive=$arrive";
  if($$phflat==0){
    $flat_link = "<a href=\"$read_page.$ext?f=$num&i=$id&t=$thread&v=f$GetVars\">".$lReadFlat."</font></a>";
  }
  else{
    $flat_link = "<a href=\"$read_page.$ext?f=$num&i=$id&t=$thread&v=t$GetVars\">".$lReadThreads."</font></a>";
  }
  if($ActiveForums>1)
  {
    $nav = "<TABLE border=0 cellpadding='2' cellspacing = '4'><TR>
    <TD align='left' nowrap>$bouton_gauche<a href=\"$post_page.$ext?f=$num$GetVars\">".$lStartTopic."</a>$bouton_droite</TD>
    <TD align='left' nowrap></TD>
    <TD align='left' nowrap>$bouton_gauche $flat_link $bouton_droite</TD>
    <TD align='left' nowrap></TD></TR></TABLE>";
  }
  else
  {
    $nav = "<TABLE border=0 cellpadding='2' cellspacing = '4'><TR>
    <TD align='left' nowrap>$bouton_gauche<a href=\"$post_page.$ext?f=$num$GetVars\">".$lStartTopic."</a>$bouton_droite</TD>
    <TD align='left' nowrap></TD>
    <TD align='left' nowrap>$bouton_gauche $flat_link $bouton_droite</TD>
    <TD align='left' nowrap></TD></TR></TABLE>";
    $nbr_cpt++;
  }
$reqFlect = mysql_result(mysql_query("select count(*) from forum_lecture where
                                               forlec_forum_no=$num and
                                               forlec_user_no=$id_user and
                                               forlec_topic_no=$i"),0);
if ($reqFlect == 0)
{
   $req_flag = mysql_query("INSERT INTO forum_lecture (forlec_topic_no,forlec_forum_no,forlec_user_no)
                            VALUES ('$i','$f','$id_user')");
}
if ($header_rows==0 || $body_rows==0) { ?>
<table width="100%" cellspacing="0" cellpadding="2" border="0">
<tr>
    <td <?php echo bgcolor($ForumTableHeaderColor); ?>><font size=1><FONT color="<?php echo $ForumTableHeaderFontColor; ?>">&nbsp;<?php echo $lViolationTitle; ?></font></td>
</tr>
<tr>
    <td <?php echo bgcolor($ForumTableBodyColor2); ?> valign="TOP">
      <table width="100%" cellspacing="0" cellpadding="5" border="0">
        <tr>
          <td <?php echo bgcolor($ForumTableBodyColor2); ?> width="100%" valign="top"><font size=1><font color="<?php echo $ForumTableBodyFontColor2; ?>"><?php echo $lNotFound; ?>
          </td>
        </tr></font>
      </table>
    </td>
</tr>
</table>

<?php
}
else
{ ?>
<?php
  @reset($headers);
  @reset($bodies);
  $head_row=@current($headers);
  $body_row=@current($bodies);
  while(is_array($head_row) && is_array($body_row)){
    if($head_row["id"]==$body_row["id"]){    $cpt++;

      $rec_id=$head_row["id"];
      $subject = chop($head_row["subject"]);
      $author = chop($head_row["author"]);
      $datestamp = dateFormat($head_row["datestamp"]);
      $email = chop($head_row["email"]);
      $attachment = chop($head_row["attachment"]);
      $real_host=chop($head_row["host"]);
      if($real_host){
        $host_arr=explode(".", $real_host);
        $count=count($host_arr);
        if($count > 1){
          if(intval($host_arr[$count-1])!=0){
            $host=substr($real_host,0,strrpos($real_host,".")).".---";
          }else{
            $host = "---".strstr($real_host, ".");
          }
        }else{
          $host=$real_host;
        }
      }else{
        $host="";
      }
      $body = $body_row["body"];
      $auteur_msg = $author;
      $qauthor=str_replace("<b>|</b>", "", $author);
      $qsubject=str_replace("<b>|</b>", "", $subject);
      if($email!=""){
       if (strchr($auteur_msg," ")){
          $le_nom = explode(" ", $auteur_msg);
          $le_prenom = $le_nom[0];
       }else
          $le_prenom = $auteur_msg;
       $id_photo=GetDataField ($connect,"select util_photo_lb from utilisateur WHERE util_email_lb = '$email' AND util_prenom_lb = \"$le_prenom\"","util_photo_lb");
       if ($id_photo != "")
          list($w_img, $h_img, $type_img, $attr_img) = getimagesize("../images/$id_photo");
       $message_mail= "<Font size=2>".addslashes($mess_mail_avert)." $author </FONT>";
       $sujet =addslashes($subject);
       $lien = "../mail.php?dou=forum&contacter=1&a_qui=$email&sujet=$sujet&message_mail=$message_mail";
       $author = "<A HREF=\"#\" onclick=\"javascript:window.open('$lien','','left=0, top=0, width=680,height=520,resizable=yes,scrollbars=yes, menubar=0,location=0, toolbar=0')\" title=\"$mess_ecrire\"";
//       if ($id_photo != "")
//          $author .= " onMouseOver=\"overlib('', TEXTCOLOR, '#ffffff', TEXTSIZE, 2, WIDTH, $w_img, HEIGHT, $h_img, BACKGROUND, '../images/$id_photo', PADX, 60, 20, PADY, 20, 20,LEFT,DELAY,500)\" onMouseOut=\"nd()\"";
       $author .= ">$auteur_msg</A>";
      }
  if($collapse == 0)
  {
    $collapse_link = "<div id='ouvre' style=\"float:left;padding-right:10px;\">".
                     "<a href=\"$list_page.$ext?f=$num&collapse=1$GetVars\" class='bouton_new'>$lCollapseThreads</A></div>";
  }
  else
  {
    $collapse_link = "<div id='ferme' style=\"float:left;padding-right:10px;\">".
                     "<a href=\"$list_page.$ext?f=$num&collapse=0$GetVars\" class='bouton_new'>$lViewThreads</A></div>";
  }
  $collapse_link .= "<div id='cherche' style=\"float:left;padding-right:10px;\">".
                    "<a href=\"search_avc.$ext?f=$num$GetVars\" class= 'bouton_new'>$lavSearch</a></div>";
  if ($f == 3 && $typ_user == 'APPRENANT')
     $collapse_link .= aide_div("forum_libre_apprenant",0,0,0,0);
  elseif ($f == 3 && $typ_user != 'APPRENANT')
     $collapse_link .= aide_div("forum_libre_formateur",0,0,0,0);
  elseif ($f > 5 && $typ_user == 'APPRENANT')
     $collapse_link .= aide_div("forum_apprenant",0,0,0,0);
  elseif ($f > 5 && $typ_user != 'APPRENANT')
     $collapse_link .= aide_div("forum_formateur",0,0,0,0);
   echo '<table width="'.$ForumTableWidth.'" border="0" cellspacing="0" cellpadding="3"><tr>';
   if  ($ActiveForums > 1 && (!isset($cpt) || (isset($cpt) && $cpt == 1))){
       $nav1 = "<TABLE width=100% border='0' cellspacing='0' cellpadding='4'>".
               "<TR><TD align='center' nowrap valign='bottom'><div id='accueil' style=\"float:left;margin-left:1px;margin-right:10px;\">".
               "<a href=\"$list_page.$ext?f=$num\" class= 'bouton_new'>$mess_acc</a></div>";
       if ($v == 'f')
       {
             $nav1 .= "<div id='lus' style=\"float:left;padding-right:10px;\">".
                      "<A HREF=\"$list_page.$ext?tout_lu=1&f=$num&t=$t$GetVars\"".
                      " class='bouton_new'>$messfrmLu</A></div>";
       }

       $nav1 .= "<div id='new_subject' style=\"float:left;padding-right:10px;\">".
               "<A HREF=\"$post_page.$ext?f=$num$GetVars\"".
               " class='bouton_new'>$lStartTopic</A></div>$collapse_link</td></tr></table";
       echo '<table width="100%" cellspacing="0" cellpadding="0" border="0" bgcolor="#FFFFFF">'.
            '<tr height="10"><td></td></tr>'.
            '<tr><td nowrap>'.$nav1.'</td></tr>';
       echo "<tr><td align='left' nowrap style=\"height: 40px;\"><form action=\"$search_page.$ext\" method='GET'>".
            "<input type=\"Hidden\" name=\"f\" value=\"$num\">".
            "<input type=\"Hidden\" name=\"num\" value=\"$num\">".
            "<input type=\"Hidden\" name=\"match\" value=\"2\">".
            "<input type=\"Hidden\" name=\"date\" value=\"0\">".
            "<input type=\"Hidden\" name=\"fldauthor\" value=\"1\">".
            "<input type=\"Hidden\" name=\"fldsubject\" value=\"1\">".
            "<input type=\"Hidden\" name=\"fldbody\" value=\"1\">".
            "<input type=\"Text\" class='INPUT' id='search' style=\"float:left;margin-left:2px; name=\"search\" size=\"30\" value=\"$lSearch\" ".
            "onClick=\"javascript:var changer=getElementById('search');changer.value='';\">".
            "<A HREF=\"javascript:document.form1.submit();\" class='bouton_new'\" style=\"float:left;margin-left:4px;\">Ok</A></form></td>";
      echo "</tr></table>";
  }
    if (isset($cpt) && ($cpt/2) == ceil($cpt/2))
       $couleur = "style=\"background-color:#FFFFFF;\"";
    else
       $couleur = "style=\"background-color:#F4F4F4;\"";
    echo "<center><table  width='99%' cellspacing='0' cellpadding='2' border='0'>";
    echo '<tr><td'.bgcolor($ForumTableHeaderColor).' style="font-size: 11px;font-weight: bold;">'.
         '<FONT color="'.$ForumTableHeaderFontColor.'">&nbsp;'.$subject.'</font></td></tr>';
    echo '<tr><td '.$couleur.' valign="TOP"><table width="100%" cellspacing="0" cellpadding="5" border="0">';
    echo '<tr><td width="30%" valign="top">';

    echo '<table width="100%" cellspacing="0" cellpadding="2" border="0">';
    echo '<tr><td width="20%" valign="top">';
    if ($id_photo != '')
    {
       $taille_logo = getimagesize("../images/".$id_photo);
       if ($taille_logo[1] > 60){
          $largeur_logo=intval(ceil($taille_logo[0]/$taille_logo[1])*50);
          $hauteur_logo=intval(ceil($taille_logo[0]/$taille_logo[1])*60);
       }else{
          $largeur_logo=$taille_logo[0];
          $hauteur_logo=$taille_logo[1];
       }
       echo '<img src="../images/'.$id_photo.'" width="'.$largeur_logo.'" height="'.$hauteur_logo.'" border="0">';
    }
    else
       echo '<IMG SRC="../images/repertoire/icoptisilhouet.gif" width="19" height="25" border="0">';
    echo '</td><td width="80%" valign="top"> <tt>'.$lAuthor.':&nbsp;'.$author;//.'&nbsp;';$host;
    echo '<br>'.$mess_mess_date.':&nbsp;&nbsp;&nbsp;'.$datestamp;
    if ($uploadDir != '' AND !empty($attachment)) {
      print "<br>$lFormAttachment:&nbsp; <A HREF=\"javascript:void(0);\" ".
            "onclick=\"window.open('$forum_url/download.$ext?f=$num&file=$attachment','',".
            "'width=680,height=380,scrollbars=yes,resizable=yes,status=no')\">".$attachment."</A><BR>";
    }
    echo'</td></tr></table></td>' ;
    reset($plugins["read_header"]);
    while(list($key,$val) = each($plugins["read_header"])) {
      $val($rec_id);
    }
   // echo '<br>';
    $qbody=$body;
    $body=str_replace("{phopen}", "", $body);
    $body=str_replace("{phclose}", "", $body);
    $body=preg_replace("`<(mailto:)([^ >\n\t]+)>`", "{phopen}a href=\"\\1\\2\"{phclose}\\2{phopen}/a{phclose}", $body);
    $body=preg_replace("`<([http|news|ftp]+://[^ >\n\t]+)>`", "{phopen}a href=\"\\1\"{phclose}\\1{phopen}/a{phclose}", $body);
/*
    if($ForumAllowHTML!="Y" && substr($body, 0, 6)!="<HTML>"){
      $body=eregi_replace("<(/*($ForumAllowHTML) *[^>]*)>", "{phopen}\\1{phclose}", $body);
      $body=str_replace("<", "&lt;", $body);
      $body=str_replace(">", "&gt;", $body);
    }
*/
    $body=str_replace("{phopen}", "<", $body);
    $body=str_replace("{phclose}", ">", $body);
    // exec all read plugins
    reset($plugins["read_body"]);
    while(list($key,$val) = each($plugins["read_body"])) {
      $body = $val($body);
    }
    if(empty($ForumAllowHTML) && substr($body, 0, 6)!="<HTML>"){
      $body=nl2br($body);
    }
    else{
      $body=my_nl2br($body);
    }
    echo '<td style="font-size: 11px; width:70%;" valign="top">'.$body.'</tt></br></td>';
?>
</tr></font>
</table>
</td>
</tr>
</table>
<?php
if(!$$phflat){ ?>
<table width="<?php echo $ForumTableWidth; ?>" cellspacing="0" cellpadding="3" border="0"><font size="1">
<tr>
    <td valign="TOP" align="RIGHT" nowrap><font size=1>&nbsp;<DIV id='sequence'><a href="<?php echo "$read_page.$ext"; ?>?<?php echo $prev_thread; ?>&<?php echo $GetVars; ?>"><?php echo $lPreviousTopic;?></a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="<?php echo "$read_page.$ext"; ?>?<?php echo $next_thread; ?>&<?php echo $GetVars; ?>"><?php echo $lNextTopic;?></a></div></td>
</tr>
</table></font>
<p>
<?php }else{
?>
<table width="100%" cellspacing="0" cellpadding="3" border="0"><font size="1">
<tr>
    <td valign="TOP" width="100%" style="padding-right:8px;" align="RIGHT" <?php echo $couleur;?>><?php echo $bouton_gauche;?><a href="#REPLY"><?php echo $lReplyMessage; ?></a><?php echo $bouton_droite;?></td>
</tr>
</table></font>
<?php }
      $body_row=next($bodies);
    }
    $head_row=next($headers);
  }
  if(!$$phflat){
    if(!$ForumMultiLevel){
      include "$include_path/threads.php";
    }
    else{
      include "$include_path/multi-threads.php";
    }
  }
  unset($author);
  unset($email);
  unset($subject);
  echo '<A name="REPLY">';
  require "$include_path/form.php";
 }
  if(file_exists("$include_path/footer_$ForumConfigSuffix.php")){
    include "$include_path/footer_$ForumConfigSuffix.php";
  }
  else{
    include "$include_path/footer.php";
  }
?>
