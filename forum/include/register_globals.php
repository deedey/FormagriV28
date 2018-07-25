<?php
$HTTP_GET_VARS = &$_GET;
$HTTP_POST_VARS = &$_POST;
$HTTP_POST_FILES = &$_FILES;
$HTTP_SERVER_VARS = &$_SERVER;
$HTTP_SESSION_VARS = &$_SESSION;
$HTTP_ENV_VARS = &$_ENV;
$HTTP_COOKIE_VARS = &$_COOKIE;
if(isset($HTTP_GET_VARS)){
  while(list($var, $val)=each($HTTP_GET_VARS)){
    $$var=$val;
  }
}
if(isset($HTTP_POST_VARS)){
  while(list($var, $val)=each($HTTP_POST_VARS)){
    $$var=$val;
  }
}
if(isset($HTTP_COOKIE_VARS)){
  while(list($var, $val)=each($HTTP_COOKIE_VARS)){
    $$var=$val;
  }
}
$PHP_SELF=$_SERVER["PHP_SELF"];
$HTTP_HOST=$_SERVER["HTTP_HOST"];
$HTTP_USER_AGENT=$_SERVER["HTTP_USER_AGENT"];
$QUERY_STRING=$_SERVER["QUERY_STRING"];
$REMOTE_ADDR=$_SERVER["REMOTE_ADDR"];
?>