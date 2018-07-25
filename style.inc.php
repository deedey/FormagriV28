<?php

if (!isset($_SESSION['id_user']) || $_SESSION['id_user'] == "")
{
  exit();
}
ini_set('error_reporting','E_ALL');
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
$aSuperGlobal = array ('_SESSION','_GET','_POST');
foreach ($aSuperGlobal as $superGlobal)
{
       foreach ($GLOBALS[$superGlobal] as $key => $superGlobalVal)
       {
               $$key = $superGlobalVal;
       }
}
include ('include/varGlobals.inc.php');

/*
echo "<pre>";
     print_r($_POST);print_r($_GET);
echo "</pre>";
*/
$currentUser = nomUser($_SESSION['id_user']);
switch ($_SESSION['typ_user'])
{
   case 'ADMINISTRATEUR' : $RoleUser = "Adm";break;
   case 'APPRENANT' : $RoleUser = "App";break;
   case 'RESPONSABLE_FORMATION' : $RoleUser = "RF";break;
   case 'FORMATEUR_REFERENT' : $RoleUser = "FR";break;
   case 'TUTEUR' : $RoleUser = "Tut";break;
}

$ContentHead = '';
if (isset($_SERVER['HTTP_REFERER']) && !strstr($_SERVER['HTTP_REFERER'],$adresse_http))
{
   echo "<script language=\"JavaScript\">";
   echo "document.location.replace(\"$adresse_http/index.php\")";
   echo "</script>";
   exit();
}
if (!isset($arrive) || $arrive == ""){
   unset($_SESSION['chaine_act']);
   unset($_SESSION['forum_act']);
}
if (!strstr($_SERVER['REQUEST_URI'],'recherche.php'))
{
   unset($_SESSION['org']);
   unset($_SESSION['getVarsRech']);
}
if (!strstr($_SERVER['REQUEST_URI'],'/wiki') && !strstr($_SERVER['REQUEST_URI'],'/mindmap'))
{
      unset($_SESSION['DroitsWiki']);
}

$bkg = GetDataField($connect,"select param_etat_lb from param_foad where param_typ_lb = 'bkg'","param_etat_lb");
/*
if (ini_get('auto_prepend_file') == '')
   ini_set('auto_prepend_file',$_SERVER['DOCUMENT_ROOT'].$_SESSION['monURI'].'/prepend.php');
ini_set('safe_mode',0);
ini_set('short_open_tag',1);
*/
$ContentHead .= '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
                <html>
                <HEAD>
                <!Quirks mode -->
                <META HTTP-EQUIV="X-UA-Compatible" content="IE=8" />
                <META HTTP-EQUIV="Content-Type" content="text/html; charset='.$charset.'">
                <META HTTP-EQUIV="Content-Language" CONTENT="'.$code_langage.'">
                <META HTTP-EQUIV="Expires" CONTENT="Fri, Jan 01 1900 00:00:00 GMT">
                <META NAME="ROBOTS" CONTENT="No Follow">
                <META HTTP-EQUIV="Cache-Control" CONTENT="no-cache, must-revalidate">
                <META HTTP-EQUIV="Pragma" CONTENT="no-cache">';

if (isset($_GET['f']) && strstr($_SERVER['REQUEST_URI'],'forum/') && $_GET['f'] > 0)
{
   $nom_forum = GetDataField ($connect,"select table_name from forums where id = ".$_GET['f'],"table_name");
   $name_forum = GetDataField ($connect,"select name from forums where id = $f","name");
   if (isset($_GET['t']) && $_GET['t'] > 0)
      $NomThread = '::Sujet::'.GetDataField ($connect,"select subject from $nom_forum where thread = '".$_GET['t']."' and parent = 0","subject");
   else
      $NomThread = '';
   $ContentHead .= '<div id="titreForum" content="Forum : '.$name_forum.$NomThread.'"></div>';
}
$ContentHead .= '<TITLE>Formagri :: '.str_replace('.educagri.fr','',str_replace('cfppa-','',str_replace('ef-','',$_SERVER['SERVER_NAME']))).
                  ' :: '.$RoleUser.' :: '.$currentUser.'</TITLE>';
