<?php
if (!isset($_SESSION)) session_start();
header("Content-type  : text/plain");
header("Cache-Control: no-cache, max-age=0");
include ("../include/UrlParam2PhpVar.inc.php");
require '../admin.inc.php';
require '../fonction.inc.php';
require "../lang$lg.inc.php";
require "../langues/adm.inc.php";
require "../langues/module.inc.php";
require '../fonction_html.inc.php';
dbConnect();
if (isset($ressources) && $ressources == 1)
    $modif_pgs = mysql_query("UPDATE param_foad SET param_etat_lb='$nbr_pgs' WHERE param_typ_lb='nbr_pages_ress'");
elseif(isset($interface) && $interface == 1 && isset($item) && $item != '')
    $requete =  mysql_query("UPDATE param_foad SET param_etat_lb = '$nb_pages' WHERE param_typ_lb='$item'");
elseif(isset($interface) && $interface == 1 && isset($chge_mp) && $chge_mp == 1)
{
    $etat_mp = GetDataField($connect,"select param_etat_lb from param_foad where param_typ_lb='multi-centre'","param_etat_lb");
    $mp = ($etat_mp == 'NON') ? "OUI" : "NON";
    $updt_mp = mysql_query ("UPDATE param_foad SET param_etat_lb = '$mp' WHERE param_typ_lb='multi-centre'");
    if ($mp == "OUI")
       $mess_notif = $mp."M"."$msgadm_mc $msgadm_mc_act";
    else
       $mess_notif = $mp."M"."$msgadm_mc $msgadm_mc_desact";
}
elseif(isset($interface) && $interface == 1 && isset($chge_fav) && $chge_fav == 1)
{
    $etat_fav = GetDataField($connect,"select param_etat_lb from param_foad where param_typ_lb='favoris'","param_etat_lb");
    $fav = ($etat_fav == 'NON') ? "OUI" : "NON";
    $updt_fav = mysql_query ("UPDATE param_foad SET param_etat_lb = '$fav' WHERE param_typ_lb='favoris'");
    if ($fav == "OUI")
       $mess_notif = $fav."F"."$msgadm_fav $msgadm_mc_act";
    else
       $mess_notif = $fav."F"."$msgadm_fav $msgadm_mc_desact";
}
elseif(isset($interface) && $interface == 1 && isset($chge_seqduref) && $chge_seqduref == 1)
{
    $etat_seqduref = GetDataField($connect,"select param_etat_lb from param_foad where param_typ_lb='seqduref'","param_etat_lb");
    $seqduref = ($etat_seqduref == 'NON') ? "OUI" : "NON";
    $updt_seqduref = mysql_query ("UPDATE param_foad SET param_etat_lb = '$seqduref' WHERE param_typ_lb='seqduref'");
    if ($seqduref == "OUI")
       $mess_notif = "OUI";
    else
       $mess_notif = "NON";
}
elseif(isset($interface) && $interface == 1 && isset($chge_messInsc) && $chge_messInsc == 1)
{
    $etat_messInsc = GetDataField($connect,"select param_etat_lb from param_foad where param_typ_lb='mess_inscription'","param_etat_lb");
    $messInsc = ($etat_messInsc == 'NON') ? "OUI" : "NON";
    $updt_messInsc = mysql_query ("UPDATE param_foad SET param_etat_lb = '$messInsc' WHERE param_typ_lb='mess_inscription'");
    if ($messInsc == "OUI")
       $mess_notif = "OUI";
    else
       $mess_notif = "NON";
}
elseif(isset($interface) && $interface == 1 && isset($suppMulti) && $suppMulti == 1)
{
    $requete= mysql_query ("delete FROM user_centre WHERE uc_cdn = $IDcentre");
    $mess_notif = "Cet utilisateur ne peut plus se connecter a ce Centre";
    $requete= mysql_query ("select * FROM user_centre WHERE uc_iduser_no = $qui");
    if (mysql_num_rows($requete) == 1)
     {
        $monCentre = mysql_result($requete,0,"uc_centre_lb");
        if (strstr($_SERVER['SERVER_NAME'],$monCentre))
        {
           $requete= mysql_query ("delete FROM user_centre WHERE uc_iduser_no = $qui");
           $requete= mysql_query ("delete FROM users WHERE util_cdn = $qui");
        }
    }

}
elseif(isset($interface) && $interface == 1 && isset($chge_GraphTout) && $chge_GraphTout == 1)
{
    $etat_GraphTout= GetDataField($connect,"select param_etat_lb from param_foad where param_typ_lb='GraphTout'","param_etat_lb");
    $messGraphTout = ($etat_GraphTout == 'NON') ? "OUI" : "NON";
    $updt_GraphTout = mysql_query ("UPDATE param_foad SET param_etat_lb = '$messGraphTout' WHERE param_typ_lb='GraphTout'");
    if ($messGraphTout == "OUI")
       $mess_notif = "OUI";
    else
       $mess_notif = "NON";
}
elseif(isset($interface) && $interface == 1 && isset($chge_mailcomment) && $chge_mailcomment == 1)
{
    $etat_MailCmt = GetDataField($connect,"select param_etat_lb from param_foad where param_typ_lb='mailcomment'","param_etat_lb");
    $messMailCmt = ($etat_MailCmt == 'NON') ? "OUI" : "NON";
    $updt_MailCmt = mysql_query ("UPDATE param_foad SET param_etat_lb = '$messMailCmt' WHERE param_typ_lb='mailcomment'");
    if ($messMailCmt == "OUI")
       $mess_notif = "OUI";
    else
       $mess_notif = "NON";
}
elseif(isset($interface) && $interface == 1 && isset($chge_chat) && $chge_chat == 1)
{
    $etat_chat = GetDataField($connect,"select param_etat_lb from param_foad where param_typ_lb='chat'","param_etat_lb");
    $chat = ($etat_chat == 'NON') ? 'OUI' : 'NON';
    $updt_chat = mysql_query ("UPDATE param_foad SET param_etat_lb = '$chat' WHERE param_typ_lb='chat'");
    if ($chat == 'OUI')
       $mess_notif = $chat."C"."$msgadm_chat $msgadm_mc_act";
    else
       $mess_notif = $chat."C"."$msgadm_chat $msgadm_mc_desact";
}
elseif(isset($interface) && $interface == 1 && isset($chge_flib) && $chge_flib == 1)
{
    $etat_flib = GetDataField($connect,"select param_etat_lb from param_foad where param_typ_lb='forum_libre'","param_etat_lb");
    $flib = ($etat_flib == 'NON') ? "OUI" : "NON";
    $updt_flib = mysql_query ("UPDATE param_foad SET param_etat_lb = '$flib' WHERE param_typ_lb='forum_libre'");
    if ($flib == "OUI")
       $mess_notif = $flib."F"."$msgadm_flib $msgadm_mc_act";
    else
       $mess_notif = $flib."F"."$msgadm_flib $msgadm_mc_desact";
}
elseif(isset($interface) && $interface == 1 && isset($chge_Rss) && $chge_Rss == 1)
{
    $etat_rss = GetDataField($connect,"select param_etat_lb from param_foad where param_typ_lb='rss'","param_etat_lb");
    $rss = ($etat_rss == 'NON') ? "OUI" : "NON";
    $updt_rss = mysql_query ("UPDATE param_foad SET param_etat_lb = '$rss' WHERE param_typ_lb='rss'");
    if ($rss == "OUI")
       $mess_notif = $rss."R"."$msgadm_rss $msgadm_mc_act";
    else
       $mess_notif = $rss."R"."$msgadm_rss $msgadm_mc_desact";
}
elseif(isset($nbrAff_seq) && $nbrAff_seq > 0)
{
   $nbr_pgs_seq = $nbrAff_seq;
   $_SESSION['nbr_pgs_seq'] = $nbr_pgs_seq;
   $mess_notif = $msg_modItm.$nbr_pgs_seq;
}
elseif(isset($nbrAff_mod) && $nbrAff_mod > 0)
{
   $nbr_pgs_mod = $nbrAff_mod;
   $_SESSION['nbr_pgs_mod'] = $nbr_pgs_mod;
   $mess_notif = $msg_modItm.$nbr_pgs_mod;
}
elseif(isset($nbrAff_act) && $nbrAff_act > 0)
{
   $nbr_pgs_act = $nbrAff_act;
   $_SESSION['nbr_pgs_act'] = $nbr_pgs_act;
   $mess_notif = $msg_modItm.$nbr_pgs_act;
}
elseif(isset($interface) && $interface == 1 && isset($lestyle) &&  $lestyle != '')
{
    if (strstr($lestyle,".css") || strstr($lestyle,"css"))
    {
       $mess_notif = "L'extension .css n'est pas autorisée dans la saisie.<br /> Formagri se charge de la rajouter en temps utile...";
       echo utf2Charset(stripslashes($mess_notif),$charset);
       exit;
    }
    elseif(strstr($lestyle,"/"))
    {
       $mess_notif = "Le fichier css doit se trouver dans le répertoire Ressources<br />et la saisie n'autorise donc aucun chemin...";
       echo utf2Charset(stripslashes($mess_notif),$charset);
       exit;
    }
    $updt_css = mysql_query ("UPDATE param_foad SET param_etat_lb = \"$lestyle\" WHERE param_typ_lb='style_devoirs'");
    $mess_notif = "La feuille de style gérant la consigne de vos activités est désormais \"$lestyle.css\"";
}
if(isset($interface) && $interface == 1 && isset($suppTrace) &&  $suppTrace == '1')
{
       $date_jour = date("Y-n-d");
       $nb_jours_req = mysql_query ("SELECT TO_DAYS('$date_jour')");
       $nb_jours_cour = mysql_result ($nb_jours_req,0);
       $nb_jours_supp = $nb_jours_cour-10;
       $new_nb = mysql_query ("SELECT FROM_DAYS('$nb_jours_supp')");
       $new_date = mysql_result($new_nb,0);
       $requete = mysql_query("delete from trace where trace_date_dt < \"$new_date\"");
       $mess_notif = "La table de tracking a été réinitialisée...";
}
if(isset($groupage) && $groupage == 1 && isset($suppForum) &&  $suppForum == '1')
{
       $comment_forum = GetDataField($connect, "select name from forums where id='$id_forum'", "name");
       $reqFlect = mysql_query("delete from forum_lecture where forlec_forum_no= ". $_GET['forumID']);
       $mess_notif = "Le compteur de consultation du forum $comment_forum a été ré-initialisé";
}

