<?php defined('ABSPATH') or die("No script kiddies please!");
$wpmediamanager_settings     = get_option('wpmediamanager_settings');
//WPMManagerLite_Libary::displayArr($wpmediamanager_settings );
$enable_wpmmanager           = ((isset($wpmediamanager_settings['enable_wpmmanager']) && $wpmediamanager_settings['enable_wpmmanager'] == 1)?1:0);
$enable_removeall            = ((isset($wpmediamanager_settings['enable_removeall']) && $wpmediamanager_settings['enable_removeall'] == 1)?1:0);
$display_medianum            = ((isset($wpmediamanager_settings['display_medianum']) && $wpmediamanager_settings['display_medianum'] == 1)?1:0);
$enable_customfilters        = ((isset($wpmediamanager_settings['enable_customfilters']) && $wpmediamanager_settings['enable_customfilters'] == 1)?1:0);

//default and selected size
$wpmediamanager_custom_size   =  get_option('wpmediamanager_custom_dimension');
$wpmediamanager_s_filtersize  =  get_option('wpmediamanager_s_filtersize');

$wpmedia_dimension_size       = ((isset($wpmediamanager_custom_size) && !empty($wpmediamanager_custom_size))?json_decode($wpmediamanager_custom_size):array());
$wpmedia_s_dimension_size     = ((isset($wpmediamanager_s_filtersize) && !empty($wpmediamanager_s_filtersize))?json_decode($wpmediamanager_s_filtersize):array());
//default and selected weight
$wpmedia_weight_default_settings       = get_option('wpmedia_weight_default');
$wpmedia_selected_wt_default_settings  = get_option('wpmedia_selected_wt_default');

$wpmedia_weight_default                = ((isset($wpmedia_weight_default_settings) && !empty($wpmedia_weight_default_settings))?json_decode($wpmedia_weight_default_settings):array());
$wpmedia_s_weight_default              = ((isset($wpmedia_selected_wt_default_settings) && !empty($wpmedia_selected_wt_default_settings))?json_decode($wpmedia_selected_wt_default_settings):array());
//gallery settings
$enable_gallery_features      = ((isset($wpmediamanager_settings['enable_gallery_features']) && $wpmediamanager_settings['enable_gallery_features'] == 1)?1:0);
//$enable_gallery_sc            = ((isset($wpmediamanager_settings['enable_gallery_sc']) && $wpmediamanager_settings['enable_gallery_sc'] == 1)?1:0);
$selected_image_sizes         = ((isset($wpmediamanager_settings['gallery_image_size']) && !empty($wpmediamanager_settings['gallery_image_size']))?unserialize($wpmediamanager_settings['gallery_image_size']):array());

