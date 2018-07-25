<HTML><HEAD><TITLE>Chat Installation</TITLE>
<link REL='StyleSheet' TYPE='text/css' HREF='chat.css'>
</HEAD>
<BODY>
<?php
include "param.php";
include "mysql.php";

Connectix();
function CheckTable() {
	global $param;
	$ok=1;
	$query="SELECT * FROM ".$param["table_msg"];
	$result=mysql_query($query);
	if ($result==true) {
		$ok=0;
		print("La table ".$param["table_msg"]." existe déjà<BR>");
	}
	$query="SELECT * FROM ".$param["table_user"];
	$result=mysql_query($query);
	if ($result==true) {
		$ok=0;
		print("La table ".$param["table_user"]." existe déjà<BR>");
	}
	$query="SELECT * FROM ".$param["table_admin"];
	$result=mysql_query($query);
	if ($result==true) {
		$ok=0;
		print("La table ".$param["table_admin"]." existe déjà<BR>");
	}
	$query="SELECT * FROM ".$param["table_salle"];
	$result=mysql_query($query);
	if ($result==true) {
		$ok=0;
		print("La table ".$param["table_salle"]." existe déjà<BR>");
	}
	if ($ok==1) {
		print("Pour l'installation, création des tables :<BR>");
		print($param["table_msg"]."<BR>");
		print($param["table_user"]."<BR>");
		print($param["table_admin"]."<BR>");
		print($param["table_salle"]."<BR>");		
		print("<BR>Etes-vous sûr de créer ces tables ?<BR><A href='install.php?confirm=yes'>OUI</A>");
	} else {
		print("<BR>Certaines tables existent déjà.<BR>");
		print("Vous pouvez changer leur nom dans le fichier param.php ou les créer quand même...<BR>");
		print("Etes-vous sûr de créer ces tables ?<BR><A href='install.php?confirm=yes'>OUI</A><BR>");
	}
}

function CreateTable() {
	global $param;
	$query="DROP table ".$param["table_user"];
	$result=mysql_query($query);

	$query="CREATE TABLE ".$param["table_user"]." (
	   ID int(11) NOT NULL auto_increment,
	   login char(10),
	   password char(10),
	   mail char(100),
	   dt timestamp(14),
	   PRIMARY KEY (ID),
	   UNIQUE ID (ID)
	)";
	$result=mysql_query($query);


	$query="DROP table ".$param["table_salle"];
	$result=mysql_query($query);

	$query="CREATE TABLE ".$param["table_salle"]." (
	   ID int(11) NOT NULL auto_increment,
	   user char(10),
	   user_ID char(10),
	   dt timestamp(14),
	   dt_first datetime,
	   PRIMARY KEY (ID),
	   UNIQUE ID (ID)
	)";
	$result=mysql_query($query);

	
	$query="DROP table ".$param["table_admin"];
	$result=mysql_query($query);

	$query="CREATE TABLE ".$param["table_admin"]." (
	   ID int(11) NOT NULL auto_increment,
	   dt_last_liste datetime,
	   dt_last_chat datetime,
	   dt_last_p4 datetime,
	   dt_last_admin datetime,
	   PRIMARY KEY (ID),
	   UNIQUE ID (ID)
	);";
	$result=mysql_query($query);

	$query="INSERT INTO ".$param["table_admin"]." (ID, dt_last_liste, dt_last_chat, dt_last_p4, dt_last_admin) VALUES ('', '2001-01-01 01:01:01', '2001-01-01 01:01:01', '2001-01-01 01:01:01','2001-01-01') ";
	$result=mysql_query($query);


	
	$query="DROP table ".$param["table_msg"];
	$result=mysql_query($query);

	$query="CREATE TABLE ".$param["table_msg"]." (
   ID int(11) NOT NULL auto_increment,
   user varchar(10),
   text text,
   dest varchar(10),
   dt timestamp(14),
   PRIMARY KEY (ID),
   UNIQUE ID (ID)
 	)";
	$result=mysql_query($query);

	print("Création des tables OK<BR>");
	print("<A href='index.php'>Tester le chat</A>");
}
print("Cette page permet de créer les tables mysql nécessaires au chat<BR>");
print("Je vous invite à lire le fichier <A href='install.txt' target='_blank'>install.txt</A><BR><BR><BR>"); 

if ($confirm!="yes") {
	CheckTable();
} else {
	CreateTable();
}

?>

</BODY></HTML>