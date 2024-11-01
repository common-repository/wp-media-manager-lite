<?php defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
if ( ! class_exists( 'Core_Lite_Class' ) ) :
/**
 * Core Class 
*/
class Core_Lite_Class {
    public static $table  = WPMManagerLite_FolderLists;
    public static $table2  = WPMManagerLite_FolderFileRelationship;
     /**
     * Constructor
     */
    public function __construct() {
               //create folder by ajax call
              add_action('wp_ajax_wpmmedia_addfolder',array($this,'wpmmedia_create_folder'));
              add_action('wp_ajax_nopriv_wpmmedia_addfolder',array($this,'wpmmedia_create_folder'));
              //check sub folders of top folder by ajax call
              add_action('wp_ajax_wpmmedia_check_sub_folders',array($this,'get_check_sub_folders'));
              //delete folder by ajax call
              add_action('wp_ajax_wpmedia_delete_single_folders',array($this,'delete_single_folders'));
              add_action('wp_ajax_wpmedia_delete_folders',array($this,'fn_delete_all_folders'));
              add_action('wp_ajax_nopriv_wpmedia_delete_folders',array($this,'fn_delete_all_folders'));
              //add and enqueue script and css files
              add_filter( 'wp_prepare_attachment_for_js', array($this,'wpmmanager_filter_wp_prepare_attachment_for_js'), 10, 3 );
              add_action( 'admin_enqueue_scripts', array( $this, 'wpmmanager_aclass_to_ml_grid_elements')); 
              add_action('wp_enqueue_media', array($this, 'wpmmanager_media_page_script'));
              add_action('admin_head', array($this, 'wpmdia_admin_head'));
              /* Display Folder Lists on Right Section of media*/
               add_action('wp_ajax_wpmmedia_rightfolderlists',array($this,'fn_rightfolderlists'));
               add_action('wp_ajax_nopriv_wpmmedia_rightfolderlists',array($this,'fn_rightfolderlists'));
               
               /* Display Folder Lists on Top Section of media*/
               add_action('wp_ajax_get_parentfolderlists',array($this,'fn_parentfolderlists'));
               add_action('wp_ajax_nopriv_get_parentfolderlists',array($this,'fn_parentfolderlists'));

                /* Get Sub folders of main folder*/
               add_action('wp_ajax_wpmmedia_get_subfolders',array($this,'fn_get_subfolders'));
               add_action('wp_ajax_nopriv_wpmmedia_get_subfolders',array($this,'fn_get_subfolders'));
               
               /* On change fitler folder */
               add_action('wp_ajax_wpmmedia_alterfolders',array($this,'fn_alterfolders'));
               add_action('wp_ajax_nopriv_wpmmedia_alterfolders',array($this,'fn_alterfolders'));
               
               add_action('wp_ajax_wpmedia_dragsave_file',array($this,'wpmedia_dragsave_file'));
               add_action('wp_ajax_nopriv_wpmedia_dragsave_file',array($this,'wpmedia_dragsave_file'));
               
               add_action('admin_footer', array( $this, 'wpmediafn_html_footer' ));
               
               /* To add custom filter of folder dropdown lists on table view of media*/ 
               add_action('restrict_manage_posts', array( $this,'fn_media_add_folder_dropdown'),'top'); // top or bottom prority label
               add_action('pre_get_posts',array( $this,'fn_media_folder_filter'));
               add_action('pre_get_posts', array($this, 'fn_media_folder_filter_gridview'), 1,1000);
               
               add_action('admin_post_wpmediamanagerlite_save_settings',array($this,'fn_save_settings')); //receives the posted values from general settings
                
               add_action('wp_ajax_change_folder_arrangement',array($this,'change_folder_arrangement'));
               add_action('wp_ajax_nopriv_change_folder_arrangement',array($this,'change_folder_arrangement'));
               
               add_action('pre_get_posts', array($this, 'fn_attachment_mimetype_fillter'), 1,1000); // for attachment filter grid hook
               
     }

      /*
       * Pass the variable to the javascript file
      */
      public function wpmmanager_filter_wp_prepare_attachment_for_js( $response, $attachment, $meta ) {
          
          $response['mycustomclass'] = "wpmmanager-uidraggable";

          return $response;

      }

