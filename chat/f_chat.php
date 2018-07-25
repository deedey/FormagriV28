<?php
if (!isset($_SESSION)) session_start();
if ($lg == "ru")
{
  $code_langage = "ru";
  $charset = "Windows-1251";
  putenv("TZ=Europe/Moscow");
}
elseif ($lg == "fr")
{
  $code_langage = "fr";
  $charset = "iso-8859-1";
  putenv("TZ=Europe/Paris");
}
elseif ($lg == "en"){
  $code_langage = "en";
  $charset = "iso-8859-1";
}
?>
<HTML>
<HEAD>
<META HTTP-EQUIV="Content-Type" content="text/html; charset=<?php echo $charset;?>">
<META HTTP-EQUIV="Content-Language" CONTENT="<?php echo $code_langage ;?>">
<META HTTP-EQUIV="Expires" CONTENT="Fri, Jan 01 1900 00:00:00 GMT">
<META NAME="ROBOTS" CONTENT="No Follow">
<META HTTP-EQUIV="Cache-Control" CONTENT="no-cache, must-revalidate">
<META HTTP-EQUIV="Pragma" CONTENT="no-cache">
<link REL='StyleSheet' TYPE='text/css' HREF='chat.css'>
<TITLE><?php print($param["title"]); ?></TITLE>
</HEAD>
<BODY>
<?php
include "param.php";
require ("../fonction.inc.php");
require ("../fonction_html.inc.php");

function p4_pluriel($nb)
{
  if ($nb>1) 
  {
     return "s";
  }
  else
  {
     return "";
  }
}


