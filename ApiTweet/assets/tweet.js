function strstr(haystack, needle, bool) {
 //   example 3: strstr('name@example.com', '@');

  var pos = 0;
  haystack += '';
  pos = haystack.indexOf(needle);
  if (pos == -1) {
    return false;
  } else {
    if (bool) {
      return haystack.substr(0, pos);
    } else {
      return haystack.slice(pos);
    }
  }
}

var TabSpec = {"[":" ","]":" ","À":"A","Á":"A","Â":"A","Ò":"O","Ó":"O","Ô":"O","Õ":"O","Ö":"O","Ø":"O","È":"E","É":"E","Ê":"E","Ë":"E","Ç":"C","Ì":"I","Í":"I","Î":"I","Ï":"I","Ù":"U","Ú":"U","Û":"U","Ü":"U","Ñ":"","à":"a","á":"a","â":"a","ã":"a","ä":"a","å":"a","ò":"o","ó":"o","ô":"o","õ":"o","ö":"o","ø":"o","è":"e","é":"e","ê":"e","ë":"e","ç":"c","ì":"i","í":"i","î":"i","ï":"i","ù":"u","ú":"u","û":"u","ü":"u","ÿ":"y","ñ":"n","_":" ","-":" "};
RegList=""
for (Char in TabSpec){ RegList+=Char.replace(/([[\]])/g,"\\$1");}

function replaceSpec(Texte){
	var reg=new RegExp('['+RegList+']','g')
	return Texte.replace(reg,function(){ return TabSpec[arguments[0].toLowerCase()];});
}

function getUrlVars()
{
	var t = window.location.search.substring(1).split('&');
	var f = [];
	for (var i=0; i<t.length; i++){
		var x = t[ i ].split('=');
		f[x[0]]=x[1];
      if (x[0] == 'activity_id')
         return x[1];
	}
	return '';
}

$(function() {
        $("#submitbutton").click(function() {
                
                if ($("textarea#PostArea").val() != "" && $("textarea#PostArea").val().length < 100 && $.trim($("textarea#PostArea").val()) != "Votre message de moins de 101 signes ici") {
                        jQ_Ajax('lib/appel.php?lance=ajout&Qui='+$("input#Qui").val()+'&Seq='+$("input#Seq").val()+'&textearea='+escape($("textarea#PostArea").val()));
                    if (strstr(window.location.href,'/ApiTweet/'))
                         window.location.replace(window.location.href.replace('activity_id='+getUrlVars('activity_id'),'activity_id='+ escape(replaceSpec($("textarea#PostArea").val())))+'|Tw|');
                    else if(strstr(window.location.href,'|Tw|'))
                         window.location.replace(window.location.href.replace('activity_id='+getUrlVars('activity_id'),'activity_id='+ escape(replaceSpec($("textarea#PostArea").val())))+'|Tw|');

                    setTimeout('$("#appendIt").text("Si vous voulez donner un avis..! ").css("color","#414E57").show()',2500);
                    $("#appendIt").text("Votre tweet a été envoyé..! ").css("color","#3D7F7E").show().fadeOut(2000);
                    $("textarea#PostArea").replaceWith('<textarea name="twitter_status" cols="" rows="3" id="PostArea" class="area" onkeyup="CountlimitChars()" onFocus="this.value =\'\';">Votre message de moins de 101 signes ici</textarea>');
                    $("textarea#PostArea").focus(function() { $(this).val('');});
                    $("#numberofWord").text('100');
                }
                else {
                        var LeTexte= $("textarea#PostArea").val();
                        setTimeout('$("#appendIt").text("Si vous voulez donner un avis..! ").css("color","#414E57").show()',2500);
                        if ($("textarea#PostArea").val() == "")
                            $("#appendIt").text("Votre texte est vide, voyons !!! ").css("color","#910909").show().fadeOut(2000);
                        else
                        {
                            if ($.trim($("textarea#PostArea").val()) == "Votre message de moins de 101 signes ici")
                            {
                                $("#appendIt").text("Veuillez donc saisir votre texte, voyons !!! ").css("color","#910909").show().fadeOut(2000);
                                var LeTexte= '';
                            }
                            else
                                $("#appendIt").text("Votre texte est bien trop long ! ").css("color","#910909").show().fadeOut(2000);
                        }
                        $("textarea#PostArea").focus(function() {$(this).val(LeTexte);});
                }
                //alert(window.location.href);

        });

});

