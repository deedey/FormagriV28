<?php
//  fichier lang
if ($lg == "fr")
{
$msgShibAvert = " <strong>Le mode d'appel et d'authentification est une émulation du WAYF qui n'est pas encore implémenté.<p>
                  <font color='#1c7d90'><u>Liste des variables d'environnement envoyées par Shibboleth :</u><P>A partir de DisplayName jusqu'à Uid, <br />
                  cela concerne les données personnelles de l'utilisateur. On peut imaginer une routine qui demande à l'utilisateur<br />
                  s'il veut mettre à jour sur Formagri ses données personnelles en fonction de ce qu'il a reçu.<br />
                  Modifier son email ou encore son nom (en cas d'épousailles)....</font></strong><p>";
$msgShibInact = " <p><strong><font size=4 color=red>Shibboleth n'a rien renvoyé pour diverses raisons ou alors <p>
                  votre session Shibboleth n'est plus active</font><br />
                  Elle sert juste à vous authentifier et à rapatrier à la volée<br />
                       certaines données personnelles et relatives à l'environnement</strong>";
}
elseif ($lg == "en")
{
$msgShibAvert = " <strong>Le mode d'appel et d'authentification est une émulation du WAYF qui n'est pas encore implémenté.<p>
                  <font color='#1c7d90'><u>Liste des variables d'environnement envoyées par Shibboleth :</u><P>A partir de DisplayName jusqu'à Uid, <br />
                  cela concerne les données personnelles de l'utilisateur. On peut imaginer une routine qui demande à l'utilisateur<br />
                  s'il veut mettre à jour sur Formagri ses données personnelles en fonction de ce qu'il a reçu.<br />
                  Modifier son email ou encore son nom (en cas d'épousailles)....</font></strong><p>";
$msgShibInact = " <p><strong><font size=4 color=red>Shibboleth n'a rien renvoyé pour diverses raisons ou alors <p>
                  votre session Shibboleth n'est plus active</font><br />
                  Elle sert juste à vous authentifier et à rapatrier à la volée<br />
                       certaines données personnelles et relatives à l'environnement</strong>";
}
elseif ($lg == "ru")
{
$msgShibAvert = " <strong>Le mode d'appel et d'authentification est une émulation du WAYF qui n'est pas encore implémenté.<p>
                  <font color='#1c7d90'><u>Liste des variables d'environnement envoyées par Shibboleth :</u><P>A partir de DisplayName jusqu'à Uid, <br />
                  cela concerne les données personnelles de l'utilisateur. On peut imaginer une routine qui demande à l'utilisateur<br />
                  s'il veut mettre à jour sur Formagri ses données personnelles en fonction de ce qu'il a reçu.<br />
                  Modifier son email ou encore son nom (en cas d'épousailles)....</font></strong><p>";
$msgShibInact = " <p><strong><font size=4 color=red>Shibboleth n'a rien renvoyé pour diverses raisons ou alors <p>
                  votre session Shibboleth n'est plus active</font><br />
                  Elle sert juste à vous authentifier et à rapatrier à la volée<br />
                       certaines données personnelles et relatives à l'environnement</strong>";
}
?>