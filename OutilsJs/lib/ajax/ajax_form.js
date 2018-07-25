
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