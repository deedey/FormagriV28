<?php
if (!isset($_SESSION)) session_start();
if (!isset($_SESSION['id_user']) || $_SESSION['id_user'] == "")
{
  exit();
}
include ('include/UrlParam2PhpVar.inc.php');
require "admin.inc.php";
require_once ('fonction.inc.php');
require_once ('fonction_html.inc.php');
require_once ("lang$lg.inc.php");
dbConnect();
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
</HEAD>
<BODY>
<?php
$bouton_gauche = "<table cellpadding='0' cellspacing='0' border=0><tbody>".
                 "<tr><td><img src=\"$adresse_http/images/complement/cg.gif\" border='0'></td>".
                 "<td background='$adresse_http/images/complement/milieu.gif' nowrap align='center'><div id='sequence'>";
$bouton_droite = "</div></td><td><img src=\"$adresse_http/images/complement/cd.gif\" border='0'></td></tr></tbody></table>";
           if  (!isset($_POST["keyword"]) || empty($_POST["keyword"]))
                $rqList = "select ".stripslashes($requeste);
           else
                $rqList = "select ".stripslashes($requeste)." AND (utilisateur.util_nom_lb like '" . $_POST["keyword"] . "%' OR utilisateur.util_prenom_lb like '" . $_POST["keyword"] . "%') order by utilisateur.util_typutil_lb,utilisateur.util_nom_lb";
           $rsList = mysql_query($rqList);
           $nombre = mysql_num_rows($rsList);
           if ($nombre > 0)
           {
              echo "<FORM name='form2' action = \"mail_envoi.php?mail_sous_grp=1\" METHOD='POST'>";
              echo "<TABLE width='490' cellpadding='4' cellspacing='0'><tr><TD width=5%>&nbsp;</TD><TD width=70%><table cellpadding='0' cellspacing='0'>";
               while ($item = mysql_fetch_object($rsList))
               {
                 if (strstr($rqList,"distinct presc_utilisateur_no"))
                        $id_util = $item->presc_utilisateur_no;
                 elseif (strstr($rqList,"distinct tuteur.tut_apprenant_no"))
                        $id_util = $item->tut_apprenant_no;
                 elseif (strstr($rqList,"distinct utilisateur.util_cdn"))
                 {
                    $id_util = $item->util_cdn;
                    $origine = "annuaire";
                    $type_user=GetDataField ($connect,"select util_typutil_lb from utilisateur WHERE util_cdn = '$id_util'","util_typutil_lb");
                    if ($typ_user == 'APPRENANT')
                    {
                      $req_grp = mysql_query("select utilgr_groupe_no from utilisateur_groupe where utilgr_utilisateur_no = $id_util");
                      $comp_grp = mysql_num_rows($req_grp);
                      if ($comp_grp > 0)
                      {
                          $iii = 0;
                          $compteur_app_grp = 0;
                          while ($iii < $comp_grp)
                          {
                            $numero_groupe = mysql_result($req_grp,$iii,"utilgr_groupe_no");
                            if ($type_user == 'APPRENANT')
                            {
                              $compt_grp = mysql_result(mysql_query("select count(utilgr_utilisateur_no) from utilisateur_groupe where utilgr_utilisateur_no = $id_util AND utilgr_groupe_no = $numero_groupe"),0);
                              if ($compt_grp == 0)
                                 continue;
                            }
                            elseif ($type_user == 'TUTEUR')
                            {
                              $compt_tut = mysql_result(mysql_query("select count(tut_tuteur_no) from tuteur where tut_tuteur_no = $id_util AND tut_apprenant_no = $id_user"),0);
                              $compt_tut_grp = mysql_result(mysql_query("select count(*) from groupe where grp_tuteur_no = $id_util AND grp_cdn = $numero_groupe"),0);
                              if ($compt_tut == 0 && $compt_tut_grp == 0)
                                 continue;
                            }
                            else
                            {
                              $compt_formpresc = mysql_result(mysql_query("select count(*) from prescription_$numero_groupe where (presc_prescripteur_no = $id_util OR presc_formateur_no = $id_util) AND presc_utilisateur_no = $id_user"),0);
                              $compt_tut = mysql_result(mysql_query("select count(tut_tuteur_no) from tuteur where tut_tuteur_no = $id_util AND tut_apprenant_no = $id_user"),0);
                              $compt_tut_grp = mysql_result(mysql_query("select count(*) from groupe where (grp_tuteur_no = $id_util or grp_resp_no = $id_util) AND grp_cdn = $numero_groupe"),0);
                              $compt_aut = mysql_result(mysql_query("select count(*) from utilisateur where util_auteur_no = $id_util AND util_cdn = $id_user"),0);
                              if ($compt_formpresc == 0 && $compt_tut == 0 && $compt_tut_grp == 0 && $compt_aut == 0)
                                 continue;
                            }
                            $iii++;
                          }
                      }
                    }
                    if ($typ_user != 'APPRENANT' && $typ_user != 'ADMINISTRATEUR' && $messagerie != 1)
                    {
                        if ($type_user == 'APPRENANT')
                        {
                              $compt_tut = mysql_result(mysql_query("select count(tut_apprenant_no) from tuteur where tut_apprenant_no = $id_util AND tut_tuteur_no = $id_user"),0);
                              $req_tut_grp = mysql_query("select grp_cdn from groupe where grp_tuteur_no = $id_user");
                              $comp_grp = mysql_num_rows($req_tut_grp);
                              if ($comp_grp > 0)
                              {
                                 $iii = 0;
                                 $compteur_app_grp = 0;
                                 while ($iii < $comp_grp)
                                 {
                                    $util_grp = mysql_result($req_tut_grp,$iii,"grp_cdn");
                                    $compt_app_grp = mysql_result(mysql_query("select count(*) from utilisateur_groupe where utilgr_utilisateur_no = $id_util AND utilgr_groupe_no = $util_grp"),0);
                                    if ($compt_app_grp > 0)
                                      $compteur_app_grp++;
                                   $iii++;
                                 }
                              }
                              if ($typ_user != 'TUTEUR')
                              {
                                    $compt_aut = mysql_result(mysql_query("select count(*) from utilisateur where util_auteur_no = $id_user AND util_cdn = $id_util"),0);
                                    $req_resp_grp = mysql_query("select grp_cdn from groupe where grp_resp_no = $id_user");
                                    $comp_resp_grp = mysql_num_rows($req_resp_grp);
                                    if ($comp_resp_grp > 0)
                                    {
                                       $iii = 0;
                                       $compteur_resp_grp = 0;
                                       while ($iii < $comp_resp_grp)
                                       {
                                         $util_grp = mysql_result($req_resp_grp,$iii,"grp_cdn");
                                         $compt_presc = mysql_result(mysql_query("select count(presc_utilisateur_no) from prescription_$util_grp where presc_utilisateur_no = '$id_util' AND (presc_prescripteur_no = '$id_user' or presc_formateur_no = '$id_user')"),0);
                                         $compt_app_grp = mysql_result(mysql_query("select count(*) from utilisateur_groupe where utilgr_utilisateur_no = $id_util AND utilgr_groupe_no = $util_grp"),0);
                                         if ($compt_resp_grp > 0 || $compt_presc > 0)
                                           $compteur_resp_grp++;
                                       $iii++;
                                       }
                                    }
                              }
                              if ($compt_tut == 0 && (($compteur_app_grp == 0 && $comp_grp > 0) || $comp_grp == 0) && (($compteur_resp_grp == 0 && $comp_resp_grp > 0) || $comp_resp_grp == 0) && ($compt_form == 0 || ($compt_presc == 0 && $typ_user == 'RESPONSABLE_FORMATION')))
                                 continue;
                        }
                    }
                 }
                $email=GetDataField ($connect,"select util_email_lb from utilisateur WHERE util_cdn = '$id_util'","util_email_lb");
                if ($compteur == $nombre-1)
                         $envoi_a .= "$email|$id_util";
                else
                         $envoi_a .= "$email|$id_util,";
                $compteur++;
                $nom_user=GetDataField ($connect,"select util_nom_lb from utilisateur WHERE util_cdn = '$id_util'","util_nom_lb");
                $prenom_user=GetDataField ($connect,"select util_prenom_lb from utilisateur WHERE util_cdn = '$id_util'","util_prenom_lb");
                $majuscule = "$nom_user $prenom_user";
                $majuscule1 = utf2Charset($majuscule,$charset);
                $id_photo=GetDataField ($connect,"select util_photo_lb from utilisateur WHERE util_cdn = '$id_util'","util_photo_lb");
                if (strstr($rqList," distinct utilisateur.util_cdn"))
                         $type_user=GetDataField ($connect,"select util_typutil_lb from utilisateur WHERE util_cdn = '$id_util'","util_typutil_lb");
                $ajout_box = "<INPUT TYPE='checkbox' name='envoi[$id_util]'>";
                echo "<tr><td>";
                $lien = "mail.php?contacter=1&num=$id_util";
                $lien= urlencode($lien);
                if ($id_photo == '')
                         echo "\n$ajout_box<A HREF=\"javascript:void(0);\" onClick=\"javascript:document.location.replace('trace.php?link=$lien');\">$majuscule1</A>";
                else
                {
                         list($w_img, $h_img, $type_img, $attr_img) = getimagesize("images/$id_photo");
                         echo "\n$ajout_box<A  HREF=\"javascript:void(0);\" onClick=\"javascript:document.location.replace('trace.php?link=$lien');\" onMouseOver=\"overlib('', TEXTCOLOR, '#ffffff', TEXTSIZE, 2, WIDTH, $w_img, HEIGHT, $h_img, BACKGROUND, 'images/$id_photo', PADX, 60, 20, PADY, 20, 20,DELAY,500)\" onMouseOut=\"nd()\">$majuscule1</A>";
                }
                if (strstr($rqList," distinct utilisateur.util_cdn"))
                {
                         if ($type_user == 'FORMATEUR_REFERENT')
                            $le_type = $mess_typ_fr;
                         if ($type_user == 'TUTEUR')
                            $le_type = $mess_typ_tut;
                         if ($type_user == 'RESPONSABLE_FORMATION')
                            $le_type = $mess_typ_rf;
                         if ($type_user == 'ADMINISTRATEUR')
                            $le_type = $mess_typ_adm;
                         if ($type_user == 'APPRENANT')
                            $le_type = "";
                         echo "&nbsp;<font size=1>". utf2Charset($le_type,$charset)."</FONT>";
                }
                echo "</td></tr>";
              }
              echo "<INPUT type='HIDDEN' name='liste_envoi' value=\"$envoi_a\">";
              echo "<INPUT type='HIDDEN' name='origine' value=\"$origine\">";
              echo "</table></TD>";
         }
         else
         {
            echo "&nbsp;&nbsp;<B>".utf2Charset($mess_search_rien,$charset,"UTF-8")."\"".$_POST["keyword"]."\"<B>";
         }

?>
</BODY></HTML>