if(isset($_GET['opale']) && $_GET['opale'] == 1 && isset($_GET['RepTincan']) &&  $_GET['RepTincan'] != '')
{

   copy_dir('../lib/OpaleTincan/','../'.$_GET['RepTincan'].'/');
   $NewContentXml = file_get_contents('../'.$_GET['RepTincan'].'/tincan.xml');
   $NewContentXml = html_entity_decode($NewContentXml,ENT_QUOTES,'iso-8859-1');
   if (isset($_GET['manifest']) && $_GET['manifest'] == 1)
   {
      if (file_exists('../'.$_GET['RepTincan'].'/index.html'))
         $LeLien = 'index.html';
      else
      {
         $IndexHtml = file_get_contents('../'.$_GET['RepTincan'].'/imsmanifest.xml');
         $pattern = "/^.*\bco\/\b.*$/m";
         $pattern1 = "/^.*\b\.html\b.*$/m";
         $matches = array();
         $matches1 = array();
         preg_match($pattern, $IndexHtml, $matches);
         preg_match($pattern1, $IndexHtml, $matches1);
         //var_dump($matches);var_dump($matches1);
         if (isset($matches[0]) && strlen($matches[0]) > 10 && isset($matches1[0]) && strlen($matches1[0]) > 10)
         {
          $LeRefresh = explode('"',$matches[0]);
          $numero = count($LeRefresh);// echo $numero;
          $kk = 0;
          while ($kk < $numero)
          {
             if (strstr($LeRefresh[$kk],'co/'))
             {
               $LeLien = $LeRefresh[$kk];
               break;
             }
             $kk++;
          }
         //echo $LeLien;exit;//var_dump($matches);var_dump($matches1);exit;//
         }
      }
   }
   if ((!empty($LeLien) && $LeLien == 'index.html') || !isset($_GET['manifest']))
   {
       $IndexHtml = file_get_contents('../'.$_GET['RepTincan'].'/index.html');
       $docu = new DOMDocument();
       @$docu->loadHTML($IndexHtml);
       $metadata = $docu->getElementsByTagName('meta');
       for ($j = 0; $j < $metadata->length; $j++)
       {
           $meta = $metadata->item($j);
           if (strtolower($meta->getAttribute('http-equiv')) == "refresh")
           {
              $SonUrl = $meta->getAttribute('content');
              $LaMeta = explode(';',$SonUrl);
              $LeRefresh = explode('=',$LaMeta[1]);
              $LeLien = str_replace('"','',$LeRefresh[1]);
              break;
           }
       }
       if (!isset($LaMeta) && isset($_GET['manifest']))
       {
         $pattern = "/^.*\bco\/\b.*$/m";
         $pattern1 = "/^.*\b\.html\b.*$/m";
         $matches = array();
         $matches1 = array();
         preg_match($pattern, $IndexHtml, $matches);
         preg_match($pattern1, $IndexHtml, $matches1);
         if (isset($matches[0]) && strlen($matches[0]) > 10 && isset($matches1[0]) && strlen($matches1[0]) > 10)
         {
          $LeRefresh = explode('"',$matches[0]);
          $numero = count($LeRefresh);// echo $numero;
          $kk = 0;
          while ($kk < $numero)
          {
             if (strstr($LeRefresh[$kk],'co/'))
             {
               $LeLien = $LeRefresh[$kk];
               break;
             }
             $kk++;
          }
         }
         //echo $LeLien;var_dump($matches);var_dump($matches1);exit;
       }
   }
   
   if (!empty($LeLien) && file_exists('../'.$_GET['RepTincan'].'/'.str_replace('%20',' ',$LeLien)))
   {
      $html = file_get_contents('../'.$_GET['RepTincan'].'/'.str_replace('%20',' ',$LeLien));
      //echo $LeLien.$html;exit;
      $doc = new DOMDocument();
      @$doc->loadHTML($html);
      $nodes = $doc->getElementsByTagName('title');
      //$title = preg_replace('#[^[:alnum:]]#u', '-', $nodes->item(0)->nodeValue);
      $title = html_entity_decode($nodes->item(0)->nodeValue,ENT_QUOTES,'ISO-8859-1');
/*
      $title = htmlentities(html_entity_decode($nodes->item(0)->nodeValue,ENT_QUOTES,'ISO-8859-1'),ENT_QUOTES,'ISO-8859-1');
      if (strstr($title, ' - '))
      {
         $LeTitre = explode(' - ',$title);
         $title =  ($LeTitre[0] == $LeTitre[1]) ? $LeTitre[0] : $title;
      }
*/
      $NewContentXml = str_replace('<name></name>','<name>'.modif_az_qw(clean_text(trim($title))).'</name>',$NewContentXml);
      if (strtolower($_GET['titre']) == 'web')
          $NewContentXml = str_replace('<activity id="http://formagri.com/"','<activity id="http://formagri.com/'.modif_az_qw(clean_text($title)).'"',$NewContentXml);
      else
          $NewContentXml = str_replace('<activity id="http://formagri.com/"','<activity id="http://formagri.com/'.$_GET['titre'].'"',$NewContentXml);
      $titrage = (strtolower($_GET['titre']) == 'web') ? modif_az_qw(clean_text($title)) : $_GET['titre'];
      $metas = $doc->getElementsByTagName('meta');

      for ($i = 0; $i < $metas->length; $i++)
      {
          $meta = $metas->item($i);
          if($meta->getAttribute('name') == 'description')
             $description = $meta->getAttribute('content');
          if($meta->getAttribute('name') == 'keywords')
             $keywords = $meta->getAttribute('content');
          if($meta->getAttribute('name') == 'generator')
             $generator = $meta->getAttribute('content');
          if($meta->getAttribute('name') == 'author')
             $author = $meta->getAttribute('content');
      }
      $desc = (isset($description)) ? $description : $generator;
      $desc = ($desc == '') ? 'Par '.$author : $title;
      $NewContentXml = str_replace('<description lang="fr-FR"></description>','<description lang="fr-FR">'.modif_az_qw(clean_text(html_entity_decode($desc,ENT_QUOTES,'ISO-8859-1'))).'</description>',$NewContentXml);
   }
   else
   {'"Attention !! " '.$LeLien.' ", fichier principal du module n\'a pas été trouvé dans le répertoire " co/ "';
      echo utf2Charset(stripslashes($mess_notif),$charset);
      exit;
   }
   $TitreCours = 'Opale';
   $fpApi = fopen('../'.$_GET['RepTincan'].'/tincan.xml', "w+");
       $fwApi = fwrite($fpApi,$NewContentXml);
   fclose($fpApi);
   // Modification du fichier Index.html
   $NewIndexHtml = str_replace('Aremplacer',$LeLien,file_get_contents('../'.$_GET['RepTincan'].'/indexNew.html'));
   $NewIndexHtml =  str_replace('Formagri',$TitreCours,$NewIndexHtml);
   $fpApi = fopen('../'.$_GET['RepTincan'].'/indexNew.html', "w+");
       $fwApi = fwrite($fpApi,$NewIndexHtml);
   fclose($fpApi);
   unlink ($repertoire.'/'.$_GET['RepTincan'].'/index.html');
   rename ($repertoire.'/'.$_GET['RepTincan'].'/indexNew.html','../'.$_GET['RepTincan'].'/index.html');
   // Modification du fichier common.js
   $NewCommonJs = str_replace('Aparent',$titrage,file_get_contents('../'.$_GET['RepTincan'].'/xapi-scripts/common.js'));
   $NewCommonJs = str_replace('Adescription',modif_az_qw(clean_text(trim($title))),$NewCommonJs);
   $NewCommonJs = str_replace('Formagri',$TitreCours,$NewCommonJs);
   $fpApi = fopen('../'.$_GET['RepTincan'].'/xapi-scripts/common.js', "w+");
       $fwApi = fwrite($fpApi,$NewCommonJs);
   fclose($fpApi);
   $handle = opendir('../'.$_GET['RepTincan'].'/co');
   $affiche = '';
   while ($fiche = readdir($handle))
   {
           if ($fiche != '.' && $fiche != '..' && strstr(strtolower($fiche),'html'))
           {
              $ContentHtml = file_get_contents('../'.$_GET['RepTincan'].'/co/'.$fiche);
              $doc = new DOMDocument();
              @$doc->loadHTML($ContentHtml);
              $nodes = $doc->getElementsByTagName('title');
              $titreFile = (isset($nodes->item(0)->nodeValue)) ? $nodes->item(0)->nodeValue : $fiche;

//$affiche .= $titreFile.'-'.$nodes->item(0)->nodeValue;
              if (strstr($ContentHtml,'(!scServices)'))
              {
                   $ContentHtml = str_replace ('var scServices = window.opener ? window.opener.scServices : window.parent.scServices;','		var scServices = {};

		scServices["scLoad"] = {
			loadFromRoot : function(pUrl) {sc$("mainFrame").src = pUrl;},
			getUrlFromRoot:function(pHref){if(!this.fRootOffset) this.fRootOffset = scCoLib.hrefBase().lastIndexOf("/")+1; return pHref.substring(this.fRootOffset);},
			getRootUrl:function(){if(!this.fRootUrl) this.fRootUrl = scCoLib.hrefBase().substring(0,scCoLib.hrefBase().lastIndexOf("/")); return this.fRootUrl;}
		}',$ContentHtml);
              }
              //$ContentHtml = str_replace ('<body>','<body onload="xapiStatement();">',$ContentHtml);
              if (strstr($ContentHtml,'</head>'))
                  $ContentHtml = str_replace('</head>','		<script type="text/javascript">
        if (window.location.href.indexOf("?endpoint") != -1)
        {
           var rien = true;
        }else{
           var leparent = (top.location.href.indexOf("?") != -1) ? top.location.search.slice(1) : top.location.search;
           if (leparent.length > 10)
              document.location.replace(window.location.href + "?" + leparent);
        }
	</script>
    <script src="../xapi-scripts/TinCanJS/build/tincan-min.js" type="text/javascript"></script>
	<script src="../xapi-scripts/common.js" type="text/javascript"></script>
    <script type="text/javascript">
      if (window.location.href.indexOf("?endpoint") != -1)
      {
        var tincan = new TinCan (
            {
                url: window.location.href,
                activity: {
                    id: '.$TitreCours.'.CourseActivity.id + "/'.$fiche.'",
                    definition: {
                        name: {
                            "fr-FR": "'.$titreFile.'"
                        },
                        description: {
                            "fr-FR": "'.$title.'"
                        }
                    }
                }
            }
        );

        tincan.sendStatement(
            {
                verb: "experienced",
                context: '.$TitreCours.'.getContext(
                    '.$TitreCours.'.CourseActivity.id
                )
            },
            function () {}
        );
        tincan.setState("location", "co/'.$fiche.'", function () {});
      }
	</script>
              </head>',$ContentHtml);
              elseif(strstr($ContentHtml,'</HEAD>'))
                  $ContentHtml = str_replace('</HEAD>','		<script type="text/javascript">
        if (window.location.href.indexOf("?endpoint") != -1){
           var rien = true;
        }else{
           var leparent = (top.location.href.indexOf("?") != -1) ? top.location.search.slice(1) : top.location.search;
           if (leparent.length > 10)
              document.location.replace(window.location.href + "?" + leparent);
        }
	</script>
	<script src="../xapi-scripts/TinCanJS/build/tincan-min.js" type="text/javascript"></script>
	<script src="../xapi-scripts/common.js" type="text/javascript"></script>
	<script type="text/javascript">
      if (window.location.href.indexOf("?endpoint") != -1)
      {
        var tincan = new TinCan (
            {
                url: window.location.href,
                activity: {
                    id: '.$TitreCours.'.CourseActivity.id + "/'.$fiche.'",
                    definition: {
                        name: {
                            "fr-FR": "'.$titreFile.'"
                        },
                        description: {
                            "fr-FR": "'.$title.'"
                        }
                    }
                }
            }
        );

        tincan.sendStatement(
            {
                verb: "experienced",
                context: '.$TitreCours.'.getContext(
                    '.$TitreCours.'.CourseActivity.id
                )
            },
            function () {}
        );
        tincan.setState("location", "co/'.$fiche.'", function () {});
      }
	</script>
              </head>',$ContentHtml);
              $fpApi = fopen('../'.$_GET['RepTincan'].'/co/'.$fiche, "w+");
                   $fwApi = fwrite($fpApi,$ContentHtml);
              fclose($fpApi);
           }
   }
   closedir($handle);
   $mess_notif = "Votre contenu est désormais compatible xApi TinCan Api. \nIl vous suffit de cliquer sur l'icone correspondant au fichier \"tincan.xml\" \n pour l'intégrer en tant qu'activité libre que vous pourrez ajouter à la séquence de votre choix.";
}

function copy_dir ($dir2copy,$dir_paste) {
  if (is_dir($dir2copy))
  {
    if ($dh = opendir($dir2copy))
    {
      while (($file = readdir($dh)) !== false)
      {
        if (!is_dir($dir_paste))
        {
             mkdir ($dir_paste, 0775);
             chmod($dir_paste, 0775);
        }
        if(is_dir($dir2copy.$file) && $file != '..'  && $file != '.'){
             copy_dir ( $dir2copy.$file.'/' , $dir_paste.$file.'/' );
             chmod($dir_paste.$file, 0775);
       }elseif($file != '..'  && $file != '.'){
             copy ( $dir2copy.$file , $dir_paste.$file );
             chmod($dir_paste.$file, 0775);
        }
        //echo $dir2copy.$file.'<br />'.$dir_paste.$file.'<br />';
      }
      closedir($dh);
    }
  }
}
//sleep(1);
echo utf2Charset(stripslashes($mess_notif),$charset);
?>
