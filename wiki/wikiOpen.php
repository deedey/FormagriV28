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
require "wikiClass.php";
dbConnect();
if (!empty($_POST['newCmt']) && $_POST['newCmt'] != '')
{
     $idCmt = Donne_ID ($connect,"select max(com_cdn) from commentaires");
     $insertCmt = mysql_query("insert into commentaires (com_cdn,com_auteur_no,comwk_body_no,com_comment_cmt,com_date_dt)values($idCmt,".$_SESSION['id_user'].",".$_GET['numBody'].",\"".
                             htmlspecialchars($_POST['newCmt'],ENT_QUOTES,'ISO-8859-1')."\",\"".date("Y-m-d H:i:s" ,time())."\")");
     $reqwkMail = GetDataField($connect,"select wkbody_titre_lb from wikibodies where wkbody_cdn=$numBody", "wkbody_titre_lb");
     $reqwkAuteur = GetDataField($connect,"select wkbody_auteur_no from wikibodies where wkbody_cdn=".$_GET['numBody'], "wkbody_auteur_no");
     if ($_SESSION['id_user'] != $reqwkAuteur)
         envoiMailCmt($numBody,$reqwkAuteur,'WikiDoc',htmlspecialchars($reqwkMail,ENT_QUOTES,'ISO-8859-1'),htmlspecialchars($_POST['newCmt'],ENT_QUOTES,'ISO-8859-1'));
     $mess_notif = 'Votre commentaire a été inséré';
}
if (!empty($_POST['ajtNewBodyImg']) && $_POST['ajtNewBodyImg'] == 1)
{
   $mess_notif = '';
   //echo "<pre>";print_r($_POST);echo "</pre>";
   if (!empty($_POST['id_wkimg']) && $_POST['id_wkimg'] > 0)
      $id_wkimg = $_POST['id_wkimg'];
   elseif(empty($_FILES['userfile']['tmp_name']))
   {
      $id_wkimg = 0;
      $userfile = 0;
   }
   if (!empty($_FILES['userfile']['tmp_name']) && is_uploaded_file($_FILES['userfile']['tmp_name']) && (!isset($_POST['id_wkimg']) ||
      ((isset($_POST['id_wkimg']) && $_POST['id_wkimg'] == 0))))
   {
       $ImgTemp = $_FILES['userfile']['tmp_name'];
       $fichier = "TempFiles/".$_FILES['userfile']['name'];
       $nom_fichier = modif_nom($_FILES['userfile']['name']);
       list($extension, $nom) = getextension($nom_fichier);
       if ($extension != 'jpeg' && $extension != 'jpg' && $extension != 'gif' && $extension != 'png')
       {
           $Img = '';
           $mess_notif .= 'L\'image n\'a pas été prise en compte:<br />'.
                          'Votre image n\'a pas une extension de type jpg, jpeg, gif ou png<br />';
           $id_wkimg = '100000';
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
               $id_wkimg = '100000';
           }
           else
           {
               $id_wkimg = Donne_ID ($connect,"select max(wkimg_cdn) from wikimg");
               $reqAjt = mysql_query("insert into wikimg values('$id_wkimg',\"".addslashes($Img)."\",'".$_SESSION['id_user']."')");
               $mess_notif .= '';
           }
       }
   }
   if ($_POST['position'] == 0 && (empty($id_wkimg) || (!empty($id_wkimg) && $id_wkimg != 100000)))
   {
        $reqAfter = mysql_query("select * from wikibodies where wkbody_clan_no='".$_GET['id_clan'].
                                   "' and wkbody_order_no >='".$_POST['newOrdWk']."' order by wkbody_order_no desc");
        $CptOrdre = $_POST['newOrdWk'];
        while ($oAfter = mysql_fetch_object($reqAfter))
        {
             $CptOrdre = $oAfter->wkbody_order_no+1;
             $reqModif= mysql_query("update wikibodies set wkbody_order_no = '$CptOrdre' where wkbody_order_no='".$oAfter->wkbody_order_no."' and wkbody_clan_no='".$_GET['id_clan']."'");
        }
   }
   if ($id_wkimg != 100000)
   {
      $id_wk = Donne_ID ($connect,"select max(wkbody_cdn) from wikibodies");
      $reqAjt = mysql_query("insert into wikibodies values('$id_wk','".$_SESSION['id_user']."','".$_GET['id_clan']."',\"".
                             clean_text(htmlspecialchars($_POST['legende'],ENT_QUOTES,'ISO-8859-1'))."\",\"".
                             clean_text(addslashes($_POST['titreImg']))."\",'$id_wkimg','".$_POST['newOrdWk']."','1','".time()."')");
      $mess_notif = "Votre insertion a été effectuée dans le document en cours.";
   }
}
if (!empty($_GET['SuppBody']) && $_GET['SuppBody'] == 1)
{
     $supprimer = mysql_query("delete from wikibodies where wkbody_cdn=".$_GET['idBody']);
     $mess_notif = "Vous venez de supprimer une instance de ce paragraphe<br />".
                   "Si d'autres instances du même paragraphe existent, elles sont toujours actives.\')";
}

