<?php
require ('../admin.inc.php');
require ('../fonction.inc.php');
require ('../fonction_html.inc.php');

function req_insert($sql)
{
   $insertion = mysql_query($sql);
}
$connect = mysql_connect($adresse,$log,$mdp);
$sql = "CREATE DATABASE IF NOT EXISTS ".$bdd;
req_insert($sql);
mysql_select_db($bdd,mysql_connect($adresse,$log,$mdp));

$sql = "CREATE TABLE IF NOT EXISTS `activite` (
  `act_cdn` smallint(5) unsigned NOT NULL auto_increment,
  `act_seq_no` smallint(5) unsigned NOT NULL default '0',
  `act_ordre_nb` smallint(5) unsigned NOT NULL default '0',
  `act_nom_lb` varchar(255) NOT NULL default '',
  `act_consigne_cmt` longtext,
  `act_commentaire_cmt` longtext NOT NULL,
  `act_ress_on` enum('OUI','NON') NOT NULL default 'OUI',
  `act_ress_no` smallint(3) unsigned default NULL,
  `act_duree_nb` smallint(5) unsigned NOT NULL default '0',
  `act_passagemult_on` enum('OUI','NON') NOT NULL default 'NON',
  `act_acquittement_lb` enum('RESSOURCE','APPRENANT','FORMATEUR_REFERENT') NOT NULL default 'APPRENANT',
  `act_notation_on` enum('OUI','NON') NOT NULL default 'NON',
  `act_devoirarendre_on` enum('OUI','NON') NOT NULL default 'NON',
  `act_auteur_no` smallint(5) NOT NULL default '1',
  `act_create_dt` date NOT NULL default '0000-00-00',
  `act_modif_dt` date NOT NULL default '0000-00-00',
  `act_publique_on` tinyint(1) NOT NULL default '1',
  `act_flag_on` tinyint(1) NOT NULL default '1',
  PRIMARY KEY  (`act_cdn`)
) ENGINE=MyISAM AUTO_INCREMENT=1 ;";

req_insert($sql);
$sql = "SELECT all FROM activite";
if (mysql_query($sql) == FALSE)
{
   $sql = "INSERT INTO `activite` (`act_cdn`, `act_seq_no`, `act_ordre_nb`, `act_nom_lb`, `act_consigne_cmt`, `act_commentaire_cmt`, `act_ress_on`, `act_ress_no`, `act_duree_nb`, `act_passagemult_on`, `act_acquittement_lb`, `act_notation_on`, `act_devoirarendre_on`, `act_auteur_no`, `act_create_dt`, `act_modif_dt`, `act_publique_on`, `act_flag_on`) VALUES
   (1, 3, 1, 'Quizz sur l''Italie', 'Faites le quizz jusqu&#39;au bout', 'Avec une consigne visuelle', 'OUI', 652, 3, 'OUI', 'RESSOURCE', 'OUI', 'NON', 1, '2012-06-28', '2012-06-28', 1, 1);";
 req_insert($sql);
}else
   $avis = 1;


$sql = "CREATE TABLE IF NOT EXISTS `apprenants` (
  `id` bigint(20) unsigned NOT NULL default '0',
  `datestamp` datetime NOT NULL default '0000-00-00 00:00:00',
  `thread` int(11) NOT NULL default '0',
  `parent` int(11) NOT NULL default '0',
  `author` char(37) NOT NULL default '',
  `subject` char(255) NOT NULL default '',
  `email` char(200) NOT NULL default '',
  `attachment` char(64) NOT NULL default '',
  `host` char(50) NOT NULL default '',
  `email_reply` char(1) NOT NULL default 'N',
  `approved` char(1) NOT NULL default 'N',
  `msgid` char(100) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `author` (`author`),
  KEY `datestamp` (`datestamp`),
  KEY `subject` (`subject`),
  KEY `thread` (`thread`),
  KEY `parent` (`parent`),
  KEY `approved` (`approved`),
  KEY `msgid` (`msgid`)
) ENGINE=MyISAM;";

req_insert($sql);

$sql = "CREATE TABLE IF NOT EXISTS `apprenants_bodies` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `body` text NOT NULL,
  `thread` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `thread` (`thread`)
) ENGINE=MyISAM AUTO_INCREMENT=5 ;";
req_insert($sql);

$sql = "CREATE TABLE IF NOT EXISTS `chatter` (
  `id` tinyint(2) NOT NULL default '0',
  `parler` tinyint(1) NOT NULL default '0',
  `login` varchar(255) NOT NULL default '',
  `appelant` varchar(255) NOT NULL default ''
) ENGINE=MyISAM;";
req_insert($sql);

$sql = "CREATE TABLE IF NOT EXISTS `compteur` (
  `heureCon` int(10) NOT NULL default '0',
  `ipCon` char(20) NOT NULL default '',
  `dateCon` date NOT NULL default '0000-00-00',
  `lheure` time NOT NULL default '00:00:00'
) ENGINE=MyISAM;";
req_insert($sql);

$sql = "CREATE TABLE IF NOT EXISTS `echange_grp` (
  `ech_cdn` int(5) NOT NULL auto_increment,
  `ech_path_lb` varchar(255) NOT NULL default '',
  `ech_grp_no` smallint(5) NOT NULL default '0',
  `ech_auteur_no` smallint(5) NOT NULL default '0',
  `ech_date_dt` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`ech_cdn`)
) ENGINE=MyISAM COMMENT='Espace échange des groupes' AUTO_INCREMENT=1 ;";
req_insert($sql);


$sql = "CREATE TABLE IF NOT EXISTS `faq` (
  `faq_cdn` smallint(3) NOT NULL auto_increment,
  `faq_question_lb_fr` varchar(255) NOT NULL default '',
  `faq_reponse_lb_fr` text NOT NULL,
  `faq_question_lb_en` varchar(255) NOT NULL default '',
  `faq_reponse_lb_en` text NOT NULL,
  `faq_question_lb_ru` varchar(255) NOT NULL default '',
  `faq_reponse_lb_ru` text NOT NULL,
  `faq_auteur_no` int(11) NOT NULL default '0',
  `faq_typutil_lb` varchar(40) NOT NULL default '',
  KEY `faq_cdn` (`faq_cdn`)
) ENGINE=MyISAM COMMENT='Questions/Réponses' AUTO_INCREMENT=1 ;";
req_insert($sql);

$sql = "INSERT INTO `faq` (`faq_cdn`, `faq_question_lb_fr`, `faq_reponse_lb_fr`, `faq_question_lb_en`, `faq_reponse_lb_en`, `faq_question_lb_ru`, `faq_reponse_lb_ru`, `faq_auteur_no`, `faq_typutil_lb`) VALUES
(1, 'Comment accéder à mes contenus de formation ?', 'Il y a deux façons d''accéder au contenu de vos formations.\r\nSi vous êtes en train de consulter cette page, c''est que vous en avez au moins utilisé une.\r\nA - Dans votre page d''accueil général que vous avez atteint en vous authentifiant, deux liens vers une de vos formations vous sont proposés :\r\n    1 - le nom de la formation qui vous mène à la page d''accueil de cette formation,\r\n    2 - Une flèche située à droite face à chaque formation qui vous permet d''ouvrir directement la dernière séquence consultée précédement.\r\nB - Si de nouveaux messages sont arrivés sur le forum d''une de vos formations, vous allez les consulter en cliquant sur le chiffre placé sous la case \"Nouveau(x) message(s) dans le forum\". La formation correspondant à ce forum est activée par la même occasion et vous pouvez dès lors choisir dans le menu \"Formation en cours > Présentation de la formation\" pour accéder à vos parcours et vos séquences déroulés dans le cadre de gauche.', '', '', '', '', 0, 'APPRENANT'),
(2, 'Qu''est-ce que le forum? Comment est-ce que cela fonctionne ?', 'Un forum est un espace asynchrone d''échange de messages. asynchrone veut dire qu''on n''est pas instantanément informé qu''un message vient d''y être déposé contrairement au CHAT qui est un espace d''échange pseudo-synchrone.\r\nCet espace permet à chaque personne inscrite à une formation d''y rédiger un message, un appel à l''aide afin que les autres lisent ce message et puissent y répondre s''ils peuvent apporter une suggestion ou une aide.\r\nLes apprenants y parlent des problèmes qu''ils rencontrent dans l''utilisation de la plate-forme et dans le déroulement de leur formation.\r\nComme leurs formateurs et tuteurs y accédent aussi, ces derniers lisent souvent ces messages et échangent avec tous les apprenants des informations par ce biais.\r\nIl est possible d''envoyer un email au rédacteur d''un message en cliquant sur son nom lors de la consultation du message.\r\nOn peut y faire une recherche par mot-clef, réduire l''arborescence et la dérouler.\r\nLe responsable de la formation peut à tout moment effacer un message hors de propos ou le masquer.\r\nMais c''est à l''usage qu''on découvre toutes ses fonctionnalités.', '', '', '', '', 0, 'APPRENANT'),
(3, 'J''ai besoin de contacter mon formateur ; comment dois-je procéder ?', 'Cela dépend du contexte.\r\n- Pour le contacter spontanément, il suffit d''aller dans  \"Formation en cours > Annuaire\" et cliquer sur son adresse email.\r\n- Si c''est pour une modification des dates de fin de séquence parce que vous êtes en dépassement, il vous suffit de cliquer sur la formation suivie de la page d''accueil général puis de choisir le nom de da séquence hors délais.\r\n- A partir du forum, cliquez sur le nom de votre formateur\r\n- A partir de votre messagerie, cliquez sur le nom de votre formateur s''il est dans la liste des expéditeurs de messages\r\nDans tous ces cas une fenêtre s''ouvre pour vous permettre de lui envoyer un email, le destinataire étant pris automatiquement en compte.\r\nVous pouvez aussi cliquer sur \"Chat\" dans l''onglet \"Communication\" du menu afin de voir s''il est connecté.\r\nS''il l''est, en cliquant sur son nom, vous l''invitez à une discussion avec vous.\r\nDans ce cas, il faut qu''il ait cliqué sur un lien quelconque pour que s''ouvre à lui la fenêtre de chat comportant la notification \"Untel vous demande de le rejoindre sur le chat\".', '', '', '', '', 0, 'APPRENANT'),
(4, 'Une de mes activités est \"En attente\" ; qu''est-ce que cela signifie ?', 'Cela signifie que tout ce qui relève de vous est fait.\r\n- Un travail à rendre a été envoyé et vous êtes en attente de la correction et de la notation.\r\n Et c''est cela qui validera et acquittera complètement l''activité.\r\n- Une activité correspondant à un regroupement en établissement, et c''est le formateur qui doit valider son acquittement.', '', '', '', '', 0, 'APPRENANT'),
(5, 'Comment puis-je savoir qui est en formation avec moi pour demander de l''aide ?', 'Pour connaître les autres apprenants quivant votre formation, il vous suffit de cliquer sur \"Formation en cours > Annuaire\".\r\nLa liste des apprenants s''affiche ainsi que la liste de tous les intervenants :\r\n- formateurs\r\n- tuteur\r\n- Prescripteur\r\nVous pouvez leur envoyer un email ou ouvrir leur fiche d''identité.\r\nEn passant sur le nom, vous pouvez voir la photo s''il y a lieu.\r\nVous pouvez toutefois demander de l''aide via le forum lié à cette formation.', '', '', '', '', 0, 'APPRENANT'),
(6, 'Mon tuteur me dit qu''il a proposé des créneaux de rendez-vous tutorat ; où puis-je les consulter sur la plate-forme ?', 'En effet, le tuteur, ou le formateur peut proposer un certain nombre de rendez-vous dont il définit le mode (Chat, téléphone, rencontre, visiophonie).\r\nCliquez sur \"Rendez-vous\" dans le menu.\r\nDès lors qu''un ou plusieurs rendez-vous sont proposés, un lien intitulé \"Prendre un rendez-vous\" s''affiche sur la gauche de votre agenda.\r\nEn l''activant, une fenêtre s''ouvre pour vous indiquer le nom du tuteur ou formateurs, les dates,créneaux et modes des rendez-vous ainsi que vos rendez-vous déjà pris avec les uns ou les autres.\r\nL''activation de l''un de ces liens vous permet d''ouvrir l''agenda du formateur et de prendre un rendez-vous en cliquant sur le lien intitulé \"Libre\".\r\nLe rendez-vous est automatiquement pris.\r\nVotre tuteur et vous-même recevrez dans votre messagerie interne et externe un mail de notification. ', '', '', '', '', 0, 'APPRENANT'),
(7, 'Une de mes ressources ne s''ouvre pas. Que dois-je faire ?', 'Si la ressource existe, elle doit s''ouvrir :\r\n- dans une page de navigateur en général,\r\n- dans son applicatif s''il s''agit d''un document Microsoft (excel, word, powerpoint) ou d''un  document PDF. Dans les deux cas vous devez disposer de ces applicatifs sur votre poste.\r\nSi votre poste ne dispose pas d''Adobe Acrobat Reader vous pouvez lancer la procédure de téléchargement dans \"Aide > Otils indispensables\".\r\nSi la page affiche une erreur sytème vous disant que cette URL n''existe pas, c''est que la ressource n''existe plus.\r\nEnvoyez un message au formateur responsable de la séquencepour qu''il intervienne et mette une autre ressource dans le lien.', '', '', '', '', 0, 'APPRENANT');";
req_insert($sql);

$sql = "CREATE TABLE IF NOT EXISTS `favoris` (
  `fav_cdn` smallint(5) NOT NULL default '0',
  `fav_utilisateur_no` smallint(5) NOT NULL default '0',
  `fav_url_lb` varchar(255) default NULL,
  `fav_seq_no` smallint(3) NOT NULL default '0',
  `fav_titre_lb` varchar(255) default NULL,
  `fav_public_on` enum('PERSONNEL','TOUS','GROUPE') default NULL,
  `fav_desc_lb` text,
  PRIMARY KEY  (`fav_cdn`)
) ENGINE=MyISAM;";
req_insert($sql);

$sql = "CREATE TABLE IF NOT EXISTS `fiche_suivi` (
  `fiche_cdn` int(11) NOT NULL default '0',
  `fiche_utilisateur_no` int(11) NOT NULL default '0',
  `fiche_auteur_no` int(11) NOT NULL default '0',
  `fiche_qualite_lb` enum('Administrateur','Prescripteur','Formateur','Tuteur','Inscripteur','Apprenant') NOT NULL default 'Administrateur',
  `fiche_date_dt` date NOT NULL default '0000-00-00',
  `fiche_heure_dt` time NOT NULL default '00:00:00',
  `fiche_commentaire_cmt` text NOT NULL,
  `fiche_grp_no` smallint(5) NOT NULL default '0',
  `fiche_parc_no` smallint(5) NOT NULL default '0',
  `fiche_seq_no` smallint(5) NOT NULL default '0',
  `fiche_act_no` smallint(5) NOT NULL default '0',
  `fiche_typaction_lb` varchar(255) NOT NULL default '',
  `fiche_autraction_lb` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`fiche_cdn`)
) ENGINE=MyISAM COMMENT='Fiche de suivi Apprenant';";
req_insert($sql);
$sql = "SELECT all FROM fiche_suivi";
if (mysql_query($sql) == FALSE)
{
   $sql = "INSERT INTO `fiche_suivi` (`fiche_cdn`, `fiche_utilisateur_no`, `fiche_auteur_no`, `fiche_qualite_lb`, `fiche_date_dt`, `fiche_heure_dt`, `fiche_commentaire_cmt`, `fiche_grp_no`, `fiche_parc_no`, `fiche_seq_no`, `fiche_act_no`, `fiche_typaction_lb`, `fiche_autraction_lb`) VALUES
(1, 2, 1, 'Inscripteur', '2012-06-28', '09:51:59', 'Inscription dans la plate-forme', 0, 0, 0, 0, '', 'observation'),
(2, 3, 1, 'Inscripteur', '2012-06-28', '09:51:59', 'Inscription dans la plate-forme', 0, 0, 0, 0, '', 'observation'),
(3, 4, 1, 'Inscripteur', '2012-06-28', '09:51:59', 'Inscription dans la plate-forme', 0, 0, 0, 0, '', 'observation'),
(4, 5, 1, 'Inscripteur', '2012-06-28', '09:51:59', 'Inscription dans la plate-forme', 0, 0, 0, 0, '', 'observation'),
(5, 6, 1, 'Inscripteur', '2012-06-28', '09:51:59', 'Inscription dans la plate-forme', 0, 0, 0, 0, '', 'observation'),
(6, 7, 1, 'Inscripteur', '2012-06-28', '09:55:09', 'Inscription dans la plate-forme', 0, 0, 0, 0, '', 'observation'),
(7, 8, 1, 'Inscripteur', '2012-06-28', '09:55:09', 'Inscription dans la plate-forme', 0, 0, 0, 0, '', 'observation'),
(8, 9, 1, 'Inscripteur', '2012-06-28', '09:59:09', 'Inscription dans la plate-forme', 0, 0, 0, 0, '', 'observation'),
(9, 10, 1, 'Inscripteur', '2012-06-28', '09:59:09', 'Inscription dans la plate-forme', 0, 0, 0, 0, '', 'observation'),
(10, 11, 1, 'Inscripteur', '2012-06-28', '09:59:09', 'Inscription dans la plate-forme', 0, 0, 0, 0, '', 'observation'),
(11, 12, 1, 'Inscripteur', '2012-06-28', '10:01:33', 'Inscription dans la plate-forme', 0, 0, 0, 0, '', 'observation'),
(12, 13, 1, 'Inscripteur', '2012-06-28', '10:01:33', 'Inscription dans la plate-forme', 0, 0, 0, 0, '', 'observation'),
(13, 3, 1, '', '2012-06-28', '11:06:14', 'Prescription de la séquence Scorm1.2', 1, 1, 0, 0, 'Affectation à une formation', ''),
(14, 4, 1, '', '2012-06-28', '11:06:21', 'Prescription de la séquence Scorm1.2', 1, 1, 0, 0, 'Affectation à une formation', ''),
(15, 6, 1, '', '2012-06-28', '11:06:28', 'Prescription de la séquence Scorm1.2', 1, 1, 0, 0, 'Affectation à une formation', '');";
req_insert($sql);
}


$sql = "CREATE TABLE IF NOT EXISTS `formateurs` (
  `id` bigint(20) unsigned NOT NULL default '0',
  `datestamp` datetime NOT NULL default '0000-00-00 00:00:00',
  `thread` int(11) NOT NULL default '0',
  `parent` int(11) NOT NULL default '0',
  `author` char(37) NOT NULL default '',
  `subject` char(255) NOT NULL default '',
  `email` char(200) NOT NULL default '',
  `attachment` char(64) NOT NULL default '',
  `host` char(50) NOT NULL default '',
  `email_reply` char(1) NOT NULL default 'N',
  `approved` char(1) NOT NULL default 'N',
  `msgid` char(100) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `author` (`author`),
  KEY `datestamp` (`datestamp`),
  KEY `subject` (`subject`),
  KEY `thread` (`thread`),
  KEY `parent` (`parent`),
  KEY `approved` (`approved`),
  KEY `msgid` (`msgid`)
) ENGINE=MyISAM;";
req_insert($sql);

$sql = "CREATE TABLE IF NOT EXISTS `formateurs_bodies` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `body` text NOT NULL,
  `thread` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `thread` (`thread`)
) ENGINE=MyISAM AUTO_INCREMENT=1 ;";
req_insert($sql);

$sql = "CREATE TABLE IF NOT EXISTS `forum_lecture` (
  `forlec_id` int(8) NOT NULL auto_increment,
  `forlec_topic_no` int(8) NOT NULL default '0',
  `forlec_forum_no` smallint(3) NOT NULL default '0',
  `forlec_user_no` smallint(5) NOT NULL default '0',
  PRIMARY KEY  (`forlec_id`)
) ENGINE=MyISAM COMMENT='Gestion des flags de lecture des forums' AUTO_INCREMENT=1 ;";
req_insert($sql);


$sql = "CREATE TABLE IF NOT EXISTS `forums` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `active` smallint(6) NOT NULL default '0',
  `description` varchar(255) NOT NULL default '',
  `config_suffix` varchar(50) NOT NULL default '',
  `folder` char(1) NOT NULL default '0',
  `parent` int(11) NOT NULL default '0',
  `display` int(11) NOT NULL default '0',
  `table_name` varchar(50) NOT NULL default '',
  `moderation` char(1) NOT NULL default 'n',
  `mod_email` varchar(50) NOT NULL default '',
  `mod_pass` varchar(50) NOT NULL default '',
  `email_list` varchar(50) NOT NULL default '',
  `email_return` varchar(50) NOT NULL default '',
  `email_tag` varchar(50) NOT NULL default '',
  `check_dup` smallint(6) NOT NULL default '0',
  `multi_level` smallint(6) NOT NULL default '0',
  `collapse` smallint(6) NOT NULL default '0',
  `flat` smallint(6) NOT NULL default '0',
  `staff_host` varchar(50) NOT NULL default '',
  `lang` varchar(50) NOT NULL default '',
  `html` varchar(40) NOT NULL default 'N',
  `table_width` varchar(4) NOT NULL default '',
  `table_header_color` varchar(7) NOT NULL default '',
  `table_header_font_color` varchar(7) NOT NULL default '',
  `table_body_color_1` varchar(7) NOT NULL default '',
  `table_body_color_2` varchar(7) NOT NULL default '',
  `table_body_font_color_1` varchar(7) NOT NULL default '',
  `table_body_font_color_2` varchar(7) NOT NULL default '',
  `nav_color` varchar(7) NOT NULL default '',
  `nav_font_color` varchar(7) NOT NULL default '',
  `allow_uploads` char(1) NOT NULL default 'N',
  PRIMARY KEY  (`id`),
  KEY `name` (`name`),
  KEY `active` (`active`),
  KEY `parent` (`parent`)
) ENGINE=MyISAM AUTO_INCREMENT=1 ;";
req_insert($sql);
$sql = "SELECT all FROM forums";
if (mysql_query($sql) == FALSE)
{
   $sql = "INSERT INTO `forums` VALUES (4, 'forum Apprenants', 1, 'Espace réservé aux apprenants afin qu''ils y échangent toutes sortes d''idées ou d''opinions en relation avec cette plateforme de formation.', '', '0', 0, 1000, 'apprenants', 'n', '$ForumModEmail', '$ForumModPass', '', '', '', 0, 1, 1, 0, '', 'lang/french.php', 'N', '540', '#000080', '#FFFFFF', '#FFFFFF', '#FFFFEA', '#000000', '#000000', '#FFFFEA', '#000000', 'N'),
      (1, 'Forum Formateurs', 1, 'Cet espace est réservé aux formateurs et leur permet  d''échanger vos expériences.', '', '0', 0, 1000, 'formateurs', 'n', '$ForumModEmail', '$ForumModPass', '', '', '', 1, 1, 1, 0, '', 'lang/french.php', 'N', '640', '#000080', '#FFFFFF', '#FFFFFF', '#FFFFEA', '#000000', '#000000', 'silver', '#000080', 'N'),
      (3, 'Forum Libre', 1, 'Forum libre où toute discussion est la bienvenue.', '', '0', 0, 1000, 'libre', 'n', '$ForumModEmail', '$ForumModPass', '', '', '', 0, 1, 1, 0, '', 'lang/french.php', 'N', '540', '#000080', '#FFFFFF', '#FFFFFF', '#FFFFEA', '#000000', '#000000', '#FFFFEA', '#000000', 'N'),
      (5, 'Formation test', 1, 'Groupe:', '', '0', 0, 60, 'groupe1', 'n', '$ForumModEmail', '$ForumModPass', '', '', '', 0, 1, 1, 0, '', 'lang/french.php', 'N', '540', '#000080', '#FFFFFF', '#FFFFFF', '#FFFFEA', '#000000', '#000000', '#FFFFEA', '#000000', 'Y');";
   req_insert($sql);
}
else
  $avis = 1;


$sql = "CREATE TABLE IF NOT EXISTS `groupe` (
  `grp_cdn` smallint(3) unsigned NOT NULL auto_increment,
  `grp_nom_lb` varchar(255) NOT NULL default '',
  `grp_commentaire_cmt` longtext,
  `grp_formobject_lb` text NOT NULL,
  `grp_formdesc_cmt` text NOT NULL,
  `grp_resp_no` mediumint(5) NOT NULL default '0',
  `grp_publique_on` tinyint(1) NOT NULL default '1',
  `grp_tuteur_no` int(11) NOT NULL default '0',
  `grp_classe_on` tinyint(1) NOT NULL default '1',
  `grp_flag_on` tinyint(1) NOT NULL default '1',
  `grp_datecreation_dt` DATETIME DEFAULT '2004-01-01 00:00:01' NOT NULL ,
  `grp_datemodif_dt` DATETIME DEFAULT '2004-01-01 00:00:01' NOT NULL ,
  PRIMARY KEY  (`grp_cdn`)
) ENGINE=MyISAM AUTO_INCREMENT=1 ;";
req_insert($sql);
$sql = "SELECT all FROM groupe";
if (mysql_query($sql) == FALSE)
{
   $sql = "INSERT INTO `groupe` (`grp_cdn`, `grp_nom_lb`, `grp_commentaire_cmt`, `grp_formobject_lb`, `grp_formdesc_cmt`, `grp_resp_no`, `grp_publique_on`, `grp_tuteur_no`, `grp_classe_on`, `grp_flag_on`, `grp_datecreation_dt`, `grp_datemodif_dt`) VALUES
        (1, 'Formation test', 'Pour voir', 'Pour voir', 'Pour voir', 1, 1, 13, 1, 1, '2012-06-28 11:03:06', '2012-06-28 11:03:06');";
   req_insert($sql);
}
else
  $avis = 1;

$sql = "CREATE TABLE IF NOT EXISTS `groupe_parcours` (
  `gp_cdn` smallint(5) NOT NULL auto_increment,
  `gp_grp_no` smallint(5) NOT NULL default '0',
  `gp_parc_no` smallint(5) NOT NULL default '0',
  `gp_formateur_no` smallint(5) NOT NULL default '0',
  `gp_db_dt` date NOT NULL default '0000-00-00',
  `gp_df_dt` date NOT NULL default '0000-00-00',
  `gp_ordre_no` smallint(2) NOT NULL default '1',
  PRIMARY KEY  (`gp_cdn`)
) ENGINE=MyISAM COMMENT='Parcours type pour chaque groupe' AUTO_INCREMENT=1 ;";
req_insert($sql);
$sql = "SELECT all FROM groupe_parcours";
if (mysql_query($sql) == FALSE)
{
   $sql = "INSERT INTO `groupe_parcours` (`gp_cdn`, `gp_grp_no`, `gp_parc_no`, `gp_formateur_no`, `gp_db_dt`, `gp_df_dt`, `gp_ordre_no`) VALUES
        (1, 1, 1, 9, '2012-06-28', '2015-06-19', 1);";
   req_insert($sql);
}
else
  $avis = 1;

$sql = "CREATE TABLE IF NOT EXISTS `inscription` (
  `insc_cdn` smallint(5) unsigned NOT NULL auto_increment,
  `insc_apprenant_no` smallint(5) unsigned NOT NULL default '0',
  `insc_referentiel_no` smallint(5) unsigned NOT NULL default '0',
  PRIMARY KEY  (`insc_cdn`)
) ENGINE=MyISAM AUTO_INCREMENT=1 ;";
req_insert($sql);

$sql = "CREATE TABLE IF NOT EXISTS `libre` (
  `id` bigint(20) unsigned NOT NULL default '0',
  `datestamp` datetime NOT NULL default '0000-00-00 00:00:00',
  `thread` int(11) NOT NULL default '0',
  `parent` int(11) NOT NULL default '0',
  `author` char(37) NOT NULL default '',
  `subject` char(255) NOT NULL default '',
  `email` char(200) NOT NULL default '',
  `attachment` char(64) NOT NULL default '',
  `host` char(50) NOT NULL default '',
  `email_reply` char(1) NOT NULL default 'N',
  `approved` char(1) NOT NULL default 'N',
  `msgid` char(100) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `author` (`author`),
  KEY `datestamp` (`datestamp`),
  KEY `subject` (`subject`),
  KEY `thread` (`thread`),
  KEY `parent` (`parent`),
  KEY `approved` (`approved`),
  KEY `msgid` (`msgid`)
) ENGINE=MyISAM;";
req_insert($sql);

$sql = "CREATE TABLE IF NOT EXISTS `libre_bodies` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `body` text NOT NULL,
  `thread` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `thread` (`thread`)
) ENGINE=MyISAM AUTO_INCREMENT=1 ;";
req_insert($sql);

$sql = "CREATE TABLE IF NOT EXISTS `log` (
  `log_cdn` int(8) NOT NULL auto_increment,
  `login` varchar(50) NOT NULL default '',
  `date_debut` date NOT NULL default '0000-00-00',
  `heure_debut` time NOT NULL default '00:00:00',
  `date_fin` date NOT NULL default '0000-00-00',
  `heure_fin` time NOT NULL default '00:00:00',
  `duree` int(5) NOT NULL default '0',
  `serveur` varchar(250) NOT NULL default '',
  `ip` varchar(50) NOT NULL default '',
  `log_agent` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`log_cdn`)
) ENGINE=MyISAM AUTO_INCREMENT=1 ;";
req_insert($sql);

$sql = "CREATE TABLE IF NOT EXISTS `message` (
  `msg_cdn` int(5) NOT NULL auto_increment,
  `msg_contenu_cmt` longtext NOT NULL,
  `msg_auteur_no` int(11) NOT NULL default '0',
  `msg_groupe_no` int(3) NOT NULL default '0',
  `msg_apprenant_no` int(4) NOT NULL default '0',
  `msg_tous_on` tinyint(1) NOT NULL default '0',
  `msg_dhdeb_dt` date NOT NULL default '0000-00-00',
  `msg_dhfin_dt` date NOT NULL default '0000-00-00',
  KEY `msg_cdn` (`msg_cdn`)
) ENGINE=MyISAM COMMENT='Messages d''alerte' AUTO_INCREMENT=1 ;";
req_insert($sql);

