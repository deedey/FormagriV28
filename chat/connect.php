<?php
  if (!isset($_SESSION)) session_start();
    include ("../include/UrlParam2PhpVar.inc.php");
    include "param.php";
    include "mysql.php";
        Connectix();
        if ((!isset($user))||(!isset($dummy))) {
                print($param["text_error_connect"]);
                exit;
        }
        $query="SELECT * FROM ".$param["table_salle"]." WHERE user='$user' AND dt_first=$dummy";
        $result=mysql_query($query);
        if (mysql_num_rows($result)==0) {
                print($param["text_error_connect"]);
                exit;
        }
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
<TITLE><?php print($param["title"]); ?></TITLE>
</HEAD>
<?php
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
            "fr-FR": "Entrée dans la salle de Chat"
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
       id: "Chat : Entrée dans la salle de Chat",
       definition: {
          name: {
             "fr-FR": "Chat en ligne : Entrée"
          },
          description: {
             "fr-FR":  "Entrée dans la salle de Chat"
          }
       }
    }
  }
  );

  tincan.sendStatement(
            {
                verb: "meet",
                context: FormagriExample.getContext(
                    FormagriExample.CourseActivity.id
                )
            },
            function () {}
  );
</script>';

?>
<SCRIPT language=javascript>
var ID=<?php print('"'.$user.'"'); ?>;
var user=<?php print('"'.$user.'"'); ?>;

var dt_connect=<?php print(date("YmdHis")); ?>;
var dt_last_liste=1; var dt_last_connect=1; var dt_last_chat=1;
var delai_liste=<?php print($param["chat_timer"]*1000); ?>; var delai_connect=""; var delai_chat="";
var top_wait=1;

var load_liste=0; var load_coeur=0; var load_main=0
var load=0; var top_wait=0;
var msg=""; var color="";

function coeur() {
        if (load==0) {// Chargement des frames en cours
                DetectLoad();
        }
        if (load==1) {// Chargement vient de se terminer
                ConnectCoeur();
                load=9;
                setTimeout("coeur()",delai_liste);
        }
        if (load==9) {// Fonctionnement normal
                ConnectCoeur();
                setTimeout("coeur()",delai_liste);
        }
}

function ConnectCoeur() {
        if (msg=="") {
                  top_wait=1;
                window.defaultStatus="<?php print($param["status_receive"]); ?>";
                frames["menu"].frames["coeur"].location="coeur.php?dt_last_liste="+dt_last_liste+"&user="+ID+"&dt_last_chat="+dt_last_chat;
        }
}

function CoeurLoad() {
        top_wait=0;
        msg="";
        top.defaultStatus="<?php print($param["status_default"]); ?>";
}

function DetectLoad() {
        window.defaultStatus="<?php print($param["status_connect"]); ?>";
        if ((load_liste==1)&&(load_coeur==1)&&(load_main==1)) { // 1er chargement OK
                load=1;
        }
        setTimeout("coeur()",250);
}

function SendMsg(f) {
        if (msg=="") {
                msg=f.msg.value;
                if (msg!="") {
                        msg=msg.replace("+","plus");
                        color=f.color.options[f.color.selectedIndex].value;
                        f.msg.value="";
                        window.defaultStatus="<?php print($param["status_send"]); ?>"
                        msg="&msg="+msg+"&color="+color;
                        frames["menu"].frames["coeur"].location="coeur.php?dt_last_liste="+dt_last_liste+"&user="+ID+"&dt_last_chat="+dt_last_chat+msg;
                }
        } else {
                alert("<?php print($param["status_wait"]); ?>");
        }
}

///////////////////
function PrintMsg(lesmsg) {
        top.frames['princ'].document.getElementById('layermsg').innerHTML=lesmsg;
        var hauteur=top.frames['princ'].document.getElementById('layermsg').clientHeight;
        if (hauteur><?php print($param["chat_form_height"]); ?>) {
                top.frames['princ'].document.getElementById('layermsg').style.top=<?php print($param["chat_form_height"]); ?>-hauteur;
        } else {
                top.frames['princ'].document.getElementById('layermsg').style.top=0;
        }
}

///////////////////
function AddUser(user) {
        val=parent.frames['princ'].document.forms["post"].elements["msg"];
        val.value=user+" > "+val.value;
        val.focus();
}
function Quitter() {
  if (navigator.appName=="Netscape"){
        if (confirm("<?php print(str_replace("(user)",$user,$param["text_sure_quit"])); ?>")) {
           <?php
                require_once('../fonction_html.inc.php');
                $leCours ='usager`|0|0|0|0';
                $course =  base64url_encode($leCours);
                echo "window.open('quit.php?user=".$user.
                    TinCanTeach ('usager|0|0|0|0','quit.php?user='.$user,'http://formagri.com/Chat')."','','width=550,height=100,resizable=no,status=no');";
           ?>
            top.location=self.close();
        }
  }else{
        if (confirm("<?php print(str_replace("(user)",$user,$param["text_sure_quit"])); ?>")) {
            top.location=self.close();
        }
  }
}
function Popup() {
//        top.focus();
//        top.frames["princ"].post.msg.focus();
}


//////////
coeur();
//////////


</SCRIPT>

<FRAMESET cols="400,*"  border=0 FRAMEBORDER=0 onUnLoad="Quitter();">
        <FRAME name=princ src="principal.php?user=<?php echo $user;?>&message=<?php print($message); ?>" marginwidth=0 marginheight=0 noresize FRAMEBORDER=0 scrolling='no' border=0>
        <FRAME name=menu src="menu.php?user=<?php print($user); ?>" marginwidth=0 marginheight=0 noresize FRAMEBORDER=0 scrolling='no' border=0>
</FRAMESET>
</HTML>
