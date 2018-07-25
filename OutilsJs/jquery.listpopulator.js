
/*...........................................................................*/
/*
 * @author Nordine Zetoutou  - <nordine.zetoutou@educagri.fr>
 * @copyright cnerta 2008
 * @version : version="2008-03-18 15:28";
 */
/*...........................................................................*/

jQuery.listpopulator=function(inputName,options){

    var aWord='';
    //
    var inputSearch="#"+inputName;
    // alert(inputSearch);
    var searchButton=inputName+'_button';
    var inputClass='searchInInput';
    var inputNewSizeClass='inputNewSize';
    //
    if(options.defaultValue)
    {
        $(inputSearch).val(options.defaultValue);
    }

    //
    $(inputSearch).attr('listPopulator','off');
    //
    $(inputSearch).addClass(inputClass);
    $(inputSearch).addClass(inputNewSizeClass);
    //
    var htmlAdds='&nbsp;';
    htmlAdds +='<input type="button" id="'+searchButton+'"  value="Chercher" class="inputSubmit" />';
    //
    htmlAdds +='&nbsp;<span id="'+inputName+'_listPopulatorResult">';
    htmlAdds +='&nbsp;<span id="resultTotal"></span>';
    htmlAdds +='&nbsp;<span id="resultHidden"></span>';
    htmlAdds +='<span id="resultList"></span>';
    htmlAdds +='<div id="resultInformation"></div>';
    htmlAdds +='</span>';
    $(inputSearch).after(htmlAdds);
    //---------------------------------------------//
     $('#'+searchButton).click(function(e) {

        $(inputSearch).attr('listPopulator','on');
        search();
     });
     //


     $(inputSearch).keyup(function(){
          $(inputSearch).val(cleanInput($(inputSearch).val()));
          $(inputSearch).attr('listPopulator','on');
      });
      //

      if(options.searchOnKeyPress)
      {
           $("#"+searchButton).remove();
            $(inputSearch).keyup(function(){
            $(inputSearch).attr('listPopulator','on');
            search();
          });
      }



    //---------------------------------------------//
    function cleanInput(str)
    {
          str = str.replace(/[\"]+/gi,'"');
          str = str.replace(/[\']+/gi,"'");
          str = str.replace(/[\s]+/gi," ");
          return str;
    }
    //
    function toBold(value, needle) {

        var aNeedle=needle.split(' ');
        var i=0;
        for(i in aNeedle)
        {

            if(aNeedle[i])
            {
                var reg=new RegExp("("+aNeedle[i]+")", "gi");
                value=value.replace(reg,'<span style="font-weight:bold">$1</span>');
            }

        }
        return value;
    }
    //
    function buildList(items)
    {

        var list='';
        //

        jQuery.each(items, function(i,item) {

          if(typeof item=='object')
          {
            item['content']=toBold(item['content'],aWord);
            list +='<li id="'+item['id'] +'">'+item['content']+'<\/li>';
          }
          else
          {
            item['content']=toBold(item,aWord);
            list +='<li>'+item+'<\/li>';
          }

        });
        //
        return '<ul>'+list+'<\/ul>';
    }
    //---------------------------------------------//
    function formatResult(items){
       var newItems=[];
       jQuery.each(items, function(i,val) {

        newItems[i]=options.formatItem(val);
        });
       return newItems;
    }
  //---------------------------------------------//
    function buildListContainer()
    {

        var width=$(inputSearch).width();
        $('#resultList').css('position','absolute');
        var resultListWidth=width+22;

        $('#resultList').css('width',resultListWidth+1);
        $('#resultList ul').css('width',width-16);
        var offset = $(inputSearch).offset();
        var left = offset.left-2;
        $('#resultList').css('left',left+2);
        var top = offset.top+$(inputSearch).height();
        $('#resultList').css('top',top+2);
        //
        if($.browser.msie)
            {
          var iFrame='<iframe id="iFrame" style="position:absolute;" frameborder="0"  scrolling="no"></iframe>';
          $('#resultList').before(iFrame);
            $('#iFrame').css('top',top);
            $('#iFrame').css('left',left+2);
            $('#iFrame').css('width',resultListWidth+3);
        }
        //
        $('li').livequery('click', function(event) {

           if($(inputSearch).attr('listPopulator')=='on')
           {
             $(inputSearch).val($(this).text());
             if(options.hidden_name_label)
             {
                var selected_id=$(this).attr("id");
                $(inputSearch+"_listPopulatorResult #resultHidden").html('<input type="hidden" value="'+selected_id+'" name="'+options.hidden_name_label+'" />');
                if(options.afterPutAction)
                 {
                   options.afterPutAction(selected_id);
                 }
             }
             //
             $('#resultList').hide();
             $('#iFrame').remove();
             $(inputSearch+'_listPopulatorResult #resultTotal').empty();
             //

             //
             return false;
           }
        });
        //
        $('li').livequery(function(){
            $(this).hover(
                function() {
                $(this).css('backgroundColor','#eff6ff');
                },
                function() {
                    $(this).css('backgroundColor','');
                });
        }, function() {
            $(this).unbind('mouseover').unbind('mouseout');
        });
        }
     //---------------------------------------------//
     function search()
     {
        $(inputSearch+'_listPopulatorResult #resultInformation').empty();
        $(inputSearch+'_listPopulatorResult #resultTotal').empty();
        $("#resultList").hide();
        $('#iFrame').remove();

        aWord=$(inputSearch).val();
        if(!aWord)
        {
            $(inputSearch).val('');
            $(inputSearch).focus();
             return;
        }
        $(inputSearch).val(aWord);
        //
        ajaxStart();
        //
        var data = {'search':encodeURIComponent(aWord)};//encodeURIComponent(aWord)
        jQuery.extend(data, options.searchParam);
         //

        jQuery.ajax({
               'url':options.url,
               'data':data,
               'cache':false,
               'dataType':'json',
               'success':function(data){
                    //
                    var total=0;
                    //
                    if(data.resultInformation)
                    {
                      $(inputSearch+'_listPopulatorResult #resultInformation').html(data.resultInformation);

                    }else{
                      if(data.items)
                      {

                           total=data.items.length;

                           if(data.items.length==0)
                           {
                              $(inputSearch+'_listPopulatorResult #resultTotal').html('('+'<b>'+total+'</b>'+')');
                              ajaxStop();
                              return;
                           }
                           //
                           buildListContainer();
                           var list=buildList(formatResult(data.items));
                           $("#resultList").html(list);
                           $("#resultList").show();
                           if($.browser.msie)
                           {
                             $("#iFrame").show();
                           }
                           //
                           var resultListHeight;
                           if(total<10)
                           {
                              resultListHeight=total*17;
                           }
                           else
                           {
                              resultListHeight=5*17;
                           }
                           //
                           $('#resultList').css('height',resultListHeight);
                           $('#iFrame').css('height',resultListHeight);
                                           //
                           $(inputSearch+'_listPopulatorResult #resultTotal').html('('+'<b>'+total+'</b>'+')');

                           //

                      }
                    }
          ajaxStop();



        }});
        //

        function ajaxStart()
        {
          $(inputSearch).removeClass('searchInInput');
          $(inputSearch).addClass('loadingInInput');
        }
        //
        function ajaxStop()
        {
            $('input').attr('listPopulator','off');
            $(inputSearch).attr('listPopulator','on');
            $(inputSearch).removeClass('loadingInInput');
            $(inputSearch).addClass('searchInInput');
        }
        //
        $(inputSearch).focus();
        //
        return false;
        //

    };
    //---------------------------------------------//

  };