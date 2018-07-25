<?php

require_once("xml2json.php");
error_reporting ("E_ALL");
// Filename from where XML contents are to be read.
$testXmlFile = "../ressources/admin_1/ressources/QcmScormTrous/Mon_premierQcm/passe_present_02.xml";
//Read the XML contents from the input file.
//file_exists($testXmlFile) or die('Could not find file ' . $testXmlFile);
$xmlStringContents = file_get_contents($testXmlFile);
$jsonContents = "";

// Convert it to JSON now.
// xml2json simply takes a String containing XML contents as input.
$jsonDeContents =xml2json::transformXmlStringToJson($xmlStringContents);
//$jsonDeContents=json_decode($jsonContents,true);
//echo $jsonContents;
$nbInteractions=count($jsonDeContents['evaluation']['interaction']);
//$affiche = 'interactions = '.$nbInteractions."\n";
$affiche .= "\n<evaluation id = \"".$jsonDeContents['evaluation']['@attributes']['id']. "\" ";
$affiche .= "label = \"".$jsonDeContents['evaluation']['@attributes']['label']. "\" ";
$affiche .= "masteryScore = \"".$jsonDeContents['evaluation']['@attributes']['masteryScore']. "\" ";
$affiche .= "positiveFeedback = \"".$jsonDeContents['evaluation']['@attributes']['positiveFeedback']. "\" ";
$affiche .= "negativeFeedback = \"".$jsonDeContents['evaluation']['@attributes']['negativeFeedback']. "\" >\n";
for ($i=0;$i<$nbInteractions;$i++)
{
   $affiche .= "<interaction id = \"".$jsonDeContents['evaluation']['interaction'][$i]['@attributes']['id']."\" ";
   $affiche .=  "label = \"".$jsonDeContents['evaluation']['interaction'][$i]['@attributes']['label']."\" ";
   $affiche .=  "type = \"".$jsonDeContents['evaluation']['interaction'][$i]['@attributes']['type']. "\" ";
   $affiche .=  "weighting = \"".$jsonDeContents['evaluation']['interaction'][$i]['@attributes']['weighting']. "\" >\n";
   for($j=0;$j<4;$j++)
   {
       $affiche .=    "<choice label = \"". $jsonDeContents['evaluation']['interaction'][$i]['choice'][$j]['@attributes']['label']. "\" ";
       if (!empty($jsonDeContents['evaluation']['interaction'][$i]['choice'][$j]['@attributes']['correct']))
          $affiche .=    "  correct = \"". $jsonDeContents['evaluation']['interaction'][$i]['choice'][$j]['@attributes']['correct']. "\" /> \n ";
       else
          $affiche .=  " /> \n ";
   }
   $affiche .=  " </interaction>\n ";
}
$affiche .=  " </evaluation> ";
header ("content-type: text/xml");
echo $affiche;
//echo($jsonDeContents['evaluation']['interaction'][2]['@attributes']['label']);echo "<br>";
//echo '<pre>';print_r($jsonDeContents);echo '</pre>';

//include ('array2xml.php');
//$xml = new arr2xml($jsonDeContents);
//header ("content-type: text/xml");
//echo $xml->get_xml();

?>