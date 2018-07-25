<?php
  if(isset($ForumParent)){
    $level="num=$ForumParent";
    $nav="<a href=\"index.php?page=manage&$level\">Up A Level</a> | <a href=\"index.php?page=manage\">Up To Top</a>";
  }
  else{
    $ForumParent=0;
    $ForumName="Liste des forums disponibles";
    $nav='&nbsp;';
    $num=0;
  }
 echo "<CENTER><TABLE bgColor='#298CA0' cellspacing='0'  cellpadding = '2' width='98%'><TR><TD>";
 echo "<TABLE bgColor='#FFFFFF' cellspacing='1'  cellpadding = '0' width='100%'><TR><TD width='100%'>";
?>
<table cellspacing="0" cellpadding="4" border="0" width='100%'>
<tr>
    <td colspan="3" background="../../images/fond_titre_table.jpg" height='36' align='center' valign='center'>
    <font size='3' face="Arial,Helvetica" color="#FFFFFF"><B><?php echo $ForumName; ?></B></font></td>
</tr>
<tr>
    <td colspan="2"><?php echo aide_div("gestion_forums",0,0,0,0); ?></td>
</tr>
<tr bgcolor='#2b677a' height="25">
    <td width="20%" ><FONT color="white">&nbsp;<B><?php echo $lAvailableForums;?></B></font></td>
    <td width="20%" ><FONT color="white">&nbsp;<B>Nombre de messages</B></font></td>
    <td width="60%" ><FONT color="white">&nbsp;<B>Description</B></font></td>
</tr>
<?php
  $etat_flib = GetDataField($connect,"select param_etat_lb from param_foad where param_typ_lb='forum_libre'","param_etat_lb");
  $sSQL="Select * from ".$pho_main." where parent=$num order by name";

  $sSQL = ($etat_flib == 'OUI') ? "Select * from ".$pho_main." where parent=$num order by name" : "Select * from ".$pho_main." where parent=$num and name != 'Forum Libre' order by name";
  $q->query($DB, $sSQL);
  $rec=(object)$q->getrow();
  if ($q->numrows()==0)
     echo "<tr><td colspan='2' align='center' bgcolor='#FFFFFF'><font face='Arial,Helvetica'>Pas de forum défini.</font></td></tr>";
  While(isset($rec->id))
  {
      $mon_cpt++;
      if  ($rec->folder)
      {
          $text="Dossier";
      }
      else{
         if ($rec->description == 'Groupe:')
            $text="Forum de la formation : ".$rec->name;
         else
            $text=$rec->description;
         $nom_table =$rec->table_name;
         $nbr_pst = mysql_result(mysql_query("select count(*) from $nom_table"),0);
      }
      echo couleur_tr($mon_cpt,30);
      echo '<td style="text-align:left;"><font face="Arial,Helvetica">';
      if ($nbr_pst > 0)
      {
         echo "<a href=\"index.php?page=easyadmin&num=".$rec->id."\"><B>".$rec->name."<B></a>";
         if ($rec->folder)
         {
             echo " - <a href=\"index.php?page=manage&num=".$rec->id."\"><B>Parcourir</B></a>";
         }
         echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font></td>";
      }else
         echo "<B>".$rec->name."<B>";
      $nbr_aff = ($nbr_pst > 0) ? $nbr_pst : '';
      echo "<td><font face='Arial,Helvetica'><B>$nbr_aff</B></font></td>";
      echo "<td style='text-align:left;'><font face='Arial,Helvetica'>$text</font></td>";
      echo "</tr>";
      $rec=(object)$q->getrow();
  }
?>
</td></tr></table></td></tr></table></td></tr></table>
