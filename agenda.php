<?php
if (!isset($_SESSION)) session_start();
// pour avoir acces à l'envoi de message decommenter les ligne if(.....) et envoi = mail_attachement
include ("include/UrlParam2PhpVar.inc.php");
if (!isset($_SESSION['id_user']) || $_SESSION['id_user'] == "")
{
  exit();
}
require "admin.inc.php";
require 'agenda.inc.php';
require 'fonction.inc.php';
require 'fonction_html.inc.php';
require 'langues/agenda.inc.php';
require "lang$lg.inc.php";
if ($lg == "ru"){
  $code_langage = "ru";
  $charset = "Windows-1251";
}elseif ($lg == "fr"){
  $code_langage = "fr";
  $charset = "iso-8859-1";
}elseif ($lg == "en"){
  $code_langage = "en";
  $charset = "iso-8859-1";
}
//include "click_droit.txt";
dbConnect();
include ('include/varGlobals.inc.php');
$nom_user = $_SESSION['name_user'] ;
$prenom_user = $_SESSION['prename_user'];
$person = $nom_user;
$bkg = GetDataField($connect,"select param_etat_lb from param_foad where param_typ_lb = 'bkg'","param_etat_lb");

if (isset($tuteur) && $tuteur == 1 && $typ_user == "APPRENANT")
{
?>
<HTML>
<HEAD>
<META HTTP-EQUIV="Content-Type" content="text/html; charset=<?php  echo $charset;?>">
<META http-equiv="Content-Language" content="<?php  echo $code_langage ;?>">
<META HTTP-EQUIV="Expires" CONTENT="Fri, Jan 01 1900 00:00:00 GMT">
<META HTTP-EQUIV="Cache-Control" CONTENT="no-cache, must-revalidate">
<META HTTP-EQUIV="Pragma" CONTENT="no-cache">
<link rel="stylesheet" type="text/css" href="<?php echo $monURI;?>/admin/style_admin.css" />
<STYLE>
//BODY { font-family: arial; font-size: 11px; color: #333333 }
TD   { font-family: arial; font-size: 11px; color: #333333 }
TH   { font-family: arial; font-size: 11px; color: #333333 }
A         {font-family:arial;font-size:11px;color:#24677A;text-decoration:none}
A:link    {font-family:arial;font-size:11px;color:#24677A;font-weight:bold}
A:visited {font-family:arial;font-size:11px;color:#24677A;font-weight:bold}
A:hover   {font-family:arial;font-size:11px;color:#D45211;font-weight:bold}
A.off     {font-family:arial;font-size:11px;color:#24677A;font-weight:bold}

#titre A:link{background-repeat:no-repeat;background-position:1% 50%;color:#3BACC4;}
#titre A:visited{background-repeat:no-repeat;background-position:1% 50%;color:#3BACC4;}
#titre A:hover {background-repeat:no-repeat;background-position:1% 50%;color:#D45211;}

#menu A:link{background-repeat:no-repeat;background-position:1% 50%;color:#002D44;}
#menu A:visited{background-repeat:no-repeat;background-position:1% 50%;color:#002D44;}
#menu A:hover {background-repeat:no-repeat;background-position:1% 50%;color:#D45211;}

#sequence A:link{background-repeat:no-repeat;background-position:1% 50%;color:#24677A;}
#sequence A:visited{background-repeat:no-repeat;background-position:1% 50%;color:#24677A;}
#sequence A:hover {background-repeat:no-repeat;background-position:1% 50%;color:#D45211;}

#seqinv A:link{background-repeat:no-repeat;background-position:1% 50%;color:#D45211;}
#seqinv A:visited{background-repeat:no-repeat;background-position:1% 50%;color:#D45211;}
#seqinv A:hover {background-repeat:no-repeat;background-position:1% 50%;color:#24677A;}

#parcours A:link{background-repeat:no-repeat;background-position:1% 50%;color:#002D44;}
#parcours A:visited{background-repeat:no-repeat;background-position:1% 50%;color:#002D44;}
#parcours A:hover {background-repeat:no-repeat;background-position:1% 50%;color:#FFFFFF;}

#parcseqtype A:link{background-repeat:no-repeat;background-position:1% 50%;color:red;}
#parcseqtype A:visited{background-repeat:no-repeat;background-position:1% 50%;color:red;}
#parcseqtype A:hover {background-repeat:no-repeat;background-position:1% 50%;color:#D45211;}

.clq {LEFT: 0px; VISIBILITY: hidden; POSITION: absolute; TOP: 0px}
.mar { font-family: arial;font-size:9px;;color:'#800000' }
.small {font-family:arial;color:navy;font-size:11px;}
.admin {font-family:arial;color:#9999FF;font-size:11px}
.texte {font-family:arial;color:navy;font-size:11px}
.Softricks_Calendar {
        position: absolute;
        visibility: visible;
        top: 200;
        left: 10;
        height: 250;
        width: 260;
}
<?php if ($typ_user == 'APPRENANT' && $nombre_groupes > 1)
{?>
<!--
#slidemenubar, #slidemenubar2{
position:absolute;
border:1.5px solid black;
line-height:20px;
}
-->
<?php
}
?>
</STYLE>
<script type="text/javascript" src="OutilsJs/Alert2/alert2.js"></script>

<SCRIPT  LANGUAGE="JavaScript1.2" SRC="calendrier_<?php echo $lg;?>.js"></SCRIPT>

<SCRIPT LANGUAGE="JavaScript">
function popupload(cible,nom,lg,ht) {
//Javascript:popupload('telecharger.pgi?cmd=frame','telecharger','470','280');
  var win = window.open(cible, nom, 'width='+lg+',height='+ht+',resizable=yes,scrollbars=yes,status=yes,menubar=no,toolbar=no,location=no,directories=no,closed=no,opener=no');
}
function appel_w(sel_val) {
  var fset=sel_val.substring(0,2);
  var f2=sel_val;
  var url1 = ""+f2+"";
  if ( fset == "tr" )
   parent.main.location=url1
}
function appel_wpop(sel_val) {
  var fset=sel_val.substring(0,2);
  var f2=sel_val;
  var url1 = ""+f2+"";
  if ( fset == "tr" )
   window.open('','','width=680,height=380,resizable=yes,status=no').location=url1
}
</script>
<script language="javascript" src="functions.js"></script>
<script language="javascript">
function TryCallFunction() {
        var sd = document.MForm.mydate1.value.split("\/");
        document.MForm.iday.value = sd[1];
        document.MForm.imonth.value = sd[0];
        document.MForm.iyear.value = sd[2];
}
function TryCallFunction1() {
        var sd = document.MForm.ma_date.value.split("\/");
        document.MForm.iday1.value = sd[1];
        document.MForm.imonth1.value = sd[0];
        document.MForm.iyear1.value = sd[2];
}

function Today() {
        var dd = new Date();
        return((dd.getMonth()+1) + "/" + dd.getDate() + "/" + dd.getFullYear());
}
function popup(f,nom, w, h) {
   window.open(f, nom, 'resizable,screenX=0,screenY=0,scrollbars=yes,menubar=yes,width=' + w + ',height=' + h);
}

function tinypopup(f,nom, w, h) {
   window.open(f, nom, 'resizable,screenX=0,screenY=0,scrollbars=no,menubar=no,width=' + w + ',height=' + h);
}
msgconfm="<?php  echo $mess_admin_valid_modif;?>"
function confm() {
        if ( confirm(msgconfm) )
                return(true);
        return(false);
}
msgconf="<?php  echo $mess_admin_valid_supp;?>"
function confm(url) {
   ShowAlert2('JavaScript:document.location.replace("'+url+'")',
              'Confirmation',
              '<?php  echo "$mess_admin_valid_supp <br />$mess_op_irrev";?>',
              '<?php echo $adresse_http;?>/images/Exclamation.gif',
              ['Confirmer', 'Annuler'],
              ['JavaScript:HideAlert2(1,"'+url+'")','JavaScript:HideAlert2(2,"'+url+'")'],
              400,
              '<?php echo $adresse_http;?>/images/close.gif',
              url
              );

}
function conf() {
        if ( confirm(msgconf) )
                return(true);
        return(false);
}
msgconfv="<?php  echo $mess_gen_val_sais;?>"
function confv() {
        if ( confirm(msgconfv) )
                return(true);
        return(false);
}
msgconfseq="<?php  echo $mess_seq_presc;?>"
function confseq() {
        if ( confirm(msgconfseq) )
                return(true);
        return(false);
}
function makevisible(cur,which){
   if(document.getElementById){
        if (which==0){
           if(document.all)
              cur.filters.alpha.opacity=100
           else
              cur.style.setProperty("-moz-opacity", 1, "");
        }else{
           if(document.all)
              cur.filters.alpha.opacity=1
           else
              cur.style.setProperty("-moz-opacity", .01, "");
        }
   }
}
//--></SCRIPT>
<div id="overDiv" style="position:absolute; visibility:hidden;z-index:1000;"></div>
<SCRIPT LANGUAGE="JavaScript" SRC="overlib.js"><!-- overLIB (c) Erik Bosrup --></SCRIPT>
<TITLE>Formagri</TITLE>
</HEAD>
 <?php
 echo "<BODY bgcolor=\"$bkg\" marginwidth='0' marginheight='0' leftmargin='0'>";
 $bouton_gauche = "<TABLE cellpadding='0' cellspacing='0' border=0><TR><TD><IMG SRC='images/complement/cg.gif' border='0'></TD>".
                  "<TD background='images/complement/milieu.gif' nowrap align='center'><DIV id='sequence'>&nbsp;";
 $bouton_droite = "&nbsp;</DIV></TD><TD><IMG SRC='images/complement/cd.gif' border='0'></TD><TR></TABLE>";
}
else
{
  $_SESSION['acces'] = $acces;
  if ($typ_user== "APPRENANT" && $acces == "annonce_grp" && $menu_prov != 1)
  {
   $_SESSION['acces'] = $acces;
   $_SESSION['menu_prov'] = $menu_prov;
   $_SESSION['agenda'] = $agenda;
   $complement = 1;
   $_SESSION['complement'] = $complement;
   include "style.inc.php";
   echo "<TABLE background=\"images/menu/fond_logo_formagri.jpg\" border='0' cellspacing='0' cellpadding='0' width='100%'>";
   echo "<TR width='100%'><TD align='left' width='800'><IMG SRC=\"images/logo_formagri.jpg\" border='0'></TD>";
   $lien="delog.php";
   $lien = urlencode($lien);
   echo "<TD align='right' valign='bottom'><A href=\"trace.php?link=$lien\" title=\"$mess_dcnx\"".
        " onmouseover=\"img_dec.src='images/complement/boutdeconecb.gif';return true;\" onmouseout=\"img_dec.src='images/complement/boutdeconec.gif'\">";
   echo "<IMG NAME=\"img_dec\" SRC=\"images/complement/boutdeconec.gif\" BORDER='0'".
        " onLoad=\"tempImg=new Image(0,0); tempImg.src='images/complement/boutdeconecb.gif'\"></A></TD></TR></TABLE>";
   echo "<TABLE background=\"images/ecran-annonce/bando.gif\" cellspacing='0' cellpadding='0' width='100%' border='0'>".
        "<TR width='100%'><TD align='left' width='100%' valign='top'><IMG SRC=\"images/complement/soustitre.gif\" border='0'>".
        "</TD></TR></TABLE>&nbsp;<P>";
   }
   else
      include "style.inc.php";
}
$date_op = date("Y-m-d H:i:s" ,time());
$heure_fiche = substr($date_op,11);
$date_fiche = substr($date_op,0,10);
// traite les dates insérées via un formulaire
if ($iday && $imonth && $iyear)
  $date_prise="$imonth/$iday/$iyear";
elseif (isset($day) && isset($month) && isset($year))
  $date_prise = "$day/$month/$year";
if ($insere_reserve == 1)
  $date_reserve = $date_prise;
if ($rdv == 1 || $insere_occupation == 1)
  $date_rdv = $date_prise;
$date_cour = date ("Y/n/d");
$effacer = mysql_query("delete from rendez_vous where
                       rdv_date_dt < '$date_cour' AND
                       ((rdv_commentaire_cmt = '$mess_ag_libre' OR
                       rdv_commentaire_cmt = 'free') OR
                       (rdv_modecontact_lb='AGENDA' AND
                       rdv_util_no > 0 AND rdv_grp_no = 0))");
$date_en_cours = date("Y-n-d");
$nb_jours_cour =  nb_jours($date_en_cours);
if ($date_rdv || $date_reserve){
  if ($date_rdv)
  {
   $ch_date = ( strstr($date_rdv,'/')) ? explode ("/",$date_rdv) :  explode ("-",$date_rdv);
   $date_rdv_1 = "$ch_date[2]-$ch_date[1]-$ch_date[0]";
   $nb_jours = nb_jours($date_rdv_1);
  }else{
   $ch_date = ( strstr($date_reserve,'/')) ? explode ("/",$date_reserve) :  explode ("-",$date_reserve);
   $date_reserve_1 = "$ch_date[2]-$ch_date[1]-$ch_date[0]";
   $nb_jours = nb_jours($date_reserve_1);
  }
  if ($nb_jours <= $nb_jours_cour)
    $non_supp = 1;
}
//$rdv_query = mysql_query ("delete from rendez_vous where rdv_date_dt < '$date_cour' and rdv_apprenant_no = 0");
$date_messagerie = date("d/m/Y H:i:s" ,time());
//si c'est un apprenant, il doit d'abord choisir le tuteur
//Pas de rendez-vous le week-end
$jour_query = mysql_query ("select dayname('$date_reserve_1')");
$jour = mysql_result ($jour_query,0);
//if (($jour == 'Saturday' || $jour == 'Sunday') && $insere_reserve == 1)
//   $messg = "<center><font size='2' color='white'><b>$mess_ag_no__regr_weend</b></font><P>";
//elseif ($insere_reserve == 1 && $jour != 'Saturday' && $jour != 'Sunday'){
if ($insere_reserve == 1)
{
   if ($non_supp == 1)
   {
       $messg = "<center><font size='2' color='white'><b>$mess_ag_date_ant</b></font><P>";
      $non_supp = 1;
   }
   else
   {
     if ($type_reserve == "Présentiel")
     {
       if ($id_grp == -1)
          $mesg = $ag_noselect_grp;
       if ($cren2 < $cren1)
          $mesg = $hf_sup_hd;
       if ($salle == "")
         $mesg = $ag_num_salle;
       if ($typ_mat == "")
         $mesg = $ag_mat_act;
       if ($mesg != "")
       {
          echo "<CENTER><TABLE bgColor='#298CA0' cellspacing='2'><TR><TD>";
          echo "<TABLE bgColor='#FFFFFF' cellspacing='2' width='100%'>";
          echo "<TR><TD background=\"images/fond_titre_table.jpg\" colspan='2' height='34' align='center' valign='center'><Font size='3' color='#FFFFFF'><B>$mess_menu_agenda</B></FONT>";
          echo "<CENTER>&nbsp;<P><FONT SIZE='2'>$mesg</FONT></CENTER><P>&nbsp;";
          echo "</TD></TR></TABLE></TD></TR></TABLE>";
       exit();
       }
     }
     $adr_mail = GetDataField ($connect, "select util_email_lb from utilisateur where util_cdn=$id_user","util_email_lb");
     $date_mess = $date_reserve;
     $ch_date = explode("/",$date_reserve);
     $date_reserve = "$ch_date[2]-$ch_date[1]-$ch_date[0]";
     $id_rdv = Donne_ID ($connect,"select max(rdv_cdn) from rendez_vous");
     if ($type_reserve != "Présentiel")
     {
       $id_rdv = Donne_ID ($connect,"select max(rdv_cdn) from rendez_vous");
       $query = mysql_query ("insert into rendez_vous (rdv_cdn,rdv_util_no,rdv_tuteur_no,rdv_apprenant_no,rdv_grp_no,rdv_creneau_nb,rdv_commentaire_cmt,rdv_date_dt,rdv_modecontact_lb) values ('$id_rdv','$id_user','0','0','$id_grp','0','$type_reserve','$date_reserve','AGENDA')");
     }
     else
     {
//     $id_cren=0;
//     echo "<FONT color='white'>for ($id_cren=$cren1;$id_cren<$cren2;$id_cren++){"; exit;

       for ($id_cren=$cren1;$id_cren<$cren2+1;$id_cren++)
       {
         $id_rdv = Donne_ID ($connect,"select max(rdv_cdn) from rendez_vous");
         $query = mysql_query ("insert into rendez_vous (rdv_cdn,rdv_util_no,rdv_tuteur_no,rdv_apprenant_no,rdv_grp_no,rdv_creneau_nb,rdv_titre_lb,rdv_commentaire_cmt,rdv_date_dt,rdv_modecontact_lb) values ('$id_rdv','$id_user','0','0','$id_grp','$id_cren','$type_reserve','$salle-$typ_mat','$date_reserve','AGENDA')");
       }
     }
     $nom_grp = GetDataField ($connect,"select grp_nom_lb from groupe where grp_cdn  = $id_grp","grp_nom_lb");
     $message.= "$convoc_regroup : $nom_user $prenom_user\n";
     $message.= "$mess_ag_blo_jour\n";
     $message.= "$mess_ag_date $date_mess $pour $mpr_grpmin $nom_grp $endroit-$salle\n";
     $message.= "$mess_ag_mat: $typ_mat\n";
     $message.= "$mess_ag_mail1_4\n\n";
     $message_base = "$convoc_regroup : $nom_user $prenom_user<BR>$mess_ag_blo_jour <BR>
                $mess_ag_date $date_mess $mpr_grpmin $nom_grp $endroit-$salle<BR>
                $mess_ag_mat: $typ_mat<BR>
                $mess_ag_mail1_4";
     $msg = StripSlashes($message);
     $origine=$nom_user."  ".$typ_user;
     $nom ='';
     $userfile = "none";
// envoi d'Emails à chaque apprenant du groupe en question
     $liste = mysql_query("select * from utilisateur,utilisateur_groupe where utilisateur.util_cdn = utilisateur_groupe.utilgr_utilisateur_no AND utilisateur_groupe.utilgr_groupe_no=$id_grp");
     $nbr = mysql_num_rows($liste);
     if ($nbr>0){
       $i = 0;
       while ($i < $nbr){
          $num = mysql_result($liste,$i,"util_cdn");
          $email = mysql_result($liste,$i,"util_email_lb");
          $reply = $adr_mail;
          $from = $adr_mail;
          $sendto = $email;
          $subject = StripSlashes($mess_ag_ag);
          if (isset($_SESSION['IsOff']) && $_SESSION['IsOff'] == 0)
          {
              if ($sendto != "")
                  $envoi=mail_attachement($sendto , $subject , $msg , $userfile ,$reply, $nom, $from );
          }
          $max_numero = Donne_ID ($connect,"select max(mess_cdn) from messagerie");
          $requete = mysql_query("INSERT INTO messagerie (mess_cdn,envoyeur,origine,contenu,date,sujet,id_user) VALUES ($max_numero,'$id_user',\"$mess_ag_rsv\",\"$message_base\",'$date_messagerie','$subject',$num)");
       $i++;
       }
     }
     $from = $adr_mail;
     $reply = $adr_mail;
     $sendto = $adr_mail;
     $subject = StripSlashes($mess_ag_ag);
     $msg = StripSlashes($message);
     $origine=$nom_user."  ".$typ_user;
     $nom ='';
     $userfile = "none";
     if (isset($_SESSION['IsOff']) && $_SESSION['IsOff'] == 0)
     {
         if ($sendto != "")
            $envoi=mail_attachement($sendto , $subject , $msg , $userfile ,$reply, $nom, $from );
     }
     $max_numero = Donne_ID ($connect,"select max(mess_cdn) from messagerie");
     $requete = mysql_query("INSERT INTO messagerie (mess_cdn,envoyeur,origine,contenu,date,sujet,id_user) VALUES ($max_numero,'$id_user',\"$mess_ag_rsv\",\"$message_base\",'$date_messagerie','$subject',$id_user)");
   }
} //fin if insere_reserve...
if (($apprenant == 1 || $apprenant == $id_user) && $typ_user == "APPRENANT")
{
   $tuteur_query = mysql_query ("select distinct utilisateur.util_cdn,utilisateur.util_nom_lb,utilisateur.util_prenom_lb from
                                 utilisateur,tuteur where utilisateur.util_typutil_lb !='apprenant' and tuteur.tut_apprenant_no = $id_user and
                                 tuteur.tut_tuteur_no=utilisateur.util_cdn");
   $Nb_Tut = mysql_num_rows ($tuteur_query);
   $date_cour = date ("Y/n/d");
   //on recupere ds un champ l'annee pour pouvoir comparer avec l'annee cherchee ($ch_date_cour[0])
   $ch_date= explode ("/",$date_cour);
   //On echange les champs car l'annee est conservee ds $ch_date[2] ds tout le script
   $ch_date[2]=$ch_date[0];
   $i = 0;
   $possibilites = array();
   while ($i != $Nb_Tut)
   {
      $id = mysql_result ($tuteur_query,$i,"util_cdn");
      $nom = mysql_result ($tuteur_query,$i,"util_nom_lb");
      $prenom = mysql_result ($tuteur_query,$i,"util_prenom_lb");
      $req_lib = mysql_query("SELECT rdv_cdn from rendez_vous where rdv_tuteur_no = $id and rdv_util_no=0 and rdv_apprenant_no=0 and rdv_grp_no=0 and rdv_date_dt > '$date_en_cours' order by rdv_date_dt asc");
      $nomb_lib = mysql_num_rows($req_lib);
      $req_occupe = mysql_query("SELECT rdv_cdn from rendez_vous where rdv_tuteur_no = $id and rdv_util_no=0 and rdv_apprenant_no=$id_user and rdv_grp_no=0 and rdv_date_dt > '$date_en_cours' order by rdv_date_dt asc");
      $nomb_ocp = mysql_num_rows($req_occupe);
      if ($nomb_lib > 0)
      {
         $possibilites[$i] = $nomb_lib;
         $possible ++;
      }
      if ($nomb_lib > $nomb_ocp || $nomb_lib == $nomb_ocp)
        $nombre_items += $nomb_lib;
      elseif($nomb_lib < $nomb_ocp)
        $nombre_items += $nomb_ocp;
    $i++;
   }
 if ($possible > 0)
 {

      $lien = "tutorat.php";
      $lien= urlencode($lien);
      $hauteur = 30*$nombre_items+100;
      $mon_lien= "<div id='rdv' style=\"float:left;padding-left:4px;padding-right:8px;\">".
                 "<A HREF=\"javascript:void(0);\" class='bouton_new' ".
                 "onclick=\"javascript:window.open('trace.php?link=$lien','','left=100,top=300,width=750,height=$hauteur,resizable=yes,status=no')\">".
                 "<img src='images/clignotant.gif' border=0>&nbsp;$mess_prise_rdv</A></div>";
 }
}  //fin if ($apprenant)
if ($reserve == 1 && !$matiere)
{
   echo "<TABLE bgColor='#298CA0' cellspacing='2'><TR><TD>";
   echo "<TABLE bgColor='#FFFFFF' cellspacing='2' width='100%'>";
   echo "<form name='form' action=\"agenda.php?reserve=$reserve&tuteur=$tuteur&Prem=0&rech=$rech&tut=$tut&date=$date_reserve\" method='post'>";
   echo "<TR><TD background=\"images/fond_titre_table.jpg\" colspan='3' height='34' align='center' valign='center'><Font size='3' color='#FFFFFF'><B>$mess_ag_choix_reserve</B></FONT>";
   echo "</TD></TR><TR><TD><TABLE><TR><TD nowrap>&nbsp;</TD><TD nowrap>OUI</TD>";
   echo "<TD nowrap>NON</TD></TR><TR><TD nowrap>$mess_ag_ch_mat</TD>";
   echo "<TD><input type='checkbox' name='matiere' value = 'oui'></TD>";
   echo "<TD><input type='checkbox' name='matiere' value = 'non'></TD></TR><TR>";
   echo "</TD></TR><TR><TD align=left><A HREF=\"javascript:history.back();\" onmouseover=\"img_annonce.src='images/fiche_identite/boutretourb.gif';return true;\" onmouseout=\"img_annonce.src='images/fiche_identite/boutretour.gif'\">";
   echo "<IMG NAME=\"img_annonce\" SRC=\"images/fiche_identite/boutretour.gif\" BORDER='0' onLoad=\"tempImg=new Image(0,0); tempImg.src='images/fiche_identite/boutretourb.gif'\"></A>";
   echo "</TD><TD align='center' colspan='2'><A HREF=\"javascript:document.form.submit();\" onmouseover=\"img1.src='images/fiche_identite/boutvalidb.gif';return true;\" onmouseout=\"img1.src='images/fiche_identite/boutvalid.gif'\">".
         "<IMG NAME=\"img1\" SRC=\"images/fiche_identite/boutvalid.gif\" BORDER='0' onLoad=\"tempImg=new Image(0,0); tempImg.src='images/fiche_identite/boutvalidb.gif'\"></A>";
   echo "</TD></TR></TABLE></TD></TR></TABLE></form></BODY></HTML>";
 exit();
}
if ($reserve == 1 && $matiere == "non")
{
                  echo "<form name=\"MForm\" action=\"agenda.php?insere_reserve=$reserve&tuteur=$tuteur&Prem=0&rech=$rech&tut=$tut&date=$date_reserve\" method='post'>";
                  entete_simple($mess_ag_choix_reserve);
                  echo "<TR><TD colspan=4>";

                  echo "<TABLE bgColor='#FFFFFF' width=100% cellpadding='4'>";
                  echo "<tr><td colspan=4>$nordvdtjour</td></tr>";
                  echo "<TR><TD nowrap>";
                  echo $mess_ag_date_reserve;
                     echo "</TD>";
                                    $ch_date_cour= explode ("/",$date_cour);
                                    $day=$ch_date_cour[2];
                                    $month=$ch_date_cour[1];
                                    $year=$ch_date_cour[0];?>
                   <input type="hidden" name=txt_custom value="AppendOrReplace=Replace;AppendChar=';';CloseOnSelect=Yes;ReturnData=Date;Title=<?php  echo $calendrier?>;InlineX=550;InlineY=250;CurrentDate=Today;SelectAfter=Today-30;SelectBefore=Today+30;AllowWeekends=No;Resizable=Yes;CallFunction=TryCallFunction;PopupX=300;PopupY=300;Nav=Yes;SmartNav=Yes;Fix=No;WeekStart=1;Weekends=06">
                   <TD><INPUT TYPE="TEXT" class="INPUT" name="imonth" value="<?php  echo $day;?>" MAXLENGTH="2" size="2"></TD>
                   <TD><INPUT TYPE="TEXT" class="INPUT" name="iday" value="<?php  echo $month;?>" MAXLENGTH="2" size="2"></TD>
                   <TD><INPUT TYPE="TEXT" class="INPUT" name="iyear" value="<?php  echo $year;?>" MAXLENGTH="4" size="4"></TD>
                   <TD><input type="hidden" value="" name="mydate1">
                   <a href="javascript:show_calendar('MForm.mydate1','09', '2002', 'DD/MM/YYYY', 'INLINE', MForm.txt_custom.value);"
                            onmouseover="img_cal1.src='images/agenda/icocalendb.gif';return true;"
                            onmouseout="img_cal1.src='images/agenda/icocalend.gif'">
                            <IMG NAME="img_cal1" SRC="images/agenda/icocalend.gif" BORDER='0' valign='top' alt="<?php  echo $cal_click  ;?>"
                            onLoad="tempImg=new Image(0,0); tempImg.src='images/agenda/icocalendb.gif'"></A>
                  </TD><TR>
                     <TD nowrap>
                        <?php  echo $mess_ag_choix ;?>
                     </TD>
                     <TD colspan=4>
                        <SELECT  name="<?php  echo $mess_ag_typ_res;?>" class='SELECT'>
                         <OPTION VALUE="Stage"><?php  echo $mess_ag_stage ;?></OPTION>
                         <OPTION VALUE="Visite"><?php  echo $mess_ag_visite ;?></OPTION>
                         </SELECT>
                     </TD>
                  </TR>
                  <TR>
                    <TD nowrap>
                        <?php  echo $mess_gp_nom_grp ;?>
                     </TD>
                     <TD colspan=3>
                        <?php
                        if ($typ_user == "ADMINISTRATEUR")
                           Ascenseur ("id_grp","select distinct grp_cdn,grp_nom_lb from groupe where grp_flag_on = 1",$connect,$param);
                        elseif ($typ_user == "RESPONSABLE_FORMATION")
                           Ascenseur ("id_grp","select distinct grp_cdn,grp_nom_lb from groupe where grp_resp_no = $id_user AND  grp_flag_on = 1",$connect,$param);
                        ?>
                     </TD>
                  </TR>
                  <?php
                  echo boutret(1,0);
                  echo "</TD><TD align='center' colspan='3'><A HREF=\"javascript:document.MForm.submit();\" onmouseover=\"img1.src='images/fiche_identite/boutvalidb.gif';return true;\" onmouseout=\"img1.src='images/fiche_identite/boutvalid.gif'\">".
                       "<IMG NAME=\"img1\" SRC=\"images/fiche_identite/boutvalid.gif\" BORDER='0' onLoad=\"tempImg=new Image(0,0); tempImg.src='images/fiche_identite/boutvalidb.gif'\"></A>";
                  ?>
                </TD></TR></TABLE></td></tr></TD></TR></TABLE></TD></TR></TABLE>
               </form>
               <DIV ID="TOP">
                  <SCRIPT Language="Javascript" TYPE="text/javascript">
                       Calendar.CreateCalendarLayer(10, 275, "");
                  </SCRIPT>
               </DIV>
               </BODY></HTML>
<?php exit;
}
if ($reserve == 1 && $matiere == "oui")
{
                  echo "<form name=\"MForm\" action=\"agenda.php?insere_reserve=$reserve&tuteur=$tuteur&Prem=0&rech=$rech&tut=$tut&date=$date_reserve\" target='main' method='post'>";
                  entete_simple($mess_ag_choix_reserve);
                  echo "<TR><TD colspan='2' width='100%'>";
                  ?>

                  <TABLE bgcolor='#FFFFFF' width=100% cellpadding="4" border=0 >
                  <?php echo "<tr><td  align=center></td><td style=\"font-weight:bold;\">$nordvdtjour</td></tr>";?>
                  <TR>
                     <TD nowrap align='right'>
                         <?php  echo $mess_ag_date_reserve ;?>
                     </TD>
                      <?php
                                    $ch_date_cour= explode ("/",$date_cour);
                                    $day=$ch_date_cour[2];
                                    $month=$ch_date_cour[1];
                                    $year=$ch_date_cour[0];
                      ?>
                    <input type="hidden" name=txt_custom value="AppendOrReplace=Replace;AppendChar=';';CloseOnSelect=Yes;ReturnData=Date;Title=<?php  echo $calendrier;?>;InlineX=550;InlineY=250;CurrentDate=Today;SelectAfter=Today-30;SelectBefore=Today+30;AllowWeekends=No;Resizable=Yes;CallFunction=TryCallFunction;PopupX=300;PopupY=300;Nav=Yes;SmartNav=Yes;Fix=No;WeekStart=1;Weekends=06">
                    <TD align='left'><INPUT TYPE="TEXT" class="INPUT" name="imonth" value="<?php  echo $day;?>" MAXLENGTH="2" size="2">
                    <INPUT TYPE="TEXT" class="INPUT" name="iday" value="<?php  echo $month;?>" MAXLENGTH="2" size="2">
                    <INPUT TYPE="TEXT" class="INPUT" name="iyear" value="<?php  echo $year;?>" MAXLENGTH="4" size="4">
                    <input type="hidden" value="" name="mydate1">
                    <a href="javascript:show_calendar('MForm.mydate1','09', '2002', 'DD/MM/YYYY', 'INLINE', MForm.txt_custom.value);"
                             onmouseover="img_cal1.src='images/agenda/icocalendb.gif';return true;"
                             onmouseout="img_cal1.src='images/agenda/icocalend.gif'">
                             <IMG NAME="img_cal1" SRC="images/agenda/icocalend.gif" BORDER='0' valign='bottom' alt="<?php  echo $cal_click  ;?>"
                             onLoad="tempImg=new Image(0,0); tempImg.src='images/agenda/icocalendb.gif'"></A>
                    </TD><TR>
                     <TD nowrap align='right'>
                        <?php  echo $mess_mail_de ;?>
                     </TD>
                     <TD><?php
                     echo "
                        <SELECT  name='cren1' class='SELECT' size='1'>
                         <OPTION VALUE='1'>8$h</OPTION>
                         <OPTION VALUE='2'>9$h</OPTION>
                         <OPTION VALUE='3'>10$h</OPTION>
                         <OPTION VALUE='4'>11$h</OPTION>
                         <OPTION VALUE='5'>12$h</OPTION>
                         <OPTION VALUE='6'>13$h </OPTION>
                         <OPTION VALUE='7'>14$h</OPTION>
                         <OPTION VALUE='8'>15$h</OPTION>
                         <OPTION VALUE='9'>16$h</OPTION>
                         <OPTION VALUE='10'>17$h</OPTION>
                         <OPTION VALUE='11'>18$h</OPTION>
                         <OPTION VALUE='12'>19$h</OPTION>
                         <OPTION VALUE='13'>20$h</OPTION>
                         <OPTION VALUE='14'>21$h</OPTION>
                         <OPTION VALUE='15'>22$h</OPTION>
                        </SELECT>"

                       ?>
                     </TD>
                  </TR>
                  <TR>
                     <TD nowrap align='right'>
                        <?php  echo $mess_mail_a ;?>
                     </TD>
                     <TD><?php
                     echo "
                        <SELECT  name='cren2' class='SELECT' size='1' >
                         <OPTION VALUE='1'>9$h</OPTION>
                         <OPTION VALUE='2'>10$h</OPTION>
                         <OPTION VALUE='3'>11$h</OPTION>
                         <OPTION VALUE='4'>12$h</OPTION>
                         <OPTION VALUE='5'>13$h</OPTION>
                         <OPTION VALUE='6'>14$h</OPTION>
                         <OPTION VALUE='7'>15$h</OPTION>
                         <OPTION VALUE='8'>16$h</OPTION>
                         <OPTION VALUE='9'>17$h</OPTION>
                         <OPTION VALUE='10'>18$h</OPTION>
                         <OPTION VALUE='11'>19$h</OPTION>
                         <OPTION VALUE='12'>20$h </OPTION>
                         <OPTION VALUE='13'>21$h</OPTION>
                         <OPTION VALUE='14'>22$h</OPTION>
                         <OPTION VALUE='15'>23$h</OPTION>
                        </SELECT>"
                        ?>
                     </TD>
                  </TR>
                     <input type="hidden" name="type_reserve" value="Présentiel">
                  <TR>
                     <TD nowrap align='right'>
                         <?php  echo $objet_ag ;?>
                     </TD>
                     <TD>
                         <INPUT TYPE="TEXT" class="INPUT" name="typ_mat" size="60">
                     </TD>
                  </TR>
                  <TR>
                     <TD nowrap align='right'>
                         <?php  echo $m_ref_lieu ;?>
                     </TD>
                     <TD>
                         <INPUT TYPE="TEXT" class="INPUT" name="salle" size="60">
                     </TD>
                  </TR>
                  <TR>
                    <TD nowrap align='right'>
                        <?php  echo $mess_gp_nom_grp ;?>
                     </TD>
                     <TD>
                        <?php
                        if ($typ_user == "ADMINISTRATEUR")
                          Ascenseur ("id_grp","select distinct grp_cdn,grp_nom_lb from groupe where grp_flag_on = 1",$connect,$param);
                        else
                          Ascenseur ("id_grp","select distinct grp_cdn,grp_nom_lb from groupe where grp_resp_no = $id_user and grp_flag_on = 1",$connect,$param);
                       ?>
                     </TD>
                  </TR>

                  <?php
                  echo boutret(1,0);
                  echo "</TD><TD align='left'><A HREF=\"javascript:document.MForm.submit();\" onmouseover=\"img1.src='images/fiche_identite/boutvalidb.gif';return true;\" onmouseout=\"img1.src='images/fiche_identite/boutvalid.gif'\">".
                       "<IMG NAME=\"img1\" SRC=\"images/fiche_identite/boutvalid.gif\" BORDER='0' onLoad=\"tempImg=new Image(0,0); tempImg.src='images/fiche_identite/boutvalidb.gif'\"></A>";
                  ?>
               </TD></TR></TABLE></TD></TR></TABLE></TD></TR></TABLE>
               </form>
               <DIV ID="TOP">
                   <SCRIPT Language="Javascript" TYPE="text/javascript">
                         Calendar.CreateCalendarLayer(10, 275, "");
                   </SCRIPT>
               </DIV>
               </BODY></HTML>
<?php exit;
}
else if ($prop_rdv == 1 || $occupation == 1) { //Ds URL,on est oblige de passer 2 fois $date_rdv pour la mettre ds le mail de confirmation
                if ($prop_rdv == 1)
                   $rdv = 1;
                  echo "<form name=\"MForm\" action=\"agenda.php?rdv=$rdv&insere_occupation=$occupation&tuteur=$tuteur&Prem=0&rech=$rech&num_sem=$num_sem&apprenant=$apprenant&tut=$tut\" method='post'>";
                  if($prop_rdv == 1)
                    $tit_mess = $mess_ag_prop_rdv_app;
                  else
                    $tit_mess = $mess_ag_ins_act;
                  entete_simple($tit_mess);
                  if ($prop_rdv == 1)
                     echo "<TR><TD colspan='2' class='sous_titre'>$mess_ag_envoi_delai</TD></TR>";
                  echo "<TR><TD colspan='2'>";
                  ?>
                  <TABLE bgcolor="#FFFFFF" width=100% cellpadding="4" border=0>
                  <?php echo "<tr><td  align=center></td><td style=\"font-weight:bold;\">$nordvdtjour</td></tr>";?>
                        <TR><TD align='right'><?php  echo $mess_ag_date_rdv ;?></TD><TD>
                       <input type="hidden" name="txt_custom" value="AppendOrReplace=Replace;AppendChar=';';CloseOnSelect=Yes;ReturnData=Date;Title=<?php  echo $calendrier;?>;InlineX=600;InlineY=150;CurrentDate=Today;SelectAfter=Today-30;SelectBefore=Today+30;AllowWeekends=No;Resizable=Yes;CallFunction=TryCallFunction;PopupX=500;PopupY=400;Nav=Yes;SmartNav=Yes;Fix=No;WeekStart=1;Weekends=06">
                       <INPUT TYPE="TEXT" class="INPUT" name="imonth" value="<?php  echo $day;?>" MAXLENGTH="2" size="2">
                       <INPUT TYPE="TEXT" class="INPUT" name="iday" value="<?php  echo $month;?>" MAXLENGTH="2" size="2">
                       <INPUT TYPE="TEXT" class="INPUT" name="iyear" value="<?php  echo $year;?>" MAXLENGTH="4" size="4">
                       <input type="hidden" value="" name="mydate1">
                       <a href="javascript:show_calendar('MForm.mydate1','09', '2002', 'DD/MM/YYYY', 'INLINE', MForm.txt_custom.value);"
                              onmouseover="img_cal1.src='images/agenda/icocalendb.gif';return true;"
                              onmouseout="img_cal1.src='images/agenda/icocalend.gif'">
                              <IMG NAME="img_cal1" SRC="images/agenda/icocalend.gif" BORDER='0' valign='top' alt="<?php  echo $cal_click  ;?>"
                              onLoad="tempImg=new Image(0,0); tempImg.src='images/agenda/icocalendb.gif'"></A>
                    </TD>
                  </TR>
                  <?php
                    echo "</TD></TR><TR><TD align='right'>$mess_ag_cren</TD>";
                    echo "<TD>";
                    echo "
                        <SELECT  name='cren' class='SELECT' size='1' >
                         <OPTION VALUE='1'>8$h-9$h</OPTION>
                         <OPTION VALUE='2'>9$h-10$h</OPTION>
                         <OPTION VALUE='3'>10$h-11$h</OPTION>
                         <OPTION VALUE='4'>11$h-12$h</OPTION>
                         <OPTION VALUE='5'>12$h-13$h</OPTION>
                         <OPTION VALUE='6'>13$h-14$h </OPTION>
                         <OPTION VALUE='7'>14$h -15$h</OPTION>
                         <OPTION VALUE='8'>15$h-16$h </OPTION>
                         <OPTION VALUE='9'>16$h -17$h</OPTION>
                         <OPTION VALUE='10'>17$h-18$h </OPTION>
                         <OPTION VALUE='11'>18$h -19$h</OPTION>
                         <OPTION VALUE='12'>19$h-20$h </OPTION>
                         <OPTION VALUE='13'>20$h -21$h</OPTION>
                         <OPTION VALUE='14'>21$h-22$h </OPTION>
                         <OPTION VALUE='15'>22$h-23$h</OPTION>
                        </SELECT>"
                        ?>
                     </TD>
                  </TR>
                  <?php
                   if ($occupation == 0)
                      echo "<input type=hidden name= 'commentaire' value='libre'>";
                   else {
                  ?>
                 <TR>
                     <TD align='right' nowrap>
                        <?php  echo $objet_ag ;?>
                     </TD>
                     <TD>
                        <INPUT TYPE="TEXT" class="INPUT"  name="titre_rdv" align="middle" size=60  MAXLENGTH=60>
                     </TD>
                  </TR>
                  <TR>
                     <TD align='right' nowrap>
                        <?php  echo $mess_admin_comment ;?>
                     </TD>
                     <TD>
                        <INPUT TYPE="TEXT" class="INPUT"  name="commentaire" size=60  MAXLENGTH=60 value="" align="middle">
                     </TD>
                  </TR>
                  <?php }?>
                  <?php  if ($prop_rdv == 1){?>
                  <TR>
                     <TD align='right' nowrap>
                        <?php  echo $mess_ag_mode_cont ;?>
                     </TD>
                     <TD>
                        <SELECT  name="mod_contact" class='SELECT' size="1" >
                         <OPTION VALUE="CHAT"><?php  echo $mess_gen_chat ;?></OPTION>
                         <OPTION VALUE="TELEPHONE"><?php  echo $mess_gen_tel ;?></OPTION>
                         <OPTION VALUE="RENCONTRE"><?php  echo $mess_gen_renc ;?></OPTION>
                         <OPTION VALUE="VISIO-CONF"><?php  echo $mess_gen_visio ;?></OPTION>
                        </SELECT>
                     </TD>
                  </TR>
                  <?php  }
                  else
                       echo "<INPUT TYPE=HIDDEN name='mod_contact' value='AGENDA'>";
                  echo boutret(1,0);
                  echo "</TD><TD align='left' colspan='2' valign='top'><A HREF=\"javascript:document.MForm.submit();\" ".
                       "onmouseover=\"img1.src='images/fiche_identite/boutvalidb.gif';return true;\" ".
                       "onmouseout=\"img1.src='images/fiche_identite/boutvalid.gif'\">".
                       "<IMG NAME=\"img1\" SRC=\"images/fiche_identite/boutvalid.gif\" BORDER='0' ".
                       "onLoad=\"tempImg=new Image(0,0); tempImg.src='images/fiche_identite/boutvalidb.gif'\"></A>";
                  ?>
                   </TD></TR></TABLE></TD></TR></FORM></TABLE></TD></TR></TABLE>


               <DIV ID="TOP">
                    <SCRIPT Language="Javascript" TYPE="text/javascript">
                            Calendar.CreateCalendarLayer(10, 275, "");
                    </SCRIPT>
               </DIV>
               </BODY></HTML>
<?php
  exit; //fin if ($prop_rdv == 1)
 }
elseif ($libre == 1 && $tut == 1)
{
//On a juste a proposer un formulaire pour rdv si c un tuteur, formateur,etc...
         echo "<center><FORM  name='form1' action=\"agenda.php?rdv_pris=1&Prem=0&rech=0&tut=1&date_rdv=$date&cren=$cren&tuteur=$tuteur&app=$app\" method='POST'>";
         echo "<CENTER><TABLE bgColor='#298CA0' cellspacing='2'><TR><TD>";
         echo "<TABLE bgColor='#FFFFFF' cellspacing='1' width='100%'>";
         echo "<TR><TD nowrap>$mess_ag_chx_app</TD><TD nowrap>";
         //Un  tuteur ne peut prendre rendez-vous qu'avec les apprenants dont il a la charge
         //Par contre, responsable, formateur et administrateur peuvent prendre rdv avec n'importe lequel
         if (($typ_user == "FORMATEUR_REFERENT") || ($typ_user == "RESPONSABLE_FORMATION") || ($typ_user == "ADMINISTRATEUR"))
            Ascenseur_mult("apprenant","select util_cdn, util_nom_lb, util_prenom_lb from utilisateur where
                           util_typutil_lb='APPRENANT' order by UTIL_NOM_LB ASC",$connect,$param);
         else
            Ascenseur_mult("apprenant","select util_cdn, util_nom_lb, util_prenom_lb from utilisateur,tuteur where
                           tuteur.tut_tuteur_no=$tuteur and util_cdn = tut_apprenant_no order by UTIL_NOM_LB ASC",$connect,$param);
         echo "<TR><TD><A HREF=\"javascript:history.back();\" ".
              "onmouseover=\"img_annonce.src='images/fiche_identite/boutretourb.gif';return true;\" ".
              "onmouseout=\"img_annonce.src='images/fiche_identite/boutretour.gif'\">".
              "<IMG NAME=\"img_annonce\" SRC=\"images/fiche_identite/boutretour.gif\" BORDER='0' ".
              "onLoad=\"tempImg=new Image(0,0); tempImg.src='images/fiche_identite/boutretourb.gif'\"></A>";
         echo "</TD><TD align='center'><A HREF=\"javascript:document.form1.submit();\" ".
              "onmouseover=\"img1.src='images/fiche_identite/boutvalidb.gif';return true;\" ".
              "onmouseout=\"img1.src='images/fiche_identite/boutvalid.gif'\">".
              "<IMG NAME=\"img1\" SRC=\"images/fiche_identite/boutvalid.gif\" BORDER='0' ".
              "onLoad=\"tempImg=new Image(0,0); tempImg.src='images/fiche_identite/boutvalidb.gif'\"></A>";
         echo "</TD></TR></TABLE></TD></TR></TABLE></FORM></center><P>";
}// fin else if ($libre == 1 && $tut == 1)
$laide = ($typ_user == 'APPRENANT') ? aide_div("rendez_vous_apprenant",0,0,0,0) : aide_div("rendez-vous_formateur",0,0,0,0);

 //On a besoin de la date courante  slt la premiere fois ou l'on arrive sur la page
if ($Prem == 0)
   $date = date("Y/n/d");

      //on insere ds la table le plage horaire proposee par le tuteur, formateur,....
      if ($rdv == 1 || $insere_occupation == 1)
      {
          if ($cren == '' || $date_rdv == '' || $mod_contact == '')
                   $form_vide=1;
          elseif ($non_supp == 1)
                  echo "<center><TABLE cellspacing=10><TR><TD align=left valign=top width='36'>".
                       "<IMG SRC=\"images/ecran-annonce/icoalert.gif\" border = '0' title ='$mess_avertis'></TD>".
                       "<TD align=left valign=top><font size='2'><b>$mess_ag_date_ant</b></font></TD></TR></TABLE>";
          else
          {
                  //Il faut inverser la date
                  $ch_date = explode("/",$date_rdv);
                  $date_rdv = "$ch_date[2]/$ch_date[1]/$ch_date[0]";
                  //Verifier qu'il n'y a pas deja un rendez-vous de pris ou propose a la meme date et au meme horaire
                  if ($typ_user == "APPRENANT" && $numero_groupe > 1)
                  {
                     $group = mysql_query("select utilgr_groupe_no from utilisateur_groupe where utilgr_utilisateur_no = $id_user");
                     $nbr_grp = mysql_num_rows($group);
                     if ($nbr_grp > 0)
                        $grp = $numero_groupe;
                     $verif_rdv_query = mysql_query ("select rdv_cdn from rendez_vous where
                                               (rdv_util_no = $id_user or rdv_apprenant_no = $id_user) and
                                               (rdv_creneau_nb = $cren or (rdv_creneau_nb = 0 and rdv_grp_no = $grp)) and
                                               rdv_date_dt = '$date_rdv'");
                  }
                  else
                  {
                    $grp = 0;
                    $verif_rdv_query = mysql_query ("select rdv_cdn from rendez_vous where (((rdv_util_no = $id_user and
                                              rdv_titre_lb != 'Présentiel') or rdv_tuteur_no = $id_user) and
                                              (rdv_creneau_nb = $cren)) and rdv_date_dt = '$date_rdv'");
                  }
                  $verif_rdv = mysql_num_rows($verif_rdv_query);
                  if ($verif_rdv == 0)
                  {
                         //Pas de rendez-vous le week-end
                         $jour_query = mysql_query ("select dayname('$date_rdv')");
                         $jour = mysql_result ($jour_query,0);
                         if (($jour == 'Saturday' || $jour == 'Sunday') && ($rdv == 1 && $mod_contact == 'RENCONTRE'))
                         {
                             echo "<center><TABLE cellspacing=10><TR><TD align=left valign=top width='36'>".
                                  "<IMG SRC=\"images/ecran-annonce/icoalert.gif\" border = '0' title ='$mess_avertis'></TD>".
                                  "<TD align=left valign=top><font size='2' color='white'><b>$mess_ag_no_weend</b></font></TD></TR></TABLE>";
                         }
                         else
                         {
                                $averti = 0;
                                $_SESSION['averti'] = $averti;
                                $id_rdv = Donne_ID ($connect,"select max(rdv_cdn) from rendez_vous");
                              if ($insere_occupation == 1)
                              {
//                                $titre_rdv = str_replace("'","\'",$titre_rdv);
//                                $commentaire = str_replace("'","\'",$commentaire);
                                $query = mysql_query ("insert into rendez_vous (rdv_cdn,rdv_util_no,rdv_titre_lb,rdv_apprenant_no,rdv_creneau_nb,rdv_commentaire_cmt,rdv_date_dt,rdv_modecontact_lb) values (\"$id_rdv\", \"$id_user\",\"$titre_rdv\",\"0\",\"$cren\",\"$commentaire\",\"$date_rdv\",\"$mod_contact\")");
                              }
                              else
                              {
                                $query = mysql_query ("insert into rendez_vous (rdv_cdn,rdv_tuteur_no,rdv_apprenant_no,rdv_creneau_nb,rdv_commentaire_cmt,rdv_date_dt,rdv_modecontact_lb) values (\"$id_rdv\", \"$tuteur\",\"0\",\"$cren\",\"$commentaire\",\"$date_rdv\",\"$mod_contact\")");
                              }
                                $date = "$ch_date[0]/$ch_date[1]/$ch_date[2]";
                                //On envoie un email pour lui rappeler quelle plage horaire il a propose
                                $adr_mail = GetDataField ($connect, "select util_email_lb from utilisateur where util_cdn=$id_user","util_email_lb");
                                $horaire = Horaire ($cren);
                              if ($mod_contact == "AGENDA")
                              {
                                $message = "$mess_ag_agenda\n$mess_ag_date $date\n$mess_ag_heure $horaire\n";
                                if ($insere_occupation == 1)
                                {
                                  $message_into .= $msq_titre." ".Stripslashes($titre_rdv)."\n";
                                  $message_into .= $mess_mail_mess." ".Stripslashes($commentaire)."\n";
                                }
                                $message .= $message_into;
                                $message .= "\n$mess_ag_cordial";
                                $reply = $adr_mail;
                                $from = $adr_mail;
                                $sendto = $adr_mail;
                                $subject = StripSlashes($mess_ag_ag);
                                $msg = StripSlashes($message);
                                $origine=$nom_user."  ".$typ_user;
                                $nom ='';
                                $userfile = "none";
                                if (isset($_SESSION['IsOff']) && $_SESSION['IsOff'] == 0)
                                {
                                  if ($sendto != "")
                                     $envoi=mail_attachement($sendto , $subject , $msg , $userfile ,$reply, $nom, $from );
                                }
                                $message_base = "$mess_ag_agenda <BR>$mess_ag_date $date<BR>$mess_ag_heure $horaire<BR>".Stripslashes($titre_rdv)."<BR>".Stripslashes($commentaire)."<BR>$mess_ag_cordial";
                                $max_numero = Donne_ID ($connect,"select max(mess_cdn) from messagerie");
                                $requete = mysql_query("INSERT INTO messagerie (mess_cdn,envoyeur,origine,contenu,date,sujet,id_user) VALUES ($max_numero,'$id_user','$mess_ag_rdv_tut',\"$message_base\",'$date_messagerie','$subject',$id_user)");
                                $date = "$ch_date[2]/$ch_date[1]/$ch_date[0]";
                              }
                              else
                              {
                                $message= "$mess_ag_mail1_1 $date\n$mess_ag_mail1_2 $horaire\n$mess_ag_mail1_3 $mod_contact\n$mess_ag_mail1_4";
                                $reply = $adr_mail;
                                $from = $adr_mail;
                                $sendto = $adr_mail;
                                $subject = StripSlashes($mess_ag_rdv_foad);
                                $msg = StripSlashes($message);
                                $origine=$nom_user."  ".$typ_user;
                                $nom ='';
                                $userfile = "none";
                                if (isset($_SESSION['IsOff']) && $_SESSION['IsOff'] == 0)
                                {
                                  if ($sendto != "")
                                     $envoi=mail_attachement($sendto , $subject , $msg , $userfile ,$reply, $nom, $from );
                                }
                                $message_base = $message;
                                $max_numero = Donne_ID ($connect,"select max(mess_cdn) from messagerie");
                                $requete = mysql_query("INSERT INTO messagerie (mess_cdn,envoyeur,origine,contenu,date,sujet,id_user) VALUES ($max_numero,'$id_user','$mess_ag_rdv_tut',\"$message_base\",'$date_messagerie','$subject',$id_user)");
                                $date = "$ch_date[2]/$ch_date[1]/$ch_date[0]";
                         } //fin else
                    }//fin if (!$verif_rdv)
                  }else
                      echo "<center><TABLE cellspacing=10><TR><TD align=left valign=top width='36'><IMG SRC=\"images/ecran-annonce/icoalert.gif\" border = '0' title ='$mess_avertis'></TD><TD align=left valign=top><font size='2' color='white'><b>$mess_ag_cren_occupe</b></font></TD></TR></TABLE>";
               } //fin else ($cren='' ....)
        $rdv = 1;
       } //fin if ($rdv == 1)
      //Les rdv sont pris
      if ($rdv_pris == 1 || $app == 1)
      {
          //if ($app == 1) $apprenant=$id_user;
          $verif_rdv_query = mysql_query ("select rdv_cdn from rendez_vous where rdv_apprenant_no = $id_user and rdv_creneau_nb = $cren and rdv_date_dt = '$date_rdv'");
          $verif_rdv = mysql_num_rows($verif_rdv_query);
          if ($verif_rdv > 0)
          {
             echo "<center><TABLE cellspacing=10><TR><TD align=left valign=top width='36'>".
                  "<IMG SRC=\"images/ecran-annonce/icoalert.gif\" border = '0' title ='$mess_avertis'></TD>".
                  "<TD align=left valign=top><font size='2' color='white'><b>$ag_rdv_also_pris</b></font></TD></TR></TABLE>";
             $lien = "agenda.php?apprenant=1";
             $lien = urlencode($lien);
             echo "<script language=\"JavaScript\">";
               echo "setTimeout(\"aller()\",2500);";
               echo "function aller() {";
                  echo "document.location.replace(\"trace.php?link=$lien\");";
               echo "}";
             echo "</script>";
            exit();
          }
          $averti = 0;
          $_SESSION['averti'] = $averti;
          $id_rdv = GetDataField ($connect,"select rdv_cdn from rendez_vous where rdv_date_dt='$date_rdv' and rdv_creneau_nb ='$cren' and rdv_tuteur_no='$tuteur'","rdv_cdn");
          $upd_rdv = mysql_query ("UPDATE rendez_vous SET rdv_apprenant_no='$apprenant',rdv_commentaire_cmt='Occupe' where rdv_cdn=$id_rdv");
          $mod_contact  = GetDataField ($connect,"select rdv_modecontact_lb from rendez_vous where rdv_cdn=$id_rdv","rdv_modecontact_lb");
          $letuteur  = GetDataField ($connect,"select rdv_tuteur_no from rendez_vous where rdv_cdn=$id_rdv","rdv_tuteur_no");
          $tuteur_query = GetDataField ($connect,"select util_typutil_lb from utilisateur where util_cdn=$letuteur","util_typutil_lb");
          $inscripteur = GetDataField ($connect,"select util_auteur_no from utilisateur where util_cdn='$apprenant'","util_auteur_no");
          if ($typ_user == "APPRENANT")
            $qualite = "Apprenant";
          elseif ($inscripteur == $id_user)
            $qualite = "Inscripteur";
          elseif ($letuteur == $id_user && $tuteur_query == $letuteur)
            $qualite = "Tuteur";
          elseif ($letuteur == $id_user && $tuteur_query != $letuteur)
            $qualite = "Formateur";
          elseif ($letuteur == $id_user && $inscripteur != $id_user && $typ_user == "ADMINISTRATEUR" && $tuteur_query != $letuteur)
            $qualite = "Administrateur";
          $nom_fiche=GetDataField ($connect,"select util_nom_lb from utilisateur where util_cdn='$letuteur'","util_nom_lb");
          $prenom_fiche=GetDataField ($connect,"select util_prenom_lb from utilisateur where util_cdn='$letuteur'","util_prenom_lb");
          //On envoie un email pour lui rappeler quelle plage horaire il a propose
          //On pourrait faire dans une boucle mais cela complique pour la personnalisation du message
          //Mail a l'utilisateur
          $horaire = Horaire ($cren);
          $adr_mail = GetDataField ($connect, "select util_email_lb from utilisateur where util_cdn=$id_user","util_email_lb");
          $reply = $adr_mail;
          $from = $adr_mail;
          $sendto = $adr_mail;
          $subject = StripSlashes($mess_ag_rdv_foad);
          $ch_date_rdv = explode ("-",$date_rdv);
          $date_rdv_pris = "$ch_date_rdv[2]/$ch_date_rdv[1]/$ch_date_rdv[0]";
          if ($app == 1) {
              $nom_user=GetDataField ($connect,"select util_nom_lb from utilisateur where util_cdn='$tuteur'","util_nom_lb");
              $prenom_user=GetDataField ($connect,"select util_prenom_lb from utilisateur where util_cdn='$tuteur'","util_prenom_lb");
           }else {
              $nom_user=GetDataField ($connect,"select util_nom_lb from utilisateur where util_cdn='$apprenant'","util_nom_lb");
              $prenom_user=GetDataField ($connect,"select util_prenom_lb from utilisateur where util_cdn='$apprenant'","util_prenom_lb");
          }

          $lecreneau = affiche_creneau($cren,$lg);
          if ($mod_contact =="TELEPHONE")
             $modee = $mess_gen_tel;
          if ($mod_contact =="CHAT")
             $modee = $mess_gen_chat;
          if ($mod_contact =="RENCONTRE")
             $modee = $mess_gen_renc;
          if ($mod_contact =="VISIO-CONF")
             $modee = $mess_gen_visio;
          $date_fichee = str_replace("/","-",$date_rdv_pris);
          $action_fiche = "Tutorat";
          $commentaire = $mess_ag_rdv_tut." $par $modee : $date_fichee $lecreneau $mess_ag_avec $prenom_fiche $nom_fiche";
          $new_fiche = Donne_ID ($connect,"select max(fiche_cdn) from fiche_suivi");
          if ($qualite == "Apprenant")
             $req_fiche = mysql_query("INSERT INTO fiche_suivi (fiche_cdn,fiche_utilisateur_no,fiche_auteur_no,fiche_qualite_lb,fiche_date_dt,fiche_heure_dt,fiche_commentaire_cmt,fiche_grp_no,fiche_parc_no,fiche_seq_no,fiche_act_no,fiche_typaction_lb) VALUES($new_fiche,$id_user,$id_user,'$qualite','$date_fiche','$heure_fiche',\"$commentaire\",0,0,0,0,\"$action_fiche\")");
          else
             $req_fiche = mysql_query("INSERT INTO fiche_suivi (fiche_cdn,fiche_utilisateur_no,fiche_auteur_no,fiche_qualite_lb,fiche_date_dt,fiche_heure_dt,fiche_commentaire_cmt,fiche_grp_no,fiche_parc_no,fiche_seq_no,fiche_act_no,fiche_typaction_lb) VALUES($new_fiche,$apprenant,$id_user,'$qualite','$date_fiche','$heure_fiche',\"$commentaire\",0,0,0,0,\"$action_fiche\")");
          $id_mess = $id_user;
          $message = "$mess_ag_rdv_app $prenom_user $nom_user:\n$mess_ag_date  $date_rdv_pris\n$mess_ag_heure $horaire\n$mess_ag_mode_cont $mod_contact\n$mess_ag_cordial\n\n";
          $msg = StripSlashes($message);
          $origine=$nom_user."  ".$typ_user;
          $nom ='';
          $userfile = "none";
          if (isset($_SESSION['IsOff']) && $_SESSION['IsOff'] == 0)
          {
                if ($sendto != "")
                    $envoi=mail_attachement($sendto , $subject , $msg , $userfile ,$reply, $nom, $from );
          }
          $message_base = "$mess_ag_rdv_app $prenom_user $nom_user:<BR>$mess_ag_date $date_rdv_pris<BR>$mess_ag_heure  $horaire<BR>$mess_ag_mode_cont $mod_contact<BR>$mess_ag_cordial";
          $max_numero = Donne_ID ($connect,"select max(mess_cdn) from messagerie");
          $requete = mysql_query("INSERT INTO messagerie (mess_cdn,envoyeur,origine,contenu,date,sujet,id_user) VALUES ($max_numero,'$id_user','$mess_ag_rdv_tut',\"$message_base\",'$date_messagerie','$subject',$id_mess)");

          //Mail a l'autre  personne concernée
              if ($app == 1) {
                  $email_util = GetDataField ($connect, "select util_email_lb from utilisateur where util_cdn='$tuteur'","util_email_lb");
                  $nom_user=GetDataField ($connect,"select util_nom_lb from utilisateur where util_cdn='$id_user'","util_nom_lb");
                  $prenom_user=GetDataField ($connect,"select util_prenom_lb from utilisateur where util_cdn='$id_user'","util_prenom_lb");
                  $adr_mail = GetDataField ($connect, "select util_email_lb from utilisateur where util_cdn='$id_user'","util_email_lb");
                  $id_mess = $tuteur;
              }else {
                  $email_util = GetDataField ($connect, "select util_email_lb from utilisateur where util_cdn=$apprenant","util_email_lb");
                  $nom_user=GetDataField ($connect,"select util_nom_lb from utilisateur where util_cdn='$id_user'","util_nom_lb");
                  $prenom_user=GetDataField ($connect,"select util_prenom_lb from utilisateur where util_cdn='$id_user'","util_prenom_lb");
                  $adr_mail = GetDataField ($connect, "select util_email_lb from utilisateur where util_cdn=$id_user","util_email_lb");
                  $id_mess = $apprenant;
              }
              $from = $adr_mail;
              $reply = $adr_mail;
              $sendto = $email_util;
              $subject = StripSlashes($mess_ag_rdv_foad);
              $message = "$mess_ag_rdv_app $prenom_user $nom_user:\n$mess_ag_date  $date_rdv_pris\n$mess_ag_heure $horaire\n$mess_ag_mode_cont $mod_contact\n$mess_ag_cordial\n\n";
              $msg = StripSlashes($message);
              $origine=$nom_user."  ".$typ_user;
              $nom ='';
              $userfile = "none";
              if (isset($_SESSION['IsOff']) && $_SESSION['IsOff'] == 0)
              {
                  if ($email_util != "")
                     $envoi=mail_attachement($sendto , $subject , $msg , $userfile , $reply , $nom , $from);
              }
              $message_base = "$mess_ag_rdv_app $prenom_user $nom_user:<BR>$mess_ag_date $date_rdv_pris<BR>$mess_ag_heure  $horaire<BR>$mess_ag_mode_cont $mod_contact<BR>$mess_ag_cordial";
              $max_numero = Donne_ID ($connect,"select max(mess_cdn) from messagerie");
              $requete = mysql_query("INSERT INTO messagerie (mess_cdn,envoyeur,origine,contenu,date,sujet,id_user) VALUES ($max_numero,$id_user,'$mess_ag_rdv_tut',\"$message_base\",'$date_messagerie','$subject',$id_mess)");
              $tuteur = "";
         }//fin $rdv_pris == 1

      //Si c'est un tuteur, formateur, ....  qui consulte son agenda, on prend son id
      //De meme, il faut separer la date en champ (besoin ds les conditions d'affichage des rdv)
      //Et il faut inverser le jour et l'annee puisque l'on prend tjs pour l'annee $ch_date[2]
      if ($tut == 1) {
           $tuteur = $id_user;
           $ch_date =explode ("/",$date);
           $ch_date[2]=$ch_date[0];
      }

      //Suppression dun rdv. On execute la requete avt l'affichage du $calendrier pour que celui ci soit a jour!
      //Mais on fait affichage a fin du mail.
      if ($supp == 1)
      {
          $cren1=$cren;
          $averti = 0;
          $_SESSION['averti'] = $averti;
          $groupe = GetDataField ($connect,"select rdv_grp_no from rendez_vous where rdv_cdn = $rdv_id","rdv_grp_no");
          $comment = GetDataField ($connect,"select rdv_commentaire_cmt from rendez_vous where rdv_cdn = $rdv_id","rdv_commentaire_cmt");
          $mod_contact = GetDataField ($connect,"select rdv_modecontact_lb from rendez_vous where rdv_cdn = $rdv_id","rdv_modecontact_lb");
          $letuteur = GetDataField ($connect,"select rdv_tuteur_no from rendez_vous where rdv_cdn = $rdv_id","rdv_tuteur_no");
          $id_rdv_supp = GetDataField ($connect,"select rdv_cdn from rendez_vous where
                                                 rdv_cdn = $rdv_id and
                                                 rdv_date_dt='$date_rdv' and
                                                 rdv_creneau_nb ='$cren' and
                                                 (rdv_tuteur_no='$tuteur' or rdv_util_no=$utilisateur or rdv_util_no=$id_user) and rdv_grp_no = 0","rdv_cdn");
//          $commentaire1 = GetDataField ($connect,"select rdv_commentaire_cmt from rendez_vous where rdv_cdn = $rdv_id and rdv_date_dt='$date_rdv' and rdv_creneau_nb ='$cren' and (rdv_tuteur_no='$tuteur' or rdv_util_no=$utilisateur or rdv_util_no=$id_user)","rdv_commentaire_cmt");
//          if ($commentaire1 == $mess_ag_occupe && $typ_user == "APPRENANT")
//            $rend_libre_rdv = mysql_query ("update rendez_vous set rdv_apprenant_no = 0,rdv_commentaire_cmt ='$mess_ag_libre' where rdv_cdn =$id_rdv_supp");
          if (($tut == 1 || $le_type_rdv == "AGENDA") && $groupe == 0){
            $del_rdv = mysql_query ("delete from rendez_vous where rdv_cdn = $id_rdv_supp");
          }elseif ($groupe > 0)
          {//echo"delete from rendez-vous where rdv_date_dt=\"$date_rdv\" and rdv_grp_no = $groupe AND rdv_commentaire_cmt=\"$comment\"";
             $del_rdv = mysql_query ("delete from rendez_vous where rdv_date_dt=\"$date_rdv\" and rdv_grp_no = '$groupe' AND rdv_commentaire_cmt=\"$comment\"");
          }
          else
          {
            $nom_fiche=GetDataField ($connect,"select util_nom_lb from utilisateur where util_cdn='$letuteur'","util_nom_lb");
            $prenom_fiche=GetDataField ($connect,"select util_prenom_lb from utilisateur where util_cdn='$letuteur'","util_prenom_lb");
            $horaire = Horaire($cren);
            $lecreneau = affiche_creneau($cren,$lg);
            if ($mod_contact =="TELEPHONE")
              $modee = $mess_gen_tel;
            if ($mod_contact =="CHAT")
              $modee = $mess_gen_chat;
            if ($mod_contact =="RENCONTRE")
              $modee = $mess_gen_renc;
            if ($mod_contact =="VISIO-CONF")
              $modee = $mess_gen_visio;
            $qualite = "Apprenant";
            $ch_date_fic = explode("-",$date_rdv);
            $date_fichee = "$ch_date_fic[2]-$ch_date_fic[1]-$ch_date_fic[0]";
            $action_fiche = "Tutorat";
            $commentaire = $mess_sup_rdv_fiche." $par $modee : $date_fichee $lecreneau $mess_ag_avec $prenom_fiche $nom_fiche";
            $new_fiche = Donne_ID ($connect,"select max(fiche_cdn) from fiche_suivi");
            $req_fiche = mysql_query("INSERT INTO fiche_suivi (fiche_cdn,fiche_utilisateur_no,fiche_auteur_no,fiche_qualite_lb,fiche_date_dt,fiche_heure_dt,fiche_commentaire_cmt,fiche_grp_no,fiche_parc_no,fiche_seq_no,fiche_act_no,fiche_typaction_lb) VALUES($new_fiche,$id_user,$id_user,'$qualite','$date_fiche','$heure_fiche',\"$commentaire\",0,0,0,0,\"$action_fiche\")");
            $rend_libre_rdv = mysql_query ("update rendez_vous set rdv_apprenant_no = 0,rdv_commentaire_cmt ='$mess_ag_libre' where rdv_cdn =$id_rdv_supp");
          }
      }
      //il nous faut la date courante pour pour ne pas afficher les semaines precedentes a celles-ci
      //il nous faut une variable différente de $date
      $date_cour = date ("Y/n/d");
      $num_sem_cou_query = mysql_query ("select week('$date_cour',1)");
      $num_sem_cour = mysql_result ($num_sem_cou_query,0);
      //on recupere ds un champ l'annee pour pouvoir comparer avec l'annee cherchee ($ch_date_cour[0])
      $ch_date_cour= explode ("/",$date_cour);

      //si on veut aller a une date sans les liens, date est au format fr  => il faut inverser les champs
      if ($rech == 1)
      {
          if (isset($date_prise))
              $date = $date_prise;
          $ch_date = explode ("/",$date);
          $date = "$ch_date[2]/$ch_date[1]/$ch_date[0]";
      }

                      // ----------------   Recuperation des rdv de la semaine affichee   --------------------------------
                      //On recupere le numero de la semaine affichee et le numero de semaine des rdv pris
                      // fonction mysql  Week   : le 2nd parametre sert a preciser si sem commence par dim ou lundi  (1 => lundi)

                      //declaration des tableaux
                      $groupe=array();
                      $creneau = array();
                      $appre = array();
                      $jour = array();
                      $num_sem_rdv = array();
                      $id = array();
                      $utilisateur = array();
                      $comment = array();

                      //-------------------------   Affichage du tableau representant l'agenda  ---------------------------
                      if ($typ_user == "APPRENANT")
                      {
                         $group = mysql_query("select utilgr_groupe_no from utilisateur_groupe where utilgr_utilisateur_no = $id_user");
                         $nbr_grp = mysql_num_rows($group);
                         if ($nbr_grp > 0)
                           $grp = $numero_groupe;
                      }
                      if (isset($tuteur) && $tuteur > 0)
                      {
                        if ($typ_user == "APPRENANT" && $grp > 0)
                          $rdv_query = mysql_query ("select * from rendez_vous where rdv_tuteur_no=$tuteur or rdv_grp_no=$grp order by rdv_cdn desc");
                        elseif ($typ_user == "APPRENANT" && !$grp)
                          $rdv_query = mysql_query ("select * from rendez_vous where rdv_tuteur_no=$tuteur order by rdv_cdn desc");
                        else
                          $rdv_query = mysql_query ("select * from rendez_vous where rdv_creneau_nb=0 or rdv_tuteur_no=$id_user or rdv_util_no=$id_user or rdv_grp_no > 0 order by rdv_cdn desc");
                        $Nb_rdv = mysql_num_rows ($rdv_query);
                      }
                      else
                      {
                        if ($typ_user == "APPRENANT" && $grp > 0)
                          $rdv_query = mysql_query ("select * from rendez_vous where rdv_util_no=$id_user or rdv_apprenant_no=$id_user or rdv_grp_no=$grp order by rdv_cdn");
                        elseif ($typ_user == "APPRENANT" && !isset($grp))
                          $rdv_query = mysql_query ("select * from rendez_vous where rdv_util_no=$id_user or rdv_apprenant_no=$id_user order by rdv_cdn desc");
                        else
                          $rdv_query = mysql_query ("select * from rendez_vous where rdv_creneau_nb=0 or rdv_util_no=$id_user or rdv_tuteur_no=$id_user or rdv_grp_no > 0 order by rdv_cdn desc");
                        $Nb_rdv = mysql_num_rows ($rdv_query);
                      }
                      if (isset($tuteur) && $tuteur > 0 && $typ_user == "APPRENANT")
                      {
                         $nom_tuteur=GetDataField ($connect,"select util_nom_lb from utilisateur where util_cdn='$tuteur'","util_nom_lb");
                         $prenom_tuteur=GetDataField ($connect,"select util_prenom_lb from utilisateur where util_cdn='$tuteur'","util_prenom_lb");
                         $np_tut = ucfirst($prenom_tuteur)." ".ucfirst($nom_tuteur);
                         $mess_titre = "$mess_ag_carnet_tut $np_tut";
                      }
                      elseif(!$tuteur && $typ_user == "APPRENANT")
                         $mess_titre = $mess_menu_agenda;
                      else
                      {
                         $mess_titre = $mess_menu_agenda;
                         if ($messg !="")
                            echo $messg;
                      }
                                 //Semaine courante
                      entete_simple($mess_titre);
                      if ($tut == 1)
                      {
                            echo "<tr><td valign='middle' style=\"float:left;padding-top:2px;\">";
                            $lien="agenda.php?occupation=1&apprenant=$apprenant&tuteur=$tuteur&tut=$tut&day=$ch_date_cour[2]&month=$ch_date_cour[1]&year=$ch_date_cour[0]";
                            $lien = urlencode($lien);
                            echo "<div id='envent' style=\"float:left;padding-left:3px;padding-right:8px;\"><A href=\"trace.php?link=$lien\" class='bouton_new' target='main'>$mess_ag_ins_act</A></div>";
                            $lien="agenda.php?tuteur=$tuteur&prop_rdv=1&tut=$tut&day=$ch_date_cour[2]&month=$ch_date_cour[1]&year=$ch_date_cour[0]";
                            $lien = urlencode($lien);
                            echo "<div id='rdv' style=\"float:left;padding-right:8px;\"><A href=\"trace.php?link=$lien\" class='bouton_new' target='main'>$mess_ag_prop_rdv_app</A></div>";
                            if ($typ_user != "APPRENANT")
                            {
                               $lien="agenda.php?tuteur=$tuteur&reserve=1&matiere=oui&tut=$tut&day=$ch_date_cour[2]&month=$ch_date_cour[1]&year=$ch_date_cour[0]";
                               $lien = urlencode($lien);
                               echo "<div id='rgt' style=\"float:left;padding-right:8px;\"><A HREF=\"trace.php?link=$lien\" class='bouton_new' target='main'>$mess_ag_rsv</A></div>$laide";
                            }
                            echo "</tr>";
                      }
                      echo "<tr><td style=\"height: 40px;\" valign='middle'><table width=100% bgcolor='#FFFFFF' border='0'><td>";
                      if ($typ_user == "APPRENANT" && $mon_lien != "" && $tuteur == "" && $numero_groupe > 0)
                            echo $mon_lien;
                      elseif ($typ_user == "APPRENANT" && $mon_lien == "")
                            echo "<td align='left' valign='center' width='220' nowrap></td>";
                         $date = ParcoursMois ($sem_der,$sem_pro,$date,$Prem,$rech,$rdv,0);
                         $date_ret = $date;
                         $ch_date_ret =explode ("/",$date_ret);
                         $date_ret = "$ch_date_ret[2]/$ch_date_ret[1]/$ch_date_ret[0]";
                         $nb_jours = nb_jours($date);
                         $new_jour = $nb_jours +6;
                         $fin_sem = nb_jours_from($new_jour);
                         $ch_date_fs = explode ("-",$fin_sem);
                         $date_fs = "$ch_date_fs[2]/$ch_date_fs[1]/$ch_date_fs[0]";
                         $lien="agenda.php?tuteur=$tuteur&sem_der=1&sem_pro=0&num_sem=$num_sem&date=$date&Prem=1&num_sem_cour=$num_sem_cour&rech=0&ch_date[2]=$ch_date[2]&tut=$tut&apprenant=$apprenant";
                         $lien =urlencode($lien);
                         $navi = "<TD width='38'><a href=\"trace.php?link=$lien\" title =\"$mess_ag_sem_prec\"".
                              " onmouseover=\"img_fl1.src='images/agenda/flechgb.gif';return true;\"".
                              " onmouseout=\"img_fl1.src='images/agenda/flechg.gif'\">".
                              "<IMG NAME=\"img_fl1\" SRC=\"images/agenda/flechg.gif\" BORDER='0' valign='top' alt=\"$mess_ag_sem_prec\"".
                              " onLoad=\"tempImg=new Image(0,0); tempImg.src='images/agenda/flechgb.gif'\"></A></TD>";
                         $lien="agenda.php?tuteur=$tuteur&sem_pro=1&sem_der=0&num_sem=$num_sem&date=$date&Prem=1&num_sem_cour=$num_sem_cour&rech=0&ch_date[2]=$ch_date[2]&tut=$tut&apprenant=$apprenant";
                         $navi .= "<TD width='220' nowrap><B>$mess_ag_sem_du $date_ret au $date_fs</B></TD>";
                         $lien =urlencode($lien);
                         $navi .= "<TD width='38'><a href=\"trace.php?link=$lien\" title =\"$mess_ag_sem_suiv\"".
                              " onmouseover=\"img_fl2.src='images/agenda/flechdb.gif';return true;\"".
                              " onmouseout=\"img_fl2.src='images/agenda/flechd.gif'\">".
                              "<IMG NAME=\"img_fl2\" SRC=\"images/agenda/flechd.gif\" BORDER='0' valign='top' alt=\"$mess_ag_sem_suiv\"".
                              " onLoad=\"tempImg=new Image(0,0); tempImg.src='images/agenda/flechdb.gif'\"></A></TD>";
                         if ($typ_user == 'APPRENANT' && $tuteur == 0)
                         {
                            $lien="agenda.php?occupation=1&apprenant=$apprenant&tuteur=$tuteur&tut=$tut&day=$ch_date_cour[2]&month=$ch_date_cour[1]&year=$ch_date_cour[0]";
                            $lien = urlencode($lien);
                            echo "<div id='event' style=\"float:left;padding-right:8px;\"><A href=\"trace.php?link=$lien\" class='bouton_new'>$mess_ag_ins_act</A></div>";
                            echo $laide;
                            echo "</td>";
                         }
                         elseif ($typ_user != "APPRENANT")
                         {
                            echo $navi;
                         }

                         echo "<TD align='right'><TABLE><TR>";
                         echo "<FORM name=\"MForm\" action=\"agenda.php?tuteur=$tuteur&entree_date=1&num_sem=$num_sem&Prem=1&num_sem_cour=$num_sem_cour&rech=1&tut=$tut&apprenant=$apprenant\" method=\"POST\">";
                         echo"<TD nowrap>$mess_ag_go_at</TD>";
                         $day=$ch_date_cour[2];
                         $month=$ch_date_cour[1];
                         $year=$ch_date_cour[0];
                         ?>
                         <input type="hidden" name=txt_custom value="AppendOrReplace=Replace;AppendChar=';';CloseOnSelect=Yes;ReturnData=Date;Title=<?php  echo $calendrier;?>;InlineX=500;InlineY=150;CurrentDate=Today;SelectAfter=Today-30;SelectBefore=Today+30;AllowWeekends=No;Resizable=Yes;CallFunction=TryCallFunction;PopupX=300;PopupY=300;Nav=Yes;SmartNav=Yes;Fix=No;WeekStart=1;Weekends=06">
                         <TD><INPUT TYPE="TEXT" class="INPUT" name="imonth" value="<?php  echo $day;?>" MAXLENGTH="2" size="2"></TD>
                         <TD><INPUT TYPE="TEXT" class="INPUT" name="iday" value="<?php  echo $month;?>" MAXLENGTH="2" size="2"></TD>
                         <TD><INPUT TYPE="TEXT" class="INPUT" name="iyear" value="<?php  echo $year;?>" MAXLENGTH="4" size="4"></TD>
                         <input type="hidden" value="" name="mydate1">
                         <TD><a href="javascript:show_calendar('MForm.mydate1','09', '2002', 'DD/MM/YYYY','INLINE' , MForm.txt_custom.value);"
                              onmouseover="img_cal1.src='images/agenda/icocalendb.gif';return true;"
                              onmouseout="img_cal1.src='images/agenda/icocalend.gif'">
                              <IMG NAME="img_cal1" SRC="images/agenda/icocalend.gif" BORDER='0' valign='top' alt="<?php  echo $cal_click  ;?>"
                              onLoad="tempImg=new Image(0,0); tempImg.src='images/agenda/icocalendb.gif'"></A></TD>
                         <?php
                         echo "<TD><A HREF=\"javascript:document.MForm.submit();\" onmouseover=\"img1.src='images/fiche_identite/boutvalidb.gif';return true;\" onmouseout=\"img1.src='images/fiche_identite/boutvalid.gif'\" style='padding-top:3px;'>".
                              "<IMG NAME=\"img1\" SRC=\"images/fiche_identite/boutvalid.gif\" BORDER='0' style='padding-top:5px;' onLoad=\"tempImg=new Image(0,0); tempImg.src='images/fiche_identite/boutvalidb.gif'\"></A>".
                              "&nbsp;&nbsp;&nbsp;&nbsp;</TD></TR></table></td></FORM>";
                         $num_sem_query = mysql_query ("select week('$date',1)");
                         $num_sem = mysql_result ($num_sem_query,0);
                         if ($ch_date[2] >= $ch_date_cour[0]  || $rech == 0)
                         {
                              ?>
                              </TR>
                              </TABLE>
                                   <TABLE width="100%" border="0">
                                     <tr bgcolor="#2B677A">
                                       <td height="15" align='center'><Font color='#FFFFFF'><B><?php echo $mess_ag_plag_horaires;?></B></FONT></td>
                                       <td height="15">
                                       <?php
                                          $nb_jours = nb_jours($date);
                                          $jours = nb_jours_from($nb_jours);
                                          $journee[1]=$jours;
                                          $ch_jour = explode ("-",$jours);
                                          $jours = " &nbsp; $ch_jour[2]/$ch_jour[1]";
                                       ?>
                                         <div align="center"><b><font color=white face="Arial" size="1"><?php  echo "$jour_lundi $jours";?></font></b></div>
                                       </td>
                                       <td height="15">
                                          <?php
                                          $new_jour = $nb_jours + 1;
                                          $jours = nb_jours_from($new_jour);
                                          $journee[2]=$jours;
                                          $ch_jour = explode ("-",$jours);
                                          $jours = "&nbsp;$ch_jour[2]/$ch_jour[1]";

                                          ?>
                                         <div align="center"><b><font color=white face="Arial" size="1"><?php  echo "$jour_mardi $jours";?></font></b></div>
                                       </td>
                                       <td height="15">
                                          <?php
                                          $new_jour = $nb_jours + 2;
                                          $jours = nb_jours_from($new_jour);
                                          $journee[3]=$jours;
                                          $ch_jour = explode ("-",$jours);
                                          $jours = " &nbsp;$ch_jour[2]/$ch_jour[1]";
                                          ?>
                                         <div align="center"><b><font color=white face="Arial" size="1"><?php  echo "$jour_mercredi $jours";?></font></b></div>
                                       </td>
                                       <td height="15">
                                          <?php
                                          $new_jour = $nb_jours + 3;
                                          $jours = nb_jours_from($new_jour);
                                          $journee[4]=$jours;
                                          $ch_jour = explode ("-",$jours);
                                          $jours = " &nbsp;$ch_jour[2]/$ch_jour[1]";
                                          ?>
                                         <div align="center"><b><font color=white face="Arial" size="1"><?php  echo "$jour_jeudi $jours";?></font></b></div>
                                       </td>
                                       <td height="15">
                                          <?php
                                          $new_jour = $nb_jours + 4;
                                          $jours = nb_jours_from($new_jour);
                                          $journee[5]=$jours;
                                          $ch_jour = explode ("-",$jours);
                                          $jours = " &nbsp;$ch_jour[2]/$ch_jour[1]";
                                          ?>
                                         <div align="center"><b><font color=white face="Arial" size="1"><?php  echo "$jour_vendredi $jours";?></font></b></div>
                                       </td>
                                      <td height="15">
                                          <?php
                                          $new_jour = $nb_jours + 5;
                                          $jours = nb_jours_from($new_jour);
                                          $journee[6]=$jours;
                                          $ch_jour = explode ("-",$jours);
                                          $jours = " &nbsp;$ch_jour[2]/$ch_jour[1]";
                                          ?>
                                         <div align="center"><b><font color=white face="Arial" size="1"><?php  echo "$jour_samedi $jours";?></font></b></div>
                                       </td>
                                      <td height="15">
                                          <?php
                                          $new_jour = $nb_jours + 6;
                                          $jours = nb_jours_from($new_jour);
                                          $journee[7]=$jours;
                                          $ch_jour = explode ("-",$jours);
                                          $jours = " &nbsp;$ch_jour[2]/$ch_jour[1]";
                                          ?>
                                         <div align="center"><b><font color=white face="Arial" size="1"><?php  echo "$jour_dimanche $jours";?></font></b></div>
                                       </td>
                                     </tr>
                                     <?php
                             $i=0;
                             while ($i != $Nb_rdv)
                             {
                                    $id[$i] = mysql_result ($rdv_query,$i,"rdv_cdn");
                                    $utilisateur[$i] = mysql_result ($rdv_query,$i,"rdv_util_no");
                                    $appre[$i] = mysql_result ($rdv_query,$i,"rdv_apprenant_no");
                                    //si $apprenant == 0  =>  Plage horaire libre
                                    $comment[$i] = mysql_result ($rdv_query,$i,"rdv_commentaire_cmt");
                                    $groupe[$i] = mysql_result ($rdv_query,$i,"rdv_grp_no");
                                    $creneau[$i] = mysql_result ($rdv_query,$i,"rdv_creneau_nb");
                                    $date = mysql_result ($rdv_query,$i,"rdv_date_dt");
                                    $jour_query = mysql_query ("select dayname('$date')");
                                    $jour[$i] = mysql_result ($jour_query,0);
                                    $num_sem_rdv_query = mysql_query ("select week('$date',1)");
                                    $num_sem_rdv[$i] = mysql_result ($num_sem_rdv_query,0);
                                    $datee[$i]=$date;
                               $i++;
                              }
                              // table de nom_de_jour
                              $nom_de_jour[1]='Monday';
                              $nom_de_jour[2]='Tuesday';
                              $nom_de_jour[3]='Wednesday';
                              $nom_de_jour[4]='Thursday';
                              $nom_de_jour[5]='Friday';
                              $nom_de_jour[6]='Saturday';
                              $nom_de_jour[7]='Sunday';
                              //Affichage des plages horaires : 22 plages  (en comptant entre 12 et 14)
                              // $j : pour afficher les plages horaires
                              // $k : pour savoir le numero du creneau. On incremente de 2 pour que rdv ne s'affiche pas 2 fois ds 1 heure
                              //on affiche que le matin
                          $date_cour = date ("Y-n-d");
                          $ch_date_cour = explode ("-",$date_cour);
                          if ($ch_date_cour[1] < 10) $date_cour = "$ch_date_cour[0]-0$ch_date_cour[1]-$ch_date_cour[2]";
                                        //On regarde si user est un apprenant car ds ce cas, on doit mattre une var ($app) à 1
                           //pour la gestion des rdv
                           $typ_user_app = $typ_user;
                           if ($typ_user_app == 'APPRENANT')
                             $app = 1;
                           else
                             $app=0;
                             for ($j=8,$k=1;$j<23;$k+=1,$j++)
                             {
                                          $marque_item = 0;
                                          $marque_supp = 0;
                                          if ($k > 22)
                                             break;
                                          $compteur ++;
                                          $l = $j + 1;
                                          echo" <tr height=\"15\" bordercolor=\"#006666\" bgcolor= \"#F4F4F4\">";
                                            ?>
                                            <td bgcolor="#348CA0" nowrap>
                                            <div align="center"><b><font face="Arial" color="white" size="2">&nbsp;
                                            <?php
                                            echo "$j $h - $l $h";
                                            echo "</font>";

                                        $nomjour = 1;
                                         while ($nomjour < 8)
                                         {
                                           echo "<td align=left><DIV id='sequence'>";
                                           $i=0;
                                           $marqueur = 0;
                                           while ($i != $Nb_rdv)
                                           {
                                             $date_act = $datee[$i];
                                             $nb_jours_act = nb_jours($date_act);
                                             if ($nb_jours_act <= $nb_jours_cour)
                                              $non_supp = 1;
                                             if ($num_sem_rdv[$i] == TRUE)
                                             {
                                                if ($id[$i] != 0 && $jour[$i] == $nom_de_jour[$nomjour] && $journee[$nomjour] == $datee[$i] && $groupe[$i] != 0 && $creneau[$i] == 0 && $k < 11){
                                                   $util_rdv = GetDataField ($connect, "select rdv_util_no from rendez_vous where rdv_cdn=$id[$i]","rdv_util_no");
                                                   if ($groupe[$i] > 0)
                                                   {
                                                      $marqueur = 1;
                                                      $nom_grp =GetDataField ($connect,"select grp_nom_lb from groupe where grp_cdn = $groupe[$i]","grp_nom_lb");
                                                   }
                                                   if ($k == 1 && $tut == 1 && $util_rdv == $id_user && $non_supp != 1)
                                                   {
                                                     $lien="agenda.php?supp=1&num_sem=$num_sem&rech=$rech&tut=1&rdv_id=$id[$i]&id_grp=$groupe[$i]&cren=$creneau[$i]&date_rdv=$datee[$i]&tuteur=$tuteur&utilisateur=$utilisateur[$i]&date=$datee[$i]";
                                                     $lien = urlencode($lien);
                                                     echo "<B>$comment[$i] $nom_grp</Font></B>&nbsp;&nbsp;&nbsp;".
                                                          "<A href=\"javascript:void(0);\" onclick=\"javascript:confm('trace.php?link=$lien');\" ".
                                                          "title=\"$mess_ag_supp\"><img src='images/suppression1.gif' border='0'></a>";
                                                   }
                                                   else
                                                   {
                                                     echo "$comment[$i] $nom_grp";
                                                   }
                                                   $nom_grp="";
                                                 }

                                                 if ($jour[$i] == $nom_de_jour[$nomjour] && $journee[$nomjour] == $datee[$i] && ($typ_user != 'APPRENANT' || ($typ_user == 'APPRENANT' && $creneau[$i] != 0 && $id[$i] != 0)))
                                                 {
                                                   $cren = Horaire($creneau[$i]);
                                                   list($valeur,$reste,$rest) = explode("h",$cren);
                                                   $dx =date("d/m/Y H:i:s",time());
                                                   list($dt,$hx) =  explode (" ",$dx);
                                                   list($hh,$mm,$ss) = explode(':',$hx);
                                                   if ($creneau[$i] == $k && $journee[$nomjour] == $datee[$i] && $id[$i] != 0 && $jour[$i] == $nom_de_jour[$nomjour]  && ($appre[$i] == 0 || $utilisateur[$i] > 0))
                                                   {
                                                    $util_rdv = GetDataField ($connect, "select rdv_util_no from rendez_vous where rdv_cdn=$id[$i]","rdv_util_no");
                                                    $typ_rdv = GetDataField ($connect, "select rdv_modecontact_lb from rendez_vous where rdv_cdn=$id[$i]","rdv_modecontact_lb");
                                                    if ($typ_rdv =="TELEPHONE")
                                                      $mode_rdv = $mess_gen_tel;
                                                    if ($typ_rdv =="CHAT")
                                                      $mode_rdv = $mess_gen_chat;
                                                    if ($typ_rdv =="RENCONTRE")
                                                      $mode_rdv = $mess_gen_renc;
                                                    if ($typ_rdv =="VISIO-CONF")
                                                      $mode_rdv = $mess_gen_visio;
                                                    $titre_rdv = GetDataField ($connect, "select rdv_titre_lb from rendez_vous where rdv_cdn=$id[$i]","rdv_titre_lb");
                                                    if ($typ_rdv == "AGENDA" && $titre_rdv != "Présentiel")
                                                    {
                                                      $util_rdv = GetDataField ($connect, "select rdv_util_no from rendez_vous where rdv_cdn=$id[$i]","rdv_util_no");
                                                      if ($util_rdv == $id_user && $journee[$nomjour] == $datee[$i])
                                                      {
                                                         $tit_rdv = str_replace("'","\'",$titre_rdv);
                                                         $nb_carac = strlen($tit_rdv);
                                                         if ($nb_carac > 10)
                                                            $tit_rdv1 = substr($tit_rdv,0,10)."..";
                                                         else
                                                            $tit_rdv1 = $tit_rdv;
                                                         $tit_rdv1 = stripslashes($tit_rdv1);
                                                         $com = str_replace("'","\'",$comment[$i]);
                                                         $com = str_replace('"','-',$com);
                                                         
                                                         echo "$passeOk<A HREF=\"javascript:void(0);\" title=\"$tit_rdv : $com\">$tit_rdv1</A>";
                                                      
                                                      }
                                                    }
                                                    elseif($typ_rdv == "AGENDA")
                                                    {
                                                         $marque_item = 1;
                                                         $marque_supp = 1;
                                                         $nom_grp =GetDataField ($connect,"select grp_nom_lb from groupe where grp_cdn = $groupe[$i]","grp_nom_lb");
                                                         echo "$comment[$i] $nom_grp";
                                                    }
                                                    elseif (($nb_jours_act > $nb_jours_cour && $typ_rdv != "AGENDA" && $app == 1 && $groupe[$i] == 0 && $marque_item == 0) || ($nb_jours_act >= $nb_jours_cour && $typ_rdv != "AGENDA" && $app != 1))
                                                    {
                                                         if ($direct == 1)
                                                             $rech = 0;
                                                         $lien="agenda.php?libre=1&rech=$rech&num_sem=$num_sem&ch_date[2]=$ch_date[2]&cren=$creneau[$i]&date=$datee[$i]&tut=$tut&tuteur=$tuteur&apprenant=$id_user&app=$app&date_rdv=$datee[$i]";
                                                         $lien = urlencode($lien);
                                                         if ($nb_jours_act > $nb_jours_cour)
                                                         {
                                                           if ($app == 1)
                                                              $titre_mess = $mess_ag_lib_rdv_app;
                                                            else
                                                              $titre_mess = $mess_ag_lib_rdv_tut;
                                                            echo "<a href='trace.php?link=$lien' target = 'main' title =\"$titre_mess\" ".
                                                                 "onclick=\"javascript:setTimeout('top.close()',1500)\";>".
                                                                 "$mess_ag_libre</a>&nbsp; $typ_rdv";
                                                         }
                                                         elseif($nb_jours_act == $nb_jours_cour && $typ_rdv != "AGENDA" && $app != 1)
                                                            echo "$passeOk $mess_ag_libre&nbsp;-&nbsp;$mode_rdv";
                                                    }
                                                 }
                                                 elseif ($tut == 1 && $creneau[$i] == $k && $jour[$i] == $nom_de_jour[$nomjour] && $id[$i] != 0 && $appre[$i] != 0 && $journee[$nomjour] == $datee[$i])
                                                 {
                                                    $typ_rdv = GetDataField ($connect, "select rdv_modecontact_lb from rendez_vous where rdv_cdn=$id[$i]","rdv_modecontact_lb");
                                                    if ($typ_rdv =="TELEPHONE")
                                                      $mode_rdv = $mess_gen_tel;
                                                    if ($typ_rdv =="CHAT")
                                                      $mode_rdv = $mess_gen_chat;
                                                    if ($typ_rdv =="RENCONTRE")
                                                      $mode_rdv = $mess_gen_renc;
                                                    if ($typ_rdv =="VISIO-CONF")
                                                      $mode_rdv = $mess_gen_visio;
                                                         if ($typ_rdv == "AGENDA")
                                                         {
                                                           $titre_rdv = GetDataField ($connect, "select rdv_titre_lb from rendez_vous where rdv_cdn=$id[$i]","rdv_titre_lb");
                                                         }
                                                         else
                                                         {
                                                           $nom_app = GetDataField ($connect, "select util_nom_lb from utilisateur,rendez_vous where rendez_vous.rdv_apprenant_no='$appre[$i]' and utilisateur.util_cdn=rendez_vous.rdv_apprenant_no","util_nom_lb");
                                                           $prenom_app = GetDataField ($connect, "select util_prenom_lb from utilisateur,rendez_vous where rendez_vous.rdv_apprenant_no='$appre[$i]' and utilisateur.util_cdn=rendez_vous.rdv_apprenant_no","util_prenom_lb");
                                                           echo "$prenom_app $nom_app";
                                                         }
                                                   if ( $typ_rdv != "AGENDA" && ($typ_rdv != "CHAT" || ($typ_rdv == "CHAT" && ($date_cour != $datee[$i] ||($date_cour == $datee[$i] && $hh != $valeur)))))
                                                     echo " - $mode_rdv";
                                                   elseif ($typ_rdv == "AGENDA")
                                                   {
                                                     echo "<a href=\"agenda.php?tut=1&num_sem=$num_sem\" target='main'>  $titre_rdv</a>";
                                                   }
                                                   elseif ($typ_rdv == "CHAT" && $date_cour == $datee[$i] && $hh == $valeur){
                                                     $lien_retour = "agenda2";
                                                     $message = "<CENTER>$mess_aparte $mess_votre_apprenant <B>$prenom_app $nom_app</B></CENTER>";
                                                     $lien="flash_chat/chat/index.php?person=$person&password=$prenom_user&destinatario=$nom_app&message=$message&lien_retour=agenda2";
                                                     echo "$passeOk<a href=\"javascript:void(0);\" onclick =\"window.open('$lien','','width=550,height=340,resizable=yes,status=no')\" title =\"$mess_ag_go_chat\">&nbsp;&nbsp;$typ_rdv</a>";//onMouseOver=\"overlib('$mess_ag_go_chat',ol_hpos,CENTER,CAPTION, '<center>$mess_ag_pop_tit</center>')\" onMouseOut=\"nd()\"
                                                   }
                                                 } //fin else if $creneau}
                                                 elseif($app == 1 && $creneau[$i] == $k && $jour[$i] == $nom_de_jour[$nomjour] && $id[$i] != 0 && $appre[$i] != 0 && $journee[$nomjour] == $datee[$i])
                                                 {
                                                    $typ_rdv = GetDataField ($connect, "select rdv_modecontact_lb from rendez_vous where rdv_cdn=$id[$i] && (rdv_apprenant_no='$appre[$i]'  or rdv_util_no = '$utilisateur[$i]') && rdv_creneau_nb = '$creneau[$i]'","rdv_modecontact_lb");
                                                    if ($typ_rdv =="TELEPHONE")
                                                      $mode_rdv = $mess_gen_tel;
                                                    if ($typ_rdv =="CHAT")
                                                      $mode_rdv = $mess_gen_chat;
                                                    if ($typ_rdv =="RENCONTRE")
                                                      $mode_rdv = $mess_gen_renc;
                                                    if ($typ_rdv =="VISIO-CONF")
                                                      $mode_rdv = $mess_gen_visio;
                                                    if ($typ_rdv == "AGENDA") {
                                                    $titre_rdv = GetDataField ($connect, "select rdv_titre_lb from rendez_vous where rdv_cdn=$id[$i] && rdv_date_dt= '$datee[$i]' && rdv_util_no = '$utilisateur[$i]' && rdv_creneau_nb = '$creneau[$i]'","rdv_titre_lb");
                                                    }
                                                    else
                                                    {
                                                      $nom_tut = GetDataField ($connect, "select util_nom_lb from utilisateur,rendez_vous where rendez_vous.rdv_apprenant_no='$appre[$i]' and utilisateur.util_cdn=rendez_vous.rdv_tuteur_no and rendez_vous.rdv_cdn=$id[$i]","util_nom_lb");
                                                      $prenom_tut = GetDataField ($connect, "select util_prenom_lb from utilisateur,rendez_vous where rendez_vous.rdv_apprenant_no='$appre[$i]' and utilisateur.util_cdn=rendez_vous.rdv_tuteur_no and rendez_vous.rdv_cdn=$id[$i]","util_prenom_lb");
                                                    }
                                                 if ($appre[$i] == $id_user && $typ_rdv != "AGENDA")
                                                 {
                                                   echo "$prenom_tut $nom_tut";
                                                   if ($typ_rdv != "CHAT" || ($typ_rdv == "CHAT" && ($date_cour != $datee[$i] || ($date_cour == $datee[$i] && $hh != $valeur))))
                                                     echo " - $mode_rdv";
                                                   elseif ($typ_rdv == "CHAT" && $date_cour == $datee[$i] && $hh == $valeur)
                                                   {
                                                     $message = "<CENTER>$mess_aparte $mess_votre_tuteur <B>$prenom_tut $nom_tut</B></CENTER>";
                                                     $lien="flash_chat/chat/index.php?person=$person&password=$prenom_user&destinatario=$nom_tut&message=$message&lien_retour=agenda1";
                                                     echo "$passeOk<a href=\"#\" onclick =\"window.open('$lien','','width=550,height=340,resizable=yes,status=no')\" title=\"$mess_ag_go_chat\">&nbsp;&nbsp;$typ_rdv</a>";
                                                   }
                                                  }
                                                 }
                                                 //fin else if $creneau}
                                              }
                                                 if (((($tut == 1 || ($app == 1 && $appre[$i] == $id_user)) && $typ_rdv != "AGENDA") || ($typ_rdv == "AGENDA" && $util_rdv == $id_user)) && $id[$i] != 0 && $creneau[$i] == $k && $jour[$i] == $nom_de_jour[$nomjour] && $date_cour != $datee[$i] && $journee[$nomjour] == $datee[$i] && $non_supp != 1)
                                                 {
                                                   //On est oblige de passer $tut par l'url pour afficher le lien pour proposer des rdv
                                                   if ($tut == 1)
                                                      $envoyeur = "tut";
                                                   else
                                                   {
                                                      $envoyeur = "app";
                                                      $id_tut = GetDataField ($connect, "select rdv_tuteur_no from rendez_vous where rendez_vous.rdv_apprenant_no='$appre[$i]' and rendez_vous.rdv_cdn=$id[$i]","rdv_tuteur_no");
                                                   }
                                                   $lien="agenda.php?supp=1&rech=0&num_sem=$num_sem&ch_date[2]=$ch_date[2]&date=$datee[$i]&tut=$tut&le_type_rdv=$typ_rdv&envoyeur=$envoyeur&apprenant=$app&rdv_id=$id[$i]&cren=$creneau[$i]&date=$date&date_rdv=$datee[$i]&tuteur=$tuteur&tut_supp=$id_tut&utilisateur=$utilisateur[$i]&app_supp=$appre[$i]";
                                                   $lien = urlencode($lien);
                                                   if ($typ_user == 'APPRENANT' && $tuteur > 0)
                                                      echo "";
                                                   else
                                                   {
                                                     if (isset($comment[$i]) && isset($comment[$i+1]) && isset($journee[$nomjour]) && isset($datee[$i]) && $comment[$i] == $comment[$i+1] && $journee[$nomjour] == $datee[$i] && $titre_rdv == "Présentiel")
                                                     {
                                                     }
                                                     else
                                                     {
                                                         echo "&nbsp;&nbsp;&nbsp<A href=\"javascript:void(0);\" ".
                                                              "onclick=\"javascript:confm('trace.php?link=$lien');\" ".
                                                              "title=\"$mess_ag_supp\"><img src='images/suppression1.gif' border='0'></a>";
                                                     }
                                                   }
                                                   if ($marque_supp == 1 && $typ_rdv == "AGENDA" && $titre_rdv == "Présentiel")
                                                      $marque_supp = 0;
                                                 }
                                                  $passeOk='<br />';
                                              }//fin if ($nmu_sem...)
                                           $non_supp = 0;
                                           $i++;
                                         }
                                         $passeOk='';
                                         echo "</DIV></TD>";
                                         $nomjour++;
                                       }// fin while pour $nomjour
                                       echo  "</TR>";
                                   } //fin for
                                    echo "</TABLE>";
                                     if ($typ_user == "APPRENANT")
                                     {
                                       echo "<TABLE cellspacing='8' width='100%' border='0'><TR>";
                                       if ($tuteur == 0)
                                       {
                                         if ($agenda != 1 && $acces == "annonce_grp")
                                         {
                                           if ($menu_prov == 1)
                                             $vient_de_menu = 'menu';
                                           $lien = "annonce_grp.php?vient_de_menu=$vient_de_menu";
                                           $lien = urlencode($lien);
                                           echo "<TD align='left' valign='center' width='90'><A HREF=\"trace.php?link=$lien\" onmouseover=\"img_annonce.src='images/fiche_identite/boutretourb.gif';return true;\" onmouseout=\"img_annonce.src='images/fiche_identite/boutretour.gif'\">";
                                           echo "<IMG NAME=\"img_annonce\" SRC=\"images/fiche_identite/boutretour.gif\" BORDER='0' onLoad=\"tempImg=new Image(0,0); tempImg.src='images/fiche_identite/boutretourb.gif'\"></A></TD>";
                                         }
                                       }
                                       echo "<TD align='center'><TABLE><TR>$navi";
                                      echo "</TR></TABLE></TD></TR></TABLE>";
                                     }
                          }//fin if ($ch_date[2])
                   echo "</TD></TR></TABLE></TD></TR></TABLE>";

                // On a une repetition des memees actions ms c plus clair en separant les cas
                // envoi d'Emails à chaque apprenant du groupe en question
                if (isset($date_rdv))
                {
                   $ch_rdv_supp = ( strstr($date_rdv,'/')) ? explode ("/",$date_rdv) :  explode ("-",$date_rdv);
                   $date_rdv = "$ch_rdv_supp[2]/$ch_rdv_supp[1]/$ch_rdv_supp[0]";
                }
                if ($supp == 1 && $id_grp > 0)
                {
                  $liste = mysql_query("select * from utilisateur,utilisateur_groupe where utilisateur.util_cdn = utilisateur_groupe.utilgr_utilisateur_no AND utilisateur_groupe.utilgr_groupe_no=$id_grp");
                  $nbr = mysql_num_rows($liste);
                  if ($nbr>0){
                     $message = "$convoc_regroup $prenom_user $nom_user\n$mess_ag_date_rdv $date_rdv\n$mess_ag_annule.\n$mess_ag_cordial\n\n";
                     $message_base = str_replace("\n","<BR>",$message);
                    $i = 0;
                    while ($i < $nbr){
                      $num = mysql_result($liste,$i,"util_cdn");
                      $email = mysql_result($liste,$i,"util_email_lb");
                      $reply = $adr_mail;
                      $from = $adr_mail;
                      $sendto = $email;
                      $subject = StripSlashes($mess_ag_ag);
                      $msg = StripSlashes($message);
                      $origine=$nom_user."  ".$typ_user;
                      $nom ='';
                      $userfile = "none";
                      if (isset($_SESSION['IsOff']) && $_SESSION['IsOff'] == 0)
                      {
                         if ($sendto != "")
                             $envoi=mail_attachement($sendto , $subject , $msg , $userfile ,$reply, $nom, $from );
                      }
                      $max_numero = Donne_ID ($connect,"select max(mess_cdn) from messagerie");
                      $requete = mysql_query("INSERT INTO messagerie (mess_cdn,envoyeur,origine,contenu,date,sujet,id_user) VALUES ($max_numero,'$id_user','Réservation journée',\"$message_base\",'$date_messagerie','$subject',$num)");
                    $i++;
                    }
                  }
                }
                if ($supp == 1 && $id_grp < 1){
                   $horaire = Horaire ($cren1);
                   //On envoie un email pour lui rappeler quelle plage horaire il a propose
                   $adr_mail = GetDataField ($connect, "select util_email_lb from utilisateur where util_cdn=$id_user","util_email_lb");

                       $message = "$mess_ag_sup_plg_hor $date_rdv $mess_ag_durant_cren $horaire.\n
                                        $mess_ag_cordial\n\n";
                       $from = $adr_mail;
                       $reply = $adr_mail;
                       $sendto = $adr_mail;
                       $subject = StripSlashes($mess_ag_ag);
                       $msg = StripSlashes($message);
                       $origine=$nom_user."  ".$typ_user;
                       $nom ='';
                       $userfile = "none";
                       if (isset($_SESSION['IsOff']) && $_SESSION['IsOff'] == 0)
                       {
                         if ($sendto != "")
                             $envoi=mail_attachement($sendto , $subject , $msg , $userfile ,$reply, $nom, $from );
                       }
                       $message_base = str_replace("<BR>","\n",$message);
                       $max_numero = Donne_ID ($connect,"select max(mess_cdn) from messagerie");
                       $requete = mysql_query("INSERT INTO messagerie (mess_cdn,envoyeur,origine,contenu,date,sujet,id_user) VALUES ($max_numero,'$id_user','$mess_ag_rdv_tut',\"$message_base\",'$date_messagerie','$subject','$id_user')");
                     if ($app_supp > 0)
                     {
                      if ($envoyeur == "tut"){
                        $a_mail = GetDataField ($connect, "select util_email_lb from utilisateur where util_cdn=$app_supp","util_email_lb");
                        $nom_user=GetDataField ($connect,"select util_nom_lb from utilisateur where util_cdn='$tut_supp'","util_nom_lb");
                        $prenom_user=GetDataField ($connect,"select util_prenom_lb from utilisateur where util_cdn='$tut_supp'","util_prenom_lb");
                        $message = "$mess_ag_votre_rdv  $date_rdv $mess_ag_durant_cren $horaire $mess_ag_avec $prenom_user $nom_user $mess_ag_annule\n
                                   $mess_ag_cordial\n\n";
                        $id_mess = $app_supp;
                      }elseif ($envoyeur == "app")  {
                        $a_mail = GetDataField ($connect, "select util_email_lb from utilisateur where util_cdn=$tut_supp","util_email_lb");
                        $nom_user=GetDataField ($connect,"select util_nom_lb from utilisateur where util_cdn='$app_supp'","util_nom_lb");
                        $prenom_user=GetDataField ($connect,"select util_prenom_lb from utilisateur where util_cdn='$app_supp'","util_prenom_lb");
                        $message= "$mess_ag_votre_rdv $date_rdv $mess_ag_durant_cren $horaire $mess_ag_avec $prenom_user $nom_user $mess_ag_annule\n
                                  $mess_ag_cordial\n\n";
                        $id_mess = $tut_supp;
                      }
                      $from = $adr_mail;
                      $reply = $adr_mail;
                      $sendto = $a_mail;
                      $subject = StripSlashes($mess_ag_ag);
                      $msg = StripSlashes($message);
                      $origine=$nom_user."  ".$typ_user;
                       $nom ='';
                       $userfile = "none";
                       if (isset($_SESSION['IsOff']) && $_SESSION['IsOff'] == 0)
                       {
                         if ($sendto != "")
                             $envoi=mail_attachement($sendto , $subject , $msg , $userfile ,$reply, $nom, $from );
                       }
                      $message_base = "$mess_sup_rdv_fiche  $date_rdv $mess_ag_durant_cren $horaire $mess_ag_annule<BR>$mess_ag_cordial";
                      $max_numero = Donne_ID ($connect,"select max(mess_cdn) from messagerie");
                      $requete = mysql_query("INSERT INTO messagerie (mess_cdn,envoyeur,origine,contenu,date,sujet,id_user) VALUES ($max_numero,'$id_user','$mess_ag_rdv_tut',\"$message_base\",'$date_messagerie','$subject','$id_mess')");
                     }// fin if ($app_supp > 0)
               }  //fin if ($supp == 1)
               //Dans le cas ou le tuteur a oublie de remplir un des champs lorsqu'il propose une plage horaire de rendez-vous
               if ($form_vide == 1)
               {
                   echo "$msq_oubli_champ_oblig<br>";
                   $lien="agenda.php?prop_rdv=1&tuteur=$tuteur";
                   $lien = urlencode($lien);
                   echo "<a href=\"trace.php?link=$lien\" target='main'>$mess_ret_form</a>";
               }
               ?>
               <DIV ID="top">
                       <SCRIPT Language="Javascript" TYPE="text/javascript">
                               Calendar.CreateCalendarLayer(10, 275, "");

                       </SCRIPT>
               </DIV>

               <?php
?>

</BODY></HTML>
