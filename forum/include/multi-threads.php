<?php
  $t_gif="<IMG SRC=\"$forum_url/images/t.gif\" WIDTH=12 HEIGHT=21 BORDER=0>";
  $l_gif="<IMG SRC=\"$forum_url/images/l.gif\" WIDTH=12 HEIGHT=21 BORDER=0>";
  $p_gif="<IMG SRC=\"$forum_url/images/p.gif\" WIDTH=9 HEIGHT=21 BORDER=0>";
  $m_gif="<IMG SRC=\"$forum_url/images/m.gif\" WIDTH=9 HEIGHT=21 BORDER=0>";
  $c_gif="<IMG SRC=\"$forum_url/images/c.gif\" WIDTH=9 HEIGHT=21 BORDER=0>";
  $i_gif="<IMG SRC=\"$forum_url/images/i.gif\" WIDTH=12 HEIGHT=21 BORDER=0>";
  $n_gif="<IMG SRC=\"$forum_url/images/n.gif\" WIDTH=9 HEIGHT=21 BORDER=0>";
  $space_gif="<IMG SRC=\"$forum_url/images/trans.gif\" WIDTH=5 HEIGHT=21 BORDER=0>";
  $trans_gif="<IMG SRC=\"$forum_url/images/trans.gif\" WIDTH=12 HEIGHT=21 BORDER=0>";

  function echo_data($image, $topic, $row_color){
    global $connect;
    GLOBAL $ForumTableWidth,$ForumTableHeaderColor,$ForumTableHeaderFontColor,$forum_act,$chaine_act,$lg;
    GLOBAL $ForumTableBodyColor1,$ForumTableBodyFontColor1,$ForumTableBodyColor2,$ForumTableBodyFontColor2;
    GLOBAL $read_page,$ext,$collapse,$id,$UseCookies;
    GLOBAL $space_gif,$num,$old_message,$haveread,$use_haveread;
    GLOBAL $lNew, $GetVars,$id_user,$typ_user,$row_color_cnt;
    require ("../admin.inc.php");
    require ("../lang$lg.inc.php");

    $thread_total="";
    if(($row_color%2)==0){
      $bgcolor=$ForumTableBodyColor1;
      $font_color=$ForumTableBodyFontColor1;
    }else{
      $bgcolor=$ForumTableBodyColor2;
      $font_color=$ForumTableBodyFontColor2;
    }

    $subject ="<TABLE CELLSPACING=0 CELLPADDING=0 BORDER=0";
    if($bgcolor!=""){
        $subject.=" BGCOLOR=\"".$bgcolor."\"";
    }
    $subject.=">\n";
    $subject.= "<TR>\n<TD>";
    $subject.=$space_gif;
    $subject.=$image."</TD>\n<TD nowrap><FONT COLOR=\"$font_color\">&nbsp;";
    $req_flag = mysql_query("SELECT * FROM forum_lecture where forlec_user_no= $id_user AND forlec_forum_no=$num AND forlec_topic_no=".$topic["id"]);
    $nbr_flag = mysql_num_rows($req_flag);
    $nom_forum = GetDataField ($connect,"select table_name from forums where id = $num","table_name");
    $req_fil = GetDataField ($connect,"SELECT parent FROM $nom_forum where id = ".$topic['id'],"parent");
    $nb_fil = mysql_result(mysql_query("SELECT count(*) FROM $nom_forum where thread = ".$topic['thread']),0);
//    if ((!isset($forum_act) || (isset($forum_act) && $forum_act =='')) && ($req_fil == 0 && $nb_fil > 1 && ($typ_user == 'ADMINISTRATEUR' || $typ_user == 'RESPONSABLE_FORMATION')))
//       $ajt = "<a href=\"../archives/fil_forum.php?f=$num&t=".$topic['thread']."\"><IMG SRC=\"../images/zip.gif\" border=0 width=15 height=15></A>";
//    else
    if ($nbr_flag == 0)
       $ajt = "<IMG SRC=\"images/flag.gif\" style=\"padding-left:4px; width:5px; height:15px; border: 0;\">";
    else
       $ajt = "";
    if ($id == $topic["id"] && $read=true){
        $subject .= "<b>".$topic["subject"]."</b>$ajt";
        $author = "<b>".$topic["author"]."</b>";
        $datestamp = "<b>".dateFormat($topic["datestamp"])."</b>";
    }
    else{
        $subject.="<a href=\"$read_page.$ext?f=$num&i=".$topic["id"];
        $subject.="&t=".$topic["thread"]."$GetVars\">".$topic["subject"]."</a>$ajt";
        $author = $topic["author"];
        $datestamp = dateFormat($topic["datestamp"]);
      if ($forum_act > 0){
        $lien_act = "f=$num&i=".$topic["id"]."&t=".$topic["thread"]."$GetVars";
        $lien_act = str_replace("&","|",$lien_act);
        $lien_act = str_replace("=","!",$lien_act);
        if (strstr($chaine_act,"acces=_entree"))
           $lien_acces = "sequence_entree";
        elseif(strstr($chaine_act,"acces=act_free"))
           $lien_acces = "activite_free";
        else
           $lien_acces = "sequence";
        //$subject .= "&nbsp;&nbsp;&nbsp;&nbsp;<A href=\"../$lien_acces.php?lien_act=$lien_act&consult_act=1&modif=1$chaine_act&id_act=$forum_act&forum=1&arrive=$arrive\" title = \"$msq_ress_assoc\"><IMG SRC=\"$adresse_http/images/modules/tut_form/icosequen20.gif\" border=0 valign=bottom ></A>";
     }
   }
   $subject.="&nbsp;&nbsp;</font>";
    if($nbr_flag > 0)
      $isnew=false;
    //else
      //$subject.="&nbsp;&nbsp;&nbsp;&nbsp;<IMG SRC=\"images/flag.gif\" border='0' width='5' height='11'>";
    $subject.="</TD>\n</TR>\n</TABLE>";
    $nb_lect = mysql_result(mysql_query("select count(*) from forum_lecture where forlec_topic_no=".$topic["id"]." and forlec_forum_no=$num"),0);
    echo couleur_tr($row_color_cnt,20);
    ?>
<TD valign='bottom' <?php echo bgcolor($bgcolor);?>><DIV id='sequence'><?php echo $subject; ?></DIV></TD>
<TD<?php echo bgcolor($bgcolor);?> nowrap><FONT COLOR="<?php echo $font_color;?>"><?php echo $nb_lect;?></FONT></TD>
<TD<?php echo bgcolor($bgcolor);?> nowrap style='text-align:left;padding-right:10px;'><FONT COLOR="<?php echo $font_color;?>"><?php echo $author;?></FONT></TD>
<TD<?php echo bgcolor($bgcolor);?> nowrap><FONT  COLOR="<?php echo $font_color;?>"><?php echo $datestamp;?></FONT></TD>
</TR>
<?php
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

  @reset($headers);
  $row=@current($headers);

  if(is_array($row)){
    if(!$read){
      reset($threads);
      $rec=current($threads);
      while(is_array($rec)){
        $thd=$rec["thread"];
        if(!isset($rec["tcount"])) $rec["tcount"]=0;

        $tcount=$rec["tcount"];
        $threadtotal[$thd]=$tcount;
        $rec=next($threads);
      }
    }
    else{
      $threadtotal[$thread]=count($headers);
    }
    $topics["max"]="0";
    $topics["min"]="0";
    While(is_array($row)){
      $cpt++;
      $x="".$row["id"]."";
      $p="".$row["parent"]."";
      $messages["$x"]=$row;
      $messages["$p"]["replies"]["$x"]="$x";
      $messages["$p"]["max"]=$row["id"];
      if(!isset($messages["max"])) $messages["max"]=0;
      if(!isset($messages["min"])) $messages["min"]=0;
      if($messages["max"]<$row["thread"]) $messages["max"]=$row["thread"];
      if($messages["min"]>$row["thread"]) $messages["min"]=$row["thread"];
      $row=next($headers);
    }
  }

  $titre_sujet = ($collapse == 0) ? $lsjtPst : $lTopics;
?>
<center><TABLE WIDTH="99%" CELLSPACING=0 CELLPADDING=0 BORDER=0>
<TR>
    <TD HEIGHT=21<?php echo bgcolor($ForumTableHeaderColor);?> WIDTH="100%"><B><FONT COLOR="<?php echo $ForumTableHeaderFontColor; ?>">&nbsp;<?php echo $titre_sujet;?></FONT></B></TD>
    <TD HEIGHT=21<?php echo bgcolor($ForumTableHeaderColor);?> NOWRAP WIDTH=150><B><FONT COLOR="<?php echo $ForumTableHeaderFontColor; ?>"><?php echo $lLu;?>&nbsp;</FONT></B></TD>
    <TD HEIGHT=21<?php echo bgcolor($ForumTableHeaderColor);?> NOWRAP WIDTH=150><B><FONT COLOR="<?php echo $ForumTableHeaderFontColor; ?>"><?php echo $lAuthor;?>&nbsp;</FONT></B></TD>
    <TD HEIGHT=21<?php echo bgcolor($ForumTableHeaderColor);?> NOWRAP WIDTH=100><B><FONT COLOR="<?php echo $ForumTableHeaderFontColor; ?>"><?php echo $lDate;?></FONT></B></TD>
</TR>
<?php
  thread();
?>
</TABLE>
