<?php
if (!isset($_SESSION)) session_start();
// Inclusion du fichier API OAuth Twitter
require_once('twitteroauth.php');

// Définition des codes API Twitter
//@formagri
if (!isset($_SESSION['tweet_ck']))
{
// il faut configurer une application sur un compte twitter viable
  $_SESSION['tweet_ck'] = '';
  $_SESSION['tweet_cs'] = '' ;
  $_SESSION['tweet_at'] = '' ;
  $_SESSION['tweet_ats'] = '' ;
}
if (isset($_SESSION['tweet_ck']) && $_SESSION['tweet_ck'] != '')
{
   define('CONSUMER_KEY', $_SESSION['tweet_ck']);
   define('CONSUMER_SECRET', $_SESSION['tweet_cs']);
   define('ACCESS_TOKEN',$_SESSION['tweet_at']);
   define('ACCESS_TOKEN_SECRET',$_SESSION['tweet_ats']);
   $connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, ACCESS_TOKEN, ACCESS_TOKEN_SECRET);
   if (!isset($_SESSION['TwOk']) || (isset($_SESSION['TwOk']) && $_SESSION['TwOk'] == 0))
   {
      $twitterInfos = $connection->get('https://api.twitter.com/1.1/account/verify_credentials.json');
      $_SESSION['TwOk'] = ($connection->http_code == 200) ? 1 : 0;
   }
}
$TweetOk = (isset($_SESSION['TwOk']) && $_SESSION['TwOk'] == 1) ? 1 : 0;
include('configData.php');
?>