      /*
       * Admin Enqueue style and js
      */
       function wpmmanager_aclass_to_ml_grid_elements( $hook ){
          global $pagenow,$wpdb;
          global $current_user;

          if (!current_user_can('upload_files'))
            return;

         $plugin_pages = array( WPMManagerLite_TD );
         $this->wpmedia_manager_loadCSS();
         
         $wpmediamanager_settings = get_option('wpmediamanager_settings');
         $enable_wpmmanager = $wpmediamanager_settings['enable_wpmmanager'];
        
         if ($pagenow == 'upload.php') {
           $this->wpmedia_manager_loadScripts();
         }
         else{
             if ( isset( $_GET['page'] ) && in_array( $_GET['page'], $plugin_pages ) ) {
                wp_enqueue_style('wp-color-picker');
                wp_enqueue_script('wp-color-picker');
                wp_enqueue_style( 'wpmmanager-selectbox', WPMManagerLite_CSS_DIR . 'backend/jquery.selectbox.css', false, WPMManagerLite_VERSION );
                wp_enqueue_style('wpmmanager-fontawesome', WPMManagerLite_CSS_DIR . 'font-awesome/font-awesome.min.css', false, WPMManagerLite_VERSION);
                wp_enqueue_style('wpmmanager-icomoon', WPMManagerLite_CSS_DIR . 'icomoon/icomoon.css', false, WPMManagerLite_VERSION);
               
                $total = $wpdb->get_var("SELECT COUNT(posts.ID) as total FROM " . $wpdb->prefix . "posts as posts WHERE posts.post_type = 'attachment'");
                wp_enqueue_script('accordion');
                wp_enqueue_script(WPMManagerLite_Prefix.'-main-settings', WPMManagerLite_BACKEND_JS_DIR . 'backend.js',array('jquery','wp-color-picker','jquery-ui-accordion') ,false, WPMManagerLite_TD );
                $wpmmanager_ajax_nonce = wp_create_nonce('wpmmanager-admin-nonce');
                $user_role = $current_user->roles[0];
                wp_localize_script(WPMManagerLite_Prefix.'-main-settings', 'wp_mdiamanager_params', array(
                  'wpmdia_tot_attach_posts' => $total,
                  'user_role' => $user_role,
                  'ajaxurl'                   => admin_url('admin-ajax.php'), 
                  'wp_admin_nonce'            => $wpmmanager_ajax_nonce,
                ));
               wp_enqueue_script( WPMManagerLite_Prefix.'-bootstrap-scripts', WPMManagerLite_BACKEND_JS_DIR . 'jquery.selectbox.min.js',array('jquery') ,false, WPMManagerLite_TD );
               
             }
         }

          /*
          * Enqueue the javascript file that will extend the view
          */
          $currentScreen = get_current_screen();

          if( 'upload' === $currentScreen->id && $enable_wpmmanager == 1) :

              global $mode;

              if( 'grid' === $mode ) :
                  wp_enqueue_script('wplink');
                  wp_enqueue_style( 'editor-buttons' ); 

                  wp_enqueue_script( 'wpmmanager_dynamicmlibrary-grid-elements', WPMManagerLite_BACKEND_JS_DIR . 'admin-backend.js', array( 'jquery','jquery-ui-draggable' ) ); //Edit to match the file location
                  $wpmmanager_admin_ajax_object = $this->localize_data();
                  wp_localize_script( 'wpmmanager_dynamicmlibrary-grid-elements', 'wpmmanager_admin_ajax', $wpmmanager_admin_ajax_object ); //localization of php variable 
           
              endif;

          endif;
           

    } 


    public function wpmmanager_media_page_script(){
       global $pagenow , $current_screen;
        if (!current_user_can('upload_files'))
            return;
       $wpmediamanager_settings = get_option('wpmediamanager_settings');
       $enable_wpmmanager = (isset($wpmediamanager_settings['enable_wpmmanager']) && $wpmediamanager_settings['enable_wpmmanager'] == 1)?1:0;

     if($pagenow != "upload.php" && $enable_wpmmanager == 1) {
       $this->wpmedia_manager_loadCSS(); 
       $this->wpmedia_manager_loadScripts();
     }

    }

    public function wpmdia_admin_head() {
        if (!current_user_can('upload_files'))
            return;
        if (isset($_GET['s']) && $_GET['s'] != '') {
            echo '<style>.wpmedia-attachments-wrap{display:none !important;}</style>';
        }
    }
    

    public function wpmedia_manager_loadCSS()
    {
          //Load CSS Files //
          wp_enqueue_style('wpmmanager-fontawesome', WPMManagerLite_CSS_DIR . '/font-awesome/font-awesome.min.css', false, WPMManagerLite_VERSION);
          wp_enqueue_style('wpmmanager-icomoon', WPMManagerLite_CSS_DIR . '/icomoon/icomoon.css', false, WPMManagerLite_VERSION);
          wp_enqueue_style('wpmmanager-admin-style', WPMManagerLite_CSS_DIR . 'backend/admin-style.css', false, WPMManagerLite_VERSION);
          wp_enqueue_style('wpmmanager-jquery-ui', WPMManagerLite_CSS_DIR . 'backend/jquery-ui.css');
    }

