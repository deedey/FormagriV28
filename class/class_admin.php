<?php
class Conception{
     function MdfPgs($nbr,$item){
          $requete = "UPDATE param_foad SET param_etat_lb = '$nbr' WHERE param_typ_lb='$item'";
          $req = mysql_query($requete);
     }
}
?>