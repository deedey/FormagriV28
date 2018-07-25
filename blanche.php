<?php
if (!isset($_SESSION)) session_start();
include ("include/UrlParam2PhpVar.inc.php");
if (!isset($_SESSION['id_user']) || $_SESSION['id_user'] == "")
{
  exit();
}
require "admin.inc.php";
require 'fonction.inc.php';
require "lang$lg.inc.php";
dbConnect();
$agent=getenv("HTTP_USER_AGENT");
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<?php
echo "<title>Cron</title></head>";
?>
<SCRIPT language="javascript" type="text/javascript">
function loadData() {
       sendData('2', 'appel_chat.php', 'POST');
}
function sendData(data, page, method)
{
    if(window.ActiveXObject)
    {
        //Internet Explorer
        var XhrObj = new ActiveXObject("Microsoft.XMLHTTP") ;
    }//fin if
    else
    {
        //Mozilla
        var XhrObj = new XMLHttpRequest();
    }//fin else
    //si on envoie par la méthode GET:
    if(method == "GET")
    {
        if(data == 'null')
        {
            //Ouverture du fichier sélectionné:
            XhrObj.open("GET", page);
        }//fin if
        else
        {
            //Ouverture du fichier en methode GET
            XhrObj.open("GET", page+"?"+data);
        }//fin else
    }//fin if
    else if(method == "POST")
    {
        //Ouverture du fichier en methode POST
        XhrObj.open("POST", page);
    }//fin elseif

    //Ok pour la page cible
    XhrObj.onreadystatechange = function()
    {
        if (XhrObj.readyState == 4 && XhrObj.status == 200)
        {
            if (XhrObj.responseText != ''){
               affiche_result(XhrObj);
               return XhrObj.responseText ;
            }
          return XhrObj.responseText ;
       }
    }
    function affiche_result(XhrObj)
    {
       if (XhrObj.readyState==4)
       {
           str1 = XhrObj.responseText;
           str=str1.substring(0,5);
           if  (str == '<div ')
           {
              //document.main.location.getElementById("msgInst").className="Oui";
              parent.frames['logo'].document.getElementById("msgInst").className="msgInst";
              parent.frames['logo'].document.getElementById("msgInst").innerHTML=XhrObj.responseText;
           }
           if (str == 'chat/')
              window.open(XhrObj.responseText,"chat_formagri","resizable=yes,status=no,scrollbars=no, menubar=no, width=550,height=400");
           if  (str != '<div ' && str != 'chat/')
              alert (unescape(XhrObj.responseText));
       }
    }

    if(method == "GET")
    {
        XhrObj.send(null);
    }//fin if
    else if(method == "POST")
    {
        XhrObj.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
        XhrObj.send(data);
    }//fin elseif
}
</SCRIPT>

<SCRIPT LANGUAGE = "JavaScript" type="text/javascript"> // Gère la boucle du timer, décrémente et lance la requete AjaX à la fin de la boucle
<!--

var secs
var timerID = null
var timerRunning = false
var delay = 1000    //definit le nombre de millisecondes dans la seconde.on peut le modifier

function InitializeTimer()
{
    secs = 20  // Nombre de secondes a decrementer
    StartTheTimer()
}

function StopTheClock()
{
    if(timerRunning)
        clearTimeout(timerID)
    timerRunning = false
}
function StartTheTimer()
{
        secs = secs - 1
        window.status = 'Formagri version 2.7.0.3 -Janvier 2013- Dey Bendifallah/Cnerta/Eduter/AgroSupDijon. Relance dans ' + secs
        timerRunning = true
        if (secs == 0){
           loadData()
           secs = 20    //reinitialisation pour rester dans StartTheTimer
        }
        timerID = setTimeout("StartTheTimer()", 1000)
}
//-->
</SCRIPT>
<?php
echo "<body bgcolor='white' style=\"margin-width:'0'; margin-right:'0'; margin-left:'0'; margin-top:'0';\" onload=\"javascript:InitializeTimer();\">";
?>
</body>
</html>
