<?php
if (!isset($_SESSION)) session_start();
header("Content-type  : text/plain");
header("Cache-Control: no-cache, max-age=0");
require "../../admin.inc.php";
require "../../fonction.inc.php";
dbConnect();
if (isset($_GET['activer']) && $_GET['activer'] == 1 && isset($_GET['id_seq']) && $_GET['id_seq'] > 0)
{
  $nbrTwit = mysql_num_rows(mysql_query("select * from seq_twitter where seqtwit_seq_no=".$_GET['id_seq']));
  if ($nbrTwit == 0)
  {
     $inserer = mysql_query("insert into seq_twitter (seqtwit_seq_no,seqtwit_auteur_no) values('".$_GET['id_seq']."','".$_SESSION['id_user']."')");
     $renvoi = "Le service Twitter a été activé pour cette séquence";
  }
  else
     $renvoi = "Twitter a déjà été activé pour cette séquence";
}
if (isset($_GET['activite']) && $_GET['activite'] == 1 && isset($_GET['id_seq']) && $_GET['id_seq'] > 0)
{
     $ReqTwitActif = mysql_query("select * from seq_twit_form where seqformtwit_seq_no='".$_GET['id_seq']."' and
                                  seqformtwit_parc_no='".$_GET['id_parc']."' and seqformtwit_grp_no='".$_GET['id_grp']."'");
     $nbrTwitActif = mysql_num_rows($ReqTwitActif);
     if ($nbrTwitActif == 0)
     {
         $tiny = createTiny(6);
         $inserer = mysql_query("insert into seq_twit_form (seqformtwit_code_lb,seqformtwit_seq_no,seqformtwit_parc_no,seqformtwit_grp_no,seqformtwit_form_no,seqformtwit_auteur_no)".
                                "values('$tiny','".$_GET['id_seq']."','".$_GET['id_parc']."','".$_GET['id_grp']."','".$_GET['formateur']."','".$_SESSION['id_user']."')");
         $renvoi = "Twitter a été validé en tant qu'activité pour cette séquence";
     }
     elseif($nbrTwitActif > 0)
     {
         $itemTwit = mysql_fetch_object($ReqTwitActif);
         $inserer = mysql_query("update seq_twit_form set seqformtwit_flag_on=1 where seqformtwit_cdn='".$itemTwit->seqformtwit_cdn."'");
         $renvoi = "Twitter a été réactivé en tant qu'activité pour cette séquence";
     }
}
if (isset($_GET['activite']) && $_GET['activite'] == 0 && isset($_GET['id_seq']) && $_GET['id_seq'] > 0)
{
     $inserer = mysql_query("update seq_twit_form set seqformtwit_flag_on=0 where seqformtwit_cdn='".$_GET['id_twit']."'");
     $renvoi = "Twitter a été désactivé en tant qu'activité pour cette séquence";
}
if (isset($_GET['lancerTwit']) && $_GET['lancerTwit'] == 1)
{
     $renvoi = '<IFRAME height=500 width=800 bgColor="#FFFFFF" Marginwidth=0 marginheight=0 hspace=0'.
               ' vspace=0 frameborder=0  scrolling=no bordercolor="#000000" '.
               'SRC="ApiTweet/index.php?endpoint=http%3A%2F%2Flrs.annulab.com%2FTinCanApi%2F'.
               '&auth='.$_COOKIE['course'].'&actor='.urlencode('{"name":["'.
               $_COOKIE['monPrenom']).'%20'.urlencode($_COOKIE['monNom'].'"],"mbox":["mailto:'.
               $_COOKIE['monMail'].'"]}').'&registration='.
               $_COOKIE['registration'].'&activity_id=/ApiTweet/"></IFRAME>';
}
if (isset($_GET['lancerTwit']) && $_GET['lancerTwit'] == 0)
{
     $renvoi = "";
}


echo mb_convert_encoding($renvoi,'UTF-8','iso-8859-1');

/// fonctions de création du Hashtag
function createTiny($long)
{
    mt_srand((double)microtime()*1000000);
    $voyellesChiffres = array("a", "e", "i", "o", "u","y", "A", "E", "U", "Y", "1", "2", "3", "4","5", "6", "7", "8", "9");
    $consonnes = array("b", "c", "d", "f", "g", "h", "j", "k",  "m", "n", "p", "q", "r", "s", "t", "v", "w","z");
    $Consonne = array("B", "C", "D", "F","G", "H", "J", "K", "L", "M", "N", "P","R", "V", "W", "X","Z");
    $num_voyellesChiffres = count($voyellesChiffres);
    $num_consonnes = count($consonnes);
    $num_Consonne = count($Consonne);
    $password = "";
    for($i = 0; $i < $long; $i++)
    {
        $password .= $consonnes[mt_rand(0, $num_consonnes - 1)] .
                     $voyellesChiffres[mt_rand(0, $num_voyellesChiffres - 1)].
                     $Consonne[mt_rand(0, $num_Consonne - 1)];
    }
    return substr($password, 0, $long);
}
?>