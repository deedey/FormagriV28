<?php
if (!isset($_SESSION)) session_start();
if (!isset($_SESSION['lg']) &&  !isset($_SESSION['id_user']))
{
  exit();
}
require "../admin.inc.php";
require "../fonction.inc.php";
require "../fonction_html.inc.php";
require "../lang$lg.inc.php";
dbconnect();
include "../style.inc.php";
/*
  développé par http://Phorum.org et modifié par dey.bendifallah@educagri.fr
  ****************************************** ****************
  *                                                         *
  * Copyright  formagri/cnerta/eduter/enesad                *
  * Dey Bendifallah                                         *
  * Les modules "archivage, lu et non lu, recherche simple  *                                       *
  * et certaines autres fonctionnalités ont été ajoutées.   *
  * Ce script fait partie intégrante du LMS Formagri.       *
  * Il peut être modifié ou utilisé à d'autres fins.        *
  * Il est libre et sous licence GPL                        *
  * Les auteurs n'apportent aucune garantie                 *
  *                                                         *
  ***********************************************************
*/
echo '
<script type="text/javascript">
   if (window.location.href.indexOf("endpoint=") != -1)
   {
           var rien = true;
   }else{
         var UrlParent = document.referrer;
         ClauseReferrer = document.referrer.split("endpoint=").slice(1).join("endpoint=");
         var leparent = (UrlParent.indexOf("?") != -1) ? ClauseReferrer : UrlParent.search;
         document.location.replace(window.location.href + "&endpoint=" + leparent);
   }
</script>';

echo "<script type='text/javascript' src='".$monURI."/lib/TinCanGeneric/scripts/TinCanJS/build/tincan-min.js'></script>";
echo "<CENTER><P><TABLE bgColor='#298CA0' cellspacing='2' cellpadding='0' width='98%' ><TR><TD width='100%'>";
echo "<TABLE bgColor='#FFFFFF' cellspacing='1' cellpadding='0'  width='100%'>";
echo "<TR><TD background=\"$adresse_http/images/fond_titre_table.jpg\" height='36' align='center' valign='center'><Font size='3' color='#FFFFFF'><B>";
if ($num > 5)
{
   if ($typ_user == "RESPONSABLE_FORMATION" || $typ_user == "ADMINISTRATEUR")
   {
      $id_grp = GetDataField ($connect,"select grp_cdn from groupe where grp_nom_lb = \"$ForumName\"","grp_cdn");
      $resp_grp = GetDataField ($connect,"select grp_resp_no from groupe where grp_cdn = $id_grp","grp_resp_no");
      if ($id_user == $resp_grp || $typ_user == "ADMINISTRATEUR")
      {
         $forum_table="groupe".$id_grp;
         $compte_forum = mysql_query("SELECT count(id) from $forum_table");
         $compte_post = mysql_result($compte_forum,0);
         $lien="admin/index.php?page=easyadmin&num=$f&arrive=$arrive";
         $lien1="admin/index.php?page=list&num=$f&arrive=$arrive";
      }
   }
}
elseif($num <= 5 && $typ_user == "ADMINISTRATEUR")
{
   $lien="admin/index.php?page=easyadmin&num=$num&arrive=$arrive";
}
if (isset($_GET['f']) && $_GET['f'] > 0)
{
   $nom_forum = GetDataField ($connect,"select table_name from forums where id = $f","table_name");
   $nb_fil = mysql_result(mysql_query("SELECT count(*) FROM $nom_forum"),0);
   if ($nb_fil > 0 && ($typ_user == 'ADMINISTRATEUR' || $typ_user == 'RESPONSABLE_FORMATION'))
      $ajt = "<a href=\"../archives/fil_forum.php?f=$f&t=\" class='bouton_new'><font size='2'>$mess_Farchv</font></A>";
   else
      $ajt = "";
}
if (strstr($_SERVER["SCRIPT_NAME"],'read.php'))
{
   $verbe ="experienced";
   $reqPost = mysql_query("select body,attachment from $nom_forum"."_bodies,$nom_forum WHERE $nom_forum"."_bodies.id = '".$_GET['i'].
                          "' and $nom_forum.id = '".$_GET['i']."'");
   if (mysql_num_rows($reqPost) == 1)
   {
      $bodPost = mysql_result($reqPost,0,'body');
      $Attachements = mysql_result($reqPost,0,'attachment');
      if ($Attachements != '' && (strstr(strtolower($Attachements),'jpg') ||
          strstr(strtolower($Attachements),'jpeg') ||
          strstr(strtolower($Attachements),'gif') || strstr(strtolower($Attachements),'png')))
      {
         $SuiteAttachements = ' <br />avec un fichier joint : <a href=\''.$adresse_http.'/ressources/forums/'.
              $nom_forum.'/'.$Attachements.'\' target=\'_blank\' ><img src=\''.$adresse_http.'/ressources/forums/'.
              $nom_forum.'/'.$Attachements.'\' width=150 border=0></a></HTML>';
         $bodPost = str_replace('</HTML>','',$bodPost);
      }
      elseif ($Attachements != '' && (!strstr(strtolower($Attachements),'jpg') &&
          !strstr(strtolower($Attachements),'jpeg') &&
          !strstr(strtolower($Attachements),'gif') && !strstr(strtolower($Attachements),'png')))
      {

         $SuiteAttachements = ' <br /><a href=\''.$adresse_http.'/ressources/forums/'.
              $nom_forum.'/'.$Attachements.'\' target=\'_blank\' >avec un fichier joint</a></HTML>';
         $bodPost = str_replace('</HTML>','',$bodPost);
      }
      else
         $SuiteAttachements = '';

   }
}
else
{
   if (isset($_GET['Poster']))
   {
      $verbe ="commented";
      $TabPost = explode('|||',$_GET['Poster']);
      $bodPost = str_replace('</HTML>','',urldecode($TabPost[1]));
      if ($TabPost[2] != '--')
      {
         $SuiteAttachements = ' <br />avec un fichier joint : <a href=\''.$TabPost[2].'\' target=\'_blank\' ><img src=\''.$TabPost[2].'\' width=150 border=0></a></HTML>';
      }
      else
         $SuiteAttachements = '';
   }
   else
   {
      $verbe ="experienced";
      $bodPost = 'Fil de discussion principal';
      $SuiteAttachements = '';
   }
}
//echo $_GET['Poster']. '------'.$SuiteAttachements;
echo '<script type="text/javascript">
  FormagriExample = {};
  var getTitre = " '.$RoleUser.' : " + document.getElementById("titreForum").getAttribute("content");
  FormagriExample.CourseActivity = {
    id: "http://formagri.com/forum",
    definition: {
        type: "http://adlnet.gov/expapi/activities/course",
        name: {
            "fr-FR": "formagri.com/forum - Tin Can Course"
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
       id: "forum : " + getTitre,
       definition: {
          name: {
             "fr-FR": "forum : " + getTitre
          },
          description: {
             "fr-FR":  "'.addslashes(html_entity_decode($bodPost,ENT_QUOTES,'iso-8859-1')).$SuiteAttachements.'"
          }
       }
    }
  }
  );

  tincan.sendStatement(
            {
                verb: "'.$verbe.'",
                context: FormagriExample.getContext(
                    FormagriExample.CourseActivity.id
                )
            },
            function () {}
  );
