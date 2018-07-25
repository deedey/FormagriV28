<?php
if (!isset($_SESSION)) session_start();
class expQti12
{
   public static function enteteXmlQtiMC($dte,$auteur,$typ_user,$adresse_http)
   {
        $data = "<?xml version=\"1.0\" encoding=\"ISO-8859-1\" standalone=\"no\"?>\n".
              "<!--\n".
              "Genere par Formagri-Ceres V 2.6.0 [Support QTI v1.2 ©] pour Qplayer©[http://www.e-teach.ch]\n".
              "Le ".$dte." sur la plate-forme $adresse_http\npar ".$auteur." : $typ_user\n".
              "//-->\n";
        ?>
        <?php
        return $data;
        
    }

    public static function ScormEntete($compteur,$author)
    {
        $data = "<organizations default=\"Q\">\n".
                "   <organization identifier=\"MCQ\">\n".
                "      <title><![CDATA[".utf8_encode(modif_az_qw(strip_tags(html_entity_decode($_SESSION['TabQcm'][0]->titre_qcm,ENT_QUOTES,'ISO-8859-1'))))."]]></title>\n";
        $data .= "         <item identifier=\"".$_SESSION['TabQcm'][0]->REF."\" identifierref=\"SCO_0001\" isvisible=\"true\">\n".
                 "            <title><![CDATA[".utf8_encode(modif_az_qw(strip_tags(html_entity_decode($_SESSION['TabQcm'][0]->titre_qcm,ENT_QUOTES,'ISO-8859-1'))))."]]></title>\n";
        $data .= "            <adlcp:masteryscore>".$_SESSION['moyenneQcm']."</adlcp:masteryscore>\n".
                 "            <adlcp:maxtimeallowed>0000:".$_SESSION['TabQcm'][0]->duree.":00.00</adlcp:maxtimeallowed>\n".
                 "            <adlcp:timelimitaction>exit,no message</adlcp:timelimitaction>\n".
                 "         </item>\n";
        $data .= "     <metadata>\n".
                 "        <schema>ADL SCORM</schema>\n".
                 "        <schemaversion>1.2</schemaversion>\n".
                 "        <imsmd:lom>\n".
                 "           <imsmd:general>\n".
                 "              <imsmd:title>\n".
                 "                 <imsmd:langstring xml:lang=\"fr\">";
        if (isset($_SESSION['TabQcm'][$compteur]->titre_qcm))
            $data .= utf8_encode(modif_az_qw(strip_tags(html_entity_decode($_SESSION['TabQcm'][$compteur]->titre_qcm,ENT_QUOTES,'ISO-8859-1'))));
        $data .= "                          </imsmd:langstring>\n".
                 "              </imsmd:title>\n".
                 "              <imsmd:language>fr</imsmd:language>\n".
                 "              <imsmd:description>\n".
                 "                 <imsmd:langstring xml:lang=\"fr\">Ensemble de QCM au standard QTI-1.2 sous Qplayer. ".
                                   "Quizz de ".$author." et package fait par ".$_SESSION['prename_user']." ".$_SESSION['name_user']."</imsmd:langstring>\n".
                 "              </imsmd:description>\n".
                 "           </imsmd:general>\n".
                 "           <imsmd:technical>\n".
                 "              <imsmd:format>application/x-shockwave-flash</imsmd:format>\n".
                 "           </imsmd:technical>\n".
                 "        </imsmd:lom>\n".
                 "     </metadata>\n".
                 "   </organization>\n".
                 "</organizations>\n".
                 "<resources>\n".
                 "   <resource identifier=\"SCO_0001\" type=\"webcontent\" href=\"index.html\" adlcp:scormtype=\"sco\">\n".
                 "      <file href=\"index.html\"/>\n".
                 "   </resource>\n".
                 "</resources>\n".
                 "</manifest>";
        return $data;
    }

    public static function IntroQtiMC($compteur)
    {
        $data = "<questestinterop>\n".
              "    <item title=\"Multiple Choice\" ident=\"XXX\">\n".
              "       <presentation>\n".
              "          <flow>\n";
        return $data;
    }
    public static function IntroManifestQtiMC($compteur)
    {
        $moyenne = ($_SESSION['TabQcm'][0]->moyenne < 21) ? $_SESSION['TabQcm'][0]->moyenne * 5 : $_SESSION['TabQcm'][0]->moyenne;
        $_SESSION['moyenneQcm'] = $moyenne;
        $data = "<manifest>\n".
                "    <finalfeedback>\n".
                "       <range lowerthan=\"".$moyenne."\">\n".
                "         <material>\n".
                "            <mattext>Malheureusement vous n'avez pas atteint le score requis (".$moyenne." %) !</mattext>\n".
                "         </material>\n".
                "       </range>\n".
                "       <range greaterthan=\"".$moyenne."\">\n".
                "          <material>\n".
                "             <mattext>Bravo, vous avez atteint un score supérieur à ".$moyenne." % !</mattext>\n".
                "          </material>\n".
                "       </range>\n".
                "    </finalfeedback>\n".
                "    <resources duration=\"PT".$_SESSION['TabQcm'][0]->duree."M0S\" maxattempts=\"1\">\n";
        for ($i=0; $i < $compteur; $i++)
        {
            $data .= "        <resource identifier=\"multiple_choice\" href=\"".$_SESSION['FileQcm'][$i]."\"/>\n";
        }
        $data .= "    </ressources>\n".
                 "</manifest>";
      return $data;
    }

