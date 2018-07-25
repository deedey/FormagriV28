<?php
if (!isset($_SESSION)) session_start();
if (!isset($_SESSION['id_user']) || $_SESSION['id_user'] == "")
{
  exit();
}
include ("include/UrlParam2PhpVar.inc.php");
require 'fonction.inc.php';
require 'fonction_html.inc.php';
require "lang$lg.inc.php";
require 'admin.inc.php';
dbConnect();
include 'style.inc.php';
$date_op = date("Y-m-d H:i:s" ,time());
$heure_fiche = substr($date_op,11);
$date_fiche = substr($date_op,0,10);
$email_user=GetDataField ($connect,"select util_email_lb from utilisateur where util_cdn = $id_user","util_email_lb");
//include ("click_droit.txt");
if ($debut == 2)
{
  $num_tut = GetdataField ($connect,"select util_cdn from utilisateur where util_nom_lb='$tuteur'","util_cdn");
  $sql = mysql_query("INSERT INTO tuteur (tut_apprenant_no,tut_tuteur_no) VALUES ($numero,$num_tut)");
    echo "<CENTER><FONT COLOR=blue><B>$mess_insc_comp ;</B></FONT></CENTER>";
  exit;
}
if ($debut == 1)
{
  $etat_messInsc = GetDataField($connect,"select param_etat_lb from param_foad where param_typ_lb='mess_inscription'","param_etat_lb");
  if ($etat_messInsc == 'OUI')
  {
       $reqMess = mysql_query("SELECT * from message_inscription");
       if (mysql_num_rows($reqMess) > 0)
       {
          $mess_insc_mess1 = mysql_result($reqMess,0,'mi_text_cmt');
          $mess_insc_mess2 = mysql_result($reqMess,1,'mi_text_cmt');
          $mess_insc_mess3 = mysql_result($reqMess,2,'mi_text_cmt');
          $mess_insc_mess6 = mysql_result($reqMess,3,'mi_text_cmt');
          $mess_insc_mess4 = mysql_result($reqMess,4,'mi_text_cmt');
       }

  }
  if(!isset($_FILES["userfile"]["tmp_name"]))
  {
    echo "<CENTER><FONT COLOR=marroon><B>$mess_gen_ins</B></FONT><BR><BR>";
    $lien = "inscription_groupee.php?retour=1";
    $lien=urlencode($lien);
    echo "<CENTER><FONT COLOR='white' size='3'><B>$mess_insc_titre_grp</B></FONT><P>";
    echo "<TABLE bgColor='#298CA0' cellspacing='2' width='100%' ><TR><TD style='text-align:left;'>";
    echo "<TABLE bgColor='#FFFFFF' cellspacing='1' width='100%'><TR><TD style='text-align:left;'>";
    echo "<CENTER><BIG>$mess_ret_form</font><P>";
    echo "<A href=\"trace.php?link=$lien\" target='main'>$mess_form_retour</A>";
    echo "</TD></TR></TABLE></TD></TR></TABLE>";
   exit;
  }
  else
  {
    list($extension,$nom) = getextension($_FILES["userfile"]["name"]);
    if (in_array(strtolower($extension), array("exe","sh","py", "ida","idq","asp","cer","cdx","asa","idc","cfm","dbm","php","php4","inc","shtml","cgi")))
           $mess_notif = $_FILES["userfile"]["name"]." : extension du fichier non autorisée.";
    else
    {
     $nom_fichier = $_FILES["userfile"]["name"];
     $taille_file = filesize($_FILES["userfile"]["tmp_name"]);
     $longueur = strlen($nom_fichier);
     $extension = substr($nom_fichier,$longueur-4,4);
     $dir_ecrit = "ressources/".$nom_fichier;
     $fichier = $nom_fichier;
     $nom_final = modif_nom($fichier);
     $dest_file="ressources/".$nom_final;
     $source_file = $_FILES["userfile"]["tmp_name"];
     $copier = move_uploaded_file($source_file , $dest_file);
     chmod ($dest_file,0777);
     $fl = fopen($dest_file, "r");
     while (!feof($fl))
     {
        $email="";
        $passe="";
        $log="";
        $grandnom="";
        $prenom="";
        $ligne = fgets($fl, 4096);
        $liste = explode($separateur,$ligne);
        if (!strstr($ligne, $separateur) || count($liste) < 5 || ((count($liste) == 5 || count($liste) == 7) && ($liste[0] =="" || $liste[1] =="")) || !strstr($extension,strtolower("txt")))
        {
           $lien = "inscription_groupee.php?retour=1";
           $lien=urlencode($lien);
           echo "<CENTER><FONT COLOR='white' size='3'><B>$mess_insc_titre_grp</B></FONT><P>";
           echo "<TABLE bgColor='#298CA0' cellspacing='2' width='640' ><TR><TD style='text-align:left;'>";
           echo "<TABLE bgColor='#FFFFFF' cellspacing='1' width='100%'><TR><TD style='text-align:left;'>";
           echo "<Font size='3'>$mess_insc_alert</font><P>";
           echo "<A href=\"trace.php?link=$lien\" target='main'>$mess_form_retour</A>";
           echo "</TD></TR></TABLE></TD></TR></TABLE>";
           exit;
        }
        if (!empty($liste[2]) && strstr($liste[2],"@"))
            $req_verif = mysql_query("select count(*) from utilisateur where util_nom_lb = '".$liste[0]."' AND  util_prenom_lb = '".$liste[1]."' AND util_email_lb = '".modif_az_qw($liste[2])."'");
        else
            $req_verif = mysql_query("select count(*) from utilisateur where util_nom_lb = '".$liste[0]."' AND  util_prenom_lb = '".$liste[1]."'");
        $nombre_verif = mysql_result($req_verif,0);
        if ($nombre_verif > 0)
        {
           if ($passage == 0)
              echo "<CENTER><TABLE width='640' bgcolor='#FFFFFF'>";
           $message_alerte .= "<FONT  size='2'>Quelqu'un porte déja ce nom et ce prénom sur la plate-forme: <B>".$liste[0]." ".$liste[1];
           if (!empty($liste[2]) && strstr($liste[2],"@"))
              $message_alerte .= "</B> ou cet email : <B>".$liste[2];
           $message_alerte .= "</B></FONT><BR>";
           $passage++;
           continue;
        }
        if (!isset($leprofil) || (isset($leprofil) && $leprofil == '1'))
        {
           // Génération d'un mot de passe de 6 lettres
           if (count($liste) == 6 && $liste[2] != "" && strstr($liste[2],"@"))
           {
               $email = modif_az_qw($liste[2]);
               $controle = verifie_email($email);
               if ($controle != $email)
               {
                   $lien = "inscription_groupee.php";
                   $lien=urlencode($lien);
                   echo "<CENTER><FONT COLOR='white' size='3'><B>$mess_menu_inscrip</B></FONT><P>";
                   echo "<TABLE bgColor='#298CA0' cellspacing='2' width='100%' ><TR><TD style='text-align:left;'>";
                   echo "<TABLE bgColor='#FFFFFF' cellspacing='1' width='100%'><TR><TD style='text-align:left;'>";
                   echo "<P><CENTER><FONT  size='2'><B>$controle</B></FONT><P></CENTER>";
                   echo "<A href=\"trace.php?link=$lien\" target='main'>$mess_form_retour</A><BR>";
                   echo "</TD></TR></TABLE></TD></TR></TABLE>";
                   exit;
               }
               list($compte, $domaine)=explode("@", $email);
               $log = $compte;
               $req = mysql_query("select * from utilisateur where util_login_lb = '$compte'");
               $nombre = mysql_num_rows($req);
               if ($nombre > 0)
                   $log = formagri_genmotpass(8);
          }
          elseif (count($liste) == 8)
          {
              $email = modif_az_qw($liste[4]);
              $log = $liste[2];
              $req = mysql_query("select * from utilisateur where util_login_lb like '$log%'");
              $nombre = mysql_num_rows($req);
              if ($nombre > 0)
              {
                  $l = $nombre+1;
                  $log = $liste[2].$l;
              }
          }
          else
              $log = formagri_genmotpass(8);
          //NOTA ---->  zone d'insertion des données dans la base
          echo "<CENTER><TABLE width='640' bgcolor='#FFFFFF'>";
          if (count($liste) == 6)
          {
             $prenom = $liste[1];
             $passe= formagri_genmotpass(6);
             $telephone = $liste[3];
             $urlwebmail = $liste[4];
             $logue_cas = $liste[5];
          }
          elseif (count($liste) == 8)
          {
             $prenom = $liste[1];
             $passe = $liste[3];
             $telephone = $liste[5];
             $urlwebmail = $liste[6];
             $logue_cas = $liste[7];
          }
        }
        elseif (isset($leprofil) && $leprofil == '2')
        {
            $grandnom = $liste[0];
            $prenom = $liste[1];
            $email = modif_az_qw($liste[2]);
            $telephone = $liste[3];
            $urlwebmail = $liste[4];
            $type_user = $liste[5];
            $log = $liste[6];
            $passe = $liste[7];
            $logue_cas = $liste[8];
        }
        $nom_final="";
        $grandnom = $liste[0];
        $grandnom = modif_nom($grandnom);
        $grandnom = str_replace("-","_",$grandnom);
        $id_insc = Donne_ID ($connect,"select max(util_cdn) from utilisateur");
        $sql = mysql_query("INSERT INTO utilisateur (util_cdn,util_nom_lb,util_prenom_lb,util_email_lb,util_tel_lb,util_urlmail_lb,util_typutil_lb,util_login_lb,util_motpasse_lb,util_logincas_lb,util_blocageutilisateur_on,util_auteur_no,util_date_dt) VALUES ($id_insc,\"$grandnom\",\"$prenom\",\"$email\",\"$telephone\",\"$urlwebmail\",\"$type_user\",\"$log\",\"$passe\",\"$logue_cas\",\"NON\",'$id_user',\"$date_op\")");
        $new_fiche = Donne_ID ($connect,"select max(fiche_cdn) from fiche_suivi");
        $req_fiche = mysql_query("INSERT INTO fiche_suivi (fiche_cdn,fiche_utilisateur_no,fiche_auteur_no,fiche_qualite_lb,fiche_date_dt,fiche_heure_dt,fiche_commentaire_cmt,fiche_grp_no,fiche_parc_no,fiche_seq_no,fiche_act_no,fiche_autraction_lb) VALUES($new_fiche,$id_insc,$id_user,'Inscripteur','$date_fiche','$heure_fiche',\"Inscription dans la plate-forme\",0,0,0,0,\"observation\")");
        //Création du casier ou du bureau
        $numero=$id_insc;
        $dir="ressources";
        $nouveau_rep=$log."_".$numero;
        chdir($dir);
        mkdir($nouveau_rep,0777);
        chmod($nouveau_rep,0777);
        chdir($nouveau_rep);
        mkdir("ressources",0777);
        mkdir("devoirs",0777);
        chmod("devoirs",0777);
        chmod("ressources",0777);
        chdir("../../");
        // envoi du courrier
        $message= "$prenom $grandnom<br> $mess_insc_mess1<br>$mess_insc_mess2 $log<br>$mess_insc_mess3 $passe<br>$mess_insc_mess6 $adresse_http<br>$mess_insc_mess4<br><br>$mess_ag_cordial";
        $from = $email_user;
        $reply = $email_user;
        $adr_mail = $from;
        $sendto = $email;
        $subject = StripSlashes($mess_auth_serv_forma)." : ".strtolower($mess_insc_titre);
        $msg = StripSlashes($message);
        $origine = $nom_user."  ".$typ_user;
        $userfile = "none";
        if ($email != "" && (isset($_POST['envoi_mail']) && $_POST['envoi_mail']== 'on'))
        {
          $envoi = mail_attachement($sendto , $subject , NewHtmlEntityDecode($msg,ENT_QUOTES) , $userfile , $reply, $nom, $from);
          $parti = 1;
        }
        else
          $parti = 0;
        $date_message = date("d/m/Y H:i:s" ,time());
        $message_base = "<B>$prenom $grandnom</B>, $mess_insc_mess1<BR> $mess_insc_mess2<B>$log</B><BR>$mess_insc_mess3 <B>$passe</B><BR>$mess_insc_mess6<B> $adresse_http</B><BR>$mess_insc_type <B>$type_user</B><BR>$mess_insc_mess4<BR>$mess_ag_cordial";
        $message_auteur .= "$prenom $grandnom -- $mess_insc_mess3 <B>$log</B> -- $mess_insc_mess2 <B>$passe</B><BR>";
        $max_numero = Donne_ID ($connect,"select max(mess_cdn) from messagerie");
        $requete = mysql_query("INSERT INTO messagerie (mess_cdn,envoyeur,origine,contenu,date,sujet,id_user) VALUES ($max_numero,'$id_user','Inscription',\"$message_base\",'$date_message','$subject',$id_insc)");
        if ($email != "" && $parti == 0   && (isset($_POST['envoi_mail']) && $_POST['envoi_mail']== 'on'))
          echo "<TR><TD style='text-align:left;'><FONT size='3'><B>$mail_no_ok</B></FONT></TD></TR>";
        elseif ($email != "" && $parti == 0 && (!isset($_POST['envoi_mail']) || (isset($_POST['envoi_mail']) && $_POST['envoi_mail'] == 'on')))
          echo "<TR><TD style='text-align:left;'><FONT size='3'><B>$prenom $grandnom $mess_insc_lms</B></FONT></TD></TR>";
        elseif ($email != "" && $parti == 1)
          echo "<TR><TD style='text-align:left;'><FONT size='3'<B>$mess_insc_mess_aff $prenom $grandnom $mess_insc_adr $email<BR></B></FONT></TD></TR>";
        elseif ($email == "")
          echo "<TR><TD style='text-align:left;'><FONT size='3'<B>$prenom $grandnom $mess_insc_lms<BR></B></FONT></TD></TR>";
    }
    if ($passage > 0)
       echo "<TR><TD style='text-align:left;'><FONT size='2'><B>$message_alerte</B></FONT></TD></TR>";
    echo "</TABLE><P>&nbsp;";
    fclose($fl);
    if ($passage == 0)
    {
      $max_numero = Donne_ID ($connect,"select max(mess_cdn) from messagerie");
      $requete = mysql_query("INSERT INTO messagerie (mess_cdn,envoyeur,origine,contenu,date,sujet,id_user) VALUES ($max_numero,'$id_user','Inscription_groupée',\"$message_auteur\",'$date_message','$subject',$id_user)");
    }
   }
  }
}// Module principal d'inscription
?>
<SCRIPT language=JavaScript>
function checkForm(frm) {
  var ErrMsg = "<?php echo $mess_info_no;?>\n";
  var lenInit = ErrMsg.length;
  if (isEmpty(frm.userfile)==true)
    ErrMsg += ' - <?php echo $mess_casier_fat;?>\n';
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
if (isset($mess_notif) && $mess_notif != '')
   echo notifier($mess_notif);

$agent=getenv("HTTP_USER_AGENT");
if (strstr($agent,"Mac") || strstr($agent,"Konqueror") || strstr(strtolower($agent),"safari"))
  $mac=1;
if (strstr($agent,"Win"))
  $win=1;

entete_simple($mess_insc_titre_grp);
echo "<TR><TD style='text-align:left;'><TABLE cellspacing='0' cellpadding='0'><TR><TD style='text-align:left;' nowrap colspan='3'>".aide_div('inscription',4,0,3,3)."</TD></TR>";
echo "<FORM NAME='form1' action=\"inscription_groupee.php\" target='main' method='post' enctype='multipart/form-data'>";
echo "<TR><TD style='text-align:left;' colspan='2' width='100%'><TABLE bgColor='#FFFFFF' cellpadding='3' cellspacing='0' width='100%'>";
echo "<TR><TD style='text-align:left;' colspan='2'><div style='border:1px solid #24677A;background-color:#EFEFEF;padding:4px;margin:6px;'>".
     "$mess_insc_obs</div></TD></TR>";
echo "<TR><TD style='text-align:left;'>$mess_chx_sep</TD>";
echo "<TD style='text-align:left;'><SELECT name='separateur' class='SELECT'>";
echo "<OPTION value=';' selected><B>;</B></OPTION>";
echo "<OPTION value='|'><B>|</B></OPTION>";
echo "<OPTION value=','><B>,</B></OPTION>";
echo "</SELECT></TD></TR>";
echo "<INPUT TYPE='HIDDEN' NAME='debut' VALUE='1'>";
if ($typ_user == "ADMINISTRATEUR")
{
   echo "<tr>";
   // dey Dfoad
   echo "<TR><TD style='text-align:left;' valign='top'>$profil";
   if ($id_user == 1)
   {
     echo " <span  style='margin:0 0 0 15px;font-weight:bold;'>mono-profil </span> ".
          "<input type ='radio' name='leprofil' value='1' checked onclick=\"\$('#leprofil').css('display','block');\" ".
          bullet("Cochez ici pour un profil unique puis sélectionnez un profil","","RIGHT","ABOVE",205)."/>";
     echo " <span  style='margin:0 0 0 15px;font-weight:bold;'>multi-profil </span> ".
          "<input type ='radio' name='leprofil' value='2' onclick=\"\$('#leprofil').css('display','none');\" ".
          bullet("Cochez ici pour choisir un mode multi-profils (importation de tous les utilisateurs d'une formation)","","RIGHT","ABOVE",205)."/>";
   }
   echo "</TD><TD style='text-align:left;'>";
   echo "<div id='leprofil'><SELECT name='type_user' class='SELECT'>";
   echo "<OPTION value = \"ADMINISTRATEUR\">$mess_typ_adm</OPTION>";
   echo "<OPTION value = \"RESPONSABLE_FORMATION\">$mess_typ_rf</OPTION>";
   echo "<OPTION value = \"FORMATEUR_REFERENT\">$mess_typ_fr</OPTION>";
   echo "<OPTION value = \"TUTEUR\">$mess_typ_tut</OPTION>";
   echo "<OPTION value = \"APPRENANT\" selected>$mess_typ_app</OPTION>";
   echo "</SELECT></div>";
   echo "</TD></TR>";
}
if ($typ_user == "RESPONSABLE_FORMATION")
   echo "<INPUT TYPE='HIDDEN' NAME='type_user' VALUE='APPRENANT'>";
echo "<TR><TD style='text-align:left;' nowrap>$mess_tit_insc</TD>";
echo "<TD style='text-align:left;' nowrap>";
echo "<INPUT TYPE='file' class='INPUT' name='userfile' size='35' enctype='multipart/form-data'>";
echo "</TD></TR>";
echo "<TR><TD style='text-align:left;' nowrap>$mess_inscgrp_mail</TD>";
echo "<TD style='text-align:left;'><INPUT TYPE='checkbox' name='envoi_mail'></TD></TR>";
echo "<TR height='40'><TD style='text-align:left;'>&nbsp;</TD><TD style='text-align:left;' align='left' valign='center'>".
     "<A HREF=\"javascript:checkForm(document.form1);\" ".
     "onmouseover=\"img1.src='images/fiche_identite/boutvalidb.gif';return true;\" ".
     "onmouseout=\"img1.src='images/fiche_identite/boutvalid.gif'\">".
     "<IMG NAME=\"img1\" SRC=\"images/fiche_identite/boutvalid.gif\" BORDER='0' ".
     "onLoad=\"tempImg=new Image(0,0); tempImg.src='images/fiche_identite/boutvalidb.gif'\"></A>";
echo "</TD></TR></TABLE></TD></TR></FORM></TABLE></TD></TR></TABLE>";

function getextension($myfile){
  $bouts = explode(".", $myfile);
  return array(array_pop($bouts), implode(".", $bouts));
}
?>
<div id="mien" class="cms"></div>
</body></html>