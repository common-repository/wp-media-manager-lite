<?php defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
if ( ! class_exists( 'Gallery_Metadata_Lite' ) ) :
/**
 * Posts Media Frame Gallery Meta data Class 
*/
class Gallery_Metadata_Lite {
     /**
     * Constructor
     */
    public function __construct( ) { 
         add_action('wp_enqueue_media', array($this, 'wpmdia_gallery_admin_scripts'));
         add_filter( 'attachment_fields_to_edit', array( $this, 'wpmdia_fn_handle_edit_gallery' ), 10, 2 ); //Adding our custom fields to the $form_fields array
         add_filter( 'attachment_fields_to_save', array( $this, 'wpmdia_fn_save_edit_gallery' ), 10, 2 ); //Saving our custom fields to the $form_fields array
         add_action('wp_ajax_update_attachement_metakey', array($this, 'wpmdiafn_attachement_metakey'));
         add_action( 'print_media_templates', array($this,'wpmdia_fnaction_print_media_templates'), 10, 1 );
         add_filter( 'post_gallery', array($this,'wpmdia_admin_gallery_shortcode'), 11, 3 );
         add_action('wp_enqueue_scripts', array($this, 'fn_enqueue_gallery')); //frontend script
         add_filter('media_send_to_editor', array($this, 'wppdf_embed_modify_html'), 66, 3); // modify shortcode and insert newly created html to editor
     }


    /**
     * Modifying Html into the editor.
     * 
     * @see wp_ajax_send_attachment_to_editor(), media_upload_form_handler()
     * 
     * @param string $html The unslashed HTML to send to the editor.
     * @param in $id The attachment id.
     * @param array $attachment An array of attachment attributes.
     *
     * @return string The filtered HTML sent to the editor.
     */
       public function wppdf_embed_modify_html( $html, $id, $attachment ) {
            $post = get_post( $id );
            $pdfembed = get_post_meta($id, 'wpmedia_embed_pdf', true);

            $settings = get_option('wpmediamanager_settings');
            $enable_single_file = ((isset($settings['enable_pdf_file_design']) && $settings['enable_pdf_file_design'] == 1)?1:0);
            $linktarget   = get_post_meta($id, '_cg_link_target', true);
            $mimetype = explode("/", $post->post_mime_type);
            $metadata = get_post_meta($id, '_wp_attached_file');
            $upload_dir = wp_upload_dir();
            $url = $post->guid;
            $title = $post->post_title;
            $url_attachment = $upload_dir['basedir'] . '/' . $metadata[0];
            $size = WPMManagerLite_Libary::get_attachment_file_size($url_attachment);
            $type = wp_check_filetype($url);
            $ext = $type['ext'];


            if ( 'pdf' == $mimetype[1] ) {
                if(isset($pdfembed) && $pdfembed == 'show_link'){
                 //show_link
                        if($enable_single_file == 1){
                            //single file 
                                $html = '<div class="wpmedia_manager_ofile_wraper" data-file="' . $id . '" style="overflow: hidden;"><a target="_blank" href="'.$url .'" class="wpdmdia-dwnload-ofile"><span class="wpdmdia-content-ofile"><h3 class="wpdmai-file-title">'.$title.'</h3><span class="wpmdia_tot_size">Size : '.$size.'</span><span class="wpmdia_format_type">Format : '.strtoupper($ext).'</span></span></a></div>';
                        }else{
                                $doc = new DOMDocument();
                                libxml_use_internal_errors(true); //isable libxml errors and allow user to fetch error information as needed
                                @$doc->loadHtml($html);
                                $tags = $doc->getElementsByTagName('a');
                                if($tags->length > 0){
                                    if(!empty($tags)){
                                        $tags->item(0)->setAttribute('target',$linktarget);
                                        $html = $doc->saveHtml();
                                    }
                                }
                        }

                    }
            }
            return $html;
        }

