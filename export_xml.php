<?php
if (!isset($_SESSION)) session_start();
class exp_xml
{
   function entete_manifest($dte,$auteur,$typ_user,$adresse_http)
   {
        $data = "<?xml version=\"1.0\" encoding=\"UTF-8\" standalone=\"yes\"?>\n".
              "<!--\n".
              "Genere par Formagri Ceres V 2.6.0 [Support SCORM v1.2 (c)]\n".
              "Le ".utf8_encode($dte)." sur la plate-forme $adresse_http\npar ".utf8_encode($auteur)." : $typ_user\n".
              "-->\n".
              "<manifest identifier=\"Manifest-Module-Formagri-Ceres\" version=\"1.1\"\n".
              "xmlns=\"http://www.imsglobal.org/xsd/imscp_v1p1\"
               xmlns:imsmd=\"http://www.imsglobal.org/xsd/imsmd_v1p2\"
               xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\"
               xsi:schemaLocation=\"http://www.imsglobal.org/xsd/imscp_v1p1 imscp_v1p1.xsd
               http://www.imsglobal.org/xsd/imsmd_v1p2 imsmd_v1p2p2.xsd\">\n";
         return $data;
   }
   function create_html_trous($titre)
   {
       $title = str_replace(dirname($_SESSION['QcmFile']).'/','',$titre);
       $data = "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" ".
               "\"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\" >
               <html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"fr\" lang=\"fr\">
               <head>
               <meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\" />
               <title>".str_replace(".xml","",utf8_encode($title))."</title>
               </head>
               <script type=\"text/javascript\" language=\"JavaScript\" src=\"cmi.js\"></script>
               <body bgcolor=\"#ffffff\" onload=\"SCOInitialize()\" onunload=\"SCOFinish()\" onbeforeunload=\"SCOFinish()\">
               <object classid=\"clsid:d27cdb6e-ae6d-11cf-96b8-444553540000\" ".
               "codebase=\"http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=8,0,0,0\" name=\"qcm\" width=\"635\" height=\"410\" align=\"middle\" id=\"qcm\">
               <param name=\"allowScriptAccess\" value=\"sameDomain\" />
               <param name=\"movie\" value=\"qcm.swf?urlEvaluation=$title\" /><param name=\"loop\" value=\"false\" />
               <param name=\"menu\" value=\"false\" />
               <param name=\"quality\" value=\"high\" />
               <param name=\"scale\" value=\"noscale\" />
               <param name=\"bgcolor\" value=\"#ffffff\" />
               <embed src=\"qcm.swf?urlEvaluation=$title\" width=\"635\" height=\"410\" loop=\"false\" align=\"middle\" menu=\"false\" quality=\"high\" scale=\"noscale\" bgcolor=\"#ffffff\" allowScriptAccess=\"sameDomain\" type=\"application/x-shockwave-flash\" pluginspage=\"http://www.macromedia.com/go/getflashplayer\" name=\"qcm\" />
               </object>";
       return $data;
   }
   function entete_qcm($dte,$auteur,$typ_user,$adresse_http)
   {
        $data = "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>\n".
              "<!-- Genere par Formagri V 2.6.2 [Support SCORM v1.2 (c)]\n".
              "Le ".clean_text($dte)." sur la plate-forme ".$adresse_http."
              par ".clean_text($auteur)." : ".$typ_user."-->\n";
        return $data;
   }
   function tagDebQuestion_qcm($titre,$moyenne,$numero)
   {
        $data = "<evaluation id=\"".$numero."\" label=\"".stripslashes(utf8_encode($titre))."\" masteryScore=\"".$moyenne."\" ".
                "positiveFeedback=\"".utf8_encode("Bonne réponse")."\" negativeFeedback=\"".utf8_encode("Mauvaise réponse")."\">\n";
        return $data;
   }
   function tagFinQuestion_qcm()
   {
        $data = '</evaluation>';
        return $data;
   }
   function controlSlash($data)
   {
        $data = str_replace('/ ','/',str_replace(' /','/',$data));
        $data = str_replace('/',' / ',$data);
        $data = str_replace('  /',' /',$data);
        $data = str_replace('   /',' /',$data);
        $data = str_replace('    /',' /',$data);
        $data = str_replace('/  ','/ ',$data);
        $data = str_replace('/   ','/ ',$data);
        $data = str_replace('/    ','/ ',$data);
        return $data;
   }
   function controlTQ($data)
   {
        $data =str_replace('.......','...',$data);
        $data =str_replace('......','...',$data);
        $data =str_replace('.....','...',$data);
        $data = str_replace('....','...',$data);
        if (!strstr($data,'etc..'))
            $data = str_replace('..','...',$data);
        if (!strstr($data,' ...'))
            $data = str_replace('...',' ...',$data);
        if (!strstr($data,'... '))
            $data = str_replace('...','... ',$data);
        return $data;
   }
   function entete_act_manifest($numero,$id_act,$ress)
   {
        GLOBAL $_SESSION;
        $typer = ($ress == "index_blanc.htm" || $ress == "index_presentiel.htm") ? "SCORM" : "ASSET";
        if (substr($ress,0,10) == "ressources")
           $ress2 = $_SESSION['adresse_http'].'/'.$ress;
        else
           $ress2 = $ress;
        $data = "<resource identifier=\"R_S01$numero\" type=\"webcontent\"".
                "\n     adlcp:scormtype=\"asset\" href=\"".NewHtmlentities($ress2)."\">\n".
                "<metadata>\n".
                "<schema>ADL SCORM</schema>\n".
                "<schemaversion>1.2</schemaversion>\n".
                "<adlcp:location>R_S01$numero.xml</adlcp:location>\n".
                "</metadata>\n";

              return $data;
   }
   function suite_act_manifest($ress)
   {
       GLOBAL $_SESSION;
       if (substr($ress,0,10) == "ressources")
          $ress2 = $_SESSION['adresse_http'].'/'.$ress;
        else
           $ress2 = $ress;
        $data = "<file href=\"".NewHtmlentities($ress2)."\" />\n";
           return $data;
   }
   function fin_act_manifest($ress)
   {
        $data ="</resource>\n";
           return $data;
   }

