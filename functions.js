/* $Id: functions.js,v 1.11 2002/01/03 12:09:30 loic1 Exp $ */
/**
* Merci à Qwix pour ce JS : http://qwix.media-box.net/index.php/2005/01/21/45-XmlhttprequestEtPhp#co
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
