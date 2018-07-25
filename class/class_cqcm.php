<?php
// cqcm_class
class cqcm
{
      var $creation_qcm;
      var $id_activit;
      var $venu;
      function create_qcm()
      {
          require "lang".$this->lg.".inc.php";
          if ($this->modifie_qcm == 1 || $this->supprime_qcm == 1)
          {   $html = "";
              if ($this->modifie_qcm == 1)
                  $html .= cqcm :: lance_form("$mess_cqcm_form_modif","$mess_cqcm_quoi_modif","modification");
              else
                  $html .= cqcm :: lance_form("$mess_cqcm_form_supp","$mess_cqcm_quoi_sup","suppression");
              $html .="<td><SELECT name='intitule' class='SELECT'>\n";
              for ($mm = 1; $mm < $this->compteur; $mm++)
              {
                    if ($this->supprime_qcm == 1)
                    {
                        $req_verif = mysql_result(mysql_query("SELECT count(activite.act_cdn) from activite,ressource_new where
                                     ressource_new.ress_url_lb like \"%qcm.php?code=".$this->oBase[$mm]->ordre."%\"
                                     and activite.act_ress_no = ressource_new.ress_cdn"),0);
                        if ($req_verif == 0)
                            $html .= "<OPTION value=\"".$this->oBase[$mm]->titre_qcm."|".$this->oBase[$mm]->ordre."\">".$this->oBase[$mm]->titre_qcm."</OPTION>";
                    }
                    else
                        $html .= "<OPTION value=\"".$this->oBase[$mm]->titre_qcm."|".$this->oBase[$mm]->ordre."\">".$this->oBase[$mm]->titre_qcm."</OPTION>";
              }
              $html .= "</SELECT></td>\n";
              $html .= "<td valign='bottom'>".cqcm :: ajout_valider("javascript:document.create.submit();")."</td>\n";
              $html .= "</tr></table></td></tr></FORM>\n";
              echo $html;
             exit();
          }//fin  if ($this->modifie_qcm == 1 || $this->supprime_qcm == 1)
          elseif ($this->consulter_qcm == 1)
          {
              $html .= "<TR><TD colspan='2'><B>$mess_consult</B><p></TD></TR>\n";
              $html .= "<TR><TD colspan='2'><form name='form' id='form'><B>$mess_cqcm_tit_qcm</B>&nbsp;&nbsp;&nbsp;\n";
              $html .= "<SELECT name='select' class='SELECT' onChange=\"appel_wpop(form.select.options[selectedIndex].value);document.location='#sommet';\">";//;
              $html .= "<OPTION value=\"#\">- - - - choisissez - - - - </OPTION>\n";
              for ($mm = 1; $mm < $this->compteur; $mm++)
              {
                   $html .= "<OPTION value=\"trace.php?link=".urlencode("qcm.php?code=".$this->oBase[$mm]->ordre)."\">".$this->oBase[$mm]->titre_qcm."</OPTION>";
              }
              $html .= "</SELECT></FORM>\n";
              $html .= "</td></tr>\n";
              echo $html;
              echo boutret(1,0);
             exit;
          }//fin  elseif ($this->consulter_qcm == 1)
          if ($this->id_activit > 0 && $this->venu == 'act')
          {
              $parametres_qcm = $this->params_qcm;
              $id_activit = $this->id_activit;
              $acced = $this->acced;
              session_register('id_activit');
              session_register('acced');
              session_register('parametres_qcm');
          }
          if ($this->consulter_qcm != 1)
              echo "<FORM  id='create' name='create' action=\"menu_qcm_class.php\" method='POST'>\n";
          if ($this->modification == 1)
          {
              echo "<INPUT TYPE=HIDDEN name='mod_qcm' value=1>\n";
              echo "<INPUT TYPE=HIDDEN name='ordre' value='".$this->oBase->ordre."'>\n";
          }
          elseif ($this->new_qcm == 1)
              echo "<INPUT TYPE=HIDDEN name='new_qcm' value=1>\n";
          if ($this->consulter_qcm != 1)
          {
              echo "<INPUT TYPE=HIDDEN name='questionnaires' value=1>\n";
              echo cqcm :: formulaire_qcm();
              echo "</FORM>\n";
          }
      }// fon function create_qcm
      function create_pages_qcm()
      {
      }
      function modifie_pages_qcm()
      {
      }
      function formulaire_qcm()
      {
          require "lang".$this->lg.".inc.php";
          if ($this->modification == 1)
          {
             $reste = ($this->oBase->duree)%60;
             $heure = floor(($this->oBase->duree)/60);
          }
          /*
          echo "<pre>";
               print_r($this->oBase);
          echo "</pre>";
          */
          $afficher = '<INPUT TYPE="HIDDEN" name="ini_creation" value="1">'.
                      '<TR><TD colspan=2 bgColor="#FFFFFF" width="100%"><TABLE cellpadding="3" border="0" width="100%">';
          $afficher .= "<TR><TD><B>$mess_cqcm_tit_qcm</B></TD><TD>\n";
          $afficher .= '<INPUT TYPE="text" class="INPUT"  name="titre" align="left" size="60" value="'.$this->oBase->titre_qcm.'"></TD></TR>';
          $afficher .= "<TR><TD><B>$mess_cqcm_nbr_ques</B></TD><TD>\n";
          $afficher .= '<INPUT TYPE="text" class="INPUT"  name="nomb_p" align="left" size="1" value="'.$this->oBase->n_pages.'" title="$mess_cqcm_nbr_ques"></TD></TR>';
          $afficher .= "<TR><TD><B>$mess_moy_qcm</B></TD><TD>\n";
          $afficher .= '<INPUT TYPE="text" class="INPUT" name="moyenne" align="left" size="2" value="'.$this->oBase->moyenne.'" title="$mess_moy_detail_qcm">';
          $afficher .= "</TD></TR>\n";
          $afficher .= "<TR><TD nowrap><B>$mess_cqcm_tps</B></TD><TD align='left'><TABLE border='0' cellspacing='0'><TR>\n";
          $afficher .= "<TD><INPUT  TYPE='text' class='INPUT' name='horaire' value='$heure' size='2' maxlength = '3' align='center'>$h </TD>\n";
          $afficher .= "<TD><INPUT  TYPE='text' class='INPUT' name='minutage' value='$reste' size='2' maxlength = '2' align='center'>$mn</TD>\n";
          $afficher .= "</td></tr></table></td></tr>\n";
          $afficher .= "<TR><td></td><td style=\"align:'center';height: 40px;\">\n".
                       cqcm :: ajout_valider("javascript:checkFormQcm(document.create);").
                       "</td></tr>\n";

          return $afficher;
      }// fin function formulaire_qcm()
      function ajout_valider($complete)
      {
           $retour = "<A HREF=\"$complete\" \n".
                       "onmouseover=\"img1.src='images/fiche_identite/boutvalidb$suffixer.gif';return true;\" \n".
                       "onmouseout=\"img1.src='images/fiche_identite/boutvalid$suffixer.gif'\">\n".
                       "<IMG NAME=\"img1\" SRC=\"images/fiche_identite/boutvalid$suffixer.gif\" BORDER='0' \n".
                       "onLoad=\"tempImg=new Image(0,0); tempImg.src='images/fiche_identite/boutvalidb$suffixer.gif'\"></A>\n";
           return $retour;
      }
      function lance_form($titre_action,$libelle,$faire_action)
      {
           $retour = "<tr><td colspan='2'><B>$titre_action </B></td></tr>\n".
                     "<FORM  id='create' name='create' action=\"menu_qcm_class.php\" method='POST'>".
                     "<INPUT TYPE=HIDDEN name='$faire_action' value=1>\n".
                     "<tr><td colspan='2'><table><tr><td style=\"height:35px\">$libelle &nbsp;&nbsp;&nbsp; </td>\n";
           return $retour;
      }
      function link_bouton($url,$titrage)
      {
           GLOBAL $bouton_gauche,$bouton_droite;
           $linkage = "<tr><td style=\"height:'30px';padding: 10px;\">$bouton_gauche".
                        "<A href=\"trace.php?link=".urlencode($url)."\">".
                        $titrage."</A>$bouton_droite</td></tr>\n";
           return $linkage;
     }
}
?>