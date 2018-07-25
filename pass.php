<?php

// Générateur de mot de passe de 6 lettres

function genmotpass($long){
    mt_srand((double)microtime()*1000000);
    $voyelles = array("a", "e", "i", "o", "u");
    $consonnes = array("b", "c", "d", "f", "g", "h", "j", "k", "l", "m", "n", "p", "q", "r", "s", "t", "v", "w","z", "tr",
    "cr", "br", "fr", "th", "dr","gr", "ch", "ph", "st", "sp", "pr", "sl", "cl","pl");
    $num_voyelles = count($voyelles);
    $num_consonnes = count($consonnes);
    for($i = 0; $i < $long; $i++){
        $password .= $consonnes[mt_rand(0, $num_consonnes - 1)] . $voyelles[mt_rand(0, $num_voyelles - 1)];
    }
    return substr($password, 0, $long);
}

?>
