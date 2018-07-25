<?php
  if (!isset($$phcollapse)) {
    $$phcollapse=0;
  }
  $titre_sujet = ($$phcollapse != 0 && !$read) ? $lTopics : $lsjtPst;
?>
<center><table width="100%" cellspacing="1" cellpadding="3" border="0">
<tr>
    <td height="21" <?php echo bgcolor($ForumTableHeaderColor); ?> width="100%" valign='top'><B><FONT color="<?php echo $ForumTableHeaderFontColor; ?>">&nbsp;<?php echo $titre_sujet;?>&nbsp;</font></B></td>

<?php if ( $$phcollapse != 0 && !$read) { ?>
    <td height="21" <?php echo bgcolor($ForumTableHeaderColor); ?> width="40" nowrap valign='top'><B><FONT color="<?php echo $ForumTableHeaderFontColor; ?>"><?php echo "Archiver";?>&nbsp;</font></B></td>
    <td height="21" <?php echo bgcolor($ForumTableHeaderColor); ?> width="150" nowrap valign='top'><B><FONT color="<?php echo $ForumTableHeaderFontColor; ?>"><?php echo $lAut_fil;?>&nbsp;</font></B></td>
    <td align="left" height="21" <?php echo bgcolor($ForumTableHeaderColor); ?> width="80" nowrap valign='top'><B><FONT color="<?php echo $ForumTableHeaderFontColor; ?>"><?php echo $lLu;?>&nbsp;</font></B></td>
    <td align="left" height="21" <?php echo bgcolor($ForumTableHeaderColor); ?> width="80" nowrap valign='top'><B><FONT color="<?php echo $ForumTableHeaderFontColor; ?>"><?php echo $lReplies;?>&nbsp;</font></B></td>
    <td height="21" <?php echo bgcolor($ForumTableHeaderColor); ?> width="150" nowrap valign='top'><B><FONT color="<?php echo $ForumTableHeaderFontColor; ?>"><?php echo $lLstrep;?>&nbsp;</font></B></td>
    <td height="21" <?php echo bgcolor($ForumTableHeaderColor); ?> width="115" nowrap valign='top'><B><FONT color="<?php echo $ForumTableHeaderFontColor; ?>"><?php echo $lLatest;?>&nbsp;</font></B></td>

<?php }else{ ?>

    <td height="21" <?php echo bgcolor($ForumTableHeaderColor); ?> width="150" nowrap valign='top'><B><FONT color="<?php echo $ForumTableHeaderFontColor; ?>"><?php echo $lAuthor;?>&nbsp;</font></B></td>
    <td height="21" <?php echo bgcolor($ForumTableHeaderColor); ?> width="115" nowrap valign='top'><B><FONT color="<?php echo $ForumTableHeaderFontColor; ?>"><?php echo $lDate;?>&nbsp;</font></B></td>

<?php } ?>
</tr>
<?php
  $x=0;
  $loc=0;
  @reset($headers);
        $message = @current($headers);
  if(!$read){
    @reset($threads);
          $trec=@current($threads);
  }
        while (is_array($message)){
                if  (($x%2)==0)
                {
                    $bgcolor=$ForumTableBodyColor1;
                    $fcolor=$ForumTableBodyFontColor1;
                }
                else
                {
                    $bgcolor=$ForumTableBodyColor2;
                    $fcolor=$ForumTableBodyFontColor2;
                }
                $t_id=$message["id"];
                $t_thread=$message["thread"];
                $t_subject=chop($message["subject"]);
                $t_author=chop($message["author"]);
                $t_datestamp = dateFormat($message["datestamp"]);

                if( ($$phcollapse != 0) && (!$read) )
                {
                  $t_latest=dateFormat($trec["latest"]);
                  $t_maxid=$trec["maxid"];
                }
                $message = next($headers);

                if($t_thread!=$t_id)
                {
                        $img = '&nbsp;<img src="images/l.gif" border=0 width=12 height=21 align="top">';
                        if(is_array($message))
                        {
                                if ($t_thread==$message["thread"])
                                        $img='&nbsp;<img src="images/t.gif" border=0 width=12 height=21 align="top">';
                        }
                }
                else
                {
                        $img="";
                        $loc=0;
                }

                if($id==$t_id && $read=true)
                {
                        $t_subject = "<b>$t_subject</b>";
                        $t_author = "<b>$t_author</b>";
                        $t_datestamp = "<b>$t_datestamp</b>";
                }else
                {
                         $t_subject = "<a href=\"$read_page.$ext?f=$num&i=$t_id&t=$t_thread$GetVars\"".
                                     bulle(htmlentities($open_subject,ENT_QUOTES,'ISO-8859-1'),"","RIGHT","ABOVE",150)."$t_subject</a>";
                        if ($forum_act > 0)
                        {
                          $lien_act="f=$num&i=$t_id&t=$t_thread$GetVars";
                          $lien_act = str_replace("&","|",$lien_act);
                          $lien_act = str_replace("=","!",$lien_act);
                          if (strstr($chaine_act,"acces=_entree"))
                             $lien_acces = "sequence_entree";
                          elseif(strstr($chaine_act,"acces=act_free"))
                             $lien_acces = "activite_free";
                           else
                              $lien_acces = "sequence";
                           $t_subject .= "&nbsp;&nbsp;&nbsp;&nbsp;<A href=\"$adresse_http/$lien_acces.php?lien_act=$lien_act&consult_act=1&modif=1$chaine_act&id_act=$forum_act&forum=1&arrive=$arrive\" title = \"$msq_ress_assoc\"><IMG SRC=\"$adresse_http/images/modules/tut_form/icosequen20.gif\" border=0></A>";
                         }
//                         $t_subject .= "";
                }
                $color=bgcolor($bgcolor);
                $cmpt++;
    $req_fil = GetDataField ($connect,"SELECT parent FROM $ForumTableName where id = ".$t_id,"parent");
    $nb_fil = mysql_result(mysql_query("SELECT count(*) FROM $nom_forum where thread = ".$t_thread),0);
    if ((!isset($forum_act) || (isset($forum_act) && $forum_act =='')) && ($req_fil == 0 && $nb_fil > 1))
       $ajt = "<a href=\"../archives/fil_forum.php?f=$num&t=".$t_thread."\"".
               bulle(htmlentities($bul_arch,ENT_QUOTES,'ISO-8859-1'),"","RIGHT","ABOVE",200).
               "<IMG SRC=\"../images/zip.gif\" border=0 width=15 height=15></A>";// && ($typ_user == 'ADMINISTRATEUR' || $typ_user == 'RESPONSABLE_FORMATION')
    else
       $ajt = "";
    $der_rep = mysql_query("select id,author from $ForumTableName where approved='Y' and thread = $t_thread order by id desc");
    $nb_lect = 0;$nb_lectot = 0;
    if (mysql_num_rows($der_rep) == TRUE){
       while ($items = mysql_fetch_object($der_rep))
       {
          $cpt_id = $items->id;
          $nb_lect += mysql_num_rows(mysql_query("select distinct forlec_topic_no from forum_lecture where forlec_user_no = $id_user and forlec_topic_no=".$cpt_id." and forlec_forum_no=$num"));
          $nb_lectot += mysql_result(mysql_query("select count(*) from forum_lecture where forlec_topic_no=".$cpt_id." and forlec_forum_no=$num"),0);
       }
       $der_id = mysql_result($der_rep,0,'id');
       $der_aut = mysql_result($der_rep,0,'author');
       $lien=" <a href=\"$read_page.$ext?f=$num&i=$der_id&t=$t_thread$GetVars\" ".
               bulle(htmlentities($consult_msg,ENT_QUOTES,'ISO-8859-1'),"","RIGHT","ABOVE",150)."$der_aut</a>";
    }
    $req_flag = mysql_query("SELECT * FROM forum_lecture where forlec_user_no = $id_user AND forlec_forum_no=$num AND forlec_topic_no=".$der_id);
    $nbr_flag = mysql_num_rows($req_flag);
    $flag_drap = ($nbr_flag > 0) ? "" : "<IMG SRC=\"images/flag.gif\" border=0 width=5 height=15>";
    $flag_lect = ($nb_lect < mysql_num_rows($der_rep)) ? "<IMG SRC=\"images/flag.gif\" border=0 width=5 height=15>" : "";
    echo couleur_tr($cmpt,30);
    $flag = ( $$phcollapse != 0 && !$read ) ? $flag_lect : $flag_drap;
    echo '  <td style="text-align:left;"><div class="sequence"><FONT color="'.$fcolor.'">'.$img.''.$t_subject.'</font>&nbsp;&nbsp;'.$flag."</div></td>\n";
    echo '  <td width="40" align="center">'.$ajt."</td>\n";
    echo '  <td width="150" nowrap style="text-align:left;"><FONT color="'.$fcolor.'">'.$t_author."</font></td>\n";
    echo '  <td width="40" nowrap><FONT color="'.$fcolor.'">'.$nb_lectot."</font></td>\n";
    if( $$phcollapse != 0 && !$read ){
        $t_count=$trec["tcount"]-1;
        $trec=next($threads);
        echo '  <td align="center" width="80" nowrap><FONT color="'.$fcolor.'" size=-1>'.$t_count."&nbsp;</font></td>\n";
        echo '  <td width="150" nowrap><FONT color="'.$fcolor.'">'.$lien.'</font>&nbsp;&nbsp;'.$flag_drap.'</td>'."\n";
        echo '  <td width="115" nowrap><FONT color="'.$fcolor.'" size=-1>'.$t_latest."</font></td>\n";
    }else{
        echo '  <td width="115" nowrap><FONT color="'.$fcolor.'" size=-1>'.$t_datestamp.'</font></td>'."\n";
    }
    echo "</tr>\n";
  $x++;
  $loc++;
} // end while
?>
</table>
