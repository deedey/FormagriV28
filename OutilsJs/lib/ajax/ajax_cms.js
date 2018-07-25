//var page;
function sendData(data, page, method)
{
//page=page;
  _debug=false;

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
    if (method == "GET")
    {
        if(data == 'null')
        {
            //Ouverture du fichier sélectionné:
            XhrObj.open("GET", page,true);
        }//fin if
        else
        {
            //Ouverture du fichier en methode GET
            XhrObj.open("GET", page+"?"+data,true);
if (_debug)
   alert ('page = '+page.value);
        }//fin else
    }//fin if
    else if(method == "post")
    {
        //Ouverture du fichier en methode POST
if (_debug)
  alert ('page = '+page);
       document.getElementById("affiche").className="Status";
       document.getElementById("affiche").innerHTML="";
       document.getElementById("affiche").innerHTML="Opération en cours....";
       if (document.getElementById("mien")){
         document.getElementById("mien").className="cms";
         document.getElementById("mien").innerHTML="";
       }
        XhrObj.open("post", page,true);
    }//fin elseif

    //Ok pour la page cible
    //on définit l'appel de la fonction au retour serveur
    XhrObj.onreadystatechange = function(){ retour_ajax(XhrObj); };

    if(method == "GET")
    {
        XhrObj.send(null);
    }//fin if
    else if(method == "post")
    {
        XhrObj.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
        XhrObj.send(data);
    }//fin elseif
    function retour_ajax(XhrObj)
    {
       _debug=false;
       if (XhrObj.readyState==4)
       {
           document.getElementById("affiche").className="Status";
           document.getElementById("affiche").innerHTML="";
           var prefixe = XhrObj.responseText.substring(0,4);
           var prefixe_1 = XhrObj.responseText.substring(0,3);

           if (prefixe == 'OUIM' || prefixe == 'NONM'){
              var aff = XhrObj.responseText.substring(3);
              if (_debug == true)
                 alert (prefixe+ "  ");
           }
           if (prefixe == 'OUIM')
           {
              document.getElementById("Oui0").className="Oui";
              document.getElementById("Oui0").style.padding="2px";
              document.getElementById("Oui0").innerHTML="Mode multi-centre activé :  OUI";
           }else if(prefixe == 'NONM')
           {
              document.getElementById("Oui0").className="Oui";
              document.getElementById("Oui0").style.padding="2px";
              document.getElementById("Oui0").innerHTML="Mode multi-centre activé :  NON";
           }
           if (prefixe == 'OUIF')
           {
              document.getElementById("Oui8").className="Oui";
              document.getElementById("Oui8").style.padding="2px";
              document.getElementById("Oui8").innerHTML="Gestion du marquage des modules séquences et activités favorites activée :  OUI";
           }else if(prefixe == 'NONF')
           {
              document.getElementById("Oui8").className="Oui";
              document.getElementById("Oui8").style.padding="2px";
              document.getElementById("Oui8").innerHTML="Gestion du marquage des modules séquences et activités favorites activée :  NON";
           }
           if (prefixe == 'OUIC' || prefixe == 'NONC'){
              var aff = XhrObj.responseText.substring(3);
              if (_debug == true)
                 alert (prefixe+ "  ");
           }
           if (prefixe == 'OUIC')
           {
              document.getElementById("Oui1").className="Oui";
              document.getElementById("Oui1").style.padding="2px";
              document.getElementById("Oui1").innerHTML="Chat activé :  OUI";
           }else if(prefixe == 'NONC')
           {
              document.getElementById("Oui1").className="Oui";
              document.getElementById("Oui1").style.padding="2px";
              document.getElementById("Oui1").innerHTML="Chat activé :  NON";
           }
           if (prefixe == 'OUIF' || prefixe == 'NONF'){
              var aff = XhrObj.responseText.substring(3);
              if (_debug == true)
                 alert (prefixe+ "  ");
           }
           if (prefixe == 'OUIF')
           {
              document.getElementById("Oui2").className="Oui";
              document.getElementById("Oui2").style.padding="2px";
              document.getElementById("Oui2").innerHTML="Forum libre activé :  OUI";
           }else if(prefixe == 'NONF')
           {
              document.getElementById("Oui2").className="Oui";
              document.getElementById("Oui2").style.padding="2px";
              document.getElementById("Oui2").innerHTML="Forum libre activé :  NON";
           }
           if (prefixe == 'OUIR' || prefixe == 'NONR'){
              var aff = XhrObj.responseText.substring(3);
              if (_debug == true)
                 alert (prefixe+ "  ");
           }
           if (prefixe == 'OUIR')
           {
              document.getElementById("Oui3").className="Oui";
              document.getElementById("Oui3").style.padding="2px";
              document.getElementById("Oui3").innerHTML="Fil Rss sur la conception des modules activé :  OUI";
           }else if(prefixe == 'NONR')
           {
              document.getElementById("Oui3").className="Oui";
              document.getElementById("Oui3").style.padding="2px";
              document.getElementById("Oui3").innerHTML="Fil Rss sur la conception des modules activé :  NON";
           }
           if (document.getElementById("mon_contenu"))
           {
               document.getElementById("mon_contenu").className="mon_contenu";
               if (prefixe_1 == 'OUI' || prefixe_1 == 'NON')
               {
                  var alen = XhrObj.responseText.length;
                  var la_reponse = XhrObj.responseText.substring(4,alen);
                  if (_debug == true)
                      alert(la_reponse);
               }else
                  var la_reponse = XhrObj.responseText;
               document.getElementById("mon_contenu").style.display='block';
               document.getElementById("mon_contenu").innerHTML=la_reponse;
               document.getElementById("mon_contenu").style.padding="2px";
           }
           if(document.getElementById("cms"))
           {
              document.getElementById("cms").style.display='block';
              document.getElementById("cms").className="cms";
              document.getElementById("cms").innerHTML=XhrObj.responseText;
              document.getElementById("cms").style.padding="2px";
           }

       }
    }
}

