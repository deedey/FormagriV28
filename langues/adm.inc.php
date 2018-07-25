<?php
if (!isset($_SESSION)) session_start();
if ($lg == "")
{
  exit();
}
//  fichier lang
if ($lg == "fr")
{
   $ntf_adm_mc = "pourra accéder à partir de cette plateforme à";
   $msgadm_nbr_aff = "le nombre d'éléments à afficher par page est passé à";
   $msgadm_msgmc = "Mode Multi-Centre activé";
   $msgadm_msgfav = "Gestion du marquage des modules, séquences et activités favorites activée";
   $msgadm_fav =  "Gestion du marquage des modules, séquences et activités favorites";
   $msgadm_msgchat = "Chat activé";
   $msgadm_msgflib = "Forum libre activé";
   $msgadm_msgrss = "Fil Rss sur la conception des modules activé";
   $msgadm_mc = "Le mode multi-centre a été";
   $msgadm_chat = "L'accès au chat a été";
   $msgadm_flib = "L'accès au forum libre a été";
   $msgadm_rss = "L'accès au fil RSS a été";
   $msgadm_mc_act = "activé";
   $msgadm_mc_desact = "désactivé";
   $msg_logo_def = "Les paramètres de l'interface sont de nouveau ceux fournis par défaut par la plate-forme";
   $msg_logo_new = "Vous venez de modifier les paramètres de l'interface de la plate-forme";
   $msgadm_supserv = "Vous venez de supprimer le serveur dont l'adresse est";
   $msgadm_modserv = "Vous venez de modifier les paramètres du serveur dont l'adresse est";
   $msgadm_ajtserv = "Vous venez d'ajouter un serveur dont l'adresse est";
   $msgadm_pgs_idx = "Le nombre d'items affiché par page dans votre index des ressources sera désormais de";
   $msgadm_scNrm = "Vous venez de sélectionner un écran au format Normal avec toutes les fonctionnalités du navigateur";
   $msgadm_scMed = "Vous venez de sélectionner un écran au format Médian avec un navigateur sans barre d'adresse";
   $msgadm_noapp_dv = "Aucun apprenant inscrit";
   $msgadm_nosch_dv = "Aucun nom ne répond à cet suite de lettres. Faites une autre recherche<br /> ou choisissez une option dans le menu déroulant.";
   $msgadm_sch_nb = " correspond(ent) au nom ou début de nom recherché";
   $msgadm_sch_nmdb = " Nom ou début de nom recherché";
   $msgadm_rf_titbul = "Responsable de la formation ";
   $msgadm_presc_titbul = "A prescrit dans la formation ";
   $msgadm_form_titbul = "Formateur dans la formation ";
   $msgadm_spv_titbul = "Observateur de la formation ";
   $msgadm_tut_titbul = "Tuteur pour ";
   $msgadm_mod = "Modules";
   $msgadm_seq = "Séquences";
   $msgadm_new_img = "Vous venez d'affecter à vos écrans une nouvelle image de fond";
   $msgadm_new_color = "Vous venez de modifier la couleur de fond d'écran de la page d'accueil";
   $msgadm_clkEtat = "Cliquez sur le lien pour modifier l'état de cette fonctionnalité";
   $msgadm_ecrmod = "Modifier la configuration du navigateur (avec [NORMAL] ou sans [MEDIAN] menus déroulants ni barre d'adresse)";
   $msgadm_etatfav = "Gestion des modules, séquences et acitivités favorites activé";
}
elseif ($lg == "en")
{
   $ntf_adm_mc = "pourra accéder à partir de cette plateforme à";
   $msgadm_nbr_aff = "le nombre d'éléments à afficher par page est passé à";
   $msgadm_msgmc = "Mode Multi-Centre activé";
   $msgadm_msgfav = "Gestion des modules, séquences et activités favorites activée";
   $msgadm_fav =  "Gestion des modules, séquences et activités favorites";   $msgadm_msgchat = "Chat activé";
   $msgadm_msgflib = "Forum libre activé";
   $msgadm_msgrss = "Fil Rss sur la conception des modules activé";
   $msgadm_mc = "Le mode multi-centre a été";
   $msgadm_chat = "L'accès au chat a été";
   $msgadm_flib = "L'accès au forum libre a été";
   $msgadm_rss = "L'accès au fil RSS a été";
   $msgadm_mc_act = "activé";
   $msgadm_mc_desact = "désactivé";
   $msg_logo_def = "Les paramètres de l'interface sont de nouveau ceux fournis par défaut par la plate-forme";
   $msg_logo_new = "Vous venez de modifier les paramètres de l'interface de la plate-forme";
   $msgadm_supserv = "Vous venez de supprimer le serveur dont l'adresse est";
   $msgadm_modserv = "Vous venez de modifier les paramètres du serveur dont l'adresse est";
   $msgadm_ajtserv = "Vous venez d'ajouter un serveur dont l'adresse est";
   $msgadm_pgs_idx = "Le nombre d'items affiché par page dans votre index des ressources sera désormais de";
   $msgadm_scNrm = "Vous venez de sélectionner un écran au format Normal avec toutes les fonctionnalités du navigateur";
   $msgadm_scMed = "Vous venez de sélectionner un écran au format Médian avec un navigateur sans barre d'adresse";
   $msgadm_noapp_dv = "Aucun apprenant inscrit";
   $msgadm_nosch_dv = "Aucun nom ne répond à cet suite de lettres. Faites une autre recherche<br /> ou choisissez une option dans le menu déroulant.";
   $msgadm_sch_nb = " correspond(ent) au nom ou début de nom recherché";
   $msgadm_sch_nmdb = " Nom ou début de nom recherché";
   $msgadm_rf_titbul = "Responsable de la formation ";
   $msgadm_presc_titbul = "A prescrit dans la formation ";
   $msgadm_form_titbul = "Formateur dans la formation ";
   $msgadm_spv_titbul = "Observateur de la formation ";
   $msgadm_tut_titbul = "Tuteur pour ";
   $msgadm_mod = "Modules";
   $msgadm_seq = "Séquences";
   $msgadm_new_img = "Vous venez d'affecter à vos écrans une nouvelle image de fond";
   $msgadm_new_color = "Vous venez de modifier la couleur de fond d'écran de la page d'accueil";
   $msgadm_clkEtat = "Cliquez sur le lien pour modifier l'état de cette fonctionnalité";
   $msgadm_ecrmod = "Modifier la configuration du navigateur (avec [NORMAL] ou sans [MEDIAN] menus déroulants ni barre d'adresse)";
   $msgadm_etatfav = "Gestion des modules, séquences et acitivités favorites activé";
}
elseif ($lg == "ru")
{
   $ntf_adm_mc = "pourra accéder à partir de cette plateforme à";
   $msgadm_nbr_aff = "le nombre d'éléments à afficher par page est passé à";
   $msgadm_msgmc = "Mode Multi-Centre activé";
   $msgadm_msgfav = "Gestion des modules, séquences et activités favorites activée";
   $msgadm_fav =  "Gestion des modules, séquences et activités favorites";   $msgadm_msgchat = "Chat activé";
   $msgadm_msgflib = "Forum libre activé";
   $msgadm_msgrss = "Fil Rss sur la conception des modules activé";
   $msgadm_mc = "Le mode multi-centre a été";
   $msgadm_chat = "L'accès au chat a été";
   $msgadm_flib = "L'accès au forum libre a été";
   $msgadm_rss = "L'accès au fil RSS a été";
   $msgadm_mc_act = "activé";
   $msgadm_mc_desact = "désactivé";
   $msg_logo_def = "Les paramètres de l'interface sont de nouveau ceux fournis par défaut par la plate-forme";
   $msg_logo_new = "Vous venez de modifier les paramètres de l'interface de la plate-forme";
   $msgadm_supserv = "Vous venez de supprimer le serveur dont l'adresse est";
   $msgadm_modserv = "Vous venez de modifier les paramètres du serveur dont l'adresse est";
   $msgadm_ajtserv = "Vous venez d'ajouter un serveur dont l'adresse est";
   $msgadm_pgs_idx = "Le nombre d'items affiché par page dans votre index des ressources sera désormais de";
   $msgadm_scNrm = "Vous venez de sélectionner un écran au format Normal avec toutes les fonctionnalités du navigateur";
   $msgadm_scMed = "Vous venez de sélectionner un écran au format Médian avec un navigateur sans barre d'adresse";
   $msgadm_noapp_dv = "Aucun apprenant inscrit";
   $msgadm_nosch_dv = "Aucun nom ne répond à cet suite de lettres. Faites une autre recherche<br /> ou choisissez une option dans le menu déroulant.";
   $msgadm_sch_nb = " correspond(ent) au nom ou début de nom recherché";
   $msgadm_sch_nmdb = " Nom ou début de nom recherché";
   $msgadm_rf_titbul = "Responsable de la formation ";
   $msgadm_presc_titbul = "A prescrit dans la formation ";
   $msgadm_form_titbul = "Formateur dans la formation ";
   $msgadm_spv_titbul = "Observateur de la formation ";
   $msgadm_tut_titbul = "Tuteur pour ";
   $msgadm_mod = "Modules";
   $msgadm_seq = "Séquences";
   $msgadm_new_img = "Vous venez d'affecter à vos écrans une nouvelle image de fond";
   $msgadm_new_color = "Vous venez de modifier la couleur de fond d'écran de la page d'accueil";
   $msgadm_clkEtat = "Cliquez sur le lien pour modifier l'état de cette fonctionnalité";
   $msgadm_ecrmod = "Modifier la configuration du navigateur (avec [NORMAL] ou sans [MEDIAN] menus déroulants ni barre d'adresse)";
   $msgadm_etatfav = "Gestion des modules, séquences et activités favorites activé";
}


?>