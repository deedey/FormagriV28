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
require "lib/ClassImgResize.php";
require "blogClass.php";
dbConnect();
   //echo "<pre>";print_r($_POST);echo "</pre>";
if (!empty($_POST['newCmt']) && $_POST['newCmt'] != '')
{
     $idCmt = Donne_ID ($connect,"select max(com_cdn) from commentaires");
     $insertCmt = mysql_query("insert into commentaires (com_cdn,com_auteur_no,combg_body_no,com_comment_cmt,com_date_dt)values($idCmt,".$_SESSION['id_user'].",".$_GET['numBody'].",\"".
                             htmlspecialchars($_POST['newCmt'],ENT_QUOTES,'ISO-8859-1')."\",\"".date("Y-m-d H:i:s" ,time())."\")");
     $reqBgMail = GetDataField($connect,"select bgbody_titre_lb from blogbodies where bgbody_cdn=$numBody", "bgbody_titre_lb");
     $reqBgAuteur = GetDataField($connect,"select bgbody_auteur_no from blogbodies where bgbody_cdn=".$_GET['numBody'], "bgbody_auteur_no");
     if ($_SESSION['id_user'] != $reqBgAuteur)
         envoiMailCmt($numBody,$reqBgAuteur,'Blog',htmlspecialchars($reqBgMail,ENT_QUOTES,'ISO-8859-1'),htmlspecialchars($_POST['newCmt'],ENT_QUOTES,'ISO-8859-1'));
     $mess_notif = 'Votre commentaire a été inséré';
}
if (!empty($_POST['ajtNewBodyImg']) && $_POST['ajtNewBodyImg'] == 1)
{
   $mess_notif = '';
   if (!empty($_POST['id_bgimg']) && $_POST['id_bgimg'] > 0)
      $id_bgimg = $_POST['id_bgimg'];
   elseif(empty($_FILES['userfile']['tmp_name']))
   {
      $id_bgimg = 0;
      $userfile = 0;
   }
   if (!empty($_FILES['userfile']['tmp_name']) && is_uploaded_file($_FILES['userfile']['tmp_name']) && (!isset($_POST['id_bgimg']) ||
      ((isset($_POST['id_bgimg']) && $_POST['id_bgimg'] == 0))))
   {
       $ImgTemp = $_FILES['userfile']['tmp_name'];
       $fichier = "TempFiles/".$_FILES['userfile']['name'];
       $nom_fichier = modif_nom($_FILES['userfile']['name']);
       list($extension, $nom) = getextension($nom_fichier);
       if (strtolower($extension) != 'jpeg' && strtolower($extension) != 'jpg' &&
           strtolower($extension) != 'gif' && strtolower($extension) != 'png')
       {
           $Img = '';
           $mess_notif .= 'L\'image n\'a pas été prise en compte:<br />'.
                          'Votre image n\'a pas une extension de type jpg, jpeg, gif ou png<br />';
           $id_bgimg = '100000';
       }
       else
       {
           copy($_FILES['userfile']['tmp_name'],"TempFiles/".$nom_fichier);
           $taille = getimagesize("TempFiles/".$nom_fichier);
           if ($taille[0] > 550)
           {
               $thumb=new thumbnail("TempFiles/".$nom_fichier);
               $thumb->size_width(550);
               $thumb->jpeg_quality(75);
               $thumb->save("TempFiles/".$nom_fichier);
           }
           $taille = getimagesize("TempFiles/".$nom_fichier);
           if ($taille[1] > 250)
           {
               $thumb=new thumbnail("TempFiles/".$nom_fichier);
               $thumb->size_height(250);
               $thumb->jpeg_quality(75);
               $thumb->save("TempFiles/".$nom_fichier);
           }
           $Img = file_get_contents("TempFiles/".$nom_fichier);
           unlink("TempFiles/".$nom_fichier);
           $taille_file = $_FILES['userfile']['size'];
           if ($taille_file > $_POST['max_img'])
           {
               $mess_notif .= 'Votre image dépasse la taille autorisée de 100 ko<br />Corrigez s\'il vous plaît..!';
               $id_bgimg = '100000';
           }
           else
           {
               $id_bgimg = Donne_ID ($connect,"select max(bgimg_cdn) from blogmg");
               $reqAjt = mysql_query("insert into blogmg values('$id_bgimg',\"".addslashes($Img)."\",'".$_SESSION['id_user']."')");
               $mess_notif .= '';
           }
       }
   }
   if ($_POST['position'] == 0 && (empty($id_bgimg) || (!empty($id_bgimg) && $id_bgimg != 100000)))
   {
        $reqAfter = mysql_query("select * from blogbodies where bgbody_auteur_no='".$_SESSION['id_user'].
                                   "' and bgbody_order_no >='".$_POST['newOrdBg']."' order by bgbody_order_no desc");
        $CptOrdre = $_POST['newOrdBg'];
        while ($oAfter = mysql_fetch_object($reqAfter))
        {
             $CptOrdre = $oAfter->bgbody_order_no+1;
             $reqModif= mysql_query("update blogbodies set bgbody_order_no = '$CptOrdre' where bgbody_order_no='".
                                     $oAfter->bgbody_order_no."' and bgbody_auteur_no='".$_SESSION['id_user']."'");
        }
   }
   if ($id_bgimg != 100000)
   {
      $id_bg = Donne_ID ($connect,"select max(bgbody_cdn) from blogbodies");
      $reqAjt = mysql_query("insert into blogbodies values('$id_bg','".$_SESSION['id_user']."','".$_SESSION['id_user']."',\"".
                             htmlspecialchars($_POST['legende'],ENT_QUOTES,'ISO-8859-1')."\",\"".
                             clean_text(addslashes($_POST['titreImg']))."\",'1','$id_bgimg','".$_POST['newOrdBg']."','".time()."')");
      $mess_notif = "Votre insertion a été effectuée dans le document en cours.";
   }
}
if (!empty($_GET['SuppBody']) && $_GET['SuppBody'] == 1)
{
     $supprimer = mysql_query("delete from blogbodies where bgbody_cdn=".$_GET['idBody']);
     $mess_notif = "Vous venez de supprimer un article.";
}

