<?php
if (!isset($_SESSION)) session_start();
//  fichier lang
if ($lg == "fr"){
   $msgrph_ActNo = "Aucune activité n'a été affectée à cet apprenant dans le cadre de cette formation";
   $msgrph_dureeNo = "Aucune durée n'a été renseignée dans ce cas : pas d'activité ou activités Scorm ou Aicc";
   $msgrph_avanti = "Etat d'avancement de la formation";
   $msgrph_Mavanti = "Etat d'avancement de ma formation";
   $msgrph_av_cmt = "Les barres représentent pour chaque apprenant :<br />".
                    " <li type=sphere> le nombre d'activités prescrites ;</li>".
                    " <li type=sphere> le volume horaire de la formation ;</li>".
                    " <br />... ainsi que le réalisé à la date en cours.";
   $msgrph_lgred = "le curseur rouge correspond à la date de la dernière connexion";
   $msgrph_lgreg = "les pointillés verticaux correspondent aux regroupements";
   $msgrph_lgdtj = "le tracé vertical noir correspond à la date du jour";
}elseif ($lg == "en"){
   $msgrph_ActNo = "Aucune activité n'a été affectée à cet apprenant dans le cadre de cette formation";
   $msgrph_dureeNo = "Aucune durée n'a été renseignée dans ce cas : probablement des activités Scorm ou Aicc";
   $msgrph_avanti = "Etat d'avancement de la formation";
   $msgrph_Mavanti = "Etat d'avancement de ma formation";
   $msgrph_av_cmt = "Les barres représentent pour chaque apprenant :<br />".
                    " <li type=sphere> le nombre d'activités prescrites ;</li>".
                    " <li type=sphere> le volume horaire de la formation ;</li>".
                    " <br />... ainsi que le réalisé à la date en cours.";
   $msgrph_lgred = "le curseur rouge correspond à la date de la dernière connexion";
   $msgrph_lgreg = "les pointillés verticaux correspondent aux regroupements";
   $msgrph_lgdtj = "le tracé vertical noir correspond à la date du jour";
}elseif ($lg == "ru"){
   $msgrph_ActNo = "Aucune activité n'a été affectée à cet apprenant dans le cadre de cette formation";
   $msgrph_dureeNo = "Aucune durée n'a été renseignée dans ce cas : probablement des activités Scorm ou Aicc";
   $msgrph_avanti = "Etat d'avancement de la formation";
   $msgrph_Mavanti = "Etat d'avancement de ma formation";
   $msgrph_av_cmt = "Les barres représentent pour chaque apprenant :<br />".
                    " <li type=sphere> le nombre d'activités prescrites ;</li>".
                    " <li type=sphere> le volume horaire de la formation ;</li>".
                    " <br />... ainsi que le réalisé à la date en cours.";
   $msgrph_lgred = "le curseur rouge correspond à la date de la dernière connexion";
   $msgrph_lgreg = "les pointillés verticaux correspondent aux regroupements";
   $msgrph_lgdtj = "le tracé vertical noir correspond à la date du jour";
}
?>