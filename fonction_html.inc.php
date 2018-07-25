<?php
function entete_concept($file,$son_titre)
{
       global $lg,$requete_parc,$requete_seq,$typ_user,$id_user,$id_act,$miens_parc,$lesseq,$choix_ref,$refer,$miens,$prem,$liste,$ordre_affiche,$liste_seq,$parc,$id_parc,$type_on,$incl,$prov,$id_seq,$seq;
       require ("lang$lg.inc.php");
       echo "<center><table style='background-color:#298CA0;' cellspacing='2' cellpadding='0' width='98%'><tbody><tr>";
       include ("$file");
       echo "<td valign='top' width='70%' height='100%' bgcolor='#FFFFFF'>";
       echo "<table bgColor='#FFFFFF' cellspacing='1' cellpadding='0' width='100%'><tbody>";
       echo "<tr><td background=\"".$_COOKIE['monpath']."/images/fond_titre_table.jpg\" colspan='2' height='40' align='center' valign='center'><Font size='3' color='#FFFFFF'><strong>$son_titre</strong></FONT></td></tr>";
}

function entete_simple($titre)
{
       global $lg;
       require ("lang$lg.inc.php");
       echo "<center><table style='background-color:#298CA0;' cellspacing='2' cellpadding='0' width='98%'><tbody><tr><td>";
       echo "<table bgColor='#FFFFFF' cellspacing='1' cellpadding='0' width='100%'><tbody>";
       echo "<tr><td background=\"".$_COOKIE['monpath']."/images/fond_titre_table.jpg\" colspan='2' height='40' align='center' valign='center'>".
            "<Font size='3' color='#FFFFFF'><strong>$titre</strong></FONT></td></tr>";
}

function entete_simple_rep($titre)
{
       global $lg;
       require ("lang$lg.inc.php");
       echo "<center><table  style='background-color:#298CA0;' cellspacing='2' cellpadding='0' width='98%'><tbody><tr><td>";
       echo "<table bgColor='#FFFFFF' cellspacing='1' cellpadding='0' width='100%'><tbody>";
       echo "<tr><td background=\"".$_COOKIE['monpath']."/images/fond_titre_table.jpg\" colspan='2' height='40' align='center' valign='center'>".
            "<Font size='3' color='#FFFFFF'><strong>$titre</strong></FONT></td></tr>";
}

function fin_tableau($html)
{
    $html = "</td></tr></table></td></tr></table>";
    return $html;
}

function affiche_contenu($message)
{
    $html = "<tr height ='50'><td>&nbsp;</td></tr>";
    $html .= "<tr height ='50' valign='center'><td><strong>$message</strong></td></tr>";
    return $html;
}

function couleur_tr($item,$hauteur)
{
    if ($hauteur == '')
       $hauteur = 30;
    if (($item/2) == ceil($item/2))
       $html = "<TR height='$hauteur' onMouseOver=\"$(this).css('background','#D4E7ED');\"".
               " onMouseOut=\"$(this).css('background','#FFFFFF');\">";
    else
       $html = "<TR height='$hauteur' style=\"background:#F0F0F0;\" onMouseOver=\"$(this).css('background','#D4E7ED');\"".
               " onMouseOut=\"$(this).css('background','#F0F0F0');\">";
    return $html;
}


function aide($fichier,$nb)
{
    global $lg;
    //require ("lang$lg.inc.php");
    $le_nb = ($nb != 0 || $nb != '') ? " width='$nb%'" : "";
    $html = "<td style=\"text-align: left;\" nowrap $le_nb>".
            "<a href=\"http://wikiformagri.educagri.fr/doku.php?id=$fichier\" class= 'bouton_new' target='_blank'>Aide</a></td>";
  return $html;
}
function aide_simple($fichier)
{
    global $lg;
    //require ("lang$lg.inc.php");
    $html = "<tr><td><div id='aide' style=\"float:left;padding-left:1px;padding-top:1px;padding-bottom:1px;\">".
            "<a href=\"http://wikiformagri.educagri.fr/doku.php?id=$fichier\" ".
            "class= 'bouton_new' target='_blank'>Aide</a></div></td></tr>";
  return $html;
}
//$L left padding,$R right padding,$T top padding,$B bottom padding
function aide_div($fichier,$L,$R,$T,$B)
{
    global $lg,$monURI;
    //require_once ("$monURI/lang$lg.inc.php");
    $html = "<div id='aide' style=\"float:left;padding-left:".$L."px; padding-right:".$R."px; padding-top:".
            $T."px; padding-bottom:".$B."px;\">".
            "<a href=\"http://wikiformagri.educagri.fr/doku.php?id=$fichier\" class= 'bouton_new' target='_blank'>Aide</a></div>";
  return $html;
}

