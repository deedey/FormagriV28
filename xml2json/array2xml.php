<?php
/****
* @PHPVER4.0
*
* @author        emnu
* @ver        --
* @date        12/08/08
*
* use this class to convert from mutidimensional array to xml.
* see example.php file on howto use this class
*
*/

class arr2xml
{
        var $array = array();
        var $xml = '';

        function arr2xml($array)
        {
                $this->array = $array;

                if(is_array($array) && count($array) > 0)
                {
                        $this->struct_xml($array);
                }
                else
                {
                        $this->xml .= "no data";
                }
        }

        function struct_xml($array)
        {
          global $tg,$te,$ti,$tc;
                foreach($array as $k=>$v)
                {
                        if(is_array($v))
                        {
                                $tag = preg_replace('/^[0-9]{1,}/','data',$k); // replace numeric key in array to 'data'
                                $tag=str_replace('@','',$tag);
                                if (strstr($tag,'evaluation') && $te == 1)
                                {
                                   $this->xml .="<evaluation ";$te = 0;
                                }
                                elseif (strstr($tag,'evaluation'))
                                {
                                   $this->xml .="<evaluation ";$te = 1;
                                }
                                elseif (strstr($tag,'interaction') && $ti == 1)
                                {
                                   $this->xml .="<interaction ";$ti = 0;
                                }
                                elseif (strstr($tag,'interaction'))
                                {
                                   $this->xml .="<interaction ";$ti = 1;
                                }
                                elseif (strstr($tag,'choice') && $tc == 1)
                                {
                                   $this->xml .="<choice ";$tc =0;
                                }
                                elseif (strstr($tag,'choice'))
                                {
                                   $this->xml .="<choice ";$tc = 1;
                                }
                                elseif (strstr($tag,'attributes'))
                                   $this->xml .= "";
                                else
                                   $this->xml .="<$tag>";
                                if (strstr($tag,'attributes') && $tg == 1)
                                {
                                    $tg = 0;
                                }
                                elseif (strstr($tag,'attributes'))
                                {
                                    $tg = 1;
                                }
                                $this->struct_xml($v);
                        }
                        else
                        {
                                $tag = preg_replace('#^[0-9]{1,}#','',$k); // replace numeric key in array to 'data'
                                if (strstr($tag,'negativeFeedback'))
                                   $this->xml .= "$tag=\"$v\"> ";
                                elseif (strstr($tag,'weighting'))
                                   $this->xml .= "$tag=\"$v\">\n";
                                elseif (strstr($tag,'correct'))
                                   $this->xml .= "$tag=\"$v\" />\n";
                                elseif($tag != '')
                                   $this->xml .= "$tag=\"$v\" ";
                        }
                }
        }

        function get_xml()
        {
                $header = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>";
                $header .= "<!-- Genere par Formagri V 2.6.2 [Support SCORM v1.2 (c)]\n
Le lundi  4 avril 2011 sur la plate-forme http://ef-dev2.educagri.fr par Dey Bendifallah : ADMINISTRATEUR-->";

                //echo $header;
                echo $this->xml;
        }
}

?>