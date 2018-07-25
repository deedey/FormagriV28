<?php
if (!isset($_SESSION)) session_start();
include ("include/UrlParam2PhpVar.inc.php");
if (!isset($_SESSION['id_user']) || $_SESSION['id_user'] == "")
{
  exit();
}
require "admin.inc.php";
require 'fonction.inc.php';
require 'fonction_html.inc.php';
require "lang$lg.inc.php";
if (isset($accede) && $accede == "_entree" && isset($dhou) && $dhou == 1)
   $_SESSION['accede'] = $accede;
//include ("click_droit.txt");
dbConnect();
//de quel type est l'utilisateur (apprenant, formateur, administrateur)
$nom_user = $_SESSION['name_user'];
$prenom_user = $_SESSION['prename_user'];
$email=$_SESSION['email_user'];
include 'style.inc.php';
GLOBAL $objet;
if (isset($dou) && $dou == 1)
{
  echo "<CENTER><FONT COLOR='white' size='3'><B>$mess_menu_favoris</B></FONT><P>";
  echo "<TABLE bgColor='#298CA0' cellspacing='2' width='800' ><TR><TD width='100%'>";
  echo "<TABLE bgColor='#FFFFFF' cellspacing='2' width='100%'><TR><TD>";
  echo "<TABLE width='100%'>";
  $lien = "favoris.php?ajouter=1";
  $lien = urlencode($lien);
  echo "<TR><TD><A HREF=\"trace.php?link=$lien\">$mess_menu_ajout_favori</A></TD></TR>";
  echo "<TR><TD><B>$mess_menu_consult_favori :</B><UL>";
  $lien="favoris.php?consulter=1&objet=perso";
  $lien = urlencode($lien);
  echo "<LI type=\"square\"><A HREF=\"trace.php?link=$lien\">".strtolower($mess_menu_consult_fav_prop)."</A></LI>";
  $lien="favoris.php?consulter=1&objet=groupe";
  $lien = urlencode($lien);
  echo "<LI type=\"square\"><A HREF=\"trace.php?link=$lien\">$mess_menu_consult_grp</A></LI>";
  $lien="favoris.php?consulter=1&objet=tous";
  $lien = urlencode($lien);
  echo "<LI type=\"square\"><A HREF=\"trace.php?link=$lien\">$mess_menu__consult_fav_app</A></LI></UL>";
  echo "</TD></TR></TABLE></TD></TR></TABLE></TD></TR></TABLE>";
  exit;
}

