<?php
////////////////////////////////////////////////////////////////////////////////
//                                                                            //
//   Copyright (C) 2000  Phorum Development Team                              //
//   http://www.phorum.org                                                    //
//                                                                            //
//   This program is free software. You can redistribute it and/or modify     //
//   it under the terms of the Phorum License Version 1.0.                    //
//                                                                            //
//   This program is distributed in the hope that it will be useful,          //
//   but WITHOUT ANY WARRANTY, without even the implied warranty of           //
//   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.                     //
//                                                                            //
//   You should have received a copy of the Phorum License                    //
//   along with this program.                                                 //
////////////////////////////////////////////////////////////////////////////////

/////////////////////////////////////////////////////////
  $admin=1;
  $fullaccess=true;
  chdir("../");
  include "common.php";
  if(isset($HTTP_POST_VARS)){
    if(isset($HTTP_POST_VARS["login"])){
      $login1=$HTTP_POST_VARS["login"];
    }
    elseif(isset($login1)){
      unset($login1);
    }
  }

  if(!isset($login1)) {
    $login1=0;
  }

  if(isset($page)) {
    $page=basename($page);
  }
  if(isset($num)) {
    $num=(int)$num;
  }

  $forum_id = 0;

  // Check that the page given is a forum level page if num given
  // otherwise people could bypass security by giving a num value
  // and a moderator password on say Master Settings.
  $modpages='managemenu,manage,quickedit,quickdel,edit,props,easyadmin,recentadmin,quickapp,datedel';
  if(!@strstr($modpages,$page)){
    $fullaccess=false;
  }
  if(isset($num) && $fullaccess==false && $login1!=1){
    $forum_id=$num;
    $num=0;
  }
  if($forum_url==""){
    $dir=dirname($PHP_SELF);
    $dir=dirname($dir);
    $forum_url="http://$HTTP_HOST$dir";
    if(substr($forum_url, -1)=="/") $forum_url=substr($forum_url, 0, -1);
  }

  if(empty($admin_url)){
    $myname="$forum_url/$admindir/$admin_page";
  }
  else{
    $myname="$admin_url/$admin_page";
  }

