/**
 *  Plugin Backend Settings Script
*/
(function ($) {
  $(document).ready(function () {
    //ajax call url and nonce 
    var AjaxURL =  wp_mdiamanager_params.ajaxurl;
    var AjaxNonce =  wp_mdiamanager_params.wp_admin_nonce;
    var user_role = wp_mdiamanager_params.user_role;
  
    $('.colorpicker').wpColorPicker();

    /* 
    * Case 1: Tab Settings
    */
  $('ul.wpmediam-nav-tabs li a').click(function(){
      var tab_id = $(this).attr('data-tab');
      $('ul.wpmediam-nav-tabs li').removeClass('current');
      $('.wpmediam-tab-content').hide().removeClass('current');
      $(this).parent().addClass('current');
      $("#"+tab_id).fadeIn('300').addClass('current');
      if(tab_id == "aboutus" || tab_id == "how_to_use" ){
        $('.wpmediamanager-form-field').hide();
      }else{
         $('.wpmediamanager-form-field').show();
      }

   });


    $('.cb_all_size').click(function () {    
         $('input.cb_specific_row_size:checkbox').prop('checked', this.checked);    
     });

     $('.cb_all_weight').click(function () {    
         $('input.cb_specific_row_weight:checkbox').prop('checked', this.checked);    
     });

  
  });//$(function () end
}(jQuery));