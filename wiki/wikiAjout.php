<?php
session_start();
include ("../include/UrlParam2PhpVar.inc.php");
if (!isset($_SESSION['id_user']) || $_SESSION['id_user'] == "")
{
  exit();
}
require "../admin.inc.php";
require '../fonction.inc.php';
require "../lang$lg.inc.php";
require "../fonction_html.inc.php";
require "../langues/formation.inc.php";
require "../langues/module.inc.php";
require  "wikiClass.php";
dbConnect();
setlocale(LC_TIME,'fr_FR');
$nom_user = $_SESSION["name_user"];
$prenom_user = $_SESSION["prename_user"];
$email_user = $_SESSION["email_user"];
$leJour = date("Y/m/d H:i:s" ,time());
$date_cour = date ("Y-m-d");
if (!empty($_GET['numApp'])) $numApp = $_GET['numApp'];
if (!empty($_GET['id_seq'])) $id_seq = $_GET['id_seq'];
if (!empty($_GET['id_parc'])) $id_parc = $_GET['id_parc'];
if (!empty($_GET['numero_groupe'])) $numero_groupe = $_GET['numero_groupe'];
if (!empty($_GET['numeroApp'])) $numeroApp = $_GET['numeroApp'];
if (!empty($_GET['id_clan'])) $id_clan = $_GET['id_clan'];
$content = '';
include ("../style.inc.php");
if (!empty($_GET['ajt']) && $_GET['ajt'] == 1)
{
   $id_wk = Donne_ID ($connect,"select max(wiki_cdn) from wiki");
   $reqWk = mysql_query("insert into wiki values('$id_wk','".$_SESSION['id_user']."',\"----------\",'".$_GET['id_seq']."','0','0',\"$leJour\")");

}
if (!empty($_GET['supp']) && $_GET['supp'] == 1)
{
    $supprimer = mysql_query("delete from wiki where wiki_cdn=".$_GET['IdWiki']);
}
if (!empty($mess_notif))
   echo notifier($mess_notif);
