<?php
if (!isset($_SESSION)) session_start();
//  fichier lang
if ($lg == "fr"){
   $msgrp_acces = " Cette formation est maintenant accessible ";
   $msgrp_noacces = " Cette formation n'est plus accessible. Elle est en construction ";
   $msgrp_mut = " Cette formation est maintenant mutualisee ";
   $msgrp_nomut = " Cette formation n'est plus mutualisee ";
   $msgrp_ind = "Cette formation permet desormais l'individualisation";
   $msgrp_noind = "Cette formation ne permet plus l'individualisation.<br /> Le parcours de formation sera identique pour tous les apprenants";
   $msgrp_ajtapform = "Ajouter un nouvel apprenant à cette formation";
   $msgrp_inscOk = "a été inscrit à la formation";
   $msgrp_notut = "n'accompagne plus en qualité de tuteur l'apprenant";
   $msgrp_permut = "a permuté avec";
   $msgrp_seq_presc = "des séquences appartenant à ce module est prescriptible. Le reste ne l'est pas";
   $msgrp_nomodif_presc = "Vous n'avez opéré aucune modification des prescriptions";
   $msg_indivi_tit = "Individualisation de la formation";
   $msg_ajtF = "Ajouter une formation";
   $msg_frm_presc = "Formation(s) prescrite(s)";
   $msg_seq_rinit = "Réinitialisation";
   $msg_bilglb = "Informations globales";
   $msg_bilfrm = "Historique d'activités liées à la formation";
   $msgActPlus = "Activité ajoutée pour vous individuellement en plus du contenu de la séquence";

}elseif ($lg == "en"){
   $msgrp_acces = " Cette formation est désormais accessible ";
   $msgrp_noacces = " Cette formation n'est plus accessible. Elle est en construction ";
   $msgrp_mut = " Cette formation est désormais mutualisée ";
   $msgrp_nomut = " Cette formation n'est plus mutualisée ";
   $msgrp_ind = "Cette formation permet désormais l'individualisation";
   $msgrp_noind = "Cette formation ne permet plus l'individualisation.<br /> Le parcours de formation sera le même pour tous les apprenants";
   $msgrp_ajtapform = "Ajouter un nouvel apprenant à cette formation";
   $msgrp_inscOk = "a été inscrit à la formation";
   $msgrp_notut = "n'accompagne plus en qualité de tuteur l'apprenant";
   $msgrp_permut = "a permuté avec";
   $msgrp_seq_presc = "des séquences appartenant à ce module est prescriptible. Le reste ne l'est pas";
   $msgrp_nomodif_presc = "Vous n'avez opéré aucune modification des prescriptions";
   $msg_indivi_tit = "Individualisation de la formation";
   $msg_ajtF = "Ajouter une formation";
   $msg_frm_presc = "Formation(s) prescrite(s)";
   $msg_seq_rinit = "Réinitialisation";
   $msg_bilglb = "Informations globales";
   $msg_bilfrm = "Historique d'activités liées à la formation";
   $msgActPlus = "Activité ajoutée pour vous individuellement en plus du contenu de la séquence";


}elseif ($lg == "ru"){
   $msgrp_acces = " Cette formation est désormais accessible ";
   $msgrp_noacces = " Cette formation n'est plus accessible. Elle est en construction ";
   $msgrp_mut = " Cette formation est désormais mutualisée ";
   $msgrp_nomut = " Cette formation n'est plus mutualisée ";
   $msgrp_ind = "Cette formation permet désormais l'individualisation";
   $msgrp_noind = "Cette formation ne permet plus l'individualisation.<br /> Le parcours de formation sera le même pour tous les apprenants";
   $msgrp_ajtapform = "Ajouter un nouvel apprenant à cette formation";
   $msgrp_inscOk = "a été inscrit à la formation";
   $msgrp_notut = "n'accompagne plus en qualité de tuteur l'apprenant";
   $msgrp_permut = "a permuté avec";
   $msgrp_nomodif_presc = "Vous n'avez opéré aucune modification des prescriptions";
   $msgrp_seq_presc = "des séquences appartenant à ce module est prescriptible. Le reste ne l'est pas";
   $msg_indivi_tit = "Individualisation de la formation";
   $msg_ajtF = "Ajouter une formation";
   $msg_frm_presc = "Formation(s) prescrite(s)";
   $msg_seq_rinit = "Réinitialisation";
   $msg_bilglb = "Informations globales";
   $msg_bilfrm = "Historique d'activités liées à la formation";
   $msgActPlus = "Activité ajoutée pour vous individuellement en plus du contenu de la séquence";


}

?>