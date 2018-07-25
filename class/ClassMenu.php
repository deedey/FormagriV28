<?php
function MenuConception($lien1_concevoir,$lien_retour_concevoir)
{
   GLOBAL $lg,$id_user;
   require ("lang$lg.inc.php");
   $ret = "<A HREF=\"trace.php?link=$lien1_concevoir\" target='main' onClick=\"javascript:document.location.replace('$lien_retour_concevoir');\"".
          " onmouseover=\"img9.src='images/modules/tut_form/icosequen1b.gif';overlib('$mess_concevoir ".
          strtolower("$mess_menu_mon_parc")." , ".strtolower("$mess_menu_mes_seq")." , ".strtolower("$mess_mes_acts").
          "',ol_hpos,LEFT,ABOVE,WIDTH,'150',DELAY,'800',CAPTION, '')\"".
          " onmouseout=\"img9.src='images/modules/tut_form/icosequen1.gif';nd()\">".
          "<IMG NAME=\"img9\" SRC=\"images/modules/tut_form/icosequen1.gif\"  border='0'".
          " onLoad=\"tempImg=new Image(0,0); tempImg.src='images/modules/tut_form/icosequen1b.gif'\"></A>";
   $ret .= "&nbsp;&nbsp;&nbsp;";
   return $ret;
}

