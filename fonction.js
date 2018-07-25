/**
* Permet d'envoyer des données en GET ou POST en utilisant les XmlHttpRequest
*/
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

    //définition de l'endroit d'affichage:
    var content = document.getElementById("contenu");

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
            content.innerHTML = XhrObj.responseText ;
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
}//fin fonction SendData

/**
* Permet de récupérer les données d'un fichier via les XmlHttpRequest:
*/
function getFile(page)
{
    sendData('null', page, 'GET')
}//fin fonction getFile

function NewHttpReq() {
    var httpReq = false;
    if (typeof XMLHttpRequest!='undefined') {
        httpReq = new XMLHttpRequest();
    } else {
        try {
            httpReq = new ActiveXObject("Msxml2.XMLHTTP.4.0");
        }
        catch (e) {
            try {
                httpReq = new ActiveXObject("Msxml2.XMLHTTP");
            }
            catch (ee) {
                try {
                    httpReq = new ActiveXObject("Microsoft.XMLHTTP");
                }
                catch (eee) {
                    httpReq = false;
                }
            }
        }
    }
    return httpReq;
}

function DoRequest(httpReq,url,param) {

    // httpReq.open (Method("get","post"), URL(string), Asyncronous(true,false))

    httpReq.open("POST", url,false);
    httpReq.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    httpReq.send(param);
    if (httpReq.status == 200) {
        return httpReq.responseText;
    } else {
        return httpReq.status;
    }
}

function AddTime (first, second) {
        var sFirst = first.split(":");
        var sSecond = second.split(":");
        var cFirst = sFirst[2].split(".");
        var cSecond = sSecond[2].split(".");
        var change = 0;

        FirstCents = 0;  //Cents
        if (cFirst.length > 1) {
            FirstCents = parseInt(cFirst[1],10);
        }
        SecondCents = 0;
        if (cSecond.length > 1) {
            SecondCents = parseInt(cSecond[1],10);
        }
        var cents = FirstCents + SecondCents;
        change = Math.floor(cents / 100);
        cents = cents - (change * 100);
        if (Math.floor(cents) < 10) {
            cents = "0" + cents.toString();
        }

        var secs = parseInt(cFirst[0],10)+parseInt(cSecond[0],10)+change;  //Seconds
        change = Math.floor(secs / 60);
        secs = secs - (change * 60);
        if (Math.floor(secs) < 10) {
            secs = "0" + secs.toString();
        }

        mins = parseInt(sFirst[1],10)+parseInt(sSecond[1],10)+change;   //Minutes
        change = Math.floor(mins / 60);
        mins = mins - (change * 60);
        if (mins < 10) {
            mins = "0" + mins.toString();
        }

        hours = parseInt(sFirst[0],10)+parseInt(sSecond[0],10)+change;  //Hours
        if (hours < 10) {
            hours = "0" + hours.toString();
        }

        if (cents != '0') {
            return hours + ":" + mins + ":" + secs + '.' + cents;
        } else {
            return hours + ":" + mins + ":" + secs;
        }
    }

    function TotalTime() {
        total_time = AddTime(cmi.core.total_time, cmi.core.session_time);
        return '&'+underscore('cmi.core.total_time')+'='+escape(total_time);
    }

