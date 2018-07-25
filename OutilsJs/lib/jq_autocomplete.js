//alert(58);
jQuery.fn.extend({
        autocompleting: function(urlOrData, options) {
                var isUrl = typeof urlOrData == "string";
                options = jQuery.extend({}, jQuery.autocompletering.defaults, {
                        url: isUrl ? urlOrData : null,
                        data: isUrl ? null : urlOrData,
                        delay: isUrl ? jQuery.autocompletering.defaults.delay : 10
                }, options);
                return this.each(function() {
                        new jQuery.autocompletering(this, options);
                });
        },
        result: function(handler) {
                return this.bind("result", handler);
        },
        search: function() {
                return this.trigger("search");
        }
});

jQuery.autocompletering = function(input, options) {

        var KEY = {
                UP: 38,
                DOWN: 40,
                DEL: 46,
                TAB: 9,
                RETURN: 13,
                ESC: 27,
                COMMA: 188
        };

        // Create jQuery object for input element
        var $input = $(input).attr("autocompleting", "off").addClass(options.inputClass);

        var timeout;
        var previousValue = "";
        var cache = jQuery.autocompletering.Cache(options);
        var hasFocus = 0;
        var lastKeyPressCode;
        var select = jQuery.autocompletering.Select(options, input, selectCurrent);

        $input.keydown(function(event) {
                // track last key pressed
                lastKeyPressCode = event.keyCode;
                switch(event.keyCode) {

                        case KEY.UP:
                                event.preventDefault();
                                if ( select.visible() ) {
                                        select.prev();
                                } else {
                                        onChange(0, true);
                                }
                                break;

                        case KEY.DOWN:
                                event.preventDefault();
                                if ( select.visible() ) {
                                        select.next();
                                } else {
                                        onChange(0, true);
                                }
                                break;

                        // matches also semicolon
                        case options.multiple && jQuery.trim(options.multipleSeparator) == "," && KEY.COMMA:
                        case KEY.TAB:
                        case KEY.RETURN:
                                if( selectCurrent() ){
                                        // make sure to blur off the current field
                                        if( !options.multiple )
                                                $input.blur();
                                        event.preventDefault();
                                }
                                break;

                        case KEY.ESC:
                                select.hide();
                                break;

                        default:
                                clearTimeout(timeout);
                                timeout = setTimeout(onChange, options.delay);
                                break;
                }
        }).keypress(function() {
                // having fun with opera - remove this binding and Opera submits the form when we select an entry via return
        }).focus(function(){
                // track whether the field has focus, we shouldn't process any
                // results if the field no longer has focus
                hasFocus++;
        }).click(function() {
                // show select when clicking in a focused field
                if ( hasFocus++ > 1 && !select.visible() ) {
                        onChange(0, true);
                }
                //}
//        }).blur(function() {
//                hasFocus = 0;
//                hideResults();
        }).bind("search", function() {
                function findValueCallback(q, data) {
                        var result;
                        if( data && data.length ) {
                                for (var i=0; i < data.length; i++) {
                                        if( data[i].result.toLowerCase() == q.toLowerCase() ) {
                                                result = data[i];
                                                break;
                                        }
                                }
                        }
                        $input.trigger("result", result && [result.data, result.value]);
                }
                jQuery.each(trimWords($input.val()), function(i, value) {
                        request(value, findValueCallback, findValueCallback);
                });
        });

        hideResultsNow();

        function selectCurrent() {
                var selected = select.selected();
                if( !selected )
                        return false;

                var v = selected.result;
                previousValue = v;

                if ( options.multiple ) {
                        var words = trimWords($input.val());
                        if ( words.length > 1 ) {
                                v = words.slice(0, words.length - 1).join( options.multipleSeparator ) + options.multipleSeparator + v;
                        }
                        v += options.multipleSeparator;
                }

                $input.val(v);
                hideResultsNow();
                $input.trigger("result", [selected.data, selected.value]);
                return true;
        }

        function onChange(crap, skipPrevCheck) {
                if( lastKeyPressCode == KEY.DEL ) {
                        select.hide();
                        return;
                }

                var currentValue = $input.val();

                if ( !skipPrevCheck && currentValue == previousValue )
                        return;

                previousValue = currentValue;

                currentValue = lastWord(currentValue);
                if ( currentValue.length >= options.minChars) {
                        $input.addClass(options.loadingClass);
                        if (!options.matchCase)
                                currentValue = currentValue.toLowerCase();
                        request(currentValue, receiveData, stopLoading);
                } else {
                        stopLoading();
                        select.hide();
                }
        };

        function trimWords(value) {
                if ( !value ) {
                        return [""];
                }
                var words = value.split( jQuery.trim( options.multipleSeparator ) );
                var result = [];
                jQuery.each(words, function(i, value) {
                        if ( jQuery.trim(value) )
                                result[i] = jQuery.trim(value);
                });
                return result;
        }

        function lastWord(value) {
                if ( !options.multiple )
                        return value;
                var words = trimWords(value);
                return words[words.length - 1];
        }

        // fills in the input box w/the first match (assumed to be the best match)
        function autoFill(q, sValue){
                // autofill in the complete box w/the first match as long as the user hasn't entered in more data
                // if the last user key pressed was backspace, don't autofill
                if( options.autoFill && (lastWord($input.val()).toLowerCase() == q.toLowerCase()) && lastKeyPressCode != 8 ) {
                        // fill in the value (keep the case the user has typed)
                        $input.val($input.val() + sValue.substring(lastWord(previousValue).length));
                        // select the portion of the value not typed by the user (so the next character will erase)
                        jQuery.autocompletering.Selection(input, previousValue.length, previousValue.length + sValue.length);
                }
        };

        function hideResults() {
                clearTimeout(timeout);
                timeout = setTimeout(hideResultsNow, 200);
        };

        function hideResultsNow() {
                select.hide();
                clearTimeout(timeout);
                stopLoading();
                // TODO fix mustMatch...
                if (options.mustMatch) {
                        if ($input.val() != previousValue) {
                                //selectCurrent();
                        }
                }
        };

        function receiveData(q, data) {
                if ( data && data.length && hasFocus ) {
                        stopLoading();
                        select.display(data, q);
                        autoFill(q, data[0].value);
                        select.show();
                } else {
                        hideResultsNow();
                }
        };

        function request(term, success, failure) {
                if (!options.matchCase)
                        term = term.toLowerCase();
                var data = cache.load(term);
                // recieve the cached data
                if (data && data.length) {
                        success(term, data);
                // if an AJAX url has been supplied, try loading the data now
                } else if( (typeof options.url == "string") && (options.url.length > 0) ){
                        jQuery.ajax({
                                url: options.url,
                                data: jQuery.extend({
                                        q: lastWord(term),
                                        limit: options.max
                                }, options.extraParams),
                                success: function(data) {
                                        var parsed = options.parse && options.parse(data) || parse(data);
                                        cache.add(term, parsed);
                                        success(term, parsed);
                                }
                        });
                } else {
                        failure(term);
                }
        }

        function parse(data) {
                var parsed = [];
                var rows = data.split("\n");
                for (var i=0; i < rows.length; i++) {
                        var row = jQuery.trim(rows[i]);
                        if (row) {
                                row = row.split("|");
                                parsed[parsed.length] = {
                                        data: row,
                                        value: row[0],
                                        result: options.formatResult && options.formatResult(row) || row[0]
                                };
                        }
                }
                return parsed;
        }

        function stopLoading() {
                $input.removeClass(options.loadingClass);
        }

}

