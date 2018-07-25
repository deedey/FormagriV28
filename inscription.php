<?php
if (!isset($_SESSION)) session_start();
if (!isset($_SESSION['id_user']) || $_SESSION['id_user'] == "")
{
  exit();
}
include ("include/UrlParam2PhpVar.inc.php");
require 'admin.inc.php';
require 'fonction.inc.php';
require 'fonction_html.inc.php';
require "lang$lg.inc.php";
dbConnect();
include 'style.inc.php';
$date_op = date("Y-m-d H:i:s" ,time());
$heure_fiche = substr($date_op,11);
$date_fiche = substr($date_op,0,10);
$email_user=GetDataField ($connect,"select util_email_lb from utilisateur where util_cdn = $id_user","util_email_lb");
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
/*
  if (isEmail(frm.email)==false)
    ErrMsg += ' - <?php echo $mess_admin_email;?>\n';
*/
?>
<SCRIPT language=JavaScript>
function checkForm(frm) {
  var ErrMsg = "<?php echo $mess_info_no;?>\n";
  var lenInit = ErrMsg.length;
  if (isEmpty(frm.grandnom)==true)
    ErrMsg += ' - <?php echo $mess_admin_nom;?>\n';
  if (isEmpty(frm.prenom)==true)
    ErrMsg += ' - <?php echo $mess_admin_prenom;?>\n';
  if (ErrMsg.length > lenInit)
    alert(ErrMsg);
  else
    frm.submit();
}
function isEmail(elm) {
  if (elm.value.indexOf(" ") + "" == "-1" && elm.value.indexOf("@") + "" != "-1" && (elm.value.lastIndexOf(".") > elm.value.indexOf("@")) && elm.value != "")
     return true;
  else
     return false;
}
function isEmpty(elm) {
  var elmstr = elm.value + "";
  if (elmstr.length == 0)
    return true;
  return false;
}
</SCRIPT>
<?php
//include ("click_droit.txt");
if ($debut == 2)
{
  $num_tut = GetdataField ($connect,"select util_cdn from utilisateur where util_nom_lb='$tuteur'","util_cdn");
  $sql = mysql_query("INSERT INTO tuteur (tut_apprenant_no,tut_tuteur_no) VALUES ($numero,$num_tut)");
    echo "<CENTER><FONT COLOR=blue><B>$mess_insc_comp ;</B></FONT></CENTER>";
  exit;
}
if($debut == 1)
{
  if (isset($_POST['email']) && $_POST['email'] != '')
  {
     $email = modif_az_qw($_POST['email']);
     list($compte, $domaine)=explode("@", $email);
     $log=$compte;
     $req = mysql_query("select * from utilisateur where util_login_lb = '$compte'");
     $nombre = mysql_num_rows($req);
     if ($nombre > 0)
     {
        $lien = "inscription.php?retour=1&grandnom=$grandnom&prenom=$prenom&telephone=$telephone&typ_user=$typ_user&email=$email&urlwebmail=$urlwebmail&commentaire=$commentaire";
        $lien=urlencode($lien);
        echo "<CENTER><FONT COLOR='white' size='3'><B>$mess_insc_titre_ind</B></FONT><P>";
        echo "<TABLE bgColor='#298CA0' cellspacing='2' width='100%' ><TR><TD>";
        echo "<TABLE bgColor='#FFFFFF' cellspacing='1' width='100%'><TR><TD>";
        echo "<P><CENTER><FONT  size='2'><B>$insc_email_idem</B></FONT><P></CENTER>";
        echo "<A href=\"trace.php?link=$lien\" target='main'>$mess_form_retour</A><BR>";
        echo "</TD></TR></TABLE></TD></TR></TABLE>";
        exit;
     }
  }
  else
     $log = formagri_genmotpass(8);
  //NOTA ---->  zone d'insertion des données dans la base
  $slash = "/";
  $passe = formagri_genmotpass(6);
  $password=$passe;
  if(isset($_FILES["myfile"]["tmp_name"]))
  {
     $ok = 1;
     $taille_file = filesize($_FILES["myfile"]["tmp_name"]);
     $longueur = strlen($_FILES["myfile"]["name"]);
     $extension = substr($_FILES["myfile"]["name"],$longueur-4,4);
  }
  else
     $ok = 0;
  if ($ok == 1 && $taille_file < 20000 && (strtolower($extension) == ".gif" || strtolower($extension) == ".png"  || strtolower($extension) == ".jpg" || strtolower($extension) == ".jpeg"))
  {
    $fichier_test = $_FILES["myfile"]["name"];
    $le_nom = modif_nom($fichier_test);
    $nom_final = "galerie/".$grandnom."_".$le_nom;
    $dir = $repertoire."/images/galerie";
    $handle=opendir($dir);
    $compare = $grandnom."_".$le_nom;
    while ($file = readdir($handle)){
        if ($file == $compare){
          $photo_exist = 1;
          break;
        }
    }
    closedir($handle) ;
    $dest_file=$repertoire."/images".$slash.$nom_final;
    $source_file=$_FILES["myfile"]["tmp_name"];
    if (!isset($photo_exist))
       $copier= move_uploaded_file($source_file , $dest_file);
    else
       $nom_final = '';
  }
  else
    $nom_final="";
  if ($type_user == "APPRENANT"){
     $grandnom = modif_nom($grandnom);
     $grandnom = str_replace("-","_",$grandnom);
  }
  $id_insc = Donne_ID ($connect,"select max(util_cdn) from utilisateur");
  $sql = mysql_query("INSERT INTO utilisateur (util_cdn,util_nom_lb,util_prenom_lb,util_photo_lb,util_email_lb,util_tel_lb,util_urlmail_lb,util_typutil_lb,util_login_lb,util_motpasse_lb,util_logincas_lb,util_blocageutilisateur_on,util_commentaire_cmt,util_auteur_no,util_date_dt) VALUES ($id_insc,\"$grandnom\",\"$prenom\",\"$nom_final\",\"$email\",\"$telephone\",\"$urlwebmail\",\"$type_user\",\"$log\",\"$password\",\"$login_cas\",\"NON\",\"$commentaire\",'$id_user',\"$date_op\")");
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
        if ($start == 'on' && $sendto != '')
          $envoi = mail_attachement($sendto , $subject , NewHtmlEntityDecode($msg,ENT_QUOTES) , $userfile , $reply, $nom, $from);
        $date_message = date("d/m/Y H:i:s" ,time());
        $message_base = "<B>$prenom $grandnom</B>, $mess_insc_mess1<BR> $mess_insc_mess2<B>$log</B><BR>$mess_insc_mess3 <B>$passe</B><BR>$mess_insc_mess6<B> $adresse_http</B><BR>$mess_insc_type <B>$type_user</B><BR>$mess_insc_mess4<BR>$mess_ag_cordial";
        $max_numero = Donne_ID ($connect,"select max(mess_cdn) from messagerie");
        $requete = mysql_query("INSERT INTO messagerie (mess_cdn,envoyeur,origine,contenu,date,sujet,id_user) VALUES ($max_numero,'$id_user','Inscription',\"$message_base\",'$date_message','$subject',$numero)");
        if ($start == 'on' && !$envoi){
          $mess_notif = "$mail_no_ok";
        }elseif ($start != 'on'){
          $mess_notif = "$prenom $grandnom $mess_insc_lms $mess_qualite : $type_user";
        }else{
          $mess_notif = "$mess_insc_mess_aff $prenom $grandnom $mess_insc_adr $email";
        }
       $lien = "inscription.php?mess_notif=$mess_notif";
        $lien = urlencode($lien);
        echo "<script language=\"JavaScript\">";
          echo "document.location.replace(\"trace.php?link=$lien\")";
        echo "</script>";
        exit;

}// Module principal d'inscription
if ($mess_notif != '')
   echo notifier($mess_notif);
