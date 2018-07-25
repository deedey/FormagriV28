<?php
if (!isset($_SESSION)) session_start();
if (!isset($_SESSION['id_user']) || $_SESSION['id_user'] == "")
{
  exit();
}
include ('include/UrlParam2PhpVar.inc.php');
require "admin.inc.php";
require 'fonction.inc.php';
require 'fonction_html.inc.php';
require "lang$lg.inc.php";
//include ("click_droit.txt");
dbConnect();
//include('include/UrlParam2PhpVar.inc.php');
if ($lg == "ru")
{
  $code_langage = "ru";
  $charset = "Windows-1251";
  putenv("TZ=Europe/Moscow");
}
elseif ($lg == "fr")
{
  $code_langage = "fr";
  $charset = "iso-8859-1";
  putenv("TZ=Europe/Paris");
}
elseif ($lg == "en")
{
  $code_langage = "en";
  $charset = "iso-8859-1";
}
$bkg = GetDataField($connect,"select param_etat_lb from param_foad where param_typ_lb = 'bkg'","param_etat_lb");
include ('include/varGlobals.inc.php');
?>
<HTML>
<HEAD>
<META HTTP-EQUIV="Content-Type" content="text/html; charset=<?php  echo $charset;?>">
<META HTTP-EQUIV="Content-Language" CONTENT="<?php  echo $code_langage ;?>">
<META HTTP-EQUIV="Expires" CONTENT="Fri, Jan 01 1900 00:00:00 GMT">
<META NAME="ROBOTS" CONTENT="No Follow">
<META HTTP-EQUIV="Cache-Control" CONTENT="no-cache, must-revalidate">
<META HTTP-EQUIV="Pragma" CONTENT="no-cache">
<link rel="stylesheet" type="text/css" href="admin/style_admin.css" />
<STYLE>
/*BODY { font-family: arial; font-size: 12px; color: #333333 }*/
TD   { font-family: arial; font-size: 11px; color: #333333 }
TH   { font-family: arial; font-size: 11px; color: #333333 }
A         {font-family:arial;font-size:11px;color:#24677A;text-decoration:none}
A:link    {font-family:arial;font-size:11px;color:#24677A;font-weight:bold}
A:visited {font-family:arial;font-size:11px;color:#24677A;font-weight:bold}
A:hover   {font-family:arial;font-size:11px;color:#D45211;font-weight:bold}

fieldset
{
     border: 0px solid #B5AEA4;
     background-color: #FFFFFF;
     margin-left: 1em;
     margin-right: 1em;
     margin-top: 2px;
     margin-bottom: 1em;
}
#contenu {height: 350px; border: 1px solid #999;overflow: auto; background-color: #FFFFFF;}
#contenu li {list-style-type: square;}

#titre A:link{background-repeat:no-repeat;background-position:1% 50%;color:#3BACC4;}
#titre A:visited{background-repeat:no-repeat;background-position:1% 50%;color:#3BACC4;}
#titre A:hover {background-repeat:no-repeat;background-position:1% 50%;color:#D45211;}

#menu A:link{background-repeat:no-repeat;background-position:1% 50%;color:#002D44;}
#menu A:visited{background-repeat:no-repeat;background-position:1% 50%;color:#002D44;}
#menu A:hover {background-repeat:no-repeat;background-position:1% 50%;color:#D45211;}
.SOUS_TITRE {
     text-align:left;
     font-family:arial;
     background-color: #D4E7ED; /*#F6E7D4;*/
     padding:4px;
     height:26px;
     width:80%;
     font-size:11px;
     color:#333333;
     border:1px solid #24677A;
}

#sequence A:link{background-repeat:no-repeat;background-position:1% 50%;color:#24677A;}
#sequence A:visited{background-repeat:no-repeat;background-position:1% 50%;color:#24677A;}
#sequence A:hover {background-repeat:no-repeat;background-position:1% 50%;color:#D45211;}
</STYLE>
<script type="text/javascript" src="fonction.js">
</script>
<TITLE>Formagri</TITLE>
</HEAD>
<?php
$bouton_gauche = "<table cellpadding='0' cellspacing='0' border=0><tbody>".
                 "<tr><td><img src=\"$adresse_http/images/complement/cg.gif\" border='0'></td>".
                 "<td background='$adresse_http/images/complement/milieu.gif' nowrap align='center'><div id='sequence'>";
$bouton_droite = "</div></td><td><img src=\"$adresse_http/images/complement/cd.gif\" border='0'></td></tr></tbody></table>";
$requeste = " distinct utilisateur.util_cdn from utilisateur where utilisateur.util_cdn != $id_user ";
if ($vientde == "annonce")
{
   if ($typ_user == 'TUTEUR' && $superviseur == 1)
     $requeste1 = " distinct utilisateur.util_cdn from utilisateur,groupe,utilisateur_groupe where ".
                   "groupe.grp_tuteur_no = $id_user AND groupe.grp_cdn = $id_grp AND ".
                   "utilisateur_groupe.utilgr_utilisateur_no=utilisateur.util_cdn AND ".
                   "utilisateur_groupe.utilgr_groupe_no = groupe.grp_cdn ".
                   "order by utilisateur.util_nom_lb";
   elseif (($typ_user == 'TUTEUR' && $superviseur != 1) || ($typ_user != 'TUTEUR' && !isset($id_grp)))
     $requeste1 = " distinct utilisateur.util_cdn from utilisateur,tuteur where ".
                   "tuteur.tut_tuteur_no = $id_user AND ".
                   "tuteur.tut_apprenant_no = utilisateur.util_cdn ".
                   "order by utilisateur.util_nom_lb";
   elseif ($typ_user == 'ADMINISTRATEUR' && isset($id_grp) && $id_grp > 0)
     $requeste1 = " distinct utilisateur.util_cdn from utilisateur,utilisateur_groupe where ".
                   "utilisateur_groupe.utilgr_utilisateur_no=utilisateur.util_cdn AND ".
                   "utilisateur_groupe.utilgr_groupe_no = $id_grp ".
                   "order by utilisateur.util_nom_lb";
   elseif ($typ_user == 'RESPONSABLE_FORMATION' && isset($id_grp) && $id_grp > 0)
     $requeste1 = " distinct utilisateur.util_cdn from utilisateur,groupe,utilisateur_groupe,prescription_$id_grp where ".
                  "(grp_resp_no = $id_user AND grp_cdn = $id_grp AND utilgr_utilisateur_no=util_cdn AND utilgr_groupe_no = $id_grp) OR ".
                  "((presc_formateur_no=$id_user OR presc_prescripteur_no = $id_user) AND presc_utilisateur_no = util_cdn) OR  ".
                  "(util_auteur_no = $id_user AND utilgr_utilisateur_no=util_cdn AND utilgr_groupe_no = $id_grp) order by util_nom_lb";
   elseif ($typ_user == 'FORMATEUR_REFERENT' && isset($id_grp) && $id_grp > 0)
     $requeste1 = " distinct utilisateur.util_cdn from utilisateur,prescription_$id_grp where ".
                   "prescription_$id_grp.presc_formateur_no=$id_user AND ".
                   "prescription_$id_grp.presc_utilisateur_no = utilisateur.util_cdn ".
                   "order by utilisateur.util_nom_lb";
}else
    $requeste1=  " distinct utilisateur.util_cdn from utilisateur where ".
                  "utilisateur.util_cdn != $id_user ".
                  "order by utilisateur.util_typutil_lb,utilisateur.util_nom_lb";
// javascript Ajax
echo "<script type=\"text/javascript\">
   function loadData() {
    if (document.getElementById('keyword').value.length > 0) {
       sendData('keyword='+ document.getElementById('keyword').value, 'cherche_nom.php?messagerie=$messagerie&requeste=$requeste', 'POST');
    }
    else {
       sendData('', 'cherche_nom.php?messagerie=$messagerie&numero_groupe=$id_grp&requeste=".addslashes($requeste1)."', 'POST');
    }
  }
</script>";
if (strstr($_SERVER["HTTP_USER_AGENT"],"MSIE"))
  $typ_agent ='msie';
echo "<BODY bgcolor=\"$bkg\" style=\"margin-top:12px;\" onload=\"loadData();\" >";
echo "<div id=\"overDiv\" style=\"position:absolute; visibility:hidden;z-index:1000;\"></div>";
echo "<SCRIPT LANGUAGE=\"JavaScript\" SRC=\"overlib.js\"><!-- overLIB (c) Erik Bosrup --></SCRIPT>";
if ($mail_sous_grp == 1)
{
   $send_to = "";
   $list_envoi = explode(",",$liste_envoi);
   $nb_envoi = count($list_envoi);
   $i=0;
   while ($i < $nb_envoi)
   {
      $envoyer = explode("|",$list_envoi[$i]);
      $adresse = $envoyer[0];
      $num = $envoyer[1];
      if ($envoi[$num] == 'on' && $i < $nb_envoi-1)
         $send_to .= $num.",";
      elseif ($envoi[$num] == 'on' && $i == $nb_envoi-1)
         $send_to .= $num;
    $i++;
   }
   if ($an == 1)
      $vers = $mess_envoi_mail_annu;
   else
      $vers = "$mess_mail_avert $mess_mail_cert_app";
   if ($send_to != "")
   {
         echo "<script language=\"JavaScript\">";
         echo "document.location.replace(\"mail.php?send_to=$send_to&sous_grp=1&message_mail=$vers\")";
         echo "</script>";
   }
}
echo "<script language=\"JavaScript\" type=\"text/javascript\">        <!--
         function CheckAll()
         {
               for (var j = 0; j < document.form2.elements.length; j++)
               {
                   if (document.form2.elements[j].type == 'checkbox')
                   {
                      document.form2.elements[j].checked = !(document.form2.elements[j].checked);
                   }
               }
         }
//--></script>";
//------------------------------------------------------------------------------------------------------------------
// XmlHttpRequest
//echo "<Font size='1' color='#FFFFFF'>$requeste";
entete_simple($mess_ad_annu);
echo "<TR><TD width='100%'><TABLE bgColor='#FFFFFF' cellspacing='1' cellpadding='0' width='100%' border='0'>";
//echo "<tr><td>".aide_div("annuaire",10,0,2,3)."</td></tr>";
echo "<tr><td valign=center>";
if ($vientde == "annonce")
{
  echo "<fieldset><label for='keyword' class='sous_titre'>$mess_mail_avert $mess_mail_cert_app</label>";
  echo "&nbsp;&nbsp;<input type='hidden' name='keyword' id='keyword' size='0' maxlenght = 0 value=\"\" />";
}
else
{
  echo "<fieldset><label for='keyword'>&nbsp;&nbsp;<strong>$mess_rech_nomprenom</strong></label>";
  echo "&nbsp;&nbsp;<input type='text' name='keyword' id='keyword' value=\"\" onkeyup=\"loadData();\" />";
}
echo "<div><div id='check' style=\"float:left; padding-right:10px;padding-top:8px;padding-bottom:4px;\">$bouton_gauche".
     "<A href=\"javascript:void(0)\" onClick=\"CheckAll();\">".
     "$mess_InvSlct</A>$bouton_droite </div>";
echo "<div id='scribe' style=\"float:left; padding-left:10px;padding-top:8px;padding-bottom:4px;\">$bouton_gauche".
     "<A HREF=\"javascript:void(0);\" onclick=\"javascript:document.form2.submit();\">$mess_rediger</A>$bouton_droite </div></div>";
//<!-- Zone pour l'affichage des résultats -->

echo "&nbsp;<BR>&nbsp;</td></tr><tr><td><div id=\"contenu\"></div></fieldset>";
echo "</td></tr></TABLE></TD></TR></TABLE></TD></TR></TABLE><P>";
?>
