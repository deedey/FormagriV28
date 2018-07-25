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
function lanceRequest(monurl)
{
    var oHttpRequest=get_oHttpRequest();
    var params = '';
    for (i = 0; i < document.form4.elements.length; i++) {
     if (i == 0)
       params = params + document.form4.elements[i].name + '=' + escape(document.form4.elements[i].value);
     else
       params = params + '&' + document.form4.elements[i].name + '=' + escape(document.form4.elements[i].value);
   }
    var url = monurl+"?"+params;
    //alert('url = ' +url);
    document.getElementById("affiche").className="Status";
    document.getElementById("affiche").innerHTML="Opération en cours....";
    if(oHttpRequest)
    {
       oHttpRequest.onreadystatechange = function()
       {
         dispContent(oHttpRequest);
       };

    }
       oHttpRequest.open("get", url, true);
       oHttpRequest.send(null);
    //
}
/**************************************/
function dispContent(oHttpRequest)
{
        if (oHttpRequest.readyState == 4)
        {
           //alert(oHttpRequest.responseText);
               document.getElementById("affiche").className="Status";
               document.getElementById("affiche").innerHTML="";
               document.getElementById("mon_contenu").style.display='block';
               document.getElementById("mon_contenu").className="cms";
               document.getElementById("mon_contenu").innerHTML=oHttpRequest.responseText;
               document.getElementById("mon_contenu").style.padding="4px";
        }
        var mien_form = document.getElementById("form4");
        mien_form.onsubmit= function(){return false;};

}
