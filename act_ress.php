<?php
include ("include/UrlParam2PhpVar.inc.php");
if (!isset($_SESSION)) session_start();
if (!isset($_SESSION['id_user']) || $_SESSION['id_user'] == "")
{
  exit();
}
require "admin.inc.php";
require 'fonction.inc.php';
require "lang$lg.inc.php";
//include ("click_droit.txt");
dbConnect();
//de quel type est l'utilisateur (apprenant, formateur, administrateur)
include 'style.inc.php';
$nom_ress = GetdataField ($connect,"select ress_titre from ressource_new where ress_cdn ='$id_ress'","ress_titre");
$titre = "$mrc_lien_ress : $nom_ress";
echo "<CENTER><TABLE bgColor='#298CA0' cellspacing='2' width='100%'><TR><TD>";
echo "<TABLE bgColor='#FFFFFF' cellspacing='1'  cellpadding='0' width='100%'>";
echo "<TR><TD background=\"images/fond_titre_table.jpg\" colspan='3' height='34' align='center' valign='center'><Font size='3' color='#FFFFFF'><B>$titre</B></FONT></TD></TR>";
echo "<TR><TD width=100% colspan='3'><TABLE width=100%  cellpadding='5' cellspacing='5'>";
echo "<TR bgcolor='#2B677A'><TD align='center'><FONT Color='white'><B>$msq_activite</B></FONT></TD><TD align='center'><FONT Color='white'><B>$msq_seq</B></FONT></TD><TD align='center'><FONT Color='white'><B>$msq_parc</B></FONT></TD></TR>";
$act_suivi = mysql_query ("select * from activite where activite.act_ress_no = $id_ress");
$Nb_act = mysql_num_rows ($act_suivi);
$i=0;
while ($i < $Nb_act){
  $nom_act = mysql_result($act_suivi,$i,"act_nom_lb");
  $lien_act = mysql_result($act_suivi,$i,"act_cdn");
  if (($i/2) == (floor($i/2)))
    echo "<TR bgcolor= \"#F4F4F4\">";
  else
    echo "<TR bgcolor= '#FFFFFF'>";
  echo "<TD align='left'>$nom_act</TD><TD align='left'>";
  $req_act_seq = mysql_query ("select sequence.seq_cdn,sequence.seq_titre_lb from activite,sequence where activite.act_cdn=$lien_act AND activite.act_seq_no = sequence.seq_cdn");
  $nb_act_seq = mysql_num_rows ($req_act_seq);
  $j=0;
  if ($nb_act_seq > 0){
    while ($j < $nb_act_seq){
      $nom_seq = mysql_result($req_act_seq,$j,"seq_titre_lb");
      $lien_seq = mysql_result($req_act_seq,$j,"seq_cdn");
      echo "$nom_seq</TD><TD align='left'>";
      $req_seq_parc = mysql_query ("SELECT seqparc_parc_no from sequence_parcours where seqparc_seq_no = $lien_seq");
      $nb_seq_parc = mysql_num_rows ($req_seq_parc);
      $k=0;
      if ($nb_seq_parc > 0){
        while ($k < $nb_seq_parc){
           $lien_parc = mysql_result($req_seq_parc,$k,"seqparc_parc_no");
           $nom_parc = getdatafield ($connect,"SELECT parcours_nom_lb from parcours where parcours_cdn = $lien_parc","parcours_nom_lb");
           echo "<LI>$nom_parc</LI>";
          $k++;
        }
      }
      $j++;
    }
  }
  echo "</TD></TR>";
  $i++;
}

//***********************************************************************************************
echo "</TABLE>";
echo "</TD></TR></TABLE></TD></TR></TABLE>";

?>