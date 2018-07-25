<?php
if (!isset($_SESSION)) session_start();
class expQti20
{
    public static function enteteXmlQtiMC($dte,$auteur,$typ_user,$adresse_http)
    {
        $data = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n".
              "<manifest xmlns=\"http://www.imsglobal.org/xsd/imscp_v1p1\"\n".
              "          xmlns:imsmd=\"http://www.imsglobal.org/xsd/imsmd_v1p2\"\n".
              "              xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\"\n".
              "              xmlns:imsqti=\"http://www.imsglobal.org/xsd/imsqti_v2p0\"\n".
              "                    identifier=\"MANIFEST-85D76736-6D19-9DC0-7C0B-57C31A9FD390\"\n".
              "                    xsi:schemaLocation=\"http://www.imsglobal.org/xsd/imscp_v1p1\n".
              "                    imscp_v1p1.xsd http://www.imsglobal.org/xsd/imsmd_v1p2\n".
              "                    imsmd_v1p2p2.xsd http://www.imsglobal.org/xsd/imsqti_v2p0 imsqti_v2p0.xsd\">";
        return $data;
    }
    public static function EnteteManifest($compteur,$author)
    {
        $data = "  <metadata>\n".
                "    <schema>IMS Content</schema>\n".
                "    <schemaversion>1.1</schemaversion>\n".
                "    <imsmd:lom>\n".
                "      <imsmd:general>\n".
                "         <imsmd:title>\n".
                "            <imsmd:langstring xml:lang=\"fr\">".utf8_encode(modif_az_qw(strip_tags(html_entity_decode($_SESSION['TabQcm'][0]->titre_qcm,ENT_QUOTES,'ISO-8859-1')))).
                                   "</imsmd:langstring>\n".
                "         </imsmd:title>\n".
                "         <imsmd:language>fr</imsmd:language>\n".
                "         <imsmd:description>\n".
                "            <imsmd:langstring xml:lang=\"fr\">Ensemble de QCM au standard QTI-2.0. ".
                                   "Quizz de ".$author." et package fait par ".$_SESSION['prename_user']." ".$_SESSION['name_user']."</imsmd:langstring>\n".
                "         </imsmd:description>\n".
                "      </imsmd:general>\n".
                "      <imsmd:lifecycle>\n".
                "         <imsmd:version>\n".
                "            <imsmd:langstring xml:lang=\"fr\">1.0</imsmd:langstring>\n".
                "         </imsmd:version>\n".
                "         <imsmd:status>\n".
                "            <imsmd:source>\n".
                "               <imsmd:langstring xml:lang=\"fr\">LOMv1.0</imsmd:langstring>\n".
                "            </imsmd:source>\n".
                "            <imsmd:value>\n".
                "               <imsmd:langstring xml:lang=\"x-none\">Final</imsmd:langstring>\n".
                "            </imsmd:value>\n".
                "         </imsmd:status>\n".
                "      </imsmd:lifecycle>\n".
                "      <imsmd:metametadata>\n".
                "         <imsmd:metadatascheme>LOMv1.0</imsmd:metadatascheme>\n".
                "         <imsmd:metadatascheme>QTIv2.0</imsmd:metadatascheme>\n".
                "         <imsmd:language>fr</imsmd:language>\n".
                "      </imsmd:metametadata>\n".
                "      <imsmd:rights>\n".
                "         <imsmd:description>\n".
                "            <imsmd:langstring xml:lang=\"fr\">(c) Quizz de ".$author." et package fait par ".
                                   $_SESSION['prename_user']." ".$_SESSION['name_user']."</imsmd:langstring>\n".
                "         </imsmd:description>\n".
                "      </imsmd:rights>\n".
                "    </imsmd:lom>\n".
                "  </metadata>\n".
                "  <organizations />\n";
        return $data;
    }
    public static function RessManifest($compteur,$author)
    {
        $data = "  <resources>\n";
        for ($i=0; $i < $compteur; $i++)
        {
            $LeType = ($_SESSION['TabQcm'][$i]->multiple == 1) ? "choiceMultiple" : "choice";
            $TypeReponse = ($_SESSION['TabQcm'][$i]->multiple == 1) ? "rp01" : "rp02";
            $data .= "     <resource identifier=\"".$_SESSION['TabQcm'][$compteur-1]->REF."_".$i."\" ".
                               "type=\"imsqti_item_xmlv2p0\" href=\"".$_SESSION['FileQcm'][$i]."\" >\n".
                     "        <metadata>\n".
                     "           <schema>IMS QTI Item</schema>\n".
                     "           <schemaversion>2.0</schemaversion>\n".
                     "           <imsmd:lom>\n".
                     "              <imsmd:general>\n".
                     "                 <imsmd:identifier>\"".$LeType."\"</imsmd:identifier>\n".
                     "                 <imsmd:title>\n".
                     "                    <imsmd:langstring xml:lang=\"fr\">".utf8_encode(modif_az_qw(strip_tags(html_entity_decode($_SESSION['TabQcm'][$i]->question,ENT_QUOTES,'ISO-8859-1'))))."</imsmd:langstring>\n".
                     "                 </imsmd:title>\n".
                     "              </imsmd:general>\n".
                     "              <imsmd:lifecycle>\n".
                     "                 <imsmd:version>\n".
                     "                    <imsmd:langstring xml:lang=\"fr\">1.0</imsmd:langstring>\n".
                     "                 </imsmd:version>\n".
                     "                 <imsmd:status>\n".
                     "                    <imsmd:source>\n".
                     "                       <imsmd:langstring xml:lang=\"fr\">LOMv1.0</imsmd:langstring>\n".
                     "                    </imsmd:source>\n".
                     "                    <imsmd:value>\n".
                     "                       <imsmd:langstring xml:lang=\"fr\">Qcm Formagri</imsmd:langstring>\n".
                     "                    </imsmd:value>\n".
                     "                 </imsmd:status>\n".
                     "               </imsmd:lifecycle>\n".
                     "               <imsmd:rights>\n".
                     "                  <imsmd:description>\n".
                     "                     <imsmd:langstring xml:lang=\"fr\">(c)Quizz de ".$author." et package fait par ".
                                             $_SESSION['prename_user']." ".$_SESSION['name_user']."</imsmd:langstring>\n".
                     "                  </imsmd:description>\n".
                     "               </imsmd:rights>\n".
                     "            </imsmd:lom>\n".
                     "            <imsqti:qtiMetadata>\n".
                     "               <imsqti:timeDependent>false</imsqti:timeDependent>\n".
                     "               <imsqti:interactionType>choiceInteraction</imsqti:interactionType>\n".
                     "               <imsqti:feedbackType>nonadaptive</imsqti:feedbackType>\n".
                     "               <imsqti:solutionAvailable>true</imsqti:solutionAvailable>\n".
                     "               <imsqti:toolName>Formagri</imsqti:toolName>\n".
                     "               <imsqti:toolVersion>2.61</imsqti:toolVersion>\n".
                     "               <imsqti:toolVendor>Cnerta/Eduter/AgroSupDijon</imsqti:toolVendor>\n".
                     "            </imsqti:qtiMetadata>\n".
                     "        </metadata>\n".
                     "        <file href=\"".$_SESSION['FileQcm'][$i]."\" />\n";
            if ($_SESSION['TabQcm'][$i]->typ_img != '')
                 $data .= "        <file href=\"".str_replace("qcm_images/","",$_SESSION['TabQcm'][$i]->image)."\" />\n";
            $data .= "        <dependency identifierref=\"".$TypeReponse."\" />\n".
                     "     </resource>\n";
        }
        $data .= "  </resources>\n".
                 "</manifest>";
        return $data;
    }
    public static function MultipleChoice($compteur)
    {
        $nbProp = 0;
        $nbRepAt = 0;
        for ($i=1;$i<11;$i++)
        {
               $valeur = $i.'_val';
               $propos = $i.'_prop';
               if ($_SESSION['TabQcm'][$compteur]->$valeur == 1)
               {
                   $nbRepAt++;
               }
               if ($_SESSION['TabQcm'][$compteur]->$propos != '')
               {
                   $nbProp++;
               }
        }
        $_SESSION['TabQcm'][$compteur]->NbRepAt = $nbRepAt;
        $_SESSION['TabQcm'][$compteur]->NbProp = $nbProp;
        $data = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n".
                "<assessmentItem xmlns=\"http://www.imsglobal.org/xsd/imsqti_v2p0\"\n".
                "     xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\"\n".
                "     xsi:schemaLocation=\"http://www.imsglobal.org/xsd/imsqti_v2p0 imsqti_v2p0.xsd\"\n".
                "     identifier=\"".$_SESSION['TabQcm'][$compteur]->REF."\" title=\"".
                utf8_encode(modif_az_qw(strip_tags(html_entity_decode($_SESSION['TabQcm'][$compteur]->question,ENT_QUOTES,'ISO-8859-1'))))."\"\n".
                "     adaptive=\"false\" timeDependent=\"false\">\n";
        $data .= "    <responseDeclaration identifier=\"".$_SESSION['TabQcm'][$compteur]->REF."\" cardinality=\"multiple\" baseType=\"identifier\">\n".
                 "       <correctResponse>\n";
        for ($i=1; $i < ($_SESSION['TabQcm'][$compteur]->NbProp+1); $i++)
        {
           $valeur = $i.'_val';
           if ($_SESSION['TabQcm'][$compteur]->$valeur == 1)
              $data .= "          <value>Reponse_".$i."</value>\n";
        }
        $data .= "       </correctResponse>\n".
                 "       <mapping lowerBound=\"0\" upperBound=\"".$nbRepAt."\" defaultValue=\"0\">\n";
        for ($i=1; $i < ($_SESSION['TabQcm'][$compteur]->NbProp+1); $i++)
        {
           $valeur = $i.'_val';
           if ($_SESSION['TabQcm'][$compteur]->$valeur == 1)
              $data .= "          <mapEntry mapKey=\"Reponse_".$i."\" mappedValue=\"1\"/>\n";
        }
        $data .= "       </mapping>\n".
                 "    </responseDeclaration>\n".
                 "    <outcomeDeclaration identifier=\"SCORE\" cardinality=\"single\" baseType=\"integer\"/>\n".
                 "    <itemBody>\n";
                 "    (Plusieurs choix possibles)\n";
        $data .= "       <choiceInteraction responseIdentifier=\"".$_SESSION['TabQcm'][$compteur]->REF."\" shuffle=\"false\" maxChoices=\"0\">\n".
                 "           <prompt>".utf8_encode(modif_az_qw(strip_tags(html_entity_decode($_SESSION['TabQcm'][$compteur]->question,ENT_QUOTES,'ISO-8859-1'))))."</prompt>\n";
        if ($_SESSION['TabQcm'][$compteur]->typ_img != '')
            $data .= "           <object type=\"".$_SESSION['TabQcm'][$compteur]->typ_img."\" data=\"".
                              str_replace("qcm_images/","",$_SESSION['TabQcm'][$compteur]->image)."\"></object>\n";
        $VAL = array();
        $PROP = array();
        for ($i=1; $i < ($_SESSION['TabQcm'][$compteur]->NbProp+1); $i++)
        {
                $propos = $i."_prop";
                $PROP[$i] = $_SESSION['TabQcm'][$compteur]->$propos;
                $data .= "          <simpleChoice identifier=\"Reponse_".$i."\" fixed=\"false\">".utf8_encode(modif_az_qw(strip_tags(html_entity_decode($PROP[$i],ENT_QUOTES,'ISO-8859-1'))))."</simpleChoice>\n";
        }
        $data .= "       </choiceInteraction>\n".
                 "    </itemBody>\n".
                 "    <responseProcessing\n".
                 "        template=\"http://www.imsglobal.org/question/qti_v2p0/rptemplates/map_response\"\n".
                 "        templateLocation=\"../rptemplates/map_response.xml\"/>\n".
                 "</assessmentItem>";
        return $data;
    }

