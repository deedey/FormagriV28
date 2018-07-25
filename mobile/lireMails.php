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
$requete_mail = mysql_query("SELECT * from messagerie where id_user = ".$_SESSION['IDUSER'].
                            " AND lu = '1' AND supprime = '0' ORDER BY mess_cdn desc limit 99");
$nbr_emails = mysql_num_rows($requete_mail);
if ($nbr_emails > 0)
{
          $contents = $nbr_emails."*";
          for ($i=0;$i<$nbr_emails;$i++)
          {
                  $MsgId = mysql_result($requete_mail,$i,"mess_cdn");
                  $date = mysql_result($requete_mail,$i,"date");
                  $contents .= "|".str_replace("<br />","",str_replace("<br/>","",trim($date)));
                  $sujet = mysql_result($requete_mail,$i,"origine");
                  $contents .= "|".str_replace("<br />","",str_replace("<br/>","",trim($sujet)));
                  $contenu = mysql_result($requete_mail,$i,"contenu");
                  $chaine = "|".str_replace("<br />","",str_replace("<br/>","",str_replace("<BR>","",trim($contenu))));
                  $chaine = trim($contenu,"");
                  $contents .= "|".$chaine;
                  $fichier = mysql_result($requete_mail,$i,"mess_fichier_lb");
                  if (!empty($fichier) && !strstr($fichier,'http://'))
                     $fichier = $_SESSION['LMS'].'/'.$fichier;
                  if ($fichier != ""){
                     if (!strstr(strtolower($fichier),'jpg') && !strstr(strtolower($fichier),'gif') &&
                         !strstr(strtolower($fichier),'png'))
                        $leFile[$i] = '<div style="float:left;margin-left:50px;">'.
                                      '<img src="'.$_SESSION['LMS'].'/images/messagerie/icoGtrombon.gif"></div>';
                     else
                       $leFile[$i] = '<div style="float:left;margin-left:50px;">'.
                                    '<img src="'.$fichier.'" style="width:60%"></div>';


                  }else
                     $leFile[$i] ='';
                  $auteur = mysql_result($requete_mail,$i,"envoyeur");
                  $nom_user=GetDataField ($connect,"select util_nom_lb from utilisateur WHERE util_cdn = '$auteur'","util_nom_lb");
                  $prenom_user=GetDataField ($connect,"select util_prenom_lb from utilisateur WHERE util_cdn = '$auteur'","util_prenom_lb");
                  $majuscule = $prenom_user." ".$nom_user;
                  $contents .= "|".str_replace("<br />","",str_replace("<br/>","",trim($majuscule)));
                  $contents .= "|".$MsgId;
                  if ($i < $nbr_emails)
                     $contents .= "|*|";
          }
          // parse le contenu de $result retourné par le serveur construit le contenu
          // ainsi restructuré en faisant appel à Overlib géré par la fonction bulle() dans fonction_html.inc.php
          $resultat = str_replace("","<BR>",$contents);
          $la_serie = explode('|*|',$resultat);
          $nb_serie = count($la_serie);
          if ($nb_serie > 0)
          {
            $si_oui = '<html><body> <div id="nbr_msg" style="float:left;font-family: arial;font-weight:bold;padding-right: 15px;">'.
                      'Vous avez '.$nbr_emails.' message(s) non lu(s).</div>';
            $ladate = array();
            $lesujet = array();
            $lecontenu = array();
            $lauteur = array();
            $MessId = array();
            $affiche_cnx = '';
            for ($i=0;$i < $nb_serie-1;$i++)
            { 
              $couleur = (($i/2) > floor($i/2)) ? 'background-color:#eee' : 'background-color:#fff';
              $laliste = explode('|',$la_serie[$i]);
              $ladate[$i] = substr($laliste[1],0,10);
              if (!strstr(substr($ladate[$i],0,4),'/'))
                 $ladate[$i]= substr($ladate[$i],8,2)."/".substr($ladate[$i],5,2)."/".substr($ladate[$i],0,4);
              $lesujet[$i] = $laliste[2];
              $lecontenu[$i] = $laliste[3];
              $lauteur[$i] = $laliste[4];
              $MessId[$i] = $laliste[5];
              $details = '<div style="float:left;margin-bottom:2px;font-size:12px;color:#000;cursor:pointer;" >'.
                       '<a class="ui-btn ui-shadow ui-btn-corner-all ui-mini ui-btn-inline ui-btn-icon-left '.
                       'ui-btn-up-b" data-wrapperels="span" data-iconshadow="true" data-shadow="true" '.
                       'data-corners="true" href="#" data-role="button" data-mini="true" data-inline="true" '.
                       ' data-theme="b" onClick="$(\'#commencer\').load(\'detailsMail.php?idMail='.$MessId[$i].'\');'.
                       '$(\'#NbrMails\').html(\'('.($nbr_emails-1).' mails non lus )\');" >'.
                       '<span class="ui-btn-inner"><span class="ui-btn-text">Détails</span>'.
                       '</span></a></div>';
              $affiche_cnx .= "<div id='msg".$i."'style='clear:both;float:left;".$couleur.";font-size:12px;".
                              "width:95%;padding:4px;margin-top:8px;color:#000;border:1px solid #000;'>";
              $affiche_cnx .= "<div style='clear:both;float:left;;border:1px dotted #000;padding:4px;'>";
              $affiche_cnx .= "<div style='clear:both;float:left;' id='date".$i."'>";
              $ajout_date[$i] = trim($ladate[$i]);
              $affiche_cnx .= $ajout_date[$i]."</div>";
              $affiche_cnx .= "<div style='float:left;padding-left:10px;font-weight:bold;'>";
              $ajout_auteur[$i] = trim($lauteur[$i]);
              if (strlen($lauteur[$i]) < 3) $ajout_auteur[$i] = "Vide";
              $affiche_cnx .= $ajout_auteur[$i]."</div>".$leFile[$i];
              $affiche_cnx .= "<div style='clear:both;float:left;font-style:italic;'>";
              $ajout_sujet[$i] = (strlen(strip_tags(nl2br(html_entity_decode($lesujet[$i],ENT_QUOTES,'ISO-8859-1')))) >  52) ?
                                   substr(strip_tags(nl2br(html_entity_decode($lesujet[$i],ENT_QUOTES,'ISO-8859-1'))),0,50).".." :
                                   strip_tags(nl2br(html_entity_decode($lesujet[$i],ENT_QUOTES,'ISO-8859-1')));
              if (strlen($lesujet[$i]) < 1) $ajout_sujet[$i] = "Vide";
              $affiche_cnx .= $ajout_sujet[$i]."</div>";
              $affiche_cnx .= "<div style='clear:both;float:left;'>";
              $ajout_contenu[$i] = trim($lecontenu[$i]);
              $ajout_contenu[$i] = (strlen(strip_tags(nl2br(html_entity_decode($ajout_contenu[$i],ENT_QUOTES,'ISO-8859-1')))) >  52) ?
                                   substr(strip_tags(nl2br(html_entity_decode($ajout_contenu[$i],ENT_QUOTES,'ISO-8859-1'))),0,50).".." :
                                   strip_tags(nl2br(html_entity_decode($ajout_contenu[$i],ENT_QUOTES,'ISO-8859-1')));
              if (strlen($lecontenu[$i]) < 1 )
                 $ajout_contenu[$i] = "Vide";
              $affiche_cnx .= $ajout_contenu[$i]."</div>";
              $affiche_cnx .= "</div>$details</div>";

              $affiche_cnx .= "</div>";
            }
            $affiche_msgs = "<div>$si_oui  $affiche_cnx</div></body></html>";
          }
          else
          {
            $affiche_msgs = '<div>'. $resultat.'>Messagerie</div>';
          }
      }
      else
          $affiche_msgs =  $msgnolucnx;
      echo utf2Charset($affiche_msgs,'iso-8859-1');
?>