if (!empty($_GET['ajTitre']) && $_GET['ajTitre'] == 1)
{
   if (!empty($_GET['IdMeta']))
       $NbTitle = mysql_num_rows(mysql_query("select wkmeta_titre_lb from wikimeta where wkmeta_cdn = ".$_GET['IdMeta']));
   if ($NbTitle > 0)
      $titreOld = GetDataField ($connect,"select wkmeta_titre_lb from wikimeta where wkmeta_cdn = ".$_GET['IdMeta'],"wkmeta_titre_lb");
   if ((stripslashes($titreOld) != $_GET['newTitre'] && $NbTitle > 0) || $NbTitle == 0)
   {
       $id_wk = Donne_ID ($connect,"select max(wkmeta_cdn) from wikimeta");
       $reqAjt = mysql_query("insert into wikimeta values('$id_wk','".$_GET['id_clan']."','".$_SESSION['id_user']."',\"".
                        clean_text(addslashes($_GET['newTitre']))."\",\"font-size:24px;\",'".time()."')");
       $mess_notif = "Vous venez de créer ou de modifier le titre du document.";
   }
   else
       $mess_notif = "Vous n'avez apporté aucun changement au titre...<br />".
                     "Nul besoin de valider quoi que ce soit dans ce cas!!!";

}
if (!empty($_GET['ajtBody']) && $_GET['ajtBody'] == 1)
{
   $id_wk = Donne_ID ($connect,"select max(wkbody_cdn) from wikibodies");
   if (!empty($_GET['ordre']))
   {
       $order_wk = $_GET['ordre'];
       $bodyOld = GetDataField ($connect,"select wkbody_body_cmt from wikibodies where wkbody_cdn = ".$_GET['IdBody'],"wkbody_body_cmt");
       $titreOld = GetDataField ($connect,"select wkbody_titre_lb from wikibodies where wkbody_cdn = ".$_GET['IdBody'],"wkbody_titre_lb");
   }
   else
       $order_wk = Donne_ID ($connect,"select max(wkbody_order_no) from wikibodies where wkbody_clan_no=".$_GET['id_clan']);
   if ((!empty($_GET['ordre']) && ($bodyOld != htmlspecialchars($_POST['newBody'],ENT_QUOTES,'ISO-8859-1') || stripslashes($titreOld) != clean_text($_GET['newTitre']))) || empty($_GET['ordre']))
   {
      $reqAjt = mysql_query("insert into wikibodies values('$id_wk','".$_SESSION['id_user']."','".$_GET['id_clan']."',\"".
                        htmlspecialchars($_POST['newBody'],ENT_QUOTES,'ISO-8859-1')."\",\"".
                        clean_text(addslashes($_GET['newTitre']))."\",'".$_GET['numImg']."','$order_wk','1','".time()."')");
      $mess_notif = "Vous venez de créer ou de modifier un paragraphe.";
   }
   else
   {
      $mess_notif = "Vous n'avez apporté aucun changement ni au titre ni au paragraphe...<br />".
                    "Nul besoin de valider quoi que ce soit dans ce cas !!!";
   }
}
$nom_user = $_SESSION["name_user"];
$prenom_user = $_SESSION["prename_user"];
$email_user = $_SESSION["email_user"];
include ("../style.inc.php");
if (!empty($_GET['numApp'])) $numApp = $_GET['numApp'];
if (!empty($_GET['id_seq'])) $id_seq = $_GET['id_seq'];
if (!empty($_GET['id_parc'])) $id_parc = $_GET['id_parc'];
if (!empty($_GET['numero_groupe'])) $numero_groupe = $_GET['numero_groupe'];
if (!empty($_GET['numeroApp'])) $numeroApp = $_GET['numeroApp'];
if (!empty($_GET['id_clan'])) $id_clan = $_GET['id_clan'];
if (!empty($_GET['id_grp'])) $id_grp = $_GET['id_grp'];
if (!empty($_GET['vuePlan'])) $vuePlan = $_GET['vuePlan'];
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
                /*if (isEmpty(frm.userfile)==true)
                    ErrMsg += ' - Image non chargée\n';*/
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
$listeClan1='';
$reqWkApp = mysql_query("select * from wiki,wikiapp where wkapp_clan_nb=".$_GET['id_clan']." and wkapp_wiki_no=wiki_cdn");
$nbWkClan = mysql_num_rows($reqWkApp);
$infos = (empty($_GET['ChxDte'])) ? "Dernière version" : "Version du ".date('d-m-Y à H:i\':s\'\'',$_GET['ChxDte']);
if ($nbWkClan > 1)
{
   while ($oClan = mysql_fetch_object($reqWkApp))
   {
            $listeClan1 .= NomUser($oClan->wkapp_app_no).'<br />';
            $theme = $oClan->wiki_consigne_cmt;
   }
}
else
{
  $theme = mysql_result($reqWkApp,0,'wiki_consigne_cmt');
  $listeClan1 .= '<strong>Seul</strong> pour l\'instant</strong>';
}
$infos .= ' de ce travail en commun. Apprenants concernés par le thème :';
$infosGrp ='Document en ligne pour tous les apprenants de la formation:';
if (empty($id_seq))
   $id_seq = GetDataField ($connect,"select wkapp_seq_no from wikiapp where wkapp_clan_nb ='".$_GET['id_clan']."'","wkapp_seq_no");
