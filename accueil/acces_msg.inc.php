<?php
/****************************************** ***************
*                                                         *
* Copyright  formagri/cnerta/eduter/enesad                *
* Dey Bendifallah                                         *
* Ce script fait partie intégrante du LMS Formagri.       *
* Il peut être modifié ou utilisé à d'autres fins.        *
* Il est libre et sous licence GPL                        *
* Les auteurs n'apportent aucune garantie                 *
*                                                         *
**********************************************************/
if (!isset($_SESSION)) session_start();
if (!isset($_SESSION['id_user']) || $_SESSION['id_user'] == "")
{
  exit();
}
require ("../admin.inc.php");
require ("../fonction.inc.php");
include ('../include/UrlParam2PhpVar.inc.php');
require ("../fonction_html.inc.php");
require ("../lang$lg.inc.php");
dbconnect();
      $agent=getenv("HTTP_USER_AGENT");
      if (strstr($agent,"MSIE") || strstr($agent,"Opera"))
         $mac=1;
      else
         $mac=2;
      $requete_mail = mysql_query("SELECT * from messagerie where id_user = $id_user AND lu = '1' AND supprime = '0' ORDER BY date desc");
      $nbr_emails = mysql_num_rows($requete_mail);
      if ($nbr_emails > 0)
      {
          $contents = $nbr_emails."\n*";
          for ($i=0;$i<$nbr_emails;$i++)
          {
                  $date = mysql_result($requete_mail,$i,"date");
                  $contents .= "|".str_replace("<br />","\n",str_replace("<br/>","\n",trim($date)));
                  $sujet = mysql_result($requete_mail,$i,"origine");
                  $contents .= "|".str_replace("<br />","\n",str_replace("<br/>","\n",trim($sujet)));
                  $contenu = mysql_result($requete_mail,$i,"contenu");
                  $chaine = "|".str_replace("<br />","\n",str_replace("<br/>","\n",str_replace("<BR>","\n",trim($contenu))));
                  $chaine = trim($contenu,"\n");
                  $contents .= "|".$chaine;
                  $auteur = mysql_result($requete_mail,$i,"envoyeur");
                  $nom_user=GetDataField ($connect,"select util_nom_lb from utilisateur WHERE util_cdn = '$auteur'","util_nom_lb");
                  $prenom_user=GetDataField ($connect,"select util_prenom_lb from utilisateur WHERE util_cdn = '$auteur'","util_prenom_lb");
                  $majuscule = $prenom_user." ".$nom_user;
                  $contents .= "|".str_replace("<br />","\n",str_replace("<br/>","\n",trim($majuscule)));
                  if ($i < $nbr_emails-1)
                     $contents .= "*";
          }
          // parse le contenu de $result retourné par le serveur construit le contenu
          // ainsi restructuré en faisant appel à Overlib géré par la fonction bulle() dans fonction_html.inc.php
          $resultat = str_replace("\n","<BR>",$contents);
          $la_serie = explode('*',$resultat);
          $nb_serie = count($la_serie)-1;
          if ($nb_serie > 0)
          {
            $si_oui = ' <div id="nbr_msg" style="float:left;font-family: arial;font-weight:bold;padding-right: 15px;">'.
                      'Vous avez '.$nb_serie.' message(s) non lu(s).</div>'.
                      '<div id="messagerie" style="float:left;padding-left: 10px;"> '.
                      '<A href="trace.php?link=messagerie.php%3Fvient_de_menu%3D" class="bouton_new">Aller à votre messagerie</A></div>';
            $ladate = array();
            $lesujet = array();
            $lecontenu = array();
            $affiche_cnx = "<TR>\n<TH valign='top' align='left' class='barre_titre'>$mess_mess_date</TH>\n<TH valign='top' align='left' class='barre_titre'>$mess_mail_origine</TH>\n<TH valign='top' align='left' class='barre_titre'>$mess_mail_sujet</TH>\n".
                      "<TH valign='top' align='left' class='barre_titre'>$mess_mail_mess</TH>\n</TR>\n";
            for ($i=1;$i < $nb_serie+1;$i++)
            {
              $laliste = explode('|',$la_serie[$i]);
              $ladate[$i] = $laliste[1];
              $lesujet[$i] = $laliste[2];
              $lecontenu[$i] = $laliste[3];
              $lauteur[$i] = $laliste[4];
              $lesujet1[$i] = (strlen($lesujet[$i]) >  15 && strlen($lesujet[$i]) >  0) ? substr($lesujet[$i],0,12).".." : $lesujet[$i];
              $lecontenu1[$i] = (strlen($lecontenu[$i]) >  45 && strlen($lecontenu[$i]) >  0) ? substr($lecontenu[$i],0,41).".." : $lecontenu[$i];
              $ladate1[$i] = (strlen($ladate[$i]) >  0) ? substr($ladate[$i],0,10).".." : "";
              $lauteur1[$i] = (strlen($lauteur[$i]) >  15 && strlen($lauteur[$i]) >  0) ? substr($lauteur[$i],0,12).".." : $lauteur[$i];
              $affiche_cnx .= couleur_tr($i,"")."\n";
              $affiche_cnx .= "<TD valign='top' align='left'>\n<DIV id='seq'>\n";
              if (strlen($ladate1[$i]) != strlen($ladate[$i]) && $ladate[$i] != '')
              {
                $ajout_date[$i] = "<A HREF=\"javascript:void(0);\" style='cursor:help;' ";
                $ajout_date[$i] .= bulle($ladate[$i],"","CENTER","ABOVE",120);
                $ajout_date[$i] .= trim($ladate1[$i])."</A>\n";
              }
              else
                $ajout_date[$i] = "<i>".trim($ladate[$i])."</i>";
              if (strlen($ladate[$i]) < 3) $ajout_date[$i] = "<i>Vide</i>";
                $affiche_cnx .= $ajout_date[$i]."</DIV></TD>";
              $affiche_cnx .= "<TD valign='top' align='left'>\n<DIV id='seq'>\n";
              if (strlen($lauteur1[$i]) != strlen($lauteur[$i]) && $lauteur[$i] != '')
              {
                $ajout_auteur[$i] = "<A HREF=\"javascript:void(0);\" style='cursor:help;' ";
                $ajout_auteur[$i] .= bulle($lauteur[$i],"","CENTER","ABOVE",120);
                $ajout_auteur[$i] .= trim($lauteur1[$i])."</A>\n";
              }
              else
                $ajout_auteur[$i] = "<i>".trim($lauteur[$i])."</i>";
              if (strlen($lauteur[$i]) < 3) $ajout_auteur[$i] = "<i>Vide</i>";
                 $affiche_cnx .= $ajout_auteur[$i]."</DIV></TD>";
              $affiche_cnx .= "<TD valign='top' align='left'><DIV id='seq'>\n";
              if (strlen($lesujet1[$i]) != strlen($lesujet[$i]) && $lesujet[$i] != '')
              {
                $ajout_sujet[$i] = "<A HREF=\"javascript:void(0);\" style='cursor:help;'";
                $ajout_sujet[$i] .= bulle($lesujet[$i],"","CENTER","ABOVE",180);
                $ajout_sujet[$i] .= trim($lesujet1[$i])."</A>\n";
              }
              else
                $ajout_sujet[$i] = "<i>".trim($lesujet[$i])."</i>";
              if (strlen($lesujet[$i]) < 1) $ajout_sujet[$i] = "<i>Vide</i>";
              $affiche_cnx .= $ajout_sujet[$i]."</DIV></TD>";
              $affiche_cnx .= "<TD valign='top' align='left'><DIV id='seq'>";

              if (strlen($lecontenu1[$i]) != strlen($lecontenu[$i]) && $lecontenu[$i] != '')
              {
                $ajout_contenu[$i] = "<A HREF=\"javascript:void(0);\" style='cursor:help;'";
                $ajout_contenu[$i] .= bulle(nl2br(str_replace("\r","",$lecontenu[$i])),"","CENTER","ABOVE",320);
                $ajout_contenu[$i] .= trim($lecontenu1[$i])."</A>\n";
              }
              else
                $ajout_contenu[$i] = "<strong>".trim(nl2br($lecontenu[$i]))."</strong>";
              if (strlen($lecontenu[$i]) < 1 )
                 $ajout_contenu[$i] = "<i>Vide</i>";
              $affiche_cnx .= $ajout_contenu[$i]."</DIV></TD>\n";
              $affiche_cnx .= "</TR>\n";
            }
            $affiche_msgs = "<center><TABLE cellpadding=1 cellspacing='1' border='0' width='98%'><TBODY><TR><TH colspan=4 nowrap>$si_oui</TH></TR>$affiche_cnx</TBODY></TABLE></center>\n";
          }
          else
          {
            $affiche_msgs = '<DIV id="sequence">'. $resultat.' sur <A HREF="trace.php?link=messagerie.php%3Fvient_de_menu%3D">Messagerie</A></div>';
          }
      }
      else
          $affiche_msgs =  $msgnolucnx;
      echo utf2Charset($affiche_msgs,$charset);

?>