if (!empty($_GET['ajTitre']) && $_GET['ajTitre'] == 1)
{
   if (!empty($_GET['IdMeta']))
       $NbTitle = mysql_num_rows(mysql_query("select bgmeta_titre_lb from blogmeta where bgmeta_auteur_no = ".$_SESSION['id_user']));
   if (isset($NbTitle) && $NbTitle > 0)
      $titreOld = GetDataField ($connect,"select bgmeta_titre_lb from blogmeta where bgmeta_cdn = ".$_GET['IdMeta'],"bgmeta_titre_lb");
   if (!isset($NbTitle) || (isset($NbTitle) && $NbTitle == 0))
   {
       $id_bg = Donne_ID ($connect,"select max(bgmeta_cdn) from blogmeta");
       $reqAjt = mysql_query("insert into blogmeta values('$id_bg','".$_SESSION['id_user']."','".$_SESSION['id_user']."',\"".
                        clean_text(addslashes($_GET['newTitre']))."\",\"font-size:24px;\",'".time()."')");
       $mess_notif = "Vous venez de créer le titre du document.";
   }
   elseif(stripslashes($titreOld) != $_GET['newTitre'] && $NbTitle > 0)
   {
       $reqAjt = mysql_query("update blogmeta set bgmeta_titre_lb  = \"".clean_text(addslashes($_GET['newTitre'])).
                             "\" where bgmeta_auteur_no='".$_SESSION['id_user']."' and bgmeta_clan_no='".$_SESSION['id_user']."'");
       $mess_notif = "Vous venez de modifier le titre du document.";
   }
   elseif(stripslashes($titreOld) == $_GET['newTitre'])
       $mess_notif = "Vous n'avez apporté aucun changement au titre...<br />".
                     "Nul besoin de valider quoi que ce soit dans ce cas!!!";

}
if (!empty($_GET['ajtBody']) && $_GET['ajtBody'] == 1)
{
   if (!empty($_GET['first']) && $_GET['first'] == 1)
   {
          $id_bgBody = Donne_ID ($connect,"select max(bgbody_cdn) from blogbodies");
          $reqAjt = mysql_query("insert into blogbodies values('$id_bgBody','".$_SESSION['id_user']."','".
                                $_SESSION['id_user']."',\"".htmlspecialchars($_POST['newBody'],ENT_QUOTES,'ISO-8859-1').
                                "\",\"".clean_text(addslashes($_GET['newTitre']))."\",1,0,1,'".time()."')");
   }
   else
   {
       $order_bg = $_GET['ordre'];
       $bodyOld = GetDataField ($connect,"select bgbody_body_cmt from blogbodies where bgbody_cdn = ".$_GET['IdBody'],"bgbody_body_cmt");
       $titreOld = GetDataField ($connect,"select bgbody_titre_lb from blogbodies where bgbody_cdn = ".$_GET['IdBody'],"bgbody_titre_lb");
       if ((!empty($_GET['ordre']) && ($bodyOld != htmlspecialchars($_POST['newBody'],ENT_QUOTES,'ISO-8859-1') || stripslashes($titreOld) != clean_text($_GET['newTitre']))) || empty($_GET['ordre']))
       {
          $reqAjt = mysql_query("update blogbodies set bgbody_body_cmt=\"".htmlspecialchars($_POST['newBody'],ENT_QUOTES,'ISO-8859-1').
                            "\",bgbody_titre_lb=\"".clean_text(addslashes($_GET['newTitre'])."\",
                            bgbody_date_dt='".time()."' where bgbody_cdn = ".$_GET['IdBody']));
          $mess_notif = "Vous venez de modifier un article.";
       }
       else
       {
          $mess_notif = "Vous n'avez apporté aucun changement ni au titre ni à l'article...<br />".
                    "Nul besoin de valider quoi que ce soit dans ce cas !!!";
       }
   }
}
$nom_user = $_SESSION["name_user"];
$prenom_user = $_SESSION["prename_user"];
$email_user = $_SESSION["email_user"];
include ("../style.inc.php");
if (!empty($_GET['numApp'])) $numApp = $_GET['numApp'];
if (!empty($_GET['numero_groupe'])) $numero_groupe = $_GET['numero_groupe'];
if (!empty($_GET['numeroApp'])) $numeroApp = $_GET['numeroApp'];
$id_clan = (!empty($_GET['id_clan'])) ? $_GET['id_clan'] : $_GET['numApp'];
if (!empty($_GET['id_grp'])) $id_grp = $_GET['id_grp'];
if (!empty($_GET['vuePlan'])) $vuePlan = $_GET['vuePlan'];
if ($id_clan != $_SESSION['id_user'])
    $vuePlan=1;
if (!empty($mess_notif))
   echo notifier($mess_notif);
?>
    <script type="text/javascript">
       function checkIt(body,titre,url,data) {
           document.write ("<form name='formulaire' action='"+url+"?"+data+"&newTitre="+escape(titre)+"' method='POST'>");
           document.write ("<input type='hidden' name='newBody' value='"+body+"'></form>");
           //document.location.replace(url+'?'+data+'&newBody='+escape(body)+'&newTitre='+escape(titre));
           document.formulaire.submit(); // envoi du formulaire
       }
       function envoiTitre(titre,url,data)
       {
          // alert(url+'?'+data+'&newTitre='+escape(titre));
           document.location.replace(url+'?'+data+'&newTitre='+escape(titre));
       }
       function envoiChp(titre,url,data)
       {
          // alert(url+'?'+data+'&newTitre='+escape(titre));
           document.location.replace(url+'?'+data+'&newChp='+escape(titre));
       }
       function sendAjax(titre,url,data)
       {
           $.ajax({type: "GET",
                   url: url,
                   data: data,
                   beforeSend:function()
                   {
                       $("#affiche").addClass("Status");
                       $("#affiche").append("Opération en cours....");
                   },
                   success: function(msg)
                   {
                       $("#ajt'.$iClan.'").empty();
                       $("#ajt'.$iClan.'").html("");
                       $("#ajt'.$iClan.'").addClass("");
                       $("#mien").empty();
                       $("#mien").html(" Vous avez d'insérer ou de modifier le titre");
                   }
           });
           $("#affiche").empty();
           setTimeout(function() {$("#mien").empty();},5000);
       }
       function checkForm(frm) {
                var ErrMsg = "Un certain nombre d'informations requises manquent\n\n";
                var lenInit = ErrMsg.length;
                if (isEmpty(frm.titreImg)==true)
                    ErrMsg += ' - Titre de l\'image\n';
                if (isEmpty(frm.legende)==true)
                    ErrMsg += ' - Légende de l\'image\n';
                if (isEmpty(frm.userfile)==true)
                    ErrMsg += ' - Image non chargée\n';
                if (ErrMsg.length > lenInit)
                    alert(ErrMsg);
                else
                    frm.submit();
       }
       function checkFormC(frm) {
                var ErrMsg = "Un certain nombre d'informations requises manquent\n\n";
                var lenInit = ErrMsg.length;
                if (isEmpty(frm.newCmt)==true)
                    ErrMsg += ' - Votre commentaire\n';
                if (ErrMsg.length > lenInit)
                    alert(ErrMsg);
                else
                    frm.submit();
       }
       function isEmpty(elm) {
                var elmstr = elm.value + "";
                if (elmstr.length == 0)
                    return true;
                return false;
       }
    </script>
<?php
$content = '';
$listeClan = '';
$listeClan1 = '';
$listeClan2 = '';
$reqBgApp = mysql_query("select * from blog,blogapp where bgapp_app_no=".$id_clan." and bgapp_blog_no=blog_cdn");
$nbBgClan = mysql_num_rows($reqBgApp);
$infos = (empty($_GET['ChxDte'])) ? "Dernière version" : "Version du ".date('d-m-Y à H:i\':s\'\'',$_GET['ChxDte']);
if ($nbBgClan > 0)
{
    $theme = mysql_result($reqBgApp,0,'blog_consigne_cmt');
    $themeID = mysql_result($reqBgApp,0,'blog_cdn');
    $listeClan1 .= '<div>par '.NomUser(mysql_result($reqBgApp,0,'bgapp_app_no')).'</div>';
    if ($id_clan == $_SESSION['id_user'])
    {
             $vue = (isset($vuePlan) && $vuePlan == 1) ? 0:1;
             $listeClan2 = '<div style="float:right;" onClick="javascript:document.location.replace(\'blogOpen.php?vuePlan='.
                            $vue.'&numApp='.$numApp.'&id_clan='.$_SESSION['id_user'].'\');" '.
                            ' title="Afficher le plan et le détail de ce blog."> '.
                            '<img src="images/plan.gif" border="0" style="padding:0 0 15px 5px;cursor:pointer;"></div>';
    }
}
if ($id_clan == $_SESSION['id_user'] && (!isset($vuePlan) || (isset($vuePlan) && $vuePlan != 1)))
{
?>
     <?php
        $titreItem = "<strong>Cliquez sur l'icone pour modifier ce theme</strong>";
       $leTheme = '<div data-zcontenteditable data-id="'.$themeID.'" '.
                   'title="'.$titreItem.'" class="Bg">';
       $leTheme .= $theme;
       $leTheme.= '</div>';
      $leTheme.= "
            <script type=\"text/javascript\">
              jQuery(function(){\$('[data-zcontenteditable]').zcontenteditable({callback:saveEditable,btnEditSaveAction:'".
                                "<span data-action=\"edit\">&nbsp;</span>' });});
              function saveEditable(zcontenteditableResponse){

            // console.log(zcontenteditableResponse);

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
                            setTimeout(function() {\$('#mien').empty();},4000);
                        }
                    });
             }
            </script>";
}
else
       $leTheme = '<div class="BgNo">'.$theme.'</div>';
$listeClan .= $leTheme.'<div class="themeCont">'.$listeClan1.'</div>'.$listeClan2.'</div></div>';
$reqBody = mysql_query("select * from blogbodies where bgbody_auteur_no=".$id_clan.
                       " and (bgbody_show_on= 1 or(bgbody_show_on=0 and bgbody_auteur_no=".$_SESSION['id_user']."))
                       order by bgbody_order_no,bgbody_date_dt desc");
$reqMeta = mysql_query("select * from blogmeta where bgmeta_auteur_no =".$id_clan.
                       " order by bgmeta_date_dt desc");
$nbBody = mysql_num_rows($reqBody);
$nbMeta = mysql_num_rows($reqMeta);
if ($nbMeta > 0)
{
   $iMeta = 0;
   $TabMeta = array();
   while($oMeta = mysql_fetch_object($reqMeta))
   {
       array_push($TabMeta,$oMeta);
       if ($iMeta == 0)
       {
          $content .= '<div id="blogInfoDoc">'.$listeClan.'</div>';
          //$content .= '<div id="blogInfoDocLong">'.$listeClan.'</div>';
          if (empty($vuePlan))
          {
              $content .= '<div id="BlkTitDoc" class="BlkTitDoc">';
              $content .= '<div id="blogTitDoc" style="'.$oMeta->bgmeta_style_lb.';"> '.
                          stripslashes($oMeta->bgmeta_titre_lb).'</div>';
              $content .= '<div id="modifTitDoc" class="modifTitDoc" ';
              $content .= 'onClick="javascript:$(\'#BlkTitDoc\').toggle();'.
                          '$(\'#NewTitDoc\').toggle();" title="Modifier le titre de ce document.">'.
                          '<div id="imgModTitDoc" style="display:block;"><img src="images/modif15.gif" border="0"></div>';
              $content .= '</div>';
          }
          $content .= '<ul id="ul25" style="display:none;">';
          $content .= listBg($_SESSION['id_user'],$id_grp);
          $content .= '</ul></div>';
          if (empty($vuePlan))
          {
             $content .= '<div id="NewTitDoc" class="NewTitDoc">';
             $content .= '<input id="newTitre" class="input" style="font-size:18px;float:left;" size="40" value="'.
                         stripslashes($oMeta->bgmeta_titre_lb).'" />';
             $content .= '<input type="image" id="envoi" style="float:left;margin:0 0 0 10px;" '.
                         'src="../images/fiche_identite/boutvalid.gif" title="procéder à la modification" '.
                         'onClick="javascript:envoiTitre($(\'#newTitre\').val(),\'blogOpen.php\',\'ajTitre=1&IdMeta='.
                         $oMeta->bgmeta_cdn.'&id_clan='.$_SESSION['id_user'].'\');" />';
             $content .= '<div id="noImgMod" class="noImgMod" style="float:left;" '.
                         'onClick="javascript:$(\'#NewTitDoc\').toggle();'.
                         '$(\'#BlkTitDoc\').toggle();" title="Abandonner cette modification.">'.
                         '<img src="images/retour15.gif" border="0"></div>';
             $content .= '</div>';
          }
          else
          {
             $content .= '<div id="LePlan" style="clear:both;float:left;text-align:left;'.
                         'max-width:800px;font-size:13px;font-weight:bold;margin:20px 0 10px 10px;" '.
                         'onMouseOver="$(\'#NumeroPlan\').removeClass();$(\'#CacherUl\').removeClass();" '.
                         'onMouseOut="$(\'#NumeroPlan\').addClass(\'opacite\');$(\'#CacherUl\').addClass(\'opacite\');">'.
                         '<div id="TitrePlan" style="clear both;cursor:pointer;text-align:center;margin-top:4px;font-size:22px;margin:20px 0 20px 20px;" '.
                         'onClick="javascript:$(\'.BlkBody\').toggle();" title="Cliquez pour Afficher le plan/Tout afficher.">';
             $content .= stripslashes($oMeta->bgmeta_titre_lb);
             $content .= '</div>';
           }

       }
     $iMeta++;
   }
   //echo "<pre>";print_r($TabMeta);echo "</pre>";
}
elseif($nbMeta == 0 && $nbBody == 0)
{
   $content .= '<div id="blogInfoDoc">'.$listeClan.'</div>';
   $content .= '<div id="blogInfoDocLong">'.$listeClan.'</div>';
   if ($id_clan == $_SESSION['id_user'] || $numApp == $_SESSION['id_user'])
   {
      $content .= '<div id="BgTitre" class="BlkTitDoc">';
      $content .= '<div style="clear:both;float:left;" title="Créez ici le titre de votre document afin '.
               'd\'avoir accés à la création du premier article.">'.
               '<input id="newTitre" class="INPUT" style="font-size:18px;" size="35" />'.
               '</div>';
      $content .= '<div style="float:left;padding:0 0 0 5px;"> <a href="javascript:void();" '.
               'onClick="javascript:envoiTitre($(\'#newTitre\').val(),\'blogOpen.php\',\'ajTitre=1&id_clan='.$_SESSION['id_user'].'\');">'.
               '<img src="images/boutvalid.gif" border="0"></a></div></div>';
      $content .= '</div>';
   }
}
elseif($nbMeta == 0 && $nbBody > 0 )
{
   $content .= '<div id="blogInfoDoc">'.$listeClan.'</div>';
   $content .= '<div id="blogInfoDocLong">'.$listeClan.'</div>';
   $content .= '<div id="BlkTitDoc" class="BlkTitDoc">';
   $content .= '<div id="blogTitDoc" style="'.$oMeta->bgmeta_style_lb.';color:red !important;"> '.
               ' Aucun titre n\'avait été saisi à cette date</div>';
   if ($id_clan == $_SESSION['id_user'] || $numApp == $_SESSION['id_user'])
   {
      $vue = ($vuePlan == 1) ? 0:1;
      $content .= '<div id="menu_vert" style="float:left;">';
      $content .= '<span onClick="javascript:document.location.replace(\'blogOpen.php?vuePlan='.$vue.'&numApp='.$numApp.
               '&id_clan='.$_SESSION['id_user'].'\')"; '.
               ' title="Passer en mode Plan et Affichage du blog."> '.
               '<img src="images/plan.gif" border="0" style="margin:0 0 0 10px;padding-top:3px;cursor:pointer;"></span>';
   }
   $content .= '<ul id="ul25" style="display:none;">';
   $content .= listBg($_SESSION['id_user']);
   $content .= '</ul></div></div>';
}
if ($nbBody > 0)
{
   $NbOrdrExist = mysql_num_rows(mysql_query("select bgbody_order_no from blogbodies where bgbody_auteur_no=".$_SESSION['id_user']));
   $iBody = 0;
   $nbPrg = 0;
   $vueBody = 0;
   $TabBody = array();
   while($oBody = mysql_fetch_object($reqBody))
   {
     array_push($TabBody,$oBody);
       $ReqComment = mysql_query("select * from commentaires,utilisateur where combg_body_no=".$oBody->bgbody_cdn.
                                        " and utilisateur.util_cdn = com_auteur_no order by com_date_dt");
       $NbComment = mysql_num_rows($ReqComment);
       $ReqStar = mysql_query("select * from starating,utilisateur where bgstar_body_no=".$oBody->bgbody_cdn.
                                        " and utilisateur.util_cdn = starate_auteur_no order by starate_date_dt");
       $NbStar = mysql_num_rows($ReqStar);
       if (((empty($_GET['IdOrdre']) || (!empty($_GET['IdOrdre']) && $oBody->bgbody_order_no != $_GET['IdOrdre'])) &&
           (($iBody > 0 && $TabBody[$iBody-1]->bgbody_order_no != $TabBody[$iBody]->bgbody_order_no) || $iBody == 0)) ||
           (!empty($_GET['IdOrdre']) && $oBody->bgbody_cdn == $_GET['IdCdn'] && $oBody->bgbody_order_no == $_GET['IdOrdre']))
       {
          $nbPrg++;
          if (!empty($vuePlan))
          {
             $vueBody++;
             $content .= vuePlan($iBody,$oBody,$vueBody);
          }//fin vue plan
          else
          {
              if ((isset($TabBody[$iBody-1]->bgbody_order_no) && $TabBody[$iBody-1]->bgbody_order_no != $TabBody[$iBody]->bgbody_order_no &&
                   empty($_GET['IdOrdre']) || !empty($_GET['IdOrdre'])) && empty($_GET['ChxDte']))
              {
                   $content .= AjtElm($iBody,$oBody,0);
              }
              $content .= '<div id="completBody'.$iBody.'" class="completBody">';
              $content .= '<div id="actuel'.$iBody.'" class="BodyActuel">';
              $content .= '<div id="enteteBg">'.
                          '<div id="titBShow'.$iBody.'" class="titBShow">'.
                          stripslashes($oBody->bgbody_titre_lb).'</div>';
              if ($_SESSION['id_user'] == $oBody->bgbody_auteur_no && $NbComment == 0 && $NbStar == 0)
              {
                  $validation = ($oBody->bgbody_show_on == 1) ? "<img src='images/visible.gif' border=0>" : "<img src='images/invisible.gif' border=0>";
                  $content .='<div id="visibilite'.$iBody.'" style="float:left;margin: 8px 0 0 10px;cursor:pointer;">'.
                           '<a href="javascript:void(0);" '.
                           'title="Changez l\'état de visibilité de cet article <br />(mode brouillon ou validé)." '.
                           'onClick="javascript:$(document).ready(function(){
                                        $.ajax({type: \'GET\',
                                              url: \'bloglib.php\',
                                              data: \'valid_public=1&idPub='.$oBody->bgbody_cdn.'\',
                                              beforeSend:function()
                                              {
                                                 $(\'#affiche\').addClass(\'Status\');
                                                 $(\'#affiche\').append(\'Opération en cours....\');
                                              },
                                              success: function(msg){
                                                   $(\'#afterValid'.$iBody.'\').empty();
                                                   $(\'#afterValid'.$iBody.'\').html(msg);
                                                   $(\'#affiche\').empty();
                                                   $(\'#mien\').empty();
                                                   if (msg.substr(17,1) == \'i\')
                                                       $(\'#mien\').html(\'Vous venez de rendre cet article <br />invisible aux autres utilisateurs (mode brouillon).\');
                                                   else
                                                       $(\'#mien\').html(\'Vous venez de rendre cet article <br />visible aux autres utilisateurs.\');
                                                   $(\'#mien\').show();
                                                   setTimeout(function() {$(\'#mien\').empty();},7000);
                                              }
                                        });
                                    });" >'.
                             '<div id="afterValid'.$iBody.'" style="text-align:center; outline: 0;">'.$validation.'</div></a></div>';

              }
              if (($_SESSION['id_user'] == $oBody->bgbody_auteur_no || $_SESSION['Droitsblog'] == 1) && $NbComment == 0 && $NbStar == 0)
              {
                  $suiTitle = (isset($_SESSION['DroitsWiki']) && $_SESSION['DroitsWiki'] == 1 && $_SESSION['id_user'] != $oBody->bgbody_auteur_no) ? " de ".NomUser($oBody->bgbody_auteur_no) : "";
                  $content .= '<div style="float:right;margin: 8px 0 0 10px;cursor:pointer;" '.
                              'onClick="javascript:document.location.replace(\'blogOpen.php?SuppBody=1&idBody='.$oBody->bgbody_cdn.
                              '&numApp='.$_SESSION['id_user'].'&id_clan='.$_SESSION['id_user'].'\');"'.
                              ' title="Supprimer cet article'.$suiTitle.'">'.
                              '<img src="images/supp.png" border="0"></div>';
              }
              $content .= '</div>';
              if (!empty($oBody->bgbody_img_no))
              {
                   $content .= '<div id="ImgShow'.$iBody.'" '.
                               'style="clear:both;float:left;width:auto;border:1px solid #24677A;margin:5px;padding:4px;">';
                   $content .= '<img src="lib/affiche_image.php?provenance=paragraphe&numImg='.$oBody->bgbody_img_no.'">';
                   $content .= '</div>';
                   $content .= '<div id="legende'.$iBody.'" style="clear:both;font-size:12px;font-weight:bold;padding:4px;">'.
                               ' Légende :</div>';
              }
              $content .= '<div id="BodyShow" class="BodyShow">'.html_entity_decode($oBody->bgbody_body_cmt,ENT_QUOTES,'ISO-8859-1').'</div>';
              if ($_SESSION['id_user'] == $oBody->bgbody_auteur_no || $_SESSION['Droitsblog'] == 1)
              {
                  $content .= '<div id="modif'.$iBody.'" class="BodyModif" ';
                  $content .= 'onClick="javascript:$(\'#leBody'.$iBody.'\').toggle();'.
                          '$(\'#actuel'.$iBody.'\').toggle();" title="Cliquez ici pour modifier.">'.
                          '<div id="imgMod'.$iBody.'" style="display:block;"><img src="images/modif15.gif" border="0"></div>';
              }
              $content .= '</div>';
              $listeComment = '';
              while ($itemComment = mysql_fetch_object($ReqComment))
              {
                  $dateComment = reverse_date(substr($itemComment->com_date_dt,0,10),'-','/');
                  $listeComment .= '<div id="ItemComment'.$itemComment->com_cdn.'">'.
                                   '<div id="leComment'.$itemComment->com_cdn.'" class="leComment">'.
                                   '<div id="enteteComment'.$itemComment->com_cdn.'" class="enteteComment" '.
                                   affFoto($itemComment->com_auteur_no).'> Commentaire ajouté par : '.
                                   NomUser($itemComment->com_auteur_no).' le '.$dateComment.'</div>';
                  $listeComment .= '<div id="corpsComment'.$itemComment->com_cdn.'" class="corpsComment">'.
                                   html_entity_decode($itemComment->com_comment_cmt,ENT_QUOTES,'ISO-8859-1').'</div></div>';
                  if ($itemComment->com_auteur_no == $_SESSION['id_user'] ||  $oBody->bgbody_auteur_no == $_SESSION['id_user'])
                  {
                       $listeComment .= '<div id="suppComment'.$itemComment->com_cdn.'" class="suppComment">'.
                                   '<a href="javascript:void(0);" '.
                                   'title="Supprimer ce commentaire." '.
                                   'onClick="javascript:$(document).ready(function(){
                                        $.ajax({type: \'GET\',
                                              url: \'bloglib.php\',
                                              data: \'suppComm=1&IdComm='.$itemComment->com_cdn.'&IdBody='.$oBody->bgbody_cdn.'\',
                                              beforeSend:function()
                                              {
                                                 $(\'#affiche\').addClass(\'Status\');
                                                 $(\'#affiche\').append(\'Opération en cours....\');
                                              },
                                              success: function(msg){
                                                   $(\'#leComment'.$itemComment->com_cdn.'\').empty();
                                                   $(\'#NbComm'.$iBody.'\').html(msg);
                                                   $(\'#affiche\').empty();
                                                   $(\'#mien\').html(\'Vous venez de supprimer un commentaire\');
                                                   setTimeout(function() {$(\'#mien\').empty();},7000);
                                                   setTimeout(function() {$(\'#ItemComment'.$itemComment->com_cdn.'\').empty();},500);
                                              }
                                        });
                                    });" ><img src="images/supp.png" border="0"></a></div></div>';
                  }
                  else
                      $listeComment .= '</div>';
              }
              if ($NbComment > 0)
              {
                 $content .= '<div id="commentBlg'.$iBody.'" class= "commentBlg" onClick="javascript:$(\'#affComment'.$iBody.'\').toggle();">'.
                             '<div id="NbComm'.$iBody.'" title="Commentaires">'.$NbComment.'</div></div>';
                 $content .= '</div>';
                 $content .= '<div id="affComment'.$iBody.'" class="affComment">'.$listeComment.'</div>';
              }
              $content .= '</div>';
              $content .= '<div id="leBody'.$iBody.'" class="leBody">';
              $content .= '<div id="BgBTitre" class="BgBTitre">';
              $content .= '<input id="newBTitre'.$iBody.'" class="INPUT" style="font-size:13px;" size="80" value="'.
                          stripslashes($oBody->bgbody_titre_lb).'" /></div>';
              if (!empty($oBody->bgbody_img_no))
              {
                   $content .= '<div id="ImgShowM'.$iBody.'" '.
                               'style="clear:both;float:left;width:auto;border:1px solid #24677A;margin:5px;padding:4px;">';
                   $content .= '<img src="lib/affiche_image.php?provenance=paragraphe&numImg='.$oBody->bgbody_img_no.'">';
                   $content .= '</div>';
                   $content .= '<div id="legendeM'.$iBody.'" style="clear:both;font-size:12px;font-weight:bold;padding:4px;">'.
                               ' Légende :</div>';
              }

              $content .= '<div class="newBody">';
              $content .= '<textarea id="newBody'.$iBody.'" class=textarea>'.
                          html_entity_decode($oBody->bgbody_body_cmt,ENT_QUOTES,'ISO-8859-1').'</textarea></div>';
              $content .= '<div class="Bodycone">'.
                          '<div class="BodyLink" onClick="javascript:TinyMCE.prototype.triggerSave();'.
                          'checkIt($(\'#newBody'.$iBody.'\').val(),$(\'#newBTitre'.$iBody.'\').val(),'.
                          '\'blogOpen.php\',\'ajtBody=1&id_clan='. $_SESSION['id_user'].'&ordre='.$oBody->bgbody_order_no.
                          '&IdBody='.$oBody->bgbody_cdn.'&numImg='.$oBody->bgbody_img_no.'\');"'.
                          ' title="Appliquer les modifications"><img src="images/boutvalid.gif" border="0"></div>'.
                          '<div id="noImgMod'.$iBody.'" class="noImgMod" '.
                          'onClick="javascript:$(\'#leBody'.$iBody.'\').toggle();'.
                          '$(\'#actuel'.$iBody.'\').toggle();" title="Abandonner cette modification.">'.
                          '<img src="images/retour15.gif" border="0"></div>';
              $content .= '</div>';
              $content .= '</div>';
          }// vue complete
        }
        if ((($iBody+1) == $nbBody) && empty($_GET['ChxDte']) && empty($vuePlan))
        {
           $content .= AjtElm($iBody+1,$oBody,1);
        }
    $iBody++;
   }
}
elseif ($nbMeta > 0 && $nbBody == 0 && $id_clan == $_SESSION['id_user'])
{
   $content .= '<div id="BgBTitre" class="BgBTitre">';
   $content .= '<input id="newBTitreN" class="newBTitreN" size="80" value="" /></div>';
   $content .= '<div id="BgBody" class="BgBody">';
   $content .= '<textarea id="newBodyN" class=textarea></textarea>';
   $content .='<a href="javascript:checkIt($(\'#newBodyN\').val(),$(\'#newBTitreN\').val(),'.
              '\'blogOpen.php\',\'ajtBody=1&first=1&id_clan='.$_SESSION['id_user'].'\');" '.
              ' onClick="TinyMCE.prototype.triggerSave();">'.
              '<img src="images/boutvalid.gif" border="0"></a>';
   $content .= '</div>';
}
echo $content;
echo '<div id="affiche"></div>';
echo '<div id="mien" class="cms" style="margin:120px 0 0 280px;" title="Cliquer pour fermer cette alerte" '.
     'onClick="javascript:$(this).empty();"></div>';
?>