//  include "$admindir/login.php";
//  check_login();

  function writefile($forum='', $main=false) {
    GLOBAL $DB, $q, $inf_path,$inf_file,$inf_back,$down,$dbName,$dbUser,$dbPass,$dbServer,$DefaultDisplay,$DefaultEmail,$PhorumMailCode,$SortForums,$Password,$UseCookies;
    GLOBAL $forum_url,$admin_url,$ext,$forum_page,$list_page,$search_page,$read_page,$post_page,$violation_page,$down_page,$admindir;
    GLOBAL $default_table_width,$default_table_header_color,$default_table_header_font_color,$default_table_body_color_1,$default_table_body_font_color_1,$default_table_body_color_2,$default_table_body_font_color_2,$default_nav_color,$default_nav_font_color,$default_lang;
    GLOBAL $pho_main, $uploadDir, $uploadUrl;

    if($main!=false || $forum=='all' || $forum==''){
      if(@copy($inf_file, $inf_back)){
        QueMessage("Changes Saved.");
      }
      else{
        QueMessage("Changes saved but $inf_file could not be backed up.");
      }
      $x=0;

      $active_count='0';

      if(isset($q)){
        $sSQL="Select count(*) as cnt from ".$pho_main." where active=1";
        $q->query($DB, $sSQL);
        $rec=$q->getrow();

        if(!@empty($rec["cnt"])){
          $active_count="$rec[cnt]";
        }
      }

      $data="<?php\n";
      $data.="// DO NOT EDIT THIS FILE.  USE THE ADMIN\n\n";
      if($down==1){
        $data.="  if(\$admin!=true){\n";
        $data.="    Header(\"Location: $forum_url/$down_page.$ext\");\n";
        $data.="    exit();\n";
        $data.="  }\n";
        $data.="  \$down=1;\n\n";
      }
      $data.="// initialize database variables\n";
      $data.="  \$dbName='$dbName';\n";
      $data.="  \$dbUser='$dbUser';\n";
      $data.="  \$dbPass='$dbPass';\n";
      $data.="  \$dbServer='$dbServer';\n";
      $data.="\n";
      $data.="// create database classes\n";
      $data.="  if ( defined( \"_DB_LAYER\" ) && \$dbName!=''){\n";
      $data.="    \$DB = new db();\n";
      $data.="    \$DB->open(\$dbName, \$dbServer, \$dbUser, \$dbPass);\n";
      $data.="    \$q = new query(\$DB); //dummy query for generic operations\n";
      $data.="  }\n";
      $data.="\n";
      $data.="// master information\n";
      $data.="  \$Password='$Password';\n";
      $data.="  \$DefaultDisplay='1000';\n";
      $data.="  \$DefaultEmail='$DefaultEmail';\n";
      $data.="  \$PhorumMailCode='$PhorumMailCode';\n";
      $data.="  \$UseCookies='$UseCookies';\n";
      $data.="  \$SortForums='$SortForums';\n";
      $data.="  \$ActiveForums='$active_count';\n";
      $data.="\n";
      $data.="  \$forum_url='$forum_url';\n";
      $data.="  \$admin_url='$admin_url';\n";
      $data.="  \$uploadDir='$uploadDir';\n";
      $data.="  \$uploadUrl='$uploadUrl';\n";
      $data.="  \$ext='$ext';\n";
      $data.="  \$forum_page='$forum_page';\n";
      $data.="  \$list_page='$list_page';\n";
      $data.="  \$search_page='$search_page';\n";
      $data.="  \$read_page='$read_page';\n";
      $data.="  \$post_page='$post_page';\n";
      $data.="  \$violation_page='$violation_page';\n";
      $data.="  \$down_page='$down_page';\n";
      $data.="  \$default_lang='$default_lang';\n";
      $data.="  \$default_table_width='$default_table_width';\n";
      $data.="  \$default_table_header_color='$default_table_header_color';\n";
      $data.="  \$default_table_header_font_color='$default_table_header_font_color';\n";
      $data.="  \$default_table_body_color_1='$default_table_body_color_1';\n";
      $data.="  \$default_table_body_font_color_1='$default_table_body_font_color_1';\n";
      $data.="  \$default_table_body_color_2='$default_table_body_color_2';\n";
      $data.="  \$default_table_body_font_color_2='$default_table_body_font_color_2';\n";
      $data.="  \$default_nav_color='$default_nav_color';\n";
      $data.="  \$default_nav_font_color='$default_nav_font_color';\n";
      $data.="\n";
      $data.="?>";

      $fp = fopen("$inf_file", "w");
      fputs($fp, $data);
      fclose($fp);
    }

    if($forum!=''){
      $sSQL="Select * from ".$pho_main;
      if($forum!='all') $sSQL.=" where id=$forum";
      $q->query($DB, $sSQL);
      $rec=(object)$q->getrow();

      While(isset($rec->id)){
        if(!get_cfg_var("magic_quotes_runtime")){
          $rec->name = str_replace("'", "\\'", $rec->name);
          $rec->description = str_replace("'", "\\'", $rec->description);
          $rec->staff_host = str_replace("'", "\\'", $rec->staff_host);
        }
        $data ="<?php\n";
        $data.="  // $rec->name forum\n";
        $data.="  \$ForumId=$rec->id;\n";
        $data.="  \$ForumActive='$rec->active';\n";
        $data.="  \$ForumName='$rec->name';\n";
        $data.="  \$ForumDescription='$rec->description';\n";
        $data.="  \$ForumConfigSuffix='$rec->config_suffix';\n";
        $data.="  \$ForumFolder='$rec->folder';\n";
        $data.="  \$ForumParent='$rec->parent';\n";
        $data.="  \$ForumLang='$rec->lang';\n";
        if($rec->folder!="1"){
          $rec->folder="0";
        }
        if($rec->folder=="0"){
          $data.="  \$ForumDisplay='$rec->display';\n";
          $data.="  \$ForumTableName='$rec->table_name';\n";
          $data.="  \$ForumModeration='$rec->moderation';\n";
          $data.="  \$ForumModEmail='$rec->mod_email';\n";
          $data.="  \$ForumModPass='$rec->mod_pass';\n";
          $data.="  \$ForumEmailList='$rec->email_list';\n";
          $data.="  \$ForumEmailReturnList='$rec->email_return';\n";
          $data.="  \$ForumEmailTag='$rec->email_tag';\n";
          $data.="  \$ForumCheckDup='$rec->check_dup';\n";
          $data.="  \$ForumMultiLevel='$rec->multi_level';\n";
          $data.="  \$ForumCollapse='$rec->collapse';\n";
          $data.="  \$ForumFlat='$rec->flat';\n";
          $data.="  \$ForumStaffHost='$rec->staff_host';\n";
          $data.="  \$ForumAllowHTML='$rec->html';\n";
          $data.="  \$ForumAllowUploads='$rec->allow_uploads';\n";
          $data.="  \$ForumTableBodyColor2='$rec->table_body_color_2';\n";
          $data.="  \$ForumTableBodyFontColor2='$rec->table_body_font_color_2';\n";
        }
        $data.="  \$ForumTableWidth='$rec->table_width';\n";
        $data.="  \$ForumNavColor='$rec->nav_color';\n";
        $data.="  \$ForumNavFontColor='$rec->nav_font_color';\n";
        $data.="  \$ForumTableHeaderColor='$rec->table_header_color';\n";
        $data.="  \$ForumTableHeaderFontColor='$rec->table_header_font_color';\n";
        $data.="  \$ForumTableBodyColor1='$rec->table_body_color_1';\n";
        $data.="  \$ForumTableBodyFontColor1='$rec->table_body_font_color_1';\n";
        $data.="\n?>";
        $fp = fopen("$admindir/forums/$rec->id.php", "w");
        fputs($fp, $data);
        fclose($fp);
        $rec=(object)$q->getrow();
      }
    }
  }

  function get_html_value() {
    GLOBAL $html, $html_all,$html_style,$html_font,$html_li,$html_img,$html_a;

    if($html_all==1){
      $html="Y";
    }
    else{
      if($html_style==1){
        $html[]="b";
        $html[]="i";
        $html[]="u";
      }
      if($html_font==1) $html[]="font";
      if($html_li==1){
        $html[]="ol";
        $html[]="ul";
        $html[]="li";
      }
      if($html_img==1) $html[]="img";
      if($html_a==1) $html[]="a";
      if(is_array($html)){
        $html=implode("|", $html);
      }
      else{
        $html="N";
      }
    }
    return $html;
  }

  function QueMessage($m){
    GLOBAL $message;
    if($message)     $message.="\\n";
    $message.="$m";
  }

  function DropForum($f, $table) {
    GLOBAL $q, $DB, $admindir, $pho_main, $uploadDir;

    $sSQL = "Select count(id) as fornum from ".$pho_main." where table_name = '".$table."'";
    $q->query($DB, $sSQL);
    $rec=$q->getrow();
    if($rec["fornum"]==1) {
      $sSQL="DROP TABLE $table";
      $q->query($DB, $sSQL);
      $sSQL="DROP TABLE $table"."_bodies";
      $q->query($DB, $sSQL);
      $DB->drop_sequence($table);
    }
    $sSQL="Delete from ".$pho_main." where id = ".$f;
    $q->query($DB, $sSQL);
    @unlink("$admindir/forums/$f.php");

    // Remove attachments-dir.  Needs fix for portability.
    if (!empty($uploadDir) && !empty($table) && file_exists("$uploadDir/$table")) {
      @system("rm -rf $uploadDir/$table");
    }
  }

  function DropFolder($f){
    GLOBAL $q, $DB, $admindir, $pho_main;
    $sSQL="Select id, folder, table_name from ".$pho_main." where parent=$f";
    $q->query($DB, $sSQL);
    while($rec=$q->getrow()){
      if($rec["folder"]){
        DropFolder($rec["id"]);
      } else{
        DropForum($rec["id"], $rec["table_name"]);
      }
    }
    $sSQL="Delete from ".$pho_main." where parent=$f";
    $q->query($DB, $sSQL);
    $sSQL="Delete from ".$pho_main." where id=$f";
    $q->query($DB, $sSQL);
    @unlink("$admindir/forums/$f.php");

  }

  switch ($action) {
    case "pass":
      // Change the master password
      if($newPassword==$confirm){
        QueMessage("Password Changed.");
        $Password=$newPassword;
        writefile();
      } else {
        QueMessage("Passwords do not match.");
        $page=$frompage;
      }
      break;
    case "activate":
      // activate the current forum
      $sSQL="Update ".$pho_main." set active=1 where id=$num";
      $q->query($DB, $sSQL);
      $ForumActive=1;
      writefile($num, true);
      break;
    case "deactivate":
      // deactivate the current forum
      $sSQL="Update ".$pho_main." set active=0 where id=$num";
      $q->query($DB, $sSQL);
      $ForumActive=0;
      writefile($num, true);
      break;
    case "drop":
      // drop the current forum/folder
      if (isset($forum_id) && ($forum_id != 0)) {
        $sSQL = "Select table_name, folder from ".$pho_main." where id = ".$forum_id;
        $q->query($DB, $sSQL);
        $rec=$q->getrow();
        if($rec["folder"]==0) {
          DropForum($forum_id, $rec["table_name"]);
          QueMessage("Forum dropped.");
        } else {
          DropFolder($forum_id);
          QueMessage("Folder dropped.");
        }
        writefile();
      } else {
        QueMessage("Drop failed.  Forum ID not available.");
      }
      break;
    case "seq":
      if($num!=0){
        $table_name=$ForumTableName;
      }
      else{
        $table_name=$pho_main;
      }
      $sSQL="Select max(id) as id from ".$table_name;
      $q->query($DB, $sSQL);
      $row=$q->getrow();
      if(isset($row["id"])){
        $id=$row["id"];
        $ret=$DB->reset_sequence($table_name, $id+1);
        if($ret==0){
          QueMessage("Sequence reset to $id.");
        }
        else{
          QueMessage("There was an error resetting the sequence.");
        }
      }
      else{
        QueMessage("Sequence not set, forum empty.");
      }
      break;
    case "stop":
      $down=1;
      writefile();
      QueMessage("Phorum has been stopped.");
      break;
    case "start":
      $down=0;
      writefile();
      $step=0;
      QueMessage("Phorum has been started.");
      break;
    case "build":
      writefile("all");
      QueMessage("All files rebuilt.");
      break;
    case "db":
      $dbName=$new_dbName;
      $dbUser=$new_dbUser;
      $dbPass=$new_dbPass;
      $dbServer=$new_dbServer;
      writefile();
      QueMessage("The Database Settings have been updated.");
      break;
    case "global":
      $DefaultDisplay=$new_DefaultDisplay;
      $DefaultEmail=$new_DefaultEmail;
      $PhorumMailCode=$new_PhorumMailCode;
      $UseCookies=$new_UseCookies;
      $SortForums=$new_SortForums;
      $default_lang=$new_default_lang;
      writefile();
      QueMessage("The Global properties have been updated.");
      break;
    case "files":
      if(substr($new_forum_url, -1)=="/") $new_forum_url=substr($new_forum_url, 0, -1);
      $forum_url=$new_forum_url;
      if(substr($new_admin_url, -1)=="/") $new_admin_url=substr($new_admin_url, 0, -1);
      $admin_url=$new_admin_url;
      $ext=$new_ext;
      $uploadDir = $new_uploadDir;
      $uploadUrl = $new_uploadUrl;
      if (!file_exists($uploadDir) && !empty($new_uploadDir)) {
        if(!@mkdir($uploadDir, 0777))
          QueMessage("The Upload Dir '$new_uploadDir' could not be created.");
      }
      $forum_page=$new_forum_page;
      $list_page=$new_list_page;
      $search_page=$new_search_page;
      $read_page=$new_read_page;
      $post_page=$new_post_page;
      $violation_page=$new_violation_page;
      $down_page=$new_down_page;
      writefile();
      QueMessage("The Files/Paths settings have been updated.");
      break;
    case "html":
      $default_table_width=$new_default_table_width;                                                                                                                                                                                                                                                                                   $default_table_width=$new_default_table_width;
      $default_table_header_color=$new_default_table_header_color;
      $default_table_header_font_color=$new_default_table_header_font_color;
      $default_table_body_color_1=$new_default_table_body_color_1;
      $default_table_body_font_color_1=$new_default_table_body_font_color_1;
      $default_table_body_color_2=$new_default_table_body_color_2;
      $default_table_body_font_color_2=$new_default_table_body_font_color_2;
      $default_nav_color=$new_default_nav_color;
      $default_nav_font_color=$new_default_nav_font_color;
      writefile();
      QueMessage("The HTML properties have been updated.");
      break;
    case "edit":
      if(!get_magic_quotes_gpc()){
        $author = addslashes($author);
        $email = addslashes($email);
        $subject = addslashes($subject);
        $attachment = @addslashes($attachment);
        $body = addslashes($body);
      }
      if (!empty($del_attachment) && $del_attachment) {
        unlink($uploadDir.'/'.$ForumTableName.'/'.$attachment);
        $attachment = '';
      }
      $sSQL="Update $ForumTableName set author='$author', email='$email', subject='$subject', attachment='$attachment' where id=$id";
      $q->query($DB, $sSQL);
      $sSQL="Update ".$ForumTableName."_bodies set body='$body' where id=$id";
      $q->query($DB, $sSQL);
      QueMessage("Le message ayant pour sujet : \"$subject\" a �t� modifi�");
      break;
    case "del":
      // suppression de la table forum_lecture
      $sSQL = "DELETE FROM forum_lecture where forlec_topic_no='$id' AND forlec_forum_no='$num'";
      $q->query($DB, $sSQL);
      if($type=="quick"){
        $sSQL = "Select id from $ForumTableName where id in ($id) and id=thread";
        $q->query($DB, $sSQL);
        $rec=$q->getrow();
        $delthreads='';
        While($rec){
          if(!empty($delthreads)) $delthreads.=',';
          $delthreads.=$rec["id"];
          $rec=$q->getrow();
        }
        if(strstr($id, ",")){
          $ids=explode(",", $id);
        }
        else{
          $ids[0]=$id;
        }
        $cnt=count($ids);
        $delids='';
        for($x=0;$x<$cnt;$x++){
          if(!strstr($delthreads, $ids[$x])){
            if(!empty($delids)) $delids.=',';
            $delids.=$ids[$x];
          }
        }
      }
      else{
        if(is_array($threadlist)){
          $delthreads=implode(",", $threadlist);
        }
        else{
          $delthreads="";
        }
        if(is_array($idlist)){
          $delids=implode(",", $idlist);
        }
        else{
          $delids="";
        }
      }
      if(!empty($delids)){
        $sSQL = "SELECT id, attachment, parent FROM $ForumTableName WHERE id in ($delids)";
        $q->query($DB, $sSQL);
        $rec = $q->getRow();
        // Delete attachments
        While($rec){
          $parents[$rec["id"]]=$rec["parent"];
          if (!empty($rec["attachment"])) {
            if (file_exists($uploadDir.'/'.$ForumTableName.'/'.$rec["attachment"])) {
              unlink($uploadDir.'/'.$ForumTableName.'/'.$rec["attachment"]);
            }
          }
          $rec=$q->getRow();
        }
        $ids=explode(",", $delids);
        while(list($key, $id)=each($ids)){
          $sSQL = "Update $ForumTableName set parent=$parents[$id] where parent=$id";
          $q->query($DB, $sSQL);
        }
        $sSQL = "Delete from $ForumTableName where id in ($delids)";
        $q->query($DB, $sSQL);
        $sSQL = "Delete from ".$ForumTableName."_bodies where id in ($delids)";
        $q->query($DB, $sSQL);
        QueMessage("Le message n�$delids a �t� supprim�<br>");
      }
      if($delthreads){
        $sSQL = "Select attachment FROM $ForumTableName WHERE thread in ($delthreads)";
        $q->query($DB, $sSQL);
        $rec = $q->getRow();
        While($rec){
          if (!empty($rec["attachment"])) {
            if (file_exists($uploadDir.'/'.$ForumTableName.'/'.$rec["attachment"])) {
              unlink($uploadDir.'/'.$ForumTableName.'/'.$rec["attachment"]);
            }
          }
          $rec=$q->getRow();
        }
        $sSQL = "Delete from $ForumTableName where thread in ($delthreads)";
        $q->query($DB, $sSQL);
        $sSQL = "Delete from ".$ForumTableName."_bodies where thread in ($delthreads)";
        $q->query($DB, $sSQL);
        $message.="Ensemble de messages $delthreads effac� !";
      }
      break;
    case "datedel":
      if($dateopt=="="){
        $cond="LIKE '$date%'";
      }
      else{
        $cond="$dateopt '$date'";
      }
      $sSQL="Select thread from $ForumTableName where thread=id and datestamp $cond";
      $q->query($DB, $sSQL);
      if($err=$q->error()){
        QueMessage("$err<br>$sSQL");
      }
      elseif($q->numrows()!=0){
        $rec=$q->getrow();
        $threads='';
        while(is_array($rec)){
          if($threads!='') $threads.=", ";
          $threads.="$rec[thread]";
          $rec=$q->getrow();
        }
        $sSQL="Select count(*) as cnt from $ForumTableName where thread in ($threads)";
        $q->query($DB, $sSQL);
        $rec=$q->getrow();
        $count=$rec["cnt"];
        if($err=$q->error()){
          QueMessage("$err<br>$sSQL");
        }
        $sSQL="Delete from $ForumTableName where thread in ($threads)";
        $q->query($DB, $sSQL);
        if($err=$q->error()){
          QueMessage("$err<br>$sSQL");
        }
        else{
          QueMessage("$count message(s) deleted.");
          $sSQL="Delete from $ForumTableName"."_bodies where thread in ($threads)";
          $q->query($DB, $sSQL);
          if($err=$q->error()){
            QueMessage("Problem deleting bodies!");
            QueMessage("$err<br>$sSQL");
          }
        }
      }
      else{
        QueMessage("No messages selected for deletion.");
      }
      break;
    case "add":
      if(get_magic_quotes_gpc()){
        $name = stripslashes($name);
        $description = stripslashes($description);
        if(!$folder) $staff_host = stripslashes($staff_host);
      }

      $name = trim($name);
      $table = trim($table);

      if(empty($name)){
        $err = "You must provide a name for the forum.";
      } elseif( !$folder && ( $mod_pass=="" || $mod_pass!=$mod_pass_2) ) {
        $err = "Either you did not provide a password or the passwords did not match.";
      } else {
        $sSQL = "Select max(id) as m_id from ".$pho_main;
        $q->query($DB, $sSQL);
        if ($q->error()) {
          create_table($DB, "forums", $pho_main);
          $sSQL = "Select max(id) as m_id from ".$pho_main;
          $q->query($DB, $sSQL);
          if ($q->error()) {
            $err = "Problem getting maximum Forum ID.\n  Did set up your database under Phorum Setup->Database Settings? (".$q->error().")";
          } elseif (!$folder) {
            if (empty($table)) {
              $err = "The table name needs to be filled in.";
            } elseif ($table == $pho_main) {
              $err = "Naming conflict with your main table (".$pho_main.").";
            } elseif(!$table_exists && !$folder) {
              $err=create_table($DB, "main", $table);
            }
          }
        } elseif (!$folder) {
          if (empty($table)) {
            $err = "The table name needs to be filled in.";
          } elseif ($table == $pho_main) {
            $err = "Naming conflict with your main table (".$pho_main.").";
          } elseif(!$table_exists && !$folder) {
            $err=create_table($DB, "main", $table);
          }
        }
      }

      if ($err=="") {
        $id=$DB->nextid($pho_main);
        if($id==0 && $DB->type!="mysql"){
          if(!$table_exists){
            $q->query($DB, "drop table $table");
            $q->query($DB, "drop table $table"."_bodies");
          }
          $page=$frompage;
          QueMessage("Could not get an id for the new forum.\nCheck your database settings.");
        } else {
          $name = addslashes($name);
          $description = addslashes($description);
          if(!$folder) $staff_host = addslashes($staff_host);
          if(!$folder) {
            $html=get_html_value();
            $sSQL="Insert into ".$pho_main." (id, name, active, description, config_suffix, folder, parent, display, table_name, moderation, mod_email, mod_pass, email_list, email_return, email_tag, check_dup, multi_level, collapse, flat, staff_host, lang, html, table_width, table_header_color, table_header_font_color, table_body_color_1, table_body_color_2, table_body_font_color_1, table_body_font_color_2, nav_color, nav_font_color, allow_uploads) values ($id, '$name', 1, '$description', '$config_suffix', $folder, $parent, $display, '$table', '$moderation', '$mod_email', '$mod_pass', '$email_list', '$email_return', '$email_tag', $check_dup, $multi_level, $collapsed, $rflat, '$staff_host', '$lang', '$html', '$table_width', '$table_header_color', '$table_header_font_color', '$table_body_color_1', '$table_body_color_2', '$table_body_font_color_1', '$table_body_font_color_2', '$nav_color', '$nav_font_color','$allow_uploads')";
          } else {
            $sSQL="Insert into ".$pho_main." (id,name,active,description,config_suffix,lang,folder,parent,table_width,table_header_color,table_header_font_color,table_body_color_1,table_body_font_color_1,nav_color,nav_font_color) values ('$id', '$name', 0, '$description', '$config_suffix', '$lang', '$folder', '$parent', '$table_width', '$table_header_color', '$table_header_font_color', '$table_body_color_1', '$table_body_font_color_1', '$nav_color', '$nav_font_color')";
          }
          $q->query($DB, $sSQL);
          $err=$q->error();
          if($err==""){
            if($DB->type=="mysql"){
              $id=$DB->lastid();
            }
            writefile($id,true);
            if(get_magic_quotes_gpc()) $name=stripslashes($name);
            if ($uploadDir != '' && !empty($allow_uploads) && $allow_uploads == 'Y') {
              mkdir($uploadDir.'/'.$table, 0777);
            }
            QueMessage("$name created [id: $id]");
            $num=$id;
            include "$admindir/forums/$num.php";
          } else {
            $name = stripslashes($name);
            $description = stripslashes($description);
            if(!$folder) $staff_host = stripslashes($staff_host);
            if(!$table_exists){
              $q->query($DB, "drop table $table");
              $q->query($DB, "drop table $table"."_bodies");
            }
            QueMessage("Could not add forum to the main table (".$pho_main.").  Database error: $err.<br>$sSQL");
            $page=$frompage;
          }
        }
      } else {
        QueMessage($err);
        $page=$frompage;
      }
      break;
    case "props":
      if($name!=""){
        if($mod_pass!=$mod_pass_2){
          QueMessage("Either you did not provide a password or the passwords did not match.");
          $page=$frompage;
        }
        else{
          if(!$ForumFolder){
            if($mod_pass=="") $mod_pass=$ForumModPass;
          }
          if(!get_magic_quotes_gpc()){
            $name = addslashes($name);
            $description = addslashes($description);
            $staff_host = addslashes($staff_host);
          }
          if(!$ForumFolder) {
            $html=get_html_value();
            $sSQL="Update ".$pho_main." set name='$name', description='$description', config_suffix='$config_suffix', parent=$parent, display=$display, moderation='$moderation', mod_email='$mod_email', mod_pass='$mod_pass', email_list='$email_list', email_return='$email_return', email_tag='$email_tag', check_dup=$check_dup, multi_level=$multi_level, collapse=$collapsed, flat=$rflat, staff_host='$staff_host', lang='$lang', html='$html', table_width='$table_width', table_header_color='$table_header_color', table_header_font_color='$table_header_font_color', table_body_color_1='$table_body_color_1', table_body_color_2='$table_body_color_2', table_body_font_color_1='$table_body_font_color_1', table_body_font_color_2='$table_body_font_color_2', nav_color='$nav_color', nav_font_color='$nav_font_color', allow_uploads='$allow_uploads' where id=$num";
          } else {
            $sSQL="Update ".$pho_main." set name='$name', description='$description', config_suffix='$config_suffix', lang='$lang', parent=$parent, table_width='$table_width', table_header_color='$table_header_color', table_header_font_color='$table_header_font_color', table_body_color_1='$table_body_color_1', table_body_font_color_1='$table_body_font_color_1', nav_color='$nav_color', nav_font_color='$nav_font_color' where id=$num";
          }
          $q->query($DB, $sSQL);
          $err=$q->error();
          if($err==""){
            if ($uploadDir != '' && !empty($allow_uploads) && $allow_uploads == 'Y') {
              mkdir($uploadDir.'/'.$table, 0777);
            }
            $ForumName=stripslashes($name);
            writefile($num);
            QueMessage("$ForumName has been updated.");
          }
          else{
            QueMessage($err);
            $page=$frompage;
          }
        }
      }
      else{
        QueMessage("You must provide a name for the forum.");
        $option=="edit_prop";
      }
      break;
    case "moderate":
      if($approved=='Y'){
        $approved='N';
        $word = "masqu�";
      }
      elseif($approved=='N'){
        $approved='Y';
        $word = "d�masqu�";
      }
       $sSQL = "SELECT subject FROM $ForumTableName WHERE id =$id";
        $q->query($DB, $sSQL);
        $rec = $q->getRow();
      $sSQL="Update $ForumTableName set approved='$approved' where id=$id";
      $q->query($DB, $sSQL);
      $err=$q->error();
      if($err=="")
      {
        QueMessage("Message [".$rec[subject]."] $word.");
      }
      else
      {
        QueMessage($err);
      }
      break;
    case "version":
      $data="";
      flush();
      $fp=fopen("http://phorum.org/version.php", "r");
      if($fp){
        $data=fgets($fp, 1024);
        fclose($fp);
      }
      if(!strstr($data, "|")){
        QueMessage("Could not contact phorum.org.  To use this feature, you must have compiled in fopen wrappers when setting up PHP.");
        $page="main";
      }
      else{
        $ver_arr=explode("|", $data);
        $page="version";
      }
      break;
  }
  include "$admindir/header.php";
  // If you add pages to the admin that operate at a forum level you need
  // to be sure and add them to the $modpages at the top of this script.
  if($page=="newforum")
  {
    $page="new";
    $folder="0";
  }
  elseif($page=="newfolder")
  {
    $page="new";
    $folder="1";
  }
  if ($message)
      echo notifier($message);
  if($page)
  {
    include "$admindir/pages/$page.php";
  }
  else
  {
    QueMessage("Sorry, this page is not available when logged in as a forum moderator.");
  }
  include "$admindir/footer.php";
?>