// CONNEXION
function P4_PrintConnect($login) 
{
  global $param;
  print("<FORM action='index.php' method=post>");
  $nb=p4_GetNbConnect();
  if ($nb==0) 
  {
    print($param["text_nobody"]);
  }
  if ($nb==1) 
  {
    print($param["text_one_people"]);
  }
  if ($nb>1) 
  {
    print(str_replace("(nb)",$nb,$param["text_more"]));
  }
  print("<BR><BR>".$param["text_form_user"]." : <INPUT type=text name=user value=$login size=10><BR>");
  //print("Votre password : <INPUT type=password name=pass><BR>");
  print("<SCRIPT language=javascript>
         var nb_essai=0;
            function Connect(f) {
               if ((f.user.value!='')&&(nb_essai==0)) {
                   f.bouton.value=\"".$param["text_form_connecting"]."\";
                   f.submit();
                   nb_essai++;
               }
            }
         </SCRIPT>");
  print("<INPUT type=hidden name=pass><BR>");
  print("<INPUT type=button name=bouton value='".$param["text_form_submit"]."' onClick='Connect(this.form)'><BR>");
  print("</FORM><BR>");
}

function P4_Connect($user,$pass,$message) 
{
  global $param,$connect,$pass;
  // Connexion des users enregistrés et des invités
  $msg="";  $ok=1;
  P4_CheckConnect();
  if ($pass=="") 
  { //mode invité
     $query="SELECT * FROM ".$param["table_user"]." WHERE login='$user'";
     $result=mysql_query($query);
     if (mysql_num_rows($result)>0) 
     {
        $msg=$param["text_form_bad_pwd"];
        $ok=0;
     }
     if ($ok==1) 
     {
        $query="SELECT * FROM ".$param["table_salle"]."";
        $result=mysql_query($query);
        if (mysql_num_rows($result)>=$param["chat_maxi_connect"]) 
        {
           $msg=$param["text_form_max"];
           $ok=0;
        }
     }
/*    if ($ok==1) {
      $query="SELECT * FROM ".$param["table_salle"]." WHERE user='$user'";
      $result=mysql_query($query);
      if (mysql_num_rows($result)>0) {
        $msg=$param["text_form_user_in"];
        $ok=0;
      }
    }
*/    if ($ok==0) 
      {
         print($msg."<BR>");
         P4_PrintConnect();
      }
  } 
  else 
  { // User enregistré
    $query="SELECT * FROM ".$param["table_user"]." WHERE login='$user' AND password='$pass'";
    $result=mysql_query($query);
    if (mysql_num_rows($result)<0) 
    {
      print($param["text_error_connect"]."<BR>");
      P4_PrintConnect();
    }
  }
  if ($ok==1) 
  { // Connexion possible
    $today=date("YmdHis");
    $query="UPDATE ".$param["table_admin"]." SET dt_last_liste=$today";
    $result=mysql_query($query);
    $query="INSERT INTO ".$param["table_salle"]." (user,user_ID,dt_first) VALUES ('$user','$user',$today)";
    $result=mysql_query($query);
    $nom=GetDataField ($connect,"select util_nom_lb from utilisateur where util_login_lb='$user'","util_nom_lb");
    P4_Add_Msg("$nom ".$param["join_us"]."","","$user","");
//    print($param["text_connection_ok"]." <B>$user</B><BR><BR>");
    $leCours ='usager`|0|0|0|0';
    $course =  base64url_encode($leCours);
    print("<SCRIPT language=javascript>");
    print("window.open('connect.php?user=$user&dummy=$today&message=$message".
          TinCanTeach ('usager|0|0|0|0','connect.php?user='.$user.'&dummy='.$today.'&message='.$message,'http://formagri.com/Chat')."','','width=550,height=330,resizable=yes,status=no');");
    print("</SCRIPT>");
  }
}


function P4_Coeur_Liste($dt_last_liste,$user) 
{
  global $connect,$param;
  $query="SELECT DATE_FORMAT(dt_last_liste,'%Y%m%d%H%i%s') AS dt_last_l, DATE_FORMAT(dt_last_admin,'%Y%m%d%H%i%s') AS dt_last_admin FROM ".$param["table_admin"];
  $retour="";
  $result=mysql_query($query);
  $row=mysql_fetch_object($result);
  $today=date("YmdHis");
  $dt_last=$row->dt_last_l;
  $dt_admin=$row->dt_last_admin;
  $query="UPDATE ".$param["table_salle"]." SET dt='$today' WHERE user_ID='$user'";
  $result=mysql_query($query);

  P4_Coeur_Admin($dt_admin);
  //print("<BR>".$dt_last."<br>");
  if ($dt_last>=$dt_last_liste) 
  {  // Rechercher les users connectés
    $query="SELECT * FROM ".$param["table_salle"]." WHERE user<>'$user' ORDER BY dt_first DESC";
    $result=mysql_query($query);
    $nom_util=GetDataField ($connect,"select util_nom_lb from utilisateur where util_login_lb='$user'","util_nom_lb");
    $Z=" - <B>$nom_util</B><BR>";
    $nb=1;
    while ($row=mysql_fetch_object($result)) 
    {
      $ligne_user=$row->user;
      $nom_user=GetDataField ($connect,"select util_nom_lb from utilisateur where util_login_lb='$ligne_user'","util_nom_lb");
      $prenom_user=GetDataField ($connect,"select util_prenom_lb from utilisateur where util_login_lb='$ligne_user'","util_prenom_lb");
      $nom =$nom_user." ".$prenom_user;
      $Z.=" - <A href='javascript:top.AddUser(\\\"".$nom."\\\")'>".$nom."</A><BR>";
      $nb++;
    }
    $pluriel=p4_pluriel($nb);
    $retour.="laliste=\"".$nb." ".$param["text_nb_connect"].$pluriel."<BR><BR>$Z\"\n";
    $retour.="parent.frames['liste'].document.getElementById('layerliste').innerHTML=laliste\n";
    $retour.="top.Popup()\n";
  }
  $retour.="top.dt_last_liste=".$today.";\n";
  return $retour;
}

function P4_Coeur_Admin($dt_last_admin) 
{
  // Gère l'admin des connectés
  global $param;
  $moinsdelai=date("YmdHis",mktime(date("H"),date("i"),date("s")-$param["delai_admin"],date("m"),date("d"),date("Y")));
  if ($moinsdelai>=$dt_last_admin) 
  { // Gérer l'admin des users
    P4_CheckConnect();
  }
}

function p4_CheckConnect() 
{ // Déconnecte les users trop anciens
  global $user,$connect,$param;
  $Hlimite=date("YmdHis",mktime(date("H"),date("i"),date("s")-$param["delai_connect"],date("m"),date("d"),date("Y")));
  $today=date("YmdHis");
  $query="SELECT user, dt FROM ".$param["table_salle"]." WHERE dt<'$Hlimite'";
  $rst=mysql_query($query);
  if (mysql_num_rows($rst)>0) 
  { // Des users à deconnecter
    $query="DELETE FROM ".$param["table_salle"]." WHERE dt<'$Hlimite'";
    $result=mysql_query($query);
    $query="UPDATE ".$param["table_admin"]." SET dt_last_liste='$today'";
    $result=mysql_query($query);
    while ($row=mysql_fetch_object($rst)) 
    {
      $nom=GetDataField ($connect,"select util_nom_lb from utilisateur where util_login_lb='".$row->user."'","util_nom_lb");
      P4_Add_Msg("$nom ".$param["quit_us"],"",$user,$row->dt);
    }
  }
  $query="UPDATE ".$param["table_admin"]." SET dt_last_admin='$today'";
  $result=mysql_query($query);
  //$query="DELETE FROM ".$param["table_msg"]." WHERE dt<".date("Ymd")."000000";
  $result=mysql_query($query);
}

///////////////////////////////////////////////////
function P4_Add_Msg($msg,$color,$user,$dt) 
{
  global $connect,$param;
  $today=date("YmdHis");
  if ($user!="")
  {
    $query="SELECT user FROM ".$param["table_salle"]." WHERE user='$user'";
    $result=mysql_query($query);
    if (mysql_num_rows($result)==0) 
    { // Post par un user non connecté
      $query="UPDATE ".$param["table_admin"]." SET dt_last_liste='$today'";
      $result=mysql_query($query);
      //print("<SCRIPT language=javascript>alert(\"".$param["text_fatal_disconnect"]."\");top.close()</SCRIPT>");
      return 0;
    }
  }

  $msg=htmlspecialchars($msg,ENT_QUOTES,'ISO-8859-1');
  if ($dt!="") 
  {
    $dt=date("H:i:s",mktime(substr($dt,8,2),substr($dt,10,2),substr($dt,12,2)+$param["delai_connect"],1,1,2000));
  } 
  else 
  {
    $dt=date("H:i:s");
  }
  $dtc =date("d-n-Y H:i:s");
  if ($user!="") 
  {
       $renom=GetDataField ($connect,"select util_nom_lb from utilisateur where util_login_lb='$user'","util_nom_lb");
       $msg="<SPAN class=small>$dtc > <B>".$renom."</B></SPAN> : <FONT color=$color>".$msg."</FONT>";
  } 
  else 
  {
       $msg="<SPAN class=admin><SPAN class=small>".$dtc."> </SPAN>".$msg."</SPAN>";
  }
  $query="INSERT INTO ".$param["table_msg"]." (user, text, dest) VALUES ('$renom',\"$msg\",'')";
  $result=mysql_query($query);
  $query="UPDATE ".$param["table_admin"]." SET dt_last_chat='$today'";
  $result=mysql_query($query);
  return 1;
}

function P4_Coeur_Msg($dt_last_chat) 
{
  global $param;
  $retour="";
  $query="SELECT DATE_FORMAT(dt_last_chat,'%Y%m%d%H%i%s') AS dt_last FROM ".$param["table_admin"];
  $result=mysql_query($query);
  $row=mysql_fetch_object($result);
  $today=date("YmdHis");
  $dt_last=$row->dt_last;
  if ($dt_last>=$dt_last_chat) 
  {  // Rechercher les users connectés
     $datedujour = date("Y-m-d");
    $query="SELECT * FROM ".$param["table_msg"]." WHERE dt like '$datedujour%' ORDER By dt DESC";
//    $query="SELECT * FROM ".$param["table_msg"]." ORDER BY dt DESC LIMIT 0,".$param["chat_nb_msg"];
    $result=mysql_query($query);
    $nbResult = mysql_num_rows($result);
    $Z=""; $nb=0;
    while ($row=mysql_fetch_object($result)) 
    {
      if ($nb == $param["chat_nb_msg"] || $nb < $param["chat_nb_msg"])
         $Z="&nbsp;".$row->text."<BR>".$Z;
      $nb++;
    }
    $retour.="lesmsg=\"$Z </DIV></DIV>\"\n";
    $retour.="top.PrintMsg(lesmsg)\n";
  }
  $retour.="top.dt_last_chat=".$today.";\n";
  return $retour;
}

function P4_Disconnect($user) 
{
  global $param;
  $Hlimite=date("YmdHis",mktime(date("H"),date("i"),date("s")-$param["delai_connect"]-1,date("m"),date("d"),date("Y")));
  $today=date("YmdHis");
  $query="UPDATE ".$param["table_salle"]." SET dt=$Hlimite WHERE user='".$user."'";
  $result=mysql_query($query);
  P4_CheckConnect();
  print($param["text_disconnect"]);
}

//////////////////////////////////////////////////////////////////////////////
function P4_GetNbConnect() 
{
  global $param;
  P4_CheckConnect();
  $query="SELECT user FROM ".$param["table_salle"];
  $result=mysql_query($query);
  return mysql_num_rows($result);
}

function P4_GetListConnect($and) 
{
  global $param;
  P4_CheckConnect();
  $query="SELECT user FROM ".$param["table_salle"];
  $result=mysql_query($query);
  $nb=mysql_num_rows($result); $Z=""; $no=0;
  if ($nb>0) 
  {
    while ($row=mysql_fetch_object($result)) 
    {
      $Z.="<B>".$row->user."</B>";
      if ($no<$nb-1)  
      {
      	$Z.=", ";
      }
      if ($no==$nb-1) 
      {
      	$Z.="$and ";
      }
    }
  } 
  else 
  {
    $Z=$param["no_one"];
  }
  return $Z;
}



//////////////////////////////////////////////////////////////////////////////:


?>
