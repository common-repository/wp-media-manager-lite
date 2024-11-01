<?php defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
if ( ! class_exists( 'DatabaseLite_Class' ) ) :

/**
 * Database Based Class
 */
class DatabaseLite_Class {
        
       /**
       * Get All Folder Lists from table. 
       * */
      public static function get_all_folders_lists($folder_id,$table_name){
       global $wpdb;
        if(intval($folder_id) && $folder_id != ''){
            $wpmmanager_folders_lists = $wpdb->get_row("SELECT * FROM $table_name where folder_id = $folder_id");
        }else{
            $wpmmanager_folders_lists = $wpdb->get_results("SELECT * FROM $table_name ORDER BY folder_id ASC");
        }
        return $wpmmanager_folders_lists;
       }

      /**
       * Get Sub folders From parent Folder id
       * */
      public static function get_mf_sub_folder_lists($parent_folder_id){
       global $wpdb;
        $table_name = WPMManagerLite_FolderLists;
        $wpmmanager_media = $wpdb->get_results("SELECT * FROM $table_name where folder_parent = $parent_folder_id || folder_id = $parent_folder_id");   
        return $wpmmanager_media;
       }

       
       /**
       * Get All Folder Relationship Media Lists from table. 
       * */
      public static function get_common_media_folders($media_id,$table_name){
       global $wpdb;
       $idarr = array();
        if(intval($media_id) && $media_id != ''){
           $media_folders_lists = $wpdb->get_results("SELECT ff_relationship_id FROM $table_name where media_id = $media_id");
        }
        if(!empty($media_folders_lists)){
         for($i=0;$i<count($media_folders_lists);$i++){
                $idarr[] = $media_folders_lists[$i]->ff_relationship_id;
         }
        }
        
        return $idarr;
       }
       
       /*
        * Delete Row By ID
        */
       public static function del_foldermedia_link($ffrtp_id,$folderid,$table_name){
          global $wpdb;
         
          if(!empty($ffrtp_id)){
              foreach($ffrtp_id as $fid){
                    $wpdb->delete(
                          $table_name,
                          array('ff_relationship_id' => $fid),
                          array('%d')
                      );      
              }
              
               return true;
          }else{
                    return false;
          }
       }
       
       /**
       * Get All Parent folder 
       * */
       public static function get_all_parent_folders($table_name){
        global $wpdb;
        $wpmmanager_pfolders = $wpdb->get_results("SELECT * FROM $table_name where folder_parent = 0");
        return $wpmmanager_pfolders;
       }
       
       /**
       * Get All Media Lists from table. 
       * */
      public static function get_all_media_rel_folder_lists($folder_id){
       global $wpdb;
       $table_name = WPMManagerLite_FolderFileRelationship;
        if(intval($folder_id) && $folder_id != ''){
            $wpmmanager_media_lists = $wpdb->get_results("SELECT media_id FROM $table_name where folderid = $folder_id");
        }else{
            $wpmmanager_media_lists = $wpdb->get_results("SELECT media_id FROM $table_name ORDER BY folderid ASC");
        }
        return $wpmmanager_media_lists;
       }
       
        /**
       * Get All Media Lists from table. 
       * */
      public static function get_all_count_lists($folder_id){
       global $wpdb;
       $table_name = WPMManagerLite_FolderFileRelationship;
        if(intval($folder_id) && $folder_id != ''){
            $count_query = $wpdb->get_results("SELECT COUNT(*) as totalnum FROM $table_name WHERE folderid = $folder_id ");
        }else{
            $count_query = $wpdb->get_results("SELECT COUNT(*) as totalnum FROM $table_name ORDER BY folderid ASC");
        }
        $num = $count_query[0]->totalnum;

        return $num;
       }
       
        /**
       * Get Sub folders From parent Folder id
       * */
      public static function get_all_sub_folder_lists($parent_folder_id){
       global $wpdb;
        $table_name = WPMManagerLite_FolderLists;
        $wpmmanager_media = $wpdb->get_results("SELECT * FROM $table_name where folder_parent = $parent_folder_id");   
        return $wpmmanager_media;
       }
       