function MenuTuteur($lien1_tutorer,$lien_tutorer)
{
   GLOBAL $lg,$id_user;
   require ("lang$lg.inc.php");
   $reqtut = mysql_query("select * from tuteur,utilisateur where tuteur.tut_tuteur_no = $id_user AND tuteur.tut_apprenant_no = utilisateur.util_cdn");
   $nbr = mysql_num_rows($reqtut);
   $reqtut_obs = mysql_query("select * from groupe where grp_tuteur_no = $id_user");
   $nbrobs = mysql_num_rows($reqtut_obs);
   if (($reqtut == TRUE && $nbr > 0) || ($reqtut_obs == TRUE && $nbrobs > 0))
   {
      $ret = "<A HREF=\"trace.php?link=$lien1_tutorer\" target='main' onClick=\"javascript:document.location.replace('$lien_tutorer');\"".
          " onmouseover=\"img10.src='images/modules/tut_form/pticoprotut.gif';overlib('$mess_suiv_app_tut',ol_hpos,LEFT,ABOVE,WIDTH,'150',DELAY,'800',CAPTION, '')\"".
          " onmouseout=\"img10.src='images/modules/tut_form/pticoprotut.gif';nd()\">".
          "<IMG NAME=\"img10\" SRC=\"images/modules/tut_form/pticoprotut.gif\"  border='0'".
          " onLoad=\"tempImg=new Image(0,0); tempImg.src='images/modules/tut_form/pticoprotut.gif'\"></A>";
      $ret .= "&nbsp;&nbsp;&nbsp;";
   }
   else
      $ret = "";
   return $ret;
}
function MenuFormateur($lien1_suivre,$lien_suivre)
{
   GLOBAL $lg,$id_user;
   require ("lang$lg.inc.php");
   $ret = '';
   $req_grp = mysql_query("select * from groupe");
   $nbrgrp = mysql_num_rows($req_grp);
   if ($nbrgrp > 0)
   {
     while ($item = mysql_fetch_object($req_grp))
     {
       $Ext = "_".$item->grp_cdn;
       $passage = 0;
       $reqform =  mysql_query("select * from prescription$Ext where presc_formateur_no = $id_user");
       $nbr = mysql_num_rows($reqform);
       if ($reqform == TRUE && $nbr > 0 && $passage !=1)
       {
           $passage = 1;
           $ret = "<A HREF=\"trace.php?link=$lien1_suivre\" target='main' onClick=\"javascript:document.location.replace('$lien_suivre');\"".
                  " onmouseover=\"img11.src='images/modules/tut_form/pticoproform.gif';overlib('$mess_suiv_app_form',ol_hpos,LEFT,ABOVE,WIDTH,'150',DELAY,'800',CAPTION, '')\"".
                  " onmouseout=\"img11.src='images/modules/tut_form/pticoproform.gif';nd()\">".
                  "<IMG NAME=\"img11\" SRC=\"images/modules/tut_form/pticoproform.gif\"  border='0'".
                  " onLoad=\"tempImg=new Image(0,0); tempImg.src='images/modules/tut_form/pticoproform.gif'\"></A>";
           $ret .= "&nbsp;&nbsp;&nbsp;";
       }
     }
   }
   return $ret;
}
function MenuPrescripteur($lien_presc)
{
   GLOBAL $lg,$id_user,$lien1_presc,$lien2_presc,$lien3_presc,$lien4_presc;
   require ("lang$lg.inc.php");
   $nomb_app = 0;
   $req_grp = mysql_query("select * from groupe");
   $nbrgrp = mysql_num_rows($req_grp);
   if ($nbrgrp > 0)
   {
     while ($item = mysql_fetch_object($req_grp))
     {
       $Ext = "_".$item->grp_cdn;
       $passage = 0;
       $req_app = mysql_query("select * from prescription$Ext ,utilisateur where presc_prescripteur_no = $id_user AND (presc_utilisateur_no = util_cdn OR util_auteur_no = $id_user)");
       $nomb_app += mysql_num_rows($req_app);
     }
   }
   $req_gp =  mysql_query("select * from groupe where grp_resp_no = $id_user");
   $nomb_gp = mysql_num_rows($req_gp);
   $chaine_lien = " target='main' onClick=\"javascript:document.location.replace('$lien_presc');\"".
          " onmouseover=\"img12.src='images/modules/tut_form/pticoprorespon.gif';overlib('$mess_suivi_app_presc',ol_hpos,LEFT,ABOVE,WIDTH,'150',DELAY,'800',CAPTION, '')\"".
          " onmouseout=\"img12.src='images/modules/tut_form/pticoprorespon.gif';nd()\">".
          "<IMG NAME=\"img12\" SRC=\"images/modules/tut_form/pticoprorespon.gif\"  border='0'".
          " onLoad=\"tempImg=new Image(0,0); tempImg.src='images/modules/tut_form/pticoprorespon.gif'\"></A>";
   if ($nomb_app > 0)
       $ret = "<A HREF=\"trace.php?link=$lien1_presc\" $chaine_lien";
   elseif ($nomb_app == 0 && $nomb_gp > 0)
       $ret = "<A HREF=\"trace.php?link=$lien1_presc\" $chaine_lien";
   elseif ($nomb_app == 0 && $nomb_gp == 0)
       $ret = "<A HREF=\"trace.php?link=$lien3_presc\" $chaine_lien";
    $ret .= "&nbsp;&nbsp;";
   return $ret;
}
function MenuAdmin($lien1_adm,$lien_adm)
{
   GLOBAL $lg,$id_user;
   require ("lang$lg.inc.php");
   $ret = "&nbsp;&nbsp;<A HREF=\"trace.php?link=$lien1_adm\" target='main' onClick=\"javascript:document.location.replace('$lien_adm');\" ".
         " onmouseover=\"img13.src='images/modules/tut_form/pticoproadmin.gif';overlib('$mess_admin_retour',ol_hpos,LEFT,ABOVE,WIDTH,'150',DELAY,'800',CAPTION, '')\"".
         " onmouseout=\"img13.src='images/modules/tut_form/pticoproadmin.gif';nd()\">".
         "<IMG NAME=\"img13\" SRC=\"images/modules/tut_form/pticoproadmin.gif\"  border='0'".
         " onLoad=\"tempImg=new Image(0,0); tempImg.src='images/modules/tut_form/pticoproadmin.gif'\"></A>";
   $ret .= "&nbsp;&nbsp;";
   return $ret;
}
?>