function TinCanTeach($id_act,$url_ressource,$commentaire)
{
    GLOBAL $adresse_http;
    $suite = (strstr($url_ressource,'?')) ? '&' : '?';
    $suite .= "endpoint=".urlencode('http://lms.annulab.com/TinCanApi/');
    $aCoder = $id_act;
    $suite .= '&auth='.base64url_encode($aCoder);
    $suite .= '&actor='.urlencode('{"name":["'.$_COOKIE['monPrenom']).'%20'. urlencode($_COOKIE['monNom'].'"],"mbox":["mailto:'.$_COOKIE['monMail'].'"]}');
    $suite .= '&activity_id='.html_entity_decode($commentaire,ENT_QUOTES,'utf-8');
    $suite .= '&registration='.base64url_encode($_COOKIE["monID"].'_'.$_COOKIE["monLogin"].'|'.str_replace('http://','',str_replace('.educagri.fr','',$adresse_http)));
    if ($_SESSION['onLine'] == 1)
       return $suite;
    else
       return '';
}

function xApiShow($id_act)
{
   GLOBAL $connect,$lg;
   $req_typdev = mysql_num_rows(mysql_query("select * from activite_devoir where actdev_act_no = $id_act"));
   $dev_act = "";
   if ($req_typdev > 0)
   {
      $dev_act = GetDataField ($connect,"select actdev_dev_lb from activite_devoir where
                                         actdev_act_no = $id_act","actdev_dev_lb");
      if ($dev_act == 'xApi TinCan')
          return "&nbsp;&nbsp;<IMG SRC=\"images/gest_parc/xApi.gif\" border='0' ".
                 bulle("Activité au format TinCan -xAPI-","","LEFT","ABOVE",100);
   }
   else
      return '';
}

function encrypt_decrypt($action, $string)
{
    $output = false;

    $encrypt_method = "AES-256-CBC";
    $secret_key = 'MonFormagri';
    $secret_iv = 'FormagriMine';

    // hash
    $key = hash('sha256', $secret_key);

    $iv = substr(hash('sha256', $secret_iv), 0, 16);

    if( $action == 'encrypt' ) {
        $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
        $output = base64_encode($output);
    }
    else if( $action == 'decrypt' ){
        $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
    }

    return $output;
}

function base64url_encode($data)
{
  return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}

function base64url_decode($data)
{
  return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT));
}

function renverse_date($date)
{
   $ch_date = explode("-",$date);
   $date = $ch_date[2]."-".$ch_date[1]."   ".$ch_date[0];
   return $date;
}

