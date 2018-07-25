<?php
foreach ($GLOBALS['_GET'] as $key => $superGlobalVal)
{
    $$key = $superGlobalVal;
}
 $sys_exp = $_SERVER['PATH'];
 $nom_local = $_SERVER['HTTP_HOST'];
 if ((strstr($nom_local , '127.0.0.') || strstr($nom_local , '192.168.')) && strstr($mon_hote,'localhost'))
    $mon_hote = str_replace("localhost",$nom_local,$mon_hote);

 if (strstr($nom_local , '127.0.0.') || strstr($nom_local,'localhost') || strstr($nom_local , '192.168.'))
   $ajoutSessionLocale = "if (!isset(\$_SESSION['IsOff']))\n      \$_SESSION['IsOff'] = 1;\n";
 else
    $ajoutSessionLocale = "if (!isset(\$_SESSION['IsOff']))\n      \$_SESSION['IsOff'] = 0;\n";
$controleInput = "\$ChainePost = '';
foreach (\$_POST as \$key => \$value)
{
 \$ChainePost .= (!is_array(\$value)) ? ' '.\$value : ' ';
}
if (isset(\$_POST) && (strstr(strtolower(\$ChainePost),'&lt;/script') || strstr(strtolower(\$ChainePost),'&#139;/script') ||
    strstr(strtolower(\$ChainePost),'&lt;script') || strstr(strtolower(\$ChainePost),'&#139;script') ||
    strstr(strtolower(\$ChainePost),'<script') || strstr(strtolower(\$ChainePost),'</script') ||
    strstr(strtolower(\$ChainePost),'< script') || strstr(strtolower(\$ChainePost),'</ script')))
{
   echo \"<script language='JavaScript'>\";
     echo \"alert('Attention !! Certaines balises incluses dans le formulaire sont interdites car dangereuses.');
          history.go(-2);\";
   echo \"</script>\";
   exit;
}
if (strstr(strtolower(urlencode(json_encode(\$_GET))),'&lt;/script') || strstr(strtolower(urlencode(json_encode(\$_GET))),'&#139;/script') ||
    strstr(strtolower(urlencode(json_encode(\$_GET))),'&lt;script') || strstr(strtolower(urlencode(json_encode(\$_GET))),'&#139;script') ||
    strstr(strtolower(urlencode(json_encode(\$_GET))),'%3cscript') || strstr(strtolower(urlencode(json_encode(\$_GET))),'%2fscript') ||
    strstr(strtolower(json_encode(\$_GET)),'insert ') || strstr(strtolower(json_encode(\$_GET)),'select '))
{
   echo \"<script language='JavaScript'>\";
     echo \"alert('Attention !! Certaines balises incluses dans l\'URL sont interdites car dangereuses.');
          history.go(-1);\";
   echo \"</script>\";
   exit;
}\n";

 if (strstr($sys_exp,"\\"))
 {
  $s_exp = "Ms";
  $line = "\$sys_exp = \$_SERVER['PATH'];\r\n";
  $line .= "if (strstr(\$sys_exp,\"/\"))\r\n    \$s_exp = \"lx\";\r\n";
  $line .= "else \r\n    \$s_exp = \"Ms\";\r\n";
  $line .= "\$signe =\"/\";\r\n \$base_root = \$_SERVER['DOCUMENT_ROOT'];\r\n";
  $line .= "\$rep_ttf = \$base_root.\$signe.\"graphique\".\$signe.\"ttf\".\$signe;\r\n";
  $line .= "\$host = \"$mon_hote\";\r\n";
  $nb_signe = count(explode('/',$mon_hote));
  if ($nb_signe > 3)
     $line .= "\$monURI = \"/\".str_replace('http://".$_SERVER['HTTP_HOST']."/','',\"$mon_hote\");\r\n ";
  else
     $line .= "\$monURI= '';\r\n";
  $line .= "\$repertoire= (\$monURI != '') ? \$base_root.\$monURI : \$base_root;\r\n";
  $line .= "\$rep_graph =\$repertoire.\$signe.\"graphique\".\$signe;\r\n";
  $line .= "\$nom_url = \"$mon_hote\";\r\n\$url_ress = \$nom_url;\r\n\$adresse_http = \$nom_url;\r\n";
  $line .= "\$adresse = \"$adresse_base\";\r\n\$bdd = \"$nom_base\";\r\n";
  $line .= "\$log = \"$login_base\";\r\n\$mdp =\"$passe_base\";\r\n";
  $line .= "\$forum_url = \$adresse_http.'/forum';\r\n\$admin_url = \$adresse_http.'/forum/admin';\r\n";
  $line .= "\$DefaultEmail = '$mon_email';\r\n";
  $line .= "\$Password = '$passe_forum';\r\n\$ForumModEmail = '$email_mod';\r\n\$ForumModPass = '$passe_mod';\r\n".
           "ini_set('error_reporting', 0);\r\n ".
           "ini_set('date.timezone','Europe/Paris');\r\n ".
           "ini_set('default_charset','iso-8859-1');\r\n";
  $line .= $ajoutSessionLocale."\r\n";
  $line .= $controleInput."\r\n";
 }
 else
 {
  $s_exp = "lx";
  $line = "\$sys_exp = \$_SERVER['PATH'];\n";
  $line .= "if (strstr(\$sys_exp,\"/\"))\n    \$s_exp = \"lx\";\n";
  $line .= "else \n   \$s_exp = \"Ms\";\n";
  $line .= "\$signe =\"/\";\n \$base_root = \$_SERVER['DOCUMENT_ROOT'];\n";
  $line .= "\$rep_ttf = \$base_root.\$signe.\"graphique\".\$signe.\"ttf\".\$signe;\n";
  $line .= "\$host = \"$mon_hote\";\n";
  $nb_signe = count(explode('/',$mon_hote));
  if ($nb_signe > 3)
     $line .= "\$monURI =  \"/\".str_replace('http://".$_SERVER['HTTP_HOST']."/','',\"$mon_hote\");\n";
  else
     $line .= "\$monURI= '';\n";
  $line .= "\$repertoire= (\$monURI != '') ? \$base_root.\$monURI : \$base_root;\n";
  $line .= "\$rep_graph =\$repertoire.\$signe.\"graphique\".\$signe;\n";
  $line .= "\$nom_url = \"$mon_hote\";\n\$url_ress = \$nom_url;\n\$adresse_http = \$nom_url;\n";
  $line .= "\$adresse = \"$adresse_base\";\n\$bdd = \"$nom_base\";\n";
  $line .= "\$log = \"$login_base\";\n\$mdp = \"$passe_base\";\n";
  $line .= "\$forum_url = \$adresse_http.'/forum';\n\$admin_url = \$adresse_http.'/forum/admin';\n";
  $line .= "\$DefaultEmail = '$mon_email';\n";
  $line .= "\$Password = '$passe_forum';\n\$ForumModEmail = '$email_mod';\n\$ForumModPass = '$passe_mod';\n".
           "ini_set('error_reporting', 0);\n".
           "ini_set('date.timezone','Europe/Paris');\n".
           "ini_set('default_charset','iso-8859-1');\n";
  $line .= $ajoutSessionLocale."\n";
  $line .= $controleInput."\r\n";
 }
  if ($s_exp == "Ms")
  {
    $suite = "";
    $suite1 = "if ((int) phpversion() < 5)\r\n".
    "    require_once (\$repertoire.\"/lib/addons.php\");\r\n".
    "if (!empty(\$_POST) && !empty(\$_SESSION['id_user']) && \r\n".
    "    !strstr(\$_SERVER['PHP_SELF'],'/trace.php') && !strstr(\$_SERVER['PHP_SELF'],'/appel_chat.php')  && \r\n".
    "    !strstr(\$_SERVER['PHP_SELF'],'/image_create.php'))\r\n".
    "{\r\n".
    "  \$ladate=date(\"Y-m-d H:i:s\" ,time());\r\n".
    "  \$file = \$_SERVER['PHP_SELF'];\r\n".
    "  \$personne = \$_SESSION['name_user'].\"   \".\$_SESSION['prename_user'].\"  : \".\$_SESSION['typ_user'];\r\n".
    "   mysql_select_db(\$bdd,mysql_connect(\$adresse,\$log,\$mdp));\r\n".
    "  \$requete = mysql_query(\"select * from tracking order by tracking_cdn desc\");\r\n".
    "  if (mysql_num_rows(\$requete) > 0)\r\n".
    "  {\r\n".
    "    \$der_track = mysql_result(\$requete,0,\"tracking_post_cmt\");\r\n".
    "    \$der_track .= mysql_result(\$requete,0,\"tracking_when_dt\");\r\n".
    "    \$der_track .= mysql_result(\$requete,0,\"tracking_file_lb\");\r\n".
    "    \$der_cdn = mysql_result(\$requete,0,\"tracking_cdn\");\r\n".
    "    \$der_cdn++;\r\n".
    "  }\r\n".
    "  if (isset(\$der_track) && \$der_track != json_encode(\$_POST).\$ladate.\$file)\r\n".
    "       \$inserer = mysql_query(\"insert into tracking values('\$der_cdn','\".\$_SESSION['id_user'].\"',\\\"\$personne\\\",\\\"\$ladate\\\",\\\"\$file\\\",'\".addslashes(json_encode(\$_POST)).\"')\");\r\n".
    "  else\r\n".
    "       \$inserer = mysql_query(\"insert into tracking values('1','\".\$_SESSION['id_user'].\"',\\\"\$personne\\\",\\\"\$ladate\\\",\\\"\$file\\\",'\".addslashes(json_encode(\$_POST)).\"')\");\r\n".
    "}\r\n";
    $line_suite = "<?php\r\n".$suite.$line."\r\n ?>";
    $suite_line = "<?php\r\n".$line.$suite1."\r\n ?>";
    $liner = "<?php\r\n".$line."\r\n ?>";
  }
  else
  {
    $suite = "";
    $suite1 = "if ((int) phpversion() < 5)\n".
    "   require_once (\$repertoire.\"/lib/addons.php\");\n".
    "if (!empty(\$_POST) && !empty(\$_SESSION['id_user']) && \n".
    "   !strstr(\$_SERVER['PHP_SELF'],'/trace.php') && !strstr(\$_SERVER['PHP_SELF'],'/appel_chat.php')  && \n".
    "   !strstr(\$_SERVER['PHP_SELF'],'/image_create.php'))\n".
    "{\n".
    "  \$ladate=date(\"Y-m-d H:i:s\" ,time());\n".
    "  \$file = \$_SERVER['PHP_SELF'];\n".
    "  \$personne = \$_SESSION['name_user'].\"   \".\$_SESSION['prename_user'].\"  : \".\$_SESSION['typ_user'];\n".
    "  mysql_select_db(\$bdd,mysql_connect(\$adresse,\$log,\$mdp));\n".
    "  \$requete = mysql_query(\"select * from tracking order by tracking_cdn desc\");\n".
    "   if (mysql_num_rows(\$requete) > 0)\n".
    "   {\n".
    "       \$der_track = mysql_result(\$requete,0,\"tracking_post_cmt\");\n".
    "       \$der_track .= mysql_result(\$requete,0,\"tracking_when_dt\");\n".
    "       \$der_track .= mysql_result(\$requete,0,\"tracking_file_lb\");\n".
    "       \$der_cdn = mysql_result(\$requete,0,\"tracking_cdn\");\n".
    "       \$der_cdn++;\n".
    "   }\n".
    "   if (isset(\$der_track) && \$der_track != json_encode(\$_POST).\$ladate.\$file)\n".
    "           \$inserer = mysql_query(\"insert into tracking values('\$der_cdn','\".\$_SESSION['id_user'].\"',\\\"\$personne\\\",\\\"\$ladate\\\",\\\"\$file\\\",'\".addslashes(json_encode(\$_POST)).\"')\");\n".
    "   else\n".
    "           \$inserer = mysql_query(\"insert into tracking values('1','\".\$_SESSION['id_user'].\"',\\\"\$personne\\\",\\\"\$ladate\\\",\\\"\$file\\\",'\".addslashes(json_encode(\$_POST)).\"')\");\n".
    "}\n ";
    $line_suite = "<?php\n".$suite.$line."\n?>";
    $suite_line = "<?php\n".$line.$suite1."\n?>";
    $liner = "<?php\n".$line."\n?>";
  }
  $fp = fopen("../admin.inc.php","w");
    $fw = fwrite($fp, $suite_line);
  fclose ($fp);
  $fp = fopen("../graphique/admin.inc.php","w");
    $fw = fwrite($fp, $line_suite);
  fclose ($fp);
  $fp = fopen("../forum/admin.inc.php","w");
    $fw = fwrite($fp, $liner);
  fclose ($fp);
  $fp = fopen("../forum/admin/admin.inc.php","w");
    $fw = fwrite($fp, $liner);
  fclose ($fp);
  $fp = fopen("../chat/admin.inc.php","w");
    $fw = fwrite($fp, $liner);
  fclose ($fp);
  $fp = fopen("../flash_chat/admin.inc.php","w");
    $fw = fwrite($fp, $liner);
  fclose ($fp);
  $fp = fopen("../flash_chat/chat/admin.inc.php","w");
    $fw = fwrite($fp, $liner);
  fclose ($fp);
  $fp = fopen("../flash_chat/chat/required/admin.inc.php","w");
    $fw = fwrite($fp, $liner);
  fclose ($fp);
  $afficher = "<strong>Operation reussie :</strong><BR><BR> <strong>Formagri</strong> est maintenant une application sur votre serveur";
  echo $afficher;
?>