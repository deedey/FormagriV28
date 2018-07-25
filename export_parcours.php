<?php
if (!isset($_SESSION)) session_start();
include ("include/UrlParam2PhpVar.inc.php");
if (!isset($_SESSION['id_user']) || $_SESSION['id_user'] == "")
{
  exit();
}
require "admin.inc.php";
require 'fonction.inc.php';
require "lang$lg.inc.php";
require ('fonction_html.inc.php');
require "langues/module.inc.php";
require "class/class_module.php";
dbConnect();
include ('style.inc.php');

?>
  <SCRIPT language=JavaScript>
    function checkForm(frm) {
      var ErrMsg = "<?php echo $mess_info_no;?>\n";
      var lenInit = ErrMsg.length;
      if (isEmpty(frm.titre)==true)
        ErrMsg += ' - <?php echo $msq_titre;?>\n';
      if (isEmpty(frm.site)==true)
        ErrMsg += ' - <?php echo $mess_quel_site;?>\n';
      if (isEmpty(frm.mail_dest)==false){
        if (isEmail(frm.mail_dest)==false)
          ErrMsg += ' - <?php echo $mess_email_dest;?>\n';
      }
      if (ErrMsg.length > lenInit)
        alert(ErrMsg);
      else
        frm.submit();
    }
  function isEmail(elm) {
   if (elm.value.indexOf(" ") + "" == "-1" && elm.value.indexOf("@") + "" != "-1" && (elm.value.lastIndexOf(".") > elm.value.indexOf("@")) && elm.value != "")
     return true;
   else
     return false;
  }
    function isEmpty(elm) {
      var elmstr = elm.value + "";
      if (elmstr.length == 0)
       return true;
     return false;
   }
  </SCRIPT>