    public function localize_data(){
       global $current_user,$pagenow,$post;
       $wpmediamanager_settings = get_option('wpmediamanager_settings');
       $enable_removeall = (isset($wpmediamanager_settings['enable_removeall']) && $wpmediamanager_settings['enable_removeall'] == 1)?'1':'0';
       $display_medianum = (isset($wpmediamanager_settings['display_medianum']) && $wpmediamanager_settings['display_medianum'] == 1)?'1':'0';
       $enable_wpmmanager = (isset($wpmediamanager_settings['enable_wpmmanager']) && $wpmediamanager_settings['enable_wpmmanager'] == 1)?'1':'0';
       $enable_customfilters = (isset($wpmediamanager_settings['enable_customfilters']) && $wpmediamanager_settings['enable_customfilters'] == 1)?'1':'0';
       
       //$user_roles = array_shift($current_user->roles);
        $current_user = wp_get_current_user();
        $user_roles = $current_user->roles[0];

       $wpmmanager_admin_ajax_nonce = wp_create_nonce('wpmmanager-ajax-nonce');
       $array_folders = array();
       $current_blog_id = get_current_blog_id();
      // $array_folderss = DatabaseLite_Class::aa(WPMManagerLite_FolderLists); // Load 'all folders' into a JavaScript variable that admin-backend.js has access to wp_localize_script
       
       $array_folders = DatabaseLite_Class::tbl_recur_array_builder_folder(WPMManagerLite_FolderLists); // Load 'all folders' into a JavaScript variable that admin-backend.js has access to wp_localize_script
       if (!empty($post) && !empty($post->post_type)) {
            $post_type = $post->post_type;
        } else {
            $post_type = '';
        }
    
       $folders_id = DatabaseLite_Class::get_folder_id(); // Load 'all folders' into a JavaScript variable that admin-backend.js has access to wp_localize_script
       $allmimetypes = get_post_mime_types();
      // WPMManagerLite_Libary::displayArr($allmimetypes);

       //custom size & weight 
       $custom_default_s_size   = json_decode(get_option('wpmediamanager_s_filtersize'));
       $custom_default_s_weight = json_decode(get_option('wpmedia_selected_wt_default'));

       $get_all_count = WPMManagerLite_Libary::get_all_count_type();
       // WPMManagerLite_Libary::displayArr($get_all_count);
       $get_filetype = $this->get_filetype();

       $wpmmanager_admin_ajax_object = array(
                  'enable_media'    => $enable_wpmmanager,
                  'enable_remove_all' =>$enable_removeall,
                  'ajax_url'        => admin_url('admin-ajax.php'), 
                  'ajax_nonce'      => $wpmmanager_admin_ajax_nonce,
                  'success_message' => __('Folder created Successfully!',WPMManagerLite_TD),
                  'error_message'   => __('Sorry, Folder couldnot be created. Try again later!',WPMManagerLite_TD),
                  'empty_message'   => __('Folder Name field is Empty. Please Fill Folder Name First.',WPMManagerLite_TD),
                  'msg_folder_delete' => __('Are you sure to want to delete this folder?', WPMManagerLite_TD),
                  'msg_folder_delete_all' => __('This folder contains other sub folders and files. Are you sure want to delete it ?', WPMManagerLite_TD),
                  'default_breadcrumb_txt' => __('You are here : ' , WPMManagerLite_TD),
                  'main_home_breadcrumb' => __('All Files' , WPMManagerLite_TD),
                  'allfolders'      =>  $array_folders,
                  'folders_id'      =>  $folders_id,
                  'allmimetypes'    => $allmimetypes,
                  'filtermimeText'  => __('All Media Items', WPMManagerLite_TD),
                  'uploadedToThisPost' => __('Media Uploaded to this', WPMManagerLite_TD),
                  'wpdia_unattached'   => __('Unattached', WPMManagerLite_TD),
                  'current_blog_id' => $current_blog_id,
                  'pagenow'         => $pagenow,
                  'current_post_type' => $post_type,
                  'userrole'         =>$user_roles,
                  'displaymedia_num' => $display_medianum,
                  'delete_message'  => __('Are you sure you want to delete this folder?', WPMManagerLite_TD),
                  'error_delete_msg'  => __('Sorry!! Folder couldnot be deleted. Try Again Later.', WPMManagerLite_TD),
                  'check_to_delete_all'  => __('This folder contain sub folders and files. Please delete its sub folders first or else activate "Remove all folders" at once options from general settings in order to delete all at once.', WPMManagerLite_TD),
                   'custom_selected_size'   =>  $custom_default_s_size,
                   'custom_selected_weight' =>  $custom_default_s_weight,
                   'size_default_lbl'   =>  __('Select Size', WPMManagerLite_TD),
                   'wt_default_lbl' => __('Select Weight', WPMManagerLite_TD),
                   'enable_customfilters' =>$enable_customfilters,
                   'media_filter_pdf' => __('All PDFs ('.$get_all_count['pdf_cnt'].')', WPMManagerLite_TD),
                   'media_filter_zip' => __('All ZIPS ('.$get_all_count['zip_cnt'].')', WPMManagerLite_TD),
                   'media_filter_documents' => __('Documents ('.$get_all_count['doc_cnt'].')', WPMManagerLite_TD),
                   'selected_filetype' => $get_filetype 
              );
         return  $wpmmanager_admin_ajax_object;
      }


          
      public function wpmedia_manager_loadScripts()
      {
        global $pagenow;
           // Load js files //
            $currentScreen = get_current_screen();
            $wpmediamanager_settings = get_option('wpmediamanager_settings');
            $enable_wpmmanager = (isset($wpmediamanager_settings['enable_wpmmanager']) && $wpmediamanager_settings['enable_wpmmanager'] == 1)?'1':'0';

             if($enable_wpmmanager == 1){
                 wp_enqueue_script('wplink');
                 wp_enqueue_style( 'editor-buttons' ); 
                if($pagenow == "upload.php"){
                      require_once ABSPATH . "wp-includes/class-wp-editor.php";
                      _WP_Editors::wp_link_dialog();
                }
              
                 wp_enqueue_script(WPMManagerLite_Prefix.'admin-scripts', WPMManagerLite_BACKEND_JS_DIR . 'admin-backend.js',array('jquery','plupload','jquery-ui-draggable', 'jquery-ui-droppable','jquery-ui-core','jquery-ui-sortable', 'media-editor', 'media-views') ,true, WPMManagerLite_VERSION );
              
                 $wpmmanager_admin_ajax_object = $this->localize_data();
                 wp_localize_script( WPMManagerLite_Prefix.'admin-scripts', 'wpmmanager_admin_ajax', $wpmmanager_admin_ajax_object ); //localization of php variable 
             }
               
       }

      /*
      *  Backend Media Enhancement
      */

      public function wpmmanager_admin_footer(){

        if (!function_exists("get_current_screen")) {      
              return;
         }else{
            $screen = get_current_screen();
            $base = $screen->base;
             if (isset($base)) {
                    if ($base == "theme-install" || !current_user_can("upload_files")) {
                  return;
                }
                require_once(WPMManagerLite_PATH . 'inc/backend/folder-management/media-folder-lists.php');
            }else{
               return;
            }
         }
      }