function getContentOption(obj)
{
   var contentOption=obj.options[obj.selectedIndex].innerHTML;
   return contentOption;
}
function get_id(page)
{
  var tab1=page.split('&');
  var tab2=tab1[1].split('=');//alert('ici = ' +tab2[1]+ "Page = "+page);

  return tab2[1];
}
function get_id2(page)
{
  var tab=page.split('?');
  var tab1=tab[1].split('&');
  return tab[1];
}
function get_id3(page)
{
  var tab=page.split('?');
  var tab3=tab[2].split('&');
  return tab[2];
}
function get_id4(page)
{
  var tab=page.split('?');
  var tab1=tab[1].split('&');
  return tab[1];
}
function addContent(page)
{
    var url= 'formation/gere_tut.php?recuperation=1&'+get_id2(page);
    var newContent =  jQ_Ajax(url,page);
}
function addContent_msg(page)
{
    var url= 'admin/msg.php?recuperation=1&'+get_id2(page);//alert(url);
    var newContent =  jQ_Ajax(url,page);
}
function addContent_star(page)
{
    var url= 'formation/star.php?recuperation=1&'+get_id2(page);
    var newContent =  jQ_Ajax(url,page);
}
function addContent_star2(page)
{
    var url= 'formation/star.php?recuperation=1&'+get_id3(page);
    var newContent =  jQ_Ajax(url,page);
}
function addContent_forum(page)
{
    var url= 'formation/forum_mod_modif.php?recuperation=1&'+get_id4(page);//alert(url);
    var newContent =  jQ_Ajax(url,page);
}
function jQ_Ajax(url,page)
{
    var tab=url.split('?');
    var datas= tab[1];
    var adr=tab[0];
    $.ajax({
            type: 'get',
            url: adr,
            data: datas,
            async: true,
            beforeSend:function()
            {
               $("#affiche").addClass('Status');
               $("#affiche").empty();
               $("#affiche").append('Opération en cours....');
            },
            success: function(responseText)
            {
               $("#affiche").addClass('Status');
               $("#affiche").empty();
               //alert('Page = ' +page+' et Url =  '+url);
               //var nouveau = $(get_id(page).value).append(responseText);
               var nouveau = document.getElementById(get_id(page)).innerHTML +=responseText;
               //alert(nouveau);
               return nouveau;
            },
            error: function(){return false;}
    });
}
/////////////////// appel simple jQAjax
function simplejQ_Ajax(url)
{
    var tab=url.split('?');
    var datas= tab[1];
    var adr=tab[0];
    $.ajax({
            type: 'get',
            url: adr,
            data: datas,
            async: true,
            success: function(){return true;},
            error: function(){return false;}
    });
}
function jQ_AjaxMsg(url)
{
    var tab=url.split('?');
    var datas= tab[1];
    var adr=tab[0];
    $.ajax({
            type: 'get',
            url: adr,
            data: datas,
            async: true,
            success: function(responseText)
            {
               //alert(responseText);
               $("#mien").html(responseText);
               var debut = responseText.substring(0,10);
               if (debut == "Le service"){
                  $("#seqtwt").html('<span style="font-weight:bold;" title="Echange sur Twitter activé pour cette séquence">'+
                             ' Echange twitter activé</span>');
               }
            },
            error: function(){return false;}
    });
}
function jQ_AjaxTweet(url)
{
    var tab=url.split('?');
    var datas= tab[1];
    var adr=tab[0];
    $.ajax({
            type: 'get',
            url: adr,
            data: datas,
            async: true,
            success: function(responseText)
            {
               var debut = responseText.substring(0,7);
               //alert(responseText);
               if (debut == "<IFRAME"){

                  $("#TweetSeq").html(responseText);
                  $("#sequenceTweet").html('<a href="javascript:void(0);" '+
                                           'onClick="javascript:jQ_AjaxTweet(\'ApiTweet/LibSeq/SeqTwit.php?lancerTwit=0\');" '+
                                           'title="Cliquez pour refermer la fenêtre de dialogue Twitter">'+
                                           '<img src="ApiTweet/assets/TwitLogo.gif" ></a>');
               }else{
                  $("#TweetSeq").html('');
                  $("#sequenceTweet").html('<a href="javascript:void(0);" '+
                                           'onClick="javascript:jQ_AjaxTweet(\'ApiTweet/LibSeq/SeqTwit.php?lancerTwit=1\');" '+
                                           'title="Cliquez pour ouvrir la fenêtre de dialogue Twitter">'+
                                           '<img src="ApiTweet/assets/TwitLogo.gif" ></a>');
               }
            },
            error: function(){return false;}
    });
}
/////////////////////////////////////////////////////utilise prototype;
function proto_Ajax(url,page)
{
    var oAjaxCall;
    oAjaxCall=new Ajax.Request(url,
    {
            asynchronous:true,
            method:'get',
            onLoading:function()
            {
               document.getElementById("affiche").className="Status";
               document.getElementById("affiche").innerHTML="Opération en cours....";
            },
            onSuccess: function(transport)
            {
               document.getElementById("affiche").className="Status";
               document.getElementById("affiche").innerHTML="";
               var data = transport.responseText;
               var nouveau = document.getElementById(get_id(page)).innerHTML +=data;
               //alert(nouveau);
               return nouveau;
            },
            onFailure: function(transport){return false;}
    });
}

