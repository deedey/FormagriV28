<?php
if (!isset($_SESSION)) session_start();
include ("include/UrlParam2PhpVar.inc.php");
if (!isset($_SESSION['id_user']) || $_SESSION['id_user'] == "")
{
  exit();
}
require "admin.inc.php";
require 'fonction.inc.php';
require "lang$lg.inc.php";
//include ("click_droit.txt");
dbConnect();
//error_reporting (E_ALL);
if (isset($_SESSION['lg']))
{
  if ($_SESSION['lg'] == "ru")
  {
    $code_langage = "ru";
    $charset = "Windows-1251";
    putenv("TZ=Europe/Moscow");
  }
  elseif ($_SESSION['lg'] == "fr")
  {
    $code_langage = "fr";
    $charset = "iso-8859-1";
    putenv("TZ=Europe/Paris");
  }
  elseif ($_SESSION['lg'] == "en")
  {
    $code_langage = "en";
    $charset = "iso-8859-1";
  }
}
$aSuperGlobal = array ('_GET','_POST','_SESSION');
foreach ($aSuperGlobal as $superGlobal)
{
       foreach ($GLOBALS[$superGlobal] as $key => $superGlobalVal)
       {
               $$key = $superGlobalVal;
       }
}
include ('include/varGlobals.inc.php');
$currentUser = nomUser($_SESSION['id_user']);
switch ($_SESSION['typ_user'])
{
   case 'ADMINISTRATEUR' : $RoleUser = "Adm";break;
   case 'APPRENANT' : $RoleUser = "App";break;
   case 'RESPONSABLE_FORMATION' : $RoleUser = "RF";break;
   case 'FORMATEUR_REFERENT' : $RoleUser = "FR";break;
   case 'TUTEUR' : $RoleUser = "Tut";break;
}
$ip = $_SERVER['REMOTE_ADDR'];
if (IsIPv6($ip) == TRUE)
    $serveur = gethostbyaddr($_SERVER['REMOTE_ADDR']);
else
   $serveur = $_SERVER['REMOTE_ADDR'];
$ChampServeur = $serveur."-".$_COOKIE['maVille']."-".$_COOKIE['maRegion']."-".$_COOKIE['monPays'];
$url = parse_url($_SERVER['REQUEST_URI']);
$resultUrl=array();
parse_str($url['query'],$resultUrl);
if (isset($resultUrl['endpoint']))
{
echo "<script type='text/javascript' src='OutilsJs/jquery-182-min.js'></script>";
echo "<script type='text/javascript' src='lib/TinCanGeneric/scripts/TinCanJS/build/tincan-min.js'></script>";

echo '<script type="text/javascript">
  FormagriExample = {};
  var getTitre = " '.$RoleUser.' : '.$ChampServeur.'";
  FormagriExample.CourseActivity = {
    id: "http://formagri.com/Suivi",
    definition: {
        type: "http://adlnet.gov/expapi/activities/course",
        name: {
            "fr-FR": "formagri.com/suivi - Tin Can Course"
        },
        description: {
            "fr-FR": getTitre
        }
    }
  };

  FormagriExample.getContext = function(parentActivityId) {
    var ctx = {
        contextActivities: {
            grouping: {
                id: FormagriExample.CourseActivity.id
            }
        }
    };
    if (parentActivityId !== undefined && parentActivityId !== null) {
        ctx.contextActivities.parent = {
            id: parentActivityId
        };
    }
    return ctx;
  };
  var tincan = new TinCan (
  {
    url: window.location.href,
    activity: {
       id: "Suivi : " + getTitre,
       definition: {
          name: {
             "fr-FR": "Suivi : " + getTitre
          },
          description: {
             "fr-FR":  "'.$_COOKIE['monPrenom'].' '.$_COOKIE['monNom'].' s\'est déconnecté."
          }
       }
    }
  }
  );

  tincan.sendStatement(
            {
                verb: "logged_out",
                context: FormagriExample.getContext(
                    FormagriExample.CourseActivity.id
                )
            },
            function () {}
  );
</script>
';
}
$nom_user=$_SESSION['name_user'];
$prenom_user=$_SESSION['prename_user'];
$type_ecran  = GetdataField ($connect,"select param_ecran from parametre where param_user='$typ_user'","param_ecran");
if ($type_ecran == "NORMAL")
    $ecran = 1;