$ContentHead .= '<link rel="shortcut icon" href="/images/icone.ico" type="image/x-icon" />
                <link rel="stylesheet" type="text/css" href="'.$monURI.'/general.css" />
                <link rel="stylesheet" type="text/css" href="'.$monURI.'/admin/style_admin.css" />
                <link rel="stylesheet" type="text/css" href="'.$monURI.'/Ldap2Mysql/css/ldap2Mysql.css" />
                <link rel="stylesheet" type="text/css" href="'.$monURI.'/OutilsJs/style_jquery.css" />
                <link rel="stylesheet" type="text/css" href="'.$monURI.'/lib/css/box.css"/>
                <link rel="stylesheet" type="text/css" href="'.$monURI.'/OutilsJs/lib/simplePagination.css"/>';
$style_devoirs = GetDataField($connect,"select param_etat_lb from param_foad where param_typ_lb='style_devoirs'","param_etat_lb");
$ContentHead .= '<link rel="stylesheet" type="text/css" href="'.$monURI.'/ressources/'.$style_devoirs.'.css" />';
if (strstr($_SERVER['REQUEST_URI'],'/wiki/wiki')  && !strstr($_SERVER['REQUEST_URI'],'/wiki/wikiGalerie'))
{
   $ContentHead .= '<link rel="stylesheet" type="text/css" href="'.$monURI.'/class/rateit/src/rateit.css" />';
   $ContentHead .= '<link rel="stylesheet" type="text/css" href="'.$monURI.'/wiki/css/wiki.css"/>';
   $ContentHead .= '<link rel="stylesheet" type="text/css" href="'.$monURI.'/wiki/css/menu_vert.css"/>';
   $ContentHead .= '<link rel="stylesheet" type="text/css" href="'.$monURI.'/wiki/css/menu_compare.css"/>';
   $ContentHead .= '<script type="text/javascript" src="'.$monURI.'/class/rateit/src/jquery.min.js"></script>';
   $ContentHead .= '<script type="text/javascript" src="'.$monURI.'/OutilsJs/box.js"></script>';
   $ContentHead .= '<script type="text/javascript" src="'.$monURI.'/class/rateit/src/jquery.rateit.min.js"></script>';
}
elseif (strstr($_SERVER['REQUEST_URI'],'/blog/blog') && !strstr($_SERVER['REQUEST_URI'],'/blog/blogGalerie'))
{
   $ContentHead .= '<link rel="stylesheet" type="text/css" href="'.$monURI.'/class/rateit/src/rateit.css" />';
   $ContentHead .= '<link rel="stylesheet" type="text/css" href="'.$monURI.'/blog/css/blog.css"/>';
   $ContentHead .= '<link rel="stylesheet" type="text/css" href="'.$monURI.'/blog/css/menu_vert.css"/>';
   $ContentHead .= '<link rel="stylesheet" type="text/css" href="'.$monURI.'/blog/css/menu_compare.css"/>';
   $ContentHead .= '<script type="text/javascript" src="'.$monURI.'/class/rateit/src/jquery.min.js"></script>';
   $ContentHead .= '<script type="text/javascript" src="'.$monURI.'/OutilsJs/box.js"></script>';
   $ContentHead .= '<script type="text/javascript" src="'.$monURI.'/class/rateit/src/jquery.rateit.min.js"></script>';
}
elseif(strstr($_SERVER['REQUEST_URI'],'/wiki/wikiGalerie'))
{
   $ContentHead .= '<link rel="stylesheet" type="text/css" href="'.$monURI.'/wiki/js/theatre/theatre.css"/>';
   $ContentHead .= '<script type="text/javascript" src="'.$monURI.'/OutilsJs/jquery-144.js"></script>';
   $ContentHead .= '<script type="text/javascript" src="'.$monURI.'/wiki/js/theatre/jquery.theatre-1.0.js"></script>';
}
elseif (strstr($_SERVER['REQUEST_URI'],'/blog/blogGalerie') )
{
   $ContentHead .= '<link rel="stylesheet" type="text/css" href="'.$monURI.'/blog/js/theatre/theatre.css"/>';
   $ContentHead .= '<script type="text/javascript" src="'.$monURI.'/OutilsJs/jquery-144.js"></script>';
   $ContentHead .= '<script type="text/javascript" src="'.$monURI.'/blog/js/theatre/jquery.theatre-1.0.js"></script>';
}
else
{
      $ContentHead .= '<link rel="stylesheet" type="text/css" href="'.$monURI.'/blog/css/menu_vert.css"/>';
      $ContentHead .= '<link rel="stylesheet" type="text/css" href="'.$monURI.'/wiki/css/menu_compare.css"/>';
      $ContentHead .= '<script type="text/javascript" src="'.$monURI.'/OutilsJs/jquery-142.js"></script>';
      $ContentHead .= '<script type="text/javascript" src="'.$monURI.'/OutilsJs/box.js"></script>';
}
if (strstr($_SERVER['REQUEST_URI'],'/wiki/wiki') || strstr($_SERVER['REQUEST_URI'],'/blog/blog'))
    $ContentHead .= '<script type="text/javascript" src="'.$monURI.'/OutilsJs/zeditable.js"></script>';
