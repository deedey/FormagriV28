<xmp>
<?php
// Please note that this script only works for upgrading from 3.1 or newer

  chdir("../");
  include "common.php";

  echo "Altering table $pho_main\n";
  flush();
  $SQL="ALTER TABLE $pho_main change id id int(11) DEFAULT '0' NOT NULL AUTO_INCREMENT";
  $q->query($DB, $SQL);
  $SQL="ALTER TABLE $pho_main ADD allow_uploads char(1) DEFAULT 'N' NOT NULL";
  $q->query($DB, $SQL);
  $SQL="ALTER TABLE $pho_main ADD email_list char(50) DEFAULT '' NOT NULL after mod_pass";
  $q->query($DB, $SQL);
  $SQL="ALTER TABLE $pho_main ADD email_return char(50) DEFAULT '' NOT NULL after email_list";
  $q->query($DB, $SQL);
  $SQL="ALTER TABLE $pho_main ADD email_tag char(50) DEFAULT '' NOT NULL after email_return";
  $q->query($DB, $SQL);
  $SQL="ALTER TABLE $pho_main ADD config_suffix char(50) DEFAULT '' NOT NULL after description";
  $q->query($DB, $SQL);

  $SQL="Select id, name, table_name from $pho_main";
  $query = new query($DB, $SQL);

  $rec=$query->getrow();

  while(is_array($rec)){
    echo "Altering tables for $rec[name]\n";
    flush();
    $SQL="ALTER TABLE $rec[table_name]_bodies CHANGE id id bigint(20) unsigned DEFAULT '0' NOT NULL auto_increment";
    $q->query($DB, $SQL);
    $SQL="ALTER TABLE $rec[table_name] ADD attachment char(64) DEFAULT '' NOT NULL AFTER email";
    $q->query($DB, $SQL);
    $SQL="ALTER TABLE $rec[table_name] ADD msgid char(100) DEFAULT '' NOT NULL, ADD KEY msgid (msgid)";
    $q->query($DB, $SQL);
    $rec=$query->getrow();
  }
?>
</xmp>