      public function FolderTreeStructure($list_results,$active_folder_id,$space_digit){     
          $wpmediamanager_settings = get_option('wpmediamanager_settings');
          $display_medianum = (isset($wpmediamanager_settings['display_medianum']) && $wpmediamanager_settings['display_medianum'] == 1)?'1':'0';
          $html = "<ul class='wpmediamanager-folders-wrap' id='first".$space_digit."_lists'>";
           
         if(!empty($list_results)):
                foreach ($list_results as $key => $item)
                {
                              $html .= "<li class='wpmmanager-media-fname wpmm-collapsed'  data-type='foldername' id='wpmedialists-".$item['id']."' data-parent_id='0'>";
                              if (is_array(@$item['child']))
                              {
                                // <i class='fa fa-caret-right' aria-hidden='true'></i>
                                 $html .= "<div class='wpmedia-icon-open wpmedia-fchild' data-id='".$item['id']."' data-order='".$item['folder_order']."' data-parent-id='0' data-slug='".esc_attr($item['slug'])."'><i class='fa fa-caret-right' aria-hidden='true'></i></div>";
                                 $data_child="1";
                              }else{
                                 $html .= "<div class='wpmedia-icon-open' data-id='".$item['id']."' data-order='".$item['folder_order']."' data-parent-id='0' data-slug='".esc_attr($item['slug'])."' style='opacity:0;visibility:hidden;'><i class='fa fa-caret-right' aria-hidden='true'></i></div>";  
                                  $data_child="0";
                              }
                              
                              $html .= $space_digit."<i class='fa fa-folder'></i><a href='javascript:void(0);' class='wpmedia-folder-title wpmedia-manager-files' data-child=".$data_child." data-id=".$item['id']."
                                    data-type='foldername' data-slug=".esc_attr($item["slug"]).">";
                              $count = DatabaseLite_Class::get_all_count_lists($item['id']);
                              
                              $html .= ucwords(esc_attr($item["name"]));
                              if($display_medianum == 1){
                               $html .= "<span class='wpmmedia-totalfiles-cnt allorganized-files'>".$count."</span>"; 
                              }
                              $html .=   "</a>";
                              $html .="</li>";
                     
                }
            endif;

           $html .= "</ul>";
        return $html;

      }

      
      /*
      * Display the html string for the right folder lists structure.
      */
      public static function FolderTreeStructure2($active_folder_id)
      {
        //$list_results = $database_class->get_all_folders_lists('',self::$table);
        $list_results = DatabaseLite_Class::tbl_recur_array_builder(WPMManagerLite_FolderLists,$active_folder_id); 

        $html = '';
        $html .= "<ul>";
       
        if(!empty($list_results)):
       
        foreach ($list_results as $key => $value) {  
      
        $html .="<li class='wpmmanager-media-fname' id='wpmedialists-".$value['id']."'><a href='javascript:void(0);' 
        class='wpmedia-manager-files' data-id=".$value['id']."
        data-type='foldername' data-slug=".esc_attr($value["slug"]).">".ucwords(esc_attr($value["name"]))."</a>";
     
        if(!empty($value['child'])){
          
           $html .= "<ul>";
           
           foreach ($value['child'] as $c_value) {
             
             $html .="<li class='wpmmanager-media-fname' id='wpmedialists-".$c_value['id']."'>-- <a href='javascript:void(0);' 
             class='wpmedia-manager-files wpmedia-manager-child-files' data-id=".$c_value['id']."
            data-type='foldername' data-slug=".esc_attr($c_value["slug"]).">".ucwords(esc_attr($c_value["name"]))."</a></li>";
 
           }
          
           $html .= "</ul>";
        }

        $html .="</li>";
        
        }
        endif;

        $html .= "</ul>";
        
        return $html;
      }
    
      /*
      * Display the dropdown field with Folder Lists
      */
       public static function toSelect($arr,$depth= 0) {  
          $html = '';
          if(!empty($arr)):
          foreach ( $arr as $key => $value ) {
              if($value['folder_order'] > 0){
                 $depth = $value['folder_order'];
              }

              $html.= '<option value="' . $value['id'] . '">';
              $html.= str_repeat('--', $depth);
              $html.= ucwords($value['name']) . '</option>' . PHP_EOL;

               if(!empty($value['child'])){
                  $html.= Core_Lite_Class::toSelect($value['child'], $depth + 1);
              }
          }
           endif;
          return $html;
      }

       /*
       * Create Folder Data
       */
      public function wpmmedia_create_folder(){
         $data  = array();
         if ( !empty( $_POST ) && wp_verify_nonce( $_POST['wp_nonce'], 'wpmmanager-ajax-nonce' ) ) {
           $database_class = new DatabaseLite_Class();
           $foldername = sanitize_text_field($_POST['foldername']);
           $active_files = sanitize_text_field($_POST['activefiles']);
           $parent_folder_id = sanitize_text_field($_POST['parent_folder_id']);
           $countlength = sanitize_text_field($_POST['countlength']);
           
           $folder_action = sanitize_text_field($_POST['folder_action']);
           $folderid = sanitize_text_field($_POST['folderid']);
           
          if($foldername != ''){
              // Case 1: Add folders
               if($folder_action == "add_folder"){
                  $lastid =  $database_class->create_folder_data($foldername,$parent_folder_id, $countlength,$active_files,WPMManagerLite_FolderLists);        
                  if( $lastid != '' && intval($lastid)){
                  $result = $database_class->get_all_folders_lists($lastid,WPMManagerLite_FolderLists);
                  $count = $database_class->get_all_count_lists($lastid);
                   if( !empty( $result ) ){
                      $data = array(
                        'message' => "success",
                        'result' => $result,
                         'count' => $count
                        );
                    }else{
                     $data = array(
                        'message' => "error",
                        'result' => '',
                         'count' => '0'
                        );
                    }
                }
                  
               }else{
                     // Case 2: Edit folders
                  $results =  $database_class->edit_folder_data($foldername,$folderid,WPMManagerLite_FolderLists);
                  if($results){
                       $resultss = $database_class->get_all_folders_lists($folderid,WPMManagerLite_FolderLists);
                       $data = array(
                        'message' => "success",
                        'result' => $resultss
                        );
                  }else{
                       $data = array(
                        'message' => "error",
                        'result' => ''
                        );
                  }
                 
               }
                
               
            } 

          echo json_encode($data);
         }
         
         die();
      }
      