if (isset($ajouter) && $ajouter == 1)
{
  if (isset($via) && $via == 1)
  {
     $url = GetDataField ($connect,"select ress_url_lb from ressource_new where ress_cdn = $id_ress","ress_url_lb");
     $tit = GetDataField ($connect,"select ress_titre from ressource_new where ress_cdn = $id_ress","ress_titre");
     $description = GetDataField ($connect,"select ress_desc_cmt from ressource_new where ress_cdn = $id_ress","ress_desc_cmt");
     $id_max = Donne_ID ($connect,"select max(fav_cdn) from favoris");
     $saisie = mysql_query ("INSERT INTO favoris (fav_cdn,fav_seq_no,fav_utilisateur_no,fav_url_lb,fav_titre_lb,fav_desc_lb) VALUES ($id_max,$seq,$id_user,'$url',\"$tit\",\"$description\")");
     $mess_ajoute = $mess_fav_link_aj;
     $lien = "favoris.php?seq=$seq&consulter=1&mess_ajoute=$mess_ajoute";
     $lien = urlencode($lien);
     echo "<script language='JavaScript'>";
     echo "document.location.replace(\"trace.php?link=$lien\");";
     echo "</script>";
   exit();
  }
  ?>
  <SCRIPT language=JavaScript>
    function checkForm(frm) {
      var ErrMsg = "<?php echo $mess_info_no;?>\n";
      var lenInit = ErrMsg.length;
      if (isEmpty(frm.url)==true)
        ErrMsg += ' - <?php echo $mess_fav_adr_site;?>\n';
      if (isEmpty(frm.tit)==true)
        ErrMsg += ' - <?php echo $mess_fav_tit;?>\n';
      if (isEmpty(frm.description)==true)
        ErrMsg += ' - <?php echo $mess_desc;?>\n';
      if (ErrMsg.length > lenInit)
        alert(ErrMsg);
      else
        frm.submit();
    }
    function isEmpty(elm) {
      var elmstr = elm.value + "";
      if (elmstr.length == 0)
       return true;
     return false;
   }
  </SCRIPT>
  <?php
  GLOBAL $objet;
  if (isset($seq) && $seq > 0)
  {
     entete_simple($mess_ress_cons_aj);
     echo "<tr><td><div id='aide' style=\"float:left; margin-left:3px;margin-top:2px;\">";
     echo "<a href=\"aide/favoris.html\" class= 'bouton_new' target='_blank'>$mess_menu_aide</a></div>";
     $nom_seq = GetDataField ($connect,"select seq_titre_lb from sequence where seq_cdn=$seq","seq_titre_lb");
     $lien = "recherche.php?flg=1&id_seq=$seq&favoris=1";
     $lien = urlencode($lien);
     echo "<div id='favoris' style=\"float:left; margin-left: 5px; margin-top:2px;\"><A HREF=\"trace.php?link=$lien\" class= 'bouton_new' ".
          bulle(NewHtmlentities("$mess_fav_mess1 : $nom_seq"),"","RIGHT","ABOVE",180).$mess_fav_ctr_ress."</A></td></tr>";
     echo "<TR><TD colspan='2'><TABLE cellpadding='5' align='MIDDLE' valign='MIDDLE' border='0'>";
  }
  else
  {
     entete_simple($mess_menu_ajout_favori);
     echo "<TR><TD colspan='2'><TABLE cellpadding='5' align='MIDDLE' valign='MIDDLE' border='0'>";
     $seq=0;
  }
  echo "<FORM NAME='form1' action=\"favoris.php?inserer=1&objet=$objet&seq=$seq\" method='post'>";
   ?><TR>
      <TD nowrap>
         <b><?php  echo $mess_fav_adr_site;?></b>
      </TD>
      <TD nowrap>
         <INPUT TYPE="TEXT"  name="url" value="<?php  echo $url;?>" size="60" align="middle">
      </TD>
   </TR>
   <TR>
      <TD nowrap>
         <b><?php  echo $mess_fav_tit;?></b>
      </TD>
      <TD nowrap>
         <INPUT TYPE="TEXT"  name="tit" value="<?php  echo $tit ;?>" size="60" align="middle">
      </TD>
   </TR>
   <TR>
      <TD nowrap>
         <b><?php  echo  $mess_desc ;?></b>
      </TD>
      <TD nowrap>
         <TEXTAREA  name="description" rows="5" cols="50" align="middle"><?php  echo $description ;?></TEXTAREA>
      </TD>
   </TR>
   <?php if (!isset($seq)){ ?>
   <TR>
      <TD nowrap>
         <small><b><?php  echo $mess_fav_pub ;?></b></small>
      </TD>
      <TD nowrap>
         <SELECT  name="public" size="1">
         <OPTION SELECTED><?php  echo $mess_fav_tous ;?></OPTION>
         <OPTION value="PERSONNEL"><?php  echo $mess_fav_pers ;?></OPTION>
         <OPTION value="GROUPE"><?php  echo $mess_fav_grp ;?></OPTION>
         </SELECT>
      </TD>
   </TR>
   <?php
   }
   else
   {
      echo "<INPUT TYPE='hidden'  name='seq' value = '$seq'>";
      echo "<INPUT TYPE='hidden'  name='public' value = '$mess_fav_tous'>";
   }
   ?>
   <TR>
      <TD></td><td>
        <?php echo "<A HREF=\"javascript:checkForm(document.form1);\" onClick=\"TinyMCE.prototype.triggerSave();\" ".
                   "onmouseover=\"img1.src='images/fiche_identite/boutvalidb$suffixer.gif';return true;\" ".
                   "onmouseout=\"img1.src='images/fiche_identite/boutvalid$suffixer.gif'\">".
                   "<IMG NAME=\"img1\" SRC=\"images/fiche_identite/boutvalid$suffixer.gif\" BORDER='0' ".
                   "onLoad=\"tempImg=new Image(0,0); tempImg.src='images/fiche_identite/boutvalidb$suffixer.gif'\"></A>";
        ?>
      </TD>
   </TR>
   </TABLE></TD></TR></TABLE></TD></TR></TABLE>
  </center>
  <div id='mien' class='cms'></div>
  </body>
  </html>
<?php
exit;
}
if (isset($inserer) && $inserer == 1){
  $rest = substr($url, 0, 7);
  if ($tit == "" || ord($tit{0}) == 32 || $description == "" || ord($description{0}) == 32)
  {
    $mess_ajoute = $mess_gen_ins;
  }
  elseif ($rest != "http://")
    $mess_ajoute = $mess_adr_novalid;
  else
  {
    $id_max = Donne_ID ($connect,"select MAX(fav_cdn) from favoris");
    $saisie = mysql_query ("INSERT INTO favoris (fav_cdn,fav_seq_no,fav_utilisateur_no,fav_url_lb,fav_titre_lb,fav_desc_lb,fav_public_on) VALUES ($id_max,$seq,$id_user,'$url',\"$tit\",\"".NewHtmlentities($description)."\",'$public')");
    if ($seq > 0)
      $mess_ajoute = $mess_fav_link_aj;
    else
      $mess_ajoute = $mess_fav_lien_ajout;
  }
  if ($public == "PERSONNEL")
    $objet = "perso";
  elseif ($public == "GROUPE")
    $objet = "groupe";
  elseif ($public == "TOUS")
    $objet = "tous";
  $consulter = 1;
}

