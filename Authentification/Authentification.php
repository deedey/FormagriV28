<?php
/*-----------------------------------------------------------------------
                             modules : Authentification
                             projet : Ldap2Mysql-Authentification
                             CHEFS DE PROJETS : P.BAFFALIE, S.GRESSARD

 Description :      gestion du mode d'authentifications externes
                             -
 Environnement PHP   : PHP4 OU PHP5
 author                    : nordine.zetoutou<nzetoutou@educagri.fr>
 date de création     : 25 sept. 06
 Historique de modification :
                             -
                             -
 version                   :        1.0
-----------------------------------------------------------------------*/
/**
   * @author Nordine Zetoutou  - <nzetoutou@educagri.fr>
   * @package Ldap2Mysql-Authentification
   * @module Authentification
   * @description gestion des bases d'authentification mysql, ldap et cas
   * @dependance  activation de l'extension php_ldap
   * @dependance  si CAS comme serveur d'authentification activation  de l'extension=php_curl.dll
   *
   * @license GPL
   * @version 1.0
   * @date :  25 sept. 2006

*/
class Authentification
{
        var $sTableLogin = 'utilisateur';
        //
        function Authentification()
        {
                define('CONFIG_DIR', 'Ldap2Mysql/config');
        }
        //
        /**
         * @description charge le mode d'authentification extrene
         * @return mixed object|bool $oBase ou false
         */
        function getAuthMode()
        {
                //
                if (file_exists(CONFIG_DIR))
                {
                        //baseAuth est le nom du fichier du mode d'authentification
                        $fFile = CONFIG_DIR . '/baseAuth';
                        if (file_exists($fFile))
                        {
                                $sFileContent = file_get_contents($fFile);
                                $oBase = @ unserialize($sFileContent);
                                return $oBase;
                        }
                }
                else
                {
                        $sMessage = "<p style='color:red;font-weight:bold;'> ************* </br>";
                        $sMessage .= "le répertoire de configuration des authentifications externes ne peut être atteint";
                        $sMessage .= "</br>*************</p>";
                        echo $sMessage;
                }
        }
        //
        /**
         * @description authentifie un utilisateur dans l'annuaire ldap
         * @param object $oBase : config du serveur d'authentification
         * @param string $login
         * @param string $pass
         *
         * @return object $oUser
         */
        function ldap_authenticate($oBase, $login, $pass)
        {
         //$affiche = "ldap_authenticate($oBase, $login, $pass)";echo $affiche;exit;
                //
                $ressource_link = ldap_connect($oBase->host, $oBase->port);
                //
                //
                switch ($oBase->type)
                {
                        //
                        case 'openldap' :
                                //
                                $oBase->dn= $oBase->login_field.'='.$login.','.$oBase->dn;
                                $bLdap_bind = @ldap_bind($ressource_link, $oBase->dn, $pass);
                                break;
                                //
                        case 'activedirectory' :
                                //
                                $bLdap_bind = @ldap_bind($ressource_link, $login, $pass);
                                break;
                                //
                } //fin switch
                //
                if ($bLdap_bind)
                {
                        //
                        $result = ldap_search($ressource_link, $oBase->dn, '(' . $oBase->login_field . '=' . $login . ')');
                        $aEntries = ldap_get_entries($ressource_link, $result);
                        //si une entré existe alors l'authentification est ok
                        if ($aEntries["count"] == (int) 1)
                        {
                                $entry = ldap_first_entry($ressource_link, $result);
                                $attrs = ldap_get_attributes($ressource_link, $entry);
                                //on construit un objet $oUser
                                $oUser = new stdClass();
                                //
                                $aKeys = array_keys($attrs);
                                foreach ($aKeys as $value)
                                {
                                        //
                                        if (!is_int($value))
                                        {
                                                //les clés attributs ldap contiennent des majuscules
                                                $propriete = strtolower($value);
                                                //
                                                $oUser-> $propriete = $attrs[$value][0];
                                                //on récupère le nom du champ contenant l'id ldap
                                                if ($propriete == $oBase->ldap_user_id_field)
                                                {
                                                        //on récupère la valeur de ce champ
                                                        $oUser->ldap_user_id = $attrs[$value][0];
                                                }
                                        }
                                }
                                return $oUser;
                        }
                        else
                        {
                                return 'notExist';
                        }
                        //
                        ldap_free_result($result);
                        //
                }
                else
                {
                        return 'noConnexion';
                }
        }
        //
        /**
         * @description récupère un utilisateur de la base mysql
         * @param object $oUser : possédant entre autres son id ldap
         *
         * return object $oUser
         */
        function mysql_authenticate($oMysql, $ldap_user_id)
        {
                $ressource_link = @ mysql_connect($oMysql->host, $oMysql->root, $oMysql->passbdd);
                if ($ressource_link)
                {
                        mysql_select_db($oMysql->db);
                        $sQuery = 'SELECT * FROM ' . $this->sTableLogin;
                        $sQuery .= " WHERE ldap_user_id='" . strtolower($ldap_user_id) . "'";
                        $result = mysql_query($sQuery);//echo $sQuery;exit;
                        //
                        if ($result)
                        {
                                //on ne récupère qu'un objet
                                return mysql_fetch_object($result);
                        } //fin if
                        //
                        //
                } //fin if
                //
        }
}
?>