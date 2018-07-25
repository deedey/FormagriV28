<?php
if (!isset($_SESSION)) session_start();
//  fichier lang
if ($lg == "fr")
{
   $msg_ajt_seq = "Ajouter une séquence à ce module pour cet apprenant";
   $msg_ajtseq_nota = "Insérer au minimum 3 caractères pour faire votre recherche, ".
                      "puis choisissez une séquence parmi celles qui sont affichées. ".
                      "En cliquant sur le nom de la séquence, vous l'ajoutez automatiquement à cet apprenant";
   $mesg_voirform = "Retour au suivi";
   $mesg_vaform = "Voir la formation";
   $mesg_listform = "Ajouter un formateur en tapant 1 lettre minimum";
   $mesg_listpresc = "Ajouter un prescripteur en tapant 1 lettre minimum";
   $mesg_modform = "Modifier le formateur en tapant 1 lettre minimum";
   $mesg_modpresc = "Modifier le prescripteur en tapant 1 lettre minimum";
   $msgajtseq = "Pour ajouter une séquence à ce module, taper 3 lettres au minimum pour lancer votre recherche";
   $msg_ajt_mod_app = "Ajouter un module en tapant 3 lettres minimum";
   $msgPresc_No = "Cet apprenant est déjà inscrit à cette formation. Vous ne pouvez l'y inscrire deux fois";
   $msgPrscSuptts = "Désinscrire tous les apprenants de cette formation";
   $msgNoPrsc = "Pas encore de prescription";

}
elseif ($lg == "en")
{
   $msg_ajt_seq = "Ajouter une séquence à ce module pour cet apprenant";
   $msg_ajtseq_nota = "Insérer au minimum 3 caractères pour faire votre recherche, ".
                      "puis choisissez une séquence parmi celles qui sont affichées. ".
                      "En cliquant sur le nom de la séquence, vous l'ajoutez automatiquement à cet apprenant";
   $mesg_voirform = "Retour au suivi";
   $mesg_vaform = "Voir la formation";
   $mesg_listform = "Ajouter un formateur en tapant 1 lettre minimum";
   $mesg_listpresc = "Ajouter un prescripteur en tapant 1 lettre minimum";
   $mesg_modform = "Modifier le formateur en tapant 1 lettre minimum";
   $mesg_modpresc = "Modifier le prescripteur en tapant 1 lettre minimum";
   $msgajtseq = "Pour ajouter une séquence à ce module, taper 3 lettres au minimum pour lancer votre recherche";
   $msg_ajt_mod_app = "Ajouter un module en tapant 3 lettres minimum";
   $msgPresc_No = "Cet apprenant est déjà inscrit à cette formation. Vous ne pouvez l'y inscrire deux fois";
   $msgPrscSuptts = "Désinscrire tous les apprenants de cette formation";
   $msgNoPrsc = "Pas encore de prescription";


}
elseif ($lg == "ru")
{
   $msg_ajt_seq = "Ajouter une séquence à ce module pour cet apprenant";
   $msg_ajtseq_nota = "Insérer au minimum 3 caractères pour faire votre recherche, ".
                      "puis choisissez une séquence parmi celles qui sont affichées. ".
                      "En cliquant sur le nom de la séquence, vous l'ajoutez automatiquement à cet apprenant";
   $mesg_voirform = "Retour au suivi";
   $mesg_vaform = "Voir la formation";
   $mesg_listform = "Ajouter un formateur en tapant 1 lettre minimum";
   $mesg_listpresc = "Ajouter un prescripteur en tapant 1 lettre minimum";
   $mesg_modform = "Modifier le formateur en tapant 1 lettre minimum";
   $mesg_modpresc = "Modifier le prescripteur en tapant 1 lettre minimum";
   $msgajtseq = "Pour ajouter une séquence à ce module, taper 3 lettres au minimum pour lancer votre recherche";
   $msg_ajt_mod_app = "Ajouter un module en tapant 3 lettres minimum";
   $msgPresc_No = "Cet apprenant est déjà inscrit à cette formation. Vous ne pouvez l'y inscrire deux fois";
   $msgPrscSuptts = "Désinscrire tous les apprenants de cette formation";
   $msgNoPrsc = "Pas encore de prescription";

}
?>