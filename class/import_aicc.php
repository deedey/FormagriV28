<?php
class parse_aicc
{
   public static function compte_des($file)
   {
     $mon_fichier = '';
     $handle = fopen($file, "r");
     while (!feof($handle))
     {
        $ligne = fgets($handle, 4096);
        if ($mon_fichier != '' && $ligne{0} == '"')
        {
           $tab = explode('",',trim($ligne));
           $nb_tab = count($tab);
           return $nb_tab;
           break;
        }
        $mon_fichier .= $ligne;
     }
   }

   public static function modifie_aicc($file)
   {
     $mon_fichier = '';
     $compte = 0;
     $handle = fopen($file, "r"); 
     while (!feof($handle))
     {
        $compte++;
        $ligne = fgets($handle, 4096);
        if ($mon_fichier == '' && $ligne{0} =='"')
        {
           $tab = explode('",',trim($ligne));
           $nb_tab = count($tab);
        }
        $ligne = str_replace(",,",",\"\",",$ligne);
        $ligne = str_replace(",,",",\"\",",$ligne);
        $ligne = str_replace(",,",",\"\",",$ligne);
        if (substr(trim($ligne), -1, 1) == ",")
            $ligne = substr_replace(trim($ligne),",\"\"", -1, 1);
        if ($mon_fichier == '' && $ligne{0} == '"')
        {
           $contient = explode('",',trim($ligne)); 
           if (count($contient) > $nb_tab)
              return false;
        }
        if (strlen($ligne) > 2)
           $mon_fichier .= $ligne;
     }
     fclose ($handle);
     //if (strstr($file,'.AU')){echo $mon_fichier;exit;}
     $handle = fopen($file, "w");
     fputs ($handle,$mon_fichier);
     fclose ($handle);
     chmod($file,0775);
     return $mon_fichier;
   }

   public static function parse_crs($file)
   {
     $data = '';
     $valide = 0;$i = 0;$passe = 0;$description='';
     $handle = fopen($file, "r");
     while (!feof($handle))
     {
        $ligne = fgets($handle, 4096);
        $ligne = str_replace("|","",$ligne);
        if (strstr(strtolower(trim($ligne)),'[course]') ||
           strstr(strtolower(trim($ligne)),'[course_behavior]') ||
           strstr(strtolower(trim($ligne)),'[course_description]'))
           $valide++;
        if (strstr(strtolower(trim($ligne)),'='))
        {
           $tab = explode('=',$ligne);
           $i++;
           if ($i == 1)
              $data .= trim($tab[0])."=".trim($tab[1]);
           else
              $data .= "|".trim($tab[0])."=".trim($tab[1]);
        }
        if ($passe == 1)
           $description .= $ligne;
        if (strstr(strtolower(trim($ligne)),'[course_description]'))
           $passe = 1;
     }
     $data .= "|description=".str_replace("\"","'",$description);
     return $data;
   }
   public static function parse_cst($file,$nb_blocks,$nb_au)
   {
     $data = '';
     $compteur = 0;
     $handle = fopen($file, "r");
     while (!feof($handle))
     {
        $ligne = fgets($handle, 4096);
        if (strlen(trim($ligne)) == "")
           continue;
        $ligne = str_replace('"','',$ligne);
        if (strstr(strtolower(trim($ligne)),'block'))
        {
           $tab = explode(',',$ligne);
           $nb_tab = count($tab);
//           if (($nb_tab-1) != $nb_blocks)
//             return FALSE;
        }
        if ($compteur > 0)
        {
           $tabB = explode(',',$ligne);
           $nb_tabB = count($tabB);
           if (strtolower($tabB[0]) != 'root')
               return FALSE;
           for ($i = 1; $i < $nb_tabB+1;$i++)
           {
               if ($i > 1 && $i < $nb_tabB+1)
                 $data .= '|';
               if (isset($tabB[$i]))
               {
                  $txt =$tabB[$i];
                  if (strtoupper($txt{0}) == 'A')
                      $data .= $tabB[$i];
               }
               elseif (strtoupper($txt{0}) == 'B')
               {
                  $ligne = fgets($handle, 4096);
                  $ligne = str_replace("*","",$ligne);
                  $ligne = str_replace("|","",$ligne);
                  $ligne = str_replace('"','',$ligne);
                  $tabA = explode(',',$ligne);
                  $nb_tabA = count($tabA);
                  for ($j = 0; $j < $nb_tabA;$j++)
                  {
                     if ($j == 1)
                        $data .= "*";
                     elseif ($j > 1 && ($j < $nb_tabA))
                        $data .= "-";
                     $data .= $tabA[$j];
                  }
               }
           }
        }
        $compteur++;
     }
     fclose ($handle);
     return $data;
   }
}
?>