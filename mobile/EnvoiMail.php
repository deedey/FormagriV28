<?php
session_start();
if (!isset($_SESSION['IDUSER']) || $_SESSION['IDUSER'] == "")
{
  exit();
}
require ("../admin.inc.php");
require ("../fonction.inc.php");
require ("../fonction_html.inc.php");
dbConnect();
$date_messagerie = date("d/m/Y H:i:s" ,time());
if (isset($_GET['go']) && $_GET['go'] == 1)
{
    $reply = $from;
    $subject .= StripSlashes($sujet);
    $msg = StripSlashes($message);
    $nom='';
    $fichier = "none";
    $typemime="multipart/mixed";
    if ($email != "" && $subject != "" && $msg != "")
        $envoi = mail_attachement("$email","$subject",str_replace("<br />","\n",html_entity_decode($msg,ENT_QUOTES,'ISO-8859-1')),"$fichier","$reply","$nom","$from");
    $track = genmotpass(10);
    $serveur = gethostbyaddr($_SERVER['REMOTE_ADDR']);
    file_put_contents('tracker.txt',"$track,$serveur,".$_SESSION['LMS'].",$date_messagerie,$from\n",FILE_APPEND);
    echo utf2Charset("Votre réponse a été envoyée à l'adresse : $email",'iso-8859-1');
}

function genmotpass($long)
{
    mt_srand((double)microtime()*1000000);
    $voyelles = array("a", "e", "i", "o", "u");
    $consonnes = array("b", "c", "d", "f", "g", "h", "j", "k", "l", "m", "n", "p", "q", "r", "s", "t", "v", "w","z", "tr",
    "cr", "br", "fr", "th", "dr","gr", "ch", "ph", "st", "sp", "pr", "sl", "cl","pl");
    $chiffres = array("1","2","3","4","5","6","7","8","9","12","18");
    $num_chiffres = count($chiffres);
    $num_voyelles = count($voyelles);
    $num_consonnes = count($consonnes);
    $captcha = "";
    for($i = 0; $i < $long; $i++)
    {
        $captcha .= $consonnes[mt_rand(0, $num_consonnes - 1)] .
                    $voyelles[mt_rand(0, $num_voyelles - 1)].
                    $chiffres[mt_rand(0, $num_chiffres - 1)];
    }
    return substr($captcha, 0, $long);
}

?>
