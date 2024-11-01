<?php defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
/**
 * Common Library Class
 * Class with all the necessary functions
 */
if ( !class_exists( 'WPMManagerLite_Libary' ) ) {
   class WPMManagerLite_Libary {
        /**
         * @param array $array
         */
        public static function displayArr( $array ) {
                echo "<pre>";
                print_r( $array );
                echo "</pre>";
        }

	  /*
      * Get Count of files not in folders
      */
      public static function get_unorganized_count_files(){
        $count_pages = wp_count_posts('attachment')->inherit;
        $custom_count_with_folders = DatabaseLite_Class::get_all_count_lists('');
        $total_unorganized_files = ( int ) $count_pages - ( int ) $custom_count_with_folders;
        return $total_unorganized_files;
      }
      
      /*
      * Create theme slug function
      */
         public static function create_unique_slug($title, $table_name)
        {
            global $wpdb;
            $slug = preg_replace("/-$/","",preg_replace('/[^a-z0-9]+/i', "-", strtolower($title)));
            $wpmmanager_folder_lists = $wpdb->get_results("SELECT * FROM $table_name where folder_slug like '%$slug'");
        
            $numHits = count($wpmmanager_folder_lists);
            return ($numHits > 0) ? ($slug . '-' . $numHits) : $slug;
        }
        
        public static function get_all_filtered_mime_types(){
            $filtered_mime_types = array();

            foreach( get_allowed_mime_types() as $key => $type ):
                if( false === strpos( $type, 'image' ) )
                    $filtered_mime_types[] = $type;
            endforeach;
            
            return $filtered_mime_types;

        }

     public static function return_unit($wt_unit,$minimum_weight,$maximum_weight){
        if (!$wt_unit || $wt_unit == 'kB') {
                $min = $minimum_weight * 1024;
                $max = $maximum_weight * 1024;
                $unit = 'kB';
            } else {
                $min = $minimum_weight * 1024 * 1024;
                $max = $maximum_weight * 1024 * 1024;
                $unit = 'MB';
            }
            $unit_arr = array('min' => $min,
              'max' => $max,
              'unit' => $unit
              );
            return $unit_arr;
    }

    public static function get_all_count_type(){
         $zip = array('application/zip', 'application/rar', 'application/x-gzip', 'application/x-7z-compressed', 'application/x-tar');
         $pdf = array('application/pdf');
         $doc = array('application/msword','application/vnd.ms-powerpoint','application/vnd.ms-write','application/vnd.ms-excel','application/vnd.ms-access','application/vnd.ms-project','application/vnd.openxmlformats-officedocument.wordprocessingml.document','application/vnd.ms-word.document.macroEnabled.12','application/vnd.openxmlformats-officedocument.wordprocessingml.template',
                            'application/vnd.ms-word.template.macroEnabled.12','application/vnd.openxmlformats-officedocument.spreadsheetml.sheet','application/vnd.ms-excel.sheet.macroEnabled.12','application/vnd.ms-excel.sheet.binary.macroEnabled.12','application/vnd.openxmlformats-officedocument.spreadsheetml.template','application/vnd.ms-excel.template.macroEnabled.12',
                            'application/vnd.ms-excel.addin.macroEnabled.12','application/vnd.openxmlformats-officedocument.presentationml.presentation','application/vnd.ms-powerpoint.presentation.macroEnabled.12','application/vnd.openxmlformats-officedocument.presentationml.slideshow','application/vnd.ms-powerpoint.slideshow.macroEnabled.12','application/vnd.openxmlformats-officedocument.presentationml.template','application/vnd.ms-powerpoint.template.macroEnabled.12','application/vnd.ms-powerpoint.addin.macroEnabled.12','application/vnd.openxmlformats-officedocument.presentationml.slide',
                            'application/vnd.ms-powerpoint.slide.macroEnabled.12','application/onenote');

        $zips_total_count          = WPMManagerLite_Libary::count_mdia_bymimemtype( $zip );
        $pdf_total_count           =  WPMManagerLite_Libary::count_mdia_bymimemtype( $pdf );
        $docs_total_count          =   WPMManagerLite_Libary::count_mdia_bymimemtype($doc );

        $count_array = array(
          'zip_cnt'   => $zips_total_count,
          'pdf_cnt'   => $pdf_total_count,
          'doc_cnt'   => $docs_total_count
          );

        return $count_array;
    }


      public static function count_mdia_bymimemtype($mimetype){
          $comma_separated = "'".implode("','", $mimetype)."'";
          global $wpdb;
          $count = 0;
          $sql = $wpdb->prepare("SELECT COUNT(ID) FROM " . $wpdb->prefix . 'posts' . " WHERE post_type = %s AND post_mime_type IN ($comma_separated) ", array('attachment'));
          $count = $wpdb->get_var($sql);
          return $count;

      }

        /**
       * Get size information for all currently-registered image sizes.
       *
       * @global $_wp_additional_image_sizes
       * @uses   get_intermediate_image_sizes()
       * @return array $sizes Data for all currently-registered image sizes.
       */
        public static function wpmdia_get_image_sizes() {
        global $_wp_additional_image_sizes;

        $sizes = array();

        foreach ( get_intermediate_image_sizes() as $_size ) {
          if ( in_array( $_size, array('thumbnail', 'medium', 'medium_large', 'large') ) ) {
            $sizes[ $_size ]['width']  = get_option( "{$_size}_size_w" );
            $sizes[ $_size ]['height'] = get_option( "{$_size}_size_h" );
            $sizes[ $_size ]['crop']   = (bool) get_option( "{$_size}_crop" );
          } elseif ( isset( $_wp_additional_image_sizes[ $_size ] ) ) {
            $sizes[ $_size ] = array(
              'width'  => $_wp_additional_image_sizes[ $_size ]['width'],
              'height' => $_wp_additional_image_sizes[ $_size ]['height'],
              'crop'   => $_wp_additional_image_sizes[ $_size ]['crop'],
            );
          }
        }

        return $sizes;
      }

      public static function get_actual_sHeight($attachments,$size){
               $height_array = array();
              foreach ($attachments as $id => $attachment) {
                  $sizes = image_get_intermediate_size($attachment->ID, $size);

                  if (!$sizes) {
                      $img_data = wp_get_attachment_metadata($attachment->ID);
                      $height_img = $img_data['height'];
                  } else {
                      $height_img = $sizes['height'];
                  }
                  $height_array[] = $height_img;
              }
              return $height_array;

      }

        /**
         * Generates random string
         *
         * @param int $length
         * @return string
         *
         * @since 1.0.0
         */
        public static function generate_random_string($length) {
            $string = '1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $random_string = '';
            for ( $i = 1; $i <= $length; $i++ ) {
                $random_string .= $string[ rand(0, 61) ];
            }
            return $random_string;
        }


           public static function get_attachment_file_size($url_attachment){
              if (file_exists($url_attachment)) {
                 $size = filesize($url_attachment);
                if ($size < 1024 * 1024) {
                   $size = round($size / 1024, 1) . ' kB';
                } else if ($size > 1024 * 1024) {
                   $size = round($size / (1024 * 1024), 1) . ' MB';
                }
              } else {
                 $size = 0;
              }

              return $size;

           }

        /*
        * Returns all the registered post types only
        */
        public static function get_registered_post_types() {
           $post_types = get_post_types();
           unset($post_types['attachment']);
           unset($post_types['product_variation']);
           unset($post_types['shop_order']);
           unset($post_types['shop_order_refund']);
           unset($post_types['shop_coupon']);
           unset($post_types['shop_webhook']);
           unset($post_types['wp1slider']);
           unset($post_types['revision']);
           unset($post_types['nav_menu_item']);
           unset($post_types['wp-types-group']);
           unset($post_types['wp-types-user-group']);
           unset($post_types['customize_changeset']);
           unset($post_types['wpcf7_contact_form']);
           unset($post_types['custom_css']);
           unset($post_types['page']);
            unset($post_types['post_tag']);
           return $post_types;
       }

	}

	//class termination
}//class exists check