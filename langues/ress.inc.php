<?php
if (!isset($_SESSION)) session_start();
//  fichier lang
if ($lg == "fr"){
   $msgrech_retqcm = "Revenir au menu de création de QCM";
   $mmsg_ModRessOk = "La ressource choisie a été associée à l'activité";
   $mmsg_RessOk = "La ressource a été modifiée";
   $mmsg_SupRess = "Le lien vers la ressource a été supprimé ";
   $mmsg_noResLgn = "Pas de ressource en ligne";
   $mmsg_noAffTt = "Afficher tout";
   $mmsg_qcmSupp = "Supprimer le QCM";
   $mmsg_qcmMdf = "Modifier le QCM";
   $mmsg_qcmOpn = "Ouvrir le QCM";
   $mmsg_qcmPgs = "Le nombre de QCM à afficher par page est passé à ";
   $mmsg_qcmDisp = "QCM disponible(s)";
   $mmsg_qcmAffAut = "Afficher tous les QCM de";
   $msgRess_supOk = "La suppression de la ressource a bien eu lieu";


}elseif ($lg == "en"){
   $msgrech_retqcm = "Revenir au menu de création de QCM";
   $mmsg_RessOk = "La ressource choisie a été associée à l'activité ";
   $mmsg_ModRessOk = "La ressource choisie a été associée à l'activité";
   $mmsg_SupRess = "Le lien vers la ressource a été supprimé ";
   $mmsg_noResLgn = "Pas de ressource en ligne";
   $mmsg_noAffTt = "Afficher tout";
   $mmsg_qcmSupp = "Supprimer le QCM";
   $mmsg_qcmMdf = "Modifier le QCM";
   $mmsg_qcmOpn = "Ouvrir le QCM";
   $mmsg_qcmPgs = "Le nombre de Qcm à afficher par page est passé à ";
   $mmsg_qcmDisp = "QCM disponible(s)";
   $mmsg_qcmAffAut = "Afficher tous les QCM de";
   $msgRess_supOk = "La suppression de la ressource a bien eu lieu";

}elseif ($lg == "ru"){
   $msgrech_retqcm = "Revenir au menu de création de QCM";
   $mmsg_RessOk = "La ressource choisie a été associée à l'activité ";
   $mmsg_ModRessOk = "La ressource choisie a été associée à l'activité";
   $mmsg_SupRess = "Le lien vers la ressource a été supprimé ";
   $mmsg_noResLgn = "Pas de ressource en ligne";
   $mmsg_noAffTt = "Afficher tout";
   $mmsg_qcmSupp = "Supprimer le QCM";
   $mmsg_qcmMdf = "Modifier le QCM";
   $mmsg_qcmOpn = "Ouvrir le QCM";
   $mmsg_qcmPgs = "Le nombre de Qcm à afficher par page est passé à ";
   $mmsg_qcmDisp = "QCM disponible(s)";
   $mmsg_qcmAffAut = "Afficher tous les QCM de";
   $msgRess_supOk = "La suppression de la ressource a bien eu lieu";

}

?>