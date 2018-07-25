<?php
if (!isset($_SESSION)) session_start();
include ("../include/UrlParam2PhpVar.inc.php");
if (!isset($_SESSION['id_user']) || $_SESSION['id_user'] == "")
{
  exit();
}
require "../admin.inc.php";
require '../fonction.inc.php';
require "../lang$lg.inc.php";
require "../fonction_html.inc.php";
require "../langues/formation.inc.php";
require "../langues/module.inc.php";
dbConnect();
if (!isset($_SESSION['id_user']) || $_SESSION['id_user'] == "")
{
  exit();
}
if (isset($lg)){
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
}
if (!strstr($_SERVER['REQUEST_URI'],'/wiki'))
{
      unset($_SESSION['DroitsWiki']);
}

?>

<HTML>
<HEAD>
<!Quirks mode -->
     <meta http-equiv = "X-UA-Compatible" content = "IE=8" />
<META HTTP-EQUIV="Content-Type" content="text/html; charset=<?php  echo $charset;?>">
<META HTTP-EQUIV="Content-Language" CONTENT="<?php  echo $code_langage ;?>">
<META HTTP-EQUIV="Expires" CONTENT="Fri, Jan 01 1900 00:00:00 GMT">
<META NAME="ROBOTS" CONTENT="No Follow">
<META HTTP-EQUIV="Cache-Control" CONTENT="no-cache, must-revalidate">
<META HTTP-EQUIV="Pragma" CONTENT="no-cache">
<TITLE>Formagri</TITLE>
<link rel="stylesheet" type="text/css" href="<?php  echo $monURI;?>/wiki/css/ppgallery.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo $monURI;?>/general.css" />
<link rel="stylesheet" type="text/css" href="<?php echo $monURI;?>/admin/style_admin.css" />
<link rel="stylesheet" type="text/css" href="<?php echo $monURI;?>/OutilsJs/style_jquery.css" />
<script type="text/javascript" src="<?php  echo $monURI;?>/OutilsJs/jquery-144.js"></script>
<script type="text/javascript" src="<?php echo $monURI;?>/OutilsJs/jquery.tooltip.pack.js"></script>
<script type="text/javascript" src="<?php  echo $monURI;?>/wiki/js/ppgallery.js"></script>
<script type="text/javascript">
$(document).ready(function() {
        $('#gallery').ppGallery({
                screenFade: 0.8, //fade screen level. default 0.5
                screenColor: '#000000', //choose color of background. default black
                showTitle: 1, //toggles to show the title. default 1 (1= yes, 0= no)
                thumbWidth: 54, //control the gallery thumbnail size. default 60(pixels)
                thumbHeight: 40, //control the gallery thumbnail size. default 40(pixels)
                maxWidth: '550', //control max width of large image and thumbnail box. leave blank for no restrictions
                slideShowDelay: '2' //control the slideshow interval. defaults at 3 seconds
        });
        $("a").tooltip({showURL: false});
        $("div").tooltip({showURL: false});
        $("span").tooltip({showURL: false});
        $("li").tooltip({showURL: false});
        $("input").tooltip({showURL: false});
        $("select").tooltip({showURL: false});
        $("img").tooltip({showURL: false});

});
</script>
</head>
<body>
<?php
$content = '';
$reqImg = mysql_query("select * from wikimg group by wkimg_content_blb");
$NbImg = mysql_num_rows($reqImg);
$content .= '<div class="SOUS_TITRE" style="position:absolute;z-index:2;left:5px;top:5px;margin-right:10px;">';
$content .= 'Cliquez sur une image pour la voir en taille réelle la galerie d\'images ';
$content .= 'ou cliquez sur "<b><u>Choisissez</u></b>" pour en choisir une sans la visionner en taille réelle dans la galerie'.
            ' (*  pour IE version inférieure à 8 )</div>';
$content .= '<ul id="gallery">';

while ($oImg = mysql_fetch_object($reqImg))
{
   $NumPrg = str_replace('Image','',$_GET['idImage']);
   $content .= '<li><a href="lib/affiche_image.php?provenance=paragraphe&numImg='.$oImg->wkimg_cdn.'&'.$NumPrg.'&'.$oImg->wkimg_cdn.'" style="margin-bottom:4px;">'.
              '<img src=lib/affiche_image.php?provenance=paragraphe&numImg='.$oImg->wkimg_cdn.' border="0" width="60" height="40"></a>';
   $content .= '<br /><span id="bout_msg" style="margin-top:4px;" onClick="'.
               'parent.$(\'#'.$_GET['idImage'].'\').html(\'<img src=lib/affiche_image.php?provenance=paragraphe&numImg='.$oImg->wkimg_cdn.'>\');'.
               'parent.$(\'#'.$_GET['idFile'].'\').html(\'<input type=hidden name=id_wkimg value='.$oImg->wkimg_cdn.' />\');'.
               'parent.$(\'#'.$_GET['Zero'].'\').html(\'<input type=hidden name=userfile value=0 />\');'.
               'parent.$(\'input[id='.$_GET['fichier'].']\').empty();'.
               'top.tb_remove();">Choisissez</span></li>';
}
$content .= '</ul><div style="clear:both;"></div></body></html>';
echo $content;
?>