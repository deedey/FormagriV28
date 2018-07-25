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
//alert ('page = '+page.value);
        }//fin else
    }//fin if
    else if(method == "POST")
    {
        //Ouverture du fichier en methode POST
//alert ('page = '+page);
       var machaine =page.substring(0,6);
       if (machaine == "formag"){
            document.getElementById("message").className="montrer";
            document.getElementById("contenu").className="contenu";
            document.getElementById("message").innerHTML="Veuillez patienter....";
            document.getElementById("contenu").innerHTML="Gestion de la base de donnees en cours.......";
       }
        XhrObj.open("POST", page);
    }//fin elseif

    //Ok pour la page cible
    //on définit l'appel de la fonction au retour serveur
    XhrObj.onreadystatechange = function(){ mon_alert_ajax(XhrObj); };

    if(method == "GET")
    {
        XhrObj.send(null);
    }//fin if
    else if(method == "POST")
    {
        XhrObj.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
        XhrObj.send(data);
    }//fin elseif
    function mon_alert_ajax(XhrObj)
    {
       if (XhrObj.readyState==4)    {

            document.getElementById("message").className="cacher";
            document.getElementById("contenu").className="contenu";
            document.getElementById("message").innerHTML="Configuration";
            document.getElementById("contenu").innerHTML=XhrObj.responseText;
            //alert('la reponse = '+XhrObj.responseText);
       }
    }
}



    function get_oHttpRequest() {

        var oHttpRequest = false;
        if (window.XMLHttpRequest)
        {
            oHttpRequest = new XMLHttpRequest();
            if (oHttpRequest.overrideMimeType)
            {
                oHttpRequest.overrideMimeType('text/xml');
            }
        } else if (window.ActiveXObject)
        {
            try
            {
                oHttpRequest = new ActiveXObject("Msxml2.XMLHTTP");
            }
            catch (e)
            {
                try
                {
                    oHttpRequest = new ActiveXObject("Microsoft.XMLHTTP");
                }
                catch (e)
                {}
            }
        }

        if (!oHttpRequest) {

            return false;
        }
        //


        return oHttpRequest;


    }
/**************************************/
function runRequest()
{
    var oHttpRequest=get_oHttpRequest();
    var params = '';
    for (i = 0; i < document.monform.elements.length; i++) {
     if (i == 0)
       params = params + document.monform.elements[i].name + '=' + document.monform.elements[i].value;
     else
       params = params + '&' + document.monform.elements[i].name + '=' + document.monform.elements[i].value;
   }
    var url="admin_save.php?"+params;
    alert('url = ' +url);
    //
    if(oHttpRequest)
    {
       oHttpRequest.onreadystatechange = function()
       {
         displayContent(oHttpRequest);

       };
       oHttpRequest.open('GET', url, true);
       oHttpRequest.send(null);

    }
    //
}
/**************************************/
function displayContent(oHttpRequest)
    {

        //
        if (oHttpRequest.readyState == 4)
        {
            if (oHttpRequest.status == 200)
            {
                        alert(oHttpRequest.responseText);
            document.getElementById("message").className="cacher";
            document.getElementById("contenu").className="contenu";
            document.getElementById("message").innerHTML="Configuration";
            document.getElementById("contenu").innerHTML=oHttpRequest.responseText;


            }
        }

    }