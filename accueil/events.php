<?php
$der_connex = mysql_query("SELECT date_fin,heure_fin from log WHERE login ='$login' AND date_fin !='0000-00-00' order by log_cdn desc");
$result = mysql_num_rows($der_connex);
if ($result > 0)
{
   $derdate = mysql_result($der_connex,0,"date_fin");
   echo "<h4>$msgevt_WNS".reverse_date($derdate,"-","/")."</h4>";
}
else
   echo "<h4>$msg_WNS</h4>";
?>


   <div id="container-1">
            <ul class=tabs-nav>
                <li><a href="<?php echo "accueil/acces_connect.inc.php";?>"><span><?php echo $msgAcc_cnx;?></span></a></li>
                <li><a href="<?php echo "accueil/acces_msg.inc.php";?>"><span><?php echo $msgAcc_msg;?></span></a></li>
                <li><a href="<?php echo "accueil/acces_forum.inc.php";?>"><span><?php echo $msgAcc_frm;?></span></a></li>
                <li><a href="<?php echo "accueil/acces_tut.inc.php";?>"><span><?php echo $msgAcc_rdv;?></span></a></li>
                <li><a href="<?php echo "accueil/acces_insc.inc.php";?>"><span><?php echo $msgAcc_ins;?></span></a></li>
                <li><a href="<?php echo "accueil/acces_contenu.inc.php?objet=module";?>"><span><?php echo $msgAcc_mod;?></span></a></li>
                <li><a href="<?php echo "accueil/acces_contenu.inc.php?objet=sequence";?>"><span><?php echo $msgAcc_seq;?></span></a></li>
                <li><a href="<?php echo "accueil/acces_contenu.inc.php?objet=activite";?>"><span><?php echo $msgAcc_act;?></span></a></li>
                <li><a href="<?php echo "accueil/acces_ress.inc.php";?>"><span><?php echo $msgAcc_res;?></span></a></li>
                <li><a href="<?php echo "accueil/acces_dosspar.inc.php";?>"><span><?php echo $msgAcc_rep;?></span></a></li>
            </ul>
   </div>