$ContentHead .= '<script type="text/javascript" src="'.$monURI.'/general.js"></script>
                <script type="text/javascript" src="'.$monURI.'/OutilsJs/lib/ajax/ajax_cms.js"></script>
                <script type="text/javascript" src="'.$monURI.'/OutilsJs/lib/interface.js"></script>
                <script type="text/javascript" src="'.$monURI.'/OutilsJs/calendar.js"></script>
                <script type="text/javascript" src="'.$monURI.'/OutilsJs/jquery.autocomplete.js"></script>
                <script type="text/javascript" src="'.$monURI.'/OutilsJs/lib/jq_autocomplete.js"></script>
                <script type="text/javascript" src="'.$monURI.'/OutilsJs/lib/jquery.bgiframe.min.js"></script>
                <script type="text/javascript" src="'.$monURI.'/OutilsJs/lib/dimensions.js"></script>
                <script type="text/javascript" src="'.$monURI.'/OutilsJs/LiveValidation.js"></script>
                <script type="text/javascript" src="'.$monURI.'/OutilsJs/jquery.listpopulator.js"></script>
                <script type="text/javascript" src="'.$monURI.'/OutilsJs/lib/jquery.livequery.js"></script>
                <script type="text/javascript" src="'.$monURI.'/OutilsJs/lib/jquery.simplePagination.js"></script>
                <script type="text/javascript" src="'.$monURI.'/OutilsJs/Alert2/alert2.js"></script>
                <script type="text/javascript" src="'.$monURI.'/OutilsJs/DivPopulator/jquery.divpopulator.js" ></script>
                <script type="text/javascript" src="'.$monURI.'/OutilsJs/jquery.tooltip.pack.js"></script>';
//if ($sock = @fsockopen('www.google.fr', 80, $number01, $error, 5))
   $ContentHead .= "\n<script type='text/javascript'>
                    var _gaq=_gaq||[];var pluginUrl = '//www.google-analytics.com/plugins/ga/inpage_linkid.js';_gaq.push(['_require', 'inpage_linkid', pluginUrl]);_gaq.push(['_setAccount','UA-42020054-1']);_gaq.push(['_trackPageview']);_gaq.push(['_setCustomVar', 1, window.location.host, window.location.host, 1]);_gaq.push(['_setDomainName','none']);(function(){var ga=document.createElement('script');ga.type='text/javascript';ga.async=true;ga.src=('https:'==document.location.protocol?'https://ssl':'http://www')+'.google-analytics.com/ga.js';var s=document.getElementsByTagName('script')[0];s.parentNode.insertBefore(ga,s)})();
                    </script>";
