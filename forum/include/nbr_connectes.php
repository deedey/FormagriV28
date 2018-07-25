<?php
include("config.php");

// variables programmes
$serveur=$REMOTE_ADDR;
$ipqry = $REMOTE_ADDR;



// Connection à la base de donnée
mysql_select_db( $dbName , $connexion) or die("Impossible d'accèder à la base $dbName");

// Suppression des enregegistrements périmés (+5mm)
$heuredlt =  mktime(date("H") , date("i") , date("s"));
  $queryb = "SELECT * FROM compteur  ";
  $resultb =  mysql_query($queryb);
  $numberb = mysql_num_rows($resultb);

if ($number != 0)
$cpt = 0;
$ind = 0;
{
while ( $ind<$numberb)
{
$heureCon = mysql_result($resultb, $ind, "heureCon");
$ipCon = mysql_result($resultb, $ind, "ipCon");
$dateCon = mysql_result($resultb, $ind, "dateCon");
$ipdlt = $ipCon;

if ($heureCon < $heuredlt)
{
  $queryc = "DELETE    FROM compteur WHERE ipCon = '$ipdlt'  ";

  $resultc =  mysql_query($queryc);
}
$ind++;
}
}
// FIN Suppression des enregegistrements périmés (+5mm)



// Ecriture enregistrement nouveau connecté

  $query = "SELECT * FROM compteur where ipCon = '$ipqry' ";
  $result =  mysql_query($query);
  $number = mysql_num_rows($result);

if ($number == 0)
{
$ipCon = $ipqry;
$heureCon = mktime(date("H"),date("i")+5,date("s"));
$dateCon = Date ("Ymd");
$lheure = Date ("His");

 $resultat_sql=  mysql_query("insert into compteur values(\"$heureCon\",\"$ipCon\",\"$dateCon\",\"$lheure\")", $connexion);
}

//comptage final ...
  $queryd = "SELECT * FROM compteur";
  $resultd =  mysql_query($queryd);
  $cpt = mysql_num_rows($resultd);

  mysql_close($connexion);
if ($cpt == 1){
$libel = $texte_singulier;
}else{
$libel = $texte_pluriel;
}

echo "<Font color=blue><Small>$cpt $libel</SMALL></FONT>";


?>