echo "<FORM NAME='form1' action=\"inscription.php\" target='main' method='post' enctype='multipart/form-data'>";
echo "<INPUT TYPE='HIDDEN' NAME='debut' VALUE='1'>";
entete_simple($mess_insc_titre_ind);
   echo "<TR><TD><TABLE cellspacing='2' cellpadding='4'><TR height='5'><TD style='text-align:left;' nowrap colspan='3'>".aide_div('inscription',0,0,3,3)."</TD></TR>";
   echo "<TR><TD style='text-align:left;' nowrap><B>$mess_admin_nom *</B></TD>";
   echo "<TD style='text-align:left;' nowrap><INPUT TYPE=\"TEXT\" class='INPUT'  name=\"grandnom\" align=\"middle\" value=\"$grandnom\"></TD><TD style='text-align:left;' nowrap></TD></TR>";
   echo "<TR><TD style='text-align:left;' nowrap><B>$mess_admin_prenom *</B></TD>";
   echo "<TD style='text-align:left;' nowrap><INPUT TYPE='text' class='INPUT'  name='prenom' align='middle' value=\"$prenom\"></TD><TD style='text-align:left;' nowrap></TD></TR>";
   echo "<TR><TD style='text-align:left;' nowrap><B>$mess_admin_photo</B></TD><TD style='text-align:left;' nowrap>";
   echo "<INPUT TYPE='file' name='myfile' enctype='multipart/form-data'>";
   echo "</TD><TD style='text-align:left;' nowrap>$insc_pds_foto</TD></TR>";
   echo "<TR><TD style='text-align:left;' nowrap><B>$mess_login_cas</B></TD>";
   echo "<TD style='text-align:left;' nowrap><INPUT TYPE='text' class='INPUT'  name='login_cas' size='20' value=\"$login_cas\" align='middle'></TD>";
   echo "<TD style='text-align:left;'>$mess_insc_cas</TD></TR>";
   echo "<TR><TD style='text-align:left;' nowrap><B>$mess_admin_email</B></TD>";
   echo "<TD style='text-align:left;' nowrap><INPUT TYPE='text' class='INPUT'  name='email' id='email' size='40' value=\"$email\" align='middle'></TD>";
         ?>
         <script type="text/javascript">
                 var mail = new LiveValidation('email');
                 mail.add( Validate.Presence );
                 mail.add( Validate.Email );
                 mail.add( Validate.Length, { minimum: 8, maximum: 40 } );
         </script>
         <?php
   echo "<TD style='text-align:left;' nowrap>$mess_insc_note1</TD></TR>";
   echo "<TR><TD style='text-align:left;' nowrap><B>$mess_admin_tel</B></TD>";
   echo "<TD style='text-align:left;' nowrap><INPUT TYPE='text' class='INPUT'  name='telephone' align='middle' value='$telephone'></TD>";
   echo "<TD style='text-align:left;' nowrap></TD></TR>";
   echo "<TR><TD style='text-align:left;' nowrap><B>$mess_admin_webmail</B></TD>";
   echo "<TD style='text-align:left;' nowrap><INPUT TYPE='text' class='INPUT'  name='urlwebmail' align='middle' value=\"$urlwebmail\"></TD>";
   echo "<TD style='text-align:left;' nowrap>$mess_insc_note2</TD></TR>";
   echo "<TR><TD style='text-align:left;' nowrap><B>$profil *</B></TD>";
   echo "<TD style='text-align:left;' nowrap><SELECT name='type_user' class='SELECT' size='1'>";
   if ($typ_user == "ADMINISTRATEUR")
      echo "<OPTION value = \"ADMINISTRATEUR\">$mess_typ_adm</OPTION>";
   if ($typ_user == "RESPONSABLE_FORMATION" || $typ_user == "ADMINISTRATEUR")
   {
      echo "<OPTION value = \"APPRENANT\" selected>$mess_typ_app</OPTION>";
      echo "<OPTION value = \"RESPONSABLE_FORMATION\">$mess_typ_rf</OPTION>";
      echo "<OPTION value = \"FORMATEUR_REFERENT\">$mess_typ_fr</OPTION>";
      echo "<OPTION value = \"TUTEUR\">$mess_typ_tut</OPTION>";
   }
   echo "</SELECT>";
   echo "</TD><TD style='text-align:left;' nowrap></TD></TR>";
   echo "<TR><TD style='text-align:left;' nowrap></TD>";
   echo "<TD style='text-align:left;' nowrap colspan='2'><INPUT TYPE='checkbox'  name='start'>&nbsp;&nbsp;$mess_go_codes<BR>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$mess_avrt_codes</TD>";
   echo "</TR>";
   echo "<TR><TD></TD><TD style='text-align:left;' colspan = '2'>$mess_insc_symb</TD></TR>";
   echo "<TR height='35'><TD></TD><TD style='text-align:left;' align='left' colspan = '2' valign='center'>";
   echo "<A HREF=\"javascript:checkForm(document.form1);\" onmouseover=\"img1.src='images/fiche_identite/boutvalidb.gif';return true;\" onmouseout=\"img1.src='images/fiche_identite/boutvalid.gif'\">".
        "<IMG NAME=\"img1\" SRC=\"images/fiche_identite/boutvalid.gif\" BORDER='0' onLoad=\"tempImg=new Image(0,0); tempImg.src='images/fiche_identite/boutvalidb.gif'\"></A>";
   echo "</TD></TR></FORM></TABLE></TD></TR></TABLE></TD></TR></TABLE>";
echo '<div id="mien" class="cms"></div>';
echo "</body></html>";

function getextension($myfile)
{
  $bouts = explode(".", $myfile);
  return array(array_pop($bouts), implode(".", $bouts));
}
?>