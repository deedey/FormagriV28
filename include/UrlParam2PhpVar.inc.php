<?php
/**
 * transformation des param�tres en variables remplace register_globals=1
 * date 20 d�c. 07 09:37:24
*/
$aSuperGlobal = array ('_SESSION','_GET','_POST','_FILES');
foreach ($aSuperGlobal as $superGlobal)
{
       foreach ($GLOBALS[$superGlobal] as $key => $superGlobalVal)
       {
               $$key = $superGlobalVal;

       }
}
?>