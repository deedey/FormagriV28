<?php
if (!isset($_SESSION)) session_start();
include('../../include/UrlParam2PhpVar.inc.php');
require "admin.inc.php";
require 'fonction.inc.php';
require ("required/config$lg.php");
echo "&url=".urlencode($url);
echo "&pre=".urlencode($before_name)."&post=".urlencode($after_name);
echo "&output=".urlencode($conn);
echo "&you_are=".urlencode($you_are);
echo "&intro_text=".urlencode($intro_text);
echo "&private_message_to=".urlencode($private_message_to);
echo "&connected_users=".urlencode($connected_users);
echo "&private_message_text=".urlencode($private_message_text);
?>