if (strstr($_SERVER['REQUEST_URI'],'/wiki/wikiAjout.php') || strstr($_SERVER['REQUEST_URI'],'/blog/blog'))
    $ContentHead .= "\n".'<script type="text/javascript" src="'.$monURI.'/OutilsJs/zeditable.js"></script>';

$ContentHead .= '<script type="text/javascript" src="'.$monURI.'/OutilsJs/swfobject.js"></script>';
$ContentHead .= '<STYLE type="text/css">
                   .myTree{ list-style: none; margin-left : 20px;}
                   .myTree ul{ list-style: none; margin-left : 20px;}
                   .expandImage{margin-left: 3px;margin-right: 10px;}
                   .treeItem{list-style: none; margin-left : 5px;}
                </STYLE>';
$src = "$adresse_http/";
$ContentHead .= '<script  LANGUAGE="JavaScript1.2" SRC="'.$monURI.'/calendrier_fr.js"></script>
                <SCRIPT LANGUAGE="JavaScript">
                var agt=navigator.userAgent.toLowerCase();
                var is_win95 = ((agt.indexOf("win95")!=-1) || (agt.indexOf("windows 95")!=-1));
                var is_win16 = ((agt.indexOf("win16")!=-1) ||
               (agt.indexOf("16bit")!=-1) || (agt.indexOf("windows 3.1")!=-1) ||
               (agt.indexOf("windows 16-bit")!=-1) );
                var is_win31 = ((agt.indexOf("windows 3.1")!=-1) || (agt.indexOf("win16")!=-1) ||
                    (agt.indexOf("windows 16-bit")!=-1));
                var is_winme = ((agt.indexOf("win 9x 4.90")!=-1));
                var is_win2k = ((agt.indexOf("windows nt 5.0")!=-1));
                var is_winxp = ((agt.indexOf("windows nt 5.1")!=-1));
                var is_win98 = ((agt.indexOf("win98")!=-1) || (agt.indexOf("windows 98")!=-1));
                var is_winnt = ((agt.indexOf("winnt")!=-1) || (agt.indexOf("windows nt")!=-1));
                var is_win32 = (is_win95 || is_winnt || is_win98 ||
                    ((is_major >= 4) && (navigator.platform == "Win32")) ||
                    (agt.indexOf("win32")!=-1) || (agt.indexOf("32bit")!=-1));
                var is_os2   = ((agt.indexOf("os/2")!=-1) ||
                    (navigator.appVersion.indexOf("OS/2")!=-1) ||
                    (agt.indexOf("ibm-webexplorer")!=-1));
                </script>';

