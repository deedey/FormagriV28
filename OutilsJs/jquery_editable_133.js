(function($){
/*
 * Editable 1.3.3
 *
 * Copyright (c) 2009 Arash Karimzadeh (arashkarimzadeh.com)
 * Licensed under the MIT (MIT-LICENSE.txt)
 * http://www.opensource.org/licenses/mit-license.php
 *
 * Date: Mar 02 2009
 */
    function getCookieVal(offset){
        var endstr=document.cookie.indexOf (";", offset);
        if (endstr==-1)
           endstr=document.cookie.length;
        return unescape(document.cookie.substring(offset, endstr));
   }
   function LireCookie(nom){
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
  var monpath=LireCookie("monpath");
$.fn.editable = function(options){
        var defaults = {
                onEdit: null,
                onSubmit: null,
                onCancel: null,
                editClass: null,
                submit: null,
                cancel: null,
                largeur:null,
                type: 'text', //text, textarea or select
                submitBy: 'blur', //blur,change,dblclick,click
                editBy: 'click',
                options: null
        }
        if(options=='disable')
                return this.unbind(this.data('editable.options').editBy,this.data('editable.options').toEditable);
        if(options=='enable')
                return this.bind(this.data('editable.options').editBy,this.data('editable.options').toEditable);
        if(options=='destroy')
                return  this.unbind(this.data('editable.options').editBy,this.data('editable.options').toEditable)
                                        .data('editable.previous',null)
                                        .data('editable.current',null)
                                        .data('editable.options',null);

        var options = $.extend(defaults, options);

        options.toEditable = function(){
                $this = $(this);
                $this.data('editable.current',$this.html());
                // dey
                $this.attr('title','');
                //
                opts = $this.data('editable.options');
                $.editableFactory[opts.type].toEditable($this.empty(),opts);
                // Configure events,styles for changed content
                $this.data('editable.previous',$this.data('editable.current'))
                         .children()
                         .focus()
                         .addClass(opts.editClass);
                // Submit Event
                if(opts.submit){
                        $('<image src="'+monpath+'/images/modif15.gif" style="cursor:pointer;float:left;" title="Modifier cet item"/>')
                                                .appendTo($this)
                                                .html(opts.submit)
                                                .one('mouseup',function(){opts.toNonEditable($(this).parent(),true)});
                }else
                        $this.one(opts.submitBy,function(){opts.toNonEditable($(this),true)})
                                 .children()
                                 .one(opts.submitBy,function(){opts.toNonEditable($(this).parent(),true)});
                // Cancel Event
                if(opts.cancel)
                        $('<image src="'+monpath+'/images/retour15.gif" style="cursor:pointer;float:left;" title="Abandonner cette édition"/>')
                                                .appendTo($this)
                                                .html(opts.cancel)
                                                .one('mouseup',function(){opts.toNonEditable($(this).parent(),false)});
                // Call User Function
                if($.isFunction(opts.onEdit))
                        opts.onEdit.apply($this,
                                               [{
                                                  current:$this.data('editable.current'),
                                                  previous:$this.data('editable.previous')
                                               }]
                );
        }
        options.toNonEditable = function($this,change){
                opts = $this.data('editable.options');
                // Configure events,styles for changed content
                $this.one(opts.editBy,opts.toEditable)
                         .data( 'editable.current',
                                    change
                                                ?$.editableFactory[opts.type].getValue($this,opts)
                                                :$this.data('editable.current')
                                        )
                         .html(
                                    opts.type=='password'
                                                   ?'*****'
                                                :$this.data('editable.current')
                                        );
                // Call User Function
                var func = null;
                if($.isFunction(opts.onSubmit)&&change==true)
                        func = opts.onSubmit;
                else if($.isFunction(opts.onCancel)&&change==false)
                        func = opts.onCancel;
                if(func!=null)
                        func.apply($this,
                                        [{
                                            current:$this.data('editable.current'),
                                            previous:$this.data('editable.previous')
                                        }]
                        );
        }
        this.data('editable.options',options);
        return  this.one(options.editBy,options.toEditable);
}
$.editableFactory = {
        'text': {
                toEditable: function($this,options){
                        // debut dey
                        if ($this.data('editable.current') == "N°")
                                  $('<input style="font-size:11px;font-faily:arial;float:left;"/>')
                                                 .css('width',opts.largeur)
                                                 .appendTo($this).text('');
                        else //fin dey
                                 $('<input style="font-size:11px;font-faily:arial;float:left;"/>')
                                                 .css('width',opts.largeur)
                                                 .appendTo($this)
                                                 .val($this.data('editable.current'));
                },
                getValue: function($this,options){
                        return $this.children().val();
                }
        },
        'password': {
                toEditable: function($this,options){
                        $this.data('editable.current',$this.data('editable.password'));
                        $this.data('editable.previous',$this.data('editable.password'));
                        $('<input type="password"/>').appendTo($this).val($this.data('editable.current'));
                },
                getValue: function($this,options){
                        $this.data('editable.password',$this.children().val());
                        return $this.children().val();
                }
        },
        'textarea': {
                toEditable: function($this,options){
                        // debut dey
                        if ($this.data('editable.current') == "----------" || $this.data('editable.current') == "Non renseigné")
                               $('<textarea style="font-size:11px;height:auto;font-faily:arial;float:left;"/>')
                                           .css('width',opts.largeur)
                                           .appendTo($this).text('');
                        else //fin dey
                                $('<textarea style="font-size:11px;height:auto;font-faily:arial;float:left;"/>')
                                                       .css('width',opts.largeur)
                                                       .appendTo($this).val($this.data('editable.current'));
                },
                getValue: function($this,options){
                        return $this.children().val();
                }
        },
        'select': {
                toEditable: function($this,options){
                        $select = $('<select/>').appendTo($this);
                        $.each( options.options,
                                        function(key,value){
                                                $('<option/>').css('width',opts.largeur)
                                                              .appendTo($select)
                                                              .html(value)
                                                              .attr('value',key);
                                        }
                                   )
                        $select.children().each(
                                function(){
                                        var opt = $(this);
                                        if(opt.text()==$this.data('editable.current'))
                                                return opt.attr('selected', 'selected').text();
                                }
                        )
                },
                getValue: function($this,options){
                        var item = null;
                        $('select', $this).children().each(
                                function(){
                                        if($(this).attr('selected'))
                                                return item = $(this).text();
                                }
                        )
                        return item;
                }
        },
        'checkbox': {
            toEditable: function($this,options){
                $.each( options.options,
                        function(){
                            $('<input type="checkbox"/>').appendTo($this)
                                        .val(this)
                                        .after('<span>'+this+'</span>');
                        }
                    )
                var currentValues = $this.data('editable.current').split(',');
                $this.children(':checkbox').each(
                    function(){
                        if(currentValues.indexOf($(this).val())>-1)
                            $(this).attr('checked', 'checked');
                    }
                )
            },
            getValue: function($this,options){
                var items = [];
                $(':checkbox', $this).each(
                    function(){
                        if($(this).attr('checked'))
                            items.push($(this).val());
                    }
                )
                return items.join(',');
            }
        }
}
})(jQuery);