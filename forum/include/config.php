<?php

//paramètres de connection à la base de données
require '../admin.inc.php';

$localhost = $adresse;        // Nom serveur ou localhost
$dbName = $bdd;                        // Nom de votre base de données
$login = $log;                             // Votre Loggin
$pwd = $mdp;                              //  Votre mot de passe

// Connexion au Serveur
$connexion=mysql_connect($localhost, $login , $pwd );


// Alimentation des libellés
$texte_singulier  =   " personne connectée";        // Alimentation libellé pour une connection
                                                                               //  Ex : $texte_singulier  =   "personne connectée ";
$texte_pluriel      =   " personnes connectées";        // Alimentation libellé pour plusieurs connections
                                                                                  //  Ex : $texte_pluriel   =   "personnes connectées ";
?>