    public static function QuestTxt($compteur)
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
           $nbRepOk = ($_SESSION['TabQcm'][$compteur]->multiple == 1) ? $_SESSION['TabQcm'][$compteur]->n_pages : 1;
           $LeType = ($_SESSION['TabQcm'][$compteur]->multiple == 0) ? "Single" : "Multiple";
           $data = "             <response_lid ident=\"".$_SESSION['TabQcm'][$compteur]->REF."\" rcardinality=\"$LeType\">\n".
                   "                <render_choice maxnumber=\"".$nbRepAt."\">\n".
                   "                    <material>\n".
                   "                       <mattext texttype=\"text/plain\" x0=\"20\" y0=\"10\"><![CDATA[".
                   utf8_encode(modif_az_qw(strip_tags(html_entity_decode($_SESSION['TabQcm'][$compteur]->question,ENT_QUOTES,'ISO-8859-1'))))."]]></mattext>\n";
            if ($_SESSION['TabQcm'][$compteur]->typ_img != '' && strstr($_SESSION['TabQcm'][$compteur]->typ_img,"jp"))
            {
                 $data .= "                       <matimage imagtype=\"".$_SESSION['TabQcm'][$compteur]->typ_img."\" uri=\"".
                           str_replace("qcm_images/","",$_SESSION['TabQcm'][$compteur]->image)."\" x0=\"300\" y0=\"350\"/>\n";
            }
            $data .= "                    </material>\n";
            $VAL = array();
            $PROP = array();
            $REF = array();
            for ($i=1; $i < ($_SESSION['TabQcm'][$compteur]->NbProp+1); $i++)
            {
                $valeur = $i.'_val';
                $propos = $i."_prop";
                $VAL[$i] = $_SESSION['TabQcm'][$compteur]->$valeur;
                $PROP[$i] = $_SESSION['TabQcm'][$compteur]->$propos;
                $REF[$i] = $_SESSION['TabQcm'][$compteur]->REF."_".$i;
                $data .= "                    <flow_label>\n".
                         "                         <response_label ident=\"".$_SESSION['TabQcm'][$compteur]->REF."_".$i."\">\n".
                         "                             <material>\n".
                         "                                <mattext texttype=\"text/plain\"><![CDATA[".
                                                                   utf8_encode(modif_az_qw(strip_tags(html_entity_decode($_SESSION['TabQcm'][$compteur]->$propos,ENT_QUOTES,'ISO-8859-1'))))."]]></mattext>\n".
                         "                             </material>\n".
                         "                         </response_label>\n".
                         "                    </flow_label>\n";
            }
            $data .= "                </render_choice>\n".
                     "           </response_lid>\n".
                     "       </flow>\n".
                     "    </presentation>\n".
                     "    <resprocessing>\n";
            return $data;
    }

    public static function ReponsesQcm($compteur)
    {
            GLOBAL $data;
             $maxValue = ($_SESSION['TabQcm'][$compteur]->multiple == 1) ? 999 : 1;
             $data .= "      <outcomes>\n".
                      "         <decvar varname=\"SCORE\" vartype=\"Integer\" defaultval=\"0\" maxvalue=\"".$maxValue."\" minvalue=\"0\" />\n".
                      "      </outcomes>\n";
             $data .= "      <respcondition title=\"Correct\" continue=\"Yes\">\n".
                      "         <conditionvar>\n".
                      "            <and>\n";
             for ($i = 1;$i < ($_SESSION['TabQcm'][$compteur]->NbProp+1); $i++)
             {
                $valeur = $i.'_val';
                $propos = $i."_prop";
                if ($_SESSION['TabQcm'][$compteur]->$valeur == 1)
                {
                   $data .= "               <varequal respident=\"".$_SESSION['TabQcm'][$compteur]->REF."\"><![CDATA[".
                                            $_SESSION['TabQcm'][$compteur]->REF."_".$i."]]></varequal>\n";
                }
             }
             $data .= "               <not>\n".
                      "                  <or>\n";
             for ($i = 1;$i < ($_SESSION['TabQcm'][$compteur]->NbProp+1); $i++)
             {
                $valeur = $i.'_val';
                $propos = $i."_prop";
                if ($_SESSION['TabQcm'][$compteur]->$valeur == 0)
                {
                   $data .= "               <varequal respident=\"".$_SESSION['TabQcm'][$compteur]->REF."\"><![CDATA[".
                                            $_SESSION['TabQcm'][$compteur]->REF."_".$i."]]></varequal>\n";
                }
             }
             $data .= "                  </or>\n".
                      "               </not>\n".
                      "            </and>\n".
                      "         </conditionvar>\n".
                      "         <displayfeedback linkrefid=\"".$_SESSION['TabQcm'][$compteur]->REF."displayRight\"/>\n".
                      "      </respcondition>\n";
             $data .= "      <respcondition title=\"Incorrect\" continue=\"Yes\">\n".
                      "         <conditionvar>\n".
                      "            <or>\n";
             for ($i = 1;$i < ($_SESSION['TabQcm'][$compteur]->NbProp+1); $i++)
             {
                $valeur = $i.'_val';
                $propos = $i."_prop";
                if ($_SESSION['TabQcm'][$compteur]->$valeur == 0)
                {
                   $data .= "               <varequal respident=\"".$_SESSION['TabQcm'][$compteur]->REF."\"><![CDATA[".
                                            $_SESSION['TabQcm'][$compteur]->REF."_".$i."]]></varequal>\n";
                }
             }
             $data .= "               <not>\n".
                      "                  <and>\n";
             for ($i = 1;$i < ($_SESSION['TabQcm'][$compteur]->NbProp+1); $i++)
             {
                $valeur = $i.'_val';
                $propos = $i."_prop";
                if ($_SESSION['TabQcm'][$compteur]->$valeur == 1)
                {
                   $data .= "               <varequal respident=\"".$_SESSION['TabQcm'][$compteur]->REF."\"><![CDATA[".
                                            $_SESSION['TabQcm'][$compteur]->REF."_".$i."]]></varequal>\n";
                }
             }
             $data .= "                  </and>\n".
                      "               </not>\n".
                      "            </or>\n".
                      "         </conditionvar>\n".
                      "         <displayfeedback linkrefid=\"".$_SESSION['TabQcm'][$compteur]->REF."displayWrong\"/>\n".
                      "      </respcondition>\n";
             for ($i = 1;$i < ($_SESSION['TabQcm'][$compteur]->NbProp+1); $i++)
             {
                $valeur = $i.'_val';
                $propos = $i."_prop";
                if ($_SESSION['TabQcm'][$compteur]->NbRepAt > 0)
                   $NoteBase = round((1 / $_SESSION['TabQcm'][$compteur]->NbRepAt),2);
                if ($_SESSION['TabQcm'][$compteur]->$valeur == 1)
                {
                     $data .= "      <respcondition title=\"adjustscore\" continue=\"Yes\">\n".
                              "          <conditionvar>\n".
                              "             <varequal respident=\"".$_SESSION['TabQcm'][$compteur]->REF."\">".
                              "<![CDATA[".$_SESSION['TabQcm'][$compteur]->REF."_".$i."]]></varequal>\n".
                              "          </conditionvar>\n".
                              "          <setvar varname=\"SCORE\" action=\"Add\">".
                              "<![CDATA[".$NoteBase."]]></setvar>\n".
                              "        </respcondition>\n";
                }
             }
             $data .= "    </resprocessing>\n";
             return $data;
    }

    public static function FeedbackQcm($compteur)
    {
       $data = "    <itemfeedback ident=\"".$_SESSION['TabQcm'][$compteur]->REF."displayWrong\">\n".
               "       <flow_mat>\n".
               "          <material>\n".
               "             <mattext texttype=\"text/plain\"><![CDATA[Réponse incorrecte.";
       for ($i = 1;$i < ($_SESSION['TabQcm'][$compteur]->NbProp+1); $i++)
       {
            $valeur = $i.'_val';
            $propos = $i."_prop";
            if ($_SESSION['TabQcm'][$compteur]->$valeur == 0)
                $data .= "- ".$_SESSION['TabQcm'][$compteur]->$propos.", ";
       }
       $data .= "]]></mattext>\n".
                "          </material>\n".
                "       </flow_mat>\n".
                "    </itemfeedback>\n".
                "    <itemfeedback ident=\"".$_SESSION['TabQcm'][$compteur]->REF."displayRight\">\n".
                "       <flow_mat>\n".
                "          <material>\n".
                "             <mattext texttype=\"text/plain\"><![CDATA[Réponse correcte !";
       for ($i = 1;$i < ($_SESSION['TabQcm'][$compteur]->NbProp+1); $i++)
       {
            $valeur = $i.'_val';
            $propos = $i."_prop";
            if ($_SESSION['TabQcm'][$compteur]->$valeur == 1)
                $data .= "- ".$_SESSION['TabQcm'][$compteur]->$propos.", ";
       }
       $data .= "]]></mattext>\n".
                "          </material>\n".
                "       </flow_mat>\n".
                "    </itemfeedback>\n".
                "  </item>\n".
                "</questestinterop>";
       return $data;
    }
}
?>