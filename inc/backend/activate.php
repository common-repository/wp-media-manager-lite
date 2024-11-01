<?php defined('ABSPATH') or die("No script kiddies please!");
/**
 * Necessary Table Creation on activation
 */
global $wpdb;
$table_name = WPMManagerLite_FolderLists;
$table_name_subs = WPMManagerLite_FolderFileRelationship;
$charset_collate = $wpdb->get_charset_collate();

if ( is_multisite() ) {
$current_blog = $wpdb->blogid;
// Get all blogs in the network and activate plugin on each one
    $blog_ids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );
    foreach ( $blog_ids as $blog_id ) {
        switch_to_blog( $blog_id );

        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
                        folder_id INT NOT NULL AUTO_INCREMENT, 
                        PRIMARY KEY(folder_id),
                        folder_name VARCHAR(255),
                        folder_slug VARCHAR(255),
                        folder_parent INT(20),
                        type VARCHAR(255),
                        folder_order BIGINT,
                        added_date datetime ,
                        modified_date datetime
                      ) $charset_collate;";


        /**
         * Create a new table for folder and media relationship 
         **/           
     $sqll = "CREATE TABLE IF NOT EXISTS $table_name_subs (
          ff_relationship_id mediumint(9) NOT NULL AUTO_INCREMENT,
          media_id INT NOT NULL,
          folderid INT NOT NULL,
           addeddate datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
         UNIQUE KEY ff_relationship_id (ff_relationship_id)
           ) $charset_collate;";


        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );
        dbDelta( $sqll );


        restore_current_blog();
    }

}else{
     
        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
                        folder_id INT NOT NULL AUTO_INCREMENT, 
                        PRIMARY KEY(folder_id),
                        folder_name VARCHAR(255),
                        folder_slug VARCHAR(255),
                        folder_parent INT(20),
                        type VARCHAR(255),
                        folder_order BIGINT,
                        added_date datetime ,
                        modified_date datetime
                      ) $charset_collate;";

        /**
         * Create a new table for folder and media relationship 
         **/           

     $sqll = "CREATE TABLE IF NOT EXISTS $table_name_subs (
          ff_relationship_id mediumint(9) NOT NULL AUTO_INCREMENT,
          media_id INT NOT NULL,
          folderid INT NOT NULL,
           addeddate datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
         UNIQUE KEY ff_relationship_id (ff_relationship_id)
           ) $charset_collate;";


        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );
        dbDelta( $sqll );
}