jQuery.autocompletering.defaults = {
        inputClass: "ac1_input",
        resultsClass: "ac1_results",
        loadingClass: "ac_loading",
        minChars: 3,
        delay: 400,
        matchCase: false,
        matchSubset: true,
        matchContains: false,
        cacheLength: 1,
        mustMatch: false,
        extraParams: {},
        selectFirst: false,
        max: 10,
        //size: 10,
        autoFill: false,
        width: 0,
        multiple: false,
        multipleSeparator: ", "
};

jQuery.autocompletering.Cache = function(options) {

        var data = {};
        var length = 0;

        function matchSubset(s, sub) {
                if (!options.matchCase)
                        s = s.toLowerCase();
                var i = s.indexOf(sub);
                if (i == -1) return false;
                return i == 0 || options.matchContains;
        };

        function add(q, value) {
                        if (length > options.cacheLength) {
                                this.flush();
                        }
                        if (!data[q]) {
                                length++;
                        }
                        data[q] = value;
                }

        // if there is a data array supplied
        if( options.data ){
                var stMatchSets = {},
                        nullData = 0;

                // no url was specified, we need to adjust the cache length to make sure it fits the local data store
                if( !options.url ) options.cacheLength = 1;

                stMatchSets[""] = [];

                // loop through the array and create a lookup structure
                jQuery.each(options.data, function(i, rawValue) {
                        // if row is a string, make an array otherwise just reference the array


                        value = options.formatItem
                                ? options.formatItem(rawValue, i+1, options.data.length)
                                : rawValue;
                        var firstChar = value.charAt(0).toLowerCase();
                        // if no lookup array for this character exists, look it up now
                        if( !stMatchSets[firstChar] )
                                stMatchSets[firstChar] = [];
                        // if the match is a string
                        var row = {
                                value: value,
                                data: rawValue,
                                result: options.formatResult && options.formatResult(rawValue) || value
                        }

                        stMatchSets[firstChar].push(row);

                        if ( nullData++ < options.max ) {
                                stMatchSets[""].push(row);
                        }

                });

                // add the data items to the cache
                jQuery.each(stMatchSets, function(i, value) {
                        // increase the cache size
                        options.cacheLength++;
                        // add to the cache
                        add(i, value);
                });
        }

        return {
                flush: function() {
                        data = {};
                        length = 0;
                },
                add: add,
                load: function(q) {
                        if (!options.cacheLength || !length)
                                return null;
                        if (data[q])
                                return data[q];
                        if (options.matchSubset) {
                                for (var i = q.length - 1; i >= options.minChars; i--) {
                                        var c = data[q.substr(0, i)];
                                        if (c) {
                                                var csub = [];
                                                jQuery.each(c, function(i, x) {
                                                        if (matchSubset(x.value, q)) {
                                                                csub[csub.length] = x;
                                                        }
                                                });
                                                return csub;
                                        }
                                }
                        }
                        return null;
                }
        };
};