$sql = "CREATE TABLE IF NOT EXISTS `messagerie` (
  `mess_cdn` smallint(5) NOT NULL auto_increment,
  `envoyeur` mediumint(5) NOT NULL default '0',
  `origine` varchar(255) NOT NULL default '',
  `contenu` text NOT NULL,
  `date` varchar(25) NOT NULL default '',
  `sujet` varchar(255) NOT NULL default '',
  `mess_fichier_lb` varchar(255) default NULL,
  `id_user` int(11) NOT NULL default '0',
  `lu` tinyint(1) NOT NULL default '1',
  `supprime` tinyint(1) NOT NULL default '0',
  `supp_envoi` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`mess_cdn`)
) ENGINE=MyISAM AUTO_INCREMENT=1 ;";
req_insert($sql);

$sql = "CREATE TABLE IF NOT EXISTS `p4_admin` (
  `ID` int(11) NOT NULL auto_increment,
  `dt_last_liste` datetime default NULL,
  `dt_last_chat` datetime default NULL,
  `dt_last_p4` datetime default NULL,
  `dt_last_admin` datetime default NULL,
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM AUTO_INCREMENT=1 ;";
req_insert($sql);

$sql = "SELECT * FROM p4_admin";
if (mysql_query($sql) == FALSE)
{
   $datedujour = date("Y-m-d H:i:s" ,time());
   $sql = "INSERT INTO `p4_admin` VALUES (1, '$datedujour', '$datedujour', '$datedujour', '$datedujour');";
   req_insert($sql);
}

$sql = "CREATE TABLE IF NOT EXISTS `p4_msg` (
  `ID` int(11) NOT NULL auto_increment,
  `user` varchar(50) default NULL,
  `text` text,
  `dest` varchar(50) default NULL,
  `dt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM AUTO_INCREMENT=1 ;";
req_insert($sql);


$sql = "CREATE TABLE IF NOT EXISTS `p4_salle` (
  `ID` int(11) NOT NULL auto_increment,
  `user` char(50) default NULL,
  `user_ID` char(50) default NULL,
  `dt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `dt_first` datetime default NULL,
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM AUTO_INCREMENT=1 ;";
req_insert($sql);

$sql = "CREATE TABLE IF NOT EXISTS `p4_user` (
  `ID` int(11) NOT NULL auto_increment,
  `login` varchar(50) default NULL,
  `password` varchar(50) default NULL,
  `mail` varchar(120) default NULL,
  `dt` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM AUTO_INCREMENT=1 ;";
req_insert($sql);

$sql = "CREATE TABLE IF NOT EXISTS `param_foad` (
  `param_cdn` mediumint(5) NOT NULL auto_increment,
  `param_typ_lb` varchar(255) NOT NULL default '',
  `param_etat_lb` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`param_cdn`)
) ENGINE=MyISAM COMMENT='Table des paramètres de la plateforme' AUTO_INCREMENT=1 ;";
req_insert($sql);

$sql = "INSERT INTO `param_foad` VALUES (1, 'bckgr_img', 'images/fondtitre.jpg'),
(2, 'logo', ''),(3, 'url', ''),(4, 'bienvenue_img', 'images/menu/haut_forma_nofse.jpg'),
(5, 'bienvenue1_img', 'images/menu/haut_forma_nofse.jpg'),(6, 'couleur_fond', '808000'),(7, 'cdi', ''),(8, 'nbr_pages_ress', '7'),
(9, 'multi-centre', 'NON'),(10, 'bkg', '#FFFFFF'),(11, 'nb_pg_mod', '10'),(12, 'nb_pg_seq', '10'),(13, 'nb_pg_act', '15'),
(14, 'label_url', ''),('15', 'forum_libre', 'OUI'), ('16', 'chat', 'OUI'), ('17', 'rss', 'OUI'),('18', 'adresse', '$bdd'),
('19', 'rss', 'OUI'), ('20', 'favoris', 'OUI'), ('21', 'style_devoirs', 'devoirs'),
('22' , 'seqduref', 'NON'),('23' , 'mess_inscription', 'NON'),('24', 'mailcomment', 'NON');";
req_insert($sql);

$sql = "CREATE TABLE IF NOT EXISTS `param_referentiel` (
  `paramref_cdn` smallint(5) NOT NULL auto_increment,
  `paramref_type_lb` varchar(30) NOT NULL default '',
  `paramref_nom_lbfr` varchar(30) NOT NULL default '',
  `paramref_nomabr_lbfr` char(3) NOT NULL default '',
  KEY `paramref_cdn` (`paramref_cdn`)
) ENGINE=MyISAM COMMENT='parametrage des referentiels' AUTO_INCREMENT=1 ;";
req_insert($sql);

$sql = "SELECT all FROM param_referentiel";
if (mysql_query($sql) == FALSE)
{
$sql = "INSERT INTO `param_referentiel` VALUES
(1, 'self_cursus', 'Formation spécifique', 'FS'),
(2, 'Diplome', 'Diplôme', 'DP'),
(3, 'option', 'Option', 'OPT'),
(4, 'module', 'Unité Capitalisable', 'UC'),
(5, 'objectif', 'Objectif', 'OI'),
(6, 'sous-objectif', 'Sous-Objectif', 'SO');";
req_insert($sql);
}

$sql = "CREATE TABLE IF NOT EXISTS `parametre` (
  `param_user` enum('APPRENANT','FORMATEUR_REFERENT','RESPONSABLE_FORMATION','TUTEUR','ADMINISTRATEUR') NOT NULL default 'APPRENANT',
  `param_ecran` enum('MEDIAN','NORMAL','PLEIN') NOT NULL default 'NORMAL'
) ENGINE=MyISAM;";
req_insert($sql);
$sql = "SELECT all FROM parametre";
if (mysql_query($sql) == FALSE)
{
$sql = "INSERT INTO `parametre` VALUES
('APPRENANT', 'NORMAL'),('FORMATEUR_REFERENT', 'NORMAL'),
('RESPONSABLE_FORMATION', 'NORMAL'),('TUTEUR', 'NORMAL'),('ADMINISTRATEUR', 'NORMAL');";
req_insert($sql);
}

$sql = "CREATE TABLE IF NOT EXISTS `parcours` (
  `parcours_cdn` smallint(5) UNSIGNED NOT NULL auto_increment,
  `parcours_nom_lb` text NOT NULL,
  `parcours_desc_cmt` text NOT NULL,
  `parcours_mots_clef` varchar(255) NOT NULL default '',
  `parcours_referentiel_no` smallint(5) unsigned NOT NULL default '0',
  `parcours_auteur_no` smallint(5) unsigned NOT NULL default '0',
  `parcours_create_dt` date NOT NULL default '0000-00-00',
  `parcours_modif_dt` date NOT NULL default '0000-00-00',
  `parcours_publique_on` tinyint(1) NOT NULL default '1',
  `parcours_type_on` tinyint(1) NOT NULL default '0',
  `parcours_type_lb` enum('NORMAL','SCORM 1.2','SCORM 2004') NOT NULL default 'NORMAL',
  PRIMARY KEY  (`parcours_cdn`)
) ENGINE=MyISAM PACK_KEYS=0 AUTO_INCREMENT=2 ;";
req_insert($sql);

$sql = "SELECT all FROM parcours";
if (mysql_query($sql) == FALSE)
{
    $sql = "INSERT INTO `parcours` (`parcours_cdn`, `parcours_nom_lb`, `parcours_desc_cmt`, `parcours_mots_clef`, `parcours_referentiel_no`, `parcours_auteur_no`, `parcours_create_dt`, `parcours_modif_dt`, `parcours_publique_on`, `parcours_type_on`, `parcours_type_lb`) VALUES
         (1, 'Scorm1.2', 'Comporte des séquences Scormées ', '', 0, 1, '2012-06-28', '2012-06-28', 1, 0, 'NORMAL');";
    req_insert($sql);
}else
   $avis=1;

$sql = "CREATE TABLE IF NOT EXISTS `plugins` (
  `plug_cdn` tinyint(2) NOT NULL auto_increment,
  `plug_tit_lb` varchar(50) NOT NULL default '',
  `plug_img_lb` varchar(255) NOT NULL default '',
  `plug_adr_cmt` text NOT NULL,
  `plug_desc_cmt` text NOT NULL,
  PRIMARY KEY  (`plug_cdn`)
) ENGINE=MyISAM COMMENT='Table des plugins' AUTO_INCREMENT=5 ;";
req_insert($sql);
$sql = "SELECT all FROM plugins";
if (mysql_query($sql) == FALSE)
{
    $sql = "INSERT INTO `plugins` VALUES (1, 'Flash 6', 'flash_get.gif', 'http://www.macromedia.com/shockwave/download/triggerpages_mmcom/flash-fr.html', 'Permet de lire les fichiers Flash 6'),
            (2, 'Acrobat Reader 5', 'getacro.gif', 'http://www.adobe.com/products/acrobat/readstep2.html', 'Permet de lire des fichiers PDF'),
            (3, 'Powerpoint', 'PPT.GIF', 'http://www.microsoft.com/downloads/details.aspx?familyid=428d5727-43ab-4f24-90b7-a94784af71a4&displaylang=fr', 'Permet la lecture de fichiers Powerpoint'),
            (4, 'IE 5.5', 'ie5_5.gif', 'http://www.microsoft.com/downloads/details.aspx?FamilyID=dabf04de-5627-4673-a075-7684fef3dd5b&DisplayLang=fr', 'Version minimale du navigateur à télécharger pour un bon fonctinnement de la plate-forme (PC)');";
    req_insert($sql);
}
$sql = "CREATE TABLE IF NOT EXISTS `prerequis` (
  `prereq_cdn` smallint(5) unsigned NOT NULL auto_increment,
  `prereq_seq_no` smallint(5) unsigned NOT NULL default '0',
  `prereq_typcondition_lb` enum('SEQUENCE','ACTIVITE','NOTE') NOT NULL default 'SEQUENCE',
  `prereq_seqcondition_no` smallint(5) unsigned NOT NULL default '0',
  `prereq_actcondition_no` smallint(5) unsigned default NULL,
  `prereq_notemin_nb1` float default NULL,
  `prereq_notemax_nb1` float default NULL,
  PRIMARY KEY  (`prereq_cdn`)
) ENGINE=MyISAM AUTO_INCREMENT=1 ;";
req_insert($sql);


$sql = "CREATE TABLE IF NOT EXISTS `referentiel` (
  `ref_parent_no` smallint(8) unsigned default NULL,
  `ref_local_on` enum('local','national') NOT NULL default 'national',
  `ref_nom_lb` varchar(255) NOT NULL default '',
  `ref_nomabrege_lb` varchar(255) NOT NULL default '',
  `ref_desc_cmt` longtext NOT NULL,
  `ref_niv_no` smallint(5) unsigned default NULL,
  `ref_dom_lb` varchar(255) NOT NULL default '',
  `ref_cdn` int(8) unsigned NOT NULL auto_increment,
  `ref_auteur_lb` varchar(50) NOT NULL default '',
  `ref_denom_lb` varchar(50) NOT NULL default '',
  `ref_nomparent_lb` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`ref_cdn`)
) ENGINE=MyISAM AUTO_INCREMENT=1 ;";
req_insert($sql);
$sql = "SELECT * FROM referentiel";
if (mysql_query($sql) == FALSE)
{
  $sql = "INSERT INTO `referentiel` VALUES (0, 'local', 'Formations courtes', 'FC', 'Formations dites courtes destinées à faire l''appoint en FOAD pour des formations initiales ou à servir des sessions courtes de préparation à des examens ou à l''aquisitions de certifications', 1, '', 1, 'super', 'Diplome', 'referentiel');";
  req_insert($sql);
  
   $sql = "INSERT INTO `referentiel` (`ref_parent_no`, `ref_local_on`, `ref_nom_lb`, `ref_nomabrege_lb`, `ref_desc_cmt`, `ref_niv_no`, `ref_dom_lb`, `ref_cdn`, `ref_auteur_lb`, `ref_denom_lb`, `ref_nomparent_lb`) VALUES
       (1, 'local', 'BTSA ACSE - D1.2 Informatique', 'BTSA ACSE - D1.2 Informatique', 'Acquérir l''usage des outils informatiques', 3, 'Informatique', 2, 'admin', 'Compétence', 'Formation spécifique'),
       (2, 'local', 'BTSA - D1.2 - OI 1', 'BTSA - D1.2 - OI 1', 'Sensibiliser aux champs d''application des nouvelles technologies', 3, 'Informatique', 3, 'admin', 'Savoir Détaillé', 'BTSA ACSE - D1.2 Informatique'),
       (2, 'local', 'BTSA - D1.2 - OI 2', 'BTSA - D1.2 - OI 2', 'Acquérir une autonomie d''utilisation', 3, 'Informatique', 4, 'admin', 'Savoir Détaillé', 'BTSA ACSE - D1.2 Informatique'),
       (4, 'local', 'BTSA - D1.2 - OI 21', 'BTSA - D1.2 - OI 21', 'Utiliser un produit informatique (matériel, logiciel, documentation)\r', 3, 'Informatique', 5, 'admin', 'Savoir Détaillé', 'BTSA - D1.2 - OI 2'),
       (4, 'local', 'BTSA - D1.2 - OI 22', 'BTSA - D1.2 - OI 22', 'Elaborer une démarche informatique en vue d''un usage autonome\r', 3, 'Informatique', 6, 'admin', 'Savoir Détaillé', 'BTSA - D1.2 - OI 2'),
       (2, 'local', 'BTSA - D1.2 - OI 3', 'BTSA - D1.2 - OI 3', 'Connaître les principes d''une démarche d''informatisation', 3, 'Informatique', 7, 'admin', 'Savoir Détaillé', 'BTSA ACSE - D1.2 Informatique'),
       (7, 'local', 'BTSA ACSE - D1.2 - OI 31', 'BTSA ACSE - D1.2 - OI 31', 'Sensibiliser à l''impact des nouvelles technologies de l''information et de la communication (NTIC)', 3, 'Informatique', 8, 'admin', 'Savoir Détaillé', 'BTSA - D1.2 - OI 3'),
       (7, 'local', 'BTSA ACSE - D1.2 - OI 32', 'BTSA ACSE - D1.2 - OI 32', 'Acquérir une autonomie d''utilisation', 3, 'Informatique', 9, 'admin', 'Savoir Détaillé', 'BTSA - D1.2 - OI 3');";
  req_insert($sql);
}

