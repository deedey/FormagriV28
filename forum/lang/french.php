<?php
  // Cyril Méchiche (cyril@omega-online.net)
  // ml sur Phorum en francais phorum-fr@omega-online.net
  // pour s'inscrire : phorum-fr-subscribe@omega-online.net
  // Phorum Version: 3.2.6
  $open_subject     = "Voir l'arborescence de ce sujet de discussion";
  $consult_msg      = "Consulter ce message";
  $bul_arch         = "Archiver et sauvegarder l'ensemble des échanges de ce sujet de discussion";
  $lForumDown       = "Nos Forums sont fermés";
  $lForumDownNotice = "Forums fermés pour maintenance. Ils seront disponibles très prochainement.<p>Excusez-nous de ce contre-temps.";
  $lNoAuthor        = "Vous devez fournir un auteur.";
  $lNoSubject       = "Vous devez fournir un sujet.";
  $lNoBody          = "N'oubliez pas votre message.";
  $lNoEmail         = "Vous n'avez pas entré d'adresse e-mail valide.  L'adresse e-mail n'est pas nécessaire.<br>Si vous ne désirez pas mettre d'adresse, laissez le champ vide.";
  $lNoEmailReply    = "En demandant à avoir une réponse par e-mail, vous devez donner une adresse e-mail valide.";
  $lModerated       = "Forum modéré.  Tous les messages sont relus avant le dépôt.";
  $lModeratedMsg    = "C'est un forum modéré.  Votre message a été mailé au modérateur qui l'analysera le plus t&ocirc;t possible.";
  $lReplyMessage    = "Répondre à ce message";
  $lReplyThread     = "Répondre à ce sujet";
  $lWrote           = "a écrit";
  $lQuote           = "Reprendre l'ancien message";
  $lFormName        = "Votre Nom";
  $lFormEmail       = "Votre Email";
  $lFormSubject     = "Sujet de discussion";
  $lFormAttachment  = "Pièce jointe"; // english: Attachment
  $lInvalidFile     = "La pièce jointe ne peut contenir d'espaces ou de caractères incompatibles"; // english: The attachment cannot contain spaces or weird characters.
  $lFileExists      = "Le nom de cette pièce jointe existe déjà sur le serveur, merci de la renommer"; // english: The filename of the attachment already esists on the server. Please rename your attachment
  $lFormPost        = "Envoyer";
  $lFormImage       = "Image";
  $lAvailableForums = "Forums disponibles";
  $lNoActiveForums  = "Pas de forum actif.";
  $lCollapseThreads = "Reduire l'arborescence aux fils de discussion";
  $lViewThreads     = "Vue arborescente des messages";
  $lReadFlat        = "Vue plane";
  $lReadThreads     = "Vue en arborescence";
  $lForumList       = "Liste des forums";
  $lMarkRead        = "Marquer tous comme lus";
  $lUpLevel         = "Monter d'un cran";
  $lGoToTop         = "Remonter";
  $lStartTopic      = "Nouveau sujet de discussion";
  $lSearch          = "Rechercher par mot-clé";
  $lavSearch        = "Recherche avancée";
  $lSearchAllWords  = "Tous les mots";
  $lSearchAnyWords  = "N'importe quel mot";
  $lSearchPhrase    = "La phrase exacte";
  $lSearchLast30    = "les 30 derniers jours";
  $lSearchLast60    = "les 60 derniers jours";
  $lSearchLast90    = "les 90 derniers jours";
  $lSearchLast180   = "les 180 derniers jours";
  $lSearchAllDates  = "Toutes les dates";
  $lSearchThisForum = "Chercher dans ce forum"; // Search This Forum";
  $lSearchAllForums = "Chercher dans tous les forums"; // Search All Forums";
  $lForum           = "forum";
  $lBigForum        = "Forum"; // Forum";
  $lNewerMessages   = "Suivants";
  $lOlderMessages   = "Précédents";
  $lNew             = "nouveau";
  $lTopics          = "Sujets de discussion";
  $lsjtPst          = "Messages postés";
  $lsjttit          = "Titre du message";
  $lAut_fil         = "Initiateur du sujet";
  $lAuthor          = "Auteur";
  $lLu              = "Consulté";
  $lLstrep          = "Dernière réponse";
  $lLatest          = "Date de dernière réponse";
  $lReplies         = "Réponses";
  $lGoToTopic       = "Aller voir un sujet";
  $lPreviousMessage = "Message Précédent";
  $lNextMessage     = "Message Suivant";
  $lPreviousTopic   = "Précédent";
  $lNextTopic       = "Suivant";
  $lSearchResults   = "Résultats de la recherche";
  $lSearchTips      = "Aide sur la recherche";
  $lTheSearchTips   = "AND par défaut. C'est à dire: une recherche pour <B>chien</B> et <B>chat</B> retourne tous les messages contenant ces mots n'importe où.<p>Le guillemet (\") permet des recherches de phrases. C'est à dire : une recherche pour <B>\"chien chat\"</B> retourne tous les messages contenant la phrase exacte, sans espace.<p>Le moins (-) elimine les mots. C'est à dire que, une recherche pour <B>dog</B> and <B>-cat</B> renvoie les messages contenant <b>dog</b> mais pas <b>cat</b>. Vous pouvez mettre des moins dans une phrases entre guillemet, comme <B>dog -\"siamiese cat\"</B>.<p>Le moteur de recherche ne différencie pas majuscule et minuscule, et cherche dans le titre, le corps et l'auteur.";
  $lNoMatches       = "Impossible de trouver :(";
  $lMessageBodies   = "Corps du message (plus lent)";
  $lMoreMatches     = "Plus de résultats";
  $lPrevMatches     = "Résultat précédent";
  $lLastPostDate    = "Dernier message";
  $lNumPosts        = "Messages";
  $lForumFolder     = "Répertoire du forum";
  $lEmailMe         = "Envoyer un mail à l'adresse ci-dessus, en cas de réponse.";
  $lEmailAlert      = "Vous devez entrer une adresse mail correcte pour que nous puissions vous répondre.";
  $lViolationTitle  = "Désolé...";
  $lViolation       = "Le message n'est pas valable à cause de votre Adresse IP, du nom que vous avez entré, ou de l'adresse e-mail que vous avez entrée. Ce n'est peut-&ecirc;tre pas votre faute.  Essayez un autre nom et/ou mail.  Si vous ne pouvez toujours pas répondre, contactez <a href=\"mailto:$ForumModEmail\">$ForumModEmail</a> pour plus d'explications.";
  $lNotFound        = "Le message demandé n'à pas été trouvé.  Pour de l'aide contactez <a href=\"mailto:$ForumModEmail\">$ForumModEmail</a>";
  $mess_for_grp     = "Forum";
// This function takes a date string in the ANSI format
// (YYYY-MM-DD HH:MM:SS) and formats it for display.
// The default is for US English, MM-DD-YY HH:MM.
// See http://www.php.net/manual/function.date.php
// for options on the date() formatting function.
// The $tzoffset variable is number of hours your display
// time will be offset from the server's local timezone.
// Negative values are legal, but fractions/decimals are not.

  function dateFormat($datestamp){
    $tzoffset = 0;
    if ($datestamp == "0000-00-00") {
      $datestamp = "0000-00-00 00:00:00";
    }
    list($date,$time) = explode(" ",$datestamp);
    list($year,$month,$day) = explode("-",$date);
    list($hour,$minute,$second) = explode(":",$time);
    $hour = $hour + $tzoffset;
    $tstamp = mktime($hour,$minute,$second,$month,$day,$year);
    $sDate = date("d/m/Y H:i",$tstamp);
    return $sDate;
  }

?>