function notifier($message)
{
    global $lg;
    require ("lang$lg.inc.php");
    $slow = (!strstr($_SERVER['HTTP_USER_AGENT'],"MSIE")) ? "'slow'" : "";
    $html =  "<div id=\"mien\" class=\"cms\" style=\"padding: 2px;\" ".
             "onClick=\"javascript:\$(document).ready(function() {".
             "if (\$.browser.msie) {".
             "\$('#mien').hide();".
             "}else{".
             "\$('#mien').hide('slow');".
             "}".
             "})\" title=\"$mess_clkF\">".
             stripslashes(html_entity_decode($message,ENT_QUOTES,'ISO-8859-1'))."</div>";
    return $html;
}
function clean_text($text) {
        $avant = array("ø","Â°","â€™","Ã§","ä§","Ã©","ä©","Ã¨","ä¨","Ãª","äª","Ã«","ä«","Ã?","ä?","Ã?","ä?","Ã®","ä®","Ã¯","ä¯","Ã¬","Ã?","ä?","Ã²","ä²","Ã´","ä´","Ã¶","ä¶","Ãµ","Ã³","Ã¸","äµ","ä³","ä¸","Ã?","ä?","Ã?","ä?","Ã ","ä ","Ã¢","ä¢","Ã¤","ä¤","Ã¥","ä¥","Ã?","ä?","Ã?","ä?","Ã¹","Ã»","Ã¼","ä¼","Ã?","Ã?","ä¹","ä»","ä¼");
        $apres = array("°","°","'","ç", "ç", "é", "é", "è", "è", "ê", "ê", "ë", "ë", "Ê", "Ê", "Ë", "Ë", "î", "î", "ï", "ï", "ì", "Î", "Î", "ò", "ò", "ô", "ô", "ö", "ö", "õ", "ó", "ø", "õ", "ó", "ø", "Ô", "Ô", "Ö", "Ö", "à", "à", "â", "â", "ä", "ä", "å", "å", "Â", "Â", "Ä", "Ä", "u", "û", "ü", "ü", "Û", "Ü", "u", "û", "ü");
        $text = str_replace($avant, $apres, $text);
        $avant = array('Ã©','Ã¨','Ãª','Â°','Â','Ã','à´','à®','à¢','à§','à¹','à»','&lt;','&gt;','&quot;','&amp;','|@|','','&nbsp;');
        $apres = array('é','è','ê','°',' ','à','ô','î','â','ç','ù','û','<','>','"','&','&#','',' ');
        $text = str_replace($avant, $apres, $text);
        $text = str_replace('<p>','', $text);
        $text = str_replace('</p>','', $text);
        return $text;
}

function boutret($nb,$balise)
{
    GLOBAL $suffixer;
    $html = "<tr height='40'><td align=left><A HREF=\"javascript:history.go(-$nb);\" ".
            "onmouseover=\"img_annonce.src='images/fiche_identite/boutretourb$suffixer.gif';return true;\" ".
            "onmouseout=\"img_annonce.src='images/fiche_identite/boutretour$suffixer.gif'\">";
    $html .= "<IMG NAME=\"img_annonce\" SRC=\"images/fiche_identite/boutretour$suffixer.gif\" BORDER='0' ".
             "onLoad=\"tempImg=new Image(0,0); tempImg.src='images/fiche_identite/boutretourb$suffixer.gif'\"></A>";
    if ($balise == 1)
       $html .= "</td></tr>";
    return $html;
}

function boutret_sd($nb,$balise)
{
    GLOBAL $suffixer;
    $html = "<tr><td align=left><A HREF=\"javascript:history.go(-$nb);\" ".
            "onmouseover=\"img_annonce.src='../images/fiche_identite/boutretourb$suffixer.gif';return true;\" ".
            "onmouseout=\"img_annonce.src='../images/fiche_identite/boutretour$suffixer.gif'\">";
    $html .= "<IMG NAME=\"img_annonce\" SRC=\"../images/fiche_identite/boutretour$suffixer.gif\" BORDER='0' ".
             "onLoad=\"tempImg=new Image(0,0); tempImg.src='../images/fiche_identite/boutretourb$suffixer.gif'\"></A>";
    if ($balise == 1)
       $html .= "</td></tr>";
    return $html;
}

