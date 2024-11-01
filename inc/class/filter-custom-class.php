<?php defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
if ( ! class_exists( 'FilterCustomClass_Lite' ) ) :
/**
 * FilterCustomClass_Lite Class 
*/
class FilterCustomClass_Lite {
    
     /**
     * Constructor
     */
    public function __construct() {
        add_action('pre_get_posts',array( $this,'fn_filter_query_custom_size_grid'), 1,1000);
        add_action('pre_get_posts',array( $this,'fn_filter_query_custom_size_table'));
     }

     /* 
     * Filter Query Posts for Custom Size Dropdown For Grid View
     */
     public function fn_filter_query_custom_size_grid($wp_query){

      if (!isset($wp_query->query_vars['post_type']) || $wp_query->query_vars['post_type'] != 'attachment')
            return;

       $query_data = array();
           $custom_size = (isset($_REQUEST['query']['wpmdia_custom_size']))?sanitize_text_field($_REQUEST['query']['wpmdia_custom_size']):'all';
           $custom_weight = (isset($_REQUEST['query']['wpmdia_custom_weight']))?sanitize_text_field($_REQUEST['query']['wpmdia_custom_weight']):'all';

             if (isset($custom_size) && $custom_size != "all" && isset( $custom_weight) &&  $custom_weight != "all") {
                 // other than all value
                 $query_data = DatabaseLite_Class::wpmdia_get_posts_attachement_ids($custom_size,$custom_weight);

              }else if(isset($custom_size) && $custom_size != "all" && isset( $custom_weight) && $custom_weight == "all"){

                 $query_data = DatabaseLite_Class::wpmdia_get_posts_attachement_ids($custom_size,'');

              }else if(isset($custom_size) && $custom_size == "all" && isset( $custom_weight) &&  $custom_weight != "all"){

                 $query_data = DatabaseLite_Class::wpmdia_get_posts_attachement_ids('',$custom_weight);

              }
           
              if(!empty($query_data)){
                $wp_query->query_vars['post__in'] = $query_data;
             }
          return $wp_query;

     }

   /* 
   * Query post by size and weight for table view
   */
    public function fn_filter_query_custom_size_table($wp_query) {
        if (!isset($wp_query->query_vars['post_type']) || $wp_query->query_vars['post_type'] != 'attachment')
            return;
       global $pagenow;
       if ($pagenow == 'upload.php') {
           $custom_size = (isset($_GET['wpmdia_custom_size']))?sanitize_text_field($_GET['wpmdia_custom_size']):'all';
           $custom_weight = (isset($_GET['wpmdia_custom_weight']))?sanitize_text_field($_GET['wpmdia_custom_weight']):'all';
            $query_data = array();
            if ((isset($custom_size) && $custom_size != 'all') && (empty($custom_weight) || $custom_weight == 'all')) {

                $query_data = DatabaseLite_Class::wpmdia_get_posts_attachement_ids($custom_size, '');
            }

            if ((isset($custom_weight) && $custom_weight != 'all' ) && (empty($custom_size) || $custom_size == 'all')) {

                $query_data = DatabaseLite_Class::wpmdia_get_posts_attachement_ids('', $custom_weight);
            }

            if ((isset($custom_size) && $custom_size != 'all') && (isset($custom_weight) && $custom_weight != 'all')) {

                $query_data = DatabaseLite_Class::wpmdia_get_posts_attachement_ids($custom_size, $custom_weight);
            }
            
            if(!empty($query_data)){
                $wp_query->query_vars['post__in'] = $query_data;
            }
        }
    }
    

 }
$global['filter_attachment_obj'] = new FilterCustomClass_Lite();
endif;