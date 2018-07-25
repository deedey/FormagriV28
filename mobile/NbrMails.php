<?php
session_start();
if (!isset($_SESSION['IDUSER']) || $_SESSION['IDUSER'] == "")
{
  exit();
}
require ("../admin.inc.php");
require ("../fonction.inc.php");
dbConnect();
$requete_mail = mysql_query("SELECT * from messagerie where id_user = ".$_SESSION['IDUSER'].
                            " AND lu = '1' AND supprime = '0' ORDER BY mess_cdn desc limit 99");
$nbr_emails = mysql_num_rows($requete_mail);
echo " (".$nbr_emails." mails non lus )";
?>