//pdf file settings
$enable_pdf_file_design = ((isset($wpmediamanager_settings['enable_pdf_file_design']) && $wpmediamanager_settings['enable_pdf_file_design'] == 1)?1:0);
$show_size_amount = ((isset($wpmediamanager_settings['show_size_amount']) && $wpmediamanager_settings['show_size_amount'] == 1)?1:0);
$show_format_type =((isset($wpmediamanager_settings['show_format_type']) && $wpmediamanager_settings['show_format_type'] == 1)?1:0);
$pdffile_bg_color                 = ((isset($wpmediamanager_settings['pdffile_bg_color']) && $wpmediamanager_settings['pdffile_bg_color'] != '')?$wpmediamanager_settings['pdffile_bg_color']:'');
$pdffile_bg_hcolor                 = ((isset($wpmediamanager_settings['pdffile_bg_hcolor']) && $wpmediamanager_settings['pdffile_bg_hcolor'] != '')?$wpmediamanager_settings['pdffile_bg_hcolor']:'');
$pdffile_font_color                 = ((isset($wpmediamanager_settings['pdffile_font_color']) && $wpmediamanager_settings['pdffile_font_color'] != '')?$wpmediamanager_settings['pdffile_font_color']:'');
$pdffile_font_hcolor                 = ((isset($wpmediamanager_settings['pdffile_font_hcolor']) && $wpmediamanager_settings['pdffile_font_hcolor'] != '')?$wpmediamanager_settings['pdffile_font_hcolor']:'');
$pdffile_font_size                 = ((isset($wpmediamanager_settings['pdffile_font_size']) && $wpmediamanager_settings['pdffile_font_size'] != '')?$wpmediamanager_settings['pdffile_font_size']:'12');
$file_icon_color                 = ((isset($wpmediamanager_settings['file_icon_color']) && $wpmediamanager_settings['file_icon_color'] != '')?$wpmediamanager_settings['file_icon_color']:'');
$file_icon_size                 = ((isset($wpmediamanager_settings['file_icon_size']) && $wpmediamanager_settings['file_icon_size'] != '')?$wpmediamanager_settings['file_icon_size']:'15');
?>
<div class="wpmedia-main-wrapper">
<div class="wpmedia-settings-main-wrapper">
     <div class="wpmedia-header">
      <?php include_once('panel-head.php');?>
     </div>

      <?php if(isset($_GET['restore_message'])){ ?>
         <div class="notice notice-success wpmediam-message">
           <p><?php _e('Restored Default Settings Successfully.',WPMManagerLite_TD);?></p>
        </div>
      <?php } ?>
      <?php if(isset($_GET['message'])){ ?>
         <div class="notice notice-success wpmediam-message">
           <p><?php _e('WP Media Manager Lite Settings Saved Successfully.',WPMManagerLite_TD);?></p>
        </div>
      <?php } ?>

    <div class="wpmediam-container wpmediamanager-tab-container">
        <div class="row">
          <div class="wpmdia_mainwrapper clearfix">
            <div class="wpmdia_second-wrapper">
          <ul class="wpmediam-nav-tabs wpmediam-tabs-left">
                <li class="tab-link current"><a class="tab_settings" data-tab="general_settings" data-toggle="tab"><?php _e('General Settings',WPMManagerLite_TD);?></a></li>
                <li class="tab-link"><a data-tab="custom_filters" class="custom_filters" data-toggle="tab"><?php _e('Custom Filters',WPMManagerLite_TD);?></a></li>
                <li class="tab-link"><a data-tab="image_settings" class="image_settings" data-toggle="tab"><?php _e('Gallery Settings',WPMManagerLite_TD);?></a></li>
                <li class="tab-link"><a data-tab="pdf_file_settings" class="pdf_file_settings" data-toggle="tab"><?php _e('PDF File Settings',WPMManagerLite_TD);?></a></li>
                <li class="tab-link"><a data-tab="how_to_use" class="how_to_use" data-toggle="tab"><?php _e('How To Use',WPMManagerLite_TD);?></a></li>
                <li class="tab-link"><a data-tab="aboutus" class="aboutus" data-toggle="tab"><?php _e('More WordPress Stuff',WPMManagerLite_TD);?></a></li>
              </ul>
            </div>

<div class="wpmediamanager-content">
        <form action="<?php echo admin_url('admin-post.php'); ?>" method="post" enctype="multipart/form-data">
                <input type="hidden" name="action" value="wpmediamanagerlite_save_settings" />
                 <?php wp_nonce_field('wpmediamanager-nonce','wpmediamanager-nonce-setup');?>
                          <!-- Tab panes -->
                          <div class="wpmediam-tab-pane">
                            <div class="wpmediam-tab-content current " id="general_settings">
                               <?php include_once('tabs/main_settings.php');?>
                            </div>
                              <div class="wpmediam-tab-content" id="custom_filters">
                                <?php include_once('tabs/custom-filters.php');?>
                            </div>
                              <div class="wpmediam-tab-content" id="image_settings">
                                <?php include_once('tabs/gallery-settings.php');  ?>
                            </div>
                            <div class="wpmediam-tab-content" id="pdf_file_settings">
                                <?php include_once('tabs/pdf-file-settings.php');?>
                            </div>
                             <div class="wpmediam-tab-content" id="how_to_use">
                                <?php include_once('tabs/how-to-use.php');?>
                            </div>
                             <div class="wpmediam-tab-content" id="aboutus">
                                <?php include_once('tabs/about.php');?>
                            </div>
                          </div>

                <div class="wpmediamanager-field-wrapper wpmediamanager-form-field">
                      <input type="submit" class="button button-primary" id="wpmediamanager-add-button" name="wpmedia_settings_submit_btn" value="<?php _e('Save',WPMManagerLite_TD);?>"/>
                       <input type="submit" class="button button-primary" id="restore_settings_btn" name="wpmedialite_restore_old_settings" value="<?php _e('Restore Default Settings',WPMManagerLite_TD);?>"/>
                </div>
    </form>
</div>

</div>
</div>
</div>
</div>

<div class="postbox-container">
  <img src="<?php echo WPMManagerLite_IMG_DIR;?>/upgradetoprobutton.png"/><br/>
  <div class="wpmdia-button-wrap-backend">
        <a href="http://demo.accesspressthemes.com/wordpress-plugins/wp-media-manager/" class="wpmm-demo-btn" target="_blank">Demo</a>
        <a href="https://accesspressthemes.com/wordpress-plugins/wp-media-manager/" target="_blank" class="wpmm-upgrade-btn">Upgrade</a>
        <a href="https://accesspressthemes.com/wordpress-plugins/wp-media-manager/" target="_blank" class="wpmm-upgrade-btn">Plugin Information</a>
</div>
  <img src="<?php echo WPMManagerLite_IMG_DIR;?>/banner.png"/>
</div>
</div>



