<?php
if (!isset($_SESSION)) session_start();
// Affichage des connections d'un utilisateur ordonées par date
require '../admin.inc.php';
require '../fonction.inc.php';
include ('../include/UrlParam2PhpVar.inc.php');
require "../lang$lg.inc.php";
require '../fonction_html.inc.php';
require '../class/class_admin.php';
//include ("click_droit.txt");
dbConnect();
if (!empty($_GET['linker']) && $_GET['linker'] != '')
{
  ForceFileDownload(urldecode($_GET['linker']),'zip');
  exit();
}
include ('../style.inc.php');
?>
<div id="affiche" class="Status"></div>
<div id="mon_contenu" class="cms"></div>
<?php
if ($sup_connection == 1)
{
    $effacer = mysql_query("delete from log where log_cdn=$num");
    echo notifier($mess_admin_connect_sup);
}
if ($connection == 1)
{
    if ((isset($jour) &&  $jour == 1))
    {
      $ch_date = explode ("-",$dt);
      $dter = "$ch_date[2]-$ch_date[1]-$ch_date[0]";
      $titre = "$mess_jour_connect_dt $dter" ;
    }
    elseif((isset($stats) && $stats == 1) && (isset($le_mois) && $le_mois != ''))
    {
      $titre = "$mess_admin_journal_connect_deb ".le_mois(intval($le_mois))." ".$annee;
    }
    else
    {
       $nom  = GetdataField ($connect,"select util_nom_lb from utilisateur where util_cdn='$id_util'","util_nom_lb");
       $prenom  = GetdataField ($connect,"select util_prenom_lb from utilisateur where util_cdn='$id_util'","util_prenom_lb");
       $titre = "$mess_admin_journal_connect_deb $prenom $nom" ;
       $appNom=$prenom."_".$nom;
    }
    if ($message_supp !="")
      echo "<CENTER><FONT COLOR=white size=3><B>$message_supp</B></FONT><P>";
    if (isset($jour) && $jour == 1 && isset($stats) && $stats == 1)
      $sql = "select * from log where date_debut='$dt' order by heure_debut ASC";
    elseif ((isset($jour) && $jour == 1 && (!isset($stats) || (isset($stats) && $stats != 1))) && $typ_user == 'ADMINISTRATEUR')
      $sql= "select * from log where date_debut='$dt' order by date_debut,heure_debut,login ASC";
    elseif ((isset($jour) && $jour == 1 && (!isset($stats) || (isset($stats) && $stats != 1))) && $typ_user != 'ADMINISTRATEUR')
      $sql= "select * from log where login='$logue' and date_debut='$dt' order by date_debut,heure_debut ASC";
    elseif (isset($par_mois) && $par_mois == 1)
      $sql= "select * from log where login='$logue' group by MONTH(date_debut),YEAR(date_debut) order by date_debut,heure_debut ASC";
    elseif (isset($integrale) && $integrale == 1)
      $sql= "select * from log where login='$logue' order by date_debut,heure_debut ASC";
    elseif (isset($logue) && $logue != '' && (!isset($jour) || (isset($jour) && $jour != 1)))
      $sql = "select * from log where login='$logue' AND MONTH(date_debut)= '$le_mois' AND YEAR(date_debut)= '$annee' ORDER BY date_debut,heure_debut ASC";
    elseif (((isset($logue) && $logue == '') || !isset($logue)) && (!isset($jour) || (isset($jour) && $jour != 1)))
      $sql = "select * from log where MONTH(date_debut)= '$le_mois' AND YEAR(date_debut)= '$annee' ORDER BY date_debut,heure_debut ASC";
    $liste = mysql_query($sql);
    $nbr = mysql_num_rows($liste);
    $i = 0;
    if ($nbr == 0)
    {
      echo "<CENTER><TABLE bgColor='#298CA0' cellspacing='2'><TR><TD>";
      echo "<TABLE bgColor='#FFFFFF' cellspacing='1'  cellpadding = '0' width='100%'>";
      echo "<TR><TD background=\"../images/fond_titre_table.jpg\" colspan='2' height='40' align='center' valign='center'><Font size='3' color='#FFFFFF'><B>$titre</B></FONT></TD></TR>";
      echo "<tr><td>".aide_div('connexions',4,0,3,3)."</td></tr>";
      echo "<tr><td  height='20' align='center'>";
      echo "<FONT size='2'>$adm_noconnect</FONT>";
      echo "</TD></TR></TABLE></TD></TR></TABLE>";
    exit;
    }
    $bgcolor2 = '#2B677A';
    $bgcolor1 = '#F8F2E4';
    echo "<CENTER><TABLE bgColor='#298CA0' cellspacing='2' width='98%'><TR><TD>";
    echo "<TABLE bgColor='#FFFFFF' cellspacing='1' cellpadding = '0' width='100%'>";
    echo "<TR><TD background=\"../images/fond_titre_table.jpg\" height='40' align='center' valign='center'><Font size='3' color='#FFFFFF'><B>$titre</B></FONT></TD></TR>";
      echo "<tr><td>".aide_div('connexions',4,0,3,3);
      if ((isset($par_mois) && $par_mois == 1) || (isset($integrale) && $integrale == 1))
      {
         echo '<div style="float:left;margin-left:8px;padding:2px;">';
         $lien = urlencode("admin/connections.php?connection=1&logue=$logue&annu=$annu&id_grp=$id_grp&integrale=1&id_util=$id_util&entantqueresp=$entantqueresp");
         echo '<a href="../trace.php?link='.$lien.'" target="main" class="bouton_new">Afficher toutes ses connexions</a>';
         echo '</div>';
         echo '<div style="float:left;margin-left:8px;padding:2px;">';
         $lien = urlencode("admin/connections.php?lance_csv=1&connection=1&logue=$logue&annu=$annu&id_grp=$id_grp&integrale=1&id_util=$id_util&entantqueresp=$entantqueresp");
         echo '<a href="../trace.php?link='.$lien.'" target="main" class="bouton_new" '.
              'title="Rappel: une durée de connexion ne signifie en aucun cas qu\'un travail a été fourni durant ce laps de temps.'.
              ' Pour une meilleure prise en compte du travail fourni, il faut s\'en remettre à l\'historique d\'activité.">'.
              'Exporter toutes ses connexions en CSV</a>';
         echo '</div>';
      }
      echo "</td></tr>";
    echo "<TR><TD><TABLE cellpadding=0 cellspacing = '0' width='100%' bgcolor='#FFFFFF'>";
    echo "<TR><TD><TABLE cellpadding=10 cellspacing = '1' width='100%'><tr bgcolor=$bgcolor2>";
    if ($par_mois != 1)
    {
      if ($jour != 1)
      {
        echo "<td height='20' align='left'><FONT COLOR=white><b>$mess_admin_date_debut</b></FONT></td>";
      }
      echo "<td height='20' align='left'><FONT COLOR=white><b>$mess_admin_heure_debut</b></FONT></td>";
      echo "<td height='20' align='left'><FONT COLOR=white><b>$mess_admin_date_fin</b></FONT></td>";
      echo "<td height='20' align='left'><FONT COLOR=white><b>$mess_admin_heure_fin</b></td>";
      if ((isset($stats) && $stats == 1) || (isset($jour) && $jour == 1))
         echo "<td height='20' align='left'><FONT COLOR=white><b> $mess_pesr_connect </b></FONT></td>";
      echo "<td height='20' align='left'><FONT COLOR=white><b>$mess_admin_total</b></FONT></td>";
      echo "<td height='20' align='left'><FONT COLOR=white><b>$mess_admin_machine</b></FONT></td>";
      echo "<td height='20' align='left'><FONT COLOR=white><b>$mess_admin_adr_ip</b></FONT></td>";
      if ($typ_user == "ADMINISTRATEUR" && $logue != "" && !isset($integrale))
         echo " <td height='20' align='left'><FONT COLOR=white><b>$mess_ag_supp</b></FONT></td>";
    }
    else
    {// fin de par_mois != 1
        echo "<td height='20' align='left' width='50%'><FONT COLOR=white><b>$mess_mois</b></FONT></td>";
        echo "<td height='20' align='left' width='50%'><FONT COLOR=white><b>$mess_admin_total</b></td>";
    }
    if (isset($integrale) && $integrale == 1 && isset($lance_csv) && $lance_csv == 1)
    {
       $fichier_csv = "$mess_admin_date_debut;$mess_admin_heure_debut;$mess_admin_date_fin;$mess_admin_heure_fin;".
             "$mess_admin_total;$mess_admin_machine;$mess_admin_adr_ip\n\n";
    }
    echo "</tr>";
  while ($i < $nbr)
  {
    $num = mysql_result($liste,$i,"log_cdn");
    $dtd = mysql_result($liste,$i,"date_debut");
    $dt_rech = $dtd;
      $ch_date = explode ("-",$dtd);
      $dtd = "$ch_date[2]-$ch_date[1]-$ch_date[0]";
      $le_mois = $ch_date[1];
      $annee = $ch_date[0];
    $hrd = mysql_result($liste,$i,"heure_debut");
    $dtf = mysql_result($liste,$i,"date_fin");
      $ch_date = explode ("-",$dtf);
      $dtf = "$ch_date[2]-$ch_date[1]-$ch_date[0]";
    $hrf = mysql_result($liste,$i,"heure_fin");
    $total = mysql_result($liste,$i,"duree");
    $tot_gen = $tot_gen+$total;
    $machine = mysql_result($liste,$i,"serveur");
    $loguer = mysql_result($liste,$i,"login");
    $adrip = mysql_result($liste,$i,"ip");
    if ((isset($jour) && $jour == 1) || (isset($par_mois) && $par_mois == 1) || (isset($stats) &&  $stats == 1))
    {
       $nom  = GetdataField ($connect,"select util_nom_lb from utilisateur where util_login_lb='$loguer'","util_nom_lb");
       $prenom  = GetdataField ($connect,"select util_prenom_lb from utilisateur where util_login_lb='$loguer'","util_prenom_lb");
       $id_util  = GetdataField ($connect,"select util_cdn from utilisateur where util_login_lb='$loguer'","util_cdn");
    }
    if ($le_mois > 0 && (!isset($par_mois) || (isset($par_mois) && $par_mois != 1)))
    {
       $nbj = mysql_result(mysql_query("select count(*) from log where date_debut='$dt_rech' and heure_debut='$hrd' and login='$loguer'"),0);
       $req_mois_an = mysql_query("SELECT SUM(duree) FROM log WHERE  date_debut='$dt_rech' and heure_debut='$hrd' and login='$loguer'");
       $total = mysql_result($req_mois_an,0);
       $total_mois +=$total;
    }
    if ($par_mois == 1)
    {
       $req_mois_an = mysql_query("SELECT SUM(duree) FROM log WHERE MONTH(date_debut) = '$le_mois' AND YEAR(date_debut) = '$annee' AND login = '$logue'");
       $total = mysql_result($req_mois_an,0);
       $duree = duree_calc($total);
       $total_mois +=$total;
    }
    echo couleur_tr($i+1,'');
   if ($par_mois != 1)
   {
     if (((isset($stats) && $stats == 1) && !isset($jour)) || (!isset($jour) || (isset($jour) && $jour != 1)))
     {
           $lien = "admin/connections.php?stats=$stats&jour=1&connection=1&dt=$dt_rech&logue=$logue";
           $lien = urlencode($lien);
           echo"<td height='20' align='left'><A HREF=\"../trace.php?link=$lien\"  target='main' ".bulle($mess_adm_date,"","RIGHT","ABOVE",200)."$dtd</A></td>";
     }
     else // elseif ($jour != 1 && $typ_user != 'ADMINISTRATEUR')
        echo "<td height='20' align='left'>$dtd</FONT></td>";
     echo "<td height='20' align='left'>$hrd</FONT></td>";
     if ($jour != 1)
        echo "<td height='20' align='left'>$dtf</FONT></td>";
        echo "<td height='20' align='left'>$hrf</td>";
     if ($total == 0 && $hrf == $hrd)
        $duree = $delog_bad;
     else
        $duree = duree_calc($total);
     if ((isset($stats) &&  $stats == 1) || (isset($jour) &&  $jour == 1))
        echo "<td height='20' align='left'>$nom $prenom</FONT></td>";
     echo "<td height='20' align='left'>$duree</FONT></td>";
  }else{
      $lien = "admin/connections.php?annee=$annee&le_mois=$le_mois&connection=1&logue=$logue&id_util=$id_util";
      $lien = urlencode($lien);
      $lemois=intval($le_mois);
      echo "<td height='20' align='left'><DIV id='sequence'><a href=\"../trace.php?link=$lien\"  ".
           "target='main'> ".le_mois($lemois)." ".$annee."</A></DIV></td>";
      echo "<td height='20' align='left'>$duree</FONT></td>";
  }
  if ($par_mois != 1)
  {
     echo "<td height='20' align='left'>$machine</td>";
     echo "<td height='20' align='left'>$adrip</td>";
    if ($typ_user == "ADMINISTRATEUR" && $logue != "")
    {
         $req_grp = mysql_query ("select grp_cdn from groupe");
         if (mysql_num_rows($req_grp) > 0)
         {
            $nbr_presc = 0;
            while ($item = mysql_fetch_object($req_grp))
            {
                $num_grp = $item->grp_cdn;
                $req_presc = mysql_query ("select count(presc_cdn) from prescription_$num_grp where
                                           presc_utilisateur_no = '$id_util' OR
                                           presc_formateur_no = '$id_util' OR
                                           presc_prescripteur_no = '$id_util'");
                $nbr_presc += mysql_result($req_presc,0);
            }
         }
         $req_log = mysql_query ("select count(log_cdn) from log where login = '$logue'");
         $nbr_log = mysql_result($req_log,0);
         if ($nbr == 1)
           $lien = "admin/connections.php?sup_connection=1&connection=1&num=$num&logue=$logue&dt=$dt&jour=$jour&par_mois=1";
         else
           $lien = "admin/connections.php?sup_connection=1&connection=1&num=$num&logue=$logue&dt=$dt&jour=$jour&le_mois=$le_mois&annee=$annee";
         $lien = urlencode($lien);
         if (($nbr_presc == 0 || ($nbr_presc > 0 && $nbr_log > 1) ) &&  !isset($integrale))
           echo "<td height='20' align='left'><A href=\"javascript:void(0);\" onclick=\"javascript:return(confm('../trace.php?link=$lien'));\" ".
                 bulle($mess_admin_label_sup,"","LEFT","ABOVE",100).
                "<IMG SRC=\"../images/messagerie/icopoubelressour.gif\" border='0'></A></td>";
    }
  }
  echo "</tr>";
  if (isset($integrale) && $integrale == 1 && isset($lance_csv) && $lance_csv == 1)
     $fichier_csv .= "$dtd;$hrd;$dtf;$hrf;$duree;$machine;$adrip\n";
  $i++;
  }
  if ($par_mois != 1 && !$le_mois)
  {
    if ($tot_gen < 60 && $tot_gen > 0)
      $message = "$tot_gen $mn";
    elseif ($tot_gen > 60)
    {
      $heur_tot=floor($tot_gen/60);
      $min = $tot_gen- $heur_tot*60;
      if ($min > 0)
         $message = "$heur_tot $h et $min $mn";
      else
         $message ="";
    }
    elseif ($tot_gen == 0)
      $message = $no_connect;
  }
  elseif ($par_mois != 1 && $le_mois > 0)
  {
      $duree = duree_calc($total_mois);
      $message = $duree;
  }
  else
  {
      $duree = duree_calc($total_mois);
      $message = $duree;
  }

  if ($par_mois != 1)
     echo "<TR><td nowrap><B>$nbr $mess_connect</B></td><td height='20' align='left' nowrap><B>$message</B>";
  else
     echo "<TR><td nowrap>";
  echo "</TD></TR></TABLE>";
  echo "</TD></TR><TR><TD align=left><A HREF=\"javascript:history.back();\" onmouseover=\"img_annonce.src='../images/fiche_identite/boutretourb.gif';return true;\" onmouseout=\"img_annonce.src='../images/fiche_identite/boutretour.gif'\">";
  echo "<IMG NAME=\"img_annonce\" SRC=\"../images/fiche_identite/boutretour.gif\" BORDER='0' onLoad=\"tempImg=new Image(0,0); tempImg.src='../images/fiche_identite/boutretourb.gif'\"></A>";
  echo "</TD></TR></TABLE></TD></TR></TABLE></TD></TR></TABLE>";
  if (isset($integrale) && $integrale == 1 && isset($lance_csv) && $lance_csv == 1)
  {
    $fichier_csv .= "Nombre total de connexions   : $nbr ;;;Durée totale  : $message;;;\n";
    $sortie_csv = modif_az_qw($appNom)."_cnx.csv";
    $dir_app_csv = "../ressources/".$login."_".$id_user."/ressources/".$sortie_csv;
    $fp = fopen($dir_app_csv, "w+");
        $fw = fwrite($fp, $fichier_csv);
    fclose($fp);
    chmod($dir_app_csv,0775);
    $linker = $dir_app_csv;
    echo "<script language='JavaScript'>";
          echo "setTimeout(\"document.location.replace('connections.php?linker=".urlencode($linker)."')\",1500);";
          echo 'setTimeout("$(\'#alerteCsv\').css(\'display\',\'none\')",15000);';
    echo "</script>";
    echo '<div id="alerteCsv" style="position:absolute;width:500px;top:250px;left:400px;padding:50px;border:2px solid red;font-size:34px;color:red;background:#eee;">';
        echo 'Téléchargement du CSV en cours';
    echo '</div>';
    exit;
  }
}
  echo "</BODY></HTML>";
echo '<div id="mien" class="cms"></div>';
exit;
?>
