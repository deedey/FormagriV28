<?php
if (!isset($_SESSION)) session_start();
if (!isset($_SESSION['id_user']) || $_SESSION['id_user'] == "")
{
  exit();
}
include "include/UrlParam2PhpVar.inc.php";
require 'graphique/admin.inc.php';
require 'fonction.inc.php';
require "lang$lg.inc.php";
dbConnect();
if (isset($_FILES['userfile']['tmp_name']) && $_FILES['userfile']['name'] != '')
{
      list($extension,$nom) = getextension($_FILES['userfile']['name']);
      if (in_array(strtolower($extension), array("exe","sh","py","ida","idq","asp","cer","cdx","asa","idc","cfm","dbm","php","php4","php3","inc","shtml","cgi")))
      {

          $mess_notif = $_FILES['userfile']['name']. ' : '.
                        'ce fichier possède une extension ('.$extension.') interdite en téléchargement';
          echo "<script language=\"JavaScript\">";
            echo "alert('$mess_notif');history.go(-1);";
          echo "</script>";
          exit();
      }
}
include "include/varGlobals.inc.php";
$Ext="_$numero_groupe";
$agent=$_SERVER["HTTP_USER_AGENT"];
if (strstr($agent,"Mac") || strstr($agent,"Konqueror"))
  $mac=1;
if (strstr($agent,"Win"))
  $win=1;
$date_op = date("Y-m-d H:i:s");
$date_messagerie = date("Y/m/d H:i:s");
$heure_fiche = substr($date_op,11);
$date_fiche = substr($date_op,0,10);
$reqAct = mysql_query("select * from activite where act_cdn = $id_act");
while ($itemAct = mysql_fetch_object($reqAct))
{
   $titreAct = $itemAct->act_nom_lb;
   $dureeAct = $itemAct->act_duree_nb;
   $pass_multi = $itemAct->act_passagemult_on;
   $acquit =  $itemAct->act_acquittement_lb;
   $notation =  $itemAct->act_notation_on;
   $devoir = $itemAct->act_devoirarendre_on;
   $id_ress = $itemAct->act_ress_no;
   $actseq = $itemAct->act_seq_no;
}
$action_fiche = $mess_suivi_titre;