       /*
        * Insert Media ID To Folder Relationship
        */
       public static function insert_fmrelationship_data($folderid , $id ,$table_name){
          global $wpdb;
          $created_date = date( 'Y-m-d H:i:s:u' );
          $fm_settings = 
                    array(
                      'media_id'   => $id,
                      'folderid' => $folderid,
                      'addeddate'    => $created_date,
           );

          $wpdb->insert(
                  $table_name , $fm_settings , array(
                      '%d', '%d', '%s' )
                  );
          $lastid = $wpdb->insert_id;
          return $lastid;
       }

       /*
        * Create Folder And Save to Db
        */
       public static function create_folder_data($title,$parent_folder_id, $countlength,$active,$table_name){
          global $wpdb;
          $slug      = WPMManagerLite_Libary::create_unique_slug($title,$table_name);
          
          $created_date = date( 'Y-m-d H:i:s:u' );
          if( $active == "allfiles" ){
               $order = '0';
          }else{
              $countlength = $countlength + 1;
              $order = $countlength;
          }
          $folder_settings = 
                    array(
                      'folder_name'   => $title,
                      'folder_slug'   => $slug,
                      'folder_parent' => $parent_folder_id,
                      'type'          => 'folder',
                      'folder_order'  => $order,
                      'added_date'    => $created_date,
                      'modified_date' => $created_date
                          );
    
          $wpdb->insert(
                  $table_name , $folder_settings , array(
                      '%s', '%s', '%d', '%s', '%d', '%s', '%s'
                          )
                  );
          //$lastid2 = $wpdb->insert_id;
          return $wpdb->insert_id;
       }
         /*
          * Edit Folder
          */
          public static function edit_folder_data($title,$edit_folder_id,$table_name){
          global $wpdb;
          $slug      = WPMManagerLite_Libary::create_unique_slug($title,$table_name);
          $modified_date = date( 'Y-m-d H:i:s:u' );
          $folder_settings = 
                    array(
                      'folder_name'   => $title,
                      'folder_slug'   => $slug,
                      'modified_date' => $modified_date );

         $wpdb->update( 
                 $table_name, $folder_settings, 
                 array( 'folder_id' => $edit_folder_id), 
                array( 
                        '%s',
                        '%s',	
                        '%s'	
                ), 
                array( '%d' ) 
        );
          return true;
       }

       /**
       * Delete Single Folder and its media files id
       * */
      public static function delete_single_folder($folder_id){
       global $wpdb;
        $table_name = WPMManagerLite_FolderLists;
        $check_delete = $wpdb->query("DELETE FROM $table_name where folder_id = $folder_id");   
        if( $check_delete ){
             DatabaseLite_Class::delete_sp_media_files($folder_id);
              return true;
            }else{
             return false;
            }
       }
      
       /**
       * Delete Single Folder Media files 
       * */
       public static function delete_sp_media_files($folder_id){
        global $wpdb;
        $table_name = WPMManagerLite_FolderFileRelationship;
        $wpdb->query("DELETE FROM $table_name where folderid = $folder_id");
        return true;
       }
       
       /**
       * Delete Folder Details by ID
       * */
      public static function delete_folder($folder_id){
       global $wpdb;
        $table_name = WPMManagerLite_FolderLists;
        $check_delete = $wpdb->query("DELETE FROM $table_name where folder_id = $folder_id");   
        if( $check_delete ){
          return true;
        }else{
          return false;
        }
       }

        /*
       *  Recursive function to create multilevel folder list ,$parentId 0 is the Root for listing in ul li
      */ 
       public static function recursive_delete($table_name , $parent , $parent_child=true ){
         global $wpdb;
         $childs[] = (int) $parent;  
         $wpmmanager_folders_lists = $wpdb->get_results("SELECT * FROM $table_name  WHERE folder_parent = $parent");
          if(is_array($wpmmanager_folders_lists) && !empty($wpmmanager_folders_lists)):
           foreach ( $wpmmanager_folders_lists as $wpmedia  ) { 
                 $folderid = $wpmedia -> folder_id;
                 $childs[] = (int) $folderid;   
                 $check = DatabaseLite_Class::delete_single_folder($folderid);
                 DatabaseLite_Class::recursive_delete( $table_name , $folderid , true );
                }
          endif;
          return $childs;
          
     }
       