if ($_GET['id_seq'] > 0 && $_GET['id_seq'] < 10000)
{
   $reqSeq =mysql_query("select * from sequence where seq_cdn='".$_GET['id_seq']."'");
   $oSeq = mysql_fetch_object($reqSeq);
   if ($oSeq->seq_auteur_no == $_SESSION['id_user'])
   {
      $lien= "wikiAjout.php?id_seq=".$_GET['id_seq']."&ajt=1";
      $content .= '<div id="ajtWk" class="sequence" style="clear:both;">'.
                  $bouton_gauche.'<a href="'.$lien.'" title="Ajouter un travail en commun (WikiDoc) pour cette séquence">'.
                  'Ajouter un travail en commun -WikiDoc-</A>'.$bouton_droite.'</div>';
   }
}
elseif($_GET['id_seq'] == 0)
{
   $lien= "wikiAjout.php?id_seq=".$_GET['id_seq']."&ajt=1";
   $content .= '<div id="ajtWk" class="sequence" style="clear:both;">'.
               '<a href="'.$lien.'" title="Ajouter un nouveau thème de travail en commun">'.
               'Travail en commun -WikiDoc-</A></div>';
}
elseif($_GET['id_seq'] > 10000)
{
   if (!empty($_GET['AffectGrp']) && $_GET['AffectGrp']== 1)
   {
       $grp = $_GET['id_seq']- 10000;
       $id_clan = Donne_ID ($connect,"select max(wkapp_clan_nb) from wikiapp");
       $req_grp = mysql_query("select * from utilisateur_groupe where utilgr_groupe_no=$grp order by utilgr_utilisateur_no");
       if (mysql_num_rows($req_grp)> 0)
       {
          while ($itemUser = mysql_fetch_object($req_grp))
          {
               $id_wk = Donne_ID ($connect,"select max(wkapp_cdn) from wikiapp");
               $req = mysql_query("insert into wikiapp values ('$id_wk','".$_GET['IdWiki']."','".$itemUser->utilgr_utilisateur_no."','".
                      $_GET['id_seq']."','10000','".$grp."','".$id_clan."',\"".
                      $date_cour."\",\"".$date_cour."\")");
          }
       }
   }
   $lien= "wikiAjout.php?id_seq=".$_GET['id_seq']."&ajt=1";
   $content .= '<div id="ajtWk" class="sequence" style="clear:both;">'.
               '<a href="'.$lien.'" title="Ajouter un nouveau thème de travail en commun pour tous les apprenants de cette formation">'.
               'Travail en commun à tous les apprenants de la formation -WikiDoc-</A></div>';
}
$reqWk = mysql_query("select * from wiki where wiki_seq_no=".$_GET['id_seq']." order by wiki_ordre_no");
if (mysql_num_rows($reqWk) > 0)
{
   $content.= '<div id="ListeWk" style="clear:both;float:left;background-color: #eee;border:1px solid #ccc;'.
           'margin:5px 2px 0 2px;padding:4px;max-height:500px;overflow-y:auto;width:670px;">';
   $i=0;
   while ($oWiki = mysql_fetch_object($reqWk))
   {
       $content.= '<div style="clear:both;float:left;width:640px;">';
       $ch_dt=explode('-',DateTiretInv(substr($oWiki->wiki_create_dt,0,10),'-','-'));
       $dateWk=strftime("%A %e %B %Y", mktime(0, 0, 0, $ch_dt[1],$ch_dt[0],$ch_dt[2]));
       $heureWk = substr($oWiki->wiki_create_dt,11);
       if (!strstr($oWiki->wiki_consigne_cmt,'-------') && $id_seq > 0)
       {
            if ($oWiki->wiki_ordre_on == 0)
               $afficheOrdre = 'N°';
            elseif($oWiki->wiki_ordre_on == 1)
               $afficheOrdre = $oWiki->wiki_ordre_no;
       }
       elseif(strstr($oWiki->wiki_consigne_cmt,'-------') && $id_seq > 0)
            $afficheOrdre = '  ';
       if ($id_seq > 0)
       {
           $Ajtitre = 'title="Cliquez sur l\'icone pour attribuer un numéro d\'ordre à ce thème."';
           $suiteDiv = (!strstr($oWiki->wiki_consigne_cmt,'-------')) ? "cursor:pointer;" : "";
           $content .= '<div data-zcontenteditable data-id="'.$oWiki->wiki_cdn.'" '.
                        'data-wiki = "&id_seq='.$id_seq.'&ordreCreate=1&table=wiki&cdn=wiki_cdn" '.
                        'style="clear:both;float:left;font-weight:bold;'.$suiteDiv.
                       'background-color: #F1F5F5;border:1px solid #bbb;margin:5px 2px 0 2px;padding:4px;width:35px;font-size:11px;" ';
           if ($oWiki->wiki_ordre_on == 0 && !strstr($oWiki->wiki_consigne_cmt,'-------'))
               $content .= $Ajtitre;
           $content .= '>'.$afficheOrdre.'</div>';
           $ajoutDiv = '';
       }
       else
           $ajoutDiv = 'clear:both;';
       if ($oWiki->wiki_auteur_no == $_SESSION['id_user'] || $_SESSION['typ_user'] == 'ADMINISTRATEUR')
       {
          // zetoutou
            $titreItem = "<strong>Cliquez sur l'image pour modifier/sauver la consigne</strong>";
            $content .= '<div data-zcontenteditable data-id="'.$oWiki->wiki_cdn.'" '.
                        'data-wiki = "&modifie=1&table=wiki&cdn=wiki_cdn&champ=wiki_consigne_cmt" '.
                        'style="'.$ajoutDiv.'float:left;border:1px dotted #bbb;width:490px;cursor:pointer;'.
                        'background-color:#F1F5F5;margin:4px 10px 0 2px;padding:4px;font-family:arial,verdana,tahoma;font-size:12px;" '.
                        'title ="Créé le '.$dateWk.' à '.$heureWk.'  par '.NomUser($oWiki->wiki_auteur_no).'. '.
                        $titreItem.'" >';
                        
            $content .= $oWiki->wiki_consigne_cmt;
            $content.= '</div>';
       }
       else
       {
           if (!strstr($oWiki->wiki_consigne_cmt,'-------'))
           {
               $content .= '<div id="WkNo'.$i.'" style="clear:both;float:left;border:1px dotted #bbb;'.
                           'max-width:470px;cursor:default;background-color:#F1F5F5;margin:4px 10px 0 2px;'.
                           'padding:4px;font-family:arial,verdana,tahoma;font-size:12px;" '.
                           'title ="Créé le '.$dateWk.' à '.$heureWk.'  par '.NomUser($oWiki->wiki_auteur_no).'. '.$titreItem.'">';
               $content .= $oWiki->wiki_consigne_cmt;
               $content .= '</div>';
           }
       }
      $nbReqAffect = mysql_num_rows(mysql_query("select * from wikiapp where wkapp_wiki_no=".$oWiki->wiki_cdn));
      if ($nbReqAffect == 0)
      {
         $content .= '<div style="float:left;margin:10px 4px 0 5px;cursor:pointer;width:20px;" '.
                     'title="Supprimer ce thème. Il n\'a encore été affecté à personne." '.
                     'onClick=document.location.replace("wikiAjout.php?supp=1&id_seq='.$id_seq.'&IdWiki='.$oWiki->wiki_cdn.'")>'.
                     '<img src="../images/supp.png" border="0"></div>';
         if ($oWiki->wiki_seq_no > 10000)
         {
             $content .= '<div style="float:left;margin:10px 4px 0 5px;cursor:pointer;width:20px;" '.
                     'title="Affecter ce thème à cette formation (Tous les apprenants de cette formation pourront y accéder)." '.
                     'onClick=document.location.replace("wikiAjout.php?AffectGrp=1&id_seq='.$id_seq.'&IdWiki='.$oWiki->wiki_cdn.'")>'.
                     '<img src="../images/ecran-annonce/icoGgo.gif" border="0"></div>';
         }
      }
      $content .= '</div>';
      $i++;
   }
       $content .= "
            <script type=\"text/javascript\">
              jQuery(function(){\$('[data-zcontenteditable]').zcontenteditable({callback:saveEditable,btnEditSaveAction:'".
                                "<span data-action=\"edit\">&nbsp;</span>' });});
              function saveEditable(zcontenteditableResponse){
              
             //console.log(zcontenteditableResponse);
             
             var dataAttributs=zcontenteditableResponse.dataAttributs;
             var params=zcontenteditableResponse;
                    \$.ajax({
                        type: 'GET',
                        url: 'wikilib.php',
                        data: 'new='+params.content+dataAttributs.wiki+'&id='+dataAttributs.id,
                        beforeSend:function(){
                        //console.log('new='+params.content+dataAttributs.wiki+'&id='+dataAttributs.id);
                            \$('#affiche').addClass('Status');
                            \$('#affiche').append('Opération en cours....');
                        },
                        success: function(){
                            \$('#mien').empty();
                            \$('#mien').html('La consigne a été modifiée.');
                            \$('#affiche').empty();
                            \$('#mien').show();
                            setTimeout(function() {\$('#mien').empty();},4000);
                        }
                    });
             }
            </script>";

}
$content.= '</div>';
echo $content;
echo '<div id="affiche"></div>';
echo '<div id="mien" class="cms" title="Cliquer pour fermer cette alerte" onClick="javascript:$(this).empty();";></div>';
//echo "<pre>";print_r($oWiki);echo "</pre>";
?>
