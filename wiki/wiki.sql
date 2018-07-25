-- phpMyAdmin SQL Dump
-- version 2.6.1
-- http://www.phpmyadmin.net
-- 
-- Serveur: localhost
-- Généré le : Mardi 17 Août 2010 à 16:22
-- Version du serveur: 4.1.9
-- Version de PHP: 4.3.10
-- 
-- Base de données: `formagri`
-- 

-- --------------------------------------------------------

-- 
-- Structure de la table `wiki`
-- 

CREATE TABLE `wiki` (
  `wiki_cdn` int(5) NOT NULL auto_increment,
  `wiki_auteur_no` int(5) NOT NULL default '0',
  `wiki_consigne_cmt` text,
  `wiki_seq_no` int(5) NOT NULL default '0',
  `wiki_create_dt` datetime NOT NULL default '2001-01-01 00:00:00',
  PRIMARY KEY  (`wiki_cdn`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=10 ;

-- 
-- Contenu de la table `wiki`
-- 

INSERT INTO `wiki` VALUES (1, 1, 'Ceci est mon premier Wiki sur cette séquence....', 1, '2010-07-19 14:05:00');
INSERT INTO `wiki` VALUES (2, 1, 'Faites ceci et pas cela!!!', 0, '2010-07-19 15:00:00');
INSERT INTO `wiki` VALUES (3, 1, 'Ne faites surtout pas ça', 0, '2010-07-19 15:20:00');
INSERT INTO `wiki` VALUES (4, 1, 'Choisir et présenter l''exploitation dans son environnement. Présenter les atouts et contraintes de l''exploitation. Réaliser un schéma de fonctionnement de l''exploitation et les moyens de production...', 0, '2010-07-16 15:00:00');
INSERT INTO `wiki` VALUES (8, 12, 'Celle de Gervais...ici et là', 9, '2010-07-23 13:50:20');
INSERT INTO `wiki` VALUES (9, 1, 'Celle-ci constituait un simple essai.', 1, '2010-07-23 15:16:51');
INSERT INTO `wiki` VALUES (5, 1, 'C''est le premier thème pour cette séquence..', 10, '2010-07-23 09:19:50');
INSERT INTO `wiki` VALUES (6, 1, 'Une nouvelle consigne et un nouveau thème pour cette séquence...', 1, '2010-07-23 12:09:22');
INSERT INTO `wiki` VALUES (7, 1, '----------', 1, '2010-07-23 12:47:13');

-- --------------------------------------------------------

-- 
-- Structure de la table `wikiapp`
-- 

CREATE TABLE `wikiapp` (
  `wkapp_cdn` int(10) NOT NULL auto_increment,
  `wkapp_wiki_no` int(5) NOT NULL default '0',
  `wkapp_app_no` int(5) NOT NULL default '0',
  `wkapp_seq_no` int(5) NOT NULL default '0',
  `wkapp_parc_no` int(3) NOT NULL default '0',
  `wkapp_grp_no` int(3) NOT NULL default '0',
  `wkapp_clan_nb` int(3) NOT NULL default '0',
  `wkapp_db_dt` date NOT NULL default '0000-00-00',
  `wkapp_df_dt` date NOT NULL default '0000-00-00',
  PRIMARY KEY  (`wkapp_cdn`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=12 ;

-- 
-- Contenu de la table `wikiapp`
-- 

INSERT INTO `wikiapp` VALUES (1, 1, 5, 1, 1, 4, 1, '2010-07-19', '2011-07-21');
INSERT INTO `wikiapp` VALUES (2, 1, 7, 1, 1, 4, 1, '2010-07-19', '2011-07-21');
INSERT INTO `wikiapp` VALUES (3, 4, 6, 1, 1, 4, 2, '2010-07-19', '2012-07-20');
INSERT INTO `wikiapp` VALUES (4, 4, 9, 1, 1, 4, 2, '2010-07-19', '2012-07-20');
INSERT INTO `wikiapp` VALUES (5, 2, 2, 1, 1, 4, 3, '2010-07-19', '2013-07-12');
INSERT INTO `wikiapp` VALUES (6, 3, 10, 1, 1, 4, 4, '2010-07-19', '2013-07-12');
INSERT INTO `wikiapp` VALUES (7, 3, 5, 10, 1, 4, 5, '2010-07-19', '2013-07-12');
INSERT INTO `wikiapp` VALUES (8, 4, 5, 1, 1, 1, 6, '2009-01-05', '2011-07-23');
INSERT INTO `wikiapp` VALUES (9, 4, 7, 10, 1, 1, 7, '2008-12-31', '2010-12-24');
INSERT INTO `wikiapp` VALUES (10, 4, 7, 1, 1, 1, 6, '2009-01-05', '2011-07-23');
INSERT INTO `wikiapp` VALUES (11, 1, 4, 1, 1, 4, 1, '2010-07-19', '2011-07-21');

-- --------------------------------------------------------

-- 
-- Structure de la table `wikibodies`
-- 

CREATE TABLE `wikibodies` (
  `wkbody_cdn` int(5) NOT NULL auto_increment,
  `wkbody_auteur_no` int(5) NOT NULL default '0',
  `wkbody_clan_no` int(5) NOT NULL default '0',
  `wkbody_body_cmt` text character set latin1 collate latin1_general_ci NOT NULL,
  `wkbody_titre_lb` varchar(255) default NULL,
  `wkbody_img_no` int(5) default NULL,
  `wkbody_order_no` int(3) NOT NULL default '1',
  `wkbody_date_dt` int(11) NOT NULL default '0',
  PRIMARY KEY  (`wkbody_cdn`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=33 ;

-- 
-- Contenu de la table `wikibodies`
-- 

INSERT INTO `wikibodies` VALUES (1, 5, 1, 'ceci n&#39;est qu&#39;un essai', NULL, NULL, 1, 1280322498);
INSERT INTO `wikibodies` VALUES (2, 5, 1, 'Celle-ci et &quot;celle-l&agrave;&quot;', 'C''est mon titre 1', NULL, 1, 1280387284);
INSERT INTO `wikibodies` VALUES (3, 5, 1, '&lt;font style=&quot;background-color: #ff6600&quot; color=&quot;#800000&quot;&gt;&lt;strong&gt;&amp;quot;Pourquo&lt;/strong&gt;i&lt;/font&gt;&amp;quot; donc&lt;br /&gt;aller ici', 'C''est mon titre 2', NULL, 2, 1280387543);
INSERT INTO `wikibodies` VALUES (4, 2, 1, 'Et c&amp;#39;est &lt;font class=&quot;Apple-style-span&quot; color=&quot;#FF0000&quot;&gt;&lt;strong&gt;&lt;span style=&quot;background-color: #ccffcc&quot; class=&quot;Apple-style-span&quot;&gt;pour &amp;ccedil;a que je vais&lt;/span&gt;&lt;/strong&gt;&lt;/font&gt; et je viens&lt;div&gt;En rigolant...&lt;/div&gt;', 'C''est mon titre 3', NULL, 1, 1280387711);
INSERT INTO `wikibodies` VALUES (5, 5, 1, '&lt;strong&gt;&lt;font size=&quot;3&quot;&gt;Je dois le modifier pour voir&lt;/font&gt;&lt;/strong&gt;&lt;br /&gt;Et c&amp;#39;est &lt;font class=&quot;Apple-style-span&quot; color=&quot;#ff0000&quot;&gt;&lt;strong&gt;&lt;span style=&quot;background-color: #ccffcc&quot; class=&quot;Apple-style-span&quot;&gt;pour &amp;ccedil;a que je vais&lt;/span&gt;&lt;/strong&gt;&lt;/font&gt; et je viens&lt;div&gt;En rigolant...&lt;/div&gt;', 'C''est mon titre 2', NULL, 1, 1280397849);
INSERT INTO `wikibodies` VALUES (6, 5, 1, '&lt;font style=&quot;background-color: #ff6600&quot; color=&quot;#800000&quot;&gt;&lt;strong&gt;&amp;quot;Pourquo&lt;/strong&gt;i&lt;/font&gt;&amp;quot; donc&lt;br /&gt;aller ici&lt;br /&gt;&lt;strong&gt;&lt;font size=&quot;3&quot;&gt;Tout simplement parce que...&lt;/font&gt;&lt;/strong&gt;', 'Mon titre deuxième', NULL, 2, 1280397922);
INSERT INTO `wikibodies` VALUES (7, 5, 1, '&lt;strong&gt;&lt;font size=&quot;3&quot;&gt;Je dois le modifier pour voir&lt;/font&gt;&lt;/strong&gt;...&lt;br /&gt;Et c&amp;#39;est &lt;font class=&quot;Apple-style-span&quot; color=&quot;#ff0000&quot;&gt;&lt;strong&gt;&lt;span style=&quot;background-color: #ccffcc&quot; class=&quot;Apple-style-span&quot;&gt;pour &amp;ccedil;a que je vais&lt;/span&gt;&lt;/strong&gt;&lt;/font&gt; et je viens&lt;div&gt;En rigolant...&lt;/div&gt;', 'C''est mon titre modifié!!!!', NULL, 1, 1280399344);
INSERT INTO `wikibodies` VALUES (8, 5, 1, '&lt;font style=&quot;background-color: #ff6600&quot; color=&quot;#800000&quot;&gt;&lt;strong&gt;&amp;quot;Pourquo&lt;/strong&gt;i&lt;/font&gt;&amp;quot; donc&lt;br /&gt;aller ici!!!!!!&lt;br /&gt;&lt;strong&gt;&lt;font size=&quot;3&quot;&gt;Tout simplement parce que...&lt;/font&gt;&lt;/strong&gt;', 'Mon titre deuxième..', NULL, 2, 1280399784);
INSERT INTO `wikibodies` VALUES (9, 5, 5, 'Et c&amp;#39;est la que &lt;strong&gt;&lt;font color=&quot;#800000&quot;&gt;frebend&lt;/font&gt;&lt;/strong&gt; intervient &lt;a href=&quot;http://www.frebend.com&quot; target=&quot;_blank&quot; title=&quot;c&#039;est ici&quot;&gt;:&lt;img src=&quot;http://www.frebend.com/images/fbcmini.gif&quot; alt=&quot; &quot; width=&quot;25&quot; height=&quot;25&quot; /&gt;&lt;/a&gt; ', 'Nouveau paragraphe', NULL, 1, 1280400854);
INSERT INTO `wikibodies` VALUES (10, 5, 5, 'Et c&amp;#39;est la que &lt;strong&gt;&lt;font color=&quot;#800000&quot;&gt;&amp;quot;frebend&amp;quot;&lt;/font&gt;&lt;/strong&gt; intervient &lt;a href=&quot;http://www.frebend.com&quot; target=&quot;_blank&quot; title=&quot;c&#039;est ici&quot;&gt;:&lt;img src=&quot;http://www.frebend.com/images/fbcmini.gif&quot; border=&quot;0&quot; alt=&quot; &quot; width=&quot;25&quot; height=&quot;25&quot; align=&quot;baseline&quot; /&gt;&lt;/a&gt; ', 'Nouveau paragraphe....', NULL, 1, 1280400933);
INSERT INTO `wikibodies` VALUES (11, 5, 5, 'Et c&amp;#39;est la que &lt;strong&gt;&lt;font color=&quot;#800000&quot;&gt;(&amp;quot;frebend&amp;quot;&lt;/font&gt;&lt;/strong&gt;) intervient en regardant&lt;br /&gt;par-dessus sa jambe&amp;nbsp; &amp;nbsp;&amp;nbsp; &lt;a href=&quot;http://www.frebend.com&quot; target=&quot;_blank&quot; title=&quot;c&#039;est ici&quot;&gt;&lt;img src=&quot;http://www.frebend.com/images/fbcmini.gif&quot; border=&quot;0&quot; alt=&quot; &quot; width=&quot;25&quot; height=&quot;25&quot; align=&quot;absmiddle&quot; /&gt;&lt;/a&gt; ', 'Nouveau paragraphe....', NULL, 1, 1280401146);
INSERT INTO `wikibodies` VALUES (12, 5, 1, 'Je dois le modifier pour voir', 'C''est mon titre modifié...!!!', NULL, 1, 1280401202);
INSERT INTO `wikibodies` VALUES (26, 1, 1, 'Et c&#039;est &lt;font class=&quot;Apple-style-span&quot; color=&quot;#FF0000&quot;&gt;&lt;strong&gt;&lt;span style=&quot;background-color: #ccffcc&quot; class=&quot;Apple-style-span&quot;&gt;pour ça que je vais&lt;/span&gt;&lt;/strong&gt;&lt;/font&gt; et je viens&lt;div&gt;En rigolant...&lt;/div&gt;', 'C''est mon titre 4', NULL, 1, 1281971340);
INSERT INTO `wikibodies` VALUES (13, 5, 1, '&lt;strong style=&quot;background-color: #ffcc00&quot;&gt;&lt;font style=&quot;background-color: #ff6600&quot; color=&quot;#800000&quot;&gt;&amp;quot;Pourquoi&lt;/font&gt;&amp;quot;&lt;/strong&gt; donc&lt;br /&gt;aller ici!!!!!!&lt;br /&gt;&lt;strong&gt;&lt;font size=&quot;3&quot;&gt;Tout simplement parce que...&lt;/font&gt;&lt;/strong&gt;', 'Mon titre deuxième..', NULL, 2, 1280405533);
INSERT INTO `wikibodies` VALUES (14, 5, 1, '&lt;strong&gt;&lt;font size=&quot;3&quot;&gt;Je dois le modifier pour voir&lt;/font&gt;&lt;/strong&gt;...&lt;br /&gt;Et c&amp;#39;est &lt;font class=&quot;Apple-style-span&quot; color=&quot;#ff0000&quot;&gt;&lt;strong&gt;&lt;span style=&quot;background-color: #ccffcc&quot; class=&quot;Apple-style-span&quot;&gt;pour &amp;ccedil;a que je vais&lt;/span&gt;&lt;/strong&gt;&lt;/font&gt; et je viens&lt;div&gt;En rigolant...&lt;/div&gt;', 'C''est mon titre modifié...!!', NULL, 1, 1280407537);
INSERT INTO `wikibodies` VALUES (15, 2, 3, 'Avec lequel je tente &lt;font class=&quot;Apple-style-span&quot; color=&quot;#008000&quot;&gt;&lt;strong&gt;certaines&lt;/strong&gt;&lt;/font&gt; choses', 'Et mon premier paragraphe', NULL, 1, 1280408907);
INSERT INTO `wikibodies` VALUES (16, 2, 3, 'Avec lequel je tente &lt;font class=&quot;Apple-style-span&quot; color=&quot;#008000&quot;&gt;&lt;strong&gt;certaines&lt;/strong&gt;&lt;/font&gt; choses...', 'Et mon premier paragraphe', NULL, 1, 1280408919);
INSERT INTO `wikibodies` VALUES (17, 2, 3, 'Avec lequel je tente &lt;font class=&quot;Apple-style-span&quot; color=&quot;#008000&quot;&gt;&lt;strong&gt;certaines&lt;/strong&gt;&lt;/font&gt; choses...&lt;div&gt;et tant que je suis je vais m&amp;#39;&amp;eacute;clater&lt;/div&gt;&lt;div&gt;&lt;a href=&quot;http://www.annulab.com/&quot; target=&quot;_blank&quot; title=&quot;Le site des labos d&#039;analyses biom&amp;eacute;dicales&quot;&gt;&lt;img src=&quot;http://www.annulab.com/fbcrelief.gif&quot; border=&quot;0&quot; alt=&quot; &quot; width=&quot;20&quot; height=&quot;20&quot; /&gt;&lt;/a&gt; &lt;/div&gt;', 'Et mon premier paragraphe', NULL, 1, 1280415675);
INSERT INTO `wikibodies` VALUES (18, 2, 3, 'Avec lequel je tente &lt;font class=&quot;Apple-style-span&quot; color=&quot;#008000&quot;&gt;&lt;strong&gt;certaines&lt;/strong&gt;&lt;/font&gt; choses...&lt;div&gt;et tant que je suis je vais m&amp;#39;&amp;eacute;clater&lt;/div&gt;&lt;div&gt;&lt;a href=&quot;http://www.annulab.com/&quot; target=&quot;_blank&quot; title=&quot;Le site des labos d&#039;analyses biom&amp;eacute;dicales&quot;&gt;&lt;img src=&quot;http://www.annulab.com/fbcrelief.gif&quot; border=&quot;0&quot; alt=&quot; &quot; width=&quot;20&quot; height=&quot;20&quot; /&gt;&lt;/a&gt; &lt;/div&gt;', 'Et mon premier paragraphe', NULL, 1, 1280415755);
INSERT INTO `wikibodies` VALUES (19, 5, 1, '&lt;strong style=&quot;background-color: #ffcc00&quot;&gt;&lt;font style=&quot;background-color: #ff6600&quot; color=&quot;#800000&quot;&gt;&amp;quot;Pourquoi&lt;/font&gt;&amp;quot;&lt;/strong&gt; donc&lt;br /&gt;aller ici!!!!!!&lt;br /&gt;&lt;strong&gt;&lt;font size=&quot;3&quot;&gt;Tout simplement parce que...&lt;/font&gt;&lt;/strong&gt;', 'Mon titre deuxième..!!', NULL, 2, 1280473222);
INSERT INTO `wikibodies` VALUES (20, 5, 1, '&lt;strong style=&quot;background-color: #ffcc00&quot;&gt;&lt;font style=&quot;background-color: #ff6600&quot; color=&quot;#800000&quot;&gt;&amp;quot;Pourquoi&lt;/font&gt;&amp;quot;&lt;/strong&gt; donc comme ceci?&lt;br /&gt;aller ici!!!!!!&lt;br /&gt;&lt;strong&gt;&lt;font size=&quot;3&quot;&gt;Tout simplement parce que...&lt;/font&gt;&lt;/strong&gt;', 'Mon titre deuxième..!!', NULL, 2, 1280473253);
INSERT INTO `wikibodies` VALUES (21, 5, 1, '&lt;strong style=&quot;background-color: #ffcc00&quot;&gt;&lt;font style=&quot;background-color: #ff6600&quot; color=&quot;#800000&quot;&gt;&quot;Pourquoi&lt;/font&gt;&quot;&lt;/strong&gt; donc comme ceci?&lt;br /&gt;aller ici!!!!!!&lt;br /&gt;&lt;strong&gt;&lt;font size=&quot;3&quot;&gt;Tout simplement parce que...&lt;/font&gt;&lt;/strong&gt;', 'Mon titre deuxième..!!', NULL, 2, 1280476405);
INSERT INTO `wikibodies` VALUES (22, 5, 1, '&lt;strong&gt;&lt;font size=&quot;3&quot;&gt;Je dois le modifier pour voir&lt;/font&gt;&lt;/strong&gt;...&lt;br /&gt;Et c&#039;est &lt;font class=&quot;Apple-style-span&quot; color=&quot;#ff0000&quot;&gt;&lt;strong&gt;&lt;span style=&quot;background-color: #ccffcc&quot; class=&quot;Apple-style-span&quot;&gt;pour ça que je vais&lt;/span&gt;&lt;/strong&gt;&lt;/font&gt; et je viens&lt;div&gt;En rigolant...&lt;/div&gt;', 'C''est mon titre modifié...!', NULL, 1, 1280476496);
INSERT INTO `wikibodies` VALUES (23, 1, 1, '&lt;strong style=&quot;background-color: #ffcc00&quot;&gt;&lt;font style=&quot;background-color: #ff6600&quot; color=&quot;#800000&quot;&gt;&quot;Pourquoi&lt;/font&gt;&quot;&lt;/strong&gt; donc comme ceci?&lt;br /&gt;aller ici!!!!!!&lt;br /&gt;&lt;strong&gt;&lt;font size=&quot;3&quot;&gt;Tout simplement parce que...&lt;/font&gt;&lt;/strong&gt;', 'Mon titre deuxième..!!!', NULL, 2, 1281940502);
INSERT INTO `wikibodies` VALUES (24, 1, 1, '&lt;strong style=&quot;background-color: #ffcc00&quot;&gt;&lt;font style=&quot;background-color: #ff6600&quot; color=&quot;#800000&quot;&gt;&quot;Pourquoi&lt;/font&gt;&quot;&lt;/strong&gt; donc comme ceci?&lt;br /&gt;aller ici!!!!!!&lt;br /&gt;&lt;strong&gt;&lt;font size=&quot;3&quot;&gt;Tout simplement parce que...&lt;/font&gt;&lt;/strong&gt;', 'Mon titre deuxième..', NULL, 2, 1281940513);
INSERT INTO `wikibodies` VALUES (25, 1, 1, '&lt;strong style=&quot;background-color: #ffcc00&quot;&gt;&lt;font style=&quot;background-color: #ff6600&quot; color=&quot;#800000&quot;&gt;&quot;Pourquoi&lt;/font&gt;&quot;&lt;/strong&gt; donc comme ceci?&lt;br /&gt;aller ici!!!!!!&lt;br /&gt;&lt;strong&gt;&lt;font size=&quot;3&quot;&gt;Tout simplement parce que...&lt;/font&gt;&lt;/strong&gt;', 'Mon titre deuxième..', NULL, 2, 1281940666);
INSERT INTO `wikibodies` VALUES (27, 5, 1, 'Et c&amp;#39;est &lt;font class=&quot;Apple-style-span&quot; color=&quot;#ff0000&quot;&gt;&lt;strong&gt;&lt;span style=&quot;background-color: #ccffcc&quot; class=&quot;Apple-style-span&quot;&gt;pour &amp;ccedil;a que je vais&lt;/span&gt;&lt;/strong&gt;&lt;/font&gt; et je viens&lt;div&gt;En rigolant...!!!&lt;/div&gt;', 'C''est mon titre 4 et 5', NULL, 1, 1282026065);
INSERT INTO `wikibodies` VALUES (28, 9, 2, 'Je le&lt;font style=&quot;background-color: #ccffff&quot; color=&quot;#800000&quot;&gt;&lt;strong&gt; teste&lt;/strong&gt;&lt;/font&gt;', 'Mon premier paragraphe', NULL, 1, 1282034227);
INSERT INTO `wikibodies` VALUES (29, 9, 2, 'Je le&lt;font style=&quot;background-color: #ccffff&quot; color=&quot;#800000&quot;&gt;&lt;strong&gt; teste encore&lt;/strong&gt;&lt;/font&gt;', 'Mon premier paragraphe !!', NULL, 1, 1282034317);
INSERT INTO `wikibodies` VALUES (30, 9, 2, 'Je le&lt;font style=&quot;background-color: #ccffff&quot; color=&quot;#800000&quot;&gt;&lt;strong&gt; teste ceci&lt;/strong&gt;&lt;/font&gt;', 'Mon premier paragraphe', NULL, 1, 1282055366);
INSERT INTO `wikibodies` VALUES (31, 5, 1, '&lt;strong style=&quot;background-color: #ffcc00&quot;&gt;&lt;font style=&quot;background-color: #ff6600&quot; color=&quot;#800000&quot;&gt;&amp;quot;Pourquoi&lt;/font&gt;&amp;quot;&lt;/strong&gt; donc comme ceci?&lt;br /&gt;aller ici!!!!!!&lt;br /&gt;&lt;strong&gt;&lt;font size=&quot;3&quot;&gt;Tout simplement parce que...&lt;/font&gt;&lt;font size=&quot;2&quot; color=&quot;#ff0000&quot;&gt;c&amp;#39;est ceci et cela&lt;/font&gt;&lt;/strong&gt;&lt;strong&gt;&lt;font size=&quot;3&quot;&gt;&lt;br /&gt;&lt;/font&gt;&lt;/strong&gt;', 'Mon titre deuxième..', 0, 2, 1282058204);
INSERT INTO `wikibodies` VALUES (32, 5, 1, '&lt;strong style=&quot;background-color: #ffcc00&quot;&gt;&lt;font style=&quot;background-color: #ff6600&quot; color=&quot;#800000&quot;&gt;&amp;quot;Pourquoi&lt;/font&gt;&amp;quot;&lt;/strong&gt; donc comme ceci?&lt;br /&gt;aller ici!!!!!!&lt;br /&gt;&lt;strong&gt;&lt;font size=&quot;3&quot;&gt;Tout simplement parce que...&lt;/font&gt;&lt;font size=&quot;2&quot; color=&quot;#ff0000&quot;&gt;c&amp;#39;est ceci et cela&lt;/font&gt;&lt;/strong&gt;&lt;strong&gt;&lt;font size=&quot;3&quot;&gt;&lt;br /&gt;&lt;/font&gt;&lt;/strong&gt;', 'Mon titre deuxième..et demi', 0, 2, 1282058229);

-- --------------------------------------------------------

-- 
-- Structure de la table `wikimeta`
-- 

CREATE TABLE `wikimeta` (
  `wkmeta_cdn` int(5) NOT NULL auto_increment,
  `wkmeta_clan_no` int(5) NOT NULL default '0',
  `wkmeta_auteur_no` int(5) NOT NULL default '0',
  `wkmeta_titre_lb` varchar(150) collate latin1_general_ci NOT NULL default '',
  `wkmeta_style_lb` varchar(255) collate latin1_general_ci NOT NULL default '',
  `wkmeta_date_dt` int(11) NOT NULL default '0',
  PRIMARY KEY  (`wkmeta_cdn`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci COMMENT='Gestion du titre du WikiDoc' AUTO_INCREMENT=13 ;

-- 
-- Contenu de la table `wikimeta`
-- 

INSERT INTO `wikimeta` VALUES (1, 1, 5, 'cei et l''element', 'font-size:24px;', 1280320081);
INSERT INTO `wikimeta` VALUES (2, 1, 2, 'c''est mon titre à Allouchy', 'font-size:24px;', 1280387731);
INSERT INTO `wikimeta` VALUES (3, 5, 5, 'C''est le titre de notre nouveau devoir', 'font-size:16px;', 1280400489);
INSERT INTO `wikimeta` VALUES (4, 3, 2, 'C''est la page que j''ai créée', 'font-size:16px;', 1280408863);
INSERT INTO `wikimeta` VALUES (5, 1, 5, 'C''est mon titre à Safia', 'font-size:24px;', 1280480472);
INSERT INTO `wikimeta` VALUES (6, 1, 5, 'C''est mon titre à Safia', 'font-size:24px;', 1280480530);
INSERT INTO `wikimeta` VALUES (7, 1, 5, 'C''est mon titre à Safia', 'font-size:24px;', 1280481792);
INSERT INTO `wikimeta` VALUES (8, 1, 5, 'C''est mon titre à Safia...', 'font-size:24px;', 1280482085);
INSERT INTO `wikimeta` VALUES (9, 2, 9, 'celui de Cyrille', 'font-size:24px;', 1282034252);
INSERT INTO `wikimeta` VALUES (10, 2, 9, 'Celui de Cyrille', 'font-size:24px;', 1282034347);
INSERT INTO `wikimeta` VALUES (11, 2, 9, 'Celui de Cyrille et moi', 'font-size:24px;', 1282054974);
INSERT INTO `wikimeta` VALUES (12, 2, 9, 'Pourquoi ça?', 'font-size:24px;', 1282055030);

-- --------------------------------------------------------

-- 
-- Structure de la table `wikimg`
-- 

CREATE TABLE `wikimg` (
  `wkimg_cdn` int(5) NOT NULL auto_increment,
  `wkimg_content_blb` blob NOT NULL,
  `wkimg_auteur_no` int(5) NOT NULL default '0',
  PRIMARY KEY  (`wkimg_cdn`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci COMMENT='bibliotheque des images' AUTO_INCREMENT=1 ;

-- 
-- Contenu de la table `wikimg`
-- 