function anoter($contenu,$large)
{
       global $lg,$mess_nota_bene;
       $largeur = ($large != "") ? $large : "350";
       $ret = "&nbsp;&nbsp;&nbsp;&nbsp;<A HREF=\"javascript:void(0);\" style=\"cursor:help\" ".
              "onclick=\"return overlib('<table><tbody><tr><td width=5></td><td>".addslashes($contenu)."</td></tr></tbody></table>'".
              ",STICKY,ol_hpos,LEFT,ABOVE,WIDTH,$largeur,CAPTION,'<table width=100% border=0 cellspacing=2>".
              "<tbody><tr height=20 width=100%>".
              "<td align=left width=90% nowrap><strong>$mess_nota_bene</strong></td></tr></tbody></table>');\"".
              " onMouseOut=\"return nd();\"><IMG SRC='images/modules/tut_form/icoaide.gif' border='0'></A>";
       return $ret;
}
function msgInst($emailId,$aQui)
{
         global $connect;
         $dateJ = date("Y-m-d");
         $verif_connex = mysql_query("SELECT util_cdn,login from log,utilisateur where ".
                                     "login = util_login_lb and util_cdn = '$emailId' AND ".
                                     "date_debut = '$dateJ' AND date_fin ='0000-00-00' AND ".
                                     "util_cdn != '".$_SESSION['id_user']."'");
         $result = mysql_num_rows($verif_connex);
         if ($result > 0)
         {
                 $lien_mess = "message_instant.php?num=$emailId";
                 $lien_mess= urlencode($lien_mess);
                 $ajout2 = "<div id='msgId' style='float:left;padding-top:5px;margin-left:8px;'><A HREF=\"javascript:void(0);\" ".
                           "title= \"Envoyer un message instantané à votre $aQui\"".
                           " onClick=\"open('".$_SESSION['monURI']."/trace.php?link=$lien_mess','window',".
                           "'scrollbars=no,resizable=yes,width=550,height=130,left=300,top=300')\">" ;

                 $ajout2 .= "<img src='".$_SESSION['monURI']."/images/ecran-annonce/icoMsgInst.gif' border=0></a></div>";
                 return $ajout2;
         }
         else
             return '';
}
function CompGauche($contenu,$large,$num)
{
       global $lg,$monURI;
       $largeur = ($large != "") ? $large : "350";
       $ret = '<span onClick="javascript:var x=$(this).offset();var monTop=x.top;var monLeft=x.left;'.
              '$(\'#ulCompG'.$num.'\').css(\'top\',monTop+15);$(\'#ulCompG'.$num.'\').css(\'left\',monLeft-260);'.
              '$(\'#ulCompG'.$num.'\').toggle();$(\'#ulHwD'.$num.'\').hide();$(\'#ulHwG'.$num.'\').hide();"> '.$contenu.
              '<img src="images/icogog.gif" border="0" style="margin:0 2px 0 20px;cursor:pointer;"></span>';
       return $ret;
}
function CompDroite($contenu,$large,$num)
{
       global $lg,$monURI;
       $largeur = ($large != "") ? $large : "350";
       $ret = '<span onClick="javascript:var x=$(this).offset();var monTop=x.top;var monLeft=x.left;'.
              '$(\'#ulCompD'.$num.'\').css(\'top\',monTop+15);$(\'#ulCompD'.$num.'\').css(\'left\',monLeft+10);'.
              '$(\'#ulCompD'.$num.'\').toggle();$(\'#ulHwD'.$num.'\').hide();$(\'#ulHwG'.$num.'\').hide();"> '.$contenu.
              '<img src="images/icogod.gif" border="0" style="margin:0 10px 0 2px;cursor:pointer;"></span>';
       return $ret;
}
function HwGauche($contenu,$large,$num)
{
       global $lg,$monURI;
       $largeur = ($large != "") ? $large : "350";
       $ret = '<span onClick="javascript:var x=$(this).offset();var monTop=x.top;var monLeft=x.left;'.
              '$(\'#ulHwG'.$num.'\').css(\'top\',monTop+15);$(\'#ulHwG'.$num.'\').css(\'left\',monLeft-260);'.
              '$(\'#ulHwG'.$num.'\').toggle();$(\'#ulCompD'.$num.'\').hide();$(\'#ulCompG'.$num.'\').hide();"> '.$contenu.
              '<img src="images/icogog.gif" border="0" style="margin:0 0 0 20px;cursor:pointer;"></span>';
       return $ret;
}
function HwDroite($contenu,$large,$num)
{
       global $lg,$monURI;
       $largeur = ($large != "") ? $large : "350";
       $ret = '<span onClick="javascript:var x=$(this).offset();var monTop=x.top;var monLeft=x.left;'.
              '$(\'#ulHwD'.$num.'\').css(\'top\',monTop+15);$(\'#ulHwD'.$num.'\').css(\'left\',monLeft+10);'.
              '$(\'#ulHwD'.$num.'\').toggle();$(\'#ulCompD'.$num.'\').hide();$(\'#ulCompG'.$num.'\').hide();"> '.$contenu.
              '<img src="images/icogod.gif" border="0" style="margin:0 10px 0 0;cursor:pointer;"></span>';
       return $ret;
}