<?php
if (isset($export_parc) && $export_parc == 1)
{
  echo "<CENTER><TABLE bgColor='#298CA0' cellspacing='2' width='98%'>";
  include ("liste_parc.inc.php");
  echo "<TD valign='top' bgColor='#FFFFFF' width='70%' height='100%'>";
  echo "<TABLE cellspacing='1'>";
  echo "<TR><TD background=\"images/fond_titre_table.jpg\" height='40' align='center' valign='center'><Font size='3' color='#FFFFFF'><B>$mess_parcours_impex</B></FONT></TD></TR>";
  echo "<TR height='30'><TD>".aide_div("export",8,0,0,0)."</TD></TR>";
  echo "<TR><TD>";
    $parc_query = mysql_query ("SELECT * from parcours where parcours_cdn = $id_parc");
    $nb_seq = mysql_num_rows ($parc_query);
    $nom = mysql_result ($parc_query,0,"parcours_nom_lb");
    $desc = mysql_result ($parc_query,0,"parcours_desc_cmt");
    $motclefs = mysql_result ($parc_query,0,"parcours_mots_clef");
    $mod_parc = mysql_result ($parc_query,0,"parcours_type_lb");
    echo "<FORM name='form1' ACTION=\"export_parcours.php?export_parc=1&copier_parc=1&id_parc=$id_parc&id_ref=$id_ref_parc&parc=$id_parc\" METHOD='POST'>";
    echo "<INPUT TYPE='HIDDEN'  name='nom' value=\"$nom\" align='middle'>";
    if (isset($structure))
        echo "<INPUT TYPE='HIDDEN'  name='structure' value=\"$structure\">";
    echo "<INPUT TYPE='HIDDEN'  name='liste_vars' value=\"$liste_vars\">";
    echo "<INPUT TYPE='HIDDEN'  name='mod_parc' value=\"$mod_parc\">";
    $site_array = explode("/",getenv('DOCUMENT_ROOT'));
    $site = $site_array[5];
    ?>
  <TABLE bgColor=#FFFFFF cellspacing='0' cellpadding=5>
   <TR>
      <TD nowrap>
         <B><?php  echo $msq_titre;?></B>
      </TD>
      <TD nowrap>
         <INPUT TYPE='TEXT'  name="titre" size="66" value="<?php  echo $nom; ?>" align='middle'>
      </TD>
   </TR>
   <TR>
      <TD nowrap>
         <B><?php  echo $msq_desc_seq ; ?></B>
      </TD>
      <TD>
         <INPUT TYPE='hidden'  name="description" value="<?php  echo addslashes($desc); ?>">
      </TD>
   </TR>
   <TR>
      <TD nowrap>
         <B><?php  echo $mess_mail_mess ; ?></B>
      </TD>
      <TD nowrap>
         <TEXTAREA name="messg" align="middle" rows='10' cols='60'></TEXTAREA><br>
      </TD>
   </TR>
   <TR>
      <TD nowrap>
         <B><?php  echo $mess_email_dest;?></B>
      </TD>
      <TD nowrap>
         <INPUT TYPE='TEXT'  name="mail_dest" size="40" value="<?php if (isset($mail_envoi)) echo $mail_envoi; ?>" align='middle'>
      </TD>
   </TR>
   <TR>
      <TD>
         <B><?php  echo $mess_quel_site;?></B>
      </TD>
      <TD nowrap>
         <INPUT TYPE='TEXT'  name="site" size="40" value="" align='middle'>
      </TD>
   </TR>
   <?php
  echo "<TR height='50'><TD>&nbsp;</TD><TD align='left'><A HREF=\"javascript:checkForm(document.form1);\"  onmouseover=\"img1.src='images/fiche_identite/boutvalidb.gif';return true;\" onmouseout=\"img1.src='images/fiche_identite/boutvalid.gif'\">".
       "<IMG NAME=\"img1\" SRC=\"images/fiche_identite/boutvalid.gif\" BORDER='0' onLoad=\"tempImg=new Image(0,0); tempImg.src='images/fiche_identite/boutvalidb.gif'\"></A>";
  echo "</TD></TR></form></TABLE>";
  echo "</TD></TR></TABLE></TD></TR></TABLE>";
  exit;
}
if ($export_parc == 1 && $copier_parc == 1)
{
   $id_ref = GetDataField ($connect,"SELECT parcours_referentiel_no from parcours where parcours_cdn = $id_parc","parcours_referentiel_no");
   $fichier_sql .= "<?php\nif (!isset($_SESSION)) session_start();\n\$adresse_http=\"http://\".\$_SERVER['SERVER_NAME'];\n".
                   "require \"../admin.inc.php\";\nrequire \"../lang\$lg.inc\";\n".
                   "require \"../fonction.inc\";\ndbConnect();\n\n";
   $fichier_sql .= "\$date_dujour = date (\"Y-m-d\");\n";
   $description = str_replace("\"","-",stripslashes($description));
   $motclefs = str_replace("\"","-","$motclefs");
   $titre = str_replace("\"","-","$titre");
   $fichier_sql .= "\$module_titre = \"$titre\";\n";
   $fichier_sql .= "\$requete= mysql_query(\"SELECT count(*) from parcours where parcours_nom_lb = \\\"\$module_titre\\\"\");\n";
   $fichier_sql .= "\$nb_requete= mysql_result(\$requete,0);\n";
   $fichier_sql .= "if (\$nb_requete > 0){\n";
   $fichier_sql .= "    echo \"Un module portant ce nom : \$module_titre existe déjà sur votre plateforme\";\n";
   $fichier_sql .= "  exit();\n";
   $fichier_sql .= "}\n";
   $fichier_sql .= "\$ref_module = $id_ref;\n";
   $fichier_sql .= "\$requete= mysql_query(\"SELECT count(*) from referentiel where ref_cdn = \$ref_module\");\n";
   $fichier_sql .= "\$nb_requete= mysql_result(\$requete,0);\n";
   $fichier_sql .= "if (\$nb_requete == 0)\n";
   $fichier_sql .= "    \$ref_module = 0;\n";
   $fichier_sql .= "\$id_new_parc = Donne_ID (\$connect,\"SELECT max(parcours_cdn) from parcours\");\n";
   $fichier_sql .= "\$requete= mysql_query(\"insert into parcours values (\$id_new_parc,\\\"$titre\\\",\\\"$description\\\",\\\"$motclefs\\\",'\$ref_module','\$id_user',\\\"\$date_dujour\\\",\\\"\$date_dujour\\\",1,0,\\\"NORMAL\\\")\");\n";
   $seq_query = mysql_query ("SELECT * from sequence,sequence_parcours where seqparc_parc_no = $id_parc and seqparc_seq_no = seq_cdn order by seqparc_ordre_no");
   $nb_seq = mysql_num_rows ($seq_query);
   if ($nb_seq == 0) {
       echo "$msq_noseq_parc<br>";
   }else {
    $j = 0;
    while ($j < $nb_seq){
     $ref_seq = "";
     $Nb_act_seq=0;
     $id_seq = mysql_result ($seq_query,$j,"seq_cdn");
     $nom_seq = mysql_result ($seq_query,$j,"seq_titre_lb");
     $nom_seq = str_replace("\"","-",$nom_seq);
     $desc_seq = mysql_result ($seq_query,$j,"seq_desc_cmt");
     $desc_seq = str_replace("\"","-",$desc_seq);
     $motsclef_seq = mysql_result ($seq_query,$j,"seq_mots_clef");
     $motsclef_seq = str_replace("\"","-",$motsclef_seq);
     $ordre_act = mysql_result ($seq_query,$j,"seq_ordreact_on");
     $duree_seq = mysql_result ($seq_query,$j,"seq_duree_nb");
     $mode_seq = mysql_result ($seq_query,$j,"seq_type_lb");
     $req_refseq = mysql_query("SELECT seqref_referentiel_no from sequence_referentiel where seqref_seq_no = '$id_seq'");
     $nb_refseq = mysql_num_rows($req_refseq);
     if ($nb_refseq > 0){
         $id_ref_seq = mysql_result ($req_refseq,0,"seqref_referentiel_no");
         $fichier_sql .= "\$ref_seq = $id_ref_seq;\n";
     }else{
         $newrs_id=  Donne_ID ($connect,"SELECT max(seqref_cdn) from sequence_referentiel");
         $reparer = mysql_query("INSERT INTO sequence_referentiel VALUES($newrs_id,$id_seq,0)");
         $fichier_sql .= "\$ref_seq = 0;\n";
     }
     if (strstr($mode_seq,"SCORM"))
        $act_query = mysql_query ("SELECT * from scorm_module where mod_seq_no = $id_seq order by mod_ordre_no");
     else
        $act_query = mysql_query ("SELECT * from activite where act_seq_no = $id_seq order by act_ordre_nb");
     $Nb_act_seq = mysql_num_rows ($act_query);
     if ($Nb_act_seq == 0) {
       echo "$msq_noact<br><br>";
     }
     else
     {
        $fichier_sql .= "\$id_new_seq = Donne_ID (\$connect,\"SELECT max(seq_cdn) from sequence\");\n";
        $fichier_sql .= "\$requete= mysql_query(\"insert into sequence values (\$id_new_seq,\\\"$nom_seq\\\",\\\"$desc_seq\\\",\\\"$motsclef_seq\\\",'$ordre_act','$duree_seq','\$id_user',\\\"\$date_dujour\\\",\\\"\$date_dujour\\\",1,0,\\\"$mode_seq\\\")\");\n";
        $id_ordre_seq = GetDataField ($connect,"SELECT seqparc_ordre_no from sequence_parcours where seqparc_seq_no = '$id_seq'","seqparc_ordre_no");
        $fichier_sql .= "\$id_new_seqparc = Donne_ID (\$connect,\"SELECT max(seqparc_cdn) from sequence_parcours\");\n";
        $fichier_sql .= "\$requete= mysql_query(\"insert into sequence_parcours values ('\$id_new_seqparc','\$id_new_seq','\$id_new_parc','$id_ordre_seq')\");\n";
        $fichier_sql .= "\$id_seqref = Donne_ID (\$connect,\"SELECT max(seqref_cdn) from sequence_referentiel\");\n";
        $fichier_sql .= "\$requete= mysql_query(\"insert into sequence_referentiel values (\$id_seqref,'\$id_new_seq','\$ref_seq')\");\n";
        if ($structure != 1 && !strstr($mode_seq,"SCORM"))
        {
          $fichier_sql .= "\$requete= mysql_query(\"SELECT count(*) FROM ressource_new where ress_cat_lb = \\\"$mess_imp_mod\\\"\");\n";
          $fichier_sql .= "\$nb_requete= mysql_result(\$requete,0);\n";
          $fichier_sql .= "if (\$nb_requete == 0)\n{\n";
          $fichier_sql .= "    \$id_new_ress = Donne_ID (\$connect, \"select max(ress_cdn) from ressource_new\");\n";
          $fichier_sql .= "    \$sql_insere= mysql_query(\"INSERT INTO ressource_new (ress_cdn,ress_cat_lb,ress_typress_no,ress_ajout,ress_create_dt,ress_modif_dt) VALUES ('\$id_new_ress',\\\"$mess_imp_mod\\\",'0','Foad',\\\"\$date_dujour\\\",\\\"\$date_dujour\\\")\");\n";
          $fichier_sql .= "    \$id_new_ress2 = Donne_ID (\$connect, \"select max(ress_cdn) from ressource_new\");\n";
          $fichier_sql .= "    \$sql_insere= mysql_query(\"INSERT INTO ressource_new (ress_cdn,ress_cat_lb,ress_typress_no,ress_ajout,ress_create_dt,ress_modif_dt) VALUES ('\$id_new_ress2',\\\"$mess_imp_mod\\\",'\$id_new_ress','Foad',\\\"\$date_dujour\\\",\\\"\$date_dujour\\\"\");\n";
          $fichier_sql .= "    \$parente = \$id_new_ress;\n";
          $fichier_sql .= "}else\n";
          $fichier_sql .= "   \$parente = GetDataField(\$connect,\"SELECT ress_cdn FROM ressource_new WHERE ress_cat_lb = \\\"$mess_imp_mod\\\" AND ress_typress_no = '0' AND ress_titre =\\\"\\\"\",\"ress_cdn\");\n";
        }
        if (strstr($mode_seq,"SCORM"))
        {
            $i = 0;
            while ($i < $Nb_act_seq)
            {
               $nommer[$i] = mysql_result ($act_query,$i,"mod_titre_lb");
               $nommer[$i] = trim($nommer[$i]);
               $desc[$i] = mysql_result ($act_query,$i,"mod_desc_cmt");
               $desc[$i] = str_replace("\"","'",trim($desc[$i]));
               $consigne[$i] = mysql_result ($act_query,$i,"mod_consigne_cmt");
               $consigne[$i] = str_replace("\"","'",trim($consigne[$i]));
               $clef[$i] = mysql_result ($act_query,$i,"mod_motclef_lb");
               $clef[$i] = str_replace("\"","'",trim($clef[$i]));
               $visible[$i] = mysql_result ($act_query,$i,"mod_visible");
               $duree[$i] = mysql_result ($act_query,$i,"mod_duree_nb");
               $niveau[$i] = mysql_result ($act_query,$i,"mod_niveau_no");
               $link[$i] = mysql_result ($act_query,$i,"mod_launch_lb");
               if (!strstr($link[$i],"http://") && $link[$i] !='')
                  $link[$i] = $adresse_http."/".$link[$i];
               $numero[$i] = mysql_result ($act_query,$i,"mod_numero_lb");
               $ordre[$i] = mysql_result ($act_query,$i,"mod_ordre_no");
               $pere[$i] = mysql_result ($act_query,$i,"mod_pere_lb");
               $papa[$i] = mysql_result ($act_query,$i,"mod_pere_no");
               $type[$i] = mysql_result ($act_query,$i,"mod_content_type_lb");
               $prereq[$i] = mysql_result ($act_query,$i,"mod_prereq_lb");
               $maxtime[$i] = mysql_result ($act_query,$i,"mod_maxtimeallowed");
               $timelimit[$i] = mysql_result ($act_query,$i,"mod_timelimitaction");
               $datalms[$i] = mysql_result ($act_query,$i,"mod_datafromlms");
               $score[$i] = mysql_result ($act_query,$i,"mod_masteryscore");
              $i++;
             }
             $i = 0;
             while ($i < $Nb_act_seq)
             {
               $fichier_sql .= "\$id_new_mod = Donne_ID (\$connect,\"select max(mod_cdn) from scorm_module\");\n";
               $fichier_sql .= "\$insert_new_mod = mysql_query (\"insert into scorm_module values ".
                                              "(\$id_new_mod,\$id_parc,\$id_new_seq,".
                                              "\\\"$nommer[$i]\\\",\\\"$desc[$i]\\\",\\\"$consigne[$i]\\\",\\\"$clef[$i]\\\",".
                                              "\\\"$visible[$i]\\\",\\\"$duree[$i]\\\",$niveau[$i],\\\"$link[$i]\\\",\\\"$numero[$i]\\\",".
                                              "\\\"$ordre[$i]\",\\\"$pere[$i]\\\",\\\"$papa[$i]\\\",\\\"$type[$i]\\\",\\\"$prereq[$i]\\\",".
                                              "\\\"$maxtime[$i]\\\",\\\"$timelimit[$i]\\\",\\\"$datalms[$i]\\\",\\\"$score[$i]\\\")\");\n";
               $i++;
             }
          }
          else
          {
            $i = 0;
            while ($i < $Nb_act_seq) {
               $nommer[$i] = mysql_result ($act_query,$i,"act_nom_lb");
               $ordre[$i] = mysql_result ($act_query,$i,"act_ordre_nb");
               $temps[$i] = mysql_result ($act_query,$i,"act_duree_nb");
               $consigne[$i] = mysql_result ($act_query,$i,"act_consigne_cmt");
               $consigne[$i] = str_replace("\"","-",$consigne[$i]);
               $commentaire[$i] = mysql_result ($act_query,$i,"act_commentaire_cmt");
               $commentaire[$i] = str_replace("\"","-",$commentaire[$i]);
               $pass_mult[$i] = mysql_result ($act_query,$i,"act_passagemult_on");
               $acquit[$i] = mysql_result ($act_query,$i,"act_acquittement_lb");
               $devoir[$i] = mysql_result ($act_query,$i,"act_devoirarendre_on");
               $notation[$i] = mysql_result ($act_query,$i,"act_notation_on");
               $ress_norok[$i] = mysql_result ($act_query,$i,"act_ress_on");
               $droit_voir[$i] = mysql_result ($act_query,$i,"act_publique_on");
               $flag[$i] = mysql_result ($act_query,$i,"act_flag_on");
               $id_ress[$i] = mysql_result ($act_query,$i,"act_ress_no");
               if ($id_ress[$i] > 0 && $structure != 1)
               {
                  $url_ress = GetDataField($connect,"SELECT ress_url_lb FROM ressource_new WHERE ress_cdn = $id_ress[$i]","ress_url_lb");
                  if (substr($url_ress,0,11) == "ressources/")
                  {
                    $new_url= $adresse_http."/".$url_ress;
                    $requete_corrige_ress = mysql_query("UPDATE ressource_new SET ress_url_lb = \"$new_url\" WHERE ress_cdn= $id_ress[$i]");
                  }
                  if (substr($url_ress,0,7) == "qcm.php")
                  {
                    $new_url= $adresse_http."/".$url_ress;
                    $requete_corrige_ress = mysql_query("UPDATE ressource_new SET ress_url_lb = \"$new_url\" WHERE ress_cdn= $id_ress[$i]");
                  }
               }
               elseif ($id_ress[$i] > 0 && $structure == 1)
               {
                  $id_ress[$i] = 0;
                  $flag[$i] = 0;
               }
              $i++;
            }
            $i = 0;
            while ($i < $Nb_act_seq)
            {
              if ($structure != 1)
              {
                if ($id_ress[$i] > 0)
                {
                   $ress_query = mysql_query ("SELECT * from ressource_new where ress_cdn = $id_ress[$i]");
                   $auteur_ress= mysql_result($ress_query,0,"ress_auteurs_cmt");
                   $description_ress= mysql_result($ress_query,0,"ress_desc_cmt");
                   $description_ress = str_replace("\"","-",$description_ress);
                   $description_ress = str_replace("\n","<BR>",$description_ress);
                   $url_ress= mysql_result($ress_query,0,"ress_url_lb");
                   $niv= mysql_result($ress_query,0,"ress_niveau");
                   $publicite= mysql_result($ress_query,0,"ress_publique_on");
                   $titre_ress= mysql_result($ress_query,0,"ress_titre");
                   $titre_ress = str_replace("\"","-",$titre_ress);
                   $type_ress= mysql_result($ress_query,0,"ress_type");
                   $sup_ress= mysql_result($ress_query,0,"ress_support");
                   $fichier_sql .= "\$id_new_ress = Donne_ID (\$connect, \"select max(ress_cdn) from ressource_new\");\n";
                   $fichier_sql .= "\$sql_insere= mysql_query(\"INSERT INTO ressource_new (ress_cdn,ress_cat_lb,ress_typress_no,".
                               "ress_url_lb,ress_auteurs_cmt,ress_publique_on,ress_titre,ress_desc_cmt,ress_ajout,ress_public_no,".
                               "ress_type,ress_support,ress_doublon,ress_niveau,ress_create_dt,ress_modif_dt) VALUES ('\$id_new_ress',\\\"$mess_imp_mod\\\",'\$parente',".
                               "\\\"$url_ress\\\",\\\"$auteur_ress\\\",'NON',\\\"$titre_ress\\\",\\\"$description_ress\\\",".
                               "\\\"\$login\\\",'TOUT',\\\"$type_ress\\\",\\\"$sup_ress\\\",'1','$niv',\\\"\$date_dujour\\\",\\\"\$date_dujour\\\")\");\n";
                }
                 else
                    $fichier_sql .= "\$id_new_ress = 0;\n";
              }
              else
                $fichier_sql .= "\$id_new_ress = 0;\n";
              $fichier_sql .= "\$id_new_act = Donne_ID (\$connect,\"SELECT max(act_cdn) from activite\");\n";
              $fichier_sql .= "\$requete= mysql_query(\"insert into activite values (\$id_new_act,\$id_new_seq,$ordre[$i],\\\"$nommer[$i]\\\",\\\"$consigne[$i]\\\",\\\"$commentaire[$i]\\\",'$ress_norok[$i]',\$id_new_ress,$temps[$i],\\\"$pass_mult[$i]\\\",\\\"$acquit[$i]\\\",\\\"$notation[$i]\\\",\\\"$devoir[$i]\\\",'\$id_user',\\\"\$date_dujour\\\",\\\"\$date_dujour\\\",'1',$flag[$i])\");\n";
              $fichier_sql .= "//---------------------------------------------------------------------------------------------------\n";
            $i++;
          }
        }
       }//fin de if ($Nb_act_seq == 0) {
       $fichier_sql .= "//---------------------------------------------------------------------------------------------------\n";
    $j++;
    }// fin de while ($j != $nb_seq) {
  }// fin de if ($Nb_seq == 0) {
  //    $fichier_sql
  $fichier_sql .="\necho \"$mess_parc_recu\";\n";
  $fichier_sql .="exit;\n?>";
  $date_messagerie = date("d/m/Y H:i:s" ,time());
  $date_complement = date("dmyhis" ,time());;
  $parcours_sql = "ModExp_".$site."_".$date_complement.".exp";
    $dir_parc_sql = "ressources/".$parcours_sql;
    $fp = fopen($dir_parc_sql, "w+");
    $fw = fwrite($fp, $fichier_sql);
    fclose($fp);
    chmod($dir_parc_sql,0775);
    if ($mail_dest != "")
    {
       $nom_user = GetDataField ($connect,"select util_nom_lb from utilisateur where util_cdn = $id_user","util_nom_lb");
       $prenom_user = GetDataField ($connect,"select util_prenom_lb from utilisateur where util_cdn = $id_user","util_prenom_lb");
       $mon_mail = GetDataField ($connect, "select util_email_lb from utilisateur where util_cdn=$id_user","util_email_lb");
       $msg = $messg;
       $msg .= "\n\n$mess_deb_impex $mess_parc_impex \n$mess_ag_cordial\n$prenom_user $nom_user @ $adresse_http";
       $from = $mon_mail;
       $reply = $mon_mail;
       $sendto = $mail_dest;
       $suj= "$mess_parcours_impex : $titre";
       $subject = $suj;
       $userfile = "none";
       $origine=$nom_user."  ".$typ_user;
       $nom ="";
       $envoi=mail_attachement($sendto , $subject , htmlentities($msg,ENT_QUOTES,'iso-8859-1') , $userfile , $reply, $nom, $from);
       $origine=$nom_user."  ".$typ_user;
       Connecter($site);
       $msg = $messg;
       $adrExp=urlencode("$adresse_http/ressources/$parcours_sql");
       $msg .= "\n\n$mess_deb_impex_int $mess_parc_impex \n$mess_ag_cordial\n$prenom_user $nom_user @ $adresse_http";
       $msg .= "\n$mess_linkactif :\n<A href='transfert_module.php?file=$adrExp'>$mess_climport : $titre</A>\n";
       $msg=str_replace("\n","<BR>",$msg);
       $user_mail = GetDataField ($connect,"select util_cdn from utilisateur where util_email_lb = \"$mail_dest\"","util_cdn");
       $prenom_user = GetDataField ($connect,"select util_prenom_lb from utilisateur where util_cdn = $id_user","util_prenom_lb");
       $requete = mysql_query("INSERT INTO messagerie (envoyeur,origine,contenu,date,sujet,id_user) VALUES (\"$user_mail\",\"$subject\",\"".
                              htmlentities($msg,ENT_QUOTES,'iso-8859-1')."\",\"$date_messagerie\",\"$origine\",$user_mail)");
       $mess_notif= $mmsg_ntExp.' : '.$titre;
    }

  $liste_vars = str_replace("|","&","$liste_vars");
  $lien="parcours.php?consult=1&mess_notif=$mess_notif&$liste_vars";
  echo "<script language='JavaScript'>";
    echo "document.location.replace(\"$lien\")";
  echo "</script>";
 exit();
//============================================================================================================
}
?>