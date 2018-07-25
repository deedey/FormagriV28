<?php
/******************************************
*                                         *
* Copyright  formagri/cnerta/eduter/enesad*
* Dey Bendifallah                         *
* Ce script fait partie intégrante du LMS *
* Formagri.Il peut être modifié ou utilisé*
* à d'autres fins.                        *
* Il est libre et sous licence GPL        *
* Les auteurs n'apportent aucune garantie *
*                                         *
******************************************/
function req_gene($requete)
{
     GLOBAL $_SESSION;
     $req = mysql_query ($requete);
     $nb_items = mysql_num_rows($req);
     if ($nb_items > 0)
        return $req;
     else
        return FALSE;

}


?>