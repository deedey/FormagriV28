<?php
if (!isset($_SESSION)) session_start();
include('lib/configData.php');
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta name="Language" content="french">
<meta HTTP-EQUIV="Content-Language" CONTENT="FR-fr">
<META NAME="ROBOTS" CONTENT="No Follow">
<META HTTP-EQUIV="Cache-Control" CONTENT="no-cache, must-revalidate">
<META HTTP-EQUIV="Pragma" CONTENT="no-cache">
<title>@Formagri sur Twitter</title>
<link rel="shortcut icon" type="image/x-icon" href="assets/favicon.ico" />
<link rel="stylesheet" type="text/css" href="assets/tweet.css" media="screen" />
<script type="text/javascript" src="assets/jquery-1.11.3.min.js"></script>
<script type="text/javascript" src="<?php echo $_SESSION['monURI'];?>/lib/TinCanGeneric/scripts/TinCanJS/build/tincan-min.js"></script>
<script type="text/javascript" src="assets/tweet.js"></script>
</head>
<body onload="InitializeTimerTweet()">
<script type="text/javascript">
    function loadData(){<?php echo 'jQ_AjaxMsg("lib/appel.php?lance=charge&count='.$NbT.'&Seq='.$Seq.'");'; ?>}
</script>
<?php
function modif_az2qw($fichier_test)
{
    $fichier_test = strtr($fichier_test,
    'ÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ-_',
    'AAAAAACEEEEIIIIOOOOOUUUUYaaaaaaceeeeiiiioooooouuuuyy  ');
  return $fichier_test;
}
$Activity = modif_az2qw(urldecode($_GET['activity_id']));
if (urldecode($_GET['activity_id']) == '/ApiTweet/')
   $verbe ="experienced";
elseif (strstr(urldecode($_GET['activity_id']),'|Tw|'))
   $verbe ="interacted";
?>
<script type="text/javascript">

FormagriExample = {};
FormagriExample.CourseActivity = {
    id: "http://formagri.com/ApiTweet",
    definition: {
        type: "http://adlnet.gov/expapi/activities/course",
        name: {
            "fr-FR": "formagri.com/ApiTweet - Tin Can Course"
        },
        description: {
            "fr-FR": "formagri.com/ApiTweet"
        }
    }
};
//+ "<?php echo $Activity;?>",

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
       id: "ApiTweet : pour le Hashtag : " + "<?php echo $Seq;?>",
       definition: {
          name: {
             "fr-FR": "ApiTweet : " + "<?php echo $Activity;?>",
          },
          description: {
             "fr-FR":  "ApiTweet : " + "<?php echo $Activity;?>"
          }
       }
    }
  }
);

tincan.sendStatement(
            {
                verb: "<?php echo $verbe;?>",
                context: FormagriExample.getContext(
                    FormagriExample.CourseActivity.id
                )
            },
            function () {}
);
</script>

<div class="boxarea">
<div id="insertionTwit"></div>
<br />
<span class="formmesssage"></span>
<div id="appendIt" class="happen-text">Si vous voulez donner un avis.. </div>
<img src="assets/twitter.gif" class="tweetImg" />
<div class="one-fourty" id="numberofWord">100</div>
<div class="clr"></div>
<div id="#formmesssage"></div>
<form id="formSubmit" action="javascript:void(0);">
<input type="hidden" name="qui" id="Qui" value="<?php echo $Qui; ?>" />
<input type="hidden" name="Seq" id="Seq" value="<?php echo $Seq; ?>" />
<textarea name="twitter_status" cols="" rows="3" id="PostArea" class="area" onkeyup="CountlimitChars()" onFocus="this.value ='';">Votre message de moins de 101 signes ici</textarea>
<div class="update-button"><input type="button" value="Twitter" id="submitbutton"  /></div>
</form>
</div>
</body>
</html>
