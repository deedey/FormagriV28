<?php

if ( !defined( "_DB_LAYER" ) ){
  define("_DB_LAYER", 1 );

if(!function_exists("mysql_connect"))
  echo "<b>Error: Mysql support is not available on this server.</b>";

class db {

  var $connect_id;
  var $type;
  var $database;

  function db($database_type="mysql") {
    $this->type=$database_type;
  }

  function open($database, $host, $user, $password) {
    if(empty($user)){
      $this->connect_id=@mysql_pconnect();
    }
    else{
      $this->connect_id=@mysql_pconnect($host, $user, $password);
    }

    if ($this->connect_id) {
      $this->database=$database;
      return $this->connect_id;
    }
    else{
      return 0;
    }
  }

  function drop_sequence($sequence){
    $esequence=$sequence."_seq";
    $sSQL="DROP TABLE $esequence";
    $query=new query($this, $sSQL);
    return $query->error();
  }

  function reset_sequence($sequence, $newval){
    $this->nextid($sequence);
    $esequence=$sequence."_seq";
    $sSQL="Replace into $esequence values ('', $newval)";
    $query=new query($this, $sSQL);
    return $query->error();
  }

  function lastid(){
    return mysql_insert_id();
  }

  function nextid($sequence) {
   /*this is all poppy cock and was a bad idea.
  // Function returns the next available id for $sequence, if it's not
  // already defined, the first id will start at 1.
  // This function will create a table for each sequence called
  // '{sequence_name}_seq' in the current database.
    $esequence=ereg_replace("'","''",$sequence)."_seq";
    $query=new query($this, "Select * from $esequence limit 1");
    $query->query($this, "REPLACE INTO $esequence values ('', nextval+1)");
    if ($query->result) {
      $result=@mysql_insert_id($this->connect_id);
    } else {
      $query->query($this, "CREATE TABLE $esequence ( seq char(1) DEFAULT '' NOT NULL, nextval bigint(20) unsigned DEFAULT '0' NOT NULL auto_increment, PRIMARY KEY (seq), KEY (nextval) )");
      $query->query($this, "REPLACE INTO $esequence values ('', nextval+1)");
      $result=@mysql_insert_id($this->connect_id);
    }
    return $result;
    
    */
    return "0";
  }

  function close() {
  // Closes the database connection and frees any query results left.

    if ($this->query_id && is_array($this->query_id)) {
      while (list($key,$val)=each($this->query_id)) {
        @mysql_free_result($val);
      }
    }
    $result=@mysql_close($this->connect_id);
    return $result;
  }

};

/************************************** QUERY ***************************/

class query {

  var $result;
  var $row;

  function query(&$db, $query="") {
  // Constructor of the query object.
  // executes the query

    if($query!=""){
      $this->result=@mysql_db_query($db->database, $query, $db->connect_id);
      return $this->result;
    }
  }

  function getrow() {
    $this->row=@mysql_fetch_array($this->result, MYSQL_ASSOC);
    return $this->row;
  }

  function numrows() {
  // Gets the number of rows returned in the query

    $result=@mysql_num_rows($this->result);
    return $result;
  }

  function error() {
  // Gets the last error message reported for this query

    $result=@mysql_error();
    return $result;
  }

  function field($field, $row="-1") {
  // get the value of the field with name $field
  // in the current row or in row $row if supplied

    if($row!=-1){
      $result=@mysql_result($this->result, $row, $field);
    }
    else{
      $result=$this->row[$field];
    }

    if(isset($result)){
      return $result;
    }
    else{
      return '0';
    }
  }

  function firstrow() {
  // return the current row pointer to the first row
  // (CAUTION: other versions may execute the query again!! (e.g. for oracle))

    $result=@mysql_data_seek($this->result,0);
    if($result){
      $result=$this->getrow();
      return $this->row;
    }
    else{
      return 0;
    }
  }

  function seek($row){
    @mysql_data_seek($this->result, $row);
  }

  function free() {
  // free the mysql result tables

    return @mysql_free_result($this->result);
  }

}; // End class

// Custom Create Table Section

  function create_table(&$DB, $table, $table_name){
    GLOBAL $q;
    if($table=="main"){
      $sSQL="CREATE TABLE $table_name (id bigint(20) unsigned DEFAULT '0' NOT NULL, datestamp datetime DEFAULT '0000-00-00 00:00:00' NOT NULL, thread int(11) DEFAULT '0' NOT NULL, parent int(11) DEFAULT '0' NOT NULL, author char(37) DEFAULT '' NOT NULL, subject char(255) DEFAULT '' NOT NULL, email char(200) DEFAULT '' NOT NULL, attachment char(64) DEFAULT '' NOT NULL, host char(50) DEFAULT '' NOT NULL, email_reply char(1) NOT NULL DEFAULT 'N', approved char(1) NOT NULL DEFAULT 'N', msgid char(100) DEFAULT '' NOT NULL, PRIMARY KEY (id), KEY author (author), KEY datestamp (datestamp), KEY subject (subject), KEY thread (thread), KEY parent (parent), KEY approved (approved), KEY msgid (msgid))";
//      echo "\n<!--$sSQL-->\n";
      $q->query($DB, $sSQL);
      if(!$q->error()){
        $sSQL="CREATE TABLE ".$table_name."_bodies (id bigint(20) unsigned DEFAULT '0' NOT NULL AUTO_INCREMENT, body text DEFAULT '' NOT NULL, thread int(11) DEFAULT '0' NOT NULL, PRIMARY KEY (id), KEY thread (thread))";
//        echo "\n<!--$sSQL-->\n";
        $q->query($DB, $sSQL);
        if($q->error()){
          $errormsg = $q->error();
          $sSQL="DROP TABLE ".$table_name;
//          echo "\n<!--$sSQL-->\n";
          $q->query($DB, $sSQL);
          return $errormsg;
        } else {
          return "";
        }
      } else {
        return $q->error();
      }
    }
    elseif($table=="forums"){
      $sSQL="CREATE TABLE ".$table_name." (
        id int(11) DEFAULT '0' NOT NULL AUTO_INCREMENT,
        name char(50) DEFAULT '' NOT NULL,
        active smallint DEFAULT 0 NOT NULL,
        description char(255) DEFAULT '' NOT NULL,
        config_suffix char(50) DEFAULT '' NOT NULL,
        folder char(1) DEFAULT '0' NOT NULL,
        parent int DEFAULT 0 NOT NULL,
        display int DEFAULT 0 NOT NULL,
        table_name char(50) DEFAULT '' NOT NULL,
        moderation char(1) DEFAULT 'n' NOT NULL,
        mod_email char(50) DEFAULT '' NOT NULL,
        mod_pass char(50) DEFAULT '' NOT NULL,
        email_list char(50) DEFAULT '' NOT NULL,
        email_return char(50) DEFAULT '' NOT NULL,
        email_tag char(50) DEFAULT '' NOT NULL,
        check_dup smallint DEFAULT 0 NOT NULL,
        multi_level smallint DEFAULT 0 NOT NULL,
        collapse smallint DEFAULT 0 NOT NULL,
        flat smallint DEFAULT 0 NOT NULL,
        staff_host char(50) DEFAULT '' NOT NULL,
        lang char(50) DEFAULT '' NOT NULL,
        html char(40) DEFAULT 'N' NOT NULL,
        table_width char(4) DEFAULT '' NOT NULL,
        table_header_color char(7) DEFAULT '' NOT NULL,
        table_header_font_color char(7) DEFAULT '' NOT NULL,
        table_body_color_1 char(7) DEFAULT '' NOT NULL,
        table_body_color_2 char(7) DEFAULT '' NOT NULL,
        table_body_font_color_1 char(7) DEFAULT '' NOT NULL,
        table_body_font_color_2 char(7) DEFAULT '' NOT NULL,
        nav_color char(7) DEFAULT '' NOT NULL,
        nav_font_color char(7) DEFAULT '' NOT NULL,
        allow_uploads char(1) DEFAULT 'N' NOT NULL,
        PRIMARY KEY (id),
        KEY (name),
        KEY (active),
        KEY (parent) )";
//      echo "\n<!--$sSQL-->\n";
      $q->query($DB, $sSQL);
      return $q->error();
    }
  }
}
?>