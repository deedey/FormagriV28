<?php
if (!isset($_SESSION)) session_start();
include ("include/UrlParam2PhpVar.inc.php");
if (!isset($_SESSION['id_user']) || $_SESSION['id_user'] == "")
{
  exit();
}
require "admin.inc.php";
require "fonction.inc.php";
require "fonction_html.inc.php";
require "lang$lg.inc.php";
dbConnect();
include ("style.inc.php");
$date_messagerie = date("d/m/Y H:i:s" ,time());
if ($mail_sous_grp == 1)
{
   $send_to = "";
   $list_envoi = explode(",",$liste_envoi);
   $nb_envoi = count($list_envoi);
   $i=0;
   while ($i < $nb_envoi){
      $envoyer = explode("|",$list_envoi[$i]);
      $adresse = $envoyer[0];
      $num = $envoyer[1];
      if ($envoi[$num] == 'on' && $i < $nb_envoi-1)
         $send_to .= $adresse.",";
      elseif ($envoi[$num] == 'on' && $i == $nb_envoi-1)
         $send_to .= $adresse;
    $i++;
   }
   if (strrchr($send_to,","))
      $send_to = substr($send_to,0,-1);
   if ($an == 1)
      $vers = "$mess_envoi_mail_annu";
   else
      $vers = "$mess_mail_cert_app $mess_menu_gestion_grp $nom_grp";
   print("<SCRIPT language=javascript>");
     print("document.location.replace('mail.php?send_to=$send_to&sous_grp=1&message_mail=$vers','','width=680,height=520,resizable=yes,status=no')");
   print("</SCRIPT>");
}
if ($groupee == 1 && $envoyer == 1)
{
  $adr_mail = GetDataField ($connect, "select util_email_lb from utilisateur where util_cdn=$id_user","util_email_lb");
  if ($grp > 0)
     $liste = mysql_query("select * from utilisateur,utilisateur_groupe where utilisateur.util_cdn = utilisateur_groupe.utilgr_utilisateur_no AND utilisateur_groupe.utilgr_groupe_no=$grp");
  if ($pourqui == "utilisateurs")
     $liste=mysql_query("select * from utilisateur");
  elseif ($pourqui == "formateurs")
     $liste=mysql_query("select * from utilisateur where util_typutil_lb='FORMATEUR_REFERENT'");
  elseif ($pourqui == "resp_form")
     $liste=mysql_query("select * from utilisateur where util_typutil_lb='RESPONSABLE_FORMATION'");
  elseif ($pourqui == "tuteurs")
     $liste=mysql_query("select * from utilisateur where util_typutil_lb='TUTEUR'");
  if ($pourqui == "apprenants" && $typ_user == "ADMINISTRATEUR")
     $liste=mysql_query("select * from utilisateur where util_typutil_lb='APPRENANT'");
  elseif ($pourqui == "apprenants" && $typ_user == "RESPONSABLE_FORMATION")
     $liste=mysql_query("select * from utilisateur,prescription,groupe,utilisateur_groupe,tuteur where (utilisateur.util_cdn = tuteur.tut_apprenant_no AND tuteur.tut_tuteur_no = $id_user) OR (utilisateur.util_cdn = prescription.presc_utilisateur_no AND (prescription.presc_prescripteur_no = $id_user OR prescription.presc_formateur_no=$id_user)) OR (utilisateur.util_cdn = utilisateur_groupe.utilgr_utilisateur_no and groupe.grp_resp_no = $id_user AND utilisateur_groupe.utilgr_groupe_no = groupe.grp_cdn) GROUP BY utilisateur.util_cdn");
  elseif ($pourqui == "apprenants" && $typ_user == "FORMATEUR_REFERENT")
     $liste=mysql_query("select * from utilisateur,prescription,tuteur where (utilisateur.util_cdn = tuteur.tut_apprenant_no AND tuteur.tut_tuteur_no = $id_user) OR (utilisateur.util_cdn = prescription.presc_utilisateur_no and prescription.presc_formateur_no=$id_user) GROUP BY utilisateur.util_cdn");
  elseif ($pourqui == "apprenants" && $typ_user == "TUTEUR")
     $liste=mysql_query("select * from utilisateur,tuteur where utilisateur.util_cdn = tuteur.tut_apprenant_no and tuteur.tut_tuteur_no=$id_user");
  $nbr = mysql_num_rows($liste);
  if ($nbr>0)
  {
    $i = 0;
    while ($i < $nbr)
    {
        $num = mysql_result($liste,$i,"util_cdn");
        $nom_util = mysql_result($liste,$i,"util_nom_lb");
        $prenom_util = mysql_result($liste,$i,"util_prenom_lb");
        $email = mysql_result($liste,$i,"util_email_lb");
        $logue = mysql_result($liste,$i,"util_login_lb");
        $passe = mysql_result($liste,$i,"util_motpasse_lb");
        $tel = mysql_result($liste,$i,"util_tel_lb");
        $webmail = mysql_result($liste,$i,"util_urlmail_lb");
        $type = mysql_result($liste,$i,"util_typutil_lb");
        $send_to = $email;
        $subject = StripSlashes($sujet);
        $msg = StripSlashes($message);
        $origine=$nom_user."  ".$typ_user;
        if (isset($_FILES["userfile"]["tmp_name"]))
        {
            list($extension,$nom) = getextension($_FILES["userfile"]["name"]);
            if (in_array(strtolower($extension), array("exe","sh","py", "ida","idq","asp","cer","cdx","asa","idc","cfm","dbm","php","php4","inc","shtml","cgi")))
            {
                $nom = '';
                $userfile = 'none';
            }
            else
            {
                $nom = $_FILES["userfile"]["name"];
                $userfile = $_FILES["userfile"]["tmp_name"];
            }
        }
        else
        {
          $nom = "";
          $userfile = "none";
        }
        $log_num = $logue;
        if ($externe == 'on')
        {
          $reply = $adr_mail;
          $from = $adr_mail;
          $sendto = $email;
          $subject = StripSlashes($sujet);
          $msg = StripSlashes($message);
          $origine=$nom_user."  ".$typ_user;
          if ($email != "")
            $envoi=mail_attachement($sendto,$subject,str_replace("<br />","\n",str_replace("<br />","\n",NewHtmlEntityDecode($msg,ENT_QUOTES))),$userfile,$reply,$nom,$from);
        }
        // procédure de telechargement dans ressources/messagerie du receveur
        if (isset($nom) && $nom != "")
        {
           $dir_num="ressources/".$log_num."_".$num."/ressources";
           $handle=opendir($dir_num);
           $drap=0;
           while ($file = readdir($handle))
           {
                 if ($file == "messagerie")
                 {
                    chmod ($dir_num."/messagerie",0777);
                    $drap=1;
                    break;
                 }
           }
           closedir($handle) ;
           if ($drap == 0)
           {
                 mkdir ($dir_num."/messagerie",0777);
                 chmod ($dir_num."/messagerie",0777);
           }

           $dir_num="ressources/".$log_num."_".$num."/ressources/messagerie";
           $nom_final = modif_nom($_FILES["userfile"]["name"]);
           $dest_file = $dir_num."/".$nom_final;
           if ($i == 0)
           {
              $copier = copy($_FILES["userfile"]["tmp_name"],$dest_file);
              $fichier_a_copier = $dest_file;
           }
           else
              $copier = copy($fichier_a_copier,$dest_file);
        }
        //Fin de boucle  de téléchargement du fichier dans ressources messagerie
        $origine=$nom_user."  ".$typ_user;
        $id_max = Donne_ID ($connect,"select max(mess_cdn) from messagerie");
        $requete = mysql_query("INSERT INTO messagerie (mess_cdn,envoyeur,origine,contenu,date,sujet,mess_fichier_lb,id_user) VALUES ($id_max,\"$id_user\",\"".addslashes($subject)."\",\"".addslashes($msg)."\",\"$date_messagerie\",\"$origine\",\"$dest_file\",$num)");
        $drap = 0;
      $i++;
    }
    if ($cc != "")
    {
        $reply = $adr_mail;
        $from = $adr_mail;
        $sendto = $cc;
        $subject = StripSlashes($sujet);
        $msg = StripSlashes($message);
        $origine=$nom_user."  ".$typ_user;
        if (isset($_FILES["userfile"]["tmp_name"]))
        {
            list($extension,$nom) = getextension($_FILES["userfile"]["name"]);
            if (in_array(strtolower($extension), array("exe","sh","py", "ida","idq","asp","cer","cdx","asa","idc","cfm","dbm","php","php4","inc","shtml","cgi")))
            {
                $nom = '';
                $userfile = 'none';
            }
            else
            {
                $nom = $_FILES["userfile"]["name"];
                $userfile = $_FILES["userfile"]["tmp_name"];
            }
        }
        else
        {
          $nom = "";
          $userfile = "none";
        }
        $envoi = mail_attachement($sendto,$subject,str_replace("<br />","\n",str_replace("<br />","\n",NewHtmlEntityDecode($msg,ENT_QUOTES))),$userfile,$reply,$nom,$from);
        if (!$envoi)
          $msgenvoi = $mail_no_ok;
        else
          $msgenvoi = $mess_mail_env_ok;
    }
    else
       $msgenvoi = $mess_mail_env_ok;

    echo "<script language=\"JavaScript\">";
    echo "setTimeout(\"Quit()\",1500);
        function Quit() {
          self.opener=null;self.close();return false;
        }
        </SCRIPT>";
  }
  else
  {
    echo "<CENTER><BR><FONT color='white' size=3><B>$mess_mail_noutil</B></FONT></CENTER>";
       echo "<script language=\"JavaScript\">";
       echo "setTimeout(\"Quit()\",1500);
        function Quit() {
          self.opener=null;self.close();return false;
        }
        </SCRIPT>";
    exit;
  }
}
?>
<SCRIPT language=JavaScript>
function checkForm(frm) {
  var ErrMsg = "<?php echo $mess_info_no;?>\n";
  var lenInit = ErrMsg.length;
  if (isEmpty(frm.someone)==true)
    ErrMsg += ' - <?php echo addslashes($mess_destNull);?>\n';
  if (isEmpty(frm.sujet)==true)
    ErrMsg += ' - <?php echo $mess_mail_sujet;?>\n';
  if (isEmpty(frm.message)==true)
    ErrMsg += ' - <?php echo $mess_votre_mess;?>\n';
<?php  if ($contact != 1 && $dou != "forum" && $sequence != 1){?>
  if (isEmpty(frm.cc)==false){
     if (isEmail(frm.cc)==false)
        ErrMsg += ' - <?php echo $mess_email_dest;?>\n';
  }
<?php }
/*
*/
?>
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
if ($groupee == 1 && !$envoyer)
{
  $adr_mail = GetDataField ($connect, "select util_email_lb from utilisateur where util_cdn=$id_user","util_email_lb");
  $adr_webmail = GetDataField ($connect, "select util_urlmail_lb from utilisateur where util_cdn=$id_user","util_urlmail_lb");
  if ($pourqui == "utilisateurs")
     $vers = $mess_mail_tous;
  if ($pourqui == "formateurs")
     $vers = $mess_mail_fr;
  if ($pourqui == "resp_form")
     $vers = $mess_mail_rf;
  if ($pourqui == "tuteurs")
     $vers = $mess_mail_tut;
  if ($pourqui == "apprenants")
     $vers = $mess_mail_app;
  if ($grp > 0)
  {
    $nom_grp =GetDataField ($connect,"select grp_nom_lb from groupe where grp_cdn = $grp","grp_nom_lb");
    $vers = "$mess_mail_app $mess_menu_gestion_grp $nom_grp";
  }
  $titre = $mess_bas_mail;
  $soustitre = "$mess_mail_avert $vers";
  entete_simple($titre);
  echo "<TR><TD><TABLE cellpadding='3'cellspacing='0' bgColor='#FFFFFF' border='0' width='100%'>";
  echo "<TR><TD></TD><TD align=left><div class='sous_titre'>$soustitre </div></TD></TR>";
  echo"<FORM  name= 'form1' action=\"mail.php?groupee=1&envoyer=1&pourqui=$pourqui&grp=$grp&complement=$complement\" method='POST' enctype='multipart/form-data'>";
  echo" <INPUT TYPE='HIDDEN' name='someone' value='1'>";
  ?>
   <TR>
      <TD nowrap align='right'>
         <B><?php  echo $mess_mail_cc ;?></B>
      </TD>
      <TD nowrap align='left'>
         <INPUT TYPE="TEXT" class='INPUT' name="cc" ' id='email' align="middle" size="40">
         <script type="text/javascript">
                 var mail = new LiveValidation('email');
                 mail.add( Validate.Presence );
                 mail.add( Validate.Email );
                 mail.add( Validate.Length, { minimum: 8, maximum: 40 } );
         </script>
      </TD>
   </TR>
   <TR>
      <TD nowrap align='right'>
         <B><?php  echo $mess_mail_sujet ;?></B>
     </TD>
      <TD nowrap>
         <INPUT TYPE="TEXT" class='INPUT' name="sujet" align="middle" size="75">
      </TD>
   </TR>
   <TR>
      <TD nowrap align='right'>
         <B><?php  echo $mess_fic_att ;?></B>
      </TD>
      <TD nowrap>
      <?php
          echo "<INPUT TYPE='file' name='userfile' enctype='multipart/form-data'>";
      echo "<INPUT type='hidden' name='MAX_FILE_SIZE' value='2000000'>";
      ?>
      </TD>
   </TR>
   <TR>
      <TD nowrap align='right'>
         <B><?php  echo $mess_mail_mess ;?></B>
      </TD>
      <TD nowrap>
         <TEXTAREA class='TEXTAREA' NAME="message" COLS=90 ROWS=15></TEXTAREA>
      </TD>
   </TR>
 <?php
   if (isset($_SESSION['IsOff']) && $_SESSION['IsOff'] == 0)
        echo "<TR><TD align='right'><INPUT type='checkbox' name='externe'></TD><TD>$mess_envoi_mail_ext</TD></TR>";
   echo "<TR><td></td><TD colspan=2><A HREF=\"javascript:checkForm(document.form1);\" onClick=\"TinyMCE.prototype.triggerSave();\" onmouseover=\"img1.src='images/fiche_identite/boutvalidb.gif';return true;\" onmouseout=\"img1.src='images/fiche_identite/boutvalid.gif'\">".
        "<IMG NAME=\"img1\" SRC=\"images/fiche_identite/boutvalid.gif\" BORDER='0' onLoad=\"tempImg=new Image(0,0); tempImg.src='images/fiche_identite/boutvalidb.gif'\"></A>";
   echo "</FORM>";
   echo "</TD></TR></TABLE></TD></TR></TABLE></BODY></HTML>";
   exit();
}
if ($prem == 1 || $contact == 1 || $contacter == 1 || $au_groupe == 1 || $sous_grp == 1)
{
//    if ($dou!= "forum")
      $titre = $mess_bas_mail;
    if ($msgenvoi != "")
      $soustitre .= stripslashes($msgenvoi);
    if ($message_mail != "")
      $soustitre .= $message_mail;
    if ($sous_grp == 1)
      $soustitre = $mess_envoi_mail_annu;
    entete_simple($titre);
    echo "<TR><TD width=100%><TABLE cellpadding='3' width=100%>";
    if (((!empty($prem) && $prem == 1) || (!empty($contacter) && $contacter == 1)) && empty($num) && empty($formation))
    {
       if (($complement != 1 && $ret !=1 && $typ_user == "APPRENANT") || $typ_user != "APPRENANT")
          $requeste = "select distinct utilisateur.util_cdn from utilisateur where utilisateur.util_cdn != $id_user ";
       if ($typ_user == "ADMINISTRATEUR")
          $lien_annuaire="annuaire.php?messagerie=1";
       else
          $lien_annuaire="annuaire.php";
       echo "<TR><TD colspan='2' align=left valign='bottom' nowrap style=\"float:left;padding-right:8px;\"><A HREF=\"$lien_annuaire\" ".
            "class='bouton_new'>$mess_ad_annu\n</A></TD></TR>";
            //"class='bouton_new'>$mess_ad_annu\n</A>".aide_div("mail",8,0,0,0)."</TD></TR>";
    }
//    else
//       echo "<TR><TD align=left valign='bottom' nowrap>".aide_div("mail",2,0,0,0)."</TD></TR>";
    if ($soustitre != "")
      echo "<tr><td></td><TD align='left'><div class='sous_titre'>$soustitre </div></td></tr>";
    if ($prem == 1 || $contacter == 1 || $au_groupe == 1 || $sous_grp == 1)
    { //Par defaut, on met l'adr de l'utilisateur ds le champ "de"
       $adr_mail = GetDataField ($connect, "select util_email_lb from utilisateur where util_cdn=$id_user","util_email_lb");
       $adr_webmail = GetDataField ($connect, "select util_urlmail_lb from utilisateur where util_cdn=$id_user","util_urlmail_lb");
    }
    echo "<FORM  name='form1' action=\"mail.php?go=1&num=$num&connu=1&adr_mail=$adr_mail&contact=$contact&prem=$prem&complement=$complement\" method='POST' enctype='multipart/form-data'>";
    echo "<INPUT TYPE='HIDDEN'  name='de' value=\"$adr_mail\">";
    if ($contacter == 1)
    {
      if ($dou != "forum")
      {
        $email_dest = GetDataField ($connect, "select util_email_lb from utilisateur where util_cdn= '$num'","util_email_lb");
        $nom = GetDataField ($connect,"select util_nom_lb from utilisateur where util_cdn='$num'","util_nom_lb");
        $prenom = GetDataField ($connect,"select util_prenom_lb from utilisateur where util_cdn='$num'","util_prenom_lb");
        $majuscule =$prenom." ".$nom;
        echo "<TR><TD></TD><TD align='left'><div class='sous_titre'>$mess_mail_avert <B>$majuscule</B></div></TD></TR>";
      }
      else
      {
         if (strstr($a_qui,"@"))
           $num =  GetDataField ($connect, "select util_cdn from utilisateur where util_email_lb = '$a_qui'","util_cdn");
      }
      echo" <INPUT TYPE='HIDDEN'  name='num' value='$num'>";
      echo" <INPUT TYPE='HIDDEN' name='someone' value='$num'>";
      echo" <INPUT TYPE='HIDDEN'  name='contacter' value=$contacter>";
    }
    elseif ($contact == 1)
    {
      $administrateur1 = GetDataField ($connect,"select util_cdn from utilisateur where util_typutil_lb='ADMINISTRATEUR'","util_cdn");
      echo" <INPUT TYPE='HIDDEN'  name='num' value=\"$administrateur1\">";
      echo" <INPUT TYPE='HIDDEN' name='someone' value=\"$administrateur1\">";
      echo" <INPUT TYPE='HIDDEN'  name='lelogin' value=\"$lelogin\">";
    }
    elseif ($au_groupe == 1 || $sous_grp == 1)
    {
      echo" <INPUT TYPE='HIDDEN' name='send_to' value=\"$send_to\">";
      echo" <INPUT TYPE='HIDDEN' name='someone' value=\"$send_to\">";
      echo" <INPUT TYPE='HIDDEN' name='au_groupe' value='1'>";
    }
    elseif($prem == 1){
      echo "<INPUT TYPE='HIDDEN' name='someone' value=''>";
    }
    if (($contact != 1 && $contacter == 1 && $sujet == "")  || $prem == 1 || $sous_grp == 1)
    {
      echo "<TR><TD nowrap align='right'><B>$mess_mail_cc</B></TD>";
      echo "<TD nowrap><INPUT TYPE='text' class='INPUT'  name='cc' id='email' align='middle' size='40'></TD></TR>";
         ?>
         <script type="text/javascript">
                 var mail = new LiveValidation('email');
                 mail.add( Validate.Presence );
                 mail.add( Validate.Email );
                 mail.add( Validate.Length, { minimum: 8, maximum: 40 } );
         </script>
         <?php
      echo "<INPUT TYPE='hidden' name='sequence'>";
    }
    else
    {
      echo "<INPUT TYPE='HIDDEN'  name='cc' value=''>";
    }
    echo "<TR><TD nowrap align='right'><B>$mess_mail_sujet</B></TD>";
    echo "<TD nowrap><INPUT TYPE='text' class='INPUT'  name='sujet' align='middle' value=\"".stripslashes($sujet)."\" size='75'></TD></TR>";
    if (($contact != 1 && $contacter== 1 && $sujet == "") || $prem == 1 || $sous_grp == 1){
        echo "<TR><TD nowrap align='right'><B>$mess_fic_att</B></TD><TD>";
        echo "<INPUT TYPE='file' name='userfile' enctype='multipart/form-data'>";
        echo "<INPUT type='hidden' name='MAX_FILE_SIZE' value='2000000'>";
        echo "</TD></TR>";
    }
    echo "<TR><TD nowrap align='right'><B>$mess_mail_mess</B></TD>";
    if ($contact == 1)
       echo "<TD nowrap> <TEXTAREA class='TEXTAREA' NAME=\"message\" COLS=88 ROWS=15>$mess_contact_adm</TEXTAREA></TD></TR>";
    else
       echo "<TD nowrap> <TEXTAREA class='TEXTAREA' NAME=\"message\" COLS=88 ROWS=15></TEXTAREA></TD></TR>";
    if ($contact != 1){
       if ((($contacter == 1 && ($email_dest !='' || $a_qui != '')) || $sous_grp == 1) && isset($_SESSION['IsOff']) && $_SESSION['IsOff'] == 0)
          echo "<TR><TD align='right'><INPUT type='checkbox' name='externe'></TD><TD>$mess_envoi_mail_ext</TD></TR>";
    }
    echo "<TR><TD></TD><TD colspan=2 align=left>";
    echo "$bouton_gauche<A HREF=\"javascript:checkForm(document.form1);\" onClick=\"TinyMCE.prototype.triggerSave();\">$mess_gen_envoi</A>$bouton_droite";
    echo "</TD></TR></FORM>";
}
if ($go == 1)
{
        if ($contact != 1)
        {
          $nom_user = GetDataField ($connect, "select util_nom_lb from utilisateur where util_cdn=$id_user","util_nom_lb");
          $envoyeur_email = GetDataField ($connect, "select util_email_lb from utilisateur where util_cdn= '$id_user'","util_email_lb");
          $adr_mail = GetDataField ($connect, "select util_email_lb from utilisateur where util_cdn= '$num'","util_email_lb");
          if ($au_groupe != 1)
            $send_to = $adr_mail;
        }
        if ($contact == 1)
        {
          $message = $message."\n".$adresse_http;
          if ($lelogin != "")
            $message .="\n Login saisi par l'utilisateur : $lelogin";
        }
        $subject .= StripSlashes($sujet);
        $msg = StripSlashes("$message");
        $origine=$nom_user."  ".$typ_user;
        $email=$adr_mail;
        if (isset($_FILES["userfile"]["tmp_name"]))
        {
           $nom = $_FILES["userfile"]["name"];
           $userfile = $_FILES["userfile"]["tmp_name"];
        }
        else
        {
          $nom = "";
          $userfile = "none";
        }
        $msg = "$message";
        $typemime="multipart/mixed";
//        verifie_email($email);
        if ($cc != "" || $contact == 1)
        {
          $from = $envoyeur_email;
          $reply = $envoyeur_email;
          if ($cc != "")
            $sendto = $cc;
          else
            $sendto = $send_to;
          $subject = StripSlashes($sujet);
          $msg = StripSlashes($message);
          if ($sendto != "" && isset($_SESSION['IsOff']) && $_SESSION['IsOff'] == 0)
            $envoi=mail_attachement($sendto,$subject,str_replace("<br />","\n",str_replace("<br />","\n",NewHtmlEntityDecode($msg,ENT_QUOTES))),$userfile,$reply,$nom,$from);
        }
        $msgenvoi = $mess_mail_env_ok;
        if ($send_to != "")
        {
          if (strstr($send_to,","))
          {
            if(strstr($send_to,"@"))
              $decompte = "emails";
            else
              $decompte = "numeros";
            $nombre = explode(",",$send_to);
            $nbr = count($nombre);
            if ($nombre[$nbr-1] =='')
               $nbr--;
            $i = 0;
            while ($i < $nbr)
            {
              if ($decompte == "emails")
              {
                $adres = $nombre[$i];
                $num = GetDataField ($connect,"select util_cdn from utilisateur where util_email_lb='$adres'","util_cdn");
                $log_num = GetDataField ($connect,"select util_login_lb from utilisateur where util_cdn='$num'","util_login_lb");
              }
              elseif ($decompte == "numeros")
              {
                $num = $nombre[$i];
                $adres = GetDataField ($connect,"select util_email_lb from utilisateur where util_cdn='$num'","util_email_lb");
                $log_num = GetDataField ($connect,"select util_login_lb from utilisateur where util_cdn='$num'","util_login_lb");
              }
              // procédure de telechargement dans ressources/messagerie du receveur
              if ($externe == 'on')
              {
                $reply = $envoyeur_email;
                $from = $envoyeur_email;
                $sendto = $adres;
                $origine=$nom_user."  ".$typ_user;
                if ($sendto != "" && isset($_SESSION['IsOff']) && $_SESSION['IsOff'] == 0)
                    $envoi=mail_attachement($sendto,$subject,str_replace("<br />","\n",str_replace("<br />","\n",NewHtmlEntityDecode($msg,ENT_QUOTES))),$userfile,$reply,$nom,$from);
              }
              if ($nom != "")
              {
                $dir_num="ressources/".$log_num."_".$num."/ressources";
                $handle=opendir($dir_num);
                $drap=0;
                while ($file = readdir($handle))
                {
                   if ($file == "messagerie")
                   {
                      chmod ($dir_num."/messagerie",0777);
                      $drap=1;
                      break;
                   }
                }
                closedir($handle) ;
                if ($drap == 0)
                {
                      mkdir ($dir_num."/messagerie");
                      chmod ($dir_num."/messagerie",0777);
                }
                $dir_num="ressources/".$log_num."_".$num."/ressources/messagerie";
                $nom_final = modif_nom($_FILES["userfile"]["name"]);
                  $dest_file = $dir_num."/".$nom_final;
                if ($i == 0)
                {
                  $copier = copy($_FILES["userfile"]["tmp_name"],$dest_file);
                  $fichier_a_copier = $dest_file;
                }
                else
                  $copier = copy($fichier_a_copier,$dest_file);
              }
             //Fin de boucle  de téléchargement du fichier dans ressources messagerie
              $id_max = Donne_ID ($connect,"select max(mess_cdn) from messagerie");
              $requete = mysql_query("INSERT INTO messagerie (mess_cdn,envoyeur,origine,contenu,date,sujet,mess_fichier_lb,id_user) VALUES ($id_max,\"$id_user\",\"".addslashes($subject)."\",\"".addslashes($msg)."\",\"$date_messagerie\",\"$origine\",\"$dest_file\",$num)");
              $drap = 0;
            $i++;
            }
          }
          else
          {
              if ($externe == 'on')
              {
                $reply = $envoyeur_email;
                $from = $envoyeur_email;
                $sendto = $send_to;
                $origine=$nom_user."  ".$typ_user;
                if ($sendto != "" && isset($_SESSION['IsOff']) && $_SESSION['IsOff'] == 0)
                  $envoi=mail_attachement($sendto,$subject,str_replace("<br />","\n",str_replace("<br />","\n",NewHtmlEntityDecode($msg,ENT_QUOTES))),$userfile,$reply,$nom,$from);
              }
              $log_num = GetDataField ($connect,"select util_login_lb from utilisateur where util_cdn='$num'","util_login_lb");
              // procédure de telechargement dans ressources/messagerie du receveur
              if ($nom != "")
              {
                $dir_num="ressources/".$log_num."_".$num."/ressources";
                $handle=opendir($dir_num);
                $drap=0;
                while ($file = readdir($handle))
                {
                   if ($file == "messagerie")
                   {
                     chmod ($dir_num."/messagerie",0777);
                      $drap=1;
                      break;
                   }
                }
                closedir($handle) ;
                if ($drap == 0)
                {
                   mkdir ($dir_num."/messagerie",0777);
                   chmod ($dir_num."/messagerie",0777);
                }
                $dir_num="ressources/".$log_num."_".$num."/ressources/messagerie";
                $nom_final = modif_nom($_FILES["userfile"]["name"]);
                $dest_file = $dir_num."/".$nom_final;
                $copier = move_uploaded_file($_FILES["userfile"]["tmp_name"],$dest_file);
              }
             //Fin de boucle  de téléchargement du fichier dans ressources messagerie
             $id_max = Donne_ID ($connect,"select max(mess_cdn) from messagerie");
             $requete = mysql_query("INSERT INTO messagerie (mess_cdn,envoyeur,origine,contenu,date,sujet,mess_fichier_lb,id_user) VALUES ($id_max,\"$id_user\",\"".addslashes($subject)."\",\"".addslashes($msg)."\",\"$date_messagerie\",\"$origine\",\"$dest_file\",$num)");
          }
        }
        if ($send_to == '')
        {
              $log_num = GetDataField ($connect,"select util_login_lb from utilisateur where util_cdn='$num'","util_login_lb");
              // procédure de telechargement dans ressources/messagerie du receveur
              if ($nom != "")
              {
                $dir_num="ressources/".$log_num."_".$num."/ressources";
                $handle=opendir($dir_num);
                $drap=0;
                while ($file = readdir($handle)){
                   if ($file == "messagerie") {
                     chmod ($dir_num."/messagerie",0777);
                      $drap=1;
                      break;
                   }
                }
                closedir($handle) ;
                if ($drap == 0)
                {
                   mkdir ($dir_num."/messagerie",0777);
                   chmod ($dir_num."/messagerie",0777);
                }
                  $dir_num="ressources/".$log_num."_".$num."/ressources/messagerie";
                $nom_final = modif_nom($_FILES["userfile"]["name"]);
                $dest_file = $dir_num."/".$nom_final;
                $copier = move_uploaded_file($_FILES["userfile"]["tmp_name"],$dest_file);
              }
           $id_max = Donne_ID ($connect,"select max(mess_cdn) from messagerie");
           $requete = mysql_query("INSERT INTO messagerie (mess_cdn,envoyeur,origine,contenu,date,sujet,mess_fichier_lb,id_user) VALUES ($id_max,\"$id_user\",\"".addslashes($subject)."\",\"".addslashes($msg)."\",\"$date_messagerie\",\"$origine\",\"$dest_file\",$num)");
        }

       if ($contact == 1)
       {
         echo "<script language=\"JavaScript\">";
         echo "document.location.replace(\"index.php\")";
         echo "</script>";
         exit;
       }
//       $lien = "mail.php?prem=1&msgenvoi=$msgenvoi&complement=$complement";
//       $lien = urlencode($lien);
       echo "<script language=\"JavaScript\">";
       echo "setTimeout(\"Quit()\",1500);
        function Quit() {
          self.opener=null;self.close();return false;
        }
        </SCRIPT>";

//       echo "document.location.replace(\"trace.php?link=$lien\")";
}
echo "</TABLE></TD></TR></TABLE></BODY></HTML>";
//include ("click_droit.txt");
function getextension($myfile)
{
  $bouts = explode(".", $myfile);
  return array(array_pop($bouts), implode(".", $bouts));
}
?>
</body>
</html>
