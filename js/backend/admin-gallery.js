/**
 * WP Media Manager Lite Gallery Script here
*/
var folderid;
(function ($) {
  $(document).ready(function () {
          /*  On click wp Link button open url link frame */
       if ( typeof wpLink == 'undefined' )
                    return;

        $(document).on('click', '.wpmdia-link-btn', function () {
                if(typeof wpLink != "undefined"){
                     wpLink.open('wpmdia-link-btn'); /* Bind to open link editor! */
                     $('#wp-link-backdrop').show();
                     $('#wp-link-wrap').show();
                     $('#link-title-field').closest('div').hide();
                     $('.wp-link-text-field').hide();
                     $('.link-target').hide();
                     $('#url-field,#wp-link-url').closest('div').find('span').html('Custom URL');
                      $('#url-field,#wp-link-url').val($('.wpmdia-custom-link').val());
                }
           });
           $(document).on('click', '#wp-link-submit', function () {
                var mediaid = $('.attachment-details').data('id');
                if (typeof mediaid == "undefined")
                    mediaid = $('#post_ID').val();
                var link = $('#url-field').val();
                if (typeof link == "undefined") {
                    link = $('#wp-link-url').val();
                }  // version 4.2+

                var link_target = $('#link-target-checkbox:checked').val();
                if (typeof link_target == "undefined") {
//                    link_target = $('#wp-link-target:checked').val();
                   var link_target = $('.attach_cg_link_target option:selected').val();
                } // version 4.2+
              
                $.ajax({
                    type: "POST",
                    url: ajaxurl,
                    data: {
                        action: "update_attachement_metakey",
                        media_id: mediaid,
                        url_link: link,
                        url_target: link_target,
                        mdia_nonce: media_nonce
                    },
                    success: function (response) {
                        $('.wpmdia-custom-link').val(response.url);
                        $('.compat-field-cg_link_target select option[value="' + response.url_target + '"]').prop('selected', true).change();
                    }
                });
            });


    $('body').addClass('wpmdia_manager_active');

      var ajaxurl = wpmdia_ajax_object.ajax_media_url;
      var media_nonce = wpmdia_ajax_object.ajax_media_nonce;
      var media = wp.media;
        //https://wordpress.stackexchange.com/questions/90114/enhance-media-manager-for-gallery
        //https://gist.github.com/ocean90/5303385
        //https://gist.github.com/dtbaker/0c6131a7ab4f65181092
        //https://developer.wordpress.org/reference/functions/wp_print_media_templates/
        ////https://wordpress.stackexchange.com/questions/182821/add-custom-fields-to-wp-native-gallery-settings
        // https://wordpress.stackexchange.com/questions/182821/add-custom-fields-to-wp-native-gallery-settings
         // Wrap the render() function to append controls
            media.view.Settings.Gallery = media.view.Settings.Gallery.extend({
                render: function() {
                    media.view.Settings.prototype.render.apply( this, arguments );
                    this.$el.find('[data-setting="size"]').parent('label').remove();
                    this.$el.find('[data-setting="link"]').parent('label').remove();
                    this.$el.find('[data-setting="columns"]').parent('label').remove();
                    this.$el.find('[data-setting="_orderbyRandom"]').parent('label').remove();
                    this.$el.find('h2').remove();
                    // Append the custom template
                    this.$el.append( media.template( 'wpmdia-gallery-settings' ) );

                    // Save the setting
                    media.gallery.defaults.image_sizes = 'large';
                    media.gallery.defaults.gallery_type = 'default';
                    media.gallery.defaults.wpmdia_folderid = '';
                    media.gallery.defaults.wpmdia_orderby = 'post__in';
                    media.gallery.defaults.wpmdia_order = 'ASC';
                    media.gallery.defaults.display_title = '0';
                    media.gallery.defaults.display_caption = '0';
                    this.update.apply(this, ['link']);
                    this.update.apply(this, ['columns']);
                    this.update.apply(this, ['size']);
                    this.update.apply( this, ['gallery_type'] );
                    this.update.apply( this, ['wpmdia_order'] );
                    this.update.apply( this, ['wpmdia_orderby'] );
                    this.update.apply( this, ['display_title'] );
                    this.update.apply( this, ['display_caption'] );

                    return this;
                }
            } );
  });//$(function () end
}(jQuery));