if (!strstr($_SERVER['REQUEST_URI'],'/wiki/wikiAjout.php'))
{
  $ContentHead .= '<script language="javascript" type="text/javascript" src="'.$monURI.'/tinymce/jscripts/tiny_mce/tiny_mce.js"></script>';
  $ContentHead .= '<script language="javascript">';
  if(strstr($_SERVER['REQUEST_URI'],'/wiki/wiki') || strstr($_SERVER['REQUEST_URI'],'/blog/blog') ||
            strstr($_SERVER['REQUEST_URI'],'/forum/') || strstr($_SERVER['REQUEST_URI'],'faq') ||
            strstr($_SERVER['REQUEST_URI'],'forum_module'))
  {
      $ContentHead .= 'tinyMCE.init({
           // General options
           mode : "textareas",
           theme : "advanced",
           language : "fr",
           force_br_newlines : true,
           force_p_newlines : false,
           forced_root_block : "",
           plugins : "style,layer,table,advhr,advimage,advlink,inlinepopups,preview,media,contextmenu,paste,directionality,xhtmlxtras",
           // Theme options   hr,removeformat,|,sub,sup,|,charmap
           theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,formatselect,fontsizeselect,|,cut,copy,paste,pastetext,pasteword",
           theme_advanced_buttons2 : "bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,|,preview,|,forecolor,backcolor,table",
           theme_advanced_buttons3 : "",
           theme_advanced_toolbar_location : "top",
           theme_advanced_toolbar_align : "left",
           width:730,height:140,
           template_external_list_url : "js/template_list.js",
           external_link_list_url : "js/link_list.js",
           external_image_list_url : "js/image_list.js",
           media_external_list_url : "js/media_list.js"
      });';
  }
  elseif(strstr($_SERVER['REQUEST_URI'],'/admin/msgInsc.php'))
  {
  $ContentHead .= 'tinyMCE.init({
        mode : "textareas",
        theme : "advanced",
        language : "fr",
        force_br_newlines : true,
        force_p_newlines : false,
        forced_root_block : "",
        plugins : "style,layer,table,advhr,advimage,advlink,inlinepopups,preview,media,contextmenu,paste,directionality,xhtmlxtras",
        theme_advanced_buttons1 : "bold,italic,underline",
        theme_advanced_buttons2 : "",
        theme_advanced_buttons3 : "",
        theme_advanced_toolbar_location : "top",
        theme_advanced_toolbar_align : "left",
        width:540,height:140
        });';
  }
  else
  {
  $ContentHead .= 'tinyMCE.init({
       mode : "textareas",
       theme : "advanced",
       language : "fr",
       force_br_newlines : true,
       force_p_newlines : false,
       forced_root_block : "",
       plugins : "style,layer,table,advhr,advimage,advlink,inlinepopups,preview,media,contextmenu,paste,directionality,xhtmlxtras",
       // Theme options   hr,removeformat,|,sub,sup,|,charmap
       theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,bullist,numlist,forecolor,backcolor,|,formatselect,|,paste,pastetext,pasteword",
       theme_advanced_buttons2 : "",
       theme_advanced_buttons3 : "",
       theme_advanced_toolbar_location : "top",
       theme_advanced_toolbar_align : "left",
       width:530,height:140,
       template_external_list_url : "js/template_list.js",
       external_link_list_url : "js/link_list.js",
       external_image_list_url : "js/image_list.js",
       media_external_list_url : "js/media_list.js"
    });';
  }
  $ContentHead .= '</script>'."\n";
}
$ContentHead .= '<script language="javascript">
                $(document).ready(function(){
                   $("a").tooltip({showURL: false});
                   $("div").tooltip({showURL: false});
                   $("span").tooltip({showURL: false});
                   $("li").tooltip({showURL: false});
                   $("input").tooltip({showURL: false});
                   setTimeout(function() {$("#mien").empty();},7000);
                });'."\n";
$ContentHead .= 'var msgconfprompt="'.$mess_admin_valid_modif.'";'."\n";
$ContentHead .= "function confprompt(url) {\n
                    $.prompt(msgconfprompt, { opacity: 0.5 , buttons: { Valider: true, Renoncer: false }}).corner();
                    mon_retour(false,url);
                    if (url != '' && true)
                       document.location.replace(url);
                }\n
                function mon_retour(ok,url){\n
                    var jok = true;
                    alert(jok+'  :  '+url);
                    return (jok,url);
                }"."\n";

$ContentHead .= 'var msgconfm = "'.$mess_admin_valid_modif.'";'."\n";
$ContentHead .= "function confm(url) {\n
                     ShowAlert2('JavaScript:document.location.replace(\"'+url+'\")',
                     'Confirmation',
                     '$mess_admin_valid_supp <br />$mess_op_irrev',
                     '$adresse_http/images/Exclamation.gif',
                     ['Confirmer', 'Annuler'],
                     ['JavaScript:HideAlert2(1,\"'+url+'\")','JavaScript:HideAlert2(2,\"'+url+'\")'],
                     400,
                     '$adresse_http/images/close.gif',
                     url
                     );
                 }\n";
$ContentHead .= "var msgconf1 = \"$mess_admin_valid_supp\";\n
                var msgconf2=\"$mess_op_irrev\";\n
                var msgconf = msgconf1 \n msgconf2;\n
                function conf() {\n
                   if ( confirm(msgconf) )\n
                      return(true);\n
                   return(false);\n
                }\n";