    public static function SingleChoice($compteur)
    {
        $nbProp = 0;
        $nbRepAt = 0;
        for ($i=1;$i<11;$i++)
        {
               $valeur = $i.'_val';
               $propos = $i.'_prop';
               if ($_SESSION['TabQcm'][$compteur]->$valeur == 1)
               {
                   $nbRepAt++;
               }
               if ($_SESSION['TabQcm'][$compteur]->$propos != '')
               {
                   $nbProp++;
               }
        }
        $_SESSION['TabQcm'][$compteur]->NbRepAt = $nbRepAt;
        $_SESSION['TabQcm'][$compteur]->NbProp = $nbProp;
        $data = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n".
                "<assessmentItem xmlns=\"http://www.imsglobal.org/xsd/imsqti_v2p0\"\n".
                "     xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\"\n".
                "     xsi:schemaLocation=\"http://www.imsglobal.org/xsd/imsqti_v2p0 ../imsqti_v2p0.xsd\"\n".
                "     identifier=\"".$_SESSION['TabQcm'][$compteur]->REF."\" title=\"".utf8_encode(modif_az_qw(strip_tags(html_entity_decode($_SESSION['TabQcm'][$compteur]->question,ENT_QUOTES,'ISO-8859-1'))))."\"\n".
                "     adaptive=\"false\" timeDependent=\"false\">\n";
        $data .= "    <responseDeclaration identifier=\"".$_SESSION['TabQcm'][$compteur]->REF."\" cardinality=\"single\" baseType=\"identifier\">\n".
                 "       <correctResponse>\n";
        for ($i=1; $i < ($_SESSION['TabQcm'][$compteur]->NbProp+1); $i++)
        {
           $valeur = $i.'_val';
           if ($_SESSION['TabQcm'][$compteur]->$valeur == 1)
              $data .= "          <value>Reponse_".$i."</value>\n";
        }
        $data .= "       </correctResponse>\n".
                 "       <mapping lowerBound=\"0\" upperBound=\"".$nbRepAt."\" defaultValue=\"0\">\n";
        for ($i=1; $i < ($_SESSION['TabQcm'][$compteur]->NbProp+1); $i++)
        {
           $valeur = $i.'_val';
           if ($_SESSION['TabQcm'][$compteur]->$valeur == 1)
              $data .= "          <mapEntry mapKey=\"Reponse_".$i."\" mappedValue=\"1\"/>\n";
        }
        $data .= "       </mapping>\n".
                 "    </responseDeclaration>\n".
                 "    <outcomeDeclaration identifier=\"SCORE\" cardinality=\"single\" baseType=\"integer\">\n".
                 "        <defaultValue>\n".
                 "           <value>0</value>\n".
                 "        </defaultValue>\n".
                 "    </outcomeDeclaration>\n".
                 "    <itemBody>\n".
                 "    (Un seul choix est attendu)\n";
        if ($_SESSION['TabQcm'][$compteur]->typ_img != '')
            $data .= "        <p><img src=\"".str_replace("qcm_images/","",$_SESSION['TabQcm'][$compteur]->image)."\" /></p>\n";
        $data .= "       <choiceInteraction responseIdentifier=\"".$_SESSION['TabQcm'][$compteur]->REF."\" shuffle=\"false\" maxChoices=\"1\">\n".
                 "           <prompt>".utf8_encode(modif_az_qw(strip_tags(html_entity_decode($_SESSION['TabQcm'][$compteur]->question,ENT_QUOTES,'ISO-8859-1'))))."</prompt>\n";
        if ($_SESSION['TabQcm'][$compteur]->typ_img != '')
            $data .= "           <object type=\"".$_SESSION['TabQcm'][$compteur]->typ_img."\" data=\"".
                              str_replace("qcm_images/","",$_SESSION['TabQcm'][$compteur]->image)."\"></object>\n";
        $VAL = array();
        $PROP = array();
        for ($i=1; $i < ($_SESSION['TabQcm'][$compteur]->NbProp+1); $i++)
        {
                $propos = $i."_prop";
                $data .= "          <simpleChoice identifier=\"Reponse_".$i."\">".utf8_encode(modif_az_qw(strip_tags(html_entity_decode($_SESSION['TabQcm'][$compteur]->$propos,ENT_QUOTES))))."</simpleChoice>\n";
        }
        $data .= "       </choiceInteraction>\n".
                 "    </itemBody>\n".
                 "    <responseProcessing\n".
                 "        template=\"http://www.imsglobal.org/question/qti_v2p0/rptemplates/map_response\"\n".
                 "        templateLocation=\"../rptemplates/match_correct.xml\"/>\n".
                 "</assessmentItem>";
        return $data;
    }

}
?>