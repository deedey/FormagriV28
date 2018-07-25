<?php
  if (!isset($_SESSION)) session_start();
  include "param.php";
  include "mysql.php";
  include "f_chat.php";
  Connectix();
if ($lg == "ru"){
  $code_langage = "ru";
  $charset = "Windows-1251";
  putenv("TZ=Europe/Moscow");
}elseif ($lg == "fr"){
  $code_langage = "fr";
  $charset = "iso-8859-1";
  putenv("TZ=Europe/Paris");
}elseif ($lg == "en"){
  $code_langage = "en";
  $charset = "iso-8859-1";
}
?>
<HTML>
<HEAD>
<META HTTP-EQUIV="Content-Type" content="text/html; charset=<?php echo $charset;?>">
<META HTTP-EQUIV="Content-Language" CONTENT="<?php echo $code_langage ;?>">
<META HTTP-EQUIV="Expires" CONTENT="Fri, Jan 01 1900 00:00:00 GMT">
<META NAME="ROBOTS" CONTENT="No Follow">
<META HTTP-EQUIV="Cache-Control" CONTENT="no-cache, must-revalidate">
<META HTTP-EQUIV="Pragma" CONTENT="no-cache">
<link REL='StyleSheet' TYPE='text/css' HREF='chat.css'>
<TITLE><?php print($param["title"]); ?></TITLE>
</HEAD>
<BODY>
<?php
  P4_Disconnect($user);
  $query="DELETE FROM ".$param["table_salle"]." WHERE user='$user'";
  $result=mysql_query($query);
  $query="DELETE FROM chatter where appelant ='$user'";
  $result=mysql_query($query);
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

echo "<script type='text/javascript' src='../lib/TinCanGeneric/scripts/TinCanJS/build/tincan-min.js'></script>";
echo '<script type="text/javascript">
  FormagriExample = {};
  FormagriExample.CourseActivity = {
    id: "http://formagri.com/Chat",
    definition: {
        type: "http://adlnet.gov/expapi/activities/course",
        name: {
            "fr-FR": "formagri.com/Chat - Tin Can Course"
        },
        description: {
            "fr-FR": "Sortie de la salle de Chat"
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
       id: "Chat : Sortie de la salle de Chat",
       definition: {
          name: {
             "fr-FR": "Chat en ligne : Sortie"
          },
          description: {
             "fr-FR":  "Sortie de la salle de Chat"
          }
       }
    }
  }
  );

  tincan.sendStatement(
            {
                verb: "interacted",
                context: FormagriExample.getContext(
                    FormagriExample.CourseActivity.id
                )
            },
            function () {}
  );
</script>';

?>
<SCRIPT language=javascript>
        setTimeout("Quit()",1500);
        function Quit() {
                top.close();
        }
</SCRIPT>
</BODY></HTML>