        /**
        * Adding our custom fields to the $form_fields array
        * @param array $form_fields
        * @param object $post
        * @return array
        */
       function wpmdia_fn_handle_edit_gallery($form_fields, $post) {
           // $form_fields is a special array of fields to include in the attachment form
           // $post is the attachment record in the database  $post->post_type == 'attachment'
           // (attachments are treated as posts in Wordpress)
           // add our custom field to the $form_fields array
           // input type="text" name/id="attachments[$attachment->ID][custom1]"
        
        $info_file_extension = wp_check_filetype($post->guid);
            
        if(!empty($info_file_extension['ext']) && $info_file_extension['ext'] == 'pdf'){
            $post_value = get_post_meta($post->ID, '_wpmedia_embed_pdf', true);
            $embed_frame_type = get_post_meta($post->ID, '_wpmedia_embed_frame_type', true);
            if(empty($post_value)) $post_value = 'default';
            //if(empty($embed_frame_type)) $embed_frame_type = 'use_iframe';
            $embed_options = array(
                'default' => __('Default', WPMManagerLite_TD),
                'show_link' => __('Show Only Link', WPMManagerLite_TD),
            );
            $option = '';
            foreach ($embed_options as $k => $v) {
                if($post_value == $k){
                    $option .= '<option selected value="' . $k . '">' . $v . '</option>';
                }else{
                    $option .= '<option value="' . $k . '">' . $v . '</option>';
                }

            }
            $form_fields['wpmedia_embed_pdf'] = array(
                'label' => __('PDF FILE', WPMManagerLite_TD),
                'input' => 'html',
                'html' => '
                            <select class="wpdmia_embed_show" name="attachments[' . $post->ID . '][wpmedia_embed_pdf]" id="attachments[' . $post->ID . '][wpmedia_embed_pdf]">
                                    '.$option.'
                            </select><p class="description">Choose pdf format display on frontend.</p>'
            );
        }else{
            
           $field_value1 = get_post_meta( $post->ID, '_cg_img_link', true );
           $field_value = get_post_meta( $post->ID, '_cg_link_target', true );
           $form_fields["cg_img_link"] = array(
               "label" => __("Custom URL"),
               "input" => "html", // this is default if "input" is omitted
               'html' => '<input type="text" class="wpmdia-custom-link" id="attachments-' . $post->ID . '-cg_img_link" name="attachments[' . $post->ID . '][cg_img_link]" value="' . $field_value1 . '"> <button role="presentation" type="button" id="wpmdia-link-btn" class="wpmdia-link-btn"><i class="mce-ico mce-i-link"></i></button>'
           );
              $isSelected1 = $field_value == '_self' ? 'selected ' : '';
            $isSelected2 = $field_value == '_blank' ? 'selected ' : '';
            $isSelected3 = $field_value == '_parent' ? 'selected ' : '';
            $isSelected4 = $field_value == '_top' ? 'selected ' : '';
             
           $html = "<select class='attach_cg_link_target' name='attachments[{$post->ID}][cg_link_target]' id='attachments-' . $post->ID . '-cg_link_target'>
                            <option ".$isSelected1." value='_self'>_self</option>
                            <option ".$isSelected2."  value='_blank'>_blank</option>
                            <option ".$isSelected3."  value='_parent'>_parent</option> 
                            <option ".$isSelected4."  value='_top'>_top</option>
                           </select>";
           // We get the already saved field meta value
            $form_fields['cg_link_target'] = array(
                'label' => __( 'URL Target' ),
                'input' => 'html',
                'html' => $html
            );
         }

           return $form_fields;
       }
       
       /*
        * To save the custom fields
        */
     public function wpmdia_fn_save_edit_gallery( $post, $attachment ) {
         if (isset($attachment['cg_img_link'])) {
            update_post_meta($post['ID'], '_cg_img_link', esc_url_raw($attachment['cg_img_link']));
        }

        if (isset($attachment['cg_link_target'])) {
            update_post_meta($post['ID'], '_cg_link_target', $attachment['cg_link_target']);
        }

        if (isset($attachment['wpmedia_embed_pdf'])) {
            update_post_meta($post['ID'], 'wpmedia_embed_pdf', $attachment['wpmedia_embed_pdf']);
        }
        return $post;
    }
    