</script>';
if ($arrive == "")
{
   unset($_SESSION['chaine_act']);
   unset($_SESSION['forum_act']);
}
 if ($num > 5)
   echo "$mess_for_grp $ForumName";
 elseif ($num == 5)
  echo $ForumName;
 elseif ($num == 4)
  echo "$mess_menu_forum $mess_menu_forum_app";
 elseif ($num < 4 && $num > 0)
  echo "$mess_for_esp $ForumName";
 else
  echo $mess_menu_consult_forum;
echo "</B></FONT></TD></TR>";

if ((!isset($arrive) || (isset($arrive) && $arrive !='activite')) &&
   (($num > 5 && $compte_post > 0 && $forum_act == "" && $typ_user == "RESPONSABLE_FORMATION") ||
   ($typ_user == "ADMINISTRATEUR" && $num > 0)))
{
  echo "<FORM name='form' action=\"list.php?f=$num&collapse=$collapse&arrive=$arrive\" method=\"POST\">";
  echo "<TR><TD align=left valign='center' colspan='2'><TABLE width='100%' border='0'>";
  echo "<TR><TD style=\"padding-left: 4px; font-size: 12px;\" valign='center'><div id='one' style=\"float:left;padding-right:10px;\">";
  echo "<A href=\"$lien\" class='bouton_new' title=\"$mess_mod_forum\">".
       "<font size='2'>$mess_mod_ceforum</font></A></div><div id='two' style=\"float:left;\">$ajt</div></td>";
  echo "</TR></FORM></TABLE></TD></TR><TR><TD align=center>";
}
else
  echo "<TR><TD align=center>";
?>
