<?php
if (!isset($_SESSION)) session_start();
if (!isset($_SESSION['id_user']) || $_SESSION['id_user'] == "")
{
  exit();
}
//  fichier lang
if ($lg == "fr")
{ 
   $mmsg_mod_miens = "Afficher uniquement mes modules";
   $mmsg_mod_ssref = "Afficher tous les modules non liés à un référentiel";
   $mmsg_mod_ref = "Afficher tous les modules liés à un référentiel ";
   $mmsg_mod_tts = "Afficher tous les modules disponibles";
   $mmsg_mod_type = "Afficher tous les modules_types duplicables et non prescriptibles";
   $mmsg_mod_new = "Créer un nouveau module";
   $mmsg_seq_miens = "Afficher uniquement mes séquences";
   $mmsg_seq_ssref = "Afficher toutes les séquences non liées à un référentiel";
   $mmsg_seq_ref = "Afficher toutes les séquences liées à un référentiel ";
   $mmsg_seq_tts = "Afficher toutes les séquences disponibles";
   $mmsg_seq_type = "Afficher toutes les séquences_type duplicables et non prescriptibles";
   $mmsg_seq_new = "Créer une nouvelle séquence";
   $mmsg_act_miens_lbrs = "Afficher toutes mes activités";
   $mmsg_act_miens_seq = "Afficher toutes les activités";
   $mmsg_act_seq = "Afficher toutes les activités liées à une séquence";
   $mmsg_act_lbtts = "Afficher toutes les activités libres";
   $mmsg_act_lb_nd = "Afficher toutes les activités libres non duplicables";
   $mmsg_act_new = "Créer une nouvelle activité libre";
   $mmsg_gene = "Les modifications apportées ont été prises en compte";
   $mmsg_supact = "Le lien vers la ressource a été supprimé";
   $mmsg_menu_qcm = "Ce module permet à un formateur de concevoir et d'inclure des tests en tant qu'activité dans une séquence. ".
                    "Le formateur doit aussi fixer un seuil de réussite (note sur 20) qui permet (si l'option a été retenue) à ".
                    "l'apprenant d'accéder à l'activité suivante…";
   $mmsg_qcm_cr = "Créer un nouveau QCM";
   $mmsg_qcm_cs = "Consulter un QCM";
   $mmsg_qcm_md = "Modifier un QCM";
   $mmsg_qcm_sp = "Supprimer un QCM";
   $mmsg_qcmNoImg = "L'image indiquée n'a pas d'extension(gif, jpg, ou png): elle ne sera pas prise en compte";
   $mmsg_qcm_TImg = "Cliquez ici pour voir l'image en taille réelle";
   $mmsg_ntExp = "Vous venez de procéder à l'exportation du module";
   $mmsg_noExp = "Export impossible : la plate-forme destinataire n'est pas hébergée par le réseau Formagri/Ceres";
   $msgPrpSeq = "Prérequis à la séquence";
   $msgRetSeq = "Retour à la séquence";
   $msgConsPreq = "Consulter les pré-requis de la séquence";
   $msgNoSeq = "Vous n\'avez choisi aucune séquence existante";
   $msgchxSeq = "Choisir la séquence";
   $msgVldSq = "Valider la séquence pré-requise";
   $msgCarSeq = "Attention, si le pré-requis est une activité, la procédure continue";
   $msgchxseqOk = "Vous allez choisir une activité de la séquence ";
   $msgchXAct = "Choisir l'activité pré-requise";
   $msgActSeqReq = "Cette activité est bien devenue un prérequis pour la séquence ";
   $msgChxSeq = "Vous avez choisi cette séquence comme prérequis";
   $msqVld = "Validez votre choix";
   $msg_DjaPrq = "est dèjà un pré-requis pour cette séquence";
   $msg_fav_ot = "Oter de mes favoris";
   $msg_mod_mark = "Modules marqués";
   $msg_seq_mark = "Séquences marquées";
   $msgseq_mark = "marquée";
   $msg_modNbPg="Choisissez le nombre de lignes par page pour cette session";
   $msg_modItm = "Nombre d'items affichables pour cette session : ";
   $mess_AjStar = "Ajouter une séquence marquée";
   $msg_formod = "Forum du module";
   $msg_supforparc = "La suppression est effective et irreversible pour le forum du module : ";
   $msgCreForMod = "Cochez cette case pour créer le forum de ce module";
   $msg_SupForMod = "Supprimer le forum de ce module.";
   $msg_ForSupOk = "Forum supprimé";
   $msg_FMNosup = "Forum actif: vous pourrez le supprimer quand le module ne sera plus prescrit et ne contiendra plus de séquences.";
   $msg_ConsMod = "Consulter le module";
   $msg_RmtSjt = "Accueil du forum";
   $msg_NewSjt = "Nouveau sujet";
   $msg_SjtVis = "Ce message ainsi que tous ceux qui lui sont sous-jacents sont desormais visibles";
   $msg_SjtNoVis = "Ce message ainsi que tous ceux qui lui sont sous-jacents sont desormais invisibles sauf pour l'auteur du module";
   $msg_LstMsg = "Liste des messages de ce fil de discussion";
   $msg_Lst_sbjt = "Liste des sujets traités";
   $msg_IsModFrm = "Ce module possede un forum";
   $msg_RmtFil = "Revenir au fil de discussion";
   
}
elseif ($lg == "en")
{
   $mmsg_mod_miens = "Afficher uniquement mes modules";
   $mmsg_mod_ssref = "Afficher tous les modules non liés à un référentiel";
   $mmsg_mod_ref = "Afficher tous les modules liés à un référentiel ";
   $mmsg_mod_tts = "Afficher tous les modules disponibles";
   $mmsg_mod_type = "Afficher tous les modules_types duplicables et non prescriptibles";
   $mmsg_mod_new = "Créer un nouveau module ";
   $mmsg_seq_miens = "Afficher uniquement mes séquences";
   $mmsg_seq_ssref = "Afficher toutes les séquences non liées à un référentiel";
   $mmsg_seq_ref = "Afficher toutes les séquences liées à un référentiel ";
   $mmsg_seq_tts = "Afficher toutes les séquences disponibles";
   $mmsg_seq_type = "Afficher toutes les séquences_type duplicables et non prescriptibles";
   $mmsg_seq_new = "Créer une nouvelle séquence";
   $mmsg_act_miens_lbrs = "Afficher uniquement mes activités libres";
   $mmsg_act_miens_seq = "Afficher uniquement mes activités liées à une séquence";
   $mmsg_act_seq = "Afficher toutes les activités liées à une séquence";
   $mmsg_act_lbtts = "Afficher toutes les activités libres";
   $mmsg_act_lb_nd = "Afficher toutes les activités libres non duplicables";
   $mmsg_act_new = "Créer une nouvelle activité libre";
   $mmsg_gene = "Les modifications apportées ont été prises en compte";
   $mmsg_supact = "Le lien vers la ressource a été supprimé";
   $mmsg_menu_qcm = "Ce module permet à un formateur de concevoir et d'inclure des tests en tant qu'activité dans une séquence. ".
                    "Le formateur doit aussi fixer un seuil de réussite (note sur 20) qui permet (si l'option a été retenue) à ".
                    "l'apprenant d'accéder à l'activité suivante…";
   $mmsg_qcm_cr = "Créer un nouveau QCM";
   $mmsg_qcm_cs = "Consulter un QCM";
   $mmsg_qcm_md = "Modifier un QCM";
   $mmsg_qcm_sp = "Supprimer un QCM";
   $mmsg_qcmNoImg = "L'image indiquée n'a pas d'extension(gif, jpg, ou png): elle ne sera pas prise en compte";
   $mmsg_qcm_TImg = "Cliquez ici pour voir l'image en taille réelle";
   $mmsg_ntExp = "Vous venez de procéder à l'exportation du module";
   $mmsg_noExp = "Export impossible : la plate-forme destinataire n'est pas hébergée par le réseau Formagri/Ceres";
   $msgPrpSeq = "Pré-requis à la séquence";
   $msgRetSeq = "Retour à la séquence";
   $msgConsPreq = "Consulter les pré-requis de la séquence";
   $msgNoSeq = "Vous n\'avez choisi aucune séquence existante";
   $msgchxSeq = "Choisir la séquence";
   $msgVldSq = "Valider la séquence pré-requise";
   $msgCarSeq = "Attention, si le pré-requis est une activité, la procédure continue";
   $msgchxseqOk = "Vous allez choisir une activité de la séquence ";
   $msgchXAct = "Choisir l'activité pré-requise";
   $msgActSeqReq = "Cette activité est bien devenue un prérequis pour la séquence ";
   $msgChxSeq = "Vous avez choisi cette séquence comme prérequis";
   $msqVld = "Validez votre choix";
   $msg_DjaPrq = "est dèjà un pré-requis pour cette séquence";
   $msg_fav_ot = "Oter de mes favoris";
   $msgseq_mark = "marquée";
   $msg_mod_mark = "Modules marqués";
   $msg_modNbPg="Choisissez le nombre de lignes par page pour cette session";
   $msg_modItm = "Nombre d'items affichables pour cette session : ";
   $mess_AjStar = "Add a favorite sequence";
   $msg_formod = "Forum of this course";
   $msg_supforparc = "The forum is absolutly deleted. Any forum now is liked to the course : ";
   $msgCreForMod = "Check this Box to link a forum to this course";
   $msg_SupForMod = "Delete forum of this course : ";
   $msg_ForSupOk = "Forum is deleted";
   $msg_FMNosup = "Forum actif: vous pourrez le supprimer quand le module ne sera plus prescrit et ne contiendra plus de séquences.";
   $msg_ConsMod = "Consulter le module";
   $msg_RmtSjt = "Accueil du forum";
   $msg_NewSjt = "Nouveau sujet";
   $msg_SjtVis = "Ce message ainsi que tous ceux qui lui sont sous-jacents sont desormais visibles";
   $msg_SjtNoVis = "Ce message ainsi que tous ceux qui lui sont sous-jacents sont desormais invisibles sauf pour l'auteur du module";
   $msg_LstMsg = "Liste des messages de ce fil de discussion";
   $msg_Lst_sbjt = "Liste des sujets traités";
   $msg_IsModFrm = "Ce module est doté de son forum";
   $msg_RmtFil = "Revenir au fil de discussion";

}
elseif ($lg == "ru")
{
   $mmsg_mod_miens = "Afficher uniquement mes modules";
   $mmsg_mod_ssref = "Afficher tous les modules non liés à un référentiel";
   $mmsg_mod_ref = "Afficher tous les modules liés à un référentiel ";
   $mmsg_mod_tts = "Afficher tous les modules disponibles";
   $mmsg_mod_type = "Afficher tous les modules_types duplicables et non prescriptibles";
   $mmsg_mod_new = "Créer un nouveau module ";
   $mmsg_seq_miens = "Afficher uniquement mes séquences";
   $mmsg_seq_ssref = "Afficher toutes les séquences non liées à un référentiel";
   $mmsg_seq_ref = "Afficher toutes les séquences liées à un référentiel ";
   $mmsg_seq_tts = "Afficher toutes les séquences disponibles";
   $mmsg_seq_type = "Afficher toutes les séquences_type duplicables et non prescriptibles";
   $mmsg_seq_new = "Créer une nouvelle séquence";
   $mmsg_act_miens_lbrs = "Afficher uniquement mes activités libres";
   $mmsg_act_miens_seq = "Afficher uniquement mes activités liées à une séquence";
   $mmsg_act_seq = "Afficher toutes les activités liées à une séquence";
   $mmsg_act_lbtts = "Afficher toutes les activités libres";
   $mmsg_act_lb_nd = "Afficher toutes les activités libres non duplicables";
   $mmsg_act_new = "Créer une nouvelle activité libre";
   $mmsg_gene = "Les modifications apportées ont été prises en compte";
   $mmsg_supact = "Le lien vers la ressource a été supprimé";
   $mmsg_menu_qcm = "Ce module permet à un formateur de concevoir et d'inclure des tests en tant qu'activité dans une séquence. ".
                    "Le formateur doit aussi fixer un seuil de réussite (note sur 20) qui permet (si l'option a été retenue) à ".
                    "l'apprenant d'accéder à l'activité suivante…";
   $mmsg_qcm_cr = "Créer un nouveau QCM";
   $mmsg_qcm_cs = "Consulter un QCM";
   $mmsg_qcm_md = "Modifier un QCM";
   $mmsg_qcm_sp = "Supprimer un QCM";
   $mmsg_qcmNoImg = "L'image indiquée n'a pas d'extension(gif, jpg, ou png): elle ne sera pas prise en compte";
   $mmsg_qcm_TImg = "Cliquez ici pour voir l'image en taille réelle";
   $mmsg_ntExp = "Vous venez de procéder à l'exportation du module";
   $mmsg_noExp = "Export impossible : la plate-forme destinataire n'est pas hébergée par le réseau Formagri/Ceres";
   $msgPrpSeq = "Pré-requis à la séquence";
   $msgRetSeq = "Retour à la séquence";
   $msgConsPreq = "Consulter les pré-requis de la séquence";
   $msgNoSeq = "Vous n\'avez choisi aucune séquence existante";
   $msgchxSeq = "Choisir la séquence";
   $msgVldSq = "Valider la séquence pré-requise";
   $msgCarSeq = "Attention, si le pré-requis est une activité, la procédure continue";
   $msgchxseqOk = "Vous allez choisir une activité de la séquence ";
   $msgchXAct = "Choisir l'activité pré-requise";
   $msgActSeqReq = "Cette activité est bien devenue un prérequis pour la séquence ";
   $msgChxSeq = "Vous avez choisi cette séquence comme prérequis";
   $msqVld = "Validez votre choix";
   $msg_DjaPrq = "est dèjà un pré-requis pour cette séquence";
   $msg_fav_ot = "Oter de mes favoris";
   $msg_mod_mark = "Modules marqués";
   $msgseq_mark = "marquée";
   $msg_modNbPg="Choisissez le nombre de lignes par page pour cette session";
   $msg_modItm = "Nombre d'items affichables pour cette session : ";
   $mess_AjStar = "Add a favorite sequence";
   $msg_formod = "Forum of this course";
   $msg_supforparc = "The forum is absolutly deleted. Any forum now is liked to the course : ";
   $msgCreForMod = "Check this Box to link a forum to this course";
   $msg_SupForMod = "Delete forum of this course ";
   $msg_ForSupOk = "Forum is deleted";
   $msg_FMNosup = "Forum actif: vous pourrez le supprimer quand le module ne sera plus prescrit et ne contiendra plus de séquences.";
   $msg_ConsMod = "Consulter le module";
   $msg_RmtSjt = "Accueil du forum";
   $msg_NewSjt = "Nouveau sujet";
   $msg_SjtVis = "Ce message ainsi que tous ceux qui lui sont sous-jacents sont desormais visibles";
   $msg_SjtNoVis = "Ce message ainsi que tous ceux qui lui sont sous-jacents sont desormais invisibles sauf pour l'auteur du module";
   $msg_LstMsg = "Liste des messages de ce fil de discussion";
   $msg_Lst_sbjt = "Liste des sujets traités";
   $msg_IsModFrm = "Ce module est doté de son forum";
   $msg_RmtFil = "Revenir au fil de discussion";
   
   
}
?>