      /*
       * Move and Save Media ID to Folders
       */
      public function wpmedia_dragsave_file(){
          global $wpdb;
          $database_class = new DatabaseLite_Class();
           if ( !empty( $_POST ) && wp_verify_nonce( $_POST['wp_nonce'], 'wpmmanager-ajax-nonce' ) ) {
                $collectids = explode(',', sanitize_text_field($_POST['collectids']));
                $folderid = sanitize_text_field($_POST['folderid']);
                if(isset($collectids)){
                    foreach ($collectids as $id){
                        $arr = $database_class->get_common_media_folders($id,WPMManagerLite_FolderFileRelationship);
                        //delete preivious data of specific media id
                        $delte_arr = $database_class->del_foldermedia_link($arr,$folderid,WPMManagerLite_FolderFileRelationship);
                        if($folderid != 0){
                          $check = $database_class->insert_fmrelationship_data($folderid , $id ,WPMManagerLite_FolderFileRelationship );    
                        }else{
                          $check = $database_class->delete_sp_media_files($folderid );    
                        }
                    }
                    
                    if($check){
                        $return = 1;
                    }else{
                         $return = 0;
                    }
                }
              wp_send_json($return);
           }
                  
      }

        /*
       * Check if sub folders exists
       */
      public function get_check_sub_folders(){
          $database_class = new DatabaseLite_Class(); 
          if ( !empty( $_POST ) && wp_verify_nonce( $_POST['wp_nonce'], 'wpmmanager-ajax-nonce' ) ) {
            $folderid = sanitize_text_field($_POST['folder_id']);
            $childs = $database_class->get_all_sub_folder_lists( $folderid );
              if(!empty($childs)){
                         $childs_data = array(
                            'status' => '1',
                            'folderchilds' => $childs
                            );
                    }else{
                        $childs_data = array(
                            'status' => '0',
                            'folderchilds' => '0'
                            );

                 }
           echo json_encode($childs_data);
           die();
          }
        
      }
      
      /*
      * Delete Single Folder and its Files First
      */
      public function delete_single_folders(){
        $database_class = new DatabaseLite_Class(); 
          if ( !empty( $_POST ) && wp_verify_nonce( $_POST['wp_nonce'], 'wpmmanager-ajax-nonce' ) ) {
            $folderid = sanitize_text_field($_POST['folder_id']);
             $check_delete = $database_class->delete_single_folder( $folderid );
              if($check_delete){
                    $data = array(
                            'check_deleted_status' => '1',
                    );
                    }else{
                     $data = array(
                            'check_deleted_status' => '0',
                    );

                 }
         }
          echo json_encode($data);
          die();
      }

       /*
       * Delete Folder Details
       */
      public function fn_delete_all_folders(){
       $database_class = new DatabaseLite_Class(); 
        if ( !empty( $_POST ) && wp_verify_nonce( $_POST['wp_nonce'], 'wpmmanager-ajax-nonce' ) ) {
            $folderid = sanitize_text_field($_POST['folder_id']);
            $table_name  = WPMManagerLite_FolderLists;

            $check_delete = $database_class->delete_single_folder( $folderid );
            $childs = $database_class->recursive_delete($table_name ,$folderid , true );
            
            $data = array(
                'status' => '1',
                'folderchilds' => $childs
            );
        }
         echo json_encode($data);
         die();

     }
      
      
      /*
       * Move Folders Inside Another Folders
       */
      public function change_folder_arrangement(){
          if ( !empty( $_POST ) && wp_verify_nonce( $_POST['wp_nonce'], 'wpmmanager-ajax-nonce' ) ) {
            $draggableFolderParentID = absint(sanitize_text_field($_POST['draggableFolderParentID']));
            $draggableFolderID = absint(sanitize_text_field($_POST['draggableFolderID']));// parent id of which is draggable
            $droppablefolderID = absint(sanitize_text_field($_POST['droppablefolderID']));// id where need to be droppable
            $droppableParentID = absint(sanitize_text_field($_POST['droppableParentID']));// parent id where need to be droppable
            $selectedParentID = absint(sanitize_text_field($_POST['selectedParentID']));// selected dropdown folder id
             if($droppablefolderID == 0){
                   $order = 0;
             }else{
                   $order = DatabaseLite_Class::get_all_parent_cnt($droppablefolderID); 
             }
            $update_folders = DatabaseLite_Class::update_folders($draggableFolderID,$droppablefolderID);
            if($update_folders){
                 $data = array('status' => true, 'count_num' => $order);
            }else{
                $data = array('status' => false, 'count_num' => '');    
            }
            echo json_encode($data); 
          }
          wp_die();
      }

      /*
      * Get all sub folders of main folder from its id
      */
      public function fn_get_subfolders(){
        if ( !empty( $_POST ) && wp_verify_nonce( $_POST['wp_nonce'], 'wpmmanager-ajax-nonce' ) ) {
                $folder_id = sanitize_text_field($_POST['folder_id']);    
               if(intval($folder_id)){
                 $list_results = DatabaseLite_Class::tbl_recur_array_builder(WPMManagerLite_FolderLists ,'', $folder_id , $parent_child=true); 
                 if (!empty($list_results)) {
                       wp_send_json($list_results);
                  } else {
                      wp_send_json('empty');
                  }
              }
            }
           wp_die();
          
      }
      
      /*
       * Display Sub folders on change folders filter options
       */
      public function fn_alterfolders(){
          if ( !empty( $_POST ) && wp_verify_nonce( $_POST['wp_nonce'], 'wpmmanager-ajax-nonce' ) ) {
               if(isset($_POST['parentid']) && $_POST['parentid'] != ''){
                  // $pfolderid = intval($_POST['parentid']);    
                   $pfolderid = (int) $_POST['parentid'] | 0;
                  // $_SESSION['wpmedia_manager_current_folder'] = $pfolderid;

                  $list_results = DatabaseLite_Class::get_all_sub_folder_lists($pfolderid);   

                 if (!empty($list_results)) {
                     $data = array(
                        'message' => "success",
                         'id'     =>  $pfolderid,
                        'result'  => $list_results
                        );
                  } else {
                      $data = array(
                        'message' => "empty",
                           'id'   =>$pfolderid,
                        'result'  => array()
                        ); 
                  }
              }else{
                  $data = array(
                        'message'  => "error",
                       'id'        =>$pfolderid,
                        'result'   => array()
                        );
                     
                  }
                     wp_send_json($data);
            }
           wp_die();
      }