$sql = "CREATE TABLE IF NOT EXISTS `rendez_vous` (
  `rdv_cdn` smallint(5) unsigned NOT NULL auto_increment,
  `rdv_util_no` smallint(5) NOT NULL default '0',
  `rdv_tuteur_no` smallint(5) unsigned NOT NULL default '0',
  `rdv_apprenant_no` smallint(5) unsigned NOT NULL default '0',
  `rdv_creneau_nb` tinyint(3) unsigned NOT NULL default '0',
  `rdv_titre_lb` tinytext NOT NULL,
  `rdv_commentaire_cmt` longtext NOT NULL,
  `rdv_date_dt` date NOT NULL default '0000-00-00',
  `rdv_modecontact_lb` enum('AGENDA','TELEPHONE','CHAT','RENCONTRE','VISIO-CONF') NOT NULL default 'AGENDA',
  `rdv_grp_no` smallint(3) NOT NULL default '0',
  PRIMARY KEY  (`rdv_cdn`)
) ENGINE=MyISAM AUTO_INCREMENT=1 ;";
req_insert($sql);
$sql = "SELECT all FROM rendez-vous";
if (mysql_query($sql) == FALSE)
{
  $sql = "INSERT INTO `rendez_vous` (`rdv_cdn`, `rdv_util_no`, `rdv_tuteur_no`, `rdv_apprenant_no`, `rdv_creneau_nb`, `rdv_titre_lb`, `rdv_commentaire_cmt`, `rdv_date_dt`, `rdv_modecontact_lb`, `rdv_grp_no`) VALUES
       (1, 0, 1, 0, 6, '', 'libre', '2012-10-19', 'TELEPHONE', 0),
       (2, 0, 1, 0, 4, '', 'libre', '2013-05-23', 'RENCONTRE', 0);";
  req_insert($sql);
}
$sql = "CREATE TABLE IF NOT EXISTS `ressource_new` (
  `ress_cdn` smallint(5) unsigned NOT NULL auto_increment,
  `ress_cat_lb` varchar(255) NOT NULL default '',
  `ress_typress_no` smallint(5) unsigned NOT NULL default '0',
  `ress_url_lb` text NOT NULL,
  `ress_auteurs_cmt` longtext NOT NULL,
  `ress_publique_on` enum('OUI','NON') NOT NULL default 'OUI',
  `ress_titre` text NOT NULL,
  `ress_desc_cmt` text NOT NULL,
  `ress_create_dt` DATE DEFAULT '2007-01-01' NOT NULL ,
  `ress_modif_dt` DATE DEFAULT '2007-01-01' NOT NULL ,
  `ress_public_no` enum('TOUT','CS','BTSA','BTA','BAC PRO','BAC TECHNO','BP','BPA','BEPA','CAPA','FND') NOT NULL default 'TOUT',
  `ress_ajout` varchar(50) NOT NULL default '',
  `ress_type` enum('ACCOMPAGNEMENT','ACQUISITION','EVALUATION','INFORMATION','INITIATION','MULTIFONCTION','PERFECTIONNEMENT','POSITIONNEMENT','SENSIBILISATION','COURS','EXERCICE','ACTIVITES MULTIPLES','INFORMATION COMPLEMENTAIRE','APPLICATION/TP') NOT NULL default 'ACQUISITION',
  `ress_support` enum('Url','Web','Livre','BROCHURE','PERIODIQUE','Vidéo','AUDIO','DIAPOSITIVES/TRANSPARENTS','CD/DVD/Disquette','ANIMATION PEDAGOGIQUE','AUTRES') NOT NULL default 'Url',
  `ress_doublon` int(1) NOT NULL default '1',
  `ress_niveau` int(1) NOT NULL default '5',
  PRIMARY KEY  (`ress_cdn`)
) ENGINE=MyISAM AUTO_INCREMENT=8 ;";
req_insert($sql);
$sql = "SELECT all FROM ressource_new";
if (mysql_query($sql) == FALSE)
{
$sql = "INSERT INTO `ressource_new` (`ress_cdn`, `ress_cat_lb`, `ress_typress_no`, `ress_url_lb`, `ress_auteurs_cmt`, `ress_publique_on`, `ress_titre`, `ress_desc_cmt`, `ress_create_dt`, `ress_modif_dt`, `ress_public_no`, `ress_ajout`, `ress_type`, `ress_support`, `ress_doublon`, `ress_niveau`) VALUES
(179, 'Géographie', 12, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 1, 5),
(1, 'Agronomie/Phytotechnie', 0, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 1, 5),
(310, 'Santé animale', 212, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(308, 'Amélioration génétique animale', 212, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(2, 'Zootechnie', 0, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 1, 5),
(4, 'Horticulture/Espaces Verts', 0, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 1, 5),
(3, 'Viticulture/Viniculture', 0, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(5, 'Foresterie et Exploitation du Bois', 0, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 1, 5),
(233, 'Mécanique', 229, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(234, 'Electricité/Electronique', 229, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(229, 'Physique', 203, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(7, 'Machinisme et Génie Rural', 0, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 1, 5),
(307, 'Reproduction animale', 212, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(6, 'Aquaculture', 0, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 1, 5),
(8, 'Aménagement du territoire', 0, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 1, 5),
(231, 'Chimie', 203, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(232, 'Optique', 229, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(9, 'Industries Agroalimentaires', 0, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 1, 5),
(10, 'Arts et Cultures', 0, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 1, 5),
(12, 'Enseignement général', 0, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 1, 5),
(309, 'Croissance et développement', 212, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(11, 'Sciences économiques et sociales', 0, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 1, 5),
(228, 'Expression et projet', 222, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(177, 'Phytotechnie générale', 1, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 1, 5),
(13, 'Protection de l''Environnement', 0, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 1, 5),
(203, 'Sciences et techniques de base', 12, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 1, 5),
(230, 'Informatique', 203, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(19, 'Caprins', 213, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 1, 5),
(251, 'Taille', 177, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(252, 'Fertilisation', 177, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(253, 'Croissance des plantes cultivées', 177, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(254, 'Reproduction et multiplication', 177, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(20, 'Aviculture', 213, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 1, 5),
(246, 'Biotechnologies', 235, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(248, 'Irrigation', 177, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(249, 'Protection des cultures', 177, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(250, 'Travail du sol', 177, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(21, 'Mathématiques', 203, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 1, 5),
(188, 'Antiquité', 22, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 1, 5),
(189, 'Moyen Âge', 22, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 1, 5),
(190, 'Renaissance', 22, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 1, 5),
(191, 'Histoire contemporaine', 22, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 1, 5),
(192, 'Géographie physique', 179, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 1, 5),
(22, 'Histoire', 12, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 1, 5),
(216, 'Internet', 397, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(235, 'Biologie/Ecologie', 203, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(236, 'Chimie minérale', 231, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(237, 'Chimie organique', 231, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(238, 'Chimie générale', 231, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(239, 'Cinétique chimique', 231, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(240, 'Biologie générale', 235, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(241, 'Biologie végétale', 235, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(242, 'Biologie animale', 235, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(24, 'Langues', 12, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 1, 5),
(243, 'Génétique', 235, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(244, 'Le corps humain', 235, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(245, 'Ecologie', 235, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(25, 'Algèbre/Analyse', 21, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 1, 5),
(183, 'Allemand', 24, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 1, 5),
(184, 'Espagnol', 24, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 1, 5),
(185, 'Italien', 24, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 1, 5),
(186, 'Russe', 24, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 1, 5),
(187, 'Préhistoire', 22, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 1, 5),
(26, 'Géométrie', 21, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 1, 5),
(298, 'Fertilisation horticole', 217, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(299, 'Protection des cultures horticoles', 217, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(300, 'Reproduction et multiplication horticole', 217, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(180, 'Statistiques/Probabilités', 21, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 1, 5),
(181, 'Français (FLE)', 24, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 1, 5),
(182, 'Anglais', 24, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 1, 5),
(28, 'Ovins', 213, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 1, 5),
(255, 'Amélioration génétique des plantes', 177, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(256, 'Reproduction sexuée', 254, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(257, 'Greffe', 254, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(258, 'Semis', 254, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(259, 'Bouturage', 254, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(29, 'Trigonométrie', 25, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 1, 5),
(286, 'Autres cultures spécialisées', 268, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', '', '', '', 0, 0),
(294, 'Viticulture', 3, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(295, 'Travaux paysagers', 4, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(296, 'Morphologie végétale', 217, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(297, 'Taille horticole', 217, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(287, 'Maïs ensilage', 264, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(288, 'Luzerne', 264, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(289, 'Trèfle', 264, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(290, 'Prairies permanentes', 264, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(291, 'Autres prairies et productions fourragères', 264, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(292, 'Oenologie', 3, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(293, 'Economie du vin/spiritueux', 3, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(31, 'Productions légumières', 142, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 1, 5),
(305, 'Multiplication in vitro en horticulture', 300, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(306, 'Alimentation animale', 212, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(303, 'Semis horticole', 300, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(304, 'Bouturage en horticulture', 300, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(32, 'Arboriculture fruitière', 142, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 1, 5),
(193, 'Géographie humaine', 179, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 1, 5),
(301, 'Reproduction sexuée horticole', 300, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(302, 'Greffe en horticulture', 300, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(271, 'Maïs', 265, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(272, 'Triticale', 265, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(33, 'Calcul matriciel', 25, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'BEPA', 'foad', '', '', 1, 5),
(274, 'Autres céréales', 265, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(275, 'Pois', 266, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(276, 'Féverolle', 266, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(277, 'Lupin', 266, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(278, 'Autres protéagineux', 266, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(279, 'Colza', 267, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(280, 'Soja', 267, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(281, 'Lin oléagineux', 267, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(282, 'Tournesol', 267, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(283, 'Autres oléagineux', 267, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(284, 'Canne à sucre', 268, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(285, 'Betterave sucrière', 268, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(312, 'Porcins', 213, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(313, 'Equins', 213, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(314, 'Apiculture', 213, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(311, 'Anatomie', 212, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(142, 'Productions végétales', 1, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 1, 5),
(273, 'Avoine', 265, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(226, 'Expression écrite', 222, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(227, 'Expression orale', 222, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(135, 'Bovins', 213, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 1, 5),
(260, 'Multiplication in vitro', 254, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(261, 'Sélection génétique', 255, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(217, 'Horticulture', 4, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 1, 5),
(223, 'Orthographe', 220, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(224, 'Grammaire', 220, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(225, 'Conjugaison', 220, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(212, 'Zootechnie générale', 2, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 1, 5),
(213, 'Productions animales', 2, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 1, 5),
(262, 'OGM', 255, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(263, 'Grandes cultures', 142, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(264, 'Prairies/productions fourragères', 142, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(265, 'Céréales', 263, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(266, 'Protéaginaux', 263, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(267, 'Oléagineux', 263, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(268, 'Cultures spécialisées', 263, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(269, 'Blé', 265, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(270, 'Orge', 265, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(218, 'Expression et Communication', 12, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(219, 'Philosophie', 12, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(220, 'Règles de français', 218, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(221, 'Techniques documentaires', 218, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(222, 'Expression écrite et orale: Techniques', 218, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(14, 'Technologies/ Information /communication', 0, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 1, 5),
(16, 'Agricultures alternatives', 0, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 1, 5),
(176, 'Education physique et sportive', 12, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 1, 5),
(15, 'Citoyenneté', 0, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 1, 5),
(315, 'Cuniculture', 213, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(316, 'Animalerie', 213, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(317, 'Autres élevages', 213, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(318, 'Aqua. générale', 6, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(319, 'Aqua. eau douce', 6, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(320, 'Aqua. marine', 6, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(321, 'Sylviculture', 5, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(322, 'Transformation et filières', 5, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(323, 'Tourisme rural', 8, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(324, 'Dévelopt durable', 8, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(325, 'Services', 8, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(326, 'Gestion des territoires', 8, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(327, 'Dévelopt local', 8, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(328, 'Paysages', 8, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(329, 'Génie rural', 7, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(330, 'Mécanique générale', 7, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(331, 'Bât. et équipements', 7, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(332, 'Machinisme agricole', 7, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(333, 'Agri. de précision', 7, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(334, 'Télédétection et SIG', 7, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(335, 'Equipts industriels', 7, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(336, 'Sécurité', 7, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(337, 'Hydraulique', 329, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(338, 'Irrigation et équipements', 329, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(339, 'Drainage', 329, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(340, 'Hydrogéologie', 329, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(351, 'Normes/signes de qualité', 9, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(352, 'Comptabilité', 11, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(353, 'Finance', 11, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(354, 'Géologie', 235, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(347, 'Biochimie aliment.', 9, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(348, 'Process/équipts', 9, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(349, 'Produits alimentaires', 9, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(350, 'Sécurité alimentaire', 9, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(355, 'Microbiologie', 235, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(356, 'Biologie de la vigne', 294, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(357, 'Conduite de la vigne', 294, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(358, 'Taille de la vigne', 294, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(359, 'Maladies et traitements', 294, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(360, 'Ampélographie', 292, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(361, 'Vinification', 292, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(362, 'Réglementation', 292, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(363, 'Vignobles français', 293, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(364, 'Vignobles du monde', 293, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(365, 'Commercialisation du vin', 293, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(366, 'Risques physiques', 350, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(367, 'Risques chimiques', 350, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(368, 'Risques microbiologiques', 350, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(369, 'Méthode HACCP', 350, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(371, 'Comptabilité agricole', 352, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(372, 'Gestion', 11, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(373, 'Economie générale', 11, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(374, 'Sociologie', 11, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(375, 'Economie sociale et familiale', 11, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(376, 'Droit et législation', 11, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(377, 'Droit des sociétés', 376, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(378, 'Droit du travail', 376, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(379, 'Droit foncier', 376, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(380, 'Sociologie des organisations', 374, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(381, 'Biodiversité', 13, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(382, 'Agriculture et environnement', 13, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(383, 'Lois agricoles', 382, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(384, 'Pollutions agricoles', 382, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(385, 'CTE', 382, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(386, 'Gestion des paysages', 382, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(387, 'Photographie', 10, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(388, 'Cinéma/Audiovisuel', 10, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(389, 'Autres Arts/Cultures', 10, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(390, 'Arts plastiques', 10, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(391, 'Musique', 10, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(392, 'Traditions populaires', 10, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(393, 'Littérature', 10, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(394, 'Théâtre', 10, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(395, 'Bureautique', 14, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(396, 'Informatique et programmation', 14, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(397, 'Réseaux et communication', 14, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(398, 'Multimédia', 14, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(399, 'Agric. biologique', 16, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(400, 'Agric. durable', 16, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(402, 'Développement et Mondialisation', 15, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(403, 'Rapports Nord-Sud', 402, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(404, 'Coopération internationale', 402, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(405, 'Institutions', 447, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(410, 'Agrométéorologie', 1, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(413, 'Sciences du sol', 1, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(414, 'Pédologie', 413, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(415, 'Géomorphologie', 413, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(416, 'Taxonomie des sols', 414, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(417, 'Chimie du sol', 414, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(418, 'Physique des sols', 414, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(447, 'Démocratie et citoyenneté', 15, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(448, 'Administrations', 447, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(449, 'Droits et devoirs du citoyen', 447, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', 'TOUT', 'foad', '', '', 0, 0),
(23, 'Santé', 11, '', '', 'OUI', '', '', '2007-01-01', '2007-01-01', '', 'foad', '', '', 1, 5),
(649, 'conchyliculture', 6, '', '', 'OUI', '', 'TOUT', '2007-01-01', '2007-01-01', '', 'foad', '', '', 1, 5),
(650, 'conchyliculture', 6, '', '', 'OUI', '', 'TOUT', '2007-01-01', '2007-01-01', '', 'foad', '', '', 1, 5),
(651, 'pisciculture', 6, '', '', 'OUI', '', 'TOUT', '2007-01-01', '2007-01-01', '', 'foad', '', '', 1, 5),
(652, 'Autres Arts/Cultures', 10, 'qcm.php?code=1', 'Formagri', 'OUI', 'Quizz sur l''Italie', 'Quelques questions dans un Quizz provenant d&#39;une archive QTI 1.2', '2012-06-28', '2007-01-01', 'TOUT', 'super', 'ACCOMPAGNEMENT', 'Url', 1, 1),
(653, 'Ressources Multimedia', 0, '', '', 'OUI', '', '', '2012-06-28', '2007-01-01', 'TOUT', 'foad', 'ACQUISITION', 'Url', 1, 5),
(654, 'Liaison vers consignes-Média', 653, '', '', 'OUI', '', '', '2012-06-28', '2007-01-01', 'TOUT', 'foad', 'ACQUISITION', 'Url', 1, 5),
(655, 'Liaison vers consignes-Média', 653, 'ressources/super_1/ressources/Ressources_Media/video_28_06_2012.flv', 'Inconnu', 'NON', 'Video', 'Sans commentaire ou commentaire à venir', '2012-06-28', '2007-01-01', 'TOUT', 'super', 'ACTIVITES MULTIPLES', 'Url', 1, 1);";
req_insert($sql);
}


$sql = "CREATE TABLE IF NOT EXISTS `qcm_donnees` (
  `qcm_data_cdn` smallint(4) NOT NULL AUTO_INCREMENT,
  `n_lignes` tinyint(2) NOT NULL DEFAULT '0',
  `question` varchar(255) NOT NULL DEFAULT '',
  `qcmdata_auteur_no` smallint(5) NOT NULL DEFAULT '1',
  `img_blb` blob NOT NULL,
  `typ_img` varchar(40) NOT NULL DEFAULT '',
  `1_prop` varchar(255) NOT NULL DEFAULT '',
  `2_prop` varchar(255) NOT NULL DEFAULT '',
  `3_prop` varchar(255) NOT NULL DEFAULT '',
  `4_prop` varchar(255) NOT NULL DEFAULT '',
  `5_prop` varchar(255) NOT NULL DEFAULT '',
  `6_prop` varchar(255) NOT NULL DEFAULT '',
  `7_prop` varchar(255) NOT NULL DEFAULT '',
  `8_prop` varchar(255) NOT NULL DEFAULT '',
  `9_prop` varchar(255) NOT NULL DEFAULT '',
  `10_prop` varchar(255) NOT NULL DEFAULT '',
  `1_val` tinyint(2) NOT NULL DEFAULT '0',
  `2_val` tinyint(2) NOT NULL DEFAULT '0',
  `3_val` tinyint(2) NOT NULL DEFAULT '0',
  `4_val` tinyint(2) NOT NULL DEFAULT '0',
  `5_val` tinyint(2) NOT NULL DEFAULT '0',
  `6_val` tinyint(2) NOT NULL DEFAULT '0',
  `7_val` tinyint(2) NOT NULL DEFAULT '0',
  `8_val` tinyint(2) NOT NULL DEFAULT '0',
  `9_val` tinyint(2) NOT NULL DEFAULT '0',
  `10_val` tinyint(2) NOT NULL DEFAULT '0',
  `note` tinyint(2) NOT NULL DEFAULT '0',
  `multiple` tinyint(1) NOT NULL DEFAULT '0',
  `image` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`qcm_data_cdn`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";
req_insert($sql);
$sql = "SELECT all FROM qcm_donnees";
if (mysql_query($sql) == FALSE)
{
    $sql = "INSERT INTO `qcm_donnees` (`qcm_data_cdn`, `n_lignes`, `question`, `qcmdata_auteur_no`, `img_blb`, `typ_img`, `1_prop`, `2_prop`, `3_prop`, `4_prop`, `5_prop`, `6_prop`, `7_prop`, `8_prop`, `9_prop`, `10_prop`, `1_val`, `2_val`, `3_val`, `4_val`, `5_val`, `6_val`, `7_val`, `8_val`, `9_val`, `10_val`, `note`, `multiple`, `image`) VALUES
(1, 2, 'Vatican City is entirely surrounded by the city of Rome. True or False?', 1, '', 'image/jpg', 'True', 'False', '', '', '', '', '', '', '', '', 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0, 'qcm_images/192939979_2995069c27_o_1340873619.jpg'),
(2, 6, 'Which of the following countries border Italy?', 1, '', '', 'Germany', 'The Netherlands', 'Slovenia', 'France', 'Austria', 'Switzerland', '', '', '', '', 0, 0, 1, 1, 1, 1, 0, 0, 0, 0, 1, 1, 'non'),
(3, 4, 'Florence is often said to be the birthplace of', 1, '', 'image/jpg', 'Socialism', 'The Reformation', 'The Renaissance', 'Post Modernism', '', '', '', '', '', '', 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 1, 0, 'qcm_images/290769260_718258a53c_b_1340873619.jpg'),
(4, 5, 'The &amp;#39;boot&amp;#39; shape of Italy is an example of what geographical feature?', 1, '', '', 'Plateau', 'Continent', 'Peninsula', 'Ridge', 'Archipelago', '', '', '', '', '', 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 1, 0, 'non'),
(5, 4, 'What is the most popular religion in Italy?', 1, '', '', 'Islam', 'Judaism', 'Roman Catholicism', 'Jehovah Witness', '', '', '', '', '', '', 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 1, 0, 'non'),
(6, 6, 'Order these cities by population size - use the mouse to drag the cities to left hand side, putting the city with the largest population at the top.', 1, '', '', '', '', '', '', '', '', '', '', '', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0, 'non'),
(7, 4, 'Italy is usually considered to have an geographical economic split. Is this split...', 1, '', '', 'developed industrial east and an agricultural west.', 'developed industrial south and an agricultural north.', 'developed industrial west and an agricultural east.', 'developed industrial north and an agricultural south.', '', '', '', '', '', '', 0, 0, 0, 1, 0, 0, 0, 0, 0, 0, 1, 0, 'non');";
    req_insert($sql);
}else
   $avis=1;

$sql = "CREATE TABLE IF NOT EXISTS `qcm_linker` (
  `qcmlinker_cdn` int(8) NOT NULL AUTO_INCREMENT,
  `qcmlinker_param_no` int(5) NOT NULL,
  `qcmlinker_data_no` int(8) NOT NULL,
  `qcmlinker_number_no` tinyint(2) NOT NULL,
  PRIMARY KEY (`qcmlinker_cdn`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";

req_insert($sql);
$sql = "SELECT all FROM qcm_linker";
if (mysql_query($sql) == FALSE)
{
    $sql = "INSERT INTO `qcm_linker` (`qcmlinker_cdn`, `qcmlinker_param_no`, `qcmlinker_data_no`, `qcmlinker_number_no`) VALUES
(1, 1, 1, 1),
(2, 1, 2, 2),
(3, 1, 3, 3),
(4, 1, 4, 4),
(5, 1, 5, 5),
(6, 1, 6, 6),
(7, 1, 7, 7);";
    req_insert($sql);
}else
   $avis=1;

$sql = "CREATE TABLE IF NOT EXISTS `qcm_param` (
  `ordre` smallint(5) NOT NULL AUTO_INCREMENT,
  `qcm_auteur_no` smallint(5) NOT NULL DEFAULT '1',
  `n_pages` smallint(5) NOT NULL DEFAULT '0',
  `duree` int(5) NOT NULL DEFAULT '0',
  `mode` tinyint(1) NOT NULL DEFAULT '0',
  `titre_qcm` varchar(255) NOT NULL DEFAULT '',
  `moyenne` smallint(2) NOT NULL DEFAULT '10',
  PRIMARY KEY (`ordre`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";
req_insert($sql);
$sql = "SELECT all FROM qcm_param";
if (mysql_query($sql) == FALSE)
{
    $sql = "INSERT INTO `qcm_param` (`ordre`, `qcm_auteur_no`, `n_pages`, `duree`, `mode`, `titre_qcm`, `moyenne`) VALUES
         (1, 1, 7, 15, 0, 'A_Qti12_1340873619', 10);";
    req_insert($sql);
}else
   $avis=1;


$sql = "CREATE TABLE IF NOT EXISTS `rss` (
  `rss_cdn` smallint(5) NOT NULL auto_increment,
  `rss_type_lb` enum('module','sequence','activite') NOT NULL default 'module',
  `rss_id_no` smallint(5) NOT NULL default '0',
  `rss_auteur_no` smallint(8) NOT NULL default '0',
  `rss_date_lb` varchar(20) NOT NULL default '',
  `rss_action_lb` enum('Ajout','Modification') NOT NULL default 'Ajout',
  PRIMARY KEY  (`rss_cdn`)
) ENGINE=MyISAM COMMENT='Gestions des fils RSS' AUTO_INCREMENT=1 ;";
req_insert($sql);
$sql = "SELECT all FROM rss";
if (mysql_query($sql) == FALSE)
{
    $sql = "INSERT INTO `rss` (`rss_cdn`, `rss_type_lb`, `rss_id_no`, `rss_auteur_no`, `rss_date_lb`, `rss_action_lb`) VALUES
(1, 'module', 1, 1, '1340870702', 'Ajout'),
(2, 'sequence', 1, 1, '1340873305', 'Ajout'),
(3, 'sequence', 2, 1, '1340873403', 'Ajout'),
(4, 'sequence', 2, 1, '1340873490', 'Modification'),
(5, 'sequence', 3, 1, '1340873866', 'Ajout'),
(6, 'sequence', 3, 1, '1340874030', 'Modification'),
(7, 'activite', 1, 1, '1340874031', 'Ajout'),
(8, 'activite', 1, 1, '1340874064', 'Modification'),
(9, 'module', 1, 1, '1340874439', 'Modification');";
    req_insert($sql);
}else
   $avis=1;


$sql = "CREATE TABLE IF NOT EXISTS `scorm_interact` (
  `sci_cdn` int(8) NOT NULL auto_increment,
  `sci_num_lb` varchar(255) NOT NULL default '',
  `sci_ordre_no` int(3) NOT NULL default '0',
  `sci_user_no` int(8) NOT NULL default '0',
  `sci_mod_no` int(11) NOT NULL default '0',
  `sci_grp_no` smallint(4) NOT NULL default '0',
  `sci_time_lb` varchar(8) NOT NULL default '',
  `sci_type_lb` varchar(15) NOT NULL default '',
  `sci_pattern_cmt` text NOT NULL,
  `sci_poids_nb` mediumint(3) NOT NULL default '0',
  `sci_student_response_cmt` text NOT NULL,
  `sci_result_lb` varchar(15) NOT NULL default '',
  `sci_latency_lb` varchar(8) NOT NULL default '',
  `sci_objectives` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`sci_cdn`)
) ENGINE=MyISAM AUTO_INCREMENT=1 ;";
req_insert($sql);

$sql = "CREATE TABLE IF NOT EXISTS `scorm_module` (
  `mod_cdn` int(8) unsigned NOT NULL auto_increment,
  `mod_parc_no` int(11) NOT NULL default '-1',
  `mod_seq_no` int(8) unsigned NOT NULL default '0',
  `mod_titre_lb` varchar(255) NOT NULL default '',
  `mod_desc_cmt` text NOT NULL,
  `mod_consigne_cmt` text NOT NULL,
  `mod_motclef_lb` varchar(255) NOT NULL default '',
  `mod_visible` enum('TRUE','FALSE') NOT NULL default 'TRUE',
  `mod_duree_nb` smallint(4) NOT NULL default '0',
  `mod_niveau_no` tinyint(2) NOT NULL default '1',
  `mod_launch_lb` varchar(255) NOT NULL default '',
  `mod_numero_lb` varchar(255) NOT NULL default '',
  `mod_ordre_no` int(3) NOT NULL default '0',
  `mod_pere_lb` varchar(255) NOT NULL default '',
  `mod_pere_no` int(3) NOT NULL default '0',
  `mod_content_type_lb` enum('DOCUMENT','LABEL','SCORM','ASSET','AICC_HACP','AICC_API','AICC_LABEL') NOT NULL default 'SCORM',
  `mod_prereq_lb` varchar(255) NOT NULL default '',
  `mod_maxtimeallowed` varchar(13) NOT NULL default '',
  `mod_timelimitaction` enum('exit,message','exit,no message','continue,message','continue,no message') NOT NULL default 'continue,no message',
  `mod_datafromlms` varchar(255) NOT NULL default '',
  `mod_masteryscore` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`mod_cdn`)
) ENGINE=MyISAM PACK_KEYS=0 COMMENT='table des modules scorm' AUTO_INCREMENT=1 ;";
req_insert($sql);
$sql = "SELECT all FROM scorm_module";
if (mysql_query($sql) == FALSE)
{
    $sql = "INSERT INTO `scorm_module` (`mod_cdn`, `mod_parc_no`, `mod_seq_no`, `mod_titre_lb`, `mod_desc_cmt`, `mod_consigne_cmt`, `mod_motclef_lb`, `mod_visible`, `mod_duree_nb`, `mod_niveau_no`, `mod_launch_lb`, `mod_numero_lb`, `mod_ordre_no`, `mod_pere_lb`, `mod_pere_no`, `mod_content_type_lb`, `mod_prereq_lb`, `mod_maxtimeallowed`, `mod_timelimitaction`, `mod_datafromlms`, `mod_masteryscore`) VALUES
(2, 1, 1, 'References and Lesson Objective\n', 'Discusses general rules of operation for vessels on inland waters.  Topics discussed include: Look-out, Safe Speed, Collision, Channels, Traffic Separation.', '', 'Vessel, Any Visibility, Look-out, Safe Speed, Collision, Channels, Traffic Separation, Maritime Navigation, Vessel Conduct', 'TRUE', 5, 2, 'ressources/super_1/ressources/Ressources_Scorm/cours_scorm/Course01/Lesson01/sco01.htm', 'S100001', 2, 'B100', 1, 'SCORM', '', '', '', '', ''),
(1, 1, 1, 'Inland Rules of the Road (HTML Format)\n\n', 'The purpose of this lesson is to demonstrate the functionality and capability of the ADL SAMPLE LMS. The material of this lesson is of the U.S Coast Guard''s Rules of the Road in compliance with U.S. Regulations. This lesson will give the student a basic understanding of the Inland Rules of Navigation. These rules have been Coast Guard approved according to the instruction listed above and U.S. Law.', '', 'Inland Maritime Navigation, Navy, Vessel, Coastal Waterways, Maritime, Navigation, navigation aids', 'TRUE', 120, 1, '', 'B100', 1, 'B0', 0, 'LABEL', '', '', '', '', ''),
(3, 1, 1, 'Steering & Sailing Rules\n\n', 'The purpose of this lesson is to demonstrate the functionality and capability of the ADL SAMPLE LMS. The material of this lesson is of the U.S Coast Guard''s Rules of the Road in compliance with U.S. Regulations. This lesson will give the student a basic understanding of the Inland Rules of Navigation. These rules have been Coast Guard approved according to the instruction listed above and U.S. Law.', '', 'Inland Maritime Navigation, Navy, Vessel, Coastal Waterways, Maritime, Navigation, navigation aids', 'TRUE', 120, 2, '', 'B110', 3, 'B100', 1, 'LABEL', '', '', '', '', ''),
(4, 1, 1, 'Conduct of Vessels in any Condition of Visibility\n', 'Steering and Sailing Rules: Conduct of Vessels in any Condition of Visibility. General Inland Vessel Navigation Rules. Includes definitions, collision avoidance, channels, and traffic separation schemes.', '', 'Look-up, Safe Speed, Collision, Channels, Traffic Separation, Maritime Navigation, conduct of vessels in any condition of visibility', 'TRUE', 15, 3, 'ressources/super_1/ressources/Ressources_Scorm/cours_scorm/Course01/Lesson01/sco02.htm', 'S110001', 4, 'B110', 3, 'SCORM', 'S100001', '0000:30:00.00', '', '', ''),
(5, 1, 1, 'Conduct of Vessels in Sight of One Another\n', 'Steering and Sailing Rules: Conduct of Vessels in Sight of One Another. Multi-vessel interactions and movement patterns and rules.', '', 'Sailing, Overtaking, Head-on, Crossing, Maritime Navigation, steering and sailing rules, conduct of vessels in sight of one another, multi-vessel interactions, movement patterns and rules', 'TRUE', 12, 3, 'ressources/super_1/ressources/Ressources_Scorm/cours_scorm/Course01/Lesson01/sco03.htm', 'S110002', 5, 'B110', 3, 'SCORM', 'S110001', '', '', '', ''),
(6, 1, 1, 'Conduct of Vessels in Restricted Visibility\n', 'Steering and Sailing Rules: Conduct of Vessels in Restricted Visibility.', '', 'Restricted Visibility, Maritime Navigation, steering and sailing rules, conduct of vessels, restricted visibility', 'TRUE', 4, 3, 'ressources/super_1/ressources/Ressources_Scorm/cours_scorm/Course01/Lesson01/sco04.htm', 'S110003', 6, 'B110', 3, 'SCORM', 'S110002', '', '', '', ''),
(7, 1, 1, 'Lights & Shapes\n', 'Lights and Shapes. Includes application, definitions, visibility and placement of lights.', '', 'Vessel Lighting, navigation lights, navigation shapes', 'TRUE', 25, 2, 'ressources/super_1/ressources/Ressources_Scorm/cours_scorm/Course01/Lesson01/sco05.htm', 'S100002', 7, 'B100', 1, 'SCORM', 'B110', '', '', '', ''),
(8, 1, 1, 'Sound & Light Signals\n', 'Sound and Light Signals', '', 'Signals, Audiable Indications, Visual Indications, Lights, Wistles, Blasts, Flags, Inland Navigation, Navigation Signals', 'TRUE', 14, 2, 'ressources/super_1/ressources/Ressources_Scorm/cours_scorm/Course01/Lesson01/sco06.htm', 'S100003', 8, 'B100', 1, 'SCORM', 'S100002', '', '', '', ''),
(9, 1, 1, 'Exam\n', 'Lesson Exam', '', 'Exam, Quiz, Test, Maritime Navigation Examination', 'TRUE', 20, 2, 'ressources/super_1/ressources/Ressources_Scorm/cours_scorm/Course01/Lesson01/sco07.htm', 'S100004', 9, 'B100', 1, 'SCORM', 'S100003', '0001:00:00.00', '', '', '75'),
(10, 1, 2, 'Comparaison\n', 'Pas de description', '', '', 'TRUE', 0, 1, 'ressources/super_1/ressources/Ressources_Scorm/qcm2_du/comparaison/comparaison.html', 'ITEM-4F73DA28-0FBA-6479-0891-A9343A3906A0', 1, 'ORG-C4E0FBEB-9B57-C0F7-E638-F97306D9EFDA', 0, 'SCORM', '', '', '', '', ''),
(11, 1, 2, 'Déterminants\n', 'Pas de description', '', '', 'TRUE', 0, 1, 'ressources/super_1/ressources/Ressources_Scorm/qcm2_du/determinants/determinants.html', 'ITEM-8CA81259-8691-A456-A0B1-E85F28232566', 2, 'ORG-C4E0FBEB-9B57-C0F7-E638-F97306D9EFDA', 0, 'SCORM', '', '', '', '', ''),
(12, 1, 2, 'Infinitif\n', 'Pas de description', '', '', 'TRUE', 0, 1, 'ressources/super_1/ressources/Ressources_Scorm/qcm2_du/infinitif/infinitif.html', 'ITEM-277236B8-5643-0FB6-12B5-41DC6A9C6ED4', 3, 'ORG-C4E0FBEB-9B57-C0F7-E638-F97306D9EFDA', 0, 'SCORM', '', '', '', '', ''),
(13, 1, 2, 'Modalité 1\n', 'Pas de description', '', '', 'TRUE', 0, 1, 'ressources/super_1/ressources/Ressources_Scorm/qcm2_du/modalite_01/modalite_01.html', 'ITEM-A213A080-F54E-5B9C-3FE9-0153B4113B3E', 4, 'ORG-C4E0FBEB-9B57-C0F7-E638-F97306D9EFDA', 0, 'SCORM', '', '', '', '', ''),
(14, 1, 2, 'Modalité 2\n', 'Pas de description', '', '', 'TRUE', 0, 1, 'ressources/super_1/ressources/Ressources_Scorm/qcm2_du/modalite_02/modalite_02.html', 'ITEM-D0F427A3-5A52-6634-C548-78967A53DAD3', 5, 'ORG-C4E0FBEB-9B57-C0F7-E638-F97306D9EFDA', 0, 'SCORM', '', '', '', '', ''),
(15, 1, 2, 'Passé-Présent\n', 'Pas de description', '', '', 'TRUE', 0, 1, 'ressources/super_1/ressources/Ressources_Scorm/qcm2_du/passe_present_01/passe_present_01.html', 'ITEM-5780B2D5-53A4-534E-6917-217B015E2853', 6, 'ORG-C4E0FBEB-9B57-C0F7-E638-F97306D9EFDA', 0, 'SCORM', '', '', '', '', ''),
(16, 1, 2, 'Passé-Présent 2\n', 'Pas de description', '', '', 'TRUE', 0, 1, 'ressources/super_1/ressources/Ressources_Scorm/qcm2_du/passe_present_02/passe_present_02.html', 'ITEM-D6E570DE-C088-5D11-50D1-A8A5FE54AC07', 7, 'ORG-C4E0FBEB-9B57-C0F7-E638-F97306D9EFDA', 0, 'SCORM', '', '', '', '', ''),
(17, 1, 2, 'Passé-Présent 3\n', 'Pas de description', '', '', 'TRUE', 0, 1, 'ressources/super_1/ressources/Ressources_Scorm/qcm2_du/passe_present_03/passe_present_03.html', 'ITEM-6C5AFC4E-AF35-0C6D-6792-3E6B15509A7E', 8, 'ORG-C4E0FBEB-9B57-C0F7-E638-F97306D9EFDA', 0, 'SCORM', '', '', '', '', ''),
(18, 1, 2, 'Passé-Présent 4\n', 'Pas de description', '', '', 'TRUE', 0, 1, 'ressources/super_1/ressources/Ressources_Scorm/qcm2_du/passe_present_04/passe_present_04.html', 'ITEM-2DE97F0D-556D-C1D3-4C70-3AE76F668BB5', 9, 'ORG-C4E0FBEB-9B57-C0F7-E638-F97306D9EFDA', 0, 'SCORM', '', '', '', '', ''),
(19, 1, 2, 'Passif 1\n', 'Pas de description', '', '', 'TRUE', 0, 1, 'ressources/super_1/ressources/Ressources_Scorm/qcm2_du/passif_01/passif_01.html', 'ITEM-06BE3161-CF32-8D46-B497-07649FFBA294', 10, 'ORG-C4E0FBEB-9B57-C0F7-E638-F97306D9EFDA', 0, 'SCORM', '', '', '', '', ''),
(20, 1, 2, 'Passif 2\n', 'Pas de description', '', '', 'TRUE', 0, 1, 'ressources/super_1/ressources/Ressources_Scorm/qcm2_du/passif_02/passif_02.html', 'ITEM-5477B34D-9087-0B0D-096F-B073373CBF28', 11, 'ORG-C4E0FBEB-9B57-C0F7-E638-F97306D9EFDA', 0, 'SCORM', '', '', '', '', ''),
(21, 1, 2, 'Propositions relatives\n', 'Pas de description', '', '', 'TRUE', 0, 1, 'ressources/super_1/ressources/Ressources_Scorm/qcm2_du/prop_relatives/prop_relatives.html', 'ITEM-CA7A12F3-8D3A-C875-AD6C-50276365848F', 12, 'ORG-C4E0FBEB-9B57-C0F7-E638-F97306D9EFDA', 0, 'SCORM', '', '', '', '', ''),
(22, 1, 2, 'Quantificateurs 1\n', 'Pas de description', '', '', 'TRUE', 0, 1, 'ressources/super_1/ressources/Ressources_Scorm/qcm2_du/quantificateurs_01/quantificateurs_01.html', 'ITEM-7E6D5888-F872-FE38-DFD7-306CDD8E9865', 13, 'ORG-C4E0FBEB-9B57-C0F7-E638-F97306D9EFDA', 0, 'SCORM', '', '', '', '', ''),
(23, 1, 2, 'Quantificateurs 2\n', 'Pas de description', '', '', 'TRUE', 0, 1, 'ressources/super_1/ressources/Ressources_Scorm/qcm2_du/quantificateurs_02/quantificateurs_02.html', 'ITEM-06BBD730-57A4-49C6-E8E9-151307C86C22', 14, 'ORG-C4E0FBEB-9B57-C0F7-E638-F97306D9EFDA', 0, 'SCORM', '', '', '', '', ''),
(24, 1, 2, 'Subordonnées circonstancielles\n', 'Pas de description', '', '', 'TRUE', 0, 1, 'ressources/super_1/ressources/Ressources_Scorm/qcm2_du/sub_circonst/sub_circonst.html', 'ITEM-CD6917BD-7B89-DE82-7E9F-20D9E8970178', 15, 'ORG-C4E0FBEB-9B57-C0F7-E638-F97306D9EFDA', 0, 'SCORM', '', '', '', '', ''),
(25, 1, 2, 'Vocabulaire 1\n', 'Pas de description', '', '', 'TRUE', 0, 1, 'ressources/super_1/ressources/Ressources_Scorm/qcm2_du/vocabulaire_01/vocabulaire_01.html', 'ITEM-96EDC549-1968-E790-1055-A2A8FC4389E7', 16, 'ORG-C4E0FBEB-9B57-C0F7-E638-F97306D9EFDA', 0, 'SCORM', '', '', '', '', ''),
(26, 1, 2, 'Vocabulaire 2\n', 'Pas de description', '', '', 'TRUE', 0, 1, 'ressources/super_1/ressources/Ressources_Scorm/qcm2_du/vocabulaire_02/vocabulaire_02.html', 'ITEM-F7F09151-87CD-31B8-83DB-3FB4D0B6E3CA', 17, 'ORG-C4E0FBEB-9B57-C0F7-E638-F97306D9EFDA', 0, 'SCORM', '', '', '', '', '');";
    req_insert($sql);
}else
   $avis=1;

$sql = "CREATE TABLE IF NOT EXISTS `scorm_objectives` (
  `scob_cdn` int(8) NOT NULL auto_increment,
  `scob_num_lb` varchar(255) NOT NULL default '',
  `scob_ordre_no` int(3) NOT NULL default '0',
  `scob_user_no` int(8) NOT NULL default '0',
  `scob_mod_no` int(11) NOT NULL default '0',
  `scob_grp_no` smallint(4) NOT NULL default '0',
  `scob_scaled` float NOT NULL default '0',
  `scob_min` float NOT NULL default '0',
  `scob_max` float NOT NULL default '0',
  `scob_raw` float NOT NULL default '0',
  `scob_status` enum('NOT ATTEMPTED','BROWSED','COMPLETED','INCOMPLETE','PASSED','FAILED') NOT NULL default 'NOT ATTEMPTED',
  `scob_success` enum('UNKNOWN','PASSED','FAILED') NOT NULL default 'UNKNOWN',
  `scob_completion` enum('UNKNOWN','NOT ATTEMPTED','COMPLETED','INCOMPLETE') NOT NULL default 'UNKNOWN',
  UNIQUE KEY `scob_cdn` (`scob_cdn`)
) ENGINE=MyISAM AUTO_INCREMENT=1 ;";
req_insert($sql);


$sql = "CREATE TABLE IF NOT EXISTS `sequence` (
  `seq_cdn` smallint(5) unsigned NOT NULL auto_increment,
  `seq_titre_lb` varchar(255) NOT NULL default '',
  `seq_desc_cmt` longtext,
  `seq_mots_clef` varchar(255) NOT NULL default '',
  `seq_ordreact_on` enum('OUI','NON') NOT NULL default 'NON',
  `seq_duree_nb` smallint(5) unsigned NOT NULL default '0',
  `seq_auteur_no` smallint(5) unsigned NOT NULL default '0',
  `seq_create_dt` date NOT NULL default '0000-00-00',
  `seq_modif_dt` date NOT NULL default '0000-00-00',
  `seq_publique_on` tinyint(1) NOT NULL default '1',
  `seq_type_on` tinyint(1) NOT NULL default '0',
  `seq_type_lb` enum('NORMAL','SCORM 1.2','SCORM 2004','SCORM AICC') NOT NULL default 'NORMAL',
  PRIMARY KEY  (`seq_cdn`)
) ENGINE=MyISAM AUTO_INCREMENT=1 ;";
req_insert($sql);
$sql = "SELECT all FROM sequence";
if (mysql_query($sql) == FALSE)
{
    $sql = "INSERT INTO `sequence` (`seq_cdn`, `seq_titre_lb`, `seq_desc_cmt`, `seq_mots_clef`, `seq_ordreact_on`, `seq_duree_nb`, `seq_auteur_no`, `seq_create_dt`, `seq_modif_dt`, `seq_publique_on`, `seq_type_on`, `seq_type_lb`) VALUES
(1, 'Maritime Navigation', 'Basic instruction on U.S. Coast Guard and U.S. Regulation of Inland Vessel Rules of NavigationORG:ADL Co-Lab', 'Vessel, Inland Navigation, Coast Guard, Maritime, Navigation, Inland Navigation, High School', 'OUI', 335, 1, '2000-01-28', '2012-06-28', 1, 0, 'SCORM 1.2'),
(2, 'QCM - Anglais médical', 'Questionnaire à trous pour vérifier un minimum de maîtrise d&#39;anglais médical', 'aucun', 'OUI', 0, 1, '2012-06-28', '2012-06-28', 1, 0, 'SCORM 1.2'),
(3, 'Séquence non scormée', 'Pour tester une activité de type Quizz importée depuis une archive QTI 1.2', '', 'NON', 3, 1, '2012-06-28', '2012-06-28', 1, 0, 'NORMAL');";
    req_insert($sql);
}else
   $avis=1;


$sql = "CREATE TABLE IF NOT EXISTS `sequence_parcours` (
  `seqparc_cdn` smallint(5) unsigned NOT NULL auto_increment,
  `seqparc_seq_no` smallint(5) unsigned NOT NULL default '0',
  `seqparc_parc_no` smallint(5) unsigned NOT NULL default '0',
  `seqparc_ordre_no` smallint(2) NOT NULL default '1',
  PRIMARY KEY  (`seqparc_cdn`)
) ENGINE=MyISAM AUTO_INCREMENT=1 ;";
req_insert($sql);
$sql = "SELECT all FROM sequence_parcours";
if (mysql_query($sql) == FALSE)
{
    $sql = "INSERT INTO `sequence_parcours` (`seqparc_cdn`, `seqparc_seq_no`, `seqparc_parc_no`, `seqparc_ordre_no`) VALUES
(1, 1, 1, 1),
(2, 2, 1, 2),
(3, 3, 1, 3);";
    req_insert($sql);
}else
   $avis=1;


$sql = "CREATE TABLE IF NOT EXISTS `sequence_referentiel` (
  `seqref_cdn` smallint(5) unsigned NOT NULL auto_increment,
  `seqref_seq_no` smallint(5) unsigned NOT NULL default '0',
  `seqref_referentiel_no` smallint(5) unsigned NOT NULL default '0',
  PRIMARY KEY  (`seqref_cdn`)
) ENGINE=MyISAM AUTO_INCREMENT=1 ;";
req_insert($sql);
$sql = "SELECT all FROM sequence_referentiel";
if (mysql_query($sql) == FALSE)
{
    $sql = "INSERT INTO `sequence_referentiel` (`seqref_cdn`, `seqref_seq_no`, `seqref_referentiel_no`) VALUES
(1, 1, 0),
(2, 2, 0),
(3, 3, 0);";
    req_insert($sql);
}else
   $avis=1;


$sql = "CREATE TABLE IF NOT EXISTS `serveur_ressource` (
  `serveur_cdn` mediumint(5) NOT NULL auto_increment,
  `serveur_nomip_lb` varchar(255) NOT NULL default '',
  `serveur_param_lb` varchar(255) NOT NULL default '',
  `serveur_label_lb` varchar(255) NOT NULL default '',
  KEY `serveur_cdn` (`serveur_cdn`)
) ENGINE=MyISAM COMMENT='serveurs de ressources' AUTO_INCREMENT=1 ;";
req_insert($sql);

$sql = "CREATE TABLE IF NOT EXISTS `trace` (
  `trace_cdn` int(8) NOT NULL default '0',
  `trace_login_lb` varchar(255) NOT NULL default '',
  `trace_lien_lb` text NOT NULL,
  `trace_date_dt` date NOT NULL default '0000-00-00',
  `trace_heure_dt` time NOT NULL default '00:00:00',
  PRIMARY KEY  (`trace_cdn`)
) ENGINE=MyISAM;";
req_insert($sql);


$sql = "CREATE TABLE IF NOT EXISTS `traceur` (
  `traceur_cdn` mediumint(8) NOT NULL auto_increment,
  `traceur_util_no` smallint(5) NOT NULL default '0',
  `traceur_grp_no` smallint(3) NOT NULL default '0',
  `traceur_der_gest1` varchar(255) NOT NULL default '',
  `traceur_der_details` varchar(255) NOT NULL default '',
  `traceur_date_dt` date NOT NULL default '0000-00-00',
  `traceur_menu_volant` tinyint(1) NOT NULL default '-1',
  PRIMARY KEY  (`traceur_cdn`)
) ENGINE=MyISAM COMMENT='Paramètres de traçage individuel' AUTO_INCREMENT=1 ;";
req_insert($sql);


$sql = "CREATE TABLE IF NOT EXISTS `traque` (
  `traq_cdn` int(8) NOT NULL auto_increment,
  `traq_util_no` mediumint(5) NOT NULL default '0',
  `traq_act_no` mediumint(5) NOT NULL default '0',
  `traq_mod_no` mediumint(5) NOT NULL default '0',
  `traq_grp_no` smallint(5) NOT NULL default '-1',
  `traq_date_dt` date NOT NULL default '0000-00-00',
  `traq_hd_dt` time NOT NULL default '00:00:00',
  `traq_hf_dt` time NOT NULL default '00:00:00',
  PRIMARY KEY  (`traq_cdn`)
) ENGINE=MyISAM PACK_KEYS=0 COMMENT='Table de trackink des activités' AUTO_INCREMENT=1 ;";
req_insert($sql);


$sql = "CREATE TABLE IF NOT EXISTS `tuteur` (
  `tut_cdn` smallint(5) unsigned NOT NULL auto_increment,
  `tut_apprenant_no` smallint(5) unsigned NOT NULL default '0',
  `tut_tuteur_no` smallint(5) unsigned NOT NULL default '0',
  PRIMARY KEY  (`tut_cdn`)
) ENGINE=MyISAM AUTO_INCREMENT=1 ;";
req_insert($sql);

$sql = "INSERT INTO `tuteur` (`tut_cdn`, `tut_apprenant_no`, `tut_tuteur_no`) VALUES
       (1, 3, 1),(2, 4, 9),(3, 3, 9),(4, 4, 13),(5, 6, 1),(6, 6, 12),(7, 3, 12),(8, 6, 9);";
req_insert($sql);

$sql = "CREATE TABLE IF NOT EXISTS `user_centre` (
  `uc_cdn` int(11) NOT NULL auto_increment,
  `uc_iduser_no` int(11) NOT NULL default '0',
  `uc_centre_lb` varchar(255) NOT NULL default '',
  KEY `uc_cdn` (`uc_cdn`)
) ENGINE=MyISAM COMMENT='Gestion des provenances' AUTO_INCREMENT=1 ;";
req_insert($sql);


$sql = "CREATE TABLE IF NOT EXISTS `users` (
  `util_cdn` int(8) unsigned NOT NULL auto_increment,
  `util_nom_lb` varchar(255) NOT NULL default '',
  `util_prenom_lb` varchar(255) NOT NULL default '',
  `util_photo_lb` varchar(100) default NULL,
  `util_email_lb` varchar(255) NOT NULL default '',
  `util_tel_lb` varchar(20) default NULL,
  `util_webmail_lb` varchar(255) NOT NULL default '',
  `util_typutil_lb` enum('APPRENANT','FORMATEUR_REFERENT','RESPONSABLE_FORMATION','TUTEUR','ADMINISTRATEUR') NOT NULL default 'APPRENANT',
  `util_login_lb` varchar(50) NOT NULL default '',
  `util_motpasse_lb` varchar(20) NOT NULL default '',
  `util_flag` tinyint(1) NOT NULL default '0',
  `util_blocageutilisateur_on` enum('OUI','NON') NOT NULL default 'NON',
  `util_commentaire_cmt` longtext,
  `util_auteur_no` smallint(5) NOT NULL default '1',
  PRIMARY KEY  (`util_cdn`)
) ENGINE=MyISAM AUTO_INCREMENT=1 ;";
req_insert($sql);


$sql = "CREATE TABLE IF NOT EXISTS `utilisateur` (
  `util_cdn` int(8) unsigned NOT NULL auto_increment,
  `util_nom_lb` varchar(255) NOT NULL default '',
  `util_prenom_lb` varchar(255) NOT NULL default '',
  `util_photo_lb` varchar(100) default NULL,
  `util_email_lb` varchar(255) NOT NULL default '',
  `util_tel_lb` varchar(20) default NULL,
  `util_urlmail_lb` varchar(70) default NULL,
  `util_typutil_lb` enum('APPRENANT','FORMATEUR_REFERENT','RESPONSABLE_FORMATION','TUTEUR','ADMINISTRATEUR') NOT NULL default 'APPRENANT',
  `util_login_lb` varchar(50) NOT NULL default '',
  `util_motpasse_lb` varchar(20) NOT NULL default '',
  `util_logincas_lb` varchar(255) NOT NULL default '',
  `util_flag` tinyint(1) NOT NULL default '0',
  `util_blocageutilisateur_on` enum('OUI','NON') NOT NULL default 'NON',
  `util_commentaire_cmt` longtext,
  `util_auteur_no` smallint(5) NOT NULL default '1',
  `util_date_dt` DATETIME DEFAULT '2007-01-01 00:00:01' NOT NULL,
   PRIMARY KEY  (`util_cdn`)
) ENGINE=MyISAM PACK_KEYS=0 AUTO_INCREMENT=1 ;";
req_insert($sql);

$sql = "SELECT all FROM utilisateur";
if (mysql_query($sql) == FALSE)
{
   $sql = "INSERT INTO `utilisateur` (`util_cdn`, `util_nom_lb`, `util_prenom_lb`, `util_photo_lb`, `util_email_lb`, `util_tel_lb`, `util_urlmail_lb`, `util_typutil_lb`, `util_login_lb`, `util_motpasse_lb`, `util_logincas_lb`, `util_flag`, `util_blocageutilisateur_on`, `util_commentaire_cmt`, `util_auteur_no`, `util_date_dt`) VALUES
   (1, 'Administrateur', 'Formagri', '', 'moi@chez_untel.fr', '', '', 'ADMINISTRATEUR', 'super', 'super', '', 0, 'NON', '', -1, '2008-01-01 00:00:01'),
   (2, 'Apprenant', '2', NULL, 'apprenant2@machine.fr', '', '', 'APPRENANT', 'apprenant2', 'apprenant2', '\r\n', 0, 'NON', NULL, 1, '2012-06-28 09:51:59'),
   (3, 'Apprenant', '1', NULL, 'apprenant1@machine.fr', '', '', 'APPRENANT', 'apprenant1', 'apprenant1', '\r\n', 0, 'NON', NULL, 1, '2012-06-28 09:51:59'),
   (4, 'Dix', 'Jade', NULL, 'accio@machine.com', '', '', 'APPRENANT', 'demo3', 'demo3', '\r\n', 0, 'NON', NULL, 1, '2012-06-28 09:51:59'),
   (5, 'Stagiaire', 'Deux', NULL, 'lyes@chahoo.fr', '', '', 'APPRENANT', 'demo4', 'demo4', '\r\n', 0, 'NON', NULL, 1, '2012-06-28 09:51:59'),
   (6, 'Tonnerre', 'Debrest', NULL, 'tonnerre@festif.fr', '', '', 'APPRENANT', 'demo2', 'demo2', '', 0, 'NON', NULL, 1, '2012-06-28 09:51:59'),
   (7, 'Aimar', 'Jean', NULL, 'jaimar@free.fr', '', '', 'RESPONSABLE_FORMATION', 'jaimar', 'jaimar', '\r\n', 0, 'NON', NULL, 1, '2012-06-28 09:55:09'),
   (8, 'test', 'responsable', NULL, 'responsable@machine.fr', '', '', 'RESPONSABLE_FORMATION', 'responsable', 'responsable', '', 0, 'NON', NULL, 1, '2012-06-28 09:55:09'),
   (9, 'Forme', 'Hateur', NULL, 'f.hateur@feer.fr', '', '', 'FORMATEUR_REFERENT', 'f.hateur', 'forme', '\r\n', 0, 'NON', NULL, 1, '2012-06-28 09:59:09'),
   (10, 'FR', 'FR', NULL, 'formateur@courge.fr', '', '', 'FORMATEUR_REFERENT', 'formateur', 'formateur', '\r\n', 0, 'NON', NULL, 1, '2012-06-28 09:59:09'),
   (11, 'Mondey', 'FR', NULL, 'dey_fr@courge.com', '', '', 'FORMATEUR_REFERENT', 'mondey', 'mondey', '', 0, 'NON', NULL, 1, '2012-06-28 09:59:09'),
   (12, 'Obe', 'Servateur', NULL, 'o.servateur@free.fr', '', '', 'TUTEUR', 'o.servateur', 'obs', '\r\n', 0, 'NON', NULL, 1, '2012-06-28 10:01:33'),
   (13, 'TUTEUR', 'Test', NULL, 'test.tuteur@machine.fr', '', '', 'TUTEUR', 'test.tuteur', 'tuteur', '', 0, 'NON', NULL, 1, '2012-06-28 10:01:33');";
  req_insert($sql);
}else
   $avis=1;

$sql = "CREATE TABLE IF NOT EXISTS `utilisateur_groupe` (
  `utilgr_cdn` smallint(5) unsigned NOT NULL auto_increment,
  `utilgr_utilisateur_no` smallint(5) unsigned NOT NULL default '0',
  `utilgr_groupe_no` smallint(5) unsigned NOT NULL default '0',
  PRIMARY KEY  (`utilgr_cdn`)
) ENGINE=MyISAM AUTO_INCREMENT=1 ;";
req_insert($sql);
$sql = "SELECT all FROM utilisateur_groupe";
if (mysql_query($sql) == FALSE)
{
   $sql = "INSERT INTO `utilisateur_groupe` (`utilgr_cdn`, `utilgr_utilisateur_no`, `utilgr_groupe_no`) VALUES
        (1, 3, 1),
        (2, 4, 1),
        (3, 6, 1);";
   req_insert($sql);
}else
  $avis=1;

$sql = "CREATE TABLE IF NOT EXISTS `causer` (
  `causer_cdn` smallint(5) NOT NULL default '0',
  `causer_origin_no` smallint(5) NOT NULL default '0',
  `causer_dest_no` smallint(5) NOT NULL default '0',
  `causer_mess_cmt` text NOT NULL,
  KEY `causer_cdn` (`causer_cdn`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;";
req_insert($sql);

$sql = "CREATE TABLE IF NOT EXISTS `favoris` (
  `fav_cdn` smallint(5) NOT NULL default '0',
  `fav_utilisateur_no` smallint(5) NOT NULL default '0',
  `fav_url_lb` varchar(255) collate latin1_general_ci default NULL,
  `fav_seq_no` smallint(3) NOT NULL default '0',
  `fav_titre_lb` varchar(255) collate latin1_general_ci default NULL,
  `fav_public_on` enum('PERSONNEL','TOUS','GROUPE') collate latin1_general_ci default NULL,
  `fav_desc_lb` text collate latin1_general_ci,
  PRIMARY KEY  (`fav_cdn`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;";
req_insert($sql);

$sql = "CREATE TABLE IF NOT EXISTS `message` (
  `msg_cdn` int(5) NOT NULL auto_increment,
  `msg_contenu_cmt` longtext collate latin1_general_ci NOT NULL,
  `msg_auteur_no` int(11) NOT NULL default '0',
  `msg_groupe_no` int(3) NOT NULL default '0',
  `msg_apprenant_no` int(4) NOT NULL default '0',
  `msg_tous_on` tinyint(1) NOT NULL default '0',
  `msg_dhdeb_dt` date NOT NULL default '0000-00-00',
  `msg_dhfin_dt` date NOT NULL default '0000-00-00',
  KEY `msg_cdn` (`msg_cdn`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci COMMENT='Messages alerte' AUTO_INCREMENT=1 ;";
req_insert($sql);

$sql = "CREATE TABLE IF NOT EXISTS `forums_modules` (
  `fm_cdn` int(8) NOT NULL auto_increment,
  `fm_module_no` smallint(5) NOT NULL,
  `fm_parent_no` int(8) NOT NULL,
  `fm_sujet_lb` varchar(255) NOT NULL,
  `fm_auteur_no` smallint(5) NOT NULL,
  `fm_visible_on` tinyint(1) NOT NULL default '1',
  `fm_body_lb` text NOT NULL,
  `fm_datetime_dt` datetime NOT NULL default '2009-01-01 00:00:00',
  PRIMARY KEY  (`fm_cdn`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";
req_insert($sql);
$sql = "SELECT all FROM forums_modules";
if (mysql_query($sql) == FALSE)
{
   $sql = "INSERT INTO `forums_modules` (`fm_cdn`, `fm_module_no`, `fm_parent_no`, `fm_sujet_lb`, `fm_auteur_no`, `fm_visible_on`, `fm_body_lb`, `fm_datetime_dt`) VALUES
     (1, 1, 0, 'Bienvenue sur le forum de votre module Scorm', 1, 1, 'Ce forum vous permettra d&#39;&eacute;changer avec les stagiaires de votre formation mais aussi ceux des autres formations qui suivent ce module', '2012-06-28 11:24:04');";
   req_insert($sql);
}else
   $avis=1;

$sql = "CREATE TABLE IF NOT EXISTS `parcours_forums` (
  `parcforum_cdn` smallint(5) NOT NULL auto_increment,
  `parcforum_parc_no` smallint(5) NOT NULL,
  `parcforum_create_dt` date NOT NULL default '0000-00-00',
  `parcforum_modif_dt` date NOT NULL default '0000-00-00',
  PRIMARY KEY  (`parcforum_cdn`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COMMENT='Table de jointure module-forums' AUTO_INCREMENT=1 ;";
req_insert($sql);
$sql = "SELECT all FROM parcours_forums";
if (mysql_query($sql) == FALSE)
{
   $sql = "INSERT INTO `parcours_forums` (`parcforum_cdn`, `parcforum_parc_no`, `parcforum_create_dt`, `parcforum_modif_dt`) VALUES
        (1, 1, '2012-06-28', '0000-00-00');";
   req_insert($sql);
}else
   $avis=1;

$sql = "CREATE TABLE IF NOT EXISTS `stars` (
  `star_cdn` int(11) NOT NULL auto_increment,
  `star_user_id` int(11) NOT NULL,
  `star_item_id` int(11) NOT NULL,
  `star_type_no` tinyint(1) NOT NULL,
  KEY `star_id` (`star_cdn`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COMMENT='Table des contenus favoris' AUTO_INCREMENT=1 ;";
req_insert($sql);

$sql = "CREATE TABLE IF NOT EXISTS `user_config` (
  `ucfg_cdn` int(4) NOT NULL auto_increment,
  `ucfg_user_no` int(8) NOT NULL default '0',
  `ucfg_affgrp_on` tinyint(1) NOT NULL default '0',
  `ucfg_affapp_on` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`ucfg_cdn`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Table de config' AUTO_INCREMENT=1 ;";
req_insert($sql);

$sql = "SELECT all FROM user_config";
if (mysql_query($sql) == FALSE)
{
    $sql = "INSERT INTO `user_config` (`ucfg_cdn`, `ucfg_user_no`, `ucfg_affgrp_on`, `ucfg_affapp_on`) VALUES
         (1, 1, 0, 0);";
    req_insert($sql);
}else
   $avis=1;


$sql = "CREATE TABLE IF NOT EXISTS  `actdev_icone` (
  `actdevico_cdn` tinyint(1) NOT NULL auto_increment,
  `actdevico_type_lb` enum('Pas de devoir','Autocorrectif','Correction','A renvoyer') NOT NULL default 'Pas de devoir',
  `actdevico_style_lb` varchar(255) default NULL,
  PRIMARY KEY  (`actdevico_cdn`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='lien devoir icone' AUTO_INCREMENT=1 ;";

req_insert($sql);

$sql = "INSERT INTO `actdev_icone` (`actdevico_cdn`, `actdevico_type_lb`, `actdevico_style_lb`) VALUES
(1, 'Pas de devoir', ' class=''pasdedevoir'''),
(2, 'Autocorrectif', ' class=''autocorrectif'''),
(3, 'Correction', ' class=''correction'''),
(4, 'A renvoyer', ' class=''arenvoyer''');";
req_insert($sql);

$sql = "CREATE TABLE IF NOT EXISTS  `activite_devoir` (
  `actdev_cdn` int(8) NOT NULL auto_increment,
  `actdev_act_no` int(8) NOT NULL default '0',
  `actdev_dev_lb` enum('Pas de devoir','Autocorrectif','Correction','A renvoyer') NOT NULL default 'Pas de devoir',
  PRIMARY KEY  (`actdev_cdn`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Lien activite devoir' AUTO_INCREMENT=1 ;";
req_insert($sql);

$sql = "SELECT all FROM activite_devoir";
if (mysql_query($sql) == FALSE)
{
   $sql = "INSERT INTO `activite_devoir` (`actdev_cdn`, `actdev_act_no`, `actdev_dev_lb`) VALUES
    (1, 1, 'Autocorrectif');";
   req_insert($sql);
}else
   $avis=1;



$sql = "CREATE TABLE IF NOT EXISTS  `activite_media` (
  `actmedia_cdn` int(8) NOT NULL auto_increment,
  `actmedia_act_no` int(8) NOT NULL default '0',
  `actmedia_ress_no` int(8) NOT NULL default '0',
  PRIMARY KEY  (`actmedia_cdn`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";

req_insert($sql);
$sql = "SELECT all FROM activite_media";
if (mysql_query($sql) == FALSE)
{
   $sql = "INSERT INTO `activite_media` (`actmedia_cdn`, `actmedia_act_no`, `actmedia_ress_no`) VALUES
     (1, 1, 655);";
   req_insert($sql);
}else
   $avis=1;

$sql = "CREATE TABLE IF NOT EXISTS  `tracking` (
  `tracking_cdn` int(8) NOT NULL auto_increment,
  `tracking_util_no` int(8) NOT NULL default '0',
  `tracking_who_lb` varchar(255) NOT NULL default '',
  `tracking_when_dt` datetime NOT NULL default '0000-00-00 00:00:00',
  `tracking_file_lb` varchar(255) NOT NULL default '',
  `tracking_post_cmt` text,
  PRIMARY KEY  (`tracking_cdn`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Table de tracking serialise' AUTO_INCREMENT=1 ;";
req_insert($sql);

$sql = "CREATE TABLE IF NOT EXISTS `wiki` (
  `wiki_cdn` int(5) NOT NULL AUTO_INCREMENT,
  `wiki_auteur_no` int(5) NOT NULL DEFAULT '0',
  `wiki_consigne_cmt` text,
  `wiki_seq_no` int(5) NOT NULL DEFAULT '0',
  `wiki_ordre_on` tinyint(1) NOT NULL DEFAULT '0',
  `wiki_ordre_no` tinyint(2) NOT NULL DEFAULT '0',
  `wiki_create_dt` datetime NOT NULL DEFAULT '2001-01-01 00:00:00',
  PRIMARY KEY (`wiki_cdn`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";
req_insert($sql);
$sql = "SELECT all FROM wiki";
if (mysql_query($sql) == FALSE)
{
   $sql = "INSERT INTO `wiki` (`wiki_cdn`, `wiki_auteur_no`, `wiki_consigne_cmt`, `wiki_seq_no`, `wiki_ordre_on`, `wiki_ordre_no`, `wiki_create_dt`) VALUES
          (1, 1, 'La FOAD est une méthode d''enseignement qui a fait ses preuves: dissertez sur la question et énumérez les avantages et inconvénients .', 10001, 1, 1, '2012-06-28 11:04:15');";
   req_insert($sql);
}else
  $avis=1;


$sql = "CREATE TABLE IF NOT EXISTS `wikiapp` (
  `wkapp_cdn` int(10) NOT NULL AUTO_INCREMENT,
  `wkapp_wiki_no` int(5) NOT NULL DEFAULT '0',
  `wkapp_app_no` int(5) NOT NULL DEFAULT '0',
  `wkapp_seq_no` int(5) NOT NULL DEFAULT '0',
  `wkapp_parc_no` int(3) NOT NULL DEFAULT '0',
  `wkapp_grp_no` int(3) NOT NULL DEFAULT '0',
  `wkapp_clan_nb` int(3) NOT NULL DEFAULT '0',
  `wkapp_db_dt` date NOT NULL DEFAULT '0000-00-00',
  `wkapp_df_dt` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`wkapp_cdn`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";
req_insert($sql);
$sql = "SELECT all FROM wikiapp";
if (mysql_query($sql) == FALSE)
{
   $sql = "INSERT INTO `wikiapp` (`wkapp_cdn`, `wkapp_wiki_no`, `wkapp_app_no`, `wkapp_seq_no`, `wkapp_parc_no`, `wkapp_grp_no`, `wkapp_clan_nb`, `wkapp_db_dt`, `wkapp_df_dt`) VALUES
(1, 1, 3, 10001, 10000, 1, 1, '2012-06-28', '2012-06-28'),
(2, 1, 4, 10001, 10000, 1, 1, '2012-06-28', '2012-06-28'),
(3, 1, 6, 10001, 10000, 1, 1, '2012-06-28', '2012-06-28');";
   req_insert($sql);
}else
  $avis=1;


$sql = "CREATE TABLE IF NOT EXISTS `wikibodies` (
  `wkbody_cdn` int(5) NOT NULL AUTO_INCREMENT,
  `wkbody_auteur_no` int(5) NOT NULL DEFAULT '0',
  `wkbody_clan_no` int(5) NOT NULL DEFAULT '0',
  `wkbody_body_cmt` text CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `wkbody_titre_lb` varchar(255) DEFAULT NULL,
  `wkbody_img_no` int(5) NOT NULL DEFAULT '0',
  `wkbody_order_no` int(3) NOT NULL DEFAULT '1',
  `wkbody_show_on` tinyint(1) NOT NULL DEFAULT '1',
  `wkbody_date_dt` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`wkbody_cdn`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";
req_insert($sql);
$sql = "SELECT all FROM wikibodies";
if (mysql_query($sql) == FALSE)
{
   $sql = "INSERT INTO `wikibodies` (`wkbody_cdn`, `wkbody_auteur_no`, `wkbody_clan_no`, `wkbody_body_cmt`, `wkbody_titre_lb`, `wkbody_img_no`, `wkbody_order_no`, `wkbody_show_on`, `wkbody_date_dt`) VALUES
(1, 1, 1, 'Ceci est un thème qui devrait vous interroger sur le &lt;font color=&quot;#993300&quot;&gt;&lt;strong style=&quot;background-color: #ffff99&quot;&gt;bien-fondé&lt;/strong&gt;&lt;/font&gt; de la formation que vous êtes entrain de suivre...', 'Une Introduction du responsable de formation', 0, 1, 1, 1340874784);";
   req_insert($sql);
}else
  $avis=1;


$sql = "CREATE TABLE IF NOT EXISTS `wikimeta` (
  `wkmeta_cdn` int(5) NOT NULL AUTO_INCREMENT,
  `wkmeta_clan_no` int(5) NOT NULL DEFAULT '0',
  `wkmeta_auteur_no` int(5) NOT NULL DEFAULT '0',
  `wkmeta_titre_lb` varchar(150) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `wkmeta_style_lb` varchar(255) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `wkmeta_date_dt` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`wkmeta_cdn`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci COMMENT='Gestion du titre du WikiDoc' AUTO_INCREMENT=1 ;";
req_insert($sql);
$sql = "SELECT all FROM wikimeta";
if (mysql_query($sql) == FALSE)
{
   $sql = "INSERT INTO `wikimeta` (`wkmeta_cdn`, `wkmeta_clan_no`, `wkmeta_auteur_no`, `wkmeta_titre_lb`, `wkmeta_style_lb`, `wkmeta_date_dt`) VALUES
        (1, 1, 1, 'La Foad: Avantages et inconvénients', 'font-size:24px;', 1340874658);";
   req_insert($sql);
}else
  $avis=1;


$sql = "CREATE TABLE IF NOT EXISTS `wikimg` (
  `wkimg_cdn` int(5) NOT NULL AUTO_INCREMENT,
  `wkimg_content_blb` blob NOT NULL,
  `wkimg_auteur_no` int(5) NOT NULL DEFAULT '0',
  PRIMARY KEY (`wkimg_cdn`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci COMMENT='bibliotheque des images' AUTO_INCREMENT=2 ;";
req_insert($sql);
$sql = "INSERT INTO `wikimg` (`wkimg_cdn`, `wkimg_content_blb`, `wkimg_auteur_no`) VALUES
(1, 0x47494638396157004600e7ff0049474b404b4c474b4d4c50524853535152505351554f53555156585755594d585955575454585a555a5c585a58505c5c5b595d575b5d535e5f5f5b5a585d5f5c5d5b5e5c60565f5b5a5e60605d615a5f615c6063566263615f63645f5e5f615e5b635f5d62645f636664626660656763656269646362666867656964686b69676b6769666d68676b696d666b6d686c6e6d6b6f6b6d6a716c6b6a6e716f6d716c70736f706d716f737472767772716f747673747276747879767a7b767573787a7678757c797d7f7a79777c7e7a7c797e7c80827d7c7a7f817d7f7c807e8285807f8381857e83858183808782818583878984838186898486838785898d848a8d87868889868b888c8e8988868b8e8e8b8f908b8a8b8d8a908e92938e8d8e908d8d91949290949791909193909492969795999a959395979493989a9c97969a979c9c9a9e9f9a99979c9fa2999f9a9c999e9ca0a19c9b9d9f9ca09ea2a39e9d9ba0a2a29fa3a3a1a5a6a1a09ea3a6a1a3a0a5a2a7a8a3a2a6a3a8a0a5a8a7a5a9aaa5a4a2a7a9a5a7a4a3a8aba9a7aba4a9acaaa8acada8a6a5aaada8aaa7aba9ada6abaeacaaaeaeabb0a8adafb1abaaabadaaaaafb2b0aeb2b3aeacaeb1adb6b0afb3b1b5b8b3b2b6b4b8b3b6b2bab5b4b2b7b9bab7bcbdb7b6b7b9b6bdbabfbabcb9c0bbb9bebcc0b8bec0bfbdc1c2bdbbc4bebdbec0bdc1bfc3c3c1c5c6c1c0bfc4c6c2c4c1c8c3c2c6c3c8c4c6c3c7c5c9cac5c3c4c9cccdc7c6c7c9c6cac8cccdcacfd0cac9caccc9d2cdcccccfcbd0ced2cad0d2d5cfcecfd1ced7d1d0d1d3d0d4d2d6dad4d3d4d6d3d8d5dad2d7d9d6d8d5dcd7d5d8dad7dbd9ddded9d7e3d8dfe1dbdadbdddadedce0e3dddcdddfdce2dfe4dee1dde5dfdee4e2e6e1e4e0e8e2e1e4e6e3e7e5e9eae5e3ede7e6e7e9e5e5eaede8eae7ebe9ede9ebe8efeae8ebedeaeeecf0f2ecebedefebeef1edecf1f4f5efeef3f0f5f0f2eff1f4f0f8f2f1f4f6f3f1f7f9fbf5f3f9f6fbf6f8f4f7f9f6fdf8f6f8faf7f6fbfef9fbf8fafcf9fffbf9fdfbfffbfdfafffdfbfffdfff9fffffcfffbfefffcffffff21f904010a00ff002c00000000570046000008fe00fd091c48d05f3e82f70a16c4a7b0a13f860e234a7408b1e14187f94e1c3093d0dfae22356e74117671a24985154faa24584f9dcb97a4aac0cb00cf1d0e1fcac68d9bb5840125782fd5d5c378af634378d9b2cd5b38f05e4a85f2c0598317d1591d3b58b1e609410e8f95403acc28ace7e5008e3e59a98d0d01e3c43185f7f460881021438e7303aba868d122068d1662dc15acb72403dd086f8c0eac67a790e341850625c2300c452043162256ab82c391a14275ec159c160185083a0aed84c8406447861121e4098432e20486db1850643844704686103882bc16a3505de341c821efe1110783a32b6426f60883bc0ebb829446884001a4203b0c0caafe0944a562841c814f32bcf0774f1eac0c27180cf5a72a420b4e0fc985c010af60393b814416d9206a90a1c40991e060ca44e08ce0c820755045101127dc515b417d60b00641c3606083404e64004353b1ec67ca413d44f00b41788ca04641e93426207265c0e1c30b91bc90cb44eb60d00868120a844f79bda810c27cfec0734504ae1824d03611ac0062062db027d039b73d22d0081194d3542f231411a43f31ce48a31d42bce0c80bc24ca48e734012e40e0330b8330506b80ca40a0d0c9043903c118c30941316d02067061924e20f390ca0a05008232c38907186400659216b9861040a91f0206944b7e0b0882279c0935042bf6050863fa16410c740fe87d8d61f53fe3881c188566299c125fe28c3800f0a9d10021c0451fa186476f8c0868f5a7031d1077930b2c401d218b54607b3f8a34d043c0c244608474664ab0a04d1828105cff8130b0355289402064f146b47a5c845460806bd94a04821114454ce154514f2c707a98811240d19f4974fa079617042490a5541e540d98c0081a3ac32405c41ef16216fa5333a1282386674e1c8105e34d4cc023734d20803e040e08d95f5d837100e1948235015213cbca82ab1c4424b2c209e304209259c60c1087488235027096c4c900b1804415099963a464816769c138223919cf08568028d73e71f8c30f2c22361fc30ab3fc7609032437f6450c9ce1df81ccbfe01b76580c1524ed42642072260c034384f4720f540545b3da98c8e3d56070cd1645088216a40b0862ad1a4420406642062c81a11a803411b12e6130a06bc09740a0662f923b1cfb4047a8269434191810a7ae0814711f1adea4f271a2bd4b8bc8f0d38c8222ea8d08b089210e2881649e070441b8e1092c71523bc59468403dd598b35d958934b0437ec0c1fc4fe003f547a550a394508112c654a04ed728cc11257cb7869218418c41f7e40873b9c60547f089063fe5084085ce11c0c288322c0c79e139c20020cc820036c33943474205c04a1c10944a33b5c09e41719b0409b7ac10023040b035d90d72008f119c880cc112278c42c16b08330a8410dfe5c7841061ef18d4030200f8ea120965080042220e1892b8840ba2a111f7510242323604888d633906d6020033baa060368f094118c404b8ffb1f0d19f1073bc0a1113880c1367661862b54e110cf50460e30700447088882a6c040180a8287ddf8e3173d4840ba06020f0664b156ea210839beb820786020048a19470868a08cfecdf0338bc88219810004159ca00cbae9412daa20c41ac0c18ff58a933fb490015514e41616709c1a32b02186dca31781d2622407528e2f7642203db0402f08628713d8a120c689cc22c000813168031f4ef10733105008498820079560821f091199005547422ac0005e08b20e06884020e0c0a078f0818913d0e0915bfe2cd617d1d88a08a8e098fe0007a2aed3bf4238220f0cf882600aa284254822091190060d185188cb20271090a16007fe56907bcc651e0ce99913a4f10b1720800c1910261705f2a60cf04120e5e09b0c86210d2c8c20030a49471d1c31830334a321f8c8070314718714580303158ddca508013e6510e004566a8a0a02b00b814803017361c001acd009030c250803c02944ecb10002acd41506380c0612908de2d4a10b3d58a4439e0086e75c82060a1450640c01be6cc4e22d0a41462cda2a10729c610966c04b366ac19e63d4e2160531062c62211b8164a3135158c21dd6d19074e46104889348258610893278410975b8542c2134a687ac4448513dfe09fb1ca28e3d6400a41289050e240107248c010d7a551e0585040c49fce10f7d18c4269c764b5d00e31cf45088346e410b82fa0317bac8ae2e78c18b5e3003490539471e32308e89b4420791688312b4d086d52667b8f960441e3e13b940d4411705c943201a41d882d4a20f70086d3ef610884008e2b87f10441ef2408a8500631d7548021e5a5b103b44a1b46648421e62193959faa31101aac32650418a41f80134dd2048801aa18d86d4a28de515c81f667815acd4a1c0fb2588352a60893fb46105c390c814c010092d1c8207053ed67b25348f1b07e214eaa809359018085b2069c52d56c88bed10637ffce172a940859839b161d60ee409b78040fe2118a1830458b121ee888022063103652095c397a2202d0241085a28461d48dc433206726216bbb88d020e90a208628f45440613123aaa32bed6883240c01a0dd1421fad10816da000117a55205f17da0842f421ba05e1441b4741e844185acb81b0433806f207432c4221f1e88321f650d58788010e6bc0c18f105a062f25241e613881231a11012b98010cc14dce04173a5f4534041779d8e940fae0eafe12a416b19eb58c1581888648a2107bc0044142708b2bcca00d8a70c4122270021dbc0002a42d4414b4c00e063ce8a2b164aa60f2315f4934441a9cc00428b6dd4d6f0f04dcb2a635b91b428a42f4a1110481c7015a01880f1441118b880422fe081c0945288274ea50421694aabccbd581da86f8834afed06d17879bd68628f715fde10a42dc01e393f2c309ba600d3c60a0097d4084c91331882360e01d1048c1836a485f1bbe5cc68cd8c3364eb262870be4c575e8728018d1104e14220f061f8867db80001888830d1fa84116d6a08632fc6001b120c409d420ba199253b5a31648f4fec00a81cc56c585e0efa1eb90e83537c4126757b7daebb0663530c00ad618061eaa500531cc821c37c00123d610863634c28f5fae57e0fd418c13e7214f13099024bcee0fb0277a106457c897fbc0d849519e9c8d20c30932d0832b54421223d0d0095cb0043118617775f89180043e90bffb81109f48b1430afe9d65fffe81f1860f84d215228a4104c21263d2290009f1074530c2107058c1337c2489087cca20b780810ac0b008e4acde1ff3c07e9f1108799008f8a510dcb7788dc70840e112dbe0097ef00779800e30721c66b204a6f00b38100949b0210dc10a3da0030f021a0b3510c160077ea04026660c4e22107ea0088aa76588466b91b1077bd0077b70077c765f2d281065a2648e7002e16006651009a523114bf004fb727505910fd4600b797062869007bb80242f1883dec76586f76512b8607e90077670073a531cc7f13f8e8001d29003c8853111910f16e0684cd810f5800c8d410870801f2e680858f86d33e80ff63076c9400c82e80ce91011c6911cfe8fc16ccac0028660073960122b70624c7878fe800a81660717e1077a487b5b966888c008f9600ff9308a12a17ec7b26611200c2ba00865e004262104709008d6a1180df1093364077ee20f97317b36878b5be8782aa10e94d761b88701aa383a8e2311276008f64515f2200ee44061fe800e79a0087d308657c889b1968b5e068c27618a59d30818300d3e305fef2411c980028bc00847800df7200d6b6007c1e01083b06bc0e08230487bb03008be2863de6812c2d872897702e0600664200918806918610176b0083a200618d009de50077dd06b0a71197dc082fea0898da09005810ab15688fe987b27218c88582f18780c37c00859e038d9a4272710fe05d65801d8b003cc1090a700677b7076e2f16102738f0a0179a14183246912a6a85404d802d5e0238d70027dd014fee00e5d10025ae03207a00e0cf00d02814486d010c3406014040a8390079257105f560804f16540f78dc3782c86c00811d00937d01896b70bea700ff9000e70c00064d00888100532100df221107376076f2310f7b00981e007982810b850607e90537750089f909689b71201a96483a008411001e41001d1930717242cf08208cb560431800e03502a02410b3346086fe60ff5800a27b606b6c0488d11089e40361e316375206e06f165453911eaa75a03226f62f00727706e60d30892e008eb981e5a600d07106b41721c94270996fe00208df85304910abb6707922009797007c7c56a03910fc28999a9759291e3081d000a8710418c10722317050c1006cff0081040088460074be183fa45088a50756b30680a310a6b80518a404eb18687e969077920732b6109ba6608fe1940e6d408231006e7f004b8712b95100e5b20023fc008e607a102610fcdb009026209c4709805610fc1307894e00b5ba710d5b0a3afe50fc32066401a6661b60aab403a55400bcd500dc3a0072e700287200aae2066089a9e3dd8a3567aa50d610fbfc00658600462600a5d86a5623aa62b0114e9c00e9448a66a7a10b448116afaa6705a10f3f0079600099090082b721261f65ab46007d62511ed0009944009fe90109b12010fa7f0075ee20fc820093d2910efd006b14908bc7012a4300a4f2111b0b006ef6012f550076ae10fd12007413611f0200a960074c7d008d8c0126db04efea00b8460a90db612aeb0067fea10de402c0351073a77a850ca092c880c8cd0aa8bf1aa03d10cb36a12a2200aafb5a9d25810d1f0948496072671aa0da6067358acae0aabbbd09526410ab53a11f3e10a7010ade9890dcf3410f372ad9f800afe500ca0a00c8a60ac02510fc82a10ab6009b46a12e450aab070ae26810e2f62786db009ee0aaff1b006d1c0ad03110f6d900de9500ec45007861a11cd6a12dda006f0100ff0a71292700af3000fa01009dc70ad9c50abb5900886103faa2b5a0bac10b3ba10a026410ca53a11d5e0099c300ce990a605010c9e7009374baec430a5b5b00bcc15a74a4ba607e1b34bfbb4501bb5523bb5541bb50101003b, 0);";
req_insert($sql);

$sql = "CREATE TABLE IF NOT EXISTS `wikinote` (
  `wknote_cdn` int(5) NOT NULL AUTO_INCREMENT,
  `wknote_app_no` int(5) NOT NULL DEFAULT '0',
  `wknote_note_lb` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`wknote_cdn`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COMMENT='Table de notation du wiki' AUTO_INCREMENT=1 ;";
req_insert($sql);


$sql = "CREATE TABLE IF NOT EXISTS `groupe1` (
  `id` bigint(20) unsigned NOT NULL default '0',
  `datestamp` datetime NOT NULL default '0000-00-00 00:00:00',
  `thread` int(11) NOT NULL default '0',
  `parent` int(11) NOT NULL default '0',
  `author` char(37) NOT NULL default '',
  `subject` char(255) NOT NULL default '',
  `email` char(200) NOT NULL default '',
  `attachment` char(64) NOT NULL default '',
  `host` char(50) NOT NULL default '',
  `email_reply` char(1) NOT NULL default 'N',
  `approved` char(1) NOT NULL default 'N',
  `msgid` char(100) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `author` (`author`),
  KEY `datestamp` (`datestamp`),
  KEY `subject` (`subject`),
  KEY `thread` (`thread`),
  KEY `parent` (`parent`),
  KEY `approved` (`approved`),
  KEY `msgid` (`msgid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;";
req_insert($sql);

$sql = "CREATE TABLE IF NOT EXISTS `seq_twitter` (
  `seqtwit_cdn` smallint(5) NOT NULL AUTO_INCREMENT,
  `seqtwit_seq_no` smallint(5) NOT NULL,
  `seqtwit_auteur_no` int(5) NOT NULL,
  `seqtwit_creation_dt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`seqtwit_cdn`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";
req_insert($sql);

$sql = "CREATE TABLE IF NOT EXISTS `seq_twit_form` (
  `seqformtwit_cdn` smallint(5) NOT NULL AUTO_INCREMENT,
  `seqformtwit_code_lb` varchar(15) NOT NULL,
  `seqformtwit_flag_on` tinyint(1) NOT NULL DEFAULT '1',
  `seqformtwit_seq_no` smallint(5) NOT NULL,
  `seqformtwit_parc_no` smallint(5) NOT NULL,
  `seqformtwit_grp_no` smallint(5) NOT NULL,
  `seqformtwit_form_no` int(5) NOT NULL,
  `seqformtwit_auteur_no` smallint(5) NOT NULL,
  `seqformtwit_date_dt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`seqformtwit_cdn`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";
req_insert($sql);


$sql = "SELECT all FROM groupe1";
if (mysql_query($sql) == FALSE)
{
   $sql = "INSERT INTO `groupe1` (`id`, `datestamp`, `thread`, `parent`, `author`, `subject`, `email`, `attachment`, `host`, `email_reply`, `approved`, `msgid`) VALUES
    (1, '2012-06-28 13:57:08', 1, 0, 'Formagri Administrateur', 'Fwd: Formidable !', 'moi@chez_untel.fr', '', '127.0.0.1', '', 'Y', '<6cec3d9cc5b39b27f5a5f8dc8cb8483a.moi@chez_untel.fr>');";
   req_insert($sql);
}else
   $avis=1;

$sql = "CREATE TABLE IF NOT EXISTS `groupe1_bodies` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `body` text NOT NULL,
  `thread` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `thread` (`thread`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";
req_insert($sql);
$sql = "SELECT all FROM groupe1_bodies";
if (mysql_query($sql) == FALSE)
{
   $sql = "INSERT INTO `groupe1_bodies` (`id`, `body`, `thread`) VALUES
    (1, '<HTML>test</HTML>', 1);";
    req_insert($sql);
}else
   $avis=1;


$sql = "CREATE TABLE IF NOT EXISTS `prescription_1` (
  `presc_cdn` smallint(5) unsigned NOT NULL auto_increment,
  `presc_seq_no` smallint(5) unsigned NOT NULL default '0',
  `presc_parc_no` smallint(5) NOT NULL default '0',
  `presc_utilisateur_no` smallint(5) unsigned NOT NULL default '0',
  `presc_datedeb_dt` date default NULL,
  `presc_datefin_dt` date default NULL,
  `presc_prescripteur_no` smallint(5) unsigned NOT NULL default '0',
  `presc_formateur_no` smallint(5) unsigned NOT NULL default '0',
  `presc_grp_no` smallint(3) NOT NULL default '0',
  `presc_ordre_no` smallint(2) NOT NULL default '1',
  PRIMARY KEY  (`presc_cdn`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";
req_insert($sql);
$sql = "SELECT all FROM prescription_1";
if (mysql_query($sql) == FALSE)
{
   $sql = "INSERT INTO `prescription_1` (`presc_cdn`, `presc_seq_no`, `presc_parc_no`, `presc_utilisateur_no`, `presc_datedeb_dt`, `presc_datefin_dt`, `presc_prescripteur_no`, `presc_formateur_no`, `presc_grp_no`, `presc_ordre_no`) VALUES
   (1, 1, 1, 3, '2012-06-28', '2015-06-19', 1, 9, 1, 1),
   (2, 2, 1, 3, '2012-06-28', '2015-06-19', 1, 9, 1, 1),
   (3, 3, 1, 3, '2012-06-28', '2015-06-19', 1, 9, 1, 1),
   (4, 1, 1, 4, '2012-06-28', '2015-06-19', 1, 9, 1, 1),
   (5, 2, 1, 4, '2012-06-28', '2015-06-19', 1, 9, 1, 1),
   (6, 3, 1, 4, '2012-06-28', '2015-06-19', 1, 9, 1, 1),
   (7, 1, 1, 6, '2012-06-28', '2015-06-19', 1, 9, 1, 1),
   (8, 2, 1, 6, '2012-06-28', '2015-06-19', 1, 9, 1, 1),
   (9, 3, 1, 6, '2012-06-28', '2015-06-19', 1, 9, 1, 1);";
   req_insert($sql);
}else
   $avis=1;

$sql = "CREATE TABLE IF NOT EXISTS `scorm_util_module_1` (
  `user_module_cdn` int(10) NOT NULL auto_increment,
  `user_module_no` int(8) NOT NULL default '0',
  `mod_module_no` int(8) NOT NULL default '0',
  `mod_grp_no` smallint(4) NOT NULL default '0',
  `lesson_location` varchar(255) NOT NULL default '',
  `lesson_mode` enum('BROWSE','NORMAL','REVIEW') NOT NULL default 'NORMAL',
  `lesson_status` enum('NOT ATTEMPTED','PASSED','FAILED','COMPLETED','BROWSED','INCOMPLETE','UNKNOWN') NOT NULL default 'NOT ATTEMPTED',
  `entry` enum('AB-INITIO','RESUME','') NOT NULL default 'AB-INITIO',
  `raw` tinyint(4) NOT NULL default '-1',
  `scoreMin` tinyint(4) NOT NULL default '-1',
  `scoreMax` tinyint(4) NOT NULL default '-1',
  `total_time` varchar(13) NOT NULL default '0000:00:00.00',
  `session_time` varchar(13) NOT NULL default '0000:00:00.00',
  `suspend_data` text NOT NULL,
  `comments` text NOT NULL,
  `comments_from_lms` text NOT NULL,
  `credit` enum('CREDIT','NO-CREDIT') NOT NULL default 'NO-CREDIT',
  `exit` varchar(8) NOT NULL default '',
  `last_acces` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`user_module_cdn`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 PACK_KEYS=0 COMMENT='Table de tracking des apprenants par sco' AUTO_INCREMENT=1 ;";
req_insert($sql);
$sql = "SELECT all FROM scorm_util_module_1";
if (mysql_query($sql) == FALSE)
{
   $sql = "INSERT INTO `scorm_util_module_1` (`user_module_cdn`, `user_module_no`, `mod_module_no`, `mod_grp_no`, `lesson_location`, `lesson_mode`, `lesson_status`, `entry`, `raw`, `scoreMin`, `scoreMax`, `total_time`, `session_time`, `suspend_data`, `comments`, `comments_from_lms`, `credit`, `exit`, `last_acces`) VALUES
(1, 3, 1, 1, '', 'NORMAL', 'NOT ATTEMPTED', 'AB-INITIO', -1, -1, -1, '0000:00:00.00', '0000:00:00.00', '', '', '', 'NO-CREDIT', '', '0000-00-00 00:00:00'),
(2, 3, 2, 1, '', 'NORMAL', 'NOT ATTEMPTED', 'AB-INITIO', -1, -1, -1, '0000:00:00.00', '0000:00:00.00', '', '', '', 'NO-CREDIT', '', '0000-00-00 00:00:00'),
(3, 3, 3, 1, '', 'NORMAL', 'NOT ATTEMPTED', 'AB-INITIO', -1, -1, -1, '0000:00:00.00', '0000:00:00.00', '', '', '', 'NO-CREDIT', '', '0000-00-00 00:00:00'),
(4, 3, 4, 1, '', 'NORMAL', 'NOT ATTEMPTED', 'AB-INITIO', -1, -1, -1, '0000:00:00.00', '0000:00:00.00', '', '', '', 'NO-CREDIT', '', '0000-00-00 00:00:00'),
(5, 3, 5, 1, '', 'NORMAL', 'NOT ATTEMPTED', 'AB-INITIO', -1, -1, -1, '0000:00:00.00', '0000:00:00.00', '', '', '', 'NO-CREDIT', '', '0000-00-00 00:00:00'),
(6, 3, 6, 1, '', 'NORMAL', 'NOT ATTEMPTED', 'AB-INITIO', -1, -1, -1, '0000:00:00.00', '0000:00:00.00', '', '', '', 'NO-CREDIT', '', '0000-00-00 00:00:00'),
(7, 3, 7, 1, '', 'NORMAL', 'NOT ATTEMPTED', 'AB-INITIO', -1, -1, -1, '0000:00:00.00', '0000:00:00.00', '', '', '', 'NO-CREDIT', '', '0000-00-00 00:00:00'),
(8, 3, 8, 1, '', 'NORMAL', 'NOT ATTEMPTED', 'AB-INITIO', -1, -1, -1, '0000:00:00.00', '0000:00:00.00', '', '', '', 'NO-CREDIT', '', '0000-00-00 00:00:00'),
(9, 3, 9, 1, '', 'NORMAL', 'NOT ATTEMPTED', 'AB-INITIO', -1, -1, -1, '0000:00:00.00', '0000:00:00.00', '', '', '', 'NO-CREDIT', '', '0000-00-00 00:00:00'),
(10, 3, 10, 1, '', 'NORMAL', 'NOT ATTEMPTED', 'AB-INITIO', -1, -1, -1, '0000:00:00.00', '0000:00:00.00', '', '', '', 'NO-CREDIT', '', '0000-00-00 00:00:00'),
(11, 3, 11, 1, '', 'NORMAL', 'NOT ATTEMPTED', 'AB-INITIO', -1, -1, -1, '0000:00:00.00', '0000:00:00.00', '', '', '', 'NO-CREDIT', '', '0000-00-00 00:00:00'),
(12, 3, 12, 1, '', 'NORMAL', 'NOT ATTEMPTED', 'AB-INITIO', -1, -1, -1, '0000:00:00.00', '0000:00:00.00', '', '', '', 'NO-CREDIT', '', '0000-00-00 00:00:00'),
(13, 3, 13, 1, '', 'NORMAL', 'NOT ATTEMPTED', 'AB-INITIO', -1, -1, -1, '0000:00:00.00', '0000:00:00.00', '', '', '', 'NO-CREDIT', '', '0000-00-00 00:00:00'),
(14, 3, 14, 1, '', 'NORMAL', 'NOT ATTEMPTED', 'AB-INITIO', -1, -1, -1, '0000:00:00.00', '0000:00:00.00', '', '', '', 'NO-CREDIT', '', '0000-00-00 00:00:00'),
(15, 3, 15, 1, '', 'NORMAL', 'NOT ATTEMPTED', 'AB-INITIO', -1, -1, -1, '0000:00:00.00', '0000:00:00.00', '', '', '', 'NO-CREDIT', '', '0000-00-00 00:00:00'),
(16, 3, 16, 1, '', 'NORMAL', 'NOT ATTEMPTED', 'AB-INITIO', -1, -1, -1, '0000:00:00.00', '0000:00:00.00', '', '', '', 'NO-CREDIT', '', '0000-00-00 00:00:00'),
(17, 3, 17, 1, '', 'NORMAL', 'NOT ATTEMPTED', 'AB-INITIO', -1, -1, -1, '0000:00:00.00', '0000:00:00.00', '', '', '', 'NO-CREDIT', '', '0000-00-00 00:00:00'),
(18, 3, 18, 1, '', 'NORMAL', 'NOT ATTEMPTED', 'AB-INITIO', -1, -1, -1, '0000:00:00.00', '0000:00:00.00', '', '', '', 'NO-CREDIT', '', '0000-00-00 00:00:00'),
(19, 3, 19, 1, '', 'NORMAL', 'NOT ATTEMPTED', 'AB-INITIO', -1, -1, -1, '0000:00:00.00', '0000:00:00.00', '', '', '', 'NO-CREDIT', '', '0000-00-00 00:00:00'),
(20, 3, 20, 1, '', 'NORMAL', 'NOT ATTEMPTED', 'AB-INITIO', -1, -1, -1, '0000:00:00.00', '0000:00:00.00', '', '', '', 'NO-CREDIT', '', '0000-00-00 00:00:00'),
(21, 3, 21, 1, '', 'NORMAL', 'NOT ATTEMPTED', 'AB-INITIO', -1, -1, -1, '0000:00:00.00', '0000:00:00.00', '', '', '', 'NO-CREDIT', '', '0000-00-00 00:00:00'),
(22, 3, 22, 1, '', 'NORMAL', 'NOT ATTEMPTED', 'AB-INITIO', -1, -1, -1, '0000:00:00.00', '0000:00:00.00', '', '', '', 'NO-CREDIT', '', '0000-00-00 00:00:00'),
(23, 3, 23, 1, '', 'NORMAL', 'NOT ATTEMPTED', 'AB-INITIO', -1, -1, -1, '0000:00:00.00', '0000:00:00.00', '', '', '', 'NO-CREDIT', '', '0000-00-00 00:00:00'),
(24, 3, 24, 1, '', 'NORMAL', 'NOT ATTEMPTED', 'AB-INITIO', -1, -1, -1, '0000:00:00.00', '0000:00:00.00', '', '', '', 'NO-CREDIT', '', '0000-00-00 00:00:00'),
(25, 3, 25, 1, '', 'NORMAL', 'NOT ATTEMPTED', 'AB-INITIO', -1, -1, -1, '0000:00:00.00', '0000:00:00.00', '', '', '', 'NO-CREDIT', '', '0000-00-00 00:00:00'),
(26, 3, 26, 1, '', 'NORMAL', 'NOT ATTEMPTED', 'AB-INITIO', -1, -1, -1, '0000:00:00.00', '0000:00:00.00', '', '', '', 'NO-CREDIT', '', '0000-00-00 00:00:00'),
(27, 4, 1, 1, '', 'NORMAL', 'NOT ATTEMPTED', 'AB-INITIO', -1, -1, -1, '0000:00:00.00', '0000:00:00.00', '', '', '', 'NO-CREDIT', '', '0000-00-00 00:00:00'),
(28, 4, 2, 1, '', 'NORMAL', 'NOT ATTEMPTED', 'AB-INITIO', -1, -1, -1, '0000:00:00.00', '0000:00:00.00', '', '', '', 'NO-CREDIT', '', '0000-00-00 00:00:00'),
(29, 4, 3, 1, '', 'NORMAL', 'NOT ATTEMPTED', 'AB-INITIO', -1, -1, -1, '0000:00:00.00', '0000:00:00.00', '', '', '', 'NO-CREDIT', '', '0000-00-00 00:00:00'),
(30, 4, 4, 1, '', 'NORMAL', 'NOT ATTEMPTED', 'AB-INITIO', -1, -1, -1, '0000:00:00.00', '0000:00:00.00', '', '', '', 'NO-CREDIT', '', '0000-00-00 00:00:00'),
(31, 4, 5, 1, '', 'NORMAL', 'NOT ATTEMPTED', 'AB-INITIO', -1, -1, -1, '0000:00:00.00', '0000:00:00.00', '', '', '', 'NO-CREDIT', '', '0000-00-00 00:00:00'),
(32, 4, 6, 1, '', 'NORMAL', 'NOT ATTEMPTED', 'AB-INITIO', -1, -1, -1, '0000:00:00.00', '0000:00:00.00', '', '', '', 'NO-CREDIT', '', '0000-00-00 00:00:00'),
(33, 4, 7, 1, '', 'NORMAL', 'NOT ATTEMPTED', 'AB-INITIO', -1, -1, -1, '0000:00:00.00', '0000:00:00.00', '', '', '', 'NO-CREDIT', '', '0000-00-00 00:00:00'),
(34, 4, 8, 1, '', 'NORMAL', 'NOT ATTEMPTED', 'AB-INITIO', -1, -1, -1, '0000:00:00.00', '0000:00:00.00', '', '', '', 'NO-CREDIT', '', '0000-00-00 00:00:00'),
(35, 4, 9, 1, '', 'NORMAL', 'NOT ATTEMPTED', 'AB-INITIO', -1, -1, -1, '0000:00:00.00', '0000:00:00.00', '', '', '', 'NO-CREDIT', '', '0000-00-00 00:00:00'),
(36, 4, 10, 1, '', 'NORMAL', 'NOT ATTEMPTED', 'AB-INITIO', -1, -1, -1, '0000:00:00.00', '0000:00:00.00', '', '', '', 'NO-CREDIT', '', '0000-00-00 00:00:00'),
(37, 4, 11, 1, '', 'NORMAL', 'NOT ATTEMPTED', 'AB-INITIO', -1, -1, -1, '0000:00:00.00', '0000:00:00.00', '', '', '', 'NO-CREDIT', '', '0000-00-00 00:00:00'),
(38, 4, 12, 1, '', 'NORMAL', 'NOT ATTEMPTED', 'AB-INITIO', -1, -1, -1, '0000:00:00.00', '0000:00:00.00', '', '', '', 'NO-CREDIT', '', '0000-00-00 00:00:00'),
(39, 4, 13, 1, '', 'NORMAL', 'NOT ATTEMPTED', 'AB-INITIO', -1, -1, -1, '0000:00:00.00', '0000:00:00.00', '', '', '', 'NO-CREDIT', '', '0000-00-00 00:00:00'),
(40, 4, 14, 1, '', 'NORMAL', 'NOT ATTEMPTED', 'AB-INITIO', -1, -1, -1, '0000:00:00.00', '0000:00:00.00', '', '', '', 'NO-CREDIT', '', '0000-00-00 00:00:00'),
(41, 4, 15, 1, '', 'NORMAL', 'NOT ATTEMPTED', 'AB-INITIO', -1, -1, -1, '0000:00:00.00', '0000:00:00.00', '', '', '', 'NO-CREDIT', '', '0000-00-00 00:00:00'),
(42, 4, 16, 1, '', 'NORMAL', 'NOT ATTEMPTED', 'AB-INITIO', -1, -1, -1, '0000:00:00.00', '0000:00:00.00', '', '', '', 'NO-CREDIT', '', '0000-00-00 00:00:00'),
(43, 4, 17, 1, '', 'NORMAL', 'NOT ATTEMPTED', 'AB-INITIO', -1, -1, -1, '0000:00:00.00', '0000:00:00.00', '', '', '', 'NO-CREDIT', '', '0000-00-00 00:00:00'),
(44, 4, 18, 1, '', 'NORMAL', 'NOT ATTEMPTED', 'AB-INITIO', -1, -1, -1, '0000:00:00.00', '0000:00:00.00', '', '', '', 'NO-CREDIT', '', '0000-00-00 00:00:00'),
(45, 4, 19, 1, '', 'NORMAL', 'NOT ATTEMPTED', 'AB-INITIO', -1, -1, -1, '0000:00:00.00', '0000:00:00.00', '', '', '', 'NO-CREDIT', '', '0000-00-00 00:00:00'),
(46, 4, 20, 1, '', 'NORMAL', 'NOT ATTEMPTED', 'AB-INITIO', -1, -1, -1, '0000:00:00.00', '0000:00:00.00', '', '', '', 'NO-CREDIT', '', '0000-00-00 00:00:00'),
(47, 4, 21, 1, '', 'NORMAL', 'NOT ATTEMPTED', 'AB-INITIO', -1, -1, -1, '0000:00:00.00', '0000:00:00.00', '', '', '', 'NO-CREDIT', '', '0000-00-00 00:00:00'),
(48, 4, 22, 1, '', 'NORMAL', 'NOT ATTEMPTED', 'AB-INITIO', -1, -1, -1, '0000:00:00.00', '0000:00:00.00', '', '', '', 'NO-CREDIT', '', '0000-00-00 00:00:00'),
(49, 4, 23, 1, '', 'NORMAL', 'NOT ATTEMPTED', 'AB-INITIO', -1, -1, -1, '0000:00:00.00', '0000:00:00.00', '', '', '', 'NO-CREDIT', '', '0000-00-00 00:00:00'),
(50, 4, 24, 1, '', 'NORMAL', 'NOT ATTEMPTED', 'AB-INITIO', -1, -1, -1, '0000:00:00.00', '0000:00:00.00', '', '', '', 'NO-CREDIT', '', '0000-00-00 00:00:00'),
(51, 4, 25, 1, '', 'NORMAL', 'NOT ATTEMPTED', 'AB-INITIO', -1, -1, -1, '0000:00:00.00', '0000:00:00.00', '', '', '', 'NO-CREDIT', '', '0000-00-00 00:00:00'),
(52, 4, 26, 1, '', 'NORMAL', 'NOT ATTEMPTED', 'AB-INITIO', -1, -1, -1, '0000:00:00.00', '0000:00:00.00', '', '', '', 'NO-CREDIT', '', '0000-00-00 00:00:00'),
(53, 6, 1, 1, '', 'NORMAL', 'NOT ATTEMPTED', 'AB-INITIO', -1, -1, -1, '0000:00:00.00', '0000:00:00.00', '', '', '', 'NO-CREDIT', '', '0000-00-00 00:00:00'),
(54, 6, 2, 1, '', 'NORMAL', 'NOT ATTEMPTED', 'AB-INITIO', -1, -1, -1, '0000:00:00.00', '0000:00:00.00', '', '', '', 'NO-CREDIT', '', '0000-00-00 00:00:00'),
(55, 6, 3, 1, '', 'NORMAL', 'NOT ATTEMPTED', 'AB-INITIO', -1, -1, -1, '0000:00:00.00', '0000:00:00.00', '', '', '', 'NO-CREDIT', '', '0000-00-00 00:00:00'),
(56, 6, 4, 1, '', 'NORMAL', 'NOT ATTEMPTED', 'AB-INITIO', -1, -1, -1, '0000:00:00.00', '0000:00:00.00', '', '', '', 'NO-CREDIT', '', '0000-00-00 00:00:00'),
(57, 6, 5, 1, '', 'NORMAL', 'NOT ATTEMPTED', 'AB-INITIO', -1, -1, -1, '0000:00:00.00', '0000:00:00.00', '', '', '', 'NO-CREDIT', '', '0000-00-00 00:00:00'),
(58, 6, 6, 1, '', 'NORMAL', 'NOT ATTEMPTED', 'AB-INITIO', -1, -1, -1, '0000:00:00.00', '0000:00:00.00', '', '', '', 'NO-CREDIT', '', '0000-00-00 00:00:00'),
(59, 6, 7, 1, '', 'NORMAL', 'NOT ATTEMPTED', 'AB-INITIO', -1, -1, -1, '0000:00:00.00', '0000:00:00.00', '', '', '', 'NO-CREDIT', '', '0000-00-00 00:00:00'),
(60, 6, 8, 1, '', 'NORMAL', 'NOT ATTEMPTED', 'AB-INITIO', -1, -1, -1, '0000:00:00.00', '0000:00:00.00', '', '', '', 'NO-CREDIT', '', '0000-00-00 00:00:00'),
(61, 6, 9, 1, '', 'NORMAL', 'NOT ATTEMPTED', 'AB-INITIO', -1, -1, -1, '0000:00:00.00', '0000:00:00.00', '', '', '', 'NO-CREDIT', '', '0000-00-00 00:00:00'),
(62, 6, 10, 1, '', 'NORMAL', 'NOT ATTEMPTED', 'AB-INITIO', -1, -1, -1, '0000:00:00.00', '0000:00:00.00', '', '', '', 'NO-CREDIT', '', '0000-00-00 00:00:00'),
(63, 6, 11, 1, '', 'NORMAL', 'NOT ATTEMPTED', 'AB-INITIO', -1, -1, -1, '0000:00:00.00', '0000:00:00.00', '', '', '', 'NO-CREDIT', '', '0000-00-00 00:00:00'),
(64, 6, 12, 1, '', 'NORMAL', 'NOT ATTEMPTED', 'AB-INITIO', -1, -1, -1, '0000:00:00.00', '0000:00:00.00', '', '', '', 'NO-CREDIT', '', '0000-00-00 00:00:00'),
(65, 6, 13, 1, '', 'NORMAL', 'NOT ATTEMPTED', 'AB-INITIO', -1, -1, -1, '0000:00:00.00', '0000:00:00.00', '', '', '', 'NO-CREDIT', '', '0000-00-00 00:00:00'),
(66, 6, 14, 1, '', 'NORMAL', 'NOT ATTEMPTED', 'AB-INITIO', -1, -1, -1, '0000:00:00.00', '0000:00:00.00', '', '', '', 'NO-CREDIT', '', '0000-00-00 00:00:00'),
(67, 6, 15, 1, '', 'NORMAL', 'NOT ATTEMPTED', 'AB-INITIO', -1, -1, -1, '0000:00:00.00', '0000:00:00.00', '', '', '', 'NO-CREDIT', '', '0000-00-00 00:00:00'),
(68, 6, 16, 1, '', 'NORMAL', 'NOT ATTEMPTED', 'AB-INITIO', -1, -1, -1, '0000:00:00.00', '0000:00:00.00', '', '', '', 'NO-CREDIT', '', '0000-00-00 00:00:00'),
(69, 6, 17, 1, '', 'NORMAL', 'NOT ATTEMPTED', 'AB-INITIO', -1, -1, -1, '0000:00:00.00', '0000:00:00.00', '', '', '', 'NO-CREDIT', '', '0000-00-00 00:00:00'),
(70, 6, 18, 1, '', 'NORMAL', 'NOT ATTEMPTED', 'AB-INITIO', -1, -1, -1, '0000:00:00.00', '0000:00:00.00', '', '', '', 'NO-CREDIT', '', '0000-00-00 00:00:00'),
(71, 6, 19, 1, '', 'NORMAL', 'NOT ATTEMPTED', 'AB-INITIO', -1, -1, -1, '0000:00:00.00', '0000:00:00.00', '', '', '', 'NO-CREDIT', '', '0000-00-00 00:00:00'),
(72, 6, 20, 1, '', 'NORMAL', 'NOT ATTEMPTED', 'AB-INITIO', -1, -1, -1, '0000:00:00.00', '0000:00:00.00', '', '', '', 'NO-CREDIT', '', '0000-00-00 00:00:00'),
(73, 6, 21, 1, '', 'NORMAL', 'NOT ATTEMPTED', 'AB-INITIO', -1, -1, -1, '0000:00:00.00', '0000:00:00.00', '', '', '', 'NO-CREDIT', '', '0000-00-00 00:00:00'),
(74, 6, 22, 1, '', 'NORMAL', 'NOT ATTEMPTED', 'AB-INITIO', -1, -1, -1, '0000:00:00.00', '0000:00:00.00', '', '', '', 'NO-CREDIT', '', '0000-00-00 00:00:00'),
(75, 6, 23, 1, '', 'NORMAL', 'NOT ATTEMPTED', 'AB-INITIO', -1, -1, -1, '0000:00:00.00', '0000:00:00.00', '', '', '', 'NO-CREDIT', '', '0000-00-00 00:00:00'),
(76, 6, 24, 1, '', 'NORMAL', 'NOT ATTEMPTED', 'AB-INITIO', -1, -1, -1, '0000:00:00.00', '0000:00:00.00', '', '', '', 'NO-CREDIT', '', '0000-00-00 00:00:00'),
(77, 6, 25, 1, '', 'NORMAL', 'NOT ATTEMPTED', 'AB-INITIO', -1, -1, -1, '0000:00:00.00', '0000:00:00.00', '', '', '', 'NO-CREDIT', '', '0000-00-00 00:00:00'),
(78, 6, 26, 1, '', 'NORMAL', 'NOT ATTEMPTED', 'AB-INITIO', -1, -1, -1, '0000:00:00.00', '0000:00:00.00', '', '', '', 'NO-CREDIT', '', '0000-00-00 00:00:00');";
   req_insert($sql);
}else
   $avis=1;
   

$sql = "CREATE TABLE IF NOT EXISTS `suivi1_1` (
  `suivi_cdn` int(6) unsigned NOT NULL auto_increment,
  `suivi_utilisateur_no` smallint(5) unsigned NOT NULL default '0',
  `suivi_act_no` smallint(5) unsigned NOT NULL default '0',
  `suivi_seqajout_no` smallint(5) NOT NULL default '0',
  `suivi_etat_lb` enum('PRESENTIEL','A FAIRE','EN COURS','ATTENTE','TERMINE') NOT NULL default 'A FAIRE',
  `suivi_fichier_lb` text,
  `suivi_note_nb1` varchar(12) default NULL,
  `suivi_commentaire_cmt` longtext,
  `suivi_date_debut_dt` datetime NOT NULL default '0000-00-00 00:00:00',
  `suivi_date_fin_dt` datetime NOT NULL default '0000-00-00 00:00:00',
  `suivi_grp_no` smallint(4) NOT NULL default '0',
  PRIMARY KEY  (`suivi_cdn`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";
req_insert($sql);
$sql = "SELECT all FROM suivi1_1";
if (mysql_query($sql) == FALSE)
{
   $sql = "INSERT INTO `suivi1_1` (`suivi_cdn`, `suivi_utilisateur_no`, `suivi_act_no`, `suivi_seqajout_no`, `suivi_etat_lb`, `suivi_fichier_lb`, `suivi_note_nb1`, `suivi_commentaire_cmt`, `suivi_date_debut_dt`, `suivi_date_fin_dt`, `suivi_grp_no`) VALUES
(1, 3, 1, 0, 'A FAIRE', NULL, NULL, NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1),
(2, 4, 1, 0, 'A FAIRE', NULL, NULL, NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1),
(3, 6, 1, 0, 'A FAIRE', NULL, NULL, NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1);";
  req_insert($sql);
}else
   $avis=1;


$sql = "CREATE TABLE IF NOT EXISTS `suivi2_1` (
  `suiv2_cdn` smallint(5) unsigned NOT NULL auto_increment,
  `suiv2_utilisateur_no` smallint(5) unsigned NOT NULL default '0',
  `suiv2_seq_no` smallint(5) unsigned NOT NULL default '0',
  `suiv2_etat_lb` enum('A FAIRE','EN COURS','ATTENTE','TERMINE') default 'A FAIRE',
  `suiv2_duree_nb` smallint(3) unsigned NOT NULL default '0',
  `suiv2_ordre_no` smallint(2) NOT NULL default '1',
  `suiv2_grp_no` smallint(4) NOT NULL default '0',
  PRIMARY KEY  (`suiv2_cdn`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";
req_insert($sql);
$sql = "SELECT all FROM suivi2_1";
if (mysql_query($sql) == FALSE)
{
   $sql = "INSERT INTO `suivi2_1` (`suiv2_cdn`, `suiv2_utilisateur_no`, `suiv2_seq_no`, `suiv2_etat_lb`, `suiv2_duree_nb`, `suiv2_ordre_no`, `suiv2_grp_no`) VALUES
(1, 3, 1, 'EN COURS', 335, 1, 1),
(2, 3, 2, 'EN COURS', 0, 2, 1),
(3, 3, 3, 'A FAIRE', 3, 3, 1),
(4, 4, 1, 'A FAIRE', 335, 1, 1),
(5, 4, 2, 'A FAIRE', 0, 2, 1),
(6, 4, 3, 'A FAIRE', 3, 3, 1),
(7, 6, 1, 'A FAIRE', 335, 1, 1),
(8, 6, 2, 'A FAIRE', 0, 2, 1),
(9, 6, 3, 'A FAIRE', 3, 3, 1);";
  req_insert($sql);
}else
   $avis=1;


$sql = "CREATE TABLE IF NOT EXISTS `seq_duree_ref` (
`seqduref_cdn` SMALLINT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`seqduref_seq_no` SMALLINT NOT NULL ,
`seqduref_duree_nb` SMALLINT NOT NULL
) ENGINE = MYISAM COMMENT = 'Duree de la sequence dans le referentiel de formation';";
req_insert($sql);

$sql = "CREATE TABLE IF NOT EXISTS `message_inscription` (
  `mi_cdn` int(2) NOT NULL AUTO_INCREMENT,
  `mi_variable_lb` varchar(255) DEFAULT NULL,
  `mi_text_cmt` text,
  PRIMARY KEY (`mi_cdn`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COMMENT='Message Inscription' AUTO_INCREMENT=1 ;";
req_insert($sql);


$sql = "INSERT INTO `message_inscription` (`mi_cdn`, `mi_variable_lb`, `mi_text_cmt`) VALUES
(1, 'mess_insc_mess1', 'Bienvenue sur votre plateforme d&#39;enseignement &agrave; distance.&nbsp;<div>Veuillez trouver ci-dessous les informations n&eacute;cessaires pour vous connecter &agrave; votre plateforme de formation.</div>'),
(2, 'mess_insc_mess3', '- Identifiant : '),
(3, 'mess_insc_mess2', '- Mot de passe : '),
(4, 'mess_insc_mess6', '- Adresse de connexion à la plateforme : '),
(5, 'mess_insc_mess4', 'Ces codes doivent &ecirc;tre saisis en minuscules. Ce seront les m&ecirc;mes tout au long de votre formation.<div>&nbsp;Imprimez-les et conservez-les pr&eacute;cieusement.&nbsp; </div><div><br /></div><div>Pour en savoir plus sur &quot;Formagri&quot;: <strong>http://www.formagri.fr</strong>/&nbsp; </div><div>A bient&ocirc;t sur la plateforme.&nbsp;</div><div>L&#39;&eacute;quipe de Formagri. </div>');";
req_insert($sql);

$sql = "CREATE TABLE IF NOT EXISTS `suivi3_1` (
  `suiv3_cdn` smallint(5) unsigned NOT NULL auto_increment,
  `suiv3_utilisateur_no` smallint(5) unsigned NOT NULL default '0',
  `suiv3_parc_no` smallint(5) unsigned NOT NULL default '0',
  `suiv3_etat_lb` enum('A FAIRE','EN COURS','ATTENTE','TERMINE') default 'A FAIRE',
  `suiv3_duree_nb` smallint(3) unsigned NOT NULL default '0',
  `suiv3_grp_no` smallint(4) NOT NULL default '0',
  PRIMARY KEY  (`suiv3_cdn`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";
req_insert($sql);
$sql = "SELECT all FROM suivi3_1";
if (mysql_query($sql) == FALSE)
{
   $sql = "INSERT INTO `suivi3_1` (`suiv3_cdn`, `suiv3_utilisateur_no`, `suiv3_parc_no`, `suiv3_etat_lb`, `suiv3_duree_nb`, `suiv3_grp_no`) VALUES
(1, 3, 1, 'EN COURS', 338, 1),
(2, 4, 1, 'A FAIRE', 338, 1),
(3, 6, 1, 'A FAIRE', 338, 1);";
  req_insert($sql);
}else
   $avis=1;

$sql = "CREATE TABLE IF NOT EXISTS `blogshare` (
`bgshr_cdn` INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`bgshr_auteur_no` INT( 11 ) NULL ,
`bgshr_grp_no` SMALLINT( 5 )  NOT NULL DEFAULT '0',
`bgshr_apps_no` TINYINT( 1 ) NOT NULL DEFAULT '0',
`bgshr_all_on` TINYINT( 1 ) NOT NULL DEFAULT '1',
`bgshr_img_on` TINYINT( 1 ) NOT NULL DEFAULT '1'
) ENGINE = MYISAM CHARACTER SET latin1 COLLATE latin1_general_ci COMMENT = 'Partage de blogs entre apprenants de la mêmme formation';";
req_insert($sql);

$sql = "CREATE TABLE IF NOT EXISTS `blog` (
  `blog_cdn` int(5) NOT NULL AUTO_INCREMENT,
  `blog_auteur_no` int(5) NOT NULL DEFAULT '0',
  `blog_consigne_cmt` text CHARACTER SET latin1 COLLATE latin1_general_ci,
  `blog_grp_no` int(5) NOT NULL DEFAULT '0',
  `blog_create_dt` datetime NOT NULL DEFAULT '2001-01-01 00:00:00',
  PRIMARY KEY (`blog_cdn`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";
req_insert($sql);


$sql = "CREATE TABLE IF NOT EXISTS `blogapp` (
  `bgapp_cdn` int(10) NOT NULL AUTO_INCREMENT,
  `bgapp_blog_no` int(5) NOT NULL DEFAULT '0',
  `bgapp_app_no` int(5) NOT NULL DEFAULT '0',
  `bgapp_grp_no` int(3) NOT NULL DEFAULT '0',
  `bgapp_clan_nb` int(3) NOT NULL DEFAULT '0',
  `bgapp_db_dt` date NOT NULL DEFAULT '0000-00-00',
  `bgapp_df_dt` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`bgapp_cdn`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";
req_insert($sql);

$sql = "CREATE TABLE IF NOT EXISTS `blogbodies` (
  `bgbody_cdn` int(5) NOT NULL AUTO_INCREMENT,
  `bgbody_auteur_no` int(5) NOT NULL DEFAULT '0',
  `bgbody_clan_no` int(5) NOT NULL DEFAULT '0',
  `bgbody_body_cmt` text CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `bgbody_titre_lb` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `bgbody_show_on` int(1) NOT NULL DEFAULT '1',
  `bgbody_img_no` int(5) DEFAULT NULL,
  `bgbody_order_no` int(3) NOT NULL DEFAULT '1',
  `bgbody_date_dt` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`bgbody_cdn`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";
req_insert($sql);

$sql = "CREATE TABLE IF NOT EXISTS `blogmeta` (
  `bgmeta_cdn` int(5) NOT NULL AUTO_INCREMENT,
  `bgmeta_clan_no` int(5) NOT NULL DEFAULT '0',
  `bgmeta_auteur_no` int(5) NOT NULL DEFAULT '0',
  `bgmeta_titre_lb` varchar(150) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `bgmeta_style_lb` varchar(255) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `bgmeta_date_dt` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`bgmeta_cdn`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci COMMENT='Gestion du titre du blogDoc' AUTO_INCREMENT=1 ;";
req_insert($sql);


$sql = "CREATE TABLE IF NOT EXISTS `blogmg` (
  `bgimg_cdn` int(5) NOT NULL AUTO_INCREMENT,
  `bgimg_content_blb` blob NOT NULL,
  `bgimg_auteur_no` int(5) NOT NULL DEFAULT '0',
  PRIMARY KEY (`bgimg_cdn`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci COMMENT='bibliotheque des images' AUTO_INCREMENT=8 ;";
req_insert($sql);
$sql = "INSERT INTO `blogmg` (`bgimg_cdn`, `bgimg_content_blb`, `bgimg_auteur_no`) VALUES
(1, 0x89504e470d0a1a0a0000000d49484452000000780000002d08060000006527af96000000097048597300000ec300000ec301c76fa8640000200049444154789ced7c677414c7b67529814446819cb3316083c1e4680c269b1c4d8e02914148221a934120c064930d180c029311026509a19c731e85913439a7fd4ef78cc0f862dfefbd77d7badf7dcb3f6acd4c774f7775ed73f6d9bbba661800f6c766e29b919a81c1646430c2dc4ce666326a98c924a7fd3266302998de64603adaaf3399a81999d6a4632a8386690c3ada6e627adac73503ed37d2394d4635331ab5740e3d339ae85c50d375b5e66b182cd7f948bffe6efffdf6918d266a3a6a2af380ebb901b76c3399df1b0d4602c80c9a4a07969aadc7d59b49d8e2f91adfcd0dc7a891fe1836e409c68e7a81f9735fc1c3eb296efe1a8f2c818e298d140c3a0a1e0a0a93890359cd9f9b0b283d5d5bcf0500df877fffe0fc5f687f02b0d69cb9946180929a9eb20e7c461b21666aa391a56628b07b77207a76bb8e06b5afc0a5d60dd4ae7102356b7e0f7bfbeda866b703d56c76c3ce6a1fec6d0fa1bacd3efabc13bd7bdec6de3d21c8c85043afe782c56066056a063abf9eaec781fdef1e98ff2beda31b4d94611c551a21a2564ad44a034f99aba56d99696acc9bfb06cd9b5e44bb5617d1b8e10e38d84f8795551fd8d87d019786c3d0a2f538b46e3b11cd5a7c0b47e7f1b0b6194b579a095be6063be681ead5f7c1c1612356bade4576961c5a0d65af96aea1e302e943704da6bfb3f95f0e3007ae41afe107d7c8d555a39ec9350676785f0a3e697913ddbb5f83938b1b7d7b10da75fc16f3166cc78fa76fe1d1233ff8fbfbe3e5abd7f00f78855741afe01f1880874f0271d4c71773bf3b82962de6c0da6e3a6c6d37c09a6d8553bd2d38ea1d06a998aea5e702cbc85fb7aafdbb07e83fbd7d74a38917543aa6a7faaba51a5b5caa6793c6ddc1805e8fd0b1fd66d8da75c4f0e10b70f2a42f5ebe8ca71686b0b0084447c72229211569c9b9c8c92a445e6e01b2b2b2a9e5202d2307f1099978f32605c78edf46bffeee74f5a5b0b5d94160afc6c449c75058a8a1faacffb02f7f83fcaf07d860e4440fa782d5ac30076c78bf47183fea09ead61f8de6ed06e394cf6504bd0c41286567f4db50646626a3b0280fc2f24a26932999562d676a958c9a92a9955aa652e9984ca1601279252b130a9820af0c8909021cf07e0c97268b606deb0a1b6afd06ec42568e1a1a2d97c5c6df01ccbdff1be87f19c09cf0d1531389c146f47f81f1a3efa37a8dae18326c0a1e3ff5873f5170c49b37484b4f4369692993cb954ca326cb6380458c71609078e2d5b89117519cea3618b93a6e600a9581c056b19252210b0a4e45df9e5b50c3663dacad57a0ff90bd2814a8e81819ff1d13679b78a1a7b1d8a7bf81fedf034c40a8298bdc96bdc6a8af9ec3befa208cfb7629fcfcdf2228280cb17109281294402a230f6c3458b2cddcaa32cf0cb096bd57e426be710a99f3c646024bad5652c6ab585a8a06c3871c4135db35b0b67223f17516a5e532a6d19ad53b0f2e7f9ebf01fe5f03cc4d72a869509f3cc9c5171deea349e385e8d673349efbbd4170603492e25351565ac9b424ba0c0613296cf2c4fca4888e6f462e6bf91a6eb15bdc24864967b65c2673561b8d66f1c679618341cb941a1dd92e0dba76d98b6ad6dfa346b5ef70e7d74888a50aa6d31a2ce732fcdb07eb3fb1fdc306233599166c509f5be8d5ed0a6ad6fe0c77eefbe1d5eb40aa9b89a8148a68d08d96cc32fb572e288c1cd0264b0657cd7871fe996f46fe3873169bbf6ba4cc37923ae79a1e2a265569d8f3e7a5a85d7d1b6cad3662c0c0cdc8cc2e8652a1fd5dc018f86bfdbb07ed3fa95940a28cb2f84fae4efa3e16a0639b9ba85e6d3496ba6e27951c82a8e8489495143083564b00bda7495e0471e0192dea9b07436f99ced4f21edac0078005742e8bf9630c3c60e6fa4cb44ddf91ab8c6cf1fc5b7c165b5b4fc68d5f02515656c9f41a93256074e6f3fc7f3070ff29ed1d48552073b43b6756049a363d809a757ae0b747a1080e09427e6e3e341a8919204bbde541e2a8d7c8d132651a796768a50cea7466d4a43383a6908056f2e7e583c2c451b6c242d93a736de62654e89a3a125f3af2c0894915649db6c2c6c61573be3b40f62a132a8565daf4ef69ccff19c00683e1dd2be77d3f69f70875eaaec497fd67e1c5cb40c4c7c640545941d9ade4a9d658257638c08c320256c8a02b24707319e47130565c87a6f43ab415fe302ab3193462da2f65260e705d0629630101ae7a47bde65a6ca671b556c73a753d0d2bab2de8d66d0da2626221aaa0e3f5ef45dabf7bd0fe931ae332d14ccf061e60611958dd9ae750bdfa222c58b41b81c12f90955140d92ba6faace1c1355828d9a893319d3c9b69c56f6090bc8649ea47c0fe0255d67a8812d6429ef32354c504b238834192c1b4c257d0943f82461e09bdaa9c9fb9321939d125e1c59691824ba7039bbfec256cac7f40ad5ad310141a82e2e27418b809101e603dfb6b25fd674150c53a96e0fcd3f6fbe34d1f9c171f9cf7f7c7ffceabff8f4bc847fa62296b1ff4e7a3fdff589fde01ace5b34767a8e4014e8856c2c1fe0cd5c0e9f0d87c0161e144cf8552a6a16cd571809854248a885e09089352c84cc24730e6ec8636df031ac101683276429f3a05a2a7c3204b5a81d2dc2b109585c094fb029ac29d5027af8736ef0a14e24828d42266d4725e57ccb43a52d33a2353d37977edf3478dea07288bbfc683a781c82a48804a45b5dfc065bbf20f03fd61e39f4ad1311cdd1bf4e0bdb999ded514a06ade5b9bbf6ff89366b20839d9fb47a5bc78e41e6daaf812c6951b4e241a0c149c1cb318b9ef29f98731a67782f0afc0347ed00c063d7f7efe1a9ce8a4a0e7b50bf5dfc08f0f777d19dd8b869f17e084ac9e348ec1a4617a83d67c4e53d57975efc42bc78cfc0d7093143aeef92d753c28488afaf52ec2da662276785dc7eb8068dcbd970829d915ad8e6c918e06892e060e5c4918d4057ba18e9f0e49fc5448d297439ab010ea3783507ea7331409635190b405a5d9e761483d49e02f843478124449bb5052f808426911d369b8796f253f25fa2a3003a5a572b67bbf3fead6f2a6f09b865fee6420392d172a8d86d4bb8ee975babfcc60dee651a0e466ab90965c0189486351fadc73679559cdff930c3603ac7af77c9a9bf8e1348a4cae6082423dcb4cd3203dad0c42a1653286179ddc634fbde5fcbf07f09f672faf818c553ac8c434743d0d8d079750465eac920535eacdc140dbb8c7ad9c63315040188cfaf7005baecd051e37c7909494608e4e03dd8496765ebe9e83e68d9ea34faf3030ab61d8e6751557af4660d0c0833870ec36a452ba011d45ab96ea6d4520f4026f287227431bdb1be280be90244d80246e0454015d21bed1049ae4cf509e3411a5c9cb28089641973816954f06a22c6e25b2d36fa24898038592630f13bb7d27139d3f5d85c4d832ecfcc10f9fb4bf8f5a35b6a349830d387b3e0465e51404a4e0f98cfccbc102cb2270d7af7e8e4fda2ec1ad1bfe50abaa8282becf51bc09ffbc71d9acff10e08282721c3d1083b9335fa06d9bd9387fd11732b9d66219cd1edf5c1ef4bf6bff046893d97d180def5bc49b4aecdbf30a3171b9d01b2b08649d791f672da91f196902705ac96c4f0d1fd239574229e3050201c2c2c2b883e494ee4676c247809ad57dd1aa75241a387bc3dab617bcbc2ee2c71fa351db6113366fbd0c41512953970b18c41130e5fb40933a0d9abcced0c5b444c5ddd650c4f7852cbe07248f5b407ab1168cf9ad5011dd0992942150250e853eb60fca7eed86d2e805c848bc8ca2e234c8154a8a58135bbbe1391caa2f40887f093cb60511455fc088a16f61c3bc606bfb2dce11c86209d197e1afe98f03f8e9b32cac58f806ad9bacc6ba7507515454027e1ad55435bbf6d7359c0786a367bd99deb9ba6fa46ce10675fba6448cfb261c2e8de6c173c751e4150960aa5aedc27b7edd1f00d67f04e0aafa6eee0797b55520e7e58a307fee437cf68937faf55b86b28a62622d4efc1af949a58444012e5e7c0885dcc03b0f83516de9ef7b80b960cccfcf47404000d1b341c95e3c11a3aedd5374eb1e8a2d7b33297b6753eb802d1ea771f2641cecedb661ed860b484b0a87bc201826c155e8d3174314de19286b0c6d645d48ced7a70cfd1ccaa40e903e6c0ac93907a0b4212ade384295d209ca948e50847580f0978e1046ce4176f40994d1b994a24ca222355bb3f615ecec96e3a16f06d66f0a812dbb8eae9d42606db59704d70ad857ff0ecf5e1442ac90f0915c456d55af553e9e1381172e2563fe9c3768de782da64e5f878ccc1c18f4263355f32b49f47c5654d5bddfd760f38c9c92cf0c7e1e9c7baa66a0da4bd7e49e877b6e48c1c0feafe0d4f03b6cdcba1b99f92400f5e65acddb41bca75b8e2a397afd0064be0fe640e0f6996ba5d162518dacb0b09275ecb40af5ebcc2780e7233a268e18c8c02f7552eb0d6cffbe97387cf80e0a0b249498741f5cd9a166e468dc42f15c532a952c35351d4c2833b02eed02d0c8310c577de5e8d0cd876adf386a6db1c58b003ef31635aaedc0dab56791127317a2b453d0904a36a40f41e5ebc680b02e54a1b5213e5c1dda440231b915c4be8da03843005738a122a836b4f14da0ca680259603354dea4fd91dfa2287a33cad38e425b720f1a591e5bedf602d5aa2dc1dd3b49705b17043babdba857c7170e0ea76065ed41fd5941b6c90d19394544eb8a0f9e177fe0e3e9e676ef4fc6c2f9f168d7da03fd07ce466252060f82c9a2a2cdf3e526cbc09ae7d2abb6994156bf9b71e3441d776e2d9d5b439f3d2983070f7a0ea74633b1d173277209608ec2392f6fb4ac3fab7ae862a6cb3fd0b4c96c47cd41fac73e981ff23c791905cfad2770fbce7d24c4c710c05afe214d569e0ac3069fc556af73c8ce2e81c1c84d136bcda5e1dd790cef299ffac3bc4f2453765cc384895958b22216d66c170de66858b1cef0f03a8f9367c350b3da566c587d0a69911748206d84387108a9e20ea8f0732410eb4016501ba25dc46829ada1ce6c01c9cd7a501daf06889c50f9d81eaa88fa44e90d207bed0cd1b5065046f74779dc186adf12f01b202f7e04d76577c89a7173d02958b53600d5ac7e410dfbbb70a871055636db88515c299b47c0e7c46f28292927a1a1fb005833d599f5c472d768cc9d938a0eed76a26dfbf1781b9d0eb546665eff65785fa7786a7c476b55b5d002bec1a29eb9ece0340ad1b3868ed9b1359e06f9115c5c66618be701e4e664c3a4d7f18cc00d38771c67f74cfca209f320ff11603e000cef6b2fc72edc717c10d27e0d9daba4ac94e564a74326363b8dca72233b7d260b9d3befc19ad5077980f59c533059be6784257bdf03ccbdb21edd2350b3462096afc984b3e34954b7d949e08ea2d6031e9e1771e24c10d5662f6c743b85acb01310c7cf8530ba05206884cae7b57980a5cf6a40bc95fa9eda14da9c26905fb287e220dd8fc819b29bd6903fad4bc73b41f1da09d2cbf5a04fea02456a5714053581367d342a920e62e9c213b0b79f8e5b94c1abd707c0cefa1a6ad5bc0fbb6ab728837790265844ca7e14faf55d8b94e46cc849d11a2c605451367783c272031b3df615c68e8e45e78e47e0e83802c1c1c99093ea07e90d8d12ac42686225c57a969f27879c84a352aee7b7090a75aca840c65454df60b123dcc3939c2c399e3f4b8050ac61e7ce0a30614c005c1c67c0d3dd077999026260030fb04a6360a9e92578139681d0a01c940bb5165b66b2582773406ad560f13145080a888342ca3903128659427e8d9a44a467c23223cbcf51a3305b429281f62597e3b8770a766ccf42b356abb170c1560407c553a0cbe85803afec8b0a95ac5850c9544a1d1355aa59894041db2a18b3afe68bc60da331f49b20ca101fca60776a23a9067e0ecf6dc771e27438aad9fd800d6e8751fe7a0f2409a3210b6948f595007e580b28af0de9f59a507958c198e40c63a12374a76a40b29bee47d2122aca6cd3216b18a5ce9052c62bced78429b31df4f98d50f6a03a7425ada00d5b8045f3b75229988e2bb7df62cd8640cae0eba8697f9b28fa1665ef7ef2c4dc0a9049a851a33ffc5e86a1b44c627ee851e51fb9ac34720ab40893a73c43ff3e41e8d2f93aea3b4ec4afbffa412c2ea2e334049608de87f2317376303a74da841347ee223e260f7bf76461fc783fb46eb9044f9f8443afe496021b5944a21c53a6de439f6e7bb178fe351c3d5188e99323d1a8e137f0d8bc8f0016839b9c494cae2400fc71e1a750ecd8f114a347bc42b7cf7ec4e59f83a0e3e6d2755ca6aa999cacdeb9f359f8accb56ecdfff08ee9b1ee3fcb90c2c5a7a16b945623cb89385f56b333060e025346d3609d252230b0fcac0cc9914b4e383d1ace90a0c1e3017070e5e230b1986b56ba230695210da75d88c9dbbce229382e1873d0f48bf6cc6faf527c0aa59dd479b563168d2ea1a0de07102770b4fd136ac3bb66df721151d4014ee45b6633d2a82974091d61d92d75ce6d685f03ed5d9f29a50fad841be9e808cad4f9f9da0fede06f2750470610b542eb382720dbdaf7484f481236427488f085d8082ba10ff6a4f41d008e58ffb61c982b5a8556d32ae5d7f8dd5eb388abe8a5a0e77e060cf65f061ea9717b1ca746add71f0f079646509a0d19ad75773f58fb30c3acaba5bb7c2b0675f06faf509408fcf1fc0c9792a4e9cb84e9e55c06799967cf736cf744c9c128e864ddce0beee47e4659763c9e2308c18f91a0d5d16e0d4c9bb10961533a5c6c876ed4f42a74f8e61e5d2f3f0bd978e7e037ec1e0816fe0d2601a3c3df7f0fd100834ecfb9db158b6d40f3f9e0b209da0c1b0817ee8dae5169ab71e8b9cec0218343a7e92e44d941073e63c42f3e68b1018588a878f73d1abe70f183b612792d20b11ec5f8ed933e2d0a3e755d4a9f70d62c2b321ae50b2858b233164d84b02d81533a6ad47645426f2e9ba0b16bdc2d0a1afd0a0f16a6cdd711a39191548cf506140bf6d080ec926806dee93350a8443edb394c127a96d27802710c03db08b003e73e211ec1d56c26bc34cc86387c390dd0495f7ec00593d945dad064d4e0de8dc09c4953650bf262a2e75826c9d2d74b389e5421b4338d11615536c80ec86501d758084cb68a93d054375484fd9c250ea02c19dae58bd7019013c1ebf5e7b04b735feb0631cc0bff2005b5b7b13b0dba8cda2d613abd7ec4562621624b252f3c279aee698cc02e7a44f081e3f13a34fcfc7e8ddd30f4e0da8567a78a3a8a0145c7de5b2dc7d5302867cf5080d291bdc96ef47665a29d15e10faf6bb0fa7fab370dce71659ab2cc4279265591483e62d7761e1e2b58889cdc3f46931e8d6f30eeabaccc066af43c8cc29248688c2f4290f89310e51d08c41627a11664f8b40cf6e8f50dff91bda7f177259396fb38e1cc9c6a00157d0ace578bc0e4a43498582cd987e065f8fda88e8847484058a317e4c283a753e43000f47e0cb4848c572e6b6361e43bff247f3a66e9831c3150949547688ea0f1e0ec7173deea2419355f86ec11664651610f05ab671d305c42711c076367708fdbbb025df69637d92c0f5224a1c4f007f86bddb8ee2bccf1dd4acb908fb368c4365482720a5014a0f7319e902d1015ba26b47e8a73368e75447c5e5fa30c4d6836cb11d4c231944179b433ccc1ee2de56c0d3a6904db582781ebd4f7780fe724d485d19e4a14d50789e04dd822554ebc7c1f7da4d02f819a9e8cb7c06db73196c45be9c6db700dc0b0b166c4374742a841539fc4c0f276a0cfc141fd80f3b831193a844df2fef6150bf301af0c5983bdf03b9d954e3747a5ea56e2180870e7f08e7c60bb16ed50164a59761d1c250f4ebff00f5eb4dc1d16397c98614c19704dfcc595134782bb160e10aa412706bd7a5a1f7e09ba8df700e366d3982acdc423c7f9188f6ed9650f6cf40a3c65f13705198393910fd7a3e83a3cb38fc78ea2c4a4b0b984406e6ea1a8f2f3ebf4ac1350841a1d110c92bd8a5cb011835c61351f14978135a8e312343d1b9cb59d4ae371841afc8ff8ba46c1551f1082aa34d1bbb61eab4a5484e8b07b7eae6c1a30cf4ea710f2ddb6cc1e0a10b919c9289a090745cfbf935f20acbc06cd84dd471bc021bdbcb3490c7f9a5accc6a0c01dc19fbb71dc6852324766a2cc2b1e5a350f6a83dc43fd543f97c0238bb03b40b19c4179a41f63929e8d1f628fb9e44d42f042465ace14b86328f369075af0d7973027523d5dfaed521eee300c32fcda099484131cc1695075ba2e8fbaef09cb8906af004dcbdfa1356ba3d24802fa1768d5fa93cfcc2036c43005b1345932cc4ec591e088f884185289fb2c20c303775c74d777a6e7e8ccc020d79d55b1836281a4d5bacc5c8d1ae484e2a34034cf5dac33d194386ff06a7c673b176d55eca6021162d083767b0e36402f80a0445e5eca47712264d0d4583a64b2900362135a5041bc9260d1a4054eebc141eee87909f930badca48203da06c5f8d53672e232c3403cb16c4e1ab417ea8e73486ce771e25c56524c094149cd1e8d6f9321a361e8c90885048652296965a8e491377213e390d61411518373a82cfe09a75072238200412b198b9ae8ec4f0118104f06acc9ce14640264043f7939ea543afcfefa043fb0368d36e32a2a3d271f1c24b8486e650e91033664b62a646ed2ba450c98e500db62291c5ac47d180b6c7c16dfb71f1c845d476588a836307a1fc7a57947f6707715f02f87a3b68e855be90c4530b0278a0352abe7380703b813ac806fa7694c1a31a40d1c201aaba565077b386c6c50eea2624ac26d782acad0d54adec20faba1e448b3fc5fadeb351cb762aee5c3f4d003f20802f5a00e632f888a5064f2680bb62f66c7744bc8984b0bc989fe1e1ad12d9908202253b74e029012cc6b429cff1d5e068b46ebb039f7d3e13b17199d06854bc95f1d89c6a01780e01bcc702f05b02f801a9ee4904c84d141408b06f6712c68e0b278057d07e4f0258808d9be33074801f1a3b7f07af4d475098954f81a3600ab594151456d031e5888f9561ebe6120c253be5d8602c8e1ebd0a4161250b0b2fc5ac9911e8dce127346cf41542c382219370cb92c0e6cddb8594f46c44045712c06104f069027830825f8711c01202f8ad056037cc9ab6114989a9a441944c4ddf1d3ce05774e9748efa3e1e7e2f12e0b9e90e383129aad030664d03598d7c30b3be4403788206731d653027b2da12c07b71e9c859d4aabe187bbef80ca2a39f42daaf3a14ad195493ea404d201a3fb586b239836c8815e48319cae7b581ea133b181d29439b525d76b686aea61581cca071b682be8e15b48de8fb0d19d44e0c8a26f4bda12de1da6a2aea5bcfc2ad6b3fc29507f8270bc037a90693c8b2da4ce08ea1d61e0b177a22ec4d284a4b2acd9e136a9e7a4383d3f1f38d10e4148ab066150dc89028b46b7f088d9a8e42544c027961095f073d36a799016e32db027039a9f89877007b1fbb86fcc242786d4cc4e851d104f0722ce6012ec6e64d540b0771ce63123c377b239f0494562f218b0476efd75c7c3362378ef9bcc4c471fee8dfff1eea3a7300ff0c4181883d7b96851953e3d0a9fd2933c0c1115048cc3e3b2da30045743fa141e518372688003e859a758622f8d55b482ae5ccd52d06c34904366dbc12b3a6ba23853488ce58cecfae2d5f1286cfc831383b4dc3c58b7e58b5fc3cf20b0a21932a19b3b13a47f47c8340fd8900f6a1b69e06712c01dc0e87b6ff804b477f445d8745d8d9ae3d641e6da16867034d2b06653b1256f46a6c690f55470689671b287b5b13d0f5a123600df5ad60aa4dc7b9907d72b181a9a92d1414088626d6d037b685aa253907ca7c1dbd97f670c69a9633e16c43005f3961c9e04b541aeea1bafdcfb0b23940006fa07e8da4d68268723f222283515a2c6346eeb12524fc24c7e59f9e23242c13825229dbbf27165f0d8944974fcfa25efde178fae22514ca0afe79b3c7260ee047168089a2532b08e05802f821ea3b4d84f7f19f50505882752bd2f0d5576fd0b0d9422c99ef85f4e4326cde48d9df3f080d1bcec016f783c8c9cd86daa027f55e8641037fc2b41947f036ba1263463f41dffef751d769027c8e51061754b007f75330657c323ee9781c8d9b0c2780a3a1102bf9478e3a838cc9945a16f9a684000e44a74f4fa1569de15483e320ae54f2007f3d2280005e45368d004eca82de28e045e3c10311e8f5d93d346bb2149e5ee7b16c21f52b2f191ab58c28dae630817a9b28fa3cd991fd3480bbe833658a753b1cf1da8bcb474fa089dd12ec6edb05c6310da06c4399b7c01eb26ef43a86e8b72f51ed2207685e368074781d28bbd680e6731bc83ad1fed644db635da0eb658bf2e90d21ffda06da1176289e5b1beac54e281f4281b1a0012a473b63558799a8673b1e4f2e1ea34127d1c76e907a7f015b874bd497ddb0b159417d1b40ef9d70ead40d44be0d474599d43cef0b313f45b973fb6fc8c895412812b3cb9763316cc01b7cdeed261c9d46e2d2a5473450323ee33ddc49450ff58373d3593cc059c912a2e048f4ee739f7cf324f878ff4c0057b265ae61e83f3c88176acbbfdb868cd42c6cda48c131c01f8d1a4c8297bb37b2b38b49d489e1ee9e82d6add6c275c51ec4451763daa410f419f01c755cc6e2c4f1e328cd13b1fb0f1231627424f9efc368d46814011c05a558cd97192d59288d49c9222244183326025d3ea1c4aa391201af63e97e54cc6d7530460e7f8dc68d3692c8f240726a12745a2d094b1309ad147cf9d963746c7b06c346b8c1cb6b3f72730b68bf9eb1ba7577d2c09d236a3e052bdb03f47e3b65ef681a48a2e8ad3b70e5e84134aeb618dbbb7584a4bf03a4bd296baf3646e524a2d7fd2d50410a5a7da03a79defa10cf7580b83f83746d732849454b67d442994f27a8a732541e6d04c5ceda90ad229b14d50cc2332ea83c405efa5a2b541eff124b872c865d8dc9f8f5ca79b8b9dd21806fc2a1e643d8d63a4decb287fa3591fad88906e60b3c7e1c8cd8d818529732fe39a981bc30f76465ff0fa1c8ca57f04f9d5e055462c2d83452d30fd1a0e158ecdefd238a8b4bf89925f72d711848de910778f5766410452f59641659f529e38e1d3d0341712529d730f4fbea2501bc00cbbedb81f49402acdf90423ef8291ab94c83d7e6e3c8ce29c7854b09249e228876e763f5eafd749c90ea64087a0f788ada4e53e073f4124a72a5ecf9cb2c0c1a1a8f0e1d7dd0a821011cfa1a4a69053f5f7dfd4a3c540456e4db0a8c191b844f3bfba04ead1178151885520a02b7d51144ff0168d4c415d3676e4652720ad472b28664fd3272d5f8a2ab2f3eef721d8e8d86e1c4c99f909f570c6e22884d9ff933d1dff73480272983bd0958770278149fc1bb776cc525effda85f7d010ecfec85f291b55039df01b2978d21395b1fa25f9aa0fcb003e4371dc817d70dc9ac6e00000dd24944415487fa7b6ba8dd6c5072ba2565b535c4679ca0f6275fbc996a6e8833447728c3afd700248ea8f023d5fdd2199af8ba903eed8ab5f3a8f6d79c88eb57af1045fb82137f0e3509e81a4789513612b87d09e886983a75235ef805232d2d153299cc3cd1c1fdf8dc6860ae8b7fc141ef6bb87cf919bc8f8661dae40c0ce8fb8a06653656acdc8682a23c7073d5ee9b533070d05b1a8c25144cdb919941226b6114faf47e4e35782a7c7c4e934d12314f8f58f419f88c2ccd3c2c9de789f4d45c2a0f71183cf8311a38cf81c7661fcae04a6cdd1681716343d080007673db87f4f45252ba7ee83df01e013c1ddede37515228626fe34ad1ab6738017c82001e4b22eb1514722ee8d46ceb467f14099524c4ca317a5c30017c9c001e8657416128231a5fe51a8b11c303d1b8f9024c9eee8694943c78ef794434ac630a027248df07a4ce2f90771e8c3b771ea1a8b014dcc40a7b15988f6ad55c69108fd120920fb6da846a7cadeb886d3b7ec0c543a748ec2cc731f7c1285d5007d2eb0d200c23cb13e6840a7fcac2a704d64b27982aeb4179d311d2fd94b5be8d20db60077d702da893eba0e21037abe50c45741d881fd784b1b216ca03c93ec539d176fa4e5027b8cd59011b87b1b875f10601fc9207d8dee157d8da7d4f013891fad30af6764d71f9d27d0407871185e642a7d3f2f6889bcbcdcb9362abc71df83e08235f9a8c7bbf15e28b1ebee8df3b044d9baec5a4296b49a52683fb55853b51f4c0819104f052b8adde8af4cc622c5f1e4535f4251ceb4fc5311259457932e67328163d7a3c24801791c85a4fc065c0c3934416d17b03e779d8b2f91032b3cab1745118860d24bfdc683156add983d4ac42b2727ee83ff80e6a3b4f20d17603f9f9651008d5ac47f7dbe8d4e118013c0621548365b24aa6526b99fb867bc82e10126d4b317a742c3eed7416756a13c0c1012823a5bd626902b98270346eb61cd366ad415c5c01766d7a4cfe5acc1406055b342f141dda1e409366e3e0ef1f8af2f24afec105d390cc9e3deb22ff636d6b2b52abd6eb891e47922df914db761ec7b983beb0afb60abbddfb4172ac1574913550fca60e8cb935210cad07757c6d5406d687b1a83ed56127884eda431242af3b6c80bcbad066dba3e202f9e0b226d067d441f9f39a40713d543caf05536c5318525a41103c1caed3dc49cd4fc1cd73cfb06a6528b1082968bbeb14740b28f8bea0d6007366ba12782f101b130391b8946a97790e9a9ba2f47f4e0afae770a46654a0a44cc20acbf4ac5fdf1b183a30022d9a7a91a25d8ab8a46428355ab6657310060cf48363e30558e5b6839fc070f748c070125e8ef5c7e2b03789a2fc72dcbf9d8c9eddefa051b355584cca3d35351f7b7e2820654dd9ea3c9b44d61eaa7542ccff2e04fd7bbd858b0b01ecf60352332bb06271129fe9755d26e0c8b15ff0fa650a141a13fb66c465aa9547d0a8f148040726422e53b00c6210ef63bec8e55661844830e69b6474ed44d9586b0802c3832094c8d9964d4918f825f9ef46eb317dd646c4c709b063c35d0a9c52288c325e68b56fed49656629517f24a45229ff008697e8b9d91a427f2b01bc86c4d6521acc6f08e0cef0da7606a78e04a396bd1b3cd60c84ec69372083008ba90d88493845123517d4a06cac0b63963310e702e14f7690c710ed9eb5030424b8d2aba1f25e0d0a88a630e5d647e58b6a3009eaa0ccaf1634099fa0387c00625f6fc082693eb0ae360f572ec4511d0b863d237ab6fe9e9fd8b0b26a4b3e6f007c7d1fe075b03f0d6a36795aa9f9a13a01cc3dab3db4d71fbf3d0ca7da29a79aa6666aa2a769d3ef53bdf4a58cf140bb3693f9d92f854acd0eed0b458fee37d0a0c972ac5d790e319112b8ad7a83fefd7e8673bd713874f43484a5f92c374b82ae5df6934d5a8c25a44c136244d8b43e85d4ec5d383b8fc176cf43c8ca2c81a747303efbf4269a375f8a95aefb280025983931007dbfb8059786d329607c89e28f412ce61e373e41db96dbd1acd97062a228da26633f5f0dc453bf04e4179721f24d19be1efe92bcf25e38d51d8080c010945306efdb1b822f7b5c2315bd8e00de82c78fdec267ff7d2a251550eaa5ecfe6f4968db6a1566cee466f912687c346680c1539c8945478bc9332e264b32966af16854b7ea4292db07c74f84930f9e839d6bbe4559707f68539a431ae302a3b81a44d1b58012475486b84095d806fa54125db71da04cae8bf2bb047e1e8198ec88b2178ed0257e02752a29e9a03a3008a87607d2f7627a21236031d1d255cc98740a36f69371ed5a1cd1e673bafe3eeac704ea4f03ba59aa2b371ee2c5cb67884f4d20355c6e5e2d4137a0278053330418dcdf8beae301949488f87f09e016adad5cf51b9ab75c41607c0dc73a7d10119ac2ff60eeb7fbb168d972331a349e89c1bdd663f11c6f74efbe8314ea0a38d61e80a33f5ea09a5904a3cec0e62f3c41543e0a03faadc46ad7d31834988e6b3117b5ebf4c056aac1f924b29ef8c5a34dfb55649d26a34b87719836651b268c3b86d62d5791451b896f462fc5ccc93b9043999d9e2c4697ae0ba9d6f7c3f367414849ce27a17796e8598912a18abd8dcc43c7ceee94e1df525fba21c03f0a956205f37b9d8a366d96124b4c46ff010b3163fa2a84052511c0954c6d54b1942c315ab59a476ade07c924c0f47aad655525ff331339bfe03c295d8ad9f38fa0461d6e2eba0d017c10de675ee1f34ecbe17b662f7223be8538a61f94115d21a39a2a8b6e0865766b94867441e5db3e90c47786f47913e8d21aa3ec553d98329a4212dd1625416d2109ef4f01d111b2e0f650657e0279786708c24622fdd5f7888d0ec2e5d3d168d17a3ceede0ec046a21e1babf1a85db73be62d70c5c37b2f88e2021197904c4244405e5665596d410013c8172ef8c26bcb75aaa3db71fdfa0d68e47af6d22f0adfff701bae6b4e62c1924d6483b662e3badd28292ee56b9ec7ce9fd0add728aa9db389351ec06bc7452c75dd8b458bdcb06aed362446e5c3a031b23c81844d2501d8f38b99d8b7e71abc4ffd8cf9cb7761e9f22d705bbc07affdc220d1c8d88973f7d1a7ef420cf8722e4e9eb883d7410998396733baf79c84f11367e3d993b724c832a0d3c859784c32a64ed94aec32016b48942591bf4ecf29214f2dc2d953bed8e8f113162df5c4e2f91be1b1e1008442295352121ef6b94d143c07bd7bcfc281fde7909595860aa18c69094325d9bf4f3f59888b971e10c3e58203975b0e647e080df312126eed71a1a09c8547a592f4ff05bebfbd4454741af23373911dff1869a107901dba0125215350103e1c45e1df203f7c027203e973e0789486f7813cba134c545745218de9fd67288b180441c83710d07e41d0380843c650900c4049c4286406bb2225e434f9d030088b842c37b718898929080f4bc493c7a14457f1f07f1580e0a0102453fd2c2b2b656a8dea834573fc63429d911593adc948cf06b7d84c2a95f3de522456b1ec9c42a4a567223d2d0b19e9593458e5fcea4cb942cd32b37210f53606090994094525c8cb2be485544646363f77acd5ea787f2d124b5972720659b34402211f790545a4e2e9b8f41c140b4a9852a5644a0a9adc5c019d2f9e0450228a04c52474442c2525035151d1484d49273a1699d73d733f30104a584a721a9d270b79f98564eda4bc1bd0a80dacb8a482ea7216d25233a804e4f07de6bec3e1939d5d80e8a864aac16928288d878632556b342f469c35e9002222a2c80e0adead76f9604d13d738eeae1055b29cfc22122c7928a0222eae2867e2e2149465f8a324f1360adff82033e47ba4066f4372e06ea4061c24aafd01b9c16b09f439a8881c8fa290f1c87c3d83405c89d4c0f5c808f64056b0177282dd9117ba0c6941eb9018ec8dd4e8474473a9fc0d8a6572565651c9caca44ac9c062097c0e16eb4a0a00012898480d4585646fee3ba626e268b135ddc7aa7dfff14e763adea5eb9e3b9fbe59a799d94f15de3f655adf9e23e73c7a8d5ea77dbcd3ff3d19b859ed1f86e1b774cd5f9b86ddcd222954af5c112a3aa3e57ede302eef7cb8fcc4b8f3ede67eebd52a9a1a0a2be6b4d4ca1e03488892f497bb73f214f5f009148f4ee3a1f005cf59eab6fdcc565721553282942343470dccdc949d2971530715e068ad3df222f3518d9c921c84e88406172248a925e222fe606b2c28f2339e838e283ce2321fc1612231f203df61972935e203ff919f2e27d911ef31b52e25e93074d41b1b08249c9cf71465fcd0fa681d705dc8d2814e6013003a0ff28c07f5c61f9c182f28ffca1cbc716ecfdd5e78f2e54ff27c7fd553f3e76bdaa40fae3f7aab6fffedcdce4865c0e76ee440ebee8b215d76f3e814c65649eee372821c550ab35ff08f00717e32e64b02ce734997f8dcf356ea1989eea924661603292e122b190092b4b584545059388a44c5a49afc22256924bb44ec0e56666222fa78032b08468a38c6849c84495d4ca84ac8c6a617131bd72df23eae154af8effaf2c736dad5afef9fea6abfe3cede300ff7ee1dd5f0dea9f81f9b17ff5f9d8e73fb6aa8cfb7f39cfc756817e6cf9ef5ff58f671e1aab9898224c1fe78b0e2d3d3076c21ab28f29387dfa3eb8c5f9dc1fd9549dfb1f07cb645ecdffee373930afc935703f89e016b8c1bcf649c72f23e5ec88d2fc97857aeea72dd4d47aa6906b2800d44c2ad532a942cf6444270adaaea49aa6d669a9ce18899aa8a650d6aab9f9547e01b7e5c6fef4c75bffbd9f83fc5f6edc4aca84f852746d4f4ea0feb7d8beed26962d3b84b7b109109408de95a83f07f8dd0fb9cdabfcabfe9ec1c037bde5bf2c3fd2aad6e39acc3f2437f07fd560e47f1560b49cc3bce0db12f926f3e7f77ff160fc13808dec6f80ab9ab90e73e3ecbee1023efd7438c68c598ea3de77914da5b3a2b2f4dd2a53aefd1733491e893885c7fd0000000049454e44ae426082, 6),
(2, 0x89504e470d0a1a0a0000000d494844520000021c000000c808030000009410c85e00000048504c5445ffffffcee6ec0000008b0000b48f939b393ba45658c5c9ce931c1dac7376bdacb1c4c4c4ffa5007575750000ff98fb98b9d3ee3333338f0e0ea4595b8c0303bdafb4b081849420219136f0330000098f49444154789ced9d8b92a3380c45c73104c86e6d6df3e8fcff9f0e608321418418b004dc5bd3ed3441962c0e466642e58f8220427fb80380e40a7040a40007440a7040a40007440a7040a4c4c2a15b2995f65bdc2bbdbc978957493ab5e767f9f87f8ba756fc706da4a37541ed2ab970bcb4d3af96f6327c15ddd745b40a8ec67feadafbdd0493c4fe7dee27e97034b3c723d669d4be526d1a23dd3671d29c793a7d981da3348d1e697326daf7ec9eda74e66cb2ccf658dbb41bba264bbb5dea974d47dd6eed5f63ffba0f3189d376c3c0da7a8cba309a66302aedda34319db45d88937438da0c46fdd9569f6b91bad7afee493b0368f356b3dfbd7e95a926c9f52ecd7b764f0787b5a9a7f3aec7bbe9e4def5d5777b6f3b1aeca65ffc3b38ee99d910f58e3b8fa65bdba51b559499368b5514abb8bdae242b66a3fd24170e5b73743348f7aa3e58892d45ea9ff8fee8f677a7ac7dcfeee9e0b0db073d2666c3a8e95e8e777bf5efe04813b3211939705becc6c1a8325b73241d4daaef4c96e4c2e1da24bba7fdc1e9326fd0a9e76cfd186e579a6e8ccd231ef5f8d23822f5d46eaf36ef1bdebbd5f682d4edfc1800eade031c5f6800479c3d92fee00c660ea3eeb2e27ede668ec4cd1c6dc9d1f798b4ab84ae517db7facdf1c4ccd1950b493f95b89963d0fb681932aa395a4e9babdc70bc92241f8efa72fc3057f5b6ac1bd41cb1a90a4627b07aaf392295b99a236e36f73d66a646c8fa52c176dbe1f018552d43ffb657e56a0ee56a0eeb31eb6b8e7838aace41bb7fbf78021c5fa8ab39ea355ebd0ec9ccab466671505f4fea53b69ea2b5bd533080a35b1f983d239dda72d2d83466b6479de9e6b0758deabbb51d75bb99bf46fe6dafc6a4dba95f42d9f54bdbadedd28d2aee0b9a74f05bf7bfe4482c1c21b4d5e5decd0c13bd1f588063ed51ac6b56771f63a2f703ebd27040f3021c1029c00191021c1029c00191021c1029c00191021c1029c0019122e1b80d7e43d71405c7adc5e2063aae2c020e4345f7035d53b39795211cb756ff40e7d24670fc814e28c00191f287635890028e53ca078eb6c050c37214709c527e33c71b2edcc380f610e08048010e8814e08048010e8814e08048ed0fc7cfcf4f909184f27321ed0ec78ffdd95ba1fc5c491bc1518b7000388eab8de098f180cbca61b53b1c98398e2bc00191021c10a9dde140cd715ced0f077458010e8814e08048010e88d40a38cc670529386e93da7e00a1fc5c51fe70cc7ffa7cfa982d3c683f3f8b971eabfc7ce3e810361b3bda090ee2987d71462f1be27a3f5e374724db6ce8c81f8ed165a5957b548a3a66cb1f8afb59b4d77a3f0b1d1dc7664b47fbcc1cb7ff26f4d519bd70e2f87f427bcc50c7b1d9d291503896561cabe1907c9cb9d9001c8bf73c868d1038e696b26be1583ac2d570483eceec6cec74130c7030d95c018ea5daa0208568010e8814e08048010e88942838bebe15ee0bc7d78e84dbece448121cd3d1ee00c7f78e64dbece548101c44b4dbc3e1e148b4cd6e8e3682e3a557caf3ec810e64233a385903da088e57d7ff4ee853b83e365e3354a8e0820dc863fe5c141ce0001c80832138c00138f807c40c47f52cb5ca2bc0217240cc70e445a55551020e910362864377ff0087c00171cf1c554d4695030e9103125073944fd41c32072471b572bb919f21159d4bc0b13f1c83eff0021cac03e28443f722d8001cbc03923773dc6e334fbc51ae679ebaf2b2a17239f77857b0e07c6c28cd0ec8c36851702be05033cfad2ce192ed440b155c201baf2c6c7a9f63fab2023804d8b0c3d1a87a168043a08d0838941adf3ebfcd3cf10638ae064781dbe7126dd8e130254741ed0838186dd8e1f820c0c1680338b64b8be8037d3c38a6ef90020e2136ec3347f1ac5e97b28043880d3b1ce5e037e09065c30e471ed733478c0ffb48b46187c37cd887dcf1b557c011d0861d8e0fc2ccc1680338b64b8be8037d4838f01952b936ec70e4cd2ab640412ad1861d0e3df80d3864d9b0c3819943ae0d3b1ca839e4dab0c3f1418083d106706c9716d107fa7870e4d4ffcae26382126c78e128ca78b2dac0674845d8305f56aaa29ce0e3869943840d7fcdf1fbd42f7cdc869795564779402c5470816cbc9efb5be468291ccd6af6eda126fe99c3c3467470879d39f29799a311e0e0b761af39f27caa26c5cc21c18679b5324906e0106223f43e87a3e450b9141cdcf1e0f82cc0c13a20c0b15d2e05070738b873293838c0c19d4bc1c1010eee5c0a0e0e7070e752707080833b9782833b251c2fbdcacea5e0e04e09c7a172293838c0c19d4bc1c1010eee5c0a0e0e7070e752707080833b978283031cdcb9141c1ce0e0cea5e0e00007772e05070738b8732938b8d3c07113f05093572e0507771638447ca586572e05077716383a400007fb8044c2c1ffc49b878de8e0fc0644c1b1d6d11a38dcf74362e6601d90c09943c2f7ca7ae5527070678143c4f7ca7ae5527070a781a311e0e0b79108c7089443e552707080833b978283031cdcb9141c1ce0e0cea5e0e00007772e05070738b873293838c0c19d4bc1c1010eee5c0a0eee9470bcf42a3b9782833b251c87caa5e0e00007772e05070738b873293838c0c19d4bc1c1010eee5c0a0e0e7070e752707080833b978283031c769f4036c11cf10d68011c5ec1010ed6e0ce0bc7eca7cf6f939a0f77179b608ef8066450d83eb81570cc7e8d97c71077b209e6886f409fd9f00bce1f8ed9c7217d86b8934d3047e71bd046704067d4367040d713e080482d2e48a1eb69f15216ba9e16df0483ae27c00191da000e8f4bcfa735d456361ec17d5ee05166211c854b5ca3f570f884abbe3f6a7e2b278ff47fedc3df2a4812562c3957c3e1bba009330df8cd1cdfda8462e37070787af69ab93d9c04c9bf1f517e336198011931c11166b6f181c3c78fef35cfcb2214b98aad200de3c9b3b83c191c9e368a090edff185c0d0efe4f4350ae1c7ff56e6fa82d4e3ec0cb68a0b7459f75c627eefc6abe6f05ec9e22618440b7040a40007440a7040a400c74954e8e27ba36afe6dc07112e5cffc7ba372fe6dc0710e55a52a3fcc0313d2f36f038e73288e9b7ff5e12e745eb9262e5595db2d6573e1f9cd7559747f693d4f07e03887ea69a36a2e12faa98aa76b8a4a3d2bf332aecb927abfa26dec5f9839aea0a22938f2664aa8da4aa26b54db54768b6541f77f018e2b28d78d7273b8f5b8d1edd5c36ea9e267397e7f4680e30caaccb2a3beb6e8aa9b272a377334b238e4f16f05382ea5b6166d9b979aa3ded8d41c8329e5b77a0ee0985fe0008e33a8ec5b1d371797bea97fead54a59f5381465193b389e58ad5c497ad4ac14e038970007440a704061043820528003220538205280032205382052800322053820527f01f43560e9e3941fe70000000049454e44ae426082, 6),
(3, 0x89504e470d0a1a0a0000000d494844520000001a000000160806000000de888cb50000000467414d410000d6d8d44f58320000001974455874536f6674776172650041646f626520496d616765526561647971c9653c000007264944415478da627cf7fdd7ffbdf7df6e618000c6dffffe737ffefd8f9d9991f18f303bf3cf3fffffff048aff07c9818833df58e5a4793974395899193efe6162f8f2e73fc3b7dfbf81b2cc0c1f7efc6610626762d875fdd1d36a3da1f350331916ec3ce00310402c0c08c0f8e1e75f016651696b535e06865d6f191876df7f76d94d94e9251f0bc38f7f4005473fb32a7d61e6d44c96646310e56061f8f48781e13dd08e5fffd9819682d89c0cbf81ae5978f23b2f92b90c3f7e7c67000820648b984196244a4338fa40a54bd9a574fb0f5f154ad116bd7df9c31fa587cc9c725b4cb9185efd6060d8f78e81e1e52f0686afff7e33f032b132703203d97f217aa519be7fbaf9f425bfbab4f847109f838b9b0120809860b67cfdf38f1dd9154c8c0c0caec20c0ce78f1f963ef0fabbeafeefec72f38db9188021c570f91b03c35ba04f6e7ffdcff0ffcf1f06552e06064ea04920c33e02c55f7efbc5f3e4e56b1eb861ffff33000410dc476c4c8cbfae7f4758f40ce86af5e34081d7771976fee795ee3011621067037281be7803346ceee3df0ca73a0a1818595919829b263054ca30305cfc0c32878181efcbcb5f6fd938b8901d0e1040701fb13032fefbf1ecc1f1a42b0c0c02fbfe3348cf3dc5f0a5218681818797e117e32f864c41605c0023ea09d022a0790cb202cc606f8b4b8a7f7203fa5c07e87e5e66689cbc7dc9f2fde74f96379fbe7040828e97012080589083ca4884e3dbee4737cfe9dcba21f8f8f9537e8e90a83f0fb5adc52e5b4930fc0306d91360fa7b0eb40d18ef0cd992401f4f9acce023c0c067c1f31f6c063733230330d131fcf9fa99e9f75f51e6f75fbeb18bf0f1fc6004265a8000825b74ff371bb7a090909db5ae04c33e66090603d9dbd7a585f8bfd8684988a9b0437cf3f9ef7f86573fff31b0ffffcbf0f72f1343bc0813031f2323c307a0d83d60bc3dfdc1040e2456c6ffffdf7ffcc2f5e6e3270e5529317082000820b8452cbc7cfaee2240c380ce9d69cccfe03ef79da604d7974f136c55c1f22f80e9f6fddf7f40cbfe82b3d5a3df4c0c2fbf0299fffe327cf9fd87e1ed774660826001265da0fbffff67646767fb8d480cff180002086e1137d031acc0e003255350d030dc38c3f0425a858f0d1a8ba07cf203e8a3ff404daffeb330fcfaf99fe1c7afbf0cacff7f3130fffacf20ccc406f6292b50edf72f9f58d85859ffbdf9f08913a4f7fdfbb70c0001044f0cdb4e5f7a7ae90b03c31e6046f5aee80486e52506e11f6fbe98aebdf058a46ff317bdfeb5c078fa078e875f8c7f19fe33ff051606c0f8fa07c2ff19fefef90b0c42a0ef800efaf4fa15fbc7776fd9bf7cffc906290a9818000208eea3686da97be54bb7f39fbf7e539ae1d307065e1d8b1f5c42e2bf191edefa6f2629fa74fb2b76f5afff9919b8811946e61f23c3bc07ef185e7cfeced0a72ec8f0f0fb4f60d0fd6560616567f8cec2ccc0cec9f18789931b39753300041072c9c050e56a78fea589d2f59c0b5f5de605ea73f0b13271b84fdd2068f4fbe9d36439e91b6ddb0ff35ed8b3539a814f8881415e956143821703df9fdf0c8f800ef8fcfd070327030b305ffd61f8fdfb37333f1fdf6f1e1eeedfb038020820148b40405c90ff4780a1d44d093606752650beb8718ae1a7a61af3eb5fbf79b424843e31d95afce7b6f395d9a2c4c870fd2b285f31317c074600070f33032b3031fc07062323170fc3ab8f5f7984f878bfc2cc0508200c8bbefffdcfaacff0e155eda6f32ca74f9f96e1fffde5df9f7f0c4ca7fe08f2a94b19cb4cb736665063fb0f2c21fe333c00daf00198cc79985918b8391819be027dc40c8c8f3f1fdf33fdfbff97f1fb8f9f2cb0380208200c8b3899197f2bf2307fe551117bc4f747fbe7959f0662cbe39cc54085ec1f70a6fdcf70e2fd1f86f77f9819de035dff07185fa0e4fe1f98287e323032bcfbf99be1efb7cf4c6c2ccc7f989918ffc3cc05082026062c809b85e98f8288c0573b439d17f73efd1631e483941ca0d2f9ed4f50390834f02fc8024670d9c6040ce30fc0d878f6e12bc34b6002f9c523ccc4f0f7df7f6666667079fef3c72f0680006261c003defdf8cb2ea9a2f1d16dfbe357cf3e7ce6f97cfb12fbdb27f7b9bf32b1b30b888a7ffbfbe13513c3cfaf4c7f7ffd65fcc9c8c4cacdcfcff0e5d54b063161e1ef6282fc5fa4850541c522c397cf1f19000208af4586c2ecef0d85190ebffbf28de3133703fb672175b6b75ad2e074fbf9fb4f8e3f7f4558befefac52ec8cfffefebaf7fdcccbf7f70b2b3ebfefcc3f0ff330713c30f4921fe6fe0fa495681012080f05a0403423c5c3f4018ca7d0df731d001df7effe17dfbe5bbd0ef3fbffefef9c3f1ebdfffff4cc2bc3cef7efdfefd4d5942045ccefdf8fe9901208088b2081710e4e1fac9fb9ff18f9800ff07608cfd676204b72d80c5ea7f501281270446607b022080188b0f3df88fcfb02fc0720aa5fefff219a891115a7102531b304130b1b000cbb72f0c7ffe42eaf2bfbf7f32fcf8f68d81959d9de1d3fb0f0cff801020c0001b11c57e7b3f58160000000049454e44ae426082, 6),
(4, 0x89504e470d0a1a0a0000000d494844520000001a000000160806000000de888cb50000000467414d410000d6d8d44f58320000001974455874536f6674776172650041646f626520496d616765526561647971c9653c000005cf4944415478da627cf7fdd7ffbdf7df6e61a02198b1798f0f40003131d009000410dd2c0208c0a4b8ac0008c340d064137bb0ffff9f7a10248fda0a016f33bb237fb93db133658c41cb4f0b5dd0055ecd65aef2351b79267785e5e4fa9f4834701c82d0d9d5fe0a20148b02b4a43cf9981918fe03d9309dfffe23e461ba7eff47c87dfdf5e7e31f20cdc5c6c20f127bf28b812166d3e9a769ea12d7c438587fc2f40204108a452baf3f637093e4669013e6871af49fe1f51f90c18c60cbbf805c0c34fdcf5f8843be82bdc2cc0f741b830450ddb92fff197a9f01d5de38c3f84ece950bd92280004289235531018689d7df329c076af8fde70fc3b75fbf19defdfccbf0eef73f8697c0b07afdeb2fc36720ffc3efbf0c4f7ffe66f8c9f09781e5ff1f064120ddf5e41f43dcf5bf0c1314ff33bcba778be7c5e76f82cfbffee081990d1040283eb217e362b0145162287bf68f4194fd3743312f13833c1b03c39bbfff18eefdfccff013e86a16a057fe3383e2ed3f031bd03fbf80be8dbc050cb24fbf19d6ebb1307cf9c7c8f0e2d52b3ea0257c9a227c2f61660304108a8f8021050caaff0c71c2ff197ade7d64487cc7c2f0ea37038330d37f065160e04932ff67106066620039531868e3e39f8c0c8ebb9e30dc3c7f89c15fe80fc3d51f0c600731884a333c7cf791e5cbaf3fec30b3010208c522909af77f18193e035dba548a8fe115f33f068bebbf19967d606660055ac0cec8c8c005b4548c9589e1de772686e8038f18186e9c66b034d662f8060cda4fc054c2c10834849b8f810b981adf7cff090f3a800042b108948a7e0093163bd0a27f2cac0ce57cff19de3dbdc590be682fc3e60f0c0c7c6ccc0c7cc0c05ef8fa2f4370613303cb86850c5ceac60c5c7f7f3288f2b033b033fd63100062067e6106a6ffff7e219b0d104058332c2815713232332cfac508d4c8c22066edc4507efa1383d5a99f0c8157fe33b42cdecec021afcec0004c30bf4f1d601066636550e16264100406ed1f06a091cfee337cfff98b939189f92fcc4c800042490c4c8c90c8fe0b8cd04bc0b839f1fd3f83b1891a4304eb7f86d59ccc0c3baede6378fcee2503232b1b839b910683424c30c3d2a35718765cbac790e8a5c6f0958185e1d37fa045fffe31bc79f791fdddcfdf5c30b3010208c52250460465ca9bdf19188e0233cd3c311606a63fbf199e01232f5a9283214c4295e1ed6f1586e74007b002e38a0ba83bd9489661fa99870c850f981826a83031fc02f9818d1de8d93fcc4c400266364000a1041d3bd037e7bf32302c7dcbc0502ac2c8a0c2ccc820044c002ac0824816180a02c0b8e0fcfb834186f10b8314e30f0681ff3f188404781866f81a30dcfdcdcc907d9f11526afc0506e9d7cf4c8cff21650917370f034000a158b4e6cd1f86fd9f181826cb035328d0b56fff021305308e98199918b8599919f839d9180459981978d9d980c1ccc4c0044c69a240c77c03c6cb54150686dbdf7e314c780134e8c73786ffdcdc0c3f7fff86243260500204108a458fbeff63289000e51106860fc020f8f21794599918de01c3fe15307b3efecbc2f08e9185e1070b27030b3001fc63e760f80d8c2f16266670e9b85e8b8d410e947380a1c0f4ffffff87ef3f09c0cc060820d4387af6f8d094934f05efbe7c237ce9cd072936262686dfcf1f424aed07b720395a540aa2f8d553b081108dc094cc0b3453401418fe9c0c0c1cdc0c1f7ffee216e1e680970c00018462911a1fd767556dd52fbfb4549f6e79f2fed3e30f9f05f8b5b5fe7efff38ffddb17cbbfc0b865fcf0fd3b07d32f6041f7df90e1c79fbf2c1c4c8c7f8091cfc1cecafa0764f1d73f7fd98041fb559287eb1b1f1717bcec070820148b80f50c48e23f2b3058036404ee3d17e2e4fe0aac933efefccdfe47948f898591f1df9b6f3f388159e02f03231323b06af8034ac2bf80852fd3df5fff0419fffc60e1e667646161fdcfccc4c828c1c9f61566364000b1e0aa118195d73f055ececfc4d6a0401732fe6264e664fffff71bba1c2b2b2b03400051ad2a0715ead82c0181cf9f3e3200041800474519eeab6983a30000000049454e44ae426082, 6),
(5, 0x89504e470d0a1a0a0000000d494844520000001a000000160806000000de888cb50000000467414d410000d6d8d44f58320000001974455874536f6674776172650041646f626520496d616765526561647971c9653c000006a94944415478da627cf7fdd7ffbdf7df6e614003ffa1342303e560c3c5db3e0001b89a83140041208cc2ceccaf810545cb0ed1fdafd3a645058588918956dbb66ff13dfc637eed83959e200d76afdcea6c0d89b30bed3b2d1a92c6a15facc61de20d262a2ca02de6ae31b8aa924ec39498a9d446a7cf0cdeab4700b1a0fbe21d0b13db197666915b4fdfcb1b88cab11a492bf2fe04daf0f4e54786c7ef3f307c78f18ae1faada71fac14a51f2b8b8b31323032b2bc6060137ef8e39fe89fdfbf19fe3f7df8549c9de59d8630d73398b92c2cac0c00018462d1172646962b9cac02273eff32dc68acc2c001f4de9d9f40af7f626078ce08741c370b0397083f4391b18a002f13a3c0b7bf8c0cdb3f32304c6ba8636078768f81e1e52306869f3fa47593ca787eebaafeb552147f02331b208058907df3969181fde8874fca6b35e419fefd057a991518941c0c0c425cff19d8ff0b3048fffac550a8cccbf0ebef67064e362e06e58c1aa04bce334848497ff44d4abec3c3c2f4fbeee5f3fc9b66b56aea364e64675014079bcdcac6c6001040c816317efefa83dd54565e881be8930f408b5eb031307c07cadd6061645825cbc4c0f28f8de1c3d77f0c71c79f301cdeb6998199878781cf399821dbcbfeaa2837e7372e56e63f7292e25f4fefdb25c5f8ebeb2f98d96cec1c0c000104b70814d1371ebc144f549667f804b4e4e637a02540c1a3cc0c0c4bd9202a5efd636210cfee6060e7e56610151265080c8b60b87ef52603233333d3a75f7f3878d8593f73b2b2fc7dfef811bfb4a8c857e468010820648b18845859bfb300bdb6e903d04740f7bc03e2595210f9f3c0b8726a9cc520a9a6cea0656cc0c0f6f93303dba70f0cff7f7c6578f8ee93b0baa8d0cb9f7ffeb2acddb44941cfd8e4a9a68cf82798d9bf7efe6000082094c4f0fde76f96e3ef1818eefdfacdf0eed54b8699e6320cdf80bebb0eb4dcbe65328380803883be853903cb57a063813a7f7c78cf7066f90c865716ae62f70c0cc5ae5db8040c824d0cbab1f9affea36541800042b1e8d3f75f5c4fde7c64f8fde737033b132bc33fa0ea395f1818ca3a3b1904d5b51954d4f418fefe6701c627303c59f918be3203bdfcf615c3a33533f91f6de16460f80b7495982c83bda8d82b2626a6ffc866030410133247849be3eb83fb0f18fefefcc990a021ceb01ce8bb86a90b1998b90518443474197e012dfffe1598cc398418b8858171f99f9d81810968e9ff7fa05cc9c0f01598d6798519e485f83ffdfbff1feea3dfc0d40a1040281699cb8b3e7ef6f42983968c34c30ba063579ebdc3f0fee10b061e6923864fcf7e327cbcf796e1d7e79f0c5f5fbd62f8fcfe1bc3c38be78041084cffccac1003387880c9fd0243e7f4b9c6d79fbf1341361b2080502ce26167fb6da9227d870f18a0d73f33309c5e309b41d8d4051877cc0c5fde7c65f8f6fe23c3f76fdf19fe00755dbe769de1c75f1688e14c40c7b373427c07f6c24f862357ef48209b0d104018659da400dfd753779f302c9b3d93814b5d9741445a96e1dd1b6002faf486e1df8f6f0cef80e5d6fb7f7fffd8c9093efbf44990e3c9ef6f620cc0cccb20a3c9206be906cca382afa579d83ef030fefef9fed36776413ede9f2073010208c52246a0c39839b9391edfbdcbc0f0ea3e83b69dfd3beefb17def37ffbcbf6eed90b1e2e5959b6efefdefdd7d5527ac3f3e7cbb74b0f9ef2bb64d5bc9115e07efde3db67665ee6ff3fd98105293f0ff74f115eaeefb078fafde33b034000a158f4e7df7fa61b1f7eaa5fbc78119832641982f5e4af3e7ff781ebf5fb4f9cff04247f7170b1ff651611fb2bc6c3f0e9fef3effc7adaaaaff838587fe84a0abc6660e067f8ffff3f030b3313b08467f9cbc7c3fd1b54b283cc65666161000820148b981919ffbd7fff9ee1d3d12d0c5ade11af65f8b9be2808f27c02e5890f9fbf723c78fe828fe13ffbdf4bf79f0a2929ca7f636162f863a522fd1ce1d07f8c2c68c91a0438b9b819000208c5a2bf40af727e7a7193e1cf2f75661666861fbfffb23032fefdffe5e76fb6fbafde735d3f7f91ff1f272f83a2bac62f1531fe77e27cdcdf50221c8b25300010402816b13233fdfbf6f3372b2330055d3fb45b34fdf10b5711a00ab19b07197e33b23108dbfb3e7331d67d262dc8f7918f8bf3073313e37f626b598000c2487521e6da7798df7afddeb966b93aebc3eb0cac5cc0d2deddeb8dbe87cf4b2116e62f7c9cecdfb980d980144b40002080502cfaf5e72f333f17c7afe8009fbb5656562f3f7effc126252cf0579083ed2bb00affcb068c647095c2c4c8f4ffdf7f502225da32800042b1881d6818b8fe6061fea72e25fa099726c67fa0328734001060004fd364ee9746f1240000000049454e44ae426082, 6),
(6, 0x47494638396114001400e60000379b66dbffed3fa56f3a9e693b9f6a3ea36d389c6747af78e2fff1136e3e42a872deffefffffff4eb77f3ca06b46ad76ebfff66fdfa5379a65399d683ea46e217f4eb7ffdb3699644bb37c76e7ad0962341b784763d19864d2994db67e55c08887fabffdfffe004f27055e30339661f6fffa187444349762c3ffe2a7ffd22b8c590e6839197545e5fff200512867d69c288855e9fff400391cbaffdd7ef1b6116c3d4ab27b31925e78eaaff9fffd43a973146f3f58c38b6edea46cdca245ac75aeffd60046239bffcd5dc99046ae771f7c4bceffe679ebb03da26c41a7710d673884f8bd0a6335126d3ed7ffeb2b8b58caffe572e3a889fdc151bb831c794891ffc7b3ffd9001d0f94ffc90044224cb57d69d89e52bd8453be86beffded5ffea74e5aa065e3123815086f9be00000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000021f90400000000002c00000000140014000007ff800082838306030488038a848417060688040e481405140e03833f1963868e0e05a1a10214048224004b4f555105900c392125210cb539252504126058493c503a140c101031080b01c9010845003257362c11405321103e1d193c3f332fcb2c1337412e1e1b1116182dc72828cacb15000e2723e44ce743471c1c0a3d5d1ccb35000c28704244020709ce6170a70c81128102049c903102610427183200f880c343800509202a387043c40801098cd860f811de0401231b181021a183951719a47020e1e3a3090013463e6880c14090150956747077ac090002021e0ce5d28041001c3bb2f4f8b0a50302040107443d70a04183645f688050b620068415404f0b287870e00391643466b86b41eca7400563b500010122856121422c7841b143102401640fe8d040594382041b329b08234802000372014f50518105950a3060a85021861100099e055d2071c17520003b, 6),
(7, 0x47494638396114001400f7000025845231935f207d4c0024120052295eca91126d3e1a7646004d260c65371d7a4900452200351a379b663a9e69389c6776e7ad3b9f6a3ca06b399d683fa56f3da26c75e6ace5fff229895673e4a93ea36d42a8723ea46e33966150ba82caffe5d5ffea329460ffffff79ebb098ffcadeffef278754e9fff452bd842b8b582e8f5c41a77153be867ef1b67aecb1effff754bf871672423699644bb37c44aa742888554fb88070e0a6065e316ddda330915e74e5aaaaffd564d299d7ffeb91ffc74ab27b82f6ba69d89e55c088d1ffe958c38b0e68396edea4aeffd6f2fff9baffdd3497622a8a5715704045ac7546ad769bffcd035c2eceffe68afec26ad99f51bb837ff2b784f8bd66d59bf6fffa68d79df9fffd78eaafb3ffd948b0792f905d46ae775cc88f80f3b8e2fff16bdba047af7883f7bb62d097dbffed63d198c3ffe2beffde81f4b92d8e5bfdfffe045d2f7df0b594ffc91b78474eb77f0d6738b0ffd75ecc92a4ffd1a7ffd200582b71e2a700562b61cf964cb57d002f1860ce940000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000002c00000000140014000008fe001b081c4890e0838305054e98e0c0818486111c4488d8f0c1c00912284489f266050b2a58caace050a142448515c0d09002c5c8921f2094f470426303c908021dd000c2620a8f0a02344cf961458b97271424481058e10984214b86644001c3c2080b2df4f4d9a001e7032724d208209301c80e212e5c64b0e0a285070a382974e1f1a04709110554dcc83002c20d0b10dc3a680021c80331275e9cc0bb010e1b0810b84008c361428322318e94d892e4448917423cdc313362460c030d2c9a10a0e485880b202e5c106161c895037b0e98903110c30737684254c8209b888d260c3c5449419009882c1f8c2cc040e4c207183b0ef081012040078118409624f940c08f82353ea4d83802a1080a03026ab46990c2c79838e54d20518364068a33368440401e74d4d000061604914302087400051e75fcc142083130c000020960d040002accc1811c066890030924d8d181010c0cb0c00100a496d10c2b383001130a1c20410a3810b0000e07a820d003136840c106146820410432e8508300093461420005352465033284e0dd1718e850504000003b, 6);";
req_insert($sql);

$sql = "CREATE TABLE IF NOT EXISTS `starating` (
`starate_cdn` int( 5 ) NOT NULL AUTO_INCREMENT ,
`starate_auteur_no` int( 11 ) NOT NULL ,
`starate_note_nb` int( 1 ) NOT NULL ,
`starate_date_dt` int( 11 ) NOT NULL ,
`bgstar_body_no` int( 8 ) NULL ,
`wkstar_body_no` int( 8 ) NULL ,
`actstar_body_no` int( 8 ) NULL ,
PRIMARY KEY ( `starate_cdn` )
) ENGINE = MYISAM DEFAULT CHARSET = latin1 COLLATE = latin1_general_ci COMMENT = 'Table de rating' AUTO_INCREMENT =1;";
req_insert($sql);

$sql = "CREATE TABLE IF NOT EXISTS `commentaires` (
`com_cdn` int( 11 ) NOT NULL AUTO_INCREMENT ,
`com_auteur_no` int( 11 ) NOT NULL ,
`com_comment_cmt` text CHARACTER SET latin1 COLLATE latin1_general_ci,
`com_date_dt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ,
`combg_body_no` int( 5 ) NULL ,
`comwk_body_no` int( 5 ) NULL ,
`comact_body_no` int( 5 ) NULL ,
PRIMARY KEY ( `com_cdn` )
) ENGINE = MYISAM DEFAULT CHARSET = latin1 COMMENT = 'Table de commentaires ' AUTO_INCREMENT =1;";
req_insert($sql);

$sql = "CREATE TABLE IF NOT EXISTS `mindmap` (
  `mindmap_cdn` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `mindmap_seq_no` int(10) unsigned DEFAULT NULL,
  `mindmap_grp_no` smallint(5) unsigned DEFAULT NULL,
  `mindmap_titre_lb` varchar(255) NOT NULL DEFAULT '',
  `mindmap_intro_cmt` mediumtext,
  `mindmap_introformat_nb` smallint(4) unsigned NOT NULL DEFAULT '1',
  `mindmap_auteur_no` int(10) unsigned NOT NULL,
  `mindmap_editable_on` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `mindmap_xmldata_cmt` longtext CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `mindmap_create_dt` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `mindmap_modif_dt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `mindmap_locking_on` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `mindmap_locked_on` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `mindmap_idlock_no` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`mindmap_cdn`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COMMENT='Mindmaps' AUTO_INCREMENT=1 ;";
req_insert($sql);


$sql = "CREATE TABLE IF NOT EXISTS `mindmapapp` (
  `mmapp_cdn` int(10) NOT NULL AUTO_INCREMENT,
  `mmapp_mindmap_no` int(5) NOT NULL DEFAULT '0',
  `mmapp_app_no` int(8) NOT NULL DEFAULT '0',
  `mmapp_seq_no` int(5) NOT NULL DEFAULT '0',
  `mmapp_parc_no` int(3) NOT NULL DEFAULT '0',
  `mmapp_grp_no` int(3) NOT NULL DEFAULT '0',
  `mmapp_clan_nb` int(3) NOT NULL DEFAULT '0',
  `mmapp_db_dt` date NOT NULL DEFAULT '0000-00-00',
  `mmapp_df_dt` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`mmapp_cdn`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";
req_insert($sql);


$sql = "CREATE TABLE IF NOT EXISTS `mindmaphistory` (
  `mindhisto_cdn` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `mindhisto_map_no` int(10) unsigned DEFAULT NULL,
  `mindhisto_auteur_no` int(10) unsigned NOT NULL,
  `mindmap_clan_no` int(5) unsigned NOT NULL DEFAULT '0',
  `mindhisto_xmldata_cmt` longtext NOT NULL,
  `mindhisto_create_dt` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`mindhisto_cdn`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COMMENT='Mindmaps History' AUTO_INCREMENT=1 ;";
req_insert($sql);


$sql = "CREATE TABLE IF NOT EXISTS `mindmapnote` (
  `mmnote_cdn` int(5) NOT NULL AUTO_INCREMENT,
  `mmnote_app_no` int(5) NOT NULL DEFAULT '0',
  `mmnote_note_lb` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`mmnote_cdn`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COMMENT='Table de notation des mindmaps' AUTO_INCREMENT=1 ;";
req_insert($sql);

if (!isset($avis) || (isset($avis) && $avis != 1))
   $afficher = "Operation reussie :<BR> Votre base de donnees <strong>$bdd</strong> ainsi que ses tables et enregistrements sont maintenant actives";
elseif(isset($avis) && $avis == 1)
   $afficher = "<strong><font color='red'>Operation vaine :</font></strong><BR>une base de donnees <strong>$bdd</strong> existe ainsi que ses tables et enregistrements<BR><BR>Passez donc a l'etape suivante";
$afficher .= "<p>&nbsp;</p><p>&nbsp;</p><br /><div class=\"le_formulaire\" ".
             "onClick=\"javascript:document.location.replace('$adresse_http');\">".
             "Etape 3 ==>  Acceder a Formagri sur votre serveur</A></DIV></TD></TR>";

echo $afficher;
?>