if ($type_ecran == "MEDIAN")
    $ecran = 2;
// Détermine la provenance de la connection
if (!isset($bouton) || (isset($bouton) && $bouton != 1))
{
   echo "<body bgcolor=\"#002D44\" marginwidth='0' marginheight='0' leftmargin='0' topmargin='0'>";
   echo "<CENTER><TABLE bgColor='#298CA0' cellspacing='2'><TR><TD>";
   echo "<TABLE bgColor='#FFFFFF' cellspacing='2'>";
   echo "<TR><TD background=\"images/fond_titre_table.jpg\" height='40' align='center' valign='center'><Font size='3' color='#FFFFFF'><B>$mess_dncx</B></FONT></TD></TR>";
   echo "<TR><TD>";
}
if ((isset($fini) && $fini== 0) || !isset($fini))
{
  GLOBAL $premulti;
  if (!isset($bouton) || (isset($bouton) && $bouton != 1))
  {
     $message = "<CENTER><Font color = '#333333' size='2'>$mess_del_merci</FONT></CENTER>";
  }
  $date=date("d/m/Y H:i:s" ,time());
  $date_fin=date("Y/m/d");
  $heure_fin= substr($date,11);
  $ch_heure_fin = explode (":",$heure_fin);
  $hour_fin = $ch_heure_fin[0];
  $minutes_fin = $ch_heure_fin[1];
  $reqLog = mysql_query("select log_cdn from log where login = '$login' AND date_fin ='0000-00-00' AND serveur = '$ChampServeur'");
  $nb_log = mysql_num_rows($reqLog);
  if ($nb_log > 0)   $id_log = mysql_result($reqLog,0,'log_cdn');
  if ($nb_log == 0)
  {
     $message .="<CENTER><Font color = '#333333' size='2'>$mess_del_vis</FONT></CENTER>";
     if (!isset($bouton) || (isset($bouton) && $bouton != 1))
     {
        echo $message;
     }
     if ($ecran == 2)
     {
       if (!empty($_SESSION))
          destroySession();
       ?>
       <SCRIPT language=javascript>
         setTimeout("Quit()",500);
         function Quit() {
            <?php  echo "top.parent.opener=null;top.parent.close();return false;";?>
          }
        </SCRIPT></BODY></HTML>
        <?php
     }
     else
     {
       if (!empty($_SESSION))
          destroySession();
      ?>
       <SCRIPT language=javascript>
         setTimeout("Quit()",500);
          function Quit() {
           top.opener=null;top.close();return false;
          }
       </SCRIPT>
       </BODY>
       </HTML>
       <?php
     }
    exit;
  }
  $heure_debut = GetDataField ($connect,"select heure_debut from log where login = '$login' AND date_fin ='0000-00-00' AND serveur = '$ChampServeur'","heure_debut");
  $ch_heure_deb = explode (":",$heure_debut);
  $heure_debut;
  $heure_deb = $ch_heure_deb[0];
  $minutes_deb = $ch_heure_deb[1];
  $dif_heures = $hour_fin-$heure_deb;
  if ($dif_heures == 0)
    $minutes_plus = 0;
  else
    $minutes_plus = $dif_heures*60;
  if (($minutes_fin > $minutes_deb) || ($minutes_fin == $minutes_deb))
  {
    $minutes = $minutes_fin-$minutes_deb;
    $minutes_rest = $minutes;
  }
  else
  {
    $dif_heures--;
    $minutes_plus=60-$minutes_deb+$minutes_fin;
    $minutes = $dif_heures*60;
    $minutes_rest = $minutes_plus;
  }
  $minutes_total = $minutes+$minutes_plus;
  $date_deb = GetDataField ($connect,"select date_debut from log where login = '$login' AND date_fin = '0000-00-00' AND  serveur = '$ChampServeur'","date_debut");
  $nb_deb = mysql_query ("select TO_DAYS('$date_deb')");
  $nb_j_deb = mysql_result ($nb_deb,0);
  $nb_fin = mysql_query ("select TO_DAYS('$date_fin')");
  $nb_j_fin = mysql_result ($nb_fin,0);
  if ($nb_j_deb < $nb_j_fin)
     $minutes_total = 1440 + $minutes_total;
  if ($minutes_total == 0)
     $minutes_total = 1;
  $inserer = mysql_query("UPDATE log SET date_fin = '$date_fin',heure_fin = '$heure_fin',duree = '$minutes_total' where log_cdn = $id_log");
  $efface_doublon = mysql_query ("DELETE from log where login = '$login' AND date_debut = '$date_deb'  AND heure_debut = '$heure_debut' AND serveur = '$ChampServeur' AND log_cdn != $id_log");
  $efface = mysql_query ("DELETE FROM `log` WHERE heure_fin < heure_debut AND date_debut = date_fin");
  if (!isset($bouton) || (isset($bouton) && $bouton != 1))
        echo "<CENTER><Font color = '#FFFFFF' size='2'>$message</FONT></CENTER>";
}
if (!isset($bouton) || (isset($bouton) && $bouton != 1))
     echo "</TABLE></TD></TR></TABLE>";