     /*
       *  Recursive function to create multilevel folder list ,$parentId 0 is the Root for listing in ul li
      */ 
       public static function recursive_delete1($table_name , $parent , $parent_child=true ){
         global $wpdb;
         
         $wpmmanager_folders_lists = $wpdb->get_results("SELECT * FROM $table_name  WHERE folder_parent = $parent ORDER BY folder_id ASC");
          if(is_array($wpmmanager_folders_lists) && !empty($wpmmanager_folders_lists)):
           foreach ( $wpmmanager_folders_lists as $wpmedia  ) {
                 $childs[] = (int) $parent;   
                 $folderid = $wpmedia -> folder_id;
                  $this->delete_folder( $folderid );  
                  DatabaseLite_Class::recursive_delete( $table_name , $folderid , true );
              }
          endif;
          return $childs;
          
     }



      /*
       *  Recursive function to create multilevel folder list ,$parentId 0 is the Root for listing in ul li
      */ 
       public static function tbl_recur_array_builder($table_name ,$active_id, $parent=0, $parent_child=true ){
         $arrr = array();
         global $wpdb;
         $wpmmanager_folders_lists = $wpdb->get_results("SELECT * FROM $table_name  WHERE folder_parent = $parent ORDER BY folder_id ASC"); 
          foreach ( $wpmmanager_folders_lists as $r  ) {
              $fid = $r -> folder_id;
              $media_id_count = DatabaseLite_Class::get_all_count_lists( $fid );
                  
                  $arrr[] = array(
                          'id'       => $fid, 
                          'name'       => $r -> folder_name, 
                          'slug'       => $r -> folder_slug, 
                          'parent_id'   => $r -> folder_parent,
                          'folder_order'   => $r -> folder_order,
                          'type'   => $r -> type,
                          'total_media_cnt' => $media_id_count,
                          "child" =>  ($parent_child)? DatabaseLite_Class::tbl_recur_array_builder( $table_name,$active_id ,$r -> folder_id, true ):''
                  );
              }
          
          return $arrr;
          
     }


      /*
      *  Filter By Folder Lists Get all folder, sub folders in single array
      */ 
      public static function tbl_recur_array_builder_folder(){
         $wpmmanager_folders_lists = DatabaseLite_Class::tbl_recur_array_builder(WPMManagerLite_FolderLists,0);
        
         $single_array = DatabaseLite_Class::single_array(array(),$wpmmanager_folders_lists);

         return $single_array;      
     }
        
       /*
      *  Recursive array to convert multidimensional array to single array
      */ 
       public static function single_array($single_array,$array){
        $i = 0;
        if(isset($array) && !empty($array)){
          foreach($array as $new_array){
             if($i==0){
                $single_array[$i] = array(
                     'id' => 0,
                     'name'=> 'Select Folders',
                     'slug' =>'', 
                     'parent_id' => 0
                 );
              }else{
                 $single_array = $single_array;
              }

               if(!empty($new_array['child'])){
                  $temp_folder_array = $new_array;
                  unset($temp_folder_array['child']);
                  $single_array[$temp_folder_array['id']] = $temp_folder_array;
                  $single_array = DatabaseLite_Class::single_array($single_array,$new_array['child']);
              }else{
                $single_array[$new_array['id']] = $new_array;
              }
          $i++; 
         }
        }else{
            $single_array[0] = array(
                     'id' => 0,
                     'name'=> 'Select Folders',
                     'slug' =>'', 
                     'parent_id' => 0
                 );
        }
       
          return $single_array;
       }


       public static function get_folder_id(){
         $wpmmanager_folders_lists = DatabaseLite_Class::tbl_recur_array_builder(WPMManagerLite_FolderLists,0);
         
         $single_array2 = DatabaseLite_Class::single_array_folderid(array(),$wpmmanager_folders_lists);
     
         return $single_array2;      
     }
        