if ($id_seq > 10000)
   $listeClan .= '<div id="infos">'.$infosGrp.'</div>'.
                 '<div id="theme"><strong>'.$theme.'</strong></div>';
else
     $listeClan .=
              '<div id="theme"><strong>'.$theme.'</strong></div>'.
              '<div style="color:#D45211;">'.$listeClan1.'</div>';
if (empty($_GET['ChxDte']))
{
   $reqBody = mysql_query("select * from wikibodies where wkbody_clan_no=".$_GET['id_clan'].
                          " and (wkbody_show_on=1 or(wkbody_show_on=0 and wkbody_auteur_no=".$_SESSION['id_user']."))
                          order by wkbody_order_no,wkbody_date_dt desc");
   $reqMeta = mysql_query("select * from wikimeta where wkmeta_clan_no =".$_GET['id_clan'].
                          " order by wkmeta_date_dt desc");
   $ChxDte='';
}
else
{
   $reqBody = mysql_query("select * from wikibodies where wkbody_clan_no=".$_GET['id_clan']." and
                          (wkbody_show_on=1 or(wkbody_show_on=0 and wkbody_auteur_no=".$_SESSION['id_user'].")) and
                          wkbody_date_dt <= ".$_GET['ChxDte']." order by wkbody_order_no,wkbody_date_dt desc");
   $reqMeta = mysql_query("select * from wikimeta where wkmeta_clan_no =".$_GET['id_clan']." and
                          wkmeta_date_dt <= ".$_GET['ChxDte']." order by wkmeta_date_dt desc");
   $ChxDte=$_GET['ChxDte'];
}
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
          $content .= '<div id="WikiInfoDoc">'.$listeClan.'</div>';
          $content .= '<div id="WikiInfoDocLong">'.$listeClan.'</div>';
          if (empty($_GET['vuePlan']))
          {
              $content .= '<div id="BlkTitDoc" class="BlkTitDoc">';
              $content .= '<div id="WikiTitDoc" style="'.$oMeta->wkmeta_style_lb.';"> '.
                          stripslashes($oMeta->wkmeta_titre_lb).'</div>';
              $content .= '<div id="modifTitDoc" class="modifTitDoc" ';
              $content .= 'onClick="javascript:$(\'#BlkTitDoc\').toggle();'.
                          '$(\'#NewTitDoc\').toggle();" title="Modifier le titre de ce document.">'.
                          '<div id="imgModTitDoc" style="display:block;"><img src="images/modif15.gif" border="0"></div></div>';
          }
          $plan = (empty($vuePlan)) ? '&vuePlan=1' : '';
          $content .= '<div id="menu_vert" style="float:left;"><div style="float:left;padding:6px 0 0 20px !important;">'.
                      'Afficher le contenu à une date précise</div>'.
                      '<span onClick="javascript:$(\'#ul25\').toggle();" title="Ouvrir / Fermer"> '.
                      '<img src="images/fleche.gif" border="0" style="margin:0 10px 3px 20px;cursor:pointer;"></span>';
          $content .= '<span onClick="javascript:document.location.replace(\'wikiOpen.php?numApp='.$numApp.
                      '&id_seq='.$id_seq.'&id_parc='.$id_parc.'&id_grp='.$id_grp.'&id_clan='.$id_clan.'\');" '.
                      ' title="Afficher la dernière version. Cela permettra de rajouter un paragraphe, une image commentée, '.
                      'le plan du document ou encore la version imprimable."> '.
                      '<img src="images/last.gif" border="0" style="margin:0 0 3px 10px;cursor:pointer;"></span>';
          $content .= '<span onClick="javascript:document.location.replace(\'wikiOpen.php?numApp='.$numApp.
                      $plan.'&id_seq='.$id_seq.'&id_parc='.$id_parc.'&id_grp='.$id_grp.'&id_clan='.
                      $id_clan.'&ChxDte='.$ChxDte.'&numerotation=alpha\');" '.
                      ' title="Afficher le plan et le détail de cette version."> '.
                      '<img src="images/plan.gif" border="0" style="margin:0 0 0 10px;padding-top:3px;cursor:pointer;"></span>';
          $content .= '<ul id="ul25" style="display:none;">';
          $content .= listWk($_GET['id_clan']);
          $content .= '</ul></div></div>';
          if (empty($_GET['vuePlan']))
          {
             $content .= '<div id="NewTitDoc" class="NewTitDoc">';
             $content .= '<input id="newTitre" class="input" style="font-size:18px;float:left;" size="40" value="'.
                         stripslashes($oMeta->wkmeta_titre_lb).'" />';
             $content .= '<input type="image" id="envoi" style="float:left;margin:0 0 0 10px;" '.
                         'src="../images/fiche_identite/boutvalid.gif" title="procéder à la modification" '.
                         'onClick="javascript:envoiTitre($(\'#newTitre\').val(),\'wikiOpen.php\',\'ajTitre=1&IdMeta='.
                         $oMeta->wkmeta_cdn.'&id_clan='.$_GET['id_clan'].'\');" />';
             $content .= '<div id="noImgMod" class="noImgMod" style="float:left;" '.
                         'onClick="javascript:$(\'#NewTitDoc\').toggle();'.
                         '$(\'#BlkTitDoc\').toggle();" title="Abandonner cette modification.">'.
                         '<img src="images/retour15.gif" border="0"></div>';
             $content .= '</div>';
          }
          else
          {
             $content .= '<div id="LePlan" style="clear:both;float:left;text-align:left;'.
                         'max-width:800px;;font-size:13px;font-weight:bold;margin:20px 0 10px 10px;" '.
                         'onMouseOver="$(\'#NumeroPlan\').removeClass();$(\'#CacherUl\').removeClass();" '.
                         'onMouseOut="$(\'#NumeroPlan\').addClass(\'opacite\');$(\'#CacherUl\').addClass(\'opacite\');">'.
                         '<div id="TitrePlan" style="float:left;cursor:pointer;margin-top:4px;" '.
                         'onClick="javascript:$(\'.BlkBody\').toggle();" title="Cliquez pour inverser l\'affichage.">';
             $content .= stripslashes($oMeta->wkmeta_titre_lb);
             $content .= '</div>';
             $content .= '<div id="NumeroPlan" class="opacite" style="float:left;border:1px solid #aaa;background-color:#eee;padding:3px;'.
                         'margin-left:10px;" title= "Choisissez le mode de numérotation des paragraphes">'.
                         '<div id="NumPrg" style="float:left;margin-left:10px;cursor:pointer;" '.
                         'onClick="javascript:document.location.replace(\'wikiOpen.php?vuePlan=1&numApp='.$numApp.
                         '&id_seq='.$id_seq.'&id_parc='.$id_parc.'&id_grp='.$id_grp.'&id_clan='.$id_clan.
                         '&ChxDte='.$ChxDte.'&numerotation=alpha\');">A-Z</div>';
             $content .= '<div id="NumPrg" style="float:left;margin-left:10px;cursor:pointer;" '.
                         'onClick="javascript:document.location.replace(\'wikiOpen.php?vuePlan=1&numApp='.$numApp.
                         '&id_seq='.$id_seq.'&id_parc='.$id_parc.'&id_grp='.$id_grp.'&id_clan='.$id_clan.
                         '&ChxDte='.$ChxDte.'&numerotation=romain\');">I-XXVI</div>';
             $content .= '<div id="NumPrg" style="float:left;margin-left:10px;cursor:pointer;" '.
                         'onClick="javascript:document.location.replace(\'wikiOpen.php?vuePlan=1&numApp='.$numApp.
                         '&id_seq='.$id_seq.'&id_parc='.$id_parc.'&id_grp='.$id_grp.'&id_clan='.$id_clan.
                         '&ChxDte='.$ChxDte.'&numerotation=numeric\');">1-n</div>';
             $content .= '</div>';
             $content .= '<div id="CacherUl" class="opacite" style="float:left;border:1px solid #aaa;background-color:#eee;padding:3px;'.
                         'margin-left:10px;cursor:pointer;" title= "Impression d\'écran ou affichage à l\'écran" '.
                         ' onClick="$(\'#menu_vert\').toggle();$(\'#infos\').toggle();'.
                         '$(\'#WikiInfoDoc\').toggle();$(\'#WikiInfoDocLong\').toggle();">';
             $content .= 'Mode</div>';
             $content .= '</div>';
           }

       }
     $iMeta++;
   }
   //echo "<pre>";print_r($TabMeta);echo "</pre>";
}
elseif($nbMeta == 0 && $nbBody == 0)
{
   $content .= '<div id="WikiInfoDoc">'.$listeClan.'</div>';
   $content .= '<div id="WikiInfoDocLong">'.$listeClan.'</div>';
   $content .= '<div id="WkTitre" class="BlkTitDoc">';
   $content .= '<div style="clear:both;float:left;" title="Créez ici le titre de votre document afin '.
               'd\'avoir accés à la création du premier paragraphe.">'.
               '<input id="newTitre" class="INPUT" style="font-size:18px;" size="35" />'.
               '</div>';
   $content .= '<div style="float:left;padding:0 0 0 5px;"> <a href="javascript:void();" '.
               'onClick="javascript:envoiTitre($(\'#newTitre\').val(),\'wikiOpen.php\',\'ajTitre=1&id_clan='.$_GET['id_clan'].'\');">'.
               '<img src="images/boutvalid.gif" border="0"></a></div></div>';
   $content .= '</div>';
}
elseif($nbMeta == 0 && $nbBody > 0)
{
   $content .= '<div id="WikiInfoDoc">'.$listeClan.'</div>';
   $content .= '<div id="WikiInfoDocLong">'.$listeClan.'</div>';
   $content .= '<div id="BlkTitDoc" class="BlkTitDoc">';
   $content .= '<div id="WikiTitDoc" style="'.$oMeta->wkmeta_style_lb.';color:red !important;"> '.
               ' Aucun titre n\'avait été saisi à cette date</div>';
   $content .= '<div id="menu_vert" style="float:left;"><span>Afficher le contenu à une date précise</span>'.
               '<span onClick="javascript:$(\'#ul25\').toggle();" title="Ouvrir / Fermer"> '.
               '<img src="images/fleche.gif" border="0" style="margin:0 10px 3px 20px;cursor:pointer;"></span>';
   $content .= '<span onClick="javascript:document.location.replace(\'wikiOpen.php?numApp='.$numApp.
               '&id_seq='.$id_seq.'&id_parc='.$id_parc.'&id_grp='.$id_grp.'&id_clan='.$id_clan.'\')"; '.
               ' title="Afficher la dernière version. Cela permettra de rajouter un paragraphe, une image commentée, '.
               'le plan du document ou encore la version imprimable"> '.
               '<img src="images/last.gif" border="0" style="margin:0 0 3px 10px;cursor:pointer;"></span>'.
   $content .= '<span onClick="javascript:document.location.replace(\'wikiOpen.php?vuePlan=1&numApp='.$numApp.
               '&id_seq='.$id_seq.'&id_parc='.$id_parc.'&id_grp='.$id_grp.'&id_clan='.$id_clan.'\')"; '.
               ' title="Passer en mode Plan et Affichage de la dernière version."> '.
               '<img src="images/plan.gif" border="0" style="margin:0 0 0 10px;padding-top:3px;cursor:pointer;"></span>';
   $content .= '<ul id="ul25" style="display:none;">';
   $content .= listWk($_GET['id_clan']);
   $content .= '</ul></div></div>';
}
if ($nbBody > 0)
{
   $NbOrdrExist = mysql_num_rows(mysql_query("select distinct wkbody_order_no from wikibodies where wkbody_clan_no=".$_GET['id_clan']));
   $iBody = 0;
   $nbPrg = 0;
   $vueBody = 0;
   $TabBody = array();
   while($oBody = mysql_fetch_object($reqBody))
   {
       array_push($TabBody,$oBody);
       $ReqComment = mysql_query("select * from commentaires,utilisateur where comwk_body_no=".$oBody->wkbody_cdn.
                                        " and utilisateur.util_cdn = com_auteur_no order by com_date_dt");
       $NbComment = mysql_num_rows($ReqComment);
       $ReqStar = mysql_query("select * from starating,utilisateur where wkstar_body_no=".$oBody->wkbody_cdn.
                                        " and utilisateur.util_cdn = starate_auteur_no order by starate_date_dt");
       $NbStar = mysql_num_rows($ReqStar);
       if (((empty($_GET['IdOrdre']) || (!empty($_GET['IdOrdre']) && $oBody->wkbody_order_no != $_GET['IdOrdre'])) &&
           (($iBody > 0 && $TabBody[$iBody-1]->wkbody_order_no != $TabBody[$iBody]->wkbody_order_no) || $iBody == 0)) ||
           (!empty($_GET['IdOrdre']) && $oBody->wkbody_cdn == $_GET['IdCdn'] && $oBody->wkbody_order_no == $_GET['IdOrdre']))
       {
          $nbPrg++;
          if (!empty($_GET['vuePlan']))
          {
             $vueBody++;
             $content .= vuePlan($iBody,$oBody,$vueBody,$_GET['numerotation']);
          }//fin vue plan
          else
          {
              if ((isset($TabBody[$iBody-1]) && $TabBody[$iBody-1]->wkbody_order_no != $TabBody[$iBody]->wkbody_order_no &&
                   empty($_GET['IdOrdre'])) || !empty($_GET['IdOrdre']) && empty($_GET['ChxDte']))
              {
                   $content .= AjtElm($iBody,$oBody,0);
              }
              $content .= '<div id="completBody'.$iBody.'" class="completBody">';
              $content .= '<div id="actuel'.$iBody.'" class="BodyActuel">';
              $content .= '<div id="enteteWk">'.
                          '<div id="titBShow'.$iBody.'" class="titBShow">'.
                          stripslashes($oBody->wkbody_titre_lb).'</div>';
              if (($_SESSION['id_user'] == $oBody->wkbody_auteur_no || (isset($_SESSION['DroitsWiki']) && $_SESSION['DroitsWiki'] == 1)) && $NbComment == 0 && $NbStar == 0)
              {
                  $suiTitle = (isset($_SESSION['DroitsWiki']) && $_SESSION['DroitsWiki'] == 1 &&
                              $_SESSION['id_user'] != $oBody->wkbody_auteur_no) ? " de ".NomUser($oBody->wkbody_auteur_no) : "";
                  $content .= '<div style="float:left;margin: 8px 0 0 10px;cursor:pointer;" '.
                              'onClick="javascript:document.location.replace(\'wikiOpen.php?SuppBody=1&idBody='.$oBody->wkbody_cdn.
                              '&numApp='.$numApp.'&id_seq='.$id_seq.'&id_parc='.$id_parc.'&id_grp='.$id_grp.'&id_clan='.$id_clan.'\');"'.
                              ' title="Supprimer cette instance de paragraphe'.$suiTitle.'">'.
                              '<img src="images/supp.png" border="0"></div>';
              }
              if ($_SESSION['id_user'] == $oBody->wkbody_auteur_no && $NbComment == 0 && $NbStar == 0)
              {
                  $validation = ($oBody->wkbody_show_on == 1) ? "<img src='images/visible.gif' border=0>" : "<img src='images/invisible.gif' border=0>";
                  $content .='<div id="visibilite'.$iBody.'" style="float:left;margin: 8px 0 0 10px;cursor:pointer;">'.
                           '<a href="javascript:void(0);" '.
                           'title="Changez l\'état de visibilité de cette instance de paragraphe <br />(mode brouillon ou validé)." '.
                           'onClick="javascript:$(document).ready(function(){
                                        $.ajax({type: \'GET\',
                                              url: \'wikilib.php\',
                                              data: \'valid_public=1&idPub='.$oBody->wkbody_cdn.'\',
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
                                                       $(\'#mien\').html(\'Vous venez de rendre cette instance de paragraphe <br />invisible aux autres utilisateurs (mode brouillon).\');
                                                   else
                                                       $(\'#mien\').html(\'Vous venez de rendre cette instance de paragraphe <br />visible aux autres utilisateurs.\');
                                                   $(\'#mien\').show();
                                                   setTimeout(function() {$(\'#mien\').empty();},7000);
                                              }
                                        });
                                    });" >'.
                             '<div id="afterValid'.$iBody.'" style="text-align:center; outline: 0;">'.$validation.'</div></a></div>';

              }
              $content .= '<div id="menuComp" class="historyWk" style="margin-left:10px;">';
              $content .= str_replace('&lt;','<',str_replace('&gt;','>',compareWk($iBody,$oBody)));
              $content .= '</div>';
              $content .= '<div id="HWk'.$iBody.'" class="historyWk">';
              $content .= str_replace('&lt;','<',str_replace('&gt;','>',HwWk($iBody,$oBody)));
              $content .= '</div></div>';
              if (!empty($oBody->wkbody_img_no))
              {
                   $content .= '<div id="ImgShow'.$iBody.'" '.
                               'style="clear:both;float:left;width:auto;border:1px solid #24677A;margin:5px;padding:4px;">';
                   $content .= '<img src="lib/affiche_image.php?provenance=paragraphe&numImg='.$oBody->wkbody_img_no.'">';
                   $content .= '</div>';
                   $content .= '<div id="legende'.$iBody.'" style="clear:both;font-size:12px;font-weight:bold;padding:4px;">'.
                               ' Légende :</div>';
              }
              $content .= '<div id="BodyShow" class="BodyShow">'.html_entity_decode($oBody->wkbody_body_cmt,ENT_QUOTES,'ISO-8859-1').'</div>';
              $content .= '<div id="modif'.$iBody.'" class="BodyModif" ';
              $content .= 'onClick="javascript:$(\'#leBody'.$iBody.'\').toggle();'.
                          '$(\'#actuel'.$iBody.'\').toggle();" title="Cliquez ici pour modifier.">'.
                          '<div id="imgMod'.$iBody.'" style="display:block;"><img src="images/modif15.gif" border="0"></div>';
              $content .= '</div></div>';
              $content .= '<div id="leBody'.$iBody.'" class="leBody">';
              $content .= '<div id="WkBTitre" class="WkBTitre">';
              $content .= '<input id="newBTitre'.$iBody.'" class="INPUT" style="font-size:13px;" size="80" value="'.
                          stripslashes($oBody->wkbody_titre_lb).'" /></div>';
              if (!empty($oBody->wkbody_img_no))
              {
                   $content .= '<div id="ImgShowM'.$iBody.'" '.
                               'style="clear:both;float:left;width:auto;border:1px solid #24677A;margin:5px;padding:4px;">';
                   $content .= '<img src="lib/affiche_image.php?provenance=paragraphe&numImg='.$oBody->wkbody_img_no.'">';
                   $content .= '</div>';
                   $content .= '<div id="legendeM'.$iBody.'" style="clear:both;font-size:12px;font-weight:bold;padding:4px;">'.
                               ' Légende :</div>';
              }

              $content .= '<div class="newBody">';
              $content .= '<textarea id="newBody'.$iBody.'" class=textarea>'.
                          html_entity_decode($oBody->wkbody_body_cmt,ENT_QUOTES,'ISO-8859-1').'</textarea></div>';
              $content .= '<div class="Bodycone">'.
                          '<div class="BodyLink" onClick="javascript:TinyMCE.prototype.triggerSave();'.
                          'checkIt($(\'#newBody'.$iBody.'\').val(),$(\'#newBTitre'.$iBody.'\').val(),'.
                          '\'wikiOpen.php\',\'ajtBody=1&id_clan='. $_GET['id_clan'].'&ordre='.$oBody->wkbody_order_no.
                          '&IdBody='.$oBody->wkbody_cdn.'&numImg='.$oBody->wkbody_img_no.'\');"'.
                          ' title="Appliquer les modifications"><img src="images/boutvalid.gif" border="0"></div>'.
                          '<div id="noImgMod'.$iBody.'" class="noImgMod" '.
                          'onClick="javascript:$(\'#leBody'.$iBody.'\').toggle();'.
                          '$(\'#actuel'.$iBody.'\').toggle();" title="Abandonner cette modification.">'.
                          '<img src="images/retour15.gif" border="0"></div></div>';
              $content .= '</div>';
              $content .= '<div id="AjtComment'.$iBody.'" class="AjtComment" onClick="javascript:$(\'#BlkComment'.$iBody.'\').toggle();" '.
                          'title="Cliquez pour saisir votre commentaire">Rédigez un commentaire</div>';
              $content .= '<div id="BlkComment'.$iBody.'" class="BlkComment" >'.
                          addComment($oBody->wkbody_cdn,$numApp,$iBody,0).'</div>';
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
                  if ($itemComment->com_auteur_no == $_SESSION['id_user'] ||  $oBody->wkbody_auteur_no == $_SESSION['id_user'])
                  {
                       $listeComment .= '<div id="suppComment'.$itemComment->com_cdn.'" class="suppComment">'.
                                   '<a href="javascript:void(0);" '.
                                   'title="Supprimer ce commentaire." '.
                                   'onClick="javascript:$(document).ready(function(){
                                        $.ajax({type: \'GET\',
                                              url: \'wikilib.php\',
                                              data: \'suppComm=1&IdComm='.$itemComment->com_cdn.'&IdBody='.$oBody->wkbody_cdn.'\',
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
                                                   setTimeout(function() {$(\'#mien\').empty();},5000);
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
                 $content .= '<div id="affComment'.$iBody.'" class="affComment">'.$listeComment.'</div>';
              }
              if (rated($oBody->wkbody_cdn) > 0)
              {
                $content .= '<div style="float:right;"><input type="hidden" value='.
                           rateIt($oBody->wkbody_cdn).' step=.5 readonly=true id="backing'.$iBody.'">';
                $debutTitle = ($oBody->wkbody_auteur_no != $_SESSION['id_user']) ? 'Vous avez donné la note de '.myrated($oBody->wkbody_cdn).'/5 à cet article <br /> ' : '';
                $content .= '<div id="rateit'.$iBody.'" title="'.$debutTitle;
                $content .= 'Moyenne sur '.totalrated($oBody->wkbody_cdn).' notes :'.rateIt($oBody->wkbody_cdn).' / 5">';
                $content .= '</div></div>';
                  ?>
                  <script type ="text/javascript">
                    $(document).ready(function() {
                          $('#rateit<?php echo $iBody;?>').rateit({ max: 5, step: 1, backingfld: '#backing<?php echo $iBody;?>'});
                    });
                  </script>
                  <?php
              }
              elseif(rated($oBody->wkbody_cdn) == 0 && totalrated($oBody->wkbody_cdn) > 0 && $oBody->wkbody_auteur_no == $_SESSION['id_user'])
              {
                  $content .= '<div style="float:right;"><input type="hidden" value='.
                           rateIt($oBody->wkbody_cdn).' step=.5 readonly=true id="backing'.$iBody.'">';
                  $content .= '<div id="rateit'.$iBody.'" title="';
                  $content .= 'Moyenne sur '.totalrated($oBody->wkbody_cdn).' notes :'.rateIt($oBody->wkbody_cdn).' / 5">';
                  $content .= '</div></div>';
                  ?>
                  <script type ="text/javascript">
                    $(document).ready(function() {
                          $('#rateit<?php echo $iBody;?>').rateit({ max: 5, step: 1, backingfld: '#backing<?php echo $iBody;?>'});
                    });
                  </script>
                  <?php
              }
              elseif(rated($oBody->wkbody_cdn) == 0 && $oBody->wkbody_auteur_no != $_SESSION['id_user'])
              {
                $content .= '<div style="float:right;"><input type="hidden" value='.
                            rateIt($oBody->wkbody_cdn).' step=1  id="backing'.$iBody.'">';
                $content .= '<div id="rateit'.$iBody.'" title="Cliquez sur le sens '.
                            'interdit pour réinitialiser puis sur une étoile pour attribuer votre note.';
                if (totalrated($oBody->wkbody_cdn) > 0)
                    $content .= '<br /> Moyenne sur '.totalrated($oBody->wkbody_cdn).' notes :'.rateIt($oBody->wkbody_cdn).' / 5">';
                else
                    $content .= '">';
                $content .= '</div></div>';
                ?>
                <script type ="text/javascript">
                $(document).ready(function() {
                      $('#rateit<?php echo $iBody;?>').rateit({ max: 5, step: 1, backingfld: '#backing<?php echo $iBody;?>'});
                });
                $(document).ready(function() {
                    $('#rateit<?php echo $iBody;?>').bind('rated', function (e) {
                    $(this).rateit('readonly',true);
                    $(this).attr('title', 'Vous lui avez attribué la note de '+ $('#backing<?php echo $iBody;?>').val()+'/ 5');
                    $.ajax({
                                url: 'wikilib.php',
                                data: { value: $('#backing<?php echo $iBody;?>').val(),IdBody : <?php echo $oBody->wkbody_cdn;?> },
                                type: 'POST',
                                beforeSend:function()
                                {
                                    $('#affiche').addClass('Status');
                                    $('#affiche').append('Opération en cours....');
                                },
                                success: function (data) {
                                    $('#affiche').empty();$('#mien').empty();
                                    $('#mien').html('Vous venez d\'attribuer '+$('#backing<?php echo $iBody;?>').val()+'/5 à ce paragraphe.');
                                    $('#mien').show();setTimeout(function() {$('#mien').empty();},7000);
                                }
                            });
                      });
                 });
                 </script>
                 <?php
               }
              //$content .= rateIt($iBody);
          }// vue complete//
          $content .= '</div>';
        }

        if (($iBody+1) == $nbBody && empty($_GET['ChxDte']) && empty($_GET['vuePlan']))
        {
           $content .= AjtElm($iBody+1,$oBody,1);
        }
    $iBody++;
   }
}
elseif ($nbMeta > 0 && $nbBody == 0)
{
   $content .= '<div id="WkBTitre" class="WkBTitre">';
   $content .= '<input id="newBTitreN" class="newBTitreN" size="80" value="" /></div>';
   $content .= '<div id="WkBody" class="WkBody">';
   $content .= '<textarea id="newBodyN" class=textarea></textarea>';
   $content .='<a href="javascript:checkIt($(\'#newBodyN\').val(),$(\'#newBTitreN\').val(),'.
              '\'wikiOpen.php\',\'ajtBody=1&id_clan='.$_GET['id_clan'].'\');" '.
              ' onClick="TinyMCE.prototype.triggerSave();">'.
              '<img src="images/boutvalid.gif" border="0"></a>';
   $content .= '</div>';
}
echo $content;
echo '<div id="affiche"></div>';
echo '<div id="mien" class="cms" style="margin:120px 0 0 280px;" title="Cliquer pour fermer cette alerte" '.
     'onClick="javascript:$(this).empty();"></div>';
?>