$(function() {

        CountlimitChars = function() {

                $("#numberofWord").text(100 - $("textarea#PostArea").val().length ).show();

                if($("textarea#PostArea").val().length > 85) {
                        $("#numberofWord").addClass("one-fourty-red");
                }
                else {
                        $("#numberofWord").removeClass('one-fourty-red').addClass("one-fourty-orange");
                }

                if($("textarea#PostArea").val().length > 90) {
                        $("#numberofWord").addClass("one-fourty-orange");
                }
                else {
                        $("#numberofWord").removeClass('one-fourty-orange').addClass("one-fourty");
                }
        }
});

function jQ_Ajax(url)
{
    var tab=url.split('php?');
    var datas= tab[1];
    var adr=tab[0]+'php';
    $.ajax({
            type: 'get',
            url: adr,
            data: datas,
            async: true,
            success: function(){return true;},
            error: function(){return false;}
    });
}
// Avec retour d'infos
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
               $("#insertionTwit").html(responseText);
            },
            error: function(){return false;}
    });
}
function getCookieVal(offset)
{
              var endstr=document.cookie.indexOf (";", offset);
              if (endstr==-1)
                 endstr=document.cookie.length;
              return unescape(document.cookie.substring(offset, endstr));
}
function LireCookie(nom)
{
              var arg=nom+"=";
              var alen=arg.length;
              var clen=document.cookie.length;
              var i=0;
              while (i<clen){
                    var j=i+alen;
                    if (document.cookie.substring(i, j)==arg)
                        return getCookieVal(j);
                    i=document.cookie.indexOf(" ",i)+1;
                    if (i==0)
                        break;

              }
              return null;
}
//Avec traitement contextuel de retour d'infos
function jQ_AjaxTweet(url)
{
    var course=LireCookie("course");
    var registration=LireCookie("registration");
    var suite='&course='+course+'&registration='+registration;
    var tab=url.split('?');
    var datas= tab[1];
    var adr=tab[0];
    LireCookie("");
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
                  $("#sequenceTweet").html('<div id="sequenceTweet"><a href="javascript:void(0);" '+
                                           'onClick="javascript:jQ_AjaxTweet(\'ApiTweet/LibSeq/SeqTwit.php?lancerTwit=0'+suite+'\');" '+
                                           'title="Cliquez pour refermer la fenêtre de dialogue Twitter">'+
                                           '<img src="ApiTweet/assets/TwitLogo.gif" ></a>');
               }else{
                  $("#TweetSeq").html('');
                  $("#sequenceTweet").html('<div id="sequenceTweet"><a href="javascript:void(0);" '+
                                           'onClick="javascript:jQ_AjaxTweet(\'ApiTweet/LibSeq/SeqTwit.php?lancerTwit=1'+suite+'\');" '+
                                           'title="Cliquez pour ouvrir la fenêtre de dialogue Twitter">'+
                                           '<img src="ApiTweet/assets/TwitLogo.gif" ></a>');
               }
            },
            error: function(){return false;}
    });
}

var secs;
var timerID = null;
var timerRunning = false;
var delay = 1000    //definit le nombre de millisecondes dans la seconde.on peut le modifier

function InitializeTimerTweet()
{
    secs = 1;  // Nombre de secondes a decrementer
    StartTheTimerTweet();
}

function StopTheClockTweet()
{
    if(timerRunning)
        clearTimeout(timerID);
    timerRunning = false;
}
function StartTheTimerTweet()
{
        secs = secs - 1;
        var seconde = (secs < 2) ? ' seconde ' : ' secondes';
        window.status = 'Formagri version 2.8 -Janvier 2015- Cnerta/Eduter/AgroSupDijon. Nouveaux tweets dans ' + secs + seconde;
        timerRunning = true;
        if (secs == 0)
        {
           loadData();
           secs = 10;    //reinitialisation pour rester dans StartTheTimer
        }
        timerID = setTimeout("StartTheTimerTweet()", delay);
}
