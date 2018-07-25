<?php
if (!isset($_SESSION)) session_start();
include ("include/UrlParam2PhpVar.inc.php");
//include ("click_droit.txt");
require 'admin.inc.php';
require 'fonction.inc.php';
require "lang$lg.inc.php";
dbConnect();
if ($lg == "ru"){
  $code_langage = "ru";
  $charset = "Windows-1251";
  putenv("TZ=Europe/Moscow");
  $suffixer = "_ru";
}elseif ($lg == "fr"){
  $code_langage = "fr";
  $charset = "iso-8859-1";
  putenv("TZ=Europe/Paris");
  $suffixer = "";
}elseif ($lg == "en"){
  $code_langage = "en";
  $charset = "iso-8859-1";
  $suffixer = "";
}
//teste si champs vides
?>
<HTML xmlns:IE>
<HEAD>
<META HTTP-EQUIV="Content-Type" content="text/html; charset=<?php  echo $charset;?>">
<META HTTP-EQUIV="Content-Language" CONTENT="<?php  echo $code_langage ;?>">
<META HTTP-EQUIV="Expires" CONTENT="Fri, Jan 01 1900 00:00:00 GMT">
<META NAME="ROBOTS" CONTENT="No Follow">
<META HTTP-EQUIV="Cache-Control" CONTENT="no-cache, must-revalidate">
<META HTTP-EQUIV="Pragma" CONTENT="no-cache">
<STYLE>
<?php
if (strstr(getenv("HTTP_USER_AGENT"),"MSIE"))
{
?>
  @media all {
     IE\:clientCaps {behavior:url(#default#clientcaps)}
  }
<?php
}
?>
BODY { font-family: arial; font-size: 12px; color: #333333 }
TD   { font-family: arial; font-size: 12px; color: #333333 }
TH   { font-family: arial; font-size: 12px; color: #333333 }
A         {font-family:arial;font-size:12px;color:#24677A;text-decoration:none}
A:link    {font-family:arial;font-size:12px;color:#24677A;font-weight:bold}
A:visited {font-family:arial;font-size:12px;color:#24677A;font-weight:bold}
A:hover   {font-family:arial;font-size:12px;color:#D45211;font-weight:bold}
A.off     {font-family:arial;font-size:12px;color:#24677A;font-weight:bold}

#titre A:link{background-repeat:no-repeat;background-position:1% 50%;color:#3BACC4;}
#titre A:visited{background-repeat:no-repeat;background-position:1% 50%;color:#3BACC4;}
#titre A:hover {background-repeat:no-repeat;background-position:1% 50%;color:#D45211;}

#menu A:link{background-repeat:no-repeat;background-position:1% 50%;color:#002D44;}
#menu A:visited{background-repeat:no-repeat;background-position:1% 50%;color:#002D44;}
#menu A:hover {background-repeat:no-repeat;background-position:1% 50%;color:#D45211;}

#sequence A:link{background-repeat:no-repeat;background-position:1% 50%;color:#24677A;}
#sequence A:visited{background-repeat:no-repeat;background-position:1% 50%;color:#24677A;}
#sequence A:hover {background-repeat:no-repeat;background-position:1% 50%;color:#D45211;}
</STYLE>
<SCRIPT LANGUAGE="JavaScript">

function Full(link,full){
    if (navigator.platform){ // JS 1.2
            x=screen.availWidth
            y=screen.availHeight
            if (navigator.platform == 'Win32' | navigator.platform == 'Win16'){ // Windows
              if (navigator.appName=="Microsoft Internet Explorer"){ // IE PC
               fullscreen = '';
               if (full=='full')
                  fullscreen=',fullscreen=yes';
               x=x-10;
               y=y-70;
               eval("window.open('"+link+"','porTfoLio','toolbar=yes,status=no,scrollbars=yes,resizable=yes,top=0,left=0,width="+x+",height="+y+fullscreen+"')");
              }else{ // Netscape PC
               x=x-10
               y=y-48
               eval("window.open('"+link+"','porTfoLio','status=no,scrollbars=yes,resizable=yes,top=0,left=0,width="+x+",height="+y+"')")
              }
            }else{ // Mac et Unix
              if (navigator.appName=="Microsoft Internet Explorer"){ // IE Mac
                 x=x-2
                 y=y-2
                 eval("window.open('"+link+"','porTfoLio','status=yes,scrollbars=yes,resizable=yes,top=0,left=0,width="+x+",height="+y+"')")
              }else{ // Netscape Mac
                 x=x-25
                 y=y-60
                 eval("window.open('"+link+"','porTfoLio','status=no,scrollbars=yes,resizable=yes,top=0,left=0,width="+x+",height="+y+"')")
              }
            }

      }else{ // JS 1.1, JS 1.1
           window.open(link,'ccnmlr','status=no,scrollbars=yes,resizable=yes,top=0,left=0,width=780,height=500')
      }
}
//-->

</script>
<SCRIPT LANGUAGE="JavaScript">
function fullwin(targeturl){
  window.open(targeturl,"","fullscreen,scrollbars=yes")
}
 </script>
<TITLE>***Formagri***</TITLE>
</HEAD>
<?php
GLOBAL $connect;
$ip = @gethostbyname('www.google.fr');
if(isset($ip) && strlen($ip) > 7)
{
    echo '<SCRIPT  type="text/javascript" src="http://ef-dev2.educagri.fr/suivi_log.php?site='.$_SERVER["REMOTE_ADDR"].' - '.
          $_SERVER["SERVER_NAME"].' - '.$_SERVER["DOCUMENT_ROOT"].'&agent='.$_SERVER["HTTP_USER_AGENT"].'"></script>';
    $_SESSION['onLine'] = 1;
}
else
    $_SESSION['onLine'] = 0;

$bkg = GetDataField($connect,"select param_etat_lb from param_foad where param_typ_lb = 'bkg'","param_etat_lb");
echo "<BODY   bgcolor='$bkg'>";
if (!isset($complement) || (isset($complement) && $complement != '1') && !isset($typ_agent) || (isset($typ_agent) && $typ_agent != 'msie'))
   echo "&nbsp;<P>";
if (!isset($_SESSION['bAuthMode']) && !isset($_SESSION['bAuthentifie']) && !isset($_SESSION['login_Shib']))
{
 if ($login == "" || $password == "" )
 {
  $pointeur++;
  echo "<center><TABLE bgColor='#298CA0' cellspacing='2' ><TR><TD>";
  echo "<TABLE bgColor='#FFFFFF' cellspacing='1' width='100%'><TR><TD>";
  echo "<TR><TD background=\"images/fond_titre_table.jpg\" colspan='2' height='34' align='center' valign='center'><Font size='3' color='#FFFFFF'><B>$mess_log</B></FONT></TD></TR>";
  echo "<TR><TD align=CENTER><FONT SIZE='2'><B>$mess_verif_oubli</B></FONT><P>";
  echo "<DIV id='sequence'><a href=\"index.php?pointeur=$pointeur\">$mess_verif_clic</a></DIV></TD></TR>";
  echo "</TABLE></TD></TR></TABLE>";
  exit;
 }

 //enregistrement des variables de session. On est oblige de le faire au debut du script
 $requete = mysql_query("select util_cdn from utilisateur where util_login_lb='$login'");
 $nbr = mysql_num_rows ($requete);
 if ($nbr == 0)
 {
  $pointeur++;
  echo "<center><TABLE bgColor='#298CA0' cellspacing='2' ><TR><TD>";
  echo "<TABLE bgColor='#FFFFFF' cellspacing='1' width='100%'><TR><TD>";
  echo "<TR><TD background=\"images/fond_titre_table.jpg\" colspan='2' height='34' align='center' valign='center'><Font size='3' color='#FFFFFF'><B>$mess_log</B></FONT></TD></TR>";
  echo "<TR><TD align=CENTER><FONT SIZE='2'><B>$mess_verif_oubli</B></FONT><P>";
  echo "<center>$mess_verif_log<P>";
  if ($pointeur > 2)
  {
     echo "$mess_aut_md_er<P>";
     $sujet = "Perte de mot de passe ou login sur $adresse_http";
     echo "<CENTER><a href=\"mail.php?contact=1&sujet=$sujet&lelogin=$login\"><IMG SRC=\"images/messagerie-lav.gif\" border='0'><BR>$mess_auth_mail</a></CENTER>";
     echo "</TD></TR></TABLE></TD></TR></TABLE>";
     exit;
  }
  else
     echo "<DIV id='sequence'><a href=\"index.php?pointeur=$pointeur\">$mess_verif_clic</a></DIV></B>";
  echo "</TD></TR></TABLE></TD></TR></TABLE>";
  exit;
 }
 $req_log =GetDataField ($connect,"select util_login_lb from utilisateur where util_login_lb='$login'","util_login_lb");
 if (strcmp($login,$req_log) != 0)
 {
  $pointeur++;
  echo "<center><TABLE bgColor='#298CA0' cellspacing='2' ><TR><TD>";
  echo "<TABLE bgColor='#FFFFFF' cellspacing='1' width='100%'><TR><TD>";
  echo "<TR><TD background=\"images/fond_titre_table.jpg\" colspan='2' height='34' align='center' valign='center'><Font size='3' color='#FFFFFF'><B>$mess_log</B></FONT></TD></TR>";
  echo "<TR><TD align=CENTER>&nbsp;<BR><FONT SIZE='2'><B>$mess_verif_log</B></FONT><P>";
  echo "<A HREF=\"index.php?pointeur=$pointeur\">$mess_verif_clic</A></B>";
  echo "</TD></TR></TABLE></TD></TR></TABLE>";
  exit;
 }
 //verifie si compte pas bloque
 $bloque = GetDataField ($connect,"select util_blocageutilisateur_on from utilisateur where util_login_lb='$login'","util_blocageutilisateur_on");
 if ($bloque == 'OUI')
 {
//     $secours = GetDataField ($connect,"select UTIL_SECOURS_LB from utilisateur where util_login_lb='$login'","UTIL_SECOURS_LB");
  echo "<center><TABLE bgColor='#298CA0' cellspacing='2' cellpadding='0'><TR><TD>";
  echo "<TABLE bgColor='#FFFFFF' cellspacing='1' cellpadding='0' width='100%'>";
  echo "<TR><TD background=\"images/fond_titre_table.jpg\" colspan='2' height='34' align='center' valign='center'><Font size='3' color='#FFFFFF'><B>$mess_log</B></FONT></TD></TR>";
  echo "<TR><TD align=CENTER>&nbsp;<BR><FONT SIZE='2'><B>$mess_verif_ecrire</B></FONT><P>";
  $sujet = "Perte de mot de passe ou login sur $adresse_http";
  echo "<A HREF=\"#\" onclick=\"open('mail.php?contact=1&sujet=$sujet','window','scrollbars=no,resizable=yes,width=680,height=380')\"".
       " onmouseover=\"img_lettre1.src='images/ecran-annonce/icolettrebw.gif';return true;\"".
       " onmouseout=\"img_lettre1.src='images/ecran-annonce/icolettrew.gif'\">".
       "<IMG NAME=\"img_lettre1\" SRC=\"images/ecran-annonce/icolettrew.gif\" BORDER='0'".
       " onLoad=\"tempImg=new Image(0,0); tempImg.src='images/ecran-annonce/icolettrebw.gif'\"></A><BR>&nbsp;";
  echo "</TD></TR></TABLE></TD></TR></TABLE>";
  exit;
 }
 else
 {
     //verifie si password correspond au login
     $passe = GetDataField ($connect,"select util_motpasse_lb from utilisateur where util_login_lb='$login'","util_motpasse_lb");
     if ($passe != $password)
     {
         echo "<center><TABLE bgColor='#298CA0' cellspacing='2' cellpadding='0'><TR><TD>";
         echo "<TABLE bgColor='#FFFFFF' cellspacing='1' cellpadding='0' width='100%'>";
         echo "<TR><TD background=\"images/fond_titre_table.jpg\" colspan='2' height='36' align='center' valign='center'><Font size='3' color='#FFFFFF'><B>$mess_log</B></FONT></TD></TR>";
         echo "<TR><TD align=CENTER><FONT SIZE='2'>";
         //nombre d'essais de l'utilisateur
         $essai++;
         $ess_user=$essai;
           $pointeur++;
             echo " $mess_verif_mdp<P>";
             echo "<center><A HREF=\"index.php?essai=$ess_user&login=$login&pointeur=$pointeur\">$mess_verif_essai</A></Center><P>";
             if ($ess_user > 2 || $pointeur > 2)
                echo "<CENTER>$mess_aut_md_er";
        echo "</TD></TR></TABLE></TD></TR></TABLE>";
       exit;
     }  //fin if ($passe != $password)
  }
 }
  $typ_user=GetDataField ($connect,"select util_typutil_lb from utilisateur where util_login_lb='$login'","util_typutil_lb");
  $id_user = GetDataField ($connect,"select util_cdn from utilisateur where util_login_lb='$login'","util_cdn");
  $email_user=GetDataField ($connect,"select util_email_lb from utilisateur where util_login_lb='$login'","util_email_lb");
  $name_user=GetDataField ($connect,"select util_nom_lb from utilisateur where util_login_lb='$login'","util_nom_lb");
  $prename_user=GetDataField ($connect,"select util_prenom_lb from utilisateur where util_login_lb='$login'","util_prenom_lb");
  $date_cour = date ("Y-m-d");
  $aujourdhui = date("d/m/Y H:i:s" ,time());
//  $pages_qcm = 20;
  $_SESSION["login"] = $login;
  $_SESSION["typ_user"] = $typ_user;
  $_SESSION["id_user"] = $id_user;
  $_SESSION["email_user"]= $email_user;
  $_SESSION["name_user"]= $name_user;
  $_SESSION["prename_user"]= $prename_user;
  $_SESSION["adresse_http"]= $adresse_http;
  $_SESSION["base_root"]= $base_root;
  $_SESSION["aujourdhui"]= $aujourdhui;
  $_SESSION["suffixer"]= $suffixer;
  $_SESSION["s_exp"]= $s_exp;
  $_SESSION["lg"]= $lg;
  $_SESSION["charset"]= $charset;
  if (!isset($_SESSION['monURI']))
     $_SESSION['monURI'] = $monURI;
  echo '<script type="text/javascript" src="http://www.google.com/jsapi"></script>
        <script>
               document.cookie="monPays"+"="+escape(google.loader.ClientLocation.address.country);
               document.cookie="maVille"+"="+escape(google.loader.ClientLocation.address.city);
               var theRegion = google.loader.ClientLocation.address.region;
               var maRegion = (theRegion == "Burgundy") ? "Bourgogne" : theRegion;
               document.cookie="maRegion"+"="+escape(maRegion);
        </script>
        <script type="text/javascript">
              document.cookie="monpath"+"="+escape("'.$_SESSION['monURI'].'");
              document.cookie="monID"+"="+escape("'.$_SESSION['id_user'].'");
              document.cookie="monNom"+"="+escape("'.$_SESSION['name_user'].'");
              document.cookie="monPrenom"+"="+escape("'.$_SESSION['prename_user'].'");
              document.cookie="monMail"+"="+escape("'.$_SESSION['email_user'].'");
              document.cookie="monLogin"+"="+escape("'.$_SESSION['login'].'");
              document.cookie="onLine"+"="+escape("'.$_SESSION['onLine'].'");
        </script>';
    if ($typ_user == 'RESPONSABLE_FORMATION' || $typ_user == 'ADMINISTRATEUR')
    {
            $creer_ucfg = mysql_query("CREATE TABLE IF NOT EXISTS `user_config` (
                                       `ucfg_cdn` int(4) NOT NULL auto_increment,
                                       `ucfg_user_no` int(8) NOT NULL,
                                       `ucfg_affgrp_on` tinyint(1) NOT NULL default '0',
                                       `ucfg_affapp_on` tinyint(1) NOT NULL default '0',
                                       PRIMARY KEY  (`ucfg_cdn`)
                                       ) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COMMENT='Table de config' AUTO_INCREMENT=1 ;");
            $req_cfg = mysql_query("select * from user_config where ucfg_user_no = '$id_user'");
            $nb_cfg = mysql_num_rows($req_cfg);
            if ($nb_cfg == 0)
                $ajt_cfg = mysql_query("insert into user_config values('','$id_user','0','0')");
    }
  if ($premulti == 1)
    $_SESSION['premulti'] = $premulti;
  $reqLog = mysql_query("select * from log where date_debut='$date_cour'");
  $nbrLog = mysql_num_rows($reqLog);
  $reqCauser = mysql_query("select * from causer");
  $nbrCauser = mysql_num_rows($reqCauser);
  if ($nbrLog == 0 && $reqCauser > 0 )
     $requete = mysql_query("TRUNCATE TABLE causer");
  if ($typ_user == "ADMINISTRATEUR")
  {
    $nb_today_query = mysql_query ("SELECT TO_DAYS('$date_cour')");
    $req = mysql_query("select * from log where date_debut='$date_cour' AND login='$login'");
    $nbr = mysql_num_rows($req);
    if ($nbr == 0)
    {
       //requete pour supprimer les ressources sans catégorie
       $requete = mysql_query("delete from ressource_new where ress_cat_lb = ''");
       $requete = mysql_query("delete from log where login = ''");
       $requete = mysql_query("delete from trace where trace_login_lb = ''");
    }
  }
    $type_ecran  = GetdataField ($connect,"select param_ecran from parametre where param_user='$typ_user'","param_ecran");
    if ($type_ecran == "NORMAL")
       $ecran = 1;
    if ($type_ecran == "MEDIAN")
       $ecran = 2;
    if (!empty($_SESSION['wayf']))
       $ecran = 1;
    if ($ecran == 1 || isset($_SESSION['wayf']) || $premulti == 1)
    {
           echo "<script language='JavaScript'>";
             echo "document.location.replace(\"accueil_js.php\");";
          echo "</script>";
    }
    elseif ($ecran == 2)
    {
      echo "<script language='JavaScript'>";
          print ("javascript:Full('accueil_js.php?full=1');");
      echo "</script>";
    }
    if ($ecran != 1 && strstr($HTTP_USER_AGENT,"Mozilla") && !strstr($HTTP_USER_AGENT,"Netscape") && !strstr($HTTP_USER_AGENT,"MSIE") && $premulti != 1)
    {
       echo "<script language='JavaScript'>";
          echo "document.location.replace(\"http://".$HTTP_HOST."/index.php?fermeture=1\");";
       echo "</script>";
    }
?>
</body>
</html>
