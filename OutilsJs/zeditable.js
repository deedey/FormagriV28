$.fn.zcontenteditable = function(options)
{

    /*
     * version 2013-07-26 options ={callback,btnEditSaveAction}
     */

    var ACTION_EDIT,ACTION_SAVE,dataAttributs,btnEditSaveAction,me,oldTextContent,newTextContent,html,pluginContainer,contenteditableContainer;
    CONSOLE_LOG_GROUP_NAME = "zcontenteditable";

    ACTION_EDIT = "edit";
    ACTION_SAVE = "save";
   // ACTION_CANCEL = "cancel";
    jQuery.each(jQuery("[data-zcontenteditable]"), function()
    {

        // text à editer et nettoyer

        var dataAttributs = jQuery(this).data();
        oldTextContent = jQuery(this).text();
        //

        //
        oldTextContent = textFormat(oldTextContent);

        // on vide le conteneur originel
        jQuery(this).empty();
        // on construit un conteneur pour à l'intérieur contenant un div de menu
        // (d'où first) et un div de content (d'où last)
        pluginContainer = jQuery("<div /><div />").appendTo(this);

        // quel bouton mettre ?
        btnEditSaveAction = options.btnEditSaveAction || "<button data-action='" + ACTION_EDIT + "'>edit</button>";
        // on affecte le bouton avec appendTo pourqu'il soit disponible car ov
        // lui
        // ajouter un attribut
        btnEditSaveAction = jQuery(btnEditSaveAction).appendTo(pluginContainer.first());

        contenteditableContainer = pluginContainer.last();
        // on met le text de départ
        contenteditableContainer.html(oldTextContent);

        pluginContainer.on("click", "[data-action]", function()
        {

            me = jQuery(this);
            contenteditableContainer = me.parent().next();

            switch (me.attr("data-action"))
            {
                default:
                case ACTION_EDIT:
                    action = ACTION_SAVE;
                    contenteditableContainer.attr("contenteditable", true);
                    contenteditableContainer.focus();
                    contenteditableContainer.css('border','1px solid orange').
                    css('padding','4px 0 8px 0').css('background','#fff').css('height','12px');
                    Content = textChange(contenteditableContainer.html());
                    contenteditableContainer.html(Content);
                    break;

                case ACTION_SAVE:
                    //action = ACTION_CANCEL;
                    action = ACTION_EDIT;
                    newTextContent = textFormat(contenteditableContainer.html());

                    options.callback(
                    {
                        contentContainer : contenteditableContainer,
                        content : newTextContent,
                        dataAttributs : dataAttributs
                    });

                    contenteditableContainer.removeAttr("contenteditable");
                    contenteditableContainer.html(newTextContent);
                    contenteditableContainer.css('border','0').
                    css('padding','4px 0 8px 0').css('background','#F1F5F5').css('height','12px');

                    break;
                    
               /* case ACTION_CANCEL:
                    action = ACTION_EDIT;
                    contenteditableContainer.removeAttr("contenteditable");
                    contenteditableContainer.html(oldTextContent);
                    contenteditableContainer.attr('title','Cliquez ici pour renoncer à la modification');
                    options.callback(
                    {
                        contentContainer : contenteditableContainer,
                        content : oldTextContent,
                        dataAttributs : dataAttributs
                    });

                    break;*/
            }
            me.attr("data-action", action);
            if (!options.btnEditSaveAction)
            {
                me.text(action);
            }

        });
   /*
        contenteditableContainer.on("keypress", function(e)
        {
            if (e.keyCode == 13)
            {

                me = jQuery(this);
                newTextContent = textFormat(me.html());
                contenteditableContainer.html(newTextContent);
            }

        });
        */

    })

    function textFormat(content)
    {

        content = content.replace(/\s+/g, ' ');
        // content = content.replace(/<div>/gi, '<br>');
        content = content.trim(content);
        return content;
    }
    function textChange(content)
    {
        content = content.replace('----------', '');
        return content;
    }
    function log(obj)
    {
        console.log(obj);
    }

    // return this;
};