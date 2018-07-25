#!/bin/sh
# phorum installation script
# originally written Morgan Christiansson <mog@linux.nu>
# revised by Brian Moon <brian@phorum.org>

if [ ! -f common.php ]; then
  echo "Run this from the phorum base directory."
  exit
fi

function secure() {
  echo "Securing $1"
  htaccess="deny from all"
  echo $htaccess > $1/.htaccess
}

function ucommon() {
  cp common.php common2.php
  echo "Updating common.php..."
  sed s%$1%$2% common2.php > common.php
  rm -rf common2.php
}

function uconfig() {
  cp $PHCONFIG/config $PHCONFIG/config.bak
  echo "Updating config..."
  sed s%$1%$2% $PHCONFIG/config.bak > $PHCONFIG/config  
}

PHCONFIG="$HOME/.phorum"
PHADMIN=$USER"_admin"
PHHTACCESS="n"
PHDBSERVER="localhost"
PHDBNAME="$USER"
PHDBUSER="$USER"
PHDBPASS=""
PHEMAIL="$USER@$HOSTNAME"

echo
echo "This script will prepare Phorum.  Please answer the"
echo "following questions."
echo

echo "You need to move the Phorum configuration files out"
echo "of the web tree."
echo
echo -n "Phorum config path [$PHCONFIG]: "; read
if [ "$REPLY" != "" -a "$REPLY" != "$PHCONFIG" ]; then
  PHCONFIG=$REPLY
fi

echo
echo "You should also rename the admin directory."
echo -n "New admin path name [$PHADMIN]: "; read
if [ "$REPLY" != "" -a "$REPLY" != "$PHADMIN" ]; then
  PHADMIN=$REPLY
fi

echo
echo ".htaccess security works only with apache and may cause"
echo "internal server error if the server doesn't allow you"
echo "to change AuthConfig settings"
echo 
echo "If you get internal server error, ask your administrator"
echo "to \"AllowOveride AuthConfig\" for the directory your"
echo "phorum installation is in."
echo

echo -n "Do you want .htaccess security [$PHHTACCESS]? "; read
if [ "$REPLY" != "" -a "$REPLY" != "$PHHTACCESS" ]; then
  PHHTACCESS=$REPLY
fi

REPLY=""
echo
echo "You need to set the Master Password and email"
echo "address for the Phorum Admin."
echo
echo -n "Enter a password: "; read
PHPASSWORD=$REPLY
echo -n "Enter an email address [$PHEMAIL]: "; read
if [ "$REPLY" != "" -a "$REPLY" != "$PHEMAIL" ]; then
  PHEMAIL=$REPLY
fi

echo
echo "Please enter your database information."
echo -n "Server Name [$PHDBSERVER]: "; read
if [ "$REPLY" != "" -a "$REPLY" != "$PHDBSERVER" ]; then
  PHDBSERVER=$REPLY
fi
echo -n "Database Name [$PHDBNAME]: "; read
if [ "$REPLY" != "" -a "$REPLY" != "$PHDBNAME" ]; then
  PHDBNAME=$REPLY
fi
echo -n "User Name [$PHDBUSER]: "; read
if [ "$REPLY" != "" -a "$REPLY" != "$PHDBUSER" ]; then
  PHDBUSER=$REPLY
fi
echo -n "Password [$PHDBPASS]: "; read
if [ "$REPLY" != "" -a "$REPLY" != "$PHDBPASS" ]; then
  PHDBPASS=$REPLY
fi

echo
echo "Configuring Phorum...."
CMD='mkdir -p $PHCONFIG'
echo $CMD
$CMD
cp -v include/forums.php-dist $PHCONFIG/config
chmod -v 606 $PHCONFIG/config

uconfig \$Password=\' \$Password=\'$PHPASSWORD
uconfig \$dbName=\' \$dbName=\'$PHDBNAME
uconfig \$dbUser=\' \$dbUser=\'$PHDBUSER
uconfig \$dbPass=\' \$dbPass=\'$PHDBPASS
uconfig \$dbServer=\' \$dbServer=\'$PHDBSERVER
uconfig \$DefaultEmail=\' \$DefaultEmail=\'$PHEMAIL

chmod -v 606 $PHCONFIG/config.bak

ucommon \$inf_path=\"\./include \$inf_path=\"$PHCONFIG
ucommon forums\.php config
ucommon forums\.bak\.php config\.bak
ucommon \$admindir=\"admin \$admindir=\"$PHADMIN

cp -v include/header.php-dist include/header.php
cp -v include/footer.php-dist include/footer.php

mv -v admin $PHADMIN
chmod -v 707 $PHADMIN/forums

if [ $PHHTACCESS = "y" -o $PHHTACCESS = "Y" ]; then
  secure db
  secure docs
  secure include
  secure lang
  secure plugin
  secure scripts
  secure $PHADMIN/forums
  secure $PHADMIN/pages
fi

echo
echo "********************************************************************"
echo "*                                                                  *"
echo "* You still need to use the admin script to configure a few items. *"
echo "* Point your web browser to the admin script, select Phorum Setup, *"
echo "* and then select Files/Paths.  You should only need to hit Update *"
echo "* here.  This allows Phorum to auto detect the web site name and   *"
echo "* paths it will be using.                                          *"
echo "*                                                                  *"
echo "* After that please refer to the readme.txt for instructions on    *"
echo "* adding forums and folders.                                       *"
echo "********************************************************************"
echo