    public function wpmdiafn_attachement_metakey(){
        if ( !empty( $_POST ) && wp_verify_nonce( $_POST['mdia_nonce'], 'wpmdia-ajax-nonce' ) ) {
        $media_id = sanitize_text_field($_POST['media_id']);
        update_post_meta($media_id, '_cg_img_link', esc_url($_POST['url_link']));
        update_post_meta($media_id, '_cg_link_target', sanitize_text_field($_POST['url_target']));
        $url        = get_post_meta($media_id, '_cg_img_link');
        $url_target = get_post_meta($media_id, '_cg_link_target');
        wp_send_json(array('url' => $url[0], 'url_target' => $url_target[0]));
        }
    }
    
    /* Display settings gallery when custom gallery in back-end 
    //https://wordpress.stackexchange.com/questions/90114/enhance-media-manager-for-gallery
     **/
    public function wpmdia_fnaction_print_media_templates() {
        if ( ! isset( get_current_screen()->id ) || get_current_screen()->base != 'post' )
            return;
         include(WPMManagerLite_PATH.'/inc/backend/media-enhancement/media-gallery-settings.php');  
    }
    
    /*
     * Allows plugins and themes to override the default gallery template, 
     * ie. what the gallery shortcode return
     * Hook into the gallery shortcode and replace its output with your own.
     */
    /**
        * Use shortcode_atts_gallery filter to add new defaults to the WordPress gallery shortcode.
        * New gallery shortcode defaults (columns="2" and size="medium").
        * Allows user input in the post gallery shortcode.
        *
        * @author Marcy Diaz
        * @link http://amethystwebsitedesign.com/how-to-use-larger-images-in-a-wordpress-gallery/
    */
    public function wpmdia_admin_gallery_shortcode($output = '', $attr){
      // Initialize
      global $post;
      $iD = $post->ID;
     // Gallery instance counter
     static $instance = 0;
     $instance++;
      
       if(!empty($attr['ids'])){
       // 'ids' is explicitly ordered, unless you specify otherwise.
       if(empty($attr['orderby'])){ $attr['orderby'] = 'post__in'; }
        $attr['include'] = $attr['ids'];
      }
      
       // Validate the author's orderby attribute
    if ( isset( $attr['orderby'] ) ) {
        $attr['orderby'] = sanitize_sql_orderby( $attr['orderby'] );
        if ( ! $attr['orderby'] ) unset( $attr['orderby'] );
    }

        if(isset($attr['gallery_type']) && $attr['gallery_type'] == "grid_view" ){
            $coln = 6;
        }else{
             $coln = 3;
        }

          extract(shortcode_atts(array(
            'order'      => 'ASC',
            'orderby'    => 'menu_order ID',
            'id'         => $post ? $iD : 0,
            'columns'    => $coln,
            'size'       => 'thumbnail',
            'link'       => 'post',
            'gallery_type' => 'default',
            'wpmdia_orderby' => 'post__in', 
            'wpmdia_order' => 'ASC',
            'display_title'=> '0',
            'display_caption' => '0',
            'clink' => 0, 
            'bottomspace' => 'default', 
            'hidecontrols' => 'false',
            'include'    => '',
            'exclude'    => '',
            'class'      => ''//now you can add custom class to container UL 
          ), $attr, 'gallery'));
        // Initialize
    $id = intval( $id );
    $attachments = array();

    if ( $order == 'RAND' ) $orderby = 'none';
        $orderby  = $wpmdia_orderby;
        $order    = $wpmdia_order;
        
        if ( ! empty( $include ) ) {
 
        // Include attribute is present
         $include = preg_replace( '/[^0-9,]+/', '', $include );
         $_attachments = get_posts(array(
                        'include' => $include,
                        'post_status' => 'inherit',
                        'post_type' => 'attachment',
                        'post_mime_type' => 'image',
                        'order' => $order,
                        'orderby' => $orderby
                      ));
        // Setup attachments array
        foreach ( $_attachments as $key => $val ) {
            $attachments[ $val->ID ] = $_attachments[ $key ];
        }
 
    } else if ( ! empty( $exclude ) ) {
 
        // Exclude attribute is present 
        $exclude = preg_replace( '/[^0-9,]+/', '', $exclude );
 
        // Setup attachments array
         $attachments = get_children(array(
                        'post_parent' => $id,
                        'exclude' => $exclude,
                        'post_status' => 'inherit',
                        'post_type' => 'attachment',
                        'post_mime_type' => 'image',
                        'order' => $order,
                        'orderby' => $orderby
                      ));
    } else {
        // Setup attachments array
          $attachments = get_children(array(
                        'post_parent' => $id,
                        'post_status' => 'inherit',
                        'post_type' => 'attachment',
                        'post_mime_type' => 'image',
                        'order' => $order,
                        'orderby' => $orderby
                      ));       
        }
     if(empty($attachments)) return '';
     
      if (is_feed()) {
            $output = "\n";
            foreach ($attachments as $attid => $attachment){
                $output .= wp_get_attachment_link($attid, $size, true) . "\n";
            }
            return $output;
        }
        
        $columns = intval($columns);
        $idselector = "gallery-{$instance}";
        $custom_class = trim($class);

        $size_class = sanitize_html_class( $size );
        
        //$clink = 1 == $clink ? true : false;
        $class = 'gallery';

        if ($link == 'none') {
           $clink = 0;
        } else {
            $clink = 1;
        }
        
        if (!empty($custom_class))
            $class[] = esc_attr($custom_class);
        if ($clink == 0)
            $class[] = "gallery-clink-{$link}";

           switch ($gallery_type) {
             case "grid_view":
                require(WPMManagerLite_PATH . 'inc/shortcodes-gallery/shortcode-grid-gallery.php');
                break;
            default:
                require(WPMManagerLite_PATH . 'inc/shortcodes-gallery/shortcode-default-gallery.php');
                break;
        }
  
      return $output;   
         
    }
    
         
    public function fn_enqueue_gallery(){
        wp_enqueue_script('jquery');
        wp_enqueue_script('wpmdia-frontend-script', WPMManagerLite_FRONTEND_JS_DIR . 'frontend.js',array('jquery', 'wpmdia-prettyphoto-script','wpmdia-jquery-bxslider','wpmdia-masonary-script','wpmdia-imageloaded-script','wpmdia-pdf-embed-script','wpmdia-pdf-worker-script') ,false, WPMManagerLite_VERSION );
        $ajaxobject = array(
                  'plugin_img_src'        => WPMManagerLite_IMG_DIR, 
         );
         wp_localize_script('wpmdia-frontend-script', 'wpmdia_frontend_data', $ajaxobject ); //localization of php variable 
    }
     
     public function wpmdia_gallery_admin_scripts(){
         wp_enqueue_script('wplink');
         wp_enqueue_style( 'editor-buttons' );  

         wp_enqueue_script(WPMManagerLite_Prefix.'admin-gallery-scripts', WPMManagerLite_BACKEND_JS_DIR . 'admin-gallery.js',array('jquery','wp-color-picker') ,true, WPMManagerLite_VERSION );
         $ajax_object = array(
                  'ajax_media_url'        => admin_url('admin-ajax.php'), 
                  'ajax_media_nonce'      => wp_create_nonce('wpmdia-ajax-nonce'),
         );
         wp_localize_script( WPMManagerLite_Prefix.'admin-gallery-scripts', 'wpmdia_ajax_object', $ajax_object ); //localization of php variable 
          
       }

    

}
$global['gallery_metadata_obj'] = new Gallery_Metadata_Lite();
endif;