jQuery.autocompletering.Select = function (options, input, select) {
        var CLASSES = {
                ACTIVE: "ac1_over"
        };

        // Create results
        var element = jQuery("<div>")
                .hide()
                .addClass(options.resultsClass)
                .css("position", "absolute")
                .appendTo("body");

        var list = jQuery("<ul>").appendTo(element).mouseover( function(event) {
                active = jQuery("li", list).removeClass(CLASSES.ACTIVE).index(target(event));
                jQuery(target(event)).addClass(CLASSES.ACTIVE);
        }).mouseout( function(event) {
                jQuery(target(event)).removeClass(CLASSES.ACTIVE);
        }).click(function(event) {
                jQuery(target(event)).addClass(CLASSES.ACTIVE);
                select();
                input.focus();
                return false;
        });
        var listItems,
                active = -1,
                data,
                term = "";

        if( options.width > 0 )
                element.css("width", options.width);

        function target(event) {
                var element = event.target;
                while(element.tagName != "LI")
                        element = element.parentNode;
                return element;
        }

        function moveSelect(step) {
                active += step;
                wrapSelection();
                listItems.removeClass(CLASSES.ACTIVE).eq(active).addClass(CLASSES.ACTIVE);
        };

        function wrapSelection() {
                if (active < 0) {
                        active = listItems.size() - 1;
                } else if (active >= listItems.size()) {
                        active = 0;
                }
        }

        function limitNumberOfItems(available) {
                return (options.max > 0) && (options.max < available)
                        ? options.max
                        : available;
        }

        function dataToDom() {
                var num = limitNumberOfItems(data.length);
                for (var i=0; i < num; i++) {
                        if (!data[i])
                                continue;
                        function highlight(value) {
                                return value.replace(new RegExp("(" + term + ")", "gi"), "<strong>$1</strong>");
                        }
                        jQuery("<li>").html( options.formatItem
                                        ? highlight(options.formatItem(data[i].data, i+1, num))
                                        : highlight(data[i].value) ).appendTo(list);
                }
                listItems = list.find("li");
                if ( options.selectFirst ) {
                        listItems.eq(0).addClass(CLASSES.ACTIVE);
                        active = 0;
                }
        }

        return {
                display: function(d, q) {
                        data = d;
                        term = q;
                        list.empty();
                        dataToDom();
                        list.bgiframe();
                },
                next: function() {
                        moveSelect(1);
                },
                prev: function() {
                        moveSelect(-1);
                },
                hide: function() {
                        element.hide();
                        active = -1;
                },
                visible : function() {
                        return element.is(":visible");
                },
                current: function() {
                        return this.visible() && (listItems.filter("." + CLASSES.ACTIVE)[0] || options.selectFirst && listItems[0]);
                },
                show: function() {
                        // get the position of the input field right now (in case the DOM is shifted)
                        var offset = jQuery(input).offset({scroll: false, border: false});
                        // either use the specified width, or autocalculate based on form element
                        element.css({
                                width: options.width > 0 ? options.width : jQuery(input).width(),
                                //height: jQuery(listItems[0]).height() * options.size,
                                top: offset.top + input.offsetHeight,
                                left: offset.left
                        }).show();
                        //active = -1;
                        //listItems.removeClass(CLASSES.ACTIVE);
                },
                selected: function() {
                        return data && data[active];
                }
        };
}

jQuery.autocompletering.Selection = function(field, start, end) {
        if( field.createTextRange ){
                var selRange = field.createTextRange();
                selRange.collapse(true);
                selRange.moveStart("character", start);
                selRange.moveEnd("character", end);
                selRange.select();
        } else if( field.setSelectionRange ){
                field.setSelectionRange(start, end);
        } else {
                if( field.selectionStart ){
                        field.selectionStart = start;
                        field.selectionEnd = end;
                }
        }
        field.focus();
};
