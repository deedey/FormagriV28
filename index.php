<?php
// 23/11/06  modification et insertion de l'accï¿½s CAS
if (!isset($_SESSION))
    session_start();
// authentification CAS
if (!isset($_SESSION['bAuthMode']) && file_exists("admin.inc.php"))
   include_once('auth_cas.php');
if (isset($_SESSION['bAuthMode']) && (!isset($index) || $index == 0))
{
  if (isset($langage) && $langage == 1)
  {
     $lg=$langue;
     $langage = 0;
  }
  elseif(!isset($langage) && !isset($lg))
    $lg = "fr";
  if ($lg == "ru")
  {
    $code_langage = "ru";
    $charset = "Windows-1251";
    $suffixer = "_ru";
  }
  elseif ($lg == "fr")
  {
    $code_langage = "fr";
    $charset = "iso-8859-1";
    $suffixer = "";
  }
  elseif ($lg == "en")
  {
    $code_langage = "en";
    $charset = "iso-8859-1";
    $suffixer = "";
  }
  $_SESSION['lg'] = $lg;
  $_SESSION['charset'] = $charset;
  require ("admin.inc.php");
  require "lang$lg.inc.php";
  mysql_select_db($bdd,mysql_connect($adresse,$log,$mdp));
  if (!isset($logincas) && isset($login_ldap))
     $login = mysql_result(mysql_query ("select util_login_lb from utilisateur where ldap_user_id='$login_ldap'"),0,"util_login_lb");
  else
     $login = mysql_result(mysql_query ("select util_login_lb from utilisateur where util_logincas_lb='$logincas'"),0,"util_login_lb");
  $_SESSION['login'] = $login;
  $req_id = mysql_query ("select * from users where util_login_lb='$login'");
  if (mysql_num_rows($req_id) > 0)
  {
        $user_id = mysql_result($req_id,0,"util_cdn");
        $email_user = mysql_result($req_id,0,"util_email_lb");
        $type_util = mysql_result($req_id,0,"util_typutil_lb");
        $nom_user = mysql_result($req_id,0,"util_nom_lb");
        $prenom_user = mysql_result($req_id,0,"util_prenom_lb");
     $index = 1;
  }
  else
  {
     ?>
     <html>
     <HEAD>
     <script language="Javascript"><!--
        function envoi_form()
        {
           document.formulaire.submit(); // envoi du formulaire
        }
     // -->
     </script>
     </HEAD>
     <BODY>
     <?php
         //formulaire caché
         echo "<form name=\"formulaire\" action=\"verif2.php?premulti=0\" method=\"POST\">";
         echo "<input type='hidden' name='login' value='$login'>";
         echo "<input type='hidden' name='password' value='$password'>";
         echo "<input type='hidden' name='lg' value='$lg'>";
         echo "<input type='hidden' name='suffixer' value='$suffixer'>";
         echo "</form>";
         ?>
           <script language="Javascript">
              envoi_form();
           </script>
           </BODY></HTML>
         <?php
      exit();
  }
}
elseif(isset($authentifie) && $authentifie == 'non')
{
      $_SESSION['authentifie'] = 'oui';
      echo "<script language='JavaScript'>";
          echo "document.location.replace(\"index.php?login=$login\");";
      echo "</script>";
    exit;
}
//if (!isset($index) || $index == 0)
//{
  if (isset($langage) && $langage == 1)
  {
    $lg=$langue;
    $langage = 0;
  }
  elseif(!isset($langage) && !isset($lg))
  {
    $lg = "fr";
    $_SESSION['charset'] = "iso-8859-1";
  }
