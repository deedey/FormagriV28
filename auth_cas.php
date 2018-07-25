<?php
//<code>
/*********************************************************************************************************/
include ("include/UrlParam2PhpVar.inc.php");
require_once 'Authentification/Authentification.php';
require ("admin.inc.php");
require ('langfr.inc.php');
//
$oAuthentification = new Authentification();
//on vérifie quel  mode d'authentification est mis en place
$oBase = $oAuthentification->getAuthMode();
//l'objet $oBase nous indique lequel ( si ldap ou cas )
//
if (isset($oBase->type) && ($oBase->type == 'openldap' || $oBase->type == 'activedirectory') && (!isset($login) || (isset($login) && $authentifie == 'oui')))
{
   echo "<form name='formLdap' method='POST' action=\"".$_SERVER['PHP_SELF']."\">";
    ?>
    <TABLE cellspacing="0" cellpadding="0" border="0" width="80%">
      <TR bgcolor="#002D45"><TD height='10' colspan=3>&nbsp;</TD></TR>
      <TR bgcolor="#002D45" width="100%" height="92">
         <TD align="center" Valign="middle">
             <TABLE cellSpacing="3" cellPadding="1" border="0">
             <TR><TD></TD>
               <TD align='left' colspan=3>
                 <?php
                   if (isset($login) && $authentifie == 'oui'){
                            $_SESSION['authentifie'] = 'non';
                            echo "<FONT size=+1 color=White family=arial>$mess_error_ident<P></FONT>";
                   }
                   echo "<FONT size=+2 color=White family=arial>";
                        if ($oBase->type == 'openldap')
                            echo $mess_connect_ldap;
                        else
                            echo $mess_connect_activD;
                 ?>
                 </FONT><P>
                </TD>
              </TR>
              <TR>
                 <TD align='left' colspan=3>
                    <FONT size=+2 color=White family=arial><?php  echo $mess_auth_cod_acc ;?></FONT><P>
                 </TD>
              </TR>
              <TR>
                  <TD align=right noWrap>
                    <FONT color= "#FFFFFF"><B><?php  echo $mess_auth_util ;?></B>&nbsp;&nbsp;&nbsp;&nbsp;</FONT>
                  </TD>
                  <TD>
                     <INPUT maxLength="50" name="login" id="login">
                  </TD>
              </TR>
              <TR>
                 <TD align=right noWrap>
                    <FONT Color="#FFFFFF"><B><?php  echo $mess_auth_mdp ;?></B>&nbsp;&nbsp;&nbsp;&nbsp;</FONT>
                 </TD>
                 <TD>
                    <INPUT type='password' maxLength="20" name="pass" id="pass">
                 </TD>
              </TR>
              <TR>
                 <TD align='center' height="20" valign='absmiddle'>
                 </TD>
                 <TD align='left' colspan=3>
                    <INPUT Type="image" Name="SUBMIT" SRC="images/menu/valid.gif" border='0'>
                 </TD>
              </TR>
              </TABLE>
             </TD>
         </TR>
      </TABLE>
        <script type="text/javascript">
            document.formLdap.login.focus();
        </script>
      </FORM>
      <?php exit;

}
if (is_object($oBase))
{
        $bAuthentifie = false;
        $bAuthMode = true;
        //
        switch ($oBase->type)
        {
                //
                case 'openldap' :
                        if(!empty($login) AND !empty($pass))
                        {
                                        //on récupère soit un objet soit une chaine erreur
                                $authentificationResult = $oAuthentification->ldap_authenticate($oBase, $login, $pass);
                                //print_r ($oBase);
                                //echo "ldap_authenticate($authentificationResult ,$oBase, $login, $pass)";exit;
                                //si c'est un objet
                                if (is_object($authentificationResult)){
                                        $bAuthentifie = true;
                                        //on construit un objet de paramètres de mysql
                                        $oMysql = new stdClass();
                                        $SERVER = $adresse;
                                        $oMysql->host = $SERVER;
                                        $oMysql->db = $bdd;
                                        $oMysql->root = $log;
                                        $oMysql->passbdd = $mdp;
                                        $oMysql->port = '';
                                        //on interroge la base mysql avec l'id ldap inséré lors de son importation
                                        //on récupère un nouvel objet $oUser de mysql
                                        $login_ldap = $authentificationResult->ldap_user_id;
                                        $oMysqlUser = $oAuthentification->mysql_authenticate($oMysql, $authentificationResult->ldap_user_id);
                                        //si n'existe pas dans la base mysql
                                        if (!is_object($oMysqlUser)){
                                                $bMysqlUserExists = false;
                                        }else{
                                           $authentifie ='oui';
                                           $_SESSION['authentifie'] = $authentifie;
                                           $_SESSION['bAuthentifie'] = true;
                                           $_SESSION['bAuthMode'] = $bAuthMode;
                                           $_SESSION['login_ldap'] = $login_ldap;
                                        }
                                        //
                                }else{
                                     $authentifie ='non';
                                     $_SESSION['authentifie'] = $authentifie;
                                     $_SESSION['bAuthentifie'] = false;
                                     $bAuthentifie = false;
                                }
                        }
                        break;
                case 'activedirectory' :
                        if(!empty($login) AND !empty($pass)){
                                        //on récupère soit un objet soit une chaine erreur
                                $authentificationResult = $oAuthentification->ldap_authenticate($oBase, $login, $pass);

                                //si c'est un objet
                                if (is_object($authentificationResult)){
                                        $bAuthentifie = true;
                                        //on construit un objet de paramètres de mysql
                                        $oMysql = new stdClass();
                                        $SERVER = $adresse;
                                        $oMysql->host = $SERVER;
                                        $oMysql->db = $bdd;
                                        $oMysql->root = $log;
                                        $oMysql->passbdd = $mdp;
                                        $oMysql->port = '';
                                        //on interroge la base mysql avec l'id ldap inséré lors de son importation
                                        //on récupère un nouvel objet $oUser de mysql
                                        $login_ldap = $authentificationResult->ldap_user_id;
                                        $oMysqlUser = $oAuthentification->mysql_authenticate($oMysql, $authentificationResult->ldap_user_id);
                                        //si n'existe pas dans la base mysql
                                        if (!is_object($oMysqlUser)){
                                                $bMysqlUserExists = false;
                                        }else{
                                           $authentifie ='oui';
                                           $_SESSION['authentifie'] = $authentifie;
                                           $_SESSION['bAuthentifie'] = true;
                                           $_SESSION['bAuthMode'] = $bAuthMode;
                                           $_SESSION['login_ldap'] = $login_ldap;
                                        }
                                        //
                                }else{
                                     $authentifie ='non';
                                     $_SESSION['authentifie'] = $authentifie;
                                     $_SESSION['bAuthentifie'] = false;
                                     $bAuthentifie = false;
                                }
                        }
                        break;
                        //
                case 'cas' :

                        require_once 'Authentification/CAS/CAS/CAS.php';
                        // initialize phpCAS
                        phpCAS :: setDebug();
                        // initialize phpCAS

                        phpCAS :: client(CAS_VERSION_2_0, $oBase->host, 443, 'cas');
                        // force CAS authentication
                        if (isset($_SESSION['logout'])){
                            unset($_SESSION['logout']);
                            phpCAS ::logout();
                        }
                        phpCAS :: forceAuthentication();
                        //

                        $logincas=phpCAS::getUser();
                        //
                        if (isset($logincas)){
                           $_SESSION['logincas']=$logincas;
                           $_SESSION['bAuthentifie']= true;
                           $_SESSION['bAuthMode']=$bAuthMode;
                        }
                        break;
                        // dey
                case 'shibboleth' :
                           require_once "admin.inc.php";
                           $_SESSION['bAuthMode']=$bAuthMode;
                           $_SESSION['shibboleth'] = true;
                           $_SESSION['newHost'] = $oBase->host;
                           /*
                           ?><script language="Javascript">
                           alert('<?php echo $oBase->host;?>');
                            </script><?php
                           */

                           //header("Location: ".$oBase->host."?$adresse_http/index.php");
                        break;
                        // dey
        } //fin switch
        //

}
/*********************************************************************************************************/
?>