   function header_act_location($ress)
   {
      $data = "<?xml version=\"1.0\" encoding=\"UTF-8\" standalone=\"yes\"?>\n".
              "<lom xmlns=\"http://www.imsglobal.org/xsd/imsmd_rootv1p2p1\" ".
              "xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" ".
              "xsi:schemaLocation=\"http://www.imsglobal.org/xsd/imsmd_rootv1p2p1 imsmd_rootv1p2p1.xsd\">\n";
              return $data;
   }
   function modifie_time_xml($duree)
   {
        $heure = floor($duree/60);
        if ($heure > 0){
           $reste = $duree%60;
           if (strlen($heure) == 3)
              $horaire = "0".$heure;
           elseif (strlen($heure) == 2)
              $horaire = "00".$heure;
           elseif (strlen($heure) == 1)
              $horaire = "000".$heure;
           if ($reste == 0)
              $duree_ret = "$horaire:00:00.0";
           else
           {
              $lereste = (strlen($reste) == 1) ? "0$reste" : $reste;
              $duree_ret = "$horaire:$reste:00.0";
           }
        }
        else
        {
           if (strlen($duree) == 1)
              $duree = "0$duree";
           $duree_ret = "0000:$duree:00.0";
        }
       return $duree_ret;
   }
   function metaTrous($mailer,$titre,$mots)
   {
        $data = "  <metadata>
    <schema>IMSContent</schema>
    <schemaversion>1.2.2</schemaversion>
    <imsmd:lom>
      <imsmd:general>
        <imsmd:title>
          <imsmd:langstring xml:lang=\"fr\">".utf8_encode($titre)."</imsmd:langstring>
        </imsmd:title>
        <imsmd:language>fr</imsmd:language>
        <imsmd:description>
          <imsmd:langstring xml:lang=\"fr\">Ensemble de QCM</imsmd:langstring>
        </imsmd:description>
        <imsmd:keyword>
          <imsmd:langstring xml:lang=\"fr\">".utf8_encode($mots)."</imsmd:langstring>
        </imsmd:keyword>
      </imsmd:general>
      <imsmd:lifecycle>
        <imsmd:status>
          <imsmd:source>
            <imsmd:langstring xml:lang=\"fr\">LOMv1.0</imsmd:langstring>
          </imsmd:source>
          <imsmd:value>
            <imsmd:langstring xml:lang=\"x-none\">Final</imsmd:langstring>
          </imsmd:value>
        </imsmd:status>
        <imsmd:contribute>
          <imsmd:role>
            <imsmd:source>
              <imsmd:langstring xml:lang=\"fr\">LOMv1.0</imsmd:langstring>
            </imsmd:source>
            <imsmd:value>
              <imsmd:langstring xml:lang=\"x-none\">Encadrement pedagogique</imsmd:langstring>
            </imsmd:value>
          </imsmd:role>
          <imsmd:centity>
            <imsmd:vcard>".utf8_encode($mailer)."</imsmd:vcard>
          </imsmd:centity>
        </imsmd:contribute>
      </imsmd:lifecycle>
      <imsmd:technical>
        <imsmd:format>application/x-shockwave-flash</imsmd:format>
        <imsmd:requirement>
          <imsmd:type>
            <imsmd:source>
              <imsmd:langstring xml:lang=\"fr\">LOMv1.0</imsmd:langstring>
            </imsmd:source>
            <imsmd:value>
              <imsmd:langstring xml:lang=\"x-none\">Navigateur</imsmd:langstring>
            </imsmd:value>
          </imsmd:type>
        </imsmd:requirement>
      </imsmd:technical>
      <imsmd:educational>
        <imsmd:interactivitytype>
          <imsmd:source>
            <imsmd:langstring xml:lang=\"fr\">LOMv1.0</imsmd:langstring>
          </imsmd:source>
          <imsmd:value>
            <imsmd:langstring xml:lang=\"x-none\">Active</imsmd:langstring>
          </imsmd:value>
        </imsmd:interactivitytype>
        <imsmd:learningresourcetype>
          <imsmd:source>
            <imsmd:langstring xml:lang=\"fr\">LOMv1.0</imsmd:langstring>
          </imsmd:source>
          <imsmd:value>
            <imsmd:langstring xml:lang=\"x-none\">Exercise</imsmd:langstring>
          </imsmd:value>
        </imsmd:learningresourcetype>
        <imsmd:interactivitylevel>
          <imsmd:source>
            <imsmd:langstring xml:lang=\"fr\">LOMv1.0</imsmd:langstring>
          </imsmd:source>
          <imsmd:value>
            <imsmd:langstring xml:lang=\"x-none\">".utf8_encode($adresse)."</imsmd:langstring>
          </imsmd:value>
        </imsmd:interactivitylevel>
        <imsmd:context>
          <imsmd:source>
            <imsmd:langstring xml:lang=\"fr\">LOMv1.0</imsmd:langstring>
          </imsmd:source>
          <imsmd:value>
            <imsmd:langstring xml:lang=\"x-none\">Enseignement</imsmd:langstring>
          </imsmd:value>
        </imsmd:context>
        <imsmd:language>fr</imsmd:language>
      </imsmd:educational>
    </imsmd:lom>
  </metadata>\n";
        return $data;
   }

}
?>