      public function fn_rightfolderlists(){
         check_ajax_referer( 'wpmmanager-ajax-nonce', 'wp_nonce' );
         if(isset($_POST) && $_POST['current_blog_id'] != ''){
          $current_blog_id = sanitize_text_field($_POST['current_blog_id']);
          include(WPMManagerLite_PATH.'/inc/backend/media-enhancement/media-folder-lists.php');  
         }
        wp_die();
      }
     
     public function fn_parentfolderlists(){
          check_ajax_referer( 'wpmmanager-ajax-nonce', 'wp_nonce' );
         if(isset($_POST) && $_POST['current_blog_id'] != ''){
//          $current_blog_id = $_POST['current_blog_id'];
          $list_results = DatabaseLite_Class::get_all_parent_folders(WPMManagerLite_FolderLists); 
          $getfolderHTML = $this->get_parentfolderlists($list_results);
          echo $getfolderHTML;
         }
        wp_die();
     }
     
      public static function get_parentfolderlists($list_results){     
         check_ajax_referer( 'wpmmanager-ajax-nonce', 'wp_nonce' );
         if(isset($_POST) && $_POST['current_blog_id'] != ''){
          $current_blog_id = sanitize_text_field($_POST['current_blog_id']);
          $html = "";          
           require_once ABSPATH . "wp-includes/class-wp-editor.php";
          _WP_Editors::wp_link_dialog();
         if(!empty($list_results)):
                foreach ($list_results as $item)
                {
                $html .= "<li class='wpmmanager-attachmedia' data-id='".$item->folder_id."' data-parentid='0'>";
                $html .= "<div class='wpmega-fonticons'>";
                
                $html .= "<i class='wpmega-fedit-icons fa fa-pencil' title='"._e('Edit Folder','wp-media-manager-lite')."'></i>";
                $html .= "<i class='wpmega-fdelete-icons fa fa-trash' title='"._e('Delete Folder','wp-media-manager-lite')."'></i></div>";
                $html .= "<div class='wpmmanager-previewattach'>";
                $html .= "<i class='wpmega-ficons fa fa-folder'></i>";
                $html .= "<div class='wpmainfilename'>";
                $html .= "<a href='javascript:void(0);' data-id=".$item->folder_id." data-slug=".esc_attr($item->folder_slug).">".ucwords(esc_attr($item->folder_name))."</a>";
                $html .= "</div></div></li>";  
                }    
            endif;
         }
       return $html;

      }
      
    public function wpmediafn_html_footer(){
     echo '<input type="hidden" class="wpmediam_select_folder_id" value="0">';
     echo '<div class="wpmmanager_overlay_wrap"><div id="wpmmanager-dialog-form" title="'.__('Create New Folder','wp-media-manager-lite').'">
                <div class="wpmmanager_close"><i class="fa fa-close"></i></div>
                <label for="wpm_manager_folder_name">'.__('Folder Name','wp-media-manager-lite').'</label>
               <hr/><input type="hidden" name="wpmm_action" class="wpmm_action_settings" value="add_folder"/>
                <input type="hidden" name="wpmm_folderid" class="wpm_folderDetailsID" value=""/>
                <input type="text" name="wpm_manager_fname" id="wpm_manager_folder_name" class="wpmmanagername" value="" />
                <span class="wpmedia-manager-fname wpmedia-manager-hide-content"></span>
                <input type="button" name="wpmmanager_submitbtn" class="button button-primary button-small wpmmanagersubmitbtn" value="'.__('ADD' , 'wp-media-manager-lite').'">
               </div></div><div class="wpmedia-manager-overlay"><img src="'.WPMManagerLite_IMG_DIR.'/loader.gif"/></div>';
    }
    
              
       /* 
       * For Table View : Folders Lists To add or display custom dropdown filter on table view of media
       */
        public function fn_media_add_folder_dropdown(){
           $scr = get_current_screen();
           if ( $scr->base !== 'upload' ) return;
        
           $array_folders = DatabaseLite_Class::tbl_recur_array_builder_folder(WPMManagerLite_FolderLists); // Load 'all folders' into a JavaScript variable that admin-backend.js has access to wp_localize_script
           $folders_id = DatabaseLite_Class::get_folder_id(); // Load 'all folders' into a JavaScript variable that admin-backend.js has access to wp_localize_script
           if (isset($_GET['wpm-selected-folder']) && intval($_GET['wpm-selected-folder'])){
                  $selectedid = sanitize_text_field($_GET['wpm-selected-folder']);
            }else{
               $selectedid = 0;
            }

           $output = "<select name='wpm-selected-folder' id='wpmedia-select-folder' class='wpmedia-all-folders'>";
            foreach($folders_id as $value){   
                 $folder_val =  $array_folders[$value];
               
                 if ($folder_val['id'] == '0' ) {
                    $order = 0;
                 }else{
                    $order = $folder_val['folder_order'];
                 }
                  if($selectedid == $folder_val['id']){
                    $val = 'selected="selected"';
                  }else{
                   $val = '';   
                 }
                
                 $output .= '<option '.$val.' value="'.$folder_val['id'].'" data-id="'.$folder_val['id'].'" data-parentid="'.$folder_val['parent_id'].'">'.str_repeat("&nbsp;&nbsp;",$order).ucwords(esc_attr($folder_val['name'])).'</option>'; 
            }
           $output .= '</select>';

       $wpmediamanager_settings = get_option('wpmediamanager_settings');
       $enable_customfilters = (isset($wpmediamanager_settings['enable_customfilters']) && $wpmediamanager_settings['enable_customfilters'] == 1)?'1':'0';
        
        if($enable_customfilters == 1){
            $custom_default_s_size   = json_decode(get_option('wpmediamanager_s_filtersize'));
            $label = __('All Custom Size',WPMManagerLite_TD);
          
            if (isset($_GET['wpmdia_custom_size']) && intval($_GET['wpmdia_custom_size'])){
                  $s_size = sanitize_text_field($_GET['wpmdia_custom_size']);
            }else{
                 $s_size = '';
            }

         //custom media size for table view
           $output .= "<select name='wpmdia_custom_size' id='wpmedia-folder-size' class='wpmedia-all-folders_csize'>";
           $output .= "<option value='all' selected>".$label."</option>";
           if(!empty($custom_default_s_size)){
            foreach($custom_default_s_size as $value){   
               if( $s_size == $value ){
                    $selected_size = 'selected="selected"';
                  }else{
                   $selected_size = '';   
                 }
                 $output .= '<option '.$selected_size.' value="'.$value.'">'.$value.'</option>'; 
              }
            }
           $output .= '</select>';

          //custom weight for table view
            $label_wt = __('All Custom Weight',WPMManagerLite_TD);
            $custom_default_s_weight = json_decode(get_option('wpmedia_selected_wt_default'));
            if (isset($_GET['wpmdia_custom_weight']) && intval($_GET['wpmdia_custom_weight'])){
                  $s_weight = sanitize_text_field($_GET['wpmdia_custom_weight']);
            }else{
                 $s_weight = '';
            }

            $output .= "<select name='wpmdia_custom_weight' id='wpmedia-folder-weight' class='wpmedia-all-folders_cweight'>";
            $output .= "<option value='all' selected>".$label_wt."</option>";
            if(!empty($custom_default_s_weight)){
              foreach($custom_default_s_weight as $value){ 
              $str_explode = explode('-',$value[0]);  
                 if ($value[1] == 'kB') {
                      $wt_lbl1 = ($str_explode[0] / 1024);
                      $wt_lbl2 = ($str_explode[1] / 1024);
                      $wt_unit = "kB";
                  } else {
                      $wt_lbl1 = ($str_explode[0] / (1024 * 1024));
                      $wt_lbl2 =($str_explode[1] / (1024 * 1024));
                      $wt_unit = "MB";
                  }
                  if( $s_weight == $value[0] ){
                    $selected_wt = 'selected="selected"';
                  }else{
                   $selected_wt = '';   
                 }
                 $output .= '<option '.$selected_wt.' value="'.$value[0].'">'. $wt_lbl1.'-'.$wt_lbl2.' '.$wt_unit.'</option>'; 
              }
            }
           $output .= '</select>';
         }

        echo $output;
       }
       