$agent=$_SERVER['HTTP_USER_AGENT'];
if ($ecran == 2)
{
   $multi = $premulti;
   if ($multi != 1)
   {
      if (!isset($_SESSION)) session_start();
      $_SESSION['logout'] = true;
   }
   if (isset($_SESSION))
       destroySession();
   echo "<SCRIPT language=javascript>
          setTimeout(\"Quit()\",500);
          function Quit() {";
             echo "top.parent.opener=null;top.parent.close();return false;";
    echo "}";
   echo "</SCRIPT></BODY></HTML>";

}
else
{
    $multi = $premulti;
    $leretour='';
      if ((isset($_SESSION['bAuthMode']) || isset($_SESSION['login_Shib'])) && $multi != 1)
      {
         if (!isset($_SESSION)) session_start();
         $_SESSION['logout'] = true;
         $leretour = (isset($_SESSION['login_Shib'])) ? "?retour=1" : $leretour = "" ;
      }
   if (!empty($_SESSION))
      destroySession();
   echo "<SCRIPT language=javascript>";
   if (strstr($agent,"MSIE") && !isset($relance) && $multi != 1)
   {
      echo "top.parent.location.replace(\"index.php$leretour\")";
   }
   elseif (strstr($agent,"MSIE") && !isset($relance) && $multi == 1)
   {
     echo "setTimeout(\"Quit()\",500);
        function Quit() {";
           echo "top.opener=null;top.close();return false;";
        echo "}";
   }
   elseif (strstr($agent,"MSIE") && isset($relance))
   {
     echo "setTimeout(\"Quit()\",500);
        function Quit() {";
           echo "top.opener=null;top.close();return false;";
        echo "}";
   }
   elseif (!strstr($agent,"MSIE") && $relance == 1)
   {
     echo "setTimeout(\"Quit()\",500);
        function Quit() {";
           echo "top.opener=null;top.close();return false;";
        echo "}";
   }elseif(!strstr($agent,"MSIE") && $relance == 0 && $multi != 1)
   {
      echo "setTimeout(\"Quit()\",500);
        function Quit() {";
                 echo "parent.parent.location.replace(\"index.php$leretour\");";
        echo "}";
  }
   elseif(!strstr($agent,"MSIE") && $relance == 0 && $multi == 1)
  {
     echo "setTimeout(\"Quit()\",500);
        function Quit() {";
           echo "top.opener=null;top.close();return false;";
        echo "}";
   }
   echo "</SCRIPT></BODY></HTML>";

}
function destroySession()
{
  if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
  }
  $_SESSION = array();
}
?>