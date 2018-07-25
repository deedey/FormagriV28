<?php
if (!isset($_SESSION)) session_start();
/****************************************** ***************
*                                                         *
* Copyright  formagri/cnerta/eduter/enesad                *
* Dey Bendifallah                                         *
* Ce script fait partie intégrante du LMS Formagri.       *
* Il peut être modifié ou utilisé à d'autres fins.        *
* Il est libre et sous licence GPL                        *
* Les auteurs n'apportent aucune garantie                 *
*                                                         *
**********************************************************/
//  fichier lang
if ($lg == "fr")
{
   $nordvdtjour = "Vous ne pouvez pas programmer un rendez-vous à la date du jour";
}
elseif ($lg == "en")
{
   $nordvdtjour = "Vous ne pouvez pas programmer un rendez-vous à la date du jour";
}
elseif ($lg == "ru")
{
   $nordvdtjour = "Vous ne pouvez pas programmer un rendez-vous à la date du jour"; 
}

?>