       /*
        * Query After Get Post url data for Table View
        */
       public function fn_media_folder_filter($wp_query){
           global $pagenow;
           $mediaIdsArr = array();
            if (!isset($wp_query->query_vars['post_type']) || $wp_query->query_vars['post_type'] != 'attachment')
            return;
             if ( is_admin() || $wp_query->is_main_query() ) {
                if (isset($_GET['wpm-selected-folder']) && intval($_GET['wpm-selected-folder'])){
                 $selectedfolder  = sanitize_text_field($_GET['wpm-selected-folder']);
                 $mediaIdsArr =  DatabaseLite_Class::media_filter_ids( $selectedfolder );
                    if(!empty($mediaIdsArr)){
                       $wp_query->query_vars['post__in'] = $mediaIdsArr;
                    }else{
                         $wp_query->query_vars['post__in'] = array(0);                    
                    }
                }else{
                       $mediaArr =  DatabaseLite_Class::get_media_filter_notids();
                       $wp_query->query_vars['post__not_in'] =  $mediaArr;
                }
                
            }     
       }
       
       /*
        * Query After Get Post url data for Grid View
        */
       public function fn_media_folder_filter_gridview($wp_query){
            $mediaIdsArr = array();
            
            if (!isset($wp_query->query_vars['post_type']) || $wp_query->query_vars['post_type'] != 'attachment')
            return;
            
            $mediaArr = array();
             if ( is_admin() || $wp_query->is_main_query() ) {
                 if (isset($_REQUEST['query']['folder_id']) && isset($_REQUEST['query']['folder_slug']) && $_REQUEST['query']['folder_slug'])  {
                       // get the ids of media having retrieved folders as parent
                     $query_fid = sanitize_text_field($_REQUEST['query']['folder_id']);
                     $mediaIdsArr =  DatabaseLite_Class::media_filter_ids($query_fid);
                    
                     if ( ! empty($mediaIdsArr) ) {
                        // force media query to retrieve only media having retrieved posts as parent
                        $wp_query->set( 'post__in', $mediaIdsArr );
                      } else {
                         
                        // force media query to return no posts
                          $wp_query->set( 'post__in', array(0)); 
                      }
                      
                 }else if (isset($_REQUEST['query']['folder_slug']) && $_REQUEST['query']['folder_slug'] == '') {
                     $mediaArr =  DatabaseLite_Class::get_media_filter_notids();
                     $wp_query->set( 'post__not_in', $mediaArr);
                 }
            }   

          
             return $wp_query;
         }
          