function bulle($tit_mess,$caption,$cote,$valigne,$largeur)
{
     GLOBAL $lg;
     if ($valigne == "")
        $valigne="BELOW";
     $ret =  " onMouseOver=\"overlib('<table><tbody><tr><td width=5></td><td>".
              addslashes(str_replace('"','-',html_entity_decode($tit_mess,ENT_QUOTES,'ISO-8859-1')))."</td></tr><tbody></table>',".
             "ol_hpos,$cote,$valigne,WIDTH,'$largeur',OFFSETY,10,DELAY,500,CAPTION,'".
               addslashes(html_entity_decode($caption,ENT_QUOTES,'ISO-8859-1'))."');\" onMouseOut=\"nd();\">";
     if ($tit_mess !='') return $ret; else  return '>';
}

function bullet($tit_mess,$caption,$cote,$valigne,$largeur)
{
  GLOBAL $lg;
  if ($valigne == "")
     $valigne="BELOW";
     
  $ret =  " onMouseOver=\"overlib('<table><tbody><tr><td width=5></td><td>".
              addslashes(str_replace('"','-',html_entity_decode($tit_mess,ENT_QUOTES,'ISO-8859-1')))."</td></tr><tbody></table>',".
             "ol_hpos,$cote,$valigne,WIDTH,'$largeur',OFFSETY,10,DELAY,500,CAPTION,'".
               addslashes(html_entity_decode($caption,ENT_QUOTES,'ISO-8859-1'))."');\" onMouseOut=\"nd();\" ";
  if ($tit_mess !='')
        return $ret;
}

function photo_img($image,$w_img,$h_img)
{
  if ($w_img == '' && $h_img == '')
      list($w_img, $h_img, $type_img, $attr_img) = getimagesize("../images/$image");
  $ret = " onMouseOver=\"overlib('', ABOVE, TEXTCOLOR, '#ffffff', TEXTSIZE, 2, WIDTH, ".$w_img.", HEIGHT, ".$h_img.", BACKGROUND,".
         " '../images/$image', PADX, 60, 20, PADY, 20, 20);\" onMouseOut=\"nd(); ";
  return $ret;
}

function utf2Charset($value,$encodeTo,$encodeFrom='UTF-8')
{
          if (!function_exists('mb_convert_encoding'))
              return $value;
          else
             return mb_convert_encoding($value,$encodeFrom,$encodeTo);
}
function DelAmp($text)
{
   $avant = array('amp;lt','amp;gt','amp;quot','amp;e','amp;a','amp;c','amp;i','amp;o','amp;u');
   $apres = array('lt','gt','quot','e','a','c','i','o','u');
   $text = str_replace($avant, $apres, $text);
   return $text;
}

function nbsp($nb)
{
   if ($nb > 1){$ret='';for ($i=0;$i<$nb;$i++){$ret.= "&nbsp;";}}else $ret = "&nbsp;";return $ret;
}
function tiret($nb)
{
   if ($nb > 1){$ret='';for ($i=0;$i<$nb;$i++){$ret.= "&nbsp;&nbsp;";}}else $ret = "&nbsp;&nbsp; ";return $ret;

}
function image_decale($img,$nb)
{
   if ($nb > 1){$ret='';for ($i=0;$i<$nb;$i++){$ret.= "$img";}}else $ret = "$img";return $ret;

}

