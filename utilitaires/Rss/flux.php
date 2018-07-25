<?php
require '../../fonction.inc.php';
require "../../langfr.inc.php";
require '../../admin.inc.php';
require '../../fonction_html.inc.php';
dbConnect();
require_once("class.easyrss.php");
$rss = new easyRSS;
setlocale(LC_ALL, 'fr_FR');
if (!isset($p) || (isset($p) && $p == 0))
   $p = 6;
$req_rss = mysql_query("SELECT * FROM rss ORDER BY rss_date_lb desc,rss_type_lb asc limit 0,$p");
WHILE ($DataRss = mysql_fetch_object($req_rss)){
     $id_rss = $DataRss -> rss_cdn;
     $type_rss = $DataRss -> rss_type_lb;
     $date_rss = $DataRss -> rss_date_lb;
     $num_rss = $DataRss -> rss_id_no;
     $auteur_rss = $DataRss -> rss_auteur_no;
     $action_rss = $DataRss -> rss_action_lb;
     if ($type_rss == 'module'){
        $ajout = "du ";
        $req_parc = requete("parcours_nom_lb, parcours_desc_cmt","parcours","parcours_cdn = '$num_rss'");
        if ($req_parc != FALSE){
           $titre_data = strip_tags(mysql_result($req_parc,0,"parcours_nom_lb"));
           $desc_data = strip_tags(mysql_result($req_parc,0,"parcours_desc_cmt"));
        }
     }elseif ($type_rss == 'sequence'){
        $ajout = "de la ";
        $req_seq = requete("seq_titre_lb,seq_desc_cmt","sequence","seq_cdn = '$num_rss'");
        if ($req_seq != FALSE){
           $titre_data = strip_tags(mysql_result($req_seq,0,"seq_titre_lb"));
           $desc_data = strip_tags(mysql_result($req_seq,0,"seq_desc_cmt"));
        }
     }elseif ($type_rss == 'activite'){
        $ajout = "de l'";
        $req_act = requete("act_nom_lb,act_consigne_cmt","activite","act_cdn = '$num_rss'");
        if ($req_act != FALSE){
           $titre_data = strip_tags(mysql_result($req_act,0,"act_nom_lb"));
           $desc_data = strip_tags(mysql_result($req_act,0,"act_consigne_cmt"));
        }
     }
     $req_util = requete("util_nom_lb,util_prenom_lb,util_email_lb","utilisateur","util_cdn = '$auteur_rss'");
     if ($req_util != FALSE){
        $nom_util = mysql_result($req_util,0,"util_nom_lb");
        $prenom_util = mysql_result($req_util,0,"util_prenom_lb");
        $mail_util = mysql_result($req_util,0,"util_email_lb");
     }
     //if ($mail_util != "" && strstr($mail_util,"@") && strstr($mail_util,"."))
        //$lauteur = "<A mailto=\"$mail_util\">$prenom_util $nom_util</A>";
     //else
        $lauteur = $prenom_util." ".$nom_util;
     $item[] = array(
                "title"=>$action_rss.' '.$ajout.$type_rss.' : '.$titre_data, // champ obligatoire
                "description"=>$desc_data, // champ obligatoire
                "pubDate"=>$date_rss,//"link"=>$adresse_http
                "author"=>$lauteur


     );

}
$rss_array = array(
             "encoding"=>"iso-8859-1",
             "language"=>"fr-FR",
             "title"=>"Contenus pédagogiques sur Formagri", // champ obligatoire
             "description"=>"Ajout et modification des divers composants de scénarii pédagogiques sur Formagri", // champ obligatoire
             "link"=>$adresse_http, //champ obligatoire
             "items"=>$item
);

header("Content-type: application/xml");
echo $rss->rss($rss_array, "rss.xsl"); // le second parametre est facultatif

?>