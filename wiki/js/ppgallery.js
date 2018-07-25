//##################################################################################
//##################################################################################
//##################################################################################
// ppGallery by jason pham (ppplugins.com) Copyright 2010 Jason Pham
// ppGallery version 1.2
//
//Modifié par dey Bendifallah d.bendifallah@gmail.com
// fonctionne désormais avec thickbox et choix d'image avec fermeture de la thickbox
//
//
//
/*
This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

(function($){
 $.fn.ppGallery = function(options) {
        var defaults = {
                screenFade: 0.8, //fade screen level. default 0.8
                screenColor: '#000000', //choose color of background. default black
                showTitle: 1, //toggles to show the title. default 1 (1= yes, 0= no)
                thumbWidth: 60, //control the gallery thumbnail size. default 60(pixels)
                thumbHeight: 40, //control the gallery thumbnail size. default 40(pixels)
                maxWidth: '', //control the max width of the large image as well as the thumbnail box. leave blank for no restrictions
                slideShowDelay: '3' //control the slideshow interval. defaults at 3 seconds
        };

        //get the id name of the gallery list
        var __galleryTable = $(this).attr('id');
        var __galleryTableID = '#'+__galleryTable;

        var options = $.extend(defaults, options);

        $(document).ready(function() {

                //disable spacebar to scroll down
                window.onkeydown=function(e){
                  if(e.keyCode==32){
                        return false;
                  }
                };

                var _h = $(document).height();
                var _w = $(document).width();

                setCookie('show_instru',0,1000);

                options.slideShowDelay = options.slideShowDelay * 1000;
//
                $('body').append('<style> body img{ -moz-user-select: none; -khtml-user-select: none; }#coverpp{ background-color:'+ options.screenColor +';position:absolute; left:0px; top:0px; display:none; z-index:10; text-align:center; -moz-user-select: none; -khtml-user-select: none; } #lightBoxGallerypp div{ -moz-user-select: none; -khtml-user-select: none; }</style><div id="coverpp"></div><div id="lightBoxGallerypp" style="position:absolute; z-index:100; display:none; text-align:center; background-color:'+options.screenColor +';"><div id="thumbListpp" style="display:none: overflow:hidden; overflow-x:scroll;border:5px solid '+options.screenColor+'; background:'+options.screenColor+'; margin: 10px auto 0 auto; padding:0 0 5px 0;"><div id="thumbImgpp"></div></div><div style="clear:both; height:0px;"></div><div id="cpanelpp" style="margin: 5px auto 0 auto; height:30px;"><div id="closeBoxpp" title="Cliquez ici pour fermer la galerie.">Fermer</div><div id="playpp"></div><div id="nextButtonBoxpp" class="nextButton">Suivante &raquo;</div><div id="xofpp"></div><div id="previousButtonBoxpp" class="previousButton">&laquo; Précédente</div></div><div style="clear:both; height:0px;"></div><div class="borderBoxpp" style="margin:0 auto; display:none; background:'+options.screenColor+';" title="Cliquez sur cette image ou appuyez sur la touche -Entrée- pour la sélectionner."></div><div id="imageTitlepp" style="text-align:center; padding:5px 0 5px 0; height:20px"></div><div class="fadeLoaderpp" style="display:none; color:white; width:18px; margin:0 auto; text-align:center; height:18px;"><img src="images/loader.gif"></div></div><input type="text" style="height:0; width:0; background-color:'+options.screenColor+'; border:0; cursor:none; position:absolute; visibilty:hidden; z-index:3; top: 0px; left: 0px;"id="fakeFocuspp"><div style="height:0; width:0; background-color:'+options.screenColor+'; width:100%; padding:10px; height:10px; z-index:300; color:#ccc; top: 0px; left: 0px;border-bottom:1px solid #333; display:none; position:fixed; text-align:center;" id="instructionspp">Utiliser les indications, les touches de direction ou cliquez sur une vignette pour naviguer.</div>');

                var totalImgW;
                var ii = 0;
                $(__galleryTableID +' li img').each(function() {
                        var liImg = $(this).attr('src');
                        ii = ii+1;
                        $('#thumbImgpp').append('<img src="'+ liImg +'" style="margin:0 10px 0 0; cursor:pointer; width:'+ options.thumbWidth +'px;max-height:'+options.thumbHeight+'px;" class="thumbNail_'+ ii +'" thumbImageId="'+ ii +'">');
                });

                $('#lightBoxGallerypp').hide();
                $('#thumbListpp').hide();
                //$('#cpanelpp').hide();
                $('#instructionspp').hide();
                $('#fakeFocuspp').fadeTo(0,0);

                //assign all 'li' an id to reference
                var countImg = 1;
                $(__galleryTableID).find('li a').each(function(){
                                $(this).attr('numid',countImg);
                                $(this).addClass('aid_'+countImg);
                                $(this).find('img').addClass('imgid_'+countImg);
                                countImg = countImg + 1;
                });

                //check for hash in url to show img
                if(window.location.hash) {
                        var hashline = window.location.hash;
                        var hashes = hashline.split('#');
                        if( hashes[1] != null && hashes[1] != '')
                                fadeInImage(hashes[1]);
                }

                //make all 'li' in the list buttons

                $(__galleryTableID).find('li a').click(function(){
                        var __largeImg = $(this).attr('href');
                        fadeInImage(__largeImg);
                        return false;
                });

                document.onkeyup = KeyPressHappened;

        });


        function KeyPressHappened(e)
        {
          if (!e) e=window.event;
          if(e != '')
          {
                  if (e.keyCode==37)//press left arrow to go back
                  {
                         code = e.keyCode;
                         $('.previousButton').trigger('click');
                  }
                  else if (e.keyCode==13)//press enter to make choice
                  {
                         code = e.keyCode;
                         $('.borderBoxpp img').trigger('click');
                  }
                  else if(e.keyCode==39 || e.keyCode==32)//press right arrow or spacebar  to go forward
                  {
                         code = e.keyCode;
                         $('#nextButtonBoxpp').trigger('click');
                  }

                  return false;
          }
        }


        function showCurtains(){

                var _h = $(document).height();
                var _w = $(document).width();

                //$(window).scrollTop(0);

                document.documentElement.style.overflow = 'hidden';         // firefox, chrome
                document.body.scroll = "no";        // ie only

                //$(document).scrollTop(0);
                //$(window).scrollTop(0);

                var currentTop = parseInt($(window).scrollTop());
                newImgW = 550;
                if(options.maxWidth != '')
                {
                        if(newImgW > options.maxWidth)
                        {
                                //get the ration of the difference so we can figure the new height
                                //newImgH = Math.floor(parseInt(newImgH / (newImgW / options.maxWidth)));
                                ///newImgW = parseInt(options.maxWidth);
                                var thisSrc = $('.borderBoxpp img').attr('src');

                                var newImg = new Image;
                                newImg.src = thisSrc;
                                //newImgW = newImg.width;
                                //newImgH = newImg.height;
                                newImgW = thisSrc.width;
                                newImgH = thisSrc.height;
                                $('.borderBoxpp img').css({'width': newImgW+'px'});
                                ///$('.borderBoxpp img').css({'width': options.maxWidth+'px'});
                        }
                }

//                $('#lightBoxGallerypp').css({'left': ''+ Math.floor(((_w / 2.2) - (newImgW / 2))) +'px', 'width': newImgW + 100 +'px', 'top': (currentTop + (_h / 30)) +'px'});
                $('#lightBoxGallerypp').css({'left':0+'px', 'width': newImgW + 100 +'px', 'top':0 +'px'});


                $('#playpp').html('');

                $('#coverpp').css({'height':_h, 'width':_w});
                $('#coverpp').fadeTo('slow', options.screenFade);

                $('#cpanelpp').fadeIn();
                $('#lightBoxGallerypp').fadeIn();

                if(options.showTitle == 1)
                        $('#imageTitlepp').fadeIn();

                $(window).resize(function() {
                        resizeWindow();
                });

                $('#thumbImgpp img').click(function(){
                        $('#imageTitlepp').html('');
                        // we fade the input box in so we can focus to it again.
                          $('#fakeFocuspp').fadeTo(0,0);

                        var thumbClicked = $(this).attr('thumbImageId');
                        var thisImageLoad = $(__galleryTableID+' li:nth-child('+thumbClicked+') a').attr('href');
                        slideShowControl('stop');
                        fadeInImage(thisImageLoad);

                });


                var show_instru = '';
                var have_seen_instru = getCookie(show_instru);
                if(have_seen_instru != 1)
                {
                        $('#instructionspp').delay(1000).slideDown(500).delay(3500).slideUp(500);
                        setCookie('show_instru',1,1000);
                }



                //$('#playpp').click(function(){
                        //slideShowControl('Faire défiler');
                //});

                $('#closeBoxpp').click(function(){
                        closelightBoxGallery();
                });

                $('#coverpp').click(function(){
                        closelightBoxGallery();
                });

                $('#coverpp').mouseenter(function(){
                        hideControls();
                });

                $('#lightBoxGallerypp').mouseenter(function(){
                        showControls();
                });

                return false;

        }

        function getCookie(c_name)
        {
        if (document.cookie.length>0)
          {
          c_start=document.cookie.indexOf(c_name + "=");
          if (c_start!=-1)
                 {
                 c_start=c_start + c_name.length+1;
                 c_end=document.cookie.indexOf(";",c_start);
                 if (c_end==-1) c_end=document.cookie.length;
                 return unescape(document.cookie.substring(c_start,c_end));
                 }
          }
        return "";
        }

        function setCookie(c_name,value,expiredays)
        {
                var exdate=new Date();
                exdate.setDate(exdate.getDate()+expiredays);
                document.cookie=c_name+ "=" +escape(value)+
                ((expiredays==null) ? "" : ";expires="+exdate.toUTCString());
        }

        function fadeInImage(__largeImg){


                $('#imageTitlepp').html('');

                $('.borderBoxpp img').fadeOut();
                $('.borderBoxpp').html('<img src="'+ __largeImg +'" border="0" class="nextButton" style="display:none;">');

                var _w = $(window).width();
                var _h = $(window).height();
                //var currentTop = parseInt($(window).scrollTop());
                var winH = $(document).width();

                var imgThumbCount = $(__galleryTableID).find('li:last-child a').attr('numid');
                var imgThumbPaddingRt = parseInt($('.thumbNail_1').css('margin-right'));
                var imgThumbPaddingLt = parseInt($('.thumbNail_1').css('margin-left'));

                $('.borderBoxpp img').one('load', function() {

                        var thisSrc = $('.borderBoxpp img').attr('src');

                        var newImg = new Image;
                        newImg.src = thisSrc;
                        //newImgW = newImg.width;
                        //newImgH = newImg.height;
                        newImgW = thisSrc.width;
                        newImgH = thisSrc.height;

                        $('.fadeLoaderpp').fadeOut(function(){

                                //if(newImgW < 700)
                                        //newImgW = 700;
                                if(newImgW > 700)
                                        newImgW = 700;
                                if(newImgH > 400)
                                        newImgH = 400;

                                if(options.maxWidth != '')
                                {
                                        if(newImgW > options.maxWidth)
                                        {
                                                //get the ration of the difference so we can figure the new height
                                                //newImgH = Math.floor(parseInt(newImgH / (newImgW / options.maxWidth)));
                                                newImgW = parseInt(options.maxWidth);
                                                $('.borderBoxpp img').css({'width': options.maxWidth+'px'});
                                        }
                                }

                                //$('#lightBoxGallerypp').css({'left': ''+ Math.floor(((_w / 2.3) - (newImgW / 2))) +'px', 'width': newImgW + 100 +'px', 'top': (currentTop + (winH / 30)) +'px'});

                                /*$('#lightBoxGallerypp:not(img)').css({
                                        '-ms-filter':'progid:DXImageTransform.Microsoft.Alpha(Opacity=50)', 'filter': 'alpha(opacity=50)', 'opacity': '.5'
                                });*/

                                $('#lightBoxGallerypp').animate({
                                        height: _h+ 'px'
                                        //height: newImgH + 170 + 'px'
                                }, 500, function() {
                                        showTitles();
                                });

                                $('#cpanelpp').css({'width': newImgW +'px'});
                                $('.borderBoxpp').css({'display':'block'});
                                $('#imageTitlepp').css({'width': newImgW + 100 +'px'});
                                //$('#thumbListpp').css({'width': newImgW+'px'});
                                $('#thumbImgpp').css({'width': (imgThumbCount * options.thumbWidth) + ((imgThumbPaddingRt + imgThumbPaddingLt) * imgThumbCount) + 50 +'px'});

                                showImage(thisSrc);
                                return false;

                        });

                }).each(function() {
                        var thisSrc = $('.borderBoxpp img').attr('src');
                        var newImg = new Image;
                        newImg.src = thisSrc;

                        if(newImg.complete)
                                $(this).trigger('load');
                        else
                                $('.fadeLoaderpp').show();

                });


                return false;

        };

        function showImage(__largeImg){

                $('.fadeLoaderpp').hide();

                $('.borderBoxpp img').css('cursor','pointer');
                var boxState = $('#coverpp').css('display');
                if(boxState == 'none')
                        showCurtains();

                var _w = $(window).width();

                $('.nextButton').unbind();
                $('.previousButton').unbind();
                //$('#playpp').unbind();

                window.location.hash = __largeImg;

                $('.previouspp').removeClass('previouspp');
                $('.activepp').removeClass('activepp');
                $('.nextpp').removeClass('nextpp');
                $("a[href="+ __largeImg +"]").addClass('activepp');

                //get the numid so we can figure out which one is next and which is previous
                var activeThumbId = $(__galleryTableID+' .activepp').attr('numid');
                var nextUp = parseInt(activeThumbId) + 1;
                var lastUp = parseInt(activeThumbId) - 1;

                $(".aid_"+ nextUp).addClass('nextpp');
                $(".aid_"+ lastUp).addClass('previouspp');


                $('.borderBoxpp img').fadeIn( function(){

                        //focus the cursor here to take it away from the thumbbox. this is so the scroll window won't move when the arrow keys are press
                        $('#fakeFocuspp').focus().hide();

                        $('.borderBoxpp img').bind('click', function(){
                                var HashUrl =__largeImg.split('&');
                                var NumPrg = HashUrl[2];
                                var NumImg = HashUrl[3];
                                parent.$('#Image'+NumPrg+'').html('<img src=lib/affiche_image.php?provenance=paragraphe&numImg='+NumImg+'>');
                                parent.$('#file'+NumPrg+'').html('<input type=hidden name=id_wkimg value='+NumImg+' />');
                                parent.$('#Zero'+NumPrg+'').html('<input type=hidden name=userfile value=0 />');
                                parent.$('input[id=fichier'+NumPrg+']').empty();
                                top.tb_remove();
                        });
                        $('.nextButton').bind('click', function(){
                                handleNext();
                                slideShowControl('stop');
                        });

                        $('.nextButton').bind('focus', function(){
                                handleNext();
                        });

                        $('.previousButton').bind('click',function(){
                                slideShowControl('stop');
                                handlePrevious();
                        });

                        //$('.fadeLoaderpp').fadeOut();
                        showTitles();
                });

                scrollToThumb(activeThumbId);

                //handle number of series
                var _thisNumId = $('.activepp').attr('numid');
                var _lastNumId = $(__galleryTableID).find('li:last-child a').attr('numid');
                $('#xofpp').html(_thisNumId +' / '+ _lastNumId);

                //preload next img
                var _img_to_load_next = $('.nextpp').attr('href');
                if(_img_to_load_next != '' && _img_to_load_next != null)
                {
                        preload(_img_to_load_next);
                }
                else
                {
                        //preload first image
                        var _img_to_load_first = $(__galleryTableID).find('li:first-child a').attr('href');
                        preload(_img_to_load_first);

                        //preload last image
                        var _img_to_load_last = $(__galleryTableID).find('li:last-child a').attr('href');
                        preload(_img_to_load_last);
                }

                //preload previous image
                var _img_to_load_prev = $('.previouspp').attr('href');
                if(_img_to_load_prev != '' && _img_to_load_prev != null)
                        preload(_img_to_load_prev);

                return false;
        }

        function scrollToThumb(activeThumbId){

                $('#thumbImgpp img').unbind('mouseover, mouseout');

                var imgThumbPaddingRt = parseInt($('.thumbNail_1').css('margin-right'));
                var imgThumbPaddingLt = parseInt($('.thumbNail_1').css('margin-left'));
                var thumbImgppW = $('#thumbListpp').width();


                var thumbBox_state = $('#thumbListpp').css('display');

                if(thumbBox_state == 'none'){
                        $('#thumbListpp').fadeIn(1000);
                }

                $('#thumbListpp').animate({
                        scrollLeft: options.thumbWidth * activeThumbId + ((imgThumbPaddingRt + imgThumbPaddingLt) * activeThumbId) - (thumbImgppW / 2)
                }, 700, function() {
                        // Animation complete.
                });

                $('#thumbImgpp img').fadeTo('fast',.5);
                $('.activeThumb').removeClass('activeThumb');
                $('.thumbNail_'+activeThumbId).addClass('activeThumb');
                $('.thumbNail_'+activeThumbId).fadeTo(1, 1);

                $('#thumbImgpp img').bind('mouseover',function(){
                        $(this).fadeTo(1, 1);
                });

                $('#thumbImgpp img:not(.activeThumb)').bind('mouseout', function(){
                        $(this).fadeTo(400, .5);
                });

                return false;
        }

        function showTitles(){
                //handle title
                var _thisTitle = $('.activepp').attr('title');
                if(_thisTitle != '' && _thisTitle != null)
                        $('#imageTitlepp').html(_thisTitle);
                else
                        $('#imageTitlepp').html('');

                return false;
        }

        function preload(arrayOfImages){
                var newImg = new Image;
                newImg.src = arrayOfImages;
        }

        function hideControls(){
                $('#cpanelpp div').delay(500).fadeOut(200);
        }

        function showControls(){
                $('#cpanelpp div').delay(0).fadeIn(200);
        }

        var handleNext = function(){
                $('.borderBoxpp img').hide();

                var __largeImg = $('.nextpp').attr('href');

                if(__largeImg == null || __largeImg == '' || __largeImg == 'undefined')
                {
                        var __largeImg = $(__galleryTableID).find('li:first-child a').attr('href');
                        fadeInImage(__largeImg);
                        $(__galleryTableID).find('li:first-child').find('a').addClass('activepp');
                        $(__galleryTableID).find('li:last-child').find('a').addClass('previouspp');

                        var activeThumbId = $(__galleryTableID+' .activepp').attr('numid');
                        var nextUp = parseInt(activeThumbId) + 1;
                        $(".aid_"+ nextUp).addClass('nextpp');

                }
                else
                {
                        fadeInImage(__largeImg);
                }

                return false;
        }
        function retour(){
                        $('.borderBoxpp img').bind('click', function(){
                                var HashUrl =__largeImg.split('&');
                                var NumPrg = HashUrl[2];
                                var NumImg = HashUrl[3];
                                parent.$('#Image'+NumPrg+'').html('<img src=lib/affiche_image.php?provenance=paragraphe&numImg='+NumImg+'>');
                                parent.$('#file'+NumPrg+'').html('<input type=hidden name=id_wkimg value='+NumImg+' />');
                                parent.$('#Zero'+NumPrg+'').html('<input type=hidden name=userfile value=0 />');
                                parent.$('input[id=fichier'+NumPrg+']').empty();
                                top.tb_remove();
                        });
        }

        function handlePrevious(){
                $('.borderBoxpp img').hide();
                var __largeImg = $('.previouspp').attr('href');

                if(__largeImg == null || __largeImg == '' || __largeImg == 'undefined')
                {
                        var __largeImg = $(__galleryTableID).find('li:last-child a').attr('href');
                        fadeInImage(__largeImg);
                        $(__galleryTableID).find('li:last-child').find('a').addClass('activepp');
                        $(__galleryTableID).find('li:last-child').find('a').addClass('previouspp');

                        var activeThumbId = $(__galleryTableID+' .activepp').attr('numid');
                        var nextUp = parseInt(activeThumbId) + 1;
                        $(".aid_"+ nextUp).addClass('nextpp');

                }
                else
                {
                        fadeInImage(__largeImg);
                }

                return false;
        }

        function closelightBoxGallery(){

                $('.nextButton, .previousButton, #playpp, #thumbImgpp img, #lightBoxGallerypp').unbind();

                slideShowControl('close');

                $('#cpanelpp, #cpanelpp div, #lightBoxGallerypp').fadeOut();
                $('#coverpp').delay(300).fadeOut(function(){
                        $('#coverpp').unbind();
                        $('#closeBoxpp').unbind();
                        document.documentElement.style.overflow = 'auto';         // firefox, chrome
                        document.body.scroll = "yes";        // ie only
                        window.location.hash = '';
                        $('.previouspp').removeClass('previouspp');
                        $('.activepp').removeClass('activepp');
                        $('.nextpp').removeClass('nextpp');
                });

                $('#instructionspp').hide();

                return false;
        }

        var slideShowControl = function(x){

                if(x == 'close')
                {
                        var slideVar = $('body').data('slideControl');
                        clearInterval(slideVar);
                        $('body').data('slideControlactive', '0');

                        $('#playpp').unbind(function(){
                                $('#playpp').fadeOut(function(){
                                });
                        });
                }
                else if(x == 'Faire défiler')
                {
                        var runSlide = setInterval("$('#nextButtonBoxpp').trigger('focus')", options.slideShowDelay);
                        $('body').data('slideControl', runSlide);
                        $('body').data('slideControlactive', '1');
                        $('#playpp').html('Stop');

                }
                else if(x == 'Stop')
                {
                        var slideVar = $('body').data('slideControl');
                        clearInterval(slideVar);
                        $('body').data('slideControlactive', '0');
                        $('#playpp').html('');

                }

                return false;
        }

        function resizeWindow(){
                var _h = $(document).height();
                var _w = $(window).width();

                var thisSrc = $('.borderBoxpp img').attr('src');
                var newImg = new Image;
                newImg.src = thisSrc;
                newImgW = thisSrc.width;
                newImgH = thisSrc.height;
                //newImgW = newImg.width;
                //newImgH = newImg.height;

                if(newImgW > 700)
                        newImgW = 700;

                if(options.maxWidth != '')
                        {
                                if(newImgW > options.maxWidth)
                                {
                                        //get the ration of the difference so we can figure the new height
                                        newImgH = Math.floor(parseInt(newImgH / (newImgW / options.maxWidth)));

                                        newImgW = parseInt(options.maxWidth);
                                        //$('.borderBoxpp img').css({'width': options.maxWidth+'px'});
                                }
                        }

                $('#lightBoxGallerypp').css({'left': ''+ Math.floor(((_w / 2.2) - (newImgW / 2))) +'px', 'height':newImgH + 170 + 'px'});
                $('#coverpp').css({'height':_h, 'width':_w});

                return false;
        };

        };
})(jQuery);