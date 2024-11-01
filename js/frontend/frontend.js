/**
 *  Frontend JS Script v1.1.0
*/
var pluginimgsrc = wpmdia_frontend_data.plugin_img_src;
//https://stackoverflow.com/questions/15226538/bxslider-with-number-pagers
function resize() {
    if (jQuery(window).width() <= 1024) {
        jQuery('.wpmedia-galleries-wrapper').addClass('wpmedia-resposive-wrap');

    } else {
        jQuery('.wpmedia-galleries-wrapper').removeClass('wpmedia-resposive-wrap');
    }
}
(function ($) {
  $(document).ready(function () {
         var pageNum = 1;
         var pdfScale = 1; // make pdfScale a global variable
         var shownPdf; // another global we'll use for the buttons


     $('.wpmdia_pdf_preview_wrapper').each(function(){
        var id = $(this).attr('id');
        var url = $('#the-canvas-'+id).data('href');
        if(url != '' || url != 'undefined'){

            var pdfDoc = PDFJS.getDocument(url).then(function getPdfHelloWorld(pdf) {
            displayPage(pdf, 1);
            shownPdf = pdf;
          __TOTAL_PAGES = pdf.numPages;
          $('#'+id).find(".pdf-total-pages").text(__TOTAL_PAGES);
        });


        function displayPage(pdf, num) {
          pdf.getPage(num).then(function getPage(page) { renderPage(page); });
         $('#'+id).find(".pdf-current-page").text(num);

        }

          function renderPage(page) {
            var scale = pdfScale; // render with global pdfScale variable
            var viewport = page.getViewport(scale);
            // var canvas = document.getElementById('the-canvas');
            var canvas =  document.getElementById('the-canvas-'+id);
            var context = canvas.getContext('2d');
            canvas.height = viewport.height;
            canvas.width = viewport.width;
            var renderContext = {
              canvasContext: context,
              viewport: viewport
            };
            page.render(renderContext);
        }

        var nextbutton = document.getElementById("nextbutton-"+id);
          nextbutton.onclick = function() {
          if (pageNum >= shownPdf.numPages) {
            return;
          }
          pageNum++;
          displayPage(shownPdf, pageNum);
        }

        var prevbutton = document.getElementById("prevbutton-"+id);
        prevbutton.onclick = function() {
          if (pageNum <= 1) {
            return;
          }
          pageNum--;
          displayPage(shownPdf, pageNum);
        }


        var zoominbutton = document.getElementById("zoominbutton-"+id);
        zoominbutton.onclick = function() {
          pdfScale = pdfScale + 0.25;
          displayPage(shownPdf, pageNum);
        }

        var zoomoutbutton = document.getElementById("zoomoutbutton-"+id);
        zoomoutbutton.onclick = function() {
          if (pdfScale <= 0.25) {
            return;
          }
          pdfScale = pdfScale - 0.25;
          displayPage(shownPdf, pageNum);
        }
       }
     });

        $(window).resize(resize);
        resize();

        var wpmdia_galery_slider = {};
        var wpmdia_scgalery_slider = {};
        var wpmdia_galery_isotope = {};

        $('.wpmdia-sc-gallery-sldiers').each(function(){
        var id = $(this).data('id');
        var speed = $(this).data('speed'); // in ms
        var pause = $(this).data('pause'); // in ms
        var auto = $(this).data('auto'); // autoplay true or false
        var transition = $(this).data('transition'); // horizontal, vertical or fade
        var pager_type = $(this).data('pagertype'); // dot , disable or pagination type
        var showcaption = "false";

        var controls = $(this).data('controls'); // show controls arrow true or false

            if(pager_type == "disable"){
              var pgtype = false;
              var pagerType =  '';
            }else{
               var pgtype = true;
               var pagerType =  'full';
            }

         var slide_count = $(this).data('slide-count');

         if( slide_count != "undefined"){
                 var slidewidth = $(this).data('slidewidth');
                 var margin = $(this).data('margin');

                if(jQuery(window).width() >= 981){
                  var mslide = slide_count;
                  var slidew = slidewidth;
                  var smargin = margin;
                }
                else if(jQuery(window).width() <= 980 && jQuery(window).width() >= 801){
                  var mslide = 3; slidew = 300; smargin = margin;
                }
                else if(jQuery(window).width() <= 800 && jQuery(window).width() >= 641){
                  var mslide = 2; slidew = 240; smargin = margin;
                }
                else if(jQuery(window).width() <= 640 && jQuery(window).width() >=320){
                  var mslide = 1; slidew = 300; smargin = 0;
                }

                if(transition == "vertical"){
                    var minSlides = 2;
                }else{
                   var minSlides = 1;
                }

           wpmdia_scgalery_slider.id =  $(this).bxSlider({
                mode: transition,
                speed: speed,
                pause: pause,
                captions: showcaption,
                infiniteLoop: true,
                controls: controls,
                pager: pgtype,
                pagerType: pagerType,
                auto:auto,
                hideControlOnEnd: false,
                minSlides: minSlides,
                maxSlides: mslide,
                moveSlides: 1,
                slideWidth: slidew,
                slideMargin: smargin,
                adaptiveHeight: true,
               // nextText: next_text,
               // prevText: pre_text,
                autoHover: true
               });
         }else{


        wpmdia_scgalery_slider.id = $(this).bxSlider({
                mode: transition,
                speed: speed,
                pause: pause,
                captions: showcaption,
                infiniteLoop: true,
                controls: controls,
                pager: pgtype,
                pagerType: pagerType,
                auto:auto,
                hideControlOnEnd: false,
                adaptiveHeight: true,
                nextText: next_text,
                prevText: pre_text,
                autoHover: true
               });

         }


        });


      $('.wpmdia-gallery-sldiers').each(function(){
        var selector = $(this);
        var id = $(this).data('id');
         id = id.split('-');
         id = id[1];

        var speed = $(this).data('speed'); // in ms
        var pause = $(this).data('pause'); // in ms
        var auto = $(this).data('auto'); // autoplay true or false
        var transition = $(this).data('transition'); // horizontal, vertical or fade

         var slidertype = $(this).data('slidertype'); // single or mutiple columns

        var pager_type = $(this).data('pagertype'); // dot , disable or pagination type

        var showcaption = "false";

        var controls = $(this).data('controls'); // show controls arrow true or false
        var slide_count = $(this).data('slide-count');

            if(pager_type == "disable"){
              var pgtype = false;
              var pagerType =  '';
            }else{
               var pgtype = true;
               var pagerType =  'full';
            }

        if(slidertype == "single"){
           wpmdia_galery_slider.id = $(this).bxSlider({
                mode: transition,
                speed: speed,
                pause: pause,
                captions: showcaption,
                infiniteLoop: true,
                controls: controls,
                pager: pgtype,
                pagerType: pagerType,
                auto:auto,
                hideControlOnEnd: false,
                adaptiveHeight: true,
                // nextText: next_text,
                // prevText: pre_text,
                autoHover: true,
               });

        }else{
             var slidewidth = $(this).data('slidewidth');
             var margin = $(this).data('margin');

            if(jQuery(window).width() >= 981){
              var mslide = slide_count;
                var slidew = slidewidth;
              var smargin = margin;
            }
            else if(jQuery(window).width() <= 980 && jQuery(window).width() >= 801){
              var mslide = 3; slidew = 300; smargin = margin;
            }
            else if(jQuery(window).width() <= 800 && jQuery(window).width() >= 641){
              var mslide = 2; slidew = 240; smargin = margin;
            }
            else if(jQuery(window).width() <= 640 && jQuery(window).width() >=320){
              var mslide = 1; slidew = 300; smargin = 0;
            }

            if(transition == "vertical"){
                var minSlides = 2;
            }else{
               var minSlides = 1;
            }


           wpmdia_galery_slider.id = $(this).bxSlider({
                mode: transition,
                speed: speed,
                pause: pause,
                captions: showcaption,
                infiniteLoop: true,
                controls: controls,
                pager: pgtype,
                pagerType: pagerType,
                auto:auto,
                hideControlOnEnd: false,
                minSlides: minSlides,
                maxSlides: mslide,
                moveSlides: 1,
                slideWidth: slidew,
                slideMargin: smargin,
                adaptiveHeight: true,
                //nextText: next_text,
               // prevText: pre_text,
                autoHover: true,
                //  onSlideAfter: function(){
                //   $(".wpmdia-title").addClass("animated bounceInRight");
                // //  $(".text").addClass("animated bounceInRight");
                //   },
                //   onSlideBefore: function(){
                //   $(".wpmdia-title").removeClass("animated bounceInRight");
                // //  $(".text").removeClass("animated bounceInRight");
                //   }
               });

        }



      });



      var slideshow_speed = $('.wpmedia-galleries-wrapper').data('slideshow_speed');
      var animation = $('.wpmedia-galleries-wrapper').data('animation_speed');
      var theme = $('.wpmedia-galleries-wrapper').data('pptheme');
      var lightbox_status = $('.wpmedia-galleries-wrapper').data('lightbox_status');
       if(lightbox_status){
         $("a[rel^='prettyPhoto']").prettyPhoto({
              theme: theme,
              slideshow:slideshow_speed,
              autoplay_slideshow:true,
              social_tools: false,
              animation_speed: animation,
               overlay_gallery: false,
                markup: '<div class="pp_pic_holder"> \
            <div class="ppt">&nbsp;</div> \
            <div class="pp_top"> \
              <div class="pp_left"></div> \
              <div class="pp_middle"></div> \
              <div class="pp_right"></div> \
            </div> \
            <div class="pp_content_container"> \
              <div class="pp_left"> \
              <div class="pp_right"> \
                <div class="pp_content"> \
                  <div class="pp_loaderIcon"></div> \
                  <div class="pp_fade"> \
                    <a href="#" class="pp_expand" title="Expand the image">Expand</a> \
                    <div class="pp_hoverContainer"> \
                      <a class="pp_next" href="#">next</a> \
                      <a class="pp_previous" href="#">previous</a> \
                    </div> \
                    <div id="pp_full_res"></div> \
                    <div class="pp_details"> \
                      <div class="pp_nav"> \
                        <a href="#" class="pp_arrow_previous">Previous</a> \
                        <a href="#" class="pp_arrow_next">Next</a> \
                      </div> \
                      <p class="pp_description"></p> \
                      <div class="pp_social">{pp_social}</div> \
                      <div class="smls-close"> \
                      <a class="pp_close" href="#"></a> \
                      </div> \
                    </div> \
                  </div> \
                </div> \
              </div> \
              </div> \
            </div> \
            <div class="pp_bottom"> \
              <div class="pp_left"></div> \
              <div class="pp_middle"></div> \
              <div class="pp_right"></div> \
            </div> \
          </div> \
          <div class="pp_overlay"></div>'
          });

        $('body').on('click', '.wpmdia-zoom', function () {
        $(this).closest('.wpmdia-gallery').find('a[rel^="prettyPhoto"]').click();
        });
     }

     /*
     *
     * check window width
     */
    var WindowWidth = $(window).width();
    $(window).resize(function() {
        WindowWidth = $(window).width();
    });

     /*
     * Add responsive class for grid column
     */
    if (WindowWidth > 1024) {

        for (i = 2; i <= 4; i++) {
            if ($('.wpmedia-galleries-wrapper').hasClass('wpmdia-tablet-column-' + i + '')) {

                $('.wpmedia-galleries-wrapper').removeClass('wpmdia-tablet-column-' + i + '');
            }
        }
        for (i = 1; i <= 2; i++) {
            if ($('.wpmedia-galleries-wrapper').hasClass('wpmdia-mobile-column-' + i + '')) {

                $('.wpmedia-galleries-wrapper').removeClass('wpmdia-mobile-column-' + i + '');
            }
        }


    }
    if (WindowWidth > 740 && WindowWidth <= 1024) {
        for (i = 2; i <= 6; i++) {
            if ($('.wpmedia-galleries-wrapper').hasClass('wpmdia-grid-column-' + i + '')) {

                $('.wpmedia-galleries-wrapper').removeClass('wpmdia-grid-column-' + i + '');
            }
        }
        for (i = 1; i <= 2; i++) {
            if ($('.wpmedia-galleries-wrapper').hasClass('wpmdia-mobile-column-' + i + '')) {

                $('.wpmedia-galleries-wrapper').removeClass('wpmdia-mobile-column-' + i + '');
            }
        }
    }

    if (WindowWidth <= 740) {
        for (i = 2; i <= 6; i++) {
            if ($('.wpmedia-galleries-wrapper').hasClass('wpmdia-grid-column-' + i + '')) {

                $('.wpmedia-galleries-wrapper').removeClass('wpmdia-grid-column-' + i + '');
            }
        }
        for (i = 2; i <= 4; i++) {
            if ($('.wpmedia-galleries-wrapper').hasClass('wpmdia-tablet-column-' + i + '')) {

                $('.wpmedia-galleries-wrapper').removeClass('wpmdia-tablet-column-' + i + '');
            }
        }

    }

       $('.wpmdia_masonary_gallery').isotope({
            itemSelector: '.wpmdia-gallery-each-box',
            percentPosition: true,
            masonry: {
                // use element for option
                columnWidth: '.wpmdia-gallery-each-box'
            }
        });


  });//$(function () end
}(jQuery));