$ContentHead .= "var msgconfv = \"$mess_gen_val_sais\"\n;
                function confv() {\n
                    if ( confirm(msgconfv) )\n
                       return(true);\n
                    return(false);\n
                }\n";
$ContentHead .= "var msgconfdupli = \"$mess_conf_dupli\";\n
                function confd() {\n
                    if ( confirm(msgconfdupli) )\n
                        return(true);\n
                    return(false);\n
                }\n
                var msgconfseq = \"$mess_seq_presc\";\n
                function confseq() {\n
                   if ( confirm(msgconfseq) )\n
                       return(true);\n
                   return(false);\n
                }\n";
$ContentHead .= "function ReinitForum(forumID)
    {
                 \$.ajax({
                         type: \"GET\",
                         url: \"admin/modif_nb.php\",
                         data: \"groupage=1&suppForum=1&forumID=\"+forumID,
                         beforeSend:function()
                         {
                                                 \$(\"#affiche\").addClass(\"Status\");
                                                 \$(\"#affiche\").append(\"Opération en cours....\");
                         },
                         success: function(msg){
                                                   \$(\"#mien\").css(\"padding\",\"4px\");
                                                   \$(\"#mien\").show();
                                                   \$(\"#mien\").html(msg);
                                                   \$(\"#affiche\").empty();
                                                   \$(\"#ReinitForum\").hide();
                         }
                 });
                 setTimeout(function() {\$(\"#mien\").hide();},7000);
    }";
$ContentHead .= "</SCRIPT>\n
                 </HEAD>\n";
$nom_user = $_SESSION["name_user"];
$prenom_user = $_SESSION["prename_user"];
$image = GetDataField($connect,"select param_etat_lb from param_foad where param_cdn=1","param_etat_lb"); //
if (strstr($_SERVER["HTTP_USER_AGENT"],"MSIE"))
    $typ_agent = 'msie';
$venue = $_SERVER["SCRIPT_NAME"]."?".$_SERVER["QUERY_STRING"];
if(strstr($_SERVER['REQUEST_URI'],'message_instant.php'))
   $ContentHead .= '<BODY bgcolor="#F7F876" ';
else
   $ContentHead .= '<BODY bgcolor="'.$bkg.'" ';
if (strstr($venue,'annonce_grp.php?depart=1'))
   $ContentHead .= 'style="margin:0px;">';
else
   $ContentHead .= 'style="margin-top:12px;">';
$ContentHead .= "<a name = 'sommet'></a>";
if (strstr($venue,'agenda.php?tuteur='))
   $venue=1;
else
   $venue=0;
$bouton_gauche = "<table cellpadding='0' cellspacing='0' border=0><tbody>".
                 "<tr><td><img src=\"$adresse_http/images/complement/cg.gif\" border='0'></td>".
                 "<td background='$adresse_http/images/complement/milieu.gif' nowrap align='center'><div id='sequence'>";
$bouton_droite = "</div></td><td><img src=\"$adresse_http/images/complement/cd.gif\" border='0'></td></tr></tbody></table>";
$bouton_gauche1 = "<table cellpadding='0' cellspacing='0' border=0><tbody>".
                 "<tr><td><img src=\"$adresse_http/images/complement/cg.gif\" border='0'></td>".
                 "<td background='$adresse_http/images/complement/milieu.gif' nowrap align='center'>";
$bouton_droite1 = "</td><td><img src=\"$adresse_http/images/complement/cd.gif\" border='0'></td></tr></tbody></table>";
$ContentHead .= '<div id="overDiv" style="position:absolute; visibility:hidden;z-index:1000;"></div>
                <SCRIPT LANGUAGE="JavaScript" SRC="'.$adresse_http.'/overlib.js"><!-- overLIB (c) Erik Bosrup --></SCRIPT>';
echo $ContentHead;