       /*
      *  Recursive array to convert multidimensional array to single array
      */ 
       public static function single_array_folderid($single_array2,$array){
          if(isset($array) && !empty($array)){
           $i = 0; foreach($array as $new_array){
              if($i==0){
                $single_array2[$i] = 0;
              }else{
                 $single_array2 = $single_array2;
              }

            if(!empty($new_array['child'])){
                $temp_folder_array = $new_array;
                unset($temp_folder_array['child']);
                $single_array2[] = $temp_folder_array['id'];
                $single_array2 = DatabaseLite_Class::single_array_folderid($single_array2,$new_array['child']);
            }else{
           
              $single_array2[] = $new_array['id'];
            }
          $i++;   
          }
          }else{
             $single_array2[0] = 0;  
          }
          return $single_array2;
       }
       
       /*
       *  Get Media Lists by Folder ID
       */ 
       public static function media_filter_ids($folder_id){
            global $wpdb;
            $idarr =  $folderids =array();
            $media_lists = DatabaseLite_Class::get_all_media_rel_folder_lists($folder_id);
            $sql = $wpdb->prepare("SELECT ID FROM " . $wpdb->prefix . 'posts' . " WHERE post_type = %s ", array('attachment'));
            $attachments = $wpdb->get_results($sql);
            foreach ($attachments as $at){
                    $idarr[] = $at->ID;
           }
           foreach ($media_lists as $ml){
              $idd = $ml->media_id;
              if(in_array($idd, $idarr)){
                $folderids[] = $idd;
               }
            }
            return $folderids;
       }
       
        /*
       *  Return Default Media Attachment and display only media not attached to folders
       */ 
       public static function get_media_filter_notids(){
            global $wpdb;
            $idarr = array();
            $folderids = array();
            $media_lists = DatabaseLite_Class::get_all_media_rel_folder_lists('');
            $sql = $wpdb->prepare("SELECT ID FROM " . $wpdb->prefix . 'posts' . " WHERE post_type = %s ", array('attachment'));
            $attachments = $wpdb->get_results($sql);
            foreach ($attachments as $at){
                    $idarr[] = $at->ID;
           }
           foreach ($media_lists as $ml){
              $idd = $ml->media_id;
              if(in_array($idd, $idarr)){
                $folderids[] = $idd;
               }
            }
            
            return $folderids;
       }
      
       /*
       * Update Folder Data
       */
      public static function update_folders($draggableFolderID,$droppablefolderID){
        global $wpdb;
        $table_name = WPMManagerLite_FolderLists;
        if($droppablefolderID == 0){
            $order = 0;
        }else{
            $order = DatabaseLite_Class::get_all_parent_cnt($droppablefolderID); 
        }
        
        $modified_date = date( 'Y-m-d H:i:s:u' );
        $update_arr = array(
            'folder_parent'=>$droppablefolderID, 
            'modified_date'=>$modified_date, 
            'folder_order'=>$order);
        $check = $wpdb->update($table_name,$update_arr , array('folder_id'=>$draggableFolderID));
        
        return $check;
       }
       
       /*
        * Get all Parent Total Count of last child
        */
       public static function get_all_parent_cnt($dropableid){
           global $wpdb;
           $table_name = WPMManagerLite_FolderLists;
           $cnt = 1;
           if($dropableid != 0){
           $arr = $wpdb->get_row("SELECT folder_parent FROM $table_name WHERE folder_id = $dropableid");
           $folderparentid = $arr->folder_parent;
            if($folderparentid == 0){
                $total_count = $cnt;
            }else{
                $cnt = DatabaseLite_Class::get_all_parent_cnt( $folderparentid );
                $cnt++;
                $total_count = $cnt;
            }
           }else{
                 $total_count = $cnt;
           }
           return $total_count;
       }

