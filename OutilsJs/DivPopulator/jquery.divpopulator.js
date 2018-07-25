
jQuery.divpopulator=function(URL,config){
//
        var options=config.options;
        var terms;
        var hiddenName;
        var searchEngineContainerId;
        var inputSearch;
        var highLightBorderColor;
        var highLightWordSearchClass;
        var defaultValue= '' || options.defaultValue;
        var searchInInputClass='searchInInput';
        var loadingInInputClass='loadingInInput';
        //
        init();
        //
        function init()
        {

            if(options.hiddenName)
            {
                hiddenName= options.hiddenName;
            }
            if(options.inputSearch)
            {
                inputSearch= options.inputSearch;
            }
            if(options.searchEngineContainerId)
            {
                searchEngineContainerId= options.searchEngineContainerId;
            }
            if(options.highLightBorderColor)
            {
                highLightBorderColor= options.highLightBorderColor;
            }
            if(options.highLightWordSearchClass)
            {
               highLightWordSearchClass= options.highLightWordSearchClass;
            }
            //
            $('#'+searchEngineContainerId+' #'+inputSearch).focus();
            //
            if(defaultValue)
            {
                $('#'+searchEngineContainerId+' #'+inputSearch).val(defaultValue);
            }
            //

            $('#'+inputSearch).addClass(searchInInputClass);
            //
            $('#'+inputSearch).after('<div id="resultInformation">&nbsp;<\/div><div id="resultList"  style="display:none;"><\/div>');
            //en attente
            /*
            $('#'+searchEngineContainerId+' #'+inputSearch).click(function(){
                //$(this).val('');
                //$("."+hiddenName).remove();
                });
            //
            */
            $('#'+searchEngineContainerId+' #'+inputSearch).keyup(function(event){
                //
                $("."+hiddenName).remove();
                //
                terms=cleanInput($('#'+searchEngineContainerId+' #'+inputSearch).val());

                if (event.keyCode != 13 && event.keyCode != 32)//entree=13 et espace = 32
                {
                  //
                  $('#'+searchEngineContainerId+' #resultInformation').empty();
                  $('#'+searchEngineContainerId+' #resultList').empty();
                  //
                  //
                  runRequest(terms);
                  //
                }
                $('#'+searchEngineContainerId+' #'+inputSearch).val(terms);
            });
            //
            $('#'+searchEngineContainerId+' #resultList div').livequery(function(){
                $(this).hover(
                    function() {
                        $(this).css('border-color',highLightBorderColor);
                    },
                    function() {
                        $(this).css('border-color','');
                    });
            }, function() {
                $(this).unbind('mouseover').unbind('mouseout');
            });
            //
            $('#'+searchEngineContainerId+' #resultList div').livequery('click', function(event) {
                //
                $("."+hiddenName).remove();
                var selected_id=$(this).attr('id');
                var selected_content=$(this).text();
                var hidden='<input type="hidden" class="'+hiddenName+'" name="'+hiddenName+'" value="'+selected_id+'" \/>';
                //
                $('#'+searchEngineContainerId+' #'+inputSearch).after(hidden);
                //
                $('#'+searchEngineContainerId+' #'+inputSearch).val(selected_content);
                //
                if(options.removeResultAfterClick)
                {
                    $('#'+searchEngineContainerId+' #resultList').hide();
                }
                $('#'+searchEngineContainerId+' #resultInformation').empty();
                $('#'+searchEngineContainerId+' #'+inputSearch).focus();
            });
            //
        }
        //
        function runRequest()
        {
            ajaxStart();
            //
            var date=new Date();
            var timestamp=date.getMilliseconds();
            var param={};
            //
            jQuery.extend(param, {search: encodeURIComponent(terms),_t:timestamp});
            jQuery.extend(param, config.postParam);
            //
            jQuery.post(
                         URL
                         ,param
                         ,onSuccess=function(data){
                            displayData(data);
                            ajaxStop();
                            }
                         ,"json"
                        );
            //

        }
        //
        function displayData(data)
        {
            //
            var items=data.items;
            if(data.resultInformation)
            {
                $('#'+searchEngineContainerId+' #resultInformation').html(data.resultInformation);
                return;
            }
            //
            $('#'+searchEngineContainerId+' #resultInformation').html(data.msg_total_found);
            //
            if(items.length==0)
            {
                 return;
            }
            //
            $('#'+searchEngineContainerId+' #resultList').html(populate(items));
            $('#'+searchEngineContainerId+' #resultList .item').css('cursor','pointer');
            $('#'+searchEngineContainerId+' #resultList').show();
            //
            $(searchEngineContainerId+' '+inputSearch).focus();
            //


        }
        //
        function populate(items)
        {
            var allItems=buildList(items);
            //
            var list='';
            jQuery.each(allItems, function() {
               if(this)
               {
                    var str=this.jLabel;
                    var reg=new RegExp("({{)","gi");
                    str=str.replace(reg,'<span class="'+highLightWordSearchClass+'">');
                    var reg=new RegExp("(}})","gi");
                    str=str.replace(reg,'<\/span>');
                    list +='<div id="'+this.jId+'" class="item">'+str+'<\/div>';
                }
            });
            //
            return list;
        }
        function buildList(aItems)
        {
           var list=[];
            //
            jQuery.each(aItems, function(i,item) {

                    item.jLabel=toHighLight(item.jLabel,terms);
                    list.push(item);
            });
            //
            return list;
        }
        //
        function ajaxStart()
        {
          $("#"+inputSearch).removeClass(searchInInputClass);
          $("#"+inputSearch).addClass(loadingInInputClass);
        }
        //
        function ajaxStop()
        {
            $("#"+inputSearch).removeClass('loadingInInput');
            $("#"+inputSearch).addClass('searchInInput');
        }
        function toHighLight(str, needle) {

            var value=str;
            var terms=needle;
            //
            var aNeedle=needle.split(' ');
            var i=0;
            jQuery.each(aNeedle, function() {
                if(this && jQuery.trim(this)!='')
                {
                    var reg=new RegExp("("+this+")","gi");
                    value=value.replace(reg,'{{$1}}');
                }

            });
            return value;
        }
        //
        function strstr(haystack,needle)
        {
            var pos = 0;
            haystack += '';
            pos = haystack.indexOf( needle );
            if (pos == -1)
                return false;
            else
                return haystack.slice( pos );
         }
         function cleanInput(str)
         {
              str = str.replace(/[\"]+/gi,'"');
              str = str.replace(/[\']+/gi,"'");
              str = str.replace(/[\s]+/gi," ");
              if(str == ' ')
                   str = '';
              if (str != '' && strstr(str,'%') == '')
                   return str;
              else
              {
                  if (strstr(str,'%') != '')
                  {
                     alert('Attention !!! Vous n\'avez pas le droit d\'utiliser le caractère - % - .');
                     str = str.substring(0,str.length-1);
                  }
                  return str;
              }
         }
        //

//
};