        /*
        * Query Posts for Filter Attachment mimetype for Grid View
        */
         public function fn_attachment_mimetype_fillter($wp_query){
             global $pagenow, $wpdb;
            if (!isset($wp_query->query_vars['post_type']) || $wp_query->query_vars['post_type'] != 'attachment')
             return;
            
             if (isset($_GET['attachment-filter'])) {
                $filetype_mime = sanitize_text_field($_GET['attachment-filter']);
            }
            if (isset($_REQUEST['query']['post_mime_type'])) {
                $filetype_mime = sanitize_text_field($_REQUEST['query']['post_mime_type']);
            }

           if (isset($filetype_mime)) {
            if ($filetype_mime == 'wpmdiamanager_pdf_type' || $filetype_mime == 'wpmdiamanager_openoffice_type' || $filetype_mime == 'wpmdiamanager_excel_type' || $filetype_mime == 'wpmdiamanager_docs_type' ||  $filetype_mime == 'wpmdiamanager_ico_type' || $filetype_mime == 'wpmdiamanager_zip_type' || $filetype_mime == 'wpmdiamanager_other') {
                $filetypes = explode('_', $filetype_mime);
                $filetype = $filetypes[1];
                if ($filetype == 'zip' || $filetype == 'pdf' || $filetype == 'docs' || $filetype == 'ico' || $filetype == 'text' || $filetype == 'openoffice' || $filetype == 'other') {
                    $wp_query->query_vars['post__not_in'] =   array();
                    switch ($filetype) {
                        case 'pdf':
                            $wp_query->query_vars['post_mime_type'] = array('application/pdf');
                            break;
                        case 'zip':
                            $wp_query->query_vars['post_mime_type'] = array('application/zip', 'application/rar', 'application/x-gzip', 
                              'application/x-7z-compressed', 'application/x-tar');
                            break;
                         case 'docs':
                            $wp_query->query_vars['post_mime_type'] = array('application/msword','application/vnd.ms-powerpoint','application/vnd.ms-write','application/vnd.ms-excel','application/vnd.ms-access','application/vnd.ms-project','application/vnd.openxmlformats-officedocument.wordprocessingml.document','application/vnd.ms-word.document.macroEnabled.12','application/vnd.openxmlformats-officedocument.wordprocessingml.template',
                              'application/vnd.ms-word.template.macroEnabled.12','application/vnd.openxmlformats-officedocument.spreadsheetml.sheet','application/vnd.ms-excel.sheet.macroEnabled.12','application/vnd.ms-excel.sheet.binary.macroEnabled.12','application/vnd.openxmlformats-officedocument.spreadsheetml.template','application/vnd.ms-excel.template.macroEnabled.12',
                              'application/vnd.ms-excel.addin.macroEnabled.12','application/vnd.openxmlformats-officedocument.presentationml.presentation','application/vnd.ms-powerpoint.presentation.macroEnabled.12','application/vnd.openxmlformats-officedocument.presentationml.slideshow','application/vnd.ms-powerpoint.slideshow.macroEnabled.12','application/vnd.openxmlformats-officedocument.presentationml.template','application/vnd.ms-powerpoint.template.macroEnabled.12','application/vnd.ms-powerpoint.addin.macroEnabled.12','application/vnd.openxmlformats-officedocument.presentationml.slide',
                              'application/vnd.ms-powerpoint.slide.macroEnabled.12','application/onenote');
                            break;
                        case 'ico':
                            $wp_query->query_vars['post_mime_type'  ] = array('image/x-icon','image/tiff','image/gif');
                            break;
                        case 'text':
                            $wp_query->query_vars['post_mime_type'] = array('text/html','text/css','text/richtext','text/calendar','text/tab-separated-values','text/csv','text/plain');
                            break;
                        case 'openoffice':
                            $wp_query->query_vars['post_mime_type'] = array('application/vnd.oasis.opendocument.text','application/vnd.oasis.opendocument.presentation','application/vnd.oasis.opendocument.spreadsheet','application/vnd.oasis.opendocument.graphics','application/vnd.oasis.opendocument.chart','application/vnd.oasis.opendocument.database','application/vnd.oasis.opendocument.formula');
                            break;
                        case 'other':
                           $other_files = array('image/jpeg','image/gif','image/png','image/bmp','image/tiff','image/x-icon','video/x-ms-asf','video/x-ms-wmv','video/x-ms-wmx','video/x-ms-wm','video/avi','video/divx','video/x-flv','video/quicktime','video/mpeg','video/mp4','video/ogg','video/webm','video/x-matroska','audio/mpeg','audio/x-realaudio','audio/wav','audio/ogg','audio/midi','audio/x-ms-wma','audio/x-ms-wax','audio/x-matroska','application/rtf','application/javascript',
                            'application/x-shockwave-flash','application/java','application/iso', 'application/wordperfect', 'application/java','application/x-msdownload','application/vnd.apple.keynote','application/vnd.apple.numbers','application/vnd.apple.pages');
                            $wp_query->query_vars['post_mime_type'] = $other_files;
                            break;
                    }
                    
                     return $wp_query;
                }
            }
        }
       }

    /* Get Mimetype of url in Table View*/
      public function get_filetype(){
        if (isset($_GET['attachment-filter'])) {
            if ($_GET['attachment-filter'] == 'wpmdiamanager_pdf_type' || $_GET['attachment-filter'] == 'wpmdiamanager_zip_type' || $_GET['attachment-filter'] == 'wpmdiamanager_docs_type'  || $_GET['attachment-filter'] == 'wpmdiamanager_other') {
                $filetype = sanitize_text_field($_GET['attachment-filter']);
            } else {
                $filetype = '';
            }
        } else {
            $filetype = '';
        }

        return $filetype;
      }

       
        /*
        *  Saves General Settings to database
        */
        public function fn_save_settings(){
            if(!empty($_POST) && wp_verify_nonce($_POST['wpmediamanager-nonce-setup'],'wpmediamanager-nonce')){
                if(isset($_POST['wpmedia_settings_submit_btn'])){
                  include_once(WPMManagerLite_PATH.'/inc/backend/save_settings.php');
                }else if(isset($_POST['wpmedialite_restore_old_settings'])){
                   $default_settings = WPMManager_MainClass::wpmediamanager_default_settings();
                   update_option('wpmediamanager_settings', $default_settings);
                   wp_redirect( admin_url() . 'admin.php?page=wp-media-manager-lite&restore_message=1');
                }
                
            }
            else{
                die('No script kiddies please!');
            }
         }
    }
$global['core_class'] = new Core_Lite_Class();
endif;