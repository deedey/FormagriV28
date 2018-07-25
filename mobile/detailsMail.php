<?php
session_start();
if (!isset($_SESSION['IDUSER']) || $_SESSION['IDUSER'] == "")
{
  exit();
}
require ("../admin.inc.php");
require ("../fonction.inc.php");
require ("../fonction_html.inc.php");
require ("../langfr.inc.php");
dbConnect();
include ("../include/varGlobals.inc.php");
$affiche='';
if (isset($_GET['idMail']))
{
   $NbrMails = mysql_num_rows(mysql_query("SELECT * from messagerie where id_user = ".$_SESSION['IDUSER'].
                                          " AND lu = '1'  AND supprime = '0' ORDER BY mess_cdn desc limit 99"));
   $requete_mail = mysql_query("SELECT * from messagerie,utilisateur where mess_cdn = ".$_GET['idMail']." and envoyeur=util_cdn");
   $Emails = mysql_num_rows($requete_mail);
   if ($Emails > 0)
   {
      $luMail = mysql_query("update messagerie set lu = 0 where mess_cdn = ".$_GET['idMail']);
      $itemMail = mysql_fetch_object($requete_mail);
      $date = $itemMail->date;
      $subject = $itemMail->origine;
      $contenu = $itemMail->contenu;
      $auteur = $itemMail->envoyeur;
      $fichier = $itemMail->mess_fichier_lb;
      if (!empty($fichier) && !strstr($fichier,'http://'))
          $fichier = $_SESSION['LMS'].'/'.$fichier;
      $mailAuteur = $itemMail->util_email_lb;
      $majuscule = $itemMail->util_prenom_lb." ".$itemMail->util_nom_lb;
      $Expediteur = $itemMail->id_user;
      $MailUser = GetDataField ($connect,"select util_email_lb from utilisateur WHERE util_cdn = '$Expediteur'","util_email_lb");
      $boutons = '<div style="float:right;padding:2px;">';
      $boutons .= '<div style="float:left;cursor:pointer;" '.
                  'onClick="$(\'#commencer\').load(\'detailsMail.php?mailAuteur='.$mailAuteur.
                  '&repondre=1&idMail='.$_GET['idMail'].'\');">'.
                  '<img src="assets/images/repondre.gif" title="Répondre"></div>';
      $boutons .= '<div style="float:left;padding-left:10px;cursor:pointer;" '.
                  'onClick="javascript:$(document).ready(function(){
                                        $.ajax({type: \'GET\',
                                              url: \'NonLuMail.php\',
                                              data: \'idMail='.$_GET['idMail'].'\',
                                              success: function(msg){
                                                   alert(msg);
                                              }
                                        });
                                    });
                                    $(\'#commencer\').load(\'lireMails.php?nonLu=1\');
                                    $(\'#NbrMails\').html(\'('.$NbrMails.' mails non lus )\');">'.
                  '<img src="assets/images/nonlu.jpg" title="Considérer comme Non Lu"></div>';
      $boutons .= '<div style="float:left;padding-left:10px;cursor:pointer;" '.
                  'onClick="javascript:$(document).ready(function(){
                                        $.ajax({type: \'GET\',
                                              url: \'poubelleMail.php\',
                                              data: \'idMail='.$_GET['idMail'].'\',
                                              success: function(msg){
                                                   alert(msg);
                                              }
                                        });
                                    });
                                    $(\'#NbrMails\').html(\'('.($NbrMails-1).' mails non lus )\');
                                    $(\'#commencer\').load(\'lireMails.php\');">'.
                  '<img src="assets/images/poubelle.gif" title="Mettre à la corbeille"></div>';
      $boutons .= '<div style="float:left;padding-left:10px;cursor:pointer;" '.
                  'onClick="javascript:$(document).ready(function(){
                                        $.ajax({type: \'GET\',
                                              url: \'supprimeMail.php\',
                                              data: \'idMail='.$_GET['idMail'].'\',
                                              success: function(msg){
                                                   alert(msg);
                                              }
                                        });
                                    });
                                    $(\'#commencer\').load(\'lireMails.php\');
                                    $(\'#NbrMails\').html(\'('.($NbrMails-1).' mails non lus )\');">'.
                  '<img src="assets/images/erase.png" title="Supprimer définitivement"></div>';
      $boutons .= '</div>';
      if ($fichier != ""){
          if (!strstr(strtolower($fichier),'jpg') && !strstr(strtolower($fichier),'jpeg') &&
              !strstr(strtolower($fichier),'gif') && !strstr(strtolower($fichier),'png'))
             $leFile = '<div><img src="'.$_SESSION['LMS'].'/images/messagerie/icoGtrombon.gif"></div>';
          else
             $leFile = '<div style="clear:both;padding:4px;">'.
                       '<span style="font-weight:bold;font-size:12px;">Image jointe</span>'.
                       '<br/><img src="'.$fichier.'" style="padding:5px;border:1px solid #bbb;"></div>';
     }else
          $leFile ='';
      $affiche .= '<div id="mail" style="clear:both;float:left;background-color:#eee;border:1px solid #999;'.
                  'font-size:14px;width:95%;padding:4px;margin-top:6px;color:#000;max-width:600px;">'.
                  '<div style="clear:both;float:left;" >';
      if (strstr(substr($date,0,4),'/'))
         $affiche .= 'Mail reçu le '.substr($date,0,10).' à '.substr($date,11,2).'h'.
                      substr($date,14,2).' provenant de <span style="font-weight:bold;">'.$majuscule.'</span>';
      else
         $affiche .= 'Mail reçu le '.substr($date,8,2).'/'.substr($date,5,2).'/'.
                      substr($date,0,4) .' à '.substr($date,11,2).'h'.
                      substr($date,14,2).' provenant de <span style="font-weight:bold;">'.$majuscule.'</span>';
      $affiche .= '</div>'.$boutons;
      if (isset($_GET['repondre']))
         $affiche .= '<div id="bloc" style="display:none;">';
      elseif (isset($_GET['idMail']))
         $affiche .= "<div id='bloc' style='display:block;'>";
      else
         $affiche .= "<div id='bloc' style='display:block;'>";
      $affiche .= "<div style='clear:both;font-style:italic;padding:5px;'>".
                  "<span style='font-weight:bold;font-size:12px;font-style:normal;'>Titre</span><br/>";
      $affiche .= $subject;
      $affiche .= "</div>";
      $affiche .= $leFile;
      $affiche .= "<div id='contenu' style='clear:both;padding:5px;'>".
                  "<span style='font-weight:bold;font-size:12px;'>Contenu</span><br/>";
      $affiche .= $contenu;
      $affiche .= "</div>";
      $affiche .= "</div>";
      $affiche .= '<div style="clear:both;margin-bottom:2px;font-size:12px;color:#000;cursor:pointer;" >'.
                  '<a class="ui-btn ui-shadow ui-btn-corner-all ui-mini ui-btn-inline ui-btn-icon-left '.
                  'ui-btn-up-b" data-wrapperels="span" data-iconshadow="true" data-shadow="true" '.
                  'data-corners="true" href="#" data-role="button" data-mini="true" data-inline="true" '.
                  'data-theme="b" onClick="$(\'#commencer\').load(\'lireMails.php\');" >'.
                  '<span class="ui-btn-inner"><span class="ui-btn-text">Revenir à la liste des mails</span>'.
                  '</span></a></div>';
      if (isset($mailAuteur) && !empty($mailAuteur) && isset($_GET['repondre']))
         $affiche .= "";
      else
         $affiche .= "</div>";
         echo utf2Charset($affiche,'iso-8859-1');
   }
}
if (isset($mailAuteur) && !empty($mailAuteur) && isset($_GET['repondre']))
{
    $content = '<div id="Emailing" style="clear:both;background-color:#ddd;border:1px solid #999;
               font-size:14px;width:95%;margin-top:20px;color:#000;max-width:600px;">
               <div style="margin:0;">
                <div style="clear:both;font-weight:bold;margin:4px;">
                    <label for="sujet">Sujet</label><span style="color:red;padding-left:2px;">*</span>
                </div>
                <div style="margin:4px;font-size:20px;">
                    <INPUT TYPE="text" class="input" style="font-size:11px;width:95%;"
                    name="sujet" id="sujet" value="Re:'.stripslashes($subject).'"  />
                </div>
                <INPUT TYPE="hidden" name="go" id="track" value="1" />
                <div style="clear:both;font-weight:bold;margin:4px;">
                    <label for="message">Message</label><span style="color:red;padding-left:2px;">*</span>
                </div>
                <div style="margin:4px;font-size:15px;">
                    <TEXTAREA cols="30" id="message" NAME="message" style="width:95%;"></TEXTAREA />
                </div>
                <div style="clear:both;float:left;margin:4px;font-size:20px;">';
    $content .= '<a class="ui-btn ui-shadow ui-btn-corner-all ui-mini ui-btn-inline ui-btn-icon-left ui-btn-up-b" '.
                'data-wrapperels="span" data-iconshadow="true" data-shadow="true" data-corners="true" href="#" '.
                'data-role="button" data-mini="true" data-inline="true" data-icon="check" data-theme="b" id="envoiMail" '.
                'onClick="javascript:$(document).ready(function(){'.
                           'if ($(\'#sujet\').val() == \'\' || $(\'#message\').val() == \'\')'.
                               '$(\'#Alerter\').css(\'display\',\'block\');'.
                           'else{

                                        $.ajax({type: \'GET\',
                                              url: \'EnvoiMail.php\',
                                              data: \'go=1&email='.$mailAuteur.'&from='.$MailUser.
                                                    '&sujet=\'+$(\'#sujet\').val()+\'&message=\'+$(\'#message\').val(),
                                              success: function(msg){
                                                   alert(msg);
                                              }
                                        });
                                        $(\'#Emailing\').hide();$(\'#bloc\').show();
                                        $(\'#Alerter\').css(\'display\',\'none\');
                            }
                 });" >'.
                '<span class="ui-btn-inner"><span class="ui-btn-text">Envoyer</span>'.
                '<span class="ui-icon ui-icon-check ui-icon-shadow">&nbsp;</span></span></a> </div>';
    $content .= "<div style='float:left;margin:4px;font-size:20px;'>";
    $content .= '<a class="ui-btn ui-shadow ui-btn-corner-all ui-mini ui-btn-inline ui-btn-icon-left ui-btn-up-b" '.
                'data-wrapperels="span" data-iconshadow="true" data-shadow="true" data-corners="true" href="#" '.
                'data-role="button" data-mini="true" data-inline="true" data-icon="check" data-theme="b" id="cancelMail" '.
                'onClick="$(\'#Emailing\').hide();$(\'#bloc\').show();" >'.
                '<span class="ui-btn-inner"><span class="ui-btn-text">Annuler</span>'.
                '<span class="ui-icon ui-icon-check ui-icon-shadow">&nbsp;</span></span></a> </div>';
    $content .= '</div><div id="Alerter" style="clear:both;display:none;color:red;padding:2px;font-weight:bold;">'.
                'Saisissez votre texte avant d\'envoyer SVP</div></div></div>';
    echo  utf2Charset(stripslashes($content),"iso-8859-1");
}
?>