if (isset($modifier) && $modifier == 1){
  ?>
  <SCRIPT language=JavaScript>
    function checkForm(frm) {
      var ErrMsg = "<?php echo $mess_info_no;?>\n";
      var lenInit = ErrMsg.length;
      if (isEmpty(frm.url)==true)
        ErrMsg += ' - <?php echo $mess_fav_adr_site;?>\n';
      if (isEmpty(frm.tit)==true)
        ErrMsg += ' - <?php echo $mess_fav_tit;?>\n';
      if (isEmpty(frm.description)==true)
        ErrMsg += ' - <?php echo $mess_desc;?>\n';
      if (ErrMsg.length > lenInit)
        alert(ErrMsg);
      else
        frm.submit();
    }
    function isEmpty(elm) {
      var elmstr = elm.value + "";
      if (elmstr.length == 0)
       return true;
     return false;
   }
  </SCRIPT>
  <?php
  $sql = mysql_query ("SELECT * from favoris where fav_cdn=$num");
  $url = mysql_result($sql,0,"fav_url_lb");
  $tit = mysql_result($sql,0,"fav_titre_lb");
  $seq = mysql_result($sql,0,"fav_seq_no");
  $public = mysql_result($sql,0,"fav_public_on");
  $description = mysql_result($sql,0,"fav_desc_lb");
  $user = mysql_result($sql,0,"fav_utilisateur_no");
  entete_simple($mess_modif_fav);
  echo "<FORM NAME='form1' action=\"favoris.php?modification=1&objet=$objet&num=$num\" method='post'>";
 ?>
  <TR><TD colspan='3'><TABLE bgColor='#FFFFFF'>
   <TR>
      <TD>
         <b><?php  echo $mess_fav_adr_site ;?></b>
      </TD>
      <TD>
         <INPUT TYPE="TEXT"  name="url" value="<?php  echo $url;?>" size="60" align="middle">
      </TD>
   </TR>
   <TR>
      <TD>
         <b><?php  echo $mess_fav_tit;?></b>
      </TD>
      <TD>
         <INPUT TYPE="TEXT"  name="tit" value="<?php  echo $tit ;?>" size="60" align="middle">
      </TD>
   </TR>
   <TR>
      <TD>
         <b><?php  echo $mess_desc;?></b>
      </TD>
      <TD>
         <TEXTAREA  name="description" rows="5" cols="50" align="middle"><?php  echo $description ;?></TEXTAREA>
      </TD>
   </TR>
   <?php
    if (!isset($seq))
    {
    ?>
   <TR>
      <TD>
         <b><?php  echo "$mess_fav_pub " ;?></b>
      </TD>
      <TD>
         <SELECT  name="public" size="1">
         <OPTION SELECTED><?php  echo $public ;?></OPTION>
         <OPTION value="TOUS"><?php  echo $mess_fav_tous ;?></OPTION>
         <OPTION value="PERSONNEL"><?php  echo $mess_fav_pers ;?></OPTION>
         <OPTION value="GROUPE"><?php  echo $mess_fav_grp ;?></OPTION>
         </SELECT>
      </TD>
   </TR>
   <?php
    }else
       echo "<INPUT TYPE='hidden'  name='seq' value = '$seq'>";

  echo "<TR><TD align=left><A HREF=\"javascript:history.back();\" TITLE=\"$alter\" onmouseover=\"img_annonce.src='images/fiche_identite/boutretourb.gif';return true;\" onmouseout=\"img_annonce.src='images/fiche_identite/boutretour.gif'\">";
  echo "<IMG NAME=\"img_annonce\" SRC=\"images/fiche_identite/boutretour.gif\" BORDER='0' onLoad=\"tempImg=new Image(0,0); tempImg.src='images/fiche_identite/boutretourb.gif'\"></A></TD>";
  echo "<TD><A HREF=\"javascript:checkForm(document.form1);\" onClick=\"TinyMCE.prototype.triggerSave();\" ".
       "onmouseover=\"img1.src='images/fiche_identite/boutvalidb$suffixer.gif';return true;\" ".
       "onmouseout=\"img1.src='images/fiche_identite/boutvalid$suffixer.gif'\">".
       "<IMG NAME=\"img1\" SRC=\"images/fiche_identite/boutvalid$suffixer.gif\" BORDER='0' ".
       "onLoad=\"tempImg=new Image(0,0); tempImg.src='images/fiche_identite/boutvalidb$suffixer.gif'\"></A>";
  echo "</TD></TR></TABLE></TD></TR></TABLE></TD></TR></TABLE>";
  echo "</center>";
  echo "<div id='mien' class='cms'></div></body></html>";
exit;
}

