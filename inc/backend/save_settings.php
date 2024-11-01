<?php defined('ABSPATH') or die("No script kiddies please!");
/**
 * Posted Data
 */
// WPMManagerLite_Libary::displayArr($_POST);
$_POST = array_map( 'stripslashes_deep', $_POST );
$settings['enable_wpmmanager']       = (isset($_POST['enable_wpmmanager']) && $_POST['enable_wpmmanager'] == 1)?'1':'0';
$settings['enable_removeall']        = (isset($_POST['enable_removeall']) && $_POST['enable_removeall'] == 1)?'1':'0';
$settings['display_medianum']        = (isset($_POST['display_medianum']) && $_POST['display_medianum'] == 1)?'1':'0';
$settings['enable_customfilters']    = (isset($_POST['enable_customfilters']) && $_POST['enable_customfilters'] == 1)?'1':'0';

if(isset($_POST['cb_specific_weight'])){
    $custom_swt_settings['cb_specific_weight'] =  WPMManagerLite_MainClass::sanitize_array($_POST['cb_specific_weight']);
    $new_weight = array();
    if(isset($custom_swt_settings['cb_specific_weight']) && !empty($custom_swt_settings['cb_specific_weight'])){
    for($i = 0; $i < count($custom_swt_settings['cb_specific_weight']);$i++){
     $r = explode(',',$custom_swt_settings['cb_specific_weight'][$i][0]);
     $new_weight[] = array($r[0] ,$r[1]);
    }
    }
    update_option('wpmedia_selected_wt_default', json_encode($new_weight));
 }

if(isset($_POST['cb_specific_size'])){ 
  $custom_selected_size_settings['cb_specific_size']  = (isset($_POST['cb_specific_size']))?array_map('sanitize_text_field', $_POST['cb_specific_size']):array();
  update_option('wpmediamanager_s_filtersize', json_encode($custom_selected_size_settings['cb_specific_size']));
 }

//gallery settings
$settings['enable_gallery_features']       = (isset($_POST['enable_gallery_features']) && $_POST['enable_gallery_features'] == 1)?'1':'0';

if(isset($_POST['gallery_image_size'])){
	$gallery_image_size = array_map('sanitize_text_field',$_POST['gallery_image_size']);
    $gallery_image_size = serialize($gallery_image_size);
    $settings['gallery_image_size']  = $gallery_image_size;
}
//pdf file settings
$settings['enable_pdf_file_design']         = (isset($_POST['enable_pdf_file_design']) && $_POST['enable_pdf_file_design'] == 1)?1:0;
$settings['show_size_amount']         = (isset($_POST['show_size_amount']) && $_POST['show_size_amount'] == 1)?1:0;
$settings['show_format_type']         = (isset($_POST['show_format_type']) && $_POST['show_format_type'] == 1)?1:0;
$settings['pdffile_bg_color']         = (isset($_POST['pdffile_bg_color']) && $_POST['pdffile_bg_color'] != '')?sanitize_text_field($_POST['pdffile_bg_color']):'';
$settings['pdffile_bg_hcolor']         = (isset($_POST['pdffile_bg_hcolor']) && $_POST['pdffile_bg_hcolor'] != '')?sanitize_text_field($_POST['pdffile_bg_hcolor']):'';
$settings['pdffile_font_color']         = (isset($_POST['pdffile_font_color']) && $_POST['pdffile_font_color'] != '')?sanitize_text_field($_POST['pdffile_font_color']):'';
$settings['pdffile_font_hcolor']         = (isset($_POST['pdffile_font_hcolor']) && $_POST['pdffile_font_hcolor'] != '')?sanitize_text_field($_POST['pdffile_font_hcolor']):'';
$settings['pdffile_font_size']         = (isset($_POST['pdffile_font_size']) && $_POST['pdffile_font_size'] != '')?sanitize_text_field($_POST['pdffile_font_size']):'';
$settings['choose_icons_type']         = (isset($_POST['choose_icons_type']) && $_POST['choose_icons_type'] != '')?sanitize_text_field($_POST['choose_icons_type']):'font_icon';
$settings['file_icon_color']         = (isset($_POST['file_icon_color']) && $_POST['file_icon_color'] != '')?sanitize_text_field($_POST['file_icon_color']):'';
$settings['file_icon_size']         = (isset($_POST['file_icon_size']) && $_POST['file_icon_size'] != '')?sanitize_text_field($_POST['file_icon_size']):'';

update_option('wpmediamanager_settings', $settings);
wp_redirect(admin_url('admin.php?page=wp-media-manager-lite&message=1'));
exit();