function serie_tut($num)
{
      global $lg;
      require ("langues/adm.inc.php");
      $serie_tut='';
      $req_aptut = mysql_query("select util_nom_lb,util_prenom_lb from utilisateur, tuteur where
                               tuteur.tut_tuteur_no=$num and tuteur.tut_apprenant_no = utilisateur.util_cdn order by util_nom_lb");
      $nb_aptut = mysql_num_rows($req_aptut);
      if ($nb_aptut > 0){
         $serie_tut ="<strong><FONT size=2>$msgadm_tut_titbul:</strong></FONT>";
         $ap=0;
         while ($ap < $nb_aptut){
               $lenomapp=mysql_result($req_aptut,$ap,"util_nom_lb");
               $leprenomapp=mysql_result($req_aptut,$ap,"util_prenom_lb");
               $serie_tut .= "<LI>$lenomapp  $leprenomapp</LI>";
            $ap++;
         }
      }
      return $serie_tut;
}
function serie_sup($num)
{
      global $lg;
      require ("langues/adm.inc.php");
      $serie_sup='';
      $req_grptut = mysql_query("select grp_nom_lb from groupe where
                               grp_tuteur_no=$num order by grp_nom_lb");
      $nb_grptut = mysql_num_rows($req_grptut);
      if ($nb_grptut > 0){
         $serie_sup ="<strong><FONT size=2>$msgadm_spv_titbul:</strong></FONT>";
         $gp=0;
         while ($gp < $nb_grptut){
               $lenomgrp=mysql_result($req_grptut,$gp,"grp_nom_lb");
               $serie_sup .= "<LI>$lenomgrp</LI>";
            $gp++;
         }
      }
      return $serie_sup;
}

function serie_form($num)
{
      global $lg,$connect;
      require ("langues/adm.inc.php");
         $serie_form ='';
         $requete_grp = mysql_query ("select * from groupe order by grp_nom_lb");
         $nb_grp_parc = mysql_num_rows($requete_grp);
         if ($nb_grp_parc > 0)
         {
            $gp=0;
            while ($gp < $nb_grp_parc)
            {
                  $id_grp = mysql_result($requete_grp,$gp,"grp_cdn");
                  $lenomgrp=mysql_result($requete_grp,$gp,"grp_nom_lb");
                  $nb_grpform = mysql_num_rows(mysql_query("select presc_cdn from prescription_$id_grp where presc_formateur_no=$num"));
                  if ($nb_grpform > 0 && $serie_form == '')
                      $serie_form = "<strong><FONT size=2>$msgadm_presc_titbul:</strong></FONT>";
                  if ($nb_grpform > 0)
                      $serie_form .= "<LI>$lenomgrp</LI>";
              $gp++;
            }
         }
      return $serie_form;
}
function img_repeat($source,$num)
{
  for ($i=0;$i<$num;$i++)
  {
     $html .= "<img src='$source' border=0>";
  }
  return $html;
}
function serie_presc($num)
{
      global $lg,$connect;
      require ("langues/adm.inc.php");
         $serie_presc= '';
         $requete_grp = mysql_query ("select * from groupe order by grp_nom_lb");
         $nb_grp_parc = mysql_num_rows($requete_grp);
         if ($nb_grp_parc > 0)
         {
            $gp=0;
            while ($gp < $nb_grp_parc)
            {
                  $id_grp = mysql_result($requete_grp,$gp,"grp_cdn");
                  $lenomgrp = mysql_result($requete_grp,$gp,"grp_nom_lb");
                  $nb_grpform = mysql_num_rows(mysql_query("select presc_cdn from prescription_$id_grp where presc_prescripteur_no=$num"));
                  if ($nb_grpform > 0 && $serie_presc == '')
                      $serie_presc ="<strong><FONT size=2>$msgadm_presc_titbul:</strong></FONT>";
                  if ($nb_grpform > 0)
                      $serie_presc .= "<LI>$lenomgrp</LI>";
              $gp++;
            }
         }
      return $serie_presc;
}

function serie_resp($num)
{
      global $lg;
      require ("langues/adm.inc.php");
      $serie_resp='';
      $req_grp = mysql_query("select distinct grp_nom_lb from groupe where
                               grp_resp_no=$num order by grp_nom_lb");
      $nb_grp = mysql_num_rows($req_grp);
      if ($nb_grp > 0){
         $serie_resp ="<strong><FONT size=2>$msgadm_rf_titbul:</strong></FONT>";
         $gp=0;
         while ($gp < $nb_grp){
               $lenomgrp=mysql_result($req_grp,$gp,"grp_nom_lb");
               $serie_resp .= "<LI>$lenomgrp</LI>";
            $gp++;
         }
      }
      return $serie_resp;
}


?>
