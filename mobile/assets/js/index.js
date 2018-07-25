$(document).ready(function(){
     $("#cancelMail").live("click",function(){
          $('#conteneur').css('height','0px');
          $('#conteneur').css('border','0px');
          $('#conteneur').empty();
      });
     $("#envoiMail").live("click",function(){
          if (escape($('#from').val()).indexOf(' ') + '' == '-1' &&
              escape($('#from').val()).indexOf('@') + '' != '-1' &&
              (escape($('#from').val()).lastIndexOf('.') > escape($('#from').val()).indexOf('@')) &&
              escape($('#from').val()) != '' && escape($('#sujet').val()) != '' &&
              escape($('#message').val()) != '')
              {
                 $.ajax({type: 'GET',
                         url: 'andromail.php',
                         data: 'go=1&from='+escape($('#from').val())+
                               '&email='+escape($('#email').val())+
                               '&sujet='+escape($('#sujet').val())+
                               '&captcha='+escape($('#captcha').val())+
                               '&track='+escape($('#track').val())+
                               '&message='+escape($('#message').val()),
                         success: function(msg){
                                  $('#icone').html(msg);
                                  $('#conteneur').css('height','0px');
                                  $('#conteneur').css('border','0px');
                                  $('#conteneur').empty();
                                  //$('#affiche').css('height','400px');
                                  $('#affiche').css('height','auto');
                                  setTimeout(function() {$('#icone').html('&nbsp;');},4000);}
                 });
         }
         else
         {
              $('#alerte').css('display','block');
              if (escape($('#sujet').val()) == '' || escape($('#message').val()) == '')
                 var lemessage = 'Sujet ou Message absent';
              else
                 var lemessage = 'Email non valide dans le champ expéditeur';
              $('#alerte').html(lemessage);
              $('#conteneur').css('height','340px');
              setTimeout(function(){
                              $('#alerte').css('display','none');
                              $('#conteneur').css('height','340px');
                              },4000);
          }
   });
  var keylist="abcdefghijklmnopqrstuvwxyz123456789";
  var temp='';
  function generatepass(plength){
    temp=''
    for (i=0;i<plength;i++)
        temp+=keylist.charAt(Math.floor(Math.random()*keylist.length));
    return temp;
  }
  function trim(myString)
  {
   return myString.replace(/^\s+/g,'').replace(/\s+$/g,'');
  }

  function verifmail(mail){
    var reg= /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,})+$/;
    if(reg.test(mail)==true)
     return true;
    else
     return false;
  }
  var track=generatepass(10);
  if (verifmail(mail) == true)
  {
       content+="<div style='width=180px;' onClick=\"javascript:$.ajax({"+
                                              "type: 'GET',"+
                                              "url: 'andromail.php?',"+
                                              "data: 'track="+track+"&email="+mail+"&subject=A propos de : "+escape(parcours)+
                                              "&body=A propos de votre module : "+escape(trim(body_parcours))+"',"+
                                              "success: function(msg){$('#conteneur').css('height','340px');"+
                                              "$('#conteneur').css('border','1px solid #24677A');"+
                                              "$('#affiche').css('height','200px');$('#conteneur').html(msg);}"+
                                        "});\"><span class='auteur'>Ecrire à l'auteur</span></div>";
   }
  $("#result").html(content);

});