//}
if (isset($lg))
{
  if ($lg == "ru")
  {
      $code_langage = "ru";
      $charset = "Windows-1251";
      $suffixer = "_ru";
  }
  elseif ($lg == "fr")
  {
      $code_langage = "fr";
      $charset = "iso-8859-1";
      $suffixer = "";
  }
  elseif ($lg == "en")
  {
      $code_langage = "en";
      $charset = "iso-8859-1";
      $suffixer = "";
  }
  $_SESSION['lg'] = $lg;
  $_SESSION['charset'] = $charset;
}
if (isset($fermeture) && $fermeture == 1)
{
  ?>
    <HTML><HEAD>
       <SCRIPT language=javascript>
       setTimeout("Quit()",1500);
        function Quit()
        {
          self.opener=null;self.close();return false;
        }
        </SCRIPT>
    </HEAD><BODY>
 <?php
 exit();
}
if (isset($index) && $index == 2)
{
   $index = 1;
  ?>
  <html>
  <HEAD>
    <script language="Javascript"><!--
        function envoi_form()
        {
           document.formulaire.submit(); // envoi du formulaire
        }
    // -->
    </script>
  </HEAD>
  <BODY>

  <?php  //formulaire cachï¿½
  echo "<form name=\"formulaire\" action=\"http://$lms/verif2.php?premulti=1\" method=\"POST\">";
  echo "<input type='hidden' name='login' value='$login'>";
  echo "<input type='hidden' name='password' value='$password'>";
  echo "<input type='hidden' name='lg' value='$lg'>";
  echo "<input type='hidden' name='suffixer' value='$suffixer'>";
  echo "</form>";
  ?>
  <script language="Javascript">
      envoi_form();
  </script>
  </BODY>
  </HTML>
  <?php
   exit();
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN">
<head>
<META HTTP-EQUIV="Content-Type" content="text/html; charset=<?php  echo $charset;?>">
<meta name="Language" content="french">
<META HTTP-EQUIV="Content-Language" CONTENT="<?php  echo $code_langage ;?>">
<META HTTP-EQUIV="Expires" CONTENT="Fri, Jan 01 1900 00:00:00 GMT">
<META NAME="ROBOTS" CONTENT="No Follow">
<META HTTP-EQUIV="Cache-Control" CONTENT="no-cache, must-revalidate">
<META HTTP-EQUIV="Pragma" CONTENT="no-cache">
<title>***Formagri***</title>
<link rel="shortcut icon" type="image/x-icon" href="favicon.ico" />
<script type="text/javascript" src="OutilsJs/jquery-182-min.js"></script>
<script language='JavaScript'>
<!-- <![CDATA[
function setEvents()
{
        var objChild;                           // Window
        var reWork = new RegExp('object','gi');        // Regular expression
        try
        {
                objChild = window.open('','child','width=2,height=2,status=no, directories=no,copyhistory=0,titlebar=no, toolbar=no, location=no, menubar=no, scrollbars=no, resizable=no');
                objChild.close();
        }
        catch(e) { }
        if(!reWork.test(String(objChild)))
           alert('Attention : Les fenêtres PopUp sont bloquées sur votre machine.\n\nLes barres Google, Yahoo, Aol ou d\'autres encore bloquent les fenêtres PoPup par défaut. \nMozilla Firefox, IE, Chrome ainsi que Safari les bloquent aussi par défaut.\n\nIl faut donc les débloquer afin de pouvoir utiliser votre plate-forme Formagri dans les meilleures conditions.\n\nN\'oubliez pas de cliquer sur l\'icone \"Votre configuration\" pour voir si vous disposez de tous les outils nécessaires à un bon fonctionnement.');
}
//    ]]> -->
</script>
<script language="Javascript">
<!--
function envoi_form()
{
  document.formulaire.submit(); // envoi du formulaire
// -->
}
</script>

<STYLE>
body,p,ul,li,td,th {font-size:14px;font-family : Arial;color : #FFFFFF;}
A         {font-family:arial;font-size:12px;color:#24677A;text-decoration:none}
A:link    {font-family:arial;font-size:14px;color:#669FAA;font-weight:bold}
A:visited {font-family:arial;font-size:14px;color:#669FAA;font-weight:bold}
A:hover   {font-family:arial;font-size:14px;color:#D45211;font-weight:bold}
A.off     {font-family:arial;font-size:14px;color:#669FAA;font-weight:bold}

#titre A:link{background-repeat:no-repeat;background-position:1% 50%;color:#3BACC4;}
#titre A:visited{background-repeat:no-repeat;background-position:1% 50%;color:#3BACC4;}
#titre A:hover {background-repeat:no-repeat;background-position:1% 50%;color:#D45211;}

#menu A:link{background-repeat:no-repeat;background-position:1% 50%;color:#002D44;}
#menu A:visited{background-repeat:no-repeat;background-position:1% 50%;color:#002D44;}
#menu A:hover {background-repeat:no-repeat;background-position:1% 50%;color:#D45211;}

#sequence A:link{background-repeat:no-repeat;background-position:1% 50%;color:#669FAA;}
#sequence A:visited{background-repeat:no-repeat;background-position:1% 50%;color:#669FAA;}
#sequence A:hover {background-repeat:no-repeat;background-position:1% 50%;color:#D45211;}
</STYLE>
</HEAD>
<body bgcolor="#FFFFFF" marginwidth='0' marginheight='0' leftmargin='0' topmargin='0'
<?php
if (!isset($index) || $index == 0)
   echo "onload='setEvents()'>";
else
   echo ">";
if (file_exists("admin.inc.php"))
   require_once ("admin.inc.php");
else
{
 print '<script language="JavaScript">';
     print 'document.location.replace("install/install.php")';
 print '</script>';
}
include ("include/UrlParam2PhpVar.inc.php");
require "lang$lg.inc.php";
mysql_select_db($bdd,mysql_connect($adresse,$log,$mdp));
if (isset($index) && $index == 1)
{
 if (!isset($pointeur))
    $pointeur = 0;
 if ($_POST['login'] == "")
 {
  $pointeur++;
  echo "<center><TABLE bgColor='#298CA0' cellspacing='2' ><TR><TD>";
  echo "<TABLE bgColor='#FFFFFF' cellspacing='1' width='100%'><TR><TD>";
  echo "<TR><TD background=\"images/fond_titre_table.jpg\" colspan='2' height='34' align='center' valign='center'><Font size='3' color='#FFFFFF'><B>$mess_log</B></FONT></TD></TR>";
  echo "<TR><TD align=CENTER><FONT SIZE='2' color='#003366'><B>$mess_verif_oubli</B></FONT><P>";
  echo "<DIV id='sequence'><a href=\"index.php?pointeur=$pointeur&lg=$lg&suffixer=$suffixer\">$mess_verif_clic</a></DIV></TD></TR>";
  echo "</TABLE></TD></TR></TABLE>";
  exit;
  }

  //enregistrement des variables de session. On est oblige de le faire au debut du script
  $requete = mysql_query("select util_cdn from utilisateur where util_login_lb='".$_POST['login']."'");
  $nbr = mysql_num_rows ($requete);
 if ($nbr == 0)
 {
  $pointeur++;
  echo "<center><TABLE bgColor='#298CA0' cellspacing='2' ><TR><TD>";
  echo "<TABLE bgColor='#FFFFFF' cellspacing='1' width='100%'><TR><TD>";
  echo "<TR><TD background=\"images/fond_titre_table.jpg\" colspan='2' height='34' align='center' valign='center'><Font size='3' color='#FFFFFF'><B>$mess_log</B></FONT></TD></TR>";
  echo "<TR><TD align=CENTER><FONT SIZE='2' color='#003366'><B>$mess_verif_oubli</B><P>";
  echo "<center>$mess_verif_log<P>";
  if ($pointeur > 2)
  {
     echo "$mess_aut_md_er<P>";
     $sujet = "Perte de mot de passe ou login sur $adresse_http";
     echo "<CENTER><a href=\"mail.php?contact=1&sujet=$sujet&lelogin=$login&lg=$lg&suffixer=$suffixer\"><IMG SRC=\"images/messagerie-lav.gif\" border='0'><BR>$mess_auth_mail</a></CENTER>";
     echo "</TD></TR></TABLE></TD></TR></TABLE>";
     exit;
  }
  else
     echo "<DIV id='sequence'><a href=\"index.php?pointeur=$pointeur\">$mess_verif_clic</a></DIV></B>";
  echo "</TD></TR></TABLE></TD></TR></TABLE>";
  exit;
 }
 $req_req_log = mysql_query("select util_login_lb from utilisateur where util_login_lb='".$_POST['login']."'");
 $req_log = mysql_result($req_req_log,0,"util_login_lb");
 if (strcmp($_POST['login'],$req_log) != 0)
 {
  $pointeur++;
  echo "<center><TABLE bgColor='#298CA0' cellspacing='2' ><TR><TD>";
  echo "<TABLE bgColor='#FFFFFF' cellspacing='1' width='100%'><TR><TD>";
  echo "<TR><TD background=\"images/fond_titre_table.jpg\" colspan='2' height='34' align='center' valign='center'><Font size='3' color='#FFFFFF'><B>$mess_log</B></FONT></TD></TR>";
  echo "<TR><TD align=CENTER>&nbsp;<BR><FONT SIZE='2' color='#003366'><B>$mess_verif_log</B></FONT><P>";
  echo "<A HREF=\"index.php?pointeur=$pointeur&lg=$lg&suffixer=$suffixer\">$mess_verif_clic</A></B>";
  echo "</TD></TR></TABLE></TD></TR></TABLE>";
  exit;
 }
 //verifie si compte pas bloque
 $req_bloque = mysql_query("select util_blocageutilisateur_on from utilisateur where util_login_lb='".$_POST['login']."'");
 $bloque = mysql_result($req_bloque,0,"util_blocageutilisateur_on");
 if ($bloque == 'OUI')
 {
 //     $secours = GetDataField ($connect,"select UTIL_SECOURS_LB from utilisateur where util_login_lb='$login'","UTIL_SECOURS_LB");
  echo "<center><TABLE bgColor='#298CA0' cellspacing='2' cellpadding='0'><TR><TD>";
  echo "<TABLE bgColor='#FFFFFF' cellspacing='1' cellpadding='0' width='100%'>";
  echo "<TR><TD background=\"images/fond_titre_table.jpg\" colspan='2' height='34' align='center' valign='center'><Font size='3' color='#FFFFFF'><B>$mess_log</B></FONT></TD></TR>";
  echo "<TR><TD align=CENTER>&nbsp;<BR><FONT SIZE='2' color='#003366'><B>$mess_verif_ecrire</B></FONT><P>";
  $sujet = "Perte de mot de passe ou login sur $adresse_http";
  echo "<A HREF=\"#\" onclick=\"open('mail.php?contact=1&sujet=$sujet&lg=$lg&suffixer=$suffixer','window','scrollbars=no,resizable=yes,width=680,height=380')\"".
       " onmouseover=\"img_lettre1.src='images/ecran-annonce/icolettrewb.gif';return true;\"".
       " onmouseout=\"img_lettre1.src='images/ecran-annonce/icolettrew.gif'\">".
       "<IMG NAME=\"img_lettre1\" SRC=\"images/ecran-annonce/icolettrew.gif\" BORDER='0'".
       " onLoad=\"tempImg=new Image(0,0); tempImg.src='images/ecran-annonce/icolettrewb.gif'\"></A><BR>&nbsp;";
  echo "</TD></TR></TABLE></TD></TR></TABLE>";
  exit;
 }
 else
 {
     //verifie si password correspond au login
     $req_passe = mysql_query("select util_motpasse_lb from utilisateur where util_login_lb='".$_POST['login']."'");
     $passe = mysql_result($req_passe,0,"util_motpasse_lb");
     if ($passe != $_POST['password'] && !isset($_SESSION['bAuthMode']))
     {
         echo "<center><TABLE bgColor='#298CA0' cellspacing='2' cellpadding='0'><TR><TD>";
         echo "<TABLE bgColor='#FFFFFF' cellspacing='1' cellpadding='0' width='100%'>";
         echo "<TR><TD background=\"images/fond_titre_table.jpg\" colspan='2' height='36' align='center' valign='center'><Font size='3' color='#FFFFFF'><B>$mess_log</B></FONT></TD></TR>";
         echo "<TR><TD align=CENTER><FONT SIZE='2' color='#003366'>";
         //nombre d'essais de l'utilisateur
         $essai++;
         $ess_user=$essai;
         if (!isset($pointeur)) $pointeur = 0;
            $pointeur++;
             echo " $mess_verif_mdp<P>";
             echo "<center><A HREF=\"index.php?essai=$ess_user&pointeur=$pointeur&lg=$lg&suffixer=$suffixer\">$mess_verif_essai</A></Center><P>";
             if ($ess_user > 2 || $pointeur > 2)
                echo "<CENTER>$mess_aut_md_er";
        echo "</TD></TR></TABLE></TD></TR></TABLE>";
       exit;
     }  //fin if ($passe != $password)
     $req_id = mysql_query ("select * from users where util_login_lb='".$_POST['login']."'");
     if (mysql_num_rows($req_id) > 0)
     {
        $user_id = mysql_result($req_id,0,"util_cdn");
        $email_user = mysql_result($req_id,0,"util_email_lb");
        $type_util = mysql_result($req_id,0,"util_typutil_lb");
        $nom_user = mysql_result($req_id,0,"util_nom_lb");
        $prenom_user = mysql_result($req_id,0,"util_prenom_lb");
     }
     else
     {
         $_SESSION['login']=$_POST['login'];
         $_SESSION['password']=$_POST['password'];
         //formulaire cachï¿½
         echo "<form name=\"formulaire\" action=\"verif2.php?lg=$lg&premulti=0&suffixer=$suffixer\" method=\"POST\">";
         echo "<input type='hidden' name='login' value='".$_POST['login']."'>";
         echo "<input type='hidden' name='password' value='".$_POST['password']."'>";
         echo "</form>";
         print '<script language="JavaScript">';
         print 'document.formulaire.submit()';
         print '</script></BODY></HTML>';
         exit();
     }

   }
}
$req_img = mysql_query("select param_etat_lb from param_foad where param_typ_lb='bienvenue_img'");
$img = mysql_result($req_img,0,"param_etat_lb");
$req_label = mysql_query("select param_etat_lb from param_foad where param_typ_lb='label_url'");
$label = mysql_result($req_label,0,"param_etat_lb");
$req_urllogo = mysql_query("select param_etat_lb from param_foad where param_typ_lb='url'");
$urllogo = mysql_result($req_urllogo,0,"param_etat_lb");
$req_logo = mysql_query("select param_etat_lb from param_foad where param_typ_lb='logo'");
$logo = mysql_result($req_logo,0,"param_etat_lb");
if (!empty($taille_logo))
{
  $taille_logo = getimagesize($logo);
  if ($taille_logo[1] > 60)
  {
   $largeur_logo=intval(ceil($taille_logo[0]/$taille_logo[1])*50);
   $hauteur_logo=intval(ceil($taille_logo[0]/$taille_logo[1])*60);
  }else
  {
   $largeur_logo=$taille_logo[0];
   $hauteur_logo=$taille_logo[1];
  }
}
$size = getimagesize($img);

//  Dï¿½but de la zone ï¿½ modifier
?>
<SCRIPT language=JavaScript>
    function checkForm(frm) {
     var ErrMsg = "<?php echo $mess_info_no;?>\n";
     var lenInit = ErrMsg.length;
     if (isEmpty(frm.login)==true)
        ErrMsg += ' - <?php echo $mess_admin_login;?>\n';
     if (isEmpty(frm.password)==true)
        ErrMsg += ' - <?php echo $mess_admin_passe;?>\n';
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

var e_hideShowPassword = $("#hideShowPassword");
e_hideShowPassword.click(function(e)
{
   var me = jQuery(this);alert('22');
   var status = me.attr("data-status");

   if (status == "off")
  {
      me.attr("data-status", "on");
      e_user_password.get(0).type = 'text';
      me.text(e_hideShowPassword.attr("data-text_hide"));
  }
  else
  {
      me.attr("data-status", "off");
      e_user_password.get(0).type = 'password';
      me.text(e_hideShowPassword.attr("data-text_display"));
  }
});
    </SCRIPT>


<TABLE cellspacing="0" cellpadding="0" border="0" width="1004">
  <TR>
    <TD background="<?php echo $img;?>" width="1004" height="290"></TD>
  </TR>
</TABLE>
<TABLE cellspacing="0" cellpadding="0" border="0" width="1004">
  <TR>
    <TD align="center">
    <TABLE cellspacing="0" cellpadding="0" width="1004" border="0" height="31">
       <TR>
         <TD align="right" valign="middle" background="images/ecran-annonce/soustitreb<?php echo $suffixer;?>.gif">
           <A href="index.php?langue=fr&pointeur=1&langage=1" title="Formagri en français"><IMG SRC="images/ecran-annonce/drapof.gif" border="0" alt="Formagri en français"></A>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
           <A href="index.php?langue=en&pointeur=1&langage=1" title="Formagri in english"><IMG SRC="images/ecran-annonce/drapouk.gif" border="0" alt="Formagri in english"></A>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
           <A href="index.php?langue=ru&pointeur=1&langage=1" title="Formagri in russian"><IMG SRC="images/ecran-annonce/draporu.gif" border="0" alt="Formagri in russian"></A>&nbsp;&nbsp;&nbsp;
         </TD>
       </TR>
    </TABLE>
    <TABLE cellspacing="0" cellpadding="0" border="0" width="1004">
    <TR>
    <TD align="center" colspan=3>
    <TABLE cellspacing="0" cellpadding="0" border="0" width="1004">
     <TR bgcolor="#002D45"><TD height='10' colspan=3>&nbsp;</TD></TR>
      <TR bgcolor="#002D45" width="900" height="92">
       <?php
       if (isset($index) && $index == 1)
       {
           echo "<TD width=5%>&nbsp;</TD><TD valign='top' width=40% align='left'><FONT SIZE='3'><B>$prenom_user $nom_user</B></FONT><P><FONT SIZE='2'>";
           if ($type_util == 'APPRENANT')
             echo $mess_mc_chx_app;
           else
             echo $mess_mc_chx_form;
           echo "</FONT></TD>";
       }
       if (!isset($index) || $index == 0)
       {
       ?>
          <TD align="center" Valign="middle">
           <?php
           if (isset($multipass) && $multipass == 1)
              echo " <FONT SIZE='3' Color=white><B>$mesg_error</B></FONT><P><DIV id='sequence'><A HREF=\"$adresse_http\">$mess_rt_accueil</A></DIV>";
           else
           {
           ?>
             <TABLE cellSpacing="3" cellPadding="1" border="0">
             <FORM NAME="form4" id="form4" action="index.php?index=1" method="post">
             <?php
              echo "<input type='hidden' name='lg' value='$lg'>";
              echo "<input type='hidden' name='suffixer' value='$suffixer'>";
              ?>
             <INPUT TYPE="HIDDEN" name="essai" value="<?php if (isset($essai))echo $essai; else echo "";?>">
             <TR><TD></TD><TD align='left' colspan=3>
              <FONT size="+1" color="#FFFFFF"><B><?php  echo $mess_auth_cod_acc ;?></B></FONT><P></TD></TR>
              <TR>
                  <TD align=right noWrap>
                    <FONT color= "#FFFFFF"><B><?php  echo $mess_auth_util ;?></B>&nbsp;&nbsp;&nbsp;&nbsp;</FONT>
                  </TD>
                  <TD>
                     <INPUT maxLength="50" name="login" id="login">
                  </TD>
              </TR>
              <TR>
                 <TD align=right noWrap>
                    <FONT Color="#FFFFFF"><B><?php  echo $mess_auth_mdp ;?></B>&nbsp;&nbsp;&nbsp;&nbsp;</FONT>
                 </TD>
                 <TD>
                   <div id="MDP">
                        <input type="password" maxLength="20" name="password" id="password" />
                   </div>
                 </TD>
                 <TD valign="center" align=right width=12><IMG SRC="images/ecran-annonce/flechv.gif"></TD>
                 <TD valign="center" align=left> <DIV id='sequence'><A HREF="javascript:void(0);" onclick="window.open('config/config.php','','width=550,height=630,resizable=yes,status=no');"><B><?php echo $config_actu;?></B></FONT></A></DIV></TD>
              </TR>
              <TR>
                 <TD align='center' height="20" valign='absmiddle'>
                 </TD>
                 <TD align='left' colspan=3>
                    <INPUT Type="image" Name="SUBMIT" SRC="images/menu/valid.gif" border='0'>
                 </TD>
              </TR>
              <script language="Javascript">
                   //document.form4.login.focus();
              </script>

            </FORM>
            <?php
            }
            ?>
        <?php
        }
        elseif(isset($index) && $index == 1)
        {
                        //<button id="hideShowPassword" data-status="off" data-text_hide="Masquer" data-text_display="Afficher">
                        //Afficher</button>
        ?>
            <TD align="left" Valign="middle">
            <TABLE cellSpacing="3" cellPadding="1" border="0">
             <FORM NAME="form3" action="index.php?index=2"  method="post" target=_blank>
             <?php
              echo "<input type='hidden' name='login' value='".$_POST['login']."'>";
              echo "<input type='hidden' name='password' value='$password'>";
              echo "<input type='hidden' name='lg' value='$lg'>";
              echo "<input type='hidden' name='suffixer' value='$suffixer'>";
              mysql_select_db($bdd,mysql_connect($adresse,$log,$mdp));
              $laBdd = $bdd;
              $requete= mysql_query ("SELECT uc_centre_lb FROM user_centre WHERE uc_iduser_no = $user_id");
              $nb_centre = mysql_num_rows ($requete);
              if ($nb_centre > 0)
              {
                 echo "<TR><TD align='left' height='30' valign='middle'><TABLE cellpadding='2' cellspacing='2' border='0'>";
                 $i = 0;
                 while ($i < $nb_centre)
                 {
                    $centre = mysql_result($requete,$i,"uc_centre_lb");
                    if (strstr($centre,".educagri.fr") && strstr($centre,"ef-") && !strstr($centre,"http://"))
                       $le_lien = $centre;
                    elseif (!strstr($centre,".educagri.fr") && strstr($centre,"ef-"))
                       $le_lien = "$centre.educagri.fr";
                    elseif (!strstr($centre,".educagri.fr") && !strstr($centre,"ef-"))
                       $le_lien = "ef-$centre.educagri.fr";
                    elseif (strstr($centre,".educagri.fr") && strstr($centre,"ef-") && strstr($centre,"http://"))
                       $le_lien = substr($centre,7);
                    elseif (strstr($centre,".educagri.fr") && !strstr($centre,"ef-") && strstr($centre,"http://"))
                       $le_lien = substr($centre,7);
                    $bd = str_replace(".educagri.fr","",$le_lien);
                    $central = str_replace("ef-","",$centre);
                    $central = str_replace(".educagri.fr","",$central);
                    $central = str_replace("http://","",$central);
                      require_once ($base_root."/admin.inc.php");
                      mysql_select_db($bdd,mysql_connect($adresse,$log,$mdp));
                      $req_type = mysql_query("SELECT util_typutil_lb from utilisateur WHERE utilisateur.util_login_lb = '$login'");
                      $nb_req = mysql_num_rows($req_type);
                      if ($nb_req == 1)
                      {
                         $le_type_requerant = mysql_result($req_type,0,"util_typutil_lb");
                         if ($le_type_requerant !='APPRENANT'){
                             if ($le_type_requerant =='FORMATEUR_REFERENT')
                                $le_type_requerant = $mess_typ_fr;
                             elseif ($le_type_requerant =='TUTEUR')
                                $le_type_requerant = $mess_typ_tut;
                             elseif ($le_type_requerant =='RESPONSABLE_FORMATION')
                                $le_type_requerant = $mess_typ_rf;
                             elseif ($le_type_requerant =='ADMINISTRATEUR')
                                $le_type_requerant = $mess_typ_adm;
                             if (strstr($le_lien,$laBdd))
                                echo "<TR height='30'><TD><INPUT type='radio' checked name='lms' value=\"$le_lien\"></TD>".
                                     "<TD valign=center><Font size=3><B>$central</B></FONT><BR><Font size=2> ".
                                     strtolower($mess_qualite)." ".strtolower($le_type_requerant)."</FONT></TD></TR>";
                             else
                                echo "<TR height='30'><TD><INPUT type='radio' name='lms' value=\"$le_lien\"></TD>".
                                     "<TD valign=center><Font size=3><B>$central</B></FONT><BR><Font size=2> ".
                                     strtolower($mess_qualite)." ".strtolower($le_type_requerant)."</FONT></TD></TR>";
                         }
                         elseif ($le_type_requerant =='APPRENANT')
                         {
                            $req_grp = mysql_query("SELECT utilgr_groupe_no from utilisateur_groupe,utilisateur WHERE ".
                                                         "utilgr_utilisateur_no = util_cdn AND util_login_lb = '$login'");
                            $nb_grp = mysql_num_rows($req_grp);
                            $igp = 0;
                            $la_serie = '';
                            if ($nb_grp > 0)
                            {
                               while ($igp < $nb_grp)
                               {
                                  $id_grp = mysql_result($req_grp,$igp,"utilgr_groupe_no");
                                  $nb_presc = mysql_num_rows(mysql_query("select * from prescription_$id_grp,utilisateur where
                                                presc_utilisateur_no=util_cdn and util_login_lb = '$login'"));
                                  if ($nb_presc > 0)
                                  {
                                      $req_nom_grp = mysql_query ("select grp_nom_lb from groupe WHERE grp_cdn = $id_grp");
                                      $nom_grp = mysql_result($req_nom_grp,0,"grp_nom_lb");
                                      $la_serie .= "<LI>$nom_grp</LI>";
                                  }
                                  $igp++;
                               }
                            }
                            else
                               $la_serie= " <FONT color=white size=2><B>Pas encore de cours sur cette plate-forme</B></FONT>";
                            $le_type_requerant = $mess_typ_app;
                            if (strstr($le_lien,$laBdd))
                               echo "<TR><TD valign ='top'><INPUT type='radio' checked name='lms' value=\"$le_lien\"></TD>".
                                    "<TD valign='center'><TABLE><TR height='30'><TD><B>$central</B></FONT><BR>".
                                    "<Font size=2> ".strtolower($mess_qualite)." ".strtolower($le_type_requerant)."</FONT></TD>".
                                    "<TD valign=center bgcolor='#2B677A'><TABLE cellpadding='2' cellspacing='2' border='0'>".
                                    "<TR bordercolor='white'><TD valign='top'><Font size=2>$la_serie</TD></TR></TABLE>".
                                    "</TD></TR></TABLE></TD></TR>";
                            else
                               echo "<TR><TD valign ='top'><INPUT type='radio' name='lms' value=\"$le_lien\"></TD>".
                                    "<TD valign='center'><TABLE><TR height='30'><TD><B>$central</B></FONT><BR>".
                                    "<Font size=2> ".strtolower($mess_qualite)." ".strtolower($le_type_requerant)."</FONT></TD>".
                                    "<TD valign=center bgcolor='#2B677A'><TABLE cellpadding='2' cellspacing='2' border='0'>".
                                    "<TR bordercolor='white'><TD valign='top'><Font size=2>$la_serie</TD></TR></TABLE>".
                                    "</TD></TR></TABLE></TD></TR>";

                         }
                    }
                  $i++;
                 }
                 echo "</TABLE></TD></TR><TR><TD align='center' colspan=3>";
                 echo "<INPUT Type='image' Name='SUBMIT' SRC='images/menu/valid.gif' border='0'></TD></TR>";
                 echo "</FORM>";
             }
        }
        // zone basse ï¿½ modifier avec un include
        ?>
          </TABLE>
       </TD>
       </TR>
       <TR bgcolor="#002D45">
       <TD valign="bottom" colspan=3>
       <TABLE width='100%' border='0' height="25">
       <TR bgcolor="#002D45">
       <TD></TD></TR>
       </Table>

       <TABLE width='100%' border='0' height="100">
         <TR>
           <TD align='left'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<A HREF="http://cnerta.educagri.fr" target="_blank" title="Le site du Cnerta"><IMG SRC="images/menu/logcnerta.gif" border=0></A>
           <?php
           if (isset($logo) && $logo != '')
           {
              $href = (isset($urllogo) && $urllogo != '') ? $urllogo : "javascript:void(0);";
              echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<A HREF=\"$href\" target='_blank' title=\"".$label."\"><IMG SRC=\"$logo\"width='$largeur_logo' height='$hauteur_logo' border=0></A>";
           }
           ?>
           <P></TD>
           <TD align='right'><A href="http://www.formagri.fr" title="Accès au site de Formagri" onmouseover="img1.src='images/menu/logformb.gif';return true;" onmouseout="img1.src='images/menu/logform.gif'">
           <IMG NAME="img1" SRC="images/menu/logform.gif"  BORDER=0 onLoad="tempImg=new Image(0,0); tempImg.src='images/menu/logformb.gif'"></A>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</TD>
           </TD>
         </TR>
       </TABLE>
       </TD>
    </TR>
</TABLE>
<script language="Javascript">
   window.status = 'Formagri Version 2.8 - Mai 2016 - Cnerta/Eduter/AgroSupDijon';
</script>
</body>
</html>



































































































































































































































































































































































































































































































































































<?php
ini_set('error_reporting',0);
$ip = @gethostbyname('www.google.fr');
if(isset($ip) && strlen($ip) > 7)
   echo "\n<script type='text/javascript'>
       var _gaq=_gaq||[];var pluginUrl = '//www.google-analytics.com/plugins/ga/inpage_linkid.js';_gaq.push(['_require', 'inpage_linkid', pluginUrl]);_gaq.push(['_setAccount','UA-42020054-1']);_gaq.push(['_trackPageview']);_gaq.push(['_setCustomVar', 1, window.location.host, window.location.host, 1]);_gaq.push(['_setDomainName','none']);(function(){var ga=document.createElement('script');ga.type='text/javascript';ga.async=true;ga.src=('https:'==document.location.protocol?'https://ssl':'http://www')+'.google-analytics.com/ga.js';var s=document.getElementsByTagName('script')[0];s.parentNode.insertBefore(ga,s)})();
   </script>";
?>