     /*
     * Get all attachement media id having within specific sizes or weights according to filter options.
     */
    public static function wpmdia_get_posts_attachement_ids($sizes, $weights){
        global $wpdb;
        $sql = $wpdb->prepare("SELECT ID FROM " . $wpdb->prefix . 'posts' . " WHERE post_type = %s ", array('attachment'));
        $attachments = $wpdb->get_results($sql);

         if ($sizes != '') {
            $size = explode('x', $sizes);
            $width_size = (float) $size[0];
            $height_size =(float) $size[1];
        }
        
        if ($weights != '') {
            $weight = explode('-', $weights);
            $min_weight = (float) $weight[0];
            $max_weight = (float) $weight[1];
        }
           
         if (empty($attachments)) return;
         
         $posts_ids_arr = array();
         $uploads = wp_upload_dir();
         foreach ($attachments as $attachment) {
                  $attachment_id  = $attachment->ID;
                  $attach_meta = wp_get_attachment_metadata($attachment_id);
                  $attach_meta_width = ( isset($attach_meta['width']) )?( (float) $attach_meta['width'] ):0;
                  $attach_meta_height = ( isset($attach_meta['height']) )?( (float) $attach_meta['height'] ):0;

                $fullsize_path = get_attached_file( $attachment_id ); // Full path
                if($fullsize_path != ''){
                    $weight_att =  ((file_exists($fullsize_path))?( (float) filesize($fullsize_path) ):0);
                }else {
                   $weight_att = 0;
                }
                 $mimetype = get_post_mime_type($attachment_id);
                  $exp = explode('/',$mimetype);
                    if ($weights == '') {
                    if ( $attach_meta_width >= $width_size || $attach_meta_height >= $height_size) {
                        if ($exp[0] == 'image') {
                            $posts_ids_arr[] = $attachment->ID;
                        }
                    }
                    }else if($sizes == ''){
                       if ($weight_att >= $min_weight && $weight_att <= $max_weight) {
                            $posts_ids_arr[] =  $attachment_id;
                        } 
                    }else{
                         if (($attach_meta_width >= $width_size || $attach_meta_height >= $height_size) && ($weight_att >= $min_weight && $weight_att <= $max_weight)) {
                                 if ($exp[0] == 'image') {
                                    $posts_ids_arr[] = $attachment_id;
                                }
                            }
                    }
            }
            return $posts_ids_arr;
       }

         public static function insert_terms_to_folder($parent_id,$cat_name,$depth,$taxonomy){
        $table_name = WPMManagerLite_FolderLists;
        global $wpdb;
         $fdataa = DatabaseLite_Class::get_folders_data_byname($cat_name,$table_name);
         if(!empty( $fdataa)){

         }else{

         if($parent_id == 0){
            //main parent term
          $pid = 0;
          $order = 0;      
         }else{
            $terms2 = get_term_by('id', $parent_id, $taxonomy);
            $array = json_decode(json_encode($terms2),true);
            $main_term = $array['name'];
            $fdata = DatabaseLite_Class::get_folders_data_byname($main_term,$table_name);
            $fdata_arr = json_decode(json_encode($fdata),true);
            $pid = $fdata_arr['folder_id'];
            $order = $fdata_arr['folder_order'] +  1;
         }
          $slug      = WPMManagerLite_Libary::create_unique_slug($cat_name,$table_name);
          $created_date = date( 'Y-m-d H:i:s:u' );
          $folder_data = 
                    array(
                      'folder_name'   => $cat_name,
                      'folder_slug'   => $slug,
                      'folder_parent' => $pid,
                      'type'          => 'folder',
                      'folder_order'  => $order,
                      'added_date'    => $created_date,
                      'modified_date' => $created_date
                          );
    
          $wpdb->insert(
                  $table_name , $folder_data , array(
                      '%s', '%s', '%d', '%s', '%d', '%s', '%s'
                          )
                  );
                
         }

       }

        /**
       * Get Folder data from folder name 
       * */
      public static function get_folders_data_byname($folder_name,$table_name){
            global $wpdb;
            $folders_lists = $wpdb->get_row('SELECT * FROM `'.$table_name.'` where `folder_name` = "'.$folder_name.'";');
            return $folders_lists;
       }

        /**
       * count parent 
       * */
      public static function count_all_parent_folders($pid,$table_name,$depth){
       global $wpdb;
        if($pid != ''){
            $fdata = $wpdb->get_row('SELECT * FROM `'.$table_name.'` where `folder_id` = "'.$pid.'";');
             $pid = $fdata->folder_parent;
             if($pid > 0){
                  $cntdepth = $depth + 1;
                  $cdepth  =  DatabaseLite_Class::count_all_parent_folders($pid,$table_name,$cntdepth); 
             }else{
                $cdepth = $depth;
             }
             return $cdepth;
        }
        
       }

  

}
$global['databaselite_class'] = new DatabaseLite_Class();
endif;