if (isset($modification) && $modification == 1)
{
  if ($tit == "" || ord($tit{0}) == 32 || $description == "" || ord($description{0}) == 32 || substr($url, 0, 7) != "http://")
  {
    $mess_ajoute = $msq_oubli_champ_oblig;
  }
  else
  {
    $sql = mysql_query("UPDATE favoris SET fav_url_lb=\"$url\",fav_titre_lb=\"$tit\",fav_desc_lb=\"$description\",fav_public_on=\"$public\" where fav_cdn = \"$num\"");
    if ($seq > 0)
    {
      $nom_seq = GetDataField ($connect,"select seq_titre_lb from sequence where seq_cdn=$seq","seq_titre_lb");
      $mess_ajoute = "$mess_fav_ress_seq1 [ $nom_seq ] $mess_fav_resseq_mod";
    }
    else
      $mess_ajoute = $mess_fav_mod;
  }
  $consulter = 1;
}
if (isset($supprimer) && $supprimer == 1)
{
  $sql = mysql_query("DELETE from favoris where fav_cdn = $num");
  if (isset($seq) && $seq > 0)
  {
    $nom_seq = GetDataField ($connect,"select seq_titre_lb from sequence where seq_cdn=$seq","seq_titre_lb");
    $mess_ajoute = "$mess_fav_ress_seq1 [ $nom_seq ] $mess_fav_resseq_sup";
  }
  else
    $mess_ajoute = $mess_fav_sup;
  $consulter = 1;
}
if (isset($consulter) && $consulter == 1)
{
  if (isset($toutes) && $toutes == 1 && $typ_user == "APPRENANT")
    $sql = mysql_query ("SELECT * from favoris,suivi2_$numero_groupe where favoris.fav_seq_no =suiv2_seq_no and suiv2_utilisateur_no = $id_user order by suiv2_seq_no,favoris.fav_utilisateur_no");
  elseif (isset($toutes) && $toutes == 1 && $typ_user != "APPRENANT")
    $sql = mysql_query ("SELECT * from favoris,sequence where favoris.fav_seq_no = sequence.seq_cdn order by sequence.seq_cdn,favoris.fav_utilisateur_no");
  elseif (isset($objet) && $objet == "perso")
    $sql = mysql_query ("SELECT * from favoris where fav_utilisateur_no = $id_user and fav_public_on ='PERSONNEL'");
  elseif (isset($seq) && $seq > 0)
    $sql = mysql_query ("SELECT * from favoris where fav_seq_no ='$seq'");
  elseif (isset($objet) && $objet == "tous" && $typ_user == "ADMINISTRATEUR")
    $sql = mysql_query ("SELECT * from favoris where fav_seq_no = 0 order by favoris.fav_utilisateur_no");
  elseif (isset($objet) && $objet == "groupe")
    $sql = mysql_query ("SELECT * from favoris,utilisateur_groupe where favoris.fav_public_on = 'GROUPE' and utilisateur_groupe.utilgr_groupe_no = '$numero_groupe' group by favoris.fav_cdn");
  elseif (isset($objet) && $objet == "tous" || (!$objet && $toutes !=1))
    $sql = mysql_query ("SELECT * from favoris where fav_public_on ='TOUS' order by favoris.fav_utilisateur_no");
  $nbr = mysql_num_rows($sql);
  if ($nbr == 0)
  {
    $agent=getenv("HTTP_USER_AGENT");
    if (strstr($agent,"MSIE"))
    {
       echo "<SCRIPT Language=\"Javascript\">";
         echo "window.parent.opener.location.reload();";
       echo "</SCRIPT>";
    }
    else
    {
       echo "<SCRIPT Language=\"Javascript\">";
         echo "parent.parent.opener.location.reload();";
       echo "</SCRIPT>";
    }
/*    print "<SCRIPT Language=\"Javascript\">";
       print("top.close()");
    print("</SCRIPT>");
*/
  }

  $message_fav = ucfirst($mess_menu_prescrites);
  if (isset($seq) && $seq > 0)
  {
     $droit_seq = GetDataField ($connect,"select seq_publique_on from sequence where seq_cdn=$seq","seq_publique_on");
     $id_auteur = GetDataField ($connect,"select seq_auteur_no from sequence where seq_cdn = $seq","seq_auteur_no");
     $nom_seq = GetDataField ($connect,"select seq_titre_lb from sequence where seq_cdn=$seq","seq_titre_lb");
//     $lien = "sequence$accede.php?liste=1&flg_seq=0&id_seq=$seq&consult=1";
//     $lien = urlencode($lien);
    if ($typ_user == "APPRENANT")
    {
      $titre = "$message_fav";
      $sous_titre ="$mess_fav_lis_resseq : <B>  $nom_seq</B>";
    }
    else
    {
      $titre = $message_fav;
      $sous_titre = $mess_fav_lis_resseq;
      $sous_titre .= " : <FONT SIZE=2><B> $nom_seq</B></FONT>";
    }
  }
  elseif (isset($toutes) && $toutes == 1)
    $titre = $message_fav;
  elseif (isset($objet) && $objet == "perso")
    $titre = $mess_menu_consult_fav_prop;
  elseif (isset($objet) && $objet == "tous")
    $titre = $mess_fav_lis_tous;
  elseif (isset($objet) && $objet == "groupe")
    $titre = $mess_fav_lis_grp;
  if ($nbr == 0)
  {
      entete_simple($titre);
      echo "<TR height='40'><TD colspan='2' class='sous_titre'>$sous_titre<br />";
      if ($seq > 0 || ($nbr == 0 && $toutes == 1))
        echo "$mess_no_ress_cons";
      else
        echo "$fav_nofav";
      echo "</TD></TR></TABLE>";
    exit;
  }
    $bgcolor2 = '#5b8bab';
    $bgcolor1 = '#F8F2E4';
    $fav = ucfirst(strtolower($mess_fav_fav));
    if (isset($mess_ajoute) && $mess_ajoute != '')
       echo notifier($mess_ajoute);
    entete_simple($titre);
    echo "<TR><TD colspan='2' class='sous_titre'>$sous_titre</TD></TR>";
    echo "<TR><TD colspan='2'><TABLE border='0' cellspacing='1' cellpadding='5' width='100%'><TR bgcolor='#2B677A'> ";
    if (isset($seq) && $seq > 0 && !isset($toutes))
      echo "<td height='20'><FONT COLOR=white><b>$msq_ress</b></FONT></td>";
    elseif (isset($toutes) && $toutes == 1)
    {
      echo "<td height='20'><FONT COLOR=white><b>$msq_prereq_seq</b></FONT></td>";
      echo "<td height='20'><FONT COLOR=white><b>$msq_ress</b></FONT></td>";
    }
    else
      echo "<td><FONT COLOR=white><b>$mess_site</b></FONT></td>";
    echo "<td height='20'><FONT COLOR=white><b>$mess_desc</b></FONT></td>";
    if ($objet != "perso")
       echo "<td height='20' nowrap><FONT COLOR=white><b>$mess_fav_aut</b></FONT></td>";
    if (((isset($seq) && $seq > 0) || (isset($toutes) && $toutes == 1)) && $typ_user == 'APPRENANT')
    {
    }
    else
    {
        echo "<td height='20' align='middle'><FONT COLOR=white><b>$mess_modif_base</b></FONT></td>";
        echo "<td height='20' align='middle'><FONT COLOR=white><b>$mess_ag_supp</b></FONT></td>";
    }
    echo "</TR>";
  $i = 0;
  while ($i < $nbr)
  {
    $num = mysql_result($sql,$i,"fav_cdn");
    $url = mysql_result($sql,$i,"fav_url_lb");
    $tit = mysql_result($sql,$i,"fav_titre_lb");
    $sequence = mysql_result($sql,$i,"fav_seq_no");
    $description = mysql_result($sql,$i,"fav_desc_lb");
    $user = mysql_result($sql,$i,"fav_utilisateur_no");
    $nom = GetDataField ($connect,"select util_nom_lb from utilisateur where util_cdn='$user'","util_nom_lb");
    $prenom = GetDataField ($connect,"select util_prenom_lb from utilisateur where util_cdn='$user'","util_prenom_lb");
    echo couleur_tr($i+1,'');
    if (isset($toutes) && $toutes == 1 && isset($sequence) && $sequence > 0)
    {
      $nom_seq = GetDataField ($connect,"select seq_titre_lb from sequence where seq_cdn=$sequence","seq_titre_lb");
      echo "<td align='left' valign='top'>$nom_seq</td>";
    }
    if (isset($url) && $url != "")
    {
      $req_serv = mysql_query("select * from serveur_ressource");
      $nb_req_serv = mysql_num_rows($req_serv);
      if ($nb_req_serv > 0)
      {
         $ir = 0;
         while ($ir < $nb_req_serv)
         {
             $adr = mysql_result($req_serv,$ir,"serveur_nomip_lb");
             $params = mysql_result($req_serv,$ir,"serveur_param_lb");
             $label = mysql_result($req_serv,$ir,"serveur_label_lb");
             if ($label != "")
             {
                if (strstr($ur,$adr) && strstr($ur,$label)){
                   $url = str_replace("&label=$label","",$url);
                   $url .= $params;
                   $transit = 1;
                   break;
                }
             }elseif ($label == "" && strstr($url,"label="))
             {
                $ir++;
                continue;
             }
             elseif ($label == "" && !strstr($url,"label="))
             {
                if (strstr($url,$adr))
                {
                   $url .= $params;
                   $transit = 1;
                   break;
                }
             }
             $ir++;
         }
         if ($transit == 1)
         {
            $url=urldecode($url);
            echo "<A HREF=\"javascript:void(0);\" onclick=\"window.open('$url','','resizable=yes,scrollbars=yes,status=no')\">";
            $transit = 0;
            $traspasse = 1;
         }
      }
      if ((strstr(strtolower($url),".doc") || strstr(strtolower($url),".xls") || strstr(strtolower($url),".xlt")) && $traspasse != 1)
         echo "<TD align='left' valign='top'><DIV id='sequence'><A href='$url' target='_blank'>$tit</A></DIV></TD>";
      elseif (!isset($traspasse) || (isset($traspasse) && $traspasse != 1))
      {
        $lien = $url;
        $lien = urlencode($lien);
        echo "<TD align='left' valign='top'><DIV id='sequence'><a href=\"trace.php?link=$lien\"  target='_blank' title=\"$mess_clic_aff_ress\"><B>$tit</B></A></DIV></TD>";
      }
    }
    else
      echo "<td align='left'>$tit</td>";
    $toutes = (isset($toutes)) ? $toutes: '' ;
    echo "<td align='left'>".html_entity_decode($description,ENT_QUOTES,'ISO-8859-1')."</td>";
    $lien = "prescription.php?id_util=$user&identite=1&affiche_fiche_app=1";
    $lien = urlencode($lien);
    if (isset($objet) && $objet != "perso")
      echo "<td align='left' valign='top'><DIV id='sequence'><a href=\"trace.php?link=$lien\" title=\"$mess_suite_fp\">$prenom $nom</A></DIV></td>";
    else
      echo "<td align='left' valign='top'>$prenom $nom</td>";
    if ((isset($user) && $user == $id_user) || $typ_user == "ADMINISTRATEUR")
    {
      $lien = "favoris.php?modifier=1&num=$num&objet=$objet&seq=$sequence&toutes=$toutes";
      $lien = urlencode($lien);
      echo "<td height='20' align='middle' valign='top'><a href=\"trace.php?link=$lien\"><IMG SRC=\"images/repertoire/icoGrenomfich.gif\" height=\"20\" width=\"20\" ALT=\"$mess_modif_fav\" BORDER=0></A></td>";
      $lien = "favoris.php?supprimer=1&seq=$sequence&num=$num&objet=$objet&toutes=$toutes";
      $lien = urlencode($lien);
      echo "<td height='20' align='middle' valign='top'><a href=\"trace.php?link=$lien\"><IMG SRC=\"images/messagerie/icoGpoubel.gif\" height=\"20\" width=\"15\" ALT=\"$mess_supp_fav\" BORDER=0></A></td>";
    }
    echo "</tr>";
  $i++;
  }
  echo "<TR><TD colspan=3>";
  echo "</TD></TR></TABLE></TD></TR></TABLE></TD></TR></TABLE>";
  echo "</BODY></HTML>";
}
?>
