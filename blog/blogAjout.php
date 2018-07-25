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
require  "blogClass.php";
dbConnect();
setlocale(LC_TIME,'fr_FR');
$nom_user = $_SESSION["name_user"];
$prenom_user = $_SESSION["prename_user"];
$email_user = $_SESSION["email_user"];
$leJour = date("Y/m/d H:i:s" ,time());
$date_cour = date ("Y-m-d");
if (!empty($_GET['numApp'])) $numApp = $_GET['numApp'];
if (!empty($_GET['numero_groupe'])) $numero_groupe = $_GET['numero_groupe'];
if (!empty($_GET['numeroApp'])) $numeroApp = $_GET['numeroApp'];
if (!empty($_GET['id_clan'])) $id_clan = $_GET['id_clan'];
$content = '';
include ("../style.inc.php");
$reqblog=mysql_query("select * from blog where blog_auteur_no = ".$_SESSION['id_user']);
$nbblog = mysql_num_rows($reqblog);
if (!empty($_GET['ajt']) && $_GET['ajt'] == 1 && $nbblog == 0)
{
   $leBody = (strstr(getenv("HTTP_USER_AGENT"),"MSIE")) ? "Mon thème":"----------";
   if (strstr(getenv("HTTP_USER_AGENT"),"MSIE"))
      $reqInitBg = mysql_query("update blog set blog_consigne_cmt = 'Mon thème' where blog_consigne_cmt = '----------'");
   if ($nbblog == 0)
   {
      $id_bg = Donne_ID ($connect,"select max(blog_cdn) from blog");
      $reqBg = mysql_query("insert into blog values('$id_bg','".$_SESSION['id_user']."',\"$leBody\",'".$numero_groupe."',\"$leJour\")");
      $id_bgClan = Donne_ID ($connect,"select max(bgapp_cdn) from blogapp");
      $reqBgClan = mysql_query("insert into blogapp values('$id_bgClan','$id_bg','".$_SESSION['id_user']."','".$numero_groupe."','".$_SESSION['id_user']."',\"$date_cour\",\"$date_cour\")");
      $id_bgshr = Donne_ID ($connect,"select max(bgshr_cdn) from blogshare");
      $reqBgClan = mysql_query("insert into blogshare values('$id_bgshr','".$_SESSION['id_user']."',0,0,1,1)");
   }

}
if (!empty($_GET['supp']) && $_GET['supp'] == 1)
{
    $supprimer = mysql_query("delete from blog where blog_cdn=".$_GET['Idblog']);
}
if (!empty($mess_notif))
   echo notifier($mess_notif);
if (strstr(getenv("HTTP_USER_AGENT"),"MSIE"))
{
       $content.= '<div id="ListeBg" class="createtheme">';
       $content .= "
            <script type=\"text/javascript\">
              \$('#mien').html('Votre blog a été activé.');
              \$('#mien').show();
              setTimeout(function() {\$('#mien').empty();parent.location.reload();},2000);
            </script>";
      $content .= "Vous venez d'activer votre blog<br />
                  <span style='font-size:12px;color:red;font-weight:bold;'>
                  Pour un meilleur rendu du blog, utilisez Firefox ou Chrome</span</div>";
}
else
{
  $content.= 'Thème général de mon Blog ou catégorie envisagée';
  $content.= '<div id="ListeBg" class="createtheme">';
  $reqblog=mysql_query("select * from blog where blog_auteur_no = ".$_SESSION['id_user']);
  $nbblog = mysql_num_rows($reqblog);
  if (mysql_num_rows($reqblog) > 0)
  {
       $oblogConsigne = mysql_result($reqblog,0,'blog_consigne_cmt');
       $oblogId = mysql_result($reqblog,0,'blog_cdn');
       $titreItem = "<strong>Cliquer sur l'icone à droite pour créer/sauvegarder le thème</strong>";
       $content .= '<div data-zcontenteditable data-id="'.$oblogId.
                   ' class="Bg" title ="Créé par '.NomUser($_SESSION['id_user']).'. '.$titreItem.'" >';
       $content .= $oblogConsigne;
       $content.= '</div>';
       $content .= "
            <script type=\"text/javascript\">
              jQuery(function(){\$('[data-zcontenteditable]').zcontenteditable({callback:saveEditable,btnEditSaveAction:'".
                                "<span data-action=\"edit\">&nbsp;</span>' });});
              function saveEditable(zcontenteditableResponse){
                 var dataAttributs=zcontenteditableResponse.dataAttributs;
                 var params=zcontenteditableResponse;
                    \$.ajax({
                        type: 'GET',
                        url: 'bloglib.php',
                        data: 'new='+params.content+'&modifie=1&table=blog&cdn=blog_cdn&champ=blog_consigne_cmt&id='+dataAttributs.id,
                        beforeSend:function(){
                            \$('#affiche').addClass('Status');
                            \$('#affiche').append('Opération en cours....');
                        },
                        success: function(){
                            \$('#mien').empty();
                            \$('#mien').html('Le thème du blog a été modifié.');
                            \$('#affiche').empty();
                            \$('#mien').show();
                            setTimeout(function() {\$('#mien').empty();parent.location.reload();},2000);
                        }
                    });
             }
            </script>";
      $content .= '</div>';
  }
  $content.= '</div>';
}
echo $content;
echo '<div id="affiche"></div>';
echo '<div id="mien" class="cms" title="Cliquer pour fermer cette alerte" onClick="javascript:$(this).empty();";></div>';
//echo "<pre>";print_r($oblog);echo "</pre>";
?>
