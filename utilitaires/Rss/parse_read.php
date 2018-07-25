<?php
/*
** fichier parse_flux-3.php
** utilisé par la page exemple-5.php
*/

// chemin relatif vers le dossier cache
// le répertoire "cache" doit être autorisé en écriture
// ne pas oublier / à la fin
define(DIR_CACHE, 'cache/');

if (!is_dir(DIR_CACHE)) {
        exit ('Répertoire cache "'.DIR_CACHE.'" inexistant !');
}

function clean_date($date)
{
        // si 'AAAA-MM-JJTHH:MM:SS+00:00' ou 'AAAA-MM-JJTHH:MM:SSZ'
        if (preg_match("/^[0-9]/",$date) and  preg_match("/(([[:digit:]]|-)*)T(([[:digit:]]|:)*)[^[:digit:]].*/",$date,$temp)) {
                $date = $temp[1].' '.$temp[3];
        }
        setlocale(LC_ALL, 'fr_FR');
        //$date = ucfirst(strftime("%A %d %B %Y", strtotime($date)));//        $date = date('d/m/Y', strtotime($date));
        return $date;
}
function clean_text($text, $encodage = '') {
        if ($encodage == 'utf-8') {
                $text = utf8_decode($text);
        }
        $avant = array(
                '&lt;',
                '&gt;',
                '&quot;',
                '&amp;',
                '|@|'
                );
        $apres = array(
                '<',
                '>',
                '"',
                '&',
                '&#'
                );
        $text = str_replace($avant, $apres, $text);
        return $text;
}

function nettoyage ($description) {
 $avant = array ('’<div.*?>’si','’</div>’si','’<font.*?>’si','’</font>’si');
 $apres = '';
 $description = preg_replace($avant,$apres,$description);
 return $description;
}

include_once 'rss_read.inc.php';
//setlocale(LC_TIME,'fr_FR');

function affiche_fil($fil, $file_cache, $delai, $n_items) {
        // le fichier est-il en cache et suffisamment jeune
        $file_cache = DIR_CACHE.$file_cache;
        // $delai en secondes
        //$delai = round($delai*3600);
        $rss = new rss_read();
        $date_modif = $rss -> get_last_modified($fil);

        if ($date_modif > 0) {
                $delai = 0;
        }
        else {
                $date_modif = time();
                $delai = $delai;
        }

        $en_cache = file_exists($file_cache);
        if ($en_cache) {
                $en_cache = ($date_modif < filemtime($file_cache) + $delai);
        }

        // $en_cache = false;
        if (!$en_cache) {
                // il est considéré comme n'étant pas en cache on le génére
                $data = '';

                // caractères parasites pouvant être contenus dans le fils
                // doit être invoqué avant parsefile
                // cette fonction est optionnelle et ne doit être utilisée que pour certains fils.
                $avant = array('&#','&bull;');
                $apres = array('|@|','-');
                $rss -> to_replace_with($avant, $apres);

                // parser le fichier news
                $rss -> parsefile($fil, $n_items);

                if ($rss) {
                        $encode = $rss -> get_encoding();

                        // recupération des données sur le channel
                        $channel = $rss -> get_channel();

                        // affichage site, url, description
                        $data = '<dl class="fulllist_title" style="margin-right: 16px;">';
                        $id = md5($channel['title']);
                        $data .= '<dt class="headline_title"><a class="cache_montre" href="javascript:void(0)" onclick="visible(\''.$id.'\')">'.
                                 '<img src="images/fleche2.gif" border=0></a> <a class="headline_link" href="'.$channel['link'].
                                 '" target="_blank" class="newpost" >'.clean_text($channel['title'], $encode).'</a>'.
                                 '<div class="fulllist_content" id="'.$id.'">'.clean_text(nettoyage($channel['description']), $encode).
                                 '<br /></div></dt><br /> '."\n";
                        $data .= '</dl>';


                        // nombre d'items
                        $nbnews = $rss -> get_num_items();

                        // recup array des données
                        $items = $rss -> get_items();
                        $data .= '<dl class="headline" style="margin-right: 10px;">';

                        for($i = 0; $i < $nbnews; $i++) {
                                $id = md5($items[$i]['title'].$i);
                                $code_montre .= 'montre_tout(\''.$id.'\');';
                                $code_cache .= 'cache_tout(\''.$id.'\');';
                        }

                        $data .= '<dt><a class="cache_montre" href="javascript:void(0)" onclick="'.$code_montre.'" title="Montrer tout">'.
                                 '<img src="images/fleche0.gif" border=0></a>&nbsp;&nbsp; '.
                                 '<a class="cache_montre" href="javascript:void(0)" onclick="'.$code_cache.'" title="Cacher tout">'.
                                 '<img src="images/fleche1.gif" border=0></a></dt><br />';
                        for($i = 0; $i < $nbnews; $i++) {
                        // la description est stockée dans un bloc div invisible au départ d'id unique dans la page
                                $id = md5($items[$i]['title'].$i);
                                $data .= '<dt class="headline_title"><a class="cache_montre" href="javascript:void(0)" onclick="visible(\''.$id.'\')" title="Cliquez ici pour montrer ou cacher le descriptif">'.
                                         '<img src="images/fleche2.gif" border=0>'.clean_text($items[$i]['title'], $encode).'</a>';
                                $data .= '<div class="headline_content" id="'.$id.'">';
                                if ($items[$i]['description'] != '')
                                   $data .= clean_text(nettoyage($items[$i]['description']), $encode).'<br />';
                                if ($items[$i]['pubdate'] != '')
                                   $data .= clean_date($items[$i]['pubdate']).'<br />';
                                if ($items[$i]['author'] != '')
                                   $data .= clean_text($items[$i]['author'], $encode).'<br />';
                                if ($items[$i]['link'] != ''){
                                   $data .= '<a class="headline_link" href="'.$items[$i]['link'].
                                         '" target="_blank" class="newpost" title="Cliquez sur ce lien pour l\'ouvrir" > >>> '.clean_text($items[$i]['title'], $encode).'</a><br />';
                                }
                                $data .= '</div></dt> '."\n";

                        }

                        $data .= '<dt><a class="cache_montre" href="javascript:void(0)" onclick="'.$code_montre.
                                 '" title="Montrer tout"><img src="images/fleche0.gif" border=0></a> &nbsp;&nbsp; '.
                                 '<a class="cache_montre" href="javascript:void(0)" onclick="'.$code_cache.'" title="Cacher tout">'.
                                 '<img src="images/fleche1.gif" border=0></a></dt>';
                        $data .= '</dl>';
                } // fin if $rss

                $fd = fopen($file_cache, "w");
                fputs($fd, $data);
                fclose($fd);

        } // fin if $en_cache

        include $file_cache;
} // fin affiche_fil
?>