if (!isset($utilisateur) || (isset($utilisateur) && $utilisateur == ''))
{
   $nom_user = $_SESSION["name_user"];
   $prenom_user =  $_SESSION["prename_user"];
   $qualite = "Apprenant";
   $num_app = $id_user;
}
elseif (isset($utilisateur) && $utilisateur > 0)
{
   $num_app = $utilisateur;
   $prescripteur = GetDataField ($connect,"select presc_prescripteur_no from prescription$Ext where presc_seq_no = $id_seq and presc_utilisateur_no = $num_app","presc_prescripteur_no");
   $formateur = GetDataField ($connect,"select presc_formateur_no from prescription$Ext where presc_seq_no = $id_seq and presc_utilisateur_no = $num_app","presc_formateur_no");
   if ($formateur == $id_user)
     $qualite = $msq_formateur;
   elseif ($prescripteur == $id_user)
     $qualite = $msq_prescripteur;
   $nom_user = GetDataField ($connect,"select util_nom_lb from utilisateur where util_cdn = $utilisateur","util_nom_lb");
   $prenom_user = GetDataField ($connect,"select util_prenom_lb from utilisateur where util_cdn = $utilisateur","util_prenom_lb");
}
if (isset($modif_etat) && $modif_etat== 1 && isset($utilisateur) && $utilisateur > 0)
{
   $act_query = mysql_query ("select * from activite,suivi1$Ext where
                              activite.act_seq_no = $id_seq and
                              suivi_act_no = activite.act_cdn and
                              suivi_utilisateur_no = $utilisateur
                              order by activite.act_ordre_nb");
   $Nb_act_seq = mysql_num_rows ($act_query);
   $id_ress = GetDataField ($connect,"select act_ress_no from activite where act_cdn = $id_act","act_ress_no");
   $commentaire = $mess_fiche_act_repas;
   $new_fiche = Donne_ID ($connect,"select max(fiche_cdn) from fiche_suivi");
   $req_fiche = mysql_query("INSERT INTO fiche_suivi (fiche_cdn,fiche_utilisateur_no,fiche_auteur_no,fiche_qualite_lb,fiche_date_dt,fiche_heure_dt,fiche_commentaire_cmt,fiche_grp_no,fiche_parc_no,fiche_seq_no,fiche_act_no,fiche_typaction_lb) VALUES($new_fiche,$num_app,$id_user,'$qualite','$date_fiche','$heure_fiche',\"$commentaire\",$numero_groupe,$id_parc,$id_seq,$id_act,\"$action_fiche\")");
   if ($id_ress == 0)
   {
      $suivi_etat= mysql_query ("update suivi1$Ext set suivi_etat_lb = 'PRESENTIEL',
                                 suivi_fichier_lb= '',suivi_note_nb1 = '',
                                 suivi_commentaire_cmt='Activité repassée' where
                                 suivi_act_no= $id_act and
                                 suivi_utilisateur_no=$utilisateur");
   }
   else
   {
      $suivi_etat= mysql_query ("update suivi1$Ext set suivi_etat_lb = 'A FAIRE',
                                 suivi_fichier_lb= '',suivi_note_nb1 = '',
                                 suivi_commentaire_cmt='Activité repassée' where
                                 suivi_act_no= $id_act and
                                 suivi_utilisateur_no=$utilisateur");
   }
   $suivi_etat_sequence = GetDataField ($connect,"select suiv2_etat_lb from suivi2$Ext where
                                                  suiv2_seq_no = $id_seq and
                                                  suiv2_utilisateur_no=$utilisateur","suiv2_etat_lb");
   if ($suivi_etat_sequence == "TERMINE" && $Nb_act_seq > 1)
   {
     $change_suivi_etat= mysql_query ("update suivi2$Ext set suiv2_etat_lb = 'EN COURS' where
                                       suiv2_seq_no= $id_seq and
                                       suiv2_utilisateur_no=$utilisateur");
   }
   elseif ($suivi_etat_sequence == "TERMINE" && $Nb_act_seq == 1)
   {
      $change_suivi_etat= mysql_query ("update suivi2$Ext set suiv2_etat_lb = 'A FAIRE' where
                                       suiv2_seq_no= $id_seq and
                                       suiv2_utilisateur_no=$utilisateur");

   }
   $suivi_etat_parcours = GetDataField ($connect,"select suiv3_etat_lb from suivi3$Ext where
                                                  suiv3_parc_no = $id_parc and
                                                  suiv3_utilisateur_no=$utilisateur","suiv3_etat_lb");
   if ($suivi_etat_parcours == "TERMINE")
   {
     $change_suivi_etat= mysql_query ("update suivi3$Ext set suiv3_etat_lb = 'EN COURS' where
                                       suiv3_parc_no= $id_parc and
                                       suiv3_utilisateur_no=$utilisateur");
   }
   $y_aller = actualiser($num_app,$id_act,$id_seq,$id_parc,$fini_note,$note,$fin_seq,$lg,$numero_groupe,$qualite,$id_user,$date_fiche,$heure_fiche,$action_fiche);
   if ($prov == "vm")
   {
      $script="details_parc.php?numero_groupe=$numero_groupe&id_act=$id_act&seq=$id_seq&id_parc=$id_parc&";
   }
   else
   {
      $script= "gest_parc.php?numero_groupe=$numero_groupe&";
   }
   $lien = $script."nom=$nom&hgrp=$hgrp&prenom=$prenom&a_faire=1&vp=1&utilisateur=$utilisateur&modif_etat=1&seq_ouverte=$id_seq&parc_ouvert=$id_parc&visible=$visible&accord=$accord&prq=$prq&depasse=$depasse&proposable=$proposable";
   $lien = urlencode($lien);
   echo "<script language=\"JavaScript\">";
   echo "document.location.replace(\"trace.php?link=$lien\")";
   echo "</script>";
   exit();
}
if (isset($verif_lien_ress) && $verif_lien_ress == 1)
{
  setcookie("id_act", $id_act, time()+60);
  $_SESSION['id_act'] = $id_act;
  // tracking
  $date = date("Y/m/d H:i:s" ,time());
  list($dtj,$hdeb) = explode(" ",$date);
  $numtrq = Donne_ID ($connect,"select max(traq_cdn) from traque");
  $insere_traq= mysql_query("INSERT INTO traque (traq_cdn,traq_util_no,traq_act_no,traq_grp_no,traq_date_dt,traq_hd_dt) values($numtrq,$id_user,$id_act,$numero_groupe,'$dtj','$hdeb')");
  // fin tracking
  $reqSuivi = mysql_query("select * from suivi1$Ext where suivi_act_no= $id_act and suivi_utilisateur_no=$num_app");
  while ($itemSuivi = mysql_fetch_object($reqSuivi))
  {
        $suivi_etat= $itemSuivi->suivi_etat_lb;
        $suivi_comment= $itemSuivi->suivi_commentaire_cmt;
        $suivi_fichier= $itemSuivi->suivi_fichier_lb;
        $suivi_note= $itemSuivi->suivi_note_nb1;
        $suivi_seqajout= $itemSuivi->suivi_seqajout_no;
  }
  $liens = str_replace("|","&",$lien_ress);
  $lien_ress = $liens;
  if ($pointer == 1 && $suivi_etat != "TERMINE")
  {
    $change_etat_seq = mysql_query("update suivi2$Ext set suiv2_etat_lb='EN COURS' where
                                    suiv2_seq_no =$id_seq and
                                    suiv2_utilisateur_no=$num_app");
    $change_etat_parc = mysql_query("update suivi3$Ext set suiv3_etat_lb='EN COURS' where
                                     suiv3_parc_no =$id_parc and
                                     suiv3_utilisateur_no=$num_app");
  }
  if ($suivi_comment != "Activité repassée" && $suivi_etat == "A FAIRE")
  {
      $change_traq = mysql_query("update suivi1$Ext set suivi_date_debut_dt='$date_op' where
                                  suivi_act_no=$id_act and
                                  suivi_utilisateur_no=$num_app");
  }
  elseif ($suivi_etat == "A FAIRE" && $suivi_comment == "Activité repassée")
  {
      $change_traq = mysql_query("update suivi1$Ext set suivi_date_fin_dt='$date_op' where
                                  suivi_act_no=$id_act and
                                  suivi_utilisateur_no=$num_app");
  }
  if ($typ_user == "APPRENANT" && $pass_multi == "OUI" && $acquit == "FORMATEUR_REFERENT" && $suivi_etat == "TERMINE")
  {
      $commentaire = $mess_lanc_act;
      $new_fiche = Donne_ID ($connect,"select max(fiche_cdn) from fiche_suivi");
      $req_fiche = mysql_query("INSERT INTO fiche_suivi (fiche_cdn,fiche_utilisateur_no,fiche_auteur_no,fiche_qualite_lb,fiche_date_dt,fiche_heure_dt,fiche_commentaire_cmt,fiche_grp_no,fiche_parc_no,fiche_seq_no,fiche_act_no,fiche_typaction_lb) VALUES($new_fiche,$num_app,$id_user,'$qualite','$date_fiche','$heure_fiche',\"$commentaire\",$numero_groupe,$id_parc,$id_seq,$id_act,\"$action_fiche\")");
  }
  else
  {
    if ($typ_user == "APPRENANT" && $suivi_etat == "A FAIRE")
    {
      $commentaire = $mess_lanc_act;
      $new_fiche = Donne_ID ($connect,"select max(fiche_cdn) from fiche_suivi");
      if ($acquit == "FORMATEUR_REFERENT" && $pass_multi == "NON")
      {
        $req_fiche = mysql_query("INSERT INTO fiche_suivi (fiche_cdn,fiche_utilisateur_no,fiche_auteur_no,fiche_qualite_lb,fiche_date_dt,fiche_heure_dt,fiche_commentaire_cmt,fiche_grp_no,fiche_parc_no,fiche_seq_no,fiche_act_no,fiche_typaction_lb) VALUES($new_fiche,$num_app,$id_user,'$qualite','$date_fiche','$heure_fiche',\"$commentaire\",$numero_groupe,$id_parc,$id_seq,$id_act,\"$action_fiche\")");
      }
      $change_etat = mysql_query("update suivi1$Ext set suivi_etat_lb='EN COURS' where
                                  suivi_act_no=$id_act and
                                  suivi_utilisateur_no=$num_app");
      $deja_passe = 1;
    }
    if ($typ_user == "APPRENANT" && $acquit == "RESSOURCE" && $notation == 'OUI')
    {
      $commentaire = $mess_lanc_ress_note;
      $new_fiche = Donne_ID ($connect,"select max(fiche_cdn) from fiche_suivi");
      $req_fiche = mysql_query("INSERT INTO fiche_suivi (fiche_cdn,fiche_utilisateur_no,fiche_auteur_no,fiche_qualite_lb,fiche_date_dt,fiche_heure_dt,fiche_commentaire_cmt,fiche_grp_no,fiche_parc_no,fiche_seq_no,fiche_act_no,fiche_typaction_lb) VALUES($new_fiche,$num_app,$id_user,'$qualite','$date_fiche','$heure_fiche',\"$commentaire\",$numero_groupe,$id_parc,$id_seq,$id_act,\"$action_fiche\")");
      $change_etat = mysql_query("update suivi1$Ext set suivi_etat_lb='TERMINE' where
                                  suivi_act_no=$id_act and
                                  suivi_utilisateur_no=$num_app");
    }
    if ($typ_user == "APPRENANT" && (!isset($deja_passe) || (isset($deja_passe) && $deja_passe != 1)) && $pass_multi == "OUI" && $acquit == "FORMATEUR_REFERENT" &&
       (($suivi_fichier != "" && $suivi_comment == "Activité repassée") ||
       ($suivi_fichier == "" && $devoir == 'NON')) && $notation == 'OUI')
    {
      $commentaire = $mess_lanc_act;
      $new_fiche = Donne_ID ($connect,"select max(fiche_cdn) from fiche_suivi");
      $req_fiche = mysql_query("INSERT INTO fiche_suivi (fiche_cdn,fiche_utilisateur_no,fiche_auteur_no,fiche_qualite_lb,fiche_date_dt,fiche_heure_dt,fiche_commentaire_cmt,fiche_grp_no,fiche_parc_no,fiche_seq_no,fiche_act_no,fiche_typaction_lb) VALUES($new_fiche,$num_app,$id_user,'$qualite','$date_fiche','$heure_fiche',\"$commentaire\",$numero_groupe,$id_parc,$id_seq,$id_act,\"$action_fiche\")");
      $change_etat = mysql_query("update suivi1$Ext set suivi_etat_lb='ATTENTE' where
                                  suivi_act_no=$id_act and
                                  suivi_utilisateur_no=$num_app");
    }
    if ($typ_user == "APPRENANT" && $pass_multi == "OUI" && $acquit == "APPRENANT" && $suivi_etat != "TERMINE")
    {
      $commentaire = $mess_lanc_act;
      $new_fiche = Donne_ID ($connect,"select max(fiche_cdn) from fiche_suivi");
      $req_fiche = mysql_query("INSERT INTO fiche_suivi (fiche_cdn,fiche_utilisateur_no,fiche_auteur_no,fiche_qualite_lb,fiche_date_dt,fiche_heure_dt,fiche_commentaire_cmt,fiche_grp_no,fiche_parc_no,fiche_seq_no,fiche_act_no,fiche_typaction_lb) VALUES($new_fiche,$num_app,$id_user,'$qualite','$date_fiche','$heure_fiche',\"$commentaire\",$numero_groupe,$id_parc,$id_seq,$id_act,\"$action_fiche\")");
       $change_etat = mysql_query("update suivi1$Ext set suivi_etat_lb='EN COURS' where
                                   suivi_act_no=$id_act and
                                   suivi_utilisateur_no=$num_app");
    }
    $suivi_etat= GetDataField ($connect,"select suivi_etat_lb from suivi1$Ext where
                                         suivi_act_no= $id_act and
                                         suivi_utilisateur_no=$num_app","suivi_etat_lb");
  }
 if (EduNet($lien_ress) == TRUE ||
      strstr(strtolower($lien_ress),"ressources.eduter-cnpr.fr") ||
      strstr(strtolower($lien_ress),".doc") ||
      strstr(strtolower($lien_ress),".xls") ||
      strstr(strtolower($lien_ress),".xlt") ||
      strstr(strtolower($lien_ress),".ppt") ||
      strstr(strtolower($lien_ress),".pps") ||
      strstr(strtolower($lien_ress),".pdf") ||
      strstr(strtolower($lien_ress),".txt") ||
      strstr(strtolower($lien_ress),".rtf") ||
      strstr(strtolower($lien_ress),".gouv.fr") ||
      strstr(strtolower($lien_ress),"google.com") ||
      strstr(strtolower($lien_ress),"youtu"))
  {
    $ch_heure_deb = explode (":",$hdeb);
    $heure_deb = $ch_heure_deb[0];
    $minutes_deb = $ch_heure_deb[1];
    $sec_deb = $ch_heure_deb[2];
    $duree_act = GetDataField ($connect,"SELECT act_duree_nb FROM activite where act_cdn = $id_act","act_duree_nb");
    $mfin = $heure_deb*60 + $duree_act;
    $hfin = floor($mfin/60);
    $mreste = $mfin%60;
    if ($mreste == 0)
      $hfin = "$hfin:$minutes_deb:$sec_deb";
    else
    {
      $min_fin = $minutes_deb+$mreste;
      if ($min_fin > 59)
      {
        $hfin = $hfin+1;
        $min_fin = $min_fin - 60;
        if ($hfin > 24)
          $hfin = $fin - 24;
      }
      $hfin =  ($hfin < 10) ? '0'.strval($hfin) :$hfin;
      $min_fin =  ($min_fin < 10) ? '0'.strval($min_fin) :$min_fin;
      $sec_deb =  ($sec_deb< 10) ? '0'.strval($sec_deb) :$sec_deb;

      $hfin = "$hfin:$min_fin:$sec_deb";
    }
    $y_aller = actualiser($num_app,$id_act,$id_seq,$id_parc,$fini_note,$note,
                          $fin_seq,$lg,$numero_groupe,$qualite,$id_user,
                          $date_fiche,$heure_fiche,$action_fiche);
    $update_traq = mysql_query("update traque set traq_hf_dt = '$hfin' where traq_cdn = $numtrq");
    if (strstr($lien_ress,'qcm.php?code='))
       $lien_ress .= "&id_act=$id_act";
    //unset($_SESSION['id_act']);
    $agent=getenv("HTTP_USER_AGENT");
    if (strstr($agent,"MSIE"))
    {
       echo "<SCRIPT Language=\"Javascript\">";
          echo "opener.DoTheRefresh();";
       echo "</SCRIPT>";
    }
    else
    {
       echo "<SCRIPT Language=\"Javascript\">";
          echo "opener.DoTheRefresh();";
       echo "</SCRIPT>";
    }
    $req_serv = mysql_query("select * from serveur_ressource");
    $nb_req_serv = mysql_num_rows($req_serv);
    if ($nb_req_serv > 0)
    {
      $i = 0;
      while ($i < $nb_req_serv)
      {
         $adr = mysql_result($req_serv,$i,"serveur_nomip_lb");
         $params = mysql_result($req_serv,$i,"serveur_param_lb");
         $label = mysql_result($req_serv,$i,"serveur_label_lb");
         if ($label != "")
         {
            if (strstr($lien_ress,$adr) && strstr($lien_ress,$label))
            {
                $lien_ress = str_replace("&label=$label","",$lien_ress);
                $lien_ress .= $params;
                echo "<script language=\"JavaScript\">";
                echo "document.location.replace(\"$lien_ress\")";
                echo "</script>";
                break;
            }
         }
         elseif ($label == "" && strstr($lien_ress,"label="))
         {
            $i++;
            continue;
         }
         elseif ($label == "" && !strstr($lien_ress,"label="))
         {
            if (strstr($lien_ress,$adr))
            {
               $lien_ress .= $params;
               echo "<script language=\"JavaScript\">";
                 echo "document.location.replace(\"$lien_ress\")";
               echo "</script>";
                break;
            }
         }
       $i++;
      }
    }
    echo "<script language=\"JavaScript\">";
      echo "document.location.replace(\"$lien_ress\")";
    echo "</script>";
  }
  else
  {
    $req_serv = mysql_query("select * from serveur_ressource");
    $nb_req_serv = mysql_num_rows($req_serv);
    if ($nb_req_serv > 0)
    {
      $i = 0;
      while ($i < $nb_req_serv)
      {
         $adr = mysql_result($req_serv,$i,"serveur_nomip_lb");
         $params = mysql_result($req_serv,$i,"serveur_param_lb");
         $label = mysql_result($req_serv,$i,"serveur_label_lb");
         if ($label != "")
         {
            if (strstr($lien_ress,$adr) && strstr($lien_ress,$label))
            {
                $lien_ress = str_replace("&label=$label","",$lien_ress);
                $lien_ress .= $params;
                $lien = urlencode($lien_ress);
                echo "<script language=\"JavaScript\">";
                  echo "document.location.replace(\"trace.php?link=$lien\")";
                echo "</script>";
                break;
            }
         }
         elseif ($label == "" && strstr($lien_ress,"label="))
         {
            $i++;
            continue;
         }
         elseif ($label == "" && !strstr($lien_ress,"label="))
         {
            if (strstr($lien_ress,$adr))
            {
               $lien_ress .= $params;
               $lien = urlencode($lien_ress);
               echo "<script language=\"JavaScript\">";
                 echo "document.location.replace(\"trace.php?link=$lien\")";
               echo "</script>";
                break;
            }
         }
       $i++;
      }
    }
    if (strstr($lien_ress,'qcm.php?code='))
       $lien_ress .= "&id_act=$id_act";
    $lien = urlencode($lien_ress);
    echo "<script language=\"JavaScript\">";
    echo "document.location.replace(\"trace.php?link=$lien\")";
    echo "</script>";
  }
  exit();
}
include 'style.css';
if (isset($validation) && $validation == 1)
{
  if (!isset($utilisateur) || (isset($utilisateur) && $utilisateur == ''))
  {
    $nom_app = $_SESSION["name_user"];
    $prenom_app = $_SESSION["prename_user"];
  }
  if (isset($utilisateur) && $utilisateur > 0)
  {
    $nom_app = GetDataField ($connect,"select util_nom_lb  from utilisateur where util_cdn = $utilisateur","util_nom_lb");
    $prenom_app = GetDataField ($connect,"select util_prenom_lb  from utilisateur where util_cdn = $utilisateur","util_prenom_lb");
  }
// cas où il s'agit d'un accés apprenant
  if (!isset($utilisateur) || (isset($utilisateur) && $utilisateur == ''))
  {
     $reqSuivi = mysql_query("select * from suivi1$Ext where suivi_act_no= $id_act and suivi_utilisateur_no=$id_user");
     while ($itemSuivi = mysql_fetch_object($reqSuivi))
    {
        $suivi_etat = $itemSuivi->suivi_etat_lb;
        $suivi_comment = $itemSuivi->suivi_commentaire_cmt;
        $suivi_fichier = $itemSuivi->suivi_fichier_lb;
        $suivi_note = $itemSuivi->suivi_note_nb1;
        $suivi_seqajout= $itemSuivi->suivi_seqajout_no;
    }
    if ($actseq == 0)
        $actseq =$suivi_seqajout;
    $actform = GetDataField ($connect,"select presc_formateur_no from prescription$Ext where
                                       presc_seq_no = $actseq and
                                       presc_utilisateur_no = $id_user","presc_formateur_no");
    if($suivi_etat == "PRESENTIEL")
    {
     // tracking Présentiel
     $date = date("Y/m/d H:i:s" ,time());
     list($dtj,$hdeb) = explode(" ",$date);
     $numtrq = Donne_ID ($connect,"select max(traq_cdn) from traque");
     $insere_traq= mysql_query("INSERT INTO traque (traq_cdn,traq_util_no,traq_act_no,traq_grp_no,traq_date_dt,traq_hd_dt,traq_hf_dt) values($numtrq,$id_user,$id_act,$numero_groupe,'$dtj','$hdeb','$hdeb')");
     // fin tracking
    }
    if ($devoir == 'OUI' && $objet != 'telecharger' && $acquit != 'RESSOURCE')
    {// && ($ress_type == "EXERCICE" || $ress_type == "EVALUATION")){
      echo "<CENTER><TABLE bgColor='#298CA0' cellspacing='2' cellpadding = '0'><TR><TD> ";
      echo "<TABLE bgcolor='#FFFFFF'' cellspacing='1' cellpadding = '0'>";
      echo "<TR><TD background=\"images/menu/fond_logo_formagri.jpg\" colspan='2' height='37' align='center'>";
      echo "<Font size='3' color='#FFFFFF'><B>$mess_suivi_env_dev</B></FONT><P></TD></TR>";
      echo "<TR height='50'><TD colspan='2' valign='center'><Font size='2' color='#333333'>$mess_suivi_valid</FONT></TD></TR>";
      $req2=mysql_query("SELECT util_login_lb,util_nom_lb FROM utilisateur WHERE util_cdn='$actform'");
      $id_tut = $actform;
      $nom_tut=mysql_result($req2,0,"util_nom_lb");
      $login_tut=mysql_result($req2,0,"util_login_lb");
      $dir_app="ressources/".$login."_".$id_user."/devoirs";
      $dir_tut="ressources/".$login_tut."_".$id_tut."/devoirs";
      $handle1=opendir($dir_app);
      $drap1 = 0;
      while ($file = readdir($handle1))
      {
        if ($file == $numero_groupe)
        {
          $dir_app="ressources/".$login."_".$id_user."/devoirs/".$numero_groupe;
          $drap1 = 1;
          break;
        }
      }
      closedir($handle1);
      if ($drap1 == 0)
      {
        chdir($dir_app);
        mkdir($numero_groupe,0777);
        chmod($numero_groupe,0777);
          $dir_app="ressources/".$login."_".$id_user."/devoirs/".$numero_groupe;
        chdir("../../../");
      }
      //Vérifie si l'apprenant possede un dossier à son nom dans le tiroir "DEVOIRS" de son formateur sinon il en crée un
      $handle=opendir($dir_tut);
      $drap=0;
      $compare = $login."--".$id_user;
      while ($file = readdir($handle))
      {
        if ($file == $compare)
        {
            $dir_tut="ressources/".$login_tut."_".$id_tut."/devoirs/".$login."--$id_user/".$numero_groupe;
          $drap=1;
          break;
        }
      }
      closedir($handle) ;
      if ($drap==0)
      {
        chdir($dir_tut);
        mkdir($compare,0777);
        chmod($compare,0777);
        chdir($compare);
        chdir("../../../../");
      }
      $drap2 = 0;
      $compare = $numero_groupe;
        $dir_tuteur ="ressources/".$login_tut."_".$id_tut."/devoirs/".$login."--$id_user";

//      chdir("../");
      $handle2=opendir($dir_tuteur);
      while ($file = readdir($handle2))
      {
        if ($file == $compare)
        {
             $dir_tut="ressources/".$login_tut."_".$id_tut."/devoirs/".$login."--$id_user/".$numero_groupe;
           $drap2 = 1;
           break;
        }
      }
      closedir($handle2) ;
      if ($drap2 == 0)
      {
             $dir_tuteur="ressources/".$login_tut."_".$id_tut."/devoirs/".$login."--".$id_user;
           chdir($dir_tuteur);
           mkdir($numero_groupe,0777);
           chmod($numero_groupe,0777);
             $dir_tut="ressources/".$login_tut."_".$id_tut."/devoirs/".$login."--$id_user/".$numero_groupe;
           chdir("../../../");
      }
      //fin du test
      // Phase d'upload du fichier
      echo "<center><FORM NAME='form1' action=\"suivi.php?numero_groupe=$numero_groupe&validation=1&objet=telecharger&hgrp=$hgrp&prov=$prov&id_act=$id_act&id_tut=$id_tut&id_seq=$id_seq&id_parc=$id_parc&visible=$visible&accord=$accord&prq=$prq&depasse=$depasse&proposable=$proposable\" method='POST' ENCTYPE='multipart/form-data'>";
      echo "<TR height='40'><TD colspan='2' nowrap>";
      echo "<INPUT TYPE='file' name='userfile' enctype='multipart/form-data'>";
      echo "<INPUT type='hidden' name='MAX_FILE_SIZE' value='2000000'>";
      echo "<INPUT type='hidden' name='dir_app' value=\"$dir_app\">";
      echo "<INPUT type='hidden' name='dir_tut' value=\"$dir_tut\"</TD></TR>";
      if (isset($_SESSION['IsOff']) && $_SESSION['IsOff'] == 0)
         echo "<TR height='40'><TD colspan='2'><INPUT type='checkbox' name='emailing' ".
              "onMouseOver=\"overlib('<TABLE border=0 cellspacing=2 cellpadding=6><TR><TD align=left><font size=2>".
              addslashes($mess_fic_email)."</font></TD></TR></TABLE>',ol_hpos,LEFT,ABOVE,WIDTH,'280')\" ".
              "onMouseOut=\"nd();\">$mess_mess_ext</TD></TR>";
      echo "<TR height='40'><TD align=left width=30% valign='center'><A HREF=\"javascript:history.back();\" ".
           "onmouseover=\"img_annonce.src='images/fiche_identite/boutretourb.gif';return true;\" ".
           "onmouseout=\"img_annonce.src='images/fiche_identite/boutretour.gif'\">";
      echo "<IMG NAME=\"img_annonce\" SRC=\"images/fiche_identite/boutretour.gif\" BORDER='0' ".
           "onLoad=\"tempImg=new Image(0,0); tempImg.src='images/fiche_identite/boutretourb.gif'\"></A></TD>";
      echo "<TD align='center' valign='center'><A HREF=\"javascript:document.form1.submit();\" ".
           "onmouseover=\"img1.src='images/fiche_identite/boutvalidb.gif';return true;\" ".
           "onmouseout=\"img1.src='images/fiche_identite/boutvalid.gif'\">".
           "<IMG NAME=\"img1\" SRC=\"images/fiche_identite/boutvalid.gif\" ALT='$mess_gen_valider' BORDER='0' ".
           "onLoad=\"tempImg=new Image(0,0); tempImg.src='images/fiche_identite/boutvalidb.gif'\"></A>";
      echo "</TD></TR></FORM></TABLE></TD></TR></TABLE>";
    exit();
   }
    // télécharge le fichier devoir dans le casier du formateur et change l'état suivi de l'activité
    if (isset($objet) && $objet == "telecharger" && isset($_FILES['userfile']['tmp_name']) && strstr($_FILES['userfile']['name'],'.'))
    {
        $fichier_test = $_FILES['userfile']['name'];
        $nom_final = modif_nom($fichier_test);
          $dir_app = "ressources/".$login."_".$id_user."/devoirs/$numero_groupe";
          $dest_file_app = $dir_app."/".$nom_final;
          $dest_file = $dir_tut."/".$nom_final;
        $newFile= str_replace('\\','/',$dir_app)."/".$nom_final;
        $source_file = $_FILES['userfile']['tmp_name'];
        $copier = move_uploaded_file($_FILES['userfile']['tmp_name'] , $dest_file_app);
        $copier = copy($dest_file_app , $dest_file);
        $commentaire = $mess_upload_fiche_app." : $nom_final";
        $new_fiche = Donne_ID ($connect,"select max(fiche_cdn) from fiche_suivi");
        $req_fiche = mysql_query("INSERT INTO fiche_suivi (fiche_cdn,fiche_utilisateur_no,fiche_auteur_no,fiche_qualite_lb,fiche_date_dt,fiche_heure_dt,fiche_commentaire_cmt,fiche_grp_no,fiche_parc_no,fiche_seq_no,fiche_act_no,fiche_typaction_lb) VALUES($new_fiche,$num_app,$id_user,'$qualite','$date_fiche','$heure_fiche',\"$commentaire\",$numero_groupe,$id_parc,$id_seq,$id_act,\"$action_fiche\")");
        $change_etat = mysql_query("update suivi1$Ext set suivi_etat_lb='ATTENTE' where
                                    suivi_act_no=$id_act and
                                    suivi_utilisateur_no=$id_user");
        $inserer_fichier = mysql_query("update suivi1$Ext set suivi_fichier_lb=\"$newFile\" where
                                        suivi_act_no=$id_act and
                                        suivi_utilisateur_no=$id_user");
//Mail au formateur référent
        $adr_mail_tut = GetDataField ($connect, "select util_email_lb from utilisateur where util_cdn=$id_tut","util_email_lb");
        $adr_mail_app = $_SESSION["email_user"];
        $nom_user= $_SESSION["name_user"];
        $prenom_user= $_SESSION["prename_user"];
        $nom_tut=GetDataField ($connect,"select util_nom_lb from utilisateur where util_cdn='$id_tut'","util_nom_lb");
        $prenom_tut=GetDataField ($connect,"select util_prenom_lb from utilisateur where util_cdn='$id_tut'","util_prenom_lb");
        $message = "$mess_suivi_env_act $prenom_user $nom_user:\n\n$mess_gen_date $date_op\n$mess_suivi_val_act $prenom_tut $nom_tut\n$mess_suivi_casier $nom_user\n$mess_suivi_nom_fic $nom_final\n$mess_ag_cordial\n\n";
        $from = "$adr_mail_app";
        $reply = "$adr_mail_app";
        $sendto = "$adr_mail_tut";
        $subject = StripSlashes($mess_suivi_dev_rendre);
        $msg = StripSlashes($message);
        $origine=$nom_user."  ".$typ_user;
        $nom = $nom_final;
        if ($dest_file == "")
        {
          $nom = "";
          $userfile = "none";
        }
        if (isset($sendto) && $sendto != "" && $emailing =='on' && isset($_SESSION['IsOff']) && $_SESSION['IsOff'] == 0)
           $envoi=mail_attachement($sendto , $subject , $msg , $dest_file , $reply, $nom, $from);
        $message_base ="$mess_suivi_env_act $prenom_user $nom_user :<BR>$mess_gen_date $date_op<BR>$mess_suivi_val_act $prenom_tut $nom_tut".
                       "<BR>$mess_suivi_casier $nom_user<BR>$mess_suivi_nom_fic $nom_final<BR>$mess_ag_cordial";
        $max_numero = Donne_ID ($connect,"select MAX(mess_cdn) from messagerie");
        $requete = mysql_query("INSERT INTO messagerie (mess_cdn,envoyeur,origine,contenu,mess_fichier_lb,date,sujet,id_user) VALUES ".
                               "($max_numero,'$id_user','$mess_suivi_titre',\"$message_base\",\"$dest_file\",'$date_messagerie','$subject',$id_tut)");
//Mail à l'apprenant lui-même
        $message = "$mess_suivi_env_act_app $prenom_tut $nom_tut\n\n$mess_gen_date  $date_op\n".
                   "$mess_suivi_val_act $prenom_tut $nom_tut\n$mess_suivi_nom_fic $nom_final\n$mess_ag_cordial\n\n";
        $from = "$adr_mail_app";
        $reply = "$adr_mail_app";
        $sendto = "$adr_mail_app";
        $subject = StripSlashes($mess_suivi_dev_rendre);
        $msg = StripSlashes($message);
        $origine=$nom_user."  ".$typ_user;
        $nom = $nom_final;
        if ($dest_file == "")
        {
          $nom = "";
          $userfile = "none";
        }
        if (isset($sendto) && $sendto != "" && $emailing =='on' && isset($_SESSION['IsOff']) && $_SESSION['IsOff'] == 0)
           $envoi=mail_attachement($sendto , $subject , $msg , $dest_file , $reply, $nom, $from);
        $message_base = "$mess_suivi_env_act_app $prenom_tut $nom_tut<BR>$mess_gen_date  $date_op<BR>".
                        "$mess_suivi_val_act $prenom_tut $nom_tut<BR>$mess_suivi_nom_fic $nom_final<BR>$mess_ag_cordial";
//        $max_num = Donne_ID ($connect,"select MAX(mess_cdn) from messagerie");
//        $requete = mysql_query("INSERT INTO messagerie (mess_cdn,envoyeur,origine,contenu,date,sujet,id_user) VALUES ($max_num,'$id_user','$mess_suivi_titre',\"$message_base\",'$date_messagerie','$subject',$id_user)");
    }
    elseif (isset($objet) && $objet == "telecharger" && !isset($_FILES['userfile']['tmp_name']))
    {
       echo "<script language=\"JavaScript\">";
       echo "document.location.replace(\"javascript:history.go(-1);\")";
       echo "</script>";
    }
    if ($devoir == "NON" && $notation == "NON" && $suivi_etat == "PRESENTIEL")
    { 
       $commentaire = $mess_terme_presentiel;
       $new_fiche = Donne_ID ($connect,"select max(fiche_cdn) from fiche_suivi");
       $req_fiche = mysql_query("INSERT INTO fiche_suivi (fiche_cdn,fiche_utilisateur_no,fiche_auteur_no,fiche_qualite_lb,fiche_date_dt,fiche_heure_dt,".
                                "fiche_commentaire_cmt,fiche_grp_no,fiche_parc_no,fiche_seq_no,fiche_act_no,fiche_typaction_lb) ".
                                "VALUES($new_fiche,$num_app,$id_user,'$qualite','$date_fiche','$heure_fiche',\"$commentaire\",$numero_groupe,$id_parc,$id_seq,$id_act,\"$action_fiche\")");
       $change_etat = mysql_query("update suivi1$Ext set suivi_etat_lb='TERMINE',suivi_date_debut_dt='$date_op',suivi_date_fin_dt='$date_op'
                                  where suivi_act_no=$id_act and suivi_utilisateur_no=$id_user");
    }
    elseif ($devoir == "NON" && $notation == "NON" && $suivi_etat != "PRESENTIEL")
    {
       $commentaire = $mess_terme_fiche;
       $new_fiche = Donne_ID ($connect,"select max(fiche_cdn) from fiche_suivi");
       $req_fiche = mysql_query("INSERT INTO fiche_suivi (fiche_cdn,fiche_utilisateur_no,fiche_auteur_no,fiche_qualite_lb,fiche_date_dt,fiche_heure_dt,".
                                "fiche_commentaire_cmt,fiche_grp_no,fiche_parc_no,fiche_seq_no,fiche_act_no,fiche_typaction_lb) VALUES ".
                                "($new_fiche,$num_app,$id_user,'$qualite','$date_fiche','$heure_fiche',\"$commentaire\",$numero_groupe,$id_parc,$id_seq,$id_act,\"$action_fiche\")");
       $change_etat = mysql_query("update suivi1$Ext set suivi_etat_lb='TERMINE' where
                                   suivi_act_no=$id_act and
                                   suivi_utilisateur_no=$id_user");
    }
    elseif ($devoir == "NON" && $notation == "OUI" && $acquit != "RESSOURCE")
       $change_etat = mysql_query("update suivi1$Ext set suivi_etat_lb='ATTENTE' where
                                   suivi_act_no=$id_act and
                                   suivi_utilisateur_no=$id_user");
    elseif ($devoir == "OUI" && $notation == "NON" && $acquit != "RESSOURCE")
       $change_etat = mysql_query("update suivi1$Ext set suivi_etat_lb='TERMINE' where
                                   suivi_act_no=$id_act and
                                   suivi_utilisateur_no=$id_user");
  }
// cas où il s'agit d'un accés formateur_referent
  if ($utilisateur > 0)
  {
    $reqSuivi = mysql_query("select * from suivi1$Ext where suivi_act_no= $id_act and suivi_utilisateur_no=$utilisateur");
    while ($itemSuivi = mysql_fetch_object($reqSuivi))
    {
        $suivi_etat = $itemSuivi->suivi_etat_lb;
        $suivi_comment = $itemSuivi->suivi_commentaire_cmt;
        $suivi_fichier = $itemSuivi->suivi_fichier_lb;
        $suivi_note = $itemSuivi->suivi_note_nb1;
        $suivi_seqajout= $itemSuivi->suivi_seqajout_no;
    }
    if ($actseq == 0)
        $actseq =$suivi_seqajout;
    $actform = GetDataField ($connect,"select presc_formateur_no from prescription$Ext where
                                       presc_seq_no = $actseq and
                                       presc_utilisateur_no = $utilisateur","presc_formateur_no");
    if ($noter == 1)
    {
      echo "<CENTER><TABLE bgColor='#298CA0' cellspacing='2' cellpadding='0'><TR><TD> ";
      echo "<TABLE bgcolor='#FFFFFF' cellspacing='1' cellpadding='0' border='0'><TR height='37'>";
      echo "<TD background=\"images/menu/fond_logo_formagri.jpg\" valign='center' colspan='2' align='center'>";
      echo "<Font size='3' color='#FFFFFF'><B>$msq_not_act_form</B></FONT></TD></TR>";
      echo "<FORM NAME='form1' action=\"suivi.php?numero_groupe=$numero_groupe&fin_seq=$fin_seq&prov=$prov&fini_note=1&hgrp=$hgrp&id_act=$id_act&id_seq=$id_seq&id_parc=$id_parc&utilisateur=$utilisateur&visible=$visible&accord=$accord&prq=$prq&depasse=$depasse&proposable=$proposable\" method='POST'>";
      echo "<TR height='50'><TD valign='center' colspan='2' ><B>$mess_suivi_notation</FONT></B></TD></TR>";
      echo "<TR height='50'><TD valign='center' width = '25%'><SELECT name='note'>";
      echo "<OPTION VALUE='$mess_suivi_acq'>$mess_suivi_acq</OPTION>
          <OPTION VALUE='$mess_suivi_noacq'>$mess_suivi_noacq</OPTION>
          <OPTION VALUE='A'>A</OPTION>
          <OPTION VALUE='B'>B</OPTION>
          <OPTION VALUE='C'>C</OPTION>
          <OPTION VALUE='D'>D</OPTION>
          <OPTION VALUE='E'>E</OPTION>
          <OPTION VALUE='20'>20</OPTION>
          <OPTION VALUE='19'>19</OPTION>
          <OPTION VALUE='18'>18</OPTION>
          <OPTION VALUE='17'>17</OPTION>
          <OPTION VALUE='16'>16</OPTION>
          <OPTION VALUE='15'>15</OPTION>
          <OPTION VALUE='14'>14</OPTION>
          <OPTION VALUE='13'>13</OPTION>
          <OPTION VALUE='12'>12</OPTION>
          <OPTION VALUE='11'>11</OPTION>
          <OPTION VALUE='10'>10</OPTION>
          <OPTION VALUE='9'>09</OPTION>
          <OPTION VALUE='8'>08</OPTION>
          <OPTION VALUE='7'>07</OPTION>
          <OPTION VALUE='6'>06</OPTION>
          <OPTION VALUE='5'>05</OPTION>
          <OPTION VALUE='4'>04</OPTION>
          <OPTION VALUE='3'>03</OPTION>
          <OPTION VALUE='2'>02</OPTION>
          <OPTION VALUE='1'>01</OPTION>
          <OPTION VALUE='0'>0</OPTION>
        </SELECT></TD>";
        echo "<TD valign='center'><A HREF=\"javascript:document.form1.submit();\" ".
             "onmouseover=\"img1.src='images/fiche_identite/boutvalidb.gif';return true;\" ".
             "onmouseout=\"img1.src='images/fiche_identite/boutvalid.gif'\">".
             "<IMG NAME=\"img1\" SRC=\"images/fiche_identite/boutvalid.gif\" ALT='$mess_gen_valider' BORDER='0' ".
             "onLoad=\"tempImg=new Image(0,0); tempImg.src='images/fiche_identite/boutvalidb.gif'\"></A></TD></TR>";
        echo "<TR height='50'><TD align=left valign='center' colspan='2'><A HREF=\"javascript:history.back();\" ".
             "onmouseover=\"img_annonce.src='images/fiche_identite/boutretourb.gif';return true;\" ".
             "onmouseout=\"img_annonce.src='images/fiche_identite/boutretour.gif'\">";
        echo "<IMG NAME=\"img_annonce\" SRC=\"images/fiche_identite/boutretour.gif\" BORDER='0' ".
             "onLoad=\"tempImg=new Image(0,0); tempImg.src='images/fiche_identite/boutretourb.gif'\"></A>";
        echo "</TD></TR></FORM></TABLE></TD></TR></TABLE>";
      exit();
    }
    if (isset($envoyer) && $envoyer == 1 && (!isset($objet) || isset($objet) && $objet != "telecharger"))
    {
      // Phase d'upload du fichier
      echo "<CENTER><TABLE bgColor='#298CA0' cellspacing='2' cellpadding='0' width='50%'><TR><TD width='100%'> ";
      echo "<TABLE bgcolor='#FFFFFF' cellspacing='1' cellpadding='0' width='100%'><TR height='45'>";
      echo "<TD background=\"images/menu/fond_logo_formagri.jpg\" colspan='2' valign='center' align='center'>";
      echo "<Font size='4' color='#FFFFFF'><B>$mess_suivi_env_dev</B></FONT></TD></TR>";
      echo "<TR height='50'><TD colspan='2'>$trvx_form_app</TD></TR>";
      echo "<FORM name='form1' action=\"suivi.php?numero_groupe=$numero_groupe&envoyer=1&utilisateur=$utilisateur&validation=1&objet=telecharger&hgrp=$hgrp&prov=$prov&id_act=$id_act&id_tut=$id_tut&id_seq=$id_seq&id_parc=$id_parc&visible=$visible&accord=$accord&prq=$prq&depasse=$depasse&proposable=$proposable\" method='POST' ENCTYPE='multipart/form-data'>";
      echo "<TR height='40'><TD colspan='2' nowrap>";
      echo "<INPUT TYPE='file' name='userfile' enctype='multipart/form-data'>";
      echo "</TD></TR><INPUT type='hidden' name='MAX_FILE_SIZE' value='2000000'>";
      if (isset($_SESSION['IsOff']) && $_SESSION['IsOff'] == 0)
         echo "<TR height='40'><TD colspan='2'><INPUT type='checkbox' name='emailing' ".
              "onMouseOver=\"overlib('<TABLE border=0 cellspacing=2 cellpadding=6><TR><TD align=left><font size=2>".
              addslashes($mess_fic_email)."</font></TD></TR></TABLE>',ol_hpos,LEFT,ABOVE,WIDTH,'280')\" ".
              "onMouseOut=\"nd();\">$mess_mess_ext</TD></TR>";
      echo "<TR height='40'><TD align=left width=30% valign='center'><A HREF=\"javascript:history.back();\" ".
           "onmouseover=\"img_annonce.src='images/fiche_identite/boutretourb.gif';return true;\" ".
           "onmouseout=\"img_annonce.src='images/fiche_identite/boutretour.gif'\">";
      echo "<IMG NAME=\"img_annonce\" SRC=\"images/fiche_identite/boutretour.gif\" BORDER='0' ".
           "onLoad=\"tempImg=new Image(0,0); tempImg.src='images/fiche_identite/boutretourb.gif'\"></A></TD>";
      echo "<TD align='center' valign='center'><A HREF=\"javascript:document.form1.submit();\" ".
           "onmouseover=\"img1.src='images/fiche_identite/boutvalidb.gif';return true;\" ".
           "onmouseout=\"img1.src='images/fiche_identite/boutvalid.gif'\">".
           "<IMG NAME=\"img1\" SRC=\"images/fiche_identite/boutvalid.gif\" ALT='$mess_gen_valider' BORDER='0' ".
           "onLoad=\"tempImg=new Image(0,0); tempImg.src='images/fiche_identite/boutvalidb.gif'\"></A>";
      echo "</TD></TR></FORM></TABLE></TD></TR></TABLE>";
    exit();
   }
   if (isset($envoyer) && $envoyer == 1)
   {
    if ((isset($utilisateur) && $utilisateur > 0 && $actform == $id_user && $devoir == "NON" &&
        ($notation == "NON" || ($notation == "OUI" && $suivi_etat == "PRESENTIEL"))) &&
        (($pass_multi == "OUI" && $acquit == "APPRENANT") || $suivi_etat == "PRESENTIEL"))
    {
      if ($suivi_comment != "Activité repassée" && $suivi_etat == "PRESENTIEL")
        $change_etat = mysql_query("update suivi1$Ext set suivi_date_debut_dt='$date_op' where
                                    suivi_act_no=$id_act and
                                    suivi_utilisateur_no=$utilisateur");
      elseif ($suivi_etat == "PRESENTIEL" && $suivi_comment == "Activité repassée")
        $change_etat = mysql_query("update suivi1$Ext set suivi_date_fin_dt='$date_op' where
                                    suivi_act_no=$id_act and
                                    suivi_utilisateur_no=$utilisateur");
      $change_etat = mysql_query("update suivi1$Ext set suivi_etat_lb='TERMINE' where
                                  suivi_act_no=$id_act and
                                  suivi_utilisateur_no=$utilisateur");
    }
    if ($devoir == "OUI" && $notation == "OUI" && $acquit == "FORMATEUR_REFERENT" && $objet == "telecharger" && isset($_FILES['userfile']['tmp_name']))
    {
      $login_app = GetDataField ($connect,"select util_login_lb from utilisateur where util_cdn = $utilisateur","util_login_lb");
      $actseq = GetDataField ($connect,"select act_seq_no from activite where act_cdn = $id_act","act_seq_no");
      $req2=mysql_query("SELECT util_login_lb,util_nom_lb FROM utilisateur WHERE util_cdn='$utilisateur'");
      $nom_app=mysql_result($req2,0,"util_nom_lb");
      $login_app=mysql_result($req2,0,"util_login_lb");
      // Phase d'upload du fichier
      // télécharge le fichier devoir dans le casier du formateur et change l'état suivi de l'activité
      if ($s_exp == "lx")
      {
        $dir_app = "ressources/".$login_app."_".$utilisateur."/devoirs/$numero_groupe";
        $fichier_test = $_FILES['userfile']['name'];
        $nom_fichier = modif_nom($fichier_test);
        $dest_file=$dir_app."/corrige_".$nom_fichier;
      }
      else
      {
        $dir_app = "ressources\\".$login_app."_".$utilisateur."\\devoirs\\$numero_groupe";
        $fichier_test = $_FILES['userfile']['name'];
        $nom_fichier = modif_nom($fichier_test);
        $dest_file=$dir_app."\\corrige_".$nom_fichier;
      }
      $commentaire = $mess_upload_fiche." : corrige_".$nom_fichier;
      $new_fiche = Donne_ID ($connect,"select max(fiche_cdn) from fiche_suivi");
      $req_fiche = mysql_query("INSERT INTO fiche_suivi (fiche_cdn,fiche_utilisateur_no,fiche_auteur_no,fiche_qualite_lb,fiche_date_dt,".
                               "fiche_heure_dt,fiche_commentaire_cmt,fiche_grp_no,fiche_parc_no,fiche_seq_no,fiche_act_no,fiche_typaction_lb) VALUES ".
                               "($new_fiche,$num_app,$id_user,'$qualite','$date_fiche','$heure_fiche',\"$commentaire\",$numero_groupe,$id_parc,$id_seq,$id_act,\"$action_fiche\")");
      $source_file = $_FILES['userfile']['tmp_name'];
      $copier = move_uploaded_file($_FILES['userfile']['tmp_name'] , $dest_file);
      $change_etat = mysql_query("UPDATE suivi1$Ext SET suivi_etat_lb='TERMINE' where
                                  suivi_act_no = $id_act and
                                  suivi_utilisateur_no = $utilisateur");
//Mail au formateur référent
        $adr_mail_tut = GetDataField ($connect, "select util_email_lb from utilisateur where util_cdn=$id_user","util_email_lb");
        $adr_mail_app = GetDataField ($connect, "select util_email_lb from utilisateur where util_cdn=$utilisateur","util_email_lb");
        $nom_user=GetDataField ($connect,"select util_nom_lb from utilisateur where util_cdn='$utilisateur'","util_nom_lb");
        $prenom_user=GetDataField ($connect,"select util_prenom_lb from utilisateur where util_cdn='$utilisateur'","util_prenom_lb");
        $nom_tut=GetDataField ($connect,"select util_nom_lb from utilisateur where util_cdn='$id_user'","util_nom_lb");
        $prenom_tut=GetDataField ($connect,"select util_prenom_lb from utilisateur where util_cdn='$id_user'","util_prenom_lb");
        $message = "$mess_suivi_dev_cor $prenom_user $nom_user:\n\n$mess_gen_date  $date_op\n$mess_suivi_act_cor \n$mess_suivi_fic_cor$nom_fichier\n$mess_ag_cordial\n\n";
        $from = "$adr_mail_tut";
        $reply = "$adr_mail_tut";
        $sendto = "$adr_mail_tut";
        $suj= "$mess_suivi_suj_env $prenom_user $nom_user";
        $subject = StripSlashes($suj);
        $msg = StripSlashes($message);
        $origine=$nom_user."  ".$typ_user;
        $nom ="corrige_".$nom_fichier;
        if ($nom_fichier == "")
        {
          $nom = "";
          $userfile = "none";
        }
        if (isset($sendto) && $sendto != "" && $emailing =='on' && isset($_SESSION['IsOff']) && $_SESSION['IsOff'] == 0)
           $envoi=mail_attachement($sendto , $subject , $msg , $dest_file , $reply, $nom, $from);
        $message_base ="$mess_suivi_dev_cor $prenom_user $nom_user :<BR>$mess_gen_date  $date_op<BR>$mess_suivi_act_cor<BR>$mess_suivi_fic_cor$nom_fichier<BR>$mess_ag_cordial";
//        $max_numero = Donne_ID ($connect,"select max(mess_cdn) from messagerie");
//        $requete = mysql_query("INSERT INTO messagerie (mess_cdn,envoyeur,origine,contenu,date,sujet,id_user) VALUES ($max_numero,'$id_user',\"$mess_suivi_titre\",\"$message_base\",'$date_messagerie',\"$subject\",$id_tut)");
//Mail a l'apprenant
        $adr_mail_app = GetDataField ($connect, "select util_email_lb from utilisateur where util_cdn=$utilisateur","util_email_lb");
        $message = "$mess_suivi_env_act_tut $prenom_tut $nom_tut\n\n$mess_gen_date $date_op\n$mess_suivi_act_cor\n$mess_suivi_fic_cor$nom_fichier\n$mess_ag_cordial\n\n";
        $from = "$adr_mail_tut";
        $reply = "$adr_mail_tut";
        $sendto = "$adr_mail_app";
        $suj= "$mess_suivi_suj_env $prenom_user $nom_user";
        $subject = StripSlashes($suj);
        $msg = StripSlashes($message);
        $origine=$nom_user."  ".$typ_user;
        $nom ="corrige_".$nom_fichier;
        if ($nom_fichier == "")
        {
          $nom = "";
          $userfile = "none";
        }
        if (isset($sendto) && $sendto != "" && $emailing =='on' && isset($_SESSION['IsOff']) && $_SESSION['IsOff'] == 0)
           $envoi=mail_attachement($sendto , $subject , $msg , $dest_file , $reply, $nom, $from);
        $message_base = "$mess_suivi_env_act_tut <B>$prenom_tut $nom_tut</B><BR>$mess_gen_date  $date_op<BR>$mess_suivi_act_cor<BR>$mess_suivi_fic_cor$nom_fichier<BR>$mess_ag_cordial";
        $max_numero = Donne_ID ($connect,"select max(mess_cdn) from messagerie");
        $requete = mysql_query("INSERT INTO messagerie (mess_cdn,envoyeur,origine,contenu,mess_fichier_lb,date,sujet,id_user) VALUES ".
                              "($max_numero,'$id_user','$mess_suivi_titre',\"$message_base\",\"$dest_file\",'$date_messagerie','$subject',$utilisateur)");
    }
    elseif ($devoir == "OUI" && $notation == "OUI" && $acquit == "FORMATEUR_REFERENT" && $objet == "telecharger" && $userfile_name == "")
    {
       echo "<script language=\"JavaScript\">";
       echo "document.location.replace(\"javascript:history.go(-1);\")";
       echo "</script>";
    }
   }// fin if envoyer == 1
    if ($devoir == "NON" && $notation == "NON" && $suivi_etat == "PRESENTIEL")
    {
       $commentaire = $mess_terme_presentiel_form;
       $new_fiche = Donne_ID ($connect,"select max(fiche_cdn) from fiche_suivi");
       $req_fiche = mysql_query("INSERT INTO fiche_suivi (fiche_cdn,fiche_utilisateur_no,fiche_auteur_no,fiche_qualite_lb,fiche_date_dt,fiche_heure_dt,".
                                "fiche_commentaire_cmt,fiche_grp_no,fiche_parc_no,fiche_seq_no,fiche_act_no,fiche_typaction_lb) ".
                                "VALUES($new_fiche,$num_app,$id_user,'$qualite','$date_fiche','$heure_fiche',\"$commentaire\",$numero_groupe,$id_parc,$id_seq,$id_act,\"$action_fiche\")");
       $change_etat = mysql_query("update suivi1$Ext set suivi_etat_lb='TERMINE',suivi_date_debut_dt='$date_op',suivi_date_fin_dt='$date_op'
                                  where suivi_act_no=$id_act and suivi_utilisateur_no=$num_app");
    }
    elseif ($devoir == "NON" && $notation == "NON"  && $suivi_etat != "PRESENTIEL")
    {// && $pass_multi == "OUI" && $acquit == "APPRENANT")
      $change_etat = mysql_query("update suivi1$Ext set suivi_etat_lb='TERMINE' where
                                  suivi_act_no=$id_act and
                                  suivi_utilisateur_no=$utilisateur");
      $commentaire = $mess_terme_fiche;
      $new_fiche = Donne_ID ($connect,"select max(fiche_cdn) from fiche_suivi");
      $req_fiche = mysql_query("INSERT INTO fiche_suivi (fiche_cdn,fiche_utilisateur_no,fiche_auteur_no,fiche_qualite_lb,fiche_date_dt,fiche_heure_dt,".
                               "fiche_commentaire_cmt,fiche_grp_no,fiche_parc_no,fiche_seq_no,fiche_act_no,fiche_typaction_lb) VALUES ".
                               "($new_fiche,$num_app,$id_user,'$qualite','$date_fiche','$heure_fiche',\"$commentaire\",$numero_groupe,$id_parc,$id_seq,$id_act,\"$action_fiche\")");
      $change_etat = mysql_query("update suivi1$Ext set suivi_note_nb1='$note' where
                                  suivi_act_no=$id_act and
                                  suivi_utilisateur_no=$num_app");
   }
  } //fin if $utilisateur
}// if $validation == 1
$y_aller = actualiser($num_app,$id_act,$id_seq,$id_parc,$fini_note,$note,$fin_seq,$lg,$numero_groupe,$qualite,$id_user,$date_fiche,$heure_fiche,$action_fiche);
if ($prov == "vm")
   $script="details_parc.php?numero_groupe=$numero_groupe&id_act=$id_act&seq=$id_seq&id_parc=$id_parc&visible=$visible&accord=$accord&prq=$prq&depasse=$depasse&";
else
   $script= "gest_parc.php?numero_groupe=$numero_groupe&";
$lien = $script."a_faire=1&hgrp=$hgrp&utilisateur=$utilisateur&parc_ouvert=$id_parc&seq_ouverte=$id_seq&vp=1";
$lien = urlencode($lien);
echo "<script language=\"JavaScript\">";
 echo "document.location.replace(\"trace.php?link=$lien\")";
echo "</script>";
//include ("click_droit.txt");
exit();
// FONCTIONS
function actualiser($num_app,$id_act,$id_seq,$id_parc,$fini_note,$note,
                    $fin_seq,$lg,$numero_groupe,$qualite,$id_user,
                    $date_fiche,$heure_fiche,$action_fiche)
{
   GLOBAL $connect,$Ext,$date_messagerie;
  require ("lang$lg.inc.php");
  $notation = GetDataField ($connect,"select act_notation_on from activite where act_cdn = $id_act","act_notation_on");
  $devoir = GetDataField ($connect,"select act_devoirarendre_on from activite where act_cdn = $id_act","act_devoirarendre_on");
  $suivi_etat= GetDataField ($connect,"select suivi_etat_lb from suivi1$Ext where
                                       suivi_act_no= $id_act and
                                       suivi_utilisateur_no = $num_app","suivi_etat_lb");
  if ($fini_note == 1 && $suivi_etat !="PRESENTIEL")
  {
    $commentaire = "$mess_note_fiche".$note;
    $new_fiche = Donne_ID ($connect,"select max(fiche_cdn) from fiche_suivi");
    $req_fiche = mysql_query("INSERT INTO fiche_suivi (fiche_cdn,fiche_utilisateur_no,fiche_auteur_no,fiche_qualite_lb,fiche_date_dt,".
                            "fiche_heure_dt,fiche_commentaire_cmt,fiche_grp_no,fiche_parc_no,fiche_seq_no,fiche_act_no,fiche_typaction_lb) VALUES ".
                            "($new_fiche,$num_app,$id_user,'$qualite','$date_fiche','$heure_fiche',\"$commentaire\",$numero_groupe,$id_parc,$id_seq,$id_act,\"$action_fiche\")");
    $change_etat = mysql_query("update suivi1$Ext set suivi_note_nb1='$note' where
                                suivi_act_no=$id_act and
                                suivi_utilisateur_no=$num_app");
    if ($notation == "OUI" && $devoir == "NON")
      $change_etat = mysql_query("update suivi1$Ext set suivi_etat_lb='TERMINE' where
                                  suivi_act_no=$id_act and
                                  suivi_utilisateur_no=$num_app");
  }
  elseif ($fini_note == 1 && $suivi_etat == "PRESENTIEL")
  {
    $commentaire = "$mess_note_fiche".$note;
    $new_fiche = Donne_ID ($connect,"select max(fiche_cdn) from fiche_suivi");
    $req_fiche = mysql_query("INSERT INTO fiche_suivi (fiche_cdn,fiche_utilisateur_no,fiche_auteur_no,fiche_qualite_lb,fiche_date_dt,".
                             "fiche_heure_dt,fiche_commentaire_cmt,fiche_grp_no,fiche_parc_no,fiche_seq_no,fiche_act_no,fiche_typaction_lb) VALUES ".
                             "($new_fiche,$num_app,$id_user,'$qualite','$date_fiche','$heure_fiche',\"$commentaire\",$numero_groupe,$id_parc,$id_seq,$id_act,\"$action_fiche\")");
    $change_etat = mysql_query("update suivi1$Ext set suivi_note_nb1='$note',suivi_etat_lb='TERMINE' where
                                suivi_act_no=$id_act and
                                suivi_utilisateur_no=$num_app");
  }
  if ($fini_note == 1)
  {
        $adr_mail_app = GetDataField ($connect, "select util_email_lb from utilisateur where util_cdn=$num_app","util_email_lb");
        $titre_grp = GetDataField ($connect,"select grp_nom_lb from groupe where grp_cdn = $numero_groupe","grp_nom_lb");
        $titre_parc = GetDataField ($connect,"select parcours_nom_lb from parcours where parcours_cdn = $id_parc","parcours_nom_lb");
        $titre_seq = GetDataField ($connect,"select seq_titre_lb from sequence where seq_cdn = $id_seq","seq_titre_lb");
        $titre_act = GetDataField ($connect,"select act_nom_lb from activite where act_cdn = $id_act","act_nom_lb");
        $message = "Vous avez obtenu cette évaluation : <b>$note</b><br />à l'activité :<b>".str_replace('"',"'",$titre_act).
        "</b> <br />de la séquence : <b>".str_replace('"',"'",$titre_seq)."</b> <br />du module <b>: ".str_replace('"',"'",$titre_act).
        "</b> <br />dans la formation : <b>".str_replace('"',"'",$titre_grp)."<br />$mess_ag_cordial";
        $from = $_SESSION['email_user'];
        $reply = $_SESSION['email_user'];
        $sendto = "$adr_mail_app";
        $suj= "Notation par ".$_SESSION['prename_user']." ". $_SESSION['name_user'];
        $subject = StripSlashes($suj);
        $msg = StripSlashes($message);
        $origine=$nom_user."  ".$typ_user;
        $nom ='';
        $dest_file = 'none';
        if (isset($sendto) && $sendto != "" && isset($_SESSION['IsOff']) && $_SESSION['IsOff'] == 0)
           $envoi=mail_attachement($sendto , $subject , $msg , $dest_file , $reply, $nom, $from);
        $message_base = str_replace('\n','<br />',$message);
        $max_numero = Donne_ID ($connect,"select max(mess_cdn) from messagerie");
        $requete = mysql_query("INSERT INTO messagerie (mess_cdn,envoyeur,origine,contenu,mess_fichier_lb,date,sujet,id_user) VALUES ".
                               "($max_numero,'$id_user','$mess_suivi_titre',\"$message_base\",'','$date_messagerie','$subject',$num_app)");
  }

// controle fin de séquence
  $nb_act_query = mysql_query ("select act_cdn from activite,suivi1$Ext where
                                activite.act_seq_no = $id_seq and
                                suivi_act_no = activite.act_cdn and
                                suivi_utilisateur_no = $num_app");
  $nb_act = mysql_num_rows ($nb_act_query);
  $na = 0;
  $calcul1 = 0;
  $calcul2 = 0;
  $calcul3 = 0;
  while ($na < $nb_act)
  {
    $num_act = mysql_result ($nb_act_query,$na,"act_cdn");
    $suivi_etat= GetDataField ($connect,"select suivi_etat_lb from suivi1$Ext where
                                         suivi_act_no= $num_act and
                                         suivi_utilisateur_no=$num_app","suivi_etat_lb");
    if ($suivi_etat == "TERMINE")
      $calcul1++;
    if ($suivi_etat == "ATTENTE")
      $calcul2++;
    if ($suivi_etat == "EN COURS")
      $calcul3++;
    $na++;
  }
  if ($fin_seq == 1 && $calcul1 == $nb_act)
  {
    $suivi_etat= GetDataField ($connect,"select suivi_etat_lb from suivi1$Ext where
                                         suivi_act_no= $id_act and
                                         suivi_utilisateur_no=$num_app","suivi_etat_lb");
    if ($suivi_etat == "TERMINE")
      $change_etat_seq = mysql_query("update suivi2$Ext set suiv2_etat_lb='TERMINE' where
                                      suiv2_seq_no =$id_seq and
                                      suiv2_utilisateur_no=$num_app");
    $fin_seq=0;
  }
  if ($calcul1 == $nb_act)
    $change_etat_seq = mysql_query("update suivi2$Ext set suiv2_etat_lb='TERMINE' where
                                    suiv2_seq_no =$id_seq and
                                    suiv2_utilisateur_no=$num_app");
  elseif ($calcul1 == ($nb_act-1) && $nb_act>1 && $calcul2 == 1 && $calcul3 == 0)
    $change_etat_seq = mysql_query("update suivi2$Ext set suiv2_etat_lb='ATTENTE' where
                                    suiv2_seq_no =$id_seq and
                                    suiv2_utilisateur_no=$num_app");
  elseif ($calcul1 < $nb_act && $nb_act>1 && $calcul3 > 0)
    $change_etat_seq = mysql_query("update suivi2$Ext set suiv2_etat_lb='EN COURS' where
                                    suiv2_seq_no =$id_seq and
                                    suiv2_utilisateur_no=$num_app");
  elseif ($calcul1 == 0 && $calcul2 == 0 && $calcul3 == 0)
    $change_etat_seq = mysql_query("update suivi2$Ext set suiv2_etat_lb='A FAIRE' where
                                    suiv2_seq_no =$id_seq and
                                    suiv2_utilisateur_no=$num_app");
  // Controle fin de parcours
  $nb_seq_query = mysql_query ("select distinct seq_cdn from sequence,prescription$Ext where
                                presc_parc_no = $id_parc and
                                presc_seq_no = sequence.seq_cdn and
                                presc_utilisateur_no = $num_app");
  $nb_seq = mysql_num_rows ($nb_seq_query);
  $na = 0;
  $calcul = 0;
  $calcul_a = 0;
  $calcul_b = 0;
  while ($na < $nb_seq)
  {
    $num_seq = mysql_result ($nb_seq_query,$na,"seq_cdn");
    $suivi_etat= GetDataField ($connect,"select suiv2_etat_lb from suivi2$Ext where
                                         suiv2_seq_no= $num_seq and
                                         suiv2_utilisateur_no=$num_app","suiv2_etat_lb");
    if ($suivi_etat == "TERMINE")
      $calcul++;
    if ($suivi_etat == "ATTENTE")
      $calcul_a++;
    if ($suivi_etat == "EN COURS")
      $calcul_b++;
    $na++;
  }
  if ($calcul == $nb_seq)
    $change_etat_parc = mysql_query("update suivi3$Ext set suiv3_etat_lb='TERMINE' where
                                     suiv3_parc_no =$id_parc and
                                     suiv3_utilisateur_no=$num_app");
  elseif ($calcul == ($nb_seq-1) && $nb_seq > 1 && $calcul_a == 1 && $calcul_b == 0)
    $change_etat_parc = mysql_query("update suivi3$Ext set suiv3_etat_lb='ATTENTE' where
                                     suiv3_parc_no =$id_parc and
                                     suiv3_utilisateur_no=$num_app");
  elseif ($calcul < $nb_seq && $nb_seq > 1 && $calcul_b > 0)
    $change_etat_parc = mysql_query("update suivi3$Ext set suiv3_etat_lb='EN COURS' where
                                     suiv3_parc_no =$id_parc and
                                     suiv3_utilisateur_no=$num_app");
  elseif ($calcul == 0 && $calcul_a == 0 && $calcul_b == 0)
    $change_etat_parc = mysql_query("update suivi3$Ext set suiv3_etat_lb='A FAIRE' where
                                     suiv3_parc_no =$id_parc and
                                     suiv3_utilisateur_no=$num_app");
}// fin fonction actualiser
function getextension($fichier)
{
  $bouts = explode(".", $fichier);
  return array(array_pop($bouts), implode(".", $bouts));
}
?>