function popupload(cible,nom,lg,ht) {
//Javascript:popupload('telecharger.pgi?cmd=frame','telecharger','470','280');
  var win = window.open(cible, nom, 'width='+lg+',height='+ht+',resizable=yes,scrollbars=yes,status=yes,menubar=no,toolbar=no,location=no,directories=no,closed=no,opener=no');
}
function strstr (haystack, needle, bool) {
    var pos = 0;
    haystack += '';
    pos = haystack.indexOf( needle );
    if (pos == -1) {
        return false;
    } else{
        if (bool){
            return haystack.substr( 0, pos );
        } else{
            return true;
        }
    }
}

function appel_w(sel_val) {

  var fset=sel_val.substring(0,2);
  var f2=sel_val;
  var url1 = ""+f2+"";
  if (fset == "tr" )
   parent.main.location=url1
}
function appel_w1(sel_val) {

  var fset=sel_val.substring(0,2);
  var f2=sel_val;
  var url1 = ""+f2+"";
  if (fset == "tr" )
  {
   document.location=url1;
   }
}
function appel_win(sel_val) {

  var f2=sel_val;
  var url1 = ""+f2+"";
  parent.location=url1
}
function appel_simple(ele) {
   appelle_ajax(ele);
}
function appelle_ajax(ele) {
   //alert ('element = '+ele);
   sendData('',ele,'post');
}
function appel_wpop(sel_val) {
  var fset=sel_val.substring(0,2);
  var f2=sel_val;
  var url1 = ""+f2+"";
  if ( fset == "tr" )
   window.open('','','width=680,height=520,resizable=yes,scrollbars=yes,status=no').location=url1
}
function TryCallFunction() {
        var sd = document.MForm.mydate1.value.split("\/");
        document.MForm.iday.value = sd[1];
        document.MForm.imonth.value = sd[0];
        document.MForm.iyear.value = sd[2];
}
function TryCallFunction1() {
        var sd = document.MForm.ma_date.value.split("\/");
        document.MForm.iday1.value = sd[1];
        document.MForm.imonth1.value = sd[0];
        document.MForm.iyear1.value = sd[2];
}

function Today() {
        var dd = new Date();
        return((dd.getMonth()+1) + "/" + dd.getDate() + "/" + dd.getFullYear());
}
function popup(f,nom, w, h) {
   window.open(f, nom, 'resizable,screenX=0,screenY=0,scrollbars=yes,menubar=yes,width=' + w + ',height=' + h);
}

function tinypopup(f,nom, w, h) {
   window.open(f, nom, 'resizable,screenX=0,screenY=0,scrollbars=no,menubar=no,width=' + w + ',height=' + h);
}
function numerotation(type,nbr)
{
          var alpha = new Array("A","B","C","D","E","F","G");
          var roma = new Array("I","II","III","IV","V","VI","VII");
          var numeral = new Array("1","2","3","4","5","6","7");
          if (type == "alphabet")
             return alpha[nbr-1];
          if (type == "romain")
             return roma[nbr-1];
          if (type == "numeric")
             return numeral[nbr-1];
}
function makevisible(cur,which){
   if(document.getElementById){
        if (which==0){
           if(document.all)
              cur.filters.alpha.opacity=100;
           else
              cur.style.setProperty("-moz-opacity", 1, "");
        }else{
           if(document.all)
              cur.filters.alpha.opacity=1;
           else
              cur.style.setProperty("-moz-opacity", .01, "");
        }
   }
}
$(document).ready(
        function()
        {
                $('#windowOpen').bind(
                        'click',
                        function() {
                                if($('#window').css('display') == 'none') {
                                        $(this).TransferTo(
                                                {
                                                        to:'window',
                                                        className:'transferer2',
                                                        duration: 400,
                                                        complete: function()
                                                        {
                                                                $('#window').show();
                                                        }
                                                }
                                        );
                                }
                                this.blur();
                                return false;
                        }
                );
                $('#windowClose').bind(
                        'click',
                        function()
                        {
                                $('#window').TransferTo(
                                        {
                                                to:'windowOpen',
                                                className:'transferer2',
                                                duration: 400
                                        }
                                ).hide();
                        }
                );
                $('#windowMin').bind(
                        'click',
                        function()
                        {
                                $('#windowContent').SlideToggleUp(300);
                                $('#windowBottom, #windowBottomContent').animate({height: 10}, 300);
                                $('#window').animate({height:40},300).get(0).isMinimized = true;
                                $(this).hide();
                                $('#windowResize').hide();
                                $('#windowMax').show();
                        }
                );
                $('#windowMax').bind(
                        'click',
                        function()
                        {
                                var windowSize = $.iUtil.getSize(document.getElementById('windowContent'));
                                $('#windowContent').SlideToggleUp(300);
                                $('#windowBottom, #windowBottomContent').animate({height: windowSize.hb + 13}, 300);
                                $('#window').animate({height:windowSize.hb+43}, 300).get(0).isMinimized = false;
                                $(this).hide();
                                $('#windowMin, #windowResize').show();
                        }
                );
                $('#window').Resizable(
                        {
                                minWidth: 200,
                                minHeight: 60,
                                maxWidth: 800,
                                maxHeight: 400,
                                dragHandle: '#windowTop',
                                handlers: {
                                        se: '#windowResize'
                                },
                                onResize : function(size, position) {
                                        $('#windowBottom, #windowBottomContent').css('height', size.height-33 + 'px');
                                        var windowContentEl = $('#windowContent').css('width', size.width - 25 + 'px');
                                        if (!document.getElementById('window').isMinimized) {
                                                windowContentEl.css('height', size.height - 48 + 'px');
                                        }
                                }
                        }
                );
        }
);
//$(document).ready(function() {
//        $('#myselectbox').selectbox();
//});
