<?php
/********************************************************************************
 *                             CHEFS DE PROJETS : P.BAFFALIE, S.GRESSARD
 *                             MODULE : package_name
 *                             PROJET : .activeDirectoy_LDAP
 *
 * Description :
 *                             -
 * Environnement PHP   : PHP4 OU PHP5
 * @author                    : nordine.zetoutou
 * @date date de création     : 26 sept. 06
 * Historique de modification :
 *                             -
 *                             -
 * @version                   :        1.0
 *
 ********************************************************************************/
class Lang
{
        /**
         * retourne un tableau des libelles et messages
         */
        public static function getParam($lang)
        {
                //
                switch ($lang)
                {
                        //
                        case 'en_UK' : //array pour l'anglais britanique par exemple
                                //exemple :  $aParamLang['Ldap2Mysql']['base']['nom'] = 'Name';
                                //
                                break;
                                //
                        default  :
                                //module Ldap2Mysql
                                //---------------------------------------------------------------------------------
                                //

                                 $aParamLang['Ldap2Mysql']['param']['dirNotFound']='les r&eacute;pertoires <b>tmp</b>, <b>config</b>, <b>images</b> du module <b>Authentification</b> sont mal d&eacute;finis';

                                //menu liste
                                //
                                $aParamLang['Ldap2Mysql']['base']['lien_1'] = 'Configurer un serveur';
                                $aParamLang['Ldap2Mysql']['base']['lien_2'] = 'Configurer une importation';
                                $aParamLang['Ldap2Mysql']['base']['lien_3'] = 'Importer';
                                $aParamLang['Ldap2Mysql']['base']['lien_4'] = 'Authentifications externes';
                                $aParamLang['Ldap2Mysql']['base']['lien_5'] = 'Consulter';
                                $aParamLang['Ldap2Mysql']['base']['lien_6'] = 'Aide';
                                //
                                //configuration base
                                $aParamLang['Ldap2Mysql']['base']['nom'] = 'Nom';
                                $aParamLang['Ldap2Mysql']['base']['type'] = 'type';
                                $aParamLang['Ldap2Mysql']['base']['host'] = 'Hôte';
                                $aParamLang['Ldap2Mysql']['base']['port'] = 'Port';
                                $aParamLang['Ldap2Mysql']['base']['dn'] = 'Base dn';
                                $aParamLang['Ldap2Mysql']['base']['login'] = 'Login';
                                $aParamLang['Ldap2Mysql']['base']['pass'] = 'Mot de passe';
                                $aParamLang['Ldap2Mysql']['base']['comment'] = 'Commentaire';
                                $aParamLang['Ldap2Mysql']['base']['path'] = 'R&eacute;pertoire de base (optionnel)';
                                $aParamLang['Ldap2Mysql']['base']['updated'] = 'Derni&egrave;re mise &agrave; jour';
                                $aParamLang['Ldap2Mysql']['base']['etat'] = 'base actuellement';
                                $aParamLang['Ldap2Mysql']['base']['login_field'] = 'Champ login';
                                $aParamLang['Ldap2Mysql']['base']['ldap_user_id_field'] = 'Champ unique';
                                $aParamLang['Ldap2Mysql']['base']['test'] = 'tester la connexion';
                                $aParamLang['Ldap2Mysql']['base']['libelleSucceed'] = 'La connexion a r&eacute;ussie';
                                $aParamLang['Ldap2Mysql']['base']['libelleNotSucceed'] = 'La connexion a &eacute;chou&eacute;e';
                                $aParamLang['Ldap2Mysql']['base']['errorMessage'] = 'Ce champ est requis';
                                $aParamLang['Ldap2Mysql']['base']['noBase'] = 'Aucune base configur&eacute;e';
                                $aParamLang['Ldap2Mysql']['base']['libelle_1'] = 'S&eacute;lectionnez une base';
                                $aParamLang['Ldap2Mysql']['base']['libelle_6'] = 'S&eacute;lectionnez un champ <b>source</b>';
                                $aParamLang['Ldap2Mysql']['base']['libelle_7'] = 'S&eacute;lectionnez un champ <b>cible</b>';
                                $aParamLang['Ldap2Mysql']['base']['libelle_8'] = 'Etape';
                                $aParamLang['Ldap2Mysql']['base']['libelle_9'] = 'Charger les champs';
                                $aParamLang['Ldap2Mysql']['serverTitle'] = array (
                                        'ldap' => 'Configuration LDAP',
                                        'cas' => 'Configuration CAS',
                                        'mysql' => 'Configuration MYSQL'
                                );
                                $aParamLang['Ldap2Mysql']['base']['libelle_13'] = 'Ce libell&eacute; existe d&eacute;j&agrave;';
                                //bouton
                                $aParamLang['Ldap2Mysql']['new'] = 'Nouveau';
                                $aParamLang['Ldap2Mysql']['cancel'] = 'Annuler';
                                $aParamLang['Ldap2Mysql']['save'] = 'Enregistrer';
                                $aParamLang['Ldap2Mysql']['modify'] = 'Modifier';
                                $aParamLang['Ldap2Mysql']['delete'] = 'Supprimer';
                                //config importation
                                $aParamLang['Ldap2Mysql']['config_import']['errorConnexionLdap'] = 'la base Ldap semble mal configur&eacute;e';
                                $aParamLang['Ldap2Mysql']['config_import']['errorConnexionMysql'] = 'la base Mysql semble mal configur&eacute;e';
                                $aParamLang['Ldap2Mysql']['config_import']['errorUniqueId'] = 'Vous devez sp&eacute;cifier un champ unique pour l\'importation dans la configuration de votre serveur ldap';
                                $aParamLang['Ldap2Mysql']['config_import']['writeFileMessageSucceed'] = 'Configuration sauvegard&eacute;e';
                                $aParamLang['Ldap2Mysql']['config_import']['writeFileMessageNotSucceed'] = 'La configuration n\'a pu ête sauvegard&eacute;e : vous ne poss&egrave;dez pas les droits d\'&eacute;criture sur le fichier de configuration';

                                //interface importation
                                $aParamLang['Ldap2Mysql']['ldap_import']['libelle_1'] = 'Base';
                                $aParamLang['Ldap2Mysql']['ldap_import']['libelle_2'] = 'Filtre - <b>optionnel</b> - ( |=or, &amp;=et, !=non)';
                                $aParamLang['Ldap2Mysql']['ldap_import']['libelle_3'] = 'Utilisateur trouv&eacute;';
                                $aParamLang['Ldap2Mysql']['ldap_import']['libelle_4'] = 'Utilisateurs trouv&eacute;s';
                                $aParamLang['Ldap2Mysql']['ldap_import']['libelle_5'] = 'Aucun utilisateur trouv&eacute;';
                                $aParamLang['Ldap2Mysql']['ldap_import']['libelle_6'] = 'Ajouter &agrave; ma liste d\'importation ';
                                $aParamLang['Ldap2Mysql']['ldap_import']['libelle_7'] = 'Supprimer de la liste &agrave; importer ';
                                $aParamLang['Ldap2Mysql']['ldap_import']['libelle_8'] = 'Utilisateur s&eacute;lectionn&eacute;';
                                $aParamLang['Ldap2Mysql']['ldap_import']['libelle_9'] = 'Utilisateurs s&eacute;lectionn&eacute;s';
                                $aParamLang['Ldap2Mysql']['ldap_import']['libelle_10'] = 'Associer ces 2 champs';
                                $aParamLang['Ldap2Mysql']['ldap_import']['libelle_11'] = 'Supprimer cette correspondance';
                                $aParamLang['Ldap2Mysql']['ldap_import']['libelle_12'] = 'Enregistrer cette importation';
                                $aParamLang['Ldap2Mysql']['ldap_import']['libelle_13'] = 'S&eacute;lectionnez une importation';
                                $aParamLang['Ldap2Mysql']['ldap_import']['libelle_14'] = 'Appliquer les modifications';
                                $aParamLang['Ldap2Mysql']['ldap_import']['libelle_15'] = 'Consulter l\'aide en ligne pour des filtres avanc&eacute;es';
                                $aParamLang['Ldap2Mysql']['ldap_import']['libelle_16'] = 'Executer l\'importation';
                                $aParamLang['Ldap2Mysql']['ldap_import']['libelle_17'] = 'V&eacute;rifier avant finalisation';
                                $aParamLang['Ldap2Mysql']['ldap_import']['libelle_18'] = "Importation r&eacute;ussie";
                                $aParamLang['Ldap2Mysql']['ldap_import']['libelle_19'] = "Importation &eacute;chou&eacute;e";
                                $aParamLang['Ldap2Mysql']['ldap_import']['libelle_20'] = 'D&eacute;j&agrave; import&eacute;';
                                $aParamLang['Ldap2Mysql']['ldap_import']['libelle_21'] = 'Appliquer un droit &agrave; la s&eacute;lection';
                                $aParamLang['Ldap2Mysql']['ldap_import']['libelle_22'] = 'Inclure les lignes d&eacute;j&agrave; import&eacute;es';
                                $aParamLang['Ldap2Mysql']['ldap_import']['libelle_23'] = 'tri d&eacute;croissant';
                                $aParamLang['Ldap2Mysql']['ldap_import']['libelle_24'] = 'tri croissant';
                                $aParamLang['Ldap2Mysql']['ldap_import']['libelle_25'] = 'S&eacute;lectionnez une configuration';
                                $aParamLang['Ldap2Mysql']['ldap_import']['libelle_26'] = 'Charger La liste';
                                $aParamLang['Ldap2Mysql']['ldap_import']['libelle_27'] = 'Mis &agrave; jour';
                                $aParamLang['Ldap2Mysql']['ldap_import']['libelle_28'] = 'Affichage des utilisateurs inscrits dans la base Mysql mais ne poss&egrave;dant pas de correspondance avec l\'annuaire <b>ldap</b> &agrave; jour';
                                        $aParamLang['Ldap2Mysql']['ldap_import']['libelle_29'] = 'Suivant';
                                        $aParamLang['Ldap2Mysql']['ldap_import']['libelle_30'] = 'Pr&eacute;c&eacute;dent';
                                        $aParamLang['Ldap2Mysql']['ldap_import']['libelle_31'] = 'Actualis&eacute;';
                                        $aParamLang['Ldap2Mysql']['ldap_import']['libelle_32'] = 'vers';

                                //interface de choix du serveur d'auhentification
                                $aParamLang['Ldap2Mysql']['authMode']['libelle_1'] = '<b>Actuellement</b> votre serveur d\'authentification est';
                                $aParamLang['Ldap2Mysql']['authMode']['NoAuthentification'] = 'Serveur interne';
                                //fin module Ldap2Mysql
                                //---------------------------------------------------------------------------------
                                break;
                                //

                } //fin switch
                //
                return $aParamLang;
        }
}
?>