<?php
include ('ApiScorm.inc.php');
echo "<frameset cols='0,*'>";
     echo "<frame src='' name='nothing' frameborder='0' scrolling='auto' />";
     $lien = urldecode($lien);
     echo "<frame src=\"$lien\" name='contenu' frameborder='0' scrolling='auto' />";
echo "</frameset>";
?>
