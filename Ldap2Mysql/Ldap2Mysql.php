<?php
/*-----------------------------------------------------------------------*
                             modules : Ldap2Mysql
                             projet : Ldap2Mysql-Authentification
                             CHEFS DE PROJETS : P.BAFFALIE, S.GRESSARD

 Description :      importation des utilisateurs ldap=>mysql et gestion du mode d'authentification
                             -
 Environnement PHP   : PHP4 OU PHP5
 author                    : nordine.zetoutou<nzetoutou@educagri.fr>
 date de création     : 25 sept. 06
 Historique de modification :
                             -
                             -
 version                   :        1.0
/*-----------------------------------------------------------------------*/
/**
   * Classe  Ldap2Mysql
   *
   * @author Nordine Zetoutou  - <nzetoutou@educagri.fr>
   * @package Ldap2Mysql-Authentification
   * @module Ldap2Mysql
   * @description importation/mise à jour des utilisateurs ldap=>mysql
   * @dependance  activation de l'extension php_ldap
   * @dependance  si CAS comme serveur d'authentification activation  de l'extension=php_curl.dll
   * @dependance  module de gestion de langue : Lang
   *
   * @license GPL
   * @version 1.0
   * @date   25 sept. 2006

*/
class Ldap2Mysql
{
        //@formagri
        var $mode_dey = false;
        //fin @formagri
        //@promethee
        //table à mettre à jour
        var $sMysqlTableToUpdate = 'utilisateur';
        //tableau contenant les droits utilisateurs
        //@formagri
        //tableau contenant les droits utilisateurs
/*        var $aMysqlTableUserGroups = array (
                'table' => 'user_group',
                'field_id' => '_IDgrp',
                'field_groupeType' => '_ident'
        );
*/
        //var $dateCreation = '_create';
        //@formagri
        var $champ_motDePasse = 'util_motpasse_lb';
        var $champ_login = 'util_login_lb';
        //masque le select des bases
        var $blisteDeroulanteVisible = false;
        //initialisation du filtre de recherche ldap
        var $sFilter = '';
        //
        var $bCheckbox_includeImportedUsers = false;
        //
        /**
         * constructeur
         *
         * @description initialise la classe avec
         * @param string $slang
         */
        function Ldap2Mysql($lang = 'fr_FR')
        {
                //chargement d'un tableau des libelles et messages selon la langue par
                $this->getParamLang($lang);
                $this->lang = $lang;
                //
                //
                if (!defined('MODULE_DIR'))
                {
                                                define('MODULE_DIR', '../Ldap2Mysql/');
                }
                //

                define('TEMP_DIR', MODULE_DIR . 'tmp/');
                define('CONFIG_DIR', MODULE_DIR . 'config/');
                define('IMAGE_DIR', MODULE_DIR . 'images/'); //



                $this->sDate_version = '@version ' . date('Y-m-d H:m', filemtime(MODULE_DIR . 'Ldap2Mysql.php'));
                if (!isset ($_SESSION))
                {
                        if (!isset($_SESSION)) session_start();
                }
                //
                $this->fBaseAuth = CONFIG_DIR . 'baseAuth';
                //
                define('SPACE6', str_repeat(chr(32), 6));
                define('SPACE5', str_repeat(chr(32), 5));
                define('SPACE4', str_repeat(chr(32), 4));
                define('SPACE3', str_repeat(chr(32), 3));
                define('SPACE2', str_repeat(chr(32), 2));
                define('SPACE1', str_repeat(chr(32), 1));
                //
        }
        //
        /**
         * @description manuel d'utilisation simplifié
         * @todo à complèter ou à améliorer
         */
        function manual_view()
        {
                $file = MODULE_DIR . 'manual/' . 'manual_' . $this->lang . '.txt';
                $content = file_get_contents($file);
                return $content;
        }
        //
        function authModeConfig_controller()
        {
                $fFile = $this->fBaseAuth;
                //
                if (!empty ($_POST['btn_validerSelect']))
                {
                        if (!empty ($_POST['select_listBase']))
                        {
                                //
                                if ($_POST['select_listBase'] == 'NoAuthentification')
                                {
                                        //si le fichier n'existe pas on tente de le créer
                                        $handle = fopen($fFile, 'w+');
                                        fwrite($handle, 'NoAuthentification');
                                        fclose($handle);
                                        $selected_item = 'NoAuthentification';
                                }
                                else
                                {
                                        //on charge les informations de la base choisi et on crée un fichier de config
                                        $arr = array ();
                                        $obj = $this->baseLoadParam($_POST['select_listBase']);
                                        //
                                        switch ($obj->type->value)
                                        {
                                                case 'cas' :
                                                        //
                                                        $port = 443;
                                                        if ($obj->port->value)
                                                        {
                                                                $port = $obj->port->value;
                                                        }
                                                        $arr['id'] = $obj->id->value;
                                                        $arr['type'] = $obj->type->value;
                                                        $arr['host'] = $obj->host->value;
                                                        $arr['port'] = (int) $port;
                                                        break;
                                                        //
                                                case 'openldap' :
                                                        $arr['id'] = $obj->id->value;
                                                        $arr['type'] = $obj->type->value;
                                                        $arr['host'] = $obj->host->value;
                                                        //
                                                        $port = 389;
                                                        if ($obj->port->value)
                                                        {
                                                                $port = $obj->port->value;
                                                        }
                                                        $arr['port'] = (int) $port;
                                                        //
                                                        $arr['login_field'] = $obj->login_field->value;
                                                        $arr['ldap_user_id_field'] = $obj->ldap_user_id_field->value;
                                                        $arr['dn'] = $obj->dn->value;
                                                        //
                                                        break;
                                                case 'activedirectory' :
                                                        //
                                                        $arr['id'] = $obj->id->value;
                                                        $arr['type'] = $obj->type->value;
                                                        $arr['host'] = $obj->host->value;
                                                        //
                                                        $port = 389;
                                                        if ($obj->port->value)
                                                        {
                                                                $port = $obj->port->value;
                                                        }
                                                        $arr['port'] = (int) $port;
                                                        //
                                                        $arr['login_field'] = $obj->login_field->value;
                                                        $arr['ldap_user_id_field'] = $obj->ldap_user_id_field->value;
                                                        $arr['dn'] = $obj->dn->value;
                                                        //
                                                        break;
                                        }
                                        $oBase = (object) $arr;
                                        //
                                        //si le fichier n'existe pas on tente de le créer
                                        $handle = fopen($fFile, 'w+');
                                        fwrite($handle, serialize($oBase));
                                        fclose($handle);
                                        //on charge le serveur activé
                                }
                        } //fin if
                }
                //
                return $this->authModeConfig_view();
        }
        /**
         * @description récupère le contenu du fichier : soit une config d'une base soit une
         * chaine spécifiant que le mode d'authentification externe est désactivé
         * @return object $oBase ou  string null
         */
        function authMode_getContentFile()
        {
                $fFile = $this->fBaseAuth;
                if (file_exists($fFile))
                {
                        $sFileContent = file_get_contents($fFile);
                        //juste une chaine
                        if ($sFileContent == 'NoAuthentification')
                        {
                                return 'NoAuthentification';
                        }
                        else
                        {
                                //alors c'est un objet base
                                $oBase = unserialize($sFileContent);
                                return $oBase;
                        }
                }
        }
        //
        /**
         * @description affiche le mode d'authentification à choisir
         */
        function authModeConfig_view()
        {
                $arr = $this->baseLoadList();
                //
                //
                if (is_array($arr))
                {
                        foreach ($arr as $key => $oBase)
                        {
                                //on prend que le cas et le ldap
                                //
                                if ($oBase->type->value <> 'mysql')
                                {
                                        $aoListeBase[$key] = $oBase;
                                } //fin if
                                //
                        }
                        //
                } //fin if
                //
                $obj = new stdClass();
                $obj->nom = new stdClass();
                $obj->nom->value = $this->aParamLang['authMode']['NoAuthentification'];
                $aoListeBase['NoAuthentification'] = $obj;
                //
                $aContent[] = '<div class="row">';
                //
                $aContent[] = '<span>' . $this->aParamLang['authMode']['libelle_1'] . ' : </span>';
                //
                $contentFile = $this->authMode_getContentFile();
                //
                if (is_object($contentFile))
                {
                        $selected_item = $contentFile->id;
                }
                else
                {
                        $selected_item = $contentFile;
                }
                //
                $aContent[] = $this->buildSelectListeBase($aoListeBase, 'noOption', $selected_item);
                //
                $aContent[] = '</div>';
                //
                return implode("\n", $aContent);
        }
        /**
         * @description  fonction de suppression d'un objet (représentant une configuration d'une importation) dans le fichier import
         * @param objet $oParamImport
         */
        function importConfig_save_delete($objetToSerialize, $sDelete = null)
        {
                //
                $file = CONFIG_DIR . 'importConfig';
                //
                $aObjet = array ();
                if (file_exists($file))
                {
                        //on charge  le fichier texte, chaque ligne est un objet
                        $aObjet = unserialize(file_get_contents($file));
                }
                //
                $aObjet[$objetToSerialize->id] = $objetToSerialize;
                //suppression
                if ($sDelete == 'delete')
                {
                        unset ($aObjet[$objetToSerialize->id]);
                }
                //si le fichier n'existe pas on tente de le créer
                $handle = @ fopen($file, 'w+');
                @ fwrite($handle, serialize($aObjet));
                //
                $bIsSucceed = @ fclose($handle);
                if ($bIsSucceed == true)
                {
                        $_SESSION['Ldap2Mysql']['writeFileMessageSucceed'] = $this->aParamLang['config_import']['writeFileMessageSucceed'];
                }
                else
                {
                        $_SESSION['Ldap2Mysql']['writeFileMessageNotSucceed'] = $this->aParamLang['config_import']['writeFileMessageNotSucceed'];
                }
                //
        }
        //
        /**
         * @description execute les actions relatives aux chargements des champs ldap et mysql
         *  execute les actions relatives à l'ajout/suppression des champs de correspondance
         *
         */
        function importConfig_controller()
        {
                if (!empty ($_POST['btn_addToListFieldsToImport']))
                {
                        //
                        if (!empty ($_POST['select_ldap_ListFields']) AND !empty ($_POST['select_mysql_ListFields']))
                        {
                                //le champ ldap à correspondre
                                $ldapFieldToConnect = $_POST['select_ldap_ListFields'];
                                //le champ mysql à correspondre
                                $mysqlFieldToConnect = $_POST['select_mysql_ListFields'];
                                //
                                $aCorrespondance = array (
                                        'ldap_ListFields' => $ldapFieldToConnect,
                                        'mysql_ListFields' => $mysqlFieldToConnect
                                );
                                //la création d'un id unique par md5 facilite la suppression
                                $correspondance_id = md5($ldapFieldToConnect);
                                //stocke la liste des champs ldap et mysql sous forme de tableaux
                                $_SESSION['Ldap2Mysql']['listFieldsToImport'][$correspondance_id] = $aCorrespondance;
                                //suppression des doublons des champs mysql
                                $session = $_SESSION['Ldap2Mysql']['listFieldsToImport'];
                                //
                                //on regarde si 2 champs mysql similaire existe
                                foreach ($session as $key => $array)
                                {
                                        //
                                        if ($mysqlFieldToConnect == $array['mysql_ListFields'])
                                        {
                                                $keyTrouve[] = $key;
                                        }
                                        //
                                }
                                //
                                if (count($keyTrouve) > 1)
                                {
                                        unset ($_SESSION['Ldap2Mysql']['listFieldsToImport'][$keyTrouve[0]]);
                                }
                        } //fin if
                } //fin if
                //
                if (!empty ($_POST['btn_loadListFields']))
                {
                        //
                        if (isset ($_SESSION['Ldap2Mysql']['listFieldsToImport']))
                        {
                                unset ($_SESSION['Ldap2Mysql']['listFieldsToImport']);
                        } //fin if
                        //
                }
                //
                if (!empty ($_POST['select_listeBase_ldap']) AND !empty ($_POST['select_listeBase_mysql']))
                {
                        $_SESSION['Ldap2Mysql']['ldap_selected_id'] = $_POST['select_listeBase_ldap'];
                        $_SESSION['Ldap2Mysql']['mysql_selected_id'] = $_POST['select_listeBase_mysql'];
                }
                //
                if (!empty ($_POST['btn_enregisterImportConfig']))
                {
                        $ldap_selected_id = $_SESSION['Ldap2Mysql']['ldap_selected_id'];
                        $mysql_selected_id = $_SESSION['Ldap2Mysql']['mysql_selected_id'];
                        //
                        $oParamImport = new stdClass();
                        //on cree un id pour cet import qui sera une date avec l'heure
                        $oParamImport->id = md5($ldap_selected_id . $mysql_selected_id);
                        //l'id du ldap (défini lors de son enregistrement dans le fichier de config)
                        $oParamImport->ldap_id = $ldap_selected_id;
                        //l'id du mysql (défini lors de son enregistrement dans le fichier de config)
                        $oParamImport->mysql_id = $mysql_selected_id;
                        //
                        $oParamImport->mysql_detailedListFields = $_SESSION['Ldap2Mysql']['mysql_detailedListFields'];
                        //
                        if (!empty ($_SESSION['Ldap2Mysql']['listFieldsToImport']))
                        {
                                $oParamImport->listFieldsToImport = $_SESSION['Ldap2Mysql']['listFieldsToImport'];
                                $this->importConfig_save_delete($oParamImport);
                        }
                        //on efface de la session la dernière récupération
                        $this->delete_var_session();
                        //return;
                }
                if ($_POST)
                {
                        //
                        foreach ($_POST as $key => $value)
                        {
                                $array = explode('_', $key);
                                //suppression d'une correspondance
                                if (isset ($array[1]) AND $array[1] == 'supprimerCorrespondance')
                                {
                                        unset ($_SESSION['Ldap2Mysql']['listFieldsToImport'][$array[2]]);
                                        //on sort de la boucle si trouvé
                                        break;
                                }
                                //
                        }
                        //
                }
                //
                return $this->importConfig_view();
                //
        }
        //
        /**
         * @description affiche le l'interface de la config
         */
        function importConfig_view()
        {
                //
                $aContent[] = '<div class="row">';
                $aContent[] = '<span class="etape">' . $this->aParamLang['base']['libelle_8'] . ' 1 :</span>';
                $aContent[] = '</div>';
                //liste de tous les serveurs
                $aContent[] = '<div class="colonneGauche">';
                //on récupère toutes les base mysql et ldap
                $aAllBases = $this->getAllBases();
                $aLdapAllBases = $aAllBases['ldap'];
                $aMysqlAllBases = $aAllBases['mysql'];
                //liste des serveurs ldap
                $aContent[] = '&raquo;&nbsp;' . $this->aParamLang['base']['libelle_1'] . ' <b>LDAP</b> <br/>' . $this->formSelectComponent('select_listeBase_ldap', $aLdapAllBases, array (
                        'style="width:200px;"'
                ));
                $aContent[] = '</div>';
                //liste des serveurs mysql
                $aContent[] = '<div class="colonneDroite">';
                $aContent[] = '&raquo;&nbsp;' . $this->aParamLang['base']['libelle_1'] . ' <b>MYSQL</b> <br/>' . $this->formSelectComponent('select_listeBase_mysql', $aMysqlAllBases, array (
                        'style="width:200px;"'
                ));
                //
                $aContent[] = ' <input type="submit" name="btn_loadListFields" value="&raquo; ' . $this->aParamLang['base']['libelle_9'] . '" class="inputSubmit"  />';
                $aContent[] = '</div>';
                //
                $aContent[] = '<div class="row">';
                $aContent[] = '&nbsp;';
                $aContent[] = '</div>';
                //
                $aLdapFields = $this->ldap_getFields();
                //on supprime le champ unique_id qu'on a crée
                unset ($aLdapFields['unique_id']);
                unset ($aLdapFields['ldap_user_id']);
                //
                $aMysqlFields = $this->mysql_getFields();
                //
                if (!empty ($aLdapFields) AND !empty ($aMysqlFields))
                {
                        //
                        $aContent[] = '<div class="row">';
                        $aContent[] = '<span class="etape">' . $this->aParamLang['base']['libelle_8'] . ' 2 :</span>';
                        $aContent[] = '</div>';
                        //liste des champs ldap
                        $aContent[] = '<div class="colonneGauche">';
                        $aContent[] = $this->aParamLang['base']['libelle_6'] . '<br/>' . $this->formSelectComponent('select_ldap_ListFields', $aLdapFields) . ' (<b>' . count($aLdapFields) . '</b>)&nbsp;';
                        $aContent[] = '</div>';
                        //
                        //liste des champs mysql
                        $aContent[] = '<div class="colonneDroite">';
                        $aContent[] = $this->aParamLang['base']['libelle_7'] . '<br/>' . $this->formSelectComponent('select_mysql_ListFields', $aMysqlFields) . ' (<b>' . count($aMysqlFields) . '</b>)&nbsp;';
                        $aContent[] = '<input type="submit" name="btn_addToListFieldsToImport" value="&raquo;&nbsp;' . $this->aParamLang['ldap_import']['libelle_10'] . '" class="inputSubmit"  />';
                        $aContent[] = '</div>';
                } //fin if
                //
                $aContent[] = '<div class="row">';
                $aContent[] = '&nbsp;';
                $aContent[] = '</div>';
                //
                $array = @ $_SESSION['Ldap2Mysql']['listFieldsToImport'];
                //
                if (!empty ($array))
                {
                        $aContent[] = '<div class="row">';
                        $aContent[] = SPACE2 . '<table  summary="correspondance" id="ldap_import_correspondance"  cellpadding="0" cellspacing="0">';
                        //
                        foreach ($array as $key => $aFields)
                        {
                                //l'utilisation de md5 permet d'avoir un id avec chiffre et lettres uniquement
                                $champ_ldap = $aFields['ldap_ListFields'];
                                $champ_mysql = $aFields['mysql_ListFields'];
                                //
                                $aContent[] = SPACE3 . '<tr>';
                                $aContent[] = SPACE4 . '<td><input type="image" name="btn_supprimerCorrespondance_' . $key . '" src="' . IMAGE_DIR . 'iconeSupprimer.png" value="' . $key . '" title="' . $this->aParamLang['ldap_import']['libelle_11'] . '" alt="' . $this->aParamLang['ldap_import']['libelle_11'] . '" />';
                                $aContent[] = SPACE4 . '</td>';
                                $aContent[] = SPACE4 . '<td>' . $champ_ldap;
                                $aContent[] = SPACE4 . '</td>';
                                $aContent[] = SPACE4 . '<td><img src="' . IMAGE_DIR . 'fleche.png" title="' . $this->aParamLang['ldap_import']['libelle_32'] . '" alt="' . $this->aParamLang['ldap_import']['libelle_32'] . '" />&nbsp;&nbsp;&nbsp;';
                                $aContent[] = SPACE4 . '<td>' . $champ_mysql;
                                $aContent[] = SPACE4 . '</td>';
                                $aContent[] = SPACE3 . '</tr>';
                        }
                        //
                        $aContent[] = SPACE2 . '</table>';
                        $aContent[] = '</div>';
                        //
                }
                //
                if (!empty ($_SESSION['Ldap2Mysql']['errorConnexionMysql']))
                {
                        $aContent[] = '<div class="row">';
                        $aContent[] = '<span  class="errorMessage">&raquo;&nbsp;' . $_SESSION['Ldap2Mysql']['errorConnexionMysql'] . '</span>';
                        $aContent[] = '</div>';
                        unset ($_SESSION['Ldap2Mysql']['errorConnexionMysql']);
                }
                //
                if (!empty ($_SESSION['Ldap2Mysql']['errorUniqueId']))
                {
                        $aContent[] = '<div class="row">';
                        $aContent[] = '<span  class="errorMessage">&raquo;&nbsp;' . $_SESSION['Ldap2Mysql']['errorUniqueId'] . '</span>';
                        $aContent[] = '</div>';
                        //
                        unset ($_SESSION['Ldap2Mysql']['errorUniqueId']);
                }
                //
                if (!empty ($_SESSION['Ldap2Mysql']['errorConnexionLdap']))
                {
                        $aContent[] = '<div class="row">';
                        $aContent[] = '<span  class="errorMessage">&raquo;&nbsp;' . $_SESSION['Ldap2Mysql']['errorConnexionLdap'] . '</span>';
                        $aContent[] = '</div>';
                        //
                        unset ($_SESSION['Ldap2Mysql']['errorConnexionLdap']);
                }
                //
                if (!empty ($_SESSION['Ldap2Mysql']['listFieldsToImport']))
                {
                        $aContent[] = '<div class="row">';
                        $aContent[] = '&nbsp;';
                        $aContent[] = '</div>';
                        $aContent[] = '<div class="row">';
                        $aContent[] = '<span  class="etape">' . $this->aParamLang['base']['libelle_8'] . ' 3 :</span>';
                        $aContent[] = '</div>';
                        //
                        $aContent[] = '<br/>';
                        //
                        $aContent[] = '<div class="row">';
                        $aContent[] = '<input type="submit" name="btn_enregisterImportConfig" value="&raquo;&nbsp;' . $this->aParamLang['ldap_import']['libelle_12'] . '" class="inputSubmit"  />';
                        $aContent[] = '</div>';
                        //
                }
                if (!empty ($_SESSION['Ldap2Mysql']['writeFileMessageSucceed']))
                {
                        $sMessage = $_SESSION['Ldap2Mysql']['writeFileMessageSucceed'];
                        unset ($_SESSION['Ldap2Mysql']['writeFileMessageSucceed']);
                }
                if (!empty ($_SESSION['Ldap2Mysql']['writeFileMessageNotSucceed']))
                {
                        $sMessage = $_SESSION['Ldap2Mysql']['writeFileMessageNotSucceed'];
                        unset ($_SESSION['Ldap2Mysql']['writeFileMessageNotSucceed']);
                }
                //
                if (isset ($sMessage))
                {
                        $aContent[] = '<div class="row">';
                        $aContent[] = '<span  class="errorMessage">&raquo;&nbsp;' . $sMessage . '</span>';
                        $aContent[] = '</div>';
                } //fin if
                //
                //
                return implode("\n", $aContent);
                //
        }
        //
        /**
        *
        * @description  retourne un tableau de la liste de tous les  champs ldap
        *
        * @return  array  $_SESSION['Ldap2Mysql']['aListeCles']
        *
        */
        function ldap_getFields()
        {
                //
                if (!empty ($_POST['select_listeBase_ldap']))
                {
                        //unset ($_SESSION['Ldap2Mysql']['listFieldsToImport']);
                        $oBase_id = $_POST['select_listeBase_ldap'];
                        //---------------------------------------------------------------------------------------------//
                        //on charge sa config
                        $object = $this->baseLoadParam($oBase_id);
                        $oLdap = new stdClass();
                        foreach ($object as $key => $obj)
                        {
                                $oLdap-> $key = $obj->value;
                        }
                        //
                        $arrayParam['justeFields'] = true;
                        $oLdap->filter = '(objectclass=*)';
                        $array = $this->ldap_import_getData($oLdap, $arrayParam);
                        //
                        //
                        if (isset ($array['Ldap2Mysql']['errorUniqueId']))
                        {
                                return $array['Ldap2Mysql']['errorUniqueId'];
                        } //fin if
                        //
                        if (isset ($array['ldap']['ldap_ListFields']))
                        {
                                $_SESSION['Ldap2Mysql']['ldap_ListFields'] = $array['ldap']['ldap_ListFields'];
                                //
                                return $array['ldap']['ldap_ListFields'];
                        }
                        //
                }
                else
                {
                        return null;
                }
                //fin if
        }
        /**
        *
        * @description  retourne un tableau de la liste de tous les  champs mysql
        *
        * @return array  $_SESSION['Ldap2Mysql']['aListeCles']
        *
        */
        //
        function ldap_import_appliquerModifSelectUsers()
        {
                //on récupère les users des id sélectionnées
                $aCheckbox_user = @ $_POST['checkbox_user'];
                if (!empty ($aCheckbox_user))
                {
                        foreach ($aCheckbox_user as $unique_id)
                        {
                                foreach ($_POST as $key => $value)
                                {
                                        $tab = explode('-', $key);
                                        $champLdap = @ $tab[1];
                                        $aSelectedUsers[$unique_id] = $_SESSION['Ldap2Mysql']['aoUser'][$unique_id];
                                        //
                                        if ($tab[0] == $unique_id)
                                        {
                                                $champLdap = $tab[1];
                                                //on modifie le contenu
                                                $_SESSION['Ldap2Mysql']['aoUser'][$unique_id][$champLdap] = trim($value);
                                                $aSelectedUsers[$unique_id] = $_SESSION['Ldap2Mysql']['aoUser'][$unique_id];
                                        }
                                } //fin foreach
                        } //fin foreach
                } //fin if
                //
                if (isset ($aSelectedUsers))
                {
                        return $aSelectedUsers;
                }
                //
        } //fin function ldap_import_appliquerModifSelectUsers()
        //
        /**
         * @description  fonction de chargement de la liste des config d'importations
         */
        function ldap_import_loadListConfig()
        {
                //
                $file = CONFIG_DIR . 'importConfig';
                //
                if (file_exists($file))
                {
                        $aObjet = array ();
                        //
                        if (file_exists($file))
                        {
                                //on charge  le tableau d'objets texte avec désérialisation
                                $aObjet = unserialize(file_get_contents($file));
                        }
                        //
                        foreach ($aObjet as $key => $objet)
                        {
                                //on charge le nom des base ldap
                                $oLdap = $this->baseLoadParam($objet->ldap_id);
                                //
                                if (isset ($oLdap->nom->value))
                                {
                                        $nom_ldap = $oLdap->nom->value;
                                } //fin if
                                //
                                //on charge le nom des base mysql
                                $oMysql = $this->baseLoadParam($objet->mysql_id);
                                if (isset ($oMysql->nom->value))
                                {
                                        $nom_mysql = $oMysql->nom->value;
                                }
                                //
                                //
                                if (isset ($nom_ldap) AND isset ($nom_mysql))
                                {
                                        $aOption[$objet->id] = $nom_ldap . ' -> ' . $nom_mysql;
                                } //fin if
                                //
                                //
                        }
                        return $aOption;
                } //fin if
        }
        //
        /**
         * @description charge une config en fonction de son id
         */
        function ldap_import_loadConfig($configImport_id)
        {
                $file = CONFIG_DIR . 'importConfig';
                //
                $aObjet = array ();
                if (file_exists($file))
                {
                        //on charge  le fichier texte, chaque ligne est un objet
                        $aObjet = unserialize(file_get_contents($file));
                }
                $oParamImport = $aObjet[$configImport_id];
                //on récupèrela liste des champs ldap
                if (isset ($oParamImport))
                {
                        //chargement des champs des 2 bases
                        //
                        if (isset ($_SESSION['Ldap2Mysql']['ldap_listFieldsToImport']))
                        {
                                unset ($_SESSION['Ldap2Mysql']['ldap_listFieldsToImport']);
                        } //fin if
                        //
                        if (isset ($_SESSION['Ldap2Mysql']['mysql_listFieldsToImport']))
                        {
                                unset ($_SESSION['Ldap2Mysql']['mysql_listFieldsToImport']);
                        } //fin if
                        //
                        foreach ($oParamImport->listFieldsToImport as $key => $value)
                        {
                                $_SESSION['Ldap2Mysql']['ldap_listFieldsToImport'][] = $value['ldap_ListFields'];
                                $_SESSION['Ldap2Mysql']['mysql_listFieldsToImport'][] = $value['mysql_ListFields'];
                        }
                        //pour la requete sql
                        $_SESSION['Ldap2Mysql']['confListFieldsToImport'] = $oParamImport->listFieldsToImport;
                        //liste détaillee des champs de la table
                        $_SESSION['Ldap2Mysql']['mysql_detailedListFields'] = $oParamImport->mysql_detailedListFields;
                        //on garde en session les propriétés des 2 base
                        $arr = $this->baseLoadParam($oParamImport->ldap_id);
                        //param ldap
                        //on construit un objet plus simple avec juste le champ valeur de chaque propriété
                        foreach ($arr as $key => $propertie)
                        {
                                $aLdap[$key] = $propertie->value;
                        }
                        $oLdap = (object) $aLdap;
                        //param mysql
                        //on construit un objet plus simple avec juste le champ valeur de chaque propriété
                        $arr = $this->baseLoadParam($oParamImport->mysql_id);
                        foreach ($arr as $key => $propertie)
                        {
                                $aMysql[$key] = $propertie->value;
                        }
                        $oMysql = (object) $aMysql;
                        $array['oLdap'] = $oLdap;
                        $array['oMysql'] = $oMysql;
                        //
                        return $array;
                } //fin if
                //
        }
        //
        /**
         * @description execute l'importation
         */
        function ldap_import_finalize()
        {
                //
                $aSelectedUsers = $this->ldap_import_appliquerModifSelectUsers();
                //
                if (!isset ($aSelectedUsers))
                {
                        return;
                }
                //une connexion est déjà initialisée en activant l'importation
                //---------------------------------------------------------------------------------------------//
                //on récupère la liste des personnes déjà importés
                //id de la config $_SESSION['Ldap2Mysql']['ldap_config_import_id']
                $sldap_config_import_id = $_SESSION['Ldap2Mysql']['ldap_config_import_id'];
                $aInsertedUsers = $this->mysql_getInsertedUsers($sldap_config_import_id);
                //on leur affecte une nouvelle propriété dans la session
                //---------------------------------------------------------------------------------------------//
                //on vérifie si les champs d'import exitent dans la base mysql
                $aFieldsToAdd = $this->mysql_isFieldsExists();
                if (isset ($aFieldsToAdd) AND is_array($aFieldsToAdd))
                {
                        //on les crée
                        $this->mysql_createFields($aFieldsToAdd);
                }
                //---------------------------------------------------------------------------------------------//
                foreach ($aSelectedUsers as $user)
                {
                        //UPDATE
                        $sWhere = '';
                        if (in_array($user['ldap_user_id'], array_keys($aInsertedUsers)))
                        {
                                //on indique quelle type de requête, servira pour l'affichage à la afin de l 'import
                                $sUpdateOrInsert = 'update';
                                //on initialise le début de la requête
                                $sQuery = 'UPDATE ' . $this->sMysqlTableToUpdate . ' SET ';
                                //on initialise la fin de la requête
                                $sWhere = ' WHERE ldap_user_id' . "='" . $user['ldap_user_id'] . "'";
                        }
                        else
                        {
                                //on indique quelle type de requête, servira pour l'affichage à la afin de l 'import
                                $sUpdateOrInsert = 'insert';
                                //on prépare la requête
                                $sQuery = 'INSERT INTO ' . $this->sMysqlTableToUpdate . ' SET ';
                                //on affecte notre tableau initialisé à un autre qui se verra écraser certains champs
                                $arr = $this->mysql_initializeFieldsToImport();
                                //CREATION
                                //@promethee
                                if (isset ($this->dateCreation))
                                {
                                        $arr['_create'] = $this->dateCreation . "='" . date('Y-m-d H:i:s') . "'";
                                } //fin if
                                //
                                //fin @promethee
                                $arr['ldap_user_id'] = "ldap_user_id='" . trim($user['ldap_user_id']) . "'";
                        }
                        //CREATION AND UPDATE
                        $arr['ldap_config_import_id'] = "ldap_config_import_id='" . $_SESSION['Ldap2Mysql']['ldap_config_import_id'] . "'";
                        $arr['ldap_last_update'] = "ldap_last_update='" . date('Y-m-d H:i:s') . "'";
                        //on parcourt la liste de tous les champs de la tableau
                        //un exemple  de value est    value=array( 'ldap_ListFields'=> 'mail','mysql_ListFields' => '_email')
                        foreach ($_SESSION['Ldap2Mysql']['confListFieldsToImport'] as $key => $value)
                        {
                                if (isset ($value['ldap_ListFields']) AND isset ($user[$value['ldap_ListFields']]))
                                {
                                        $arr[$value['mysql_ListFields']] = $value['mysql_ListFields'] . "='" . addslashes(trim($user[$value['ldap_ListFields']])) . "'";
                                }
                        }
                        //UPDATE
                        //on affecte à tous les utilisateurs le droit choisi
                        if (!empty ($_POST['select_usersGroups']))
                        {
                                $arr[$this->aMysqlTableUserGroups['field_id']] = $this->aMysqlTableUserGroups['field_id'] . '=' . trim($_POST['select_usersGroups']);
                        }
                        //
                        $sQuery .= implode(',', $arr);
                        //on crée un tableau assoc avec comme clé l'unique_id du ldap
                        $aQuery[$user['unique_id']] = $sQuery . $sWhere;
                        $aUpdateOrInsert[$user['unique_id']] = $sUpdateOrInsert;
                        //destruction des variables
                        unset ($arr);
                        unset ($sQuery);
                        unset ($sUpdateOrInsert);
                }
                //insertion
                if (isset ($aQuery))
                {
                        $this->mysql_addUsersFromLdap($aQuery, $aUpdateOrInsert);
                }
        } //fin ldap_import_finalize()
        //
        /**
         * @description execute le chargement des utilisateurs du ldap (si click su bouton 'charger liste')
         *  et charge un tableau de session qui est réutilisé
         * sinon renvoi le tableau de session chargé la première fois
         * @return un tableau d'objets des utilisateurs
         */
        function ldap_import_getUsers()
        {
                //
                if (!empty ($_POST['btn_loadUsers']))
                {
                        //pour l'affichage du résultat
                        $this->result = true;
                        //si des utilsateurs ont été déjà sélectionnés dans une autre config, on les supprime
                        if (isset ($_SESSION['Ldap2Mysql']['selectedUsers']))
                        {
                                unset ($_SESSION['Ldap2Mysql']['selectedUsers']);
                        }
                        //on remet à zéro la pagination
                        if (!empty ($_POST['select_configImport']))
                        {
                                unset ($_SESSION['Ldap2Mysql']['numeroPage']);
                        }
                        //
                        if (!empty ($_POST['select_configImport']))
                        {
                                //on charge la config qui va alimenter des variables de sessions
                                $arr = $this->ldap_import_loadConfig($_POST['select_configImport']);
                                $oLdap = $arr['oLdap'];
                                //on charge les propriétés du ldap de la config
                                $_SESSION['Ldap2Mysql']['ldap_config_import_id'] = $_POST['select_configImport'];
                        }
                        //
                        if (isset ($oLdap))
                        {
                                //on construit un filtre par défaut
                                $filter = '(|'; // |=or &=et
                                foreach ($_SESSION['Ldap2Mysql']['ldap_listFieldsToImport'] as $key => $value)
                                {
                                        $filter .= '(' . $value . '=*)';
                                }
                                $filter .= ')';
                                $oLdap->filter = $filter;
                                //
                                if (!empty ($_POST['txt_filter']))
                                {
                                        $oLdap->filter = str_replace(' ', '', $_POST['txt_filter']);
                                }
                                //pour recharger le filtre
                                $this->ldap_import_setFilter($oLdap->filter);
                                //
                                $arrayParam['ldap_listFieldsToImport'] = $_SESSION['Ldap2Mysql']['ldap_listFieldsToImport'];
                                $array = $this->ldap_import_getData($oLdap, $arrayParam);
                                //
                                if (isset ($array['ldap']['aoUser']))
                                {
                                        $_SESSION['Ldap2Mysql']['aoUser'] = $array['ldap']['aoUser'];
                                        return $array['ldap']['aoUser'];
                                }
                        } //fin if
                }
                else
                {
                        //
                        if (isset ($_SESSION['Ldap2Mysql']['aoUser']))
                        {
                                //pour l'affichage du résultat
                                $this->result = true;
                                return $_SESSION['Ldap2Mysql']['aoUser'];
                        }
                        //
                } //fin if
                return null;
        } //fin function ldap_import_getUsers()
        //
        function ldap_import_getData($oLdap, $arrayParam = null)
        {
                //
                if (empty ($oLdap->port))
                {
                        $oLdap->port = 389; //port ldap par défaut
                }
                //
                $ressource_link = ldap_connect($oLdap->host, $oLdap->port);
                //on attache la base
                //$bBind =  ldap_bind($ressource_link, $oLdap->login, $oLdap->pass);
                $bBind = @ ldap_bind($ressource_link, $oLdap->login, $oLdap->pass);
                //on tente en anonymous
                if ($bBind == false)
                {
                        $bBind = @ ldap_bind($ressource_link, '', '');
                } //fin if
                //
                //
                if ($bBind == false)
                {
                        $_SESSION['Ldap2Mysql']['errorConnexionLdap'] = $this->aParamLang['config_import']['errorConnexionLdap'];
                        return array ();
                }
                $result = @ ldap_search($ressource_link, $oLdap->dn, $oLdap->filter);
                //
                //
                if (!$result)
                {
                        return array ();
                } //fin if
                //
                $aEntries = ldap_get_entries($ressource_link, $result);
                //on libère la mémoire serveur
                ldap_free_result($result);
                //
                $bLdap_user_id = false;
                //
                foreach ($aEntries as $key => $entrie)
                {
                        //
                        if (is_array($entrie))
                        {
                                //
                                $sStringToHash = '';
                                //
                                foreach ($entrie as $_key => $attribut)
                                {
                                        //
                                        if (is_array($attribut))
                                        {
                                                //si $attribut n'est pas un tableau donc a une  valeur=1
                                                if ($attribut['count'] == (int) 1)
                                                {
                                                        unset ($attribut['count']); //on n'a pas besoin de ce tableau
                                                        $value = strtolower(trim($attribut[0])); //on prend la première valeur
                                                }
                                                else
                                                {
                                                        unset ($attribut['count']);
                                                        $value = $attribut;
                                                } //fin if
                                                //
                                                if ($_key == $oLdap->ldap_user_id_field)
                                                {
                                                        if (is_array($value))
                                                        {
                                                                $value = $value[0];
                                                        }
                                                        $user['ldap_user_id'] = $value;
                                                        $bLdap_user_id = true;
                                                        //utile pour la manipulation des données en session
                                                        //on rajoute une chaine dans l'id car les id de balise n'autorise pas un début de chaine en numérique
                                                        $user['unique_id'] = 'md5' . md5($value);
                                                }
                                                //
                                                //
                                                $user[$_key] = $value;
                                                //on crée un tableau de toutes les clés car certains users n'ont pas certains attributs
                                                //on met comme clé le champ lui même cela permet d'avoir un tableau dont la liste est unique
                                                $aListeCles[$_key] = $_key;
                                                //on ajoute ce champ comme clé
                                                $aListeCles['ldap_user_id'] = 'ldap_user_id';
                                                $aListeCles['unique_id'] = 'unique_id';
                                        } //fin if
                                } //fin foreach
                                //on constitue un tableau d'objet
                                $aUserTemp[] = $user;
                                unset ($user);
                        } //fin if
                } //fin foreach
                //
                //
                if ($bLdap_user_id == false)
                {
                        $_SESSION['Ldap2Mysql']['errorUniqueId'] = $this->aParamLang['config_import']['errorUniqueId'];
                        return array ();
                }
                else
                {
                        unset ($_SESSION['Ldap2Mysql']['errorUniqueId']);
                }
                //
                if (!isset ($aListeCles))
                {
                        return;
                }
                //idem pour la liste des champs
                ksort($aListeCles);
                //
                $array['ldap']['ldap_ListFields'] = $aListeCles;
                //si simple récupération des champs
                if (isset ($arrayParam['justeFields']) AND $arrayParam['justeFields'] == true)
                {
                        return $array;
                }
                //si affichage des utilisateurs
                foreach ($aUserTemp as $index => $user)
                {
                        //
                        foreach ($aListeCles as $cle)
                        {
                                //
                                if (!isset ($user[$cle]))
                                {
                                        //donc pour éviter des erreurs php on leurs affecte une chaine vide
                                        $arr[$cle] = null;
                                }
                                else
                                {
                                        $arr[$cle] = $user[$cle];
                                }
                                //
                        }
                        //
                        if (!empty ($arr))
                        {
                                //on ajoute au tableau le tableau de l'utilisateur
                                $aUser[$user['unique_id']] = $arr;
                                unset ($arr);
                        }
                        //
                } //fin foreach
                //on stocke cette liste dans une session pour éviter de réinterroger le ldap
                //
                $array['ldap']['aoUser'] = $aUser;
                //
                return $array;
                //
        }
        //
        /**
         * @description enregistre le filtre saisie pour le recharger
         */
        function ldap_import_setFilter($sFilter = '')
        {
                //on éfface la dernière saisie
                unset ($_SESSION['Ldap2Mysql']['sFilter']);
                $_SESSION['Ldap2Mysql']['sFilter'] = $sFilter;
                $this->sFilter = $sFilter;
        }
        //
        /**
         * @description permet de recharger le filtre saisi
         * @return string $_SESSION['Ldap2Mysql']['sFilter']
         */
        function ldap_import_getFilter()
        {
                //
                if (isset ($_SESSION['Ldap2Mysql']['sFilter']))
                {
                        return $_SESSION['Ldap2Mysql']['sFilter'];
                }
        }
        //
        function ldap_import_controller()
        {
                //on crée une connexion avec selection de la base
                if (!empty ($_POST['select_configImport']))
                {
                        $this->mysql_getConnexion();
                }
                //
                if (!empty ($_POST['btn_finaliserModifSelectUsers']))
                {
                        $this->ldap_import_finalize();
                }
                //
                return $this->ldap_import_view();
        }
        /**
         * @description affiche la vue de l'interface d'importation
         */
        function ldap_import_view()
        {
                $aoUser = $this->ldap_import_getUsers();
                //
                //liste des serveurs ldap
                $aContent[] = SPACE1 . '<div class="row">';
                $aContent[] = '<div class ="divLeft">' . $this->aParamLang['ldap_import']['libelle_13'] . '&nbsp;:&nbsp;</div>';
                $aContent[] = '<div class ="divRight">';
                $aConfigImport = $this->ldap_import_loadListConfig();
                $aContent[] = $this->formSelectComponent('select_configImport', $aConfigImport, $aOption = array (
                        'onchange="clearField();"'
                ));
                $aContent[] = '</div>';
                $aContent[] = SPACE1 . '</div>';
                //case à cocher de visualisation des users déjà importées
                $aContent[] = SPACE1 . '<div class="row">';
                $aContent[] = '<div class ="divLeft">&nbsp;</div>';
                $aContent[] = '<div class ="divRight">';
                $aContent[] = '<input type="checkbox" checked="checked"  id="checkbox_includeImportedUsers" name="checkbox_includeImportedUsers"   />' . $this->aParamLang['ldap_import']['libelle_22'];
                $aContent[] = '</div>';
                $aContent[] = SPACE1 . '</div>';
                //
                $aContent[] = SPACE1 . '<br/>';
                //filtre
                $aContent[] = SPACE1 . '<div class="row">';
                $aContent[] = '<div class ="divLeft">';
                $aContent[] = $this->aParamLang['ldap_import']['libelle_2'] . '&nbsp;:&nbsp;';
                $aContent[] = '</div>';
                $aContent[] = '<div class ="divRight">';
                $aContent[] = '<input type="text"  id="txt_filter" name="txt_filter" value="' . $this->ldap_import_getFilter() . '" class="inputText"  size="50"  />';
                $aContent[] = '</div>';
                $aContent[] = SPACE1 . '</div>';
                //
                $libelle = $this->aParamLang['ldap_import']['libelle_5'];
                $nombreUser = 0;
                $nombreUser = count($aoUser);
                //
                switch ($nombreUser)
                {
                        //
                        case (int) 1 :
                                $libelle = $this->aParamLang['ldap_import']['libelle_3'];
                                break;
                                //
                        case (int) 0 :
                                $libelle = $this->aParamLang['ldap_import']['libelle_5'];
                                break;
                                //
                        case $nombreUser > (int) 1 :
                                $libelle = $this->aParamLang['ldap_import']['libelle_4'];
                                break;
                                //
                }
                //
                if (isset ($_SESSION['Ldap2Mysql']['ldap_config_import_id']))
                {
                        $aInsertedUsers = $this->mysql_getInsertedUsers($_SESSION['Ldap2Mysql']['ldap_config_import_id']);
                        if (!isset ($_POST['checkbox_includeImportedUsers']))
                        {
                                $nombreUser = $nombreUser -count($aInsertedUsers);
                        }
                }
                //
                if (isset ($this->result))
                {
                        //résultat nombre personnes trouvées
                        $aContent[] = SPACE1 . '<div class="row">';
                        $aContent[] = '<div class ="divLeft">&nbsp;';
                        $aContent[] = '</div>';
                        $aContent[] = '<div class ="divRight">';
                        //
                        $aContent[] = '&nbsp;&raquo;&nbsp;<span class="redBold">' . $nombreUser . '</span>&nbsp;' . $libelle;
                        //
                        $aContent[] = '</div>';
                        $aContent[] = SPACE1 . '</div>';
                }
                //
                $aContent[] = SPACE1 . '<div class="row">';
                $aContent[] = '<div class ="divLeft">&nbsp;';
                $aContent[] = '</div>';
                $aContent[] = '<div class ="divRight">';
                $aContent[] = SPACE2 . $this->formInputComponent('submit', 'btn_loadUsers', '&raquo;&nbsp;' . $this->aParamLang['ldap_import']['libelle_26'], array (
                        'class' => 'inputSubmit'
                ));
                $aContent[] = '</div>';
                $aContent[] = SPACE1 . '</div>';
                $aContent[] = SPACE1 . '<br/><br/>';
                //
                if (!empty ($aoUser))
                {
                        //select des droits utlisateurs
                        $aContent[] = SPACE1 . '<div class="row">';
                        $aContent[] = '<div class ="divLeft">' . $this->aParamLang['ldap_import']['libelle_21'] . '&nbsp;:&nbsp;';
                        $aContent[] = '</div>';
                        $aContent[] = '<div class ="divRight">';
                        //@formagri
                        //$aUsersGroups = $this->mysql_getFieldsUserGroups();
                        //$aContent[] = $this->formSelectComponent('select_usersGroups', $aUsersGroups);
                        $aContent[] = '</div>';
                        $aContent[] = SPACE1 . '</div>';
                        //
                        $aContent[] = SPACE1 . '<br/><br/>';
                        //legende des picto
                        $aContent[] = SPACE1 . '<div id="legende">';
                        $listePicto = '<img src="' . IMAGE_DIR . 'croix.png" title="" alt="" />&nbsp;' . $this->aParamLang['ldap_import']['libelle_19'] . '&nbsp;';
                        $listePicto .= '<img src="' . IMAGE_DIR . 'coche.png" title="" alt="" />&nbsp;' . $this->aParamLang['ldap_import']['libelle_18'] . '&nbsp;';
                        $listePicto .= '<img src="' . IMAGE_DIR . 'exclamation_orange.png" title="" alt="" />&nbsp;' . $this->aParamLang['ldap_import']['libelle_20'] . '&nbsp;';
                        $listePicto .= '<img src="' . IMAGE_DIR . 'refresh.png" title="" alt="" />&nbsp;' . $this->aParamLang['ldap_import']['libelle_27'] . '&nbsp;';
                        $aContent[] = $listePicto;
                        $aContent[] = SPACE1 . '</div>';
                        //gestion de la pagination
                        $aContent[] = SPACE1 . '<div class="row">';
                        $iTotalUser = count($aoUser);
                        //on récupère un objet pager : avec les prop iLimiteInferieure et   iNombreLignesAffichees
                        $oPager = $this->pager($iTotalUser);
                        //on découpe le tableau et on préserve les clés associatives
                        $aoUser_sliced_arrays = array_chunk($aoUser, $oPager->iNombreLignesAffichees, true);
                        //
                        $aContent[] = SPACE2 . $this->formInputComponent('submit', 'btn_finaliserModifSelectUsers', '&raquo;&nbsp;' . $this->aParamLang['ldap_import']['libelle_16'], array (
                                'class' => 'inputSubmit'
                        ));
                        //
                        if ($oPager->iNumeroPage == (int) 0)
                        {
                                $aContent[] = SPACE2 . $this->formInputComponent('submit', 'btn_preview', '&laquo;&nbsp;' . $this->aParamLang['ldap_import']['libelle_30'], array (
                                        'class' => 'inputSubmit',
                                        'disabled' => 'disabled'
                                ));
                        }
                        else
                        {
                                $aContent[] = SPACE2 . $this->formInputComponent('submit', 'btn_preview', '&laquo;&nbsp;' . $this->aParamLang['ldap_import']['libelle_30'], array (
                                        'class' => 'inputSubmit'
                                ));
                        }
                        if (isset ($oPager->bStop) AND $oPager->bStop == true)
                        {
                                $aContent[] = SPACE2 . $this->formInputComponent('submit', 'btn_next', $this->aParamLang['ldap_import']['libelle_29'] . '&nbsp;&raquo;', array (
                                        'class' => 'inputSubmit',
                                        'disabled' => 'disabled'
                                ));
                        }
                        else
                        {
                                $aContent[] = SPACE2 . $this->formInputComponent('submit', 'btn_next', $this->aParamLang['ldap_import']['libelle_29'] . '&nbsp;&raquo;', array (
                                        'class' => 'inputSubmit'
                                ));
                        }
                        //
                        $aData = array (
                                '10' => '10',
                                '20' => '20',
                                '50' => '50'
                        );
                        $aContent[] = $this->formSelectComponent('select_nombre_Affichage', $aData, $aOption = array (
                                'onchange="javascript:submit();"'
                        ), 'freeze');
                        $aContent[] = $this->formInputComponent('submit', 'btn_validerSelect', 'OK', array (
                                'class' => 'inputSubmit'
                        ));
                        $aContent[] = SPACE1 . '</div>';
                        //
                        $aContent[] = SPACE1 . '<div class="row">&nbsp;';
                        $aContent[] = SPACE1 . '</div>';
                        //
                        $aContent[] = '<!-- table des utilisateurs-->';
                        $aContent[] = SPACE1 . '<div class="row">';
                        //
                        $aContent[] = SPACE2 . '<table  summary="users" class="ldap_import"  cellpadding="0" cellspacing="0">';
                        $aContent[] = SPACE3 . '<tr>';
                        $aContent[] = SPACE4 . '<th>#</th>';
                        //
                        $aContent[] = SPACE4 . '<th>';
                        $aContent[] = '<input type="checkbox"  id="checkbox_selectAll" name="checkbox_selectAll"  onclick="selectAll();"  />';
                        $aContent[] = '</th>';
                        $aContent[] = SPACE4 . '<th>';
                        $aContent[] = '</th>';
                        //
                        $sSortBy = '';
                        //
                        if (!empty ($_POST))
                        {
                                //
                                foreach ($_POST as $key => $value)
                                {
                                        $tab = explode('#', $key);
                                        //
                                        if (isset ($tab[0]) AND $tab[0] == 'sortBy')
                                        {
                                                $sAttribut = $tab[1];
                                                //
                                                if (isset ($_SESSION['Ldap2Mysql']['sSortBy']))
                                                {
                                                        //
                                                        if ($_SESSION['Ldap2Mysql']['sSortBy'] == 'ASC')
                                                        {
                                                                $_SESSION['Ldap2Mysql']['sSortBy'] = 'DESC';
                                                                $sSortBy = 'DESC';
                                                        }
                                                        else
                                                        {
                                                                $_SESSION['Ldap2Mysql']['sSortBy'] = 'ASC';
                                                                $sSortBy = 'ASC';
                                                        }
                                                        //
                                                }
                                                else
                                                {
                                                        $sSortBy = 'ASC';
                                                        $_SESSION['Ldap2Mysql']['sSortBy'] = 'ASC';
                                                }
                                                //
                                        } //fin if
                                } //fin foreach
                                //
                        }
                        else
                        {
                                $sSortBy = @ $_SESSION['Ldap2Mysql']['sSortBy'];
                        } //fin if
                        //
                        $imageSortType = '';
                        switch ($sSortBy)
                        {
                                case 'ASC' :
                                        //
                                        $sSortBy = 'DESC';
                                        $pictoDesc = IMAGE_DIR . 'desc.png';
                                        //on récupère un tableau des propriétés de l'image
                                        //assurera la norme future pour xhtml 2.0
                                        $imageWidthHeight = getimagesize($pictoDesc);
                                        $imageSortType = '<img src="' . $pictoDesc . '" ' . $imageWidthHeight[3] . ' alt="' . $this->aParamLang['ldap_import']['libelle_23'] . '" title="' . $this->aParamLang['ldap_import']['libelle_23'] . '" />';
                                        break;
                                        //
                                case 'DESC' :
                                        //
                                        $sSortBy = 'ASC';
                                        $pictoAsc = IMAGE_DIR . 'asc.png';
                                        $imageWidthHeight = getimagesize($pictoAsc);
                                        $imageSortType = '<img src="' . $pictoAsc . '" ' . $imageWidthHeight[3] . ' alt="' . $this->aParamLang['ldap_import']['libelle_24'] . '" title="' . $this->aParamLang['ldap_import']['libelle_24'] . '" />';
                                        break;
                                        //
                        } //fin switch
                        //
                        foreach ($this->ldap_getAttributListToDisplay() as $key => $value)
                        {
                                $entete = '<input type="submit" name="sortBy#' . $value . '" value="' . $value . '" class="submit_entete" />';
                                //on n'active que celui qui est cliqueé
                                if (isset ($sAttribut) AND $sAttribut == $value)
                                {
                                        $aContent[] = SPACE4 . '<th>' . $imageSortType . $entete;
                                }
                                else
                                {
                                        $aContent[] = SPACE4 . '<th>' . $entete;
                                }
                                $aContent[] = '</th>';
                        }
                        //
                        $aContent[] = SPACE3 . '</tr>';
                        //
                        $aId_insertedUsers = array_keys($aInsertedUsers);
                        //
                        if (isset ($aoUser_sliced_arrays[$oPager->iNumeroPage]))
                        {
                                //
                                foreach ($aoUser_sliced_arrays[$oPager->iNumeroPage] as $key => $user)
                                {
                                        $oUser = (object) $user;
                                        //si case à cocher checkbox_includeImportedUsers activé
                                        if (!isset ($_POST['checkbox_includeImportedUsers']))
                                        {
                                                //on crée un tabelau avec les clés de ce tableau associatif
                                                if (in_array($oUser->unique_id, $aId_insertedUsers))
                                                {
                                                        continue;
                                                }
                                        }
                                        //
                                        //colonne des numéros de lignes,
                                        static $iterator = 0;
                                        $iterator++;
                                        //
                                        $numero = ($oPager->iNombreLignesAffichees * $oPager->iNumeroPage) + $iterator;
                                        //
                                        $tr_user[] = SPACE3 . '<tr id="TR_' . $numero . '">';
                                        $tr_user[] = SPACE4 . '<td>' . $numero . '</td>';
                                        //colonne des checkboxes
                                        $tr_user[] = SPACE4 . '<td><input id="checkbox_' . $numero . '" type="checkbox" name="checkbox_user[]" value="' . $oUser->unique_id . '" />';
                                        //on affiche ceux existants dans la base mysql
                                        //on examine l'état de l'insertion déterminé par la nouvelle propriété
                                        //affecté à l'objet oUser lors de la tentative d'insertion
                                        if (isset ($oUser->userImported))
                                        {
                                                //
                                                switch ($oUser->userImported)
                                                {
                                                        //
                                                        case 'insert' :
                                                                $tr_user[] = SPACE4 . '<td class="White_TD"><img src="' . IMAGE_DIR . 'coche.png" title="' . $this->aParamLang['ldap_import']['libelle_18'] . '" alt="' . $this->aParamLang['ldap_import']['libelle_18'] . '" /></td>';
                                                                break;
                                                                //
                                                        case 'update' :
                                                                $tr_user[] = SPACE4 . '<td class="White_TD"><img src="' . IMAGE_DIR . 'refresh.png" title="' . $this->aParamLang['ldap_import']['libelle_20'] . '" alt="' . $this->aParamLang['ldap_import']['libelle_31'] . '" /></td>';
                                                                break;
                                                                //
                                                        case 'failed' :
                                                                $tr_user[] = SPACE4 . '<td class="White_TD"><img src="' . IMAGE_DIR . 'croix.png" title="' . $this->aParamLang['ldap_import']['libelle_19'] . '" alt="' . $this->aParamLang['ldap_import']['libelle_19'] . '" /></td>';
                                                                break;
                                                                //
                                                } //fin switch
                                        }
                                        else
                                        {
                                                //
                                                if (in_array($oUser->ldap_user_id, $aId_insertedUsers))
                                                {
                                                        $tr_user[] = SPACE4 . '<td class="White_TD"><img src="' . IMAGE_DIR . 'exclamation_orange.png" title="' . $this->aParamLang['ldap_import']['libelle_20'] . '" alt="' . $this->aParamLang['ldap_import']['libelle_20'] . '" /></td>';
                                                }
                                                else
                                                {
                                                        $tr_user[] = SPACE4 . '<td>&nbsp;</td>';
                                                }
                                                //
                                        } //fin if
                                        //on récupère les td de chaque champ
                                        $tr_user[] = $this->ldap_checkUserAttribut($oUser);
                                        //
                                        $tr_user[] = '</td>';
                                        $tr_user[] = SPACE3 . '</tr>';
                                        //on ajoute la ligne tr à un tableau pour le trier selon le critère demandé
                                        if (isset ($sAttribut))
                                        {
                                                //on provoque le tri avec $oUser->$sortBy
                                                //$oUser->unique_id  évite les doublons
                                                $arraytemp["'" . $oUser-> $sAttribut . $oUser->unique_id . "'"] = implode("\n", $tr_user);
                                        }
                                        else
                                        {
                                                $arraytemp[] = implode("\n", $tr_user);
                                        }
                                        unset ($tr_user);
                                } //fin foreach
                                //on active le type de tri selon le cas
                                switch ($sSortBy)
                                {
                                        //
                                        case 'ASC' :
                                                ksort($arraytemp);
                                                break;
                                        case 'DESC' :
                                                krsort($arraytemp);
                                                break;
                                                //
                                }
                                //on inclue la numérotation
                                //                        foreach ($arraytemp as $value)
                                //                        {
                                //                                static $i = 1;
                                //                                $aTR[] = str_replace('##ITERATOR##', $i++, $value);
                                //                        }
                                //
                                $aContent[] = implode("\n", $arraytemp);
                        } //fin if
                        //
                        $aContent[] = SPACE2 . '</table>';
                        $aContent[] = SPACE1 . '</div>';
                }
                //
                return implode("\n", $aContent);
        } //fin ldap_display_import()
        //
        /**
         * @description gère la pagination des résultats
         */
        function pager($totalData)
        {
                //on construit un objet pager (sans class)
                $oPager = new stdclass();
                //si aucune action
                if (!isset ($_SESSION['Ldap2Mysql']['numeroPage']))
                {
                        $_SESSION['Ldap2Mysql']['numeroPage'] = 0;
                        $_SESSION['Ldap2Mysql']['nombreLignesAffichees'] = 10; //$this->_aSelect_nombre_Affichage[0];
                }
                if (!empty ($_POST))
                {
                        foreach ($_POST as $_key => $_value)
                        {
                                switch ($_key)
                                {
                                        //select_nombre_Affichage
                                        case 'select_nombre_Affichage' :
                                                //
                                                $_SESSION['Ldap2Mysql']['nombreLignesAffichees'] = $_POST['select_nombre_Affichage'];
                                                break;
                                                //
                                        case 'btn_next' :
                                                //
                                                $iLimiteDesactivation = $_SESSION['Ldap2Mysql']['numeroPage'] * ($_SESSION['Ldap2Mysql']['nombreLignesAffichees']);
                                                //
                                                if ($iLimiteDesactivation <= ($totalData - $_SESSION['Ldap2Mysql']['nombreLignesAffichees']))
                                                {
                                                        $_SESSION['Ldap2Mysql']['numeroPage']++;
                                                }
                                                //
                                                $iTotalPage = ceil($totalData / $_SESSION['Ldap2Mysql']['nombreLignesAffichees']);
                                                if ($_SESSION['Ldap2Mysql']['numeroPage'] == ($iTotalPage -1))
                                                {
                                                        $oPager->bStop = true;
                                                } //fin if
                                                //
                                                break;
                                                //
                                        case 'btn_preview' :
                                                //
                                                if ($_SESSION['Ldap2Mysql']['numeroPage'] > (int) 0)
                                                {
                                                        $_SESSION['Ldap2Mysql']['numeroPage']--;
                                                }
                                                //
                                                break;
                                                //
                                } //fin switch
                                //
                        } //fin foreach
                } //fin if
                //
                $oPager->iNombreLignesAffichees = $_SESSION['Ldap2Mysql']['nombreLignesAffichees'];
                $oPager->iNumeroPage = $_SESSION['Ldap2Mysql']['numeroPage'];
                //
                return $oPager;
        } //fin  function pager()
        //
        function consult_getAttributListToDisplay()
        {
                //
                if ($_SESSION['Ldap2Mysql']['mysql_listFieldsToImport'])
                {
                        return $_SESSION['Ldap2Mysql']['mysql_listFieldsToImport'];
                }
                else
                {
                        return null;
                }
                //
        }
        //
        /**
         * @description récupère les utilisateurs non présents dans le ldap
         * @return array d'objets $aoUser
         */
        //
        function consult_getUsersNotInLdap()
        {
                //
                $sQuery = 'SELECT * FROM ' . $this->sMysqlTableToUpdate;
                $sQuery .= " WHERE ldap_user_id IS NULL OR  ldap_user_id LIKE ''";
                //construction de la requête
                //connexion et création d'une ressource
                $bNoConnexion = $this->mysql_getConnexion();
                //
                if (isset ($bNoConnexion))
                {
                        return null;
                } //fin if
                //
                $res = mysql_query($sQuery);
                //
                //on récupère la list des champs mysql
                $mysql_listFieldsToImport = $_SESSION['Ldap2Mysql']['mysql_listFieldsToImport'];
                //on récupère les données correspondantes
                while ($row = mysql_fetch_assoc($res))
                {
                        //
                        $oUser = new stdClass();
                        //$oUser->
                        foreach ($mysql_listFieldsToImport as $champ)
                        {
                                $oUser-> $champ = $row[$champ];
                        }
                        $aoUser[] = $oUser;
                        //
                }
                //
                if (isset ($aoUser))
                {
                        return $aoUser;
                }
                //
        }
        function consult_controller()
        {
                return $this->consult_view($this->consult_getUsersNotInLdap());
        }
        //
        function consult_view($aoUser)
        {
                //
                //liste des serveurs ldap
                $aContent[] = SPACE1 . '<div class="row">';
                $aContent[] = '<div class ="divLeft">' . $this->aParamLang['ldap_import']['libelle_25'] . '&nbsp;:&nbsp;</div>';
                $aContent[] = '<div class ="divRight">';
                $aConfigImport = $this->ldap_import_loadListConfig();
                $aContent[] = $this->formSelectComponent('select_configImport', $aConfigImport, $aOption = array (
                        'onchange="clearField();"'
                ));
                $aContent[] = '<input type="submit" name="btn_loadUsersfromMysql" value="&raquo;&nbsp;' . $this->aParamLang['ldap_import']['libelle_26'] . '" class="inputSubmit"  />';
                $aContent[] = '</div>';
                $aContent[] = SPACE1 . '</div><br/>';
                //
                $aContent[] = SPACE1 . '<div class="row">&raquo;&nbsp;' . $this->aParamLang['ldap_import']['libelle_28'];
                $aContent[] = SPACE1 . '</div>';
                //
                $libelle = $this->aParamLang['ldap_import']['libelle_5'];
                $nombreUser = 0;
                //
                if (isset ($aoUser))
                {
                        $nombreUser = count($aoUser);
                        $this->result = true;
                }
                //
                //
                switch ($nombreUser)
                {
                        //
                        case (int) 1 :
                                $libelle = $this->aParamLang['ldap_import']['libelle_3'];
                                break;
                                //
                        case (int) 0 :
                                $libelle = $this->aParamLang['ldap_import']['libelle_5'];
                                break;
                                //
                        case $nombreUser > (int) 1 :
                                $libelle = $this->aParamLang['ldap_import']['libelle_4'];
                                break;
                                //
                }
                //
                if (isset ($this->result))
                {
                        //résultat nombre personnes trouvées
                        $aContent[] = SPACE1 . '<div class="row">';
                        $aContent[] = '<div class ="divLeft">&nbsp;';
                        $aContent[] = '</div>';
                        $aContent[] = '<div class ="divRight">';
                        //
                        $aContent[] = '&nbsp;&raquo;&nbsp;<span class="redBold">' . $nombreUser . '</span>&nbsp;' . $libelle;
                        //
                        $aContent[] = '</div>';
                        $aContent[] = SPACE1 . '</div>';
                }
                //
                $aContent[] = SPACE1 . '<div class="row">';
                $aContent[] = '&nbsp;';
                $aContent[] = SPACE1 . '</div>';
                if (!empty ($aoUser))
                {
                        //
                        $aContent[] = '<!-- table des utilisateurs-->';
                        $aContent[] = SPACE1 . '<div class="row">';
                        //
                        $aContent[] = SPACE2 . '<table  summary="users" class="ldap_import"  cellpadding="0" cellspacing="0">';
                        $aContent[] = SPACE3 . '<tr>';
                        $aContent[] = SPACE4 . '<th>#';
                        $aContent[] = SPACE4 . '</th>';
                        //
                        $sSortBy = '';
                        //
                        if (!empty ($_POST))
                        {
                                //
                                foreach ($_POST as $key => $value)
                                {
                                        $tab = explode('#', $key);
                                        //
                                        if (isset ($tab[0]) AND $tab[0] == 'sortBy')
                                        {
                                                $sAttribut = $tab[1];
                                                //
                                                if (isset ($_SESSION['Ldap2Mysql']['sSortBy']))
                                                {
                                                        //
                                                        if ($_SESSION['Ldap2Mysql']['sSortBy'] == 'ASC')
                                                        {
                                                                $_SESSION['Ldap2Mysql']['sSortBy'] = 'DESC';
                                                                $sSortBy = 'DESC';
                                                        }
                                                        else
                                                        {
                                                                $_SESSION['Ldap2Mysql']['sSortBy'] = 'ASC';
                                                                $sSortBy = 'ASC';
                                                        }
                                                        //
                                                }
                                                else
                                                {
                                                        $_SESSION['Ldap2Mysql']['sSortBy'] = 'ASC';
                                                }
                                                //
                                        } //fin if
                                } //fin foreach
                                //
                        }
                        else
                        {
                                $sSortBy = @ $_SESSION['Ldap2Mysql']['sSortBy'];
                        } //fin if
                        //
                        $imageSortType = '';
                        switch ($sSortBy)
                        {
                                case 'ASC' :
                                        //
                                        $sSortBy = 'DESC';
                                        $pictoDesc = IMAGE_DIR . 'desc.png';
                                        $imageWidthHeight = getimagesize($pictoDesc);
                                        $imageSortType = '<img src="' . $pictoDesc . '" ' . $imageWidthHeight . ' alt="' . $this->aParamLang['ldap_import']['libelle_23'] . '" title="' . $this->aParamLang['ldap_import']['libelle_23'] . '" />';
                                        break;
                                        //
                                case 'DESC' :
                                        //
                                        $sSortBy = 'ASC';
                                        $pictoAsc = IMAGE_DIR . 'asc.png';
                                        $imageWidthHeight = getimagesize($pictoAsc);
                                        $imageSortType = '<img src="' . $pictoAsc . '" ' . $imageWidthHeight . ' alt="' . $this->aParamLang['ldap_import']['libelle_24'] . '" title="' . $this->aParamLang['ldap_import']['libelle_24'] . '" />';
                                        break;
                                        //
                        } //fin switch
                        //
                        $aListeChampMysql = $this->consult_getAttributListToDisplay();
                        foreach ($aListeChampMysql as $key => $champ)
                        {
                                $entete = '<input type="submit" name="sortBy#' . $champ . '" value="' . $champ . '" class="submit_entete" />';
                                //on n'active que celui qui est cliqueé
                                if (isset ($sAttribut) AND $sAttribut == $champ)
                                {
                                        $aContent[] = SPACE4 . '<th>' . $imageSortType . $entete;
                                }
                                else
                                {
                                        $aContent[] = SPACE4 . '<th>' . $entete;
                                }
                                $aContent[] = SPACE4 . '</th>';
                        }
                        //
                        $aContent[] = SPACE3 . '</tr>';
                        //
                        foreach ($aoUser as $key => $oUser)
                        {
                                //
                                $tr_user[] = SPACE3 . '<tr>';
                                //colonne des numéros de lignes, on numérote les lignes avec $key
                                $tr_user[] = SPACE4 . '<td>##ITERATOR##</td>';
                                //
                                foreach ($aListeChampMysql as $champ)
                                {
                                        $tr_user[] = SPACE4 . '<td>' . $oUser-> $champ . '</td>';
                                }
                                $tr_user[] = SPACE3 . '</tr>';
                                //on ajoute la ligne tr à un tableau pour le trier selon le critère demandé
                                if (isset ($sAttribut))
                                {
                                        //on provoque le tri avec $oUser->$sortBy
                                        //pour  éviter les doublons on ajoute $this->get_temporary_unique_id
                                        $arraytemp["'" . $oUser-> $sAttribut . $this->get_temporary_unique_id() . "'"] = implode("\n", $tr_user);
                                }
                                else
                                {
                                        $arraytemp[] = implode("\n", $tr_user);
                                }
                                unset ($tr_user);
                        } //fin foreach
                        //on active le type de tri selon le cas
                        switch ($sSortBy)
                        {
                                //
                                case 'ASC' :
                                        ksort($arraytemp);
                                        break;
                                case 'DESC' :
                                        krsort($arraytemp);
                                        break;
                                        //
                        }
                        //on inclue la numérotation
                        foreach ($arraytemp as $value)
                        {
                                static $i = 1;
                                $aTR[] = str_replace('##ITERATOR##', $i++, $value);
                        }
                        //
                        $aContent[] = implode("\n", $aTR);
                        //
                        $aContent[] = SPACE2 . '</table>';
                        $aContent[] = SPACE1 . '</div>';
                }
                //
                return implode("\n", $aContent);
        }
        //
        /**
         * @description liste des champs ldap à afficher
         */
        function ldap_getAttributListToDisplay()
        {
                //
                if ($_SESSION['Ldap2Mysql']['ldap_listFieldsToImport'])
                {
                        return $_SESSION['Ldap2Mysql']['ldap_listFieldsToImport'];
                }
                else
                {
                        return null;
                }
                //
        }
        //
        function ldap_checkUserAttribut($oUser)
        {
                //
                $ldap_attributListToDisplay = $this->ldap_getAttributListToDisplay();
                if (isset ($ldap_attributListToDisplay))
                {
                        //$value étant le champ à afficher donc l'attribut de $oUser
                        foreach ($ldap_attributListToDisplay as $key => $attribut)
                        {
                                //on vérifie quel type de contenu (array,string)
                                switch (@ gettype($oUser-> $attribut))
                                {
                                        //l'attibut est un array donc à parcourir
                                        case 'array' :
                                                //
                                                $select[] = '<select name="' . $oUser->unique_id . '-' . $attribut . '">';
                                                foreach ($oUser-> $attribut as $_key => $_value)
                                                {
                                                        $_value = strtolower(trim($_value));
                                                        $select[] = '<option value="' . $_value . '">' . $_value . '</option>';
                                                }
                                                $select[] = '</select>';
                                                $td[] = implode("\n", $select);
                                                break;
                                                //
                                        case 'string' :
                                                //
                                                $contentTD = $oUser-> $attribut;
                                                //
                                                if (strlen($contentTD) > 25)
                                                {
                                                        $td[] = '<a href="#" title="' . $contentTD . '">' . substr($oUser-> $attribut, 0, 25) . '...</a>';
                                                }
                                                else
                                                {
                                                        $td[] = $contentTD;
                                                }
                                                break;
                                                //
                                        case 'NULL' :
                                                //
                                                $td[] = null;
                                                break;
                                                //
                                } //fin switch
                                //
                        }
                        //
                        if (isset ($td))
                        {
                                return SPACE4 . '<td class="content">' . implode('</td>' . "\n" . SPACE4 . '<td class="content">', $td);
                        }
                        //
                } //fin if
        }
        //
        function baseConfig_save_delete($objetToSerialize, $fFile, $sDelete = null)
        {
                //
                $aObjet = array ();
                if (file_exists($fFile))
                {
                        //on charge  le fichier texte, chaque ligne est un objet
                        $aObjet = unserialize(file_get_contents($fFile));
                }
                //
                $aObjet[$objetToSerialize->id->value] = $objetToSerialize;
                //suppression
                if ($sDelete == 'delete')
                {
                        unset ($aObjet[$objetToSerialize->id->value]);
                }
                //si le fichier n'existe pas on tente de le créer
                $handle = fopen($fFile, 'w+');
                fwrite($handle, serialize($aObjet));
                fclose($handle);
        }
        //
        /**
         * @description initialise un  objet base (MYSQL, ldap, CAS) avec les
         * paramètres par défaut
         *
         * @param string $sBaseType
         * @return object $oBase
         *
         */
        function baseInitializeObject($sBaseType)
        {
                //on charge un modèle d'objet
                $oBase = $this->formInitializeObject($sBaseType);
                //on redéfinit certaines propriétés sinon on renvoie l'objet tel quel
                switch ($sBaseType)
                {
                        //
                        case 'activedirectory' :
                                //
                                $oBase->port->required = false; //pass
                                break;
                                //
                        case 'openldap' :
                                //
                                $oBase->port->required = false; //pass
                                break;
                                //
                        case 'mysql' :
                                //
                                $oBase->pass->required = false; //pass
                                $oBase->port->required = false; //port
                                break;
                                //
                        case 'cas' :
                                //
                                $oBase->path->required = false; //path
                                $oBase->port->required = false; //port
                                $oBase->nom->required = false; //port
                                break;
                                //
                }
                //
                return $oBase;
        }
        //
        /**
         * @description gestion de toutes les actions sur l'interface de configuration
         * @return object $oBase
         */
        function baseConfig_controller($sBaseType = 'openldap')
        {
                $fFile = CONFIG_DIR . 'baseConfig';
                //
                $action = null;
                if (!empty ($_POST['type']))
                {
                        $sBaseType = $_POST['type'];
                        $action = 'type';
                }
                //
                $aAction = array (
                        'new',
                        'cancel',
                        'save',
                        'modify',
                        'delete'
                );
                //
                $oBase = $this->baseInitializeObject($sBaseType);
                $this->formVisible = false;
                //liste des boutons à afficher
                $this->listeBoutons = array (
                        'new',
                        'modify',
                        'delete'
                ); //on affiche la liste des serveurs
                $this->blisteDeroulanteVisible = true;
                //on active l'affichage de la date de mise à jour
                //on capture l'action
                if (!empty ($_POST))
                {
                        foreach ($_POST as $key => $value)
                        {
                                //l'action est la valeur name du bouton ou le submit du select de la base select_loadBase
                                if (in_array($key, $aAction))
                                {
                                        $action = $key;
                                }
                        }
                        //
                }
                //
                if (!empty ($_POST['select_listBase']))
                {
                        //chargement du modèle de notre objet $oBase
                        $oBase = $this->baseLoadParam($_POST['select_listBase']);
                        $this->formVisible = true;
                }
                //
                switch ($action)
                {
                        //
                        case 'new' :
                                $this->formVisible = true;
                                //
                                $oBase = $this->baseInitializeObject($sBaseType);
                                //pour rendre visible le formulaire vide
                                //on affiche la liste des serveurs
                                $this->blisteDeroulanteVisible = false;
                                $this->isVisible_btn_Test = true;
                                $this->listeBoutons = array (
                                        'cancel',
                                        'save'
                                );
                                break;
                                //
                        case 'type' :
                                //
                        case 'save' :
                                //
                                $this->formVisible = true;
                                $bValider = true;
                                //
                                foreach ($oBase as $propertie => $value)
                                {
                                        //pour  éviter d'écraser les valeurs par défaut de l'objet
                                        if (isset ($_POST[$propertie]))
                                        {
                                                $oBase-> $propertie->value = trim($_POST[$propertie]);
                                                //on récupère au passage la liste des champs saisis
                                                if ($propertie <> 'id' AND $oBase-> $propertie->required == 1 AND $oBase-> $propertie->value == '')
                                                {
                                                        $oBase-> $propertie->valide = false;
                                                        $bValider = false;
                                                }
                                        }
                                }
                                if ($action == 'save')
                                {
                                        //on vérifie si nom n'existe pas déjà
                                        if (isset ($oBase->nom->value) AND trim($oBase->nom->value) <> '')
                                        {
                                                $iNombreBaseTrouve = $this->baseNomExist($oBase->nom->value);
                                        }
                                        //on verifie qu'aucun nom n'est déjà attribué
                                        if (isset ($iNombreBaseTrouve))
                                        {
                                                //
                                                $bValider = false;
                                                //
                                                $oBase->nom->valide = false;
                                                $oBase->nom->errorMessage = $this->aParamLang['base']['libelle_13'];
                                                //
                                        }
                                }
                                //les champs requis sont remplis on enregistre ET ce n'est pas un tes de connexion
                                if ($bValider == true)
                                {
                                        //la première portion faciltera le tri selon le type de base
                                        $oBase->id->value = $oBase->type->value . '-' . md5($oBase->nom->value);
                                        //
                                        $this->baseConfig_save_delete($oBase, $fFile);
                                        //on affiche la liste des serveurs
                                        $this->blisteDeroulanteVisible = true;
                                        $this->listeBoutons = array (
                                                'new',
                                                'modify',
                                                'delete'
                                        );
                                        $oBase = $this->baseInitializeObject($sBaseType);
                                }
                                else
                                {
                                        $this->blisteDeroulanteVisible = false;
                                        $this->isVisible_btn_Test = true;
                                        $this->listeBoutons = array (
                                                'cancel',
                                                'save'
                                        );
                                } //fin if
                                break;
                                //
                        case 'modify' :
                                //
                                $bValider = true;
                                //
                                foreach ($oBase as $propertie => $value)
                                {
                                        //pour  éviter d'écraser les valeurs par défaut de l'objet
                                        if (isset ($_POST[$propertie]))
                                        {
                                                $oBase-> $propertie->value = trim($_POST[$propertie]);
                                                //on récupère au passage la liste des champs saisis
                                                if ($oBase-> $propertie->required == 1 AND $oBase-> $propertie->value == '')
                                                {
                                                        $oBase-> $propertie->valide = false;
                                                        $bValider = false;
                                                } //fin if
                                        } //fin if
                                } //fin foreach
                                //on vérifie si nom n'existe pas déjà
                                $iNombreBaseTrouve = $this->baseNomExist($oBase->nom->value);
                                //on verifie qu'aucun nom n'est déjà attribué
                                if (count($iNombreBaseTrouve) > 1)
                                { //
                                        $bValider = false;
                                        $oBase->nom->valide = false;
                                        $oBase->nom->errorMessage = $this->aParamLang['base']['libelle_13'];
                                }
                                //les champs requis sont remplis on enregistre
                                if ($bValider == true)
                                {
                                        $this->baseConfig_save_delete($oBase, $fFile);
                                }
                                //on affiche la liste des serveurs
                                $this->blisteDeroulanteVisible = true;
                                $this->listeBoutons = array (
                                        'new',
                                        'modify',
                                        'delete'
                                );
                                break;
                                //
                        case 'delete' :
                                //
                                if (isset ($oBase))
                                {
                                        $this->baseConfig_save_delete($oBase, $fFile, 'delete');
                                } //fin if
                                //on réinitialise l'objet base
                                $oBase = $this->baseInitializeObject($sBaseType);
                                $this->listeBoutons = array (
                                        'new',
                                        'modify',
                                        'delete'
                                );
                                //on affiche la liste des serveurs
                                $this->blisteDeroulanteVisible = true;
                                //on met les boutons de contrôles necéssaires
                                //
                                break;
                                //
                        case 'cancel' :
                                //on affiche la liste des bases
                                $this->blisteDeroulanteVisible = true;
                                $this->listeBoutons = array (
                                        'new',
                                        'modify',
                                        'delete'
                                );
                                //
                                break;
                                //
                        default :
                                break;
                } //fin switch
                //
                //
                return $this->baseConfig_view($oBase);
        } // fin baseConfig_controller($sBaseType)
        //
        function baseConfig_view($oBase)
        {
                //selection des différents serveurs pour chaque type
                if ($this->blisteDeroulanteVisible == true)
                {
                        unset ($oBase->type);
                        $aContent[] = '<div class="row">';
                        $aContent[] = '<div class ="divLeft">' . $this->aParamLang['base']['libelle_1'] . ' : </div>';
                        $aContent[] = '<div class ="divRight">';
                        //on récupère un tableau d'objets et on construit le select  de la liste des bases
                        $aoListeBase = $this->baseLoadList();


                        $aContent[] = $this->buildSelectListeBase($aoListeBase);
                        $aContent[] = '</div>';
                        $aContent[] = '</div>';
                        //
                }
                //
                if ($this->formVisible == true)
                {
                        $sComponent = '';
                        $bMysqlTrouve=false;
                        //
                        foreach ($oBase as $propertie => $value)
                        {


                                //
                                switch ($propertie)
                                {


                                                //si type de la base, on le met en hidden
                                                //
                                        case 'type' :
                                                //$mode
                                                $aServer= array (
                                                        'activedirectory' => 'ACTIVE DIRECTORY',
                                                        'openldap' => 'OPEN LDAP',
                                                        'cas' => 'CAS',
                                                        'mysql' => 'MYSQL'
                                                );
 //


                                                $sComponent = $this->formSelectComponent('type',$aServer, $aOption = array (
                                                        'onchange="javascript:submit();"'
                                                ), $oBase-> $propertie->value);
                                                $sComponent .= $this->formInputComponent('submit', 'btn_validerSelect', 'OK', array (
                                                        'class' => 'inputSubmit'
                                                ));
                                                break;
                                                //
                                          //si id de la base, on le met en hidden
                                        case 'id' :
                                                $sComponentHidden = $this->formInputComponent('hidden', 'id', $oBase-> $propertie->value);
                                        default :
                                                //

                                                if(substr($value->value,0,5)=='mysql' OR ($bMysqlTrouve==true AND ($propertie=='login' OR $propertie=='pass')))
                                                {
                                                 $bMysqlTrouve=true;


                                                       $sComponent= $this->formInputComponent('password', $propertie, $oBase-> $propertie->value, array (
                                                        'class' => 'inputText'
                                                ));


                                                }
                                                else
                                                {
                                                 $sComponent= $this->formInputComponent('text', $propertie, $oBase-> $propertie->value, array (
                                                        'class' => 'inputText'
                                                ));
                                                }
                                                //
                                                break;
                                } //fin switch
                                //
                                //on charge le label de chaque contrôle
                                if ($propertie <> 'id')
                                {
                                        //
                                        $sAsterisque = '';
                                        if ($oBase-> $propertie->required == true)
                                        {
                                                $sAsterisque = '<span style="color:red;font-weight:normal;">&nbsp;*</span>';
                                        }else
                                                $sAsterisque = '<span style="color:white;font-weight:normal;">&nbsp;*</span>';
                                        //
                                        $aContent[] = '<div class="row">';
                                        $aContent[] = '<div class ="divLeft">' . $this->aParamLang['base'][$propertie] . ' : </div>';
                                        $aContent[] = '<div class ="divRight">' . $sComponent . $sAsterisque . '</div>';
                                        $aContent[] = '</div>';
                                        //si la propriété est invalide , on affiche son message d'erreur
                                        if (isset ($oBase-> $propertie->valide) AND $oBase-> $propertie->valide == false)
                                        {
                                                $aContent[] = '<div class="row">';
                                                $aContent[] = '<div class ="divLeft">&nbsp;</div>';
                                                $aContent[] = '<div class ="divRight">';
                                                $aContent[] = '<span class="errorMessage">^&nbsp;' . $oBase-> $propertie->errorMessage . '</span>';
                                                $aContent[] = '</div>';
                                                $aContent[] = '</div>';
                                        }
                                } //fin if
                                //
                                //
                        } //fin foreach
                        $aContent[] = $sComponentHidden;
                        //
                        //
                } //fin if
                //
                $aContent[] = '<div id="boutonsControls">';
                $aContent[] = '<div class ="divLeft">&nbsp;</div>';
                $aContent[] = '<div class ="divRight">';
                //
                foreach ($this->listeBoutons as $value)
                {
                        static $sListeBoutons = '';
                        $sListeBoutons .= $this->formInputComponent('submit', $value, $this->aParamLang[$value], array (
                                'class' => 'inputSubmit'
                        )) . '&nbsp;';
                }
                $aContent[] = $sListeBoutons;
                $aContent[] = '</div>';
                $aContent[] = '</div>';
                return implode("\n", $aContent);
        } //fin  ldap_get_controlPanel
        //
        //
        /**
         *
         * @description vérifie si le nom du base existe
         * @param string $nom
         * @return object $oBase
         */
        //
        function baseNomExist($nom)
        {
                //on charge toutes les base selon le type
                $aObjet = $this->baseLoadList();
                //
                //
                if (isset ($aObjet))
                {
                        //
                        foreach ($aObjet as $objet)
                        {
                                //
                                if (isset ($nom) AND isset ($objet->nom->value))
                                {
                                        //
                                        if (strtolower($nom) == strtolower($objet->nom->value))
                                        {
                                                //on comptabilise le nombre de libelle semblable
                                                //utile pour l'enregistrement et sa modif
                                                $arr[] = true;
                                        }
                                } //fin if
                        }
                        //
                        if (isset ($arr))
                        {
                                return $arr;
                        } //fin if
                } //fin if
                //
        } //fin  baseExist()
        /**
         * @description charge la config d'une base
         * @param string $oBase_id
         * @return object
         */
        function baseLoadParam($oBase_id)
        {
                //on charge toutes les base
                $aObjet = $this->baseLoadList();
                //on charge celle choisie
                if (isset ($aObjet[$oBase_id]))
                {
                        return $aObjet[$oBase_id];
                }
                //
        }
        //
        function baseLoadList()
        {
                //

                $fFile = CONFIG_DIR . 'baseConfig';
                //
                if (file_exists($fFile))
                {
                        if (file_exists($fFile))
                        { //on charge  le fichier texte, chaque ligne est un objet
                                $aObjet = unserialize(file_get_contents($fFile));
                        }
                }
                //

                if (!empty ($aObjet))
                {
                        ksort($aObjet);
                        return $aObjet;
                }
                //
        }
        //
        function mysql_import_display()
        {
                $this->mysql_import_action();
                //liste des serveurs mysql
                $aContent[] = '<div class="row">';
                $aContent[] = '<div class ="divLeft">' . $this->aParamLang['base']['libelle_1'] . ' <b>MYSQL</b> : </div>';
                $aContent[] = '<div class ="divRight">';
                $aContent[] = $this->buildSelectBaseOptions('mysql');
                $aContent[] = '<input type="submit" name="btn_validerSelect" value="OK" class="inputSubmit"  />';
                $aContent[] = '</div>';
                $aContent[] = '</div>';
        }
        //
        function mysql_getFieldsUserGroups()
        {
                if (!empty ($_POST['btn_loadUsers']))
                {
                        $this->mysql_getConnexion();
                        $table = $this->aMysqlTableUserGroups['table'];
                        $field_id = $this->aMysqlTableUserGroups['field_id'];
                        $field_groupeType = $this->aMysqlTableUserGroups['field_groupeType'];
                        $sQuery = 'SELECT ' . $field_id . ' ,' . $field_groupeType;
                        $sQuery .= ' FROM ' . $table;
                        $sQuery .= ' ORDER BY ' . $field_groupeType;
                        $res = mysql_query($sQuery);
                        while ($row = mysql_fetch_assoc($res)) //on ne récupère pas le champ de la clé primaire
                        {
                                $aFields[$row[$field_id]] = $row[$field_groupeType];
                        }
                        //
                        //
                        if (isset ($aFields))
                        {
                                $_SESSION['Ldap2Mysql']['field_groupeType'] = $aFields;
                                return $aFields;
                        } //fin if
                        //
                }
                else
                {
                }
                return $_SESSION['Ldap2Mysql']['field_groupeType'];
                //
        }
        /**
        * @description crée les champs gérant l'importation ldap
        * @return void
        */
        function mysql_createFields($aFieldsToAdd)
        {
                //on construit
                foreach ($aFieldsToAdd as $field => $value)
                {
                        $aQuery[] = 'ALTER TABLE ' . $this->sMysqlTableToUpdate . ' ADD ' . $field . ' ' . $value;
                }
                //
                foreach ($aQuery as $sQuery)
                {
                        $res = mysql_query($sQuery);
                }
                //
        }
        //
        /**
                  * @description vérifie si les champs gérant l'importation ldap existent
                * @return mixed bool si true ou array $aFieldsToAdd nom des champs à créer
        */
        //
        function mysql_isFieldsExists()
        {
                //
                $sQuery = 'SHOW COLUMNS  FROM ' . $this->sMysqlTableToUpdate;
                $res = mysql_query($sQuery);
                //modfication de la table
                $aFieldsToAdd['ldap_user_id'] = 'varchar(255) not null';
                $aFieldsToAdd['ldap_config_import_id'] = 'varchar(80) not null';
                $aFieldsToAdd['ldap_last_update'] = 'datetime not null';
                //
                $aFound = array ();
                while ($row = mysql_fetch_assoc($res)) //on ne récupère pas le champ de la clé primaire
                {
                        //on vérifie si le champ existe dans la liste
                        if (in_array($row['Field'], array_keys($aFieldsToAdd)))
                        {
                                //on le comptabilise
                                $aFound[] = $row['Field'];
                        }
                }
                // le nombre de champs voulu doit être = au nombre de champ trouvé
                if (count($aFound) == count($aFieldsToAdd))
                {
                        //tous les champs existent
                        return true;
                }
                else
                {
                        //certains n'existent pas on envoi toute la liste
                        return $aFieldsToAdd;
                }
                //
        } //fin  mysql_isFieldsExists()
        //
        function mysql_getImportedUsers()
        {
                $arr = $this->ldap_import_loadConfig($_POST['select_configImport']);
                $oMysql = $arr['oMysql'];
                if (empty ($oMysql->port))
                {
                        $oMysql->port = 3306; //port ldap par défaut
                }
                //on transmet un tableau qui représente les propriétés d'une base
                mysql_connect($oMysql->host, $oMysql->login, $oMysql->pass);
                mysql_select_db($oMysql->nom);
                $sQuery = 'SELECT * FROM ' . $this->sMysqlTableToUpdate . ' WHERE ldap_user_id NOT LIKE \'\'';
                //
                $res = mysql_query($sQuery);
                while ($row = mysql_fetch_assoc($res))
                {
                        $champ['ldap_config_import_id'] = $row['ldap_config_import_id'];
                        $champ['ldap_last_update'] = $row['ldap_last_update'];
                        $champ['ldap_user_id'] = $row['ldap_user_id'];
                        $arraytemp[] = $champ;
                }
                return $arraytemp;
                //
        } //fin function mysql_getImportedUsers()
        //
        function mysql_getFields()
        {
                //
                if (!empty ($_POST['select_listeBase_mysql']))
                {
                        $oBase_id = $_POST['select_listeBase_mysql'];
                        //on charge sa config
                        $object = $this->baseLoadParam($oBase_id);
                        //
                        $oMysql = new stdClass();
                        //
                        foreach ($object as $key => $obj)
                        {
                                $oMysql-> $key = $obj->value;
                        }
                        @ mysql_connect($oMysql->host, $oMysql->login, $oMysql->pass);
                        //
                        $bSelectSucceed = @ mysql_select_db($oMysql->nom);
                        if ($bSelectSucceed == true)
                        {
                                //
                                $sQuery = 'SHOW COLUMNS FROM ' . $this->sMysqlTableToUpdate;
                                $res = mysql_query($sQuery);
                                while ($row = mysql_fetch_assoc($res)) //on ne récupère pas le champ de la clé primaire
                                {
                                        if ($row['Key'] <> 'PRI')
                                        {
                                                $aFields[$row['Field']] = $row;
                                        }
                                }
                                if (isset ($aFields))
                                {
                                        //on stocke la structure détaillée des tables( type de champ etc..) pour l'enregistrement de la config
                                        $_SESSION['Ldap2Mysql']['mysql_detailedListFields'] = $aFields;
                                        //on récupère uniquement la liste des champs
                                        $liste = array_keys($aFields);
                                        //on construit un tableau associatif pour le contrôle select
                                        foreach ($liste as $value)
                                        {
                                                $_SESSION['Ldap2Mysql']['mysql_ListFields'][$value] = $value;
                                        }
                                        //on ne renvoi que le liste des champs
                                        return $_SESSION['Ldap2Mysql']['mysql_ListFields'];
                                }
                                else
                                {
                                        return null;
                                } //fin if
                        }
                        else
                        {
                                $_SESSION['Ldap2Mysql']['errorConnexionMysql'] = $this->aParamLang['config_import']['errorConnexionMysql'];
                        } //fin if
                }
                else
                {
                        return null;
                } //fin if
                //
        }
        //
        /**
         *
         * @description  crée une ressource mysql
         * @return bool si pas de connexion
         */
        function mysql_getConnexion()
        {
                //
                if (!empty ($_POST['select_configImport']))
                {
                        $arr = $this->ldap_import_loadConfig($_POST['select_configImport']);
                        $oMysql = $arr['oMysql'];
                        //
                        if (empty ($oMysql->port))
                        {
                                $oMysql->port = 3306; //port ldap par défaut
                        }
                        //on transmet un tableau qui représente les propriétés d'une base
                        $resource_link = @ mysql_connect($oMysql->host, $oMysql->login, $oMysql->pass);
                        //
                        if ($resource_link)
                        {
                                mysql_select_db($oMysql->nom);
                        }
                        else
                        {
                        }
                }
                else
                {
                        return false;
                }
        }
        //
        /**
         * @description fonction récupérant les logins de la base mysql
         *
         * @return array associatif liste  des logins
         */
        function mysql_getAllLogin()
        {
                //construction de la requête
                $sQuery = 'SELECT ' . $this->champ_login . ' FROM ' . $this->sMysqlTableToUpdate;
                //
                $res = mysql_query($sQuery);
                $aLogin = array ();
                //
                while ($row = @ mysql_fetch_assoc($res))
                {
                        $aLogin[$this->champ_login] = $row[$this->champ_login];
                }
                return $aLogin;
        }
        /**
         * @description fonction récupérant les personnes déjà importées
         *
         * @param string le nom de la config d'importation
         * @return array associatif liste objet des personnes importées
         */
        function mysql_getInsertedUsers($sldap_config_import_id)
        {
                //connexion et création d'une ressource
                $sWhere = '  WHERE ldap_config_import_id ' . "='" . $sldap_config_import_id . "'";
                //construction de la requête
                $sQuery = 'SELECT * FROM ' . $this->sMysqlTableToUpdate . $sWhere;
                //
                $res = @ mysql_query($sQuery);
                $arr = array ();
                //
                while ($row = @ mysql_fetch_assoc($res))
                {
                        $oInsertedUser = new stdClass();
                        $oInsertedUser->ldap_config_import_id = $row['ldap_config_import_id'];
                        $oInsertedUser->ldap_user_id = $row['ldap_user_id'];
                        $oInsertedUser->ldap_last_update = $row['ldap_last_update'];
                        //on crée un tableau respectant l'unicité par ldap_user_id
                        $arr[$oInsertedUser->ldap_user_id] = $oInsertedUser;
                }
                //
                return $arr;
                //
        } //fin function mysql_getInsertedUsers($sldap_config_import_id)
        //
        /**
         * @description fonction qui va initialiser un tableay dont les clés sont les champs mysql et les valeurs
         * sont la valeur default du champ si elle existe, si le champ est obligatoire
         *  on lui asscie un valeur temporaire sinon une valeur null  ou
         */
        function mysql_initializeFieldsToImport()
        {
                //
                $aMysql_detailedListFields = $_SESSION['Ldap2Mysql']['mysql_detailedListFields'];
                //on initialise un tabelau avec des valeurs par défaut
                foreach ($aMysql_detailedListFields as $champ => $champDetail)
                {
                        //on vérifie la nullité de chaque champ de la base
                        if ($champDetail['Null'] == 'YES')
                        {
                                if (!empty ($champDetail['Default']))
                                {
                                        //on prend la valeur par défaut du champ
                                        $aMysql_ListFields[$champ] = $champ . "='" . $champDetail['Default'] . "'";
                                }
                                else
                                {
                                        //sinon on lui affecte la valeur null
                                        $aMysql_ListFields[$champ] = $champ . "=null";
                                }
                        }
                        else
                        {
                                if (!empty ($champDetail['Default']))
                                {
                                        //on prend la valeur par défaut du champ
                                        $aMysql_ListFields[$champ] = $champ . "='" . $champDetail['Default'] . "'";
                                }
                                else
                                {
                                        //@promethee
                                        //on prompose un mot de passe pour promethee
                                        if (isset ($this->champ_motDePasse) AND $champ == $this->champ_motDePasse)
                                        {
                                                $aMysql_ListFields[$this->champ_motDePasse] = $this->champ_motDePasse . "='" . $this->mysql_generate_passWord() . "'";
                                        }
                                        elseif (isset ($this->champ_login) AND $champ == $this->champ_login)
                                        {
                                                //on récupère les logins de la base
                                                $aLogin = $this->mysql_getAllLogin();
                                                //on génère un login unique
                                                $newLogin = '';
                                                while (in_array($newLogin = $this->mysql_generate_login(), $aLogin))
                                                {
                                                        //la boucle tourne jusqu'à trouver un login unique
                                                }
                                                //à la fin, on récupère notre variable $newLogin
                                                $aMysql_ListFields[$this->champ_login] = $this->champ_login . "='" . $newLogin . "'";
                                        }
                                        else
                                        {
                                                //on remplit les champs non nul avec une valeur temporaire  unique
                                                //à ne pas confondre avec unique_id qui, lui, vient du ldap
                                                $aMysql_ListFields[$champ] = $champ . "='tmp" . $this->get_temporary_unique_id() . "'";
                                        }
                                }
                                //
                        } //fin if
                } //fin foreach
                return $aMysql_ListFields;
        } //fin function mysql_initializeFieldsToImport()
        //
        /**
         * @description  ajoute les utilisateurs dans la base mysql
         * @param  array $aQuery les requêtes d'insertion
         * @return  ajoute des valeurs booléennes de succès (insertion ou mise
         * à jour ou non succès dans chaque  objet ($aoUser)
         *
         */
        function mysql_addUsersFromLdap($aQuery, $aUpdateOrInsert)
        {
                //
                foreach ($aQuery as $unique_id => $sQuery)
                {
                        $res = mysql_query($sQuery);
                        $userLog = new stdClass();
                        $userLog->ldap_user_id = $unique_id;
                        $userLog->date_import = date('Y-m-d H:i:s');
                        //
                        if (mysql_affected_rows() == (int) 1)
                        {
                                $this->set_variableSessionUser($unique_id, 'userImported', $aUpdateOrInsert[$unique_id]);
                        }
                        else
                        {
                                $this->set_variableSessionUser($unique_id, 'userImported', 'failed');
                        }
                        //
                }
                //
        }
        //
        /**
        * @description crée un login temporaire pour promethée
        * @return string login
        */
        function mysql_generate_login()
        {
                $aBannerList = array (
                        'pute'
                );
                $aVoyelle = array (
                        'a',
                        'e',
                        'i',
                        'o',
                        'u',
                        'ou',
                        'on'
                );
                $aConsonne = array (
                        'b',
                        'c',
                        'd',
                        'f',
                        'g',
                        'j',
                        'k',
                        'l',
                        'm',
                        'n',
                        'p',
                        'r',
                        's',
                        't',
                        'v',
                        'w',
                        'x',
                        'z'
                );
                //
                foreach ($aConsonne as $consonne)
                {
                        if (!in_array($consonne, $aVoyelle))
                        {
                                shuffle($aVoyelle);
                                shuffle($aConsonne);
                                $str1 = $aConsonne[0] . $aVoyelle[0];
                                $arr1[] = $str1;
                                //
                                shuffle($arr1);
                                shuffle($aVoyelle);
                                shuffle($aConsonne);
                                $str2 = $aConsonne[0] . $aVoyelle[0];
                                $arr2[] = $str2 . $arr1[0];
                                //
                                shuffle($arr2);
                                $arr[] = $arr2[0];
                                $arr[] = $str1 . $str2;
                                //
                                shuffle($arr2);
                                shuffle($aVoyelle);
                                shuffle($aConsonne);
                                $arr[] = $aVoyelle[0] . $arr2[0] . $aConsonne[0] . $aVoyelle[1];
                                //
                                shuffle($arr2);
                                shuffle($aConsonne);
                                shuffle($aVoyelle);
                                $arr[] = $aVoyelle[0] . $arr2[0] . $aConsonne[0] . $aVoyelle[1];
                                //
                                shuffle($aConsonne);
                                shuffle($aVoyelle);
                                $arr[] = $aVoyelle[0] . $aConsonne[1] . $aConsonne[0] . $aVoyelle[1];
                                //
                                shuffle($aConsonne);
                                shuffle($aVoyelle);
                                $arr[] = $aVoyelle[0] . $aConsonne[1] . $aConsonne[0] . $aVoyelle[1];
                                //
                                shuffle($arr);
                                //
                                if (!in_array($arr[0], $aBannerList))
                                {
                                        //
                                        return $arr[0];
                                } //fin if
                        }
                        //
                }
        }
        //
        /**
         * @description crée un mot de passe temporaire
         * @return string $pass de 4 caract maj et min
         */
        function mysql_generate_passWord($passLength = 4)
        {
                $chiffres = substr(str_shuffle('23456789'), 0, $passLength / 2);
                //
                $lettres = substr(str_shuffle('abcdefghjkmnpqrstuvwxyz'), 0, $passLength / 2);
                $pass = str_shuffle($chiffres . $lettres);
                return $pass;
        }
        //
        /**
         * @description récupère les libellés du module selon la langue choisie
         * par défaut langue fr_FR
         */
        function getParamLang($lang)
        {
                //
                require_once 'Lang/Lang.php';
                //
                $aParamLang = Lang :: getParam($lang);
                $this->aParamLang = $aParamLang['Ldap2Mysql'];
                //
        }
        //
        /**
         * @description construit un select selon le type de base
         */
        function buildSelectListeBase($aoListeBase, $aOption = null, $selected = null)
        {
                //si aucune base n'est configurée
                $aData = array (
                        '' => $this->aParamLang['base']['noBase']
                );
                //
                if (isset ($aoListeBase))
                {
                        //
                        $aData = null;
                        foreach ($aoListeBase as $key => $propertieValue)
                        {

                               if ($this->mode_dey==false AND substr($propertieValue->id->value,0,5)=='mysql')
                               {
                                    continue;
                               }
                                //
                                if (isset ($propertieValue->nom->value))
                                {
                                        //
                                        if ($key == 'NoAuthentification')
                                        {
                                                $aData[$key] = $propertieValue->nom->value;
                                        }
                                        else
                                        {
                                                //
                                                switch ($propertieValue->type->value)
                                                {
                                                        //
                                                        case 'activedirectory' :
                                                                $libelle = 'Active directory';
                                                                break;
                                                                //
                                                        case 'openldap' :
                                                                $libelle = 'OpenLdap';
                                                                break;
                                                                //

                                                        default :

                                                                $libelle = $propertieValue->type->value;
                                                                break;
                                                } //fin switch
                                                //
                                                $aData[$key] = $libelle . ' &raquo;  ' . $propertieValue->nom->value;
                                        }
                                }
                                //
                        }
                        //
                }
                //
                if ($aOption == null)
                {
                        $aOption = array (
                                'onchange="javascript:submit();"'
                        );
                }
                //
                $aContent[] = $this->formSelectComponent('select_listBase', $aData, $aOption, $selected);
                $aContent[] = $this->formInputComponent('submit', 'btn_validerSelect', 'OK', array (
                        'class' => 'inputSubmit'
                ));
                //
                return implode("\n", $aContent);
        }
        //
        /**
         * @description construit le menu
         * @return string menu
         */
        function ldap_getLinks($typeMenu)
        {
                //liste des liens
                $array = array (
                        'baseConfig' => $this->aParamLang['base']['lien_1'],
                        'importConfig' => $this->aParamLang['base']['lien_2'],
                        'import' => $this->aParamLang['base']['lien_3'],
                        'consult' => $this->aParamLang['base']['lien_5'],
                        'authConfig' => $this->aParamLang['base']['lien_4'],
                        'manual' => $this->aParamLang['base']['lien_6']
                );
                //l'action est toujours la première valeur
                $tab = explode('&', $_SERVER['QUERY_STRING']);
                $action = $tab[0];
                //
                //en quittant l'ecran de l'import, on supprime le tableau  de sessions
                if ($action <> 'import')
                {
                        if (isset ($_SESSION['Ldap2Mysql']['aoUser']))
                        {
                                unset ($_SESSION['Ldap2Mysql']['aoUser']);
                        }
                }
                //
                //
                if (!in_array($action, array_keys($array)))
                {
                        $action = 'baseConfig';
                } //fin if
                //
                foreach ($array as $key => $value)
                {
                        //
                        if (isset ($action) AND $action == $key)
                        {
                                $str = '<li class="selected">' . $value . '</li>';
                        }
                        else
                        {
                                $urlQueryString = $key . '&amp;item=23';
                                //
                                $str = '<li><a href="?' . $urlQueryString . '" >' . $value . '</a></li>';
                        }
                        $aLien[] = $str;
                }
                //
                return '<ul  class="ldapMenu">' . implode('', $aLien) . '</ul>';
                //
        }
        //
        function formInputComponent($type, $elementName, $value = null, $aOptions = array ())
        {
                //text
                $attributs['password'] = array (
                        'type' => 'password',
                        'name' => 'name',
                        'value' => null,
                        'size' => 35
                );
                //text
                $attributs['text'] = array (
                        'type' => 'text',
                        'name' => 'name',
                        'value' => null,
                        'size' => 35,
                        'maxlength' => 50
                );
                //bouton image
                $attributs['image'] = array (
                        'type' => 'image',
                        'name' => 'name',
                        'src' => null,
                        'title' => null,
                        'alt' => null
                );
                //hidden
                $attributs['hidden'] = array (
                        'type' => 'hidden',
                        'name' => null,
                        'value' => null
                );
                //submit
                $attributs['submit'] = array (
                        'type' => 'submit',
                        'name' => 'value'
                );
                //on construit  l'objet
                $oComponent = new stdClass();
                $oComponent->name = $elementName;
                $oComponent->type = $type;
                $oComponent->value = $value;
                //
                if (in_array($oComponent->type, array_keys($attributs)))
                {
                        foreach ($oComponent as $key => $value)
                        {
                                //on complète le tableau si clé manquante
                                $attributs[$oComponent->type][$key] = $value;
                        }
                        //
                        $sComponent = '<input';
                        foreach ($attributs[$oComponent->type] as $key => $value)
                        {
                                $sComponent .= ' ' . trim($key) . '="' . trim($value) . '"';
                        }
                        //construction des attributs supplémentaires
                        $sOptions = '';
                        if (!empty ($aOptions))
                        {
                                foreach ($aOptions as $key => $value)
                                {
                                        $sOptions .= ' ' . trim($key) . '="' . trim($value) . '" ';
                                }
                        }
                        $sComponent .= $sOptions . ' />';
                        //
                        return $sComponent;
                        //
                }
                else
                {
                        echo 'le type du input demandé n\'existe pas !';
                }
                //
        }
        //
        /**
         * @description initialise un  objet de champde formulaire
         * @return    object $oComponent
         */
        function formInitializeObject($sBaseType)
        {
                switch ($sBaseType)
                {
                        //ldap
                        case 'activedirectory' :
                                $aBase = array (
                                        'id',
                                        'type',
                                        'nom',
                                        'host',
                                        'port',
                                        'dn',
                                        'ldap_user_id_field',
                                        'login_field',
                                        'login',
                                        'pass'
                                );
                                break;
                        case 'openldap' :
                                $aBase = array (
                                        'id',
                                        'type',
                                        'nom',
                                        'host',
                                        'port',
                                        'dn',
                                        'ldap_user_id_field',
                                        'login_field',
                                        'login',
                                        'pass'
                                );
                                break;
                                //ldap
                        case 'mysql' :
                                $aBase = array (
                                        'id',
                                        'type',
                                        'nom',
                                        'host',
                                        'port',
                                        'login',
                                        'pass'
                                );
                                break;
                                //
                        case 'cas' :
                                $aBase = array (
                                        'id',
                                        'type',
                                        'nom',
                                        'host',
                                        'port',
                                        'path'
                                );
                                break;
                                //
                }
                //on instancie un objet vide sans class
                //dont les propriétés seront des objets aussi
                $oBase = new stdClass();
                foreach ($aBase as $propertie)
                {
                        $oPropertie = new stdClass();
                        //
                        $oPropertie->defaultValue = null;
                        $oPropertie->value = null;
                        $oPropertie->required = true;
                        $oPropertie->errorMessage = $this->aParamLang['base']['errorMessage'];
                        //
                        $oBase-> $propertie = $oPropertie;
                }
                //
                return $oBase;
        }
        //
        /**
         * @description fonction de construction d'un select
         * @param1 string <nom du select>
         * @param2 array de type associatif (les cles sont transformées en values)
         * @param3 array ajoute des fonctionnalités au select exemple du javascript, une classe etc
         * @return string
         */
        function formSelectComponent($selectName, $adata, $aOption = null, $default = null)
        {
                //
                $sOption = '';
                //
                if (is_array($aOption))
                {
                        $sOption = implode(' ', $aOption);
                }
                //
                $selected = $default;
                if (isset ($default) AND in_array($default, array_keys($adata)))
                {
                        $selected = $default;
                }
                elseif (!empty ($_POST[$selectName]))
                {
                        $selected = $_POST[$selectName];
                }
                //
                $select[] = '<select   name="' . $selectName . '" ' . $sOption . '   >';
                //
                //on veut la première valuer qui s'affiche
                if ($default == 'freeze')
                {
                        //$select[] = '<option value="">&nbsp;</option>';
                }
                else
                {
                        $select[] = '<option value="">&nbsp;</option>';
                }
                //
                if (!empty ($adata))
                {
                        //
                        foreach ($adata as $key => $value)
                        {
                                if ($key == $selected)
                                {
                                        $select[] = '<option value="' . $key . '" selected="selected" >' . $value . '</option>';
                                }
                                else
                                {
                                        $select[] = '<option value="' . $key . '">' . $value . '</option>';
                                }
                        }
                        //
                }
                //
                $select[] = '</select>';
                //
                return "\n" . implode("\n", $select);
                //
        }
        //
        /**
         * @description récupère la liste des bases ldap et mysql
         */
        function getAllBases()
        {
                $aObjects = $this->baseLoadList();
                //
                if (isset ($aObjects))
                {
                        //initialisation
                        $arr = array (
                                'activedirectory' => '',
                                'openldap' => '',
                                'mysql' => ''
                        );
                        //
                        foreach ($aObjects as $key => $value)
                        {
                                //
                                //
                                if (isset ($value->type->value))
                                {
                                        if (in_array($value->type->value, array_keys($arr)))
                                        {
                                                //
                                                switch ($value->type->value)
                                                {
                                                        //on charge les 2 types de ldap
                                                        case 'activedirectory' :
                                                                $arr['ldap'][$key] = $value->nom->value;
                                                                //
                                                                break;
                                                                //
                                                        case 'openldap' :
                                                                //
                                                                $arr['ldap'][$key] = $value->nom->value;
                                                                //
                                                                break;
                                                                //
                                                        case 'mysql' :
                                                                //
                                                                $arr['mysql'][$key] = $value->nom->value;
                                                                break;
                                                                //
                                                }
                                        } //fin if
                                } //fin if
                                //
                        } //fin foreach
                        return $arr;
                } //fin if
        } //fin function getAllBases()
        //
        /**
         * @description fonction ajoutant une propriété  ou modifiant une valeur d'une propriété dans la session au tableau des users
         * $_SESSION['Ldap2Mysql']['aoUser'][$unique_id]['variable']
         */
        function set_variableSessionUser($unique_id, $variable, $value)
        {
                $_SESSION['Ldap2Mysql']['aoUser'][$unique_id][$variable] = $value;
        }
        //
        /**
         * @description supprime des variables de sessions dont on n'a plus besoin
         */
        function delete_var_session()
        {
                unset ($_SESSION['Ldap2Mysql']['aoUser']);
                unset ($_SESSION['Ldap2Mysql']['listFieldsToImport']);
                unset ($_SESSION['Ldap2Mysql']['ldap_ListFields']);
                unset ($_SESSION['Ldap2Mysql']['ldap_listFieldsToImport']);
                unset ($_SESSION['Ldap2Mysql']['mysql_ListFields']);
                unset ($_SESSION['Ldap2Mysql']['mysql_listFieldsToImport']);
                unset ($_SESSION['Ldap2Mysql']['mysql_detailedListFields']);
        }
        //
        //
        /**
         * @description fonction de création d'une chaine unique servant pour l'insertion dans la base pour éviter les doublons
         */
        function get_temporary_unique_id()
        {
                list ($usec, $sec) = explode(' ', microtime());
                $time = (float) $sec + ((float) $usec * 100000);
                mt_srand($time);
                $unique_id = md5(uniqid(mt_rand(), 1));
                return $unique_id;
        }
        function display_warning($messageWarning, $NumeroLigne = null)
        {
                $line = str_repeat('-', strlen($messageWarning) * 3 / 2);
                $warning = '<span style="font-family: \'trebuchet MS\', arial, sans-serif;font-size: 0.7em;color: red;">';
                $warning .= $line . ' <br/> ';
                $warning .= '  <span style="font-weight:bold;">Attention : </span><br/>';
                $warning .= $messageWarning . '<br/>' . $line . ' <br/> ';
                $warning .= '</span>';
                //
                echo $warning;
                //
        }
        function run()
        {
                //
                $tab = explode('&', $_SERVER['QUERY_STRING']);
                $action = $tab[0];
                //
                switch ($action)
                {
                        //
                        case 'manual' :
                                $this->display($this->manual_view());
                                break;
                                //
                        case 'authConfig' :
                                $this->display($this->authModeConfig_controller());
                                break;
                                //
                        case 'import' :
                                $this->display($this->ldap_import_controller());
                                break;
                                //
                        case 'importConfig' :
                                //
                                $this->display($this->importConfig_controller());
                                break;
                                //
                        case 'baseConfig' :
                                $this->display($this->baseConfig_controller());
                                break;
                                //
                        case 'consult' :
                                $this->display($this->consult_controller());
                                break;
                                //
                        default :
                                $this->display($this->baseConfig_controller());
                                break;
                } // fin switch
                //
        }
        //
        /**
         * @description remplit le div principal avec le contenu de chaque interface
         */
        function display($sControlPanel)
        {
                $aContent[] = '<form id="Ldap2Mysql_form" action="' . str_replace('&', '&amp;', $_SERVER['REQUEST_URI']) . '" method="post">';
                $aContent[] = '<div id="ldap2Mysql">';
                //
                $aContent[] = '<div class="row">';
                $aContent[] = $this->ldap_getLinks('menu');
                $aContent[] = '</div>';
                //
                $aContent[] = '<div class="row">&nbsp;</div>';
                //on récupère une chaine representant le panneau de chaque type de serveur
                $aContent[] = $sControlPanel;
                $aContent[] = '<div class="row">&nbsp;</div>';
                $aContent[] = '<hr class="ligneHorizontale" />';
                //
                $aContent[] = '</div>';
                $aContent[] = '<div style="text-align:left;font-size:10px;font-style: italic;font-family:  \'georgia\',sans-serif;">' . $this->sDate_version . '</div>';
                $aContent[] = '</form>';
                //on créé le html de sortie avec un retour chariot pour chaque ligne
                echo implode("\n", $aContent);
        }
} //fin classe Base
?>







