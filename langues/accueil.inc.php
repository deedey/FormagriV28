<?php
if (!isset($_SESSION)) session_start();
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
//  fichier lang
if ($lg == "fr")
{
   $msg_WNS = "Quoi de neuf ?";
   $msgevt_WNS = "Quoi de neuf depuis votre dernière connexion le ";
   $msgevtWNS = "Quoi de neuf depuis votre dernière connexion le";
   $msgfrm_lib = "Il n'y a eu aucun nouveau message posté dans le forum libre";
   $msgfrm_form = "Il n'y a eu aucun nouveau message posté dans le forum des encadrants pédagogiques";
   $msgfrm_app = "Il n'y a eu aucun nouveau message posté dans les forums des formations auxquels vous avez accès";
   $msgfrm_nolib = "Le forum libre est actuellement désactivé sur cette plate-forme";
   $msgins_no = "Aucune nouvelle inscription n'a eu lieu";
   $msgress_no = "Aucune nouvelle ressource ni catégorie n'a été ajoutée";
   $msgCnt_formno = "Aucune nouvelle formation n'a été ajoutée";
   $msgCnt_modno = "Aucun nouveau module n'a été ajouté";
   $msgCnt_seqno = "Aucune nouvelle séquence n'a été ajoutée";
   $msgCnt_actno = "Aucune nouvelle activité n'a été ajoutée";
   $msgRep_Frmno = "Aucun fichier ni dossier n'a été ajouté aux dossiers partagés des formateurs";
   $msgRep_Grpno = "Aucun fichier ni dossier n'a été ajouté aux dossiers partagés des formations auxquelles vous avez accès";
   $msgtutno = "Vous n'avez aucun rendez-vous de tutorat aujourd'hui";
   $msgins_aut = "Inscrit par";
   $msgress_aut = "Inséré(e) par";
   $msgress_cat = "Création d'une catégorie";
   $msgnew_ins = "nouvelle(s) insertion(s)";
   $msgnew_act = "nouvelle(s) activité(s)";
   $msgnew_seq = "nouvelle(s) séquence(s)";
   $msgnew_mod = "nouveau(x) module(s)";
   $msgAcc_msg = 'Messages';
   $msgAcc_frm = 'Forums';
   $msgAcc_cnx = 'Connectés';
   $msgAcc_ins = 'Inscrits';
   $msgAcc_mod = 'Modules';
   $msgAcc_seq = 'Séquences';
   $msgAcc_act = 'Activités';
   $msgAcc_rdv = 'Rendez-vous';
   $msgAcc_rep = 'Dossiers';
   $msgAcc_res = 'Ressources';
   $msgRep_dos = 'Dossier';
   $msgRep_fic = 'Fichier';
   $msgRep_rpf = 'Dossiers partagés';
   $msgRep_clkFic = 'Cliquez sur le lien pour ouvrir le fichier';
   $msgRep_clkRep = 'Cliquez sur le lien pour accéder à ce dossier';

}
elseif ($lg == "en")
{
   $msg_WNS = "Quoi de neuf ?";
   $msgevt_WNS = "Quoi de neuf depuis votre dernière connexion le ";
   $msgfrm_lib = "Il n'y a eu aucun nouveau message posté dans le forum libre";
   $msgfrm_form = "Il n'y a eu aucun nouveau message posté dans le forum des encadrants pédagogiques";
   $msgfrm_app = "Il n'y a eu aucun nouveau message posté dans les forums des formations auxquels vous avez accès";
   $msgfrm_nolib = "Le forum libre est actuellement désactivé sur cette plate-forme";
   $msgins_no = "Aucune nouvelle inscription n'a eu lieu";
   $msgress_no = "Aucune nouvelle ressource ni catégorie n'a été ajoutée";
   $msgCnt_formno = "Aucune nouvelle formation n'a été ajoutée";
   $msgCnt_modno = "Aucun nouveau module n'a été ajouté";
   $msgCnt_seqno = "Aucune nouvelle séquence n'a été ajoutée";
   $msgCnt_actno = "Aucune nouvelle activité n'a été ajoutée";
   $msgRep_Frmno = "Aucun fichier ni dossier n'a été ajouté aux dossiers partagés des formateurs";
   $msgRep_Grpno = "Aucun fichier ni dossier n'a été ajouté aux dossiers partagés des formations auxquelles vous avez accès";
   $msgtutno = "Vous n'avez aucun rendez-vous de tutorat aujourd'hui";
   $msgins_aut = "Inscrit par";
   $msgress_aut = "Inséré(e) par";
   $msgress_cat = "Création d'une catégorie";
   $msgnew_ins = "nouvelle(s) insertion(s)";
   $msgnew_act = "nouvelle(s) activité(s)";
   $msgnew_seq = "nouvelle(s) séquence(s)";
   $msgnew_mod = "nouveau(x) module(s)";
   $msgAcc_msg = 'Messages';
   $msgAcc_frm = 'Forums';
   $msgAcc_cnx = 'Connectés';
   $msgAcc_ins = 'Inscrits';
   $msgAcc_mod = 'Modules';
   $msgAcc_seq = 'Séquences';
   $msgAcc_act = 'Activités';
   $msgAcc_rdv = 'Rendez-vous';
   $msgAcc_rep = 'Dossiers';
   $msgAcc_res = 'Ressources';
   $msgRep_dos = 'Dossier';
   $msgRep_fic = 'Fichier';
   $msgRep_rpf = 'Dossiers partagés de(s)';
   $msgRep_clkFic = 'Cliquez sur le lien pour ouvrir le fichier';
   $msgRep_clkRep = 'Cliquez sur le lien pour accéder à ce dossier';

}
elseif ($lg == "ru")
{
   $msg_WNS = "Quoi de neuf ?";
   $msgevt_WNS = "Quoi de neuf depuis votre dernière connexion le ";
   $msgfrm_lib = "Il n'y a eu aucun nouveau message posté dans le forum libre";
   $msgfrm_form = "Il n'y a eu aucun nouveau message posté dans le forum des encadrants pédagogiques";
   $msgfrm_app = "Il n'y a eu aucun nouveau message posté dans les forums des formations auxquels vous avez accès";
   $msgfrm_nolib = "Le forum libre est actuellement désactivé sur cette plate-forme";
   $msgins_no = "Aucune nouvelle inscription n'a eu lieu";
   $msgress_no = "Aucune nouvelle ressource ni catégorie n'a été ajoutée";
   $msgCnt_formno = "Aucune nouvelle formation n'a été ajoutée";
   $msgCnt_modno = "Aucun nouveau module n'a été ajouté";
   $msgCnt_seqno = "Aucune nouvelle séquence n'a été ajoutée";
   $msgCnt_actno = "Aucune nouvelle activité n'a été ajoutée";
   $msgRep_Frmno = "Aucun fichier ni dossier n'a été ajouté aux dossiers partagés des formateurs";
   $msgRep_Grpno = "Aucun fichier ni dossier n'a été ajouté aux dossiers partagés des formations auxquelles vous avez accès";
   $msgtutno = "Vous n'avez aucun rendez-vous de tutorat aujourd'hui";
   $msgins_aut = "Inscrit par";
   $msgress_aut = "Inséré(e) par";
   $msgress_cat = "Création d'une catégorie";
   $msgnew_ins = "nouvelle(s) insertion(s)";
   $msgnew_act = "nouvelle(s) activité(s)";
   $msgnew_seq = "nouvelle(s) séquence(s)";
   $msgnew_mod = "nouveau(x) module(s)";
   $msgAcc_msg = 'Messages';
   $msgAcc_frm = 'Forums';
   $msgAcc_cnx = 'Connectés';
   $msgAcc_ins = 'Inscrits';
   $msgAcc_mod = 'Modules';
   $msgAcc_seq = 'Séquences';
   $msgAcc_act = 'Activités';
   $msgAcc_rdv = 'Rendez-vous';
   $msgAcc_rep = 'Dossiers';
   $msgAcc_res = 'Ressources';
   $msgRep_dos = 'Dossier';
   $msgRep_fic = 'Fichier';
   $msgRep_rpf = 'Dossiers partagés de(s)';
   $msgRep_clkFic = 'Cliquez sur le lien pour ouvrir le fichier';
   $msgRep_clkRep = 'Cliquez sur le lien pour accéder à ce dossier';

}

?>